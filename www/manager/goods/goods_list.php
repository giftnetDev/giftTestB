<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

	if ($mode == "T") {
		updateGoodsUseTF($conn, $use_tf, $s_adm_no, $goods_no);
	}

	if ($mode == "SU") {
		$row_cnt = count($chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no = $chk_no[$k];
			$result = updateStateGoods($conn, $goods_state_mod, $str_goods_no, $s_adm_no);
		}
	}

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			if (isSaleGoods($conn, $str_goods_no)) {
				echo"<script>alert('saleGoods');</script>";
			} else {
				$result = deleteGoods($conn, $str_goods_no, $s_adm_no);
			}
		}
		
	}

	if ($mode == "C") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			$result = copyGoods($conn, $str_goods_no, $s_adm_no);
		
		}
		
	}

	if($mode == "UPDATE_NEXT_SALE_PRICE") { 

		$result = updateNextSalePrice($conn, $s_adm_no);


	}

#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		//$start_date = date("Y-m-d",strtotime("-12 month"));
		$start_date = "2010-07-24";
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	if($chk_vendor != "Y" && $txt_vendor_calc == "100")
		$txt_vendor_calc = 100;

	$arr_options = array("exclude_category" => $con_exclude_category, "vendor_calc" => $txt_vendor_calc);

	$nListCnt =totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listGoods1($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize,$nListCnt);
	
	//MRO판매가(TBL_GOODS_PRICE.MRO_SALE_PRICE) 추가
	if(sizeof($arr_rs)>0){
		for($i=0;$i<sizeof($arr_rs);$i++){
			$TEMP_GOODS_NO = trim($arr_rs[$i]["GOODS_NO"]);
			$arr_rs[$i]["MRO_SALE_PRICE"] = getMroSalePrice($conn, $TEMP_GOODS_NO);
		}
	}

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04;
	$strParam = $strParam."&chk_vendor=".$chk_vendor."&txt_vendor_calc=".$txt_vendor_calc."&view_type=".$view_type."&con_exclude_category=".$con_exclude_category;

	if($result) { 
?>
	<script type="text/javascript">
		document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
	
	</script>
<?
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	.wrong {background-color:#ffcdcf;} 

	td.DETAIL_EXISTS{
		background-color:cadetblue;
	}	

</style>

<script>
	var link;
	$(document).ready(function(){
		$("input[name=btnAccess").on("mousedown",function(){
			if($("input[name='chk_no[]']:checked").length==0){
				alert("상품을 한 개 이상 선택해 주세요.");
			}
			else{
				confirmTF=confirm("선택한 상품들의 최신화를 진행하시겠습니까?");
				if(!confirmTF) return;
				var goods_nos= new Array();
				$("input[name='chk_no[]']:checked").each(function(){
					goods_nos.push($(this).val());
				});
				$.ajax({
					url:'/manager/ajax_processing.php',
					dataType: 'json',
					type: 'POST',
					data:{
						'mode': "UPDATE_ACCESS_TIME",
						'goods_nos': goods_nos
					},
					success: function(response){
						alert('갱신 완료');

					},
					error: function(jqXHR, textStatus, errorThrown){
						alert('갱신 실패');

					}
				});
			}
		});
		$("#make_link_btn").on("mousedown", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				alert("상품을 한개 이상 선택해주세요.");
			} else {
				var goods_no = new Array();
				var reg_adm = "<?=$s_adm_no?>";
				$("input[name='chk_no[]']:checked").each(function(){
					goods_no.push($(this).val());
				});
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
					'mode': "INSERT_LINK",
					'goods_no': goods_no,
					"reg_adm": reg_adm
					},
					success: function(response) {
						if(response != false){
							link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info.php?key=" + response;
							var $temp = $("<input id='clipboard' />");
							$("body").append($temp);
							$('#clipboard').val(link);
						} else{
							alert("실패하였습니다.");
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});//ajax
			}//else
		});//make_link_btn mousedown
		
		$("#make_link_btn").on("click", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				// alert("상품을 한개 이상 선택해주세요."); //경고 한 번만 띄움
			} else {
				setTimeout(function() {
					var $input = $("#clipboard");
					if ($input.length && $input.val().length > 0) {
						$input.select();
						document.execCommand("copy");
						$input.remove();
					}
					window.open(link, '_blank', 'width=450, height=768');
					// alert("링크가 복사되었습니다.\n\n"+link);
					link="";
				}, 100);
			}
		});

		$("#make_image_btn").on("click", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				alert("상품을 한개 이상 선택해주세요.");
			} else {
				var goods_no = new Array();
				var reg_adm = "<?=$s_adm_no?>";
				$("input[name='chk_no[]']:checked").each(function(){
					goods_no.push($(this).val());
				});
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
					'mode': "INSERT_LINK",
					'goods_no': goods_no,
					"reg_adm": reg_adm
					},
					success: function(response) {
						if(response != false){
							link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info_image.php?key=" + response;
							window.open(link, '_blank', 'width=450, height=768');
						} else{
							alert("실패하였습니다.");
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});//ajax
			}//else
		});//make_image_btn click
	});//ready
	$(function() {
		$( ".datepicker" ).datepicker({
			showOn: "button",
			buttonImage: "/manager/images/calendar/cal.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			beforeShow: function() {
				setTimeout(function(){
					$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});
	});
  
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
	
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});

  </script>
  <script type="text/javascript" >

	function js_write() {
		document.location.href = "goods_write.php";
	}

	function js_view(rn, goods_no) {
		// $.ajax({
		// 			url: '/manager/ajax_processing.php',
		// 			dataType: 'json',
		// 			type: 'post',
		// 			data : {
		// 			'mode': "UPDATE_ACCESS_TIME",
		// 			'goods_no': goods_no,
		// 			},
		// 			success: function(response) {
		// 				if(response != false){
							
		// 				} else{
		// 					alert("실패하였습니다.");
		// 				}
		// 			}, error: function(jqXHR, textStatus, errorThrown) {
		// 				console.log(jqXHR.responseText); 
		// 			}
		// 		});//ajax

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_write.php";
		frm.submit();
		
	}

	function js_view_company(cp_no) {

		window.open(
		  "/manager/company/company_write.php?mode=S&cp_no=" + cp_no,
		  '_blank' 
		);
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_exclude_category() {
		var frm = document.frm;
		
		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.con_exclude_category.value = frm.con_cate.value;
		frm.con_cate.value = "";

		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_toggle(goods_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('사용 여부를 변경 하시겠습니까?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.goods_no.value = goods_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_excel() {
		
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품을 삭제 하시겠습니까?\n체크박스에 선택을 하셨어도 상품 판매 내역이 있을 경우 삭제 되지 않을 수 있습니다.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_state_mod() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품 판매 상태를 변경 하시겠습니까?\n');
		
		if (bDelOK==true) {
			
			frm.mode.value = "SU";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_copy() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품을 복사 하시겠습니까? 상품 복사시 기본 정보와 가격정보만 복사됩니다.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_update_next_sale_price() { 

		var frm = document.frm;

		bOK = confirm('다음 전단지 가격 기준으로 판매가를 일괄 업데이트 합니다. 신중하게 진행해주세요.');
		
		if (bOK==true) {
			
			frm.mode.value = "UPDATE_NEXT_SALE_PRICE";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_layer_show(goods_no, img_url) {
		
		var origin_img = img_url.replace("simg/s_50_50_","");
		$("#l_"+goods_no).html("<img src='"+origin_img+"'>").show();

	}

	function js_layer_hide(goods_no) {
		$("#l_"+goods_no).hide();
	}


	function js_batch_modify() {
		var url = "/manager/goods/goods_batch_modify.php";
		var frm = document.frm;
		NewWindow('about:blank', 'modify_batch_popup', '860', '513', 'YES');
		frm.mode.value = "";
		frm.target = "modify_batch_popup";
		frm.action = url;
		frm.submit();
	}

	function js_link_to_item_ledger(goods_no, order_date) { 

		window.open("/manager/stock/item_ledger_list.php?nPage=1&nPageSize=20&search_field=GOODS_NO&start_date="+order_date+"&search_str=" + goods_no,'_blank');

	}

	function js_search_postback() {
		var frm = document.frm;
		
		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_goods_ordering_list(goods_no) {

		var url = "pop_goods_ordering_list.php?goods_no=" + goods_no;
		NewWindow(url,'pop_goods_ordering_list','800','450','YES');

	}

	/*
	function js_sub_goods_modify() {

		var url = "goods_sub_goods_modify.php";
		NewWindow('about:blank', 'sub_goods_modify_popup', '860', '513', 'YES');
		var frm = document.frm;
		frm.target = "sub_goods_modify_popup";
		frm.action = url;
		frm.submit();
	}
	*/

	function js_goods_proposal() { 

		var frm = document.frm;

		frm.target = "_blank";
		frm.method = "post";
		frm.action = "/manager/proposal/goods_proposal_write.php";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"?v="+(new Date()).getTime()+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		var win;
		$(".goods_thumb").click(function() {
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');
			window.setTimeout('check()',1000);

		});

		function check() {
			if(win.document.readyState =='complete'){
				win.document.execCommand("SaveAs");
				win.close();
			} else { 
				window.setTimeout('check();',1000);
			}
		}

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});
</script>

</head>

<body id="admin">
<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">

<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2>상품 관리 5678</h2>
				<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
				<? } ?>
				</div>
				<div class="category_choice">&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>카테고리</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $con_exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
							<input type="button" value="제외" onclick="js_exclude_category();"/>
							<input type="hidden" name="con_exclude_category" value="<?=$con_exclude_category?>"/>
							<span class="exception">
							<?
								if($con_exclude_category <> "") { 
									$max_index = 0;
									while($max_index <= strlen($con_exclude_category)) {
												
										if($max_index > 2)
											echo " > ";
										echo getCategoryNameOnly($conn, left($con_exclude_category, $max_index));

										$max_index += 2;

									}
							?>
							
							<a href="#" id="clear_exclude_category">(X)</a>
							</span>
							<?

								}
							?>
						</td>
						<td><input type="checkbox" name="con_use_tf" value="Y" <?if($con_use_tf == "Y") echo "checked";?>/>사용상품</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>등록일</th>
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
						<td colspan="2" align="right">
							
							<select name="print_type" style="width:84px;">
								<option value="FOR_REG" <? if ($print_type == "FOR_REG") echo "selected"; ?> >상품등록용서식</option>
								<option value="FOR_REG_NO_SUB" <? if ($print_type == "FOR_REG_NO_SUB") echo "selected"; ?> >상품등록용서식(세트제외)</option>
								<option value="FOR_PRINT" <? if ($print_type == "FOR_PRINT") echo "selected"; ?> >출력용</option>
								<option value="FOR_PRINT_NO_SUB" <? if ($print_type == "FOR_PRINT_NO_SUB") echo "selected"; ?> >출력용(세트제외)</option>
								<option value="FOR_CATALOG" <? if ($print_type == "FOR_CATALOG") echo "selected"; ?> >카달로그용</option>
								<option value="FOR_CATALOG_NO_SUB" <? if ($print_type == "FOR_CATALOG_NO_SUB") echo "selected"; ?> >카달로그용(세트제외)</option>
								<option value="DISPLAY" <? if ($print_type == "DISPLAY") echo "selected"; ?> >화면그대로</option>
							</select>&nbsp;
							
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr>
						<th>판매상태</th>
						<td>
							<?= makeSelectBox($conn,"GOODS_STATE","con_cate_04","125","선택","",$con_cate_04)?>
							&nbsp;&nbsp;
							<label>다음판매가 보기 <input type="checkbox" name="chk_next_sale_price" value="Y" <?if($chk_next_sale_price == "Y") echo "checked"; ?>/></label>
						</td>
						<th>과세구분</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","선택","",$con_tax_tf)?>
						</td>
					</tr>

					<tr>
						<th>판매가 </th>
						<td>
							<input type="text" value="<?=$start_price?>" name="start_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원 ~
							<input type="text" value="<?=$end_price?>" name="end_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원

							 (<input type="checkbox" name="chk_vendor" <? if($chk_vendor == "Y") echo "checked"; ?> value="Y"/> 벤더할인<input type="text" name="txt_vendor_calc" value="<?=$txt_vendor_calc?>" class="txt" style="width:25px;"/>%)
							
						</td>
						<th>공급업체</th>
						<td colspan="2">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cate_03)?>" />
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){
										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cate_03]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cate_03]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}

							</script>
							
						</td>
					</tr>

					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="BUY_PRICE" <? if ($order_field == "BUY_PRICE") echo "selected"; ?> >매입가</option>
								<option value="SALE_PRICE" <? if ($order_field == "SALE_PRICE") echo "selected"; ?> >판매가</option>
								<option value="EXTRA_PRICE" <? if ($order_field == "EXTRA_PRICE") echo "selected"; ?> >매입합계</option>
								<option value="CP_NAME" <? if ($order_field == "CP_NAME") echo "selected"; ?> >공급업체</option>
								<option value="STOCK_CNT" <? if ($order_field == "STOCK_CNT") echo "selected"; ?> >재고</option>
								<option value="UP_DATE" <? if ($order_field == "UP_DATE") echo "selected"; ?> >수정일</option>
								<option value="SALE_COUNT" <? if ($order_field == "SALE_COUNT") echo "selected"; ?> >#주문회수</option>
								<option value="SALE_AMOUNT" <? if ($order_field == "SALE_AMOUNT") echo "selected"; ?> >#주문수량</option>
								<option value="SALE_TOTAL" <? if ($order_field == "SALE_TOTAL") echo "selected"; ?> >#주문합계</option>
								<option value="VENDOR_PRICE" <? if ($order_field == "VENDOR_PRICE") echo "selected"; ?> >*벤더가(할인체크)</option>
								<option value="MAJIN" <? if ($order_field == "MAJIN") echo "selected"; ?> >*마진</option>
								<option value="MAJIN_RATE" <? if ($order_field == "MAJIN_RATE") echo "selected"; ?> >*마진률</option>
								<option value="CATALOG" <? if($order_field=="CATALOG") echo "selected"; ?> >**카탈로그</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순 &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림차순 
						</td>
						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="MEMO" <? if ($search_field == "MEMO") echo "selected"; ?> >비고란</option>
								<option value="GOODS_NAME_AND" <? if ($search_field == "GOODS_NAME_AND") echo "selected"; ?> >*상품명(AND)</option>
								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >*공급사코드</option>
								<option value="GOODS_SUB_NO" <? if ($search_field == "GOODS_SUB_NO") echo "selected"; ?> >*포함상품번호</option>
								<option value="GOODS_SUB_CODE" <? if ($search_field == "GOODS_SUB_CODE") echo "selected"; ?> >*포함상품코드</option>
								<option value="GOODS_SUB_CODE_AND" <? if ($search_field == "GOODS_SUB_CODE_AND") echo "selected"; ?> >*포함상품코드(AND)</option>
								<option value="GOODS_SUB_NAME_AND" <? if ($search_field == "GOODS_SUB_NAME_AND") echo "selected"; ?> >*포함상품명(AND)</option>
								<option value="SUB_CP_CODE" <? if ($search_field == "SUB_CP_CODE") echo "selected"; ?> >*포함공급사코드</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
	
				총 <?=number_format($nListCnt)?> 건

				<div style="float:right; margin-right:60px;">
					보기 : 
					<select name="view_type" onchange="js_search_postback();">
						<option value="price" <? if ($view_type == "price" || $view_type == "") echo "selected"; ?> >가격</option>
						<option value="stock" <? if ($view_type == "stock") echo "selected"; ?> >재고</option>
					</select>
				<!--(박스단위 주문)  VAT포함, 물류비포함 박스단위 미만 주문시 물류비별도-->
				</div>
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					

					<colgroup>
						<col width="2%" />
						<col width="5%" />
						<col width="5%" />
						<col width="8%" />
						<col width="*"/>
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<? if($view_type == "price" || $view_type == "") { ?>
							<!-- 보는 방식이 가격인 경우 -->
							<col width="5%" />
							<col width="7%" />
							<col width="5%"/>
							<col width="5%" />
							<col width="5%" />
						<?} else {?>
							<!-- 보는 방식이 재고인 경우 -->
							<col width="5%" />
							<col width="5%"/>
							<col width="5%" />
							<col width="6%" />
							<col width="10%" />
						<?}?>
					</colgroup>
					<thead>
						<? if($view_type == "price" || $view_type == "") { ?>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>공급사</th>
							<th>매입가</th>
							<th>매입합계</th>
							<th>
								<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
									<b>판매가</b>
								<? } else { ?>
									판매가
									<? if ($chk_vendor == "Y") {?>
										<br/><b>벤더가</b>사
									<? } ?>
									<? if($chk_next_sale_price == "Y") { ?>
										<br/><b>다음판매가</b>
									<? } ?>
								<? } ?>
							</th>
							<th>마진</th>
							<th>마진률</th>
							<th>MRO판매가</th>
							<th>박스입수</th>
							<?if($order_field == "SALE_COUNT") { ?>
								<th><b>주문회수</b></th>
							<? } else if ($order_field == "SALE_AMOUNT") {  ?>
								<th><b>주문수량</b></th>
							<? } else if ($order_field == "SALE_TOTAL") { ?>
								<th><b>주문합계</b></th>
							<? } else { ?>
								<th>재고</th>
							<? } ?>
							<th class="end">판매상태</th>
						</tr>
						<? } ?>
						<? if ($view_type == "stock") { ?>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>박스입수</th>
							<th>최소재고</th>
							<th>선출고</th>
							<th>가재고</th>
							<th>정상재고</th>
							<th>불량재고</th>
							<th>가용재고</th>
							<th>판매상태</th>
							<th>내역확인</th>
							<th class="end">최종조회시간</th>
						</tr>
						<? } ?>
					</thead>
					

					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
							$PRICE						= trim($arr_rs[$j]["PRICE"]);
							$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
							$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
							$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$IMG_URL					= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM					= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM					= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$CONTENTS					= trim($arr_rs[$j]["CONTENTS"]);
							$READ_CNT					= trim($arr_rs[$j]["READ_CNT"]);
							$DISP_SEQ					= trim($arr_rs[$j]["DISP_SEQ"]);
							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF						= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);

							$STOCK_TF				= trim($arr_rs[$j]["STOCK_TF"]);
							$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
							$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
							$MSTOCK_CNT				= trim($arr_rs[$j]["MSTOCK_CNT"]);

							//2018-11-30 주문회수, 주문수량, 주문합계
							$SALE_COUNT				= trim($arr_rs[$j]["SALE_COUNT"]);
							$SALE_AMOUNT			= trim($arr_rs[$j]["SALE_AMOUNT"]);
							$SALE_TOTAL				= trim($arr_rs[$j]["SALE_TOTAL"]);

							//echo "SALE_COUNT : ".$SALE_COUNT.", SALE_AMOUNT : ".$SALE_AMOUNT.", SALE_TOTAL : ".$SALE_TOTAL."<br/>";
	
							/* 2015 9월 08일 추가*/
							$STICKER_PRICE		= trim($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE		= trim($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE		= trim($arr_rs[$j]["DELIVERY_PRICE"]); 

							/* 2016 2월 18일 추가*/
							$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= trim($arr_rs[$j]["OTHER_PRICE"]);

							//2016-10-20 추가
							$RESTOCK_DATE			= trim($arr_rs[$j]["RESTOCK_DATE"]);

							$NEXT_SALE_PRICE		= trim($arr_rs[$j]["NEXT_SALE_PRICE"]);

							$str_goods_no = $GOODS_TYPE.substr("000000".$GOODS_NO,-5);
							$ACCESS_DATE = trim($arr_rs[$j]["UP_DATE"]);
							$ACCESS_DATE = date("Y-m-d",strtotime($ACCESS_DATE));

							$convertedGoodsCode	=	str_replace("-", "_", $GOODS_CODE);
							$detailURL			=	$_SERVER['DOCUMENT_ROOT']."/upload_data/goods_image/detail/".$convertedGoodsCode.".jpg";

							if(file_exists($detailURL)){
								$DETAIL_TF="DETAIL_EXISTS";
							}
							else{
								$DETAIL_TF="";
							}
							?>

							<?

							//echo $IMG_URL;

							// 이미지가 저장 되어 있을 경우
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>사용중</font>";
							} else {
								$STR_USE_TF = "<font color='red'>사용안함</font>";
							}

							if ($TAX_TF == "비과세") {
								$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
							} else {
								$STR_TAX_TF = "<font color='navy'>(과세)</font>";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if($RESTOCK_DATE <> '0000-00-00 00:00:00' && $CATE_04 != "단종")
								$RESTOCK_DATE = "<span style='color:#0047ab;'>(".date("Y-m-d",strtotime($RESTOCK_DATE)).")</span>";
							else
								$RESTOCK_DATE = '';
							
							if($BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX) + $LABOR_PRICE + $OTHER_PRICE != $PRICE)
								$str_wrong_style = "wrong";
							else
								$str_wrong_style = "";


							if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
								$DELIVERY_PER_PRICE = 0;
							else 
								$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
							
							$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

							$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

							if($SALE_PRICE != 0)
								$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2)."%";
							else 
								$MAJIN_PER = "계산불가";
							
							if($USE_TF == "N")
								$str_use_style = "unused";
							else { 
								if($CATE_04 != "판매중")
									$str_use_style = "expired";
								else
									$str_use_style = "";
							}

							if($chk_vendor == "Y") {
								$DC_RATE = $txt_vendor_calc;
								$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
							} else {
								$DC_RATE = "";
								$VENDER_PRICE = $SALE_PRICE;
							}

							if($view_type == "stock")
								$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);
							else
								$TSTOCK_CNT = 0;
							
				
				?>

						<? if($view_type == "price" || $view_type == "") { ?>
						<tr class="<?=$str_use_style?> <?=$str_wrong_style?>" >
							<td>
								<input type="checkbox" name="chk_no[]" class="chk" value="<?=$GOODS_NO?>">
							</td>
							<td class="<?=$DETAIL_TF?>">
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><a href="javascript:js_view_company(<?=$CATE_03?>)"><?= getCompanyName($conn, $CATE_03);?></a></td>
							<td class="price"><?= number_format($BUY_PRICE) ?> 원</td>
							<td class="price"><?= number_format($PRICE) ?> 원</td>
							<td class="price">

								<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
									<b><?= number_format($SALE_PRICE) ?> 원</b>
								<? } else { ?>
									<?= number_format($SALE_PRICE) ?> 원
									<? if ($chk_vendor == "Y") {?>
										<br/><b><?= number_format($VENDER_PRICE)?> 원</b>
									<? } ?>
									<?  if($chk_next_sale_price == "Y") { 
											if($NEXT_SALE_PRICE == "") { 
									?>
										<br/><b>미정</b>
									<?	    } else { ?>
										<br/><b><?= getSafeNumberFormatted($NEXT_SALE_PRICE) ?> 원</b>
									<?      }
										}
									?>
								<? } ?>
							</td>
							<td class="price" title="수수료:<?=$SUSU_PRICE?>"><?= number_format($MAJIN) ?> 원</td>
							<td class="price" title="수수료:<?=$SUSU_PRICE?>"><?=$MAJIN_PER?></td>
							<td class="price" title="MRO판매가:<?=$MRO_SALE_PRICE?>"><?= number_format($MRO_SALE_PRICE) ?> 원</td>
							<td><?= $DELIVERY_CNT_IN_BOX ?></td>

							<? if($order_field == "SALE_COUNT") { ?>
								<td class="price"><b><?= number_format($SALE_COUNT) ?></b></td>
							<? } else if ($order_field == "SALE_AMOUNT") {  ?>
								<td class="price"><b><?= number_format($SALE_AMOUNT) ?></b></td>
							<? } else if ($order_field == "SALE_TOTAL") {  ?>
								<td class="price"><b><?= number_format($SALE_TOTAL) ?></b></td>
							<? } else { ?>
								<td class="price"><?= number_format($STOCK_CNT) ?></td>
							<? } ?>

							
							<td class="filedown">
								<?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?>
								<? if($CATE_04 != "판매중") {?>	
									<br/><?=$RESTOCK_DATE?>
								<? } ?>
							</td>
						</tr>
						<? } ?>
						<? if ($view_type == "stock") { ?>
						<tr class="<?=$str_use_style?> <?=$str_wrong_style?>" >
							<td>
								<input type="checkbox" name="chk_no[]" value="<?=$GOODS_NO?>">
							</td>
							<td>
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="클릭하시면 새 창에 원본 이미지가 열립니다." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><?= $DELIVERY_CNT_IN_BOX ?></td>
							<td class="price"><?= number_format($MSTOCK_CNT) ?> </td>
							<td class="price"><?= ($TSTOCK_CNT > 0 ? "<a href=\"javascript:js_pop_goods_ordering_list('".$GOODS_NO."');\">-".number_format($TSTOCK_CNT)."</a>" : 0) ?> </td>
							<td class="price"><?= number_format($FSTOCK_CNT) ?> </td>
							<td class="price"><?= number_format($STOCK_CNT) ?> </td>
							<td class="price"><?= number_format($BSTOCK_CNT) ?> </td>
							<td class="price"><b><?= number_format($STOCK_CNT + $FSTOCK_CNT - $TSTOCK_CNT) ?></b> </td>
							<td class="filedown">
								<?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?>
								<? if($CATE_04 != "판매중") {?>	
									<br/><?=$RESTOCK_DATE?>
								<? } ?>
							</td>
							<td class="modeual_nm">
								<input type="button" name="b" value="조회" class="btntxt" onclick="js_link_to_item_ledger('<?=$GOODS_NO?>', '<?=$REG_DATE?>');">
							</td>
							<td class="modeual_nm"><?=$ACCESS_DATE?></td>
							
						</tr>

						<? } ?>
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="14">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					
					<? if ($sPageRight_D == "Y") {?>
						<input type="button" name="aa" value="선택한 상품" class="btntxt" onclick="js_state_mod();">
						<?= makeSelectBox($conn,"GOODS_STATE","goods_state_mod","125","상태선택","","")?>
						<input type="button" name="aa" value="으로 변경" class="btntxt" onclick="js_state_mod();">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<input type="button" name="aa" value=" 선택한 상품 복사 " class="btntxt" onclick="js_copy();"> 
						<input type="button" name="aa" value=" 선택한 상품 삭제 " class="btntxt" onclick="js_delete();">
					<? } ?>
				</div>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<input type="button" name="" value=" 선택한 상품내역 이미지로 저장 " class="btntxt" id ="make_image_btn">
					<input type="button" name="" value=" 선택한 상품내역 링크로 만들기 " class="btntxt" id ="make_link_btn">
					<input type="button" name="aa" value=" 선택한 상품내역 제안하기 " class="btntxt" onclick="js_goods_proposal();">
					<input type="button" name="aa" value="선택한 상품내역 일괄변경" class="btntxt" onclick="js_batch_modify();">
					<input type="button" name="btnAccess" value="선택한 상품 최신화 확인" class="btntxt">
				</div>

				
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
							$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf."&chk_vendor=".$chk_vendor."&txt_vendor_calc=".$txt_vendor_calc."&view_type=".$view_type."&con_exclude_category=".$con_exclude_category."&chk_next_sale_price=".$chk_next_sale_price;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<div class="sp50"></div>
			<!--<? if ($sPageRight_F == "Y") {?>
				<div><input type="button" name="aa" value=" 다음 전단지 가격 기준 판매가 일괄 업데이트 " class="btntxt" onclick="js_update_next_sale_price();"> </div>
				<div class="sp50"></div>
			<? } ?>--	//20210805 효곤대리요청 버튼 제외 시킴.		>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<script>

		$(function(){

			$("#clear_exclude_category").click(function(e){

				e.preventDefault();
				$("input[name=con_exclude_category]").val('');
				$(".exception").html('');

				js_search();

			});

			//전체 로딩전 클릭 방지
			$("input[name=all_chk]").show();

			$("input[name=txt_vendor_calc]").keyup(function(){
				if($(this).val() != "")
					$("input[name=chk_vendor]").prop("checked", true);
				else
					$("input[name=chk_vendor]").prop("checked", false);
			});


			var last_click_idx = -1;
			$(".chk").click(function(event){
				
				var clicked_elem = $(this);
				var clicked_elem_chked = $(this).prop("checked");

				var start_idx = -1;
				var end_idx = -1;
				var click_idx = -1;

				$(".chk").each(function( index, elem ) {

					//클릭위치 저장
					if(clicked_elem.val() == $(elem).val())
						click_idx = index;

				});

				if(event.shiftKey) {

					if($(".chk:checked").size() >= 2) {
						$(".chk").each(function( index, elem ) {

							//체크된 곳의 시작 체크
							if(start_idx == -1 && $(elem).prop("checked"))
								start_idx = index;

							//체크의 마지막 인덱스 체크
							if($(elem).prop("checked"))
								end_idx = index;

						});

						if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
							start_idx = click_idx;

						if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
							end_idx = click_idx;


						//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

						
						$(".chk").each(function(index, elem) {

							if(start_idx <= index && index <= end_idx) {
								$(elem).prop("checked", true);
							}
							else
								$(elem).prop("checked", false);
							
						});
						
					}

					last_click_idx = click_idx;
				}

			});

		});
	
	</script>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>