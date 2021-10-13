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

	function updateGoodsCategorySeq($db, $goods_cate, $goods_no, $seq) {

		$query = " UPDATE TBL_GOODS_CATEGORY 
		              SET SEQ = '$seq'
					WHERE GOODS_CATE = '$goods_cate' AND GOODS_NO = '$goods_no'
			     ";
		//echo $query."<br/>";
		return mysql_query($query,$db);
	}


	$sub_seq_no = $_REQUEST['sub_key'];
	$mode = $_REQUEST['mode'];

	if ($mode == "O") {
		
		$row_cnt = count($sub_seq_no);

		//echo "º¯°æ".$row_cnt;
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$seq_no = $sub_seq_no[$k];

			$arr = explode("|", $seq_no);
			$goods_cate = $arr[0];
			$goods_no = $arr[1];


	
			$result = updateGoodsCategorySeq($conn, $goods_cate, $goods_no, $k);
		
		}

	}


?>