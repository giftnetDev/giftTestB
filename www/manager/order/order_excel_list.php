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
//	$menu_right = "SP009"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/payment/payment.php";

	$file_name="�ֹ���������Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	
	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";

	if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "�Ǹ�"  ) { 
		$cp_type = $s_adm_com_code;
	}


	$arr_rs = listManagerOrder($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_all = listAllOrder($conn, $start_date, $end_date, $sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf, $del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>
<body>
<font size=3><b><?=$Admin_shop_name?> �ֹ� ����Ʈ </b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>

<? if ($s_adm_cp_type == "�") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�ֹ���ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>��ü��</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�����ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�Ѱ��ް�</td>
		<td align='center' bgcolor='#F4F1EF'>���ǸŰ�</td>
		<td align='center' bgcolor='#F4F1EF'>�ѹ�ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�Ѽ���</td>
		<td align='center' bgcolor='#F4F1EF'>�߰���ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�հ�</td>
		<td align='center' bgcolor='#F4F1EF'>���Ǹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>���Ǹ�������</td>
		<td align='center' bgcolor='#F4F1EF'>�������</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��Ͻ�</td>
		<td align='center' bgcolor='#F4F1EF'>&nbsp;</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼ�</td>
		<td align='center' bgcolor='#F4F1EF'>���ް�</td>
		<td align='center' bgcolor='#F4F1EF'>�ǸŰ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>����</td>
		<td align='center' bgcolor='#F4F1EF'>��Ÿ ��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�Ұ�</td>
		<td align='center' bgcolor='#F4F1EF'>�Ǹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>�Ǹ�������</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>��û��</td>
		<td align='center' bgcolor='#F4F1EF'>ó����</td>
		<td align='center' bgcolor='#F4F1EF'>��۸޸�</td>
		<td align='center' bgcolor='#F4F1EF'>��ü�ֹ���ȣ</td>
	</tr>
<? } ?>

<? if ($s_adm_cp_type == "�Ǹ�" ) { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�ֹ���ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�����ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�ǸŰ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�߰���ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>����</td>
		<td align='center' bgcolor='#F4F1EF'>�հ�</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��Ͻ�</td>
		<td align='center' bgcolor='#F4F1EF'>��û��</td>
		<td align='center' bgcolor='#F4F1EF'>ó����</td>
		<td align='center' bgcolor='#F4F1EF'>��۸޸�</td>
	</tr>
<? } ?>

<? if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�ֹ���ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�����ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>���ް�</td>
		<td align='center' bgcolor='#F4F1EF'>��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�߰���ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>����</td>
		<td align='center' bgcolor='#F4F1EF'>�հ�</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��Ͻ�</td>
		<td align='center' bgcolor='#F4F1EF'>��û��</td>
		<td align='center' bgcolor='#F4F1EF'>ó����</td>
		<td align='center' bgcolor='#F4F1EF'>��۸޸�</td>
	</tr>
<? } ?>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn										= trim($arr_rs[$j]["rn"]);
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
							$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
							$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
							
							$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
							//$ORDER_STATE					= trim($arr_rs[$j]["ORDER_STATE"]);
							$PAY_STATE						= trim($arr_rs[$j]["PAY_STATE"]);
							$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
							$TOTAL_BUY_PRICE			= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
							$TOTAL_SALE_PRICE			= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
							$TOTAL_EXTRA_PRICE		= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
							$TOTAL_QTY						= trim($arr_rs[$j]["TOTAL_QTY"]);
							$TOTAL_DELIVERY_PRICE	= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
							
							$TOTAL_PRICE					= trim($arr_rs[$j]["TOTAL_PRICE"]);
							$TOTAL_PLUS_PRICE			= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
							$LEE									= trim($arr_rs[$j]["LEE"]);
							
							$ORDER_DATE						= trim($arr_rs[$j]["ORDER_DATE"]);
							$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
							$CANCEL_DATE					= trim($arr_rs[$j]["CANCEL_DATE"]);

							$DELIVERY_TYPE			= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							$MEMO								= trim($arr_rs[$j]["MEMO"]);
							
							$ORDER_DATE		= date("Y-m-d H:i:s",strtotime($ORDER_DATE));
							
							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$GOODS_NAME					= SetStringFromDB(trim($arr_goods[$h]["GOODS_NAME"]));
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);
									$DELIVERY_PRICE			= trim($arr_goods[$h]["DELIVERY_PRICE"]);
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY								= trim($arr_goods[$h]["QTY"]);
									$ORDER_DATE					= trim($arr_goods[$h]["ORDER_DATE"]);
									$REQ_DATE						= trim($arr_goods[$h]["PAY_DATE"]);
									$END_DATE						= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);

									$GOODS_OPTION_01		= trim($arr_goods[$h]["GOODS_OPTION_01"]);
									$GOODS_OPTION_02		= trim($arr_goods[$h]["GOODS_OPTION_02"]);
									$GOODS_OPTION_03		= trim($arr_goods[$h]["GOODS_OPTION_03"]);
									$GOODS_OPTION_04		= trim($arr_goods[$h]["GOODS_OPTION_04"]);
									$GOODS_OPTION_NM_01	= trim($arr_goods[$h]["GOODS_OPTION_NM_01"]);
									$GOODS_OPTION_NM_02	= trim($arr_goods[$h]["GOODS_OPTION_NM_02"]);
									$GOODS_OPTION_NM_03	= trim($arr_goods[$h]["GOODS_OPTION_NM_03"]);
									$GOODS_OPTION_NM_04	= trim($arr_goods[$h]["GOODS_OPTION_NM_04"]);

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

									if ($ORDER_DATE <> "")  {
										$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($END_DATE <> "")  {
										$END_DATE		= date("Y-m-d H:i",strtotime($END_DATE));
									}
									

									if ($h == (sizeof($arr_goods)-1)) {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1_end'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3_end'";
										} else {
											$str_tr = "class='goods_end'";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "class='goods_1'";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "class='goods_3'";
										} else {
											$str_tr = "class='goods'";
										}
									}
									

									$str_price_class = "price";
									$str_state_class = "state";
									
									$STR_QTY = number_format($QTY);

									if (($ORDER_STATE == "4") || ($ORDER_STATE == "6") || ($ORDER_STATE == "7") || ($ORDER_STATE == "8")) {
										
										if ($ORDER_STATE == "4") {
											
											$BUY_PRICE = 0;
											$SALE_PRICE = 0;
											$EXTRA_PRICE = 0;
											$STR_QTY = "[".number_format($QTY)."]";
											$SUM_PRICE = 0;
											$PLUS_PRICE = 0;
											$GOODS_LEE = 0;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;
											$str_price_class = "price_cancel";
											$str_state_class = "state_cancel";

										} else {
											$BUY_PRICE = -$BUY_PRICE;
											$SALE_PRICE = -$SALE_PRICE;
											$EXTRA_PRICE = -$EXTRA_PRICE;
											$STR_QTY = number_format(-$QTY);
											$SUM_PRICE = -$SUM_PRICE;
											$PLUS_PRICE = -$PLUS_PRICE;
											$GOODS_LEE = - $GOODS_LEE;

											$REQ_DATE = $ORDER_DATE;
											$END_DATE = $ORDER_DATE;

											$str_price_class = "price_refund";
											$str_state_class = "state_refund";
										}
									} 
									
									if ($s_adm_cp_type == "�") {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='center'><?= getCompanyName($conn, $CP_NO);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($TOTAL_PLUS_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$LEE?>%</td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "PAY_TYPE", $PAY_TYPE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$ORDER_DATE?></td>
		<td bgColor='#FFFFFF' align='center'>&nbsp;</td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='left'><?=$option_str?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$STR_QTY?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($PLUS_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$GOODS_LEE?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REQ_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$END_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$MEMO?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CP_ORDER_NO?></td>
	</tr>
						<?
									}

									if ($s_adm_cp_type == "�Ǹ�" ) {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$STR_QTY?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$ORDER_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REQ_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$END_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$MEMO?></td>
	</tr>
						<?
									}

									if ($s_adm_cp_type == "����" || $s_adm_cp_type == "�ǸŰ���") {
						?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$STR_QTY?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format(($BUY_PRICE * $QTY) + ($EXTRA_PRICE * $QTY) + $DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF' align='center'><?=$ORDER_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REQ_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$END_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$MEMO?></td>
	</tr>
						<?
									}

								}
							}
						?>
						<?
						}

						if (sizeof($arr_rs_all) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
								$ALL_BUY_PRICE			= trim($arr_rs_all[$j]["ALL_BUY_PRICE"]);
								$ALL_SALE_PRICE			= trim($arr_rs_all[$j]["ALL_SALE_PRICE"]);
								$ALL_EXTRA_PRICE		= trim($arr_rs_all[$j]["ALL_EXTRA_PRICE"]);
								$ALL_STR_QTY				= trim($arr_rs_all[$j]["ALL_QTY"]);
								$ALL_DELIVERY_PRICE	= trim($arr_rs_all[$j]["ALL_DELIVERY_PRICE"]);
								$ALL_SUM_PRICE			= trim($arr_rs_all[$j]["ALL_SUM_PRICE"]);
								$ALL_PLUS_PRICE			= trim($arr_rs_all[$j]["ALL_PLUS_PRICE"]);
								$ALL_GOODS_LEE			= trim($arr_rs_all[$j]["ALL_LEE"]);
							}
						}
						
						if ($s_adm_cp_type == "�") {
					?>

					<!-- �հ� -->
	<tr>
		<td colspan="15">
			&nbsp;
		</td>
	</tr>

	<tr>
		<td bgColor='#FFFFFF' align='center'>�� ��</td>
		<td bgColor='#FFFFFF' align='left' colspan='3'>&nbsp;</td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_BUY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$ALL_STR_QTY?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ALL_PLUS_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$ALL_GOODS_LEE?></td>
		<td bgColor='#FFFFFF' align='center'>&nbsp;</td>
		<td bgColor='#FFFFFF' align='center'>&nbsp;</td>
		<td bgColor='#FFFFFF' align='center'>&nbsp;</td>
	</tr>

					<?
						}

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="15">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
</table>


</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>