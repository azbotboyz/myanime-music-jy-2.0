/**
 * Theme: JYmusic Admin Template app.js
 * Author: JYmusic
 */
/*! iCheck v1.0.2 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
(function(f){function A(a,b,d){var c=a[0],g=/er/.test(d)?_indeterminate:/bl/.test(d)?n:k,e=d==_update?{checked:c[k],disabled:c[n],indeterminate:"true"==a.attr(_indeterminate)||"false"==a.attr(_determinate)}:c[g];if(/^(ch|di|in)/.test(d)&&!e)x(a,g);else if(/^(un|en|de)/.test(d)&&e)q(a,g);else if(d==_update)for(var f in e)e[f]?x(a,f,!0):q(a,f,!0);else if(!b||"toggle"==d){if(!b)a[_callback]("ifClicked");e?c[_type]!==r&&q(a,g):x(a,g)}}function x(a,b,d){var c=a[0],g=a.parent(),e=b==k,u=b==_indeterminate,
v=b==n,s=u?_determinate:e?y:"enabled",F=l(a,s+t(c[_type])),B=l(a,b+t(c[_type]));if(!0!==c[b]){if(!d&&b==k&&c[_type]==r&&c.name){var w=a.closest("form"),p='input[name="'+c.name+'"]',p=w.length?w.find(p):f(p);p.each(function(){this!==c&&f(this).data(m)&&q(f(this),b)})}u?(c[b]=!0,c[k]&&q(a,k,"force")):(d||(c[b]=!0),e&&c[_indeterminate]&&q(a,_indeterminate,!1));D(a,e,b,d)}c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"default");g[_add](B||l(a,b)||"");g.attr("role")&&!u&&g.attr("aria-"+(v?n:k),"true");
g[_remove](F||l(a,s)||"")}function q(a,b,d){var c=a[0],g=a.parent(),e=b==k,f=b==_indeterminate,m=b==n,s=f?_determinate:e?y:"enabled",q=l(a,s+t(c[_type])),r=l(a,b+t(c[_type]));if(!1!==c[b]){if(f||!d||"force"==d)c[b]=!1;D(a,e,s,d)}!c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"pointer");g[_remove](r||l(a,b)||"");g.attr("role")&&!f&&g.attr("aria-"+(m?n:k),"false");g[_add](q||l(a,s)||"")}function E(a,b){if(a.data(m)){a.parent().html(a.attr("style",a.data(m).s||""));if(b)a[_callback](b);a.off(".i").unwrap();
f(_label+'[for="'+a[0].id+'"]').add(a.closest(_label)).off(".i")}}function l(a,b,f){if(a.data(m))return a.data(m).o[b+(f?"":"Class")]}function t(a){return a.charAt(0).toUpperCase()+a.slice(1)}function D(a,b,f,c){if(!c){if(b)a[_callback]("ifToggled");a[_callback]("ifChanged")[_callback]("if"+t(f))}}var m="iCheck",C=m+"-helper",r="radio",k="checked",y="un"+k,n="disabled";_determinate="determinate";_indeterminate="in"+_determinate;_update="update";_type="type";_click="click";_touch="touchbegin.i touchend.i";
_add="addClass";_remove="removeClass";_callback="trigger";_label="label";_cursor="cursor";_mobile=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);f.fn[m]=function(a,b){var d='input[type="checkbox"], input[type="'+r+'"]',c=f(),g=function(a){a.each(function(){var a=f(this);c=a.is(d)?c.add(a):c.add(a.find(d))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(a))return a=a.toLowerCase(),g(this),c.each(function(){var c=
f(this);"destroy"==a?E(c,"ifDestroyed"):A(c,!0,a);f.isFunction(b)&&b()});if("object"!=typeof a&&a)return this;var e=f.extend({checkedClass:k,disabledClass:n,indeterminateClass:_indeterminate,labelHover:!0},a),l=e.handle,v=e.hoverClass||"hover",s=e.focusClass||"focus",t=e.activeClass||"active",B=!!e.labelHover,w=e.labelHoverClass||"hover",p=(""+e.increaseArea).replace("%","")|0;if("checkbox"==l||l==r)d='input[type="'+l+'"]';-50>p&&(p=-50);g(this);return c.each(function(){var a=f(this);E(a);var c=this,
b=c.id,g=-p+"%",d=100+2*p+"%",d={position:"absolute",top:g,left:g,display:"block",width:d,height:d,margin:0,padding:0,background:"#fff",border:0,opacity:0},g=_mobile?{position:"absolute",visibility:"hidden"}:p?d:{position:"absolute",opacity:0},l="checkbox"==c[_type]?e.checkboxClass||"icheckbox":e.radioClass||"i"+r,z=f(_label+'[for="'+b+'"]').add(a.closest(_label)),u=!!e.aria,y=m+"-"+Math.random().toString(36).substr(2,6),h='<div class="'+l+'" '+(u?'role="'+c[_type]+'" ':"");u&&z.each(function(){h+=
'aria-labelledby="';this.id?h+=this.id:(this.id=y,h+=y);h+='"'});h=a.wrap(h+"/>")[_callback]("ifCreated").parent().append(e.insert);d=f('<ins class="'+C+'"/>').css(d).appendTo(h);a.data(m,{o:e,s:a.attr("style")}).css(g);e.inheritClass&&h[_add](c.className||"");e.inheritID&&b&&h.attr("id",m+"-"+b);"static"==h.css("position")&&h.css("position","relative");A(a,!0,_update);if(z.length)z.on(_click+".i mouseover.i mouseout.i "+_touch,function(b){var d=b[_type],e=f(this);if(!c[n]){if(d==_click){if(f(b.target).is("a"))return;
A(a,!1,!0)}else B&&(/ut|nd/.test(d)?(h[_remove](v),e[_remove](w)):(h[_add](v),e[_add](w)));if(_mobile)b.stopPropagation();else return!1}});a.on(_click+".i focus.i blur.i keyup.i keydown.i keypress.i",function(b){var d=b[_type];b=b.keyCode;if(d==_click)return!1;if("keydown"==d&&32==b)return c[_type]==r&&c[k]||(c[k]?q(a,k):x(a,k)),!1;if("keyup"==d&&c[_type]==r)!c[k]&&x(a,k);else if(/us|ur/.test(d))h["blur"==d?_remove:_add](s)});d.on(_click+" mousedown mouseup mouseover mouseout "+_touch,function(b){var d=
b[_type],e=/wn|up/.test(d)?t:v;if(!c[n]){if(d==_click)A(a,!1,!0);else{if(/wn|er|in/.test(d))h[_add](e);else h[_remove](e+" "+t);if(z.length&&B&&e==v)z[/ut|nd/.test(d)?_remove:_add](w)}if(_mobile)b.stopPropagation();else return!1}})})}})(window.jQuery||window.Zepto);

! function($) {

    "use strict";
    var Sidemenu = function() {
        this.$body = $("body"),
        this.$openLeftBtn = $(".open-left"),
        this.$menuItem = $("#sidebar-menu a")
    };

    Sidemenu.prototype.openLeftBar = function() {
        $("#wrapper").toggleClass("enlarged");
        $("#wrapper").addClass("forced");
        if ($("#wrapper").hasClass("enlarged") && $("body").hasClass("fixed-left")) {
            $("body").removeClass("fixed-left").addClass("fixed-left-void");
        } else if (!$("#wrapper").hasClass("enlarged") && $("body").hasClass("fixed-left-void")) {
            $("body").removeClass("fixed-left-void").addClass("fixed-left");
        }
        if ($("#wrapper").hasClass("enlarged")) {
            $(".left ul").removeAttr("style");
        } else {
            $(".subdrop").siblings("ul:first").show();
        }

        toggle_slimscroll(".slimscrollleft");
        $("body").trigger("resize");
    },

    //menu item click
    Sidemenu.prototype.menuItemClick = function(e) {
        if (!$("#wrapper").hasClass("enlarged")) {
            if ($(this).parent().hasClass("has_sub")) {
                e.preventDefault();
            }
            if (!$(this).hasClass("subdrop")) {
                // hide any open menus and remove all other classes
                $("ul", $(this).parents("ul:first")).slideUp(150);
                $("a", $(this).parents("ul:first")).removeClass("subdrop");
                $("#sidebar-menu .pull-right i").removeClass("md-remove").addClass("md-add");
                // open our new menu and add the open class
                $(this).next("ul").slideDown(150);
                $(this).addClass("subdrop");
                $(".pull-right i", $(this).parents(".has_sub:last")).removeClass("md-add").addClass("md-remove");
                $(".pull-right i", $(this).siblings("ul")).removeClass("md-remove").addClass("md-add");
            } else if ($(this).hasClass("subdrop")) {
                $(this).removeClass("subdrop");
                $(this).next("ul").slideUp(150);
                $(".pull-right i", $(this).parent()).removeClass("md-remove").addClass("md-add");
            }
        }
    },
    //init sidemenu 初始化侧边栏导航
    Sidemenu.prototype.init = function() {
        var $this = this;
        //bind on click
        $(".open-left").click(function(e) {
            e.stopPropagation();
            $this.openLeftBar();
        });
        // LEFT SIDE MAIN NAVIGATION
        $this.$menuItem.on('click', $this.menuItemClick);
        // NAVIGATION HIGHLIGHT & OPEN PARENT
        $("#sidebar-menu ul li.has_sub a.active").parents("li:last").children("a:first").addClass("active").trigger("click");
    },
    //init Sidemenu
    $.Sidemenu = new Sidemenu, $.Sidemenu.Constructor = Sidemenu
}(window.jQuery),

/**全屏**/
function($) {
    "use strict";
    var FullScreen = function() {
        this.$body = $("body"),
        this.$fullscreenBtn = $("#btn-fullscreen")
    };
    //turn on full screen
    // Thanks to http://davidwalsh.name/fullscreen
    FullScreen.prototype.launchFullscreen = function(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        },
        FullScreen.prototype.exitFullscreen = function() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        },
        //toggle screen
        FullScreen.prototype.toggle_fullscreen = function() {
            var $this = this;
            var fullscreenEnabled = document.fullscreenEnabled || document.mozFullScreenEnabled || document.webkitFullscreenEnabled;
            if (fullscreenEnabled) {
                if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                    $this.launchFullscreen(document.documentElement);
                } else {
                    $this.exitFullscreen();
                }
            }
        },
        //init sidemenu
        FullScreen.prototype.init = function() {
            var $this = this;
            //bind
            $this.$fullscreenBtn.on('click', function() {
                $this.toggle_fullscreen();
            });
        },
        //init FullScreen
        $.FullScreen = new FullScreen, $.FullScreen.Constructor = FullScreen
}(window.jQuery),

