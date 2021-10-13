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
	$mode	= trim($mode);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	$mode			= trim($mode);
	$cl_no			= trim($cl_no);

	$strParam = "?start_date=".$start_date."&end_date=".$end_date."&cp_type=".$cp_type;
	
	$inout_date				= SetStringToDB($inout_date);
	$inout_type				= SetStringToDB($inout_type);
	$name					= SetStringToDB($name);
	$qty					= SetStringToDB($qty);
	$unit_price				= SetStringToDB($unit_price);
	$withdraw				= SetStringToDB($withdraw);
	$deposit				= SetStringToDB($deposit);
	$reserve_no				= SetStringToDB($reserve_no);
	$memo					= SetStringToDB($memo);
	
	$result	= false  ;


#====================================================================
# DML Process
#====================================================================
	if ($mode == "I") {
		
		$result	= insertCompanyLedger($conn, $cp_no, $inout_date, $inout_type, null, $name, $qty, $unit_price, null, $surtax, $memo, null, null, null, $s_adm_no);

		if($result) { 
?>
<script type="text/javascript">
	
	alert('저장했습니다.');
	window.opener.js_search();
	self.close();

</script>
<?
		}
		mysql_close($conn);
		exit;
	}

	if ($mode == "APPEND") {

		$arr_rs = selectCompanyLedger($conn, $cl_no);

		$RS_CP_NO								= trim($arr_rs[0]["CP_NO"]); 
		$RS_INOUT_DATE							= SetStringFromDB($arr_rs[0]["INOUT_DATE"]); 
		$RS_INOUT_TYPE							= SetStringFromDB($arr_rs[0]["INOUT_TYPE"]); 

		$RS_NAME								= ""; 
		$RS_QTY									= "";
		$RS_UNIT_PRICE							= "";
		$RS_WITHDRAW							= "";
		$RS_DEPOSIT								= "";
		
		$RS_MEMO								= SetStringFromDB($arr_rs[0]["MEMO"]); 
		$RS_RESERVE_NO							= SetStringFromDB($arr_rs[0]["RESERVE_NO"]); 

		$RS_INOUT_DATE			= date("Y-m-d",strtotime($RS_INOUT_DATE));
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
		
		var cl_no = "<?= $cl_no ?>";
		var frm = document.frm;
		
		if(frm.mode.value == "APPEND")  
			frm.mode.value = "I";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}


</script>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

	<input type="hidden" name="mode" value="<? if($mode == "APPEND") { echo "APPEND"; } ?>">
	<input type="hidden" name="cl_no" value="<?=$cl_no?>">
	<input type="hidden" name="start_date" value="<?=$start_date?>">
	<input type="hidden" name="end_date" value="<?=$end_date?>">
	<input type="hidden" name="cp_type" value="<?=$cp_type?>">

	<input type="hidden" name="cp_no" value="<?=$RS_CP_NO?>">
	<input type="hidden" name="inout_type" value="매입"/>
	<input type="hidden" name="withdraw" value="<?= $RS_WITHDRAW?>" >
	<input type="hidden" name="deposit" value="<?= $RS_DEPOSIT?>" >
	<input type="hidden" name="inout_date" value="<?= $RS_INOUT_DATE?>" >
	<input type="hidden" name="reserve_no" value="<?= $RS_RESERVE_NO?>" >

<div id="popupwrap_file">
	<h1>매입 추가</h1>
	<div id="postsch">
		<div class="addr_inp">

			<table cellpadding="0" cellspacing="0" class="colstable02">

				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="10%" />
					<col width="35%" />
				</colgroup>
				
				<tr>
					<th>기장명</th>
					<td colspan="3" class="line">
						<input type="text" name="name" value="" style="width:100%;" class="txt">
					</td>
				</tr>
				<tr>
					<th>수량</th>
					<td class="line">
						<input type="text" name="qty" value="" onkeyup="return isNumber(this)" class="txt">
					</td>
					<th>단가</th>
					<td class="line">
						<input type="text" name="unit_price" value="" onkeyup="return isNumber(this)" class="txt">
					</td>
				</tr>	
				<tr>
					<th>비고</th>
					<td colspan="3" class="line">
						<input type="text" name="memo" value="<?=$RS_MEMO?>" style="width:100%;" class="txt">
					</td>
				</tr>
			</table>
				
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