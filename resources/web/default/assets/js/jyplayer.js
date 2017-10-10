$(document).ready(function() {
    var cookiePlayer = JY.getCookie('player');
    var timer     = '';
    var cacheList = {};
    var options = {
        listModel :'page',
        curPlayId : 0,
        curPlayDom : false,
        curPage   : false,
        maxError  : 1,
        curPlayerIndex : (new Date()).valueOf(),
    }
    JY = $.extend(JY, options);

    var listWramp = $('#play-list-wramp');
    var $playerList = $('#play_lists');
    var playerWramp = $('#footer-player');
    var lockbnt = $('#player-dw');
    var lrcWramp = $('#lrc-wramp');

    //初始化------------
    JY.player = $("#JYplayer").jPlayer({
        swfPath: JY.public + '/static/libs/jplayer', //swfUrl,
        volume: 0.7,
        supplied: "mp3,m4a",
        wmode: "window",
        cssSelectorAncestor: "#jy-player",
        keyEnabled: true,
        remainingDuration: true,
        toggleDuration: true,
        loop: false,
        ready : function() {},
        timeupdate : function(event) {
            time = event.jPlayer.status.currentTime;
        },
        ended : function() {palayNext()},
        error : function() {
            if (JY.curPlayId && JY.maxError < 3) {
                ++JY.maxError;
                infoAlert('Sorry song failed to load and load the next song...');
                setTimeout(palayNext, 500);
            }
        }
    });

    //播放事件
    $(document).on($.jPlayer.event.play, $.jPlayer.cssSelector, function() {
        
        if (JY.listModel = 'page') {
            var $cur = JY.curPlayDom.parents('.play_box');
            $playerList.find('li').removeClass('playing');
            $playerList.find('[data-id=' + JY.curPlayId + ']')
                .addClass('playing')
                .siblings().removeClass('playing');
        } else {
            var $cur = JY.curPlayDom;
        }
        JY.setCookie('player', {status : 'play', index  : JY.curPlayerIndex}); 
        $cur.addClass('playing').siblings().removeClass('playing');
        infoAlert('Has started playing');
    });

    //暂停事件
    $(document).on($.jPlayer.event.pause, $.jPlayer.cssSelector, function() {
        if (JY.listModel = 'page') {
            var $cur = JY.curPlayDom.parents('.play_box');
            $playerList.find('[data-id=' + JY.curPlayId + ']').removeClass('playing');
        } else {
            var $cur = JY.curPlayDom;
        }
        $cur.removeClass('playing');
        JY.setCookie('player', {status : 'pause', index : 0});
    });

    //下一曲
    $(document).on('click', '.jp-next', function() {
        palayNext();
    });

    //上一曲
    $(document).on('click', '.jp-previous', function() {
        palayPrev();
    });

    //下一曲
    var palayNext = function() {
        if (!$('.jp-repeat').is(':visible')) {
            JY.curPlayDom.trigger('click');
            return;
        }
        
        if(JY.listModel === 'page') {
            var $parents =JY.curPlayDom.parents('.play_box');
            var next = $parents.next();
            if (next.length < 1) {
                next = $parents.siblings().first();
            }
            next.find('.jp-play-me').trigger('click');
        } else {
            var next = JY.curPlayDom.next();
            if (next.length < 1) {
                next = $playerList.find('li').first();
            }
            next.find('.jp-play-me').trigger('click');
        }
    }

    //上一曲
    var palayPrev = function() {
        if (!$('.jp-repeat').is(':visible')) {
            JY.curPlayDom.trigger('click');
            return;
        }
        
        if(JY.listModel === 'page') {
            var $parents =JY.curPlayDom.parents('.play_box');
            var prev = $parents.prev();
            if (prev.length < 1) {
                prev = $parents.siblings().first();
            }
            prev.find('.jp-play-me').trigger('click');
        } else {
            var prev = JY.curPlayDom.prev();
            if (prev.length < 1) {
                prev = $playerList.find('li').first();
            }
            prev.find('.jp-play-me').trigger('click');
        }
    }

    $(document).on('click', '.jp-play-me', function(e) {
        e && e.preventDefault();
        var $this = $(this);
        JY.curPlayDom = $this;

        if ($this.find('.num').length) {
            var num = $this.find('.num').html();
            $this.find('.num').html(Number(num) + 1);
        }
        
        //获取歌曲Id
        var index = parseInt($(this).attr('data-id'));
        JY.curPlayId = index;

        var song = cacheList[index];
        if (typeof(song) === "undefined") {
            $.get(U("api/songs/" + index), function(res) {;
                if (res.code === 0) {
                    cacheList[index] = res.result;
                    playSong(res.result);
                }
            }, "json");
        } else {
            playSong(song);
        }

        //定义播放模式
        if ($this.parent('#play_lists').length) {
            JY.listModel = 'player';
        } else {
            JY.listModel = 'page';
            $('.jp-play-me').not($this).removeClass('active');
            $this.parents('.play_box').addClass('p-active').siblings().removeClass('p-active');
        }
        $this.toggleClass('active');
        
        //弹出播放器
        $('#footer-player').animate({bottom: 0}, 600);
    });


    /*专辑播放*/
    $('.album_play').click(function() {
        var album_id = $(this).attr('data-id');
        $.get($.Url.build("api/songs"), {album : album_id, sub : true}, function(res) {
            if (res.code === 0) {
                var data = res.result;
                var html = '';
                $.each(data, function(i, val){
                    html += makeList(val);
                    var index = parseInt(val['id'])
                    cacheList[index] = val;
                });

                JY.curPlayId = data[0]['id'];
                $playerList.prepend(html);
                $playerList.find('[data-id=' + JY.curPlayId + ']').click();
            } else {
                infoAlert(res.error);
            }
        }, "json");
        return false;
    });

    //播放器隐藏/显示
    lockbnt.click(function() {
        if (lockbnt.hasClass('on')) {
            lockbnt.html('<i class="jy jy-unlock-f"></i>').removeClass('on');
        } else {
            lockbnt.html('<i class="jy jy-lock-f"></i>').addClass('on');
        }
        return false;
    });

    playerWramp.hover(function() {
        playerWramp.stop().animate({
            bottom: 0
        }, 600);
    }, function() {
        if (!lockbnt.hasClass('on') && !lrcWramp.hasClass('on') && !listWramp.hasClass('on')) {
            playerWramp.stop().animate({
                bottom: -52
            }, 600);
        }
    });

    $('#lrc-btn').click(function() {
        if (lrcWramp.hasClass('on')) {
            lrcWramp.removeClass('on').hide();
        } else {
            lrcWramp.addClass('on').show();
        }
    });
    $('#l-close').click(function() {
        lrcWramp.removeClass('on').hide();
    });

    $('#list-btn').click(function() {
        if (listWramp.hasClass('on')) {
            listWramp.removeClass('on').hide();
        } else {
            listWramp.addClass('on').show();
        }
    });
    $('#pl-close').click(function() {
        listWramp.removeClass('on').hide();
    });

    timer = setInterval(function() {
        var player = JY.getCookie('player');
        if (player && (player['status'] == 'play') && (parseInt(player['index']) !== JY.curPlayerIndex)) {    
            JY.player.jPlayer("pause");
        }
    }, 800);

    function playSong(song) {
        JY.player.jPlayer("setMedia", {
            mp3: song.sub.listen_url,
            title: $.trim(song.artist_name) ? song.artist_name + ' - ' + song.name : song.name,
        });
        JY.player.jPlayer("play");
        $('#play-cover').attr('src', song.cover_url);
        var lrc = song.lrc;
        $('#l-title').html(song.name);
        if ($.type(lrc) != 'null') {
            $('#lrc_list').html('Lyrics loaded....');
            $('.lrc-content').text(lrc);
            $.lrc.start(lrc, function() {
                return time;
            });
        } else {
            $('#lrc_list').html('<li>Did not find the relevant lyrics....</li>');
            $.lrc.stop();
        }

        if ($playerList.find('[data-id=' + JY.curPlayId + ']').length < 1) {
            $playerList.prepend(makeList(song));
        }

        //添加歌曲试听数
        $.ajax({ url: $.Url.build("/api/actions/listen"),data: {id: JY.curPlayId},dataType: "jsonp"});
    }
});

