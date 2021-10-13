<?session_start();?>
<?
# =============================================================================
# File Name    : company_modify.php
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
	$menu_right = "CP003"; // �޴����� ���� �� �־�� �մϴ�

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
	
	$temp_no	= trim($temp_no);
	$cp_no		= trim($cp_no);

	
	//echo $pb_nm; 
	//echo $$mode;
	
	$cp_type				= SetStringToDB($cp_type);
	$cp_nm					= SetStringToDB($cp_nm);
	$cp_nm2					= SetStringToDB($cp_nm2);
	$cp_code				= SetStringToDB($cp_code);
	$cp_phone				= SetStringToDB($cp_phone);
	$cp_hphone			    = SetStringToDB($cp_hphone);
	$cp_fax					= SetStringToDB($cp_fax);
	$cp_addr				= SetStringToDB($cp_addr);
	$re_addr				= SetStringToDB($re_addr);
	$homepage				= SetStringToDB($homepage);
	$biz_no					= SetStringToDB($biz_no);
	$ceo_nm					= SetStringToDB($ceo_nm);
	$upjong					= SetStringToDB($upjong);
	$uptea					= SetStringToDB($uptea);
	$manager_nm			    = SetStringToDB($manager_nm);
	$phone					= SetStringToDB($phone);
	$hphone					= SetStringToDB($hphone);
	$fphone					= SetStringToDB($fphone);
	$email					= SetStringToDB($email);
	$ad_type				= SetStringToDB($ad_type);
	$account_bank		    = SetStringToDB($account_bank);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectTempCompany($conn, $temp_no, $cp_no);

		$rs_cp_no							= trim($arr_rs[0]["CP_NO"]); 
		$rs_cp_nm							= SetStringFromDB($arr_rs[0]["CP_NM"]); 
		$rs_cp_nm2							= SetStringFromDB($arr_rs[0]["CP_NM2"]); 
		$rs_cp_code							= SetStringFromDB($arr_rs[0]["CP_CODE"]); 
		$rs_cp_type							= SetStringFromDB($arr_rs[0]["CP_TYPE"]); 
		$rs_ad_type							= SetStringFromDB($arr_rs[0]["AD_TYPE"]); 
		$rs_cp_phone						= SetStringFromDB($arr_rs[0]["CP_PHONE"]); 
		$rs_cp_hphone						= SetStringFromDB($arr_rs[0]["CP_HPHONE"]); 
		$rs_cp_fax							= SetStringFromDB($arr_rs[0]["CP_FAX"]); 
		$rs_cp_zip							= trim($arr_rs[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs[0]["CP_ADDR"]); 
		$rs_re_zip							= trim($arr_rs[0]["RE_ZIP"]); 
		$rs_re_addr							= SetStringFromDB($arr_rs[0]["RE_ADDR"]); 
		$rs_biz_no							= trim($arr_rs[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs[0]["CEO_NM"]); 
		$rs_upjong							= SetStringFromDB($arr_rs[0]["UPJONG"]); 
		$rs_uptea							= SetStringFromDB($arr_rs[0]["UPTEA"]); 
		$rs_account_bank				    = SetStringFromDB($arr_rs[0]["ACCOUNT_BANK"]); 
		$rs_account							= trim($arr_rs[0]["ACCOUNT"]); 
		$rs_account_owner_nm		        = trim($arr_rs[0]["ACCOUNT_OWNER_NM"]); 
		$rs_homepage						= SetStringFromDB($arr_rs[0]["HOMEPAGE"]); 
		$rs_memo							= trim($arr_rs[0]["MEMO"]); 
		$rs_dc_rate							= trim($arr_rs[0]["DC_RATE"]); 
		$rs_sale_adm_no                     = trim($arr_rs[0]["SALE_ADM_NO"]);
		$rs_manager_nm					    = SetStringFromDB($arr_rs[0]["MANAGER_NM"]); 
		$rs_phone							= SetStringFromDB($arr_rs[0]["PHONE"]); 
		$rs_hphone							= SetStringFromDB($arr_rs[0]["HPHONE"]); 
		$rs_fphone							= SetStringFromDB($arr_rs[0]["FPHONE"]); 
		$rs_email							= SetStringFromDB($arr_rs[0]["EMAIL"]); 
		$rs_email_tf						= trim($arr_rs[0]["EMAIL_TF"]); 
		$rs_contract_start			        = trim($arr_rs[0]["CONTRACT_START"]); 
		$rs_contract_end				    = trim($arr_rs[0]["CONTRACT_END"]); 
		$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf							= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_adm							= trim($arr_rs[0]["REG_ADM"]); 
		$rs_reg_date						= trim($arr_rs[0]["REG_DATE"]); 
		$rs_up_adm							= trim($arr_rs[0]["UP_ADM"]); 
		$rs_up_date							= trim($arr_rs[0]["UP_DATE"]); 
		$rs_del_adm							= trim($arr_rs[0]["DEL_ADM"]); 
		$rs_del_date						= trim($arr_rs[0]["DEL_DATE"]); 

		if ($rs_contract_start <> "0000-00-00 00:00:00") {
			$rs_contract_start = date("Y-m-d",strtotime($rs_contract_start));
		} else {
			$rs_contract_start = "";
		}


		if ($rs_contract_end <> "0000-00-00 00:00:00") {
			$rs_contract_end = date("Y-m-d",strtotime($rs_contract_end));
		} else {
			$rs_contract_end = "";
		}

	}

	if ($mode == "U") {
		
		$result = updateTempCompany($conn, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $use_tf, $s_adm_no, $temp_no, $cp_no);
	}

	if ($mode == "D") {
		$result = deleteCompany($conn,$cp_no);
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
		location.href =  "company_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script>

    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.
                // �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
                // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
                var fullAddr = ''; // ���� �ּ� ����
                var extraAddr = ''; // ������ �ּ� ����

                // ����ڰ� ������ �ּ� Ÿ�Կ� ���� �ش� �ּ� ���� �����´�.
                if (data.userSelectedType === 'R') { // ����ڰ� ���θ� �ּҸ� �������� ���
                    fullAddr = data.roadAddress;

                } else { // ����ڰ� ���� �ּҸ� �������� ���(J)
                    fullAddr = data.jibunAddress;
                }

                // ����ڰ� ������ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
                if(data.userSelectedType === 'R'){
                    //���������� ���� ��� �߰��Ѵ�.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // �ǹ����� ���� ��� �߰��Ѵ�.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
								if (document.getElementById("addr_type").value == "s") {
								  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
									document.getElementById("cp_zip").value = data.postcode1+"-"+data.postcode2;
									//document.getElementById("cp_zip").value = data.postcode2;
									document.getElementById("cp_addr").value = fullAddr;
									// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
									document.getElementById("cp_addr").focus();
								} else {
								  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
									document.getElementById("re_zip").value = data.postcode1+"-"+data.postcode2;
									//document.getElementById("re_zip").value = data.postcode2;
									document.getElementById("re_addr").value = fullAddr;
									// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
									document.getElementById("re_addr").focus();
								}


            }
        }).open();
    }

		function js_addr_open(s) {
			document.getElementById("addr_type").value = s;
			sample6_execDaumPostcode();
		}

