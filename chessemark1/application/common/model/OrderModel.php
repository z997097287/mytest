<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 14:28
 */

namespace app\common\model;

use app\common\model\BaseModel;

class OrderModel extends BaseModel
{
    protected $table_name = 'order';

    protected function generateTableField()
    {
        return [
            'user_id' => self::generateVARCHAR('用户唯一id'),
            //'user_name' => self::generateVARCHAR('用户名'),
            'buy_name' => self::generateVARCHAR('用户购买的商品'),
            'order_number' => self::generateVARCHAR('订单编号'),
            'product_id' => self::generateINT('产品id'),
            'user_phone'=>self::generateVARCHAR('用户留下的手机号'),
            'cost_exchange' => self::generateINT('花费积分')
        ];
    }
    //关联用户表通过唯一id
    public function items(){
        return $this->hasOne('User','openid');
    }


}