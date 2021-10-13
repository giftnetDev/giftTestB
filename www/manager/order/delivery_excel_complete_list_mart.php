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
	$menu_right = "OD015"; // �޴����� ���� �� �־�� �մϴ�

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
		$file_name="�̸�Ʈ ���ϸ���Ʈ-".$specific_date.".xls";
	} else if($mode == 'homeplus') {
		$file_name="Ȩ�÷��� ���ϸ���Ʈ-".$specific_date.".xls";
	} else if($mode == 'lotte') {
		$file_name="�Ե���Ʈ ���ϸ���Ʈ-".$specific_date.".xls";
	} else
		$file_name = '';
	  
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
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
					<td>��۹�ȣ</td>
					<td>���������</td>
					<td>�ù��</td>
					<td>�����ȣ</td>
					<td>�߷�����</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForEmart($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn			            = trim($arr_rs[$j]["rn"]);                 //no
							$CP_DELIVERY_NO			= trim($arr_rs[$j]["GOODS_OPTION_NM_02"]); //��۹�ȣ
							$DELIVERY_TYPE_DETAIL   = '22';                                    //���������
							$DELIVERY_CP_CODE	    = '0000033073';                            //�ù��
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //�����ȣ
							$WEIGHT_INFO			= '';                                      //�߷����� - ������۽� �ʿ����

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
							<td height="50" align="center" colspan="6">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
					 }
					?>
			</table>
<?  } ?>

<? if($mode == 'homeplus') { ?>
			<TABLE border=1>
				<tr>
					<td>�ֹ���ȣ</td>
					<td>�ù��</td>
					<td>������ȣ</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForHomeplus($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);        //�ֹ���ȣ
							$DELIVERY_CP_CODE	    = '27230';                                 //�ù��
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //������ȣ

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
							<td height="50" align="center" colspan="3">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
					 }
					?>
			</table>
<?  } ?>


<? if($mode == 'lotte') { ?>
			<TABLE border=1>
				<tr>
					<td>�ֹ���ȣ</td>
					<td>������ȣ</td>
					<td>�ù���ڵ�</td>
				</tr>
				<?

					$arr_rs = listMartCompleteForLotte($conn, $specific_date);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CP_ORDER_NO			= trim($arr_rs[$j]["CP_ORDER_NO"]);        //�ֹ���ȣ
							$DELIVERY_NO			= trim($arr_rs[$j]["DELIVERY_NO"]);        //������ȣ
							$DELIVERY_CP_CODE	    = '04';                                    //�ù���ڵ�

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
							<td height="50" align="center" colspan="3">�����Ͱ� �����ϴ�. </td>
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