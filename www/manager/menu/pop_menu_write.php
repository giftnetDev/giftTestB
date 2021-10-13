<?session_start();?>
<?
# =============================================================================
# File Name    : menu_list.php
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
	$menu_right = "AD004"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/menu/menu.php";

#====================================================================
# Request Parameter
#====================================================================

$m_level = Trim($m_level);
$m_seq01 = Trim($m_seq01);
$m_seq02 = Trim($m_seq02);

#====================================================================
# Declare variables
#====================================================================

#====================================================================
# Request Parameter
#====================================================================

$menu_no		= trim($menu_no);
$m_level		= trim($m_level);
$m_seq01		= trim($m_seq01);
$m_seq02		= trim($m_seq02);
$menu_name	= trim($menu_name);
$menu_url		= trim($menu_url);
$menu_yn		= trim($menu_yn);
$menu_cd		= trim($menu_cd);
$in_menu_right = trim($in_menu_right);

//echo $m_level;

$result = false;
#====================================================================
# DML Process
#====================================================================
	
#====================================================================
	$savedir1 = $g_physical_path."upload_data/menu";
#====================================================================
	
	if ($mode == "I") {
		

		$menu_img					= upload($_FILES[menu_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		$menu_img_over		= upload($_FILES[menu_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
		//$file_rnm					= $_FILES[menu_img][name];

		$result = insertAdminMenu($conn, $m_level, $m_seq01, $m_seq02, $menu_name, $menu_url, $menu_flag, $in_menu_right, $menu_img, $menu_img_over, $use_tf, $s_adm_no);

	}


	if ($mode == "S") {

		$arr_rs = selectAdminMenu($conn, $menu_no);

		//MENU_NO, MENU_NAME, MENU_URL, MENU_FLAG, MENU_CD, MENU_RIGHT,MENU_IMG,MENU_IMG_OVER

		$rs_menu_no				= trim($arr_rs[0]["MENU_NO"]); 
		$rs_menu_name			= trim($arr_rs[0]["MENU_NAME"]); 
		$rs_menu_url			= trim($arr_rs[0]["MENU_URL"]); 
		$rs_menu_flag			= trim($arr_rs[0]["MENU_FLAG"]); 
		$rs_menu_cd				= trim($arr_rs[0]["MENU_CD"]); 
		$rs_menu_right			= trim($arr_rs[0]["MENU_RIGHT"]); 
		$rs_menu_img			= trim($arr_rs[0]["MENU_IMG"]); 
		$rs_menu_img_over		= trim($arr_rs[0]["MENU_IMG_OVER"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($mode == "U") {
		
		switch ($flag01) {
			case "insert" :
				$menu_img					= upload($_FILES[menu_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$menu_img			= $old_menu_img;
			break;
			case "delete" :
				$menu_img			= "";
			break;
			case "update" :
				$menu_img					= upload($_FILES[menu_img], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		switch ($flag02) {
			case "insert" :
				$menu_img_over		= upload($_FILES[menu_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
			case "keep" :
				$menu_img_over		= $old_menu_img_over;
			break;
			case "delete" :
				$menu_img_over		= "";
			break;
			case "update" :
				$menu_img_over		= upload($_FILES[menu_img_over], $savedir1, 100 , array('gif', 'jpeg', 'jpg','png'));
			break;
		}

		$result = updateAdminMenu($conn, $menu_name, $menu_url, $menu_flag, $in_menu_right, $menu_img, $menu_img_over, $use_tf, $s_adm_no, $menu_no);

	}

	if ($mode == "D") {
		$result = deleteAdminMenu($conn, $s_adm_no, $menu_no);
	}

	if ($rs_menu_cd <> "") {

		if (strlen($m_level) == 2) {
			$level_str = "��з� �޴�";
		} else if (strlen($m_level) == 4) {
			$level_str = "�ߺз� �޴�";
		} else if (strlen($m_level) == 6) {
			$level_str = "�Һз� �޴�";
		}

	
	} else {

		if (strlen($m_level) == 0) {
			$level_str = "��з� �޴�";
		} else if (strlen($m_level) == 2) {
			$level_str = "�ߺз� �޴�";
		} else if (strlen($m_level) == 4) {
			$level_str = "�Һз� �޴�";
		}	
	}


#=================================================================
# Get Result set from stored procedure
#=================================================================
	if ($result) {

		if($mode == "I" && $result === "2") { 
?>	
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script language="javascript">
		alert('�ߺ��� �����ڵ尡 �ֽ��ϴ�. �ٸ� �ڵ带 ������ּ���.');
		opener.document.location = "menu_list.php"
		self.close();
</script>
<?
		} else {
?>	
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		opener.document.location = "menu_list.php"
		self.close();
</script>
<?
		}

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
		
		var menu_no = "<?= $menu_no ?>";
		var frm = document.frm;

		if (frm.menu_name.value == "") {
			alert("�޴����� �Է��ϼ���.");
			frm.menu_name.focus();
			return;
		}

		if (frm.menu_url.value == "") {
			alert("�޴���θ� �Է��ϼ���.");
			frm.menu_url.focus();
			return;
		}

		if (frm.in_menu_right.value == "") {
			alert("�����ڵ带 �Է��ϼ���.");
			frm.in_menu_right.focus();
			return;
		}

		if (isNull(menu_no)) {
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

		if (frm.old_menu_right.value != frm.in_menu_right.value)	{

			var keyword = document.frm.in_menu_right.value;

			//alert(keyword);
						
			if (keyword != '') {
				var params = "keyword="+encodeURIComponent(keyword);
			
				//alert(params);
				sendRequest("menu_dup_check.php", params, displayResult, 'POST');
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
					alert("������� ���� �ڵ� �Դϴ�.");
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
<input type="hidden" name="menu_no" value="<?=$menu_no?>">
<input type="hidden" name="m_level" value="<?=$m_level?>">
<input type="hidden" name="m_seq01" value="<?=$m_seq01?>">
<input type="hidden" name="m_seq02" value="<?=$m_seq02?>">

<div id="popupwrap_code">
	<h1>������ �޴� ���</h1>
	<div id="postsch_code">
		<h2>* �ý��ۿ��� ����� �޴��� ����ϴ� ȭ�� �Դϴ�.</h2>
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
							<th>�޴��з�</th>
							<td>
								<?=$level_str?>
							</td>
						</tr>
						<tr>
							<th>�޴���</th>
							<td>
								<input type="text" name="menu_name" value="<?= $rs_menu_name ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>�޴����</th>
							<td colspan="3">
								<input type="text" name="menu_url" value="<?= $rs_menu_url ?>" style="width:90%;" class="txt" />
							</td>
						</tr>
						<tr>
							<th>�����ڵ�</th>
							<td colspan="3">
								<input type="text" name="in_menu_right" value="<?= $rs_menu_right ?>" style="width:30%;" class="txt" />
								<input type="hidden" name="old_menu_right" value="<?= $rs_menu_right ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		
		<div class="btn">
				<? if ($menu_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
			<a href="javascript:sendKeyword();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<? } ?>
				<? }?>
				<? if ($menu_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
					<? } ?>
				<? }?>

		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
<input type="hidden" name="menu_flag" value="Y">
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
