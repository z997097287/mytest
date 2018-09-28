<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


class SystemMessageModel extends BaseModel
{

    protected $table_name = "message";

    protected function generateTableField()
    {
        return [
            "type" => self::generateVARCHAR("ALL_MESSAGE，USER_MESSAGE,USER_Match，PICK_WIN，PICK_LOSE,WE_MATCH"),
            "user_id"=>self::generateINT('要发送的用户id'),
            "pick_group_id"=>self::generateINT("pick组id"),
            "cheese_card_id"=>self::generateINT("被匹配到的cheese卡id"),
            "title" => self::generateVARCHAR("消息标题"),
            "content" => self::generateTEXT("消息内容"),
            "is_read"=>self::generateINT("指定的用户是否已读"),
        ];
    }
    public function getAssemblyList()
    {
        $data = parent::getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user"] = UserReadModel::getInstance()->where(["message_id" => $data[$i]["id"]])->where("is_read",0)->getAssembly();
        }
        return $data;
    }



}