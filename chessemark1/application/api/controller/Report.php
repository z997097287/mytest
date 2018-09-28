<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 9:24
 */

namespace app\api\controller;

use app\common\model\ReportModel;
use think\Request;

class Report extends Token
{
    public function reportUser($id)
    {
        $user_id = $this->autoUserCertification();
        // 获取经过过滤的全部post变量
        if (ReportModel::getInstance()->insert(['user_id'=>$user_id,'report_user_id'=>$id,'report_type'=>'USER'])) {
            $this->displayBySuccess('用户举报成功');
        }
    }

    public function reportCheeseCard($cheese_card_id,$content='')
    {
        $user_id = $this->autoUserCertification();
        if (ReportModel::getInstance()->insert(['user_id'=>$user_id,'cheese_card_id'=>$cheese_card_id,'report_type'=>1,'cheese_card_report_content'=>$content,'report_type'=>"CHEESECARD"])) {
            $this->displayBySuccess('卡片举报成功');
        }

    }

}