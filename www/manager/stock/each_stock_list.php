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

	if($tab_index == "")
		$tab_index = "0";

	

	if($warehouse_code == "")
		$warehouse_code = "WH004";


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 year"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_stock_no = $chk_no[$k];
			
			$result = deleteEachStock($conn, $str_stock_no, $s_adm_no);
		
		}
		
	}


	if($tab_index == "0") { 

		$arr_list = listEachStock($conn, $warehouse_code, $search_field, $search_str, $order_field, $order_str);

	} else if ($tab_index == "1") { 
	
		$nListCnt = totalCntEachStockInOut($conn, $warehouse_code, $start_date, $end_date, $stock_type, $stock_code, $search_field, $search_str);
			
		#echo $nListCnt;

		$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

		if ((int)($nTotalPage) < (int)($nPage)) {
			$nPage = $nTotalPage;
		}

		$arr_rs = listEachStockInOut($conn, $warehouse_code, $start_date, $end_date, $stock_type, $stock_code, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
	}
	
	
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
		$("#tabs").tabs({
			active: <?=$tab_index?>,
			activate: function (e, ui) {
				var frm = document.frm;

				frm.tab_index.value = ui.newTab.index();
				frm.order_field.value = "";
				$('input[name=order_str]').attr('checked',false);
				frm.method = "post";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
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


	function js_pop_write(inout_type) {

		NewWindow("pop_each_stock_write.php?warehouse_code="+document.frm.warehouse_code.value + "&inout_type=" + inout_type ,'pop_each_stock_write','1000','500','YES');

	}


	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;

		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	/*
	//보안문제로 다시 제거
	function js_goods_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "blank";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();
		
	}
	*/

	function js_delete() {
	
		var frm = document.frm;

		bDelOK = confirm('정말로 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();s
		}
	}

	function js_warehouse_code() { 

		location.href = "each_stock_list.php?warehouse_code=" +  document.frm.warehouse_code.value;

	}

	function js_out_stock(goods_code) { 

		var inout_type = "OUT";

		NewWindow("pop_each_stock_write.php?warehouse_code="+document.frm.warehouse_code.value + "&inout_type=" + inout_type + "&goods_code=" + goods_code ,'pop_each_stock_write','1000','500','YES');

	}

	function js_search_stock(goods_code) { 

		location.href = "each_stock_list.php?warehouse_code=" +  document.frm.warehouse_code.value + "&tab_index=1&search_field=GOODS_CODE&search_str=" + goods_code;

	}

	function js_view_order(reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
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
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="depth" value="">
<input type="hidden" name="tab_index" value="<?=$tab_index?>">
<input type="hidden" name="goods_no" value="">

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

				<h2>개별 창고 관리 <?=makeSelectBoxOnChange($conn,"WAREHOUSE","warehouse_code","100","","창고선택",$warehouse_code)?></h2>
				<div class="btnright">
				
					<? if($sPageRight_I == "Y") { ?>
						<input type="button" value=" 입고 " onclick="js_pop_write('IN')"> 
						<!--<input type="button" value=" 출고 " onclick="js_pop_write('OUT')">-->
					<? } ?>
				
				</div>
				<div class="category_choice">&nbsp;</div>

					
				<div id="tabs" style="width:95%; margin:10px 0;">
				<ul>
					<li><a href="#tabs-0">재고현황</a></li>
					<li><a href="#tabs-1">입출고내역</a></li>
				</ul>
				<div id="tabs-0">
					
					<? if($tab_index == "0") {?>
					<table cellpadding="0" cellspacing="0" class="colstable">
						<colgroup>
							<col width="100" />
							<col width="*" />
							<col width="100" />
							<col width="*" />
							<col width="50" />
						</colgroup>
						<tr>
							<th>정렬 :</th>
							<td>
								<select name="order_field" style="width:84px;">
									<option value="G.GOODS_CODE" <? if ($order_field == "G.GOODS_CODE") echo "selected"; ?> >상품코드</option>
									<option value="G.GOODS_NAME" <? if ($order_field == "G.GOODS_NAME") echo "selected"; ?> >상품명</option>
									<option value="N_TOTAL" <? if ($order_field == "N_TOTAL") echo "selected"; ?> >정상재고</option>
									<option value="B_TOTAL" <? if ($order_field == "B_TOTAL") echo "selected"; ?> >불량재고</option>
								</select>&nbsp;&nbsp;
								<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC"  || $order_str == "" ) echo " checked"; ?> > 오름차순 &nbsp;
								<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> 내림차순
							</td>

							<th>검색조건 :</th>
							<td>
								<select name="search_field" style="width:84px;">
									<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
									<option value="B.GOODS_CODE" <? if ($search_field == "B.GOODS_CODE") echo "selected"; ?> >상품코드</option>
									<option value="B.GOODS_NAME" <? if ($search_field == "B.GOODS_NAME") echo "selected"; ?> >상품명</option>
								</select>&nbsp;

								<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"  />
								<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							</td>
							<td align="right">
								<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
							</td>
						</tr>
					</table>

					<div class="sp20"></div>
					<b>총 <?=sizeof($arr_list)?> 건</b> 
					<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
						<colgroup>
							<col width="10%" />
							<col width="*" />
							<col width="12%"/>
							<col width="12%" />
							<col width="11%" />
						</colgroup>
						<thead>
							<tr>
								<th>상품코드</th>
								<th>상품명</th>
								<th>정상수량</th>
								<th>불량수량</th>
								<th class="end"></th>
							</tr>
						</thead>
						<tbody>
						<?
							$SUM_N_TOTAL = 0;
							$SUM_B_TOTAL = 0;

							if (sizeof($arr_list) > 0) {
								for ($j = 0 ; $j < sizeof($arr_list); $j++) {
									
									$GOODS_NO					= trim($arr_list[$j]["GOODS_NO"]);
									$GOODS_CODE					= trim($arr_list[$j]["GOODS_CODE"]);
									$GOODS_NAME					= SetStringFromDB($arr_list[$j]["GOODS_NAME"]);
									$N_TOTAL					= trim($arr_list[$j]["N_TOTAL"]);
									$B_TOTAL					= trim($arr_list[$j]["B_TOTAL"]);
									
									$SUM_N_TOTAL += $N_TOTAL;
									$SUM_B_TOTAL += $B_TOTAL;
						?>
							<tr height="37">
								<td class="modeual_nm"><?=$GOODS_CODE?></td>
								<td class="modeual_nm"><?=$GOODS_NAME?></td>
								<td class="price">
									<?=number_format($N_TOTAL)?>
								</td>
								<td class="price">
									<?=number_format($B_TOTAL)?>
								</td>
								<td>
									<!--<input type="button" name="aa" value=" 정상<->불량 " class="btntxt" onclick="js_modify_stock('<?=$GOODS_NO?>');">-->
									<input type="button" value=" 조회 " onclick="js_search_stock('<?=$GOODS_CODE?>');">
									<input type="button" value=" 출고 " onclick="js_out_stock('<?=$GOODS_CODE?>');">
								</td>
							</tr>
							<?
								}
							} else {
							?>
								<tr class="order">
									<td height="50" align="center" colspan="5">데이터가 없습니다. </td>
								</tr>
							<?
							}
							?>
							<tr height="37" style="font-weight:bold;">
								<td colspan="2" class="modeual_nm">합 계 : </td>
								<td class="price">
									<?=number_format($SUM_N_TOTAL)?>
								</td>
								<td class="price">
									<?=number_format($SUM_B_TOTAL)?>
								</td>
								<td>
								</td>
							</tr>
						</tbody>
					</table>
					<? } ?>
				</div>
				<div id="tabs-1">
					<? if($tab_index == "1") {?>
					<table cellpadding="0" cellspacing="0" class="colstable">
						<colgroup>
							<col width="100" />
							<col width="*" />
							<col width="100" />
							<col width="*" />
							<col width="50" />
						</colgroup>
						<tr>
							<th>입/출고일 :</th>
							<td colspan="3">
								<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" /> ~
								<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							</td>
							<td align="right">
								<!--a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a-->
							</td>
							
						</tr>
						<tr>
							<th>정렬 :</th>
							<td>
								<select name="order_field" style="width:84px;">
									<option value="A.INOUT_DATE" <? if ($order_field == "A.INOUT_DATE") echo "selected"; ?> >입출고일</option>
									<option value="A.REG_DATE" <? if ($order_field == "A.REG_DATE") echo "selected"; ?> >등록일</option>
								</select>&nbsp;&nbsp;
								<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?> > 오름차순 &nbsp;
								<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC" || $order_str == "") echo " checked"; ?>> 내림차순
							</td>

							<th>검색조건 :</th>
							<td>
								<select name="nPageSize" style="width:74px;">
									<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
									<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
									<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
									<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
									<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
									<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
									<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
								</select>&nbsp;
								<select name="search_field" style="width:74px;">
									<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
									<option value="B.GOODS_CODE" <? if ($search_field == "B.GOODS_CODE") echo "selected"; ?> >상품코드</option>
									<option value="B.GOODS_NAME" <? if ($search_field == "B.GOODS_NAME") echo "selected"; ?> >상품명</option>
								</select>&nbsp;

								<input type="text" value="<?=$search_str?>" name="search_str" class="txt"  onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" style="width:84px"  />
								<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							</td>
							<td align="right">
								<!--<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>-->
							</td>
						</tr>
					</table>

					<div class="sp20"></div>
					<b>총 <?=$nListCnt?> 건</b>
					<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
						<colgroup>
							<col width="2%" />
							<col width="7%" />
							<col width="8%" />
							<col width="*" />
							<col width="7%" />
							<col width="6%" />
							<col width="6%" />
							<col width="15%" />
							<col width="7%" />
							<col width="9%" />
						</colgroup> 
						<thead>
							<tr>
								<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
								<th>입/출고일</th>
								<th>재고구분</th>
								<th>상품명</th>
								<th>박스입수</th>
								<th>입고</th>
								<th>출고</th>
								<th>주문번호</th>
								<th>메모</th>
								<th class="end">등록일</th>
							</tr>
						</thead>
						<tbody>
						<?
							if (sizeof($arr_rs) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
									
									//A.SE_NO, A.GOODS_NO, B.GOODS_NAME, B.GOODS_CODE, A.INOUT_DATE, A.DELIVERY_CNT_IN_BOX, A.NQTY, A.BQTY, A.MEMO, A.REG_DATE

									$SE_NO							= trim($arr_rs[$j]["SE_NO"]);
									$GOODS_NO						= trim($arr_rs[$j]["GOODS_NO"]);
									$GOODS_NAME						= SetStringFromDB(trim($arr_rs[$j]["GOODS_NAME"]));
									$GOODS_CODE						= SetStringFromDB(trim($arr_rs[$j]["GOODS_CODE"]));
									$INOUT_DATE						= trim($arr_rs[$j]["INOUT_DATE"]);
									$STOCK_TYPE						= trim($arr_rs[$j]["STOCK_TYPE"]);
									$STOCK_CODE						= trim($arr_rs[$j]["STOCK_CODE"]);
									$DELIVERY_CNT_IN_BOX			= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
									$IN_NQTY						= trim($arr_rs[$j]["IN_NQTY"]);
									$IN_BQTY						= trim($arr_rs[$j]["IN_BQTY"]);
									$OUT_NQTY						= trim($arr_rs[$j]["OUT_NQTY"]);
									$OUT_BQTY						= trim($arr_rs[$j]["OUT_BQTY"]);
									$MEMO							= SetStringFromDB(trim($arr_rs[$j]["MEMO"]));
									$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);

									$RESERVE_NO						= trim($arr_rs[$j]["RESERVE_NO"]);
									$ORDER_GOODS_NO					= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
									
									$INOUT_DATE			= date("Y-m-d",strtotime($INOUT_DATE));
									$REG_DATE			= date("Y-m-d H:i:s",strtotime($REG_DATE));
									
									$STR_STOCK_TYPE = "";
									if($STOCK_TYPE == "IN")
										$STR_STOCK_TYPE = "입고";
									else
										$STR_STOCK_TYPE = "출고";


						?>
							<tr height="37">
								<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$SE_NO?>"></td>
								<td><?=$INOUT_DATE?></td>
								<td><?=$STR_STOCK_TYPE ?>/<?=$STOCK_CODE ?></td>
								<td class="modeual_nm">[<?=$GOODS_CODE?>] <?= $GOODS_NAME?></td>
								<td class="price"><?=number_format($DELIVERY_CNT_IN_BOX)?></td>
								<td class="price">
									<?  
										if($STOCK_TYPE == "IN") { 
											if($IN_NQTY != "0")
												echo number_format($IN_NQTY);
										} else { 
											if($IN_BQTY != "0")
												echo number_format($IN_BQTY);
										}
									?>
								</td>
								<td class="price">
									<?  
										if($STOCK_TYPE == "OUT") { 
											if($OUT_NQTY != "0")
												echo number_format($OUT_NQTY);
										} else { 
											if($OUT_BQTY != "0")
												echo number_format($OUT_BQTY);
										}
									?>
								</td>
								<td title='<?=$ORDER_GOODS_NO?>'><a href="javascript:js_view_order('<?=$RESERVE_NO?>')"><?=$RESERVE_NO?></a></td>
								<td><?=$MEMO?></td>
								<td><?=$REG_DATE?></td>
							</tr>
							<?

								}

							?>
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
								$strParam = $strParam."&warehouse_code=".$warehouse_code."&tab_index=".$tab_index;
								$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;

						?>
						<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
						<?
							}
						?>
						<!-- --------------------- 페이지 처리 화면 END -------------------------->
					<br />
					<div class="sp10"></div>
					<? } ?>
				</div>

					
			<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->
			
		</td>
	</tr>
	</table>
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