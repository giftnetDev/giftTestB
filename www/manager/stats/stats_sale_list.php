<?session_start();?>
<?
# =============================================================================
# File Name    : stats_delivery_list.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

	$menu_right = "ST007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/stats/stats.php";


#====================================================================
# Request Parameter
#====================================================================

	$specific_date = $sel_year."-".str_pad($sel_month,2,"0",STR_PAD_LEFT)."-01";
#============================================================
# Page process
#============================================================
	
	function listWeekdayOfSpecificWeek($db, $specific_date) { 

		$query = "
					SELECT SELECTED_WEEK.WEEK_OF_YEAR, 
							DATE_ADD( SELECTED_WEEK.SAT, INTERVAL -5 DAY ) AS MON, 
							DATE_ADD( SELECTED_WEEK.SAT, INTERVAL -4 DAY ) AS TUE, 
							DATE_ADD( SELECTED_WEEK.SAT, INTERVAL -3 DAY ) AS WED, 
							DATE_ADD( SELECTED_WEEK.SAT, INTERVAL -2 DAY ) AS THU, 
							DATE_ADD( SELECTED_WEEK.SAT, INTERVAL -1 DAY ) AS FRI,
							SELECTED_WEEK.SAT,
							WEEKDAY( '$specific_date' ) AS WEEK_DAY
							
					FROM (
						SELECT WEEK( '$specific_date' , 1 ) AS WEEK_OF_YEAR, 
						DATE_ADD( '$specific_date' , INTERVAL 5 - WEEKDAY( '$specific_date' ) DAY ) AS SAT
					) AS SELECTED_WEEK;
				";


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


	function priceOrderGoods($db, $start_datetime, $end_datetime, $show_type, $opt_manager_no, $cate_01) { 

		//IFNULL(SUM(CASE WHEN OG.ORDER_STATE <=3 THEN OG.QTY ELSE -1 * OG.QTY END * OG.SALE_PRICE - CASE WHEN OG.CATE_01 = '' OR OG.CATE_01 = '추가' THEN OG.DISCOUNT_PRICE ELSE 0 END), 0)

		if($show_type == "") 
			$show_type = "STAT_TOTAL_SALE";

		$query = "
					SELECT IFNULL(SUM(".$show_type."), 0)
						FROM TBL_ORDER O
						JOIN TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
						WHERE O.USE_TF =  'Y'
						AND O.DEL_TF =  'N'
						AND OG.USE_TF =  'Y'
						AND OG.DEL_TF =  'N'
						AND OG.ORDER_DATE >=  '$start_datetime'
						AND OG.ORDER_DATE <=  '$end_datetime'
				";

		if($opt_manager_no <> "")
			$query .= " AND O.OPT_MANAGER_NO = '$opt_manager_no' ";
		else
			$query .= " AND O.OPT_MANAGER_NO <> 0 ";

		$query .= " AND OG.CATE_01 = '$cate_01' ";

		//echo $query."<br/>";
		//exit;

		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$record  = $rows[0];
		return $record;

	}

	function getWeeks($month, $year){
			$lastday = date("t", mktime(0, 0, 0, $month, 1, $year)); 
			$no_of_weeks = 0; 
			$count_weeks = 0; 
			while($no_of_weeks < $lastday){ 
				$no_of_weeks += 7; 
				$count_weeks++; 
			} 
		return $count_weeks;
	} 


	function getColorRate($rate) { 

		$str_color = "";
		if($rate >= 80)
			$str_color = "#5cf442";
		else if($rate >= 50 && $rate < 80)
			$str_color = "#aaf442";
		else if($rate < 50)
			$str_color = "#ebf442";

		return $str_color;

	}
#===============================================================
# Get Search list count
#===============================================================

	// 주문상품 판매액 계산 
	updateOrderGoodsTotalSale($conn, $re_calc);
	


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

				<h2>종합 현황</h2>
				<div class="btnright">&nbsp;</div>
				<div class="category_choice">&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<tbody>
					
					<tr>
						<th>필터</th>
						<td colspan="3">
							<b>영업담당자 :</b>
							<?if($s_adm_group_no <> "6") { //2016-09-02 영업부는 자기 주문만 보게 수정?>
								<?= makeAdminInfoByMDSelectBox($conn,"opt_manager_no"," style='width:70px;' ","전체","", $opt_manager_no) ?>
							<? } else { ?>
								<input type="hidden" name="opt_manager_no" value="<?=$opt_manager_no?>"/>
								<?=getAdminName($conn,$opt_manager_no)?>
							<? } ?>
							<b>주문상품종류 :</b>
							<?=makeSelectBox($conn, "ORDER_GOODS_TYPE", "cate_01", "100", "일반", "", $cate_01)?>
						</td>
						<td><label><input type="checkbox" name="re_calc" value="Y"/>통계 재 계산</label></td>
					</tr>
					<tr>
						<th>조회기준</th>
						<td>
							<!--
							<select name="sel_year">
								<option value="" <?if($sel_year == "") echo "selected";?>>전체</option>
								<? for($y = 2016; $y <= date('Y'); $y++) { ?>
								<option value="<?=$y?>" <?if($sel_year == $y) echo "selected";?>><?=$y?></option>
								<? } ?>
							</select>
							
							
							<select name="sel_month">
								<option value="" <?if($sel_month == "") echo "selected";?>>전체</option>
								<option value="1" <?if($sel_month == "1") echo "selected";?>>1</option>
								<option value="2" <?if($sel_month == "2") echo "selected";?>>2</option>
								<option value="3" <?if($sel_month == "3") echo "selected";?>>3</option>
								<option value="4" <?if($sel_month == "4") echo "selected";?>>4</option>
								<option value="5" <?if($sel_month == "5") echo "selected";?>>5</option>
								<option value="6" <?if($sel_month == "6") echo "selected";?>>6</option>
								<option value="7" <?if($sel_month == "7") echo "selected";?>>7</option>
								<option value="8" <?if($sel_month == "8") echo "selected";?>>8</option>
								<option value="9" <?if($sel_month == "9") echo "selected";?>>9</option>
								<option value="10" <?if($sel_month == "10") echo "selected";?>>10</option>
								<option value="11" <?if($sel_month == "11") echo "selected";?>>11</option>
								<option value="12" <?if($sel_month == "12") echo "selected";?>>12</option>
							</select>
							-->
							<select name="show_type">
								<option value="STAT_TOTAL_SALE" <?if($show_type == "STAT_TOTAL_SALE") echo "selected"?>>판매총액</option>
								<option value="STAT_TOTAL_EXPENSE" <?if($show_type == "STAT_TOTAL_EXPENSE") echo "selected"?>>매입총액</option>
								<option value="STAT_TOTAL_MAJIN" <?if($show_type == "STAT_TOTAL_MAJIN") echo "selected"?>>마진총액</option>
							</select>
						</td>
						<th>조회</th>
						<td>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
						</td>
					</tr>
				</tbody>
			</table>

		
			<!--------------------   년 매출    -------------------------------------------->
			<div class="sp20"></div>
			<div style="width:95%; text-align:right;">단위 : 천원</div>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="*" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="7%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th>매출액</th>
						<th>1월</th>
						<th>2월</th>
						<th>3월</th>
						<th>4월</th>
						<th>5월</th>
						<th>6월</th>
						<th>7월</th>
						<th>8월</th>
						<th>9월</th>
						<th>10월</th>
						<th>11월</th>
						<th>12월</th>
						<th class="end">합계</th>
					</tr>
				</thead>
				<tbody>
					<?
						$cntOrderGoods[] = array();

						for($y = 2016; $y <= date("Y",strtotime("0 month")); $y++) { 
							$cntOrderGoods_total = 0;
							$cntOrderGoods[$y][1] = priceOrderGoods($conn, $y."-01-01 00:00:00", $y."-01-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][2] = priceOrderGoods($conn, $y."-02-01 00:00:00", $y."-02-29 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][3] = priceOrderGoods($conn, $y."-03-01 00:00:00", $y."-03-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][4] = priceOrderGoods($conn, $y."-04-01 00:00:00", $y."-04-30 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][5] = priceOrderGoods($conn, $y."-05-01 00:00:00", $y."-05-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][6] = priceOrderGoods($conn, $y."-06-01 00:00:00", $y."-06-30 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][7] = priceOrderGoods($conn, $y."-07-01 00:00:00", $y."-07-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][8] = priceOrderGoods($conn, $y."-08-01 00:00:00", $y."-08-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][9] = priceOrderGoods($conn, $y."-09-01 00:00:00", $y."-09-30 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][10] = priceOrderGoods($conn, $y."-10-01 00:00:00", $y."-10-31 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][11] = priceOrderGoods($conn, $y."-11-01 00:00:00", $y."-11-30 23:59:59", $show_type, $opt_manager_no, $cate_01);
							$cntOrderGoods[$y][12] = priceOrderGoods($conn, $y."-12-01 00:00:00", $y."-12-31 23:59:59", $show_type, $opt_manager_no, $cate_01);

					?>
					<tr height="30">
						<td><?=$y?></td>
						<?
							for($m = 1; $m <= 12; $m++) {  

								$cntOrderGoods_total += $cntOrderGoods[$y][$m];

						?>
						<td title="<?=number_format($cntOrderGoods[$y][$m])?>"><?=number_format($cntOrderGoods[$y][$m]/1000.0)?></td>
						
						<? } ?>
						<td title="<?=number_format($cntOrderGoods_total)?>"><?=number_format($cntOrderGoods_total/1000.0)?></td>
					</tr>
					<? 
						}
						
					?>

				</tbody>
			</table>
			
			<?
				for($m = 1; $m <= 12; $m++) {  
					$row_script .= "[".$m.",";
					for($y = 2016; $y <= date("Y",strtotime("0 month")); $y++) { 
						$row_script .= $cntOrderGoods[$y][$m].",";
					}
					$row_script = RTRIM($row_script, ",");
					$row_script .= "],";
				}
				$row_script = RTRIM($row_script, ",");

			?>
			<div class="sp20"></div>
			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			<script type="text/javascript">
				google.charts.load('current', {'packages':['line']});
				google.charts.setOnLoadCallback(drawChart);

				function drawChart() {

				  var data = new google.visualization.DataTable();
				  data.addColumn('number', '월');

				  <? for($y = 2016; $y <= date("Y",strtotime("0 month")); $y++) { ?>
				  data.addColumn('number', '<?=$y?>');
				  <? } ?>

				  data.addRows([<?=$row_script?>]);

				  var options = {
					chart: {
					},
					width: 900,
					height: 500,
					axes: {
					  x: {
						0: {side: 'top'}
					  }
					}
				  };

				  var chart = new google.charts.Line(document.getElementById('line_top_x'));

				  chart.draw(data, google.charts.Line.convertOptions(options));
				}
			</script>
			<div id="line_top_x"></div>

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