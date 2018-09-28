<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\BannerModel;
use app\common\model\CheeseCardCommentModel;
use app\common\model\CheeseCardCommentStarModel;
use app\common\model\CheeseCardModel;
use app\common\model\EventModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupModel;
use app\common\model\UserModel;
use app\common\model\UserStarModel;

class CheeseCardComment extends Token
{

    /**
     * [卡片页]获取某个卡片的评论
     * @param int $page
     * @param $cheese_card_id
     * @internal param $pick_group_id
     * @internal param string $type
     */
    public function index($page = 1, $cheese_card_id)
    {
        $user_id = $this->autoUserCertification();
        $data = CheeseCardCommentModel::getInstance()->page($page, 10)->where([
            "cheese_card_id" => $cheese_card_id
        ])->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['is_star']=UserStarModel::getInstance()->where(['user_id'=>$user_id,'cheese_card_comment_id'=>$data[$i]['id']])->count();
        }
        $this->displayByData($data);
    }

    public function updateStar($id)
    {
        $user_id = $this->autoUserCertification();
        $data['is_read'] = 1;
        CheeseCardCommentStarModel::getInstance()->where('id', $id)->save($data);
    }

    /**
     * @param int $page
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNumber($page = 1)
    {
        //获取当前用户id
        $user_id = $this->autoUserCertification();
        $data['cheesse_card_conment'] = CheeseCardCommentModel::getInstance()->where('cheese_card_user_id', $user_id)->where('is_read', 0)->page($page, 10)->getAssemblyList();
        for ($i = 0; $i < count($data['cheesse_card_conment']); $i++) {
            $data['cheesse_card_conment'][$i]['user_info'] = UserModel::getInstance()->where('id', $data['cheesse_card_conment'][$i]['user_id'])->find();
            $data['cheesse_card_conment'][$i]['cheese_card_info'] = CheeseCardModel::getInstance()->where('id', $data['cheesse_card_conment'][$i]['cheese_card_id'])->find();
            $data['cheesse_card_conment'][$i]['is_cheese_card_comment'] = 1;
        }
        $type = "pick_group_comment_left_user_id|pick_group_comment_right_user_id";
        $read = "left_user_is_read|right_user_is_read";
        //dump($filter[$type]);
        $data['pick_group_comment'] = PickGroupCommentModel::getInstance()->where([$type => $user_id, $read => 0])->page($page, 10)->getAssemblyList();
        for ($i = 0; $i < count($data['pick_group_comment']); $i++) {
            $data['pick_group_comment'][$i]['card_info'] = PickGroupModel::getInstance()->where('id', $data['pick_group_comment'][$i]['pick_group_id'])->getAssemblyList($user_id);
            $data['pick_group_comment'][$i]['user_info'] = UserModel::getInstance()->where('id', $data['pick_group_comment'][$i]['user_id'])->find();
            $data['pick_group_comment'][$i]['is_pick_comment'] = 1;
        }
        $row = array_merge_recursive($data['cheesse_card_conment'], $data['pick_group_comment']);
        $this->displayByData($row);


    }

    /**
     * 更新评论为已读
     */
    public function updateRead($user_id, $cheese_card_id)
    {
        $data['is_read'] = 1;
        if (CheeseCardCommentModel::getInstance()->where('user_id', $user_id)->where('cheese_card_id', $cheese_card_id)->save($data)) {
            $this->displayBySuccess();
        } else {
            $this->displayByError();
        }

    }


    /**
     * [操作]评论一个卡片
     * @param $cheese_card_id
     * @param $content
     */
    public function comment($cheese_card_id, $content)
    {

        $user_id = $this->autoUserCertification();
        $data = CheeseCardModel::getInstance()->where(["id" => $cheese_card_id])->find();

        $is = CheeseCardCommentModel::getInstance()->add([
            "user_id" => $user_id,
            "cheese_card_user_id" => $data["user_id"],
            "cheese_card_id" => $cheese_card_id,
            "content" => $content,
        ],false,true);
        if ($is === false) {
            $this->displayByError();
        }
        EventModel::cheeseCardComment($is,$user_id, $cheese_card_id,$data["user_id"]);
        $this->displayBySuccess();
    }


}
