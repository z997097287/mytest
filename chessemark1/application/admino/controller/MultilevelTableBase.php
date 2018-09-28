<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/29
 * Time: 10:39
 */

namespace app\admino\controller;


use think\db\Query;
use think\Request;

class MultilevelTableBase extends AdminBase
{
    protected $model = '';
    //主键字段
    protected $primary_key = 'id';
    protected $title = '';
    //字段，查找数据时过滤
    protected $field = '';
    //是否显示
    protected $primary_show = true;

    protected $table_header = [];
//    protected $table_header_more = [];

    const SEARCH_FUZZY = "SEARCH_FUZZY";
    const SEARCH_FULL_MATCH = "SEARCH_FULL_MATCH";
    protected $search_header = false;

    protected $table_option = [];


    protected $index_view = "/multileveltablebase/index";
    protected $save_view = "/multileveltablebase/save";

    protected $limit = 10;
    protected $order = "id desc";

    //普通输入框
    const TYPE_TEXT = "TYPE_TEXT";
    const TYPE_DISABLED_TEXT = "TYPE_DISABLED_TEXT";
    //富文本
    const TYPE_TEXTAREA = "TYPE_TEXTAREA";
    const TYPE_ORDINARY_TEXTAREA = "TYPE_ORDINARY_TEXTAREA";
    //选择 table_option.data.key = val
    const TYPE_SELECT = "TYPE_SELECT";
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

    public function __construct()
    {
        parent::__construct();
//        if (empty($this->table_header_more)) {
//            $this->table_header_more = $this->table_header;
//        }
    }

    public function index($id = 0, $page = 1, $search = false)
    {
        $model = db($this->model);
        $second_data = [];
        if (!empty($id)) {
            $data = $model->where($this->field, $id)->order($this->order)->page($page, $this->limit)->select();
            $list = [];
            foreach ($data as $item) {
                $vo = $this->filterArr($item, $this->table_header);
                array_push($list, $vo);
            }

            $count = $model->where($this->field, $id)->count();
            $second_data = [
                'count' => $count,
                'data' => $list
            ];
        }
        $list = $this->getFirstLevel($model, $this->field, $page);
        $this->displayByData(array(
            "title" => $this->title,
            "search_header" => $this->search_header,
            "list_header" => $this->table_header,
            "list_header_more" => $this->tableHeaderMore(),
            "list_data" => $list,
            "list_data_option" => $this->table_option,
            "list_limit" => $this->limit,
            "list_data_second" => $second_data,
            "field" => $this->field
        ), $this->index_view);
    }

    public function save($id = 0)
    {

        $model = db($this->model);
        if ($_POST) {

            if ($id > 0) {
                $arr = array_filter($_POST);
                $model->where([
                    $this->primary_key => $id
                ])->update($arr);
            } else {
                $arr = array_filter($_POST);
                $model->insert($arr);
            }
        }
        if ($id > 0) {
            $data = $model->find([
                $this->primary_key => $id
            ]);
        } else {
            $data = [];
        }

        $this->displayByData(array(
            "title" => $this->title,
            "primary_key" => $this->primary_key,
            "header" => $this->tableHeaderMore(),
            "data_option" => $this->table_option,
            "data" => $data,
            "field" => $this->field
        ), $this->save_view);
    }

    public
    function delete($id)
    {
        $model = db($this->model);
        $model->where(array(
            $this->primary_key => $id
        ))->delete();

    }

    protected
    function tableHeaderMore()
    {
        return $this->table_header;
    }


    protected
    function getFirstLevel($model, $field, $page)
    {
        $data = $model->where($field, 0)->order($this->order)->page($page, $this->limit)->select();
        $list = [];
        foreach ($data as $item) {
            $vo = $this->filterArr($item, $this->table_header);
            array_push($list, $vo);
        }
        $count = $model->where($field, 0)->count();
        return [
            'count' => $count,
            'data' => $list
        ];
    }

    protected
    function getUnFirstLevel($model, $field, $page)
    {
        $data = $model->where($field . '!=0')->order($this->order)->page($page, $this->limit)->select();
        $list = [];
        foreach ($data as $item) {
            $vo = $this->filterArr($item, $this->table_header);
            array_push($list, $vo);
        }
        $count = $model->where($field . '!=0')->count();
        return [
            'count' => $count,
            'data' => $list
        ];
    }

    protected
    function filterArr($firstArr, $secondArr)
    {
        $a = array_intersect(array_flip($firstArr), array_flip($secondArr));
        $arrs = array_flip($a);
        return $arrs;
    }

    protected
    function generateText($label, $placeholder = '', $value = '')
    {
        return $this->generateInputType($label, 'TEXT', $placeholder, $value);
    }

    protected
    function generateInputType($label, $type, $placeholder = '', $value = '')
    {
        $inputType = strtoupper($type);
        $arr = [];
        switch ($inputType) {
            case 'TEXT':
                $arr = [
                    "type" => TYPE_TEXT,
                    "label" => $label,
                    "placeholder" => $placeholder,
                    "value" => $value
                ];
                break;
        }
        return $arr;
    }

}