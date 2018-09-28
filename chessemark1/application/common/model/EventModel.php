<?php
/**
 * 事件触发操作
 */

namespace app\common\model;


class EventModel extends BaseModel
{

    /**
     * 触发对卡片的点赞
     * @param $id
     */
    public static function cheeseCardStar($id,$user_id)
    {
        $data = CheeseCardStarModel::getInstance()->where(["id" => $id])->getAssembly(); //点赞详情
        CheeseCardModel::getInstance()->where(["id" => $data["cheese_card_id"]])->setInc("star",1); //为卡片添加点赞数
        $row['user_id']=$user_id;
        $row['star_type']=1;
        $row['cheese_card_id']=$data['cheese_card_id'];
        $row['cheese_card_user_id']=$data['cheese_card_user_id'];
        UserStarModel::getInstance()->insert($row);
    }

    /**
     * 触发取消对卡片的点赞
     * @param $user_id
     * @param $cheese_card_id
     */
    public static function unCheeseCardStar($user_id, $cheese_card_id)
    {
        CheeseCardModel::getInstance()->where(["id" => $cheese_card_id])->setDec("star"); //为卡片减少点赞数
        UserStarModel::getInstance()->where(['user_id'=>$user_id,'cheese_card_id'=>$cheese_card_id])->delete();
    }

    /**
     * 触发对卡片的评论
     * @param $id
     * @param $user_id
     * @param $cheese_card_id
     * @param $content
     * $data["user_id"]
     */
    public static function cheeseCardComment($is,$user_id, $cheese_card_id,$data)
    {
        //发表评论的用户id
        $row['user_id']=$user_id;
        $row['cheese_card_id']=$cheese_card_id;
        //cheese_card主人
        $row['cheese_card_user_id']=$data;
        $row['cheese_card_comment_id']=$is;
        $row['comment_type']=1;
        UserCommentModel::getInstance()->insert($row);
    }

    /**
     * 触发对卡片评论的点赞
     * @param $id
     */
    public static function cheeseCardCommentStar($id)
    {
        $data = CheeseCardCommentStarModel::getInstance()->where(["id" => $id])->getAssembly(); //点赞详情
        CheeseCardCommentModel::getInstance()->where(["id" => $data["cheese_card_comment_id"]])->setInc("star"); //为评论添加点赞数
        $row['user_id']=$data['user_id'];
        $row['star_type']=2;
        $row['cheese_card_comment_id']=$data['cheese_card_comment_id'];
        $row['cheese_card_comment_user_id']=$data['cheese_card_comment_user_id'];
        UserStarModel::getInstance()->insert($row);
    }


    /**
     * 触发取消对卡片评论的点赞
     * @param $user_id
     * @param $cheese_card_comment_id
     */
    public static function unCheeseCardCommentStar($user_id, $cheese_card_comment_id)
    {
        CheeseCardCommentModel::getInstance()->where(["id" => $cheese_card_comment_id])->setDec("star"); //为卡片减少点赞数
        UserStarModel::getInstance()->where(['user_id'=>$user_id,'cheese_card_comment_id'=>$cheese_card_comment_id])->delete();
    }

    /**
     * 触发对Pick的投票
     * @param $id
     */
    public static function pickGroupVotes($id)
    {
        //根据id查询出用户投的是左边还是右边
        $data=PickGroupVotesModel::getInstance()->where(id,$id)->select();
        if($data[0]['pick_group_votes_position']=='RIGHT'){
             PickGroupModel::getInstance()->where(['id'=>$data[0]['pick_group_id'],'right_user_id'=>$data[0]['pick_group_votes_user_id']])->setInc('right_votes_num');
        }
        if($data[0]['pick_group_votes_position']=='LEFT'){
            PickGroupModel::getInstance()->where(['id'=>$data[0]['pick_group_id'],'left_user_id'=>$data[0]['pick_group_votes_user_id']])->setInc('left_votes_num');
        }


    }

    /**
     * 触发开始Pick
     * @param $id
     */
    public static function doPick($left_cheese_card_id)
    {


    }

    /**
     * 触发对Pick的评论
     * @param $id
     */
    public static function pickGroupComment($id)
    {
        $data=PickGroupCommentModel::getInstance()->where('id',$id)->find();
        //dump($data);
        $row['pick_group_id']=$data['pick_group_id'];
        $row['user_id']=$data['user_id'];
        $row['pick_group_comment_id']=$id;
        $row['pick_group_comment_left_user_id']=$data['pick_group_comment_left_user_id'];
        $row['pick_group_comment_right_user_id']=$data['pick_group_comment_right_user_id'];
        $row['comment_type']=2;
        UserCommentModel::getInstance()->insert($row);
    }

    /**
     * 触发对Pick评论的点赞
     * @param $id
     */
    public static function pickGroupCommentStar($id)
    {
        $data = PickGroupCommentStarModel::getInstance()->where(["id" => $id])->find(); //点赞详情
        PickGroupCommentModel::getInstance()->where("id",$data['pick_group_comment_id'])->setInc("star"); //为评论添加点赞数
        $row['user_id']=$data['user_id'];
        $row['star_type']=3;
        $row['pick_group_comment_id']=$data['pick_group_comment_id'];
        $row['pick_group_comment_user_id']=$data['pick_group_comment_user_id'];
        UserStarModel::getInstance()->insert($row);

    }

    /**
     * 触发取消对Pick评论的点赞
     * @param $user_id
     * @param $pick_group_comment_id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function unPickGroupCommentStar($user_id, $pick_group_comment_id)
    {
        $c = UserStarModel::getInstance()->where(['user_id'=>$user_id,'pick_group_comment_id'=>$pick_group_comment_id])->count();
        if($c > 0){
            PickGroupCommentModel::getInstance()->where("id",$pick_group_comment_id)->setDec("star");
            UserStarModel::getInstance()->where(['user_id'=>$user_id,'pick_group_comment_id'=>$pick_group_comment_id])->delete();
        }
    }

}