</script>  


<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>
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
		
		var cp_no = "<?= $cp_no ?>";
		var frm = document.frm;
		
		if (isNull(frm.cp_nm.value)) {
			alert('��ü���� �Է����ּ���.');
			frm.cp_nm.focus();
			return ;		
		}

		if (isNull(frm.cp_code.value)) {
			alert('�����ڵ带 �Է����ּ���.');
			frm.cp_code.focus();
			return ;		
		}
		
		
		if (frm.cp_type.value == "") {
			alert('��ü������ �������ּ���.');
			frm.cp_type.focus();
			return ;		
		}
		
		/*
		if (isNull(frm.biz_no.value)) {
			alert('����� ��Ϲ�ȣ�� �Է����ּ���.');
			frm.biz_no.focus();
			return ;		
		}

		if (isNull(frm.ceo_nm.value)) {
			alert('��ǥ�ڸ��� �Է����ּ���.');
			frm.ceo_nm.focus();
			return ;		
		}

		if (isNull(frm.cp_phone.value)) {
			alert('��ǥ ��ȭ��ȣ�� �Է����ּ���.');
			frm.cp_phone.focus();
			return ;		
		}

		if (isNull(frm.account_bank.value)) {
			alert('�ŷ������� �Է����ּ���.');
			frm.account_bank.focus();
			return ;		
		}

		if (isNull(frm.account.value)) {
			alert('���¹�ȣ�� �Է����ּ���.');
			frm.account.focus();
			return ;		
		}
		*/

		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}

		if (frm.rd_email_tf[0].checked == true) {
			frm.email_tf.value = "Y";
		} else {
			frm.email_tf.value = "N";
		}

		frm.mode.value = "U";

		frm.method = "post";
		frm.action = "company_modify.php";
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


