<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 15:41
 */
namespace app\common\model;
class FeedbackModel extends BaseModel{
    protected $table_name='feedback_user';
    protected function generateTableField()
    {
        return [
            'user_id'=>self::generateINT("用户id"),
            'feedback'=>self::generateVARCHAR("用户反馈的内容"),
            'user_concact'=>self::generateVARCHAR("用户的联系方式",20),
            'img_url'=>self::generateVARCHAR('截图',256),
        ];
    }
}