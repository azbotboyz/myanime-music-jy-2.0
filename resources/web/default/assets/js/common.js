// on document ready
(function($) {
    "use strict";

    var widget = function () {
        var ellipsis = function () {
            var el = $('.ellipsis').hide();
            el.each(function () {
                var self = $(this);
                self.css({
                    'width': self.parent().outerWidth(),
                    'white-space': 'nowrap'
                });
                self.show();
            });
        };

        return {
            //幻灯片
            slide: function () {
                //分享达人
                var bsu = $('.bestuser_carousel');
                if (bsu.length) {
                    var bu = bsu.owlCarousel({
                        itemsCustom: [[1199, 6], [992, 5], [768, 4], [590, 3], [300, 2]],
                        autoPlay: true,
                        slideSpeed: 1000,
                        autoHeight: true
                    });

                    $('.bestuser_prev').on('click', function () {
                        bu.trigger('owl.prev');
                    });

                    $('.bestuser_next').on('click', function () {
                        bu.trigger('owl.next');
                    });
                }

                //热门专辑
                var orw = $('.bestalbum_carousel');
                if (orw.length) {
                    var orwc = orw.owlCarousel({
                        itemsCustom: [[1199, 6], [992, 5], [768, 4], [590, 3], [300, 2]],
                        autoPlay: true,
                        slideSpeed: 1000,
                        autoHeight: true
                    });

                    $('.bestalbum_prev').on('click', function () {
                        orwc.trigger('owl.prev');
                    });

                    $('.bestalbum_next').on('click', function () {
                        orwc.trigger('owl.next');
                    });
                }

                //资讯页面
                var infoCarousel = $('.info_carousel');
                if (infoCarousel.length) {
                    var inc = infoCarousel.owlCarousel({
                        items: 1,
                        autoPlay: true,
                        slideSpeed: 1000,
                        autoHeight: true
                    });

                    $('.info_carousel_prev').on('click', function () {
                        inc.trigger('owl.prev');
                    });

                    $('.info_carousel_next').on('click', function () {
                        inc.trigger('owl.next');
                    });
                }

                //推荐列表
                var rp = $('.hot-carousel');
                if (rp.length) {
                    var qv = rp.owlCarousel({
                        itemsCustom: [[1199, 5], [992, 4], [768, 4], [480, 2], [300, 1]],
                        autoPlay: false,
                        slideSpeed: 1000,
                        autoHeight: true
                    });

                    $('.rp_prev').on('click', function () {
                        qv.trigger('owl.prev');
                    });

                    $('.rp_next').on('click', function () {
                        qv.trigger('owl.next');
                    });
                }
            },
            //字符截取
            ellipsis: function () {
                ellipsis();
                $(window).on('resize', ellipsis);
            },
            //菜单
            menu: function () {
                var menuWrap = $('[role="navigation"]'),
                    menu = $('.main_menu'),
                    button = $('#menu_button');

                function orientationChange() {
                    if ($(window).width() < 767) {
                        button.off('click').on('click', function () {
                            menuWrap.stop().slideToggle();
                            $(this).toggleClass('active');
                        });
                        menu.children('li').children('a').off('click').on('click', function (e) {
                            var self = $(this);
                            self.closest('li')
                                .toggleClass('current_click')
                                .find('.sub_menu_wrap')
                                .stop()
                                .slideToggle()
                                .end()
                                .closest('li')
                                .siblings('li')
                                .removeClass('current_click')
                                .children('a').removeClass('prevented').parent()
                                .find('.sub_menu_wrap')
                                .stop()
                                .slideUp();
                            if (!(self.hasClass('prevented'))) {
                                e.preventDefault();
                                self.addClass('prevented');
                            } else {
                                self.removeClass('prevented');
                            }
                        });
                    } else if ($(window).width() > 767) {
                        menuWrap.removeAttr('style').find('.sub_menu_wrap').removeAttr('style');
                        menu.children('li').children('a').off('click');
                    }
                }

                orientationChange();
                $(window).on('resize', orientationChange);
            },
            // 自定义 select
            select: function () {
                $('.custom_select').each(function () {
                    var list = $(this).children('ul'),
                        select = $(this).find('select'),
                        title = $(this).find('.select_title');
                    title.css('min-width', title.outerWidth());
                    // select items to list items
                    if ($(this).find('[data-filter]').length) {
                        for (var i = 0, len = select.children('option').length; i < len; i++) {
                            list.append('<li data-filter="' + select.children('option').eq(i).data('filter') + '" class="tr_delay_hover">' + select.children('option').eq(i).text() + '</li>');
                        }
                    } else {
                        for (var i = 0, len = select.children('option').length; i < len; i++) {
                            list.append('<li class="tr_delay_hover">' + select.children('option').eq(i).text() + '</li>');
                        }
                    }
                    select.hide();

                    // 开启列表
                    title.on('click', function () {
                        list.slideToggle(400);
                        $(this).toggleClass('active');
                    });

                    // 选择选项
                    list.on('click', 'li', function () {
                        var val = $(this).text();
                        title.text(val);
                        list.slideUp(400);
                        select.val(val);
                        title.toggleClass('active');
                    });
                });
            },
            // popup
            popup: function () {
                var $popup = $('.popup');

                $popup.on('popup/position', function () {
                    var _this = $(this),
                        pos = setTimeout(function () {
                            var mt = _this.outerHeight() / -2,
                                ml = _this.outerWidth() / -2;
                            _this.css({
                                'margin-left': ml,
                                'margin-top': mt
                            });
                            clearTimeout(pos);
                        }, 100);
                });

                var close = $('.popup > .close');
                if ($('[data-popup]').length) {
                    $("body").on('click', '[data-popup]', function (e) {
                        var popup = $(this).data('popup'),
                            pc = $(popup).find('.popup');

                        pc.trigger('popup/position');

                        $(popup).fadeIn(function () {
                            $(popup).on('click', function (e) {
                                if ($(e.target).hasClass('popup_wrap')) {
                                    $(this).fadeOut();
                                }
                            });
                        });
                        e.preventDefault();
                    });
                }
                close.on('click', function () {
                    $(this).closest('.popup_wrap').fadeOut();
                });
            },
            //滚动条
            scroll : function () {
                var scroll = $('.custom_scrollbar');
                if (scroll.length) {
                    var isVisible = setInterval(function() {
                        if (scroll.is(':visible')) {
                            scroll.customScrollbar({
                                preventDefaultScroll: true
                            });
                            clearInterval(isVisible);
                        }
                    }, 100);
                }
            },

            //小工具
            tool : function () {

            }
        }
    };

    $(function ($) {
        var initPage = widget();
        initPage.slide();
        initPage.ellipsis();
        initPage.select();
        initPage.popup();
        initPage.scroll();
        initPage.tool();
    });

    var globalDfd = $.Deferred();
    $(window).bind('load', function() {
        // 加载所有的脚本
        globalDfd.resolve();
        //测试登录
        checkLogin();
    });

    $(function() {
        $.fx.speeds._default = 500;
        $('#myTab a:last').tab('show');
        // 打开下拉
        // animation 主页
        (function() {
            globalDfd.done(function() {
                $('.bestuser_carousel .animate_ftb ').waypointSynchronise({
                    container: '.bestuser_carousel',
                    delay: 200,
                    offset: 700,
                    globalDelay: 400,
                    classN: "animate_vertical_finished"
                });

                $('.bestalbum_carousel .animate_ftb').waypointSynchronise({
                    container: '.bestalbum_carousel',
                    delay: 200,
                    offset: 700,
                    globalDelay: 400,
                    classN: "animate_vertical_finished"
                });

                $('.animate_half_tc').waypointSynchronise({
                    container: '.row',
                    delay: 0,
                    offset: 830,
                    classN: "animate_horizontal_finished"
                });

                $('.nav_buttons_wrap.animate_fade').waypointInit('animate_sj_finished animate_fade_finished', '800px');


                $('.s_animate.animate_ftr').waypointInit('animate_horizontal_finished', '800px');

            });
        })();

        // ie9 占位符
        (function() {
            if ($('html').hasClass('ie9')) {
                $('input[placeholder]').each(function() {
                    $(this).val($(this).attr('placeholder'));
                    var v = $(this).val();
                    $(this).on('focus', function() {
                        if ($(this).val() === v) {
                            $(this).val("");
                        }
                    }).on("blur", function() {
                        if ($(this).val() === "") {
                            $(this).val(v);
                        }
                    });
                });
            }
        })();


        $('body').on('click', '.rating_list li', function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active').prevAll().addClass('active');
        });


    });

    $(window).load(function() {
        function randomSort(selector, items) {
            var sel = selector,
                it = items,
                random = [],
                len = it.length;
            it.removeClass('random');
            if (selector === ".random") {
                for (var i = 0; i < len; i++) {
                    random.push(+(Math.random() * len).toFixed());
                }
                $.each(random, function(i, v) {
                    items.eq(Math.floor(Math.random() * v - 1)).addClass('random');
                });
            }
        }
        //艺人页面
        (function() {
            // 作品集
            if ($('.portfolio_masonry_container').length) {
                var container1 = $('.portfolio_masonry_container');
                container1.isotope({
                    itemSelector: '.portfolio_item',
                    layoutMode: 'masonry',
                    masonry: {
                        columnWidth: 10,
                        gutter: 0
                    }
                });

                $('#filter_portfolio').on('click', 'li', function() {
                    var self = $(this),
                        selector = self.data('filter');
                    container1.isotope({
                        filter: selector
                    });
                });
            }
        })();

    });

    // 粘性导航菜单
    window.sticky = function() {
        var container = $('.h_bot_part'),
            offset = container.closest('[role="banner"]').hasClass('type_5') ? 0 : -container.outerHeight(),
            menu = $('.menu_wrap'),
            mHeight = menu.outerHeight();
        container.waypoint(function(direction) {
            var _this = $(this);
            if (direction === "down") {
                menu.addClass('sticky');
                container.parent('[role="banner"]').css('border-bottom', mHeight + "px solid transparent");
            } else if (direction === "up") {
                menu.removeClass('sticky');
                container.parent('[role="banner"]').css('border-bottom', 'none');
            }
        }, {
            offset: offset
        });

        function getMenuWidth() {
            if (menu.hasClass('type_2')) {
                menu.css('width', menu.parent().width());
            }
        }
        getMenuWidth();
        $(window).on('resize', getMenuWidth);
    };
    sticky();

    //css3 动画
    $.fn.css3Animate = function(element) {
        return $(this).on('click', function(e) {
            var dropdown = element;
            $(this).toggleClass('active');
            e.preventDefault();
            if (dropdown.hasClass('opened')) {
                dropdown.removeClass('opened').addClass('closed');
                setTimeout(function() {
                    dropdown.removeClass('closed');
                }, 500);
            } else {
                dropdown.addClass('opened');
            }
        });
    };
    // 站点辅助函数
    $.fn.waypointInit = function(classN, offset, delay, inv) {
        return $(this).waypoint(function(direction) {
            var current = $(this);
            if (direction === 'down') {
                if (delay) {
                    setTimeout(function() {
                        current.addClass(classN);
                    }, delay);
                } else {
                    current.addClass(classN);
                }
            } else {
                if (inv === true) {
                    current.removeClass(classN);
                }
            }
        }, {
            offset: offset
        });
    };
    // 同位
    $.fn.waypointSynchronise = function(config) {
        var element = $(this);
        function addClassToElem(el, eq) {
            el.eq(eq).addClass(config.classN);
        }
        element.closest(config.container).waypoint(function(direction) {
            element.each(function(i) {
                if (direction === 'down') {
                    if (config.globalDelay !== undefined) {
                        setTimeout(function() {
                            setTimeout(function() {
                                addClassToElem(element, i);
                            }, i * config.delay);
                        }, config.globalDelay);
                    } else {
                        setTimeout(function() {
                            addClassToElem(element, i);
                        }, i * config.delay);
                    }
                } else {
                    if (config.inv) {
                        setTimeout(function() {
                            element.eq(i).removeClass(config.classN);
                        }, i * config.delay);
                    }
                }
            });
        }, {
            offset: config.offset
        });
        return element;
    };

    var $goTo = $('#go_to_top');
    $goTo.waypointInit('animate_horizontal_finished', '0px', 0, true);
    $goTo.on('click', function() {
        $('html,body').animate({ scrollTop: 0 }, 500);
    });
    $('.sw_button').on('click', function() {
        $(this).parent().toggleClass('opened').siblings().removeClass('opened');
    });


})(jQuery);


