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
	$menu_right = "SG007"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/stock/stock.php";
	
#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	if ($mode == "FIX") {
		$result_fix = fixStockGoods($conn, $goods_no, $cal_qty, $cal_fqty, $cal_bqty, $cal_tqty);
	}

	if ($order_field == "")
		$order_field = "B.GOODS_NAME";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize		= trim($nPageSize);

	$cp_type2		= trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

	
	$exclude_category = "";

#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================
	
	$where_cause = "";
	if(count($qty_type) > 0 && ($min_qty <> "" || $max_qty <> "")) { 

		$where_cause .= " AND (";

		for($i=0; $i < count($qty_type); $i++) { 
			
			if($min_qty != "" && $max_qty != "")
				$where_cause .= " (".$qty_type[$i]." BETWEEN ".$min_qty." AND ".$max_qty." ) ".$qty_type_conjunction;
			else if($min_qty != "" && $max_qty == "")
				$where_cause .= " (".$qty_type[$i]." >= ".$min_qty." ) ".$qty_type_conjunction;
			else if($min_qty == "" && $max_qty != "")
				$where_cause .= " (".$qty_type[$i]." <= ".$max_qty." ) ".$qty_type_conjunction;
			else 
				$where_cause .= "";
		}

		$where_cause = rtrim($where_cause, $qty_type_conjunction);
		$where_cause .= " ) ";
	}

	$filter = array('is_same' => $is_same, 'is_under_mstock' => $is_under_mstock, 'is_zero' => $is_zero, 'is_set' => $is_set );

	$nListCnt = totalCntStockTotalGoods($conn, $start_date, $end_date, $con_in_cp_no, $con_out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStockTotalGoods($conn, $start_date, $end_date, $con_in_cp_no, $con_out_cp_no, $con_cate, $where_cause, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });
  });
