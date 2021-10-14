<?
	# =============================================================================
	# File Name    : goods.php
	# =============================================================================

	function listGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, 
						$search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount) {

		if($order_field == "SALE_COUNT") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_COUNT , 0) AS SALE_COUNT
						FROM 
							( ";
		}

		if($order_field == "SALE_AMOUNT") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_AMOUNT , 0) AS SALE_AMOUNT  
						FROM 
							( ";
		}

		if($order_field == "SALE_TOTAL") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_TOTAL , 0) AS SALE_TOTAL 
						FROM 
							( ";
		}

		$total_cnt = totalCntGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

		$and_search_category = $arr_options["and_search_category"];
		$or_search_category = $arr_options["or_search_category"];
		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];
		//$next_sale_price  = $arr_options["next_sale_price"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query_r = "set @rownum = ".$logical_num ."; ";
		mysql_query($query_r,$db);

		$query .= "SELECT @rownum:= @rownum - 1  as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
										 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
										 READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME,
										 DELIVERY_CNT_IN_BOX, STOCK_TF, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, NEXT_SALE_PRICE, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE, MEMO
										 , CONCEAL_PRICE_TF
				    FROM TBL_GOODS 
				   WHERE 1 = 1 ";

		if ($goods_cate <> "") {

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

		if ($and_search_category <> "" || $or_search_category <> "") {
		
			$query .= " AND ( GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE 1 = 1 ";

			if ($and_search_category <> "") {
				foreach (explode(",", $and_search_category) as $splited_goods_cate){

					$query .= " AND GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			if ($or_search_category <> "") {
				foreach (explode(",", $or_search_category) as $splited_goods_cate){

					$query .= " OR GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			$query .= " )) ";
		}

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 <= '".$end_price."' ";
			}

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

		if ($tax_tf <> "") {
			$query .= " AND TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				//$query .= " AND GOODS_CATE not like '".$splited_exclude_category."%' ";

				$query .= " AND (GOODS_CATE NOT like '".$splited_exclude_category."%' 
						 AND GOODS_NO NOT IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$splited_exclude_category."%' )) ";
			}
		}

		/*
		if($next_sale_price == "Y") { 
			$query .= " AND NEXT_SALE_PRICE <> '' ";
		}
		*/

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND GOODS_CODE IN (".$query2.") ";
		
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND GOODS_CODE LIKE '".$search_str."%' ";
						
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}
			//공급사가 제공하는 상품을 포함하는 세트 검색 
			} else if ($search_field == "SUB_CP_CODE"){

				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO 
											   FROM TBL_GOODS_SUB B 
											   JOIN TBL_GOODS GG ON B.GOODS_SUB_NO = GG.GOODS_NO
											   JOIN TBL_COMPANY C ON GG.CATE_03 = C.CP_NO
											  WHERE C.CP_CODE = '".$search_str."'
											)
											   ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		if ($order_field == "") 
			$order_field = " ORDER BY REG_DATE";
		else if($order_field == "VENDOR_PRICE") { 
			if($vendor_calc != "")
				$order_field = " ORDER BY CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10";
			else
				$order_field = " ORDER BY SALE_PRICE";
		} else if($order_field == "MAJIN") { 
			$order_field = " ORDER BY  (SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) ";
		} else if($order_field == "MAJIN_RATE") { 
			$order_field = " ORDER BY  ((SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) / SALE_PRICE * 100) ";
		} else if($order_field == "RANDOM") { 
			$order_field = " ORDER BY rand()";
		} else if($order_field == "SALE_COUNT") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT COUNT( * ) AS SALE_COUNT , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_COUNT

						 ";
		} else if($order_field == "SALE_AMOUNT") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT SUM(QTY) AS SALE_AMOUNT , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_AMOUNT

						 ";
		} else if($order_field == "SALE_TOTAL") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT SUM(SALE_PRICE * QTY) AS SALE_TOTAL , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_TOTAL

						 ";
		} else { 
			$order_field = " ORDER BY ".$order_field;
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= $order_field." ".$order_str.", GOODS_NO ASC limit ".$offset.", ".$nRowCount;

		// echo $query."<br/><br/>";
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


//-----------------Optimized listGoods()------------------------------

	function listGoods1($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, 
						$search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount,$total_cnt) {

		if($order_field == "SALE_COUNT") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_COUNT , 0) AS SALE_COUNT
						FROM 
							( ";
		}

		if($order_field == "SALE_AMOUNT") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_AMOUNT , 0) AS SALE_AMOUNT  
						FROM 
							( ";
		}

		if($order_field == "SALE_TOTAL") { 
			$query = "SELECT G1.*, IFNULL(T1.SALE_TOTAL , 0) AS SALE_TOTAL 
						FROM 
							( ";
		}

		$and_search_category = $arr_options["and_search_category"];
		$or_search_category = $arr_options["or_search_category"];
		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];
		//$next_sale_price  = $arr_options["next_sale_price"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query_r = "set @rownum = ".$logical_num ."; ";
		mysql_query($query_r,$db);

		$query .= "SELECT @rownum:= @rownum - 1  as rn, TBL_GOODS.GOODS_NO, TBL_GOODS.GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
										 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
										 READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, TBL_GOODS.REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME,
										 DELIVERY_CNT_IN_BOX, STOCK_TF, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, NEXT_SALE_PRICE, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE, MEMO, ACCESS_DATE";
					if($order_field=="CATALOG" && startsWith($goods_cate,"20")){
						$query.=", PAGE, SEQ ";
					}
					
					$query.=" FROM TBL_GOODS ";
					if($order_field=="CATALOG"){
						$query.="JOIN TBL_GOODS_CATEGORY ON TBL_GOODS.GOODS_NO=TBL_GOODS_CATEGORY.GOODS_NO ";
					}
					
				   $query.=" WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			echo"<script>console.log('1. goodsCate : ".$goods_cate."');</script>";

			if (strpos($goods_cate, ',') !== false) {

				$query .= " AND ( ";
				foreach (explode(",", $goods_cate) as $splited_goods_cate){
					
					$query .= " GOODS_CATE like '".$splited_goods_cate."%' OR";

				}

				$query = rtrim($query, "OR");
				$query .= " ) ";
			} else { 
				echo"<script>console.log('catalog view');</script>";
				if(startsWith($goods_cate,"20") && $order_field == "CATALOG"){
					$query .=" AND TBL_GOODS_CATEGORY.GOODS_CATE LIKE '".$goods_cate."%'";
					$query .=" ORDER BY PAGE, SEQ LIMIT ".$offset.", ".$nRowCount;;

					//echo $query."<br><br>";
					$result = mysql_query($query,$db);
					$record = array();
			
					if ($result <> "") {
						for($i=0;$i < mysql_num_rows($result);$i++) {
							$record[$i] = sql_result_array($result,$i);
						}
					}
					return $record;
					
				}
				else{
					$query .= " AND (GOODS_CATE like '".$goods_cate."%' 
					OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%'))
				 ";
				}
				
						  
			}
		}

		if ($and_search_category <> "" || $or_search_category <> "") {
			echo"<script>console.log('2. and/or_search_category');</script>";
		
			$query .= " AND ( GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE 1 = 1 ";

			if ($and_search_category <> "") {
				foreach (explode(",", $and_search_category) as $splited_goods_cate){

					$query .= " AND GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			if ($or_search_category <> "") {
				foreach (explode(",", $or_search_category) as $splited_goods_cate){

					$query .= " OR GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			$query .= " )) ";
		}

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 <= '".$end_price."' ";
			}

		}

		if ($cate_01 <> "") {
			echo"<script>console.log('cate_01 : ".$cate_01."');</script>";
			$query .= " AND CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			echo"<script>console.log('cate_02 : ".$cate_02."');</script>";
			$query .= " AND CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			echo"<script>console.log('cate_03 : ".$cate_03."');</script>";
			$query .= " AND CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			echo"<script>console.log('cate_04 : ".$cate_04."');</script>";
			$query .= " AND CATE_04 = '".$cate_04."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($exclude_category <> "") {
			echo"<script>console.log('3. exclude_category');</script>";
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				//$query .= " AND GOODS_CATE not like '".$splited_exclude_category."%' ";

				$query .= " AND (GOODS_CATE NOT like '".$splited_exclude_category."%' 
						 AND GOODS_NO NOT IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$splited_exclude_category."%' )) ";
			}
		}

		/*
		if($next_sale_price == "Y") { 
			$query .= " AND NEXT_SALE_PRICE <> '' ";
		}
		*/

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND GOODS_CODE IN (".$query2.") ";
		
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND GOODS_CODE LIKE '".$search_str."%' ";
						
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}
			//공급사가 제공하는 상품을 포함하는 세트 검색 
			} else if ($search_field == "SUB_CP_CODE"){

				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO 
											   FROM TBL_GOODS_SUB B 
											   JOIN TBL_GOODS GG ON B.GOODS_SUB_NO = GG.GOODS_NO
											   JOIN TBL_COMPANY C ON GG.CATE_03 = C.CP_NO
											  WHERE C.CP_CODE = '".$search_str."'
											)
											   ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		if ($order_field == "") 
			$order_field = " ORDER BY REG_DATE";
		else if($order_field == "VENDOR_PRICE") { 
			if($vendor_calc != "")
				$order_field = " ORDER BY CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10";
			else
				$order_field = " ORDER BY SALE_PRICE";
		} else if($order_field == "MAJIN") { 
			$order_field = " ORDER BY  (SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) ";
		} else if($order_field == "MAJIN_RATE") { 
			$order_field = " ORDER BY  ((SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) / SALE_PRICE * 100) ";
		} else if($order_field == "RANDOM") { 
			$order_field = " ORDER BY rand()";
		} else if($order_field == "SALE_COUNT") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT COUNT( * ) AS SALE_COUNT , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_COUNT

						 ";
		} else if($order_field == "SALE_AMOUNT") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT SUM(QTY) AS SALE_AMOUNT , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_AMOUNT

						 ";
		} else if($order_field == "SALE_TOTAL") { 

			$order_field = "
						) G1
						LEFT JOIN 
						(
							SELECT SUM(SALE_PRICE * QTY) AS SALE_TOTAL , GOODS_NO
							FROM TBL_ORDER_GOODS OG
							WHERE OG.USE_TF =  'Y'
							AND OG.DEL_TF =  'N'
							AND OG.ORDER_STATE IN ( 1, 2, 3 )
							AND OG.CATE_01 = ''
						";

			if ($start_date <> "") {
				$order_field .= " AND OG.REG_DATE >= '".$start_date."' ";
			}

			if ($end_date <> "") {
				$order_field .= " AND OG.REG_DATE <= '".$end_date." 23:59:59' ";
			}

			$order_field .=   "	GROUP BY GOODS_NO
							) T1 ON G1.GOODS_NO = T1.GOODS_NO
							ORDER BY T1.SALE_TOTAL

						 ";
		} else { 
			$order_field = " ORDER BY ".$order_field;
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= $order_field." ".$order_str.", GOODS_NO ASC limit ".$offset.", ".$nRowCount;

		// echo $query."<br/><br/>";
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

	function catalogList($db, $goodsCate, $startDate, $endDate, $startPrice, $endPrice, $cate01, $cate02, $cate03, $cate04, $taxTF, $useTF, $del_tf,
						$searchField, $searchStr, $arrOpts, $orderField, $nPage, $nRowCount, $totalCnt)
	{
		//여기에서 $orderField="CATALOG", $goodsCate

	}


	

	function totalCntGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str){

		$and_search_category = $arr_options["and_search_category"];
		$or_search_category = $arr_options["or_search_category"];
		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];
		//$next_sale_price  = $arr_options["next_sale_price"];

		$query ="SELECT COUNT(*) CNT FROM TBL_GOODS WHERE 1 = 1 ";

		if ($goods_cate <> "") {

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

		if ($and_search_category <> "" || $or_search_category <> "") {
		
			$query .= " AND ( GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE 1 = 1 ";

			if ($and_search_category <> "") {
				foreach (explode(",", $and_search_category) as $splited_goods_cate){

					$query .= " AND GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			if ($or_search_category <> "") {
				foreach (explode(",", $or_search_category) as $splited_goods_cate){

					$query .= " OR GOODS_CATE LIKE '".$splited_goods_cate."%' ";
				}
			}

			$query .= " )) ";
		}

		if ($start_date <> "") {
			$query .= " AND REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10 <= '".$end_price."' ";
			}

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

		if ($tax_tf <> "") {
			$query .= " AND TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				//$query .= " AND GOODS_CATE not like '".$splited_exclude_category."%' ";

				$query .= " AND (GOODS_CATE NOT like '".$splited_exclude_category."%' 
						 AND GOODS_NO NOT IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$splited_exclude_category."%' )) ";
			}
		}

		/*
		if($next_sale_price == "Y") { 
			$query .= " AND NEXT_SALE_PRICE <> '' ";
		}
		*/

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_NO = ".$search_str." OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%' OR GOODS_CODE LIKE '%".$search_str."%' OR MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND GOODS_CODE IN (".$query2.") ";
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND GOODS_CODE LIKE '".$search_str."%' ";
			
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}
			//공급사가 제공하는 상품을 포함하는 세트 검색 
			} else if ($search_field == "SUB_CP_CODE"){

				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO 
											   FROM TBL_GOODS_SUB B 
											   JOIN TBL_GOODS GG ON B.GOODS_SUB_NO = GG.GOODS_NO
											   JOIN TBL_COMPANY C ON GG.CATE_03 = C.CP_NO
											  WHERE C.CP_CODE = '".$search_str."'
											)
											   ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function listGoodsWithPage($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount) {

		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];

		$query = "SELECT   
						G.GOODS_NO, G.GOODS_CATE, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
						G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, 
						G.PRICE, G.BUY_PRICE, G.SALE_PRICE, G.EXTRA_PRICE, G.STOCK_CNT, G.TAX_TF, 
						G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
						G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, 
						G.CONTENTS, G.READ_CNT, G.DISP_SEQ, 
						G.USE_TF, G.DEL_TF, G.REG_ADM, G.REG_DATE, G.UP_ADM, G.UP_DATE, G.DEL_ADM, G.DEL_DATE,
						G.DELIVERY_CNT_IN_BOX, G.MSTOCK_CNT, G.TSTOCK_CNT, G.FSTOCK_CNT, G.BSTOCK_CNT, G.STICKER_PRICE, G.PRINT_PRICE, G.DELIVERY_PRICE, G.SALE_SUSU, G.LABOR_PRICE, G.OTHER_PRICE, G.DELIVERY_PRICE, G.USE_TF, G.WRAP_WIDTH, G.WRAP_LENGTH, G.WRAP_MEMO, G.RESTOCK_DATE,
						C.CP_NM AS CP_NAME,
						GC.GOODS_CATE AS SEARCH_CATE, GC.PAGE, GC.SEQ
										 
				    FROM TBL_GOODS G 
					JOIN TBL_GOODS_CATEGORY GC ON G.GOODS_NO = GC.GOODS_NO
					JOIN TBL_COMPANY C ON G.CATE_03 = C.CP_NO
				   WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			$query .= " AND GC.GOODS_CATE LIKE '".$goods_cate."%' ";
		}

		if ($start_date <> "") {
			$query .= " AND G.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND G.SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND G.SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 <= '".$end_price."' ";
			}

		}

		if ($cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND G.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND G.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND G.CATE_04 = '".$cate_04."' ";
		}

		/*
		if ($cate_04 <> "") {

			$query .= " AND (";
			$query2 = '';
			foreach (explode(",", $cate_04) as $splited_cate_04){
				$query2 .= " G.CATE_04 = '".$splited_cate_04."' OR ";
			}
			$query2 = rtrim($query2, ' OR ');
			$query .= $query2.") ";
		}
		*/

		if ($tax_tf <> "") {
			$query .= " AND G.TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND G.GOODS_CATE not like '".$splited_exclude_category."%' ";
			}
		}


		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_NO = ".$search_str." OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND C.CP_CODE LIKE '%".$search_str."%' ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (G.GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND G.GOODS_NO = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND G.GOODS_CODE = '".$search_str."' ";
		
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND G.GOODS_CODE LIKE '".$search_str."%' ";
						
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$query .= " ORDER BY PAGE, SEQ limit 0, ".$nRowCount;

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

	function insertGoods($db, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_tf, $stock_cnt, $fstock_cnt, $bstock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_GOODS ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_goods_no  = ($rows[0] + 1);
		$goods_sub_name1=SetStringToDB($goods_sub_name);
		

		$query="INSERT INTO TBL_GOODS (
										GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
										CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
										PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, 
										STOCK_TF, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, 
										TAX_TF, IMG_URL, 
										FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, 
										CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, 
										LABOR_PRICE, OTHER_PRICE,
										READ_CNT, DISP_SEQ, USE_TF, REG_ADM, REG_DATE) 
						values ('$new_goods_no', '$goods_cate', '$goods_code', '$goods_name', '$goods_sub_name1', 
										'$cate_01', '$cate_02', '$cate_03', '$cate_04', '$restock_date',
										'$price', '$buy_price', '$sale_price', " ; 

		if($next_sale_price == "") 
			$query .= "					null,";
		else
			$query .= "					'$next_sale_price',";
		
										
		$query .= "						'$extra_price', 
										'$stock_tf', '$stock_cnt', '$fstock_cnt', '$bstock_cnt', '$mstock_cnt', 
										'$tax_tf', '$img_url', 
										'$file_nm_100', '$file_rnm_100', '$file_path_100', '$file_size_100', '$file_ext_100',
									  '$file_nm_150', '$file_rnm_150', '$file_path_150', '$file_size_150', '$file_ext_150',
										'$contents', '$memo', '$delivery_cnt_in_box', '$sticker_price', '$print_price', '$delivery_price', '$sale_susu', 
										'$labor_price', '$other_price',
										'0', '0', '$use_tf', '$reg_adm', now()); ";
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return $new_goods_no;
		}
	}

	function updateGoodsSub($db, $good_no,  $goods_sub_no_array, $goods_cnt_array){

		$query ="DELETE FROM TBL_GOODS_SUB WHERE goods_no = '$good_no' ";
		$result = mysql_query($query,$db);

		$arrlength = count($goods_sub_no_array);

		for($x = 0; $x < $arrlength; $x++) {
			$goods_sub_no = $goods_sub_no_array[$x];
			$goods_cnt = $goods_cnt_array[$x];

			$query="INSERT INTO TBL_GOODS_SUB (GOODS_NO, GOODS_SUB_NO, GOODS_CNT) 
															 VALUES ('$good_no', '$goods_sub_no', '$goods_cnt'); ";
			mysql_query($query,$db);
		}
		
	}

	function updateGoodsCate($db, $good_sub_no,  $goods_cate){

		if($goods_cate == "단종") { 
			$query ="UPDATE TBL_GOODS G JOIN TBL_GOODS_SUB GS ON G.GOODS_NO = GS.GOODS_NO
						SET G.GOODS_CATE = '$goods_cate'
					 WHERE GS.GOODS_SUB_NO = '$good_sub_no' ";
		
			mysql_query($query,$db);

		} else {
/*
			$query ="UPDATE TBL_GOODS G 
						JOIN TBL_GOODS_SUB GS ON G.GOODS_NO = GS.GOODS_NO
						JOIN TBL_GOODS GG ON GG.GOODS_NO = GS.GOODS_SUB_NO	
						SET G.GOODS_CATE = '$goods_cate'
					 WHERE GG.GOODS_NO = '$good_sub_no' AND GG.GOODS_CATE ";
			mysql_query($query,$db);
*/

		}
		
	}

	function updateTempGoodsSub($db, $file_nm, $good_no,  $goods_sub_no_array, $goods_cnt_array){

		$query ="DELETE FROM TBL_TEMP_GOODS_SUB WHERE GOODS_NO = '$good_no' AND TEMP_NO = '$file_nm' ";
		$result = mysql_query($query,$db);

		$arrlength = count($goods_sub_no_array);

		for($x = 0; $x < $arrlength; $x++) {
			$goods_sub_no = $goods_sub_no_array[$x];
			$goods_cnt = $goods_cnt_array[$x];

			$query="INSERT INTO TBL_TEMP_GOODS_SUB (TEMP_NO, GOODS_NO, GOODS_SUB_NO, GOODS_CNT) 
															 VALUES ('$file_nm', '$good_no', '$goods_sub_no', '$goods_cnt'); ";
			mysql_query($query,$db);
		}
		
	}
	
	function selectGoods($db, $goods_no) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
								PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, TSTOCK_CNT, 
								TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
								FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
								DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE,
								READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, STOCK_TF,REASON, EXPOSURE_TF
								, OPTION_CF, OPTION_ADM, OPTION_DATE, CONCEAL_PRICE_TF
					FROM TBL_GOODS
				   WHERE GOODS_NO = '$goods_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function selectGoodsPriceOnly($db, $goods_no) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
								PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE,  
								DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE
								FROM TBL_GOODS
							 WHERE GOODS_NO = '$goods_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function selectGoodsSub($db, $goods_no) {

		$query = "SELECT G.GOODS_NAME, S.GOODS_SUB_NO, S.GOODS_CNT, G.GOODS_CODE, G.PRICE, G.BUY_PRICE, G.GOODS_CATE, G.FSTOCK_CNT, G.STOCK_CNT, G.TSTOCK_CNT, G.DELIVERY_CNT_IN_BOX
					FROM TBL_GOODS_SUB S
					JOIN TBL_GOODS G
					WHERE S.GOODS_SUB_NO = G.GOODS_NO
					  AND S.GOODS_NO = '".$goods_no."' 
				 ORDER BY G.GOODS_CODE ASC	
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

	function selectGoodsSubDetail($db, $goods_no) {

		$query = "SELECT G.GOODS_NAME, S.GOODS_SUB_NO, S.GOODS_CNT, G.GOODS_CODE, G.PRICE, G.CATE_03, G.CATE_04, C.CP_NM, G.BUY_PRICE, G.GOODS_CATE, G.FSTOCK_CNT, G.STOCK_CNT, G.TSTOCK_CNT, G.DELIVERY_CNT_IN_BOX
					FROM TBL_GOODS_SUB S
					JOIN TBL_GOODS G ON S.GOODS_SUB_NO = G.GOODS_NO
					JOIN TBL_COMPANY C ON G.CATE_03 = C.CP_NO
					WHERE S.GOODS_NO = '".$goods_no."' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function selectTempGoodsSub($db, $file_nm, $goods_no) {

		$query = "SELECT G.GOODS_NAME, G.GOODS_CATE, S.GOODS_SUB_NO, S.GOODS_CNT, S.GOODS_SUB_CODE, G.PRICE, G.BUY_PRICE
					FROM TBL_TEMP_GOODS_SUB S
					LEFT JOIN TBL_GOODS G ON S.GOODS_SUB_NO = G.GOODS_NO
					WHERE S.GOODS_NO = '$goods_no'
					AND S.TEMP_NO = '$file_nm'
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

	function updateGoods($db, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_tf, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $up_adm, $goods_no, $exposure_tf, $conceal_price_tf) {

		$goods_sub_name1=SetStringToDB($goods_sub_name);
		//상품 정보 저장 시 현재 가격 변동 내역 메모가 지워지는 논리오류가 발생하여 가격 변동사항이 있을 때만 메모를 초기화 하도록 수정함
		$i_flag = "0";

		$query ="SELECT BUY_PRICE, SALE_PRICE, PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX,  LABOR_PRICE, OTHER_PRICE, SALE_SUSU, 0 AS CP_SALE_SUSU, 0 AS CP_SALE_PRICE, REASON        
			           	FROM TBL_GOODS 
			          	WHERE GOODS_NO = '$goods_no'";
						  
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$this_buy_price				= $rows[0];
		$this_sale_price			= $rows[1];	
		$this_price					= $rows[2];	
		$this_sticker_price			= $rows[3];	
		$this_print_price			= $rows[4];	
		$this_delivery_price		= $rows[5];	
		$this_delivery_cnt_in_box	= $rows[6];
		$this_labor_price			= $rows[7];
		$this_other_price			= $rows[8];
		$this_sale_susu				= $rows[9];	
		$this_cp_sale_susu			= $rows[10];	
		$this_cp_sale_price			= $rows[11];	
		$this_reason					= $rows[12];

		/////////////////////////////////////////////////// 체크 //////////////////////////////////////////////////////////
			if ((($this_buy_price + $this_sale_price + $this_price + $this_sticker_price + $this_print_price + $this_delivery_price  + $this_delivery_cnt_in_box + $this_labor_price + $this_other_price + $this_sale_susu) 
				- ($buy_price + $sale_price + $price + $sticker_price + $print_price + $delivery_price + $delivery_cnt_in_box + $labor_price + $other_price + $sale_susu)) <> 0) {
				$i_flag = "1";
			}
		
		if ($i_flag == "1") {
			//기존코드
			$query="UPDATE TBL_GOODS SET 
						GOODS_CATE				= '$goods_cate',
						GOODS_CODE				= '$goods_code',
						GOODS_NAME				= '$goods_name',
						GOODS_SUB_NAME			= '$goods_sub_name1',
						CATE_01					= '$cate_01',
						CATE_02					= '$cate_02',
						CATE_03					= '$cate_03',
						CATE_04					= '$cate_04',
						RESTOCK_DATE            = '$restock_date',
						PRICE					= '$price',
						BUY_PRICE				= '$buy_price',
						SALE_PRICE				= '$sale_price',
						REASON 					  = '' , ";
			if($next_sale_price == "") 
			$query .= "					NEXT_SALE_PRICE			= null,";
			else
			$query .= "					NEXT_SALE_PRICE			= '$next_sale_price',";

			$query .= "
						EXTRA_PRICE				= '$extra_price',
						STOCK_TF				= '$stock_tf',
						STOCK_CNT				= '$stock_cnt',
						MSTOCK_CNT				= '$mstock_cnt',
						TAX_TF					= '$tax_tf',
						IMG_URL					= '$img_url',
						FILE_NM_100				= '$file_nm_100',
						FILE_RNM_100			= '$file_rnm_100',
						FILE_PATH_100			= '$file_path_100',
						FILE_SIZE_100			= '$file_size_100',
						FILE_EXT_100			= '$file_ext_100',
						FILE_NM_150				= '$file_nm_150',
						FILE_RNM_150			= '$file_rnm_150',
						FILE_PATH_150			= '$file_path_150',
						FILE_SIZE_150			= '$file_size_150',
						FILE_EXT_150			= '$file_ext_150',
						CONTENTS				= '$contents',
						MEMO					= '$memo',
						DELIVERY_CNT_IN_BOX		= '$delivery_cnt_in_box', 
						STICKER_PRICE			= '$sticker_price', 
						PRINT_PRICE				= '$print_price', 
						DELIVERY_PRICE			= '$delivery_price', 
						SALE_SUSU				= '$sale_susu', 
						LABOR_PRICE				= '$labor_price', 
						OTHER_PRICE				= '$other_price', 
						USE_TF					= '$use_tf',
						UP_ADM					=	'$up_adm',
						UP_DATE					=	now(),
						EXPOSURE_TF				=	'$exposure_tf',
						CONCEAL_PRICE_TF		=	'$conceal_price_tf'

				WHERE GOODS_NO = '$goods_no' ";
		} else {
			//reason을 유지하는 코드
			//UP_DATE와 UP_ADM도 유지한다.
			//echo"<script>alert('가격 변동사항 없음');</script>";
			$query="UPDATE TBL_GOODS SET 
						GOODS_CATE				= '$goods_cate',
						GOODS_CODE				= '$goods_code',
						GOODS_NAME				= '$goods_name',
						GOODS_SUB_NAME			= '$goods_sub_name',
						CATE_01					= '$cate_01',
						CATE_02					= '$cate_02',
						CATE_03					= '$cate_03',
						CATE_04					= '$cate_04',
						RESTOCK_DATE            = '$restock_date',
						PRICE					= '$price',
						BUY_PRICE				= '$buy_price',
						SALE_PRICE				= '$sale_price',
						REASON 					  = '$this_reason' , ";
			if($next_sale_price == "") 
			$query .= "					NEXT_SALE_PRICE			= null,";
			else
			$query .= "					NEXT_SALE_PRICE			= '$next_sale_price',";

			$query .= "
						EXTRA_PRICE				= '$extra_price',
						STOCK_TF				= '$stock_tf',
						STOCK_CNT				= '$stock_cnt',
						MSTOCK_CNT				= '$mstock_cnt',
						TAX_TF					= '$tax_tf',
						IMG_URL					= '$img_url',
						FILE_NM_100				= '$file_nm_100',
						FILE_RNM_100			= '$file_rnm_100',
						FILE_PATH_100			= '$file_path_100',
						FILE_SIZE_100			= '$file_size_100',
						FILE_EXT_100			= '$file_ext_100',
						FILE_NM_150				= '$file_nm_150',
						FILE_RNM_150			= '$file_rnm_150',
						FILE_PATH_150			= '$file_path_150',
						FILE_SIZE_150			= '$file_size_150',
						FILE_EXT_150			= '$file_ext_150',
						CONTENTS				= '$contents',
						MEMO					= '$memo',
						DELIVERY_CNT_IN_BOX		= '$delivery_cnt_in_box', 
						STICKER_PRICE			= '$sticker_price', 
						PRINT_PRICE				= '$print_price', 
						DELIVERY_PRICE			= '$delivery_price', 
						SALE_SUSU				= '$sale_susu', 
						LABOR_PRICE				= '$labor_price', 
						OTHER_PRICE				= '$other_price', 
						USE_TF					= '$use_tf',
						EXPOSURE_TF				=	'$exposure_tf',
						CONCEAL_PRICE_TF		=	'$conceal_price_tf'
				WHERE GOODS_NO = '$goods_no' ";
		}
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
		} else {
			return true;
		}
	}

	//일괄변경 - 상품리스트
	function updateGoodsBatch($db, $column, $value_to_change, $up_adm, $goods_no) {

		$query="UPDATE TBL_GOODS SET " ; 

		if($column <> '' && $value_to_change <> '')
			$query .= "           ".$column."					= '".$value_to_change."', ";

		$query .= "                 UP_ADM						=	'$up_adm',
									UP_DATE						=	now()
							 WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateGoodsUseTF($db, $use_tf, $up_adm, $goods_no) {
		
		$query="UPDATE TBL_GOODS SET 
							USE_TF					= '$use_tf',
							UP_ADM					= '$up_adm',
							UP_DATE					= now()
				 WHERE GOODS_NO				= '$goods_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	//판매중,단종 변경시 세트상품까지 검사하여 상위상품의 단종,판매중 여부 변경
	function updateStateGoods($db, $goods_state, $goods_no, $up_adm) {

		//출시예정에서 변경시 
		if($goods_state != '출시예정') { 
			$query="UPDATE TBL_GOODS
					   SET 
							REG_DATE = now()
					 WHERE GOODS_NO = ".$goods_no." AND CATE_04 = '출시예정'  ";
	
			//echo $query;
			mysql_query($query,$db);
		}
		$query ="SELECT CATE_04 FROM TBL_GOODS WHERE GOODS_NO = ".$goods_no." ; ";
		

		
		$query="UPDATE TBL_GOODS SET 
					CATE_04					= '$goods_state',
					UP_ADM					= '$up_adm',
					UP_DATE					= now()
		 WHERE GOODS_NO				= '$goods_no' ";

		//echo $query;
		$result_main = mysql_query($query,$db);

		if($result_main) { 
			$query="SELECT G.GOODS_NO,  
						   SUM(CASE WHEN GG.CATE_04 =  '단종' THEN 1 ELSE 0 END) AS SUM_DISCONTINUED,
						   SUM(CASE WHEN GG.CATE_04 =  '품절' THEN 1 ELSE 0 END) AS SUM_EXPIRED
						FROM TBL_GOODS G
						JOIN TBL_GOODS_SUB GS ON G.GOODS_NO = GS.GOODS_NO
						JOIN TBL_GOODS GG ON GG.GOODS_NO = GS.GOODS_SUB_NO
						WHERE G.GOODS_NO
						IN (
							SELECT GOODS_NO
							FROM TBL_GOODS_SUB
							WHERE GOODS_SUB_NO = '$goods_no'
						)
						GROUP BY G.GOODS_NO ";

			$result = mysql_query($query,$db);
			$record = array();

			if ($result <> "") {
				for($i=0;$i < mysql_num_rows($result);$i++) {
					$record[$i] = sql_result_array($result,$i);

					$GOODS_NO			  = $record[$i]["GOODS_NO"];
					$SUM_DISCONTINUED	  = $record[$i]["SUM_DISCONTINUED"];
					$SUM_EXPIRED		  = $record[$i]["SUM_EXPIRED"];

					//echo "GOODS_NO : ".$GOODS_NO."/ TOTAL_CNT : ".$TOTAL_CNT."/ SUM_DISCONTINUED : ".$SUM_DISCONTINUED."<br/>";


					$set_goods_state = '판매중';
					if($SUM_EXPIRED > 0)
						$set_goods_state = '품절';
					if($SUM_DISCONTINUED > 0)
						$set_goods_state = '단종';

					//세트 기준 기준 수정
					$query="UPDATE TBL_GOODS
							   SET 
									CATE_04					= '$set_goods_state',
									UP_ADM					= '$up_adm',
									UP_DATE					= now()
							 WHERE GOODS_NO = '$GOODS_NO' AND CATE_04 IN ('판매중', '품절', '단종') ";

					//echo $query;

					mysql_query($query,$db);
				}
			}
		}

		if(!$result_main) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateOrderGoods($db, $goods_seq_no, $goods_no) {

		$query="UPDATE TBL_GOODS SET
						DISP_SEQ			=	'$goods_seq_no'
				 WHERE GOODS_NO				= '$goods_no' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteGoods($db, $goods_no, $del_adm) {
		
		$query= "DELETE FROM TBL_GOODS_PRICE  WHERE GOODS_NO = '$goods_no'";
		mysql_query($query,$db);

		//$query= "DELETE FROM TBL_GOODS_OPTION WHERE GOODS_NO = '$goods_no'";
		//mysql_query($query,$db);

		$query= "DELETE FROM TBL_GOODS_IMAGES WHERE GOODS_NO = '$goods_no'";
		mysql_query($query,$db);

		$query="UPDATE TBL_GOODS SET 
							 DEL_TF				= 'Y',
							 DEL_ADM			= '$del_adm',
							 DEL_DATE			= now()														 
				 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="DELETE FROM TBL_GOODS_SUB WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertGoodsFile($db, $goods_no, $file_nm1, $file_rnm1, $file_path1, $file_size1, $file_ext1, $file_nm2, $file_rnm2, $file_path2, $file_size2, $file_ext2) {
		
		if ($reg_date == "") {
			$query="INSERT INTO TBL_GOODS_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, 
																		FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
													  values ('$goods_no', '$file_nm1', '$file_rnm1', '$file_path1', '$file_size1', '$file_ext1',
																		'$file_nm2', '$file_rnm2', '$file_path2', '$file_size2', '$file_ext2'); ";
		} else {
			$query="INSERT INTO TBL_GOODS_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, 
																		FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
													  values ('$goods_no', '$file_nm1', '$file_rnm1', '$file_path1', '$file_size1', '$file_ext1',
																		'$file_nm2', '$file_rnm2', '$file_path2', '$file_size2', '$file_ext2'); ";
		}
		
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectGoodsFile($db, $goods_no) {

		$query = "SELECT GOODS_IMAGE_NO,GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2
								FROM TBL_GOODS_IMAGES
							 WHERE GOODS_NO = '$goods_no' order by GOODS_IMAGE_NO asc ";
		
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

	function selectGoodsOption($db, $goods_no) {

		$query = "SELECT OPTION_NO,GOODS_NO, OPTION_NAME, OPTION_SUB, OPTION_SEQ, QTY, USE_TF
								FROM TBL_GOODS_OPTION 
							 WHERE GOODS_NO = '$goods_no' order by OPTION_NAME asc, OPTION_NO asc ";
		
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


	function selectGoodsOptionName($db, $goods_no) {

		$query = "SELECT OPTION_NAME
								FROM TBL_GOODS_OPTION 
							 WHERE GOODS_NO = '$goods_no' group by OPTION_NAME order by OPTION_NAME asc ";
		
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

	function selectGoodsOptionValue($db, $goods_no, $option_nm) {

		$query = "SELECT OPTION_SUB
								FROM TBL_GOODS_OPTION 
							 WHERE GOODS_NO = '$goods_no' AND OPTION_NAME = '$option_nm' order by OPTION_NAME asc, OPTION_NO asc ";
		
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		$str_values = "";

		if (sizeof($record) > 0) {
			for ($j = 0 ; $j < sizeof($record); $j++) {

				if ($j == 0) {
					$str_values = trim($record[$j]["OPTION_SUB"]);
				} else {
					$str_values .= ",".trim($record[$j]["OPTION_SUB"]);
				}

			}
		}

		return $str_values;
	}

	function insertGoodsOption($db, $goods_no, $option_name, $option_sub, $option_seq) {
		
		$query="INSERT INTO TBL_GOODS_OPTION (GOODS_NO, OPTION_NAME, OPTION_SUB, OPTION_SEQ) 
													  values ('$goods_no', '$option_name', '$option_sub', '$option_seq'); ";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteGoodsFile($db, $del_adm, $goods_no) {

		$query="DELETE FROM TBL_GOODS_IMAGES WHERE GOODS_NO = '$goods_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function deleteGoodsOption($db, $del_adm, $goods_no) {

		$query="DELETE FROM TBL_GOODS_OPTION WHERE GOODS_NO = '$goods_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertTempGoods($db, $file_nm, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm, $prev_goods_no) {
		
		$isSingleItem = "N";

		if($goods_cate == "" && $cate_01 <> ""){

			$query ="SELECT GOODS_NO FROM TBL_GOODS WHERE GOODS_CODE = '$goods_code' AND DEL_TF = 'N' ";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$goods_sub_no = $rows[0];

			//if($goods_sub_no <> '')
			//{
				$query ="SELECT GOODS_CNT FROM TBL_TEMP_GOODS_SUB WHERE TEMP_NO = '$file_nm' AND GOODS_NO = '$prev_goods_no' AND GOODS_SUB_NO = '$goods_sub_no'";
				$result = mysql_query($query,$db);
				$rows   = mysql_fetch_array($result);
				$goods_cnt = $rows[0];

				if($goods_cnt > 0)  
					$query="UPDATE TBL_TEMP_GOODS_SUB 
							   SET GOODS_CNT = $goods_cnt + 1
							 WHERE TEMP_NO = '$file_nm' AND GOODS_NO = '$prev_goods_no' AND GOODS_SUB_NO = '$goods_sub_no'";
				 else 
					$query="INSERT INTO TBL_TEMP_GOODS_SUB (TEMP_NO, GOODS_NO, GOODS_SUB_NO, GOODS_SUB_CODE, GOODS_CNT)
										 VALUES ('$file_nm', '$prev_goods_no', '$goods_sub_no', '$goods_code', '$cate_01')";
				
			//}

			//echo $query."<br/>";
			$isSingleItem = "Y";
		

		}	else {
			$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_TEMP_GOODS ";
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$new_goods_no  = ($rows[0] + 1);


			$query="INSERT INTO TBL_TEMP_GOODS (TEMP_NO, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
																	CATE_01, CATE_02, CATE_03, CATE_04,
																	PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, 
																	STOCK_CNT, MSTOCK_CNT, 
																	TAX_TF, IMG_URL, 
																	FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																	FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, 
																	CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, 
																	READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, USE_TF, REG_ADM, REG_DATE)
															 values 
															 ('$file_nm', '$new_goods_no', '$goods_cate', '$goods_code', '$goods_name', '$goods_sub_name', 
																'$cate_01', '$cate_02', '$cate_03', '$cate_04', 
																'$price', '$buy_price', '$sale_price', '$extra_price', 
																'$stock_cnt', '$mstock_cnt', 
																'$tax_tf', '$img_url', 
																'$file_nm_100', '$file_rnm_100', '$file_path_100', '$file_size_100', '$file_ext_100',
																'$file_nm_150', '$file_rnm_150', '$file_path_150', '$file_size_150', '$file_ext_150',
																'$contents', '$memo', '$delivery_cnt_in_box', 
																'0', '0', '$sticker_price', '$print_price', '$delivery_price', '$labor_price', '$other_price', '$sale_susu', '$use_tf', '$reg_adm', now());  ";

			//echo $query."<br/>";
			//exit;
		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			if($isSingleItem == 'N')
				return $new_goods_no;
			else
				return $prev_goods_no;
		}
	}

	function listTempGoods($db, $temp_no) {

		$query = "SELECT TEMP_NO, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
										 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, 
																	FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																	FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, 
																	CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, 
																	READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_TEMP_GOODS.CATE_03 ) AS CP_NAME,
										 (SELECT CASE WHEN COUNT(*) >= 1 THEN 'Y' ELSE 'N' END 
											FROM TBL_GOODS 
											WHERE TBL_GOODS.USE_TF='Y' 
											  AND TBL_GOODS.DEL_TF='N' 
											  AND (TBL_TEMP_GOODS.GOODS_CODE <> '' AND TRIM(TBL_GOODS.GOODS_CODE) = TRIM(TBL_TEMP_GOODS.GOODS_CODE))
										 ) AS DUPLICATED_TF
								FROM TBL_TEMP_GOODS WHERE TEMP_NO = '$temp_no' AND CATE_01 = ''";


		$query .= " ORDER BY GOODS_NO asc ";

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

	function selectTempGoods($db, $temp_no, $goods_no) {

		$query = "SELECT TEMP_NO, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, 
																	CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, MSTOCK_CNT, TAX_TF, IMG_URL, 
																	FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																	FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, 
																	CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, 
																	READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, USE_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_TEMP_GOODS
							 WHERE TEMP_NO = '$temp_no' AND GOODS_NO = '$goods_no' ";
		
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

	function deleteTempGoods($db, $temp_no, $goods_no) {

		$query="DELETE FROM TBL_TEMP_GOODS WHERE TEMP_NO = '$temp_no' AND GOODS_NO = '$goods_no'  ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function updateTempGoods($db, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $use_tf, $up_adm, $temp_no, $goods_no) 
		
		{

		$query="UPDATE TBL_TEMP_GOODS 
				SET 
									GOODS_CATE				= '$goods_cate',
									GOODS_CODE				= '$goods_code',
									GOODS_NAME				= '$goods_name',
									GOODS_SUB_NAME		    = '$goods_sub_name',
									CATE_01					= '$cate_01',
									CATE_02					= '$cate_02',
									CATE_03					= '$cate_03',
									CATE_04					= '$cate_04',
									PRICE					= '$price',
									BUY_PRICE				= '$buy_price',
									SALE_PRICE				= '$sale_price',
									EXTRA_PRICE				= '$extra_price',
									STOCK_CNT				= '$stock_cnt',
									MSTOCK_CNT				= '$mstock_cnt',
									TAX_TF					= '$tax_tf',
									IMG_URL					= '$img_url',
									FILE_NM_100				= '$file_nm_100',
									FILE_RNM_100			= '$file_rnm_100',
									FILE_PATH_100			= '$file_path_100',
									FILE_SIZE_100			= '$file_size_100',
									FILE_EXT_100			= '$file_ext_100',
									FILE_NM_150				= '$file_nm_150',
									FILE_RNM_150			= '$file_rnm_150',
									FILE_PATH_150			= '$file_path_150',
									FILE_SIZE_150			= '$file_size_150',
									FILE_EXT_150			= '$file_ext_150',
									CONTENTS				= '$contents',
									MEMO                    = '$memo', 
									DELIVERY_CNT_IN_BOX     = '$delivery_cnt_in_box', 
									STICKER_PRICE           = '$sticker_price', 
									PRINT_PRICE             = '$print_price', 
									DELIVERY_PRICE          = '$delivery_price', 
									SALE_SUSU               = '$sale_susu',
									USE_TF					= '$use_tf',
									UP_ADM					= '$up_adm',
									UP_DATE					= now()
				WHERE GOODS_NO = '$goods_no' ";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function updateTempGoodsBuyPriceForSets($db, $temp_no) {

		$query="
					SELECT B.GOODS_NO, 
							SUM( 
								 CASE WHEN C.GOODS_CATE LIKE '010202%' 
										THEN C.BUY_PRICE * B.GOODS_CNT / G.DELIVERY_CNT_IN_BOX
										ELSE C.BUY_PRICE * B.GOODS_CNT 
							  	 END 
							    ) AS BUY_TOTAL
						FROM TBL_TEMP_GOODS_SUB B 
						JOIN TBL_TEMP_GOODS G ON G.GOODS_NO = B.GOODS_NO
						JOIN TBL_GOODS C ON B.GOODS_SUB_NO = C.GOODS_NO
						WHERE B.TEMP_NO =  '$temp_no'
						GROUP BY B.GOODS_NO " ;
		
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$GOODS_NO = $record[$i]["GOODS_NO"];
				$BUY_TOTAL = $record[$i]["BUY_TOTAL"];

				$query="	UPDATE TBL_TEMP_GOODS A 
					SET A.BUY_PRICE = '$BUY_TOTAL',
					    A.PRICE = $BUY_TOTAL + A.PRINT_PRICE + A.STICKER_PRICE + ROUND(A.DELIVERY_PRICE / A.DELIVERY_CNT_IN_BOX) + A.LABOR_PRICE + A.OTHER_PRICE,
						A.EXTRA_PRICE = A.PRINT_PRICE + A.STICKER_PRICE + ROUND(A.DELIVERY_PRICE / A.DELIVERY_CNT_IN_BOX) + A.LABOR_PRICE + A.OTHER_PRICE 
					WHERE A.TEMP_NO = '$temp_no' AND  A.GOODS_NO = '$GOODS_NO'
				";

				mysql_query($query,$db);
				//echo $query;
				//exit;
			}
		}

	}

	function chkCateNm ($db, $cate_no) {

		$query="SELECT COUNT(*) AS CNT FROM TBL_CATEGORY WHERE CATE_CD = '$cate_no' AND DEL_TF = 'N' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}

	}


	function insertTempToRealGoods($db, $temp_no, $str_goods_no) {
		
		
		$query="SELECT TEMP_NO, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
									 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, MSTOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
									 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
							FROM TBL_TEMP_GOODS
						 WHERE TEMP_NO = '$temp_no' AND GOODS_NO IN ($str_goods_no)";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		
		if (sizeof($record) > 0) {
			for ($j = 0 ; $j < sizeof($record); $j++) {
				
				// 인서트 합니다..
				$GOODS_NO					= trim($record[$j]["GOODS_NO"]);
				$CATE_03					= trim($record[$j]["CATE_03"]);
				/*
				$OPTION01_NAME		= trim($record[$j]["OPTION01_NAME"]);
				$OPTION01_VALUE		= trim($record[$j]["OPTION01_VALUE"]);
				$OPTION02_NAME		= trim($record[$j]["OPTION02_NAME"]);
				$OPTION02_VALUE		= trim($record[$j]["OPTION02_VALUE"]);
				*/
				$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_GOODS ";
				$result = mysql_query($query,$db);
				$rows   = mysql_fetch_array($result);
				$new_goods_no  = ($rows[0] + 1);

				//$query ="SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE = '$CATE_03' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
				//$result = mysql_query($query,$db);
				//$rows   = mysql_fetch_array($result);
				//$CATE_03 = $rows[0];
				

				$query="INSERT INTO TBL_GOODS (GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
																			PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, 
																			TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
																			DELIVERY_CNT_IN_BOX, READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE,  USE_TF, REG_ADM, REG_DATE) 
																SELECT '$new_goods_no', GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, '$CATE_03', 
																		CATE_04, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, 
																		TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																		FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
																		DELIVERY_CNT_IN_BOX, READ_CNT, DISP_SEQ, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, 'Y', REG_ADM, REG_DATE
																FROM TBL_TEMP_GOODS
																WHERE TEMP_NO = '$temp_no' AND GOODS_NO = '$GOODS_NO' ";

				mysql_query($query,$db);

				$query="INSERT INTO TBL_GOODS_SUB (GOODS_NO, GOODS_SUB_NO, GOODS_CNT) 
																SELECT '$new_goods_no', GOODS_SUB_NO, GOODS_CNT
																FROM TBL_TEMP_GOODS_SUB
																WHERE TEMP_NO = '$temp_no' AND GOODS_NO = '$GOODS_NO' ";

				mysql_query($query,$db);


				/*
				if ($OPTION01_NAME <> "") {
					if ($OPTION01_VALUE <> "") {
						$arr_option01_value = explode(",",$OPTION01_VALUE);
						for ($i = 0 ; $i < sizeof($arr_option01_value) ; $i++) {
							$result = insertGoodsOption($db, $new_goods_no, $OPTION01_NAME, $arr_option01_value[$i], $i);
						}
					}
				}

				if ($OPTION02_NAME <> "") {
					if ($OPTION02_VALUE <> "") {
						$arr_option02_value = explode(",",$OPTION02_VALUE);
						for ($i = 0 ; $i < sizeof($arr_option02_value) ; $i++) {
							$result = insertGoodsOption($db, $new_goods_no, $OPTION02_NAME, $arr_option02_value[$i], $i);
						}
					}
				}
				*/

			}
		}
		
		#echo $query;

		if(!$result) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteTempToRealGoods($db, $temp_no, $str_goods_no) {
		

		$query=" DELETE FROM  TBL_TEMP_GOODS WHERE TEMP_NO = '$temp_no' AND GOODS_NO IN ($str_goods_no) ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}



	function listGoodsPrice($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$cp_no						= $filter["cp_no"];
		$diff_buy_price				= $filter["diff_buy_price"];
		$diff_delivery_cnt_in_box	= $filter["diff_delivery_cnt_in_box"];
		$display					= $filter["chk_display"];

		$offset = $nRowCount*($nPage-1);
		
		$query = "set @rownum = ".$offset."; ";
		mysql_query($query,$db);

		$query =   "SELECT
						@rownum:=@rownum + 1 AS rn,
						G.GOODS_NO,
						G.GOODS_CATE,
						G.GOODS_CODE,
						G.GOODS_NAME,
						G.GOODS_SUB_NAME, 
						G.CATE_01,
						G.CATE_02,
						G.CATE_03,
						G.CATE_04, 
						G.STOCK_CNT,
						G.TAX_TF,
						G.IMG_URL,
						G.FILE_NM_100,
						G.FILE_RNM_100,
						G.FILE_PATH_100,
						G.FILE_SIZE_100,
						G.FILE_EXT_100,
						G.FILE_NM_150,
						G.FILE_RNM_150,
						G.FILE_PATH_150,
						G.FILE_SIZE_150,
						G.FILE_EXT_150,
						G.CONTENTS,
						G.MSTOCK_CNT,
						G.TSTOCK_CNT,
						G.FSTOCK_CNT,
						G.BSTOCK_CNT, 
						G.BUY_PRICE AS ORI_BUY_PRICE,
						G.DELIVERY_CNT_IN_BOX AS ORI_DELIVERY_CNT_IN_BOX,
						P.STICKER_PRICE,
						P.PRINT_PRICE,
						P.DELIVERY_PRICE,
						P.SALE_SUSU,
						P.LABOR_PRICE,
						P.OTHER_PRICE,
						P.DELIVERY_PRICE,
						P.DELIVERY_CNT_IN_BOX,
						P.PRICE,
						P.BUY_PRICE,
						P.SEQ_NO,
						P.SALE_PRICE,
						P.REG_DATE,
						ROUND((((P.SALE_PRICE - ROUND((P.SALE_PRICE / 100 * P.SALE_SUSU), 0) - P.PRICE) / P.SALE_PRICE) * 100),2)
						AS MAJIN_PER,
						P.CP_SALE_SUSU,
						P.CP_SALE_PRICE,
						P.DISPLAY,
						C.CP_NM AS CP_NAME
						FROM
							TBL_GOODS G,
							TBL_GOODS_PRICE P,
							TBL_COMPANY C
						WHERE
							G.GOODS_NO = P.GOODS_NO
							AND P.CP_NO = C.CP_NO ";

		if ($goods_cate <> "") {
			$query .= " AND G.GOODS_CATE like '".$goods_cate."%' ";
		}

		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($start_price <> "") {
			$query .= " AND P.SALE_PRICE >= '".$start_price."' ";
		}

		if ($end_price <> "") {
			$query .= " AND P.SALE_PRICE <= '".$end_price."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND G.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND G.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND G.CATE_04 = '".$cate_04."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($cp_no <> "") { 
			$query .= " AND P.CP_NO = '".$cp_no."' ";
		}

		if ($display <> "") { 
			$query .= " AND P.DISPLAY = '".$display."' ";
		}

		if ($diff_buy_price <> "" || $diff_delivery_cnt_in_box <> "" ) { 
			$query .= " AND ( ";


			if ($diff_buy_price <> "") { 
				$query .= " G.BUY_PRICE <> P.BUY_PRICE ";
			}

			if ($diff_buy_price <> "" && $diff_delivery_cnt_in_box <> "" ) { 
				$query .= " OR ";
			}

			if ($diff_delivery_cnt_in_box <> "") { 
				$query .= " G.DELIVERY_CNT_IN_BOX <> P.DELIVERY_CNT_IN_BOX ";
			}

			$query .= " ) ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_NO = ".$search_str." OR G.GOODS_CODE LIKE '%".$search_str."%') ";
				else
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND G.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND G.GOODS_CODE LIKE '%".$search_str."%' ";
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			//공급사가 제공하는 상품을 포함하는 세트 검색 
			} else if ($search_field == "SUB_CP_CODE"){

				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO 
											   FROM TBL_GOODS_SUB B 
											   JOIN TBL_GOODS GG ON B.GOODS_SUB_NO = GG.GOODS_NO
											   JOIN TBL_COMPANY C ON GG.CATE_03 = C.CP_NO
											  WHERE C.CP_CODE = '".$search_str."'
											)
											   ";

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		
		if ($order_field == "") 
			$order_field = "P.REG_DATE";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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


	function totalCntGoodsPrice($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str){

		$cp_no						= $filter["cp_no"];
		$diff_buy_price				= $filter["diff_buy_price"];
		$diff_delivery_cnt_in_box	= $filter["diff_delivery_cnt_in_box"];
		$display					= $filter["chk_display"];

		$query ="SELECT COUNT(*) CNT 
		           FROM TBL_GOODS G
				   JOIN TBL_GOODS_PRICE P ON G.GOODS_NO = P.GOODS_NO 
				   JOIN TBL_COMPANY C ON P.CP_NO = C.CP_NO 
				  WHERE 1 = 1
				   ";

		if ($goods_cate <> "") {
			$query .= " AND G.GOODS_CATE like '".$goods_cate."%' ";
		}

		if ($start_date <> "") {
			$query .= " AND P.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND P.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($start_price <> "") {
			$query .= " AND P.SALE_PRICE >= '".$start_price."' ";
		}

		if ($end_price <> "") {
			$query .= " AND P.SALE_PRICE <= '".$end_price."' ";
		}

		if ($cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND G.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND G.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND G.CATE_04 = '".$cate_04."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND P.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($cp_no <> "") { 
			$query .= " AND P.CP_NO = '".$cp_no."' ";
		}

		if ($display <> "") { 
			$query .= " AND P.DISPLAY = '".$display."' ";
		}

		if ($diff_buy_price <> "" || $diff_delivery_cnt_in_box <> "" ) { 
			$query .= " AND ( ";


			if ($diff_buy_price <> "") { 
				$query .= " G.BUY_PRICE <> P.BUY_PRICE ";
			}

			if ($diff_buy_price <> "" && $diff_delivery_cnt_in_box <> "" ) { 
				$query .= " OR ";
			}

			if ($diff_delivery_cnt_in_box <> "") { 
				$query .= " G.DELIVERY_CNT_IN_BOX <> P.DELIVERY_CNT_IN_BOX ";
			}

			$query .= " ) ";
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_NO = ".$search_str." OR G.GOODS_CODE LIKE '%".$search_str."%') ";
				else
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND G.GOODS_NO = '".$search_str."' ";
			} else if ($search_field == "GOODS_CODE"){
				$query .= " AND G.GOODS_CODE LIKE '%".$search_str."%' ";
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}
			//공급사가 제공하는 상품을 포함하는 세트 검색 
			} else if ($search_field == "SUB_CP_CODE"){

				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO 
											   FROM TBL_GOODS_SUB B 
											   JOIN TBL_GOODS GG ON B.GOODS_SUB_NO = GG.GOODS_NO
											   JOIN TBL_COMPANY C ON GG.CATE_03 = C.CP_NO
											  WHERE C.CP_CODE = '".$search_str."'
											)
											   ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listCompanyPrice($db, $goods_no, $price, $type) {
	
		if ($type == 'NO') {
			$query = "SELECT CP_NO, CP_NM, CP_NM2, CP_CODE
									FROM TBL_COMPANY 
								 WHERE CP_TYPE IN ('판매','판매공급')
									 AND USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND CP_NO NOT IN (SELECT CP_NO FROM TBL_GOODS_PRICE WHERE GOODS_NO = '$goods_no' AND SALE_PRICE = '$price' AND USE_TF = 'Y') 
								 ORDER BY CP_NM ASC ";
		} else {
			$query = "SELECT CP_NO, CP_NM, CP_NM2, CP_CODE
									FROM TBL_COMPANY 
								 WHERE CP_TYPE IN ('판매','판매공급')
									 AND USE_TF = 'Y'
									 AND DEL_TF = 'N'
									 AND CP_NO IN (SELECT CP_NO FROM TBL_GOODS_PRICE WHERE GOODS_NO = '$goods_no' AND SALE_PRICE = '$price' AND USE_TF = 'Y') 
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


	/*

	// 구버전 - 업체별 판매가만 관리
	function insertGoodsPrice($db, $goods_no, $cp_no, $new_price, $memo, $reg_adm) {
		
		$query="SELECT COUNT(*) AS CNT FROM TBL_GOODS_PRICE WHERE USE_TF = 'Y' AND GOODS_NO = '$goods_no' AND CP_NO = '$cp_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {

			$query="INSERT INTO TBL_GOODS_PRICE (GOODS_NO, CP_NO, SALE_PRICE, MEMO, USE_TF, REG_ADM, REG_DATE) 
														  values ('$goods_no', '$cp_no', '$new_price', '$memo', 'Y', '$reg_adm', now()); ";
		
		} else {

			$query="UPDATE TBL_GOODS_PRICE 
					   SET SALE_PRICE = '$new_price',
						   MEMO				= '$memo',
						   UP_ADM			= '$reg_adm',
						   UP_DATE		= now()
					 WHERE USE_TF = 'Y' AND GOODS_NO = '$goods_no' AND CP_NO = '$cp_no' ";

		}
			//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	*/

	function deleteGoodsPrice($db, $goods, $cp_no) {

		$query="DELETE FROM TBL_GOODS_PRICE WHERE GOODS_NO = '$goods_no' AND CP_NO = '$cp_no' ";
		
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function deleteGoodsPriceAsSeqNo($db, $seq_no) {

		$query="DELETE FROM TBL_GOODS_PRICE WHERE SEQ_NO = '$seq_no' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function syncGoodsPriceAsSeqNo($db, $chk_goods_option, $seq_no, $up_adm) {
		
		$has_buy_price = in_array("buy_price", $chk_goods_option);
		$has_delivery_cnt_in_box = in_array("delivery_cnt_in_box", $chk_goods_option);

		$query = " UPDATE TBL_GOODS G 
		             JOIN TBL_GOODS_PRICE P ON G.GOODS_NO = P.GOODS_NO 
				      SET ";

		if($has_buy_price)
			$query.=" P.BUY_PRICE = G.BUY_PRICE, ";

		if($has_delivery_cnt_in_box)
			$query.=" P.DELIVERY_CNT_IN_BOX = G.DELIVERY_CNT_IN_BOX, ";

		$query.=" P.PRICE = P.STICKER_PRICE + P.PRINT_PRICE  + P.LABOR_PRICE + P.OTHER_PRICE ";

		if($has_buy_price)
			$query.=" + G.BUY_PRICE ";
		else
			$query.=" + P.BUY_PRICE ";

		if($has_delivery_cnt_in_box)
			$query.=" + ROUND(P.DELIVERY_PRICE / G.DELIVERY_CNT_IN_BOX) ";
		else
			$query.=" + ROUND(P.DELIVERY_PRICE / P.DELIVERY_CNT_IN_BOX) ";

		$query.="	                   ,
					 P.UP_ADM = '$up_adm',
					 P.UP_DATE = now()
					WHERE SEQ_NO = '$seq_no' AND (";

		if($has_buy_price)
			$query .= " P.BUY_PRICE <> G.BUY_PRICE ";

		if($has_buy_price && $has_delivery_cnt_in_box)
			$query .= " OR ";
		
		if($has_delivery_cnt_in_box)
			$query .= " P.DELIVERY_CNT_IN_BOX <> G.DELIVERY_CNT_IN_BOX ";
		
		$query .= ")";
		
		//echo $query."<br/>";
		//exit;

		//이전 기록 추가
		insertGoodsPriceChange($db, $seq_no, $up_adm);

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function insertGoodsPriceChange($db, $seq_no, $reg_adm) { 
		/********* 20210806 쿼리 수정 *******************************/
		/*$query = "
					INSERT INTO TBL_GOODS_PRICE_CHANGE (GOODS_NO, CP_NO, PRICE, BUY_PRICE, SALE_PRICE, DELIVERY_CNT_IN_BOX,                                                  STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE,  LABOR_PRICE, OTHER_PRICE, 
					                                    SALE_SUSU, CP_SALE_SUSU, CP_SALE_PRICE, REG_ADM, REG_DATE)
					SELECT GOODS_NO, CP_NO, PRICE, BUY_PRICE, SALE_PRICE, DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, 
							 DELIVERY_PRICE,  LABOR_PRICE, OTHER_PRICE, SALE_SUSU, CP_SALE_SUSU, CP_SALE_PRICE, '".$reg_adm."', now() 
						FROM TBL_GOODS_PRICE
					   WHERE SEQ_NO = '".$seq_no."' 
					";*/
		$query = "
					INSERT INTO TBL_GOODS_PRICE_CHANGE (GOODS_NO, CP_NO, PRICE, BUY_PRICE, SALE_PRICE, DELIVERY_CNT_IN_BOX,                                                  STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE,  LABOR_PRICE, OTHER_PRICE, 
					                                    SALE_SUSU, CP_SALE_SUSU, CP_SALE_PRICE, REG_ADM, REG_DATE
														, AFTER_PRICE, AFTER_BUY_PRICE, AFTER_SALE_PRICE)
					SELECT A.GOODS_NO, A.CP_NO, A.PRICE, A.BUY_PRICE, A.SALE_PRICE, A.DELIVERY_CNT_IN_BOX, A.STICKER_PRICE, A.PRINT_PRICE, 
						A.DELIVERY_PRICE,  A.LABOR_PRICE, A.OTHER_PRICE, A.SALE_SUSU, A.CP_SALE_SUSU, A.CP_SALE_PRICE, '".$reg_adm."', now() 
						, B.PRICE, B.BUY_PRICE, B.SALE_PRICE
						FROM TBL_GOODS_PRICE A, TBL_GOODS B
					   WHERE A.SEQ_NO = '".$seq_no."' 
					     AND A.GOODS_NO = B.GOODS_NO
					";
	
		//echo $query."<br/>";
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}




	function insertGoodsPriceUpdate($db, $kind, $goods_no, $cp_no, $price, $buy_price, $sale_price, $sticker_price, $print_price, $delivery_price, $delivery_cnt_in_box, $labor_price, $other_price, $sale_susu, $cp_sale_susu, $cp_sale_price, $reg_adm, $display) {
		
		$i_flag = "0";


		if ($kind == "TBL_GOODS") {
			$query ="SELECT BUY_PRICE, SALE_PRICE, PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX, 
			                LABOR_PRICE, OTHER_PRICE, SALE_SUSU, 0 AS CP_SALE_SUSU, 0 AS CP_SALE_PRICE, REASON        
			           FROM TBL_GOODS 
			          WHERE GOODS_NO = '$goods_no'";
		} else {
			$query ="SELECT B.BUY_PRICE, B.SALE_PRICE, B.PRICE, B.STICKER_PRICE, B.PRINT_PRICE, B.DELIVERY_PRICE, B.DELIVERY_CNT_IN_BOX,
			                B.LABOR_PRICE, B.OTHER_PRICE, B.SALE_SUSU, B.CP_SALE_SUSU, B.CP_SALE_PRICE
				   	   FROM TBL_GOODS A 
					   JOIN TBL_GOODS_PRICE B ON A.GOODS_NO = B.GOODS_NO
					  WHERE B.CP_NO = '$cp_no'
						AND B.GOODS_NO = '$goods_no'
				   ORDER BY B.REG_DATE DESC limit 1";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$this_buy_price				= $rows[0];
		$this_sale_price			= $rows[1];	
		$this_price					= $rows[2];	
		$this_sticker_price			= $rows[3];	
		$this_print_price			= $rows[4];	
		$this_delivery_price		= $rows[5];	
		$this_delivery_cnt_in_box	= $rows[6];
		$this_labor_price			= $rows[7];
		$this_other_price			= $rows[8];
		$this_sale_susu				= $rows[9];	
		$this_cp_sale_susu			= $rows[10];	
		$this_cp_sale_price			= $rows[11];	
		$this_reason					= $rows[12];	

		/////////////////////////////////////////////////// 체크 //////////////////////////////////////////////////////////
		if ($kind == "TBL_GOODS") {
			if ((($this_buy_price + $this_sale_price + $this_price + $this_sticker_price + $this_print_price + $this_delivery_price  + $this_delivery_cnt_in_box + $this_labor_price + $this_other_price + $this_sale_susu) 
				- ($buy_price + $sale_price + $price + $sticker_price + $print_price + $delivery_price + $delivery_cnt_in_box + $labor_price + $other_price + $sale_susu)) <> 0) {
				$i_flag = "1";
			}
		} else {
			//echo $this_sale_price." // ".$sale_price." : ".$this_cp_sale_susu." // ".$cp_sale_susu." : ".$this_cp_sale_price." // ".$cp_sale_price."<br/>";
			if (($this_sale_price - $sale_price) <> 0 || ($this_cp_sale_susu - $cp_sale_susu) <> 0 || ($this_cp_sale_price - $cp_sale_price) <> 0) {
				$i_flag = "1";
			}
		}

		if ($i_flag == "1") {
			/********* 20210806 수정 AFTER_BUY_PRICE, AFTER_SALE_PRICE, AFTER_PRICE 컬럼 추가 어떤값으로 변경됬는지 추가함.*******************************/
			$query =   "INSERT INTO TBL_GOODS_PRICE_CHANGE 
						(
							GOODS_NO
							,CP_NO
							,BUY_PRICE
							,SALE_PRICE
							,PRICE
							,STICKER_PRICE
							,PRINT_PRICE
							,DELIVERY_PRICE
							,DELIVERY_CNT_IN_BOX
							,LABOR_PRICE
							,OTHER_PRICE
							,SALE_SUSU
							,CP_SALE_SUSU
							,CP_SALE_PRICE
							,REG_ADM
							,REG_DATE
							,DISPLAY
							,REASON
							,AFTER_BUY_PRICE
							,AFTER_SALE_PRICE
							,AFTER_PRICE
							) 
							values (
								'$goods_no'
								,'$cp_no'
								,'$this_buy_price'
								,'$this_sale_price'
								,'$this_price'
								,'$this_sticker_price'
								,'$this_print_price'
								,'$this_delivery_price'
								,'$this_delivery_cnt_in_box'
								,'$this_labor_price'
								,'$this_other_price'
								,'$this_sale_susu'
								,'$this_cp_sale_susu'
								,'$this_cp_sale_price'
								,'$reg_adm'
								,now()
								,'Y'
								,'$this_reason'
								,'$buy_price'
								,'$sale_price'
								,'$price'
								); ";
			//echo $query;
			mysql_query($query,$db);
			if($display<>''){
				$query="UPDATE TBL_GOODS_PRICE_CHANGE SET DISPLAY = '".$display."' WHERE GOODS_NO = ".$goods_no." ; ";
			}

		} 
	}

	//상품 디테일 아래 상품가격 변동 리스팅
	function listGoodsPriceUpdate($db, $goods_no, $cp_no = '') {
		$query =   "SELECT
						SEQ_NO
						,GOODS_NO
						,CP_NO
						,BUY_PRICE
						,SALE_PRICE
						,PRICE
						,STICKER_PRICE
						,PRINT_PRICE
						,DELIVERY_PRICE
						,DELIVERY_CNT_IN_BOX
						,LABOR_PRICE
						,OTHER_PRICE
						,SALE_SUSU
						,CP_SALE_SUSU
						,CP_SALE_PRICE
						,REG_ADM
						,REG_DATE
						,DEL_TF
						,DEL_ADM
						,DEL_DATE
						,DISPLAY
						,REASON
					FROM
						TBL_GOODS_PRICE_CHANGE 
					WHERE
						GOODS_NO = '".$goods_no."'
						AND CP_NO = '".$cp_no."'
					ORDER BY
						REG_DATE DESC
					LIMIT 20 ";
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	/// 상품가격 판매 정보
	function listGoodsPriceBySaleCompany($db, $goods_no) {
	
		$query = "SELECT C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, GP.SALE_PRICE, GP.CP_SALE_SUSU, GP.CP_SALE_PRICE,
					     GP.SALE_SUSU, GP.PRICE
					FROM TBL_GOODS_PRICE GP
					JOIN TBL_COMPANY C ON GP.CP_NO = C.CP_NO
				   WHERE GP.GOODS_NO = '".$goods_no."'
				GROUP BY C.CP_NO
				ORDER BY CP_NM ASC ";
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function deleteGoodsPriceChangeAsSeqNo($db, $seq_no, $del_adm) {

		$query="UPDATE TBL_GOODS_PRICE_CHANGE 
		           SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
				 WHERE SEQ_NO = '$seq_no' ";
		
		//echo $query."<br/>";
		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

		/*
		$query = "INSERT INTO TBL_GOODS_PRICE_CHANGE_DELETED 
		          SELECT * 
				  FROM TBL_GOODS_PRICE_CHANGE 
				  WHERE SEQ_NO = '$seq_no' ";
		
		if(mysql_query($query,$db)) { 

			$query="DELETE FROM TBL_GOODS_PRICE_CHANGE WHERE SEQ_NO = '$seq_no' ";
		
			//echo $query."<br/>";
			if(!mysql_query($query,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}
		} else { 
			return false;
		}
		*/
	}

	function getGoodsNoAsName($db, $goods_name, $order_no) {
		
		$val = "";

		$query = "SELECT GOODS_NO, COUNT(GOODS_NO) AS CNT
									FROM TBL_GOODS 
								 WHERE DEL_TF = 'N' AND GOODS_NAME = '$goods_name' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NO						= $rows[0];
		$CNT								= $rows[1];

		if ($CNT == "1") {
			
			if ($order_no) {
				$query = "UPDATE TBL_TEMP_ORDER SET GOODS_NO = '$GOODS_NO'
									 WHERE ORDER_NO = '$order_no' ";

				mysql_query($query,$db);
			}
			
			$val = $GOODS_NO;
		} 
		
		return $val;		

	}

	function getGoodsNoAsCode($db, $goods_code, $order_no) {
		
		$val = "";

		$query = "SELECT GOODS_NO, COUNT(GOODS_NO) AS CNT
									FROM TBL_GOODS 
								 WHERE DEL_TF = 'N' AND GOODS_CODE = '$goods_code' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NO						= $rows[0];
		$CNT								= $rows[1];

		if ($CNT == "1") {
			
			if ($order_no) {
				$query = "UPDATE TBL_TEMP_ORDER SET GOODS_NO = '$GOODS_NO'
									 WHERE ORDER_NO = '$order_no' ";

				mysql_query($query,$db);
			}
			
			$val = $GOODS_NO;
		} 
		
		return $val;		

	}
	
	/*
	function getStockGoodsNoAsName($db, $goods_name, $stock_no) {
		
		$val = "";

		$query = "SELECT GOODS_NO, COUNT(GOODS_NO) AS CNT
									FROM TBL_GOODS 
								 WHERE GOODS_NAME = '$goods_name' AND DEL_TF = 'N' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NO						= $rows[0];
		$CNT								= $rows[1];

		if ($CNT == "1") {
			
			if ($order_goods_no) {
				$query = "UPDATE TBL_TEMP_STOCK SET GOODS_NO = '$GOODS_NO'
									 WHERE STOCK_NO = '$stock_no' ";

				mysql_query($query,$db);
			}
			
			$val = $GOODS_NO;
		} 
		
		return $val;

	}

	function getStockGoodsNoAsCode($db, $goods_code, $stock_no) {
		
		$val = "";

		$query = "SELECT GOODS_NO, COUNT(GOODS_NO) AS CNT
									FROM TBL_GOODS 
								 WHERE GOODS_CODE = '$goods_code' AND DEL_TF = 'N' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$GOODS_NO						= $rows[0];
		$CNT								= $rows[1];

		if ($CNT == "1") {
			
			if ($order_goods_no) {
				$query = "UPDATE TBL_TEMP_STOCK SET GOODS_NO = '$GOODS_NO'
									 WHERE STOCK_NO = '$stock_no'  ";

				mysql_query($query,$db);
			}
			
			$val = $GOODS_NO;
		} 
		
		return $val;		

	}
	*/

	function listGoodsNoSale($db) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
										 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
										 READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_GOODS.CATE_03 ) AS CP_NAME,
										 (SELECT ADM_NAME FROM TBL_ADMIN_INFO WHERE TBL_ADMIN_INFO.ADM_NO = TBL_GOODS.UP_ADM) AS ADMIN_NM
								FROM TBL_GOODS WHERE 1 = 1 ";

		$query .= " AND UP_DATE >= DATE_ADD(now(), INTERVAL -7 DAY)";

		$query .= " AND USE_TF = 'Y' ";

		$query .= " AND DEL_TF = 'N' ";

		$query .= " AND CATE_04 <> '판매중' ";

		$order_field = "UP_DATE";

		$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str ;

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


	function copyGoods($db, $goods_no, $reg_adm) {

		$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_GOODS ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_goods_no  = ($rows[0] + 1);

		$query="INSERT INTO TBL_GOODS (GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX,
																			 READ_CNT, DISP_SEQ, USE_TF, REG_ADM, REG_DATE) 
																SELECT '$new_goods_no', GOODS_CATE, '', CONCAT(GOODS_NAME, ' - COPY'), GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX,
																			 '0', DISP_SEQ, 'Y', '$reg_adm', now()
																	FROM TBL_GOODS
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);


		$query="INSERT INTO TBL_GOODS_SUB (GOODS_NO, GOODS_SUB_NO, GOODS_CNT) 
																SELECT '$new_goods_no', GOODS_SUB_NO, GOODS_CNT
																	FROM TBL_GOODS_SUB
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		/*
		// 2018-01-18 복사후 첫페이지 이후는 보지 않으므로 빼달라고 함 (남보경대리 요청)

		$query="INSERT INTO TBL_GOODS_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
																SELECT '$new_goods_no', FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2
																	FROM TBL_GOODS_IMAGES
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="INSERT INTO TBL_GOODS_OPTION (GOODS_NO, OPTION_NAME, OPTION_SUB, OPTION_SEQ, QTY, USE_TF) 
																SELECT '$new_goods_no', OPTION_NAME, OPTION_SUB, OPTION_SEQ, QTY, USE_TF
																	FROM TBL_GOODS_OPTION
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="INSERT INTO  TBL_GOODS_PROPOSAL (GOODS_NO, COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME) 
																SELECT '$new_goods_no', COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME
																	FROM TBL_GOODS_PROPOSAL
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);
		*/
	}

	
	function getGoodsList($db, $category, $search_field, $search_str) {

			$query = "SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, FILE_NM_100, IMG_URL, FILE_PATH_150, FILE_RNM_150
									FROM TBL_GOODS WHERE DEL_TF = 'N' AND USE_TF = 'Y' ";

			
			if ($category <> "") {
				$query .= " AND GOODS_CATE like '".$category."%' ";
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
				}
				else
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		
			//echo $query;
	        //exit;

			$result = mysql_query($query,$db);

			return $result;
	}



	function getGoodsByNo($db, $goods_no) {

		$query = "SELECT GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, IMG_URL
								FROM TBL_GOODS WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND GOODS_NO = '$goods_no' ";

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);
		return $result;
	}

	function isSaleGoods($db, $goods_no) {
		
		$val = "";

		$query = "SELECT COUNT(GOODS_NO) AS CNT
								FROM TBL_ORDER_GOODS 
								 WHERE GOODS_NO = '$goods_no' 
								 AND DEL_TF = 'N' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$CNT = $rows[0];

		if ($CNT == 0) {
			$val = false;
		} else {
			$val = true;
		}
		
		return $val;		

	}

	function getGoodsBuyPrice($db, $goods_no) {
		
		$query = "SELECT BUY_PRICE
					FROM TBL_GOODS 
				   WHERE GOODS_NO = '$goods_no' ";
			
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];

	}
	/*

	function insertGoodsProposal($db, $cp_no, $title, $image_url, $component, $description, $proposal_price, $retail_price, $delivery_cnt_in_box, $manufacturer, $origin, $use_tf, $goods_no, $goods_proposal_no, $reg_adm) {

		$query = "INSERT INTO TBL_PROPOSAL (CP_NO, TITLE, IMAGE_URL, COMPONENT, DESCRIPTION, PROPOSAL_PRICE, RETAIL_PRICE, DELIVERY_CNT_IN_BOX, MANUFACTURER, ORIGIN, USE_TF, GOODS_NO, GOODS_PROPOSAL_NO, REG_ADM, REG_DATE)
				  VALUES ('$cp_no', '$title', '$image_url', '$component', '$description', '$proposal_price', '$retail_price', '$delivery_cnt_in_box', '$manufacturer', '$origin', '$use_tf', '$goods_no', '$goods_proposal_no', '$reg_adm', now())  ";

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

	function updateGoodsProposal($db, $cp_no, $title, $image_url, $component, $description, $proposal_price, $retail_price, $delivery_cnt_in_box, $manufacturer, $origin, $use_tf, $goods_no, $goods_proposal_no, $up_adm) {

		$query = "UPDATE TBL_PROPOSAL
				  SET 
				     CP_NO = '$cp_no', 
					 TITLE = '$title', 
					 IMAGE_URL = '$image_url', 
					 COMPONENT = '$component', 
					 DESCRIPTION = '$description', 
					 PROPOSAL_PRICE = '$proposal_price', 
					 RETAIL_PRICE = '$retail_price', 
					 DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box', 
					 MANUFACTURER = '$manufacturer', 
					 ORIGIN = '$origin', 
					 USE_TF = '$use_tf', 
					 GOODS_NO = '$goods_no', 
					 UP_ADM = '$up_adm', 
					 UP_DATE = now()
				  WHERE GOODS_PROPOSAL_NO = '$goods_proposal_no'  ";

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

	function listGoodsProposal($db, $goods_no) {
	    //GOODS_PROPOSAL_NO, GOODS_NO, TITLE, IMAGE_URL, COMPONENT, DESCRIPTION, PROPOSAL_PRICE, RETAIL_PRICE, DELIVERY_CNT_IN_BOX, MANUFACTURER, ORIGIN, REG_DATE, REG_ADM, UP_DATE, UP_ADM, USE_TF

		$query = "SELECT GP.GOODS_PROPOSAL_NO, GP.CP_NO, C.CP_NM, GP.GOODS_NO, GP.TITLE, GP.IMAGE_URL, GP.COMPONENT, GP.DESCRIPTION,								 GP.PROPOSAL_PRICE, GP.RETAIL_PRICE, GP.DELIVERY_CNT_IN_BOX, GP.MANUFACTURER, GP.ORIGIN, GP.PROPOSAL_DATE, GP.REG_ADM, GP.UP_ADM,				GP.REG_DATE, GP.UP_DATE
				  FROM TBL_PROPOSAL GP 
				   LEFT JOIN TBL_COMPANY C ON GP.CP_NO = C.CP_NO
				  WHERE GP.GOODS_NO = '$goods_no'
				    AND GP.USE_TF = 'Y' 
				  ORDER BY GP.GOODS_PROPOSAL_NO DESC ";

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
	

	function selectGoodsProposal($db, $goods_proposal_no) {
	    //GOODS_PROPOSAL_NO, GOODS_NO, TITLE, IMAGE_URL, COMPONENT, DESCRIPTION, PROPOSAL_PRICE, RETAIL_PRICE, DELIVERY_CNT_IN_BOX, MANUFACTURER, ORIGIN, REG_DATE, REG_ADM, UP_DATE, UP_ADM, USE_TF

		$query = "SELECT GP.GOODS_PROPOSAL_NO, GP.CP_NO, C.CP_NM, GP.GOODS_NO, GP.TITLE, GP.IMAGE_URL, GP.COMPONENT, GP.DESCRIPTION,								 GP.PROPOSAL_PRICE, GP.RETAIL_PRICE, GP.DELIVERY_CNT_IN_BOX, GP.MANUFACTURER, GP.ORIGIN, GP.PROPOSAL_DATE, GP.REG_ADM, GP.UP_ADM,				 GP.USE_TF
				  FROM TBL_PROPOSAL GP 
				    LEFT JOIN TBL_COMPANY C ON GP.CP_NO = C.CP_NO
				  WHERE GOODS_PROPOSAL_NO = '$goods_proposal_no'  ";

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

	*/	

	
	/*
	//구성품 일괄 변경 - 사용안함
	function updateGoodsSubBatch($db, $goods_no, $pre_goods_no, $next_goods_no)
	{

		$query = "SELECT COUNT(*) FROM TBL_GOODS_SUB WHERE GOODS_NO = '$goods_no'";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$CNT = $rows[0];

		if($CNT < 1)
			return true;

		$query = " UPDATE TBL_GOODS_SUB
				   SET GOODS_SUB_NO = '$next_goods_no'
				   WHERE GOODS_SUB_NO = '$pre_goods_no' AND GOODS_NO = '$goods_no' ";

		

		//echo $query;
		//exit;

		$result = mysql_query($query,$db);

		//같은 쿼리 안에 같은 테이블이 있을경우 업데이트 해결
		//http://the-stickman.com/uncategorized/mysql-update-with-select-on-the-same-table-in-the-same-query/
		$query = "
		
		UPDATE TBL_GOODS 
		SET BUY_PRICE = 
			(
				SELECT selected_value
				FROM (

						SELECT SUM( BUY_PRICE ) AS selected_value
						FROM TBL_GOODS
						WHERE GOODS_NO
						IN (
							SELECT C.GOODS_SUB_NO
							FROM TBL_GOODS_SUB C
							WHERE C.GOODS_NO =  '$goods_no'
							)
	  				) AS sub_selected_value
			)
		WHERE GOODS_NO =  '$goods_no'
		";


		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}
	*/

	function tryGoodNoByGoodsCode($db, $goods_code, $is_force = 'N'){
		$query = "SELECT GOODS_NO, CATE_04 FROM TBL_GOODS WHERE GOODS_CODE = '$goods_code'
												 AND USE_TF = 'Y'
												 AND DEL_TF = 'N'";

	    //echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0]; //GOODS_NO
			$goods_type  = $rows[1]; //CATE_04(판매중, 단종, 품절)

			if($is_force == 'Y') { 
				return $record;
			} else {
				if($goods_type <> "판매중")
					return $goods_type; //단종,품절
				else
					return $record;
			}

		} else if(mysql_num_rows($result) > 1){

			return "복수상품 존재";

		} else {
			return "등록요망";
		}
	}


	function tryGoodsNameByGoodsCode($db, $goods_code){
		$query = "SELECT GOODS_NAME FROM TBL_GOODS WHERE GOODS_CODE = '$goods_code'
												 AND USE_TF = 'Y'
												 AND DEL_TF = 'N'";

	    //echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;

		} else {
			return "등록요망";
		}
	}

	function updateCalculatingGoodsPrice($db, $goods_no, $up_adm) {

		$query = "SELECT BUY_PRICE, SALE_PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX, LABOR_PRICE, OTHER_PRICE, SALE_SUSU
					FROM TBL_GOODS 
				   WHERE GOODS_NO = '$goods_no'
					 AND USE_TF = 'Y'
					 AND DEL_TF = 'N'; ";

		$rows = array();
		$result = mysql_query($query,$db);
		if(mysql_num_rows($result) == 1){

			$rows[0] = sql_result_array($result,0);

			$BUY_PRICE			 = $rows[0]["BUY_PRICE"];
			$SALE_PRICE			 = $rows[0]["SALE_PRICE"];
			$STICKER_PRICE		 = $rows[0]["STICKER_PRICE"];
			$PRINT_PRICE		 = $rows[0]["PRINT_PRICE"];
			$DELIVERY_PRICE		 = $rows[0]["DELIVERY_PRICE"];
			$DELIVERY_CNT_IN_BOX = $rows[0]["DELIVERY_CNT_IN_BOX"];
			$LABOR_PRICE		 = $rows[0]["LABOR_PRICE"];
			$OTHER_PRICE		 = $rows[0]["OTHER_PRICE"];
			$SALE_SUSU			 = $rows[0]["SALE_SUSU"];

			$query2 = "SELECT G.GOODS_CATE, G.BUY_PRICE, GS.GOODS_CNT
						FROM TBL_GOODS_SUB GS
						JOIN TBL_GOODS G ON GS.GOODS_SUB_NO = G.GOODS_NO
					   WHERE GS.GOODS_NO = '$goods_no'  ";

			$result2 = mysql_query($query2,$db);
			$record2 = array();

			$TOTAL_BUY_PRICE_SUB = 0;

			if ($result2 <> "") {
				for($i=0;$i < mysql_num_rows($result2);$i++) {
					$record2[$i] = sql_result_array($result2,$i);

					$SUB_GOODS_CATE = $record2[$i]["GOODS_CATE"];
					$SUB_BUY_PRICE  = $record2[$i]["BUY_PRICE"];
					$SUB_GOODS_CNT  = $record2[$i]["GOODS_CNT"];
					
					if($SUB_GOODS_CATE == "010202") {
						$TOTAL_BUY_PRICE_SUB += round($SUB_BUY_PRICE / $DELIVERY_CNT_IN_BOX);
					} else {
						$TOTAL_BUY_PRICE_SUB += $SUB_BUY_PRICE * $SUB_GOODS_CNT;
					}

				}
			}

			//변화없음
			if($BUY_PRICE == $TOTAL_BUY_PRICE_SUB) return true;

			$RECAL_PRICE = $TOTAL_BUY_PRICE_SUB + $STICKER_PRICE + $PRINT_PRICE + round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX) + $LABOR_PRICE + $OTHER_PRICE;

			insertGoodsPriceUpdate($db, "TBL_GOODS", $goods_no, "", $RECAL_PRICE, $TOTAL_BUY_PRICE_SUB, $SALE_PRICE, $STICKER_PRICE, $PRINT_PRICE, $DELIVERY_PRICE, $DELIVERY_CNT_IN_BOX, $LABOR_PRICE, $OTHER_PRICE, $SALE_SUSU, '', '', $up_adm,'');

			$query3 = " UPDATE TBL_GOODS
						   SET BUY_PRICE = '$TOTAL_BUY_PRICE_SUB', 
						       PRICE = '$RECAL_PRICE' 
						 WHERE GOODS_NO = '$goods_no';  ";
			
			if(!mysql_query($query3,$db)) {
				return false;
				echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
				exit;
			} else {
				return true;
			}

		} else 
			return true;

	}

	//이 상품을 포함한 포함상품(세트) 리스트 가져오기
	function listGoodsSet($db, $goods_sub_no) {
		
		$query = "SELECT GS.GOODS_NO 
					FROM TBL_GOODS_SUB GS 
				   WHERE GS.GOODS_SUB_NO IN (SELECT G.GOODS_NO FROM TBL_GOODS G WHERE G.GOODS_NO = '$goods_sub_no')  ";

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

	function updateGoodsBuyPriceForSets($db) {

		$query="
					SELECT B.GOODS_NO, 
							SUM( 
								 CASE WHEN C.GOODS_CATE LIKE '010202%' 
										THEN C.BUY_PRICE * B.GOODS_CNT / G.DELIVERY_CNT_IN_BOX
										ELSE C.BUY_PRICE * B.GOODS_CNT 
							  	 END 
							    ) AS BUY_TOTAL
						FROM TBL_GOODS_SUB B 
						JOIN TBL_GOODS G ON G.GOODS_NO = B.GOODS_NO
						JOIN TBL_GOODS C ON B.GOODS_SUB_NO = C.GOODS_NO
						WHERE G.GOODS_CATE LIKE '14%'
						GROUP BY B.GOODS_NO " ;
		
		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$GOODS_NO = $record[$i]["GOODS_NO"];
				$BUY_TOTAL = $record[$i]["BUY_TOTAL"];

				$query="	UPDATE TBL_GOODS A 
					SET A.BUY_PRICE = '$BUY_TOTAL',
					    A.PRICE = $BUY_TOTAL + A.PRINT_PRICE + A.STICKER_PRICE + ROUND(A.DELIVERY_PRICE / A.DELIVERY_CNT_IN_BOX) + A.LABOR_PRICE + A.OTHER_PRICE,
						A.EXTRA_PRICE = A.PRINT_PRICE + A.STICKER_PRICE + ROUND(A.DELIVERY_PRICE / A.DELIVERY_CNT_IN_BOX) + A.LABOR_PRICE + A.OTHER_PRICE 
					WHERE A.GOODS_NO = '$GOODS_NO'
				";

				mysql_query($query,$db);
			}
		}
	}


	function getMaxCodeByGoodsCode($db) {
		$query = "SELECT MAX(SUBSTRING(GOODS_CODE,6)) + 1
					FROM 
					(
						SELECT GOODS_CODE
						  FROM TBL_GOODS WHERE LENGTH(GOODS_CODE) >= 10 AND DEL_TF = 'N' AND USE_TF = 'Y' 
					     UNION ALL
						SELECT GOODS_CODE
					      FROM TBL_GOODS_CODE_REFERENCE 
					) C";

	    //echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;
		}
	}

	//상품코드 중복체크
	//json_goods_list, pop_goods_reference, goods_write_file, goods_modify
	function chkDuplicateGoodsCode($db, $goods_code)
	{
		$query = "SELECT COUNT(*)
					FROM TBL_GOODS 
				   WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND GOODS_CODE = '$goods_code' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	//상품코드 일련번호 중복체크
	//json_goods_list, pop_goods_reference, goods_write_file, goods_modify
	function chkDuplicatePartlyGoodsCode($db, $goods_code)
	{
		
		$query = "SELECT COUNT(*)
					FROM TBL_GOODS 
				   WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND SUBSTRING(GOODS_CODE, 5) = '$goods_code' ";
		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}



	////////////////////////////////////////////////////////////////////////////////
	///                임시 상품 코드 생성/조회/삭제
	/////////////////////////////////////////////////////////////////////////////////
		
	function listGoodsCodeReference($db, $keyword, $order_field, $order_str) {

		if($keyword != "") { 
		$query = "SELECT REF_NO, GOODS_CODE, GOODS_NAME, '임시상품' AS FROM_TABLE
					FROM TBL_GOODS_CODE_REFERENCE

				   UNION ALL

				  SELECT  REF_NO, CONCAT(B, '-', C, D) AS GOODS_CODE, E AS GOODS_NAME,  '기존엑셀등록상품' AS FROM_TABLE
				    FROM TBL_GOODS_REFERENCE
				   WHERE (CONCAT(B, '-', C, D) LIKE  '%".$keyword."%' OR E LIKE  '%".$keyword."%')

				   UNION ALL
					
				  SELECT GOODS_NO AS REF_NO, GOODS_CODE, GOODS_NAME,  '등록상품' AS FROM_TABLE
					FROM TBL_GOODS
				   WHERE USE_TF =  'Y'
					 AND DEL_TF =  'N'
					 AND (GOODS_CODE LIKE  '%".$keyword."%' OR GOODS_NAME LIKE  '%".$keyword."%') ";

		} else {
			$query = "SELECT REF_NO, GOODS_CODE, GOODS_NAME, '임시상품' AS FROM_TABLE
					FROM TBL_GOODS_CODE_REFERENCE " ;
	
		}

		if($order_field == "")
			$order_field = "REF_NO";

		if($order_str == "")
			$order_str = "ASC";

		
		$query .= " ORDER BY ".$order_field." ".$order_str;
		
		// echo $query;
		// exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function InsertGoodsCodeReference($db, $goods_code, $goods_name) {

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_CODE_REFERENCE 
				   WHERE GOODS_CODE = '$goods_code' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		//기존에 들어있다면 패스
		if($rows[0] > 0) return;


		$query = "INSERT INTO TBL_GOODS_CODE_REFERENCE (GOODS_CODE, GOODS_NAME)
					   VALUES ('$goods_code', '$goods_name') ";
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

	function deleteGoodsCodeReference($db, $ref_no) { 

		$query = "DELETE FROM TBL_GOODS_CODE_REFERENCE 
						WHERE REF_NO = '".$ref_no."'";
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

	///////////////////////////////////////////////////////////////////////////////////
	////		상품 제안서
	///////////////////////////////////////////////////////////////////////////////////


	function selectGoodsProposal($db, $goods_no) {

		$query = "SELECT COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME
					FROM TBL_GOODS_PROPOSAL 
				   WHERE GOODS_NO = '$goods_no' ";

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

	function updateGoodsProposal($db, $component, $description_title, $description_body, $origin, $goods_no) { 

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_PROPOSAL 
				   WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if($rows[0] > 0) { 
			//UPDATE
			$query = "UPDATE TBL_GOODS_PROPOSAL
						 SET COMPONENT		   = '".$component."', 
						     DESCRIPTION_TITLE = '".$description_title."', 
							 DESCRIPTION_BODY  = '".$description_body."',
							 ORIGIN			   = '".$origin."'
				       WHERE GOODS_NO = '".$goods_no."' ";

			//echo $query;
			//exit;

		} else { 
			//INSERT
			$query = "INSERT INTO TBL_GOODS_PROPOSAL (GOODS_NO, COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN)
			               VALUES ('".$goods_no."', '".$component."', '".$description_title."', '".$description_body."', '".$origin."') ";

			//echo $query;
			//exit;
		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	// 상품가격 변경된 정보로 재 설정
	function updateGoodsPrice($db, $column, $value, $up_adm, $goods_no) {

		$query ="SELECT BUY_PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX, LABOR_PRICE, OTHER_PRICE, PRICE, SALE_PRICE, SALE_SUSU
		           FROM TBL_GOODS
				  WHERE GOODS_NO = '$goods_no' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$BUY_PRICE				= $rows[0];
		$STICKER_PRICE			= $rows[1];
		$PRINT_PRICE			= $rows[2];
		$DELIVERY_PRICE			= $rows[3];
		$DELIVERY_CNT_IN_BOX	= $rows[4];
		$LABOR_PRICE			= $rows[5];
		$OTHER_PRICE			= $rows[6];
		$PRICE					= $rows[7];
		$SALE_PRICE				= $rows[8];
		$SALE_SUSU				= $rows[9];

		if($column == "BUY_PRICE")
			return;      //상위 세트의 공급가를 변경하기 때문에 잠시 보류                          //$BUY_PRICE = $value;
		if($column == "STICKER_PRICE")
			$STICKER_PRICE = $value;
		if($column == "PRINT_PRICE")
			$PRINT_PRICE = $value;
		if($column == "DELIVERY_PRICE")
			$DELIVERY_PRICE = $value;
		if($column == "DELIVERY_CNT_IN_BOX")
			$DELIVERY_CNT_IN_BOX = $value;
		if($column == "LABOR_PRICE")
			$LABOR_PRICE = $value;
		if($column == "OTHER_PRICE")
			$OTHER_PRICE = $value;
		if($column == "SALE_PRICE")
			$SALE_PRICE = $value;
		if($column == "SALE_SUSU")
			$SALE_SUSU = $value;

		if($BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX) + $LABOR_PRICE + $OTHER_PRICE != $PRICE) { 
			//매입합계 재 계산
			$PRICE = $BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX) + $LABOR_PRICE + $OTHER_PRICE;
		} 

		insertGoodsPriceUpdate($db, "TBL_GOODS", $goods_no, $no_cp_no, $PRICE, $BUY_PRICE, $SALE_PRICE, $STICKER_PRICE, $PRINT_PRICE, $DELIVERY_PRICE, $DELIVERY_CNT_IN_BOX, $LABOR_PRICE, $OTHER_PRICE, $SALE_SUSU, '', '', $up_adm);

		$query="	UPDATE TBL_GOODS 
				       SET " ; 

		if($column <> '' && $value <> '')
			$query .= "     ".$column."					= '".$value."', ";

$query .= "                 PRICE = '$PRICE',
							UP_ADM						=	'$up_adm',
							UP_DATE						=	now()
					 WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function updateGoodsWrapInfo($db, $wrap_width, $wrap_length, $wrap_memo, $goods_no) { 

		$query = "	UPDATE TBL_GOODS 
					   SET WRAP_WIDTH = '$wrap_width',
						   WRAP_LENGTH = '$wrap_length',
						   WRAP_MEMO = '$wrap_memo'
					 WHERE GOODS_NO = '$goods_no'
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

	function updateNextSalePrice($db, $reg_adm) { 

		$query = "	INSERT INTO TBL_GOODS_PRICE_CHANGE 
								(GOODS_NO, CP_NO, BUY_PRICE, SALE_PRICE, PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, REG_ADM, REG_DATE)
					SELECT GOODS_NO, 0, BUY_PRICE, SALE_PRICE, PRICE, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, DELIVERY_CNT_IN_BOX, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, '$reg_adm', now()
					  FROM TBL_GOODS 
					 WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND NEXT_SALE_PRICE IS NOT NULL AND SALE_PRICE <> NEXT_SALE_PRICE
				 ";

		mysql_query($query,$db);

		$query = "	UPDATE TBL_GOODS 
					   SET SALE_PRICE = NEXT_SALE_PRICE, NEXT_SALE_PRICE = null
					 WHERE NEXT_SALE_PRICE IS NOT NULL
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

	function getGoodsWrapInfo($db, $goods_no) {

		$query = "SELECT WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO
					FROM TBL_GOODS 
				   WHERE USE_TF = 'Y' AND DEL_TF = 'N' ";
		
		if ($goods_no <> "") {
			$query .= " AND GOODS_NO = '".$goods_no."' ";
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


	function listSearchGoodsCategory($db, $goods_cate, $has_sub_cate, $page, $goods_no) {

		$query = "set @rownum = 0; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum + 1  as rn, C.CATE_CD, C.CATE_NAME, GC.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, GC.PAGE, GC.SEQ
					FROM TBL_GOODS_CATEGORY GC
					JOIN TBL_CATEGORY C ON GC.GOODS_CATE = C.CATE_CD 
					JOIN TBL_GOODS G ON G.GOODS_NO = GC.GOODS_NO
				   WHERE C.USE_TF = 'Y' AND C.DEL_TF = 'N' 
				     AND G.USE_TF = 'Y' AND G.DEL_TF = 'N' 
				   
				   ";

		if ($goods_cate <> "") {
			if($has_sub_cate == "Y")
				$query .= " AND C.CATE_CD LIKE '".$goods_cate."%' ";
			else
				$query .= " AND C.CATE_CD = '".$goods_cate."' ";
		}
		
		if ($page <> "") {
			$query .= " AND GC.PAGE = '".$page."' ";
		}

		if ($goods_no <> "") {
			$query .= " AND GC.GOODS_NO = '".$goods_no."' ";
		}		

		$query .= "ORDER BY  C.REG_DATE, GC.PAGE, GC.SEQ, GC.REG_DATE DESC";

		// echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function cntSearchGoodsCategory($db, $goods_cate, $has_sub_cate) {

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_CATEGORY GC
					JOIN TBL_CATEGORY C ON GC.GOODS_CATE = C.CATE_CD 
				   WHERE C.USE_TF = 'Y' AND C.DEL_TF = 'N' 
				   
				   ";

		if ($goods_cate <> "") {
			if($has_sub_cate == "Y")
				$query .= " AND C.CATE_CD LIKE '".$goods_cate."%' ";
			else
				$query .= " AND C.CATE_CD = '".$goods_cate."' ";
		}	

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function insertGoodsCategory($db, $goods_no, $goods_cate, $page, $seq = '') { 

		//2017-10-12 $seq 자동부여/ajax 변경으로 직접입력 안함

		if($page <> "") { 
			$query="SELECT IFNULL(MAX(SEQ), 0) + 1
					  FROM TBL_GOODS_CATEGORY 
					 WHERE GOODS_CATE = '$goods_cate' AND PAGE = '$page'
					 ";
			//echo $query."<br/>";
			//exit;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			$max_seq  = $rows[0];
		} else 
			$max_seq = "";

		$query="INSERT INTO TBL_GOODS_CATEGORY (GOODS_NO, GOODS_CATE, PAGE, SEQ, REG_DATE) 
													  values ('$goods_no', '$goods_cate', '$page', '$max_seq', now()); ";
		//echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteGoodsCategory($db, $goods_no, $goods_cate) { 

		$query="DELETE FROM TBL_GOODS_CATEGORY 
				      WHERE GOODS_NO = '$goods_no' AND GOODS_CATE = '$goods_cate' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function deleteGoodsCategoryBatch($db, $goods_no, $goods_cate) { 

		$query="DELETE FROM TBL_GOODS_CATEGORY 
				      WHERE GOODS_NO = '$goods_no' AND GOODS_CATE LIKE '$goods_cate%' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function cntGoodsCategory($db, $goods_no){

		$query="SELECT COUNT(*) FROM TBL_GOODS_CATEGORY 
				      WHERE GOODS_NO = '$goods_no'; ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listGoodsBySearchCategory($db, $goods_cate) {

		$query = "SELECT  GC.GOODS_CATE, G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.SALE_PRICE, G.DELIVERY_CNT_IN_BOX, GC.PAGE, G.CATE_04, GC.SEQ
				    FROM TBL_GOODS G 
					JOIN TBL_GOODS_CATEGORY GC ON G.GOODS_NO = GC.GOODS_NO
				   WHERE 1 = 1 AND G.USE_TF = 'Y' AND G.DEL_TF = 'N' ";

		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' OR GC.GOODS_CATE LIKE '".$goods_cate."%') ";
		}

		$query .= " ORDER BY GC.PAGE, GC.SEQ, GC.GOODS_CATE, G.GOODS_NAME ";

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


	//////////////////////////////////////////////////////////////////
	//    
	//////////////////////////////////////////////////////////////////
	function insertGoodsBuyCompany($db, $goods_no, $buy_cp_no, $buy_price, $memo, $reg_adm) { 

		$query="INSERT INTO TBL_GOODS_BUY_COMPANY (GOODS_NO, BUY_CP_NO, BUY_PRICE, MEMO, REG_ADM, REG_DATE) 
		             VALUES ('$goods_no', '$buy_cp_no', '$buy_price', '$memo', '$reg_adm', now())";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}

	function listGoodsBuyCompany($db, $goods_no) {

		$query = "SELECT B.BUY_CP_NO, B.BUY_PRICE, B.MEMO,
						 CONCAT(C.CP_NM, ' ', C.CP_NM2) BUY_CP_NAME
				    FROM TBL_GOODS_BUY_COMPANY B
					JOIN TBL_COMPANY C ON B.BUY_CP_NO = C.CP_NO
				   WHERE B.DEL_TF = 'N' AND B.GOODS_NO = '$goods_no' ";

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

	function deleteGoodsBuyCompany($db, $goods_no, $cp_no, $del_adm) { 

		$query=" UPDATE TBL_GOODS_BUY_COMPANY 
				    SET DEL_TF = 'Y', DEL_ADM = '$del_adm', DEL_DATE = now()
		          WHERE GOODS_NO = '$goods_no' AND BUY_CP_NO = '$cp_no' ";
		
		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}




	///////////////////////////////////////////////////////////////////////////////////
	////		상품 추가 정보
	///////////////////////////////////////////////////////////////////////////////////

	function listGoodsExtraGroup($db, $pcode) {

		$query = "SELECT DCODE, DCODE_NM, DCODE_EXT 
					FROM TBL_CODE_DETAIL 
				   WHERE PCODE = '$pcode' ";

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

	function selectGoodsExtra($db, $goods_no, $pcode = '') {

		$query = "SELECT PCODE, DCODE
					FROM TBL_GOODS_EXTRA 
				   WHERE GOODS_NO = '$goods_no' ";

		if($pcode <> "")
			$query .= " AND PCODE = '$pcode' ";

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

	function updateGoodsExtra($db, $pcode, $dcode, $goods_no) { 

		$query = "SELECT COUNT(*)
					FROM TBL_GOODS_EXTRA 
				   WHERE GOODS_NO = '".$goods_no."' AND PCODE = '".$pcode."' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if($rows[0] > 0) { 
			//UPDATE
			$query = "UPDATE TBL_GOODS_EXTRA
						 SET DCODE		   = '".$dcode."'
				       WHERE GOODS_NO = '".$goods_no."' AND PCODE = '".$pcode."'  ";

			//echo $query;
			//exit;

		} else { 
			//INSERT
			$query = "INSERT INTO TBL_GOODS_EXTRA (GOODS_NO, PCODE, DCODE)
			               VALUES ('".$goods_no."', '".$pcode."', '".$dcode."') ";

			//echo $query;
			//exit;
		}

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}


	//////////////////////////////////////////////////////////////
	// 
	/////////////////////////////////////////////////////////////

function totalCntBuyCompany($db, $arr_options, $search_field, $search_str) {

		$query = "
				
			   SELECT COUNT(DISTINCT C.CP_NO)
					FROM TBL_GOODS G
					JOIN TBL_COMPANY C ON G.CATE_03 = C.CP_NO
				   WHERE G.USE_TF =  'Y'
					 AND G.DEL_TF =  'N' ";

				if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (C.CP_NM like '%".$search_str."%' OR C.CP_NM2 like '%".$search_str."%' OR C.CP_CODE = '".$search_str."')"; 
			
			} else {

				if ($search_field == "CP_NAME") {
					$query .= " AND (C.CP_NM like '%".$search_str."%' OR C.CP_NM2 like '%".$search_str."%')"; 
				} else if ($search_field == "CP_CODE") {
					$query .= " AND (C.CP_CODE = '".$search_str."')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listBuyCompany($db, $arr_options, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "
				
			   SELECT @rownum:= @rownum - 1  as rn, C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, C.EMAIL, COUNT(*) AS GOODS_CNT
					FROM TBL_GOODS G
					JOIN TBL_COMPANY C ON G.CATE_03 = C.CP_NO
				   WHERE G.USE_TF =  'Y'
					 AND G.DEL_TF =  'N' ";

				if ($search_str <> "") {

			if ($search_field == "ALL") {
				
					$query .= " AND (C.CP_NM like '%".$search_str."%' OR C.CP_NM2 like '%".$search_str."%' OR C.CP_CODE = '".$search_str."')"; 
			
			} else {

				if ($search_field == "CP_NAME") {
					$query .= " AND (C.CP_NM like '%".$search_str."%' OR C.CP_NM2 like '%".$search_str."%')"; 
				} else if ($search_field == "CP_CODE") {
					$query .= " AND (C.CP_CODE = '".$search_str."')"; 
				} else {
					$query .= " AND ".$search_field." like '%".$search_str."%' ";
				}
			}
		}
		$query .= "	GROUP BY C.CP_NO ";


		if ($order_field == "") 
			$order_field = "C.CP_NM";

		if ($order_str == "") 
			$order_str = "ASC";

		$query .= " ORDER BY ".$order_field." ".$order_str.", C.CP_NO limit ".$offset.", ".$nRowCount;

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

	function cntGoodsBuyCompany($db, $cp_no, $cate_04){

		$query=" SELECT COUNT(*) 
		           FROM TBL_GOODS 
				  WHERE CATE_03 = '$cp_no' AND USE_TF = 'Y' AND DEL_TF = 'N' AND CATE_04 = '$cate_04'    ; ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function getGoodsTaxTF($db, $goods_no){
		$query = "SELECT TAX_TF 
		            FROM TBL_GOODS 
				   WHERE GOODS_NO = '$goods_no'
					 AND USE_TF = 'Y'
					 AND DEL_TF = 'N'";

	    //echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;

		} else {
			return '';
		}
	}



	/////////////////////////////////////////////////////////////////////////////////
	// 홈페이지 카달로그 리스트
	/////////////////////////////////////////////////////////////////////////////////

	function listGoodsCatalog($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount, $total_cnt) {

		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];
		$cate_page		  = $arr_options["cate_page"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, 
						 G.GOODS_NO, G.GOODS_CATE, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, 
						 
						 G.PRICE, G.BUY_PRICE, G.SALE_PRICE, G.EXTRA_PRICE, G.STICKER_PRICE, G.PRINT_PRICE, G.DELIVERY_PRICE, G.SALE_SUSU, G.LABOR_PRICE, G.OTHER_PRICE, G.DELIVERY_PRICE,
						 G.STOCK_CNT, G.TAX_TF, G.DELIVERY_CNT_IN_BOX, G.MSTOCK_CNT, G.TSTOCK_CNT, G.FSTOCK_CNT, G.BSTOCK_CNT, 
						 
						 G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, G.CONTENTS,
										 
						 G.READ_CNT, G.DISP_SEQ, G.USE_TF, G.DEL_TF, G.REG_ADM, G.REG_DATE, G.UP_ADM, G.UP_DATE, G.DEL_ADM, G.DEL_DATE,
						 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = G.CATE_03 ) AS CP_NAME,
						 G.NEXT_SALE_PRICE, G.USE_TF, G.WRAP_WIDTH, G.WRAP_LENGTH, G.WRAP_MEMO, G.RESTOCK_DATE, 
						 GC.PAGE, GC.SEQ, G.CONCEAL_PRICE_TF
				    FROM TBL_GOODS G 
					JOIN TBL_GOODS_CATEGORY GC ON G.GOODS_NO = GC.GOODS_NO
				   WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR GC.GOODS_CATE LIKE '".$goods_cate."%')";
		}

		if ($start_date <> "") {
			$query .= " AND G.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND G.SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND G.SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 <= '".$end_price."' ";
			}

		}

		if ($cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND G.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND G.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND G.CATE_04 = '".$cate_04."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND G.TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($cate_page <> "") {
			$query .= " AND GC.PAGE = '".$cate_page."' ";
		}

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				//$query .= " AND GOODS_CATE not like '".$splited_exclude_category."%' ";

				$query .= " AND (G.GOODS_CATE NOT like '".$splited_exclude_category."%' 
						 AND GC.GOODS_CATE NOT LIKE '".$splited_exclude_category."%' ) ";
			}
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_NO = ".$search_str." OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND G.CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (G.GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND G.GOODS_CODE IN (".$query2.") ";
		
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND G.GOODS_CODE LIKE '".$search_str."%' ";
						
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		
		
		if ($order_field == "") 
			$order_field = "G.REG_DATE";
		else if($order_field == "VENDOR_PRICE") { 
			if($vendor_calc != "")
				$order_field = "CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10";
			else
				$order_field = "G.SALE_PRICE";
		} else if($order_field == "MAJIN") { 
			$order_field = " (G.SALE_PRICE - G.PRICE - ROUND(G.SALE_PRICE / 100 * G.SALE_SUSU)) ";
		} else if($order_field == "MAJIN_RATE") { 
			$order_field = " ((G.SALE_PRICE - G.PRICE - ROUND(G.SALE_PRICE / 100 * G.SALE_SUSU)) / G.SALE_PRICE * 100) ";
		} else if($order_field == "RANDOM") { 
			$order_field = "rand()";
		} else if($order_field == "PAGE_SEQ") { 
			$order_field = "GC.PAGE, GC.SEQ";
		}

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


	function totalCntGoodsCatalog($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options){

		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];
		$cate_page		  = $arr_options["cate_page"];

		$query ="SELECT COUNT(*) CNT 
		           FROM TBL_GOODS G 
				   JOIN TBL_GOODS_CATEGORY GC ON G.GOODS_NO = GC.GOODS_NO 
				  WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR GC.GOODS_CATE LIKE '".$goods_cate."%')";
		}

		if ($start_date <> "") {
			$query .= " AND G.REG_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= " AND G.REG_DATE <= '".$end_date." 23:59:59' ";
		}

		if($vendor_calc == "") { 

			if ($start_price <> "") {
				$query .= " AND G.SALE_PRICE >= '".$start_price."' ";
			}

			if ($end_price <> "") {
				$query .= " AND G.SALE_PRICE <= '".$end_price."' ";
			}

		} else { 

			if ($start_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 >= '".$start_price."'  ";
			}

			if ($end_price <> "") {
				$query .= " AND CEIL(((G.SALE_PRICE - G.PRICE) * ".$vendor_calc." / 100.0 + G.PRICE) / 10) * 10 <= '".$end_price."' ";
			}

		}

		if ($cate_01 <> "") {
			$query .= " AND G.CATE_01 = '".$cate_01."' ";
		}

		if ($cate_02 <> "") {
			$query .= " AND G.CATE_02 = '".$cate_02."' ";
		}

		if ($cate_03 <> "") {
			$query .= " AND G.CATE_03 = '".$cate_03."' ";
		}

		if ($cate_04 <> "") {
			$query .= " AND G.CATE_04 = '".$cate_04."' ";
		}

		if ($tax_tf <> "") {
			$query .= " AND G.TAX_TF = '".$tax_tf."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND G.USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND G.DEL_TF = '".$del_tf."' ";
		}

		if ($cate_page <> "") {
			$query .= " AND GC.PAGE = '".$cate_page."' ";
		}

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				//$query .= " AND GOODS_CATE not like '".$splited_exclude_category."%' ";

				$query .= " AND (G.GOODS_CATE NOT like '".$splited_exclude_category."%' 
						 AND GC.GOODS_CATE NOT LIKE '".$splited_exclude_category."%' ) ";
			}
		}

		if ($search_str <> "") {
			if ($search_field == "ALL") {
				if(is_numeric($search_str)) 
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_NO = ".$search_str." OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";
				else
					$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_SUB_NAME like '%".$search_str."%' OR G.GOODS_CODE LIKE '%".$search_str."%' OR G.MEMO LIKE '%".$search_str."%') ";

			//공급사코드
			} else if ($search_field == "CP_CODE") {
				$query .= " AND G.CATE_03 IN (SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE LIKE '%".$search_str."%') ";
		
			//상품명 + 규격
			} else if ($search_field == "GOODS_NAME") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR GOODS_SUB_NAME like '%".$search_str."%') ";
			
			//동시 포함 상품명<키워드1,키워드2>
			} else if ($search_field == "GOODS_NAME_AND") {
				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND (G.GOODS_NAME like '%".$splited_search_str."%') ";
				}
			//시스템 상품번호
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND ".$search_field." = '".$search_str."' ";
			//상품코드
			} else if ($search_field == "GOODS_CODE"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query2 .= "'".$splited_search_str."', ";
				}
				$query2 = rtrim($query2, ', ');

				$query .= " AND G.GOODS_CODE IN (".$query2.") ";
		
			//상품코드
			} else if ($search_field == "GOODS_CODE_STARTS_WITH"){
				$query .= " AND G.GOODS_CODE LIKE '".$search_str."%' ";
						
			//구성상품번호
			} else if ($search_field == "GOODS_SUB_NO"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND G.GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_GOODS_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_GOODS A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function listGoodsCategoryPage($db, $goods_cate) {
		
		$query = "
				  SELECT DISTINCT PAGE
					FROM TBL_GOODS_CATEGORY 
				   WHERE GOODS_CATE LIKE  '$goods_cate%' 
				ORDER BY PAGE ASC ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	/*
	function listSaleGoods($db, $goods_cate, $start_date, $end_date, $cp_no, $opt_manager_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nRowCount) {

		$query = "
					SELECT G.GOODS_CODE, G.GOODS_NAME, C.CP_CODE, C.CP_NM, C.CP_NM2, L.INOUT_DATE, L.QTY, L.UNIT_PRICE 
					  FROM TBL_COMPANY_LEDGER L
					  JOIN TBL_GOODS G ON L.GOODS_NO = G.GOODS_NO
					  JOIN TBL_COMPANY C ON L.CP_NO = C.CP_NO

					WHERE 
						L.DEL_TF = 'N'
					AND L.USE_TF = 'Y'
					AND L.INOUT_TYPE = '매출'
					AND L.INPUT_TYPE = '매출상품'
							
				";

		if ($goods_cate <> "") {
			$query .= " AND (G.GOODS_CATE like '".$goods_cate."%' 
						 OR L.GOODS_NO IN (SELECT GOODS_NO FROM TBL_GOODS_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
		}

		if ($start_date <> "") {
			$query .= "	AND L.INOUT_DATE >= '".$start_date."' ";
		}

		if ($end_date <> "") {
			$query .= "	AND L.INOUT_DATE <= '".$end_date." 23:59:59' ";
		}

		if ($cp_no <> "") {
			$query .= " AND L.CP_NO = '".$cp_no."' ";
		} 

		if ($opt_manager_no <> "") { 
			$query .= " AND C.SALE_ADM_NO = '".$opt_manager_no."' ";
		}

		if ($search_str <> "") {

			if ($search_field == "ALL") {
				$query .= " AND (G.GOODS_NAME like '%".$search_str."%' OR G.GOODS_CODE like '%".$search_str."%') ";
			} else if ($search_field == "GOODS_NO") {
				$query .= " AND (G.GOODS_NO  = '".$search_str."') ";
			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		$offset = $nRowCount*($nPage-1);
		
		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

			
		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	*/


	function listGoodsByGoodsNoArray($db, $arr_goods_no) {

		$query = "SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE AS RETAIL_PRICE, SALE_PRICE AS PROPOSAL_PRICE, DELIVERY_CNT_IN_BOX
				    FROM TBL_GOODS 
				   WHERE 1 = 1 AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		
		$query2 = "AND GOODS_NO IN (";
		foreach($arr_goods_no as $each_goods_no) { 
			$query2 .= "'".$each_goods_no."',";
		}
		$query2 = rtrim($query2, ",");
		$query2 .= ")";
		$query .= $query2;
		$query .= " ORDER BY GOODS_NAME ASC ";

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

	function chkExistSearchGoodsCateByGoodsNo($db, $goods_cate, $goods_no){

		$query = "SELECT COUNT(*) 
		            FROM TBL_GOODS_CATEGORY
				   WHERE GOODS_CATE LIKE '".$goods_cate."%' AND GOODS_NO = '".$goods_no."' ";


		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function selectGoodsPriceChange($db, $kind, $seq_no) {

		if($kind == "history") { 
			$query = "SELECT GOODS_NO, CP_NO, PRICE, BUY_PRICE, SALE_PRICE, DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, 
							 DELIVERY_PRICE,  LABOR_PRICE, OTHER_PRICE, SALE_SUSU, CP_SALE_SUSU, CP_SALE_PRICE, DISPLAY
						FROM TBL_GOODS_PRICE_CHANGE
					   WHERE SEQ_NO = '".$seq_no."' ";
		} else { 
			$query = "SELECT GOODS_NO, CP_NO, PRICE, BUY_PRICE, SALE_PRICE, DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, 
							 DELIVERY_PRICE,  LABOR_PRICE, OTHER_PRICE, SALE_SUSU, CP_SALE_SUSU, CP_SALE_PRICE, DISPLAY
						FROM TBL_GOODS_PRICE
					   WHERE SEQ_NO = '".$seq_no."' ";
		}
		
		$result = mysql_query($query,$db);
		$record = array();

		//echo $query."<br/>";

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function selectGoodsPriceChangeDistinctCPNo($db, $goods_no) {

		$query = "SELECT DISTINCT GP.CP_NO, C.CP_NM
					FROM TBL_GOODS_PRICE_CHANGE GP
					JOIN TBL_COMPANY C ON GP.CP_NO = C.CP_NO 
				   WHERE GOODS_NO = '".$goods_no."' AND C.DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		//echo $query."<br/>";

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function insertGoodsPrice($db, $goods_no, $cp_no, $price, $buy_price, $sale_price, $sticker_price, $print_price, $delivery_price, $delivery_cnt_in_box, $labor_price, $other_price, $sale_susu, $cp_sale_susu, $cp_sale_price, $reg_adm, $display) {
		$query="SELECT COUNT(*) AS CNT FROM TBL_GOODS_PRICE WHERE USE_TF = 'Y' AND GOODS_NO = '$goods_no' AND CP_NO = '$cp_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			$query =   "INSERT INTO TBL_GOODS_PRICE 
						(
							GOODS_NO
							,CP_NO
							,BUY_PRICE
							,SALE_PRICE
							,PRICE
							,STICKER_PRICE
							,PRINT_PRICE
							,DELIVERY_PRICE
							,DELIVERY_CNT_IN_BOX
							,LABOR_PRICE
							,OTHER_PRICE
							,SALE_SUSU
							,CP_SALE_SUSU
							,CP_SALE_PRICE
							,REG_ADM
							,REG_DATE
							,DISPLAY
							) 
							values (
								'$goods_no'
								,'$cp_no'
								,'$buy_price'
								,'$sale_price'
								,'$price'
								,'$sticker_price'
								,'$print_price'
								,'$delivery_price'
								,'$delivery_cnt_in_box'
								,'$labor_price'
								,'$other_price'
								,'$sale_susu'
								,'$cp_sale_susu'
								,'$cp_sale_price'
								,'$reg_adm'
								,now()
								,'$display'
								);";
		} else {
			$query =   "UPDATE TBL_GOODS_PRICE 
						SET BUY_PRICE 			= '$buy_price', 
					       SALE_PRICE 			= '$sale_price', 
						   PRICE 				= '$price', 
						   STICKER_PRICE 		= '$sticker_price', 
						   PRINT_PRICE 		= '$print_price', 
						   DELIVERY_PRICE 		= '$delivery_price', 
						   DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box', 
						   LABOR_PRICE 		= '$labor_price', 
						   OTHER_PRICE 		= '$other_price', 
						   SALE_SUSU 			= '$sale_susu',
						   CP_SALE_SUSU 		= '$cp_sale_susu',
						   CP_SALE_PRICE 		= '$cp_sale_price',
						   UP_ADM				= '$reg_adm',
						   UP_DATE				= now(),
						   DISPLAY				= '$display'
					 WHERE USE_TF = 'Y'
					 	AND GOODS_NO = '$goods_no'
						AND CP_NO = '$cp_no' ";
		}
		//echo $query;
		if(!mysql_query($query,$db)) {
			return false;
		} else {
			return true;
		}
	}

	function selectGoodsByCode($db, $goods_code) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
								PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, TSTOCK_CNT, 
								TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
								FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
								DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE,
								READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE, STOCK_TF
					FROM TBL_GOODS
				   WHERE GOODS_CODE = '$goods_code' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getGoodsMemo($db, $goods_no){


		$query ="SELECT MEMO 
		           FROM TBL_GOODS 
				  WHERE GOODS_NO = '$goods_no' ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}

	function getMroSalePrice($db, $goods_no){
		$query = "SELECT SALE_PRICE AS MRO_SALE_PRICE
							FROM TBL_GOODS_PRICE
							WHERE GOODS_NO = '".mysql_real_escape_string($goods_no)."'
								AND CP_NO ='1480'
		";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = (int)$rows[0];
		return $record;
	}

?>