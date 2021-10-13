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

	$menu_right = "ST006"; // �޴����� ���� �� �־�� �մϴ�


	function listStatsSaleCompany($db, $start_date, $end_date, $order_field, $order_str, $nRowCount) { 

		$query = "
					SELECT  @rownum:=@rownum+1 AS rownum,A.*
					FROM
					(
						SELECT CP_CODE, CP_NM, ORDER_COUNT, SUM_DEPOSIT
						  FROM (

								SELECT CP_NO, COUNT( * ) AS ORDER_COUNT, SUM( SUM_DEPOSIT_BY_RESERVE ) AS SUM_DEPOSIT
								FROM (

										SELECT CP_NO, RESERVE_NO, SUM( DEPOSIT ) AS SUM_DEPOSIT_BY_RESERVE
										FROM  `TBL_COMPANY_LEDGER` 
										WHERE USE_TF =  'Y'
										AND DEL_TF =  'N'
										AND INOUT_TYPE =  '����'
										AND CATE_01 =  '' ";
		
		if($start_date <> "") 
			$query .= "				    AND INOUT_DATE >= '".$start_date." 00:00:00' ";
		
		if($end_date <> "") 
			$query .= "					AND INOUT_DATE <= '".$end_date." 23:59:59' ";
		
		$query .= "	
										GROUP BY CP_NO, RESERVE_NO
									)A
								GROUP BY CP_NO
								)B
								JOIN TBL_COMPANY C ON B.CP_NO = C.CP_NO
				 ";
		
		if ($order_field == "") 
			$order_field = "ORDER_COUNT";

		if ($order_str == "") 
			$order_str = "DESC";

		$query .= " ORDER BY ".$order_field." ".$order_str." limit 0, ".$nRowCount;
		$query .= ") A,(SELECT @rownum:=0) r ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;

	}

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


#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listStatsSaleCompany($db, $start_date, $end_date, $order_field, $order_str, $nRowCount);

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

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;

		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}



