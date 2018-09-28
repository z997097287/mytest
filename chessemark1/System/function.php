<?php

function displayBySuccess($msg = "操作成功！")
{ 
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array(
		'code' => "0",
		'msg' => $msg,
	)));
}
function displayByError($msg = "操作失败！", $code = "-1")
{
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array(
		'code' => $code,
		'msg' => $msg,
	)));
}

function displayBoolByData($result)
{
    if ($result) {
        displayBySuccess();
    }
    displayByError();
}