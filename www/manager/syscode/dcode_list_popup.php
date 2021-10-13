<?session_start();?>
<?
# =============================================================================
# File Name    : dcode_list_popup.php
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
	$menu_right = "SY002"; // �޴����� ���� �� �־�� �մϴ�

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
		
		$type = "DCODE";
		$result = dupDcode($conn, $dcode, $pcode);

		if ($result == 0) {
		
			$dcode_seq_no = "0";
			//echo "para--->".$pcode." ".$pcode_nm." ".$pcode_memo." ".$pcode_seq_no." ".$use_tf." ".$reg_adm."<br>";
			$result = insertDcode($conn, $pcode, $dcode, $dcode_nm, $dcode_ext, $dcode_seq_no, $use_tf, $s_adm_no);

		}
		
		$mode = "R";
	}

	if ($mode == "U") {

		//echo $dcode_no."<br>";

		$result = updateDcode($conn, $pcode, $dcode, $dcode_nm, $dcode_ext, $use_tf, $s_adm_no, $dcode_no);
		$mode = "R";

	}

	if ($mode == "T") {

		$result_no = updateDcodeUseTF($conn, $use_tf, $s_adm_no, $pcode, $dcode_no);

		$mode = "R";

	}


	if ($mode == "D") {

		$result = deleteDcode($conn, $s_adm_no, $pcode, $dcode_no);
		$mode = "R";

	}

	if (($mode == "S") || ($mode == "R")) {

		$arr_rs = selectPcode($conn, $pcode_no);

		$rs_pcode_no			= trim($arr_rs[0]["PCODE_NO"]); 
		$rs_pcode					= trim($arr_rs[0]["PCODE"]); 
		$rs_pcode_nm			= trim($arr_rs[0]["PCODE_NM"]); 
		$rs_pcode_memo		= trim($arr_rs[0]["PCODE_MEMO"]); 
		$rs_pcode_seq_no	= trim($arr_rs[0]["PCODE_SEQ_NO"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 

	}


	$use_tf			= "";
	$del_tf			= "N";
	$nPage			= "1";
	$nPageSize  = "1000";

	$arr_rs_dcode = listDcode($conn, $rs_pcode, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	if ($result) {
?>	
<script language="javascript">
	//alert('���� ó�� �Ǿ����ϴ�.');
</script>
<?
		//exit;
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
#pop_table_scroll { z-index: 1;  overflow: auto; height: 368px; }
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

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;
		
		var dcode_no = frm.dcode_no.value;
			
		if (isNull(frm.dcode.value)) {
			alert('�ڵ带 �Է����ּ���.');
			frm.dcode.focus();
			return ;		
		}

		if (isNull(frm.dcode_nm.value)) {
			alert('�ڵ���� �Է����ּ���.');
			frm.dcode_nm.focus();
			return ;		
		}

		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}

		if (isNull(dcode_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}
		
		if (frm.mode.value == "U") {
			bDelOK = confirm('���� �Ͻðڽ��ϱ�?');//
			if (bDelOK == false) {	
				return;
			}
		}
		
		//alert(frm.dcode_no.value);

		frm.method = "post";
		frm.target = "";
		frm.action = "dcode_list_popup.php";
		frm.submit();
	}

	function js_delete() {
		
		bDelOK = confirm('���� ���� �Ͻðڽ��ϱ�?');//���� ���� �Ͻðڽ��ϱ�?
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.method = "post";
			frm.target = "";
			frm.action = "dcode_list_popup.php";
			frm.submit();
		} else {
			return;
		}
	}

	function js_view (rn, dcode_no, dcode, dcode_nm,  dcode_ext, use_tf) {
			
			frm.mode.value = "U";
			//alert(dcode_no);
			frm.dcode_no.value = dcode_no;

			//alert(frm.dcode_no.value);

			frm.dcode.value = dcode;
			frm.old_dcode.value = dcode;
			frm.dcode_nm.value = dcode_nm;
			frm.dcode_ext.value = dcode_ext;

			if (use_tf == "Y") {
				frm.rd_use_tf[0].checked = true;
				frm.use_tf.value = "Y";
			} else {
				frm.rd_use_tf[1].checked = true;
				frm.use_tf.value = "N";
			}

	}

function js_toggle(dcode_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('��� ���θ� ���� �Ͻðڽ��ϱ�?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.dcode_no.value = dcode_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

/*
 * @(#)menu.js
 * 
 * ���������� : �޴� ���� �ٲٱ� ��ũ��Ʈ ����
 * �ۼ�  ���� : 2003.12.01
 */  


var preid = -1;

function js_up(n) {
	
	preid = parseInt(n);

	if (preid > 1) {
		

		temp1 = document.getElementById("t").rows[preid].innerHTML;
		temp2 = document.getElementById("t").rows[preid-1].innerHTML;

		var cells1 = document.getElementById("t").rows[preid].cells;
		var cells2 = document.getElementById("t").rows[preid-1].cells;

		for(var j=0 ; j < cells1.length; j++) {
			
			if (j != 0) {
				var temp = cells2[j].innerHTML;

				cells2[j].innerHTML =cells1[j].innerHTML;
				cells1[j].innerHTML = temp;

				var tempCode = document.frm.seq_dcode_no[preid-2].value;
			
				document.frm.seq_dcode_no[preid-2].value = document.frm.seq_dcode_no[preid-1].value;
				document.frm.seq_dcode_no[preid-1].value = tempCode;
			}
		}
		
		//preid = preid - 1;
		js_change_order();

	} else {
		alert("���� ������ �ֽ��ϴ�. ");
	}
}


function js_down(n) {

	preid = parseInt(n);

	//alert(preid_plus);

	if (preid < document.getElementById("t").rows.length-1) {
		
		temp1 = document.getElementById("t").rows[preid].innerHTML;
		temp2 = document.getElementById("t").rows[preid+1].innerHTML;
		
		var cells1 = document.getElementById("t").rows[preid].cells;
		var cells2 = document.getElementById("t").rows[preid+1].cells;
		
		for(var j=0 ; j < cells1.length; j++) {

			if (j != 0) {
				var temp = cells2[j].innerHTML;

			
				cells2[j].innerHTML =cells1[j].innerHTML;
				cells1[j].innerHTML = temp;
	
				var tempCode = document.frm.seq_dcode_no[preid-1].value;
				document.frm.seq_dcode_no[preid-1].value = document.frm.seq_dcode_no[preid].value;
				document.frm.seq_dcode_no[preid].value = tempCode;
			}
		}
		
		//preid = preid + 1;	
		js_change_order();
	} else{
		alert("���� ������ �ֽ��ϴ�. ");
	}
}

function js_change_order() {
	
	if(document.getElementById("t").rows.length < 2) {
		alert("������ ������ �޴��� �����ϴ�");//������ ������ �޴��� �����ϴ�");
		return;
	}

	document.frm.mode.value = "O";
	document.frm.target = "ifr_hidden";
	document.frm.action = "dcode_order_dml.php";
	document.frm.submit();

}

	// Ajax
	function sendKeyword() {

		if (frm.old_dcode.value != frm.dcode.value)	{

			var keyword = document.frm.dcode.value+"^"+document.frm.pcode.value;
			
			//alert(keyword);

			if (keyword != '') {
				var params = "keyword="+encodeURIComponent(keyword);

				sendRequest("dcode_dup_check.php", params, displayResult, 'POST');
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

				if (result == "1") {
					alert("������� �ڵ� �Դϴ�.");
					return;
				} else {
					js_save();
				}

			} else {
				alert("���� �߻�: "+httpRequest.status);
			}
		}
	}

</script>

</head>
<body id="popup_code" onload="frm.dcode.focus();">

<form name="frm" method="post">
<input type="hidden" name="mode" value="" >
<input type="hidden" name="pcode" value="<?= $rs_pcode ?>">
<input type="hidden" name="pcode_no" value="<?= $pcode_no ?>">
<input type="hidden" name="dcode_no" value="">
<input type="hidden" name="seq_no" value="">


<div id="popupwrap_code">
	<h1>���κз� �ڵ� ���</h1>
	<div id="postsch">
		<h2>* �ý��ۿ��� ����� �ڵ��� ���κз��� ����ϴ� ȭ�� �Դϴ�.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
						<colgroup>
							<col width="20%">
							<col width="30%">
							<col width="20%">
							<col width="30%">
						</colgroup>
							<tr>
								<td colspan="4"><b><?= $rs_pcode_nm ?></b></td>
							</tr>
							<tr>
								<th>�ڵ�</th>
								<td>
									<input type="Text" name="dcode" value="" style="width:95%;" required class="txt">
									<input type="hidden" name="old_dcode" value="">
								</td>
								<th>�ڵ��</th>
								<td>
									<input type="Text" name="dcode_nm" value="" style="width:95%;" required class="txt">
								</td>
							</tr>
							<tr>
								<th>��Ÿ����</th>
								<td colspan="3">
									<input type="Text" name="dcode_ext" value="" style="width:95%;" class="txt">
								</td>
							<tr>
							<tr>
								<th>��뿩��</th>
								<td colspan="3">
									<input type="radio" name="rd_use_tf" value="Y" checked> ���<span style="width:20px;"></span>
									<input type="radio" name="rd_use_tf" value="N"> �̻��
									<input type="hidden" name="use_tf" value=""> 
								</td>
							<tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		<div class="btn">
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
		</div>

		<div class="addr_inp">	
		<table cellpadding="0" cellspacing="0" border="0" width="95%">
		<tr>
			<td width="100%" align="left">
				<div id="pop_table_list">
					<div id="pop_table_scroll">
						<table id='t' cellpadding="0" class="rowstable" cellspacing="0" border="0" width="100%">
							<colgroup>
								<col width="20%">
								<col width="30%">
								<col width="30%">
								<col width="20%">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">NO.</th>
									<th scope="col">�ڵ�</th>
									<th scope="col">�ڵ��</th>
									<th class="end" scope="col">��뿩��</th>
								</tr>
							</thead>
							<tbody>
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs_dcode) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs_dcode); $j++) {
										
										#rn, DCODE_NO, PCODE, DCODE, DCODE_NM, DCODE_SEQ_NO, 
										#USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

										$rn							= trim($arr_rs_dcode[$j]["rn"]);
										$DCODE_NO				= trim($arr_rs_dcode[$j]["DCODE_NO"]);
										$PCODE					= trim($arr_rs_dcode[$j]["PCODE"]);
										$DCODE					= trim($arr_rs_dcode[$j]["DCODE"]);
										$DCODE_NM				= trim($arr_rs_dcode[$j]["DCODE_NM"]);
										$DCODE_EXT			= trim($arr_rs_dcode[$j]["DCODE_EXT"]);
										$DCODE_SEQ_NO		= trim($arr_rs_dcode[$j]["DCODE_SEQ_NO"]);
										$USE_TF					= trim($arr_rs_dcode[$j]["USE_TF"]);
										$DEL_TF					= trim($arr_rs_dcode[$j]["DEL_TF"]);
										$REG_DATE				= trim($arr_rs_dcode[$j]["REG_DATE"]);

										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							

										if ($USE_TF == "Y") {
											$STR_USE_TF = "���";
										} else {
											$STR_USE_TF = "������";
										}
							?>
								<tr>
									<td class="sort"><span><?=$rn?></span> 
										<a href="javascript:js_up('<?=($j+1)?>');"><img src="../images/admin/icon_arr_top.gif" alt="" /></a> 
										<a href="javascript:js_down('<?=($j+1)?>');"><img src="../images/admin/icon_arr_bot.gif" alt="" /></a>
									</td>
									<td>
										<a href="javascript:js_view('<?= $rn ?>','<?= $DCODE_NO ?>','<?= $DCODE ?>','<?= $DCODE_NM ?>','<?= $DCODE_EXT ?>','<?= $USE_TF ?>');"><?= $DCODE ?></a>
										<input type="hidden" name="seq_dcode_no" value="<?=$DCODE_NO?>">
										<input type="hidden" name="dcode_seq_no[]" value="<?=$DCODE_NO?>">
									</td>
									<td><?= $DCODE_NM?></td>
									<td><a href="javascript:js_toggle('<?=$DCODE_NO?>','<?=$USE_TF?>');"><?=$STR_USE_TF?></a></td>
								</tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="7">�����Ͱ� �����ϴ�. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="1000" height="1000" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>