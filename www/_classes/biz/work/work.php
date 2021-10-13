<?
	# =============================================================================
	# File Name    : work.php
	// 2018-08-17 GROUP BY의 CLAIM_ORDER_GOODS_NO -> GROUP_NO 로 전체 교체, 취소 반품은 GROUP_NO, 교환은 GROUP_NO가 없음
	# =============================================================================


	function listWorkOrder($db, $order_type, $start_date, $end_date, $order_state, $cp_no, $work_order_type, $search_field, $search_str, $order_field, $order_str) {

		$query = "SELECT O.ORDER_NO, O.RESERVE_NO, O.ORDER_DATE, O.CP_NO, C.CP_NM, C.CP_NM2, O.O_MEM_NM, O.R_MEM_NM,  
						 OG.OPT_OUTSTOCK_DATE, OG.ORDER_GOODS_NO, OG.CATE_01, OG.CATE_04, OG.GOODS_CODE, OG.GOODS_NAME, SUB.QTY, AI.ADM_NAME AS OPT_MANAGER_NAME, O.BULK_TF, OG.OPT_MEMO, OG.WORK_START_DATE, OG.GOODS_NO, OG.WORK_QTY, OG.WORK_SEQ, OG.DELIVERY_TYPE, OG.DELIVERY_CP, OG.WORK_MSG, OG.WORK_REG_ADM, AI2.ADM_NAME AS WORK_REG_ADM_NAME,  OG.WORK_REG_DATE, 
						 CASE WHEN OG.WORK_START_DATE = '0000-00-00 00:00:00' THEN 1 ELSE 0 END AS WORK_ORDERED,
						 OG.OPT_WRAP_NO, WG.GOODS_NAME AS OPT_WRAP_NAME, OG.OPT_STICKER_NO, SG.GOODS_NAME AS OPT_STICKER_NAME, OG.OPT_STICKER_READY, OG.OPT_OUTBOX_TF, OG.OPT_STICKER_MSG, OG.OPT_PRINT_MSG, OG.REG_DATE, OG.WORK_REQ_QTY, 
						 (SELECT COUNT(*) FROM TBL_GOODS_REQUEST_GOODS GRG WHERE GRG.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND GRG.DEL_TF = 'N' AND GRG.CANCEL_TF = 'N') AS RGN_COUNT,
						 CD.DCODE_NM AS DELIVERY_TYPE_NAME

					FROM TBL_ORDER O 
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO 
			   LEFT JOIN TBL_COMPANY C ON C.CP_NO = O.CP_NO
			   LEFT JOIN TBL_ADMIN_INFO AI ON O.OPT_MANAGER_NO = AI.ADM_NO
			   LEFT JOIN TBL_ADMIN_INFO AI2 ON OG.WORK_REG_ADM = AI2.ADM_NO
		 	   LEFT JOIN TBL_GOODS WG ON WG.GOODS_NO = OG.OPT_WRAP_NO
			   LEFT JOIN TBL_GOODS SG ON SG.GOODS_NO = OG.OPT_STICKER_NO
					JOIN TBL_CODE_DETAIL CD ON CD.DCODE = OG.DELIVERY_TYPE AND CD.PCODE = 'DELIVERY_TYPE'
			   LEFT JOIN (
						SELECT ORDER_GOODS_NO,  
							   SUM( 
								CASE WHEN ORDER_STATE =1
									   OR ORDER_STATE =2
									   OR ORDER_STATE =3
									 THEN QTY
									 WHEN ORDER_STATE >=5
									 THEN -1 * QTY
									  END ) AS QTY
								FROM TBL_ORDER_GOODS
								WHERE USE_TF =  'Y' AND DEL_TF =  'N'
								  AND ORDER_STATE IN ('2', '6', '8')
							 GROUP BY CASE WHEN GROUP_NO =0
								      THEN ORDER_GOODS_NO
									  ELSE GROUP_NO
								END
					
						 ) SUB ON SUB.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
				   WHERE OG.ORDER_STATE = '2' 
				     AND OG.WORK_FLAG = 'N'
				     AND OG.DELIVERY_TYPE NOT IN ('98', '99')    
					 AND SUB.QTY > 0
				     AND OG.USE_TF = 'Y' 
				     AND OG.DEL_TF = 'N'
							   ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if ($start_date <> "" || $end_date <> "") {

			$query .= " AND (OG.WORK_SEQ !=  '' OR ((";

			if ($start_date <> "") {
				$query .= "OG.OPT_OUTSTOCK_DATE >= '".$start_date."'";
			}
			
			if ($start_date <> "" && $end_date <> "")  
				$query .= " AND ";

			if ($end_date <> "") {
				$query .= "OG.OPT_OUTSTOCK_DATE <= '".$end_date." 23:59:59' ";
			}

			$query .= " ) ";
			$query .= " OR OG.OPT_OUTSTOCK_DATE = '0000-00-00 00:00:00') )";
		}

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		} 

		switch($work_order_type) { 
			case "Y" : $query .= " AND OG.WORK_START_DATE <> '0000-00-00 00:00:00' "; break;
			case "N" : $query .= " AND OG.WORK_START_DATE = '0000-00-00 00:00:00' "; break;
			default : break;
		}
				

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$default_order_field = "WORK_ORDERED ASC, WORK_START_DATE ASC, WORK_SEQ ASC,";
		if($order_field  == "") 
			$order_field = $default_order_field." OG.OPT_OUTSTOCK_DATE DESC, OG.ORDER_GOODS_NO";
		else {

			if($order_field  == "OPT_OUTSTOCK_DATE")
				$order_field = $default_order_field."OG.OPT_OUTSTOCK_DATE DESC";
			else if($order_field  == "ORDER_DATE")
				$order_field = $default_order_field."OG.ORDER_DATE ASC";
			else if($order_field  == "CP_NO")
				$order_field = $default_order_field."O.CP_NO ASC";
			else if($order_field  == "R_MEM_NM")
				$order_field = $default_order_field."O.R_MEM_NM ASC";
			else if($order_field  == "GOODS_NAME")
				$order_field = $default_order_field."OG.GOODS_NAME ASC";
			else 
				$order_field = $default_order_field.$order_field;
		}
		$order_field = $order_field.", OG.ORDER_GOODS_NO ASC";

		$query .= " ORDER BY ".$order_field." limit 0, 2000";

		echo $query."<br/>";
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

	function checkOrderGoodsOutcase ($db, $goods_no) {
		
		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT COUNT(*) AS CNT FROM TBL_GOODS CG
							 WHERE CG.GOODS_CATE LIKE '010202%'
							   AND CG.GOODS_NO IN 
									(SELECT GOODS_SUB_NO 
									   FROM TBL_GOODS_SUB GS, TBL_GOODS G
									  WHERE GS.GOODS_NO = G.GOODS_NO
										AND GS.GOODS_NO = '$goods_no')";
		
		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$record  = $rows[0];
		
		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function checkOrderGoodsIncase ($db, $goods_no) {
		
		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT COUNT(*) AS CNT 
		            FROM TBL_GOODS CG
				   WHERE CG.GOODS_CATE LIKE '010203%'
					 AND CG.GOODS_NO IN 
					   		            (SELECT GOODS_SUB_NO 
									       FROM TBL_GOODS_SUB 
										  WHERE GOODS_NO = '$goods_no')";
		
		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$record  = $rows[0];
		
		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	/*
	function getOrderGoodsIncase ($db, $goods_no) {

		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT FILE_RNM_150, FILE_PATH_150 FROM TBL_GOODS CG
							 WHERE CG.GOODS_CATE LIKE '010203%'
								 AND CG.GOODS_NO IN (SELECT GOODS_SUB_NO 
								FROM TBL_GOODS_SUB GS, TBL_GOODS G
							 WHERE GS.GOODS_NO = G.GOODS_NO
								 AND GS.GOODS_NO = '$goods_no')";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$FILE_RNM_150  = $rows[0];
		$FILE_PATH_150  = $rows[1];

		if ($FILE_PATH_150 <> "" ) {
			$FILE_PATH_150 = str_replace("/upload_data/goods_image/", "", $FILE_PATH_150);
		}
		
		$record = $FILE_PATH_150.$FILE_RNM_150;
		
		return $record;
	}
	*/

	/*
	function getOrderGoodsIncaseName ($db, $goods_no) {

		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT GOODS_NAME FROM TBL_GOODS CG
							 WHERE CG.GOODS_CATE LIKE '010203%'
								 AND CG.GOODS_NO IN (SELECT GOODS_SUB_NO 
								FROM TBL_GOODS_SUB GS, TBL_GOODS G
							 WHERE GS.GOODS_NO = G.GOODS_NO
								 AND GS.GOODS_NO = '$goods_no')";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NAME  = $rows[0];

		$record = $GOODS_NAME;
		
		return $record;
	}
	*/

	function getOrderGoodsSub($db, $goods_no, $goods_type) {

		if($goods_type == "INCASE")
			$goods_cate = "010203";
		else if($goods_type == "OUTCASE")
			$goods_cate = "010202";

		$query = "SELECT G.GOODS_NO, G.GOODS_NAME, G.WRAP_WIDTH, G.WRAP_LENGTH, G.WRAP_MEMO
					FROM TBL_GOODS G
					JOIN TBL_GOODS_SUB GS ON G.GOODS_NO = GS.GOODS_SUB_NO
				   WHERE G.GOODS_CATE LIKE  '".$goods_cate."%'
					 AND GS.GOODS_NO =  '".$goods_no."'";
		
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

	/*
	//체크해서 없애야함
	function getOrderGoodsImage ($db, $goods_no) {
		
		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT FILE_RNM_150, FILE_PATH_150 FROM TBL_GOODS
							 WHERE GOODS_NO = '$goods_no' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$FILE_RNM_150  = $rows[0];
		$FILE_PATH_150  = $rows[1];

		if ($FILE_PATH_150 <> "" ) {
			$FILE_PATH_150 = str_replace("/upload_data/goods_image/", "", $FILE_PATH_150);
		}
		
		$record = $FILE_PATH_150.$FILE_RNM_150;
		
		return $record;
	}
	*/

	/*
	function insertWork($db, $order_goods_no, $work_type, $work_flag, $work_date, $work_order, $reg_adm) {
		
		$query = "SELECT COUNT(*) AS CNT FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_TYPE = '$work_type' ";
		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			$query = "INSERT INTO TBL_ORDER_WORK (ORDER_GOODS_NO, WORK_TYPE, WORK_FLAG, WORK_DATE, WORK_ORDER) VALUES 
													('$order_goods_no', '$work_type', '$work_flag', '$work_date', '$work_order'); ";
			
			//echo $query."<br/>";
			mysql_query($query,$db);

			$query = "UPDATE TBL_ORDER_GOODS 
						 SET WORK_START_DATE = '$work_date', WORK_REG_ADM = '$reg_adm', WORK_REG_DATE = now()
					   WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_START_DATE = '0000-00-00 00:00:00' ";
			//echo $query."<br/>";
		} else {
			$query = "UPDATE TBL_ORDER_WORK SET WORK_ORDER = '$work_order' WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_TYPE = '$work_type' ";
			//echo $query."<br/>";
		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	function deleteWork($db, $order_goods_no, $work_type, $work_flag) {
		
		$query = "DELETE FROM TBL_ORDER_WORK 
						WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_TYPE = '$work_type' AND WORK_FLAG = 'N' ";
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
	*/

	function updateWorkSeq($db, $order_goods_no, $work_date, $reg_adm) { 
		//$work_date :ORDER_GOODS_NO.WORK_START_DATE에 입력됨

		$query = "SELECT IFNULL( MAX( OG.WORK_SEQ ) , 0 ) +1 AS WORK_SEQ
					FROM TBL_ORDER_GOODS OG 
				   WHERE OG.WORK_START_DATE =  '$work_date'
					 AND OG.USE_TF =  'Y'
					 AND OG.DEL_TF =  'N'  ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$WORK_SEQ = $rows[0];

		insertOrderWorkLog($db, $order_goods_no, $work_date, $reg_adm, $rows[0]);



		//echo "NEW WORK_SEQ".$WORK_SEQ."<br/>";

		$query = "UPDATE TBL_ORDER_GOODS 
				 	 SET WORK_SEQ = '$WORK_SEQ', WORK_START_DATE = '$work_date', WORK_REG_ADM = '$reg_adm', WORK_REG_DATE = now()
					   WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_SEQ = '' ";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {

			//모니터링 해제 20170404
			//insertSelWorkLog($db, $order_goods_no, $work_date, $WORK_SEQ, 'updateWorkSeq', $reg_adm);

			return true;
		}

	}
	
	function insertOrderWorkLog($db, $orderGoodsNo, $workDate, $regAdm, $workSeq){
		$query= "INSERT INTO T_ORDER_WORK_LOG(ORDER_GOODS_NO, WORK_SEQ, REG_ADMIN, REG_DATE) VALUES('$orderGoodsNo', '$workSeq', '$regAdm', NOW())
					";
		
		$result=mysql_query($query, $db);
		if(!$result){
			echo "<script>alert('INSERT_ORDER_WORK_LOG_ERROR');</script>";
		}
	}

	/*
	function updateWorkSeq($db, $order_goods_no, $work_date, $reg_adm) { 

		$query = "SELECT IFNULL( MAX( OG.WORK_SEQ ) , 0 ) +1 AS WORK_SEQ
					FROM TBL_ORDER_GOODS OG 
					JOIN TBL_ORDER_WORK OW ON OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
				   WHERE OW.WORK_DATE =  '$work_date'
					 AND OG.USE_TF =  'Y'
					 AND OG.DEL_TF =  'N'  ";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$WORK_SEQ = $rows[0];

		//echo "NEW WORK_SEQ".$WORK_SEQ."<br/>";

		$query = "UPDATE TBL_ORDER_GOODS 
				 	 SET WORK_SEQ = '$WORK_SEQ'
					   WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_SEQ = '' ";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {

			//모니터링 해제 20170404
			//insertSelWorkLog($db, $order_goods_no, $work_date, $WORK_SEQ, 'updateWorkSeq', $reg_adm);

			return true;
		}

	}
	*/
	
	function deleteWorks($db, $no_selected_reserve_no, $reg_adm) {
		
		$arr_order_goods_no = explode(",",$no_selected_reserve_no);
		//echo sizeof($arr_order_goods_no);

		for ($i = 0; $i < sizeof($arr_order_goods_no); $i++) {
			
			$query = "SELECT COUNT(*) AS CNT FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '".$arr_order_goods_no[$i]."' AND WORK_FLAG = 'Y' ";

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			
			if ($rows[0] == 0) {
				
				$query = "DELETE FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '".$arr_order_goods_no[$i]."' ";
				mysql_query($query,$db);

				$query = "UPDATE 	T_ORDER_WORK_LOG
							SET 	DEL_TF='Y'
							,		DEL_ADMIN='$reg_adm'
							,		DEL_DATE=NOW()
							WHERE	ORDER_GOODS_NO='".$arr_order_goods_no[$i]."'
							
								";
				if(!mysql_query($query, $db)){
					echo "<script>alert('DELETE_T_ORDER_WORK_LOG_ERROR');</script>";
				}

				$query = "UPDATE TBL_ORDER_GOODS 
							 SET WORK_START_DATE = '0000-00-00 00:00:00', WORK_SEQ = ''
						   WHERE ORDER_GOODS_NO = '".$arr_order_goods_no[$i]."' ";
				mysql_query($query,$db);

				//모니터링 해제 20170404
				//insertSelWorkLog($db, $arr_order_goods_no[$i], '', '', 'deleteWorks', $reg_adm);
				
			}
		}
	}

	function updateWorksFlagN($db, $no_selected_flag) {
		
		$arr_work_no = explode(",",$no_selected_flag);

		for ($i = 0; $i < sizeof($arr_work_no); $i++) {
			$query = "UPDATE TBL_ORDER_WORK SET WORK_FLAG = 'N' WHERE WORK_NO = '".$arr_work_no[$i]."' ";
			mysql_query($query,$db);
		}
	}


	function updateWorksLine($db, $work_no, $line) {
		$query = "UPDATE TBL_ORDER_WORK SET WORK_LINE = '$line' WHERE WORK_NO = '$work_no' ";
		mysql_query($query,$db);
	}

	function updateWorksLineAll($db, $order_goods_no, $line) {
		$query = "UPDATE TBL_ORDER_WORK 
					 SET WORK_LINE = '$line'
				   WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		mysql_query($query,$db);
	}

	//외부발송을 제외한 자체작업완료
	function updateWorksFlagY($db, $work_no, $arr_partial_done_no, $confirm_adm) {

		$query = "SELECT ORDER_GOODS_NO FROM TBL_ORDER_WORK WHERE WORK_NO = '".$work_no."' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$order_goods_no  = $rows[0];

		//부분완료된경우 작업완료 없이 패스
		if($arr_partial_done_no != null) {
			foreach ($arr_partial_done_no as $value) {
				$arr = explode("|",$value);
				$partial_done_no = $arr[0];
				$sum_done_qty = $arr[1];

				if($partial_done_no == $order_goods_no) { 
					$query = "UPDATE TBL_ORDER_GOODS SET WORK_QTY = '$sum_done_qty' WHERE ORDER_GOODS_NO = '".$partial_done_no."' ";
					//echo $query."<br/>";
										
					mysql_query($query,$db);
					return;
				}
				
			}
		}

		$query = "UPDATE TBL_ORDER_WORK SET WORK_FLAG = 'Y', CONFIRM_ADM = '$confirm_adm' WHERE WORK_NO = '".$work_no."' ";
		mysql_query($query,$db);
		
		$query = "SELECT COUNT(*) AS CNT FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_FLAG = 'N' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$cnt_flag_N  = $rows[0];

		//주문상품의 모든 작업이 끝나면 전체 완료하고 리스트에서 제외
		if ($cnt_flag_N == 0) {

			//작업끝난 주문인지 체크해서 끝난것이면 패스
			$query = "SELECT COUNT(*) AS CNT FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO = '$order_goods_no' AND WORK_FLAG = 'Y' AND WORK_END_DATE <> '0000-00-00 00:00:00' ";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$cnt_work_end  = $rows[0];
			if($cnt_work_end > 0) return;

			$refund_able_qty = getRefundAbleQty($db, '', $order_goods_no);
			$query = "UPDATE TBL_ORDER_GOODS SET WORK_FLAG = 'Y', WORK_END_DATE = now(), WORK_QTY = '$refund_able_qty'  WHERE ORDER_GOODS_NO = '$order_goods_no' ";
			mysql_query($query,$db);
		}

	}

	// 외부발송일경우 주문상품에 완료처리, 클레임시 주문보다 작업 수량이 더 많으면 작업완료 처리 (2017-05-17)
	function updateWorksFlagYOrderGoods($db, $order_goods_no) {
		$query = "UPDATE TBL_ORDER_GOODS 
					 SET WORK_FLAG = 'Y', WORK_END_DATE = now() 
				   WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		mysql_query($query,$db);
	}

	/*
	function listWorkList($db, $work_date, $arr_work_type, $arr_work_line, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntWorkList($db, $work_date, $arr_work_type, $arr_work_line, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

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
										//(SELECT FILE_RNM_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_IMG,
										//(SELECT FILE_PATH_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_PATH_IMG,
										//(SELECT IFNULL(MAX(WORK_DATE),'헤') FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS WORK_DATE,

		$query = "SELECT distinct O.ORDER_NO, OG.ORDER_GOODS_NO, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO,  
										 O.O_MEM_NM, O.R_MEM_NM, OG.GOODS_NO, OG.CATE_01, OG.CATE_04, OG.GOODS_NAME, OG.QTY, O.OPT_MANAGER_NO, OG.OPT_MEMO, OG.WORK_QTY, OG.WORK_START_DATE,
										 (SELECT IFNULL(MAX(WORK_ORDER),100000) FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS WORK_ORDER,
										 (SELECT WORK_LINE FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND OW.WORK_TYPE = 'OUTCASE') AS WORK_LINE,
										 O.BULK_TF, OG.OPT_STICKER_NO, OG.OPT_OUTBOX_TF, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_STICKER_MSG,
										 OG.WORK_SEQ, OG.DELIVERY_CNT_IN_BOX, OG.WORK_MSG, OG.DELIVERY_TYPE, OG.DELIVERY_CP
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW  
							 WHERE O.RESERVE_NO = OG.RESERVE_NO 
								 AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND OG.ORDER_STATE = '2' 
								 AND OG.WORK_FLAG = 'N' 
								 AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
								 ";

		if ($work_date <> "") {
			$query .= " AND OG.WORK_START_DATE <= '".$work_date."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

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
		$query .= " ORDER BY ".$order_field." limit ".$offset.", ".$nRowCount;

		//$query .= " ORDER BY O.ORDER_NO DESC limit ".$offset.", ".$nRowCount;

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

	
	function totalCntWorkList($db, $work_date, $arr_work_type, $arr_work_line, $use_tf, $del_tf, $search_field, $search_str) {

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

		$query ="SELECT COUNT(distinct OW.ORDER_GOODS_NO) CNT
							 FROM  TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW
							WHERE O.RESERVE_NO = OG.RESERVE_NO
								AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
							  AND OG.ORDER_STATE = '2' AND OG.WORK_FLAG = 'N' ";

		if ($work_date <> "") {
			$query .= " AND OG.WORK_START_DATE <= '".$work_date."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

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

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
	*/
	
	function listWorkList($db, $work_date, $search_field, $search_str) {

		$query = "SELECT O.ORDER_NO, OG.ORDER_GOODS_NO, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO,  
										 O.O_MEM_NM, O.R_MEM_NM, OG.GOODS_NO, OG.CATE_01, OG.CATE_04, OG.GOODS_NAME, SUB.QTY, O.OPT_MANAGER_NO, OG.OPT_MEMO, OG.WORK_QTY, OG.WORK_START_DATE,
										 O.BULK_TF, OG.OPT_STICKER_NO, OG.OPT_OUTBOX_TF, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_STICKER_MSG,
										 OG.WORK_SEQ, OG.DELIVERY_CNT_IN_BOX, OG.WORK_MSG, OG.DELIVERY_TYPE, OG.DELIVERY_CP,
										 OG.WORK_REQ_QTY
								FROM TBL_ORDER O 
								JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO  
						   LEFT JOIN (
										SELECT ORDER_GOODS_NO,  
											   SUM( 
												CASE WHEN ORDER_STATE =1
													   OR ORDER_STATE =2
													   OR ORDER_STATE =3
													 THEN QTY
													 WHEN ORDER_STATE >=5
													 THEN -1 * QTY
													  END ) AS QTY
												FROM TBL_ORDER_GOODS
												WHERE USE_TF =  'Y'
												AND DEL_TF =  'N'
												GROUP BY CASE WHEN GROUP_NO =0
												THEN ORDER_GOODS_NO
												ELSE GROUP_NO
												END
									
									  ) SUB ON SUB.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
							 WHERE  OG.ORDER_STATE = '2' 
								 AND OG.WORK_FLAG = 'N' 
								 AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
								 AND O.USE_TF = 'Y' 
								 AND O.DEL_TF = 'N'
								 AND OG.WORK_SEQ > 0
								 AND SUB.QTY > 0
								 AND OG.ORDER_GOODS_NO NOT IN (
										SELECT ORDER_GOODS_NO 
										FROM  TBL_ORDER_WORK_HISTORY 
										WHERE ORDER_GOODS_NO = OG.ORDER_GOODS_NO
										AND DATE_FORMAT( REG_DATE,  '%Y%m%d' ) = DATE_FORMAT( NOW( ) ,  '%Y%m%d' ) 
										AND DEL_TF =  'N')
								 ";

		if ($work_date <> "") {
			$query .= " AND OG.WORK_START_DATE <= '".$work_date."' ";
		}

		$query .= " ORDER BY WORK_START_DATE ASC, WORK_SEQ ASC, OG.OPT_OUTSTOCK_DATE DESC limit 0, 2000";

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

	/*
	function totalCntWorkList($db, $work_date, $search_field, $search_str) {

		$query ="SELECT COUNT(OG.ORDER_GOODS_NO) CNT
							 FROM  TBL_ORDER O, TBL_ORDER_GOODS OG
							WHERE O.RESERVE_NO = OG.RESERVE_NO
							  AND OG.ORDER_STATE = '2' 
							  AND OG.WORK_FLAG = 'N' 
							  AND O.USE_TF = 'Y' 
							  AND O.DEL_TF = 'N' ";

		if ($work_date <> "") {
			$query .= " AND OG.WORK_START_DATE <= '".$work_date."' ";
		}

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
	*/

	function selectWork($db, $order_goods_no) {

		$query = "SELECT WORK_NO, ORDER_GOODS_NO, WORK_TYPE, WORK_FLAG, WORK_GOODS_ORDER, WORK_ORDER, WORK_DATE, CONFIRM_ADM 
							 FROM TBL_ORDER_WORK
							WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
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


	//상세 작업리스트
	function listWorkDetailList($db, $order_type, $work_date, $arr_work_type, $arr_work_line, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntWorkDetailList($db, $order_type, $work_date, $arr_work_type, $arr_work_line, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

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

										 //(SELECT FILE_RNM_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_IMG,
										 //(SELECT FILE_PATH_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_PATH_IMG,

		$query = "SELECT O.ORDER_NO, OG.ORDER_GOODS_NO, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO,  
										 O.O_MEM_NM, OG.GOODS_NO, OG.GOODS_NAME, OG.QTY, O.OPT_MANAGER_NO, OG.OPT_MEMO,

										 (SELECT IFNULL(MAX(WORK_DATE),'헤') FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS WORK_DATE,
										 (SELECT IFNULL(MAX(WORK_ORDER),100000) FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS WORK_ORDER,
										 O.BULK_TF, OW.WORK_DATE, OG.OPT_STICKER_NO, OG.OPT_OUTBOX_TF, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_STICKER_MSG,
										 OW.WORK_NO, OW.WORK_LINE, OW.WORK_TYPE, OW.WORK_FLAG
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW 
							 WHERE O.RESERVE_NO = OG.RESERVE_NO 
								 AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND OG.ORDER_STATE = '2' AND OW.WORK_FLAG = 'N' ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if ($work_date <> "") {
			$query .= " AND OW.WORK_DATE <= '".$work_date."' ";
		}
		
		if ($work_type1 == "") {
			$query .= " AND OW.WORK_TYPE <> 'INCASE' ";
		}

		if ($work_type2 == "") {
			$query .= " AND OW.WORK_TYPE <> 'WRAP' ";
		}

		if ($work_type3 == "") {
			$query .= " AND OW.WORK_TYPE <> 'STICKER' ";
		}

		if ($work_type4 == "") {
			$query .= " AND OW.WORK_TYPE <> 'OUTCASE' ";
		}

		if ($work_type5 == "") {
			$query .= " AND OW.WORK_TYPE <> 'OUTSTICKER' ";
		}

		if ($work_lineA == "") {
			$query .= " AND OW.WORK_LINE <> 'A' ";
		}

		if ($work_lineB == "") {
			$query .= " AND OW.WORK_LINE <> 'B' ";
		}

		if ($work_lineC == "") {
			$query .= " AND OW.WORK_LINE <> 'C' ";
		}

		if ($arr_work_line <> "Y|Y|Y") {
			$query .= " AND OW.WORK_LINE <> '' ";
		}

		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		$order_field = "WORK_DATE ASC, WORK_GOODS_ORDER ASC, WORK_ORDER ASC";
		$query .= " ORDER BY ".$order_field." limit ".$offset.", ".$nRowCount;

		//$query .= " ORDER BY O.ORDER_NO DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntWorkDetailList($db, $order_type, $work_date, $arr_work_type, $arr_work_line,  $use_tf, $del_tf, $search_field, $search_str) {
		
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

		$query ="SELECT COUNT(WORK_NO) CNT 
							 FROM  TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW
							WHERE O.RESERVE_NO = OG.RESERVE_NO
								AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
							  AND OG.ORDER_STATE = '2' AND OW.WORK_FLAG = 'N' ";

		if ($order_type <> "") {
			$query .= " AND O.ORDER_TYPE = '".$order_type."' ";
		}

		if ($work_date <> "") {
			$query .= " AND OW.WORK_DATE <= '".$work_date."' ";
		}

		if ($work_type1 == "") {
			$query .= " AND OW.WORK_TYPE <> 'INCASE' ";
		}

		if ($work_type2 == "") {
			$query .= " AND OW.WORK_TYPE <> 'WRAP' ";
		}

		if ($work_type3 == "") {
			$query .= " AND OW.WORK_TYPE <> 'STICKER' ";
		}

		if ($work_type4 == "") {
			$query .= " AND OW.WORK_TYPE <> 'OUTCASE' ";
		}

		if ($work_type5 == "") {
			$query .= " AND OW.WORK_TYPE <> 'OUTSTICKER' ";
		}

		if ($work_lineA == "") {
			$query .= " AND OW.WORK_LINE <> 'A' ";
		}

		if ($work_lineB == "") {
			$query .= " AND OW.WORK_LINE <> 'B' ";
		}

		if ($work_lineC == "") {
			$query .= " AND OW.WORK_LINE <> 'C' ";
		}

		if ($arr_work_line <> "Y|Y|Y") {
			$query .= " AND OW.WORK_LINE <> '' ";
		}


		if ($use_tf <> "") {
			$query .= " AND O.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND O.DEL_TF = '".$del_tf."' ";
		}

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function selectWorkDetail($db, $order_goods_no) {

		$query = "SELECT distinct O.ORDER_NO, OG.ORDER_GOODS_NO, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO,  
										 O.O_MEM_NM, OG.GOODS_NO, OG.GOODS_NAME, OG.QTY, O.OPT_MANAGER_NO, OG.OPT_MEMO,
										 (SELECT FILE_RNM_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_IMG,
										 (SELECT FILE_PATH_150 FROM TBL_GOODS G WHERE G.GOODS_NO = OG.GOODS_NO) AS GOODS_PATH_IMG,
										 O.BULK_TF, OW.WORK_DATE, OG.OPT_STICKER_NO, OG.OPT_OUTBOX_TF, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_STICKER_MSG,
										 OG.WORK_QTY
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW  
							 WHERE O.RESERVE_NO = OG.RESERVE_NO 
								 AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND O.USE_TF = 'Y'
								 AND O.DEL_TF = 'N' 
								 AND OG.ORDER_GOODS_NO = '$order_goods_no' ";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//금일 작업 자재리스트
	function listWorkGoods($db, $start_work_date, $end_work_date) {

		$query = "SELECT distinct O.RESERVE_NO, OG.ORDER_GOODS_NO, OG.GOODS_NO, OG.WORK_QTY, OG.DELIVERY_CNT_IN_BOX, G.GOODS_CATE, OG.WORK_SEQ, OG.WORK_REQ_QTY, SUB.QTY
								FROM TBL_ORDER O
                                JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO 
								JOIN TBL_GOODS G ON OG.GOODS_NO = G.GOODS_NO
						   LEFT JOIN (
										SELECT ORDER_GOODS_NO,  
											   SUM( 
												CASE WHEN ORDER_STATE =1
													   OR ORDER_STATE =2
													   OR ORDER_STATE =3
													 THEN QTY
													 WHEN ORDER_STATE >=5
													 THEN -1 * QTY
													  END ) AS QTY
												FROM TBL_ORDER_GOODS
												WHERE USE_TF =  'Y'
												AND DEL_TF =  'N'
												GROUP BY CASE WHEN GROUP_NO =0
												THEN ORDER_GOODS_NO
												ELSE GROUP_NO
												END
									
									  ) SUB ON SUB.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
							 WHERE OG.ORDER_STATE = '2' 
							   AND SUB.QTY > 0
							 
							 ";
		
		if($start_work_date <> "")
			$query .= "		     AND OG.WORK_START_DATE >= '".$start_work_date."' ";

		if($end_work_date <> "")
			$query .= "		     AND OG.WORK_START_DATE <= '".$end_work_date."' ";

		$query .= "				 AND O.USE_TF = 'Y'
								 AND O.DEL_TF = 'N' 
								 AND OG.WORK_FLAG =  'N'
								
							 ORDER BY OG.WORK_START_DATE ASC, OG.WORK_SEQ ASC ";

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
	/*
	function listWorkGoods($db, $start_work_date, $end_work_date) {

		$query = "SELECT distinct O.RESERVE_NO, OG.ORDER_GOODS_NO, OG.GOODS_NO, OG.WORK_QTY, OG.DELIVERY_CNT_IN_BOX, G.GOODS_CATE, OG.WORK_SEQ
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK OW, TBL_GOODS G
							 WHERE O.RESERVE_NO = OG.RESERVE_NO 
								 AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND OG.GOODS_NO = G.GOODS_NO
								 AND OG.ORDER_STATE = '2' ";
		
		if($start_work_date <> "")
			$query .= "		     AND OW.WORK_DATE >= '".$start_work_date."' ";

		if($end_work_date <> "")
			$query .= "		     AND OW.WORK_DATE <= '".$end_work_date."' ";

		$query .= "				 AND O.USE_TF = 'Y'
								 AND O.DEL_TF = 'N' 
								 AND OG.WORK_FLAG =  'N'
								
							 ORDER BY OW.WORK_DATE ASC, OW.WORK_ORDER ASC ";

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


	function getSubGoodsInfo($db, $goods_no) {
		
		$query = "SELECT G.GOODS_CATE, GS.GOODS_SUB_NO, GS.GOODS_CNT 
					FROM TBL_GOODS_SUB GS 
					JOIN TBL_GOODS G ON G.GOODS_NO = GS.GOODS_SUB_NO
				   WHERE GS.GOODS_NO = '$goods_no' ";

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


	function updateDelivaryFlag($db, $order_goods_no) {
		
		// 이미 작업 리스트에 있으면 직배송으로 바뀌지 않는다.

		$query = "SELECT COUNT(*) AS CNT FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$cnt_flag_D  = $rows[0];

		if ($cnt_flag_D == 0) {

			$query = "INSERT INTO TBL_ORDER_WORK (ORDER_GOODS_NO, WORK_TYPE, WORK_FLAG, WORK_DATE, WORK_ORDER) VALUES 
													('$order_goods_no', 'DELIVERY', 'Y', '직배송', '0'); ";

			mysql_query($query,$db);

			$query = "UPDATE TBL_ORDER_GOODS SET WORK_FLAG = 'Y' WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
		}

	}

	function cancelDelivaryFlag($db, $order_goods_no) {

		$query = "DELETE FROM TBL_ORDER_WORK WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		mysql_query($query,$db);

		$query = "UPDATE TBL_ORDER_GOODS SET WORK_FLAG = 'N' WHERE ORDER_GOODS_NO = '$order_goods_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function getOrderGoodsOutcaseImage($db, $goods_no) {

		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT CG.GOODS_NO FROM TBL_GOODS CG
							 WHERE CG.GOODS_CATE = '010203'
								 AND CG.GOODS_NO IN (SELECT GOODS_SUB_NO 
								FROM TBL_GOODS_SUB GS, TBL_GOODS G
							 WHERE GS.GOODS_NO = G.GOODS_NO
								 AND GS.GOODS_NO = '$goods_no')";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$case_goods_no  = $rows[0];

		$query = "SELECT FILE_NM1 FROM TBL_GOODS_IMAGES WHERE GOODS_NO = '$case_goods_no' ORDER BY GOODS_IMAGE_NO ASC LIMIT 1";

		//echo $query."<br>";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function getOrderGoodsOutcaseImageList($db, $goods_no) {

		// 주문 상춤을 구한 뒤 인케이스 있는지 확인
		$query = "SELECT CG.GOODS_NO FROM TBL_GOODS CG
							 WHERE CG.GOODS_CATE = '010203'
								 AND CG.GOODS_NO IN (SELECT GOODS_SUB_NO 
								FROM TBL_GOODS_SUB GS, TBL_GOODS G
							 WHERE GS.GOODS_NO = G.GOODS_NO
								 AND GS.GOODS_NO = '$goods_no')";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$case_goods_no  = $rows[0];

		$query = "SELECT FILE_NM1 FROM TBL_GOODS_IMAGES WHERE GOODS_NO = '$case_goods_no' ORDER BY GOODS_IMAGE_NO ASC";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function checkStock($db, $goods_no, $qty) {

		$return_flag = true;
		// 구성품이 있는지 확인
		$query = "SELECT GS.GOODS_SUB_NO, GS.GOODS_CNT 
		            FROM TBL_GOODS_SUB GS JOIN TBL_GOODS G ON G.GOODS_NO = GS.GOODS_SUB_NO 
				   WHERE GS.GOODS_NO = '$goods_no' 
				     AND G.GOODS_CATE NOT LIKE '0102%'  ";
		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		if (sizeof($record) > 0) {
			for ($i = 0 ; $i < sizeof($record); $i++) {
				
				$GOODS_SUB_NO		= trim($record[$i]["GOODS_SUB_NO"]);
				$GOODS_CNT			= trim($record[$i]["GOODS_CNT"]);
				// 구성품이 있는경우 
				//2017-07-20 익일 작업리스트 속도 때문에 아웃박스 재고 체크 부분 제외, 2017-07-24 케이스 전체 제외
				$query = "SELECT STOCK_CNT FROM TBL_GOODS WHERE GOODS_NO = '$GOODS_SUB_NO' ";
				$result = mysql_query($query,$db);
				$rows   = mysql_fetch_array($result);
				$stock_cnt  = $rows[0];
				
				//echo "stock_cnt -> ".$stock_cnt."<br>";

				if ($stock_cnt < ($qty * $GOODS_CNT)) {
					$return_flag = false;
				}

			}
		} else {
			// 구성품이 없는 경우
			$query = "SELECT STOCK_CNT FROM TBL_GOODS WHERE GOODS_NO = '$goods_no' ";

			//echo $query;
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$stock_cnt  = $rows[0];

			if ($stock_cnt < $qty) {
				$return_flag = false;
			}
		}
		
		return $return_flag;
	}

	function updateOptMemo($db, $order_goods_no, $opt_memo) {

		$query = "UPDATE TBL_ORDER_GOODS SET OPT_MEMO = '$opt_memo' WHERE ORDER_GOODS_NO = '$order_goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function listCompanyByIsPackage($db) {

		$query = "SELECT DISTINCT C.CP_NM, O.CP_NO
					FROM TBL_ORDER O
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
					WHERE O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND C.USE_TF =  'Y'
					AND C.DEL_TF =  'N'
					AND O.IS_PACKAGE = 'Y'
					AND (O.ORDER_STATE LIKE '%2%' OR O.ORDER_STATE LIKE '%3%')
					ORDER BY C.CP_NM";

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

	function listOrderDeliveryForMart_LEVEL1($db, $start_date, $end_date, $order_state, $cp_no) {

		$query = "SELECT DISTINCT C.CP_NM, O.CP_NO, O.R_ADDR1, O.R_MEM_NM
					FROM TBL_ORDER O
					LEFT JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
					WHERE OG.ORDER_DATE >=  '".$start_date."'
					AND OG.ORDER_DATE <=  '".$end_date." 23:59:59'
					AND O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					AND O.IS_PACKAGE = 'Y'
					AND OG.IS_PACKAGE = 'Y'
					";

		if($cp_no <> '')
			$query .= "AND O.CP_NO = '$cp_no' ";
		
		if($order_state <> '')
			$query .= "AND O.ORDER_STATE LIKE  '%$order_state%' ";
		
		$query .= "ORDER BY OG.REG_DATE DESC, O.CP_NO, O.R_ADDR1, O.ORDER_NO";

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

	function listOrderDeliveryForMart_LEVEL2($db, $start_date, $end_date, $company_no, $receiver_name,  $receiver_addr, $order_state) {

		$query = "SELECT OG.CP_ORDER_NO, O.RESERVE_NO, OG.ORDER_GOODS_NO, OG.ORDER_STATE, OG.GROUP_NO, O.R_MEM_NM, O.R_PHONE, O.R_HPHONE, O.R_ADDR1, O.MEMO, O.O_MEM_NM,  GG.GOODS_NO, GG.GOODS_NAME, GG.GOODS_SUB_NAME, GG.GOODS_CODE, GG.CATE_01, OG.OPT_STICKER_NO, OG.OPT_STICKER_MSG, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_OUTBOX_TF,
		CASE WHEN OG.ORDER_STATE =  '6'
		THEN OG.QTY * -1
		ELSE OG.QTY
		END AS QTY, GG.DELIVERY_CNT_IN_BOX, GG.CATE_04
					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_GOODS GG ON OG.GOODS_NO = GG.GOODS_NO
					WHERE OG.ORDER_DATE >=  '".$start_date."'
					AND OG.ORDER_DATE <=  '".$end_date." 23:59:59'
					AND O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N' 
					AND O.IS_PACKAGE = 'Y'
					AND OG.IS_PACKAGE = 'Y'
					AND O.R_ADDR1 = '$receiver_addr'
					AND O.R_MEM_NM = '$receiver_name'
					AND O.CP_NO = '$company_no'";
		
		if($order_state <> '')
			$query .= "AND OG.ORDER_STATE IN ($order_state) ";

		$query .= "ORDER BY GG.GOODS_NO, OG.OPT_STICKER_NO ";

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

	function listOrderDeliveryForMart($db, $order_goods_no_total) {

		$query = "SELECT DISTINCT OGD.ORDER_GOODS_DELIVERY_NO, 
						OGD.DELIVERY_SEQ, OGD.DELIVERY_CP, OGD.DELIVERY_NO, OGD.RECEIVER_PHONE, OGD.RECEIVER_HPHONE, OGD.GOODS_DELIVERY_NAME,  OGD.DELIVERY_FEE, OGD.DELIVERY_DATE, OGD.MEMO, OGD.ORDER_QTY, OGD.USE_TF
					FROM TBL_ORDER_GOODS_DELIVERY OGD
					JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OGOGD.ORDER_GOODS_DELIVERY_NO = OGD.ORDER_GOODS_DELIVERY_NO 
					
					WHERE OGOGD.ORDER_GOODS_NO IN ($order_goods_no_total) 
					  AND OGD.DEL_TF = 'N' 

					ORDER BY OGD.ORDER_GOODS_DELIVERY_NO";

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

	function chkOrderDelivery($db, $order_goods_no) {

		$query = "SELECT COUNT(*)
					FROM TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD
					JOIN TBL_ORDER_GOODS_DELIVERY OGD ON OGOGD.ORDER_GOODS_DELIVERY_NO = OGD.ORDER_GOODS_DELIVERY_NO
					WHERE OGOGD.ORDER_GOODS_NO IN ('$order_goods_no') 
					
					AND OGD.USE_TF='Y' 
					AND OGD.DEL_TF='N' 
				  ";

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function insertOrderDeliveryPerPaper($db, $cp_order_no, $r_mem_nm, $r_phone, $r_hphone, $r_addr1, $qty, $memo, $o_mem_nm, $order_phone, $order_manager_nm, $order_manager_phone, $payment_type, $send_cp_addr, $goods_delivery_name, $delivery_cp, $delivery_type, $delivery_fee, $delivery_fee_code, $s_adm_no) {

		$query = "INSERT INTO TBL_ORDER_GOODS_DELIVERY	(CP_ORDER_NO,
														 DELIVERY_SEQ, SEQ_OF_DAY, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, 
														 RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, 
														 ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, 
														 DELIVERY_CP, DELIVERY_NO, DELIVERY_TYPE, DELIVERY_DATE, DELIVERY_FEE, DELIVERY_FEE_CODE, REG_ADM, REG_DATE, USE_TF)
				   
				   VALUES ('$cp_order_no',  
						   '', '', '$r_mem_nm', '$r_phone', '$r_hphone', 
						   '$r_addr1', '$qty', '$memo', '$o_mem_nm', '$order_phone', 
						   '$order_manager_nm', '$order_manager_phone' , '$payment_type', '$send_cp_addr', '$goods_delivery_name', 
						   '$delivery_cp', '', '$delivery_type', '', '$delivery_fee', '$delivery_fee_code', '$s_adm_no', now(), 'Y' ); ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
	}

	
	//송장과 상품(바코드)을 연결하기 위한 입력 - 일단 제외 
	function InsertOrderGoodsByDeliveryNo($db, $order_goods_delivery_no, $goods_no, $goods_total, $sticker_no)
	{
		$query = "INSERT INTO TBL_TEMP_ORDER_GOODS_SCAN 
					(ORDER_GOODS_DELIVERY_NO, GOODS_NO, GOODS_TOTAL, STICKER_NO, REG_DATE)  
				  VALUES ('$order_goods_delivery_no', '$goods_no', '$goods_total', '$sticker_no', now() )";
		
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
	

	function updateOrderGoods_OrderGoodsDelivery($db, $order_goods_no, $order_goods_delivery_no) {

		$query = "INSERT INTO TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY	(ORDER_GOODS_NO, ORDER_GOODS_DELIVERY_NO)
				   VALUES ('$order_goods_no', '$order_goods_delivery_no'); ";

		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
	}

	function deleteOrderDeliveryForMartAll($db, $start_date, $end_date, $cp_no, $adm_no) {

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY A JOIN 
		              (SELECT DISTINCT OGD.ORDER_GOODS_DELIVERY_NO
									FROM TBL_ORDER O
									JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
									JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OG.ORDER_GOODS_NO = OGOGD.ORDER_GOODS_NO
									JOIN TBL_ORDER_GOODS_DELIVERY OGD ON OGOGD.ORDER_GOODS_DELIVERY_NO = OGD.ORDER_GOODS_DELIVERY_NO
									WHERE O.ORDER_DATE >=  '".$start_date."'
									AND O.ORDER_DATE <=  '".$end_date." 23:59:59'
									AND O.USE_TF =  'Y'
									AND O.DEL_TF =  'N'
									AND OG.ORDER_STATE = '2'
									AND OG.USE_TF =  'Y'
									AND OG.DEL_TF =  'N'
									AND O.IS_PACKAGE = 'Y'
									AND OG.IS_PACKAGE = 'Y'
									AND O.CP_NO = '$cp_no') B 
									
									ON A.ORDER_GOODS_DELIVERY_NO = B.ORDER_GOODS_DELIVERY_NO
									

					SET A.DEL_TF = 'Y',
					    A.DEL_ADM = '$adm_no'
					WHERE A.DELIVERY_NO = '' AND 
						  A.DELIVERY_SEQ = ''
								 ";

		
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

	//순번 입력
	function updateOrderGoodsDeliveryPaperSeq($db, $order_goods_delivery_no, $specific_date) {


		$max_seq = cntOrderGoodsDeliveryLastSeqMart($db, $specific_date);

		$seq = ($max_seq >= 5000  ? $max_seq + 1 : 5000);
		
		$delivery_seq = $specific_date."-".$seq;

		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
							SET 
								DELIVERY_SEQ	= '$delivery_seq', 
								SEQ_OF_DAY	= '$seq'								
							WHERE ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no' AND SEQ_OF_DAY = ''";
		
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

	//순번계산 리스팅
	function listOrderDeliveryForMart_LEVEL3($db, $start_date, $end_date, $order_state, $cp_no) {

		$query = "SELECT O.RESERVE_NO, OG.ORDER_GOODS_NO, OGD.ORDER_GOODS_DELIVERY_NO, G.GOODS_NAME, OG.QTY, OG.GOODS_NO, OGD.GOODS_DELIVERY_NAME, 
						LENGTH(OGD.GOODS_DELIVERY_NAME) - LENGTH(REPLACE(OGD.GOODS_DELIVERY_NAME, '//', '' ) ) AS ContainMulti, 
						CASE WHEN INSTR(OGD.RECEIVER_ADDR, '제주시') > 0 OR INSTR(OGD.RECEIVER_ADDR, '옹진군') > 0 OR INSTR(OGD.RECEIVER_ADDR, '서귀포시') > 0 OR INSTR(OGD.RECEIVER_ADDR, '울릉군') > 0 THEN 1 ELSE 0 END AS ContainLocation,
						CASE WHEN INSTR(OGD.GOODS_DELIVERY_NAME, '박스(') > 0 THEN 0 ELSE 1 END AS ContainBox,
						DELIVERY_CLAIM_CODE AS ContainSpecial

					FROM TBL_ORDER O
					JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					JOIN TBL_GOODS G ON OG.GOODS_NO = G.GOODS_NO
					JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OG.ORDER_GOODS_NO = OGOGD.ORDER_GOODS_NO
					JOIN TBL_ORDER_GOODS_DELIVERY OGD ON OGOGD.ORDER_GOODS_DELIVERY_NO = OGD.ORDER_GOODS_DELIVERY_NO
					WHERE OG.ORDER_DATE >=  '".$start_date."'
					AND OG.ORDER_DATE <=  '".$end_date." 23:59:59'
					AND O.USE_TF =  'Y'
					AND O.DEL_TF =  'N'
					AND O.IS_PACKAGE =  'Y'
					AND OG.USE_TF =  'Y'
					AND OG.DEL_TF =  'N'
					AND OG.IS_PACKAGE =  'Y'
					AND OGD.USE_TF = 'Y'
					AND OGD.DEL_TF = 'N'
					AND OGD.DELIVERY_SEQ = ''
					AND OGD.DELIVERY_NO = ''
					AND O.CP_NO = '$cp_no' ";

		if($order_state <> '')
			$query .= " AND OG.ORDER_STATE =  '$order_state' ";
		
		//2016-06-28 이마트 송장 순서중 섞이는 부분 때문에 수정
		//$query .= " ORDER BY ContainLocation, ContainSpecial, ContainMulti, ContainBox, G.GOODS_NAME, GS.GOODS_CNT * OG.QTY % G.DELIVERY_CNT_IN_BOX,  OGD.GOODS_DELIVERY_NAME, OGD.RECEIVER_ADDR";

		$query .= " ORDER BY ContainLocation, ContainSpecial, ContainMulti, ContainBox, OGD.GOODS_DELIVERY_NAME, OG.QTY % G.DELIVERY_CNT_IN_BOX,  G.GOODS_NO, OGD.RECEIVER_ADDR";
		
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

	function cntOrderGoodsDeliveryLastSeqMart($db, $specific_date) {
		
		$query = "SELECT MAX(SEQ_OF_DAY) AS CNT
					FROM TBL_ORDER_GOODS_DELIVERY 
					WHERE DELIVERY_SEQ LIKE  '".$specific_date."%' 
						AND USE_TF =  'Y'
						AND DEL_TF =  'N'
						AND INSTR( REPLACE( DELIVERY_SEQ, '".$specific_date."-', '' ), SEQ_OF_DAY) > 0
						";

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	//로딩된 송장 리스트
	function listOrderDeliveryExcelForMart($db, $specific_date, $cp_no) {

		$company_name = getCompanyNameWithNoCode($db, $cp_no);
		$query = "SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_NO, DELIVERY_SEQ, RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, GOODS_DELIVERY_NAME, ORDER_QTY, MEMO, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, ORDER_NM, ORDER_PHONE, DELIVERY_FEE, DELIVERY_FEE_CODE, PAYMENT_TYPE, SEND_CP_ADDR
					
					FROM TBL_ORDER_GOODS_DELIVERY 
					WHERE DELIVERY_SEQ LIKE '$specific_date%'
						AND SEQ_OF_DAY  >= 5000
						AND USE_TF =  'Y'
						AND DEL_TF =  'N'
						AND DELIVERY_NO = ''
						";

		if($company_name <> '&nbsp;')
			$query .= " AND ORDER_MANAGER_NM = '$company_name' ";

		$query .= " ORDER BY SEQ_OF_DAY ";

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

	// 출고 안된 송장번호 반환
	function listOrderGoodsDeliveryNumberComplete($db, $delivery_seq, $delivery_no) {
	
		$query = "SELECT ORDER_GOODS_DELIVERY_NO, CP_ORDER_NO, ORDER_MANAGER_NM, DELIVERY_CLAIM_CODE 
							FROM TBL_ORDER_GOODS_DELIVERY 
							WHERE DELIVERY_SEQ		= '$delivery_seq' 
							  AND DELIVERY_NO	= '$delivery_no' 
							  AND USE_TF = 'Y' 
						      AND DEL_TF = 'N'
							  AND SEQ_OF_DAY >= 5000
							  AND DELIVERY_DATE <> ''
							  AND OUTSTOCK_TF = 'N' ";

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

	//업체 주문번호로 시스템 주문번호 찾기
	function selectReserveNoByCompanyOrderNo($db, $cp_order_no) {

		$query = "SELECT DISTINCT RESERVE_NO FROM TBL_ORDER_GOODS WHERE CP_ORDER_NO = '".$cp_order_no."' ";
	
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			return $rows[0];
		} else {
			return "";
		}
	}

	//합포된 주문상품 번호가져오기 - 송장 합포 출고용
	function selectOrderGoodsNoByPackage($db, $order_goods_delivery_no) {

		$query = "SELECT CONVERT(GROUP_CONCAT(OGOGD.ORDER_GOODS_NO SEPARATOR ',  ') USING utf8) AS Categories
					FROM TBL_ORDER_GOODS_DELIVERY OGD 
					JOIN TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY OGOGD ON OGD.ORDER_GOODS_DELIVERY_NO = OGOGD.ORDER_GOODS_DELIVERY_NO
				   WHERE OGD.ORDER_GOODS_DELIVERY_NO = ".$order_goods_delivery_no." AND OGD.USE_TF = 'Y' AND OGD.DEL_TF='N' ";
	
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			return $rows[0];
		} else {
			return "";
		}
	}

	// 송장에 엮인 스캔해야할 문건들 리스트 가져오기
	function selectOrderDeliveryGoods($db, $order_goods_delivery_no) {

		$query = "SELECT S.DELIVERY_GOODS_SEQ, S.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, S.GOODS_TOTAL
					FROM TBL_TEMP_ORDER_GOODS_SCAN S 
					JOIN TBL_GOODS G ON S.GOODS_NO = G.GOODS_NO
					WHERE 1 = 1 AND S.ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no'  ";

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

	function appendOrderDeliveryPaperMart($db, $specific_date, $order_goods_delivery_no, $delivery_seq_tf, $upd_admin_no) {

		if($delivery_seq_tf == 'Y')
		{
			$max_seq = cntOrderGoodsDeliveryLastSeqMart($db, $specific_date);

			$seq = ($max_seq >= 5000 ? $max_seq + 1 : 5000);
			$delivery_seq = $specific_date ."-".$seq;
		}
		
		//$DELIVERY_PROFIT_CODE = 'DP001';
		//$DELIVERY_PROFIT = getDcodeName($db, 'DELIVERY_PROFIT', $DELIVERY_PROFIT_CODE);

		$query = " INSERT INTO TBL_ORDER_GOODS_DELIVERY
						(DELIVERY_SEQ, SEQ_OF_DAY, CP_ORDER_NO,
						 RECEIVER_NM, RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, 
						 PAYMENT_TYPE, SEND_CP_ADDR, GOODS_DELIVERY_NAME, DELIVERY_CP, DELIVERY_TYPE,  
						 DELIVERY_FEE, DELIVERY_FEE_CODE, REG_ADM, REG_DATE)
				   SELECT  '$delivery_seq', '$seq', CP_ORDER_NO,
						   CONCAT(RECEIVER_NM, ' +1'), RECEIVER_PHONE, RECEIVER_HPHONE, RECEIVER_ADDR, ORDER_QTY, MEMO, ORDER_NM, ORDER_PHONE, ORDER_MANAGER_NM, ORDER_MANAGER_PHONE, 
						   PAYMENT_TYPE, SEND_CP_ADDR, CONCAT(GOODS_DELIVERY_NAME, ' - 추가송장'), DELIVERY_CP, DELIVERY_TYPE,  
						   DELIVERY_FEE, DELIVERY_FEE_CODE, '$upd_admin_no', now()
					FROM TBL_ORDER_GOODS_DELIVERY
					WHERE ORDER_GOODS_DELIVERY_NO = '$order_goods_delivery_no' ";

		//echo $query;
		//exit;

		if(mysql_query($query,$db)) {

			$inserted_order_goods_delivery_no = mysql_insert_id();

			$query2 = "INSERT INTO TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY (ORDER_GOODS_NO, ORDER_GOODS_DELIVERY_NO)
					  SELECT ORDER_GOODS_NO, '$inserted_order_goods_delivery_no'
					  FROM TBL_ORDER_GOODS_ORDER_GOODS_DELIVERY
					  WHERE ORDER_GOODS_DELIVERY_NO = $order_goods_delivery_no ";
					 
			
			if(!mysql_query($query2,$db)) {
				return null;
			}
			else
			{
				$query3 = " SELECT ORDER_GOODS_DELIVERY_NO, DELIVERY_SEQ, DELIVERY_NO, GOODS_DELIVERY_NAME, DELIVERY_FEE
							FROM TBL_ORDER_GOODS_DELIVERY
							WHERE ORDER_GOODS_DELIVERY_NO = '$inserted_order_goods_delivery_no' ";

				//echo $query3;
				//exit;

				$result = mysql_query($query3,$db);
				$record = array();
				

				if ($result <> "") {
					for($i=0;$i < mysql_num_rows($result);$i++) {
						$record[$i] = sql_result_array($result,$i);
					}
				}

				return $record;

			}
		}
		else
			return null;
	}

	
	//$combined_type = 0 - 박스작업, $combined_type = 1 - 낱개작업, $combined_type = 2 - 합포작업, $combined_type = 3 - 낱개+합포작업
	function listOrderDeliveryWarehouseForMart($db, $start_date, $end_date, $from_seq_of_day, $to_seq_of_day, $cp_nm, $combined_type, $has_island) {

		$query = "
					SELECT GOODS_CODE, KANCODE, GOODS_NAME, STOCK_CNT, DELIVERY_CNT_IN_BOX, SUM(GOODS_TOTAL) AS GOODS_CNT_SUM 
					FROM (
						SELECT G.GOODS_CODE, G.KANCODE, G.GOODS_NAME, G.STOCK_CNT, G.DELIVERY_CNT_IN_BOX, OGS.GOODS_TOTAL 
						FROM TBL_ORDER_GOODS_DELIVERY OGD 
						JOIN TBL_TEMP_ORDER_GOODS_SCAN OGS ON OGD.ORDER_GOODS_DELIVERY_NO = OGS.ORDER_GOODS_DELIVERY_NO
						JOIN TBL_GOODS G ON OGS.GOODS_NO = G.GOODS_NO
						WHERE OGD.USE_TF = 'Y' 
						  AND OGD.DEL_TF = 'N'
						  AND OGD.DELIVERY_NO <> '' 
						  AND OGD.OUTSTOCK_TF = 'N' ";


		if($start_date <> '')
			$query.= "    AND OGD.REG_DATE >= '".$start_date." 00:00:00' ";

		if($end_date <> '')
			$query.= "    AND OGD.REG_DATE <= '".$end_date." 23:59:59' ";

		if($from_seq_of_day <> '')
			$query.= "    AND OGD.SEQ_OF_DAY >= ".$from_seq_of_day." ";

		if($to_seq_of_day <> '')
			$query.= "    AND OGD.SEQ_OF_DAY <= ".$to_seq_of_day." ";

		if($combined_type == "0")
			$query.= "    AND OGD.DELIVERY_PROFIT_CODE = 'DP002' ";
		else if($combined_type == "1")
			$query.= "    AND (OGD.DELIVERY_PROFIT_CODE <> 'DP002' AND LENGTH(OGD.GOODS_DELIVERY_NAME) - LENGTH(REPLACE(OGD.GOODS_DELIVERY_NAME, '//', '' ) ) = 0 ) ";
		else if($combined_type == "2")
			$query.= "    AND LENGTH(OGD.GOODS_DELIVERY_NAME) - LENGTH(REPLACE(OGD.GOODS_DELIVERY_NAME, '//', '' ) ) > 0 ";
		else if($combined_type == "3")
			$query.= "    AND OGD.DELIVERY_PROFIT_CODE <> 'DP002' ";

		if($cp_nm <> '')
		{
			$query.= "
					      AND OGD.ORDER_MANAGER_NM = '".$cp_nm."'
					 ";
		}

		if($has_island != "on") 
			$query .= "   AND INSTR(OGD.RECEIVER_ADDR, '제주시') = 0 AND INSTR(OGD.RECEIVER_ADDR, '옹진군') = 0 AND INSTR(OGD.RECEIVER_ADDR, '서귀포시') = 0 AND INSTR(OGD.RECEIVER_ADDR, '울릉군') = 0 ";

		$query.= "
					      ) A
						GROUP BY GOODS_CODE, KANCODE, GOODS_NAME, STOCK_CNT, DELIVERY_CNT_IN_BOX
						ORDER BY GOODS_NAME
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

	//$combined_type = 0 - 박스작업, $combined_type = 1 - 낱개작업, $combined_type = 2 - 합포작업, $combined_type = 3 - 낱개+합포작업
	function listOrderDeliveryWarehouseForMart_Sticker($db, $start_date, $end_date, $from_seq_of_day, $to_seq_of_day, $cp_nm, $combined_type, $has_island) {

		$query = "
					SELECT GOODS_CODE, KANCODE, GOODS_NAME, STOCK_CNT, DELIVERY_CNT_IN_BOX, SUM(GOODS_TOTAL) AS GOODS_CNT_SUM 
					FROM (
						SELECT G.GOODS_CODE, G.KANCODE, G.GOODS_NAME, G.STOCK_CNT, G.DELIVERY_CNT_IN_BOX, OGS.GOODS_TOTAL 
						FROM TBL_ORDER_GOODS_DELIVERY OGD 
						JOIN TBL_TEMP_ORDER_GOODS_SCAN OGS ON OGD.ORDER_GOODS_DELIVERY_NO = OGS.ORDER_GOODS_DELIVERY_NO
						JOIN TBL_GOODS G ON OGS.STICKER_NO = G.GOODS_NO
						WHERE OGD.USE_TF = 'Y' 
						  AND OGD.DEL_TF = 'N'
						  AND OGD.DELIVERY_NO <> '' 
						  AND OGD.OUTSTOCK_TF = 'N' ";


		if($start_date <> '')
			$query.= "    AND OGD.REG_DATE >= '".$start_date." 00:00:00' ";

		if($end_date <> '')
			$query.= "    AND OGD.REG_DATE <= '".$end_date." 23:59:59' ";

		if($from_seq_of_day <> '')
			$query.= "    AND OGD.SEQ_OF_DAY >= ".$from_seq_of_day." ";

		if($to_seq_of_day <> '')
			$query.= "    AND OGD.SEQ_OF_DAY <= ".$to_seq_of_day." ";

		if($combined_type == "0")
			$query.= "    AND OGD.DELIVERY_PROFIT_CODE = 'DP002' ";
		else if($combined_type == "1")
			$query.= "    AND (OGD.DELIVERY_PROFIT_CODE <> 'DP002' AND LENGTH(OGD.GOODS_DELIVERY_NAME) - LENGTH(REPLACE(OGD.GOODS_DELIVERY_NAME, '//', '' ) ) = 0 ) ";
		else if($combined_type == "2")
			$query.= "    AND LENGTH(OGD.GOODS_DELIVERY_NAME) - LENGTH(REPLACE(OGD.GOODS_DELIVERY_NAME, '//', '' ) ) > 0 ";
		else if($combined_type == "3")
			$query.= "    AND OGD.DELIVERY_PROFIT_CODE <> 'DP002' ";

		if($cp_nm <> '')
		{
			$query.= "
					      AND OGD.ORDER_MANAGER_NM = '".$cp_nm."'
					 ";
		}

		if($has_island != "on") 
			$query .= "   AND INSTR(OGD.RECEIVER_ADDR, '제주시') = 0 AND INSTR(OGD.RECEIVER_ADDR, '옹진군') = 0 AND INSTR(OGD.RECEIVER_ADDR, '서귀포시') = 0 AND INSTR(OGD.RECEIVER_ADDR, '울릉군') = 0 ";

		$query.= "
					      ) A
						GROUP BY GOODS_CODE, KANCODE, GOODS_NAME, STOCK_CNT, DELIVERY_CNT_IN_BOX
						ORDER BY GOODS_NAME
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

	//마트 작업 & 출고 리스트
	function updateOrderGoodsDeliveryPaperOutStockStatus($db, $order_goods_delivery_no, $outstock_tf)
	{
		$query = "UPDATE TBL_ORDER_GOODS_DELIVERY 
							SET 
								OUTSTOCK_TF = '$outstock_tf',
								OUTSTOCK_DATE = now()
							WHERE ORDER_GOODS_DELIVERY_NO	= '$order_goods_delivery_no' ";
		
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


	function listWorkListCompleted($db, $work_date, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntWorkListCompleted($db, $work_date, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT distinct O.ORDER_NO, OG.ORDER_GOODS_NO, O.RESERVE_NO, O.ORDER_DATE, OG.OPT_OUTSTOCK_DATE, O.CP_NO,  
										 O.O_MEM_NM, O.R_MEM_NM, OG.GOODS_NO, OG.CATE_01, OG.CATE_04, OG.GOODS_NAME, OG.QTY, O.OPT_MANAGER_NO, OG.OPT_MEMO, OG.WORK_QTY, OG.WORK_START_DATE,
										 (SELECT IFNULL(MAX(WORK_ORDER),100000) FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) AS WORK_ORDER,
										 (SELECT WORK_LINE FROM TBL_ORDER_WORK OW WHERE OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND OW.WORK_TYPE = 'OUTCASE') AS WORK_LINE,
										 O.BULK_TF, OG.OPT_STICKER_NO, OG.OPT_OUTBOX_TF, OG.OPT_WRAP_NO, OG.OPT_PRINT_MSG, OG.OPT_STICKER_MSG,
										 OG.WORK_SEQ, OG.DELIVERY_CNT_IN_BOX, OG.WORK_MSG, OG.DELIVERY_TYPE, OG.DELIVERY_CP,
										 (SELECT MAX(REG_DATE) FROM TBL_ORDER_WORK_HISTORY OH WHERE OH.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND OH.DEL_TF = 'N') AS WORK_HISTORY
								FROM TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK_HISTORY OW
							 WHERE O.RESERVE_NO = OG.RESERVE_NO 
								 AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
								 AND OG.ORDER_STATE IN (2,3) 
								 AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
								 AND OG.WORK_QTY > 0   
								 AND OW.REG_DATE > '".$work_date."' AND OW.REG_DATE <= '".$work_date." 23:59:59'
								 AND OW.DEL_TF = 'N' ";


		$order_field = "WORK_HISTORY DESC";
		$query .= " ORDER BY ".$order_field." limit ".$offset.", ".$nRowCount;

		echo $query;
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

	function totalCntWorkListCompleted($db, $work_date, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(distinct OW.ORDER_GOODS_NO) CNT
							 FROM  TBL_ORDER O, TBL_ORDER_GOODS OG, TBL_ORDER_WORK_HISTORY OW
							WHERE O.RESERVE_NO = OG.RESERVE_NO
							  AND OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
							  AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
							  AND OG.WORK_QTY > 0 
							  AND OG.ORDER_STATE IN (2,3)
							  AND OW.REG_DATE > '".$work_date."' AND OW.REG_DATE <= '".$work_date." 23:59:59'
							  AND OW.DEL_TF = 'N' ";


		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	//작업완료 히스토리
	function selectOrderWorkHistory($db, $order_goods_no) {

		$query = "SELECT WORK_DONE_NO, WORK_TYPE, QTY, REG_DATE, REG_ADM
					FROM TBL_ORDER_WORK_HISTORY
				   WHERE ORDER_GOODS_NO = '$order_goods_no' AND DEL_TF = 'N' 
				ORDER BY REG_DATE DESC  ";
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

	//작업완료 히스토리 - 주문기준
	function selectOrderWorkHistoryByReserveNo($db, $reserve_no) {

		$query = "SELECT OW.ORDER_GOODS_NO, OW.WORK_DONE_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME, OW.WORK_TYPE, OW.QTY, OW.REG_DATE, OW.REG_ADM
					FROM TBL_ORDER_WORK_HISTORY OW
					JOIN TBL_ORDER_GOODS OG ON OW.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
				   WHERE OG.RESERVE_NO = '$reserve_no' AND OW.DEL_TF = 'N' AND OG.DEL_TF = 'N' 
				ORDER BY OG.ORDER_GOODS_NO DESC, OW.REG_DATE DESC  ";
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

	//주문상품 작업수량 되돌리기
	function updateOrderGoodsWorkUndo($db, $work_done_no, $del_adm) {

		$query = "SELECT ORDER_GOODS_NO, QTY
					FROM TBL_ORDER_WORK_HISTORY  
				   WHERE WORK_DONE_NO = '$work_done_no' AND DEL_TF = 'N' ";
		
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		

		if (sizeof($record) > 0) {
			for ($i = 0 ; $i < sizeof($record); $i++) {
				
				$order_goods_no		= trim($record[$i]["ORDER_GOODS_NO"]);
				$qty				= trim($record[$i]["QTY"]);

				$query = "UPDATE TBL_ORDER_GOODS
							 SET WORK_QTY = WORK_QTY - $qty, WORK_FLAG = 'N', WORK_END_DATE = '0000-00-00 00:00:00' 
						   WHERE ORDER_GOODS_NO = '$order_goods_no' ";

				//echo $query;
				//exit;
				
				mysql_query($query,$db);

			}
			
			$query = "UPDATE TBL_ORDER_WORK_HISTORY
						 SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					   WHERE WORK_DONE_NO = '$work_done_no' ";

			//echo $query;
			//exit;

			mysql_query($query,$db);
		}
	
	}

		//금일 작업 자재리스트
	function listWorkGoodsCompleted($db, $work_date) {

		$query = "SELECT DISTINCT OG.GOODS_NO, OW.QTY AS WORK_QTY, OG.DELIVERY_CNT_IN_BOX, G.GOODS_CATE
					FROM TBL_ORDER_GOODS OG, TBL_ORDER_WORK_HISTORY OW, TBL_GOODS G
				   WHERE OG.ORDER_GOODS_NO = OW.ORDER_GOODS_NO
					 AND OG.GOODS_NO = G.GOODS_NO
				     AND OG.DELIVERY_TYPE <> 98 AND OG.DELIVERY_TYPE <> 99
				     AND OG.WORK_QTY > 0 AND OG.ORDER_STATE IN (2,3) 
				     AND OW.REG_DATE > '".$work_date."' AND OW.REG_DATE <= '".$work_date." 23:59:59'
				     AND OW.DEL_TF = 'N' 
 				ORDER BY G.GOODS_NAME ";

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

	function listOrderWorkLog($db, $orderGoodsNo){
		$query="SELECT 	OWL.WORK_SEQ, OWL.DEL_TF, OWL.DEL_ADMIN, OWL.REG_ADMIN, OWL.REG_DATE
				FROM	T_ORDER_WORK_LOG OWL
				WHERE	OWL.ORDER_GOODS_NO = '$orderGoodsNo'
				ORDER BY REG_DATE
				";

				// echo $query;

		$result=mysql_query($query, $db);
		$record=array();
		$cnt=0;

		if($result<>""){
			$cnt=mysql_num_rows($result);
		}
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
			
		}
		return $record;
	}//end of function_listOrderWorkLog


?>