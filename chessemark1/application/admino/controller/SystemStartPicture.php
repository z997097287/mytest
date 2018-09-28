<?php

namespace app\admino\controller;


use app\common\model\SystemModel;

class SystemStartPicture extends SingleTableBase
{

    protected $model = "system";
    protected $primary_show = false;
    protected $title = "启动页设置";
    protected $table_header = [
        "key" => "键",
        "value" => "值",
        "text" => "说明文字",
    ];
    protected $table_header_more = [
        "key" => "标识",
        "value" => "图片",
//        "text" => "说明文字",
    ];

    protected $table_option = [
        "key" => [
            'type' => self::TYPE_DISABLED_TEXT,
            "table_width" => "200",

        ],
        "value"=>[
            'type' => self::TYPE_TX_COS,
            "host" => SystemModel::TX_COS_HOST,
            'path' => "start-pic",
        ]
    ];
    protected $order = "id";


    public function save($id = 0)
    {
        $id = 18;


        $model = db($this->model);
        if ($_POST) {
            $this->saveDataFilter($_POST);
            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT || $item['type'] == self::TYPE_ARRAY) {
                    $_POST[$index] = json_encode($_POST[$index], JSON_UNESCAPED_UNICODE);
                }
            }
            if ($id > 0) {
                $fields = $model->getTableFields();
                foreach ($fields as $field) {
                    if($_POST[$field]){
                        $u_data[$field] = $_POST[$field];
                    }
                }
                $model->where([
                    $this->primary_key => $id
                ])->update($u_data);


                if ($this->ext_model) {
                    foreach ($this->ext_model as $index => $item) {
                        $ext_model = db($index);

                        $fields = $ext_model->getTableFields();
                        $u_data = [];
                        foreach ($fields as $field) {
                            if($_POST[$field]){
                                $u_data[$field] = $_POST[$field];
                            }
                        }
                        $ext_model->where([
                            $item => $id
                        ])->update($u_data);
                    }
                }
                SystemModel::redirect("/Admino/SystemStartPicture/save.html?id=18");


            } else {

                $fields = $model->getTableFields();
                $u_data = [];
                foreach ($fields as $field) {
                    if($_POST[$field]){
                        $u_data[$field] = $_POST[$field];
                    }
                }
                $id = $model->insert($u_data);
                if ($this->ext_model) {
                    foreach ($this->ext_model as $index => $item) {

                        $_POST[$item] = $model->getLastInsID();
                        $ext_model = db($index);

                        $fields = $ext_model->getTableFields();
                        $u_data = [];
                        foreach ($fields as $field) {
                            if($_POST[$field]){
                                $u_data[$field] = $_POST[$field];
                            }
                        }


                        $ext_model->insert($u_data);
                    }
                }
            }

            Log::writeRemote("[{$this->model}][{$model->getLastSql()}]");
        }
        if ($id > 0) {
            $data = $model->find([
                $this->primary_key => $id
            ]);
            if ($this->ext_model) {
                foreach ($this->ext_model as $index => $item) {
                    $ext_model = db($index);
                    $ext_data = $ext_model->where([$item => $data[$this->primary_key]])->find();
                    unset($ext_data[$this->primary_key]);
                    $ext_data = $ext_data ? $ext_data : [];
                    $data += $ext_data;
                }
            }
            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT || $item['type'] == self::TYPE_ARRAY) {
                    $data[$index] = json_decode($data[$index]);
                    empty($data[$index]) && $data[$index] = [];
                }
            }

        } else {
            $data = [];
        }

        $this->dataIterator($data);
        $this->displayByData(array(
            "title" => $this->title,
            "is_only_read" => $_GET["is_only_read"],
            "primary_key" => $this->primary_key,
            "header" => $this->table_header_more,
            "data_option" => $this->table_option,
            "data" => $data
        ), $this->save_view);


    }

}