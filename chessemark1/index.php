<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]


if (!file_exists(dirname(__file__) . "/sql_config.php")) {
    header("location: /Init/index.php");
    exit();
}
require_once  __DIR__ .'/cos-sdk-v5/cos-php-sdk-v5/vendor/autoload.php';
require_once  __DIR__ .'/core/autoload.php';
require_once __DIR__ . '/image-php-sdk/index.php';
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
