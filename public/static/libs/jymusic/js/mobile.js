/*设置cookie的键值对  $.cookie(’name’, ‘value’);设置cookie的键值对，有效期，路径，域，安全；$.cookie(’name,‘value’,{expires: 7, path: ‘/’, domain: ‘jquery.com’, secure: true});读取cookie的值  var account= $.cookie(’name’); 删除一个cookie  $.cookie(’name’, null); **/
(function($,document,undefined){var pluses=/\+/g;function raw(s){return s;}function decoded(s){return decodeURIComponent(s.replace(pluses,' '));}$.cookie=function(key,value,options){if(value!==undefined&&!/Object/.test(Object.prototype.toString.call(value))){options=$.extend({},$.cookie.defaults,options);if(value===null){options.expires=-1;}if(typeof options.expires==='number'){var days=options.expires,t=options.expires=new Date();t.setDate(t.getDate()+days);}value=String(value);return(document.cookie=[encodeURIComponent(key),'=',options.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''));}options=value||$.cookie.defaults||{};var decode=options.raw?raw:decoded;var cookies=document.cookie.split('; ');for(var i=0,parts;(parts=cookies[i]&&cookies[i].split('='));i++){if(decode(parts.shift())===key){return decode(parts.join('='));}}return null;};$.cookie.defaults={};$.removeCookie=function(key,options){if($.cookie(key,options)!==null){$.cookie(key,null,options);return true;}return false;};})(jQuery,document);
/*jQuery Storage API Plugin Project home: https://github.com/julien-maurel/jQuery-Storage-API Version: 1.9.4*/
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports?require("jquery"):jQuery)}(function(e){function t(){var t,r,i,o=this._type,n=arguments.length,s=window[o],a=arguments,f=a[0];if(1>n)throw new Error("Minimum 1 argument must be given");if(e.isArray(f)){r={};for(var l in f){t=f[l];try{r[t]=JSON.parse(s.getItem(t))}catch(c){r[t]=s.getItem(t)}}return r}if(1!=n){try{r=JSON.parse(s.getItem(f))}catch(c){throw new ReferenceError(f+" is not defined in this storage")}for(var l=1;n-1>l;l++)if(r=r[a[l]],void 0===r)throw new ReferenceError([].slice.call(a,1,l+1).join(".")+" is not defined in this storage");if(e.isArray(a[l])){i=r,r={};for(var u in a[l])r[a[l][u]]=i[a[l][u]];return r}return r[a[l]]}try{return JSON.parse(s.getItem(f))}catch(c){return s.getItem(f)}}function r(){var t,r,i,o=this._type,n=arguments.length,s=window[o],a=arguments,f=a[0],l=a[1],c=isNaN(l)?{}:[];if(1>n||!e.isPlainObject(f)&&2>n)throw new Error("Minimum 2 arguments must be given or first parameter must be an object");if(e.isPlainObject(f)){for(var u in f)t=f[u],e.isPlainObject(t)||this.alwaysUseJson?s.setItem(u,JSON.stringify(t)):s.setItem(u,t);return f}if(2==n)return"object"==typeof l||this.alwaysUseJson?s.setItem(f,JSON.stringify(l)):s.setItem(f,l),l;try{i=s.getItem(f),null!=i&&(c=JSON.parse(i))}catch(h){}i=c;for(var u=1;n-2>u;u++)t=a[u],r=isNaN(a[u+1])?"object":"array",(!i[t]||"object"==r&&!e.isPlainObject(i[t])||"array"==r&&!e.isArray(i[t]))&&("array"==r?i[t]=[]:i[t]={}),i=i[t];return i[a[u]]=a[u+1],s.setItem(f,JSON.stringify(c)),c}function i(){var t,r,i=this._type,o=arguments.length,n=window[i],s=arguments,a=s[0];if(1>o)throw new Error("Minimum 1 argument must be given");if(e.isArray(a)){for(var f in a)n.removeItem(a[f]);return!0}if(1==o)return n.removeItem(a),!0;try{t=r=JSON.parse(n.getItem(a))}catch(l){throw new ReferenceError(a+" is not defined in this storage")}for(var f=1;o-1>f;f++)if(r=r[s[f]],void 0===r)throw new ReferenceError([].slice.call(s,1,f).join(".")+" is not defined in this storage");if(e.isArray(s[f]))for(var c in s[f])delete r[s[f][c]];else delete r[s[f]];return n.setItem(a,JSON.stringify(t)),!0}function o(t){var r=a.call(this);for(var o in r)i.call(this,r[o]);if(t)for(var o in e.namespaceStorages)f(o)}function n(){var r=arguments.length,i=arguments,o=i[0];if(0==r)return 0==a.call(this).length;if(e.isArray(o)){for(var s=0;s<o.length;s++)if(!n.call(this,o[s]))return!1;return!0}try{var f=t.apply(this,arguments);e.isArray(i[r-1])||(f={totest:f});for(var s in f)if(!(e.isPlainObject(f[s])&&e.isEmptyObject(f[s])||e.isArray(f[s])&&!f[s].length)&&f[s])return!1;return!0}catch(l){return!0}}function s(){var r=arguments.length,i=arguments,o=i[0];if(1>r)throw new Error("Minimum 1 argument must be given");if(e.isArray(o)){for(var n=0;n<o.length;n++)if(!s.call(this,o[n]))return!1;return!0}try{var a=t.apply(this,arguments);e.isArray(i[r-1])||(a={totest:a});for(var n in a)if(void 0===a[n]||null===a[n])return!1;return!0}catch(f){return!1}}function a(){var e=this._type,r=arguments.length,i=window[e],o=arguments,n=[],s={};if(s=r>0?t.apply(this,o):i,s&&s._cookie)for(var a in Cookies.get())""!=a&&n.push(a.replace(s._prefix,""));else for(var f in s)s.hasOwnProperty(f)&&n.push(f);return n}function f(t){if(!t||"string"!=typeof t)throw new Error("First parameter must be a string");h?(window.localStorage.getItem(t)||window.localStorage.setItem(t,"{}"),window.sessionStorage.getItem(t)||window.sessionStorage.setItem(t,"{}")):(window.localCookieStorage.getItem(t)||window.localCookieStorage.setItem(t,"{}"),window.sessionCookieStorage.getItem(t)||window.sessionCookieStorage.setItem(t,"{}"));var r={localStorage:e.extend({},e.localStorage,{_ns:t}),sessionStorage:e.extend({},e.sessionStorage,{_ns:t})};return"undefined"!=typeof Cookies&&(window.cookieStorage.getItem(t)||window.cookieStorage.setItem(t,"{}"),r.cookieStorage=e.extend({},e.cookieStorage,{_ns:t})),e.namespaceStorages[t]=r,r}function l(e){var t="jsapi";try{return window[e]?(window[e].setItem(t,t),window[e].removeItem(t),!0):!1}catch(r){return!1}}var c="ls_",u="ss_",h=l("localStorage"),g={_type:"",_ns:"",_callMethod:function(e,t){var r=[],t=Array.prototype.slice.call(t),i=t[0];return this._ns&&r.push(this._ns),"string"==typeof i&&-1!==i.indexOf(".")&&(t.shift(),[].unshift.apply(t,i.split("."))),[].push.apply(r,t),e.apply(this,r)},alwaysUseJson:!1,get:function(){return this._callMethod(t,arguments)},set:function(){var t=arguments.length,i=arguments,o=i[0];if(1>t||!e.isPlainObject(o)&&2>t)throw new Error("Minimum 2 arguments must be given or first parameter must be an object");if(e.isPlainObject(o)&&this._ns){for(var n in o)this._callMethod(r,[n,o[n]]);return o}var s=this._callMethod(r,i);return this._ns?s[o.split(".")[0]]:s},remove:function(){if(arguments.length<1)throw new Error("Minimum 1 argument must be given");return this._callMethod(i,arguments)},removeAll:function(e){return this._ns?(this._callMethod(r,[{}]),!0):this._callMethod(o,[e])},isEmpty:function(){return this._callMethod(n,arguments)},isSet:function(){if(arguments.length<1)throw new Error("Minimum 1 argument must be given");return this._callMethod(s,arguments)},keys:function(){return this._callMethod(a,arguments)}};if("undefined"!=typeof Cookies){window.name||(window.name=Math.floor(1e8*Math.random()));var m={_cookie:!0,_prefix:"",_expires:null,_path:null,_domain:null,setItem:function(e,t){Cookies.set(this._prefix+e,t,{expires:this._expires,path:this._path,domain:this._domain})},getItem:function(e){return Cookies.get(this._prefix+e)},removeItem:function(e){return Cookies.remove(this._prefix+e,{path:this._path})},clear:function(){for(var e in Cookies.get())""!=e&&(!this._prefix&&-1===e.indexOf(c)&&-1===e.indexOf(u)||this._prefix&&0===e.indexOf(this._prefix))&&Cookies.remove(e)},setExpires:function(e){return this._expires=e,this},setPath:function(e){return this._path=e,this},setDomain:function(e){return this._domain=e,this},setConf:function(e){return e.path&&(this._path=e.path),e.domain&&(this._domain=e.domain),e.expires&&(this._expires=e.expires),this},setDefaultConf:function(){this._path=this._domain=this._expires=null}};h||(window.localCookieStorage=e.extend({},m,{_prefix:c,_expires:3650}),window.sessionCookieStorage=e.extend({},m,{_prefix:u+window.name+"_"})),window.cookieStorage=e.extend({},m),e.cookieStorage=e.extend({},g,{_type:"cookieStorage",setExpires:function(e){return window.cookieStorage.setExpires(e),this},setPath:function(e){return window.cookieStorage.setPath(e),this},setDomain:function(e){return window.cookieStorage.setDomain(e),this},setConf:function(e){return window.cookieStorage.setConf(e),this},setDefaultConf:function(){return window.cookieStorage.setDefaultConf(),this}})}e.initNamespaceStorage=function(e){return f(e)},h?(e.localStorage=e.extend({},g,{_type:"localStorage"}),e.sessionStorage=e.extend({},g,{_type:"sessionStorage"})):(e.localStorage=e.extend({},g,{_type:"localCookieStorage"}),e.sessionStorage=e.extend({},g,{_type:"sessionCookieStorage"})),e.namespaceStorages={},e.removeAllStorages=function(t){e.localStorage.removeAll(t),e.sessionStorage.removeAll(t),e.cookieStorage&&e.cookieStorage.removeAll(t),t||(e.namespaceStorages={})},e.alwaysUseJsonInStorage=function(t){g.alwaysUseJson=t,e.localStorage.alwaysUseJson=t,e.sessionStorage.alwaysUseJson=t,e.cookieStorage&&(e.cookieStorage.alwaysUseJson=t)}});
+function($) {
    'use strict';
    /**
     * 获取基础配置
     * @type {object}
     */
    var JY = window.JY;

    /* 基础对象检测 */
    JY || $.error("基础配置没有正确加载！");

    //scheme://host:port/path?query#fragment
    JY.U = function(url, vars, suffix) {
        return $.Url.build(url, vars, suffix);
    };

    JY.tipMsg = function(msg, type, time) {
        var timer = $.type(time) === 'number' ? time * 1000 : 3000;
        var setting = { time: timer };
        if (type === 1 || type === 'success') {
            setting.icon = 1;
        } else if (type === 2 || type === 'waring') {
            setting.icon = 0;
        } else if (type === 3 || type === 'error') {
            setting.icon = 2;
        } else if (type === 4 || type === 'info') {
            setting.icon = 3;
        }

        layer.msg(msg, setting);
    };

    /*cookie操作*/
    JY.getCookie = function(name) {
        var val = $.cookie(name);
        if (val != null && 0 === val.indexOf("jy")) {
            val = decodeURIComponent(val).substr(3);
            if (0 === val.indexOf("[")) {
                val = val.replace('["', '').replace('"]', '');
                return val.split('","');
            } else {
                return $.parseJSON(val);
            }
        } else {
            return val;
        }
    };

    JY.delCookie = function(name, val, time) {
        $.cookie(name, null);
    };

    JY.setCookie = function(name, val, time) {
        var $cookie = "";
        if ($.isPlainObject(val)) {
            for (var item in val) {
                $cookie += ',"' + item + '":"' + val[item] + '"';
            }
            $cookie = "jy:{ " + $cookie.substr(1) + " }";
        } else if ($.isArray(val)) {
            $cookie = 'jy:[' + val.join(",") + ']';
        } else {
            $cookie = val;
        }
        if ($.type(time) != "number") {
            $.cookie(name, $cookie, { path: '/' });
        } else {
            $.cookie(name, $cookie, { expires: time, path: '/' });
        }
    };
}(jQuery),

