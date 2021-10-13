<?session_start();?>
<?
# =============================================================================
# File Name    : goods_list.php
# =============================================================================

$file_name="상품-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
header( "Content-Disposition: attachment; filename=$file_name" );

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
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

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-6 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPageBlock	= 10;
	
	$nPage = 1;
	$nPageSize = 100000;

#===============================================================
# Get Search list count
#===============================================================
	$this_date = date("Ymd",strtotime("0 day"));

	if($chk_vendor != "Y" && $txt_vendor_calc == "100")
		$txt_vendor_calc = 100;

	$arr_options = array("exclude_category" => $con_exclude_category, "txt_vendor_calc" => $txt_vendor_calc);

	if(startsWith($print_type, "FOR_CATALOG")) { 
		$arr_rs = listGoodsWithPage($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);
	} else { 
		$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);
		
		//상품리스트에 MRO판매가(TBL_GOODS_PRICE.MRO_SALE_PRICE) 추가
		if(sizeof($arr_rs)>0){
			for($i=0;$i<sizeof($arr_rs);$i++){
				$TEMP_GOODS_NO = trim($arr_rs[$i]["GOODS_NO"]);
				$arr_rs[$i]["MRO_SALE_PRICE"] = getMroSalePrice($conn, $TEMP_GOODS_NO);
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>
<!-- List -->
<? if(startsWith($print_type, "FOR_REG")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">상품번호</th>
		<th style="color:#ff0000;">상품카테고리</th>
		<th style="color:#ff0000;">상품명</th>
		<th style="color:#ff0000;">모델명</th>
		<th style="color:#ff0000;">상품코드</th>
		<th style="color:#ff0000;">구성품수량</th>
		<th style="color:#ff0000;">박스입수</th>
		<th style="color:#ff0000;">최소재고</th>
		<th style="color:#ff0000;">제조사</th>
		<th style="color:#ff0000;">공급사</th>
		<th style="color:#ff0000;">판매상태</th>
		<th style="color:#ff0000;">매입가</th>
		<th style="color:#ff0000;">스티커비용</th>
		<th style="color:#ff0000;">포장인쇄비용</th>
		<th style="color:#ff0000;">택배 배송비</th>
		<th style="color:#ff0000;">인건비</th>
		<th style="color:#ff0000;">기타비용</th>
		<th style="color:#ff0000;">판매가</th>
		<th style="color:#ff0000;">MRO판매가</th>
		<th style="color:#ff0000;">판매수수율</th>
		<th style="color:#ff0000;">과세구분</th>
		<th style="color:#ff0000;">이미지파일URL</th>
		<th style="color:#ff0000;">이미지경로</th>
		<th style="color:#ff0000;">이미지파일명</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}


	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--상품번호(시스템)-->
			<td><?=$GOODS_CATE ?></td>							<!--상품카테고리-->
			<td><?=$GOODS_NAME ?></td>							<!--상품명-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--모델명-->
			<td><?=$GOODS_CODE ?></td>							<!--상품코드-->
			<td><?=$CATE_01	?></td>								<!--구성품수량(빈값)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--박스입수-->
			<td><?=$MSTOCK_CNT ?></td>							<!--최소재고-->
			<td><?=$CATE_02	?></td>								<!--제조사-->
		    <td><?=getCompanyCodeAsNo($conn, $CATE_03)?></td>	<!--공급사-->
			<td><?=$CATE_04 ?></td>								<!--판매상태-->
			<td><?=$BUY_PRICE ?></td>							<!--매입가-->				
			<td><?=$STICKER_PRICE ?></td>						<!--스티커비용-->
			<td><?=$PRINT_PRICE ?></td>							<!--포장인쇄비용-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--택배 배송비-->
			<td><?=$LABOR_PRICE ?></td>							<!--인건비-->
			<td><?=$OTHER_PRICE ?></td>							<!--기타비용-->
			<td><?=$SALE_PRICE ?></td>							<!--판매가-->
			<td><?=$MRO_SALE_PRICE ?></td>						<!--MRO판매가-->
			<td><?=$SALE_SUSU ?></td>							<!--판매수수율-->
			<td><?=$TAX_TF ?></td>								<!--과세구분-->		
			<td><?=$IMG_URL ?></td>								<!--이미지파일URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--이미지경로-->
			<td><?=$FILE_RNM_150 ?></td>						<!--이미지파일명-->
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>		
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--매입가-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>

<? } ?>
<!------------------------------------------------------------------------------------------------------------------------->

<? if(startsWith($print_type, "FOR_PRINT")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">상품번호</th>
		<th style="color:#ff0000;">상품카테고리</th>
		<th style="color:#ff0000;">상품명</th>
		<th style="color:#ff0000;">모델명</th>
		<th style="color:#ff0000;">상품코드</th>
		<th style="color:#ff0000;">구성품수량</th>
		<th style="color:#ff0000;">박스입수</th>
		<th style="color:#ff0000;">최소재고</th>
		<th style="color:#ff0000;">제조사</th>
		<th style="color:#ff0000;">공급사</th>
		<th style="color:#ff0000;">판매상태</th>
		<th style="color:#ff0000;">매입가</th>
		<th style="color:#ff0000;">스티커비용</th>
		<th style="color:#ff0000;">포장인쇄비용</th>
		<th style="color:#ff0000;">택배 배송비</th>
		<th style="color:#ff0000;">물류비</th>
		<th style="color:#ff0000;">인건비</th>
		<th style="color:#ff0000;">기타비용</th>
		<th style="color:#ff0000;">매입합계</th>
		<th style="color:#ff0000;">판매가</th>
		<th style="color:#ff0000;">MRO판매가</th>
		<th style="color:#ff0000;">벤더15%</th>
		<th style="color:#ff0000;">벤더35%</th>
		<th style="color:#ff0000;">벤더55%</th>
		<? if ($chk_vendor == "Y") { ?>
			<th style="color:#ff0000;">벤더할인가</th>
		<? } ?>
		<th style="color:#ff0000;">판매수수율</th>
		<th style="color:#ff0000;">판매수수료</th>
		<th style="color:#ff0000;">마진</th>
		<th style="color:#ff0000;">마진율</th>
		<th style="color:#ff0000;">다음적용 판매가</th>
		<th style="color:#ff0000;">과세구분</th>
		<th style="color:#ff0000;">이미지파일URL</th>
		<th style="color:#ff0000;">이미지경로</th>
		<th style="color:#ff0000;">이미지파일명</th>
		<th style="color:#ff0000;">정상재고</th>
		<th style="color:#ff0000;">불량재고</th>
		
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$NEXT_SALE_PRICE		= trim($arr_rs[$j]["NEXT_SALE_PRICE"]); 

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);


				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);


				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "계산불가";

				$VENDER_15_PRICE = ceiling((($SALE_PRICE - $PRICE) * 15 / 100.0 + $PRICE), -1);
				$VENDER_35_PRICE = ceiling((($SALE_PRICE - $PRICE) * 35 / 100.0 + $PRICE), -1);
				$VENDER_55_PRICE = ceiling((($SALE_PRICE - $PRICE) * 55 / 100.0 + $PRICE), -1);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--상품번호(시스템)-->
			<td>
			<?
				$max_index = 0;
				while($max_index <= strlen($GOODS_CATE)) {
							
					if($max_index > 2)
						echo " > ";
					echo getCategoryNameOnly($conn, left($GOODS_CATE, $max_index));

					$max_index += 2;

				}
			?></td>							<!--상품카테고리-->
			<td><?=$GOODS_NAME ?></td>							<!--상품명-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--모델명-->
			<td><?=$GOODS_CODE ?></td>							<!--상품코드-->
			<td><?=$CATE_01	?></td>								<!--구성품수량(빈값)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--박스입수-->
			<td><?=$MSTOCK_CNT ?></td>							<!--최소재고-->
			<td><?=$CATE_02	?></td>								<!--제조사-->
		    <td><?=getCompanyName($conn, $CATE_03)?></td>		<!--공급사-->
			<td><?=$CATE_04 ?></td>								<!--판매상태-->
			<td><?=$BUY_PRICE ?></td>							<!--매입가-->				
			<td><?=$STICKER_PRICE ?></td>						<!--스티커비용-->
			<td><?=$PRINT_PRICE ?></td>							<!--포장인쇄비용-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--택배 배송비-->
			<td><?=$DELIVERY_PER_PRICE ?></td>					<!--물류비-->
			<td><?=$LABOR_PRICE ?></td>							<!--인건비-->
			<td><?=$OTHER_PRICE ?></td>							<!--기타비용-->
			<td><?=$PRICE ?></td>								<!--매입합계-->
			<td><?=$SALE_PRICE ?></td>							<!--판매가-->
			<td><?=$MRO_SALE_PRICE ?></td>						<!--MRO판매가-->
			<td><?=$VENDER_15_PRICE ?></td>						<!--벤더15가-->
			<td><?=$VENDER_35_PRICE ?></td>						<!--벤더35가-->
			<td><?=$VENDER_55_PRICE ?></td>						<!--벤더55가-->
			<? if ($chk_vendor == "Y") { ?>
				<td><?=$VENDER_PRICE ?></td>
			<? } ?>
			<td><?=$SALE_SUSU ?></td>							<!--판매수수율-->
			<td><?=$SUSU_PRICE ?></td>							<!--판매수수료-->
			<td><?=$MAJIN ?></td>								<!--마진-->
			<td><?=$MAJIN_PER ?></td>							<!--마진률-->
			<td><?=$NEXT_SALE_PRICE ?></td>						<!--다음적용 판매가-->
			<td><?=$TAX_TF ?></td>								<!--과세구분-->		
			<td><?=$IMG_URL ?></td>								<!--이미지파일URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--이미지경로-->
			<td><?=$FILE_RNM_150 ?></td>						<!--이미지파일명-->
			<td><?=$STOCK_CNT ?></td>							<!--정상재고-->
			<td><?=$BSTOCK_CNT ?></td>							<!--불량재고-->
			
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>		
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--매입가-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<? if ($chk_vendor == "Y") { ?>
			<td></td>
			<? }?>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>


