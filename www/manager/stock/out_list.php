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
	$menu_right = "SG006"; // 메뉴마다 셋팅 해 주어야 합니다

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
	
	if ($mode == "T") {
		
		#echo $chg_order_state;
		
		$reserve_no_cnt = count($chk_reserve_no);

		for($i=0; $i <= ($reserve_no_cnt - 1) ; $i++) {
			$result = updateStockState($conn, $chk_reserve_no[$i], $chg_order_state);
		}

		//updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}


	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_stock_no = $chk_no[$k];
			
			$result = deleteStock($conn, $str_stock_no, $s_adm_no);
		
		}
		
	}
#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

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

	if ($order_field == "")
		$order_field = "REG_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_stock_type = "OUT";
	$con_in_cp_no = "";

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

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $con_in_cp_no, $cp_type2, $sel_loc, $filter, $del_tf, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStock($conn, $start_date, $end_date, $con_stock_type, $con_stock_code, $con_in_cp_no, $cp_type2, $sel_loc, $filter, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

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

	function js_write() {

		var frm = document.frm;
		
		frm.target = "";
		frm.method = "get";
		frm.action = "out_write.php";
		frm.submit();
		
	}

	function js_view(stock_no) {
	
		/*
		//자동 출고 변경할수 없도록 한시적 사용불가
		var frm = document.frm;
		
		frm.stock_no.value = stock_no;

		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "out_write.php";
		frm.submit();
		*/
	}

	function js_view_goods_request(req_no) {

		var url = "../stock/pop_goods_request.php?req_no=" + req_no;

		NewWindow(url, 'pop_goods_request','1024','600','YES');
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle() {

	var frm = document.frm;
	var chk_cnt = 0;

	if (frm('chk_reserve_no[]') == null) {
		alert("선택 항목이 없습니다.");
		return;
	}

	if (frm('chk_reserve_no[]').length != null) {
		
		for (i = 0 ; i < frm('chk_reserve_no[]').length; i++) {
			if (frm('chk_reserve_no[]')[i].checked == true) {
				chk_cnt = 1;
			}
		}
	
	} else {
		if (frm('chk_reserve_no[]').checked == true) chk_cnt = 1;
	}
	
	if (chk_cnt == 0) {
		alert("상태 변경할 주문을 선택해 주세요");
		return;
	}

	bDelOK = confirm('주문 상태를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
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

	function js_reload() {
		location.reload();
	}

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

	function js_stock_memo_view(stock_no) {

		var url = "pop_stock_memo.php?stock_no="+stock_no;
		NewWindow(url,'pop_stock_memo','820','700','YES');

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

				<h2>출고 관리</h2>
				<div class="btnright">
					<? if ($s_adm_cp_type == "운영") { ?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
					<? } else { ?>
					&nbsp;
					<? } ?>
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
				<thead>
					<tr>
						<th>출고일</th>
						<td>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
						</td>
						
						<th>출고사유</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"LOC","sel_loc","125","선택","",$sel_loc)?>
						</td>
						
					</tr>
				</thead>
				<tbody>
					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>판매업체</th>
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
						<th>출고구분 :</th>
						<td colspan="2">
							<?= makeSelectBox($conn, 'OUT_ST','con_stock_code',"125", "선택", "", $con_stock_code);?>
						</td>					
					</tr>
					<? } else { ?>
					<input type="hidden" name="cp_type" value = "">
					<input type="hidden" name="sel_pay_type" value = "">
					<? } ?>
					<!--
					<tr>
						<th>필터</th>
						<td colspan="4">
							<input type="checkbox" name="con_close_tf" <?=($con_close_tf == "N" ? 'checked' : '')?> value="N"/>전체 내역 보기(기초재고 입력 후 재고)
						</td>
					</tr>
					-->
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="A.REG_DATE" <? if ($order_field == "A.REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="A.IN_DATE" <? if ($order_field == "A.OUT_DATE") echo "selected"; ?> >출고일</option>
								<!--
								<option value="IN_QTY" <? if ($order_field == "IN_QTY") echo "selected"; ?> >정상입고</option>
								<option value="IN_BQTY" <? if ($order_field == "IN_BQTY") echo "selected"; ?> >불량입고</option>
								-->
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>

						<th>검색조건</th>
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
								<option value="IN_LOC_EXT" <? if ($search_field == "IN_LOC_EXT") echo "selected"; ?> >사유상세</option>
								<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
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
			<b>총 <?=$nListCnt?> 건</b>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="2%" />
					<col width="8%" />
					<col width="8%" />
					<col width="*" />
					<col width="8%"/>
					<col width="7%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>출고일</th>
						<th>업체명</th>
						<th>상품명</th>
						<th>재고구분</th>
						<th>판매가</th>
						<th>수량</th>
						<th>출고사유</th>
						<th>사유상세</th>
						<th>메모</th>
						<th class="end">주문번호</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn								= trim($arr_rs[$j]["rn"]);
							$OUT_DATE						= trim($arr_rs[$j]["OUT_DATE"]);
							$STOCK_NO						= trim($arr_rs[$j]["STOCK_NO"]);
							$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
							$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
							$OUT_PRICE						= trim($arr_rs[$j]["OUT_PRICE"]);
							$OUT_QTY						= trim($arr_rs[$j]["OUT_QTY"]);
							$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
							$OUT_TQTY						= trim($arr_rs[$j]["OUT_TQTY"]);
							$PAY_DATE						= trim($arr_rs[$j]["PAY_DATE"]);
							$OUT_CP_NO					    = trim($arr_rs[$j]["OUT_CP_NO"]);
							$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
							$IN_LOC							= trim($arr_rs[$j]["IN_LOC"]);
							$IN_LOC_EXT						= trim($arr_rs[$j]["IN_LOC_EXT"]);
							$CLOSE_TF						= trim($arr_rs[$j]["CLOSE_TF"]);
							$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO					= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO							= trim($arr_rs[$j]["RGN_NO"]);
							$MEMO							= trim($arr_rs[$j]["MEMO"]);

							$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
							
							$OUT_DATE			= date("Y-m-d",strtotime($OUT_DATE));
							$REG_DATE			= date("Y-m-d",strtotime($REG_DATE));
							$PAY_DATE			= date("Y-m-d",strtotime($PAY_DATE));

							if (left($STOCK_CODE,1) == "N") {
								$QTY = $OUT_QTY;
							} else if (left($STOCK_CODE,1) == "B") {
								$QTY = $OUT_BQTY;
							} else if (left($STOCK_CODE,1) == "T") {
								$QTY = $OUT_TQTY;
							}

							if($CLOSE_TF == "Y")
								$closed_style_tr = "class='closed'";
							else
								$closed_style_tr = "";
							
				?>
					<tr height="37" <?=$closed_style_tr ?> >
						<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$STOCK_NO?>"></td>
						<td ><?=$OUT_DATE?></a></td>
						<td class="modeual_nm"><?= getCompanyName($conn, $OUT_CP_NO);?></td>
						<td class="modeual_nm"><a href="javascript:js_view('<?=$STOCK_NO?>');"><?= $GOODS_NAME?> [<?=$GOODS_CODE?>]</a></td>
						<td><?= getDcodeName($conn, "OUT_ST", $STOCK_CODE)?></td>
						<td class="price"><?=number_format($OUT_PRICE)?></td>
						<td class="price"><?=number_format($QTY)?></td>
						<td><?= getDcodeName($conn, "LOC", $IN_LOC)?></td>
						<td><?=$IN_LOC_EXT?></td>
						<td onclick="javascript:js_stock_memo_view('<?=$STOCK_NO?>');"><?=$MEMO?></td>
						<td><?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?></td>
					</tr>
					<?
									
						}

					?>

					<!-- 합계 -->
						<!--
						<tr class="goods_end">
							<td colspan="10">
								&nbsp;
							</td>
						</tr>
						<tr class="goods_end" height="35">
							<td class="filedown" colspan="2">합 계</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="modeual_nm" colspan="1" ></td>
							<td class="price"><?=number_format($ALL_QTY)?></td>
							<td class="price"><?=number_format($ALL_BUY_PRICE)?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						-->

					<?

					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="10">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "운영")) {?>
					<input type="button" name="aa" value=" 선택한 출고 삭제 " class="btntxt" onclick="js_delete();"> 
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
							$strParam = $strParam."&con_stock_code=".$con_stock_code."&cp_type=".$cp_type."&cp_type2=".$cp_type2;
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