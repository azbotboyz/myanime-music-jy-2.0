JY.page = 2;
$(function () {
	$('#list img').click(function (e) {
		if($('#disNav').is(':hidden')){
			$('#disNav').slideDown();
		}else{
			$('#disNav').slideUp();
		}
	});
	$(window).manhuatoTop({
		showHeight : 400,//设置滚动高度时显示
		speed : 500 //返回顶部的速度以毫秒为单位
	});
		
	$('.show_tab').click(function(e){
		e.preventDefault();
		var that = $(this);
		var tab = $(that.attr('href'));
		if (!tab.hasClass('active')){
			that.addClass('active');
			that.siblings('.active').removeClass('active');
			tab.siblings('.in').removeClass('in').hide();
			tab.addClass('in').fadeIn("norma");
		}
	
	})
	/*加载更多*/
	$('#load-more').click(function(e){
		var that	= $(this);
		var opts	= {};
		var url 	= that.data('url');
        var type 	= that.data('type');
		var order	= that.data('order');

		if ($.type(order) === "string"){
			opts.order = order;
		} 
		var limit	= that.data('limit');
		if ($.type(limit) !== 'undefined'){
			opts.limit =  parseInt(limit) ;
		}

		opts.page	= JY.page;
		that.addClass('loading').text('正在努力加载...');

		$.get(url, opts, function(res){
			if (res.code === 0) {
				JY.page = ++JY.page;
				var html	=  '';
				if (type === 'artist'){
					html	= createArtist(res.result)
				}else if (type === 'album'){
					html	= createAlbum(res.result)
				}else{
					html	= createSong(res.result);
				}
				that.before(html);
				that.removeClass('loading').text('点击查看更多');
			}else{
				that.removeClass('loading').text('亲，没有更多了');
				setTimeout(function(){that.remove()},2000);				
			}
			
		})
		
	})
	
	function createSong(list){
		var html 	= '';
		var len		= list.length;
		for (var i=0; i < len; i++){
			$v	= list[i];
			 html += '<li>'+
				'<span class="numb"></span>'+
				'<a class="sname text-ellipsis" title="'+$v['name']+'" href="'+$v['url']+'">'+$v['name']+'</a>'+
				'<a class="gname text-ellipsis" title="'+$v['name']+'" href="'+$v['artist_url']+'">'+$v['artist_name']+'</a>'+
				'<a class="play" title="播放'+$v['name']+'" href="'+$v['url']+'"></a>'+		
			'</li>';
		}
		return html
	}
	function createArtist(list){
		var html 	= '';
		var len		= list.length;
		for (var i=0; i < len; i++){
			$v	= list[i];
			html += '<div class="ar_con">'+
			'	<a href="'+$v['url']+'">'+
			'		<img  src="'+$v['cover_url']+'">'+
			'	</a>'+
			'	<a class="ar_info" href="'+$v['url']+'">'+$v['name']+'</a>'+	
			'</div>';
		}
		return html;		
	}
	
	function createAlbum(list){
		var html 	= '';
		var len		= list.length;
		for (var i=0; i < len; i++){
			$v	= list[i];
			html +='<div class="tm_div al_ls">'+
				'	<div class="tm_pro">'+
				'		<a href="'+$v['url']+'"><img class="tm_img"  src="'+$v['cover_url']+'"></a>'+
				'		<div class="tm_title">'+
				'			<div class="tm_title_bg"></div>'+
				'			<div class="tm_title_con">'+
				'				<div class="tm_title_con_1"><a href="'+$v['url']+'">'+$v['name']+'</a></div>'+
				'			</div>'+
				'		</div>'+
				'	</div>'+
				// '	<div class="tm_price">'+
				// '		<a href="'+$v['artist_url']+'">'+$v['artist_name']+'</a>'+
				// '	</div>'+
				'</div>';
		}
		return html;
	}

})
$.fn.manhuatoTop = function(options) {
	var defaults = {			
		showHeight : 150,
		speed : 1000
	};
	var options = $.extend(defaults,options);
	$("body").prepend("<div id='totop'><a></a></div>");
	var $toTop = $(this);
	var $top = $("#totop");
	var $ta = $("#totop a");
	$toTop.scroll(function(){
		var scrolltop=$(this).scrollTop();		
		if(scrolltop>=options.showHeight){				
			$top.show();
		}
		else{
			$top.hide();
		}
	});	
	$ta.hover(function(){ 		
		$(this).addClass("cur");	
	},function(){			
		$(this).removeClass("cur");		
	});	
	$top.click(function(){
		$("html,body").animate({scrollTop: 0}, options.speed);	
	});
}
