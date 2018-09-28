<?php

namespace app\admino\controller;


use app\common\model\BaseModel;
use Exception;
use think\db\Query;
use think\Log;

class SingleTableBase extends AdminBase
{
    protected $model = "";
    protected $ext_model = false;
    //主键字段
    protected $primary_key = 'id';
    protected $title = "";
    protected $is_add = true;
    protected $is_del = true;
    protected $is_edit = true;

    //是否显示
    protected $primary_show = true;


    protected $table_header = [];
    protected $table_header_more = [];

    const SEARCH_FUZZY = "SEARCH_FUZZY";
    const SEARCH_FULL_MATCH = "SEARCH_FULL_MATCH";
    protected $search_header = false;
    protected $filter_header = false;

    protected $table_ext_button = false;

    protected $table_ext_button_iframe = false;


//    protected $table_option = [
//        "" => [
//            "type"=>self::TYPE_TEXT_APPEND,
//            "table_width"=>"300",
//            "data   "=>["a","b"],//前后缀
//        ],
//        "" => [
//            "type"=>self::TYPE_ORDINARY_TEXTAREA,
//            "table_width"=>"300",
//            "data   "=>"1",//1为禁用
//        ],
//        "" => [
//            "type"=>self::TYPE_SELECT,//或者TYPE_MULTIPLE_SELECT
//            "table_width"=>"300",
//            "data   "=>[
//                "key"=>"value"
//            ]
//        ]
//    ];

    protected $table_option = [];


    protected $index_view = "/singletablebase/index";
    protected $save_view = "/singletablebase/save";

    protected $limit = 10;
    protected $order = "id desc";

    //普通输入框
    const TYPE_TEXT = "TYPE_TEXT";
    const TYPE_DISABLED_TEXT = "TYPE_DISABLED_TEXT";
    const TYPE_CLEAR_TEXT_PASSWORD = "TYPE_CLEAR_TEXT_PASSWORD";
    //富文本
    const TYPE_TEXTAREA = "TYPE_TEXTAREA";
    const TYPE_ORDINARY_TEXTAREA = "TYPE_ORDINARY_TEXTAREA";
    //选择 table_option.data.key = val
    const TYPE_SELECT = "TYPE_SELECT";
    const TYPE_TIMESTAMP = "TYPE_TIMESTAMP";
    //可选择的输入框 table_option.data = [val1,val2]
    const TYPE_SELECT_INPUT = "TYPE_SELECT_INPUT";
    //多选框,回传以数组序列化的方式 table_option.data.key = val
    const TYPE_MULTIPLE_SELECT = "TYPE_MULTIPLE_SELECT";
    //时间
    const TYPE_TIME = "TYPE_TIME";
    //图片
    const TYPE_IMAGE = "TYPE_IMAGE";

    //数字输入框
    const TYPE_NUM = "TYPE_NUM";
    //头尾补齐输入框 table_option.data = ['头文字','尾文字']
    const TYPE_TEXT_APPEND = "TYPE_TEXT_APPEND";
    const TYPE_COMPLEX_MULTIPLE_SELECT = "TYPE_COMPLEX_MULTIPLE_SELECT";
    //腾讯云储存
    const TYPE_TX_COS = "TYPE_TX_COS";

    //标签数组类
    const TYPE_ARRAY = "TYPE_ARRAY";


    public function __construct()
    {
        parent::__construct();

        if (empty($this->table_header_more)) {
            $this->table_header_more = $this->table_header;
        }

    }

    public function index($page = 1, $search = false, $filter = false)
    {
        $model = db($this->model);
        $data = $this->data($model, $page, $search, $filter);
        //渲染到前端由vue做判断
        $this->displayByData(array(
            "table_ext_button" => $this->table_ext_button,
            "table_ext_button_iframe" => $this->table_ext_button_iframe,
            "is_add" => $this->is_add,
            "is_edit" => $this->is_edit,
            "is_del" => $this->is_del,
            "title" => $this->title,
            "search_header" => $this->search_header,
            "filter_header" => $this->filter_header,
            "list_header" => $this->table_header,
            "list_header_more" => $this->table_header_more,
            "list_data" => $data['data'],
            "list_data_option" => $this->table_option,
            "list_count" => $data['count'],
            "list_limit" => $this->limit
        ), $this->index_view);
    }

