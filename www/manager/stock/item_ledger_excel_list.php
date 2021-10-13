<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : 입출고 관리 > 엑셀 다운로드
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG019"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/stock/stock.php";
	
	
#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_stock_code = "";
	$con_stock_code = trim($con_stock_code);
	$cp_type2 = trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPage = 1;

	$nPageSize = 100000;

	$nPageBlock	= 1000000;
	
#	echo $start_date;
#	echo $end_date;

	//두 상태 다 확인
	//$con_close_tf = "N";

#===============================================================
# Get Search list count
#===============================================================


	$file_name="입출고관리-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );


	if($print_type <> "") { 
		//$order_field = " CASE WHEN A.STOCK_TYPE =  'IN' THEN A.IN_DATE ELSE A.OUT_DATE END "; 
		$order_str = " ASC ";
	}
		

	$arr_rs = listStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>


<? if($print_type == "") { ?>
<TABLE border=1>

	<table cellpadding="0" cellspacing="0" class="rowstable" border="1">
		<colgroup>
			<col width="8%" />
			<col width="6%" />
			<col width="*" />
			<col width="5%" />
			<col width="5%" />
			<col width="5%" />
			<col width="5%" />
			<col width="5%" />
			<col width="7%" />
			<col width="5%" />
			<col width="8%" />
			<col width="5%" />
			<col width="8%" />
			<col width="7%" />
		</colgroup> 
		<thead>
			<tr>
				<th>입/출고일</th>
				<th>재고구분</th>
				<th>상품명</th>
				<th>가입고</th>
				<th>정상입고</th>
				<th>정상출고</th>
				<th>불량입고</th>
				<th>불량출고</th>
				<th>업체명</th>
				<th>사유</th>
				<th>사유상세</th>
				<th>메모</th>
				<th>등록일</th>
				<th class="end">주문번호</th>
			</tr>
		</thead>
		<tbody>
		<?
			$nCnt = 0;
			
			$ALL_IN_QTY = 0;
			$ALL_OUT_QTY = 0;
			$ALL_IN_BQTY = 0;
			$ALL_OUT_BQTY = 0;
			$ALL_IN_FQTY = 0;
			$ALL_OUT_TQTY = 0;

			if (sizeof($arr_rs) > 0) {
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

					$STR_DATE = "";
					$STR_COMPANY = "";
					$STR_STOCK_CODE = "";
					
					$rn								= trim($arr_rs[$j]["rn"]);
					$IN_DATE						= trim($arr_rs[$j]["IN_DATE"]);
					$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
					$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
					$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
					$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
					$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
					$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
					$IN_PRICE						= trim($arr_rs[$j]["IN_PRICE"]);
					$OUT_PRICE						= trim($arr_rs[$j]["OUT_PRICE"]);
					$IN_QTY							= trim($arr_rs[$j]["IN_QTY"]);
					$IN_BQTY						= trim($arr_rs[$j]["IN_BQTY"]);
					$IN_FQTY						= trim($arr_rs[$j]["IN_FQTY"]);
					$OUT_QTY						= trim($arr_rs[$j]["OUT_QTY"]);
					$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
					$OUT_FQTY						= trim($arr_rs[$j]["OUT_FQTY"]);
					$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
					$IN_CP_NO						= trim($arr_rs[$j]["IN_CP_NO"]);
					$OUT_CP_NO						= trim($arr_rs[$j]["OUT_CP_NO"]);
					
					$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
					$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);
					$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
					
					$CLOSE_TF						= trim($arr_rs[$j]["CLOSE_TF"]);
					$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
					
					$IN_DATE			= date("Y-m-d",strtotime($IN_DATE));
					$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
					$REG_DATE			= date("Y-m-d H:i:s",strtotime($REG_DATE));

					//if ($PAY_DATE) $PAY_DATE = date("Y-m-d",strtotime($PAY_DATE));


					if($CLOSE_TF == "Y")
						$closed_style_tr = "class='closed'";
					else
						$closed_style_tr = "";

					if(trim($STOCK_TYPE) == "IN") { 
						$STR_DATE = $IN_DATE;
						$STR_COMPANY = getCompanyNameWithNoCode($conn, $IN_CP_NO);
						$STR_STOCK_CODE = getDcodeName($conn, "IN_ST", $STOCK_CODE);
					} else {
						$STR_DATE = $OUT_DATE;
						$STR_COMPANY = getCompanyNameWithNoCode($conn, $OUT_CP_NO);
						$STR_STOCK_CODE = getDcodeName($conn, "OUT_ST", $STOCK_CODE);
					}

					if($CLOSE_TF != "Y") { 
						$ALL_IN_QTY = $ALL_IN_QTY + $IN_QTY;
						$ALL_OUT_QTY = $ALL_OUT_QTY + $OUT_QTY;
						$ALL_IN_BQTY = $ALL_IN_BQTY + $IN_BQTY;
						$ALL_OUT_BQTY = $ALL_OUT_BQTY + $OUT_BQTY;
						$ALL_IN_FQTY = $ALL_IN_FQTY + $IN_FQTY;
						//$ALL_OUT_TQTY = $ALL_OUT_TQTY + $OUT_TQTY;
					}

					$STR_TITLE = "부분합 : 정상입고 ".$ALL_IN_QTY.", 정상출고 ".$ALL_OUT_QTY.", 불량입고 ".$ALL_IN_BQTY.", 불량출고 ".$ALL_OUT_BQTY." ";
		?>
			<tr height="37" <?=$closed_style_tr ?>  title="<?=$STR_TITLE?>">
				<td><?=$STR_DATE?></td>
				<td><?=$STR_STOCK_CODE ?></td>
				<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
				<td class="price"><?=(startsWith($STOCK_CODE, 'FST') ? number_format($IN_FQTY) : "")?></td>
				<td class="price"><?=(startsWith($STOCK_CODE, 'NST') ? number_format($IN_QTY) : "")?></td>
				<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=(startsWith($STOCK_CODE, 'NOUT') ? number_format($OUT_QTY) : "")?></td>
				<td class="price"><?=(startsWith($STOCK_CODE, 'BST') ? number_format($IN_BQTY) : "")?></td>
				<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=(startsWith($STOCK_CODE, 'BOUT') ? number_format($OUT_BQTY) : "")?></td>
				<td class="modeual_nm"><?=$STR_COMPANY?></td>
				<td><?= getDcodeName($conn, "LOC", $IN_LOC)?></td>
				<td><?=$IN_LOC_EXT?></td>
				<td><?=$MEMO?></td>
				<td><?=$REG_DATE?></td>
				<td><?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?></td>
			</tr>

			<?

				}

			?>

			<!-- 합계 -->
				
				<tr class="goods_end">
					<td colspan="14">
						&nbsp;
					</td>
				</tr>
				<?
					$arr_sum = totalStockInOut($conn, $search_date_type, $start_date, $end_date, '', $search_field, $search_str);
					
					for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
						$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
						$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
						$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
						$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
						$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
						$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
						$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
						$SUM_OUT_FQTY					= trim($arr_sum[$k]["SUM_OUT_FQTY"]);
				?>
				<tr class="goods_end" height="35">
					<td class="filedown" colspan="1">해당 기간 합계</td>
					<td>&nbsp;</td>
					<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
					<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? if($is_total_show) { ?>
					<td><b>정상재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
					<td><b>불량재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
					<? } else { ?>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
					}
				?>
				<tr class="goods_end">
					<td colspan="14">
						&nbsp;
					</td>
				</tr>
				<?
					$arr_sum = totalStockInOut($conn, '', '', '', '', $search_field, $search_str);
					
					for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
						$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
						$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
						$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
						$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
						$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
						$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
						$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
						$SUM_OUT_FQTY					= trim($arr_sum[$k]["SUM_OUT_FQTY"]);
				?>
				<tr class="goods_end" height="35">
					<td class="filedown" colspan="1">전체 기간 합계</td>
					<td>&nbsp;</td>
					<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
					<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? if($is_total_show) { ?>
					<td><b>정상재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
					<td><b>불량재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
					<? } else { ?>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
					}
				?>
			<?

			}else{
				?>
				<tr class="order">
					<td height="50" align="center" colspan="14">데이터가 없습니다. </td>
				</tr>
			<?
				}
			?>
		</tbody>
	</table>
