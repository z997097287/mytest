<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 17:08
 */

namespace app\common\model;
class UserStarModel extends BaseModel{
    protected $table_name='user_star';
    protected function generateTableField()
    {
        return ['user_id'=>self::generateINT('点赞的用户id'),
            'cheese_card_id'=>self::generateINT('点赞的chesse_card id'),
            'cheese_card_user_id'=>self::generateINT('被点赞的cheese卡用户拥有着'),
            'cheese_card_comment_id'=>self::generateINT('评论id'),
            'cheese_card_comment_user_id'=>self::generateINT('评论的用户id'),
            'pick_group_id'=>self::generateINT('点赞的pick组id'),
            'pick_group_comment_id'=>self::generateINT('点赞的pick组评论id'),
            'pick_group_comment_user_id'=>self::generateINT('被评论的用户id'),
            'is_read'=>self::generateINT('是否已读'),
            'star_type'=>self::generateINT('1代表cheese卡点赞,2代表cheese卡评论点赞,3代表pick组评论点赞'),
            'is_delete'=>self::generateINT('0代表正常，1代表已删除'),
            'is_delete'=>self::generateINT('0代表正常，1代表已删除'),
            ];

    }
}