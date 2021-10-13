<?

	# =============================================================================
	# File Name    : activity.php
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
	CREATE TABLE IF NOT EXISTS TBL_ACTIVITY (
	ACTIVITY_NO				int(10) NOT NULL default '1'					COMMENT '할동	번호',
	ACTIVITY_CODE			varchar(5) NOT NULL default ''				COMMENT '할동	코드',
	ACTIVITY_CATEGORY_NO	int(11) NULL default '0'					COMMENT '구분 번호',
	TITLE							varchar(100) default NULL							COMMENT '제목',
	TITLE_IMG					varchar(50) default NULL							COMMENT '제목 이미지',
	LECTURER_NM				varchar(50) NOT NULL default ''				COMMENT '강사',
	THEME							varchar(100) NOT NULL default ''			COMMENT '주제',
	DATE_INFO					varchar(100) default NULL							COMMENT '일정',
	PRICE							int(11) default '0'										COMMENT '참가비',
	PERSON						int(11) default '0'										COMMENT '정원',
	STATE							varchar(5) NOT NULL default ''				COMMENT '상태',
	PLACE							varchar(50) NOT NULL default ''				COMMENT '장소',
	CONTENTS					text																	COMMENT '내용',
	INFO01						text																	COMMENT '추가 정보',
	INFO02						text																	COMMENT '추가 정보,
	IS_HTML01					varchar(5) NOT NULL default ''				COMMENT '상태',
	IS_HTML02					varchar(5) NOT NULL default ''				COMMENT '상태',
	IS_HTML03					varchar(5) NOT NULL default ''				COMMENT '상태',
	USE_TF						char(1) NOT NULL default 'Y'					COMMENT '사용	여부 사용(Y),사용안함(N)',
	DEL_TF						char(1) NOT NULL default 'N'					COMMENT '삭제	여부 삭제(Y),사용(N)',
	REG_ADM						int(11) unsigned default NULL					COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE					datetime default NULL									COMMENT '등록일',
	UP_ADM						int(11) unsigned default NULL					COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE						datetime default NULL									COMMENT '수정일',
	DEL_ADM						int(11) unsigned default NULL					COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE					datetime default NULL									COMMENT '삭제일',
	PRIMARY KEY  (ACTIVITY_NO, ACTIVITY_CODE)
	) ENGINE=MyISAM COMMENT='할동 마스터';	

	CREATE TABLE IF NOT EXISTS TBL_ACTIVITY_REQ (
	REQ_NO						int(11) NOT NULL default '1'					COMMENT '신청번호',
	ACTIVITY_NO				int(11) NOT NULL default '1'					COMMENT '할동번호',
	ACTIVITY_CODE			varchar(5) NOT NULL default ''				COMMENT '할동코드',
	MEM_NO						int(11)																COMMENT '회원번호',
	MEM_NAME					varchar(50) default NULL							COMMENT '회원명',
	REQ_PERSON_CNT		int(11) NOT NULL default '1'					COMMENT '동반인수',
	REQ_PERSON_NM			varchar(300) NOT NULL default ''			COMMENT '동반인정보',
	TOTAL_PRICE				int(11) NOT NULL default '0'					COMMENT '총참가비',
	STATE							varchar(5) NOT NULL default ''				COMMENT '상태',
	MEMO							text																	COMMENT '추가 정보,
	USE_TF						char(1) NOT NULL default 'Y'					COMMENT '사용	여부 사용(Y),사용안함(N)',
	DEL_TF						char(1) NOT NULL default 'N'					COMMENT '삭제	여부 삭제(Y),사용(N)',
	REG_ADM						int(11) unsigned default NULL					COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE					datetime default NULL									COMMENT '등록일',
	UP_ADM						int(11) unsigned default NULL					COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE						datetime default NULL									COMMENT '수정일',
	DEL_ADM						int(11) unsigned default NULL					COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE					datetime default NULL									COMMENT '삭제일',
	PRIMARY KEY  (REQ_NO)
	) ENGINE=MyISAM COMMENT='할동 마스터';

	CREATE TABLE IF NOT EXISTS `TBL_ACTIVITY_FILE` (
	ACTIVITY_IMAGE_NO int(11) unsigned NOT NULL auto_increment COMMENT '할동 일련번호',
	ACTIVITY_NO int(11) unsigned NOT NULL COMMENT '할동 일련번호',
	FILE_NM1 varchar(150) NOT NULL default '' COMMENT '첨부	파일명',
	FILE_RNM1 varchar(150) NOT NULL default '' COMMENT '첨부	파일 실제	파일명',
	FILE_PATH1 varchar(150) NOT NULL default '' COMMENT '파일	경로',
	FILE_SIZE1 int(11) default NULL COMMENT '파일	사이즈',
	FILE_EXT1 varchar(5) NOT NULL default '' COMMENT '파일	확장자',
	PRIMARY KEY  (`ACTIVITY_IMAGE_NO`)
	) ENGINE=MyISAM   COMMENT='할동 이미지' AUTO_INCREMENT=358 ;

	CREATE TABLE IF NOT EXISTS TBL_ACTIVITY_CATEGORY (
	ACTIVITY_CATEGORY_NO int(11) unsigned NOT NULL auto_increment, 
	ACTIVITY_CODE varchar(5) NOT NULL default '',
	ACTIVITY_CATEGORY_NM varchar(100) default NULL,
	USE_TF char(1) NOT NULL default 'Y',
	DEL_TF char(1) NOT NULL default 'N',
	REG_ADM int(11) unsigned default NULL,
	REG_DATE datetime default NULL,
	UP_ADM int(11) unsigned default NULL,
	UP_DATE datetime default NULL,
	DEL_ADM int(11) unsigned default NULL,
	DEL_DATE datetime default NULL,
	PRIMARY KEY  (ACTIVITY_CATEGORY_NO,ACTIVITY_CODE)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='아카데미 구분';


	*/


	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#ACTIVITY_NO, ACTIVITY_CODE, TITLE, TITLE_IMG, LECTURER_NM, THEME, DATE_INFO, PRICE, PERSON, STATE, PLACE, CONTENTS, INFO01, INFO02, 
	#USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

	function listActivity($db, $activity_code, $activity_category_no, $state, $condition, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntActivity($db, $activity_code, $activity_category_no, $state, $condition, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, ACTIVITY_NO, ACTIVITY_CODE, ACTIVITY_CATEGORY_NO, TITLE, TITLE_IMG, LECTURER_NM, THEME, DATE_INFO, PRICE, PERSON, 
										 STATE, PLACE, CONTENTS, INFO01, INFO02, IS_HTML01, IS_HTML02, IS_HTML03, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ACTIVITY WHERE 1 = 1 ";

		
		if ($activity_code <> "") {
			$query .= " AND ACTIVITY_CODE = '".$activity_code."' ";
		}

		if ($activity_category_no <> "") {
			$query .= " AND ACTIVITY_CATEGORY_NO = '".$activity_category_no."' ";
		}

		if ($state <> "") {
			$query .= " AND STATE = '".$state."' ";
		}

		if ($condition <> "") {
			$query .= " AND ".$condition." ";
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

	function totalCntActivity($db, $activity_code, $activity_category_no, $state, $condition, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_ACTIVITY WHERE 1 = 1 ";
		
		if ($activity_code <> "") {
			$query .= " AND ACTIVITY_CODE = '".$activity_code."' ";
		}

		if ($activity_category_no <> "") {
			$query .= " AND ACTIVITY_CATEGORY_NO = '".$activity_category_no."' ";
		}

		if ($state <> "") {
			$query .= " AND STATE = '".$state."' ";
		}

		if ($condition <> "") {
			$query .= " AND ".$condition." ";
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

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertActivity($db, $activity_code, $activity_category_no, $title, $title_img, $lecturer_nm, $theme, $date_info, $price, $person, $state, $place, $contents, $info01, $info02, $is_html01, $is_html02, $is_html03, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(ACTIVITY_NO),0) AS MAX_NO FROM TBL_ACTIVITY ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_activity_no  = ($rows[0] + 1);


		$query5="INSERT INTO TBL_ACTIVITY (ACTIVITY_NO, ACTIVITY_CODE, ACTIVITY_CATEGORY_NO, TITLE, TITLE_IMG, LECTURER_NM, THEME, DATE_INFO, PRICE, 
																			 PERSON, STATE, PLACE, CONTENTS, INFO01, INFO02, IS_HTML01, IS_HTML02, IS_HTML03, USE_TF, REG_ADM, REG_DATE) 
															values ('$new_activity_no', '$activity_code', '$activity_category_no', '$title', '$title_img', '$lecturer_nm', '$theme', '$date_info', '$price', 
																			'$person', '$state', '$place', '$contents', '$info01', '$info02', '$is_html01', '$is_html02', '$is_html03', '$use_tf', '$reg_adm', now()); ";
		
	//	echo $query5;

	//	exit;

		if(!mysql_query($query5,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_activity_no;
		}
	}


	function selectActivity($db, $activity_code, $activity_no) {

		$query = "SELECT ACTIVITY_NO, ACTIVITY_CODE, ACTIVITY_CATEGORY_NO, TITLE, TITLE_IMG, LECTURER_NM, THEME, DATE_INFO, PRICE, 
										 PERSON, STATE, PLACE, CONTENTS, INFO01, INFO02, IS_HTML01, IS_HTML02, IS_HTML03, USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ACTIVITY WHERE  ACTIVITY_CODE = '$activity_code' AND  ACTIVITY_NO = '$activity_no' ";
		
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


	function updateActivity($db, $activity_code, $activity_category_no, $title, $title_img, $lecturer_nm, $theme, $date_info, $price, $person, $state, $place, $contents, $info01, $info02, $is_html01, $is_html02, $is_html03, $use_tf, $up_adm, $activity_no) {

		$query = "UPDATE TBL_ACTIVITY SET 
													ACTIVITY_CODE	=	'$activity_code',
													ACTIVITY_CATEGORY_NO =	'$activity_category_no',
													TITLE					=	'$title',
													TITLE_IMG			=	'$title_img',
													LECTURER_NM		=	'$lecturer_nm',
													THEME					=	'$theme',
													DATE_INFO			=	'$date_info',
													PRICE					=	'$price',
													PERSON				=	'$person',
													STATE					=	'$state',
													PLACE					=	'$place',
													CONTENTS			=	'$contents',
													INFO01				=	'$info01',
													INFO02				=	'$info02',
													IS_HTML01			=	'$is_html01',
													IS_HTML02			=	'$is_html02',
													IS_HTML03			=	'$is_html03',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
											 WHERE ACTIVITY_NO = '$activity_no' ";
		
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

	function updateActivityUseTF($db, $use_tf, $up_adm, $activity_code, $activity_no) {
		
		$query="UPDATE TBL_ACTIVITY SET 
							USE_TF					= '$use_tf',
							UP_ADM					= '$up_adm',
							UP_DATE					= now()
				 WHERE ACTIVITY_CODE = '$activity_code' AND ACTIVITY_NO = '$activity_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteActivity($db, $del_adm, $activity_code, $activity_no) {

		$query="UPDATE TBL_ACTIVITY SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE ACTIVITY_CODE = '$activity_code' AND ACTIVITY_NO = '$activity_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertActivityFile($db, $activity_no, $file_nm1, $file_rnm1, $file_path1, $file_size1, $file_ext1) {
		
		if ($reg_date == "") {
			$query="INSERT INTO TBL_ACTIVITY_FILE (ACTIVITY_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1) 
													  values ('$activity_no', '$file_nm1', '$file_rnm1', '$file_path1', '$file_size1', '$file_ext1'); ";
		} else {
			$query="INSERT INTO TBL_ACTIVITY_FILE (ACTIVITY_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1) 
													  values ('$activity_no', '$file_nm1', '$file_rnm1', '$file_path1', '$file_size1', '$file_ext1'); ";
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

	function selectActivityFile($db, $activity_no) {

		$query = "SELECT ACTIVITY_IMAGE_NO, ACTIVITY_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1
								FROM TBL_ACTIVITY_FILE
							 WHERE ACTIVITY_NO = '$activity_no' order by ACTIVITY_IMAGE_NO asc ";
		
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

	function deleteActivityFile($db, $del_adm, $activity_no) {

		$query="DELETE FROM TBL_ACTIVITY_FILE WHERE ACTIVITY_NO = '$activity_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertActivityReq($db, $reserve_no, $activity_no, $activity_code, $mem_no, $mem_name, $req_person_cnt, $req_person_nm, $total_price, $state, $memo, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(REQ_NO),0) AS MAX_NO FROM TBL_ACTIVITY_REQ ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_req_no  = ($rows[0] + 1);


		$query5="INSERT INTO TBL_ACTIVITY_REQ (REQ_NO, RESERVE_NO, ACTIVITY_NO, ACTIVITY_CODE, MEM_NO, MEM_NAME, REQ_PERSON_CNT, REQ_PERSON_NM, TOTAL_PRICE, STATE, 
																			 MEMO, USE_TF, REG_ADM, REG_DATE) 
															values ('$new_req_no','$reserve_no', '$activity_no', '$activity_code', '$mem_no', '$mem_name', '$req_person_cnt', '$req_person_nm', '$total_price', '$state', 
																			'$memo', '$use_tf', '$reg_adm', now()); ";
		
	//	echo $query5;

	//	exit;

		if(!mysql_query($query5,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_req_no;
		}
	}


	function selectActivityReq($db, $req_no) {

		$query = "SELECT REQ_NO, RESERVE_NO, ACTIVITY_NO, ACTIVITY_CODE, MEM_NO, MEM_NAME, REQ_PERSON_CNT, REQ_PERSON_NM, TOTAL_PRICE, STATE, 
										 MEMO, USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ACTIVITY_REQ
							 WHERE REQ_NO = '$req_no' ";
		
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

	function selectActivityReqAsReserveNo($db, $reserve_no) {

		$query = "SELECT Q.REQ_NO, Q.RESERVE_NO, Q.ACTIVITY_NO, Q.ACTIVITY_CODE, Q.MEM_NO, Q.MEM_NAME, Q.REQ_PERSON_CNT, Q.REQ_PERSON_NM, Q.TOTAL_PRICE, Q.STATE, 
										 Q.MEMO, Q.USE_TF, Q.REG_ADM, Q.REG_DATE, Q.UP_ADM, Q.UP_DATE, Q.DEL_ADM, Q.DEL_DATE,
										 (SELECT TITLE FROM TBL_ACTIVITY WHERE Q.ACTIVITY_NO = ACTIVITY_NO AND Q.ACTIVITY_CODE = ACTIVITY_CODE)  as TITLE,
										 (SELECT PAY_TYPE FROM TBL_PAYMENT WHERE RESERVE_NO = Q.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAY_TYPE,
										 (SELECT PAY_STATE FROM TBL_PAYMENT WHERE RESERVE_NO = Q.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAY_STATE
								FROM TBL_ACTIVITY_REQ Q
							 WHERE Q.DEL_TF = 'N' AND Q.USE_TF= 'Y' AND Q.RESERVE_NO = '$reserve_no' ";
		
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


	function listActivityReq($db, $reserve_no, $activity_no, $activity_code, $state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntActivityReq($db, $reserve_no, $activity_no, $activity_code, $state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, Q.REQ_NO, Q.RESERVE_NO, Q.ACTIVITY_NO, Q.ACTIVITY_CODE, Q.MEM_NO, Q.MEM_NAME, Q.REQ_PERSON_CNT, Q.REQ_PERSON_NM, Q.TOTAL_PRICE, Q.STATE, 
										 Q.MEMO, Q.USE_TF, Q.REG_ADM, Q.REG_DATE, Q.UP_ADM, Q.UP_DATE, Q.DEL_ADM, Q.DEL_DATE,
										 (SELECT TITLE FROM TBL_ACTIVITY WHERE Q.ACTIVITY_NO = ACTIVITY_NO AND Q.ACTIVITY_CODE = ACTIVITY_CODE)  as TITLE,
										 (SELECT PAY_TYPE FROM TBL_PAYMENT WHERE RESERVE_NO = Q.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAY_TYPE,
										 (SELECT PAY_STATE FROM TBL_PAYMENT WHERE RESERVE_NO = Q.RESERVE_NO AND USE_TF = 'Y' AND DEL_TF = 'N' ORDER BY PAY_NO DESC LIMIT 1 ) AS PAY_STATE

								FROM TBL_ACTIVITY_REQ Q WHERE 1 = 1 ";

		if ($reserve_no <> "") {
			$query .= " AND Q.RESERVE_NO = '".$reserve_no."' ";
		}

		if ($activity_no <> "") {
			$query .= " AND Q.ACTIVITY_NO = '".$activity_no."' ";
		}
		
		if ($activity_code <> "") {
			$query .= " AND Q.ACTIVITY_CODE = '".$activity_code."' ";
		}

		if ($state <> "") {
			$query .= " AND Q.STATE = '".$state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND Q.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND Q.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY Q.REG_DATE desc limit ".$offset.", ".$nRowCount;

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

	function totalCntActivityReq($db, $reserve_no, $activity_no, $activity_code, $state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_ACTIVITY_REQ WHERE 1 = 1 ";

		if ($reserve_no <> "") {
			$query .= " AND RESERVE_NO = '".$reserve_no."' ";
		}

		if ($activity_no <> "") {
			$query .= " AND ACTIVITY_NO = '".$activity_no."' ";
		}

		if ($activity_code <> "") {
			$query .= " AND ACTIVITY_CODE = '".$activity_code."' ";
		}

		if ($state <> "") {
			$query .= " AND STATE = '".$state."' ";
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

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listActivityCategory($db, $activity_code, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntActivityCategory($db, $activity_code, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, ACTIVITY_CATEGORY_NO, ACTIVITY_CODE, ACTIVITY_CATEGORY_NM,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ACTIVITY_CATEGORY WHERE 1 = 1 ";

		if ($activity_code <> "") {
			$query .= " AND ACTIVITY_CODE = '".$activity_code."' ";
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

	function totalCntActivityCategory($db, $activity_code, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_ACTIVITY_CATEGORY WHERE 1 = 1 ";
		
		if ($activity_code <> "") {
			$query .= " AND ACTIVITY_CODE = '".$activity_code."' ";
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

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function selectActivityCategory($db, $activity_category_no) {

		$query = "SELECT ACTIVITY_CATEGORY_NO, ACTIVITY_CODE, ACTIVITY_CATEGORY_NM, USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ACTIVITY_CATEGORY
							 WHERE ACTIVITY_CATEGORY_NO = '$activity_category_no' ";
		
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


	function insertActivityCategory($db, $activity_code, $activity_category_nm, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_ACTIVITY_CATEGORY (ACTIVITY_CODE, ACTIVITY_CATEGORY_NM, USE_TF, REG_ADM, REG_DATE) 
															values ('$activity_code', '$activity_category_nm', '$use_tf', '$reg_adm', now()); ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateActivityCategory($db, $activity_code, $activity_category_nm, $use_tf, $up_adm, $activity_category_no) {

		$query = "UPDATE TBL_ACTIVITY_CATEGORY SET 
													ACTIVITY_CODE	=	'$activity_code',
													ACTIVITY_CATEGORY_NM	=	'$activity_category_nm',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
											 WHERE ACTIVITY_CATEGORY_NO = '$activity_category_no' ";
		
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

	function updateActivityCategoryUseTF($db, $use_tf, $up_adm, $activity_category_no) {
		
		$query="UPDATE TBL_ACTIVITY_CATEGORY SET 
							USE_TF					= '$use_tf',
							UP_ADM					= '$up_adm',
							UP_DATE					= now()
				 WHERE ACTIVITY_CATEGORY_NO = '$activity_category_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteActivityCategory($db, $del_adm, $activity_category_no) {

		$query="UPDATE TBL_ACTIVITY_CATEGORY SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE ACTIVITY_CATEGORY_NO = '$activity_category_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteActivityReqInfo($db, $reserve_no) {

		$query="UPDATE TBL_PAYMENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE RESERVE_NO				= '$reserve_no'";
		
		mysql_query($query,$db);


		$query="UPDATE TBL_ACTIVITY_REQ SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE RESERVE_NO = '$reserve_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateActivityReqConfrim($db, $reserve_no,$state, $up_adm) {

		$query="UPDATE TBL_ACTIVITY_REQ SET 
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

	function updateActivityReqState($db, $reserve_no, $state) {

		$query="UPDATE TBL_ACTIVITY_REQ SET 
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
?>