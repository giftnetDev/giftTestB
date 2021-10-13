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

	$today = date("Y-m-d",strtotime("0 month"));
	$seed_number = 1;

	if($mode == "APPEND_DELIVERY_PAPER")
	{
		$arr_rs = appendOrderDeliveryPaper($conn, $today, $order_goods_delivery_no, $delivery_seq_tf, $s_adm_no, $seed_number);

		for($i = 0; $i < sizeof($arr_rs); $i ++)
		{
			//ORDER_GOODS_DELIVERY_NO, DELIVERY_SEQ, DELIVERY_NO, GOODS_DELIVERY_NAME, DELIVERY_PROFIT, DELIVERY_FEE

			$rs_order_goods_delivery_no = $arr_rs[$i]['ORDER_GOODS_DELIVERY_NO'];
			$rs_delivery_seq = $arr_rs[$i]['DELIVERY_SEQ'];
			$rs_delivery_no = $arr_rs[$i]['DELIVERY_NO'];
			$rs_goods_delivery_name = iconv("EUC-KR", "UTF-8", $arr_rs[$i]['GOODS_DELIVERY_NAME']);
			$rs_delivery_profit = iconv("EUC-KR", "UTF-8", $arr_rs[$i]['DELIVERY_PROFIT']);
			$rs_delivery_fee = iconv("EUC-KR", "UTF-8", $arr_rs[$i]['DELIVERY_FEE']);

			$results = "[{\"ORDER_GOODS_DELIVERY_NO\":\"".$rs_order_goods_delivery_no."\",\"DELIVERY_SEQ\":\"".$rs_delivery_seq."\",\"DELIVERY_NO\":\"".$rs_delivery_no."\",\"GOODS_DELIVERY_NAME\":\"".$rs_goods_delivery_name."\",\"DELIVERY_PROFIT\":\"".$rs_delivery_profit."\",\"DELIVERY_FEE\":\"".$rs_delivery_fee."\"}]";
		}

		echo $results;

	}
		
	if($mode == "DELETE_DELIVERY_PAPER")
	{
		$isSuccess = deleteOrderDeliveryPaper($conn, $today, $order_goods_delivery_no, $s_adm_no);

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}

	if($mode == "UPDATE_DELIVERY_PAPER")
	{
	//	$isSuccess = deleteOrderDeliveryPaper($conn, $today, $order_goods_delivery_no, $s_adm_no);

	//	$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
	//	echo $results;

	/*

	if ($mode == "DELIVERY_PAPER_UPDATE") {
		
		$row_cnt = count($hid_order_goods_delivery_no);
		

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_order_goods_delivery_no		= $hid_order_goods_delivery_no[$k];
			$temp_goods_delivery_name			= SetStringToDB($goods_delivery_name[$k]);

			$checked = $hid_order_goods_delivery_no_selected[$k];

			$temp_delivery_profit = $delivery_profit[$k];
			$temp_delivery_fee = $delivery_fee[$k];
			
			//echo $temp_order_goods_delivery_no."---".$temp_goods_delivery_name."<br/>";
			if($temp_goods_delivery_name <> '' && $temp_delivery_profit <> '' && $temp_delivery_fee <> '' && $checked == 'Y' )
				$result = updateOrderDeliveryPaper($conn, $temp_order_goods_delivery_no, $temp_goods_delivery_name, $temp_delivery_profit, $temp_delivery_fee);
			
		}
	}


	*/
	}


?>

