<?php

namespace app\admino\controller;


use app\common\controller\BaseController;
use app\common\model\SystemModel;
use think\Log;
use think\Request;
use think\Session;

class AdminBase extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        if (!Session::has('user')) {
            if ($this->ext != "json") {
                redirect('/Admino/Login/index');
            } else {
                $this->displayByError("未登录", 501);
            }
        }
        $data = Session::get('user');
        $_level = $data['level_id'];
        $_json = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/public/menu/{$_level}.json"), true);
        $this->assign('json', $_json);

        $system = new  SystemModel();
        $system_data = $system->select();
        $system_assign = [];
        for ($i = 0; $i < count($system_data); $i++) {
            $system_assign[$system_data[$i]["key"]] = $system_data[$i]["value"];
        }
        $this->assign('system', $system_assign);
    }

}