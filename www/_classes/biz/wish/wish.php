<?
	# =============================================================================
	# File Name    : wish.php
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
CREATE TABLE IF NOT EXISTS TBL_WISH (
	WISH_NO int(7) unsigned NOT NULL auto_increment,
	MEM_NO int(11) unsigned NOT NULL default '0' COMMENT '회원 번호',
	GOODS_NO int(11) unsigned NOT NULL default '0' COMMENT '상품 일련번호',
	GOODS_CODE varchar(10) NOT NULL default '' COMMENT '상품 코드',
	GOODS_NAME varchar(100) NOT NULL default '' COMMENT '상품명',
	GOODS_SUB_NAME varchar(100) NOT NULL default '' COMMENT '상품명',
	GOODS_OPTION_01 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_02 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_03 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_04 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_NM_01 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_NM_02 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_NM_03 varchar(100) NOT NULL default '' COMMENT '옵션',
	GOODS_OPTION_NM_04 varchar(100) NOT NULL default '' COMMENT '옵션',
	CATE_01 varchar(50) default NULL COMMENT '임시	1',
	CATE_02 varchar(50) default NULL,
	CATE_03 varchar(50) default NULL,
	CATE_04 varchar(50) default NULL,
	USE_TF char(1) NOT NULL default 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
	DEL_TF char(1) NOT NULL default 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
	REG_ADM int(11) unsigned default NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE datetime default NULL COMMENT '등록일',
	DEL_ADM int(11) unsigned default NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE datetime default NULL COMMENT '삭제일',
	PRIMARY KEY  (WISH_NO)
) ENGINE=MyISAM  DEFAULT CHARSET=euckr COMMENT='찜하기 마스터'

	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================
	/*
	WISH_NO, MEM_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME,
	GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03,
	GOODS_OPTION_04, GOODS_OPTION_NM_01, GOODS_OPTION_NM_02,
	GOODS_OPTION_NM_03, GOODS_OPTION_NM_04, CATE_01, CATE_02,
	CATE_03, CATE_04, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
	*/

	function listWish($db, $mem_no, $use_tf, $del_tf) {

		$query = "SELECT C.WISH_NO, C.MEM_NO, C.GOODS_NO, C.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, C.QTY, 
										 C.GOODS_OPTION_01, C.GOODS_OPTION_02, C.GOODS_OPTION_03,
										 C.GOODS_OPTION_04, C.GOODS_OPTION_NM_01, C.GOODS_OPTION_NM_02,
										 C.GOODS_OPTION_NM_03, C.GOODS_OPTION_NM_04, C.CATE_01, C.CATE_02,
										 C.CATE_03, C.CATE_04, G.PRICE, G.SALE_PRICE, G.EXTRA_PRICE, C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE,
										 G.FILE_NM_100, G.GOODS_TYPE
								FROM TBL_WISH C, TBL_GOODS G WHERE C.GOODS_NO = G.GOODS_NO ";

		if ($mem_no <> "") {
			$query .= " AND C.MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND C.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND C.DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY C.WISH_NO DESC ";

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

	function totalCntWish($db, $mem_no, $use_tf, $del_tf){

		$query ="SELECT COUNT(*) CNT FROM TBL_WISH WHERE 1 = 1 ";

		if ($mem_no <> "") {
			$query .= " AND MEM_NO = '".$mem_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		#echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertWish($db, $mem_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $goods_option_01, $goods_option_02, $goods_option_03,$goods_option_04, $goods_option_nm_01, $goods_option_nm_02,$goods_option_nm_03, $goods_option_nm_04, $cate_01, $cate_02, $cate_03, $cate_04, $use_tf, $reg_adm) {
		
		$query = "SELECT COUNT(GOODS_NO) AS CNT 
								FROM TBL_WISH 
							 WHERE MEM_NO = '$mem_no' 
								 AND GOODS_NO = '$goods_no' 
								 AND GOODS_OPTION_01 = '$goods_option_01' 
								 AND GOODS_OPTION_02 = '$goods_option_02' 
								 AND GOODS_OPTION_03 = '$goods_option_03' 
								 AND GOODS_OPTION_04 = '$goods_option_04' 
								 ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$cnt  = $rows[0];
		
		if ($cnt == 0) {

			if ($qty == "") $qty = 1;

			$query="INSERT INTO TBL_WISH (MEM_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY,
																		GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03,
																		GOODS_OPTION_04, GOODS_OPTION_NM_01, GOODS_OPTION_NM_02,
																		GOODS_OPTION_NM_03, GOODS_OPTION_NM_04, CATE_01, CATE_02,
																		CATE_03, CATE_04, USE_TF, REG_ADM, REG_DATE) 
																 values ('$mem_no', '$goods_no', '$goods_code', '$goods_name', '$goods_sub_name', '$qty',
																		'$goods_option_01', '$goods_option_02', '$goods_option_03',
																		'$goods_option_04', '$goods_option_nm_01', '$goods_option_nm_02',
																		'$goods_option_nm_03', '$goods_option_nm_04', '$cate_01', '$cate_02',
																		'$cate_03', '$cate_04', '$use_tf', '$reg_adm', now()); ";
		
		} else {
			$query = "UPDATE TBL_WISH SET REG_DATE = now()
								 WHERE MEM_NO = '$mem_no' 
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

	function insertCartToWish($db, $cart_no, $mem_no) {


		$query = "SELECT	GOODS_NO, GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03, GOODS_OPTION_04
								FROM TBL_CART 
							 WHERE CART_NO				= '$cart_no' ";

		$result						= mysql_query($query,$db);
		$rows							= mysql_fetch_array($result);
		$goods_no					= $rows[0];
		$goods_option_01	= $rows[1];
		$goods_option_02	= $rows[2];
		$goods_option_03	= $rows[3];
		$goods_option_04	= $rows[4];

		$query = "SELECT COUNT(GOODS_NO) AS CNT 
								FROM TBL_WISH 
							 WHERE MEM_NO = '$mem_no' 
								 AND GOODS_NO = '$goods_no' 
								 AND GOODS_OPTION_01 = '$goods_option_01' 
								 AND GOODS_OPTION_02 = '$goods_option_02' 
								 AND GOODS_OPTION_03 = '$goods_option_03' 
								 AND GOODS_OPTION_04 = '$goods_option_04' 
								 ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$cnt  = $rows[0];

		if ($cnt == 0) {
			$query="INSERT INTO TBL_WISH (
								MEM_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY,
								GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03,
								GOODS_OPTION_04, GOODS_OPTION_NM_01, GOODS_OPTION_NM_02,
								GOODS_OPTION_NM_03, GOODS_OPTION_NM_04, CATE_01, CATE_02,
								CATE_03, CATE_04, USE_TF, REG_ADM, REG_DATE
							)
							SELECT '$mem_no', GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, QTY,
											GOODS_OPTION_01, GOODS_OPTION_02, GOODS_OPTION_03,
											GOODS_OPTION_04, GOODS_OPTION_NM_01, GOODS_OPTION_NM_02,
											GOODS_OPTION_NM_03, GOODS_OPTION_NM_04, CATE_01, CATE_02,
											CATE_03, CATE_04, USE_TF, 'WEB', now()
								FROM TBL_CART 
							 WHERE CART_NO				= '$cart_no' ";

		} else {
			$query = "UPDATE TBL_WISH SET REG_DATE = now()
								 WHERE MEM_NO = '$mem_no' 
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

	function updateWish($db, $qty, $wish_no) {

		$query="UPDATE TBL_WISH SET 
									QTY								= '$qty'
							 WHERE WISH_NO = '$wish_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteWish($db, $wish_no) {

		$query="DELETE FROM TBL_WISH 
											WHERE WISH_NO = '$wish_no' ";

//		$query="UPDATE TBL_CART SET 
//														DEL_TF				= 'Y',
//														DEL_ADM			= '$del_adm',
//														DEL_DATE			= now()
//											WHERE CART_NO = '$cart_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

?>