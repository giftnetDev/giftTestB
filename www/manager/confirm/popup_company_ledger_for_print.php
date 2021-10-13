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
	$menu_right = "CF006"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "판매" || $s_adm_cp_type == "판매공급") { 
	$cp_type = $s_adm_com_code;
}


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

#====================================================================
# Request Parameter
#====================================================================

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

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs_prev = getCompanyLedgerPreviousMonth($conn, $start_date, $cp_type);

	if($cp_type <> "")
		$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type);

	
	$arr_rs_company = selectCompany($conn, $cp_type);
	
	if(sizeof($arr_rs_company) > 0) { 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=2"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />

<style>
	.row_monthly {background-color:#DFDFDF; font-weight:bold;}
	.row_daily {background-color:#EFEFEF; font-weight:bold;}
	tr.row_tax_confirm > td {/*background-color:#99c1ef;*/ color:blue;} 
	tr.closed > td {background-color:#fff; color: #A2A2A2;} 
</style> 
</head>

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>거래 원장</h1>  
	<div id="postsch">
		<div class="addr_inp">
			<br/>
			<!-- S: mwidthwrap -->
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="250" />
					<col width="120" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tr>
					<th>
						기간
					</th>
					<td colspan="4">
						<?=date("Y년 n월 j일",strtotime($start_date))?>
						 ~ 
						<?=date("Y년 n월 j일",strtotime($end_date))?>
					</td>
				</tr>
				<tr>
					<th>업체명</th>
					<td>
						<?=getCompanyNameWithNoCode($conn, $cp_type)?>
					</td>
					<td colspan="3">

						<b>잔액 : </b><?=getSafeNumberFormatted(getBalance($conn, $cp_type))?>
					</td>
				</tr>
				<tr>
					<th>상세정보</th>
					<td colspan="4"><b>사업자 번호</b> : <?=$rs_biz_no?>,&nbsp;&nbsp;<b>대표자 명</b> : <?=$rs_ceo_nm?>,&nbsp;&nbsp;<b>대표 주소</b> : <?=$rs_cp_zip?><?=$rs_cp_addr?></td>
				</tr>
			</table>
			<div class="sp20"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="10%" />
					<col width="3%" />
					<col width="*"/>
					<col width="3%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="13%" />
				</colgroup>
				<thead>
				<tr>
					<th>날짜</th>
					<th>구분</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>잔액</th>
					<th class="end">비고</th>
				</tr>
				</thead>

				
				<?
					if (sizeof($arr_rs_prev) > 0) {
						for ($k = 0 ; $k < sizeof($arr_rs_prev); $k++) {

							$BALANCE					= trim($arr_rs_prev[$k]["BALANCE"]);
				?>
				<tr height="30">
					<td><?=date("Y-m-d",strtotime("-1 day", strtotime($start_date)))?></td>
					<td></td>
					<td class="modeual_nm"><전기이월></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="price"><?=number_format($BALANCE, 0)?></td>
					<td></td>
				</tr>

				<? }  }  ?>

				<?
					
					$month_group = "";
					$month_qty_total = "";
					$month_withdraw_total = ""; 
					$month_deposit_total = "";
					$month_surtax_total = "";
					$month_balance_total = "";
					

					$day_group = "";
					$day_qty_total = "";
					$day_withdraw_total = ""; 
					$day_deposit_total = "";
					$day_surtax_total = "";
					$day_balance_total = "";

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
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

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							if($USE_TF == "Y")
								$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
							else { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							if($INOUT_TYPE == "매출") { 
								$TAX_TF = getOrderGoodsTaxTF($conn, $ORDER_GOODS_NO);

								if ($TAX_TF == "비과세") {
									$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
								} else {
									$STR_TAX_TF = "<font color='navy'>(과세)</font>";
								}
							} else 
								$STR_TAX_TF = "";


							//일이 변경될 시점에 일계 표시
							if($day_group != date("Y-m-d", strtotime($INOUT_DATE)) && $day_group != "" ) { 
				?>
				<tr height="30" class="row_daily">
					<td class="modeual_nm" colspan="3">일계 : <?=$day_group?></td>
					<td class="price"><?=number_format($day_qty_total)?></td>
					<td></td>
					<td class="price"><?=number_format($day_deposit_total)?></td>
					<td class="price"><?=number_format($day_withdraw_total)?></td>
					<td class="price"><?=number_format($day_surtax_total)?></td>
					<td class="price"><?=number_format($day_balance_total, 0)?></td>
					<td></td>
				</tr>

				<? 

								$day_group = date("Y-m-d",strtotime($INOUT_DATE));
								$day_qty_total = $QTY;
								$day_withdraw_total = $WITHDRAW;
								$day_deposit_total = $DEPOSIT;
								$day_surtax_total = $SURTAX;
								$day_balance_total = $BALANCE;
							} else { 
								
								$day_qty_total += $QTY;
								$day_withdraw_total += $WITHDRAW;
								$day_deposit_total += $DEPOSIT;
								$day_surtax_total += $SURTAX;
								$day_balance_total = $BALANCE;
							}




							//월이 변경될 시점에 월계 표시
							if($month_group != date("Y-m", strtotime($INOUT_DATE)) && $month_group != "" ) { 
				?>
				<tr height="30" class="row_monthly">
					<td class="modeual_nm" colspan="3">월계 : <?=$month_group?></td>
					<td class="price"><?=number_format($month_qty_total)?></td>
					<td></td>
					<td class="price"><?=number_format($month_deposit_total)?></td>
					<td class="price"><?=number_format($month_withdraw_total)?></td>
					<td class="price"><?=number_format($month_surtax_total)?></td>
					<td class="price"><?=number_format($month_balance_total, 0)?></td>
					<td></td>
				</tr>

				<? 

								$month_group = date("Y-m",strtotime($INOUT_DATE));
								$month_qty_total = $QTY;
								$month_withdraw_total = $WITHDRAW;
								$month_deposit_total = $DEPOSIT;
								$month_surtax_total = $SURTAX;
								$month_balance_total = $BALANCE;
							} else { 
								
								$month_qty_total += $QTY;
								$month_withdraw_total += $WITHDRAW;
								$month_deposit_total += $DEPOSIT;
								$month_surtax_total += $SURTAX;
								$month_balance_total = $BALANCE;
							}

							
				?>
				<tr height="30" class="<? if($TAX_CONFIRM_TF == "Y") echo "row_tax_confirm";?> <?if($USE_TF != "Y") echo "closed";?>" title="<? if($TAX_CONFIRM_TF == "Y") echo "발행처리일: ". date("Y년m월d일", strtotime($TAX_CONFIRM_DATE));?>">
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<?=$STR_TAX_TF?>
						<?=$NAME?>
					</td>
					<td class="price"><?=number_format($QTY)?></td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price"><?=number_format($DEPOSIT)?></td>
					<td class="price"><?=number_format($WITHDRAW)?></td>
					<td class="price"><?=number_format($SURTAX)?></td>
					<td class="price"><?=number_format($BALANCE, 0)?></td>
					<td data-cl_no="<?=$CL_NO?>"><?=$MEMO?></td>
					
				</tr>

				<? 
							if($day_group == "")
									$day_group = date("Y-m-d",strtotime($INOUT_DATE));

							if($month_group == "")
									$month_group = date("Y-m",strtotime($INOUT_DATE));

						}

						?>
						<tr height="30" class="row_daily">
							<td class="modeual_nm" colspan="3">일계 : <?=$day_group?></td>
							<td class="price"><?=number_format($day_qty_total)?></td>
							<td></td>
							<td class="price"><?=number_format($day_deposit_total)?></td>
							<td class="price"><?=number_format($day_withdraw_total)?></td>
							<td class="price"><?=number_format($day_surtax_total)?></td>
							<td class="price"><b><?=number_format($day_balance_total, 0)?></b></td>
							<td></td>
						</tr>
						<tr height="30" class="row_monthly">
							<td class="modeual_nm" colspan="3">월계 : <?=$month_group?></td>
							<td class="price"><?=number_format($month_qty_total)?></td>
							<td></td>
							<td class="price"><?=number_format($month_deposit_total)?></td>
							<td class="price"><?=number_format($month_withdraw_total)?></td>
							<td class="price"><?=number_format($month_surtax_total)?></td>
							<td class="price"><b><?=number_format($month_balance_total, 0)?></b></td>
							<td></td>
						</tr>

						<? 

					} else { 
				?>

				<tr height="35">
					<td colspan="11">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
		</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>

</body>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>