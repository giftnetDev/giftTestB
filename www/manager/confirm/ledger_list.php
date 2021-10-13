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
	$menu_right = "CF009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/syscode/syscode.php";

	function findRowFromDB($arr, $key, $key_value) {
		for($i = 0; $i < sizeof($arr); $i++) { 
			if($arr[$i][$key] == $key_value)
				return true;
		}
		return false;
	}


	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$arr_cl = selectCompanyLedger($conn, $str_cl_no);

			if(sizeof($arr_cl) > 0) { 
				$rs_inout_type	= $arr_cl[0]["INOUT_TYPE"]; 

				// 안정화 이후에 매출, 매입 삭제 불가토록 변경
				//if($rs_inout_type != "매출" && $rs_inout_type != "매입" && $rs_inout_type != "대변실사" && $rs_inout_type != "차변실사")
				
				$result = deleteCompanyLedger($conn, $str_cl_no, $s_adm_no);
			}
		}

?>	
<script language="javascript">
		alert('처리 되었습니다.');
</script>
<?

	}

	if($mode == "UPDATE_INOUT_DATE") { 
		
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cl_no = $chk_no[$k];

			$result = updateCompanyLedgerInoutDate($conn, $str_cl_no, $tax_confirm_date);
		}

?>	
<script language="javascript">
		alert('처리 되었습니다.');
</script>
<?

	}

