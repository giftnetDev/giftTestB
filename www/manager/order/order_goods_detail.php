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
	$menu_right = "GD005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/cart/cart.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {

		$cart_seq = 0;
		$use_tf		= "Y";

		$s_ord_no = get_session('s_ord_no');

		$opt_sticker_no				= trim($opt_sticker_no);
		$opt_sticker_msg			= trim($opt_sticker_msg);
		$opt_outbox_tf				= trim($opt_outbox_tf);

		$opt_wrap_no				= trim($opt_wrap_no);
		$opt_print_msg				= trim($opt_print_msg);
		$opt_outstock_date			= trim($opt_outstock_date);
		$opt_memo					= trim($opt_memo);
		$opt_request_memo			= trim($opt_request_memo);
		$opt_support_memo			= trim($opt_support_memo);
		
		$delivery_type				= trim($delivery_type);

		$cate_01					= trim($cate_01);

		$price						= trim($price);
		$sticker_price				= trim($sticker_price);
		$print_price				= trim($print_price);
		$sale_susu					= trim($sale_susu);
		$delivery_cnt_in_box		= trim($delivery_cnt_in_box);
		$labor_price				= trim($labor_price);
		$other_price				= trim($other_price);

		$delivery_cnt_in_box		= str_replace(",", "", $delivery_cnt_in_box);
		$discount_price				= str_replace(",", "", $discount_price);
		$sale_price					= str_replace(",", "", $sale_price);
		$sale_total					= str_replace(",", "", $sale_total);
		$price						= str_replace(",", "", $price);
		$buy_price					= str_replace(",", "", $buy_price);
		$sticker_price				= str_replace(",", "", $sticker_price);
		$print_price				= str_replace(",", "", $print_price);
		$sa_delivery_price			= str_replace(",", "", $sa_delivery_price);
		$labor_price				= str_replace(",", "", $labor_price);
		$other_price				= str_replace(",", "", $other_price);

		//2017-03-30 MRO만 수수료, 수수율 적용 - 일단 하드코딩
		//if($is_mall)
		if($cp_no == 1480) 
			$susu_price = round($sale_price / 100.0 * $sale_susu, 4);
		else {
			$susu_price = 0;
			$sale_susu = 0;
		}

		$qty = str_replace(",", "", $qty);
		$extra_price = $susu_price;


		if($reserve_no <> "") { 
			//기존 주문에 상품 추가

			$arr_rs = selectOrder($conn, $reserve_no);
			$ON_UID				= $arr_rs[0]["ON_UID"];
			$MEM_NO				= $arr_rs[0]["MEM_NO"];

			$new_mem_no = $MEM_NO;

			$arr_rs_goods = selectGoods($conn, $goods_no);
			$TAX_TF				= trim($arr_rs_goods[0]["TAX_TF"]); 
			$order_state		= "1"; 
			$use_tf					= "Y";

			$order_seq = selectNextOrderGoodsSeq($conn, $reserve_no) + 1;

			$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

			$result = insertOrderGoods($conn, $ON_UID, $reserve_no, $cp_order_no, $buy_cp_no, $new_mem_no, $order_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $TAX_TF, $order_state, $use_tf, $s_adm_no);

			resetOrderInfor($conn, $reserve_no);

		} else {

			$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

			//원래 주문등록으로 입력
			$result = insertCart($conn, $s_ord_no, $cp_order_no, $cp_no, $buy_cp_no, $cart_seq, $goods_no, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);
		}
		//exit;
	}
	
	if ($result) {

		if($reserve_no <> "") { 
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	//window.opener.location.reload();
	parent.js_reload_list("STOP");
</script>
<?
		} else { 
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	parent.js_reload_list('<?=$keep?>');
</script>
<?
		}
		mysql_close($conn);
		exit;
	}

	
	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name		= SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01				= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02				= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03				= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04				= trim($arr_rs[0]["CATE_04"]); 
		$rs_price				= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt			= trim($arr_rs[0]["STOCK_CNT"]); 
		//$rs_tstock_cnt			= trim($arr_rs[0]["TSTOCK_CNT"]); 
		$rs_fstock_cnt			= trim($arr_rs[0]["FSTOCK_CNT"]);
		$rs_img_url				= trim($arr_rs[0]["IMG_URL"]); 
		$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
		$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
		$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
		$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
		$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
		$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
		$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
		$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
		$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
		$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
		$rs_contents			= trim($arr_rs[0]["CONTENTS"]); 
		$rs_read_cnt			= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq			= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		$content				= trim($arr_rs[0]["CONTENTS"]); 
		$rs_tax_tf				= trim($arr_rs[0]["TAX_TF"]); 

		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
		$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 
		$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 
		$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]);

		//$arr_rs_file = selectGoodsFile($conn, $goods_no);
		//$arr_rs_option = selectGoodsOption($conn, $goods_no);

		if ($rs_tax_tf == "비과세") {
			$STR_TAX_TF = "<font color='orange'>비과세</font>";
		} else {
			$STR_TAX_TF = "<font color='navy'>과세</font>";
		}
		
		$img_url	= getGoodsImage($rs_file_nm_100, $rs_img_url, $rs_file_path_150, $rs_file_rnm_150, "250", "250");

		$arr_cp = selectCompany($conn, $cp_no);
		$CP_CATE	= $arr_cp[0]["CP_CATE"];
		$CP_NM		= $arr_cp[0]["CP_NM"];
		$CP_PHONE	= $arr_cp[0]["CP_PHONE"];
		$DC_RATE	= $arr_cp[0]["DC_RATE"];
		$IS_MALL	= $arr_cp[0]["IS_MALL"];
		
		//업체 할인율 적용전 단가표의 판매가격 표기
		$origin_sale_price = $rs_sale_price;
		
		//업체 할인율 적용
		if ($cp_no <> "") {
			$rs_sale_price = getCompanyGoodsPriceOrDCRate($conn, $rs_goods_no, $rs_sale_price, $rs_price, $cp_no);
		}

		$arr_rs_sub = selectGoodsSub($conn, $goods_no);

		$arr_rs_goods_extra = selectGoodsExtra($conn, $goods_no, '');

		if(sizeof($arr_rs_goods_extra) > 0) { 
			for($i = 0; $i < sizeof($arr_rs_goods_extra); $i++) { 
			
				$rs_pcode			= SetStringFromDB($arr_rs_goods_extra[$i]["PCODE"]); 
				if($rs_pcode == "GOODS_STICKER_SIZE")
					$rs_sticker_size		= SetStringFromDB($arr_rs_goods_extra[$i]["DCODE"]); 

				if($rs_pcode == "WRAP_CODE") { 
					$rs_wrap_code			= SetStringFromDB($arr_rs_goods_extra[$i]["DCODE"]); 
				}
			}

		} else { 
			$rs_sticker_size = "";
			$rs_wrap_code = "";
			$rs_wrap_no = "";
		}

		$is_mall = ($IS_MALL == "Y");

		$rs_tstock_cnt = getCalcGoodsInOrdering($conn, $goods_no);
		
		//할인율이 적용되어있다면 벤더이므로 벤더명으로 송장 기본세팅
		if($DC_RATE <= 0) { 
			$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);
			$CP_NM		= $arr_op_cp[0]["CP_NM"];
			$CP_PHONE	= $arr_op_cp[0]["CP_PHONE"];
		}
		$arr_company_etc = listCompanyEtc($conn, $rs_cate_03);

	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/board.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="../jquery/theme.css" type="text/css" />
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script>
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
		minDate : 0,
		numberOfMonths: 2
    });

  	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkDt($("input[name=opt_outstock_date]"));
	});


    $( ".accordion" ).accordion({
		collapsible: true,
		active: false
    });

  });
