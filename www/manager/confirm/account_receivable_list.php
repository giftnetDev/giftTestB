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
	$menu_right = "CF008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$con_sale_adm_no = $s_adm_no;
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y-m-d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	 #List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

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
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$filter = array('con_sale_adm_no' => $con_sale_adm_no, 'con_cp_type' => $con_cp_type, 'con_ad_type' => $con_ad_type);

	$nListCnt = totalCntAccountReceivable($conn, $start_date, $end_date, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listAccountReceivable($conn, $start_date, $end_date, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_sum = SumAccountReceivable($conn, $start_date, $end_date, $filter);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=2"></script>
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

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_search_date_by_code(code) {

		var frm = document.frm;

		if (code == "prev_month") {
			SetPrevMonthDays("start_date", "end_date");
		}

		if (code == "prev_week") {
			SetPrevWeek("start_date", "end_date");
		}

		if (code == "prev_day") {
			SetYesterday("start_date", "end_date");
		}

		if (code == "today") {
			SetToday("start_date", "end_date");
		}

		if (code == "this_week") {
			SetWeek("start_date", "end_date");
		}

		if (code == "this_month") {
			SetCurrentMonthDays("start_date", "end_date");
		}

		frm.method = "post";
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


	function js_link_to_company_ledger(cp_no) {

		var frm = document.frm;
		
		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no + "&start_date=" + frm.start_date.value + "&end_date=" + frm.end_date.value ,'_blank');
		
	}

	function js_link_to_company_info(cp_no) {

		var frm = document.frm;
		
		window.open("/manager/company/company_write.php?rn=&cp_no=" + cp_no + "&mode=S" ,'_blank');
		
	}
</script>
<style>
	.row_color_lev1 {background-color:#EFEFEF; font-weight:bold;}
	.row_color_lev2 {background-color:#DFDFDF; font-weight:bold;}
	.only_tax {display:none; color:blue;} 
</style> 
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="cl_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
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

				<h2>미수금 원장</h2>
				<div class="btnright">
					<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tr>
					<th>
						기간
					</th>
					<td colspan="3">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="start_date" name="start_date" value="<?=$start_date?>" maxlength="10"/>
						 ~ 
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" id="end_date" name="end_date" value="<?=$end_date?>" maxlength="10"/>
						&nbsp;
						<input type="button" value="전월" onclick="javascript:js_search_date_by_code('prev_month');"/>
						<input type="button" value="전주" onclick="javascript:js_search_date_by_code('prev_week');"/>
						<input type="button" value="전일" onclick="javascript:js_search_date_by_code('prev_day');"/>
						<input type="button" value="오늘" onclick="javascript:js_search_date_by_code('today');"/>
						<input type="button" value="금주" onclick="javascript:js_search_date_by_code('this_week');"/>
						<input type="button" value="금월" onclick="javascript:js_search_date_by_code('this_month');"/>
						
					</td>
					<td align="right">
					</td>
				</tr>
				<tr>
					<th>
						필터
					</th>
					<td colspan="3">
						<b>결제구분 : </b>
						<?= makeSelectBox($conn,"AD_TYPE","con_ad_type","125","전체","",$con_ad_type)?>
					</td>
					<td align="right">
					</td>
				</tr>
				<tr>
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:84px;">
							<option value="E.SUM_BALANCE" <? if ($order_field == "E.SUM_BALANCE") echo "selected"; ?> >미수금</option>
							<option value="O.CP_NM" <? if ($order_field == "O.CP_NM") echo "selected"; ?> >업체명</option>
						</select>&nbsp;&nbsp;
						<input type='radio' name='order_str' value='ASC' <? if (($order_str == "ASC")  || ($order_str == "")) echo " checked"; ?>> 오름차순 &nbsp;
						<input type='radio' class="" name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?> > 내림차순 
					</td>
					<th>검색조건</th>
					<td>
						<select name="nPageSize" style="width:84px;">
							<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
							<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
							<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
							<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
						</select>&nbsp;
						
						<select name="search_field" style="width:84px;">
							<option value="ALL" <? if ($search_field == "ALL" || $search_field == "") echo "selected"; ?> >통합검색</option>
							<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >업체코드</option>
							<option value="CP_NAME" <? if ($search_field == "CP_NAME") echo "selected"; ?> >업체명</option>
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					<td align="right">
						
					</td>
				</tr>
			</table>
			<div class="sp20"></div>

			<div>
				<span>총 <?=$nListCnt?> 건</span>
				<div style="float:right; margin-right:60px;"><label><input type="checkbox" id="show_only_tax" value="Y"/>계산서발행액</label></div>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="8%" />
					<col width="6%" />
					<col width="*" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
				<tr>
					<th><?= makeSelectBox($conn,"CP_TYPE","con_cp_type","75","업체구분","",$con_cp_type)?></th>
					<th>업체코드</th>
					<th>업체명</th>
					<th>이월잔액</th>
					<th>매출액</th>
					<th>입금액</th>
					<th>매입액</th>
					<th>지급액</th>
					<th>잔 액</th>
					<th>
						<? if ($s_adm_md_tf != "Y") { ?>
							<?= makeAdminInfoByMDSelectBox($conn,"con_sale_adm_no"," style='width:70px;' ","영업사원","", $con_sale_adm_no) ?>
						<? } else { ?>
							<input type="hidden" name="con_sale_adm_no" value="<?=$con_sale_adm_no?>"/>
							영업담당
						<? } ?>
					</th>
					<th class="end">보기</th>
				</tr>
				<script>
					$("select[name=con_cp_type], select[name=con_ad_type], select[name=con_sale_adm_no]").change(function(){
						js_search();
					});
				</script>
				</thead>
				<?
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$CP_TYPE					= trim($arr_rs[$j]["CP_TYPE"]);
							$CP_CODE					= trim($arr_rs[$j]["CP_CODE"]);
							$CP_NM						= trim($arr_rs[$j]["CP_NM"]);
							$CP_NM2						= trim($arr_rs[$j]["CP_NM2"]);
							$PREV_BALANCE				= trim($arr_rs[$j]["PREV_BALANCE"]);
							$SUM_SALES					= trim($arr_rs[$j]["SUM_SALES"]);
							$SUM_SALES_TAX				= trim($arr_rs[$j]["SUM_SALES_TAX"]);
							$SUM_COLLECT				= trim($arr_rs[$j]["SUM_COLLECT"]);
							$SUM_BUYING					= trim($arr_rs[$j]["SUM_BUYING"]);
							$SUM_BUYING_TAX				= trim($arr_rs[$j]["SUM_BUYING_TAX"]);
							$SUM_PAID					= trim($arr_rs[$j]["SUM_PAID"]);
							$SUM_BALANCE				= trim($arr_rs[$j]["SUM_BALANCE"]);
							$SALE_ADM_NO				= trim($arr_rs[$j]["SALE_ADM_NO"]);

							$SALE_ADM_NM = getAdminName($conn, $SALE_ADM_NO);

				?>
				<tr height="30">
					<!--<td><input type="checkbox" name="chk_no[]" value="<?=$CP_NO?>"/></td>-->
					<td><?=$CP_TYPE?></td>
					<td><a href="javascript:js_link_to_company_ledger('<?=$CP_NO?>')"><?=$CP_CODE?></a></td>
					<td class="modeual_nm"><a href="javascript:js_link_to_company_ledger('<?=$CP_NO?>')"><?=$CP_NM." ".$CP_NM2?></a></td>
					<td class="price"><?=getSafeNumberFormatted($PREV_BALANCE)?></td>
					<td class="price">
						<span class="include_tax"><?=getSafeNumberFormatted($SUM_SALES)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($SUM_SALES_TAX)?></span>
					</td>
					<td class="price"><?=getSafeNumberFormatted($SUM_COLLECT)?></td>
					<td class="price">
						<span class="include_tax"><?=getSafeNumberFormatted($SUM_BUYING)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($SUM_BUYING_TAX)?></span>
					</td>
					<td class="price"><?=getSafeNumberFormatted($SUM_PAID)?></td>
					<td class="price"><?=getSafeNumberFormatted($SUM_BALANCE)?></td>
					<td><?=$SALE_ADM_NM?></td>
					<td>
						<!--<input type="button" name="bb" value="원장" onclick="js_link_to_company_ledger('<?=$CP_NO?>');"/>&nbsp;-->
						<input type="button" name="bb" value="업체정보" onclick="js_link_to_company_info('<?=$CP_NO?>');"/>
					</td>
				</tr>

				<? 
						}
					} else { 
				?>

				<tr height="35">
					<td colspan="11">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				
				</div>

					
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&con_sale_adm_no=".$con_sale_adm_no."&con_cp_type=".$con_cp_type."&con_ad_type=".$con_ad_type."&order_field=".$order_field."&order_str=".$order_str;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
					
				<br />


				<?if($search_str == "") { ?>
				<div class="sp30"></div>

				<table cellpadding="0" cellspacing="0" class="rowstable" border="0">

				<colgroup>
					<col width="8%" />
					<col width="6%" />
					<col width="*" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="9%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
				</thead>
				<?
					if (sizeof($arr_rs_sum) > 0) {

						$ALL_TOTAL_PREV_BALANCE		= 0;
						$ALL_TOTAL_SUM_SALES		= 0;
						$ALL_TOTAL_SUM_SALES_TAX	= 0;
						$ALL_TOTAL_SUM_COLLECT		= 0;
						$ALL_TOTAL_SUM_BUYING		= 0;
						$ALL_TOTAL_SUM_BUYING_TAX	= 0;
						$ALL_TOTAL_SUM_PAID			= 0;
						$ALL_TOTAL_SUM_BALANCE		= 0;


						for ($k = 0 ; $k < sizeof($arr_rs_sum); $k++) {
							
							//CP_NO, CP_CODE, CP_NM, CP_NM2, PREV_BALANCE, SUM_SALES, SUM_COLLECT, SUM_BUYING, SUM_PAID, SUM_BALANCE, SALE_ADM_NO

							$TOTAL_PREV_BALANCE				= trim($arr_rs_sum[$k]["TOTAL_PREV_BALANCE"]);
							$TOTAL_SUM_SALES				= trim($arr_rs_sum[$k]["TOTAL_SUM_SALES"]);
							$TOTAL_SUM_SALES_TAX			= trim($arr_rs_sum[$k]["TOTAL_SUM_SALES_TAX"]);
							$TOTAL_SUM_COLLECT				= trim($arr_rs_sum[$k]["TOTAL_SUM_COLLECT"]);
							$TOTAL_SUM_BUYING				= trim($arr_rs_sum[$k]["TOTAL_SUM_BUYING"]);
							$TOTAL_SUM_BUYING_TAX			= trim($arr_rs_sum[$k]["TOTAL_SUM_BUYING_TAX"]);
							$TOTAL_SUM_PAID					= trim($arr_rs_sum[$k]["TOTAL_SUM_PAID"]);
							$TOTAL_SUM_BALANCE				= trim($arr_rs_sum[$k]["TOTAL_SUM_BALANCE"]);
							$GROUP_SALE_ADM_NO				= trim($arr_rs_sum[$k]["SALE_ADM_NO"]);

							

							$ALL_TOTAL_PREV_BALANCE		+= $TOTAL_PREV_BALANCE;
							$ALL_TOTAL_SUM_SALES		+= $TOTAL_SUM_SALES;
							$ALL_TOTAL_SUM_SALES_TAX	+= $TOTAL_SUM_SALES_TAX;
							$ALL_TOTAL_SUM_COLLECT		+= $TOTAL_SUM_COLLECT;
							$ALL_TOTAL_SUM_BUYING		+= $TOTAL_SUM_BUYING;
							$ALL_TOTAL_SUM_BUYING_TAX	+= $TOTAL_SUM_BUYING_TAX;
							$ALL_TOTAL_SUM_PAID			+= $TOTAL_SUM_PAID;
							$ALL_TOTAL_SUM_BALANCE		+= $TOTAL_SUM_BALANCE;
			
							$GROUP_SALE_ADM_NM = getAdminName($conn, $GROUP_SALE_ADM_NO);

							if(sizeof($arr_rs_sum) > 2) {

				?>
				<tr height="30" class="row_color_lev1">
					<td colspan="3" class="modeual_nm"><?=$GROUP_SALE_ADM_NM?> 합계 :</td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_PREV_BALANCE)?></td>
					<td class="price">
						<span class="include_tax"><?=getSafeNumberFormatted($TOTAL_SUM_SALES)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($TOTAL_SUM_SALES_TAX)?></span>
					</td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_SUM_COLLECT)?></td>
					<td class="price">
						<span class="include_tax"><?=getSafeNumberFormatted($TOTAL_SUM_BUYING)?></span>
						<span class="only_tax"><?=getSafeNumberFormatted($TOTAL_SUM_BUYING_TAX)?></span>
					</td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_SUM_PAID)?></td>
					<td class="price"><?=getSafeNumberFormatted($TOTAL_SUM_BALANCE)?></td>
					<td colspan="2"></td>
				</tr>

				<? 
							}
						}
				?>
				<tr height="30" class="row_color_lev2">
					<td  class="modeual_nm" colspan="3">전체 합계:</td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_PREV_BALANCE)?></td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_SALES)?></td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_COLLECT)?></td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_BUYING)?></td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_PAID)?></td>
					<td class="price"><?=getSafeNumberFormatted($ALL_TOTAL_SUM_BALANCE)?></td>
					<td colspan="2"></td>
				</tr>
				<?
					} else { 
				?>

				<tr height="35">
					<td colspan="11">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
			<? } ?>
			<div class="sp50"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<script type="text/javascript">
		$(function(){
			$("#show_only_tax").click(function(){
				$(".include_tax").toggle();
				$(".only_tax").toggle();
			});
		});
	
	</script>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
</div>
</form>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>