<?session_start();?>
<?
# =============================================================================
# File Name    : dcode_order_dml.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SY002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";
	
#====================================================================
# common_header
#====================================================================
	require "../../_common/common_header.php"; 
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/syscode/syscode.php";
	

	//$arr_rs = getSiteInfo($conn, $site_no);
	

#====================================================================
# DML Process
#====================================================================
	
	//echo "변경";

	if ($mode == "O") {
		
		$row_cnt = count($dcode_seq_no);

		//echo "변경".$row_cnt;
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_dcode_no = $dcode_seq_no[$k];

			$result = updateOrderDcode($conn, $k, $pcode, $tmp_dcode_no);
		
		}
	}


?>
