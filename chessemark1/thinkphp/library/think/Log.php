<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\exception\ClassNotFoundException;

/**
 * Class Log
 * @package think
 *
 * @method void log($msg) static
 * @method void error($msg) static
 * @method void info($msg) static
 * @method void sql($msg) static
 * @method void notice($msg) static
 * @method void alert($msg) static
 */
class Log
{
    const LOG = 'log';
    const ERROR = 'error';
    const INFO = 'info';
    const SQL = 'sql';
    const NOTICE = 'notice';
    const ALERT = 'alert';
    const DEBUG = 'debug';

    // 日志信息
    protected static $log = [];
    // 配置参数
    protected static $config = [];
    // 日志类型
    protected static $type = ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'];
    // 日志写入驱动
    protected static $driver;

    // 当前日志授权key
    protected static $key;

    /**
     * 日志初始化
     * @param array $config
     */
    public static function init($config = [])
    {
        $type = isset($config['type']) ? $config['type'] : 'File';
        $class = false !== strpos($type, '\\') ? $type : '\\think\\log\\driver\\' . ucwords($type);
        self::$config = $config;
        unset($config['type']);
        if (class_exists($class)) {
            self::$driver = new $class($config);
        } else {
            throw new ClassNotFoundException('class not exists:' . $class, $class);
        }
        // 记录初始化信息
        App::$debug && Log::record('[ LOG ] INIT ' . $type, 'info');
    }

    /**
     * 获取日志信息
     * @param string $type 信息类型
     * @return array
     */
    public static function getLog($type = '')
    {
        return $type ? self::$log[$type] : self::$log;
    }

    /**
     * 记录调试信息
     * @param mixed $msg 调试信息
     * @param string $type 信息类型
     * @return void
     */
    public static function record($msg, $type = 'log')
    {
        self::$log[$type][] = $msg;
        if (IS_CLI) {
            // 命令行下面日志写入改进
            self::save();
        }
    }

    /**
     * 清空日志信息
     * @return void
     */
    public static function clear()
    {
        self::$log = [];
    }

    /**
     * 当前日志记录的授权key
     * @param string $key 授权key
     * @return void
     */
    public static function key($key)
    {
        self::$key = $key;
    }

    /**
     * 检查日志写入权限
     * @param array $config 当前日志配置参数
     * @return bool
     */
    public static function check($config)
    {
        if (self::$key && !empty($config['allow_key']) && !in_array(self::$key, $config['allow_key'])) {
            return false;
        }
        return true;
    }

    /**
     * 保存调试信息
     * @return bool
     */
    public static function save()
    {
        if (!empty(self::$log)) {
            if (is_null(self::$driver)) {
                self::init(Config::get('log'));
            }

            if (!self::check(self::$config)) {
                // 检测日志写入权限
                return false;
            }

            if (empty(self::$config['level'])) {
                // 获取全部日志
                $log = self::$log;
                if (!App::$debug && isset($log['debug'])) {
                    unset($log['debug']);
                }
            } else {
                // 记录允许级别
                $log = [];
                foreach (self::$config['level'] as $level) {
                    if (isset(self::$log[$level])) {
                        $log[$level] = self::$log[$level];
                    }
                }
            }

            $result = self::$driver->save($log);
            if ($result) {
                self::$log = [];
            }
            Hook::listen('log_write_done', $log);
            return $result;
        }
        return true;
    }

    /**
     * 实时写入日志信息 并支持行为
     * @param mixed $msg 调试信息
     * @param string $type 信息类型
     * @param bool $force 是否强制写入
     * @return bool
     */
    public static function write($msg, $type = 'log', $force = false)
    {
        $log = self::$log;
        // 封装日志信息
        if (true === $force || empty(self::$config['level'])) {
            $log[$type][] = $msg;
        } elseif (in_array($type, self::$config['level'])) {
            $log[$type][] = $msg;
        } else {
            return false;
        }

        // 监听log_write
        Hook::listen('log_write', $log);
        if (is_null(self::$driver)) {
            self::init(Config::get('log'));
        }
        // 写入日志
        $result = self::$driver->save($log);
        if ($result) {
            self::$log = [];
        }
        return $result;
    }


    /**
     * 实时写入日志信息 并支持行为
     * @param mixed $msg 调试信息
     * @param string $type 信息类型
     * @param bool $force 是否强制写入
     * @return bool
     */
    public static function writeRemote($msg = "---END---", $type = 'log', $force = false)
    {
        $log = self::$log;
        // 封装日志信息
        if (true === $force || empty(self::$config['level'])) {
            $log[$type][] = $msg;
        } elseif (in_array($type, self::$config['level'])) {
            $log[$type][] = $msg;
        } else {
            return false;
        }

        // 监听log_write
        Hook::listen('log_write', $log);
        if (is_null(self::$driver)) {
            self::init(Config::get('log'));
        }
        // 写入即时日志
        $log["name"] = self::$config["remote_self_id"];
        $host = self::$config["remote_host"];
        $path = self::$config["remote_path"];
        $port = self::$config["remote_port"] ? self::$config["remote_port"] : 80;
        if (!empty($host) && !empty($path)) {
            self::post($host, $path, $port, ["data" => json_encode($log)]);
        }

        $log_file_name = $_SERVER["REQUEST_URI"];
        // 写入日志
        $result = self::$driver->save($log, "{$_SERVER["DOCUMENT_ROOT"]}/RemoteLog/", $log_file_name);
        if ($result) {
            self::$log = [];
        }
        return $result;
    }

    public static function post($host, $path, $port, $data)
    {
        $post = http_build_query($data);
        $len = strlen($post);

        $fp = fsockopen($host, $port, $errno, $errstr, 3);
        if (!$fp) {
            return;
        }

        $out = "POST $path HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n";
        $out .= "\r\n";
        $out .= $post . "\r\n";
        // echo($out);
        fwrite($fp, $out);

        // 注释掉如下代码实现不等待 HTTP 响应，从而实现“异步”
//    $receive = '';
//    while (!feof($fp)) {
//        $receive .= fgets($fp, 128);
//    }
//    echo "<br />" . $receive;


        fclose($fp);
    }

    /**
     * 静态调用
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$type)) {
            array_push($args, $method);
            return call_user_func_array('\\think\\Log::record', $args);
        }
    }

}
