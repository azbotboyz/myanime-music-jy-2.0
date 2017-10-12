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
        $.Notify.confirm('Are you sure you want to log out?', function(){
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
	        $.Notify.msg('Please select at least one');
	        return false;
	    }

	    var tip = $this.data('tip');

	    if (typeof(tip) == 'undefined' || tip == '') {
	    	if ($this.hasClass('disable-batch')) {
				tip = 'Are you sure you want to batch disable?';
	    	} else if($this.hasClass('delete-batch')) {
				tip = 'Are you sure you want to delete these data in batches and that the deletion may not be recoverable?';
	    	} else if($this.hasClass('clear-batch')) {
                tip = 'Are you sure you want to empty the data and will not be able to recover after emptying?';
            } else {
	    		tip = 'Are you sure you want to do this?';
	    	}
	    }

    	$.Notify.confirm(tip, function(){
            $.Notify.loading('Please submit the data later please...', 'warning', 200);  
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
        layer.msg('The page will automatically jump');
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
