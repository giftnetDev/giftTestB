<?session_start();?>
<?
# =============================================================================
# File Name    : goods_price_excel_list.php
# =============================================================================

$file_name="상품가격-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-Description: orion@giringrim.com" );

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD005"; // 메뉴마다 셋팅 해 주어야 합니다

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

	$filter = array("cp_no" => $cp_no);

	$arr_rs = listGoodsPrice($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>
<h3>상품가격 리스트</h3>

<?if($print_type == "") { ?>
<table border="1">
	<tr>
		<th>No.</th>
		<th>상품번호</th>
		<th>상품코드</th>
		<th>상품명</th>
		<th>모델명</th>
		<th>공급사</th>
		<th>박스입수</th>
		<th>매입가</th>
		<th>매입합계</th>
		<th>판매가</th>
		<th>마진(마진률)</th>
		<th>판매업체 마진율</th>
		<th>판매업체 판매가</th>
		<th>판매업체</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn						= trim($arr_rs[$j]["rn"]);
							$SEQ_NO					= trim($arr_rs[$j]["SEQ_NO"]);
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
							$CP_NAME				= trim($arr_rs[$j]["CP_NAME"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$STICKER_PRICE			= trim($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
							$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= trim($arr_rs[$j]["OTHER_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);

							$CP_SALE_SUSU			= trim($arr_rs[$j]["CP_SALE_SUSU"]);
							$CP_SALE_PRICE			= trim($arr_rs[$j]["CP_SALE_PRICE"]);
							

							if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
								$DELIVERY_PER_PRICE = 0;
							else 
								$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
							
							$SUSU_PRICE = round($SALE_PRICE / 100, 0) * $SALE_SUSU;

							$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

							if($SALE_PRICE != 0)
								$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2)."%";
							else 
								$MAJIN_PER = "계산불가";

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>

	<tr>
		<td><?= $rn ?></td>
		<td><?=$GOODS_NO?></td>
		<td><?=$GOODS_CODE?></td>
		<td><?=$GOODS_NAME?></td>
		<td><?=$GOODS_SUB_NAME?></td>
		<td><?= getCompanyName($conn, $CATE_03);?></td>
		<td><?=$DELIVERY_CNT_IN_BOX?></td>
		<td><?= number_format($BUY_PRICE) ?> 원</td>
		<td><?= number_format($PRICE) ?> 원</td>
		<td><?= number_format($SALE_PRICE) ?> 원</td>
		<td><?= number_format($MAJIN) ?> 원(<?=$MAJIN_PER?>)</td>
		<td><?= $CP_SALE_SUSU ?> %</td>
		<td><?= number_format($CP_SALE_PRICE) ?> 원</td>
		<td><?= $CP_NAME ?></td>
	</tr>

				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="14">등록된 내용이 없습니다</td>
	</tr>
				<? 
					}
				?>

</table>
<? } ?>
<? if($print_type == "FOR_REG") { ?>

<table border="1">
	<tr>
		<th style="color:#ff0000;">순번</th>
		<th style="color:#ff0000;">판매업체</th>
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

							$CP_NAME				= trim($arr_rs[$j]["CP_NAME"]);
							
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


				?>
	<tr>
			<td><?= $rn ?></td>
			<td><?=$CP_NAME?></td>								<!--판매업체-->
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
			<td><?=$SALE_SUSU ?></td>							<!--판매수수율-->
			<td><?=$TAX_TF ?></td>								<!--과세구분-->		
			<td><?=$IMG_URL ?></td>								<!--이미지파일URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--이미지경로-->
			<td><?=$FILE_RNM_150 ?></td>						<!--이미지파일명-->
	</tr>

<?	
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
			<td></td>
			<td></td>
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
?>
				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="25">등록된 내용이 없습니다</td>
	</tr>
				<? 
					}
				?>

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