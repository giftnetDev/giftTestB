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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/biz/goods/goods.php";

	function insertGoodsRequestWarehouse($db, $goods_no) { 
		
		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_REQUEST_WAREHOUSE  
				   WHERE REQ_DATE = CURDATE() AND GOODS_NO = '$goods_no' AND DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] > 0) return false;

		$ip_address = $_SERVER['REMOTE_ADDR'];
		$query="INSERT INTO TBL_GOODS_REQUEST_WAREHOUSE (REQ_DATE, GOODS_NO, IP_ADDRESS, REG_DATE) 
													 values (CURDATE(), '$goods_no', '$ip_address', now()); ";
		
		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else { 
			return true;
		}

	}

	function cancelGoodsRequestWarehouse($db, $goods_no) { 
		
		$ip_address = $_SERVER['REMOTE_ADDR'];

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_REQUEST_WAREHOUSE  
				   WHERE GOODS_NO = '$goods_no' AND IP_ADDRESS = '$ip_address' AND DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] <= 0) return false;

		$query="UPDATE TBL_GOODS_REQUEST_WAREHOUSE 
				   SET DEL_DATE = now(), DEL_IP_ADDRESS = '$ip_address', DEL_TF = 'Y'
				 WHERE GOODS_NO = '$goods_no' AND IP_ADDRESS = '$ip_address' AND DEL_TF = 'N' ";
		
		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else { 
			return true;
		}

	}

	//상품 번호로 검색
	$goods_no = $_REQUEST['goods_no'];

	if($goods_no != "")
	{

		if($mode == "insert") { 

			$result = insertGoodsRequestWarehouse($conn, $goods_no);

			$results = "[{\"RESULT\":\"".$result."\",\"GOODS_NO\":\"".$goods_no."\"}]";

			echo $results;

		} else if($mode == "cancel") { 

			$result = cancelGoodsRequestWarehouse($conn, $goods_no);

			$results = "[{\"RESULT\":\"".$result."\",\"GOODS_NO\":\"".$goods_no."\"}]";

			echo $results;

		}

	}

?>

