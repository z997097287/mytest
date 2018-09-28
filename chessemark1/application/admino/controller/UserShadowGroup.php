<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 9:16
 */

namespace app\admino\controller;


use app\common\model\UserModel;
use app\common\model\UserShadowGroupModel;
use think\db\Query;

class UserShadowGroup extends SingleTableBase
{
    protected $model = "user_shadow_group";
    protected $primary_show = true;
    protected $title = "影子账户分组";

    protected $table_header = [
        "name" => "分组名",
    ];



}