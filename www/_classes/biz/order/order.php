<?
	# =============================================================================
	# File Name    : order.php
	# =============================================================================

	function listOrder($db, $mem_no, $use_tf, $del_tf, $nPage, $nRowCount) {

		$total_cnt = totalCntOrder($db, $mem_no, $use_tf, $del_tf);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, ORDER_NO, RESERVE_NO, MEM_NO, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, 
										MEMO, ORDER_STATE, TOTAL_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, ORDER_DATE, PAY_DATE, BULK_TF,
										PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, CANCEL_DATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ORDER WHERE 1 = 1 ";

		if ($mem_no <> "") {
			$query .= " AND MEM_NO = '".$mem_no."' ";
		} else {
			$query .= " AND MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY ORDER_NO DESC limit ".$offset.", ".$nRowCount;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntOrder($db, $mem_no, $use_tf, $del_tf) {

		$query ="SELECT COUNT(*) CNT FROM TBL_ORDER WHERE 1 = 1 ";

		if ($mem_no <> "") {
			$query .= " AND MEM_NO = '".$mem_no."' ";
		} else {
			$query .= " AND MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listManagerOrder($db, $order_type, $search_date_type, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $opt_manager_no, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntManagerOrder($db, $order_type, $search_date_type, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $opt_manager_no, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, O.ORDER_NO, O.RESERVE_NO, O.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, O.BULK_TF, O.ORDER_STATE, O.OPT_MANAGER_NO, O.TOTAL_PRICE, O.TOTAL_BUY_PRICE, O.TOTAL_SALE_PRICE, O.TOTAL_EXTRA_PRICE, O.TOTAL_DELIVERY_PRICE, O.TOTAL_QTY,  O.TOTAL_SA_DELIVERY_PRICE, O.TOTAL_DISCOUNT_PRICE,
										 
										 O.ORDER_DATE, O.PAY_DATE, O.PAY_TYPE, O.DELIVERY_TYPE, O.DELIVERY_DATE, O.FINISH_DATE, O.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE, 
										 
										 (O.TOTAL_SALE_PRICE - O.TOTAL_SA_DELIVERY_PRICE - O.TOTAL_DISCOUNT_PRICE) AS TOTAL_SUM_SALE_PRICE,

										 ((O.TOTAL_SALE_PRICE - O.TOTAL_SA_DELIVERY_PRICE - O.TOTAL_DISCOUNT_PRICE) - (O.TOTAL_PRICE + O.TOTAL_EXTRA_PRICE)) AS TOTAL_PLUS_PRICE, 

										 ROUND((((O.TOTAL_SALE_PRICE - O.TOTAL_SA_DELIVERY_PRICE - O.TOTAL_DISCOUNT_PRICE) - (O.TOTAL_PRICE + O.TOTAL_EXTRA_PRICE)) / 
										 (O.TOTAL_SALE_PRICE - O.TOTAL_SA_DELIVERY_PRICE - O.TOTAL_DISCOUNT_PRICE) * 100),2) AS LEE

					FROM TBL_ORDER O WHERE 1 = 1  ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if($search_date_type == "order_date") { 
			if ($start_date <> "") {
				$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND O.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($buy_cp_no <> "") {

			$query .= " AND RESERVE_NO IN (
											SELECT RESERVE_NO 
												FROM TBL_ORDER_GOODS 
											 WHERE BUY_CP_NO = '".$buy_cp_no."'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
											) "; 
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (RESERVE_NO IN (
														SELECT RESERVE_NO 
															FROM TBL_ORDER_GOODS 
														WHERE GOODS_NAME LIKE '%".$search_str."%'
															 AND USE_TF = 'Y'
															AND DEL_TF = 'N'
													) OR RESERVE_NO like '%".$search_str."%' OR O_MEM_NM like '%".$search_str."%' OR R_MEM_NM like '%".$search_str."%'  )"; 
			
			} else {

				if ($search_field == "GOODS_NAME") {
					$query .= " AND RESERVE_NO IN (
													SELECT RESERVE_NO 
														FROM TBL_ORDER_GOODS 
													 WHERE GOODS_NAME LIKE '%".$search_str."%'
														 AND USE_TF = 'Y'
														 AND DEL_TF = 'N'
											) "; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;


		//echo $query."<br/>";
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

	function totalCntManagerOrder($db, $order_type, $search_date_type, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $opt_manager_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(*) CNT FROM TBL_ORDER O WHERE 1 = 1 ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if($search_date_type == "order_date") { 
			if ($start_date <> "") {
				$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND O.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($buy_cp_no <> "") {

			$query .= " AND RESERVE_NO IN (
											SELECT RESERVE_NO 
												FROM TBL_ORDER_GOODS 
											 WHERE BUY_CP_NO = '".$buy_cp_no."'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
											) "; 
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (RESERVE_NO IN (
														SELECT RESERVE_NO 
															FROM TBL_ORDER_GOODS 
														WHERE GOODS_NAME LIKE '%".$search_str."%'
															 AND USE_TF = 'Y'
															AND DEL_TF = 'N'
													) OR RESERVE_NO like '%".$search_str."%' OR O_MEM_NM like '%".$search_str."%' OR R_MEM_NM like '%".$search_str."%'  )"; 
			
			} else {

				if ($search_field == "GOODS_NAME") {
					$query .= " AND RESERVE_NO IN (
													SELECT RESERVE_NO 
														FROM TBL_ORDER_GOODS 
													 WHERE GOODS_NAME LIKE '%".$search_str."%'
														 AND USE_TF = 'Y'
														 AND DEL_TF = 'N'
											) "; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listAllOrder($db, $order_type, $search_date_type, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $opt_manager_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(O.TOTAL_PRICE) AS ALL_PRICE, 
						 SUM(O.TOTAL_BUY_PRICE) AS ALL_BUY_PRICE, 
						 SUM(O.TOTAL_SALE_PRICE) AS ALL_SALE_PRICE,  
						 SUM(O.TOTAL_EXTRA_PRICE) AS ALL_EXTRA_PRICE, 
						 SUM(O.TOTAL_DELIVERY_PRICE) AS ALL_DELIVERY_PRICE,  
						 SUM(O.TOTAL_QTY) AS ALL_QTY, 
						 SUM(O.TOTAL_SA_DELIVERY_PRICE) AS ALL_SA_DELIVERY_PRICE,  
						 SUM(O.TOTAL_DISCOUNT_PRICE) AS ALL_DISCOUNT_PRICE, 
						 (SUM(O.TOTAL_SALE_PRICE) - SUM(O.TOTAL_SA_DELIVERY_PRICE) - SUM(O.TOTAL_DISCOUNT_PRICE)) AS ALL_SUM_PRICE,
						 ((SUM(O.TOTAL_SALE_PRICE) - SUM(O.TOTAL_SA_DELIVERY_PRICE) - SUM(O.TOTAL_DISCOUNT_PRICE)) - (SUM(O.TOTAL_PRICE) + SUM(O.TOTAL_EXTRA_PRICE))) AS ALL_PLUS_PRICE, 
						 ROUND((((SUM(O.TOTAL_SALE_PRICE) - SUM(O.TOTAL_SA_DELIVERY_PRICE) - SUM(O.TOTAL_DISCOUNT_PRICE)) - (SUM(O.TOTAL_PRICE) + SUM(O.TOTAL_EXTRA_PRICE))) / (SUM(O.TOTAL_SALE_PRICE) - SUM(O.TOTAL_SA_DELIVERY_PRICE) - SUM(O.TOTAL_DISCOUNT_PRICE)) * 100),2) AS ALL_LEE
				    
					FROM TBL_ORDER O WHERE 1 = 1 ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if($search_date_type == "order_date") { 
			if ($start_date <> "") {
				$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND O.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($buy_cp_no <> "") {

			$query .= " AND RESERVE_NO IN (
											SELECT RESERVE_NO 
												FROM TBL_ORDER_GOODS 
											 WHERE BUY_CP_NO = '".$buy_cp_no."'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
											) "; 
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (RESERVE_NO IN (
														SELECT RESERVE_NO 
															FROM TBL_ORDER_GOODS 
														WHERE GOODS_NAME LIKE '%".$search_str."%'
															 AND USE_TF = 'Y'
															AND DEL_TF = 'N'
													) OR RESERVE_NO like '%".$search_str."%' OR O_MEM_NM like '%".$search_str."%' OR R_MEM_NM like '%".$search_str."%'  )"; 
			
			} else {

				if ($search_field == "GOODS_NAME") {
					$query .= " AND RESERVE_NO IN (
													SELECT RESERVE_NO 
														FROM TBL_ORDER_GOODS 
													 WHERE GOODS_NAME LIKE '%".$search_str."%'
														 AND USE_TF = 'Y'
														 AND DEL_TF = 'N'
											) "; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
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

	function listManagerOrderGoods($db, $reserve_no, $mem_no, $use_tf, $del_tf) {

		$query = "SELECT C.ORDER_GOODS_NO, C.CLAIM_ORDER_GOODS_NO, C.RESERVE_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
						 C.QTY, C.OPT_STICKER_NO, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO, 
						 C.DELIVERY_TYPE, C.CATE_01, C.CATE_02,
						 C.CATE_03, C.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_CONFIRM_DATE,
						 G.FILE_NM_100, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE,
						 ((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) AS SUM_PRICE, 
						 (((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) - ((C.PRICE + C.EXTRA_PRICE) * C.QTY)) AS PLUS_PRICE, 
						 ROUND(((((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE) - ((C.PRICE + C.EXTRA_PRICE) * C.QTY)) / ((C.SALE_PRICE * C.QTY) + C.SA_DELIVERY_PRICE - C.DISCOUNT_PRICE)) * 100,2) AS LEE,
						 C.DELIVERY_CP, C.DELIVERY_NO, C.CP_ORDER_NO, C.WORK_FLAG, C.WORK_QTY, C.WORK_START_DATE, C.WORK_END_DATE, 
						 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_YMD,
						 G.CATE_04 AS GOODS_STATE, G.TAX_TF,
						 R.HAS_GOODS_REQUEST

							  FROM TBL_ORDER_GOODS C 
						 LEFT JOIN TBL_GOODS G ON C.GOODS_NO = G.GOODS_NO
						 LEFT JOIN (SELECT ORDER_GOODS_NO, COUNT(*) > 0 AS HAS_GOODS_REQUEST 
						              FROM TBL_GOODS_REQUEST_GOODS 
				                     WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'
								  GROUP BY ORDER_GOODS_NO) R ON C.ORDER_GOODS_NO = R.ORDER_GOODS_NO
							 WHERE G.DEL_TF = 'N' 
							     ";
		
		// 2017-03-14 ???????? ???????????? ????(???? ???? ???? ???? ?????? ??????)?????? ?? ???? ?????? ???????? ???????? ?????? ???? ?????? ????
		//G.USE_TF= 'Y' AND
		
		if ($reserve_no <> "") {
			$query .= " AND C.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($mem_no <> "") {
			$query .= " AND C.MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY C.ORDER_GOODS_NO DESC ";

		//echo $query."<br/>"."<br/>";
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


	function listOrderGoods($db, $reserve_no, $mem_no, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntOrderGoods($db, $reserve_no, $mem_no, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, C.ORDER_GOODS_NO, C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
										 C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, O.OPT_MANAGER_NO, C.OPT_MEMO, C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, C.PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 G.FILE_NM_100, 
										 C.ORDER_STATE, C.BUY_CP_NO
								FROM TBL_ORDER O, TBL_ORDER_GOODS C, TBL_GOODS G 
							 WHERE G.USE_TF= 'Y' 
								 AND G.DEL_TF = 'N' 
								 AND C.GOODS_NO = G.GOODS_NO 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND O.ORDER_STATE <> '-1' ";

		if ($reserve_no <> "") {
			$query .= " AND C.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($mem_no <> "") {
			$query .= " AND C.MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " ORDER BY C.ORDER_GOODS_NO DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntOrderGoods($db, $reserve_no, $mem_no, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT 
							 FROM TBL_ORDER O, TBL_ORDER_GOODS G 
							 WHERE G.USE_TF= 'Y' 
								 AND G.DEL_TF = 'N' 
								 AND O.RESERVE_NO = G.RESERVE_NO
								 AND O.ORDER_STATE <> '-1' ";

		if ($reserve_no <> "") {
			$query .= " AND G.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($mem_no <> "") {
			$query .= " AND G.MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function insertOrder($db, $on_uid, $reserve_no, $mem_no, $cp_no, $o_mem_nm, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $r_mem_nm, $r_zipcode, $r_addr1, $r_addr2, $r_phone, $r_hphone, $r_email, $memo, $bulk_tf, $opt_manager_no, $order_state, $total_price, $total_buy_price, $total_sale_price, $total_extra_price, $total_delivery_price, $total_sa_delivery_price, $total_discount_price, $total_qty, $pay_type, $delivery_type, $use_tf, $reg_adm) {


		if ($order_state == "1") {
		
			// PAY_DATE added
			$query="INSERT INTO TBL_ORDER (ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO, ORDER_STATE, TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_SA_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, USE_TF, REG_ADM, REG_DATE) 
													 values ('$on_uid','$reserve_no', '$mem_no', '$cp_no', '$o_mem_nm', '$o_zipcode', '$o_addr1', '$o_addr2', '$o_phone', '$o_hphone', '$o_email',
																	 '$r_mem_nm', '$r_zipcode', '$r_addr1', '$r_addr2', '$r_phone', '$r_hphone', '$r_email',
																	 '$memo', '$bulk_tf', '$opt_manager_no', '$order_state', '$total_price', '$total_buy_price', '$total_sale_price', '$total_extra_price', '$total_delivery_price',  '$total_sa_delivery_price', '$total_discount_price', '$total_qty', now(), 
																	 now(), '$pay_type', '$delivery_type', '$use_tf', '$reg_adm', now()); ";
		} else {

			$query="INSERT INTO TBL_ORDER (ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO, ORDER_STATE, TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_SA_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, ORDER_DATE, PAY_TYPE, DELIVERY_TYPE, USE_TF, REG_ADM, REG_DATE) 
													 values ('$on_uid','$reserve_no', '$mem_no', '$cp_no', '$o_mem_nm', '$o_zipcode', '$o_addr1', '$o_addr2', '$o_phone', '$o_hphone', '$o_email',
																	 '$r_mem_nm', '$r_zipcode', '$r_addr1', '$r_addr2', '$r_phone', '$r_hphone', '$r_email',
																	 '$memo', '$bulk_tf', '$order_state', '$total_price', '$total_buy_price', '$total_sale_price', '$total_extra_price', '$total_delivery_price', '$total_sa_delivery_price', '$total_discount_price', '$total_qty', now(), 
																	 '$pay_type', '$delivery_type', '$use_tf', '$reg_adm', now()); ";
		}
		
		// echo $query;
		// exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function insertOrderGoods($db, $on_uid, $reserve_no, $cp_order_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $tax_tf, $order_state, $use_tf, $reg_adm) {
		
		$opt_request_memo = $memos["opt_request_memo"];
		$opt_support_memo = $memos["opt_support_memo"];

		if ($order_state == "4") {
			$query = "UPDATE TBL_ORDER_GOODS SET QTY = QTY - $qty
							 WHERE RESERVE_NO = '$reserve_no' 
								 AND GOODS_NO = '$goods_no' 
								 AND USE_TF = 'Y'
								 AND DEL_TF = 'N' ";
				mysql_query($query,$db);
		}


		if (($order_state == "4") || ($order_state == "6") || ($order_state == "7") || ($order_state == "8")) {

			$query="INSERT INTO TBL_ORDER_GOODS (ON_UID, RESERVE_NO, CP_ORDER_NO, BUY_CP_NO, MEM_NO, ORDER_SEQ, 
												GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME,
												QTY, OPT_STICKER_NO, OPT_STICKER_MSG, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX, OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, CATE_01, CATE_02,
												CATE_03, CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE, 
												DISCOUNT_PRICE, STICKER_PRICE, PRINT_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, TAX_TF, ORDER_STATE, ORDER_DATE, USE_TF, REG_ADM, REG_DATE ) 
										values ('$on_uid', '$reserve_no', '$cp_order_no', '$buy_cp_no', '$mem_no', '$cart_seq', 
										'$goods_no', '$goods_code', '$goods_name', '$goods_sub_name', 
										'$qty', '$opt_sticker_no', '$opt_sticker_msg', '$opt_outbox_tf','$delivery_cnt_in_box', '$opt_wrap_no', '$opt_print_msg', '$opt_outstock_date', '$opt_memo', '$opt_request_memo', '$opt_support_memo', '$delivery_type', '$delivery_cp', '$sender_nm', '$sender_phone', '$cate_01', '$cate_02', '$cate_03', '$cate_04', '$price', '$buy_price', '$sale_price',	'$extra_price',  '$delivery_price', '$sa_delivery_price', '$discount_price', '$sticker_price', '$print_price', '$sale_susu', '$labor_price', '$other_price', '$tax_tf', '$order_state', now(), '$use_tf', '$reg_adm', now()); ";

			//$query_st = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT + $qty WHERE GOODS_NO = '$goods_no'";
			//mysql_query($query_st,$db);
		
		
		
		} 
		else {//order_state?? 4,6,7,8?? ???? ???? ?? (1,2,3,5,9....?? ????)

			if ($order_state == "1") {

			$query="INSERT INTO TBL_ORDER_GOODS (ON_UID, RESERVE_NO, CP_ORDER_NO, BUY_CP_NO, MEM_NO, ORDER_SEQ, 
												GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY, REFUNDABLE_QTY, OPT_STICKER_NO, OPT_STICKER_MSG, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX, OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, CATE_01, CATE_02,
												CATE_03, CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE, DISCOUNT_PRICE,
												STICKER_PRICE, PRINT_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, TAX_TF, ORDER_STATE, PAY_DATE, ORDER_DATE, USE_TF, REG_ADM, REG_DATE, WORK_REQ_QTY) 
										values ('$on_uid', '$reserve_no', '$cp_order_no', '$buy_cp_no', '$mem_no', '$cart_seq', '$goods_no', '$goods_code',	'$goods_name', '$goods_sub_name', '$qty', '$qty', '$opt_sticker_no', '$opt_sticker_msg', '$opt_outbox_tf', '$delivery_cnt_in_box', '$opt_wrap_no', '$opt_print_msg', '$opt_outstock_date', '$opt_memo', '$opt_request_memo', '$opt_support_memo', '$delivery_type', '$delivery_cp', '$sender_nm', '$sender_phone', '$cate_01', '$cate_02', '$cate_03', '$cate_04', '$price', '$buy_price', '$sale_price', '$extra_price', '$delivery_price', '$sa_delivery_price', '$discount_price', '$sticker_price', '$print_price', '$sale_susu', '$labor_price', '$other_price', '$tax_tf', '$order_state', now(), now(), '$use_tf', '$reg_adm', now(), '$qty'); ";


			} 
			else {//2,3,5,9,99

			$query="INSERT INTO TBL_ORDER_GOODS (ON_UID, RESERVE_NO, CP_ORDER_NO, BUY_CP_NO, MEM_NO, ORDER_SEQ, 
												GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY, OPT_STICKER_NO, OPT_STICKER_MSG, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX, OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, CATE_01, CATE_02, CATE_03, 
												CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE, DISCOUNT_PRICE, STICKER_PRICE, PRINT_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, TAX_TF, ORDER_STATE, ORDER_DATE, USE_TF, REG_ADM, REG_DATE) 
										 values ('$on_uid', '$reserve_no', '$cp_order_no', '$buy_cp_no', '$mem_no', '$cart_seq', '$goods_no', '$goods_code', '$goods_name', '$goods_sub_name', '$qty', '$opt_sticker_no', '$opt_sticker_msg', '$opt_outbox_tf', '$delivery_cnt_in_box', '$opt_wrap_no', '$opt_print_msg', '$opt_outstock_date', '$opt_memo', '$opt_request_memo', '$opt_support_memo', '$delivery_type', '$delivery_cp', '$sender_nm', '$sender_phone', '$cate_01', '$cate_02', '$cate_03', '$cate_04', '$price', '$buy_price', '$sale_price', '$extra_price', '$delivery_price', '$sa_delivery_price',			'$discount_price', '$sticker_price', '$print_price', '$sale_susu', '$labor_price', '$other_price', '$tax_tf', '$order_state', now(), '$use_tf',	'$reg_adm', now()); ";

			}
		
		}
		// echo $query."<br/>";
		// exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			$query="SELECT MAX(ORDER_GOODS_NO) AS LAST_ORDER_NO FROM TBL_ORDER_GOODS";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			return $rows[0];
		}
	}
	function GetCompanyNameWithReserveNo($db, $reserve_no){
		$query="SELECT CP_NO FROM TBL_ORDER WHERE RESERVE_NO= '$reserve_no' ";
		$result=mysql_query($query,$db);
		$cnt=mysql_num_rows($result);
		$record=array();
		if($result<>""){
			for($i=0;$i<$cnt;$i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
		}
		$cp_no= $record[0]["CP_NO"];
		$query2="SELECT CP_NM, CP_NM2 FROM TBL_COMPANY WHERE CP_NO = ".$cp_no." ; ";
		$result2=mysql_query($query2,$db);
		$cnt=mysql_num_rows($result2);
		$record2=array();
		if($result2<>""){
			for($i=0;$i<$cnt;$i++){
				$record2[$i]=mysql_fetch_assoc($result2);
			}
		}
		return $record2[0]["CP_NM"]." ".$record2[0]["CP_NM2"];
	}

	function selectOrder($db, $reserve_no) {

		$query = "SELECT ORDER_NO, ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL,
										 R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO,
										 TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, TOTAL_SA_DELIVERY_PRICE,
										 ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, 
										 CANCEL_DATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ORDER WHERE USE_TF= 'Y' AND DEL_TF = 'N' AND RESERVE_NO = '$reserve_no' ";
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

	function deleteOrderInfo($db, $reserve_no) {

		$query="UPDATE TBL_PAYMENT SET DEL_TF = 'Y' WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);

		$query="UPDATE TBL_ORDER SET DEL_TF = 'Y' WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);
		
		$query="UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y' WHERE RESERVE_NO = '$reserve_no' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteOrder($db, $reserve_no, $del_adm) {

		$query="UPDATE TBL_PAYMENT SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);

		$query="UPDATE TBL_ORDER SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);
		
		$query="UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

/*
	function updateOrderConfrim($db, $reserve_no, $order_state, $up_adm) {
		
		if ($order_state == 0) { // ??????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state'
					WHERE RESERVE_NO				= '$reserve_no'";
		}

		if ($order_state == 1) { // ??????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state'
					WHERE RESERVE_NO				= '$reserve_no'";
		}

		if ($order_state == 2) {	// ????????, ??????????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state',
								PAY_DATE 					= now()
					WHERE RESERVE_NO				= '$reserve_no'";
		}

		if ($order_state == 3) {	// ???? ??????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state',
								DELIVERY_DATE 		= now()
					WHERE RESERVE_NO				= '$reserve_no'";
		}

		if ($order_state == 4) {	// ???? ????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state',
								FINISH_DATE 			= now()
					WHERE RESERVE_NO				= '$reserve_no'";
		}

		if ($order_state == 7) {	// ???? ????
			$query="UPDATE TBL_ORDER SET 
								ORDER_STATE 			= '$order_state',
								CANCEL_DATE				= now()
					WHERE RESERVE_NO				= '$reserve_no'";
		}


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
*/

	function updateOrderFinish($db, $str_cart_no) {

		$query="UPDATE TBL_CART SET 
							USE_TF		= 'N',
							DEL_TF		= 'Y',
							DEL_DATE	= now()
				WHERE CART_NO		IN  $str_cart_no ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function changeOrderNo($db, $old_orderno, $new_orderno) {

		$query="UPDATE TBL_CART SET 
									 RESERVE_NO = '$new_orderno'
						 WHERE RESERVE_NO =	'$old_orderno'
							 AND USE_TF		= 'Y'
							 AND DEL_TF		= 'N' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	/*
	function updateOrderState($db, $reserve_no, $order_state) {

		if ($order_state == "0") {
			$str_finish_date		= " FINISH_DATE = NULL, ";
			$str_delivery_date	= "	DELIVERY_DATE = NULL, ";
			$str_pay_date				= " PAY_DATE = NULL, ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
		}

		if ($order_state == "1") {
			$str_finish_date		= "";
			$str_delivery_date	= "";
			$str_pay_date				= " PAY_DATE = now() , ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
		}

		if ($order_state == "2") {
			$str_finish_date		= "";
			$str_delivery_date	= "";
			$str_pay_date				= " PAY_DATE = now() , ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
		}

		if ($order_state == "3") {
			$str_finish_date		= " FINISH_DATE = now(), ";
			$str_delivery_date	= "	DELIVERY_DATE = now(), ";
			$str_pay_date				= "";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
		}

		if ($order_state == "4") {
			$str_finish_date		= "";
			$str_delivery_date	= "";
			$str_pay_date				= "";
			$str_cancel_date		= "	CANCEL_DATE = now() ";
		}

		$query = "UPDATE TBL_ORDER SET 
												ORDER_STATE		= '$order_state', ";

		$query .= $str_finish_date;
		$query .= $str_delivery_date;
		$query .= $str_pay_date;
		$query .= $str_cancel_date;

		$query .=	" WHERE RESERVE_NO		= '$reserve_no'
									 AND USE_TF		= 'Y'
									 AND DEL_TF		= 'N' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	*/
	
	/*
	function totalCntManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(DISTINCT G.RESERVE_NO) CNT 
				   FROM TBL_ORDER O 
				   JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
				   
				  
				  WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N' "; 
				  
				  //AND O.TOTAL_QTY <> 0 
				  //JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		}

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		}

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 

		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
							  
							  )"; 
				//
				//			  OR C.CP_CODE = '".$search_str."'
				//			  OR C.CP_NM LIKE '%".$search_str."%'
				//			  OR C.CP_NM2 LIKE '%".$search_str."%'
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE LIKE '%".$search_str."%' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT DISTINCT O.ORDER_NO, O.RESERVE_NO, O.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, O.ORDER_STATE, O.TOTAL_PRICE, O.TOTAL_BUY_PRICE, O.TOTAL_SALE_PRICE, O.TOTAL_EXTRA_PRICE, O.TOTAL_DELIVERY_PRICE, O.TOTAL_SA_DELIVERY_PRICE, O.TOTAL_DISCOUNT_PRICE, O.TOTAL_QTY,
										 O.ORDER_DATE, O.PAY_DATE, O.PAY_TYPE, O.DELIVERY_TYPE, O.DELIVERY_DATE, O.FINISH_DATE, O.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE, O.OPT_MANAGER_NO,
										 ((O.TOTAL_SALE_PRICE) - (O.TOTAL_BUY_PRICE + O.TOTAL_DELIVERY_PRICE)) AS TOTAL_PLUS_PRICE, 
										 (SELECT MAX(PAY_DATE) FROM TBL_ORDER_GOODS GG WHERE GG.RESERVE_NO = O.RESERVE_NO) AS G_REG_DATE, O.REG_ADM
								FROM TBL_ORDER O 
								JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
							    
							   
							   WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N'
								
								"; //AND O.TOTAL_QTY <> 0
								//JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		}

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
				
							  )"; 
				//
				//			  OR C.CP_CODE = '".$search_str."'
				//			  OR C.CP_NM LIKE '%".$search_str."%'
				//			  OR C.CP_NM2 LIKE '%".$search_str."%'
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
	
			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", O.RESERVE_NO DESC limit ".$offset.", ".$nRowCount;

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


	function sumManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(TOTAL_PRICE) AS SUM_TOTAL_PRICE, 
		                 SUM(TOTAL_BUY_PRICE) AS SUM_TOTAL_BUY_PRICE, 
						 SUM(TOTAL_SALE_PRICE) AS SUM_TOTAL_SALE_PRICE, 
						 SUM(TOTAL_EXTRA_PRICE) AS SUM_TOTAL_EXTRA_PRICE, 
						 SUM(TOTAL_DELIVERY_PRICE) AS SUM_TOTAL_DELIVERY_PRICE, 
						 SUM(TOTAL_SA_DELIVERY_PRICE) AS SUM_TOTAL_SA_DELIVERY_PRICE, 
						 SUM(TOTAL_DISCOUNT_PRICE) AS SUM_TOTAL_DISCOUNT_PRICE, 
						 SUM(TOTAL_QTY) AS SUM_TOTAL_QTY
				 FROM (
				  SELECT O.RESERVE_NO, O.TOTAL_PRICE,  O.TOTAL_BUY_PRICE,  O.TOTAL_SALE_PRICE,  O.TOTAL_EXTRA_PRICE,  O.TOTAL_DELIVERY_PRICE,  O.TOTAL_SA_DELIVERY_PRICE,  O.TOTAL_DISCOUNT_PRICE  , O.TOTAL_QTY

					FROM TBL_ORDER O 
					JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
					
				   WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N'
								
				  "; 
				  //AND O.TOTAL_QTY <> 0
				  //JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO

	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_TF >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_TF <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		} 

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
							  
							  
							  )";
				//
				//			  OR C.CP_CODE = '".$search_str."'
				//			  OR C.CP_NM LIKE '%".$search_str."%'
				//			  OR C.CP_NM2 LIKE '%".$search_str."%'
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";

			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " GROUP BY O.RESERVE_NO, O.TOTAL_PRICE,  O.TOTAL_BUY_PRICE,  O.TOTAL_SALE_PRICE,  O.TOTAL_EXTRA_PRICE,  O.TOTAL_DELIVERY_PRICE,  O.TOTAL_SA_DELIVERY_PRICE,  O.TOTAL_DISCOUNT_PRICE, O.TOTAL_QTY ";

		$query .= " ) A";

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
	*/

	//?????? ?????? ???? ????
	function listManagerDeliverySelected($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str) {

		$query = "SELECT O.RESERVE_NO, O.CP_NO, O.O_MEM_NM, O.O_PHONE, O.O_HPHONE, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.OPT_MANAGER_NO, O.R_PHONE, O.ORDER_DATE,
				G.CP_ORDER_NO, G.ORDER_CONFIRM_DATE, G.GOODS_NO, G.CATE_01, G.CATE_04, G.GOODS_CODE, G.GOODS_NAME, G.OPT_OUTSTOCK_DATE, G.OPT_STICKER_NO, G.OPT_STICKER_MSG, G.OPT_OUTBOX_TF, G.OPT_WRAP_NO, G.OPT_PRINT_MSG, G.OPT_MEMO, G.QTY, G.DELIVERY_TYPE, G.DELIVERY_CP, G.DELIVERY_NO, G.ORDER_STATE, G.WORK_FLAG, G.WORK_START_DATE, G.WORK_END_DATE, G.FINISH_DATE, 
				G.ORDER_GOODS_NO, G.ORDER_STATE,
				(SELECT MAX(PAY_DATE) FROM TBL_ORDER_GOODS GG WHERE GG.RESERVE_NO = O.RESERVE_NO) AS G_REG_DATE
		  FROM TBL_ORDER O 
		  JOIN TBL_ORDER_GOODS G 
		 WHERE O.RESERVE_NO = G.RESERVE_NO 
		   AND O.IS_PACKAGE = 'N' 
		   AND G.IS_PACKAGE = 'N'
								
			"; 
	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND G.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 
		
		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		$query .=" AND G.USE_TF='Y' AND G.DEL_TF='N'	";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.RESERVE_NO LIKE '%".$search_str."%' OR O.O_MEM_NM LIKE '%".$search_str."%' OR O.R_MEM_NM LIKE '%".$search_str."%' OR G.ORDER_GOODS_NO = '".$search_str."'  )"; 
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND G.RESERVE_NO LIKE '%".$search_str."%' ";
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND (G.GOODS_NO IN (SELECT GG.GOODS_NO FROM TBL_GOODS GG WHERE GG.GOODS_CODE like '%".$search_str."%' ) OR  G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			} else if ($search_field == "R_ADDR") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " AND G.ORDER_STATE IN ('1', '2', '3', '7') ";

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", O.RESERVE_NO DESC ";

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}
	function listManagerDeliverySelected2($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str) {

		$query = "SELECT O.RESERVE_NO, O.CP_NO, O.O_MEM_NM, O.O_PHONE, O.O_HPHONE, GD.RECEIVER_NM, O.R_ZIPCODE, GD.RECEIVER_ADDR, O.OPT_MANAGER_NO, GD.RECEIVER_PHONE, GD.RECEIVER_HPHONE, O.ORDER_DATE,
				G.CP_ORDER_NO, G.ORDER_CONFIRM_DATE, G.GOODS_NO, G.CATE_01, G.CATE_04, G.GOODS_CODE, G.GOODS_NAME, G.OPT_OUTSTOCK_DATE, G.OPT_STICKER_NO, G.OPT_STICKER_MSG, 
				G.OPT_OUTBOX_TF, G.OPT_WRAP_NO, G.OPT_PRINT_MSG, G.OPT_MEMO, GD.GOODS_DELIVERY_NAME, G.DELIVERY_TYPE, GD.DELIVERY_CP, G.DELIVERY_NO, G.ORDER_STATE, G.WORK_FLAG, G.WORK_START_DATE, 
				 G.WORK_END_DATE, G.FINISH_DATE, 
				G.ORDER_GOODS_NO, G.ORDER_STATE,
				(SELECT MAX(PAY_DATE) FROM TBL_ORDER_GOODS GG WHERE GG.RESERVE_NO = O.RESERVE_NO) AS G_REG_DATE
		  FROM TBL_ORDER O 
		  JOIN TBL_ORDER_GOODS G 
		  LEFT JOIN TBL_ORDER_GOODS_DELIVERY GD ON G.ORDER_GOODS_NO=GD.ORDER_GOODS_NO
		 WHERE O.RESERVE_NO = G.RESERVE_NO 
		   AND O.IS_PACKAGE = 'N' 
		   AND G.IS_PACKAGE = 'N'
								
			"; 
	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND G.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 
		
		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		$query .=" AND G.USE_TF='Y' AND G.DEL_TF='N'	";
		$query .= "AND GD.USE_TF='Y' AND GD.DEL_TF='N'	";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.RESERVE_NO LIKE '%".$search_str."%' OR O.O_MEM_NM LIKE '%".$search_str."%' OR O.R_MEM_NM LIKE '%".$search_str."%' OR G.ORDER_GOODS_NO = '".$search_str."'  )"; 
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND G.RESERVE_NO LIKE '%".$search_str."%' ";
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND (G.GOODS_NO IN (SELECT GG.GOODS_NO FROM TBL_GOODS GG WHERE GG.GOODS_CODE like '%".$search_str."%' ) OR  G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			} else if ($search_field == "R_ADDR") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " AND G.ORDER_STATE IN ('1', '2', '3', '7') ";

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", O.RESERVE_NO DESC ";


		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function listOrderGoodsDeliveryStatus($db, $specific_date){
	
		$query = "SELECT OG.ORDER_GOODS_NO, OG.RESERVE_NO, 
										(SELECT COUNT( * ) FROM TBL_ORDER_GOODS_DELIVERY WHERE ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS TOTAL_DELIVERY_CNT, 
										(SELECT COUNT( * ) FROM TBL_ORDER_GOODS_DELIVERY WHERE ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND DELIVERY_DATE <>  '0000-00-00 00:00:00') AS TOTAL_DELIVERY_COMPLETED_CNT
							FROM TBL_ORDER_GOODS OG
							WHERE OG.ORDER_DATE >=  '".$specific_date."'
								AND OG.ORDER_DATE <=  '".$specific_date." 23:59:59'
								AND OG.USE_TF =  'Y'
								AND OG.DEL_TF =  'N'
								AND OG.ORDER_STATE = 2";
		
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


	function listOrderDelivery($db, $reserve_no, $order_goods_no) {

		$query = "SELECT DELIVERY_SEQ, DELIVERY_CP, DELIVERY_NO 
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE RESERVE_NO = '$reserve_no' AND ORDER_GOODS_NO = '$order_goods_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		$query .= 	"ORDER BY ORDER_GOODS_DELIVERY_NO";

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




	function selectOrderDelivery($db, $delivery_seq) {

		$query = "SELECT DELIVERY_CP, DELIVERY_NO 
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE DELIVERY_SEQ = '$delivery_seq' ";

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

	function cntOrderGoodsDeliveryLastSeq($db, $specific_date) {
		
		$query = "SELECT MAX(SEQ_OF_DAY) AS CNT
					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_ORDER_GOODS_DELIVERY OGD ON OG.ORDER_GOODS_NO = OGD.ORDER_GOODS_NO
					WHERE O.TOTAL_QTY <> 0
						AND O.ORDER_DATE >= '".$specific_date."' 
						AND O.ORDER_DATE <= '".$specific_date." 23:59:59' 
						AND O.USE_TF =  'Y'
						AND O.DEL_TF =  'N'
						AND OG.USE_TF =  'Y'
						AND OG.DEL_TF =  'N'
						";

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function updateOrderGoodsDeliveryStatus($db, $order_goods_delivery_no, $specific_date, $receiver_nm, $delivery_cp, $delivery_type, $order_phone, $order_manager_nm, $order_manager_phone, $delivery_profit, $delivery_profit_code, $delivery_fee, $delivery_fee_code, $payment_type, $send_cp_addr) {

		$max_seq = cntOrderGoodsDeliveryLastSeq($db, $specific_date);

		$seq = ($max_seq <> 0 ? $max_seq + 1 : 300);
		
		$delivery_seq = $specific_date ."-".$seq;

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
							SET 
								DELIVERY_SEQ	= '$delivery_seq', 
								SEQ_OF_DAY	= '$seq',
								RECEIVER_NM = '$receiver_nm',
								DELIVERY_CP =  '$delivery_cp',
								DELIVERY_TYPE = '$delivery_type',
								DELIVERY_PROFIT = '$delivery_profit',
								DELIVERY_PROFIT_CODE = '$delivery_profit_code',
								DELIVERY_FEE = '$delivery_fee',
								DELIVERY_FEE_CODE = '$delivery_fee_code',
								PAYMENT_TYPE = '$payment_type',
								SEND_CP_ADDR = '$send_cp_addr',
								ORDER_PHONE = '$order_phone',
								ORDER_MANAGER_NM = '$order_manager_nm',
								ORDER_MANAGER_PHONE = '$order_manager_phone'
							WHERE ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no' AND SEQ_OF_DAY = ''";
		
		//echo $query;
	    //exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	
	function updateOrderGoodsDeliveryNumber($db, $delivery_seq, $delivery_cp, $delivery_no) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
				  SET 
				  	DELIVERY_NO	= '$delivery_no'";
		
		$query .= " WHERE DELIVERY_SEQ		= '$delivery_seq' AND DELIVERY_CP	= '$delivery_cp' ";
		
		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	

	function updateOrderGoodsDeliveryNumberMart($db, $delivery_seq, $delivery_cp, $delivery_no) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
				  SET DELIVERY_NO	= '$delivery_no' ";
		
		if($delivery_cp <> ""){
			$query .= ", DELIVERY_CP	= '$delivery_cp'";
		}

		$query .= " WHERE DELIVERY_SEQ		= '$delivery_seq'
					    
						AND USE_TF = 'Y' 
						AND DEL_TF = 'N' ";
		
		//AND DELIVERY_NO	= ''

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateOrderGoodsDeliveryNumberComplete($db, $delivery_seq, $delivery_no, $sent_date) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
							SET 
								DELIVERY_DATE = '$sent_date'
							WHERE DELIVERY_SEQ		= '$delivery_seq' 
							  AND DELIVERY_NO	= '$delivery_no' 
							  AND USE_TF = 'Y' 
						      AND DEL_TF = 'N'
							  AND DELIVERY_DATE = '' ";
		
		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function chkOrderConfirmState($db, $order_goods_no) {
		
		$query = "SELECT ORDER_CONFIRM_ADM
					FROM TBL_ORDER_GOODS
				   WHERE ORDER_GOODS_NO		= '$order_goods_no'
					 AND ORDER_STATE	IN ('1')
					 AND USE_TF		= 'Y'
					 AND DEL_TF		= 'N' 
						";

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function updateOrderConfirmState($db, $reserve_no, $order_goods_no, $up_adm) {


		$query = "UPDATE TBL_ORDER_GOODS SET 
												ORDER_STATE		= '2', 
												ORDER_CONFIRM_DATE	= now(), 
												ORDER_CONFIRM_ADM		= '$up_adm'
						WHERE ORDER_GOODS_NO		= '$order_goods_no'
									 AND ORDER_STATE	IN ('1')
									 AND USE_TF		= 'Y'
									 AND DEL_TF		= 'N' ";
		
		//echo $query;
		mysql_query($query,$db);

		$query3 = "SELECT ORDER_STATE FROM TBL_ORDER_GOODS WHERE RESERVE_NO	= '$reserve_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
			
		$result = mysql_query($query3,$db);
		$total  = mysql_affected_rows();
			
		$tmp_order_state = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			$RS_ORDER_STATE			= Trim($row[0]);
			if ($i == 0) {
				$tmp_order_state = $RS_ORDER_STATE;
			} else {
				$tmp_order_state .= ",".$RS_ORDER_STATE;
			}
		}
		
		//echo "query3----".$query3."<br>";

		$query4 = "UPDATE TBL_ORDER SET 
											ORDER_STATE		= '$tmp_order_state' ";

		$query4 .=	" WHERE RESERVE_NO		= '$reserve_no' ";

		//echo "query4----".$query3."<br>";

		if(!mysql_query($query4,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	function updateOrderDeliveryGoodsName($db, $order_goods_delivery_no, $goods_delivery_name) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY
				  SET GOODS_DELIVERY_NAME = '$goods_delivery_name'
				  WHERE ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}

	}

	function listOrderGoodsInfoForOutStock($db, $reserve_no) {

		$query = "	SELECT (SELECT CP_NO FROM TBL_ORDER WHERE RESERVE_NO = OG.RESERVE_NO) AS CP_NO, 
									GS.GOODS_SUB_NO AS GOODS_NO, 
									OG.QTY * GS.GOODS_CNT AS SUM_GOODS_SUB_CNT,
									(SELECT BUY_PRICE FROM TBL_GOODS WHERE GOODS_NO = GS.GOODS_SUB_NO) AS BUY_PRICE
								FROM TBL_ORDER_GOODS OG 
								JOIN TBL_GOODS_SUB GS ON OG.GOODS_NO = GS.GOODS_NO
								WHERE RESERVE_NO = '$reserve_no'";

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

	//???? - ???? 1:1 ????
	function updateDeliveryState($db, $reserve_no, $order_goods_no, $delivery_cp, $delivery_no, $up_adm) {
		
		//echo $order_goods_no."<br/>";
		$query = "SELECT FINISH_DATE, DELIVERY_DATE FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO	= '$order_goods_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$f_date  = $rows[0];
		$d_date  = $rows[1];

		if ($f_date) {
			$str_finish_date		= "";
		} else {
			$str_finish_date		= " FINISH_DATE = now(), ";
		}

		if ($d_date) {
			$str_delivery_date	= "";
		} else {
			$str_delivery_date	= "	DELIVERY_DATE = now(), ";
		}
		
		$str_cancel_date		= "	CANCEL_DATE = NULL ";

		$query = "UPDATE TBL_ORDER_GOODS SET 
												ORDER_STATE		= '3', 
												DELIVERY_CP		= '$delivery_cp', 
												DELIVERY_NO		= '$delivery_no', ";

		$query .= $str_finish_date;
		$query .= $str_delivery_date;
		$query .= $str_cancel_date;

		$query .=	" WHERE ORDER_GOODS_NO		= '$order_goods_no'
									 AND ORDER_STATE	IN ('1','2','3')
									 AND USE_TF		= 'Y'
									 AND DEL_TF		= 'N' ";
		
		//echo $query;
		//exit;

		mysql_query($query,$db);

		$query = "UPDATE TBL_ORDER_GOODS SET 
												DELIVERY_CP		= '$delivery_cp', 
												DELIVERY_NO		= '$delivery_no', ";

		$query .= $str_finish_date;
		$query .= $str_delivery_date;
		$query .= $str_cancel_date;

		$query .=	" WHERE ORDER_GOODS_NO		= '$order_goods_no'
									 AND ORDER_STATE	IN ('7')
									 AND USE_TF		= 'Y'
									 AND DEL_TF		= 'N' ";
		//echo $query;
		mysql_query($query,$db);

		$query3 = "SELECT ORDER_STATE FROM TBL_ORDER_GOODS WHERE RESERVE_NO	= '$reserve_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		//echo $query3;
		$result = mysql_query($query3,$db);
		$total  = mysql_affected_rows();
			
		$tmp_order_state = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			$RS_ORDER_STATE			= Trim($row[0]);
			if ($i == 0) {
				$tmp_order_state = $RS_ORDER_STATE;
			} else {
				$tmp_order_state .= ",".$RS_ORDER_STATE;
			}
		}
		
		$query4 = "UPDATE TBL_ORDER SET 
											ORDER_STATE		= '$tmp_order_state', ";

		$query4 .= $str_finish_date;
		$query4 .= $str_delivery_date;
		$query4 .= $str_cancel_date;
			
		$query4 .=	" WHERE RESERVE_NO		= '$reserve_no' ";

		//echo $query4;
		if(!mysql_query($query4,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//???????? - ???? 1:N ???? ????
	function updateDeliveryStateMulti($db, $delivery_seq, $delivery_cp, $delivery_no) {
		


		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY SET 
												DELIVERY_CP		= '$delivery_cp', 
												DELIVERY_NO		= '$delivery_no'
												";

		$query .=	" WHERE DELIVERY_SEQ		= '$delivery_seq'";
		

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//?????? ???????? ????
	function updateOrderSaleCompany($db, $reserve_no, $cp_no) {
		
		$query = "UPDATE TBL_ORDER 
					 SET CP_NO = '$cp_no' 
				   WHERE RESERVE_NO = '$reserve_no' ";
			
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}	
	}

	//?????? ???????? ????
	function updateOrderBuyCompany($db, $order_goods_no, $buy_cp_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS
					 SET BUY_CP_NO = '$buy_cp_no' 
				   WHERE ORDER_GOODS_NO = '$order_goods_no' ";
			
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}	
	}

	function selectOrderGoods($db, $order_goods_no) {

		$query = "SELECT C.ON_UID, C.ORDER_GOODS_NO, C.RESERVE_NO, C.CP_ORDER_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, G.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, C.QTY, C.CATE_01, C.CATE_02, C.CATE_03, C.CATE_04, 
						 C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.TAX_TF, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE, C.ORDER_CONFIRM_DATE,
					
						 ((C.SALE_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY)) AS SUM_PRICE, 
						 ((C.SALE_PRICE * C.QTY) - ((C.BUY_PRICE * C.QTY)+C.DELIVERY_PRICE)) AS PLUS_PRICE,
						 ROUND((((C.SALE_PRICE * C.QTY) - ((C.BUY_PRICE * C.QTY)+C.DELIVERY_PRICE)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE,
										 
						 C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.DELIVERY_NO, C.PRICE, C.STICKER_PRICE, C.PRINT_PRICE, C.DISCOUNT_PRICE,
						 C.DELIVERY_CNT_IN_BOX, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
						 C.OPT_WRAP_NO, C.OPT_STICKER_NO, C.OPT_STICKER_READY, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.OPT_PRINT_MSG, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
						 C.DELIVERY_TYPE, C.OPT_OUTSTOCK_DATE, C.WORK_SEQ, C.WORK_QTY, C.WORK_FLAG, C.WORK_START_DATE, C.WORK_END_DATE,
						 C.SALE_CONFIRM_TF, 
						 G.CATE_03 AS G_CATE_03, G.FILE_NM_100, G.STOCK_CNT, G.FSTOCK_CNT, G.TSTOCK_CNT, G.DELIVERY_CNT_IN_BOX AS DELIVERY_CNT_IN_BOX_G

						FROM TBL_ORDER_GOODS C, TBL_GOODS G 
						 WHERE G.USE_TF= 'Y' 
							 AND G.DEL_TF = 'N' 
							 AND C.GOODS_NO = G.GOODS_NO  
							 AND C.ORDER_GOODS_NO = '$order_goods_no' ";

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

	function getOrderGoodsMaxSeq($db, $reserve_no) {
	
		$query ="SELECT MAX(ORDER_SEQ) AS M_SEQ FROM TBL_ORDER_GOODS WHERE RESERVE_NO = '$reserve_no' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;	
	
	
	}

/*
	function getRefundAbleQty($db, $reserve_no, $goods_no) {

		
		$total_qty = 0;
		$query = "SELECT QTY, ORDER_STATE
								FROM TBL_ORDER_GOODS
							 WHERE RESERVE_NO = '$reserve_no' 
								 AND GOODS_NO = '$goods_no' 
								 AND USE_TF = 'Y'
								 AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$total_qty = 0;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_QTY							= Trim($row["QTY"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
			} else if ($RS_ORDER_STATE == "4") {
				$total_qty = $total_qty;
			} else {
				$total_qty = $total_qty - $RS_QTY;
			}
		}
		
		return $total_qty;
	}
*/

	//$reserve_no ?????? ???????????? ????
	function getRefundAbleQty($db, $reserve_no, $order_goods_no) {
		
		$total_qty = 0;
		$query = "SELECT QTY, ORDER_STATE
								FROM TBL_ORDER_GOODS
							 WHERE ((ORDER_GOODS_NO = '$order_goods_no' AND GROUP_NO = 0) OR (GROUP_NO = '$order_goods_no'))
								 AND USE_TF = 'Y'
								 AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$total_qty = 0;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_QTY							= Trim($row["QTY"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
			} else if ($RS_ORDER_STATE == "4") {
				$total_qty = $total_qty;
			} else {
				$total_qty = $total_qty - $RS_QTY;
			}
		}
		
		return $total_qty;
	}

	function getRealDeliveryQty($db, $reserve_no, $order_goods_no) {

		
		$total_qty = 0;
		$query = "SELECT QTY, ORDER_STATE
					FROM TBL_ORDER_GOODS
		   		   WHERE ((ORDER_GOODS_NO = '$order_goods_no' AND GROUP_NO = 0) OR (GROUP_NO = '$order_goods_no'))
					 AND USE_TF = 'Y'
					 AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$total_qty = 0;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_QTY							= Trim($row["QTY"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
			} else if ($RS_ORDER_STATE == "6") {
				$total_qty = $total_qty - $RS_QTY;;
			} 
		}
		
		return $total_qty;
	}

	//?????? + ????????????
	function getRefundAbleQty_EstimateTransaction($db, $reserve_no, $order_goods_no) {
		
		$total_qty = 0;
		$query = "SELECT QTY, ORDER_STATE
								FROM TBL_ORDER_GOODS
							 WHERE ((ORDER_GOODS_NO = '$order_goods_no' AND GROUP_NO = 0) OR (GROUP_NO = '$order_goods_no'))
								 AND USE_TF = 'Y'
								 AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$total_qty = 0;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_QTY							= Trim($row["QTY"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
			} else if (($RS_ORDER_STATE == "4") || ($RS_ORDER_STATE == "8")) {
				$total_qty = $total_qty;
			} else {
				$total_qty = $total_qty - $RS_QTY;
			}
		}
		
		return $total_qty;
	}
/*
	function getPreOrderQty($db, $reserve_no, $goods_no, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04) {

		
		$total_qty = 0;
		$query = "SELECT QTY, ORDER_STATE
								FROM TBL_ORDER_GOODS
							 WHERE RESERVE_NO = '$reserve_no' 
								 AND GOODS_NO = '$goods_no' 
								 AND GOODS_OPTION_01 = '$goods_option_01' 
								 AND GOODS_OPTION_02 = '$goods_option_02' 
								 AND GOODS_OPTION_03 = '$goods_option_03' 
								 AND GOODS_OPTION_04 = '$goods_option_04' 
								 AND GOODS_OPTION_NM_01 = '$goods_option_nm_01' 
								 AND GOODS_OPTION_NM_02 = '$goods_option_nm_02' 
								 AND GOODS_OPTION_NM_03 = '$goods_option_nm_03' 
								 AND GOODS_OPTION_NM_04 = '$goods_option_nm_04'
								 AND USE_TF = 'Y'
								 AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$total_qty = 0;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_QTY							= Trim($row["QTY"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
			} else if ($RS_ORDER_STATE == "4") {
				$total_qty = $total_qty - $RS_QTY;
			}
		}
		
		return $total_qty;
	}
*/
	function cancelClaim($db, $reserve_no, $order_goods_no, $order_seq, $up_admin_no) {
		
		$query = "UPDATE TBL_REFUND SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$up_admin_no' 
											WHERE RESERVE_NO = '$reserve_no' AND ORDER_SEQ = '$order_seq' ";
		
		//echo $query;
		mysql_query($query,$db);

		$query = "UPDATE TBL_BOARD SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$up_admin_no' 
											WHERE BB_CODE = 'CLAIM' 
												AND CATE_01 = '$reserve_no' 
												AND CATE_03 = '$order_seq'
												AND CATE_04 <> '99' ";
		
		mysql_query($query,$db);
		//echo $query."<br/>";
		

		$query ="SELECT ORDER_STATE FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO = '$order_goods_no' AND ORDER_SEQ = '$order_seq'  ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$order_state  = $rows[0];
		
		if ($order_state == "8") {
			
			$order_seq2 = $order_seq + 1;

			$query = "UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$up_admin_no' 
												WHERE RESERVE_NO = '$reserve_no' AND ORDER_SEQ = '$order_seq2' ";
			
			//echo $query;
			mysql_query($query,$db);

			$query = "UPDATE TBL_BOARD SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$up_admin_no' 
												WHERE BB_CODE = 'CLAIM' 
													AND CATE_01 = '$reserve_no' 
													AND CATE_03 = '$order_seq2' ";
		
			mysql_query($query,$db);

			$query = "
					SELECT S.STOCK_NO 
					  FROM TBL_STOCK S
					  JOIN TBL_BOARD B ON S.BB_NO = B.BB_NO
		           WHERE B.BB_CODE = 'CLAIM' 
					 AND B.CATE_01 = '$reserve_no' 
					 AND B.CATE_03 = '$order_seq2'
					 AND B.CATE_04 <> '99' 
					 AND S.CLOSE_TF = 'N'
					 AND S.DEL_TF = 'N'
					 AND S.STOCK_CODE = 'FST02'
					";
		
			
			//echo $query."<br/>";

			$result = mysql_query($query,$db);
			$record = array();
			

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);
					
					$stock_no = $record[$i]["STOCK_NO"];

					//echo $stock_no."<br/>";

					deleteStock($db, $stock_no, $up_admin_no);

				}
			}	

		}
		else if($order_state == "7"){
			$query = "SELECT S.STOCK_NO 
			  FROM TBL_STOCK S
			  JOIN TBL_BOARD B ON S.BB_NO = B.BB_NO
		   WHERE B.BB_CODE = 'CLAIM' 
			 AND B.CATE_01 = '$reserve_no' 
			 AND B.CATE_03 = '$order_seq'
			 AND B.CATE_04 <> '99' 
			 AND S.CLOSE_TF = 'N'
			 AND S.DEL_TF = 'N'
			 AND S.STOCK_CODE = 'FST02'
			";

	
	//echo $query."<br/>";

			$result = mysql_query($query,$db);
			$record = array();
			

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);
					
					$stock_no = $record[$i]["STOCK_NO"];

					//echo "<script>alert('".$stock_no."');<script>";

					deleteStock($db, $stock_no, $up_admin_no);

				}
			}
		}

		$query = "UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$up_admin_no' 
											WHERE ORDER_GOODS_NO = '$order_goods_no' AND ORDER_SEQ = '$order_seq' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//???????? - ???? ???? ???? ???? ?????? - ??????
	function listConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, C.ORDER_GOODS_NO, 
										 C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, 
										 C.GOODS_NAME, C.GOODS_SUB_NAME, 
										 C.QTY, C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, 
										 C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 C.ORDER_STATE, C.BUY_CP_NO, C.FINISH_DATE, O.O_MEM_NM, O.R_MEM_NM, C.CONFIRM_TF, C.CONFIRM_DATE, O.CP_NO,
										 ((C.BUY_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY) ) AS SUM_PRICE, 
										 ((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) AS PLUS_PRICE, 
										 ROUND((((C.SALE_PRICE * C.QTY) - ((C.BUY_PRICE * C.QTY) + C.DELIVERY_PRICE)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE, C.TAX_TF,
										 CASE WHEN C.ORDER_STATE = '7' THEN -C.QTY ELSE C.QTY END AS QQTY,
										 O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE
								FROM TBL_ORDER O, TBL_ORDER_GOODS C
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND C.ORDER_STATE IN ('3','7') 
								 AND C.CATE_04 <> 'CHANGE' ";

		if ($order_field == "C.CONFIRM_DATE") {
			$str_con_date = "C.CONFIRM_DATE";
		} else if ($order_field == "C.ORDER_DATE") {
			$str_con_date = "C.ORDER_DATE";
		} else {
			$str_con_date = "C.FINISH_DATE";
		}

		if ($start_date <> "") {
			$query .= " AND ".$str_con_date." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$str_con_date." <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sale_cp_no."' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND C.CONFIRM_TF = '".$confirm_tf."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND C.TAX_TF = '".$tax_tf."' ";
		}

		if ($etc_condition <> "") {
			$query .= $etc_condition;
		}


		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		if ($order_field == "") 
			$order_field = "C.FINISH_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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

	function totalCntConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT 
							 FROM TBL_ORDER O, TBL_ORDER_GOODS C 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND C.ORDER_STATE IN ('3','7') 
								 AND C.CATE_04 <> 'CHANGE' ";

		if ($order_field == "C.CONFIRM_DATE") {
			$str_con_date = "C.CONFIRM_DATE";
		} else if ($order_field == "C.ORDER_DATE") {
			$str_con_date = "C.ORDER_DATE";
		} else {
			$str_con_date = "C.FINISH_DATE";
		}

		if ($start_date <> "") {
			$query .= " AND ".$str_con_date." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$str_con_date." <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sale_cp_no."' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND C.CONFIRM_TF = '".$confirm_tf."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND C.TAX_TF = '".$tax_tf."' ";
		}

		if ($etc_condition <> "") {
			$query .= $etc_condition;
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	//???????? - ???? ???? ???? ???? ?????? - ????????, ???????? ????
	function updateConfirmState($db, $order_goods_no, $confirm_ymd, $confirm_tf, $up_adm_no) {
		
		if ($confirm_tf == "Y") {

			$query = "UPDATE TBL_ORDER_GOODS SET CONFIRM_TF = 'Y', CONFIRM_DATE = now(), 
																				 CONFIRM_YMD = '$confirm_ymd',
																				 CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND CONFIRM_TF = 'N' ";
		} else {

			$query = "UPDATE TBL_ORDER_GOODS SET CONFIRM_TF = 'N', CONFIRM_DATE = NULL, 
																				 CONFIRM_YMD = '',
																				 CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND CONFIRM_TF = 'Y' ";
		}
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//???????? - ????, ???? ???? ???? ???? ?????? - ????, ?????? ????
	function updateTaxState($db, $order_goods_no, $tax_tf) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET TAX_TF = '$tax_tf'
									 WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertTempOrder($db, $file_nm, $cp_nm, $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $order_state, $cp_order_no, $opt_manager_no, $opt_manager_name, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_TEMP_ORDER (TEMP_NO, CP_NO, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
																	 MEMO, ORDER_STATE, CP_ORDER_NO, OPT_MANAGER_NO, OPT_MANAGER_NAME, USE_TF, REG_ADM, REG_DATE) 
													 values ('$file_nm', '$cp_nm', '$o_name', '$o_phone', '$o_hphone', '$r_name', '$r_phone', '$r_hphone', '$r_zipcode', '$r_addr1', '$memo', '$order_state', '$cp_order_no', '$opt_manager_no', '$opt_manager_name', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query."<br>";
		//exit;



		if(!mysql_query($query,$db)) {
			return "";
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {

			$query="SELECT MAX(ORDER_NO) AS LAST_ORDER_NO FROM TBL_TEMP_ORDER";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			return $rows[0];
		}
	}


	function listTempOrder($db, $temp_no) {

		$query = "SELECT CP_NO, ORDER_NO, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
										 MEMO, ORDER_STATE, CP_ORDER_NO, USE_TF, OPT_MANAGER_NO, OPT_MANAGER_NAME, REG_ADM, REG_DATE
								FROM TBL_TEMP_ORDER WHERE TEMP_NO = '$temp_no' ";

		
		$query .= " ORDER BY CP_NO asc ";

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

	function selectTempOrder($db, $temp_no, $order_no) {

		$query = "SELECT CP_NO, ORDER_NO, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
										 MEMO, ORDER_STATE, CP_ORDER_NO, OPT_MANAGER_NO, OPT_MANAGER_NAME, USE_TF, REG_ADM, REG_DATE
								FROM TBL_TEMP_ORDER WHERE TEMP_NO = '$temp_no' AND ORDER_NO = '$order_no' ";
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


	function updateTempOrder($db, $cp_type, $o_name, $o_phone, $o_hphone, $r_name, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $cp_order_no, $opt_manager_no, $up_adm, $temp_no, $order_no) {

		$query="UPDATE TBL_TEMP_ORDER SET 
													CP_NO							= '$cp_type',
													O_NAME							= '$o_name',
													O_PHONE							= '$o_phone',
													O_HPHONE						= '$o_hphone',
													R_NAME							= '$r_name',
													R_PHONE							= '$r_phone',
													R_HPHONE						= '$r_hphone',
													R_ZIPCODE						= '$r_zipcode',
													R_ADDR1							= '$r_addr1',
													MEMO							= '$memo',
													CP_ORDER_NO	                    = '$cp_order_no',
													OPT_MANAGER_NO                  = '$opt_manager_no'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	 function updateTempOrderGoodsNo($db, $order_no, $order_seq, $goods_no, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
													GOODS_NO					= '$goods_no'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateTempOrderGoodsName($db, $order_no, $order_seq, $goods_name, $temp_no) {

		$query =   "UPDATE
						TBL_TEMP_ORDER_GOODS
					SET
						GOODS_NAME = '$goods_name'
					WHERE TEMP_NO = '$temp_no'
						AND ORDER_NO = '$order_no'
						AND ORDER_SEQ = '$order_seq'";
		//echo $query;
		if(!mysql_query($query,$db)) {
			return false;
		} else {
			return true;
		}
	}

	function updateTempOrderStickerNo($db, $order_no, $order_seq, $opt_sticker_no, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
													OPT_STICKER_NO					= '$opt_sticker_no'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	
    function updateTempOrderWrapNo($db, $order_no, $order_seq, $opt_wrap_no, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
													OPT_WRAP_NO					= '$opt_wrap_no'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

    function updateTempOrderOutboxTF($db, $order_no, $order_seq, $opt_outbox_tf, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
												OPT_OUTBOX_TF_CODE					= '$opt_outbox_tf'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

    function updateTempOrderDeliveryType($db, $order_no, $order_seq, $delivery_type, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
												DELIVERY_TYPE_CODE				= '$delivery_type'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateTempOrderAdmNo($db, $order_no, $order_seq, $opt_manager_no, $temp_no) {

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
													OPT_MANAGER_NO				= '$opt_manager_no'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateTempOrderGoods($db, $order_no, $order_seq, $goods_no, $goods_code, $goods_name, $goods_price, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf_code, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $delivery_type_code, $delivery_price, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box, $temp_no) {


		$DELIVERY_TYPE = getDcodeName($db, "DELIVERY_TYPE", $delivery_type_code);

		$query="UPDATE TBL_TEMP_ORDER_GOODS SET 
												GOODS_NO = '$goods_no',
												GOODS_CODE = '$goods_code',
												GOODS_NAME = '$goods_name',
												GOODS_PRICE = '$goods_price',
												QTY = '$qty',
												OPT_STICKER_NO = '$opt_sticker_no',
												OPT_STICKER_MSG = '$opt_sticker_msg',
												OPT_OUTBOX_TF_CODE = '$opt_outbox_tf_code',
												OPT_WRAP_NO = '$opt_wrap_no',
												OPT_PRINT_MSG = '$opt_print_msg',
												OPT_OUTSTOCK_DATE = '$opt_outstock_date',
												OPT_MEMO = '$opt_memo',
												DELIVERY_TYPE = '$DELIVERY_TYPE',
												DELIVERY_TYPE_CODE = '$delivery_type_code',
												DELIVERY_PRICE = '$delivery_price',
												DELIVERY_CP = '$delivery_cp',
												SENDER_NM = '$sender_nm',
												SENDER_PHONE = '$sender_phone',
												DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_NO						= '$order_no'
											AND  ORDER_SEQ                      = '$order_seq'";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempOrder($db, $temp_no, $order_no) {

		$query="DELETE FROM TBL_TEMP_ORDER WHERE TEMP_NO = '$temp_no' AND ORDER_NO = '$order_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	
	function deleteTempOrderGoods($db, $temp_no, $order_no) {

		$query="DELETE FROM TBL_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' AND ORDER_NO = '$order_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertTempToRealOrderWithDate($db, $temp_no, $str_order_no, $order_date, $is_package) {
		
		
		$query="SELECT CP_NO, ORDER_NO, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
										 MEMO, ORDER_STATE, CP_ORDER_NO, OPT_MANAGER_NO, OPT_MANAGER_NAME, USE_TF, REG_ADM, REG_DATE
							FROM TBL_TEMP_ORDER
						 WHERE TEMP_NO = '$temp_no' AND ORDER_NO IN ($str_order_no) ORDER BY ORDER_NO ";
		
		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		if (sizeof($record) > 0) {

			
			for ($j = 0 ; $j < sizeof($record); $j++) {

				$SUM_PRICE = 0;
				$SUM_BUY_PRICE = 0;
				$SUM_SALE_PRICE = 0;
				$SUM_EXTRA_PRICE = 0;
				$TOTAL_QTY = 0;
				$TOTAL_PRICE = 0;
				$TOTAL_BUY_PRICE = 0;
				$TOTAL_SALE_PRICE = 0;
				$TOTAL_EXTRA_PRICE = 0;
				$TOTAL_DELIVERY_PRICE = 0;

				// ???? ???? ????
				$new_reserve_no = getReservNo($db,"EN");
			
				if (!get_session('s_ord_no')) {
					set_session('s_ord_no', getUniqueId($db));
				}

				$s_ord_no = get_session('s_ord_no');

				// ?????? ??????..
				$CP_NO						= trim($record[$j]["CP_NO"]);
				$O_NAME						= SetStringToDB($record[$j]["O_NAME"]);
				$O_PHONE					= trim($record[$j]["O_PHONE"]);
				$O_HPHONE					= trim($record[$j]["O_HPHONE"]);
				$R_NAME						= SetStringToDB($record[$j]["R_NAME"]);
				$R_PHONE					= trim($record[$j]["R_PHONE"]);
				$R_HPHONE					= trim($record[$j]["R_HPHONE"]);
				$R_ZIPCODE					= trim($record[$j]["R_ZIPCODE"]);
				$R_ADDR1					= SetStringToDB($record[$j]["R_ADDR1"]);
				$MEMO						= SetStringToDB($record[$j]["MEMO"]);
				$ORDER_STATE				= trim($record[$j]["ORDER_STATE"]);
				//$DELIVERY					= trim($record[$j]["DELIVERY"]);
				//$SA_DELIVERY				= trim($record[$j]["SA_DELIVERY"]);
				$CP_ORDER_NO				= trim($record[$j]["CP_ORDER_NO"]);
				$OPT_MANAGER_NO				= trim($record[$j]["OPT_MANAGER_NO"]);
				$REG_ADM					= trim($record[$j]["REG_ADM"]);
				$TEMP_ORDER_NO				= trim($record[$j]["ORDER_NO"]);

				//?????? ???? ????
				$is_mall = isMallCompany($db, $CP_NO) > 0;
				
				// ???? ????
				$new_mem_no = 0;

				$arr_rs_temp_goods = selectTempOrderGoods($db, $temp_no, $TEMP_ORDER_NO);
				if (sizeof($arr_rs_temp_goods) > 0) {
					for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

						$GOODS_NO		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
						$GOODS_CODE		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_CODE"]); 
						$GOODS_NAME		= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
						$ORDER_DATE		= SetStringFromDB($arr_rs_temp_goods[$k]["ORDER_DATE"]); 

						// ???? ???? ????
						$arr_rs = selectGoods($db, $GOODS_NO);
						//$rs_goods_code		= SetStringFromDB($arr_rs[0]["GOODS_CODE"]); 
						//$rs_goods_name		= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
						$rs_price			= trim($arr_rs[0]["PRICE"]); 

						
						$BUY_CP_NO			= trim($arr_rs[0]["CATE_03"]); 
						$PRICE				= trim($arr_rs[0]["PRICE"]);
						$BUY_PRICE			= trim($arr_rs[0]["BUY_PRICE"]); 
						$SALE_PRICE			= trim($arr_rs[0]["SALE_PRICE"]); 
						//$EXTRA_PRICE		= trim($arr_rs[0]["EXTRA_PRICE"]); 
						$TAX_TF				= trim($arr_rs[0]["TAX_TF"]);
						$STICKER_PRICE		= trim($arr_rs[0]["STICKER_PRICE"]); 
						$PRINT_PRICE		= trim($arr_rs[0]["PRINT_PRICE"]); 
						$SALE_SUSU			= trim($arr_rs[0]["SALE_SUSU"]); 
						$LABOR_PRICE		= trim($arr_rs[0]["LABOR_PRICE"]); 
						$OTHER_PRICE		= trim($arr_rs[0]["OTHER_PRICE"]); 
						$DELIVERY_CNT_IN_BOX = trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]);
						$DELIVERY_PRICE		= trim($arr_rs[0]["DELIVERY_PRICE"]);

						$GOODS_NAME			  = SetStringToDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
						$GOODS_PRICE		  = trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);

						$QTY				  = trim($arr_rs_temp_goods[$k]["QTY"]);

						$OPT_STICKER_NO		  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_STICKER_NO"]);
						$OPT_STICKER_MSG	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_STICKER_MSG"]);
						$OPT_OUTBOX_TF_CODE	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_OUTBOX_TF_CODE"]);
						//$DELIVERY_CNT_IN_BOX  = SetStringToDB($arr_rs_temp_goods[$k]["DELIVERY_CNT_IN_BOX"]);
						$OPT_WRAP_NO		  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_WRAP_NO"]);
						$OPT_PRINT_MSG		  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_PRINT_MSG"]);
						$OPT_OUTSTOCK_DATE	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_OUTSTOCK_DATE"]);
						$OPT_STICKER_MSG	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_STICKER_MSG"]);
						$DELIVERY_TYPE_CODE	  = SetStringToDB($arr_rs_temp_goods[$k]["DELIVERY_TYPE_CODE"]);
						$DELIVERY_CP		  = SetStringToDB($arr_rs_temp_goods[$k]["DELIVERY_CP"]);
						$SENDER_NM			  = SetStringToDB($arr_rs_temp_goods[$k]["SENDER_NM"]);
						$SENDER_PHONE		  = SetStringToDB($arr_rs_temp_goods[$k]["SENDER_PHONE"]);
						$TEMP_DELIVERY_CNT_IN_BOX  = SetStringToDB($arr_rs_temp_goods[$k]["DELIVERY_CNT_IN_BOX"]);
						$OPT_MEMO			  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_MEMO"]);
						$OPT_REQUEST_MEMO	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_REQUEST_MEMO"]);
						$OPT_SUPPORT_MEMO	  = SetStringToDB($arr_rs_temp_goods[$k]["OPT_SUPPORT_MEMO"]);

						if($TEMP_DELIVERY_CNT_IN_BOX != "" && $TEMP_DELIVERY_CNT_IN_BOX != "0")
							$DELIVERY_CNT_IN_BOX = $TEMP_DELIVERY_CNT_IN_BOX;
						
						// ???????? ?????? ????
						if ($CP_NO <> "") {
							$new_price = getCompanyGoodsPrice($db, $GOODS_NO, $CP_NO );
							if ($new_price <> 0)
								$SALE_PRICE = $new_price;
						}
					
						// ?????? ???? ????
						$SALE_PRICE = $GOODS_PRICE;

						//???????? ?????? ???? -> MRO?? ?????? ?????? ????
						if($CP_NO == 1480) 
							$EXTRA_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 4) ;
						else
							$EXTRA_PRICE = 0;

						// ???????? ???? ???? ???? ????
						$SUM_PRICE = $QTY * $PRICE;
						$SUM_BUY_PRICE = $QTY * $BUY_PRICE;
						$SUM_SALE_PRICE = $QTY * $SALE_PRICE;
						$SUM_EXTRA_PRICE = $QTY * $EXTRA_PRICE;
						$TOTAL_QTY = $TOTAL_QTY + $QTY;
						$TOTAL_PRICE = $TOTAL_PRICE + $SUM_PRICE;
						$TOTAL_BUY_PRICE = $TOTAL_BUY_PRICE + $SUM_BUY_PRICE;
						$TOTAL_SALE_PRICE = $TOTAL_SALE_PRICE + $SUM_SALE_PRICE;
						$TOTAL_EXTRA_PRICE = $TOTAL_EXTRA_PRICE + $SUM_EXTRA_PRICE;

						//$SA_DELIVERY_PRICE = $SA_DELIVERY;

						$order_state	= "1";
						$use_tf			= "Y";
						$seq_j			= 0;
						$goods_sub_name	= "";
						$pay_type		= "BANK";

						$con_discount_price = 0;

						$memos = array('opt_request_memo' => $OPT_REQUEST_MEMO, 'opt_support_memo' => $OPT_SUPPORT_MEMO);

						$result = insertOrderGoods($db, $s_ord_no, $new_reserve_no, $CP_ORDER_NO, $BUY_CP_NO, $new_mem_no, $seq_j, $GOODS_NO, $GOODS_CODE, $GOODS_NAME, $goods_sub_name, $QTY, $OPT_STICKER_NO, $OPT_STICKER_MSG, $OPT_OUTBOX_TF_CODE, $DELIVERY_CNT_IN_BOX, $OPT_WRAP_NO, $OPT_PRINT_MSG, $OPT_OUTSTOCK_DATE, $OPT_MEMO, $memos, $DELIVERY_TYPE_CODE, $DELIVERY_CP, $SENDER_NM, $SENDER_PHONE, $cate_01, $cate_02, $cate_03, $cate_04, $PRICE, $BUY_PRICE, $SALE_PRICE, $EXTRA_PRICE, $DELIVERY_PRICE, $SA_DELIVERY_PRICE, $con_discount_price, $STICKER_PRICE, $PRINT_PRICE, $SALE_SUSU, $LABOR_PRICE, $OTHER_PRICE, $TAX_TF, $order_state, $use_tf, $REG_ADM);

					}
				}


				if($ORDER_DATE == '0000-00-00' || $ORDER_DATE == '' || $ORDER_DATE == '1970-01-01') { 
					$ORDER_DATE = $order_date;
				}

				$query_date = "UPDATE TBL_ORDER_GOODS SET ORDER_DATE = '$ORDER_DATE' WHERE RESERVE_NO = '$new_reserve_no' ";
				mysql_query($query_date,$db);

				$o_mem_name = $O_NAME;
				if ($o_mem_name == "") {
					$o_mem_name = $R_NAME;
				}

				$o_phone = $O_PHONE;
				if ($o_phone == "") {
					$o_phone = $R_PHONE;
				}

				$o_hphone = $O_HPHONE;
				if ($o_hphone == "") {
					$o_hphone = $R_HPHONE;
				}

				// ???? ?????? ??????
				//$query_st = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT - $QTY WHERE GOODS_NO = '$GOODS_NO'";
				//mysql_query($query_st,$db);


				// ???? ???? ?????? ?????? ??????

				//$result = insertOrder($db, $s_ord_no, $new_reserve_no, $new_mem_no, $CP_NO, $o_mem_name, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $R_NAME, $R_ZIPCODE, $R_ADDR1, $r_addr2, $R_PHONE, $R_HPHONE, $r_email, $MEMO, $order_state, $TOTAL_BUY_PRICE, $TOTAL_SALE_PRICE, $TOTAL_EXTRA_PRICE, $DELIVERY, $TOTAL_QTY, $pay_type, $delivery_type, $use_tf, $REG_ADM);

				$bulk_tf = 'N';
				$con_total_sa_delivery_price = 0;
				$con_total_discount_price = 0;

				$result =insertOrder($db, $s_ord_no, $new_reserve_no, $new_mem_no, $CP_NO, $o_mem_name, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $R_NAME, $R_ZIPCODE, $R_ADDR1, $r_addr2, $R_PHONE, $R_HPHONE, $r_email, $MEMO, $bulk_tf, $OPT_MANAGER_NO, $order_state, $TOTAL_PRICE, $TOTAL_BUY_PRICE, $TOTAL_SALE_PRICE, $TOTAL_EXTRA_PRICE, $DELIVERY, $con_total_sa_delivery_price, $con_total_discount_price, $TOTAL_QTY, $pay_type, $DELIVERY_TYPE_CODE, $use_tf, $REG_ADM);

				$query_date = "UPDATE TBL_ORDER SET ORDER_DATE = '$ORDER_DATE' WHERE RESERVE_NO = '$new_reserve_no' ";
				mysql_query($query_date,$db);

				
				//2016-09-24 ?????? ?????? ???????? ?????? ???? - ?????? ??????/?????????????? ????
				if($is_package == "Y")
					updateIsPackage($db, $new_reserve_no);

				$pay_state	= "1";
				$pay_reason = "????????";
				$cms_amount	= 0;
				$cms_casu		= 0;

				if ($pay_type == "BANK") {
					$bank_amount = $TOTAL_SALE_PRICE + $TOTAL_EXTRA_PRICE + $DELIVERY;
				}

				// ???? ??????????
				$result = insertPayment($db, $pay_type, $pay_state, $s_ord_no, $new_reserve_no, $new_mem_no, $o_mem_name, $CP_NO, $pay_reason, $cms_amount, $cms_casu, $cms_pay_bank, $cms_pay_account, $o_mem_name, $bank_amount, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $cash_receipt_state, $card_amount, $card_name, $pgbank_amount, $pgbank_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $req_date, $use_tf, $REG_ADM);
		
				set_session('s_ord_no', "");

			}
		}
		
		#echo $query;

		if(!$result) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateIsPackage($db, $reserve_no) {

		$query=" UPDATE TBL_ORDER 
					SET IS_PACKAGE = 'Y' 
				  WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);
		//echo $query;

		$query=" UPDATE TBL_ORDER_GOODS
					SET IS_PACKAGE = 'Y' 
				  WHERE RESERVE_NO = '$reserve_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempToRealOrder($db, $temp_no, $str_order_no) {

		$query=" DELETE FROM  TBL_TEMP_ORDER WHERE TEMP_NO = '$temp_no' AND ORDER_NO IN ($str_order_no) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listBuyConfirmList($db, $start_date, $end_date, $buy_cp_no, $ad_type, $tax_tf, $order_field, $order_str) {

		$query = "SELECT AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE,
										 SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
										 SUM(AA.SALE_PRICE * AA.QTY) ALL_SALE_PRICE,
										 SUM(AA.EXTRA_PRICE * AA.QTY) ALL_EXTRA_PRICE,
										 SUM(AA.DELIVERY_PRICE) ALL_DELIVERY_PRICE,
										 SUM(AA.DELIVERY_PRICE * AA.DELIVERY_QTY) ALL_DELIVERY_PRICE2,
										 SUM(AA.SA_DELIVERY_PRICE * AA.QTY) ALL_SA_DELIVERY_PRICE,
										 (SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) AS PLUS_PRICE,
										 (SUM(AA.BUY_PRICE * AA.QTY) + SUM(AA.EXTRA_PRICE * AA.QTY) + SUM(AA.DELIVERY_PRICE)) AS ALL_PAY_PRICE,
										 ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - (SUM(AA.BUY_PRICE * AA.QTY) + SUM(AA.EXTRA_PRICE * AA.QTY) + SUM(AA.DELIVERY_PRICE))) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
								FROM
										 (SELECT CONFIRM_YMD, BUY_CP_NO, 
															CASE ORDER_STATE 
															WHEN '0' THEN QTY 
															WHEN '1' THEN QTY 
															WHEN '2' THEN QTY 
															WHEN '3' THEN QTY 
															WHEN '4' THEN -QTY 
															WHEN '6' THEN -QTY 
															WHEN '7' THEN -QTY 
															WHEN '8' THEN -QTY 
															END AS QTY,
															CASE ORDER_STATE 
															WHEN '0' THEN 1 
															WHEN '1' THEN 1 
															WHEN '2' THEN 1 
															WHEN '3' THEN 1 
															WHEN '4' THEN -1 
															WHEN '6' THEN -1 
															WHEN '7' THEN -1 
															WHEN '8' THEN -1 
															END AS DELIVERY_QTY,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN BUY_PRICE ELSE 0 END AS BUY_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN SALE_PRICE ELSE 0 END AS SALE_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN EXTRA_PRICE ELSE 0 END AS EXTRA_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN DELIVERY_PRICE ELSE 0 END AS DELIVERY_PRICE,
															SA_DELIVERY_PRICE 
												FROM TBL_ORDER_GOODS 
											 WHERE CONFIRM_YMD <> ''
												 AND CONFIRM_TF = 'Y'
												 AND USE_TF = 'Y' 
												 AND DEL_TF = 'N'
												 AND ORDER_STATE IN ('3','7') 
												 AND CATE_04 <> 'CHANGE' ";

		if ($start_date <> "") {
			$query .= " AND CONFIRM_YMD >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND CONFIRM_YMD <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND TAX_TF = '".$tax_tf."' ";
		}

		$query .= "					 AND DEL_TF = 'N') AA, TBL_COMPANY BB
									 WHERE AA.BUY_CP_NO = BB.CP_NO ";

		if ($ad_type <> "") {
			$query .= " AND BB.AD_TYPE  = '".$ad_type."' ";
		}

		$query .= "		GROUP BY AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE ";

		if ($order_field == "") 
			$order_field = "AA.CONFIRM_YMD";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str;
		
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

	function listBuyConfirmAll($db, $start_date, $end_date, $buy_cp_no, $ad_type, $tax_tf) {

		$query = "SELECT IFNULL(SUM(AA.BUY_PRICE * AA.QTY),0) ALL_BUY_PRICE, 
										 IFNULL(SUM(AA.SALE_PRICE * AA.QTY),0) ALL_SALE_PRICE,
										 IFNULL(SUM(AA.EXTRA_PRICE * AA.QTY),0) ALL_EXTRA_PRICE,
										 IFNULL(SUM(AA.DELIVERY_PRICE),0) ALL_DELIVERY_PRICE2,
										 IFNULL(SUM(AA.DELIVERY_PRICE * AA.DELIVERY_QTY),0) ALL_DELIVERY_PRICE,
										 IFNULL(SUM(AA.SA_DELIVERY_PRICE * AA.QTY),0) ALL_SA_DELIVERY_PRICE,
										 IFNULL((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)),0) AS PLUS_PRICE,
										 IFNULL((SUM(AA.BUY_PRICE * AA.QTY) + SUM(AA.EXTRA_PRICE * AA.QTY) + SUM(AA.DELIVERY_PRICE)),0) AS ALL_PAY_PRICE,
										 ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - (SUM(AA.BUY_PRICE * AA.QTY) + SUM(AA.EXTRA_PRICE * AA.QTY) + SUM(AA.DELIVERY_PRICE))) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
								FROM
										 (SELECT	BUY_CP_NO, 
															CASE ORDER_STATE 
															WHEN '0' THEN QTY 
															WHEN '1' THEN QTY 
															WHEN '2' THEN QTY 
															WHEN '3' THEN QTY 
															WHEN '4' THEN -QTY 
															WHEN '6' THEN -QTY 
															WHEN '7' THEN -QTY 
															WHEN '8' THEN -QTY 
															END AS QTY,
															CASE ORDER_STATE 
															WHEN '0' THEN 1 
															WHEN '1' THEN 1 
															WHEN '2' THEN 1 
															WHEN '3' THEN 1 
															WHEN '4' THEN -1 
															WHEN '6' THEN -1 
															WHEN '7' THEN -1 
															WHEN '8' THEN -1 
															END AS DELIVERY_QTY,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN BUY_PRICE ELSE 0 END AS BUY_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN SALE_PRICE ELSE 0 END AS SALE_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN EXTRA_PRICE ELSE 0 END AS EXTRA_PRICE,
															CASE SA_DELIVERY_PRICE 
															WHEN '0' THEN DELIVERY_PRICE ELSE 0 END AS DELIVERY_PRICE,
															SA_DELIVERY_PRICE 
												FROM TBL_ORDER_GOODS 
											 WHERE CONFIRM_YMD <> ''
												 AND CONFIRM_TF = 'Y'
												 AND USE_TF = 'Y' 
												 AND DEL_TF = 'N' 
												 AND ORDER_STATE IN ('3','7') 
												 AND CATE_04 <> 'CHANGE' ";

		if ($start_date <> "") {
			$query .= " AND CONFIRM_YMD >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND CONFIRM_YMD <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND TAX_TF = '".$tax_tf."' ";
		}

		$query .= "					 AND DEL_TF = 'N') AA, TBL_COMPANY BB WHERE AA.BUY_CP_NO = BB.CP_NO ";


		if ($ad_type <> "") {
			$query .= " AND BB.AD_TYPE  = '".$ad_type."' ";
		}

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

	function listSaleConfirmList($db, $start_date, $end_date, $cp_no, $ad_type, $tax_tf, $order_field, $order_str) {

		$query = "SELECT AA.SALE_CONFIRM_YMD, AA.CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE,
										 SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
										 SUM(AA.SALE_PRICE * AA.QTY) ALL_SALE_PRICE,
										 SUM(AA.EXTRA_PRICE * AA.QTY) ALL_EXTRA_PRICE,
										 SUM(AA.DELIVERY_PRICE) ALL_DELIVERY_PRICE,
										 SUM(AA.DELIVERY_PRICE * AA.DELIVERY_QTY) ALL_DELIVERY_PRICE2,
										 SUM(AA.SA_DELIVERY_PRICE * AA.QTY) ALL_SA_DELIVERY_PRICE,
										 (SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) AS PLUS_PRICE,
										 ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
								FROM
										 (SELECT A.SALE_CONFIRM_YMD, B.CP_NO, 
															CASE A.ORDER_STATE 
															WHEN '0' THEN A.QTY 
															WHEN '1' THEN A.QTY 
															WHEN '2' THEN A.QTY 
															WHEN '3' THEN A.QTY 
															WHEN '4' THEN -A.QTY 
															WHEN '6' THEN -A.QTY 
															WHEN '7' THEN -A.QTY 
															WHEN '8' THEN -A.QTY 
															END AS QTY,
															CASE A.ORDER_STATE 
															WHEN '0' THEN 1 
															WHEN '1' THEN 1 
															WHEN '2' THEN 1 
															WHEN '3' THEN 1 
															WHEN '4' THEN -1 
															WHEN '6' THEN -1 
															WHEN '7' THEN -1 
															WHEN '8' THEN -1 
															END AS DELIVERY_QTY,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.BUY_PRICE ELSE A.BUY_PRICE END AS BUY_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.SALE_PRICE ELSE A.SALE_PRICE END AS SALE_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.EXTRA_PRICE ELSE A.EXTRA_PRICE END AS EXTRA_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.DELIVERY_PRICE ELSE A.DELIVERY_PRICE END AS DELIVERY_PRICE,
															A.SA_DELIVERY_PRICE 
												FROM TBL_ORDER_GOODS A, TBL_ORDER B
											 WHERE A.RESERVE_NO = B.RESERVE_NO 
												 AND A.SALE_CONFIRM_YMD <> ''
												 AND A.SALE_CONFIRM_TF = 'Y'
												 AND A.USE_TF = 'Y' 
												 AND A.DEL_TF = 'N' 
												 AND A.ORDER_STATE IN ('3','7') 
												 AND A.CATE_04 <> 'CHANGE' ";

		if ($start_date <> "") {
			$query .= " AND A.SALE_CONFIRM_YMD >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.SALE_CONFIRM_YMD <= '".$end_date." 23:59:59' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND A.TAX_TF  = '".$tax_tf."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO  = '".$cp_no."' ";
		}

		$query .= "					 AND A.DEL_TF = 'N') AA, TBL_COMPANY BB
									 WHERE AA.CP_NO = BB.CP_NO ";

		if ($ad_type <> "") {
			$query .= " AND BB.AD_TYPE  = '".$ad_type."' ";
		}

		$query .= "		GROUP BY AA.SALE_CONFIRM_YMD, AA.CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE ";

		if ($order_field == "") 
			$order_field = "AA.SALE_CONFIRM_YMD";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str;

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

	function listSaleConfirmAll($db, $start_date, $end_date, $cp_no, $ad_type, $tax_tf) {

		$query = "SELECT IFNULL(SUM(AA.BUY_PRICE * AA.QTY),0) ALL_BUY_PRICE, 
										 IFNULL(SUM(AA.SALE_PRICE * AA.QTY),0) ALL_SALE_PRICE,
										 IFNULL(SUM(AA.EXTRA_PRICE * AA.QTY),0) ALL_EXTRA_PRICE,
										 IFNULL(SUM(AA.DELIVERY_PRICE),0) ALL_DELIVERY_PRICE,
										 IFNULL(SUM(AA.DELIVERY_PRICE * AA.DELIVERY_QTY),0) ALL_DELIVERY_PRICE2,
										 IFNULL(SUM(AA.SA_DELIVERY_PRICE * AA.QTY),0) ALL_SA_DELIVERY_PRICE,
										 IFNULL((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)),0) AS PLUS_PRICE,
										 ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
								FROM
										 (SELECT	B.CP_NO, 
															CASE A.ORDER_STATE 
															WHEN '0' THEN A.QTY 
															WHEN '1' THEN A.QTY 
															WHEN '2' THEN A.QTY 
															WHEN '3' THEN A.QTY 
															WHEN '4' THEN -A.QTY 
															WHEN '6' THEN -A.QTY 
															WHEN '7' THEN -A.QTY 
															WHEN '8' THEN -A.QTY 
															END AS QTY,
															CASE A.ORDER_STATE 
															WHEN '0' THEN 1 
															WHEN '1' THEN 1 
															WHEN '2' THEN 1 
															WHEN '3' THEN 1 
															WHEN '4' THEN -1 
															WHEN '6' THEN -1 
															WHEN '7' THEN -1 
															WHEN '8' THEN -1 
															END AS DELIVERY_QTY,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.BUY_PRICE ELSE A.BUY_PRICE END AS BUY_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.SALE_PRICE ELSE A.SALE_PRICE END AS SALE_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.EXTRA_PRICE ELSE A.EXTRA_PRICE END AS EXTRA_PRICE,
															CASE A.SA_DELIVERY_PRICE 
															WHEN '0' THEN A.DELIVERY_PRICE ELSE A.DELIVERY_PRICE END AS DELIVERY_PRICE,
															A.SA_DELIVERY_PRICE 
												FROM TBL_ORDER_GOODS A, TBL_ORDER B
											 WHERE A.RESERVE_NO = B.RESERVE_NO 
												 AND A.SALE_CONFIRM_YMD <> ''
												 AND A.SALE_CONFIRM_TF = 'Y'
												 AND A.USE_TF = 'Y' 
												 AND A.DEL_TF = 'N' 
												 AND A.ORDER_STATE IN ('3','7') 
												 AND A.CATE_04 <> 'CHANGE' ";

		if ($start_date <> "") {
			$query .= " AND A.SALE_CONFIRM_YMD >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.SALE_CONFIRM_YMD <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO  = '".$cp_no."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND A.TAX_TF  = '".$tax_tf."' ";
		}

		$query .= "					 AND A.DEL_TF = 'N') AA, TBL_COMPANY BB WHERE AA.CP_NO = BB.CP_NO ";


		if ($ad_type <> "") {
			$query .= " AND BB.AD_TYPE  = '".$ad_type."' ";
		}

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

	//???? ???? ???? ???? ?????? - ??????
	function listSaleConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntSaleConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, C.ORDER_GOODS_NO, C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, 
										 C.GOODS_NAME, C.GOODS_SUB_NAME, 
										 C.QTY, 
										 C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, 
										 C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 C.ORDER_STATE, C.BUY_CP_NO, C.FINISH_DATE, O.O_MEM_NM, O.R_MEM_NM, C.CONFIRM_TF, C.CONFIRM_DATE, 
										 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_DATE, C.SALE_CONFIRM_YMD, C.SALE_CONFIRM_ADM,
										 O.CP_NO,
										 ((C.SALE_PRICE * C.QTY)) AS SUM_PRICE, 
										 ((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) AS PLUS_PRICE, 
										 ROUND((((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE, C.TAX_TF,
										 CASE WHEN C.ORDER_STATE = '7' THEN -C.QTY ELSE C.QTY END AS QQTY,
										 O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE
								FROM TBL_ORDER O, TBL_ORDER_GOODS C
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND C.ORDER_STATE IN ('3','7') 
								 AND C.CATE_04 <> 'CHANGE' ";
		/*
		$query = "SELECT @rownum:= @rownum - 1  as rn, C.ORDER_GOODS_NO, C.RESERVE_NO, C.MEM_NO, C.ORDER_SEQ, C.GOODS_NO, C.GOODS_CODE, 
										 C.GOODS_NAME, C.GOODS_SUB_NAME, 
										 C.QTY, C.GOODS_OPTION_01, C.GOODS_OPTION_02, C.GOODS_OPTION_03,
										 C.GOODS_OPTION_04, C.GOODS_OPTION_NM_01, C.GOODS_OPTION_NM_02,
										 C.GOODS_OPTION_NM_03, C.GOODS_OPTION_NM_04, C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, 
										 C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 C.ORDER_STATE, C.BUY_CP_NO, C.FINISH_DATE, O.O_MEM_NM, O.R_MEM_NM, C.CONFIRM_TF, C.CONFIRM_DATE, 
										 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_DATE, C.SALE_CONFIRM_YMD, C.SALE_CONFIRM_ADM,
										 O.CP_NO,
										 ((C.SALE_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY)) AS SUM_PRICE, 
										 ((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) AS PLUS_PRICE, 
										 ROUND((((C.SALE_PRICE * C.QTY) - (C.BUY_PRICE * C.QTY)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE, C.TAX_TF,
										 CASE WHEN C.ORDER_STATE = '7' THEN -C.QTY ELSE C.QTY END AS QQTY
								FROM TBL_ORDER O, TBL_ORDER_GOODS C
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND C.ORDER_STATE IN ('3','7') 
								 AND C.CATE_04 <> 'CHANGE' ";
		*/

		if ($order_field == "C.CONFIRM_DATE") {
			$str_con_date = "C.CONFIRM_DATE";
		} else if ($order_field == "C.ORDER_DATE") {
			$str_con_date = "C.ORDER_DATE";
		} else {
			$str_con_date = "C.FINISH_DATE";
		}

		if ($start_date <> "") {
			$query .= " AND ".$str_con_date." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$str_con_date." <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sale_cp_no."' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND C.SALE_CONFIRM_TF = '".$confirm_tf."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND C.TAX_TF = '".$tax_tf."' ";
		}

		if ($etc_condition <> "") {
			$query .= $etc_condition;
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		if ($order_field == "") 
			$order_field = "C.FINISH_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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

	function totalCntSaleConfirmOrderGoods($db, $start_date, $end_date, $buy_cp_no, $sale_cp_no, $confirm_tf, $tax_tf, $etc_condition, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT 
							 FROM TBL_ORDER O, TBL_ORDER_GOODS C 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND O.RESERVE_NO = C.RESERVE_NO
								 AND C.ORDER_STATE IN ('3','7') 
								 AND C.CATE_04 <> 'CHANGE' ";

		if ($order_field == "C.CONFIRM_DATE") {
			$str_con_date = "C.CONFIRM_DATE";
		} else if ($order_field == "C.ORDER_DATE") {
			$str_con_date = "C.ORDER_DATE";
		} else {
			$str_con_date = "C.FINISH_DATE";
		}

		if ($start_date <> "") {
			$query .= " AND ".$str_con_date." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$str_con_date." <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sale_cp_no."' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND C.SALE_CONFIRM_TF = '".$confirm_tf."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND C.TAX_TF = '".$tax_tf."' ";
		}

		if ($etc_condition <> "") {
			$query .= $etc_condition;
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	// ???????? - ????????, ???????? ????
	function updateSaleConfirmState($db, $order_goods_no, $confirm_ymd, $confirm_tf, $up_adm_no) {
		
		if ($confirm_tf == "Y") {

			$query = "UPDATE TBL_ORDER_GOODS SET SALE_CONFIRM_TF = 'Y', SALE_CONFIRM_DATE = now(), 
																				 SALE_CONFIRM_YMD = '$confirm_ymd',
																				 SALE_CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND SALE_CONFIRM_TF = 'N' ";
		} else {

			$query = "UPDATE TBL_ORDER_GOODS SET SALE_CONFIRM_TF = 'N', SALE_CONFIRM_DATE = NULL, 
																				 SALE_CONFIRM_YMD = '',
																				 SALE_CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND SALE_CONFIRM_TF = 'Y' ";
		}
		
		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}





	function listBuyOrder($db, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBuyOrder($db, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, O.ORDER_NO, OG.RESERVE_NO, OG.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, OG.ORDER_STATE, 
										 (OG.PRICE  * OG.QTY) AS TOTAL_PRICE, 
										 (OG.BUY_PRICE  * OG.QTY) AS TOTAL_BUY_PRICE, 
										 (OG.SALE_PRICE  * OG.QTY) AS TOTAL_SALE_PRICE, 
										 (OG.EXTRA_PRICE  * OG.QTY) AS TOTAL_EXTRA_PRICE, 
										 (OG.DELIVERY_PRICE  * OG.QTY) AS TOTAL_DELIVERY_PRICE, 
										 OG.QTY AS TOTAL_QTY,
										 OG.ORDER_DATE, 
										 OG.PAY_DATE, 
										 O.PAY_TYPE, 
										 O.DELIVERY_TYPE, 
										 OG.DELIVERY_DATE, 
										 OG.FINISH_DATE, 
										 OG.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE, O.O_MEM_NM,
										 ((OG.SALE_PRICE  * OG.QTY) - (OG.BUY_PRICE  * OG.QTY)) AS TOTAL_PLUS_PRICE,
										 ROUND((((OG.SALE_PRICE  * OG.QTY) - (OG.BUY_PRICE  * OG.QTY)) / 
										 ((OG.SALE_PRICE  * OG.QTY)) * 100),2) AS LEE,
										 (((OG.SALE_PRICE  * OG.QTY) + (OG.EXTRA_PRICE  * OG.QTY) + (OG.DELIVERY_PRICE  * OG.QTY)) - (OG.BUY_PRICE  * OG.QTY)) AS TOTAL_PLUS_PRICE_DEL,
										 ROUND(((((OG.SALE_PRICE  * OG.QTY) + (OG.EXTRA_PRICE  * OG.QTY) + (OG.DELIVERY_PRICE  * OG.QTY)) - (OG.BUY_PRICE  * OG.QTY)) / 
										 ((OG.SALE_PRICE  * OG.QTY) + (OG.EXTRA_PRICE  * OG.QTY) + (OG.DELIVERY_PRICE  * OG.QTY)) * 100),2) AS LEE_DEL
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG
							 WHERE O.RESERVE_NO = OG.RESERVE_NO ";

		if ($start_date <> "") {
			$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND OG.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "GOODS_NAME") {
				$query .= " AND RESERVE_NO IN (
												SELECT RESERVE_NO 
													FROM TBL_ORDER_GOODS 
												 WHERE GOODS_NAME LIKE '%".$search_str."%'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
										) "; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;


		//$query .= " ORDER BY O.ORDER_NO DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntBuyOrder($db, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(*) CNT 								
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG
							 WHERE O.RESERVE_NO = OG.RESERVE_NO ";

		if ($start_date <> "") {
			$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE = '".$order_state."' ";
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "GOODS_NAME") {
				$query .= " AND RESERVE_NO IN (
												SELECT RESERVE_NO 
													FROM TBL_ORDER_GOODS 
												 WHERE GOODS_NAME LIKE '%".$search_str."%'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
										) "; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listAllBuyOrder($db, $start_date, $end_date, $order_state, $cp_no, $buy_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
										 SUM(AA.SALE_PRICE * AA.QTY) ALL_SALE_PRICE,
										 SUM(AA.EXTRA_PRICE * AA.QTY) ALL_EXTRA_PRICE,
										 SUM(AA.QTY) ALL_QTY,
										 SUM(AA.DELIVERY_PRICE) ALL_DELIVERY_PRICE,
										 SUM(AA.SA_DELIVERY_PRICE * AA.QTY) ALL_SA_DELIVERY_PRICE,
										 SUM(AA.SALE_PRICE * AA.QTY) + SUM(AA.EXTRA_PRICE * AA.QTY) + SUM(AA.DELIVERY_PRICE) AS ALL_SUM_PRICE,
										 (SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) AS ALL_PLUS_PRICE,
										 ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS ALL_LEE
								FROM
										 (SELECT	
															CASE A.ORDER_STATE 
																WHEN '0' THEN A.QTY
																WHEN '1' THEN A.QTY
																WHEN '2' THEN A.QTY
																WHEN '3' THEN A.QTY
																WHEN '4' THEN -A.QTY
																WHEN '6' THEN -A.QTY
																WHEN '7' THEN -A.QTY
																WHEN '8' THEN -A.QTY
														 END AS QTY,
														 A.BUY_PRICE,
														 A.SALE_PRICE, 
														 A.EXTRA_PRICE, 
														 A.DELIVERY_PRICE, 
														 A.SA_DELIVERY_PRICE
												FROM TBL_ORDER_GOODS A, TBL_ORDER B
											 WHERE A.RESERVE_NO = B.RESERVE_NO 
												 AND A.USE_TF = 'Y' ";

		if ($start_date <> "") {
			$query .= " AND B.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND A.ORDER_STATE  = '".$order_state."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO  = '".$cp_no."' ";
		}

		if ($pay_type <> "") {
			$query .= " AND B.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "GOODS_NAME") {
				$query .= " AND RESERVE_NO IN (
												SELECT RESERVE_NO 
													FROM TBL_ORDER_GOODS 
												 WHERE GOODS_NAME LIKE '%".$search_str."%'
													 AND USE_TF = 'Y'
													 AND DEL_TF = 'N'
										) "; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= "	) AA";

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


	function insertTempDelivery($db, $file_nm, $reserve_no, $order_goods_no, $qty, $option, $r_mem_nm, $r_phone, $r_zipcode, $r_addr, $goods_name, $delivery_cp, $delivery_no, $cp_no, $buy_cp_no, $reg_adm) {
		
		$query="INSERT INTO TBL_TEMP_DELIVERY (TEMP_NO, RESERVE_NO, ORDER_GOODS_NO, QTY, OPTIONS, R_MEM_NM, R_PHONE, R_ZIPCODE,
																					 R_ADDR, GOODS_NAME, DELIVERY_CP, DELIVERY_NO, CP_NO, BUY_CP_NO, REG_ADM, REG_DATE) 
													 values ('$file_nm', '$reserve_no', '$order_goods_no', '$qty', '$option', '$r_mem_nm', '$r_phone', '$r_zipcode',
																					 '$r_addr', '$goods_name', '$delivery_cp', '$delivery_no', '$cp_no', '$buy_cp_no', '$reg_adm', now()); ";
		
		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function listTempDelivery($db, $temp_no) {

		$query = "SELECT SEQ_NO, RESERVE_NO, ORDER_GOODS_NO, QTY, OPTIONS, R_MEM_NM, R_PHONE, R_ZIPCODE,
										 R_ADDR, GOODS_NAME, DELIVERY_CP, DELIVERY_NO, CP_NO, BUY_CP_NO, REG_ADM, REG_DATE
								FROM TBL_TEMP_DELIVERY WHERE TEMP_NO = '$temp_no' ";

		
		$query .= " ORDER BY ORDER_GOODS_NO asc ";

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

	function selectTempDelivery($db, $temp_no, $seq_no) {

		$query = "SELECT SEQ_NO, RESERVE_NO, ORDER_GOODS_NO, GOODS_NAME, R_MEM_NM, DELIVERY_CP, DELIVERY_NO, REG_ADM, REG_DATE
								FROM TBL_TEMP_DELIVERY WHERE TEMP_NO = '$temp_no' AND SEQ_NO = '$seq_no' ";

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

	function updateTempDelivery($db, $delivery_cp, $delivery_no, $temp_no, $seq_no) {

		$query="UPDATE TBL_TEMP_DELIVERY SET 
													DELIVERY_CP								= '$delivery_cp',
													DELIVERY_NO						= '$delivery_no'
										WHERE TEMP_NO							= '$temp_no'
											AND SEQ_NO							= '$seq_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempDelivery($db, $temp_no, $seq_no) {

		$query="DELETE FROM TBL_TEMP_DELIVERY WHERE TEMP_NO = '$temp_no' AND SEQ_NO = '$seq_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function insertTempToRealDelivery($db, $temp_no, $str_seq_no) {
		
		
		$query="SELECT SEQ_NO, RESERVE_NO, ORDER_GOODS_NO, GOODS_NAME, R_MEM_NM, DELIVERY_CP, DELIVERY_NO, REG_ADM, REG_DATE
							FROM TBL_TEMP_DELIVERY
						 WHERE TEMP_NO = '$temp_no' AND SEQ_NO IN ($str_seq_no) ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		if (sizeof($record) > 0) {

			for ($j = 0 ; $j < sizeof($record); $j++) {

				$RESERVE_NO					= trim($record[$j]["RESERVE_NO"]);
				$ORDER_GOODS_NO			= trim($record[$j]["ORDER_GOODS_NO"]);
				$DELIVERY_CP				= trim($record[$j]["DELIVERY_CP"]);
				$DELIVERY_NO				= trim($record[$j]["DELIVERY_NO"]);
				$REG_ADM						= trim($record[$j]["REG_ADM"]);

				updateDeliveryState($db, $RESERVE_NO, $ORDER_GOODS_NO, $DELIVERY_CP, $DELIVERY_NO, $REG_ADM);

			}
		}
		
		#echo $query;

		if(!$result) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempToRealDelivery($db, $temp_no, $str_seq_no) {
		

		$query=" DELETE FROM  TBL_TEMP_DELIVERY WHERE TEMP_NO = '$temp_no' AND SEQ_NO IN ($str_seq_no) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}



	function setOrderStateAll ($db) {

		$query = "SELECT RESERVE_NO FROM TBL_ORDER WHERE DEL_TF = 'N' AND USE_TF = 'Y'";

		//echo $query."<br>";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row	= mysql_fetch_array($result);
			$RESERVE_NO			= Trim($row[0]);
		
			$query3 = "SELECT ORDER_STATE FROM TBL_ORDER_GOODS WHERE RESERVE_NO	= '$RESERVE_NO' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
			$result3 = mysql_query($query3,$db);
			$total3  = mysql_affected_rows();
		
			$tmp_order_state = "";

			for($j=0 ; $j< $total3 ; $j++) {
				mysql_data_seek($result3,$j);
				$row3	= mysql_fetch_array($result3);
				$RS_ORDER_STATE			= Trim($row3[0]);
				if ($j == 0) {
					$tmp_order_state = $RS_ORDER_STATE;
				} else {
					$tmp_order_state .= ",".$RS_ORDER_STATE;
				}
			}

			$query4 = "UPDATE TBL_ORDER SET 
												ORDER_STATE		= '$tmp_order_state' ";
			$query4 .=	" WHERE RESERVE_NO		= '$RESERVE_NO' ";

			echo $query4."<br>";
			
			mysql_query($query4,$db);
		}
	}

	function getSaleCompanyName($db, $reserve_no) {
	
		$query = "SELECT CP_NO FROM TBL_ORDER WHERE RESERVE_NO = '$reserve_no' LIMIT 1 ";
			
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];

		$record = getCompanyName($db, $record);
		return $record;
	}


	function updateOrderGoodsOption($db, $order_goods_no, $goods_option_nm_01, $reg_adm) {

		$query=" UPDATE TBL_ORDER_GOODS SET 
							GOODS_OPTION_01 = '',
							GOODS_OPTION_02 = '',
							GOODS_OPTION_03 = '',
							GOODS_OPTION_04 = '',
							GOODS_OPTION_NM_01 = '$goods_option_nm_01',
							GOODS_OPTION_NM_02 = '',
							GOODS_OPTION_NM_03 = '',
							GOODS_OPTION_NM_04 = ''
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateOrderGoodsOptionWork($db, $reserve_no, $order_goods_no, $old_cate_01, $cate_01, $cate_02, $opt_wrap_no, $opt_sticker_no, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $cp_order_no, $opt_outstock_date, $delivery_type, $old_sa_delivery_price, $sa_delivery_price, $opt_memo, $memos, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box, $s_adm_no) {
	
		$opt_request_memo = $memos["opt_request_memo"];
		$opt_support_memo = $memos["opt_support_memo"];

		/*
		//???? -> ????, ???? -> ????, ???? -> ????
		if($old_cate_01 == $cate_01) 
			$sale_price_correction = 1;
		else
		{
			//???? -> ????, ???? -> ????
			if($old_cate_01 == "")
				$sale_price_correction = -1;
			else {  
				// ????, ???? -> ????
				if($cate_01 == "")
					$sale_price_correction = -1;
				else //???? -> ????, ???? -> ????
					$sale_price_correction = 1;
			}
		}
		*/

		$query=" UPDATE TBL_ORDER_GOODS SET 
							CATE_01 = '$cate_01',
							CATE_02 = '$cate_02',
							OPT_WRAP_NO = '$opt_wrap_no',
							OPT_STICKER_NO = '$opt_sticker_no',
							OPT_STICKER_MSG = '$opt_sticker_msg',
							OPT_PRINT_MSG = '$opt_print_msg',
							OPT_OUTBOX_TF = '$opt_outbox_tf',
							CP_ORDER_NO = '$cp_order_no',
							OPT_OUTSTOCK_DATE = '$opt_outstock_date',
							DELIVERY_TYPE = '$delivery_type',
							SA_DELIVERY_PRICE = '$sa_delivery_price',
							OPT_MEMO = '$opt_memo',
							OPT_REQUEST_MEMO = '$opt_request_memo',
							OPT_SUPPORT_MEMO = '$opt_support_memo',
							DELIVERY_CP = '$delivery_cp',
							SENDER_NM = '$sender_nm',
							SENDER_PHONE = '$sender_phone',
							DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box'

							WHERE ORDER_GOODS_NO = '$order_goods_no' AND RESERVE_NO = '$reserve_no' ";
		
		//,
		//					SALE_PRICE = SALE_PRICE * '$sale_price_correction'
		//echo $query;
		//exit;
		if(!mysql_query($query,$db)){
			echo "<script>alert('?????? ?????????????? -func:updateOrderGoodsOptionWork()_UPDATE_TBL_ORDER_GOODS_".mysql_errno()." : ".mysql_error()."');</script>";
			exit;
		}


		$change_total_sa_delivery_price = $sa_delivery_price  - $old_sa_delivery_price;
		
		if ($order_state == "3") {
			$change_total_sa_delivery_price = -$change_total_sa_delivery_price;
		} else {
			$change_total_sa_delivery_price = $change_total_sa_delivery_price;
		}
		
		$query = "UPDATE TBL_ORDER 
					 SET TOTAL_SA_DELIVERY_PRICE = TOTAL_SA_DELIVERY_PRICE + ".$change_total_sa_delivery_price."
					 WHERE RESERVE_NO  = '$reserve_no' ";
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			
			echo "<script>alert('[1]?????? ?????????????? -func:updateOrderGoodsOptionWork()_UPDATE_TBL_ORDER_ ".mysql_errno().":".mysql_error()."'); //history.go(-1);</script>";
			return false;
			exit;
		} else {
			return true;
		}

	}

	function updateDeliveryOrder($db, $reserve_no, $total_delivery_price) {

		$query=" UPDATE TBL_ORDER SET 
							TOTAL_DELIVERY_PRICE = '$total_delivery_price'
							WHERE RESERVE_NO = '$reserve_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//?????????? - ?????? ???? - ????????
	function updateClaimOrderFinish($db, $reserve_no, $order_seq, $bb_code, $bb_no, $upd_adm_no) {

		$query=" SELECT REPLY_STATE FROM TBL_BOARD
							WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];

		//echo $record;
		
		if ($record == "N") {

			$query=" UPDATE TBL_BOARD SET REPLY_DATE = now()
								WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

			mysql_query($query,$db);

			$query=" UPDATE TBL_ORDER_GOODS SET FINISH_DATE = now()
								WHERE RESERVE_NO = '$reserve_no' AND ORDER_SEQ = '$order_seq' ";

			mysql_query($query,$db);
		}

	}

	function cntOrderGoodsStateByMember($db, $order_state, $work_tf, $start_date, $end_date, $cp_no) {

		$query = "SELECT COUNT(OG.ORDER_GOODS_NO) AS CNT
					FROM TBL_MEMBER M 
					JOIN TBL_ORDER_GOODS OG ON M.MEM_NO = OG.MEM_NO
				   WHERE OG.USE_TF= 'Y' 
					 AND OG.DEL_TF = 'N' 
					 AND OG.ORDER_DATE > '$start_date'
					 AND OG.ORDER_DATE <= '$end_date 23:59:59'
					 AND OG.ORDER_STATE = '$order_state'
					 AND M.CP_NO = '$cp_no'
					 
					 ";
		
		if ($work_tf <> "") {
			$query .= " AND OG.WORK_FLAG = '".$work_tf."' ";
		}

		/*
		// ???????? ???? ????
		$query .= " AND (

						SELECT SUM( 
									CASE ORDER_STATE
									WHEN  '0'
									THEN QTY
									WHEN  '1'
									THEN QTY
									WHEN  '2'
									THEN QTY
									WHEN  '3'
									THEN QTY
									WHEN  '4'
									THEN - QTY
									WHEN  '6'
									THEN - QTY
									WHEN  '7'
									THEN - QTY
									WHEN  '8'
									THEN - QTY
									END 
								   ) AS TOTAL_QTY
						FROM TBL_ORDER_GOODS
						WHERE (
									(
										ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND GROUP_NO =0
								    )
								OR  (
										GROUP_NO = OG.ORDER_GOODS_NO
									)
								)
							AND USE_TF =  'Y'
							AND DEL_TF =  'N'
						) > 0

		";
		*/

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;


	}

	function cntOrderGoodsState($db, $order_state, $cp_no, $buy_cp_no) {

		/*
		$query = "SELECT COUNT(C.ORDER_GOODS_NO) AS CNT
								FROM TBL_ORDER_GOODS C, TBL_ORDER G 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND C.RESERVE_NO = G.RESERVE_NO 
								 AND G.TOTAL_QTY <> 0 ";
		
		if ($order_state <> "") {
			$query .= " AND C.ORDER_STATE = '".$order_state."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND G.CP_NO = '".$cp_no."' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
		
		*/

		$query = "SELECT COUNT(C.ORDER_GOODS_NO) AS CNT
								FROM TBL_ORDER_GOODS C, TBL_ORDER G 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND C.RESERVE_NO = G.RESERVE_NO 
								 AND C.ORDER_DATE > '2014-01-01'
								 AND G.ORDER_DATE > '2014-01-01'
								 AND G.TOTAL_QTY <> 0 ";

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($order_state <> "") {
			$query .= " AND C.ORDER_STATE = '".$order_state."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND G.CP_NO = '".$cp_no."' ";
		}

		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;


	}

	function cntOrderGoodsStateAsDate($db, $order_state, $cp_no, $buy_cp_no) {

		/*
		$query = "SELECT COUNT(C.ORDER_GOODS_NO) AS CNT
								FROM TBL_ORDER_GOODS C, TBL_ORDER G 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND C.RESERVE_NO = G.RESERVE_NO 
								 AND G.TOTAL_QTY <> 0 ";
		
		if ($order_state <> "") {
			$query .= " AND C.ORDER_STATE = '".$order_state."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND G.CP_NO = '".$cp_no."' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
		
		*/

		$query = "SELECT COUNT(C.ORDER_GOODS_NO) AS CNT
								FROM TBL_ORDER_GOODS C, TBL_ORDER G 
							 WHERE C.USE_TF= 'Y' 
								 AND C.DEL_TF = 'N' 
								 AND C.RESERVE_NO = G.RESERVE_NO 
								 AND C.ORDER_DATE > '2013-01-01'
								 AND G.TOTAL_QTY <> 0 ";

		if ($buy_cp_no <> "") {
			$query .= " AND C.BUY_CP_NO = '".$buy_cp_no."' ";
		}

		if ($order_state <> "") {
			$query .= " AND C.ORDER_STATE = '".$order_state."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND G.CP_NO = '".$cp_no."' ";
		}

		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;


	}

	function updateOrderGoodsDelivery($db, $order_goods_no, $delivery_price, $reserve_no, $reg_adm_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET DELIVERY_PRICE = '$delivery_price' WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		mysql_query($query,$db);

		$query = "SELECT SUM(AA.DELIVERY_PRICE) AS ALL_DELIVERY_PRICE
								FROM
										 (SELECT	
															CASE A.ORDER_STATE 
																WHEN '0' THEN A.QTY
																WHEN '1' THEN A.QTY
																WHEN '2' THEN A.QTY
																WHEN '3' THEN A.QTY
																WHEN '4' THEN -A.QTY
																WHEN '6' THEN -A.QTY
																WHEN '7' THEN -A.QTY
																WHEN '8' THEN -A.QTY
														 END AS QTY,
														 A.EXTRA_PRICE, 
														 A.DELIVERY_PRICE
												FROM TBL_ORDER_GOODS A, TBL_ORDER B
											 WHERE A.RESERVE_NO = B.RESERVE_NO 
												 AND A.USE_TF = 'Y' 
												 AND A.DEL_TF = 'N'
												 AND A.RESERVE_NO = '$reserve_no'
											) AA ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];


		$query = "UPDATE TBL_ORDER SET TOTAL_DELIVERY_PRICE = '$record' WHERE RESERVE_NO = '$reserve_no' ";
			
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}	
	}

	function updateOrderGoodsDeliveryNo($db, $order_goods_no, $delivery_cp, $delivery_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET 
												DELIVERY_CP = '$delivery_cp', 
												DELIVERY_NO = '$delivery_no' 
								WHERE ORDER_GOODS_NO = '$order_goods_no' ";
			
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}	
	}

	// ???? ???? ??????
	function listConfirmCpOrderList($db, $confirm_ymd, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {
		
		$query = "SELECT G.RESERVE_NO, O.O_MEM_NM, O.R_MEM_NM, G.GOODS_NAME, G.CONFIRM_YMD, G.BUY_CP_NO, G.ORDER_STATE,
										 G.FINISH_DATE,
										 CASE G.ORDER_STATE 
										 WHEN '0' THEN G.QTY 
										 WHEN '1' THEN G.QTY 
										 WHEN '2' THEN G.QTY 
										 WHEN '3' THEN G.QTY 
										 WHEN '4' THEN -G.QTY 
										 WHEN '6' THEN -G.QTY 
										 WHEN '7' THEN -G.QTY 
										 WHEN '8' THEN -G.QTY 
										 END AS QTY,
										 CASE G.ORDER_STATE 
										 WHEN '0' THEN 1 
										 WHEN '1' THEN 1 
										 WHEN '2' THEN 1 
										 WHEN '3' THEN 1 
										 WHEN '4' THEN -1 
										 WHEN '6' THEN -1 
										 WHEN '7' THEN -1 
										 WHEN '8' THEN -1 
										 END AS DELIVERY_QTY,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.BUY_PRICE ELSE 0 END AS BUY_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.SALE_PRICE ELSE 0 END AS SALE_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.EXTRA_PRICE ELSE 0 END AS EXTRA_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.DELIVERY_PRICE ELSE 0 END AS DELIVERY_PRICE,
										 G.SA_DELIVERY_PRICE 
								FROM TBL_ORDER_GOODS G, TBL_ORDER O 
							 WHERE G.RESERVE_NO = O.RESERVE_NO 
								 AND G.CONFIRM_YMD <> ''
								 AND G.CONFIRM_TF = 'Y'
								 AND G.USE_TF = 'Y' 
								 AND G.DEL_TF = 'N'
								 AND G.ORDER_STATE IN ('3','7') 
								 AND G.CATE_04 <> 'CHANGE' ";

		if ($confirm_ymd <> "") {
			$query .= " AND G.CONFIRM_YMD = '".$confirm_ymd."' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.RESERVE_NO like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%'  )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ORDER BY G.ORDER_GOODS_NO DESC ";
		
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


	// ???? ???? ??????
	function listSaleConfirmCpOrderList($db, $confirm_ymd, $cp_no, $use_tf, $del_tf, $search_field, $search_str) {
		
		$query = "SELECT G.RESERVE_NO, O.O_MEM_NM, O.R_MEM_NM, G.GOODS_NAME, G.SALE_CONFIRM_YMD, O.CP_NO, G.ORDER_STATE,
										 G.FINISH_DATE,
										 CASE G.ORDER_STATE 
										 WHEN '0' THEN G.QTY 
										 WHEN '1' THEN G.QTY 
										 WHEN '2' THEN G.QTY 
										 WHEN '3' THEN G.QTY 
										 WHEN '4' THEN -G.QTY 
										 WHEN '6' THEN -G.QTY 
										 WHEN '7' THEN -G.QTY 
										 WHEN '8' THEN -G.QTY 
										 END AS QTY,
										 CASE G.ORDER_STATE 
										 WHEN '0' THEN 1 
										 WHEN '1' THEN 1 
										 WHEN '2' THEN 1 
										 WHEN '3' THEN 1 
										 WHEN '4' THEN -1 
										 WHEN '6' THEN -1 
										 WHEN '7' THEN -1 
										 WHEN '8' THEN -1 
										 END AS DELIVERY_QTY,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.BUY_PRICE ELSE 0 END AS BUY_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.SALE_PRICE ELSE 0 END AS SALE_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.EXTRA_PRICE ELSE 0 END AS EXTRA_PRICE,
										 CASE G.SA_DELIVERY_PRICE 
										 WHEN '0' THEN G.DELIVERY_PRICE ELSE 0 END AS DELIVERY_PRICE,
										 G.SA_DELIVERY_PRICE 
								FROM TBL_ORDER_GOODS G, TBL_ORDER O 
							 WHERE G.RESERVE_NO = O.RESERVE_NO 
								 AND G.SALE_CONFIRM_YMD <> ''
								 AND G.SALE_CONFIRM_TF = 'Y'
								 AND G.USE_TF = 'Y' 
								 AND G.DEL_TF = 'N'
								 AND G.ORDER_STATE IN ('3','7') 
								 AND G.CATE_04 <> 'CHANGE' ";

		if ($confirm_ymd <> "") {
			$query .= " AND G.SALE_CONFIRM_YMD = '".$confirm_ymd."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO  = '".$cp_no."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.RESERVE_NO like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%'  )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ORDER BY G.ORDER_GOODS_NO DESC ";
		
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

	function updateOrderGoodsSalePrice($db, $old_sale_price, $sale_price, $qty, $order_state, $order_goods_no, $reserve_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET SALE_PRICE = '$sale_price' WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		mysql_query($query,$db);

		$change_total_price = (($sale_price - $old_sale_price) * $qty);
		
		if ($order_state == "3") {
			$change_total_price = $change_total_price;
		} else {
			$change_total_price = -$change_total_price;
		}

		$query = "UPDATE TBL_ORDER SET TOTAL_SALE_PRICE = TOTAL_SALE_PRICE + ".$change_total_price." WHERE RESERVE_NO  = '$reserve_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderGoodsSalePriceOrderRead($db, $old_sale_price, $sale_price, $qty, $order_state, $order_goods_no, $reserve_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET SALE_PRICE = '$sale_price' WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		mysql_query($query,$db);

		$change_total_price = (($sale_price - $old_sale_price) * $qty);
		
		if ($order_state == "3") {
			$change_total_price = -$change_total_price;
		} else {
			$change_total_price = $change_total_price;
		}

		$query = "UPDATE TBL_ORDER SET TOTAL_SALE_PRICE = TOTAL_SALE_PRICE + ".$change_total_price." WHERE RESERVE_NO  = '$reserve_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderGoodsSalePriceOrderReadAdvanced($db, $old_sale_price, $old_qty, $old_discount_price, $sale_price, $qty, $discount_price, $order_state, $order_goods_no, $reserve_no) {
		
		// ?????????? ????, ????, ?????? ????
		$query = "UPDATE TBL_ORDER_GOODS 
					 SET SALE_PRICE = '$sale_price', 
					     QTY = '$qty', 
						 WORK_REQ_QTY='$qty',
						 REFUNDABLE_QTY='$qty',
						 DISCOUNT_PRICE = '$discount_price'
					 WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		if(mysql_query($query,$db)) { 

			
			$query = "UPDATE TBL_ORDER_GOODS 
					 SET SALE_PRICE = '$sale_price' 
					 WHERE GROUP_NO = '$order_goods_no' ";
		
			mysql_query($query,$db);
			

		}


		/* ???????? ???????? ????
		//2017-05-02 ?????? ???? ??????, ???? ?????? ?????? ?? ?????????? ???????? ???????? ?????? ?????? ????
		$query = "SELECT SALE_CONFIRM_TF, DELIVERY_TYPE, GOODS_NO
					FROM TBL_ORDER_GOODS 
				   WHERE ORDER_GOODS_NO = '$order_goods_no'
				 ";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$SALE_CONFIRM_TF	= $record[$i]["SALE_CONFIRM_TF"];
				$DELIVERY_TYPE		= $record[$i]["DELIVERY_TYPE"];
				$GOODS_NO			= $record[$i]["GOODS_NO"];

				//???? ?????????? ???? ?????? ???? ????
				if($SALE_CONFIRM_TF == "Y" && $DELIVERY_TYPE != "3") { 
					$query = " UPDATE TBL_COMPANY_LEDGER
							  SET 
								  UNIT_PRICE = '".$sale_price."',
								  DEPOSIT = ".$sale_price." * QTY
							WHERE INOUT_TYPE = '????' AND GOODS_NO = '".$GOODS_NO."' AND ORDER_GOODS_NO = '".$order_goods_no."' AND GRGL_NO IS NULL AND RGN_NO IS NULL ";
					//echo $query;
					//exit;
					mysql_query($query,$db);
				}
			}
		}
		*/
	}

	function getClaimMemo ($db, $reserve_no) {
		
		$query = "SELECT CONTENTS FROM TBL_BOARD WHERE CATE_01 = '$reserve_no' AND DEL_TF ='N' AND CATE_04 IN ('8','9') ORDER BY BB_NO ASC";
		
		$result = mysql_query($query,$db);
		$record = array();
		
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		$CONTENTS = "";

		if (sizeof($record) > 0) {
			for ($h = 0 ; $h < sizeof($record); $h++) {
				$CONTENTS	.= trim($record[$h]["CONTENTS"])."<br><br>";
			}
		}
		
		return $CONTENTS;

		//SELECT CONTENTS FROM TBL_BOARD WHERE CATE_01 = '20150610E00108' AND DEL_TF ='N' AND CATE_04 IN ('8','9') ORDER BY ORDER_GOODS_NO ASC

	}

	function insertTempOrderGoods($db, $file_nm, $order_date, $order_no, $order_seq, $goods_no, $goods_code, $goods_name, $goods_price, $qty, $opt_sticker_no, $opt_sticker_code, $opt_sticker_msg, $opt_outbox_tf, $opt_wrap_no, $opt_wrap_code, $opt_print_msg, $opt_outstock_date, $opt_memo, $delivery_type_code, $delivery_type, $delivery_price, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box) {


		$query="INSERT INTO TBL_TEMP_ORDER_GOODS (TEMP_NO, ORDER_DATE, ORDER_NO, ORDER_SEQ, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_PRICE, QTY, OPT_STICKER_NO, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_OUTBOX_TF, OPT_WRAP_NO, OPT_WRAP_CODE, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MEMO, DELIVERY_TYPE_CODE, DELIVERY_TYPE, DELIVERY_PRICE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX) 
													 values ('$file_nm', '$order_date', '$order_no', '$order_seq', '$goods_no', '$goods_code', '$goods_name', '$goods_price', '$qty', '$opt_sticker_no', '$opt_sticker_code', '$opt_sticker_msg', '$opt_outbox_tf', '$opt_wrap_no', '$opt_wrap_code', '$opt_print_msg', '$opt_outstock_date', '$opt_memo', '$delivery_type_code', '$delivery_type', '$delivery_price', '$delivery_cp', '$sender_nm', '$sender_phone', '$delivery_cnt_in_box'); ";
		
		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	
	function selectTempOrderGoods($db, $temp_no, $order_no) {

		$query = "SELECT TEMP_NO, ORDER_DATE, ORDER_NO, ORDER_SEQ, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_PRICE, QTY, 
						  OPT_STICKER_NO, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_OUTBOX_TF, OPT_OUTBOX_TF_CODE, OPT_WRAP_NO, OPT_WRAP_CODE, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE,  OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE_CODE, DELIVERY_TYPE, DELIVERY_PRICE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX
					FROM TBL_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' AND ORDER_NO = '$order_no' ";

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


	function listWorkOrderGoods($db, $reserve_no, $use_tf, $del_tf, $start_date, $end_date, $cp_type, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntWorkOrderGoods($db, $reserve_no, $use_tf, $del_tf, $start_date, $end_date, $cp_type, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO, O.MEM_NO, OG.GOODS_NAME, OG.QTY,								O.OPT_MANAGER_NO, G.STOCK_CNT, OG.OPT_MEMO
			  	  FROM TBL_ORDER O 
 				  JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
				  JOIN TBL_GOODS G ON OG.GOODS_NO = G.GOODS_NO
				  WHERE O.USE_TF= 'Y' 
					AND O.DEL_TF = 'N' 
					AND OG.OPT_OUTSTOCK_DATE > '$start_date' 
					AND OG.OPT_OUTSTOCK_DATE <= '$end_date 23:59:59'
								 ";

		if ($reserve_no <> "") {
			$query .= " AND OG.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($cp_type <> "") {
			$query .= " AND O.CP_NO = '".$cp_type."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND OG.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND OG.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " ORDER BY OG.OPT_OUTSTOCK_DATE DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntWorkOrderGoods($db, $reserve_no, $use_tf, $del_tf, $start_date, $end_date, $cp_type, $search_field, $search_str){

		$query ="SELECT COUNT(*)
					FROM TBL_ORDER O 
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_GOODS G ON OG.GOODS_NO = G.GOODS_NO
					WHERE O.USE_TF = 'Y' 
					  AND O.DEL_TF = 'N' 
					  AND OG.OPT_OUTSTOCK_DATE > '$start_date' 
					  AND OG.OPT_OUTSTOCK_DATE <= '$end_date 23:59:59' ";

		if ($reserve_no <> "") {
			$query .= " AND OG.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($cp_type <> "") {
			$query .= " AND O.CP_NO = '".$cp_type."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND OG.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND OG.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function totalCntTempOrder($db, $file_nm) {

		$query ="SELECT COUNT(*) FROM TBL_TEMP_ORDER WHERE TEMP_NO = '$file_nm'  ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function updateOrderOrderInfo($db, $o_mem_nm, $o_phone, $o_hphone, $o_zipcode, $o_addr1, $o_email, $opt_manager_no, $reserve_no) {

		$query = "UPDATE TBL_ORDER
				  SET 
				  	O_MEM_NM	= '$o_mem_nm',
					O_PHONE     = '$o_phone',
					O_HPHONE     = '$o_hphone',
					O_ZIPCODE     = '$o_zipcode',
					O_ADDR1     = '$o_addr1',
					O_EMAIL     = '$o_email',
					OPT_MANAGER_NO     = '$opt_manager_no'
					WHERE RESERVE_NO = '$reserve_no'
				
				";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderReceiverInfo($db, $r_mem_nm, $r_email, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $memo, $reserve_no) {

		$query = "UPDATE TBL_ORDER
				  SET 
				  	R_MEM_NM	= '$r_mem_nm',
					R_EMAIL     = '$r_email',
					R_PHONE     = '$r_phone',
					R_HPHONE     = '$r_hphone',
					R_ZIPCODE     = '$r_zipcode',
					R_ADDR1     = '$r_addr1',
					MEMO     = '$memo'
				  WHERE RESERVE_NO = '$reserve_no'
				
				";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderGoodsStickerReady($db, $order_goods_no, $opt_sticker_ready) {

		$query=" UPDATE TBL_ORDER_GOODS SET 
							OPT_STICKER_READY = '$opt_sticker_ready'
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateOrderGoodsWorkQty($db, $order_goods_no, $work_qty) {

		$query=" UPDATE TBL_ORDER_GOODS SET 
							WORK_QTY = '$work_qty'
							WHERE ORDER_GOODS_NO = '$order_goods_no' AND QTY >= $work_qty ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateOrderGoodsWorkSeq($db, $order_goods_no, $work_seq) {

		$query=" UPDATE TBL_ORDER_GOODS SET 
							WORK_SEQ = '$work_seq'
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//???? ?????????????? ???????? ?????? ????
	function updateOrderGoodsDeliveryType($db, $order_goods_no, $delivery_type) {

		/*
		// ???? ?????????????? ?????????? -> ????,?????? ???? ?????????????? ???????? ??(??????????) ???? ???? ?????????????? ???????? ???? - ???? ?????? ???????? ????

		$query=" SELECT DELIVERY_TYPE
				   FROM TBL_ORDER_GOODS 
				  WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";
	
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$old_delivery_type	= $rows[0];

		//????????????, ???? (????????)????
		if($old_delivery_type == "98" || $old_delivery_type == "99") { 
			//????, ????????, ??, ???? (????????)???? ????????
			if($delivery_type == "0" || $delivery_type == "1" || $delivery_type == "2" || $delivery_type == "3") { 
			
				updateWorksFlagNOrderGoods($conn, $order_goods_no);

			}
		}
		*/

		$query=" UPDATE TBL_ORDER_GOODS SET 
							DELIVERY_TYPE = '$delivery_type'
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateOrderGoodsDeliveryCP($db, $order_goods_no, $delivery_cp) {

		$query=" UPDATE TBL_ORDER_GOODS SET 
							DELIVERY_CP = '$delivery_cp'
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function insertTempOrderMRO($db, $temp_no, $order_date, $cp_order_no, $o_mem_nm, $o_phone, $o_hphone, $o_addr1, $r_mem_nm, $r_phone, $r_hphone, $r_zipcode, $r_addr2, $goods_name, $qty, $sale_price, $sale_total_price, $delivery_state, $delivery_request, $memo, $delivery_no, $wrap_method) {

		$query = "INSERT INTO TBL_TEMP_ORDER_MRO (TEMP_NO, ORDER_DATE, CP_ORDER_NO, O_MEM_NM, O_PHONE, O_HPHONE, O_ADDR1, R_MEM_NM, R_PHONE, R_HPHONE, ZIPCODE, R_ADDR2, GOODS_NAME, QTY, SALE_PRICE, SALE_TOTAL_PRICE, DELIVERY_STATE, DELIVERY_REQUEST, MEMO, DELIVERY_NO, WRAP_METHOD)
				   
				   VALUES (	'$temp_no', '$order_date', '$cp_order_no', '$o_mem_nm', '$o_phone', '$o_hphone', '$o_addr1', '$r_mem_nm', '$r_phone', '$r_hphone', '$r_zipcode', '$r_addr2', '$goods_name', '$qty', '$sale_price', '$sale_total_price', '$delivery_state', '$delivery_request', '$memo', '$delivery_no', '$wrap_method' ); ";

		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
		
	}

	function listTempOrderMRO($db, $temp_no) {

		$query = "SELECT TEMP_NO, ORDER_DATE, CP_ORDER_NO, 
		                 O_MEM_NM, O_PHONE, O_HPHONE, O_ADDR1, R_MEM_NM, R_PHONE, R_HPHONE, ZIPCODE, R_ADDR2, 
						 GOODS_NAME, QTY, SALE_PRICE, SALE_TOTAL_PRICE, DELIVERY_STATE, DELIVERY_REQUEST, MEMO, DELIVERY_NO, WRAP_METHOD
				  FROM TBL_TEMP_ORDER_MRO	
				  WHERE TEMP_NO = '$temp_no' ";

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

	function insertTempOrderMROConversion($db, $temp_no, $order_date, $cp_order_no, $cp_no, $goods_code, $goods_name, $sale_price, $qty, $o_mem_nm, $o_phone, $o_hphone, $r_mem_nm, $r_phone, $r_hphone, $zipcode, $r_addr1, $memo, $opt_wrap_code, $opt_sticker_code, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $opt_manager_nm, $opt_outstock_date, $delivery_type, $delivery_price, $work_memo, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box ) {

		$query = "INSERT INTO TBL_TEMP_ORDER_MRO_CONVERSION	
							(TEMP_NO, ORDER_DATE, CP_ORDER_NO, CP_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE, QTY, O_MEM_NM, O_PHONE, O_HPHONE, R_MEM_NM, R_PHONE, R_HPHONE, ZIPCODE, R_ADDR1, MEMO, OPT_WRAP_CODE, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_PRINT_MSG, OPT_OUTBOX_TF, OPT_MANAGER_NM, OPT_OUTSTOCK_DATE, DELIVERY_TYPE, DELIVERY_PRICE, WORK_MEMO, DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX)
	
				   VALUES   ('$temp_no', '$order_date', '$cp_order_no', '$cp_no', '$goods_code', '$goods_name', '$sale_price', '$qty', '$o_mem_nm', '$o_phone', '$o_hphone', '$r_mem_nm', '$r_phone', '$r_hphone', '$zipcode', '$r_addr1', '$memo', '$opt_wrap_code', '$opt_sticker_code', '$opt_sticker_msg', '$opt_print_msg', '$opt_outbox_tf', '$opt_manager_nm', '$opt_outstock_date', '$delivery_type', '$delivery_price', '$work_memo', '$delivery_cp', '$sender_nm', '$sender_phone', '$delivery_cnt_in_box');
				 ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
		
	}

	function listTempOrderMROConversion($db, $temp_no) {

		$query = "SELECT ORDER_DATE, CP_ORDER_NO, CP_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE, QTY, 
		                 O_MEM_NM, O_PHONE, O_HPHONE, R_MEM_NM, R_PHONE, R_HPHONE, 
						ZIPCODE, R_ADDR1, MEMO, OPT_WRAP_CODE, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_PRINT_MSG, OPT_OUTBOX_TF, OPT_MANAGER_NM, OPT_OUTSTOCK_DATE, DELIVERY_TYPE, DELIVERY_PRICE, WORK_MEMO, DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX
				  FROM TBL_TEMP_ORDER_MRO_CONVERSION	
				  WHERE TEMP_NO = '$temp_no' ";

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

	function cntTempOrderMROConversion($db, $temp_no) {

		$query = "SELECT COUNT(*)
				  FROM TBL_TEMP_ORDER_MRO_CONVERSION	
				  WHERE TEMP_NO = '$temp_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
		
	}

	function insertTempOrderMROComplete($db, $temp_no, $order_date, $order_no, $seq, $seller_goods_code, $box_seq, $box_qty, $sender, $receiver, $reg_adm) { 

		$cp_order_no =  $order_date."_".$order_no."_".$seq;
		$query = "SELECT OG.DELIVERY_CP, CD.DCODE_NM, OG.DELIVERY_NO
					FROM TBL_ORDER_GOODS OG 
					LEFT JOIN TBL_CODE_DETAIL CD ON OG.DELIVERY_CP = CD.DCODE AND CD.PCODE = 'MRO_DELIVERY_CP' 
				   WHERE OG.CP_ORDER_NO = '$cp_order_no' 
				    AND OG.DELIVERY_DATE <> '0000-00-00 00:00:00' 
				    AND OG.DELIVERY_DATE IS NOT NULL
				    ";
		// AND DATE_FORMAT(OG.ORDER_DATE,'%Y%m%d') = '$order_date'
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		$DELIVERY_NM = $record[0]["DCODE_NM"];
		$DELIVERY_CP = $record[0]["DELIVERY_CP"];
		$DELIVERY_NO = $record[0]["DELIVERY_NO"];

		if($DELIVERY_NM == "")
			$DELIVERY_NM = $DELIVERY_CP;

		$query = "INSERT INTO TBL_TEMP_ORDER_MRO_COMPLETE	
							(TEMP_NO, ORDER_DATE, ORDER_NO, SEQ, SELLER_GOODS_CODE, BOX_SEQ, BOX_QTY, SENDER, RECEIVER, DELIVERY_CODE, DELIVERY_NO, REG_ADM, REG_DATE)
	
				   VALUES   ('$temp_no', '$order_date', '$order_no', '$seq', '$seller_goods_code', '$box_seq', '$box_qty', '$sender', '$receiver', '$DELIVERY_NM', '$DELIVERY_NO', '$reg_adm', now());
				 ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}

	}

	function listTempOrderMROComplete($db, $temp_no) {

		$query = "SELECT ORDER_DATE, ORDER_NO, SEQ, SELLER_GOODS_CODE, BOX_SEQ, BOX_QTY, SENDER, RECEIVER, DELIVERY_CODE, DELIVERY_NO, ETC
				  FROM TBL_TEMP_ORDER_MRO_COMPLETE	
				  WHERE TEMP_NO = '$temp_no' ";

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

	/************ start  ???? ????  ***************/
	function listOrderDeliveryPaper($db, $order_goods_no, $individual_no) {

		$query = "SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, 
						 ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
						 DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, USE_TF, DEL_TF, REG_DATE, INDIVIDUAL_NO
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE 1 = 1 AND DEL_TF = 'N' AND ORDER_GOODS_NO = '$order_goods_no' ";
		
		if($individual_no <> "")
			$query .=" AND INDIVIDUAL_NO = '$individual_no' ";

			
		$query .=" ORDER BY REG_DATE DESC, SEQ_OF_DAY DESC ";

		echo $query;
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

	function getFirstOrderDeliveryPaper($db, $order_goods_no) {

		$query = "SELECT DELIVERY_CP, DELIVERY_NO
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND ORDER_GOODS_NO = '$order_goods_no'
					ORDER BY DELIVERY_SEQ 
					LIMIT 0 , 1";

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

	function getOrderGoodsDeliveryPaper($db, $order_goods_no) {

		$query = "SELECT DELIVERY_CP, DELIVERY_NO, DELIVERY_DATE
					FROM TBL_ORDER_GOODS
					WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND ORDER_GOODS_NO = '$order_goods_no'
					LIMIT 0 , 1";

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

	function countOrderDeliveryPaper($db, $order_goods_no, $individual_no) {

		$query = "SELECT COUNT(*)
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE 1 = 1 
					AND DEL_TF = 'N' "; 
					
		//2016-11-03 ?????? ???? ?????? ???????? ???? ?????? ?????? ???????????? ?????????? ???? ???????? ???????? ?????????? ?????????? ?????? ???? ???? AND USE_TF = 'Y'
					
					
		if($order_goods_no <> "") {
			$query .=" AND ORDER_GOODS_NO = '$order_goods_no'  ";
		}

		if($individual_no <> "") {
			$query .=" AND INDIVIDUAL_NO = '$individual_no'  ";
		}

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function insertOrderDeliveryPaper($db, $work_date, $reserve_no, $order_goods_no, $individual_no, $cp_no, $delivery_cnt, $seq_of_delivery, $r_mem_nm, $r_phone, $r_hphone, $r_addr1, $qty, $memo, $o_mem_nm, $order_phone, $order_manager_nm, $order_manager_phone, $payment_type, $send_cp_addr, $goods_delivery_name, $delivery_cp, $delivery_type, $delivery_fee, $delivery_fee_code, $s_adm_no) {

		$query = "SELECT MAX(SEQ_OF_DAY) + 1 AS SEQ_OF_DAY 
					FROM TBL_ORDER_GOODS_DELIVERY
				   WHERE DELIVERY_CP = '$delivery_cp'
					 AND LEFT(DELIVERY_SEQ, 10) = '$work_date' 
					 AND USE_TF = 'Y' 
					 AND DEL_TF = 'N'; ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$SEQ_OF_DAY  = $rows[0];
		if($SEQ_OF_DAY == "")
			$SEQ_OF_DAY = 1;

		$DELIVERY_SEQ = $work_date."-".str_pad($SEQ_OF_DAY,3, "0", STR_PAD_LEFT);

		$goods_delivery_name = str_replace("\'","",$goods_delivery_name);

		$query = "INSERT INTO TBL_ORDER_GOODS_DELIVERY	(RESERVE_NO, ORDER_GOODS_NO, INDIVIDUAL_NO, CP_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, 
														 DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, 
														 RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, 
														 ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
														 DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, REG_ADM, REG_DATE, USE_TF, DEL_TF)
				   
				   VALUES ('$reserve_no', '$order_goods_no', '$individual_no', '$cp_no', '$delivery_cnt', '$seq_of_delivery', 
						   '$DELIVERY_SEQ', '$SEQ_OF_DAY', '$r_mem_nm', '$r_phone', '$r_hphone', 
						   '$r_addr1', '$qty', '$memo', '$o_mem_nm', '$order_phone', 
						   '$order_manager_nm', '$order_manager_phone' , '$payment_type', '$send_cp_addr', '$goods_delivery_name', 
						   '$delivery_cp', '', '$delivery_type', '', '$delivery_fee', '$delivery_fee_code', '$s_adm_no', now(), 'Y', 'N' ); ";

		// echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
	}

	function appendOrderDeliveryPaper($db, $base_order_goods_delivery_no, $individual_no, $base_date, $s_adm_no) {

		$query = "SELECT DELIVERY_CP, LEFT(DELIVERY_SEQ, 10) AS DELIVERY_DAY FROM TBL_ORDER_GOODS_DELIVERY WHERE order_goods_delivery_no = '$base_order_goods_delivery_no' ; ";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			$record[0] = sql_result_array($result,0);
		}

		$delivery_cp = $record[0]["DELIVERY_CP"];

		$delivery_day = $record[0]["DELIVERY_DAY"];

		//2016-10-24 ?????????? ???????? ???????????? ????
		//2017-08-07 ???? ?????? ???? ???? ???????? ???????? ???????? ??
		if($delivery_day == "")
			$delivery_day = $base_date;

		$query = "SELECT MAX(SEQ_OF_DAY) + 1 AS SEQ_OF_DAY 
					FROM TBL_ORDER_GOODS_DELIVERY 
				   WHERE DELIVERY_CP = '$delivery_cp'
					 AND LEFT(DELIVERY_SEQ, 10) = '$delivery_day' AND DEL_TF = 'N' AND USE_TF = 'Y' ; ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$SEQ_OF_DAY  = $rows[0];

		$DELIVERY_SEQ = $delivery_day."-".str_pad($SEQ_OF_DAY,3, "0", STR_PAD_LEFT);

		$query = "INSERT INTO TBL_ORDER_GOODS_DELIVERY	(RESERVE_NO, ORDER_GOODS_NO, INDIVIDUAL_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, 
														 DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, 
														 RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, 
														 ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
														 DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, REG_ADM, REG_DATE, USE_TF, DEL_TF)
				   
				  SELECT RESERVE_NO, ORDER_GOODS_NO, '$individual_no', DELIVERY_CNT, SEQ_OF_DELIVERY + 1, 
														 '$DELIVERY_SEQ', '$SEQ_OF_DAY', CONCAT(RECEIVER_NM, ' + 1'), RECEIVER_PHONE, RECEIVER_HPHONE, 
														 RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, 
														 ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
														 DELIVERY_CP, '', DELIVERY_TYPE, '', DELIVERY_FEE, DELIVERY_FEE_CODE, '$s_adm_no', now(), 'Y', 'N'
					FROM TBL_ORDER_GOODS_DELIVERY 
					WHERE ORDER_GOODS_DELIVERY_NO = '$base_order_goods_delivery_no'; ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
	}

	function deleteOrderDeliveryPaper($db, $base_order_goods_delivery_no, $del_adm) {

		$query = " UPDATE TBL_ORDER_GOODS_DELIVERY 
					  SET DEL_TF = 'Y',
					   DEL_ADM = '$del_adm',
					   DEL_DATE = now()
					WHERE ORDER_GOODS_DELIVERY_NO = '$base_order_goods_delivery_no'; ";

		// echo $query."<br/>";
		// exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteEmptyOrderDeliveryPaperByOrderGoodsNo($db, $order_goods_no, $del_adm) {

		$query = " UPDATE TBL_ORDER_GOODS_DELIVERY 
					  SET DEL_TF = 'Y',
					   DEL_ADM = '$del_adm',
					   DEL_DATE = now()
					WHERE ORDER_GOODS_NO = '$order_goods_no' AND DELIVERY_NO = '' ;";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateOrderDeliveryPaper($db, $base_order_goods_delivery_no, $delivery_no, $delivery_fee_code, $delivery_fee, $use_tf, $s_adm_no) {
		
		$query = " UPDATE TBL_ORDER_GOODS_DELIVERY 
					  SET DELIVERY_NO  = '$delivery_no',
						  DELIVERY_FEE_CODE = '$delivery_fee_code',
						  DELIVERY_FEE = '$delivery_fee',
						  USE_TF	   = '$use_tf'
					WHERE ORDER_GOODS_DELIVERY_NO = '$base_order_goods_delivery_no'; ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
	}

	function selectOrderDeliveryPaper($db, $order_goods_delivery_no, $delivery_no) {

		$query = "SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, 
						 ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
						 DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, DELIVERY_CLAIM_CODE,  USE_TF
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE 1 = 1 ";
					
					
		if($order_goods_delivery_no <> "") {
			$query .=" AND ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no'  ";
		}

		if($delivery_no <> "") {
			$query .=" AND DELIVERY_NO = '$delivery_no' ";
		}

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

	function updateOrderGoodsDeliveryPaperAll($db, $order_goods_delivery_no, $delivery_seq, $delivery_cp, $delivery_no, $goods_delivery_name, $order_nm, $order_phone, $order_manager_nm, $order_manager_phone, $send_cp_addr,  $receiver_nm, $receiver_phone, $receiver_hphone, $receiver_addr, $delivery_fee_code, $delivery_claim_code, $memo, $chk_force_complete, $use_tf) {

		$delivery_fee =  getDcodeName($db, 'DELIVERY_FEE', $delivery_fee_code);

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
				  SET DELIVERY_SEQ			= '$delivery_seq',
					  DELIVERY_NO			= '$delivery_no',
					  GOODS_DELIVERY_NAME	= '$goods_delivery_name',
					  DELIVERY_FEE_CODE	    = '$delivery_fee_code',
					  DELIVERY_FEE	        = '$delivery_fee',
					  DELIVERY_CLAIM_CODE   = '$delivery_claim_code',
					  DELIVERY_CP           = '$delivery_cp',

					  ORDER_NM				= '$order_nm', 
					  ORDER_PHONE           = '$order_phone', 
					  ORDER_MANAGER_NM      = '$order_manager_nm', 
					  ORDER_MANAGER_PHONE   = '$order_manager_phone', 
					  SEND_CP_ADDR          = '$send_cp_addr',

					  RECEIVER_NM			= '$receiver_nm', 
					  RECEIVER_PHONE        = '$receiver_phone', 
					  RECEIVER_HPHONE       = '$receiver_hphone', 
					  RECEIVER_ADDR         = '$receiver_addr',
					  MEMO					= '$memo',

					  USE_TF		        = '$use_tf'
				  ";
		if($chk_force_complete == "on")
		{
			$query .= " , DELIVERY_DATE		= now() ";
		}

		$query .= " WHERE ORDER_GOODS_DELIVERY_NO		= '$order_goods_delivery_no' ";
		
		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	//order_read
	function cntOrderGoodsDelivery($db, $reserve_no, $order_goods_no, $individual_no) {

		$query = "SELECT DELIVERY_CP, COUNT(*) AS TOTAL, 
						 SUM(CASE WHEN USE_TF = 'Y' THEN 1 ELSE 0 END) AS CNT_YES, 
						 SUM(CASE WHEN USE_TF = 'N' THEN 1 ELSE 0 END) AS CNT_NO
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE RESERVE_NO = '$reserve_no' AND ORDER_GOODS_NO = '$order_goods_no' AND DEL_TF = 'N' ";
		
		if($individual_no <> "")
			$query .=" AND INDIVIDUAL_NO = '$individual_no' ";

		$query .="  GROUP BY DELIVERY_CP";

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

	function totalCntDeliveryPaper($db, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $deliveryNoTF, $search_field, $search_str, $order_field, $order_str){
	
		$query = "
					SELECT  COUNT(*) CNT
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE 1 = 1
				";

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND ORDER_MANAGER_NM = '".$cp_type."' ";
		} 

		if ($delivery_cp <> "") {
			$query .= " AND DELIVERY_CP = '".$delivery_cp."' ";
		} 

		if ($delivery_fee_code <> "") {
			$query .= " AND DELIVERY_FEE_CODE = '".$delivery_fee_code."' ";
		}

		if ($delivery_claim_code <> "") {
			$query .= " AND DELIVERY_CLAIM_CODE = '".$delivery_claim_code."' ";
		}

		if ($isSent == "Y") {
			$query .= " AND DELIVERY_DATE <> '0000-00-00' AND DELIVERY_DATE <> '' ";
		}

		if ($deliveryNoTF == "Y") {
			$query .= " AND DELIVERY_NO <> '' ";
		} else if ($deliveryNoTF == "N") {
			$query .= " AND DELIVERY_NO = '' ";
		}

		$query .= "	AND DELIVERY_SEQ <> ''
					AND USE_TF =  'Y'
					AND DEL_TF =  'N'
				  ";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_DELIVERY_NAME like '%".$search_str."%' OR RECEIVER_NM like '%".$search_str."%' OR ORDER_NM like '%".$search_str."%'  OR DELIVERY_NO like '%".$search_str."%' OR DELIVERY_SEQ like '%".$search_str."%' OR RECEIVER_ADDR like '%".$search_str."%')";
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND DELIVERY_NO = '".$search_str."'  ";
			} else if ($search_field == "DELIVERY_NO_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND DELIVERY_NO IN (".$search_str.") ";
			} else if ($search_field == "DELIVERY_SEQ") {
				$query .= " AND DELIVERY_SEQ = '".$search_str."' ";
			} else if ($search_field == "DELIVERY_SEQ_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND DELIVERY_SEQ IN (".$search_str.") ";
			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND ORDER_GOODS_NO IN (".$search_str.")  ";
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND RESERVE_NO IN (".$search_str.")  ";				
			} else {
				$query .= " AND (".$search_field." like '%".$search_str."%')";
			}
		}

		//echo $query."<br/><br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function listDeliveryPaper($db, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $deliveryNoTF, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount){
	
		$total_cnt = totalCntDeliveryPaper($db, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $deliveryNoTF, $search_field, $search_str, $order_field, $order_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "
					SELECT  @rownum:= @rownum - 1  as rn,  
					ORDER_GOODS_DELIVERY_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, DELIVERY_CLAIM_CODE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
					FROM TBL_ORDER_GOODS_DELIVERY 
					WHERE 1 = 1
				";
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND ORDER_MANAGER_NM = '".$cp_type."' ";
		} 

		if ($delivery_cp <> "") {
			$query .= " AND DELIVERY_CP = '".$delivery_cp."' ";
		} 

		if ($delivery_fee_code <> "") {
			$query .= " AND DELIVERY_FEE_CODE = '".$delivery_fee_code."' ";
		}

		if ($delivery_claim_code <> "") {
			$query .= " AND DELIVERY_CLAIM_CODE = '".$delivery_claim_code."' ";
		}

		if ($isSent == "Y") {
			$query .= " AND DELIVERY_DATE <> '0000-00-00' AND DELIVERY_DATE <> '' ";
		}

		if ($deliveryNoTF == "Y") {
			$query .= " AND DELIVERY_NO <> '' ";
		} else if ($deliveryNoTF == "N") {
			$query .= " AND DELIVERY_NO = '' ";
		}

		$query .= "	AND DELIVERY_SEQ <> ''
					AND USE_TF =  'Y'
					AND DEL_TF =  'N'
				  ";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_DELIVERY_NAME like '%".$search_str."%' OR RECEIVER_NM like '%".$search_str."%' OR ORDER_NM like '%".$search_str."%' OR DELIVERY_NO like '%".$search_str."%' OR DELIVERY_SEQ like '%".$search_str."%' OR RECEIVER_ADDR like '%".$search_str."%')";
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND DELIVERY_NO = '".$search_str."' ";
			} else if ($search_field == "DELIVERY_NO_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND DELIVERY_NO IN (".$search_str.") ";
			} else if ($search_field == "DELIVERY_SEQ") {
				$query .= " AND DELIVERY_SEQ = '".$search_str."' ";
			} else if ($search_field == "DELIVERY_SEQ_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND DELIVERY_SEQ IN (".$search_str.")  ";
			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND ORDER_GOODS_NO IN (".$search_str.")  ";
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND RESERVE_NO IN (".$search_str.")  ";
			} else {
				$query .= " AND (".$search_field." like '%".$search_str."%')";
			}
		}

		if ($order_field == "") 
			$order_field = "REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", ORDER_GOODS_DELIVERY_NO ".$order_str." limit ".$offset.", ".$nRowCount;

		// echo $query."<br/>";
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function TotalDeliveryFee($db, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $deliveryNoTF, $search_field, $search_str, $order_field, $order_str){
		$query = "
					SELECT B.DCODE AS FEE_NAME, B.DCODE_NM AS FEE, COUNT(*) AS FEE_CNT, B.DCODE_NM * COUNT(*) AS FEE_TOTAL
					FROM TBL_ORDER_GOODS_DELIVERY A 
					JOIN TBL_CODE_DETAIL B ON A.DELIVERY_FEE_CODE = B.DCODE

					WHERE 1 = 1
				";

		if ($start_date <> "") {
			$query .= " AND A.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND A.ORDER_MANAGER_NM = '".$cp_type."' ";
		} 

		if ($delivery_cp <> "") {
			$query .= " AND A.DELIVERY_CP = '".$delivery_cp."' ";
		} 

		if ($delivery_fee_code <> "") {
			$query .= " AND A.DELIVERY_FEE_CODE = '".$delivery_fee_code."' ";
		}

		if ($delivery_claim_code <> "") {
			$query .= " AND A.DELIVERY_CLAIM_CODE = '".$delivery_claim_code."' ";
		}

		if ($isSent == "Y") {
			$query .= " AND A.DELIVERY_DATE <> '0000-00-00' AND A.DELIVERY_DATE <> '' ";
		}

		if ($deliveryNoTF == "Y") {
			$query .= " AND A.DELIVERY_NO <> '' ";
		} else if ($deliveryNoTF == "N") {
			$query .= " AND A.DELIVERY_NO = '' ";
		}

		$query .= "	AND A.DELIVERY_SEQ <> ''
					AND A.USE_TF =  'Y'
					AND A.DEL_TF =  'N'
				  ";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (A.GOODS_DELIVERY_NAME like '%".$search_str."%' OR A.RECEIVER_NM like '%".$search_str."%' OR A.ORDER_NM like '%".$search_str."%' OR A.DELIVERY_NO like '%".$search_str."%' OR A.DELIVERY_SEQ like '%".$search_str."%' OR A.RECEIVER_ADDR like '%".$search_str."%')";
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND A.DELIVERY_NO = '".$search_str."' ";
			} else if ($search_field == "DELIVERY_NO_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND A.DELIVERY_NO IN (".$search_str.") ";
			} else if ($search_field == "DELIVERY_SEQ") {
				$query .= " AND A.DELIVERY_SEQ = '".$search_str."' ";
			} else if ($search_field == "DELIVERY_SEQ_MULTI") {
				$search_str = "'".str_replace(" ","','", $search_str)."'";
				$query .= " AND A.DELIVERY_SEQ IN (".$search_str.") ";
								
			} else {
				$query .= " AND (".$search_field." like '%".$search_str."%')";
			}
		}
		
		$query.= "
					GROUP BY A.DELIVERY_FEE
				 ";

		//echo $query."<br/>";
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

	// ???????? (?????????? ????) -> ?????? ???????? ???????? 
	function listDeliveryPaperLoadingExcel($db, $specific_date, $delivery_cp, $delivery_fee) {

		$query = "SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_NO, DELIVERY_SEQ, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, GOODS_DELIVERY_NAME, ORDER_QTY, MEMO, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, ORDER_NM, ORDER_PHONE, DELIVERY_FEE, DELIVERY_FEE_CODE, PAYMENT_TYPE, SEND_CP_ADDR
					
					FROM TBL_ORDER_GOODS_DELIVERY 
					WHERE 1 = 1 ";
		
		if($specific_date != "")
			$query .= " AND DELIVERY_SEQ like '$specific_date%' ";
		
		$query .=	  " AND USE_TF =  'Y'
						AND DEL_TF =  'N'
						AND DELIVERY_NO = ''
						AND DELIVERY_CP = '$delivery_cp'
						";

		if($delivery_fee != "")
			$query .= " AND DELIVERY_FEE = '$delivery_fee' ";

		$query .= " ORDER BY LEFT(DELIVERY_SEQ, 10), SEQ_OF_DAY ";

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}



	function getWorkedOrderGoodsNo($db, $work_date, $order_field) { 

		$query = "SELECT OG.ORDER_GOODS_NO, 
						 OG.RESERVE_NO, 
  						 CASE WHEN OG.DELIVERY_TYPE =3
							THEN OGI.INDIVIDUAL_NO
							ELSE  ''
						 END AS INDIVIDUAL_NO,
						 OG.DELIVERY_TYPE, 
						 OG.DELIVERY_CP, 
						 OG.SENDER_NM, 
						 OG.SENDER_PHONE

					FROM TBL_ORDER_GOODS OG
					LEFT JOIN TBL_ORDER_GOODS_INDIVIDUAL OGI ON OG.ORDER_GOODS_NO = OGI.ORDER_GOODS_NO
					WHERE 
						OG.WORK_START_DATE =  '$work_date'
					AND OG.ORDER_STATE = '2'
					AND (OG.DELIVERY_TYPE = 0 OR OG.DELIVERY_TYPE = 3)
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					ORDER BY OG.WORK_SEQ ASC
					";

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

	function getOrderGoodsByOrderGoodsNos($db, $arr_order_goods_no) { 

		$str_order_goods_no = "";
		if(sizeof($arr_order_goods_no) > 0) { 
			for($i = 0; $i < sizeof($arr_order_goods_no); $i++) {
				if($arr_order_goods_no[$i] <> "")
					$str_order_goods_no .= $arr_order_goods_no[$i].", ";
			}
			
			$str_order_goods_no = rtrim($str_order_goods_no, ", ");
		}

		//echo $str_order_goods_no."<br/>";

		$query = "SELECT OG.ORDER_GOODS_NO, 
						 OG.RESERVE_NO, 
  						 CASE WHEN OG.DELIVERY_TYPE =3
							THEN OGI.INDIVIDUAL_NO
							ELSE  ''
						 END AS INDIVIDUAL_NO,
						 OG.DELIVERY_TYPE, 
						 OG.DELIVERY_CP, 
						 OG.SENDER_NM, 
						 OG.SENDER_PHONE

					FROM TBL_ORDER_GOODS OG
					LEFT JOIN TBL_ORDER_GOODS_INDIVIDUAL OGI ON OG.ORDER_GOODS_NO = OGI.ORDER_GOODS_NO
					WHERE 
						OG.ORDER_GOODS_NO IN (".$str_order_goods_no.")
					AND OG.ORDER_STATE = '2'
					AND (OG.DELIVERY_TYPE = 0 OR OG.DELIVERY_TYPE = 3)
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					";

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
	/************ end  ???? ????  ***************/



	/************ start ????????  ***************/

	function cntDeliveryIndividual($db, $order_goods_no) {

		$query = "SELECT COUNT(*) AS CNT_DELIVERY_PLACE, 
		                 SUM(SUB_QTY) AS TOTAL_GOODS_DELIVERY_QTY, 
						 SUM(IF(IS_DELIVERED = 'Y', SUB_QTY, 0)) AS TOTAL_DELIVERED_QTY 
					
					FROM TBL_ORDER_GOODS_INDIVIDUAL 
					WHERE DEL_TF =  'N'
					  AND ORDER_GOODS_NO = '$order_goods_no'
						";

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

	//???????? ??????
	function listDeliveryIndividual($db, $order_goods_no, $direction) {

		$query = "SELECT INDIVIDUAL_NO, ORDER_GOODS_NO, R_MEM_NM, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1, GOODS_DELIVERY_NAME, SUB_QTY, MEMO, DELIVERY_TYPE, IS_DELIVERED, DELIVERY_DATE, REG_DATE, REG_ADM, USE_TF
					
					FROM TBL_ORDER_GOODS_INDIVIDUAL 
					WHERE 1 = 1 
						AND DEL_TF =  'N'
						AND ORDER_GOODS_NO = '$order_goods_no'
						";

		if($direction != "")
			$query .= " ORDER BY REG_DATE ".$direction.", INDIVIDUAL_NO ".$direction;
		else
			$query .= " ORDER BY REG_DATE DESC, INDIVIDUAL_NO DESC ";

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

	function insertDeliveryIndividual($db, $temp_no, $order_goods_no, $r_mem_nm, $r_phone, $r_hphone, $r_zipcode, $r_addr1, $goods_delivery_name, $sub_qty, $memo, $delivery_type, $reg_adm) {
		
		$query = "INSERT INTO TBL_ORDER_GOODS_INDIVIDUAL 
							(TEMP_NO, ORDER_GOODS_NO, R_MEM_NM, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1, GOODS_DELIVERY_NAME, SUB_QTY, MEMO, DELIVERY_TYPE, DEL_TF, REG_ADM, REG_DATE) 
					   VALUES ('$temp_no', '$order_goods_no', '$r_mem_nm', '$r_phone', '$r_hphone', '$r_zipcode', '$r_addr1', '$goods_delivery_name', '$sub_qty', '$memo', '$delivery_type', 'N', '$reg_adm', now()); ";
		
		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return "";
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else 
			return 0;
	}

	//???????? ?????? ?? ???? ??????????
	function selectDeliveryIndividual($db, $individual_no) {

		$query = "SELECT INDIVIDUAL_NO, ORDER_GOODS_NO, R_MEM_NM, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1, SUB_QTY, MEMO, GOODS_DELIVERY_NAME, DELIVERY_TYPE, IS_DELIVERED, USE_TF
					
					FROM TBL_ORDER_GOODS_INDIVIDUAL 
					WHERE 1 = 1 
						AND DEL_TF =  'N'
						AND INDIVIDUAL_NO = '$individual_no'  ";

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

	function completeDeliveryIndividual($db, $individual_no) { 

		$query="UPDATE TBL_ORDER_GOODS_INDIVIDUAL 
				   SET IS_DELIVERED = 'Y', DELIVERY_DATE = now()
				 WHERE INDIVIDUAL_NO = '$individual_no' AND IS_DELIVERED = 'N' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function cancelDeliveryIndividual($db, $individual_no) { 

		$query="UPDATE TBL_ORDER_GOODS_INDIVIDUAL 
				   SET IS_DELIVERED = 'N', DELIVERY_DATE = ''
				 WHERE INDIVIDUAL_NO = '$individual_no' AND IS_DELIVERED = 'Y' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteDeliveryIndividual($db, $individual_no) { 

		$query="UPDATE TBL_ORDER_GOODS_INDIVIDUAL 
				   SET DEL_TF = 'Y' 
				 WHERE INDIVIDUAL_NO = '$individual_no' AND NOT EXISTS (SELECT * FROM TBL_ORDER_GOODS_DELIVERY WHERE INDIVIDUAL_NO = '$individual_no' AND USE_TF = 'Y' AND DEL_TF = 'N' ) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	// ?????????? ??????/???????????? ????
	function updateDeliveryIndividualUseTF($db, $individual_no, $use_tf) { 

		$query="UPDATE TBL_ORDER_GOODS_INDIVIDUAL 
				   SET USE_TF = '$use_tf' 
				 WHERE INDIVIDUAL_NO = '$individual_no' ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	/************ end ????????  ***************/


	/*********** start  ??????  *****************/

	//mysql_insert_id(); ???????? ??
	function getLastInsertedID($db) {

		$query ="SELECT LAST_INSERT_ID()";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	// 2017-11-30 ???????? ???????? ?????? ?????? ???????? ????????????, ?????????? ???????? ???? ?????? ???? ?? ?? ???? ??????
	function updateOrderGoodsGroupNo($db, $prev_order_goods_no, $claim_order_goods_no, $cancel_qty) { 

		//echo "func_updateOrderGoodsGroupNo()????<br>";

		$query="UPDATE TBL_ORDER_GOODS
				   SET 
				   	GROUP_NO = $prev_order_goods_no, 
					REFUNDABLE_QTY = REFUNDABLE_QTY - $cancel_qty
				 WHERE ORDER_GOODS_NO = $prev_order_goods_no   ; ";
		
		mysql_query($query,$db);
 
		//echo $query;

		$query="UPDATE TBL_ORDER_GOODS
				   SET GROUP_NO = $prev_order_goods_no
				 WHERE ORDER_GOODS_NO = $claim_order_goods_no; ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	// 2017-11-30 ???????? ???????? ?????????? ?????? ???? - ???????? ??????
	function updateOrderGoodsClaimNo($db, $prev_order_goods_no, $claim_order_goods_no) { 

		$query="UPDATE TBL_ORDER_GOODS
				   SET CLAIM_ORDER_GOODS_NO = $prev_order_goods_no
				 WHERE ORDER_GOODS_NO = $claim_order_goods_no; ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	// ?????????? - ??(DELIVERY_TYPE=2)?? ?????? -- 20160717 ??????(????????)???? -- 20160902 ???????? ????
	function listOrderGoodsDeliveryConfirmation($db, $reserve_no, $print_type) {

		$query = "SELECT ORDER_GOODS_NO, GOODS_NAME, GOODS_SUB_NAME
					FROM TBL_ORDER_GOODS 
				   WHERE USE_TF= 'Y' 
					 AND DEL_TF = 'N' 
					 AND DELIVERY_TYPE = '$print_type' 
					 AND ORDER_STATE IN ('2')
								  ";
		
		$query .= " AND RESERVE_NO = '".$reserve_no."' ";

		$query .= " ORDER BY ORDER_GOODS_NO DESC ";

		//echo $query."<br/>";
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

	//???????? ??????????
	function selectOrderGoodsDeliveryConfirmation($db, $individual_no, $print_type) {

		$query = "SELECT R_ZIPCODE, R_ADDR1, R_MEM_NM, R_PHONE, R_HPHONE, GOODS_DELIVERY_NAME, SUB_QTY, MEMO

								FROM TBL_ORDER_GOODS_INDIVIDUAL 
							 WHERE DEL_TF = 'N' 
								 AND DELIVERY_TYPE = '$print_type' 
								  ";
		
		$query .= " AND INDIVIDUAL_NO = '".$individual_no."' ";

		//echo $query."<br/>";
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


	/*********** end  ??????     ****************/


	// ?????????????? ???? ?????? ???????? ???????????? ???? -> 2016-12-01 ???????????? ????
	function listKyungbakLoading($db, $start_date, $end_date) { 

		$query = " (SELECT DATE_FORMAT(OG.DELIVERY_DATE, '%Y-%m-%d') AS DELIVERY_DATE, C.CP_CODE, CONCAT( C.CP_NM,  ' ', C.CP_NM2 ) AS CP_NAME, C.IS_MALL, G.GOODS_CODE, OG.GOODS_NAME, OG.QTY, OG.SALE_PRICE, OG.CATE_01, OG.CATE_04, O.O_MEM_NM, OG.SA_DELIVERY_PRICE, OG.DISCOUNT_PRICE, OG.ORDER_STATE, OG.DELIVERY_TYPE, OG.ORDER_GOODS_NO, OG.OPT_MEMO
					FROM TBL_ORDER O
					JOIN TBL_COMPANY C
					JOIN TBL_ORDER_GOODS OG
					JOIN TBL_GOODS G
				   WHERE O.RESERVE_NO = OG.RESERVE_NO
					 AND O.CP_NO = C.CP_NO
					 AND OG.GOODS_NO = G.GOODS_NO
					 AND O.TOTAL_QTY <> 0 ";


		if ($start_date <> "") {
			$query .= " AND OG.DELIVERY_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
		}

		//2016-11-28 ????????, ???????? (???? - ?????? ???????????? ?????? ??????)
		$query .= "	
				 AND OG.ORDER_STATE IN (3)  
				 AND OG.DELIVERY_TYPE <> '3'
				 AND O.DEL_TF =  'N'
				 AND O.USE_TF =  'Y'
				 AND OG.DEL_TF =  'N'
				 AND OG.USE_TF =  'Y' )

				 UNION ALL

				 (SELECT DATE_FORMAT(OG.REG_DATE, '%Y-%m-%d') AS REG_DATE, C.CP_CODE, CONCAT( C.CP_NM,  ' ', C.CP_NM2 ) AS CP_NAME, C.IS_MALL, G.GOODS_CODE, OG.GOODS_NAME, -1 * OG.QTY AS QTY, OG.SALE_PRICE, OG.CATE_01, OG.CATE_04, O.O_MEM_NM, OG.SA_DELIVERY_PRICE, OG.DISCOUNT_PRICE, OG.ORDER_STATE, OG.DELIVERY_TYPE,  0 AS ORDER_GOODS_NO, OG.OPT_MEMO
					FROM TBL_ORDER O
					JOIN TBL_COMPANY C
					JOIN TBL_ORDER_GOODS OG
					JOIN TBL_GOODS G
					WHERE O.RESERVE_NO = OG.RESERVE_NO
					  AND O.CP_NO = C.CP_NO
					  AND OG.GOODS_NO = G.GOODS_NO
					  AND O.TOTAL_QTY <> 0 
					";
		//???????? ???? - 2016-12-28
		
		if ($start_date <> "") {
			$query .= " AND OG.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
		}
				 
		$query .= "			
		
					AND OG.ORDER_STATE = '7'
					AND O.DEL_TF =  'N'
					AND O.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
				 
				 )
				
				 UNION ALL
				 
				 (SELECT DATE_FORMAT(OGI.DELIVERY_DATE, '%Y-%m-%d') AS DELIVERY_DATE, C.CP_CODE, CONCAT( C.CP_NM,  ' ', C.CP_NM2 ) AS CP_NAME, C.IS_MALL, G.GOODS_CODE, OG.GOODS_NAME, SUM(OGI.SUB_QTY) AS QTY, OG.SALE_PRICE, OG.CATE_01, OG.CATE_04, O.O_MEM_NM, OG.SA_DELIVERY_PRICE, OG.DISCOUNT_PRICE, OG.ORDER_STATE, OG.DELIVERY_TYPE,  0 AS ORDER_GOODS_NO, OG.OPT_MEMO
					FROM TBL_ORDER O
					JOIN TBL_COMPANY C
					JOIN TBL_ORDER_GOODS OG
					JOIN TBL_ORDER_GOODS_INDIVIDUAL OGI
					JOIN TBL_GOODS G
					WHERE O.RESERVE_NO = OG.RESERVE_NO
					  AND O.CP_NO = C.CP_NO
					  AND OG.GOODS_NO = G.GOODS_NO
					  AND OG.ORDER_GOODS_NO = OGI.ORDER_GOODS_NO
					  AND O.TOTAL_QTY <> 0 
					
					";
		//?????????? GROUP?? - 2016-10-26
		
		if ($start_date <> "") {
			$query .= " AND OGI.DELIVERY_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OGI.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
		}

				 
		$query .= "	
					
					AND O.DEL_TF =  'N'
					AND O.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OGI.DEL_TF =  'N'
					
					 GROUP BY DATE_FORMAT(OGI.DELIVERY_DATE, '%Y-%m-%d'), C.CP_CODE, CONCAT( C.CP_NM,  ' ', C.CP_NM2 ), C.IS_MALL, G.GOODS_CODE, OG.GOODS_NAME, OG.SALE_PRICE, OG.CATE_01, O.O_MEM_NM, OG.SA_DELIVERY_PRICE, OG.DISCOUNT_PRICE, OG.ORDER_STATE, OG.DELIVERY_TYPE, OG.ORDER_GOODS_NO, DATE_FORMAT(OGI.REG_DATE, '%Y-%m-%d')
				
					
					) ";

		$query .= " 	ORDER BY DELIVERY_DATE DESC ";

		//AND OG.DELIVERY_TYPE = '3'
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


	function updateDeliveryInfo($db, $delivery_cp, $delivery_no, $order_goods_no) {
		
		$query = "UPDATE TBL_ORDER_GOODS SET 
												DELIVERY_CP		= '$delivery_cp', 
												DELIVERY_NO		= '$delivery_no'

									   WHERE    ORDER_GOODS_NO = '$order_goods_no'";
		

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	/*
	function chkOrderGoodsOrderState($db, $order_goods_no) {
	
		$query = "SELECT ORDER_STATE FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO	= '$order_goods_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		//echo $query;
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		return $rows[0];
	}
	*/

	function selectOrderGoodsForDeliveryList($db, $order_goods_no) {

		$query = "SELECT C.RESERVE_NO, C.GROUP_NO, C.GOODS_NO, G.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, C.CATE_01, C.CATE_02, C.CATE_03, 
						 C.BUY_PRICE, C.SALE_PRICE, C.DISCOUNT_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.ORDER_DATE, 
						 C.DELIVERY_TYPE, C.WORK_FLAG, C.WORK_END_DATE, C.ORDER_STATE, C.WORK_QTY, C.TAX_TF,
						 C.SALE_CONFIRM_TF

						FROM TBL_ORDER_GOODS C JOIN TBL_GOODS G ON C.GOODS_NO = G.GOODS_NO
						WHERE C.ORDER_GOODS_NO = '$order_goods_no' AND C.USE_TF = 'Y' AND C.DEL_TF= 'N' ";

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

	// ?????????? - ????
	function totalCntManagerDeliveryPackage($db, $start_date, $end_date, $order_state, $buy_cp_no, $sel_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(DISTINCT G.RESERVE_NO) CNT, COUNT(G.RESERVE_NO) CNT2
				   FROM TBL_ORDER O JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO 
				  WHERE 1 = 1 AND O.IS_PACKAGE = 'Y' AND G.IS_PACKAGE = 'Y' ";
		//AND O.TOTAL_QTY <> 0

		if ($start_date <> "") {
			$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.RESERVE_NO like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%'  )"; 
			} else if ($search_field == "SUB_GOODS_NAME") {
				$query .= " AND G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS LEFT JOIN TBL_GOODS GGG ON GS.GOODS_SUB_NO = GGG.GOODS_NO WHERE GGG.GOODS_NAME LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "SUB_GOODS_CODE") {
				$query .= " AND G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS LEFT JOIN TBL_GOODS GGG ON GS.GOODS_SUB_NO = GGG.GOODS_NO WHERE GGG.GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "MART_GOODS_CODE") {
				$query .= " AND G.GOODS_NO IN (SELECT GGG.GOODS_NO FROM TBL_GOODS GGG WHERE GGG.SELLER_GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "O.O_HPHONE") {
				$query .= " AND O.O_HPHONE LIKE '%".$search_str."%'  ";
			} else if ($search_field == "O.R_HPHONE") {
				$query .= " AND O.R_HPHONE LIKE '%".$search_str."%'  ";
			} else if ($search_field == "O.R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%'  ";
			} else if ($search_field == "GOODS_OPTION_NM_02") {
				$query .= " AND G.GOODS_OPTION_NM_02 LIKE '%".$search_str."%'  ";
			} else if ($search_field == "CP_ORDER_NO_MULTI") {
				$search_str = "'".str_replace("  ","','", $search_str)."'";
				$query .= " AND G.CP_ORDER_NO IN (".$search_str.")  ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$row   = mysql_fetch_array($result);
		return $row;
	}

	//?????????? - ????
	function listManagerDeliveryPackage($db, $start_date, $end_date, $order_state, $buy_cp_no, $sel_cp_no, $pay_type, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

/*
,
										 (O.TOTAL_SALE_PRICE + O.TOTAL_EXTRA_PRICE) AS TOTAL_PRICE, 
										 ((O.TOTAL_SALE_PRICE) - (O.TOTAL_BUY_PRICE + O.TOTAL_DELIVERY_PRICE)) AS TOTAL_PLUS_PRICE, 
										 (SELECT MAX(PAY_DATE) FROM TBL_ORDER_GOODS GG WHERE GG.RESERVE_NO = O.RESERVE_NO) AS G_REG_DATE
*/
		$query = "SELECT DISTINCT O.ORDER_NO, O.RESERVE_NO, O.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, O.ORDER_STATE, O.TOTAL_BUY_PRICE, O.TOTAL_SALE_PRICE, O.TOTAL_EXTRA_PRICE, O.TOTAL_DELIVERY_PRICE, O.TOTAL_QTY,
										 O.ORDER_DATE, O.PAY_DATE, O.PAY_TYPE, O.DELIVERY_TYPE, O.DELIVERY_DATE, O.FINISH_DATE, O.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE
								FROM TBL_ORDER O JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO WHERE 1=1 AND O.IS_PACKAGE = 'Y' AND G.IS_PACKAGE = 'Y'  ";

								//AND O.TOTAL_QTY <> 0

		if ($start_date <> "") {
			$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($pay_type <> "") {
			$query .= " AND O.PAY_TYPE = '".$pay_type."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.RESERVE_NO like '%".$search_str."%' OR O.O_MEM_NM like '%".$search_str."%' OR O.R_MEM_NM like '%".$search_str."%'  )"; 
			} else if ($search_field == "SUB_GOODS_NAME") {
				$query .= " AND G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS LEFT JOIN TBL_GOODS GGG ON GS.GOODS_SUB_NO = GGG.GOODS_NO WHERE GGG.GOODS_NAME LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "SUB_GOODS_CODE") {
				$query .= " AND G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS LEFT JOIN TBL_GOODS GGG ON GS.GOODS_SUB_NO = GGG.GOODS_NO WHERE GGG.GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "MART_GOODS_CODE") {
				$query .= " AND G.GOODS_NO IN (SELECT GGG.GOODS_NO FROM TBL_GOODS GGG WHERE GGG.SELLER_GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "GOODS_OPTION_NM_02") {
				$query .= " AND G.GOODS_OPTION_NM_02 LIKE '%".$search_str."%'  ";
			} else if ($search_field == "O.O_HPHONE") {
				$query .= " AND O.O_HPHONE LIKE '%".$search_str."%'  ";
			} else if ($search_field == "O.R_HPHONE") {
				$query .= " AND O.R_HPHONE LIKE '%".$search_str."%'  ";
			} else if ($search_field == "O.R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%'  ";
			} else if ($search_field == "CP_ORDER_NO_MULTI") {
				$search_str = "'".str_replace("  ","','", $search_str)."'";
				$query .= " AND G.CP_ORDER_NO IN (".$search_str.")  ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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


	//?????????? - ????
	function listOrderDeliveryPackage($db, $order_goods_no_total) {

		// 
		$query = "SELECT DISTINCT OGD.ORDER_GOODS_DELIVERY_NO, 
						OGD.DELIVERY_SEQ, OGD.DELIVERY_CP, OGD.DELIVERY_NO, OGD.RECEIVER_PHONE, OGD.RECEIVER_HPHONE, OGD.GOODS_DELIVERY_NAME,  OGD.DELIVERY_FEE, OGD.DELIVERY_DATE, OGD.MEMO,  OGD.OUTSTOCK_TF, OGD.ORDER_QTY, OGD.USE_TF
					FROM TBL_ORDER_GOODS_DELIVERY OGD
					JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OGOGD.ORDER_GOODS_DELIVERY_NO = OGD.ORDER_GOODS_DELIVERY_NO 
					
					WHERE OGOGD.ORDER_GOODS_NO IN ($order_goods_no_total) 
					  AND OGD.DEL_TF = 'N' 

					ORDER BY OGD.ORDER_GOODS_DELIVERY_NO";

		//echo $query."<br/>";
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

	//?????????? - ????????
	function deleteOrderPackage($db, $reserve_no, $del_adm) {

		$query="UPDATE TBL_PAYMENT SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);

		$query="UPDATE TBL_ORDER SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);
		
		$query="UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now() WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);

		$query = "DELETE FROM TBL_TEMP_ORDER_GOODS_STOCK WHERE RESERVE_NO = '$reserve_no' ";

		mysql_query($query,$db);

		$query="UPDATE TBL_ORDER_GOODS_DELIVERY OGD
				JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OGD.ORDER_GOODS_DELIVERY_NO = OGOGD.ORDER_GOODS_DELIVERY_NO
				JOIN TBL_ORDER_GOODS OG ON OG.ORDER_GOODS_NO = OGOGD.ORDER_GOODS_NO
				SET OGD.DEL_TF = 'Y', OGD.DEL_ADM = '$del_adm', OGD.DEL_DATE = now() 
				WHERE OG.RESERVE_NO = '$reserve_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectCompanyNoByOrderGoodsNo($db, $order_goods_no) { 

		$query = " SELECT CP_NO 
					 FROM TBL_ORDER O 
					 JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					WHERE OG.ORDER_GOODS_NO = $order_goods_no ; 
				 ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function selectCompanyByReserveNo($db, $reserve_no) {

		$query = "SELECT C.CP_CATE, C.CP_TYPE, C.CP_NM, C.CP_NM2
				    FROM TBL_COMPANY C
					JOIN TBL_ORDER O ON C.CP_NO = O.CP_NO
				   WHERE O.RESERVE_NO = '$reserve_no' ";
		
		//, CP_CODE, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP, RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM, PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT, ACCOUNT_OWNER_NM, MEMO, IS_MALL, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

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

	
	// ????????????,???? -> ????,????????,??,?????? ?????? ???? ?????????? ???????? ?? ?????????? ???? 
	function updateWorksFlagNOrderGoods($db, $order_goods_no) {
		$query = "UPDATE TBL_ORDER_GOODS 
					 SET WORK_FLAG = 'N', WORK_END_DATE = '0000-00-00 00:00:00' 
				   WHERE ORDER_GOODS_NO = '".$order_goods_no."' AND WORK_FLAG = 'Y' AND WORK_END_DATE <> '0000-00-00 00:00:00' ";
		
		// ???? ???????? ?????? ???????? ?? ???? ?????? ???? 
		// AND WORK_SEQ = 0 AND WORK_QTY = 0 AND WORK_START_DATE = '0000-00-00 00:00:00'
		
		//echo $query."<br/>";
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	//???????? ????
	function selectNextOrderGoodsSeq($db, $reserve_no) { 

		$query = " SELECT ORDER_SEQ 
					 FROM TBL_ORDER_GOODS 
					WHERE RESERVE_NO = '$reserve_no' ; 
				 ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}


	// ???? ???? ???? 
	function selectOrderWorkInfo($db, $order_goods_no) {

		$query = "SELECT WORK_SEQ, WORK_FLAG, WORK_START_DATE, WORK_END_DATE, WORK_QTY, WORK_MSG
					FROM TBL_ORDER_GOODS
				   WHERE ORDER_GOODS_NO = '$order_goods_no' AND USE_TF = 'Y' AND DEL_TF = 'N'  ";


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

	//???????? ???? ???? ????
	function insertOrderDeliveryPaperOutside($db, $order_goods_no, $delivery_cp, $delivery_no, $memo) { 

		$query = "SELECT COUNT(*)
					FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE
				   WHERE ORDER_GOODS_NO = '$order_goods_no' 
				     AND DELIVERY_CP = '$delivery_cp' 
					 AND DELIVERY_NO = '$delivery_no' 
					 AND DEL_TF = 'N'  ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];

		if($record > 0) {
			$query = "  UPDATE TBL_ORDER_GOODS_DELIVERY_OUTSIDE 
						   SET MEMO = '$memo'
						 WHERE ORDER_GOODS_NO = '$order_goods_no' 
						   AND DELIVERY_CP = '$delivery_cp' 
						   AND DELIVERY_NO = '$delivery_no' 
					 ";
		} else { 

			$query = "  INSERT INTO TBL_ORDER_GOODS_DELIVERY_OUTSIDE (ORDER_GOODS_NO, DELIVERY_CP, DELIVERY_NO, MEMO, REG_DATE)
						     VALUES ('".$order_goods_no."', '".$delivery_cp."', '".$delivery_no."', '".$memo."', now())";

		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	//???????? ???? ???? ??????
	function listOrderDeliveryPaperOutside($db, $order_goods_no) {

		$query = "SELECT DELIVERY_CP, DELIVERY_NO, MEMO, REG_DATE
					FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE
				   WHERE ORDER_GOODS_NO = '$order_goods_no' AND DEL_TF = 'N'
				   ORDER BY REG_DATE DESC ";

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

	function deleteOrderDeliveryPaperOutside($db, $order_goods_no, $delivery_no) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY_OUTSIDE
					 SET DEL_TF = 'Y' 
				   WHERE ORDER_GOODS_NO = '$order_goods_no' 
				     AND DELIVERY_NO = '$delivery_no' 
					 AND DEL_TF = 'N'  ";


		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	// ?????????????? ?????????? ???? ???? ??????
	function getFirstOrderDeliveryPaperOutside($db, $order_goods_no) {

		$query = "SELECT DELIVERY_CP, DELIVERY_NO
					FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE
					WHERE DEL_TF = 'N' AND ORDER_GOODS_NO = '$order_goods_no' AND DELIVERY_CP <> '' AND DELIVERY_NO <> ''
					ORDER BY DELIVERY_CP, DELIVERY_NO 
					LIMIT 0 , 1";

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

	function insertTempOrderEzwell($db, $temp_no, $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $O, $P, $Q, $R, $S, $T, $U, $V, $W, $X, $Y, $Z, $AA, $AB, $AC, $AD, $reg_adm) { 

		$query="INSERT INTO TBL_TEMP_ORDER_EZWELL 
						(TEMP_NO, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, AA, AB, AC, AD, REG_ADM, REG_DATE) 
					 VALUES 
						('$temp_no', '$A', '$B', '$C', '$D', '$E', '$F', '$G', '$H', '$I', '$J', '$K', '$L', '$M', '$N', '$O', '$P', '$Q', '$R', '$S', '$T', '$U', '$V', '$W', '$X', '$Y', '$Z', '$AA', '$AB', '$AC', '$AD', '$reg_adm', now()); ";
		
		//echo $query."<br>";
		//exit;
  	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//???? ????????
	function getMemoFromOrderGoods($db, $order_goods_no) { 

		$query = " SELECT 
						CASE WHEN C.IS_MALL =  'Y'
						THEN O.O_MEM_NM
						ELSE ''
						END AS A, 
						
						OG.CATE_01 AS B, 
						
						CASE WHEN OG.CATE_04 <>  ''
						THEN  '????'
						ELSE  ''
						END AS C, 
						
						CASE WHEN OG.ORDER_STATE =  '7'
						THEN  '????'
						ELSE  ''
						END AS D
						
					FROM TBL_ORDER O
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					WHERE OG.ORDER_GOODS_NO = '$order_goods_no' ";
		
		//echo $query."<br/>";
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

	//???????? ????
	function deleteOrderGoods($db, $order_goods_no, $del_adm) {

		$query="UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_ADM = '".$del_adm."', DEL_DATE = now() WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//???????? ?????? ????????
	function getOrderGoodsRegDate($db, $order_goods_no) { 

		$query = " SELECT REG_DATE 
					 FROM TBL_ORDER_GOODS 
					WHERE ORDER_GOODS_NO = '$order_goods_no' ; 
				 ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}


	function getOrderGoodsTaxTF($db, $order_goods_no) { 
	
		$query = " SELECT TAX_TF
					 FROM TBL_ORDER_GOODS
					WHERE ORDER_GOODS_NO =  '$order_goods_no' ; 
				 ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	//MRO ?????? ?????? ???? ?????? ???????? ????????
	function selectStickerSize($db, $category, $goods_code, $sticker_type) { 

		//?? ?????? ?????????? ?????? ?????? ????
		$query="SELECT IFNULL(G.GOODS_CODE, '')
				  FROM TBL_GOODS G
				  JOIN TBL_GOODS_EXTRA E ON G.GOODS_NO = E.GOODS_NO
				 WHERE E.DCODE IN (

											SELECT E.DCODE
											  FROM TBL_GOODS G 
											  JOIN TBL_GOODS_EXTRA E ON G.GOODS_NO = E.GOODS_NO
											 WHERE G.GOODS_CODE = '$goods_code' 
											 AND E.PCODE = 'GOODS_STICKER_SIZE'
					                      )
				   AND E.PCODE = 'GOODS_STICKER_SIZE'
				   AND G.GOODS_NAME LIKE  '%$sticker_type%' 
				   AND G.GOODS_CATE LIKE '$category%' ";

		// echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		if($record <> '')
			return $record;
		else
		{
			//???????? ???? ?????? ?? ???????? ????
			$query = " SELECT G.GOODS_CODE
						 FROM TBL_GOODS G
						 JOIN TBL_GOODS_EXTRA E ON G.GOODS_NO = E.GOODS_NO
						WHERE E.DCODE = '??'
						  AND E.PCODE = 'GOODS_STICKER_SIZE'
						  AND G.GOODS_NAME LIKE  '%$sticker_type%' 
						  AND G.GOODS_CATE LIKE '$category%' ";

			//echo $query."<br/>";

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;
		}

	}

	function chkGoodsExtraWrap($db, $goods_no) { 

		$query = " SELECT DCODE
		             FROM TBL_GOODS_EXTRA 
					WHERE PCODE = 'WRAP_CODE' AND GOODS_NO = '$goods_no' 
    			  ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function undoOrderStateFromComplete($db, $order_goods_no) { 

		$query=" UPDATE TBL_ORDER_GOODS
		            SET ORDER_STATE = 2, DELIVERY_NO = '', DELIVERY_DATE = null, FINISH_DATE = null
				  WHERE ORDER_GOODS_NO = '$order_goods_no' AND ORDER_STATE = 3 ";
		
		//echo $query."<br>";
		//exit;
  	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	// ???? ?????? ???? ?????? ?????? ?????? ??????
	function getOrderGoodsLatestSticker($db, $cp_no, $goods_no) { 

		$query=" SELECT G.GOODS_NAME, G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150, OG.OPT_STICKER_MSG 
				   FROM TBL_ORDER O
				   JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
				   JOIN TBL_GOODS G ON G.GOODS_NO = OG.OPT_STICKER_NO
				  WHERE O.CP_NO = '$cp_no' AND "; 
		
		if($goods_no <> 0) { 
			$query.=" OG.GOODS_NO = '$goods_no' AND ";
		}
		
		$query.=" 		OG.OPT_STICKER_NO <> 0
			   ORDER BY OG.REG_DATE DESC LIMIT 0, 1 
			   ";

		//echo $query."<br/>";
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

	function getOrderMemo($db, $order_goods_no){

		$query ="SELECT O.MEMO  
		           FROM TBL_ORDER_GOODS OG
				   JOIN TBL_ORDER O ON OG.RESERVE_NO = O.RESERVE_NO 
				  WHERE OG.ORDER_GOODS_NO = '".$order_goods_no."' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function getOrderReceiverZipcode($db, $order_goods_no){

		$query ="SELECT O.R_ZIPCODE  
		           FROM TBL_ORDER_GOODS OG
				   JOIN TBL_ORDER O ON OG.RESERVE_NO = O.RESERVE_NO 
				  WHERE OG.ORDER_GOODS_NO = '".$order_goods_no."' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}






	//////////////////////////////////////////////////////////////////////////////////////////
	// ?????????? -> ??????
	//////////////////////////////////////////////////////////////////////////////////////////


	function totalCntManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $homepage_filter) {

		// echo "homepage_filter : ".$homepage_filter."<br>";
	
		$query ="SELECT COUNT(DISTINCT G.RESERVE_NO) CNT 
				   FROM TBL_ORDER O 
				   JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
				   JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO

				   
				  
				  WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N' "; 
				  
				  //AND O.TOTAL_QTY <> 0 
				  //JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		}

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		}

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 

		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}
		if($homepage_filter <>""){
			$query.= " AND O.HOMEPAGE_YN='Y'	";
			$query.= " AND G.ORDER_STATE='1'	";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
				
							  OR C.CP_NM LIKE '%".$search_str."%'
							  OR C.CP_NM2 LIKE '%".$search_str."%'
							  
							  )"; 
				
				//			  OR C.CP_CODE = '".$search_str."'

			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM LIKE '%".$search_str."%' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE LIKE '%".$search_str."%' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "
		
			  	  SELECT DISTINCT O.ORDER_NO, O.RESERVE_NO, O.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, O.ORDER_STATE, O.TOTAL_PRICE, O.TOTAL_BUY_PRICE, O.TOTAL_SALE_PRICE, O.TOTAL_EXTRA_PRICE, O.TOTAL_DELIVERY_PRICE, O.TOTAL_SA_DELIVERY_PRICE, O.TOTAL_DISCOUNT_PRICE, O.TOTAL_QTY,
										 O.ORDER_DATE, O.PAY_TYPE, O.DELIVERY_TYPE, O.DELIVERY_DATE, O.FINISH_DATE, O.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE, O.OPT_MANAGER_NO
					FROM TBL_ORDER O 
			   LEFT JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
				   WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N'
								
					"; 
								//AND O.TOTAL_QTY <> 0
								//JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		}

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
				
							  )"; 
				//
				//			  OR C.CP_CODE = '".$search_str."'
				//			  OR C.CP_NM LIKE '%".$search_str."%'
				//			  OR C.CP_NM2 LIKE '%".$search_str."%'
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
	
			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "G.PAY_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", O.RESERVE_NO DESC 
		            limit ".$offset.", ".$nRowCount;

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


	function listManagerDelivery2($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $homepage_filter) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "
		 SELECT DISTINCT ORDER_NO, RESERVE_NO, ON_UID, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, 
										 O_PHONE, O_HPHONE, O_EMAIL, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL,
										 MEMO, ORDER_STATE, TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_SA_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY,
										 ORDER_DATE, PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, CANCEL_DATE, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE, OPT_MANAGER_NO
										 ,HOMEPAGE_YN, CALL_YN
		  FROM (
			  	  SELECT G.PAY_DATE, O.ORDER_NO, O.RESERVE_NO, O.ON_UID, O.MEM_NO, O.CP_NO, O.O_MEM_NM, O.O_ZIPCODE, O.O_ADDR1, O.O_ADDR2, 
										 O.O_PHONE, O.O_HPHONE, O.O_EMAIL, O.R_MEM_NM, O.R_ZIPCODE, O.R_ADDR1, O.R_ADDR2, O.R_PHONE, O.R_HPHONE, O.R_EMAIL,
										 O.MEMO, O.ORDER_STATE, O.TOTAL_PRICE, O.TOTAL_BUY_PRICE, O.TOTAL_SALE_PRICE, O.TOTAL_EXTRA_PRICE, O.TOTAL_DELIVERY_PRICE, O.TOTAL_SA_DELIVERY_PRICE, O.TOTAL_DISCOUNT_PRICE, O.TOTAL_QTY,
										 O.ORDER_DATE, O.PAY_TYPE, O.DELIVERY_TYPE, O.DELIVERY_DATE, O.FINISH_DATE, O.CANCEL_DATE, 
										 O.USE_TF, O.DEL_TF, O.REG_ADM, O.REG_DATE, O.DEL_ADM, O.DEL_DATE, O.OPT_MANAGER_NO
										 , O.HOMEPAGE_YN, O.CALL_YN
					FROM TBL_ORDER O 
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
			   LEFT JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
				   WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N'
								
					"; 
								//AND O.TOTAL_QTY <> 0
								//JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		}

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}
		if($homepage_filter <>""){
			$query.= " AND O.HOMEPAGE_YN='Y'	";
			$query.= " AND G.ORDER_STATE='1'	";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 

							  OR C.CP_NM LIKE '%".$search_str."%'
							  OR C.CP_NM2 LIKE '%".$search_str."%'

							  )"; 
				//
				//			  OR C.CP_CODE = '".$search_str."'

			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";
	
			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "G.PAY_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", O.RESERVE_NO DESC 
		           ) E limit ".$offset.", ".$nRowCount;

		// echo $query."<br/>";
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function sumManagerDelivery($db, $search_date_type, $start_date, $end_date, $bulk_tf, $order_state, $buy_cp_no, $sel_cp_no, $sel_cate_01, $sel_sale_confirm_tf, $work_flag, $opt_manager_no, $delivery_type, $delivery_cp, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(TOTAL_PRICE) AS SUM_TOTAL_PRICE, 
		                 SUM(TOTAL_BUY_PRICE) AS SUM_TOTAL_BUY_PRICE, 
						 SUM(TOTAL_SALE_PRICE) AS SUM_TOTAL_SALE_PRICE, 
						 SUM(TOTAL_EXTRA_PRICE) AS SUM_TOTAL_EXTRA_PRICE, 
						 SUM(TOTAL_DELIVERY_PRICE) AS SUM_TOTAL_DELIVERY_PRICE, 
						 SUM(TOTAL_SA_DELIVERY_PRICE) AS SUM_TOTAL_SA_DELIVERY_PRICE, 
						 SUM(TOTAL_DISCOUNT_PRICE) AS SUM_TOTAL_DISCOUNT_PRICE, 
						 SUM(TOTAL_QTY) AS SUM_TOTAL_QTY
				 FROM (
				  SELECT O.RESERVE_NO, O.TOTAL_PRICE,  O.TOTAL_BUY_PRICE,  O.TOTAL_SALE_PRICE,  O.TOTAL_EXTRA_PRICE,  O.TOTAL_DELIVERY_PRICE,  O.TOTAL_SA_DELIVERY_PRICE,  O.TOTAL_DISCOUNT_PRICE  , O.TOTAL_QTY

					FROM TBL_ORDER O 
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
					JOIN TBL_ORDER_GOODS G ON O.RESERVE_NO = G.RESERVE_NO
					
				   WHERE O.IS_PACKAGE = 'N' AND G.IS_PACKAGE = 'N'
								
				  "; 
				  //AND O.TOTAL_QTY <> 0
				  //JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO

	
		if($bulk_tf <> "") { 
			$query .= " AND G.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00' ";
		} else {

			if($search_date_type == "order_date") { 
				if ($start_date <> "") {
					$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "order_confirm_date") { 
				if ($start_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.ORDER_CONFIRM_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "work_date") { 
				if ($start_date <> "") {
					$query .= " AND G.WORK_END_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.WORK_END_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "reg_date") { 
				if ($start_date <> "") {
					$query .= " AND G.REG_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
				}		
			} else if ($search_date_type == "opt_outstock_date") { 

				if ($start_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "delivery_date") { 

				if ($start_date <> "") {
					$query .= " AND G.DELIVERY_DATE >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.DELIVERY_DATE <= '".$end_date." 23:59:59' ";
				}
			} else if ($search_date_type == "sale_confirm_date") { 

				if ($start_date <> "") {
					$query .= " AND G.SALE_CONFIRM_TF >= '".$start_date."' ";
				}

				if ($end_date <> "") {
					$query .= " AND G.SALE_CONFIRM_TF <= '".$end_date." 23:59:59' ";
				}
			}

		}

		if ($order_state <> "") {
			$query .= " AND O.ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND G.BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($sel_cp_no <> "") {
			$query .= " AND O.CP_NO = '".$sel_cp_no."' ";
		} 

		if ($sel_cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$sel_cate_01."' ";
		} 

		if ($sel_sale_confirm_tf <> "") {
			$query .= " AND G.SALE_CONFIRM_TF = '".$sel_sale_confirm_tf."' ";
		} 

		if ($work_flag <> "") {
			$query .= " AND G.WORK_FLAG = '".$work_flag."' ";
		} 

		if ($opt_manager_no <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$opt_manager_no."' ";
		} 


		if ($delivery_type <> "") {
			$query .= " AND G.DELIVERY_TYPE = '".$delivery_type."' ";
		} 
		
		if ($delivery_cp <> "") {
			$query .= " AND G.DELIVERY_CP = '".$delivery_cp."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.RESERVE_NO LIKE '%".$search_str."%' 
							  OR O.O_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_MEM_NM LIKE '%".$search_str."%' 
							  OR O.R_ADDR1 LIKE '%".$search_str."%' 
							  
							  OR G.ORDER_GOODS_NO = '".$search_str."' 
							  OR G.GOODS_CODE LIKE '%".$search_str."%' 
							  OR G.GOODS_NAME LIKE '%".$search_str."%' 
				
							  OR C.CP_NM LIKE '%".$search_str."%'
							  OR C.CP_NM2 LIKE '%".$search_str."%'							  
							  
							  )";
				//
				//			  OR C.CP_CODE = '".$search_str."'

			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND O.RESERVE_NO = '".$search_str."' ";
			} else if ($search_field == "O_MEM_NM") {
				$query .= " AND O.O_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_MEM_NM") {
				$query .= " AND O.R_MEM_NM = '".$search_str."' ";
			} else if ($search_field == "R_ADDR1") {
				$query .= " AND O.R_ADDR1 LIKE '%".$search_str."%' ";

			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND G.ORDER_GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE") {
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND G.GOODS_NAME LIKE '%".$search_str."%' ";
			
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
				$query .= " AND (C.CP_NM LIKE '%".$search_str."%' OR C.CP_NM2 LIKE '%".$search_str."%') ";
				
			//????????????
			} else if ($search_field == "CP_ORDER_NO") {
				$query .= " AND G.CP_ORDER_NO LIKE '%".$search_str."%' ";	
			
			//??????(????????????)
			} else if ($search_field == "R_MEM_NM_ALL") {
				$query .= " AND (O.R_MEM_NM LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_MEM_NM like '%".$search_str."%' )) ";
			
			//?????? + ???????? ??????
			} else if ($search_field == "GOODS_NAME_ALL") {
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' )) ";
			
			//????????(????????????)
			} else if ($search_field == "GOODS_CODE_ALL") {
				$query .= " AND (G.GOODS_CODE like '%".$search_str."%' OR G.GOODS_NO IN (SELECT GS.GOODS_NO FROM TBL_GOODS_SUB GS JOIN TBL_GOODS GG ON GS.GOODS_SUB_NO = GG.GOODS_NO WHERE GG.GOODS_CODE like '%".$search_str."%' )) ";
			
			//??????????(????????????)
			} else if ($search_field == "R_ADDR_ALL") {
				$query .= " AND (O.R_ADDR1 LIKE '%".$search_str."%' OR  G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' )) ";
			
			//??????(??????)
			} else if ($search_field == "REG_ADM") {
				$query .= " AND O.REG_ADM IN (SELECT ADM_NO FROM TBL_ADMIN_INFO WHERE ADM_NAME like '%".$search_str."%' ) ";
			
			//???????? ????(??????,??????,??????)
			} else if ($search_field == "INDIVIDUAL_DELIVERY") {
				$query .= " AND G.ORDER_GOODS_NO IN (SELECT OGI.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_INDIVIDUAL OGI WHERE OGI.R_ADDR1 like '%".$search_str."%' OR OGI.GOODS_DELIVERY_NAME like '%".$search_str."%' OR OGI.R_MEM_NM like '%".$search_str."%') ";

			//???????? ????(????)
			} else if ($search_field == "DELIVERY_NO") {
				$query .= " AND (G.ORDER_GOODS_NO IN (SELECT OGD.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY OGD WHERE OGD.DELIVERY_NO =  '".$search_str."') OR G.ORDER_GOODS_NO IN (SELECT OGDO.ORDER_GOODS_NO FROM TBL_ORDER_GOODS_DELIVERY_OUTSIDE OGDO WHERE OGDO.DELIVERY_NO =  '".$search_str."')) ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " GROUP BY O.RESERVE_NO, O.TOTAL_PRICE,  O.TOTAL_BUY_PRICE,  O.TOTAL_SALE_PRICE,  O.TOTAL_EXTRA_PRICE,  O.TOTAL_DELIVERY_PRICE,  O.TOTAL_SA_DELIVERY_PRICE,  O.TOTAL_DISCOUNT_PRICE, O.TOTAL_QTY ";

		$query .= " ) A";

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


	function listManagerOrderGoodsV3($db, $arr_reserve_no) {


		$query = "SELECT C.ORDER_GOODS_NO, C.CLAIM_ORDER_GOODS_NO, C.GROUP_NO, C.RESERVE_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
						 C.QTY, C.OPT_STICKER_NO, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO, 
						 C.DELIVERY_TYPE, C.CATE_01, C.CATE_02,
						 C.CATE_03, C.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_CONFIRM_DATE,
						 G.FILE_NM_100, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE,
						 C.DELIVERY_CP, C.DELIVERY_NO, C.CP_ORDER_NO, C.WORK_FLAG, C.WORK_QTY, C.WORK_START_DATE, C.WORK_END_DATE, 
						 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_YMD,
						 G.CATE_04 AS GOODS_STATE, G.TAX_TF,
						 R.HAS_GOODS_REQUEST

							  FROM TBL_ORDER_GOODS C 
						 LEFT JOIN TBL_GOODS G ON C.GOODS_NO = G.GOODS_NO
						 LEFT JOIN (SELECT ORDER_GOODS_NO, COUNT(*) AS HAS_GOODS_REQUEST 
						              FROM TBL_GOODS_REQUEST_GOODS 
				                     WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'
								  GROUP BY ORDER_GOODS_NO) R ON C.ORDER_GOODS_NO = R.ORDER_GOODS_NO
							 WHERE C.DEL_TF = 'N' AND G.DEL_TF = 'N' AND C.RESERVE_NO IN (".$arr_reserve_no.")
					      ORDER BY C.ORDER_GOODS_NO DESC ";

		//echo $query."<br/>"."<br/>";
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

	function listManagerOrderGoodsV2($db, $arr_reserve_no) {


		$query = "SELECT C.ORDER_GOODS_NO, C.CLAIM_ORDER_GOODS_NO, C.GROUP_NO, C.RESERVE_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
						 C.QTY, C.OPT_STICKER_NO, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO, 
						 C.DELIVERY_TYPE, C.CATE_01, C.CATE_02,
						 C.CATE_03, C.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_CONFIRM_DATE,
						 G.FILE_NM_100, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE,
						 C.DELIVERY_CP, C.DELIVERY_NO, C.CP_ORDER_NO, C.WORK_FLAG, C.WORK_QTY, C.WORK_START_DATE, C.WORK_END_DATE, 
						 C.SALE_CONFIRM_TF, C.SALE_CONFIRM_YMD,
						 G.CATE_04 AS GOODS_STATE, G.TAX_TF,
						 (SELECT COUNT(*)   
						    FROM TBL_GOODS_REQUEST_GOODS 
				           WHERE DEL_TF = 'N' AND CANCEL_TF = 'N' AND ORDER_GOODS_NO = C.ORDER_GOODS_NO) AS HAS_GOODS_REQUEST

							  FROM TBL_ORDER_GOODS C 
						      JOIN TBL_GOODS G ON C.GOODS_NO = G.GOODS_NO
						
							 WHERE C.DEL_TF = 'N' AND G.DEL_TF = 'N' AND C.RESERVE_NO IN (".$arr_reserve_no.")
						  ORDER BY C.ORDER_GOODS_NO DESC ";

		//echo $query."<br/>"."<br/>";
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

	function chkCompanyOrderNo($db, $cp_order_no, $goods_no) {
		
		$query = "SELECT COUNT(*)
					FROM TBL_ORDER_GOODS
					WHERE CP_ORDER_NO = '".$cp_order_no."' AND GOODS_NO = '".$goods_no."'
						AND USE_TF =  'Y'
						AND DEL_TF =  'N'
						";

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	//???? - ???? ???? ?????? -> ?????? ?????? ???????? ?????? ?????? 
	function listOrderGoodsDeliveryForStickerWithoutDeliveryNo($db, $cp_no) {

		$query = "
				SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_CNT, SEQ_OF_DELIVERY, DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, DELIVERY_CLAIM_CODE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
		          FROM TBL_ORDER_GOODS_DELIVERY 
		         WHERE CP_NO = '$cp_no' 
				   AND USE_TF = 'Y' 
				   AND DEL_TF = 'N' 
				   AND DELIVERY_NO = ''
				    "; //

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

	//2018-11-29 ?????? ?????? ???? ?????? ???? ????
	function cancelOrderGoodsRefundableQty($db, $group_no, $cancel_qty) { 

		$query=" UPDATE TBL_ORDER_GOODS
		            SET REFUNDABLE_QTY = REFUNDABLE_QTY + $cancel_qty
				  WHERE ORDER_GOODS_NO = '$group_no' ";
		
		//echo $query."<br>";
		//exit;
  	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]?????? ?????????????? - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	
	function selectCompanyNumberAndMemberName($db, $delivery_seq, $delivery_cp){
		$query =    "SELECT 
								O.CP_NO, O.O_MEM_NM
							FROM
								TBL_ORDER_GOODS OG
								JOIN
								TBL_ORDER_GOODS_DELIVERY OGD ON OG.ORDER_GOODS_NO = OGD.ORDER_GOODS_NO
								JOIN
								TBL_ORDER O ON O.RESERVE_NO = OG.RESERVE_NO
							WHERE
								OGD.DELIVERY_SEQ = '$delivery_seq'
								AND OGD.DELIVERY_CP = '$delivery_cp'
								AND OG.USE_TF = 'Y'
								AND OG.DEL_TF = 'N'
								AND OGD.USE_TF = 'Y'
								AND OGD.DEL_TF = 'N'
								AND O.USE_TF = 'Y'
								AND O.DEL_TF = 'N'
		";
	
		$result = mysql_query($query,$db);
		$record = array();
		
		// echo $query;
	
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
	
		return $record;
	}

	function getSalesmanWithReserveno($db,$reserveno){
		$query =    "SELECT ADM_NAME
							FROM TBL_ORDER O JOIN TBL_ADMIN_INFO A ON O.OPT_MANAGER_NO = A.ADM_NO
							WHERE O.RESERVE_NO = '$reserveno'
								AND O.DEL_TF = 'N'
								AND O.USE_TF = 'Y'
								AND A.DEL_TF = 'N'
								AND A.USE_TF = 'Y'
		";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
?>