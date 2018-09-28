<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 17:31
 */

namespace app\api\controller;


use app\common\model\EventModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupCommentStarModel;

//评论点赞
class PickGroupCommentStar extends Token
{


    /**
     * 【操作】用户对pick中的评论点赞
     * @method /Api/PickGroupCommentStar/like.json
     * @param int $pick_group_comment_id 评论id
     */
    public function like($pick_group_comment_id)
    {
        //进行点赞操作,如有数据则退出
        $user_id = $this->autoUserCertification();

        $data = PickGroupCommentModel::getInstance()->where(["id" => $pick_group_comment_id])->find();

        $data = [
            'user_id' => $user_id,
            'pick_group_comment_id' => $pick_group_comment_id,
            'pick_group_comment_user_id' => $data["user_id"]
        ];
        //查询是否点赞
        $count = PickGroupCommentStarModel::getInstance()->where($data)->count();
        if ($count > 0) {
            $this->displayByError('你已经点赞过');
        }
        //进行点赞操作
        $res = PickGroupCommentStarModel::getInstance()->add($data, false, true);
        if ($res) {
            EventModel::pickGroupCommentStar($res);
            $this->displayBySuccess();
        }
        $this->displayByError();
    }


    /**
     * 【操作】用户对pick中的评论取消点赞
     * @method /Api/PickGroupCommentStar/cancelLike.json
     * @param $pick_group_comment_id
     */
    public function cancelLike($pick_group_comment_id)
    {
        //进行点赞删除操作
        $user_id = $this->autoUserCertification();
        $where = [
            'user_id' => $user_id,
            'pick_group_comment_id' => $pick_group_comment_id
        ];

        $res = PickGroupCommentStarModel::getInstance()->where($where)->delete();
        if ($res) {
            EventModel::unPickGroupCommentStar($user_id, $pick_group_comment_id);
            $this->displayBySuccess();
        }
        $this->displayByError();
    }
}