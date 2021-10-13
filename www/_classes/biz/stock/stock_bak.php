<?
	# =============================================================================
	# File Name    : stock.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.05
	# Modify Date  : 
	#	Copyright : Copyright @Orion Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table ST_ORDER_GOODS, ST_TEMP_ORDER_GOODS 
	#=========================================================================================================
	
	/*	
CREATE TABLE IF NOT EXISTS `ST_ORDER_GOODS` (
  `ORDER_GOODS_NO` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `BUY_CP_NO` int(10) unsigned NOT NULL,
  `GOODS_NO` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '상품 일련번호',
  `GOODS_CODE` varchar(10) NOT NULL DEFAULT '' COMMENT '상품 코드',
  `QTY` int(11) NOT NULL DEFAULT '0',
  `GOODS_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
  `GOODS_SUB_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
  `GOODS_OPTION_01` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_02` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_03` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_04` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_01` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_02` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_03` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_04` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `CATE_01` varchar(50) DEFAULT NULL COMMENT '임시	1',
  `CATE_02` varchar(50) DEFAULT NULL,
  `CATE_03` varchar(50) DEFAULT NULL,
  `CATE_04` varchar(50) DEFAULT NULL,
  `BUY_PRICE` int(11) NOT NULL DEFAULT '0' COMMENT '상품 가격',
  `SALE_PRICE` int(11) NOT NULL DEFAULT '0' COMMENT '상품 판매 가격',
  `EXTRA_PRICE` int(11) NOT NULL DEFAULT '0' COMMENT '관리비',
  `DELIVERY_PRICE` int(11) NOT NULL,
  `SA_DELIVERY_PRICE` int(11) NOT NULL DEFAULT '0' COMMENT '관리비',
  `ORDER_STATE` varchar(10) NOT NULL DEFAULT '' COMMENT '주문상태',
  `ORDER_DATE` datetime DEFAULT NULL COMMENT '등록일',
  `CONFIRM_TF` varchar(2) NOT NULL DEFAULT 'N',
  `CONFIRM_DATE` datetime NOT NULL,
  `CONFIRM_YMD` varchar(10) NOT NULL,
  `CONFIRM_ADM` varchar(30) NOT NULL,
  `USE_TF` char(1) NOT NULL DEFAULT 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  `DEL_TF` char(1) NOT NULL DEFAULT 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  `REG_ADM` varchar(30) DEFAULT NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  `REG_DATE` datetime DEFAULT NULL COMMENT '등록일',
  `DEL_ADM` varchar(30) DEFAULT NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  `DEL_DATE` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`ORDER_GOODS_NO`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='발주 상품 마스터' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ST_TEMP_ORDER_GOODS` (
  `TEMP_NO` varchar(30) NOT NULL,
  `ORDER_GOODS_NO` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `BUY_CP_NO` varchar(30) NOT NULL,
  `GOODS_NO` varchar(30) NOT NULL COMMENT '상품 일련번호',
  `GOODS_CODE` varchar(10) NOT NULL DEFAULT '' COMMENT '상품 코드',
  `QTY` int(11) NOT NULL DEFAULT '0',
  `GOODS_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
  `GOODS_SUB_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
  `GOODS_OPTION_01` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_02` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_03` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_04` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_01` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_02` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_03` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `GOODS_OPTION_NM_04` varchar(100) NOT NULL DEFAULT '' COMMENT '옵션',
  `BUY_PRICE` int(11) NOT NULL DEFAULT '0' COMMENT '상품 가격',
  `ORDER_STATE` varchar(10) NOT NULL DEFAULT '' COMMENT '주문상태',
  `ORDER_DATE` datetime DEFAULT NULL COMMENT '등록일',
  `USE_TF` char(1) NOT NULL DEFAULT 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  `DEL_TF` char(1) NOT NULL DEFAULT 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  `REG_ADM` varchar(30) DEFAULT NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  `REG_DATE` datetime DEFAULT NULL COMMENT '등록일',
  `DEL_ADM` varchar(30) DEFAULT NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  `DEL_DATE` datetime DEFAULT NULL COMMENT '삭제일',
  PRIMARY KEY (`ORDER_GOODS_NO`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='임시 발주 상품 마스터' AUTO_INCREMENT=1 ;


	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listStOrder($db, $start_date, $end_date, $order_state, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntStOrder($db, $start_date, $end_date, $order_state, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO,
										 GOODS_CODE, QTY, GOODS_NAME, GOODS_SUB_NAME,
										 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
										 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
										 CATE_01, CATE_02, CATE_03, CATE_04,
										 BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE,
										 ORDER_STATE, ORDER_DATE, PAY_DATE, CONFIRM_TF, CONFIRM_DATE, CONFIRM_YMD, CONFIRM_ADM,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM ST_ORDER_GOODS WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

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

	function totalCntStOrder($db, $start_date, $end_date, $order_state, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT COUNT(*) AS CNT
								FROM ST_ORDER_GOODS WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND ORDER_STATE LIKE '%".$order_state."%' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertStOrder($db, $buy_cp_no, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $cate_01, $cate_02, $cate_03, $cate_04, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $order_state, $order_date, $pay_date, $use_tf, $reg_adm) {

			$query="INSERT INTO ST_ORDER_GOODS (BUY_CP_NO, GOODS_NO, GOODS_CODE, QTY, GOODS_NAME, GOODS_SUB_NAME,
																					GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
																					GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
																					CATE_01, CATE_02, CATE_03, CATE_04,
																					BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE,
																					ORDER_STATE, ORDER_DATE, PAY_DATE, USE_TF, REG_ADM, REG_DATE) 
													 values ('$buy_cp_no', '$goods_no', '$goods_code', '$qty', '$goods_name', '$goods_sub_name',
																					'$goods_option_01', '$goods_option_02', '$goods_option_03', '$goods_option_04',
																					'$goods_option_nm_01', '$goods_option_nm_02', '$goods_option_nm_03', '$goods_option_nm_04',
																					'$cate_01', '$cate_02', '$cate_03', '$cate_04',
																					'$buy_price', '$sale_price', '$extra_price', '$delivery_price', '$sa_delivery_price',
																					'$order_state', '$order_date', '$pay_date', '$use_tf', '$reg_adm', now()); ";

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


	function selectStOrder($db, $order_goods_no) {

		$query = "SELECT ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO,
										 GOODS_CODE, QTY, GOODS_NAME, GOODS_SUB_NAME,
										 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
										 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
										 CATE_01, CATE_02, CATE_03, CATE_04,
										 BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE,
										 ORDER_STATE, ORDER_DATE, PAY_DATE, CONFIRM_TF, CONFIRM_DATE, CONFIRM_YMD, CONFIRM_ADM,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM ST_ORDER_GOODS WHERE USE_TF= 'Y' AND DEL_TF = 'N' AND ORDER_GOODS_NO = '$order_goods_no' ";
		
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

	function updateStOrder($db, $buy_cp_no, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $pay_date, $buy_price, $up_adm, $order_goods_no) {

		$query="UPDATE ST_ORDER_GOODS SET 
													BUY_CP_NO						= '$buy_cp_no',
													GOODS_NO						= '$goods_no',
													GOODS_CODE					= '$goods_code',
													QTY									= '$qty',
													GOODS_NAME					= '$goods_name',
													GOODS_SUB_NAME			= '$goods_sub_name',
													GOODS_OPTION_01			= '$goods_option_01',
													GOODS_OPTION_02			= '$goods_option_02',
													GOODS_OPTION_03			= '$goods_option_03',
													GOODS_OPTION_NM_01	= '$goods_option_nm_01',
													GOODS_OPTION_NM_02	= '$goods_option_nm_02',
													GOODS_OPTION_NM_03	= '$goods_option_nm_03',
													PAY_DATE						= '$pay_date',
													BUY_PRICE						= '$buy_price'
										WHERE ORDER_GOODS_NO			= '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteStOrder($db, $order_goods_no) {

		$query="UPDATE ST_ORDER_GOODS SET DEL_TF = 'Y' WHERE ORDER_GOODS_NO = '$order_goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function insertTempStOrder($db, $file_nm, $buy_cp_no, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $buy_price, $order_state, $order_date, $pay_date, $use_tf, $reg_adm) {
		
		$query="INSERT INTO ST_TEMP_ORDER_GOODS (TEMP_NO, BUY_CP_NO, GOODS_NO, GOODS_CODE, QTY, GOODS_NAME, GOODS_SUB_NAME,
																						 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
																						 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
																						 BUY_PRICE, ORDER_STATE, ORDER_DATE, PAY_DATE,
																						 USE_TF, REG_ADM, REG_DATE) 
													 values ('$file_nm', '$buy_cp_no', '$goods_no', '$goods_code', '$qty', '$goods_name', '$goods_sub_name', 
																	 '$goods_option_01', '$goods_option_02', '$goods_option_03', '$goods_option_04', 
																	 '$goods_option_nm_01', '$goods_option_nm_02', '$goods_option_nm_03', '$goods_option_nm_04', 
																	 '$buy_price', '$order_state', '$order_date', '$pay_date', '$use_tf', '$reg_adm', now()); ";
		
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


	function listTempStOrder($db, $temp_no) {

		$query = "SELECT TEMP_NO, ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO, GOODS_CODE,
										 QTY, GOODS_NAME, GOODS_SUB_NAME,
										 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
										 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
										 BUY_PRICE, ORDER_STATE, ORDER_DATE, PAY_DATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM ST_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' ";

		
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


	function selectTempStOrder($db, $temp_no, $order_goods_no) {

		$query = "SELECT TEMP_NO, ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO, GOODS_CODE,
										 QTY, GOODS_NAME, GOODS_SUB_NAME,
										 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
										 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
										 BUY_PRICE, ORDER_STATE, ORDER_DATE, PAY_DATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM ST_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' AND ORDER_GOODS_NO = '$order_goods_no' ";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}


	function updateTempStOrder($db, $buy_cp_no, $goods_no, $goods_code, $qty, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03, $goods_option_04, $goods_option_nm_01, $goods_option_nm_02, $goods_option_nm_03, $goods_option_nm_04, $pay_date, $buy_price, $up_adm, $temp_no, $order_goods_no) {

		$query="UPDATE ST_TEMP_ORDER_GOODS SET 
													BUY_CP_NO						= '$buy_cp_no',
													GOODS_NO						= '$goods_no',
													GOODS_CODE					= '$goods_code',
													QTY									= '$qty',
													GOODS_NAME					= '$goods_name',
													GOODS_SUB_NAME			= '$goods_sub_name',
													GOODS_OPTION_01			= '$goods_option_01',
													GOODS_OPTION_02			= '$goods_option_02',
													GOODS_OPTION_03			= '$goods_option_03',
													GOODS_OPTION_NM_01	= '$goods_option_nm_01',
													GOODS_OPTION_NM_02	= '$goods_option_nm_02',
													GOODS_OPTION_NM_03	= '$goods_option_nm_03',
													PAY_DATE						= '$pay_date',
													BUY_PRICE						= '$buy_price'
										WHERE TEMP_NO							= '$temp_no'
											AND ORDER_GOODS_NO			= '$order_goods_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempStOrder($db, $temp_no, $order_goods_no) {

		$query="DELETE FROM ST_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' AND ORDER_GOODS_NO = '$order_goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertTempToRealStOrder($db, $temp_no, $str_order_goods_no) {
		
		$query="SELECT TEMP_NO, ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO, GOODS_CODE,
									 QTY, GOODS_NAME, GOODS_SUB_NAME,
									 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
									 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
									 BUY_PRICE, ORDER_STATE, ORDER_DATE, PAY_DATE,
									 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
							FROM ST_TEMP_ORDER_GOODS
						 WHERE TEMP_NO = '$temp_no' AND ORDER_GOODS_NO IN ($str_order_goods_no) ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		if (sizeof($record) > 0) {

			for ($j = 0 ; $j < sizeof($record); $j++) {

				// 인서트 합니다..
				$BUY_CP_NO					= trim($record[$j]["BUY_CP_NO"]);
				$GOODS_NO						= trim($record[$j]["GOODS_NO"]);
				$GOODS_NAME					= trim($record[$j]["GOODS_NAME"]);
				$QTY								= trim($record[$j]["QTY"]);
				$BUY_PRICE					= trim($record[$j]["BUY_PRICE"]);
				$GOODS_OPTION_NM_01	= trim($record[$j]["GOODS_OPTION_NM_01"]);
				$GOODS_OPTION_01		= trim($record[$j]["GOODS_OPTION_01"]);
				$GOODS_OPTION_NM_02	= trim($record[$j]["GOODS_OPTION_NM_02"]);
				$GOODS_OPTION_02		= trim($record[$j]["GOODS_OPTION_02"]);
				$GOODS_OPTION_NM_03	= trim($record[$j]["GOODS_OPTION_NM_03"]);
				$GOODS_OPTION_03		= trim($record[$j]["GOODS_OPTION_03"]);
				$ORDER_DATE					= trim($record[$j]["ORDER_DATE"]);
				$PAY_DATE						= trim($record[$j]["PAY_DATE"]);
				$REG_ADM						= trim($record[$j]["REG_ADM"]);
				
				// 상품 정보 수집
				$arr_rs = selectGoods($db, $GOODS_NO);
				$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
				$rs_price						= trim($arr_rs[0]["PRICE"]); 
				//$BUY_CP_NO					= trim($arr_rs[0]["CATE_03"]); 
	
				$order_state		= "1";
				$use_tf					= "Y";
				$goods_code			= "";
				$goods_sub_name	= "";

				$result = insertStOrder($db, $BUY_CP_NO, $GOODS_NO, $goods_code, $QTY, $rs_goods_name, $goods_sub_name, $GOODS_OPTION_01, $GOODS_OPTION_02, $GOODS_OPTION_03, $GOODS_OPTION_04, $GOODS_OPTION_NM_01, $GOODS_OPTION_NM_02, $GOODS_OPTION_NM_03, $GOODS_OPTION_NM_04, $cate_01, $cate_02, $cate_03, $cate_04, $BUY_PRICE, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $order_state, $ORDER_DATE, $PAY_DATE, $use_tf, $REG_ADM);

			}
		}
		
		#echo $query;

		if(!$result) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteTempToRealStOrder($db, $temp_no, $str_order_goods_no) {
		

		$query=" DELETE FROM  ST_TEMP_ORDER_GOODS WHERE TEMP_NO = '$temp_no' AND ORDER_GOODS_NO IN ($str_order_goods_no) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listAllStOrder($db, $start_date, $end_date, $order_state, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
										 SUM(AA.QTY) ALL_QTY
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
														 A.BUY_PRICE
												FROM ST_ORDER_GOODS A
											 WHERE A.USE_TF = 'Y' 
												 AND A.DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND A.ORDER_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND A.ORDER_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($order_state <> "") {
			$query .= " AND A.ORDER_STATE  = '".$order_state."' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND A.BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
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

	function listStConfirmOrder($db, $start_date, $end_date, $confirm_tf, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntStConfirmOrder($db, $start_date, $end_date, $confirm_tf, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
										 ORDER_GOODS_NO, BUY_CP_NO, GOODS_NO,
										 GOODS_CODE, QTY, GOODS_NAME, GOODS_SUB_NAME,
										 GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04,
										 GOODS_OPTION_NM_01, GOODS_OPTION_NM_02, GOODS_OPTION_NM_03, GOODS_OPTION_NM_04,
										 CATE_01, CATE_02, CATE_03, CATE_04,
										 BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DELIVERY_PRICE, SA_DELIVERY_PRICE,
										 ORDER_STATE, ORDER_DATE, PAY_DATE, CONFIRM_TF, CONFIRM_DATE, CONFIRM_YMD, CONFIRM_ADM,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM ST_ORDER_GOODS WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND PAY_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND PAY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND CONFIRM_TF = '".$confirm_tf."' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "ORDER_DATE";

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

	function totalCntStConfirmOrder($db, $start_date, $end_date, $confirm_tf, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT COUNT(*) AS CNT
								FROM ST_ORDER_GOODS WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND PAY_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND PAY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND CONFIRM_TF = '".$confirm_tf."' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO = '".$buy_cp_no."' ";
		} 

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}


		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listAllStConfirmOrder($db, $start_date, $end_date, $confirm_tf, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
										 SUM(AA.QTY) ALL_QTY
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
														 A.BUY_PRICE
												FROM ST_ORDER_GOODS A
											 WHERE A.USE_TF = 'Y' 
												 AND A.DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND PAY_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND PAY_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($confirm_tf <> "") {
			$query .= " AND CONFIRM_TF = '".$confirm_tf."' ";
		} 

		if ($buy_cp_no <> "") {
			$query .= " AND A.BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' )"; 
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

	function updateConfirmStateStOrder($db, $order_goods_no, $confirm_ymd, $confirm_tf, $up_adm_no) {
		
		if ($confirm_tf == "Y") {

			$query = "UPDATE ST_ORDER_GOODS SET CONFIRM_TF = 'Y', CONFIRM_DATE = now(), 
																				 CONFIRM_YMD = '$confirm_ymd',
																				 CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND CONFIRM_TF = 'N' ";
		} else {

			$query = "UPDATE ST_ORDER_GOODS SET CONFIRM_TF = 'N', CONFIRM_DATE = NULL, 
																				 CONFIRM_YMD = '',
																				 CONFIRM_ADM = '$up_adm_no'
																	 WHERE ORDER_GOODS_NO = '$order_goods_no' AND CONFIRM_TF = 'Y' ";
		}
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function listBuyStConfirmList($db, $start_date, $end_date, $buy_cp_no, $order_field, $order_str) {

		$query = "SELECT AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.CP_PHONE,
										 SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE
								FROM
										 (SELECT	CONFIRM_YMD, BUY_CP_NO, 
															QTY,
															BUY_PRICE
												FROM ST_ORDER_GOODS 
											 WHERE CONFIRM_YMD <> ''
												 AND CONFIRM_TF = 'Y'
												 AND USE_TF = 'Y' 
												 AND DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND CONFIRM_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND CONFIRM_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO  = '".$buy_cp_no."' ";
		}


		$query .= "					 AND DEL_TF = 'N') AA, TBL_COMPANY BB
									 WHERE AA.BUY_CP_NO = BB.CP_NO ";


		$query .= "		GROUP BY AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.CP_PHONE ";

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

	function listBuyStConfirmAll($db, $start_date, $end_date, $buy_cp_no) {

		$query = "SELECT SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE
								FROM
										 (SELECT	BUY_CP_NO, 
															QTY,
															BUY_PRICE 
												FROM	ST_ORDER_GOODS 
											 WHERE	CONFIRM_YMD <> ''
												 AND	CONFIRM_TF = 'Y'
												 AND	USE_TF = 'Y' 
												 AND	DEL_TF = 'N' ";

		if ($start_date <> "") {
			$query .= " AND CONFIRM_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND CONFIRM_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		$query .= "					 AND DEL_TF = 'N') AA, TBL_COMPANY BB WHERE AA.BUY_CP_NO = BB.CP_NO ";

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

	// 정산 상세 리스트
	function listConfirmCpStOrderList($db, $confirm_ymd, $buy_cp_no, $use_tf, $del_tf, $search_field, $search_str) {
		
		$query = "SELECT GOODS_NAME, CONFIRM_YMD, BUY_CP_NO,
										 PAY_DATE,
										 QTY,
										 BUY_PRICE 
								FROM ST_ORDER_GOODS
							 WHERE CONFIRM_YMD <> ''
								 AND CONFIRM_TF = 'Y'
								 AND USE_TF = 'Y' 
								 AND DEL_TF = 'N' ";

		if ($confirm_ymd <> "") {
			$query .= " AND CONFIRM_YMD = '".$confirm_ymd."' ";
		}

		if ($buy_cp_no <> "") {
			$query .= " AND BUY_CP_NO  = '".$buy_cp_no."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				$query .= " AND (GOODS_NAME LIKE '%".$search_str."%' )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ORDER BY ORDER_GOODS_NO DESC ";
		
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