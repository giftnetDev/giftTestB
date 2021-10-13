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
	$menu_right = "CC003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/confirm/confirm.php";

	$bb_code = "CALL";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	//if ($mode == "S") {

		$arr_rs = selectCompany($conn, $cp_no);

		$rs_cp_no							= trim($arr_rs[0]["CP_NO"]); 
		$rs_cp_cate							= SetStringFromDB($arr_rs[0]["CP_CATE"]); 
		$rs_cp_nm							= SetStringFromDB($arr_rs[0]["CP_NM"]); 
		$rs_cp_nm2							= SetStringFromDB($arr_rs[0]["CP_NM2"]); 
		$rs_cp_code							= SetStringFromDB($arr_rs[0]["CP_CODE"]); 
		$rs_cp_type							= SetStringFromDB($arr_rs[0]["CP_TYPE"]); 
		$rs_ad_type							= SetStringFromDB($arr_rs[0]["AD_TYPE"]); 
		$rs_cp_phone						= SetStringFromDB($arr_rs[0]["CP_PHONE"]); 
		$rs_cp_hphone						= SetStringFromDB($arr_rs[0]["CP_HPHONE"]); 
		$rs_cp_fax							= SetStringFromDB($arr_rs[0]["CP_FAX"]); 
		$rs_cp_zip							= trim($arr_rs[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs[0]["CP_ADDR"]); 
		$rs_re_zip							= trim($arr_rs[0]["RE_ZIP"]); 
		$rs_re_addr							= SetStringFromDB($arr_rs[0]["RE_ADDR"]); 
		$rs_biz_no							= trim($arr_rs[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs[0]["CEO_NM"]); 
		$rs_upjong							= SetStringFromDB($arr_rs[0]["UPJONG"]); 
		$rs_uptea							= SetStringFromDB($arr_rs[0]["UPTEA"]); 
		$rs_account_bank				    = SetStringFromDB($arr_rs[0]["ACCOUNT_BANK"]); 
		$rs_account							= trim($arr_rs[0]["ACCOUNT"]); 
		$rs_account_owner_nm		        = trim($arr_rs[0]["ACCOUNT_OWNER_NM"]); 
		$rs_homepage						= SetStringFromDB($arr_rs[0]["HOMEPAGE"]); 
		$rs_memo							= trim($arr_rs[0]["MEMO"]); 
		$rs_dc_rate							= trim($arr_rs[0]["DC_RATE"]); 
		$rs_sale_adm_no                     = trim($arr_rs[0]["SALE_ADM_NO"]);
		$rs_manager_nm					    = SetStringFromDB($arr_rs[0]["MANAGER_NM"]); 
		$rs_phone							= SetStringFromDB($arr_rs[0]["PHONE"]); 
		$rs_hphone							= SetStringFromDB($arr_rs[0]["HPHONE"]); 
		$rs_fphone							= SetStringFromDB($arr_rs[0]["FPHONE"]); 
		$rs_email							= SetStringFromDB($arr_rs[0]["EMAIL"]); 
		$rs_email_tf						= trim($arr_rs[0]["EMAIL_TF"]); 
		$rs_contract_start			        = trim($arr_rs[0]["CONTRACT_START"]); 
		$rs_contract_end			    	= trim($arr_rs[0]["CONTRACT_END"]); 
		$rs_is_mall							= trim($arr_rs[0]["IS_MALL"]); 
		$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf							= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_adm							= trim($arr_rs[0]["REG_ADM"]); 
		$rs_reg_date						= trim($arr_rs[0]["REG_DATE"]); 
		$rs_up_adm							= trim($arr_rs[0]["UP_ADM"]); 
		$rs_up_date							= trim($arr_rs[0]["UP_DATE"]); 
		$rs_del_adm							= trim($arr_rs[0]["DEL_ADM"]); 
		$rs_del_date						= trim($arr_rs[0]["DEL_DATE"]); 
		
		

		if ($rs_contract_start <> "0000-00-00") {
			$rs_contract_start = date("Y-m-d",strtotime($rs_contract_start));
		} else {
			$rs_contract_start = "";
		}


		if ($rs_contract_end <> "0000-00-00") {
			$rs_contract_end = date("Y-m-d",strtotime($rs_contract_end));
		} else {
			$rs_contract_end = "";
		}

		$arr_company_etc = listCompanyEtc($conn, $cp_no);

	//}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
	$(function() {
		$("#tabs").tabs({
		  active : 0
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

	function js_view(cp_no) {
		var frm = document.frm;
		
		frm.cp_no.value = cp_no;
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_view(rn, bb_code, bb_no) {

		var frm = document.frm;
		
		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "call_center_write.php";
		frm.submit();
		
	}
	

	function js_view_order(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

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

				<h2>상담 메인</h2>

				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="15%" />
						<col width="*" />
						<col width="15%" />
						<col width="*" />
					</colgroup>
					<tr>
						<th>
							거래처 조회
						</th>
						<td colspan="3">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_no" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_no)?>" />
							<input type="hidden" name="cp_no" value="<?=$cp_no?>">

							<script>
								$(function(){

									$("input[name=txt_cp_no]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											
											if(keyword == "") { 
												$("input[name=cp_no]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('판매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_no", data[0].label, "cp_no", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=판매,판매공급&search_str="+keyword + "&target_name=txt_cp_no&target_value=cp_no",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});
									
									$("input[name=txt_cp_no]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cp_no]").val('');
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
							<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
						</td>
					</tr>
				</table>

				<div id="tabs" style="width:95%; margin:10px 0;">
					<ul>
						
						<li><a href="#tabs-1">거래처 정보</a></li>
						<li><a href="#tabs-2">최근 거래내역</a></li>
						<li><a href="#tabs-3">최근 상담 내역</a></li>
						<li><a href="#tabs-0">나의 거래처</a></li>
					</ul>
					
					<div id="tabs-1">
						<!--<h3>거래처 정보</h3>-->
						<table cellpadding="0" cellspacing="0" class="colstable">

							<colgroup>
								<col width="12%">
								<col width="38%">
								<col width="12%">
								<col width="38%">
							</colgroup>
							<thead>
								<tr>
									<th>업체명</th>
									<td>
										<?= $rs_cp_nm ?>
										<?= $rs_cp_nm2 ?>
									</td>
									<th>관리코드</th>
									<td>
										<?= $rs_cp_code ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>업체구분</th>
									<td>
										<?= getDcodeName($conn, "CP_TYPE", $rs_cp_type)?>
									</td>
									<th title="판매:판매가에 할인율을 적용, 카드:수수율을 적용">할인/수수율</th>
									<td>
										<?= $rs_dc_rate?> %
									</td>
								</tr>
								<tr>
									<th>사업자	등록번호</th>
									<td>
										<?= $rs_biz_no?>
									</td>
									<th>대표자명</th>
									<td><?= $rs_ceo_nm ?></td>
								</tr>
								<tr>
									<th>대표 전화번호</th>
									<td>
										<?= $rs_cp_phone?>
									</td>
									<th>대표 FAX</th>
									<td>
										<?= $rs_cp_fax?>
									</td>
								</tr>
								<tr>
									<th>주소 1</th>
									<td colspan="3">
										<?= $rs_cp_zip?> <?= $rs_cp_addr?>
									</td>
								<tr>
								<tr>
									<th>주소 2</th>
									<td colspan="3">
										<?= $rs_re_zip?> <?= $rs_re_addr?>
									</td>
								<tr>
								<tr>
									<th>담당자 명</th>
									<td><?= $rs_manager_nm ?></td>
									<th>전화번호</th>
									<td>
										<?= $rs_phone ?>
									</td>
								</tr>
								<tr>
									<th>휴대 전화번호</th>
									<td>
										<?= $rs_hphone ?>
									</td>
									<th>FAX 번호</th>
									<td>
										<?= $rs_fphone ?>
									</td>
								<tr>
								<tr>
									<th>이메일</th>
									<td><?= $rs_email ?></td>
									<th>이메일 수신여부</th>
									<td>
										<? if (($rs_email_tf =="Y") || ($rs_email_tf =="")) echo "수신"; ?>
									</td>
								<tr>
								<tr>
									<th>영업담당자</th>
									<td>
										<?=getAdminName($conn, $rs_sale_adm_no)?>
									</td>
									<th>결재구분</th>
									<td>
										<?= getDcodeName($conn,"AD_TYPE",$rs_ad_type)?>
									</td>
								</tr>
								<tr>
									<th>업체메모</th>
									<td colspan="3" class="memo">
										<?= str_replace("char(13)", "<br/>", $rs_memo) ?>
									</td>
								</tr>
								
							</tbody>
						</table>


					</div>
					<div id="tabs-2">
						<?
							//($db, $start_date, $end_date, $cp_no, $order_field = "", $order_str = "", $search_field = "", $search_str = "", $nRowCount = 10000) {
							if($cp_no != "")
								$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_no, "INOUT_DATE", "DESC", "", "", 10);
						?>

						<!--<h3>최근 거래내역</h3>-->
						*. 최근 20개 거래의 기장을 표시합니다.
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
								<col width="8%" />
								<col width="10%" />
								<col width="8%" />
								<col width="7%" />
								<col width="5%" />
							</colgroup>
							<tr>
								<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
								<th>날짜</th>
								<th>구분</th>
								<th>상품명</th>
								<th>수량</th>
								<th>단가</th>
								<th>매출/지급액</th>
								<th>매입/입금액</th>
								<th>부가세</th>
								<th>잔액</th>
								<th>비고</th>
								<th class="end" colspan="2">
									주문/발주
									<!--
									<select name="show_type" onchange="js_search();">
										<option value="1" <?if($show_type == "1" || $show_type == "") echo "selected";?>>주문/발주</option>
										<option value="2" <?if($show_type == "2") echo "selected";?>>세금계산서</option>
									</select>
									-->
								</th>
							</tr>

						<?
							if (sizeof($arr_rs) > 0) {
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

									//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

									$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
									$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
									$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
									$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
									$NAME						= trim($arr_rs[$j]["NAME"]);
									$QTY						= trim($arr_rs[$j]["QTY"]);
									$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
									$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
									$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
									$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
									$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
									$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
									$MEMO						= trim($arr_rs[$j]["MEMO"]);
									$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
									$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
									$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);
									$TO_CP_NO					= trim($arr_rs[$j]["TO_CP_NO"]);

									$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

									$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
									$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

									//$CF_CODE					= trim($arr_rs[$j]["CF_CODE"]);


									$INOUT_DATE = date("Y-m-d",strtotime($INOUT_DATE));

									if($USE_TF == "Y")
										$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
									else { 
										$QTY = 0;
										$WITHDRAW = 0;
										$DEPOSIT = 0;
										$SURTAX = 0;
									}

									if($TAX_CONFIRM_TF == "Y") {

										$arr_cf_code = listTaxInvoiceConfirmCode($conn, $CL_NO);
										
										//for($p = 0; $p < sizeof($arr_cf_code); $p ++) { 
										//	if(chkCashStatementByCFCode($conn, $CF_CODE) <= 0)
												$str_tax_class = "row_tax_confirm";
										//	else
										//		$str_tax_class = "row_tax_confirm_safe";
										//}
									} else { 
										$arr_cf_code = null;
										$str_tax_class = "";
									}

									if($INOUT_TYPE == "매입") { 
										//2017-07-19 과세 비과세 기장 자체에 입력으로 주문에서 가져오지 않음
										//$TAX_TF = getOrderGoodsTaxTF($conn, $ORDER_GOODS_NO);
										$TAX_TF =  getGoodsTaxTF($conn, $GOODS_NO);
									} 

									if ($TAX_TF == "비과세") {
										$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
									} else if ($TAX_TF == "과세") {
										$STR_TAX_TF = "<font color='navy'>(과세)</font>";
									} else
										$STR_TAX_TF = "";
									
						?>
						<tr height="30" class="<?=$str_tax_class?> <?if($USE_TF != "Y") echo "closed";?>">
							<td><input type="checkbox" name="chk_no[]" value="<?=$CL_NO?>"/></td>
							<td><?=$INOUT_DATE?></td>
							<td><?=$INOUT_TYPE?></td>
							<td class="modeual_nm">
								<?=$STR_TAX_TF?>
								<?=$NAME?>
								
							</td>
							<td class="price"><?=number_format($QTY)?></td>
							<td class="price"><?=number_format($UNIT_PRICE)?></td>
							<td class="price row_deposit" data-value="<?=$DEPOSIT?>"><?=number_format($DEPOSIT)?></td>
							<td class="price row_withdraw" data-value="<?=$WITHDRAW?>"><?=number_format($WITHDRAW)?></td>
							<td class="price row_surtax" data-value="<?=$SURTAX?>"><?=number_format($SURTAX)?></td>
							<td class="price"><?=number_format($BALANCE, 0)?></td>
							<td class="memo_trigger" data-cl_no="<?=$CL_NO?>">
								<?if($CATE_01 <> "") echo "[".$CATE_01."] "?>
								<?=$MEMO?>
							</td>
							<td colspan="2">

								<? if($show_type == "1" || $show_type == "") { ?>
									<? if($INOUT_TYPE == "매입" || $INOUT_TYPE == "매출") { ?>
										<?=getLinkScriptForOrderView($conn, $RESERVE_NO, $ORDER_GOODS_NO, $RGN_NO)?>
									<? } ?>
								<? } ?>
								<? if($show_type == "2") { ?>

									<? 
										
										for($p = 0; $p < sizeof($arr_cf_code); $p ++) { 

											$t_cf_code = $arr_cf_code[$p]["CF_CODE"];
											?>
												<div class="btn_cf_code" data-cf_code="<?=$t_cf_code?>" data-total_price="" ><?=$t_cf_code?></div>
											<?
										}
									
									?>
									
								<? } ?>

								<?
									$rs_biz_no	= "";
									if($rs_cp_type == "통장") { 
										if($TO_CP_NO  > 0) { 
											$arr_rs_company = selectCompany($conn, $TO_CP_NO);
									
											if(sizeof($arr_rs_company)) { 
												$rs_biz_no	= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
											}
										}
									}
								?>
								<?=$rs_biz_no?>
							</td>
						</tr>
						<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="13">최근 거래내역이 없습니다. </td>
								</tr>
							<? 
								}
							?>
						</table>



					</div>
					<div id="tabs-3">
						<!--<h3>상담내역 입력</h3>-->

						<?
							$arr_rs = listBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $cp_no, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);
						?>
						<table cellpadding="0" cellspacing="0" class="rowstable">
						<colgroup>
						  <col width="5%" />
						  <col width="7%" />
						  <col width="5%" />
						  <col width="15%" />
						  <col width="7%" />
						  <col width="*" />
						  <col width="10%" />
						</colgroup>
						<tr>
							<th><input type="checkbox" name="chk_all" value=""/></th>
							<th>날짜</th>
							<th>시간</th>
							<th>문의업체</th>
							<th>담당자</th>
							<th>문의내용</th>
							<th class="end"></th>
						</tr>
						<?
							$nCnt = 0;
							
							if (sizeof($arr_rs) > 0) {
								
								for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
									
									$rn							= trim($arr_rs[$j]["rn"]);
									$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
									$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
									$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
									$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
									$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
									$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
									$WRITER_NM				= trim($arr_rs[$j]["WRITER_NM"]);
									$WRITER_PW				= trim($arr_rs[$j]["WRITER_PW"]);
									$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
									$CONTENTS				= SetStringFromDB($arr_rs[$j]["CONTENTS"]);
									$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
									$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
									$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
									$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);
									
									$RS_DATE = date("Y-m-d",strtotime($REG_DATE));
									$RS_TIME = date("H:i",strtotime($REG_DATE));
						
						?>
						<tr height="35"> 
							<td><a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$rn?></a></td>
							<td><?= $RS_DATE ?></td>
							
							<td><?= $RS_TIME ?></td>
							<td class="modeual_nm">
								<?
									if($CATE_04 <> "") 
										echo getCompanyName($conn,$CATE_04);
									else
										echo $CATE_03;
								?>
							</td>
							<td class="modeual_nm">
								<?
									if($WRITER_PW <> "") 
										echo getAdminName($conn,$WRITER_PW);
									else
										echo $WRITER_NM;
								?>
							</td>
							<td class="modeual_nm">
								<a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');">
								<?
									$CONTENTS = nl2br($CONTENTS);
									echo $CONTENTS;
								?>
								</a>
							</td>
							<td></td>
						</tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="7">최근 상담내역이 없습니다. </td>
								</tr>
							<? 
								}
							?>
						</table>

					</div>
					<div id="tabs-0">
						<!--<h3>나의 거래처</h3>-->
						<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
							<colgroup>
								<col width="5%">
								<col width="5%">
								<col width="*">
								<col width="10%">
								<col width="12%">
								<col width="10%">
								<col width="7%">
								<col width="7%">
								<col width="7%">
								<col width="7%">
								<col width="10%">
							</colgroup>
							<thead>
								<tr>
									<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
									<th>업체번호</th>
									<th>[관리코드] 업체명 - 지점명</th>
									<th>담당자명</th>
									<th>연락처</th>
									<th>팩스</th>
									<th>업체구분</th>
									<th>결제구분</th>
									<th>밴더할인<br/>/수수율</th>
									<th>등록일</th>
									<th class="end">영업담당자</th>
								</tr>
							</thead>
							<tbody>
							<?

								$arr_rs = listCompany($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $s_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
																		
										$rn							= trim($arr_rs[$j]["rn"]);
										$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
										$CP_CODE				= trim($arr_rs[$j]["CP_CODE"]);
										$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]);
										$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]);
										$CEO_NM					= SetStringFromDB($arr_rs[$j]["CEO_NM"]);
										$CP_TYPE				= SetStringFromDB($arr_rs[$j]["CP_TYPE"]);
										$AD_TYPE				= SetStringFromDB($arr_rs[$j]["AD_TYPE"]);
										$SALE_ADM_NO		    = SetStringFromDB($arr_rs[$j]["SALE_ADM_NO"]);
										$MANAGER_NM			    = SetStringFromDB($arr_rs[$j]["MANAGER_NM"]);
										$CP_PHONE				= SetStringFromDB($arr_rs[$j]["CP_PHONE"]);
										$CP_FAX					= SetStringFromDB($arr_rs[$j]["CP_FAX"]);
										$PHONE					= SetStringFromDB($arr_rs[$j]["PHONE"]);
										$DC_RATE				= SetStringFromDB($arr_rs[$j]["DC_RATE"]);
										
										$CONTRACT_START	= trim($arr_rs[$j]["CONTRACT_START"]);
										$CONTRACT_END		= trim($arr_rs[$j]["CONTRACT_END"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
										
										$CONTRACT_START = date("Y-m-d",strtotime($CONTRACT_START));
										$CONTRACT_END		= date("Y-m-d",strtotime($CONTRACT_END));
										$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

										$SALE_ADM_NM = getAdminInfoNameMD($conn, $SALE_ADM_NO); 
										
										if($USE_TF == "N")
											$str_use_style = "unused";
										else
											$str_use_style = "";
							
							?>
								<tr class="<?=$str_use_style ?>" >
									<td>
										<input type="checkbox" name="chk_no[]" value="<?=$CP_NO?>">
									</td>
									<td><?=$CP_NO?></td>
									<td class="modeual_nm"><a href="javascript:js_view('<?= $CP_NO ?>');">[<?=$CP_CODE?>] <?= $CP_NM ?> <?= $CP_NM2 ?></a></td>
									<td><?= $MANAGER_NM ?></td>
									<td><?= $CP_PHONE ?></td>
									<td><?= $CP_FAX ?></td>
									<td><?= getDcodeName($conn, "CP_TYPE", $CP_TYPE);?></td>
									<td><?= getDcodeName($conn, "AD_TYPE", $AD_TYPE);?></td>
									<td><?= ($DC_RATE != "0" ? $DC_RATE."%" : "") ?></td>
									<td class="filedown"><?= $REG_DATE ?></td>
									<td><?= $SALE_ADM_NM ?></td>
								</tr>
							<?			
											}
										} else { 
									?> 
										<tr>
											<td align="center" height="50"  colspan="11">내가 관리중인 데이터가 없습니다. </td>
										</tr>
									<? 
										}
									?>
							</tbody>
						</table>

					</div>
				</div>

				
				
				
				
			
			</div>

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