//检测登录
function checkLogin() {
    $.App.activeUser(function(res){
        console.log(res);
        if (res.code == 0) {
            var user = res.result;
            $('#user-info').html('<a href="' + user.url + '">' + user.nickname + '</a>');
            $('#upage-url').attr('href', user.url);
            $('.user-show').hide();
            $('.user-hide').show();
            if ($('.login_btn').size() > 0) {
                $('.login_btn').addClass('disabled').prop('disabled', true);
            }

        }
        return false;
    });
}

//ajax post submit请求
$(function($) {
    //顶部导航
    var $navBar = $('#navbar');
    var li = $navBar.find('.current');
    if (li.length > 0) {
        var left = li.position().left;
        var width = (li.width() / 2) - 9;
        var defleft = left + width;
    } else {
        var defleft = 24;
    }

    $("#cre").css("left", defleft);
    $('.t-nav').mouseenter(function() {
        var left = $(this).position().left;
        var width = ($(this).width() / 2) - 9;
        var posleft = left + width;
        $("#cre").animate({
            left: posleft
        }, 200);
    });

    $navBar.mouseleave(function() {
        $("#cre").animate({
            left: defleft
        }, 200);
    });

    $('.ajax-post').click(function(e) {
        postForm(e);
    });

    $('#login_btn').click(function(e){
        e.preventDefault();
        $('#login_popup').find('.popup').css({
            'position' :'static',
            'width' : '360px'
        });
        $.Action.openModal($('#login_popup').html());
    });

    $(document).on('click', '.ajax-post-form', function(e){
        e.preventDefault();
        var form,targetForm = $(this).data('target-form');
        if (typeof targetForm === 'undefined' ) {
            form = $(this).parents('form');
        } else {
            form = $(targetForm);
        }
        $.Action.postForm(form);
    });

    //登录
    $(document).on('click', '.ajax-login-user',function(e) {
        e.preventDefault();
        var $this = $(this);
        var form = $this.parents('form');
        $.ajax({
            type: "get",
            async: false,
            url: form.attr('action'),
            data : form.serialize(),
            dataType: "jsonp",
            success: function(res){
                if (res.code == 0) {
                    infoAlert(res.msg, true);
                    checkLogin();
                    setTimeout(function(){
                        $.Notify.close();
                    }, 1500);               
                } else {  
                    $.Notify.msg(res.error);
                    if ($(".reloadverify").length) {
                        $(".reloadverify").click();
                    }
                }
            },
            error: function(){
                console.log('用户登录获取失败');
            }
        });
        return false;
    });

    //ajax退出登录
    $('#login_out').click(function(e) {
        e.preventDefault();
        $.App.logout(function(res){
            if (res.code === 0) {
                $('.user-show').show();
                $('.user-hide').hide();
            }
            layer.closeAll();
        }, function(){
            console.log('退出失败');
        });
        return false;
    });
    //公共按钮操作
    $(document).on('click', "[data-action=fav]", function(){
        var $this = $(this);
        var id = $this.data('id');
        var type = $this.data('type');
        if (typeof type === 'undefined' || type === 'song') {
            type = 'songs'
        }

        if (!parseInt(id)) {
            $.Notify.msg('无效的参数！！');
            return false;
        }

        $.Action.ajaxPost($.Url.build('/api/actions/fav'), {id : id,type : type}, function(res){
            if (res.remove) {
                $this.removeClass('active');
            } else {
                $this.addClass('active');
            }
            return false;
        });

    });

    $(document).on('click', "[data-action=digg]", function(){
        var $this = $(this);
        var id = $this.data('id');
        var type = $this.data('type');
        if (typeof type === 'undefined' || type === 'song') {
            type = 'songs'
        }

        if (!parseInt(id)) {
            $.Notify.msg('无效的参数！！');
            return false;
        }

        $.Action.ajaxPost($.Url.build('/api/actions/digg'), {id : id,type : type}, function(res){
            if (res.remove) {
                $this.removeClass('active');
            } else {
                $this.addClass('active');
            }
            return false;
        });
    });

    $(document).on('click', "[data-action=bury]", function(){
        $.Action.bury($(this));
    });

    var $introduce = $('.artist-introduce');
    if ($introduce.length) {
        $introduce.show();

        var height = $introduce.height();
        var $btn = $('.introduce-btns-action');
        if (height > 120) {
            $introduce.css('height', '120px');    
            $btn.show().click(function(argument) {
                if ($btn.hasClass('active')) {
                    $introduce.css('height', '120px');
                    $btn.html('展开<i class="jy jy-sort-down"></i>').removeClass('active');
                } else {
                    $introduce.css('height', 'auto');
                    $btn.html('收起<i class="jy jy-sort-up"></i>').addClass('active');
                } 
            })
        }
    }

    //回车提交
    $("#search_form").keydown(function(e) {
        var e = e || event,
            keycode = e.which || e.keyCode;
        if (keycode === 13) {
            $(this).submit();
        }
    }).submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var keys = form.find("input[name='keys']").val();
        if (!$.trim(keys)) {
            JY.tipMsg('请输入搜索关键字');
            return false;
        }
        var query = form.find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
        query = query.replace(/^&/g, '');
        if (url.indexOf('?') > 0) {
            url += '&' + query;
        } else {
            url += '?' + query;
        }

        window.location.href = url;
        return false;
    });

});


