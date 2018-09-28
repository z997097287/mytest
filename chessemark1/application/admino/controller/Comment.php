<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 12:01
 */
namespace app\admino\controller;
use \app\common\controller\BaseController;
use app\common\model\CheeseCardCommentModel;
use \app\common\model\PickGroupCommentModel;
use \app\common\model\PickGroupCommentStarModel;
use \app\common\model\EventModel;
use app\common\model\UserModel;
use \app\common\model\PickGroupModel;
use app\common\model\CheeseCardCommentStarModel;
use app\common\model\UserStarModel;

class Comment extends BaseController{
   public function getShadow($page=1){
       $data=UserModel::getInstance()->where('type','SHADOW')->page($page,10)->getAssemblyList();
       $this->displayByData($data);
   }
   public function PickIndex($page=1){
       $data['count']=PickGroupCommentModel::getInstance()->count();
       $data['list']=PickGroupCommentModel::getInstance()->page($page,10)->getAssemblyList();
       $this->displayByData($data);
   }
   public function ShadowStar($shadow,$pick_group_comment_id,$pick_group_comment_user_id){
       $data = [
           'user_id' => $shadow,
           'pick_group_comment_id' => $pick_group_comment_id,
           'pick_group_comment_user_id' => $pick_group_comment_user_id
       ];
       //查询是否点赞
       $count = PickGroupCommentStarModel::getInstance()->where($data)->count();
       if ($count > 0) {
           $this->displayByError('这个影子账户已经点赞过');
       }
       //进行点赞操作
       $res = PickGroupCommentStarModel::getInstance()->add($data, false, true);
       if ($res) {
           EventModel::pickGroupCommentStar($res);
           $this->displayBySuccess();
       }
   }
   public function ShieldByPick($id){
       if(PickGroupCommentModel::getInstance()->where('id',$id)->delete()){
           UserStarModel::getInstance()->where('pick_group_comment_id',$id)->save(['is_delete'=>1]);
           $this->displayBySuccess('屏蔽成功');
       }
   }
   //卡片列表
    public function CheeseCardIndex($page){
       $data['count']=CheeseCardCommentModel::getInstance()->count();
       $data['list']=CheeseCardCommentModel::getInstance()->page($page,10)->select();
       $this->displayByData($data);
    }
    //点赞cheese卡评论
    public function CheeseCardStar($shadow,$cheese_card_comment_id,$cheese_card_comment_user_id){
        $data = CheeseCardCommentModel::getInstance()->where(["id" => $cheese_card_comment_id])->find();
        $data = [
            'user_id' => $shadow,
            'cheese_card_comment_id' => $cheese_card_comment_id,
            'cheese_card_comment_user_id' => $cheese_card_comment_user_id
        ];
        //查询是否点赞
        $count = CheeseCardCommentStarModel::getInstance()->where($data)->count();
        if ($count > 0) {
            $this->displayByError('这个影子用户已经点赞过');
        }
        //进行点赞操作
        $res = CheeseCardCommentStarModel::getInstance()->add($data);
        if($res){
            EventModel::cheeseCardCommentStar(CheeseCardCommentStarModel::getInstance()->getLastInsID());
            $this->displayBySuccess();
        }
    }
    public function search($keyword=''){
       $data=PickGroupModel::getInstance()->where(['left_user_name|right_user_name'=>['like',"%".$keyword."%"]])->getAssemblyList();
       $this->displayByData($data);
    }
}