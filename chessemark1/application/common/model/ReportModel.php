<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 9:11
 */
namespace app\common\model;
class ReportModel extends BaseModel{
    //定义表名
    protected $table_name="report";
    protected function generateTableField()
    {
        return [
            'user_id'=>self::generateINT('用户id'),
            'report_user_id'=>self::generateINT('被举报的用户id'),
            'cheese_card_id'=>self::generateINT("被举报的卡片id"),
            'user_report_content'=>self::generateVARCHAR('用户举报的内容'),
            'cheese_card_report_content'=>self::generateVARCHAR('被举报卡片的卡片内容'),
            'report_type'=>self::generateVARCHAR("USER，CHEESECARD"),
            'is_examine'=>self::generateINT('0代表未审核,1代表审核',1,0),
        ];
    }
}