function($) {

    var Notify = function(){
        this.isLoading = false,
        this.curBox = false,
        this.layerLoading = {}
    };

    /**
     * 信息提示
     * @param {string} msg    消息内容
     * @param {string} type   消息类型 'warning' , 'success' ,'error' 
     * @param {int}    timer  提示持续时间
     * @param {string} title  消息标题
     * @returns void
     */
    Notify.prototype.msg = function (msg, type, timer, title) {
        if (typeof type === 'undefined') {
            type = 'warning';
        }
        var icons = {'warning': 0, 'success': 1, 'error' : 2};
        if (typeof title === 'undefined') {
            title = "system hint";
        }
        if (isNaN(timer)) {
            timer = 3;
        }

        timer = timer * 1000;    
        this.curBox = layer.msg(msg, {
            time: timer,
            area: '300px;',
            id: 'jy-laymsg', //设定一个id，防止重复弹出
            icon: icons[type],
            btnAlign: 'c',
            btn: ['shut down']
        });
    };

    Notify.prototype.confirm = function (msg, func) {    
        this.curBox = layer.confirm(msg, {
            title:'系统提示',
            btn: ['确定','取消'] //按钮
        }, function(){
            func();
        });
    };

    Notify.prototype.loading = function (msg, timer) {
        if (typeof msg === 'undefined') {
            msg = "正在加载，请稍后...";
        }

        if (isNaN(timer)) {
            timer = 300;
        }

        this.curBox = layer.msg(msg, {
            icon: 16,
            shade: 0.01,
            time: timer * 1000,
            area: '240px'
        });
    };

    Notify.prototype.close = function () {
        if (this.curBox) {
            layer.close(this.curBox);
            return;
        }
        layer.closeAll();
    };

    Notify.prototype.open = function (html) {
        layer.open({
            type: 1,
            skin: 'layui-layer-demo', //样式类名
            closeBtn: 1, //不显示关闭按钮
            anim: 2,
            shadeClose: true, //开启遮罩关闭
            content: html
        });
    }


    /**
     * 顶部信息提示
     * @param {string} msg   消息内容
     * @param {string} type  消息类型 'warning' , 'success' ,'error'
     * @param {int}    timer 提示持续时间
     * @returns void
     */
    Notify.prototype.topMsg = function (msg, type, timer) {
        var $dom = $('#alerttop'),
            //timeout1,
            icon,
            style = 'kode-alert kode-alert-icon kode-alert-click kode-alert-top ';
        if (type == 'success') {
            style   += 'alert3';
            icon    = '<i class="fa fa-check" style="position: static;display: inline-block;"></i>';
        } else if (type == 'info') {
            style   += 'alert1';
            icon    = '<i class="fa fa-info" style="position: static;display: inline-block;"></i>';
        } else if (type == 'error') {
            style   += 'alert6';
            icon    = '<i class="fa fa-warning" style="position: static;display: inline-block;"></i>';
        } else {
            style   += 'alert5';
            icon    = '<i  class="fa fa-warning" style="position: static; display: inline-block;"></i>';
        }
        timer = !isNaN(timer)? timer * 1000 : 3000;
        if (this.curBox) {
            layer.close(this.curBox);
        }
        $dom.html(icon + msg ).attr('class', style).css('text-align', 'center').fadeIn(300);
        clearTimeout(timeout1);
        var timeout1 = window.setTimeout(function () {
            $dom.fadeOut(500);

        }, timer)
    };


    $.Notify = new Notify, $.Notify.Constructor = Notify;

}(window.jQuery),   

