<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


class PickGroupModel extends BaseModel
{

    protected $table_name = "pick_group";


    protected function generateTableField()
    {
        return [
            "left_cheese_card_id" => self::generateINT("左边的卡片id,发起pick"),
            "left_user_id" => self::generateINT("左边的用户ID"),
            "left_user_name" => "左边用户名",
            "left_votes_num" => self::generateINT("左边的卡片票数"),
            "left_user_is_read" => self::generateINT("左边的对消息是否已读"),
            "right_cheese_card_id" => self::generateINT("右边的卡片id"),
            "right_user_id" => self::generateINT("右边的用户ID"),
            "right_user_name" => "右边用户名",
            "right_votes_num" => self::generateINT("右边的卡片票数"),
            "right_user_is_read" => self::generateINT("右边的对消息是否已读"),
            "deadline" => self::generateDATETIME("截止时间"),
            "recommend_weights" => self::generateINT("平台推荐权重"),
            "who_win" => self::generateINT("谁赢1代表左边，2代表右边,3代表平局"),
            "system_notice"=>self::generateINT('系统是否通知,1代表已通知'),
            "is_over"=>self::generateINT("这场pick是否已经结束"),
            "pick_delete"=>self::generateINT("这场pick的卡片被删除了"),
            "is_shadow"=>self::generateVARCHAR("是否包含影子用户"),
        ];
    }


    public function getAssembly()
    {

        $data = $this->find();
        if (empty($data)) {
            return null;
        }
        $data["left_cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data["left_cheese_card_id"]])->getAssembly();
        $data["right_cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data["right_cheese_card_id"]])->getAssembly();

        //$data["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data["id"]])->order("star desc")->limit(2)->getAssemblyList();
        $data["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data["id"]])->count();
        return $data;
    }


    public function getAssemblyList()
    {
        $data = $this->select();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["left_cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data[$i]["left_cheese_card_id"]])->getAssembly();
            $data[$i]["right_cheese_card"] = CheeseCardModel::getInstance()->where(["id" => $data[$i]["right_cheese_card_id"]])->getAssembly();
        }
        return $data;
    }
}