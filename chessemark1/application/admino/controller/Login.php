<?php

namespace app\admino\controller;

use app\admino\model\ChangePasswordModel;
use app\common\model\Admin;
use app\common\model\AdminModel;
use app\common\model\ChangePassword;
use app\common\lib\Encryption;
use app\common\model\UserModel;
use think\captcha\Captcha;
use think\Session;
use app\common\controller\BaseController;

class Login extends BaseController
{
    protected $admin;
    protected $change;

    public function __construct()
    {
        $this->admin = new AdminModel();
        $this->change = new ChangePasswordModel();
        parent::__construct();
    }

    public function index()
    {
        //页面渲染
        $this->displayByData();
    }


    public function loginByShadow($username, $password)
    {
        $data = UserModel::getInstance()->where(["username" => $username])->find();
        if (empty($data)) {
            $this->displayByError("没有该账户");
        }
        $data = UserModel::getInstance()->where(["username" => $username, "password" => $password])->find();
        if (!empty($data)) {
            Session::set('shadow_user', $data["id"]);
            $user = array(
                'username' => $data['username'],
                'user_id' => $data['id'],
                'level_id' => "shadow",
            );
            Session::set('user', $user);
            $this->displayBySuccess('登录成功', "/Admino/Index");
        }
        $this->displayByError('帐号或密码输入错误');
    }

    /**
     * 登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool|string $verify 验证码
     * @param bool $is_shadow
     */
    public function login($username, $password, $verify = false, $is_shadow = false, $type = "NONE")
    {
        if ($type == "SHADOW") {
            $this->loginByShadow($username, $password);
        }
        if (true || !empty($verify)) {
            $captcha = new Captcha();
            if (false && !$captcha->check($verify)) {
                $this->displayByError('验证码错误');
            }
            if (empty($username) || empty($password)) {
                $this->displayByError('帐号密码必须填');
            }
            $data = $this->admin->obtain($username, $password);
            if ($data) {
                $user = array(
                    'username' => $data['username'],
                    'user_id' => $data['id'],
                    'level_id' => $data['level'],
                );
                Session::set('user', $user);
                $this->displayBySuccess('登录成功', "/Admino/Index");
            }
            $this->displayByError('帐号或密码输入错误');
        }
        $this->displayByError('请输入验证码');

    }

    public function getPassword()
    {
        //页面渲染
        $this->displayByData();
    }

    /**
     * 更改密码
     */
    public function changePassword($verify, $npassword, $rpassword)
    {
        if (!empty($verify)) {
            $verify_res = $this->change->get($verify);
            if ($verify_res) {
                $nowTime = date('Y-m-d H:i:s');
                $minute = floor((strtotime($nowTime) - strtotime($verify_res['created_at'])) % 86400 / 60);
                if ($minute > 5) {
                    $this->change->deleteVerify($verify_res['id']);
                    $this->displayByError('验证码已经过期');
                }
                if ($npassword != $rpassword) {
                    $this->displayByError('两次密码需保持一致');
                }
                $res = $this->admin->updatePassword($verify_res['aid'], $npassword);
                if (!$res) {
                    $this->displayByError('更新密码失败，请重试');
                }
                $this->change->deleteVerify($verify_res['id']);
                $this->displayBySuccess('更新成功', 'login/index');
            }
            $this->displayByError('无效的验证码');
        }
        $this->displayByError('请输入验证码');
    }

}
