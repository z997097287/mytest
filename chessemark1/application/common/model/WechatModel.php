<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


use EasyWeChat\Foundation\Application;

class WechatModel extends BaseModel
{


    public static function getWxaCodeUnLimit($page, $scene)
    {
        $options = [
            'app_id' => SystemModel::config("mini_program_app_id"),
            'secret' => SystemModel::config("mini_program_secret"),
        ];
        $app = new Application($options);
        $access_token = $app->access_token->getToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$access_token}";

        $post_data = json_encode([
            'page' => $page,
            'scene' => $scene
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type:application/json',
                'content' => $post_data,
                'timeout' => 60
            ]
        ]);
        $result = file_get_contents($url, false, $context);
        return $result;
    }


}