/**主操作模块**/
function($) {
    var Action = function(){
        this.isSubmit = false,
        this.curDom = false
    };

    /**
     * 公共返回成功操作
     * @param {json} data
     */
    Action.prototype.success = function (res, func) {
        var $this = this;
        if (res.code) {
            var msg = res.msg;
            var noRefresh = !$.Action.curDom? $.Action.curDom : $.Action.curDom.hasClass('no-refresh');
            
            if (res.url && !noRefresh) {
                msg = msg + ' 页面即将自动跳转~';
            }
    
            $.Notify.msg(msg, 'success');
            if (!noRefresh) {
                setTimeout(function () {
                    if (res.url) {
                        location.href = res.url;
                    } else {
                        location.reload();
                    }
                }, 1500);
            }
        } else {
            if (typeof res.data === 'undefined' ) {
                $.Notify.msg('无法解析的数据响应格式！', 'error');
            }else if (typeof res.data['input_error'] === 'undefined') {
                $.Notify.topMsg(res.msg, 'error');
            } else {
                $.Notify.msg(res.msg, 'error');
            }           
        }
    };

    /**
     * 公共返回错误操作
     * @param {json} data
     */
    Action.prototype.error = function (res, func) {
        layer.closeAll();
        this.isSubmit = false;
        $.Notify.msg('请求失败,服务端响应失败！');
    };

    /**
     * post 数据提交
     * @param {string} url
     * @param {string} query
     * @returns void
     */
    Action.prototype.ajaxPost = function (url, query) {
        var $this = this;
   
        // if ($this.isSubmit) {
        //     return false;
        // }
        if (typeof url !== 'string') {
            $this.curDom = url;
            var url = url.attr('href') || url.data('url') ;
        }
        
        // $this.isSubmit = true;
        $.Notify.loading('正在提交数据，请稍后...', 'warning', 200);  
        $.ajax({
            type     : 'POST',
            url      : url,
            data     : query,
            success  : $this.success,
            error    : $this.error
        });
        
        return false;
    };

    //get提交
    Action.prototype.ajaxGet =  function (elem) {
        var $this = this;
        $this.submit = true;

        if (typeof elem !== 'string') {
            $this.curDom = elem;
            var url     = elem.attr('href') || elem.data('url') ;
            var confirm = elem.data('confirm');
            var remove  = elem.data('remove');
        } else {
            var url = elem;
        }

        if(confirm === 'true' || typeof remove !== 'undefined') {      
            $.Notify.confirm('你确定要执行该操作？', function(){
                $.Notify.loading('正在提交数据，请稍后...', 'warning', 200);  
                $.ajax({
                    'type'    : 'GET',
                    'url'     : url,
                    'success' : $this.success,
                    'error'   : $this.error
                });
            });
            return false;
        }

        $.Notify.loading('正在提交数据，请稍后...', 'warning', 200);       
        $.ajax({
            type    : 'GET',
            url     : url,
            success : $this.success,
            error   : $this.error
        });
        return false;
    };

    /**
     * get 数据提交
     * @param {object} elem 提交的元素
     * @returns boolean
     */
    Action.prototype.ajaxDel    = function (elem, data) {
        var $this = this,
            url     = elem.attr('href') || elem.data('url'),
            remove  = elem.attr('data-remove'),
            text    = elem.attr('title');       

        if ($this.submit) {
            return false;
        }

        if (typeof(url) === 'undefined') {
            $.Notify.msg('请求地址不能为空！');
            return false;
        }

        if (typeof(data) === 'undefined') {
            data = {};
        }

        if (typeof(text) === 'undefined') {
            text = '这个数据'
        } else {
            text = '['+text+']'
        }

        $this.submit = true;

        $.Notify.confirm('你确定要删除<b class="text-danger">'+text+'</b>，有可能无法恢复', function(){
            $.Notify.loading('正在提交数据，请稍后...', 'warning', 200);  
            $.ajax({
                type   : 'GET',
                url    : url,
                data   : data,
                success: $this.success,
                error  : $this.error
            });
        });

        $this.submit = false;
        return false;
    };

    //post表单提交
    Action.prototype.postForm = function(form) {
        var url    = form.attr('action') || form.attr('href') || form.data('url');
        var query  = form.serialize();
        this.ajaxPost(url, query);
    };

    Action.prototype.formModal = function(dom, func) {
        $.Notify.loading();
        var $this = dom,$customModal = $('#custom-modal'),
            $customModalBody = $('#custom-modal-content'),
            $customModalTitle = $('#custom-modal-title'),
            url = $this.attr('href') || $this.data('url');
        if (typeof url == 'undefined') {
            $.Notify.msg('未指定加载的URL地址');
            return;
        }
        $.ajax({
            url : url, 
            type : 'GET',
            success : function (html) {
                var closeBtn = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                var $body = $(closeBtn + html.replace("/r/n", ""));
                var form  = $($body).find('form');
                form.find('.btn-return').attr('data-dismiss', 'modal')
                .removeClass('btn-return').text('关闭');
                $customModalBody.html($body)  
                           
                $.Notify.close();
                $customModal.modal('show');
                $.App.initIcheck();
                if (typeof func == 'function') {
                    func($this, form, $customModal);
                }
            },
            error : $.Action.error
        }); 
    };

    /* 设置表单的值 */
    Action.prototype.setValue = function(name, value){
        var first = name.substr(0,1), input, i = 0, val;
        if(value === "") return;
        if("#" === first || "." === first){
            input = $(name);
        } else {
            input = $("[name='" + name + "']");
        }

        if(input.eq(0).is(":radio")) { //单选按钮
            input.filter("[value='" + value + "']").each(function(){this.checked = true});
        } else if(input.eq(0).is(":checkbox")) { //复选框
            if(!$.isArray(value)){
                val = new Array();
                val[0] = value;
            } else {
                val = value;
            }
            for(i = 0, len = val.length; i < len; i++){
                input.filter("[value='" + val[i] + "']").each(function(){this.checked = true});
            }
        } else {  //其他表单选项直接设置值
            input.val(value);
        }
    };

    Action.prototype.init = function() {
        var $this = this;
    };

    $.Action = new Action, $.Action.Constructor = Action;

}(window.jQuery),  