</script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">


	function js_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;

		var url = "stock_detail.php?mode=S&goods_no="+goods_no;
		NewWindow(url, '����', '950', '600', 'YES');

	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}

	
	function js_fix_stock(goods_no, cal_qty, cal_fqty, cal_bqty, cal_tqty) {

		var frm = document.frm;
		frm.goods_no.value = goods_no;
		frm.cal_qty.value = cal_qty;
		frm.cal_fqty.value = cal_fqty;
		frm.cal_bqty.value = cal_bqty;
		frm.cal_tqty.value = cal_tqty;
		frm.mode.value = "FIX";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	

	function js_modify_stock(goods_no) {

		var url = "stock_modify.php?goods_no="+goods_no;
		NewWindow(url, ' ������', '820', '613', 'YES');
	}
	function js_open_manual_stock_list(){
		NewWindow("manual_stock_list.php","manager/stock/manualStockList",800,600,"NO");
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="cal_qty" value="">
<input type="hidden" name="cal_bqty" value="">
<input type="hidden" name="cal_fqty" value="">
<input type="hidden" name="cal_tqty" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="depth" value="">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
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

				<h2>��� ��ȸ &nbsp; <input type="button" value="�������" onclick="js_open_manual_stock_list()"> </h2>
				
				<div class="category_choice"><!--<select style="width: 100px;"><option>��ǰ����</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="50" />
				</colgroup>
					<tr>
						<th>ī�װ�</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
					<tr>
						<th>���� ���� :</th>
						<td>
							<input type="checkbox" name="qty_type[]" <?=(in_array("TSTOCK_CNT", $qty_type) ? 'checked' : '')?> value="TSTOCK_CNT"/>�����
							<input type="checkbox" name="qty_type[]" <?=(in_array("STOCK_CNT", $qty_type) ? 'checked' : '')?> value="STOCK_CNT"/>�������
							<input type="checkbox" name="qty_type[]" <?=(in_array("FSTOCK_CNT", $qty_type) ? 'checked' : '')?> value="FSTOCK_CNT"/>�����
							<input type="checkbox" name="qty_type[]" <?=(in_array("BSTOCK_CNT", $qty_type) ? 'checked' : '')?> value="BSTOCK_CNT"/>�ҷ����

							<select name="qty_type_conjunction" style="width:60px;">
								<option <? if ($qty_type_conjunction == "AND") echo "selected"; ?> >AND</option>
								<option <? if ($qty_type_conjunction == "OR") echo "selected"; ?> >OR</option>
							</select>
						</td>
						<th>���� ���� :</th>
						<td colspan="2">
							�ּ� <input type="text" name="min_qty" style="width:60px;" value="<?=$min_qty?>"/>�� ~ �ִ� <input type="text" name="max_qty" style="width:60px;" value="<?=$max_qty?>"/>��
						</td>
					</tr>
					<tr>
						<th>��Ÿ  :</th>
						<td colspan="4">
							<label><input type="checkbox" name="is_under_mstock" <?=($is_under_mstock == "Y" ? 'checked' : '')?> value="Y"/>������� �̴� ��ǰ</label>
							<label><input type="checkbox" name="is_same" <?=($is_same == "N" ? 'checked' : '')?> value="N"/>��ǰ-����� ���� ��ǰ</label>
							<label><input type="checkbox" name="is_zero" <?=($is_zero == "Y" ? 'checked' : '')?> value="Y"/>������ 0�� ��ǰ����</label>
							<label><input type="checkbox" name="is_set" <?=($is_set == "Y" ? 'checked' : '')?> value="Y"/>��Ʈǰ ����</label>
						</td>
					</tr>
					<tr>
						<th>���� :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="B.GOODS_NAME" <? if ($order_field == "B.GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="B.TSTOCK_CNT" <? if ($order_field == "B.TSTOCK_CNT") echo "selected"; ?> >�����</option>
								<option value="B.STOCK_CNT" <? if ($order_field == "B.STOCK_CNT") echo "selected"; ?> >�������</option>
								<option value="B.FSTOCK_CNT" <? if ($order_field == "B.FSTOCK_CNT") echo "selected"; ?> >�����</option>
								<option value="B.BSTOCK_CNT" <? if ($order_field == "B.BSTOCK_CNT") echo "selected"; ?> >�ҷ����</option>
								<option value="B.MSTOCK_CNT" <? if ($order_field == "B.MSTOCK_CNT") echo "selected"; ?> >�������</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC"  || $order_str == "" ) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> ��������
						</td>

						<th>�˻����� :</th>
						<td colspan="2">
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="B.GOODS_CODE" <? if ($search_field == "B.GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="B.GOODS_NAME" <? if ($search_field == "B.GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						
					</tr>
				</tbody>
			</table>
				<div class="btnright">
					<b>��� ������ : </b><input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/> (�� ��������)
				</div>
			 <!--<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">�� ��� �ڻ� : <b><?=number_format(getStockAsset($conn))?> ��</b></div>-->
			<b>�� <?=$nListCnt?> ��</b> 
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="8%"/>
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%"/>
					<col width="7%" />
					<col width="11%" />
				</colgroup>
				<thead>
					<tr>
						<th>��ǰ�ڵ�</th>
						<th>��ǰ��</th>
						<th>�����</th>
						<th>�������</th>
						<th>�����</th>
						<th>�ҷ����</th>
						<th>�������</th>
						<th><b>�������</b></th>
						<th>����ȭ</th>
						<th class="end">�������</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					// echo sizeof($arr_rs);
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn									= trim($arr_rs[$j]["rn"]);
							$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$S_IN_QTY					= trim($arr_rs[$j]["S_IN_QTY"]);
							$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
							$S_IN_FQTY					= trim($arr_rs[$j]["S_IN_FQTY"]);
							$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
							$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
							$S_OUT_TQTY					= trim($arr_rs[$j]["S_OUT_TQTY"]);
							$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
							$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
							$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
							//$TSTOCK_CNT				= trim($arr_rs[$j]["TSTOCK_CNT"]);
							$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);
							$IS_SAME					= trim($arr_rs[$j]["IS_SAME"]);
							$IS_SET						= trim($arr_rs[$j]["IS_SET"]);

							
							$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);
							
							
							$CAL_QTY = $S_IN_QTY - $S_OUT_QTY;
							$CAL_BQTY = $S_IN_BQTY - $S_OUT_BQTY;
							$CAL_FQTY = $S_IN_FQTY; 
							$CAL_TQTY = - $TSTOCK_CNT;
							
							if ($CAL_QTY < $MSTOCK_CNT) {
								$str_goods_name = "<font color='red'>".$GOODS_NAME."</font> ";
							} else {
								$str_goods_name = $GOODS_NAME;
							}

							$AVAIL_STOCK = $CAL_TQTY + $CAL_QTY + $CAL_FQTY;

							//����ȭ ��ư ǥ�ø� ���� üũ
							$is_same = true;
							if ($CAL_TQTY != $TSTOCK_CNT || $CAL_QTY != $STOCK_CNT || $CAL_FQTY != $FSTOCK_CNT || $CAL_BQTY != $BSTOCK_CNT)
								$is_same = false;
				?>
					<tr height="37">
						<td class="modeual_nm"><?=$GOODS_CODE?></td>
						<td class="modeual_nm">
							<?
								if($IS_SET == "1")
									echo "<span style='color:red;'>(��Ʈ)</span>";
							?>
							<a href="javascript:js_view('<?=$GOODS_NO?>');"><?=$str_goods_name?></a>
						</td>
						<td class="price">
							<!--
							<?=number_format($CAL_TQTY)?>
							<? if ($CAL_TQTY != $TSTOCK_CNT) { ?>
							<font color="red">(<?=number_format($TSTOCK_CNT)?>)</a>
							<? } ?>
							-->
							<?=number_format($CAL_TQTY)?>
						</td>
						<td class="price">
							<?=number_format($CAL_QTY)?>
							<? if ($CAL_QTY != $STOCK_CNT) { ?>
							<font color="red">(<?=number_format($STOCK_CNT)?>)</a>
							<? } ?>
						</td>
						<td class="price">
							<?=number_format($CAL_FQTY)?>
							<? if ($CAL_FQTY != $FSTOCK_CNT) { ?>
							<font color="red">(<?=number_format($FSTOCK_CNT)?>)</a>
							<? } ?>
						</td>
						<td class="price">
							<?=number_format($CAL_BQTY)?>
							<? if ($CAL_BQTY != $BSTOCK_CNT) { ?>
							<font color="red">(<?=number_format($BSTOCK_CNT)?>)</a>
							<? } ?>
						</td>
						
						<td class="price"><?=number_format($MSTOCK_CNT)?></td>
						<td class="price"><b><?=number_format($AVAIL_STOCK)?></b></td>
						
						<td>
							<? if($IS_SAME == 'N' && $end_date == "") { ?>
							&nbsp; <input type="button" name="aa" value=" ����ȭ " class="btntxt" onclick="js_fix_stock('<?=$GOODS_NO?>','<?=$CAL_QTY?>','<?=$CAL_FQTY?>','<?=$CAL_BQTY?>','<?=$CAL_TQTY?>');"> 

							<script>
								//js_fix_stock('<?=$GOODS_NO?>','<?=$CAL_QTY?>','<?=$CAL_FQTY?>','<?=$CAL_BQTY?>','<?=$CAL_TQTY?>');
							</script>
							<? } ?>
						</td>
						<td>
							<input type="button" name="aa" value=" ����<->�ҷ� " class="btntxt" onclick="js_modify_stock('<?=$GOODS_NO?>');">
						</td>
					</tr>
					<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="10">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_out_cp_no=".$con_out_cp_no."&con_in_cp_no=".$con_in_cp_no."&qty_type_conjunction=".$qty_type_conjunction."&max_qty=".$max_qty."&min_qty=".$min_qty."&is_under_mstock=".$is_under_mstock."&is_zero=".$is_zero."&is_set=".$is_set."&con_cate=".$con_cate."&is_same=".$is_same."&end_date=".$end_date;

							if(sizeof($qty_type) > 0) { 
								foreach($qty_type as $t) { 
									$strParam .= "&qty_type%5B%5D=".$t;
								}
							}
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
				<br />

				<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">�� ����</a>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>