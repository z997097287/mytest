<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/21
 * Time: 14:30
 */

namespace app\admino\controller;


use app\api\controller\CheeseCardCommentStar;
use app\api\controller\CheeseCardStar;
use app\common\model\CheeseCardModel;
use app\common\model\SystemModel;
use app\common\model\UserModel;
use think\db\Query;

class CheeseCardComment extends SingleTableBase
{
    protected $model = "cheese_card_comment";
    protected $primary_show = false;
    protected $title = "卡片评论";

    protected $is_add = false;
    protected $is_edit = false;
//    protected $is_del = false;

    protected $table_header = [
        "user_name" => '用户',
//        "cheese_card_user_id" => '发布卡片的用户编号',
        "full_pic_url" => '卡片',
        "content" => '评论内容',
        "star" => '点赞数',
        "created_at" => "评论时间"
    ];
    protected $table_header_more = [
        "user_name" => '用户',
//        "cheese_card_user_id" => '发布卡片的用户编号',
//        "cheese_card_id" => '卡片id',
        "full_pic_url" => '卡片',
        "content" => '评论内容',
        "star" => '点赞数',
        "created_at" => "评论时间"
    ];
    protected $table_option = [

        "full_pic_url" => [
            'type' => self::TYPE_TX_COS,
            "host" => SystemModel::TX_COS_HOST,
            'path' => "user-card",
        ],
    ];


    protected $table_ext_button_iframe = [
        "点赞" => "/Admino/UserShadow/selectUser",
//        "评论" => "/Admino/UserShadow/selectUser",
    ];

    protected function data(Query $model, $page, $search, $filter = false, $w = [])
    {
        if (isset($_GET['id'])) {
            $w = [
                "cheese_card_id" => $_GET['id']
            ];
        }
        return parent::data($model, $page, $search, $filter, $w); // TODO: Change the autogenerated stub
    }

    protected function dataIterator(&$data)
    {
        $data["full_pic_url"] = CheeseCardModel::getInstance()->where(["id" => $data["cheese_card_id"]])->find()["full_pic_url"];

        $tmp = UserModel::getInstance()->where([
            "id" => $data['user_id']
        ])->find();
        $data['user_name'] = $tmp['nickName'];

        parent::dataIterator($data); // TODO: Change the autogenerated stub
    }

    public function select($id, $pid, $type)
    {
        $this->displayBySuccess($type);
        $_REQUEST['debug'] = $id;
        $c = new CheeseCardCommentStar();
        $c->like($pid);
    }
}