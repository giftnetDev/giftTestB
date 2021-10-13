/* ���� ���� */
var ua = navigator.userAgent;
var windowWidth = $(window).width();
var windowHeight = $(window).height();
var isMobile;

/* useagent check */
function userAgentChk(){
	/* device check */
	if(ua.match(/iPhone|iPod|iPad|Android|LG|SAMSUNG|Samsung|GallaxyTab/i) != null){
		$("body").addClass("device");		
	}else if(ua.indexOf("Mac") > -1){
		$("body").addClass("mac");
	}else{
		$("body").addClass("pc");
	}
		
	/* browser check */
	if(ua.indexOf('MSIE') > -1){
		if(ua.indexOf("MSIE 7.0") > -1){
			$("body").addClass("ie7");
		}else if(ua.indexOf("MSIE 8.0") > -1){
			$("body").addClass("ie8");
		}else if(ua.indexOf("MSIE 9.0") > -1){
			$("body").addClass("ie9");
		}else if(ua.indexOf("MSIE 10.0") > -1){
			$("body").addClass("ie10");
		}
	}else if(ua.indexOf("rv:11.0") > -1){
		$("body").addClass("ie11");
	}else if(ua.indexOf("Edge") > -1){
		$("body").addClass("edge");
	}else if (ua.indexOf("Chrome") > -1 || ua.indexOf("CriOS") > -1){
		$("body").addClass("chrome");
	}else if (ua.indexOf("Firefox") > -1){
		$("body").addClass("firefox");
	}else if (ua.indexOf("OPT") > -1){
		$("body").addClass("opera");
	}else if (ua.indexOf("NAVER") > -1){
		$("body").addClass("naver");
	}else if (ua.indexOf("KAKAOTALK") > -1){
		$("body").addClass("kakao");
	}else if (ua.indexOf("SamsungBrowser") > -1){
		$("body").addClass("samsungbrowser");
	}else if (ua.indexOf("Safari") > -1){
		$("body").addClass("safari");
	}
}

function bodyClassChange(){
	/* display check */
	if (windowWidth > 1440){
		isMobile = false;
		$("body").removeClass("smallbrowser").removeClass("tablet").removeClass("mobile").addClass("normal");
	}else if (windowWidth <= 1440 && windowWidth > 1200){
		isMobile = false;
		$("body").removeClass("normal").removeClass("tablet").removeClass("mobile").addClass("smallbrowser");
	}else if (windowWidth <= 1200 && windowWidth >= 768){
		isMobile = true;
		$("body").removeClass("normal").removeClass("smallbrowser").removeClass("mobile").addClass("tablet");
	}else if (windowWidth < 768){
		isMobile = true;
		$("body").removeClass("normal").removeClass("smallbrowser").removeClass("tablet").addClass("mobile");
	}

	/* orientation check */
	switch(window.orientation){ 
		case -90:
		$("body").addClass("landscape").removeClass("portrait");
		break;
		case 90:
		$("body").addClass("landscape").removeClass("portrait");
		break;
		case 0:
		$("body").addClass("portrait").removeClass("landscape");
		break;
		case 180:
		$("body").addClass("portrait").removeClass("landscape");
		break;
	}
}

/* ù �ε��� */
function firstLoad(){
	//$("#wrap").animate({opacity:1}, 800); 
}


/* ����˾� ���̱� */ 
function modalView(modalName){
	var modalWidth;
	var modalHeight;

	$(".transparents-layer").remove();	
	$(".popupwrap").removeClass("active").css("left", "-99999rem").css("top", "-99999rem").css("opacity", "0");
	$(".modalpop").css({"top": 0, "left":0, "opacity":1});
	
	modalWidth = $(".popupwrap."+modalName).innerWidth()/2;
	modalHeight = $(".popupwrap." + modalName).innerWidth()/2;	
	
	if ($("body").hasClass("mobile")){
		$(".popupwrap." + modalName).css({ left: "0", top: $(window).scrollTop()+"px"}).animate({ opacity: 1 }, 500).addClass("active");
	}else{
		$(".popupwrap." + modalName).css({ top: "50%", left: "50%", marginTop: -modalHeight + "px", marginLeft: -modalWidth + "px" }).animate({ opacity: 1 }, 500).addClass("active");
	}

	$("body").append("<div class='transparents-layer'></div>");
	$(".transparents-layer").attr("onclick", "modalHide('"+modalName+"')");	
	$(".transparents-layer").on('scroll touchmove mousewheel', function(e) { //��� ��ũ�� ����
		e.preventDefault();
	});
}

