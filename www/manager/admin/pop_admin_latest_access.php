<?session_start();?>
<?

if ($s_adm_cp_type <> "운영") {
	$next_url = "admin_write.php?mode=S&adm_no=$s_adm_no";
?>
<meta http-equiv='Refresh' content='0; URL=<?=$next_url?>'>
<?
	exit;
}

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
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/admin/admin.php";

	$adm_id = trim($adm_id);

	function listUserLog($db, $adm_id, $days) {


		$query = "SELECT LOGIN_DATE, LOG_IP 
					FROM  TBL_USER_LOG 
					WHERE USER_TYPE IN ('ad', 'AO')
					  AND LOG_ID =  '".$adm_id."'
					  AND LOGIN_DATE >= ( CURDATE() - INTERVAL ".$days." DAY )
					ORDER BY LOGIN_DATE DESC  ";
		

		#echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


#===============================================================
# Get Search list count
#===============================================================

	$days = 5;
	$arr_rs = listUserLog($conn, $adm_id, $days);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
</head>
<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>최근 5일 접속 기록</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">

	<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
	<colgroup>
		<col width="50%" />
		<col width="*" />
	</colgroup>
	<thead>
		<tr>
			<th>접속시간</th>
			<th class="end">접속IP</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

		//LOGIN_DATE, LOG_IP 

		$LOGIN_DATE			= trim($arr_rs[$i]["LOGIN_DATE"]);
		$LOG_IP			    = trim($arr_rs[$i]["LOG_IP"]); 


	?>

		<tr height="35">
			<td><?=$LOGIN_DATE?></td>
			<td><?=$LOG_IP?></td>
		</tr>
	
	<?
		}
	} else {

	?>
		<tr>
			<td colspan="2" height="50" align="center">데이터가 없습니다</td>
		</tr>
	<?

	}
	
	?>
	
	</tbody>
	</table>
	
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>