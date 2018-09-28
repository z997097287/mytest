<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 9:26
 */

namespace app\admino\controller;


use think\Model;

class Pagination extends AdminBase
{
    protected $page = 0;
    protected $count = 10;
    public $totalPage = 0;

    public function __construct($page = 0, $count = 10)
    {
        $this->page = $page * $count;
        $this->count = $count;
    }

    public function getPagination(Model $model)
    {
        $list = $model->order('id desc')->limit($this->page, $this->count)->select();
        $this->getCount();
        return $list;
    }

    public function getCount(Model $model)
    {
        $total = $model->count();
        $this->totalPage = ceil($total / ($this->page));
    }
}