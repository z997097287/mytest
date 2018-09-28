<?php

namespace app\api\controller;


use app\common\model\CheeseCardCommentModel;
use app\common\model\CheeseCardCommentStarModel;
use app\common\model\EventModel;

class CheeseCardCommentStar extends Token
{


    /**
     * 【操作】用户对卡片中的评论点赞
     * @param int $cheese_card_comment_id
     */
    public function like($cheese_card_comment_id)
    {
        //进行点赞操作,如有数据则退出
        $user_id = $this->autoUserCertification();

        $data = CheeseCardCommentModel::getInstance()->where(["id" => $cheese_card_comment_id])->find();

        $data = [
            'user_id' => $user_id,
            'cheese_card_comment_id' => $cheese_card_comment_id,
            'cheese_card_comment_user_id' => $data["user_id"]
        ];
        //dump($data);
        //查询是否点赞
        $count = CheeseCardCommentStarModel::getInstance()->where($data)->count();
        if ($count > 0) {
            $this->displayByError('你已经点赞过');
        }
        //进行点赞操作
        $res = CheeseCardCommentStarModel::getInstance()->add($data);
        if ($res) {
            EventModel::cheeseCardCommentStar(CheeseCardCommentStarModel::getInstance()->getLastInsID());
            $this->displayBySuccess();
        }
        $this->displayByError();
    }


    /**
     * 【操作】用户对卡片中的评论取消点赞
     * @param $cheese_card_comment_id
     */
    public function cancelLike($cheese_card_comment_id)
    {
        //进行点赞删除操作
        $user_id = $this->autoUserCertification();
        $where = [
            'user_id' => $user_id,
            'cheese_card_comment_id' => $cheese_card_comment_id
        ];

        $res = CheeseCardCommentStarModel::getInstance()->where($where)->delete();
        if ($res) {
            EventModel::unCheeseCardCommentStar($user_id, $cheese_card_comment_id);
            $this->displayBySuccess();
        }
        $this->displayByError();
    }
}