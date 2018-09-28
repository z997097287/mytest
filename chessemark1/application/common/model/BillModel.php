<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 15:53
 */

namespace app\common\model;
class BillModel extends BaseModel
{
    protected $table_name = 'bill';

    protected function generateTableField()
    {
        return [
            'user_id' => self::generateINT('用户id'),
            'integral' => self::generateINT('用户积分增加或者减少'),
            'title' => self::generateVARCHAR('减少原因'),
        ];
    }
}