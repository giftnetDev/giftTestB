<?session_start();?>
<?
# =============================================================================
# File Name    : st_order_list.php
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
	$menu_right = "SG004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	
	//echo $s_adm_cp_type;
	//echo $s_adm_com_code;

	if ($s_adm_cp_type == "구매") { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	if ($confirm_ymd == "") {
		$confirm_ymd = date("Y-m-d",strtotime("0 month"));;
	} else {
		$confirm_ymd = trim($confirm_ymd);
	}

	//echo $s_adm_cp_type;
	//echo $s_adm_com_code;

	$file_name="입고 정산 상세 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	
	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$arr_rs = listStConfirmOrder($conn, $start_date, $end_date, $sel_confirm_tf, $cp_type2, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 공급 업체 정산 상세 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>결제일</td>
		<td align='center' bgcolor='#F4F1EF'>업체명</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>옵션</td>
		<td align='center' bgcolor='#F4F1EF'>매입가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>합계</td>
		<td align='center' bgcolor='#F4F1EF'>입고등록일</td>
		<td align='center' bgcolor='#F4F1EF'>정산여부</td>
		<td align='center' bgcolor='#F4F1EF'>정산일</td>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn									= trim($arr_rs[$j]["rn"]);
							$ORDER_GOODS_NO			= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$GOODS_NAME					= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
							$CONFIRM_TF					= trim($arr_rs[$j]["CONFIRM_TF"]);
							$CONFIRM_DATE				= trim($arr_rs[$j]["CONFIRM_DATE"]);

							if (($CONFIRM_TF == "N") || ($CONFIRM_TF == "") ) {
								$CONFIRM_DATE		= "";
								$str_confirm = "<font color = 'gray'>미정산</font>";
							} else {
								$CONFIRM_DATE		= date("Y-m-d H:i",strtotime($CONFIRM_DATE));
								$str_confirm = "<font color = 'navy'>정산</font>";
							}

							$GOODS_OPTION_01		= trim($arr_rs[$j]["GOODS_OPTION_01"]);
							$GOODS_OPTION_02		= trim($arr_rs[$j]["GOODS_OPTION_02"]);
							$GOODS_OPTION_03		= trim($arr_rs[$j]["GOODS_OPTION_03"]);
							$GOODS_OPTION_04		= trim($arr_rs[$j]["GOODS_OPTION_04"]);
							$GOODS_OPTION_NM_01	= trim($arr_rs[$j]["GOODS_OPTION_NM_01"]);
							$GOODS_OPTION_NM_02	= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]);
							$GOODS_OPTION_NM_03	= trim($arr_rs[$j]["GOODS_OPTION_NM_03"]);
							$GOODS_OPTION_NM_04	= trim($arr_rs[$j]["GOODS_OPTION_NM_04"]);

							$option_str = "";

							if ($GOODS_OPTION_NM_01 <> "") {
								$option_str .= $GOODS_OPTION_NM_01." : ".$GOODS_OPTION_01."&nbsp;";
							}

							if ($GOODS_OPTION_NM_02 <> "") {
								$option_str .= $GOODS_OPTION_NM_02." : ".$GOODS_OPTION_02."&nbsp;";
							}

							if ($GOODS_OPTION_NM_03 <> "") {
								$option_str .= $GOODS_OPTION_NM_03." : ".$GOODS_OPTION_03."&nbsp;";
							}

							if ($GOODS_OPTION_NM_04 <> "") {
								$option_str .= $GOODS_OPTION_NM_04." : ".$GOODS_OPTION_04."&nbsp;";
							}

							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$ORDER_DATE		= date("Y-m-d",strtotime($ORDER_DATE));
							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));
							
				?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$PAY_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?= getCompanyName($conn, $BUY_CP_NO);?></td>
		<td bgColor='#FFFFFF' align='left'><?= $GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='left'><?= $option_str?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE * $QTY)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REG_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$str_confirm?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CONFIRM_DATE?></td>
	</tr>
					<?
									
						}
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