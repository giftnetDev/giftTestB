<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	include "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");


	$query ="SELECT ORDER_GOODS_NO, RESERVE_NO, ORDER_SEQ, ORDER_STATE FROM TBL_ORDER_GOODS WHERE ORDER_STATE = '8' ; ";

	$result = mysql_query($query,$conn);
	$total  = mysql_affected_rows();

	for($i=0 ; $i< $total ; $i++) {
		mysql_data_seek($result,$i);
		$row     = mysql_fetch_array($result);
			
		$ORDER_GOODS_NO		= Trim($row[0]);
		$RESERVE_NO				= Trim($row[1]);
		$ORDER_SEQ				= Trim($row[2]);
		$ORDER_STATE			= Trim($row[3]);
		$PLUS_ORDER_SEQ		= $ORDER_SEQ +1;

		echo $ORDER_GOODS_NO." ".$RESERVE_NO." ".$ORDER_STATE." ".$ORDER_SEQ."<br>";
		
		$query ="SELECT ORDER_GOODS_NO, RESERVE_NO, ORDER_SEQ, ORDER_STATE, CATE_04 FROM TBL_ORDER_GOODS 
							WHERE RESERVE_NO = '$RESERVE_NO' AND ORDER_SEQ = '$PLUS_ORDER_SEQ' ; ";
		
		$result_sub = mysql_query($query,$conn);
		$total_sub  = mysql_affected_rows();

		for($i_sub=0 ; $i_sub< $total_sub ; $i_sub++) {
			mysql_data_seek($result_sub,$i_sub);
			$row_sub     = mysql_fetch_array($result_sub);
			
			$ORDER_GOODS_NO		= Trim($row_sub[0]);
			$RESERVE_NO				= Trim($row_sub[1]);
			$ORDER_SEQ				= Trim($row_sub[2]);
			$ORDER_STATE			= Trim($row_sub[3]);
			$CATE_04					= Trim($row_sub[4]);

			echo $ORDER_GOODS_NO." ".$RESERVE_NO." ".$ORDER_STATE." ".$ORDER_SEQ." ".$CATE_04."<br><br>";


			$query_update = "UPDATE TBL_ORDER_GOODS SET CATE_04 = 'CHANGE' 
														WHERE ORDER_GOODS_NO = '$ORDER_GOODS_NO' ";
			//mysql_query($query_update,$conn);

		}

		//echo $query_update."<br>";

	}


#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
