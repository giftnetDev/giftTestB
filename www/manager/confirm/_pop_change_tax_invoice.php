<?session_start();?>
<?

#=========================================================================
# 발주서에서 매입 비용 추가 - 발주서에서 추가하는걸로 변경으로 사용안함 (2017-05-12)
#=========================================================================

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
# Request Parameter
#====================================================================

	$mode			= trim($mode);
	$prev_cf_code	= trim($prev_cf_code);
	$next_cf_code	= trim($next_cf_code);
	
	$result	= false  ;


#====================================================================
# DML Process
#====================================================================
	if ($mode == "C") {
		
		$result	= changeCashStatementCFCode($conn, $prev_cf_code, $next_cf_code, $s_adm_no);

		if($result) { 
?>
<script type="text/javascript">
	
	alert('변경했습니다.');
	window.opener.js_search();
	self.close();

</script>
<?
		}
		mysql_close($conn);
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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		
		if(frm.prev_cf_code.value == "") { 
			alert('이전 승인번호를 입력해주세요.');
			frm.prev_cf_code.focus();
		}
		
		if(frm.next_cf_code.value == "") { 
			alert('변경할 승인번호를 입력해주세요.');
			frm.next_cf_code.focus();
		}

		frm.mode.value = "C";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value=""/>
<div id="popupwrap_file">
	<h1>세금 계산서 승인번호 이전</h1>
	
	<div id="postsch">
		<div class="addr_inp">
			
			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="25%" />
					<col width="75%" />
				</colgroup>
				
				<tr>
					<th>이전 승인번호</th>
					<td colspan="3" class="line">
						<input type="text" name="prev_cf_code" value="<?=$prev_cf_code?>" class="txt">
					</td>
				</tr>
				<tr>
					<th>변경 승인번호</th>
					<td class="line">
						<input type="text" name="next_cf_code" value="<?=$next_cf_code?>" class="txt">
					</td>
				</tr>	
			</table>
			<br/>
			<span>1. 계산서가 재발행 될때 사용하며 원장에 매칭된 승인번호(계산서)를 모두 새로 발행한 승인번호(계산서)로 바꿀때 사용합니다.<br/>
				  2. 계산서 원본의 승인번호는 변경되지 않습니다.</span>
			<br/>
		</div>
		<div class="btn">
			<? if ($sPageRight_I == "Y") {?>
				<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<? } ?>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
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