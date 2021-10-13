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


	function updateIndividualDelivery($db, $individual_no, $column, $value) { 

		$query="     UPDATE TBL_ORDER_GOODS_INDIVIDUAL ";

		if($column != "")
			$query .= " SET $column = '$value' ";

		$query .= "   WHERE INDIVIDUAL_NO = '$individual_no'";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateOrderGoodsWorkMsg($db, $order_goods_no, $work_msg) {
	
		$query="     UPDATE TBL_ORDER_GOODS 
					    SET WORK_MSG = '$work_msg'  
					  WHERE ORDER_GOODS_NO = '$order_goods_no'";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderGoodsWorkReqQty($db, $order_goods_no, $req_qty) { 
	
		$query="     UPDATE TBL_ORDER_GOODS 
					    SET WORK_REQ_QTY = '$req_qty'  
					  WHERE ORDER_GOODS_NO = '$order_goods_no'";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	if($mode == "UPDATE_ORDER_GOODS_OPT_STICKER_READY")
	{
		$result = updateOrderGoodsStickerReady($conn, $order_goods_no, $opt_sticker_ready);

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	if($mode == "UPDATE_ORDER_GOODS_WORK_QTY")
	{
		$result = updateOrderGoodsWorkQty($conn, $order_goods_no, $work_qty);

		echo "[{\"RESULT\":\"".$result."\",\"WORK_QTY\":\"".$work_qty."\",\"ORDER_GOODS_NO\":\"".$order_goods_no."\"}]";

	}

	if($mode == "UPDATE_ORDER_GOODS_WORK_SEQ")
	{
		$result = updateOrderGoodsWorkSeq($conn, $order_goods_no, $work_seq);

		echo "[{\"RESULT\":\"".$result."\",\"WORK_SEQ\":\"".$work_seq."\",\"ORDER_GOODS_NO\":\"".$order_goods_no."\"}]";

	}

	if($mode == "UPDATE_ORDER_GOODS_WORK_MSG")
	{
		$result = updateOrderGoodsWorkMsg($conn, $order_goods_no, iconv('utf-8', 'euc-kr', $work_msg));

		echo "[{\"RESULT\":\"".$result."\"}]";
	}

	if($mode == "UPDATE_ORDER_GOODS_DELIVERY_TYPE")
	{
		
		$result = updateOrderGoodsDeliveryType($conn, $order_goods_no, $delivery_type);
		updateOrderGoodsDeliveryCP($conn, $order_goods_no, "");

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	if($mode == "UPDATE_ORDER_GOODS_DELIVERY_CP")
	{
		$result = updateOrderGoodsDeliveryCP($conn, $order_goods_no, iconv('utf-8', 'euc-kr', $delivery_cp));

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	if($mode == "UPDATE_INDIVIDUAL_DELIVERY")
	{
		$result = updateIndividualDelivery($conn, $individual_no, iconv('utf-8', 'euc-kr', $column), iconv('utf-8', 'euc-kr', $value));

		echo "[{\"RESULT\":\"".$result."\"}]";

	}

	if($mode == "UPDATE_ORDER_GOODS_DELIVERY_INFO")
	{
		$result = updateOrderGoodsDeliveryNo($conn, $order_goods_no, iconv('utf-8', 'euc-kr', $delivery_cp), iconv('utf-8', 'euc-kr', $delivery_no));

		echo "[{\"RESULT\":\"".$result."\"}]";
	}

	if($mode == "UPDATE_ORDER_GOODS_REQ_QTY")
	{
		$result = updateOrderGoodsWorkReqQty($conn, $order_goods_no, $req_qty);

		echo "[{\"RESULT\":\"".$result."\"}]";
	}
	

	
?>

