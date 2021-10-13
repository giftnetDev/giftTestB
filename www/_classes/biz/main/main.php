<?

	# =============================================================================
	# File Name    : main.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.07.08
	# Modify Date  : 
	#	Copyright : Copyright @minumsa Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_COMPANY, TBL_PUBLISHING
	#=========================================================================================================

	#=========================================================================================================
	# End Table
	#=========================================================================================================


	function listCompanyPublishing($db, $listCnt) {

		$query = "	SELECT * FROM (
									SELECT CP_NO AS SEQ, CP_NM AS NM, REG_DATE, '기업' AS TYPE
										FROM TBL_COMPANY
									 WHERE DEL_TF = 'N'

									UNION

									SELECT PB_NO AS SEQ, PB_NM AS NM, REG_DATE, '출판사' AS TYPE
										FROM TBL_PUBLISHING 
									 WHERE DEL_TF = 'N'

									) AA
									 ORDER BY AA.REG_DATE DESC	
									 LIMIT $listCnt
									";
								
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listReqFixMain($db) {

		$query = "SELECT B.BOOK_NM, FC.BOOK_NO, FC.COMMENT_NO, FC.COMMENT_PO, FC.COMMENT_RE, FC.COMMENT_DE, FC.PB_NO, FC.CONTENTS, 
										 FC.USE_TF, FC.DEL_TF, FC.REG_ADM, FC.REG_DATE, FC.UP_ADM, FC.UP_DATE, FC.DEL_ADM, FC.DEL_DATE,
										 AD.ADM_NM, AD.ADM_ID, P.PB_NM,
										 (SELECT DCODE_NM FROM TBL_CODE_DETAIL WHERE DCODE = AD.ADM_TYPE AND PCODE = 'ADMIN_TYPE') AS ADMIN_TYPE_NM
								FROM TBL_BOOK B, TBL_BOOK_FIX_COMMENT FC, TBL_ADMIN AD, TBL_PUBLISHING P 
							 WHERE B.BOOK_NO = FC.BOOK_NO
								 AND FC.REG_ADM = AD.ADM_NO
								 AND FC.PB_NO = P.PB_NO
								 AND FC.DEL_TF = 'N' 
								 AND FC.COMMENT_DE = '1' ";
		
		$query .= " ORDER BY FC.COMMENT_PO LIMIT 2";
		
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

	function listFixReqNewBook($db, $use_tf, $del_tf, $nPage, $nRowCount) {

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, B.BOOK_NO, B.PB_NO, B.BOOK_NM, B.ISBN, B.CONTENTS_TYPE, B.PUBLISHING_TYPE, B.MEDIA_TYPE, B.ADULT_TF, 
										 B.PAPER_PB_DATE, B.EBOOK_PB_DATE, B.PAPER_COST, B.BOOK_IMG_NM, B.CATE_NO, B.BOOK_INFO, B.BOOK_CONTENTS, 
										 B.REQ_ADM, B.REQ_DATE, B.REQ_CONFIRM_DATE, B.BOOK_STATE, B.USE_TF, B.DEL_TF, B.REG_ADM, B.REG_DATE, B.UP_ADM, B.UP_DATE, B.DEL_ADM, B.DEL_DATE,
										 P.PB_NM,
										 
										 (SELECT W.WRITER_NM_HAN 
												FROM TBL_WRITER W, TBL_BOOK_WRITER BW 
											 WHERE W.WRITER_NO = BW.WRITER_NO 
											   AND BW.BOOK_NO = B.BOOK_NO
											 ORDER BY DIS_SEQ_NO LIMIT 1) as WRITER_NM,

										 (SELECT COUNT(WRITER_NO)
												FROM TBL_BOOK_WRITER
											 WHERE BOOK_NO = B.BOOK_NO) as WRITER_CNT,

										 (SELECT DCODE_NM
												FROM TBL_BOOK_WRITER BW, TBL_CODE_DETAIL CD
											 WHERE BW.WRITER_ROLL = CD.DCODE
											   AND CD.PCODE = 'WRITER_ROLL'
										  ORDER BY DIS_SEQ_NO LIMIT 1) as WRITER_ROLL,

										 (SELECT COUNT(FILE_NO) FROM TBL_BOOK_FILE WHERE BOOK_NO = B.BOOK_NO AND FILE_STATE = 'req') AS REQ_CNT,
										 (SELECT COUNT(FILE_NO) FROM TBL_BOOK_FILE WHERE BOOK_NO = B.BOOK_NO AND FILE_STATE = 'res') AS RES_CNT,
										 REQ_DATE, REQ_CONFIRM_DATE,
										 (SELECT ADM_NM FROM TBL_ADMIN WHERE ADM_NO = B.REQ_ADM) AS REQ_ADM_NM,
										 (SELECT DCODE_NM FROM TBL_CODE_DETAIL WHERE DCODE = B.BOOK_STATE AND PCODE = 'BOOK_STATE') AS BOOK_STATE_NM
								FROM TBL_BOOK B , TBL_PUBLISHING P WHERE B.PB_NO = P.PB_NO ";


			$query .= " AND B.BOOK_STATE IN ('00','10','50') ";

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}
		
		$query .= " ORDER BY B.REG_DATE desc limit ".$offset.", ".$nRowCount;
		
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


	//goods/goods.php 에서 옮겨옴
	function selectDisplayMainGoods($db, $display_idx) {

		$query = "SELECT * FROM TBL_DISPLAY where DISPLAY_IDX = '$display_idx' ";
		
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



?>