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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";

	if ($mode == "U") {

        $goods_delivery_name = SetStringToDB($goods_delivery_name);
		$memo				 = SetStringToDB($memo);

		$result = updateOrderGoodsDeliveryPaperAll($conn, $order_goods_delivery_no, $delivery_seq, $delivery_cp, $delivery_no, $goods_delivery_name, $order_nm, $order_phone, $order_manager_nm, $order_manager_phone, $send_cp_addr, $receiver_nm, $receiver_phone, $receiver_hphone, $receiver_addr, $delivery_fee_code, $delivery_claim_code, $memo, $chk_force_complete, $use_tf);

		/*
		$arrlength = count($arr_delivery_goods_seq);

		
		for($x = 0; $x < $arrlength; $x++) {
			$delivery_goods_seq = $arr_delivery_goods_seq[$x];
			$goods_total = $arr_goods_total[$x];

			updateOrderDeliveryGoods($conn, $delivery_goods_seq, $goods_total);
		}
		*/
		
		if ($result) {
		?>
		<script type="text/javascript">
			//if(window.opener.js_reload)
			//	window.opener.js_reload();
			alert("수정 되었습니다.");
			//self.close();
		</script>
		<?
			//mysql_close($conn);
			//exit;
		}
	}

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_delivery_no				= trim($order_goods_delivery_no);
	
#===============================================================
# Get Search list count
#===============================================================
	$arr_order_rs = selectOrderDeliveryPaper($conn, $order_goods_delivery_no, "");

	$rs_delivery_seq	        = trim($arr_order_rs[0]["DELIVERY_SEQ"]); 
	$rs_delivery_no 		    = trim($arr_order_rs[0]["DELIVERY_NO"]);
	$rs_delivery_cp				= trim($arr_order_rs[0]["DELIVERY_CP"]);
	$rs_order_nm		        = trim($arr_order_rs[0]["ORDER_NM"]); 
	$rs_order_phone		        = trim($arr_order_rs[0]["ORDER_PHONE"]);

	$rs_order_manager_nm	    = trim($arr_order_rs[0]["ORDER_MANAGER_NM"]);
	$rs_order_manager_phone		= trim($arr_order_rs[0]["ORDER_MANAGER_PHONE"]);
	$rs_send_cp_addr			= trim($arr_order_rs[0]["SEND_CP_ADDR"]);
	
	$rs_receiver_nm		        = trim($arr_order_rs[0]["RECEIVER_NM"]); 
	$rs_receiver_phone		    = trim($arr_order_rs[0]["RECEIVER_PHONE"]);
	$rs_receiver_hphone		    = trim($arr_order_rs[0]["RECEIVER_HPHONE"]);
	$rs_receiver_addr			= trim($arr_order_rs[0]["RECEIVER_ADDR"]); 
	
	$rs_goods_delivery_name	    = trim($arr_order_rs[0]["GOODS_DELIVERY_NAME"]); 
	$rs_memo				    = trim($arr_order_rs[0]["MEMO"]); 
	$rs_delivery_fee_code		= trim($arr_order_rs[0]["DELIVERY_FEE_CODE"]); 
	$rs_delivery_claim_code		= trim($arr_order_rs[0]["DELIVERY_CLAIM_CODE"]); 
	$rs_delivery_date           = trim($arr_order_rs[0]["DELIVERY_DATE"]); 
	$rs_use_tf					= trim($arr_order_rs[0]["USE_TF"]); 

	if($rs_delivery_date == '0000-00-00 00:00:00')
		$rs_delivery_date = "";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script language="javascript">

function js_save() {
	var frm = document.frm;

	frm.mode.value = "I";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_update() {
	var frm = document.frm;

	if (document.frm.rd_use_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}
	}

	frm.mode.value = "U";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}


