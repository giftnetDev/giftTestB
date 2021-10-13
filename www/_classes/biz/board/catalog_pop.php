<?

	# =============================================================================
	# File Name    : catalog_pop.php
	# Modlue       : 
	# Writer       : KBJ
	# Create Date  : 20210407
	# Modify Date  : 
	# =============================================================================

	function catalog_pop_list($db, $search_field, $search_str, $nPage, $nRowCount) 
	{
		$offset = $nRowCount*($nPage-1);
		$query = "set @rownum = ".$offset."; ";

		mysql_query($query,$db);

		$query = " SELECT @ROWNUM := @ROWNUM +1 AS RN
						, A.CTLPOP_NO
						, A.TITLE
						, A.CTLPOP_START
						, A.CTLPOP_END
						, A.HIT_CNT
						, A.FILE_NM
						, A.FILE_RNM
						, A.FILE_PATH
						, A.FILE_SIZE
						, A.FILE_EXT
						, A.DEL_TF
		 				, (SELECT C.ADM_NAME FROM TBL_ADMIN_INFO C WHERE C.ADM_NO = A.REG_ADM) AS ADM_NAME
						, A.REG_ADM
						, A.REG_DATE  		 
						, A.UP_ADM
						, A.UP_DATE
						, A.DEL_ADM
						, A.DEL_DATE
				     FROM T_CATALOG_POP A
					WHERE DEL_TF = 'N' 
				";

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY CTLPOP_NO DESC limit ".$offset.", ".$nRowCount;

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

	function totalCntCatalogPop($db, $search_field, $search_str)
	{
		$query ="SELECT COUNT(*) CNT FROM T_CATALOG_POP WHERE DEL_TF = 'N' ";
		
		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function catalog_pop_Sel($db, $catalogNo) 
	{
		hitCkUpdate($db,$catalogNo);

		$query = " SELECT A.CTLPOP_NO
						, A.TITLE
						, A.CTLPOP_START
						, A.CTLPOP_END
						, A.HIT_CNT
						, A.FILE_NM
						, A.FILE_RNM
						, A.FILE_PATH
						, A.FILE_SIZE
						, A.FILE_EXT
						, A.DEL_TF
		 				, (SELECT C.ADM_NAME FROM TBL_ADMIN_INFO C WHERE C.ADM_NO = A.REG_ADM) AS ADM_NAME
						, A.REG_ADM
						, A.REG_DATE  		 
						, A.UP_ADM
						, A.UP_DATE
						, A.DEL_ADM
						, A.DEL_DATE
				     FROM T_CATALOG_POP A
					WHERE DEL_TF 	= 'N' 
					  AND CTLPOP_NO = '$catalogNo'
				";

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

	function hitCkUpdate($db, $catalogNo) 
	{		
		$query = "	UPDATE T_CATALOG_POP 
					   SET HIT_CNT = HIT_CNT + 1 
					 WHERE CTLPOP_NO = '$catalogNo'
					   AND DEL_TF = 'N' 
					";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function pop_Sel_catalog($db) 
	{
		$query = " SELECT CTLPOP_NO
						, DATE(NOW()) AS NOW_DATE
						, TITLE	
						, DATE(CTLPOP_START) AS START_DATE
						, DATE(CTLPOP_END) AS END_DATE
						, FILE_NM
						, FILE_RNM
						, CONCAT(FILE_PATH, FILE_NM) AS FILEPATH
					 FROM T_CATALOG_POP 
					WHERE DATE(NOW()) BETWEEN DATE(CTLPOP_START) AND DATE(CTLPOP_END)
					  AND DEL_TF = 'N'
					ORDER BY CTLPOP_NO DESC, DATE(CTLPOP_END) DESC LIMIT 1
				";

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

	function pop_Sel_catalog_cnt($db)
	{
		$query ="  SELECT COUNT(1) AS CNT
					 FROM T_CATALOG_POP 
					WHERE DATE(NOW()) BETWEEN DATE(CTLPOP_START) AND DATE(CTLPOP_END)
					  AND DEL_TF = 'N'
					";
		
		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

?>