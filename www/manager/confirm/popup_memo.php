<?session_start();?>
<?
# =============================================================================
# File Name    : popup_memo.php
# 매출 미수 보고 > 비고
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF012"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
#====================================================================
# DML Process
#====================================================================
	
	if ($mode == "U") {
		//$result = updateCompanyMemo2($conn, $memo2, $cp_no);
		$prev_0 = trim(str_replace(",", "", $prev_0));
		$prev_1 = trim(str_replace(",", "", $prev_1));
		$prev_2 = trim(str_replace(",", "", $prev_2));
		$prev_3 = trim(str_replace(",", "", $prev_3));
		$result = updateAccountReceivableReport($conn, $cp_no, $memo, $prev_0, $prev_1, $prev_2, $prev_3, $except_tf, $s_adm_no);
		mysql_close($conn);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<script>
	alert('수정 되었습니다');
	opener.js_search();
	self.close();
</script>
</head>
</html>
<?
		exit;
	}

	//$arr_rs = selectCompanyMemo2($conn, $cp_no);

	$arr_rs = selectAccountReceivableReport($conn, $cp_no);
	
	if(sizeof($arr_rs) > 0) { 
		$MEMO		= trim($arr_rs[0]["MEMO"]);
		$PREV_0		= trim($arr_rs[0]["PREV_0"]);
		$PREV_1		= trim($arr_rs[0]["PREV_1"]);
		$PREV_2		= trim($arr_rs[0]["PREV_2"]);
		$PREV_3		= trim($arr_rs[0]["PREV_3"]);
		$PREV_4		= trim($arr_rs[0]["PREV_4"]);
		$EXCEPT_TF	= trim($arr_rs[0]["EXCEPT_TF"]);
		$UP_ADM		= trim($arr_rs[0]["UP_ADM"]);
		$UP_DATE	= trim($arr_rs[0]["UP_DATE"]);
	}

	if($PREV_4 == "")
		$PREV_4 = "...";

	if($UP_ADM == "")
		$UP_ADM = "...";
		
	if($UP_DATE == "")
		$UP_DATE = "...";
	else
		$UP_DATE = date("Y-m-d H:i:s",strtotime($UP_DATE));

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

<script type="text/javascript">

	function js_save() {
		var frm = document.frm;

		frm.mode.value = "U";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

</script>

</head>
<body id="popup_file">

<form name="frm" method="post">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
<input type="hidden" name="mode" value="">
<div id="popupwrap_file">
	<h1>미수 메모</h1>
	<div id="postsch_code">
		
		<div class="addr_inp">
		<table cellpadding="0" cellspacing="0" width="98%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
						<colgroup>
							<col width="20%">
							<col width="80%">
						</colgroup>
						<tr>
							<th><?=date("n월",strtotime("0 month"))?></th>
							<td>
								<input type="text" name="prev_0" value="<?=$PREV_0?>"/> 원
							</td>
						</tr>
						<tr>
							<th><?=date("n월",strtotime("first day of -1 month"))?></th>
							<td>
								<input type="text" name="prev_1" value="<?=$PREV_1?>"/> 원
							</td>
						</tr>
						<tr>
							<th><?=date("n월",strtotime("first day of -2 month"))?></th>
							<td>
								<input type="text" name="prev_2" value="<?=$PREV_2?>"/> 원
							</td>
						</tr>
						<tr>
							<th><?=date("n월",strtotime("first day of -3 month"))?></th>
							<td>
								<input type="text" name="prev_3" value="<?=$PREV_3?>"/> 원
							</td>
						</tr>
						<!--
						<tr>
							<th><?=date("n월",strtotime("first day of -4 month"))?></th>
							<td>
								<?=$PREV_4?> 원
							</td>
						</tr>
						-->
					</table>
					<h2>* 미수 메모 입니다. 등록을 클릭하면 해당 내용이 저장 됩니다.</h2>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="colstable">
						<colgroup>
							<col width="20%">
							<col width="80%">
						</colgroup>
							<tr>
								<td colspan="2" class="lpd20 rpd20 right" style="border:none;">
									<textarea name="memo" class="txt" style="width:100%" rows="15"><?=$MEMO ?></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<label style="color:red;"><input type="checkbox" name="except_tf" value="Y" <?if($EXCEPT_TF == "Y") echo "checked";?>/> 미수금 계산 제외</label>
								</td>
							</tr>

					</table>
				</td>
			</tr>
		</table>
		<br/>
		최종 수정자 : <?=getAdminName($conn, $UP_ADM)?>, 최종 수정일 : <?=$UP_DATE?>
		<div class="btn">
			<a href="javascript:js_save();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
		</div>
	</div>
	<div class="sp20"></div>
	</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>