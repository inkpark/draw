
var div_resize = function(){
	var bh = $(document.body).outerHeight(true);//body高度
	var bw = $(document.body).outerWidth(true);//body宽度
	var ww = $(window).width();//而是区域宽度
	var wh = $(window).height();//而是区域宽度
    $('.m-item').each(function(){
        $(this).css('top',(wh-$(this).height())/2+$(document).scrollTop() + 'px');
        $(this).css('left',(ww - $(this).width())/2 + 'px');
    });

	//$('.m-item').css('top',(wh-$('#m-item').height())/2+$(document).scrollTop() + 'px');
	//$('.m-item').css('left',(ww - $('#m-item').width())/2 + 'px');
	$('#model').css('height', bh+"px");
	$('#model').css('width', bw+"px");
    $('.main_wrap').perfectScrollbar('update');
};


var removeModel = function(){
    $('#model').hide();
    $('.m-item').hide();
}


function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

    if(arr=document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}


function addToFavorite() {
	var a = "http://www.dianxinqipai.com/",
		b = "点心棋牌";
	document.all ? window.external.AddFavorite(a, b) : window.sidebar && window.sidebar.addPanel ? window.sidebar.addPanel(b, a, "") : alert("对不起，您的浏览器不支持此操作!\n请您使用菜单栏或Ctrl+D收藏本站。")
}



$(document).ready(function(){
	div_resize();

    var username =  getCookie('username');
    if (username) {
        $('#uinfo').html('欢迎回来，<span>' + username + '</span>');
    };

	$(window).bind('resize',function(){
		div_resize();
	});
	$(document).scroll(function(){
		div_resize();
	});
	$('#login').click(function(){
        $('#model').show();
		$('#m-item').show();
        $(document).keyup(function(event){
            if (event.keyCode == 13) {
                $('.login-btn').click();
            };
        })
	});
	$('#m-bg').click(function(){
        $('#model').hide();
		$('.m-item').hide();
	});
	$('#m-tit span').click(function(){
        $('#model').hide();
		$('.m-item').hide();
	});

    $('.licon').click(function(){
        var url = $(this).data('u');
        $.get(
            '/api/signin.php',
            {u: url},
            function (data) {
                window.location= data.uloginurl;
            },"json"
        );
    });

	$('.login-btn').click(function(){
		var user = $('#username').val();
		var pass = $('#password').val();
		$.get(
			'/api/signin.php',
			{ username: user, password: pass },
			function(data){
                if (data.status > 0) {
                    getInfo();
                    flyFun();
                    $('#uinfo').html('欢迎回来，<span>' + data.username +'</span>');
                    removeModel();
                } else {
                    switch(data.status) {
                        case -1:
                            $('#err_info').html('用户不存在，或者被删除');
                            break;
                        case -2:
                            $('#err_info').html('密码错误');
                            break;
                        case -3:
                            $('#err_info').html('安全提问错误');
                            break;
                        case -99:
                            $('#err_info').html('appid,appkey配置错误');
                            break;
                        case -1100:
                            $('#err_info').html('API-验证失败');
                            break;
                        case -1101:
                            $('#err_info').html('无效的Accesstoken');
                            break;
                        default:
                            $('#err_info').html('未知错误');
                    }
                };
			},"json"
		);

	});
})
