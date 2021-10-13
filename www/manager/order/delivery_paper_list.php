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

	$menu_right = "OD016"; // 메뉴마다 셋팅 해 주어야 합니다


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


	if ($mode == "D") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_delivery_paper_no = $chk_no[$k];
			// echo $temp_delivery_paper_no;
			// exit;
						
			$result = deleteOrderDeliveryPaper($conn, $temp_delivery_paper_no, $s_adm_no);
		
		}

	?>	
	<script language="javascript">
		alert('선택항목이 삭제 되었습니다.');
	</script>
	<?

	}


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

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	if ($order_field == "")
		$order_field = "DELIVERY_DATE";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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
	
	if ($nPage <> "" && $nPage <> "0") {
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

#   echo "F: ".$chkFee."<br/>";

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt = totalCntDeliveryPaper($conn, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $withoutDeliveryNo, $search_field, $search_str, $order_field, $order_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listDeliveryPaper($conn, $start_date, $end_date, $cp_type, $delivery_cp,  $delivery_fee_code, $delivery_claim_code, $isSent, $withoutDeliveryNo, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	if($chkFee == "Y") {
		$arrTotalFee = TotalDeliveryFee($conn, $start_date, $end_date, $cp_type, $delivery_cp, $delivery_fee_code, $delivery_claim_code, $isSent, $withoutDeliveryNo, $search_field, $search_str, $order_field, $order_str);
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/calendar.js"></script>
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

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
		
	function js_delete() {

		var frm = document.frm;
		
		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_reset() {
		
		var frm = document.frm;
		frm.start_date.value = "<?=date("Y-m-d",strtotime("-1 month"))?>";
		frm.end_date.value = "<?=date("Y-m-d",strtotime("0 month"))?>";

		frm.cp_type.value = "";
		frm.delivery_profit_code.value = "";
		frm.delivery_fee_code.value = "";
		frm.delivery_claim_code.value = "";
		
		frm.order_field.value = "REG_DATE";
		frm.order_str[0].checked = true;
		frm.nPageSize.value = "20";
		frm.search_field.value = "ALL";
		frm.search_str.value = "";
	}

	function js_update_delivery_paper(order_goods_delivery_no) {

		var url = "pop_delivery_paper_detail.php?order_goods_delivery_no=" + order_goods_delivery_no;

		NewWindow(url, 'delivery_paper_detail','1000','500','YES');
		
	}

	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

	}

	function js_delivery_paper_loading() { 

		var url = "pop_delivery_paper_loading.php";

		NewWindow(url, 'pop_delivery_paper_loading','1000','500','YES');

	}
	function js_open_manual(){
		NewWindow('delivery_paper_list_manual.php','delivery_paper_list_manual','800','500');
	}
</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
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

				<h2>송장 리스트 &nbsp; &nbsp; <input type="button" value="참고사항" onclick="js_open_manual()"></h2>

				<div class="btnright">
					<input type="button" name="bb" value="송장로딩 및 송장번호등록" onclick="js_delivery_paper_loading()"/>
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
						<th>조회기간</th>
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
						<td colspan="2">
							요약 :  
							<label><input type="checkbox" name="chkFee" <?=($chkFee == 'Y' ? "checked='checked'" : "")?> value="Y">&nbsp;택배무게구분</label>
							&nbsp;&nbsp;
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>판매업체</th>
						<td>
							<input type="text" name="cp_type" value="<?=$cp_type?>" placeholder="송장출력시 기록된 업체명 검색" style="width:90%;">
						</td>
						<th>택배사</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"DELIVERY_CP","delivery_cp","105","전체","", $delivery_cp)?>
						</td>
					</tr>
					<tr>
						<th>운임타입</th>
						<td>
							<?= makeSelectBox($conn,"DELIVERY_FEE","delivery_fee_code","105","전체","", $delivery_fee_code)?>
						</td>
						<th>배송클레임</th>
						<td colspan="2">
							<?= makeSelectBoxWithExt($conn,"DELIVERY_CLAIM","delivery_claim_code","105","전체","", $delivery_claim_code)?>
						</td>
					</tr>
					<tr>
						<th>필터</th>
						<td colspan="4">
							송장번호 여부 : <?= makeSelectBox($conn,"DELIVERY_NO_TF","withoutDeliveryNo","105","전체","", $withoutDeliveryNo)?>
						</td>
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:74px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="DELIVERY_DATE" <? if ($order_field == "DELIVERY_DATE") echo "selected"; ?> >배송일</option>
							</select>&nbsp;&nbsp;
							<label><input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림차순</label> &nbsp;
							<label><input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순</label>
						</td>
						<th>검색조건</th>
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
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_DELIVERY_NAME" <? if ($search_field == "GOODS_DELIVERY_NAME") echo "selected"; ?> >상품명</option>
								<option value="RECEIVER_NM" <? if ($search_field == "RECEIVER_NM") echo "selected"; ?> >수령인</option>
								<option value="ORDER_NM" <? if ($search_field == "ORDER_NM") echo "selected"; ?> >주문자명</option>
								<option value="DELIVERY_NO" <? if ($search_field == "DELIVERY_NO") echo "selected"; ?> >송장번호</option>
								<option value="DELIVERY_NO_MULTI" <? if ($search_field == "DELIVERY_NO_MULTI") echo "selected"; ?> >*송장번호(복수)</option>
								<option value="DELIVERY_SEQ" <? if ($search_field == "DELIVERY_SEQ") echo "selected"; ?> >출고번호</option>
								<option value="DELIVERY_SEQ_MULTI" <? if ($search_field == "DELIVERY_SEQ_MULTI") echo "selected"; ?> >*출고번호(복수)</option>
								<!--option value="CP_ORDER_NO" <? if ($search_field == "CP_ORDER_NO") echo "selected"; ?> >대표주문번호</option-->
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
							<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp10"></div>
			<div>
				<? if($chkFee == "Y") { ?>
				<table cellpadding="0" cellspacing="0" class="rowstable" border="0" style="width:400px; margin-right:20px;  margin-bottom:10px;">
					<colgroup>
						<col width="30%" />
						<col width="30%" />
						<col width="*" />
					</colgroup>
					<thead>
						<tr>
							<th>택배무게구분</th>
							<th>운임</th>
							<th>수량</th>
							<th class="end">총합</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						$TOTAL_FEE_PRICE = 0;
						if (sizeof($arrTotalFee) > 0) {
							for ($k = 0 ; $k < sizeof($arrTotalFee); $k++) {

								$FEE_NAME	= trim($arrTotalFee[$k]["FEE_NAME"]);
								$FEE		= trim($arrTotalFee[$k]["FEE"]);
								$FEE_CNT	= trim($arrTotalFee[$k]["FEE_CNT"]);
								$FEE_TOTAL	= trim($arrTotalFee[$k]["FEE_TOTAL"]);

								$TOTAL_FEE_PRICE += $FEE_TOTAL;
					?>
					<tr height="37">
						<td><?=$FEE_NAME?></td>
						<td><?=$FEE?></td>
						<td><?=$FEE_CNT?></td>
						<td><?=number_format($FEE_TOTAL) ?></td>
					</tr>

					<?
							}
						}
					?>

					</tbody>
				</table>
				<? } ?>
				
			</div>
			<div style="width: 95%; text-align: right; margin: 0;">
				<? if($withoutDeliveryNo == "N") { ?>
					<input type="button" name="bb" value="선택한 송장 삭제" onclick="js_delete()"/>
				<? } else { ?>
					*. 송장을 삭제하시려면 송장번호 여부 : "없음" 으로 검색해주시고 송장번호 있는 송장은 개별 클릭해서 "사용안함"으로 변경해주세요.
				<? } ?>
			</div>
			<b>총 <?=$nListCnt?> 건</b>
			<? if($chkFee == "Y") { ?>
			&nbsp;&nbsp;&nbsp;&nbsp; 
			<b>택배 <?=number_format($TOTAL_FEE_PRICE)?> 원</b>
			&nbsp;&nbsp;&nbsp;&nbsp; 
			<? } ?>

			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="2%" />
					<col width="7%" />
					<col width="8%" />
					<col width="8%" />
					<col width="10%" />
					<col width="*" />
					<col width="25%" />
					<col width="8%" />
					<col width="7%" />
					<col width="5%" />

				</colgroup>
				<thead>
					<tr>
						<th rowspan="2"><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th rowspan="2">출고번호</th>
						<th>송장번호</th>
						<th>수령인</th>
						<th>수령인전화번호</th>
						<th rowspan="2">상품명</th>
						<th>수령자주소</th>
						<th>판매처</th>
						<th>배송클레임</th>	
						<th rowspan="2" class="end">등록일</th>
					</tr>
					<tr>
						<th>택배사</th>
						<th>주문자이름</th>
						<th>수령인핸드폰번호</th>
						<th>메모</th>
						<th>판매처전화</th>
						<th>운임타입</th>	
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//$CP_ORDER_NO	= trim($arr_rs[$j]["CP_ORDER_NO"]);

							$ORDER_GOODS_DELIVERY_NO	= trim($arr_rs[$j]["ORDER_GOODS_DELIVERY_NO"]);
							$DELIVERY_CNT	= trim($arr_rs[$j]["DELIVERY_CNT"]);
							$SEQ_OF_DELIVERY	= trim($arr_rs[$j]["SEQ_OF_DELIVERY"]);
							$DELIVERY_SEQ	= trim($arr_rs[$j]["DELIVERY_SEQ"]);
							$SEQ_OF_DAY	= trim($arr_rs[$j]["SEQ_OF_DAY"]);

							$RECEIVER_NM	= trim($arr_rs[$j]["RECEIVER_NM"]);
							$RECEIVER_PHONE	= trim($arr_rs[$j]["RECEIVER_PHONE"]);
							$RECEIVER_HPHONE	= trim($arr_rs[$j]["RECEIVER_HPHONE"]);
							$RECEIVER_ADDR	= trim($arr_rs[$j]["RECEIVER_ADDR"]);
							$ORDER_QTY	= trim($arr_rs[$j]["ORDER_QTY"]);

							$MEMO	= trim($arr_rs[$j]["MEMO"]);
							$ORDER_NM	= trim($arr_rs[$j]["ORDER_NM"]);
							$ORDER_PHONE	= trim($arr_rs[$j]["ORDER_PHONE"]);
							$ORDER_MANAGER_NM	= trim($arr_rs[$j]["ORDER_MANAGER_NM"]);
							$ORDER_MANAGER_PHONE	= trim($arr_rs[$j]["ORDER_MANAGER_PHONE"]);

							$PAYMENT_TYPE	= trim($arr_rs[$j]["PAYMENT_TYPE"]);
							$SEND_CP_ADDR	= trim($arr_rs[$j]["SEND_CP_ADDR"]);
							$GOODS_DELIVERY_NAME	= trim($arr_rs[$j]["GOODS_DELIVERY_NAME"]);
							$DELIVERY_CP	= trim($arr_rs[$j]["DELIVERY_CP"]);
							$DELIVERY_NO	= trim($arr_rs[$j]["DELIVERY_NO"]);
							
							$DELIVERY_TYPE	= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$DELIVERY_DATE	= trim($arr_rs[$j]["DELIVERY_DATE"]);
							$DELIVERY_FEE	= trim($arr_rs[$j]["DELIVERY_FEE"]);
							$DELIVERY_FEE_CODE	= trim($arr_rs[$j]["DELIVERY_FEE_CODE"]);
							$DELIVERY_CLAIM_CODE	= trim($arr_rs[$j]["DELIVERY_CLAIM_CODE"]);
							$DELIVERY_CLAIM =  getDcodeName($conn, 'DELIVERY_CLAIM', $DELIVERY_CLAIM_CODE);

							$USE_TF	= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF	= trim($arr_rs[$j]["DEL_TF"]);
							$REG_ADM	= trim($arr_rs[$j]["REG_ADM"]);

							$REG_DATE	= trim($arr_rs[$j]["REG_DATE"]);
						?>
						<tr height="37">
							<td rowspan="2">
							<?
								if ($DELIVERY_NO == "") {
							?>
								<input type="checkbox" name="chk_no[]" value="<?=$ORDER_GOODS_DELIVERY_NO?>">
							<? } ?>
							</td>
							<td rowspan="2"><a href="javascript:js_update_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>')"><?=$DELIVERY_SEQ?></a></td>
							<td><a href="javascript:js_pop_delivery_paper_frame('<?=$DELIVERY_CP?>', '<?=$DELIVERY_NO?>');" style="font-weight:bold;" title="<?=$DELIVERY_NO?>"><?=$DELIVERY_NO?></a>
							</td>
							<td class="modeual_nm"><?=$RECEIVER_NM?> </td>
							<td><?=$RECEIVER_PHONE?> </td>
							<td rowspan="2"><?=$GOODS_DELIVERY_NAME?> </td>
							<td class="modeual_nm"><?=$RECEIVER_ADDR?> </td>
							<td class="modeual_nm"><?=$ORDER_MANAGER_NM?> </td>
							<td><?=$DELIVERY_CLAIM?> </td>
							<td rowspan="2"><?=$REG_DATE?> </td>
						</tr>
						<tr height="37">
							<td><?=$DELIVERY_CP?> </td>
							<td class="modeual_nm"><?=$ORDER_NM?> </td>
							<td><?=$RECEIVER_HPHONE?> </td>
							<td class="modeual_nm"><?=$MEMO?> </td>
							<td><?=$ORDER_MANAGER_PHONE?> </td>
							<td><?=$DELIVERY_FEE?> </td>
						</tr>
						<?
								}
							} else { 
					?>
						<tr height="37">
							<td colspan="9">데이터가 없습니다.</td>
						</tr>

					<?      } ?>
						<!--tr class="goods_end">
							<td colspan="11">
								&nbsp;
							</td>
						</tr>
						<tr height="37">
							<td>합계</td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_ORDER_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_DELIVERY_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_CANCEL_SALE_PRICE)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_QTY)?></td>
							<td class="price"><?=number_format($TOT_SUN_SALE_PRICE)?></td>
							<td class="price"><?=number_format($PLUS_PRICE)?></td>
							<td class="price"><?=$LEE?> %</td>
						</tr-->
				</tbody>
			</table>
			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
				<? if($withoutDeliveryNo == "N") { ?>
					<input type="button" name="bb" value="선택한 송장 삭제" onclick="js_delete()"/>
				<? } else { ?>
					*. 송장을 삭제하시려면 송장번호 여부 : "없음" 으로 검색해주시고 송장번호 있는 송장은 개별 클릭해서 "사용안함"으로 변경해주세요.
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
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&start_date=".$start_date."&end_date=".$end_date;
							$strParam = $strParam."&cp_type=".$cp_type."&delivery_cp=".$delivery_cp."&delivery_profit_code=".$delivery_profit_code."&delivery_fee_code=".$delivery_fee_code."&delivery_claim_code=".$delivery_claim_code."&withoutDeliveryNo=".$withoutDeliveryNo;
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