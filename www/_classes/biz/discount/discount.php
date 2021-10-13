<?

	# =============================================================================
	# File Name    : discount.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.08.16
	# Modify Date  : 
	#	Copyright : Copyright @�Ƹ����� Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_ADMIN
	#=========================================================================================================

	/*
	CREATE TABLE IF NOT EXISTS `TBL_DISCOUNT` (
	DC_NO						int(11) unsigned NOT NULL auto_increment	COMMENT '���� �Ϸù�ȣ',
	DC_CATE					varchar(12) NOT NULL default ''						COMMENT '���� ����',
	TITLE						varchar(20) NOT NULL default ''						COMMENT '���� ',
	MEMO						varchar(150) NOT NULL default ''					COMMENT '�޸�',
	DC_FROM					varchar(12) NOT NULL default ''						COMMENT '������',
	DC_TO						varchar(12) NOT NULL default ''						COMMENT '������',
	DC_RATE					varchar(12) NOT NULL default ''						COMMENT '������',
	DC_RATE_MEMBER	varchar(12) NOT NULL default ''						COMMENT 'ȸ��������',
	DC_RATE_EMP			varchar(12) NOT NULL default ''						COMMENT '����������',
	USE_TF					char(1) NOT NULL default 'Y'							COMMENT '���	���� ���(Y),������(N)',
	DEL_TF					char(1) NOT NULL default 'N'							COMMENT '����	���� ����(Y),���(N)',
	REG_ADM					int(11) unsigned default NULL							COMMENT '���	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	REG_DATE				datetime default NULL											COMMENT '�����',
	UP_ADM					int(11) unsigned default NULL							COMMENT '����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	UP_DATE					datetime default NULL											COMMENT '������',
	DEL_ADM					int(11) unsigned default NULL							COMMENT '����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	DEL_DATE				datetime default NULL											COMMENT '������',
  PRIMARY KEY  (DC_NO)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr COMMENT='���� ������'  ;
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	function listDiscount($db, $dc_cate, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntDiscount($db, $dc_cate, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, DC_NO, DC_CATE, TITLE, MEMO, DC_FROM, DC_TO, DC_RATE, DC_RATE_MEMBER, DC_RATE_EMP, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_DISCOUNT A WHERE 1 = 1 ";
		
		if ($dc_cate <> "") {
			$query .= " AND DC_CATE = '".$dc_cate."' ";
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
		
		$query .= " ORDER BY DC_NO desc limit ".$offset.", ".$nRowCount;

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


	function totalCntDiscount ($db, $dc_cate, $use_tf, $del_tf, $search_field, $search_str) {

		$query ="SELECT COUNT(*) CNT FROM TBL_DISCOUNT WHERE 1 = 1 ";

		if ($dc_cate <> "") {
			$query .= " AND DC_CATE = '".$dc_cate."' ";
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

	function insertDiscount($db, $dc_cate, $title, $memo, $dc_from, $dc_to, $dc_rate, $dc_rate_member, $dc_rate_emp, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_DISCOUNT (DC_CATE, TITLE, MEMO, DC_FROM, DC_TO, DC_RATE, DC_RATE_MEMBER, DC_RATE_EMP, USE_TF, REG_ADM, REG_DATE) 
											 values ('$dc_cate', '$title', '$memo', '$dc_from', '$dc_to', '$dc_rate', '$dc_rate_member', '$dc_rate_emp', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateDiscount($db, $dc_cate, $title, $memo, $dc_from, $dc_to, $dc_rate, $dc_rate_member, $dc_rate_emp, $use_tf, $up_adm, $dc_no) {
		
		$query="UPDATE TBL_DISCOUNT SET 
									 DC_CATE				= '$dc_cate', 
									 TITLE					= '$title', 
									 MEMO						= '$memo', 
									 DC_FROM				= '$dc_from', 
									 DC_TO					= '$dc_to', 
									 DC_RATE				= '$dc_rate', 
									 DC_RATE_MEMBER	= '$dc_rate_member', 
									 DC_RATE_EMP		= '$dc_rate_emp', 
									 USE_TF					= 'Y',
									 UP_ADM					= '$up_adm',
									 UP_DATE				= now()
						 WHERE DC_NO					= '$dc_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteDiscount($db, $del_adm, $dc_no) {

		$query="UPDATE TBL_DISCOUNT SET 
											 DEL_TF				= 'Y',
											 DEL_ADM			= '$del_adm',
											 DEL_DATE			= now()														 
								 WHERE DC_NO					= '$dc_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function selectDiscount($db, $dc_no) {

		$query = "SELECT DC_NO, DC_CATE, TITLE, MEMO, DC_FROM, DC_TO, DC_RATE, DC_RATE_MEMBER, DC_RATE_EMP, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_DISCOUNT WHERE DC_NO = '$dc_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getDiscountList($db, $dc_cate, $this_date) {

		$query = "SELECT DC_NO, DC_CATE, TITLE, MEMO, DC_FROM, DC_TO, DC_RATE, DC_RATE_MEMBER, DC_RATE_EMP, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_DISCOUNT A WHERE USE_TF = 'Y' AND DEL_TF = 'N' ";

		if ($dc_cate <> "") {
			$query .= " AND DC_CATE >= '".$dc_cate."' ";
		}

		if ($this_date <> "") {
			$query .= " AND DC_TO >= '".$this_date."' ";
		}
		
		$query .= " ORDER BY DC_NO asc ";

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
?>