<?

	# =============================================================================
	# File Name    : support.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.25
	# Modify Date  : 
	#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_ACTIVITY
	#=========================================================================================================
	
	/*
	CREATE TABLE IF NOT EXISTS TBL_SUPPORT (
  SUPPORT_NO		int(11) unsigned NOT NULL						COMMENT '후원	일련번호',
  RESERVE_NO		int(11) unsigned NOT NULL						COMMENT '주문	일련번호',
  SUPPORT_TYPE	varchar(10) NOT NULL default ''			COMMENT '후원 구분',
  BIZ_TYPE			varchar(10) NOT NULL default ''			COMMENT '후원사업 구분',
  NM						varchar(30) NOT NULL default ''			COMMENT '후원회원 명',
  MEM_NO				int(11) unsigned NOT NULL						COMMENT '회원명',
  MEM_ID				varchar(16) NOT NULL default ''			COMMENT '회원	ID',
  AMOUT					int(11) NOT NULL										COMMENT '후원금',
  EMAIL					varchar(100) NOT NULL default ''		COMMENT '이메일',
  PHONE					varchar(30) NOT NULL default ''			COMMENT '전화번호',
  HPHONE				varchar(30) NOT NULL default ''			COMMENT '휴대전화번호',
  CPHONE				varchar(30) NOT NULL default ''			COMMENT '회사전화번호',
  ZIPCODE				varchar(10) NOT NULL default ''			COMMENT '우편번호',
  ADDR1					varchar(100) NOT NULL default ''		COMMENT '주소',
  ADDR2					varchar(100) NOT NULL default ''		COMMENT '상세주소',
  CZIPCODE			varchar(10) NOT NULL default ''			COMMENT '회사우편번호',
  CADDR1				varchar(100) NOT NULL default ''		COMMENT '회사주소',
  CADDR2				varchar(100) NOT NULL default ''		COMMENT '회사상세주소',
  COMPANY				varchar(30) NOT NULL default ''			COMMENT '직업',
  POSITION			varchar(60) NOT NULL default ''			COMMENT '소속/직위',
  MEMO					text																COMMENT '기타사항',
  PAY_TYPE			varchar(10) NOT NULL default 'N',
  STATE					varchar(10) NOT NULL default '0',
  USE_TF char(1) NOT NULL default 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  DEL_TF char(1) NOT NULL default 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  REG_ADM int(11) unsigned default NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  REG_DATE datetime default NULL COMMENT '등록일',
  UP_ADM int(11) unsigned default NULL COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
  UP_DATE datetime default NULL COMMENT '수정일',
  DEL_ADM int(11) unsigned default NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  DEL_DATE datetime default NULL COMMENT '삭제일',
  PRIMARY KEY  (SUPPORT_NO)
	) ENGINE=MyISAM  COMMENT='후원 마스터';

	*/


	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#SUPPORT_NO, RESERVE_NO, SUPPORT_TYPE, BIZ_TYPE, NM, MEM_NO, MEM_ID, EMAIL, PHONE, HPHONE, CPHONE, ZIPCODE, ADDR1, 
	#ADDR2, CZIPCODE, CADDR1, CADDR2, COMPANY, POSITION, MEMO, PAY_TYPE, STATE,
	#USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

	function listSupport($db, $support_type, $biz_type, $state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntSupport($db, $support_type, $biz_type, $state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, S.SUPPORT_NO, S.RESERVE_NO, S.SUPPORT_TYPE, S.BIZ_TYPE, S.NM, S.MEM_NO, S.MEM_ID, S.AMOUNT, S.EMAIL, S.PHONE, S.HPHONE, S.CPHONE, S.ZIPCODE, S.ADDR1,
										 S.ADDR2, S.CZIPCODE, S.CADDR1, S.CADDR2, S.COMPANY, S.POSITION, S.MEMO, S.PAY_TYPE, S.STATE,
										 S.USE_TF, S.DEL_TF, S.REG_ADM, S.REG_DATE, S.UP_ADM, S.UP_DATE, S.DEL_ADM, S.DEL_DATE,
										 (SELECT PAY_STATE FROM TBL_PAYMENT WHERE RESERVE_NO = S.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAY_STATE,
										 (SELECT PAID_DATE FROM TBL_PAYMENT WHERE RESERVE_NO = S.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAID_DATE
								FROM TBL_SUPPORT S WHERE 1 = 1 ";

		
		if ($support_type <> "") {
			$query .= " AND S.SUPPORT_TYPE = '".$support_type."' ";
		}

		if ($biz_type <> "") {
			$query .= " AND S.BIZ_TYPE = '".$biz_type."' ";
		}

		if ($state <> "") {
			$query .= " AND S.STATE = '".$state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND S.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND S.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY S.REG_DATE desc limit ".$offset.", ".$nRowCount;

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntSupport($db, $support_type, $biz_type, $state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_SUPPORT S WHERE 1 = 1 ";
		
		if ($support_type <> "") {
			$query .= " AND S.SUPPORT_TYPE = '".$support_type."' ";
		}

		if ($biz_type <> "") {
			$query .= " AND S.BIZ_TYPE = '".$biz_type."' ";
		}

		if ($state <> "") {
			$query .= " AND S.STATE = '".$state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND S.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND S.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function insertSupport($db, $reserve_no, $support_type, $biz_type, $nm, $mem_no, $mem_id, $amount, $email, $phone, $hphone, $cphone, $zipcode, $addr1, $addr2, $czipcode, $caddr1, $caddr2, $company, $position, $memo, $pay_type, $state, $use_tf, $reg_adm) {
		

		$query ="SELECT IFNULL(MAX(SUPPORT_NO),0) AS MAX_NO FROM TBL_SUPPORT ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_no  = ($rows[0] + 1);

		$query="INSERT INTO TBL_SUPPORT (SUPPORT_NO, RESERVE_NO, SUPPORT_TYPE, BIZ_TYPE, NM, MEM_NO, MEM_ID, AMOUNT, EMAIL, PHONE, HPHONE, CPHONE, ZIPCODE, ADDR1, 
																			ADDR2, CZIPCODE, CADDR1, CADDR2, COMPANY, POSITION, MEMO, PAY_TYPE, STATE, USE_TF, REG_ADM, REG_DATE) 
														values ('$new_no', '$reserve_no', '$support_type', '$biz_type', '$nm', '$mem_no', '$mem_id', '$amount', '$email', '$phone', '$hphone', '$cphone', '$zipcode', '$addr1', 
																		'$addr2', '$czipcode', '$caddr1', '$caddr2', '$company', '$position', '$memo', '$pay_type', '$state', 
																		'$use_tf', '$reg_adm', now()); ";
		
	//	echo $query;

	//	exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_no;
		}
	}


	function selectSupport($db, $support_no) {

		$query = "SELECT SUPPORT_NO, RESERVE_NO, SUPPORT_TYPE, BIZ_TYPE, NM, MEM_NO, MEM_ID, AMOUNT, EMAIL, PHONE, HPHONE, CPHONE, ZIPCODE, ADDR1, 
										 ADDR2, CZIPCODE, CADDR1, CADDR2, COMPANY, POSITION, MEMO, PAY_TYPE, STATE,
										 USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_SUPPORT WHERE SUPPORT_NO = '$support_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function selectSupportAsReserveNo($db, $reserve_no) {

		$query = "SELECT SUPPORT_NO, RESERVE_NO, SUPPORT_TYPE, BIZ_TYPE, NM, MEM_NO, MEM_ID, AMOUNT, EMAIL, PHONE, HPHONE, CPHONE, ZIPCODE, ADDR1, 
										 ADDR2, CZIPCODE, CADDR1, CADDR2, COMPANY, POSITION, MEMO, PAY_TYPE, STATE,
										 USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_SUPPORT 
							 WHERE USE_TF = 'Y'
								 AND DEL_TF = 'N' 
								 AND RESERVE_NO = '$reserve_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateSupport($db, $support_type, $biz_type, $nm, $mem_no, $mem_id, $amount, $email, $phone, $hphone, $cphone, $zipcode, $addr1, $addr2, $czipcode, $caddr1, $caddr2, $company, $position, $memo, $pay_type, $state, $use_tf, $up_adm, $support_no) {

		$query = "UPDATE TBL_SUPPORT SET 
													SUPPORT_TYPE	=	'$support_type',
													BIZ_TYPE			=	'$biz_type',
													NM						=	'$nm',
													MEM_NO				=	'$mem_no',
													MEM_ID				=	'$mem_id',
													AMOUNT				=	'$amount',
													EMAIL					=	'$email',
													PHONE					=	'$phone',
													HPHONE				=	'$hphone',
													CPHONE				=	'$cphone',
													ZIPCODE				=	'$zipcode',
													ADDR1					=	'$addr1',
													ADDR2					=	'$addr2',
													CZIPCODE			=	'$czipcode',
													CADDR1				=	'$caddr1',
													CADDR2				=	'$caddr2',
													COMPANY				=	'$company',
													POSITION			=	'$position',
													MEMO					=	'$memo',
													PAY_TYPE			=	'$pay_type',
													STATE					=	'$state',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
											 WHERE SUPPORT_NO = '$support_no' ";
		
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


	function deleteSupport($db, $del_adm, $support_no) {

		$query="UPDATE TBL_SUPPORT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE SUPPORT_NO		= '$support_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteSupportInfo($db, $reserve_no) {


		$query="UPDATE TBL_PAYMENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE RESERVE_NO				= '$reserve_no'";
		
		mysql_query($query,$db);

		$query="UPDATE TBL_SUPPORT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE RESERVE_NO				= '$reserve_no'";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateSupportConfrim($db, $reserve_no,$state, $up_adm) {

		$query="UPDATE TBL_SUPPORT SET 
								STATE 			= '$state',
								UP_ADM			= '$up_adm',
								UP_DATE			= now()
					WHERE DEL_TF = 'N' 
						AND USE_TF = 'Y' 
						AND RESERVE_NO				= '$reserve_no'";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		
	}


	function updateSupportState($db, $reserve_no,$state) {

		$query="UPDATE TBL_SUPPORT SET 
								STATE 			= '$state'
					WHERE DEL_TF = 'N' 
						AND USE_TF = 'Y' 
						AND RESERVE_NO				= '$reserve_no'";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		
	}
?>