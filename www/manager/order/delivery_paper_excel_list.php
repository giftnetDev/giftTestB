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
	$menu_right = "OD016"; // �޴����� ���� �� �־�� �մϴ�

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

	$file_name="�߼ۼ��帮��Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	$nPage = 1;
	$nPageSize = 1000000;

	$arr_rs = listDeliveryPaper($conn, $start_date, $end_date, $cp_type, $delivery_cp,  $delivery_fee_code, $delivery_claim_code, $isSent, $withoutDeliveryNo, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b> ���� ����Ʈ </b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>����ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�����ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�Ǹ�ó</td>
		<td align='center' bgcolor='#F4F1EF'>�Ǹ�ó��ȭ��ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ����̸�</td>
		<td align='center' bgcolor='#F4F1EF'>������</td>
		<td align='center' bgcolor='#F4F1EF'>��������ȭ��ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�������ڵ�����ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�������ּ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>�޸�</td>
		<? if($print_type != "out") { ?>
		<td align='center' bgcolor='#F4F1EF'>����Ÿ��</td>
		<td align='center' bgcolor='#F4F1EF'>���Ŭ����</td>
		<td align='center' bgcolor='#F4F1EF'>�����</td>
		<td align='center' bgcolor='#F4F1EF'>�����</td>
		<? } ?>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$ORDER_GOODS_DELIVERY_NO	= trim($arr_rs[$j]["ORDER_GOODS_DELIVERY_NO"]);
				$DELIVERY_CNT	= trim($arr_rs[$j]["DELIVERY_CNT"]);
				$SEQ_OF_DELIVERY	= trim($arr_rs[$j]["SEQ_OF_DELIVERY"]);
				$DELIVERY_SEQ	= trim($arr_rs[$j]["DELIVERY_SEQ"]);
				$SEQ_OF_DAY	= trim($arr_rs[$j]["SEQ_OF_DAY"]);

				$RECEIVER_NM	= trim($arr_rs[$j]["RECEIVER_NM"]);
				$RECEIVER_PHONE	= trim($arr_rs[$j]["RECEIVER_PHONE"]);
				$RECEIVER_HPHONE	= trim($arr_rs[$j]["RECEIVER_HPHONE"]);
				$RECEIVER_ADDR	= trim($arr_rs[$j]["RECEIVER_ADDR"]);
				$ORDER_QTY	= trim($arr_rs[$j]["ORDER_QTY"]);

				$MEMO	= trim($arr_rs[$j]["MEMO"]);
				$ORDER_NM	= trim($arr_rs[$j]["ORDER_NM"]);
				$ORDER_PHONE	= trim($arr_rs[$j]["ORDER_PHONE"]);
				$ORDER_MANAGER_NM	= trim($arr_rs[$j]["ORDER_MANAGER_NM"]);
				$ORDER_MANAGER_PHONE	= trim($arr_rs[$j]["ORDER_MANAGER_PHONE"]);

				$PAYMENT_TYPE	= trim($arr_rs[$j]["PAYMENT_TYPE"]);
				$SEND_CP_ADDR	= trim($arr_rs[$j]["SEND_CP_ADDR"]);
				$GOODS_DELIVERY_NAME	= trim($arr_rs[$j]["GOODS_DELIVERY_NAME"]);
				$DELIVERY_CP	= trim($arr_rs[$j]["DELIVERY_CP"]);
				$DELIVERY_NO	= trim($arr_rs[$j]["DELIVERY_NO"]);
				
				$DELIVERY_TYPE	= trim($arr_rs[$j]["DELIVERY_TYPE"]);
				$DELIVERY_DATE	= trim($arr_rs[$j]["DELIVERY_DATE"]);
				$DELIVERY_FEE	= trim($arr_rs[$j]["DELIVERY_FEE"]);
				$DELIVERY_FEE_CODE	= trim($arr_rs[$j]["DELIVERY_FEE_CODE"]);
				$DELIVERY_CLAIM_CODE	= trim($arr_rs[$j]["DELIVERY_CLAIM_CODE"]);
				$DELIVERY_CLAIM =  getDcodeName($conn, 'DELIVERY_CLAIM', $DELIVERY_CLAIM_CODE);

				$USE_TF	= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF	= trim($arr_rs[$j]["DEL_TF"]);
				$REG_ADM	= trim($arr_rs[$j]["REG_ADM"]);

				$REG_DATE	= trim($arr_rs[$j]["REG_DATE"]);
				$OUTSTOCK_TF	= trim($arr_rs[$j]["OUTSTOCK_TF"]);
			?>
	<tr>
		<td  bgColor='#FFFFFF' align='right'><?=$DELIVERY_SEQ?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$DELIVERY_NO?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$ORDER_MANAGER_NM?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$ORDER_MANAGER_PHONE?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$ORDER_NM?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$RECEIVER_NM?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$RECEIVER_PHONE?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$RECEIVER_HPHONE?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$RECEIVER_ADDR?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$GOODS_DELIVERY_NAME?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$MEMO?></td>
		<? if($print_type != "out") { ?>
		<td  bgColor='#FFFFFF' align='right'><?=$DELIVERY_FEE?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$DELIVERY_CLAIM?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$REG_DATE?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$DELIVERY_DATE?></td>
		<? } ?>
	</tr>
		<?
					}
				} else { 
		?>
			<tr height="37">
				<? if($print_type != "out") { ?>
				<td colspan="11">�����Ͱ� �����ϴ�.</td>
				<? } else { ?>
				<td colspan="15">�����Ͱ� �����ϴ�.</td>
				<? } ?>
			</tr>

		<?      } ?>
	</tbody>
</table>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>