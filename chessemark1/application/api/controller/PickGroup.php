<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\BannerModel;
use app\common\model\BillModel;
use app\common\model\CheeseCardModel;
use app\common\model\EventModel;
use app\common\model\JoinModel;
use app\common\model\PickGroupModel;
use app\common\model\SystemMessageModel;
use app\common\model\UserModel;
use app\common\model\PickGroupVotesModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\UserStarModel;

class PickGroup extends Token
{

    /**
     * [消息]我的Pick列表
     * @param int $page
     * @param string $type
     * @param int $user_id
     */
    public function index($page = 1, $type = "left_user_id")
    {
        $user_id = $this->autoUserCertification();
        $data = PickGroupModel::getInstance()->page($page, 10)->where([
            $type => $user_id
        ])->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->getAssemblyList();
            if (!empty($data[$i]["top_comment"][0]['id'])) {
                $data[$i]["top_comment"][0]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][0]['id'], 'user_id' => $user_id])->count();
            }
            if (!empty($data[$i]["top_comment"][1]['id'])) {
                $data[$i]["top_comment"][1]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][1]['id'], 'user_id' => $user_id])->count();
            }
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();
        }
        $this->displayByData($data);
    }

    /**
     * [消息]我参与的pick列表
     * @param int $page
     * @param string $type
     * @param int $user_id
     */
    public function participationPick($page = 1)
    {
        $user_id = $this->autoUserCertification();
        $data = JoinModel::getInstance()->page($page, 10)->where([
            'user_id' => $user_id
        ])->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['pick_group_id'],
            ])->find();
            $data[$i]['pick_group_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssembly();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["pick_group_id"]])->order("star desc")->limit(2)->getAssemblyList();
            if (!empty($data[$i]["top_comment"][0]['id'])) {
                if ($user_id == '') {
                } else {
                    $data[$i]["top_comment"][0]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][0]['id'], 'user_id' => $user_id])->count();
                }
                $data[$i]["top_comment"][0]['user'] = UserModel::getInstance()->where(["id" => $data[$i]["top_comment"][0]['user_id']])->find();
            }
            if (!empty($data[$i]["top_comment"][1]['id'])) {
                if ($user_id == '') {
                } else {
                    $data[$i]["top_comment"][1]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][1]['id'], 'user_id' => $user_id])->count();
                }
                $data[$i]["top_comment"][1]['user'] = UserModel::getInstance()->where(["id" => $data[$i]["top_comment"][1]['user_id']])->find();
            }
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["pick_group_id"]])->count();
        }
        $this->displayByData($data);
    }

    public function otherIndex($page = 1, $type = "left_user_id", $other_user_id = 2)
    {
        $user_id = $this->autoUserCertification();

        $data = PickGroupModel::getInstance()->page($page, 10)->where([
            $type => $other_user_id
        ])->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->getAssemblyList();
            if (!empty($data[$i]["top_comment"][0]['id'])) {
                $data[$i]["top_comment"][0]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][0]['id'], 'user_id' => $user_id])->count();
            }
            if (!empty($data[$i]["top_comment"][1]['id'])) {
                $data[$i]["top_comment"][1]['is_star'] = UserStarModel::getInstance()->where(["pick_group_comment_id" => $data[$i]["top_comment"][1]['id'], 'user_id' => $user_id])->count();
            }
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();
        }
        $this->displayByData($data);
    }

    public function whoWin()
    {
        //查询出所有pick未判断结果的
        $data = PickGroupModel::getInstance()->where('who_win', 0)->select();
        //得到当前时间
        $time = strtotime(date("Y-m-d H:i:s", time()));
        //查询出当前时间和截至时间的差值大于0的就是已经结束的pick次数
        for ($i = 0; $i < count($data); $i++) {
            $value = $data[$i]['deadline'];

            if ($time - strtotime($value) > 0) {
        //dump($a);
                if ($data[$i]['left_votes_num'] - $data[$i]['right_votes_num'] > 0) {
                    CheeseCardModel::getInstance()->where('id', $data[$i]['message_type'])->setInc('win_constant');
                    $win['who_win'] = 1;
                    $win['is_over']=1;
                    PickGroupModel::getInstance()->where('id', $data[$i]['id'])->save($win);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_WIN','user_id'=>$data[$i]['left_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片被更多p友喜欢了']);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_LOSE','user_id'=>$data[$i]['right_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片没有被更多p友喜欢了']);
                }
                if ($data[$i]['left_votes_num'] - $data[$i]['right_votes_num'] < 0) {
                    CheeseCardModel::getInstance()->where('id', $data[$i]['right_cheese_card_id'])->setInc('win_constant');
                    $win['who_win'] = 2;
                    $win['is_over']=1;
                    PickGroupModel::getInstance()->where('id', $data[$i]['id'])->save($win);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_WIN','user_id'=>$data[$i]['right_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片被更多p友喜欢了']);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_LOSE','user_id'=>$data[$i]['left_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片没有被更多p友喜欢了']);
                }
                if ($data[$i]['left_votes_num'] - $data[$i]['right_votes_num'] == 0) {
                    CheeseCardModel::getInstance()->where('id', $data[$i]['right_cheese_card_id'])->setInc('win_constant');
                    $win['who_win'] = 3;
                    $win['is_over']=1;
                    PickGroupModel::getInstance()->where('id', $data[$i]['id'])->save($win);
                    CheeseCardModel::getInstance()->where('id', $data[$i]['left_cheese_card_id'])->setInc('win_constant');
                    CheeseCardModel::getInstance()->where('id', $data[$i]['right_cheese_card_id'])->setInc('win_constant');
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_WIN','user_id'=>$data[$i]['left_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片被更多p友喜欢了']);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_WIN','user_id'=>$data[$i]['right_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片被更多p友喜欢了']);
                }
                //如果左右票数都为0则不操作
                if($data[$i]['left_votes_num']==0&&$data[$i]['right_votes_num']==0){
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_LOSE','user_id'=>$data[$i]['left_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片没有被更多p友喜欢了']);
                    SystemMessageModel::getInstance()->insert(['type'=>'PICK_LOSE','user_id'=>$data[$i]['right_user_id'],'pick_group_id'=>$data[$i]['id'],'content'=>'你的卡片没有被更多p友喜欢了']);
                    $win['is_over']=1;
                    PickGroupModel::getInstance()->where('id', $data[$i]['id'])->save($win);
                }


            }

        }
        //查询出所有cheese卡赢了5场但是用户积分还没有增加的
        $rows['integral']=1;
        $rows['title']='pick胜利';
        $row=CheeseCardModel::getInstance()->where('win_constant','>',4)->where('is_add',0)->select();
        for ($i=0;$i<count($row);$i++){
            UserModel::getInstance()->where('id',$row[$i]['user_id'])->setInc('cheese_integral');
            //写入积分表
            $rows['user_id']=$row[$i]['user_id'];
            BillModel::getInstance()->insert($rows);
            CheeseCardModel::getInstance()->where('id',$row[$i]['id'])->save(['is_add'=>1]);
        }
        $this->displayBySuccess();


    }

    /**
     * 被pick通知
     */
    public function pickNotice()
    {
        $user_id = $this->autoUserCertification();

        $data['user_notice'] = PickGroupModel::getInstance()->where('right_user_id', $user_id)->where('right_user_is_read', '0')->getAssemblyList($user_id);
        $data['system_notice'] = PickGroupModel::getInstance()->where('right_user_id', $user_id)->where('right_user_is_read', '0')->where('system_notice', 0)->getAssemblyList($user_id);
        for ($i = 0; $i < count($data['user_notice']); $i++) {
            $data['user_notice'][$i]['user_info'] = UserModel::getInstance()->where('id', $data['user_notice'][$i]['left_user_id'])->find();
            $data['user_notice'][$i]['type'] = 0;
        }
        for ($i = 0; $i < count($data['system_notice']); $i++) {
            $data['system_notice'][$i]['user_info'] = UserModel::getInstance()->where('id', $data['system_notice'][$i]['left_user_id'])->find();
            $data['system_notice'][$i]['system_info'] = UserModel::getInstance()->where('id', 4)->find();
            $data['system_notice'][$i]['type'] = 1;
        }
        $row = array_merge_recursive($data['user_notice'], $data['system_notice']);
        $this->displayByData($row);
    }

    public function updateNotice($id)
    {
        $user_id = $this->autoUserCertification();
        $data['right_user_is_read'] = 1;
        if (PickGroupModel::getInstance()->where('id', $id)->save($data)) {
            $this->displayBySuccess();

        }
    }


    /**
     * pick别人
     * @param int $left_cheese_card_id pick
     * @param int $right_cheese_card_id 被pick
     */
    public function pickByPickGroupId($right_cheese_card_id, $left_cheese_card_id)
    {
        $row='NONE';
        $user_id = $this->autoUserCertification();
        $left_cheese_card_data = CheeseCardModel::getInstance()->where(["id" => $left_cheese_card_id])->getAssembly();
        $right_cheese_card_data = CheeseCardModel::getInstance()->where(["id" => $right_cheese_card_id])->getAssembly();
        if (PickGroupModel::getInstance()->where(["left_user_id" => $user_id, "created_at" => ["like", date("Y-m-d", time()) . "%"]])->count() > 10) {
            $this->displayByError('今天的pick次数已经超过10场了哦');
        }
        if(PickGroupModel::getInstance()->where(['left_cheese_card_id'=>$right_cheese_card_id,'right_cheese_card_id'=>$left_cheese_card_id])->find()){
            $this->displayByError('已经互相匹配过了哦');
        }
        if(PickGroupModel::getInstance()->where(['left_cheese_card_id'=>$left_cheese_card_id,'right_cheese_card_id'=>$right_cheese_card_id])->find()){
            $this->displayByError('已经互相匹配过了哦');
        }
        if($right_cheese_card_id==$left_cheese_card_id){
            $this->displayByError('自己不能匹配自己');
        }
        //只要有一个用户是影子用户这局都算影子对局
        if($right_cheese_card_data["user"]["type"]=='SHADOW'||$left_cheese_card_data["user"]["type"]=='SHADOW'){
            $row='SHADOW';
        }
        $id = PickGroupModel::getInstance()->add([
            "left_cheese_card_id" => $left_cheese_card_id,
            "left_user_id" => $left_cheese_card_data["user_id"],
            "left_user_name" => $left_cheese_card_data["user"]["nickName"],
            "right_cheese_card_id" => $right_cheese_card_id,
            "right_user_id" => $right_cheese_card_data["user_id"],
            "right_user_name" => $right_cheese_card_data["user"]["nickName"],
            "is_shadow" => $row,
            "deadline" => date("Y-m-d H:i:s", time() + 60 * 60 * 24),
        ],false,true);
        SystemMessageModel::getInstance()->insert(['type'=>'USER_Match','user_id'=>$right_cheese_card_data["user_id"],'cheese_card_id'=>$right_cheese_card_id,'pick_group_id'=>$id]);
        SystemMessageModel::getInstance()->insert(['type'=>'WE_MATCH','user_id'=>$left_cheese_card_data["user_id"],'cheese_card_id'=>$left_cheese_card_id,'pick_group_id'=>$id]);
        JoinModel::getInstance()->insert(['user_id'=>$left_cheese_card_data["user_id"],'pick_group_id'=>$id,'type'=>'WE_MATCH']);
        if($left_cheese_card_data["user_id"]==$right_cheese_card_data["user_id"]){
        }else {
            JoinModel::getInstance()->insert(['user_id'=>$right_cheese_card_data["user_id"],'pick_group_id'=>$id,'type'=>'WE_MATCH']);
        }
        if (empty($id)) {
            $this->displayByError();
        }
        EventModel::doPick($left_cheese_card_id);
        $data = PickGroupModel::getInstance()->where([
            "id" => $id
        ])->getAssembly();
        $this->displayByData($data);
    }


}
