<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>系统工具</title>
    <script src="http://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
    <style>
        html, body {
            width: 100%;
        }
    </style>
</head>
<body>

<nav class="nav-extended">
    <div class="nav-content">
        <ul class="tabs tabs-transparent">
            <li class="tab"><a id="m_init" href="#git">git</a></li>
            <li class="tab" id="l_composer"><a id="m_composer" href="#composer">composer</a></li>
        </ul>
    </div>
</nav>
<div id="git" class="col s12" style="padding: 10px;height: 100%;">

    <div class="card " style="width: 100%;">

        <div class="card-content">

            <div class="input-field ">
                <input id="url" type="text" value="https://帐号:密码@">
                <label for="url">url</label>
            </div>
            <pre style="padding:10px;height: 300px;background: #eeeeee;" id="result"></pre>
        </div>
        <div class="card-action">
            <a href="#" id="init_btn" style="color:#26a69a;">克隆代码</a>
            <a href="#" id="clean_btn" style="color:#26a69a;">清理重置</a>
            <a href="#" id="pull_btn" style="color:#26a69a;">远程同步</a>
        </div>
    </div>

</div>
<div id="composer" class="col s12" style="padding: 10px;height: 100%;">
    <div class="card " style="width: 100%;">

        <div class="card-content">

            <div class="input-field ">
                <input id="composer_cmd" type="text" value="">
                <label for="composer_cmd">组件</label>
            </div>
            <pre style="padding:10px;height: 300px;background: #eeeeee;" id="composer_result"></pre>
        </div>
        <div class="card-action">
            <a href="#" id="composer_btn" style="color:#26a69a;">composer</a>
        </div>
    </div>
</div>
</body>
<script>
    $.get("/System/model/git/isInit.php", function (data) {
        if (data.code == "0") {
            $("#init_btn").hide();
        }
    });

    $("#composer_btn").click(function () {
        $("#composer_result").html("正在初始化...");
        $.post("/System/model/composer/composer.php", {
            cmd: $("#composer_cmd").val()
        }, function (data) {
            $("#composer_result").html(data);
        });
    });

    $("#init_btn").click(function () {
        $("#result").html("正在初始化...");
        $.post("/System/model/git/init.php", {
            url: $("#url").val()
        }, function (data) {
            $("#result").html(data);
        });
    });
    $("#clean_btn").click(function () {
        if (confirm("确定?")) {
            $("#result").html("正在清理...");
            $.post("/System/model/git/clean.php", {
                url: $("#url").val()
            }, function (data) {
                $("#result").append("已完成");
            });
        }
    });
    $("#pull_btn").click(function () {
        $("#result").html("正在同步...");
        $.get("/System/model/git/pull.php", function (data) {
            $("#result").html(data);
        });
    });
    $("#log_btn").click(function () {
        $.get("/System/model/git/log.php", function (data) {
            $("#result").html(data);
        });
    });
    $.get("/System/model/git/log.php", function (data) {
        $("#result").html(data);
    });
</script>
</html>