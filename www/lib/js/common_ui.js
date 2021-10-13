/* 전역 변수 */
var ua = navigator.userAgent;
var windowWidth = $(window).width();
var windowHeight = $(window).height();
var isMobile;


/* useagent check */
function userAgentChk(){
	if(ua.match(/iPhone|iPod|LG|Android|SAMSUNG|Samsung/i) != null){
		if (windowWidth > 720){
			$("body").addClass("device").addClass("tablet");
			switch(window.orientation){ 
				case -90:
				$("body").addClass("tablet_landscape");
				$("body").addClass("pc").removeClass("tablet");
				break;
				case 90:
				$("body").addClass("tablet_landscape");
				$("body").addClass("pc").removeClass("tablet");
				break;
				case 0:
				$("body").addClass("tablet_portrait");
				$("body").removeClass("pc").removeClass("normal").addClass("tablet");
				break;
				case 180:
				$("body").addClass("tablet_portrait");
				$("body").removeClass("pc").removeClass("normal").addClass("tablet");
				break;
			 }
		}else{
			$("body").addClass("mobile").addClass("device");
			switch(window.orientation){  
				case -90:
				$("body").addClass("mobile_landscape")
				break;
				case 90:
				$("body").addClass("mobile_landscape");
				break;
				case 0:
				$("body").addClass("mobile_portrait");
				break;
				case 180:
				$("body").addClass("mobile_portrait");
				break;
			 }
		}
		isMobile = true;
	}else if (ua.match(/iPad|GallaxyTab/i) != null){
		$("body").addClass("device").addClass("tablet");
		switch(window.orientation){ 
			case -90:
			$("body").addClass("tablet_landscape");
			$("body").addClass("pc").removeClass("tablet");
			break;
			case 90:
			$("body").addClass("tablet_landscape");
			$("body").addClass("pc").removeClass("tablet");
			break;
			case 0:
			$("body").addClass("tablet_portrait");
			$("body").removeClass("pc").removeClass("normal").addClass("tablet");
			break;
			case 180:
			$("body").addClass("tablet_portrait");
			$("body").removeClass("pc").removeClass("normal").addClass("tablet");
			break;
		 }
		isMobile = true;
	}else{
		bodyClassChange();

		$(window).resize(function(){
			windowWidth = $(window).width();
			windowHeight = $(window).height();
			bodyClassChange();
		}).resize();

		if(ua.indexOf("MSIE 8.0") > -1 || ua.indexOf("Trident/4.0") > -1){ //IE8 이하일 경우
			$("body").addClass("pc").addClass("pc_ie8");
			if(ua.indexOf("Windows NT 6.2") > -1){
			}else if (ua.indexOf("Windows NT 6.1") > -1){			
				$("body").addClass("pc").addClass("pc_ie8").addClass("w7"); //window7, IE8
			}else if (ua.indexOf("Windows NT 5.1") > -1){
				$("body").addClass("pc").addClass("pc_ie8").addClass("xp"); //windowXP, IE8
			}
		}else if(ua.indexOf("MSIE 7.0") > -1 || ua.indexOf("MSIE 6.0") > -1){
			$("body").addClass("pc").addClass("pc_ie8");
		}else if(ua.indexOf("Trident") > -1){
			$("body").addClass("pc").addClass("ie");
		}else{ //IE9 PC 
			if (ua.indexOf("Chrome") > -1){
				$("body").addClass("pc").addClass("chrome");
			}else if(ua.indexOf("Mac") > -1){
				$("body").addClass("mac");
			}else{
				$("body").addClass("pc");
			}
		}
	}
	isMobile = false;
}
userAgentChk();

function bodyClassChange(){
	if (windowWidth > 1201){
		isMobile = false;
		$("body").removeClass("mobile_portrait").removeClass("mobile").removeClass("tablet").removeClass("smallbrowser").addClass("normal");
		$("#wrap").css("margin-left", "0");
		$(".toparea").css("left", "0");
	}else if (windowWidth <= 1200 && windowWidth > 1025){
		isMobile = false;
		$("body").removeClass("mobile_portrait").removeClass("normal").removeClass("mobile").removeClass("tablet").addClass("smallbrowser");
		$(".contentsarea").css("min-height", (windowHeight-$(".toparea").innerHeight()-$(".bottomarea").innerHeight())+"px");
		$("#wrap").css("margin-left", "0");
		$(".toparea").css("left", "0");
	}else if (windowWidth <= 1024 && windowWidth > 769){
		isMobile = true;
		$("body").removeClass("mobile_portrait").removeClass("normal").removeClass("mobile").removeClass("smallbrowser").addClass("tablet");
		$(".contentsarea").css("min-height", (windowHeight-$(".toparea").innerHeight()-$(".bottomarea").innerHeight())+"px");
	}else if (windowWidth <= 768){
		isMobile = true;
		$("body").removeClass("mobile_portrait").removeClass("normal").removeClass("tablet").removeClass("smallbrowser").addClass("mobile");
		if (windowWidth < 481) {
			$("body").addClass("mobile_portrait");
		}
	}
}