function js_delete(delivery_goods_seq) {
	var frm = document.frm;

	frm.mode.value = "D";
	frm.target = "";
	frm.delivery_goods_seq.value = delivery_goods_seq;
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

</script>

<!--
<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {

		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				frm.keyword.value = keyword;
				frm.goods_type.value = "unit";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
				
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+
				arr_keywordList[1]+"</a></td><td width='105px'>판매가 : "+arr_keywordList[3]+"</td></tr></table>";
		
			}

			var listView = document.getElementById('suggestList');
			listView.innerHTML = html;
					
			suggest.style.visibility  ="visible"; 
		} else {
			suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword) {

		var frm = document.frm;

		frm.search_name.value = selectedKeyword;

		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		//frm.goods_no.value						= arr_keywordValues[1];
        // arr_keywordValues[2]; 공급가
		// arr_keywordValues[3]; 판매가
		var sub_goods_ids = frm.elements['sub_goods_id[]'];
		if(sub_goods_ids != undefined)
		{
			if(sub_goods_ids.value == arr_keywordValues[1]) 
			{
				alert('이미 추가한 상품입니다');
				return;
			}
			for (var i = 0; i < sub_goods_ids.length; i++) {
				if(sub_goods_ids[i].value == arr_keywordValues[1]){
					alert('이미 추가한 상품입니다');
					return;
				}
			}
		}


		$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'>" + arr_keywordValues[0] + "[<a href=\"javascript:js_goods_view('"+ arr_keywordValues[1] + "')\">"+ arr_keywordValues[1] + "</a>]" + "<input type='hidden' name='sub_goods_id[]' value='" + arr_keywordValues[1] + "'><input type='hidden' name='sub_buy_price[]' value='" + arr_keywordValues[2] + "'><input type='hidden' name='sub_sale_price[]' value='" + arr_keywordValues[3] + "'></td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:80px' value='1'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
	});
});
	

