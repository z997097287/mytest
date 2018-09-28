<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 9:36
 */

namespace app\admino\controller;


use app\admino\model\MenuModel;
use app\common\controller\BaseController;

class Page extends BaseController
{
    public function index()
    {
        $model = new MenuModel();
        $page = new Pagination();
        $list = $page->getPagination($model);
        return json($list);
    }
}