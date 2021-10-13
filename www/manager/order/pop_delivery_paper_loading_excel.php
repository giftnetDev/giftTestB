<?
ini_set('memory_limit',-1);
session_start();
?>
<?

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

#====================================================================
# Request Parameter
#====================================================================
	if($deliveryCP<>""){
		$delivery_cp=$deliveryCP;
	}

	
	$arr_rs = listDeliveryPaperLoadingExcel($conn, $specific_date, $delivery_cp, $delivery_fee);

	if($mode == "excel") {

		require_once "../../_PHPExcel/Classes/PHPExcel.php";

		$objPHPExcel = new PHPExcel();


		if($mode == "pop") {

			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A1", "����ȣ")
							->setCellValue("B1", "������")
							->setCellValue("C1", "��������ȭ��ȣ")
							->setCellValue("D1", "�������ڵ���")
							->setCellValue("E1", "�������ּ�")
							->setCellValue("F1", "���峻��")
							->setCellValue("G1", "�ֹ���")
							->setCellValue("H1", "�޸�")
							->setCellValue("I1", "�ֹ�������")
							->setCellValue("J1", "�ֹ������ڹ�ȣ")
							->setCellValue("K1", "�ֹ���")
							->setCellValue("L1", "�ֹ��ڹ�ȣ")
							->setCellValue("M1", "��ۿ���")
							->setCellValue("N1", "��������")
							->setCellValue("O1", "�߼����ּ�");
							
			if ($delivery_cp == "CJ�������")
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("P1", "��Ÿ1");
		}

		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$ORDER_STATE		    = SetStringFromDB($arr_rs[$j]["ORDER_STATE"]);
				$ORDER_GOODS_DELIVERY_NO= SetStringFromDB($arr_rs[$j]["ORDER_GOODS_DELIVERY_NO"]);
				$RESERVE_NO			    = SetStringFromDB($arr_rs[$j]["RESERVE_NO"]);
				$CP_ORDER_NO			= SetStringFromDB($arr_rs[$j]["CP_ORDER_NO"]);
				$DELIVERY_NO			= SetStringFromDB($arr_rs[$j]["DELIVERY_NO"]);
				$DELIVERY_SEQ			= SetStringFromDB($arr_rs[$j]["DELIVERY_SEQ"]);
				$RECEIVER_NM			= SetStringFromDB($arr_rs[$j]["RECEIVER_NM"]);
				$RECEIVER_PHONE			= SetStringFromDB($arr_rs[$j]["RECEIVER_PHONE"]);
				$RECEIVER_HPHONE		= SetStringFromDB($arr_rs[$j]["RECEIVER_HPHONE"]);
				$RECEIVER_ADDR			= SetStringFromDB($arr_rs[$j]["RECEIVER_ADDR"]);
				$GOODS_DELIVERY_NAME	= SetStringFromDB($arr_rs[$j]["GOODS_DELIVERY_NAME"]);								
				$ORDER_QTY		        = SetStringFromDB($arr_rs[$j]["ORDER_QTY"]);
				$MEMO			        = SetStringFromDB($arr_rs[$j]["MEMO"]);
				$ORDER_MANAGER_NM	    = SetStringFromDB($arr_rs[$j]["ORDER_MANAGER_NM"]);								
				$ORDER_MANAGER_PHONE    = SetStringFromDB($arr_rs[$j]["ORDER_MANAGER_PHONE"]);								
				$ORDER_NM			    = SetStringFromDB($arr_rs[$j]["ORDER_NM"]);
				$ORDER_PHONE		    = SetStringFromDB($arr_rs[$j]["ORDER_PHONE"]);
				$DELIVERY_FEE		    = SetStringFromDB($arr_rs[$j]["DELIVERY_FEE"]);
				$DELIVERY_PROFIT		= SetStringFromDB($arr_rs[$j]["DELIVERY_PROFIT"]);
				$PAYMENT_TYPE   		= SetStringFromDB($arr_rs[$j]["PAYMENT_TYPE"]);
				$SEND_CP_ADDR      	    = SetStringFromDB($arr_rs[$j]["SEND_CP_ADDR"]);

				//$DELIVERY_FEE = getDcodeExtByCode($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);

				$RECEIVER_ADDR = str_replace(array("\r\n", "\n", "\r", "<br>", "<br/>", "<BR/>", "<BR>"), '', $RECEIVER_ADDR);
				$GOODS_DELIVERY_NAME = str_replace(array("&quot;"), '"', $GOODS_DELIVERY_NAME);
				

				//���峪 ��Ÿ ���Ŀ��� �� �����̽� ���ڵ��̶� ������ - ����
				$RECEIVER_ADDR = str_replace(array("&#160;"), ' ', $RECEIVER_ADDR);

				if($mode == "pop") 
					$k = $j+2;
				else
					$k = $j+1;

				$GOODS_CODE = iconv("EUC-KR", "UTF-8", $GOODS_CODE);
				$GOODS_NAME = iconv("EUC-KR", "UTF-8", $GOODS_NAME);

				## MRO ��û�� ���� �����ڸ��� (��)����Ʈ�ݿ��� ���������� ���� 
				
				$O_CP_NO_AND_MEM_NM = selectCompanyNumberAndMemberName($conn, $DELIVERY_SEQ, $delivery_cp);
				// $O_CP_NO_AND_MEM_NM = selectCompanyNumberAndMemberName($conn, $DELIVERY_SEQ, "CJ�������");
				// $MEMO = "M: ".$O_CP_NO_AND_MEM_NM[0]["CP_NO"];
				if($O_CP_NO_AND_MEM_NM[0]["CP_NO"] == '1480'){
					if($ORDER_MANAGER_NM =='(��)����Ʈ��'){
						$ORDER_MANAGER_NM = $O_CP_NO_AND_MEM_NM[0]["O_MEM_NM"];
					}
				
					if($ORDER_NM == '(��)����Ʈ��'){
						$ORDER_NM = $O_CP_NO_AND_MEM_NM[0]["O_MEM_NM"];
					}
				} 

				##

				$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue("A$k", iconv("EUC-KR", "UTF-8", $DELIVERY_SEQ))
								->setCellValue("B$k", iconv("EUC-KR", "UTF-8", $RECEIVER_NM))
								->setCellValue("C$k", iconv("EUC-KR", "UTF-8", $RECEIVER_PHONE))
								->setCellValue("D$k", iconv("EUC-KR", "UTF-8", $RECEIVER_HPHONE))
								->setCellValue("E$k", iconv("EUC-KR", "UTF-8", $RECEIVER_ADDR))
								->setCellValue("F$k", iconv("EUC-KR", "UTF-8", $GOODS_DELIVERY_NAME))
								->setCellValue("G$k", iconv("EUC-KR", "UTF-8", $ORDER_QTY))
								->setCellValue("H$k", iconv("EUC-KR", "UTF-8", $MEMO))
								->setCellValue("I$k", iconv("EUC-KR", "UTF-8", $ORDER_MANAGER_NM))
								->setCellValue("J$k", iconv("EUC-KR", "UTF-8", $ORDER_MANAGER_PHONE))
								->setCellValue("K$k", iconv("EUC-KR", "UTF-8", $ORDER_NM))
								->setCellValue("L$k", iconv("EUC-KR", "UTF-8", $ORDER_PHONE))
								->setCellValue("M$k", iconv("EUC-KR", "UTF-8", $DELIVERY_FEE))
								->setCellValue("N$k", iconv("EUC-KR", "UTF-8", $PAYMENT_TYPE))
								->setCellValue("O$k", iconv("EUC-KR", "UTF-8", $SEND_CP_ADDR));

				if ($delivery_cp == "CJ�������")
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue("P$k", iconv("EUC-KR", "UTF-8", $DELIVERY_SEQ));

			}
		}

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle(iconv("EUC-KR", "UTF-8", $delivery_cp));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(39);

		// ������ ���������� utf-8�� ��� �ѱ����� �̸��� �����Ƿ� euc-kr�� ��ȯ���ش�.
		$filename = $delivery_cp."_����ε�_����";

		// Redirect output to a client��s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header('Cache-Control: max-age=0');
	 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

		mysql_close($conn);
		exit;


	} else {

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<style>td { mso-number-format:\@; } </style> 
<title><?=$g_title?></title>
</head>

<body>

<?
	if($mode == "pop") {
?>
			<link rel="stylesheet" href="../css/admin.css" type="text/css" />
			<table cellpadding="0" cellspacing="0" class="rowstable03" border="0" style="width:100%">
				<colgroup>
					<col width="5%">
					<col width="5%">
					<col width="7%">
					<col width="7%">
					<col width="11%">
					<col width="10%">
					<col width="5%">
					<col width="10%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="10%">
					<? if ($delivery_cp == "CJ�������") { ?>
					<col width="5%">
					<? } ?>
				</colgroup>
				<thead>
					<tr>
						<th>����ȣ</th>
						<th>������</th>
						<th>��������ȭ��ȣ</th>
						<th>�������ڵ���</th>
						<th>�������ּ�</th>
						<th>���峻��</th>
						<th>�ֹ���</th>
						<th>�޸�</th>
						<th>�ֹ�������</th>
						<th>�ֹ������ڹ�ȣ</th>
						<th>�ֹ���</th>
						<th>�ֹ��ڹ�ȣ</th>
						<th>��ۿ���</th>
						<th>��������</th>
						<th>�߼����ּ�</th>
						<? if ($delivery_cp == "CJ�������") { ?>
						<th class="end">��Ÿ1</th>
						<? } ?>
					</tr>
				</thead>

			
<?
}else{
?>
	<TABLE border=1>
<?
} 
?>
				<?
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$ORDER_STATE		    = SetStringFromDB($arr_rs[$j]["ORDER_STATE"]);
							$ORDER_GOODS_DELIVERY_NO= SetStringFromDB($arr_rs[$j]["ORDER_GOODS_DELIVERY_NO"]);
							$RESERVE_NO			    = SetStringFromDB($arr_rs[$j]["RESERVE_NO"]);
							$CP_ORDER_NO			= SetStringFromDB($arr_rs[$j]["CP_ORDER_NO"]);
							$DELIVERY_NO			= SetStringFromDB($arr_rs[$j]["DELIVERY_NO"]);
							$DELIVERY_SEQ			= SetStringFromDB($arr_rs[$j]["DELIVERY_SEQ"]);
							$RECEIVER_NM			= SetStringFromDB($arr_rs[$j]["RECEIVER_NM"]);
							$RECEIVER_PHONE			= SetStringFromDB($arr_rs[$j]["RECEIVER_PHONE"]);
							$RECEIVER_HPHONE		= SetStringFromDB($arr_rs[$j]["RECEIVER_HPHONE"]);
							$RECEIVER_ADDR			= SetStringFromDB($arr_rs[$j]["RECEIVER_ADDR"]);
							$GOODS_DELIVERY_NAME	= SetStringFromDB($arr_rs[$j]["GOODS_DELIVERY_NAME"]);								
							$ORDER_QTY		        = SetStringFromDB($arr_rs[$j]["ORDER_QTY"]);
							$MEMO			        = SetStringFromDB($arr_rs[$j]["MEMO"]);
							$ORDER_MANAGER_NM	    = SetStringFromDB($arr_rs[$j]["ORDER_MANAGER_NM"]);								
							$ORDER_MANAGER_PHONE    = SetStringFromDB($arr_rs[$j]["ORDER_MANAGER_PHONE"]);								
							$ORDER_NM			    = SetStringFromDB($arr_rs[$j]["ORDER_NM"]);
							$ORDER_PHONE		    = SetStringFromDB($arr_rs[$j]["ORDER_PHONE"]);
							$DELIVERY_FEE_CODE	    = SetStringFromDB($arr_rs[$j]["DELIVERY_FEE_CODE"]);
							$DELIVERY_PROFIT		= SetStringFromDB($arr_rs[$j]["DELIVERY_PROFIT"]);
							$PAYMENT_TYPE   		= SetStringFromDB($arr_rs[$j]["PAYMENT_TYPE"]);
							$SEND_CP_ADDR      	    = SetStringFromDB($arr_rs[$j]["SEND_CP_ADDR"]);

							$DELIVERY_FEE = getDcodeExtByCode($conn, 'DELIVERY_FEE', $DELIVERY_FEE_CODE);

							$RECEIVER_ADDR = str_replace(array("\r\n", "\n", "\r", "<br>", "<br/>", "<BR/>", "<BR>"), '', $RECEIVER_ADDR);

						?>
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_SEQ?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_HPHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$RECEIVER_ADDR?></td>
							<td bgColor='#FFFFFF' align='left'><?=$GOODS_DELIVERY_NAME?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_QTY?></td>
							<td bgColor='#FFFFFF' align='left'><?=$MEMO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_MANAGER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_MANAGER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_PHONE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_FEE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$PAYMENT_TYPE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$SEND_CP_ADDR?></td>
							<? if ($delivery_cp == "CJ�������") { ?>
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_SEQ?></td>
							<? } ?>
						</tr>
						
					<?
						}
					 }else {
					?>
						<tr class="order">
							<? if ($delivery_cp == "CJ�������") { ?>
							<td height="50" align="center" colspan="16">�����Ͱ� �����ϴ�. </td>
							<? } else { ?>
							<td height="50" align="center" colspan="15">�����Ͱ� �����ϴ�. </td>
							<? } ?>
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

	}

?>