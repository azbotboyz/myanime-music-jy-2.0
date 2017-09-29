{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>JYmusic - 跳转提示</title>
    <link type="text/css" href="/public/static/libs/sweet-alert/css/sweet-alert.css" rel="stylesheet">
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        html {
            position: relative;
            min-height: 100%;
            background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAMAAAAp4XiDAAAAGFBMVEX29vb19fXw8PDy8vL09PTz8/Pv7+/x8fGKuegbAAAAyUlEQVR42pXRQQ7CMBRDwST9pfe/MahEmgURbt7WmpVb6+vG0dd9REnn66xRy/qXiCgmEIIJhGACIZhACCYQgvlDCDFIEAwSBIMEwSBBMEgQDBIEgwTBIEEwCJEMQiSDENFMQmQzCZEbNyGemd6KeGZ6u4hnXe2qbdLHFjhf1XqNLXHev4wdMd9nspiEiWISJgqECQJhgkCYIBAmCIQJAmGCQJggECYJhAkCEUMEwhCBMEQgDJEIQ2RSg0iEIRJhiB/S+rrjqvXQ3paIJUgPBXxiAAAAAElFTkSuQmCC");
            background-color: #ebeff2;
        }

        body{ 
            font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif;
            color: #333;
            font-size: 16px;
            height: 100%;
        }

        a {
            color: #868686;
            cursor: pointer;
        }

        .system-message{ display: block; line-height: 30px; }

        .sweet-overlay {
            background-color: #ccc;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=10)";
            background-color: rgba(0,0,0,.1);
        }

        .sweet-alert .sa-icon {
            width: 40px;
            height: 40px;
            border: 4px solid gray;
            -webkit-border-radius: 40px;
            border-radius: 50%;
            margin: 10px auto;
        }

        .sweet-alert h2 {
            color: #575757;
            font-size: 18px;
            margin: 10px 0;
            padding: 0;
            line-height: 30px;
        }
        .sweet-alert .sa-icon.sa-warning .sa-body {
            width: 4px;
            height: 27px;
            top: 2px;
        }

        .sweet-alert .sa-icon.sa-warning .sa-dot {
            width: 4px;
            height: 4px;
            margin-left: -2px;
            bottom: 5px;
        }

        .sweet-alert button {
            border-radius: 0;
        }
        .wait-msg {
            font-size: 14px;
        }
        .wrapper-page {
            margin: 5% auto;
            position: relative;
            width: 500px;
        }

        .wrapper-page h2 {
            color: #505458;
            font-family: "Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 10px 0;
            font-size: 30px;
        }

        .wrapper-page .text-error {
            color: #252932;
            font-size: 68px;
            font-weight: 700;
            line-height: 150px;
        }

        .text-center {
            text-align: center;
        }
        .text-primary {
            color: #5d9cec;
        }
        .text-pink {
            color: #fb6d9d;
            font-style: normal;
        }
        .text-info {
            color: #34d3eb;
            font-style: normal;
        }
        .text-muted {
            color: #98a6ad;
        }
        .copyright{
            margin-top: 24px;
            padding: 12px 0;
            border-top: 1px solid #eee;
            position: absolute;
            width: 100%;
            bottom: 5%;
            height: 36px;
        }

        .error-msg {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="exception wrapper-page">
        <div class="ex-page-content text-center">
            <div class="text-error">
                <span class="text-primary">E</span>
                <i class="text-pink">R</i>
                <i class="text-info">R</i>
                <i class="text-pink">O</i>
                <i class="text-info">R</i>
            </div>
            <div class="info"><h2>'ಠ_ಠ'</h2></div>
        </div>
    </div>
    <div class="system-message">
        <script type="text/javascript">
            var type    = 'warning';
            var msg     = '<span class="system-message error-msg wait-msg"  style="color: #e73c31;"><?php echo(strip_tags($msg));?></span>';
            var href = "<?php echo($url);?>";
            var wait = <?php echo($wait);?>;
        </script>
    </div>
    <div class="copyright" style="text-align: center">
        <a title="官方网站" href="http://www.jyuu.cn">JYmusic</a>
        <span>V<?php echo JYMUSIC_VERSION; ?></span>
        <span>[ 沉淀三年，待我霸气回眸 ]</span>
    </div>

    <script type="text/javascript" src="/public/static/libs/sweet-alert/js/sweet-alert.min.js"></script>
    <script type="text/javascript">
        (function(){
            var interval;
            swal({
                title: "系统提示",
                text: msg + '<span class="system-message wait-msg">系统将在 <i id="wait" class="text-info">'+wait+'</i>秒后自动跳转, 如没有跳转请<a id="href" href="<?php echo($url);?>">立即跳转</a></span>',
                type: type,
                html: true,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "立即跳转",
                cancelButtonText: "停止跳转",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    location.href = href;
                } else {
                    clearInterval(interval);
                }
            });

            setTimeout(function () {
                var wait = document.getElementById('wait'),
                    href = document.getElementById('href').href;
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                    };
                }, 1000);
            },500);
        })();
    </script>
</body>
</html>
