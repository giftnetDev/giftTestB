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
	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($mode == "I") {

		$row_cnt = count($chk_reserve_no);

		$numSuccess = 0;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$reserve_no			= $chk_reserve_no[$k];

			$arr_order_rs = selectOrder($conn, $reserve_no);

			/*
			ORDER_NO, ON_UID, RESERVE_NO, MEM_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL,
			R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO,
			OPT_STCK_L1, OPT_STCK_L2, OPT_STCK_L3, OPT_STCK_SIZE, OPT_WRAPPING_PAPER, OPT_STCK_OUTBOX_TF, OPT_READY_DATE, OPT_MANAGER_NM,
			TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_QTY,
			ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, 
			CANCEL_DATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
			*/
			$rs_cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
			$rs_on_uid						= trim($arr_order_rs[0]["ON_UID"]); 
			$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
			$rs_cp_order_no					= trim($arr_order_rs[0]["CP_ORDER_NO"]); 
			$rs_reserve_no				    = trim($arr_order_rs[0]["RESERVE_NO"]); 
			$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]); 
			$rs_o_zipcode					= trim($arr_order_rs[0]["O_ZIPCODE"]); 
			$rs_o_addr1						= trim($arr_order_rs[0]["O_ADDR1"]); 
			$rs_o_addr2						= trim($arr_order_rs[0]["O_ADDR2"]); 
			$rs_o_phone						= trim($arr_order_rs[0]["O_PHONE"]); 
			$rs_o_hphone					= trim($arr_order_rs[0]["O_HPHONE"]); 
			$rs_o_email						= trim($arr_order_rs[0]["O_EMAIL"]); 
			$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
			$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
			$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]); 
			$rs_r_addr2						= trim($arr_order_rs[0]["R_ADDR2"]); 
			$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
			$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
			$rs_r_email						= trim($arr_order_rs[0]["R_EMAIL"]); 
			$rs_order_date				= trim($arr_order_rs[0]["ORDER_DATE"]); 
			$rs_total_delivery_price	= trim($arr_order_rs[0]["TOTAL_DELIVERY_PRICE"]); 
		  
			$rs_opt_stck_l1					= trim($arr_order_rs[0]["OPT_STCK_L1"]); 
			$rs_opt_stck_l2					= trim($arr_order_rs[0]["OPT_STCK_L2"]); 
			$rs_opt_stck_l3					= trim($arr_order_rs[0]["OPT_STCK_L3"]); 
			$rs_opt_stck_size				= trim($arr_order_rs[0]["OPT_STCK_SIZE"]); 
			$rs_opt_wrapping_paper	= trim($arr_order_rs[0]["OPT_WRAPPING_PAPER"]); 
			$rs_opt_stck_outbox_tf	= trim($arr_order_rs[0]["OPT_STCK_OUTBOX_TF"]); 
			$rs_opt_ready_date			= trim($arr_order_rs[0]["OPT_READY_DATE"]); 
			$rs_opt_manager_nm			= trim($arr_order_rs[0]["OPT_MANAGER_NM"]); 

			$mem_no = "";

			$arr_rs = listManagerOrderGoods($conn, $reserve_no, $mem_no, "Y", "N");


			if (sizeof($arr_rs) > 0) {

				$buy_cp_no					= trim($arr_rs[0]["BUY_CP_NO"]);
				$mem_no							= trim($arr_rs[0]["MEM_NO"]);

				for ($j = 0; $j < sizeof($sub_goods_id); $j++) {
					
					$cart_seq = getOrderGoodsMaxSeq($conn, $reserve_no);
					$cart_seq++;
					$use_tf = "Y";

					$goods_no = $sub_goods_id[$j];
					$qty    = $sub_goods_cnt[$j];

					$arr_rs_goods = selectGoods($conn, $goods_no);
					if (sizeof($arr_rs_goods) > 0) {

						$goods_name = $arr_rs_goods[0]["GOODS_NAME"];
						$goods_code = $arr_rs_goods[0]["GOODS_CODE"];
						$goods_option_nm_01 = $arr_rs_goods[0]["GOODS_SUB_NAME"];
						$buy_price = $arr_rs_goods[0]["BUY_PRICE"] * $qty;

						$goods_sub_name = "";
						$goods_option_01 = "";
						$goods_option_02 = ""; 
						$goods_option_03 = "";
						$goods_option_04 = ""; 
						$goods_option_nm_02 = ""; 
						$goods_option_nm_03 = ""; 
						$goods_option_nm_04 = "";
						$cate_01  = ""; 
						$cate_02  = "";
						$cate_03  = ""; 
						$cate_04  = "";
						$sale_price = ""; 
						$extra_price = "";  
						$delivery_price = "";  
						$sa_delivery_price = ""; 
						$tax_tf = "과세";
						$order_state = "1";

						$result = insertOrderGoods($conn, $rs_on_uid, "", $rs_reserve_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $cate_01, $cate_02, $cate_03, $cate_04, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $tax_tf, $order_state, $use_tf, $s_adm_no);

					}

				}
				
				$result = resetOrderInfor($conn, $reserve_no);
				
				if($result)
					$numSuccess ++;

			}

		}

		if ($result) {
		?>
		<script type="text/javascript">
			alert("<?=$numSuccess?>건이 완료되었습니다.");
		//	parent.js_reload();
			self.close();
			
		</script>
		<?

		}
	}

#====================================================================
# DML Process
#====================================================================

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	function js_save() {
		
		var frm = document.frm;

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	
</script>
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
				frm.goods_type.value = "mart";
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
				arr_keywordList[1]+"</a></td></tr></table>";
		
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
	
	function js_goods_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "blank";
		frm.method = "post";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();
		
	}

	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "order_read.php?reserve_no="+reserve_no;

		NewWindow(url, '','860','600','YES'); //window name : 다중창으로 인해 이름 제거 order_detail
		
	}
</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="keyword" value="" />
<input type="hidden" name="goods_type" value="" />
<input type="hidden" name="goods_no" value="" />
<?
	if($chk_reserve_no != null)
	{
		$postvalue = "";
		foreach ($chk_reserve_no as $reserve_no) {
		  $postvalue .= '<input type="hidden" name="chk_reserve_no[]" value="'.$reserve_no.'" />';
		}
		echo $postvalue;
	}
?>

<div id="popupwrap_file">
	<h1>사은품 입력</h1>
	<div id="postsch">
		<h2>* 사은품 추가 발송.</h2>
		<div class="addr_inp">
		
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>
				<tr>
					<th>사은품 등록</th>
					<td colspan="3" style="position:relative" class="line">
						<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
							<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
						</div>
						<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" name="search_name" value="" onKeyDown="startSuggest();" onFocus="this.value='';" />

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
								<th colspan="5" class="line">상품을 검색해서 선택해주세요</th> 
							</tr>
						</thead>
						<tbody class="sub_goods_list">
						</tbody>
						</table>
					</td>
				</tr>
			</table>
			<h2>* 체크된 리스트. </h2>
			<div class="sp10"></div>
			<div class="addr_inp">
				<table cellpadding="0" cellspacing="0" class="colstable02">
					<colgroup>
						<col width="*" />
					</colgroup>
					<tbody class="code_list">
					<?
						if($chk_reserve_no != null)
						{
							foreach ($chk_reserve_no as $reserve_no) {
							  echo "<tr><td><a href=\"javascript:js_view('".$reserve_no."');\">".$reserve_no."</a></td></tr>";
							}
						}
					?>
					</tbody>
				</table>
			</div>
			
			<div class="btn">
			  <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
			  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
			</div>      
		</div>
	</div>
	<br />

</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>