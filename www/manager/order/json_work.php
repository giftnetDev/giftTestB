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
	require "../../_classes/biz/stock/stock.php";


	function updateOrderGoodsWorkState($db, $order_goods_no, $work_tf, $work_done_adm) { 
		
		$query = "UPDATE TBL_ORDER_GOODS 	
				  SET 
						WORK_TF = '$work_tf',
						WORK_DONE_ADM = '$work_done_adm',
						WORK_DONE_DATE = now()
						
				  WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query."<br/>";
	    //exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function selectStockByOrderGoods($db, $order_goods_no) { 
		
		$query = "SELECT STOCK_NO
					FROM TBL_STOCK 	
				   WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
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
	
	if($mode == "UPDATE_WORK_STATE")
	{
		//작업 상태 변경
		$isSuccess = updateOrderGoodsWorkState($conn, $order_goods_no, $work_tf, $work_done_adm);

		if($isSuccess) { 
			if($work_tf == "Y") { 
				//재고 추가

				$stock_type     = "OUT";          
				$stock_code     = "NOUT01";      
				$in_cp_no		= "";	         
				$out_cp_no	    = 1;        // 기프트넷으로 고정

				$goods_no		= $goods_no; 
				$in_loc			= "LOCE";      
				$in_loc_ext	    = "작업출고(".$order_goods_no.")";
				$in_qty			= 0;
				$in_bqty		= 0;
				$in_fbqty		= 0;
				$out_qty		= $qty;
				$out_bqty		= 0;
				$out_fbqty	    = 0;
				$in_price		= 0;
				$out_price	    = 0;     
				$in_date		= "";
				$out_date		= date("Y-m-d H:i",strtotime("0 month"));
				$pay_date		= "";
				$reserve_no	    = getReserveNoByOrderGoodsNo($conn, $order_goods_no);
				$close_tf		= "N";
				$memo			= "";

				$new_stock_no = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $work_done_adm, $memo);

				updateOrderGoodsNo($conn, $order_goods_no, $new_stock_no);
				

			} else { 
				//재고 삭제

				$arr_rs = selectStockByOrderGoods($conn, $order_goods_no);
				if(sizeof($arr_rs) > 0) { 

					for($i = 0; $i <= sizeof($arr_rs); $i++) { 

						$STOCK_NO = $arr_rs[$i]["STOCK_NO"];

						deleteStock($conn, $STOCK_NO, $work_done_adm);

					}

				}

			}
		}

		$results = "[{\"RESULT\":\"".$isSuccess."\"}]";
		echo $results;
	}


	if($mode == "GET_DELIVERY_INDIVIDUAL")
	{
		$arr_rs_individual = cntDeliveryIndividual($conn, $order_goods_no);
		if(sizeof($arr_rs_individual) > 0) { 
			$cnt_delivery_place = $arr_rs_individual[0]["CNT_DELIVERY_PLACE"];
			$total_sub_qty		= $arr_rs_individual[0]["TOTAL_GOODS_DELIVERY_QTY"];
			$total_delivered_qty= $arr_rs_individual[0]["TOTAL_DELIVERED_QTY"];
			
			$results = "[{\"CNT_DELIVERY_PLACE\":\"".$cnt_delivery_place."\",\"TOTAL_GOODS_DELIVERY_QTY\":\"".$total_sub_qty."\",\"TOTAL_DELIVERED_QTY\":\"".$total_delivered_qty."\"}]";
		}
		echo $results;
	}

	if($mode == "GET_ORDER_GOODS_DELIVERY")
	{
		$arr_rs_delivery = cntOrderGoodsDelivery($conn, $reserve_no, $order_goods_no, $individual_no);
									
		for($p = 0; $p < sizeof($arr_rs_delivery); $p ++)
		{
			$P_DELIVERY_CP				= trim($arr_rs_delivery[$p]["DELIVERY_CP"]);
			$TOTAL						= trim($arr_rs_delivery[$p]["TOTAL"]);
			$CNT_YES					= trim($arr_rs_delivery[$p]["CNT_YES"]);
			$CNT_NO						= trim($arr_rs_delivery[$p]["CNT_NO"]);

			$short_delivery_cp = iconv("EUC-KR", "UTF-8", substr($P_DELIVERY_CP, 0, 2));
			
			$results = "[{\"SHORT_DELIVERY_CP\":\"".$short_delivery_cp."\",\"TOTAL\":\"".$TOTAL."\",\"CNT_YES\":\"".$CNT_YES."\",\"CNT_NO\":\"".$CNT_NO."\",\"RESERVE_NO\":\"".$reserve_no."\",\"ORDER_GOODS_NO\":\"".$order_goods_no."\"}]";
		}
		echo $results;
	}



?>

