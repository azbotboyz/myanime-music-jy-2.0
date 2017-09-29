var jp = $("#jy-player");
$(document).ready(function() {
        /*播放器*/
        //初始化-----------
        jp.jPlayer({
            volume: 1,
            swfPath: JY.public + "/static/player",
            supplied: "mp3,m4a,ogg",
            wmode: "window",
            solution: "html",
            keyEnabled: true,
            remainingDuration: true,
            toggleDuration: true,
            cssSelectorAncestor: "#song-play",
            loop: false,
            ended: function() {
                var url = $('.jp-next').attr('href');
                document.location.href = url;
            }
        });
        //暂停事件
        $(document).on($.jPlayer.event.pause, $.jPlayer.cssSelector, function() {
            $('.img_border').removeClass('rotate');
        });
        //播放事件
        $(document).on($.jPlayer.event.play, $.jPlayer.cssSelector, function() {
            $('.img_border').addClass('rotate');
        });

    });
    //设置播放
function setplayer(songApiUrl) {
    $.get(songApiUrl, function(res) {
        if (res.code === 0) {
            var data = res.result;
            jp.jPlayer('setMedia', { 'mp3': data.sub.listen_url, 'title': data.name, 'id': data.id });
            jp.jPlayer("play");
            $('.play-song').html(data.name);
            $('.play-user').html(data.up_uname);
        }
    }, "json");
}