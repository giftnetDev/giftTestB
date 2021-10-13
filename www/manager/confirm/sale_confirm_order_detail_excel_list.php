<?session_start();?>
<?
# =============================================================================
# File Name    : confirm_order_detail_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2011.01.13
# Modify Date  : 
#	Copyright : Copyright @orion. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CF004"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/order/order.php";

	$file_name="�Ǹ� ��ü ���� �� ����Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
#====================================================================
# DML Process
#====================================================================

#====================================================================
# Request Parameter
#====================================================================

	$use_tf = "Y";
	$del_tf = "N";
#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================
	//echo $p_confirm_ymd;
	//echo $p_buy_cp_no;

	$arr_rs = listSaleConfirmCpOrderList($conn, $p_confirm_ymd, $p_cp_no, $use_tf, $del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> �Ǹ� ��ü ���� �� ����Ʈ </b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�ֹ���ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>�����ڸ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>�ǸŰ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>�߰���ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>����</td>
		<td align='center' bgcolor='#F4F1EF'>�հ�</td>
		<td align='center' bgcolor='#F4F1EF'>3�ڹ���</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>�Ϸ��Ͻ�</td>
	</tr>
				<?
					$nCnt = 0;

					$SUM_PRICE = 0;
					$TOT_SUM_PRICE = 0;
					$TOT_QTY	= 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$O_MEM_NM						= trim($arr_rs[$j]["O_MEM_NM"]);
							$R_MEM_NM						= trim($arr_rs[$j]["R_MEM_NM"]);
							
							$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_OPTION_NAME	= trim($arr_rs[$j]["GOODS_OPTION_NAME"]);
							
							$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
							$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);
							$QTY								= trim($arr_rs[$j]["QTY"]);
							$SA_DELIVERY_PRICE	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
							$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
							$FINISH_DATE				= trim($arr_rs[$j]["FINISH_DATE"]);

							$FINISH_DATE = date("Y-m-d",strtotime($FINISH_DATE));

							//$SUM_PRICE = ($SALE_PRICE * $QTY) + ($EXTRA_PRICE * $QTY);
							$SUM_PRICE = ($SALE_PRICE * $QTY);
							$TOT_SUM_PRICE = $TOT_SUM_PRICE + $SUM_PRICE;
							$TOT_QTY = $TOT_QTY + $QTY;
							$TOT_SA_DELIVERY_PRICE = $TOT_SA_DELIVERY_PRICE + $SA_DELIVERY_PRICE;
				
				?>
	<tr>
		<td bgColor='#FFFFFF'><?=$RESERVE_NO?></td>
		<td bgColor='#FFFFFF'><?=$O_MEM_NM?></td>
		<td bgColor='#FFFFFF'><?=$R_MEM_NM?></td>
		<td bgColor='#FFFFFF'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SALE_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($EXTRA_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($QTY)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SUM_PRICE)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($SA_DELIVERY_PRICE)?></td>
		<td bgColor='#FFFFFF'><?=getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);?></td>
		<td bgColor='#FFFFFF'><?= $FINISH_DATE ?></td>
	</tr>
				<?
						}
					}
				?>
	<tr>
		<td bgcolor='#F4F1EF'>�հ�</td>
		<td bgcolor='#F4F1EF' colspan="6">&nbsp;</td>
		<td bgcolor='#F4F1EF' align='right'><?=number_format($TOT_QTY)?></td>
		<td bgcolor='#F4F1EF' align='right'><?=number_format($TOT_SUM_PRICE)?></td>
		<td bgcolor='#F4F1EF' align='right'><?=number_format($TOT_SA_DELIVERY_PRICE)?></td>
		<td bgcolor='#F4F1EF' align='right'>&nbsp;</td>
		<td bgcolor='#F4F1EF'>&nbsp;</td>
	</tr>
</table>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>