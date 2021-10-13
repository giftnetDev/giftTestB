<?session_start();?>
<?
# =============================================================================
# ��ǰ�� ������Ȳ > ����
# =============================================================================


#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

	$str_menu_title = "��ǰ�� ���� ��Ȳ";
	$file_name= $str_menu_title."-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );


#==============================================================================
# Confirm right
#==============================================================================

	$menu_right = "ST010"; // �޴����� ���� �� �־�� �մϴ�


	if ($code == "goods") {
		$str_menu_title = "��ǰ�� ���� ��Ȳ";
	}

	if ($code == "company") {
		$str_menu_title = "��ü�� ���� ��Ȳ";
	}

	if ($code == "period") {
		$str_menu_title = "�Ⱓ�� ���� ��Ȳ";
	}

	$file_name= $str_menu_title."-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );

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
	require "../../_classes/biz/goods/goods.php";


#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime(date('Y-01-01')));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================


	$nPage = 1;
	$nPageSize = 1000;
	$nListCnt = 1000;

#===============================================================
# Get Search list count
#===============================================================
	
	$arr_rs = listStatsBuyingByCompanyLedger($conn, $code, $con_cate, $start_date, $end_date, $cp_type2, $sel_opt_manager_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
?>
<font size=3><b><?=$Admin_shop_name?> <?=$str_menu_title?></b></font> <br>
<br>
��� ���� : [<?=date("Y�� m�� d��")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>����</th>
		<? if($code == "goods") { ?>
		<th align='center' bgcolor='#F4F1EF'>��ǰ�ڵ�</th>
		<th align='center' bgcolor='#F4F1EF'>��ǰ��</th>
		<? } else if($code == "company"){ ?>
		<th align='center' bgcolor='#F4F1EF'>��ü�ڵ�</th>
		<th align='center' bgcolor='#F4F1EF'>��ü��</th>
		<? } else if($code == "period"){ ?>
		<th align='center' bgcolor='#F4F1EF'>�⵵</th>
		<th align='center' bgcolor='#F4F1EF'>�⵵-����</th>
		<? } ?>
		
		<th align='center' bgcolor='#F4F1EF'>����ȸ��</th>
		<th align='center' bgcolor='#F4F1EF'>���Լ���</th>
		<th align='center' bgcolor='#F4F1EF'>�����հ�</th>
		<th align='center' bgcolor='#F4F1EF'>���� �߼���</th>
		<? if($code == "goods") { ?>
			<th align='center' bgcolor='#F4F1EF'>�ǸŰ�</th>
			<th align='center' bgcolor='#F4F1EF'><?=$vendor_calc?>%������</th>
		<? } ?>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$CODE						= trim($arr_rs[$j]["CODE"]);
							$TITLE						= trim($arr_rs[$j]["TITLE"]);
							$ORDER_TOTAL				= trim($arr_rs[$j]["ORDER_TOTAL"]);
							$QTY_TOTAL					= trim($arr_rs[$j]["QTY_TOTAL"]);
							$PRICE_TOTAL				= trim($arr_rs[$j]["PRICE_TOTAL"]);
							$LATEST_ORDER_DATE			= trim($arr_rs[$j]["LATEST_ORDER_DATE"]);
							
							if($code == "goods") { 

								$arr_rs_goods = selectGoodsByCode($conn, $CODE);
								$rs_price				= trim($arr_rs_goods[0]["PRICE"]); 
								$rs_sale_price			= trim($arr_rs_goods[0]["SALE_PRICE"]); 

								//var vendor_calc = Math.ceil10(((i_sale_price - i_total_wonga) * i_vender_calc / 100.0 + i_total_wonga) , 1);

								if($vendor_calc <> "")
									$vendor_price = number_format(ceiling((($rs_sale_price - $rs_price) * $vendor_calc / 100.0 + $rs_price), -1));
								else
									$vendor_price = "����������";
							}
						?>
	<tr height="37">
		<td bgColor='#FFFFFF' align='left'><?=$j+1?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$TITLE?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($ORDER_TOTAL)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($QTY_TOTAL)?></td>
		<td bgColor='#FFFFFF' align='right'><?=number_format($PRICE_TOTAL)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$LATEST_ORDER_DATE?></td>
		<? if($code == "goods") { ?>
		<td bgColor='#FFFFFF' align='right'><?=number_format($rs_sale_price)?></td>
		<td bgColor='#FFFFFF' align='right'><?=$vendor_price?></td>
		<? } ?>
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