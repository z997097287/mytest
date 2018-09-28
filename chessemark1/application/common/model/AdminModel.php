<?php

namespace app\common\model;

use app\common\lib\Encryption;

class AdminModel extends BaseModel
{

    protected $table_name = "admin";


    /**
     * 获取
     */
    public function obtain($username, $password)
    {
        $encryption = new Encryption($password);
        $password = $encryption->to_string($encryption);
        $where = ['password' => $password, 'username' => $username];
        return $this->where($where)->find();
    }


    /**
     * 更新密码
     */
    public function updatePassword($id, $password)
    {
        $encryption = new Encryption($password);
        $password = $encryption->to_string($encryption);
        return $this->where(['id' => $id])->update([
            'password' => $password
        ]);
    }

    protected function generateTableField()
    {
        return [
            "username" => "用户名",
            "password" => "密码",
            "level" => "级别",
            "email" => "邮箱",
        ];
    }

    protected function initData()
    {
        return [
            [
                "username" => "admin",
                "password" => "33e2t1fc3d033e22aeouc2140aec3x850csa99u21232f297al57a5a7438n4a0eo4a801fc3d0",
                "level" => "root",
                "email" => "782345044@qq.com",
            ]
        ];
    }

}