function firstLoad(){
	setTimeout(function(){
		$("#wrap").animate({opacity:1}, 500); 
	}, 200);
}
firstLoad();

$(window).resize(function(){	
	windowWidth = $(window).width();
	windowHeight = $(window).height();
	modalResize();
}).resize();

if ($("body").hasClass("mobile") == true || $("body").hasClass("tablet") == true || $("body").hasClass("device") == true){
	isMobile = true;
}else{
	isMobile = false;
}

function modalView(modalName, parentName){
	if (isMobile){	
		var modalWidth = $(window).width()*0.9; 
	}else{
		var modalWidth = $(".modalpop .popupwrap."+modalName).innerWidth()/2; 
		var modalHeight = $(".modalpop .popupwrap."+modalName).innerHeight()/2;
	}	
	if (!parentName){$(".transparents-layer").remove()}
	$(".popupwrap").removeClass("active").css("left", "-99999px").css("top", "-99999px").css("opacity", "0");
	$(".modalpop").show().css({"top" : 0, "left": 0});
	if (isMobile){
		$("body").append("<div class='transparents-layer'></div>");
		$(".popupwrap."+modalName).css("top", "10%").css("left","5%").animate({opacity:1}, 500);
	}else{
		if (parentName){
			$(".popupwrap."+parentName).animate({opacity:0}, 400, function(){
				$(".popupwrap."+parentName).removeClass("active").css("top", "-99999px").css("left","-99999px");
			});
			$(".popupwrap."+modalName).addClass("active").css("top", "40%").css("left","50%").css("margin-top", -($(".modalpop .popupwrap."+modalName).innerHeight()/2.7)+"px").css("margin-left", -modalWidth+"px").animate({opacity:1}, 500);
		}else{
			$("body").append("<div class='transparents-layer'></div>");
			$(".popupwrap."+modalName).addClass("active").css("top", "40%").css("left","50%").css("margin-top", -($(".modalpop .popupwrap."+modalName).innerHeight()/2.7)+"px").css("margin-left", -modalWidth+"px").animate({opacity:1}, 500);
		}
	}	
	$(".transparents-layer").attr("onclick", "modalHide('"+modalName+"')");
	$(".popupwrap."+modalName).addClass("active");
}

function modalResize(){
	if ($(".popupwrap.active").length > 0){
		if (isMobile){
			var modalWidth = $(window).width()*0.9;
			$(".popupwrap.active").css("top", "10%").css("left","5%").css("margin-left", "0").css("margin-top", "0").animate({opacity:1}, 500);
		}else{
			var modalWidth = $(".modalpop .popupwrap").innerWidth()/2; 
			var modalHeight = $(".modalpop .popupwrap").innerHeight()/2;
			$(".popupwrap.active").css("top", "40%").css("left","50%").css("margin-top", -($(".modalpop .popupwrap").innerHeight()/2.7)+"px").css("margin-left", -modalWidth+"px").animate({opacity:1}, 500);
		}	
	}
}
modalResize();

function modalHide(modalName){
	$(".popupwrap."+modalName).animate({opacity:0}, 400, function(){
		$(".popupwrap."+modalName).css("top", "-99999px").css("left","-99999px");
		$(".modalpop").css({"top" : "-99999px", "left": "-99999px"});
		$(".transparents-layer").animate({opacity:0}, 400, function(){
			$(this).remove();
		});
		$(".popupwrap."+modalName).removeClass("active");
	});
}

function commonTab(tabParent, tabName){
	$("."+tabParent+" ul.tabbox li").removeClass("on");
	$("."+tabParent+" ul.tabbox li."+tabName).addClass("on");
	$("."+tabParent+" .tabcontents").removeClass("on");
	$("."+tabParent+" .tabcontents."+tabName).addClass("on");
	$('.slick-slider').slick('setPosition');
}

