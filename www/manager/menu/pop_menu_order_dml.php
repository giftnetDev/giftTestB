<?session_start();?>
<?
# =============================================================================
# File Name    : pop_menu_order.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.12.07
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");


#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#==============================================================================
# Confirm right
#==============================================================================

	$sPageRight_		= "Y";
	$sPageRight_R		= "Y";
	$sPageRight_I		= "Y";
	$sPageRight_U		= "Y";
	$sPageRight_D		= "Y";
	$sPageRight_F		= "Y";


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/menu/menu.php";


#====================================================================
# Request Parameter
#====================================================================

	# System Parameter
	$m_level = Trim($m_level);

	$i = 1;

	$error_flag = "0";

	$row_cnt = count($catid);

	for ($k = 0; $k < $row_cnt; $k++) {

		if (strlen($m_level) == 0) {
			$menu_level = "MENU_SEQ01";
		}

		if (strlen($m_level) == 2) {
			$menu_level = "MENU_SEQ02";
		}

		if (strlen($m_level) == 4) {
			$menu_level = "MENU_SEQ03";
		}

		$str_seq = "0".$i;
		$str_seq = substr($str_seq, -2);


		$temp_menu_no =  $arr_menu_no[$k];

		$temp_menu_no = "(" . str_replace("^",",", $temp_menu_no) . ")";

		#echo $temp_menu_no."<br>";

		$result = updateAdminMenuOrder($conn, $temp_menu_no, $menu_level, $str_seq);
		
		$i++;
#		'response.write arr_menu_no & "<br>"

#		.ActiveConnection = objDbCon
#		.CommandType = adCmdStoredProc
#		.CommandText = "AUpd_Menu_Order"
#		.Parameters.Append .CreateParameter("RETURN_VALUE"		,adInteger	,adParamReturnValue)
#		.Parameters.Append .CreateParameter("@sMenu_no"				,adVarChar	,adParamInput	,100	,arr_menu_no)
#		.Parameters.Append .CreateParameter("@sLevel"					,adVarChar	,adParamInput	,15		,s_level)
#		.Parameters.Append .CreateParameter("@sSeq"						,adVarChar	,adParamInput	,2		,str_seq)
	
	}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=<?=$g_charset?>'>
<title><?=$g_title?></title>
<script type="text/javascript">
<!--
	function init() {
		parent.opener.document.location = "menu_list.php"
	}
//-->
</script>

</head>
<!--<body>-->
<body onLoad="init();">
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>