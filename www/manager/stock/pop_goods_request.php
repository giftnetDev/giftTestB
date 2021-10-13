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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================

	$req_no = trim($req_no);
	$mode = trim($mode);

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectGoodsRequestByReqNo($conn, $req_no);

	$REQ_DATE				= $arr_rs[0]["REQ_DATE"];
	$SENDER_CP				= $arr_rs[0]["SENDER_CP"];
	$CEO_NM					= $arr_rs[0]["CEO_NM"];
	$SENDER_ADDR			= $arr_rs[0]["SENDER_ADDR"];
	$SENDER_PHONE			= $arr_rs[0]["SENDER_PHONE"];
	$BUY_CP_NM				= $arr_rs[0]["BUY_CP_NM"];
	$BUY_MANAGER_NM			= $arr_rs[0]["BUY_MANAGER_NM"];
	$BUY_CP_PHONE			= $arr_rs[0]["BUY_CP_PHONE"];
	$DELIVERY_TYPE			= $arr_rs[0]["DELIVERY_TYPE"];
	$MEMO					= $arr_rs[0]["MEMO"];
	$TOTAL_REQ_QTY			= $arr_rs[0]["TOTAL_REQ_QTY"];
	$TOTAL_BUY_TOTAL_PRICE  = $arr_rs[0]["TOTAL_BUY_TOTAL_PRICE"];

	$arr_rs_goods = listGoodsRequestGoods($conn, $req_no, 'N');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title>기프트넷</title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
  
</head>
<style>
	input[type=text] {display:none; width:90%;} 
	.request_date {width:95%; text-align:right;}
	.row {position:relative;}
	.extra_button {display:none; position:absolute; }
	.minus {color: red; cursor:pointer;}
</style>
<? if($mode == "edit") { ?>
<script>
	$(function(){
		$("td, th").on("click", function(){
			var source = $(this).find("span");
			var text_box = $(this).find("input[type=text]");
			source.hide();
			text_box.val(source.html()).show().focus();

		});

		$("input[type=text]").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var req_no = $(this).closest("table").data("req_no");
				var req_goods_no = $(this).closest("tr").data("req_goods_no");
				var column = $(this).closest("th").data("column");
				var value = $(this).val();
				//alert(req_no);
				//alert(req_goods_no);
				//alert(column);

				(function() {
				  $.getJSON( "/manager/stock/json_goods_request.php", {
					mode: "UPDATE_REQUEST_GOODS",
					req_no: req_no,
					req_goods_no: req_goods_no,
					column: column,
					value : value
				  })
					.done(function( data ) {

					  $("span").show();
					  $("input[type=text]").hide();

					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('연결오류 : 잠시후 다시 시도해주세요');
					  });
					});
				})();

			}
		});

		$("select.sel_receiver").on("change", function() {

			bOK = confirm('이 상품의 수신처를 변경하시겠습니까?');
			
			if (bOK) {
				var req_goods_no = $(this).closest("tr").data("req_goods_no");
				var order_goods_no = $(this).data("order_goods_no");
				var to_here = $(this).val();

				(function() {
				  $.getJSON( "/manager/stock/json_goods_request.php", {
					mode: "UPDATE_REQUEST_GOODS_RECEIVER",
					req_goods_no: req_goods_no,
					order_goods_no: order_goods_no,
					op_cp_no: <?=$s_adm_com_code?>,
					to_here : to_here
				  })
					.done(function( data ) {

						window.location.reload(true); 
					});
				})();
			}
			
		});

		$("input[type=text]").on("blur", function(){
			$("span").show();
			$("input[type=text]").hide();
		});

		$(".row, .extra_button").hover(
		  function() { //IN
			$( this ).find(".extra_button").show();
		  }, function() { //OUT
			$( this ).find(".extra_button").hide();
		  }
		);

		$(".minus").on("click", function(){
			$(this).closest(".row").remove();
		});

	});
</script>
<? } ?>
<style>
body#popup_delivery_confirmation {width:1000px;}
</style>
<body id="popup_delivery_confirmation">

<div id="popup_delivery_confirmation">
	<div id="postsch_code">
		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
