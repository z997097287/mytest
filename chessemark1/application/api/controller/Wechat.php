<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\controller\BaseController;
use app\common\model\System;
use app\common\model\SystemModel;
use app\common\model\UserModel;
use EasyWeChat\Foundation\Application;
use Exception;
use think\exception\DbException;
use think\Log;
use think\Request;
use Qcloud\Cos\Client;


class Wechat extends BaseController
{
    protected $user_db;

    /**
     * 登录
     * @method /Api/Wechat/login.json
     * @param string $code
     * @param $ext_data
     * @return array|\EasyWeChat\Support\Collection|false|int|mixed|\PDOStatement|string|\think\Model
     */
    public function login($code, $ext_data)
    {
        $options = [
            'mini_program' => [
                'app_id' => SystemModel::config("mini_program_app_id"),
                'secret' => SystemModel::config("mini_program_secret"),
            ],
        ];
        $app = new Application($options);
        $miniProgram = $app->mini_program;


        $data = $miniProgram->sns->getSessionKey($code);
        $data = json_decode(json_encode($data, true), true);


        $openid = $data["openid"];

        $data = UserModel::getInstance()->getDataByOpenid($openid);


        $expire = date("Y-m-d H:i:s", time());
        $token = md5($openid . $expire);
        if (empty($data)) {
            $ext_data['openid'] = $openid;
            $ext_data['username'] = $openid;
            Log::writeRemote($ext_data);
            $model = new UserModel();
            $model->add($ext_data);
            Log::writeRemote($model->getLastSql());
        }

        $param['token'] = $token;
        $param['expire_in'] = $expire;
        if (!empty($data["nickName"])) {
            $param["nickName"] = $data["nickName"];
        }
        UserModel::getInstance()->where(["openid" => $openid])->save($param);

        $data = UserModel::getInstance()->getDataByOpenid($openid);
        $result=UserModel::getInstance()->where(["openid" => $openid])->find();
        $cos_client = new Client([
            'region' => SystemModel::config("txy_cos_secret_region"),
            'credentials' => [
                'secretId' => SystemModel::config("txy_cos_secret_id"),
                'secretKey' => SystemModel::config("txy_cos_secret_key"),
            ]
        ]);
        $path = "/user-picture/" . time() . ".jpg";
        if(strpos($result['avatarUrl'],'user-picture')==false){
            $row = @file_get_contents($result['avatarUrl']);
            if ($row) {
                try {
                    /** @var \Guzzle\Service\Resource\Model $result */
                    if ($cos_client->Upload(SystemModel::config("txy_cos_secret_bucket"), $path, $row)) {
                        UserModel::getInstance()->where(["openid" => $openid])->save([
                            "avatarUrl" => $path,
                        ]);
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
               echo 'fail';
            }
        }
        $this->displayByData($data);

    }


}
