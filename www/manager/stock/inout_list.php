<?session_start();?>
<?
# =============================================================================
# File Name    : inout_list.php
# Modlue       : 
# Writer       : MIN SUNGWOOK
# Create Date  : 2015-11-03
# Modify Date  : 
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG012"; // 메뉴마다 셋팅 해 주어야 합니다

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

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-7 day"));
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

	$con_stock_code = trim($con_stock_code);
	$cp_type2 = trim($cp_type2);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
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
	
#	echo $start_date;
#	echo $end_date;

	//두 상태 다 확인
	//$con_close_tf = "N";

#===============================================================
# Get Search list count
#===============================================================

	//$nListCnt =totalCntStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $cp_type2, $con_out_cp_no, $sel_loc, $con_close_tf, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	//$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	//if ((int)($nTotalPage) < (int)($nPage)) {
	//	$nPage = $nTotalPage;
	//}

	$arr_rs_in  = listStatInStockGoods($conn, $start_date, $end_date, $cp_type2, $search_field, $search_str, $order_str);
	$arr_rs_out = listStatOutStockGoods($conn, $start_date, $end_date, $cp_type2, $search_field, $search_str, $order_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="../jquery/theme.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
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

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
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
<input type="hidden" name="stock_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
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

				<h2>입출고 현황</h2>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

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
						<th>기준일 :</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
						</td>
						<th>업체 :</th>
						<td colspan="2">
							<?=makeCompanySelectBoxWithName($conn, 'cp_type2', '', $cp_type2)?>
						</td>
						
						
					</tr>
				</thead>
				<tbody>
					<!--tr>
						<th>입고사유 :</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"LOC","sel_loc","125","선택","",$sel_loc)?>
						</td>
						<th>입고구분 :</th>
						<td colspan="2">
							<?= makeSelectBox($conn, 'IN_ST','con_stock_code',"125", "선택", "", $con_stock_code);?>
						</td>					
					</tr -->
					<tr>
						<th>정렬 :</th>
						<td>
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건 :</th>
						<td>
							<select name="nPageSize" style="width:84px;">
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

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
			  <script>
			  $(function() {
				$( "#tabs" ).tabs();
			  });
			  </script>
			<div id="tabs" style="width:95%;">
			  <ul>
				<li><a href="#tabs-1">입고</a></li>
				<li><a href="#tabs-2">출고</a></li>
			  </ul>
			  <div id="tabs-1">
				  <table cellpadding="0" cellspacing="0" class="rowstable" border="0">
					<colgroup>
						<col width="10%" />
						<col width="20%" />
						<col width="*"/>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>입고일</th>
							<th>업체명</th>
							<th>상품명</th>
							<th>정상수량합계</th>
							<th>불량수량합계</th>
							<th class="end">가수량합계</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs_in) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs_in); $j++) {
								
								$IN_DATE						= trim($arr_rs_in[$j]["IN_DATE"]);
								$GOODS_NAME						= SetStringFromDB(trim($arr_rs_in[$j]["GOODS_NAME"]));
								$GOODS_CODE						= SetStringFromDB(trim($arr_rs_in[$j]["GOODS_CODE"]));
								$SUM_IN_QTY							= trim($arr_rs_in[$j]["SUM_IN_QTY"]);
								$SUM_IN_BQTY						= trim($arr_rs_in[$j]["SUM_IN_BQTY"]);
								$SUM_IN_FQTY						= trim($arr_rs_in[$j]["SUM_IN_FQTY"]);
								$IN_CP_NO						= trim($arr_rs_in[$j]["IN_CP_NO"]);
								
								$IN_DATE			= date("Y-m-d",strtotime($IN_DATE));

					?>
						<tr height="37">
							<td><?=$IN_DATE?></a></td>
							<td class="modeual_nm"><?= getCompanyName($conn, $IN_CP_NO);?></td>
							<td class="modeual_nm"><?= $GOODS_NAME?> [<?=$GOODS_CODE?>]</td>
							<td class="price"><?=number_format($SUM_IN_QTY)?></td>
							<td class="price"><?=number_format($SUM_IN_BQTY)?></td>
							<td class="price"><?=number_format($SUM_IN_FQTY)?></td>
						</tr>
						<?
										
							}

						?>

						<?

						}else{
							?>
							<tr class="order">
								<td height="50" align="center" colspan="6">데이터가 없습니다. </td>
							</tr>
						<?
							}
						?>
					</tbody>
				</table>
			  </div>
			  <div id="tabs-2">
				  <table cellpadding="0" cellspacing="0" class="rowstable" border="0">
					<colgroup>
						<col width="10%" />
						<col width="20%" />
						<col width="*"/>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>출고일</th>
							<th>업체명</th>
							<th>상품명</th>
							<th>정상수량합계</th>
							<th>불량수량합계</th>
							<th class="end">선수량합계</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs_out) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs_out); $j++) {
								
								$OUT_DATE						= trim($arr_rs_out[$j]["OUT_DATE"]);
								$GOODS_NAME						= SetStringFromDB(trim($arr_rs_out[$j]["GOODS_NAME"]));
								$GOODS_CODE						= SetStringFromDB(trim($arr_rs_out[$j]["GOODS_CODE"]));
								$SUM_OUT_QTY							= trim($arr_rs_out[$j]["SUM_OUT_QTY"]);
								$SUM_OUT_BQTY						= trim($arr_rs_out[$j]["SUM_OUT_BQTY"]);
								$SUM_OUT_TQTY						= trim($arr_rs_out[$j]["SUM_OUT_TQTY"]);
								$OUT_CP_NO						= trim($arr_rs_out[$j]["OUT_CP_NO"]);
								
								$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));

					?>
						<tr height="37">
							<td><?=$OUT_DATE?></a></td>
							<td class="modeual_nm"><?= getCompanyName($conn, $OUT_CP_NO);?></td>
							<td class="modeual_nm"><?= $GOODS_NAME?> [<?=$GOODS_CODE?>]</td>
							<td class="price"><?=number_format($SUM_OUT_QTY)?></td>
							<td class="price"><?=number_format($SUM_OUT_BQTY)?></td>
							<td class="price"><?=number_format($SUM_OUT_TQTY)?></td>
						</tr>
						<?
										
							}

						?>

						<?

						}else{
							?>
							<tr class="order">
								<td height="50" align="center" colspan="6">데이터가 없습니다. </td>
							</tr>
						<?
							}
						?>
					</tbody>
				</table>
			  </div>
			</div>
			<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
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