<?

	# =============================================================================
	# File Name    : award.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.08.16
	# Modify Date  : 
	#	Copyright : Copyright @�Ƹ����� Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_AWARD
	#=========================================================================================================

	/*
	CREATE TABLE IF	NOT	EXISTS TBL_AWARD (
	AWARD_NO					int(11) unsigned	NOT	NULL									COMMENT	'AWARD',
	CONTENTS						text																			COMMENT	'����',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'���	���� ���(Y),������(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'����	���� ����(Y),���(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'���	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'�����',
	UP_ADM							int(11)	unsigned													COMMENT	'����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'������',
	DEL_ADM							int(11)	unsigned													COMMENT	'����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'������',
	PRIMARY	KEY	 (AWARD_NO)
	)	TYPE=MyISAM COMMENT	=	'AWARD ������';	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================


	function selectAward($db, $award_no) {

		$query = "SELECT AWARD_NO, CONTENTS, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_AWARD WHERE AWARD_NO	= '$award_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateAward($db, $contents, $up_adm, $award_no) {
		
		$query="UPDATE TBL_AWARD SET 
														 CONTENTS			= '$contents', 
														 UP_ADM				= '$up_adm',
														 UP_DATE			= now()
											 WHERE AWARD_NO				= '$award_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //Award.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
?>