//主体app模块
function($) {
    "use strict";
    var App = function() {
        this.VERSION = "2.0.0",
        this.AUTHOR = "JYmusic",
        this.SUPPORT = "jyuucn@gmail.com",
        this.pageScrollElement = "html, body",
        this.$body = $("body")
    };

    //页面加载执行
    App.prototype.onDocReady = function(e) {
        FastClick.attach(document.body);
        resizefunc.push("initscrolls");
        resizefunc.push("changeptype");
        $('.animate-number').each(function() {
            $(this).animateNumbers($(this).attr("data-value"), true, parseInt($(this).attr("data-duration")));
        });

        //RUN RESIZE ITEMS
        $(window).resize(debounce(resizeitems, 100));
        $("body").trigger("resize");
        // right side-bar toggle
        $('.right-bar-toggle').on('click', function(e) {
            e.preventDefault();
            $('#wrapper').toggleClass('right-bar-enabled');
        });

        //高亮右侧栏导航
        
        $('#sidebar-menu').find('a[href="'+JYconf.url+'"]').addClass('active');
    },

    App.prototype.initIcheck = function(e) {
        if ($('.i-checkboxs').size() > 0 || $('.i-radios').size() > 0 ) {
            $('.i-checkboxs, .i-radios').find('input').iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%'
            });
        }
    },

    //初始化
    App.prototype.init = function() {
        var $this = this;
        //页面加载试下
        $(document).ready($this.onDocReady);
        $this.initIcheck();
        //初始化侧边栏
        $.Sidemenu.init();
        //初始化全屏插件
        $.FullScreen.init();
        //$.Action.init();
    },
    $.App = new App, $.App.Constructor = App
}(window.jQuery),

