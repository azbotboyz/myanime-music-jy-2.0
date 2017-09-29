/**
 * page.js (http://jyuu.cn/)
 *
 * JYmusic 页面js
 *
 **/
$(document).ready(function($) {
	//表单提交  
    $(document).on('click', '.ajax-post-form', function(e){
        e.preventDefault();
        var target = $(this).attr('target-form'), form;

        if ( typeof(target) !== 'undefined') {
            form = $('.' + $(this).attr('target-form'));
        } else if($(this).get(0).nodeName=='FORM') {
            form = $(this);
        } else {
            form = $(this).parents('form');
        }
        $.Action.postForm(form);
        return false;
    });

	//退出登录
	$(document).on('click', '#logout-action', function(e){
        e.preventDefault();
        $.Notify.confirm('你确定要退出登录？', function(){
        	$.Action.ajaxGet(url('/user/logout'));
        });
    });

    $(document).on('click', '#test-desktop', function(e){
        e.preventDefault();      
    });

    //全局搜索功能
	$("#search-action").click(function(){
		var url = $(this).attr('url');
        var query  = $('#searchbox').find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
        query = query.replace(/^&/g,'');
        if( url.indexOf('?') > 0){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
		window.location.href = url;
	});

    //回车自动提交
    $('#searchbox').find('input').keyup(function(event){
        if(event.keyCode===13){
            $("#search").click();
        }
    }); 

    //全选反选
    $(document).on('click', '.check-btn', function(e) {
    	e.preventDefault();
    	var inputs = $($(this).data('target'));
    	inputs.each(function(index, val) {
    		$(this).prop('checked', !$(this).prop('checked'));
    	});
    	$(this).toggleClass('active');
    });

    //批量禁用
    $(document).on('click', '.disable-batch,.delete-batch,.clear-batch,.success-batch', function(e) {
    	e.preventDefault();
    	var $this = $(this);
    	var inputs  = $($this.data('target') + ':checked');
        
	    if (inputs.size() < 1) {
	        $.Notify.msg('请至少选中一个');
	        return false;
	    }

	    var tip = $this.data('tip');

	    if (typeof(tip) == 'undefined' || tip == '') {
	    	if ($this.hasClass('disable-batch')) {
				tip = '你确定要批量禁用？';
	    	} else if($this.hasClass('delete-batch')) {
				tip = '你确定要批量删除这些数据，删除有可能将无法恢复？';
	    	} else if($this.hasClass('clear-batch')) {
                tip = '你确定要清空这些数据，清空后将无法恢复？';
            } else {
	    		tip = '你确定要执行这个操作？';
	    	}
	    }

    	$.Notify.confirm(tip, function(){
            $.Notify.loading('正在提交数据，请稍后...', 'warning', 200);  
            $.ajax({
                type   : 'GET',
                url    : $this.data('url'),
                data   : inputs.serialize(),
                success : $.Action.success
            });
        });
        return false;
    });

    $(document).on('click', '.delete-btn,.ajax-del', function(e) {
    	e.preventDefault();
		$.Action.ajaxDel($(this));
    });

    $(document).on('click', '.ajax-get', function(e) {
        e.preventDefault();
        $.Action.ajaxGet($(this));
    });


    //表单提交
    $(document).on('submit', '.form-submit', function(e) {
    	e.preventDefault();
		$.Action.postForm($(this));
    });

    $(document).on('click', '.ajax-form', function(e) {
    	e.preventDefault();
    	var $this = $(this), form,
    		target = $this.data('target');
    	
    	if (typeof target !== 'undefined') {
			form = $(target);
    	} else {
    		form = $this.parents('form');
    	}

		$.Action.postForm(form);
    });

    $('#test-desktop').click(function(event) {
        layer.msg('页面将自动跳转');
    });

    var $customModal = $('#custom-modal'),
        $customModalBody = $('#custom-modal-content');
    $(document).on('click', '.open-form-modal', function (e) {
        e.preventDefault();
        $.Action.formModal($(this));
    });

    $(document).on('click', '.open-find-modal', function (e) {
        e.preventDefault();
        var $this = $(this);
        var url = $this.attr('href') || $this.data('url');
        $.ajax({
            url : url, 
            type : 'GET',
            success : function (html) {
                var closeBtn = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                var $body = $(closeBtn + html.replace("/r/n", ""));
                $customModalBody.html($body)           
                $.Notify.close();
                $customModal.modal('show');
            },
            error : $.Action.error
        });
    });

    $(document).on('click', '.btn-return', function(){
        javascript:history.back(-1);
        return false;
    });
});
