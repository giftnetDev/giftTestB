<?session_start();?>
<?
# =============================================================================
# File Name    : company_select.php
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
	$menu_right = "BO004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

		$arr_cp_no = explode(",",$send_data);

		if (sizeof($arr_cp_no) > 0) {

			for ($m = 0; $m < sizeof($arr_cp_no); $m++) {
				//$result =  deleteGoodsPrice($conn, $goods_no, $cp_no);
				$result = insertGoodsPriceUpdate($conn, "TB_GOODS_PRICE", $goods_no, $arr_cp_no[$m], "0", $new_sale_price, "0", $s_adm_no);
				$result = insertGoodsPrice($conn, $goods_no, $arr_cp_no[$m], $new_sale_price, $memo, $s_adm_no);
			}
		}

?>
<script type="text/javascript">
	opener.location = "goods_price_list.php";
	self.close();
</script>
<?
		exit;
	}

	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		#GOODS_NO, GOODS_TYPE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
		#PRICE, SALE_PRICE, EXTRA_PRICE, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, CONTENTS,
		#READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, ADM_NO, UP_DATE, DEL_ADM, DEL_DATE

		$rs_goods_no				= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name	= SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_price						= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price				= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt				= trim($arr_rs[0]["STOCK_CNT"]); 
		$rs_img_url					= trim($arr_rs[0]["IMG_URL"]); 
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
		$rs_contents				= trim($arr_rs[0]["CONTENTS"]); 
		$rs_read_cnt				= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq				= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		$content						= trim($arr_rs[0]["CONTENTS"]); 

		$arr_rs_file = selectGoodsFile($conn, $goods_no);

		$arr_rs_option = selectGoodsOption($conn, $goods_no);
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "goods_list.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

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
				frm.action = "search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
				//var params = "keyword="+encodeURIComponent(keyword);
				//var params = "keyword="+keyword;
				//alert(params);
				//sendRequest("search_dept.asp", params, displayResult, 'POST');

			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
		
//		if (httpRequest.readyState == 4) {
//			if (httpRequest.status == 200) {
				
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
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+
				arr_keywordList[1]+"</a></td><td width='105'>판매가 : "+arr_keywordList[3]+"</td></tr></table>";
		
				//alert(html);
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

		frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];
		frm.goods_buy_price.value			= arr_keywordValues[2];
		frm.goods_sale_price.value		= arr_keywordValues[3];
		
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


