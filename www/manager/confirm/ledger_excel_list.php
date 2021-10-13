<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/syscode/syscode.php";

	function findRowFromDB($arr, $key, $key_value) {
		for($i = 0; $i < sizeof($arr); $i++) { 
			if($arr[$i][$key] == $key_value)
				return true;
		}
		return false;
	}
#====================================================================
# Request Parameter
#====================================================================

	$file_name="기장 전체 내역-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

#============================================================
# Page process
#============================================================

	$nPage = 1;
	$nPageSize = 1000;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_chk_all = listDcode($conn, 'LIMIT_COMPANY_LEDGER', 'Y', 'N', 'DCODE', '', 1, 1000);

	$nListCnt = totalCntLedger($conn, $search_date_type, $start_date, $end_date, $cp_type, $adm_no, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listLedger($conn, $search_date_type, $start_date, $end_date, $cp_type, $adm_no, $filter, $order_field, $order_str, $search_field, $search_str, $nPage, $nPageSize, $nListCnt);

	
?>
<font size=3><b>기장 전체 내역 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>업체명</th>
		<th align='center' bgcolor='#F4F1EF'>날짜</th>
		<th align='center' bgcolor='#F4F1EF'>구분</th>
		<th align='center' bgcolor='#F4F1EF'>상품명</th>
		<th align='center' bgcolor='#F4F1EF'>수량</th>
		<th align='center' bgcolor='#F4F1EF'>단가</th>
		<th align='center' bgcolor='#F4F1EF'>매출/지급액</th>
		<th align='center' bgcolor='#F4F1EF'>매입/입금액</th>
		<th align='center' bgcolor='#F4F1EF'>부가세</th>
		<th align='center' bgcolor='#F4F1EF'>등록자<br/>등록일</th>
		<th align='center' bgcolor='#F4F1EF'>비고</th>
		<th align='center' bgcolor='#F4F1EF'>보기</th>
	</tr>
	<?
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

				$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
				$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
				$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
				$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
				$NAME						= trim($arr_rs[$j]["NAME"]);
				$QTY						= trim($arr_rs[$j]["QTY"]);
				$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
				$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
				$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
				$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
				$MEMO						= trim($arr_rs[$j]["MEMO"]);
				$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
				$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
				$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);

				$REG_ADM					= trim($arr_rs[$j]["REG_ADM"]);
				$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);

				if($REG_ADM != 0 && $REG_ADM != null)
					$REG_ADM = getAdminName($conn, $REG_ADM);
				else
					$REG_ADM = "자동기장";

				$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));

				$CP_NAME = getCompanyName($conn, $CP_NO);


				$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
				$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

				$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

	?>
	<?
		if(findRowFromDB($arr_chk_all, "DCODE", $CP_NO) && $s_adm_group_no != 1){
	?>
	<tr style="background-color:#ddd; height:30px; text-align:center;">
		<td colspan="12">
			열람권한 없음
		</td>
	</tr>
	<?  } else { ?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CP_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$INOUT_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$INOUT_TYPE?></td>
		<td bgColor='#FFFFFF' align='center'>
			<?=$NAME?>
		</td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($QTY)?></td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($UNIT_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($DEPOSIT)?></td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($WITHDRAW)?></td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($SURTAX)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REG_ADM?> / <?=date("Y-m-d",strtotime($REG_DATE))?></td>
		<td bgColor='#FFFFFF' align='center'><?=$MEMO?></td>
		<td bgColor='#FFFFFF' align='center'>
			<? if($INOUT_TYPE == "매입" || $INOUT_TYPE == "매출") { ?>
				<?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?>
			<? } ?>
		</td>
	</tr>
	<?    }  ?>
	<? 
						
				}

			} else { 
		?>

	<tr height="35">
		<td colspan="12">데이터가 없습니다.</td>
	</tr>

	<? } ?>
</table>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>