<?

	# =============================================================================
	# File Name    : serve.php
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
	CREATE TABLE IF NOT EXISTS `TBL_SERVE` (
  SERVE_NO int(11) unsigned NOT NULL auto_increment COMMENT '자원봉사	일련번호',
  MEM_NO int(11) unsigned NOT NULL COMMENT '회원	일련번호',
  MEM_TYPE varchar(10) NOT NULL default '' COMMENT '회원 구분',
  MEM_ID varchar(16) NOT NULL default '' COMMENT '회원	ID',
  MEM_NM varchar(30) NOT NULL default '' COMMENT '회원명',
  PHONE varchar(30) NOT NULL default '' COMMENT '전화번호',
  HPHONE varchar(30) NOT NULL default '' COMMENT '휴대전화번호',
  SERVE_TYPE varchar(30) NOT NULL default '' COMMENT '자원봉사 일시',
  INFO01 text NOT NULL default '' COMMENT '전공분야',
  INFO02 text NOT NULL default '' COMMENT '봉사할동',
  INFO03 text NOT NULL default '' COMMENT '신청동기',
  STATE varchar(10) NOT NULL default '' COMMENT '신청동기',
  USE_TF char(1) NOT NULL default 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  DEL_TF char(1) NOT NULL default 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  REG_ADM int(11) unsigned default NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  REG_DATE datetime default NULL COMMENT '등록일',
  UP_ADM int(11) unsigned default NULL COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
  UP_DATE datetime default NULL COMMENT '수정일',
  DEL_ADM int(11) unsigned default NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  DEL_DATE datetime default NULL COMMENT '삭제일',
  FOREIGNER_NUM varchar(50) default NULL,
  PRIMARY KEY  (SERVE_NO)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr COMMENT='자원 봉사 마스터';
	*/


	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#SERVE_NO, MEM_NO, MEM_TYPE, MEM_ID, MEM_NM, PHONE, HPHONE, SERVE_TYPE, INFO01, INFO02, INFO03, STATE,
	#USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

	function listServe($db, $serve_type, $state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntServe($db, $serve_type, $state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, SERVE_NO, MEM_NO, MEM_TYPE, MEM_ID, MEM_NM, PHONE, HPHONE, SERVE_TYPE, INFO01, INFO02, INFO03, STATE,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_SERVE WHERE 1 = 1 ";

		
		if ($serve_type <> "") {
			$query .= " AND SERVE_TYPE = '".$serve_type."' ";
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

	function totalCntServe($db, $serve_type, $state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_SERVE WHERE 1 = 1 ";
		
		if ($serve_type <> "") {
			$query .= " AND SERVE_TYPE = '".$serve_type."' ";
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

	function insertServe($db, $mem_no, $mem_type, $mem_id, $mem_nm, $phone, $hphone, $serve_type, $info01, $info02, $info03, $state, $use_tf, $reg_adm) {
		
		$query5="INSERT INTO TBL_SERVE (MEM_NO, MEM_TYPE, MEM_ID, MEM_NM, PHONE, HPHONE, SERVE_TYPE, INFO01, INFO02, INFO03, STATE, USE_TF, REG_ADM, REG_DATE) 
														values ('$mem_no', '$mem_type', '$mem_id', '$mem_nm', '$phone', '$hphone', '$serve_type', '$info01', '$info02', '$info03', '$state', 
																		'$use_tf', '$reg_adm', now()); ";
		
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


	function selectServe($db, $serve_no) {

		$query = "SELECT SERVE_NO, MEM_NO, MEM_TYPE, MEM_ID, MEM_NM, PHONE, HPHONE, SERVE_TYPE, INFO01, INFO02, INFO03, STATE,
										 USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_SERVE WHERE SERVE_NO = '$serve_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function updateServe($db, $mem_no, $mem_type, $mem_id, $mem_nm, $phone, $hphone, $serve_type, $info01, $info02, $info03, $state, $use_tf, $up_adm, $serve_no) {

		$query = "UPDATE TBL_SERVE SET 
													MEM_NO			=	'$mem_no',
													MEM_TYPE		=	'$mem_type',
													MEM_ID			=	'$mem_id',
													MEM_NM			=	'$mem_nm',
													PHONE				=	'$phone',
													HPHONE			=	'$hphone',
													SERVE_TYPE	=	'$serve_type',
													INFO01			=	'$info01',
													INFO02			=	'$info02',
													INFO03			=	'$info03',
													STATE				=	'$state',
													USE_TF			=	'$use_tf',
													UP_ADM			=	'$up_adm',
													UP_DATE			=	now()
											 WHERE SERVE_NO = '$serve_no' ";
		
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

	function updateServeUseTF($db, $use_tf, $up_adm, $serve_no) {
		
		$query="UPDATE TBL_SERVE SET 
								USE_TF		= '$use_tf',
								UP_ADM		= '$up_adm',
								UP_DATE		= now()
				  WHERE SERVE_NO	= '$serve_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteServe($db, $del_adm, $serve_no) {

		$query="UPDATE TBL_SERVE SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE SERVE_NO	= '$serve_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateServeState($db, $serve_no, $state) {

		$query="UPDATE TBL_SERVE SET 
														 STATE				= '$state'
											 WHERE SERVE_NO	= '$serve_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateServeType($db, $serve_no, $serve_type) {

		$query="UPDATE TBL_SERVE SET 
														 SERVE_TYPE	= '$serve_type'
											 WHERE SERVE_NO		= '$serve_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


?>