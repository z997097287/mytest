<?php
if (file_exists("./update.lock")) {
    exit("正在更新");
}
$now = date("Y-m-d H:i:s", time());
file_put_contents("git_log.txt", "{$now}:git pull\n", FILE_APPEND);


file_put_contents("./update.lock", "0");


file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/pull.log", time());

$data = system("cd {$_SERVER['DOCUMENT_ROOT']}/git_tmp && git pull");

if ($data != "Already up-to-date.") {
    echo "update";
    system("cd {$_SERVER['DOCUMENT_ROOT']} &&  \\cp -rf ./git_tmp/* ./");
}
unlink("./update.lock");


