<?

	function selectCustomer($db, $groupNo, $search_field, $search_str, $nPage, $nRowCount) {
		
		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query = "SELECT  @ROWNUM := @ROWNUM +1 AS RN
						, A.CUSTOMER_NO
						, A.CUSTOMER_NM
						, A.DEPARTMENT
						, A.GROUP_NO
						, A.HPHONE
						, A.COMPANY_NM
						, A.POSITION
						, A.MEMO
						, A.DEL_TF
				    FROM TBL_CUSTOMER  A
				   WHERE A.GROUP_NO = '$groupNo' 
					 AND A.DEL_TF =  'N'
				";
		
		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (CUSTOMER_NM like '%".$search_str."%' OR COMPANY_NM like '%".$search_str."%')"; 
			
			} else {

				if ($search_field == "CUSTOMER_NM") {
					$query .= " AND (CUSTOMER_NM like '%".$search_str."%')"; 
				} else if ($search_field == "COMPANY_NM") {
					$query .= " AND (COMPANY_NM like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}					
		
		$query .= " ORDER BY CUSTOMER_NO ASC limit ".$offset.", ".$nRowCount;

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

	function totalCntCustomer($db, $groupNo, $search_field, $search_str){

		$query ="SELECT COUNT(*) CNT 
				   FROM TBL_CUSTOMER 
				  WHERE 1=1
					AND GROUP_NO = '$groupNo'
					AND DEL_TF =  'N'
				";

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (CUSTOMER_NM like '%".$search_str."%' OR COMPANY_NM like '%".$search_str."%')"; 
			
			} else {

				if ($search_field == "CUSTOMER_NM") {
					$query .= " AND (CUSTOMER_NM like '%".$search_str."%')"; 
				} else if ($search_field == "COMPANY_NM") {
					$query .= " AND (COMPANY_NM like '%".$search_str."%')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}			

		//echo"$query";
		//exit;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		
		return $record;
	}

	function CustGroupSelectBox($db,$objname,$size,$str,$val,$checkVal) {

		/*$query = "
					SELECT A.GROUP_NO, A.GROUP_NM, B.DCODE_EXT
					  FROM TBL_CUSTOMER_GROUP A, TBL_CODE_DETAIL B
					 WHERE A.GROUP_TYPE = B.DCODE
					   AND B.PCODE = 'SALES_PERSON_CODE'
					   AND A.DEL_TF = 'N'
					 GROUP BY A.GROUP_NO, A.GROUP_NM, B.DCODE_EXT
					 ORDER BY A.GROUP_NO
				 ";*/
				 
		$query = "				 
					SELECT GROUP_NO, GROUP_NM
					  FROM TBL_CUSTOMER_GROUP
					 WHERE DEL_TF = 'N'
					 GROUP BY GROUP_NO, GROUP_NM
					 ORDER BY GROUP_NO
				";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select id='".$objname."' name='".$objname."' class=\"box01\"  style='width:".$size."px;' onChange=\"js_".$objname."();\">";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_GROUP_NO		= Trim($row[0]);
			$RS_GROUP_NM		= Trim($row[1]);
			$RS_DCODE_EXT		= Trim($row[2]);

			//$tmp_str .= "<option value='".$RS_GROUP_NO."'>".$RS_GROUP_NM."</option>";

			if ($checkVal == $RS_GROUP_NO) {
				$tmp_str .= "<option value='".$RS_GROUP_NO."' selected>".$RS_GROUP_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_GROUP_NO."'>".$RS_GROUP_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

?>