//主体app模块
function($) {
    "use strict";
    var App = function() {
        this.VERSION = "1.0.0",
        this.AUTHOR = "JYmusic",
        this.SUPPORT = "jyuucn@gmail.com",
        this.isLogin = false,
        this.pageScrollElement = "html, body",
        this.$body = $("body")
    };
    var loaded = [],
	promise = false,
	deferred = $.Deferred();

	/**
	 * Chain loads the given sources
	 * @param srcs array, script or css
	 * @returns {*} Promise that will be resolved once the sources has been loaded.
	 */
	App.prototype.load = function (srcs) {
		srcs = $.isArray(srcs) ? srcs : srcs.split(/\s+/);
		if(!promise){
			promise = deferred.promise();
		}

		$.each(srcs, function(index, src) {
			promise = promise.then( function(){
				return loaded[src] ? loaded[src].promise() : (src.indexOf('.css') >=0 ? loadCSS(src) : loadScript(src));
			} );
		});
		deferred.resolve();
		return promise;
	};

	App.prototype.remove = function(srcs){
		srcs = $.isArray(srcs) ? srcs : srcs.split(/\s+/);
		$.each(srcs, function(index, src) {
			src.indexOf('.css') >=0 ? $('link[href="'+src+'"]').remove() : $('script[src="'+src+'"]').remove();
			delete loaded[src];
		});
	};

	/**
	 * Dynamically loads the given script
	 * @param src The url of the script to load dynamically
	 * @returns {*} Promise that will be resolved once the script has been loaded.
	 */
	var loadScript = function (src) {
		var deferred = $.Deferred();
		var script = document.createElement('script');
		script.src = src;
		script.onload = function (e) {
			deferred.resolve(e);
		};
		script.onerror = function (e) {
			deferred.reject(e);
		};
		document.body.appendChild(script);
		loaded[src] = deferred;
		return deferred.promise();
	};

	/**
	 * Dynamically loads the given CSS file
	 * @param href The url of the CSS to load dynamically
	 * @returns {*} Promise that will be resolved once the CSS file has been loaded.
	 */
	var loadCSS = function (href) {
		var deferred = $.Deferred();
		var style = document.createElement('link');
		style.rel = 'stylesheet';
		style.type = 'text/css';
		style.href = href;
		style.onload = function (e) {
			deferred.resolve(e);
		};
		style.onerror = function (e) {
			deferred.reject(e);
		};
		document.head.appendChild(style);
		loaded[href] = deferred;

		return deferred.promise();
	};

    App.prototype.testPwd = function(dom){
        var width = dom.width();

        var html =  '<div id="password-level" width="'+width+'px" class="pw-strength">'+
                '<span class="weak">弱</span>'+
                '<span class="medium">中</span>'+
                '<span class="strong">强</span>'+
            '</div>';

        dom.keyup(function () {
            var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
            var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
            var enoughRegex = new RegExp("(?=.{6,}).*", "g");
            var $pwdlevel = $('#password-level');
            if ($pwdlevel.length < 1) {
                dom.after(html);
            }
            if (false == enoughRegex.test($(this).val())) {
                $pwdlevel.removeClass('pw-weak')
                $pwdlevel.removeClass('pw-medium')
                $pwdlevel.removeClass('pw-strong');
                $pwdlevel.addClass('pw-defule');
                 //密码小于六位的时候，密码强度图片都为灰色
            } else if (strongRegex.test($(this).val())) {
                $pwdlevel.removeClass('pw-weak')
                $pwdlevel.removeClass('pw-medium')
                $pwdlevel.removeClass('pw-strong');
                $pwdlevel.addClass(' pw-strong');
                 //密码为八位及以上并且字母数字特殊字符三项都包括,强度最强
            } else if (mediumRegex.test($(this).val())) {
                $pwdlevel.removeClass('pw-weak')
                $pwdlevel.removeClass('pw-medium')
                $pwdlevel.removeClass('pw-strong');
                $pwdlevel.addClass(' pw-medium');
                 //密码为七位及以上并且字母、数字、特殊字符三项中有两项，强度是中等
            } else {
                $pwdlevel.removeClass('pw-weak')
                $pwdlevel.removeClass('pw-medium')
                $pwdlevel.removeClass('pw-strong');
                $pwdlevel.addClass('pw-weak');
                 //如果密码为6为及以下，就算字母、数字、特殊字符三项都包括，强度也是弱的
            }
            return true;
        });
    };

    App.prototype.activeUser = function(callback){
        //查看是否已经请求
        var self = this;


        if ($.cookie('dotcom_user') && $.localStorage.isSet('active_user')) {
            self.isLogin = true;
            if ($.isFunction(callback)) {
               callback($.localStorage.get('active_user'));
            }
            return ;
        }

        $.ajax({
            type    : 'GET',
            url     : JY['user_active_url'],
            dataType: "jsonp",
            success : function(res){
                console.log(res);
                if (res.code == 0) {
                    self.isLogin = true;
                    var user = res.result;
                    $.localStorage.set('active_user', res);
                    $.cookie('dotcom_user', user.nickname);
                } else {
                    self.isLogin = false;
                    if ($.cookie('dotcom_user') && $.localStorage.isSet('active_user')) {
                        $.localStorage.remove('active_user');
                    }
                }
                console.log($.isFunction(callback));
                if ($.isFunction(callback)) {
                    callback(res);
                }
            }
        });
    };

    App.prototype.logout = function(callback){
        //查看是否已经请求
        if (!$.cookie('dotcom_user') || !$.localStorage.isSet('active_user')) {
            $.Notify.msg('成功退出登录', 'success');
            self.isLogin = false;
            if ($.isFunction(callback)) {
               callback({code : 0});
            }
            return ;
        }

        $.ajax({
            type    : 'GET',
            url     : $.Url.build('/user/logout'),
            dataType: "jsonp",
            success : function(res){
                if ($.isFunction(callback)) {
                    callback(res);
                }
                if (res.code == 0) {
                    self.isLogin = false;
                    $.localStorage.remove('active_user');
                    $.cookie('dotcom_user', null);
                    $.Notify.msg('成功退出登录', 'success');
                }
            }
        });
    };

    //页面加载执行
    App.prototype.onDocReady = function(e) {
        //加载弹窗组件
        var layerJs = JY.public + "/static/libs/layer/layer.js";
        $.App.load(layerJs);
    },

    //初始化
    App.prototype.init = function() {
        var $this = this;
        //页面加载试下
        $(document).ready($this.onDocReady);
    },
    $.App = new App, $.App.Constructor = App;
}(window.jQuery),

