<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\BannerModel;
use app\common\model\CheeseCardModel;
use app\common\model\EventModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupCommentStarModel;
use app\common\model\PickGroupModel;
use app\common\model\UserModel;
use app\common\model\UserStarModel;

class PickGroupComment extends Token
{

    /**
     * [Pick页]获取某个Pick的评论
     * @param int $page
     * @param $pick_group_id
     * @internal param string $type
     */
    public function index($page = 1, $pick_group_id)
    {
        $user_id=$this->autoUserCertification();
        $data = PickGroupCommentModel::getInstance()->page($page, 10)->where([
            "pick_group_id" => $pick_group_id
        ])->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
           $data[$i]['is_star']=UserStarModel::getInstance()->where(['user_id'=>$user_id,'pick_group_comment_id'=>$data[$i]['id']])->count();
        }
        $this->displayByData($data);
    }

    public function isGive($pick_group_comment_id)
    {
        $user_id = $this->autoUserCertification();
        $data = [
            'user_id' => $user_id,
            'pick_group_comment_id' => $pick_group_comment_id,
            'pick_group_comment_user_id' => $user_id,
        ];
        $count = PickGroupCommentStarModel::getInstance()->where($data)->count();
        if ($count > 0) {
            $this->displayBySuccess('已经点过赞了');
        }
    }

    /**
     * [操作]评论一个pick组
     * @param $pick_group_id
     * @param $content
     */
    public function comment($pick_group_id, $content)
    {

        $user_id = $this->autoUserCertification();
        $data = PickGroupModel::getInstance()->where(["id" => $pick_group_id])->getAssembly();
        $is = PickGroupCommentModel::getInstance()->add([
            "user_id" => $user_id,
            "pick_group_comment_left_user_id" => $data["left_user_id"],
            "pick_group_comment_right_user_id" => $data["right_user_id"],
            "pick_group_id" => $pick_group_id,
            "content" => $content,
        ], false, true);
            EventModel::pickGroupComment($is);
        $this->displayByData($is);
    }

    /**
     *通知用户有pick组未读评论
     */
    public function getNumber($type = "pick_group_comment_left_user_id|pick_group_comment_right_user_id")
    {
        //获取当前用户id
        $user_id = $this->autoUserCertification();
        $read = "left_user_is_read|right_user_is_read";
        $data = PickGroupCommentModel::getInstance()->where([$type => $user_id, $read => 0])->getAssemblyList();
        $data['is_pick'] = 1;
        $this->displayByData($data);
    }

    /**
     *通知用户有pick组未读评论点赞
     */
    public function getStar()
    {
        //获取当前用户id
        $user_id = $this->autoUserCertification();
        $data = PickGroupCommentStarModel::getInstance()->where(['pick_group_comment_user_id' => $user_id, 'is_read' => 0])->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupCommentStarModel::getInstance()->where(['id' => $data[$i]['pick_group_comment_id']])->find();
            $data[$i]['pick_group_info'] = PickGroupModel::getInstance()->where('id', $data[$i]['info']['pick_group_id']);
            $data[$i]['user_info'] = UserModel::getInstance()->where(['id' => $data[$i]['user_id']])->getAssemblyList();
        }
        $data['is_pick_star'] = 1;
        $this->displayByData($data);

    }

    public function updateStar($id)
    {
        $user_id = $this->autoUserCertification();
        $data['is_read'] = 1;
        if (PickGroupCommentStarModel::getInstance()->where('id', $id)->save($data)) {
            $this->displayBySuccess();
        }
    }

    /**
     * 将pick组评论更新为已读
     * @param $pick_group_id
     */
    public function isRead($pick_group_id)
    {
        $left = "pick_group_comment_left_user_id";
        $right = "pick_group_comment_right_user_id";
        $user_id = $this->autoUserCertification();
        if (!empty(PickGroupCommentModel::getInstance()->where([$left => $user_id, 'pick_group_id' => $pick_group_id])->find())) {
            $data['left_user_is_read'] = 1;
            PickGroupCommentModel::getInstance()->where(['pick_group_id' => $pick_group_id])->save($data);
            $this->displayBySuccess();
        }
        if (!empty(PickGroupCommentModel::getInstance()->where([$right => $user_id, 'pick_group_id' => $pick_group_id])->find())) {
            $data['right_user_is_read'] = 1;
            PickGroupCommentModel::getInstance()->where(['pick_group_id' => $pick_group_id])->save($data);
            $this->displayBySuccess();
        }
    }


}
