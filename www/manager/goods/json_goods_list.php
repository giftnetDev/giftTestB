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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/biz/goods/goods.php";

	//상품 번호로 검색
	$goods_no = $_REQUEST['goods_no'];

	//상품명으로 검색
	$category = $_REQUEST['category'];
	$search_field = "GOODS_NAME,GOODS_CODE,GOODS_NO";
	$search_str = urldecode (iconv("UTF-8", "EUC-KR", $_REQUEST['term']));
	$search_str	= trim($search_str);

	$category = $_REQUEST['category'];

	if($goods_no != "")
	{

		$arr_rs = getGoodsByNo($conn, $goods_no);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{
			$rs_goods_name = iconv("EUC-KR", "UTF-8", $row['GOODS_NAME']);
			$rs_goods_sub_name = iconv("EUC-KR", "UTF-8", $row['GOODS_SUB_NAME']);
			$rs_goods_name_total = '';
			if($rs_goods_sub_name != '')
				$rs_goods_name_total = $rs_goods_name.'/'.$rs_goods_sub_name;
			else
				$rs_goods_name_total = $rs_goods_name;

			$results .= "{\"GOODS_NAME\":\"".$rs_goods_name_total."\",\"GOODS_CODE\":\"".$row['GOODS_CODE']."\",\"IMG_URL\":\"".$row['IMG_URL']."\"},";
		}
		
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;

	}
	else if($category != "")
	{

		$arr_rs = getGoodsList($conn, $category, $search_field, $search_str);

		$results = "[";
		while($row = mysql_fetch_array($arr_rs))
		{
			$rs_goods_name = iconv("EUC-KR", "UTF-8", $row['GOODS_NAME']);
			$rs_goods_sub_name = iconv("EUC-KR", "UTF-8", $row['GOODS_SUB_NAME']);
			$rs_goods_code = iconv("EUC-KR", "UTF-8", $row['GOODS_CODE']);

			$rs_goods_name_total = $rs_goods_name;
			if($rs_goods_sub_name != '')
				$rs_goods_name_total = $rs_goods_name_total.'/'.$rs_goods_sub_name;

			if($rs_goods_code != '') $goods_code = $rs_goods_code;

			$rs_goods_name_total = $rs_goods_name_total.' ['.$goods_code.']';

			$img_url	= getGoodsImage($row['FILE_NM_100'], $row['IMG_URL'], $row['FILE_PATH_150'], $row['FILE_RNM_150'], "50", "50");

			$results .= "{\"id\":\"".$row['GOODS_NO']."\",\"label\":\"".$img_url."|".$rs_goods_name_total."\",\"value\":\"".$rs_goods_name_total."\"},";
		}
		$results = rtrim($results, ',');
		$results .= "]";

		echo $results;
	}
	else if($goods_code != "")
	{
		$cntGoodsCode = chkDuplicateGoodsCode($conn, $goods_code);
		
		if($serial_part == "")
			$serial_part = substr($goods_code, 4);

		if(strlen($serial_part) >= 4)
			$cntPartlyGoodsCode = chkDuplicatePartlyGoodsCode($conn, $serial_part);
		else
			$cntPartlyGoodsCode = 0;

		$results = "[{\"RESULT\":\"".$cntGoodsCode."\",\"PARTLY\":\"".$cntPartlyGoodsCode."\"}]";
		echo $results;

	}

?>

