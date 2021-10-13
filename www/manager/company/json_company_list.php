<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";

	function getCompanyName($db, $cp_type, $search_field, $search_str) {
		$cp_codes="";

		$query = "SELECT CP_NO, CP_CODE, CP_NM, CP_NM2, DC_RATE, CP_TYPE
						  FROM TBL_COMPANY 
							WHERE 1=1 AND USE_TF='Y' AND DEL_TF='N' ";

		if($cp_type <> "") { 
		
			$query2 = '';
			foreach (explode(",", $cp_type) as $splited_cp_type){
				$query2 .= "'".$splited_cp_type."',";
			}
			$query2 = rtrim($query2, ',');
			$query .= " AND CP_TYPE IN (".$query2.") ";
		}

		if ($search_str <> "") {

			preg_match('/[[0-9]*]/', $search_str, $cp_codes);
			if(sizeof($cp_codes) > 0) { 
				$t = $cp_codes[0];
				$t = str_replace('[', '', $t);
				$t = str_replace(']', '', $t);

				$query .= " AND CP_CODE = '".$t."' ";
			} else { 
				if (strpos($search_field,',') !== false) {
						$query .= " AND (";
						$query2 = '';
						foreach (explode(",", $search_field) as $splited_search_field){
							$query2 .= $splited_search_field." like '%".$search_str."%' OR ";
						}
						$query2 = rtrim($query2, ' OR ');
						$query .= $query2.") ";
				}
			}
		}

		$query .= " ORDER BY CP_CODE ASC" ;
	
		// echo $query;
		// exit;

		$result = mysql_query($query,$db);

		return $result;
	}

	function getCompanyInfoByNo($db, $cp_no) {
			
		//2015-10-14 ����� ��û���� ����ڸ�, ������̸���, ȸ���ȣ, �޴����ʿ�X, ȸ���ǥ�ּҷ� ����
		//2017-03-09 ����������߰�
		//2017-04-20 ������/ī��������� �߰�
		//2017-05-17 ����ڹ�ȣ, ��ǥ�� �߰�
		//2017-06-01 �ŷ�����, ���¹�ȣ, ������ �߰�
		//2018-05-17 ����� ��ȭ �߰� (�ֹ���)
		$query = "SELECT CP_NO, CP_CODE, CP_NM, CP_NM2, MANAGER_NM, EMAIL, CP_PHONE, '' AS CP_HPHONE, CP_ZIP, CP_ADDR, SALE_ADM_NO, DC_RATE, BIZ_NO, CEO_NM, ACCOUNT_BANK, ACCOUNT, ACCOUNT_OWNER_NM, PHONE, CP_TYPE
						  FROM TBL_COMPANY 
							WHERE USE_TF='Y' AND DEL_TF='N' AND CP_NO = '$cp_no'
							ORDER BY CP_CODE ASC" ;

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);

		return $result;
	}

	function dupCompanyByCPCode($db, $cp_code) {
			
		$query = "SELECT COUNT(*) AS CNT
						  FROM TBL_COMPANY 
							WHERE USE_TF='Y' AND DEL_TF='N' AND CP_CODE = '$cp_code' " ;

		// echo $query;
		// exit;

		$result = mysql_query($query,$db);

		return $result;
	}
	
	//ȸ�� ��ȣ�� �˻�
	$cp_no = $_REQUEST['cp_no'];



	//ȸ������� �˻�
	$cp_type = urldecode (iconv("UTF-8", "EUC-KR", $_REQUEST['cp_type']));
	$cp_type	= trim($cp_type);
	if($search_field == "") 
		$search_field = "CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,MEMO";
	$search_str = urldecode (iconv("UTF-8", "EUC-KR", $_REQUEST['term']));
	$search_str	= trim($search_str);

	//ȸ�� �ڵ�� �ߺ��˻�
	$cp_code = $_REQUEST['cp_code'];

	if($cp_no != "")
	{
		$arr_rs = getCompanyInfoByNo($conn, $cp_no);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{
			$results .= "{\"CP_NO\":\"".$row['CP_NO']."\",
			   			  \"COMPANY\":\"".iconv("EUC-KR", "UTF-8",$row['CP_NM'])." ".iconv("EUC-KR", "UTF-8",$row['CP_NM2'])." [".$row['CP_CODE']."]\",
						  \"MANAGER_NM\":\"".iconv("EUC-KR", "UTF-8", $row['MANAGER_NM'])."\",
						  \"EMAIL\":\"".iconv("EUC-KR", "UTF-8",$row['EMAIL'])."\",
						  \"PHONE\":\"".iconv("EUC-KR", "UTF-8",$row['CP_PHONE'])."\",
						  \"HPHONE\":\"".iconv("EUC-KR", "UTF-8",$row['CP_HPHONE'])."\",
						  \"PHONE2\":\"".iconv("EUC-KR", "UTF-8",$row['PHONE'])."\",
						  \"RE_ZIP\":\"".iconv("EUC-KR", "UTF-8",$row['CP_ZIP'])."\",
						  \"RE_ADDR\":\"".iconv("EUC-KR", "UTF-8", $row['CP_ADDR'])."\",
						  \"DC_RATE\":\"".iconv("EUC-KR", "UTF-8", $row['DC_RATE'])."\",
						  \"BIZ_NO\":\"".iconv("EUC-KR", "UTF-8", $row['BIZ_NO'])."\",
						  \"CP_TYPE\":\"".iconv("EUC-KR", "UTF-8", $row['CP_TYPE'])."\",
						  \"CEO_NM\":\"".iconv("EUC-KR", "UTF-8", $row['CEO_NM'])."\",
						  \"SALE_ADM_NO\":\"".iconv("EUC-KR", "UTF-8", $row['SALE_ADM_NO'])."\",
						  \"ACCOUNT_BANK\":\"".iconv("EUC-KR", "UTF-8", $row['ACCOUNT_BANK'])."\",
						  \"ACCOUNT\":\"".iconv("EUC-KR", "UTF-8", $row['ACCOUNT'])."\",
						  \"ACCOUNT_OWNER_NM\":\"".iconv("EUC-KR", "UTF-8", $row['ACCOUNT_OWNER_NM'])."\"},";

		}
		
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	}
	else if($search_str != "")
	{
		$arr_rs = getCompanyName($conn, $cp_type, $search_field, $search_str);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{
			$results .= "{\"id\":\"".$row['CP_NO']."\",\"label\":\"".iconv("EUC-KR", "UTF-8",$row['CP_NM'])." ".iconv("EUC-KR", "UTF-8",$row['CP_NM2'])." [".$row['CP_CODE']."]\",\"dc_rate\":\"".$row['DC_RATE']."\",\"cp_type\":\"".iconv("EUC-KR", "UTF-8",$row['CP_TYPE'])."\"},";
		}
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	} else if($cp_code != "")
	{
		$arr_rs = dupCompanyByCPCode($conn, $cp_code);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{
			$results .= "{\"CNT\":\"".$row['CNT']."\"},";
		}
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;
	}
?>

