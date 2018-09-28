<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/31
 * Time: 15:11
 */

namespace app\admino\model;


use app\common\model\BaseModel;

class KeywordModel extends BaseModel
{
    protected $table_name = "keyword";

    protected function generateTableField()
    {
        return [
            "name" => "关键词",
            "type" => "回复关键词类型",
            "apply" => self::generateTEXT('回复关键词', ''),

        ];
    }

    public function replyKeyword($text)
    {
        $model = new KeywordModel();
        $list = $model->select();
        foreach ($list as $vo) {
            if (strpos($text, $vo['name']) !== false) {
                return $vo['apply'];
            }
        }
        return false;
    }
}