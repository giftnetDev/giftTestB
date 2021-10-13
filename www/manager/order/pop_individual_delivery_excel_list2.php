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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";


#====================================================================
# Request Parameter
#====================================================================

	$arr_rs = listDeliveryIndividual($conn, $order_goods_no, "DESC");

	$file_name="�����ù�����-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<form name="frm" method="post" enctype="multipart/form-data">

	<table border="1" cellpadding="1" cellspacing="1">
	<thead>
		<tr>
			<th>������</th>
			<th>����ó</th>
			<th>�޴�����ȣ</th>
			<th>�ּ�</th>
			<th>��۸޸�</th>
			<th>�����ǰ��</th>
			<th>��ǰ����</th>
			<th>�������</th>
			<th>�����</th>
			<th>����Ͻ�</th>
			<th>��۹��</th>
			<th class="end">�Ϸ�ó��</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

			$INDIVIDUAL_NO			= trim($arr_rs[$i]["INDIVIDUAL_NO"]);
			$R_ZIPCODE			    = trim($arr_rs[$i]["R_ZIPCODE"]); 
			$R_ADDR1 				= trim($arr_rs[$i]["R_ADDR1"]);
			$R_MEM_NM				= trim($arr_rs[$i]["R_MEM_NM"]);
			$R_PHONE				= trim($arr_rs[$i]["R_PHONE"]); 
			$R_HPHONE				= trim($arr_rs[$i]["R_HPHONE"]); 
			$GOODS_DELIVERY_NAME	= trim($arr_rs[$i]["GOODS_DELIVERY_NAME"]); 
			$SUB_QTY				= trim($arr_rs[$i]["SUB_QTY"]);
			$MEMO					= trim($arr_rs[$i]["MEMO"]);
			$DELIVERY_TYPE			= trim($arr_rs[$i]["DELIVERY_TYPE"]);
			$IS_DELIVERED			= trim($arr_rs[$i]["IS_DELIVERED"]);

			$REG_DATE				= date("n��j��H��i��", strtotime(trim($arr_rs[$i]["REG_DATE"])));

			$REG_ADM				= trim($arr_rs[$i]["REG_ADM"]);
			$REG_ADM = getAdminName($conn, $REG_ADM);

			$DELIVERY_PAPER_QTY = countOrderDeliveryPaper($conn, $order_goods_no, $INDIVIDUAL_NO);

			if($IS_DELIVERED == "Y") { 
				$DELIVERY_DATE = date("n��j��H��i��", strtotime(trim($arr_rs[$i]["DELIVERY_DATE"])));

			} else { 
				$DELIVERY_DATE = "�����";
			}

	?>

		<tr>
			<td><?=$R_MEM_NM?></td>
			<td><?=$R_PHONE?></td>
			<td><?=$R_HPHONE?></td>
			<td><?=$R_ADDR1?></td>
			<td><?=$MEMO?></td>
			<td><?=$GOODS_DELIVERY_NAME?></td>
			<td><?=$SUB_QTY?></td>
			<td style="font-weight:bold; <?if($DELIVERY_TYPE == "0" && $DELIVERY_PAPER_QTY == "0") echo "color:red;";?>"><?=$DELIVERY_PAPER_QTY?></td>
			<td><?=$REG_ADM?></td>
			<td><?=$REG_DATE?></td>
			<td><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)?></td>
			<td><?=$DELIVERY_DATE?></td>
		</tr>
	
	<?
		}
	} else {

	?>
		<tr>
			<td colspan="12" height="50" align="center">�����Ͱ� �����ϴ�</td>
		</tr>
	<?

	}
	
	?>
	
	</tbody>
	</table>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>