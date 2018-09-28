<?php

namespace app\common\controller;

use think\Controller;
use think\exception\HttpResponseException;
use think\Response;
use think\Session;

class BaseController extends Controller
{



    public function _empty()
    {
        return $this->displayByData();
    }

    //url后缀
    public $ext;

    /**
     * 初始化
     */
    protected function _initialize()
    {
        Session::start();
        //获取url后缀
        $this->ext = request()->ext();
    }

    /**
     * 判断后缀是否是html
     * @return mixed
     */
    public function isHtml()
    {
        if (empty($this->ext) || $this->ext == 'html') {
            return true;
        }
        return false;
    }


    protected function templateDisplay($data, $template)
    {
        $result = $this->fetch($template, $data);
        $response = Response::create($result, "html")->header([]);
        throw new HttpResponseException($response);
    }

    protected function displayByArray($data = [], $template = "")
    {
        if ($this->isHtml()) {
            $this->templateDisplay($data, $template);
        }
        $this->displayJson($data);
    }

    public function displayByData($data = [], $template = "")
    {
        $data = [
            'code' => 0,
            'msg' => 'success',
            'data' => $data,
        ];
        return $this->displayByArray($data, $template);
    }

    public function displayBySuccess($msg = "操作成功！", $url = '')
    {
        if(empty($url)){
            $url = $_SERVER["HTTP_REFERER"];
        }
        $data['code'] = 0;
        $data['msg'] = $msg;
        $data['data'] = null;
        if ($this->isHtml()) {
//            $this->success($msg, $url);
            throw new HttpResponseException(redirect($url));
        }
        $this->displayJson($data);
    }


    public function displayByError($msg = "操作失败！", $code = -1, $data = [])
    {
        $data['code'] = $code;
        $data['msg'] = $msg;
        $data['data'] = null;
        if ($this->isHtml()) {
            $this->error($msg, null, null, 1);
        }
        $this->displayJson($data);
    }


    protected function displayBoolByData($result)
    {
        if ($result !== false) {
            $this->displayBySuccess();
        }
        $this->displayByError();
    }


    protected function displayJson($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

}