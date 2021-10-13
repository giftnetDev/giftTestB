<?session_start();?>
<?
# =============================================================================
# File Name    : category_list.php
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
	$memu_right = "GD003"; // �޴����� ���� �� �־�� �մϴ�

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


	#sPageRight_R �ش� �޴� �б� ����
	#sPageRight_I �ش� �޴� �Է� ����
	#sPageRight_U �ش� �޴� ���� ����
	#sPageRight_D �ش� �޴� ���� ����
	#sPageRight_  �ش� �޴� �ڵ� �޴� ���� ���� ������ �ڵ� ���� ����Ѵ�.

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/category/category.php";

#====================================================================
# Request Parameter
#====================================================================

$m_level = Trim($m_level);
$m_seq01 = Trim($m_seq01);
$m_seq02 = Trim($m_seq02);
$m_seq03 = Trim($m_seq03);

#====================================================================
# Declare variables
#====================================================================

#====================================================================
# Request Parameter
#====================================================================

$cate_no			= trim($cate_no);
$m_level			= trim($m_level);
$m_seq01			= trim($m_seq01);
$m_seq02			= trim($m_seq02);
$m_seq03			= trim($m_seq03);
$cate_name		= trim($cate_name);
$cate_memo		= trim($cate_memo);
$cate_yn			= trim($cate_yn);
$cate_cd			= trim($cate_cd);
$in_cate_code = trim($in_cate_code);

//echo $m_level;

$result = false;
#====================================================================
# DML Process
#====================================================================
	
#====================================================================
	$savedir1 = $g_physical_path."upload_data/category";
