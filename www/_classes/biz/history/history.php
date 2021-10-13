<?

	# =============================================================================
	# File Name    : history.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.08.16
	# Modify Date  : 
	#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_HISTORY
	#=========================================================================================================

	/*
	CREATE TABLE IF	NOT	EXISTS TBL_HISTORY (
	HISTORY_NO					int(11) unsigned	NOT	NULL								COMMENT	'연혁',
	CONTENTS						text																			COMMENT	'내용',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (HISTORY_NO)
	)	TYPE=MyISAM COMMENT	=	'연혁 마스터';	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================


	function selectHistory($db, $history_no) {

		$query = "SELECT HISTORY_NO, CONTENTS, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_HISTORY WHERE HISTORY_NO	= '$history_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateHistory($db, $contents, $up_adm, $history_no) {
		
		$query="UPDATE TBL_HISTORY SET 
														 CONTENTS			= '$contents', 
														 UP_ADM				= '$up_adm',
														 UP_DATE			= now()
											 WHERE HISTORY_NO				= '$history_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
?>