    protected function dataIterator(&$data)
    {
    }

    protected function data(Query $model, $page, $search, $filter = false, $w = [])
    {
        if (!empty($this->search_header) && !empty($search)) {
            /** @var array $search_header */
            $search_header = $this->search_header;
            for ($i = 0; $i < count($search_header); $i++) {
                $item = $search_header[$i];
                $w = [
                    $item => ["like", "%{$search}%"]
                ];
            }
        }
        if (!empty($this->filter_header) && !empty($filter)) {
            /** @var array $filter_header */
            $filter_header = $this->filter_header;
            for ($i = 0; $i < count($filter_header); $i++) {
                $item = $filter_header[$i];
                if (!empty($filter[$item])) {
                    if ($this->table_option[$item]["type"] == self::TYPE_SELECT) {
                        $w[$item] = ["like", "{$filter[$item]}"];
                    } else {
                        $w[$item] = ["like", "%{$filter[$item]}%"];
                    }
                }
            }
        }

        $data = $model->where($w)->order($this->order)->page($page, $this->limit)->select();

        for ($i = 0; $i < count($data); $i++) {
            if ($this->ext_model) {
                foreach ($this->ext_model as $index => $item) {
                    $ext_model = db($index);
                    $ext_data = $ext_model->where([$item => $data[$i][$this->primary_key]])->find();
                    unset($ext_data[$this->primary_key]);
                    $ext_data = $ext_data ? $ext_data : [];
                    $data[$i] += $ext_data;
                }
            }


            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT) {
                    $data[$i][$index] = json_decode($data[$i][$index]);

                }
                if ($item['type'] == self::TYPE_ARRAY) {
                    $data[$i][$index] = json_decode($data[$i][$index]);
                    if (!$data[$i][$index]) {
                        $data[$i][$index] = [];
                    }
                }
                if ($item["type"] == self::TYPE_TIMESTAMP) {
                    $data[$i][$index] = date("Y-m-d h:i:s", $data[$i][$index]);
                }
            }


            $this->dataIterator($data[$i]);
        }
        return array(
            "count" => $model->where($w)->count(),
            "data" => $data,
        );
    }


    protected function saveDataFilter(&$data)
    {

    }

    public function save($id = 0)
    {

        $model = db($this->model);
        if ($_POST) {
            $this->saveDataFilter($_POST);
            foreach ($this->table_option as $index => $item) {
                if ($item['type'] == self::TYPE_MULTIPLE_SELECT || $item['type'] == self::TYPE_ARRAY) {
                    $_POST[$index] = json_encode($_POST[$index], JSON_UNESCAPED_UNICODE);
                }
                if($item['type'] == self::TYPE_TIMESTAMP){
                    $_POST[$index] = strtotime($_POST[$index]);
                }
            }
            if ($id > 0) {
                $fields = $model->getTableFields();
                foreach ($fields as $field) {
                    if ($_POST[$field]) {
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
                            if ($_POST[$field]) {
                                $u_data[$field] = $_POST[$field];
                            }
                        }
                        $ext_model->where([
                            $item => $id
                        ])->update($u_data);
                    }
                }

            } else {

                $fields = $model->getTableFields();
                $u_data = [];
                foreach ($fields as $field) {
                    if ($_POST[$field]) {
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
                            if ($_POST[$field]) {
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
                if ($item['type'] == self::TYPE_TIMESTAMP) {
                    $data[$index] = date("Y-m-d h:i:s", $data[$index]);
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

    public function delete($id)
    {
        $model = db($this->model);
        $model->where(array(
            $this->primary_key => $id
        ))->delete();

        if ($this->ext_model) {
            foreach ($this->ext_model as $index => $item) {
                $ext_model = db($index);
                $ext_model->where([$item => $id])->delete();
            }
        }


    }

}