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
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/goods/goods.php";


	function updateGenericGoodsRequest($db, $column, $value, $req_no) { 

		$query="     UPDATE TBL_GOODS_REQUEST ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE REQ_NO = '$req_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateGenericGoodsRequestGoods($db, $column, $value, $req_goods_no) { 

		$query="     UPDATE TBL_GOODS_REQUEST_GOODS ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE REQ_GOODS_NO = '$req_goods_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	/*
	function cancelGoodsRequestGoods($db, $req_goods_no, $cancel_tf, $cancel_adm) { 

		$query="     UPDATE TBL_GOODS_REQUEST_GOODS ";

		if($cancel_tf == 'N')
			$query .= " SET CANCEL_TF = 'N', CANCEL_DATE = '0000-00-00 00:00:00', CANCEL_ADM = '$cancel_adm'  ";
		else
			$query .= " SET CANCEL_TF = 'Y', CANCEL_DATE = now(), CANCEL_ADM = '$cancel_adm'  ";

		$query .= "   WHERE REQ_GOODS_NO = '$req_goods_no'";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	*/

	function updateGoodsRequestGoodsReceiver($db, $req_goods_no, $order_goods_no, $to_here) {

		if($to_here == "Y") {

			$arr_op_cp = getOperatingCompany($db, $op_cp_no);
			$RECEIVER_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$RECEIVER_ADDR = $arr_op_cp[0]["CP_ADDR"];
			$RECEIVER_PHONE = $arr_op_cp[0]["CP_PHONE"];
			$RECEIVER_HPHONE = $arr_op_cp[0]["CP_HPHONE"];

		} else {

			if($order_goods_no <> "") { 
				$query="SELECT R_MEM_NM, R_ADDR1, R_PHONE, R_HPHONE 
						  FROM TBL_ORDER  
						 WHERE RESERVE_NO IN (SELECT RESERVE_NO FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO = '$order_goods_no' ) ";

				//echo $query;
				//exit;

				$result = mysql_query($query,$db);
				$record = array();

				if ($result <> "") {
					for($i=0;$i < mysql_num_rows($result);$i++) {
						$record[$i] = sql_result_array($result,$i);
					}
				}

				$RECEIVER_NM = $record[0]["R_MEM_NM"];
				$RECEIVER_ADDR = $record[0]["R_ADDR1"];
				$RECEIVER_PHONE = $record[0]["R_PHONE"];
				$RECEIVER_HPHONE = $record[0]["R_HPHONE"];
			}
		}

		$query="UPDATE TBL_GOODS_REQUEST_GOODS 
				   SET TO_HERE = '$to_here', 
				       RECEIVER_NM = '$RECEIVER_NM', 
					   RECEIVER_ADDR = '$RECEIVER_ADDR',
					   RECEIVER_PHONE = '$RECEIVER_PHONE', 
					   RECEIVER_HPHONE = '$RECEIVER_HPHONE'
				 WHERE 
				       REQ_GOODS_NO = '$req_goods_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function undoStock($db, $stock_no, $prev_stock_no, $qty, $del_adm) { 

		deleteStock($db, $stock_no, $del_adm);

		$query = "SELECT STOCK_TYPE, GOODS_NO, IN_FQTY, DEL_TF, RGN_NO
					FROM TBL_STOCK 
				   WHERE STOCK_NO = '$prev_stock_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$STOCK_TYPE		    = $rows[0];
		$GOODS_NO			= $rows[1];
		$IN_FQTY			= $rows[2];
		$DEL_TF				= $rows[3];
		$RGN_NO				= $rows[4];


		if($DEL_TF == "Y")
		{
			$query = "	UPDATE TBL_STOCK
						SET DEL_TF = 'N', DEL_ADM = '$del_adm', DEL_DATE = now()
						WHERE STOCK_NO = '$prev_stock_no'
					 ";
			mysql_query($query,$db);
		}
			
		
		$query = "	UPDATE TBL_STOCK
					   SET IN_FQTY = IN_FQTY + $qty, MEMO = CONCAT(MEMO,' (입고 번복됨:', now(), ')')
					 WHERE STOCK_NO = '$prev_stock_no'
				 ";
		mysql_query($query,$db);
		
		$query = "  UPDATE TBL_GOODS
					   SET FSTOCK_CNT = FSTOCK_CNT + $qty
					 WHERE GOODS_NO = '$GOODS_NO' 
				 ";
		mysql_query($query,$db);


		//발주서의 받은 수량 취소
		if($RGN_NO <> "")
		echo "RGN_NO : $RGN_NO<br>";
		exit;
			updateGoodsRequestGoodsQty($conn, $RGN_NO);

		return true;

	}

	if($mode == "UPDATE_REQUEST_GOODS")
	{
		//echo "req_no".$req_no."<br/>";
		//echo "req_goods_no".$req_goods_no."<br/>";

		if($req_goods_no != "" || $req_no != "") {

			if($req_goods_no == "")
				$result = updateGenericGoodsRequest($conn, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value), $req_no);

			if($req_no == "")
				$result = updateGenericGoodsRequestGoods($conn, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value), $req_goods_no);
			
		}

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	if($mode == "UPDATE_REQUEST_GOODS_RECEIVER")
	{
		//echo "order_goods_no".$order_goods_no."<br/>";
		//echo "req_goods_no".$req_goods_no."<br/>";

		if($req_goods_no != "" || $order_goods_no != "") {

			$result = updateGoodsRequestGoodsReceiver($conn, $req_goods_no, $order_goods_no, $to_here);

			if($to_here == "Y") { 
				// (입력하는) 선출고 기능 삭제 20170321
				insertFStockByGoods($conn, $req_goods_no);
			
			} else { 
				// (입력하는) 선출고 기능 삭제 20170321
				deleteFStock($conn, $req_goods_no, $s_adm_no);

			}
		}

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	/*
	if($mode == "CANCEL_GOODS_REQUEST_GOODS")
	{
		if($req_goods_no != "" || $cancel_tf != "") {

			$result = cancelGoodsRequestGoods($db, $req_goods_no, $cancel_tf, $cancel_adm);
			
		}

		echo "[{\"RESULT\":\"".$result."\"}]";

	}
	*/

	if($mode == "FSTOCK_MOVE") 
	{ 
		$result = updateStatusFStock($conn, $stock_no, $input_qty, $input_bqty, iconv('utf-8', 'euc-kr', $memo), $del_adm);
		echo "[{\"RESULT\":\"".$result."\", \"STOCK_NO\":\"".$stock_no."\"}]";
	}

	if($mode == "FSTOCK_DELETE")
	{
		$result = deleteStock($conn, $stock_no, $del_adm);
		
		echo "[{\"RESULT\":\"".$result."\", \"STOCK_NO\":\"".$stock_no."\"}]";

	}

	if($mode == "NBSTOCK_UNDO") 
	{
		$result = undoStock($conn, $stock_no, $prev_stock_no, $qty, $del_adm);

		echo "[{\"RESULT\":\"".$result."\"}]";

	}
	

	
?>

