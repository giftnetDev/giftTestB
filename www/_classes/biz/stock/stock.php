<?
	# =============================================================================
	# File Name    : stock.php
	# =============================================================================


	function listStockTotalGoods($db, $start_date, $end_date, $in_cp_no, $out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$is_same			= $filter['is_same'];
		$is_under_mstock	= $filter['is_under_mstock'];
		$is_zero			= $filter['is_zero'];
		$is_set				= $filter['is_set'];

		$offset = $nRowCount*($nPage-1);
		$logical_num = ($total_cnt - $offset) + 1 ;
		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);


		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, B.DELIVERY_CNT_IN_BOX,
										 IFNULL(SUM(IN_QTY),0) AS S_IN_QTY, 
										 IFNULL(SUM(IN_BQTY),0) AS S_IN_BQTY, 
										 IFNULL(SUM(IN_FQTY),0) AS S_IN_FQTY, 
										 IFNULL(SUM(OUT_QTY),0) AS S_OUT_QTY, 
										 IFNULL(SUM(OUT_BQTY),0) AS S_OUT_BQTY, 
										 B.STOCK_CNT,
										 B.BSTOCK_CNT,
										 B.FSTOCK_CNT,
										 B.MSTOCK_CNT,

										 CASE WHEN INSTR(B.GOODS_CATE , '14') = 1 THEN 1 ELSE 0 END AS IS_SET, 

										 CASE WHEN IFNULL(SUM(IN_QTY),0) - IFNULL(SUM(OUT_QTY),0) <> B.STOCK_CNT || 
												   IFNULL(SUM(IN_FQTY),0) <> B.FSTOCK_CNT ||
												   IFNULL(SUM(IN_BQTY),0) - IFNULL(SUM(OUT_BQTY),0) <> B.BSTOCK_CNT
											  THEN 'N'
											  ELSE 'Y'
										  END AS IS_SAME

								FROM TBL_STOCK A 
						  RIGHT JOIN (SELECT * 
						                FROM TBL_GOODS 
									   WHERE DEL_TF ='N' 
									     AND USE_TF ='Y'  "; 
										//  AND STOCK_TF = 'Y' "; //2021_10_12 : 재고 조회시 STOCK_TF가 chk되어있지 않으면 조회가 안 되는데 STOCK_TF가 딱히 다른 의미를 가지고 있지는 않아 사용의 의미를 없엠
		
		if ($is_set == "")
			$query .= "				     AND GOODS_CATE NOT LIKE '14%' ";

		$query .= "					 ) B ON A.GOODS_NO = B.GOODS_NO 
						         AND A.DEL_TF ='N' 
								 AND A.CLOSE_TF = 'N' 
							 WHERE 1 = 1   ";
		
		//2016-02-03 최소재고 세팅 되어있는 상품만 재고관리
		//$query .= " AND B.MSTOCK_CNT <> 0  ";

		if ($end_date <> "") {
			$query .= " AND A.IN_DATE <= '".$end_date." 23:59:59' AND A.OUT_DATE <= '".$end_date." 23:59:59'";
		}

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($con_cate != "")
			$query .= " AND B.GOODS_CATE LIKE '".$con_cate."%' ";


		$query .= $where_cause;

		if ($search_str <> "") {
			if($search_field == "ALL") 
				$query .= " AND (B.GOODS_NAME LIKE '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%') ";
			else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		if ($is_zero == "") {
			$query .= " AND (B.STOCK_CNT <> 0 OR B.BSTOCK_CNT <> 0 OR B.FSTOCK_CNT <> 0)  ";
		} 

		$query .= " GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, B.MSTOCK_CNT ";
		$query .= " HAVING 1=1 ";

		if ($is_same == 'N')
			$query .=" AND IS_SAME =  'N' ";

		if ($is_under_mstock <> "") {
			$query .= " AND IFNULL(SUM(A.IN_QTY),0) - IFNULL(SUM(A.OUT_QTY),0) < B.MSTOCK_CNT AND B.MSTOCK_CNT > 0 ";
		} 


		if ($order_field == "") 
			$order_field = "B.GOODS_NAME";

		if ($order_str == "") 
			$order_str = "ASC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

		echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntStockTotalGoods($db, $start_date, $end_date, $in_cp_no, $out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str) {

		$is_same			= $filter['is_same'];
		$is_under_mstock	= $filter['is_under_mstock'];
		$is_zero			= $filter['is_zero'];
		$is_set				= $filter['is_set'];

		$query = "SELECT COUNT(*)
					FROM (
							SELECT B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, B.MSTOCK_CNT, 
									CASE WHEN 	   IFNULL(SUM(IN_QTY),0) - IFNULL(SUM(OUT_QTY),0) <> B.STOCK_CNT || 
												   IFNULL(SUM(IN_FQTY),0) <> B.FSTOCK_CNT ||
												   IFNULL(SUM(IN_BQTY),0) - IFNULL(SUM(OUT_BQTY),0) <> B.BSTOCK_CNT
											  THEN 'N'
											  ELSE 'Y'
										  END AS IS_SAME
							  FROM TBL_STOCK A 
						RIGHT JOIN (SELECT * 
						              FROM TBL_GOODS 
						             WHERE DEL_TF ='N' 
									   AND USE_TF ='Y' ";
									//    AND STOCK_TF = 'Y' ";
		
		if ($is_set == "")
			$query .= "				   AND GOODS_CATE NOT LIKE '14%' ";

		$query .= "					) B ON A.GOODS_NO = B.GOODS_NO 
						       AND A.DEL_TF ='N' 
							   AND A.CLOSE_TF = 'N' 
							  
							 WHERE 1 = 1 ";
		
		//2016-02-03 최소재고 세팅 되어있는 것만 재고관리
		//$query .= " AND B.MSTOCK_CNT <> 0 ";

		if ($end_date <> "") {
			$query .= " AND A.IN_DATE <= '".$end_date." 23:59:59' AND A.OUT_DATE <= '".$end_date." 23:59:59'";
		}

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($con_cate != "")
			$query .= " AND B.GOODS_CATE LIKE '".$con_cate."%' ";

		$query .= $where_cause;

		if ($search_str <> "") {
			if($search_field == "ALL") 
				$query .= " AND (B.GOODS_NAME LIKE '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%') ";
			else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		if ($is_zero == "") {
			$query .= " AND (B.STOCK_CNT <> 0 OR B.BSTOCK_CNT <> 0 OR B.FSTOCK_CNT <> 0)  ";
		} 

		$query .= " GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, B.MSTOCK_CNT ";
		$query .= " HAVING 1=1 ";

		if ($is_same == 'N')
			$query .=" AND IS_SAME = 'N' ";

		if ($is_under_mstock <> "") {
			$query .= " AND IFNULL(SUM(A.IN_QTY),0) - IFNULL(SUM(A.OUT_QTY),0) < B.MSTOCK_CNT AND B.MSTOCK_CNT > 0 ";
		} 

		$query .= " ) C";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listStock($db, $start_date, $end_date, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $loc, $filter, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 A.STOCK_NO, A.STOCK_TYPE, A.STOCK_CODE,
										 A.IN_CP_NO, A.OUT_CP_NO, A.GOODS_NO,
										 A.IN_LOC, A.IN_LOC_EXT, A.IN_QTY, A.IN_BQTY,
										 A.IN_FQTY, A.OUT_QTY, A.OUT_BQTY, A.OUT_TQTY,
										 A.IN_PRICE, A.OUT_PRICE, A.IN_DATE, A.OUT_DATE, A.PAY_DATE, 
										 A.RESERVE_NO, A.ORDER_GOODS_NO, A.RGN_NO, A.CLOSE_TF, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.DEL_ADM, A.DEL_DATE, A.MEMO, A.PREV_STOCK_NO, A.BB_NO,
										 B.GOODS_NAME, B.GOODS_SUB_NAME, B.GOODS_CODE, B.GOODS_CATE
								FROM TBL_STOCK A 
								JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
							   WHERE 1 = 1 ";

		if($stock_type == "IN")
		{
			if ($start_date <> "") {
				$query .= " AND A.IN_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.IN_DATE <= '".$end_date." 23:59:59' ";
			}
		}else if($stock_type == "OUT") {
			if ($start_date <> "") {
				$query .= " AND A.OUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.OUT_DATE <= '".$end_date." 23:59:59' ";
			}

		}else {
			if ($start_date <> "") {
				$query .= " AND A.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
			}
		}


		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE= '".$stock_code."' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($loc <> "") {
			$query .= " AND A.IN_LOC = '".$loc."' ";
		} 

		$query .= " AND A.CLOSE_TF = 'N' ";

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%' OR A.IN_LOC_EXT LIKE '%".$search_str."%' OR A.RESERVE_NO = '".$search_str."'  OR A.ORDER_GOODS_NO = '".$search_str."' OR A.MEMO LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "IN_LOC_EXT"){
				$query .= " AND A.IN_LOC_EXT = '".$search_str."' ";
			} else if ($search_field == "RESERVE_NO"){
				$query .= " AND (A.RESERVE_NO = '".$search_str."') ";
			} else if ($search_field == "ORDER_GOODS_NO"){
				$query .= " AND (A.ORDER_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "RGN_NO"){
				$query .= " AND (A.RGN_NO = '".$search_str."') ";
			} else if ($search_field == "WORK_DONE_NO"){
				$query .= " AND (A.WORK_DONE_NO = '".$search_str."') ";
			} else if ($search_field == "BB_NO"){
				$query .= " AND (A.BB_NO = '".$search_str."') ";
			} else if ($search_field == "STOCK_NO"){
				$query .= " AND A.STOCK_NO = '".$search_str."' ";
			} else if ($search_field == "FSTOCK_ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%' OR A.IN_LOC_EXT LIKE '%".$search_str."%' OR A.RESERVE_NO LIKE '%".$search_str."' OR A.MEMO LIKE '%".$search_str."%' OR B.CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_NM LIKE '%".$search_str."%' OR CP_NM2 LIKE '%".$search_str."%')) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "A.REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

		// echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		// echo "size : ".sizeof($record)."<br>";
		return $record;
	}

	function totalCntStock($db, $start_date, $end_date, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $loc, $filter, $del_tf, $search_field, $search_str) {

		$query = "		
						SELECT COUNT(*) AS CNT
						FROM (
								SELECT A.STOCK_NO
								  FROM TBL_STOCK A
								  JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO
								 WHERE 1 = 1 ";

		if($stock_type == "IN")
		{
			if ($start_date <> "") {
				$query .= " AND A.IN_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.IN_DATE <= '".$end_date." 23:59:59' ";
			}
		}else if($stock_type == "OUT") {
			if ($start_date <> "") {
				$query .= " AND A.OUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.OUT_DATE <= '".$end_date." 23:59:59' ";
			}

		}else {
			if ($start_date <> "") {
				$query .= " AND A.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
			}
		}

		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE= '".$stock_code."' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($loc <> "") {
			$query .= " AND A.IN_LOC = '".$loc."' ";
		} 

		$query .= " AND A.CLOSE_TF = 'N' ";

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%' OR A.IN_LOC_EXT LIKE '%".$search_str."%' OR A.RESERVE_NO = '".$search_str."'  OR A.ORDER_GOODS_NO = '".$search_str."' OR A.MEMO LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "IN_LOC_EXT"){
				$query .= " AND A.IN_LOC_EXT = '".$search_str."' ";
			} else if ($search_field == "RESERVE_NO"){
				$query .= " AND (A.RESERVE_NO = '".$search_str."') ";
			} else if ($search_field == "ORDER_GOODS_NO"){
				$query .= " AND (A.ORDER_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "RGN_NO"){
				$query .= " AND (A.RGN_NO = '".$search_str."') ";
			} else if ($search_field == "WORK_DONE_NO"){
				$query .= " AND (A.WORK_DONE_NO = '".$search_str."') ";
			} else if ($search_field == "BB_NO"){
				$query .= " AND (A.BB_NO = '".$search_str."') ";
			} else if ($search_field == "STOCK_NO"){
				$query .= " AND A.STOCK_NO = '".$search_str."' ";
			} else if ($search_field == "FSTOCK_ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE LIKE '%".$search_str."%' OR A.IN_LOC_EXT LIKE '%".$search_str."%' OR A.RESERVE_NO LIKE '%".$search_str."' OR A.MEMO LIKE '%".$search_str."%' OR B.CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_NM LIKE '%".$search_str."%' OR CP_NM2 LIKE '%".$search_str."%')) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ) C ";

	    //echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $reg_adm, $memo) {

			$query="SELECT COUNT(*) 
			          FROM TBL_STOCK 
					 WHERE STOCK_TYPE = '$stock_type' AND  
						   STOCK_CODE = '$stock_code' AND 
						   IN_CP_NO = '$in_cp_no' AND 
						   OUT_CP_NO = '$out_cp_no' AND  
						   GOODS_NO = '$goods_no' AND 
						   IN_LOC = '$in_loc' AND  
						   IN_LOC_EXT = '$in_loc_ext' AND  
						   IN_QTY = '$in_qty' AND 
						   IN_BQTY = '$in_bqty' AND 
						   IN_FQTY = '$in_fqty' AND  
						   OUT_QTY = '$out_qty' AND  
						   OUT_BQTY = '$out_bqty' AND  
						   OUT_TQTY = '$out_tqty' AND 
						   IN_PRICE = '$in_price' AND  
						   OUT_PRICE = '$out_price' AND  
						   IN_DATE = '$in_date' AND  
						   OUT_DATE = '$out_date' AND  
						   PAY_DATE = '$pay_date' AND  
						   RESERVE_NO = '$reserve_no' AND  
						   ORDER_GOODS_NO = '$order_goods_no' AND
						   RGN_NO = '$rgn_no' AND
						   CLOSE_TF = '$close_tf' AND  
						   REG_ADM = '$reg_adm' AND 
						   MEMO = '$memo' ";
			//echo $query."<br>";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			//2016-06-15 선출고 이중등록때문에 방지코드 입력
			if($record > 0) return;

			//주문번호 없을때 가져오기
			if($reserve_no == "" && $order_goods_no <> "") { 
				$query="SELECT RESERVE_NO 
						  FROM TBL_ORDER_GOODS
						 WHERE ORDER_GOODS_NO = '$order_goods_no' ";
				//echo $query."<br>";
				$result = mysql_query($query,$db);
				$rows   = mysql_fetch_array($result);
				$reserve_no  = $rows[0];
			}

			// 입력 값에 따라 TBL_GOODS 테이블 수랭 변경 합니다.
			// $stock_type IN, OUT

			if ($stock_type =="IN") {
					if ($in_qty <> "") $query = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT + $in_qty WHERE GOODS_NO = '$goods_no' ";
					if ($in_bqty <> "") $query = "UPDATE TBL_GOODS SET BSTOCK_CNT = BSTOCK_CNT + $in_bqty WHERE GOODS_NO = '$goods_no' ";
					if ($in_fqty <> "") $query = "UPDATE TBL_GOODS SET FSTOCK_CNT = FSTOCK_CNT + $in_fqty WHERE GOODS_NO = '$goods_no' ";
					mysql_query($query,$db);
			}

			if ($stock_type =="OUT") {
					if ($out_qty <> "") $query = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT - $out_qty WHERE GOODS_NO = '$goods_no' ";
					if ($out_bqty <> "") $query = "UPDATE TBL_GOODS SET BSTOCK_CNT = BSTOCK_CNT - $out_bqty WHERE GOODS_NO = '$goods_no' ";
					if ($out_tqty <> "") $query = "UPDATE TBL_GOODS SET TSTOCK_CNT = TSTOCK_CNT - $out_tqty WHERE GOODS_NO = '$goods_no' ";
					mysql_query($query,$db);
			}

			//echo $query."<br>";

			$query="INSERT INTO TBL_STOCK (STOCK_TYPE, STOCK_CODE, IN_CP_NO, OUT_CP_NO, GOODS_NO,
																					IN_LOC, IN_LOC_EXT, IN_QTY, IN_BQTY,
																					IN_FQTY, OUT_QTY, OUT_BQTY, OUT_TQTY,
																					IN_PRICE, OUT_PRICE, IN_DATE, OUT_DATE, PAY_DATE, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, CLOSE_TF, REG_ADM, REG_DATE,MEMO) 
													values ('$stock_type', '$stock_code', '$in_cp_no', '$out_cp_no', '$goods_no',
																	'$in_loc', '$in_loc_ext', '$in_qty', '$in_bqty', '$in_fqty', '$out_qty', '$out_bqty', 
																	'$out_tqty', '$in_price', '$out_price',  '$in_date', '$out_date', '$pay_date', '$reserve_no', '$order_goods_no', '$rgn_no', '$close_tf', '$reg_adm', now(),'$memo'); ";

		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			$query ="SELECT MAX(STOCK_NO) FROM TBL_STOCK";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			return $rows[0];
		}
	}

	function selectStock($db, $stock_no) {

		$query = "SELECT A.STOCK_NO, A.STOCK_TYPE, A.STOCK_CODE,
										 A.IN_CP_NO, A.OUT_CP_NO, A.GOODS_NO,
										 A.IN_LOC, A.IN_LOC_EXT, A.IN_QTY, A.IN_BQTY,
										 A.IN_FQTY, A.OUT_QTY, A.OUT_BQTY, A.OUT_TQTY,
										 A.IN_PRICE, A.OUT_PRICE, A.RESERVE_NO, A.CLOSE_TF, A.IN_DATE, A.OUT_DATE, PAY_DATE, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.DEL_ADM, A.DEL_DATE, A.MEMO,
										 B.GOODS_NAME, B.GOODS_CODE
								FROM TBL_STOCK A, TBL_GOODS B WHERE A.GOODS_NO = B.GOODS_NO AND  A.STOCK_NO = '$stock_no' ";
		
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



	function updateStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $reserve_no, $order_goods_no, $rgn_no, $in_date, $out_date, $pay_date, $close_tf, $up_adm, $memo, $stock_no) {
		
		$query = "SELECT STOCK_TYPE, GOODS_NO, IN_QTY, IN_BQTY, IN_FQTY, OUT_QTY, OUT_BQTY, OUT_TQTY 
		            FROM TBL_STOCK 
				   WHERE STOCK_NO = '$stock_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$STOCK_TYPE		= $rows[0];
		$GOODS_NO			= $rows[1];
		$IN_QTY				= $rows[2];
		$IN_BQTY			= $rows[3];
		$IN_FQTY			= $rows[4];
		$OUT_QTY			= $rows[5];
		$OUT_BQTY			= $rows[6];
		$OUT_TQTY			= $rows[7];
		
		if ($STOCK_TYPE == "IN") {
			$query = "UPDATE TBL_GOODS SET 
												STOCK_CNT = STOCK_CNT - $IN_QTY, 
												BSTOCK_CNT = BSTOCK_CNT - $IN_BQTY, 
												FSTOCK_CNT = FSTOCK_CNT - $IN_FQTY 
								 WHERE GOODS_NO = '$GOODS_NO' ";

		}

		if ($STOCK_TYPE == "OUT") {
			$query = "UPDATE TBL_GOODS SET 
												STOCK_CNT = STOCK_CNT + $OUT_QTY, 
												BSTOCK_CNT = BSTOCK_CNT + $OUT_BQTY, 
												TSTOCK_CNT = TSTOCK_CNT + $OUT_TQTY 
								 WHERE GOODS_NO = '$GOODS_NO' ";

		}

		mysql_query($query,$db);

		if ($stock_type =="IN") {
				if ($in_qty <> "") $query = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT + $in_qty WHERE GOODS_NO = '$goods_no' ";
				if ($in_bqty <> "") $query = "UPDATE TBL_GOODS SET BSTOCK_CNT = BSTOCK_CNT + $in_bqty WHERE GOODS_NO = '$goods_no' ";
				if ($in_fqty <> "") $query = "UPDATE TBL_GOODS SET FSTOCK_CNT = FSTOCK_CNT + $in_fqty WHERE GOODS_NO = '$goods_no' ";
		}

		if ($stock_type =="OUT") {
				if ($out_qty <> "") $query = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT - $out_qty WHERE GOODS_NO = '$goods_no' ";
				if ($out_bqty <> "") $query = "UPDATE TBL_GOODS SET BSTOCK_CNT = BSTOCK_CNT - $out_bqty WHERE GOODS_NO = '$goods_no' ";
				if ($out_tqty <> "") $query = "UPDATE TBL_GOODS SET TSTOCK_CNT = TSTOCK_CNT - $out_tqty WHERE GOODS_NO = '$goods_no' ";
		}
		
		mysql_query($query,$db);

		$query="UPDATE TBL_STOCK SET 
							STOCK_TYPE			= '$stock_type',
							STOCK_CODE			= '$stock_code',
							IN_CP_NO				= '$in_cp_no',
							OUT_CP_NO				= '$out_cp_no',
							GOODS_NO				= '$goods_no',
							IN_LOC					= '$in_loc',
							IN_LOC_EXT				= '$in_loc_ext',
							IN_QTY					= '$in_qty',
							IN_BQTY					= '$in_bqty',
							IN_FQTY					= '$in_fqty',
							OUT_QTY					= '$out_qty',
							OUT_BQTY				= '$out_bqty',
							OUT_TQTY				= '$out_tqty',
							IN_PRICE				= '$in_price',
							OUT_PRICE				= '$out_price',
							RESERVE_NO				= '$reserve_no',
							ORDER_GOODS_NO			= '$order_goods_no',
							RGN_NO					= '$rgn_no',
							IN_DATE					= '$in_date',
							OUT_DATE				= '$out_date',
							CLOSE_TF				= '$close_tf',
							MEMO					= '$memo'
						WHERE STOCK_NO				= '$stock_no' ";
		
		//echo $query;
		//exit;


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteStock($db, $stock_no, $reg_adm) {

		$query = "SELECT STOCK_TYPE, GOODS_NO, IN_QTY, IN_BQTY, IN_FQTY, OUT_QTY, OUT_BQTY, OUT_TQTY, RESERVE_NO, ORDER_GOODS_NO, RGN_NO
		            FROM TBL_STOCK 
				   WHERE STOCK_NO = '$stock_no' AND CLOSE_TF = 'N' AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$STOCK_TYPE		= $rows[0];
		$GOODS_NO			= $rows[1];
		$IN_QTY				= $rows[2];
		$IN_BQTY			= $rows[3];
		$IN_FQTY			= $rows[4];
		$OUT_QTY			= $rows[5];
		$OUT_BQTY			= $rows[6];
		$OUT_TQTY			= $rows[7];
		$RESERVE_NO			= $rows[8];
		$ORDER_GOODS_NO		= $rows[9];
		$RGN_NO				= $rows[10];
		
		if ($STOCK_TYPE == "IN") {
			$query = "UPDATE TBL_GOODS 
			             SET 
								STOCK_CNT = STOCK_CNT - $IN_QTY, 
								BSTOCK_CNT = BSTOCK_CNT - $IN_BQTY, 
								FSTOCK_CNT = FSTOCK_CNT - $IN_FQTY 
    				   WHERE GOODS_NO = '$GOODS_NO' ";

		}

		//echo "상품에서 빼는 쿼리 ".$query."<br>";

		if ($STOCK_TYPE == "OUT") {
			$query = "UPDATE TBL_GOODS 
						 SET 
								STOCK_CNT = STOCK_CNT + $OUT_QTY, 
								BSTOCK_CNT = BSTOCK_CNT + $OUT_BQTY, 
								TSTOCK_CNT = TSTOCK_CNT + $OUT_TQTY 
   					   WHERE GOODS_NO = '$GOODS_NO' ";

		}

		//echo "상품에서 넣는 쿼리 ".$query."<br>";

		mysql_query($query,$db);

		$query="UPDATE TBL_STOCK 
		           SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$reg_adm'  
				 WHERE STOCK_NO = '$stock_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			
			if($RGN_NO != 0)
				updateGoodsRequestGoodsQty($db, $RGN_NO);

			return true;
		}
	}

	// 가입고를 실입고로 전환
	function updateStatusFStock($db, $stock_no, $input_qty, $input_bqty, $memo, $reg_adm) {

		$query = "SELECT STOCK_TYPE, STOCK_CODE, GOODS_NO, IN_QTY, IN_BQTY, IN_FQTY, OUT_QTY, OUT_BQTY, OUT_TQTY, IN_PRICE, 
						 IN_CP_NO, RESERVE_NO, ORDER_GOODS_NO, WORK_DONE_NO, RGN_NO, BB_NO, MEMO, IN_LOC 
					FROM TBL_STOCK 
				   WHERE STOCK_NO = '".$stock_no."' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$STOCK_TYPE			= $rows[0];
		$STOCK_CODE			= $rows[1];
		$GOODS_NO			= $rows[2];
		$IN_QTY				= $rows[3];
		$IN_BQTY			= $rows[4];
		$IN_FQTY			= $rows[5];
		$OUT_QTY			= $rows[6];
		$OUT_BQTY			= $rows[7];
		$OUT_TQTY			= $rows[8];
		$IN_PRICE			= $rows[9];
		$IN_CP_NO			= $rows[10];
		$RESERVE_NO			= $rows[11];
		$ORDER_GOODS_NO		= $rows[12];
		$WORK_DONE_NO		= $rows[13];
		$RGN_NO				= $rows[14];
		$BB_NO				= $rows[15];
		$MEMO				= $rows[16];
		$IN_LOC				= $rows[17];

		//가입고 전환시 넣은 메모가 있다면 입력, 없으면 가입고 메모 유지
		if($memo != null)
			$MEMO = $memo;


		// 가입고가 아니면 패스
		if($STOCK_CODE != "FST02") return false;

		$LAST_INSERTED_TIME = strtotime(getLastInsertedGoodsRequestGoodsQty($db, $RGN_NO, $GOODS_NO, $IN_FQTY));
		$now =  strtotime("0 month");

		//5분->30분(2016-07-27)내에 같은 수량으로 재입고 된게 있다면 패스 (refresh로 간주)
		if($now - $LAST_INSERTED_TIME <= 1800) return false;

		$sum_input_qty = $input_qty + $input_bqty;

		//2016-10-26 가재고보다 실입고가 많을 경우에 이전에 더 잡힌 가재고를 확인해야하고 만약 그럴경우에 수량을 나눠서 입고 시켜야함 * 꼭 확인 * 발주 입고 상황에도 영향을 줌
		// 예) (20161026)가입고 100개  <-- 여기다  130개 입고를 쳐버리면  ->  가입고 0  실입고 130 
		//     (20160910)가입고 30개                              ->  가입고 30이 남아버려서 
		//    -------------------
		//      (합) 상품 가입고 130개                              ->  상품 가입고 30개 
		//$query = "UPDATE TBL_GOODS  SET 
		//								FSTOCK_CNT = (CASE WHEN FSTOCK_CNT - $sum_input_qty >= 0 THEN FSTOCK_CNT - $sum_input_qty ELSE 0 END)
		//							WHERE GOODS_NO = '$GOODS_NO' ";
		/*
		//가입고보다 입고량이 많은 경우가 있어서 TBL_STOCK 수정 이후에 새로 계산해서 싱크 
		$query = "UPDATE TBL_GOODS  
					 SET FSTOCK_CNT = CASE 
											WHEN FSTOCK_CNT - $sum_input_qty >= 0 
											THEN FSTOCK_CNT - $sum_input_qty 
											ELSE 0 
											END
				   WHERE GOODS_NO = '$GOODS_NO' ";
		mysql_query($query,$db);
		*/

		

		$query="UPDATE TBL_STOCK 
		           SET IN_FQTY = (CASE 
										WHEN (IN_FQTY - ($sum_input_qty)) >= 0 
										THEN IN_FQTY - ($sum_input_qty) 
										ELSE 0 
										END)   ";
		
		if($sum_input_qty >= $IN_FQTY) { 
			$query .= " , DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$reg_adm' ";
		}

		$query .= "		 WHERE STOCK_NO = '$stock_no' ";

		//echo $query."<br/>";
		mysql_query($query,$db);

		


		//발주상품데이터를 입고받음으로 수정 -- 20160922 교환, 반품일때는 패스
		if($RGN_NO != 0 || $RGN_NO != null) { 
			$query="UPDATE TBL_GOODS_REQUEST_GOODS 
			           SET RECEIVE_QTY = RECEIVE_QTY + ($sum_input_qty), RECEIVE_DATE = now() 
					 WHERE REQ_GOODS_NO = '".$RGN_NO."' AND CANCEL_TF = 'N' AND DEL_TF = 'N' ";
			mysql_query($query,$db);
		}

		$query = "SELECT S.GOODS_SUB_NO, S.GOODS_CNT, G.GOODS_CATE
					FROM TBL_GOODS_SUB S
					JOIN TBL_GOODS G
					WHERE S.GOODS_SUB_NO = G.GOODS_NO
					AND S.GOODS_NO = '".$GOODS_NO."'";

		$query_result = mysql_query($query,$db);
		$rows_cnt = mysql_num_rows($query_result);
		$record = array();

		//구성품이 있는 경우 가입고 -> 정상/불량 입고 전환 시에 구성품이 입고됨
		if ($query_result <> "" && $rows_cnt > 0 && $sum_input_qty > 0) {
			for($i=0;$i < $rows_cnt;$i++) {
				$record[$i] = sql_result_array($query_result,$i);

				$GOODS_SUB_NO = $record[$i]["GOODS_SUB_NO"];
				$GOODS_CNT	  = $record[$i]["GOODS_CNT"];
				$GOODS_CATE   = $record[$i]["GOODS_CATE"];

				//아웃박스 제외
				if($GOODS_CATE != "010202") { 

					if($input_qty > 0) { 

						$cp_no = $IN_CP_NO;
						$in_qty = ($input_qty * $GOODS_CNT); 
						$in_bqty = 0; 
						$in_fqty = 0; 
						$out_qty = 0;
						$out_bqty = 0;
						$out_tqty = 0;
						$price = $IN_PRICE;
						$out_price = 0;
						$in_date = date("Y-m-d H:i:s",strtotime("0 month"));
						$out_date = '';
						$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
						$close_tf = 'N';
						$memo = $MEMO;

						$result = insertStock($db, 'IN', 'NST01', $cp_no, '', $GOODS_SUB_NO, $IN_LOC, '가입고전환-세트해체', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);

						// 되돌리기 추적을 위해 이전 내용 남김
						$query = "UPDATE TBL_STOCK 
									 SET PREV_STOCK_NO = '$stock_no', BB_NO = '$BB_NO'
								   WHERE STOCK_NO = LAST_INSERT_ID();
								 ";
						mysql_query($query,$db);

					}

					if($input_bqty > 0) { 

						$cp_no = $IN_CP_NO;
						$in_qty = 0; 
						$in_bqty = ($input_bqty * $GOODS_CNT);  
						$in_fqty = 0; 
						$out_qty = 0;
						$out_bqty = 0;
						$out_tqty = 0;
						$price = $IN_PRICE;
						$out_price = 0;
						$in_date = date("Y-m-d H:i:s",strtotime("0 month"));
						$out_date = '';
						$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
						$close_tf = 'N';
						$memo = $MEMO;

						$result = insertStock($db, 'IN', 'BST03', $cp_no, '', $GOODS_SUB_NO, $IN_LOC, '가입고전환-세트해체', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);

						// 되돌리기 추적을 위해 이전 내용 남김
						$query = "UPDATE TBL_STOCK 
									 SET PREV_STOCK_NO = '$stock_no', BB_NO = '$BB_NO'
								   WHERE STOCK_NO = LAST_INSERT_ID();
								 ";
						mysql_query($query,$db);
					}
					
				}

			}

			syncGoodsStock($db, $GOODS_NO);

		} else { 

			if($input_qty <> 0) { 

				if($input_qty > 0) { 
					$cp_no = $IN_CP_NO;
					$in_qty = $input_qty; 
					$in_bqty = 0; 
					$in_fqty = 0; 
					$out_qty = 0;
					$out_bqty = 0;
					$out_tqty = 0;
					$price = $IN_PRICE;
					$out_price = 0;
					$in_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$out_date = '';
					$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$close_tf = 'N';
					$memo = $MEMO;

					$result = insertStock($db, 'IN', 'NST01', $cp_no, '', $GOODS_NO, $IN_LOC, '가입고전환', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);
				} else { 
					$cp_no = $IN_CP_NO;
					$in_qty = 0; 
					$in_bqty = 0; 
					$in_fqty = 0; 
					$out_qty = abs($input_qty);
					$out_bqty = 0;
					$out_tqty = 0;
					$price = 0;
					$out_price = $IN_PRICE;
					$in_date = '';
					$out_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$close_tf = 'N';
					$memo = $MEMO;

					$result = insertStock($db, 'OUT', 'NOUT01', '', $cp_no, $GOODS_NO, $IN_LOC, '가출고전환', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);

				}

				// 되돌리기 추적을 위해 이전 내용 남김
				$query = "UPDATE TBL_STOCK 
							 SET PREV_STOCK_NO = '$stock_no', BB_NO = '$BB_NO'
						   WHERE STOCK_NO = LAST_INSERT_ID();
						 ";
				mysql_query($query,$db);
				

			}

			if($input_bqty <> 0) { 
				
				if($input_bqty > 0) { 
					$cp_no = $IN_CP_NO;
					$in_qty = 0; 
					$in_bqty = $input_bqty; 
					$in_fqty = 0; 
					$out_qty = 0;
					$out_bqty = 0;
					$out_tqty = 0;
					$price = $IN_PRICE;
					$out_price = 0;
					$in_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$out_date = '';
					$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$close_tf = 'N';
					$memo = $MEMO;

					$result = insertStock($db, 'IN', 'BST03', $cp_no, '', $GOODS_NO, $IN_LOC, '가입고전환', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);
				
				} else { 

					$cp_no = $IN_CP_NO;
					$in_qty = 0; 
					$in_bqty = 0; 
					$in_fqty = 0; 
					$out_qty = 0;
					$out_bqty = abs($input_bqty);
					$out_tqty = 0;
					$price = 0;
					$out_price = $IN_PRICE;
					$in_date = '';
					$out_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$pay_date = date("Y-m-d H:i:s",strtotime("0 month"));
					$close_tf = 'N';
					$memo = $MEMO;

					$result = insertStock($db, 'OUT', 'NOUT01', '', $cp_no, $GOODS_NO, $IN_LOC, '가출고전환', $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO, $close_tf, $reg_adm, $memo);

				}

				// 되돌리기 추적을 위해 이전 내용 남김
				$query = "UPDATE TBL_STOCK 
							 SET PREV_STOCK_NO = '$stock_no', BB_NO = '$BB_NO'
						   WHERE STOCK_NO = LAST_INSERT_ID();
						 ";
				mysql_query($query,$db);
			}
			
			syncGoodsStock($db, $GOODS_NO);

		}
		
		return true;
		/*
		if(!$result) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		*/
	}

	function listStockGoods($db, $goods_cate, $in_cp_no, $out_cp_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);
		$logical_num = ($total_cnt - $offset) + 1 ;
		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);


		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, B.DELIVERY_CNT_IN_BOX,
										 IFNULL(SUM(IN_QTY),0) AS S_IN_QTY, 
										 IFNULL(SUM(IN_BQTY),0) AS S_IN_BQTY, 
										 IFNULL(SUM(IN_FQTY),0) AS S_IN_FQTY, 
										 IFNULL(SUM(OUT_QTY),0) AS S_OUT_QTY, 
										 IFNULL(SUM(OUT_BQTY),0) AS S_OUT_BQTY, 
										 IFNULL(SUM(OUT_TQTY),0) AS S_OUT_TQTY,
										 B.STOCK_CNT,
										 B.BSTOCK_CNT,
										 B.FSTOCK_CNT,
										 B.TSTOCK_CNT,
										 B.MSTOCK_CNT
								FROM TBL_STOCK A RIGHT JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO AND A.DEL_TF ='N' AND B.DEL_TF ='N' AND A.CLOSE_TF = 'N'
							 WHERE B.DEL_TF ='N' ";
		
		//2016-02-03 최소재고 세팅 되어있는 상품만 재고관리
		//$query .= " AND B.MSTOCK_CNT <> 0  ";

		if ($goods_cate <> "") {
			$query .= " AND B.GOODS_CATE LIKE '".$goods_cate."%' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE ";

		if ($order_field == "") 
			$order_field = "B.GOODS_NAME";

		if ($order_str == "") 
			$order_str = "ASC";

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

	function totalCntStockGoods($db, $goods_cate, $in_cp_no, $out_cp_no, $search_field, $search_str) {

		$query = "SELECT COUNT(DISTINCT B.GOODS_NO)
								FROM TBL_STOCK A RIGHT JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO AND A.DEL_TF ='N' AND B.DEL_TF ='N' AND A.CLOSE_TF = 'N' AND B.DEL_TF ='N' 
								WHERE 1 = 1 ";
		
		//2016-02-03 최소재고 세팅 되어있는 것만 재고관리
		//$query .= " AND B.MSTOCK_CNT <> 0 ";

		if ($goods_cate <> "") {
			$query .= " AND B.GOODS_CATE LIKE '".$goods_cate."%' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//$query .= " GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE ";


		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function fixStockGoods($db, $goods_no, $cal_qty, $cal_fqty, $cal_bqty, $cal_tqty) {

		$query = "UPDATE TBL_GOODS SET STOCK_CNT = '$cal_qty', FSTOCK_CNT = '$cal_fqty', BSTOCK_CNT = '$cal_bqty', TSTOCK_CNT = '$cal_tqty' WHERE GOODS_NO = '$goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function getDetailGoodsStock($db, $goods_no) {


		$query = "SELECT B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, A.IN_LOC,
										 IFNULL(SUM(IN_QTY),0) AS S_IN_QTY, 
										 IFNULL(SUM(IN_BQTY),0) AS S_IN_BQTY, 
										 IFNULL(SUM(IN_FQTY),0) AS S_IN_FQTY, 
										 IFNULL(SUM(OUT_QTY),0) AS S_OUT_QTY, 
										 IFNULL(SUM(OUT_BQTY),0) AS S_OUT_BQTY, 
										 IFNULL(SUM(OUT_TQTY),0) AS S_OUT_TQTY,
										 B.STOCK_CNT,
										 B.BSTOCK_CNT,
										 B.FSTOCK_CNT,
										 B.TSTOCK_CNT
								FROM TBL_STOCK A RIGHT JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO AND A.DEL_TF ='N' AND B.DEL_TF ='N' AND A.CLOSE_TF = 'N'
							 WHERE B.DEL_TF ='N' AND B.GOODS_CATE NOT LIKE '02%' AND B.GOODS_NO = '$goods_no'
							 GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, A.IN_LOC 
							 ORDER BY IN_LOC ASC ";

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

	function getGoodsStock($db, $goods_no) {


		$query = "SELECT B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE,
										 IFNULL(SUM(IN_QTY),0) AS S_IN_QTY, 
										 IFNULL(SUM(IN_BQTY),0) AS S_IN_BQTY, 
										 IFNULL(SUM(IN_FQTY),0) AS S_IN_FQTY, 
										 IFNULL(SUM(OUT_QTY),0) AS S_OUT_QTY, 
										 IFNULL(SUM(OUT_BQTY),0) AS S_OUT_BQTY, 
										 IFNULL(SUM(OUT_TQTY),0) AS S_OUT_TQTY,
										 B.STOCK_CNT,
										 B.BSTOCK_CNT,
										 B.FSTOCK_CNT
								FROM TBL_STOCK A RIGHT JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO AND A.DEL_TF ='N' AND B.DEL_TF ='N' AND A.CLOSE_TF = 'N'
							 WHERE B.DEL_TF ='N' AND B.GOODS_CATE NOT LIKE '02%' AND B.GOODS_NO = '$goods_no'
							 GROUP BY B.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE ";

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


	function insertTempStock($db, $file_nm, $stock_type, $goods_code, $goods_name,  $str_stock_code, $cp_code, $qty, $price, $in_loc, $in_loc_ext, $in_date, $pay_date, $reserve_no) {

/*
CREATE TABLE IF NOT EXISTS TBL_TEMP_STOCK (
  TEMP_NO varchar(30) NOT NULL,
  STOCK_NO int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '일련번호',
  STOCK_TYPE varchar(10) NOT NULL,
  STOCK_CODE varchar(10) NOT NULL,
  STOCK_CODE_NAME varchar(20) NOT NULL,
  GOODS_CODE varchar(30) NOT NULL,
  GOODS_NAME varchar(130) NOT NULL,
  GOODS_NO int(11) unsigned NOT NULL DEFAULT '0' COMMENT '상품 일련번호',
  CP_NO int(10) unsigned NOT NULL,
  CP_NAME varchar(130) NOT NULL,
  IN_LOC varchar(10) NOT NULL DEFAULT '',
  IN_LOC_NAME varchar(20) NOT NULL DEFAULT '',
  IN_LOC_EXT varchar(200) NOT NULL DEFAULT '',
  QTY int(11) NOT NULL DEFAULT '0',
  PRICE int(11) NOT NULL DEFAULT '0' COMMENT '입출고가',
  IN_DATE datetime DEFAULT NULL,
  PAY_DATE datetime DEFAULT NULL,
 PRIMARY KEY (STOCK_NO)
)
*/
		$query="INSERT INTO TBL_TEMP_STOCK  (TEMP_NO, STOCK_TYPE, STOCK_CODE, GOODS_CODE,
																	 GOODS_NAME, CP_CODE, IN_LOC, IN_LOC_EXT,
																	 QTY, PRICE, IN_DATE, PAY_DATE, RESERVE_NO) 
												values ('$file_nm', '$stock_type', '$str_stock_code', '$goods_code', 
																'$goods_name', '$cp_code', '$in_loc', '$in_loc_ext', 
																'$qty', '$price', '$in_date', '$pay_date', '$reserve_no'); ";
		
		//echo $query;

		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function listTempStock($db, $file_nm) {

		$query = "SELECT TEMP_NO, STOCK_NO, STOCK_TYPE, STOCK_CODE, GOODS_CODE, GOODS_NAME, CP_CODE, IN_LOC, IN_LOC_EXT, QTY, 
										 PRICE, IN_DATE, PAY_DATE, RESERVE_NO
								FROM TBL_TEMP_STOCK  WHERE TEMP_NO = '$file_nm' ORDER BY STOCK_NO ASC ";

		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getStockGoodsNoAsName($db, $goods_name, $stock_no) {
		
		$val = "";
		
		if ($goods_name) { 

			$query = "SELECT GOODS_CODE, GOODS_NO, GOODS_NAME, COUNT(GOODS_CODE) AS CNT
										FROM TBL_GOODS 
									 WHERE DEL_TF = 'N' AND GOODS_NAME = '$goods_name' ";
			
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			$GOODS_CODE					= $rows[0];
			$GOODS_NO						= $rows[1];
			$GOODS_NAME					= $rows[2];
			$CNT								= $rows[3];

			if ($CNT == "1") {
			
				if ($stock_no) {
					$query = "UPDATE TBL_TEMP_STOCK SET GOODS_CODE = '$GOODS_CODE', GOODS_NO = '$GOODS_NO', GOODS_NAME = '$GOODS_NAME'
										 WHERE STOCK_NO = '$stock_no' ";

					mysql_query($query,$db);
				}
			
				$val = $GOODS_NO;
			} 
		}
		
		return $val;		

	}

	function getStockGoodsNoAsCode($db, $goods_code, $goods_name, $stock_no) {
		
		$val = "";

		$query = "SELECT GOODS_CODE, GOODS_NO, GOODS_NAME, COUNT(GOODS_CODE) AS CNT
									FROM TBL_GOODS 
								 WHERE DEL_TF = 'N' AND GOODS_CODE = '$goods_code' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_CODE					= $rows[0];
		$GOODS_NO						= $rows[1];
		$GOODS_NAME					= $rows[2];
		$CNT								= $rows[3];

		if ($CNT == "1") {
			
			if ($stock_no) {

				if ($goods_name) {
					$query = "UPDATE TBL_TEMP_STOCK SET GOODS_CODE = '$GOODS_CODE', GOODS_NO = '$GOODS_NO'
										 WHERE STOCK_NO = '$stock_no' ";
				} else {
					$query = "UPDATE TBL_TEMP_STOCK SET GOODS_CODE = '$GOODS_CODE', GOODS_NO = '$GOODS_NO', GOODS_NAME = '$GOODS_NAME'
										 WHERE STOCK_NO = '$stock_no' ";
				}
				mysql_query($query,$db);
			}
			
			$val = $GOODS_NO;
		} 
		
		return $val;

	}

	function getStockCompayChkAsCode ($db, $s_adm_cp_type, $cp_code, $stock_no) {

		if ($s_adm_cp_type ="운영") {

			$query="SELECT CP_NO, CP_NM, COUNT(*) AS CNT FROM TBL_COMPANY 
							 WHERE CP_CODE	= '$cp_code'
								 AND DEL_TF = 'N' ";
			
		} else {

			$query="SELECT CP_NO, CP_NM, COUNT(*) AS CNT 
								FROM TBL_COMPANY C, TBL_ADMIN_INFO A
							 WHERE C.CP_NO = A.COM_CODE
								 AND A.ADM_ID	= '$cp_code'
								 AND C.DEL_TF = 'N' ";

		}

		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$CP_NO							= $rows[0];
		$CP_NAME						= $rows[1];
		$CNT								= $rows[2];

		if ($CNT == "1") {
			
			if ($stock_no) {

				$query = "UPDATE TBL_TEMP_STOCK SET CP_NO = '$CP_NO', CP_NAME = '$CP_NAME'
									 WHERE STOCK_NO = '$stock_no' ";

				mysql_query($query,$db);
			}
			
			$val = $CP_NO;
		} 

		return $val;
	}

	function selectTempStock($db, $file_nm, $stock_no) {

		$query = "SELECT TEMP_NO, STOCK_NO, STOCK_TYPE, STOCK_CODE, GOODS_NO, GOODS_CODE, GOODS_NAME, CP_NO, CP_CODE, CP_NAME, IN_LOC, IN_LOC_EXT, QTY, 
										 PRICE, IN_DATE, PAY_DATE 
								FROM TBL_TEMP_STOCK  WHERE TEMP_NO = '$file_nm' AND STOCK_NO = '$stock_no' ";
			
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


	function updateTempStock($db, $stock_type, $stock_code_name, $goods_no, $goods_code, $goods_name, $cp_type, $cp_code, $txt_cp_type, $in_loc_name, $in_loc_ext, $qty, $price, $in_date, $pay_date, $temp_no, $stock_no) {

		$query = "UPDATE TBL_TEMP_STOCK SET 
										STOCK_TYPE = '$stock_type',
										STOCK_CODE = '$stock_code_name',
										GOODS_NO	 = '$goods_no',
										GOODS_CODE = '$goods_code',
										GOODS_NAME = '$goods_name',
										CP_NO			 = '$cp_type',
										CP_CODE		 = '$cp_code',
										CP_NAME		 = '$txt_cp_type',
										IN_LOC		 = '$in_loc_name',
										IN_LOC_EXT = '$in_loc_ext',
										QTY				 = '$qty',
										PRICE			 = '$price',
										IN_DATE		 = '$in_date',
										PAY_DATE	 = '$pay_date'
									 WHERE TEMP_NO = '$temp_no' AND STOCK_NO = '$stock_no'";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		
	}
	
	function deleteTempStock($db, $temp_no, $stock_no) {

		$query = "DELETE FROM TBL_TEMP_STOCK 
									 WHERE TEMP_NO = '$temp_no' AND STOCK_NO = '$stock_no'";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	// (사용안함) 입고,출고 일괄등록 
	/*
	function insertTempToRealStock($db, $temp_no, $str_stock_no, $adm_no) {

		$query="SELECT TEMP_NO, STOCK_NO, STOCK_TYPE, STOCK_CODE, GOODS_NO, GOODS_CODE, GOODS_NAME, CP_NO, 
									 CP_CODE, CP_NAME, IN_LOC, IN_LOC_EXT, QTY, PRICE, IN_DATE, PAY_DATE, RESERVE_NO, ORDER_GOODS_NO, RGN_NO
				  FROM TBL_TEMP_STOCK
				 WHERE TEMP_NO = '$temp_no' AND STOCK_NO IN ($str_stock_no) ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		

		if (sizeof($record) > 0) {

			for ($j = 0 ; $j < sizeof($record); $j++) {
				
				$stock_type					= trim($record[$j]["STOCK_TYPE"]);
				$stock_code_name			= trim($record[$j]["STOCK_CODE"]);
				$cp_no						= trim($record[$j]["CP_NO"]);
				$cp_code					= trim($record[$j]["CP_CODE"]);
				$goods_no					= trim($record[$j]["GOODS_NO"]);
				$in_loc_code_name			= trim($record[$j]["IN_LOC"]);
				$in_loc_ext					= trim($record[$j]["IN_LOC_EXT"]);
				$qty						= trim($record[$j]["QTY"]);
				$price						= trim($record[$j]["PRICE"]);
				$in_date					= trim($record[$j]["IN_DATE"]);
				$pay_date					= trim($record[$j]["PAY_DATE"]);
				$reserve_no					= trim($record[$j]["RESERVE_NO"]);
				$order_goods_no				= trim($record[$j]["ORDER_GOODS_NO"]);
				$rgn_no						= trim($record[$j]["RGN_NO"]);
				
				if ($stock_type == "IN") {

					$stock_code = getDcodeCode($db, "IN_ST", $stock_code_name);

					if (left($stock_code,1) == "N") {
						$in_qty		= $qty;
						$in_bqty	= 0;
						$in_fqty	= 0;
					} if (left($stock_code,1) == "B") {
						$in_qty		= 0;
						$in_bqty	= $qty;
						$in_fqty	= 0;
					} if (left($stock_code,1) == "F") {
							$in_qty		= 0;
						$in_bqty	= 0;
						$in_fqty	= $qty;
					}
				}

				if ($stock_type == "OUT") {
					$stock_code = getDcodeCode($db, "OUT_ST", $stock_code_name);

					if (left($stock_code,1) == "N") {
						$out_qty	= $qty;
						$out_bqty	= 0;
						$out_tqty	= 0;
					} if (left($stock_code,1) == "B") {
						$out_qty	= 0;
						$out_bqty	= $qty;
						$out_tqty	= 0;
					} if (left($stock_code,1) == "T") {
						$out_qty	= 0;
						$out_bqty	= 0;
						$out_tqty	= $qty;
					}

				}

				$arr_rs_goods = selectGoods($db, $goods_no);

				$rs_goods_name			= trim($arr_rs_goods[0]["GOODS_NAME"]); 
				$rs_price				= trim($arr_rs_goods[0]["BUY_PRICE"]); 
				$rs_cp_no				= trim($arr_rs_goods[0]["CATE_03"]); 
				
				if ($stock_type == "IN") {
					if ($cp_no == "0" || $cp_no == "") {
						$cp_no = $rs_cp_no;
					}
				}

				if ($stock_type == "OUT") {
					$out_cp_no	= $cp_no;
					$out_price	= $price;
					$out_date	= $in_date;
					
					$cp_no		= "";
					$price		= 0;
					$in_date	= "";

				}


				$in_loc = getDcodeCode($db, "LOC", $in_loc_code_name);

				$goods_no				= trim($record[$j]["GOODS_NO"]);

				if ($price == "") {
					$price = $rs_price;
				}
				
				$close_tf = "N";
				$memo	  = "파일등록";

				$result = insertStock($db, $stock_type, $stock_code, $cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $adm_no, $memo);
			
			}
		}

		if(!$result) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	*/
	

	function deleteTempToRealStock($db, $temp_no, $str_stock_no) {

		$query=" DELETE FROM TBL_TEMP_STOCK WHERE TEMP_NO = '$temp_no' AND STOCK_NO IN ($str_stock_no) ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function setBaseStock($db, $goods_no, $goods_cp_no, $goods_buy_price, $qty, $bqty, $memo, $adm_no) {
		
		$this_date = date("Y-m-d",strtotime("0 month"));

		// 기존 데이터에 대한 close 정보 update
		//$query = "UPDATE TBL_STOCK SET CLOSE_TF = 'Y', CLOSE_DATE = '$this_date' WHERE GOODS_NO = '$goods_no' AND CLOSE_DATE IS NULL ";
		$query = "UPDATE TBL_STOCK 
					 SET CLOSE_TF = 'Y', CLOSE_DATE = '$this_date' 
				   WHERE GOODS_NO = '$goods_no' AND CLOSE_DATE IS NULL AND STOCK_CODE NOT LIKE 'T%' AND STOCK_CODE NOT LIKE 'F%'; ";
		mysql_query($query,$db);

		if($memo == "")
			$memo = "기초재고등록";

		// 정상 입고 등록
		$stock_type = "IN";
		$stock_code	= "NST98";
		$in_cp_no		= $goods_cp_no;
		$in_loc			= "LOCA";
		$in_qty			= $qty;
		$in_bqty		= 0;
		$in_fqty		= 0;
		$in_price		= $goods_buy_price;
		$in_date		= $this_date;
		$close_tf		= "N";
		$pay_date		= "";
		//$memo			= "기초재고등록";

		$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);

		// 불량 입고 등록
		$stock_type = "IN";
		$stock_code	= "BST98";
		$in_cp_no		= $goods_cp_no;
		$in_loc			= "LOCA";
		$in_qty			= 0;
		$in_bqty		= $bqty;
		$in_fqty		= 0;
		$in_price		= $goods_buy_price;
		$in_date		= $this_date;
		$close_tf		= "N";
		$pay_date		= "";

		$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);


		// 상품 정보 update
		//$query = "UPDATE TBL_GOODS SET STOCK_CNT = '$qty', FSTOCK_CNT = '0', BSTOCK_CNT = '$bqty' ,TSTOCK_CNT = '0' WHERE GOODS_NO = '$goods_no' ";
		$query = "UPDATE TBL_GOODS SET STOCK_CNT = '$qty',  BSTOCK_CNT = '$bqty' WHERE GOODS_NO = '$goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function fixGoodsStock($db, $goods_no, $fix_qty, $org_qty, $fix_bqty, $org_bqty, $adm_no) {
		
		$this_date = date("Y-m-d h:i:s",strtotime("0 month"));

		// 운영인 회사 번호 조회
		$query = "SELECT CP_NO FROM TBL_COMPANY WHERE CP_TYPE = '운영' ORDER BY CP_NO DESC LIMIT 1 ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$CP_NO  = $rows[0];

		// 재고 정정 입니다.
		// 정상재고 수정 시
		$temp_qty = $org_qty - $fix_qty;
		
		if ($temp_qty > 0) {

			// 기존 재고 수량 차감
			// 정상 재고 출고 후 불량재고 입고
			// 정상 재고 출고 등록
			$stock_type = "OUT";
			$stock_code	= "NOUT97";
			$out_cp_no	= $CP_NO;
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty			= $temp_qty;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$out_price		= 0;
			$out_date		= $this_date;
			$close_tf		= "N";
			$memo				= "재고정정";
			
			// 정상재고 출고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no,$memo);

			// 불랼 재고 입고 등록
			$stock_type = "IN";
			$stock_code	= "BST97";
			$in_cp_no		= $CP_NO;
			$out_cp_no	= "";
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= $temp_qty;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$in_price		= 0;
			$in_date		= $this_date;
			$pay_date		= "";
			$close_tf		= "N";
			$memo				= "재고정정";
			
			// 불량재고 입고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no,$memo);

		} 
		if ($temp_qty < 0) {
			// 기존 재고 수량 증가
			// 불량 재고 출고 후 정상재고 입고
			$temp_bqty = -$temp_qty;

			// 불량 재고 출고 등록
			$stock_type = "OUT";
			$stock_code	= "BOUT97";
			$out_cp_no	= $CP_NO;
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= $temp_bqty;
			$out_tqty		= 0;
			$out_price		= 0;
			$out_date		= $this_date;
			$close_tf		= "N";
			$memo				= "재고정정";
			
			// 불량재고 출고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no,$memo);

			// 정상 재고 입고 등록
			$stock_type = "IN";
			$stock_code	= "NST97";
			$out_cp_no	= "";
			$in_cp_no		= $CP_NO;
			$in_loc			= "LOCA";
			$in_qty			= $temp_bqty;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$in_price		= 0;
			$in_date		= $this_date;
			$pay_date		= "";
			$close_tf		= "N";
			$memo				= "재고정정";
			
			// 정상재고 입고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no,$memo);
		}
		return true;
	}

	function getStockAsset($db) {
		
		$query = "SELECT (SUM(IN_QTY * IN_PRICE) - SUM(OUT_QTY * OUT_PRICE)) + (SUM(IN_BQTY * IN_PRICE) - SUM(OUT_BQTY * OUT_PRICE)) AS ALL_ASSET
								FROM TBL_STOCK WHERE CLOSE_TF = 'N' AND DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$ALL_ASSET  = $rows[0];
		
		return $ALL_ASSET;

	}



	function listFixStockGoods($db, $file_nm) {

		$query = "SELECT A.TEMP_NO, B.GOODS_NO, B.GOODS_CODE, B.GOODS_NAME, A.STOCK_CNT, A.GOODS_STOCK_CNT, A.BSTOCK_CNT, A.GOODS_BSTOCK_CNT, A.MEMO
								FROM TBL_TEMP_FIX_GOODS_STOCK A, TBL_GOODS B 
							 WHERE A.GOODS_NO = B.GOODS_NO 
								 AND A.TEMP_FILE = '$file_nm' 
								 AND (A.STOCK_CNT <> A.GOODS_STOCK_CNT OR A.BSTOCK_CNT <> A.GOODS_BSTOCK_CNT)
								 AND A.IS_INSERTED = 'N'
							 ORDER BY B.GOODS_NAME ASC ";
		
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

	function updateFixStockGoods($db, $temp_no, $adm_no) {

		$this_date = date("Y-m-d H:i:s",strtotime("0 month"));

		// 운영인 회사 번호 조회
		$query = "SELECT CP_NO FROM TBL_COMPANY WHERE CP_TYPE = '운영' ORDER BY CP_NO DESC LIMIT 1 ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$CP_NO  = $rows[0];

		$query = "SELECT GOODS_NO, STOCK_CNT, GOODS_STOCK_CNT, BSTOCK_CNT, GOODS_BSTOCK_CNT, MEMO
								FROM TBL_TEMP_FIX_GOODS_STOCK
							 WHERE TEMP_NO = '$temp_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NO				= $rows[0];
		$STOCK_CNT				= $rows[1];
		$GOODS_STOCK_CNT		= $rows[2];
		$BSTOCK_CNT				= $rows[3];
		$GOODS_BSTOCK_CNT		= $rows[4];
		$MEMO					= $rows[5];

		if($MEMO == "")
			$MEMO = "재고실사";


		//echo $GOODS_NO ."-". $STOCK_CNT ."-". $GOODS_STOCK_CNT  ."-". $BSTOCK_CNT ."-". $GOODS_BSTOCK_CNT."<br>";

		// 정상재고 처리
		if ($STOCK_CNT > $GOODS_STOCK_CNT) {
			// 실사 재고가 많을 경우
			$temp_qty = $STOCK_CNT - $GOODS_STOCK_CNT;
			// $temp_qty 수량 만큼 입고

			// 기존 재고 수량 차감
			// 정상 재고 출고 후 불량재고 입고
			// 정상 재고 출고 등록
			$stock_type = "IN";
			$stock_code	= "NST96";
			$in_cp_no		= $CP_NO;
			$out_cp_no	= "";
			$in_loc			= "LOCA";
			$in_qty			= $temp_qty;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$in_price		= 0;
			$out_price		= 0;
			$in_date		= $this_date;
			$out_date		= "";
			$close_tf		= "N";
			//$memo			= "재고실사";
			$memo			= $MEMO;
			
			// 정상재고 입고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);

		}

		if ($STOCK_CNT < $GOODS_STOCK_CNT) {
			// 상품 재고가 많을 경우
			$temp_qty = $GOODS_STOCK_CNT - $STOCK_CNT;

			// $temp_qty 수량 만큼 출고
			// 정상 재고 출고 등록
			$stock_type = "OUT";
			$stock_code	= "NOUT96";
			$in_cp_no		= "";
			$out_cp_no	= $CP_NO;
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty		= $temp_qty;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$in_price		= 0;
			$out_price	= 0;
			$in_date		=	"";
			$out_date		= $this_date;
			$close_tf		= "N";
			$memo			= $MEMO;
			
			// 정상재고 출고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);

		}

		// 불량재고 처리
		if ($BSTOCK_CNT > $GOODS_BSTOCK_CNT) {
			// 실사 재고가 많을 경우
			$temp_qty = $BSTOCK_CNT - $GOODS_BSTOCK_CNT;
			// $temp_qty 수량 만큼 입고

			$stock_type = "IN";
			$stock_code	= "BST96";
			$in_cp_no		= $CP_NO;
			$out_cp_no	= "";
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= $temp_qty;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= 0;
			$out_tqty		= 0;
			$in_price		= 0;
			$out_price		= 0;
			$in_date		= $this_date;
			$out_date		= "";
			$close_tf		= "N";
			//$memo				= "재고실사";
			$memo			= $MEMO;
			
			// 불량재고 입고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);



		}

		if ($BSTOCK_CNT < $GOODS_BSTOCK_CNT) {
			// 상품 재고가 많을 경우
			$temp_qty = $GOODS_BSTOCK_CNT - $BSTOCK_CNT;

			// $temp_qty 수량 만큼 출고
			// 정상 재고 출고 등록
			$stock_type = "OUT";
			$stock_code	= "BOUT96";
			$in_cp_no		= "";
			$out_cp_no	= $CP_NO;
			$in_loc			= "LOCA";
			$in_qty			= 0;
			$in_bqty		= 0;
			$in_fqty		= 0;
			$out_qty		= 0;
			$out_bqty		= $temp_qty;
			$out_tqty		= 0;
			$in_price		= 0;
			$out_price		= 0;
			$in_date		=	"";
			$out_date		= $this_date;
			$close_tf		= "N";
			//$memo				= "재고실사";
			$memo			= $MEMO;
			
			// 불량재고 출고
			$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $adm_no, $memo);

		}

		$query = "UPDATE TBL_TEMP_FIX_GOODS_STOCK 
					 SET IS_INSERTED = 'Y'
				   WHERE TEMP_NO = '$temp_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		// 불량재고 처리

	}

	function listStatInStockGoods($db, $start_date, $end_date, $in_cp_no, $search_field, $search_str, $order_str) {

	/*

	SELECT A.INOUT_DATE, A.GOODS_NO, B.GOODS_CODE, B.GOODS_NAME, B.GOODS_SUB_NAME, A.SUM_IN_QTY, A.SUM_OUT_QTY, A.SUM_IN_BQTY, A.SUM_OUT_BQTY
FROM
(
 SELECT DATE_FORMAT( CASE WHEN IN_DATE = '0000-00-00 00:00:00' THEN OUT_DATE WHEN OUT_DATE = '0000-00-00 00:00:00' THEN IN_DATE END ,  '%Y-%m-%d' ) AS INOUT_DATE, GOODS_NO, SUM(IN_QTY) AS SUM_IN_QTY, SUM(OUT_QTY) AS SUM_OUT_QTY,SUM(IN_BQTY) AS SUM_IN_BQTY, SUM(OUT_BQTY) AS SUM_OUT_BQTY 
FROM TBL_STOCK 
WHERE CLOSE_TF= 'N' AND DEL_TF='N' 
AND CASE WHEN IN_DATE = '0000-00-00 00:00:00' THEN OUT_DATE WHEN OUT_DATE = '0000-00-00 00:00:00' THEN IN_DATE END > '2015-11-03'
AND CASE WHEN IN_DATE = '0000-00-00 00:00:00' THEN OUT_DATE WHEN OUT_DATE = '0000-00-00 00:00:00' THEN IN_DATE END <= '2015-11-03 23:59:59'

GROUP BY  DATE_FORMAT( CASE WHEN IN_DATE = '0000-00-00 00:00:00' THEN OUT_DATE WHEN OUT_DATE = '0000-00-00 00:00:00' THEN IN_DATE END ,  '%Y-%m-%d' ) , GOODS_NO
) A
JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO
ORDER BY A.INOUT_DATE DESC, B.GOODS_NAME, B.GOODS_SUB_NAME


	*/

		$query = "		
					SELECT A.IN_DATE, A.IN_CP_NO, A.GOODS_NO, B.GOODS_CODE, B.GOODS_NAME, B.GOODS_SUB_NAME, A.SUM_IN_QTY, A.SUM_IN_BQTY, A.SUM_IN_FQTY
					FROM (

					SELECT DATE_FORMAT( IN_DATE,  '%Y-%m-%d' ) AS IN_DATE, GOODS_NO, IN_CP_NO, SUM( IN_QTY ) AS SUM_IN_QTY, SUM( IN_BQTY ) AS SUM_IN_BQTY, SUM( IN_FQTY ) AS SUM_IN_FQTY
					FROM TBL_STOCK
					WHERE CLOSE_TF =  'N'
					AND DEL_TF =  'N'
					AND STOCK_TYPE =  'IN'
				 ";

		if ($start_date <> "") {
			$query .= " AND IN_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND IN_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($in_cp_no <> "") {
			$query .= " AND IN_CP_NO = '".$in_cp_no."' ";
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " 
					GROUP BY DATE_FORMAT( IN_DATE,  '%Y-%m-%d' ), GOODS_NO, IN_CP_NO
					)A
					JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO ";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " WHERE (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')";
			
			} else {
				$query .= " WHERE (".$search_field." like '%".$search_str."%')";
			}
		}

		$query .= "
					ORDER BY A.IN_DATE $order_str , B.GOODS_NAME, B.GOODS_SUB_NAME
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

	function listStatOutStockGoods($db, $start_date, $end_date, $out_cp_no, $search_field, $search_str, $order_str) {

		$query = "		
					SELECT A.OUT_DATE, A.OUT_CP_NO, A.GOODS_NO, B.GOODS_CODE, B.GOODS_NAME, B.GOODS_SUB_NAME, A.SUM_OUT_QTY, A.SUM_OUT_BQTY, A.SUM_OUT_TQTY
					FROM (

					SELECT DATE_FORMAT( OUT_DATE,  '%Y-%m-%d' ) AS OUT_DATE, GOODS_NO, OUT_CP_NO, SUM( OUT_QTY ) AS SUM_OUT_QTY, SUM( OUT_BQTY ) AS SUM_OUT_BQTY, SUM( OUT_TQTY ) AS SUM_OUT_TQTY
					FROM TBL_STOCK
					WHERE CLOSE_TF =  'N'
					AND DEL_TF =  'N'
					AND STOCK_TYPE =  'OUT'
				 ";

		if ($start_date <> "") {
			$query .= " AND OUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND OUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($out_cp_no <> "") {
			$query .= " AND OUT_CP_NO = '".$out_cp_no."' ";
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " 
					GROUP BY DATE_FORMAT( OUT_DATE,  '%Y-%m-%d' ), GOODS_NO, OUT_CP_NO
					)A
					JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO ";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " WHERE (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')";
			
			} else {
				$query .= " WHERE (".$search_field." like '%".$search_str."%')";
			}
		}

		$query .= "
					ORDER BY A.OUT_DATE $order_str , B.GOODS_NAME, B.GOODS_SUB_NAME
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
	
	// 발주관리 리스트
	function totalCntGoodsRequest($db, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str) {

		$con_delivery_tf	=	$filter['con_delivery_tf'];
		$con_to_here		=	$filter['con_to_here'];
		$con_cancel_tf		=	$filter['con_cancel_tf'];
		$con_confirm_tf		=	$filter['con_confirm_tf'];
		$con_changed_tf		=	$filter['con_changed_tf'];
		$con_receive_tf		=	$filter['con_receive_tf'];
		$con_wrap_tf		=	$filter['con_wrap_tf'];
		$con_sticker_tf		=	$filter['con_sticker_tf'];
		$chk_after_confirm  =	$filter['chk_after_confirm'];
		$con_payment		=	$filter['con_payment'];

		// echo"db_con_payment : ".$con_payment."<br>";

		$query = "
		
					SELECT COUNT(*) 
					  FROM (
		
							SELECT DISTINCT GR.REQ_NO
								FROM TBL_GOODS_REQUEST GR
								JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
								JOIN TBL_GOODS G ON G.GOODS_NO = GRG.GOODS_NO
								JOIN TBL_COMPANY C ON C.CP_NO= GR.BUY_CP_NO
								WHERE GR.DEL_TF = 'N' AND GRG.DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND GR.REQ_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GR.REQ_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND GR.BUY_CP_NO = '".$cp_type."' ";
		}

		
		if ($con_delivery_tf <> "") {
			$query .= " AND GR.IS_SENT = '".$con_delivery_tf."' ";
		}

		if ($con_to_here <> "") {
			$query .= " AND GRG.TO_HERE = '".$con_to_here."' ";
		}

		if ($con_cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$con_cancel_tf."' ";
		}

		if ($con_confirm_tf <> "") {
			$query .= " AND GRG.CONFIRM_TF = '".$con_confirm_tf."' ";
		}

		if ($con_changed_tf <> "") {
			$query .= " AND GRG.CHANGED_TF = '".$con_changed_tf."' ";
		}

		if ($con_receive_tf <> "") {
			if ($con_receive_tf == "Y") 
				$query .= " AND GRG.RECEIVE_QTY > 0 ";
			else
				$query .= " AND GRG.RECEIVE_QTY = 0 ";
		}

		if ($con_wrap_tf <> "") {
			if($con_wrap_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%포장지 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%포장지 :%' ";
		}

		if ($con_sticker_tf <> "") {
			if($con_sticker_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%스티커 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%스티커 :%' ";
		}

		if ($chk_after_confirm <> "") {
			$query .= " AND GRG.CONFIRM_DATE < GRG.RECEIVE_DATE ";
		}
		if($con_payment <> ""){
			// echo "test<br>";
			$query .= " AND C.AD_TYPE = '".$con_payment."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GR.BUY_CP_NM LIKE '%".$search_str."%' OR GR.BUY_MANAGER_NM LIKE '%".$search_str."%' OR GR.BUY_CP_PHONE LIKE '%".$search_str."%' OR GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR GRG.MEMO2 LIKE '%".$search_str."%' OR GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO2") {
				$query .= " AND (GRG.MEMO2 LIKE '%".$search_str."%') ";
			} else if ($search_field == "REQ_GOODS_NO") {
				$query .= " AND (GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND (GRG.ORDER_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND (GRG.RESERVE_NO = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ) AA";

	    // echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	//발주서 관리 리스트 페이지
	function listGoodsRequest($db, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$con_delivery_tf	=	$filter['con_delivery_tf'];
		$con_to_here		=	$filter['con_to_here'];
		$con_cancel_tf		=	$filter['con_cancel_tf'];
		$con_confirm_tf		=	$filter['con_confirm_tf'];
		$con_changed_tf		=	$filter['con_changed_tf'];
		$con_receive_tf		=	$filter['con_receive_tf'];
		$con_wrap_tf		=	$filter['con_wrap_tf'];
		$con_sticker_tf		=	$filter['con_sticker_tf'];
		$chk_after_confirm  =	$filter['chk_after_confirm'];
		$con_payment		=	$filter['con_payment'];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		//@rownum:= @rownum - 1  as rn,
		$query = "SELECT DISTINCT GR.REQ_NO, GR.GROUP_NO, GR.BUY_CP_NO, GR.BUY_CP_NM, GR.BUY_MANAGER_NM, GR.BUY_CP_PHONE, GR.TOTAL_REQ_QTY, GR.TOTAL_BUY_TOTAL_PRICE, GR.DELIVERY_TYPE, GR.REQ_DATE, GR.REG_DATE, GR.IS_SENT, GR.SENT_DATE, GR.CHECK_YN
								FROM TBL_GOODS_REQUEST GR
								JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
								JOIN TBL_GOODS G ON G.GOODS_NO = GRG.GOODS_NO
								JOIN TBL_COMPANY C ON GR.BUY_CP_NO=C.CP_NO
							   WHERE GR.DEL_TF = 'N' AND GRG.DEL_TF = 'N'  ";

		if ($start_date <> "") {
			$query .= " AND GR.REQ_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GR.REQ_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND GR.BUY_CP_NO = '".$cp_type."' ";
		}

		
		if ($con_delivery_tf <> "") {
			$query .= " AND GR.IS_SENT = '".$con_delivery_tf."' ";
		}

		if ($con_to_here <> "") {
			$query .= " AND GRG.TO_HERE = '".$con_to_here."' ";
		}

		if ($con_cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$con_cancel_tf."' ";
		}

		if ($con_confirm_tf <> "") {
			$query .= " AND GRG.CONFIRM_TF = '".$con_confirm_tf."' ";
		}

		if ($con_changed_tf <> "") {
			$query .= " AND GRG.CHANGED_TF = '".$con_changed_tf."' ";
		}

		if ($con_receive_tf <> "") {
			if ($con_receive_tf == "Y") 
				$query .= " AND GRG.RECEIVE_QTY > 0 ";
			else
				$query .= " AND GRG.RECEIVE_QTY = 0 ";
		}

		if ($con_wrap_tf <> "") {
			if($con_wrap_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%포장지 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%포장지 :%' ";
		}

		if ($con_sticker_tf <> "") {
			if($con_sticker_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%스티커 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%스티커 :%' ";
		}

		if ($chk_after_confirm <> "") {
			$query .= " AND GRG.CONFIRM_DATE < GRG.RECEIVE_DATE ";
		}
		if($con_payment <> ""){
			// echo "test<br>";
			$query .= " AND C.AD_TYPE = '".$con_payment."' ";
		}


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GR.BUY_CP_NM LIKE '%".$search_str."%' OR GR.BUY_MANAGER_NM LIKE '%".$search_str."%' OR GR.BUY_CP_PHONE LIKE '%".$search_str."%' OR GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR GRG.MEMO2 LIKE '%".$search_str."%' OR GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO2") {
				$query .= " AND (GRG.MEMO2 LIKE '%".$search_str."%') ";
			} else if ($search_field == "REQ_GOODS_NO") {
				$query .= " AND (GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND (GRG.RESERVE_NO = '".$search_str."') ";
			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND (GRG.ORDER_GOODS_NO = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

	    if ($order_field == "") 
			$order_field = "GR.REG_DATE";

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

	//발주서 관리 리스트  - 업체만 표기 + 이메일(2019.01.31 추가)
	function listGoodsRequestDistinctBuyCP($db, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str) {

		$query = "SELECT DISTINCT GR.BUY_CP_NM, GR.SENT_EMAIL, GR.BUY_CP_PHONE
								FROM TBL_GOODS_REQUEST GR
								JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
								JOIN TBL_GOODS G ON G.GOODS_NO = GRG.GOODS_NO
							   WHERE GR.DEL_TF = 'N' AND GRG.DEL_TF = 'N'  ";

		if ($start_date <> "") {
			$query .= " AND GR.REQ_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GR.REQ_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND GR.BUY_CP_NO = '".$cp_type."' ";
		}

		if ($filter['con_delivery_tf'] <> "") {
			$query .= " AND GR.IS_SENT = '".$filter['con_delivery_tf']."' ";
		}

		if ($filter['con_to_here'] <> "") {
			$query .= " AND GRG.TO_HERE = '".$filter['con_to_here']."' ";
		}

		if ($filter['con_cancel_tf'] <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$filter['con_cancel_tf']."' ";
		}

		if ($filter['con_confirm_tf'] <> "") {
			$query .= " AND GRG.CONFIRM_TF = '".$filter['con_confirm_tf']."' ";
		}

		if ($filter['con_changed_tf'] <> "") {
			$query .= " AND GRG.CHANGED_TF = '".$filter['con_changed_tf']."' ";
		}
		
		if ($filter['con_wrap_tf'] <> "") {
			if($filter['con_wrap_tf'] == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%포장지 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%포장지 :%' ";
		}

		if ($filter['con_sticker_tf'] <> "") {
			if($filter['con_sticker_tf'] == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%스티커 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%스티커 :%' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GR.BUY_CP_NM LIKE '%".$search_str."%' OR GR.BUY_MANAGER_NM LIKE '%".$search_str."%' OR GR.BUY_CP_PHONE LIKE '%".$search_str."%' OR GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR GRG.MEMO2 LIKE '%".$search_str."%' OR GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO2") {
				$query .= " AND (GRG.MEMO2 LIKE '%".$search_str."%') ";
			} else if ($search_field == "REQ_GOODS_NO") {
				$query .= " AND (GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

	    //echo $query;
		//if ($order_field == "") 
			$order_field = "GR.BUY_CP_NM";

		//if ($order_str == "") 
			$order_str = "ASC";

		$query .= " ORDER BY ".$order_field." ".$order_str;

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

	//발주서 관리 리스트  - 업체만 표기
	function listGoodsRequestDistinctBuyCP2($db, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str) {

		$query = " SELECT GR.BUY_CP_NM, GR.BUY_CP_NO, 
				            COUNT(*) AS TOTAL_GRG,
							SUM(CASE WHEN GRG.CANCEL_TF = 'Y' THEN 1 ELSE 0 END ) AS CANCEL_GRG, 
							SUM(CASE WHEN CL.CL_NO IS NOT NULL THEN 1 ELSE 0 END ) AS TOTAL_CL, 
							SUM(CASE WHEN CL.TAX_CONFIRM_TF =  'N' AND CL.CL_NO IS NOT NULL THEN 1 ELSE 0 END ) AS NOT_CONFIRMED_CL,
							MAX(CL.INOUT_DATE) AS MAX_INOUT_DATE,

							SUM(CASE WHEN MONTH(CL.INOUT_DATE) = MONTH(CURRENT_DATE()) AND YEAR(CL.INOUT_DATE) = YEAR(CURRENT_DATE()) AND CL.TAX_CONFIRM_TF =  'N'
							 THEN 1 ELSE 0 END) AS SAME_MONTH_CNT

					FROM TBL_GOODS_REQUEST GR
					JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
					JOIN TBL_GOODS G ON G.GOODS_NO = GRG.GOODS_NO
			   LEFT JOIN 
						(SELECT CL_NO, INOUT_DATE, TAX_CONFIRM_TF, RGN_NO 
						   FROM TBL_COMPANY_LEDGER 
						  WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND INOUT_TYPE = '매입') CL ON GRG.REQ_GOODS_NO = CL.RGN_NO 
				   WHERE GR.DEL_TF = 'N' AND GRG.DEL_TF = 'N'  ";

		if ($start_date <> "") {
			$query .= " AND GR.REQ_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GR.REQ_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND GR.BUY_CP_NO = '".$cp_type."' ";
		}

		if ($filter['con_delivery_tf'] <> "") {
			$query .= " AND GR.IS_SENT = '".$filter['con_delivery_tf']."' ";
		}

		if ($filter['con_to_here'] <> "") {
			$query .= " AND GRG.TO_HERE = '".$filter['con_to_here']."' ";
		}

		if ($filter['con_cancel_tf'] <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$filter['con_cancel_tf']."' ";
		}

		if ($filter['con_confirm_tf'] <> "") {
			$query .= " AND GRG.CONFIRM_TF = '".$filter['con_confirm_tf']."' ";
		}

		if ($filter['con_changed_tf'] <> "") {
			$query .= " AND GRG.CHANGED_TF = '".$filter['con_changed_tf']."' ";
		}
		
		if ($filter['con_wrap_tf'] <> "") {
			if($filter['con_wrap_tf'] == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%포장지 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%포장지 :%' ";
		}

		if ($filter['con_sticker_tf'] <> "") {
			if($filter['con_sticker_tf'] == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%스티커 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%스티커 :%' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GR.BUY_CP_NM LIKE '%".$search_str."%' OR GR.BUY_MANAGER_NM LIKE '%".$search_str."%' OR GR.BUY_CP_PHONE LIKE '%".$search_str."%' OR GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR GRG.MEMO2 LIKE '%".$search_str."%' OR GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO2") {
				$query .= " AND (GRG.MEMO2 LIKE '%".$search_str."%') ";
			} else if ($search_field == "REQ_GOODS_NO") {
				$query .= " AND (GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " GROUP BY GR.BUY_CP_NM, GR.BUY_CP_NO
					
		          ";
		/*
		//if ($order_field == "") 
			$order_field = "GR.BUY_CP_NM";

		//if ($order_str == "") 
			$order_str = "ASC";
		*/

		$query .= " ORDER BY (SUM(CASE WHEN CL.TAX_CONFIRM_TF =  'N' AND CL.CL_NO IS NOT NULL THEN 1 ELSE 0 END) - SUM(CASE WHEN MONTH(CL.INOUT_DATE) = MONTH(CURRENT_DATE()) AND YEAR(CL.INOUT_DATE) = YEAR(CURRENT_DATE()) AND CL.TAX_CONFIRM_TF =  'N'
							 THEN 1 ELSE 0 END)) DESC, GR.BUY_CP_NM ASC";

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

	//발주서 관리 리스트 페이지 - 서브
	function listGoodsRequestGoods($db, $req_no, $cancel_tf) {

		$query = "SELECT GRG.RESERVE_NO, 
						 GRG.REQ_GOODS_NO, GRG.ORDER_GOODS_NO, GRG.GOODS_NO, GRG.GOODS_CODE, GRG.GOODS_NAME, GRG.GOODS_SUB_NAME, GRG.BUY_PRICE,	GRG.REQ_QTY, GRG.BUY_TOTAL_PRICE, 
						 GRG.RECEIVE_QTY, GRG.RECEIVE_DATE, GRG.REASON, 
						 GRG.TO_HERE, GRG.RECEIVER_NM, GRG.RECEIVER_ADDR, GRG.RECEIVER_PHONE, GRG.RECEIVER_HPHONE, GRG.MEMO1, GRG.MEMO2, GRG.MEMO3, GRG.CHANGED_TF, GRG.UP_ADM, GRG.UP_DATE, GRG.CANCEL_TF, GRG.CANCEL_DATE, GRG.CANCEL_ADM, GRG.CONFIRM_TF, GRG.CONFIRM_DATE, GRG.REG_DATE
						 , DATE_FORMAT(CONCAT(SUBSTRING(GRG.REG_DATE,1,10), ' 13:59:59'), '%Y-%m-%d %H:%i:%s') AS DEFAULT_DATE
						 , CASE WHEN GRG.REG_DATE > DATE_FORMAT(CONCAT(SUBSTRING(GRG.REG_DATE,1,10), ' 13:59:59'), '%Y-%m-%d %H:%i:%s') THEN DATE_ADD(SUBSTRING(GRG.REG_DATE,1,10),INTERVAL 1 DAY)  ELSE SUBSTRING(GRG.REG_DATE,1,10) END AS DELIVERY_DATE
						 , (SELECT B.DELIVERY_CNT_IN_BOX FROM TBL_GOODS B WHERE B.GOODS_NO = GRG.GOODS_NO) AS DELIVERY_CNT_IN_BOX
					FROM TBL_GOODS_REQUEST_GOODS GRG 
					JOIN TBL_GOODS G ON GRG.GOODS_NO = G.GOODS_NO
				   WHERE GRG.REQ_NO = '$req_no' AND GRG.DEL_TF ='N'";

		if ($cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$cancel_tf."' ";
		}

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

	// 발주서 디테일 팝업 페이지
	function selectGoodsRequestByReqNo($db, $req_no) {

		$query = "SELECT REQ_NO, GROUP_NO, REQ_DATE, SENDER_CP, CEO_NM, SENDER_ADDR, SENDER_PHONE, 
						BUY_CP_NO, BUY_CP_NM, BUY_MANAGER_NM, BUY_CP_PHONE, DELIVERY_TYPE, MEMO, TOTAL_REQ_QTY, TOTAL_BUY_TOTAL_PRICE,
						REQUEST_TYPE, SENT_EMAIL, EMAIL_SUBJECT, EMAIL_BODY, IS_SENT, SENT_DATE
					FROM TBL_GOODS_REQUEST 
				   WHERE REQ_NO = '$req_no' AND DEL_TF = 'N' ";

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

	//전표번호내의 모든 상품의 단가의 합을 구해서 업데이트
	function resetGoodsRequestTotal($db, $req_no) {

		$query = "SELECT SUM(REQ_QTY) AS TOTAL_REQ_QTY, SUM(BUY_TOTAL_PRICE) AS TOTAL_BUY_TOTAL_PRICE
								FROM TBL_GOODS_REQUEST_GOODS
								WHERE DEL_TF = 'N' AND CANCEL_TF = 'N' AND REQ_NO = '$req_no' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		$TOTAL_REQ_QTY = $record[0]["TOTAL_REQ_QTY"];
		$TOTAL_BUY_TOTAL_PRICE = $record[0]["TOTAL_BUY_TOTAL_PRICE"];

		$query="UPDATE TBL_GOODS_REQUEST
					SET TOTAL_REQ_QTY = '$TOTAL_REQ_QTY', TOTAL_BUY_TOTAL_PRICE = '$TOTAL_BUY_TOTAL_PRICE'
				WHERE REQ_NO = '$req_no' ; ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//발주서 업체를 수정하고 기장내역을 해당 업체로 옮김
	function updateGoodsRequestCPNo($db, $req_no, $cp_no, $cp_nm, $up_adm){

		$query="UPDATE TBL_GOODS_REQUEST
					SET BUY_CP_NO = '$cp_no', BUY_CP_NM = '$cp_nm', UP_ADM = '$up_adm', UP_DATE = now()
				WHERE REQ_NO = '$req_no' AND BUY_CP_NO <> '$cp_no';";

		//echo $query;
		
		if(mysql_query($query,$db)) {

			$query = "SELECT REQ_GOODS_NO
						FROM TBL_GOODS_REQUEST_GOODS
					   WHERE DEL_TF = 'N' AND CANCEL_TF = 'N' AND REQ_NO = '$req_no' ";

			//echo $query;

			$result = mysql_query($query,$db);
			$record = array();
			

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);
					$REQ_GOODS_NO = $record[$i]["REQ_GOODS_NO"];
					
					$query="UPDATE TBL_COMPANY_LEDGER
							   SET CP_NO = '$cp_no'
							 WHERE RGN_NO = '$REQ_GOODS_NO' ; ";

					mysql_query($query,$db);

				}
			}
		}
		
	}

	//전표를 삭제하고 전표에 속한 모든 상품의 발주를 삭제
	function DeleteGoodsRequest($db, $req_no, $del_adm){

		$query="UPDATE TBL_GOODS_REQUEST
					SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
				WHERE REQ_NO = '$req_no' ; ";

		//echo $query;
		
		if(mysql_query($query,$db)) {

			//발주전이므로 가입고 및 상품 가입고 삭제할 필요 없음

			$query="UPDATE TBL_GOODS_REQUEST_GOODS
					   SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					 WHERE REQ_NO = '$req_no' ; ";

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
		}
	}

	//전표의 상품을 취소
	function UpdateGoodsRequestGoodsStatus($db, $req_goods_no, $del_adm){

		$query = "SELECT REQ_NO, CANCEL_TF
					FROM TBL_GOODS_REQUEST_GOODS  
				   WHERE REQ_GOODS_NO = '$req_goods_no';  ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$REQ_NO	   = $rows[0];
		$CANCEL_TF = $rows[1];

		//echo $CANCEL_TF."<br/>";
		//exit;


		if($CANCEL_TF != 'Y') { 
			//취소프로세스 시작


			//취소하고 기장확정 되돌리기
			$query="UPDATE TBL_GOODS_REQUEST_GOODS
						SET CANCEL_TF = 'Y', CANCEL_ADM = '$del_adm', CANCEL_DATE = now(), CONFIRM_TF = 'N', CONFIRM_DATE = ''
					WHERE REQ_GOODS_NO = '$req_goods_no' ; ";
			mysql_query($query,$db);

			//세부 항목 기장 취소하고 기장확정 되돌리기
			$query="UPDATE TBL_GOODS_REQUEST_GOODS_LEDGER
					  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now(), CONFIRM_TF = 'N', CONFIRM_DATE = ''
					WHERE DEL_TF = 'N' AND REQ_GOODS_NO = '$req_goods_no' ; ";
			mysql_query($query,$db);


			//발주서 토탈 수정
			$query="
					UPDATE TBL_GOODS_REQUEST A JOIN 
					(
						SELECT IFNULL(SUM( REQ_QTY ), 0) AS SUM_REQ_QTY, IFNULL(SUM( BUY_TOTAL_PRICE ), 0) AS SUM_BUY_TOTAL_PRICE, '$REQ_NO' AS REQ_NO
						FROM TBL_GOODS_REQUEST_GOODS WHERE REQ_NO = '$REQ_NO' AND CANCEL_TF = 'N' AND DEL_TF = 'N'
					) B 
					ON A.REQ_NO = B.REQ_NO
					SET A.TOTAL_REQ_QTY = B.SUM_REQ_QTY, A.TOTAL_BUY_TOTAL_PRICE = B.SUM_BUY_TOTAL_PRICE
					WHERE A.REQ_NO = '$REQ_NO' ";
			mysql_query($query,$db);

			//발주서 입고수량 수정
			$query="
					UPDATE TBL_GOODS_REQUEST_GOODS A JOIN 
					(
						SELECT SUM(IN_QTY) AS SUM_IN_QTY
						FROM TBL_STOCK WHERE RGN_NO = '$req_goods_no' AND CLOSE_TF = 'N' AND DEL_TF = 'N'
					) B 
					ON A.REQ_GOODS_NO = B.RGN_NO
					SET A.RECEIVE_QTY = B.SUM_IN_QTY
					WHERE A.REQ_GOODS_NO = '$req_goods_no' ";
			mysql_query($query,$db);

			//기장삭제
			$query="UPDATE TBL_COMPANY_LEDGER
					  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					WHERE DEL_TF = 'N' AND RGN_NO = '$req_goods_no' ; ";
			mysql_query($query,$db);
			
			//가재고 삭제
			$query2 = "SELECT STOCK_NO 
						FROM TBL_STOCK 
					   WHERE RGN_NO = '".$req_goods_no."' 
						 AND STOCK_TYPE = 'IN'
						 AND STOCK_CODE = 'FST02'
						 AND CLOSE_TF = 'N'
						 AND DEL_TF = 'N' ";

				//echo $query;

			$result2 = mysql_query($query2,$db);
			$record2 = array();

			if ($result2 <> "") {

				for($j=0;$j < mysql_num_rows($result2);$j++) {
					$record2[$j] = sql_result_array($result2,$j);

					$stock_no = $record2[$j]["STOCK_NO"];
					deleteStock($db, $stock_no, $del_adm);
				}
			}
		} else { 
			//취소번복 프로세스 시작

			//취소 번복 처리 그러나 기장확정전 상태로 되돌리기
			$query="UPDATE TBL_GOODS_REQUEST_GOODS
						SET CANCEL_TF = 'N', CANCEL_ADM = '$del_adm', CANCEL_DATE = now(), CONFIRM_TF = 'N', CONFIRM_DATE = ''
					WHERE REQ_GOODS_NO = '$req_goods_no'  ; ";
			mysql_query($query,$db);

			//세부 항목 기장 취소번복 그러나 기장확정 되돌리기
			$query="UPDATE TBL_GOODS_REQUEST_GOODS_LEDGER
					  SET DEL_TF = 'N', DEL_ADM = '$del_adm', DEL_DATE = now(), CONFIRM_TF = 'N', CONFIRM_DATE = ''
					WHERE DEL_TF = 'Y' AND REQ_GOODS_NO = '$req_goods_no' ; ";
			mysql_query($query,$db);


			//발주서 토탈 수정 - 취소와 그대로
			$query="
					UPDATE TBL_GOODS_REQUEST A JOIN 
					(
						SELECT IFNULL(SUM( REQ_QTY ), 0) AS SUM_REQ_QTY, IFNULL(SUM( BUY_TOTAL_PRICE ), 0) AS SUM_BUY_TOTAL_PRICE, '$REQ_NO' AS REQ_NO
						FROM TBL_GOODS_REQUEST_GOODS WHERE REQ_NO = '$REQ_NO' AND CANCEL_TF = 'N' AND DEL_TF = 'N'
					) B 
					ON A.REQ_NO = B.REQ_NO
					SET A.TOTAL_REQ_QTY = B.SUM_REQ_QTY, A.TOTAL_BUY_TOTAL_PRICE = B.SUM_BUY_TOTAL_PRICE
					WHERE A.REQ_NO = '$REQ_NO' ";
			mysql_query($query,$db);

			//발주서 입고수량 수정 - 취소와 그대로
			$query="
					UPDATE TBL_GOODS_REQUEST_GOODS A JOIN 
					(
						SELECT SUM(IN_QTY) AS SUM_IN_QTY
						FROM TBL_STOCK WHERE RGN_NO = '$req_goods_no' AND CLOSE_TF = 'N' AND DEL_TF = 'N'
					) B 
					ON A.REQ_GOODS_NO = B.RGN_NO
					SET A.RECEIVE_QTY = B.SUM_IN_QTY
					WHERE A.REQ_GOODS_NO = '$req_goods_no' ";
			mysql_query($query,$db);

			//기장삭제
			/*
			$query="UPDATE TBL_COMPANY_LEDGER
					  SET DEL_TF = 'N', DEL_ADM = '$del_adm', DEL_DATE = now()
					WHERE DEL_TF = 'Y' AND RGN_NO = '$req_goods_no' ; ";
			mysql_query($query,$db);
			*/

			//가재고 입력
			insertFStock($db, $REQ_NO, $del_adm);

			/*
			$query2 = "SELECT STOCK_NO 
						FROM TBL_STOCK 
					   WHERE RGN_NO = '".$req_goods_no."' 
						 AND STOCK_TYPE = 'IN'
						 AND STOCK_CODE = 'FST02'
						 AND CLOSE_TF = 'N'
						 AND DEL_TF = 'Y' ";

				//echo $query;

			$result2 = mysql_query($query2,$db);
			$record2 = array();

			if ($result2 <> "") {

				for($j=0;$j < mysql_num_rows($result2);$j++) {
					$record2[$j] = sql_result_array($result2,$j);

					$stock_no = $record2[$j]["STOCK_NO"];
					//deleteStock($db, $stock_no, $del_adm);
				}
			}
			*/


		}
	}

	//발주후 수량변경되었을때 발주서의 입고 수량 변경
	function getLastInsertedGoodsRequestGoodsQty($db, $rgn_no, $goods_no, $in_fqty) { 

		$query = "SELECT REG_DATE
					FROM TBL_STOCK  
				   WHERE RGN_NO = '$rgn_no' AND GOODS_NO = '$goods_no' AND DEL_TF = 'N' AND CLOSE_TF = 'N' AND STOCK_CODE = 'NST01' AND IN_QTY = '$in_fqty'
				   ORDER BY REG_DATE DESC";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];

	}

	//발주후 수량변경되었을때 발주서의 입고 수량 변경
	function updateGoodsRequestGoodsQty($db, $REQ_GOODS_NO) { 

		$query = "SELECT SUM(IN_QTY)
					FROM TBL_STOCK  
				   WHERE RGN_NO = '$REQ_GOODS_NO' AND DEL_TF = 'N' AND CLOSE_TF = 'N' AND STOCK_CODE = 'NST01'  ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$total_in_qty = $rows[0];

		if($total_in_qty > 0) { 
			$query="UPDATE TBL_GOODS_REQUEST_GOODS
					   SET RECEIVE_QTY = '$total_in_qty',
						   RECEIVE_DATE = now()
					 WHERE REQ_GOODS_NO = '$REQ_GOODS_NO' AND RECEIVE_QTY <> '$total_in_qty'; ";
		} else { 
			$query="UPDATE TBL_GOODS_REQUEST_GOODS
					   SET RECEIVE_QTY = '', 
					   RECEIVE_DATE = null
					 WHERE REQ_GOODS_NO = '$REQ_GOODS_NO' AND RECEIVE_QTY <> '$total_in_qty'; ";
		}

		mysql_query($query,$db);

	}

	// 발주를 위한 전표번호 받기
	function cntMaxGroupNoRequest($db) { 

		$query = "SELECT IFNULL( MAX( GROUP_NO ) , 0 ) +1 AS GROUP_NO
					FROM TBL_GOODS_REQUEST  
				   WHERE DEL_TF = 'N' AND REQ_DATE = CURDATE()  ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	// 발주 - 기존 발주 내역이 있는지 체크
	function chkExistOrderGoodsNo($db, $order_goods_no) { 

		$query = "SELECT COUNT(*) FROM TBL_GOODS_REQUEST_GOODS 
				   WHERE DEL_TF = 'N' AND CANCEL_TF = 'N' AND ORDER_GOODS_NO = '".$order_goods_no."';  ";

		//echo $query."<br/>";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	// 발주 - 기존 발주의 주문상품, 구성상품 수량총합을 가져옴
	function chkSumOrderGoodsNo($db, $order_goods_no, $goods_no) { 

		$query = "SELECT SUM(REQ_QTY) FROM TBL_GOODS_REQUEST_GOODS 
				   WHERE DEL_TF = 'N' AND CANCEL_TF = 'N' AND ORDER_GOODS_NO = '$order_goods_no' AND GOODS_NO = '$goods_no';  ";

		//echo $query."<br/>";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	//발주관리 - 발주등록
	function insertGoodsRequest($db, $op_cp_no,  $group_no, $req_date, $buy_cp_no, $delivery_type, $memo,  $reg_adm) {

		$arr_op_cp = getOperatingCompany($db, $op_cp_no);
		$OP_CP_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
		$OP_CEO_NM = $arr_op_cp[0]["CEO_NM"];
		$OP_CP_ADDR = $arr_op_cp[0]["CP_ADDR"];
		$OP_CP_PHONE = $arr_op_cp[0]["CP_PHONE"];
		$OP_CP_FAX = $arr_op_cp[0]["CP_FAX"];
		$OP_EMAIL = $arr_op_cp[0]["EMAIL"];

		$OP_CP_PHONE = $OP_CP_PHONE."/ F: ".$OP_CP_FAX." (".$OP_EMAIL.")";

		$arr_buy_cp = selectCompany($db, $buy_cp_no);
		$BUY_CP_NM = $arr_buy_cp[0]["CP_NM"]." ".$arr_buy_cp[0]["CP_NM2"];
		$BUY_MANAGER_NM = $arr_buy_cp[0]["MANAGER_NM"];
		$BUY_CP_PHONE = $arr_buy_cp[0]["CP_PHONE"];
		$BUY_CP_FAX = $arr_buy_cp[0]["CP_FAX"];
		$BUYER_EMAIL = $arr_buy_cp[0]["EMAIL"];

		$BUY_PHONE = $BUY_CP_PHONE.($BUY_CP_FAX != "" ? "/ F: ".$BUY_CP_FAX : "");
		
		$query="INSERT INTO TBL_GOODS_REQUEST (GROUP_NO, REQ_DATE, SENDER_CP, CEO_NM, SENDER_ADDR, SENDER_PHONE, 
											   	BUY_CP_NO, BUY_CP_NM, BUY_MANAGER_NM, BUY_CP_PHONE, DELIVERY_TYPE, MEMO, SENT_EMAIL, REG_ADM, REG_DATE) 
				 VALUES ('$group_no', '$req_date', '$OP_CP_NM', '$OP_CEO_NM', '$OP_CP_ADDR', '$OP_CP_PHONE', 
						 '$buy_cp_no', '$BUY_CP_NM', '$BUY_MANAGER_NM', '$BUY_PHONE', '$delivery_type', '$memo', '$BUYER_EMAIL', '$reg_adm', now()) ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			$query = "SELECT MAX(REQ_NO)
						FROM TBL_GOODS_REQUEST  
					   WHERE DEL_TF =  'N'  ";
		
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			return $rows[0];
		}
		
	}

	//발주관리 - 발주등록
	function insertGoodsRequestGoods($db, $op_cp_no, $req_no, $reserve_no, $order_goods_no, $group_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $reg_adm) { 

		if($chk_to_here) {

			$arr_op_cp = getOperatingCompany($db, $op_cp_no);
			$RECEIVER_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$RECEIVER_ADDR = $arr_op_cp[0]["CP_ADDR"];
			$RECEIVER_PHONE = $arr_op_cp[0]["CP_PHONE"];
			$RECEIVER_HPHONE = $arr_op_cp[0]["CP_HPHONE"];
			$SENDER_NM = "";
			//$memo1 = "";

			$to_here = 'Y';
		} else {

			if($order_goods_no <> "") { 
				$query="SELECT O.R_MEM_NM, O.R_ADDR1, O.R_PHONE, O.R_HPHONE, OG.SENDER_NM
						  FROM TBL_ORDER O 
						  JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO  
						 WHERE OG.ORDER_GOODS_NO = '$order_goods_no' ";

				//echo $query;
				//exit;

				$result = mysql_query($query,$db);
				$record = array();

				if ($result <> "") {
					for($i=0;$i < mysql_num_rows($result);$i++) {
						$record[$i] = sql_result_array($result,$i);
					}
				}

				$RECEIVER_NM		= $record[0]["R_MEM_NM"];
				$RECEIVER_ADDR		= $record[0]["R_ADDR1"];
				$RECEIVER_PHONE		= $record[0]["R_PHONE"];
				$RECEIVER_HPHONE	= $record[0]["R_HPHONE"];
				$SENDER_NM			= $record[0]["SENDER_NM"];
			}

			$to_here = 'N';
		}

		$query="INSERT INTO TBL_GOODS_REQUEST_GOODS 
						(REQ_NO, RESERVE_NO, ORDER_GOODS_NO, GROUP_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, BUY_PRICE, REQ_QTY, BUY_TOTAL_PRICE, TO_HERE, RECEIVER_NM, RECEIVER_ADDR, RECEIVER_PHONE, RECEIVER_HPHONE, MEMO1, MEMO2, MEMO3, REG_ADM, REG_DATE) 
				 VALUES ('$req_no', '$reserve_no', '$order_goods_no', '$group_no', '$goods_no', '$goods_code', '$goods_name', '$goods_sub_name', '$buy_price', '$req_qty', '$buy_total_price', '$to_here', '$RECEIVER_NM', '$RECEIVER_ADDR', '$RECEIVER_PHONE', '$RECEIVER_HPHONE', '$memo1', '$memo2', '$SENDER_NM', '$reg_adm', now() ) ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	// 발주관리 - 전표 수정
	function updateGoodsRequest($db, $req_date, $delivery_type, $memo, $req_no, $up_adm) {

		$query="UPDATE TBL_GOODS_REQUEST 
				   SET REQ_DATE = '$req_date', 
					   DELIVERY_TYPE = '$delivery_type', 
					   MEMO = '$memo',
					   UP_ADM = '$up_adm',
					   UP_DATE = now()
				 WHERE REQ_NO = '$req_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	//발주관리 - 전표 상품 수정
	function updateGoodsRequestGoods($db, $op_cp_no, $order_goods_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $buy_price, $req_qty, $buy_total_price, $origin_chk_to_here, $chk_to_here, $req_goods_no, $upd_adm) { 

		if($chk_to_here) {

			$arr_op_cp = getOperatingCompany($db, $op_cp_no);
			$RECEIVER_NM = $arr_op_cp[0]["CP_NM"]." ".$arr_op_cp[0]["CP_NM2"];
			$RECEIVER_ADDR = $arr_op_cp[0]["CP_ADDR"];
			$RECEIVER_PHONE = $arr_op_cp[0]["CP_PHONE"];
			$RECEIVER_HPHONE = $arr_op_cp[0]["CP_HPHONE"];

			$to_here = 'Y';
		} else {

			if($order_goods_no <> "") { 
				$query="SELECT O.R_MEM_NM, O.R_ADDR1, O.R_PHONE, O.R_HPHONE, OG.SENDER_NM 
						  FROM TBL_ORDER O 
						  JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
						 WHERE OG.ORDER_GOODS_NO = '$order_goods_no' ) ";

				//echo $query;
				//exit;

				$result = mysql_query($query,$db);
				$record = array();

				if ($result <> "") {
					for($i=0;$i < mysql_num_rows($result);$i++) {
						$record[$i] = sql_result_array($result,$i);
					}
				}

				$RECEIVER_NM	 = $record[0]["R_MEM_NM"];
				$RECEIVER_ADDR	 = $record[0]["R_ADDR1"];
				$RECEIVER_PHONE	 = $record[0]["R_PHONE"];
				$RECEIVER_HPHONE = $record[0]["R_HPHONE"];
				$SENDER_NM		 = $record[0]["SENDER_NM"];
				
			} else { 
			
				$RECEIVER_NM = "";
				$RECEIVER_ADDR = "";
				$RECEIVER_PHONE = "";
				$RECEIVER_HPHONE = "";
				$SENDER_NM		 = "";
			}

			$to_here = 'N';
		}

		$query = "UPDATE TBL_GOODS_REQUEST_GOODS 
		           SET BUY_PRICE		 = '$buy_price', 
					   REQ_QTY			 = '$req_qty',
					   BUY_TOTAL_PRICE   = '$buy_total_price', ";

		if($origin_chk_to_here != $to_here) { 
		$query .= "	   GOODS_NO			 = '$goods_no',
					   GOODS_CODE		 = '$goods_code',
				       GOODS_NAME		 = '$goods_name',
					   GOODS_SUB_NAME	 = '$goods_sub_name',
					   TO_HERE			 = '$to_here', 
					   RECEIVER_NM		 = '$RECEIVER_NM', 
					   RECEIVER_ADDR	 = '$RECEIVER_ADDR', 
					   RECEIVER_PHONE	 = '$RECEIVER_PHONE', 
					   RECEIVER_HPHONE	 = '$RECEIVER_HPHONE', 
					   SENDER_NM		 = '$SENDER_NM', ";
		}
		$query .= "
					   UP_ADM			 = '$upd_adm',
					   UP_DATE			 = now() ";
		
		$query .= "WHERE REQ_GOODS_NO = '$req_goods_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	//발주서에서 상품 삭제
	function deleteGoodsRequestGoods($db, $req_goods_no, $del_adm) {

		$query="UPDATE TBL_GOODS_REQUEST_GOODS 
		           SET DEL_TF = 'Y',
				       DEL_ADM = '$del_adm',
					   DEL_DATE = now()
				 WHERE REQ_GOODS_NO = '$req_goods_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	//메일로 발주가 나가지 않은 최신번호의 전표를 가져와서 상품 추가
	function getReqNoFromBuyCPNo($db, $buy_cp_no) { 

		$query = "SELECT G.REQ_NO, G.GROUP_NO
					FROM TBL_GOODS_REQUEST G JOIN TBL_GOODS_REQUEST_GOODS GG ON G.REQ_NO = GG.REQ_NO
				   WHERE G.BUY_CP_NO = '$buy_cp_no' AND G.DEL_TF = 'N' AND G.IS_SENT = 'N' AND LEFT(G.REG_DATE,10) = CURDATE() 
					 AND GG.DEL_TF = 'N' AND GG.CANCEL_TF = 'N'
				   ORDER BY G.GROUP_NO DESC limit 1; ";

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

	//발주 메일 발송하고 발송 처리
	function updateGoodsRequestSentEmail($db, $req_no, $sent_email, $email_subject, $email_body) {
		
		$query="UPDATE TBL_GOODS_REQUEST
				   SET SENT_EMAIL = '$sent_email',
				       IS_SENT = 'Y',
					   SENT_DATE = now(),
					   EMAIL_SUBJECT = '$email_subject',
					   EMAIL_BODY = '$email_body'
				 WHERE REQ_NO = '$req_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//입출고관리 페이지
	function listStockInOut($db, $search_date_type, $start_date, $end_date, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $loc,  $close_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 A.STOCK_NO, A.STOCK_TYPE, A.STOCK_CODE,
										 A.IN_CP_NO, A.OUT_CP_NO, A.GOODS_NO, A.RESERVE_NO, 
										 A.IN_LOC, A.IN_LOC_EXT, A.IN_QTY, A.IN_BQTY,
										 A.IN_FQTY, A.OUT_QTY, A.OUT_BQTY, A.OUT_TQTY,
										 A.IN_PRICE, A.OUT_PRICE, A.IN_DATE, A.OUT_DATE, A.PAY_DATE, A.CLOSE_TF, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.DEL_ADM, A.DEL_DATE, A.MEMO,
										 B.GOODS_NAME, B.GOODS_CODE
								FROM TBL_STOCK A 
								JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
								WHERE 1 = 1 ";

		
		if($search_date_type == "inout_date" || $search_date_type == "") { 
			if ($start_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND A.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE LIKE '".$stock_code."%' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($loc <> "") {
			$query .= " AND A.IN_LOC = '".$loc."' ";
		} 

		if ($close_tf <> "") {
			$query .= " AND A.CLOSE_TF = '".$close_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')  ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "RESERVE_NO"){
				$query .= " AND (A.RESERVE_NO = '".$search_str."' OR A.ORDER_GOODS_NO = '".$search_str."' OR A.RGN_NO = '".$search_str."') ";
			} else if ($search_field == "IN_LOC_EXT"){
				$query .= " AND (A.IN_LOC_EXT LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO"){
				$query .= " AND (A.MEMO LIKE '%".$search_str."%') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", A.STOCK_NO ".$order_str." limit ".$offset.", ".$nRowCount;

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

	//입출고관리 페이지
	function totalCntStockInOut($db, $search_date_type, $start_date, $end_date, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $loc, $close_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT COUNT(*) AS CNT
								FROM TBL_STOCK A 
								JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
								LEFT JOIN TBL_ORDER O ON O.RESERVE_NO = A.RESERVE_NO 
								WHERE 1 = 1 ";

		if($search_date_type == "inout_date" || $search_date_type == "") { 
			if ($start_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND A.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE LIKE '".$stock_code."%' ";
		} 

		if ($in_cp_no <> "") {
			$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
		} 

		if ($out_cp_no <> "") {
			$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
		} 

		if ($loc <> "") {
			$query .= " AND A.IN_LOC = '".$loc."' ";
		} 

		if ($close_tf <> "") {
			$query .= " AND A.CLOSE_TF = '".$close_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')  ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "RESERVE_NO"){
				$query .= " AND (A.RESERVE_NO = '".$search_str."' OR A.ORDER_GOODS_NO = '".$search_str."' OR A.RGN_NO = '".$search_str."') ";
			} else if ($search_field == "IN_LOC_EXT"){
				$query .= " AND (A.IN_LOC_EXT LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO"){
				$query .= " AND (A.MEMO LIKE '%".$search_str."%') ";
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

	function listSumStockInOut($db, $search_date_type, $start_date, $end_date, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $loc,  $close_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount, $total_cnt, $option) {

		
		if($nPage == 1){
			$offset = $nRowCount*($nPage-1);
			$query = "SELECT SUM( IN_QTY + IN_BQTY + IN_FQTY - OUT_QTY - OUT_BQTY ) AS SUM_PREV_QTY, 
							 GOODS_NO
						FROM ( 

							SELECT 
									 A.GOODS_NO, 
									 A.IN_LOC, A.IN_LOC_EXT, A.IN_QTY, A.IN_BQTY,
									 A.IN_FQTY, A.OUT_QTY, A.OUT_BQTY
							FROM TBL_STOCK A 
							JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
							WHERE 1 = 1 AND CLOSE_TF = 'N' ";

			if ($start_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END < '".$start_date." 00:00:00' ";
			}

			if ($stock_type <> "") {
				$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
			}

			if ($stock_code <> "") {
				$query .= " AND A.STOCK_CODE LIKE '".$stock_code."%' ";
			} 

			if ($in_cp_no <> "") {
				$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
			} 

			if ($out_cp_no <> "") {
				$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
			} 

			if ($loc <> "") {
				$query .= " AND A.IN_LOC = '".$loc."' ";
			} 

			if ($del_tf <> "") {
				$query .= " AND A.DEL_TF = '".$del_tf."' ";
			}

			if ($search_str <> "") {
				if ($search_field == "ALL") {
					$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')  ";
				} else if ($search_field == "GOODS_NAME") {
					$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
				} else if ($search_field == "GOODS_NO") {
					$query .= " AND B.GOODS_NO = '".$search_str."' ";
				} else if ($search_field == "GOODS_CODE"){
					$query .= " AND B.GOODS_CODE = '".$search_str."' ";
				} else if ($search_field == "RESERVE_NO"){
					$query .= " AND (A.RESERVE_NO = '".$search_str."' OR A.ORDER_GOODS_NO = '".$search_str."' OR A.RGN_NO = '".$search_str."') ";
				} else if ($search_field == "IN_LOC_EXT"){
					$query .= " AND (A.IN_LOC_EXT LIKE '%".$search_str."%') ";
				} else if ($search_field == "MEMO"){
					$query .= " AND (A.MEMO LIKE '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			if ($order_field == "") 
				$order_field = "CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END";

			if ($order_str == "") 
				$order_str = "DESC";

			$query .= " ORDER BY ".$order_field." ".$order_str.", A.STOCK_NO ".$order_str." limit ".$offset.", 1000000";
			$query .= " ) O ";
		
		} else { 

			$base_date = $option['BASE_DATE'];
			$stock_no = $option['STOCK_NO'];

			$query = "SELECT SUM( IN_QTY + IN_BQTY + IN_FQTY - OUT_QTY - OUT_BQTY ) AS SUM_PREV_QTY, 
							 GOODS_NO
						FROM ( 

							SELECT  A.STOCK_NO, A.STOCK_TYPE,
									 CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END AS BASE_DATE,
									 A.GOODS_NO, 
									 A.IN_LOC, A.IN_LOC_EXT, A.IN_QTY, A.IN_BQTY,
									 A.IN_FQTY, A.OUT_QTY, A.OUT_BQTY
							FROM TBL_STOCK A 
							JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
							WHERE 1 = 1 AND CLOSE_TF = 'N' ";

			if ($stock_type <> "") {
				$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
			}

			if ($stock_code <> "") {
				$query .= " AND A.STOCK_CODE LIKE '".$stock_code."%' ";
			} 

			if ($in_cp_no <> "") {
				$query .= " AND A.IN_CP_NO = '".$in_cp_no."' ";
			} 

			if ($out_cp_no <> "") {
				$query .= " AND A.OUT_CP_NO = '".$out_cp_no."' ";
			} 

			if ($loc <> "") {
				$query .= " AND A.IN_LOC = '".$loc."' ";
			} 

			if ($del_tf <> "") {
				$query .= " AND A.DEL_TF = '".$del_tf."' ";
			}

			if ($search_str <> "") {
				if ($search_field == "ALL") {
					$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_CODE = '".$search_str."')  ";
				} else if ($search_field == "GOODS_NAME") {
					$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
				} else if ($search_field == "GOODS_NO") {
					$query .= " AND B.GOODS_NO = '".$search_str."' ";
				} else if ($search_field == "GOODS_CODE"){
					$query .= " AND B.GOODS_CODE = '".$search_str."' ";
				} else if ($search_field == "RESERVE_NO"){
					$query .= " AND (A.RESERVE_NO = '".$search_str."' OR A.ORDER_GOODS_NO = '".$search_str."' OR A.RGN_NO = '".$search_str."') ";
				} else if ($search_field == "IN_LOC_EXT"){
					$query .= " AND (A.IN_LOC_EXT LIKE '%".$search_str."%') ";
				} else if ($search_field == "MEMO"){
					$query .= " AND (A.MEMO LIKE '%".$search_str."%') ";
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}

			/*
			if ($order_field == "") 
				$order_field = "CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END";

			if ($order_str == "") 
				$order_str = "DESC";
			*/

			//$query .= " ORDER BY ".$order_field." ".$order_str.", A.STOCK_NO ".$order_str." limit ".$offset.", 1";
			$query .= " AND DATE_FORMAT(CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END, \"%Y-%m-%d\") <= '$base_date' AND A.STOCK_NO < '$stock_no' ";
			$query .= " ) O ";

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


	//입출고관리 페이지 - 합계
	function totalStockInOut($db, $search_date_type, $start_date, $end_date, $stock_code, $search_field, $search_str) {

		if($search_field == "IN_LOC_EXT" || $search_field == "MEMO" || $search_str == "") return;

		$query = "SELECT A.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, 
						 SUM(A.IN_FQTY) AS SUM_IN_FQTY, SUM(A.IN_QTY) AS SUM_IN_QTY, SUM(A.IN_BQTY) AS SUM_IN_BQTY, SUM(A.OUT_TQTY) AS SUM_OUT_TQTY, SUM(A.OUT_QTY) AS SUM_OUT_QTY, SUM(A.OUT_BQTY) AS SUM_OUT_BQTY,
						 SUM(A.IN_FQTY + A.IN_QTY + A.IN_BQTY) AS SUM_TOTAL_IN_QTY,
						 SUM(A.OUT_QTY + A.OUT_BQTY) AS SUM_TOTAL_OUT_QTY
										 
					FROM TBL_STOCK A 
					JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
					LEFT JOIN TBL_ORDER O ON O.RESERVE_NO = A.RESERVE_NO 
					WHERE A.CLOSE_TF = 'N' AND A.DEL_TF = 'N' ";

		if($search_date_type == "inout_date" || $search_date_type == "") { 
			if ($start_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND A.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND A.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE LIKE '".$stock_code."%' ";
		}	

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE = '".$search_str."' OR A.RESERVE_NO LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else if ($search_field == "RESERVE_NO"){
				$query .= " AND A.RESERVE_NO LIKE '%".$search_str."%' ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " GROUP BY A.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE ";

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



	function updateWorksDone($db, $work_type, $order_goods_no, $done_today_qty, $confirm_adm) {

		$refund_able_qty = getRefundAbleQty($db, "", $order_goods_no);
		if($refund_able_qty <= 0) return;

		//전체 작업완료된 주문 수량 체크
		$query = "SELECT O.CP_NO, OG.GOODS_NO, OG.WORK_QTY, OG.QTY, OG.BUY_PRICE, OG.RESERVE_NO, OG.WORK_REQ_QTY
		            FROM TBL_ORDER O 
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
				   WHERE OG.ORDER_GOODS_NO = '".$order_goods_no."' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$out_cp_no  = $rows[0];
		$goods_no   = $rows[1];
		$work_qty   = $rows[2];
		//$QTY  = $rows[3];
		$buy_price   = $rows[4];
		$reserve_no  = $rows[5];
		$work_req_qty= $rows[6];

		if($work_qty == null)
			$work_qty = 0;

		if($work_qty + $done_today_qty > $refund_able_qty)
			return;
		else if($work_qty + $done_today_qty == $refund_able_qty) { 
			
			//작업 전체 완료
			$query = "UPDATE TBL_ORDER_GOODS 
			             SET WORK_QTY = '".($work_qty + $done_today_qty)."', 
						     WORK_REQ_QTY = 0,
						     WORK_FLAG = 'Y',
						     WORK_END_DATE = now()
					   WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";
			//echo $query."<br/>";
								
			mysql_query($query,$db);
		} else { 
			
			//전체 완료 되지 않아 작업 수량만 증가 
			
			$query = "UPDATE TBL_ORDER_GOODS 
			             SET WORK_QTY = '".($work_qty + $done_today_qty)."',
						     WORK_REQ_QTY = WORK_REQ_QTY - $done_today_qty " ;

			if($work_req_qty == $done_today_qty)
				$query .= "	             ,
							 WORK_SEQ = 0,
							 WORK_START_DATE = '0000-00-00 00:00:00'
						  ";

			$query .= "WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";
			//echo $query."<br/>";
								
			mysql_query($query,$db);

		}

		//완료 기록 추가
		$query = "INSERT INTO TBL_ORDER_WORK_HISTORY(WORK_TYPE, ORDER_GOODS_NO, QTY, REG_DATE, REG_ADM) 
					   VALUES ('$work_type', '$order_goods_no', '$done_today_qty', now(), $confirm_adm);
				 ";
		//echo $query."<br/>";
	    //exit;
		if(mysql_query($query,$db))
		{
			$work_done_no = mysql_insert_id();

			$in_qty			= 0;
			$in_bqty		= 0;
			$in_fbqty		= 0;
			$out_bqty		= 0;
			$out_tqty	    = 0; 
			$in_price		= 0;
			$in_date		= "";
			$out_date		= date("Y-m-d H:i:s",strtotime("0 month"));
			$pay_date		= "";
			$close_tf		= "N";
			


			// 세트품의 구성품을 출고하고 완성된 세트품을 입고
			// 세트품의 구성품 혹은 단품만 출고 2017-05-24

			$query = "SELECT OG.GOODS_NO, OG.DELIVERY_CNT_IN_BOX, GS.GOODS_SUB_NO, GS.GOODS_CNT, G.GOODS_CATE, G.BUY_PRICE
						FROM TBL_ORDER_GOODS OG
						JOIN TBL_GOODS_SUB GS ON OG.GOODS_NO = GS.GOODS_NO
						JOIN TBL_GOODS G ON G.GOODS_NO = GS.GOODS_SUB_NO
						WHERE OG.ORDER_GOODS_NO = '$order_goods_no' ";

			$result = mysql_query($query,$db);
			$record = array();
			

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);
				}
			}

			if(sizeof($record) > 0) { //세트품인경우

				for($i=0 ; $i< sizeof($record); $i++) {

					$GOODS_NO			= Trim($record[$i]["GOODS_NO"]);
					$GOODS_SUB_NO		= Trim($record[$i]["GOODS_SUB_NO"]);
					$GOODS_CNT			= Trim($record[$i]["GOODS_CNT"]);
					$DELIVERY_CNT_IN_BOX= Trim($record[$i]["DELIVERY_CNT_IN_BOX"]);
					$GOODS_CATE			= Trim($record[$i]["GOODS_CATE"]);
					$BUY_PRICE			= Trim($record[$i]["BUY_PRICE"]);

					//오늘 작업수량(구성품 수 * 오늘 작업 수량)
					$done_today_sub_qty = (startsWith("010202", $GOODS_CATE) ? ceil(($GOODS_CNT * $done_today_qty) / $DELIVERY_CNT_IN_BOX) : $GOODS_CNT * $done_today_qty);

					// 세트품의 구성품을 출고
					$stock_type     = "OUT";         //입출고 구분 (출고) 
					$stock_code     = "NOUT90";      //출고 구분코드
					$in_cp_no		= "";	         // 입고 업체
					$goods_no		= $GOODS_SUB_NO;	
					$in_loc			= "LOCA";        
					$in_loc_ext	    = "세트전환출고";
					$out_qty		= $done_today_sub_qty;
					$out_price	    = (startsWith("010202", $GOODS_CATE) ? $BUY_PRICE / $DELIVERY_CNT_IN_BOX : $BUY_PRICE) ; 
					switch($work_type) { 
						case "WORK_DONE": $memo = "출고대기"; break;
						case "WORK_SENT": $memo = "즉시출고"; break;
					}
					
					
					if($done_today_sub_qty > 0) { 

						$out_result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $confirm_adm, $memo);

						$query = "UPDATE TBL_STOCK 
									 SET WORK_DONE_NO = '$work_done_no' 
								   WHERE STOCK_NO  = '$out_result'
								 ";
						//echo $query."<br/>";
						//exit;
						mysql_query($query,$db);
					}
				
				}

				
				//완성된 입고 없이 출고만 진행 2017-05-24 

				// 완성된 세트품을 입고
				if($work_type == 'WORK_DONE') { //출고대기
					$arr_og = selectOrderGoods($db, $order_goods_no);

					if(sizeof($arr_og) > 0) { 

						$GOODS_NO			= trim($arr_og[0]["GOODS_NO"]);
						$BUY_PRICE			= trim($arr_og[0]["BUY_PRICE"]);
						
						$stock_type     = "IN";          //입출고 구분 (입고) 
						$stock_code     = "NST95";		 //입고 구분코드 (조립입고)
						$in_cp_no		= "1";	         // 입고 업체 (운영 - 기프트넷)
						$out_cp_no	    = "";			 // 출고업체 

						$goods_no		= $GOODS_NO;	
						$in_loc			= "LOCA";        
						$in_loc_ext	    = "세트전환입고";
						$in_qty			= $done_today_qty;
						$in_bqty		= 0;
						$in_fbqty		= 0;
						$out_qty		= 0;
						$out_bqty		= 0;
						$out_tqty	    = 0; 
						$in_price		= $BUY_PRICE;
						$out_price	    = 0;     
						$in_date		= date("Y-m-d H:i:s",strtotime("1 second"));
						$out_date		= "";
						$pay_date		= "";
						$close_tf		= "N";

						if($done_today_qty > 0) { 
							//$in_result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $confirm_adm, $memo);

							
							$keys = array();
							$keys['RESERVE_NO'] = $reserve_no;
							$keys['ORDER_GOODS_NO'] = $order_goods_no;
							$keys['WORK_DONE_NO'] = $work_done_no;
							
							insertEachStock($db, 'WH004', 'IN', '정상', $goods_no, 1, $in_qty, $in_date, $in_loc_ext, $keys, $confirm_adm);
						}
					}
				}		
			} 
			else { //세트품이 아니고 단품인 경우

				$stock_type     = "OUT";         //입출고 구분 (출고) 
				$stock_code     = "NOUT01";      //출고 구분코드 (정상출고)
				$in_cp_no		= "";	         // 입고 업체

				$in_loc			= "LOCA";        // 창고 구분 디폴트 창고 A, 클레임 있을시 B
				$in_loc_ext	    = "단품 출고";
				$out_qty		= $done_today_qty;
				$out_price	    = $buy_price;     
				switch($work_type) { 
					case "WORK_DONE": $memo = "출고대기"; break;
					case "WORK_SENT": $memo = "즉시출고"; break;
				}
				
				$out_result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $confirm_adm, $memo);

				$query = "UPDATE TBL_STOCK 
							 SET WORK_DONE_NO = '$work_done_no' 
						   WHERE STOCK_NO  = '$out_result'
						 ";
				//echo $query."<br/>";
				//exit;
				mysql_query($query,$db);


				if($work_type == 'WORK_DONE') { //출고대기

					$keys = array();
					$keys['RESERVE_NO'] = $reserve_no;
					$keys['ORDER_GOODS_NO'] = $order_goods_no;
					$keys['WORK_DONE_NO'] = $work_done_no;
					
					insertEachStock($db, 'WH004', 'IN', '정상', $goods_no, 1, $out_qty, $out_date, $in_loc_ext, $keys, $confirm_adm);

				}
			}

		}

		return $order_goods_no;
	}

	//매입확정
	function updateGoodsRequestConfirm($db, $req_goods_no, $reg_adm) {

		$query="UPDATE TBL_GOODS_REQUEST_GOODS 
				   SET CONFIRM_TF = 'Y', CONFIRM_DATE = now(), CONFIRM_ADM = '$reg_adm'
				 WHERE REQ_GOODS_NO = '$req_goods_no' AND CONFIRM_TF = 'N' ; ";

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


	//발주 수령 상황
	function cntRequestGoodsState($db, $start_date, $end_date) { 

		$query .= "
				  SELECT COUNT(*) AS CNT, '발주상품' AS GRG_TYPE 
				    FROM TBL_GOODS_REQUEST_GOODS 
				   WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'  ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		$query .= "
		
			   UNION ALL
				  SELECT COUNT(*) AS CNT, ' [총 자체수령' AS GRG_TYPE 
					FROM TBL_GOODS_REQUEST_GOODS
				   WHERE TO_HERE = 'Y' AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		$query .= "
		
			   UNION ALL
				  SELECT COUNT(*) AS CNT, ' =  수령완료' AS GRG_TYPE 
					FROM TBL_GOODS_REQUEST_GOODS
				   WHERE RECEIVE_DATE <> '0000-00-00 00:00:00' AND TO_HERE = 'Y' AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		$query .= "

				   UNION ALL

				  SELECT COUNT(*) AS CNT, '( 기장완료' AS GRG_TYPE 
				    FROM TBL_GOODS_REQUEST_GOODS 
				   WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'  AND TO_HERE = 'Y' AND CONFIRM_TF = 'Y'  ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}
		
		$query .= "UNION ALL

				  SELECT COUNT(*) AS CNT, ') + 미수령' AS GRG_TYPE 
				    FROM TBL_GOODS_REQUEST_GOODS 
				   WHERE RECEIVE_DATE = '0000-00-00 00:00:00' AND TO_HERE = 'Y' AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}


		$query .= "
		
			   UNION ALL
				  SELECT COUNT(*) AS CNT, ']  총 직송' AS GRG_TYPE 
					FROM TBL_GOODS_REQUEST_GOODS
				   WHERE TO_HERE = 'N' AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		$query .= "
			 UNION ALL
			    SELECT COUNT(*) AS CNT, '중 기장완료' AS GRG_TYPE 
				  FROM TBL_GOODS_REQUEST_GOODS 
				 WHERE DEL_TF = 'N' AND CANCEL_TF = 'N'  AND TO_HERE = 'N' AND CONFIRM_TF = 'Y'  ";
		
		
		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}
					
		
		/*
		$query .= "	   
				   UNION ALL

				  SELECT COUNT(*) AS CNT, '장기미수령(7일이상)' AS GRG_TYPE 
				    FROM TBL_GOODS_REQUEST_GOODS GRG 
					JOIN TBL_GOODS_REQUEST GR ON GRG.REQ_NO = GR.REQ_NO
				   WHERE GRG.RECEIVE_DATE = '0000-00-00 00:00:00' AND GRG.DEL_TF = 'N' AND GRG.CANCEL_TF = 'N' AND GR.SENT_DATE > ( DATE(NOW()) - INTERVAL 7 DAY )";
		
		
		if ($start_date <> "") {
			$query .= " AND GRG.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GRG.REG_DATE <= '".$end_date." 23:59:59' ";
		}
		*/
					


		

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

	function updateStatusFStockCancel($db, $cancel_order_goods_no, $cancel_qty, $goods_no, $upd_adm) { 

		$claim_date	= date("Y-m-d H:i",strtotime("0 month"));
		$memo = " 클레임취소(".$claim_date.") ";

		$cancel_qty = -1 * $cancel_qty;
		$query = "UPDATE TBL_STOCK 
					 SET IN_FQTY = IN_FQTY + (".$cancel_qty."), DEL_TF = 'Y', DEL_ADM = '".$upd_adm."', DEL_DATE = now(), MEMO = CONCAT(MEMO, '".$memo."')
				   WHERE ORDER_GOODS_NO =  '".$cancel_order_goods_no."' AND STOCK_TYPE = 'IN' AND STOCK_CODE = 'FST02' AND DEL_TF = 'N' AND CLOSE_TF = 'N' ; 
				 ";
		mysql_query($query,$db);

		//echo $query."<br/>";
		//exit;

		if(mysql_affected_rows() > 0) { 
			$query = "UPDATE TBL_GOODS 
						 SET FSTOCK_CNT = FSTOCK_CNT + (".$cancel_qty.")
					   WHERE GOODS_NO =  '".$goods_no."' ; 
					 ";
			mysql_query($query,$db);
		}

	}

	/*
	// 선출고 기능 삭제 2017-05-10
	function updateStatusTStockCancel($db, $order_goods_no, $is_cancelling, $cancel_qty) { 

		$query = "SELECT OG.GOODS_NO, OG.QTY, G.DELIVERY_CNT_IN_BOX, OG.GROUP_NO
					FROM TBL_ORDER_GOODS OG JOIN TBL_GOODS G ON OG.GOODS_NO = G.GOODS_NO
				   WHERE OG.ORDER_GOODS_NO = '".$order_goods_no."' ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$goods_no			 = $rows[0];
		//$qty				 = $rows[1];
		$delivery_cnt_in_box = $rows[2];
		$group_no			 = $rows[3];

		if($group_no != "0")
			$order_goods_no = $group_no;

		//echo "order_goods_no:".$order_goods_no.",goods_no:".$goods_no.",cancel_qty:".$cancel_qty."<br/>";

		$claim_date	= date("Y-m-d H:i",strtotime("0 month"));
		if($is_cancelling != "Y") { 
			$cancel_qty = -1 * $cancel_qty;
			$memo = " 클레임취소복귀(".$claim_date.") ";
		} else 
			$memo = " 클레임취소(".$claim_date.") ";

		$arr_goods = selectGoodsSub($db, $goods_no);

		if(sizeof($arr_goods) > 0) { 

			for($i = 0; $i < sizeof($arr_goods); $i ++) { 

				$goods_sub_no = $arr_goods[$i]["GOODS_SUB_NO"];
				$goods_cnt    = $arr_goods[$i]["GOODS_CNT"];
				$goods_cate   = $arr_goods[$i]["GOODS_CATE"];

				
				if(startsWith("010202", $goods_cate)) 
					$total_cancel_qty = ceil(($cancel_qty * $goods_cnt) / $delivery_cnt_in_box) ;
				else
					$total_cancel_qty = $cancel_qty * $goods_cnt;
				
				$reverse_total_cancel_qty = -1 * $total_cancel_qty;

				$query = "UPDATE TBL_STOCK 
							 SET OUT_TQTY = OUT_TQTY + ($reverse_total_cancel_qty), MEMO = CONCAT(MEMO, '$memo')
						   WHERE RESERVE_NO =  '".$order_goods_no."' AND GOODS_NO = '$goods_sub_no' AND OUT_TQTY <> '' ; 
						 ";
				mysql_query($query,$db);

				if(mysql_affected_rows() > 0) { 
					$query = "UPDATE TBL_GOODS 
								 SET TSTOCK_CNT = TSTOCK_CNT + ($total_cancel_qty)
							   WHERE GOODS_NO =  '$goods_sub_no' ; 
							 ";
					mysql_query($query,$db);
				}
			}

		} else { 

			$reverse_cancel_qty = -1 * $cancel_qty;

			$query = "UPDATE TBL_STOCK 
						 SET OUT_TQTY = OUT_TQTY + ($reverse_cancel_qty), MEMO = CONCAT(MEMO, '$memo')
					   WHERE RESERVE_NO =  '".$order_goods_no."' AND OUT_TQTY <> '' ; 
					 ";
			mysql_query($query,$db);

			if(mysql_affected_rows() > 0) { 
				$query = "UPDATE TBL_GOODS 
							 SET TSTOCK_CNT = TSTOCK_CNT + ($cancel_qty)
						   WHERE GOODS_NO =  '$goods_no' ; 
						 ";
				mysql_query($query,$db);
			}

		}


	}
	*/

	/*
	// 선출고 기능 삭제 2017-03-21
	function deleteTStock($db, $order_goods_no, $del_adm) { 

		$query = "SELECT STOCK_NO 
		            FROM TBL_STOCK 
		           WHERE RESERVE_NO = '".$order_goods_no."' 
				     AND STOCK_TYPE = 'OUT'
					 AND STOCK_CODE = 'TOUT96'
					 AND CLOSE_TF = 'N'
					 AND DEL_TF = 'N' ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {

			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$stock_no = $record[$i]["STOCK_NO"];

				deleteStock($db, $stock_no, $del_adm);
			}
		}
	}
	*/


	function deleteFStock($db, $rgn_no, $del_adm) { 

		$query = "SELECT STOCK_NO 
		            FROM TBL_STOCK 
		           WHERE RGN_NO = '".$rgn_no."' 
				     AND STOCK_TYPE = 'IN'
					 AND STOCK_CODE = 'FST02'
					 AND CLOSE_TF = 'N'
					 AND DEL_TF = 'N' ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {

			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$stock_no = $record[$i]["STOCK_NO"];

				deleteStock($db, $stock_no, $del_adm);
			}
		}
	}

	//출고 - 작업장
	function listOrderGoodsByDeliveryNo($db, $delivery_no)
	{
		//G.KANCODE,							G.KANCODE_BOX,
		$query = "SELECT OGD.DELIVERY_NO, S.DELIVERY_GOODS_SEQ, '' AS RESERVE_NO, OGD.ORDER_MANAGER_NM AS CP_NO, S.GOODS_NO,  G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, G.DELIVERY_CNT_IN_BOX, S.GOODS_TOTAL, S.SCAN_CNT, G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150, S.STICKER_NO
					FROM TBL_TEMP_ORDER_GOODS_SCAN S
					JOIN TBL_GOODS G ON S.GOODS_NO = G.GOODS_NO
					JOIN TBL_ORDER_GOODS_DELIVERY OGD ON OGD.ORDER_GOODS_DELIVERY_NO = S.ORDER_GOODS_DELIVERY_NO
					WHERE OGD.DELIVERY_NO =  '$delivery_no' AND OGD.USE_TF = 'Y' AND OGD.DEL_TF = 'N' ";
		

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

	
	function insertFStock($db, $req_no, $reg_adm) { 

		$arr_gr = selectGoodsRequestByReqNo($db, $req_no);
		$BUY_CP_NO			 = $arr_gr[0]["BUY_CP_NO"];

		$arr = listGoodsRequestGoods($db, $req_no, 'N');
		
		for ($k = 0; $k < sizeof($arr); $k++) {

			$REQ_GOODS_NO		 = $arr[$k]["REQ_GOODS_NO"];
			$ORDER_GOODS_NO		 = $arr[$k]["ORDER_GOODS_NO"];
			$GOODS_NO			 = $arr[$k]["GOODS_NO"];
			$REQ_QTY			 = $arr[$k]["REQ_QTY"];
			$BUY_PRICE			 = $arr[$k]["BUY_PRICE"];
			$MEMO2				 = $arr[$k]["MEMO2"];
			$TO_HERE			 = $arr[$k]["TO_HERE"];
			
			//직송일때는 LOCC(직배송)
			//변경 - 직송일때는 가입고 잡지 않음
			if($TO_HERE != 'Y') continue;

			$datetime_now = date("Y-m-d H:i:s",strtotime("0 month"));

			$stock_type = "IN";
			$stock_code = "FST02";
			$in_qty		= 0;
			$in_bqty	= 0;
			$in_fqty	= $REQ_QTY;
			$in_cp_no   = $BUY_CP_NO; //LG 생활건강
			$out_cp_no  = 0;
			$in_loc     = "LOCA"; 
			$in_loc_ext = "발주 (".$datetime_now.")";
			$out_qty    = 0;
			$out_bqty   = 0;
			$out_tqty   = 0;
			$in_price   = $BUY_PRICE;
			$out_price  = 0;
			$in_date    = $datetime_now;
			$out_date   = "";
			$pay_date   = "";
			$close_tf   = "N"; 
			$memo       = $MEMO2;
			
			if(chkDuplicateStockReserveNo($db, $REQ_GOODS_NO) <= 0)
				$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $ORDER_GOODS_NO, $REQ_GOODS_NO, $close_tf, $reg_adm, $memo);
		}

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

	//발주확인하고 발주상태입력
	function updateGoodsRequestSent($db, $request_type, $req_no) {

		$query="UPDATE TBL_GOODS_REQUEST
				   SET IS_SENT = 'Y',
					   SENT_DATE = now(),
					   REQUEST_TYPE = '$request_type'
				 WHERE REQ_NO = '$req_no' ";
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

	function selectGoodsRequestGoods($db, $req_goods_no, $cancel_tf) {

		$query = "SELECT GRG.REQ_GOODS_NO, GRG.ORDER_GOODS_NO, GRG.GOODS_NO, GRG.GOODS_CODE, GRG.GOODS_NAME, GRG.GOODS_SUB_NAME, GRG.BUY_PRICE, 
						 GRG.REQ_QTY, GRG.BUY_TOTAL_PRICE, 
						 GRG.RECEIVE_QTY, GRG.RECEIVE_DATE, GRG.REASON, 
						 GRG.TO_HERE, GRG.RECEIVER_NM, GRG.RECEIVER_ADDR, GRG.RECEIVER_PHONE, GRG.RECEIVER_HPHONE, GRG.MEMO1, GRG.MEMO2, GRG.UP_ADM, GRG.UP_DATE, GRG.CANCEL_TF, GRG.CANCEL_DATE, GRG.CANCEL_ADM, GRG.CONFIRM_TF, GRG.CONFIRM_DATE
					FROM TBL_GOODS_REQUEST_GOODS GRG 
					JOIN TBL_GOODS G ON GRG.GOODS_NO = G.GOODS_NO
				   WHERE GRG.REQ_GOODS_NO = '".$req_goods_no."' AND GRG.DEL_TF = 'N' ";

		if ($cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$cancel_tf."' ";
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

	function insertFStockByGoods($db, $req_goods_no) { 

		$query = "SELECT BUY_CP_NO
					FROM TBL_GOODS_REQUEST GR 
					JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
				   WHERE GRG.REQ_GOODS_NO = '".$req_goods_no."' AND GR.DEL_TF = 'N' AND GRG.CANCEL_TF = 'N' AND GRG.DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$BUY_CP_NO			 = $rows[0];

		$arr = selectGoodsRequestGoods($db, $req_goods_no, 'N');
		
		for ($k = 0; $k < sizeof($arr); $k++) {


			$REQ_GOODS_NO		 = $arr[$k]["REQ_GOODS_NO"];
			$GOODS_NO			 = $arr[$k]["GOODS_NO"];
			$REQ_QTY			 = $arr[$k]["REQ_QTY"];
			$BUY_PRICE			 = $arr[$k]["BUY_PRICE"];
			$MEMO2				 = $arr[$k]["MEMO2"];
			$TO_HERE			 = $arr[$k]["TO_HERE"];
			
			//직송일때는 LOCC(직배송)
			//변경 - 직송일때는 가입고 잡지 않음
			if($TO_HERE != 'Y') continue;

			$datetime_now = date("Y-m-d H:i:s",strtotime("0 month"));

			$stock_type = "IN";
			$stock_code = "FST02";
			$in_qty		= 0;
			$in_bqty	= 0;
			$in_fqty	= $REQ_QTY;
			$in_cp_no   = $BUY_CP_NO; //LG 생활건강
			$out_cp_no  = 0;
			$in_loc     = "LOCA"; 
			$in_loc_ext = "발주 (".$datetime_now.")";
			$out_qty    = 0;
			$out_bqty   = 0;
			$out_tqty   = 0;
			$in_price   = $BUY_PRICE;
			$out_price  = 0;
			$in_date    = $datetime_now;
			$out_date   = "";
			$pay_date   = "";
			$close_tf   = "N"; 
			$memo       = $MEMO2;
			
			if(chkDuplicateStockReserveNo($db, $REQ_GOODS_NO) <= 0)
				$result = insertStock($db, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $GOODS_NO, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $REQ_GOODS_NO, $close_tf, $s_adm_no, $memo);
		}

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


	function chkDuplicateStockReserveNo($db, $rgn_no)
	{
		$query = "SELECT COUNT(*)
					FROM TBL_STOCK 
				   WHERE DEL_TF = 'N' AND STOCK_TYPE = 'IN' AND RGN_NO = '$rgn_no' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}


	function listEachStockHistory($db, $warehouse, $start_date, $end_date) { 

		$query = "
			
			SELECT SE.STOCK_TYPE, SE.STOCK_CODE, 
				   G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME,
				   SE.NQTY, SE.BQTY, SE.INOUT_DATE, SE.MEMO
			FROM TBL_STOCK_EACH SE 
			JOIN TBL_GOODS G ON SE.GOODS_NO = G.GOODS_NO
			WHERE SE.DEL_TF = 'N' AND SE.WAREHOUSE = '".$warehouse."' ";
	
		if($start_date <> "")
			$query .= " AND SE.INOUT_DATE < '".$start_date."' ";

		if($end_date <> "")
			$query .= " AND SE.INOUT_DATE >= '".$end_date." 23:59:59' ";

		$query .= "	ORDER BY INOUT_DATE DESC ";


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


	///////////////////////////////////////////////////////////////////////////////
	//       개별 창고 관리
	///////////////////////////////////////////////////////////////////////////////

	function insertEachStock($db, $warehouse_code, $stock_type, $stock_code, $goods_no, $delivery_cnt_in_box, $box_cnt, $inout_date, $memo, $keys, $reg_adm) { 

		$RESERVE_NO		= $keys['RESERVE_NO'];
		$ORDER_GOODS_NO = $keys['ORDER_GOODS_NO'];
		$WORK_DONE_NO	= $keys['WORK_DONE_NO'];

		$in_nqty = 0;
		$out_nqty = 0;
		$in_bqty = 0;
		$out_bqty = 0;

		if($stock_type == "IN") { 
			if($stock_code == "정상")   
				$in_nqty = $box_cnt;
			else 
				$in_bqty = $box_cnt;
			
		} else { 
			if($stock_code == "정상") 
				$out_nqty = $box_cnt;
			else 
				$out_bqty = $box_cnt;

		}

		$query1 = "";
		$query2 = "";

		if($RESERVE_NO <> "") { 
			$query1 .= "RESERVE_NO, ";
			$query2 .= " '".$RESERVE_NO."', ";
		}
		if($ORDER_GOODS_NO <> "") { 
			$query1 .= "ORDER_GOODS_NO, ";
			$query2 .= " '".$ORDER_GOODS_NO."', ";
		}
		if($WORK_DONE_NO <> "") { 
			$query1 .= "WORK_DONE_NO, ";
			$query2 .= " '".$WORK_DONE_NO."', ";
		}

		if($query1 <> "" || $query2 <> "") { 
			$query1 = ", ".rtrim($query1,", ");
			$query2 = ", ".rtrim($query2,", ");		
		}

		$query="INSERT INTO TBL_STOCK_EACH
					  (WAREHOUSE, STOCK_TYPE, STOCK_CODE, GOODS_NO, DELIVERY_CNT_IN_BOX, IN_NQTY, IN_BQTY, OUT_NQTY, OUT_BQTY, INOUT_DATE, MEMO, REG_ADM, REG_DATE".$query1.") 
			    VALUES 
					  ('$warehouse_code', '$stock_type', '$stock_code', '$goods_no', '$delivery_cnt_in_box', '$in_nqty', '$in_bqty', '$out_nqty', '$out_bqty', '$inout_date', '$memo', '$reg_adm', now()".$query2."); ";
		

		//echo $query."<br>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	function listEachStock($db, $warehouse_code, $search_field, $search_str, $order_field, $order_str) {

		$query = "SELECT SE.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME,
						 SUM(SE.DELIVERY_CNT_IN_BOX * SE.IN_NQTY) - SUM(SE.DELIVERY_CNT_IN_BOX * SE.OUT_NQTY) AS N_TOTAL, 
						 SUM(SE.DELIVERY_CNT_IN_BOX * SE.IN_BQTY) - SUM(SE.DELIVERY_CNT_IN_BOX * SE.OUT_BQTY) AS B_TOTAL
					FROM TBL_STOCK_EACH SE 
					JOIN TBL_GOODS G ON SE.GOODS_NO = G.GOODS_NO
				   WHERE WAREHOUSE = '$warehouse_code' AND SE.DEL_TF = 'N'  ";

		if ($search_str <> "") {
			if($search_field == "ALL") 
				$query .= " AND (G.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " GROUP BY G.GOODS_CODE, G.GOODS_NAME ";

		if ($order_field == "") 
			$order_field = "G.GOODS_NAME";

		if ($order_str == "") 
			$order_str = "ASC";

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


	//입출고관리 페이지 (수량)
	function totalCntEachStockInOut($db, $warehouse_code, $start_date, $end_date, $stock_type, $stock_code, $search_field, $search_str) {

		$query = "SELECT COUNT(*) AS CNT
								FROM TBL_STOCK_EACH A 
								JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
								WHERE A.DEL_TF = 'N' AND A.WAREHOUSE = '$warehouse_code' ";

		if ($start_date <> "") {
			$query .= " AND A.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE= '".$stock_code."' ";
		} 


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_NO = '".$search_str."' OR B.GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
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

	//입출고관리 페이지 - 합계
	function listEachStockInOut($db, $warehouse_code, $start_date, $end_date, $stock_type, $stock_code, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT  @rownum:= @rownum - 1  as rn, A.SE_NO, A.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, A.INOUT_DATE, A.STOCK_TYPE, A.STOCK_CODE, A.DELIVERY_CNT_IN_BOX, A.IN_NQTY, A.IN_BQTY, A.OUT_NQTY, A.OUT_BQTY, A.MEMO, A.REG_DATE, A.RESERVE_NO, A.ORDER_GOODS_NO
										 
					FROM TBL_STOCK_EACH A 
					JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
					WHERE A.DEL_TF = 'N' AND A.WAREHOUSE = '$warehouse_code' ";

		if ($start_date <> "") {
			$query .= " AND A.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($stock_type <> "") {
			$query .= " AND A.STOCK_TYPE= '".$stock_type."' ";
		}

		if ($stock_code <> "") {
			$query .= " AND A.STOCK_CODE= '".$stock_code."' ";
		} 

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%' OR B.GOODS_NO = '".$search_str."' OR B.GOODS_CODE = '".$search_str."' ) ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (B.GOODS_NAME like '%".$search_str."%' OR B.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND B.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND B.GOODS_CODE = '".$search_str."' ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "A.REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", A.SE_NO ".$order_str." limit ".$offset.", ".$nRowCount;

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

	function deleteEachStock($db, $stock_no, $reg_adm) {

		$query="UPDATE TBL_STOCK_EACH SET DEL_TF = 'Y', DEL_DATE = now(), DEL_ADM = '$reg_adm'  WHERE SE_NO = '$stock_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//발주서에서 발주 상품에 대한 공급가와 수량을 변경했을때
	function updateStockFromRequestGoods($db, $goods_no, $in_fqty, $in_price, $rgn_no, $up_adm, $memo) {
		
		$query = "SELECT STOCK_TYPE, GOODS_NO, IN_FQTY, STOCK_NO
		            FROM TBL_STOCK 
				   WHERE RGN_NO = '$rgn_no' AND GOODS_NO = '$goods_no' AND STOCK_CODE = 'FST02' AND DEL_TF = 'N' AND CLOSE_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$STOCK_TYPE			= $rows[0];
		$GOODS_NO			= $rows[1];
		$IN_FQTY			= $rows[2];
		$STOCK_NO			= $rows[3];

		//2017-06-28 일부 입고후에 가입고 수량 변경되었을때, 기존 입고분에 대해 차감하고 새로 가입고 입력해야 함
		$query = "SELECT IFNULL(SUM(IN_QTY), 0) AS SUM_IN_QTY, IFNULL(SUM(IN_BQTY), 0) AS SUM_IN_BQTY
		            FROM TBL_STOCK 
				   WHERE RGN_NO = '$rgn_no' AND GOODS_NO = '$goods_no' AND STOCK_CODE IN ('NST01', 'BST03') AND DEL_TF = 'N' AND CLOSE_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$SUM_IN_QTY				= $rows[0];
		$SUM_IN_BQTY			= $rows[1];

		/*
		if ($STOCK_TYPE == "IN") {
			$query = "UPDATE TBL_GOODS SET  
												FSTOCK_CNT = FSTOCK_CNT - $IN_FQTY 
								 WHERE GOODS_NO = '$GOODS_NO' ";
			mysql_query($query,$db);
		}
		*/


		//2017-06-28 일부 선입고분에 대해서 공급가 가격 변경
		$query = "UPDATE TBL_STOCK
					 SET IN_PRICE				= '$in_price'
				   WHERE RGN_NO = '$rgn_no' AND GOODS_NO = '$goods_no' AND STOCK_CODE IN ('NST01', 'BST03') AND DEL_TF = 'N' AND CLOSE_TF = 'N' ";
		mysql_query($query,$db);
		
		//선체크된 정상 + 불량 수량을 발주 수정 수량에서 차감하고 가입고 입력
		$REST_FQTY  = $in_fqty - ($SUM_IN_QTY + $SUM_IN_BQTY);
		$query="UPDATE TBL_STOCK SET 
							
							IN_FQTY					= '$REST_FQTY',
							IN_PRICE				= '$in_price',
							MEMO					= CONCAT(MEMO, '$memo')
						WHERE STOCK_NO				= '$STOCK_NO' ";
		
		//echo $query;
		//exit;
		mysql_query($query,$db);

		syncGoodsStock($db, $GOODS_NO);

	}

	function insertRequestGoodsHistory($db, $req_no, $req_goods_no, $goods_no, $buy_price, $req_qty, $reg_adm) { 

		$query = "SELECT BUY_PRICE, REQ_QTY
		            FROM TBL_GOODS_REQUEST_GOODS
				   WHERE REQ_GOODS_NO = '$req_goods_no' AND GOODS_NO = '$goods_no' ";
		
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$BUY_PRICE			= $rows[0];
		$REQ_QTY			= $rows[1];

		//echo ((float)$BUY_PRICE)." // ".((float)$buy_price)." || ".$REQ_QTY." // ".$req_qty."<br/>";

		if(((float)$BUY_PRICE) <> ((float)$buy_price) || $REQ_QTY <> $req_qty) { 

			$query = "UPDATE TBL_GOODS_REQUEST_GOODS
						 SET CHANGED_TF = 'Y'
					   WHERE REQ_GOODS_NO = '$req_goods_no' AND GOODS_NO = '$goods_no' ";
			
			//echo $query;

			if(mysql_query($query,$db)) { 
				$query="INSERT INTO TBL_GOODS_REQUEST_GOODS_HISTORY (REQ_NO, REQ_GOODS_NO, BUY_PRICE, REQ_QTY, REG_ADM, REG_DATE)
		             VALUES ('$req_no', '$req_goods_no', '$BUY_PRICE', '$REQ_QTY', '$reg_adm', now()); ";
			}
		} else
			return false;

		//echo $query;
		//exit;


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listRequestGoodsHistory($db, $req_no, $req_goods_no)
	{
		if($req_no == "" && $req_goods_no == "") return;

		$query = "
					SELECT REQ_GOODS_NO, BUY_PRICE, REQ_QTY, REG_ADM, REG_DATE
					  FROM TBL_GOODS_REQUEST_GOODS_HISTORY
					 WHERE 1 = 1
				 ";

		if($req_no <> "") 
			$query .= " AND REQ_NO = '$req_no' ";

		if($req_goods_no <> "") 
			$query .= " AND REQ_GOODS_NO = '$req_goods_no' ";

		$query .= " ORDER BY REG_DATE DESC";

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

	// 재고정보에 클레임 번호 연결 업데이트
	function updateStockClaimNo($db, $stock_no, $bb_no) { 

		$query = " UPDATE TBL_STOCK 
					  SET BB_NO = '$bb_no'
					WHERE STOCK_NO = '$stock_no'
				 ";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	// 작업취소 - 출고취소
	function deleteStockByWorkDoneNo($db, $work_done_no, $del_adm){

		$query = "SELECT STOCK_NO
					FROM TBL_STOCK  
				   WHERE WORK_DONE_NO = '$work_done_no' AND DEL_TF = 'N' AND CLOSE_TF = 'N' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
				$STOCK_NO			= Trim($record[$i]["STOCK_NO"]);
				deleteStock($db, $STOCK_NO, $del_adm);
			}
		}
	
	}

	// 작업취소 - 출고대기취소
	function deleteStockEachByWorkDoneNo($db, $work_done_no, $del_adm){

		$query = "UPDATE TBL_STOCK_EACH
				     SET DEL_TF = 'Y'
				   WHERE WORK_DONE_NO = '$work_done_no' AND DEL_TF = 'N' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		
	}

	function syncGoodsStock($db, $goods_no) { 
		//여기서 TBL_STOCK에 해당 상품번호관련 STOCK REC가 모두 DEL이면  SELECT이하의 QUERY의 결과값이 GOODS_NO가 =NULL이 나온다. 2021.06
		$qry = "  SELECT COUNT(1) AS CNT
			FROM TBL_STOCK 
			WHERE CLOSE_TF    = 'N' 
			AND DEL_TF    = 'N' 
			AND GOODS_NO    = '$goods_no'
		";
      
      //echo $qry;

		$result = mysql_query($qry,$db);
		$rows   = mysql_fetch_array($result);

		$CNT   = $rows[0];

		if($CNT > 0){
			
			$query = " UPDATE TBL_GOODS G 
				JOIN (
					SELECT GOODS_NO, 
						SUM(IN_FQTY) AS FSTOCK_CNT, 
						SUM(IN_QTY) - SUM(OUT_QTY) AS STOCK_CNT, 
						SUM(IN_BQTY) - SUM(OUT_BQTY) AS BSTOCK_CNT 
					FROM TBL_STOCK 
					WHERE CLOSE_TF = 'N' AND DEL_TF = 'N' AND GOODS_NO = '$goods_no'
				) S ON G.GOODS_NO = S.GOODS_NO
				SET G.FSTOCK_CNT = S.FSTOCK_CNT,
				G.STOCK_CNT  = S.STOCK_CNT,
				G.BSTOCK_CNT = S.BSTOCK_CNT ";
		}
		else{
			
			$query = "	UPDATE TBL_GOODS G 
						SET G.FSTOCK_CNT = 0
							, G.STOCK_CNT  = 0
							, G.BSTOCK_CNT = 0
						WHERE G.GOODS_NO   = '$goods_no'   
				   ";
		}   
      
		  //echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} 
		else {
			return true;
		}

	}

	function updateStockMemo($db, $stock_no, $memo) { 
		
		$query = " UPDATE TBL_STOCK
		              SET MEMO = '".$memo."' 
					WHERE STOCK_NO = '".$stock_no."' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function getGoodsCodeLGWithGoodsNo($db,$goodsNo){
		$query="SELECT DCODE FROM TBL_GOODS_EXTRA WHERE PCODE = 'GOODS_CODE_LG' AND GOODS_NO=".$goodsNo." ; ";
		$result=mysql_query($query,$db);
		$record=mysql_fetch_array($result);
		return $record[0];
	}

	//발주서 관리 리스트 페이지 입고처리일 정렬
	function listGoodsRequestOrderDelivery($db, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$con_delivery_tf	=	$filter['con_delivery_tf'];
		$con_to_here		=	$filter['con_to_here'];
		$con_cancel_tf		=	$filter['con_cancel_tf'];
		$con_confirm_tf		=	$filter['con_confirm_tf'];
		$con_changed_tf		=	$filter['con_changed_tf'];
		$con_receive_tf		=	$filter['con_receive_tf'];
		$con_wrap_tf		=	$filter['con_wrap_tf'];
		$con_sticker_tf		=	$filter['con_sticker_tf'];
		$chk_after_confirm  =	$filter['chk_after_confirm'];
		$con_payment		=	$filter['con_payment'];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT A.REQ_NO, A.GROUP_NO, A.BUY_CP_NO, A.BUY_CP_NM, A.BUY_MANAGER_NM, A.BUY_CP_PHONE, A.TOTAL_REQ_QTY, A.TOTAL_BUY_TOTAL_PRICE, A.DELIVERY_TYPE, A.REQ_DATE, A.REG_DATE, A.IS_SENT, A.SENT_DATE, GR.CHECK_YN, MAX(A.DELIVERY_DATE) AS DELIVERY_DATE
					FROM
					(
					SELECT DISTINCT GR.REQ_NO, GR.GROUP_NO, GR.BUY_CP_NO, GR.BUY_CP_NM, GR.BUY_MANAGER_NM, GR.BUY_CP_PHONE, GR.TOTAL_REQ_QTY, GR.TOTAL_BUY_TOTAL_PRICE, GR.DELIVERY_TYPE, GR.REQ_DATE, GR.REG_DATE, GR.IS_SENT, GR.SENT_DATE, GR.CHECK_YN
					, (
							SELECT TG.DELIVERY_DATE
							FROM TBL_ORDER_GOODS TG
							WHERE TG.ORDER_GOODS_NO = GRG.ORDER_GOODS_NO
							ORDER BY TG.DELIVERY_DATE DESC
							LIMIT 1
						) AS DELIVERY_DATE
					FROM TBL_GOODS_REQUEST GR
					JOIN TBL_GOODS_REQUEST_GOODS GRG ON GR.REQ_NO = GRG.REQ_NO
					JOIN TBL_GOODS G ON G.GOODS_NO = GRG.GOODS_NO
					JOIN TBL_COMPANY C ON GR.BUY_CP_NO = C.CP_NO
					WHERE GR.DEL_TF = 'N'
					AND GRG.DEL_TF = 'N'
					";
					

		if ($start_date <> "") {
			$query .= " AND GR.REQ_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND GR.REQ_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_type <> "") {
			$query .= " AND GR.BUY_CP_NO = '".$cp_type."' ";
		}

		
		if ($con_delivery_tf <> "") {
			$query .= " AND GR.IS_SENT = '".$con_delivery_tf."' ";
		}

		if ($con_to_here <> "") {
			$query .= " AND GRG.TO_HERE = '".$con_to_here."' ";
		}

		if ($con_cancel_tf <> "") {
			$query .= " AND GRG.CANCEL_TF = '".$con_cancel_tf."' ";
		}

		if ($con_confirm_tf <> "") {
			$query .= " AND GRG.CONFIRM_TF = '".$con_confirm_tf."' ";
		}

		if ($con_changed_tf <> "") {
			$query .= " AND GRG.CHANGED_TF = '".$con_changed_tf."' ";
		}

		if ($con_receive_tf <> "") {
			if ($con_receive_tf == "Y") 
				$query .= " AND GRG.RECEIVE_QTY > 0 ";
			else
				$query .= " AND GRG.RECEIVE_QTY = 0 ";
		}

		if ($con_wrap_tf <> "") {
			if($con_wrap_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%포장지 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%포장지 :%' ";
		}

		if ($con_sticker_tf <> "") {
			if($con_sticker_tf == "Y")
				$query .= " AND GRG.MEMO1 LIKE '%스티커 :%' ";
			else
			    $query .= " AND GRG.MEMO1 NOT LIKE '%스티커 :%' ";
		}

		if ($chk_after_confirm <> "") {
			$query .= " AND GRG.CONFIRM_DATE < GRG.RECEIVE_DATE ";
		}
		if($con_payment <> ""){
			$query .= " AND C.AD_TYPE = '".$con_payment."' ";
		}


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GR.BUY_CP_NM LIKE '%".$search_str."%' OR GR.BUY_MANAGER_NM LIKE '%".$search_str."%' OR GR.BUY_CP_PHONE LIKE '%".$search_str."%' OR GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR GRG.MEMO2 LIKE '%".$search_str."%' OR GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GRG.GOODS_NAME LIKE '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "MEMO2") {
				$query .= " AND (GRG.MEMO2 LIKE '%".$search_str."%') ";
			} else if ($search_field == "REQ_GOODS_NO") {
				$query .= " AND (GRG.REQ_GOODS_NO = '".$search_str."') ";
			} else if ($search_field == "RESERVE_NO") {
				$query .= " AND (GRG.RESERVE_NO = '".$search_str."') ";
			} else if ($search_field == "ORDER_GOODS_NO") {
				$query .= " AND (GRG.ORDER_GOODS_NO = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
	    
		if ($order_field == "") 
			$order_field = "GR.REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " )A
				GROUP BY A.REQ_NO, A.GROUP_NO, A.BUY_CP_NO, A.BUY_CP_NM, A.BUY_MANAGER_NM, A.BUY_CP_PHONE, A.TOTAL_REQ_QTY, A.TOTAL_BUY_TOTAL_PRICE, A.DELIVERY_TYPE, A.REQ_DATE, A.REG_DATE, A.IS_SENT, A.SENT_DATE
				ORDER BY 14 " .$order_str." limit ".$offset.", ".$nRowCount;

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

	//발주 전용서식 조회 20210525
	function selectexclusiveformcpno($db, $cp_no) 
	{
		$query = "SELECT CP_NO 
					FROM T_EXCLUSIVE_FORM
				   WHERE CP_NO = '$cp_no'	
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
?>