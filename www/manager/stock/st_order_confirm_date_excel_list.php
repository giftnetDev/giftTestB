<?session_start();?>
<?
# =============================================================================
# File Name    : order_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG005"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/payment/payment.php";


#====================================================================
# Request Parameter
#====================================================================


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

	if ($confirm_ymd == "") {
		$confirm_ymd = date("Y-m-d",strtotime("0 month"));
	} else {
		$confirm_ymd = trim($confirm_ymd);
	}

	$file_name="입고 정산 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listBuyStConfirmList($conn, $start_date, $end_date, $cp_type, $order_field, $order_str);

	$arr_rs_all = listBuyStConfirmAll($conn, $start_date, $end_date, $cp_type);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 입고 업체 정산 상세 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
				<? if ($s_adm_cp_type == "운영") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>정산일</td>
		<td align='center' bgcolor='#F4F1EF'>공급업체</td>
		<td align='center' bgcolor='#F4F1EF'>결재은행</td>
		<td align='center' bgcolor='#F4F1EF'>계좌번호</td>
		<td align='center' bgcolor='#F4F1EF'>연락처</td>
		<td align='center' bgcolor='#F4F1EF'>총매입가</td>
	</tr>
					<? } ?>

				<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>정산일</td>
		<td align='center' bgcolor='#F4F1EF'>결재은행</td>
		<td align='center' bgcolor='#F4F1EF'>계좌번호</td>
		<td align='center' bgcolor='#F4F1EF'>연락처</td>
		<td align='center' bgcolor='#F4F1EF'>총매입가</td>
	</tr>
					<? } ?>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							/*
							AA.CONFIRM_YMD, AA.BUY_CP_NO, BB.CP_NM, BB.ACCOUNT_BANK, BB.ACCOUNT, BB.AD_TYPE, BB.CP_PHONE,
							SUM(AA.BUY_PRICE * AA.QTY) ALL_BUY_PRICE, 
							SUM(AA.SALE_PRICE * AA.QTY) ALL_SALE_PRICE,
							SUM(AA.EXTRA_PRICE * AA.QTY) ALL_EXTRA_PRICE,
							SUM(AA.SA_DELIVERY_PRICE * AA.QTY) ALL_SA_DELIVERY_PRICE,
							(SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) AS PLUS_PRICE,
							ROUND(((SUM(AA.SALE_PRICE * AA.QTY) - SUM(AA.BUY_PRICE * AA.QTY)) / SUM(AA.SALE_PRICE * AA.QTY) * 100),2) AS LEE
							*/

							$CONFIRM_YMD						= trim($arr_rs[$j]["CONFIRM_YMD"]);
							$BUY_CP_NO							= trim($arr_rs[$j]["BUY_CP_NO"]);
							$CP_NM									= trim($arr_rs[$j]["CP_NM"]);
							$ACCOUNT_BANK						= trim($arr_rs[$j]["ACCOUNT_BANK"]);
							$ACCOUNT								= trim($arr_rs[$j]["ACCOUNT"]);
							
							$ALL_BUY_PRICE					= trim($arr_rs[$j]["ALL_BUY_PRICE"]);

							$str_price_class = "price";
							$str_state_class = "state";
							
							if ($s_adm_cp_type == "운영") {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CONFIRM_YMD?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT_BANK?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_PHONE?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_BUY_PRICE)?></td>
	</tr>
						<?
							}

							if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CONFIRM_YMD?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT_BANK?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_PHONE?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_BUY_PRICE)?></td>
	</tr>
						<?
							}

						}
					}else{
						?>
	<tr>
		<td height="50" align="center" colspan="5">데이터가 없습니다. </td>
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