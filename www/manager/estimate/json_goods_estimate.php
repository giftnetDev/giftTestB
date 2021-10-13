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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/estimate/estimate.php";
	require "../../_classes/biz/company/company.php";


	function updateGenericGoodsEstimate($db, $column, $value, $gp_no) { 

		$query="     UPDATE TBL_GOODS_ESTIMATE ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE GP_NO = '$gp_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateGenericGoodsEstimateGoods($db, $column, $value, $gpg_no) { 

		$query="     UPDATE TBL_ESTIMATE_SUB ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE GPG_NO = '$gpg_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	
	if($mode == "UPDATE_ESTIMATE_GOODS")
	{
		if($gpg_no != "" || $gp_no != "") {

			if($req_goods_no == "")
				$result = updateGenericGoodsEstimate($conn, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value), $gp_no);

			if($req_no == "")
				$result = updateGenericGoodsEstimateGoods($conn, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value), $gpg_no);
			
		}

		echo "[{\"RESULT\":\"".$result."\"}]";

	}


	
?>