/**Url处理模块**/
function($) {
    "use strict";
    var Url = function() {
        this.deep = JY.deep,
        this.model = parseInt(JY.model);
    };

    /**
     * 解析URL
     * @param  {string} url 被解析的URL
     * @return {object}     解析后的数据
     */
    Url.prototype.parse_url = function(url) {
        var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
        parse || $.error("url格式不正确！");
        return {
            "scheme": parse[1],
            "host": parse[2],
            "port": parse[3],
            "path": parse[4],
            "query": parse[5],
            "fragment": parse[6]
        };
    };

    Url.prototype.parse_str = function(str) {
        var value = str.split("&"),
            val, vars = {},
            param;
        for (val in value) {
            param = value[val].split("=");
            vars[param[0]] = param[1];
        }
        return vars;
    };

    Url.prototype.get_domain = function (url) {
        var suffix = this.model[1];
        if (url.indexOf("user") === 0) {
            var userUrl = JY.domains.user;
            if (userUrl.indexOf(suffix)) {
                userUrl = userUrl.replace('user.' + suffix, "");
            }
            return userUrl;
        }

        if (url.indexOf("api") === 0) {
            var apiUrl = JY.domains.api;
            if (apiUrl.indexOf(suffix)) {
                apiUrl = apiUrl.replace('api.' + suffix, "");
            }
            return apiUrl;
        }

        if (url.indexOf("article") === 0) {
            var articleUrl = JY.domains.article;
            if (articleUrl.indexOf(suffix)) {
                articleUrl = articleUrl.replace('article.' + suffix, "");
            }
            return articleUrl;
        }

        return JY.domains.home;
    };

    Url.prototype.build = function(url, vars, suffix) {
        var info = this.parse_url(url);
        /* 验证info */
        info.path || $.error("url格式错误！");
        url = info.path;

        /* 组装URL */
        if (0 === url.indexOf("/")) { //路由模式
            this.model[0] === 0 && $.error("该URL模式不支持使用路由!(" + url + ")");
            /* 去掉右侧分割符 */
            if ("/" === url.substr(-1)) {
                url = url.substr(0, url.length - 1)
            }
            url = ("/" === this.deep) ? url.substr(1) : url.substr(1).replace(/\//g, this.deep);
        } else {
            if ("/" === url.substr(-1)) {
                url = url.substr(0, url.length - 1)
            }
        }
        /* 解析参数 */
        if (typeof vars === "string") {
            vars = this.parse_str(vars);
        } else if (!$.isPlainObject(vars)) {
            vars = {};
        }

        /* 解析URL自带的参数 */
        info.query && $.extend(vars, this.parse_str(info.query));
        if ($.param(vars)) {
            url += "&" + $.param(vars);
        }
        url = url.replace(/(\w+=&)|(&?\w+=$)/g, "").replace(/[&=]/g, JY.deep);
        url = url.toLowerCase();
        var domain = this.get_domain(url);
        /* 添加伪静态后缀 */
        if (false !== suffix) {
            suffix = suffix || this.model[1];
            if (suffix) {
                url += "." + suffix;
            }
        }
        return domain + url;
    };

    $.Url =  new Url, $.Url.Constructor = Url;

}(jQuery),


//初始化
$.App.init();
