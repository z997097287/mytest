<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/29
 * Time: 9:25
 */

namespace app\admino\model;


use app\common\model\BaseModel;

class MenuModel extends BaseModel
{
    protected $table_name = "menu";

    protected function generateTableField()
    {
        return [
            "name" => "菜单名称",
            "type" => self::generateVARCHAR('菜单类型', 128, 'click'),
            "parent_id" => self::generateINT("父级id", 11, 0),
            "action" => self::generateVARCHAR('菜单动作，如果type为view,action就填url地址', 128, ''),
            "appid" => "链接小程序的openid",
            "pagepath" => "链接小程序得默认主页"
        ];
    }

    public function getDetailMenu()
    {
        $firstList = $this->getAppointMenu();
        $secondList = $this->getAppointMenu(1);
        $list = [];
        foreach ($firstList as $item) {
            $arrs = [];
            foreach ($secondList as $value) {
                if ($item['id'] == $value['parent_id']) {
                    // unset($item['id']);
                    $arr = $this->transformMenu($value);
                    $arrs[] = $arr;
                }
            }
            if (count($arrs) <= 0) {
                $item = $this->transformMenu($item);
            } else {
                $item['sub_button'] = $arrs;
                $item = $this->unsetFirstMenu($item);
            }
            $list[] = $item;
        }
        $list = array_filter($list);
        return $list;
    }

    public function getAppointMenu($parent_id = 0)
    {
        $model = new MenuModel();
        if ($parent_id != 0) {
            $list = $model->where('parent_id!=0')->select();
        } else {
            $list = $model->where('parent_id', $parent_id)->select();
        }
        return $list;
    }

    protected function transformMenu($arr)
    {
        unset($arr['id']);
        $type = strtolower($arr['type']);
        switch ($type) {
            case 'view':
                $action = $arr['action'];
                unset($arr['action']);
                unset($arr['appid']);
                unset($arr['pagepath']);
                $arr['url'] = $action;
                break;
            case 'click':
                $action = $arr['action'];
                unset($arr['action']);
                unset($arr['appid']);
                unset($arr['pagepath']);
                $arr['key'] = $action;
                break;
            case 'miniprogram':
                $action = $arr['action'];
                unset($arr['action']);
                $arr['url'] = $action;
                break;
        }
        unset($arr['created_at']);
        unset($arr['updated_at']);
        $arr['parent_id'] = null;
        $arr = array_filter(json_decode($arr, true));
        return $arr;

    }

    protected function unsetFirstMenu($arr)
    {
        unset($arr['id']);
        unset($arr['type']);
        unset($arr['parent_id']);
        unset($arr['action']);
        unset($arr['appid']);
        unset($arr['pagepath']);
        unset($arr['created_at']);
        unset($arr['updated_at']);
        return $arr;
    }

}