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
	require "../../_classes/biz/confirm/confirm.php";



	
	//회사 번호로 검색
	$cp_no = $_REQUEST['cp_no'];

	if($cp_no != "")
	{
		$arr_rs = SumCompanyLedger($conn, $cp_no);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{				
			$results .= "{\"SUM_WITHDRAW\":\"".$row['SUM_WITHDRAW']."\",
			   			  \"SUM_DEPOSIT\":\"".$row['SUM_DEPOSIT']."\",
						  \"SUM_BALANCE\":\"".$row['SUM_BALANCE']."\"},";
		}
		
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	}

	if($mode == "GET_TOTAL_PRICE")
	{
		$arr_rs = getTaxInvoicePrice($conn, $cf_code);

		$results = "[";

		if(sizeof($arr_rs) > 0) { 

			for($i = 0; $i < sizeof($arr_rs); $i++) { 

				$TOTAL_PRICE = $arr_rs[$i]["TOTAL_PRICE"];

				$results .= "{\"TOTAL_PRICE\":\"".$TOTAL_PRICE."\"},";

			}

		}
		
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	}

	
?>

