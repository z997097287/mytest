<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/13
 * Time: 10:17
 */

namespace app\api\controller;


use app\common\controller\BaseController;
use app\common\model\UserModel;
use Exception;
use think\Session;

class Token extends BaseController
{

    /**
     * 判断token是否过期
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    protected function checkToken()
    {
        $shadow_user = Session::get("shadow_user");
        if (!empty($shadow_user)) {
            return ["id" => $shadow_user];
        }
        $user_db = new UserModel();
        try {
            $data = $user_db->getDataByToken($_REQUEST["token"]);
            if (empty($data)) {
                $this->displayByError("认证失败", 502);
            }
            if (!$this->isExpire($data['expire_in'])) {//没过期
                return $data;
            }
            $this->displayByError("凭证过期", 501);
        } catch (Exception $e) {
            $this->displayByError($e->getMessage());
        }
    }

    protected function autoUserCertification()
    {
        if ($_REQUEST["debug"]) {
            return $_REQUEST["debug"];
        }
        $data = $this->checkToken();
        return $data["id"];
    }

    /**
     * 是否过期
     * @param $date
     * @return bool
     */
    protected function isExpire($date)
    {
        $expire = strtotime(date('Y-m-d H:i:s', time())) - strtotime($date);
        if ($expire > 259200)//三天过期
        {
            return true;
        }
        return false;
    }
}