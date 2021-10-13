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

	
	function updateGoodsReference($db, $ref_no, $field, $value) {

		$query = "UPDATE TBL_GOODS_REFERENCE 
					 SET $field = '$value'
			       WHERE REF_NO = '$ref_no'";
					
				
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else 
			return true;
	}
	

	$ref_no = $_REQUEST['ref_no'];
	$value = trim(urldecode (iconv("UTF-8", "EUC-KR", $_REQUEST['value'])));
	$field = $_REQUEST['field'];

	if($ref_no != "" && $field != "")
	{
		$results = "[{\"RESULT\":\"".updateGoodsReference($conn, $ref_no, $field, $value)."\"}]";
		echo $results;

	}

?>