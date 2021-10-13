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
	$menu_right = "CP002"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/company/company.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($gd_cate_01 <> "") {
		$con_cate = $gd_cate_01;
	}
	if ($gd_cate_02 <> "") {
		$con_cate = $gd_cate_02;
	}
	if ($gd_cate_03 <> "") {
		$con_cate = $gd_cate_03;
	}
	if ($gd_cate_04 <> "") {
		$con_cate = $gd_cate_04;
	}

	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if($mode == "" && count($chk_no) == 0) {
?>
<script language="javascript">
		alert('���õ� ��ü�� �����ϴ�. üũ�ڽ��� �����Ͻ� ��ü�� ����Ʈ���� ���� �������ּ���.');
		self.close();
</script>
<?
		exit;
	}

	if ($mode == "U") {

		$row_cnt = count($hid_chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_cp_no = $hid_chk_no[$k];

			//echo $str_cp_no."<br/>";

			
			if($chk1 == 'on') {

				if($con_cate == "")
					continue;

				$result = updateCompanyBatch($conn, "CP_CATE", $con_cate, $s_adm_no, $str_cp_no);

			}
 			
		}

	}


	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type;
		
		if ($mode == "U") {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		self.close();
		opener.location.href = "company_list.php?<?=$strParam?>";
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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;

		// ��ũ��Ʈ ���� �κ�

		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="depth" value=""/>
<?
	if($chk_no != null)
	{
		$postvalue = "";
		foreach ($chk_no as $cp_no) {
			$postvalue .= '<input type="hidden" name="hid_chk_no[]" value="' .$cp_no. '" />';
		}
		echo $postvalue;

	}
?>
<div id="popupwrap_file">
	<h1>��ü �ϰ� ����</h1>
	<div id="postsch">
		<h2>* ��ü ������ �ϰ� ���� �մϴ�.<br>
			- ���� �׸��� üũ �� Ȯ���� Ŭ�� �Ͻø� �ش� ������ �ϰ� ���� �˴ϴ�.
		</h2>
		<div class="addr_inp">
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="*" />
				</colgroup>
				<tr>
					<th><input type="checkbox" name="chk1"></th>
					<th>ī�װ�</th>
					<td class="line input_field">
						<?= makeCategorySelectBoxOnChange($conn, $con_cate, "");?>
						<br/><br/>
						<b>���� ī�װ� :</b> <label><input type="radio" name="cate_option" value="M" checked="checked"/> �̵�</label><br/>
					</td>
				</tr>
			</table>
			<script>
				$(function(){
					$(".input_field").click(function(){
						$(this).closest("tr").find("th").eq(0).find("input").prop("checked", "checked");
					});
				});
			</script>
				
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
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>