<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 16:23
 */

namespace app\admino\controller;

use app\admino\model\KeywordModel;
use app\admino\model\MenuModel;
use app\common\controller\BaseController;
use app\common\model\SystemModel;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;

class Wechat extends BaseController
{
    protected $app;
    protected $server;
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $options = [
            'debug' => true,
            'app_id' => SystemModel::config('appid'),
            'secret' => SystemModel::config('appsecret'),
            'token' => SystemModel::config('token'),
            'log' => [
                'level' => 'debug',
                'file' => '/tmp/easywechat.log',
            ],
        ];
        $this->app = new Application($options);
        $this->server = $this->app->server;
        $this->user = $this->app->user;
    }

    public function index()
    {
        $this->createMenu();
        $this->reply();
        $this->server->serve()->send();
    }

    protected function reply()
    {
        $this->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    $model = new KeywordModel();
                    $data = $model->replyKeyword($message['Content']);
                    if (!$data) {
                        return new Text('对不起，我不知道');
                    } else {
                        $type = strtoupper($data['type']);
                        switch ($type) {
                            case 'TEXT':
                                return new Text($data['apply']);
                                break;
                        }
                    }
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                default:
                    return '收到其它消息';
                    break;
            }
        });
    }

    protected function createMenu()
    {
        $model = new MenuModel();
        $button = $model->getDetailMenu();
        $this->app->menu->create($button);
    }
}