<? } ?>
<!-------------------------------------------------------------------------------------------------------------------------->
<? if(startsWith($print_type, "FOR_CATALOG")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">상품번호</th>
		<th style="color:#ff0000;">검색카테고리</th>
		<th style="color:#ff0000;">페이지</th>
		<th style="color:#ff0000;">순서</th>
		<th style="color:#ff0000;">상품명</th>
		<th style="color:#ff0000;">모델명</th>
		<th style="color:#ff0000;">상품코드</th>
		<th style="color:#ff0000;">구성품수량</th>
		<th style="color:#ff0000;">박스입수</th>
		<th style="color:#ff0000;">최소재고</th>
		<th style="color:#ff0000;">제조사</th>
		<th style="color:#ff0000;">공급사</th>
		<th style="color:#ff0000;">판매상태</th>
		<th style="color:#ff0000;">매입가</th>
		<th style="color:#ff0000;">스티커비용</th>
		<th style="color:#ff0000;">포장인쇄비용</th>
		<th style="color:#ff0000;">택배 배송비</th>
		<th style="color:#ff0000;">인건비</th>
		<th style="color:#ff0000;">기타비용</th>
		<th style="color:#ff0000;">판매가</th>
		<? if ($chk_vendor == "Y") { ?>
			<th style="color:#ff0000;">벤더할인가</th>
		<? } ?>
		<th style="color:#ff0000;">판매수수율</th>
		<th style="color:#ff0000;">과세구분</th>
		<th style="color:#ff0000;">이미지파일URL</th>
		<th style="color:#ff0000;">이미지경로</th>
		<th style="color:#ff0000;">이미지파일명</th>
		
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}


	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--상품번호(시스템)-->
			<td>
			<?
				$max_index = 0;
				while($max_index <= strlen($SEARCH_CATE)) {
							
					if($max_index > 2)
						echo " > ";
					echo getCategoryNameOnly($conn, left($SEARCH_CATE, $max_index));

					$max_index += 2;

				}
			?>
			</td>
			<td><?=$PAGE ?></td>								<!--페이지-->
			<td><?=$SEQ ?></td>									<!--순서-->
			<td><?=$GOODS_NAME ?></td>							<!--상품명-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--모델명-->
			<td><?=$GOODS_CODE ?></td>							<!--상품코드-->
			<td><?=$CATE_01	?></td>								<!--구성품수량(빈값)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--박스입수-->
			<td><?=$MSTOCK_CNT ?></td>							<!--최소재고-->
			<td><?=$CATE_02	?></td>								<!--제조사-->
		    <td><?=getCompanyCodeAsNo($conn, $CATE_03)?></td>	<!--공급사-->
			<td><?=$CATE_04 ?></td>								<!--판매상태-->
			<td><?=$BUY_PRICE ?></td>							<!--매입가-->				
			<td><?=$STICKER_PRICE ?></td>						<!--스티커비용-->
			<td><?=$PRINT_PRICE ?></td>							<!--포장인쇄비용-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--택배 배송비-->
			<td><?=$LABOR_PRICE ?></td>							<!--인건비-->
			<td><?=$OTHER_PRICE ?></td>							<!--기타비용-->
			<td><?=$SALE_PRICE ?></td>							<!--판매가-->
			<? if ($chk_vendor == "Y") { ?>
				<td><?=$VENDER_PRICE ?></td>
			<? } ?>
			<td><?=$SALE_SUSU ?></td>							<!--판매수수율-->
			<td><?=$TAX_TF ?></td>								<!--과세구분-->		
			<td><?=$IMG_URL ?></td>								<!--이미지파일URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--이미지경로-->
			<td><?=$FILE_RNM_150 ?></td>						<!--이미지파일명-->
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>	
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--매입가-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<? if ($chk_vendor == "Y") { ?>
			<td></td>
			<? } ?>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>