</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?= $temp_no?>">
<input type="hidden" name="cp_no" value="<?= $cp_no?>">
<input type="hidden" name="con_cp_type" value="<?= $con_cp_type?>">
<input type="hidden" name="date_start" value="<?= $date_start ?>">
<input type="hidden" name="date_end" value="<?= $date_end ?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<div id="popupwrap_file">
	<h1>��ü ��� ����</h1>
	<div id="postsch">
		<h2>* ��ü ������ ���� �մϴ�.</h2>
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
							<th>��ü��</th>
							<td>
								<input type="text" name="cp_nm" value="<?= $rs_cp_nm ?>" style="width:60%;" itemname="��ü��" required class="txt">
								<input type="text" name="cp_nm2" value="<?= $rs_cp_nm2 ?>" style="width:30%;" itemname="������" class="txt">
							</td>
							<th>�����ڵ�</th>
							<td>
								<input type="text" name="cp_code" value="<?= $rs_cp_code ?>" style="width:20%;" itemname="�����ڵ�" required class="txt">
							</td>
					</tr>
				</thead>
				<tbody>
						<th>��ü����</th>
						<td>
							<?= makeSelectBox($conn,"CP_TYPE","cp_type","125","����","",$rs_cp_type)?>
						</td>
						<th>���籸��</th>
						<td>
							<?= makeSelectBox($conn,"AD_TYPE","ad_type","125","����","",$rs_ad_type)?>
						</td>
					</tr>
					<tr>
						<th>�����	��Ϲ�ȣ</th>
						<td><input type="Text" name="biz_no" value="<?= $rs_biz_no?>" style="width:140px;" itemname="�����	��Ϲ�ȣ" class="txt"></td>
						<th>��ǥ�ڸ�</th>
						<td><input type="Text" name="ceo_nm" value="<?= $rs_ceo_nm ?>" style="width:30%;" itemname="��ǥ�ڸ�" class="txt"></td>
					</tr>
					<tr>
						<th>��ǥ ��ȭ��ȣ</th>
						<td>
							<input type="Text" name="cp_phone" value="<?= $rs_cp_phone?>" style="width:120px;" itemname="��ǥ ��ȭ��ȣ" class="txt" onkeyup="return isPhoneNumber(this)">
						</td>
						<th>��ǥ FAX</th>
						<td>
							<input type="Text" name="cp_fax" value="<?= $rs_cp_fax?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
						</td>
					</tr>
					<tr>
						<th>�ּ� 1</th>
						<td colspan="3">
							<input type="Text" name="cp_zip" id="cp_zip" value="<?= $rs_cp_zip?>" style="width:60px;" maxlength="7" class="txt">
							<input type="Text" name="cp_addr" id="cp_addr" value="<?= $rs_cp_addr?>" style="width:65%;" class="txt">
							<a href="#none" onClick="js_addr_open('s');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
						</td>
					<tr>
					<tr>
						<th>�ּ� 2</th>
						<td colspan="3">
							<input type="Text" name="re_zip" id="re_zip" value="<?= $rs_re_zip?>" style="width:60px;" maxlength="7" class="txt">
							<input type="Text" name="re_addr" id="re_addr" value="<?= $rs_re_addr?>" style="width:65%;" class="txt">
							<a href="#none" onClick="js_addr_open('r');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
						</td>
					<tr>
					<tr>
						<th>����</th>
						<td><input type="Text" name="upjong" value="<?= $rs_upjong?>" style="width:40%;" class="txt"></td>
						<th>����</th>
						<td><input type="Text" name="uptea" value="<?= $rs_uptea?>" style="width:40%;" class="txt"></td>
					</tr>
					<tr>
						<th>����� ��</th>
						<td><input type="Text" name="manager_nm" value="<?= $rs_manager_nm ?>" style="width:90%;" class="txt"></td>
						<th>��ȭ��ȣ</th>
						<td>
							<input type="Text" name="phone" value="<?= $rs_phone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
						</td>
					</tr>
					<tr>
						<th>�޴� ��ȭ��ȣ</th>
						<td>
							<input type="Text" name="hphone" value="<?= $rs_hphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
						</td>
						<th>FAX ��ȣ</th>
						<td>
							<input type="Text" name="fphone" value="<?= $rs_fphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
						</td>
					<tr>
					<tr>
						<th>�̸���</th>
						<td><input type="Text" name="email" value="<?= $rs_email ?>" style="width:90%;" class="txt"></td>
						<th>�̸��� ���ſ���</th>
						<td>
							<input type="radio" name="rd_email_tf" value="Y" <? if (($rs_email_tf =="Y") || ($rs_email_tf =="")) echo "checked"; ?>> ����<span style="width:20px;"></span>
							<input type="radio" name="rd_email_tf" value="N" <? if ($rs_email_tf =="N") echo "checked"; ?>> �̼���</td>
							<input type="hidden" name="email_tf" value="">
						</td>
					<tr>
					<tr>
						<th>�ŷ�����</th>
						<td><input type="Text" name="account_bank" value="<?= $rs_account_bank?>" itemname="�ŷ�����" style="width:40%;" class="txt"></td>
						<th>���¹�ȣ</th>
						<td><input type="Text" name="account" value="<?= $rs_account?>" style="width:40%;" itemname="���¹�ȣ" class="txt" onkeyup="return isPhoneNumber(this)"></td>
					</tr>

					<tr>
						<th>������</th>
						<td><input type="Text" name="account_owner_nm" value="<?= $rs_account_owner_nm?>" style="width:40%;" itemname="������" class="txt"></td>
						<th>��� �Ⱓ</th>
						<td class="lpd20 right">
							<input name="contract_start" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $rs_contract_start ?>"> ~ 
							<input name="contract_end" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $rs_contract_end ?>">
						</td>
					</tr>
						<tr>
							<th>Ȩ������</th>
							<td>
								<input type="Text" name="homepage" value="<?= $rs_homepage?>" style="width:90%;" class="txt">
							</td>
							<th>��뿩��</th>
							<td>
								<input type="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> ���<span style="width:20px;"></span>
								<input type="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> �̻��
								<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
							</td>
						</tr>
						<tr>
							<th>��ü�޸�</th>
							<td colspan="3" class="memo">
								<textarea style="width:75%; height:160px;" name="memo"><?= $rs_memo ?></textarea>
							</td>
						</tr>
					  <tr>
							<th>���������</th>
							<td colspan="3">
								<?=makeAdminInfoByMDSelectBox($conn, "sale_adm_no" ," style='width:100px;' ","����","",$rs_sale_adm_no)?>
							</td>
						</tr>
				</tbody>
			</table>
				
			<div class="sp20"></div>

			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="12%">
					<col width="38%">
					<col width="12%">
					<col width="38%">
				</colgroup>
				<thead>
					
				</thead>
				<tbody>
					
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
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>