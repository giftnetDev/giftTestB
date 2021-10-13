<?

	# =============================================================================
	# File Name    : member.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.05
	# Modify Date  : 
	#	Copyright : Copyright @minumsa Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_MEMBER
	#=========================================================================================================
	
	/*
	CREATE TABLE IF	NOT	EXISTS TBL_MEMBER (
	MEM_NO							int(11) unsigned	NOT	NULL auto_increment	COMMENT	'회원	일련번호',
	MEM_TYPE						varchar(10)	NOT	NULL default ''						COMMENT	'회원 구분',
	MEM_ID							varchar(16)	NOT	NULL default ''						COMMENT	'회원	ID',
	MEM_PW							varchar(16)	NOT	NULL default ''						COMMENT	'회원	비밀번호',
	MEM_NM							varchar(30)	NOT	NULL default ''						COMMENT	'회원명',
	JUMIN1							varchar(10)	NOT	NULL default ''						COMMENT	'주민등록번호1',
	JUMIN2							varchar(10)	NOT	NULL default ''						COMMENT	'주민등록번호2',
	BIRTH_DATE					varchar(10)	NOT	NULL default ''						COMMENT	'생년월일',
	CALENDAR						char(1)			NOT	NULL default ''						COMMENT	'음력(L), 양력(S)',
	EMAIL								varchar(100)NOT	NULL default ''						COMMENT	'이메일',
	EMAIL_TF						varchar(1)	NOT	NULL default 'Y'					COMMENT	'이메일 수신여부',
	ZIPCODE							varchar(10)	NOT	NULL default ''						COMMENT	'우편번호',
	ADDR1								varchar(100)NOT	NULL default ''						COMMENT	'주소',
	ADDR2								varchar(100)NOT	NULL default ''						COMMENT	'상세주소',
	PHONE								varchar(30)	NOT	NULL default ''						COMMENT	'전화번호',
	HPHONE							varchar(30)	NOT	NULL default ''						COMMENT	'휴대전화번호',
	JOB									varchar(30)	NOT	NULL default ''						COMMENT	'직업',
	ETC									text																			COMMENT	'기타사항',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (MEM_NO)
	)	TYPE=MyISAM COMMENT	=	'회원 마스터';

	CREATE TABLE IF	NOT	EXISTS TBL_MEMBER_DEL (
	MEM_NO							int(11) unsigned	NOT	NULL auto_increment	COMMENT	'회원	일련번호',
	MEM_TYPE						varchar(10)	NOT	NULL default ''						COMMENT	'회원 구분',
	MEM_ID							varchar(16)	NOT	NULL default ''						COMMENT	'회원	ID',
	MEM_PW							varchar(16)	NOT	NULL default ''						COMMENT	'회원	비밀번호',
	MEM_NM							varchar(30)	NOT	NULL default ''						COMMENT	'회원명',
	JUMIN1							varchar(10)	NOT	NULL default ''						COMMENT	'주민등록번호1',
	JUMIN2							varchar(10)	NOT	NULL default ''						COMMENT	'주민등록번호2',
	BIRTH_DATE					varchar(10)	NOT	NULL default ''						COMMENT	'생년월일',
	CALENDAR						char(1)			NOT	NULL default ''						COMMENT	'음력(L), 양력(S)',
	EMAIL								varchar(100)NOT	NULL default ''						COMMENT	'이메일',
	EMAIL_TF						varchar(1)	NOT	NULL default 'Y'					COMMENT	'이메일 수신여부',
	ZIPCODE							varchar(10)	NOT	NULL default ''						COMMENT	'우편번호',
	ADDR1								varchar(100)NOT	NULL default ''						COMMENT	'주소',
	ADDR2								varchar(100)NOT	NULL default ''						COMMENT	'상세주소',
	PHONE								varchar(30)	NOT	NULL default ''						COMMENT	'전화번호',
	HPHONE							varchar(30)	NOT	NULL default ''						COMMENT	'휴대전화번호',
	JOB									varchar(30)	NOT	NULL default ''						COMMENT	'직업',
	ETC									text																			COMMENT	'기타사항',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (MEM_NO)
	)	TYPE=MyISAM COMMENT	=	'탈퇴 회원 마스터';
	*/
	//20210310 jumin1, jumin2 -> cp_nm, ceo_nm 으로 변경
	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, 
										 CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, UPJONG, 
										 UPTEA, ETC, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_MEMBER WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY MEM_NO desc limit ".$offset.", ".$nRowCount;
		//echo "$query";
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_MEMBER WHERE 1 = 1 ";


		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
	
	//20210702 사용안하는 듯.
	function listManagerMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, M.MEM_NO, V.MEM_TYPE, M.MEM_ID, M.MEM_PW, M.MEM_NM, M.CP_NM, 
										 M.CEO_NM, M.BIZ_NUM1, M.BIZ_NUM2, M.BIZ_NUM3, M.BIRTH_DATE, M.CALENDAR, M.EMAIL, M.EMAIL_TF, M.ZIPCODE, M.ADDR1, M.ADDR2, M.PHONE, M.HPHONE, M.JOB, 
										 M.POSITION, M.CPHONE, M.CFAX, M.CZIPCODE, M.CADDR1, M.CADDR2, M.JOIN_HOW, M.JOIN_HOW_PERSON, M.JOIN_HOW_ETC, M.ETC, 
										 M.USE_TF, M.DEL_TF, M.REG_ADM, M.REG_DATE, M.UP_ADM, M.UP_DATE, M.DEL_ADM, M.DEL_DATE
								FROM TBL_MEMBER M, V_MEMBER_TYPE V WHERE M.MEM_NO = V.MEM_NO ";

		if ($mem_type <> "") {
			$query .= " AND V.MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND M.EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND M.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND M.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY M.MEM_NO desc limit ".$offset.", ".$nRowCount;
		
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntManagerMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_MEMBER M, V_MEMBER_TYPE V WHERE M.MEM_NO = V.MEM_NO ";


		if ($mem_type <> "") {
			$query .= " AND V.MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND M.EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND M.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND M.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listmember_xls($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT * FROM TBL_MEMBER WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
//		$query .= " ORDER BY seq desc limit ".$offset.", ".$nRowCount;
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listApply_xls($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT * FROM TBL_application WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
//		$query .= " ORDER BY seq desc limit ".$offset.", ".$nRowCount;
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listApply($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, seq, MEM_TYPE, MEM_ID, MEM_NM, gubun,regdate,number,mail_chk FROM TBL_application WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY seq desc limit ".$offset.", ".$nRowCount;
		
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntApply($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_application WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}
	//20210702 새로생성 이 함수 사용하자.(컬럼정리했음)
	function insertMember($db, $mem_type, $mem_id, $mem_pw, $mem_nm, $cp_nm, $ceo_nm, $biz_num1, $biz_num2, $biz_num3, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $upjong, $uptea, $etc, $use_tf, $reg_adm, $cp_no ="") {
		
		$query ="SELECT IFNULL(MAX(MEM_NO),0) + 1 AS MAX_NO FROM TBL_MEMBER ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		$new_mem_no = $rows[0];

		$query="INSERT INTO TBL_MEMBER (MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3,
																			EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, UPJONG, UPTEA, 
																			ETC, USE_TF, REG_ADM, REG_DATE, CP_NO) 
																values ('$new_mem_no', 'C', '$mem_id', MD5('$mem_pw'), '$mem_nm', '$cp_nm', '$ceo_nm', '$biz_num1', '$biz_num2', '$biz_num3', 
																				'$email', '$email_tf', '$zipcode', '$addr1', '$addr2' 
																				, CASE  WHEN LENGTH('$phone') = '9'  THEN CONCAT(LEFT('$phone', 2),'-',MID('$phone', 3,3),'-',RIGHT('$phone', 4)) 
																						WHEN LENGTH('$phone') = '10' AND  LEFT('$phone',2) = '02' 
																													 THEN CONCAT(LEFT('$phone', 2),'-',MID('$phone', 3,4),'-',RIGHT('$phone', 4))
																						WHEN LENGTH('$phone') = '10' THEN CONCAT(LEFT('$phone', 3),'-',MID('$phone', 4,3),'-',RIGHT('$phone', 4)) 
																						WHEN LENGTH('$phone') = '11' THEN CONCAT(LEFT('$phone', 3),'-',MID('$phone', 4,4),'-',RIGHT('$phone', 4)) 
																				  ELSE '$phone' END
																				, CASE  WHEN LENGTH('$hphone') = '9'  THEN CONCAT(LEFT('$hphone', 2),'-',MID('$hphone', 3,3),'-',RIGHT('$hphone', 4)) 
																						WHEN LENGTH('$hphone') = '10' AND  LEFT('$hphone',2) = '02' 
																													  THEN CONCAT(LEFT('$hphone', 2),'-',MID('$hphone', 3,4),'-',RIGHT('$hphone', 4))
																						WHEN LENGTH('$hphone') = '10' THEN CONCAT(LEFT('$hphone', 3),'-',MID('$hphone', 4,3),'-',RIGHT('$hphone', 4)) 
																						WHEN LENGTH('$hphone') = '11' THEN CONCAT(LEFT('$hphone', 3),'-',MID('$hphone', 4,4),'-',RIGHT('$hphone', 4)) 
																				  ELSE '$hphone' END
										  										, '$upjong', 
																				'$uptea', '$etc', '$use_tf', '$reg_adm', now(), $cp_no); ";
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_mem_no;
		}
	}

	//20210702 예전 홈페이지 사용안함.
	function insertMember_bak($db, $mem_type, $mem_id, $mem_pw, $mem_nm, $cp_nm, $ceo_nm, $biz_num1, $biz_num2, $biz_num3, $birth_date, $calendar, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $job, $position, $cphone, $cfax, $czipcode, $caddr1, $caddr2, $join_how, $join_how_person, $join_how_etc, $etc, $foreigner_num, $use_tf, $reg_adm, $cp_no ="") {
		
		$query ="SELECT IFNULL(MAX(MEM_NO),0) + 1 AS MAX_NO FROM TBL_MEMBER ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		$new_mem_no = $rows[0];

		if($mem_type == "S" && $cp_no != ""){
			$query="INSERT INTO TBL_MEMBER (MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, BIRTH_DATE, CALENDAR, 
																			EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, JOB, POSITION, CPHONE, CFAX, CZIPCODE, CADDR1, CADDR2, 
																			JOIN_HOW, JOIN_HOW_PERSON, JOIN_HOW_ETC, ETC, FOREIGNER_NUM, USE_TF, REG_ADM, REG_DATE, CP_NO) 
																values ('$new_mem_no', '$mem_type', '$mem_id', '$mem_pw', '$mem_nm', '$cp_nm', '$ceo_nm', '$biz_num1', '$biz_num2', '$biz_num3', 
																				'$birth_date', '$calendar', '$email', '$email_tf', '$zipcode', '$addr1', '$addr2', '$phone', '$hphone', '$job', 
																				'$position', '$cphone', '$cfax', '$czipcode', '$caddr1', '$caddr2', 
																				'$join_how', '$join_how_person', '$join_how_etc', '$etc', '$foreigner_num', '$use_tf', '$reg_adm', now(), $cp_no); ";
		} else {
			$query="INSERT INTO TBL_MEMBER (MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, BIRTH_DATE, CALENDAR, 
																			EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, JOB, POSITION, CPHONE, CFAX, CZIPCODE, CADDR1, CADDR2, 
																			JOIN_HOW, JOIN_HOW_PERSON, JOIN_HOW_ETC, ETC, FOREIGNER_NUM, USE_TF, REG_ADM, REG_DATE) 
																values ('$new_mem_no', '$mem_type', '$mem_id', '$mem_pw', '$mem_nm', '$cp_nm', '$ceo_nm', '$biz_num1', '$biz_num2', '$biz_num3', 
																				'$birth_date', '$calendar', '$email', '$email_tf', '$zipcode', '$addr1', '$addr2', '$phone', '$hphone', '$job', 
																				'$position', '$cphone', '$cfax', '$czipcode', '$caddr1', '$caddr2', 
																				'$join_how', '$join_how_person', '$join_how_etc', '$etc', '$foreigner_num', '$use_tf', '$reg_adm', now()); ";
		}
		// echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_mem_no;
		}
	}

	function selectMember($db, $mem_no) {

		$query = "SELECT MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, 
										 EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, UPJONG, UPTEA, 
										 ETC,
										 CP_NO,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
										, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 1), '-', -1) PHONE1
										, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 2), '-', -1) PHONE2
										, SUBSTRING_INDEX(SUBSTRING_INDEX(PHONE, '-', 3), '-', -1) PHONE3
										, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 1), '-', -1) HPHONE1
										, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 2), '-', -1) HPHONE2
										, SUBSTRING_INDEX(SUBSTRING_INDEX(HPHONE, '-', 3), '-', -1) HPHONE3
								FROM TBL_MEMBER WHERE MEM_NO = '$mem_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();