/*重新封装便于调用*/
function U(str) {
    return $.Url.build(str);
}

/*重新封装便于调用*/
function infoAlert(text, type, time) {
    JY.tipMsg(text, type, time);
}

function downMusic(sid) {
    $.Notify.load();
    $.ajax({
        type: "post",
        url: U('user/down/check'),
        data: { 'id': sid },
        dataType: "json",
        success: function(res) {
            if (res.code === 0) {
                if (res.is_confirm === 1) {
                    $.Notify.confirm(res.msg, function () {
                        getDownUrl(sid);
                    });
                } else {
                    getDownUrl(sid);
                }
                return false;
            } else {
                JY.tipMsg(res.error);
                return false;
            }
        }
    });
    return false;
}

function getDownUrl(sid) {

    $.Notify.load('正在获取文件...');
    $.ajax({
        type: "post",
        url: U('user/down/get'),
        data: { 'id': sid},
        dataType: "json",
        success: function(res) {
            if (res.code === 0) {
                var file = res.result;
                var html = '';
                if (file.is_local === 1) {
                    html ='<p class="text-center mt_20">'+
                        '<a class="btn btn-success" target="_blank" href="' + file['down_url'] + '">【点击自动下载】</a>'+
                    '</p>'+
                    '<p class="text-center mt_20">'+
                        '<a class="btn btn-success"  href="' + file['down_url'] + '" >【使用右键另存】</a>'+
                    '</p>';

                } else if (file.is_disk === 1) {
                    html = '<p class="text-center mt_20">网盘验证码：<b class="text-danger">' + file['disk_pass'] + '</b></p>' +
                        '<p class="text-center mt_20">网盘链接地址<a class="text-info" href="' + file['disk_url'] + '" target="_blank">【点击前往】</a></p>';

                } else {
                    html ='<p class="text-center mt_20"><a class="btn btn-success" href="' + file['down_url'] + '">【使用右键另存1】</a></p>' +
                        '<p class="text-center mt_20"><a class="btn btn-success"  href="' + file['down_url'] + '" >【使用右键另存2】</a></p>';
                }
                $.Notify.closeLoad();
                $.Notify.open(html, '下载[' + file['name'] + ']', 300, 200);
            } else {
                JY.tipMsg(res.error, 2);
            }
        }
    });
    return false;
}

function gotoUrl(url) {
    $('body').append('<a href="' + url + '" id="goto" target="_blank"></a>');
    $('#goto').get(0).click();
}

