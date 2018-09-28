<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 14:23
 */
namespace app\common\model;
class UserReadModel extends BaseModel{
    protected $table_name='user_read';
    protected function generateTableField()
    {
        return [
            'user_id'=>self::generateINT('用户id'),
            'is_read'=>self::generateINT('是否已读',1,0),
            'message_id'=>self::generateINT('系统消息id'),
        ];
    }
}