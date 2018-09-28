<?php
try {

    system("/www/wdlinux/php/bin/php {$_SERVER['DOCUMENT_ROOT']}/System/model/composer/composer.phar -v");
    $cmd = "cd {$_SERVER['DOCUMENT_ROOT']} && /www/wdlinux/phps/71/bin/php {$_SERVER['DOCUMENT_ROOT']}/System/model/composer/composer.phar {$_POST['cmd']}  --ignore-platform-reqs ";
    system($cmd);
} catch (Exception $e) {
    echo $e->getMessage();
}