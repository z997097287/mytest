<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/17
 * Time: 18:15
 */

namespace app\api\controller;

//卡片点赞
use app\common\model\CheeseCardModel;
use app\common\model\CheeseCardStarModel;
use app\common\model\EventModel;
use app\common\model\UserModel;
use app\common\model\PickGroupCommentStarModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupModel;
use app\common\model\CheeseCardCommentStarModel;
use app\common\model\CheeseCardCommentModel;
use app\common\model\UserStarModel;

class CheeseCardStar extends Token
{


    /**
     * [用户主页]卡片点赞列表
     * @param $page
     */
    public function index($page)
    {

        $user_id = $this->autoUserCertification();
        $data = CheeseCardStarModel::getInstance()->page($page)->where([
            'cheese_card_user_id' => $user_id,
        ])->getAssemblyList();
        $this->displayByData($data);
    }


    /**
     * 【操作】用户卡片点赞
     * @method /Api/CheeseCardStar/like.json
     * @param $cheese_card_id
     */
    public function like($cheese_card_id)
    {
        //进行点赞操作,如有数据则退出
        $user_id = $this->autoUserCertification();
        $cheese_card = CheeseCardModel::getInstance()->where(["id" => $cheese_card_id])->find();
        $data = [
            'user_id' => $user_id,
            'cheese_card_id' => $cheese_card_id,
            'cheese_card_user_id' => $cheese_card["user_id"],
        ];
        //查询是否点赞
        $count = CheeseCardStarModel::getInstance()->where($data)->count();
        if ($count > 0) {
            $this->displayByError('你已经点赞过');
        }
        //进行点赞操作
        $res = CheeseCardStarModel::getInstance()->add($data);
        if ($res) {
            EventModel::cheeseCardStar(CheeseCardStarModel::getInstance()->getLastInsID(),$user_id);
            $this->displayBySuccess();
        }
        $this->displayByError();
    }