</script>
<script>
	$(function() {
		$("#tabs").tabs({
		  active: 0
		});
	});
</script>
<script type="text/javascript">

	function js_add_goods () {
		
		if (document.frm.qty.value.trim() == "") {
			alert("주문 수량을 입력해 주십시오");
			document.frm.qty.focus();
			return;
		}

		if (document.frm.qty.value.trim() < 1) {
			alert("주문 수량은 1 보다 커야 합니다.");
			document.frm.qty.focus();
			return;
		}

		//작업지시서에 계속 떠있게 하기 위해서 -> 변경 대량건을 제외하고 출고일 반드시 지정
		if(!document.frm.bulk_tf.checked) { 
			if (document.frm.opt_outstock_date.value.length == 0) {
				
				alert("대량건을 제외하고 출고예정일을 지정해주십시오.");
				document.frm.opt_outstock_date.focus();
				return;
			} else { 

				var diff = new Date(new Date() - new Date(document.frm.opt_outstock_date.value));
				var days = diff/1000/60/60/24;

				if(days > 1) {
					alert('출고일은 오늘 이후로 가능합니다');
					return;
				}

			}
		}

		if (document.frm.delivery_type.value == "") {
			alert("배송방식을 지정해주십시오.");
			document.frm.delivery_type.focus();
			return;
		}

		document.frm.mode.value = "I";
		document.frm.keep.value = "GO";
		document.frm.target = "";
		document.frm.action = "<?=$_SERVER[PHP_SELF]?>";
		document.frm.submit();

	}

	function js_add_goods_close () {
		
		if (document.frm.qty.value.trim() == "") {
			alert("주문 수량을 입력해 주십시오");
			document.frm.qty.focus();
			return;
		}

		if (document.frm.qty.value.trim() < 1) {
			alert("주문 수량은 1 보다 커야 합니다.");
			document.frm.qty.focus();
			return;
		}

		//작업지시서에 계속 떠있게 하기 위해서 -> 변경 대량건을 제외하고 출고일 반드시 지정
		if(!document.frm.bulk_tf.checked) { 
			if (document.frm.opt_outstock_date.value.length == 0) {
				
				alert("대량건을 제외하고 출고예정일을 지정해주십시오.");
				document.frm.opt_outstock_date.focus();
				return;
			} else { 

				var diff = new Date(new Date() - new Date(document.frm.opt_outstock_date.value));
				var days = diff/1000/60/60/24;

				if(days > 1) {
					alert('출고일은 오늘 이후로 가능합니다');
					return;
				}

			}
		}


		if (document.frm.delivery_type.value == "") {
			alert("배송방식을 지정해주십시오.");
			document.frm.delivery_type.focus();
			return;
		}
		
		document.frm.mode.value = "I";
		document.frm.keep.value = "STOP";
		document.frm.target = "";
		document.frm.action = "<?=$_SERVER[PHP_SELF]?>";
		document.frm.submit();

	}

