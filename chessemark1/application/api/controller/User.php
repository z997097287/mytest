<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\CheeseCardModel;
use app\common\model\FeedbackModel;
use app\common\model\FollowModel;
use app\common\model\PickGroupModel;
use app\common\model\UserModel;
use app\common\model\SystemModel;
use Exception;
use Qcloud\Cos\Client;

class User extends Token
{


    public function index()
    {

        $user_id = $this->autoUserCertification();
        $data = UserModel::getInstance()->where(["id" => $user_id])->getAssembly();
        $data["statistics"] = [
            "pick_group" => PickGroupModel::getInstance()->where(["right_user_id" => $user_id])->count(),
            "follow" => FollowModel::getInstance()->where(["user_id" => $user_id])->where('follow_user_id', '>', 0)->count(),
            "fan" => FollowModel::getInstance()->where('follow_user_id', $user_id)->count(),
            "cheese_card" => CheeseCardModel::getInstance()->where(["user_id" => $user_id, "is_delete" => 0])->where('win_constant', '>', 4)->count(),
            "frequency" => PickGroupModel::getInstance()->where(["left_user_id" => $user_id, "created_at" => ["like", date("Y-m-d", time()) . "%"]])->count()
        ];
        $this->displayByData($data);

    }

    //根据用户id查询用户信息
    public function otherIndex($user_id, $my_user_id)
    {
        $data = UserModel::getInstance()->where(["id" => $user_id])->getAssembly();
        $data["statistics"] = [
            "pick_group" => PickGroupModel::getInstance()->where(["left_user_id" => $user_id])->count(),
            "follow" => FollowModel::getInstance()->where(["user_id" => $user_id])->where('follow_user_id', '>', 0)->count(),
            "fan" => FollowModel::getInstance()->where('follow_user_id', $user_id)->count(),
            "cheese_card" => CheeseCardModel::getInstance()->where(["user_id" => $user_id, "is_delete" => 0])->where('win_constant', '>', 4)->count(),
            "is_follow" => FollowModel::getInstance()->where(['user_id' => $my_user_id, 'follow_user_id' => $user_id])->count(),
        ];
        $this->displayByData($data);
    }

    /**
     * 更新用户信息
     * @param $nickName
     * @param $diyAvatarUrl
     * @param $signature
     */
    public function update($nickName, $diyAvatarUrl='', $signature)
    {
        $user_id = $this->autoUserCertification();
        $cos_client = new Client([
            'region' => SystemModel::config("txy_cos_secret_region"),
            'credentials' => [
                'secretId' => SystemModel::config("txy_cos_secret_id"),
                'secretKey' => SystemModel::config("txy_cos_secret_key"),
            ]
        ]);
        if($diyAvatarUrl!='') {

            $diyAvatarUrl = "https://cheese-res-1257281477.cos.ap-shanghai.myqcloud.com" . $diyAvatarUrl;
            $path = "/user-picture/" . time() . ".jpg";
            $row = @file_get_contents($diyAvatarUrl);
            if ($row) {
                try {
                    /** @var \Guzzle\Service\Resource\Model $result */
                    if ($result = $cos_client->Upload(SystemModel::config("txy_cos_secret_bucket"), $path, $row)) {
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo 'fail';
            }
        }
        UserModel::getInstance()->where(["id" => $user_id])->save([
            "nickName" => $nickName,
            "diyAvatarUrl" => $path,
            "signature" => $signature,
        ]);
        $data = UserModel::getInstance()->where(["id" => $user_id])->getAssembly();
        $this->displayByData($data);
    }
}
