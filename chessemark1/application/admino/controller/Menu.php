<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/29
 * Time: 9:21
 */

namespace app\admino\controller;


class Menu extends MultilevelTableBase
{

    protected $model = "menu";
    protected $primary_show = false;
//    protected $limit = 1;
    protected $title = "微信菜单管理";
    protected $field = 'parent_id';
    protected $table_header = [
        "id" => "ID",
        "name" => "菜单名称",
        "type" => "菜单类型",
        "action" => "菜单动作"
    ];

    protected function tableHeaderMore()
    {
        return [
            "name" => "菜单名称",
            "type" => self::generateText('菜单类型', 'click,view等'),
            "action" => self::generateText('菜单的动作', '如类型为url,就填url地址'),
            "appid" => self::generateText('小程序openid', '链接小程序的openid(可不填)'),
            "pagepath" => self::generateText('默认主页', '链接小程序的默认主页(可不填)')
        ];
    }

}