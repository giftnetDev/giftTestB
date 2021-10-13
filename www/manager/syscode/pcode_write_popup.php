<?session_start();?>
<?
# =============================================================================
# File Name    : pcode_write_popup.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.13
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
	$menu_right = "SY002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";
	
#====================================================================
# common_header
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/syscode/syscode.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode 			= trim($mode);
	$pcode_no		= trim($pcode_no);
	
	$result = false;
#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {
		
		$pcode_seq_no = "0";
		
		#echo "para--->".$pcode." ".$pcode_nm." ".$pcode_memo." ".$pcode_seq_no." ".$use_tf." ".$reg_adm."<br>";

		$result = insertPcode($conn, $g_site_no, $pcode, $pcode_nm, $pcode_memo, $pcode_seq_no, $use_tf, $s_adm_no);

	}

	if ($mode == "S") {

		$arr_rs = selectPcode($conn, $pcode_no);

		$rs_pcode_no			= trim($arr_rs[0]["PCODE_NO"]); 
		$rs_pcode					= trim($arr_rs[0]["PCODE"]); 
		$rs_pcode_nm			= trim($arr_rs[0]["PCODE_NM"]); 
		$rs_pcode_memo		= trim($arr_rs[0]["PCODE_MEMO"]); 
		$rs_pcode_seq_no	= trim($arr_rs[0]["PCODE_SEQ_NO"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($mode == "U") {
		renamePcodeOnDcode($conn, $pcode, $pcode_no);
		$result = updatePcode($conn, $g_site_no, $pcode, $pcode_nm, $pcode_memo, $pcode_seq_no, $use_tf, $s_adm_no, $pcode_no);
	}

	if ($mode == "D") {
		$result = deletePcode($conn, $s_adm_no, $pcode_no);
	}

	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		opener.js_search();
		self.close();
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
<!--<script type="text/javascript" src="../js/httpRequest.js"></script>--> <!-- Ajax js -->

<style type="text/css">
<!--
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#pop_table_scroll { z-index: 1;  height: 220; background-color:#f7f7f7; overflow: auto; height: 325px; border:1px solid #d1d1d1;}
-->
</style>
<script language="javascript">


function getXMLHttpRequest() {
	if (window.ActiveXObject) {
		try {
			return new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
	
		try {
			return new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e1) { return null; }
	}
	
	} else if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else {
	return null;
	}
}

var httpRequest = null;

function sendRequest(url, params, callback, method) {
	
	httpRequest = getXMLHttpRequest();
	var httpMethod = method ? method : 'GET';
	
	if (httpMethod != 'GET' && httpMethod != 'POST') {
		httpMethod = 'GET';
	}
	
	var httpParams = (params == null || params == '') ? null : params;
	var httpUrl = url;
	
	if (httpMethod == 'GET' && httpParams != null) {
		httpUrl = httpUrl + "?" + httpParams;
	}
	
	httpRequest.open(httpMethod, httpUrl, true);
	httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	httpRequest.onreadystatechange = callback;
	httpRequest.send(httpMethod == 'POST' ? httpParams : null);
}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var pcode_no = "<?= $pcode_no ?>";
		var frm = document.frm;
		
		if (isNull(frm.pcode.value)) {
			alert('코드를 입력해주세요.');
			frm.pcode.focus();
			return ;		
		}

		if (isNull(frm.pcode_nm.value)) {
			alert('코드명을 입력해주세요.');
			frm.pcode_nm.focus();
			return ;		
		}

		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}

		if (isNull(pcode_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}

		frm.method = "post";
		frm.action = "pcode_write_popup.php";
		frm.submit();
	}

	function js_delete() {
		
		bDelOK = confirm('정말 삭제 하시겠습니까?');//정말 삭제 하시겠습니까?
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.method = "post";
			frm.action = "pcode_write_popup.php";
			frm.submit();
		} else {
			return;
		}
	}

	// Ajax
	function sendKeyword() {

		if (frm.old_pcode.value != frm.pcode.value)	{

			var keyword = document.frm.pcode.value;

			//alert(keyword);
						
			if (keyword != '') {
				var params = "keyword="+encodeURIComponent(keyword);
			
				//alert(params);
				sendRequest("pcode_dup_check.php", params, displayResult, 'POST');
			}
			//setTimeout("sendKeyword();", 100);
		} else {
			js_save();
		}
	}

	function displayResult() {
		
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				
				var resultText = httpRequest.responseText;
				
				var result = resultText;
				
				//alert(result);
				
				//return;
				if (result == "1") {
					alert("사용중인 코드 입니다.");
					return;
				} else {
					js_save();
				}
			} else {
				alert("에러 발생: "+httpRequest.status);
			}
		}
	}
</script>

</head>
<body id="popup_code" onload="frm.pcode.focus();">
<form name="frm" method="post">
<input type="hidden" name="mode" value="" >
<input type="hidden" name="pcode_no" value="<?= $pcode_no ?>">




<div id="popupwrap_code">
	<h1>대분류 코드 등록</h1>
	<div id="postsch">
		<h2>* 시스템에서 사용할 코드의 대분류를 등록하는 화면 입니다.</h2>
		<div class="addr_inp">
		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
						<colgroup>
							<col width="20%">
							<col width="80%">
						</colgroup>
							<tr>
								<td class="lpd20 left bu03">코드</td>
								<td colspan="3" class="lpd20 rpd20 right">
									<input type="Text" name="pcode" value="<?= $rs_pcode ?>" style="width:95%;" required class="txt">
									<input type="hidden" name="old_pcode" value="<?= $rs_pcode ?>">
								</td>
							</tr>
							<tr>
								<td class="lpd20 left bu03">코드명</td>
								<td colspan="3" class="lpd20 rpd20 right">
									<input type="Text" name="pcode_nm" value="<?= $rs_pcode_nm ?>" style="width:95%;" required class="txt">
								</td>
							</tr>
							<tr>
								<td class="lpd20 left">코드설명</td>
								<td colspan="3" class="lpd20 rpd20 right">
									<textarea name="pcode_memo" class="txt" style="width:95%" rows="4"><?= $rs_pcode_memo ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="lpd20 left">사용여부</td>
								<td class="lpd20 right">
									<input type="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용<span style="width:20px;"></span>
									<input type="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 미사용
									<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
								</td>
							<tr>
					</table>
				</td>
			</tr>
		</table>
		</div>

		<div class="btn">
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
		</div>

		
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>