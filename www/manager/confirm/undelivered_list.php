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
	$menu_right = "CF010"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";

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
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

#===============================================================
# Get Search list count
#===============================================================

	$filter = array('con_sale_adm_no' => $con_sale_adm_no, 'order_state1' => $order_state1, 'order_state2' => $order_state2, 'cate_01' => $cate_01, 'delivery_type' => $delivery_type);

	//echo "order_state : ".$order_state1." || ".$order_state2."<br/>";
	//echo "cate_01 : ".$cate_01."<br/>";

	$arr_rs = listUndeliveredOrderGoods($conn, $search_date_type, $start_date, $end_date, $cp_type, $filter, $search_field, $search_str, $order_field, $order_str);

	//if( $cp_type <> 0)
	//	echo getUndeliveredOrderGoods($conn, $cp_type);

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
		
		//frm.nPage.value = "1";
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

	function js_confirm_tax_invoice(tax_confirm_tf) { 

		var frm = document.frm;

		bDelOK = confirm((tax_confirm_tf == 'Y' ? '발행 처리하시겠습니까?' : '발행 취소하시겠습니까?'));
		
		if (bDelOK==true) {
			
			frm.mode.value = "TAX_INVOICE";
			frm.tax_confirm_tf.value = tax_confirm_tf;
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_update_inout_date() { 

		var frm = document.frm;

		bOK = confirm("선택된 기장을 " + frm.tax_confirm_date.value + " 로 변경하시겠습니까?");
		
		if (bOK) {
			
			frm.mode.value = "UPDATE_INOUT_DATE";
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

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_read','860','600','YES');
		
	}

	function js_view_goods_request(req_no) {

		var url = "../stock/pop_goods_request.php?req_no=" + req_no;

		NewWindow(url, 'pop_goods_request','1024','600','YES');
		
	}

	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

</script>
<style>
	.row_monthly {background-color:#DFDFDF; font-weight:bold;}
	.row_daily {background-color:#EFEFEF; font-weight:bold;}
	tr.row_tax_confirm > td {/*background-color:#99c1ef;*/ color:blue;} 
	tr.closed > td {background-color:#fff; color: #A2A2A2;} 
</style> 
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="cl_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="tax_confirm_tf" value="">
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

				<h2>외상매출 관리</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="100" />
					<col width="300" />
					<col width="100" />
					<col width="300" />
					<col width="*" />
				</colgroup>
				
				<tr>
					<th>
						<select name="search_date_type">
							<option value="order_date" <? if ($search_date_type == "order_date" || $search_date_type == "") echo "selected" ?>>주문일</option>
							<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>주문등록일</option>
						</select>
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
					<th>업체명</th>
					<td>
						<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" />
						<input type="hidden" name="cp_type" value="<?=$cp_type?>">

						<script>
							$(function(){

								$("input[name=txt_cp_type]").keydown(function(e){

									if(e.keyCode==13) { 

										var keyword = $(this).val();
										if(keyword == "") { 
											$("input[name=cp_type]").val('');
											js_search();
										} else { 
										
											$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
												if(data.length == 1) { 
													
													js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

												} else if(data.length > 1){ 
													NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

												} else 
													alert("검색결과가 없습니다.");
											});
										}
									}

								});

								$("input[name=txt_cp_type]").keyup(function(e){
									var keyword = $(this).val();

									if(keyword == "") { 
										$("input[name=cp_type]").val('');
									}
								});

							});

							function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
								
								$(function(){

									$("input[name="+target_name+"]").val(cp_nm);
									$("input[name="+target_value+"]").val(cp_no);
									js_search();

								});
							}
						</script>
					</td>
					<th>필터</th>
					<td colspan="2">
						<b>주문접수:</b><label><input type="checkbox" name="order_state1" value="1" <? if($order_state1 == "1" || ($order_state1 == "" && $order_state2 == "")) echo 'checked="checked"';?>/>주문접수</label><label><input type="checkbox" name="order_state2" value="2" <? if($order_state2 == "2" || ($order_state1 == "" && $order_state2 == "")) echo 'checked="checked"';?>/>배송준비중</label>&nbsp;&nbsp;&nbsp;
						<b>추가포함 여부:</b><label><input type="checkbox" name="cate_01" value="Y" <? if($cate_01 == "Y") echo 'checked="checked"';?>/>포함</label>
					</td>
				</tr>
				<tr>
					<th>필터</th>
					<td colspan="4">
						<b>배송방식:</b>
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("0", $delivery_type)) echo "checked='checked'";?> value="0"/>택배</label>&nbsp;&nbsp;
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("1", $delivery_type)) echo "checked='checked'";?> value="1"/>직접수령</label>&nbsp;&nbsp;
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("2", $delivery_type)) echo "checked='checked'";?> value="2"/>퀵서비스</label>&nbsp;&nbsp;
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("3", $delivery_type)) echo "checked='checked'";?> value="3"/>개별택배</label>&nbsp;&nbsp;
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("98", $delivery_type)) echo "checked='checked'";?> value="98"/>외부업체발송</label>&nbsp;&nbsp;
						<label><input type="checkbox" name="delivery_type[]" <?if(in_array("99", $delivery_type)) echo "checked='checked'";?> value="99"/>기타</label>&nbsp;&nbsp;

					</td>
				</tr>
				<tr>
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:94px;">
							<option value="OG.REG_DATE" <? if ($order_field == "OG.REG_DATE") echo "selected"; ?> >등록일</option>
							<option value="A.ADM_NAME" <? if ($order_field == "A.ADM_NAME") echo "selected"; ?> >영업담당자</option>
							<option value="OG.RESERVE_NO" <? if ($order_field == "OG.RESERVE_NO") echo "selected"; ?> >주문번호</option>
							<option value="OG.GOODS_NAME" <? if ($order_field == "OG.GOODS_NAME") echo "selected"; ?> >상품명</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> 내림차순
					</td>
					<th>검색조건</th>
					<td colspan="2">
						<!--
						<select name="nPageSize" style="width:74px;">
							<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
							<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
							<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
							<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
							<option value="400" <? if ($nPageSize == "400") echo "selected"; ?> >400개씩</option>
							<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
						</select>&nbsp;
						-->
						<select name="search_field" style="width:84px;">
							<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
							<option value="OG.RESERVE_NO" <? if ($search_field == "OG.RESERVE_NO") echo "selected"; ?> >주문번호</option>
							<option value="OG.GOODS_NAME" <? if ($search_field == "OG.GOODS_NAME") echo "selected"; ?> >상품명</option>
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
						
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
				</tr>
			</table>
			<div class="sp20"></div>

			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="10%">
					<col width="10%">
					<col width="6%">
					<col width="*">
					<col width="8%">
					<col width="8%">
					<col width="8%">
					<col width="10%">
					<col width="5%">
					<col width="7%">
				</colgroup>
				<thead>
					<tr>
						<th>주문번호</th>
						
						<th>업체/지점명</th>
						<th>상품코드</th>
						<th>상품명</th>
						<th>판매가</th>
						<th>원주문수량</th>
						<th>배송예정수량</th>
						<th>총액</th>
						<th>배송방식</th>
						<th class="end">
							<? if ($s_adm_md_tf != "Y") { ?>
								<?= makeAdminInfoByMDSelectBox($conn,"con_sale_adm_no"," style='width:70px;' ","영업사원","", $con_sale_adm_no) ?>
								<script type="text/javascript">
									$('[name=con_sale_adm_no]').on('change', function() {
									  js_search();
									})
								</script>
							<? } else { ?>
								<input type="hidden" name="con_sale_adm_no" value="<?=$con_sale_adm_no?>"/>
								영업담당
							<? } ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {

						$total_balance = 0;
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//O.RESERVE_NO, C.CP_CODE, CONCAT( C.CP_NM,  ' ', CP_NM2 ) AS CP_NAME, OG.GOODS_CODE, OG.GOODS_NAME, OG.SALE_PRICE, OG.QTY, IFNULL(K.REFUNDABLE_QTY, 0) AS REFUNDABLE_QTY, IFNULL(OGI.SUB_SUM, 0) AS SUM_SUB_QTY, A.ADM_NAME\

							$RESERVE_NO				= SetStringFromDB($arr_rs[$j]["RESERVE_NO"]);
							$CP_CODE				= SetStringFromDB($arr_rs[$j]["CP_CODE"]);
							$CP_NAME				= SetStringFromDB($arr_rs[$j]["CP_NAME"]);
							$GOODS_CODE				= SetStringFromDB($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$SALE_PRICE				= SetStringFromDB($arr_rs[$j]["SALE_PRICE"]);
							$QTY					= SetStringFromDB($arr_rs[$j]["QTY"]);
							$REFUNDABLE_QTY			= SetStringFromDB($arr_rs[$j]["REFUNDABLE_QTY"]);
							$SUM_SUB_QTY			= SetStringFromDB($arr_rs[$j]["SUM_SUB_QTY"]);
							$ADM_NAME				= SetStringFromDB($arr_rs[$j]["ADM_NAME"]);
							$DELIVERY_TYPE			= SetStringFromDB($arr_rs[$j]["DELIVERY_TYPE"]);
							
							//$ADM_NAME = getAdminName($conn, $ADM_NAME); 
							$DELIVERY_TYPE = getDcodeName($conn, "DELIVERY_TYPE", $DELIVERY_TYPE);


							$REMAIN_EACH_DELIVERY_QTY = 0;

							if($REFUNDABLE_QTY == null) { 
								$REFUNDABLE_QTY = $QTY - $SUM_SUB_QTY;
								$REMAIN_EACH_DELIVERY_QTY = $QTY - $SUM_SUB_QTY;
							} else {
								$REFUNDABLE_QTY = $REFUNDABLE_QTY - $SUM_SUB_QTY;
								$REMAIN_EACH_DELIVERY_QTY = $REFUNDABLE_QTY;
							}


							$BALANCE = $SALE_PRICE * $REFUNDABLE_QTY;

							$total_balance += $BALANCE;
							

				?>
					<tr height="40">
						<td><a href="javascript:js_view_order('','<?= $RESERVE_NO ?>')"><?= $RESERVE_NO ?></a></td>
						
						<td class="modeual_nm"><?= $CP_NAME ?></td>
						<td><?= $GOODS_CODE ?></td>
						<td class="modeual_nm"><?= $GOODS_NAME ?></td>
						<td class="price"><?= number_format($SALE_PRICE) ?></td>
						<td class="price"><?= number_format($QTY) ?></td>
						<td class="price"><?= number_format($REFUNDABLE_QTY) ?></td>
						<td class="price"><?= number_format($BALANCE) ?> 원</td>
						<td><?= $DELIVERY_TYPE ?></td>
						<td><?= $ADM_NAME ?></td>
					</tr>
				<?			
								}
				?>
					<tr height="40" style="font-weight:bold;">
						<td>합계 : </td>
						<td colspan="4"></td>
						<td colspan="3" class="price"><?= number_format($total_balance) ?> 원</td>
						<td colspan="2"></td>
					</tr>
				<?
							} else { 
						?> 
							<tr>
								<td align="center" height="50"  colspan="10">데이터가 없습니다. </td>
							</tr>
						<? 
							}
						?>
				</tbody>
			</table>
				
									
				<br />

				<div class="sp30"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
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