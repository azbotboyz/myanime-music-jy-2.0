$(function($){
	//ajax退出登录   
	$('#user-login-out').click(function () {
        $.ajax({
            type    : 'GET',
            url     : $.Url.build('/user/logout'),
            dataType: "jsonp",
            success : function(res){
                if (res.code == 0) {
                    self.isLogin = false;
                    $.localStorage.remove('active_user');
                    $.cookie('dotcom_user', null);
                    $.Notify.msg('Successfully log out, the page will automatically jump~', 'success');
                    setTimeout(function(){
                        location.href=res.url;
                    },1500);
                } else {
                    $.Notify.msg('Exit login failed');
                }
            }
        });

	});

	$('.ajax-post').click(function(){
		var target,query,form;
		var target_form = $(that).attr('target-form');       
		var nead_confirm=false;
		if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
			form = $(target_form);                   
			if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
				form = $('.hide-data');
				query = form.serialize();            	
			}else if (form.get(0)==undefined){
				return false;
			}else if ( form.get(0).nodeName=='FORM' ){            	
				if ( $(this).hasClass('confirm') ) {
					if(!confirm('Are you sure you want to do this??')){
						return false;
					}
				}
				if($(this).attr('url') !== undefined){
					target = $(this).attr('url');

				}else{
					target = form.get(0).action;
				}                 
				query = form.serialize();
			}else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {                
				form.each(function(k,v){
					if(v.type=='checkbox' && v.checked==true){
						nead_confirm = true;
					}
				})
				if ( nead_confirm && $(this).hasClass('confirm') ) {
					if(!confirm('Are you sure you want to do this??')){
						return false;
					}
				}
				query = form.serialize();
			}else{
				if ( $(this).hasClass('confirm') ) {
					if(!confirm('Are you sure you want to do this??')){
						return false;
					}
				}
				query = form.find('input,select,textarea').serialize();
			}
			$(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
			$.post(target,query).success(function(data){
				if (data.code == 0) {
					if (data.url) {
						infoAlert(data.msg + ' The page is about to jump automatically~',true);
					}else{
						infoAlert(data.msg,true);
					}
				}else{
					infoAlert(data.error);
				}
				setTimeout(function(){
					$(that).removeClass('disabled').prop('disabled',false);
					if (data.url) {
						location.href=data.url;
					}
				},1500);
			});
		}
		return false;
    });

    $(document).on('click', "[data-action=fav]", function(){
        var $this = $(this);
        var id = $this.data('id');
        var type = $this.data('type');
        if (typeof type === 'undefined' || type == 'song') {
            type = 'songs'
        }

        if (!parseInt(id)) {
            $.Notify.msg('Invalid parameter! ');
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

    $(document).on('click', "[data-action=follow]", function(){
        var $this = $(this);
        var uid = $this.data('uid');
        var type = $this.data('type');

        if (!parseInt(uid)) {
            $.Notify.msg('Invalid parameter! ');
        }

        $.Action.ajaxPost($.Url.build('/api/actions/follow'), {follow_uid : uid}, function(res){
        	if (res.code == 0) {
	            if (res.remove) {
	            	$('#btn-unfollow').hide();
			        $('#btn-follow').show();
			    } else {
			    	$('#btn-follow').hide();
			        $('#btn-unfollow').show();
			    }
        	}
            return false;
        });     
    });
})

$.validator.setDefaults({
	submitHandler: function(form) {
		postForm(form);
		return false;		
	}
});

/*表单提交*/
function postForm(form) {
	var form 	= $(form);
	var target  = form.attr('action');
	var btn		= $(form).find('button');
	var	query 	= form.serialize();

	btn.addClass('disabled').attr('autocomplete','off').prop('disabled',true);
	$.Action.ajaxPost(target, query, function(res){
		if (res.code == 0) {
			setTimeout(function(){
				btn.removeClass('disabled').prop('disabled',false);
				if (res.url) {
					location.href=data.url;
				} else {
					location.reload();
				}
			},1500);
		} else {
			setTimeout(function(){
				btn.removeClass('disabled').prop('disabled', false);		
				if(res.token) {
					$('input[name=__token__]').val(res.token);
				} else {
					location.reload();
				}
			},1500);
		}
	});
	return false;
}

/*重新封装便于调用*/
function U (str){
	return JY.U(str);	
}


/*重新封装便于调用*/
function infoAlert (text,type) {
	JY.tipMsg (text,type);
}