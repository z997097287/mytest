<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


<body style="padding: 50px;">

<?php
error_reporting(0);
if ($_POST) {
    echo '<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">初始化中</h3>
        </div>
        <div class="panel-body">';
    echo '<pre id="console" style=" height: 400px;">';
    $arr_str_b = "";
    foreach ($_POST as $key => $val) {
        if (empty($arr_str_b)) {
            $arr_str_b .= '"' . $key . '" => "' . $val . '"';
        } else {
            $arr_str_b .= ', "' . $key . '" => "' . $val . '"';
        }
    }
    $arr_str = "<?php return array($arr_str_b);";
    file_put_contents("../sql_config.php", $arr_str);
//    if (empty($_POST['db_pwd'])) {
//        $con = mysql_connect($_POST['db_host'] . ":" . $_POST['db_port'], $_POST['db_user']);
//    } else {
//        $con = mysql_connect($_POST['db_host'] . ":" . $_POST['db_port'], $_POST['db_user'], $_POST['db_pwd']);
//    }
    $con = mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pwd'], '', $_POST['db_port']);
    if (!$con) {
        die('数据库无法链接[' . mysql_error() . ']');
    }
    if (empty($_POST['db_name'])) {
        die('数据库名为空');
    }
    if (!mysqli_query($con, "CREATE DATABASE " . $_POST['db_name'] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci")) {
    }
    $con = mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pwd'], $_POST['db_name'], $_POST['db_port']);
    if (!$con) {
        die('没有权限');
    }
    echo '</pre>';
    echo("<a href='/admino/index' class='btn btn-default'>前往管理后台</a>");
    echo("<a href='javascript:init()' class='btn btn-default'>重试</a>");
    echo "
    </div>
    <script>
    function init() {
      
        $.get('/system/index/initSql.json',function(data) {
            console.log(data.data.join('\\n'));
            $('#console').append(data.data.join('\\n'));
        });
    }
    init();
    </script>
</div>";
    exit();
}

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">初始化数据库</h3>
    </div>
    <div class="panel-body">

        <form action="./index.php" method="post">
            <div class="form-group">
                <label>服务器地址</label>
                <input type="text" class="form-control" name="db_host" value="localhost">
            </div>
            <div class="form-group">
                <label>端口</label>
                <input type="text" class="form-control" name="db_port" value="3306">
            </div>
            <div class="form-group">
                <label>用户名</label>
                <input type="text" class="form-control" name="db_user">
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="text" class="form-control" name="db_pwd">
            </div>
            <div class="form-group">
                <label>数据库名</label>
                <input type="text" class="form-control" name="db_name">
            </div>
            <button type="submit" class="btn btn-default">下一步</button>
        </form>

    </div>
</div>
</body>
