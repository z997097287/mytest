<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 18:06
 */

namespace app\common\model;
use QcloudImage\CIClient;
class CheeseCardModel extends BaseModel
{

    protected $table_name = "cheese_card";


    protected function generateTableField()
    {
        return [
            "user_id" => self::generateINT("用户ID", 11, 0, self::INDEX_TYPE_INDEX),
            'cheese_card_id' => self::generateINT("cheese卡id", 11, 0, self::INDEX_TYPE_INDEX),
            "user_name" => self::generateVARCHAR("用户名", 128, "", self::INDEX_TYPE_INDEX),
            "text" => "说明文字",
            "ext_date_time" => "带样式日期",
            "pic_url" => "图片地址",
            "full_pic_url" => "整张卡片的图片地址",
            "tag" => self::generateTEXT("标签"),
            "star" => self::generateINT("点赞数"),
            "user_type" => self::generateVARCHAR("账号类型,NONE为普通用户,SHADOW为影子用户", "32", "NONE"),
            "is_pickpool" => self::generateINT('是否在pick池,0代表不在pick池，1代表在pick池', 1, '0'),
            "is_delete" => self::generateINT("是否被删除"),
            "review_status" => self::generateVARCHAR(" 审核状态", "15", "NONE"),
            'win_constant' => self::generateINT("赢得场数", 11, 0),
            "ext_data" => self::generateTEXT("其他数据"),
            "is_add" => self::generateINT('积分是否已经增加'),
            "is_yellow"=>self::generateINT("是否是一个小黄图"),
        ];
    }

    public function getAssembly()
    {

        $data = $this->find();
        if (empty($data)) {
            return null;
        }
        $data["user"] = UserModel::getInstance()->where(["id" => $data["user_id"]])->getAssembly();
        $data["tag"] = json_decode($data["tag"]);
        $data["top_comment"] = CheeseCardCommentModel::getInstance()->where(["cheese_card_id" => $data["id"]])->order("star desc")->limit(2)->select();
        $data["comment_total"] = CheeseCardCommentModel::getInstance()->where(["cheese_card_id" => $data["id"]])->count();
        return $data;
    }


    /**
     * 组合数据
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAssemblyList()
    {
        $data = $this->select();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user"] = UserModel::getInstance()->where(["id" => $data[$i]["user_id"]])->find();
            $data[$i]["tag"] = json_decode($data[$i]["tag"]);
            $data[$i]["top_comment"] = CheeseCardCommentModel::getInstance()->where(["cheese_card_id" => $data[$i]["id"]])->order("star desc")->limit(2)->select();
            $data[$i]["comment_total"] = CheeseCardCommentModel::getInstance()->where(["cheese_card_id" => $data[$i]["id"]])->count();
        }
        return $data;
    }

    /**
     * @return string
     * 生成签名，有效期30天
     */
    public static function getAutograph()
    {
        $appid = "1257281477";
        $secret_id = SystemModel::config("txy_cos_secret_id");
        $secret_key = SystemModel::config("txy_cos_secret_key");
        $expired = time() + 2592000;
        $current = time();
        $rdm = rand();
        $srcStr = 'a=' . $appid . '&k=' . $secret_id . '&e=' . $expired . '&t=' . $current . '&r=' . $rdm . '&f=';
        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true) . $srcStr);
        $data['value'] = $signStr;
        SystemModel::getInstance()->where('id', 10)->save($data);
        return $signStr;
    }

    public static function isYellowPicture($full_picture)
    {

        //$url = "http://recognition.image.myqcloud.com/detection/porn_detect";
        $data = SystemModel::getInstance()->where('key', 'autograph')->find();
        $client = new CIClient(SystemModel::config('yellow_app_id'), SystemModel::config("txy_cos_secret_id"), SystemModel::config("txy_cos_secret_key"),'');
        $client->setTimeout(30);
        if (strtotime(date("Y-m-d H:i:s", time())) - strtotime($data['updated_at']) > 2592000) {
            //$autograph=self::getAutograph();
            $row=$client->pornDetect(array('urls'=>array($full_picture)));
            return $row;
        } else {
            $row=$client->pornDetect(array('urls'=>array($full_picture)));
            return $row;
        }
    }
}