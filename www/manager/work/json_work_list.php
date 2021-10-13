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
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";

/*
	function chkWorkList($db, $work_date, $arr_work_type, $arr_work_line) {

		$arr_work = explode("|",$arr_work_type);

		$work_type1 = $arr_work[0];
		$work_type2 = $arr_work[1];
		$work_type3 = $arr_work[2];
		$work_type4 = $arr_work[3];
		$work_type5 = $arr_work[4];

		$arr_line = explode("|",$arr_work_line);

		$work_lineA = $arr_line[0];
		$work_lineB = $arr_line[1];
		$work_lineC = $arr_line[2];

		// 조건식 만들기
		$query_condition = "";

		if ($work_type1 == "Y") {
			if ($query_condition == "") {
				$query_condition = " OW.WORK_TYPE = 'INCASE' ";
			} else {
				$query_condition = $query_condition." OR OW.WORK_TYPE = 'INCASE' ";
			}
		}

		if ($work_type2 == "Y") {
			if ($query_condition == "") {
				$query_condition = " OW.WORK_TYPE = 'WRAP' ";
			} else {
				$query_condition = $query_condition." OR OW.WORK_TYPE = 'WRAP' ";
			}
		}

		if ($work_type3 == "Y") {
			if ($query_condition == "") {
				$query_condition = " OW.WORK_TYPE = 'STICKER' ";
			} else {
				$query_condition = $query_condition." OR OW.WORK_TYPE = 'STICKER' ";
			}
		}

		if ($work_type4 == "Y") {
			if ($query_condition == "") {
				$query_condition = " OW.WORK_TYPE = 'OUTCASE' ";
			} else {
				$query_condition = $query_condition." OR OW.WORK_TYPE = 'OUTCASE' ";
			}
		}

		if ($work_type5 == "Y") {
			if ($query_condition == "") {
				$query_condition = " OW.WORK_TYPE = 'OUTSTICKER' ";
			} else {
				$query_condition = $query_condition." OR OW.WORK_TYPE = 'OUTSTICKER' ";
			}
		}

		// 조건식 만들기
		$query_condition2 = "";

		if ($work_lineA == "Y") {
			if ($query_condition2 == "") {
				$query_condition2 = " OW.WORK_LINE = 'A' ";
			} else {
				$query_condition2 = $query_condition2." OR OW.WORK_LINE = 'A' ";
			}
		}

		if ($work_lineB == "Y") {
			if ($query_condition2 == "") {
				$query_condition2 = " OW.WORK_LINE = 'B' ";
			} else {
				$query_condition2 = $query_condition2." OR OW.WORK_LINE = 'B' ";
			}
		}

		if ($work_lineC == "Y") {
			if ($query_condition2 == "") {
				$query_condition2 = " OW.WORK_LINE = 'C' ";
			} else {
				$query_condition2 = $query_condition2." OR OW.WORK_LINE = 'C' ";
			}
		}

		$query = "SELECT DISTINCT OG.ORDER_GOODS_NO, OG.WORK_QTY
								FROM TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW  
							 WHERE OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND OG.ORDER_STATE = '2' 
								 AND OG.WORK_FLAG = 'N' 
								 AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
								 ";

		if ($work_date <> "") {
			$query .= " AND OG.WORK_START_DATE <= '".$work_date."' ";
		}

		$query .= " AND OG.USE_TF = 'Y' ";
		$query .= " AND OG.DEL_TF = 'N' ";

		if ($query_condition <> "") {
			$query .= " AND (".$query_condition.") ";
		} else {
			$query .= " AND OW.WORK_TYPE = 'XXXX' ";
		}

		if ($query_condition2 <> "") {
			$query .= " AND (".$query_condition2.") ";
		} else {
			$query .= " AND OW.WORK_LINE = 'XXXX' ";
		}

		$order_field = "WORK_START_DATE ASC, WORK_SEQ ASC, OG.OPT_OUTSTOCK_DATE DESC";
		$query .= " ORDER BY ".$order_field;

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
*/

/*
	if($mode == "CHECK_ORDER_GOODS_WORK")
	{
		$work_date = iconv('utf-8', 'euc-kr', $work_date);
		$arr_work_type = iconv('utf-8', 'euc-kr', $arr_work_type);
		$arr_work_line = iconv('utf-8', 'euc-kr', $arr_work_line);

		$arr_rs = chkWorkList($conn, $work_date, $arr_work_type, $arr_work_line);

		$results = "[";
		for($i = 0; $i < sizeof($arr_rs); $i++)
		{
			$rs_order_goods_no = $arr_rs[$i]['ORDER_GOODS_NO'];
			$rs_work_qty = $arr_rs[$i]['WORK_QTY'];

			$results .= "{\"ORDER_GOODS_NO\":\"".$rs_order_goods_no."\",\"WORK_QTY\":\"".$rs_work_qty."\"},";
		}
		
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	}
*/
/*
	if($mode == "CHECK_ORDER_GOODS_WORK_SEQ")
	{
		$order_goods_no = iconv('utf-8', 'euc-kr', $order_goods_no);
		$work_seq		= iconv('utf-8', 'euc-kr', $work_seq);

		$arr_rs = selectOrderWorkInfo($conn, $order_goods_no);

		if(sizeof($arr_rs) > 0) { 

			$WORK_SEQ = $arr_rs[0]['WORK_SEQ'];

			//echo $work_seq." // ".$WORK_SEQ;
			//DB순번과 외부 순번이 일치하는지 여부 체크 - 같은지는 중요하지 않고 순번이 0인지 아닌지만 중요
			if($WORK_SEQ == $work_seq)
				$result = "Y";
			else 
				$result = "N";

		} else 
			$result = "ERROR";

		$results = "[{\"RESULT\":\"".$result."\"}]";

		echo $results;

	}
*/

	if($mode == "WORK_DONE")
	{
		//echo $order_goods_no." ".$selected_qty." ".$work_done_adm." <br/>";
		$result = updateWorksDone($conn, $mode, $order_goods_no, $selected_qty, $work_done_adm);

		$results = "[{\"RESULT\":\"".$result."\"}]";

		echo $results;
	}

	if($mode == "WORK_SENT")
	{
		//echo $order_goods_no." ".$selected_qty." ".$work_done_adm." <br/>";
		$result = updateWorksDone($conn, $mode, $order_goods_no, $selected_qty, $work_done_adm);

		$results = "[{\"RESULT\":\"".$result."\"}]";

		echo $results;
	}
?>