/* ����˾� ����� */ 
function modalHide(modalName){
	$(".popupwrap."+modalName).animate({opacity:0}, 400, function(){
		$(".popupwrap."+modalName).css("top", "-99999rem").css("left","-99999rem");
		$(".modalpop").css({"top" : "-99999rem", "left": "-99999rem", "opacity":"0"});
		$(".transparents-layer").animate({opacity:0}, 400, function(){
			$(this).remove();
		});		
	});
}

function modalResize(){
	if ($(".popupwrap.active").length > 0){
		var modalWidth = $(".modalpop .popupwrap.active").innerWidth()/2; 
		var modalHeight = $(".modalpop .popupwrap.active").innerHeight()/2;
		if ($("body").hasClass("mobile")){
			$(".popupwrap." + modalName).css({ left: "0", top: $(window).scrollTop()+"px"}).animate({ opacity: 1 }, 500).addClass("active");
		}else{
			$(".popupwrap." + modalName).css({ top: "50%", left: "50%", marginTop: -modalHeight + "px", marginLeft: -modalWidth + "px" }).animate({ opacity: 1 }, 500).addClass("active");
			$("html, body").css({overflow:"hidden"});
		}
	}
}

/* tabbox �� ȭ����ȯ */ 
function commonTab(tabName){
	$(".tabbox ul li").removeClass("on");
	$(".tabbox ul li."+tabName).addClass("on");
	$(".tab-hiddencontents."+tabName).siblings(".tab-hiddencontents").removeClass("on");
	$(".tab-hiddencontents."+tabName).addClass("on");
}

function searchToggle(){
	if (!$(".searchbox").hasClass("on")){
		$(".searchbox").height($(window).height()).animate({right:"0"}, 600, function(){
			$(".searchbox").addClass("on");
		});
	}else{
		$(".searchbox").animate({right:"-200%"}, 900, function(){
			$(".searchbox").removeClass("on").css("height", "auto");
		});
	}
}

function categoryToggle(){
	if (!$(".categorybox").hasClass("on")){
		$(".categorybox").height($(window).height()).animate({left:"0"}, 500, function(){
			$(".categorybox").addClass("on");
		});
	}else{
		$(".categorybox").animate({left:"-200%"}, 800, function(){
			$(".categorybox").removeClass("on").css("height", "auto");
		});
	}
}

function submenuToggle(){
	if (!$(".submenu").hasClass("on")){
		$(".ctgbox ul").slideDown();
		$(".submenu").addClass("on");
	}else{
		$(".ctgbox ul").slideUp();
		$(".submenu").removeClass("on");
	}
}


$(window).scroll(function() { 
});

$(window).resize(function() {
	windowWidth = $(window).width();
	windowHeight = $(window).height();
	userAgentChk();
	console.log($("body").attr("class"));
	bodyClassChange();
});

$(function(){
	userAgentChk();
	firstLoad();
	bodyClassChange();
	console.log($("body").attr("class"));

	// �����ϱ�, kr region callendar
	$.datepicker.regional['kr'] = {
		closeText: '�ݱ�', // �ݱ� ��ư �ؽ�Ʈ ����
		currentText: '����', // ���� �ؽ�Ʈ ����
		monthNames: ['1 ��','2 ��','3 ��','4 ��','5 ��','6 ��','7 ��','8 ��','9 ��','10 ��','11 ��','12 ��'], // ���� �ؽ�Ʈ ����
		monthNamesShort: ['1 ��','2 ��','3 ��','4 ��','5 ��','6 ��','7 ��','8 ��','9 ��','10 ��','11 ��','12 ��'], // ���� �ؽ�Ʈ ����
		dayNames: ['������','ȭ����','������','�����','�ݿ���','�����','�Ͽ���'], // ���� �ؽ�Ʈ ����
		dayNamesShort: ['��','��','ȭ','��','��','��','��'], // ���� �ؽ�Ʈ ��� ����    
		dayNamesMin: ['��','��','ȭ','��','��','��','��'], // ���� �ּ� ��� �ؽ�Ʈ ����
		dateFormat: 'dd/mm/yy' // ��¥ ���� ����
	};

	$.datepicker.setDefaults($.datepicker.regional['kr']);
	 
	$(".datebox input").datepicker({
		showOn: "both",
		changeMonth: false,
		changeYear: false,
		dateFormat: "yy.mm.dd",	
		buttonImage: "../images/icon_calendar.png",
		buttonImageOnly: true
	});


	/* top button */
	$(".btn-gotop").click(function(){
		$("html, body").animate({scrollTop:0})
	});
		
});