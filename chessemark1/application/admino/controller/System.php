<?php

namespace app\admino\controller;


class System extends SingleTableBase
{

    protected $model = "system";
    protected $primary_show = false;
    protected $title = "系统设置";
    protected $table_header = [
        "key" => "键",
        "value" => "值",
        "text" => "说明文字",
    ];
    protected $table_header_more = [
        "key" => "键",
        "value" => "值",
        "text" => "说明文字",
    ];

    protected $table_option = [
        "key" => [
            "table_width" => "200"
        ],
        "value" => [
            "table_width" => "300"
        ]
    ];
    protected $order = "id";
    protected $search_header = [
        "key" => self::SEARCH_FUZZY
    ];
}