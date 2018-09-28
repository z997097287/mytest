<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 17:21
 */

namespace app\common\model;


class CheeseCardCommentStarModel extends BaseModel
{
    protected $table_name = "cheese_card_comment_star";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT('用户id'),
            "cheese_card_comment_id" => self::generateINT('评论id'),
            "cheese_card_comment_user_id" => self::generateINT('评论的用户id'),
            "star" => self::generateINT('评论的点赞数'),
            "is_read"=>self::generateINT("评论的点赞是否已通知",1,0)
        ];
    }


}