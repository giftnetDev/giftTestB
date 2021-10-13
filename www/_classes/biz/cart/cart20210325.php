<?
	# =============================================================================
	# File Name    : cart.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.05
	# Modify Date  : 
	#	Copyright : Copyright @minumsa Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_CART
	#=========================================================================================================
	
	/*	

	--
	-- 테이블 구조 `TBL_CART`
	--

	CREATE TABLE IF NOT EXISTS `TBL_CART` (
	  `CART_NO` int(7) unsigned NOT NULL AUTO_INCREMENT,
	  `ON_UID` varchar(32) NOT NULL,
	  `CP_NO` int(11) NOT NULL,
	  `BUY_CP_NO` int(10) unsigned NOT NULL,
	  `MEM_NO` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '회원 번호',
	  `CART_SEQ` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '순서 번호',
	  `GOODS_NO` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '상품 일련번호',
	  `GOODS_CODE` varchar(10) NOT NULL DEFAULT '' COMMENT '상품 코드',
	  `GOODS_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
	  `GOODS_SUB_NAME` varchar(100) NOT NULL DEFAULT '' COMMENT '상품명',
	  `QTY` int(11) NOT NULL DEFAULT '0',
	  `OPT_STICKER_NO` int(11) NOT NULL COMMENT '스티커번호',
	  `OPT_OUTBOX_TF` char(1) NOT NULL COMMENT '아웃박스스티커유무',
	  `OPT_OUTBOX_CNT` int(11) NOT NULL COMMENT '아웃박스수량',
	  `OPT_WRAP_NO` int(11) NOT NULL COMMENT '포장지번호',
	  `OPT_PRINT_MSG` varchar(200) NOT NULL COMMENT '인쇄내용',
	  `OPT_OUTSTOCK_DATE` datetime NOT NULL COMMENT '출고일',
	  `OPT_MEMO` varchar(200) NOT NULL COMMENT '작업메모',
	  `CATE_01` varchar(50) DEFAULT NULL COMMENT '임시	1',
	  `CATE_02` varchar(50) DEFAULT NULL,
	  `CATE_03` varchar(50) DEFAULT NULL,
	  `CATE_04` varchar(50) DEFAULT NULL,
	  `BUY_PRICE` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '공급가',
	  `SALE_PRICE` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '상품 판매 가격',
	  `EXTRA_PRICE` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '관리비',
	  `DELIVERY_PRICE` int(11) NOT NULL,
	  `SA_DELIVERY_PRICE` int(11) unsigned NOT NULL DEFAULT '0',
	  `USE_TF` char(1) NOT NULL DEFAULT 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
	  `DEL_TF` char(1) NOT NULL DEFAULT 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
	  `REG_ADM` varchar(30) DEFAULT NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	  `REG_DATE` datetime DEFAULT NULL COMMENT '등록일',
	  `DEL_ADM` varchar(30) DEFAULT NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	  `DEL_DATE` datetime DEFAULT NULL COMMENT '삭제일',
	  PRIMARY KEY (`CART_NO`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr COMMENT='장바구니 마스터' AUTO_INCREMENT=1 ;

	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================
	/*
	CART_NO, RESERVE_NO, MEM_NO, CART_SEQ, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY,
	OPT_STICKER_NO, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX, OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE, OPT_MEMO, 
	CATE_01, CATE_02, CATE_03, CATE_04, PRICE, SALE_PRICE, EXTRA_PRICE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
	*/



	function listCart($db, $on_uid, $cp_no, $use_tf, $del_tf, $order_str) {

		$query = "SELECT C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
										 C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
										 C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, C.CATE_04 AS C_CATE_04, 
										 G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, 
										 C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
										 G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
										 G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
								FROM TBL_CART C, TBL_GOODS G WHERE C.GOODS_NO = G.GOODS_NO ";

		if ($on_uid <> "") {
			$query .= " AND C.ON_UID = '".$on_uid."' ";
		} else {
			$query .= " AND C.ON_UID = 'X' ";
		}

		if ($cp_no <> "") {
			$query .= " AND C.CP_NO = '".$cp_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY C.CART_NO ".$order_str;

		echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntCart($db, $on_uid, $cp_no, $use_tf, $del_tf){

		$query ="SELECT COUNT(*) CNT FROM TBL_CART WHERE 1 = 1 ";

		if ($reserve_no <> "") {
			$query .= " AND ON_UID = '".$reserve_no."' ";
		} else {
			$query .= " AND ON_UID = 'X' ";
		}

		if ($mem_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function insertCart($db, $on_uid, $cp_order_no, $cp_no, $buy_cp_no, $cart_seq, $goods_no, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04,  $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm)
	{
		$opt_request_memo = $memos["opt_request_memo"];
		$opt_support_memo = $memos["opt_support_memo"];

		//$query = "SELECT COUNT(GOODS_NO) AS CNT 
		//						FROM TBL_CART 
		//					 WHERE ON_UID = '$on_uid' 
		//						 AND GOODS_NO = '$goods_no' 
		//						 ";

		//$result = mysql_query($query,$db);
		//$rows   = mysql_fetch_array($result);
		//$cnt  = $rows[0];
		
		//if ($cnt == 0) {

			$query="INSERT INTO TBL_CART (ON_UID, CP_ORDER_NO, CP_NO, BUY_CP_NO, CART_SEQ, GOODS_NO, QTY,
											OPT_STICKER_NO, OPT_STICKER_MSG, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX,
											OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE,
											OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, CATE_01, CATE_02, CATE_03, CATE_04,  
											PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, 
											DELIVERY_PRICE, SA_DELIVERY_PRICE, DISCOUNT_PRICE, STICKER_PRICE, PRINT_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, 
											USE_TF, REG_ADM, REG_DATE) 
								 values ('$on_uid', '$cp_order_no', '$cp_no', '$buy_cp_no', '$cart_seq', '$goods_no', '$qty',
										'$opt_sticker_no', '$opt_sticker_msg', '$opt_outbox_tf', '$delivery_cnt_in_box',
										'$opt_wrap_no', '$opt_print_msg', '$opt_outstock_date',
										'$opt_memo', '$opt_request_memo', '$opt_support_memo', '$delivery_type', '$delivery_cp', '$sender_nm', '$sender_phone', 
										'$cate_01', '$cate_02', '$cate_03', '$cate_04', 
										'$price', '$buy_price', '$sale_price', '$extra_price',
										'$delivery_price', '$sa_delivery_price', '$discount_price', '$sticker_price', '$print_price', '$sale_susu', '$labor_price', '$other_price', '$use_tf', '$reg_adm', now()); ";
		
			//echo $query;
			//exit;
		//} 
		//else {
		//	$query = "UPDATE TBL_CART SET QTY = QTY + 1
		//						 WHERE ON_UID = '$on_uid' 
		//							 AND GOODS_NO = '$goods_no' 
		//							 ";
		//}

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	
	/*
	function insertWishToCart($db, $wish_no, $reserve_no) {

		$query = "SELECT GOODS_NO, GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04
								FROM TBL_WISH 
							 WHERE WISH_NO				= '$wish_no' ";

		$result						= mysql_query($query,$db);
		$rows							= mysql_fetch_array($result);
		$goods_no					= $rows[0];
		$goods_option_01	= $rows[1];
		$goods_option_02	= $rows[2];
		$goods_option_03	= $rows[3];
		$goods_option_04	= $rows[4];

		$query = "SELECT COUNT(GOODS_NO) AS CNT 
								FROM TBL_CART 
							 WHERE RESERVE_NO = '$reserve_no' 
								 AND GOODS_NO	= '$goods_no' 
								 AND GOODS_OPTION_01 = '$goods_option_01' 
								 AND GOODS_OPTION_02 = '$goods_option_02' 
								 AND GOODS_OPTION_03 = '$goods_option_03' 
								 AND GOODS_OPTION_04 = '$goods_option_04' 
								 ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$cnt  = $rows[0];

		if ($cnt == 0) {

			$query="INSERT INTO TBL_CART (
								RESERVE_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY,
								GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03,
								GOODS_OPTION_04, GOODS_OPTION_NM_01, GOODS_OPTION_NM_02,
								GOODS_OPTION_NM_03, GOODS_OPTION_NM_04, CATE_01, CATE_02,
								CATE_03, CATE_04, PRICE, SALE_PRICE, EXTRA_PRICE, USE_TF, REG_ADM, REG_DATE
							)
							SELECT '$reserve_no', W.GOODS_NO, W.GOODS_CODE, W.GOODS_NAME, W.GOODS_SUB_NAME, W.QTY,
											W.GOODS_OPTION_01, W.GOODS_OPTION_02, W.GOODS_OPTION_03,
											W.GOODS_OPTION_04, W.GOODS_OPTION_NM_01, W.GOODS_OPTION_NM_02,
											W.GOODS_OPTION_NM_03, W.GOODS_OPTION_NM_04, W.CATE_01, W.CATE_02,
											W.CATE_03, W.CATE_04, G.PRICE, G.SALE_PRICE, G.EXTRA_PRICE, W.USE_TF, 'WEB', now()
								FROM TBL_WISH W, TBL_GOODS G
							 WHERE W.GOODS_NO = G.GOODS_NO
								 AND WISH_NO		= '$wish_no' ";

		} else {
			$query = "UPDATE TBL_CART SET REG_DATE = now()
								 WHERE RESERVE_NO = '$reserve_no' 
									 AND GOODS_NO = '$goods_no' 
									 AND GOODS_OPTION_01 = '$goods_option_01' 
									 AND GOODS_OPTION_02 = '$goods_option_02' 
									 AND GOODS_OPTION_03 = '$goods_option_03' 
									 AND GOODS_OPTION_04 = '$goods_option_04' 
									 ";
		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	*/

	function selectCart($db, $cart_no) {

		$query = "SELECT C.CART_NO, C.RESERVE_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, 
										 C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.OPT_OUTBOX_CNT, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, 
										 C.OPT_MEMO, C.DELIVERY_TYPE, C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, C.PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DISCOUNT_PRICE, C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 G.FILE_NM_100, G.GOODS_TYPE
								FROM TBL_CART C, TBL_GOODS G 
							 WHERE C.GOODS_NO = G.GOODS_NO 
								 AND C.CART_NO = '$cart_no' ";


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


	function updateCart($db, $sale_price, $qty, $discount_price, $cart_no) {

		$query="UPDATE TBL_CART SET 
									SALE_PRICE = '$sale_price',
									QTY	= '$qty',
									DISCOUNT_PRICE = '$discount_price'
							 WHERE CART_NO = '$cart_no' ";

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

	function deleteCart($db, $cart_no) {

		$query="DELETE FROM TBL_CART WHERE CART_NO = '$cart_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function quickCartNo($db, $reserve_no){

		$query ="SELECT CART_NO FROM TBL_CART WHERE DEL_TF = 'N' AND USE_TF = 'Y' ";

		if ($reserve_no <> "") {
			$query .= " AND RESERVE_NO = '".$reserve_no."' ";
		} else {
			$query .= " AND RESERVE_NO = 'X' ";
		}
		
		$query .= " ORDER BY REG_DATE DESC LIMIT 1 ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
?>