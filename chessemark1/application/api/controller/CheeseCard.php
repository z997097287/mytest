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
use app\common\model\FollowModel;
use app\common\model\PickGroupModel;
use app\common\model\UserModel;
use app\common\model\UserStarModel;
use think\Log;

class CheeseCard extends Token
{

    /**
     * CheeseCard列表
     * @param int $page
     */
    public function index($page = 1, $user_id = '')
    {
        //$user_id=$this->autoUserCertification();
        $data['cheese_info'] = CheeseCardModel::getInstance()->where(["is_delete" => 0])->page($page, 12)->order("updated_at desc")->getAssemblyList();
        for ($i = 0; $i < count($data['cheese_info']); $i++) {
            if ($user_id == '') {
            } else {
                $data['cheese_info'][$i]['is_star'] = UserStarModel::getInstance()->where(['user_id' => $user_id, 'cheese_card_id' => $data['cheese_info'][$i]['id']])->count();
            }
        }
        $data['baner_info']= BannerModel::getInstance()->page($page, 1)->order("id desc")->getAssembly();
        $this->displayByData($data);
    }
    public function ReflushIndex($update_time){

    }

    public function showCard($page)
    {
        $user_id = $this->autoUserCertification();
        $data = [];
        $data["cheese_card"] = CheeseCardModel::getInstance()->where(["user_id" => $user_id, "is_delete" => 0])->where('win_constant', '>', 4)->page($page, 10)->order("id desc")->getAssemblyList();
        $data["count"] = CheeseCardModel::getInstance()->where('win_constant', '>', 4)->count();
        $this->displayByData($data);

    }


    /**
     * ,获取本人的所有卡片
     * @param $page
     */
    public function listByUserId($page)
    {
        $user_id = $this->autoUserCertification();
        $data = [];
        $data["cheese_card"] = CheeseCardModel::getInstance()->where(["user_id" => $user_id, "is_delete" => 0])->page($page, 10)->order("id desc")->getAssemblyList();
        $data["count"] = CheeseCardModel::getInstance()->count();
        $this->displayByData($data);
    }

    /**
     * ,获取他人的所有卡片
     * @param $page
     * @param $other_user_id 别的用户
     */
    public function listByotherCard($page, $other_user_id)
    {
        $user_id = $this->autoUserCertification();
        $data = CheeseCardModel::getInstance()->where(["user_id" => $other_user_id, "is_delete" => 0])->page($page, 10)->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['is_star'] = UserStarModel::getInstance()->where(['user_id' => $user_id, 'cheese_card_id' => $data[$i]['id']])->count();
        }
        $this->displayByData($data);
    }

    /**
     * CheeseCard列表,获取别人的
     * @param $page
     */
    public function listByotherUserId($page, $other_user_id = 2)
    {
        $user_id = $this->autoUserCertification();
        $data = CheeseCardModel::getInstance()->where(["user_id" => $other_user_id, "is_delete" => 0])->where('win_constant', '>', 4)->page($page, 10)->order("id desc")->getAssemblyList();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['is_star'] = UserStarModel::getInstance()->where(['user_id' => $user_id, 'cheese_card_id' => $data[$i]['id']])->count();
        }
        $this->displayByData($data);
    }

    /**
     * 软删除cheess卡
     */
    public function deleteCheese($cheese_id)
    {
        $user_id = $this->autoUserCertification();
        $data['is_delete'] = 1;
        if (CheeseCardModel::getInstance()->where('id', $cheese_id)->save($data)) {
            //更新所有包含这张cheese卡的pick组为over
            PickGroupModel::getInstance()->where('left_cheese_card_id|right_cheese_card_id',$cheese_id)->save(['pick_delete'=>1]);
            UserStarModel::getInstance()->where('cheese_card_id',$cheese_id)->save(['is_delete'=>1]);
            $this->displayBySuccess();
        } else {
            $this->displayByError('请传入合法的cheese卡id');
        }

    }

    /**
     * 单个卡片
     * @param $id
     */
    public function info($id)
    {
        $user_id = $this->autoUserCertification();
        Log::writeRemote();
        $data = CheeseCardModel::getInstance()->where(["id" => $id, "is_delete" => 0])->getAssembly();
        if(!empty($data)){
            $data['is_star'] = UserStarModel::getInstance()->where(['user_id' => $user_id, 'cheese_card_id' => $data['id']])->count();
            $data['is_follow'] = FollowModel::getInstance()->where(['user_id' => $user_id, 'follow_user_id' => $data['user_id']])->count();
            $this->displayByData($data);
        }
        $this->displayByError();
    }

    public function test($full_pic_url)
    {
        $data = CheeseCardModel::isYellowPicture($full_pic_url);
        $row = json_decode($data, true);
        dump($row['result_list'][0]['data']['result']);
    }

    /**
     * 发布CheeseCard卡
     * @param $text
     * @param $pic_url
     * @param $full_pic_url
     * @param $ext_date_time
     * @param $tag
     */
    public function add($text, $pic_url, $full_pic_url, $ext_date_time, $tag='')
    {
        $user_id = $this->autoUserCertification();
        $user_data = UserModel::getInstance()->where(["id" => $user_id])->getAssembly();
        Log::writeRemote(UserModel::getInstance()->getLastSql());
        if (empty($user_data)) {
            $this->displayByError("无法找到该用户");
        }
        $yellow_data = CheeseCardModel::isYellowPicture("https://cheese-res-1257281477.cos.ap-shanghai.myqcloud.com".$full_pic_url);
        //dump($yellow_data);
        $row = json_decode($yellow_data, true);
        if ($row['result_list'][0]['data']['result'] == 1) {
            $this->displayByError('该图片违背社会主义价值观.');
        }
        $id = CheeseCardModel::getInstance()->add([
            "user_id" => $user_id,
            "user_name" => $user_data["nickName"],
            "user_type" => $user_data['type'],
            "text" => $text,
            "ext_date_time" => $ext_date_time,
            "pic_url" => $pic_url,
            "full_pic_url" => $full_pic_url,
            "tag" => json_encode($tag, JSON_UNESCAPED_UNICODE),
        ], false, true);
        if ($row['result_list'][0]['data']['result'] == 2) {
            CheeseCardModel::getInstance()->where('id', $id)->save(['is_yellow'=> 2]);
        }
        if ($id === false) {
            $this->displayByError();
        }
        $data['id'] = $id;
        $this->displayByData($data);
    }

}
