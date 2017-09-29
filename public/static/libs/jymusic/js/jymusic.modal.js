! function($) {
    "use strict";
    var UploadModal = function() {
        this.isMove  = true;
        this.width = 400,
        this.height = 120,
        this.dom = $('#upload-modal'),
        this.filesContent = $('#table-files'),
        this.content = $('#upload-modal-content')
    };

    UploadModal.prototype.show = function() {
        var $this = this;
        $this.content.css({
            "width": $this.width,
            "height": $this.height,
            "left": ($(document).width() - parseInt($this.width))/2,
            "top": ($(window).height() - parseInt($this.height))/2,
        });

        $this.dom.fadeIn('400');
    },

    //menu item click
    UploadModal.prototype.close = function(e) {
        this.dom.fadeOut('400');
        this.filesContent.empty();
    },

    UploadModal.prototype.move = function() {
        var $modalContent = this.content;
        var offsetHeight  = $(document).height() - $(window).height();
      
        $modalContent.find("[control-move]").css("cursor","move")
        .on("mousedown",function(e){
            e.stopPropagation();
            /*$(this)[0].onselectstart = function(e) { return false; }*///防止拖动窗口时，会有文字被选中的现象(事实证明不加上这段效果会更好)
            $(this)[0].oncontextmenu = function(e) { return false; }//防止右击弹出菜单
            var getStartX = e.pageX,
                getStartY =  e.pageY;
            var getPositionX = $modalContent.offset().left,
                getPositionY = $modalContent.offset().top;

            $(document).on("mousemove",function(e){
                e.stopPropagation();
                var getEndX = e.pageX,
                    getEndY =  e.pageY;
                $modalContent.css({
                    left: getEndX-getStartX+getPositionX,
                    top: getEndY-getStartY+getPositionY - $(document).scrollTop()
                });   
            });   
                
            $(document).on("mouseup",function(){
                $(document).unbind("mousemove");
            })
        });  
    },

    //init sidemenu 初始化侧边栏导航
    UploadModal.prototype.init = function(width, height) {
        var $this = this;
        if (typeof width !== 'undefined') {
            $this.width = width;
        }

        if (typeof height !== 'undefined') {
            $this.height = height;
        }

        if($this.isMove){//设定弹窗是否可以拖动改变大小
            $this.move();
        };

        $(document).mouseup(function(){
            $(this).unbind("mousemove");
        });

        $(".modal-close").click(function(){
            $this.close()
        });

    },
    //init Sidemenu
    $.UploadModal = new UploadModal, $.UploadModal.Constructor = UploadModal

}(window.jQuery);
