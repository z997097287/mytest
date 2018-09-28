<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 9:16
 */

namespace app\admino\controller;


use app\common\model\UserModel;
use think\db\Query;

class User extends SingleTableBase
{
    protected $model = "user";
    protected $primary_show = false;
    protected $title = "用户列表";

    protected $table_header = [
        "nickName" => "姓名",
        "username" => "账号",
        "avatarUrl" => "头像",
        "gender" => ('性别'),
        "age" => ('年龄'),
        "city" => "城市",
        "province" => "省份",
        "country" => "国家",
        "language" => "语言",
        "constellation" => "星座",
        "cheese_num" => ('cheese卡数'),
        "signature" => ('个性签名'),
        "is_black" => ('状态'),
        "type" => "账号类型",
        "group" => "分组",
        "created_at" => ('注册时间'),
        "expire_in" => ('生成token时间')
    ];
    protected $table_header_more = [
        "username" => "用户名",
        "password" => "密码",
        "openid" => ("用户唯一标识"),
        "token" => ("token令牌"),
        "nickName" => "用户姓名",
        "username" => "账号",
        "avatarUrl" => "用户头像",
        "gender" => ('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知'),
        "age" => ('用户年龄'),
        "city" => "用户所在城市",
        "province" => "用户所在省份",
        "country" => "用户所在国家",
        "language" => "用户的语言，简体中文为zh_CN",
        "constellation" => "星座",
        "cheese_num" => ('cheese卡数'),
        "signature" => ('个性签名'),
        "is_black" => ('是否拉黑'),
        "type" => "账号类型,NONE为普通用户,SHADOW为影子用户",
        "group" => "分组",
        "expire_in" => ('生成token时间')
    ];

    protected $search_header = [
        "nickName"
    ];

    /**
     *【操作】添加影子账户
     * @method /Admino/User/addShadow.json
     * @return void
     */
    public function addShadow()
    {
        $_POST["openid"] = time() . rand(0, 10000);
        if (empty($_POST["password"]) || empty($_POST["username"])) {
            $this->displayByError("账号密码不可为空");
        }
        UserModel::getInstance()->add($_POST);
        $this->displayBySuccess();
    }

    /**
     * 【列表】用户列表
     * @method /Admino/User/index.json
     * @param int $page 分页
     * @param bool $search 查询
     * @param string $type 类别，NONE为普通用户，SHADOW为影子用户
     */
    public function index($page = 1, $search = false, $type = '')
    {
        $model = db($this->model);
        $data = $this->data($model, $page, $search, $type);
        $this->displayByData(array(
            "title" => $this->title,
            "search_header" => $this->search_header,
            "list_header" => $this->table_header,
            "list_header_more" => $this->table_header_more,
            "list_data" => $data['data'],
            "list_data_option" => $this->table_option,
            "list_count" => $data['count'],
            "list_limit" => $this->limit
        ), $this->index_view);
    }

    public function data(Query $model, $page, $search, $type = '')
    {
        $w = [];
        if (!empty($this->search_header)) {
            /** @var array $search_header */
            $search_header = $this->search_header;
            for ($i = 0; $i < count($search_header); $i++) {
                $item = $search_header[$i];
                $w = [
                    $item => ["like", "%{$search}%"]
                ];
            }
        }
        if ($type != '') {
            $w['type'] = $type;
        }

        $data = $model->where($w)->order($this->order)->page($page, $this->limit)->select();
        foreach ($this->table_option as $index => $item) {
            if ($item['type'] == self::TYPE_MULTIPLE_SELECT) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i][$index] = json_decode($data[$i][$index]);
                }
            }
        }


        return array(
            "count" => $model->where($w)->count(),
            "data" => $data,
        );
    }


}