</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="code" value="<?=$code?>">
<input type="hidden" name="goods_no" value="<?=$goods_no?>">
<input type="hidden" name="depth" value="">
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
					<col width="50" />
				</colgroup>
				<thead>
					<tr>
						<th>ī�װ�</th>
						<td colspan="4">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>��ȸ�Ⱓ</th>
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							<!--&nbsp;
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_month');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_week');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('prev_day');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('today');"/>
							<input type="button" value="����" onclick="javascript:js_search_date_by_code('this_week');"/>
							<input type="button" value="�ݿ�" onclick="javascript:js_search_date_by_code('this_month');"/>-->
						</td>
						
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:144px;">
								<option value="ORDER_COUNT" <? if ($order_field == "ORDER_COUNT") echo "selected"; ?> >�ֹ�ȸ��</option>
								<option value="SUM_DEPOSIT" <? if ($order_field == "SUM_DEPOSIT") echo "selected"; ?> >�ֹ��հ�</option>
								<option value="PRICE_TOTAL" <? if ($order_field == "PRICE_TOTAL") echo "selected"; ?> >�ֹ��հ�</option>
								<option value="INOUT_DATE" <? if ($order_field == "INOUT_DATE") echo "selected"; ?> >[�Ⱓ��]������</option>
								<option value="TITLE" <? if ($order_field == "TITLE") echo "selected"; ?> >[��ü��]��ü��</option>
								<option value="CODE" <? if ($order_field == "CODE") echo "selected"; ?> >[��ü��]��ü�ڵ�</option>

							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?>> ��������
						</td>
						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:74px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<!--
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
							</select>&nbsp;
							<input type="text" value="<?=$search_str?>" name="search_str" size="15"class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							-->
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<span>�� <?=number_format($nListCnt)?> ��</span>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="4%"/>
					<col width="10%" />
					<col width="*" />
					<col width="10%"/>
					<col width="10%"/>
					<col width="15%" />
					<col width="15%"/>
				</colgroup>
				<thead>
					<tr>
						<th>����</th>
						<th>��ü�ڵ�</th>
						<th>��ü��</th>
						
						<th>�ֹ�ȸ��</th>
						<th>�ֹ��հ�</th>
						<th class="end">�󼼺���</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$CODE						= trim($arr_rs[$j]["CODE"]);
							$TITLE						= trim($arr_rs[$j]["TITLE"]);
							$ORDER_TOTAL				= trim($arr_rs[$j]["ORDER_TOTAL"]);
							$QTY_TOTAL					= trim($arr_rs[$j]["QTY_TOTAL"]);
							$PRICE_TOTAL				= trim($arr_rs[$j]["PRICE_TOTAL"]);
							
							$index = ((($nPage - 1) * $nPageSize) + ($j+1));
						?>
						<tr height="37">
							<td><?=$index?></td>
							<td class="modeual_nm"><?=$CODE?></td>
							<td class="modeual_nm"><?=$TITLE?></td>
							<td class="price"><?=number_format($ORDER_TOTAL)?></td>
							<td class="price"><?=number_format($QTY_TOTAL)?></td>
							<td class="price"><?=number_format($PRICE_TOTAL)?></td>
							<td>
								<? if($code == "goods") { ?>
								<input type="button" onclick="js_stats_by('company','<?=$CODE?>')" value="��ü��"/> 
								<input type="button" onclick="js_stats_by('period','<?=$CODE?>')" value="�Ⱓ��"/>
								<? } else if($code == "company"){ ?>
								<input type="button" onclick="js_stats_by('goods','')" value="��ǰ��ü��"/> 
								<? } else if($code == "period"){ ?>
								<input type="button" onclick="js_stats_by('goods','')" value="��ǰ��ü��"/>
								<? } ?>
							</td>
						</tr>
						<?
						}
					}

					$arr_sum = SumStatsByCompanyLedger($conn, $code, $con_cate, $start_date, $end_date, $cp_type2, $sel_opt_manager_no, $search_field, $search_str);

					// SUM_ORDER_TOTAL,  SUM_QTY_TOTAL,  SUM_PRICE_TOTAL
					if (sizeof($arr_sum) > 0) {
						for ($q = 0 ; $q < sizeof($arr_sum); $q++) {
							$SUM_ORDER_TOTAL				= trim($arr_sum[$q]["SUM_ORDER_TOTAL"]);
							$SUM_QTY_TOTAL					= trim($arr_sum[$q]["SUM_QTY_TOTAL"]);
							$SUM_PRICE_TOTAL				= trim($arr_sum[$q]["SUM_PRICE_TOTAL"]);
					?>
						<tr height="10">
							<td colspan="7"></td>
						</tr>
						<tr height="37">
							<td colspan="3">�հ� : </td>
							<td class="price"><?=number_format($SUM_ORDER_TOTAL)?></td>
							<td class="price"><?=number_format($SUM_QTY_TOTAL)?></td>
							<td class="price"><?=number_format($SUM_PRICE_TOTAL)?></td>
							<td></td>
						</tr>
					<?
						}
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
							$strParam = $strParam."&nPageSize=".$nPageSize."&code=".$code."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam .= "&order_field=".$order_field."&order_str=".$order_str;
							$strParam .= "&goods_no=".$goods_no."&depth=".$depth."&old_sel_opt_manager_no=".$old_sel_opt_manager_no."&depth=".$depth."&gd_cate_01=".$gd_cate_01."&gd_cate_02=".$gd_cate_02."&gd_cate_03=".$gd_cate_03."&gd_cate_04=".$gd_cate_04."&con_cate=".$con_cate."&txt_cp_type2=".$txt_cp_type2."&sel_opt_manager_no=".$sel_opt_manager_no;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
				<br />

				<div class="sp50"></div>
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