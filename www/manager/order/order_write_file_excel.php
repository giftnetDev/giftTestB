<?
ini_set('memory_limit',-1);
session_start();
?>
<?
# =============================================================================
# File Name    : admin_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD003"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
#====================================================================
# Request Parameter
#====================================================================

	$file_name="�̵���ֹ�����Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );

	$arr_rs = listTempOrder($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>
<body>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>�����ü</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ�ڵ�</td>
		<td align='center' bgcolor='#F4F1EF'>��ǰ��</td>
		<td align='center' bgcolor='#F4F1EF'>���ް�</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ�����</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼǸ�1</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼ�1</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼǸ�2</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼ�2</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼǸ�3</td>
		<td align='center' bgcolor='#F4F1EF'>�ɼ�3</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ���</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڿ���ó</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ����޴���ȭ��ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>������</td>
		<td align='center' bgcolor='#F4F1EF'>�����ο���ó</td>
		<td align='center' bgcolor='#F4F1EF'>�������޴���ȭ��ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�����ȣ</td>
		<td align='center' bgcolor='#F4F1EF'>�ּ�</td>
		<td align='center' bgcolor='#F4F1EF'>�ֹ��ڸ޸�</td>
		<td align='center' bgcolor='#F4F1EF'>��ۺ�</td>
		<td align='center' bgcolor='#F4F1EF'>3�� ������</td>
		<td align='center' bgcolor='#F4F1EF'>��ü�ֹ���ȣ</td>
	</tr>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								// CP_NO, GOODS_NO, GOODS_NAME, QTY , GOODS_OPTION_NM_01, GOODS_OPTION_01, GOODS_OPTION_NM_02, GOODS_OPTION_02,
								// GOODS_OPTION_NM_03, GOODS_OPTION_03, O_NAME, O_PHONE, O_HPHONE, R_NAME, R_PHONE, R_HPHONE, R_ZIPCODE, R_ADDR1,
								// MEMO, ORDER_STATE, SA_DELIVERY, USE_TF, REG_ADM, REG_DATE
								
								//echo $j;

								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$ORDER_NO				= trim($arr_rs[$j]["ORDER_NO"]);
								$GOODS_NO				= SetStringFromDB($arr_rs[$j]["GOODS_NO"]);
								$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
								$GOODS_PRICE		= trim($arr_rs[$j]["GOODS_PRICE"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$OPTION_NAME_01	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_01"]);
								$OPTION_01			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_01"]);
								$OPTION_NAME_02	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_02"]);
								$OPTION_02			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_02"]);
								$OPTION_NAME_03	= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_NM_03"]);
								$OPTION_03			= SetStringFromDB($arr_rs[$j]["GOODS_OPTION_03"]);
								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE			= SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$MEMO						= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY		= trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO		= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

					?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CP_NO?></td>
		<td bgColor='#FFFFFF' align='center'><?=$GOODS_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
		<td bgColor='#FFFFFF' align='right'><?=$GOODS_PRICE?></td>
		<td bgColor='#FFFFFF' align='right'><?=$QTY?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_NAME_01?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_01?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_NAME_02?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_02?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_NAME_03?></td>
		<td bgColor='#FFFFFF' align='left'><?=$OPTION_03?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_PHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_HPHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_PHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_HPHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_ZIPCODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$R_ADDR1?></td>
		<td bgColor='#FFFFFF' align='left'><?=$MEMO?></td>
		<td bgColor='#FFFFFF' align='right'><?=$DELIVERY?></td>
		<td bgColor='#FFFFFF' align='right'><?=$SA_DELIVERY?></td>
		<td bgColor='#FFFFFF' align='right'><?=$CP_ORDER_NO?></td>
	</tr>
					<?			
										$err_str = "";
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