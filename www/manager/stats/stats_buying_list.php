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

	$menu_right = "ST010"; // 메뉴마다 셋팅 해 주어야 합니다


	if ($code == "goods") {
		$str_menu_title = "상품별 매입 현황";

	}

	if ($code == "company") {
		$str_menu_title = "업체별 매입 현황";

	}

	if ($code == "period") {
		$str_menu_title = "기간별 매입 현황";

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

	$nListCnt = totalCntStatsBuyingByCompanyLedger($conn, $code, $con_cate, $start_date, $end_date, $cp_type2, $sel_opt_manager_no, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	$arr_rs = listStatsBuyingByCompanyLedger($conn, $code, $con_cate, $start_date, $end_date, $cp_type2, $sel_opt_manager_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

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

	// 조회 버튼 클릭 시 
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

	function js_stats_by(code, goods_code) { 
		
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

		if(code == "period")
			frm.order_field.value = "INOUT_DATE";

		if(code == "company")
			frm.order_field.value = "TITLE";

		if(code == "goods")
			frm.order_field.value = "ORDER_TOTAL";

		frm.code.value = code;
		frm.search_field.value = "GOODS_CODE";
		frm.search_str.value = goods_code;


		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

    function js_view_ledger(code) { 
		var frm = document.frm;

		<? if ($code == "goods") {?>
		window.open("/manager/confirm/ledger_list.php?sel_inout_type=매입&search_date_type=ledger_date&start_date="+frm.start_date.value+"&end_date="+frm.end_date.value+"&search_field=NAME&search_str=" + code + "&cp_type=" + frm.cp_type2.value,'_blank');
		<? } ?>

		<? if ($code == "company") {?>
		window.open("/manager/confirm/ledger_list.php?sel_inout_type=매입&search_date_type=ledger_date&start_date="+frm.start_date.value+"&end_date="+frm.end_date.value+"&search_field=NAME&search_str=" + frm.search_str.value + "&cp_type=" + code,'_blank');
		<? } ?>

		<? if ($code == "period") {?>
		window.open("/manager/confirm/ledger_list.php?sel_inout_type=매입&search_date_type=ledger_date&start_date="+code+"-01&end_date="+code+"-31&search_field=NAME&search_str=" + frm.search_str.value + "&cp_type=" + frm.cp_type2.value,'_blank');
		<? } ?>
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
				<div style="text-align:right; width:95%">
					*. 원장 기준으로 통계가 작성 되었기 때문에 매입확인 = 기장일 입니다.
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="100" />
					<col width="*" />
					<col width="100" />
					<col width="*" />
					<col width="100" />
				</colgroup>
				<thead>
					<tr>
						<th>카테고리</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
						<td colspan="1" align="right">
							
						</td>
					</tr>
					
				</thead>
				<tbody>
					<tr>
						<th>조회기간</th>
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
							(매입확인/기장일 기준)
						</td>
						<td colspan="1" align="right">
							벤더가<input type="text" name="vendor_calc" value="35" class="txt" style="width:20px;"/> %<br/><a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
					<tr>
						<th>공급업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type2" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type2)?>" />
							<input type="hidden" name="cp_type2" value="<?=$cp_type2?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type2]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=cp_type2]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type2", data[0].label, "cp_type2", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type2&target_value=cp_type2",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_type2]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_type2]").val('');
										}
									});

								});

							</script>
							<script>
								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}
							</script>
						</td>
						<th>영업담당자</th>
						<td colspan="2">
							<?= makeAdminInfoByMDSelectBox($conn,"sel_opt_manager_no"," style='width:70px;' ","전체","", $sel_opt_manager_no) ?>
						</td>
						
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:144px;">
								<option value="ORDER_TOTAL" <? if ($order_field == "ORDER_TOTAL") echo "selected"; ?> >매입회수</option>
								<option value="QTY_TOTAL" <? if ($order_field == "QTY_TOTAL") echo "selected"; ?> >매입수량</option>
								<option value="PRICE_TOTAL" <? if ($order_field == "PRICE_TOTAL") echo "selected"; ?> >매입합계</option>
								<option value="INOUT_DATE" <? if ($order_field == "INOUT_DATE") echo "selected"; ?> >[기간별]기장일</option>
								<option value="TITLE" <? if ($order_field == "TITLE") echo "selected"; ?> >[업체별]업체명</option>
								<option value="CODE" <? if ($order_field == "CODE") echo "selected"; ?> >[업체별]업체코드</option>

							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?>> 내림차순
						</td>
						<th>검색조건</th>
						<td colspan="2">
							<select name="nPageSize" style="width:74px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;
							<input type="text" value="<?=$search_str?>" name="search_str" size="15"class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			<span>총 <?=number_format($nListCnt)?> 개</span>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="4%"/>
					<col width="7%" />
					<col width="*" />
					<col width="10%"/>
					<col width="10%"/>
					<col width="15%" />
					<col width="10%" />
					<col width="15%"/>
				</colgroup>
				<thead>
					<tr>
						<th>순위</th>
						<? if($code == "goods") { ?>
						<th>상품코드</th>
						<th>상품명</th>
						<? } else if($code == "company"){ ?>
						<th>업체코드</th>
						<th>업체명</th>
						<? } else if($code == "period"){ ?>
						<th>년도</th>
						<th>년도-월별</th>
						<? } ?>
						
						<th>매입회수</th>
						<th>매입수량</th>
						<th>매입합계</th>
						<th>최종 매입일</th>
						<th class="end">상세보기</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$KEY_CODE					= trim($arr_rs[$j]["KEY_CODE"]);
							$CODE						= trim($arr_rs[$j]["CODE"]);
							$TITLE						= trim($arr_rs[$j]["TITLE"]);
							$ORDER_TOTAL				= trim($arr_rs[$j]["ORDER_TOTAL"]);
							$QTY_TOTAL					= trim($arr_rs[$j]["QTY_TOTAL"]);
							$PRICE_TOTAL				= trim($arr_rs[$j]["PRICE_TOTAL"]);
							$LATEST_ORDER_DATE			= trim($arr_rs[$j]["LATEST_ORDER_DATE"]);
							
							$index = ((($nPage - 1) * $nPageSize) + ($j+1));
						?>
						<tr height="37">
							<td><?=$index?></td>
							<td class="modeual_nm">
								<?
									if($code == "goods") { 
								?>
								<a href="javascript:js_view_ledger('<?=$TITLE?>')"><?=$CODE?></a>
								<?
									}
								?>
								<?
									if($code == "company") { 
								?>
								<a href="javascript:js_view_ledger('<?=$KEY_CODE?>')"><?=$CODE?></a>
								<?
									}
								?>
								<?
									if($code == "period") { 
								?>
								<a href="javascript:js_view_ledger('<?=$TITLE?>')"><?=$CODE?></a>
								<?
									}
								?>
							</td>
							<td class="modeual_nm">
								<?
									if($code == "goods") { 
								?>
								<a href="javascript:js_view_ledger('<?=$TITLE?>')"><?=$TITLE?></a>
								<a href="/manager/goods/goods_list.php?search_field=GOODS_CODE&search_str=<?=$CODE?>" target="_blank" style="font-weight:bold; font-size:10px;">(보기)</a>
								<?
									}
								?>
								<?
									if($code == "company") { 
								?>
								<a href="javascript:js_view_ledger('<?=$KEY_CODE?>')"><?=$TITLE?></a>
								<a href="/manager/company/company_list.php?search_field=CP_NO&search_str=<?=$CODE?>" target="_blank" style="font-weight:bold; font-size:10px;">(보기)</a>
								<?
									}
								?>
								<?
									if($code == "period") { 
								?>
								<a href="javascript:js_view_ledger('<?=$TITLE?>')"><?=$TITLE?></a>
								<?
									}
								?>
							</td>
							<td class="price"><?=number_format($ORDER_TOTAL)?></td>
							<td class="price"><?=number_format($QTY_TOTAL)?></td>
							<td class="price"><?=number_format($PRICE_TOTAL)?></td>
							<td><?=$LATEST_ORDER_DATE?></td>
							<td>
								<? if($code == "goods") { ?>
								<input type="button" onclick="js_stats_by('company','<?=$CODE?>')" value="업체별"/> 
								<input type="button" onclick="js_stats_by('period','<?=$CODE?>')" value="기간별"/>
								<? } else if($code == "company"){ ?>
								<input type="button" onclick="js_stats_by('goods','')" value="상품전체로"/> 
								<? } else if($code == "period"){ ?>
								<input type="button" onclick="js_stats_by('goods','')" value="상품전체로"/>
								<? } ?>
							</td>
						</tr>
						<?
						}
					}

					$arr_sum = SumStatsBuyingByCompanyLedger($conn, $code, $con_cate, $start_date, $end_date, $cp_type2, $sel_opt_manager_no, $search_field, $search_str);

					if (sizeof($arr_sum) > 0) {
						for ($q = 0 ; $q < sizeof($arr_sum); $q++) {
							$SUM_ORDER_TOTAL				= trim($arr_sum[$q]["SUM_ORDER_TOTAL"]);
							$SUM_QTY_TOTAL					= trim($arr_sum[$q]["SUM_QTY_TOTAL"]);
							$SUM_PRICE_TOTAL				= trim($arr_sum[$q]["SUM_PRICE_TOTAL"]);
					?>
						<tr height="10">
							<td colspan="8"></td>
						</tr>
						<tr height="37">
							<td colspan="3">합계 : </td>
							<td class="price"><?=number_format($SUM_ORDER_TOTAL)?></td>
							<td class="price"><?=number_format($SUM_QTY_TOTAL)?></td>
							<td class="price"><?=number_format($SUM_PRICE_TOTAL)?></td>
							<td colspan="2"></td>
						</tr>
					<?
						}
					}
					?>
						
				</tbody>
			</table>
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&code=".$code."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam .= "&order_field=".$order_field."&order_str=".$order_str;
							$strParam .= "&goods_no=".$goods_no."&depth=".$depth."&old_sel_opt_manager_no=".$old_sel_opt_manager_no."&depth=".$depth."&gd_cate_01=".$gd_cate_01."&gd_cate_02=".$gd_cate_02."&gd_cate_03=".$gd_cate_03."&gd_cate_04=".$gd_cate_04."&con_cate=".$con_cate."&cp_type2=".$cp_type2."&sel_opt_manager_no=".$sel_opt_manager_no;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
				
				<div class="sp50"></div>
				
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
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