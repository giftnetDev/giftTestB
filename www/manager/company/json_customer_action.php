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

	function Update_Del($db, $reg_adm, $custno)
	{
		$query="UPDATE TBL_CUSTOMER 
				   SET DEL_TF = 'Y'
				   	 , DEL_DATE = NOW() 
				   	 , DEL_ADM = '$reg_adm'
				WHERE CUSTOMER_NO = '$custno'
				";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Update_GroupCnt($db, $reg_adm, $groupNo)
	{
		$query=" UPDATE TBL_CUSTOMER_GROUP
					SET GROUP_CNT = (SELECT COUNT( 1 ) AS CNT
									   FROM TBL_CUSTOMER
									  WHERE GROUP_NO = '$groupNo'
										AND DEL_TF = 'N')
				   	  , UP_DATE = NOW() 
				   	  , UP_ADM = '$reg_adm'
				  WHERE GROUP_NO = '$groupNo'
				";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Update_AllGroupCnt($db, $reg_adm)
	{
		$query=" UPDATE TBL_CUSTOMER_GROUP A, 
						( SELECT GROUP_NO, COUNT( 1 ) AS CNT
							FROM TBL_CUSTOMER B
						   WHERE B.DEL_TF = 'N'
						   GROUP BY GROUP_NO) B
					SET A.GROUP_CNT = B.CNT
					  , A.UP_DATE = NOW() 
				   	  , A.UP_ADM = '$reg_adm'
				  WHERE A.DEL_TF = 'N'					 
					AND A.GROUP_NO = B.GROUP_NO	
				";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Update_Cust_Del($db, $reg_adm, $arrCustNo)
	{
		$query="UPDATE TBL_CUSTOMER 
				   SET DEL_TF = 'Y'
				   	 , DEL_DATE = NOW() 
				   	 , DEL_ADM = '$reg_adm'
				WHERE CUSTOMER_NO IN(".$arrCustNo.") ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function SelectCustCnt($db, $groupNo)  
	{
		$query = " SELECT A.DCODE_EXT, (SELECT COUNT( 1 ) AS CNT
										  FROM TBL_CUSTOMER
										 WHERE GROUP_NO = '$groupNo'
										   AND DEL_TF = 'N')AS CNT
					 FROM TBL_CODE_DETAIL A, TBL_CUSTOMER_GROUP B
					WHERE A.PCODE = 'SALES_PERSON_CODE'
					  AND A.DCODE = B.GROUP_TYPE
					  AND B.GROUP_NO = '$groupNo'
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

	function CustUpdate($db, $reg_adm, $customer_no, $Ncustomer_nm, $Ncompany_nm, $NhPhone, $Ndepartment, $Nposition, $Nmemo, $Ngroup_no)
	{
		$query=	"	 
						UPDATE TBL_CUSTOMER 
						   SET CUSTOMER_NM 		= '$Ncustomer_nm'
						   	 , COMPANY_NM 		= '$Ncompany_nm'
							 , HPHONE			= '$NhPhone'
							 , DEPARTMENT		= '$Ndepartment'
							 , POSITION			= '$Nposition'
							 , MEMO				= '$Nmemo'
							 , GROUP_NO			= '$Ngroup_no'
							 , UP_DATE 			= NOW() 
							 , UP_ADM 			= '$reg_adm'
						 WHERE CUSTOMER_NO 		= '$customer_no' 
					";		

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function CustInsert($db, $reg_adm, $customer_no, $customer_nm, $company_nm, $hPhone, $department, $position, $memo, $Ngroup_no)
	{
		$query=	"	 
						INSERT INTO TBL_CUSTOMER 
							( 
							   CUSTOMER_NM
							 , COMPANY_NM
							 , GROUP_NO
							 , HPHONE
							 , DEPARTMENT
							 , POSITION
							 , MEMO
							 , DEL_TF
							 , REG_DATE
							 , REG_ADM
							 )
						VALUE(
							   '$customer_nm'
							 , '$company_nm'
							 , '$Ngroup_no'
							 , '$hPhone'
							 , '$department'
							 , '$position'
							 , '$memo'
							 , 'N'
							 , NOW() 
							 , '$reg_adm'
							 )
					";		

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Cust_Move_Update($db, $reg_adm,$groupNo,$arrCustNo)
	{
		$query="UPDATE TBL_CUSTOMER 
				   SET GROUP_NO = '$groupNo'
				   	 , UP_DATE = NOW() 
				   	 , UP_ADM = '$reg_adm'
				WHERE CUSTOMER_NO IN(".$arrCustNo.") ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	if($mode=="CUSTOMER_DEL")
	{
		if(Update_Del($conn,$reg_adm,$customer_no) == 1 )
		{
			if(Update_GroupCnt($conn,$reg_adm, $groupNo) == 1 )
			{
				echo 1;
			}
			else 
			{
				echo 0;
			}	
		}
		else 
		{
			echo 0;
		}
	}
	
	
	if($mode=="CUSTOMER_SELDEL")
	{
		$arr_customer_no = $_POST['customer_no'];
		$cnt = sizeof($arr_customer_no);
		
		$strCustNo="";

		for($i = 0; $i < $cnt; $i++){
			$strCustNo.=$arr_customer_no[$i].",";
		}
		
		$strCustNo=rtrim($strCustNo, ",");

		if(Update_Cust_Del($conn,$reg_adm,$strCustNo) == 1 )
		{
			if(Update_GroupCnt($conn,$reg_adm, $groupNo) == 1 )
			{
				echo 1;
			}
			else 
			{
				echo 0;
			}
		}
		else 
		{
			echo 0;
		}
	}

	if($mode=="CUSTOMER_MERGE")
	{
		$Ncustomer_nm 		= iconv("utf8","euckr",$customer_nm);
		$Ncompany_nm 		= iconv("utf8","euckr",$company_nm);
		$NhPhone 			= iconv("utf8","euckr",$hPhone);
		$Ndepartment 		= iconv("utf8","euckr",$department);
		$Nposition 			= iconv("utf8","euckr",$position);
		$Nmemo 				= iconv("utf8","euckr",$memo);
		$Ngroup_no 			= iconv("utf8","euckr",$groupNo);

		if($customer_no != "")
		{
			if($groupType == 0)
			{
				$result = CustUpdate($conn, $reg_adm, $customer_no, $Ncustomer_nm, $Ncompany_nm, $NhPhone, $Ndepartment, $Nposition, $Nmemo, $Ngroup_no);

				if(Update_GroupCnt($conn,$reg_adm, $groupNo) == 1 )
				{
					echo "[{\"RESULT\":\"".$result."\"}]";
				}
				else 
				{
					echo "[{\"RESULT\":\"".$result."\"}]";
				}
			}
			else
			{
				$arr = SelectCustCnt($conn, $groupNo);

				$DCODE_EXT = $arr[0]["DCODE_EXT"];
				$GROUP_CNT = $arr[0]["CNT"];

				if($DCODE_EXT > $GROUP_CNT)
				{					
					$result = CustUpdate($conn, $reg_adm, $customer_no, $Ncustomer_nm, $Ncompany_nm, $NhPhone, $Ndepartment, $Nposition, $Nmemo, $Ngroup_no);

					if(Update_AllGroupCnt($conn,$reg_adm, $groupNo) == 1 )
					{
						echo "[{\"RESULT\":\"".$result."\"}]";
					}
					else 
					{
						echo "[{\"RESULT\":\"".$result."\"}]";
					}
				}
				else
				{
					$result = "N";
	
					echo "[{\"RESULT\":\"".$result."\",\"GROUP_CNT\":\"".$DCODE_EXT."\"}]";
				}
			}
			
		}
		else
		{
			$arr = SelectCustCnt($conn, $groupNo);

			$DCODE_EXT = $arr[0]["DCODE_EXT"];
			$GROUP_CNT = $arr[0]["CNT"];

			if($DCODE_EXT > $GROUP_CNT)
			{
				$result = CustInsert($conn, $reg_adm, $customer_no, $Ncustomer_nm, $Ncompany_nm, $NhPhone, $Ndepartment, $Nposition, $Nmemo, $Ngroup_no);

				if(Update_AllGroupCnt($conn,$reg_adm, $groupNo) == 1 )
				{
					echo "[{\"RESULT\":\"".$result."\"}]";
				}
				else 
				{
					echo "[{\"RESULT\":\"".$result."\"}]";
				}
			}
			else
			{
				$result = "N";

				echo "[{\"RESULT\":\"".$result."\",\"GROUP_CNT\":\"".$DCODE_EXT."\"}]";
			}
		}		
	}

	if($mode=="CUSTOMER_GROUP_MOVE")
	{
		$arr_customer_no = $_POST['customer_no'];
		$cnt = sizeof($arr_customer_no);

		$arr = SelectCustCnt($conn, $groupNo);

		$DCODE_EXT = $arr[0]["DCODE_EXT"];
		$GROUP_CNT = $arr[0]["CNT"];

		$TotalCnt  = $cnt+$GROUP_CNT;

		if($DCODE_EXT >= $TotalCnt)
		{
			$strCustNo="";

			for($i = 0; $i < $cnt; $i++){
				$strCustNo.=$arr_customer_no[$i].",";
			}
			
			$strCustNo=rtrim($strCustNo, ",");

			$result = Cust_Move_Update($conn,$reg_adm,$groupNo,$strCustNo);
			
			if(Update_AllGroupCnt($conn,$reg_adm, $groupNo) == 1 )
			{
				echo "[{\"RESULT\":\"".$result."\"}]";
			}
			else 
			{
				echo "[{\"RESULT\":\"".$result."\"}]";
			}
		}
		else
		{
			$result = "N";

			echo "[{\"RESULT\":\"".$result."\",\"GROUP_CNT\":\"".$DCODE_EXT."\"}]";
		}		
	}
	
?>

