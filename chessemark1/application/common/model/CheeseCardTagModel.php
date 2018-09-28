<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


use Exception;

class CheeseCardTagModel extends BaseModel
{

    protected $table_name = "cheese_card_tag";


    protected function generateTableField()
    {
        return [
            "text" => self::generateVARCHAR("标签名"),
        ];
    }


    protected function initData()
    {
        return [
            ["text" => "旅行"],
            ["text" => "摄影"],
            ["text" => "人物"],
            ["text" => "运动"],
            ["text" => "萌宠"],
            ["text" => "美食"],
            ["text" => "娱乐"],
            ["text" => "好物"],
            ["text" => "美妆"],
            ["text" => "书"],
            ["text" => "二次元"],
            ["text" => "影音"],
            ["text" => "生活"],
            ["text" => "搞笑"]
        ];
    }


}