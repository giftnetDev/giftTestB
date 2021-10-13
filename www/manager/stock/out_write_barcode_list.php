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
	$menu_right = "SG016"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);
	
	$result	= false  ;

	$mm_subtree	 = "3";

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$end_date = trim($end_date);
	}


#====================================================================
# DML Process
#====================================================================
	

	$arr_rs = listOrderGoodsScan_LEVEL1($conn, $start_date, $end_date, $cp_no, $search_field, $search_str);

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
<script type="text/javascript" src="../jquery/jquery-barcode.min.js"></script>
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

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
</script>

</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
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

				<h2>출고 등록 - 바코드 리스트</h2>
				<div class="btnright">
					&nbsp;
				</div>

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
						<th>출고일 :</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
						</td>
						<th>주문업체 :</th>
						<td colspan="2">
							<?= makeCompanySelectBoxCompanyNameValue($conn, '판매', $cp_no);?>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>

						<th>검색조건 :</th>
						<td colspan="3">
							<select name="search_field" style="width:84px;">
								<option value="DELIVERY_NO" <? if ($search_field == "DELIVERY_NO") echo "selected"; ?> >송장번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<!--<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>-->
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp10"></div>
			<table cellpadding="0" cellspacing="0" class="rowstable" border="0">
				<colgroup>
					<col width="10%" />
					<col width="10%" />
					<col width="10%"/>
					<col width="50%"/>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>판매회사</th>
						<th>송장번호</th>
						<th>상품코드</th>
						<th>상품명</th>
						<th>주문상품수</th>
						<th>스캔상품수</th>
						<th class="end">등록일</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CP_NM , DELIVERY_NO, GOODS_NO, KANCODE, KANCODE_BOX, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, DELIVERY_CNT_IN_BOX, GOODS_TOTAL, SCAN_CNT
							
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);

							$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);
							$GROUP_CNT					= trim($arr_rs[$j]["GROUP_CNT"]);
							$each_cnt = $GROUP_CNT;

							$nCnt++;

							$arr_rs2 = listOrderGoodsScan_LEVEL2($conn, $start_date, $end_date, $cp_no, $DELIVERY_NO, $search_field, $search_str);

							
							for ($k = 0 ; $k < sizeof($arr_rs2); $k++) {

								$GOODS_CODE			= trim($arr_rs2[$k]["GOODS_CODE"]);
								$GOODS_NAME			= trim($arr_rs2[$k]["GOODS_NAME"]);
								$GOODS_SUB_NAME		= trim($arr_rs2[$k]["GOODS_SUB_NAME"]);

								$GOODS_TOTAL		= trim($arr_rs2[$k]["GOODS_TOTAL"]);
								$SCAN_CNT			= trim($arr_rs2[$k]["SCAN_CNT"]);

								$REG_DATE			= trim($arr_rs2[$k]["REG_DATE"]);
								$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							
				?>
					<tr height="37">
						<? if($GROUP_CNT > 1 && $GROUP_CNT == $each_cnt) { ?>
							<td class="modeual_nm" rowspan="<?=$GROUP_CNT?>"><?=$CP_NO?></td>
							<td rowspan="<?=$GROUP_CNT?>"><?=$DELIVERY_NO?></td>
						<? 
							} else if($GROUP_CNT == 1) { 
						?>
							<td class="modeual_nm"><?=$CP_NO?></td>
							<td><?=$DELIVERY_NO?></td>
						<? } 
						?>
						<td><?=$GOODS_CODE?></td>
						<td class="modeual_nm"><?=$GOODS_NAME." ".$GOODS_SUB_NAME?></td>
						<td><?=$GOODS_TOTAL?></td>
						<td><?=$SCAN_CNT?></td>
						<td><?=$REG_DATE?></td>
					</tr>
					<?
								$each_cnt--;
							}	
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="7">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

				<div class="sp10"></div>
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