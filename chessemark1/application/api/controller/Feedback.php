<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 15:54
 */

namespace app\api\controller;

use app\common\model\FeedbackModel;
use think\Request;

class Feedback extends Token
{
    public function reciveMessage()
    {
        $user_id=$this->autoUserCertification();
        $data = Request::instance()->param();
        $data['user_id'] =$user_id;
        if (FeedbackModel::getInstance()->insert($data)) {
            $this->displayBySuccess('用户留言成功');
        }
    }
}