#====================================================================
# Request Parameter
#====================================================================

	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$adm_no = $s_adm_no;
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

	// 2017-08-25
	// 일부 거래처에 원장 열람에 대한 권한 부여, 시스템 코드에서 수정
	// 시스템관리자 롤은 전체 원장 열람가능

	$arr_chk_all = listDcode($conn, 'LIMIT_COMPANY_LEDGER', 'Y', 'N', 'DCODE', '', 1, 1000);
		
	$filter = array();
	$filter["is_different_date"] = $is_different_date;
	$filter["inout_type"] = $sel_inout_type;

	$nListCnt = totalCntLedger($conn, $search_date_type, $start_date, $end_date, $cp_type, $adm_no, $filter, $search_field, $search_str);
	
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listLedger($conn, $search_date_type, $start_date, $end_date, $cp_type, $adm_no, $filter, $order_field, $order_str, $search_field, $search_str, $nPage, $nPageSize, $nListCnt);

	
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

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
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

				<h2>기장 전체 내역</h2>
				<div class="btnright">
					<!--<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>-->
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="100" />
					<col width="*" />
					<col width="100" />
					<col width="*" />
					<col width="50" />
				</colgroup>
				<tr>
					<th>
						<select name="search_date_type">
							<option value="ledger_date" <? if ($search_date_type == "ledger_date" || $search_date_type == "") echo "selected" ?>>기장일</option>
							<option value="reg_date" <? if ($search_date_type == "reg_date") echo "selected" ?>>등록일</option>
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
						<b>등록자 : </b><?=makeAdminInfoSelectBox($conn, $adm_no)?>&nbsp;
						<b>등록일 - 기장일 다름 : </b><input type="checkbox" name="is_different_date" value="Y" <? if($is_different_date == "Y") echo "checked";?>/>
					</td>
				</tr>
				<tr>
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:94px;">
							<option value="INOUT_DATE" <? if ($order_field == "INOUT_DATE") echo "selected"; ?> >기장일</option>
							<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?> > 오름차순 &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?>> 내림차순
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
							<option value="RESERVE_NO" <? if ($search_field == "RESERVE_NO") echo "selected"; ?> >주문번호</option>
							<option value="NAME" <? if ($search_field == "NAME") echo "selected"; ?> >기장명</option>
							<option value="MEMO" <? if ($search_field == "MEMO") echo "selected"; ?> >비고</option>
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="12" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
						<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						<a href="javascript:js_reset();"><img src="/manager/images/admin/btn_in.gif" alt="reset"/></a>
					</td>
					<td align="right"><a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a></td>
				</tr>
			</table>
			<div class="sp20"></div>
			<b>총 <?=$nListCnt?> 건</b>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

				<colgroup>
					<col width="3%" />
					<col width="8%" />
					<col width="8%" />
					<col width="3%" />
					<col width="*"/>
					<col width="3%" />
					<col width="8%" />
					<col width="8%" />
					<col width="8%" />
					<col width="6%" />
					<col width="7%" />
					<col width="8%" />
					<col width="4%" />
					<col width="4%" />
				</colgroup>
				<thead>
				<tr>
					<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
					<th>업체명</th>
					<th>날짜</th>
					<th>
						<select name="sel_inout_type" style="width:50px">
							<option value="" <?if($sel_inout_type == "") echo "selected";?>>구분</option>
							<option value="매출" <?if($sel_inout_type == "매출") echo "selected";?>>매출</option>
							<option value="매입" <?if($sel_inout_type == "매입") echo "selected";?>>매입</option>
							<option value="입금" <?if($sel_inout_type == "입금") echo "selected";?>>입금</option>
							<option value="지급" <?if($sel_inout_type == "지급") echo "selected";?>>지급</option>
							<option value="대체" <?if($sel_inout_type == "대체") echo "selected";?>>대체</option>
							<option value="대입" <?if($sel_inout_type == "대입") echo "selected";?>>대입</option>
							<option value="차변실사" <?if($sel_inout_type == "차변실사") echo "selected";?>>차변실사</option>
							<option value="대변실사" <?if($sel_inout_type == "대변실사") echo "selected";?>>대변실사</option>
						</select>
					</th>
					<th>상품명</th>
					<th>수량</th>
					<th>단가</th>
					<th>매출/지급액</th>
					<th>매입/입금액</th>
					<th>부가세</th>
					<th>등록자<br/>등록일</th>
					<th>비고</th>
					<th class="end" colspan="2">보기</th>
				</tr>
				</thead>
				<?
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);

							$REG_ADM					= trim($arr_rs[$j]["REG_ADM"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);

							if($REG_ADM != 0 && $REG_ADM != null)
								$REG_ADM = getAdminName($conn, $REG_ADM);
							else
								$REG_ADM = "자동기장";

							$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));

							$CP_NAME = getCompanyName($conn, $CP_NO);


							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

				?>
				<?
					if(findRowFromDB($arr_chk_all, "DCODE", $CP_NO) && $s_adm_group_no != 1){
				?>
				<tr style="background-color:#ddd; height:30px;">
					<td colspan="14">
						열람권한 없음
					</td>
				</tr>
				<?  } else { ?>
				<tr height="30" class="<? if($TAX_CONFIRM_TF == "Y") echo "row_tax_confirm";?> title="<? if($TAX_CONFIRM_TF == "Y") echo "발행처리일: ". date("Y년m월d일", strtotime($TAX_CONFIRM_DATE));?>">
					<td><input type="checkbox" name="chk_no[]" value="<?=$CL_NO?>"/></td>
					<td><?=$CP_NAME?></td>
					<td><?=$INOUT_DATE?></td>
					<td><?=$INOUT_TYPE?></td>
					<td class="modeual_nm">
						<?=$NAME?>
					</td>
					<td class="price"><?=number_format($QTY)?></td>
					<td class="price"><?=number_format($UNIT_PRICE)?></td>
					<td class="price"><?=number_format($DEPOSIT)?></td>
					<td class="price"><?=number_format($WITHDRAW)?></td>
					<td class="price"><?=number_format($SURTAX)?></td>
					<td title="<?=date("Y-m-d H:i",strtotime($REG_DATE))?>"><?=$REG_ADM?><br/><?=date("Y-m-d",strtotime($REG_DATE))?></td>
					<td><?if($CATE_01 <> "") echo "[".$CATE_01."] "?><?=$MEMO?></td>
					<td>
						<? if($INOUT_TYPE == "매입" || $INOUT_TYPE == "매출") { ?>
							<?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?>
						<? } ?>
					</td>
					<td>
					</td>
				</tr>
				<?    }  ?>
				<? 
						
						}

					} else { 
				?>

				<tr height="35">
					<td colspan="15">데이터가 없습니다.</td>
				</tr>

				<? } ?>
			</table>
				
				<div style="width: 95%; margin: 10px 0 20px 0; overflow:hidden;">
					<div style="width:30%; float:left;">
						
					</div>
					<div style="width:70%; float:right; text-align: right;">
						<b>기준일 : </b><input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="tax_confirm_date" value="<?=$day_0?>" maxlength="10"/>
						<input type="button" name="aa" value=" 기장 일자변경 " class="btntxt" onclick="js_update_inout_date();"> 
						<? if ($sPageRight_D == "Y") {?>
						<input type="button" name="aa" value=" 기장 삭제 " class="btntxt" onclick="js_delete();"> 
						<? } ?>
					</div>
				</div>
				<br />
				<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&search_date_type=".$search_date_type."&start_date=".$start_date."&end_date=".$end_date."&cp_type=".$cp_type."&adm_no=".$adm_no;
							$strParam = $strParam."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&is_different_date=".$is_different_date."&sel_inout_type=".$sel_inout_type;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				
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