#====================================================================
	
	if ($mode == "I") {
		

		$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		//$file_rnm					= $_FILES[cate_img][name];

		$result = insertCategory($conn, $m_level, $m_seq01, $m_seq02, $m_seq03, $cate_name, $cate_memo, $cate_flag, $in_cate_code, $cate_img, $cate_img_over, $use_tf, $s_adm_no);

	}


	if ($mode == "S") {

		$arr_rs = selectCategory($conn, $cate_no);

		//category_NO, category_NAME, category_URL, category_FLAG, category_CD, category_RIGHT,category_IMG,category_IMG_OVER

		$rs_cate_no				= trim($arr_rs[0]["CATE_NO"]); 
		$rs_cate_name			= trim($arr_rs[0]["CATE_NAME"]); 
		$rs_cate_memo			= trim($arr_rs[0]["CATE_MEMO"]); 
		$rs_cate_flag			= trim($arr_rs[0]["CATE_FLAG"]); 
		$rs_cate_cd				= trim($arr_rs[0]["CATE_CD"]); 
		$rs_cate_code			= trim($arr_rs[0]["CATE_CODE"]); 
		$rs_cate_img			= trim($arr_rs[0]["CATE_IMG"]); 
		$rs_cate_img_over	= trim($arr_rs[0]["CATE_IMG_OVER"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($mode == "U") {
		
		switch ($flag01) {
			case "insert" :
				$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$cate_img			= $old_cate_img;
			break;
			case "delete" :
				$cate_img			= "";
			break;
			case "update" :
				$cate_img					= upload($_FILES[cate_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		switch ($flag02) {
			case "insert" :
				$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$cate_img_over		= $old_cate_img_over;
			break;
			case "delete" :
				$cate_img_over		= "";
			break;
			case "update" :
				$cate_img_over		= upload($_FILES[cate_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		$result = updateCategory($conn, $cate_name, $cate_memo, $cate_flag, $in_cate_code, $cate_img, $cate_img_over, $use_tf, $s_adm_no, $cate_no);

	}

	if ($mode == "D") {
		$result = deleteCategory($conn, $s_adm_no, $cate_no);
	}

	if ($rs_cate_cd <> "") {

		if (strlen($m_level) == 2) {
			$level_str = "��ǥ�з� �޴�";
		} else if (strlen($m_level) == 4) {
			$level_str = "��з� �޴�";
		} else if (strlen($m_level) == 6) {
			$level_str = "�ߺз� �޴�";
		} else if (strlen($m_level) == 8) {
			$level_str = "�Һз� �޴�";
		}

	
	} else {

		if (strlen($m_level) == 0) {
			$level_str = "��ǥ�з� �޴�";
		} else if (strlen($m_level) == 2) {
			$level_str = "��з� �޴�";
		} else if (strlen($m_level) == 4) {
			$level_str = "�ߺз� �޴�";
		} else if (strlen($m_level) == 6) {
			$level_str = "�Һз� �޴�";
		}	
	}


#=================================================================
# Get Result set from stored procedure
#=================================================================
	if ($result) {
?>	
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script language="javascript">
		//alert('���� ó�� �Ǿ����ϴ�.');
		opener.js_search();
		self.close();
</script>
<?
		exit;
	}	
?>
<!-- Top Menu ���� -->
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script language="javascript">
	
	function js_save() {
		
		var cate_no = "<?= $cate_no ?>";
		var frm = document.frm;

		if (frm.cate_name.value == "") {
			alert("ī�װ����� �Է��ϼ���.");
			frm.cate_name.focus();
			return;
		}


		if (isNull(cate_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}
		
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	// Ajax
	function sendKeyword() {

		if (frm.old_cate_code.value != frm.in_cate_code.value)	{

			var keyword = document.frm.in_cate_code.value;

			//alert(keyword);
						
			if (keyword != '') {
				var params = "keyword="+encodeURIComponent(keyword);
			
				//alert(params);
				sendRequest("category_dup_check.php", params, displayResult, 'POST');
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

	function js_fileView(obj,idx) {
	
		var frm = document.frm;
	
		if (idx == 01) {
			if (obj.selectedIndex == 2) {
				document.getElementById("file_change").style.display = "inline";
			} else {
				document.getElementById("file_change").style.display = "none";
			}
		}

		if (idx == 02) {
			if (obj.selectedIndex == 2) {
				document.getElementById("file_change2").style.display = "inline";
			} else {
				document.getElementById("file_change2").style.display = "none";
			}
		}
	}
	

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('�ش� �޴��� ���� �Ͻðڽ��ϱ�?\n\n�ش� �޴��� ���� �޴��� ��� ���� �˴ϴ�.');
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}


</script>
</head>
<body id="popup_code">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cate_no" value="<?=$cate_no?>">
<input type="hidden" name="m_level" value="<?=$m_level?>">
<input type="hidden" name="m_seq01" value="<?=$m_seq01?>">
<input type="hidden" name="m_seq02" value="<?=$m_seq02?>">
<input type="hidden" name="m_seq03" value="<?=$m_seq03?>">

<div id="popupwrap_code">
	<h1>ī�װ� ���</h1>
	<div id="postsch_code">
		<h2>* ��ǰ�������� ����� ī�װ��� ����ϴ� ȭ�� �Դϴ�.</h2>
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
							<th>ī�װ��з�</th>
							<td>
								<?=$level_str?>
							</td>
						</tr>
						<tr>
							<th>ī�װ���</th>
							<td>
								<input type="text" name="cate_name" value="<?= $rs_cate_name ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>ī�װ�����</th>
							<td colspan="3">
								<input type="text" name="cate_memo" value="<?= $rs_cate_memo ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>������ڵ�</th>
							<td colspan="3">
								<input type="text" name="in_cate_code" value="<?= $rs_cate_code ?>" style="width:30%;" class="txt" />
								<input type="hidden" name="old_cate_code" value="<?= $rs_cate_code ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		
		<div class="btn">
				<? if ($cate_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } ?>
				<? }?>
				<? if ($cate_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
					<? } ?>
				<? }?>

		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<input type="hidden" name="cate_flag" value="Y">
<input type="hidden" name="use_tf" value="Y">
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>