function($) {
    "use strict";
    var Url = function() {
        this.DEEP = JYconf.pathinfo_depr,
        this.SUFFIX = JYconf.url_html_suffix
    };

    /**
     * 解析URL
     * @param  {string} url 被解析的URL
     * @return {object}     解析后的数据
     */
    Url.prototype.parse = function(url){

        var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
        
        parse || $.error("url格式不正确！");

        return {
            "scheme"   : parse[1],
            "host"     : parse[2],
            "port"     : parse[3],
            "path"     : parse[4],
            "query"    : parse[5],
            "fragment" : parse[6]
        };
    }
    Url.prototype.parseRoot = function(){
        var urls = JYconf.url.split("/");
        return '/' + urls[1];
    }

    //scheme://host:port/path?query#fragment
    Url.prototype.build = function(url, vars, suffix){
        
        var info = this.parse(url), root = this.parseRoot(), path = [], param = {}, reg;
        /* 验证info */
        info.path || $.error("url格式错误！");
        url = info.path;
        /* 去掉右侧分割符 */
        if("/" == url.substr(-1)){
            url = url.substr(0, url.length -1)
        }

        url = ("/" == this.DEEP) ? url.substr(1) : url.substr(1).replace(/\//g, this.DEEP);
        url = "/" + url ;
    
        /* 解析参数 */
        if(typeof vars === "string"){
            vars = this.parse_str(vars);
        } else if(!$.isPlainObject(vars)){
            vars = {};
        }
        

        /* 解析URL自带的参数 */
        info.query && $.extend(vars, this.parse_str(info.query));
        if($.param(vars)){
            url += "&" + $.param(vars);
        }
         
        url = root + url + '.' +this.SUFFIX;
        return url;
    }
    $.Url =  new Url, $.Url.Constructor = Url;

}(window.jQuery),

//initializing main application module
function($) {
    "use strict";
    $.App.init();
}(window.jQuery);

//this full screen
var toggle_fullscreen = function() {}

function executeFunctionByName(functionName, context /*, args */ ) {
    var args = [].slice.call(arguments).splice(2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(this, args);
}
var w, h, dw, dh;
var changeptype = function() {
    w = $(window).width();
    h = $(window).height();
    dw = $(document).width();
    dh = $(document).height();
    if (jQuery.browser.mobile === true) {
        $("body").addClass("mobile").removeClass("fixed-left");
    }
    if (!$("#wrapper").hasClass("forced")) {
        if (w > 990) {
            $("body").removeClass("smallscreen").addClass("widescreen");
            $("#wrapper").removeClass("enlarged");
        } else {
            $("body").removeClass("widescreen").addClass("smallscreen");
            $("#wrapper").addClass("enlarged");
            $(".left ul").removeAttr("style");
        }
        if ($("#wrapper").hasClass("enlarged") && $("body").hasClass("fixed-left")) {
            $("body").removeClass("fixed-left").addClass("fixed-left-void");
        } else if (!$("#wrapper").hasClass("enlarged") && $("body").hasClass("fixed-left-void")) {
            $("body").removeClass("fixed-left-void").addClass("fixed-left");
        }
    }
    toggle_slimscroll(".slimscrollleft");
}
var debounce = function(func, wait, immediate) {
    var timeout, result;
    return function() {
        var context = this,
            args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) result = func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) result = func.apply(context, args);
        return result;
    };
}