</script>
-->
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>송장 상세 조회</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_type" value="">
<input type="hidden" name="delivery_goods_seq" value="">
<input type="hidden" name="order_goods_delivery_no" value="<?=$order_goods_delivery_no?>">

	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<th>출고번호</th>
		<td class="line">
			<input type="text" name="delivery_seq" value="<?=$rs_delivery_seq?>"/>
			
		</td>
		<th>택배사/송장번호</th>
		<td class="line">
			<?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "delivery_cp","90", "택배사 선택", "", $rs_delivery_cp)?> <input type="text" name="delivery_no" value="<?=$rs_delivery_no?>"/>
		</td>
	</tr>
	<tr>
		<th>송장내용</th>
		<td class="line" colspan="3">
			<input type="text" name="goods_delivery_name" class="txt" style="width:95%" value="<?=$rs_goods_delivery_name?>"/>
		</td>
	</tr>
	</table>
	<h2>* 주문자정보</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<th>주문자명</th>
		<td class="line">
			<input type="text" name="order_nm" class="txt" style="width:95%" value="<?=$rs_order_nm?>"/>
		</td>
		<th>연락처</th>
		<td class="line" >
			<input type="text" name="order_phone" class="txt" style="width:95%" value="<?=$rs_order_phone?>"/>
		</td>
	</tr>						
	</table>
	<h2>* 담당자정보</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<th>담당자명</th>
		<td class="line">
			<input type="text" name="order_manager_nm" class="txt" style="width:95%" value="<?=$rs_order_manager_nm?>"/>
		</td>
		<th>담당자전화번호</th>
		<td class="line">
			<input type="text" name="order_manager_phone" class="txt" style="width:95%" value="<?=$rs_order_manager_phone?>"/>
		</td>
	</tr>	
	<tr>
		<th>보내는분 주소</th>
		<td class="line" colspan="3">
			<input type="text" name="send_cp_addr" class="txt" style="width:95%" value="<?=$rs_send_cp_addr?>"/>
		</td>
	</tr>
	</table>
	<h2>* 수령자정보</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<th>수령자명</th>
		<td class="line" colspan="3">
			<input type="text" name="receiver_nm" class="txt" style="width:95%" value="<?=$rs_receiver_nm?>"/>
		</td>
	</tr>						
	<tr>
		<th>연락처</th>
		<td class="line">
			<input type="text" name="receiver_phone" class="txt" style="width:95%" value="<?=$rs_receiver_phone?>"/>
		</td>
		<th>휴대전화번호</th>
		<td class="line">
			<input type="text" name="receiver_hphone" class="txt" style="width:95%" value="<?=$rs_receiver_hphone?>"/>
		</td>
	</tr>				
	<tr>
		<th>주소</th>
		<td class="line" colspan="3">
			<input type="text" name="receiver_addr" value="<?=$rs_receiver_addr?>" style="width:95%;"/>
		</td>
	</tr>
	<tr>
		<th>메모</th>
		<td class="line" colspan="3">
			<input type="text" name="memo" value="<?=$rs_memo?>" style="width:95%;"/>
		</td>
	</tr>
	<tr>
		<!--th>배송수익</th>
		<td class="line">
			<?= makeSelectBox($conn,"DELIVERY_PROFIT","delivery_profit_code","105","","", $rs_delivery_profit_code)?>
		</td -->
		<th>운임타입</th>
		<td class="line">
			<?= makeSelectBox($conn,"DELIVERY_FEE","delivery_fee_code","105","","", $rs_delivery_fee_code)?>
			<script>
				$(function(){

					delivery_fee_options_all = $("select[name=delivery_fee_code] option").clone();

					//택배회사 변경시 비용변경
					$("select[name=delivery_cp]").change(function(){

						var delivery_fee_code = $("select[name=delivery_fee_code]").find('option').remove().end();
						delivery_fee_options_all.each(function(index, item){

							if(item.value.startsWith($("select[name=delivery_cp]").val()))
								$("select[name=delivery_fee_code]").append(item);

						});

					});

					// 로딩후 초기화
					var delivery_fee_code = $("select[name=delivery_fee_code]").find('option').remove().end();
					delivery_fee_options_all.each(function(index, item){

						if(item.value.startsWith($("select[name=delivery_cp]").val()))
							$("select[name=delivery_fee_code]").append(item);

					});

				});
			</script>
		</td>
		<th>배송클레임</th>
		<td class="line">
			<?= makeSelectBoxWithExt($conn,"DELIVERY_CLAIM","delivery_claim_code","105","선택","", $rs_delivery_claim_code)?>
			<script>
				$(function(){
					$("select[name=delivery_claim_code]").change(function(){
						var claim = $("select[name=delivery_claim_code] option:selected").attr("data-ext");
						if(claim == undefined) 
							claim = "";
						var goods_delivery_name = $("input[name=goods_delivery_name]").val();
						
						$("select[name=delivery_claim_code] option").each(function(i, d){

							var selected = $(d).attr("data-ext");
							if(selected == undefined)
								selected = "";
							
							if(selected != "") {
								if(goods_delivery_name.indexOf(selected, 0) >= 0)
								{
									goods_delivery_name = goods_delivery_name.substring(selected.length);
								}
							}
								
						});

						goods_delivery_name = claim + goods_delivery_name;
						
						$("input[name=goods_delivery_name]").val(goods_delivery_name);
					});
				});
			</script>
		</td>
	</tr>
	<tr>
		<th>배송일</th>
		<td class="line" colspan="3">
			
			<? if($rs_delivery_date == '') {
					echo "발송전";
			?>
			&nbsp;&nbsp; <label><input type="checkbox" name="chk_force_complete" />강제완료</label>
			<? } else {
				echo $rs_delivery_date;
			}	
			?>
			
		</td>
	</tr>
	<tr>
		<th>사용여부</th>
		<td colspan="3" class="line">
			<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용함 <span style="width:20px;"></span>
			<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 사용안함
			<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
		</td>
	</tr>
	</table>
	
	<!--
	<div class="sp10"></div>
	* 송장 상품 수량 &nbsp; 
	<table cellpadding="0" cellspacing="0" class="rowstable" style="width:100%;">

		<colgroup>
			<col width="30%" />
			<col width="*" />
			<col width="20%" />
			<col width="10%" />
		</colgroup>
		<thead>
			<tr>
				<th>상품코드</th>
				<th>상품명</th>
				<th>구성수량</th>
				<th class="end"></th>
			</tr>
		</thead>
		<tbody>
		<?
			if (sizeof($arr_order_goods) > 0) {
				
				for ($j = 0 ; $j < sizeof($arr_order_goods); $j++) {
					$DELIVERY_GOODS_SEQ	= trim($arr_order_goods[$j]["DELIVERY_GOODS_SEQ"]);					
					$GOODS_NO			= trim($arr_order_goods[$j]["GOODS_NO"]);
					$GOODS_CODE			= trim($arr_order_goods[$j]["GOODS_CODE"]);
					$GOODS_NAME			= SetStringFromDB($arr_order_goods[$j]["GOODS_NAME"]);
					$GOODS_TOTAL		= trim($arr_order_goods[$j]["GOODS_TOTAL"]);
		?>
			<tr>
				<td height="24px">
					<input type="hidden" name="arr_delivery_goods_seq[]" value="<?=$DELIVERY_GOODS_SEQ?>" />
					<?=$GOODS_CODE?>
				</td>
				<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
				<td><input type="text" name="arr_goods_total[]" value="<?=$GOODS_TOTAL?>"/></td>
				<td><input type="button" name="aa" value="삭제" onclick="javascript:js_delete('<?=$DELIVERY_GOODS_SEQ?>');"/></td>
			</tr>
		<?
				}
			} else {
		?>
			<tr>
				<td colspan="3" height="30">데이터가 없습니다</td>
			</tr>
		<?
			}
		?>
		</tbody>
	</table>

	<div class="sp10"></div>
	<table cellpadding="0" cellspacing="0" class="colstable02">
	<colgroup>
		<col width="15%" />
		<col width="35%" />
		<col width="15%" />
		<col width="35%" />
	</colgroup>
	<tbody>
		
		<tr class="set_goods">
			<th>송장 자재 추가</th>
				<td colspan="3" style="position:relative" class="line">
					<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
						<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
					</div>
					<input type="text" class="txt search_name" style="width:75%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />
					<input type="button" name="aa" value="추가" onclick="javascript:js_save();"/>

					<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
					<colgroup>
						<col width="12.5%" />
						<col width="35%" />
						<col width="12.5%" />
						<col width="35%" />
						<col width="5%" />
					</colgroup>
					<thead>
						<tr>
							<th colspan="5" class="line">상품을 검색해서 선택하시면 아래에 자재가 추가됩니다</th>
						</tr>
					</thead>
					<tbody class="sub_goods_list">
					</tbody>
					</table>
				</td>
		</tr>
		<script>
		<? 
			for($i = 0; $i < sizeof($arr_rs_goods_sub); $i++) {
			
		?>
			$(".sub_goods_list").append("<tr><th>상품명</th><td class='line'> <?=$arr_rs_goods_sub[$i]["GOODS_NAME"]?>[<?=$arr_rs_goods_sub[$i]["GOODS_SUB_NO"]?>]<input type='hidden' name='sub_goods_id[]' value='<?=$arr_rs_goods_sub[$i]["GOODS_SUB_NO"]?>'></td><th>수량</th><td class='line'><input type='text' name='sub_goods_cnt[]' class='txt' style='width:80px' value='<?=$arr_rs_goods_sub[$i]["GOODS_CNT"]?>'>개</td><td style='border-underline:1px solid #d2dfe5'><span class='remove_sub' style='color:#478fb2; cursor:pointer; font-weight:bold; text-decoration:underline;'>삭제</span></td></tr>");	
		<?
			}
		 ?>
		</script>
	</tbody>
	</table>
	-->

	<div class="btn">
	  <a href="javascript:js_update();"><img src="../images/admin/btn_modify.gif" alt="확인" /></a>
	  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
	</div>      

	


<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>