<input type="hidden" name="mode" value="<?=$mode?>">

			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<tr>
					<th class="h1">발 주 서</th>
				</tr>
			</table>
			<div class="sp10"></div>
			<div class="request_date">발주일 : <?=date("Y년 n월 j일",strtotime($REQ_DATE))?></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03" data-req_no="<?=$req_no?>">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tr>
					<th>발신처</th>
					<th class="line" data-column="SENDER_CP">
						<span><?=$SENDER_CP?></span>
						<input type="text" value=""/>
					</th>
					<th>수신처</th>
					<th class="line" data-column="BUY_CP_NM">
						<span><?=$BUY_CP_NM?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				<tr>
					<th>대표자</th>
					<th class="line" data-column="CEO_NM">
						<span><?=$CEO_NM?></span>
						<input type="text" value=""/>
					</th>
					<th>담당자</th>
					<th class="line" data-column="BUY_MANAGER_NM">
						<span><?=$BUY_MANAGER_NM?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				<tr>
					<th>주소</th>
					<th class="line" data-column="SENDER_ADDR">
						<span><?=$SENDER_ADDR?></span>
						<input type="text" value=""/>
					</th>
					<th>연락처</th>
					<th class="line" data-column="BUY_CP_PHONE">
						<span><?=$BUY_CP_PHONE?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				<tr style="height:80px;">
					<th rowspan="2">연락처</th>
					<th rowspan="2" class="line" data-column="SENDER_PHONE">
						<span><?=$SENDER_PHONE?></span>
						<input type="text" value=""/>
					</th>
					<th rowspan="2">특이사항</th>
					<th rowspan="2" class="line" data-column="MEMO">
						<span><?=$MEMO?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				<!--
				<tr>
					<th>배송방식</th>
					<th class="line" data-column="DELIVERY_TYPE">
						<span><?=$DELIVERY_TYPE?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				-->
			</table>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable03">
				<colgroup>
					<col width="*" />
					<col width="4%" />
					<col width="5%" />
					<col width="7%" />
					<? if($mode == "edit") { ?>
					<col width="14%" />
					<? } ?>
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<tr>
					<th>
						품명
					</th>
					<th>
						수량
					</th>
					<th>
						판매단가<br/>
						(+VAT)
					</th>
					<th>
						합계<br/>
						(+VAT)
					</th>
					<? if($mode == "edit") { ?>
					<th style="background-color:red; color:white;">
						(선택)
					</th>
					<? } ?>
					<th>
						수취인명
					</th>
					<th>
						수취인연락처
					</th>
					<th>
						수취인휴대폰
					</th>
					<th>
						수취인주소
					</th>
					<th>
						비고-작업
					</th>
					<th>
						비고-주문지
					</th>
					<th>
						비고-발송자
					</th>
				</tr>
				<?

				if (sizeof($arr_rs_goods) > 0) {
					for ($j = 0 ; $j < (sizeof($arr_rs_goods) <= 13 ? 13 : sizeof($arr_rs_goods)); $j++) {

						$REQ_GOODS_NO				= trim($arr_rs_goods[$j]["REQ_GOODS_NO"]);
						$ORDER_GOODS_NO				= trim($arr_rs_goods[$j]["ORDER_GOODS_NO"]);
						$GOODS_NAME					= trim(setStringFromDB($arr_rs_goods[$j]["GOODS_NAME"]));
						$GOODS_SUB_NAME				= trim(setStringFromDB($arr_rs_goods[$j]["GOODS_SUB_NAME"]));
						$REQ_QTY					= trim($arr_rs_goods[$j]["REQ_QTY"]);
						$BUY_PRICE					= trim($arr_rs_goods[$j]["BUY_PRICE"]);
						$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$j]["BUY_TOTAL_PRICE"]);

						$RECEIVER_NM				= trim(setStringFromDB($arr_rs_goods[$j]["RECEIVER_NM"]));
						$RECEIVER_ADDR				= trim(setStringFromDB($arr_rs_goods[$j]["RECEIVER_ADDR"]));
						$RECEIVER_PHONE				= trim($arr_rs_goods[$j]["RECEIVER_PHONE"]);
						$RECEIVER_HPHONE			= trim($arr_rs_goods[$j]["RECEIVER_HPHONE"]);
						$MEMO1						= trim(setStringFromDB($arr_rs_goods[$j]["MEMO1"]));
						$MEMO2						= trim(setStringFromDB($arr_rs_goods[$j]["MEMO2"]));
						$MEMO3						= trim(setStringFromDB($arr_rs_goods[$j]["MEMO3"]));

						$TO_HERE					= trim($arr_rs_goods[$j]["TO_HERE"]);

						// 2016-12-15 외부업체 발송인데 기타 시트를 참조안하고 수령처로 보내는 일때문에 수령처 정보 제거
						$cnt_individual = cntDeliveryIndividual($conn, $ORDER_GOODS_NO);
						if($cnt_individual[0]["CNT_DELIVERY_PLACE"] > 0 && $TO_HERE != 'Y') { 
							$RECEIVER_NM = "";
							$RECEIVER_ADDR = "해당 상품 시트 참조";
							$RECEIVER_PHONE = "";
							$RECEIVER_HPHONE = "";
						} 

						if($REQ_QTY <> "") 
							$REQ_QTY = number_format($REQ_QTY);
						
						if($BUY_PRICE <> "") 
							$BUY_PRICE = number_format($BUY_PRICE);
						
						if($BUY_TOTAL_PRICE <> "") 
							$BUY_TOTAL_PRICE = number_format($BUY_TOTAL_PRICE);

						$arr_og = selectOrderGoods($conn, $ORDER_GOODS_NO);

				?>
				<tr class="row" data-req_goods_no="<?=$REQ_GOODS_NO?>">
					<th data-column="GOODS_NAME">
						<?=($REQ_GOODS_NO != 0 ? ($j+1).". " : "")?><span><?=$GOODS_NAME." ".$GOODS_SUB_NAME?></span>
						<input type="text" value=""/>
					</th>
					<th>
						<?=$REQ_QTY?>
					</th>
					<th>
						<?=$BUY_PRICE?>
					</th>
					<th>
						<?=$BUY_TOTAL_PRICE?>
					</th>
					<? if($mode == "edit") { ?>
					<th style="background-color:#ffb3b3;">
						<? if($TO_HERE <> "") { ?>
						<select class="sel_receiver" data-order_goods_no = "<?=$ORDER_GOODS_NO?>">
							<option value="Y" <?=($TO_HERE == "Y" ? "selected" : "")?>>운영처</option>
							<option value="N" <?=($TO_HERE == "N" ? "selected" : "")?>>수령처</option>
						</select>
						<? } ?>
						<? if($arr_og[0]["DELIVERY_TYPE"] == "98" && $TO_HERE == "Y") {?>
						<br/><br/>외부발송 주의 
						<? } ?>
						<? if($arr_og[0]["DELIVERY_TYPE"] != "98" && $TO_HERE == "N") {?>
						<br/><br/>수신처 재확인 
						<? } ?>
					</th>
					<? } ?>
					<th data-column="RECEIVER_NM">
						<span><?=$RECEIVER_NM?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="RECEIVER_PHONE">
						<span><?=$RECEIVER_PHONE?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="RECEIVER_HPHONE">
						<span><?=$RECEIVER_HPHONE?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="RECEIVER_ADDR">
						<span><?=$RECEIVER_ADDR?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="MEMO1">
						<span><?=$MEMO1?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="MEMO2">
						<span><?=$MEMO2?></span>
						<input type="text" value=""/>
					</th>
					<th data-column="MEMO3">
						<span><?=$MEMO3?></span>
						<input type="text" value=""/>
					</th>
				</tr>
				<?
					
					}
				}

				?>
				<tr>
					<th colspan="4">
						합 계
					</th>
					<? if($mode == "edit") { ?>
					<th>
							
					</th>
					<? } ?>
					<th>
						
					</th>
					<th>
						
					</th>
					<th>
						
					</th>
					<th>

					</th>
					<th>

					</th>
					<th>
						<?=number_format($TOTAL_REQ_QTY)?> 개
					</th>
					<th>
						<?=number_format($TOTAL_BUY_TOTAL_PRICE)?> 원
					</th>
				</tr>
			</table>

	</div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
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