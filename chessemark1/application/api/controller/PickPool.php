<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 12:07
 */

namespace app\api\controller;

use app\common\model\CheeseCardCommentModel;
use app\common\model\CheeseCardModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupModel;
use app\common\model\PickGroupVotesModel;
use app\common\model\UserCommentModel;
use app\common\model\UserModel;
use app\common\model\UserReadModel;
use app\common\model\UserStarModel;
use app\common\model\SystemMessageModel;

class PickPool extends Token
{
    //匹配规则
    public function poolMatching($cheese_card_id)
    {
        $user_id = $this->autoUserCertification();
        //根据cheese卡id，把卡牌放入pick池中
        $data['is_pickpool'] = 1;
        $row = CheeseCardModel::getInstance()->where('id', $cheese_card_id)->find();
        if(UserModel::getInstance()->where(['id'=>$row['user_id']])->where('type','<>','SHADOW')->find()){
            if ($row['win_content'] > 4) {
                $this->displayByError('你已经赢了5场哦');
            }
        }
        if (CheeseCardModel::getInstance()->where('id', $cheese_card_id)->save($data)) {
            //右边卡片的信息
            $right_info = $this->getOpponent($cheese_card_id);
            if (empty($right_info)) {
                $raw = CheeseCardModel::getInstance()->order('rand()')->where([
                    "created_at" => ["like", date("Y-m-d", time()) . "%"],'is_delete'=>0
                ])->limit(1)->getAssemblyList();
                if(empty($raw)){
                    $this->displayByError("卡片池为空");
                }
                $this->displayByData($raw);
            }
            $this->displayByData($right_info);
        }
    }

    //获取匹配到的信息
    public function getOpponent($id)
    {
        $row = CheeseCardModel::getInstance()->where('id', $id)->getAssembly();
        foreach ($row['tag'] as $value) {
            if ($data = CheeseCardModel::getInstance()->where(['is_pickpool' => '1', 'is_delete' => 0])->where('id', '<>', $id)->where('tag', 'like', '%' . $value . '%')->order('rand()')->limit(1)->getAssemblyList()) {
                return $data;
            }
        }
    }

    public function getComment($page = 1)
    {
        $user_id = $this->autoUserCertification();
        $type = "cheese_card_user_id|pick_group_comment_left_user_id|pick_group_comment_right_user_id";
        $data = UserCommentModel::getInstance()->where([$type => $user_id])->order('updated_at desc')->page($page, 10)->getAssemblyList();
        //dump($data);
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['comment_type'] == 1) {
                $data[$i]['user_info'] = UserModel::getInstance()->where('id', $data[$i]['user_id'])->find();
                //查询出cheese_card_user_info
                $data[$i]['cheese_card_user_info'] = UserModel::getInstance()->where('id', $data[$i]['cheese_card_user_id'])->find();
                //查询出cheese_card信息
                $data[$i]['cheese_card_info'] = CheeseCardModel::getInstance()->where('id', $data[$i]['cheese_card_id'])->find();
                //查询出评论内容
                $data[$i]['cheese_card_comment_info'] = CheeseCardCommentModel::getInstance()->where('id', $data[$i]['cheese_card_comment_id'])->find();
            }
            if ($data[$i]['comment_type'] == 2) {
                $data[$i]['user_info'] = UserModel::getInstance()->where('id', $data[$i]['user_id'])->find();
                //查询评论信息
                $data[$i]['pick_group_comment_info'] = PickGroupCommentModel::getInstance()->where('id', $data[$i]['pick_group_comment_id'])->find();
                if($user_id==$data[$i]['pick_group_comment_left_user_id']){
                    $data[$i]['group_type']=0;
                } else{
                    $data[$i]['group_type']=1;
                }
                //查询pick组信息
                $data[$i]['pick_group_info'] = PickGroupModel::getInstance()->where('id', $data[$i]['pick_group_id'])->getAssemblyList();
            }
            //批量更新为已读
            UserStarModel::getInstance()->where('id',$data[$i]['id'])->save(['is_read'=>1]);
        }
        $this->displayByData($data);
    }

    public function updateComment($id)
    {
        //更新评论为已读
        $data['is_read'] = 1;
        if (UserCommentModel::getInstance()->where('id', $id)->save($data)) {
            $this->displayBySuccess();
        }
    }
    //查询是否信息未读
    public function getMessage(){
        $user_id=$this->autoUserCertification();
        $type="cheese_card_user_id|pick_group_comment_left_user_id|pick_group_comment_right_user_id";
        $te="cheese_card_user_id|cheese_card_comment_user_id|pick_group_comment_user_id";
        $data=UserCommentModel::getInstance()->where([$type=>$user_id,'is_read'=>0])->select();
        $result=UserStarModel::getInstance()->where([$te=>$user_id,'is_read'=>0])->find();
        $rows = SystemMessageModel::getInstance()->where(['user_id'=> $user_id,'is_read'=>0])->find();
        $now=PickGroupVotesModel::getInstance()->where('pick_group_votes_user_id',$user_id)->where('is_read',0)->find();
        $row['has_star']=0;
        $row['has_comment']=0;
        $row['has_system_message']=0;
        $row['has_votes']=0;
        if(!empty($data)){
            $row['has_comment']=1;
        }
        if(!empty($result)){
            $row['has_star']=1;
        }
        if(!empty($rows)){
            $row['has_system_message']=1;
        }
        if(!empty($now)){
            $row['has_votes']=1;
        }
        $this->displayByData($row);
    }
}