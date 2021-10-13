<?session_start();?>
<?
# =============================================================================
# File Name    : admin_write.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "AD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/admin/admin.php";

	$mm_subtree	 = "4";
#====================================================================
# DML Process
#====================================================================
	$adm_name	= SetStringToDB($adm_name);
	$adm_info	= SetStringToDB($adm_info);

	#echo $adm_no;

	if ($mode == "I") {

		$result_flag = dupAdmin($conn, $adm_id);
		
		if ($result_flag == 0) {
		
			$result =  insertAdmin($conn, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $md_tf, $use_tf, $s_adm_no);
		
		} else {
?>	
<script language="javascript">
		alert('사용중인 ID 입니다.');
		document.location.href = "admin_write.php";
</script>
<?
		exit;
		}

	}

	if ($mode == "U") {

		if ($old_adm_id <> $adm_id) {
			$result_flag = dupAdmin($conn, $adm_id);
		}
		
		if ($result_flag == 0) {

			$result = updateAdmin($conn, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $md_tf, $use_tf, $s_adm_no, $adm_no);
		} else {
?>	
<script language="javascript">
		alert('사용중인 ID 입니다.');
		document.location.href = "admin_write.php?mode=S&adm_no=<?=$adm_no?>";
</script>
<?
		exit;
		}
	}


	//if ($mode == "T") {

	//	updateEventUseTF($conn, $use_tf, $s_adm_no, $event_no);

	//}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

	//		$result = deleteEvent($conn, $s_adm_no, $event_no);
		
	//	}

        updateAdminUseTF($conn, 'N', $s_adm_no, $adm_no);

?>	
<script language="javascript">
		alert('수정되었습니다.');
		document.location.href = "admin_write.php?mode=S&adm_no=<?=$adm_no?>";
