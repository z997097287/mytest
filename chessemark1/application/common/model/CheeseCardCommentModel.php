<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 18:14
 */

namespace app\common\model;


class CheeseCardCommentModel extends BaseModel
{
    //卡片点赞表
    protected $table_name = "cheese_card_comment";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT('用户id'),
            "cheese_card_user_id" => self::generateINT('发布卡片的用户id'),
            "cheese_card_id" => self::generateINT('卡片id'),
            "content" => self::generateTEXT('评论内容'),
            "star" => self::generateINT('该评论的点赞数'),
            "is_read"=>self::generateINT("该评论是否被用户已读",1,0)
        ];
    }
    public function getAssemblyList()
    {
        $data = parent::getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user"] = UserModel::getInstance()->where(["id" => $data[$i]["user_id"]])->getAssembly();
        }
        return $data;
    }

}