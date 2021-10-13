<?

	# =============================================================================
	# File Name    : store.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.25
	# Modify Date  : 
	#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_STORE
	#=========================================================================================================
	
	/*
	CREATE TABLE IF	NOT	EXISTS TBL_STORE (
	STORE_NO						int(11) unsigned	NOT	NULL								COMMENT	'����	�Ϸù�ȣ',
	SITE_NO							int(11)	unsigned													COMMENT	'����Ʈ �Ϸù�ȣ',
	STORE_TYPE					varchar(20) NOT NULL	default	''					COMMENT	'���� ���� ',
	STORE_CATE					varchar(20) NOT NULL	default	''					COMMENT	'���� ���� ',
	STORE_NM						varchar(150) NOT NULL	default	''					COMMENT	'���� ��',
	STORE_URL						varchar(150) NOT NULL	default	''					COMMENT	'���� URL',
	ZIPCODE							varchar(6) NOT NULL	default	''						COMMENT	'�����ȣ ',
	ADDR01							varchar(50) NOT NULL	default	''					COMMENT	'�ּ�',
	ADDR02							varchar(50) NOT NULL	default	''					COMMENT	'�ּ�',
	PHONE01							varchar(10) NOT NULL	default	''					COMMENT	'��ȭ��ȣ',
	PHONE02							varchar(10) NOT NULL	default	''					COMMENT	'��ȭ��ȣ',
	PHONE03							varchar(10) NOT NULL	default	''					COMMENT	'��ȭ��ȣ',
	STORE_HOUR					varchar(60) NOT NULL	default	''					COMMENT	'�����ð�',
	CONTENTS						text																			COMMENT	'����',
	FILE_NM							varchar(150) NOT NULL	default	''					COMMENT	'÷��	���ϸ�',
	FILE_RNM						varchar(150) NOT NULL	default	''					COMMENT	'÷��	���� ����	���ϸ�',
	FILE_PATH						varchar(150) NOT NULL	default	''					COMMENT	'����	���',
	FILE_SIZE						int(11)																		COMMENT	'����	������',
	FILE_EXT						varchar(5) NOT NULL	default	''						COMMENT	'����	Ȯ����',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'���	���� ���(Y),������(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'����	���� ����(Y),���(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'���	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'�����',
	UP_ADM							int(11)	unsigned													COMMENT	'����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'������',
	DEL_ADM							int(11)	unsigned													COMMENT	'����	������ �Ϸù�ȣ TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'������',
	PRIMARY	KEY	 (STORE_NO)
	)	TYPE=MyISAM COMMENT	=	'���� ������';

	*/
	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#STORE_NO, SITE_NO, STORE_TYPE, STORE_CATE, STORE_NM, STORE_URL, ZIPCODE, ADDR01, ADDR02, PHONE01, PHONE02, PHONE03, STORE_HOUR, CONTENTS,
	#FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

	function listStore($db, $site_no, $store_type, $store_cate, $store_url, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, STORE_NO, SITE_NO, STORE_TYPE, STORE_CATE, STORE_NM, STORE_URL, 
										 ZIPCODE, ADDR01, ADDR02, PHONE01, PHONE02, PHONE03, STORE_HOUR, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_STORE WHERE 1 = 1 ";

		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}

		if ($store_url <> "") {
			$query .= " AND STORE_URL = '".$store_url."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}
		
		if ($search_str <> "") {

			if ($search_field == "ADDR") {
				$query .= " AND (ADDR01 like '%".$search_str."%' OR ADDR02 like '%".$search_str."%' ) ";			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}

		}
		
		$query .= " ORDER BY STORE_NM asc limit ".$offset.", ".$nRowCount;

//		echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntStore($db, $site_no, $store_type, $store_cate, $store_url, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_STORE WHERE 1 = 1 ";
		
		if ($site_no <> "") {
			$query .= " AND SITE_NO = '".$site_no."' ";
		}

		if ($store_type <> "") {
			$query .= " AND STORE_TYPE = '".$store_type."' ";
		}

		if ($store_cate <> "") {
			$query .= " AND STORE_CATE = '".$store_cate."' ";
		}

		if ($store_url <> "") {
			$query .= " AND STORE_URL = '".$store_url."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ADDR") {
				$query .= " AND (ADDR01 like '%".$search_str."%' OR ADDR02 like '%".$search_str."%' ) ";			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}

		}

//		echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertStore($db, $site_no, $store_type, $store_cate, $store_nm, $store_url, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $store_hour, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(STORE_NO),0) AS MAX_NO FROM TBL_STORE WHERE 1= 1 ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$new_store_no = $rows[0] + 1;
		
		$query5="INSERT INTO TBL_STORE (STORE_NO, SITE_NO, STORE_TYPE, STORE_CATE, STORE_NM, STORE_URL, ZIPCODE, ADDR01, ADDR02, PHONE01, PHONE02, PHONE03, STORE_HOUR, CONTENTS,
																		FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, USE_TF, REG_ADM, REG_DATE) 
														values ('$new_store_no', '$site_no', '$store_type', '$store_cate', '$store_nm', '$store_url', '$zipcode', '$addr01', '$addr02', 
																		'$phone01', '$phone02', '$phone03', '$store_hour', '$contents', '$file_nm', '$file_rnm', '$file_path', '$file_size', '$file_ext', 
																		'$use_tf', '$reg_adm', now()); ";
		
		//echo $query5;

		//exit;

		if(!mysql_query($query5,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}



	function selectStore($db, $store_no) {

		$query = "SELECT STORE_NO, SITE_NO, STORE_TYPE, STORE_CATE, STORE_NM, STORE_URL, ZIPCODE, ADDR01, ADDR02, PHONE01, PHONE02, PHONE03, STORE_HOUR, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_STORE WHERE STORE_NO = '$store_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function updateStore($db, $site_no, $store_type, $store_cate, $store_nm, $store_url, $zipcode, $addr01, $addr02, $phone01, $phone02, $phone03, $store_hour, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $use_tf, $up_adm, $store_no) {

		$query = "UPDATE TBL_STORE SET 
													SITE_NO				=	'$site_no',
													STORE_TYPE		=	'$store_type',
													STORE_CATE		=	'$store_cate',
													STORE_NM			=	'$store_nm',
													STORE_URL			=	'$store_url',
													ZIPCODE				=	'$zipcode',
													ADDR01				=	'$addr01',
													ADDR02				=	'$addr02',
													PHONE01				=	'$phone01',
													PHONE02				=	'$phone02',
													PHONE03				=	'$phone03',
													STORE_HOUR		=	'$store_hour',
													CONTENTS			=	'$contents',
													FILE_NM				=	'$file_nm',
													FILE_RNM			=	'$file_rnm',
													FILE_PATH			=	'$file_path',
													FILE_SIZE			=	'$file_size',
													FILE_EXT			=	'$file_ext',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
											 WHERE STORE_NO = '$store_no' ";
		
		//echo $query."<br>";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateStoreUseTF($db, $use_tf, $up_adm, $bb_code, $bb_no) {
		
		$query="UPDATE TBL_STORE SET 
							 USE_TF					= '$use_tf',
							 UP_ADM					= '$up_adm',
							 UP_DATE					= now()
				 WHERE STORE_NO = '$store_no'  ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteStore($db, $del_adm, $store_no) {

		$query="UPDATE TBL_STORE SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE STORE_NO = '$store_no'  ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

?>