function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "goods_price_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var goods_no = "<?= $goods_no ?>";
	
	frm.new_sale_price.value = frm.new_sale_price.value.trim();


	if (isNull(frm.goods_sale_price.value)) {
		alert('상품명을 선택해주세요.');
		frm.search_name.focus();
		return ;		
	}

	if (isNull(frm.new_sale_price.value)) {
		alert('판매가를 입력해주세요.');
		frm.new_sale_price.focus();
		return ;		
	}

	var send_data = ""; 
		
	for(var i = 0; i < frm.sel_right.length; i++) {
		frm.sel_right.options[i].selected = true; 
		if (send_data == "") {
			send_data = frm.sel_right.options[i].value;	
		} else {
			send_data = send_data+","+frm.sel_right.options[i].value;	
		}
	}	

	frm.send_data.value = send_data;

	if (frm.send_data.value == "") {
		alert("가격 적용 업체가 선택되지 않았습니다.");
		return;
	}

	if (isNull(goods_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "I";
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

function LeftToRight() {
	var objSel, objSel
	var frm = document.frm;
	
	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objLeft.length; i++)
		if(objLeft.options[i].selected)
			objRight.options[objRight.length] = new Option(objLeft.options[i].text,  objLeft.options[i].value);

	for(var i = 0; i < objRight.length; i++) {
		for(var j = 0; j < objLeft.length; j++) {
			if(objLeft.options[j].selected) {
				objLeft.options[j] = null;
				break;
			}
		}
	}
}

function RightToLeft() {
	var objSel, objSel
	var frm = document.frm;

	objLeft = frm.sel_left;
	objRight = frm.sel_right;
		
	for(var i = 0; i < objRight.length; i++)
		if(objRight.options[i].selected)
			objLeft.options[objLeft.length] = new Option(objRight.options[i].text,  objRight.options[i].value);

	for(var i = 0; i < objLeft.length; i++) {
		for(var j = 0; j < objRight.length; j++) {
			if(objRight.options[j].selected) {
				objRight.options[j] = null;
				break;
			}	
		}
	} 
}

</script>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="depth" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="send_data" value="">

<div id="popupwrap_file">
	<h1>상품 가격 관리</h1>
	<div id="postsch_code">
		<h2>* 업체별 상품 가격을 등록합니다. 상품명을 입력하세요.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">

					<colgroup>
						<col width="15%" />
						<col width="35%" />
						<col width="15%" />
						<col width="35%" />
					</colgroup>
					<thead>
						<tr>
							<th>상품검색</th>
							<td colspan="3" style="position:relative" class="line">
								<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
									<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
								</div>
								<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value="<?=$rs_goods_name?>" onKeyDown="startSuggest();" />
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>상품명</th>
							<td colspan="3" class="line">
								<input type="text" class="txt" style="width:95%; " name="goods_name" required value="<?=$rs_goods_name?>" readonly="1" />
							</td>
						</tr>
						<tr>
							<th>공급가</th>
							<td class="line">
								<input type="text" class="txt" style="width:110px" name="goods_buy_price" value="<?=$rs_buy_price?>" readonly="1" />
							</td>
							<th>기준 판매가</th>
							<td class="line">
								<input type="text" class="txt" style="width:110px" name="goods_sale_price" value="<?=$rs_sale_price?>" readonly="1" />
							</td>
						</tr>

						<tr>
							<th>업체선택</th>
							<td class="line" colspan="3">
								<table border="0">
									<tr>
										<td width="230" style="padding: 10px 10px 10px 0px">
											* 업체 리스트 <br>
											<select name="sel_left" size="17" style="width:230px" multiple>
												<?
													$arr_company_no = listCompanyPrice($conn, $goods_no, $price, "NO");
													
													if (sizeof($arr_company_no) > 0) {
						
														for ($j = 0 ; $j < sizeof($arr_company_no); $j++) {
															$CP_NO					= trim($arr_company_no[$j]["CP_NO"]);
															$CP_NM					= trim($arr_company_no[$j]["CP_NM"]);
												?>
												<option value="<?=$CP_NO?>"><?=$CP_NM?></option>
												<?
														}
													}
												?>
											</select>
										</td>
										<td style="padding: 10px 10px 10px 10px" align="center">
											<input type=button name=LR value=' &gt; ' onClick="javascript:LeftToRight()" class="txt" style="width:30px">
											<br><br>
											<input type=button name=RL value=' &lt; '  onClick="javascript:RightToLeft()" class="txt" style="width:30px">
										</td>
										<td width="230" style="padding: 10px 10px 10px 10px">
											* 적용 업체 리스트 <br>
											<select name="sel_right" size="17" style="width:230px" multiple>
												<?
													$arr_company_yes = listCompanyPrice($conn, $goods_no, $price, "YES");
													
													if (sizeof($arr_company_yes) > 0) {
						
														for ($j = 0 ; $j < sizeof($arr_company_yes); $j++) {
															$CP_NO					= trim($arr_company_yes[$j]["CP_NO"]);
															$CP_NM					= trim($arr_company_yes[$j]["CP_NM"]);
												?>
												<option value="<?=$CP_NO?>"><?=$CP_NM?></option>
												<?
														}
													}
												?>
											</select>
										</td>
									</table>
							</td>
						</tr>

						<tr>
							<th>판매가</th>
							<td class="line" colspan="3">
								<input type="text" class="txt" style="width:110px" name="new_sale_price" value="<?=$price?>" required onkeyup="return isNumber(this)"/>
							</td>
						</tr>

					</tbody>
					<tfoot>
						<tr>
							<td colspan="10"></td>
						</tr>
					</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</div>
		
	<div class="btn">

				<? if ($adm_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a> 
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a> 
					<? } ?>
				<? }?>
				<? if ($adm_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
				<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
				<? } ?>
	</div>

</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>