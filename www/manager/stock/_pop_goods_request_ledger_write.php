<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG025"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================

	$mode = trim($mode);
	$rg_no = trim($rg_no);
	$cl_no = false;

	if ($base_confirm_date == "") {
		$base_confirm_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$base_confirm_date = trim($base_confirm_date);
	}

	if ($mode == "I") {

		//* 순서 변경 금지
		//매입기장
		$cl_no = insertCompanyLedgerDepositFromGoodsRequest($conn, $rg_no, $base_confirm_date, $s_adm_no);

		//기타 비용 추가 리스트
		if (isset($arr_option_name)) 
		{

			$arr_rs = selectCompanyLedger($conn, $cl_no);

			$RS_CP_NO								= trim($arr_rs[0]["CP_NO"]); 
			$RS_INOUT_DATE							= SetStringFromDB($arr_rs[0]["INOUT_DATE"]); 
			$RS_INOUT_TYPE							= SetStringFromDB($arr_rs[0]["INOUT_TYPE"]); 

			$RS_DEPOSIT								= "";
			
			$RS_RESERVE_NO							= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 
			$RS_ORDER_GOODS_NO						= SetStringFromDB($arr_rs[0]["ORDER_GOODS_NO"]); 
			$RS_RGN_NO								= SetStringFromDB($arr_rs[0]["RGN_NO"]); 

			$RS_INOUT_DATE			= date("Y-m-d H:i:s",strtotime($RS_INOUT_DATE));
			
			for($j = 0; $j < sizeof($arr_option_name); $j ++) { 
				$t_option_nm = $arr_option_name[$j]; 
				$t_option_qty = $arr_option_qty[$j]; 
				$t_option_price = $arr_option_price[$j]; 
				$withdraw = $t_option_qty * $t_option_price; 

				if($t_option_nm != "" && $t_option_qty != "" && $t_option_price != "") { 
					
					$result2 = insertCompanyLedger($conn, $RS_CP_NO, $RS_INOUT_DATE, $RS_INOUT_TYPE, null, $t_option_nm, $t_option_qty, $t_option_price, $withdraw, $RS_DEPOSIT, 0, $RS_RESERVE_NO, $RS_ORDER_GOODS_NO, $RS_RGN_NO, $s_adm_no);

				}
			}
		}

		//매입확정 표기 
		if($cl_no)
			updateGoodsRequestConfirm($conn, $rg_no, $s_adm_no);

	}

	if($cl_no) {

?>
<script type="text/javascript">
	window.opener.js_search();
	alert("입력 되었습니다.");
	self.close();
</script>
<?
		mysql_close($conn);
		exit;
	}

	$arr_rs_goods = selectGoodsRequestGoods($conn, $rg_no, 'N'); 

	if (sizeof($arr_rs_goods) > 0) {
		
		$REQ_GOODS_NO				= trim($arr_rs_goods[0]["REQ_GOODS_NO"]);
		$ORDER_GOODS_NO				= trim($arr_rs_goods[0]["ORDER_GOODS_NO"]);
		$GOODS_NO					= trim($arr_rs_goods[0]["GOODS_NO"]);
		$GOODS_NAME					= SetStringFromDB($arr_rs_goods[0]["GOODS_NAME"]);
		$GOODS_SUB_NAME				= SetStringFromDB($arr_rs_goods[0]["GOODS_SUB_NAME"]);
		$BUY_PRICE					= trim($arr_rs_goods[0]["BUY_PRICE"]);
		$REQ_QTY					= trim($arr_rs_goods[0]["REQ_QTY"]);
		$BUY_TOTAL_PRICE			= trim($arr_rs_goods[0]["BUY_TOTAL_PRICE"]);
		$RECEIVE_QTY				= trim($arr_rs_goods[0]["RECEIVE_QTY"]);
		$RECEIVE_DATE				= trim($arr_rs_goods[0]["RECEIVE_DATE"]);
		$RECEIVER_NM				= trim($arr_rs_goods[0]["RECEIVER_NM"]);
		$TO_HERE					= trim($arr_rs_goods[0]["TO_HERE"]);
		$MEMO2						= trim($arr_rs_goods[0]["MEMO2"]);

		$UP_DATE					= trim($arr_rs_goods[0]["UP_DATE"]);
		$UP_ADM						= trim($arr_rs_goods[0]["UP_ADM"]);
		
		$CANCEL_TF					= trim($arr_rs_goods[0]["CANCEL_TF"]);
		$CANCEL_DATE				= trim($arr_rs_goods[0]["CANCEL_DATE"]);
		$CANCEL_ADM					= trim($arr_rs_goods[0]["CANCEL_ADM"]);

		$CONFIRM_TF					= trim($arr_rs_goods[0]["CONFIRM_TF"]);
		$CONFIRM_DATE				= trim($arr_rs_goods[0]["CONFIRM_DATE"]);

		if($RECEIVE_DATE != "0000-00-00 00:00:00")
			$RECEIVE_DATE = "<font color='blue'>".date("Y-m-d H:i",strtotime($RECEIVE_DATE))."</font>";
		else
			$RECEIVE_DATE = "<font color='red'>입고전</font>";

		if($UP_DATE != "0000-00-00 00:00:00")
			$UP_DATE = date("Y-m-d",strtotime($UP_DATE));
		else
			$UP_DATE = "";
	
		if($CANCEL_DATE != "0000-00-00 00:00:00")
			$CANCEL_DATE = date("Y-m-d",strtotime($CANCEL_DATE));
		else
			$CANCEL_DATE = "";

		if($CONFIRM_DATE != "0000-00-00 00:00:00" && $CONFIRM_TF == 'Y')
			$CONFIRM_DATE = "매출확정일시:".date("Y-m-d H:i",strtotime($CONFIRM_DATE));
		else
			$CANCEL_DATE = "";

		if($CANCEL_TF == "Y")
			$str_cancel_style = "cancel_order";
		else
			$str_cancel_style = "";

		if($CONFIRM_TF == "Y")
			$str_confirm_style = "confirm_order";
		else
			$str_confirm_style = "";

		$is_ready_to_write = true;

		//if($TO_HERE != "Y") { 
			
			$arr_order_delivery_paper = getOrderGoodsDeliveryPaper($conn, $ORDER_GOODS_NO);
			
			if(sizeof($arr_order_delivery_paper) > 0) {
				$DELIVERY_NO = $arr_order_delivery_paper[0]["DELIVERY_NO"];
				$DELIVERY_CP = $arr_order_delivery_paper[0]["DELIVERY_CP"];
				$DELIVERY_DATE = $arr_order_delivery_paper[0]["DELIVERY_DATE"];

				$trace = getDeliveryUrl($conn, $DELIVERY_CP);
				$trace = $trace.trim($DELIVERY_NO);
				//echo $DELIVERY_CP."//".$DELIVERY_NO."<br/>";

				if($DELIVERY_DATE != "0000-00-00 00:00:00" && $DELIVERY_DATE != "")
					$DELIVERY_DATE = "<font color='blue'>".date("Y-m-d H:i",strtotime($DELIVERY_DATE))."</font>";
				else { 

					if($TO_HERE != "Y")
						$DELIVERY_DATE = "<font color='red'>직송배송완료전</font>";
					else
						$DELIVERY_DATE = "<font color='red'>입고전</font>";
					$is_ready_to_write = false;
				}

			} 
		//}
	} else { 

?>
<script type="text/javascript">
	alert("선택된 발주가 없습니다.");
	self.close();
</script>
<?
		mysql_close($conn);
		exit;

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title>기프트넷</title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
  
</head>
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
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

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
	});
  });

