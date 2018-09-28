<?php
 $url = $_REQUEST['url']; 
 if (file_exists("./update.lock")) {
     exit("正在更新");
 }
 
 $now = date("Y-m-d H:i:s", time());
 $m_url = explode("@", $url);
 $user = explode(":", str_replace("https://", "", $m_url[0]));
 $user = $user[0];
 $m_url = $m_url[1];


 file_put_contents("git_log.txt", "{$now}:git clone http://{$user}:xxx@{$m_url}\n", FILE_APPEND);

 file_put_contents("./update.lock", "0");
 system("cd {$_SERVER['DOCUMENT_ROOT']} && rm -rf ./git_tmp && git clone $url ./git_tmp && \\cp -rf ./git_tmp/* ./");
 system("cd {$_SERVER['DOCUMENT_ROOT']} && rm -rf ./.git");
 unlink("./update.lock");


