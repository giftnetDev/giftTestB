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

	$file_name="�ŷ�ó(�ܺ�)���帮��Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	$nPage = 1;
	$nPageSize = 1000000;

	$arr_order_outside = listOrderDeliveryPaperOutside($conn, $order_goods_no);


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b> �ŷ�ó(�ܺ�)���� ����Ʈ </b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�ù��</td>
		<td align='center' bgcolor='#F4F1EF'>�����ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�޸�</td>
	</tr>
	<?
		if(sizeof($arr_order_outside) > 0) { 
			for($o = 0; $o < sizeof($arr_order_outside); $o ++) { 
				$rs_delivery_cp = $arr_order_outside[$o]["DELIVERY_CP"];
				$rs_delivery_no = $arr_order_outside[$o]["DELIVERY_NO"];
				$rs_memo		= $arr_order_outside[$o]["MEMO"];
	?>
	<tr>
		<td  bgColor='#FFFFFF' align='right'><?=$rs_delivery_cp?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$rs_delivery_no?></td>
		<td  bgColor='#FFFFFF' align='right'><?=$rs_memo?></td>
	</tr>
		<?
				}
			} else { 
		?>
			<tr height="37">
				<td colspan="3">�����Ͱ� �����ϴ�.</td>
			</tr>

		<?  } ?>
	</tbody>
</table>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>