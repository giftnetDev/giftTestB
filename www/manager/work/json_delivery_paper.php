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
	require "../../_classes/biz/work/work.php";


	if($mode == "APPEND_DELIVERY_PAPER")
	{
		$arr_rs = appendOrderDeliveryPaperMart($conn, $this_date, $order_goods_delivery_no, $delivery_seq_tf, $s_adm_no);

		for($i = 0; $i < sizeof($arr_rs); $i ++)
		{
			$rs_order_goods_delivery_no = $arr_rs[$i]['ORDER_GOODS_DELIVERY_NO'];
			$rs_delivery_seq = $arr_rs[$i]['DELIVERY_SEQ'];
			$rs_delivery_no = $arr_rs[$i]['DELIVERY_NO'];
			$rs_goods_delivery_name = iconv("EUC-KR", "UTF-8", $arr_rs[$i]['GOODS_DELIVERY_NAME']);
			$rs_delivery_profit = "";
			$rs_delivery_fee = iconv("EUC-KR", "UTF-8", $arr_rs[$i]['DELIVERY_FEE']);

			$results = "[{\"ORDER_GOODS_DELIVERY_NO\":\"".$rs_order_goods_delivery_no."\",\"DELIVERY_SEQ\":\"".$rs_delivery_seq."\",\"DELIVERY_NO\":\"".$rs_delivery_no."\",\"GOODS_DELIVERY_NAME\":\"".$rs_goods_delivery_name."\",\"DELIVERY_PROFIT\":\"".$rs_delivery_profit."\",\"DELIVERY_FEE\":\"".$rs_delivery_fee."\"}]";
		}

		echo $results;

	}
		
	if($mode == "DELETE_DELIVERY_PAPER")
	{
		$isSuccess = deleteOrderDeliveryPaper($conn, $order_goods_delivery_no, $s_adm_no);

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}


?>

