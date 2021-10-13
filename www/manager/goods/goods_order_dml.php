<?session_start();?>
<?
# =============================================================================
# File Name    : goods_order_dml.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/goods/goods.php";
	
#====================================================================
# DML Process
#====================================================================
	

	if ($mode == "O") {
		
		$row_cnt = count($goods_seq_no);

		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_goods_no = $goods_seq_no[$k];

			$result = updateOrderGoods($conn, $k, $tmp_goods_no);
		
		}
	}


?>
