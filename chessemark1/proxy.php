<?php

$fileres = file_get_contents("http://cheese-res-1257281477.cos.ap-shanghai.myqcloud.com{$_GET['u']}");
header('Content-type: image/jpeg');
echo $fileres;