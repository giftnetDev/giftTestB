<?session_start();?>
<?
# =============================================================================
# File Name    : manager_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2016-04-01
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
	require "../../_classes/biz/admin/admin.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$date_start			= trim($date_start);
	$date_end				= trim($date_end);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	
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
	$ad_type2				= SetStringToDB($ad_type2);
	$account_bank		= SetStringToDB($account_bank);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	
	
	if ($mode == "I") {
		
		$result =  insertCompany($conn, $cp_type, $cp_nm, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $manager_nm, $dept, $phone, $hphone, $fphone, $email, $email_tf, $manager_nm2, $dept2, $phone2, $hphone2, $fphone2, $email2, $email_tf2, $manager_nm3, $dept3, $phone3, $hphone3, $fphone3, $email3, $email_tf3, $contract_start, $contract_end, $ad_type, $ad_type2, $account_bank, $account_name, $account, $account_bank2, $account_name2, $account2, $account_bank3, $account_name3, $account3, $memo, $use_tf, $s_adm_no);
		
		$new_cp_no = mysql_insert_id();
		
		if ($cp_type == "�Ǹ�") $group_no = "2";
		if ($cp_type == "����") $group_no = "3";
		if ($cp_type == "�ǸŰ���") $group_no = "4";

		$result = insertAdmin($conn, $m_id, $m_pwd, $manager_nm, $adm_info, $hphone, $cp_phone, $email, $group_no, $adm_flag, $position_code, $dept_code, $new_cp_no, 'Y', '0');

	}

	if ($result) {
?>
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		location.href =  "/manager/";
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
<script type="text/javascript" src="../js/jquery-1.7.min.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script language="javascript">

	function sample6_execDaumPostcode(a) {
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

				// �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				if (a == "O") {
					document.getElementById('cp_zip').value = data.zonecode; //5�ڸ� �������ȣ ���
					document.getElementById('cp_addr').value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById('cp_addr').focus();
				}

				// �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				if (a == "R") {
					document.getElementById('re_zip').value = data.zonecode; //5�ڸ� �������ȣ ���
					document.getElementById('re_addr').value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById('re_addr').focus();
				}

			}
		}).open();
	}


	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var frm = document.frm;
		
		if (isNull(frm.cp_nm.value)) {
			alert('��ü���� �Է����ּ���.');
			frm.cp_nm.focus();
			return ;		
		}
		
		if (frm.cp_type.value == "") {
			alert('��ü������ �������ּ���.');
			frm.cp_type.focus();
			return ;		
		}

		if (frm.ad_type.value == "") {
			alert('���籸���� �������ּ���.');
			frm.ad_type.focus();
			return ;		
		}

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
		

		if (isNull(frm.m_id.value)) {
			alert('���̵� �Է����ּ���.');
			frm.m_id.focus();
			return ;		
		}

		if (isNull(frm.m_pwd.value)) {
			alert('��й�ȣ�� �Է����ּ���.');
			frm.m_pwd.focus();
			return ;		
		}

		if (isNull(frm.manager_nm.value)) {
			alert('����ڸ��� �Է����ּ���.');
			frm.manager_nm.focus();
			return ;		
		}

		if (isNull(frm.hphone.value)) {
			alert('�޴���ȭ��ȣ�� �Է����ּ���.');
			frm.hphone.focus();
			return ;		
		}

		if (isNull(frm.email.value)) {
			alert('�̸����� �Է����ּ���.');
			frm.email.focus();
			return ;		
		}

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

		frm.mode.value = "I";

		frm.method = "post";
		frm.action = "manager_write.php";
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

	function chk_admin_id() {
		
		var frm = document.frm;

		var admin_id = frm.m_id.value.trim();

		var request = $.ajax({
			url:"/manager/company/manager_id_chk.php",
			type:"POST",
			data:{admin_id:admin_id},
			dataType:"html"
		});

		request.done(function(msg) {
			if (msg == "1") {
				alert("�ߺ��� ���̵� �ֽ��ϴ�.");
				frm.m_id.value = "";
			}
		});

		request.fail(function(jqXHR, textStatus) {
			alert("Request failed : " +textStatus);
			return false;
		});

	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="<?= $cp_no?>">
<input type="hidden" name="con_cp_type" value="<?= $con_cp_type?>">
<input type="hidden" name="date_start" value="<?= $date_start ?>">
<input type="hidden" name="date_end" value="<?= $date_end ?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

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

	//require "../../_common/left_area.php";
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>���Ⱦ�ü ȸ������</h2>  
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>��ü��</th>
							<td colspan="3"><input type="text" name="cp_nm" value="<?= $rs_cp_nm ?>" style="width:60%;" itemname="��ü��" required class="txt"></td>
						</tr>
					</thead>
					<tbody>
							<th>��ü����</th>
							<td>
								<select name='cp_type' class="box01"  style='width:125px;' >
									<option value=''>����</option><option value='�Ǹ�'>�Ǹ�</option>
									<option value='����'>����</option>
									<option value='�ǸŰ���'>�ǸŰ���</option>
								</select>
							</td>
							<th>���籸��</th>
							<td>
								<?= makeSelectBox($conn,"AD_TYPE","ad_type","125","����","",$rs_ad_type)?>
							</td>
						</tr>

						<tr>
							<th>�����	��Ϲ�ȣ</th>
							<td>
								<input type="Text" name="biz_no" value="<?= $rs_biz_no?>" style="width:140px;" itemname="�����	��Ϲ�ȣ" required class="txt"> '-' �����Ͽ� �Է��� �ּ���
							</td>
							<th>��ǥ�ڸ�</th>
							<td><input type="Text" name="ceo_nm" value="<?= $rs_ceo_nm ?>" style="width:30%;" itemname="��ǥ�ڸ�" required class="txt"></td>
						</tr>
						<tr>
							<th>��ǥ ��ȭ��ȣ</th>
							<td>
								<input type="Text" name="cp_phone" value="<?= $rs_cp_phone?>" style="width:120px;" itemname="��ǥ ��ȭ��ȣ" required class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>��ǥ FAX</th>
							<td>
								<input type="Text" name="cp_fax" value="<?= $rs_cp_fax?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>

						<tr>
							<th>�ּ�</th>
							<td colspan="3">
								<input type="Text" name="cp_zip" id="cp_zip" value="<?= $rs_cp_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="cp_addr" id="cp_addr" value="<?= $rs_cp_addr?>" style="width:65%;" class="txt">
								<a href="javascript:void(0);" onclick="sample6_execDaumPostcode('O')"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
							</td>
						<tr>

						<tr>
							<th>��ǰ �ּ�</th>
							<td colspan="3">
								<input type="Text" name="re_zip" id="re_zip" value="<?= $rs_re_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="re_addr" id="re_addr" value="<?= $rs_re_addr?>" style="width:65%;" class="txt">
								<a href="javascript:void(0);" onclick="sample6_execDaumPostcode('R')"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
							</td>
						<tr>

						<tr>
							<th>����</th>
							<td><input type="Text" name="upjong" value="<?= $rs_upjong?>" style="width:40%;" class="txt"></td>
							<th>����</th>
							<td><input type="Text" name="uptea" value="<?= $rs_uptea?>" style="width:40%;" class="txt"></td>
						</tr>

						<tr>
							<th>�ŷ�����</th>
							<td><input type="Text" name="account_bank" value="<?= $rs_account_bank?>" itemname="�ŷ�����" style="width:40%;" class="txt"></td>
							<th>���¹�ȣ</th>
							<td><input type="Text" name="account" value="<?= $rs_account?>" style="width:40%;" itemname="���¹�ȣ" class="txt" onkeyup="return isPhoneNumber(this)"></td>
						</tr>

						<tr>
							<th>��� �Ⱓ</th>
							<td class="lpd20 right" colspan="3">
								<input name="contract_start" type="text" class="txt" style="width:80px;" readonly value="<?= $rs_contract_start ?>"><a onclick="show_calendar('document.frm.contract_start', document.frm.contract_start.value)" style="cursor:hand">
								<img src="/manager/images/calendar/cal.gif" align="absmiddle"></a> ~ 
								<input name="contract_end" type="text" class="txt" style="width:80px;" readonly value="<?= $rs_contract_end ?>"><a onclick="show_calendar('document.frm.contract_end', document.frm.contract_end.value)" style="cursor:hand">
								<img src="/manager/images/calendar/cal.gif" align="absmiddle"></a>
							</td>
						</tr>


						<tr>
							<!--
							<th>Ȱ����</th>
							<td>
								<input type="Text" name="dc_rate" value="<?= $rs_dc_rate?>" value="" style="width:70px;" class="txt" onkeyup="return isPhoneNumber(this)"> %
							</td>
							-->
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
								<textarea style="width:75%" name="memo"><?= $rs_memo ?></textarea>
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
						<tr>
							<th>���̵�</th>
							<td><input type="Text" name="m_id" value="" style="width:90px;" required class="txt" onBlur="chk_admin_id()"></td>
							<th>��й�ȣ</th>
							<td><input type="password" name="m_pwd" value="" style="width:120px;" required class="txt"></td>
						</tr>
						<tr>
							<th>����� ��</th>
							<td><input type="Text" name="manager_nm" value="<?= $rs_manager_nm ?>" required style="width:90px;" class="txt"></td>
							<th>��ȭ��ȣ</th>
							<td>
								<input type="Text" name="phone" value="<?= $rs_phone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>�޴� ��ȭ��ȣ</th>
							<td>
								<input type="Text" name="hphone" value="<?= $rs_hphone ?>" style="width:120px;" required class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>FAX ��ȣ</th>
							<td>
								<input type="Text" name="fphone" value="<?= $rs_fphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						<tr>
						<tr>
							<th>�̸���</th>
							<td><input type="Text" name="email" value="<?= $rs_email ?>" style="width:90%;" required class="txt"></td>
							<th>�̸��� ���ſ���</th>
							<td>
								<input type="radio" name="rd_email_tf" value="Y" <? if (($rs_email_tf =="Y") || ($rs_email_tf =="")) echo "checked"; ?>> ����<span style="width:20px;"></span>
								<input type="radio" name="rd_email_tf" value="N" <? if ($rs_email_tf =="N") echo "checked"; ?>> �̼���</td>
								<input type="hidden" name="email_tf" value="">
							</td>
						<tr>
					</tbody>
				</table>

				<div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
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