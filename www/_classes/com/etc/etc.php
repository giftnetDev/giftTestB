<?
	function getZipCode($db,$dong) {

		$offset = $nRowCount*($nPage-1);


		$query = "SELECT POST_NO , SIDO , SIGUNGU, DONG, RI , BUNJI, FULL_ADDR
								FROM TBL_ZIPCODE WHERE 1 = 1 ";
		
		if ($dong <> "") {
			$query .= " AND DONG like '%".$dong."%' ";
		}
		
		$query .= " ORDER BY POST_NO ";
		
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


	function getDCRate($db, $cp_no) {

		$query = "SELECT DC_RATE FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	function getLinkScriptForStock($db, $sub_reserve_no) {

		if($sub_reserve_no == "") return;

		if(startsWith($sub_reserve_no, "RGN:"))
		{
			$request_goods_no = str_replace('RGN:','', $sub_reserve_no);
			$query = "SELECT REQ_NO, GROUP_NO FROM TBL_GOODS_REQUEST_GOODS WHERE REQ_GOODS_NO = $request_goods_no AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";

			$result = mysql_query($query,$db);
			
			if($result) {
				$record = array();
				$record[0] = sql_result_array($result,$i);
				$request_no = $record[0]["REQ_NO"];
				$group_no = $record[0]["GROUP_NO"];
			 
				if($request_no <> '' && $group_no <> '')
					return "<a href=\"javascript:js_view_goods_request('".$request_no."');\">발주번호:".$request_goods_no."</a>";
			}

		}
		else { 
			$query = "SELECT RESERVE_NO FROM TBL_ORDER_GOODS  WHERE ORDER_GOODS_NO = $sub_reserve_no AND USE_TF = 'Y' AND DEL_TF = 'N' ";
			//echo $query."<br/>";
			$result = mysql_query($query,$db);
			if($result) {
				$rows   = mysql_fetch_array($result);
				$reserve_no = $rows[0];
				
				if($reserve_no <> '')
					return "<a href=\"javascript:js_view_order('','".$reserve_no."');\" title=\"".$sub_reserve_no."\">".$reserve_no."</a>";
			} 
		}

		return "<span style='color:red;'>검색불가</span>";
		
	}


	function getLinkScriptForOrderView($db, $reserve_no, $order_goods_no, $rgn_no) {

		//if($order_goods_no == "") return;

		$str_buttons = "";

		if($rgn_no != "" && $rgn_no != "0")
		{
			$query = "SELECT REQ_NO, GROUP_NO FROM TBL_GOODS_REQUEST_GOODS WHERE REQ_GOODS_NO = $rgn_no AND DEL_TF = 'N' AND CANCEL_TF = 'N' ";

			//echo $query."<br/>";

			$result = mysql_query($query,$db);
			
			if($result) {
				$record = array();
				$record[0] = sql_result_array($result,$i);
				$request_no = $record[0]["REQ_NO"];
				$group_no = $record[0]["GROUP_NO"];
			 
				if($request_no <> '' && $group_no <> '')
					$str_buttons .= "<input type='button' onclick=\"javascript:js_view_goods_request('".$request_no."');\" class=\"btntxt\" value='발주내역' />";
			}

		}

		
		if($str_buttons != "")
			$str_buttons .= "<br/>";
			//$str_buttons .= "&nbsp;&nbsp;";

		if($reserve_no == "" && $order_goods_no <> "") { 

			$query = "SELECT RESERVE_NO FROM TBL_ORDER_GOODS  WHERE ORDER_GOODS_NO = $order_goods_no AND USE_TF = 'Y' AND DEL_TF = 'N' ";
			//echo $query."<br/>";
			$result = mysql_query($query,$db);
			if($result) {
				$rows   = mysql_fetch_array($result);
				$reserve_no = $rows[0];
				
				if($reserve_no <> '')
					$str_buttons .=  "<a onclick=\"javascript:js_view_order('','".$reserve_no."');\" title=\"".$order_goods_no."\">".$reserve_no."</a>";
					//$str_buttons .=  "<input type='button' onclick=\"javascript:js_view_order('','".$reserve_no."');\" class=\"btntxt\" title=\"".$order_goods_no."\" value='주문내역' />";
			} 
		} else { 
			//echo $reserve_no;
			if($reserve_no != "") { 
				$str_buttons .=  "<a onclick=\"javascript:js_view_order('','".$reserve_no."');\" title=\"".$order_goods_no."\">".$reserve_no."</a>";
				//$str_buttons .=  "<input type='button' onclick=\"javascript:js_view_order('','".$reserve_no."');\" class=\"btntxt\" value='주문내역' />";
			}
		}

		if($str_buttons == "")
			$str_buttons =  "<span style='color:red;'>검색불가</span>";
	
		return $str_buttons;
		
	}

	function makeSelectBoxOnChange($db,$pcode,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO, DCODE_NO DESC ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\" style='width:".$size."px;' onChange=\"js_".$objname."();\">";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	//DCODE의 코드명과 코드위치가 바뀌어 나옴
	function makeSelectBoxAsName($db,$pcode,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:".$size."px;' >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE_NM) {
				$tmp_str .= "<option value='".$RS_DCODE_NM."' selected>".$RS_DCODE."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE_NM."'>".$RS_DCODE."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}
	



	function makeSelectBox($db,$pcode,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:".$size."px;' >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}
	function makeSelectBox1($db,$pcode,$objname,$str,$val,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeSelectBoxWithAttributes($db,$pcode,$objname,$size,$str,$val,$checkVal, $attribute) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;' ".$attribute." >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}


	function makeSelectBoxExtraClass($db,$pcode,$objname,$size,$str,$val,$checkVal, $class) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$select_class = ($class != "" ? "box01 ".$class : "box01");
		$tmp_str = "<select name='".$objname."' class=\"".$select_class."\"  style='width:".$size."px;' >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeSelectBoxWithExt($db,$pcode,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM, DCODE_EXT
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:".$size."px;' >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			$RS_DCODE_EXT	= Trim($row[2]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' data-ext='".$RS_DCODE_EXT."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."' data-ext='".$RS_DCODE_EXT."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeGenericSelectBox($db, $arr_result, $objname, $size, $str, $val, $checkVal, $value_column, $text_column) {

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;'>";


		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i = 0 ; $i < sizeof($arr_result); $i++) {
				
			$option_value = $arr_result[$i][$value_column];
			$option_text = $arr_result[$i][$text_column];

			if ($checkVal == $option_value) {
				$tmp_str .= "<option value='".$option_value."' selected>".$option_text."</option>";
			} else {
				$tmp_str .= "<option value='".$option_value."'>".$option_text."</option>";
			}
		}
		
		$tmp_str .= "</select>";
		return $tmp_str;
	
	}

	function makeGoodsSelectBoxWithDataImage($db, $arr_result, $objname, $size, $str, $val, $checkVal, $value_column, $text_column) {

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;'>";


		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'  data-image='/manager/images/no_img.gif'>".$str."</option>";
		}

		for($i = 0 ; $i < sizeof($arr_result); $i++) {
				
			$option_value = $arr_result[$i][$value_column];
			$option_text = $arr_result[$i][$text_column];

			$img_url	= getImage($db, $arr_result[$i]["GOODS_NO"], "250", "250");

			if ($checkVal == $option_value) {
				$tmp_str .= "<option value='".$option_value."'  data-image='".$img_url."' selected>".$option_text."</option>";
			} else {
				$tmp_str .= "<option value='".$option_value."'  data-image='".$img_url."'>".$option_text."</option>";
			}
		}
		
		$tmp_str .= "</select>";
		return $tmp_str;
	
	}

	function makeGoodsSelectBoxWithImage($db, $arr_result, $objname, $size, $className, $str, $val, $checkVal, $value_column, $text_column) {

		$tmp_str = "<select name='".$objname."' class='".$className."' style='width:".$size."px;'>";


		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i = 0 ; $i < sizeof($arr_result); $i++) {

			$img_url	= getImage($db, $arr_result[$i]["GOODS_NO"], "200", "200");

			$option_value = $arr_result[$i][$value_column];
			$option_text = $arr_result[$i][$text_column];

			if ($checkVal == $option_value) {
				$tmp_str .= "<option data-class='option_img' data-style='background-image: url(&apos;".$img_url."&apos;);' value='".$option_value."' selected>".$option_text."</option>";
			} else {
				$tmp_str .= "<option data-class='option_img' data-style='background-image: url(&apos;".$img_url."&apos;);' value='".$option_value."'>".$option_text."</option>";
			}
		}
		
		$tmp_str .= "</select>";
		return $tmp_str;
	
	}

	//require "../../_classes/com/util/ImgUtil.php"; 필요, width, height가 있을 경우 섬네일 제작
	function getImage($db, $goods_no, $width, $height) {

		$query = "SELECT IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150 FROM TBL_GOODS WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND GOODS_NO = '$goods_no' ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();


		for($i=0 ; $i < $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$img_url		= Trim($row[0]);
			$file_nm_100	= Trim($row[1]);
			$file_rnm_100	= Trim($row[2]);
			$file_path_100	= Trim($row[3]);
			$file_size_100	= Trim($row[4]);
			$file_ext_100	= Trim($row[5]);
			$file_nm_150	= Trim($row[6]);
			$file_rnm_150	= Trim($row[7]);
			$file_path_150	= Trim($row[8]);
			$file_size_150	= Trim($row[9]);
			$file_ext_150	= Trim($row[10]);

			if($width != "" && $height != "" && $file_path_150 == "") // 썸네일 제작(썸네일 사이즈가 있을때, 추가-이미지 캐쉬문제로 이미지경로로 들어갔을땐 썸네일 제작 안함)
				return getGoodsImage($file_nm_100, $img_url, $file_path_150, $file_rnm_150, $width, $height);
			else //섬네일 제작 없음
				return getGoodsOriginImage($file_nm_100, $img_url, $file_path_150, $file_rnm_150);

		}

		
	}

	function getGoodsOriginImage($file_nm, $img_url, $file_path_150, $file_rnm_150) {

		// 이미지가 저장 되어 있을 경우

		if ($file_nm <> "") {

			$img =  "/upload_data/goods"."/".$file_nm;

		} else {

			if ($img_url <> "") {
				$img = $img_url; 
			} else {
				if ($file_path_150 <> "") {

					$img = $file_path_150.$file_rnm_150;
							
				} else {
					$img = "/manager/images/no_img.gif";
				}
			}
		}

		return $img;
	}


	function makeShoppingSelectBox($db, $groupcd, $objname, $size, $str, $val, $checkVal) {

		$query = "SELECT itemcd, itemnm
								FROM gd_code WHERE 1 = 1 ";
		
		if ($groupcd <> "") {
			$query .= " AND groupcd = '".$groupcd."' ";
		}
		
		$query .= " ORDER BY sort ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeShoppingCheckBox($db, $groupcd, $objname, $checkVal) {

		$query = "SELECT itemcd, itemnm
								FROM gd_code WHERE 1 = 1 ";
		
		if ($groupcd <> "") {
			$query .= " AND groupcd = '".$groupcd."' ";
		}
		
		$query .= " ORDER BY sort ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			//echo ($checkVal&pow(2,$RS_DCODE))."<br>";

			if ($checkVal&pow(2,$RS_DCODE)) {
				$tmp_str .= "<input type = 'checkbox' class='checkbox' name= '".$objname."' value='".$RS_DCODE."' checked> ".$RS_DCODE_NM." \n";
			} else {
				$tmp_str .= "<input type = 'checkbox' class='checkbox' name= '".$objname."' value='".$RS_DCODE."'> ".$RS_DCODE_NM." \n";
			}
		}
		return $tmp_str;
	}

	function getDcodeName($db, $pcode, $dcode) {

		$query = "SELECT DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 ";
		
		$query .= " AND PCODE = '".$pcode."' ";
		$query .= " AND DCODE = '".$dcode."' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "";
		}

		return $tmp_str;
	}

	function getDcodeCode($db, $pcode, $dcode_nm) {

		$query = "SELECT DCODE
								FROM TBL_CODE_DETAIL WHERE 1 = 1 ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}

		if ($pcode <> "") {
			$query .= " AND DCODE_NM = '".$dcode_nm."' ";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;
	}

	function getDcodeExtByCode($db, $pcode, $dcode) {

		$query = "SELECT DCODE_EXT
								FROM TBL_CODE_DETAIL WHERE 1 = 1 ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}

		if ($dcode <> "") {
			$query .= " AND DCODE = '".$dcode."' ";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "";
		}

		return $tmp_str;
	}

	function getDcodeByExt($db, $pcode, $dcode_ext) {

		$query = "SELECT DCODE
								FROM TBL_CODE_DETAIL WHERE 1 = 1 ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}

		if ($dcode <> "") {
			$query .= " AND DCODE_EXT = '".$dcode_ext."' ";
		}
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;
	}

	function makeRadioBox($db,$pcode,$objname,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span class=\"lpd10\"><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked> ".$RS_DCODE_NM." </span> ";
			} else {
				$tmp_str .= "<span class=\"lpd10\"><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."'> ".$RS_DCODE_NM." </span> ";
			}
		}
		return $tmp_str;
	}

	function makeRadioBoxOnClick($db,$pcode,$objname,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			} else {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			}
		}
		return $tmp_str;
	}

	// 회원 종류에 따라 달리 보여지는 부분 (회원 정보 수정 시 만 사용
	function makeMemberRadioBoxOnClick($db,$pcode,$objname,$checkVal, $mem_type) {
		
		//echo $mem_type;

		if ($mem_type == "C") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE <> 'E' ";
		}

		if ($mem_type == "L") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE = 'L' ";
		}

		if ($mem_type == "E") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE = 'E' ";
		}

		if ($mem_type == "Y") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE IN ('Y','L') ";
		}

		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			} else {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			}
		}
		return $tmp_str;
	}


	// 회원 종류에 따라 달리 보여지는 부분 (회원 정보 수정 시 만 사용
	function makeMemberWithConditionRadioBoxOnClick($db,$pcode,$objname,$checkVal, $mem_type,$condition) {
		
		if ($mem_type == "C") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE <> 'E' ".$condition." ";
		}

		if ($mem_type == "L") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE = 'L' ".$condition." ";
		}

		if ($mem_type == "E") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE = 'E' ".$condition." ";
		}

		if ($mem_type == "Y") {
			$query = "SELECT DCODE, DCODE_NM
									FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND DCODE IN ('Y','L') ".$condition." ";
		}

		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			} else {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			}
		}
		return $tmp_str;
	}


	function makeRadioBoxJoinHow($db,$pcode,$objname,$checkVal,$join_how_person,$join_how_etc ) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			} else {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			
			if ($RS_DCODE == "JH98") {
				$tmp_str .= "<input type=\"text\" class=\"box01\" style=\"width: 71px;\" maxlength=\"15\" name=\"join_how_person\" value=\"".$join_how_person."\" />\n";
				$tmp_str .= "</td>\n";
				$tmp_str .= "</tr>\n";
				$tmp_str .= "<tr>\n";
				$tmp_str .= "<td>&nbsp;</td>\n";
				$tmp_str .= "<td class=\"end font11\">\n";
			}

			if ($RS_DCODE == "JH99") {
				$tmp_str .= "<input type=\"text\" class=\"box01\" style=\"width: 300px;\" maxlength=\"30\" name=\"join_how_etc\" value=\"".$join_how_etc."\" />\n";
			}

		}
		return $tmp_str;
	}

	function makeCheckBox($db,$pcode,$objname,$checkVal) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if (strrpos($checkVal,$RS_DCODE)) {
				$tmp_str .= "<input type = 'checkbox' class='checkbox' name= '".$objname."' value='".$RS_DCODE."' checked> ".$RS_DCODE_NM." ";
			} else {
				$tmp_str .= "<input type = 'checkbox' class='checkbox' name= '".$objname."' value='".$RS_DCODE."'> ".$RS_DCODE_NM." ";
			}
		}
		return $tmp_str;
	}

	function getSiteInfo($db, $site_no) {

		if($site_no <= 0) return null;

		$query = "SELECT SITE_NO, SITE_NM, SITE_LANG, SITE_CONTENT
					FROM TBL_SITE_INFO 
				   WHERE SITE_NO = '$site_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getCodeList($db,$pcode) {

		$query = "SELECT DCODE, DCODE_NM
					FROM TBL_CODE_DETAIL 
				   WHERE DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


	function makeGoodsSelectBox($db,$goods_type,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT GOODS_NO, GOODS_NM
								FROM TBL_GOODS WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($goods_type <> "") {
			$query .= " AND GOODS_TYPE = '".$goods_type."' ";
		} else {
			$query .= " AND GOODS_TYPE = '' ";
		}
		
		$query .= " ORDER BY DISP_SEQ ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_GOODS_NO	= Trim($row[0]);
			$RS_GOODS_NM	= Trim($row[1]);

			if ($checkVal == $RS_GOODS_NO) {
				$tmp_str .= "<option value='".$RS_GOODS_NO."' selected>".$RS_GOODS_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_GOODS_NO."'>".$RS_GOODS_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}


	function makeGoodsSelectBoxOnChange($db,$goods_type,$objname,$size,$str,$val,$checkVal) {

		$query = "SELECT GOODS_NO, GOODS_NM
								FROM TBL_GOODS WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($goods_type <> "") {
			$query .= " AND GOODS_TYPE = '".$goods_type."' ";
		} else {
			$query .= " AND GOODS_TYPE = '' ";
		}
		
		$query .= " ORDER BY DISP_SEQ ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;' onChange=\"js_".$objname."();\">";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_GOODS_NO	= Trim($row[0]);
			$RS_GOODS_NM	= Trim($row[1]);

			if ($checkVal == $RS_GOODS_NO) {
				$tmp_str .= "<option value='".$RS_GOODS_NO."' selected>".$RS_GOODS_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_GOODS_NO."'>".$RS_GOODS_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function getGoodsCodeName($db, $goods_no) {

		if($goods_no <= 0) return "&nbsp;";

		$query = "SELECT GOODS_CODE, GOODS_NAME
					FROM TBL_GOODS 
				   WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = "[".$rows[0]."] ".$rows[1];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;
	}

	function getGoodsName($db, $goods_no) {

		if($goods_no <= 0) return "&nbsp;";

		$query = "SELECT GOODS_NAME
					FROM TBL_GOODS WHERE 1 = 1 ";
		
		if ($goods_no <> "") {
			$query .= " AND GOODS_NO = '".$goods_no."' ";
		}		

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;
	}

	function getGoodsCode($db, $goods_no) {

		if($goods_no <= 0) return "&nbsp;";

		$query = "SELECT GOODS_CODE	
		            FROM TBL_GOODS 
				   WHERE GOODS_NO = '".$goods_no."' ";

		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;
	}

	function getBuyPrice($db, $buy_price, $goods_no, $delivery_cnt_in_box) {

		$query = "SELECT S.GOODS_CNT, G.BUY_PRICE, G.GOODS_CATE
					FROM TBL_GOODS_SUB S
					JOIN TBL_GOODS G
					WHERE S.GOODS_SUB_NO = G.GOODS_NO
					AND S.GOODS_NO = '$goods_no' ";
		
		$result = mysql_query($query,$db);
		$record = array();

		$TOTAL_BUY_PRICE = 0;
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);

				$buy_price = $record[$i]["BUY_PRICE"];
				$goods_cnt = $record[$i]["GOODS_CNT"];
				$goods_cate = $record[$i]["GOODS_CATE"];

				if(startsWith($goods_cate, "010202"))
					$TOTAL_BUY_PRICE += ($buy_price * $goods_cnt) / $delivery_cnt_in_box;
				else
					$TOTAL_BUY_PRICE += $buy_price * $goods_cnt;

			}
		} 

		if($TOTAL_BUY_PRICE == 0)
			return $buy_price; //세트가 아니면 기본 매입가로
		else
			return $TOTAL_BUY_PRICE;
	}

	function makeEventSelectBoxOnChange($db, $objname, $size, $str, $val, $checkVal,$event_type) {

		$query = "SELECT EVENT_NO, EVENT_NM
								FROM TBL_EVENT WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' and event_type='$event_type' ";
				
		$query .= " ORDER BY EVENT_NO DESC ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;' onChange=\"js_".$objname."();\">";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function getListGoods($db, $site_no, $goods_type) {

		$query = "SELECT GOODS_NO, GOODS_NM, GOODS_TYPE
								FROM TBL_GOODS WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		if ($site_no <> "") {
			$query .= " AND SITE_NO = '$site_no' ";
		}
		
		if ($goods_type <> "") {
			$query .= " AND GOODS_TYPE = '".$goods_type."' ";
		} else {
			$query .= " AND GOODS_TYPE = '' ";
		}
		
		$query .= " ORDER BY DISP_SEQ ";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function getListShoppingGoods($db, $str) {

		$query = "SELECT distinct a.goodsno,b.*,c.price,c.reserve,c.consumer,d.brandnm
								FROM gd_goods_link a left join gd_goods b on a.goodsno=b.goodsno 
										left join gd_goods_option c on a.goodsno=c.goodsno and link 
										left join gd_goods_brand d on b.brandno=d.sno left join gd_category e on a.category=e.category 
								WHERE a.hidden=0
									AND e.level<=0
									AND open
									AND (concat( keyword, goodsnm, goodscd, maker, if(brandnm is null,'',brandnm) ) like '%".$str."%') 
									ORDER BY 	a.sort ";


		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	function makeAdminInfoByMDSelectBox($db, $objname, $select_attribute, $str, $val, $checkVal) {

		$query = "SELECT ADM_NO, ADM_NAME
								FROM TBL_ADMIN_INFO WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' AND MD_TF = 'Y' ";
		
		
		$query .= " ORDER BY ADM_NAME ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' ".$select_attribute." >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_ADM_NO			= Trim($row[0]);
			$RS_ADM_NAME		= Trim($row[1]);

			if ($checkVal == $RS_ADM_NO) {
				$tmp_str .= "<option value='".$RS_ADM_NO."' selected>".$RS_ADM_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_ADM_NO."'>".$RS_ADM_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

/*
 	function makeAdminInfoSelectBox($db, $objname, $select_attribute, $str,$val,$checkVal) {

		$query = "SELECT ADM_NO, ADM_NAME
								FROM TBL_ADMIN_INFO WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		
		$query .= " ORDER BY ADM_NAME ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' ".$select_attribute." >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_ADM_NO			= Trim($row[0]);
			$RS_ADM_NAME		= Trim($row[1]);

			if ($checkVal == $RS_ADM_NO) {
				$tmp_str .= "<option value='".$RS_ADM_NO."' selected>".$RS_ADM_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_ADM_NO."'>".$RS_ADM_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}
*/

	function makeAdminInfoSelectBox($db, $checkVal){
		
		$query="SELECT ADM_NO, ADM_NAME
							FROM TBL_ADMIN_INFO A, TBL_ADMIN_GROUP G
						    WHERE A.GROUP_NO = G.GROUP_NO
							 AND A.DEL_TF = 'N'
							 AND A.USE_TF = 'Y' ";
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='adm_no' class=\"box01\"  style='width:100px;'>";

		$tmp_str .= "<option value=''>전체</option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$ADM_NO		= Trim($row[0]);
			$ADM_NAME	= Trim($row[1]);

			if ($checkVal == $ADM_NO) {
				$tmp_str .= "<option value='".$ADM_NO."' selected>".$ADM_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$ADM_NO."'>".$ADM_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;

	}

	function makeAdminInfoSelectBoxExtra($db, $objname, $group_no, $cp_no, $str, $val, $checkVal){
		
		$query="SELECT ADM_NO, ADM_NAME
				  FROM TBL_ADMIN_INFO 
				 WHERE DEL_TF = 'N'
				   AND USE_TF = 'Y' 
							 ";
		if($group_no <> "") 
			$query .= " AND GROUP_NO = '".$group_no."' ";

		if($cp_no <> "") 
			$query .= " AND COM_CODE = '".$cp_no."' ";

		$query .= " ORDER BY ADM_NAME ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:100px;'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$ADM_NO		= Trim($row[0]);
			$ADM_NAME	= Trim($row[1]);

			if ($checkVal == $ADM_NO) {
				$tmp_str .= "<option value='".$ADM_NO."' selected>".$ADM_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$ADM_NO."'>".$ADM_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;

	}

	function makeAdminGroupSelectBox($db, $objname,$size,$str,$val,$checkVal) {

		$query = "SELECT GROUP_NO, GROUP_NAME
								FROM TBL_ADMIN_GROUP WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		
		$query .= " ORDER BY GROUP_NAME ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' style='width:".$size."px;'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_GROUP_NO			= Trim($row[0]);
			$RS_GROUP_NAME		= Trim($row[1]);

			if ($checkVal == $RS_GROUP_NO) {
				$tmp_str .= "<option value='".$RS_GROUP_NO."' selected>".$RS_GROUP_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_GROUP_NO."'>".$RS_GROUP_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function getListAdminGroupMenu($db, $group_no) {


		$query = "SELECT CONCAT(A.MENU_SEQ01,A.MENU_SEQ02,A.MENU_SEQ03) as SEQ, A.MENU_CD, A.MENU_NAME, A.MENU_URL, 
										 B.READ_FLAG, B.REG_FLAG, B.UPD_FLAG, B.DEL_FLAG, B.FILE_FLAG, A.MENU_RIGHT 
								FROM TBL_ADMIN_MENU A, TBL_ADMIN_MENU_RIGHT B 
							 WHERE A.MENU_CD = B.MENU_CD 
								 AND B.GROUP_NO = '".$group_no."' 
								 AND A.MENU_FLAG = 'Y'
								 AND A.DEL_TF = 'N'
							 ORDER BY SEQ ";
		

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


	function makeScriptArray($db,$pcode,$objname) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
			
		$tmp_str_name		=	"";
		$tmp_str_value	=	"";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			$tmp_str_name		.= ",'".$RS_DCODE_NM."'";
			$tmp_str_value	.= ",'".$RS_DCODE."'";
				
		}
		
		$tmp_str_name  = substr($tmp_str_name, 1, strlen($tmp_str_name)-1);
		$tmp_str_value = substr($tmp_str_value, 1, strlen($tmp_str_value)-1);


		$tmp_str	= $objname."_nm = new Array(".$tmp_str_name."); \n";
		$tmp_str .= $objname."_val = new Array(".$tmp_str_value."); \n";

		return $tmp_str;
	}

	function DateScript($db,$choice_date) {

		$query = "SELECT count(RESERVE_NO) FROM TBL_RESERVATION WHERE CHECK_IN_DATE <= '$choice_date' and  CHECK_OUT_DATE >='$choice_date' and PERMISSION_YN='Y' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if($rows[0] > 0){
			$RESERVE_YN="Y";
		}

		return $RESERVE_YN;
	}

	function makeRadioBoxWithConditionOnClick($db,$pcode,$objname,$checkVal,$condition) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ".$condition." ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
		
		$tmp_str = "";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);
			
			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' checked onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			} else {
				$tmp_str .= "<span><input type = 'radio' name= '".$objname."' value='".$RS_DCODE."' onClick=\"js_".$objname."();\"> ".$RS_DCODE_NM." </span>&nbsp;&nbsp;&nbsp;";
			}
		}
		return $tmp_str;
	}

	function makeSelectBoxWithCondition($db,$pcode,$objname,$size,$str,$val,$checkVal, $condition) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ".$condition." ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:".$size."px;'>";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeSelectBoxWithConditionOnChange($db,$pcode,$objname,$size,$str,$val,$checkVal, $condition) {

		$query = "SELECT DCODE, DCODE_NM
								FROM TBL_CODE_DETAIL WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ".$condition." ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' class=\"box01\"  style='width:".$size."px;' onChange=\"js_".$objname."();\">";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeCategorySelectBoxOnChange($db, $checkVal, $exclude_category) {
		$query = "SELECT MAX(LENGTH(CATE_CD)) as MAX
									FROM TBL_CATEGORY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
			}
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$MAX = $rows[0];

		if ($checkVal == "") {
		
			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
									FROM TBL_CATEGORY 
								   WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "            ORDER BY SEQ ASC ";

			//echo $query;
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='gd_cate_01' onChange=\"js_gd_cate_01();\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);

				$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
			}
			$tmp_str .= "</select>&nbsp;";


			if ($MAX >= 4) {
				$tmp_str .= "<select name='gd_cate_02' onChange=\"js_gd_cate_02();\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 6) {
				$tmp_str .= "<select name='gd_cate_03' onChange=\"js_gd_cate_03();\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 8) {
				$tmp_str .= "<select name='gd_cate_04' onChange=\"js_gd_cate_04();\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

		} else {
			
			$cate_01 = substr($checkVal,0,2);
			$cate_02 = substr($checkVal,0,4);
			$cate_03 = substr($checkVal,0,6);
			$cate_04 = substr($checkVal,0,8);

			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
									FROM TBL_CATEGORY 
								 WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "            ORDER BY SEQ ASC ";
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='gd_cate_01' onChange=\"js_gd_cate_01();\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);
				
				if (trim($cate_01) == trim($RS_CATE_CD)) {
					$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
				} else {
					$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
				}
			}
			$tmp_str .= "</select>&nbsp;";
			
			if (strlen($checkVal) >= 2) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									   WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_01."%' 
										 AND LENGTH(CATE_CD) = '4' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "                ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='gd_cate_02' onChange=\"js_gd_cate_02();\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_02) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 4) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_02."%' 
				    					 AND LENGTH(CATE_CD) = '6' " ;
			
				if ($exclude_category <> "") {
					foreach (explode(",", $exclude_category) as $splited_exclude_category){
						$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
					}
				}
			
				$query .= "            ORDER BY SEQ ASC ";

				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='gd_cate_03' onChange=\"js_gd_cate_03();\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_03) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 6) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_03."%' 
										 AND LENGTH(CATE_CD) = '8' " ;
			
				if ($exclude_category <> "") {
					foreach (explode(",", $exclude_category) as $splited_exclude_category){
						$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
					}
				}
			
				$query .= "            ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='gd_cate_04' onChange=\"js_gd_cate_04();\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_04) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) == 2) {
		
				if ($MAX >= 6) {
					$tmp_str .= "<select name='gd_cate_03' onChange=\"js_gd_cate_03();\">";
					$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";

				}

				if ($MAX >= 8) {
					$tmp_str .= "<select name='gd_cate_04' onChange=\"js_gd_cate_04();\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}

			}

			if (strlen($checkVal) == 4) {
	

				if ($MAX >= 8) {
					$tmp_str .= "<select name='gd_cate_04' onChange=\"js_gd_cate_04();\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}
			}

		}

		return $tmp_str;
	}

	function makeCategoryGenericSelectBoxOnChange($db, $group_name, $checkVal) {

		$query = "SELECT MAX(LENGTH(CATE_CD)) as MAX
									FROM TBL_CATEGORY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$MAX = $rows[0];

		if ($checkVal == "") {
		
			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME, CATE_CODE
									FROM TBL_CATEGORY 
								 WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' 
								 ORDER BY SEQ ASC ";

			//echo $query;
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='".$group_name."_01' onChange=\"js_generic_cate_01('".$group_name."');\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);
				$RS_CATE_CODE	= Trim($row[3]);

				$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."'>".$RS_CATE_NAME."</option>";
			}
			$tmp_str .= "</select>&nbsp;";


			if ($MAX >= 4) {
				$tmp_str .= "<select name='".$group_name."_02' onChange=\"js_generic_cate_02('".$group_name."');\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 6) {
				$tmp_str .= "<select name='".$group_name."_03' onChange=\"js_generic_cate_03('".$group_name."');\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 8) {
				$tmp_str .= "<select name='".$group_name."_04' onChange=\"js_generic_cate_04('".$group_name."');\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

		} else {
			
			$cate_01 = substr($checkVal,0,2);
			$cate_02 = substr($checkVal,0,4);
			$cate_03 = substr($checkVal,0,6);
			$cate_04 = substr($checkVal,0,8);

			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME, CATE_CODE
									FROM TBL_CATEGORY 
								 WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' 
								 ORDER BY SEQ ASC ";
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='".$group_name."_01' onChange=\"js_generic_cate_01('".$group_name."');\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);
				$RS_CATE_CODE	= Trim($row[3]);
				
				if (trim($cate_01) == trim($RS_CATE_CD)) {
					$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."' selected>".$RS_CATE_NAME."</option>";
				} else {
					$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."'>".$RS_CATE_NAME."</option>";
				}
			}
			$tmp_str .= "</select>&nbsp;";
			
			if (strlen($checkVal) >= 2) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME, CATE_CODE
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_01."%' 
										 AND LENGTH(CATE_CD) = '4' 
									 ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$group_name."_02' onChange=\"js_generic_cate_02('".$group_name."');\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
					$RS_CATE_CODE	= Trim($row[3]);
				
					if (trim($cate_02) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 4) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME, CATE_CODE
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_02."%' 
										 AND LENGTH(CATE_CD) = '6' 
									 ORDER BY SEQ ASC ";
				//echo $query;
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$group_name."_03' onChange=\"js_generic_cate_03('".$group_name."');\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
					$RS_CATE_CODE	= Trim($row[3]);
				
					if (trim($cate_03) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 6) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME, CATE_CODE
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_03."%' 
										 AND LENGTH(CATE_CD) = '8' 
									 ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$group_name."_04' onChange=\"js_generic_cate_04('".$group_name."');\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
					$RS_CATE_CODE	= Trim($row[3]);
				
					if (trim($cate_04) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."' data-code='".$RS_CATE_CODE."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) == 2) {
		
				if ($MAX >= 6) {
					$tmp_str .= "<select name='".$group_name."_03' onChange=\"js_generic_cate_03('".$group_name."');\">";
					$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";

				}

				if ($MAX >= 8) {
					$tmp_str .= "<select name='".$group_name."_04' onChange=\"js_generic_cate_04('".$group_name."');\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}

			}

			if (strlen($checkVal) == 4) {
	

				if ($MAX >= 8) {
					$tmp_str .= "<select name='".$group_name."_04' onChange=\"js_generic_cate_04('".$group_name."');\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}
			}

		}

		return $tmp_str;
	}

	/*
	function makeCompanySelectBox($db, $cp_type, $checkVal) {

		$query = "SELECT CP_NO, CP_NM, CP_TYPE
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='cp_type' class=\"txt\" >";

		$tmp_str .= "<option value=''> 업체선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE				= Trim($row[0]);
			$RS_DCODE_NM		= Trim($row[1]);
			$RS_DCODE_TYPE	= Trim($row[2]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM." [".$RS_DCODE." ".$RS_DCODE_TYPE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM." [".$RS_DCODE." ".$RS_DCODE_TYPE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}
	*/

	function makeCompanySelectBoxWithName($db, $obj, $cp_type, $checkVal) {

		$query = "SELECT CP_NO, CP_NM, CP_TYPE
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$obj."' class=\"txt\" >";

		$tmp_str .= "<option value=''> 업체선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE				= Trim($row[0]);
			$RS_DCODE_NM		= Trim($row[1]);
			$RS_DCODE_TYPE	= Trim($row[2]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM." [".$RS_DCODE." ".$RS_DCODE_TYPE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM." [".$RS_DCODE." ".$RS_DCODE_TYPE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeCompanySelectBoxAsCpNoWithName($db, $cp_type, $obj, $checkVal) {

		$query = "SELECT CP_NO, CP_NM
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE = '".$cp_type."' ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$obj."' class=\"txt\" style=\"width:120px;\" >";

		$tmp_str .= "<option value=''> 선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeCompanySelectBoxAsCpNo($db, $cp_type, $checkVal) {

		$query = "SELECT CP_NO, CP_NM
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='cp_type2' class=\"txt\" >";

		$tmp_str .= "<option value=''> 업체선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

	function makeCompanySelectBoxOnChabge($db, $cp_type, $checkVal) {

		$query = "SELECT CP_NO, CP_NM
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='cp_type' class=\"txt\" onChange=\"js_cp_type()\">";

		$tmp_str .= "<option value=''> 업체선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE) {
				$tmp_str .= "<option value='".$RS_DCODE."' selected>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}


	function getCompanyName($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CONCAT('[', CP_CODE, '] ', CP_NM, ' ', CP_NM2) FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	
	function getCompanyAddress($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CP_ADDR FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCompanyEmail($db, $cp_no) {

		if($cp_no <= 0) return "";

		if (is_numeric($cp_no)) {

			$query = "SELECT EMAIL FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "";
			}
		} else {
			$tmp_str  = "";
		}

		return $tmp_str;

	}

	function getCompanyNameWithNoCode($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CONCAT(CP_NM, ' ', CP_NM2) FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function isCompanyMall($db, $cp_no) {

		if($cp_no <= 0) return "N";

		$query = "SELECT IS_MALL FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$is_mall  = ($rows[0] == "Y");
		} else {
			$is_mall  = "N";
		}

		return $is_mall;

	}

	function getCompanyCodeAsNo($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CP_CODE FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCompanyNameAsCode($db, $cp_code) {

		if($cp_code == "") return "&nbsp;";
		
		$query = "SELECT CP_NO, CP_NM FROM TBL_COMPANY WHERE CP_CODE = '".$cp_code."' ";
	
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[1]." [".$rows[0]."]";
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCompanyNoAsCode($db, $cp_code) {

		if($cp_code == "") return "&nbsp;";

		$query = "SELECT CP_NO FROM TBL_COMPANY WHERE CP_CODE = '".$cp_code."' ";
	
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCompanyNoAsName($db, $cp_name) {

		if($cp_name == "") return "&nbsp;";

		$query = "SELECT CP_NO FROM TBL_COMPANY WHERE CP_NM = '".$cp_name."' ";
	
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}


	function getCompanyPhone($db, $cp_no) {

		if($cp_no <= 0) return "&nbsp;";

		if (is_numeric($cp_no)) {

			$query = "SELECT CP_PHONE FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCategoryName($db, $cate_code) {

		if($cate_code == "") return "&nbsp;";

		$query = "SELECT CATE_CD, CATE_NAME
								FROM TBL_CATEGORY WHERE 1 = 1 ";
		$query .= " AND CATE_CD = '$cate_code' ";


		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0]) {
			$tmp_str  = $rows[1]." [".$rows[0]."]";
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCategoryNameOnly($db, $cate_code) {

		if($cate_code == "") return "&nbsp;";

		$query = "SELECT CATE_NAME
					FROM TBL_CATEGORY WHERE 1 = 1 ";
		$query .= " AND CATE_CD = '$cate_code' ";


		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0]) {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCategoryMemoOnly($db, $cate_code) {

		if($cate_code == "") return "&nbsp;";

		$query = "SELECT CATE_MEMO
					FROM TBL_CATEGORY WHERE 1 = 1 ";
		$query .= " AND CATE_CD = '$cate_code' ";


		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0]) {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}

	function getCompanyCode($db, $admin_id) {

		if($admin_id == "") return "&nbsp;";

		$query="SELECT C.CP_NO
							FROM TBL_COMPANY C, TBL_ADMIN_INFO A
						 WHERE C.CP_NO = A.COM_CODE
							 AND A.ADM_ID	= '$admin_id'
							 AND C.DEL_TF = 'N' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "&nbsp;";
		}

		return $tmp_str;

	}


	function getCompanyGoodsPrice($db, $goods_no, $cp_no) {

		if($goods_no <= 0 || $cp_no <= 0) return 0;

		$query = "SELECT SALE_PRICE FROM TBL_GOODS_PRICE WHERE USE_TF = 'Y' AND GOODS_NO = '".$goods_no."' AND CP_NO = '".$cp_no."' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$tmp_str  = $rows[0];
		} else {
			$tmp_str  = "0";
		}

		return $tmp_str;

	}


	function getCompanyGoodsPriceOrDCRate($db, $goods_no, $sale_price, $price, $cp_no, $dc_rate = null) {

		if($goods_no <= 0 || $cp_no <= 0) return $sale_price;

		$query = "SELECT SALE_PRICE FROM TBL_GOODS_PRICE WHERE USE_TF = 'Y' AND GOODS_NO = '".$goods_no."' AND CP_NO = '".$cp_no."' ";
		//echo $query."<br/>";
		
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "" && $rows[0] <> "") {
			return $rows[0];
		} else {

			if($dc_rate == null) { 
				$query2 = "SELECT DC_RATE FROM TBL_COMPANY WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND CP_NO = '".$cp_no."' ";
				
				//echo $query2."<br/>";
				$result2 = mysql_query($query2,$db);
				$rows2   = mysql_fetch_array($result2);
				$dc_rate = $rows2[0];
			} 

			//echo $dc_rate."<br/>";
			
			if ($sale_price > 0 && $dc_rate != null && $dc_rate <> "0") {
				return ceil((($sale_price - $price) * $dc_rate / 100.0 + $price) / 10) * 10; //round($sale_price * (100 - $dc_rate) / 100);
			} else {

				return $sale_price;
			}
		}

	}

	// 유일키를 생성
	function getUniqueId($db, $len=32) {

		$result = @mysql_query(" LOCK TABLES TBL_UNIQUE_ID WRITE, TBL_CART READ, TBL_ORDER READ ");
		
		if (!$result) {
			$sql = " CREATE TABLE TBL_UNIQUE_ID (
											`on_id` int(11) NOT NULL auto_increment,
											`on_uid` varchar(32) NOT NULL default '',
											`on_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
											`session_id` varchar(32) NOT NULL default '',
											PRIMARY KEY  (`on_id`),
											UNIQUE KEY `on_uid` (`on_uid`) ) ";
			mysql_query($sql,$db);
		}

		// 이틀전 자료는 모두 삭제함
		$ytime = date("Y-m-d H:i:s",strtotime("-2 day"));
		$sql = " delete from TBL_UNIQUE_ID where on_datetime < '$ytime' ";
		//echo $sql;
		mysql_query($sql,$db);

		$unique = false;

		do {
			$sql = " INSERT INTO TBL_UNIQUE_ID set on_uid = NOW(), on_datetime = NOW(), session_id = '".session_id()."' ";
			
			mysql_query($sql,$db);

			$id = @mysql_insert_id();
			$uid = md5($id);
			$sql =  " UPDATE TBL_UNIQUE_ID set on_uid = '$uid' where on_id = '$id' ";
			mysql_query($sql,$db);
			// 장바구니에도 겹치는게 있을 수 있으므로 ...
			$sql = "select COUNT(*) as cnt from TBL_CART where ON_UID = '$uid' ";
			
			$result = mysql_query($sql,$db);
			$rows   = mysql_fetch_array($result);

			if (!$row[0]) {
				// 주문서에도 겹치는게 있을 수 있으므로 ...
				$sql = "select COUNT(*) as cnt from TBL_ORDER where ON_UID = '$uid' ";

				$result = mysql_query($sql,$db);
				$rows   = mysql_fetch_array($result);

				if (!$row[0])
					$unique = true;
				}
			} while (!$unique); // $unique 가 거짓인동안 실행

			@mysql_query(" UNLOCK TABLES ");

		return $uid;
	}


	// 주문에서 사용할 함수들 입니다.
	// 유일키를 생성
	function getReservNo($db, $type, $len=13) {

		$thisdate = date("Y-m-d",strtotime("0 month"));;
		$thisdate_Reserve_no = date("Ymd",strtotime("0 month"));;

		$query ="SELECT COUNT(CNT_NO) AS CNT FROM TBL_RESERVE_NO WHERE THIS_DATE = '$thisdate'";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if (!$rows[0]) {
			$sql = " INSERT INTO TBL_RESERVE_NO (CNT_NO, THIS_DATE) VALUES ('1','$thisdate'); ";
		} else {
			$sql = " UPDATE TBL_RESERVE_NO SET CNT_NO = CNT_NO + 1 WHERE THIS_DATE = '$thisdate' ";
		}

		//echo $sql;
		
		mysql_query($sql,$db);
		
		$query ="SELECT IFNULL(MAX(CNT_NO),0) AS NEXT_NO FROM TBL_RESERVE_NO WHERE THIS_DATE = '$thisdate'";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_reserve_no  = $thisdate_Reserve_no.$type.right("00000".$rows[0],5);
		
		return $new_reserve_no;
	}

	function getDeliveryLink($db, $delivery_cp, $delivery_no) {

		if($delivery_cp == "") return "택배사없음";

		if($delivery_no == "") return "송장번호없음";
		
		$delivery_url = "";

		$query ="SELECT DCODE_EXT, DCODE, DCODE_NM 
							 FROM TBL_CODE_DETAIL 
							WHERE PCODE = 'DELIVERY_CP'
								AND DCODE = '$delivery_cp'
								AND USE_TF = 'Y' 
								AND DEL_TF = 'N' ";
		
		//echo $query;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($result <> "") {
			$delivery_url		= $rows[0];
			$delivery_cp		= $rows[2];
		}
		
		if ($delivery_url == "") {
			$url = "택배사경로 없음";
		} else {
			$url = "<a href='".$delivery_url.$delivery_no."' target='_new'>".$delivery_cp." ".$delivery_no."</a>";
		}
		return $url;

	}

	function resetOrderInfor($db, $reserve_no) {

		if($reserve_no == "") return false;
		
		$query = "SELECT CATE_01, QTY, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DISCOUNT_PRICE, ORDER_STATE
					FROM TBL_ORDER_GOODS
				   WHERE USE_TF = 'Y'
					 AND DEL_TF = 'N'
					 AND RESERVE_NO = '$reserve_no' ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
			
		$tmp_order_state = "";

		$total_qty = 0;
		$total_price = 0;
		$total_buy_price = 0;
		$total_sale_price = 0;
		$total_extra_price = 0;
		$total_discount_price = 0;

		$is_all_completed = true;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_CATE_01				= Trim($row["CATE_01"]);
			$RS_QTY					= Trim($row["QTY"]);
			$RS_PRICE				= Trim($row["PRICE"]);
			$RS_BUY_PRICE			= Trim($row["BUY_PRICE"]);
			$RS_SALE_PRICE			= Trim($row["SALE_PRICE"]);
			$RS_EXTRA_PRICE			= Trim($row["EXTRA_PRICE"]);
			$RS_DISCOUNT_PRICE		= Trim($row["DISCOUNT_PRICE"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			//2016-10-11 증정, 샘플은 주문금액 합산에서 아예 제외, 2016-12-21 샘플, 증정 주문서 금액에 다시 추가
			/*
			if($RS_CATE_01 <> "") { 
				$RS_SALE_PRICE = 0;
				$RS_DISCOUNT_PRICE = 0;
				$RS_EXTRA_PRICE = 0;
				$RS_PRICE = 0;
				$RS_BUY_PRICE = 0;

			}
			*/
			if($RS_ORDER_STATE != "3")
				$is_all_completed = false;

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
				$total_price = $total_price + ($RS_PRICE * $RS_QTY);
				$total_buy_price = $total_buy_price + ($RS_BUY_PRICE * $RS_QTY);
				$total_sale_price = $total_sale_price + ($RS_SALE_PRICE * $RS_QTY);
				$total_extra_price = $total_extra_price + ($RS_EXTRA_PRICE * $RS_QTY);
				$total_discount_price = $total_discount_price + $RS_DISCOUNT_PRICE;
			} else if ($RS_ORDER_STATE == "4") {
				$total_qty = $total_qty;
				$total_price = $total_price;
				$total_buy_price = $total_buy_price;
				$total_sale_price = $total_sale_price;
				$total_extra_price = $total_extra_price;
				$total_discount_price = $total_discount_price;
			} else {
				$total_qty = $total_qty - $RS_QTY;
				$total_price = $total_price - ($RS_PRICE * $RS_QTY);
				$total_buy_price = $total_buy_price - ($RS_BUY_PRICE * $RS_QTY);
				$total_sale_price = $total_sale_price - ($RS_SALE_PRICE * $RS_QTY);
				$total_extra_price = $total_extra_price - ($RS_EXTRA_PRICE * $RS_QTY);
				$total_discount_price = $total_discount_price - $RS_DISCOUNT_PRICE;
			}

			if ($i == 0) {
				$tmp_order_state = $RS_ORDER_STATE;
			} else {
				$tmp_order_state .= ",".$RS_ORDER_STATE;
			}
		}

		//echo $total_sale_price."<br/>";
		
		$up_query = "UPDATE TBL_ORDER 
						SET ORDER_STATE = '$tmp_order_state', 
							TOTAL_PRICE = '$total_price',
							TOTAL_BUY_PRICE = '$total_buy_price',
							TOTAL_SALE_PRICE = '$total_sale_price',
							TOTAL_EXTRA_PRICE = '$total_extra_price',
							TOTAL_DISCOUNT_PRICE = '$total_discount_price',
							TOTAL_QTY = '$total_qty' ";
		if(!$is_all_completed) { 
			$up_query .= ", FINISH_DATE = null
						  , DELIVERY_DATE = null
						 ";
		}
		
		$up_query .=" WHERE RESERVE_NO = '$reserve_no' 
						AND USE_TF = 'Y'
						AND DEL_TF = 'N' ";
		
		if(!mysql_query($up_query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}


	function getCompayChk ($db, $cp_type, $s_adm_cp_type, $cp_no) {

		if($cp_no <= 0) return false;

		if ($s_adm_cp_type ="운영") {

			$query="SELECT COUNT(*) AS CNT FROM TBL_COMPANY 
							 WHERE (CP_TYPE LIKE '%".$cp_type."%' OR CP_TYPE LIKE '%판매공급%')
								 AND CP_NO	= '$cp_no'
								 AND DEL_TF = 'N' ";
			
		} else {

			$query="SELECT COUNT(*) AS CNT 
								FROM TBL_COMPANY C, TBL_ADMIN_INFO A
							 WHERE C.CP_NO = A.COM_CODE
								 AND (C.CP_TYPE LIKE '%".$cp_type."%' OR CP_TYPE LIKE '%판매공급%')
								 AND A.ADM_ID	= '$cp_no'
								 AND C.DEL_TF = 'N' ";
			
		
		}
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}

	}

	function getCompayChkAsCode ($db, $cp_type, $s_adm_cp_type, $cp_code) {

		if($cp_code == "") return false;

		if ($s_adm_cp_type ="운영") {

			$query="SELECT COUNT(*) AS CNT FROM TBL_COMPANY 
							 WHERE CP_CODE	= '$cp_code'
								 AND DEL_TF = 'N' ";
			
		} else {

			$query="SELECT COUNT(*) AS CNT 
								FROM TBL_COMPANY C, TBL_ADMIN_INFO A
							 WHERE C.CP_NO = A.COM_CODE
								 AND A.ADM_ID	IN (SELECT CP_NO AS CNT FROM TBL_COMPANY WHERE CP_CODE	= '$cp_code' AND DEL_TF = 'N')
								 AND C.DEL_TF = 'N' ";
		}
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getStcokCodeChk ($db, $stock_type, $stock_code_nm) {

		if($stock_code_nm == "") return false;
		
		if ($stock_type == "IN") {
		
			$query="SELECT COUNT(*) AS CNT FROM TBL_CODE_DETAIL
							 WHERE PCODE = 'IN_ST' 
								 AND DCODE_NM = '$stock_code_nm'
								 AND DEL_TF = 'N' ";
		
		} else {

			$query="SELECT COUNT(*) AS CNT FROM TBL_CODE_DETAIL
							 WHERE PCODE = 'OUT_ST' 
								 AND DCODE_NM = '$stock_code_nm'
								 AND DEL_TF = 'N' ";
		}

		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}

	}

	function getGoodsNoChk ($db, $goods_no) {

		if($goods_no <= 0) return false;

		$query="SELECT COUNT(*) AS CNT FROM TBL_GOODS 
						 WHERE GOODS_NO	= '$goods_no'
							 AND DEL_TF = 'N' ";
			
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getLocChkAsName ($db, $in_loc) {

		if($in_loc == "") return false;

		$query="SELECT COUNT(*) AS CNT FROM TBL_CODE_DETAIL 
							 WHERE PCODE = 'LOC' 
								 AND DCODE_NM	= '$in_loc'
								 AND DEL_TF = 'N' ";
			
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getCompanyChk ($db, $cp_no) {

		if($cp_no <= 0) return false;

		$query="SELECT COUNT(*) AS CNT FROM TBL_COMPANY 
						 WHERE CP_NO	= '$cp_no'
							 AND DEL_TF = 'N' ";
			
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	// 인터넷 몰인지 여부 검사(수수료적용여부와 관련)
	function isMallCompany($db, $cp_no) {

		if($cp_no <= 0) return false;

		$query="SELECT COUNT(*) AS CNT FROM TBL_COMPANY 
						 WHERE CP_NO	= '$cp_no'
							 AND USE_TF = 'Y' AND DEL_TF = 'N' AND IS_MALL = 'Y' ";
			
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function chkZip($db, $zipcode) {
		
		$zipcode = str_replace("-","",$zipcode);

		$query="SELECT COUNT(*) AS CNT FROM TBL_ZIPCODE WHERE POST_NO = '$zipcode' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getDeliveryUrl($db, $delivery_cp) {

		if($delivery_cp == "") return false;
		
		$query="SELECT DCODE_EXT FROM TBL_CODE_DETAIL WHERE PCODE = 'DELIVERY_CP' AND DCODE = '$delivery_cp' AND USE_TF = 'Y' AND DEL_TF = 'N' ";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0]) {
			return $rows[0];
		} else {
			return false;
		}
	}

	function isHeadAdmin($db, $adm_no) {

		if($adm_no <= 0) return;
		
		$query="SELECT COUNT(*) AS CNT 
							FROM TBL_ADMIN_INFO A, TBL_COMPANY C
						 WHERE A.COM_CODE = C.CP_NO
							 AND C.CP_TYPE = '운영'
							 AND A.DEL_TF = 'N'
							 AND A.USE_TF = 'Y'
							 AND A.ADM_NO = '$adm_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getAdminName($db, $adm_no) {

		if($adm_no <= 0) return;
		$query="SELECT ADM_NAME
				  FROM TBL_ADMIN_INFO
				 WHERE ADM_NO = '$adm_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

	function getAdminPhone($db, $adm_no) {

		if($adm_no <= 0) return;
		
		$query="SELECT ADM_PHONE
				  FROM TBL_ADMIN_INFO
				 WHERE ADM_NO = '$adm_no' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		return $rows[0];
	}

    // 2015-06-22 민성욱 : 영업담당자 영업부에서 SELECT BOX로 가져오기 - company 페이지 
	function makeSaleManagersSelectBox($db, $checkVal){
		
		$query="SELECT ADM_NO, ADM_NAME
							FROM TBL_ADMIN_INFO A, TBL_ADMIN_GROUP G
						    WHERE A.GROUP_NO = G.GROUP_NO
							 AND G.GROUP_NAME = '영업부'
							 AND A.DEL_TF = 'N'
							 AND A.USE_TF = 'Y' ";
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='sale_adm_no' class=\"box01\"  style='width:125px;'>";

		$tmp_str .= "<option value=''>선택</option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$ADM_NO		= Trim($row[0]);
			$ADM_NAME	= Trim($row[1]);

			if ($checkVal == $ADM_NO) {
				$tmp_str .= "<option value='".$ADM_NO."' selected>".$ADM_NAME."</option>";
			} else {
				$tmp_str .= "<option value='".$ADM_NO."'>".$ADM_NAME."</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;

	}

	function getCompanyAutocompleteTextBox($db, $cp_type, $checkVal) {

		if($checkVal <> "")
		{
			$query = "SELECT CP_NO, CP_CODE, CP_NM
									FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
			
			if ($cp_type <> "") {
				$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
			}

			if ($checkVal <> "") {
				$query .= " AND CP_NO = '".$checkVal."' ";
			}
			

			//echo $query; 

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			

			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();
			
			for($i=0 ; $i< $total ; $i++) {

				mysql_data_seek($result,$i);

				$row     = mysql_fetch_array($result);

				$RS_CP_NM			= Trim($row["CP_NM"]);
				$RS_CP_CODE			= Trim($row["CP_CODE"]);
				
				return $RS_CP_NM	." [".$RS_CP_CODE."]";
			}
		}else
			return "";
	}

	function getGoodsAutocompleteTextBox($db, $cate_code, $checkVal) {

		if($checkVal <> "")
		{
			$query = "SELECT GOODS_CODE, GOODS_NAME, IMG_URL
									FROM TBL_GOODS WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";

			
			if ($cp_type <> "") {
				$query .= " AND GOODS_CATE like '".$cate_code."%' ";
			}
			

			if ($checkVal <> "") {
				$query .= " AND GOODS_NO = '".$checkVal."' ";
			}
			
			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);
			

			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();
			
			for($i=0 ; $i< $total ; $i++) {

				mysql_data_seek($result,$i);

				$row     = mysql_fetch_array($result);

				$RS_GOODS_NAME			= Trim($row["GOODS_NAME"]);
				$RS_GOODS_CODE			= Trim($row["GOODS_CODE"]);
				
				return $RS_GOODS_NAME	."[".$RS_GOODS_CODE."]";
			}
		}else
			return "";
	}


	function makeCompanySelectBoxCompanyNameValue($db, $cp_type, $checkVal) {

		$query = "SELECT CP_NO, CP_NM
								FROM TBL_COMPANY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";
		
		if ($cp_type <> "") {
			$query .= " AND CP_TYPE IN ('".$cp_type."','판매공급') ";
		}
		
		$query .= " ORDER BY CP_NM ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='cp_type' class=\"txt\" >";

		$tmp_str .= "<option value=''> 업체선택 </option>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE			= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			if ($checkVal == $RS_DCODE_NM) {
				$tmp_str .= "<option value='".$RS_DCODE_NM."' selected>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_DCODE_NM."'>".$RS_DCODE_NM." [".$RS_DCODE."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//쇼핑몰 _common/common_front_header.php
	function listMainBoard($db, $bb_code, $use_tf, $del_tf, $list_cnt) {

		$query = "SELECT BB_CODE, BB_NO, BB_PO, BB_RE, BB_DE, CATE_01, CATE_02, CATE_03, CATE_04, 
										 WRITER_NM, WRITER_PW, EMAIL, HOMEPAGE, TITLE, HIT_CNT, REF_IP, RECOMM, CONTENTS,
										 FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, KEYWORD, REPLY, REPLY_ADM, REPLY_DATE, REPLY_STATE, COMMENT_TF,
										 USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_BOARD WHERE 1 = 1 ";

		
		if ($bb_code <> "") {
			$query .= " AND BB_CODE = '".$bb_code."' ";
		}

		if ($use_tf <> "") {
			$query .= " AND USE_TF = '".$use_tf."' ";
		}

		if ($del_tf <> "") {
			$query .= " AND DEL_TF = '".$del_tf."' ";
		}

		$query .= " ORDER BY REG_DATE desc limit ".$list_cnt;

		//echo $query."<br>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	//다중 카테고리 구현을 위한 수정 함수 
	function makeCategorySelectBoxOnChangeAdv($db, $elem_name, $checkVal, $exclude_category) {

		$query = "SELECT MAX(LENGTH(CATE_CD)) as MAX
									FROM TBL_CATEGORY WHERE 1 = 1 AND DEL_TF = 'N' AND USE_TF = 'Y' ";

		if ($exclude_category <> "") {
			foreach (explode(",", $exclude_category) as $splited_exclude_category){
				$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
			}
		}

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		$MAX = $rows[0];

		if ($checkVal == "") {
		
			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
									FROM TBL_CATEGORY 
								   WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "            ORDER BY SEQ ASC ";

			//echo $query;
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='".$elem_name."_01' onChange=\"js_gd_cate_01();\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);

				$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
			}
			$tmp_str .= "</select>&nbsp;";


			if ($MAX >= 4) {
				$tmp_str .= "<select name='".$elem_name."_02' onChange=\"js_gd_cate_02();\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 6) {
				$tmp_str .= "<select name='".$elem_name."_03' onChange=\"js_gd_cate_03();\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

			if ($MAX >= 8) {
				$tmp_str .= "<select name='".$elem_name."_04' onChange=\"js_gd_cate_04();\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
				$tmp_str .= "</select>&nbsp;";
			}

		} else {
			
			$cate_01 = substr($checkVal,0,2);
			$cate_02 = substr($checkVal,0,4);
			$cate_03 = substr($checkVal,0,6);
			$cate_04 = substr($checkVal,0,8);

			$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
									FROM TBL_CATEGORY 
								 WHERE 1 = 1 
									 AND DEL_TF = 'N' 
									 AND USE_TF = 'Y'
									 AND LENGTH(CATE_CD) = '2' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "            ORDER BY SEQ ASC ";
	
			$result = mysql_query($query,$db);
			$total  = mysql_affected_rows();

			$tmp_str = "<select name='".$elem_name."_01' onChange=\"js_gd_cate_01();\">";
			$tmp_str .= "<option value=''>1차 카테고리 선택</option>";

			for($i=0 ; $i< $total ; $i++) {
				mysql_data_seek($result,$i);
				$row     = mysql_fetch_array($result);
			
				$RS_CATE_CD		= Trim($row[1]);
				$RS_CATE_NAME	= Trim($row[2]);
				
				if (trim($cate_01) == trim($RS_CATE_CD)) {
					$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
				} else {
					$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
				}
			}
			$tmp_str .= "</select>&nbsp;";
			
			if (strlen($checkVal) >= 2) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									   WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_01."%' 
										 AND LENGTH(CATE_CD) = '4' " ;
			
			if ($exclude_category <> "") {
				foreach (explode(",", $exclude_category) as $splited_exclude_category){
					$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
				}
			}
			
			$query .= "                ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$elem_name."_02' onChange=\"js_gd_cate_02();\">";
				$tmp_str .= "<option value=''>2차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_02) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 4) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_02."%' 
				    					 AND LENGTH(CATE_CD) = '6' " ;
			
				if ($exclude_category <> "") {
					foreach (explode(",", $exclude_category) as $splited_exclude_category){
						$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
					}
				}
			
				$query .= "            ORDER BY SEQ ASC ";

				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$elem_name."_03' onChange=\"js_gd_cate_03();\">";
				$tmp_str .= "<option value=''>3차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_03) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) >= 6) {

				$query = "SELECT CONCAT(CATE_SEQ01,CATE_SEQ02,CATE_SEQ03,CATE_SEQ04) as SEQ, CATE_CD, CATE_NAME
										FROM TBL_CATEGORY 
									 WHERE 1 = 1 
										 AND DEL_TF = 'N' 
										 AND USE_TF = 'Y'
										 AND CATE_CD LIKE '".$cate_03."%' 
										 AND LENGTH(CATE_CD) = '8' " ;
			
				if ($exclude_category <> "") {
					foreach (explode(",", $exclude_category) as $splited_exclude_category){
						$query .= " AND CATE_CD NOT LIKE '".$splited_exclude_category."%' ";
					}
				}
			
				$query .= "            ORDER BY SEQ ASC ";
				$result = mysql_query($query,$db);
				$total  = mysql_affected_rows();

				$tmp_str .= "<select name='".$elem_name."_04' onChange=\"js_gd_cate_04();\">";
				$tmp_str .= "<option value=''>4차 카테고리 선택</option>";

				for($i=0 ; $i< $total ; $i++) {
					mysql_data_seek($result,$i);
					$row     = mysql_fetch_array($result);
			
					$RS_CATE_CD		= Trim($row[1]);
					$RS_CATE_NAME	= Trim($row[2]);
				
					if (trim($cate_04) == trim($RS_CATE_CD)) {
						$tmp_str .= "<option value='".$RS_CATE_CD."' selected>".$RS_CATE_NAME."</option>";
					} else {
						$tmp_str .= "<option value='".$RS_CATE_CD."'>".$RS_CATE_NAME."</option>";
					}
				}
				$tmp_str .= "</select>&nbsp;";
			}

			if (strlen($checkVal) == 2) {
		
				if ($MAX >= 6) {
					$tmp_str .= "<select name='".$elem_name."_03' onChange=\"js_gd_cate_03();\">";
					$tmp_str .= "<option value=''>3차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";

				}

				if ($MAX >= 8) {
					$tmp_str .= "<select name='".$elem_name."_04' onChange=\"js_gd_cate_04();\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}

			}

			if (strlen($checkVal) == 4) {
	

				if ($MAX >= 8) {
					$tmp_str .= "<select name='".$elem_name."_04' onChange=\"js_gd_cate_04();\">";
					$tmp_str .= "<option value=''>4차 카테고리 선택</option>";
					$tmp_str .= "</select>&nbsp;";
				}
			}

		}

		$tmp_str .= "<input type='hidden' name='".$elem_name."' value=''/>";

		return $tmp_str;
	}

	// 세트 계산중
	function getCalcGoodsInOrdering($db, $goods_no) { 

		//단품
		$query ="SELECT  IFNULL(
							SUM(CASE WHEN A.QTY > A.WORK_QTY THEN A.QTY - A.WORK_QTY ELSE 0 END)
							, 0)
				   FROM
						(
						SELECT 
							
								/*IFNULL((
								SELECT  SUM( 
													CASE OGS.ORDER_STATE
													WHEN 6 
													THEN - OGS.QTY
													WHEN 8 
													THEN - OGS.QTY
													ELSE OGS.QTY
													END )
											    
								FROM TBL_ORDER_GOODS OGS
								WHERE OGS.GROUP_NO = OG.ORDER_GOODS_NO
								  AND OGS.USE_TF =  'Y'
								  AND OGS.DEL_TF =  'N'
								  AND OGS.CATE_01 <> '추가'
								  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
								), 0) 

								+ 

								IFNULL((
									SELECT  SUM(OGS.QTY)
									FROM TBL_ORDER_GOODS OGS
									WHERE OGS.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
									  AND OGS.GROUP_NO = 0
									  AND OGS.USE_TF =  'Y'
									  AND OGS.DEL_TF =  'N'
									  AND OGS.CATE_01 <> '추가'
									  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
								), 0)
							
							- IFNULL((SELECT SUM(I.SUB_QTY) FROM TBL_ORDER_GOODS_INDIVIDUAL I WHERE I.IS_DELIVERED = 'Y' AND I.DEL_TF = 'N' AND I.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) , 0)
							AS QTY*/

							IFNULL((
									SELECT  SUM(OGS.QTY)
									FROM TBL_ORDER_GOODS OGS
									WHERE OGS.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
									  AND OGS.GROUP_NO = 0
									  AND OGS.USE_TF =  'Y'
									  AND OGS.DEL_TF =  'N'
									  AND OGS.CATE_01 <> '추가'
									  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
								), 0) AS QTY		/******* 20210908 효곤대리 요청으로 위에 쿼리 주석  **********/

							, OG.WORK_QTY

						FROM TBL_ORDER_GOODS OG
						WHERE OG.USE_TF = 'Y' AND OG.DEL_TF = 'N' 
						AND OG.ORDER_STATE IN (1, 2)
						AND OG.DELIVERY_TYPE NOT IN ('98', '99')
						AND OG.CATE_01 <> '추가'
						AND OG.GOODS_NO = $goods_no
						) A
				 ";

		//echo $query."<br/><br/><br/><br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$single_qty  = $rows[0];

		//세트 구성품
		$query ="SELECT IFNULL(
							SUM(CASE WHEN A.QTY > A.WORK_QTY THEN A.QTY - A.WORK_QTY ELSE 0 END)
							, 0)
						
				   FROM 
					(
						SELECT 
							
								(
									(SELECT IFNULL(SUM( 
									CASE OGS.ORDER_STATE
									WHEN 6 
									THEN - OGS.QTY
									WHEN 8 
									THEN - OGS.QTY
									ELSE OGS.QTY
									END  * GS.GOODS_CNT ), 0) AS QTY
										FROM TBL_ORDER_GOODS OGS
										WHERE OGS.GROUP_NO = OG.ORDER_GOODS_NO
										  AND OGS.USE_TF =  'Y'
										  AND OGS.DEL_TF =  'N'
										  AND OGS.CATE_01 <> '추가'
										  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
									)

									+

									IFNULL(( 
										SELECT SUM(OGS.QTY  * GS.GOODS_CNT)
										FROM TBL_ORDER_GOODS OGS
										WHERE OGS.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
										  AND OGS.GROUP_NO = 0
									  	  AND OGS.USE_TF =  'Y'
										  AND OGS.DEL_TF =  'N'
										  AND OGS.CATE_01 <> '추가'
										  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
									), 0)
								) AS QTY
								
								, (OG.WORK_QTY  * GS.GOODS_CNT) AS WORK_QTY
							
						FROM TBL_ORDER_GOODS OG
						JOIN TBL_GOODS_SUB GS ON OG.GOODS_NO = GS.GOODS_NO
						WHERE GS.GOODS_SUB_NO = $goods_no
						AND OG.USE_TF =  'Y'
						AND OG.DEL_TF =  'N'
						AND OG.CATE_01 <> '추가'
						AND OG.ORDER_STATE IN (1, 2)
						AND OG.DELIVERY_TYPE NOT IN ('98', '99')
					) A
				 ";

		//echo $query."<br/><br/><br/><br/><br/>";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$set_qty  = $rows[0];

		//echo $single_qty." // ".$set_qty."<br/>"; 

		return  intval($single_qty) +  intval($set_qty);

	}


	function getOPCompanyNoByBizNo($db, $biz_no) { 

		if($biz_no == "") return "";

		$query = "SELECT CP_NO 
		            FROM TBL_COMPANY 
				   WHERE BIZ_NO = '$biz_no'
				     AND CP_TYPE = '운영'
				ORDER BY REG_DATE ASC 
					 ";

	   // echo $query."<br>";

		$result = mysql_query($query,$db);

		if(mysql_num_rows($result) == 1){

			$rows   = mysql_fetch_array($result);
			$record  = $rows[0];
			return $record;

		} else {
			return "";
		}
	}

	function getPcodeName($db, $pcode){

		if($pcode == "") return "";

		$query ="SELECT PCODE_NM 
		           FROM TBL_CODE_PARENT 
				  WHERE PCODE = '".$pcode."' ";

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;
	}


	function selectOrderByOrderGoodsNo($db, $order_goods_no) {

		if($order_goods_no <= 0) return null;

		$query = "SELECT ORDER_NO, ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL,
										 R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO,
										 TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, TOTAL_SA_DELIVERY_PRICE,
										 ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, DELIVERY_DATE, FINISH_DATE, 
										 CANCEL_DATE, USE_TF, DEL_TF, REG_ADM, REG_DATE, DEL_ADM, DEL_DATE
								FROM TBL_ORDER 
							WHERE USE_TF= 'Y' AND DEL_TF = 'N' 
							  AND RESERVE_NO IN (SELECT RESERVE_NO FROM TBL_ORDER_GOODS WHERE ORDER_GOODS_NO = '".$order_goods_no."') ";
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

	function getGoodsNoByGoodsCode ($db, $goods_code) {

		if($goods_code == "") return false;

		$query="SELECT GOODS_NO
		          FROM TBL_GOODS 
				 WHERE GOODS_CODE = '$goods_code'
				   AND DEL_TF = 'N'
				   AND USE_TF = 'Y' ";
			
		
		//echo $query;
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);

		if ($rows[0] == 0) {
			return false;
		} else {
			return true;
		}
	}

	function getCompanyNameOnly($db, $cp_no) {
		if (is_numeric($cp_no)) {
			$query = "SELECT CONCAT(CP_NM, ' ', CP_NM2) FROM TBL_COMPANY WHERE CP_NO = '$cp_no' ";
		
			//echo $query;

			$result = mysql_query($query,$db);
			$rows   = mysql_fetch_array($result);

			if ($result <> "") {
				$tmp_str  = $rows[0];
			} else {
				$tmp_str  = "&nbsp;";
			}
		} else {
			$tmp_str  = "&nbsp;";
		}
		return $tmp_str;
	}
	
	function makeAdminSelectBox($db, $objname, $str, $val, $checkVal) {

		$query = "SELECT ADM_ID, ADM_NO, ADM_NAME
								FROM TBL_ADMIN_INFO WHERE 1 = 1 AND USE_TF = 'Y' AND DEL_TF = 'N' ";

		$query .= " ORDER BY ADM_NAME ASC ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."' id='".$objname."' >";

		if ($str <> "") {
			$tmp_str .= "<option value='".$val."'>".$str."</option>";
		}

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row	= mysql_fetch_array($result);
			
			$RS_ADM_ID		= Trim($row[0]);
			$RS_ADM_NO		= Trim($row[1]);
			$RS_ADM_NAME	= Trim($row[2]);

			if ($checkVal == $RS_ADM_NO) {
				$tmp_str .= "<option value='".$RS_ADM_NO."' selected>".$RS_ADM_NAME."[".$RS_ADM_ID."]</option>";
			} else {
				$tmp_str .= "<option value='".$RS_ADM_NO."'>".$RS_ADM_NAME."[".$RS_ADM_ID."]</option>";
			}
		}
		$tmp_str .= "</select>";
		return $tmp_str;
	}

?>