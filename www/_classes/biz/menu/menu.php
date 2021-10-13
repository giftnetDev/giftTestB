<?

	# =============================================================================
	# File Name    : menu.php
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_ADMIN_MENU
	#=========================================================================================================

	/*
	CREATE TABLE IF NOT EXISTS TBL_ADMIN_MENU (
	MENU_NO							int(11)				unsigned NOT NULL default '0'	COMMENT	'메뉴 SEQ',
	MENU_CD							varchar(10)		NOT	NULL default ''						COMMENT	'메뉴코드',
	MENU_NAME						varchar(50)		NOT	NULL default ''						COMMENT	'메뉴명',
	MENU_URL						varchar(100)	NOT	NULL default ''						COMMENT	'메뉴 URL',
	MENU_SEQ01					varchar(3)		NOT	NULL default ''						COMMENT	'메뉴 순서 1',
	MENU_SEQ02					varchar(3)		NOT	NULL default ''						COMMENT	'메뉴 순서 2',
	MENU_SEQ03					varchar(3)		NOT	NULL default ''						COMMENT	'메뉴 순서 3',
	MENU_FLAG						char(1)				NOT	NULL default ''						COMMENT	'메뉴 상태',
	MENU_RIGHT					varchar(10)		NOT	NULL default ''						COMMENT	'메뉴 권한',
	MENU_IMG						varchar(50)		NOT	NULL default ''						COMMENT	'메뉴 이미지',
	MENU_IMG_OVER				varchar(50)		NOT	NULL default ''						COMMENT	'메뉴 이미지 2',
	USE_TF							char(1)				NOT	NULL default 'Y'					COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)				NOT	NULL default 'N'					COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)				unsigned											COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																		COMMENT	'등록일',
	UP_ADM							int(11)				unsigned											COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																		COMMENT	'수정일',
	DEL_ADM							int(11)				unsigned											COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																		COMMENT	'삭제일'
	) TYPE=MyISAM COMMENT	=	'메뉴 마스터';
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================


	function listAdminMenu($db, $use_tf, $del_tf, $search_field, $search_str) {
		
		$query = "SELECT CONCAT(MENU_SEQ01,MENU_SEQ02,MENU_SEQ03) as SEQ,
										 MENU_NO, MENU_CD, MENU_NAME, MENU_URL, MENU_FLAG, MENU_SEQ01, MENU_SEQ02, MENU_SEQ03, MENU_RIGHT
							FROM TBL_ADMIN_MENU WHERE 1 = 1 ";

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " ORDER BY SEQ ASC ";

		$result = mysql_query($query,$db);
		$record = array();

		for($i=0;$i < mysql_num_rows($result);$i++) {
			
			$record[$i] = sql_result_array($result,$i);
		}
		return $record;
	}

	function dupMenuRight ($db, $menu_right) {
		
		$query ="SELECT COUNT(*) CNT FROM TBL_ADMIN_MENU WHERE 1 = 1 AND DEL_TF = 'N' ";
		
		if ($menu_right <> "") {
			$query .= " AND MENU_RIGHT = '".$menu_right."' ";
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if ($rows[0] == 0) {
			return 0;
		} else {
			return 1;
		}
				
	}


	/*	메뉴 등록*/
	
	function insertAdminMenu($db, $m_level, $m_seq01, $m_seq02, $menu_name, $menu_url, $menu_flag, $menu_right, $menu_img, $menu_img_over, $use_tf, $reg_adm) {

		$iMax = "0";	

		$sSeq01		= "";
		$sSeq02		= "";
		$sSeq03		= "";
		$sSeq_01	= "";
		$sSeq_02	= "";
		$sSeq_03	= "";
		$sMenu_cd	= "";
		
		$query = "SELECT COUNT(*) cnt FROM TBL_ADMIN_MENU WHERE MENU_RIGHT = '".$menu_right."' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		$result = mysql_query($query,$db);
		$row = mysql_fetch_array($result);

		//중복 권한 코드
		if ($row["cnt"] > 0) {
			return "2";
		}

		
		if (strlen($m_level) == 0) { 
			
			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(MENU_CD,1,2)),0) + 1),-2) as M_CD FROM TBL_ADMIN_MENU ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq02 = "00";
			$sSeq03 = "00";

			$sMenu_cd = $row["M_CD"];

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(MENU_SEQ01),0) + 1),-2) as SEQ FROM TBL_ADMIN_MENU ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_01 = $row["SEQ"];

			$sSeq_02 = "00";
			$sSeq_03 = "00";

		}

		if (strlen($m_level) == 2) { 
			
			 $sSeq01 = $m_level;

			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(MENU_CD,3,2)),0) + 1),-2) as M_CD FROM TBL_ADMIN_MENU WHERE substring(MENU_CD,1,2) = '$m_level' ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);

			$sSeq02 = $row["M_CD"];
			$sSeq03 = "00";

			$sMenu_cd = $sSeq01.$sSeq02;

			$sSeq_01 = $m_seq01;

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(MENU_SEQ02),0) + 1),-2) as SEQ FROM TBL_ADMIN_MENU WHERE substring(MENU_CD,1,2) = '$m_level' ";

			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_02 = $row["SEQ"];
			$sSeq_03 = "00";

		}

		if (strlen($m_level) == 4) { 

			$sSeq01 = substr($m_level,0,2);
			$sSeq02 = substr($m_level,2,2);
			
			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(MENU_CD,5,2)),0) + 1),-2) as M_CD FROM TBL_ADMIN_MENU WHERE substring(MENU_CD,1,2) = '".substr($m_level,0,2)."' and substring(MENU_CD,3,2) = '".substr($m_level,2,2)."' ";
						
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq03 = $row["M_CD"];
			
			$sMenu_cd =  $sSeq01.$sSeq02.$sSeq03;

			$sSeq_01 = $m_seq01;
			$sSeq_02 = $m_seq02;

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(MENU_SEQ03),0) + 1),-2) as SEQ FROM TBL_ADMIN_MENU WHERE substring(MENU_CD,1,2) = '".substr($m_level,0,2)."' and substring(MENU_CD,3,2) = '".substr($m_level,2,2)."' ";
			
			#echo $query;

			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_03 = $row["SEQ"];

		}
		
		$query = "SELECT IFNULL(MAX(MENU_NO),0) + 1  as IMAX FROM TBL_ADMIN_MENU ";
		$result = mysql_query($query,$db);
		$row = mysql_fetch_array($result);
			
		$iMax = $row["IMAX"];
		
		$query = "INSERT INTO TBL_ADMIN_MENU (MENU_NO, MENU_CD, MENU_NAME, MENU_URL, MENU_SEQ01, MENU_SEQ02, MENU_SEQ03, 
												MENU_FLAG, MENU_RIGHT, MENU_IMG, MENU_IMG_OVER, USE_TF, REG_ADM, REG_DATE)
							VALUES	('$iMax', '$sMenu_cd', '$menu_name', '$menu_url', '$sSeq_01', '$sSeq_02', '$sSeq_03', 
											 '$menu_flag', '$menu_right','$menu_img','$menu_img_over','$use_tf', '$reg_adm', now()); ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function selectAdminMenu($db, $menu_no) {

		$query = "SELECT MENU_NO, MENU_NAME, MENU_URL, MENU_FLAG, MENU_CD, MENU_RIGHT,MENU_IMG,MENU_IMG_OVER 
								FROM TBL_ADMIN_MENU 
							 WHERE MENU_NO = '$menu_no' ";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
							
		return $record;
	}

	function updateAdminMenu($db, $menu_name, $menu_url, $menu_flag, $menu_right, $menu_img, $menu_img_over, $use_tf, $up_adm, $menu_no) {

		$query="UPDATE TBL_ADMIN_MENU SET 
									 menu_name			= '$menu_name', 
									 menu_url				= '$menu_url', 
									 menu_flag			= '$menu_flag', 
									 menu_right			= '$menu_right', 
									 menu_img				= '$menu_img', 
									 menu_img_over	= '$menu_img_over', 
									 USE_TF					= '$use_tf',
									 UP_ADM					= '$up_adm',
									 UP_DATE				= now()
						 WHERE MENU_NO				= '$menu_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteAdminMenu($db, $del_adm, $menu_no) {

		$query="SELECT MENU_CD FROM TBL_ADMIN_MENU WHERE MENU_NO			= '$menu_no' ";
		$result = mysql_query($query,$db);
		$row = mysql_fetch_array($result);
			
		$rs_menu_cd = $row["MENU_CD"];
		
		#echo $rs_menu_cd;

		$query="UPDATE TBL_ADMIN_MENU SET 
												 DEL_TF				= 'Y',
												 DEL_ADM			= '$del_adm',
												 DEL_DATE			= now()														 
									 WHERE MENU_CD			like '".$rs_menu_cd."%' ";

		mysql_query($query,$db);

		$query="DELETE FROM TBL_ADMIN_MENU_RIGHT WHERE MENU_CD like '".$rs_menu_cd."%' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateAdminMenuOrder($db, $arr_menu_no, $menu_level, $seq_no) {

		$query="UPDATE TBL_ADMIN_MENU SET " .$menu_level. " = '" .$seq_no. "' WHERE MENU_NO IN	".$arr_menu_no;

		#echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

?>