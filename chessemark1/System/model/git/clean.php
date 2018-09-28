<?php
$now = date("Y-m-d H:i:s", time()); 
file_put_contents("git_log.txt", "{$now}:clean\n", FILE_APPEND);


if (file_exists("./update.lock")) {
    exit("正在更新");
}
system("rm -rf {$_SERVER['DOCUMENT_ROOT']}/git_tmp ");