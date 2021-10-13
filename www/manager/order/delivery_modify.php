<?session_start();?>
<?
# =============================================================================
# File Name    : delivery_modify.php
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
	$menu_right = "OD007"; // 메뉴마다 셋팅 해 주어야 합니다

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

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	$temp_no	= trim($temp_no);
	$seq_no		= trim($seq_no);

	
	//echo $pb_nm; 
	//echo $$mode;
	
	$delivery_cp		= SetStringToDB($delivery_cp);
	$delivery_no		= SetStringToDB($delivery_no);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempDelivery($conn, $temp_no, $seq_no);

		$rs_seq_no							= trim($arr_rs[0]["SEQ_NO"]); 
		$rs_reserve_no					= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 
		$rs_goods_no						= SetStringFromDB($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_name					= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_r_mem_nm						= SetStringFromDB($arr_rs[0]["R_MEM_NM"]); 
		$rs_delivery_cp					= SetStringFromDB($arr_rs[0]["DELIVERY_CP"]); 
		$rs_delivery_no					= SetStringFromDB($arr_rs[0]["DELIVERY_NO"]); 

	}

	if ($mode == "U") {
		echo "s";
		$result = updateTempDelivery($conn, $delivery_cp, $delivery_no, $temp_no, $seq_no);
	}

	if ($mode == "D") {
		$result = deleteOrder($conn,$order_no);
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
		alert('정상 처리 되었습니다.');
		location.href =  "order_list.php<?=$strParam?>";
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
	
	// 저장 버튼 클릭 시 
	function js_save() {
		
		var seq_no = "<?= $seq_no ?>";
		var frm = document.frm;

		if (isNull(frm.delivery_cp.value)) {
			alert('택배사를 선택해주세요.');
			frm.delivery_cp.focus();
			return ;		
		}

		if (isNull(frm.delivery_no.value)) {
			alert('송장번호를 입력해주세요.');
			frm.delivery_no.focus();
			return ;		
		}

		frm.mode.value = "U";

		frm.target = "";
		frm.method = "post";
		frm.action = "delivery_modify.php";
		frm.submit();
	}

</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="seq_no" value="<?= $seq_no?>">
<input type="hidden" name="keyword" value="">

<div id="popupwrap_file">
	<h1>송장 등록 수정</h1>
	<div id="postsch">
		<h2>* 송장 정보를 수정 합니다.</h2>
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
						<th>주문번호</th>
						<td colspan="3">
							<?=$rs_reserve_no?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>수령자</th>
						<td colspan="3">
							<?= $rs_r_mem_nm?>
						</td>
					</tr>
					<tr>
						<th>상품검색</th>
						<td colspan="3" class="line">
							<?=$rs_goods_name?>
						</td>
					</tr>
					</tr>
					<tr>
						<th>택배사</th>
						<td colspan="3">
							<?=makeSelectBox($conn,"DELIVERY_CP", "delivery_cp","120", "택배사 선택", "", $rs_delivery_cp)?>
						</td>
					</tr>
					<tr>
						<th>송장</th>
						<td colspan="3">
							<input type="Text" name="delivery_no" value="<?= $rs_delivery_no?>" style="width:120px;" itemname="옵션명1" required class="txt">
						</td>
					</tr>
				</tbody>
			</table>
			<br><br><br>
		</div>
		<div class="btn">
		<? if ($adm_no <> "" ) {?>
			<? if ($sPageRight_U == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? } else {?>
			<? if ($sPageRight_I == "Y") {?>
      <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		<? }?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
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
	//searchKeyword();
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