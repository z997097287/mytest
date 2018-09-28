<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 18:14
 */

namespace app\common\model;


class PickGroupCommentModel extends BaseModel
{
    //卡片点赞表
    protected $table_name = "pick_group_comment";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT('用户id'),
            "pick_group_comment_left_user_id" => self::generateINT('pick中左边用户的id'),
            "pick_group_comment_right_user_id" => self::generateINT('pick中右边用户的id'),
            "pick_group_id" => self::generateINT('pick组id'),
            "content" => self::generateTEXT('评论内容'),
            "star" => self::generateINT('该评论的点赞数'),
            "left_user_is_read"=>self::generateINT('左边的用户是否已读评论',1,0),
            "right_user_is_read"=>self::generateINT('右边的用户是否已读评论',1,0),
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