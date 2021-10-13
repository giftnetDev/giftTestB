<?session_start();?>
<?
# =============================================================================
# File Name    : popup_company_ledger_memo.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF006"; // 메뉴마다 셋팅 해 주어야 합니다

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
		$result = updateCompanyLedgerMemo($conn, $memo, $cl_no);
		mysql_close($conn);

	//저장후 익일작업리스트 갱신하면 잡고있던 순번이 날아감으로 갱신안함
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

	$arr_rs = selectCompanyLedgerMemo($conn, $cl_no);
	
	$MEMO	= trim($arr_rs[0]["MEMO"]);

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
<input type="hidden" name="cl_no" value="<?=$cl_no?>">
<input type="hidden" name="mode" value="">
<div id="popupwrap_file">
	<h1>원장 메모</h1>
	<div id="postsch_code">
		<h2>* 원장 메모 입니다. 등록을 클릭하면 해당 내용이 저장 됩니다.</h2>
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
								<td colspan="3" class="lpd20 rpd20 right">
									<textarea name="memo" class="txt" style="width:100%" rows="15"><?=$MEMO ?></textarea>
								</td>
							</tr>
					</table>
				</td>
			</tr>
		</table>
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