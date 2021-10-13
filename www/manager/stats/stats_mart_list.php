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

	
	if ($code == "day") {
		$menu_right = "ST002"; // �޴����� ���� �� �־�� �մϴ�
		$str_menu_title = "�Ϻ� �Ǹ� ��Ȳ";
		$str_list_title = "�����";
	}

	if ($code == "month") {
		$menu_right = "ST003"; // �޴����� ���� �� �־�� �մϴ�
		$str_menu_title = "���� �Ǹ� ��Ȳ";
		$str_list_title = "���";
	}

	if ($code == "goods") {
		$menu_right = "ST004"; // �޴����� ���� �� �־�� �մϴ�
		$str_menu_title = "��ǰ�� �Ǹ� ��Ȳ";
		$str_list_title = "��ǰ��";
	}

	if ($order_field == "")
		$order_field = "TITLE";

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
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listStatsOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $search_field, $search_str, $order_field, $order_str);
	$arr_rs_all = listStatsAllOrder($conn, $code, $sel_date_type, $start_date, $end_date, $cp_type, $cp_type2, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script language="javascript">

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_reload() {
		location.reload();
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="code" value="<?=$code?>">
<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2><?=$str_menu_title?></h2>
				<div class="btnright">&nbsp;</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<thead>
					<tr>
						<th>��ȸ�Ⱓ</th>
						<td colspan="4">
							<? 
								$arr_result = listDistinctYearMonth($conn);
								echo makeGenericSelectBox($conn, $arr_result, "specific_month", "100px", "����", "", $this_month, "YearMonth" , "YearMonth");
							?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>���޾�ü</th>
						<td>
							<?= makeCompanySelectBox($conn, '����', $cp_type);?>
						</td>
						<th>�Ǹž�ü</th>
						<td colspan="2">
							<?= makeCompanySelectBoxAsCpNo($conn, '�Ǹ�', $cp_type2);?>
						</td>
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:144px;">
								<option value="TITLE" <? if ($order_field == "TITLE") echo "selected"; ?> >�׸�</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> ��������
						</td>
						<th>�˻�����</th>
						<td>
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
							</select>&nbsp;
							<input type="text" value="<?=$search_str?>" name="search_str" size="15"class="txt" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>

					<? if ($code == "goods") { ?>
					<col width="28%" />
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="5%" />
					<col width="7%"/>
					<col width="7%" />
					<col width="7%"/>
					<? } else { ?>
					<col width="10%" />
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<col width="9%" />
					<col width="9%"/>
					<? } ?>


				</colgroup>
				<thead>
					<tr>
						<th rowspan="2">�׸�</th>
						<th colspan="2">�ֹ��Ϸ�</th>
						<th colspan="2">��ۿϷ�</th>
						<th colspan="2">�ֹ����</th>
						<th colspan="2">���Ǹ�</th>
						<th rowspan="2">�Ǹ�����</th>
						<th rowspan="2" class="end">������</th>
					</tr>
					<tr>
						<th>����</th>
						<th>�հ�</th>
						<th>����</th>
						<th>�հ�</th>
						<th>����</th>
						<th>�հ�</th>
						<th>����</th>
						<th>�հ�</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							/*
							G_DATE,
							sum(TOT_ORDER_SALE_PRICE) AS TOT_ORDER_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) AS TOT_ORDER_SALE_QTY,
							sum(TOT_DELIVERY_SALE_PRICE) AS TOT_DELIVERY_SALE_PRICE, 
							sum(TOT_DELIVERY_SALE_QTY) AS TOT_DELIVERY_SALE_QTY,
							sum(TOT_CANCEL_SALE_PRICE) AS TOT_CANCEL_SALE_PRICE, 
							sum(TOT_CANCEL_SALE_QTY) AS TOT_CANCEL_SALE_QTY,
							sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE) AS TOT_SUN_SALE_PRICE, 
							sum(TOT_ORDER_SALE_QTY) - sum(TOT_CANCEL_SALE_QTY) AS TOT_SUN_SALE_QTY,
							(sum(TOT_ORDER_SALE_PRICE) - sum(TOT_CANCEL_SALE_PRICE)) -
							(sum(TOT_ORDER_BUY_PRICE) - sum(TOT_CANCEL_BUY_PRICE)) AS PLUS_PRICE,
							ROUND(((sum(TOT_ORDER_SALE_PRICE) - sum(TOT_ORDER_BUY_PRICE)) / 
							sum(TOT_ORDER_SALE_PRICE) * 100),2) AS LEE
							*/

							$G_DATE										= trim($arr_rs[$j]["G_DATE"]);
							$TOT_ORDER_SALE_PRICE			= trim($arr_rs[$j]["TOT_ORDER_SALE_PRICE"]);
							$TOT_ORDER_SALE_QTY				= trim($arr_rs[$j]["TOT_ORDER_SALE_QTY"]);
							$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs[$j]["TOT_DELIVERY_SALE_PRICE"]);
							$TOT_DELIVERY_SALE_QTY		= trim($arr_rs[$j]["TOT_DELIVERY_SALE_QTY"]);
							$TOT_CANCEL_SALE_PRICE		= trim($arr_rs[$j]["TOT_CANCEL_SALE_PRICE"]);
							$TOT_CANCEL_SALE_QTY			= trim($arr_rs[$j]["TOT_CANCEL_SALE_QTY"]);
							$TOT_SUN_SALE_PRICE				= trim($arr_rs[$j]["TOT_SUN_SALE_PRICE"]);
							$TOT_SUN_SALE_QTY					= trim($arr_rs[$j]["TOT_SUN_SALE_QTY"]);
							$PLUS_PRICE								= trim($arr_rs[$j]["PLUS_PRICE"]);
							$LEE											= trim($arr_rs[$j]["LEE"]);
							

						?>
						<tr height="37">
							<td class="modeual_nm"><?=$G_DATE?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_PRICE)?></td>
							<td class="price"><?=number_format($PLUS_PRICE)?></td>
							<td class="price"><?=$LEE?> %</td>
						</tr>
						<?
								}
							}

							if (sizeof($arr_rs_all) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs_all); $j++) {
									$TOT_ORDER_SALE_QTY				= trim($arr_rs_all[$j]["TOT_ORDER_SALE_QTY"]);
									$TOT_ORDER_SALE_PRICE			= trim($arr_rs_all[$j]["TOT_ORDER_SALE_PRICE"]);
									$TOT_DELIVERY_SALE_QTY		= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_QTY"]);
									$TOT_DELIVERY_SALE_PRICE	= trim($arr_rs_all[$j]["TOT_DELIVERY_SALE_PRICE"]);
									$TOT_CANCEL_SALE_QTY			= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_QTY"]);
									$TOT_CANCEL_SALE_PRICE		= trim($arr_rs_all[$j]["TOT_CANCEL_SALE_PRICE"]);
									$TOT_SUN_SALE_QTY					= trim($arr_rs_all[$j]["TOT_SUN_SALE_QTY"]);
									$TOT_SUN_SALE_PRICE				= trim($arr_rs_all[$j]["TOT_SUN_SALE_PRICE"]);
									$PLUS_PRICE								= trim($arr_rs_all[$j]["PLUS_PRICE"]);
									$LEE											= trim($arr_rs_all[$j]["LEE"]);
								}
							}
					?>
						<tr class="goods_end">
							<td colspan="11">
								&nbsp;
							</td>
						</tr>
						<tr height="37">
							<td>�հ�</td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_PRICE)?></td>
							<td class="price"><?=number_format($PLUS_PRICE)?></td>
							<td class="price"><?=$LEE?> %</td>
						</tr>
				</tbody>
			</table>

				<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>