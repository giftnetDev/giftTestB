<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : company_write_txt.php
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
	$menu_right = "CP004"; // �޴����� ���� �� �־�� �մϴ�

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
	

#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
	
		
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_company";
	#====================================================================
	$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('txt','TXT'));
	
	$file_dir = "../../upload_data/temp_company/".$file_nm;

	$fo = fopen($file_dir, "r");
		
	$number_id = 0;

	while($str = fgets($fo, 3000)){

		$number_id++;

		$a_str = explode("	",$str);
		
		$cp_type				= trim($a_str[1]);
		$cp_code				= trim($a_str[2]);
		$cp_nm					= trim($a_str[3]);
		$cp_nm2					= trim($a_str[4]);
		$biz_no					= trim($a_str[5]);
		$ceo_nm					= trim($a_str[6]);
		$uptea					= trim($a_str[7]);
		$upjong					= trim($a_str[8]);
		$cp_phone				= trim($a_str[9]);
		$manager_nm			= trim($a_str[10]);
		$cp_fax					= trim($a_str[11]);
		$cp_addr				= trim($a_str[12]);
		$re_addr				= trim($a_str[13]);
		$email					= trim($a_str[14]);
		$account_bank		= trim($a_str[15]);
		$account				= trim($a_str[16]);
		$account_owner_nm      = trim($a_str[17]);
		$sale_admin_nm         = trim($a_str[18]);
		$memo					= trim($a_str[19]);

		$cp_type = str_replace(" ","",$cp_type);
		
		if($cp_type == '1.�Ǹ�ó')
			$cp_type = '�Ǹ�';
		else if($cp_type == '2.����ó')
			$cp_type = '����';

		$cp_hphone			    = "";
		$cp_zip			        = "";
		$re_zip			        = "";
		$homepage		        = "";
		$phone					= "";
		$hphone					= "";
		$fphone					= "";
		$contract_start	        = "";
		$contract_end		    = "";
		$ad_type				= "";
		$email_tf				= "Y";
		$use_tf					= "Y";

		$cp_type				= SetStringToDB($cp_type);
		$cp_code				= SetStringToDB($cp_code);
		$cp_nm					= SetStringToDB($cp_nm);
		$cp_nm2					= SetStringToDB($cp_nm2);
		$ceo_nm					= SetStringToDB($ceo_nm);
		$uptea					= SetStringToDB($uptea);
		$upjong					= SetStringToDB($upjong);
		$manager_nm				= SetStringToDB($manager_nm);
		$cp_addr				= SetStringToDB($cp_addr);
		$re_addr				= SetStringToDB($re_addr);
		$email					= SetStringToDB($email);
		$account_bank			= SetStringToDB($account_bank);
		$account				= SetStringToDB($account);
		$account_owner_nm		= SetStringToDB($account_owner_nm);
		$sale_admin_nm			= SetStringToDB($sale_admin_nm);

		$cp_hphone				= SetStringToDB($cp_hphone);
		$homepage				= SetStringToDB($homepage);
		$phone					= SetStringToDB($phone);
		$hphone					= SetStringToDB($hphone);
		$fphone					= SetStringToDB($fphone);
		$ad_type				= SetStringToDB($ad_type);
		$memo				= SetStringToDB($memo);
		
		$utime = strtotime($contract_start); 
		$utime = $utime - (60*60*24);
		$contract_start = date('Y-m-d',$utime); 

		if($contract_start == "1969-12-31") {
			$contract_start = "";
		}

		$utime = strtotime($contract_end); 
		$utime = $utime - (60*60*24);
		$contract_end = date('Y-m-d',$utime); 

		if($contract_end == "1969-12-31") {
			$contract_end = "";
		}

		$biz_no = str_replace("-","",$biz_no);
								
		if (strlen($biz_no) == "10") {
			$BIZ_NO_01 = left($biz_no,3);
			$BIZ_NO_02 = substr($biz_no,3,2);
			$BIZ_NO_03 = right($biz_no,5);

			$biz_no = $BIZ_NO_01."-".$BIZ_NO_02."-".$BIZ_NO_03;
		}

		$cp_phone				= str_replace(" ","", $cp_phone);
		$cp_fax					= str_replace(" ","", $cp_fax);
		$cp_zip					= str_replace(" ","", $cp_zip);
		$re_zip					= str_replace(" ","", $re_zip);
		$account				= str_replace(" ","", $account);
		$phone					= str_replace(" ","", $phone);
		$hphone					= str_replace(" ","", $hphone);
		$fphone					= str_replace(" ","", $fphone);

		$temp_result = insertTempCompany($conn, $file_nm, $cp_type, $cp_code, $cp_nm, $cp_nm2, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $use_tf, $sale_admin_nm, $s_adm_no);

	}
		
		/*
		$temp_file = $savedir1."/".$file_nm;						
		$exist = file_exists($temp_file);

		if($exist){
			$delrst=unlink($temp_file);
			if(!$delrst) {
				echo "��������";
			}
		}
		*/
