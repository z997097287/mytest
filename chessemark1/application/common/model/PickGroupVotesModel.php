<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;


class PickGroupVotesModel extends BaseModel
{

    protected $table_name = "pick_group_votes";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT("投票人的user_id"),
            "pick_group_id" => self::generateINT("pick的id"),
            "pick_group_votes_cheese_card_id" => self::generateINT("被投票者的卡片id"),
            "pick_group_votes_user_id" => self::generateINT("被投票者的user_id"),
            "pick_group_votes_position" => self::generateVARCHAR("被投票者的位置[左(LEFT)或者右(RIGHT)]", 12),
            "is_read"=>self::generateINT("被投票的用户id是否通知")
        ];
    }


}