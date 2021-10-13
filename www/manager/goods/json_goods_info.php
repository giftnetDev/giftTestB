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

	function selectGoodsColumnInfo($db, $column, $goods_no) {

		$query = "SELECT $column
					FROM TBL_GOODS
					WHERE GOODS_NO = '$goods_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	$goods_no = $_REQUEST['goods_no'];
	$mode = $_REQUEST['mode'];

	if($mode == "DELIVERY_CNT_IN_BOX")
	{
		$arr = selectGoodsColumnInfo($conn, $mode, $goods_no);
		if(sizeof($arr) > 0) { 
			$value = $arr[0][$mode];

			$results = "[{\"RESULT\":\"".$value."\"}]";
			echo $results;
		}
	}

?>