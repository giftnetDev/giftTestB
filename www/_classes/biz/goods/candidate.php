<?
	# =============================================================================
	# File Name    : TBL_CANDIDATE
	# =============================================================================

	function listGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount) {

		$total_cnt = totalCntGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];

		$offset = $nRowCount*($nPage-1);

		$logical_num = ($total_cnt - $offset) + 1 ;

		$query = "set @rownum = ".$logical_num ."; ";
		mysql_query($query,$db);

		$query = "SELECT @rownum:= @rownum - 1  as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, 
										 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
										 READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
										 (SELECT CP_NM FROM TBL_COMPANY WHERE TBL_COMPANY.CP_NO = TBL_CANDIDATE.CATE_03 ) AS CP_NAME,
										 DELIVERY_CNT_IN_BOX, MSTOCK_CNT, TSTOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, DELIVERY_PRICE, NEXT_SALE_PRICE, USE_TF, WRAP_WIDTH, WRAP_LENGTH, WRAP_MEMO, RESTOCK_DATE
				    FROM TBL_CANDIDATE 
				   WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			$query .= " AND (GOODS_CATE like '".$goods_cate."%' 
						 OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_CANDIDATE_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
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
						 AND GOODS_NO NOT IN (SELECT GOODS_NO FROM TBL_CANDIDATE_CATEGORY WHERE GOODS_CATE LIKE '".$splited_exclude_category."%' )) ";
			}
		}

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
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}

		
		if ($order_field == "") 
			$order_field = "REG_DATE";
		else if($order_field == "VENDOR_PRICE") { 
			if($vendor_calc != "")
				$order_field = "CEIL(((SALE_PRICE - PRICE) * ".$vendor_calc." / 100.0 + PRICE) / 10) * 10";
			else
				$order_field = "SALE_PRICE";
		} else if($order_field == "MAJIN") { 
			$order_field = " (SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) ";
		} else if($order_field == "MAJIN_RATE") { 
			$order_field = " ((SALE_PRICE - PRICE - ROUND(SALE_PRICE / 100 * SALE_SUSU)) / SALE_PRICE * 100) ";
		} else if($order_field == "RANDOM") { 
			$order_field = "rand()";
		}

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit ".$offset.", ".$nRowCount;

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


	function totalCntGoods($db, $goods_cate, $start_date, $end_date, $start_price, $end_price, $cate_01, $cate_02, $cate_03, $cate_04, $tax_tf, $use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str){

		$exclude_category = $arr_options["exclude_category"];
		$vendor_calc	  = $arr_options["vendor_calc"];

		$query ="SELECT COUNT(*) CNT FROM TBL_CANDIDATE WHERE 1 = 1 ";

		if ($goods_cate <> "") {
			$query .= " AND (GOODS_CATE like '".$goods_cate."%' 
						 OR GOODS_NO IN (SELECT GOODS_NO FROM TBL_CANDIDATE_CATEGORY WHERE GOODS_CATE LIKE '".$goods_cate."%' ))";
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
						 AND GOODS_NO NOT IN (SELECT GOODS_NO FROM TBL_CANDIDATE_CATEGORY WHERE GOODS_CATE LIKE '".$splited_exclude_category."%' )) ";
			}
		}

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
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO = '".$search_str."') ";
			
			//구성상품코드
			} else if ($search_field == "GOODS_SUB_CODE"){
				$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_CODE = '".$search_str."')) ";
			
			//동시포함상품코드<상품코드1,상품코드2>
			} else if ($search_field == "GOODS_SUB_CODE_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_CODE = '".$splited_search_str."')) ";
				}
			//동시포함상품명<상품명1,상품명2>
			} else if ($search_field == "GOODS_SUB_NAME_AND"){

				foreach (explode(",", $search_str) as $splited_search_str){
					$query .= " AND GOODS_NO IN (SELECT B.GOODS_NO FROM TBL_CANDIDATE_SUB B WHERE B.GOODS_SUB_NO IN (SELECT A.GOODS_NO FROM TBL_CANDIDATE A WHERE A.GOODS_NAME LIKE '%".$splited_search_str."%')) ";
				}

			} else {
				$query .= " AND ".$search_field." like '%".$search_str."%' ";
			}
		}
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}



	function insertGoods($db, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_cnt, $fstock_cnt, $bstock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm) {
		
		$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_CANDIDATE ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_goods_no  = ($rows[0] + 1);
		

		$query="INSERT INTO TBL_CANDIDATE (
										GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, 
										CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
										PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, 
										STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, 
										TAX_TF, IMG_URL, 
										FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
										FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, 
										CONTENTS, MEMO, DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, 
										LABOR_PRICE, OTHER_PRICE,
										READ_CNT, DISP_SEQ, USE_TF, REG_ADM, REG_DATE) 
						values ('$new_goods_no', '$goods_cate', '$goods_code', '$goods_name', '$goods_sub_name', 
										'$cate_01', '$cate_02', '$cate_03', '$cate_04', '$restock_date',
										'$price', '$buy_price', '$sale_price', " ; 

		if($next_sale_price == "") 
			$query .= "					null,";
		else
			$query .= "					'$next_sale_price',";
		
										
		$query .= "						'$extra_price', 
										'$stock_cnt', '$fstock_cnt', '$bstock_cnt', '$mstock_cnt', 
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

	function updateGoodsProposal($db, $component, $description_title, $description_body, $origin, $goods_no) { 

		$query = "SELECT COUNT(*)
					FROM TBL_CANDIDATE_PROPOSAL 
				   WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query;
	    //exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if($rows[0] > 0) { 
			//UPDATE
			$query = "UPDATE TBL_CANDIDATE_PROPOSAL
						 SET COMPONENT		   = '".$component."', 
						     DESCRIPTION_TITLE = '".$description_title."', 
							 DESCRIPTION_BODY  = '".$description_body."',
							 ORIGIN			   = '".$origin."'
				       WHERE GOODS_NO = '".$goods_no."' ";

			//echo $query;
			//exit;

		} else { 
			//INSERT
			$query = "INSERT INTO TBL_CANDIDATE_PROPOSAL (GOODS_NO, COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN)
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

	function updateGoods($db, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $up_adm, $goods_no) {

		$query="UPDATE TBL_CANDIDATE SET 
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
									SALE_PRICE				= '$sale_price', ";
		if($next_sale_price == "") 
			$query .= "					NEXT_SALE_PRICE			= null,";
		else
			$query .= "					NEXT_SALE_PRICE			= '$next_sale_price',";
		
		$query .= "
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
									UP_DATE					=	now()
							 WHERE GOODS_NO = '$goods_no' ";

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


	function deleteGoodsFile($db, $del_adm, $goods_no) {

		$query="DELETE FROM TBL_CANDIDATE_IMAGES WHERE GOODS_NO = '$goods_no' ";
		
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
			$query="INSERT INTO TBL_CANDIDATE_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, 
																		FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
													  values ('$goods_no', '$file_nm1', '$file_rnm1', '$file_path1', '$file_size1', '$file_ext1',
																		'$file_nm2', '$file_rnm2', '$file_path2', '$file_size2', '$file_ext2'); ";
		} else {
			$query="INSERT INTO TBL_CANDIDATE_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, 
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

	function deleteGoods($db, $goods_no, $del_adm) {
		
		$query= "DELETE FROM TBL_CANDIDATE_PRICE  WHERE GOODS_NO = '$goods_no'";
		mysql_query($query,$db);

		//$query= "DELETE FROM TBL_CANDIDATE_OPTION WHERE GOODS_NO = '$goods_no'";
		//mysql_query($query,$db);

		$query= "DELETE FROM TBL_CANDIDATE_IMAGES WHERE GOODS_NO = '$goods_no'";
		mysql_query($query,$db);

		$query="UPDATE TBL_CANDIDATE SET 
							 DEL_TF				= 'Y',
							 DEL_ADM			= '$del_adm',
							 DEL_DATE			= now()														 
				 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="DELETE FROM TBL_CANDIDATE_SUB WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

	function selectGoods($db, $goods_no) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
								PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, TSTOCK_CNT, 
								TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
								FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
								DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE,
								READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_CANDIDATE
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

	function selectGoodsFile($db, $goods_no) {

		$query = "SELECT GOODS_IMAGE_NO,GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2
								FROM TBL_CANDIDATE_IMAGES
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


	function selectGoodsProposal($db, $goods_no) {

		$query = "SELECT COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME
					FROM TBL_CANDIDATE_PROPOSAL 
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


	function copyGoods($db, $goods_no, $reg_adm) {

		$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_CANDIDATE ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_goods_no  = ($rows[0] + 1);

		$query="INSERT INTO TBL_CANDIDATE (GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX,
																			 READ_CNT, DISP_SEQ, USE_TF, REG_ADM, REG_DATE) 
																SELECT '$new_goods_no', GOODS_CATE, '', CONCAT(GOODS_NAME, ' - COPY'), GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX,
																			 '0', DISP_SEQ, 'Y', '$reg_adm', now()
																	FROM TBL_CANDIDATE
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);
		
		/*
		// 2018-01-18 복사후 첫페이지 이후는 보지 않으므로 빼달라고 함 (남보경대리 요청)

		$query="INSERT INTO TBL_CANDIDATE_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
																SELECT '$new_goods_no', FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2
																	FROM TBL_CANDIDATE_IMAGES
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="INSERT INTO  TBL_CANDIDATE_PROPOSAL (GOODS_NO, COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME) 
																SELECT '$new_goods_no', COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME
																	FROM TBL_CANDIDATE_PROPOSAL
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);
		*/
	}


	function moveGoods($db, $goods_no, $reg_adm) {

		$query ="SELECT IFNULL(MAX(GOODS_NO),0) AS MAX_NO FROM TBL_GOODS ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_goods_no  = ($rows[0] + 1);

		$query="INSERT INTO TBL_GOODS (GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX, MEMO,
																			 READ_CNT, DISP_SEQ, USE_TF, REG_ADM, REG_DATE) 
																SELECT '$new_goods_no', GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
																			 PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
																			 FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS,
																			 STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, LABOR_PRICE, OTHER_PRICE, SALE_SUSU, DELIVERY_CNT_IN_BOX, MEMO,
																			 '0', DISP_SEQ, 'Y', '$reg_adm', now()
																	FROM TBL_CANDIDATE
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="UPDATE TBL_CANDIDATE
		           SET DEL_TF = 'Y', DEL_ADM = '$reg_adm', DEL_DATE = now()
				 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);
		
		$query="INSERT INTO TBL_GOODS_IMAGES (GOODS_NO, FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2) 
																SELECT '$new_goods_no', FILE_NM1, FILE_RNM1, FILE_PATH1, FILE_SIZE1, FILE_EXT1, FILE_NM2, FILE_RNM2, FILE_PATH2, FILE_SIZE2, FILE_EXT2
																	FROM TBL_CANDIDATE_IMAGES
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="DELETE FROM TBL_CANDIDATE_IMAGES
					  WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="INSERT INTO  TBL_GOODS_PROPOSAL (GOODS_NO, COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME) 
																SELECT '$new_goods_no', COMPONENT, DESCRIPTION_TITLE, DESCRIPTION_BODY, ORIGIN, DESCRIPTION_FILE_PATH, DESCRIPTION_FILE_NAME
																	FROM TBL_CANDIDATE_PROPOSAL
																 WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);

		$query="DELETE FROM TBL_CANDIDATE_PROPOSAL
					  WHERE GOODS_NO = '$goods_no' ";

		mysql_query($query,$db);
	}


?>