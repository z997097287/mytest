<?php

namespace app\admino\controller;


use think\captcha\Captcha;
use think\Session;

class Index extends AdminBase
{

    public function welcome()
    {
        echo "<h1>你好</h1>";
    }
    public function index()
    {
        $data = Session::get('user');
        $_level = $data['level_id'];
        $_json = json_decode(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/public/menu/{$_level}.json"), true);
        $this->assign('json', $_json);
        $this->displayByData();
    }
    public function captcha_src()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    3,
            // 关闭验证码杂点
            'useNoise'    =>    false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    public function test1()
    {
        $this->displayByData();
    }

    public function test2()
    {
        $this->displayByData();
    }

    /*
     * 退出登录
     * http:/admino/index/loginout
     * method:get
     * */
    public function loginOut()
    {
        Session::set('user', null);
        $this->displayBySuccess('成功退出');
    }
}