<? if ($s_adm_cp_type == "운영") { ?>
	// 원가 계산
	function js_calculate_all_price() {
		
		var i_sale_price		= 0;
		var i_buy_price			= 0;
		var i_sticker_price = 0;
		var i_print_price		= 0;
		var i_delivery_cnt_in_box = 1;
		var i_delivery_price = 0;
		var f_sale_susu = 0;
		var i_delivery_per_price = 0;
		var i_total_wonga = 0;
		var i_susu_price = 0;
		var i_labor_price = 0;
		var i_other_price = 0;
		var i_majin	= 0;
		var f_majin_per	= 0;
		var i_discount	= 0;
		var i_sale_total	= 0;
		var i_qty = 1;

		// 벤더 계산용 원 상품 판매가, 상품관리와 다름
		var i_origin_sale_price	= 0;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val().replace(",", ""));
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val().replace(",", ""));
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val().replace(",", ""));
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val().replace(",", ""));
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val().replace(",", ""));
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val().replace(",", ""));
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=qty]").val() != "") i_qty = parseInt($("input[name=qty]").val().replace(",", ""));
		if ($("input[name=discount_price]").val() != "") i_discount = parseInt($("input[name=discount_price]").val().replace(",", ""));
		if ($("input[name=sale_total]").val() != "") i_sale_total = parseInt($("input[name=sale_total]").val().replace(",", ""));
		if ($("input[name=labor_price]").val() != "") i_labor_price = parseInt($("input[name=labor_price]").val());
		if ($("input[name=other_price]").val() != "") i_other_price = parseInt($("input[name=other_price]").val());

		// 벤더 계산용 원 상품 판매가, 상품관리와 다름
		if ($("input[name=origin_sale_price]").val() != "") i_origin_sale_price = parseInt($("input[name=origin_sale_price]").val().replace(",", ""));
		
		i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		<? if($is_mall) { ?>
		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));
		$("input[name=susu_price]").val(i_susu_price);
		<? } else { ?>
			i_susu_price = 0;
			$("#susu_price").html("0");
			$("input[name=susu_price]").val("0");
		<? } ?>


		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price + i_labor_price + i_other_price;
		$("#total_wonga").val(numberFormat(i_total_wonga));
		
		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		$("#majin").html(numberFormat(i_majin));
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round10((i_majin / i_sale_price) * 100, -2);
			$("#majin_per").html(f_majin_per);
		} else {
			if (i_majin == 0) {
				f_majin_per = 0
				$("#majin_per").html(f_majin_per);
			} else {
				f_majin_per = -100
				$("#majin_per").html(f_majin_per);
			}
		}
		i_sale_total = i_sale_price * i_qty - i_discount;
		$("#sale_total").val(numberFormat(i_sale_total));

		//구성상품 계산
		<? if(sizeof($arr_rs_sub) > 0) {?>

			var has_error = false;
			$(".error_msg").hide();
			
			$(".sub_goods_cnt").each(function( index, value ){
				var each_value = parseInt($(value).data("goods_cnt"));
				var each_goods_cate = String($(value).data("goods_cate"));
				var total_order_qty = 1;

				if(each_goods_cate.startsWith("010202")) 
					total_order_qty = Math.ceil10((i_qty * each_value) / i_delivery_cnt_in_box, 0);
				else
					total_order_qty = i_qty * each_value;

				$(value).html(numberFormat(total_order_qty));

				var stock_cnt = $(this).closest("tr").find(".sub_stock_cnt").data("stock_cnt");

				if(total_order_qty > stock_cnt || stock_cnt <= 0) { 
					$(this).closest("tr").find(".sub_stock_cnt").css("color","#f00");
					has_error = true;
				} else { 
					$(this).closest("tr").find(".sub_stock_cnt").css("color","#000");
				}
			});

			if(has_error) { 
				$(".error_msg").show();
			}

		<? } else { ?>

			var single_stock_cnt = $(".single_stock_cnt").data("single_stock_cnt");
			if(i_qty > single_stock_cnt || single_stock_cnt <= 0) { 
				$(".single_stock_cnt").css("color","#f00");
				$(".error_msg").show();
			} else {
				$(".single_stock_cnt").css("color","#000");
				$(".error_msg").hide();
			}

		<? } ?>

		// 벤더 계산용 원 상품 판매가, 상품관리와 다름
		var i_vender_calc = 0;
		if(i_origin_sale_price > 0) { 
			
			if ($("input[name=vendor_calc]").val() != "") i_vender_calc = parseInt($("input[name=vendor_calc]").val());
			var vendor15 = Math.ceil10(((i_origin_sale_price - i_total_wonga) * 15 / 100.0 + i_total_wonga) , 1);
			var vendor35 = Math.ceil10(((i_origin_sale_price - i_total_wonga) * 35 / 100.0 + i_total_wonga) , 1);
			var vendor_calc = Math.ceil10(((i_origin_sale_price - i_total_wonga) * i_vender_calc / 100.0 + i_total_wonga) , 1);

			$("#vendor15").html(numberFormat(vendor15));
			$("#vendor35").html(numberFormat(vendor35));
			$("#vendor_calc").html(numberFormat(vendor_calc));
		} else { 
			$("#vendor15").html("0");
			$("#vendor35").html("0");
			$("#vendor_calc").html("0");
		}
		
	}

<? } else { ?>
	function js_calculate_all_price() {
		
		var i_sale_price	= 0;
		var i_sale_total	= 0;
		var i_qty = 1;

		if ($("input[name=qty]").val() != "") i_qty = parseInt($("input[name=qty]").val().replace(",", ""));
		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val().replace(",", ""));
		
		
		i_sale_total = i_sale_price * i_qty;
		$("span[name=sale_total]").html(numberFormat(i_sale_total));

	}
<? } ?>

