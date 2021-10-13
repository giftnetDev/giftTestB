<?

	function listGoodsEstimate($db, $start_date, $end_date, $cp_no, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntGoodsEstimate($db, $start_date, $end_date, $cp_no, $del_tf, $search_field, $search_str, $order_field, $order_str);

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query =    "SELECT 
						@rownum:=@rownum - 1 AS rn,
						GP_NO,
						GROUP_NO,
						CP_NO,
						MEMO,
						REQUEST_TYPE,
						SENT_EMAIL,
						EMAIL_SUBJECT,
						EMAIL_BODY,
						IS_SENT,
						SENT_DATE,
						REG_ADM,
						REG_DATE,
						TOTAL_QTY,
						TOTAL_SALE_PRICE,
						TOTAL_DISCOUNT_PRICE,
						TOTAL_SA_DELIVERY_PRICE,
						GRAND_TOTAL_SALE_PRICE
					FROM
						TBL_ESTIMATE
					WHERE
						1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') OR GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str.")) ";
				else
					$query .= "AND (CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') OR GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' )) ";
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' ) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		
		if ($order_field == "") 
			$order_field = "REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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


	function totalCntGoodsEstimate($db, $start_date, $end_date, $cp_no, $del_tf, $search_field, $search_str, $order_field, $order_str){

		$query ="SELECT COUNT(*) CNT FROM TBL_ESTIMATE WHERE 1 = 1 ";

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND CP_NO = '".$cp_no."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') OR GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str.")) ";
				else
					$query .= "AND (CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') OR GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' )) ";
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CP_NO IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND GP_NO IN (SELECT GP_NO FROM TBL_ESTIMATE_SUB WHERE GOODS_NAME like '%".$search_str."%' ) ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

        //echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertGoodsEstimate($db, $group_no, $cp_no, $dc_rate, $memo, $reg_adm, $TOTAL_QTY,$TOTAL_SALE_PRICE,$TOTAL_DISCOUNT_PRICE,$TOTAL_SA_DELIVERY_PRICE,$GRAND_TOTAL_SALE_PRICE, $goods_cate = "") {

		$arr_estimate_cp = selectCompany($db, $cp_no);
		$ESTIMATE_EMAIL = $arr_estimate_cp[0]["EMAIL"];

		if($goods_cate <> "")
			$query="INSERT INTO TBL_ESTIMATE (GROUP_NO, CP_NO, GOODS_CATE, DC_RATE, MEMO, SENT_EMAIL, REG_ADM, REG_DATE,TOTAL_QTY,TOTAL_SALE_PRICE,TOTAL_DISCOUNT_PRICE,TOTAL_SA_DELIVERY_PRICE,GRAND_TOTAL_SALE_PRICE) 
				 VALUES ('$group_no', '$cp_no', '$goods_cate', '$dc_rate', '$memo', '$ESTIMATE_EMAIL', '$reg_adm', now(),'$TOTAL_QTY','$TOTAL_SALE_PRICE','$TOTAL_DISCOUNT_PRICE','$TOTAL_SA_DELIVERY_PRICE','$GRAND_TOTAL_SALE_PRICE') ";
		else
			$query="INSERT INTO TBL_ESTIMATE (GROUP_NO, CP_NO, DC_RATE, MEMO, SENT_EMAIL, REG_ADM, REG_DATE,TOTAL_QTY,TOTAL_SALE_PRICE,TOTAL_DISCOUNT_PRICE,TOTAL_SA_DELIVERY_PRICE,GRAND_TOTAL_SALE_PRICE) 
				 VALUES ('$group_no', '$cp_no', '$dc_rate', '$memo', '$ESTIMATE_EMAIL', '$reg_adm', now(),'$TOTAL_QTY','$TOTAL_SALE_PRICE','$TOTAL_DISCOUNT_PRICE','$TOTAL_SA_DELIVERY_PRICE','$GRAND_TOTAL_SALE_PRICE') ";

		//echo "신규 견적서 쿼리".$query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			$query = "SELECT MAX(GP_NO)
						FROM TBL_ESTIMATE  
					   WHERE DEL_TF =  'N'  ";
		
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			return $rows[0];
		}
		
	}

	function insertGoodsEstimateGoods($db, $gp_no, $group_no, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $reg_adm, $qty, $supply_price, $goods_cate = "", $page = "", $seq = "") { 

		$arr_goods = selectGoodsProposal($db, $goods_no);
		if(sizeof($arr_goods) > 0) {
			$COMPONENT		   = $arr_goods[0]["COMPONENT"];
			$DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
			$DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
			$ORIGIN			   = $arr_goods[0]["ORIGIN"];

			$DESCRIPTION_BODY = str_replace("//","\n",$DESCRIPTION_BODY);

			if($DESCRIPTION_TITLE != "" || $DESCRIPTION_BODY != "")
				$DESCRIPTION  = $DESCRIPTION_TITLE."\n\n".$DESCRIPTION_BODY;
		}

		
		if($COMPONENT == "" || $DESCRIPTION == "") {  
			$arr_goods_sub = selectGoodsSub($db, $goods_no);
			
			$SUB_TOTAL_COMPONENT = "";
			$SUB_TOTAL_DESCRIPTION = "";
			if (sizeof($arr_goods_sub) > 0) {

				for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
					$sub_goods_no			= trim($arr_goods_sub[$jk]["GOODS_SUB_NO"]);
					$goods_cnt				= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
					
					$arr_goods = selectGoodsProposal($db, $sub_goods_no);
					if(sizeof($arr_goods) > 0) {
						$SUB_COMPONENT		   = $arr_goods[0]["COMPONENT"];
						$SUB_DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
						$SUB_DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
						$SUB_DESCRIPTION_BODY = str_replace("//","\n",$SUB_DESCRIPTION_BODY);

						if($SUB_COMPONENT <> "")
							$SUB_COMPONENT = $SUB_COMPONENT."(".$goods_cnt."입)";
					} else {
						$SUB_COMPONENT = "";
						$SUB_DESCRIPTION_TITLE = "";
						$SUB_DESCRIPTION_BODY = "";
					}

					if($COMPONENT == "") { 
						$SUB_TOTAL_COMPONENT .= ($SUB_TOTAL_COMPONENT != "" && $SUB_COMPONENT != "" ? ", " : "").$SUB_COMPONENT;
					}

					if($DESCRIPTION == "") { 
						if($SUB_DESCRIPTION_TITLE != "") 
							$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_TITLE."\n\n";
						
						if($SUB_DESCRIPTION_BODY != "")
							$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_BODY."\n\n";
					}

					//$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
					//$sub_goods_cate			= trim($arr_goods_sub[$jk]["GOODS_CATE"]);
						
					//if(!startsWith($sub_goods_cate, '0102')) 
					//	$COMPONENT .=  $sub_goods_name." (".$sub_goods_cnt."입)<br />";
				}
			} 

			if($COMPONENT == "") 
				$COMPONENT = $SUB_TOTAL_COMPONENT;

			if($DESCRIPTION == "") 
				$DESCRIPTION = $SUB_TOTAL_DESCRIPTION;

		}

		$arr_goods = selectGoods($db, $goods_no);
		if (sizeof($arr_goods) > 0) {
			$SIZE			= trim($arr_goods[0]["GOODS_SUB_NAME"]);
			$MANUFACTURER	= trim($arr_goods[0]["CATE_02"]);
		}

		$COMPONENT=SetStringToDB($COMPONENT);
		$DESCRIPTION=SetStringToDB($DESCRIPTION);


		if($goods_cate <> "")
			$query="INSERT INTO TBL_ESTIMATE_SUB 
						(GP_NO, GROUP_NO, GOODS_NO, GOODS_NAME, DELIVERY_CNT_IN_BOX, RETAIL_PRICE, ESTIMATE_PRICE, SIZE, COMPONENT, DESCRIPTION, MANUFACTURER, ORIGIN, GOODS_CATE, PAGE, SEQ, REG_ADM, REG_DATE, QTY, SUPPLY_PRICE) 
				 VALUES ('$gp_no', '$group_no', '$goods_no', '$goods_nm', '$delivery_cnt_in_box', '$retail_price', '$estimate_price', '$SIZE', '$COMPONENT', '$DESCRIPTION', '$MANUFACTURER', '$ORIGIN', '$goods_cate', '$page', '$seq', '$reg_adm', now(), $qty, $supply_price) ";
		else
			$query="INSERT INTO TBL_ESTIMATE_SUB 
						(GP_NO, GROUP_NO, GOODS_NO, GOODS_NAME, DELIVERY_CNT_IN_BOX, RETAIL_PRICE, ESTIMATE_PRICE, SIZE, COMPONENT, DESCRIPTION, MANUFACTURER, ORIGIN, REG_ADM, REG_DATE, QTY, SUPPLY_PRICE) 
				 VALUES ('$gp_no', '$group_no', '$goods_no', '$goods_nm', '$delivery_cnt_in_box', '$retail_price', '$estimate_price', '$SIZE', '$COMPONENT', '$DESCRIPTION', '$MANUFACTURER', '$ORIGIN', '$reg_adm', now(), $qty, $supply_price) ";

		// echo "신규 견적서 상품 쿼리 : ".$query."<br/>";
		// exit;

		if(!mysql_query($query,$db)) {
			
			echo "<script>alert(\"[1]오류가 발생하였습니다 1- ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
			return false;
		} else {
			return true;
		}

	}
	
	function selectGoodsEstimateByGpNo($db, $gp_no) {

		$query =   "SELECT 
						GROUP_NO,
						CP_NO,
						GOODS_CATE,
						DC_RATE,
						MEMO,
						REQUEST_TYPE,
						SENT_EMAIL,
						EMAIL_SUBJECT,
						EMAIL_BODY,
						IS_SENT,
						SENT_DATE,
						REG_DATE,
						TOTAL_QTY,
						TOTAL_SALE_PRICE,
						TOTAL_DISCOUNT_PRICE,
						TOTAL_SA_DELIVERY_PRICE,
						GRAND_TOTAL_SALE_PRICE
					FROM
						TBL_ESTIMATE
					WHERE
						GP_NO = '$gp_no' AND DEL_TF = 'N' 
		";

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

	function selectGoodsEstimateGoods($db, $gpg_no) { 

		$query = "SELECT GOODS_NO, GOODS_NAME, SIZE, COMPONENT, DESCRIPTION, ESTIMATE_PRICE, RETAIL_PRICE, DELIVERY_CNT_IN_BOX, MANUFACTURER, ORIGIN
					FROM TBL_ESTIMATE_SUB 
				   WHERE GPG_NO = '$gpg_no' AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";

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

	function listGoodsEstimateGoods($db, $gp_no, $cancel_tf) {

		$query = "SELECT GPG.GPG_NO, GPG.GP_NO, GPG.GOODS_NO, G.GOODS_CODE, GPG.GOODS_NAME, GPG.RETAIL_PRICE, GPG.DELIVERY_CNT_IN_BOX, 
						 GPG.COMPONENT, GPG.DESCRIPTION, GPG.SIZE, GPG.MANUFACTURER, GPG.ORIGIN, GPG.GOODS_CATE, GPG.PAGE, GPG.SEQ,
						 GPG.ESTIMATE_PRICE, GPG.UP_ADM, GPG.UP_DATE, GPG.CANCEL_TF, GPG.CANCEL_DATE, GPG.CANCEL_ADM, GPG.QTY, GPG.SUPPLY_PRICE
					FROM TBL_ESTIMATE_SUB GPG 
					JOIN TBL_GOODS G ON GPG.GOODS_NO = G.GOODS_NO
				   WHERE GPG.GP_NO = '$gp_no' AND GPG.DEL_TF = 'N' ";


		if ($cancel_tf <> "") {
			$query .= " AND GPG.CANCEL_TF = '".$cancel_tf."' ";
		}

		//$query .= " AND GPG.CANCEL_TF = '".$cancel_tf."' ";

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

	function cntMaxGroupNoEstimate($db) { 

		$query = "SELECT IFNULL( MAX( GROUP_NO ) , 0 ) +1 AS GROUP_NO
					FROM TBL_ESTIMATE  
				   WHERE DEL_TF = 'N' AND DATE_FORMAT(REG_DATE,'%Y-%m-%d') = CURDATE()  ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	function updateEstimate($db, $dc_rate, $memo, $gp_no, $up_adm,$TOTAL_QTY,$TOTAL_SALE_PRICE,$TOTAL_DISCOUNT_PRICE,$TOTAL_SA_DELIVERY_PRICE,$GRAND_TOTAL_SALE_PRICE) {
		$query="UPDATE TBL_ESTIMATE 
				   SET DC_RATE = '$dc_rate',
					   MEMO = '$memo',
					   UP_ADM = '$up_adm',
					   UP_DATE = now(),
					   TOTAL_QTY = '$TOTAL_QTY',
					   TOTAL_SALE_PRICE = '$TOTAL_SALE_PRICE',
					   TOTAL_DISCOUNT_PRICE = '$TOTAL_DISCOUNT_PRICE',
					   TOTAL_SA_DELIVERY_PRICE = '$TOTAL_SA_DELIVERY_PRICE',
					   GRAND_TOTAL_SALE_PRICE = '$GRAND_TOTAL_SALE_PRICE'
				 WHERE GP_NO = '$gp_no' ";

		//echo "수정 견적서 쿼리".$query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateGoodsEstimateGoods($db, $goods_no, $goods_nm, $delivery_cnt_in_box, $retail_price, $estimate_price, $gpg_no, $up_adm, $qty, $supply_price) { 

		$arr_goods = selectGoodsProposal($db, $goods_no);
		if(sizeof($arr_goods) > 0) {
			$COMPONENT		   = $arr_goods[0]["COMPONENT"];
			$DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
			$DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
			$ORIGIN			   = $arr_goods[0]["ORIGIN"];

			$DESCRIPTION_BODY = str_replace("//","\n",$DESCRIPTION_BODY);

			if($DESCRIPTION_TITLE != "" || $DESCRIPTION_BODY != "")
				$DESCRIPTION  = $DESCRIPTION_TITLE."\n\n".$DESCRIPTION_BODY;
		}

		
		if($COMPONENT == "" || $DESCRIPTION == "") {  
			$arr_goods_sub = selectGoodsSub($db, $goods_no);
			
			$SUB_TOTAL_COMPONENT = "";
			$SUB_TOTAL_DESCRIPTION = "";
			if (sizeof($arr_goods_sub) > 0) {

				for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
					$sub_goods_no			= trim($arr_goods_sub[$jk]["GOODS_SUB_NO"]);
					$goods_cnt				= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
					
					$arr_goods = selectGoodsProposal($db, $sub_goods_no);
					if(sizeof($arr_goods) > 0) {
						$SUB_COMPONENT		   = $arr_goods[0]["COMPONENT"];
						$SUB_DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
						$SUB_DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
						$SUB_DESCRIPTION_BODY = str_replace("//","\n",$SUB_DESCRIPTION_BODY);

						if($SUB_COMPONENT <> "")
							$SUB_COMPONENT = $SUB_COMPONENT."(".$goods_cnt."입)";
					} else {
						$SUB_COMPONENT = "";
						$SUB_DESCRIPTION_TITLE = "";
						$SUB_DESCRIPTION_BODY = "";
					}

					if($COMPONENT == "") { 
						$SUB_TOTAL_COMPONENT .= ($SUB_TOTAL_COMPONENT != "" && $SUB_COMPONENT != "" ? ", " : "").$SUB_COMPONENT;
					}

					if($DESCRIPTION == "") { 
						if($SUB_DESCRIPTION_TITLE != "") 
							$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_TITLE."\n\n";
						
						if($SUB_DESCRIPTION_BODY != "")
							$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_BODY."\n\n";
					}

					//$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
					//$sub_goods_cate			= trim($arr_goods_sub[$jk]["GOODS_CATE"]);
						
					//if(!startsWith($sub_goods_cate, '0102')) 
					//	$COMPONENT .=  $sub_goods_name." (".$sub_goods_cnt."입)<br />";
				}
			} 

			if($COMPONENT == "") 
				$COMPONENT = $SUB_TOTAL_COMPONENT;

			if($DESCRIPTION == "") 
				$DESCRIPTION = $SUB_TOTAL_DESCRIPTION;

		}

		$arr_goods = selectGoods($db, $goods_no);
		if (sizeof($arr_goods) > 0) {
			$SIZE			= trim($arr_goods[0]["GOODS_SUB_NAME"]);
			$MANUFACTURER	= trim($arr_goods[0]["CATE_02"]);
		}

		$query="UPDATE TBL_ESTIMATE_SUB 
				   SET GOODS_NO = '$goods_no', 
				       GOODS_NAME = '$goods_nm', 
				       DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box', 
				       RETAIL_PRICE = '$retail_price', 
				       ESTIMATE_PRICE = '$estimate_price',
					   SIZE = '$SIZE',
					   COMPONENT = '$COMPONENT',
					   DESCRIPTION = '$DESCRIPTION',
					   MANUFACTURER = '$MANUFACTURER',
					   ORIGIN       = '$ORIGIN',
					   UP_ADM = '$up_adm',
					   UP_DATE = now(),
					   QTY = '$qty',
					   SUPPLY_PRICE = '$supply_price'
				 WHERE GPG_NO = '$gpg_no' ";

		//echo "수정 견적서 상품 쿼리".$query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteGoodsEstimateGoods($db, $gpg_no, $del_adm) {

		$query="UPDATE TBL_ESTIMATE_SUB 
		           SET DEL_TF = 'Y',
				       DEL_ADM = '$del_adm',
					   DEL_DATE = now()
				 WHERE GPG_NO = '$gpg_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function DeleteGoodsEstimate($db, $gp_no, $del_adm){

		$query="UPDATE TBL_ESTIMATE
					SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
				WHERE GP_NO = '$gp_no' ; ";

		//echo $query;

		if(mysql_query($query,$db)) {
			$query="UPDATE TBL_ESTIMATE_SUB
					   SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
					 WHERE GP_NO = '$gp_no' ; ";

			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
		}
		
	}

	function UpdateGoodsEstimateGoodsStatus($db, $gpg_no, $del_adm){

		$query="UPDATE TBL_ESTIMATE_SUB
					SET CANCEL_TF = 'Y', CANCEL_ADM = '$del_adm', CANCEL_DATE = now()
				WHERE GPG_NO = '$gpg_no' AND CANCEL_TF = 'N' ; ";
		mysql_query($query,$db);
	
	}


	function updateGoodsEstimateSentEmail($db, $gp_no, $request_type, $sent_email, $email_subject, $email_body) {
		
		$query="UPDATE TBL_ESTIMATE
				   SET REQUEST_TYPE = '$request_type',
					   SENT_EMAIL = '$sent_email',
				       IS_SENT = 'Y',
					   SENT_DATE = now(),
					   EMAIL_SUBJECT = '$email_subject',
					   EMAIL_BODY = '$email_body'
				 WHERE GP_NO = '$gp_no' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

?>