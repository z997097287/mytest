<?php

namespace app\common\exception;

use app\common\controller\BaseController;
use Exception;
use think\exception\Handle;
use think\exception\HttpException;

class Http extends Handle
{

    public function render(Exception $e)
    {
        $controller = new BaseController();
        if ($controller->isHtml()) {
            return parent::render($e);
        }
        $controller->displayByError($e->getMessage());
    }

}