<? } ?>
<!-------------------------------------------------------------------------------------------------------->
<? if(startsWith($print_type, "DISPLAY")) { ?>
<table border="1">
	<? if($view_type == "price") { ?>
	<tr>
		<th style="color:#ff0000;">상품번호</th>
		<th style="color:#ff0000;">상품코드</th>
		<th style="color:#ff0000;">상품명</th>
		<th style="color:#ff0000;">공급사</th>
		<th style="color:#ff0000;">매입가</th>
		<th style="color:#ff0000;">매입합계</th>
		<th style="color:#ff0000;">판매가</th>
		<th style="color:#ff0000;">마진</th>
		<th style="color:#ff0000;">마진율</th>
		<th style="color:#ff0000;">박스입수</th>
		<th style="color:#ff0000;">재고</th>
		<th style="color:#ff0000;">판매상태</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);
				
				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "계산불가";

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

	?>
	<tr>
		<td><?=$GOODS_NO ?></td>									<!--상품번호(시스템)-->
		<td><?=$GOODS_CODE ?></td>									<!--상품코드-->
		<td><?=$GOODS_NAME ?> <?=$GOODS_SUB_NAME ?></td>			<!--상품명-->
		<td><?=getCompanyName($conn, $CATE_03)?></td>				<!--공급사-->
		<td><?=$BUY_PRICE ?></td>									<!--매입가-->		
		<td><?=$PRICE ?></td>										<!--매입합계-->	
		<td>														<!--판매가-->
		<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
			<?= number_format($SALE_PRICE) ?> 원
		<? } else { ?>
			<?= number_format($SALE_PRICE) ?> 원
			<? if ($chk_vendor == "Y") {?>
				(<?= number_format($VENDER_PRICE)?> 원)
			<? } ?>
			<?  if($chk_next_sale_price == "Y") { 
					if($NEXT_SALE_PRICE == "") { 
			?>
				(미정)
			<?	    } else { ?>
				(<?= getSafeNumberFormatted($NEXT_SALE_PRICE) ?> 원)
			<?      }
				}
			?>
		<? } ?>
		</td>														
		<td><?=$MAJIN ?></td>										<!--마진-->
		<td><?=$MAJIN_PER ?></td>									<!--마진율-->		
		<td><?=$MRO_SALE_PRICE ?></td>								<!--MRO판매가-->		
		<td><?=$DELIVERY_CNT_IN_BOX ?></td>							<!--박스입수-->
		<td><?=$STOCK_CNT ?></td>									<!--재고-->
		<td><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></td><!--판매상태-->
	</tr>
	<?		}
		}
	?>

	<? } ?>
	<? if($view_type == "stock") { ?>
	<tr>
		<th style="color:#ff0000;">상품번호</th>
		<th style="color:#ff0000;">상품코드</th>
		<th style="color:#ff0000;">상품명</th>
		<th style="color:#ff0000;">박스입수</th>
		<th style="color:#ff0000;">최소재고</th>
		<th style="color:#ff0000;">선출고</th>
		<th style="color:#ff0000;">가재고</th>
		<th style="color:#ff0000;">정상재고</th>
		<th style="color:#ff0000;">불량재고</th>
		<th style="color:#ff0000;">가용재고</th>
		<th style="color:#ff0000;">판매상태</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);
				
				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "계산불가";

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

				$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);

	?>
	<tr>
		<td><?=$GOODS_NO ?></td>									<!--상품번호(시스템)-->
		<td><?=$GOODS_CODE ?></td>									<!--상품코드-->
		<td><?=$GOODS_NAME ?> <?=$GOODS_SUB_NAME ?></td>			<!--상품명-->
		<td><?=$DELIVERY_CNT_IN_BOX ?></td>							<!--박스입수-->
		<td><?=$MSTOCK_CNT ?></td>									<!--최소재고-->		
		<td><?= ($TSTOCK_CNT > 0 ? "-".$TSTOCK_CNT : 0) ?></td>		<!--선출고-->	
		<td><?=$FSTOCK_CNT ?></td>									<!--가입고-->
		<td><?=$STOCK_CNT ?></td>									<!--정상재고-->
		<td><?=$BSTOCK_CNT ?></td>									<!--불량재고-->
		<td><?=($STOCK_CNT + $FSTOCK_CNT - $TSTOCK_CNT) ?></td>		<!--가용재고-->
		<td><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></td><!--판매상태-->
	</tr>
	<?		}
		}
	?>

	<? } ?>
</table>
<? } ?>
<!-- //List -->
			
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
