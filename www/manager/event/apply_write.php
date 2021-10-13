<?session_start();?>
<?
# =============================================================================
# File Name    : apply_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/event/event.php";

	$mm_subtree	 = "4";
#====================================================================
# DML Process
#====================================================================

	if ($mode == "U") {

		$result = updateEventApply($conn, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $email, $pick_tf, $use_tf, $s_adm_no, $apply_no);
	}


	if ($mode == "T") {

		updateEventPickTF($conn, $pick_tf, $s_adm_no, $apply_no);

	}

	if ($mode == "D") {
		
		
		$result = deleteEventApply($conn, $s_adm_no, $apply_no);
		
	}

	if ($mode == "S") {

		$arr_rs = selectEventApply($conn, $apply_no);
		
		$rs_apply_no				= trim($arr_rs[0]["APPLY_NO"]); 
		$rs_event_no				= trim($arr_rs[0]["EVENT_NO"]); 
		$rs_event_type			= trim($arr_rs[0]["EVENT_TYPE"]); 
		$rs_member_no				= trim($arr_rs[0]["MEMBER_NO"]); 
		$rs_member_nm				= trim($arr_rs[0]["MEMBER_NM"]); 
		$rs_member_type			= trim($arr_rs[0]["MEMBER_TYPE"]); 
		$rs_member_id				= trim($arr_rs[0]["MEMBER_ID"]); 
		$rs_email						= trim($arr_rs[0]["EMAIL"]); 
		$rs_phone01					= trim($arr_rs[0]["PHONE01"]); 
		$rs_phone02					= trim($arr_rs[0]["PHONE02"]); 
		$rs_phone03					= trim($arr_rs[0]["PHONE03"]); 
		$rs_zipcode					= trim($arr_rs[0]["ZIPCODE"]); 
		$rs_addr01					= trim($arr_rs[0]["ADDR01"]); 
		$rs_addr02					= trim($arr_rs[0]["ADDR02"]); 
		$rs_answer					= trim($arr_rs[0]["ANSWER"]); 
		$rs_pick_tf					= trim($arr_rs[0]["PICK_TF"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
		
		if ($rs_member_type == "M") {
			$str_member_type = "회원";
		} else {
			$str_member_type = "비회원";
		}

		$arr_email = explode("@",$rs_email);
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_member_type=".$con_member_type."&con_event_no=".$con_event_no."&con_pick_tf=".$con_pick_tf."&search_field=".$search_field."&search_str=".$search_str;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "apply_list.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script language="javascript" type="text/javascript" src="../js/calendar.js"></script>

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "apply_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var apply_no = "<?= $apply_no ?>";

	if (isNull(frm.email01.value)) {
		alert('이메일을 입력해주세요.');
		frm.email01.focus();
		return ;		
	}

	if (isNull(frm.email02.value)) {
		alert('이메일을 입력해주세요.');
		frm.email02.focus();
		return ;		
	}
	
	frm.email.value = frm.email01.value+"@"+frm.email02.value;

	if (isNull(frm.phone01.value)) {
		alert('연락처을 입력해주세요.');
		frm.phone01.focus();
		return ;		
	}

	if (isNull(frm.phone02.value)) {
		alert('연락처을 입력해주세요.');
		frm.phone02.focus();
		return ;		
	}

	if (isNull(frm.phone03.value)) {
		alert('연락처을 입력해주세요.');
		frm.phone03.focus();
		return ;		
	}

	if (isNull(frm.zipcode01.value)) {
		alert('주소을 입력해주세요.');
		return ;		
	}

	if (isNull(frm.zipcode02.value)) {
		alert('주소을 입력해주세요.');
		return ;		
	}

	if (isNull(frm.addr01.value)) {
		alert('주소을 입력해주세요.');
		return ;		
	}

	if (isNull(frm.addr02.value)) {
		alert('상세 주소을 입력해주세요.');
		frm.addr02.focus();
		return ;		
	}

	frm.zipcode.value = frm.zipcode01.value+frm.zipcode02.value;


	if (document.frm.rd_pick_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_pick_tf[0].checked == true) {
			frm.pick_tf.value = "Y";
		} else {
			frm.pick_tf.value = "N";
		}
	}

	if (isNull(apply_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.apply_no.value = frm.apply_no.value;
	}

	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}


function file_change(file) { 
	document.getElementById("file_name").value = file; 
}

function file_change2(file) { 
	document.getElementById("file_name2").value = file; 
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

function js_cate_02 () {

	var frm = document.frm;
	
	if (frm.cate_02.value == "") {
		frm.email02.value = "";
	} else {
		frm.email02.value = frm.cate_02.value;
	}

}

//우편번호 찾기
function js_post() {
	var url = "/_common/common_post.php?flag=div";
	NewWindow(url, '우편번호찾기', '390', '370', 'NO');
}

</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="apply_no" value="<?=$apply_no?>" />
<input type="hidden" name="event_no" value="<?=$event_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

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
?>


		</td>
		<td class="contentarea">

      
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>이벤트 응모자 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
 				<tr>
					<th>회원구분</th>
					<td>
						<?=$str_member_type?>
					</td>
				</tr>
 				<tr>
					<th>아이디</th>
					<td>
						<?=$rs_member_id?>
					</td>
				</tr>
 				<tr>
					<th>성명</th>
					<td>
						<?=$rs_member_nm?>
					</td>
				</tr>
				<tr>
          <th>이메일</th>
          <td>
						<input type="text" name="email01" value="<?=$arr_email[0]?>" class="txt" style="width: 120px;" /> @
						<input type="text" name="email02" value="<?=$arr_email[1]?>" class="txt" style="width: 120px;" />
						<?= makeSelectBoxOnChange($conn,"EMAIL","cate_02","120","직접입력","",$arr_email[1])?>
						<input type="hidden" name="email" value="<?=$rs_email?>">
					</td>
				</tr>

				<tr>
          <th>연락처</th>
          <td>
						<input type="text" name="phone01" class="txt" style="width: 55px;" value="<?=$rs_phone01?>" maxlength="4" onkeyup="return isNumber(this)" /> - 
						<input type="text" name="phone02" class="txt" style="width: 55px;" value="<?=$rs_phone02?>" maxlength="4" onkeyup="return isNumber(this)" /> - 
						<input type="text" name="phone03" class="txt" style="width: 55px;" value="<?=$rs_phone03?>" maxlength="4" onkeyup="return isNumber(this)" />
					</td>
        </tr>

				<tr>
          <th rowspan="3">주소</th>
          <td>
						<input type="text" class="txt" name="zipcode01" value="<?=substr($rs_zipcode,0,3)?>" maxlength="3" readonly="1" style="width: 30px;" /> -
						<input type="text" class="txt" name="zipcode02" value="<?=substr($rs_zipcode,-3)?>" maxlength="3" readonly="1" style="width: 30px;" />
						<input type="hidden" name="zipcode" value="<?=$rs_zipcode?>" />
						<a href="javascript:js_post();"><img src="../images/admin/btn_filesch.gif" alt="찾기" class="btn_sch"></a><br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" class="txt" name="addr01" value="<?=$rs_addr01?>" readonly="1" style="width: 80%;" /><br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" class="txt" name="addr02" value="<?=$rs_addr02?>" style="width: 80%;" />
					</td>
					</td>
        </tr>

				<tr>
          <th>이벤트 유형</th>
					<td>
						<? if (($rs_event_type =="C") || ($rs_event_type =="")) echo "단순 응모형"; ?>
						<? if ($rs_event_type =="A") echo "답변 입력형"; ?> 
					</td>
        </tr>
				<? if ($rs_event_type =="A") {?>
				<tr>
					<th>답변</th>
					<td>
						<?=nl2br($rs_answer)?>
					</td>
				</tr>
				<? } ?>
        <tr class="end">
          <th>당첨 여부</th>
					<td class="choices">
						<input type="radio" class="radio" name="rd_pick_tf" value="Y" <? if ($rs_pick_tf =="Y") echo "checked"; ?>> 당첨 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_pick_tf" value="N" <? if ($rs_pick_tf =="N") echo "checked"; ?>> 미당첨
						<input type="hidden" name="pick_tf" value="<?= $rs_pick_tf ?>"> 
					</td>
        </tr>

        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($apply_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  <tr>
    <td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
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