<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/21
 * Time: 15:06
 */

namespace app\admino\controller;


class Banner extends SingleTableBase
{
    protected $model = "banner";
    protected $primary_show = false;
    protected $title = "banner列表";

    protected $table_header = [
        "pic_url" => "图片地址",
        "cheese_card_id" => "关联的卡片ID",
        "uri" => "扩展链接[网页/小程序链接/其他地址]"
    ];
    protected $table_header_more = [
        "pic_url" => "图片地址",
        "cheese_card_id" => "关联的卡片ID",
        "uri" => "扩展链接[网页/小程序链接/其他地址]"
    ];

}