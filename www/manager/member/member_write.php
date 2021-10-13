<?session_start();?>
<?
# =============================================================================
# File Name    : member_write.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "ME002"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/member/member.php";

#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") 
	{
		insertMember($conn, $mem_type, $mem_id, $mem_pw, $mem_nm, $cp_nm, $ceo_nm, $biz_num1, $biz_num2, $biz_num3, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $upjong, $uptea, $etc, $use_tf, $reg_adm, $cp_type);
	?>	
	<script language="javascript">
		alert('���� �Ǿ����ϴ�.');
		document.location.href = "member_list.php";
	</script>
	<?

	}

	if ($mode == "U") {
		
		if($use_tf == "N" || ($use_tf == "Y" && $cp_type <> "" && $cp_type <> "0")) { 
			
			$result = updateMember($conn, $mem_type, $mem_nm, $mem_pw, $biz_num1, $biz_num2, $biz_num3, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $upjong, $uptea, $etc, $use_tf, $s_adm_no, $mem_no);

			updateMemberExtra($conn, $cp_type, $mem_no);
		}

		if($result) { 
?>	
<script language="javascript">
		alert('�����Ǿ����ϴ�.');
		document.location.href = "member_write.php?mode=S&mem_no=<?=$mem_no?>";
</script>
<?
		} else { 
?>	
<script language="javascript">
		alert('������ �߻��Ͽ����ϴ�. ��� �����ÿ��� �� �ҼӾ�ü�� �����Ͽ� �ּž� �ֹ��Է� �� ��������� �����մϴ�.');
</script>
<?
		}
	}

	if ($mode == "D") {

		deleteMember($conn, $mem_no, $s_adm_no);
?>	
<script language="javascript">
		alert('���� �Ǿ����ϴ�.');
		//document.location.href = "member_write.php?mode=S&mem_no=<?=$mem_no?>";
		document.location.href = "member_list.php";
</script>
<?

	}

	if ($mode == "S") {

		$arr_rs = selectMember($conn, $mem_no);
		
		$rs_mem_type				= trim($arr_rs[0]["MEM_TYPE"]); 
		$rs_mem_id					= trim($arr_rs[0]["MEM_ID"]); 
		$rs_mem_pw					= trim($arr_rs[0]["MEM_PW"]); 
		$rs_mem_nm					= SetStringFromDB($arr_rs[0]["MEM_NM"]); 
		$rs_email					= trim($arr_rs[0]["EMAIL"]); 
		$rs_phone					= trim($arr_rs[0]["PHONE"]); 
		$rs_hphone					= trim($arr_rs[0]["HPHONE"]); 
		$rs_zipcode					= SetStringFromDB(trim($arr_rs[0]["ZIPCODE"])); 
		$rs_addr1					= SetStringFromDB(trim($arr_rs[0]["ADDR1"])); 
		$rs_etc						= SetStringFromDB(trim($arr_rs[0]["ETC"])); 
		$rs_biz_num1				= SetStringFromDB(trim($arr_rs[0]["BIZ_NUM1"])); 
		$rs_biz_num2				= SetStringFromDB(trim($arr_rs[0]["BIZ_NUM2"])); 
		$rs_biz_num3				= SetStringFromDB(trim($arr_rs[0]["BIZ_NUM3"])); 

		$cp_type					= trim($arr_rs[0]["CP_NO"]); 

		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.');
		document.location.href = "member_list.php<?=$strParam?>";
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
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
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
								
				 // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				document.getElementById("zipcode").value = data.zonecode;
				//document.getElementById("re_zip").value = data.postcode2;
				document.getElementById("addr1").value = fullAddr;
				// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
				document.getElementById("addr1").focus();


            }
        }).open();
    }

		function js_addr_open(s) {
			sample6_execDaumPostcode();
		}

</script>  
<script type="text/javascript">

function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "member_list.php";
	frm.submit();
}


function js_save(val) {

	var frm = document.frm;
	
	if (frm.cp_type.value == "") {
		alert('�Ҽ� ��ü�� �������ּ���.');
		frm.txt_cp_type.focus();
		return ;		
	}
	
	if (document.frm.rd_use_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}
	}

	if(val== "I")
	{
		frm.mode.value = "I";
	}
	else
	{
		frm.mode.value = "U";
	}	

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

