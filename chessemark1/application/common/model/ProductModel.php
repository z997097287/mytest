<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/4
 * Time: 11:23
 */

namespace app\common\model;


class ProductModel extends BaseModel
{
    //设计表名
    protected $table_name = 'product';

    //设计数据表
    protected function generateTableField()
    {
        return [
            'name' => self::generateVARCHAR("产品名称"),
            'stock' => self::generateINT('库存'),
            'number' => self::generateINT('要兑换的cheese数量'),
            'introduce' => self::generateVARCHAR('产品介绍'),
            'price'=>self::generateINT('商品的原价'),
            'img_src' => self::generateVARCHAR('图片地址', '256'),
            'status' => self::generateINT('是否下架，0为下架，1为上架', 1, 0),
        ];
    }

}