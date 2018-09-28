<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 18:14
 */

namespace app\common\model;


class CheeseCardStarModel extends BaseModel
{
    //卡片点赞表
    protected $table_name = "cheese_card_star";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT('用户id'),
            "cheese_card_id" => self::generateINT('卡片id'),
            "cheese_card_user_id" => self::generateINT('卡片对应的用户id'),
            "is_notice"=>self::generateINT('是否已经通知卡片的用户id',1,0),
        ];
    }

    public function getAssemblyList()
    {
        $data = parent::getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user"] = UserModel::getInstance()->where(["id" => $data[$i]["user_id"]])->getAssembly();
            $data[$i]["cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data[$i]["cheese_card_id"]])->getAssembly();
        }
    }

}