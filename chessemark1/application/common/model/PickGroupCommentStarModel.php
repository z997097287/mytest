<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 17:21
 */

namespace app\common\model;


class PickGroupCommentStarModel extends BaseModel
{
    //评论点赞表
    protected $table_name = "pick_group_comment_star";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT('用户id'),
            "pick_group_comment_id" => self::generateINT('评论id'),
            "pick_group_comment_user_id" => self::generateINT('评论的用户id'),
            "is_read"=>self::generateINT()
        ];
    }


}