//echo"$query";
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function chkMember($db, $mem_id) {

		$query = "SELECT MEM_NO, MEM_TYPE, MEM_NM, MEM_PW, EMAIL, PHONE, HPHONE, CP_NO, USE_TF, DEL_TF
					FROM TBL_MEMBER 
				   WHERE MEM_ID = '$mem_id' 
				   		AND DEL_TF = 'N' ";

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


	function updateMember($db, $mem_type, $mem_nm, $mem_pw, $biz_num1, $biz_num2, $biz_num3, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $upjong, $uptea, $etc, $use_tf, $up_adm, $mem_no) {

		if ($mem_no <> "") {

			if ($mem_no <> "0") {

				$query="UPDATE TBL_MEMBER SET 
													MEM_TYPE				= '$mem_type',
													MEM_NM					= '$mem_nm',
													MEM_PW					= MD5('$mem_pw'),
													BIZ_NUM1				= '$biz_num1',
													BIZ_NUM2				= '$biz_num2',
													BIZ_NUM3				= '$biz_num3',
													EMAIL					= '$email',
													EMAIL_TF				= '$email_tf',
													ZIPCODE					= '$zipcode',
													ADDR1					= '$addr1',
													ADDR2					= '$addr2',
													PHONE					=  CASE  WHEN LENGTH('$phone') = '9'  THEN CONCAT(LEFT('$phone', 2),'-',MID('$phone', 3,3),'-',RIGHT('$phone', 4)) 
																					 WHEN LENGTH('$phone') = '10' AND  LEFT('$phone',2) = '02' 
																					 							  THEN CONCAT(LEFT('$phone', 2),'-',MID('$phone', 3,4),'-',RIGHT('$phone', 4))
																					 WHEN LENGTH('$phone') = '10' THEN CONCAT(LEFT('$phone', 3),'-',MID('$phone', 4,3),'-',RIGHT('$phone', 4)) 
																					 WHEN LENGTH('$phone') = '11' THEN CONCAT(LEFT('$phone', 3),'-',MID('$phone', 4,4),'-',RIGHT('$phone', 4)) 
																			   ELSE '$phone' END ,
													HPHONE					=  CASE  WHEN LENGTH('$hphone') = '9'  THEN CONCAT(LEFT('$hphone', 2),'-',MID('$hphone', 3,3),'-',RIGHT('$hphone', 4)) 
																					 WHEN LENGTH('$hphone') = '10' AND  LEFT('$hphone',2) = '02' 
																					 							   THEN CONCAT(LEFT('$hphone', 2),'-',MID('$hphone', 3,4),'-',RIGHT('$hphone', 4))
																					 WHEN LENGTH('$hphone') = '10' THEN CONCAT(LEFT('$hphone', 3),'-',MID('$hphone', 4,3),'-',RIGHT('$hphone', 4)) 
																					 WHEN LENGTH('$hphone') = '11' THEN CONCAT(LEFT('$hphone', 3),'-',MID('$hphone', 4,4),'-',RIGHT('$hphone', 4)) 
																			   ELSE '$hphone' END , 
													UPJONG					= '$upjong',
													UPTEA					= '$uptea',
													ETC						= '$etc',
													USE_TF					= '$use_tf',
													UP_ADM					= '$up_adm',
													UP_DATE					=	now()
											 WHERE MEM_NO				= '$mem_no' ";

		#echo $query;

				if(!mysql_query($query,$db)) {
					return false;
					echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
					exit;
				} else {
					return true;
				}
			}
		}
		return true;
	}


	function updateMemberExtra($db, $cp_no, $mem_no) { 
		if ($mem_no <> "") {

			if ($mem_no <> "0") {

			/*$query="UPDATE TBL_MEMBER SET 
											CP_NO					=	'$cp_no'
										 WHERE MEM_NO				= '$mem_no' ";			20210608 제외 하고 아래 추가수정	*/		
			$query="UPDATE TBL_MEMBER A
						, (
								SELECT CP_NO
									 , CP_NM
									 , CEO_NM
									 , SUBSTR(BIZ_NO, 1,3) AS BIZ_NUM1
									 , SUBSTR(BIZ_NO, 5,2) AS BIZ_NUM2
									 , SUBSTR(BIZ_NO, 8,5) AS BIZ_NUM3
								  FROM TBL_COMPANY
								 WHERE CP_NO = '$cp_no'
							)B
					  SET A.CP_NO		= B.CP_NO
						, A.CP_NM		= B.CP_NM
						, A.CEO_NM 		= B.CEO_NM
						, A.BIZ_NUM1	= B.BIZ_NUM1
						, A.BIZ_NUM2  	= B.BIZ_NUM2
						, A.BIZ_NUM3  	= B.BIZ_NUM3
					WHERE A.MEM_NO = '$mem_no'
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
		}
		return true;

	}

	//20210310 사용안하는듯.
	function updateMemberAdmin($db, $mem_type, $mem_id, $mem_nm, $cp_nm, $ceo_nm, $mem_pw, $birth_date, $calendar, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $job, $etc, $foreigner_num, $use_tf, $up_adm, $mem_no) {

		$query="UPDATE TBL_MEMBER SET 
													MEM_ID					= '$mem_id',
													MEM_NM					= '$mem_nm',
													CP_NM					= '$cp_nm',
													CEO_NM					= '$ceo_nm',
													MEM_TYPE				= '$mem_type',
													MEM_PW					= '$mem_pw',
													BIRTH_DATE			= '$birth_date',
													CALENDAR				= '$calendar',
													EMAIL						= '$email',
													EMAIL_TF				= '$email_tf',
													ZIPCODE					= '$zipcode',
													ADDR1						= '$addr1',
													ADDR2						= '$addr2',
													PHONE						= '$phone',
													HPHONE					= '$hphone',
													JOB							= '$job',
													ETC							= '$etc',
													FOREIGNER_NUM		= '$foreigner_num',
													USE_TF					= '$use_tf',
													UP_ADM					=	'$up_adm',
													UP_DATE					=	now()
											 WHERE MEM_NO				= '$mem_no' ";



		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteMember($db, $mem_no, $del_adm) {

		$query="UPDATE TBL_MEMBER SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE MEM_NO				= '$mem_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function dupMember ($db,$mem_id) {
		
		$query ="SELECT COUNT(*) CNT FROM TBL_MEMBER WHERE DEL_TF = 'N' AND MEM_ID = '".$mem_id."' ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if ($rows[0] == 0) {
			return 0;
		} else {
			return 1;
		}
	}
	//20210310 사용안하는듯.
	function dupMemberJumin ($db,$mem_jumin) {
		
		$query ="SELECT COUNT(*) CNT FROM TBL_MEMBER WHERE 1 = 1 AND DEL_TF = 'N' ";
		
		if ($mem_jumin <> "") {
			$query .= " AND CONCAT(JUMIN1, JUMIN2) = '".$mem_jumin."' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if ($rows[0] == 0) {
			return 0;
		} else {
			return 1;
		}
	}


	function listDelMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, 
										 CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, BIRTH_DATE, CALENDAR, EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, JOB, POSITION, CPHONE, CFAX, CZIPCODE, CADDR1, CADDR2, 
										 JOIN_HOW, JOIN_HOW_PERSON, JOIN_HOW_ETC, ETC, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_MEMBER_DEL WHERE 1 = 1 ";

		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY REG_DATE desc limit ".$offset.", ".$nRowCount;
		
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntDelMember($db, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_MEMBER_DEL WHERE 1 = 1 ";


		if ($mem_type <> "") {
			$query .= " AND MEM_TYPE = '".$mem_type."' ";
		}

		if ($email_tf <> "") {
			$query .= " AND EMAIL_TF = '".$email_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function selectDelMember($db, $mem_no) {

		$query = "SELECT MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, CP_NM, CEO_NM, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, BIRTH_DATE, CALENDAR, EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, JOB, POSITION, CPHONE, CFAX, CZIPCODE, CADDR1, CADDR2, 
										 JOIN_HOW, JOIN_HOW_PERSON, JOIN_HOW_ETC, ETC,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_MEMBER_DEL WHERE MEM_NO = '$mem_no' ";
		
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