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
use app\common\model\JoinModel;
use app\common\model\PickGroupModel;
use app\common\model\PickGroupVotesModel;
use app\common\model\UserModel;
use think\Log;

class PickGroupVotes extends Token
{

    /**
     * 为一个pick投票
     * @param int $pick_group_id
     * @param string $position 被投票者的位置[左(LEFT)或者右(RIGHT)]
     */
    public function votes($pick_group_id, $position)
    {
        $user_id = $this->autoUserCertification();
        $pick_data = PickGroupModel::getInstance()->where(["id" => $pick_group_id])->getAssembly();


        if (empty($pick_data)) {
            $this->displayByError("该pick异常");
        }
        if (date("Y-m-d H:i:s", time()) > $pick_data["deadline"]) {
            $this->displayByError("已经超过截止时间");
        }
        $count = PickGroupVotesModel::getInstance()->where([
            "user_id" => $user_id,
            "pick_group_id" => $pick_group_id,
        ])->count();
        if ($count > 0) {
            $this->displayByError("你已经pick过了");
        }

        $pick_group_votes_cheese_card_id = null;
        $pick_group_votes_user_id = null;
        if ($position == "LEFT") {
            $pick_group_votes_cheese_card_id = $pick_data["left_cheese_card_id"];
            $pick_group_votes_user_id = $pick_data["left_user_id"];
        } elseif ($position == "RIGHT") {
            $pick_group_votes_cheese_card_id = $pick_data["right_cheese_card_id"];
            $pick_group_votes_user_id = $pick_data["right_user_id"];
        } else {
            $this->displayByError("投票异常");
        }
        $id = PickGroupVotesModel::getInstance()->add([
            "user_id" => $user_id,
            "pick_group_id" => $pick_group_id,
            "pick_group_votes_cheese_card_id" => $pick_group_votes_cheese_card_id,
            "pick_group_votes_user_id" => $pick_group_votes_user_id,
            "pick_group_votes_position" => $position,
        ], false, true);
        if(empty(JoinModel::getInstance()->where(['user_id'=>$user_id,'pick_group_id'=>$pick_group_id])->find())){
            JoinModel::getInstance()->insert(['user_id'=>$user_id,'pick_group_id'=>$pick_group_id,'type'=>'VOTES']);
        }
        if (empty($id)) {
            Log::writeRemote(PickGroupVotesModel::getInstance()->getLastSql());
            $this->displayByError();
        }

        EventModel::pickGroupVotes($id);

        $pick_data = PickGroupModel::getInstance()->where(["id" => $pick_group_id])->getAssembly();
        $pick_data['direction'] = PickGroupVotesModel::getInstance()->where('id', $id)->find();
        $this->displayByData($pick_data);
    }

    //判断用户是否投过票
    public function isVotes($pick_group_id)
    {
        $user_id = $this->autoUserCertification();
        $data = PickGroupVotesModel::getInstance()->where('pick_group_id', $pick_group_id)->find();
        $this->displayByData($data);
    }
    //返回用户投票未读列表
    public function unreadVotes($page=1){
        $user_id=$this->autoUserCertification();
        $data=array();
        $data=PickGroupVotesModel::getInstance()->where('pick_group_votes_user_id',$user_id)->order('updated_at desc')->page($page,10)->select();
        for($i=0;$i<count($data);$i++){
            $data[$i]['user_info']=UserModel::getInstance()->where('id',$data[$i]['user_id'])->find();
            $data[$i]['pick_group_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssemblyList();
            PickGroupVotesModel::getInstance()->where('id',$data[$i]['id'])->save(['is_read'=>1]);
        }
        if(!empty($data)){
            $this->displayByData($data);
        } else{
            $this->displayByData($data);
        }
    }
    public function Read($id){
        $user_id=$this->autoUserCertification();
        $data['is_read']=1;
        if(PickGroupVotesModel::getInstance()->where('id',$id)->save($data)){
            $this->displayBySuccess();
        }
    }


}