function resizeitems() {
    if ($.isArray(resizefunc)) {
        for (i = 0; i < resizefunc.length; i++) {
            window[resizefunc[i]]();
        }
    }
}

function initscrolls() {
    if (jQuery.browser.mobile !== true) {
        //SLIM SCROLL
        $('.slimscroller').slimscroll({
            height: 'auto',
            size: "5px"
        });
        $('.slimscrollleft').slimScroll({
            height: 'auto',
            position: 'right',
            size: "5px",
            color: '#dcdcdc',
            wheelStep: 5
        });
    }
}

function toggle_slimscroll(item) {
    if ($("#wrapper").hasClass("enlarged")) {
        $(item).css("overflow", "inherit").parent().css("overflow", "inherit");
        $(item).siblings(".slimScrollBar").css("visibility", "hidden");
    } else {
        $(item).css("overflow", "hidden").parent().css("overflow", "hidden");
        $(item).siblings(".slimScrollBar").css("visibility", "visible");
    }
}
var wow = new WOW({
    boxClass: 'wow', // animated element css class (default is wow)
    animateClass: 'animated', // animation css class (default is animated)
    offset: 50, // distance to the element when triggering the animation (default is 0)
    mobile: false // trigger animations on mobile devices (true is default)
});
wow.init();

function url (url) {
    return $.Url.build(url);
}

