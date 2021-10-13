<?
	# =============================================================================
	# File Name    : stats.php
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_ORDER, TBL_ORDER_GOODS
	#=========================================================================================================
	
	/*	
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listStatsOrder($db, $group_name, $date_type, $start_date, $end_date, $buy_cp_no, $cp_no, $opt_manager_no, $search_field, $search_str, $order_field, $order_asc) {

		if ($group_name == "day") {
			$group_str = "substring(AA.G_DATE,1, 10) AS G_DATE";
		}
		
		if ($group_name == "month") {
			$start_date = left($start_date,7)."-01";
			$end_date = left($end_date,7)."-31";
			$group_str = "substring(AA.G_DATE,1, 7) AS G_DATE";
		}

		if ($group_name == "goods") {
			$group_str = "AA.GOODS_NAME AS G_DATE";
		}

		$query = "SELECT ".$group_str.",
										sum(TOT_ORDER_SALE_PRICE) AS TOT_ORDER_SALE_PRICE, 
										sum(TOT_ORDER_SALE_QTY) AS TOT_ORDER_SALE_QTY,
										sum(TOT_DELIVERY_SALE_PRICE) AS TOT_DELIVERY_SALE_PRICE, 
										sum(TOT_DELIVERY_SALE_QTY) AS TOT_DELIVERY_SALE_QTY,
										sum(TOT_CANCEL_SALE_PRICE) AS TOT_CANCEL_SALE_PRICE, 
										sum(TOT_CANCEL_SALE_QTY) AS TOT_CANCEL_SALE_QTY,
										sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE) AS TOT_SUN_SALE_PRICE, 
										sum(TOT_ORDER_SALE_QTY) - sum(TOT_CANCEL_SALE_QTY) AS TOT_SUN_SALE_QTY,
										(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) -
										(sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE)) AS PLUS_PRICE,
										ROUND(((
										(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) - (sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE))
										) / 
										(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) * 100),2) AS LEE

										FROM (

										SELECT A.RESERVE_NO, A.PAY_DATE, A.DELIVERY_DATE, A.FINISH_DATE, A.ORDER_STATE, A.DISCOUNT_PRICE, A.SA_DELIVERY_PRICE,
										(SELECT GOODS_NAME FROM TBL_GOODS C WHERE A.GOODS_NO = C.GOODS_NO) AS GOODS_NAME,
										CASE  
										WHEN A.ORDER_STATE IN ('1','2','3') THEN A.PAY_DATE
										WHEN A.ORDER_STATE = '3' THEN A.DELIVERY_DATE 
										WHEN A.ORDER_STATE = '6' THEN A.FINISH_DATE 
										WHEN A.ORDER_STATE = '7' THEN A.FINISH_DATE 
										WHEN A.ORDER_STATE = '8' THEN A.FINISH_DATE
										END AS G_DATE,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_ORDER_SALE_PRICE,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN A.QTY ELSE 0 END AS TOT_ORDER_SALE_QTY,
										CASE WHEN A.ORDER_STATE = '3' THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_DELIVERY_SALE_PRICE,
										CASE WHEN A.ORDER_STATE = '3' THEN A.QTY ELSE 0 END AS TOT_DELIVERY_SALE_QTY,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_CANCEL_SALE_PRICE,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN (A.QTY) ELSE 0 END AS TOT_CANCEL_SALE_QTY,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN ((A.PRICE + A.EXTRA_PRICE) * A.QTY) + A.SA_DELIVERY_PRICE ELSE 0 END AS TOT_ORDER_BUY_PRICE,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN ((A.PRICE + A.EXTRA_PRICE) * A.QTY) + A.SA_DELIVERY_PRICE ELSE 0 END AS TOT_CANCEL_BUY_PRICE

										FROM TBL_ORDER_GOODS A, TBL_ORDER B
										WHERE A.RESERVE_NO = B.RESERVE_NO
										AND A.DEL_TF = 'N'
										AND A.USE_TF = 'Y'
										AND B.DEL_TF = 'N'
										AND B.USE_TF = 'Y' ";
/*
		if ($date_type == "PAY_DATE") {				
			$query .= "	AND A.PAY_DATE >= '".$start_date."' and A.PAY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($date_type == "DELIVERY_DATE") {				
			$query .= "	AND A.DELIVERY_DATE >= '".$start_date."' and A.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($date_type == "FINISH_DATE") {				
			$query .= "	AND A.FINISH_DATE >= '".$start_date."' and A.FINISH_DATE <= '".$end_date." 23:59:59' ";
		}
*/
			$query .= "		AND ((A.PAY_DATE >= '".$start_date."' and A.PAY_DATE <= '".$end_date." 23:59:59')
										OR (A.DELIVERY_DATE >= '".$start_date."' and A.DELIVERY_DATE <= '".$end_date." 23:59:59')
											OR (A.FINISH_DATE >= '".$start_date."' and A.FINISH_DATE <= '".$end_date." 23:59:59')) ";

		//									OR (A.DELIVERY_DATE >= '".$start_date."' and A.DELIVERY_DATE <= '".$end_date." 23:59:59')
		//									OR (A.FINISH_DATE >= '".$start_date."' and A.FINISH_DATE <= '".$end_date." 23:59:59')) ";


		if ($buy_cp_no <> "") {
			$query .= " AND A.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND B.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND A.GOODS_NAME like '%".$search_str."%' ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
			
		$query .= "		) AA  WHERE AA.G_DATE IS NOT NULL ";

			//$query .= "	AND AA.G_DATE >= '".$start_date."' and AA.G_DATE <= '".$end_date." 23:59:59' ";


		if ($group_name == "day") {
			$group_str = "substring(AA.G_DATE,1, 10)";
		}
		
		if ($group_name == "month") {
			$group_str = "substring(AA.G_DATE,1, 7)";
		}

		if ($group_name == "goods") {
			$group_str = "AA.GOODS_NAME";
		}

		if ($order_field == "TITLE") {
			if ($group_name == "day") {
				$order_str = "substring(G_DATE,1, 10) DESC";
			}
		
			if ($group_name == "month") {
				$order_str = "substring(AA.G_DATE,1, 7) DESC";
			}

			if ($group_name == "goods") {
				$order_str = "AA.GOODS_NAME ASC ";
			}	
		} else {
			$order_str = $order_field." ".$order_asc;
		}

		$query .= "		GROUP BY " .$group_str." ORDER BY ".$order_str;

		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listStatsAllOrder($db, $group_name, $date_type, $start_date, $end_date, $buy_cp_no, $cp_no, $opt_manager_no, $search_field, $search_str) {

		if ($group_name == "month") {
			$start_date = left($start_date,7)."-01";
			$end_date = left($end_date,7)."-31";
			$group_str = "substring(AA.G_DATE,1, 7) AS G_DATE";
		}

		$query = "SELECT 
										IFNULL(sum(TOT_ORDER_SALE_PRICE),0) AS TOT_ORDER_SALE_PRICE, 
										IFNULL(sum(TOT_ORDER_SALE_QTY),0) AS TOT_ORDER_SALE_QTY,
										IFNULL(sum(TOT_DELIVERY_SALE_PRICE),0) AS TOT_DELIVERY_SALE_PRICE, 
										IFNULL(sum(TOT_DELIVERY_SALE_QTY),0) AS TOT_DELIVERY_SALE_QTY,
										IFNULL(sum(TOT_CANCEL_SALE_PRICE),0) AS TOT_CANCEL_SALE_PRICE, 
										IFNULL(sum(TOT_CANCEL_SALE_QTY),0) AS TOT_CANCEL_SALE_QTY,
										IFNULL(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE),0) AS TOT_SUN_SALE_PRICE, 
										IFNULL(sum(TOT_ORDER_SALE_QTY) - sum(TOT_CANCEL_SALE_QTY),0) AS TOT_SUN_SALE_QTY,
										IFNULL(
												(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) -
												(sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE)), 0) AS PLUS_PRICE,
										ROUND((
										(
										(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) - (sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE))
										) / 
										(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) * 100),2) AS LEE

										FROM (

										SELECT A.RESERVE_NO, A.PAY_DATE, A.DELIVERY_DATE, A.FINISH_DATE, A.ORDER_STATE, A.DISCOUNT_PRICE, A.SA_DELIVERY_PRICE,
										(SELECT GOODS_NAME FROM TBL_GOODS C WHERE A.GOODS_NO = C.GOODS_NO) AS GOODS_NAME,
										CASE  
										WHEN A.ORDER_STATE IN ('1','2','3') THEN A.PAY_DATE
										WHEN A.ORDER_STATE = '3' THEN A.DELIVERY_DATE 
										WHEN A.ORDER_STATE = '6' THEN A.FINISH_DATE 
										WHEN A.ORDER_STATE = '7' THEN A.FINISH_DATE 
										WHEN A.ORDER_STATE = '8' THEN A.FINISH_DATE
										END AS G_DATE,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_ORDER_SALE_PRICE,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN A.QTY ELSE 0 END AS TOT_ORDER_SALE_QTY,
										CASE WHEN A.ORDER_STATE = '3' THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_DELIVERY_SALE_PRICE,
										CASE WHEN A.ORDER_STATE = '3' THEN A.QTY ELSE 0 END AS TOT_DELIVERY_SALE_QTY,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN (A.SALE_PRICE * A.QTY) - A.DISCOUNT_PRICE ELSE 0 END AS TOT_CANCEL_SALE_PRICE,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN (A.QTY) ELSE 0 END AS TOT_CANCEL_SALE_QTY,
										CASE WHEN A.ORDER_STATE IN ('1','2','3') THEN ((A.PRICE + A.EXTRA_PRICE) * A.QTY) + A.SA_DELIVERY_PRICE ELSE 0 END AS TOT_ORDER_BUY_PRICE,
										CASE WHEN A.ORDER_STATE IN ('6','7','8') THEN ((A.PRICE + A.EXTRA_PRICE) * A.QTY) + A.SA_DELIVERY_PRICE ELSE 0 END AS TOT_CANCEL_BUY_PRICE

										FROM TBL_ORDER_GOODS A, TBL_ORDER B
										WHERE A.RESERVE_NO = B.RESERVE_NO
										AND A.DEL_TF = 'N'
										AND A.USE_TF = 'Y'
										AND B.DEL_TF = 'N'
										AND B.USE_TF = 'Y'  ";
/*
		if ($date_type == "PAY_DATE") {				
			$query .= "	AND A.PAY_DATE >= '".$start_date."' and A.PAY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($date_type == "DELIVERY_DATE") {				
			$query .= "	AND A.DELIVERY_DATE >= '".$start_date."' and A.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($date_type == "FINISH_DATE") {				
			$query .= "	AND A.FINISH_DATE >= '".$start_date."' and A.FINISH_DATE <= '".$end_date." 23:59:59' ";
		}
*/
			$query .= "		AND ((A.PAY_DATE >= '".$start_date."' and A.PAY_DATE <= '".$end_date." 23:59:59')
										OR (A.DELIVERY_DATE >= '".$start_date."' and A.DELIVERY_DATE <= '".$end_date." 23:59:59')
											OR (A.FINISH_DATE >= '".$start_date."' and A.FINISH_DATE <= '".$end_date." 23:59:59')) ";


		if ($buy_cp_no <> "") {
			$query .= " AND A.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND B.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND A.GOODS_NAME like '%".$search_str."%' ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= "		) AA  WHERE AA.G_DATE IS NOT NULL ";

			//$query .= "	AND AA.G_DATE >= '".$start_date."' and AA.G_DATE <= '".$end_date." 23:59:59' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listSalesStatement($db, $start_date, $end_date, $buy_cp_no, $cp_no, $opt_manager_no, $filter, $search_field, $search_str, $order_field, $order_asc, $nPage, $nRowCount, $total_cnt) {

		$cate_01 = $filter['cate_01'];
		$order_state = $filter['PICK_ORDER_STATE'];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
						O.OPT_MANAGER_NO, OG.ORDER_GOODS_NO, OG.ORDER_DATE, O.RESERVE_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, O.O_MEM_NM, O.R_MEM_NM, 
					   CC.CP_CODE AS BUY_CP_CODE, CC.CP_NM AS BUY_CP_NM , CC.CP_NM2 AS BUY_CP_NM2, 
					   OG.GOODS_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME, OG.QTY,  
					   OG.OPT_STICKER_NO, OG.OPT_STICKER_READY,	OG.OPT_STICKER_MSG, OG.OPT_OUTBOX_TF, OG.DELIVERY_CNT_IN_BOX, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_OUTSTOCK_DATE, OG.OPT_MEMO,
					   OG.CATE_01, OG.CATE_04, OG.TAX_TF,
					   OG.PRICE, OG.BUY_PRICE, OG.SALE_PRICE, OG.EXTRA_PRICE, OG.DELIVERY_PRICE, OG.SA_DELIVERY_PRICE, OG.DISCOUNT_PRICE, OG.STICKER_PRICE, OG.PRINT_PRICE, OG.SALE_SUSU, IFNULL(OG.LABOR_PRICE, 0) AS LABOR_PRICE, IFNULL(OG.OTHER_PRICE, 0) AS OTHER_PRICE, 
					   OG.ORDER_STATE, 
					   OG.ORDER_CONFIRM_DATE, OG.DELIVERY_DATE, OG.SALE_CONFIRM_YMD, OG.DELIVERY_TYPE,
					   OG.CP_ORDER_NO
	

					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
			   LEFT JOIN TBL_COMPANY CC ON OG.BUY_CP_NO = CC.CP_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N' ";

		if ($start_date <> "") {
			$query .= " AND OG.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND OG.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		}

		if(sizeof($cate_01) > 0) {

			$query2 = "";
			foreach($cate_01 as $each_cate) { 
				$query2 .= "'".$each_cate."',";
			}
			$query2 = rtrim($query2, ",");
			
			$query .= " AND CATE_01 IN (".$query2.", '') ";
	
		} else { 
			$query .= " AND CATE_01 = '' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO like '%".$search_str."%' OR OG.GOODS_NAME like '%".$search_str."%' OR OG.GOODS_CODE like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if(sizeof($order_state) > 0){
			$query3 = "";
			foreach($order_state as $each_state) { 
				$query3 .= "'".$each_state."',";
			}
			$query3 = rtrim($query3, ",");
			$query .= " AND OG.ORDER_STATE NOT IN (".$query3.", '') ";
		}

		$query .= "	ORDER BY OG.ORDER_DATE ".$order_asc." limit ".$offset.", ".$nRowCount;

		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function cntSalesStatement($db, $start_date, $end_date, $buy_cp_no, $cp_no, $opt_manager_no, $filter, $search_field, $search_str) {

		$cate_01 = $filter['cate_01'];
		$order_state = $filter['PICK_ORDER_STATE'];

		$query = "SELECT COUNT(*)

					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
			   LEFT JOIN TBL_COMPANY CC ON OG.BUY_CP_NO = CC.CP_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N' ";

		if ($start_date <> "") {
			$query .= " AND OG.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND OG.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		}

		if(sizeof($cate_01) > 0) {

			$query2 = "";
			foreach($cate_01 as $each_cate) { 
				$query2 .= "'".$each_cate."',";
			}
			$query2 = rtrim($query2, ",");

			$query .= " AND CATE_01 IN (".$query2.", '') ";
	
		} else { 
			$query .= " AND CATE_01 = '' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO like '%".$search_str."%' OR OG.GOODS_NAME like '%".$search_str."%' OR OG.GOODS_CODE like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if(sizeof($order_state) > 0){
			$query3 = "";
			foreach($order_state as $each_state) { 
					$query3 .= "'".$each_state."',";
			}
			$query3 = rtrim($query3, ",");
			$query .= " AND OG.ORDER_STATE NOT IN (".$query3.", '') ";
		}

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function sumSalesStatement($db, $start_date, $end_date, $buy_cp_no, $cp_no, $opt_manager_no, $filter, $search_field, $search_str) {

		$cate_01 = $filter['cate_01'];
		$order_state = $filter['PICK_ORDER_STATE'];
		
		$query = "
			SELECT SUM(QTY) AS SUM_QTY,
				   SUM(SALE_PRICE * QTY) AS SUM_SALE_PRICE,
				   SUM(DISCOUNT_PRICE) AS SUM_DISCOUNT_PRICE,
				   SUM(SA_DELIVERY_PRICE) AS SUM_SA_DELIVERY_PRICE,
				   SUM(EXTRA_PRICE * QTY) AS SUM_EXTRA_PRICE,
				   SUM((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY)) AS SUM_TOTAL_SALE_PRICE,
				
				   SUM(TOTAL_WONGA * QTY) AS SUM_TOTAL_WONGA,
				   ROUND(SUM(CASE WHEN ORDER_STATE < 4 
				              THEN MAJIN
							  ELSE 0
							   END  
							       * QTY) / COUNT(*) , 2) AS AVG_MAJIN,
				   
				   ROUND(SUM((CASE WHEN ORDER_STATE < 4
								   THEN MAJIN
				                   ELSE 0 
							        END
									 * QTY / ((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY) / QTY)) * 100) / COUNT(*), 2) AS AVG_MAJIN_PER,
				   SUM((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY) - (TOTAL_WONGA * QTY)) AS SUM_TOTAL_MAJIN
		      FROM (
				    
					SELECT OG.ORDER_STATE,
					       CASE WHEN OG.ORDER_STATE < 4 THEN OG.QTY ELSE OG.QTY * -1 END AS QTY,  
						   OG.SALE_PRICE,
						   OG.DISCOUNT_PRICE,
						   OG.SA_DELIVERY_PRICE,
						   OG.EXTRA_PRICE,

						   OG.BUY_PRICE,
						   CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END AS STICKER_PRICE,
						   CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END AS PRINT_PRICE,
						   CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END AS DELIVERY_PER_PRICE,
						   OG.LABOR_PRICE,
						   OG.OTHER_PRICE,
						   ROUND(BUY_PRICE + CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END + CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END + (CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END) + LABOR_PRICE + OTHER_PRICE, 0) AS TOTAL_WONGA,
						   OG.SALE_PRICE - OG.DISCOUNT_PRICE - ROUND(BUY_PRICE + (CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END) + (CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END) + (CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END) + LABOR_PRICE + OTHER_PRICE, 0) AS MAJIN


					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
			   LEFT JOIN TBL_COMPANY CC ON OG.BUY_CP_NO = CC.CP_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N' ";

		if ($start_date <> "") {
			$query .= " AND OG.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND OG.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		}

		if(sizeof($cate_01) > 0) {

			$query2 = "";
			foreach($cate_01 as $each_cate) { 
				$query2 .= "'".$each_cate."',";
			}
			$query2 = rtrim($query2, ",");
			
			$query .= " AND CATE_01 IN (".$query2.", '') ";
	
		} else { 
			$query .= " AND CATE_01 = '' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO like '%".$search_str."%' OR OG.GOODS_NAME like '%".$search_str."%' OR OG.GOODS_CODE like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if(sizeof($order_state) > 0){
			$query3 = "";
			foreach($order_state as $each_state) { 
					$query3 .= "'".$each_state."',";
			}
			$query3 = rtrim($query3, ",");
			$query .= " AND OG.ORDER_STATE NOT IN (".$query3.", '') ";
		}
		
		$query .= " ) AS C ";

		 //echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	//매입리스트
	function listStatsBuyingByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		if($group_name == "company") { 
		
			$query = "
						SELECT CP_NO AS KEY_CODE, CP_CODE AS CODE, CONCAT(CP_NM, ' ', CP_NM2) AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE 
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매입' 
								AND L.CATE_01  = ''
								
								";
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "TITLE";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA GROUP BY CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}

		if($group_name == "period") { 
	
			$query = "
						SELECT '' AS KEY_CODE, DATE_FORMAT(INOUT_DATE, '%Y') AS CODE, DATE_FORMAT(INOUT_DATE, '%Y-%m') AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매입' 
								AND L.CATE_01  = ''
								
					 ";
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "INOUT_DATE";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA GROUP BY DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m') ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}

		if($group_name == "goods") { 

			/*
				SELECT GOODS_NO AS KEY_CODE, GOODS_CODE AS CODE, GOODS_NAME AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
			*/
			$query = "
						SELECT GOODS_NO AS KEY_CODE, GOODS_CODE AS CODE, GOODS_NAME AS TITLE, SUM( CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE 0 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE 0 END) ) AS QTY_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN UNIT_PRICE ELSE 0 END) ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매입' 
								AND L.CATE_01  = ''
								
								";
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "ORDER_TOTAL";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA	GROUP BY GOODS_NO, GOODS_CODE, GOODS_NAME ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}
			
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//매입리스트
	function totalCntStatsBuyingByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str) {

		$query = "
				SELECT COUNT(*) 
				  FROM (

				  SELECT
				  ";

		if($group_name == "company")  
			$query .= " CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ";

		if($group_name == "period")  
			$query .= "	 DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m')";

		if($group_name == "goods")  
			$query .= "	GOODS_NO, GOODS_CODE, GOODS_NAME";

		$query .="
						
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매입' 
								AND L.MEMO NOT LIKE '%교환%' 
								AND L.CATE_01  = ''
								
								";
		
		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
		}

		if ($start_date <> "") {
			$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND L.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if($group_name == "company")  
			$query .= "	 ) AA	GROUP BY CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ) BB";

		if($group_name == "period")  
			$query .= "	 ) AA	GROUP BY DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m') ) BB ";

		if($group_name == "goods")  
			$query .= "	 ) AA	GROUP BY GOODS_NO, GOODS_CODE, GOODS_NAME ) BB";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	//매입리스트
	function SumStatsBuyingByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str) {

		$query = "
					SELECT IFNULL(SUM(CASE WHEN L.CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END), 0) AS SUM_ORDER_TOTAL, IFNULL(SUM( QTY * (CASE WHEN L.CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ), 0) AS SUM_QTY_TOTAL, IFNULL(SUM( L.QTY * L.UNIT_PRICE ), 0) AS SUM_PRICE_TOTAL
					  
							  FROM TBL_COMPANY_LEDGER L
							  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
							  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
 
							WHERE 
								L.DEL_TF = 'N'
							AND L.USE_TF = 'Y'
							AND L.INOUT_TYPE = '매입' 
							AND L.MEMO NOT LIKE '%교환%' 
							AND L.CATE_01  = ''
							
							";
	
		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
		}

		if ($start_date <> "") {
			$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND L.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
			
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//매출리스트
	function listStatsByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		if($group_name == "company") { 
		
			$query = "
						SELECT CP_NO AS KEY_CODE, CP_CODE AS CODE, CONCAT(CP_NM, ' ', CP_NM2) AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE 
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매출' 
								AND L.MEMO NOT LIKE '%교환%' 
								AND L.CATE_01  = ''
								
								";
								//NOT IN ('증정','샘플','추가')
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "TITLE";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA GROUP BY CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}

		if($group_name == "period") { 
	
			$query = "
						SELECT '' AS KEY_CODE, DATE_FORMAT(INOUT_DATE, '%Y') AS CODE, DATE_FORMAT(INOUT_DATE, '%Y-%m') AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매출' 
								AND L.MEMO NOT LIKE '%교환%' 
								AND L.CATE_01  = ''
								
					 ";
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "INOUT_DATE";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA GROUP BY DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m') ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}

		if($group_name == "goods") { 

			/*
				SELECT GOODS_NO AS KEY_CODE, GOODS_CODE AS CODE, GOODS_NAME AS TITLE, SUM(CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ) AS QTY_TOTAL, SUM( QTY * UNIT_PRICE ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
			*/
			$query = "
						SELECT GOODS_NO AS KEY_CODE, GOODS_CODE AS CODE, GOODS_NAME AS TITLE, CATE_04 AS SALE_STATE, SUM( CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE 0 END) AS ORDER_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE 0 END) ) AS QTY_TOTAL, SUM( QTY * (CASE WHEN CLAIM_ORDER_GOODS_NO = 0 THEN UNIT_PRICE ELSE 0 END) ) AS PRICE_TOTAL, MAX(INOUT_DATE) AS LATEST_ORDER_DATE
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.CATE_04, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매출' 
								AND L.MEMO NOT LIKE '%교환%' 
								AND L.CATE_01  = ''
								
								";
		
			if ($goods_cate <> "") {
				$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
							 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
			}

			if ($start_date <> "") {
				$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
			}

			if ($cp_no <> "") {
				$query .= " AND L.CP_NO = '".$cp_no."' ";
			} 

			if ($opt_manager_no <> "") { 
				$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
			}

			if ($search_str <> "") {

				if ($search_field == "ALL") {
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "ORDER_TOTAL";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= "	 ) AA	GROUP BY GOODS_NO, GOODS_CODE, GOODS_NAME ORDER BY ".$order_field." ".$order_str;
			$query .= " limit ".$offset.", ".$nRowCount;

		}
			
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//매출리스트
	function totalCntStatsByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str) {

		$query = "
				SELECT COUNT(*) 
				  FROM (

				  SELECT
				  ";

		if($group_name == "company")  
			$query .= " CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ";

		if($group_name == "period")  
			$query .= "	 DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m')";

		if($group_name == "goods")  
			$query .= "	GOODS_NO, GOODS_CODE, GOODS_NAME";

		$query .="
						
						  FROM (
								SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE, L.CLAIM_ORDER_GOODS_NO 
								  FROM TBL_COMPANY_LEDGER L
								  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
								  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
	 
								WHERE 
									L.DEL_TF = 'N'
								AND L.USE_TF = 'Y'
								AND L.INOUT_TYPE = '매출' 
								AND L.MEMO NOT LIKE '%교환%' 
								AND L.CATE_01  = ''
								
								";
		
		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
		}

		if ($start_date <> "") {
			$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND L.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if($group_name == "company")  
			$query .= "	 ) AA	GROUP BY CP_NO, CP_CODE, CONCAT(CP_NM, ' ', CP_NM2) ) BB";

		if($group_name == "period")  
			$query .= "	 ) AA	GROUP BY DATE_FORMAT(INOUT_DATE, '%Y'), DATE_FORMAT(INOUT_DATE, '%Y-%m') ) BB ";

		if($group_name == "goods")  
			$query .= "	 ) AA	GROUP BY GOODS_NO, GOODS_CODE, GOODS_NAME ) BB";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	//매출리스트
	function SumStatsByCompanyLedger($db, $group_name,  $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str) {

		$query = "
					SELECT IFNULL(SUM(CASE WHEN L.CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END), 0) AS SUM_ORDER_TOTAL, IFNULL(SUM( QTY * (CASE WHEN L.CLAIM_ORDER_GOODS_NO = 0 THEN 1 ELSE -1 END) ), 0) AS SUM_QTY_TOTAL, IFNULL(SUM( L.QTY * L.UNIT_PRICE ), 0) AS SUM_PRICE_TOTAL
					  
							  FROM TBL_COMPANY_LEDGER L
							  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
							  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO
 
							WHERE 
								L.DEL_TF = 'N'
							AND L.USE_TF = 'Y'
							AND L.INOUT_TYPE = '매출' 
							AND L.MEMO NOT LIKE '%교환%' 
							AND L.CATE_01  = ''
							
							";
	
		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
		}

		if ($start_date <> "") {
			$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND L.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
			
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function calcSalesStatement($db, $start_date, $end_date, $option) {

		$query = "
			SELECT SUM(QTY) AS SUM_QTY,
				   SUM(SALE_PRICE * QTY) AS SUM_SALE_PRICE,
				   SUM(DISCOUNT_PRICE) AS SUM_DISCOUNT_PRICE,
				   SUM(SA_DELIVERY_PRICE) AS SUM_SA_DELIVERY_PRICE,
				   SUM(EXTRA_PRICE * QTY) AS SUM_EXTRA_PRICE,
				   SUM((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY)) AS SUM_TOTAL_SALE_PRICE,
				
				   SUM(TOTAL_WONGA * QTY) AS SUM_TOTAL_WONGA,
				   ROUND(SUM(MAJIN * QTY) / COUNT(*), 2) AS AVG_MAJIN,
				   
				   ROUND(SUM((MAJIN * QTY / ((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY) / QTY)) * 100) / COUNT(*), 2) AS AVG_MAJIN_PER,
				   SUM((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY) - (TOTAL_WONGA * QTY)) AS SUM_TOTAL_MAJIN
		      FROM (
				    
					SELECT CASE WHEN OG.ORDER_STATE < 4 THEN OG.QTY ELSE OG.QTY * -1 END AS QTY,  
						   OG.SALE_PRICE,
						   OG.DISCOUNT_PRICE,
						   OG.SA_DELIVERY_PRICE,
						   OG.EXTRA_PRICE,

						   OG.BUY_PRICE,
						   CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END AS STICKER_PRICE,
						   CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END AS PRINT_PRICE,
						   CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END AS DELIVERY_PER_PRICE,
						   OG.LABOR_PRICE,
						   OG.OTHER_PRICE,
						   ROUND(BUY_PRICE + CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END + CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END + (CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END) + LABOR_PRICE + OTHER_PRICE, 0) AS TOTAL_WONGA,
						   SALE_PRICE - ROUND(BUY_PRICE + (CASE WHEN OG.OPT_STICKER_NO = 0 THEN 0 ELSE OG.STICKER_PRICE END) + (CASE WHEN OG.OPT_WRAP_NO = 0 THEN 0 ELSE OG.PRINT_PRICE END) + (CASE WHEN OG.SA_DELIVERY_PRICE > 0 THEN 0 ELSE CASE WHEN OG.DELIVERY_PRICE = 0 OR OG.DELIVERY_CNT_IN_BOX = 0 THEN 0 ELSE ROUND(OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX) END END) + LABOR_PRICE + OTHER_PRICE, 0) AS MAJIN


					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N' ";

		if ($start_date <> "") {
			$query .= " AND OG.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}
		
		$query .= " ) AS C ";

		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateOrderGoodsTotalSale($db, $re_calc) {

		//판매 합계
		$query=" UPDATE TBL_ORDER_GOODS 
					SET STAT_TOTAL_SALE = ((SALE_PRICE * QTY) - DISCOUNT_PRICE - (EXTRA_PRICE * QTY)) * (CASE WHEN ORDER_STATE < 4 THEN 1 ELSE -1 END)
				  WHERE USE_TF = 'Y' AND DEL_TF = 'N'";

		if ($re_calc <> "Y") {
			$query .= "  AND STAT_TOTAL_SALE IS null ";
		}

		//echo $query."<br/><br/>";
		mysql_query($query,$db);

		//비용 합계
		$query = "
			UPDATE TBL_ORDER_GOODS TOG 
			  JOIN 
				(
					SELECT  ORDER_GOODS_NO, 
							ROUND( BUY_PRICE 
								 + CASE WHEN OG.OPT_STICKER_NO =0 THEN 0 ELSE OG.STICKER_PRICE END 
								 + CASE WHEN OG.OPT_WRAP_NO =0 THEN 0 ELSE OG.PRINT_PRICE END 
								 + (CASE WHEN OG.SA_DELIVERY_PRICE >0 
										 THEN 0 
										 ELSE 
											CASE WHEN OG.DELIVERY_PRICE =0 OR OG.DELIVERY_CNT_IN_BOX =0
												 THEN 0 
												 ELSE ROUND( OG.DELIVERY_PRICE / OG.DELIVERY_CNT_IN_BOX ) 
												  END 
										END ) 
								 + LABOR_PRICE 
								 + OTHER_PRICE, 0 ) 
							
							* (CASE WHEN OG.ORDER_STATE < 4 THEN OG.QTY ELSE OG.QTY * -1 END) AS SUM_TOTAL_WONGA
								 
					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
				) C ON TOG.ORDER_GOODS_NO = C .ORDER_GOODS_NO
			SET TOG.STAT_TOTAL_EXPENSE = C.SUM_TOTAL_WONGA 
		  WHERE 1 = 1
		";
		
		if ($re_calc <> "Y") {
			$query .= "  AND TOG.STAT_TOTAL_EXPENSE IS NULL ";
		}

		//echo $query."<br/><br/>";
		mysql_query($query,$db);

		//마진 합계
		$query=" UPDATE TBL_ORDER_GOODS 
					SET STAT_TOTAL_MAJIN = STAT_TOTAL_SALE - STAT_TOTAL_EXPENSE
				  WHERE USE_TF = 'Y' AND DEL_TF = 'N'";

		if ($re_calc <> "Y") {
			$query .= "  AND STAT_TOTAL_MAJIN IS null ";
		}

		//echo $query."<br/><br/>";
		mysql_query($query,$db);

	}
?>