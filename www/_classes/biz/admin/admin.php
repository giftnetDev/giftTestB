<?

	# =============================================================================
	# File Name    : admin.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.08.16
	# Modify Date  : 
	#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_ADMIN
	#=========================================================================================================

	/*
	CREATE TABLE IF NOT EXISTS TBL_ADMIN_INFO (
	ADM_ID							varchar(15)		NOT NULL default ''								COMMENT	'관리자 ID',
	ADM_NO							int(11)				unsigned NOT NULL auto_increment	COMMENT	'관리자 SEQ',
	PASSWD							varchar(15)		NOT NULL default ''								COMMENT	'비밀번호',
	ADM_NAME						varchar(50)		NOT	NULL default ''								COMMENT	'성명',
	ADM_INFO						text																						COMMENT	'관리자 정보',
	ADM_HPHONE					varchar(50)		NOT	NULL default ''								COMMENT	'연락처',
	ADM_PHONE						varchar(50)		NOT	NULL default ''								COMMENT	'연락처',
	ADM_EMAIL						varchar(70)		NOT	NULL default ''								COMMENT	'이메일',
	GROUP_NO						int(11)				unsigned NOT NULL default '0'			COMMENT	'관리자 그룹',
	ADM_FLAG						char(1)				NOT NULL default ''								COMMENT	'관리자 구분',
	POSITION_CODE				varchar(20)		NOT	NULL default ''								COMMENT	'직급',
	DEPT_CODE						varchar(20)		NOT	NULL default ''								COMMENT	'직책',
	COM_CODE						varchar(20)		NOT	NULL default ''								COMMENT	'회사 코드',
	USE_TF							char(1)				NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)				NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)				unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																				COMMENT	'등록일',
	UP_ADM							int(11)				unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																				COMMENT	'수정일',
	DEL_ADM							int(11)				unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																				COMMENT	'삭제일',
	PRIMARY KEY  (ADM_ID, ADM_NO)
	) TYPE=MyISAM COMMENT	=	'관리자 마스터';

CREATE TABLE IF NOT EXISTS TBL_ADMIN_GROUP (
	GROUP_NO						int(11)				unsigned NOT NULL auto_increment 	COMMENT	'관리자 그룹',
	GROUP_NAME					varchar(50)		NOT NULL default ''								COMMENT	'그룹 명',
	GROUP_FLAG					char(1)				NOT NULL default ''								COMMENT	'그룹 상태',
	USE_TF							char(1)				NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)				NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)				unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																				COMMENT	'등록일',
	UP_ADM							int(11)				unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																				COMMENT	'수정일',
	DEL_ADM							int(11)				unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																				COMMENT	'삭제일',
	PRIMARY KEY  (GROUP_NO)
) TYPE=MyISAM COMMENT	=	'관리자 그룹 마스터';
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listAdminGroup($db, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, GROUP_NO, GROUP_NAME, GROUP_FLAG, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ADMIN_GROUP A WHERE 1 = 1 ";
		

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY GROUP_NO desc limit ".$offset.", ".$nRowCount;

		#echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function totalCntAdminGroup ($db, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(*) CNT FROM TBL_ADMIN_GROUP WHERE 1 = 1 ";
		
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

	function insertAdminGroup($db, $group_name, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_ADMIN_GROUP (GROUP_NAME, GROUP_FLAG, USE_TF, REG_ADM, REG_DATE) 
											 values ('$group_name', 'Y', 'Y', '$reg_adm', now()); ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateAdminGroup($db, $group_name, $use_tf, $up_adm, $group_no) {
		
		$query="UPDATE TBL_ADMIN_GROUP SET 
									 GROUP_NAME	= '$group_name', 
									 USE_TF				= 'Y',
									 UP_ADM				= '$up_adm',
									 UP_DATE			= now()
						 WHERE GROUP_NO				= '$group_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteAdminGroup($db, $del_adm, $group_no) {

		$query="UPDATE TBL_ADMIN_INFO SET 
											 DEL_TF				= 'Y',
											 DEL_ADM			= '$del_adm',
											 DEL_DATE			= now()														 
								 WHERE GROUP_NO			= '$group_no' ";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		}

		$query="UPDATE TBL_ADMIN_GROUP SET 
											 DEL_TF				= 'Y',
											 DEL_ADM			= '$del_adm',
											 DEL_DATE			= now()														 
								 WHERE GROUP_NO			= '$group_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listAdmin($db, $group_no, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, ADM_ID, ADM_NO, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
										 GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT GROUP_NAME FROM TBL_ADMIN_GROUP WHERE GROUP_NO = A.GROUP_NO) AS GROUP_NAME,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = A.COM_CODE) AS CP_NM
								FROM TBL_ADMIN_INFO A WHERE 1 = 1 ";
		

		if ($group_no <> "") {
			$query .= " AND GROUP_NO = '".$group_no."' ";
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
		
		$query .= " ORDER BY ADM_NO desc limit ".$offset.", ".$nRowCount;

		#echo $query;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function totalCntAdmin ($db, $group_no, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(*) CNT FROM TBL_ADMIN_INFO WHERE 1 = 1 ";
		
		if ($group_no <> "") {
			$query .= " AND GROUP_NO = '".$group_no."' ";
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

	function insertAdmin($db, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $md_tf, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(ADM_NO),0) + 1 AS MAX_NO FROM TBL_ADMIN_INFO ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		$new_adm_no = $rows[0];

		$query="INSERT INTO TBL_ADMIN_INFO (ADM_NO, ADM_ID, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
																				GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE, MD_TF, USE_TF, REG_ADM, REG_DATE) 
											 values ('$new_adm_no','$adm_id', '$passwd', '$adm_name', '$adm_info', '$adm_hphone', '$adm_phone', '$adm_email', 
															 '$group_no', '$adm_flag', '$position_code', '$dept_code', '$com_code', '$md_tf', '$use_tf', '$reg_adm', now()); ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectAdmin($db, $adm_no) {

		$query = "SELECT ADM_NO, ADM_ID, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
										 GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_ADMIN_INFO.COM_CODE) AS CP_NM,
										 MD_TF
								FROM TBL_ADMIN_INFO WHERE ADM_NO = '$adm_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateAdmin($db, $adm_id, $passwd, $adm_name, $adm_info, $adm_hphone, $adm_phone, $adm_email, $group_no, $adm_flag, $position_code, $dept_code, $com_code, $md_tf, $use_tf, $up_adm, $adm_no) {
		
		$query="UPDATE TBL_ADMIN_INFO SET 
									 ADM_ID					= '$adm_id', 
									 PASSWD					= '$passwd', 
									 ADM_NAME				= '$adm_name', 
									 ADM_INFO				= '$adm_info', 
									 ADM_HPHONE			= '$adm_hphone', 
									 ADM_PHONE			= '$adm_phone', 
									 ADM_EMAIL			= '$adm_email', 
									 GROUP_NO				= '$group_no', 
									 ADM_FLAG				= '$adm_flag', 
									 POSITION_CODE	= '$position_code', 
									 DEPT_CODE			= '$dept_code', 
									 COM_CODE				= '$com_code', 
									 MD_TF					= '$md_tf',
									 USE_TF					= '$use_tf',
									 UP_ADM					= '$up_adm',
									 UP_DATE				= now()
						 WHERE ADM_NO					= '$adm_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteAdmin($db, $del_adm, $adm_no) {

		$query="UPDATE TBL_ADMIN_INFO SET 
									 DEL_TF				= 'Y',
									 DEL_ADM			= '$del_adm',
									 DEL_DATE			= now()														 
						 WHERE ADM_NO				= '$adm_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function dupAdmin ($db,$adm_id) {
		
		$query ="SELECT COUNT(*) CNT FROM TBL_ADMIN_INFO WHERE 1 = 1 AND DEL_TF = 'N' ";
		
		if ($adm_id <> "") {
			$query .= " AND ADM_ID = '".$adm_id."' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if ($rows[0] == 0) {
			return 0;
		} else {
			return 1;
		}
				
	}


	function confirmAdmin($db, $adm_id) {
	
		$query = "SELECT ADM_NO, ADM_ID, PASSWD, ADM_NAME, ADM_EMAIL, GROUP_NO, MD_TF, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, COM_CODE,
										 (SELECT CP_TYPE FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = A.COM_CODE) AS CP_TYPE
								FROM TBL_ADMIN_INFO A WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND ADM_ID = '$adm_id' ";
		
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

	function insertUserLog($db, $user_type, $log_id, $log_ip) {
		
		$query="INSERT INTO TBL_USER_LOG (USER_TYPE, LOG_ID, LOG_IP, LOGIN_DATE) 
															 values ('$user_type', '$log_id', '$log_ip', now()); ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectUserLogLatest($db, $user_type, $log_id) {

		$query = "SELECT LOG_IP, LOGIN_DATE
					FROM TBL_USER_LOG 
				   WHERE USER_TYPE IN ($user_type) AND LOG_ID = '$log_id'
				ORDER BY LOGIN_DATE DESC
				   LIMIT 0, 1
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

	function updateAdminUseTF($db, $use_tf, $up_adm, $adm_no) {
		
		$query="UPDATE TBL_ADMIN_INFO SET 
							USE_TF			= '$use_tf',
							UP_ADM			= '$up_adm',
							UP_DATE			= now()
				 WHERE ADM_NO			= '$adm_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectAdminGroup($db, $group_no) {

		$query = "SELECT GROUP_NO, GROUP_NAME, GROUP_FLAG, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ADMIN_GROUP WHERE GROUP_NO = '$group_no' ";

		#echo $query;
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listAdminGroupMenuRight($db, $group_no) {

		$query = "SELECT MENU_CD, GROUP_NO, READ_FLAG, REG_FLAG, UPD_FLAG, DEL_FLAG, FILE_FLAG, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ADMIN_MENU_RIGHT WHERE GROUP_NO = '$group_no' ";

		#echo $query;
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function deleteAdminGroupMenuRight($db, $group_no) {
		
		$query="DELETE FROM TBL_ADMIN_MENU_RIGHT WHERE GROUP_NO			= '$group_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertAdminGroupMenuRight($db, $group_no, $menu_cd, $read_chk, $reg_chk, $upd_chk, $del_chk, $file_chk) {
		
		$query="INSERT INTO TBL_ADMIN_MENU_RIGHT (GROUP_NO, MENU_CD, READ_FLAG, REG_FLAG, UPD_FLAG, DEL_FLAG, FILE_FLAG) 
																		  VALUES ('$group_no', '$menu_cd', '$read_chk', '$reg_chk', '$upd_chk', '$del_chk', '$file_chk')";
		#echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function getAdminInfoNameMD($db, $adm_no) {

		$query = "SELECT ADM_NAME
								FROM TBL_ADMIN_INFO 
								WHERE 1 = 1 AND 
								DEL_TF = 'N' AND 
								USE_TF = 'Y' AND 
								MD_TF = 'Y' AND 
								ADM_NO = '$adm_no'";
		
		
		#echo $query;
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		return $rows[0];
	}

	function getAdminInfoNoMD($db, $adm_nm) {

		$query = "SELECT ADM_NO
								FROM TBL_ADMIN_INFO 
								WHERE 1 = 1 AND 
								DEL_TF = 'N' AND 
								USE_TF = 'Y' AND 
								MD_TF = 'Y' AND 
								ADM_NAME = '$adm_nm'";
		
		
		#echo $query;
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		return $rows[0];
	}

	function getAdminInfoName($db, $adm_no) {

		$query = "SELECT ADM_NAME
								FROM TBL_ADMIN_INFO 
								WHERE 1 = 1 AND 
								DEL_TF = 'N' AND 
								USE_TF = 'Y' AND 
								ADM_NO = '$adm_no'";
		
		
		#echo $query;
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		return $rows[0];
	}

	function tryAdminNoByName($db, $adm_name){
		$query = "SELECT ADM_NO
								FROM TBL_ADMIN_INFO 
								WHERE 1 = 1 AND 
								DEL_TF = 'N' AND 
								USE_TF = 'Y' AND 
								MD_TF = 'Y' AND 
								ADM_NAME = '$adm_name' 
								";

	    //echo $query."<br>";
		//exit;

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;

		} else {
			return "등록요망";
		}
	}
?>