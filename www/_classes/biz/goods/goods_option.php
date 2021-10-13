<?
	# =============================================================================
	# File Name    : goods_option.php
	# =============================================================================

	function OptionlistGoods($db, $goods_cate, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount,$total_cnt, $option_yn, $option_confirm_yn) 
	{

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query_r = "set @rownum = ".$logical_num ."; ";
		mysql_query($query_r,$db);

		$query .= "SELECT C.* FROM (
						SELECT @rownum:= @rownum - 1  as rn, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
										 IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, REG_DATE
										 , (SELECT CASE WHEN COUNT(1) = 0 THEN 'N' ELSE 'Y' END FROM T_GOODS_OPTION B WHERE B.GOODS_NO = A.GOODS_NO AND B.OPTION_TYPE = 'S') AS OPTION_YN
										 , OPTION_CF
										 , OPTION_ADM
										 , OPTION_DATE
						  FROM TBL_GOODS A
						 WHERE 1 = 1 
				   		   AND DEL_TF = 'N'		
						   AND USE_TF = 'Y'			
					";
		
		if ($goods_cate <> "") 
		{
			if (strpos($goods_cate, ',') !== false) {

				$query .= " AND ( ";
				foreach (explode(",", $goods_cate) as $splited_goods_cate){
					
					$query .= " GOODS_CATE like '".$splited_goods_cate."%' OR";

				}

				$query = rtrim($query, "OR");
				$query .= " ) ";
			} else { 
				$query .= " AND (GOODS_CATE like '".$goods_cate."%' 
							 OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))
						  ";
			}
		}
		
		if ($search_str <> "") 
		{
			if ($search_field == "ALL") 
			{
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%') ";

			
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//상품번호
			}  else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND GOODS_CODE IN (".$query2.") ";
			} 
		}

		$query .= " )C WHERE 1=1";
		
		if ($option_yn == "ALL") 
		{
			$query .= "";			
		}
		else if ($option_yn == "Y")
		{ 
			$query .= " AND OPTION_YN = 'Y' ";
		}
		else if ($option_yn == "N")
		{
			$query .= " AND OPTION_YN = 'N' ";
		}
		
		if ($option_confirm_yn == "ALL") 
		{
			$query .= "";			
		}
		else if ($option_confirm_yn == "Y")
		{ 
			$query .= " AND OPTION_CF = 'Y' ";
		}
		else if ($option_confirm_yn == "N")
		{
			$query .= " AND OPTION_CF = 'N' ";
		} 

		if ($order_field == "") 
		{
			$order_field = " ORDER BY REG_DATE";
		} 
		else 
		{ 
			$order_field = " ORDER BY ".$order_field;
		}

		
		if ($order_str == "") 
			$order_str = "DESC";

		$query .= $order_field." ".$order_str.", GOODS_NO ASC limit ".$offset.", ".$nRowCount;

		//echo $query."<br/><br/>";
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

	function totalCntGoodsOption($db, $goods_cate, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount,$total_cnt, $option_yn, $option_confirm_yn) 
	{
		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query_r = "set @rownum = ".$logical_num ."; ";
		mysql_query($query_r,$db);

		$query .= "SELECT COUNT( 1 ) AS CNT FROM (
						SELECT @rownum:= @rownum - 1  as rn, GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
										 IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, REG_DATE
										 , (SELECT CASE WHEN COUNT(1) = 0 THEN 'N' ELSE 'Y' END FROM T_GOODS_OPTION B WHERE B.GOODS_NO = A.GOODS_NO AND B.OPTION_TYPE = 'S') AS OPTION_YN
										 , OPTION_CF
										 , OPTION_ADM
										 , OPTION_DATE
						  FROM TBL_GOODS A
						 WHERE 1 = 1 
				   		   AND DEL_TF = 'N'				
						   AND USE_TF = 'Y'			
					";

		if ($goods_cate <> "") 
		{
			if (strpos($goods_cate, ',') !== false) {

				$query .= " AND ( ";
				foreach (explode(",", $goods_cate) as $splited_goods_cate){
					
					$query .= " GOODS_CATE like '".$splited_goods_cate."%' OR";

				}

				$query = rtrim($query, "OR");
				$query .= " ) ";
			} else { 
				$query .= " AND (GOODS_CATE like '".$goods_cate."%' 
							OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))
						";
			}
		}			
		
		if ($search_str <> "") 
		{
			if ($search_field == "ALL") 
			{
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%') ";

			
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//상품번호
			}  else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND GOODS_CODE IN (".$query2.") ";
			} 
		}

		$query .= " )C WHERE 1=1";
		
		if ($option_yn == "ALL") 
		{
			$query .= "";			
		}
		else if ($option_yn == "Y")
		{ 
			$query .= " AND OPTION_YN = 'Y' ";
		}
		else if ($option_yn == "N")
		{
			$query .= " AND OPTION_YN = 'N' ";
		} 
		
		
		if ($option_confirm_yn == "ALL") 
		{
			$query .= "";			
		}
		else if ($option_confirm_yn == "Y")
		{ 
			$query .= " AND OPTION_CF = 'Y' ";
		}
		else if ($option_confirm_yn == "N")
		{
			$query .= " AND OPTION_CF = 'N' ";
		} 
		//echo $query."<br/><br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

?>