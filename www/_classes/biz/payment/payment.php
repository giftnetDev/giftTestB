<?

	# =============================================================================
	# File Name    : payment.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.12.29
	# Modify Date  : 
	#	Copyright : Copyright @minumsa Corp. All Rights Reserved.
	# =============================================================================

	/*
	CREATE TABLE IF NOT EXISTS `TBL_PAYMENT` (
	PAY_NO							int(11) unsigned NOT NULL auto_increment	COMMENT '지불 일련번호',
	PAY_TYPE						varchar(10) NOT NULL default ''						COMMENT '지불 구분',
	PAY_STATE						varchar(10) NOT NULL default ''						COMMENT '지불 상태',
	RESERVE_NO					varchar(30) NOT NULL											COMMENT '예약 번호',
	MEM_NO							int(11) unsigned NOT NULL									COMMENT '회원 번호',
	MEM_TYPE						varchar(10) NOT NULL default ''						COMMENT '회원 형태',
	PAY_REASON					varchar(16) NOT NULL default ''						COMMENT '지불 명세',
	CMS_AMOUNT					int(11) unsigned NOT NULL default '0'			COMMENT 'CMS 금액',
	CMS_CASU						int(11) unsigned NOT NULL default '0'			COMMENT 'CMS 차수',
	CMS_PAY_BANK				varchar(50) NOT NULL default ''						COMMENT 'CMS 등록은행',
	CMS_PAY_ACCOUNT			varchar(50) NOT NULL default ''						COMMENT 'CMS 등록은행 게좌',
	CMS_DEPOSITOR				varchar(50) NOT NULL default ''						COMMENT 'CMS 예금주',
	BANK_AMOUNT					int(11) unsigned NOT NULL default '0'			COMMENT '무통장 금액',
	BANK_PAY_ACCOUNT		varchar(50) NOT NULL default ''						COMMENT '무통장 은행 게좌',
	BANK_PAY_DATE				varchar(12) NOT NULL default ''						COMMENT '무통장 입금일',
	CASH_RECEIPT				varchar(20) NOT NULL default ''						COMMENT '현금영수증',
	CASH_RECEIPT_PHONE	varchar(20) NOT NULL default ''						COMMENT '현금영수증 휴대번호',
	CASH_RECEIPT_STATE	varchar(20) NOT NULL default ''						COMMENT '현금영수증 상태',
	CARD_AMOUNT					int(11) unsigned NOT NULL default '0'			COMMENT 'CARD 금액',
	CARD_NAME						varchar(30) NOT NULL default ''						COMMENT '신용 카드 이름',
	PGBANK_AMOUNT				int(11) unsigned NOT NULL default '0'			COMMENT 'PGBANK 금액',
	PGBANK_NAME					varchar(30) NOT NULL default ''						COMMENT 'PGBANK 이름',
	CARD_CODE						varchar(30) NOT NULL default ''						COMMENT '신용 카드 코드',
	CARD_ISSCODE				varchar(30) NOT NULL default ''						COMMENT '신용 카드 코드',
	CARD_APPR_NO				varchar(30) NOT NULL default ''						COMMENT '신용 카드 승인 번호',
	CARD_APPR_DM				varchar(30) NOT NULL default ''						COMMENT '신용 카드 승인 일',
	CARD_MSG						varchar(100) NOT NULL default ''					COMMENT '신용 카드 메시지',
	CARD_VANTR					varchar(30) NOT NULL default ''						COMMENT '트렌젝션 아이디',
	CARD_NUM						varchar(30) NOT NULL default ''						COMMENT '신용카드 번호',
	REQ_DATE						datetime default NULL											COMMENT '입금 신청일',
	CANCEL_DATE					datetime default NULL											COMMENT '입금 취소일',
	USE_TF							char(1) NOT NULL default 'Y'							COMMENT '사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1) NOT NULL default 'N'							COMMENT '삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11) unsigned default NULL							COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime default NULL											COMMENT '등록일',
	UP_ADM							int(11) unsigned default NULL							COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime default NULL											COMMENT '수정일',
	DEL_ADM							int(11) unsigned default NULL							COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime default NULL											COMMENT '삭제일',
	PRIMARY KEY  (`PAY_NO`)
	)

CREATE TABLE IF NOT EXISTS `TBL_REFUND` (
  `REFUND_NO` int(11) unsigned NOT NULL auto_increment COMMENT '지불 일련번호',
  `REFUND_TYPE` varchar(10) NOT NULL default '' COMMENT '지불 구분',
  `REFUND_STATE` varchar(10) NOT NULL default '' COMMENT '지불 상태',
  `ON_UID` varchar(32) NOT NULL,
  `RESERVE_NO` varchar(30) NOT NULL COMMENT '예약 번호',
  `MEM_NM` varchar(30) default NULL,
  `CP_NO` int(11) unsigned NOT NULL default '0',
  `CMS_DEPOSITOR` varchar(50) NOT NULL default '' COMMENT 'CMS 예금주',
  `BANK_AMOUNT` int(11) unsigned NOT NULL default '0' COMMENT '무통장 금액',
  `BANK_PAY_ACCOUNT` varchar(50) NOT NULL default '' COMMENT '무통장 은행 게좌',
  `BANK_PAY_DATE` varchar(12) NOT NULL default '' COMMENT '무통장 입금일',
  `REQ_DATE` datetime default NULL COMMENT '입금 신청일',
  `PAID_DATE` datetime default NULL,
  `CANCEL_DATE` datetime default NULL COMMENT '입금 취소일',
  `USE_TF` char(1) NOT NULL default 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  `DEL_TF` char(1) NOT NULL default 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  `REG_ADM` varchar(30) default NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  `REG_DATE` datetime default NULL COMMENT '등록일',
  `UP_ADM` varchar(30) default NULL COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
  `UP_DATE` datetime default NULL COMMENT '수정일',
  `DEL_ADM` varchar(30) default NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  `DEL_DATE` datetime default NULL COMMENT '삭제일',
  PRIMARY KEY  (`REFUND_NO`)
) ENGINE=MyISAM  DEFAULT CHARSET=euckr COMMENT='환불 마스터' AUTO_INCREMENT=789 ;


	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listPayment($db, $start_date, $end_date, $pay_type, $cp_type, $pay_state, $account_bank, $reserve_no, $mem_no, $pay_reason, $cash_receipt_state, $use_tf, $del_tf, $order_field, $order_str, $search_field, $search_str, $condition, $nPage, $nRowCount) {

		$total_cnt = totalCntPayment($db, $start_date, $end_date, $pay_type, $cp_type, $pay_state, $account_bank, $reserve_no, $mem_no, $pay_reason, $cash_receipt_state, $use_tf, $del_tf, $search_field, $search_str, $condition);
		
		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, P.PAY_NO, P.PAY_TYPE, P.PAY_EXT, P.PAY_STATE, P.RESERVE_NO, P.MEM_NO, P.MEM_TYPE, P.PAY_REASON, 
										 P.CMS_AMOUNT, P.CMS_CASU, P.CMS_PAY_BANK, P.CMS_PAY_ACCOUNT, P.CMS_DEPOSITOR, P.BANK_AMOUNT, P.BANK_PAY_ACCOUNT, P.BANK_PAY_DATE, P.CASH_RECEIPT, 
										 P.CASH_RECEIPT_PHONE, P.CASH_RECEIPT_STATE, P.CARD_AMOUNT, P.CARD_NAME, P.PGBANK_AMOUNT, P.PGBANK_NAME, P.CARD_CODE, P.CARD_ISSCODE, P.CARD_APPR_NO, 
										 P.CARD_APPR_DM, P.CARD_MSG, P.CARD_VANTR, P.CARD_NUM, P.REQ_DATE, P.PAID_DATE, P.CANCEL_DATE,
										 P.USE_TF, P.DEL_TF, P.REG_ADM, P.REG_DATE, P.UP_ADM, P.UP_DATE, P.DEL_ADM, P.DEL_DATE,
										 P.MEM_NM, O.CP_NO,
										 (O.TOTAL_SALE_PRICE + O.TOTAL_EXTRA_PRICE + O.TOTAL_DELIVERY_PRICE) AS TOTAL_PRICE, O.O_MEM_NM, O.O_PHONE, O.O_HPHONE, O.ORDER_DATE
								FROM TBL_PAYMENT P, TBL_ORDER O 
							 WHERE P.RESERVE_NO = O.RESERVE_NO 
								 AND P.PAY_TYPE <> '' 
								 AND (P.CMS_AMOUNT + P.BANK_AMOUNT + P.PGBANK_AMOUNT + P.CARD_AMOUNT) > 0 ";

		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($pay_type == "PG") {
			$query .= " AND P.PAY_TYPE IN ('CARD','PGBANK') ";
		} else {
		
			if ($pay_type <> "") {
				$query .= " AND P.PAY_TYPE IN ('".$pay_type."') ";
			}
		}
		
		if ($cp_type <> "") {
			$query .= " AND O.CP_NO = '".$cp_type."' ";
		}
		

		if ($pay_state <> "") {
			$query .= " AND P.PAY_STATE IN ('".$pay_state."') ";
		}

		if ($account_bank <> "") {
			$query .= " AND P.BANK_PAY_ACCOUNT  = '".$account_bank."' ";
		}


		if ($reserve_no <> "") {
			$query .= " AND P.RESERVE_NO = '".$reserve_no."' ";
		}


		if ($mem_no <> "") {
			$query .= " AND P.MEM_NO = '".$mem_no."' ";
		}
		
		//echo $pay_reason;

		if ($pay_reason <> "") {
			$query .= " AND P.PAY_REASON = '".$pay_reason."' ";
		}

		if ($cash_receipt_state <> "") {
			$query .= " AND P.CASH_RECEIPT_STATE = '".$cash_receipt_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND P.DEL_TF = '".$del_tf."' ";
		}

		if ($condition <> "") {
			$query .= $condition;
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		

		if ($order_field == "") 
			$order_field = "P.PAY_NO";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;


		//$query .= " ORDER BY P.PAY_NO desc limit ".$offset.", ".$nRowCount;
		
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

	function totalCntPayment($db, $start_date, $end_date, $pay_type, $cp_type, $pay_state, $account_bank, $reserve_no, $mem_no, $pay_reason, $cash_receipt_state, $use_tf, $del_tf, $search_field, $search_str, $condition){

		$query ="SELECT COUNT(*) CNT 
							 FROM TBL_PAYMENT P, TBL_ORDER O 
							WHERE P.RESERVE_NO = O.RESERVE_NO 
								AND P.PAY_TYPE <> '' AND (P.CMS_AMOUNT + P.BANK_AMOUNT + P.PGBANK_AMOUNT + P.CARD_AMOUNT) > 0 ";


		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($pay_type == "PG") {
			$query .= " AND P.PAY_TYPE IN ('CARD','PGBANK') ";
		} else {
		
			if ($pay_type <> "") {
				$query .= " AND P.PAY_TYPE IN ('".$pay_type."') ";
			}
		}
		
		if ($cp_type <> "") {
			$query .= " AND O.CP_NO = '".$cp_type."' ";
		}
		

		if ($pay_state <> "") {
			$query .= " AND P.PAY_STATE IN ('".$pay_state."') ";
		}

		if ($account_bank <> "") {
			$query .= " AND P.BANK_PAY_ACCOUNT  = '".$account_bank."' ";
		}


		if ($reserve_no <> "") {
			$query .= " AND P.RESERVE_NO = '".$reserve_no."' ";
		}


		if ($mem_no <> "") {
			$query .= " AND P.MEM_NO = '".$mem_no."' ";
		}
		
		//echo $pay_reason;

		if ($pay_reason <> "") {
			$query .= " AND P.PAY_REASON = '".$pay_reason."' ";
		}

		if ($cash_receipt_state <> "") {
			$query .= " AND P.CASH_RECEIPT_STATE = '".$cash_receipt_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND P.DEL_TF = '".$del_tf."' ";
		}
		
		if ($condition <> "") {
			$query .= $condition;
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
	
	function insertPayment($db, $pay_type, $pay_state, $on_uid, $reserve_no, $mem_no, $mem_nm, $mem_type, $pay_reason, $cms_amount, $cms_casu, $cms_pay_bank, $cms_pay_account, $cms_depositor, $bank_amount, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $cash_receipt_state, $card_amount, $card_name, $pgbank_amount, $pgbank_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $req_date, $use_tf, $reg_adm) {
		

		$query ="SELECT IFNULL(MAX(PAY_NO),0) + 1 AS MAX_NO FROM TBL_PAYMENT ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		$new_pay_no = $rows[0];

		$query="INSERT INTO TBL_PAYMENT (PAY_NO, PAY_TYPE, PAY_STATE, ON_UID, RESERVE_NO, MEM_NO, MEM_NM, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, CARD_CODE, CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, USE_TF, REG_ADM, REG_DATE) 
						values ('$new_pay_no', '$pay_type', '$pay_state', '$on_uid', '$reserve_no', '$mem_no', '$mem_nm', '$mem_type', '$pay_reason', '$cms_amount', '$cms_casu', '$cms_pay_bank', 
										'$cms_pay_account', '$cms_depositor', '$bank_amount', '$bank_pay_account', '$bank_pay_date', '$cash_receipt', '$cash_receipt_phone', 
										'$cash_receipt_state', '$card_amount', '$card_name', '$pgbank_amount', '$pgbank_name', '$card_code', '$card_isscode', '$card_appr_no', 
										'$card_appr_dm', '$card_msg', '$card_vantr', '$card_num', '$req_date', '$use_tf', '$reg_adm', now()); ";
		


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_pay_no;
		}
	}

	function insertPaymentExt($db, $pay_type, $pay_ext, $pay_state, $reserve_no, $mem_no, $mem_nm, $mem_type, $pay_reason, $cms_amount, $cms_casu, $cms_pay_bank, $cms_pay_account, $cms_depositor, $bank_amount, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $cash_receipt_state, $card_amount, $card_name, $pgbank_amount, $pgbank_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $req_date, $use_tf, $reg_adm) {
		

		$query ="SELECT IFNULL(MAX(PAY_NO),0) + 1 AS MAX_NO FROM TBL_PAYMENT ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		$new_pay_no = $rows[0];

		$query="INSERT INTO TBL_PAYMENT (PAY_NO, PAY_TYPE, PAY_EXT, PAY_STATE, RESERVE_NO, MEM_NO, MEM_NM, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, CARD_CODE, CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, USE_TF, REG_ADM, REG_DATE) 
						values ('$new_pay_no', '$pay_type', '$pay_ext', '$pay_state', '$reserve_no', '$mem_no', '$mem_nm', '$mem_type', '$pay_reason', '$cms_amount', '$cms_casu', '$cms_pay_bank', 
										'$cms_pay_account', '$cms_depositor', '$bank_amount', '$bank_pay_account', '$bank_pay_date', '$cash_receipt', '$cash_receipt_phone', 
										'$cash_receipt_state', '$card_amount', '$card_name', '$pgbank_amount', '$pgbank_name', '$card_code', '$card_isscode', '$card_appr_no', 
										'$card_appr_dm', '$card_msg', '$card_vantr', '$card_num', '$req_date', '$use_tf', '$reg_adm', now()); ";
		


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_pay_no;
		}
	}

	function selectPayment($db, $pay_no) {

		$query = "SELECT PAY_NO, PAY_TYPE, PAY_EXT, PAY_STATE, RESERVE_NO, MEM_NO, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CASH_RECEIPT_DATE, CASH_VANTR, CASH_MSG, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, CARD_CODE, 
										 CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, PAID_DATE, CANCEL_DATE, CANCEL_MSG,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT NM FROM TBL_SUPPORT WHERE P.RESERVE_NO = RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N'  ) AS NM
								FROM TBL_PAYMENT P WHERE PAY_NO = '$pay_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getMemberPayment($db, $mem_no) {

		$query = "SELECT PAY_NO, PAY_TYPE, PAY_EXT, PAY_STATE, RESERVE_NO, MEM_NO, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CASH_RECEIPT_DATE, CASH_VANTR, CASH_MSG, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, CARD_CODE, 
										 CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, PAID_DATE, CANCEL_DATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_PAYMENT WHERE MEM_NO = '$mem_no' AND PAY_REASON IN ('회원가입','회원정보수정') AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO ASC";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getReservationPayment($db, $reserve_no) {

		$query = "SELECT PAY_NO, PAY_TYPE, PAY_EXT, PAY_STATE, RESERVE_NO, MEM_NO, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CASH_RECEIPT_DATE, CASH_VANTR, CASH_MSG, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, 
										 CARD_CODE, CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, PAID_DATE, CANCEL_DATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_PAYMENT WHERE RESERVE_NO = '$reserve_no' AND PAY_REASON IN ('함양예약','함양추가입금') AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO ASC";
		
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


	function deleteMemberPayment($db, $mem_no, $del_adm) {

		$query="UPDATE TBL_PAYMENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE MEM_NO = '$mem_no' AND PAY_REASON IN ('회원가입','회원정보수정') AND PAY_TYPE <> 'CMS' AND PAY_STATE = '0' ";
											 
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteMemberCmsPayment($db, $mem_no, $del_adm) {

		$query="UPDATE TBL_PAYMENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE MEM_NO = '$mem_no' AND PAY_REASON IN ('회원가입','회원정보수정') AND PAY_STATE = '0' ";
											 
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updatePayment($db, $pay_type, $pay_state, $reserve_no, $mem_no, $mem_type, $pay_reason, $cms_amount, $cms_casu, $cms_pay_bank, $cms_pay_account, $cms_depositor, $bank_amount, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $cash_receipt_state, $card_amount, $card_name, $pgbank_amount, $pgbank_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $req_date, $cancel_date, $use_tf, $up_adm, $pay_no) {

		$query="UPDATE TBL_PAYMENT SET 
													PAY_TYPE						= '$pay_type',
													PAY_STATE						= '$pay_state',
													RESERVE_NO					= '$reserve_no',
													MEM_NO							= '$mem_no',
													MEM_TYPE						= '$mem_type',
													PAY_REASON					= '$pay_reason',
													CMS_AMOUNT					= '$cms_amount',
													CMS_CASU						= '$cms_casu',
													CMS_PAY_BANK				= '$cms_pay_bank',
													CMS_PAY_ACCOUNT			= '$cms_pay_account',
													CMS_DEPOSITOR				= '$cms_depositor',
													BANK_AMOUNT					= '$bank_amount',
													BANK_PAY_ACCOUNT		= '$bank_pay_account',
													BANK_PAY_DATE				= '$bank_pay_date',
													CASH_RECEIPT				= '$cash_receipt',
													CASH_RECEIPT_PHONE	= '$cash_receipt_phone',
													CASH_RECEIPT_STATE	= '$cash_receipt_state',
													CARD_AMOUNT					= '$card_amount',
													CARD_NAME						= '$card_name',
													PGBANK_AMOUNT				= '$pgbank_amount',
													PGBANK_NAME					= '$pgbank_name',
													CARD_CODE						= '$card_code',
													CARD_ISSCODE				= '$card_isscode',
													CARD_APPR_NO				= '$card_appr_no',
													CARD_APPR_DM				= '$card_appr_dm',
													CARD_MSG						= '$card_msg',
													CARD_VANTR					= '$card_vantr',
													CARD_NUM						= '$card_num',
													REQ_DATE						= '$req_date',
													CANCEL_DATE					= '$cancel_date',
													USE_TF							= '$use_tf',
													UP_ADM							=	'$up_adm',
													UP_DATE							=	now()
											 WHERE PAY_NO						= '$pay_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateCmsPayment($db, $cms_pay_bank, $cms_pay_account, $cms_depositor, $cash_receipt, $cash_receipt_phone, $mem_no) {

		$query="UPDATE TBL_PAYMENT SET 
													CMS_PAY_BANK				= '$cms_pay_bank',
													CMS_PAY_ACCOUNT			= '$cms_pay_account',
													CMS_DEPOSITOR				= '$cms_depositor',
													CASH_RECEIPT				= '$cash_receipt',
													CASH_RECEIPT_PHONE	= '$cash_receipt_phone',
													UP_DATE							=	now()
 											 WHERE MEM_NO						= '$mem_no' AND PAY_STATE = '0' AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updatePaymentState($db, $pay_no, $pay_reason, $reserve_no, $pay_state, $up_adm) {
		
		if ($pay_state == 0) {

			$order_state	 = "0";		// 물품구매
			
			$str_finish_date		= " FINISH_DATE = NULL, ";
			$str_delivery_date	= "	DELIVERY_DATE = NULL, ";
			$str_pay_date				= " PAY_DATE = NULL, ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
			
			$query="UPDATE TBL_PAYMENT SET 
														PAY_STATE						= '$pay_state',
														PAID_DATE						= NULL,
														CANCEL_DATE					= NULL,
														UP_ADM							=	'$up_adm',
														UP_DATE							=	now()
												 WHERE PAY_NO						= '$pay_no' ";

		}

		if ($pay_state == 1) {

			$order_state	 = "1";

			$str_finish_date		= "";
			$str_delivery_date	= "";
			$str_pay_date				= " PAY_DATE = now() , ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";

			$query="UPDATE TBL_PAYMENT SET 
														PAY_STATE						= '$pay_state',
														PAID_DATE						= now(),
														CANCEL_DATE					= NULL,
														UP_ADM							=	'$up_adm',
														UP_DATE							=	now()
 												 WHERE PAY_NO						= '$pay_no' ";
		}

		if ($pay_state == 2) {

			$order_state	 = "5";

			$str_finish_date		= "";
			$str_delivery_date	= "";
			$str_pay_date				= "";
			$str_cancel_date		= "	CANCEL_DATE = now() ";

			$query="UPDATE TBL_PAYMENT SET 
														PAY_STATE						= '$pay_state',
														CANCEL_DATE					= now(),
														UP_DATE							=	now()
 												 WHERE PAY_NO						= '$pay_no' ";
		}


		if ($pay_reason == "물품구매") {
				
			$query2 = "UPDATE TBL_ORDER_GOODS SET 
												ORDER_STATE		= '$pay_state', ";

			$query2 .= $str_finish_date;
			$query2 .= $str_delivery_date;
			$query2 .= $str_pay_date;
			$query2 .= $str_cancel_date;
			
			if ($pay_state == 1) {
				$query2 .=	" WHERE RESERVE_NO		= '$reserve_no' AND ORDER_STATE = '0' ";
			} else {
				$query2 .=	" WHERE RESERVE_NO		= '$reserve_no' AND ORDER_STATE = '1' ";
			}
			
			//echo $query2."<br>";
			mysql_query($query2,$db);

			$query3 = "SELECT ORDER_STATE, GOODS_NO, QTY FROM TBL_ORDER_GOODS WHERE RESERVE_NO	= '$reserve_no' AND DEL_TF = 'N' AND USE_TF = 'Y' ";
			
			$result = mysql_query($query3,$db);
			$total  = mysql_affected_rows();
			
			$tmp_order_state = "";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
				$RS_ORDER_STATE			= Trim($row[0]);
				$RS_GOODS_NO				= Trim($row[1]);
				$RS_QTY							= Trim($row[2]);

				if (($pay_state == 0) || ($pay_state == 2)) {
					$query_st = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT + $RS_QTY WHERE GOODS_NO = '$RS_GOODS_NO'";
				}
				
				if ($pay_state == 1) {
					$query_st = "UPDATE TBL_GOODS SET STOCK_CNT = STOCK_CNT - $RS_QTY WHERE GOODS_NO = '$RS_GOODS_NO'";
				}
				
				mysql_query($query_st,$db);

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
			$query4 .= $str_pay_date;
			$query4 .= $str_cancel_date;
			
			$query4 .=	" WHERE RESERVE_NO		= '$reserve_no' ";
			mysql_query($query4,$db);


		}

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateBankPayment($db, $bank_pay_bank, $bank_pay_account, $bank_pay_date, $cash_receipt, $cash_receipt_phone, $mem_no) {

		$query="UPDATE TBL_PAYMENT SET 
													PAY_TYPE	='BANK',
													BANK_AMOUNT					= '$bank_pay_account',
													BANK_PAY_ACCOUNT		= '$bank_pay_bank',
													BANK_PAY_DATE				= '$bank_pay_date',
													CASH_RECEIPT				= '$cash_receipt',
													CASH_RECEIPT_PHONE	= '$cash_receipt_phone',
													CASH_RECEIPT_STATE= '0',
													UP_DATE							=	now()
 											 WHERE MEM_NO						= '$mem_no' AND PAY_STATE = '0' AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateCardPaidPayment($db, $reserve_no, $card_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $card_auth, $pay_no) {
	

		if ($card_auth == "O") { 

			$query="UPDATE TBL_PAYMENT SET 
													CARD_NAME			= '$card_name',
													CARD_CODE			= '$card_code',
													CARD_ISSCODE	= '$card_isscode',
													CARD_APPR_NO	= '$card_appr_no',
													CARD_APPR_DM	= '$card_appr_dm',
													CARD_MSG			= '$card_msg',
													CARD_VANTR		= '$card_vantr',
													CARD_NUM			= '$card_num',
													CARD_AUTH			= '$card_auth',
													PAY_STATE			= '1',
													PAID_DATE			=	now(),
													UP_DATE				=	now()
 										WHERE PAY_NO				= '$pay_no'
											AND RESERVE_NO		= '$reserve_no'
											AND PAY_STATE			= '0' 
											AND DEL_TF				= 'N' 
											AND USE_TF				= 'Y' ";
		} else {
			$query="UPDATE TBL_PAYMENT SET 
													CARD_NAME			= '$card_name',
													CARD_CODE			= '$card_code',
													CARD_ISSCODE	= '$card_isscode',
													CARD_APPR_NO	= '$card_appr_no',
													CARD_APPR_DM	= '$card_appr_dm',
													CARD_MSG			= '$card_msg',
													CARD_VANTR		= '$card_vantr',
													CARD_NUM			= '$card_num',
													CARD_AUTH			= '$card_auth',
													UP_DATE				=	now()
 										WHERE PAY_NO				= '$pay_no'
											AND RESERVE_NO		= '$reserve_no'
											AND PAY_STATE			= '0' 
											AND DEL_TF				= 'N' 
											AND USE_TF				= 'Y' ";
		}

		echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updatePgbankPaidPayment($db, $reserve_no, $card_name, $card_code, $card_isscode, $card_appr_no, $card_appr_dm, $card_msg, $card_vantr, $card_num, $card_auth, $pay_no) {

		if ($card_auth == "O") { 

			$query="UPDATE TBL_PAYMENT SET 
													PGBANK_NAME		= '$card_name',
													CARD_CODE			= '$card_code',
													CARD_ISSCODE	= '$card_isscode',
													CARD_APPR_NO	= '$card_appr_no',
													CARD_APPR_DM	= '$card_appr_dm',
													CARD_MSG			= '$card_msg',
													CARD_VANTR		= '$card_vantr',
													CARD_NUM			= '$card_num',
													CARD_AUTH			= '$card_auth',
													PAY_STATE			= '1',
													PAID_DATE			=	now(),
													UP_DATE				=	now()
 										WHERE PAY_NO				= '$pay_no'
											AND RESERVE_NO		= '$reserve_no'
											AND PAY_STATE			= '0' 
											AND DEL_TF				= 'N' 
											AND USE_TF				= 'Y' ";
		} else {
		
			$query="UPDATE TBL_PAYMENT SET 
													PGBANK_NAME		= '$card_name',
													CARD_CODE			= '$card_code',
													CARD_ISSCODE	= '$card_isscode',
													CARD_APPR_NO	= '$card_appr_no',
													CARD_APPR_DM	= '$card_appr_dm',
													CARD_MSG			= '$card_msg',
													CARD_VANTR		= '$card_vantr',
													CARD_NUM			= '$card_num',
													CARD_AUTH			= '$card_auth',
													UP_DATE				=	now()
 										WHERE PAY_NO				= '$pay_no'
											AND RESERVE_NO		= '$reserve_no'
											AND PAY_STATE			= '0' 
											AND DEL_TF				= 'N' 
											AND USE_TF				= 'Y' ";

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

	function deletePayment($db, $pay_no, $del_adm) {


		$query="UPDATE TBL_PAYMENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE PAY_NO				= '$pay_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateReceiptPayment($db, $pay_no, $cash_vantr, $cash_receipt_date, $cash_msg, $cash_receipt_state ) {

		$query="UPDATE TBL_PAYMENT SET 
													CASH_RECEIPT_STATE	= '$cash_receipt_state',
													CASH_VANTR					=	'$cash_vantr',
 													CASH_RECEIPT_DATE		=	'$cash_receipt_date',
 													CASH_MSG						=	'$cash_msg',
 													CASH_RECEIPT_DATE		=	now()
 											 WHERE PAY_NO						= '$pay_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listManagerPayment($db, $reserve_no, $use_tf, $del_tf) {

		$query = "SELECT PAY_NO, PAY_TYPE, PAY_EXT, PAY_STATE, RESERVE_NO, MEM_NO, MEM_TYPE, PAY_REASON, 
										 CMS_AMOUNT, CMS_CASU, CMS_PAY_BANK, CMS_PAY_ACCOUNT, CMS_DEPOSITOR, BANK_AMOUNT, BANK_PAY_ACCOUNT, BANK_PAY_DATE, CASH_RECEIPT, 
										 CASH_RECEIPT_PHONE, CASH_RECEIPT_STATE, CASH_RECEIPT_DATE, CASH_VANTR, CASH_MSG, CARD_AMOUNT, CARD_NAME, PGBANK_AMOUNT, PGBANK_NAME, CARD_CODE, 
										 CARD_ISSCODE, CARD_APPR_NO, 
										 CARD_APPR_DM, CARD_MSG, CARD_VANTR, CARD_NUM, REQ_DATE, PAID_DATE, CANCEL_DATE, CANCEL_MSG,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_PAYMENT WHERE RESERVE_NO = '$reserve_no' ";

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function listRefund($db, $start_date, $end_date, $refund_type, $cp_no, $refund_state, $reserve_no, $use_tf, $del_tf, $order_field, $order_str, $search_field, $search_str, $condition, $nPage, $nRowCount) {

		$total_cnt = totalCntRefund($db, $start_date, $end_date, $refund_type, $cp_no, $refund_state, $reserve_no, $use_tf, $del_tf, $search_field, $search_str, $condition);
		
		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, P.REFUND_NO, P.REFUND_TYPE, P.REFUND_STATE, P.ORDER_SEQ, P.RESERVE_NO, 
										 P.CMS_DEPOSITOR, P.BANK_AMOUNT, P.BANK_NAME, P.BANK_PAY_ACCOUNT, P.BANK_PAY_DATE, 
										 P.REQ_DATE, P.PAID_DATE, P.CANCEL_DATE,
										 P.USE_TF, P.DEL_TF, P.REG_ADM, P.REG_DATE, P.UP_ADM, P.UP_DATE, P.DEL_ADM, P.DEL_DATE,
										 P.MEM_NM, O.CP_NO, O.O_MEM_NM, O.R_MEM_NM
								FROM TBL_REFUND P, TBL_ORDER O 
							 WHERE P.RESERVE_NO = O.RESERVE_NO 
								 AND P.REFUND_TYPE <> '' 
								 AND P.BANK_AMOUNT > 0 ";

		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($refund_type <> "") {
			$query .= " AND P.REFUND_TYPE = '".$refund_type."' ";
		}
		
		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		}
		

		if ($refund_state <> "") {
			$query .= " AND P.REFUND_STATE IN ('".$refund_state."') ";
		}

		if ($reserve_no <> "") {
			$query .= " AND P.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND P.DEL_TF = '".$del_tf."' ";
		}
		
		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND P.DEL_TF = '".$del_tf."' ";
		}

		if ($condition <> "") {
			$query .= $condition;
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		

		if ($order_field == "") 
			$order_field = "P.REFUND_NO";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;


		//$query .= " ORDER BY P.PAY_NO desc limit ".$offset.", ".$nRowCount;
		
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

	function totalCntRefund($db, $start_date, $end_date, $refund_type, $cp_no, $refund_state, $reserve_no, $use_tf, $del_tf, $search_field, $search_str, $condition){

		$query ="SELECT COUNT(*) CNT 
							 FROM TBL_REFUND P, TBL_ORDER O 
							WHERE P.RESERVE_NO = O.RESERVE_NO 
								AND P.REFUND_TYPE <> '' AND P.BANK_AMOUNT > 0 ";


		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($refund_type <> "") {
			$query .= " AND P.REFUND_TYPE = '".$refund_type."' ";
		}
		
		if ($cp_no <> "") {
			$query .= " AND O.CP_NO = '".$cp_no."' ";
		}
		

		if ($refund_state <> "") {
			$query .= " AND P.REFUND_STATE IN ('".$refund_state."') ";
		}

		if ($reserve_no <> "") {
			$query .= " AND P.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND P.DEL_TF = '".$del_tf."' ";
		}
		
		if ($condition <> "") {
			$query .= $condition;
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

	function insertRefund($db, $refund_type, $refund_state, $order_seq, $on_uid, $reserve_no, $mem_nm, $cp_no, $cms_depositor, $bank_amount,$bank_name, $bank_pay_account, $use_tf, $reg_adm) {
		

		//$query ="SELECT IFNULL(MAX(REFUND_NO),0) + 1 AS MAX_NO FROM TBL_REFUND ";
		//$result = mysql_query($query,$db);
		//$rows   = mysql_fetch_array($result);
		
		//$new_pay_no = $rows[0];

		$query="INSERT INTO TBL_REFUND (REFUND_TYPE, REFUND_STATE, ORDER_SEQ, ON_UID, RESERVE_NO, MEM_NM, 
										 CMS_DEPOSITOR, BANK_AMOUNT, BANK_NAME, BANK_PAY_ACCOUNT, BANK_PAY_DATE, REQ_DATE, USE_TF, REG_ADM, REG_DATE) 
						values ('$refund_type', '$refund_state', '$order_seq,', '$on_uid', '$reserve_no', '$mem_nm',
										'$cms_depositor', '$bank_amount', '$bank_name', '$bank_pay_account', now(),
										 now(), '$use_tf', '$reg_adm', now()); ";
		


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateRefundState($db, $refund_no, $reserve_no, $refund_state, $up_adm) {
		
		if ($refund_state == 0) {

			$str_finish_date		= " PAID_DATE = NULL, ";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";
			
			$query="UPDATE TBL_REFUND SET 
														REFUND_STATE				= '$refund_state',
														PAID_DATE						= NULL,
														CANCEL_DATE					= NULL,
														UP_ADM							=	'$up_adm',
														UP_DATE							=	now()
												 WHERE REFUND_NO				= '$refund_no' ";

		}

		if ($refund_state == 1) {

			$str_finish_date		= "";
			$str_cancel_date		= "	CANCEL_DATE = NULL ";

			$query="UPDATE TBL_REFUND SET 
														REFUND_STATE				= '$refund_state',
														PAID_DATE						= now(),
														CANCEL_DATE					= NULL,
														UP_ADM							=	'$up_adm',
														UP_DATE							=	now()
 										  WHERE REFUND_NO				= '$refund_no' ";
		}

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}



?>