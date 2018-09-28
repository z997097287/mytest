<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\BannerModel;

class Banner extends Token
{

    /**
     * Banner列表
     */
    public function index()
    {
        $data = BannerModel::getInstance()->order("id desc")->getAssemblyList();
        $this->displayByData($data);
    }


}
