<?

	//발주로 부터의 기장
	function insertCompanyLedgerDepositFromGoodsRequest($db, $req_no, $confirm_date, $reg_adm) {

		$return_value = false;

		$query="SELECT GR.BUY_CP_NO, GRG.RESERVE_NO, GRG.ORDER_GOODS_NO, GRG.GOODS_NO, GRG.GOODS_NAME, GRG.GOODS_SUB_NAME, GRG.RECEIVE_QTY, GRG.BUY_PRICE, GRG.RECEIVE_DATE, GRG.TO_HERE, GRG.REQ_QTY, GR.SENT_DATE, GRG.CONFIRM_TF, GRG.MEMO2, GRG.REG_DATE
				  FROM TBL_GOODS_REQUEST_GOODS GRG 
				  JOIN TBL_GOODS_REQUEST GR ON GRG.REQ_NO = GR.REQ_NO 
				 WHERE GRG.CANCEL_TF = 'N' AND GRG.DEL_TF = 'N' AND GRG.REQ_GOODS_NO = '$req_no' ";
		
		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$BUY_CP_NO		= $record[$i]["BUY_CP_NO"];
				$RESERVE_NO		= $record[$i]["RESERVE_NO"];
				$ORDER_GOODS_NO	= $record[$i]["ORDER_GOODS_NO"];
				$GOODS_NO		= $record[$i]["GOODS_NO"];
				$GOODS_NAME		= $record[$i]["GOODS_NAME"];
				$GOODS_SUB_NAME	= $record[$i]["GOODS_SUB_NAME"];
				$RECEIVE_QTY	= $record[$i]["RECEIVE_QTY"];
				$RECEIVE_DATE	= $record[$i]["RECEIVE_DATE"];
				$BUY_PRICE		= $record[$i]["BUY_PRICE"];
				$TO_HERE		= $record[$i]["TO_HERE"];
				$REQ_QTY		= $record[$i]["REQ_QTY"];
				$SENT_DATE		= $record[$i]["SENT_DATE"];
				$CONFIRM_TF		= $record[$i]["CONFIRM_TF"];
				$MEMO2			= $record[$i]["MEMO2"];
				$REG_DATE		= $record[$i]["REG_DATE"];  //기장기준일 추가 2017-06-02

				//기장기준일 추가 2017-06-02
				//기장확인일이 기준일 이후면 기장되도록 수정 (등록일 -> 확인일)
				$base_date = getDcodeExtByCode($db, "LEDGER_SETUP", "BASE_DATE");
				if($base_date >= $confirm_date) continue;

				$goods_name = $GOODS_NAME." ".$GOODS_SUB_NAME;
				$tax_tf = getGoodsTaxTF($db, $GOODS_NO);

				
				if($CONFIRM_TF == 'N') { 
					if($TO_HERE == "Y") { 

						//부분입고나 일부 취소가 있을수 있으므로 부분 입고시엔 매입할인으로 처리 2017-05-12
						$WITHDRAW = $REQ_QTY * $BUY_PRICE;
						//WITHDRAW, DEPOSIT 입력전에 ROUND() 처리 (60 * 1666.660 = 9999.7 같은 문제로) 2018.11.14 
						$WITHDRAW = ROUND($WITHDRAW);
						//$WITHDRAW = $RECEIVE_QTY * $BUY_PRICE;

						if($RECEIVE_QTY != 0) { 
							$query="INSERT INTO TBL_COMPANY_LEDGER 
									(CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, TAX_TF, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, RGN_NO, REG_ADM, REG_DATE)
									 VALUES ('$BUY_CP_NO', '$confirm_date', '매입', '$GOODS_NO', '$goods_name', '$REQ_QTY', '$BUY_PRICE', '$WITHDRAW', '0', '$tax_tf', '$MEMO2', '$RESERVE_NO', '$ORDER_GOODS_NO', '발주수령', '$req_no',  '$reg_adm', now()); ";
		
							//echo $query."<br/>";
							//exit;
							
							mysql_query($query,$db);
							$cl_no = mysql_insert_id();

							updateGoodsRequestConfirm($db, $req_no, $reg_adm);
							
							$return_value = true;
						}
						
					} else { 

						//부분입고나 일부 취소가 있을수 있으므로 부분 입고시엔 매입할인으로 처리 2017-05-12
						$WITHDRAW = $REQ_QTY * $BUY_PRICE;
						//WITHDRAW, DEPOSIT 입력전에 ROUND() 처리 (60 * 1666.660 = 9999.7 같은 문제로) 2018.11.14 
						$WITHDRAW = ROUND($WITHDRAW);

						$query="INSERT INTO TBL_COMPANY_LEDGER 
							(CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, TAX_TF, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, RGN_NO, REG_ADM, REG_DATE)
							 VALUES ('$BUY_CP_NO', '$confirm_date', '매입', '$GOODS_NO', '$goods_name', '$REQ_QTY', '$BUY_PRICE', '$WITHDRAW', '0', '$tax_tf', '$MEMO2', '$RESERVE_NO', '$ORDER_GOODS_NO', '발주직송', '$req_no', '$reg_adm', now()); ";

						//echo $query."<br/>";
						//exit;

						mysql_query($query,$db);
						$cl_no = mysql_insert_id();

						updateGoodsRequestConfirm($db, $req_no, $reg_adm);
						$return_value = true;
					}

					
					//주문상품에 매입가 적용 -원가계산
					if($ORDER_GOODS_NO > 0) { 
						
						$query="UPDATE TBL_ORDER_GOODS
								   SET BUY_PRICE = '$BUY_PRICE'
								 WHERE ORDER_GOODS_NO = '$ORDER_GOODS_NO' OR CLAIM_ORDER_GOODS_NO = '$ORDER_GOODS_NO' ";
	
						//echo $query."<br/>";
						//exit;
						
						mysql_query($query,$db);
					}
				}

				//서브항목들 기장
				$query = "SELECT GRGL_NO, NAME, QTY, UNIT_PRICE, MEMO 
							FROM TBL_GOODS_REQUEST_GOODS_LEDGER 
						   WHERE REQ_GOODS_NO = '$req_no' AND CONFIRM_TF = 'N' AND DEL_TF = 'N' ";

				//echo $query."<br/>";
				$result = mysql_query($query,$db);
				$record = array();

				if ($result <> "") {
					for($j=0;$j < mysql_num_rows($result);$j++) {
						$record[$j] = sql_result_array($result,$j);

						$GRGL_NO			= $record[$j]["GRGL_NO"];
						$EXTRA_NAME			= $record[$j]["NAME"];
						$EXTRA_QTY			= $record[$j]["QTY"];
						$EXTRA_UNIT_PRICE	= $record[$j]["UNIT_PRICE"];
						$EXTRA_MEMO			= $record[$j]["MEMO"];
						$EXTRA_WITHDRAW		= $EXTRA_QTY * $EXTRA_UNIT_PRICE;

						//WITHDRAW, DEPOSIT 입력전에 ROUND() 처리 (60 * 1666.660 = 9999.7 같은 문제로) 2018.11.14 
						$EXTRA_WITHDRAW = ROUND($EXTRA_WITHDRAW);

						$query="INSERT INTO TBL_COMPANY_LEDGER 
							(CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, RGN_NO, GRGL_NO, REG_ADM, REG_DATE)
							 VALUES ('$BUY_CP_NO', '$confirm_date', '매입', '$EXTRA_NAME', '$EXTRA_QTY', '$EXTRA_UNIT_PRICE', '$EXTRA_WITHDRAW', '0', '$EXTRA_MEMO', '$RESERVE_NO', '$ORDER_GOODS_NO', '발주기타', '$req_no', '$GRGL_NO', '$reg_adm', now()); ";
				
						if(mysql_query($query,$db)) { 

							$query="UPDATE TBL_GOODS_REQUEST_GOODS_LEDGER 
									   SET CONFIRM_TF = 'Y', CONFIRM_DATE = now()
									 WHERE GRGL_NO = '$GRGL_NO' ";
							mysql_query($query,$db);
						}

					}
				}

			}
		}
		return $return_value;
	}

	//발주 매입확정 취소
	function deleteCompanyLedgerDepositFromGoodsRequest($db, $req_no, $del_adm) {
		
		$query="UPDATE TBL_GOODS_REQUEST_GOODS  
				   SET CONFIRM_TF = 'N', CONFIRM_DATE = NULL, CONFIRM_ADM = NULL
				 WHERE CANCEL_TF = 'N' AND DEL_TF = 'N' AND REQ_GOODS_NO = '$req_no' AND CONFIRM_TF = 'Y' ";
		
		//echo $query."<br/>";
		if(mysql_query($query,$db)) { 
			$query="UPDATE TBL_COMPANY_LEDGER 
					   SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					 WHERE RGN_NO = '$req_no' ";

			//echo $query."<br/>";
			//exit;
			
			mysql_query($query,$db);
		}
						
					
		//서브항목들 기장
		$query = "SELECT GRGL_NO
						FROM TBL_GOODS_REQUEST_GOODS_LEDGER 
					   WHERE REQ_GOODS_NO = '$req_no' AND CONFIRM_TF = 'N' AND DEL_TF = 'N' ";

		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {

			for($j=0;$j < mysql_num_rows($result);$j++) {
				$record[$j] = sql_result_array($result,$j);

				$GRGL_NO			= $record[$j]["GRGL_NO"];

				$query="UPDATE TBL_COMPANY_LEDGER 
						   SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
						 WHERE GRGL_NO = '$req_no' ";
					
				mysql_query($query,$db);
			}

			$query = "UPDATE TBL_GOODS_REQUEST_GOODS_LEDGER
						 SET CONFIRM_TF = 'N', CONFIRM_DATE = NULL
					   WHERE REQ_GOODS_NO = '$req_no' AND CONFIRM_TF = 'Y' AND DEL_TF = 'N' ";

			//echo $query."<br/>";
			mysql_query($query,$db);
		}

	}


	function insertCompanyLedger($db, $cp_no, $inout_date, $inout_type, $goods_no, $name, $qty, $unit_price, $to_cp_no, $surtax, $cate_01, $tax_tf, $memo, $reserve_no, $order_goods_no, $input_type, $rgn_no, $reg_adm, $options) {

		//금액이 0으로는 기장되지 않도록 수정
		if($unit_price == 0) return true;

		$inout_type_name = getDcodeName($db, "COMPANY_LEDGER_TYPE", $inout_type);

		//중복체크
		/*
		$query .= " SELECT COUNT(*)
					  FROM TBL_COMPANY_LEDGER
					 WHERE CP_NO = '$cp_no' AND INOUT_TYPE = '$inout_type_name' AND GOODS_NO = '$goods_no' AND NAME = '$name' AND QTY = '$qty' AND UNIT_PRICE = '$unit_price' AND ORDER_GOODS_NO = '$order_goods_no' AND INPUT_TYPE = '$input_type' ";

		//echo $query;
		//exit;

		//$result = mysql_query($query,$db);
		//$rows   = mysql_fetch_array($result);
		//if($rows[0] > 0) return false;
		*/

		
		if(startsWith($inout_type, "L")) { 
			$deposit = $qty * $unit_price;
			$withdraw = 0;
		}
		else if(startsWith($inout_type, "R")) { 
			$withdraw = $qty * $unit_price;
			$deposit = 0;
		}

		if(($inout_type == "RX05" || $inout_type == "LX06") && $to_cp_no != null)  { 
			$group_no = maxCompanyLedgerGroupNo($db);

			$query="INSERT INTO TBL_COMPANY_LEDGER 
								(GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO,  RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, REG_ADM, REG_DATE)
					 VALUES ($group_no, '$cp_no', '$to_cp_no', '$inout_date', '$inout_type_name', '$goods_no', '$name', '$qty', '$unit_price', '$withdraw', '$deposit', '$surtax', '$memo', '$reserve_no', '$order_goods_no', '$input_type', '$reg_adm', now()); ";
		}
		else { 
			$group_no = null;
			$query="INSERT INTO TBL_COMPANY_LEDGER 
								(CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, CATE_01, TAX_TF, MEMO,  RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, REG_ADM, REG_DATE"; 
				
			if ($options['CLAIM_ORDER_GOODS_NO'] <> "") {
				$query .= " , CLAIM_ORDER_GOODS_NO ";
			}
			
			$query.=			")
				 VALUES ('$cp_no', '$to_cp_no', '$inout_date', '$inout_type_name', '$goods_no', '$name', '$qty', '$unit_price', '$withdraw', '$deposit', '$surtax', '$cate_01', '$tax_tf', '$memo', '$reserve_no', '$order_goods_no', '$input_type', '$reg_adm', now()";

					 
			if ($options['CLAIM_ORDER_GOODS_NO'] <> "") {
				$query .= " , '".$options['CLAIM_ORDER_GOODS_NO']."' ";
			}	

			$query.=        "); ";
		}

		//echo $query."<br/>";
		//exit;

		if(mysql_query($query,$db)) { 

			if($group_no != null) { 

				$t_deposit = 0;
				
				if($inout_type == "RX05") { 
					$t_inout_type_name = "대입";
					$t_withdraw = 0;
					$t_deposit = $qty * $unit_price - $surtax;
				} else { 
					$t_inout_type_name = "대체";
					$t_deposit = 0;
					$t_withdraw = $qty * $unit_price - $surtax;
				}

				$t_name = getCompanyName($db, $cp_no);


				//2017-06-02 직접 입력한 기장을 찾기 위해 대입에 대한 등록자 제거
				$query="INSERT INTO TBL_COMPANY_LEDGER 
									(GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, REG_ADM, REG_DATE)
						 VALUES ('$group_no', '$to_cp_no', '$cp_no', '$inout_date', '$t_inout_type_name', '$goods_no', '$t_name', '$qty', '$unit_price', '$t_withdraw', '$t_deposit', '$surtax', '$memo', '$reserve_no', '$order_goods_no', '대입', '', now()); ";
				//echo $query."<br/>";
				//exit;
				mysql_query($query,$db);
			}

			return true;
		} else 
			return false;
	}

	// 전기이월
	function getCompanyLedgerPreviousMonth($db, $start_month, $cp_no) { 

		if($cp_no == "") return;

		$query = "SELECT IFNULL(FLOOR(SUM(DEPOSIT)) - FLOOR(SUM(WITHDRAW)) , 0) AS BALANCE
					FROM TBL_COMPANY_LEDGER WHERE INOUT_DATE < '".$start_month."' AND CP_NO = '".$cp_no."' AND DEL_TF = 'N' AND USE_TF = 'Y' ";

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

	function listCompanyLedger($db, $start_date, $end_date, $cp_no, $order_field = "", $order_str = "", $search_field = "", $search_str = "", $nRowCount = 10000) {

		$query = "SELECT CL_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, ROUND(WITHDRAW, 0) AS WITHDRAW, 
							ROUND(DEPOSIT, 0) AS DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, TAX_CONFIRM_TF, TAX_CONFIRM_DATE, 
							USE_TF, CATE_01, TAX_TF, CF_CODE, INPUT_TYPE
					FROM TBL_COMPANY_LEDGER
				   WHERE 1 = 1  AND DEL_TF = 'N' ";
		
		if ($start_date <> "") {
			$query .= " AND INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if($search_field == "LATEST_5_BY_REG_ADM") { 
			$query .= " AND REG_ADM =  ".$search_str." AND INOUT_TYPE IN ('입금', '지급', '대체', '대입', '기타지급', '기타입금') ";
		}


		if($order_field  == "" && $order_str == "") 
			$query .= " ORDER BY INOUT_DATE ASC, REG_DATE ASC, ORDER_GOODS_NO ASC, CL_NO ASC ";
		else
		{

			if ($order_field == "") 
				$order_field = "INOUT_DATE";

			if ($order_str == "") 
				$order_str = "ASC";

			$query .= " ORDER BY ".$order_field." ".$order_str;

		}

		$query .= " limit 0, ".$nRowCount;

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

	function deleteCompanyLedger($db, $cl_no, $del_adm) { 

		//GROUP_NO, TO_CP_NO, INOUT_TYPE, UNIT_PRICE, INOUT_DATE, RGN_NO, GRGL_NO, REG_DATE

		$query="SELECT GROUP_NO, RGN_NO, GRGL_NO
				  FROM TBL_COMPANY_LEDGER 
				 WHERE CL_NO = '$cl_no'   ";
		
		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				//$TO_CP_NO		= $record[$i]["TO_CP_NO"];
				//$UNIT_PRICE		= $record[$i]["UNIT_PRICE"];
				//$INOUT_DATE		= $record[$i]["INOUT_DATE"];
				//$INOUT_TYPE		= $record[$i]["INOUT_TYPE"];
				//$REG_DATE		= $record[$i]["REG_DATE"];
				$RGN_NO			= $record[$i]["RGN_NO"];
				$GRGL_NO		= $record[$i]["GRGL_NO"];
				$GROUP_NO		= $record[$i]["GROUP_NO"];

				//대입이거나 대체일경우 같이 삭제되는 것으로
				if($GROUP_NO != null) { 
					$query = " UPDATE TBL_COMPANY_LEDGER
						  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
						WHERE GROUP_NO = $GROUP_NO AND CL_NO <> $cl_no  ";

					//echo $query."<br/>";
					//exit;

					mysql_query($query,$db);
				} 
				

			}
		}

		$query = " UPDATE TBL_COMPANY_LEDGER
					  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					WHERE CL_NO = '$cl_no' AND DEL_TF = 'N' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteCompanyLedgerByCode($db, $options, $del_adm) { 

		$query = " UPDATE TBL_COMPANY_LEDGER
					  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					WHERE DEL_TF = 'N' ";

		if ($options['ORDER_GOODS_NO'] <> "") {
			//발주가 아닌 매출에 대한 자료만 삭제
			$query .= " AND ORDER_GOODS_NO  = ".$options['ORDER_GOODS_NO'];
			$query .= " AND RGN_NO IS NULL ";
		}

		if ($options['CLAIM_ORDER_GOODS_NO'] <> "") {
			$query .= " AND CLAIM_ORDER_GOODS_NO  = ".$options['CLAIM_ORDER_GOODS_NO'];
		}
 
		if ($options['RGN_NO'] <> "") {
			$query .= " AND RGN_NO  = ".$options['RGN_NO'];
		}

		if ($options['GRGL_NO'] <> "") {
			$query .= " AND GRGL_NO  = ".$options['GRGL_NO'];
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

	function selectCompanyLedger($db, $cl_no) { 

		$query = " SELECT CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, CATE_01, TAX_TF, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO
					  FROM TBL_COMPANY_LEDGER
					 WHERE CL_NO = $cl_no ";

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

	function updateCompanyLedger($db, $cp_no, $inout_date, $inout_type, $name, $qty, $unit_price, $memo, $reserve_no, $order_goods_no, $rgn_no, $cl_no) { 

		//echo $cl_no."<br/>";

		$inout_type_name = getDcodeName($db, "COMPANY_LEDGER_TYPE", $inout_type);

		if(startsWith($inout_type, "L"))
			$deposit = $qty * $unit_price;
		else if(startsWith($inout_type, "R"))
			$withdraw = $qty * $unit_price;


		$query = " UPDATE TBL_COMPANY_LEDGER
					  SET CP_NO = '$cp_no', 
					      INOUT_DATE = '$inout_date', 
						  INOUT_TYPE = '$inout_type_name', 
						  NAME = '$name', 
						  QTY = '$qty', 
						  UNIT_PRICE = '$unit_price', 
						  WITHDRAW = '$withdraw', 
						  DEPOSIT = '$deposit', 
						  MEMO = '$memo', 
						  RESERVE_NO = '$reserve_no',
						  ORDER_GOODS_NO = '$order_goods_no',
						  RGN_NO = '$rgn_no'
					WHERE CL_NO = '$cl_no' ";

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


	// 주문 판매업체 변경시
	function updateCompanyLedgerCompanyNoByReserveNo($db, $cp_no, $reserve_no) { 

		$query = " UPDATE TBL_COMPANY_LEDGER
					  SET CP_NO = '$cp_no'
					WHERE RESERVE_NO = '$reserve_no' AND (RGN_NO IS NULL OR RGN_NO = 0) ";

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

	// 주문 판매업체 변경시
	function updateCompanyLedgerByRGNNo($db, $qty, $unit_price, $rgn_no) { 

		$withdraw = $qty * $unit_price;
		$query = " UPDATE TBL_COMPANY_LEDGER
					  SET QTY = '$qty', UNIT_PRICE = '$unit_price', WITHDRAW = '$withdraw'
					WHERE RGN_NO = '$rgn_no' AND (INPUT_TYPE = '발주수령' OR INPUT_TYPE = '발주직송') ";

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

	function updateCompanyLedgerByOrderSub($db, $input_type, $order_goods_no, $changed_price, $reg_adm) { 

		//기장 되어있는지 체크
		$query = "SELECT COUNT(*)
					FROM TBL_COMPANY_LEDGER
				   WHERE INPUT_TYPE = '$input_type' AND ORDER_GOODS_NO = '$order_goods_no' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		if($rows[0] > 0) {

			$query="SELECT CATE_01, TAX_TF
				  FROM TBL_ORDER_GOODS 
				 WHERE ORDER_GOODS_NO = '$order_goods_no' ";

			//echo $query;

			$CATE_01		= "";
			$TAX_TF			= "";
			$result2 = mysql_query($query,$db);
			$record = array();
			if ($result2 <> "") {
				for($i=0;$i < mysql_num_rows($result2);$i++) {
					$record[$i] = sql_result_array($result2,$i);

					$CATE_01		= $record[$i]["CATE_01"];
					$TAX_TF			= $record[$i]["TAX_TF"];
				}
			}

			//기장 되어있다면 업데이트
			$query = " UPDATE TBL_COMPANY_LEDGER
							  SET 
								  UNIT_PRICE = $changed_price, 
								  DEPOSIT = QTY * $changed_price,
								  CATE_01 = '$CATE_01',
								  TAX_TF = '$TAX_TF',
								  UP_DATE = now(),
								  UP_ADM = $reg_adm
							WHERE INPUT_TYPE = '$input_type' AND ORDER_GOODS_NO = '$order_goods_no' ";

			//echo $query;
			//exit;

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}

		} else {
			// 기장 안되어있다면 정보 가져와서 새로 기장

			$query="SELECT O.CP_NO, OG.GOODS_NAME, O.RESERVE_NO, OG.REG_DATE, OG.SALE_CONFIRM_TF, OG.CATE_01, OG.TAX_TF, OG.QTY
				  FROM TBL_ORDER O
				  JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO 
				 WHERE OG.ORDER_GOODS_NO = '$order_goods_no' ";

			//echo $query;

			$inout_type = "LR01";
			$inout_date = date("Y-m-d", strtotime("0 month"));

			$TEMP_MEMO = "";
			$arr_memo = getMemoFromOrderGoods($db, $order_goods_no);
			if(sizeof($arr_memo) > 0) { 
				$A = $arr_memo[0]["A"];
				//$B = $arr_memo[0]["B"];
				$C = $arr_memo[0]["C"];
				$D = $arr_memo[0]["D"];

				$TEMP_MEMO .= $A;
				//$TEMP_MEMO .= ($B != "" ? ($TEMP_MEMO != "" ? "/".$B : $B) : "");
				$TEMP_MEMO .= ($C != "" ? ($TEMP_MEMO != "" ? "/".$C : $C) : "");
				$TEMP_MEMO .= ($D != "" ? ($TEMP_MEMO != "" ? "/".$D : $D) : "");

			}
		
			//echo $query."<br/>";
			$result = mysql_query($query,$db);
			$record = array();

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);

					$CP_NO			= $record[$i]["CP_NO"];
					$GOODS_NO		= $record[$i]["GOODS_NO"];
					$GOODS_NAME		= $record[$i]["GOODS_NAME"];
					$RESERVE_NO		= $record[$i]["RESERVE_NO"];
					$REG_DATE		= $record[$i]["REG_DATE"];
					$SALE_CONFIRM_TF= $record[$i]["SALE_CONFIRM_TF"];
					$CATE_01		= $record[$i]["CATE_01"];
					$TAX_TF			= $record[$i]["TAX_TF"];
					$QTY			= $record[$i]["QTY"];

					//클레임 취소수량을 따로 기장하기 때문에 운용하는 숫자만 가져오면 안됨
					//$refund_able_qty = getRefundAbleQty($db, $RESERVE_NO, $order_goods_no);


					if($input_type == "매출할인" || $input_type == "추가배송비") { 

						$GOODS_NO = 0;
						$GOODS_NAME = $input_type;
						$QTY = 1;

					}

					//매출기장확정 안되있으면 패스 - 매출상품 신규기장은 애초에 여기에 올수 없음
					if($SALE_CONFIRM_TF == "N") continue;

					//기장기준일 추가 2017-06-02
					$base_date = getDcodeExtByCode($db, "LEDGER_SETUP", "BASE_DATE");
					if($base_date >= $REG_DATE) continue;
					

					//echo "base_date : ".$base_date.", REG_DATE : ".$REG_DATE;

					if($QTY == 0 || $changed_price == 0) continue;

					return insertCompanyLedger($db, $CP_NO, $inout_date, $inout_type, $GOODS_NO, $GOODS_NAME, $QTY, $changed_price,  null, 0, $CATE_01, $TAX_TF, $TEMP_MEMO, $RESERVE_NO, $order_goods_no, $input_type, null, $reg_adm, null);

				}
			}

		}
	}

	function updateCompanyLedgerByOrderSubClaim($db, $order_goods_no, $changed_price, $reg_adm) { 

		//취소나 교환을 위한 역 금액
		//금액대신 수량을 -처리 2018-07-12 
		//$claimed_price = $changed_price * -1;

		//기장 되어있는지 체크
		$query = "SELECT IFNULL(COUNT(*), 0)
					FROM TBL_COMPANY_LEDGER
				   WHERE CLAIM_ORDER_GOODS_NO > 0 AND ORDER_GOODS_NO = '$order_goods_no' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		if($rows[0] > 0) {

			$query="SELECT ORDER_GOODS_NO AS CLAIM_ORDER_GOODS_NO, CATE_01, TAX_TF
				  FROM TBL_ORDER_GOODS 
				 WHERE CLAIM_ORDER_GOODS_NO = '$order_goods_no' ";

			//echo $query;

			$CATE_01		= "";
			$TAX_TF			= "";
			$result2 = mysql_query($query,$db);
			$record = array();
			if ($result2 <> "") {
				for($i=0;$i < mysql_num_rows($result2);$i++) {
					$record[$i] = sql_result_array($result2,$i);

					$CATE_01				= $record[$i]["CATE_01"];
					$TAX_TF					= $record[$i]["TAX_TF"];
					$CLAIM_ORDER_GOODS_NO	= $record[$i]["CLAIM_ORDER_GOODS_NO"];
				}
			}

			

			//기장 되어있다면 업데이트
			$query = " UPDATE TBL_COMPANY_LEDGER
							  SET 
								  UNIT_PRICE = $changed_price, 
								  DEPOSIT = QTY * $changed_price,
								  CATE_01 = '$CATE_01',
								  TAX_TF = '$TAX_TF',
								  UP_DATE = now(),
								  UP_ADM = $reg_adm
							WHERE CLAIM_ORDER_GOODS_NO = '$CLAIM_ORDER_GOODS_NO' ";

			//echo $query;
			//exit;

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}

		} else {
			// 기장 안되어있다면 정보 가져와서 새로 기장

			$query="SELECT O.CP_NO, OG.GOODS_NO, OG.GOODS_CODE, OG.GOODS_NAME, O.RESERVE_NO, OG.REG_DATE, OG.SALE_CONFIRM_TF, OG.CATE_01, OG.TAX_TF, OG.QTY, OG.ORDER_STATE, OG.ORDER_GOODS_NO AS CLAIM_ORDER_GOODS_NO
				  FROM TBL_ORDER O
				  JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO 
				 WHERE OG.CLAIM_ORDER_GOODS_NO =  '$order_goods_no' AND OG.USE_TF = 'Y' AND OG.DEL_TF = 'N'
			  ORDER BY OG.ORDER_GOODS_NO DESC ";

			//echo $query;

			$inout_type = "LR01";
			$inout_date = date("Y-m-d", strtotime("0 month"));
		
			//echo $query."<br/>";
			$result = mysql_query($query,$db);
			$record = array();

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);

					$CP_NO					= $record[$i]["CP_NO"];
					$GOODS_NO				= $record[$i]["GOODS_NO"];
					$GOODS_CODE				= $record[$i]["GOODS_CODE"];
					$GOODS_NAME				= $record[$i]["GOODS_NAME"];
					$RESERVE_NO				= $record[$i]["RESERVE_NO"];
					$REG_DATE				= $record[$i]["REG_DATE"];
					$SALE_CONFIRM_TF		= $record[$i]["SALE_CONFIRM_TF"];
					$CATE_01				= $record[$i]["CATE_01"];
					$TAX_TF					= $record[$i]["TAX_TF"];
					$QTY					= $record[$i]["QTY"];
					$ORDER_STATE			= $record[$i]["ORDER_STATE"];
					$CLAIM_ORDER_GOODS_NO	= $record[$i]["CLAIM_ORDER_GOODS_NO"];

					//기장기준일 추가 2017-06-02
					$base_date = getDcodeExtByCode($db, "LEDGER_SETUP", "BASE_DATE");
					if($base_date >= $REG_DATE) continue;

					//echo "base_date : ".$base_date.", REG_DATE : ".$REG_DATE;

					if($QTY == 0 || $changed_price == 0) continue;

					$claim_state_name = getDcodeName($db, "ORDER_STATE", $ORDER_STATE);

					$TEMP_MEMO = $claim_state_name."(클레임:".$CLAIM_ORDER_GOODS_NO.")";
					$options = array('CLAIM_ORDER_GOODS_NO' => $CLAIM_ORDER_GOODS_NO);

					if($ORDER_STATE >= 4)
						$QTY = -1 * $QTY;

					return insertCompanyLedger($db, $CP_NO, $inout_date, $inout_type, $GOODS_NO, $GOODS_NAME."[".$GOODS_CODE."]", $QTY, $changed_price, null, 0, $CATE_01, $TAX_TF, $TEMP_MEMO, $RESERVE_NO, $order_goods_no, "클레임 ".$claim_state_name, null, $reg_adm, $options);

				}
			}

		}
	}

	function SumCompanyLedger($db, $cp_no) {

		if($cp_no == "") return;

		$query = "SELECT IFNULL(ROUND(SUM(WITHDRAW)), 0) AS SUM_WITHDRAW, 
						 IFNULL(ROUND(SUM(DEPOSIT)), 0) AS SUM_DEPOSIT, 
						 IFNULL(ROUND(SUM(DEPOSIT) - SUM(WITHDRAW)), 0) AS SUM_BALANCE
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND CP_NO = '".$cp_no."' ";

		//echo $query;

		$result = mysql_query($query,$db);

		return $result;
	}


	//발주 추가비용 
	function selectRequestGoodsSubLedger($db, $rgn_no) {

		$query = "SELECT GRGL_NO, NAME, QTY, UNIT_PRICE, MEMO, CONFIRM_TF 
					FROM TBL_GOODS_REQUEST_GOODS_LEDGER 
				   WHERE REQ_GOODS_NO = '$rgn_no' AND DEL_TF = 'N' ";

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

	function InsertRequestGoodsSubLedger($db, $rgn_no, $name, $qty, $price, $memo, $reg_adm) { 

		$query = " INSERT INTO TBL_GOODS_REQUEST_GOODS_LEDGER (REQ_GOODS_NO, NAME, QTY, UNIT_PRICE, MEMO, REG_ADM, REG_DATE)
					  VALUES ('$rgn_no', '$name', '$qty', '$price', '$memo', '$reg_adm', now()); ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteRequestGoodsSubLedger($db, $rgnl_no, $del_adm) { 

		$query = " UPDATE TBL_GOODS_REQUEST_GOODS_LEDGER 
					  SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					WHERE GRGL_NO = '$rgnl_no' ; ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}



	function totalCntAccountReceivable($db, $start_date, $end_date, $filter, $search_field, $search_str) {

		$query = "
		
					SELECT COUNT(*)
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, AD_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) D ON O.CP_NO = D.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) D2 ON O.CP_NO = D2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_Y
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '과세'
							GROUP BY CP_NO 
						) D3 ON O.CP_NO = D3.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_N
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '비과세'
							GROUP BY CP_NO 
						) D4 ON O.CP_NO = D4.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급') AND (A.PREV_BALANCE <> 0 || B.SUM_SALES <> 0 || C.SUM_COLLECT <> 0 || D.SUM_BUYING <> 0 ||  C.SUM_PAID <> 0 ||  E.SUM_BALANCE <> 0)
		";

		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE = '".$filter['con_cp_type']."' ";
		}

		if ($filter['con_ad_type'] <> "") {
			$query .= " AND O.AD_TYPE = '".$filter['con_ad_type']."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.CP_CODE = '".$search_str."' OR O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
			} else {

				if ($search_field == "CP_CODE") {
					$query .= " AND (O.CP_CODE = '".$search_str."')";
				} else if ($search_field == "CP_NAME") {
					$query .= " AND (O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		//echo $query."<br/><br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listAccountReceivable($db, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntAccountReceivable($db, $start_date, $end_date, $filter, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "
					SELECT O.CP_NO, O.CP_TYPE, O.CP_CODE, O.CP_NM, O.CP_NM2, 
							IFNULL(A.PREV_BALANCE, 0) AS PREV_BALANCE, 
							IFNULL(B.SUM_SALES, 0) AS SUM_SALES, 
							IFNULL(B2.SUM_SALES_TAX, 0) AS SUM_SALES_TAX, 
							IFNULL(C.SUM_COLLECT, 0) AS SUM_COLLECT, 
							IFNULL(D.SUM_BUYING, 0) AS SUM_BUYING, 
							IFNULL(D2.SUM_BUYING_TAX, 0) AS SUM_BUYING_TAX, 
							IFNULL(D3.SUM_BUYING_TAX_Y, 0) AS SUM_BUYING_TAX_Y, 
							IFNULL(D4.SUM_BUYING_TAX_N, 0) AS SUM_BUYING_TAX_N, 
							IFNULL(C.SUM_PAID, 0) AS SUM_PAID, 
							IFNULL(E.SUM_BALANCE, 0) AS SUM_BALANCE, 
							O.SALE_ADM_NO
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, AD_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) D ON O.CP_NO = D.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) D2 ON O.CP_NO = D2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_Y
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '과세'
							GROUP BY CP_NO 
						) D3 ON O.CP_NO = D3.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_N
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '비과세'
							GROUP BY CP_NO 
						) D4 ON O.CP_NO = D4.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급') AND (A.PREV_BALANCE <> 0 || B.SUM_SALES <> 0 || C.SUM_COLLECT <> 0 || D.SUM_BUYING <> 0 ||  C.SUM_PAID <> 0 ||  E.SUM_BALANCE <> 0)
		";


		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE = '".$filter['con_cp_type']."' ";
		}

		if ($filter['con_ad_type'] <> "") {
			$query .= " AND O.AD_TYPE = '".$filter['con_ad_type']."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.CP_CODE = '".$search_str."' OR O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
			} else {

				if ($search_field == "CP_CODE") {
					$query .= " AND (O.CP_CODE = '".$search_str."')";
				} else if ($search_field == "CP_NAME") {
					$query .= " AND (O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		if ($order_field == "") 
			$order_field = "E.SUM_BALANCE";

		if ($order_str == "") 
			$order_str = "ASC";

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

	//매출보고용
	function listAccountReceivableSaleReport($db, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {
		if($filter["chk_prev_month"] == "Y")
			$order_field = "SUM_BALANCE";
		
		$query = "
					SELECT O.CP_NO, O.CP_TYPE, O.CP_CODE, O.CP_NM, O.CP_NM2, 
							IFNULL(A.PREV_BALANCE, 0) AS PREV_BALANCE, 
							IFNULL(B.SUM_SALES, 0) AS SUM_SALES, 
							IFNULL(B2.SUM_SALES_TAX, 0) AS SUM_SALES_TAX, 
							IFNULL(C.SUM_COLLECT, 0) AS SUM_COLLECT, 
							IFNULL(E.SUM_BALANCE, 0) AS SUM_BALANCE, 
							IFNULL(A.PREV_BALANCE, 0) - IFNULL(C.SUM_COLLECT, 0) AS EXCEPT_SALE,
							O.SALE_ADM_NO,
							Z.MEMO, Z.PREV_0, Z.PREV_1, Z.PREV_2, Z.PREV_3, Z.EXCEPT_TF, Z.UP_DATE
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, AD_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO, MEMO2
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, MEMO, PREV_0, PREV_1, PREV_2, PREV_3, EXCEPT_TF, UP_DATE
							FROM TBL_COMPANY_LEDGER_REPORT
						) Z ON O.CP_NO = Z.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급')
		";

		if ($filter['chk_prev_month'] == "Y") {
			$query .= " AND (IFNULL(E.SUM_BALANCE, 0) > 0) ";
		} else {
			$query .= " AND (IFNULL(A.PREV_BALANCE, 0) - IFNULL(C.SUM_COLLECT, 0) > 0) ";
		}

		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE IN (".$filter['con_cp_type'].") ";
		}

		if ($filter['con_ad_type'] <> "") {
			$query .= " AND O.AD_TYPE = '".$filter['con_ad_type']."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.CP_CODE = '".$search_str."' OR O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
			} else {

				if ($search_field == "CP_CODE") {
					$query .= " AND (O.CP_CODE = '".$search_str."')";
				} else if ($search_field == "CP_NAME") {
					$query .= " AND (O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}
		
		if ($order_field == "") 
			$order_field = "EXCEPT_SALE";

		if ($order_str == "") 
			$order_str = "DESC";

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

	

	function selectBaseLedgerByOrder($db, $cp_no, $reserve_no) { 

		$query = "  SELECT INOUT_DATE, REG_DATE, ORDER_GOODS_NO, CL_NO 
					  FROM TBL_COMPANY_LEDGER 
					 WHERE RESERVE_NO = '$reserve_no' AND 
						   USE_TF = 'Y' AND 
						   DEL_TF = 'N' AND 
						   (RGN_NO IS NULL OR RGN_NO = 0)
				  ORDER BY INOUT_DATE ASC, REG_DATE ASC, ORDER_GOODS_NO ASC, CL_NO ASC
					 LIMIT 0, 1 ";

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

	function getPrevBalance($db, $cp_no, $base_inout_date, $base_order_goods_no, $base_cl_no) { 

		$query ="SELECT IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
				   FROM TBL_COMPANY_LEDGER 
		    	  WHERE DEL_TF =  'N' AND 
						USE_TF = 'Y' AND 
						CP_NO = '$cp_no' AND
						INOUT_DATE <= '$base_inout_date' AND 
						ORDER_GOODS_NO <= '$base_order_goods_no' AND 
						CL_NO NOT IN ('$base_cl_no')
			   ORDER BY INOUT_DATE ASC, REG_DATE ASC, ORDER_GOODS_NO ASC, CL_NO ASC 
				";

		//REG_DATE <= '$base_reg_date' AND -- 주문 이후에 넣은 입출금이 누락됨 
						
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}


	function getSumCollect($db, $cp_no, $base_inout_date) { 

		$query ="SELECT IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT
				   FROM TBL_COMPANY_LEDGER 
				  WHERE DEL_TF =  'N' AND 
					    USE_TF = 'Y' AND 
					    INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND 
					    INOUT_DATE >= '$base_inout_date 00:00:00' AND 
					    INOUT_DATE <= '$base_inout_date 23:59:59' AND
					    CP_NO = '$cp_no' 
			   ORDER BY INOUT_DATE ASC, REG_DATE ASC, ORDER_GOODS_NO ASC, CL_NO ASC 
				";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function getBalance($db, $cp_no) {

		$query = "SELECT IFNULL(SUM(DEPOSIT) - SUM(WITHDRAW), 0) AS SUM_BALANCE
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND 
				         USE_TF = 'Y' AND 
						 CP_NO = '".$cp_no."' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function selectAccountReceivable($db, $cp_no, $start_date, $end_date, $filter, $search_field, $search_str) {

		$query = "
					SELECT O.CP_NO, O.CP_TYPE, O.CP_CODE, O.CP_NM, O.CP_NM2, 
							IFNULL(A.PREV_BALANCE, 0) AS PREV_BALANCE, 
							IFNULL(B.SUM_SALES, 0) AS SUM_SALES, 
							IFNULL(B2.SUM_SALES_TAX, 0) AS SUM_SALES_TAX, 
							IFNULL(C.SUM_COLLECT, 0) AS SUM_COLLECT, 
							IFNULL(D.SUM_BUYING, 0) AS SUM_BUYING, 
							IFNULL(D2.SUM_BUYING_TAX, 0) AS SUM_BUYING_TAX, 
							IFNULL(C.SUM_PAID, 0) AS SUM_PAID, 
							IFNULL(E.SUM_BALANCE, 0) AS SUM_BALANCE, 
							O.SALE_ADM_NO
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) D ON O.CP_NO = D.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) D2 ON O.CP_NO = D2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급') AND (A.PREV_BALANCE <> 0 || B.SUM_SALES <> 0 || C.SUM_COLLECT <> 0 || D.SUM_BUYING <> 0 ||  C.SUM_PAID <> 0 ||  E.SUM_BALANCE <> 0)
					AND O.CP_NO = '$cp_no'
		";

		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE = '".$filter['con_cp_type']."' ";
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

	function SumAccountReceivable($db, $start_date, $end_date, $filter) {

		$query = "
					SELECT O.SALE_ADM_NO, 
							IFNULL(SUM(A.PREV_BALANCE), 0) AS TOTAL_PREV_BALANCE, 
							IFNULL(SUM(B.SUM_SALES), 0) AS TOTAL_SUM_SALES, 
							IFNULL(SUM(B2.SUM_SALES_TAX), 0) AS TOTAL_SUM_SALES_TAX, 
							IFNULL(SUM(C.SUM_COLLECT), 0) AS TOTAL_SUM_COLLECT, 
							IFNULL(SUM(D.SUM_BUYING), 0) AS TOTAL_SUM_BUYING, 
							IFNULL(SUM(D2.SUM_BUYING_TAX), 0) AS TOTAL_SUM_BUYING_TAX, 
							IFNULL(SUM(D3.SUM_BUYING_TAX_Y), 0) AS TOTAL_SUM_BUYING_TAX_Y, 
							IFNULL(SUM(D4.SUM_BUYING_TAX_N), 0) AS TOTAL_SUM_BUYING_TAX_N, 
							IFNULL(SUM(C.SUM_PAID), 0) AS TOTAL_SUM_PAID, 
							IFNULL(SUM(E.SUM_BALANCE), 0) AS TOTAL_SUM_BALANCE
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, AD_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) D ON O.CP_NO = D.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) D2 ON O.CP_NO = D2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_Y
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '과세'
							GROUP BY CP_NO 
						) D3 ON O.CP_NO = D3.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_BUYING_TAX_N
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매입' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND TAX_CONFIRM_TF = 'Y' AND TAX_TF = '비과세'
							GROUP BY CP_NO 
						) D4 ON O.CP_NO = D4.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급') AND (A.PREV_BALANCE <> 0 || B.SUM_SALES <> 0 || C.SUM_COLLECT <> 0 || D.SUM_BUYING <> 0 ||  C.SUM_PAID <> 0 ||  E.SUM_BALANCE <> 0)
				";


		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE = '".$filter['con_cp_type']."' ";
		}

		if ($filter['con_ad_type'] <> "") {
			$query .= " AND O.AD_TYPE = '".$filter['con_ad_type']."' ";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.CP_CODE = '".$search_str."' OR O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
			} else {

				if ($search_field == "CP_CODE") {
					$query .= " AND (O.CP_CODE = '".$search_str."')";
				} else if ($search_field == "CP_NAME") {
					$query .= " AND (O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		$query .= "  	GROUP BY O.SALE_ADM_NO  ";

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

	//매출보고용
	function SumAccountReceivableSaleReport($db, $start_date, $end_date, $filter) {

		$query = "
					SELECT O.SALE_ADM_NO, 
							IFNULL(SUM(A.PREV_BALANCE), 0) AS TOTAL_PREV_BALANCE, 
							IFNULL(SUM(B.SUM_SALES), 0) AS TOTAL_SUM_SALES, 
							IFNULL(SUM(B2.SUM_SALES_TAX), 0) AS TOTAL_SUM_SALES_TAX, 
							IFNULL(SUM(C.SUM_COLLECT), 0) AS TOTAL_SUM_COLLECT, 
							IFNULL(SUM(E.SUM_BALANCE), 0) AS TOTAL_SUM_BALANCE,
							IFNULL(SUM(A.PREV_BALANCE), 0) - IFNULL(SUM(C.SUM_COLLECT), 0) AS TOTAL_EXCEPT_SALE,
							IFNULL(SUM(CASE WHEN Z.PREV_0 IS NULL || Z.PREV_0 = 0 THEN B.SUM_SALES ELSE Z.PREV_0 END), 0) AS TOTAL_SUM_PREV_0,
							IFNULL(SUM(Z.PREV_1), 0) AS TOTAL_SUM_PREV_1,
							IFNULL(SUM(Z.PREV_2), 0) AS TOTAL_SUM_PREV_2,
							IFNULL(SUM(Z.PREV_3), 0) AS TOTAL_SUM_PREV_3
					FROM 
					(
						(
							SELECT CP_NO, CP_TYPE, AD_TYPE, CP_CODE, CP_NM, CP_NM2, SALE_ADM_NO
							FROM TBL_COMPANY
							WHERE USE_TF = 'Y' AND DEL_TF = 'N'
						) O 
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							GROUP BY CP_NO 
						) A ON O.CP_NO = A.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가'
							GROUP BY CP_NO 
						) B ON O.CP_NO = B.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ), 0) AS SUM_SALES_TAX
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE = '매출' AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59' AND CATE_01 <> '추가' AND TAX_CONFIRM_TF = 'Y'
							GROUP BY CP_NO 
						) B2 ON O.CP_NO = B2.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT, IFNULL(SUM( DEPOSIT ), 0) AS SUM_PAID
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
						LEFT JOIN 
						(
							SELECT CP_NO, MEMO, PREV_0, PREV_1, PREV_2, PREV_3, EXCEPT_TF
							FROM TBL_COMPANY_LEDGER_REPORT
						) Z ON O.CP_NO = Z.CP_NO
					) 
					WHERE O.CP_TYPE IN ('판매', '구매', '판매공급') AND (Z.EXCEPT_TF IS NULL OR Z.EXCEPT_TF <> 'Y')
				";


		if ($filter['chk_prev_month'] == "Y") {
			$query .= " AND (IFNULL(E.SUM_BALANCE, 0) > 0) ";
		} else {
			$query .= " AND (IFNULL(A.PREV_BALANCE, 0) - IFNULL(C.SUM_COLLECT, 0) > 0) ";
		}


		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.SALE_ADM_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if ($filter['con_cp_type'] <> "") {
			$query .= " AND O.CP_TYPE IN (".$filter['con_cp_type'].") ";
		}

		if ($filter['con_ad_type'] <> "") {
			$query .= " AND O.AD_TYPE = '".$filter['con_ad_type']."' ";
		}

		/*
		if ($filter['except_cp_no'] <> "") {
			$query .= " AND O.CP_NO NOT IN ('2644')";// '".$filter['con_ad_type']."' ";
		}
		*/

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (O.CP_CODE = '".$search_str."' OR O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
			} else {

				if ($search_field == "CP_CODE") {
					$query .= " AND (O.CP_CODE = '".$search_str."')";
				} else if ($search_field == "CP_NAME") {
					$query .= " AND (O.CP_NM like '%".$search_str."%' OR O.CP_NM2 like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		$query .= "  	GROUP BY O.SALE_ADM_NO  ";

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

	//대체, 대입을 위한 그룹화 번호 (내부사용)
	function maxCompanyLedgerGroupNo($db) {

		$query ="SELECT IFNULL(MAX(GROUP_NO),0) + 1
		           FROM TBL_COMPANY_LEDGER ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	//세금계산서 발행/취소
	function updateTaxInvoiceTF($db, $cl_no, $cf_type, $cf_code, $tax_confirm_tf, $tax_confirm_adm) { 

		$cf_code = trim($cf_code);

		$query="SELECT TAX_CONFIRM_TF
				  FROM TBL_COMPANY_LEDGER 
				 WHERE CL_NO = '$cl_no' AND DEL_TF = 'N' AND USE_TF = 'Y'   ";
		
		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$TAX_CONFIRM_TF			= $record[$i]["TAX_CONFIRM_TF"];

				if($tax_confirm_tf == "Y") { 

					//발행처리/취소가 기존 정보와 다를때만 수정
					if($tax_confirm_tf != $TAX_CONFIRM_TF) { 
						$query = " 
							   UPDATE TBL_COMPANY_LEDGER
								  SET TAX_CONFIRM_TF = 'Y', TAX_CONFIRM_ADM = '$tax_confirm_adm', TAX_CONFIRM_DATE = now()
								WHERE CL_NO = '$cl_no' AND DEL_TF = 'N' AND USE_TF = 'Y'
							 ";
						//echo $query."<br/>";
						//exit;

						mysql_query($query,$db);
					}

					if($cf_code <> "") { 
					
						$query = " INSERT INTO TBL_COMPANY_LEDGER_CONFIRM_CODE (CL_NO, CF_TYPE, CF_CODE) 
										VALUES ('$cl_no', '$cf_type', '$cf_code' )";
						
						//echo $query."<br/>";
						//exit;

						mysql_query($query,$db);

						
						$query = " UPDATE TBL_CASH_FLOW_STATEMENT 
									  SET MATCH_TF = 'Y'
									WHERE CF_CODE = '$cf_code'";
						
						//echo $query."<br/>";
						//exit;

						mysql_query($query,$db);

					}

				} else { 

					//발행처리/취소가 기존 정보와 다를때만 수정
					if($tax_confirm_tf != $TAX_CONFIRM_TF) { 
						$query = " 
							   UPDATE TBL_COMPANY_LEDGER
								  SET TAX_CONFIRM_TF = 'N', TAX_CONFIRM_ADM = NULL, TAX_CONFIRM_DATE = NULL
								WHERE CL_NO = '$cl_no' AND DEL_TF = 'N' AND USE_TF = 'Y'
							 ";

						//echo $query."<br/>";
						//exit;

						mysql_query($query,$db);
					}

												
					$query = " UPDATE TBL_CASH_FLOW_STATEMENT AS CF 
								 JOIN TBL_COMPANY_LEDGER_CONFIRM_CODE CL ON CF.CF_CODE = CL.CF_CODE
								  SET MATCH_TF = 'N'
								WHERE CL.CL_NO = '$cl_no' AND CF.DEL_TF = 'N' ";
					
					//echo $query."<br/>";
					//exit;

					mysql_query($query,$db);
					
					$query = " DELETE FROM TBL_COMPANY_LEDGER_CONFIRM_CODE
									 WHERE CL_NO = '$cl_no' ";
					
					//echo $query."<br/>";
					//exit;

					mysql_query($query,$db);
				}
				
			}
		}
		
		return true;
	}

	//기장 일자 변경
	function updateCompanyLedgerInoutDate($db, $cl_no, $new_inout_date) { 

		// $query = " UPDATE TBL_COMPANY_LEDGER
		// 			  SET INOUT_DATE = '$new_inout_date' 
		// 			WHERE CL_NO = '$cl_no' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
					
		// 기장일자변경 시 기존 데이터 값도 같이 변경 되도록 기존거 주석 및 20210326 수정

		$preQuery="	SELECT 	GROUP_NO
					FROM	TBL_COMPANY_LEDGER
					WHERE	CL_NO = '$cl_no'
					AND		USE_TF='Y'
					AND		DEL_TF='N'
					";
		
		$result=mysql_query($preQuery, $db);
		$rows="";

		if(!$result){
			echo "<script>alert('SELECT ERRROR());</script>";
			exit;
		}
		else{
			$rows=mysql_fetch_row($result);
		}

		if($_SESSION['s_adm_no']!="58" || $rows[0]==""){
			$query = " UPDATE 	TBL_COMPANY_LEDGER
						SET 	INOUT_DATE = '$new_inout_date' 
						WHERE 	CL_NO = '$cl_no' 
						AND 	USE_TF = 'Y' 
						AND 	DEL_TF = 'N' 
						";
			if(!mysql_query($query, $db)){
				echo "<script>alert('UPDATE ERRROR(1));</script>";
				exit;
			}

		}
		else{
			$query = " UPDATE 	TBL_COMPANY_LEDGER 
						SET 	INOUT_DATE = '$new_inout_date' 
						WHERE 	GROUP_NO IN( 
								SELECT * FROM 
									(
										SELECT GROUP_NO 
										FROM TBL_COMPANY_LEDGER 
										WHERE CL_NO = '$cl_no' 
										AND USE_TF = 'Y' 
										AND DEL_TF = 'N' 
									) AS A
								)					
			";
			if(!mysql_query($query, $db)){
				echo "<script>alert('UPDATE ERRROR(2));</script>";
				exit;
			}
		}

	}
	
	function insertCompanyLedgerBalance($db, $cp_no, $inout_date, $balance, $memo, $reg_adm) {

		//잔액을 가져오고 비교해서 최종 잔액으로 수정 같으면 패스

		$query ="SELECT IFNULL(SUM(DEPOSIT) - SUM(WITHDRAW), 0) 
		           FROM TBL_COMPANY_LEDGER 
				  WHERE CP_NO = '$cp_no' AND DEL_TF = 'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$inout_date 23:59:59' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$prev_balance  = $rows[0];

		$diff = $balance - $prev_balance;

		//echo "prev_balance:".$prev_balance.", balance:".$balance.", diff:".$diff;
		//exit;

		if($diff > 0) { 

			$query="INSERT INTO TBL_COMPANY_LEDGER 
								(GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, REG_ADM, REG_DATE)
					 VALUES (NULL, '$cp_no', NULL, '$inout_date', '차변실사', NULL, '잔액일괄조정', '1', '$diff', 0, '$diff', 0, '$memo', '', NULL, '수기실사', '$reg_adm', now()); ";
			
			//echo $query."<br/>";
			//exit;
			return mysql_query($query,$db);


		} else if($diff < 0) {
			
			$withdraw = -1 * $diff;

			$query="INSERT INTO TBL_COMPANY_LEDGER 
								(GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, INPUT_TYPE, REG_ADM, REG_DATE)
					 VALUES (NULL, '$cp_no', NULL, '$inout_date', '대변실사', NULL, '잔액일괄조정', '1', '$diff', '$withdraw', 0, 0, '$memo', '', NULL, '수기실사', '$reg_adm', now()); ";
			
			//echo $query."<br/>";
			//exit;
			return mysql_query($query,$db);

		} else 
			return false;

	}


	function listLedger($db, $search_date_type, $start_date, $end_date, $cp_no,  $reg_adm, $filter, $order_field = "", $order_str = "", $search_field = "", $search_str = "", $nPage = 1, $nRowCount = 20, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, CL_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, CATE_01, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, TAX_CONFIRM_TF, TAX_CONFIRM_DATE, REG_DATE, REG_ADM
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if($search_date_type == "ledger_date") { 
			if ($start_date <> "") {
				$query .= " AND INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND INOUT_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($cp_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND REG_ADM = '".$reg_adm."' ";
		}

		if ($filter['is_different_date'] == "Y") {
			$query .= " AND DATE_FORMAT(REG_DATE,'%Y-%m-%d') <> INOUT_DATE ";
		}

		if ($filter['inout_type'] != "") {
			$query .= " AND INOUT_TYPE = '".$filter['inout_type']."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (RESERVE_NO = '".$search_str."' OR NAME LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%' ) ";
			
			} else {

				if ($search_field == "RESERVE_NO") {
					$query .= " AND RESERVE_NO = '".$search_str."' "; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}


		if ($order_field == "") 
			$order_field = "INOUT_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str;


		$query .= "  limit ".$offset.", ".$nRowCount;

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

	function totalCntLedger($db, $search_date_type, $start_date, $end_date, $cp_no,  $reg_adm, $filter, $search_field = "", $search_str = "", $nRowCount = 10000) {

		$query ="SELECT COUNT(*) CNT 
				   FROM TBL_COMPANY_LEDGER
				  WHERE DEL_TF = 'N' AND USE_TF = 'Y' ";

		if($search_date_type == "ledger_date") { 
			if ($start_date <> "") {
				$query .= " AND INOUT_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND INOUT_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($cp_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND REG_ADM = '".$reg_adm."' ";
		}

		if ($filter['is_different_date'] == "Y") {
			$query .= " AND DATE_FORMAT(REG_DATE,'%Y-%m-%d') <> INOUT_DATE ";
		}

		if ($filter['inout_type'] != "") {
			$query .= " AND INOUT_TYPE = '".$filter['inout_type']."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (RESERVE_NO = '".$search_str."' OR NAME LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%' ) ";
			
			} else {

				if ($search_field == "RESERVE_NO") {
					$query .= " AND RESERVE_NO = '".$search_str."' "; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function listUndeliveredOrderGoods($db, $search_date_type, $start_date, $end_date, $cp_no, $filter, $search_field, $search_str, $order_field, $order_str) {
		
		$query = " SELECT O.RESERVE_NO, C.CP_CODE, CONCAT( C.CP_NM,  ' ', CP_NM2 ) AS CP_NAME, 
						  OG.GOODS_CODE, OG.GOODS_NAME, OG.SALE_PRICE, OG.QTY, OG.DELIVERY_TYPE,
						  K.REFUNDABLE_QTY AS REFUNDABLE_QTY, IFNULL(OGI.SUB_SUM, 0) AS SUM_SUB_QTY, A.ADM_NAME
					 FROM TBL_ORDER O
					 JOIN TBL_COMPANY C ON O.CP_NO = C.CP_NO
					 JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
					 JOIN TBL_ADMIN_INFO A ON O.OPT_MANAGER_NO = A.ADM_NO
			    LEFT JOIN (

							SELECT GROUP_NO, SUM( 
												CASE WHEN ORDER_STATE =1 || ORDER_STATE =2 THEN QTY
												ELSE -1 * QTY
											 END) AS REFUNDABLE_QTY
							  FROM TBL_ORDER_GOODS
							 WHERE USE_TF =  'Y'
							   AND DEL_TF =  'N'
							   AND ORDER_STATE <>3
							 GROUP BY GROUP_NO
						  ) K ON OG.ORDER_GOODS_NO = K.GROUP_NO
				LEFT JOIN (
							SELECT ORDER_GOODS_NO, SUM(SUB_QTY) AS SUB_SUM
							  FROM TBL_ORDER_GOODS_INDIVIDUAL 
							 WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND IS_DELIVERED = 'Y'
						  GROUP BY ORDER_GOODS_NO
						  ) OGI ON OGI.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND OG.DELIVERY_TYPE IN ('3', '98')

	                WHERE OG.USE_TF =  'Y'
					  AND OG.DEL_TF =  'N'
					  AND (K.REFUNDABLE_QTY != 0 OR K.REFUNDABLE_QTY IS NULL)
					  ";

		if($search_date_type == "order_date") { 
			if ($start_date <> "") {
				$query .= " AND O.ORDER_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND O.ORDER_DATE <= '".$end_date." 23:59:59' ";
			}
	
		} else if ($search_date_type == "reg_date") { 
			if ($start_date <> "") {
				$query .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$query .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}		
		} 

		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		}

		if ($filter['con_sale_adm_no'] <> "") {
			$query .= " AND O.OPT_MANAGER_NO = '".$filter['con_sale_adm_no']."' ";
		}

		if($filter['order_state1'] <> "" && $filter['order_state2'] <> "")
			$query .= " AND OG.ORDER_STATE IN ('1', '2') ";
		else if($filter['order_state1'] <> "" && $filter['order_state2'] == "")
			$query .= " AND OG.ORDER_STATE IN ('1') ";
		else  if($filter['order_state1'] == "" && $filter['order_state2'] <> "")
			$query .= " AND OG.ORDER_STATE IN ('2') ";
		else
			$query .= " AND OG.ORDER_STATE IN ('1', '2') ";

		if($filter['delivery_type'] <> "") { 
		
			$delivery_type = $filter['delivery_type'];
			if(sizeof($delivery_type) > 0) { 
				$sub_query = "AND (";
				foreach($delivery_type as $each) { 

					$sub_query .= " OG.DELIVERY_TYPE = '$each' OR ";
				}
				$sub_query = rtrim($sub_query, " OR ");
				$sub_query .= ")";
				$query .= $sub_query;
			}
		}

		//추가가 포함이 아니면
		if ($filter['cate_01'] != "Y") {
			$query .= " AND OG.CATE_01 != '추가' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (OG.RESERVE_NO = '".$search_str."' OR OG.GOODS_NAME like '%".$search_str."%' OR A.ADM_NAME LIKE '%".$search_str."%')  ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "	A.ADM_NAME, OG.RESERVE_NO ";

		if ($order_str == "") 
			$order_str = "ASC";

		$query .= " ORDER BY ".$order_field." ".$order_str." ";

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


	function getUndeliveredOrderGoods($db, $cp_no) {
		
		$query = " 
				SELECT SUM(EACH_SUM) AS TOTAL_UNDELIVERED_PRICE
				FROM (
				   SELECT OG.SALE_PRICE * (CASE WHEN K.REFUNDABLE_QTY IS NULL THEN OG.QTY ELSE K.REFUNDABLE_QTY END - IFNULL(OGI.SUB_SUM, 0)) AS EACH_SUM
					 FROM TBL_ORDER O
					 JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
			    LEFT JOIN (

							SELECT GROUP_NO, SUM( 
												CASE WHEN ORDER_STATE =1 || ORDER_STATE =2 THEN QTY
												ELSE -1 * QTY
											 END) AS REFUNDABLE_QTY
							  FROM TBL_ORDER_GOODS
							 WHERE USE_TF =  'Y'
							   AND DEL_TF =  'N'
							   AND ORDER_STATE <>3
							 GROUP BY GROUP_NO
						  ) K ON OG.ORDER_GOODS_NO = K.GROUP_NO
				LEFT JOIN (
							SELECT ORDER_GOODS_NO, SUM(SUB_QTY) AS SUB_SUM
							  FROM TBL_ORDER_GOODS_INDIVIDUAL 
							 WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND IS_DELIVERED = 'Y'
						  GROUP BY ORDER_GOODS_NO
						  ) OGI ON OGI.ORDER_GOODS_NO = OG.ORDER_GOODS_NO AND OG.DELIVERY_TYPE = '3'

	                WHERE OG.USE_TF =  'Y'
					  AND OG.DEL_TF =  'N'
					  AND (K.REFUNDABLE_QTY != 0 OR K.REFUNDABLE_QTY IS NULL)
					  AND OG.ORDER_STATE IN ( 1, 2 ) 

					  AND O.CP_NO = '".$cp_no."' 
				) A
					  
				";



		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	//주문에서 세금계산서 직접 변경
	function updateTaxConfirmByOrderGoodsNo($db, $order_goods_no, $tax_confirm_tf, $confirm_adm) { 

		if($tax_confirm_tf == 'Y')
			$query = " UPDATE TBL_COMPANY_LEDGER
						  SET TAX_CONFIRM_TF = 'Y', TAX_CONFIRM_ADM = $confirm_adm, TAX_CONFIRM_DATE = now()
						WHERE ORDER_GOODS_NO = '$order_goods_no' AND INOUT_TYPE = '매출' AND USE_TF = 'Y' AND DEL_TF = 'N' AND (RGN_NO IS NULL OR RGN_NO = 0)  ";
		else
			$query = " UPDATE TBL_COMPANY_LEDGER
						  SET TAX_CONFIRM_TF = 'N', TAX_CONFIRM_ADM = NULL, TAX_CONFIRM_DATE = NULL, UP_ADM = '$confirm_adm', UP_DATE = now()
						WHERE ORDER_GOODS_NO = '$order_goods_no' AND INOUT_TYPE = '매출' AND USE_TF = 'Y' AND DEL_TF = 'N' AND (RGN_NO IS NULL OR RGN_NO = 0)  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function selectCompanyLedgerMemo($db, $cl_no) { 

		$query = " 
				SELECT MEMO
				  FROM TBL_COMPANY_LEDGER
				 WHERE CL_NO = '$cl_no' ";

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


	function updateCompanyLedgerMemo($db, $memo, $cl_no) {
		$query = " UPDATE TBL_COMPANY_LEDGER
				      SET MEMO = '$memo'
				    WHERE CL_NO = '$cl_no'  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateCompanyLedgerExtraInfo($db, $cate_01, $tax_tf, $order_goods_no) {
		$query = " UPDATE TBL_COMPANY_LEDGER
				      SET CATE_01 = '$cate_01',
						  TAX_TF = '$tax_tf'
				    WHERE ORDER_GOODS_NO = '$order_goods_no'  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	
	/////////////////////////////////////////////////////////////////////////////
	/////////   자금총괄표
	/////////////////////////////////////////////////////////////////////////////
	function insertCashFlow($db, $params, $reg_adm) {

		$op_cp_no		= $params['OP_CP_NO'];
		$cf_inout		= $params['CF_INOUT'];
		$cf_type		= $params['CF_TYPE']; 
		$cf_code		= $params['CF_CODE'];
		$biz_no			= $params['BIZ_NO'];
		$cp_nm			= $params['CP_NM']; 
		$goods_nm		= $params['GOODS_NM'];
		$out_date		= $params['OUT_DATE']; 
		$written_date	= $params['WRITTEN_DATE'];
		$supply_price	= $params['SUPPLY_PRICE']; 
		$surtax			= $params['SURTAX'];
		$total_price	= $params['TOTAL_PRICE'];


		$query = " 
			INSERT INTO TBL_CASH_FLOW_STATEMENT
						(OP_CP_NO, CF_INOUT, CF_TYPE, CF_CODE, BIZ_NO, CP_NM, GOODS_NM, OUT_DATE, WRITTEN_DATE, SUPPLY_PRICE, SURTAX, TOTAL_PRICE, REG_ADM, REG_DATE) 
			VALUES      ('$op_cp_no', '$cf_inout', '$cf_type', '$cf_code', '$biz_no', '$cp_nm', '$goods_nm', '$out_date', '$written_date', '$supply_price', '$surtax', '$total_price', '$reg_adm', now())
				  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function totalCntCashFlow($db, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str) { 
		
		$op_cp_no = $filter["op_cp_no"];
		$account_cp_no = $filter["account_cp_no"];
		$sale_cp_no = $filter["sale_cp_no"];
		$sale_adm_no = $filter["sale_adm_no"];
		$cf_inout = $filter["cf_inout"];
		$cf_type = $filter["cf_type"];
		$has_in_cash = $filter["has_in_cash"];
		$match_tf = $filter["match_tf"];

		$query = " 
				SELECT COUNT(*)
				  FROM TBL_CASH_FLOW_STATEMENT
				 WHERE DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND ".$search_date_type." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$search_date_type." <= '".$end_date." 23:59:59' ";
		}

		if ($op_cp_no <> "") {
			$query .= " AND OP_CP_NO =  '".$op_cp_no."' ";
		}

		if ($account_cp_no <> "") {
			$query .= " AND ACCOUNT_CP_NO =  '".$account_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND SALE_CP_NO =  '".$sale_cp_no."' ";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND SALE_ADM_NO =  '".$sale_adm_no."' ";
		}

		if ($cf_inout <> "") {
			$query .= " AND CF_INOUT =  '".$cf_inout."' ";
		}

		if ($cf_type <> "") {
			$query .= " AND CF_TYPE =  '".$cf_type."' ";
		}

		if ($has_in_cash <> "") {
			if($has_in_cash == "Y")
				$query .= " AND CASH > 0 ";
			else
				$query .= " AND CASH = 0 AND CF_INOUT = '매출' AND TOTAL_PRICE > 0 ";
		}

		if ($match_tf <> "") {
			$query .= " AND MATCH_TF =  '".$match_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "" || $search_field == "ALL") { 

				$query .= " AND (CP_NM like '%".$search_str."%' OR BIZ_NO = '".$search_str."' OR CF_CODE LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "BIZ_NO") {
					$query .= " AND BIZ_NO = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
					$query .= " AND CP_NM like '%".$search_str."%' ";
			} else if ($search_field == "CF_CODE") {
					$query .= " AND CF_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listCashFlow($db, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) { 
		
		$op_cp_no = $filter["op_cp_no"];
		$account_cp_no = $filter["account_cp_no"];
		$sale_cp_no = $filter["sale_cp_no"];
		$sale_adm_no = $filter["sale_adm_no"];
		$cf_inout = $filter["cf_inout"];
		$cf_type = $filter["cf_type"];
		$has_in_cash = $filter["has_in_cash"];
		$match_tf = $filter["match_tf"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = " 
				SELECT @rownum:= @rownum - 1  as rn, CF_NO, CF_TYPE, CF_INOUT, CF_CODE, OP_CP_NO, BIZ_NO, CP_NM, ACCOUNT_CP_NO, SALE_CP_NO, OUT_DATE, WRITTEN_DATE, IN_DATE, SUPPLY_PRICE, SURTAX, TOTAL_PRICE, CASH, SALE_ADM_NO, MATCH_TF, CHECK_TF
				  FROM TBL_CASH_FLOW_STATEMENT
				 WHERE DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND ".$search_date_type." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$search_date_type." <= '".$end_date." 23:59:59' ";
		}

		if ($op_cp_no <> "") {
			$query .= " AND OP_CP_NO =  '".$op_cp_no."' ";
		}

		if ($account_cp_no <> "") {
			$query .= " AND ACCOUNT_CP_NO =  '".$account_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND SALE_CP_NO =  '".$sale_cp_no."' ";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND SALE_ADM_NO =  '".$sale_adm_no."' ";
		}

		if ($cf_inout <> "") {
			$query .= " AND CF_INOUT =  '".$cf_inout."' ";
		}

		if ($cf_type <> "") {
			$query .= " AND CF_TYPE =  '".$cf_type."' ";
		}

		if ($has_in_cash <> "") {
			if($has_in_cash == "Y")
				$query .= " AND CASH > 0 ";
			else
				$query .= " AND CASH = 0 AND CF_INOUT = '매출' AND TOTAL_PRICE > 0 ";
		}

		if ($match_tf <> "") {
			$query .= " AND MATCH_TF =  '".$match_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "" || $search_field == "ALL") { 

				$query .= " AND (CP_NM like '%".$search_str."%' OR BIZ_NO = '".$search_str."' OR CF_CODE LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "BIZ_NO") {
					$query .= " AND BIZ_NO = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
					$query .= " AND CP_NM like '%".$search_str."%' ";
			} else if ($search_field == "CF_CODE") {
					$query .= " AND CF_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		
		if ($order_field == "") 
			$order_field = "OUT_DATE";

		if ($order_str == "") 
			$order_str = "ASC";

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


	function sumCashFlow($db, $search_date_type, $start_date, $end_date, $filter, $search_field, $search_str) { 
		
		$op_cp_no = $filter["op_cp_no"];
		$account_cp_no = $filter["account_cp_no"];
		$sale_cp_no = $filter["sale_cp_no"];
		$sale_adm_no = $filter["sale_adm_no"];
		$cf_inout = $filter["cf_inout"];
		$cf_type = $filter["cf_type"];
		$has_in_cash = $filter["has_in_cash"];
		$match_tf = $filter["match_tf"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = " 
				SELECT IFNULL(SUM(SUPPLY_PRICE), 0) AS SUM_SUPPLY_PRICE,
					   IFNULL(SUM(SURTAX), 0) AS SUM_SURTAX,
					   IFNULL(SUM(TOTAL_PRICE), 0) AS SUM_TOTAL_PRICE
				  FROM
				  (
				  SELECT SUPPLY_PRICE, SURTAX, TOTAL_PRICE
				    FROM TBL_CASH_FLOW_STATEMENT
				   WHERE DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND ".$search_date_type." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ".$search_date_type." <= '".$end_date." 23:59:59' ";
		}

		if ($op_cp_no <> "") {
			$query .= " AND OP_CP_NO =  '".$op_cp_no."' ";
		}

		if ($account_cp_no <> "") {
			$query .= " AND ACCOUNT_CP_NO =  '".$account_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND SALE_CP_NO =  '".$sale_cp_no."' ";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND SALE_ADM_NO =  '".$sale_adm_no."' ";
		}

		if ($cf_inout <> "") {
			$query .= " AND CF_INOUT =  '".$cf_inout."' ";
		}

		if ($cf_type <> "") {
			$query .= " AND CF_TYPE =  '".$cf_type."' ";
		}

		if ($has_in_cash <> "") {
			if($has_in_cash == "Y")
				$query .= " AND CASH > 0 ";
			else
				$query .= " AND CASH = 0 AND CF_INOUT = '매출' AND TOTAL_PRICE > 0 ";
		}

		if ($match_tf <> "") {
			$query .= " AND MATCH_TF =  '".$match_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "" || $search_field == "ALL") { 

				$query .= " AND (CP_NM like '%".$search_str."%' OR BIZ_NO = '".$search_str."' OR CF_CODE LIKE '%".$search_str."%' ) ";
			} else if ($search_field == "BIZ_NO") {
					$query .= " AND BIZ_NO = '".$search_str."' ";
			} else if ($search_field == "CP_NM") {
					$query .= " AND CP_NM like '%".$search_str."%' ";
			} else if ($search_field == "CF_CODE") {
					$query .= " AND CF_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " ) AA ";

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

	function listCashFlowAccountCpDistinct($db, $search_date_type, $start_date, $end_date, $filter) { 
		
		$op_cp_no		= $filter["op_cp_no"];
		$account_cp_no  = $filter["account_cp_no"];
		$sale_cp_no		= $filter["sale_cp_no"];
		$sale_adm_no	= $filter["sale_adm_no"];

		$query = " 
				SELECT DISTINCT CF.ACCOUNT_CP_NO, CONCAT(C.CP_NM, ' ', C.CP_NM2) AS CP_NM
				  FROM TBL_CASH_FLOW_STATEMENT CF
				  JOIN TBL_COMPANY C ON CF.ACCOUNT_CP_NO = C.CP_NO
				 WHERE CF.DEL_TF = 'N' 
				 ";

		if ($start_date <> "") {
			$query .= " AND CF.".$search_date_type." >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND CF.".$search_date_type." <= '".$end_date." 23:59:59' ";
		}

		if ($op_cp_no <> "") {
			$query .= " AND CF.OP_CP_NO =  '".$op_cp_no."' ";
		}

		if ($sale_cp_no <> "") {
			$query .= " AND CF.SALE_CP_NO =  '".$sale_cp_no."' ";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND CF.SALE_ADM_NO =  '".$sale_adm_no."' ";
		}

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

	function selectCashFlowAccount($db, $cf_no) { 

		$query = " 
				SELECT OP_CP_NO, ACCOUNT_CP_NO, SALE_CP_NO, OUT_DATE, WRITTEN_DATE, IN_DATE, CASH, SALE_ADM_NO
				  FROM TBL_CASH_FLOW_STATEMENT
				 WHERE CF_NO = '$cf_no' ";

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

	function updateCashFlow($db, $params, $up_adm, $cf_no) {

		$op_cp_no		= $params['OP_CP_NO'];
		$cf_inout		= $params['CF_INOUT'];
		$cf_type		= $params['CF_TYPE']; 
		$cf_code		= $params['CF_CODE'];
		$biz_no			= $params['BIZ_NO'];
		$cp_nm			= $params['CP_NM']; 
		$goods_nm		= $params['GOODS_NM'];
		$out_date		= $params['OUT_DATE']; 
		$written_date	= $params['WRITTEN_DATE'];
		$supply_price	= $params['SUPPLY_PRICE']; 
		$surtax			= $params['SURTAX'];
		$total_price	= $params['TOTAL_PRICE'];

		$query = " 
			UPDATE TBL_CASH_FLOW_STATEMENT
			   SET OP_CP_NO = '$op_cp_no', 
				   CF_INOUT = '$cf_inout', 
				   CF_TYPE = '$cf_type', 
				   CF_CODE = '$cf_code', 
				   BIZ_NO = '$biz_no', 
				   CP_NM = '$cp_nm', 
				   GOODS_NM = '$goods_nm', 
				   OUT_DATE = '$out_date', 
				   WRITTEN_DATE = '$written_date', 
				   SUPPLY_PRICE = '$supply_price', 
				   SURTAX = '$surtax', 
				   TOTAL_PRICE = '$total_price'
				   UP_ADM = '$up_adm', 
				   UP_DATE = now()
			 WHERE CF_NO = '$cf_no'
				  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteCashFlow($db, $del_adm, $cf_no) {
		$query = " 
					UPDATE TBL_CASH_FLOW_STATEMENT
					   SET DEL_TF = 'Y',
						   DEL_ADM = '$del_adm', 
						   DEL_DATE = now()
					 WHERE CF_NO = '$cf_no'
				  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateCashFlowCheckTF($db, $check_tf, $cf_no) { 
		$query = " 
					UPDATE TBL_CASH_FLOW_STATEMENT
					   SET CHECK_TF = '$check_tf'
					 WHERE CF_NO = '$cf_no'
				  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	////////////////////////////////
	// 세금계산서 엑셀 입력
	////////////////////////////////

	function insertTempCashStatement($db, $temp_no, $written_date, $cf_code, $out_date, $biz_no1, $cp_nm1, $biz_no2, $cp_nm2, $total_price, $supply_price, $surtax, $goods_nm, $reg_adm) { 

		$query = " 
					INSERT INTO TBL_TEMP_CASH_FLOW_STATEMENT (TEMP_NO, WRITTEN_DATE, CF_CODE, OUT_DATE, BIZ_NO1, CP_NM1, BIZ_NO2, CP_NM2, TOTAL_PRICE, SUPPLY_PRICE, SURTAX, GOODS_NM, REG_ADM, REG_DATE)
					VALUES ('$temp_no', '$written_date', '$cf_code', '$out_date', '$biz_no1', '$cp_nm1', '$biz_no2', '$cp_nm2', '$total_price', '$supply_price', '$surtax', '$goods_nm', '$reg_adm', now() )
				 ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listTempCashStatement($db, $temp_no) { 

		$query = " 
				SELECT WRITTEN_DATE, CF_CODE, OUT_DATE, BIZ_NO1,  CP_NM1, BIZ_NO2, CP_NM2, TOTAL_PRICE, SUPPLY_PRICE, SURTAX, GOODS_NM
				  FROM TBL_TEMP_CASH_FLOW_STATEMENT
				 WHERE TEMP_NO = '$temp_no' ";

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


	function chkCashStatementByCFCode($db, $cf_code) { 

		//카드등일 경우 계산서가 없음
		if(trim($cf_code) == "") return 0;
		
		$query = " 
					SELECT COUNT(*) 
					  FROM TBL_CASH_FLOW_STATEMENT 
					 WHERE CF_CODE = '$cf_code' AND DEL_TF = 'N'
				 ";


		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function deleteTempCashStatement($db, $temp_no, $cf_code) { 

		$query = " 
				DELETE FROM TBL_TEMP_CASH_FLOW_STATEMENT
				      WHERE TEMP_NO = '$temp_no' AND CF_CODE = '$cf_code' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertTempToRealCashStatement($db, $temp_no, $cf_code) { 

		$query="SELECT WRITTEN_DATE, CF_CODE, OUT_DATE, BIZ_NO1, CP_NM1, BIZ_NO2, CP_NM2, TOTAL_PRICE, SUPPLY_PRICE, SURTAX, GOODS_NM
				  FROM TBL_TEMP_CASH_FLOW_STATEMENT
				 WHERE TEMP_NO = '$temp_no' AND CF_CODE IN ($cf_code)
			  ORDER BY REG_DATE ASC ";
		
		//echo $query."<br/>";
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

				$WRITTEN_DATE			= SetStringToDB($record[$j]["WRITTEN_DATE"]);
				$CF_CODE				= SetStringToDB($record[$j]["CF_CODE"]);
				$OUT_DATE				= SetStringToDB($record[$j]["OUT_DATE"]);
				$BIZ_NO1				= SetStringToDB($record[$j]["BIZ_NO1"]);
				$CP_NM1					= SetStringToDB($record[$j]["CP_NM1"]);
				$BIZ_NO2				= SetStringToDB($record[$j]["BIZ_NO2"]);
				$CP_NM2					= SetStringToDB($record[$j]["CP_NM2"]);
				$TOTAL_PRICE			= SetStringToDB($record[$j]["TOTAL_PRICE"]);
				$SUPPLY_PRICE			= SetStringToDB($record[$j]["SUPPLY_PRICE"]);
				$SURTAX					= SetStringToDB($record[$j]["SURTAX"]);
				$GOODS_NM				= SetStringToDB($record[$j]["GOODS_NM"]);

				$CP_NO1 = getOPCompanyNoByBizNo($db, $BIZ_NO1);
				$CP_NO2 = getOPCompanyNoByBizNo($db, $BIZ_NO2);

				
				if($CP_NO1 > 0) {
					$OP_CP_NO = $CP_NO1;
					$BIZ_NO = $BIZ_NO2;
					$CP_NM = $CP_NM2;
					$CF_INOUT = '매출';
				}

				if($CP_NO2 > 0) {
					$OP_CP_NO = $CP_NO2;
					$BIZ_NO = $BIZ_NO1;
					$CP_NM = $CP_NM1;
					$CF_INOUT = '매입';
				}

				if($SURTAX <> '0')
					$CF_TYPE = "CF002";
				else
					$CF_TYPE = "CF001";


				$query="INSERT INTO TBL_CASH_FLOW_STATEMENT (CF_TYPE, WRITTEN_DATE, CF_INOUT, CF_CODE, OUT_DATE, OP_CP_NO, BIZ_NO, CP_NM, TOTAL_PRICE, SUPPLY_PRICE, SURTAX, GOODS_NM) 
				             VALUES ('$CF_TYPE','$WRITTEN_DATE', '$CF_INOUT', '$CF_CODE', '$OUT_DATE', '$OP_CP_NO', '$BIZ_NO', '$CP_NM', '$TOTAL_PRICE', '$SUPPLY_PRICE', '$SURTAX', '$GOODS_NM') ";

				//echo $query."<br/>";

				if(mysql_query($query,$db)) {
					
					$query = " DELETE FROM TBL_TEMP_CASH_FLOW_STATEMENT WHERE TEMP_NO = '$temp_no' AND CF_CODE = '$CF_CODE' ";
					
					//echo $query."<br/>";

					mysql_query($query,$db);
					
				}
			}
		}


	}

	//승인번호 새 코드로 일괄 변경
	function changeCashStatementCFCode($db, $prev_cf_code, $next_cf_code, $up_adm) { 
	
		$query = " 
					UPDATE TBL_COMPANY_LEDGER
					   SET CF_CODE = '$next_cf_code', UP_ADM = '$up_adm', UP_DATE = now()
				     WHERE CF_CODE = '$prev_cf_code' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	function syncCashStatementWithCompanyLedger($db) { 

		$query = "
					UPDATE 
							(SELECT DISTINCT CF_CODE
							   FROM TBL_COMPANY_LEDGER_CONFIRM_CODE) CL
					  JOIN TBL_CASH_FLOW_STATEMENT CF ON CL.CF_CODE = CF.CF_CODE
					   SET CF.MATCH_TF = 'Y'
					 WHERE CF.MATCH_TF = 'N'
					  
				  ";

		//echo $query."<br/>";
		
		if(mysql_query($query,$db)) {
					
			$query = "
						UPDATE TBL_CASH_FLOW_STATEMENT CF
						   SET CF.MATCH_TF = 'N'
						 WHERE CF.MATCH_TF = 'Y' AND CF.CF_CODE NOT IN (SELECT DISTINCT CF_CODE
								   FROM TBL_COMPANY_LEDGER_CONFIRM_CODE)
						  
					  ";
			//echo $query."<br/>";

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
					
		} else 
			return false;

	}


	function listTaxInvoiceConfirmCode($db, $cl_no) {

		$query = "SELECT CD.DCODE_NM AS CF_TYPE, CLCC.CF_CODE
					FROM TBL_COMPANY_LEDGER_CONFIRM_CODE CLCC
			   LEFT JOIN TBL_CODE_DETAIL CD ON CLCC.CF_TYPE = CD.DCODE 
				   WHERE CL_NO = '$cl_no' ";

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

	function insertTaxInvoiceCode($db, $cl_no, $cf_code) { 

		$query = " INSERT INTO TBL_COMPANY_LEDGER_CONFIRM_CODE (CL_NO, CF_CODE)
				VALUES ('$cl_no', '$cf_code')
		 ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function getCompanyLedgerCLNoByOrderGoodsNo($db, $order_goods_no) {

		$query="SELECT CL_NO
				  FROM TBL_COMPANY_LEDGER 
				 WHERE ORDER_GOODS_NO = '$order_goods_no' AND INOUT_TYPE = '매출' AND DEL_TF = 'N' ";
		

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

	function listCompanyLedgerByReserveNo($db, $reserve_no) {

		$query="SELECT CL.CL_NO, CL.INOUT_DATE, CL.INOUT_TYPE, CL.NAME, CL.QTY, CL.UNIT_PRICE, CL.WITHDRAW, CL.DEPOSIT, CL.SURTAX, CL.MEMO, CL.ORDER_GOODS_NO
				  FROM TBL_COMPANY_LEDGER CL
				  JOIN TBL_ORDER_GOODS OG ON CL.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
				 WHERE OG.RESERVE_NO = '".$reserve_no."' AND CL.DEL_TF = 'N' 
			  ORDER BY CL.INOUT_DATE ASC, CL.CL_NO ASC	 
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

	function cancelTaxInvoiceCode($db, $cl_no, $cf_code) { 

		$query = " DELETE FROM TBL_COMPANY_LEDGER_CONFIRM_CODE 
		                 WHERE CL_NO = '$cl_no' AND CF_CODE = '$cf_code'
				 ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function getTaxInvoicePrice($db, $cf_code) { 

		$query = " SELECT TOTAL_PRICE
		             FROM TBL_CASH_FLOW_STATEMENT 
		            WHERE CF_CODE = '$cf_code' AND DEL_TF = 'N'
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

	function updateTaxInvoiceInCash($db, $cf_no, $inout_type, $cash, $up_adm) { 

		$inout_type_name = getDcodeName($db, "COMPANY_LEDGER_TYPE", $inout_type);

		$query="SELECT CL_NO
				  FROM TBL_COMPANY_LEDGER
				 WHERE REG_ADM = '$up_adm' AND INOUT_TYPE = '$inout_type_name'
			  ORDER BY REG_DATE DESC LIMIT 0, 1 ";
		
		//echo $query."<br/>";
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

				$CL_NO			= SetStringToDB($record[$j]["CL_NO"]);

				$query = " UPDATE TBL_CASH_FLOW_STATEMENT
							  SET CL_NO = '$CL_NO', CASH = CASH + $cash
		                    WHERE CF_NO = '$cf_no'
				 ";

				//echo $query."<br/>";

				if(!mysql_query($query,$db)) {
					return false;
					echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
					exit;
				} else {
					return true;
				}

			}
		}

		return false;

	}

	function deleteTaxInvoiceInCash($db, $cl_no) { 

		$query = " UPDATE TBL_CASH_FLOW_STATEMENT 
		              SET CL_NO = '', CASH = '0'
		            WHERE CL_NO = '$cl_no'
				 ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	// 계산서에대한 영업사원 정보, 업체 정보 업데이트  
	function updateTaxInvoceExtraInfo($db, $cl_no, $cf_code) { 

		$query="SELECT CL.CP_NO, O.OPT_MANAGER_NO
				  FROM TBL_COMPANY_LEDGER CL 
				  JOIN TBL_ORDER_GOODS OG ON CL.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
				  JOIN TBL_ORDER O ON O.RESERVE_NO = OG.RESERVE_NO
				 WHERE CL.CL_NO = '$cl_no'  ";
		
		//echo $query."<br/>";
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

				$CL_NO			= SetStringToDB($record[$j]["CL_NO"]);
				$OPT_MANAGER_NO	= SetStringToDB($record[$j]["OPT_MANAGER_NO"]);

				$query = " UPDATE TBL_CASH_FLOW_STATEMENT
							  SET SALE_ADM_NO = '$OPT_MANAGER_NO', SALE_CP_NO = '$OPT_MANAGER_NO'
						    WHERE CF_CODE = '$cf_code'
						 ";

				//echo $query."<br/>";

				if(!mysql_query($query,$db)) {
					return false;
					echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
					exit;
				} else {
					return true;
				}

			}
		}
	}


	function listPrevBalanceByMonth($db, $cp_no, $last_up_date) { 

		$query = " SELECT CP_NO, DATE_FORMAT( INOUT_DATE,  '%Y-%m' ) AS GROUP_MONTH, 
						  SUM(CASE WHEN INOUT_TYPE IN ('대체', '대입', '매출') AND CATE_01 <> '추가' THEN DEPOSIT ELSE 0 END ) AS SUM_DEPOSIT,
						  SUM(CASE WHEN INOUT_TYPE IN ('대체', '대입', '매출') AND CATE_01 = '추가' THEN DEPOSIT ELSE 0 END ) AS SUM_APPEND,
						  SUM(CASE WHEN INOUT_TYPE IN ('대체', '대입', '입금') THEN WITHDRAW ELSE 0 END ) AS SUM_WITHDRAW, ";
		
		if($last_up_date <> "") { 
			$query .= " 	  SUM(CASE WHEN INOUT_TYPE IN ('대체', '대입', '입금') AND INOUT_DATE >= '$last_up_date' THEN WITHDRAW ELSE 0 END ) AS SUM_LATER_WITHDRAW, ";
		}

		$query .= "		  SUM(CASE WHEN INOUT_TYPE IN ('차변실사') THEN DEPOSIT ELSE 0 END ) AS SUM_DEPOSIT_START,
						  SUM(CASE WHEN INOUT_TYPE IN ('대변실사') THEN WITHDRAW ELSE 0 END ) AS SUM_WITHDRAW_START
					 FROM TBL_COMPANY_LEDGER
					WHERE USE_TF =  'Y'
					  AND DEL_TF =  'N'
					  AND CP_NO = '".$cp_no."' ";

		$query .= " 	  
				 GROUP BY DATE_FORMAT( INOUT_DATE,  '%Y-%m' )
				 ORDER BY DATE_FORMAT( INOUT_DATE,  '%Y-%m' ) ASC
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

	function selectAccountReceivableReport($db, $cp_no) { 

		$query .= " SELECT CP_NO, MEMO, PREV_0, PREV_1, PREV_2, PREV_3, PREV_4, EXCEPT_TF, UP_ADM, UP_DATE
					  FROM TBL_COMPANY_LEDGER_REPORT
					 WHERE CP_NO = '".$cp_no."' ";

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

	function updateAccountReceivableReport($db, $cp_no, $memo, $prev_0, $prev_1, $prev_2, $prev_3, $except_tf, $up_adm) { 
	
		$query = " SELECT COUNT(*) 
		             FROM TBL_COMPANY_LEDGER_REPORT
				    WHERE CP_NO = '".$cp_no."' ";
		
		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		if($rows[0] > 0) { 

			$query = " UPDATE TBL_COMPANY_LEDGER_REPORT
					      SET MEMO = '$memo', PREV_0 = '$prev_0', PREV_1 = '$prev_1', PREV_2 = '$prev_2', PREV_3 = '$prev_3', EXCEPT_TF = '$except_tf',UP_ADM = '$up_adm', UP_DATE = now()
				    	WHERE CP_NO = '$cp_no'
					 ";

			//echo $query."<br/>";

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}

		} else { 

			$query = " INSERT INTO TBL_COMPANY_LEDGER_REPORT (CP_NO, MEMO, PREV_0, PREV_1, PREV_2, PREV_3, EXCEPT_TF, UP_ADM, UP_DATE)
			                VALUES ('$cp_no', '$memo', '$prev_0', '$prev_1', '$prev_2', '$prev_3', '$except_tf', '$up_adm', now())
					 ";

			//echo $query."<br/>";

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
		}

	}

	function updateAccountReceivableReport_MovePrev($db, $cp_no, $up_adm) { 

		$query = " UPDATE TBL_COMPANY_LEDGER_REPORT
					  SET PREV_4 = PREV_3, PREV_3 = PREV_2, PREV_2 = PREV_1, PREV_1 = '0', UP_ADM = '$up_adm', UP_DATE = now()
					WHERE CP_NO = '$cp_no' AND DATE_FORMAT(now(), '%Y-%m') <> DATE_FORMAT(UP_DATE, '%Y-%m') 
				 ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}



	function listCompanyLedgerByClNos($db, $arr_cl_no) {

		$str_cl_no = "";
		for($i= 0; $i < sizeof($arr_cl_no); $i++) { 
			$str_cl_no .= $arr_cl_no[$i].",";
		}
		$str_cl_no = rtrim($str_cl_no, ",");

		
		$query = "
				  SELECT CL_NO, GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, TAX_CONFIRM_TF, TAX_CONFIRM_DATE, USE_TF, CATE_01, TAX_TF, CF_CODE, INPUT_TYPE
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND CL_NO IN (".$str_cl_no.");
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

	function listCompanyLedgerByGroupNos($db, $arr_group_no, $arr_not_cl_no) {

		$str_group_no = "";
		for($i= 0; $i < sizeof($arr_group_no); $i++) { 
			$str_group_no .= $arr_group_no[$i].",";
		}
		$str_group_no = rtrim($str_group_no, ",");

		$str_not_cl_no = "";
		for($i= 0; $i < sizeof($arr_not_cl_no); $i++) { 
			$str_not_cl_no .= $arr_not_cl_no[$i].",";
		}
		$str_not_cl_no = rtrim($str_not_cl_no, ",");

		
		$query = "
				  SELECT CL_NO, GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, TAX_CONFIRM_TF, TAX_CONFIRM_DATE, USE_TF, CATE_01, TAX_TF, CF_CODE, INPUT_TYPE
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND GROUP_NO IN (".$str_group_no.") AND CL_NO NOT IN (".$str_not_cl_no.") ;
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

	function updateCompanyLedgerBySusuRate($db, $arr_cl_no, $susu_rate, $up_adm) {

		$str_cl_no = "";
		for($i= 0; $i < sizeof($arr_cl_no); $i++) { 
			$str_cl_no .= $arr_cl_no[$i].",";
		}
		$str_cl_no = rtrim($str_cl_no, ",");

		
		$query = "
				  SELECT CL_NO, GROUP_NO, CP_NO, TO_CP_NO, INOUT_DATE, INOUT_TYPE, GOODS_NO, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, SURTAX, MEMO, RESERVE_NO, ORDER_GOODS_NO, RGN_NO, TAX_CONFIRM_TF, TAX_CONFIRM_DATE, USE_TF, CATE_01, TAX_TF, CF_CODE, INPUT_TYPE
					FROM TBL_COMPANY_LEDGER
				   WHERE DEL_TF = 'N' AND CL_NO IN (".$str_cl_no.") ;
		";

		//echo $query."<br/>";
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$CL_NO			= $record[$i]["CL_NO"];
				$GROUP_NO		= $record[$i]["GROUP_NO"];
				$INOUT_TYPE		= $record[$i]["INOUT_TYPE"];

				$QTY			= $record[$i]["QTY"];
				$UNIT_PRICE		= $record[$i]["UNIT_PRICE"];
				$DEPOSIT		= $record[$i]["DEPOSIT"];
				$SURTAX			= $record[$i]["SURTAX"];

				//혹시 잘못된 CL_NO가 왔을때를 방지
				if($INOUT_TYPE != "대입") continue;

				$query2 = "
							UPDATE TBL_COMPANY_LEDGER
							   SET SURTAX = ROUND($UNIT_PRICE * $susu_rate / 100),
							       DEPOSIT = ROUND($UNIT_PRICE - ($UNIT_PRICE * $susu_rate / 100)),
								   MEMO = $susu_rate,
								   UP_DATE = now(),
								   UP_ADM = '$up_adm'
							 WHERE DEL_TF = 'N' AND CL_NO = ".$CL_NO." ; ";
				
				if(mysql_query($query2,$db)) { 

					$query3 = "
							UPDATE TBL_COMPANY_LEDGER
							   SET SURTAX = $UNIT_PRICE * $susu_rate / 100,
								   MEMO = $susu_rate,
								   UP_DATE = now(),
								   UP_ADM = '$up_adm'
							 WHERE DEL_TF = 'N' AND GROUP_NO = ".$GROUP_NO." AND CL_NO != ".$CL_NO." ; ";
					mysql_query($query3,$db);
				}

			}
		}
	}

?>