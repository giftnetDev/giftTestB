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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/com/util/ImgUtil.php";

	function GoodsOptionInsert($db, $reg_adm, $goods_no, $goods_cd, $optionTp, $optionNo)
	{
		$qre = "SELECT MAX(OPTION_NO) + 1 FROM T_GOODS_OPTION ";		

		$result 		= mysql_query($qre,$db);
		$rows   		= mysql_fetch_array($result);
		$optionMaxNo  	= $rows[0];

		$query=	"	 
						INSERT INTO T_GOODS_OPTION 
							( 
							   OPTION_NO
							 , GOODS_NO
							 , GOODS_CODE
							 , OPTION_TYPE
							 , OPTION_GOODS_NO
							 , CP_CATE
							 , REG_DATE
							 , REG_ADM
							 )
						VALUE(
							   '$optionMaxNo'
							 , '$goods_no'
							 , '$goods_cd'
							 , '$optionTp'
							 , '$optionNo'
							 , (SELECT MAX( GOODS_CATE ) FROM TBL_GOODS_CATEGORY WHERE GOODS_NO = '$optionNo')
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

	function GoodsOptionSelInsert($db, $reg_adm, $goods_no, $optionTp, $strChkGoodsNo)
	{
		$qre = "SELECT MAX(OPTION_NO) + 1 FROM T_GOODS_OPTION ";		

		$result 		= mysql_query($qre,$db);
		$rows   		= mysql_fetch_array($result);
		$optionMaxNo  	= $rows[0];

		$query=	"	 
						INSERT INTO T_GOODS_OPTION
						SELECT @ROWNUM := @ROWNUM +1 AS ROWNUM, B . *
						FROM (
								SELECT '$goods_no'
									 , O.GOODS_CODE
									 , LEFT('$optionTp',1)
									 , O.GOODS_NO
									 , (SELECT MAX( GOODS_CATE ) FROM TBL_GOODS_CATEGORY WHERE GOODS_NO = O.GOODS_NO)
									 , '$reg_adm'
									 , NOW( )
								FROM TBL_GOODS O
								WHERE O.GOODS_NO IN(".$strChkGoodsNo.")
						)B, (
						SELECT @rownum := '$optionMaxNo'
						)C
					";		

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Delete_Goods_Option($db, $strOptionNo)
	{
		$query="DELETE 
				  FROM T_GOODS_OPTION 
				 WHERE OPTION_NO IN(".$strOptionNo.") ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function GoodsOptionConfirmUp($db, $reg_adm, $goods_no)
	{
		$query="UPDATE TBL_GOODS
				   SET OPTION_CF = 'Y'
				     , OPTION_ADM = '$reg_adm'
					 , OPTION_DATE = NOW()
				 WHERE GOODS_NO = '$goods_no'
				 ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function selGoods( $db, $optionTpNo, $goods_no, $optionTp, $stikerTp)
	{
		$query = " SELECT @ROWNUM := @ROWNUM +1 AS RN
						, GOODS_NO
						, GOODS_CODE
						, GOODS_NAME
						, (SELECT MAX( D.GOODS_CATE ) FROM TBL_GOODS_CATEGORY D WHERE D.GOODS_NO = '$goods_no') AS CP_CATE
						, (SELECT CP_NM 
							FROM TBL_COMPANY 
							WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03) AS CP_NAME
						, IMG_URL
						, FILE_NM_100
						, FILE_PATH_150
						, FILE_RNM_150
					FROM   TBL_GOODS , (SELECT @rownum:=0) B 
					WHERE  1 = 1 
					AND ( GOODS_CATE LIKE '".$optionTpNo."%' 
								OR GOODS_NO IN (SELECT GOODS_NO 
												FROM TBL_GOODS_CATEGORY 
												WHERE GOODS_CATE LIKE '".$optionTpNo."%' ) ) 
					AND GOODS_NO NOT IN ( SELECT OPTION_GOODS_NO FROM T_GOODS_OPTION WHERE GOODS_NO = '$goods_no' AND OPTION_TYPE = '$optionTp')
					AND CATE_04 = '∆«∏≈¡ﬂ' 
					AND USE_TF = 'Y' 
					AND DEL_TF = 'N' 	 			 	
			    ";
		
		if ($stikerTp != "") 
		{
			$query .= "AND GOODS_CATE LIKE '".$stikerTp."%'  ";			
		}
		
		$query .= "ORDER BY GOODS_NAME ASC
					   , GOODS_NO ASC ";
	
		//echo $query;
	
		$result = mysql_query($query,$db);
		$record = array();
	
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		for($i=0;$i<sizeof($record);$i++){
			$record[$i]["GOODS_NAME"] = iconv("euc-kr","utf-8",$record[$i]["GOODS_NAME"]);
			$record[$i]["IMG_URL"]	 = getGoodsImage($record[$i]["FILE_NM_100"], $record[$i]["IMG_URL"], $record[$i]["FILE_PATH_150"], $record[$i]["FILE_RNM_150"], "50", "50");
		}

		return $record;
	}

	function selGoodsOption($db, $goods_no, $optionTp)
	{
		$query = "SELECT @ROWNUM := @ROWNUM +1 AS RN
					   , A.OPTION_NO
					   , A.GOODS_NO
					   , C.GOODS_CODE
					   , A.OPTION_TYPE
					   , A.OPTION_GOODS_NO
					   , C.GOODS_NAME AS OPTION_GOODS_NAME
					   , A.CP_CATE
					   , (SELECT D.CATE_NAME FROM TBL_CATEGORY D WHERE D.CATE_CD = A.CP_CATE) AS CATE_NAME
					   , A.REG_ADM
					   , A.REG_DATE 
					   , C.FILE_NM_100
					   , C.IMG_URL
					   , C.FILE_PATH_150
					   , FILE_RNM_150
				    FROM T_GOODS_OPTION A
					 JOIN TBL_GOODS C ON C.GOODS_NO = A.OPTION_GOODS_NO
				       , (SELECT @rownum:=0) B 
				   WHERE 1 = 1
				     AND A.GOODS_NO = '$goods_no' 
					 AND A.OPTION_TYPE = '$optionTp'
				   ORDER BY A.GOODS_CODE ASC	 			 	
			    ";
	
		//echo $query;
	
		$result = mysql_query($query,$db);
		$record = array();
	
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		for($i=0;$i<sizeof($record);$i++){
			$record[$i]["OPTION_GOODS_NAME"] = iconv("euc-kr","utf-8",$record[$i]["OPTION_GOODS_NAME"]);
			$record[$i]["CATE_NAME"]		 = iconv("euc-kr","utf-8",$record[$i]["CATE_NAME"]);
			$record[$i]["REG_ADM"] 			 = iconv("euc-kr","utf-8",getAdminName($db,$record[$i]["REG_ADM"]));
			$record[$i]["IMG_URL"]			 = getGoodsImage($record[$i]["FILE_NM_100"], $record[$i]["IMG_URL"], $record[$i]["FILE_PATH_150"], $record[$i]["FILE_RNM_150"], "50", "50");
		}

		return $record;
	}

	function selGoodsBox($db, $stiker_type)
	{
		$query = " SELECT CATE_CD
						, CATE_NAME
						, CONCAT(CATE_SEQ01, CATE_SEQ02, CATE_SEQ03, CATE_SEQ04) AS SEQ
					FROM TBL_CATEGORY 
				   WHERE 1 = 1 
					 AND CATE_CD LIKE '0103%' 
					 AND DEL_TF = 'N'    
					 AND CATE_CD <> '0103'	
			    ";

		if ($stiker_type <> "") {
			$query .= " AND CATE_CD = '".$stiker_type."' ";
		}
		
		$query .= " ORDER BY SEQ ASC  ";		
	
		//echo $query;
	
		$result = mysql_query($query,$db);
		$record = array();
	
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		for($i=0;$i<sizeof($record);$i++){
			$record[$i]["CATE_NAME"] = iconv("euc-kr","utf-8",$record[$i]["CATE_NAME"]);
		}

		return $record;
	}

	function Selectgoods($db, $goods_no)
	{
		$query 	=" SELECT GOODS_NO
						, GOODS_CATE
						, GOODS_CODE
						, GOODS_NAME
						, CATE_03
						, SALE_PRICE
						, DELIVERY_CNT_IN_BOX
						, TAX_TF
						, IMG_URL
						, FILE_NM_100
						, FILE_PATH_150
						, FILE_RNM_150
						, OPTION_CF
						, OPTION_DATE
				FROM TBL_GOODS
				WHERE GOODS_NO = '$goods_no'
			";

		$result = mysql_query($query,$db);
		$record = array();
		//echo $query;
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	/************************************************************************************************************************************ */

	if($mode=="GOODS_OPTION_SAVE")
	{
		if(GoodsOptionInsert($conn, $reg_adm, $goods_no, $goods_cd, $optionTp, $optionNo, $cpcate) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}
	
	if($mode=="GOODS_OPTION_SEL_SAVE")
	{
		$arr_chk_goods_no = $_POST['chk_goods_no'];
		$cnt = sizeof($arr_chk_goods_no);
		
		$strChkGoodsNo="";

		for($i = 0; $i < $cnt; $i++){
			$strChkGoodsNo.=$arr_chk_goods_no[$i].",";
		}
		
		$strChkGoodsNo=rtrim($strChkGoodsNo, ",");

		if(GoodsOptionSelInsert($conn, $reg_adm, $goods_no, $optionTp, $strChkGoodsNo) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}

	if($mode=="GOODS_OPTION_DEL")
	{
		$arr_option_no = $_POST['option_no'];
		$cnt = sizeof($arr_option_no);
		
		$strOptionNo="";

		for($i = 0; $i < $cnt; $i++){
			$strOptionNo.=$arr_option_no[$i].",";
		}
		
		$strOptionNo=rtrim($strOptionNo, ",");

		if(Delete_Goods_Option($conn,$strOptionNo) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}
	}

	if($mode=="GOODS_OPTION_CONFIRM")
	{
		if(GoodsOptionConfirmUp($conn, $reg_adm, $goods_no) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}

	if($mode == "SEL_STIKER")
	{
		echo json_encode(selGoods($conn, $optionTpNo, $goods_no, $optionTp, $stikerTp));
	}

	if($mode == "SEL_STIKER_OPTION")
	{
		echo json_encode(selGoodsOption($conn, $goods_no, $optionTp));
	}

	if($mode == "SELECT_GOODS_BOX")
	{
		echo json_encode(selGoodsBox($conn, $stiker_type));
	}

	if($mode=="SELECT_GOODS")
	{
		$arr = Selectgoods($conn, $goods_no);
		
		if(sizeof($arr) > 0) 
		{
			$GOODS_CATE 	= $arr[0]["GOODS_CATE"];
			$GOODS_CODE 	= $arr[0]["GOODS_CODE"];
			$GOODS_NAME 	= iconv("euckr","utf8", $arr[0]["GOODS_NAME"]);
			$CATE_03 		= $arr[0]["CATE_03"];
			$SALE_PRICE 	= number_format($arr[0]["SALE_PRICE"]);
			$DELIVERY_CNT_IN_BOX 	= $arr[0]["DELIVERY_CNT_IN_BOX"];
			$TAX_TF 		= iconv("euckr","utf8", $arr[0]["TAX_TF"]);
			$IMG_URL 		= $arr[0]["IMG_URL"];
			$FILE_NM_100 	= $arr[0]["FILE_NM_100"];
			$FILE_PATH_150 	= $arr[0]["FILE_PATH_150"];
			$FILE_RNM_150 	= $arr[0]["FILE_RNM_150"];
			$OPTION_CF		= $arr[0]["OPTION_CF"];
			$OPTION_DATE	= $arr[0]["OPTION_DATE"];

			$cate_03 	= iconv("euckr","utf8", getCompanyName($conn, $CATE_03));

			$img_url	= getGoodsImage($FILE_NM_100, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "250", "250");

			$result = "Y";
			echo "[{
				\"RESULT\":\"".$result."\"
				,\"GOODS_CATE\":\"".$GOODS_CATE."\"
				,\"GOODS_CODE\":\"".$GOODS_CODE."\"
				,\"GOODS_NAME\":\"".$GOODS_NAME."\"
				,\"CATE_03\":\"".$cate_03."\"
				,\"SALE_PRICE\":\"".$SALE_PRICE."\"
				,\"DELIVERY_CNT_IN_BOX\":\"".$DELIVERY_CNT_IN_BOX."\"
				,\"TAX_TF\":\"".$TAX_TF."\"
				,\"IMG_URL\":\"".$img_url."\"
				,\"FILE_NM_100\":\"".$FILE_NM_100."\"
				,\"FILE_PATH_150\":\"".$FILE_PATH_150."\"
				,\"FILE_RNM_150\":\"".$FILE_RNM_150."\"
				,\"OPTION_CF\":\"".$OPTION_CF."\"
				,\"OPTION_DATE\":\"".$OPTION_DATE."\"
				}]";
		}
		else
		{
			$result = "N";
			echo "[{\"RESULT\":\"".$result."\"}]";
		}
	}

	
?>

