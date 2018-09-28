<?php

namespace app\system\controller;

use app\common\controller\BaseController;
use app\common\model\System;
use Exception;

class Index extends BaseController
{

    public function initSql()
    {
        $root = dir("{$_SERVER['DOCUMENT_ROOT']}/application");
        $data = [];
        while ($file = $root->read()) {
            if (is_dir("{$_SERVER['DOCUMENT_ROOT']}/application/{$file}/model")) {
                $model_dir = dir("{$_SERVER['DOCUMENT_ROOT']}/application/{$file}/model");
                while ($model_file = $model_dir->read()) {
                    if ($model_file != "." && $model_file != ".." && $model_file != "BaseModel.php") {
                        $model_file = explode(".", $model_file);
                        $model_file = $model_file[0];
                        $model_name = "\\app\\{$file}\\model\\{$model_file}";
                        try {
                            new $model_name();
                            $data[] = "初始化{$model_name}完成...";
                        } catch (Exception $e) {
                            $data[] = $e->getMessage();
                        }
                    }
                }
            }
        }
        $this->displayByData($data);
    }
}
