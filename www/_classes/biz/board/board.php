<?

	# =============================================================================
	# File Name    : board.php
	# Modlue       : 
	# Writer       : Park Chan Ho 
	# Create Date  : 2009.06.25
	# Modify Date  : 
	#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
	# =============================================================================

	#=========================================================================================================
	# Used Table TBL_BOARD
	#=========================================================================================================
	
	/*
	CREATE TABLE IF	NOT	EXISTS TBL_BOARD (
	BB_CODE							varchar(5) NOT NULL	default	''						COMMENT	'게시판	코드',
	BB_NO								int(10)	NOT	NULL default '1'							COMMENT	'게시판	번호',
	BB_PO								int(10)	NOT	NULL default '1'							COMMENT	'게시물	포지션 번호',
	BB_RE								int(10)	NOT	NULL default '1'							COMMENT	'게시물	답변 번호',
	BB_DE								int(10)	NOT	NULL default '1'							COMMENT	'게시물	뎁스 번호',
	CATE_01							varchar(50)	default	NULL									COMMENT	'임시	1',
	CATE_02							varchar(50)	default	NULL,
	CATE_03							varchar(50)	default	NULL,
	CATE_04							varchar(50)	default	NULL,
	WRITER_NM						varchar(20)	NOT	NULL default ''						COMMENT	'작성자',
	WRITER_PW						varchar(20)	NOT	NULL default ''						COMMENT	'작성자	비밀번호',
	EMAIL								varchar(100) default NULL									COMMENT	'작성자	이메일',
	HOMEPAGE						varchar(100) default NULL									COMMENT	'작성자	홈페이지',
	TITLE								varchar(100) default NULL									COMMENT	'제목',
	HIT_CNT							int(11)	default	'0'												COMMENT	'조회수',
	REF_IP							varchar(20)	default	NULL									COMMENT	'관련	URL',
	RECOMM							int(11)	default	'0'												COMMENT	'추천수',
	CONTENTS						text																			COMMENT	'내용',
	FILE_NM							varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일명',
	FILE_RNM						varchar(150) NOT NULL	default	''					COMMENT	'첨부	파일 실제	파일명',
	FILE_PATH						varchar(150) NOT NULL	default	''					COMMENT	'파일	경로',
	FILE_SIZE						int(11)																		COMMENT	'파일	사이즈',
	FILE_EXT						varchar(5) NOT NULL	default	''						COMMENT	'파일	확장자',
	KEYWORD							varchar(200) NOT NULL	default	''					COMMENT	'키워드',
	REPLY								text																			COMMENT	'답변',
	REPLY_ADM						int(11)	unsigned													COMMENT	'답변	관리자 TBL_ADMIN ADM_NO',
	REPLY_DATE					datetime																	COMMENT	'답변일',
	REPLY_STATE					char(1)	default	'N'												COMMENT	'답변	상태',
	COMMENT_TF					char(1)	default	'N'												COMMENT	'답변	사용 사용(Y),사용안함(N)',
	USE_TF							char(1)	NOT	NULL default 'Y'							COMMENT	'사용	여부 사용(Y),사용안함(N)',
	DEL_TF							char(1)	NOT	NULL default 'N'							COMMENT	'삭제	여부 삭제(Y),사용(N)',
	REG_ADM							int(11)	unsigned													COMMENT	'등록	관리자 일련번호 TBL_ADMIN ADM_NO',
	REG_DATE						datetime																	COMMENT	'등록일',
	UP_ADM							int(11)	unsigned													COMMENT	'수정	관리자 일련번호 TBL_ADMIN ADM_NO',
	UP_DATE							datetime																	COMMENT	'수정일',
	DEL_ADM							int(11)	unsigned													COMMENT	'삭제	관리자 일련번호 TBL_ADMIN ADM_NO',
	DEL_DATE						datetime																	COMMENT	'삭제일',
	PRIMARY	KEY	 (BB_CODE, BB_NO)
	)	TYPE=MyISAM COMMENT	=	'게시판 마스터';
	*/

	#=========================================================================================================
	# End Table
	#=========================================================================================================

	#BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP RECOMM, CONTENTS, 
	#FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE

	function listBoard($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBoard($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, 
										 WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP, RECOMM, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_BOARD WHERE 1 = 1 ";

		
		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND REPLY_STATE = '".$reply_state."' ";
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

	function totalCntBoard($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_BOARD WHERE 1 = 1 ";
		
		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
			}
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

function listBoardComment($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBoardComment($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, 
										 WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP, RECOMM, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_BOARD WHERE 1 = 1 ";

		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
		}

		if ($reply_state <> "") {
			$query .= " AND REPLY_STATE = '".$reply_state."' ";
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

	//	echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntBoardComment($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_BOARD WHERE 1 = 1 ";
		
		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
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

	//	echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function selectPostBoard($db, $bb_code, $bb_no, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, 
										 WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP RECOMM, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF, 
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_BOARD WHERE BB_NO > '$bb_no' ";

		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
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

		$query .= " ORDER BY REG_DATE ASC limit 1";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
							
		return $record;
	}

	function selectPreBoard($db, $bb_code, $bb_no, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str) {

		$query = "SELECT BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, 
										 WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP RECOMM, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_BOARD WHERE BB_NO < '$bb_no' ";


		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (KEYWORD like '%".$keyword."%') or (TITLE like '%".$keyword."%') or (WRITER_NM like '%".$keyword."%')) ";
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
								
		$query .= " ORDER BY REG_DATE DESC limit 1";
		
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

	//공지사항, 업체게시판, 게인메세지 읽은수 체크
	function viewChkBoard($db, $bb_code, $bb_no) {
		
		$query="UPDATE TBL_BOARD SET HIT_CNT = HIT_CNT + 1 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";
	
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function insertBoard($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $reg_adm) {
		// echo"<script>alert('test');</script>";
		$query ="SELECT IFNULL(MAX(BB_NO),0) AS MAX_NO FROM TBL_BOARD WHERE BB_CODE = '$bb_code' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] <> 0) {
					
			$new_bb_no = $rows[0] + 1;

			//답변글 번호 찾기
			$query2 ="SELECT IFNULL(MAX(BB_RE),0) AS MAX_NO FROM TBL_BOARD WHERE BB_CODE = '$bb_code' ";
			$result2 = mysql_query($query2,$db);
			$rows2   = mysql_fetch_array($result2);

			$new_bb_re = $rows2[0] + 1;

			//po 최소값 찾기
			$query3 ="SELECT IFNULL(MIN(BB_PO),0) AS MAX_NO FROM TBL_BOARD WHERE BB_CODE = '$bb_code' ";
			$result3 = mysql_query($query3,$db);
			$rows3   = mysql_fetch_array($result3);

			$new_bb_po = $rows3[0] + 1;


			$query4 ="UPDATE TBL_BOARD SET BB_PO = BB_PO + 1 WHERE BB_CODE = '$bb_code' AND BB_PO > 0 ";

			mysql_query($query4,$db);
		
		} else {
		
			$new_bb_no = "1";
			$new_bb_po = "1";
			$new_bb_re = "1";
			$new_bb_de = "1";

		}
		
		$query5="INSERT INTO TBL_BOARD (BB_CODE, CATE_01, CATE_02, CATE_03, CATE_04, BB_NO, BB_PO, BB_RE, BB_DE, WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP, RECOMM, 
																	 CONTENTS, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, COMMENT_TF, USE_TF, REG_ADM, REG_DATE) 
														values ('$bb_code', '$cate_01', '$cate_02', '$cate_03', '$cate_04', '$new_bb_no', '1', '$new_bb_re', '1', '$writer_nm', '$writer_pw', 
																		'$email', '$homepage', '$title', '0', '$ref_ip', '$recomm', '$contents', '$file_nm', '$file_rnm', '$file_path', '$file_size', '$file_ext', 
																		'$keyword', '$comment_tf', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query5;

		//exit;

		if(!mysql_query($query5,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_bb_no;
		}
	}



	function selectBoard($db, $bb_code, $bb_no) {

		$query = "SELECT BB_CODE, CATE_01, CATE_02, CATE_03, CATE_04, BB_NO, BB_PO, BB_RE, BB_DE, WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP, RECOMM, CONTENTS, 
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, REF_IP
								FROM TBL_BOARD WHERE  BB_CODE = '$bb_code' AND  BB_NO = '$bb_no' ";
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


	function updateBoard($db, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $up_adm, $bb_code, $bb_no) {

		$query = "UPDATE TBL_BOARD SET 
													CATE_01				=	'$cate_01',
													CATE_02				=	'$cate_02',
													CATE_03				=	'$cate_03',
													CATE_04				=	'$cate_04',
													WRITER_NM			=	'$writer_nm',
													WRITER_PW			=	'$writer_pw',
													EMAIL					=	'$email',
													HOMEPAGE			=	'$homepage',
													TITLE					=	'$title',
													REF_IP				=	'$ref_ip',
													CONTENTS			=	'$contents',
													FILE_NM				=	'$file_nm',
													FILE_RNM			=	'$file_rnm',
													FILE_PATH			=	'$file_path',
													FILE_SIZE			=	'$file_size',
													FILE_EXT			=	'$file_ext',
													KEYWORD				=	'$keyword',
													COMMENT_TF		=	'$comment_tf',
													USE_TF				=	'$use_tf',
													UP_ADM				=	'$up_adm',
													UP_DATE				=	now()
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";
		
		//echo $query."<br>";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateBoardUseTF($db, $use_tf, $up_adm, $bb_code, $bb_no) {
		
		$query="UPDATE TBL_BOARD SET 
							USE_TF					= '$use_tf',
							UP_ADM					= '$up_adm',
							UP_DATE					= now()
				 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateBoardConfirmTF($db, $confirm_tf, $up_adm, $bb_code, $bb_no) {
		


		$query="UPDATE TBL_BOARD SET 
							REPLY_STATE					= '$confirm_tf', ";

		if($confirm_tf == 'Y')
			$query .= " REPLY_DATE = now(),	";
		else
			$query .= " REPLY_DATE = '',	";

		$query .= "					UP_ADM					= '$up_adm',
							UP_DATE					= now()
				 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateClaimExtra($db, $reply, $reply_adm, $bb_code, $bb_no) {

		$query = "UPDATE TBL_BOARD SET 
													REPLY				=	'$reply',
													REPLY_ADM		=	'$reply_adm',
													REPLY_DATE	=	now()
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";
		
		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateQnaAnswer($db, $reply, $reply_adm, $reply_state, $bb_code, $bb_no) {

		$query = "UPDATE TBL_BOARD SET 
													REPLY				=	'$reply',
													REPLY_ADM		=	'$reply_adm',
													REPLY_STATE	=	'$reply_state',
													REPLY_DATE	=	now()
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";
		
		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteBoard($db, $del_adm, $bb_code, $bb_no) {

		$query="UPDATE TBL_BOARD SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//										업체 공지사항												 ///
	////////////////////////////////////////////////////////////////////////////////////////////////////

	// 업체 공지사항의 적용/미적용 업체 리스트
	function listCompanyBoard($db, $bb_code, $bb_no, $type) {
	
		if ($type == 'NO') {
			$query = "SELECT CP_NO, CP_NM
									FROM TBL_COMPANY 
								 WHERE USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND CP_NO NOT IN (SELECT CP_NO FROM TBL_BOARD_COMPANY WHERE BB_NO = '$bb_no' AND BB_CODE = '$bb_code' ) 
								 ORDER BY CP_NM ASC ";
		} else {
			$query = "SELECT CP_NO, CP_NM
									FROM TBL_COMPANY 
								 WHERE USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND CP_NO IN (SELECT CP_NO FROM TBL_BOARD_COMPANY WHERE BB_NO = '$bb_no' AND BB_CODE = '$bb_code') 
								 ORDER BY CP_NM ASC ";
		}

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

	// 업체 공지사항의 스크립트 생성
	function makeCompanyScriptArray($db, $objname) {

		$query = "SELECT CP_NO, CP_NM, CP_TYPE
								FROM TBL_COMPANY 
							 WHERE USE_TF = 'Y'
								 AND DEL_TF = 'N'
							 ORDER BY CP_NM ASC, CP_NO ASC ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
			
		$tmp_str_no			=	"";
		$tmp_str_name		=	"";
		$tmp_str_type		=	"";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_CP_NO			= Trim($row[0]);
			$RS_CP_NM			= Trim($row[1]);
			$RS_CP_TYPE		= Trim($row[2]);

			$tmp_str_no			.= ",'".$RS_CP_NO."'";
			$tmp_str_name		.= ",'".$RS_CP_NM."'";
			$tmp_str_type		.= ",'".$RS_CP_TYPE."'";
				
		}
		
		$tmp_str_no			= substr($tmp_str_no, 1, strlen($tmp_str_no)-1);
		$tmp_str_name		= substr($tmp_str_name, 1, strlen($tmp_str_name)-1);
		$tmp_str_type		= substr($tmp_str_type, 1, strlen($tmp_str_type)-1);


		$tmp_str  = $objname."_no = new Array(".$tmp_str_no."); \n";
		$tmp_str .= $objname."_name = new Array(".$tmp_str_name."); \n";
		$tmp_str .= $objname."_type = new Array(".$tmp_str_type."); \n";

		return $tmp_str;
	}

	// 업체 공지사항의 게시물과 업체 연결
	function deleteBoardCompany($db, $bb_code, $bb_no) {

		$query="DELETE FROM TBL_BOARD_COMPANY 
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertBoardCompany($db, $bb_code, $bb_no, $cp_no) {
		
		$query="INSERT INTO TBL_BOARD_COMPANY (BB_CODE, BB_NO, CP_NO) 
														values ('$bb_code', '$bb_no', '$cp_no'); ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listBoardCompany($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $cp_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBoardCompany($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $cp_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, A.BB_CODE, A.BB_NO, A.BB_PO, A.BB_RE, A.BB_DE, A.CATE_01, A.CATE_02, A.CATE_03, A.CATE_04, 
										 A.WRITER_NM, A.WRITER_PW, A.EMAIL, A.HOMEPAGE, A.TITLE, A.HIT_CNT, A.REF_IP, A.RECOMM, A.CONTENTS,
										 A.FILE_NM, A.FILE_RNM, A.FILE_PATH, A.FILE_SIZE, A.FILE_EXT, A.KEYWORD, A.REPLY, A.REPLY_ADM, A.REPLY_DATE, A.REPLY_STATE, A.COMMENT_TF,
										 A.USE_TF, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.UP_ADM, A.UP_DATE, A.DEL_ADM, A.DEL_DATE
								FROM TBL_BOARD A, TBL_BOARD_COMPANY B WHERE A.BB_CODE = B.BB_CODE AND A.BB_NO = B.BB_NO ";

		
		if ($bb_code <> "") {
			$query .= " AND A.BB_CODE = '".$bb_code."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO= '".$cp_no."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND A.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND A.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND A.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND A.CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (A.KEYWORD like '%".$keyword."%') or (A.TITLE like '%".$keyword."%') or (A.WRITER_NM like '%".$keyword."%')) ";
		}

		if ($reply_state <> "") {
			$query .= " AND A.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY A.REG_DATE desc limit ".$offset.", ".$nRowCount;

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

	function totalCntBoardCompany($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $cp_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_BOARD A, TBL_BOARD_COMPANY B WHERE A.BB_CODE = B.BB_CODE AND A.BB_NO = B.BB_NO ";
		
		if ($bb_code <> "") {
			$query .= " AND A.BB_CODE = '".$bb_code."' ";
		}

		if ($cp_no <> "") {
			$query .= " AND B.CP_NO= '".$cp_no."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND A.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND A.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND A.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND A.CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (A.KEYWORD like '%".$keyword."%') or (A.TITLE like '%".$keyword."%') or (A.WRITER_NM like '%".$keyword."%')) ";
		}

		if ($reply_state <> "") {
			$query .= " AND A.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
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

	///////////////////////////////     업체 공지사항 끝  /////////////////////////////////////////////////


	////////////////////////////////////////////////////////////////////////////////////////////////////
	//										개인 메세지 												 ///
	////////////////////////////////////////////////////////////////////////////////////////////////////


	//개인메세지 읽은수 체크
	function viewChkBoardMessage($db, $bb_code, $bb_no, $read_adm) {

		viewChkBoard($db, $bb_code, $bb_no);

		$query="UPDATE TBL_BOARD_COMPANY 
				   SET OPT_READ_DATE = now()
		         WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' AND CP_NO = '$read_adm' ";
	
		//echo $query;
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	// 개인메세지 의 적용/미적용 관리자 리스트
	function listMessageBoard($db, $bb_code, $bb_no, $type) {
	
		if ($type == 'NO') {
			$query = "SELECT ADM_NO, ADM_NAME
									FROM TBL_ADMIN_INFO 
								 WHERE USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND ADM_NO NOT IN (SELECT ADM_NO FROM TBL_BOARD_COMPANY WHERE BB_NO = '$bb_no' AND BB_CODE = '$bb_code' ) 
								 ORDER BY ADM_NAME ASC ";
		} else {
			$query = "SELECT ADM_NO, ADM_NAME
									FROM TBL_ADMIN_INFO 
								 WHERE USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND ADM_NO IN (SELECT CP_NO FROM TBL_BOARD_COMPANY WHERE BB_NO = '$bb_no' AND BB_CODE = '$bb_code') 
								 ORDER BY ADM_NAME ASC ";
		}

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

	// 개인 메세지의 스크립트 생성
	function makeMessageScriptArray($db, $objname) {

		$query = "SELECT ADM_NO, ADM_NAME, COM_CODE
								FROM TBL_ADMIN_INFO 
							 WHERE USE_TF = 'Y'
								 AND DEL_TF = 'N'
							 ORDER BY ADM_NAME ASC, ADM_NO ASC ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
			
		$tmp_str_no			=	"";
		$tmp_str_name		=	"";
		$tmp_str_type		=	"";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_ADM_NO			= Trim($row[0]);
			$RS_ADM_NAME		= Trim($row[1]);
			$RS_COM_CODE		= Trim($row[2]);

			$tmp_str_no			.= ",'".$RS_ADM_NO."'";
			$tmp_str_name		.= ",'".$RS_ADM_NAME."'";
			$tmp_str_type		.= ",'".$RS_COM_CODE."'";
				
		}
		
		$tmp_str_no			= substr($tmp_str_no, 1, strlen($tmp_str_no)-1);
		$tmp_str_name		= substr($tmp_str_name, 1, strlen($tmp_str_name)-1);
		$tmp_str_type		= substr($tmp_str_type, 1, strlen($tmp_str_type)-1);


		$tmp_str  = $objname."_no = new Array(".$tmp_str_no."); \n";
		$tmp_str .= $objname."_name = new Array(".$tmp_str_name."); \n";
		$tmp_str .= $objname."_type = new Array(".$tmp_str_type."); \n";

		return $tmp_str;
	}

	// 업체 공지사항의 게시물과 업체 연결
	function deleteBoardMessage($db, $bb_code, $bb_no) {

		$query="DELETE FROM TBL_BOARD_COMPANY 
											 WHERE BB_CODE = '$bb_code' AND BB_NO = '$bb_no' ";

		//echo $query."<br>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertBoardMessage($db, $bb_code, $bb_no, $adm_no) {
		
		$query="INSERT INTO TBL_BOARD_COMPANY (BB_CODE, BB_NO, CP_NO) 
														values ('$bb_code', '$bb_no', '$adm_no'); ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listBoardMessage($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $adm_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBoardMessage($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $adm_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT DISTINCT @rownum:= @rownum - 1  as rn, A.BB_CODE, A.BB_NO, A.BB_PO, A.BB_RE, A.BB_DE, A.CATE_01, A.CATE_02, A.CATE_03, A.CATE_04, 
										 A.WRITER_NM, A.WRITER_PW, A.EMAIL, A.HOMEPAGE, A.TITLE, A.HIT_CNT, A.REF_IP, A.RECOMM, A.CONTENTS,
										 A.FILE_NM, A.FILE_RNM, A.FILE_PATH, A.FILE_SIZE, A.FILE_EXT, A.KEYWORD, A.REPLY, A.REPLY_ADM, A.REPLY_DATE, A.REPLY_STATE, A.COMMENT_TF,
										 A.USE_TF, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.UP_ADM, A.UP_DATE, A.DEL_ADM, A.DEL_DATE
								FROM TBL_BOARD A, TBL_BOARD_COMPANY B WHERE A.BB_CODE = B.BB_CODE AND A.BB_NO = B.BB_NO ";

		
		if ($bb_code <> "") {
			$query .= " AND A.BB_CODE = '".$bb_code."' ";
		}

		if ($adm_no <> "") {
			$query .= " AND (A.REG_ADM = '".$adm_no."' OR B.CP_NO= '".$adm_no."') ";
		}

		if ($cate_01 <> "") {
			$query .= " AND A.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND A.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND A.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND A.CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (A.KEYWORD like '%".$keyword."%') or (A.TITLE like '%".$keyword."%') or (A.WRITER_NM like '%".$keyword."%')) ";
		}

		if ($reply_state <> "") {
			$query .= " AND A.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}
		
		$query .= " ORDER BY A.REG_DATE desc limit ".$offset.", ".$nRowCount;

		echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntBoardMessage($db, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $adm_no, $keyword, $reply_state, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(DISTINCT A.BB_NO) CNT FROM TBL_BOARD A, TBL_BOARD_COMPANY B WHERE A.BB_CODE = B.BB_CODE AND A.BB_NO = B.BB_NO ";
		
		if ($bb_code <> "") {
			$query .= " AND A.BB_CODE = '".$bb_code."' ";
		}

		if ($adm_no <> "") {
			$query .= " AND (A.REG_ADM = '".$adm_no."' OR B.CP_NO= '".$adm_no."') ";
		}

		if ($cate_01 <> "") {
			$query .= " AND A.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND A.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND A.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND A.CATE_04 = '".$cate_04."' ";
		}

		if ($keyword <> "") {
			$query .= " AND ( (A.KEYWORD like '%".$keyword."%') or (A.TITLE like '%".$keyword."%') or (A.WRITER_NM like '%".$keyword."%')) ";
		}

		if ($reply_state <> "") {
			$query .= " AND A.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
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

	////////////////////////////////////      개인 메세지 - 끝          ///////////////////////////////////////////////

	function listBoardClaim($db, $start_date, $end_date, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $reg_adm, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntBoardClaim($db, $start_date, $end_date, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $reg_adm, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, B.BB_CODE, B.BB_NO, B.BB_PO, B.BB_RE, B.BB_DE, B.CATE_01, B.CATE_02, B.CATE_03, B.CATE_04, 
										 B.WRITER_NM, B.WRITER_PW, B.EMAIL, B.HOMEPAGE, B.TITLE, B.HIT_CNT, B.REF_IP, B.RECOMM, B.CONTENTS,
										 B.FILE_NM, B.FILE_RNM, B.FILE_PATH, B.FILE_SIZE, B.FILE_EXT, B.KEYWORD, B.REPLY, B.REPLY_ADM, B.REPLY_DATE, 
										 B.REPLY_STATE, B.COMMENT_TF,
										 B.USE_TF, B.DEL_TF, B.REG_ADM, B.REG_DATE, B.UP_ADM, B.UP_DATE, B.DEL_ADM, B.DEL_DATE, O.CP_NO, O.R_ZIPCODE, O.R_ADDR1, O.R_PHONE, O.R_HPHONE
								FROM TBL_BOARD B, TBL_ORDER O WHERE B.CATE_01 = O.RESERVE_NO "; //, O.CP_ORDER_NO

		if ($start_date <> "") {
			$query .= " AND B.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.REG_DATE <= '".$end_date." 23:59:59' ";
		}
		
		if ($bb_code <> "") {
			$query .= " AND B.BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND O.CP_NO = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND B.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND B.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND B.CATE_04 = '".$cate_04."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND B.REG_ADM = '".$reg_adm."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND B.KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (B.KEYWORD like '%".$keyword."%') or (B.TITLE like '%".$keyword."%') or (B.WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND B.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (B.CATE_01 = '".$search_str."' 
					             OR B.EMAIL LIKE '%".$search_str."%'
								 OR B.HOMEPAGE LIKE '%".$search_str."%'
								 OR B.TITLE LIKE '%".$search_str."%'
								 OR B.CONTENTS LIKE '%".$search_str."%'
								    )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		if ($order_field == "") 
			$order_field = "B.REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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

	function totalCntBoardClaim($db, $start_date, $end_date, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $reg_adm, $use_tf, $del_tf, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT 
		           FROM TBL_BOARD B 
				   JOIN TBL_ORDER O ON B.CATE_01 = O.RESERVE_NO
				  WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND B.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($bb_code <> "") {
			$query .= " AND B.BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND O.CP_NO = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND B.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND B.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND B.CATE_04 = '".$cate_04."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND B.REG_ADM = '".$reg_adm."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND B.KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (B.KEYWORD like '%".$keyword."%') or (B.TITLE like '%".$keyword."%') or (B.WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND B.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (B.CATE_01 = '".$search_str."' 
					             OR B.EMAIL LIKE '%".$search_str."%'
								 OR B.HOMEPAGE LIKE '%".$search_str."%'
								 OR B.TITLE LIKE '%".$search_str."%'
								 OR B.CONTENTS LIKE '%".$search_str."%'
								    )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listClaimGoods($db, $bb_no, $reserve_no){

		$query = " SELECT A.STOCK_TYPE, A.STOCK_CODE, B.GOODS_NAME, A.GOODS_NO, A.IN_QTY, A.IN_FQTY, A.IN_BQTY, A.OUT_QTY, A.OUT_BQTY, A.IN_DATE, A.OUT_DATE, A.IN_LOC, A.IN_LOC_EXT, A.ORDER_GOODS_NO, 
							A.WORK_DONE_NO, A.RGN_NO
					FROM TBL_STOCK A JOIN TBL_GOODS B ON A.GOODS_NO = B.GOODS_NO 
					WHERE A.CLOSE_TF = 'N' AND A.DEL_TF = 'N' ";

		if ($bb_no <> "") {
			$query .= " AND A.BB_NO = '".$bb_no."' ";
		}

		if ($reserve_no <> "") {
			$query .= " AND A.RESERVE_NO = '".$reserve_no."'  ";
		}

		$query .= "	ORDER BY A.REG_DATE DESC
					
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


	// 개발 주 : 성능개선 위해 JOIN 후 PIVOT 테이블 처리할 필요 있음
	function totalCntClaimOrderState($db, $start_date, $end_date, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $reg_adm, $use_tf, $del_tf, $search_field, $search_str){

		$query .= " 	SELECT CD.DCODE, CD.DCODE_NM AS ORDER_STATE_NAME, ";

		$query .=  "	(
							SELECT COUNT( * ) 
							FROM TBL_BOARD B, TBL_ORDER O WHERE B.CATE_01 = O.RESERVE_NO
							 ";

		if ($start_date <> "") {
			$query .= " AND B.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($bb_code <> "") {
			$query .= " AND B.BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND O.CP_NO = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND B.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND B.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND B.CATE_04 = '".$cate_04."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND B.REG_ADM = '".$reg_adm."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND B.KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (B.KEYWORD like '%".$keyword."%') or (B.TITLE like '%".$keyword."%') or (B.WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND B.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (B.CATE_01 = '".$search_str."' 
					             OR B.EMAIL LIKE '%".$search_str."%'
								 OR B.HOMEPAGE LIKE '%".$search_str."%'
								 OR B.TITLE LIKE '%".$search_str."%'
								 OR B.CONTENTS LIKE '%".$search_str."%'
								    )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " AND CATE_04 = CD.DCODE AND B.REPLY_STATE = 'Y' ) AS ORDER_STATE_CNT_YES ,   ";


		
		$query .=  "	(
							SELECT COUNT( * ) 
							FROM TBL_BOARD B, TBL_ORDER O WHERE B.CATE_01 = O.RESERVE_NO
							 ";

		if ($start_date <> "") {
			$query .= " AND B.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($bb_code <> "") {
			$query .= " AND B.BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND O.CP_NO = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND B.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND B.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND B.CATE_04 = '".$cate_04."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND B.REG_ADM = '".$reg_adm."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND B.KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (B.KEYWORD like '%".$keyword."%') or (B.TITLE like '%".$keyword."%') or (B.WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND B.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		$query .= " AND CATE_04 = CD.DCODE AND B.REPLY_STATE = 'N' ) AS ORDER_STATE_CNT_NO   ";
		
		$query .= "     	
							FROM TBL_CODE_DETAIL CD
							WHERE PCODE =  'ORDER_STATE'
							AND USE_TF =  'Y'
							AND DEL_TF =  'N'
							AND DCODE IN ('6','7','8','99')
							ORDER BY DCODE_SEQ_NO
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

	function totalCntClaimType($db, $start_date, $end_date, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $keyword, $reply_state, $reg_adm, $use_tf, $del_tf, $search_field, $search_str){

		$query .= " 	SELECT 
							CASE WHEN CD.DCODE LIKE 'CC%' THEN '6'
							WHEN CD.DCODE LIKE 'CR%' THEN '7'
							WHEN CD.DCODE LIKE 'CE%' THEN '8'
							WHEN CD.DCODE LIKE 'CX%' THEN '99'
							END	AS PCODE, 
							CASE WHEN CD.DCODE LIKE 'CC%' THEN '취소'
							WHEN CD.DCODE LIKE 'CR%' THEN '반품'
							WHEN CD.DCODE LIKE 'CE%' THEN '교환'
							WHEN CD.DCODE LIKE 'CX%' THEN '기타'
							END	AS PCODE_NM,
							CD.DCODE_NM,
							(SELECT COUNT(*) 
							FROM  TBL_BOARD B, TBL_ORDER O WHERE B.CATE_01 = O.RESERVE_NO
							AND CATE_02 = CD.DCODE ";

		if ($start_date <> "") {
			$query .= " AND B.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND B.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($bb_code <> "") {
			$query .= " AND B.BB_CODE = '".$bb_code."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND O.CP_NO = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND B.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND B.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND B.CATE_04 = '".$cate_04."' ";
		}

		if ($reg_adm <> "") {
			$query .= " AND B.REG_ADM = '".$reg_adm."' ";
		}

		if ($keyword <> "") {
			if ($bb_code == "CLAIM") {
				$query .= " AND B.KEYWORD =  '".$keyword."' ";
			} else {
				$query .= " AND ( (B.KEYWORD like '%".$keyword."%') or (B.TITLE like '%".$keyword."%') or (B.WRITER_NM like '%".$keyword."%')) ";
			}
		}

		if ($reply_state <> "") {
			$query .= " AND B.REPLY_STATE = '".$reply_state."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND B.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND B.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (B.CATE_01 = '".$search_str."' 
					             OR B.EMAIL LIKE '%".$search_str."%'
								 OR B.HOMEPAGE LIKE '%".$search_str."%'
								 OR B.TITLE LIKE '%".$search_str."%'
								 OR B.CONTENTS LIKE '%".$search_str."%'
								    )"; 
			
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}


		$query .= "     	) AS CNT

							FROM TBL_CODE_DETAIL CD
							WHERE PCODE = 'CLAIM_TYPE' AND USE_TF='Y' AND DEL_TF = 'N'
							ORDER BY DCODE_SEQ_NO
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


?>