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
	$menu_right = "SG025"; // 메뉴마다 셋팅 해 주어야 합니다

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


#============================================================
# Page process
#============================================================


#===============================================================
# Get Search list count
#===============================================================


	$arr_rs = listEachStockHistory($conn, $warehouse, $start_date, $end_date);

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
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
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
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
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

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('정말로 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
	

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

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

				<h2>개별 창고 입/출고 관리</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="100" />
					<col width="*" />
					<col width="100" />
					<col width="*" />
					<col width="120" />
				</colgroup>
				<thead>
					<tr>
						<th>입/출고일 :</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
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
								<option value="100000" <? if ($nPageSize == "100000") echo "selected"; ?> >기록전체(주의)</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
							</select>&nbsp; 

							<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<!--a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a-->
						</td>
						
					</tr>
				</thead>
			</table>
			<div class="sp20"></div>
			<b>총 <?=$nListCnt?> 건</b>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="2%" />
					<col width="6%" />
					<col width="8%" />
					<col width="*" />
					<col width="7%" />
					<col width="7%" />
					<col width="8%" />
					<col width="7%" />
					<col width="8%" />
				</colgroup> 
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>입/출고일</th>
						<th>창고구분</th>
						<th>재고구분</th>
						<th>상품명</th>
						<th>정상</th>
						<th>불량</th>
						<th>메모</th>
						<th class="end">등록일</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					$ALL_IN_QTY = 0;
					$ALL_OUT_QTY = 0;
					$ALL_IN_BQTY = 0;
					$ALL_OUT_BQTY = 0;

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$STR_STOCK_CODE = "";
							
							$INOUT_DATE						= trim($arr_rs[$j]["INOUT_DATE"]);
							$SE_NO							= trim($arr_rs[$j]["SE_NO"]);
							$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
							$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
							$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
							$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
							$NQTY							= trim($arr_rs[$j]["NQTY"]);
							$BQTY							= trim($arr_rs[$j]["BQTY"]);
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$MEMO							= trim($arr_rs[$j]["MEMO"]);
							
							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$INOUT_DATE			= date("Y-m-d",strtotime($INOUT_DATE));
							$REG_DATE			= date("Y-m-d H:i",strtotime($REG_DATE));

							if(trim($STOCK_TYPE) == "IN") { 
								$STR_STOCK_CODE = getDcodeName($conn, "IN_ST", $STOCK_CODE);
							} else {
								$STR_STOCK_CODE = getDcodeName($conn, "OUT_ST", $STOCK_CODE);
							}

							$ALL_IN_QTY = $ALL_IN_QTY + $IN_QTY;
							$ALL_OUT_QTY = $ALL_OUT_QTY + $OUT_QTY;
							$ALL_IN_BQTY = $ALL_IN_BQTY + $IN_BQTY;
							$ALL_OUT_BQTY = $ALL_OUT_BQTY + $OUT_BQTY;

							$STR_TITLE = "부분합 : 정상입고 ".$ALL_IN_QTY.", 정상출고 ".$ALL_OUT_QTY.", 불량입고 ".$ALL_IN_BQTY.", 불량출고 ".$ALL_OUT_BQTY." ";
				?>
					<tr height="37" title="<?=$STR_TITLE?>">
						<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$SE_NO?>"></td>
						<td><?=$INOUT_DATE?></td>
						<td><?=$STR_STOCK_CODE ?></td>
						<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
						<td class="price"><?=(startsWith($STOCK_CODE, 'NST') ? number_format($NQTY) : "")?></td>
						<td class="price"><?=(startsWith($STOCK_CODE, 'BST') ? number_format($BQTY) : "")?></td>
						<td><?=$MEMO?></td>
						<td><?=$REG_DATE?></td>
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
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "운영")) {?>
					<input type="button" name="aa" value=" 선택한 입/출고 삭제 " class="btntxt" onclick="js_delete();"> 
				<? } ?>
				</div>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							//$sel_order_state, $cp_type, $cp_type2, $sel_pay_type, $con_use_tf,
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&con_stock_code=".$con_stock_code."&cp_type=".$cp_type."&cp_type2=".$cp_type2."&sel_loc=".$sel_loc;
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