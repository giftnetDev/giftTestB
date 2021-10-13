<?session_start();?>
<?
# =============================================================================
# File Name    : popup_work_goods.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2015-11-04
# Modify Date  : 
#	Copyright : Copyright @giftnet Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "WO004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";

#====================================================================
# DML Process
#====================================================================
	
	$arr_rs = getOrderGoodsOutcaseImageList($conn, $goods_no);

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


</script>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

<div id="popupwrap_work">
	<h1>아웃 박스</h1>
	<br>
	<div id="postsch_code">
		<table cellpadding="0" cellspacing="0" width="98%">
			<colgroup>
			<col width="100%" />
			</colgroup>
			<tr>
				<td style="text-align:center">
			<?
				if (sizeof($arr_rs) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
						$FILE_NM1						= trim($arr_rs[$j]["FILE_NM1"]);
			?>
					<img src="/upload_data/goods/<?=$FILE_NM1?>" width="300">
			<?
					}
				}
			?>
				</td>
			</tr>
		</table>
	</div>
	<div class="sp20"></div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>