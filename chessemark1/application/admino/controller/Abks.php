<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/28
 * Time: 17:12
 */

namespace app\admino\controller;


use app\common\controller\BaseController;

class Abks extends BaseController
{
    public function index($url="https://www.baidu.com",$level=3,$size=4)
    {
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        //生成二维码图片
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
        exit();
    }
}