<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 11:01
 */
namespace app\common\model;
class UserCommentModel extends BaseModel{
    protected $table_name='user_comment';
    protected function generateTableField()
    {
        return ['user_id'=>self::generateINT('评论的用户id'),
            'cheese_card_id'=>self::generateINT('评论的chesse_card id'),
            'cheese_card_user_id'=>self::generateINT('被评论的cheese卡用户拥有着'),
            'cheese_card_comment_id'=>self::generateINT('评论id'),
            'cheese_card_comment_user_id'=>self::generateINT('评论的用户id'),
            'pick_group_id'=>self::generateINT('评论的pick组id'),
            'pick_group_comment_id'=>self::generateINT('被评论的pick组评论id'),
            'pick_group_comment_left_user_id'=>self::generateINT('被评论的用户左边id'),
            'pick_group_comment_right_user_id'=>self::generateINT('被评论的用户右边id'),
            'is_read'=>self::generateINT('是否已读'),
            'comment_type'=>self::generateINT('1代表cheese卡评论,2代表pick组评论')
        ];

    }
}