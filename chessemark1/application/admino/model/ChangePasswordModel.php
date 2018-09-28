<?php

namespace app\admino\model;

use app\common\model\BaseModel;

class ChangePasswordModel extends BaseModel
{
    protected $table_name = "change_password";


    protected function generateTableField()
    {
        return [
            "verify" => "验证码",
            "aid" => self::generateINT('管理员id'),
        ];
    }
}