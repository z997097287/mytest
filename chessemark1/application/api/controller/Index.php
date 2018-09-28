<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\CheeseCardModel;
use app\common\model\PickGroupCommentStarModel;
use app\common\model\PickGroupModel;
use app\common\model\PickGroupVotesModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\SystemModel;
use app\common\model\UserStarModel;
use app\common\model\UserModel;
use think\Log;
use think\Session;

class Index extends Token
{


    public function test()
    {

        exit(json_encode($_SESSION));
    }

    /**
     * 首页展示,推荐-最新页,显示总投票数最高的和平台内推荐的
     * @param int $page
     * @internal param $order
     */
    public function index($page = 1, $user_id = '')
    {
        //$user_id=$this->autoUserCertification();
        /*
         * 优先后台推荐
         * right_votes_num+left_votes_num为总票数,recommend_weights当后台推荐时为1,以后可扩展大于1
         */
        $data = PickGroupModel::getInstance()
            ->page($page, 10)->where(['is_over' => 0, 'pick_delete' => 0])
            ->order(["`recommend_weights` desc", "`right_votes_num` + `left_votes_num` desc"])
            ->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->select();
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
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();

        }
        $this->displayByData($data);
    }
    public function getRefashIndex($page = 1, $update_time,$user_id = '')
    {
        $data = PickGroupModel::getInstance()->page($page, 10)
            ->where(['is_over' => 0, 'pick_delete' => 0])->where('updated_at', '>', $update_time)
            ->order(["`recommend_weights` desc", "`right_votes_num` + `left_votes_num` desc"])
            ->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->select();
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
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();

        }
        $this->displayByData($data);

    }

    /**
     * 首页展示,最新页,显示最新的
     * @param int $page
     * @internal param $order
     */
    public function indexByTime($page = 1, $user_id = '')
    {
        //$user_id = $this->autoUserCertification();
        $data = PickGroupModel::getInstance()->where(['is_over' => 0, 'pick_delete' => 0])
            ->page($page, 10)
            ->order(["`id` desc"])
            ->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->getAssemblyList();
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
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();
        }
        $this->displayByData($data);
    }


    /**
     * 首页展示,最新页,显示最新的
     * @param int $page
     * @internal param $order
     */
    public function indexByUpdateTime($page = 1, $user_id = '', $update_time)
    {
        //$user_id = $this->autoUserCertification();
        $data = PickGroupModel::getInstance()->where(['is_over' => 0, 'pick_delete' => 0])->where('updated_at', '>', $update_time)
            ->page($page, 10)
            ->order(["`id` desc"])
            ->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['info'] = PickGroupVotesModel::getInstance()->where([
                "user_id" => $user_id,
                "pick_group_id" => $data[$i]['id'],
            ])->find();
            $data[$i]["top_comment"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->order("star desc")->limit(2)->getAssemblyList();
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
            $data[$i]["comment_total"] = PickGroupCommentModel::getInstance()->where(["pick_group_id" => $data[$i]["id"]])->count();
        }
        $this->displayByData($data);
    }


    /**
     * 展示cheese卡按照时间顺序排序
     */
    public function showCheese($page = 1)
    {
        //$user_id = $this->autoUserCertification();
        $data = CheeseCardModel::getInstance()->page($page, 10)
            ->where('win_constant', '>', 4)->where('is_delete', 0)
            ->order('updated_at desc')->fromCache()->getAssemblyList();
        $this->displayByData($data);
    }

    public function getRefash($page = 1, $update_time)
    {
        $data = CheeseCardModel::getInstance()->page($page, 10)
            ->where('win_constant', '>', 4)->where(['is_delete' => 0])->where('updated_at', '>', $update_time)
            ->order('updated_at desc')->getAssemblyList();
        $this->displayByData($data);

    }

    /**
     * 获取对应key的value值
     */
    public function getValue($key){
        $data = SystemModel::getInstance()->where('key',$key)->find();
        $this->displayByData($data['value']);
    }
}
