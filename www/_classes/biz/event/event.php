<?

	# =============================================================================
	# File Name    : event.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.25
	# Modify Date  : 
	#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_EVENT
	#=========================================================================================================
	
	/*
	CREATE TABLE IF	NOT	EXISTS TBL_EVENT (
	EVENT_NO						int(11) unsigned	NOT	NULL auto_increment	COMMENT	'이벤트	일련번호',
	SITE_NO							int(11)	unsigned													COMMENT	'사이트 일련번호',
	EVENT_TYPE					varchar(20) NOT NULL	default	''					COMMENT	'이벤트 구분 ',
	EVENT_NM						varchar(150) NOT NULL	default	''					COMMENT	'이벤트 명',
	EVENT_FROM					varchar(12) NOT NULL	default	''					COMMENT	'시작일',
	EVENT_TO						varchar(12) NOT NULL	default	''					COMMENT	'종료일',
	EVENT_RESULT				varchar(12) NOT NULL	default	''					COMMENT	'결과발표일',
	RESULT							text				NOT NULL	default	''					COMMENT	'결과',
	FILE_NM							varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일명',
	FILE_RNM						varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일 실제	파일명',
	FILE_PATH						varchar(150) NOT NULL	default	''					COMMENT	'파일	경로',
	FILE_SIZE						int(11)																		COMMENT	'파일	사이즈',
	FILE_EXT						varchar(5) NOT NULL	default	''						COMMENT	'파일	확장자',
	FILE_NM2						varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일명',
	FILE_RNM2						varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일 실제	파일명',
	FILE_PATH2					varchar(150) NOT NULL	default	''					COMMENT	'파일	경로',
	FILE_SIZE2					int(11)																		COMMENT	'파일	사이즈',
	FILE_EXT2						varchar(5) NOT NULL	default	''						COMMENT	'파일	확장자',
	EVENT_STATE					char(1) NOT NULL	default	''							COMMENT	'상태',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (EVENT_NO)
	)	TYPE=MyISAM COMMENT	=	'이벤트 마스터';


	CREATE TABLE IF	NOT	EXISTS TBL_EVENT_APPLY (
	APPLY_NO						int(11) unsigned	NOT	NULL auto_increment	COMMENT	'이벤트 참가자	일련번호',
	EVENT_NO						int(11) unsigned													COMMENT	'이벤트	일련번호',
	EVENT_TYPE					char(1)			NOT NULL	default	'N'					COMMENT	'이벤트 구분 ',
	MEMBER_NO						varchar(30) NOT NULL	default	''					COMMENT	'회원아이디',
	MEMBER_NM						varchar(30) NOT NULL	default	''					COMMENT	'회원명',
	MEMBER_TYPE					char(1)			NOT NULL	default	'Y'					COMMENT	'회원 구분 ',
	ZIPCODE							varchar(6) NOT NULL	default	''						COMMENT	'우편번호 ',
	ADDR01							varchar(50) NOT NULL	default	''					COMMENT	'주소',
	ADDR02							varchar(50) NOT NULL	default	''					COMMENT	'주소',
	PHONE01							varchar(10) NOT NULL	default	''					COMMENT	'전화번호',
	PHONE02							varchar(10) NOT NULL	default	''					COMMENT	'전화번호',
	PHONE03							varchar(10) NOT NULL	default	''					COMMENT	'전화번호',
	EMAIL								varchar(150) NOT NULL	default	''					COMMENT	'이메일',
	ANSWER							varchar(60) NOT NULL	default	''					COMMENT	'답변',
	PICK_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'담첨	여부 사용(Y),사용안함(N)',
	HIT_CNT							int(11)	default	'0'												COMMENT	'조회수',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (APPLY_NO)
	)	TYPE=MyISAM COMMENT	=	'당첨자 마스터';
	*/
	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
	#FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

function listEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {
		
		$this_date = date("Y-m-d",strtotime("0 day"));

		$total_cnt = totalCntEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, 
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE,  
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT WHERE 1 = 1 ";

		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}
		
		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}


		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
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
		
		$query .= " ORDER BY EVENT_NO DESC limit ".$offset.", ".$nRowCount;

		#echo $query."<br>";


		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str){

		$this_date = date("Y-m-d",strtotime("0 day"));

		$query ="SELECT COUNT(*) CNT FROM TBL_EVENT WHERE 1 = 1 ";
		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}
		
		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}

		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
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

	#	echo $query."<br>";
	#	die;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listEventResult($db, $site_no, $event_type, $event_from, $event_to, $event_result, $event_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {
		
		$this_date = date("Y-m-d",strtotime("0 day"));

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, 
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE, HIT_CNT, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT WHERE 1 = 1 ";

		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}


		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
		}

		if ($event_result <> "") {
			$query .= " AND EVENT_RESULT <= '".$this_date."' ";
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
		
		$query .= " ORDER BY EVENT_NO DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntEventResult($db, $site_no, $event_type, $event_from, $event_to, $event_result, $event_state, $use_tf, $del_tf, $search_field, $search_str){

		$this_date = date("Y-m-d",strtotime("0 day"));

		$query ="SELECT COUNT(*) CNT FROM TBL_EVENT WHERE 1 = 1 ";
		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}

		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
		}

		if ($event_result <> "") {
			$query .= " AND EVENT_RESULT <= '".$this_date."' ";
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

//		echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

function selectPreEvent($db, $event_no, $g_site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str) {
		
		$this_date = date("Y-m-d",strtotime("0 day"));

		$query = "SELECT EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, RESULT,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT WHERE event_no < '$event_no' ";

		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}
		
		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
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
								
		$query .= " ORDER BY REG_DATE DESC limit 1";
		
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

function selectPostEvent($db, $event_no, $g_site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str) {
		
		$this_date = date("Y-m-d",strtotime("0 day"));

		$query = "SELECT EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, RESULT,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT WHERE event_no > '$event_no' ";

		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
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

		$query .= " ORDER BY REG_DATE ASC limit 1";
		

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
							
		return $record;
	}

	function insertEvent($db, $site_no, $event_type, $event_nm, $event_from, $event_to, $event_result,$contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $file_nm2, $file_rnm2, $file_path2, $file_size2, $file_ext2, $event_state, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_EVENT (SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
																	 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE, USE_TF, REG_ADM, REG_DATE, CONTENTS) 
														values ('$site_no', '$event_type', '$event_nm', '$event_from', '$event_to', '$event_result', 
																		'$file_nm', '$file_rnm', '$file_path', '$file_size', '$file_ext', 
																		'$file_nm2', '$file_rnm2', '$file_path2', '$file_size2', '$file_ext2', '$event_state',
																		'$use_tf', '$reg_adm', now(),'$contents'); ";
		

		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}



	function selectEvent($db, $event_no) {

		$query = "SELECT EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, RESULT,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,CONTENTS
								FROM TBL_EVENT WHERE EVENT_NO = '$event_no' ";

		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function viewChkEventApply($db, $apply_no) {
		
		$query="UPDATE TBL_EVENT_APPLY SET HIT_CNT = HIT_CNT + 1 WHERE APPLY_NO = '$apply_no' ";
	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function viewChkEvent($db, $event_no) {
		
		$query="UPDATE TBL_EVENT SET HIT_CNT = HIT_CNT + 1 WHERE EVENT_NO = '$event_no' ";
	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateEvent($db, $site_no, $event_type, $event_nm, $event_from, $event_to, $event_result,$contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $file_nm2, $file_rnm2, $file_path2, $file_size2, $file_ext2, $event_state, $use_tf, $up_adm, $event_no) {


		$query = "UPDATE TBL_EVENT SET 
													SITE_NO				=	'$site_no',
													EVENT_TYPE		=	'$event_type',
													EVENT_NM			=	'$event_nm',
													EVENT_FROM		=	'$event_from',
													EVENT_TO			=	'$event_to',
													EVENT_RESULT	=	'$event_result',
													CONTENTS			=	'$contents',
													FILE_NM				=	'$file_nm',
													FILE_RNM			=	'$file_rnm',
													FILE_PATH			=	'$file_path',
													FILE_SIZE			=	'$file_size',
													FILE_EXT			=	'$file_ext',
													FILE_NM2			=	'$file_nm2',
													FILE_RNM2			=	'$file_rnm2',
													FILE_PATH2		=	'$file_path2',
													FILE_SIZE2		=	'$file_size2',
													FILE_EXT2			=	'$file_ext2',
													EVENT_STATE		=	'$event_state',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
									 WHERE EVENT_NO = '$event_no' ";



		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertEventResult($db, $result, $up_adm, $event_no) {
		
		$query="UPDATE TBL_EVENT SET 
							 RESULT					= '$result',
							 UP_ADM					= '$up_adm',
							 UP_DATE					= now()
				 WHERE EVENT_NO = '$event_no'  ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	// $contents, $s_adm_no, $event_no, $rs_event_state
	function updateEventResult($db, $result, $up_adm, $event_no, $event_state) {
		
		$query="UPDATE TBL_EVENT SET 
							 RESULT					= '$result',
							 UP_ADM					= '$up_adm',
							 EVENT_STATE		= '$event_state',
							 UP_DATE					= now()
				 WHERE EVENT_NO = '$event_no'  ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
	function updateEventUseTF($db, $use_tf, $up_adm, $event_no) {
		
		$query="UPDATE TBL_EVENT SET 
							 USE_TF					= '$use_tf',
							 UP_ADM					= '$up_adm',
							 UP_DATE					= now()
				 WHERE EVENT_NO = '$event_no'  ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteEvent($db, $del_adm, $event_no) {

		$query="UPDATE TBL_EVENT SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE EVENT_NO = '$event_no'  ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function listEventApply($db, $event_no, $event_type, $member_type,  $pick_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {
		
		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, APPLY_NO, EVENT_NO, EVENT_TYPE, MEMBER_NO, MEMBER_NM, MEMBER_TYPE, ZIPCODE, ADDR01, ADDR02, PHONE01, PHONE02, PHONE03, EMAIL, ANSWER, PICK_TF, USE_TF, REG_ADM, REG_DATE,CONTENTS,HIT_CNT FROM TBL_EVENT_APPLY WHERE 1 = 1 ";
		
		if ($event_no <> "") {
			$query .= " AND EVENT_NO = '".$event_no."' ";
		}

		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE = '".$event_type."' ";
		}

		if ($member_type <> "") {
			$query .= " AND MEMBER_TYPE = '".$member_type."' ";
		}

		if ($pick_tf <> "") {
			$query .= " AND PICK_TF = '".$pick_tf."' ";
		}


		if ($use_tf <> "") {
#			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY APPLY_NO DESC limit ".$offset.", ".$nRowCount;

	#	echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntEventApply($db, $event_no, $event_type, $member_type, $pick_tf, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_EVENT_APPLY WHERE 1 = 1 ";
		
		if ($event_no <> "") {
			$query .= " AND EVENT_NO = '".$event_no."' ";
		}

		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE = '".$event_type."' ";
		}

		if ($member_type <> "") {
			$query .= " AND MEMBER_TYPE = '".$member_type."' ";
		}

		if ($pick_tf <> "") {
			$query .= " AND PICK_TF = '".$pick_tf."' ";
		}

		if ($use_tf <> "") {
	#		$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}


	#echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertEventApply($db, $event_no, $event_type, $member_no, $member_nm, $member_type, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $email, $answer,$contents, $pick_tf, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_EVENT_APPLY (EVENT_NO, EVENT_TYPE, MEMBER_NO, MEMBER_NM, MEMBER_TYPE, ZIPCODE, ADDR01, ADDR02, 
																	 PHONE01, PHONE02, PHONE03, EMAIL, ANSWER, PICK_TF, USE_TF, REG_ADM, REG_DATE,CONTENTS) 
														values ('$event_no', '$event_type', '$member_no', '$member_nm', '$member_type', '$zipcode', '$addr01', '$addr02', 
																		'$phone01', '$phone02', '$phone03', '$email', '$answer', 'N',
																		'$use_tf', '$reg_adm', now(),'$contents'); ";
		
	//	echo $query;
	//	die;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function selectEventApply($db, $apply_no) {

		$query = "SELECT APPLY_NO, EVENT_NO, EVENT_TYPE, MEMBER_NO, MEMBER_NM, MEMBER_TYPE, ZIPCODE, ADDR01, ADDR02, 
										 PHONE01, PHONE02, PHONE03, EMAIL, ANSWER,CONTENTS, PICK_TF, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT_APPLY A WHERE APPLY_NO = '$apply_no' ";
	
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateEventApply($db, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $email, $pick_tf, $use_tf, $up_adm, $apply_no) {

		$query = "UPDATE TBL_EVENT_APPLY SET 
													ZIPCODE				=	'$zipcode',
													ADDR01				=	'$addr01',
													ADDR02				=	'$addr02',
													PHONE01				=	'$phone01',
													PHONE02				=	'$phone02',
													PHONE03				=	'$phone03',
													EMAIL					=	'$email',
													PICK_TF				=	'$pick_tf',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
									 WHERE APPLY_NO = '$apply_no' ";
		
		//echo $query."<br>";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateEventPickTF($db, $pick_tf, $up_adm, $apply_no) {
		
		$query="UPDATE TBL_EVENT_APPLY SET 
							 PICK_TF				= '$pick_tf',
							 UP_ADM					= '$up_adm',
							 UP_DATE					= now()
				 WHERE APPLY_NO = '$apply_no'  ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteEventApply($db, $del_adm, $apply_no) {

		$query="UPDATE TBL_EVENT_APPLY SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE APPLY_NO = '$apply_no'  ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function totalWinnerCntEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str){

		$this_date = date("Y-m-d",strtotime("0 day"));

		$query ="SELECT COUNT(*) CNT FROM TBL_EVENT WHERE event_state = 'Y' ";
		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}

	/*	if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
		}*/
		
		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
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

	#	echo $query."<br>";
	#	die;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

function listWinnerEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {
		
		$this_date = date("Y-m-d",strtotime("0 day"));

		$total_cnt = totalWinnerCntEvent($db, $site_no, $event_type, $event_from, $event_to, $event_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, EVENT_NO, SITE_NO, EVENT_TYPE, EVENT_NM, EVENT_FROM, EVENT_TO, EVENT_RESULT, 
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2, EVENT_STATE,  
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_EVENT WHERE event_state = 'Y' ";

		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}


	/*		if ($event_state == "Y") {
			$query .= " AND EVENT_FROM <= '".$this_date."' AND EVENT_TO >= '".$this_date."' ";
		}

		if ($event_state == "C") {
			$query .= " AND EVENT_FROM > '".$this_date."' AND EVENT_TO > '".$this_date."' ";
		}

		if ($event_state == "E") {
			$query .= " AND EVENT_FROM < '".$this_date."' AND EVENT_TO < '".$this_date."' ";
		}
		
	if ($event_state <> "") {
			$query .= " AND EVENT_STATE = '".$event_state."' ";
		}*/

		if ($event_type <> "") {
			$query .= " AND EVENT_TYPE  = '".$event_type."' ";
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
		
		$query .= " ORDER BY EVENT_NO DESC limit ".$offset.", ".$nRowCount;

		#echo $query."<br>";


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