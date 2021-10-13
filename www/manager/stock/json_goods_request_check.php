<?php

# =============================================================================
# File Name    : json_goods_request_check.php
# Modlue       : 
# Writer       : KBJ
# Create Date  : 20210520
# Modify Date  : 
# Memo		   : 발주관리 화면 발송일자 클릭 시 체크 확인
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";

	function Update_Check($db, $reg_adm, $check_yn ,$req_no)
	{
		$query="UPDATE TBL_GOODS_REQUEST 
				   SET CHECK_YN 	= '$check_yn'
				   	 , CHECK_DATE 	= NOW() 
				   	 , CHECK_ADM 	= '$reg_adm'
				 WHERE REQ_NO 		= '$req_no'
				";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	/**************************************************************************************************************************************************************/

	if($mode == "GODDS_REQUEST_CONFIRM")
	{
		if(Update_Check($conn,$reg_adm,$check_yn,$req_no) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}
	}
	
?>

