<?session_start();?>
<?
# =============================================================================
# File Name    : goods_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$search_field		= "ALL";
	$search_str			= trim($search_name);
	$cp_no					= trim($cp_no);

	//$goods_type			= trim($goods_type);
	
	//if ($goods_type == "unit") $exclude_category = "02";

	
	if($s_adm_cp_type != "운영")
		$exclude_category = "01";

	$con_use_tf = "Y";
	$del_tf = "N";

#============================================================
# Page process
#============================================================

	$nPageBlock	= 10;
	
	$nPage = 1;
	$nPageSize = 50;

	$order_field = "CASE WHEN CATE_04 = '판매중' THEN 0 ELSE 1 END, GOODS_NAME,GOODS_CODE";
	$order_str	 = "ASC";


	$con_cate = "";

	$start_date = "";
	$end_date = "";

#===============================================================
# Get Search list count
#===============================================================

	if($chk_vendor != "Y" && $vendor_calc == "100")
		$vendor_calc = 100;

	$arr_options = array("exclude_category" => $exclude_category, "vendor_calc" => $vendor_calc);

	$nListCnt =totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

	/*
	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	*/
	$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);

	$nCnt = 0;
								
	if (sizeof($arr_rs) > 0) {

		$str_title = $nListCnt."|";
									
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
			$rn					= trim($arr_rs[$j]["rn"]);
			$GOODS_NO			= trim($arr_rs[$j]["GOODS_NO"]);
			$GOODS_CODE			= trim($arr_rs[$j]["GOODS_CODE"]);
			$GOODS_NAME			= trim($arr_rs[$j]["GOODS_NAME"]);
			$GOODS_SUB_NAME		= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
			$GOODS_CATE			= trim($arr_rs[$j]["GOODS_CATE"]);
			$CATE_03			= trim($arr_rs[$j]["CATE_03"]);
			$CATE_04			= trim($arr_rs[$j]["CATE_04"]);
			$PRICE				= trim($arr_rs[$j]["PRICE"]);
			$BUY_PRICE			= trim($arr_rs[$j]["BUY_PRICE"]);
			$SALE_PRICE			= trim($arr_rs[$j]["SALE_PRICE"]);
			$IMG_URL			= trim($arr_rs[$j]["IMG_URL"]);
			$FILE_NM			= trim($arr_rs[$j]["FILE_NM_100"]);
			$FILE_RNM			= trim($arr_rs[$j]["FILE_RNM_100"]);
			$FILE_PATH			= trim($arr_rs[$j]["FILE_PATH_100"]);
			$FILE_SIZE			= trim($arr_rs[$j]["FILE_SIZE_100"]);
			$FILE_EXT			= trim($arr_rs[$j]["FILE_EXT_100"]);
			$FILE_NM_150		= trim($arr_rs[$j]["FILE_NM_150"]);
			$FILE_RNM_150		= trim($arr_rs[$j]["FILE_RNM_150"]);
			$FILE_PATH_150		= trim($arr_rs[$j]["FILE_PATH_150"]);
			$FILE_SIZE_150		= trim($arr_rs[$j]["FILE_SIZE_150"]);
			$FILE_EXT_150		= trim($arr_rs[$j]["FILE_EXT_150"]);

			$STOCK_CNT			= trim($arr_rs[$j]["STOCK_CNT"]);
			$FSTOCK_CNT			= trim($arr_rs[$j]["FSTOCK_CNT"]);
			$BSTOCK_CNT			= trim($arr_rs[$j]["BSTOCK_CNT"]);
			$MEMO				= trim($arr_rs[$j]["MEMO"]);

			$MEMO = str_replace("\r"," ",$MEMO);
			$MEMO = str_replace("\n"," ",$MEMO);


			$DELIVERY_CNT_IN_BOX= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

			$COM_NAME	= getCompanyName($conn, $CATE_03);

			$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

			$ORIGINAL_SALE_PRICE = $SALE_PRICE;

			if ($cp_no <> "") {
				$SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $cp_no, $dc_rate);
			}

			//   구분자 js_select()
			//(arr_keywordValues[0]); // 상품명
			//(arr_keywordValues[1]); // 상품번호
			//(arr_keywordValues[2]); // 매입가
			//(arr_keywordValues[3]); // 판매가(벤더 정보 있을경우 벤더 할인가)
			//(arr_keywordValues[4]); // 상품코드 (경박)
			//(arr_keywordValues[5]); // 업체번호
			//(arr_keywordValues[6]); // 업체이름 [업체코드 (경박)]
			//(arr_keywordValues[7]); // 정상재고
			//(arr_keywordValues[8]); // 불량제고
			//(arr_keywordValues[9]); // 가재고
			//(arr_keywordValues[10]); // 상품카테고리
			//(arr_keywordValues[11]); // 박스입수
			//(arr_keywordValues[12]); // 모델명
			//(arr_keywordValues[13]); // 원판매가(기프트넷 단가 - 벤더 없을경우 판매가)
			//(arr_keywordValues[14]); // 판매상태
			

			//   구분자 display_result()
			//(arr_keywordList[1]); // 상품명 [상품코드 (경박)]
			//(arr_keywordList[2]); // 상품이미지
			//(arr_keywordList[3]); // 판매가
			//(arr_keywordList[4]); // 정상재고
			//(arr_keywordList[5]); // 불량제고
			//(arr_keywordList[6]); // 가재고
			//(arr_keywordList[7]); // 매입가
			//(arr_keywordList[8]); // 판매상태
			//(arr_keywordList[9]); // 박스입수
			//(arr_keywordList[10]); // 원 판매가(벤더 할인 있을경우)
			//(arr_keywordList[11]); // 상품비고 



			if ($j == 0) {
				$str_title = $str_title.$GOODS_NAME."".$GOODS_NO."".$BUY_PRICE."".$SALE_PRICE."".$GOODS_CODE."".$CATE_03."".$COM_NAME."".$STOCK_CNT."".$BSTOCK_CNT."".$FSTOCK_CNT."".$GOODS_CATE."".$DELIVERY_CNT_IN_BOX."".$GOODS_SUB_NAME."".$ORIGINAL_SALE_PRICE."".$CATE_04."".$GOODS_NAME." [".$GOODS_CODE."]".$img_url."".getSafeNumberFormatted($SALE_PRICE)."".getSafeNumberFormatted($STOCK_CNT)."".getSafeNumberFormatted($BSTOCK_CNT)."".getSafeNumberFormatted($FSTOCK_CNT)."".getSafeNumberFormatted($BUY_PRICE)."".$CATE_04."".$DELIVERY_CNT_IN_BOX."".getSafeNumberFormatted($ORIGINAL_SALE_PRICE)."".$MEMO;
			} else {
				$str_title = $str_title."^".$GOODS_NAME."".$GOODS_NO."".$BUY_PRICE."".$SALE_PRICE."".$GOODS_CODE."".$CATE_03."".$COM_NAME."".$STOCK_CNT."".$BSTOCK_CNT."".$FSTOCK_CNT."".$GOODS_CATE."".$DELIVERY_CNT_IN_BOX."".$GOODS_SUB_NAME."".$ORIGINAL_SALE_PRICE."".$CATE_04."".$GOODS_NAME." [".$GOODS_CODE."]".$img_url."".getSafeNumberFormatted($SALE_PRICE)."".getSafeNumberFormatted($STOCK_CNT)."".getSafeNumberFormatted($BSTOCK_CNT)."".getSafeNumberFormatted($FSTOCK_CNT)."".getSafeNumberFormatted($BUY_PRICE)."".$CATE_04."".$DELIVERY_CNT_IN_BOX."".getSafeNumberFormatted($ORIGINAL_SALE_PRICE)."".$MEMO;
			}
		}
	}
#====================================================================
# DB Close
#====================================================================
	
	$str_title = str_replace("'","&#39;",$str_title);
	$str_title = str_replace("\"","&quot;",$str_title);


	mysql_close($conn);
?>
<script Type="Text/JavaScript">
	<?
		if($mode == "") {
	?>	
		parent.displayResult('<?=$str_title?>');
	<?
		} else {	
	?>
		parent.displayResult('<?=$str_title?>', '<?=$mode?>');
	<? 
		}
	?>
</script>
