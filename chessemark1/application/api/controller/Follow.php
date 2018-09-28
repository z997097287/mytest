<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\FollowModel;
use app\common\model\UserModel;

class Follow extends Token
{
    const TYPE_FOLLOW = "TYPE_FOLLOW";
    const TYPE_FAN = "TYPE_FAN";

    /**
     * [用户主页](关注/粉丝)列表
     * @param int $page
     * @param string $type [TYPE_FOLLOW/TYPE_FAN]
     */
    public function index($page = 1, $type = self::TYPE_FOLLOW)
    {
        $user_id = $this->autoUserCertification();
        if ($type == self::TYPE_FOLLOW) {
            $data = FollowModel::getInstance()->where(["user_id" => $user_id])->page($page, 10)->order("id desc")->getAssemblyList();
        } else {
            $data = FollowModel::getInstance()->where(["follow_user_id" => $user_id])->page($page, 10)->order("id desc")->getAssemblyList();
        }
        $this->displayByData($data);
    }

    //获取他人关注列表
    public function otherIndex($page = 1, $type = self::TYPE_FOLLOW, $other_user_id)
    {
        $user_id = $this->autoUserCertification();
        if ($type == self::TYPE_FOLLOW) {
            $data = FollowModel::getInstance()->where(["user_id" => $other_user_id])->page($page, 10)->order("id desc")->getAssemblyList();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['is_follow'] = FollowModel::getInstance()->where(['user_id' => $user_id, 'follow_user_id' => $data[$i]['follow_user_id']])->count();
            }
        } else {
            $data = FollowModel::getInstance()->where(["follow_user_id" => $other_user_id])->page($page, 10)->order("id desc")->getAssemblyList();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['is_follow'] = FollowModel::getInstance()->where(['user_id' => $user_id, 'follow_user_id' => $data[$i]['user_id']])->count();
            }
        }
        $this->displayByData($data);

    }

    /**
     * [操作]关注用户
     * @param $follow_user_id
     */
    public function follow($follow_user_id)
    {
        $user_id = $this->autoUserCertification();
        $data = FollowModel::follow($user_id, $follow_user_id);
        $this->displayByData($data);
    }

    /**
     * [操作]取消用户
     * @param $follow_user_id
     */
    public function unFollow($follow_user_id)
    {
        $self_user_id = $this->autoUserCertification();
        FollowModel::unFollow($self_user_id, $follow_user_id);
        $this->displayBySuccess();
    }

}
