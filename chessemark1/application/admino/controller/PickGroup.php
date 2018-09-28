<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/20
 * Time: 13:30
 */

namespace app\admino\controller;


use app\common\model\PickGroupModel;

class PickGroup extends SingleTableBase
{
    protected $model = "pick_group";
    protected $primary_show = false;
    protected $title = "pick列表";

    protected $table_header = [
        "left_cheese_card_id" => "左边的卡片id,被pick",
        "left_user_id" => "左边的用户ID",
        "left_user_name" => "左边用户名",
        "left_votes_num" => "左边的卡片票数",
        "left_user_is_read" => "左边的对消息是否已读",
        "right_cheese_card_id" => "右边的卡片id",
        "right_user_id" => "右边的用户ID",
        "right_user_name" => "右边用户名",
        "right_votes_num" => "右边的卡片票数",
        "right_user_is_read" => "右边的对消息是否已读",
        "deadline" => "截止时间",
        "recommend_weights" => "平台推荐权重"
    ];
    protected $table_header_more = [
        "left_cheese_card_id" => "左边的卡片id,被pick",
        "left_user_id" => "左边的用户ID",
        "left_user_name" => "左边用户名",
        "left_votes_num" => "左边的卡片票数",
        "left_user_is_read" => "左边的对消息是否已读",
        "right_cheese_card_id" => "右边的卡片id",
        "right_user_id" => "右边的用户ID",
        "right_user_name" => "右边用户名",
        "right_votes_num" => "右边的卡片票数",
        "right_user_is_read" => "右边的对消息是否已读",
        "deadline" => "截止时间",
        "recommend_weights" => "平台推荐权重"
    ];
    protected $search_header = [
        'left_user_name',
        'right_user_name'
    ];

}