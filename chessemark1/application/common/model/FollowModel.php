<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


use Exception;

class FollowModel extends BaseModel
{

    protected $table_name = "user_follow";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT("用户ID"),
            "follow_user_id" => self::generateINT("我关注的用户ID"),
            "is_each_other_follow" => self::generateINT("是否互相关注"),
        ];
    }


    public static function follow($user_id, $follow_user_id)
    {
        $c = FollowModel::getInstance()->where(["user_id" => $user_id, "follow_user_id" => $follow_user_id])->count();
        $is_each_other_follow = FollowModel::getInstance()->where(["user_id" => $follow_user_id, "follow_user_id" => $user_id])->count();
        if (empty($c)) {
            FollowModel::getInstance()->add([
                "user_id" => $user_id,
                "follow_user_id" => $follow_user_id,
                "is_each_other_follow" => $is_each_other_follow,
            ]);
            FollowModel::getInstance()->where(["user_id" => $follow_user_id, "follow_user_id" => $user_id])->save([
                "is_each_other_follow" => 1,
            ]);
        }
        return FollowModel::getInstance()->where(["user_id" => $user_id, "follow_user_id" => $follow_user_id])->find();
    }
    public static function unFollow($user_id, $follow_user_id)
    {
        $c = FollowModel::getInstance()->where(["user_id" => $user_id, "follow_user_id" => $follow_user_id])->delete();
        if ($c === false) {
            throw new Exception("取消关注失败");
        }
        FollowModel::getInstance()->where(["user_id" => $follow_user_id, "follow_user_id" => $user_id])->save([
            "is_each_other_follow" => 0,
        ]);

        return true;
    }
    public function getAssembly()
    {
        $data = $this->find();
        if (empty($data)) {
            return null;
        }
        $data["follow_user"] = UserModel::getInstance()->field(["id", "nickName", "avatarUrl", "diyAvatarUrl","signature"])->where(["id" => $data["follow_user_id"]])->find();
        $data["user"] = UserModel::getInstance()->field(["id", "nickName", "avatarUrl","diyAvatarUrl", "signature"])->where(["id" => $data["user_id"]])->find();
        return $data;
    }


    public function getAssemblyList()
    {
        $data = $this->select();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["follow_user"] = UserModel::getInstance()->field(["id", "nickName", "avatarUrl","diyAvatarUrl", "signature"])->where(["id" => $data[$i]["follow_user_id"]])->find();
            //$data[$i]["follow_user"] = PickGroupModel::getInstance()->field(["id"])->where(["id" => $data[$i]["follow_user_id"]])->find();
            $data[$i]["fan_user"] = UserModel::getInstance()->field(["id", "nickName", "avatarUrl","diyAvatarUrl", "signature"])->where(["id" => $data[$i]["user_id"]])->find();
        }
        return $data;
    }
}