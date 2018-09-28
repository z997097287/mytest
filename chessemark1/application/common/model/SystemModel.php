<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


class SystemModel extends BaseModel
{

    protected $table_name = "system";

    const TX_COS_HOST = "http://cheese-res-1257281477.cos.ap-shanghai.myqcloud.com";

    protected function generateTableField()
    {
        return [
            "text" => "说明文字",
            "key" => "键",
            "type" => self::generateVARCHAR("数据类型", "128", "TEXT"),
            "value" => self::generateTEXT("值"),
        ];
    }

    protected function initData()
    {
        return [
            [
                "text" => "后台标题",
                "key" => "admin_title",
                "value" => "欧轩网络CMS管理系统",
            ],
            [
                "text" => "旷视app_key",
                "key" => "fpp_app_key",
                "value" => ""
            ],
            [
                "text" => "旷视app_secret",
                "key" => "fpp_app_secret",
                "value" => ""
            ],
            [
                "text" => "腾讯云对象储存-Secretld",
                "key" => "txy_cos_secret_id",
                "value" => ""
            ],
            [
                "text" => "腾讯云对象储存-SecretKey",
                "key" => "txy_cos_secret_key",
                "value" => ""
            ],
            [
                "text" => "腾讯云对象储存-存储桶名称",
                "key" => "txy_cos_secret_bucket",
                "value" => ""
            ],
            [
                "text" => "腾讯云对象储存-Region",
                "key" => "txy_cos_secret_region",
                "value" => ""
            ],
            [
                "text" => "小程序APPID",
                "key" => "mini_program_app_id",
                "value" => ""
            ],
            [
                "text" => "小程序secret",
                "key" => "mini_program_secret",
                "value" => ""
            ],
            [
                "text" => "腾讯黄图识别",
                "key" => "autograph",
                "value" => ""
            ],
            [
                "text"=>"腾讯黄图识别开发者id",
                "key"=>"yellow_app_id",
                "value"=>""
            ]
        ];
    }

    public static function config($key, $value = null)
    {
        $model = new SystemModel();
        if ($value == null) {
//            $data = $model->get([
//                "key" => $key
//            ]);
            $data = $model->where([
                'key' => $key
            ])->find();
            return $data['value'];
        }

    }

    public function findByKey($key)
    {
        return $this->where(['key' => $key])->find();
    }

    public static function redirect($url, $p = [])
    {

        $query = "";
        if (!empty($p)) {
            $query = "?" . http_build_query($p);
        }
        header("Location: {$url}{$query}");
        exit();

    }

}