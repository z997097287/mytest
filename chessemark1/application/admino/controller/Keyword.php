<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/31
 * Time: 15:18
 */

namespace app\admino\controller;


class Keyword extends SingleTableBase
{
    protected $model = "keyword";
    protected $primary_show = false;
    protected $title = "关键词回复管理";
    protected $table_header = [
        "name" => "关键词",
        "type" => "类型",
        "apply" => "关键词回复",
    ];
    protected $table_header_more = [
        "name" => "关键词",
        "type" => "类型",
        "apply" => "关键词回复",
    ];

    protected $table_option = [
        "apply" => [
            "type" => self::TYPE_TEXTAREA,
            "table_width" => "300",
        ]
    ];
}