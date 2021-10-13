<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/com/util/ImgUtil.php";


	function updateCompanyExtra($db, $ce_no, $column, $value) { 

		$query="     UPDATE TBL_COMPANY_EXTRA ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE CE_NO = '$ce_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	if($mode == "UPDATE_COMPANY_EXTRA")
	{
		$result = updateCompanyExtra($conn, $ce_no, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value));

		echo "[{\"RESULT\":\"".$result."\"}]";

	}
	
?>

