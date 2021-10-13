<?session_start();?>
<?
# =============================================================================
# File Name    : main.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @기린그림 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
//	include "$_SERVER[DOCUMENT_ROOT]/common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../_common/config.php";
	require "../_classes/com/util/Util.php";
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/admin/admin.php";


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="js/common.js"></script>
<style type="text/css">
	* {margin:0; padding:0}
	body {width:100%; height:100%; position:relative}
	img {vertical-align:top}

#list_wrap {float:left; position:absolute; top:100px; left:255px; padding:10px}
.list dl {float:left; width:160px; height:180px; margin-bottom:15px}
.list dl dt {padding-bottom:10px}
.list dl dd {line-height:1.4em}
.list dl ul {padding-left:10px; padding-right:5px}

</style>
</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">

<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../_common/top_area.php";
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

	require "../_common/left_area.php";
?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2> 관리자 페이지 메인</h2>
					<div id="list_wrap">
						<div class="list">

<?
	if (sizeof($arr_rs_menu) > 0) {
		for ($m = 0 ; $m < sizeof($arr_rs_menu); $m++) {
			
			$M_MENU_CD		= trim($arr_rs_menu[$m]["MENU_CD"]);
			$M_MENU_NAME	= trim($arr_rs_menu[$m]["MENU_NAME"]);
			$M_MENU_URL		= trim($arr_rs_menu[$m]["MENU_URL"]);

			if (strlen($M_MENU_CD) == "2") {
				if ($m <> 0) {
?>
										</ul>
									</dd>
								</dl>
<?
				}
?>
								<dl>
									<dt><a href="<?=$M_MENU_URL?>"><img src="/manager/images/list_icon_folder.gif" alt="폴더" /> <strong><?=$M_MENU_NAME?></strong></a></dt>
									<dd>
										<ul>
<?
			}
			if (strlen($M_MENU_CD) == "4") {
?>
											<li><a href="<?=$M_MENU_URL?>"><img src="/manager/images/list_icon_data.gif" alt="데이터" /> <?=$M_MENU_NAME?></a></li>
<?
			}
		}
	}
?>
										</ul>
									</dd>
								</dl>
						</div><!--//list-->
					</div><!--//list_wrap-->
			</div>
		</td>
	</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>