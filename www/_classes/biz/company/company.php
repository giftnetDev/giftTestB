<?

	function listCompany($db, $cp_cate, $cp_type, $ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$exclude_category = $filter["exclude_category"];
		$con_is_mall	  = $filter["con_is_mall"];

		$total_cnt = totalCntCompany($db, $cp_cate, $cp_type, $ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, CP_NO, CP_CATE, CP_CODE, CP_TYPE, CP_NM, CP_NM2, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP, RE_ADDR, HOMEPAGE, SALE_ADM_NO,
										 BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, MANAGER_NM, PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, 
										 AD_TYPE, ACCOUNT_BANK, ACCOUNT, MEMO, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 replace(replace(replace(replace(replace(CP_NM,' ',''),'(주)',''),'(유)',''),'(사)',''),'(X)','') AS TRIM_CP_NAME, USE_TF
								FROM TBL_COMPANY WHERE 1 = 1 
								 ";

		if ($cp_cate <> "") {
			$query .= " AND CP_CATE LIKE '".$cp_cate."%' ";
		}

		if ($cp_type <> "") {
			$query2 = '';
			foreach (explode(",", $cp_type) as $splited_cp_type){
				$query2 .= "'".$splited_cp_type."',";
			}
			$query2 = rtrim($query2, ',');
			$query .= " AND CP_TYPE IN (".$query2.") ";

		}

		if ($ad_type <> "") {
			$query .= " AND AD_TYPE = '".$ad_type."' ";
		}

		if ($date_start <> "" && $date_end <> "") {
			$query .= " AND CONTRACT_START BETWEEN '".$date_start."' AND date_add('".$date_end."', interval 1 day) ";
		} else if ($date_start <> "") {
			$query .= " AND CONTRACT_START  >= '".$date_start."'	";
		} else if ($date_end <> "") {
			$query .= " AND CONTRACT_START  <= date_add('".$date_end."', interval 1 day)	";
		}

		if ($min_dc_rate <> "" && $max_dc_rate <> "") {
			$query .= " AND DC_RATE BETWEEN ".$min_dc_rate." AND ".$max_dc_rate." ";
		} else if ($min_dc_rate <> "") {
			$query .= " AND DC_RATE  >= ".$min_dc_rate."	";
		} else if ($max_dc_rate <> "") {
			$query .= " AND DC_RATE  <= ".$max_dc_rate."	";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND SALE_ADM_NO = '".$sale_adm_no."' ";
		}

		
		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND CP_CATE NOT LIKE '".$splited_exclude_category."%' ";
			}
		}

		if ($con_is_mall <> "") {
			$query .= " AND IS_MALL = '".$con_is_mall."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if (strpos($search_field,',') !== false) {
					$query .= " AND (";
					$query2 = '';
					foreach (explode(",", $search_field) as $splited_search_field){
						$query2 .= $splited_search_field." like '%".$search_str."%' OR ";
					}
					$query2 = rtrim($query2, ' OR ');
					$query .= $query2.") ";
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CP_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		
		if ($order_field == "") 
			$order_field = "TRIM_CP_NAME";

		if ($order_str == "") 
			$order_str = "ASC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;
	
		echo $query;
		//exit;
		
	
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function listCompanyWithLastOrderDate($db, $cp_cate, $cp_type, $ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$exclude_category = $filter["exclude_category"];
		$con_is_mall	  = $filter["con_is_mall"];

		$total_cnt = totalCntCompany($db, $cp_cate, $cp_type, $ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query =   "SELECT
						@rownum:= @rownum - 1  as rn, 
						A.CP_NO, A.CP_CATE, A.CP_CODE, A.CP_TYPE, A.CP_NM, A.CP_NM2, A.CP_PHONE, A.CP_HPHONE, A.CP_FAX, A.CP_ZIP, 
						A.CP_ADDR, A.RE_ZIP, A.RE_ADDR, A.HOMEPAGE, A.SALE_ADM_NO,A.BIZ_NO, A.CEO_NM, A.UPJONG, A.UPTEA, A.DC_RATE, 
						A.MANAGER_NM, A.PHONE, A.HPHONE, A.FPHONE, A.EMAIL, A.EMAIL_TF, A.CONTRACT_START, A.CONTRACT_END, A.AD_TYPE, 
						A.ACCOUNT_BANK, A.ACCOUNT, A.MEMO, A.USE_TF, A.DEL_TF, A.REG_ADM, A.REG_DATE, A.UP_ADM, A.UP_DATE, 
						A.DEL_ADM, A.DEL_DATE, replace(replace(replace(replace(replace(A.CP_NM,' ',''),'(주)',''),'(유)',''),'(사)',''),'(X)','') AS TRIM_CP_NAME,
						MAX(B.ORDER_DATE) AS LAST_ORDER
					FROM TBL_COMPANY A LEFT JOIN TBL_ORDER B ON A.CP_NO = B.CP_NO
					WHERE 1 = 1 ";

		if ($cp_cate <> "") {
			$query .= " AND A.CP_CATE LIKE '".$cp_cate."%' ";
		}

		if ($cp_type <> "") {
			$query2 = '';
			foreach (explode(",", $cp_type) as $splited_cp_type){
				$query2 .= "'".$splited_cp_type."',";
			}
			$query2 = rtrim($query2, ',');
			$query .= " AND A.CP_TYPE IN (".$query2.") ";

		}

		if ($ad_type <> "") {
			$query .= " AND A.AD_TYPE = '".$ad_type."' ";
		}

		if ($date_start <> "" && $date_end <> "") {
			$query .= " AND A.CONTRACT_START BETWEEN '".$date_start."' AND date_add('".$date_end."', interval 1 day) ";
		} else if ($date_start <> "") {
			$query .= " AND A.CONTRACT_START  >= '".$date_start."'	";
		} else if ($date_end <> "") {
			$query .= " AND A.CONTRACT_START  <= date_add('".$date_end."', interval 1 day)	";
		}

		if ($min_dc_rate <> "" && $max_dc_rate <> "") {
			$query .= " AND A.DC_RATE BETWEEN ".$min_dc_rate." AND ".$max_dc_rate." ";
		} else if ($min_dc_rate <> "") {
			$query .= " AND A.DC_RATE  >= ".$min_dc_rate."	";
		} else if ($max_dc_rate <> "") {
			$query .= " AND A.DC_RATE  <= ".$max_dc_rate."	";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND A.SALE_ADM_NO = '".$sale_adm_no."' ";
		}

		
		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND A.CP_CATE NOT LIKE '".$splited_exclude_category."%' ";
			}
		}

		if ($con_is_mall <> "") {
			$query .= " AND A.IS_MALL = '".$con_is_mall."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND A.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND A.DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if (strpos($search_field,',') !== false) {
					$query .= " AND (";
					$query2 = '';
					foreach (explode(",", $search_field) as $splited_search_field){
						$query2 .= $splited_search_field." like '%".$search_str."%' OR ";
					}
					$query2 = rtrim($query2, ' OR ');
					$query .= $query2.") ";
			} else if ($search_field == "A.CP_CODE") {
				$query .= " AND A.CP_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		
		if ($order_field == "") 
			$order_field = "TRIM_CP_NAME";

		if ($order_str == "") 
			$order_str = "ASC";

		$query .= " GROUP BY A.CP_NO ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;
	
		// echo $query;
		//exit;
		
	
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function totalCntCompany($db, $cp_cate, $cp_type, $ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str){

		$exclude_category = $filter["exclude_category"];
		$con_is_mall	  = $filter["con_is_mall"];

		$query ="SELECT COUNT(*) CNT FROM TBL_COMPANY WHERE 1 = 1 ";

		if ($cp_cate <> "") {
			$query .= " AND CP_CATE LIKE '".$cp_cate."%' ";
		}

		if ($cp_type <> "") {
			$query2 = '';
			foreach (explode(",", $cp_type) as $splited_cp_type){
				$query2 .= "'".$splited_cp_type."',";
			}
			$query2 = rtrim($query2, ',');
			$query .= " AND CP_TYPE IN (".$query2.") ";

		}

		if ($ad_type <> "") {
			$query .= " AND AD_TYPE = '".$ad_type."' ";
		}

		if ($date_start <> "" && $date_end <> "") {
			$query .= " AND CONTRACT_START BETWEEN '".$date_start."' AND date_add('".$date_end."', interval 1 day) ";
		} else if ($date1 <> "") {
			$query .= " AND CONTRACT_START  >= '".$date_start."'	";
		} else if ($date2 <> "") {
			$query .= " AND CONTRACT_START  <= date_add('".$date_end."', interval 1 day)	";
		}

		if ($min_dc_rate <> "" && $max_dc_rate <> "") {
			$query .= " AND DC_RATE BETWEEN ".$min_dc_rate." AND ".$max_dc_rate." ";
		} else if ($min_dc_rate <> "") {
			$query .= " AND DC_RATE  >= ".$min_dc_rate."	";
		} else if ($max_dc_rate <> "") {
			$query .= " AND DC_RATE  <= ".$max_dc_rate."	";
		}

		if ($sale_adm_no <> "") {
			$query .= " AND SALE_ADM_NO = '".$sale_adm_no."' ";
		}
		
		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND CP_CATE NOT LIKE '".$splited_exclude_category."%' ";
			}
		}

		if ($con_is_mall <> "") {
			$query .= " AND IS_MALL = '".$con_is_mall."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if (strpos($search_field,',') !== false) {
					$query .= " AND (";
					$query2 = '';
					foreach (explode(",", $search_field) as $splited_search_field){
						$query2 .= $splited_search_field." like '%".$search_str."%' OR ";
					}
					$query2 = rtrim($query2, ' OR ');
					$query .= $query2.") ";
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CP_CODE = '".$search_str."' ";
			} else
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
		}

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertCompany($db, $cp_cate, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $is_mall, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_COMPANY 
							(CP_CATE, CP_TYPE, CP_NM, CP_NM2, CP_CODE, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP, RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM, PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT, ACCOUNT_OWNER_NM, MEMO, IS_MALL, USE_TF, REG_ADM, REG_DATE) 
						VALUES 
							('$cp_cate', '$cp_type', '$cp_nm', '$cp_nm2','$cp_code', '$cp_phone', '$cp_hphone', '$cp_fax', '$cp_zip', '$cp_addr', '$re_zip', '$re_addr', '$homepage', '$biz_no', '$ceo_nm', '$upjong', '$uptea', '$dc_rate', '$sale_adm_no', '$manager_nm', '$phone', '$hphone', '$fphone', '$email', '$email_tf', '$contract_start', '$contract_end', '$ad_type', '$account_bank', '$account', '$account_owner_nm', '$memo', '$is_mall', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id($db);
		}
	}

	function selectCompany($db, $cp_no, $managerNo = "") {

		if($managerNo <> "")
		{
			$query = "SELECT 
						  A.CP_CATE
						, A.CP_TYPE
						, A.CP_NM
						, A.CP_NM2
						, A.CP_CODE
						, A.CP_PHONE
						, A.CP_HPHONE
						, A.CP_FAX
						, A.CP_ZIP
						, A.CP_ADDR
						, A.RE_ZIP
						, A.RE_ADDR
						, B.HOMEPAGE
						, A.BIZ_NO
						, A.CEO_NM
						, A.UPJONG
						, A.UPTEA
						, A.DC_RATE
						, B.SALE_ADM_NO
						, B.MANAGER_NO
						, B.MANAGER_NM AS MANAGER_NM
						, B.PHONE AS PHONE
						, B.HPHONE AS HPHONE
						, B.FPHONE AS FPHONE
						, B.EMAIL AS EMAIL
						, B.EMAIL_TF AS EMAIL_TF
						, B.MEMO AS MEMO
						, B.CONTRACT_START
						, B.CONTRACT_END
						, B.AD_TYPE
						, B.ACCOUNT_BANK
						, B.ACCOUNT
						, B.ACCOUNT_OWNER_NM
						, B.IS_MALL
						, A.USE_TF
						, A.DEL_TF
						, A.REG_ADM
						, A.REG_DATE
						, A.UP_ADM
						, A.UP_DATE
						, A.DEL_ADM
						, A.DEL_DATE
						, (SELECT COUNT(1) FROM TBL_COMPANY_MAM C WHERE C.CP_NO = '$cp_no' AND C.DEL_TF = 'N') AS DT_CNT
					FROM TBL_COMPANY A, TBL_COMPANY_MAM B 
					WHERE A.CP_NO = B.CP_NO
					AND A.CP_NO = '$cp_no'
					AND B.MANAGER_NO = '$managerNo'
					";
		}
		else
		{
			$query = "SELECT 
							 CP_CATE
							, CP_TYPE
							, CP_NM
							, CP_NM2
							, CP_CODE
							, CP_PHONE
							, CP_HPHONE
							, CP_FAX
							, CP_ZIP
							, CP_ADDR
							, RE_ZIP
							, RE_ADDR
							, HOMEPAGE
							, BIZ_NO
							, CEO_NM
							, UPJONG
							, UPTEA
							, DC_RATE
							, SALE_ADM_NO
							, MANAGER_NO
							, (CASE WHEN MANAGER_NM = '' THEN (SELECT B.MANAGER_NM FROM TBL_COMPANY_MAM B WHERE CP_NO = '$cp_no') ELSE MANAGER_NM END) AS MANAGER_NM
							, PHONE
							, HPHONE
							, FPHONE
							, EMAIL
							, EMAIL_TF
							, CONTRACT_START
							, CONTRACT_END
							, AD_TYPE
							, ACCOUNT_BANK
							, ACCOUNT
							, ACCOUNT_OWNER_NM
							, MEMO
							, IS_MALL
							, USE_TF
							, DEL_TF
							, REG_ADM
							, REG_DATE
							, UP_ADM
							, UP_DATE
							, DEL_ADM
							, DEL_DATE
							, (SELECT COUNT(1) FROM TBL_COMPANY_MAM B WHERE B.CP_NO = '$cp_no' AND B.DEL_TF = 'N') AS DT_CNT
						FROM TBL_COMPANY WHERE CP_NO = '$cp_no' 
						";
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

	function updateCompany($db, $cp_cate, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $is_mall, $memo, $use_tf, $up_adm, $cp_no) {
		
		$qre0 = "  UPDATE TBL_COMPANY_MAM
						  SET USE_TF = 'N'
						WHERE CP_NO = '$cp_no'
			  ";

		//echo $qre0;
		//exit;
		if(!mysql_query($qre0,$db)) {
			//return false;
			echo "<script>alert(\"[0]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} 
		/*else {
			return true;
		}*/

		$qre1 = "  UPDATE TBL_COMPANY_MAM
						  SET MANAGER_NM = '$manager_nm'
							, PHONE = '$phone'
							, HPHONE = '$hphone'
							, FPHONE = '$fphone'
							, EMAIL = '$email'
							, EMAIL_TF = '$email_tf'
							, SALE_ADM_NO = '$sale_adm_no'
							, AD_TYPE ='$ad_type'
							, ACCOUNT_BANK ='$account_bank'
							, ACCOUNT = '$account'
							, ACCOUNT_OWNER_NM = '$account_owner_nm'
							, CONTRACT_START = '$contract_start'
							, CONTRACT_END = '$contract_end'
							, HOMEPAGE = '$homepage'
							, IS_MALL = '$is_mall'
							, MEMO = '$memo'
							, USE_TF = '$use_tf'
							, UP_ADM = '$up_adm'
							, UP_DATE = NOW()
						WHERE CP_NO = '$cp_no'
						  AND MANAGER_NO = '$manager_no'
			  ";

		//echo $qre1;
		//exit;
		if(!mysql_query($qre1,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} 
		/*else {
			return true;
		}*/

		$query="UPDATE TBL_COMPANY 
				   SET 
						CP_CATE					= '$cp_cate',
						CP_TYPE					= '$cp_type',
						CP_NM					= '$cp_nm',
						CP_NM2					= '$cp_nm2',
						CP_CODE					= '$cp_code',
						CP_PHONE				= '$cp_phone',
						CP_HPHONE				= '$cp_hphone',
						CP_FAX					= '$cp_fax',
						CP_ZIP					= '$cp_zip',
						CP_ADDR					= '$cp_addr',
						RE_ZIP					= '$re_zip',
						RE_ADDR					= '$re_addr',
						HOMEPAGE				= '$homepage',
						BIZ_NO					= '$biz_no',
						CEO_NM					= '$ceo_nm',
						UPJONG					= '$upjong',
						UPTEA					= '$uptea',
						DC_RATE					= '$dc_rate',
						SALE_ADM_NO				= '$sale_adm_no',
						MANAGER_NO				= '$manager_no',
						MANAGER_NM				= '$manager_nm',
						PHONE					= '$phone',
						HPHONE					= '$hphone',
						FPHONE					= '$fphone',
						EMAIL					= '$email',
						EMAIL_TF				= '$email_tf',
						CONTRACT_START			= '$contract_start',
						CONTRACT_END			= '$contract_end',
						AD_TYPE					= '$ad_type',
						ACCOUNT_BANK			= '$account_bank',
						ACCOUNT					= '$account',
						ACCOUNT_OWNER_NM		= '$account_owner_nm',
						IS_MALL					= '$is_mall',
						MEMO					= '$memo',
						USE_TF					= '$use_tf',
						UP_ADM					= '$up_adm',
						UP_DATE					= now()
				 WHERE CP_NO				= '$cp_no' ";
		
		//echo $query;
		//exit;
		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[2]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteCompany($db, $del_adm, $cp_no) {

		$query0="SELECT * FROM TBL_COMPANY_LEDGER WHERE CP_NO=".$cp_no." AND DEL_TF='N' AND USE_TF='Y' ; ";
		$result0=mysql_query($query0,$db);

		// echo $query0;
		// exit;
		if($result0<>""){

			$cnt=mysql_num_rows($result0);
			if($cnt>0){
				echo "<script>alert('원장 정보가 있는 업체여서 삭제할 수 없습니다');</script>";
				return false;
			}

		}

		$query="UPDATE TBL_COMPANY SET 
														 DEL_TF				= 'Y',
														 DEL_ADM			= '$del_adm',
														 DEL_DATE			= now()														 
											 WHERE CP_NO				= '$cp_no' ";

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function listTempCompany($db, $temp_no) {

		$query = "SELECT TEMP_NO, CP_NO, CP_TYPE, CP_NM, CP_NM2, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP, RE_ADDR, HOMEPAGE, 
										 BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM, PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, 
										 AD_TYPE, ACCOUNT_BANK, ACCOUNT, ACCOUNT_OWNER_NM, MEMO, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_TEMP_COMPANY WHERE TEMP_NO = '$temp_no' ";

		
		$query .= " ORDER BY CP_NO asc ";
		
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function insertTempCompany($db, $file_nm, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $use_tf, $reg_adm) {
		
		$query="INSERT INTO TBL_TEMP_COMPANY 
							(TEMP_NO, CP_TYPE, CP_NM, CP_NM2, CP_CODE, CP_PHONE, CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP, RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM, PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT, ACCOUNT_OWNER_NM, MEMO, USE_TF, REG_ADM, REG_DATE) 
						VALUES 							
							('$file_nm', '$cp_type', '$cp_nm', '$cp_nm2','$cp_code', '$cp_phone', '$cp_hphone', '$cp_fax', '$cp_zip', '$cp_addr', '$re_zip', '$re_addr', '$homepage', '$biz_no', '$ceo_nm', '$upjong', '$uptea', '$dc_rate', '$sale_adm_no', '$manager_nm', '$phone', '$hphone', '$fphone', '$email', '$email_tf', '$contract_start', '$contract_end', '$ad_type', '$account_bank', '$account', '$account_owner_nm', '$memo', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateTempCompany($db, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $use_tf, $up_adm, $temp_no, $cp_no) {

		$query="UPDATE TBL_TEMP_COMPANY SET 
													CP_TYPE					= '$cp_type',
													CP_NM					= '$cp_nm',
													CP_NM2					= '$cp_nm2',
													CP_CODE					= '$cp_code',
													CP_PHONE				= '$cp_phone',
													CP_HPHONE				= '$cp_hphone',
													CP_FAX					= '$cp_fax',
													CP_ZIP					= '$cp_zip',
													CP_ADDR					= '$cp_addr',
													RE_ZIP					= '$re_zip',
													RE_ADDR					= '$re_addr',
													HOMEPAGE				= '$homepage',
													BIZ_NO					= '$biz_no',
													CEO_NM					= '$ceo_nm',
													UPJONG					= '$upjong',
													UPTEA					= '$uptea',
													DC_RATE					= '$dc_rate',
													SALE_ADM_NO				= '$sale_adm_no',
													MANAGER_NM				= '$manager_nm',
													PHONE					= '$phone',
													HPHONE					= '$hphone',
													FPHONE					= '$fphone',
													EMAIL					= '$email',
													EMAIL_TF				= '$email_tf',
													CONTRACT_START			= '$contract_start',
													CONTRACT_END			= '$contract_end',
													AD_TYPE					= '$ad_type',
													ACCOUNT_BANK			= '$account_bank',
													ACCOUNT					= '$account',
													ACCOUNT_OWNER_NM		= '$account_owner_nm',
													MEMO					= '$memo',
													USE_TF					= '$use_tf',
													UP_ADM					=	'$up_adm',
													UP_DATE					=	now()
										WHERE TEMP_NO					= '$temp_no'
											AND CP_NO						= '$cp_no' ";
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function selectTempCompany($db, $temp_no, $cp_no) {

		$query = "SELECT 
								TEMP_NO, CP_NO, CP_CODE, CP_TYPE, CP_NM, CP_NM2, CP_PHONE,	CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP,	RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM,	PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT,	ACCOUNT_OWNER_NM,	MEMO,	USE_TF, DEL_TF,	REG_ADM, REG_DATE, UP_ADM, UP_DATE,	DEL_ADM, DEL_DATE

							FROM TBL_TEMP_COMPANY 
							WHERE TEMP_NO = '$temp_no' AND CP_NO = '$cp_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}



	function deleteTempCompany($db, $temp_no, $cp_no) {

		$query="DELETE FROM TBL_TEMP_COMPANY WHERE TEMP_NO = '$temp_no' AND CP_NO = '$cp_no' ";

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function chkCpNm($db, $cp_nm) {
		$query="SELECT COUNT(*) AS CNT FROM TBL_COMPANY WHERE CP_NM = '$cp_nm' AND DEL_TF = 'N' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return true;
		} else {
			return false;
		}
	}


	function insertTempToRealCompany($db, $str_cp_no) {
		

		$query="INSERT INTO TBL_COMPANY 
		(CP_CODE, CP_TYPE, CP_NM, CP_NM2, CP_PHONE,	CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP,	RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM,	PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT,	ACCOUNT_OWNER_NM,	MEMO,	USE_TF, DEL_TF,	REG_ADM, REG_DATE) 
		
		SELECT CP_CODE, CP_TYPE, CP_NM, CP_NM2, CP_PHONE,	CP_HPHONE, CP_FAX, CP_ZIP, CP_ADDR, RE_ZIP,	RE_ADDR, HOMEPAGE, BIZ_NO, CEO_NM, UPJONG, UPTEA, DC_RATE, SALE_ADM_NO, MANAGER_NM,	PHONE, HPHONE, FPHONE, EMAIL, EMAIL_TF, CONTRACT_START, CONTRACT_END, AD_TYPE, ACCOUNT_BANK, ACCOUNT,	ACCOUNT_OWNER_NM,	MEMO,	USE_TF, DEL_TF,	REG_ADM, REG_DATE
		
		FROM  TBL_TEMP_COMPANY
		WHERE  CP_NO IN ($str_cp_no) ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteTempToRealCompany($db, $str_cp_no) {
		

		$query=" DELETE FROM  TBL_TEMP_COMPANY WHERE  CP_NO IN ($str_cp_no) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function tryCompanyNoByCompanyCode($db, $cp_code) { 
		$query = "SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE = '$cp_code'
												 AND USE_TF = 'Y'
												 AND DEL_TF = 'N'";

	   // echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;

		} else {
			return "등록요망";
		}
	}

	function getOperatingCompany($db, $cp_no) {

		$query =  "SELECT CP_NO, CP_NM, CP_NM2, CEO_NM, CP_ADDR, CP_PHONE, CP_FAX, EMAIL, BIZ_NO, UPTEA, UPJONG, CEO_NM
				     FROM TBL_COMPANY 
					WHERE " ;

		if($cp_no <> '')
			$query .= "   CP_NO = '$cp_no' ";
		else
			$query .= "   CP_TYPE = '운영' 
		         ORDER BY REG_DATE ASC ";

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

	function listCompanyEtc($db, $company_no, $option_nm = "") {

		$query = "SELECT CP_OPTION_NM, CP_OPTION_VALUE
					FROM TBL_COMPANY_ETC 
				   WHERE CP_NO = '$company_no' ";

		if($option_nm <> "")
			$query .= " AND CP_OPTION_NM = '$option_nm' ";

		$query .= " ORDER BY CP_OPTION_NM ";
		
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function insertCompanyEtc($db, $cp_no, $cp_option_nm, $cp_option_value) {
		
		$query="INSERT INTO TBL_COMPANY_ETC 
							(CP_NO, CP_OPTION_NM, CP_OPTION_VALUE) 
						VALUES 
							('$cp_no', '$cp_option_nm', '$cp_option_value'); ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteCompanyEtc($db, $cp_no) {
		
		$query="DELETE FROM TBL_COMPANY_ETC 
					  WHERE CP_NO = '$cp_no' ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			//return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function getNextCPNo($db) {
		$query="SELECT MAX(CP_NO) + 1 FROM TBL_COMPANY ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return str_pad($rows[0], 5, "A", STR_PAD_LEFT); //2017-08-24 "0"에서 "A"로 변경 (사유 : 엑셀입력 숫자인식 오류)
	}

	function selectCompanyByCP_Code($db, $cp_code) {

		$query = "SELECT 
							CP_NO, CP_NM, CP_NM2, CP_CODE, USE_TF, DEL_TF
							FROM TBL_COMPANY WHERE CP_CODE = '$cp_code' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//일괄변경 - 업체리스트
	function updateCompanyBatch($db, $column, $value_to_change, $up_adm, $cp_no) {

		$query="UPDATE TBL_COMPANY SET " ; 

		if($column <> '' && $value_to_change <> '')
			$query .= "           ".$column."					= '".$value_to_change."', ";

		$query .= "                 UP_ADM						=	'$up_adm',
									UP_DATE						=	now()
							 WHERE CP_NO = '".$cp_no."' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listCompanyExtra($db, $cp_no) { 

		$query = "SELECT CE_NO, MANAGER_NM, PHONE, HPHONE, ADDR, MEMO
					FROM TBL_COMPANY_EXTRA 
				   WHERE CP_NO = '$cp_no' AND DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function insertCompanyExtra($db, $cp_no, $manager_nm, $phone, $hphone, $addr, $memo, $reg_adm) { 
	
		$query="INSERT INTO TBL_COMPANY_EXTRA (CP_NO, MANAGER_NM, PHONE, HPHONE, ADDR, MEMO, REG_DATE, REG_ADM)  
					 VALUES ('$cp_no', '$manager_nm', '$phone', '$hphone', '$addr', '$memo', now(), $reg_adm)
		" ; 

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteCompanyExtra($db, $ce_no, $del_adm) { 
	
		$query="UPDATE TBL_COMPANY_EXTRA 
                   SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
				 WHERE CE_NO = '$ce_no'
		" ; 

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectCompanyExtra($db, $ce_no) { 

		$query = "SELECT CP_NO, MANAGER_NM, PHONE, HPHONE, ADDR, MEMO
					FROM TBL_COMPANY_EXTRA 
				   WHERE CE_NO = '$ce_no' AND DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function selectCompanyMemo2($db, $cp_no) { 

		$query = " 
				SELECT MEMO2
				  FROM TBL_COMPANY
				 WHERE CP_NO = '".$cp_no."' ";

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function updateCompanyMemo2($db, $memo2, $cp_no) {
		$query = " UPDATE TBL_COMPANY
				      SET MEMO2 = '$memo2'
				    WHERE CP_NO = '$cp_no'  ";
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	function isCompanyNameRedundancy($db, $cp_nm, $cp_nm2){
		$query="SELECT CP_NO FROM TBL_COMPANY 
		WHERE CP_NM ='".$cp_nm."' 
		AND CP_NM2 ='".$cp_nm2."' 
		AND DEL_TF='N' ; ";
		$result=mysql_query($query, $db);
		$cnt=mysql_num_rows($result);
		if($cnt>0){
			return 1;
		}
		else{
			return 0;
		}

	}

	function insertCompanyHistory($db, $arr_data) {

		// 게시물 등록
		$set_field = "";
		$set_value = "";
		
		$first = "Y";
		foreach ($arr_data as $key => $value) {
			if ($first == "Y") {
				$set_field .= $key; 
				$set_value .= "'".$value."'"; 
				$first = "N";
			} else {
				$set_field .= ",".$key; 
				$set_value .= ",'".$value."'";
			}
		}

		$query = "INSERT INTO TBL_COMPANY_HISTORY (".$set_field.") 
					values (".$set_value."); ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listCompanyHistory($db, $cp_no) { 

		$query = "SELECT *
								FROM TBL_COMPANY_HISTORY 
							 WHERE CP_NO = '$cp_no' AND DEL_TF = 'N' 
							 ORDER BY HISTORY_NO DESC";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function selectCompanyHistory($db, $history_no) { 

		$query = "SELECT *
								FROM TBL_COMPANY_HISTORY 
							 WHERE HISTORY_NO = '$history_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

	function insertCompanyManager($db, $cp_no, $manager_nm, $reg_adm) 
	{ 
	
		$query = " INSERT INTO TBL_COMPANY_MAM 
												(
														CP_NO
													, MANAGER_NO
													, MANAGER_NM
													, REG_ADM
													, REG_DATE
												) 
									VALUES
											(
													'$cp_no'
													, (SELECT LPAD(CASE WHEN COUNT(A.MANAGER_NO) = 0 THEN 1 ELSE MAX(A.MANAGER_NO)+1 END, 3 ,'0') FROM TBL_COMPANY_MAM A WHERE A.CP_NO  = '$cp_no')
													, '$manager_nm'
													, '$reg_adm'
													, now()
											) 
			";

		echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function listManager($db, $cp_no) {

		$query = "
				SELECT CP_NO
					 , MANAGER_NO
					 , MANAGER_NM
					 , DEL_TF
					 , @ROWNUM := @ROWNUM +1 AS RN
				 FROM TBL_COMPANY_MAM A, (SELECT @rownum:=0) B 
				WHERE 1=1
				  AND A.CP_NO = '$cp_no'
				  AND A.USE_TF = 'N'					 
				  AND A.DEL_TF = 'N'
				ORDER BY MANAGER_NO
				";
		
		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function deleteCompanyManager($db, $cp_no, $manager_no, $del_adm) 
	{ 

		$query = " UPDATE TBL_COMPANY_MAM
					  SET DEL_TF = 'Y'
						, DEL_ADM = '$del_adm'
						, DEL_DATE = NOW()
					WHERE CP_NO = '$cp_no'
			  		  AND MANAGER_NO = '$manager_no'
				";

		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

?>