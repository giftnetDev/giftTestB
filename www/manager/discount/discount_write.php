<?session_start();?>
<?
# =============================================================================
# File Name    : discount_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @arumjigi Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

	
#==============================================================================
# Confirm right
#==============================================================================

$dc_cate			= trim($dc_cate);

if($dc_cate == "hy"){
	$menu_right = "R0003"; // 메뉴마다 셋팅 해 주어야 합니다
}else if ($dc_cate == "art"){
	$menu_right = "AE002"; // 메뉴마다 셋팅 해 주어야 합니다
}else if ($dc_cate == "aca"){
	$menu_right = "SP003"; // 메뉴마다 셋팅 해 주어야 합니다
}


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

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/discount/discount.php";

	if($mode==""){
		$mode="S";
	}

#====================================================================
# DML Process
#====================================================================

	if ($mode == "I") {

		$title			= SetStringToDB($title);
		$memo				= SetStringToDB($memo);
		
		$result =  insertDiscount($conn, $dc_cate, $title, $memo, $dc_from, $dc_to, $dc_rate, $dc_rate_member, $dc_rate_emp, $use_tf, $s_adm_no);

	}

	if ($mode == "U") {

		$title			= SetStringToDB($title);
		$memo				= SetStringToDB($memo);

		$result = updateDiscount($conn, $dc_cate, $title, $memo, $dc_from, $dc_to, $dc_rate, $dc_rate_member, $dc_rate_emp, $use_tf, $s_adm_no, $dc_no);
	}

	if ($mode == "D") {
		$result = deleteDiscount($conn, $s_adm_no, $dc_no);
	}

	if ($mode == "S") {

		$arr_rs = selectDiscount($conn, $dc_no);
		

		$rs_dc_no						= trim($arr_rs[0]["DC_NO"]); 
		$rs_dc_cate					= trim($arr_rs[0]["DC_CATE"]); 
		$rs_title						= SetStringFromDB($arr_rs[0]["TITLE"]); 
		$rs_memo						= SetStringFromDB($arr_rs[0]["MEMO"]); 
		$rs_dc_from					= trim($arr_rs[0]["DC_FROM"]); 
		$rs_dc_to						= trim($arr_rs[0]["DC_TO"]); 
		$rs_dc_rate					= trim($arr_rs[0]["DC_RATE"]); 
		$rs_dc_rate_member	= trim($arr_rs[0]["DC_RATE_MEMBER"]); 
		$rs_dc_rate_emp			= trim($arr_rs[0]["DC_RATE_EMP"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]); 
	}

	if ($rs_dc_from == "") {
		$rs_dc_from = date("Y-m-d",strtotime("0 day"));
	}

	if ($rs_dc_to == "") {
		$rs_dc_to = date("Y-m-d",strtotime("0 day"));
	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&con_event_state=".$con_event_state."&con_use_tf=".$con_use_tf."&search_field=".$search_field."&search_str=".$search_str."&dc_cate=".$dc_cate;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "discount_list.php<?=$strParam?>";
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

<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.method = "get";
	frm.action = "discount_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var dc_no = "<?= $dc_no ?>";
	
	frm.title.value = frm.title.value.trim();
	
	if (isNull(frm.title.value)) {
		alert('제목을 입력해주세요.');
		frm.title.focus();
		return ;		
	}

	if (frm.dc_rate.value == "") {
		alert("할인율을 선택해 주세요.");
		frm.dc_rate.focus();
		return;
	}

	if (frm.dc_rate_member.value == "") {
		alert("회원 할인율을 선택해 주세요.");
		frm.dc_rate_member.focus();
		return;
	}
	
	if (frm.dc_rate_emp.value == "") {
		alert("직원 할인율을 선택해 주세요.");
		frm.dc_rate_emp.focus();
		return;
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

	if (isNull(dc_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.dc_no.value = frm.dc_no.value;
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

</script>

</head>

<body id="bg">
<div id="wrap">
<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="dc_cate" value="<?=$dc_cate?>" />
<input type="hidden" name="dc_no" value="<?=$dc_no?>" />
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

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>
	<div id="contents">
		<p><a href="/">홈</a> &gt; 할인 관리</p>

		<div id="tit01">
			<h2>할인 관리</h2>
		</div>

		<div id="bbsWrite">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
					<tr>
						<td class="lpd20">사용 여부</td>
						<td colspan="2"><input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 미사용
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> </td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="lpd20">제목</td>
						<td colspan="2"><input type="text" class="box01" name="title" value="<?=$rs_title?>" style="width: 60%;" /></td>
					</tr>
					<tr>
						<td class="lpd20">기간</td>
						<td colspan="2">
						<input type="text" class="box01" name="dc_from" value="<?=$rs_dc_from?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.dc_from', document.frm.dc_from.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
						~
						<input type="text" class="box01" name="dc_to" value="<?=$rs_dc_to?>" style="width: 65px;" readonly="1">
						<a href="javascript:show_calendar('document.frm.dc_to', document.frm.dc_to.value);"><img src="../images/calendar/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
						</td>
					</tr>
					<tr>
						<td class="lpd20">할인율</td>
						<td colspan="2">
							<?= makeSelectBox($conn,"DC_RATE","dc_rate","100","선택하세요","",$rs_dc_rate)?>
						</td>
					</tr>
					<tr>
						<td class="lpd20">회원 할인율</td>
						<td colspan="2">
							<?= makeSelectBox($conn,"DC_RATE","dc_rate_member","100","선택하세요","",$rs_dc_rate_member)?>
						</td>
					</tr>
					<tr>
						<td class="lpd20">직원 할인율</td>
						<td colspan="2">
							<?= makeSelectBox($conn,"DC_RATE","dc_rate_emp","100","선택하세요","",$rs_dc_rate_emp)?>
						</td>
					</tr>
					<tr>
						<td class="lpd20">메모</td>
						<td colspan="2">
							<textarea name="memo" class="box01" style="width:600px; height:150px;"></textarea>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
			<span class="btn_list">
				<? if ($bb_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a> 
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/common/btn/btn_save.gif" alt="확인" /></a> 
					<? } ?>
				<? }?>
				 
				<a href="javascript:js_list();"><img src="../images/common/btn/btn_list.gif" alt="목록" /></a>
				
				<? if ($sPageRight_D == "Y") {?>
				<a href="javascript:js_delete();"><img src="../images/common/btn/btn_delete.gif" alt="삭제" /></a>
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