/* datepicker */
$("input.txt_date").datepicker({
	changeMonth: false,
	changeYear: false,
	dateFormat: "yy.mm.dd",
	buttonImageOnly: true,
	buttonText:"날짜선택",
	dayNames: [ "일", "월", "화", "수", "목", "금", "토" ],
	dayNamesMin: [ "일", "월", "화", "수", "목", "금", "토" ],
	dayNamesShort: [ "일", "월", "화", "수", "목", "금", "토" ],
	monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
	monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
});

function mobileLnbToggle(){
	var mobileLnbHeight = $(".midarea").height();
	if (!$(".btn-category").hasClass("menuClose")){
		mobileLnbHeight = $(".midarea").innerHeight();
		$(".leftarea").css("height", mobileLnbHeight+"px").animate({"left":"0"}, 500);
		$("#wrap").animate({"marginLeft":"65%"}, 500);
		$(".toparea").animate({"left":"65%"}, 500);
		$(".btn-category").addClass("menuClose");
		$("body").css("overflow-x", "hidden");
	}else{
		mobileLnbHeight = "auto";
		$(".leftarea").animate({"left":"-65%"}, 500, function(){
			$(this).css("height", "auto")
		});
		$("#wrap").animate({"marginLeft":"0"}, 500, function(){
			$("body").css("overflow-x", "auto");
		});
		$(".toparea").animate({"left":"0"}, 500);
		$(".btn-category").removeClass("menuClose");
	}
}


$(document).ready(function(){
	var tabL = $('.tab_wrap.type02 .tabs').length;

	$(function() {
		 $('.tab_wrap.type02').addClass('tabs' + '0' + tabL);
	});

	$(".side_menu > ul > li:has('ul')").addClass('has');

	$(".side_menu > ul > li.has > a").click(function(){
		if($(this).parent().hasClass('open')){
			$(this).parent().removeClass('open');
			$(this).parent().find('ul').slideUp();
		} else {
			$(".side_menu > ul > li").removeClass('open');
			$(".side_menu > ul > li.has ul").slideUp();
			$(this).parent().addClass('open');
			$(this).parent().find('ul').slideDown();
		}		
		return false;
	});

	//$("input[type=file].nicefileinput").nicefileinput();

	$('.tab_wrap.type02 .tabs').click(function(){
		if($(this).hasClass('sele')){
	
		} else {
			$('.tab_wrap.type02 .tabs').removeClass('sele');
			$('.tab_wrap.type02 .tab_cont').removeClass('sele');
			$(this).addClass('sele');
			$(this).next().addClass('sele');
		}

		return false;	
	});		
	
	//$('.bxslider').bxSlider({
	//	pagerCustom: '#bx-pager'
	//});

	$('.tab a, .tab .menu').hover(function(){
		$('.tab a').removeClass('open');
		$(this).addClass('open');
		$(this).parent().find('.menu').show();
	}, function(){
		$('.tab a').removeClass('open');
		$(this).parent().find('.menu').hide();
	});

	


	
});


// 설정하기, kr region callendar
$.datepicker.regional['kr'] = {
	closeText: '닫기', // 닫기 버튼 텍스트 변경
	currentText: '오늘', // 오늘 텍스트 변경
	monthNames: ['1 월','2 월','3 월','4 월','5 월','6 월','7 월','8 월','9 월','10 월','11 월','12 월'], // 개월 텍스트 설정
	monthNamesShort: ['1 월','2 월','3 월','4 월','5 월','6 월','7 월','8 월','9 월','10 월','11 월','12 월'], // 개월 텍스트 설정
	dayNames: ['월요일','화요일','수요일','목요일','금요일','토요일','일요일'], // 요일 텍스트 설정
	dayNamesShort: ['일','월','화','수','목','금','토'], // 요일 텍스트 축약 설정    
	dayNamesMin: ['일','월','화','수','목','금','토'], // 요일 최소 축약 텍스트 설정
	//dateFormat: 'dd/mm/yy' // 날짜 포맷 설정
};
$.datepicker.setDefaults($.datepicker.regional['kr']);
$( function() {
	$( ".datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		showMonthAfterYear: true,
		showButtonPanel: true,
		dateFormat: "yy-mm-dd",
		showOn: "button",
		buttonImage: "../images/icon_calendar.png",
		buttonImageOnly: true
	});
});