</script>
<script>

	function js_save() {
		var frm = document.frm;

		frm.target = "";
		frm.method = "post";
		frm.mode.value = "I";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_link_to_in_stock(request_goods_no) { 

		window.open("/manager/stock/in_list.php?nPage=1&order_field=REG_DATE&order_str=DESC&nPageSize=20&search_field=RESERVE_NO&search_str=RGN%3A" + request_goods_no,'_blank');

	}

</script>

<body id="popup_stock">

<div id="popupwrap_stock">
	<div id="postsch_stock">
		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="rg_no" value="<?=$rg_no?>">

			<div class="sp10"></div>
			<h1>발주 매입확정 + 기타 비용 추가</h1>

			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="*" />
					<col width="15%" />
					<col width="*" />
				</colgroup>
				<tr>
					<th>발주번호</th>
					<td class="line">
						<? if ($TO_HERE == "Y") {?>
						<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>');" style="font-weight:bold;">RGN:<?=$REQ_GOODS_NO?></a>
						<? } else { ?>
							RGN:<?=$REQ_GOODS_NO?>
						<? } ?>
					</td>
					<th>상품명</th>
					<td class="line">
						<?= $GOODS_NAME." ".$GOODS_SUB_NAME ?>
					</td>
				</tr>
				<tr>
					<th>단가</th>
					<td class="line">
						<?= number_format($BUY_PRICE)?>
					</td>
					<th>수량</th>
					<td class="line">
						<?= number_format($REQ_QTY)?>
					</td>
				</tr>
				<tr>
					<th>매입합계</th>
					<td class="line" colspan="3">
						<?= number_format($BUY_TOTAL_PRICE)?>
					</td>
				</tr>

				<? if ($TO_HERE == "Y") {?>
				<tr>
					<th>납품처</th>
					<td class="line"><?=$RECEIVER_NM?></td>
					<th>비고2</th>
					<td class="line"><?=$MEMO2?></td>
				</tr>
				<tr>
					<th>입고수량</th>
					<td class="line"><a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>');" style="font-weight:bold; color:blue;"><?=$RECEIVE_QTY?></a></td>
					<th>입고처리일/취소여부</th>
					<td class="line">
						<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>');" style="font-weight:bold;"><?= $RECEIVE_DATE ?></a>
						<? if($CANCEL_TF == "Y") {?>
							/ <font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">취소됨</font>
						<? } ?>
					</td>
				</tr>
				<? } else { ?>
				<tr>
					<th>납품처</th>
					<td class="line"><?="직송(".$RECEIVER_NM.")"?></td>
					<th>비고2</th>
					<td class="line"><?=$MEMO2?></td>
				</tr>
				<tr>
					<th>입고수량</th>
					<td class="line">
						<? if ($DELIVERY_NO) {?>
							<a href="javascript:js_trace('<?=$trace?>');" style="font-weight:bold; color:blue;"><?=$DELIVERY_CP?>(<?=$DELIVERY_NO?>)</a>
						<? } ?>
					</td>
					<th>입고처리일/취소여부</th>
					<td class="line">
						<?= $DELIVERY_DATE ?>
						<? if($CANCEL_TF == "Y") {?>
							/ <font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">취소됨</font>
						<? } ?>
					</td>
				</tr>
				<? } ?>

				<? if($is_ready_to_write) { ?>
				<tr>
					<th>매입확정일</th>
					<td colspan="3" class="line">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="base_confirm_date" value="<?=$base_confirm_date?>" maxlength="10"/>
					</td>
				</tr>
				<tr>
					<th>기타 비용 추가</th>
					<td colspan="3" class="line add_here">
					<div class="options">
						<input type="text" name="arr_option_name[]" value="" placeholder="기장명" />
						<input type="text" name="arr_option_qty[]" value="" placeholder="수량" style="width:40px;" />
						<input type="text" name="arr_option_price[]" value="" placeholder="단가" style="width:80px;" />
						<input type="button" name="b" onclick="js_append_option(this);" value="추가" />
						<input type="button" name="b" onclick="js_delete_option(this);" value="삭제" />
							
					</div>
					</td>
				</tr>
				<? } else { ?>
				<tr>
					<td colspan="4"><span style="color:red; font-weight:bold;">입고전/직배송 전에 기장하실 수 없습니다.</span></td>
				</tr>
				<? } ?>
				<script>
					function js_append_option(elem) { 
						var copied = $(elem).closest(".options").clone();
						copied.find("input[type=select]").val('');
						copied.find("input[type=text]").val('');
						$(".add_here").append(copied);
					}

					function js_delete_option(elem) {
						$(elem).closest(".options").remove();
					}
				</script>

			</table>
			
	</div>
	<div class="btn">
		<? if($is_ready_to_write) { ?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
		<? } ?>
	</div>

</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
</body>
</html>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>