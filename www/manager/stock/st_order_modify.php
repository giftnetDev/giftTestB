<?session_start();?>
<?
# =============================================================================
# File Name    : admin_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG003"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$temp_no	= trim($temp_no);
	$order_no		= trim($order_no);

	
	//echo $pb_nm; 
	//echo $$mode;
	
	$cp_type				= SetStringToDB($cp_type);
	$cp_nm					= SetStringToDB($cp_nm);
	$cp_phone				= SetStringToDB($cp_phone);
	$cp_hphone			= SetStringToDB($cp_hphone);
	$cp_fax					= SetStringToDB($cp_fax);
	$cp_addr				= SetStringToDB($cp_addr);
	$re_addr				= SetStringToDB($re_addr);
	$homepage				= SetStringToDB($homepage);
	$biz_no					= SetStringToDB($biz_no);
	$ceo_nm					= SetStringToDB($ceo_nm);
	$upjong					= SetStringToDB($upjong);
	$uptea					= SetStringToDB($uptea);
	$manager_nm			= SetStringToDB($manager_nm);
	$phone					= SetStringToDB($phone);
	$hphone					= SetStringToDB($hphone);
	$fphone					= SetStringToDB($fphone);
	$email					= SetStringToDB($email);
	$ad_type				= SetStringToDB($ad_type);
	$account_bank		= SetStringToDB($account_bank);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempStOrder($conn, $temp_no, $order_no);

		//TEMP_NO, ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO, GOODS_CODE,
		//QTY, GOODS_NAME, GOODS_SUB_NAME,
		//GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
		//GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
		//BUY_PRICE, ORDER_STATE, ORDER_DATE, PAY_DATE,
		//USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE

		$rs_cp_no								= trim($arr_rs[0]["CP_NO"]); 
		$rs_order_goods_no			= SetStringFromDB($arr_rs[0]["ORDER_GOODS_NO"]); 
		$rs_buy_cp_no						= SetStringFromDB($arr_rs[0]["BUY_CP_NO"]); 
		$rs_goods_no						= SetStringFromDB($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_name					= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_qty									= trim($arr_rs[0]["QTY"]); 
		$rs_goods_option_nm_01	= SetStringFromDB($arr_rs[0]["GOODS_OPTION_NM_01"]); 
		$rs_goods_option_01			= SetStringFromDB($arr_rs[0]["GOODS_OPTION_01"]); 
		$rs_goods_option_nm_02	= SetStringFromDB($arr_rs[0]["GOODS_OPTION_NM_02"]); 
		$rs_goods_option_02			= SetStringFromDB($arr_rs[0]["GOODS_OPTION_02"]); 
		$rs_goods_option_nm_03	= SetStringFromDB($arr_rs[0]["GOODS_OPTION_NM_03"]); 
		$rs_goods_option_03			= SetStringFromDB($arr_rs[0]["GOODS_OPTION_03"]); 
		$rs_buy_price						= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_order_date					= trim($arr_rs[0]["ORDER_DATE"]); 
		$rs_pay_date						= trim($arr_rs[0]["PAY_DATE"]); 
		$rs_order_state					= trim($arr_rs[0]["ORDER_STATE"]); 
		$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 

	}

	if ($mode == "U") {
		$result = updateTempStOrder($conn, $cp_type, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $pay_date, $buy_price, $up_adm, $temp_no, $order_goods_no);
	}

	if ($mode == "D") {
		$result = deleteStOrder($conn,$order_no);
	}

	
	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "U") {
?>	
<script language="javascript">
	opener.js_reload();
	self.close();
	//location.href =  "company_modify.php<?=$strParam?>&mode=S&temp_no=<?=$temp_no?>&cp_no=<?=$cp_no?>";
</script>
<?
		} else {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  "st_order_list.php<?=$strParam?>";
</script>
<?
		}
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
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script language="javascript">
	
	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var order_no = "<?= $rs_order_no ?>";
		var frm = document.frm;

		if (isNull(frm.cp_type.value)) {
			alert('���޾�ü�� �������ּ���.');
			frm.cp_type.focus();
			return ;		
		}

		if (isNull(frm.goods_no.value)) {
			alert('��ǰ�� �������ּ���.');
			frm.goods_no.focus();
			return ;		
		}
		
		if (isNull(frm.qty.value)) {
			alert('������ �Է����ּ���.');
			frm.qty.focus();
			return ;		
		}

		frm.mode.value = "U";

		frm.target = "";
		frm.method = "post";
		frm.action = "st_order_modify.php";
		frm.submit();
	}

	// tag ���� ���̾ �� �ε� �Ǳ������� �Ǻ��ϱ� ���� �ʿ�
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
				frm.action = "/manager/goods/search_goods.php";
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

	function searchKeyword() {
		var frm = document.frm;
		var keyword = document.frm.search_name.value;
		frm.keyword.value = keyword;
		frm.action = "/manager/goods/search_goods.php";
		frm.target = "ifr_hidden";
		frm.submit();
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
				
				html += "<table width='100%' border='0'><tr><td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td><td><a href=\"javascript:js_select('"+
				arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+
				arr_keywordList[1]+"</a></td><td width='105px'>�ǸŰ� : "+arr_keywordList[3]+"</td></tr></table>";
		
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
		//frm.goods_buy_price.value			= arr_keywordValues[2];
		//frm.goods_sale_price.value		= arr_keywordValues[3];
		
		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

		//document.getElementById('goods_detail').src = "order_goods_detail.php?goods_no="+frm.goods_no.value+"&cp_no=<?=$cp_no?>&mode=S";

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


</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="order_goods_no" value="<?= $rs_order_goods_no?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_name" value="<?=$rs_goods_name?>">

<div id="popupwrap_file">
	<h1>�԰� ��� ����</h1>
	<div id="postsch">
		<h2>* �԰� ������ ���� �մϴ�.</h2>
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable">

				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
				<thead>
					<tr>
						<th>���޾�ü</th>
						<td colspan="5" style="position:relative" class="line">
							<?= makeCompanySelectBox($conn, '����', $rs_buy_cp_no);?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>��ǰ�˻�</th>
						<td colspan="5" style="position:relative" class="line">
							<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
								<div id="suggestList" style="position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
							</div>
							<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value="<?=$rs_goods_name?>" onKeyDown="startSuggest();" />
						</td>
					</tr>
					<tr>
						<th>��ǰ��ȣ</th>
						<td><input type="Text" name="goods_no" value="<?= $rs_goods_no?>" style="width:140px;" itemname="��ǰ ��ȣ" required class="txt"></td>
						<th>�԰����</th>
						<td>
							<input type="Text" name="qty" value="<?= $rs_qty ?>" style="width:40px;" required onkeyup="return isPhoneNumber(this)" class="txt">
						</td>
					</tr>
					<tr>
						<th>���԰�</th>
						<td colspan="3">
							<input type="Text" name="buy_price" value="<?= $rs_buy_price?>" style="width:120px;" itemname="����" required onkeyup="return isNumber(this)" class="txt">
						</td>
					</tr>
					<tr>
						<th>�ɼǸ�1</th>
						<td>
							<input type="Text" name="goods_option_nm_01" value="<?= $rs_goods_option_nm_01?>" style="width:120px;" itemname="�ɼǸ�1" required class="txt">
						</td>
						<th>�ɼ�1</th>
						<td>
							<input type="Text" name="goods_option_01" value="<?= $rs_goods_option_01?>" style="width:120px;" itemname="�ɼ�1" required class="txt">
						</td>
					</tr>
					<tr>
						<th>�ɼǸ�2</th>
						<td>
							<input type="Text" name="goods_option_nm_02" value="<?= $rs_goods_option_nm_02?>" style="width:120px;" itemname="�ɼǸ�2" required class="txt">
						</td>
						<th>�ɼ�2</th>
						<td>
							<input type="Text" name="goods_option_02" value="<?= $rs_goods_option_02?>" style="width:120px;" itemname="�ɼ�2" required class="txt">
						</td>
					</tr>
					<tr>
						<th>�ɼǸ�3</th>
						<td>
							<input type="Text" name="goods_option_nm_03" value="<?= $rs_goods_option_nm_03?>" style="width:120px;" itemname="�ɼǸ�3" required class="txt">
						</td>
						<th>�ɼ�3</th>
						<td>
							<input type="Text" name="goods_option_03" value="<?= $rs_goods_option_03?>" style="width:120px;" itemname="�ɼ�3" required class="txt">
						</td>
					</tr>
					<tr>
						<th>������</th>
						<td colspan="3">
							<input type="Text" name="pay_date" value="<?= $rs_pay_date?>" style="width:100px;" itemname="������" required class="txt">
							<a href="javascript:show_calendar('document.frm.pay_date', document.frm.pay_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
						</td>
					</tr>
				</tbody>
			</table>
				
		</div>
		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<script type="text/javascript">
<?
	if ($rs_goods_no == "") {
		if ($rs_goods_name <> "") {
?>
	searchKeyword();
<?
		}
	}
?>
</script>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>