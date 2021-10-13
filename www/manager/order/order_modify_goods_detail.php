<?session_start();?>
<?
# =============================================================================
# File Name    : order_goods_detail.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

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
	//require "../../_classes/biz/cart/cart.php";

#====================================================================
# DML Process
#====================================================================

	//echo $goods_no."//".$temp_no."//".$order_no."<br/>";

	if ($mode == "U") {

		$opt_sticker_no    = trim($opt_sticker_no);
		$opt_sticker_msg   = trim($opt_sticker_msg);
		$opt_outbox_tf     = trim($opt_outbox_tf);

		$opt_wrap_no       = trim($opt_wrap_no);
		$opt_print_msg     = trim($opt_print_msg);
		$opt_outstock_date = trim($opt_outstock_date);
		$opt_memo          = trim($opt_memo);

		//$cate_01					= trim($cate_01);

		//$price						= trim($price);
		//$sticker_price				= trim($sticker_price);
		//$print_price				= trim($print_price);
		//$sale_susu					= trim($sale_susu);
		//$delivery_cnt_in_box		= trim($delivery_cnt_in_box);
		$delivery_type				= trim($delivery_type);

		$con_order_seq = "1";
		$goods_price = $sale_price;
		$delivery_price = $sa_delivery_price;

		$result = updateTempOrderGoods($conn, $order_no, $con_order_seq, $goods_no, $goods_code, $goods_name, $goods_price, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $delivery_type, $delivery_price, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box, $temp_no);

	}

	if ($result) {
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	//parent.js_reload_list('<?=$keep?>');
	parent.parent.close();
</script>
<?
		mysql_close($conn);
		exit;
	}

	
	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		#GOODS_NO, GOODS_TYPE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
		#PRICE, SALE_PRICE, EXTRA_PRICE, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, CONTENTS,
		#READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, ADM_NO, UP_DATE, DEL_ADM, DEL_DATE

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

		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
		$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 

		//$arr_rs_file = selectGoodsFile($conn, $goods_no);
		//$arr_rs_option = selectGoodsOption($conn, $goods_no);
		
		$img_url	= getGoodsImage($rs_file_nm_100, $rs_img_url, $rs_file_path_150, $rs_file_rnm_150, "250", "250");
		
		$arr_rs_sub = selectGoodsSub($conn, $goods_no);

		if($temp_no <> "" && $order_no <> "") { 
			$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $order_no);
			if (sizeof($arr_rs_temp_goods) > 0) {
				for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

					//$rs_goods_no			= trim($arr_rs_temp_goods[$k]["GOODS_NO"]); 
					//$rs_goods_code			= trim($arr_rs_temp_goods[$k]["GOODS_CODE"]); 
					//$rs_goods_name			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]); 
					//$rs_goods_sub_name		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_SUB_NAME"]);
					$rs_qty					= trim($arr_rs_temp_goods[$k]["QTY"]); 
					//$rs_goods_price			= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]); 
					
					$rs_opt_sticker_no					= trim($arr_rs_temp_goods[$k]["OPT_STICKER_NO"]); 
					$rs_opt_sticker_msg					= trim($arr_rs_temp_goods[$k]["OPT_STICKER_MSG"]); 
					$rs_opt_outbox_tf_code				= trim($arr_rs_temp_goods[$k]["OPT_OUTBOX_TF_CODE"]); 
					$rs_opt_wrap_no						= trim($arr_rs_temp_goods[$k]["OPT_WRAP_NO"]); 
					$rs_opt_print_msg					= trim($arr_rs_temp_goods[$k]["OPT_PRINT_MSG"]); 
					$rs_opt_outstock_date				= trim($arr_rs_temp_goods[$k]["OPT_OUTSTOCK_DATE"]); 
					$rs_opt_manager_no					= trim($arr_rs_temp_goods[$k]["OPT_MANAGER_NO"]); 
					$rs_opt_memo						= trim($arr_rs_temp_goods[$k]["OPT_MEMO"]); 
					$rs_delivery_type_code				= trim($arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"]); 
					$rs_delivery_price					= trim($arr_rs_temp_goods[$k]["DELIVERY_PRICE"]); 
					$rs_delivery_cp						= trim($arr_rs_temp_goods[$k]["DELIVERY_CP"]); 
					$rs_sender_nm						= trim($arr_rs_temp_goods[$k]["SENDER_NM"]); 
					$rs_sender_phone					= trim($arr_rs_temp_goods[$k]["SENDER_PHONE"]);
					$rs_delivery_cnt_in_box				= trim($arr_rs_temp_goods[$k]["DELIVERY_CNT_IN_BOX"]);
					

					if($rs_opt_outstock_date == "0000-00-00 00:00:00")
						$rs_opt_outstock_date = "";
					else if($rs_opt_outstock_date == "1970-01-01") { 
						$rs_opt_outstock_date = "출고일오류";
					}

				}
			}
		}

		/*
		if ($cp_no <> "") {
			$rs_sale_price = getCompanyGoodsPriceOrDCRate($conn, $rs_goods_no, $rs_sale_price, $cp_no);
		}
		*/

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
	  minDate : 0
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

  });
