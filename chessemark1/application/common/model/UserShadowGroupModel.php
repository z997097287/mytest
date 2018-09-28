<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 17:56
 */

namespace app\common\model;


class UserShadowGroupModel extends BaseModel
{
    //用户表
    protected $table_name = "user_shadow_group";

    protected function generateTableField()
    {
        return [
            "name" => "分组名",
        ];
    }

}