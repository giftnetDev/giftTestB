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
	$menu_right = "SG010"; // 메뉴마다 셋팅 해 주어야 합니다

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

	if ($order_field == "")
		$order_field = "B.GOODS_NAME";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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

#===============================================================
# Get Search list count
#===============================================================
	$con_cate = '';
	$nListCnt =totalCntStockGoods($conn, $con_cate, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStockGoods($conn, $con_cate, $con_in_cp_no, $con_out_cp_no, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
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
		
		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel_dn() {

		var frm = document.frm;
		
		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "base_stock_excel_list.php";
		frm.submit();
	}

	function js_excel_up() {
		var url = "base_stock_input.php";
		NewWindow(url, ' 기초재고등록', '820', '613', 'YES');
	}

	function js_reload() {
		location.reload();
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="cal_qty" value="">
<input type="hidden" name="cal_bqty" value="">
<input type="hidden" name="cal_fqty" value="">
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

				<h2>기초 재고 등록</h2>
				<div class="btnright">
					&nbsp;
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
					<col width="120" />
					<col width="*" />
					<col width="120" />
				</colgroup>
					<tr>
						<th>정렬 :</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="B.GOODS_NAME" <? if ($order_field == "B.GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="S_IN_QTY" <? if ($order_field == "S_IN_QTY") echo "selected"; ?> >정상재고</option>
								<option value="S_IN_BQTY" <? if ($order_field == "S_IN_BQTY") echo "selected"; ?> >불량재고</option>
								<option value="S_IN_FQTY" <? if ($order_field == "S_IN_FQTY") echo "selected"; ?> >가재고</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str =="" )) echo " checked"; ?>> 내림차순
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
								<option value="B.GOODS_CODE" <? if ($search_field == "B.GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="B.GOODS_NAME" <? if ($search_field == "B.GOODS_NAME") echo "selected"; ?> >상품명</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<!--<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>-->
						</td>
					</tr>
			</table>
			<div class="btnright">
				<input type="button" name="aa" value=" 기초재고등록용 엑셀받기 " class="btntxt" onclick="js_excel_dn();">&nbsp;&nbsp;&nbsp;
				<input type="button" name="aa" value=" 기초재고등록하기 " class="btntxt" onclick="js_excel_up();"> 
			</div>

			<b>총 <?=$nListCnt?> 건</b><span style="padding-left:20px"><font color="red"><b>기초재고를 등록하시면 이전 재고가 초기화 됩니다. 초기화 할 상품의 정상, 불량재고에만 수량 입력하시면 됩니다.</b></font></span>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="10%" />
					<col width="40%" />
					<col width="25%"/>
					<col width="25%" />
				</colgroup>
				<thead>
					<tr>
						<th>상품코드</th>
						<th>상품명</th>
						<th>정상재고</th>
						<th class="end">불량재고</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn									= trim($arr_rs[$j]["rn"]);
							$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$S_IN_QTY					= trim($arr_rs[$j]["S_IN_QTY"]);
							$S_IN_BQTY					= trim($arr_rs[$j]["S_IN_BQTY"]);
							$S_OUT_QTY					= trim($arr_rs[$j]["S_OUT_QTY"]);
							$S_OUT_BQTY					= trim($arr_rs[$j]["S_OUT_BQTY"]);
							$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
							$BSTOCK_CNT					= trim($arr_rs[$j]["BSTOCK_CNT"]);
							$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
							$MSTOCK_CNT					= trim($arr_rs[$j]["MSTOCK_CNT"]);
							
							$CAL_QTY = $S_IN_QTY - $S_OUT_QTY;
							$CAL_BQTY = $S_IN_BQTY - $S_OUT_BQTY;
							
							if ($CAL_QTY < $MSTOCK_CNT) {
								$str_goods_name = $GOODS_NAME;
								//$str_goods_name = "<font color='red'>".$GOODS_NAME."</font> ";
							} else {
								$str_goods_name = $GOODS_NAME;
							}
				?>
					<tr height="37">
						<td class="modeual_nm"><?=$GOODS_CODE?></td>
						<td class="modeual_nm"><a href="javascript:js_view('<?=$GOODS_NO?>');"><?=$str_goods_name?></a></td>
						<td class="price"><?=number_format($CAL_QTY)?></td>
						<td class="price"><?=number_format($CAL_BQTY)?></td>
					</tr>
					<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="9">데이터가 없습니다. </td>
						</tr>
					<?
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
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_out_cp_no=".$con_out_cp_no."&con_in_cp_no=".$con_in_cp_no;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />

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