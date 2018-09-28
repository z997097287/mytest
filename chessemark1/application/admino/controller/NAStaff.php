<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 9:16
 */

namespace app\admino\controller;


use think\Log;

class NAStaff extends AdminBase
{

    public function current()
    {
        $this->displayByData($_SESSION);
    }

    public function test()
    {
        Log::writeRemote("213");
    }
}