?>	
<script language="javascript">
	location.href =  'company_write_file_txt.php?mode=L&temp_no=<?=$file_nm?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_cp_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_cp_no .= "'".$ok[$k]."',";
		}

		$str_cp_no = substr($str_cp_no, 0, (strlen($str_cp_no) -1));
		//echo $str_cp_no;
		$insert_result = insertTempToRealCompany($conn, $str_cp_no);

		if ($insert_result) {
			$delete_result = deleteTempToRealCompany($conn, $str_cp_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_cp_no = $chk[$k];

			$temp_result = deleteTempCompany($conn, $temp_no, $tmp_cp_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$arr_rs = listTempCompany($conn, $temp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  'company_list.php';
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
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<style type="text/css">
<!--
/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}
-->
</style>

<script language="javascript">
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "company_list.php";
		frm.submit();
	}

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;
		
		if (isNull(frm.file_nm.value)) {
			alert('������ ������ �ּ���.');
			frm.file_nm.focus();
			return ;		
		}
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";
		} else {
			frm.mode.value = "I";
		}

		frm.method = "post";
		frm.action = "company_write_file_txt.php";
		frm.submit();
	}

	//�����ȣ ã��
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '�����ȣã��', '390', '370', 'NO');
	}

	/**
	* ���� ÷�ο� ���� ���ÿ� ���� ����÷�� �Է¶� visibility ����
	*/
	function js_fileView(obj,idx) {
		
		var frm = document.frm;
		
		if (idx == 01) {
			if (obj.selectedIndex == 2) {
				frm.contracts_nm.style.visibility = "visible";
			} else {
				frm.contracts_nm.style.visibility = "hidden";
			}
		}

	}

	function LimitAttach(obj) {
		var file = obj.value;
		extArray = new Array(".jsp", ".cgi", ".php", ".asp", ".aspx", ".exe", ".com", ".php3", ".inc", ".pl", ".asa", ".bak");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (!allowSubmit){
			//
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return;
		}
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".txt");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			//
		}else{
			alert("�Է��Ͻ� ������ ���ε� �� �� �����ϴ�!");
			return;
		}
	}

	function js_view(rn, file_nm, cp_no) {
		
		var url = "company_modify.php?mode=S&temp_no="+file_nm+"&cp_no="+cp_no;
		NewWindow(url, '��ü�뷮�Է�', '860', '513', 'YES');
		
	}

	function js_reload() {
		location.href =  'company_write_file_txt.php?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_delete() {

		var frm = document.frm;
		var chk_cnt = 0;

		check=document.getElementsByName("chk[]");
		
		for (i=0;i<check.length;i++) {
			if(check.item(i).checked==true) {
				chk_cnt++;
			}
		}
		
		if (chk_cnt == 0) {
			alert("���� �Ͻ� �ڷᰡ �����ϴ�.");
		} else {

			bDelOK = confirm('�����Ͻ� �ڷḦ ���� �Ͻðڽ��ϱ�?');
			
			if (bDelOK==true) {
				frm.mode.value = "D";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			}
		}
	}

	function js_register() {
		var frm = document.frm;
		bDelOK = confirm('���� ����Ÿ�� ��� ��� �Ͻðڽ��ϱ�?');

		if (bDelOK==true) {
			frm.mode.value = "I";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
		
	}


</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="cp_no" value="">

<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>��ü ���</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>
								�Է� ��
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_company.xls&filename_rnm=insert_example.xls&str_path=manager/company/">�ޱ�</a>
							</th>
							<td colspan="3">
								<div id="ex_scroll">
								<img src="company_example.jpg">
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>����</th>
							<td colspan="3"><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
						</tr>
					</tbody>
				</table>

				<div class="btnright">
				<? if ($file_nm <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
					<? } ?>
				<? }?>
				</div>

				<div class="sp20"></div>
				<div>
					* �� <?=sizeof($arr_rs)?> �� &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* ��ϰ� <?=$row_cnt?> ��
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:3555px">
					<colgroup>
						<col width="35">
						<col width="200">
						<col width="180">
						<col width="90">
						<col width="90">
						<col width="100">
						<col width="80">
						<col width="100">
						<col width="100">
						<col width="80">
						<col width="350">
						<col width="80">
						<col width="350">
						<col width="150"><!-- ���� -->
						<col width="150"><!-- ���� -->
						<col width="110"><!-- ���� -->
						<col width="150"><!-- ���¹�ȣ -->
						<col width="100"><!-- ����� -->
						<col width="100"><!-- ����� -->
						<col width="160"><!-- Ȩ������ -->
						<col width="200"><!-- �޸� -->
						<col width="100"><!-- ����� -->
						<col width="100"><!-- ��ȭ��ȣ -->
						<col width="100"><!-- �޴��� -->
						<col width="100"><!-- FAX -->
						<col width="200"><!-- �̸��� -->
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>���</th>
							<th>��ü��</th>
							<th>��ü����</th>
							<th>���籸��</th>
							<th>����ڵ�Ϲ�ȣ</th>
							<th>��ǥ�ڸ�</th>
							<th>��ǥ��ȭ��ȣ</th>
							<th>��ǥFAX</th>
							<th>�����ȣ</th>
							<th>�ּ�</th>
							<th>��ǰ�����ȣ</th>
							<th>��ǰ�ּ�</th>
							<th>����</th>
							<th>����</th>
							<th>�ŷ�����</th>
							<th>���¹�ȣ</th>
							<th>���Ⱓ������</th>
							<th>���Ⱓ������</th>
							<th>Ȩ������</th>
							<th>��ü�޸�</th>
							<th>����ڸ�</th>
							<th>��ȭ��ȣ</th>
							<th>�޴���ȭ��ȣ</th>
							<th>FAX��ȣ</th>
							<th class="end">�̸���</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								//rn, CP_NO, CP_NM, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, 
								//BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, MANAGER_NM, PHONE, HPHONE, FPHONE, CONTRACT_START, CONTRACT_END, 
								//USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								
								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]);
								$CEO_NM					= SetStringFromDB($arr_rs[$j]["CEO_NM"]);
								$CP_PHONE				= SetStringFromDB($arr_rs[$j]["CP_PHONE"]);
								$CP_FAX					= SetStringFromDB($arr_rs[$j]["CP_FAX"]);
								$CP_TYPE				= SetStringFromDB($arr_rs[$j]["CP_TYPE"]);
								$AD_TYPE				= SetStringFromDB($arr_rs[$j]["AD_TYPE"]);
								$BIZ_NO					= SetStringFromDB($arr_rs[$j]["BIZ_NO"]);
								$CP_ZIP					= trim($arr_rs[$j]["CP_ZIP"]);
								$CP_ADDR				= SetStringFromDB($arr_rs[$j]["CP_ADDR"]);
								$RE_ZIP					= trim($arr_rs[$j]["RE_ZIP"]);
								$RE_ADDR				= SetStringFromDB($arr_rs[$j]["RE_ADDR"]);
								$UPJONG					= SetStringFromDB($arr_rs[$j]["UPJONG"]);
								$UPTEA					= SetStringFromDB($arr_rs[$j]["UPTEA"]);
								$ACCOUNT_BANK		= SetStringFromDB($arr_rs[$j]["ACCOUNT_BANK"]);
								$ACCOUNT				= trim($arr_rs[$j]["ACCOUNT"]);
								$MANAGER_NM			= SetStringFromDB($arr_rs[$j]["MANAGER_NM"]);
								$PHONE					= SetStringFromDB($arr_rs[$j]["PHONE"]);
								$HPHONE					= SetStringFromDB($arr_rs[$j]["HPHONE"]);
								$FPHONE					= SetStringFromDB($arr_rs[$j]["FPHONE"]);
								$MEMO						= trim($arr_rs[$j]["MEMO"]);
								$EMAIL					= SetStringFromDB($arr_rs[$j]["EMAIL"]);
								$CONTRACT_START	= trim($arr_rs[$j]["CONTRACT_START"]);
								$CONTRACT_END		= trim($arr_rs[$j]["CONTRACT_END"]);
								$HOMEPAGE				= SetStringFromDB($arr_rs[$j]["HOMEPAGE"]);
								$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
								
								//$CONTRACT_START = date("Y-m-d",strtotime($CONTRACT_START));
								//$CONTRACT_END		= date("Y-m-d",strtotime($CONTRACT_END));
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// ������ ��ȿ�� �˻�
								$err_str = "����";
								
								if ($CP_NM == "") {
									$err_str =  "��ü�� ����,";
								} else {
									if (!chkCpNm($conn, $CP_NM)) {
										$err_str .=  "��ü�� �ߺ�,";
									}
								}
								
								if ($CP_TYPE == "") {
									$err_str .=  "��ü���� ����,";
								} else {
									if (getDcodeName($conn, "CP_TYPE", $CP_TYPE) == "") {
										$err_str .=  "��ü���� ����,";
									}
								}
								
								if ($AD_TYPE == "") {
									$err_str .=  "���籸�� ����,";
								} else {
									if (getDcodeName($conn, "AD_TYPE", $AD_TYPE) == "") {
										$err_str .=  "���籸�� ����,";
									}
								}

								if ($BIZ_NO == "") {
									$err_str .=  "����ڵ�Ϲ�ȣ ����,";
								} else {

									$BIZ_NO = str_replace("-","",$BIZ_NO);
									
									if (strlen($BIZ_NO) <> "10") {
										$err_str .=  "����ڵ�Ϲ�ȣ ����,";
									}

									$BIZ_NO_01 = left($BIZ_NO,3);
									$BIZ_NO_02 = substr($BIZ_NO,3,2);
									$BIZ_NO_03 = right($BIZ_NO,5);

									$BIZ_NO = $BIZ_NO_01."-".$BIZ_NO_02."-".$BIZ_NO_03;

									if (strlen($BIZ_NO) <> "12") {
										$err_str .=  "����ڵ�Ϲ�ȣ ����,";
									}
								}

								if ($CEO_NM == "") {
									$err_str .=  "��ǥ�ڸ� ����,";
								}

								if ($CP_PHONE == "") {
									$err_str .=  "��ǥ��ȭ��ȣ ����,";
								}
								
								if ($CP_ZIP <> "") {
									if (!chkZip($conn, $CP_ZIP)) {
										$err_str .=  "�����ȣ ����,";
									}
								}

								if ($RE_ZIP <> "") {
									if (!chkZip($conn, $RE_ZIP)) {
										$err_str .=  "��ǰ�����ȣ ����,";
									}
								}

								if ($ACCOUNT_BANK == "") {
									$err_str .=  "�ŷ����� ����,";
								}

								if ($ACCOUNT == "") {
									$err_str .=  "���¹�ȣ ����,";
								}

								if ($CONTRACT_START <> "") { 
									if (!chkDate($CONTRACT_START, "YYYY-MM-DD")) {
										$err_str .=  "�������ϳ�¥ ���� ���� ,";
									}
								} 

								if ($CONTRACT_END <> "") { 
									if (!chkDate($CONTRACT_END, "YYYY-MM-DD")) {
										$err_str .=  "��������ϳ�¥ ���� ���� ,";
									}
								} 

								if ($err_str <> "����") {
									$err_str = str_replace("����","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}
					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$CP_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $CP_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "����") {?>
								<input type="hidden" name="ok[]" value="<?=$CP_NO?>">
								<? } ?>
							</td>
							<td class="modeual_nm"><?= $CP_NM ?></a></td>
							<td><?= getDcodeName($conn, "CP_TYPE", $CP_TYPE);?></td>
							<td><?= getDcodeName($conn, "AD_TYPE", $AD_TYPE);?></td>
							<td><?= $BIZ_NO ?></td>
							<td><?= $CEO_NM ?></td>
							<td><?= $CP_PHONE ?></td>
							<td><?= $CP_FAX ?></td>
							<td><?= $CP_ZIP ?></td>
							<td class="modeual_nm"><?= $CP_ADDR ?></td>
							<td><?= $RE_ZIP ?></td>
							<td class="modeual_nm"><?= $RE_ADDR ?></td>
							<td><?= $UPJONG ?></td>
							<td><?= $UPTEA ?></td>
							<td><?= $ACCOUNT_BANK ?></td>
							<td><?= $ACCOUNT ?></td>
							<td><?= $CONTRACT_START?></td>
							<td><?= $CONTRACT_END?></td>
							<td><?= $HOMEPAGE?></td>
							<td><?= $MEMO?></td>
							<td><?= $MANAGER_NM?></td>
							<td><?= $PHONE?></td>
							<td><?= $HPHONE?></td>
							<td><?= $FPHONE?></td>
							<td class="filedown"><?= $EMAIL ?></td>
						</tr>
					<?			
										$err_str = "";
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="25">�����Ͱ� �����ϴ�. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>
						</div>


				<div class="btnright">
					<a href="javascript:js_register();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
					<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
				</div>

			</div>
			<!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>




</div>
<script type="text/javascript" src="../js/wrest.js"></script>
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