</script>
<script type="text/javascript">

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

		//작업지시서에 계속 떠있게 하기 위해서
		if (document.frm.opt_outstock_date.value == "1970-01-01" || document.frm.opt_outstock_date.value == "출고일오류") {
			alert("출고예정일오류입니다 (년도-월-일)서식으로 지정해주세요.");
			document.frm.opt_outstock_date.focus();
			return;
		}

		if (document.frm.delivery_type.value == "") {
			alert("배송방식을 지정해주십시오.");
			document.frm.delivery_type.focus();
			return;
		}

		if (document.frm.delivery_type.value == "") {
			alert("배송방식을 지정해주십시오.");
			document.frm.delivery_type.focus();
			return;
		}

		document.frm.mode.value = "U";
		document.frm.keep.value = "STOP";
		document.frm.target = "";
		document.frm.action = "<?=$_SERVER[PHP_SELF]?>";
		document.frm.submit();

	}

	// 원가 계산
	/*
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
		var i_majin	= 0;
		var f_majin_per	= 0;
		var i_discount_price	= 0;
		var i_sale_total	= 0;
		var i_qty = 1;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val());
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val());
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val());
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val());
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val());
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=qty]").val() != "") i_qty = parseInt($("input[name=qty]").val());
		if ($("input[name=discount_price]").val() != "") i_discount = parseInt($("input[name=discount_price]").val());
		if ($("input[name=sale_total]").val() != "") i_sale_total = parseInt($("input[name=sale_total]").val());
		
		i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));

		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price;
		$("#total_wonga").val(i_total_wonga);
		
		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		$("#majin").html(numberFormat(i_majin));
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round((i_majin / i_sale_price) * 100);
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

		i_sale_total = i_sale_price * i_qty - i_discount_price;
		$("#sale_total").val(i_sale_total);

		$(".sub_goods_cnt").each(function( index, value ){
			var each_value = parseInt($(value).attr("data-goods_cnt"))
			$(value).html(i_qty * each_value);

		});
	}
	*/

