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

	$con_order_type = "";

	$menu_right = "ST006"; // �޴����� ���� �� �־�� �մϴ�


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
	require "../../_classes/biz/stats/stats.php";

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($order_field == "")
		$order_field = "REG_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

#============================================================
# Page process
#============================================================

	$nPage = 1;
	$nPageSize = 100000;
	$nListCnt = 100000;

#===============================================================
# Get Search list count
#===============================================================

	$filter = array("cate_01" => $cate_01);

	$arr_rs = listSalesStatement($conn, $start_date, $end_date, $cp_type2, $cp_type, $opt_manager_no, $filter, $search_field, $search_str, $order_field, $order_asc, $nPage, $nPageSize, $nListCnt);

	
	$file_name= "��������ǥ"."-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );

?>
<font size=3><b>��������ǥ <?=getAdminName($conn, $opt_manager_no);?></b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>�����ֹ�����ȣ</th>
		<th align='center' bgcolor='#F4F1EF'>�����ֹ���ǰ��ȣ</th>
		<th align='center' bgcolor='#F4F1EF'>�ܺ��ֹ���ȣ</th>
		<th align='center' bgcolor='#F4F1EF'>�Ǹ�_��ü�ڵ�</th>
		<th align='center' bgcolor='#F4F1EF'>�Ǹ�_��ü��</th>
		<th align='center' bgcolor='#F4F1EF'>�Ǹ�_������</th>
		<th align='center' bgcolor='#F4F1EF'>�ֹ��ڸ�</th>
		<th align='center' bgcolor='#F4F1EF'>�����ڸ�</th>
		
		<th align='center' bgcolor='#F4F1EF'>�ֹ���ǰ����</th>
		<th align='center' bgcolor='#F4F1EF'>��������</th>
		<th align='center' bgcolor='#F4F1EF'>��ȯ����</th>

		<th align='center' bgcolor='#F4F1EF'>��ǰ�ڵ�</th>
		<th align='center' bgcolor='#F4F1EF'>��ǰ��</th>
		<th align='center' bgcolor='#F4F1EF'>�Ӽ�</th>
		<th align='center' bgcolor='#F4F1EF'>����</th>
		<th align='center' bgcolor='#F4F1EF'>�ǸŰ�</th>
		<th align='center' bgcolor='#F4F1EF'>��������</th>
		<th align='center' bgcolor='#F4F1EF'>�߰���ۺ�</th>
		<th align='center' bgcolor='#F4F1EF'>������</th>
		<th align='center' bgcolor='#F4F1EF'><b>�Ǹű��հ�</b></th>

		<th align='center' bgcolor='#F4F1EF'>����_��ü�ڵ�</th>
		<th align='center' bgcolor='#F4F1EF'>����_��ü��</th>
		<th align='center' bgcolor='#F4F1EF'>����_������</th>
		
		<th align='center' bgcolor='#F4F1EF'>���԰�</th>
		<th align='center' bgcolor='#F4F1EF'>��ƼĿ���</th>
		<th align='center' bgcolor='#F4F1EF'>����/�μ���</th>
		<th align='center' bgcolor='#F4F1EF'>�ù��</th>
		<th align='center' bgcolor='#F4F1EF'>�ڽ��Լ�</th>
		<th align='center' bgcolor='#F4F1EF'>������</th>
		<th align='center' bgcolor='#F4F1EF'>�Ǹż�����</th>
		<th align='center' bgcolor='#F4F1EF'>�۾���</th>
		<th align='center' bgcolor='#F4F1EF'>��Ÿ���</th>
		<th align='center' bgcolor='#F4F1EF'>���Կ���</th>
		<th align='center' bgcolor='#F4F1EF'><b>���Կ����հ�</b></th>

		<th align='center' bgcolor='#F4F1EF'>����</th>
		<th align='center' bgcolor='#F4F1EF'>������</th>
		<th align='center' bgcolor='#F4F1EF'><b>�����հ�</b></th>
		
		<th align='center' bgcolor='#F4F1EF'>��ƼĿ��Ī</th>
		<th align='center' bgcolor='#F4F1EF'>��ƼĿ�޼���</th>
		<th align='center' bgcolor='#F4F1EF'>�ƿ��ڽ���ƼĿ����</th>
		<th align='center' bgcolor='#F4F1EF'>��������Ī</th>
		<th align='center' bgcolor='#F4F1EF'>�μ�޼���</th>
		<th align='center' bgcolor='#F4F1EF'>���������</th>
		<th align='center' bgcolor='#F4F1EF'>�۾��޸�</th>
		<th align='center' bgcolor='#F4F1EF'>��۹��</th>

		<th align='center' bgcolor='#F4F1EF'>�ֹ�����</th>
		<th align='center' bgcolor='#F4F1EF'>�ֹ�Ȯ����</th>
		<th align='center' bgcolor='#F4F1EF'>�ù�����</th>
		<th align='center' bgcolor='#F4F1EF'>����Ȯ����</th>
		<th align='center' bgcolor='#F4F1EF'>���������</th>
		<th align='center' bgcolor='#F4F1EF' class="end">�ֹ���</th>
	</tr>
	<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//�ʱ�ȭ 
							$QTY					= 0;  
							$SALE_PRICE				= 0; 
							$DISCOUNT_PRICE			= 0; 
							$SA_DELIVERY_PRICE		= 0; 
							$EXTRA_PRICE			= 0; 
							$BUY_PRICE				= 0;
							$STICKER_PRICE			= 0;
							$PRINT_PRICE			= 0;
							$DELIVERY_PRICE			= 0;
							$DELIVERY_CNT_IN_BOX	= 0;
							$SALE_SUSU				= 0;
							$LABOR_PRICE			= 0;
							$OTHER_PRICE			= 0;
							$PRICE					= 0;
							$TOTAL_SALE_PRICE		= 0;
							$MAJIN					= 0;
							$MAJIN_PER				= 0;

							
							$RESERVE_NO				= SetStringFromDB($arr_rs[$j]["RESERVE_NO"]); 
							$ORDER_GOODS_NO			= SetStringFromDB($arr_rs[$j]["ORDER_GOODS_NO"]); 
							$CP_ORDER_NO			= SetStringFromDB($arr_rs[$j]["CP_ORDER_NO"]); 
							$CP_CODE				= SetStringFromDB($arr_rs[$j]["CP_CODE"]); 
							$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]); 
							$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]); 
							$O_MEM_NM				= SetStringFromDB($arr_rs[$j]["O_MEM_NM"]); 
							$R_MEM_NM				= SetStringFromDB($arr_rs[$j]["R_MEM_NM"]); 
							
							$CATE_01				= SetStringFromDB($arr_rs[$j]["CATE_01"]); 
							$TAX_TF					= SetStringFromDB($arr_rs[$j]["TAX_TF"]);
							$CATE_04				= SetStringFromDB($arr_rs[$j]["CATE_04"]); 
							
							$GOODS_NO				= SetStringFromDB($arr_rs[$j]["GOODS_NO"]); 
							$GOODS_CODE				= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]); 
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]); 
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]); 
							$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);  
							$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]); 
							$DISCOUNT_PRICE			= SetStringFromDB($arr_rs[$j]["DISCOUNT_PRICE"]); 
							$SA_DELIVERY_PRICE		= SetStringFromDB($arr_rs[$j]["SA_DELIVERY_PRICE"]); 
							$EXTRA_PRICE			= SetStringFromDB($arr_rs[$j]["EXTRA_PRICE"]); 

							$BUY_CP_CODE			= SetStringFromDB($arr_rs[$j]["BUY_CP_CODE"]); 
							$BUY_CP_NM				= SetStringFromDB($arr_rs[$j]["BUY_CP_NM"]); 
							$BUY_CP_NM2				= SetStringFromDB($arr_rs[$j]["BUY_CP_NM2"]); 
							
							$BUY_PRICE				= SetStringFromDB($arr_rs[$j]["BUY_PRICE"]); 
							$STICKER_PRICE			= SetStringFromDB($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE			= SetStringFromDB($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE			= SetStringFromDB($arr_rs[$j]["DELIVERY_PRICE"]); 
							$DELIVERY_CNT_IN_BOX	= SetStringFromDB($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]); 
							$SALE_SUSU				= SetStringFromDB($arr_rs[$j]["SALE_SUSU"]); 
							$LABOR_PRICE			= SetStringFromDB($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= SetStringFromDB($arr_rs[$j]["OTHER_PRICE"]); 
							$PRICE					= SetStringFromDB($arr_rs[$j]["PRICE"]); 

							$OPT_STICKER_NO			= SetStringFromDB($arr_rs[$j]["OPT_STICKER_NO"]); 
							$OPT_STICKER_READY		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_READY"]);	
							$OPT_STICKER_MSG		= SetStringFromDB($arr_rs[$j]["OPT_STICKER_MSG"]); 
							$OPT_OUTBOX_TF			= SetStringFromDB($arr_rs[$j]["OPT_OUTBOX_TF"]); 
							$OPT_WRAP_NO			= SetStringFromDB($arr_rs[$j]["OPT_WRAP_NO"]); 
							$OPT_PRINT_MSG			= SetStringFromDB($arr_rs[$j]["OPT_PRINT_MSG"]); 
							$OPT_OUTSTOCK_DATE		= SetStringFromDB($arr_rs[$j]["OPT_OUTSTOCK_DATE"]); 
							$OPT_MEMO				= SetStringFromDB($arr_rs[$j]["OPT_MEMO"]);
							
							$ORDER_STATE			= SetStringFromDB($arr_rs[$j]["ORDER_STATE"]); 
							$ORDER_CONFIRM_DATE		= SetStringFromDB($arr_rs[$j]["ORDER_CONFIRM_DATE"]); 
							$DELIVERY_DATE			= SetStringFromDB($arr_rs[$j]["DELIVERY_DATE"]); 
							$SALE_CONFIRM_YMD		= SetStringFromDB($arr_rs[$j]["SALE_CONFIRM_YMD"]);
							$OPT_MANAGER_NO			= SetStringFromDB($arr_rs[$j]["OPT_MANAGER_NO"]); 
							$ORDER_DATE				= SetStringFromDB($arr_rs[$j]["ORDER_DATE"]); 

							$DELIVERY_TYPE			= SetStringFromDB($arr_rs[$j]["DELIVERY_TYPE"]); 
							

							
							

							if($ORDER_STATE > 3)
								$QTY = $QTY * -1;

							if($CATE_04 == "CHANGE") {
								$CATE_04 = "��ȯ��";
							}

							//if($DELIVERY_TYPE != 99) { 

								if($OPT_STICKER_NO == "0") { 
									$OPT_STICKER_NO = "����";
									$STICKER_PRICE = 0;
								} else { 
									$OPT_STICKER_NO = getGoodsCodeName($conn, $OPT_STICKER_NO);
								}

								if($OPT_WRAP_NO == "0") { 
									$OPT_WRAP_NO = "����";
									$PRINT_PRICE = 0;
								} else { 
									$OPT_WRAP_NO = getGoodsCodeName($conn, $OPT_WRAP_NO);
								}

								if($OPT_OUTBOX_TF == "N" || $OPT_OUTBOX_TF == "")
									$OPT_OUTBOX_TF = "����";
								else
									$OPT_OUTBOX_TF = "����";


								//////////////////////////////////////////////////////////////////
								//$BUY_PRICE = getBuyPrice($conn, $BUY_PRICE, $GOODS_NO, $DELIVERY_CNT_IN_BOX);

								if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
									$DELIVERY_PER_PRICE = 0;
								else 
									$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
								
								
								//���� ������� MRO�� �����
								//if($CP_CODE == "3559") 
								//	$SUSU_PRICE = round($SALE_PRICE / 100.0 * $SALE_SUSU, 4);
								//else {
								//	$SUSU_PRICE = 0;
								//	$SALE_SUSU = 0;
								//}

								//$EXTRA_PRICE�� ����
								//$SUSU_PRICE = round($SALE_PRICE / 100.0 * $SALE_SUSU, 0);

								//�ǸŽ� �����Ḧ ����ϰ� ���� �������� ���� 2018-08-08
								$TOTAL_WONGA = round($BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + $DELIVERY_PER_PRICE + $LABOR_PRICE + $OTHER_PRICE, 0);

							
								//$MAJIN = $SALE_PRICE - $SUSU_PRICE - $TOTAL_WONGA;
								$TOTAL_SALE_PRICE = ($SALE_PRICE * $QTY) - $DISCOUNT_PRICE - ($EXTRA_PRICE * $QTY);
								
								if($QTY > 0)
									$MAJIN = $SALE_PRICE - $TOTAL_WONGA;
								else
									$MAJIN = ($SALE_PRICE - $TOTAL_WONGA) * -1;

								if($SALE_PRICE != 0)
									$MAJIN_PER = round(($MAJIN / ($TOTAL_SALE_PRICE / $QTY)) * 100, 2);
								else 
									$MAJIN_PER = 0;
							
							/*
							} else { 
								
								$OPT_STICKER_NO = "";
								$STICKER_PRICE = "0";
								$OPT_WRAP_NO = "";
								$PRINT_PRICE = "0";
								$OPT_OUTBOX_TF = "";
								$BUY_PRICE = "0";
								$DELIVERY_PER_PRICE = 0;
								$DELIVERY_CNT_IN_BOX = 0;
								$SUSU_PRICE = 0;
								$SALE_SUSU = 0;
								$SUSU_PRICE = 0;
								$TOTAL_WONGA = 0;
								$MAJIN = 0;
								$MAJIN_PER = 0;

								$TOTAL_SALE_PRICE = ($SALE_PRICE * $QTY) - $DISCOUNT_PRICE - ($EXTRA_PRICE * $QTY);

							}
							*/
							
							///////////////////////////////////////////////////////////////////

							if ($OPT_OUTSTOCK_DATE <> "")  {
								$OPT_OUTSTOCK_DATE		= date("Y-m-d",strtotime($OPT_OUTSTOCK_DATE));
							}


							if ($ORDER_CONFIRM_DATE <> "")  {
								$ORDER_CONFIRM_DATE		= date("Y-m-d",strtotime($ORDER_CONFIRM_DATE));
							}

							if ($DELIVERY_DATE <> "")  {
								$DELIVERY_DATE		= date("Y-m-d",strtotime($DELIVERY_DATE));
							}

							
							if ($ORDER_DATE <> "")  {
								$ORDER_DATE		= date("Y-m-d H:i",strtotime($ORDER_DATE));
							}

							$OPT_MANAGER_NO = getAdminName($conn, $OPT_MANAGER_NO);
							$ORDER_STATE = getDcodeName($conn, 'ORDER_STATE', $ORDER_STATE);

								
						?>
						<tr>
							<td bgColor='#FFFFFF' align='left'><?=$RESERVE_NO ?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_GOODS_NO ?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CP_ORDER_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CP_CODE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CP_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CP_NM2?></td>
							<td bgColor='#FFFFFF' align='left'><?=$O_MEM_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$R_MEM_NM?></td> 
							
							<td bgColor='#FFFFFF' align='left'><?=$CATE_01?></td>
							<td bgColor='#FFFFFF' align='left'><?=$TAX_TF?></td>
							<td bgColor='#FFFFFF' align='left'><?=$CATE_04?></td>
							
							<td bgColor='#FFFFFF' align='left'><?=$GOODS_CODE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$GOODS_NAME?></td>
							<td bgColor='#FFFFFF' align='left'><?=$GOODS_SUB_NAME?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($QTY)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($SALE_PRICE)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($DISCOUNT_PRICE)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($SA_DELIVERY_PRICE)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($EXTRA_PRICE * $QTY)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($TOTAL_SALE_PRICE)?></td>

							<td bgColor='#FFFFFF' align='left'><?=$BUY_CP_CODE?></td> 
							<td bgColor='#FFFFFF' align='left'><?=$BUY_CP_NM?></td>
							<td bgColor='#FFFFFF' align='left'><?=$BUY_CP_NM2?></td>
							
							<td bgColor='#FFFFFF' align='left'><?=number_format($BUY_PRICE)?></td> 
							<td bgColor='#FFFFFF' align='left'><?=number_format($STICKER_PRICE)?></td> 
							<td bgColor='#FFFFFF' align='left'><?=number_format($PRINT_PRICE)?></td> 
							<td bgColor='#FFFFFF' align='left'><?=number_format($DELIVERY_PRICE)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($DELIVERY_CNT_IN_BOX) ?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($DELIVERY_PER_PRICE) ?></td>
							<td bgColor='#FFFFFF' align='left'><?=$SALE_SUSU?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($LABOR_PRICE)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($OTHER_PRICE)?></td> 
							<td bgColor='#FFFFFF' align='left'><?=number_format($TOTAL_WONGA)?></td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($TOTAL_WONGA * $QTY)?></td>
	
							<td bgColor='#FFFFFF' align='left'><?=number_format($MAJIN)?></td>
							<td bgColor='#FFFFFF' align='left'><?=$MAJIN_PER?>%</td>
							<td bgColor='#FFFFFF' align='left'><?=number_format($TOTAL_SALE_PRICE - ($TOTAL_WONGA * $QTY))?></td>

							<td bgColor='#FFFFFF' align='left'><?=$OPT_STICKER_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$OPT_STICKER_MSG?></td> 
							<td bgColor='#FFFFFF' align='left'><?=$OPT_OUTBOX_TF?></td> 
							<td bgColor='#FFFFFF' align='left'><?=$OPT_WRAP_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$OPT_PRINT_MSG?></td> 
							<td bgColor='#FFFFFF' align='left'><?=$OPT_OUTSTOCK_DATE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$OPT_MEMO?></td>
							<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn,"DELIVERY_TYPE",$DELIVERY_TYPE)?></td>
							
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_STATE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_CONFIRM_DATE?></td> 
							<td bgColor='#FFFFFF' align='left'><?=$DELIVERY_DATE?></td>
							<td bgColor='#FFFFFF' align='left'><?=$SALE_CONFIRM_YMD?></td>
							<td bgColor='#FFFFFF' align='left'><?=$OPT_MANAGER_NO?></td>
							<td bgColor='#FFFFFF' align='left'><?=$ORDER_DATE?></td>
							
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