    /**
     * 通知用户卡片被人点赞
     * 字段is_notice 0代表未读，1代表已读
     */
    public function userNotice($page = 1)
    {
        $user_id = $this->autoUserCertification();
        $data['cheese_card_comment_star'] = CheeseCardCommentStarModel::getInstance()->where(['cheese_card_comment_user_id' => $user_id, 'is_read' => 0])->page($page, 10)->getAssemblyList();
        for ($i = 0; $i < count($data['cheese_card_comment_star']); $i++) {
            $data['cheese_card_comment_star'][$i]['info'] = CheeseCardCommentModel::getInstance()->where(['id' => $data['cheese_card_comment_star'][$i]['cheese_card_comment_id']])->find();
            $data['cheese_card_comment_star'][$i]['cheese_card_info'] = CheeseCardModel::getInstance()->where('id', $data['cheese_card_comment_star'][$i]['info']['cheese_card_id'])->find();
            $data['cheese_card_comment_star'][$i]['user_info'] = UserModel::getInstance()->where(['id' => $data['cheese_card_comment_star'][$i]['user_id']])->getAssemblyList();
            $data['cheese_card_comment_star'][$i]['is_card_comment_star'] = 1;
        }
        $data['cheese_card_star'] = CheeseCardStarModel::getInstance()->where('cheese_card_user_id', $user_id)->where('is_notice', 0)->page($page, 10)->select();
        for ($i = 0; $i < count($data['cheese_card_star']); $i++) {
            $data['cheese_card_star'][$i]['user_info'] = UserModel::getInstance()->where('id', $data['cheese_card_star'][$i]['cheese_card_user_id'])->find();
            $data['cheese_card_star'][$i]['cheese_card_info'] = CheeseCardModel::getInstance()->where('id', $data['cheese_card_star'][$i]['cheese_card_id'])->find();
            $data['cheese_card_star'][$i]['is_cheese_card_star'] = 1;
        }
        $data['pick_group_comment_star'] = PickGroupCommentStarModel::getInstance()->where(['pick_group_comment_user_id' => $user_id, 'is_read' => 0])->page($page, 10)->getAssemblyList();
        for ($i = 0; $i < count($data['pick_group_comment_star']); $i++) {
            $data['pick_group_comment_star'][$i]['info'] = PickGroupCommentStarModel::getInstance()->where(['id' => $data['pick_group_comment_star'][$i]['pick_group_comment_id']])->find();
            $data['pick_group_comment_star'][$i]['pick_group_info'] = PickGroupModel::getInstance()->where('id', $data['pick_group_comment_star'][$i]['info']['pick_group_id'])->find();
            $data['pick_group_comment_star'][$i]['user_info'] = UserModel::getInstance()->where(['id' => $data['pick_group_comment_star'][$i]['user_id']])->getAssemblyList();
            $data['pick_group_comment_star'][$i]['is_pick_comment_star'] = 1;
        }
        //将三个数组合并为一个数组
        $row = array_merge_recursive($data['cheese_card_comment_star'], $data['cheese_card_star'], $data['pick_group_comment_star']);
        $this->displayByData($row);
    }
    public function Star($page=1){
        $user_id=$this->autoUserCertification();
        $type="cheese_card_user_id|cheese_card_comment_user_id|pick_group_comment_user_id";
        $data=UserStarModel::getInstance()->where([$type=>$user_id,'is_delete'=>0])->order('updated_at desc')->page($page,10)->getAssemblyList();
        for ($i=0;$i<count($data);$i++){
            //如果是cheese卡点赞
            if($data[$i]['star_type']==1){
                //echo '我执行了';
                //查询出点赞的用户信息
                $data[$i]['user_info']=UserModel::getInstance()->where('id',$data[$i]['user_id'])->find();
                //查询出cheese_card信息
                $data[$i]['cheese_card_info']=CheeseCardModel::getInstance()->where('id',$data[$i]['cheese_card_id'])->find();
                //查询出被点赞的用户信息
                $data[$i]['cheese_card_user_info']=UserModel::getInstance()->where('id',$data[$i]['cheese_card_user_id'])->find();
            }
            //如果是cheesecard评论被点赞
            if($data[$i]['star_type']==2){
                //查询出点赞的用户信息
                $data[$i]['user_info']=UserModel::getInstance()->where('id',$data[$i]['user_id'])->find();
                //查询出被点赞的用户信息
                $data[$i]['cheese_card_comment_user_info']=UserModel::getInstance()->where('id',$data[$i]['cheese_card_comment_user_id'])->find();
                //查询出被点赞的评论信息
                $data[$i]['cheese_card_comment_info']=CheeseCardCommentModel::getInstance()->where(['id'=>$data[$i]['cheese_card_comment_id']])->find();
                //查询出被点赞的卡片信息
                $data[$i]['cheese_card_info']=CheeseCardModel::getInstance()->where('id',$data[$i]['cheese_card_comment_info']['cheese_card_id'])->find();
            }
            //如果是pick组的评论被点赞
            if($data[$i]['star_type']==3){
                $data[$i]['user_info']=UserModel::getInstance()->where('id',$data[$i]['user_id'])->find();
                //查询出pick组评论信息
                $data[$i]['pick_group_comment_info']=PickGroupCommentModel::getInstance()->where('id',$data[$i]['pick_group_comment_id'])->find();
                $data[$i]['pick_group_comment_user_info']=UserModel::getInstance()->where('id',$data[$i]['pick_group_comment_user_id'])->find();
                //查询出pick组信息
                $data[$i]['pick_group_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_comment_info']['pick_group_id'])->getAssemblyList();
            }
            //批量更新为已读
            UserStarModel::getInstance()->where('id',$data[$i]['id'])->save(['is_read'=>1]);
        }
        $this->displayByData($data);
    }

    public function readNotice($user_id, $cheese_card_id)
    {
        //$user_id=$this->autoUserCertification();
        $data['is_notice'] = 1;
        if (CheeseCardStarModel::getInstance()->where('cheese_card_id', $cheese_card_id)->where('cheese_card_user_id', $user_id)->save($data)) {
            $this->displayBySuccess();
        } else {
            $this->displayByError('请传入合法卡片id');
        }

    }
    /**
     * 将点赞转为已读
     */
    public function getStar($id){
        $user_id=$this->autoUserCertification();
        $data['is_read']=1;
        UserStarModel::getInstance()->where('id',$id)->save($data);
        $this->displayBySuccess();

    }


    /**
     * 【操作】用户取消卡片点赞
     * @method /Api/CheeseCardStar/cancelLike.json
     * @param $cheese_card_id
     */
    public function cancelLike($cheese_card_id)
    {
        //进行点赞删除操作
        $user_id = $this->autoUserCertification();
        $where = [
            'user_id' => $user_id,
            'cheese_card_id' => $cheese_card_id
        ];

        $res = CheeseCardStarModel::getInstance()->where($where)->delete();
        if ($res) {
            EventModel::unCheeseCardStar($user_id, $cheese_card_id);
            $this->displayBySuccess();
        }
        $this->displayByError();
    }
}