/*创建播放列表*/
function makeList(song) {
    var $li = '<li class="jp-play-me" data-id="' + song['id'] + '">' + '<div class="play_icon"><i class="jy jy-play"></i></div>' + '<div class="play_name">' + song['name'] + '</div>' + '<div class="play_aname">' + song['artist_name'] + '</div>' + '<div class="play_del">' + '<!--a class="del-song" href="javascript:;" ><i class="jy jy-trash"></i></a-->' + '</div>' + '</li>';
    return $li;
}
/*歌词插件*/
(function(d) {
    d.lrc = {
        handle: null,
        list: [],
        regex: /^[^\[]*((?:\s*\[\d+\:\d+(?:\.\d+)?\])+)([\s\S]*)$/,
        regex_time: /\[(\d+)\:((?:\d+)(?:\.\d+)?)\]/g,
        regex_trim: /^\s+|\s+$/,
        callback: null,
        interval: .3,
        format: "\x3cli\x3e{html}\x3c/li\x3e",
        prefixid: "lrc",
        hoverClass: "hover",
        hoverTop: 100,
        duration: 0,
        __duration: -1,
        start: function(a, e) {
            if (!("string" != typeof a || 1 > a.length || "function" != typeof e)) {
                this.stop();
                this.callback = e;
                var c = null,
                    f = null,
                    l = "";
                a = a.split("\n");
                for (var b = 0; b < a.length; b++)
                    if (c = a[b].replace(this.regex_trim, ""), !(1 > c.length) && (c = this.regex.exec(c))) {
                        for (; f = this.regex_time.exec(c[1]);) this.list.push([60 * parseFloat(f[1]) + parseFloat(f[2]), c[2]]);
                        this.regex_time.lastIndex = 0
                    }
                if (0 < this.list.length) {
                    this.list.sort(function(a, c) {
                        return a[0] - c[0]
                    });
                    .1 <= this.list[0][0] && this.list.unshift([this.list[0][0] - .1, ""]);
                    this.list.push([this.list[this.list.length - 1][0] + 1, ""]);
                    for (b = 0; b < this.list.length; b++) l += this.format.replace(/\{html\}/gi, this.list[b][1]);
                    d("#" + this.prefixid + "_list").html(l).animate({
                        marginTop: 0
                    }, 100).show();
                    d("#" + this.prefixid + "_nofound").hide();
                    this.handle = setInterval("$.lrc.jump($.lrc.callback());", 1E3 * this.interval)
                } else d("#" + this.prefixid + "_list").hide(), d("#" + this.prefixid + "_nofound").show()
            }
        },
        jump: function(a) {
            if ("number" != typeof this.handle || "number" != typeof a || !d.isArray(this.list) || 1 > this.list.length) return this.stop();
            0 > a && (a = 0);
            if (this.__duration != a) {
                this.__duration = a += .2;
                a += this.interval;
                var e = 0,
                    c = this.list.length - 1;
                pivot = Math.floor(c / 2);
                tmpobj = null;
                tmp = 0;
                for (thisobj = this; e <= pivot && pivot <= c && !(this.list[pivot][0] <= a && (pivot == c || a < this.list[pivot + 1][0]));) {
                    this.list[pivot][0] > a ? c = pivot : e = pivot;
                    tmp = e + Math.floor((c - e) / 2);
                    if (tmp == pivot) break;
                    pivot = tmp
                }
                pivot != this.pivot && (this.pivot = pivot, tmpobj = d("#" + this.prefixid + "_list").children().removeClass(this.hoverClass).eq(pivot).addClass(thisobj.hoverClass), tmp = tmpobj.next().offset().top - tmpobj.parent().offset().top - this.hoverTop, tmp = 0 < tmp ? -1 * tmp : 0, this.animata(tmpobj.parent()[0]).animate({
                    marginTop: tmp + "px"
                }, 1E3 * this.interval))
            }
        },
        stop: function() {
            "number" == typeof this.handle && clearInterval(this.handle);
            this.handle = this.callback = null;
            this.__duration = -1;
            this.regex_time.lastIndex = 0;
            this.list = []
        },
        animata: function(a) {
            var e = j = 0,
                c, d = {
                    execution: function(d, b, f) {
                        var h = (new Date).getTime(),
                            k = f || 500,
                            m = parseInt(a.style[d]) || 0,
                            n = b - m;
                        (function() {
                            var b = (new Date).getTime() - h;
                            if (b > k) {
                                var b = k,
                                    f = a.style,
                                    g = b,
                                    b = -n * (g /= k) * (g - 2) + m;
                                f[d] = b + "px";
                                ++e == j && c && c.apply(a);
                                return !0
                            }
                            f = a.style;
                            g = b;
                            b = -n * (g /= k) * (g - 2) + m;
                            f[d] = b + "px";
                            setTimeout(arguments.callee, 10)
                        })()
                    },
                    animate: function(a, b, e) {
                        c = e;
                        for (var h in a) j++, d.execution(h, parseInt(a[h]), b)
                    }
                };
            return d
        }
    }
})(jQuery);