</script>
</head>
<body>
<div class="sp10"></div>
<table cellpadding="0" cellspacing="0" width="100%">
<form name="frm" method="post">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="goods_no" value="<?=$rs_goods_no?>">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="extra_price" value="<?=$rs_extra_price?>">
<input type="hidden" name="buy_cp_no" value="<?=$rs_cate_03?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keep" value="">
<input type="hidden" name="is_mall" value="<?=$is_mall?>">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02" border="0">

			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>
				<tr>
					<td style="padding: 5px 5px 5px 5px" class="line" colspan="2" rowspan="<?= ($s_adm_cp_type == "운영" ? "11" : "5") ?>">
						<img src="<?=$img_url?>" border="0" width="250" height="250">
					</td>
					<th>상품명</th>
					<td colspan="5" class="line">
						<?=$rs_goods_name?>
						<input type="hidden" name="goods_code" value="<?=$rs_goods_code?>"/>
						<input type="hidden" name="goods_name" value="<?=$rs_goods_name?>"/>
						<input type="hidden" name="goods_sub_name" value="<?=$rs_goods_sub_name?>"/>
					</td>
				</tr>
				<? if ($s_adm_cp_type == "운영") { ?>
				<tr>
					<th>주문상품종류</th>
					<td class="line">
						<?=makeSelectBox($conn, "ORDER_GOODS_TYPE", "cate_01", "100", "선택", "", $rs_cate_01)?>
						<script>
							$(function(){
								$("select[name=cate_01").change(function(){

									if($(this).val() == "추가") { 
										$("input[name=opt_outstock_date]").val('<?=date("Y-m-d", strtotime("15 day"))?>'); //출고예정일 +15일
										$("input[name=bulk_tf]").prop("checked", false); //출고미지정 해제
										$("select[name=delivery_type]").val("99"); //기타
										$("select[name=delivery_cp]").hide(); //택배회사 가리기
									}

								});
							});
						</script>
					</td>
				</tr>
				<tr>
					<th>공급업체</th>
					<td class="line">
						<?= getCompanyName($conn, $rs_cate_03);?>
					</td>
				</tr>
				<tr>
					<th>과세여부</th>
					<td class="line">
						<?=$STR_TAX_TF?>
					</td>
				</tr>
				<tr>
					<th>정상재고</th>
					<td class="line">
						<?= getSafeNumberFormatted($rs_stock_cnt) ?> 개
					</td>
				</tr>
				<tr>
					<th title="정상재고 - 선출고 + 가입고">가용재고</th>
					<td class="line">
						<span class="single_stock_cnt" data-single_stock_cnt="<?=$rs_stock_cnt - $rs_tstock_cnt + $rs_fstock_cnt?>"><?= getSafeNumberFormatted($rs_stock_cnt - $rs_tstock_cnt + $rs_fstock_cnt) ?> 개</span> &nbsp;<?= "<font>(선주문 ".getSafeNumberFormatted($rs_tstock_cnt)."개, 입고예정 ".getSafeNumberFormatted($rs_fstock_cnt)."개)</font>"?>
					</td>
				</tr>
				<tr>
					<th>박스입수</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="delivery_cnt_in_box" value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/>

					</td>
				</tr>
				
				<tr>
					<th>수량</th>
					<td class="line">
						<input type="text" id="qty" class="txt" style="width:75px" name="qty" value="1" required onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/> 개 &nbsp;&nbsp;<span class="error_msg" style="color:red; display:none;">재고가 부족합니다.</span>
					</td>
				</tr>

				<tr>
					<th>판매가</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="sale_price" value="<?=number_format($rs_sale_price) ?>" onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"> 원
						<input type="hidden" name="origin_sale_price" value="<?=$origin_sale_price?>"/>
					</td>
				</tr>
				<tr>
					<th>할인금액</th>
					<td class="line">
						<input type="text" id="discount_price" class="txt" style="width:90px" name="discount_price" value="0" onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/> 원
					</td>
				</tr>
				<tr>
					<th>총 판매가</th>
					<td class="line">
						<input type="text" id="sale_total" class="txt" style="width:90px" name="sale_total" value="0" onkeyup="return isNumber(this)" readonly/> 원
						

					</td>
				</tr>
			</table>

			<div class="sp10"></div>
			<div class="accordion" style="width:94%">
				<h3>단가정보</h3>
				<table cellpadding="0" cellspacing="0" width="100%" class="colstable02 " border="0">

				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
					<tr>
						<th>매입가</th>
						<td class="line">
							<input type="hidden" name="buy_price" value="<?=$rs_buy_price?>"/>
							<?=number_format($rs_buy_price)?> 원
						</td>
						<th>밴더할인 15%</th>
						<td class="line">
							<span id="vendor15"></span>원
						</td>
					</tr>
					<tr>
						<th>스티커비용</th>
						<td class="line">
							<input type="hidden" name="sticker_price" value="<?=$rs_sticker_price?>"/>
							<?=number_format($rs_sticker_price)?> 원

						</td>
						<th>밴더할인 35%</th>
						<td class="line">
							<span id="vendor35"></span>원
						</td>
					</tr>
					<tr>
						<th>포장인쇄비용</th>
						<td class="line">
							<input type="hidden" name="print_price" value="<?=$rs_print_price?>"/>
							<?=number_format($rs_print_price)?> 원

						</td>
						<th>밴더할인 <input type="text" name="vendor_calc" value="55" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
						<td class="line">
							<span id="vendor_calc"></span>원
						</td>
					</tr>
					<tr>
						<th title="왕복택배비 / 박스입수">물류비</th>
						<td class="line"  colspan="<?=($is_mall? "" : "3") ?>">
							<input type="hidden" name="delivery_price" value="<?=$rs_delivery_price?>"/>
							<span id="delivery_per_price">0</span> 원 (택배비용 : <?=number_format($rs_delivery_price)?> 원)

						</td>
						<? if($is_mall) { ?>
						<th>판매 수수률</th>
						<td class="line">
							<input type="hidden" name="sale_susu" value="<?=$rs_sale_susu?>"/>
							<?= $rs_sale_susu ?> % 

						</td>
						<? } ?>
						
					</tr>
					<tr>
						<th>인건비</th>
						<td class="line"  colspan="<?=($is_mall? "" : "3") ?>">
							<input type="hidden" name="labor_price" value="<?=$rs_labor_price?>"/>
							<?=number_format($rs_labor_price)?> 원
						</td>
						<? if($is_mall) { ?>
						<th title="(판매가 * 100) * 판매 수수률">판매 수수료</th>
						<td class="line">
							  <span id="susu_price">0</span> 원
							  <input type="hidden" name="susu_price" value="0"/>
						</td>
						<? } ?>
					</tr>
					<tr>
						<th>기타비용</th>
						<td class="line">
							<input type="hidden" name="other_price" value="<?=$rs_other_price?>"/>
							<?=number_format($rs_other_price)?> 원
						</td>
						<th title="판매가 - 판매수수료 - 매입합계">마진</th>
						<td class="line">
							<span id="majin">0</span> 원
						</td>
					</tr>
					<tr>
						<th title="매입가 + 스티커비용 + 포장인쇄비용 + 물류비">매입합계</th>
						<td class="line">
							<input type="hidden" name="price" value="<?=$rs_price?>"/>
							<?=number_format($rs_price)?> 원
						</td>
						<th title="마진 / 판매가 * 100">마진률</th>
						<td class="line">
							<span id="majin_per">0</span> %
						</td>
					</tr> 
					<? } else { ?>
					<tr>
						<th>과세여부</th>
						<td class="line">
							<?=$STR_TAX_TF?>
						</td>
					</tr>
					<tr>
						<th>수량</th>
						<td class="line">
							<input type="text" id="qty" class="txt" style="width:75px" name="qty" value="1" required onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/> 개 &nbsp;&nbsp;<span class="error_msg" style="color:red; display:none;">재고가 부족합니다.</span>
						</td>
					</tr>
					<tr>
						<th>판매가</th>
						<td class="line">
							<?=number_format($rs_sale_price) ?> 원
							<input type="hidden" name="sale_price" value="<?=$rs_sale_price?>"/>
							<input type="hidden" name="origin_sale_price" value="<?=$origin_sale_price?>"/>
						</td>
					</tr>
					<tr>
						<th>총 판매가</th>
						<td class="line">
							<span name="sale_total">0</span> 원
						</td>
					</tr>
					<? } ?>

					<tr>
						<td style="padding: 5px 5px 5px 5px" colspan="5">
							<? $rs_contents = preg_replace("/(\<img )([^\>]*)(\>)/i", "\\1 name='target_resize_image[]' onclick='image_window(this)' style='cursor:pointer;' \\2 \\3", $rs_contents);?>
							<?=$rs_contents?>
						</td>
					</tr>
					<tr>
						<td colspan="10"></td>
					</tr>
				</table>
			</div>
			<? if ($s_adm_cp_type == "운영") { ?>
			<div class="sp10"></div>
			* 구성 상품
			<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
				<!--<col width="5%" />-->
					<col width="15%" />
					<col width="*" />
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
				</colgroup>
				<tr>
					<th>코드</th>
					<th>상품명</th>
					<th>매입가</th>
					<th>구성수량</th>
					<th>선출고</th>
					<th>가재고</th>
					<th>정상재고</th>
					<th class="end">가용재고</th>
				</tr>
				<?
					if(sizeof($arr_rs_sub) > 0) {
						for($i = 0; $i < sizeof($arr_rs_sub); $i++)
						{
							$sub_goods_sub_no = $arr_rs_sub[$i]["GOODS_SUB_NO"];
							$sub_goods_code = $arr_rs_sub[$i]["GOODS_CODE"];
							$sub_goods_name = $arr_rs_sub[$i]["GOODS_NAME"];
							$sub_buy_price	= $arr_rs_sub[$i]["BUY_PRICE"];
							$sub_goods_cnt	= $arr_rs_sub[$i]["GOODS_CNT"];
							$sub_stock		= $arr_rs_sub[$i]["STOCK_CNT"];
							//$sub_tstock		= $arr_rs_sub[$i]["TSTOCK_CNT"];
							$sub_fstock		= $arr_rs_sub[$i]["FSTOCK_CNT"];
							$sub_goods_cate = $arr_rs_sub[$i]["GOODS_CATE"];

							//2017-03-28 아웃박스등 속도 문제로 클릭시 계산으로 수정 필요
							if($sub_goods_cate != "010202")
								$sub_tstock = getCalcGoodsInOrdering($conn, $sub_goods_sub_no);
							else
								$sub_tstock = 0;

							//세트의 인박스케이스가 있을 경우에 인박스용 스티커 사이즈를 가져 옴, 세트 자체에 설정이 되어있다면 패스
							if($sub_goods_cate == "010203" && $rs_sticker_size == "") { 

								$arr_rs_goods_extra = selectGoodsExtra($conn, $sub_goods_sub_no, 'GOODS_STICKER_SIZE');

								if(sizeof($arr_rs_goods_extra) > 0) { 
									$rs_sticker_size		= SetStringFromDB($arr_rs_goods_extra[0]["DCODE"]); 
									//echo "구성품 인케이스 스티커 사이즈 설정 : ".$rs_sticker_size."<br/>";
								}
							}

				?>
				<tr>
					<td><?=$sub_goods_code?></td>
					<td class="modeual_nm"><?=$sub_goods_name?> </td>
					<td class="price"><?=number_format($sub_buy_price)?></td>
					<td class="price"><span class="sub_goods_cnt" data-goods_cnt="<?=$sub_goods_cnt ?>" data-goods_cate="<?=$sub_goods_cate ?>"><?=$sub_goods_cnt ?></span></td>
					<td class="price"><span><?=getSafeNumberFormatted($sub_tstock) ?></span></td>
					<td class="price"><span><?=getSafeNumberFormatted($sub_fstock) ?></span></td>
					<td class="price"><span><?=number_format($sub_stock) ?></span></td>
					<td class="price"><span class="sub_stock_cnt" data-stock_cnt="<?=$sub_stock - $sub_tstock + $sub_fstock ?>"><?=number_format($sub_stock - $sub_tstock + $sub_fstock)?></td>
				</tr>
				<?
						}
					} else {
				?>
				<tr>
					<td height="30" colspan="8">구성품이 없습니다</td>
				</tr>
				<?
					}
				?>
			</table>
			<? } ?>
			<?
				if(sizeof($arr_company_etc) > 0) { 
			?>
			<div class="sp10"></div>
			* 상품 공급업체 (<?= getCompanyName($conn, $rs_cate_03);?>) 추가정보
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tbody>
			<?
					for($o = 0; $o < sizeof($arr_company_etc); $o ++) { 
						$rs_cp_option_nm = $arr_company_etc[$o]["CP_OPTION_NM"];
						$rs_cp_option_value = $arr_company_etc[$o]["CP_OPTION_VALUE"];
						//echo $rs_cp_option_nm." ".$rs_cp_option_value."<br/>";
			?>
				<tr>
					<th>옵션명</th>
					<td>
						<?=$rs_cp_option_nm?>
					</td>
					<th>옵션메모</th>
					<td>
						<?=$rs_cp_option_value?>
					</td>
				</tr>
			<?
					}
			?>
			</table>
			<?
				}
			?>
			<div class="sp10"></div>
			* 작업 내용
			<div id="tabs" style="width:95%; margin:10px 0;">
				<ul>
					<li><a href="#tabs-1">작업 내역</a></li>
					<li><a href="#tabs-2">이전 작업내역</a></li>
				</ul>
				<div id="tabs-1">
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="*" />
					</colgroup>
					<tbody>


					<tr>
						<th>포장지</th>
						<td class="line">
							<?
								if($rs_wrap_code == "Y") { 

									echo "<span style='color:red; font-weight:bold;'>*. 포장 필수 상품입니다!</span><br/>";
								}
							?>
							<?
									if($CP_CATE <> "") { 
								?>
								<label><input type="checkbox" id="wrap_all" value="Y"/>전체</label><br/>
								<?
									}
							?>
							<?
								$ar_wrap_filtered = array();
								$ar_wrap_all = array();
								//echo $CP_CATE."<br/>";

								
								if($CP_CATE <> "") { 

									$arr_options = array("and_search_category" => $CP_CATE, "or_search_category" => "310301");

									$arr_wrap_filtered = listGoods($conn, "010204", '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', $arr_options, 'GOODS_NAME', 'ASC', '1', '1000');

									$arr_wrap_all = listGoods($conn, '010204', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');

									
									foreach($arr_wrap_filtered as $item) { 
										$ar_wrap_filtered[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_filtered, "opt_wrap_no", "150", "선택없음", "", $rs_wrap_no, "GOODS_NO", "GOODS_NAME");

								} else { 
									$arr_wrap_all = listGoods($conn, '010204', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_all, "opt_wrap_no", "150", "선택없음", "", $rs_wrap_no, "GOODS_NO", "GOODS_NAME");
								}

							?>
								
								<script>
								$(function(){

									var arr_wrap_filtered = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_wrap_filtered)?>));
									var arr_wrap_all = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_wrap_all)?>));
			
									$("#wrap_all").click(function(){
										$("select[name=opt_wrap_no]").find('option').remove().end();
										$("select[name=opt_wrap_no]").append('<option value="" data-image="/manager/images/no_img.gif">선택없음</option>');

										if($(this).is(":checked")) { 
											

											for(var i = 0; i < arr_wrap_all.length; i++) { 
												$("select[name=opt_wrap_no]").append('<option value="'+arr_wrap_all[i].GOODS_NO+'" data-image="'+arr_wrap_all[i].IMG_URL+'">'+arr_wrap_all[i].GOODS_NAME2+'</option>');
											}
											
										} else { 
											for(var i = 0; i < arr_wrap_filtered.length; i++) { 
												$("select[name=opt_wrap_no]").append('<option value="'+arr_wrap_filtered[i].GOODS_NO+'" data-image="'+arr_wrap_filtered[i].IMG_URL+'">'+arr_wrap_filtered[i].GOODS_NAME2+'</option>');
											}
										}

									});
						

									$("select[name=opt_wrap_no]").change(function(){
										var image_url = $(this).find(':selected').attr('data-image');
										$("img[name=sample_img]").attr("src", image_url);

										js_calculate_all_price();

											

									});

								});
								</script>

						</td>
						<td rowspan="2" colspan="2" style="text-align:center;"><img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/></td>
					</tr>
					<tr>
						<th>스티커</th>
						<td class="line"> 
								<? if($CP_CATE <> "") { ?>
								<label><input type="checkbox" id="sticker_all" value="Y"/>전체</label><br/>
								<? } ?>
								<?

								$ar_sticker_filtered = array();
								$ar_sticker_all = array();

								if($CP_CATE <> "") { 

									$arr_options = array("and_search_category" => $CP_CATE, "or_search_category" => "310302");
	

									if($rs_sticker_size <> "")
										$search_str = "(".$rs_sticker_size.")";
									else
										$search_str = "";

									$arr_sticker_filtered = listGoods($conn, "0103", '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', 'GOODS_NAME', $search_str, $arr_options, 'GOODS_NAME', 'ASC', '1', '1000');


									$arr_sticker_all = listGoods($conn, '0103', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');
									
									foreach($arr_sticker_filtered as $item) { 
										$ar_sticker_filtered[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}
									
									foreach($arr_sticker_all as $item) { 
										$ar_sticker_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_sticker_filtered, "opt_sticker_no", "150", "선택없음", "", $rs_opt_sticker_no, "GOODS_NO", "GOODS_NAME");

									
								} else { 

									$arr_sticker_all = listGoods($conn, '0103', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');

									foreach($arr_sticker_all as $item) { 
										$ar_sticker_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_sticker_all, "opt_sticker_no", "150", "선택없음", "", $rs_opt_sticker_no, "GOODS_NO", "GOODS_NAME");
								}

								

								?>
								
							<script>
							$(function(){

								var arr_sticker_filtered = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_sticker_filtered)?>));
								var arr_sticker_all = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_sticker_all)?>));
		
								$("#sticker_all").click(function(){
									$("select[name=opt_sticker_no]").find('option').remove().end();
									$("select[name=opt_sticker_no]").append('<option value="" data-image="/manager/images/no_img.gif">선택없음</option>');

									if($(this).is(":checked")) { 
										

										for(var i = 0; i < arr_sticker_all.length; i++) { 
											$("select[name=opt_sticker_no]").append('<option value="'+arr_sticker_all[i].GOODS_NO+'" data-image="'+arr_sticker_all[i].IMG_URL+'" >'+arr_sticker_all[i].GOODS_NAME2+'</option>');
										}
										
									} else { 
										for(var i = 0; i < arr_sticker_filtered.length; i++) { 
											$("select[name=opt_sticker_no]").append('<option value="'+arr_sticker_filtered[i].GOODS_NO+'"  data-image="'+arr_sticker_filtered[i].IMG_URL+'">'+arr_sticker_filtered[i].GOODS_NAME2+'</option>');
										}
									}

								});

								$("select[name=opt_sticker_no]").change(function(){
									var image_url = $(this).find(':selected').attr('data-image');
									$("img[name=sample_img]").attr("src", image_url);

									js_calculate_all_price();

								});

							});
							</script>
						</td>
						
					</tr>
					<tr>
						<th>스티커 메세지</th>
						<td class="line" colspan="3"> 
							<input type="text" class="txt" style="width:90%" name="opt_sticker_msg" value="<?=$rs_opt_sticker_msg?>"/>
						</td>
					</tr>
					<tr>
						<th>인쇄 (통장지갑등)</th>
						<td class="line" colspan="3">
								<input type="text" class="txt" style="width:90%" name="opt_print_msg" value="<?=$rs_opt_print_msg?>"/>
						</td>
					</tr>
					<tr>
						<th>아웃박스 스티커</th>
						<td><?= makeSelectBox($conn,"OUTBOX_STICKER_TF","opt_outbox_tf","150","","",$rs_opt_outbox_tf) ?></td>
						<th>업체주문번호</th>
						<td class="line">
								<input type="text" class="txt" style="width:120px;" name="cp_order_no" value=""/>
						</td>
					</tr>
					<tr>
						<th>출고예정일</th>
						<td class="line" colspan="3">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="opt_outstock_date" value="<?=$rs_opt_outstock_date?>" autocomplete="off" maxlength="10"/>
							&nbsp; <label><input type="checkbox"  name="bulk_tf" value="Y"/> 대량건/출고미지정</label>
							<script>
								$(function(){
									$("input[type=checkbox][name=bulk_tf]").click(function(){
										$("input[type=text][name=opt_outstock_date]").val('');
									});

									$("input[type=text][name=opt_outstock_date]").on('keydown, click',function(){
										$("input[type=checkbox][name=bulk_tf]").prop('checked', false);
									});
								});
							</script>
						</td>
						
					</tr>
					<tr>
						<th>작업메모(창고)</th>
						<td colspan="3">
							<textarea name="opt_memo" style="width:98%; height:50px" class="txt"></textarea>
						</td>
					</tr>
					<tr>
						<th>발주메모(공급사)</th>
						<td colspan="3">
							<textarea name="opt_request_memo" style="width:98%; height:50px" class="txt"></textarea>
						</td>
					</tr>
					<tr>
						<th>운영메모(지원)</th>
						<td colspan="3">
							<textarea name="opt_support_memo" style="width:98%; height:50px" class="txt"></textarea>
						</td>
					</tr>
					</tbody>
					</table>
				</div>
				<div id="tabs-2">
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="*" />
						<col width="*" />
					</colgroup>
					<tbody>
					<tr>
						<th>동일 상품 최근 스티커 주문 내역</th>
						<th>상품 불문 최근 스티커 주문 내역</th>
					</tr>

					<tr>
						<td style="text-align:center;">
						<?
							$arr_latest_sticker_by_goods = getOrderGoodsLatestSticker($conn, $cp_no, $rs_goods_no);
							if(sizeof($arr_latest_sticker_by_goods) > 0) { 

								$lsb_sticker_name			= trim($arr_latest_sticker_by_goods[0]["GOODS_NAME"]); 
								$lsb_img_url				= trim($arr_latest_sticker_by_goods[0]["IMG_URL"]); 
								$lsb_file_nm_100			= trim($arr_latest_sticker_by_goods[0]["FILE_NM_100"]); 
								$lsb_file_rnm_150			= trim($arr_latest_sticker_by_goods[0]["FILE_RNM_150"]); 
								$lsb_file_path_150			= trim($arr_latest_sticker_by_goods[0]["FILE_PATH_150"]); 
								$lsb_opt_sticker_msg		= trim($arr_latest_sticker_by_goods[0]["OPT_STICKER_MSG"]);

								$lsb_img_url	= getGoodsImage($lsb_file_nm_100, $lsb_img_url, $lsb_file_path_150, $lsb_file_rnm_150, "250", "250");
						?>
							<img src="<?=$lsb_img_url?>" border="0" style="width:100%;"><br/>
							<span><?=$lsb_sticker_name?></span><br/>
							<span><?=$lsb_opt_sticker_msg?></span>
						<?  
							}  else { 
						?>
								이력 없음 
						<?  }   ?>
						</td>
						<td style="text-align:center;">
						<?
							$arr_latest_sticker_by_order = getOrderGoodsLatestSticker($conn, $cp_no, '');
							if(sizeof($arr_latest_sticker_by_order) > 0) { 

								$lso_sticker_name			= trim($arr_latest_sticker_by_order[0]["GOODS_NAME"]); 
								$lso_img_url				= trim($arr_latest_sticker_by_order[0]["IMG_URL"]); 
								$lso_file_nm_100			= trim($arr_latest_sticker_by_order[0]["FILE_NM_100"]); 
								$lso_file_rnm_150			= trim($arr_latest_sticker_by_order[0]["FILE_RNM_150"]); 
								$lso_file_path_150			= trim($arr_latest_sticker_by_order[0]["FILE_PATH_150"]); 
								$lso_opt_sticker_msg		= trim($arr_latest_sticker_by_order[0]["OPT_STICKER_MSG"]);

								$lso_img_url	= getGoodsImage($lso_file_nm_100, $lso_img_url, $lso_file_path_150, $lso_file_rnm_150, "250", "250");
						?>
							<img src="<?=$lso_img_url?>" border="0" style="width:100%;"><br/>
							<span><?=$lso_sticker_name?></span><br/>
							<span><?=$lso_opt_sticker_msg?></span>
						<?  
							}  else { 
						?>
								이력 없음 
						<?  }   ?>
						</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
			

			<div class="sp10"></div>
			* 배송 내용
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<th>배송방식</th>
					<td>
						<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type", "100", "배송방법을 선택하세요", "", "0")?>
						<?=makeSelectBox($conn,"DELIVERY_CP_OP", "delivery_cp", "100", "택배회사를 선택하세요", "", "롯데택배")?>
						<script>
							$(function(){

								$("select[name=delivery_type]").change(function(){
									if($("select[name=delivery_type]").val() == "0" || $("select[name=delivery_type]").val() == "3") //택배, 개별택배시
										$("select[name=delivery_cp]").show();
									else
										$("select[name=delivery_cp]").hide();
								});
							});
						</script>
					</td>
					<th>배송비(운반비차액)</th>
					<td>
						<input type="text" class="txt" style="width:105px" name="sa_delivery_price" value="0" required onkeyup="return isMathNumber(this)"/> 원
					</td>
				</tr>
				<tr>
					<?
						if($cp_no==5597){
							$CP_NM="CU몰 기프트넷";
							$CP_PHONE="031-527-6812";
						}
					?>
					<th>보내는사람</th>
					<td>
						<input type="Text" name="sender_nm" value="<?=$CP_NM?>" style="width:70%;" class="txt">
					</td>
					<th>보내는사람연락처</th>
					<td>
						<input type="Text" name="sender_phone" value="<?=$CP_PHONE?>" style="width:160px;" class="txt">
					</td>
				</tr>
				</tbody>
				</table>
				<div class="sp10"></div>
				* 세금 계산서 
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<th>승인번호</th>
					<td>
						<?=makeSelectBox($conn, 'CASH_STATEMENT_TYPE', 'cate_03','100','결제종류','','')?>
					</td>
					<td>
						<textarea name="cate_02" style="width:345px; height:60px" placeholder="ex)20500101-10000000-XXXXXXXX"></textarea>
					</td>
				</tr>
				</tbody>
				</table>
				<script type="text/javascript">
					$(function(){
						$("[name=cate_02]").keyup(function(){
							var ks = $(this).val();
							prev_row = ks.substring(0, ks.lastIndexOf("\n") + 1);
							last_row = ks.substring(ks.lastIndexOf("\n") + 1);
							key_text = $(this).val();
							if(last_row.length == 8) { 
								merged = last_row + "-10000000-";
								$(this).val(prev_row + merged);
							}
						});
					});
				</script>
				<div class="btn_right">
					<? if($reserve_no == "") { ?>
						<input type="button" name="aa" value=" 상품추가 (계속) " class="btntxt" onclick="js_calculate_all_price(); js_add_goods();">
						<input type="button" name="aa" value=" 상품추가 (창닫기) " class="btntxt" onclick="js_calculate_all_price(); js_add_goods_close();">
						<input type="button" name="aa" value=" 창닫기 " class="btntxt" onclick="parent.close();"> 
					<? } else { ?>
						<input type="button" name="aa" value=" 상품추가 (창닫기) " class="btntxt" onclick="js_calculate_all_price(); js_add_goods_close();">
						<input type="button" name="aa" value=" 창닫기 " class="btntxt" onclick="parent.close();"> 
					<? } ?>
				</div>

		</td>
	</tr>
</table>
</form>
</body>
</html>
<script type="text/javascript">
	window.onload=function() {
		resizeBoardImage('700');
	}

js_calculate_all_price();

</script>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>