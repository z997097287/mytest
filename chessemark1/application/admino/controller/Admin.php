<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 9:16
 */

namespace app\admino\controller;


use app\common\lib\Encryption;
use think\Log;

class Admin extends SingleTableBase
{
    protected $model = "admin";
    protected $primary_show = false;
    protected $title = "用户管理";

    protected $table_header = [
        "username" => "用户名",
        "level" => "级别",
        "email" => "邮箱",
        "update_at" => "上次的登录时间"
    ];
    protected $table_header_more = [
        "username" => "用户名",
        "level" => "级别",
        "password" => "密码",
        "email" => "邮箱",
    ];
    protected $table_option = [
        "username" => [
            "table_width" => "300"
        ],
        "level" => [
            "table_width" => "200"
        ],
        "email" => [
            "table_width" => "400"
        ]
    ];


    public function save($id = 0)
    {

        $model = db($this->model);
        $data = $model->find([
            $this->primary_key => $id
        ]);
        if ($_POST) {
            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT) {
                    $_POST[$index] = json_encode($_POST[$index]);
                }
            }
            if ($id > 0) {
                if (!empty($_POST["password"])) {
                    $encryption = new Encryption($_POST["password"]);
                    $password = $encryption->to_string($encryption);
                    $_POST["password"] = $password;
                } else {
                    unset($_POST["password"]);
                }
                $model->where([
                    $this->primary_key => $id
                ])->update($_POST);

            } else {
                $id = $model->insert($_POST);
            }

            Log::writeRemote("[{$this->model}][{$model->getLastSql()}]");
        }
        if ($id > 0) {
            $data = $model->find([
                $this->primary_key => $id
            ]);
            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT) {
                    $data[$index] = json_decode($data[$index]);
                    empty($data[$index]) && $data[$index] = [];
                }
            }
        } else {
            $data = [];
        }

        $this->displayByData(array(
            "title" => $this->title,
            "primary_key" => $this->primary_key,
            "header" => $this->table_header_more,
            "data_option" => $this->table_option,
            "data" => $data
        ), $this->save_view);
    }

}