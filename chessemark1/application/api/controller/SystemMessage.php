<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 14:19
 */

namespace app\api\controller;

use app\common\controller\BaseController;
use app\common\model\PickGroupModel;
use app\common\model\SystemMessageModel;
use app\common\model\UserReadModel;
use app\common\model\UserModel;

class SystemMessage extends Token
{


    //更新系统消息为已读
    public function updateRead($id)
    {
        $user_id = $this->autoUserCertification();
        $data['is_read'] = 1;
        $data['user_id'] = $user_id;
        $data['message_id']=$id;
        if (UserReadModel::getInstance()->insert($data)) {
            $this->displayBySuccess();
        }
    }

    public function message($page=1){
        $user_id=$this->autoUserCertification();
        //查询全体消息，把当前用户id写到所有消息里去
        $row=SystemMessageModel::getInstance()->where('user_id','=','null')->find();
        if(!empty($row)){
            SystemMessageModel::getInstance()->insert(['user_id'=>$user_id,'title'=>$row['title'],'content'=>$row['content']]);
        }
        $data=SystemMessageModel::getInstance()->where(['user_id'=>$user_id])->order('updated_at desc')->page($page,10)->select();
        for($i=0;$i<count($data);$i++){
            //如果是全体消息
            if($data[$i]['type']=='ALL_MESSAGE'){
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
            }
            if($data[$i]['type']=='USER_MESSAGE'){
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
                $data[$i]['user_info']=UserModel::getInstance()->where('id',$user_id)->find();
                //如果在已读表里找得到这条系统消息，那就更新为已读
                if(UserReadModel::getInstance()->where(['message_id'=>$data[$i]['id'],'user_id'=>$user_id,'is_read'=>1])->find()){
                    $data[$i]['is_read']=1;
                }
            }
            //如果是被匹配到
            if($data[$i]['type']=='USER_Match'){
                $data[$i]['pick_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssemblyList();
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
            }
            //如果是pick赢了
            if($data[$i]['type']=='PICK_WIN'){
                $data[$i]['pick_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssemblyList();
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
            }
            //如果是pick赢了
            if($data[$i]['type']=='PICK_LOSE'){
                $data[$i]['pick_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssemblyList();
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
            }
            //如果是pick赢了
            if($data[$i]['type']=='WE_MATCH'){
                $data[$i]['pick_info']=PickGroupModel::getInstance()->where('id',$data[$i]['pick_group_id'])->getAssemblyList();
                $data[$i]['system_info']=UserModel::getInstance()->where('id',4)->find();
            }
            //批量更新这10条数据为已读
            SystemMessageModel::getInstance()->where('id',$data[$i]['id'])->save(['is_read'=>1]);
        }
        $this->displayByData($data);
    }
    public function updateMessage($id){
        if(SystemMessageModel::getInstance()->where('id',$id)->save(['is_read'=>1])){
            $this->displayBySuccess();
        }
    }

}