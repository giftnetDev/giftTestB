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
	$menu_right = "CF011"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode			= trim($mode);

	$result	= false  ;

#====================================================================
# DML Process
#====================================================================

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];
			
			$result = deleteCompanyLedger($conn, $str_cl_no, $s_adm_no);
		
		}
	}

	if ($mode == "I") {

		$params = array('OP_CP_NO' => $op_cp_no, 'CF_INOUT' => $cf_inout, 'CF_TYPE' => $cf_type, 'CF_CODE' => $cf_code, 'BIZ_NO' => $biz_no, 'CP_NM' => $cp_nm, 'GOODS_NM' => $goods_nm, 'OUT_DATE' => $out_date, 'WRITTEN_DATE' => $written_date, 'SUPPLY_PRICE' => $supply_price, 'SURTAX' => $surtax, 'TOTAL_PRICE' => $total_price);

		if($cf_no <> "") { 
			$result = updateCashFlow($conn, $params, $s_adm_no, $cf_no);
		} else { 
			$result = insertCashFlow($conn, $params, $s_adm_no);
		}

		if($result) { 
?>
<script type="text/javascript">
	
    <?
		if($cf_no <> "") { 	
	?>
		document.location = "cash_flow_statement.php";
	<?
		} else { 	
	?>
	
	if(confirm('저장되었습니다. 계속 입력하실꺼면 취소를 눌러주세요.'))
		document.location = "cash_flow_statement.php";
	else
		document.location = "cash_flow_statement_write.php";

	<? } ?>
	
</script>
<?
		} else { 
?>
<script type="text/javascript">
	
	alert('에러 발생 되었습니다. 시스템 담당자랑 상의해주세요.');

</script>
<?

		}
	}


	$out_date = date("Y-m-d",strtotime("0 month"));
	$written_date = date("Y-m-d",strtotime("0 month"));

	/*
	if($cf_no <> "") { 
		
		$arr = selectCashFlowAccount($conn, $cf_no);
		
		$OP_CP_NO			= $arr[0]["OP_CP_NO"];
		$ACCOUNT_CP_NO		= $arr[0]["ACCOUNT_CP_NO"];
		$SALE_CP_NO			= $arr[0]["SALE_CP_NO"];
		$OUT_DATE			= $arr[0]["OUT_DATE"];
		$WRITTEN_DATE		= $arr[0]["WRITTEN_DATE"];
		$IN_DATE			= $arr[0]["IN_DATE"];
		$CASH				= $arr[0]["CASH"];
		$SALE_ADM_NO		= $arr[0]["SALE_ADM_NO"];

		if($OUT_DATE == "0000-00-00")
			$OUT_DATE = "";
		if($WRITTEN_DATE == "0000-00-00")
			$WRITTEN_DATE = "";
		if($IN_DATE == "0000-00-00")
			$IN_DATE = "";

	}
	*/
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js?v=5"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
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
<script language="javascript">

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


	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		
		
		if (isNull(frm.op_cp_no.value)) {
			alert('구분을 선택해주세요.');
			frm.op_cp_no.focus();
			return ;		
		}

		if (isNull(frm.cf_inout.value)) {
			alert('종류를 선택해주세요.');
			frm.cf_inout.focus();
			return ;		
		}

		if (isNull(frm.cf_type.value)) {
			alert('승인종류를 선택해주세요.');
			frm.cf_type.focus();
			return ;		
		}
		
		
		/*
		if (isNull(frm.cf_code.value)) {
			alert('승인번호를 입력해주세요.');
			frm.cf_code.focus();
			return ;		
		}
		*/
		if(frm.cf_type.value == "CF003") { 
			if (isNull(frm.biz_no.value)) {
				alert('사업자번호를 입력해주세요.');
				frm.biz_no.focus();
				return ;		
			}
		}

		if (isNull(frm.cp_nm.value)) {
			alert('상호를 입력해주세요.');
			frm.cp_nm.focus();
			return ;		
		}

		/*
		if (isNull(frm.goods_nm.value)) {
			alert('물품명을 입력해주세요.');
			frm.goods_nm.focus();
			return ;		
		}
		*/

		if (isNull(frm.out_date.value)) {
			alert('발행일을 선택해주세요.');
			frm.out_date.focus();
			return ;		
		}

		if (isNull(frm.written_date.value)) {
			alert('작성일을 선택해주세요.');
			frm.written_date.focus();
			return ;		
		}	

		if (isNull(frm.supply_price.value)) {
			alert('공급액을 입력해주세요.');
			frm.supply_price.focus();
			return ;		
		}

		if (isNull(frm.surtax.value)) {
			alert('과세액을 입력해주세요.');
			frm.surtax.focus();
			return ;		
		}

		if (isNull(frm.total_price.value)) {
			alert('합계액을 입력해주세요.');
			frm.total_price.focus();
			return ;		
		}
		frm.mode.value = "I";

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_list() {

		location.href = "company_ledger_list.php<?=$strParam?>";
	}

	function js_calculate_surtax() {

		var i_susu_rate			= 0;
		var i_unit_price		= 0;
		var f_surtax			= 0;

		if ($("input[name=susu_rate]").val() != "") i_susu_rate = parseFloat($("input[name=susu_rate]").val().replaceall(",", ""));

		if ($("input[name=unit_price]").val() != "") i_unit_price = parseInt($("input[name=unit_price]").val().replaceall(",", ""));

		if(i_unit_price == "0") return;
		if($("input[name=inout_method]:checked").val() != "카드") return;

		f_surtax =  Math.round10(i_unit_price * i_susu_rate / 100.0, 0); 

		var unit_price = i_unit_price - f_surtax;

		$("input[name=surtax]").val(numberFormat(f_surtax));
		$("input[name=unit_price]").val(numberFormat(unit_price));

	}

	function js_calculate_show_price() { 

		var i_prev_balance = 0;
		var i_next_balance = 0;
		
		var i_current_price = $("input[name=unit_price]").val().replaceall(",", "");

		if(i_current_price == "NaN")
			$("input[name=unit_price]").val('');

		if(!$.isNumeric(i_current_price)) { 
			//$("input[name=unit_price]").val('');
			$(".remain_balance").html("...");
			return;
		}


		if ($(".get_balance").html() != "...") i_prev_balance = parseInt($(".get_balance").html().replaceall(",", "").replaceall("원", ""));
		
		if($("input[name=inout_type]:checked").val() == "지급" || $("input[name=inout_type]:checked").val() == "입금") { 
			if($("input[name=inout_type]:checked").val() == "지급") 
				i_next_balance = parseInt(i_prev_balance) + parseInt(i_current_price);
			else 
				i_next_balance = parseInt(i_prev_balance) - parseInt(i_current_price);

			$(".remain_balance").html(numberFormat(i_next_balance) + " 원");
		}
		else
			$(".remain_balance").html("...");
		
	}

	function js_link_to_company_ledger(cp_no) {

		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + cp_no,'_blank');
		
	}

