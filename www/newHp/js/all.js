

$(document).ready(function(){
	
	$(".more").click(function(){
		$(".infopop").css("display","block");
		$(".banner.hidden-xs").stop().animate({right: "275px"},400, function(){ $(".banner.hidden-xs").css("z-index","9999999999999") });
	});
	$(".cl").click(function(){
		$(".banner.hidden-xs").css("z-index","99")
		$(".infopop").css("display","none");
		$(".banner.hidden-xs").stop().animate({right: "0px"},400);
	});
	$(".cart").click(function(){
	
		if ( $(this).hasClass("cart_on") == true )
		{
			$(this).removeClass("cart_on");
		} else {
		
			$(this).addClass("cart_on");
		}
	});
	// $(".like").click(function(){
	
	// 	if ( $(this).hasClass("like_on") == true )
	// 	{
	// 		$(this).removeClass("like_on");
	// 	} else {
		
	// 		$(this).addClass("like_on");
	// 	}
	// });

	$(".play_button_to_the_first").click(function(){
		$(".wrapper").stop().animate({scrollLeft : 0},700);
	});

	$(".play_button_next").click(function(){
		var sc_now = $(".wrapper").scrollLeft();
		var sc_now_02 = sc_now + 700;
		var sc_now_04 = String(sc_now_02) + "px";
	
		$(".wrapper").stop().animate({scrollLeft : sc_now_04},700);
	});

	$(".play_button_prev").click(function(){
		var sc_now2 = $(".wrapper").scrollLeft();
		var sc_now2_02 = sc_now2 - 700;
		var sc_now2_04 = String(sc_now2_02) + "px";
	
		$(".wrapper").stop().animate({scrollLeft : sc_now2_04},700);
	});
	$(".user_name").click(function(){
		if ( $(".user_name_detail").css("display") == "none" )
		{
			$(this).addClass("user_name_on");
			$(".user_name_detail").css("display","block");
		} else {
			$(this).removeClass("user_name_on");
			$(".user_name_detail").css("display","none");
			
			}
	});

	$(".like_wrap .like_on").click(function(){
	
		$(this).parent().parent().remove();
	});

	$(".nav_close").click(function(){
	
		$(this).css("display","none");
		$("nav").addClass("nav_off");
		$(".wrapper").removeClass("wrapper_sub");
		$(".nav_open").css("display","block");

	});

	$(".nav_open").click(function(){
	
		$(this).css("display","none");
		$("nav").removeClass("nav_off");
		$(".wrapper").addClass("wrapper_sub");
		$(".nav_close").css("display","block");
	});
	
	$(".count_up_down_up").click(function(){
		var no = $(this).parent().children("span").html();
		var no2 = Number(no);
		var no4 = no2 + 1;
		$(this).parent().children("span").html('');
		$(this).parent().children("span").html(no4);
	});
	$(".count_up_down_down").click(function(){
		var no = $(this).parent().children("span").html();
		var no2 = Number(no);
		var no4 = no2 - 1;
		$(this).parent().children("span").html('');
		$(this).parent().children("span").html(no4);
	});


	$(".login_pop_x").click(function(){
		$("#login_popup").css("display","none");
	});
	$(".opt_pop_x").click(function(){
		$("#option_popup").css("display","none");
	});
	$(".claim_pop_x").click(function(){
		$("#claim_popup").css("display","none");
		$("#claim_detail").css("display","none");
	});

	$(".cart_info table tr td:nth-of-type(2)").mouseover(function(){
		$(this).children("div").css("display","block");
	});
	$(".cart_info table tr td:nth-of-type(2)").mouseout(function(){
		$(this).children("div").css("display","none");
	});

	$("#minus_catalogue").click(function(){
	
		$("#catalog_banner").css("display","none");
		$("#catalog_banner_small").css("display","block");
	});
	$("#plus_catalogue").click(function(){
	
		$("#catalog_banner").css("display","block");
		$("#catalog_banner_small").css("display","none");
	});
});
