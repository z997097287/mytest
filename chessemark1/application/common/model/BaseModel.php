<?php

namespace app\common\model;

use Redis;
use think\Config;
use think\Db;
use think\db\Query;
use think\Log;
use think\Model;

class BaseModel extends Query
{
    protected $table_prefix;

    public static $instance;

    public static $base_connection = null;

    /**
     * @return static
     */
    public static function getInstance()
    {
        $t = get_called_class();
        if (static::$instance[$t] == null) {
            static::$instance[$t] = new static();
        }
        return static::$instance[$t];
    }


    protected $table_name = "";
    /** @var array $table_field 表字段 */
    protected $table_field = array();

    protected function generateTableField()
    {
        return $this->table_field;
    }

    protected $init_data = [];

    protected function initData()
    {
        return $this->init_data;

    }

    /**
     * 初始化表
     */
    public function initializeTheTable()
    {
        if (empty($this->table_name)) {
            return false;
        }

        $table_name = "{$this->table_prefix}{$this->table_name}";
        $table_data = $this->query("show tables;");
        $table_hash = array();
        for ($i = 0; $i < count($table_data); $i++) {
            foreach ($table_data[$i] as $item) {
                $table_hash[$item] = true;
            }
        }
        if (!isset($table_hash[$table_name])) {
            $this->query("CREATE TABLE `{$table_name}` ( `id` INT NOT NULL AUTO_INCREMENT, `created_at` DATETIME NOT NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL DEFAULT CURRENT_TIMESTAMP() , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
            return true;
        }
        return false;
    }


    public static function generateType($type, $comment, $length, $default, $index = false)
    {
        return [
            "type" => $type,
            "comment" => $comment,
            "length" => $length,
            "default" => $default,
            "index" => $index,
        ];
    }


    const INDEX_TYPE_NONE = false;
    const INDEX_TYPE_INDEX = "INDEX";
    const INDEX_TYPE_UNIQUE = "UNIQUE ";

    public static function generateINT($comment = "", $length = 11, $default = 0, $index = self::INDEX_TYPE_NONE)
    {
        return self::generateType("INT", $comment, $length, $default, $index);
    }

    public static function generateVARCHAR($comment = "", $length = 128, $default = '', $index = self::INDEX_TYPE_NONE)
    {
        return self::generateType("VARCHAR", $comment, $length, $default, $index);
    }

    public static function generateTEXT($comment = "", $default = '', $index = self::INDEX_TYPE_NONE)
    {
        return self::generateType("TEXT", $comment, 0, $default, $index);
    }

    public static function generateDATETIME($comment = "", $default = '0000-00-00 00:00:00', $index = self::INDEX_TYPE_NONE)
    {
        return self::generateType("DATETIME", $comment, 0, $default, $index);
    }

    public static function generateDECIMAL($comment = "", $length = "10,2", $default = '0', $index = self::INDEX_TYPE_NONE)
    {
        return self::generateType("DECIMAL", $comment, $length, $default, $index);
    }


    /**
     * 初始化表结构
     */
    public function initializeTheTableStructure()
    {
        if (empty($this->table_name)) {
            return;
        }
        $table_name = "{$this->table_prefix}{$this->table_name}";
        $fields = $this->getTableFields();
        $field_hash = array();
        for ($i = 0; $i < count($fields); $i++) {
            $field_hash[$fields[$i]] = true;
        }
        $last = "id";

        $table_field = $this->generateTableField();
        foreach ($table_field as $index => $item) {
            if (!isset($field_hash[$index])) {
                $type = !isset($item["type"]) ? "VARCHAR" : $item["type"];
                switch (strtoupper($type)) {
                    case "VARCHAR":
                        {
                            $length = !isset($item["length"]) ? "128" : $item["length"];
                            $default = !isset($item["default"]) ? "" : $item["default"];
                            $comment = !isset($item["comment"]) ? "" : $item["comment"];
                            break;
                        }
                    case "INT":
                        {
                            $length = !isset($item["length"]) ? "11" : $item["length"];
                            $default = !isset($item["default"]) ? "0" : $item["default"];
                            $comment = !isset($item["comment"]) ? "" : $item["comment"];
                            break;
                        }
                    case "TEXT":
                        {
                            $length = !isset($item["length"]) ? "0" : $item["length"];
                            $default = !isset($item["default"]) ? "" : $item["default"];
                            $comment = !isset($item["comment"]) ? "" : $item["comment"];
                            break;
                        }
                    default :
                        {
                            $length = !isset($item["length"]) ? NULL : $item["length"];
                            $default = !isset($item["default"]) ? "" : $item["default"];
                            $comment = !isset($item["comment"]) ? "" : $item["comment"];
                        }
                }
                if (is_string($item)) {
                    $comment = $item;
                }
                $length_text = "";
                if ($length) {
                    $length_text = "({$length})";
                }

                $this->query("ALTER TABLE `{$table_name}` ADD `{$index}` {$type}{$length_text} NOT NULL DEFAULT '{$default}' COMMENT '{$comment}' AFTER `{$last}`;");
                if (!empty($item["index"])) {
                    $this->query("ALTER TABLE `{$table_name}` ADD {$item['index']} (`{$index}`);");
                }

//                exit("ALTER TABLE `{$table_name}` ADD `{$index}` {$type}({$length}) NOT NULL DEFAULT '{$default}' COMMENT '{$comment}' AFTER `{$last}`");;
            }
            $last = $index;
        }
    }

    public function __construct($name = "")
    {
        if (!empty($name)) {
            $this->table_name = $name;
        }
        if (self::$base_connection == null) {
            self::$base_connection = Db::connect([], true);
        }
        $this->table_prefix = config('database.prefix');
        parent::__construct(self::$base_connection);
        $this->name($this->table_name);
        if (!empty($this->table_name)) {
            $is_new = $this->initializeTheTable();
            $this->initializeTheTableStructure();
            if ($is_new) {
                Query::$info = [];
                $this->__construct();
                $this->insertAll($this->initData());
            }
        }
    }


    public function insertAll(array $dataSet, $replace = false)
    {
        $date = date("Y-m-d H:i:s", time());
        for ($i = 0; $i < count($dataSet); $i++) {
            empty($dataList[$i]['created_at']) && $dataList[$i]['created_at'] = $date;
            empty($dataList[$i]['updated_at']) && $dataList[$i]['updated_at'] = $date;
        }
        return parent::insertAll($dataSet, $replace);
    }


    public function add(array $data = [], $replace = false, $getLastInsID = false, $sequence = null)
    {
        return $this->insert($data, $replace, $getLastInsID, $sequence);
    }


    public function insert(array $data = [], $replace = false, $getLastInsID = false, $sequence = null)
    {
        empty($data['created_at']) && $data['created_at'] = date("Y-m-d H:i:s", time());
        empty($data['updated_at']) && $data['updated_at'] = date("Y-m-d H:i:s", time());
        return parent::insert($data, $replace, $getLastInsID, $sequence);
    }


    public function save($data = [])
    {
        empty($data['updated_at']) && $data['updated_at'] = date("Y-m-d H:i:s", time());
        return $this->update($data);
    }

    public function getAssembly()
    {
        return $this->find();
    }

    public function getAssemblyList()
    {
        return $this->select();
    }

    public function sync($data)
    {
        $m_data = $this->find();
        if (empty($data)) {
            $is = $this->add($data);
        } else {
            $is = $this->where($m_data)->save($data);
        }
        return $is !== false;

    }


    public static function selectPage($data, $filter_callback, $page = 1, $limit = 10, $sort_callback = false)
    {
        if ($sort_callback) {
            usort($data, $sort_callback);
        }
        $result = [];
        $t = 0;
        for ($i = 0; $i < count($data) && count($result) < $limit; $i++) {
            if ($filter_callback($data[$i])) {
                $t++;
                if ($t > ($page - 1) * $limit) {
                    $result[] = $data[$i];
                }
            }
        }
        return $result;
    }


    public $redis_cache_sec = 0;


    /** @var Redis $argc */
    public static $redis = null;

    public function fromCache($sec = 3)
    {
        if (empty(self::$redis)) {
            self::$redis = new Redis();
            $redis_cache = Config::get('database.redis_cache');
            self::$redis->connect($redis_cache["host"], $redis_cache["port"]);
        }

        $this->redis_cache_sec = $sec;
        return $this;
    }

    public function query($sql, $bind = [], $master = false, $class = false)
    {
        if ($this->redis_cache_sec > 0) {
            $key = "{$sql}-" . md5(serialize($bind));
            $result = self::$redis->get($key);
//            exit($key);
//            self::$redis->flushAll();
            if ($result === false) {
                $result = parent::query($sql, $bind, $master, $class);
                $is = self::$redis->ttl($key);
                if ($is < 0) {
//                    exit(\GuzzleHttp\json_encode($this->redis_cache_sec));
                    self::$redis->set($key, serialize($result));
                    self::$redis->setTimeout($key, $this->redis_cache_sec);
                }

//                Log::writeRemote("数据库");
            } else {
                $result = unserialize($result);
//                Log::writeRemote("缓存");
            }//
            return $result;
        }
        return parent::query($sql, $bind, $master, $class); // TODO: Change the autogenerated stub
    }


}