</script>
</head>
<body>
<div class="sp10"></div>
<table cellpadding="0" cellspacing="0" width="100%">
<form name="frm" method="post">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="order_no" value="<?=$order_no?>">
<input type="hidden" name="goods_no" value="<?=$goods_no?>">
<input type="hidden" name="goods_code" value="<?=$rs_goods_code?>">
<input type="hidden" name="goods_name" value="<?=$rs_goods_name?>">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keep" value="">
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
					<td style="padding: 5px 5px 5px 5px" class="line" colspan="2" rowspan="10">
						<img src="<?=$img_url?>" border="0" width="250" height="250">
					</td>
					<th>상품코드</th>
					<td colspan="5" class="line">
						<?=$rs_goods_code?>
					</td>
				</tr>
				<tr>
					<th>상품명</th>
					<td colspan="5" class="line">
						<?=$rs_goods_name?>
					</td>
				</tr>
				<!--tr>
					<th>주문상품종류</th>
					<td class="line">
						<?=makeSelectBox($conn, "ORDER_GOODS_TYPE", "cate_01", "100", "선택", "", $rs_cate_01)?>
					</td>
				</tr-->				
				<tr>
					<th>제조사</th>
					<td class="line">
						<?=$rs_cate_02?>
					</td>
				</tr>
				
				<tr>
					<th>공급업체</th>
					<td class="line">
						<?= getCompanyName($conn, $rs_cate_03);?>
					</td>
				</tr>
				<tr>
					<th>재고</th>
					<td class="line">
						<?= getSafeNumberFormatted($rs_stock_cnt) ?> 개
					</td>
				</tr>
				<!--tr>
					<th>박스입수</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="delivery_cnt_in_box" value="<?=$rs_delivery_cnt_in_box?>" onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/>

					</td>
				</tr-->
				<tr>
					<th>수량</th>
					<td class="line">
						<input type="text" id="qty" class="txt" style="width:75px" name="qty" value="<?=$rs_qty?>" required onkeyup="return isNumber(this)" /> 개
					</td>
				</tr>

				<tr>
					<th>판매가</th>
					<td class="line">
						<input type="text" class="txt" style="width:90px" name="sale_price" value="<?=($rs_goods_price != "" ? $rs_goods_price : $rs_sale_price)?>" onkeyup="return isPhoneNumber(this)" /> 원 <?=($rs_goods_price != $rs_sale_price  ? "(정상가 : ".number_format($rs_sale_price)."원)" : "")?> 
					</td>
				</tr>
				<!--tr>
					<th>할인금액</th>
					<td class="line">
						<input type="text" id="discount_price" class="txt" style="width:90px" name="discount_price" value="0" onkeyup="return isNumber(this)" onChange="js_calculate_all_price()"/> 원
					</td>
				</tr>
				<tr>
					<th>총 판매가</th>
					<td class="line">
						<input type="text" id="sale_total" class="txt" style="width:90px" name="sale_total" value="0" onkeyup="return isNumber(this)" readonly onChange="js_calculate_all_price()"/> 원
					</td>
				</tr-->
				<!--tr>
					<th>매입가</th>
					<td class="line" colspan="3">
						<input type="hidden" name="buy_price" value="<?=$rs_buy_price?>"/>
						<?=number_format($rs_buy_price)?> 원
					</td>
				</tr>
				<tr>
					<th>스티커비용</th>
					<td class="line">
						<input type="hidden" name="sticker_price" value="<?=$rs_sticker_price?>"/>
						<?=number_format($rs_sticker_price)?> 원

					</td>
					<th>판매 수수률</th>
					<td class="line">
						<input type="hidden" name="sale_susu" value="<?=$rs_sale_susu?>"/>
						<?= $rs_sale_susu ?> % 

					</td>
				</tr>
				<tr>
					<th>포장인쇄비용</th>
					<td class="line">
						<input type="hidden" name="print_price" value="<?=$rs_print_price?>"/>
						<?=number_format($rs_print_price)?> 원
					</td>
					<th title="(판매가 * 100) * 판매 수수률">판매 수수료</th>
					<td class="line">
						  <span id="susu_price">0</span> 원
					</td>
				</tr>
				<tr>
					<th title="왕복택배비 / 박스입수">물류비</th>
					<td class="line">
						<input type="hidden" name="delivery_price" value="<?=$rs_delivery_price?>"/>
						<span id="delivery_per_price">0</span> 원 (택배비용 : <?=number_format($rs_delivery_price)?> 원)

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
				</tr--> 

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
			<div class="sp10"></div>
			* 구성 상품
			<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
				<!--<col width="5%" />-->
				<col width="15%" />
				<col width="*" />
				<col width="15%"/>
				<col width="10%"/>
				<col width="15%" />
				</colgroup>
				<tr>
					<th>코드</th>
					<th>상품명</th>
					<th>매입가</th>
					<th>구성수량</th>
					<th class="end">재고량</th>
				</tr>
				<?
					if(sizeof($arr_rs_sub) > 0) {
						for($i = 0; $i < sizeof($arr_rs_sub); $i++)
						{
							$sub_goods_code = $arr_rs_sub[$i]["GOODS_CODE"];
							$sub_goods_name = $arr_rs_sub[$i]["GOODS_NAME"];
							$sub_buy_price =  $arr_rs_sub[$i]["BUY_PRICE"];
							$sub_goods_cnt =  $arr_rs_sub[$i]["GOODS_CNT"];
							$sub_stock =      $arr_rs_sub[$i]["STOCK_CNT"];

				
				?>
				<tr>
					<td><?=$sub_goods_code?></td>
					<td class="modeual_nm"><?=$sub_goods_name?> </td>
					<td class="price"><?=number_format($sub_buy_price)?></td>
					<td class="price"><span class="sub_goods_cnt" data-goods_cnt="<?=$sub_goods_cnt ?>"><?=$sub_goods_cnt ?></span></td>
					<td class="price"><?=number_format($sub_stock)?></td>
				</tr>
				<?
						}
					} else {
				?>
				<tr>
					<td height="30" colspan="5">구성품이 없습니다</td>
				</tr>
				<?
					}
				?>
			</table>

			<div class="sp10"></div>
			* 작업 내용
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
							$arr_wrapping = listGoods($conn, '010204', '', '', '', '', '', '', '', '', '', 'Y', 'N', '', '', '', '', '', '1', '1000');

							echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrapping, "opt_wrap_no", "200", "선택없음", "", $rs_opt_wrap_no, "GOODS_NO", "GOODS_NAME");

							?>

							<script>
							$(function(){
								$("select[name=opt_wrap_no]").change(function(){
									var image_url = $(this).find(':selected').attr('data-image');
									$("img[name=sample_img]").attr("src", image_url);

									//if($(this).val() != "")
									//	$("input[name=print_price]").val($("input[name=backup_print_price]").val());
									//else
									//	$("input[name=print_price]").val('0');

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
							
							<?
							$arr_sticker = listGoods($conn, '0103', '', '', '', '', '', '', '', '', '', 'Y', 'N', '', '', '', '', '', '1', '1000');

							echo makeGoodsSelectBoxWithDataImage($conn, $arr_sticker, "opt_sticker_no", "200", "선택없음", "", $rs_opt_sticker_no, "GOODS_NO", "GOODS_NAME");
							?>
							
						<script>
						$(function(){
							$("select[name=opt_sticker_no]").change(function(){
								var image_url = $(this).find(':selected').attr('data-image');
								$("img[name=sample_img]").attr("src", image_url);

								//if($(this).val() != "")
								//	$("input[name=sticker_price]").val($("input[name=backup_sticker_price]").val());
								//else
								//	$("input[name=sticker_price]").val('0');

								js_calculate_all_price();

							});

						});
						</script>
						<script>
							$(function(){
								$("select option:selected").each(function(index, obj){
									var image_url = $(this).attr('data-image');
									if(image_url != '')
										$("img[name=sample_img]").attr("src", image_url);
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
					<td><?= makeSelectBox($conn,"OUTBOX_STICKER_TF","opt_outbox_tf","150","","", $rs_opt_outbox_tf_code) ?></td>
					<!--th>업체주문번호</th>
					<td class="line">
							<input type="text" class="txt" style="width:120px;" name="cp_order_no" value=""/>
					</td-->
				</tr>
				<tr>
					<th>출고예정일</th>
					<td class="line" colspan="3">
						<input type="text" class="txt datepicker" style="width: 160px; margin-right:3px;" name="opt_outstock_date" placeholder="YYYY-MM-DD"value="<?=$rs_opt_outstock_date?>" maxlength="10"/>
					</td>
					
				</tr>
				<tr>
					<th>작업메모</th>
					<td colspan="3">
						<textarea name="opt_memo" style="width:98%; height:50px" class="txt"><?=$rs_opt_memo ?></textarea>
					</td>
				</tr>
			</tbody>
			</table>
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
						<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type", "100", "배송방법을 선택하세요", "", $rs_delivery_type_code)?>
						<?=makeSelectBox($conn,"DELIVERY_CP", "delivery_cp", "100", "택배회사를 선택하세요", "", $rs_delivery_cp)?>
						<script>
							$(function(){

								if($("select[name=delivery_type]").val() == "0" || $("select[name=delivery_type]").val() == "3") //택배, 개별택배시
									$("select[name=delivery_cp]").show();
								else
									$("select[name=delivery_cp]").hide();

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
					<th>보내는사람</th>
					<td>
						<input type="Text" name="sender_nm" value="<?= $rs_sender_nm?>" style="width:70%;" class="txt">
					</td>
					<th>보내는사람연락처</th>
					<td>
						<input type="Text" name="sender_phone" value="<?= $rs_sender_phone?>" style="width:160px;" class="txt">
					</td>
				</tr>
				<tr>
					<th>박스입수</th>
					<td colspan="3">
						<input type="Text" name="delivery_cnt_in_box" value="<?= $rs_delivery_cnt_in_box?>" style="width:70%;" class="txt">
					</td>
				</tr>
<? if ($sPageRight_I == "Y") {?>
				<tr>
					<td class="line" colspan="4" style="text-align:right;">
						<input type="button" name="aa" value=" 상품수정 " class="btntxt" onclick="js_add_goods_close();">
						<input type="button" name="aa" value=" 창닫기 " class="btntxt" onclick="parent.close();"> 
					</td>
				</tr>
<? } ?>

				</tbody>
				</table>


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

//js_calculate_all_price();

</script>

<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>