</script>
<style>
.input-disabled{background-color:#EBEBE4;border:1px solid #ABADB3;padding:2px 1px;color:rgb(84, 84, 84);}
</style>

</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<input type="hidden" name="cf_no" value="<?=$cf_no?>">

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
				<h2>자금총괄표 - 계산서 개별 입력</h2>  
				<div class="sp5"></div>
				<!--
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="16%">
					<col width="34%">
					<col width="16%">
					<col width="34%">
				</colgroup>
					
					
					<tr>
						<th>통장명</th>
						<td class="line">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_account_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$ACCOUNT_CP_NO)?>" />
							<input type="hidden" name="account_cp_no" value="<?=$ACCOUNT_CP_NO?>">

							<script>
								$(function(){

									$("input[name=txt_account_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=account_cp_no]").val('');
											} else { 
											
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('통장') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_account_cp_no", data[0].label, "account_cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=통장&search_str="+keyword + "&target_name=txt_account_cp_no&target_value=account_cp_no",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_account_cp_no]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=account_cp_no]").val('');
										}
									});

								});
							</script>
						</td>
						<th> </th>
						<td class="line">
							
						</td>
					</tr>
					<tr>
						<th>업체</th>
						<td class="line">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_sale_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$SALE_CP_NO)?>" />
							<input type="hidden" name="sale_cp_no" value="<?=$SALE_CP_NO?>">

							<script>
								$(function(){

									$("input[name=txt_sale_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=sale_cp_no]").val('');
											} else { 
											
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_sale_cp_no", data[0].label, "sale_cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,구매,판매공급&search_str="+keyword + "&target_name=txt_sale_cp_no&target_value=sale_cp_no",'pop_company_searched_list2','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_sale_cp_no]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=sale_cp_no]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){
										
										//alert(target_name + " " + cp_nm  + " " + target_value + " " + cp_no);
										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});
								}
							</script>
						</td>
						<th></th>
						<td class="line">
							
						</td>
					</tr>
				</table>
				<div class="sp20"></div>
				-->
		
				<table cellpadding="0" cellspacing="0" class="colstable">
					
					<colgroup>
						<col width="10%">
						<col width="*">
						<col width="10%">
						<col width="*">
						<col width="10%">
						<col width="*">
					</colgroup>
					<tr>
						<th>
							구분
						</th>
						<td>
							<?
								$arr_op = getOperatingCompany($conn, '');
								echo makeGenericSelectBox($conn, $arr_op, 'op_cp_no', '100', "선택", "", $OP_CP_NO, "CP_NO", "CP_NM");
							?>
						</td>
						<th>종류</th>
						<td>
							<select name="cf_inout">
								<option value="" <?if($cf_inout == "") echo "selected";?>>선택</option>
								<option value="매출" <?if($cf_inout == "매출") echo "selected";?>>매출</option>
								<option value="매입" <?if($cf_inout == "매입") echo "selected";?>>매입</option>
							</select>
						</td>
						
						<td colspan="2">
						</td>
					</tr>
					<tr>
						<th>승인종류</th>
						<td>
							<?=makeSelectBox($conn, 'CASH_STATEMENT_TYPE', 'cf_type','100','선택','',$cf_type)?>
							<script type="text/javascript">
								$(function(){
									$("[name=cf_type] > option[value=CF001], [name=cf_type] > option[value=CF002]").remove();
								});
							</script>
						</td>
						<th>승인번호</th>
						<td>
							<input type="text" name="cf_code" value="<?=$cf_code?>" style="width:80px; margin-right:3px;" class="txt">
						</td>
						<td colspan="2">
						</td>
					</tr>
					<tr>
						<th>사업자번호</th>
						<td>
							<input type="text" name="biz_no" value="<?=$biz_no?>" style="width:80px; margin-right:3px;" class="txt">
							<script type="text/javascript">
								$(function(){
									$("[name=biz_no]").keyup(function(e){

										if(e.keyCode == "8" ||  e.keyCode == "46") return;

										var typed = $(this).val();
										if(typed.length == "3" || typed.length == "6") { 
											typed += "-";
										}
										$(this).val(typed);
									});
								});
							</script>
						</td>
						<th>상호</th>
						<td>
							<input type="text" name="cp_nm" value="<?=$cp_nm?>" style="width:80%;" class="txt">
						</td>
						<th>물품명</th>
						<td>
							<input type="text" name="goods_nm" value="<?=$goods_nm?>" style="width:80%;" class="txt">
						</td>
					</tr>
					<tr>
						<th>발행일</th>
						<td class="line">
							<input type="Text" name="out_date" value="<?=$out_date?>" style="width:80px; margin-right:3px;" class="txt datepicker">
						</td>
						<th>작성일</th>
						<td class="line">
							<input type="Text" name="written_date" value="<?=$written_date?>" style="width:80px; margin-right:3px;" class="txt datepicker">
						</td>
						<td colspan="2" class="line">
						</td>
					</tr>
					<tr>
						<th>공급액</th>
						<td class="line">
							<input type="Text" name="supply_price" value="<?=$supply_price?>"  required class="txt">
						</td>
						<th>세액</th>
						<td class="line">
							<input type="Text" name="surtax" value="<?=$surtax?>"  required class="txt">
						</td>
						<td style="text-align:right;">합계액 : </td>
						<td class="line">
							<input type="Text" name="total_price" value="<?=$total_price?>" readonly class="txt">
							<script type="text/javascript">
								$("[name=supply_price], [name=surtax]").keyup(function(){
									var supply_price = $("[name=supply_price]").val();
									var surtax = $("[name=surtax]").val();

									if(supply_price != "" && surtax != "") { 
										$("[name=total_price]").val(parseInt(supply_price) + parseInt(surtax));
									} else
										return;
								});
							
							</script>
						</td>
					</tr>
					
				</table>
				
				
			<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();" class="btn_insert"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
				<? } ?>
			</div>  
			
			<!-- --------------------- 페이지 처리 화면 END -------------------------->
			<!--
			<h2> 최근 기장 내역 </h2>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="3%" />
					<col width="*"/>
					<col width="3%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="10%" />
					<col width="8%" />
					<col width="11%" />
					<col width="11%" />
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>날짜</th>
					<th>구분</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>비고</th>
					<th>업체</th>
					<th class="end">대입처</th>
				</tr>
				</thead>
				<?
					$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type, $order_field = "REG_DATE", $order_str = "DESC", $search_field = "LATEST_5_BY_REG_ADM", $search_str = $s_adm_no, $nRowCount = 5);

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);

							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

							//$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;

				?>

				<tr height="30">
					<td><input type="checkbox" name="chk_no[]" value="<?=$CL_NO?>"/></td>
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<? if($INOUT_TYPE == "매입" || $INOUT_TYPE == "매출") { ?>
							<a href="javascript:js_view('<?=$CL_NO?>');"><?=$NAME?></a>
						<? } else { ?>
							<?=$NAME?>
						<? } ?>
					</td>
					<td class="price"><?=number_format($QTY)?></td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price"><?=number_format($DEPOSIT)?></td>
					<td class="price"><?=number_format($WITHDRAW)?></td>
					<td class="price"><?=number_format($SURTAX)?></td>
					<td><?=$MEMO?></td>
					<td><a href="javascript:js_link_to_company_ledger('<?=$CP_NO?>');"><?=getCompanyNameWithNoCode($conn, $CP_NO)?></a>
					</td>
					<td><a href="javascript:js_link_to_company_ledger('<?=$TO_CP_NO?>');"><?=getCompanyNameWithNoCode($conn, $TO_CP_NO)?></a>
					</td>
				</tr>

				<? 
						}
					} else { 
				?>

				<tr height="35">
					<td colspan="12">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
			<div class="btnright">
				<? if ($sPageRight_D == "Y") {?>
					<input type="button" name="aa" value=" 선택한 기장 삭제 " class="btntxt" onclick="js_delete();"> 
				<? } ?>
			</div>
			-->
      </div>
      <!-- // E: mwidthwrap -->
    </td>
  </tr>
  </table>
</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>