<? } else { ?>
<TABLE border=1>

	<table cellpadding="0" cellspacing="0" class="rowstable" border="1">
		<colgroup>
			<col width="8%" />
			<col width="6%" />
			<col width="*" />
			<col width="7%" />
			<col width="7%" />
			<col width="7%" />
			<col width="12%" />
			<col width="5%" />
			<col width="8%" />
			<col width="5%" />
			<col width="8%" />
			<col width="7%" />
		</colgroup> 
		<thead>
			<tr>
				<th>입/출고일</th>
				<th>재고구분</th>
				<th>상품명</th>
				<th>입고</th>
				<th>출고</th>
				<th>합계</th>
				<th>업체명</th>
				<th>사유</th>
				<th>사유상세</th>
				<th>메모</th>
				<th>등록일</th>
				<th class="end">주문번호</th>
			</tr>
		</thead>
		<? 
			$ALL_IN_QTY = 0;
			$ALL_OUT_QTY = 0;
			$ALL_TOTAL_QTY = 0;

			if (sizeof($arr_rs) > 0) {
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

					$STR_DATE = "";
					$STR_COMPANY = "";
					$STR_STOCK_CODE = "";
					
					$rn								= trim($arr_rs[$j]["rn"]);
					$IN_DATE						= trim($arr_rs[$j]["IN_DATE"]);
					$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
					$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
					$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
					$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
					$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
					$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
					$IN_PRICE						= trim($arr_rs[$j]["IN_PRICE"]);
					$OUT_PRICE						= trim($arr_rs[$j]["OUT_PRICE"]);
					$IN_NQTY						= trim($arr_rs[$j]["IN_QTY"]);
					$IN_BQTY						= trim($arr_rs[$j]["IN_BQTY"]);
					$IN_FQTY						= trim($arr_rs[$j]["IN_FQTY"]);
					$OUT_NQTY						= trim($arr_rs[$j]["OUT_QTY"]);
					$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
					$OUT_TQTY						= trim($arr_rs[$j]["OUT_TQTY"]);
					$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
					$IN_CP_NO						= trim($arr_rs[$j]["IN_CP_NO"]);
					$OUT_CP_NO						= trim($arr_rs[$j]["OUT_CP_NO"]);
					
					$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
					$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);
					$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
					$ORDER_GOODS_NO					= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
					$RGN_NO							= trim($arr_rs[$j]["RGN_NO"]);
					$MEMO							= trim($arr_rs[$j]["MEMO"]);
					
					$CLOSE_TF						= trim($arr_rs[$j]["CLOSE_TF"]);
					$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
					
					$IN_DATE			= date("Y-m-d",strtotime($IN_DATE));
					$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
					$REG_DATE			= date("Y-m-d H:i:s",strtotime($REG_DATE));

					//if ($PAY_DATE) $PAY_DATE = date("Y-m-d",strtotime($PAY_DATE));


					if($CLOSE_TF == "Y")
						$closed_style_tr = "class='closed'";
					else
						$closed_style_tr = "";

					if(trim($STOCK_TYPE) == "IN") { 
						$STR_DATE = $IN_DATE;
						$STR_COMPANY = getCompanyNameWithNoCode($conn, $IN_CP_NO);
						$STR_STOCK_CODE = getDcodeName($conn, "IN_ST", $STOCK_CODE);

						if(startsWith($STOCK_CODE, "F")) 
							$IN_QTY =  $IN_FQTY;
						else if(startsWith($STOCK_CODE, "N")) 
							$IN_QTY =  $IN_NQTY;
						else if(startsWith($STOCK_CODE, "B")) 
							$IN_QTY =  $IN_BQTY;

					} else {
						$STR_DATE = $OUT_DATE;
						$STR_COMPANY = getCompanyNameWithNoCode($conn, $OUT_CP_NO);
						$STR_STOCK_CODE = getDcodeName($conn, "OUT_ST", $STOCK_CODE);

						if(startsWith($STOCK_CODE, "F")) 
							$OUT_QTY =  $OUT_FQTY;
						else if(startsWith($STOCK_CODE, "N")) 
							$OUT_QTY =  $OUT_NQTY;
						else if(startsWith($STOCK_CODE, "B")) 
							$OUT_QTY =  $OUT_BQTY;
					}

					if($CLOSE_TF != "Y") { 
						$ALL_IN_QTY += $IN_QTY;
						$ALL_OUT_QTY += $OUT_QTY;
					}

			if($j == 0) { 
				if($nPage == 1) { 
					$arr_total = listSumStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize, $nListCnt, $option);

					
				} else { 
					$option = array('BASE_DATE' => $STR_DATE, 'STOCK_NO'=> $STOCK_NO);
					$arr_total = listSumStockInOut($conn, $search_date_type, $start_date, $end_date, $con_stock_type, $print_type, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize, $nListCnt, $option);
				}

				if (sizeof($arr_total) > 0 && ($print_type == "F" || $print_type == "N" || $print_type == "B")) {
					for ($o = 0 ; $o < sizeof($arr_total); $o++) {
						$SUM_PREV_QTY = $arr_total[$o]["SUM_PREV_QTY"];

					
			?>
			<tr height="37">
				<td><?=$STR_DATE?></td>
				<td></td>
				<td class="modeual_nm"> 이전재고 </td>
				<td class="price"></td>
				<td class="price"></td>
				<td class="price"><?=number_format($SUM_PREV_QTY)?></td>
				<td class="modeual_nm"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?
					}
				}
			}

			if($CLOSE_TF != "Y") { 
				$SUM_PREV_QTY += ($IN_QTY - $OUT_QTY);
			}

			?>

			
			<tr height="37" <?=$closed_style_tr ?>>
				<td><?=$STR_DATE?></td>
				<td><?=$STR_STOCK_CODE ?></td>
				<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
				<td class="price"><?= number_format($IN_QTY)?></td>
				<td class="price" <?=($CLOSE_TF == "Y" ? "" : "style='color:red;'") ?>><?=number_format($OUT_QTY)?></td>
				<td class="price"><?=number_format($SUM_PREV_QTY)?></td>
				<td class="modeual_nm"><?=$STR_COMPANY?></td>
				<td><?= getDcodeName($conn, "LOC", $IN_LOC)?></td>
				<td><?=$IN_LOC_EXT?></td>
				<td onclick="javascript:js_stock_memo_view('<?=$STOCK_NO?>');"><?=$MEMO?></td>
				<td><?=$REG_DATE?></td>
				<td><?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?></td>
			</tr>
			<?
					$IN_QTY = 0;
					$OUT_QTY = 0;

				}

			?><!-- 합계 -->
				<tr class="goods_end">
					<td colspan="14">
						&nbsp;
					</td>
				</tr>
				<?
					$arr_sum = totalStockInOut($conn, $search_date_type, $start_date, $end_date, '', $search_field, $search_str);
					
					for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
						$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
						$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
						$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
						$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
						$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
						$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
						$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
						$SUM_OUT_FQTY					= trim($arr_sum[$k]["SUM_OUT_FQTY"]);
				?>
				<tr class="goods_end" height="35">
					<td class="filedown" colspan="1">해당 기간 합계</td>
					<td>&nbsp;</td>
					<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
					<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? if($is_total_show) { ?>
					<td><b>정상재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
					<td><b>불량재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
					<? } else { ?>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
					}
				?>
				<tr class="goods_end">
					<td colspan="14">
						&nbsp;
					</td>
				</tr>
				<?
					$arr_sum = totalStockInOut($conn, '', '', '', '', $search_field, $search_str);
					
					for ($k = 0 ; $k < sizeof($arr_sum); $k++) {
						$GOODS_NAME						= SetStringFromDB(trim($arr_sum[$k]["GOODS_NAME"]));
						$GOODS_CODE						= SetStringFromDB(trim($arr_sum[$k]["GOODS_CODE"]));
						$SUM_IN_QTY						= trim($arr_sum[$k]["SUM_IN_QTY"]);
						$SUM_IN_BQTY					= trim($arr_sum[$k]["SUM_IN_BQTY"]);
						$SUM_IN_FQTY					= trim($arr_sum[$k]["SUM_IN_FQTY"]);
						$SUM_OUT_QTY					= trim($arr_sum[$k]["SUM_OUT_QTY"]);
						$SUM_OUT_BQTY					= trim($arr_sum[$k]["SUM_OUT_BQTY"]);
						$SUM_OUT_FQTY					= trim($arr_sum[$k]["SUM_OUT_FQTY"]);
				?>
				<tr class="goods_end" height="35">
					<td class="filedown" colspan="1">전체 기간 합계</td>
					<td>&nbsp;</td>
					<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
					<td class="price"><b><?=number_format($SUM_IN_FQTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_QTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_QTY)?></b></td>
					<td class="price"><b><?=number_format($SUM_IN_BQTY)?></b></td>
					<td class="price"><b style="color:red;"><?=number_format($SUM_OUT_BQTY)?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? if($is_total_show) { ?>
					<td><b>정상재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_QTY - $SUM_OUT_QTY)?></b></td>
					<td><b>불량재고</b></td>
					<td class="price"><b style="color:blue;"><?=number_format($SUM_IN_BQTY - $SUM_OUT_BQTY)?></b></td>
					<? } else { ?>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
					}
				?>
			<?

			}else{
				?>
				<tr class="order">
					<td height="50" align="center" colspan="14">데이터가 없습니다. </td>
				</tr>
			<?
				}
			?>
		</tbody>
	</table>

<? } ?>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>