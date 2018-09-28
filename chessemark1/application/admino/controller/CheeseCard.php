<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/20
 * Time: 10:20
 */

namespace app\admino\controller;


//卡片列表
use app\common\model\CheeseCardModel;
use app\common\model\SystemModel;
use app\common\model\UserModel;
use think\db\Query;

class CheeseCard extends SingleTableBase
{
    protected $model = "cheese_card";
    protected $title = "普通用户卡片管理";

    protected $is_add = false;
//    protected $is_edit = false;
    protected $table_header = [
        "user_id" => "用户编号",
        "user_name" => "用户名",
        "full_pic_url" => "卡片",
        "text" => "短语",
        "tag" => "标签",
        "is_pickpool" => "卡片池",
        "created_at" => "上传时间"
    ];
    protected $table_header_more = [
        "user_id" => "用户编号",
        "user_name" => "用户名",
        "full_pic_url" => "图片地址",
        "tag" => "标签",
        "text" => "短语",
        "star" => "点赞数",
//        "is_pickpool" => "是否在卡片池",
//        "is_delete" => "是否删除",
    ];
    protected $table_option = [
        "full_pic_url" => [
            'type' => self::TYPE_TX_COS,
            "host" => SystemModel::TX_COS_HOST,
            'path' => "user-card",
        ],
        "user_name" => [
            'type' => self::TYPE_DISABLED_TEXT,
            "table_width" => "100",
        ],
        "user_id" => [
            'type' => self::TYPE_DISABLED_TEXT,
            "table_width" => "100",
        ],
        "is_pickpool" => [
            'type' => self::TYPE_SELECT,
            "data" => [
                "0" => "否",
                "1" => "是",
            ]
        ],
        "tag" => [
            'type' => self::TYPE_ARRAY
        ]
    ];

    protected $filter_header = ["text", "tag"];
    protected $table_ext_button = [
        "放入卡片池" => "/Admino/CheeseCard/pool",
//        "移出卡片池" => "/Admino/CheeseCard/unpool"
    ];
    protected $table_ext_button_iframe = [
        "评论" => "/Admino/CheeseCardComment/index"
    ];

    public function unpool($id)
    {
        CheeseCardModel::getInstance()->where([
            "id" => $id
        ])->save([
            "is_pickpool" => 0
        ]);
        $this->displayBySuccess();
    }

    public function pool($id)
    {
        CheeseCardModel::getInstance()->where([
            "id" => $id
        ])->save([
            "is_pickpool" => 1
        ]);
        $this->displayBySuccess();
    }

    protected function data(Query $model, $page, $search, $filter = false, $w = [])
    {
        return parent::data($model, $page, $search, $filter, ["is_delete" => 0, "user_type" => "NONE"]); // TODO: Change the autogenerated stub
    }


    public function delete($id)
    {
        CheeseCardModel::getInstance()->where(["id" => $id])->save([
            "is_delete" => 1
        ]);
        $this->displayBySuccess();
    }
}