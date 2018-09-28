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

class CheeseCardBad extends SingleTableBase
{
    protected $model = "cheese_card";
    protected $title = "涉黄可疑卡片管理";

    protected $is_edit = false;
    protected $is_add = false;

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
        "pic_url" => "图片地址",
        "full_pic_url" => "卡片预览",
        "tag" => "标签",
        "text" => "短语",
        "star" => "点赞数",
//        "is_pickpool" => "是否在卡片池",
//        "is_delete" => "是否删除",
    ];
    protected $table_option = [
        "pic_url" => [
            'type' => self::TYPE_TX_COS,
            "host" => SystemModel::TX_COS_HOST,
            'path' => "user-card",
        ],
        "full_pic_url" => [
            'type' => self::TYPE_TX_COS,
            "host" => SystemModel::TX_COS_HOST,
            'path' => "user-card",
        ],
        "user_name" => [
            "table_width" => "100"
        ],
        "is_pickpool" => [
            'type' => self::TYPE_SELECT,
            "data" => [
                "0" => "否",
                "1" => "是",
            ]
        ],
    ];


    public function sendPick($id)
    {
        $data = [];
        $data["left_cheese_card"] = CheeseCardModel::getInstance()->where([
            "id" => $id
        ])->find();
        $this->displayByData($data);
    }


    protected function data(Query $model, $page, $search, $filter = false, $w = [])
    {
        return parent::data($model, $page, $search, $filter, ["is_delete" => 0, "is_yellow" => "2"]); // TODO: Change the autogenerated stub
    }

    protected function dataIterator(&$data)
    {
        if (!empty($data["tag"])) {
            $data["tag"] = implode(",", json_decode($data["tag"]));
        }
    }

    protected function saveDataFilter(&$data)
    {
        $data["tag"] = explode(",", $data["tag"]);
        if (!is_array($data["tag"])) {
            $this->displayByError("标签有误,标签之间用英文逗号隔开");
        }
        $data["tag"] = json_encode($data["tag"], JSON_UNESCAPED_UNICODE);
    }

    public function delete($id)
    {
        CheeseCardModel::getInstance()->where(["id" => $id])->save([
            "is_delete" => 1
        ]);
        $this->displayBySuccess();
    }
}