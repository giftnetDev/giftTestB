<?session_start();?>
<?
# =============================================================================
# File Name    : admin_modify.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
//	$menu_right = "AD002"; // 메뉴마다 셋팅 해 주어야 합니다

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	$sPageRight_		= "Y";
	$sPageRight_R		= "Y";
	$sPageRight_I		= "Y";
	$sPageRight_U		= "Y";
	$sPageRight_D		= "Y";
	$sPageRight_F		= "Y";

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/admin/admin.php";

	$mm_subtree	 = "4";
#====================================================================
# DML Process
#====================================================================
	$adm_name	= SetStringToDB($adm_name);
	$adm_info	= SetStringToDB($adm_info);

	#echo $adm_no;

	if ($mode == "") $mode = "S";


	if ($mode == "I") {
		
		$result =  insertAdmin($conn, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {

		$result = updateAdmin($conn, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $use_tf, $s_adm_no, $adm_no);
	}


	if ($mode == "T") {

		updateEventUseTF($conn, $use_tf, $s_adm_no, $event_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			//$result = deleteAdmin($conn, $s_adm_no, $admi_no);
		
//		}
	}
	
	if ($mode == "S") {

		$arr_rs = selectAdmin($conn, $s_adm_no);

		//ADM_NO, ADM_ID, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
		//GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE

		$rs_adm_no					= trim($arr_rs[0]["ADM_NO"]); 
		$rs_adm_id					= trim($arr_rs[0]["ADM_ID"]); 
		$rs_passwd					= trim($arr_rs[0]["PASSWD"]); 
		$rs_adm_name				= SetStringFromDB($arr_rs[0]["ADM_NAME"]); 
		$rs_adm_info				= SetStringFromDB($arr_rs[0]["ADM_INFO"]); 
		$rs_adm_hphone			= trim($arr_rs[0]["ADM_HPHONE"]); 
		$rs_adm_phone				= trim($arr_rs[0]["ADM_PHONE"]); 
		$rs_adm_email				= trim($arr_rs[0]["ADM_EMAIL"]); 
		$rs_group_no				= trim($arr_rs[0]["GROUP_NO"]); 
		$rs_adm_flag				= trim($arr_rs[0]["ADM_FLAG"]); 
		$rs_position_code		= trim($arr_rs[0]["POSITION_CODE"]); 
		$rs_dept_code				= trim($arr_rs[0]["DEPT_CODE"]); 
		$rs_com_code				= trim($arr_rs[0]["COM_CODE"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 

	}

	if ($rs_event_from == "") {
		$rs_event_from = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_event_to == "") {
		$rs_event_to = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_event_result == "") {
		$rs_event_result = date("Y-m-d",strtotime("0 day"));
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "admin_modify.php";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script type="text/javascript">

function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "admin_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var adm_no = "<?= $s_adm_no ?>";
	
	frm.adm_name.value = frm.adm_name.value.trim();
	frm.adm_id.value = frm.adm_id.value.trim();
	frm.passwd.value = frm.passwd.value.trim();

	if (frm.group_no.value == "") {
		alert('관리자 그룹을 선택해주세요.');
		frm.group_no.focus();
		return ;		
	}

	if (isNull(frm.adm_name.value)) {
		alert('이름을 입력해주세요.');
		frm.adm_name.focus();
		return ;		
	}

	if (isNull(frm.adm_id.value)) {
		alert('아이디을 입력해주세요.');
		frm.adm_id.focus();
		return ;		
	}

	if (isNull(frm.passwd.value)) {
		alert('비밀번호를 입력해주세요.');
		frm.passwd.focus();
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

	if (isNull(adm_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.adm_no.value = frm.adm_no.value;
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

function js_delete() {

	var frm = document.frm;

	bDelOK = confirm('자료를 삭제 하시겠습니까?');
	
	if (bDelOK==true) {
		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

}

	// Ajax
function sendKeyword() {

	if (frm.old_adm_id.value != frm.adm_id.value)	{

		var keyword = document.frm.adm_id.value;

		//alert(keyword);
					
		if (keyword != '') {
			var params = "keyword="+encodeURIComponent(keyword);
		
			//alert(params);
			sendRequest("admin_dup_check.php", params, displayResult, 'POST');
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
				alert("사용중인 권한 코드 입니다.");
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
<body id="bg">
<div id="wrap">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="adm_no" value="<?=$s_adm_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
<input type="hidden" name="group_no" value="<?= $rs_group_no ?>"> 
<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>

	<div id="contents">
		<p><a href="/">홈</a> &gt; 관리자 관리</p>

		<div id="tit01">
			<h2>관리자 정보 수정</h2>
		</div>

		<div id="bbsWrite">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<thead>
					<tr>
						<td class="lpd20">이름</td>
						<td><input type="text" class="box01" style="width:35%" name="adm_name" value="<?=$rs_adm_name?>" /></td>
						<td class="lpd20">아이디</td>
						<td>
							<input type="hidden" name="adm_id" value="<?=$rs_adm_id?>" /><?=$rs_adm_id?>
							<input type="hidden" name="old_adm_id" value="<?=$rs_adm_id?>">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">비밀번호</td>
						<td colspan="3"><input type="password" class="box01" style="width:35%" name="passwd" value="<?=$rs_passwd?>" /></td>
					</tr>
					<tr>
						<td class="lpd20">부서</td>
						<td><?= makeSelectBox($conn,"DEPT","dept_code","125","선택","",$rs_dept_code)?></td>
						<td class="lpd20">직급</td>
						<td><?= makeSelectBox($conn,"POSITION","position_code","125","선택","",$rs_position_code)?></td>
					</tr>
					<tr>
						<td class="lpd20">전화번호</td>
						<td><input type="text" class="box01" style="width:35%" name="adm_phone" value="<?=$rs_adm_phone?>" onkeyup="return isPhoneNumber(this)" /></td>
						<td class="lpd20">휴대전화번호</td>
						<td><input type="text" class="box01" style="width:35%" name="adm_hphone" value="<?=$rs_adm_hphone?>" onkeyup="return isPhoneNumber(this)" /></td>
					</tr>
					<tr>
						<td class="lpd20">이메일</td>
						<td colspan="3"><input type="text" class="box01" style="width:45%" name="adm_email" value="<?=$rs_adm_email?>" /></td>
					</tr>
					<tr>
						<td class="lpd20">기타메모</td>
						<td colspan="3" class="subject"><textarea class="box01" cols="100" rows="5" name="adm_info"><?=$rs_adm_info?></textarea></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
			<span class="btn_list">

				<? if ($adm_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:sendKeyword();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a> 
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:sendKeyword();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a> 
					<? } ?>
				<? }?>
				<!--<a href="javascript:js_list();"><img src="../images/common/btn/btn_list.gif" alt="목록" /></a>-->
				<? if ($adm_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
				<!--<a href="javascript:js_delete();"><img src="../images/common/btn/btn_delete.gif" alt="삭제" /></a>-->
					<? } ?>
				<? } ?>
			</span>
		</div>
	</div>

	<div id="site_info">Copyright &copy; 2009 (재)아름지기 All Rights Reserved.</div>

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