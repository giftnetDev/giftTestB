<?session_start();?>
<?
# =============================================================================
# File Name    : delivery_excel_list.php
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
	$menu_right = "OD015"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/payment/payment.php";

    $file_name = '';
	if($mode == 'emart') {
		$file_name="이마트 집하리스트-".$specific_date.".xls";
	} else if($mode == 'homeplus') {
		$file_name="홈플러스 집하리스트-".$specific_date.".xls";
	} else if($mode == 'lotte') {
		$file_name="롯데마트 집하리스트-".$specific_date.".xls";
	} else
		$file_name = '';
	  
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<? if($mode == 'emart') { ?>
			<TABLE border=1>
				<tr>
					<td>no</td>
					<td>배송번호</td>
					<td>배송유형상세</td>
					<td>택배사</td>
					<td>송장번호</td>
					<td>중량정보</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForEmart($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn			            = trim($arr_rs[$j]["rn"]);                 //no
							$CP_DELIVERY_NO			= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]); //배송번호
							$DELIVERY_TYPE_DETAIL   = '22';                                    //배송유형상세
							$DELIVERY_CP_CODE	    = '0000033073';                            //택배사
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //송장번호
							$WEIGHT_INFO			= '';                                      //중량정보 - 국내배송시 필요없음

						?>
						
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$rn?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CP_DELIVERY_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_TYPE_DETAIL?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_CP_CODE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$WEIGHT_INFO?></td>
						</tr>
						
					<?
						}
					 }else {
					?>
						<tr class="order">
							<td height="50" align="center" colspan="6">데이터가 없습니다. </td>
						</tr>
					<?
					 }
					?>
			</table>
<?  } ?>

<? if($mode == 'homeplus') { ?>
			<TABLE border=1>
				<tr>
					<td>주문번호</td>
					<td>택배사</td>
					<td>운송장번호</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForHomeplus($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);        //주문번호
							$DELIVERY_CP_CODE	    = '27230';                                 //택배사
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //운송장번호

						?>
						
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_CP_CODE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_NO?></td>
						</tr>
						
					<?
						}
					 }else {
					?>
						<tr class="order">
							<td height="50" align="center" colspan="3">데이터가 없습니다. </td>
						</tr>
					<?
					 }
					?>
			</table>
<?  } ?>


<? if($mode == 'lotte') { ?>
			<TABLE border=1>
				<tr>
					<td>주문번호</td>
					<td>운송장번호</td>
					<td>택배사코드</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForLotte($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);        //주문번호
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //운송장번호
							$DELIVERY_CP_CODE	    = '04';                                    //택배사코드

						?>
						
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_CP_CODE?></td>
						</tr>
						
					<?
						}
					 }else {
					?>
						<tr class="order">
							<td height="50" align="center" colspan="3">데이터가 없습니다. </td>
						</tr>
					<?
					 }
					?>
			</table>
<?  } ?>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>