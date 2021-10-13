<?session_start();?>
<?
# =============================================================================
# File Name    : pop_menu_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.12.10
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
	require "../../_classes/biz/admin/admin.php";


#====================================================================
# Request Parameter
#====================================================================

	$group_no		= trim($group_no);

	$error_flag = "0";

	$result = deleteAdminGroupMenuRight($conn, $group_no);


	$row_cnt = count($menu_cd);

	for ($k = 0; $k < $row_cnt; $k++) {
		
		$temp_menu_cd		=  trim($menu_cd[$k]);
		$temp_read_chk	=  trim($read_chk[$k]);
		$temp_reg_chk		=  trim($reg_chk[$k]);
		$temp_upd_chk		=  trim($upd_chk[$k]);
		$temp_del_chk		=  trim($del_chk[$k]);
		$temp_file_chk	=  trim($file_chk[$k]);

		If	(($temp_read_chk == "Y") ||
				 ($temp_reg_chk == "Y") ||
				 ($temp_upd_chk == "Y") ||
				 ($temp_del_chk == "Y") ||
				 ($temp_file_chk == "Y")) {
		
			$result = insertAdminGroupMenuRight($conn, $group_no, $temp_menu_cd, $temp_read_chk, $temp_reg_chk, $temp_upd_chk, $temp_del_chk, $temp_file_chk);
		}
	}

#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=<?=$g_charset?>'>
<title><?=$g_title?></title>
<script type="text/javascript">
<!--
	function init() {
		alert("저장 되었습니다."); //저장 되었습니다.
		document.frm.submit();
	}
//-->
</script>

</head>
<!--<body>-->
<body onLoad="init();">
<form name="frm" action="pop_menu_list.php" method="post">
<input type="hidden" name="group_no" value="<?=$group_no?>">
</form>
</body>
</html>