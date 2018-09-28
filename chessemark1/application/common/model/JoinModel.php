<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/21
 * Time: 16:05
 */

namespace app\common\model;
class JoinModel extends BaseModel{
    protected $table_name='join';
    protected function generateTableField()
    {
        return [
            'user_id'=>self::generateINT('我投票的用户id'),
            'pick_group_id'=>self::generateINT("我被匹配到或者投票的pick组id"),
            'type'=>self::generateVARCHAR("投票，匹配"),
        ];
    }
}