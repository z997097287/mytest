<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 16:08
 */

namespace app\api\controller;


use app\common\model\BannerModel;
use app\common\model\CheeseCardCommentModel;
use app\common\model\CheeseCardModel;
use app\common\model\CheeseCardTagModel;
use app\common\model\PickGroupCommentModel;
use app\common\model\PickGroupModel;
use app\common\model\UserModel;

class CheeseCardTag extends Token
{

    /**
     * [杂项]标签列表
     */
    public function index()
    {

        $data = CheeseCardTagModel::getInstance()->fromCache()->getAssemblyList();
        $this->displayByData($data);
    }

}
