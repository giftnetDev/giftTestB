<?session_start();?>
<?
# =============================================================================
# File Name    : payment_excel_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/payment/payment.php";

	$file_name="입금관리리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );


#====================================================================
# Request Parameter
#====================================================================

	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";
	
	$arr_rs = listPayment($conn, $start_date, $end_date, $sel_pay_type, $cp_type, $sel_pay_state, $sel_account_bank, $reserve_no, $mem_no, $sel_pay_reason, $cash_receipt_state, $con_use_tf, $del_tf, $order_field, $order_str, $search_field, $search_str, $condition, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>
<body>
<font size=3><b><?=$Admin_shop_name?> 입금 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>판매업체</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>입금인</td>
		<td align='center' bgcolor='#F4F1EF'>주문인</td>
		<td align='center' bgcolor='#F4F1EF'>전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>연락처</td>
		<td align='center' bgcolor='#F4F1EF'>총주문액</td>
		<td align='center' bgcolor='#F4F1EF'>입금액</td>
		<td align='center' bgcolor='#F4F1EF'>은행</td>
		<td align='center' bgcolor='#F4F1EF'>상태</td>
		<td align='center' bgcolor='#F4F1EF'>결재방법</td>
		<td align='center' bgcolor='#F4F1EF'>주문일</td>
		<td align='center' bgcolor='#F4F1EF'>입금일</td>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$RESERVE_NO				= trim($arr_rs[$j]["RESERVE_NO"]);
							$PAY_NO						= trim($arr_rs[$j]["PAY_NO"]);
							$PAY_REASON				= trim($arr_rs[$j]["PAY_REASON"]);
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$TOTAL_PRICE			= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$O_MEM_NM					= trim($arr_rs[$j]["O_MEM_NM"]);
							$O_PHONE					= trim($arr_rs[$j]["O_PHONE"]);
							$O_HPHONE					= trim($arr_rs[$j]["O_HPHONE"]);
							
							$PAY_EXT					= trim($arr_rs[$j]["PAY_EXT"]);
							$PAY_TYPE					= trim($arr_rs[$j]["PAY_TYPE"]);
							$PAY_STATE				= trim($arr_rs[$j]["PAY_STATE"]);
							$MEM_NM						= trim($arr_rs[$j]["MEM_NM"]);
							$NM								= trim($arr_rs[$j]["NM"]);
							$CMS_AMOUNT				= trim($arr_rs[$j]["CMS_AMOUNT"]);
							$CMS_PAY_BANK			= trim($arr_rs[$j]["CMS_PAY_BANK"]);
							$CMS_PAY_ACCOUNT	= trim($arr_rs[$j]["CMS_PAY_ACCOUNT"]);
							$CMS_DEPOSITOR		= trim($arr_rs[$j]["CMS_DEPOSITOR"]);
							$BANK_AMOUNT			= trim($arr_rs[$j]["BANK_AMOUNT"]);
							$BANK_PAY_ACCOUNT	= trim($arr_rs[$j]["BANK_PAY_ACCOUNT"]);
							$BANK_PAY_DATE		= trim($arr_rs[$j]["BANK_PAY_DATE"]);

							$BANK_AMOUNT			= trim($arr_rs[$j]["BANK_AMOUNT"]);
							$CARD_AMOUNT			= trim($arr_rs[$j]["CARD_AMOUNT"]);
							$PGBANK_AMOUNT		= trim($arr_rs[$j]["PGBANK_AMOUNT"]);
							$RESERVE_NO				= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_DATE				= trim($arr_rs[$j]["ORDER_DATE"]);
							$REQ_DATE					= trim($arr_rs[$j]["REQ_DATE"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
							$PAID_DATE				= trim($arr_rs[$j]["PAID_DATE"]);
							$CANCEL_DATE			= trim($arr_rs[$j]["CANCEL_DATE"]);
							
							if ($MEM_TYPE == "") {
								$str_mem_type = "비회원";
								$MEM_NM = $NM;
							} else {
								$str_mem_type = getDcodeName($conn, "MEM_TYPE", $MEM_TYPE);
							}

							$amount = "0";
							
							$amount = ($CMS_AMOUNT + $BANK_AMOUNT + $CARD_AMOUNT + $PGBANK_AMOUNT);

							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));

							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));

							if (($REQ_DATE == "") || ($REQ_DATE == "0000-00-00 00:00:00")) { 
								$REQ_DATE		= "&nbsp;";
							} else {
								$REQ_DATE		= date("Y-m-d",strtotime($REQ_DATE));
							}

							if (($PAID_DATE == "") || ($PAID_DATE == "0000-00-00 00:00:00")) { 
								$PAID_DATE		= "&nbsp;";
							} else {
								$PAID_DATE		= date("Y-m-d",strtotime($PAID_DATE));
							}

							if (($CANCEL_DATE == "") || ($CANCEL_DATE == "0000-00-00 00:00:00")) { 
								$CANCEL_DATE		= "&nbsp;";
							} else {
								$CANCEL_DATE	= date("Y-m-d",strtotime($CANCEL_DATE));
							}
				
							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>공개</font>";
							} else {
								$STR_USE_TF = "<font color='red'>비공개</font>";
							}

							
							$str_pay_account = "ACCOUNT_BANK";

							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");
							
							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									if ($h == 0) {
										$str_gooods_name = $GOODS_NAME;
									} else {
										$str_gooods_name = $GOODS_NAME."외 ".$h."건";
									}
								}
							}

							
						?>
	<tr>
		<td align='center' bgColor='#FFFFFF'><?=$RESERVE_NO?></td>
		<td align='left' bgColor='#FFFFFF'><?= getCompanyName($conn, $CP_NO);?></td>
		<td align='left' bgColor='#FFFFFF'><?=$str_gooods_name?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$CMS_DEPOSITOR?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$O_MEM_NM?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$O_PHONE?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$O_HPHONE?></td>
		<td align='right' bgcolor='#FFFFFF'><?=number_format($TOTAL_PRICE)?></td>
		<td align='right' bgcolor='#FFFFFF'><?=number_format($amount)?></td>
		<td align='left' bgcolor='#FFFFFF'><?=getDcodeName($conn, $str_pay_account, $BANK_PAY_ACCOUNT)?></td>
		<td align='center' bgcolor='#FFFFFF'><?=getDcodeName($conn, "PAY_STATE", $PAY_STATE);?></td>
		<td align='center' bgcolor='#FFFFFF'><?=getDcodeName($conn, "PAY_TYPE", $PAY_TYPE);?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$ORDER_DATE?></td>
		<td align='center' bgcolor='#FFFFFF'><?=$PAID_DATE?></td>
	</tr>
						<?
						}
					}else{
						?>
						<tr>
							<td height="50" align="center" colspan="12">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
	</table>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>