function js_delete() {

	var frm = document.frm;

	bDelOK = confirm('�ڷḦ ���� �Ͻðڽ��ϱ�?');
	
	if (bDelOK==true) {
		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

}


</script>

</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="mem_no" value="<?=$mem_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

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

      <input type="hidden" name="mem_type" value="<?=$rs_mem_type?>"/>
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>ȸ�� ����</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable">
			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>
			<!--<tr>
				<th>����� ��ȣ</th>
				<td colspan="3">
					<input type="text" class="box01" style="width:100px" name="biz_num1" value="<?=$rs_biz_num1?>" />
					<input type="text" class="box01" style="width:100px" name="biz_num2" value="<?=$rs_biz_num2?>" />
					<input type="text" class="box01" style="width:100px" name="biz_num3" value="<?=$rs_biz_num3?>" />
				</td>
			</tr>				20210608 ����-->
			<tr>
				<th>���̵�</th>
				<td>
					<input type="text" class="box01" style="width:35%" name="mem_id" value="<?=$rs_mem_id?>" />
				</td>
				<th>�̸�</th>
				<td><input type="text" class="box01" style="width:35%" name="mem_nm" value="<?=$rs_mem_nm?>" /></td>
				
			</tr>
			<tr>
				<th>��й�ȣ</th>
				<td colspan="3">
					<input type="password" class="box01" style="width:35%" name="mem_pw" value="<?=$rs_mem_pw?>" />
				</td>
			</tr>
			<tr>
				<th>��ȭ��ȣ</th>
				<td><input type="text" class="box01" style="width:35%" name="phone" value="<?=$rs_phone?>" onkeyup="return isPhoneNumber(this)" maxlength="13" /></td>
				<th>�޴���ȭ��ȣ</th>
				<td><input type="text" class="box01" style="width:35%" name="hphone" value="<?=$rs_hphone?>" onkeyup="return isPhoneNumber(this)" maxlength="13" /></td>
			</tr>
			<tr>
				<th>�̸���</th>
				<td colspan="3"><input type="text" class="box01" style="width:35%" name="email" value="<?=$rs_email?>" /></td>
			</tr>
			<tr>
				<th>�ּ� 1</th>
				<td colspan="3">
					<input type="Text" name="zipcode" id="zipcode" value="<?= $rs_zipcode?>" style="width:60px;" maxlength="7" class="txt">
					<input type="Text" name="addr1" id="addr1" value="<?= $rs_addr1?>" style="width:65%;" class="txt">
					<a href="#none" onClick="js_addr_open('s');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
				</td>
			<tr>
			<tr>
				<th>��Ÿ ����</th>
				<td colspan="3">
					<textarea class="box01" cols="100" rows="5" name="etc"><?=$rs_etc?></textarea>
				</td>
			</tr>
			
        </table>

		<h3>ȸ�� ����</h3>  
		<table cellpadding="0" cellspacing="0" class="colstable">
			<colgroup>
				<col width="15%" />
				<col width="35%" />
				<col width="15%" />
				<col width="35%" />
			</colgroup>
			<tr>
				<th>�Ҽ� ��ü</th>
				<td colspan="3">
					<input type="text" class="autocomplete_off" style="width:35%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
					<input type="hidden" name="cp_type" value="<?=$cp_type?>">

					<script>
						$(function(){

							$("input[name=txt_cp_type]").keydown(function(e){

								if(e.keyCode==13) { 

									var keyword = $(this).val();
									if(keyword == "") { 
										$("input[name=cp_type]").val('');
										js_search();
									} else { 
										$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
											if(data.length == 1) { 
												
												js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

											} else if(data.length > 1){ 
												NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

											} else 
												alert("�˻������ �����ϴ�.");
										});
									}
								}

							});

							$("input[name=txt_cp_type]").keyup(function(e){
								var keyword = $(this).val();

								if(keyword == "") { 
									$("input[name=cp_type]").val('');
								}
							});

						});

					</script>
					<script>
						function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
							
							$(function(){

								$("input[name="+target_name+"]").val(cp_nm);
								$("input[name="+target_value+"]").val(cp_no);

							});
						}
					</script>
				</td>
			</tr>
			<tr>
				<th>���ο���</th>
				<td colspan="3">
					<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> ���οϷ� <span style="width:20px;"></span>
					<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> �̽���
					<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
				</td>
			</tr>
		</table>
        <div class="btnright">
			<? if ($sPageRight_U == "Y" && $mem_no <> "") {?>
				<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? }else{ ?>
				<a href="javascript:js_save('I');"><img src="../images/admin/btn_confirm.gif" alt="Ȯ��" /></a>
			<? } ?>
			<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="���" /></a>

			<? if ($sPageRight_U == "Y" && $mem_no <> "") {?>
				<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
			<? } ?>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>