</script>
<?

	}

	if ($mode == "S") {

		$arr_rs = selectAdmin($conn, $adm_no);

		//ADM_NO, ADM_ID, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
		//GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE, MD_TF

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
		$rs_md_tf					= trim($arr_rs[0]["MD_TF"]); 
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
		document.location.href = "admin_list.php<?=$strParam?>";
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
<script type="text/javascript">

function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "admin_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var adm_no = "<?= $adm_no ?>";
	
	frm.adm_name.value = frm.adm_name.value.trim();
	frm.adm_id.value = frm.adm_id.value.trim();
	frm.passwd.value = frm.passwd.value.trim();

	if (frm.group_no.value == "") {
		alert('관리자 그룹을 선택해주세요.');
		frm.group_no.focus();
		return ;		
	}

	if (frm.txt_com_code.value == "") {
		alert('소속 업체를 선택해주세요.');
		frm.txt_com_code.focus();
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

	if (document.frm.rd_md_tf == null) {
				alert(document.frm.rd_md_tf);
	} else {
		if (frm.rd_md_tf[0].checked == true) {
			frm.md_tf.value = "Y";
		} else {
			frm.md_tf.value = "N";
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
				alert("사용중인 아이디 입니다.");
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
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="adm_no" value="<?=$adm_no?>" />
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

      
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>관리자 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<thead>
					<? if ($s_adm_cp_type <> "운영") { ?>
					<input type="hidden" name="group_no" value="<?=$rs_group_no?>">
					<tr>
						<th>소속 업체</th>
						<td colspan="3">
							<?= getCompanyName($conn, $rs_com_code);?>
							<input type="hidden" name="cp_type" value="<?=$rs_com_code?>">
							<input type="hidden" name="com_code" value="<?=$rs_com_code?>">
						</td>
					</tr>
					<? } else { ?>
					<tr>
						<th>관리자 그룹</th>
						<td>
							<?= makeAdminGroupSelectBox($conn, "group_no" , "125px", "관리자 그룹 선택", "", $rs_group_no); ?>
						</td>
						<th>소속 업체</th>
						<td>
							<input type="text" class="companys" style="width:210px" name="txt_com_code" value="<?=getCompanyAutocompleteTextBox($conn,'',$rs_com_code)?>" />
							<script>
							$(function() {
						     var cache = {};
								$( ".companys" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response( cache[term] );
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=", request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										}).fail(function(jqXHR, status, error){
												alert(error);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".companys").val(ui.item.value);
										$("input[name=com_code]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=com_code]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=com_code]").val('');
											} else {
												if(data[0].COMPANY != $(".companys").val())
												{

													$(".companys").val();
													$("input[name=com_code]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="com_code" value="<?=$rs_com_code?>">
						</td>
					</tr>
					<? } ?>
				</thead>
				<tbody>
					<tr>
						<th>이름</th>
						<td><input type="text" class="box01" style="width:35%" name="adm_name" value="<?=$rs_adm_name?>" /></td>
						<th>아이디</th>
						<td>
							<input type="text" class="box01" style="width:35%" name="adm_id" value="<?=$rs_adm_id?>" />
							<input type="hidden" name="old_adm_id" value="<?=$rs_adm_id?>">
						</td>
					</tr>
					<tr>
						<th>비밀번호</th>
						<td colspan="3">
						<!--<? if ($s_adm_cp_type <> "운영") { ?>
							<input type="password" class="box01" style="width:35%" name="passwd" value="<?=$rs_passwd?>" />
							<? } else { ?>
							<input type="text" class="box01" style="width:35%" name="passwd" value="<?=$rs_passwd?>" />
						<? } ?>-->						

						<? 	if ($s_adm_no == "1") 
							{ 
						?>
								<input type="text" class="box01" style="width:35%" name="passwd" value="<?=$rs_passwd?>" />
						<? 	} 
							else  
							{ 
								if ($s_adm_no == $rs_adm_no)
								{	
						?>
									<input type="text" class="box01" style="width:35%" name="passwd" value="<?=$rs_passwd?>" />
						<?		}
								else	
								{
						?>
									<input type="password" class="box01" style="width:35%" name="passwd" disabled value="<?=$rs_passwd?>"/>
						<?		}
							}
						?>
						
						</td>
					</tr>
					<tr>
						<th>부서</th>
						<td><?= makeSelectBox($conn,"DEPT","dept_code","125","선택","",$rs_dept_code)?></td>
						<th>직급</th>
						<td><?= makeSelectBox($conn,"POSITION","position_code","125","선택","",$rs_position_code)?></td>
					</tr>
					<tr>
						<th>전화번호</th>
						<td><input type="text" class="box01" style="width:35%" name="adm_phone" value="<?=$rs_adm_phone?>" /></td>
						<th>휴대전화번호</th>
						<td><input type="text" class="box01" style="width:35%" name="adm_hphone" value="<?=$rs_adm_hphone?>" onkeyup="return isPhoneNumber(this)" /></td>
					</tr>
					<tr>
						<th>이메일</th>
						<td><input type="text" class="box01" style="width:90%" name="adm_email" value="<?=$rs_adm_email?>" /></td>
						<th>영업담당</th>
						<td>
							<input type="radio" class="radio" name="rd_md_tf" value="Y" <? if ($rs_md_tf =="Y") echo "checked"; ?>> 네 <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_md_tf" value="N" <? if (($rs_md_tf =="N") || ($rs_md_tf =="")) echo "checked"; ?>> 아니오
							<input type="hidden" name="md_tf" value="<?= $rs_md_tf ?>"> 
						</td>
					</tr>
					<tr>
						<th>기타메모</th>
						<td colspan="3" class="subject"><textarea class="box01" cols="100" rows="5" name="adm_info"><?=$rs_adm_info?></textarea></td>
					</tr>

					<tr>
						<th>사용여부</th>
						<td colspan="3">
							<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용함 <span style="width:20px;"></span>
							<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 사용안함
							<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						</td>
					</tr>

				</tbody>
        </table>
        <div class="btnright">
				<? if ($adm_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
          <a href="javascript:sendKeyword();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
          <a href="javascript:sendKeyword();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? }?>

				<? if ($s_adm_cp_type == "운영") { ?>
					<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
				<? } ?>

				<? if ($s_adm_cp_type == "운영") { ?>
				<? if ($adm_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
				<? } ?>
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