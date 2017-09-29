<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>页面不存在</title>
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        html {
            position: relative;
            min-height: 100%;
            background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAMAAAAp4XiDAAAAGFBMVEX29vb19fXw8PDy8vL09PTz8/Pv7+/x8fGKuegbAAAAyUlEQVR42pXRQQ7CMBRDwST9pfe/MahEmgURbt7WmpVb6+vG0dd9REnn66xRy/qXiCgmEIIJhGACIZhACCYQgvlDCDFIEAwSBIMEwSBBMEgQDBIEgwTBIEEwCJEMQiSDENFMQmQzCZEbNyGemd6KeGZ6u4hnXe2qbdLHFjhf1XqNLXHev4wdMd9nspiEiWISJgqECQJhgkCYIBAmCIQJAmGCQJggECYJhAkCEUMEwhCBMEQgDJEIQ2RSg0iEIRJhiB/S+rrjqvXQ3paIJUgPBXxiAAAAAElFTkSuQmCC");
            background-color: #ebeff2;
        }

        body{
            color: #333;
            font: 14px Verdana, "Helvetica Neue", helvetica, Arial, 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0 20px 20px;
            word-break: break-word;
        }

        a {
            color: #868686;
            cursor: pointer;
        }

        .wrapper-page {
            margin: 5% auto;
            position: relative;
            width: 420px;
        }

        .wrapper-page .text-error {
            color: #252932;
            font-size: 98px;
            font-weight: 700;
            line-height: 150px;
        }
        .wrapper-page h2  {
            /*color: #4288ce;*/
            font-weight: 400;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            color: #505458;
            margin: 10px 0;
            font-size: 30px;
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
        .copyright {
            margin-top: 24px;
            padding: 12px 0;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 6px 16px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: #5fbeaa !important;
            color:#fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="exception wrapper-page">
        <div class="ex-page-content text-center">
            <div class="text-error">
                <span class="text-primary">4</span>
                <i class="text-pink">0</i>
                <i class="text-info">4</i>
            </div>
            <div class="info"><h2>'ಠ_ಠ' 抱歉无法打开页面！</h2></div>
            <br>
            <p class="text-muted">
                可能网络信号差找不到请求的页面   或输入的网址不正确
            </p>
            <br>
            <p>
                <a class="btn btn-default" href="/"> 返回首页</a>
                <b id="wait">3</b>秒后跳转到首页
            </p>

        </div>
    </div>
    <div class="copyright" style="text-align: center">
        <a title="官方网站" href="http://www.jyuu.cn">JYmusic</a>
        <span>V<?php echo JYMUSIC_VERSION; ?></span>
        <span>[ 沉淀三年，待我霸气回眸 ]</span>
    </div>
    <script type="text/javascript">
        (function(){
            var interval;
            /*setTimeout(function () {
                var wait = document.getElementById('wait');
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        location.href = "/";
                        clearInterval(interval);
                    };
                }, 1000);
            },500);*/
        })();
    </script>
</body>
</html>