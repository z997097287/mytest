<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/4
 * Time: 15:58
 */

namespace app\api\controller;

use app\common\model\BillModel;
use think\Db;
use app\common\controller\BaseController;
use app\common\model\ProductModel;
use app\common\model\UserModel;
use app\common\model\OrderModel;

class Product extends Token
{
    public function productList($page = 1)
    {
        $end = $page * 6;
        if ($end != 6) {
            $start = $end - 6;
        } else {
            $start = 0;
        }
        $data = ProductModel::getInstance()->limit($start, $end)->order('id desc')->getAssemblyList();
        $this->displayByData($data);
    }

    /**
     * @param $open_id 用户信息表 查询积分
     * @param $product_id 商品id 用于更新库存
     * @param $member 兑换需要多少积分
     */
        public function productExchange($product_id, $member,$phone)
    {
        //生成订单号
        $str = (int)date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $user_id=$this->autoUserCertification();

        //开启一个事务
        Db::startTrans();
        try {
            $data = UserModel::getInstance()->where('id', $user_id)->getAssembly();
            $exchange['cheese_integral'] = $data['cheese_integral'] - $member;
            $exchange['phone']=$phone;
            if ($exchange['cheese_integral'] < 0) {
                $this->displayByError('用户积分不足');
                exit();
            }

            $rows = ProductModel::getInstance()->where('id', $product_id)->getAssembly();
            $stock['stock'] = $rows['stock'] - 1;
            if ($stock['stock'] < 0) {
                $this->displayByError('商品库存不足');
                exit();
            }
            //TODO 普通用户没有username 只能使用user_id,关于用户的以用户user_id为外键
            $result = [
                'user_id' => $user_id,
                'buy_name' => $rows['name'],
                'order_number' => $str,
                'product_id' => $rows['id'],
                'cost_exchange' => $member,
                'user_phone'=>$phone,
            ];
            $row['integral']=-$member;
            $row['title']=$rows['name'];
            $row['user_id']=$user_id;
            //dump($result);

            //更新用户订单表
            OrderModel::getInstance()->insert($result);
            //更新用户积分，更新商品库存
            UserModel::getInstance()->where('id', $user_id)->save($exchange);
            //更新库存
            ProductModel::getInstance()->where('id', $product_id)->save($stock);
            //更新用户积分增减记录表
            BillModel::getInstance()->where('id',$user_id)->insert($row);
            Db::commit();
            $this->displayBySuccess('兑换成功');
        } catch (\Exception $e) {
            Db::rollback();
            $this->displayByError();
        }


    }
    //积分记录
    public function getBill($page=1){
        $user_id=$this->autoUserCertification();
        $data['bill_info']=BillModel::getInstance()->where('user_id',$user_id)->page($page,10)->order('updated_at desc')->getAssemblyList();
        $data['bill']=UserModel::getInstance()->where('id',$user_id)->field('cheese_integral')->find();
        $this->displayByData($data);
    }

    //根据用户id查询兑换过的商品
    public function exchangeHistory($user_id)
    {
        if ($data = OrderModel::getInstance()->where('user_names', $user_id)->getAssemblyList()) {
            $data['userinfo'] = OrderModel::get($data['id']);
            $this->displayByData($data);
        } else {
            $this->displayByError('请传入合法的用户id');
        }


    }

}