/*! iCheck v1.0.2 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
(function(f){function A(a,b,d){var c=a[0],g=/er/.test(d)?_indeterminate:/bl/.test(d)?n:k,e=d==_update?{checked:c[k],disabled:c[n],indeterminate:"true"==a.attr(_indeterminate)||"false"==a.attr(_determinate)}:c[g];if(/^(ch|di|in)/.test(d)&&!e)x(a,g);else if(/^(un|en|de)/.test(d)&&e)q(a,g);else if(d==_update)for(var f in e)e[f]?x(a,f,!0):q(a,f,!0);else if(!b||"toggle"==d){if(!b)a[_callback]("ifClicked");e?c[_type]!==r&&q(a,g):x(a,g)}}function x(a,b,d){var c=a[0],g=a.parent(),e=b==k,u=b==_indeterminate,
v=b==n,s=u?_determinate:e?y:"enabled",F=l(a,s+t(c[_type])),B=l(a,b+t(c[_type]));if(!0!==c[b]){if(!d&&b==k&&c[_type]==r&&c.name){var w=a.closest("form"),p='input[name="'+c.name+'"]',p=w.length?w.find(p):f(p);p.each(function(){this!==c&&f(this).data(m)&&q(f(this),b)})}u?(c[b]=!0,c[k]&&q(a,k,"force")):(d||(c[b]=!0),e&&c[_indeterminate]&&q(a,_indeterminate,!1));D(a,e,b,d)}c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"default");g[_add](B||l(a,b)||"");g.attr("role")&&!u&&g.attr("aria-"+(v?n:k),"true");
g[_remove](F||l(a,s)||"")}function q(a,b,d){var c=a[0],g=a.parent(),e=b==k,f=b==_indeterminate,m=b==n,s=f?_determinate:e?y:"enabled",q=l(a,s+t(c[_type])),r=l(a,b+t(c[_type]));if(!1!==c[b]){if(f||!d||"force"==d)c[b]=!1;D(a,e,s,d)}!c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"pointer");g[_remove](r||l(a,b)||"");g.attr("role")&&!f&&g.attr("aria-"+(m?n:k),"false");g[_add](q||l(a,s)||"")}function E(a,b){if(a.data(m)){a.parent().html(a.attr("style",a.data(m).s||""));if(b)a[_callback](b);a.off(".i").unwrap();
f(_label+'[for="'+a[0].id+'"]').add(a.closest(_label)).off(".i")}}function l(a,b,f){if(a.data(m))return a.data(m).o[b+(f?"":"Class")]}function t(a){return a.charAt(0).toUpperCase()+a.slice(1)}function D(a,b,f,c){if(!c){if(b)a[_callback]("ifToggled");a[_callback]("ifChanged")[_callback]("if"+t(f))}}var m="iCheck",C=m+"-helper",r="radio",k="checked",y="un"+k,n="disabled";_determinate="determinate";_indeterminate="in"+_determinate;_update="update";_type="type";_click="click";_touch="touchbegin.i touchend.i";
_add="addClass";_remove="removeClass";_callback="trigger";_label="label";_cursor="cursor";_mobile=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);f.fn[m]=function(a,b){var d='input[type="checkbox"], input[type="'+r+'"]',c=f(),g=function(a){a.each(function(){var a=f(this);c=a.is(d)?c.add(a):c.add(a.find(d))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(a))return a=a.toLowerCase(),g(this),c.each(function(){var c=
f(this);"destroy"==a?E(c,"ifDestroyed"):A(c,!0,a);f.isFunction(b)&&b()});if("object"!=typeof a&&a)return this;var e=f.extend({checkedClass:k,disabledClass:n,indeterminateClass:_indeterminate,labelHover:!0},a),l=e.handle,v=e.hoverClass||"hover",s=e.focusClass||"focus",t=e.activeClass||"active",B=!!e.labelHover,w=e.labelHoverClass||"hover",p=(""+e.increaseArea).replace("%","")|0;if("checkbox"==l||l==r)d='input[type="'+l+'"]';-50>p&&(p=-50);g(this);return c.each(function(){var a=f(this);E(a);var c=this,
b=c.id,g=-p+"%",d=100+2*p+"%",d={position:"absolute",top:g,left:g,display:"block",width:d,height:d,margin:0,padding:0,background:"#fff",border:0,opacity:0},g=_mobile?{position:"absolute",visibility:"hidden"}:p?d:{position:"absolute",opacity:0},l="checkbox"==c[_type]?e.checkboxClass||"icheckbox":e.radioClass||"i"+r,z=f(_label+'[for="'+b+'"]').add(a.closest(_label)),u=!!e.aria,y=m+"-"+Math.random().toString(36).substr(2,6),h='<div class="'+l+'" '+(u?'role="'+c[_type]+'" ':"");u&&z.each(function(){h+=
'aria-labelledby="';this.id?h+=this.id:(this.id=y,h+=y);h+='"'});h=a.wrap(h+"/>")[_callback]("ifCreated").parent().append(e.insert);d=f('<ins class="'+C+'"/>').css(d).appendTo(h);a.data(m,{o:e,s:a.attr("style")}).css(g);e.inheritClass&&h[_add](c.className||"");e.inheritID&&b&&h.attr("id",m+"-"+b);"static"==h.css("position")&&h.css("position","relative");A(a,!0,_update);if(z.length)z.on(_click+".i mouseover.i mouseout.i "+_touch,function(b){var d=b[_type],e=f(this);if(!c[n]){if(d==_click){if(f(b.target).is("a"))return;
A(a,!1,!0)}else B&&(/ut|nd/.test(d)?(h[_remove](v),e[_remove](w)):(h[_add](v),e[_add](w)));if(_mobile)b.stopPropagation();else return!1}});a.on(_click+".i focus.i blur.i keyup.i keydown.i keypress.i",function(b){var d=b[_type];b=b.keyCode;if(d==_click)return!1;if("keydown"==d&&32==b)return c[_type]==r&&c[k]||(c[k]?q(a,k):x(a,k)),!1;if("keyup"==d&&c[_type]==r)!c[k]&&x(a,k);else if(/us|ur/.test(d))h["blur"==d?_remove:_add](s)});d.on(_click+" mousedown mouseup mouseover mouseout "+_touch,function(b){var d=
b[_type],e=/wn|up/.test(d)?t:v;if(!c[n]){if(d==_click)A(a,!1,!0);else{if(/wn|er|in/.test(d))h[_add](e);else h[_remove](e+" "+t);if(z.length&&B&&e==v)z[/ut|nd/.test(d)?_remove:_add](w)}if(_mobile)b.stopPropagation();else return!1}})})}})(window.jQuery||window.Zepto);
