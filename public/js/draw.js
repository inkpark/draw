var isdrawing = false;
var gamepage = 'http://yandui-lb1.chinacloudapp.cn/Lobby/ReceiveLoginInfo';
var index = 0;
var islogin = false;
var login  = function(){
    $('#login').click();
};
//信息加载

var getInfo = function(){
    var session = getCookie('session_id');
    if (session) { 
        islogin = true;       
        $.get(
            '/api/hdinfo.php',
            '',
            function(res){
                index = res.info.index;
                $('.big_btn').attr('href','javascript:void(0)');
                $('.numx').find('div').html(res.info.tickets);

                

                if (!res.info.checkIned) {
                    $('.big_btn').addClass('big_btn_logined');
                    $('.big_btn').on('click',function(){
                                checkin(index);
                            });
                } else {
                    $('.big_btn').attr('href',gamepage+res.info.resurl);
                    $('.big_btn').addClass('big_btn_checked');
                };



                $('.qd_btn').each(function(index){
                    if (index < res.info.index) {
                        $(this).addClass('qded');
                        $(this).html('<p>已签到<br>进入玩牌</p>');
                        $(this).attr('href',gamepage+res.info.resurl);
                    } else if(index == res.info.index){
                        if (!res.info.checkIned) {
                            $(this).addClass('qding');
                            $(this).on('click',function(){
                                checkin(index);
                            });
                        } 
                        
                    };
                });
            },"json"
        );        
    };        
};

var checkin = function(index){
    $.get(
        '/api/checkIn.php',
        {'index':index},
        function(res){
            if (res.status === 0) {
                location.reload();
            } else {
                $('#mess_item').html("<div class='err'>"+res.message+"</div>");

                $('#model').show();
                $('#mess_item').show();
            };
        },"json"
    );        
};

//活动区交互效果
var bgFun = function(obj,index){
    obj.on({'hover':function(){
        var posi = '';
        switch (index){
            case 0:
                posi = '0% -50%';
                break;
            case 1:
                posi = '0% 50%';
                break;
            case 2:
                posi = '50% 50%';
                break;
            case 3:
                posi = '50% 0%';
                break;
            case 4:
                posi = '100% 50%';
                break;
            case 5:
                posi = '100% 0%';
                break;

            default:
                posi = '0% 0%';
                break;
        }
        $('.fly').css('background-position',posi);
        $('.hd_item').eq(index).addClass('hdi_hover');

    },'mouseleave':function(){

        $('.fly').css('background-position','0% 0%');
        $('.hd_item').eq(index).removeClass('hdi_hover');

    },'click':function(){
        switch (index){
            case 1:
                break;

            case 2:
                $('#model').show();
                $('#ph_item').show();                  
                break;
            case 3:

                break;

            default:
                if (!islogin) {
                    login();
                };
                break;
        }            
    }});
};

var flyFun = function(){
    $('.hd_bt').each(function(index){
        bgFun($(this),index);
        if (islogin) {
            switch (index) {
                case 1:
                    $(this).css('width','176px');
                    break;

                case 2:
                       
                    
                    break;
                case 3:
                    break;
                case 4:
                    $(this).css('width','180px');
                    $(this).css('left','251px');
                    $(this).addClass('hdbt_hover');
                    break;

                default:
                    $(this).addClass('hdbt_hover');
                    break;
            }
        };
    });

    $('.hd_item').each(function(index){
        bgFun($(this),index);
    });   
}

$(function(){


    getInfo();
    flyFun();

    //抽奖转盘实现   
	var timeOut = function(){  //超时函数
		$("#lotteryBtn2").rotate({
			angle:0, 
			duration: 10000, 
			animateTo: 2160, //这里是设置请求超时后返回的角度，所以应该还是回到最原始的位置，2160是因为我要让它转6圈，就是360*6得来的
			callback:function(){
				alert('亲！没有中奖哦！！');
                isdrawing = false;
			}
		}); 
	}; 
	var rotateFunc = function(awards,angle,text){  //awards:奖项，angle:奖项对应的角度
		$('#zhongjiang').hide();
		$('#lotteryBtn2').stopRotate();
		$("#lotteryBtn2").rotate({
			angle:0, 
			duration: 5000, 
			animateTo: angle+1440, //angle是图片上各奖项对应的角度，1440是我要让指针旋转4圈。所以最后的结束的角度就是这样子^^
			callback:function(){
				
				switch (awards){
					case 1:
						$('#zhongjiang').css('background-position',' 200% 100%');
						break;
					case 2:
						$('#zhongjiang').css('background-position','250% 100%');
						break;
					case 3:
						$('#zhongjiang').css('background-position','300% 100%');
						break;
					case 4:
						$('#zhongjiang').css('background-position','250% 0%');
						break;
					case 5:
						$('#zhongjiang').css('background-position','200% 0%');
                        break;
                    case 6:
                        $('#zhongjiang').css('background-position','300% 0%');
						break;
                    default:
                        $('#zhongjiang').css('background-position','0 0');
                        break;
				}
                var t = $('.numx > div').html();
                $('.numx > div').html(t-1);
                $('#draw_item > .ph_info > .ph_i > span').html(text);
                $('#model').show();
                $('#draw_item').show();

				$('#zhongjiang').show();
                isdrawing = false;
				
			}
		}); 
	};



	$("#lotteryBtn").rotate({ 
	   bind: 
		 { 
            click: function(){
                if(!isdrawing){
                    isdrawing = true;
                    $.get(
                        '/api/draw.php',
                        '',
                        function(res){
                            if (res.status == 0) {
                                var data;
                                var id = res.info.p_id;
                                var p_name = res.info.p_name;
                                switch(id){
                                    case "1":
                                        rotateFunc(1,20,p_name);
                                        break;
                                    case "2":
                                        rotateFunc(2,220,p_name);
                                        break;
                                    case "3":
                                        rotateFunc(3,180,p_name);
                                        break;
                                    case "4":
                                        data = [1,2]; //返回的数组
                                        data = data[Math.floor(Math.random()*data.length)];
                                        if(data==1){

                                            rotateFunc(4,300,p_name)
                                        }
                                        if(data==2){

                                            rotateFunc(4,100,p_name)
                                        }
                                     
                                        break;
                                    case "5":
                                        data = [1,2]; //返回的数组
                                        data = data[Math.floor(Math.random()*data.length)];
                                        if(data==1){

                                            rotateFunc(5,60,p_name)
                                        }
                                        if(data==2){

                                            rotateFunc(5,140,p_name)
                                        }
                                        break;

                                    case "6":
                                        data = [1,2]; //返回的数组
                                        data = data[Math.floor(Math.random()*data.length)];
                                        if(data==1){

                                            rotateFunc(6,340,p_name)
                                        }
                                        if(data==2){

                                            rotateFunc(6,260,p_name)
                                        }
                                        break;
                                    default :
                                        timeOut();
                                        break;
                                }
                            } else {
                                    switch (res.status) {
                                        case 303:
                                            $('#login').click();
                                            break;
                                        default:
                                            $('#mess_item').html("<div class='err'>"+res.message+"</div>");
                                            $('#model').show();
                                            $('#mess_item').show();
                                            break;
                                    }
                                    
                                    isdrawing = false
                            };
                        },"json"
                    );
                }
			}
		 } 
	   
	});
	
})