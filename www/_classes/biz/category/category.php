<?

	# =============================================================================
	# File Name    : category.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.08.16
	# Modify Date  : 
	#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_CATEGORY
	#=========================================================================================================

	/*
	CREATE TABLE IF NOT EXISTS `TBL_CATEGORY` (
  `CATE_NO` int(11) unsigned NOT NULL default '0' COMMENT '카테고리 SEQ',
  `CATE_CD` varchar(10) NOT NULL default '' COMMENT '카테고리코드',
  `CATE_NAME` varchar(50) NOT NULL default '' COMMENT '카테고리명',
  `CATE_MEMO` text NOT NULL default '' COMMENT '카테고리 설명',
  `CATE_SEQ01` varchar(3) NOT NULL default '' COMMENT '카테고리 순서 1',
  `CATE_SEQ02` varchar(3) NOT NULL default '' COMMENT '카테고리 순서 2',
  `CATE_SEQ03` varchar(3) NOT NULL default '' COMMENT '카테고리 순서 3',
  `CATE_SEQ04` varchar(3) NOT NULL default '' COMMENT '카테고리 순서 4',
  `CATE_FLAG` char(1) NOT NULL default '' COMMENT '카테고리 상태',
  `CATE_CODE` varchar(10) NOT NULL default '' COMMENT '카테고리 코드',
  `CATE_IMG` varchar(50) NOT NULL default '' COMMENT '카테고리 이미지',
  `CATE_IMG_OVER` varchar(50) NOT NULL default '' COMMENT '카테고리 이미지 2',
  `USE_TF` char(1) NOT NULL default 'Y' COMMENT '사용	여부 사용(Y),사용안함(N)',
  `DEL_TF` char(1) NOT NULL default 'N' COMMENT '삭제	여부 삭제(Y),사용(N)',
  `REG_ADM` int(11) unsigned default NULL COMMENT '등록	관리자 일련번호 TBL_ADMIN ADM_NO',
  `REG_DATE` datetime default NULL COMMENT '등록일',
  `UP_ADM` int(11) unsigned default NULL COMMENT '수정	관리자 일련번호 TBL_ADMIN ADM_NO',
  `UP_DATE` datetime default NULL COMMENT '수정일',
  `DEL_ADM` int(11) unsigned default NULL COMMENT '삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
  `DEL_DATE` datetime default NULL COMMENT '삭제일'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='카테고리 마스터';
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================


	function listCategory($db, $category, $use_tf, $del_tf, $search_field, $search_str) {
		
		$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ,
										 CATE_NO, CATE_CD, CATE_NAME, CATE_MEMO, CATE_FLAG, CATE_SEQ01, CATE_SEQ02, CATE_SEQ03, CATE_SEQ04, CATE_CODE
							FROM TBL_CATEGORY WHERE 1 = 1 ";

		
		if ($category <> "") {
			$query .= " AND CATE_CD like '".$category."%' ";
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

		$query .= " ORDER BY SEQ ASC ";
		
		echo $query;

		$result = mysql_query($query,$db);
		$record = array();

		for($i=0;$i < mysql_num_rows($result);$i++) {
			
			$record[$i] = sql_result_array($result,$i);
		}
		return $record;
	}

	function dupCategory ($db, $cate_code) {
		
		if ($cate_code <> "") {
			$query ="SELECT COUNT(*) CNT FROM TBL_CATEGORY WHERE 1 = 1 AND DEL_TF = 'N' ";
		
			if ($cate_code <> "") {
				$query .= " AND CATE_CODE = '".$cate_code."' ";
			}

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
		
			if ($rows[0] == 0) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
				
	}


	/*	카테고리 등록*/
	
	function insertCategory($db, $m_level, $m_seq01, $m_seq02, $m_seq03, $cate_name, $cate_memo, $cate_flag, $cate_code, $cate_img, $cate_img_over, $use_tf, $reg_adm) {

		$iMax = "0";	

		$sSeq01		= "";
		$sSeq02		= "";
		$sSeq03		= "";
		$sSeq04		= "";
		$sSeq_01	= "";
		$sSeq_02	= "";
		$sSeq_03	= "";
		$sSeq_04	= "";
		$sCate_cd	= "";
	
		if ($cate_code <> "") {
			$query = "SELECT COUNT(*) cnt FROM TBL_CATEGORY WHERE CATE_CODE = '$cate_code' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);

			if ($row["cnt"] > 0) {
				return "2";
				exit;
			}
		}
		
		if (strlen($m_level) == 0) { 
			
			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(CATE_CD,1,2)),0) + 1),-2) as M_CD 
									FROM TBL_CATEGORY ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq02 = "00";
			$sSeq03 = "00";
			$sSeq04 = "00";

			$sCate_cd = $row["M_CD"];

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(CATE_SEQ01),0) + 1),-2) as SEQ 
									FROM TBL_CATEGORY ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_01 = $row["SEQ"];

			$sSeq_02 = "00";
			$sSeq_03 = "00";
			$sSeq_04 = "00";

		}

		if (strlen($m_level) == 2) { 
			
			 $sSeq01 = $m_level;

			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(CATE_CD,3,2)),0) + 1),-2) as M_CD 
									FROM TBL_CATEGORY 
								 WHERE substring(CATE_CD,1,2) = '$m_level' ";
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);

			$sSeq02 = $row["M_CD"];
			$sSeq03 = "00";
			$sSeq04 = "00";

			$sCate_cd = $sSeq01.$sSeq02;

			$sSeq_01 = $m_seq01;

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(CATE_SEQ02),0) + 1),-2) as SEQ 
									FROM TBL_CATEGORY 
								 WHERE substring(CATE_CD,1,2) = '$m_level' ";

			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_02 = $row["SEQ"];
			$sSeq_03 = "00";
			$sSeq_04 = "00";

		}

		if (strlen($m_level) == 4) { 

			$sSeq01 = substr($m_level,0,2);
			$sSeq02 = substr($m_level,2,2);
			
			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(CATE_CD,5,2)),0) + 1),-2) as M_CD 
									FROM TBL_CATEGORY 
								 WHERE substring(CATE_CD,1,2) = '".substr($m_level,0,2)."' 
									 and substring(CATE_CD,3,2) = '".substr($m_level,2,2)."' ";
						
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq03 = $row["M_CD"];
			$sSeq04 = "00";
			
			$sCate_cd =  $sSeq01.$sSeq02.$sSeq03;

			$sSeq_01 = $m_seq01;
			$sSeq_02 = $m_seq02;

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(CATE_SEQ03),0) + 1),-2) as SEQ 
									FROM TBL_CATEGORY 
								 WHERE substring(CATE_CD,1,2) = '".substr($m_level,0,2)."' 
									 and substring(CATE_CD,3,2) = '".substr($m_level,2,2)."' ";
			
			#echo $query;

			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_03 = $row["SEQ"];
			$sSeq_04 = "00";

		}
		
		if (strlen($m_level) == 6) { 

			$sSeq01 = substr($m_level,0,2);
			$sSeq02 = substr($m_level,2,2);
			$sSeq03 = substr($m_level,4,2);
			
			$query = "SELECT substring(CONCAT('00', ifnull(max(substring(CATE_CD,7,2)),0) + 1),-2) as M_CD 
									FROM TBL_CATEGORY 
								 WHERE substring(CATE_CD,1,2) = '".substr($m_level,0,2)."' 
									 and substring(CATE_CD,3,2) = '".substr($m_level,2,2)."' 
									 and substring(CATE_CD,5,2) = '".substr($m_level,4,2)."' ";
						
			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq04 = $row["M_CD"];
			
			$sCate_cd =  $sSeq01.$sSeq02.$sSeq03.$sSeq04;

			$sSeq_01 = $m_seq01;
			$sSeq_02 = $m_seq02;
			$sSeq_03 = $m_seq03;

			$query = "SELECT substring(CONCAT('00', ifnull(MAX(CATE_SEQ04),0) + 1),-2) as SEQ 
								 FROM TBL_CATEGORY 
								WHERE substring(CATE_CD,1,2) = '".substr($m_level,0,2)."' 
									and substring(CATE_CD,3,2) = '".substr($m_level,2,2)."' 
									and substring(CATE_CD,5,2) = '".substr($m_level,4,2)."' ";
			
			#echo $query;

			$result = mysql_query($query,$db);
			$row = mysql_fetch_array($result);
			
			$sSeq_04 = $row["SEQ"];

		}

		$query = "SELECT IFNULL(MAX(CATE_NO),0) + 1  as IMAX FROM TBL_CATEGORY ";
		$result = mysql_query($query,$db);
		$row = mysql_fetch_array($result);
			
		$iMax = $row["IMAX"];
		
		$query = "INSERT INTO TBL_CATEGORY (CATE_NO, CATE_CD, CATE_NAME, CATE_MEMO, CATE_SEQ01, CATE_SEQ02, CATE_SEQ03, CATE_SEQ04, 
												CATE_FLAG, CATE_CODE, CATE_IMG, CATE_IMG_OVER, USE_TF, REG_ADM, REG_DATE)
							VALUES	('$iMax', '$sCate_cd', '$cate_name', '$cate_memo', '$sSeq_01', '$sSeq_02', '$sSeq_03', '$sSeq_04', 
											 '$cate_flag', '$cate_code','$cate_img','$cate_img_over','$use_tf', '$reg_adm', now()); ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function selectCategory($db, $cate_no) {

		$query = "SELECT CATE_NO, CATE_NAME, CATE_MEMO, CATE_FLAG, CATE_CD, CATE_CODE,CATE_IMG,CATE_IMG_OVER 
								FROM TBL_CATEGORY 
							 WHERE CATE_NO = '$cate_no' ";
		
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

	function updateCategory($db, $cate_name, $cate_memo, $cate_flag, $cate_code, $cate_img, $cate_img_over, $use_tf, $up_adm, $cate_no) {

		$query="UPDATE TBL_CATEGORY SET 
									 CATE_NAME			= '$cate_name', 
									 CATE_MEMO			= '$cate_memo', 
									 CATE_FLAG			= '$cate_flag', 
									 CATE_CODE			= '$cate_code', 
									 CATE_IMG				= '$cate_img', 
									 CATE_IMG_OVER	= '$cate_img_over', 
									 USE_TF					= '$use_tf',
									 UP_ADM					= '$up_adm',
									 UP_DATE				= now()
						 WHERE CATE_NO				= '$cate_no' ";

		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteCategory($db, $del_adm, $cate_no) {

		$query="SELECT CATE_CD FROM TBL_CATEGORY WHERE CATE_NO			= '$cate_no' ";
		$result = mysql_query($query,$db);
		$row = mysql_fetch_array($result);
			
		$rs_cate_cd = $row["CATE_CD"];
		
		#echo $rs_CATE_cd;

		//지워진 사용자코드 카테고리와의 충돌때문에 삭제시 사용자 코드 초기화
		$query="UPDATE TBL_CATEGORY SET 
												 CATE_CODE          = '',
												 DEL_TF				= 'Y',
												 DEL_ADM			= '$del_adm',
												 DEL_DATE			= now()														 
									 WHERE CATE_CD			like '".$rs_cate_cd."%' ";

		mysql_query($query,$db);

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateCategoryOrder($db, $arr_cate_no, $cate_level, $seq_no) {

		$query="UPDATE TBL_CATEGORY SET " .$cate_level. " = '" .$seq_no. "' WHERE CATE_NO IN	".$arr_cate_no;

		#echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}



	function listSubCategory($db, $category, $exept_cate) {
		
		$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ,
										 CATE_NO, CATE_CD, CATE_NAME, CATE_MEMO, CATE_FLAG, CATE_SEQ01, CATE_SEQ02, CATE_SEQ03, CATE_SEQ04, CATE_CODE
							FROM TBL_CATEGORY WHERE CATE_CD like '".$category."%' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
	
		if($exept_cate <> "")
			$query .= " AND CATE_CD != '$exept_cate' ";

		$query .= " ORDER BY SEQ ASC ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$record = array();

		for($i=0;$i < mysql_num_rows($result);$i++) {
			
			$record[$i] = sql_result_array($result,$i);
		}
		return $record;
	}

?>