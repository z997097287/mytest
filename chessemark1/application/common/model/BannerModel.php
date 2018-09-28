<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


class BannerModel extends BaseModel
{

    protected $table_name = "banner";


    protected function generateTableField()
    {
        return [
            "pic_url" => "图片地址",
            "cheese_card_id" => self::generateINT("关联的卡片ID"),
            "uri" => "扩展链接[网页/小程序链接/其他地址]",
        ];
    }


    public function getAssembly()
    {

        $data = $this->find();
        if (empty($data)) {
            return null;
        }
        $data["cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data["cheese_card_id"]])->getAssembly();
        return $data;
    }

}