<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 17:56
 */

namespace app\common\model;


use think\Log;

class UserModel extends BaseModel
{
    //用户表
    protected $table_name = "user";

    protected function generateTableField()
    {
        return [
            "openid" => self::generateVARCHAR("用户唯一标识", 128, "", self::INDEX_TYPE_UNIQUE),
            "token" => self::generateVARCHAR("token令牌", 128, "", self::INDEX_TYPE_INDEX),
            "username" => self::generateVARCHAR("用户名", 128, "", self::INDEX_TYPE_UNIQUE),
            "password" => self::generateVARCHAR("密码", 128, ""),
            "nickName" => "用户姓名",
            "diyAvatarUrl" => "自定义用户头像,即用户头像",
            "avatarUrl" => "用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表132*132正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效",
            "gender" => self::generateINT('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知'),
            "phone" => "手机号码",
            "age" => self::generateINT('用户年龄'),
            "city" => "用户所在城市",
            "province" => "用户所在省份",
            "country" => "用户所在国家",
            "language" => "用户的语言，简体中文为zh_CN",
            "constellation" => "星座",
            "cheese_num" => self::generateINT('cheese卡数'),
            "cheese_integral"=>self::generateINT("用户积分"),
            "signature" => self::generateTEXT('个性签名'),
            "type" => self::generateVARCHAR("账号类型,NONE为普通用户,SHADOW为影子用户", "32", "NONE"),
            "group" => self::generateVARCHAR("分组", "32", "NONE"),
            "is_black" => self::generateINT('是否拉黑'),
            "expire_in" => self::generateDATETIME('生成token时间')
        ];
    }


    //插入数据
    public function insertData($data)
    {
        $time = date('Y-m-d H:i:s', $data['expire_in']);
        $data['expire_in'] = $time;
        $res = $this->insert($data);
        if ($res) {
            return $this->order('id desc')->find();
        }
    }

    public function getDataByOpenid($openid)
    {
        return $this->where([
            'openid' => $openid
        ])->find();
    }

    public function getDataByToken($token)
    {
        return $this->where([
            'token' => $token
        ])->find();
    }

    //更新数据
    public function updateData($data)
    {
        if (array_key_exists('token', $data)) {
            $time = date('Y-m-d H:i:s', $data['expire_in']);
            $data['expire_in'] = $time;
        }
        $where = [
            'openid' => $data['openid'],
        ];
        $res = $this->where($where)->save($data);
        if ($res) {
            return $this->where($where)->find();
        }
    }

    public function findUserById($id)
    {
        return $this->where(['id' => $id])->find();
    }

    public function findUserByToken($token)
    {
        return $this->where(['token' => $token])->find();
    }

    public function updateUserSmile($user_id, $smile)
    {
        $where = ['id' => $user_id];
        return $this->where($where)->setInc('humor_num', $smile);
    }

    /**
     * 获取数据
     * @param $uid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function retrieveData($uid)
    {
        $model = new UserModel();
        return $model->where(['id' => $uid])->find();
    }

    /**
     * 更新数据
     * @param $uid
     * @param $data
     * @return int|string
     */
    public static function updateTheData($uid, $data)
    {
        $model = new UserModel();
        return $model->where(['id' => $uid])->update($data);
    }

    /**
     * 增加cheese_num数量
     * @param $user_id
     * @throws \think\Exception
     */
    public static function addCheeseCount($user_id)
    {
        $model = new UserModel();
        $model->where(['id' => $user_id])->setInc('cheese_num', 1);
    }
}