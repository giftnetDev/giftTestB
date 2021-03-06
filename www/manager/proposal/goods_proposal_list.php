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
	$menu_right = "GD007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/proposal/proposal.php";


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

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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

	if ($mode == "D") {

		$arrlength = count($chk_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_gp_no = $chk_no[$x];

			DeleteGoodsProposal($conn, $temp_gp_no, $s_adm_no);
		}
	}

	if ($mode == "C") {

		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_gpg_no = $chk_rg_no[$x];

			UpdateGoodsProposalGoodsStatus($conn, $temp_gpg_no, $s_adm_no);
		}
		
	}

	
	$nListCnt =totalCntGoodsProposal($conn, $start_date, $end_date, $con_cp_type, $del_tf, $search_field, $search_str, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listGoodsProposal($conn, $start_date, $end_date, $con_cp_type, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
	

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;

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

		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});

	$(document).ready(function(){
		//링크 생성 버튼 클릭 이벤트 바인딩
		$("input[name='btnCreateLink']").on("mousedown", function() {
			var reg_adm = "<?=$s_adm_no?>";
			if($("input[name='chk_no[]']:checked").length == 1){
				var gp_no = $("input[name='chk_no[]']:checked").val();
			} else {
				alert("한개의 제안서를 선택해주십시오.");
			}
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'json',
				type: 'post',
				data : {
				'mode': "INSERT_LINK",
				'gp_no': gp_no,
				"reg_adm": reg_adm
				},
				success: function(response) {
					if(response != false){
						link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info.php?key=" + response;
						var $temp = $("<input id='clipboard' />");
						$("body").append($temp);
						$('#clipboard').val(link);
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});
		});
		
		$("input[name='btnCreateLink']").on("mouseup", function() {
			setTimeout(function() {
				var $input = $("#clipboard");
				if ($input.length && $input.val().length > 0) {
					$input.select();
					document.execCommand("copy");
					$input.remove();
				}
				alert("링크가 복사되었습니다.\n\n"+link);
				link="";
			}, 100);
		});

		$("input[name='btnCreateImage']").on("click", function() {
			var reg_adm = "<?=$s_adm_no?>";
			if($("input[name='chk_no[]']:checked").length == 1){
				var gp_no = $("input[name='chk_no[]']:checked").val();
			} else {
				alert("한개의 제안서를 선택해주십시오.");
			}
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'json',
				type: 'post',
				data : {
				'mode': "INSERT_LINK",
				'gp_no': gp_no,
				"reg_adm": reg_adm
				},
				success: function(response) {
					if(response != false){
						link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info_image.php?key=" + response;
						window.open(link, '_blank', 'width=450, height=768');
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText); 
				}
			});
		});
	});//ready
</script>
  <script type="text/javascript" >

	function js_write() {

		var frm = document.frm;
		
		//var url = "pop_goods_proposal_write.php";

		//NewWindow(url, 'pop_goods_proposal_write','860','600','YES');
		
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_proposal_write.php";
		frm.submit();
		
	}

	function js_view(gp_no) {

		var frm = document.frm;

		frm.gp_no.value = gp_no;
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_proposal_write.php";
		frm.submit();

		//var url = "pop_goods_proposal.php";

		//NewWindow(url, 'pop_goods_proposal','860','600','YES');
		
		
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {
		
		//alert("준비중 입니다..");
		//return;

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
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

		bDelOK = confirm('선택한 제안를 삭제하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_cancel() {
		var frm = document.frm;

		bDelOK = confirm('선택한 제안를 취소/복구하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
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


</script>
<style>
	.top_group td {border-top: 1px solid #86a4b3; border-bottom: 1px dotted #eee; font-weight:bold;}
	table.rowstable td {background: none;}
</style>
</head>

<body id="admin">

<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="gp_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="group_no" value="">
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
				
				<h2>제안서 관리</h2>
				<div class="btnright">
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록"></a>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tbody>
					<tr>
						<th>제안일자</th>
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
					</tr>
					<tr>
						<th>제안업체</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cp_type)?>" />
							<input type="hidden" name="con_cp_type" value="<?=$con_cp_type?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cp_type",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									
									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cp_type]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}
							</script>
							<!--
							<input type="text" class="supplier" style="width:160px" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cp_type)?>" placeholder="업체명/코드입력후 선택해주세요" />
							<script>
							$(function() {
						     var cache = {};
								$( ".supplier" ).autocomplete({
									source: function( request, response ) {
										var term = request.term;
										if ( term in cache ) {
											response(cache[term]);
											return;
										}
						 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent(''), request, function( data, status, xhr ) {
											cache[term] = data;
											response(data);
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										$(".supplier").val(ui.item.value);
										$("input[name=con_cp_type]").val(ui.item.id);
									}
								}).bind( "blur", function( event ) {
									var cp_no = $("input[name=con_cp_type]").val();
									if(cp_no != '') {
										$.getJSON( "../company/json_company_list.php?cp_no=" + cp_no, function(data) {
											if(data[0].CP_NO == 'undefined') {
												$("input[name=con_cp_type]").val('');
											} else {
												if(data[0].COMPANY != $(".supplier").val())
												{

													$(".supplier").val();
													$("input[name=con_cp_type]").val('');
												}
											}
										});
									} 
								});
							});
							</script>
							<input type="hidden" name="con_cp_type" value="<?=$con_cp_type?>">
							-->
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="SEND_DATE" <? if ($order_field == "SEND_DATE") echo "selected"; ?> >제안일</option>
								<option value="CP_NO" <? if ($order_field == "CP_NO") echo "selected"; ?> >제안업체</option>
								<option value="MANAGER_NM" <? if ($order_field == "MANAGER_NM") echo "selected"; ?> >담당자</option>
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
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="CP_NO" <? if ($search_field == "CP_NO") echo "selected"; ?> >제안업체</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >제품명/코드</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="20" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>

				<div style="width: 95%; text-align: right; margin: 0 0 10px 0;">
					<input type="button" name="btnCreateLink" value=" 링크 생성 " class="btntxt">&nbsp;&nbsp;&nbsp;
					<input type="button" name="btnCreateImage" value=" 이미지로 저장 " class="btntxt">&nbsp;&nbsp;&nbsp;
					<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
						<input type="button" name="aa" value=" 선택한 제안서 삭제 " class="btntxt" onclick="js_delete();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" 선택한 제안상품 취소 " class="btntxt" onclick="js_cancel();">
					<? } ?>
				</div>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="5%" />
						<col width="*" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>전표</th>
							<th colspan="3">제안업체</th>
							<th>등록일</th>
							<th class="end">발송일</th>
						</tr>
						<tr>
							<th></th>
							<th>제품명</th>
							<th>기프트넷단가</th>
							<th>제안가</th>
							<th>최종수정일</th>
							<th class="end">취소일/취소여부</th>
						</tr>
					</thead>
					<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$GP_NO						= trim($arr_rs[$j]["GP_NO"]);
							$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
							$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
							$CP_NM						= getCompanyNameWithNoCode($conn, $CP_NO);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
							$IS_SENT					= trim($arr_rs[$j]["IS_SENT"]);
							$SENT_DATE					= trim($arr_rs[$j]["SENT_DATE"]);

							if($SENT_DATE == "0000-00-00 00:00:00")
								$SENT_DATE = "";
							else
								$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

							$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));
				?>
						<tr height="30" class="top_group">
							<td>
								
								<? if($IS_SENT != "Y") { ?>
								<input type="checkbox" name="chk_no[]" value="<?=$GP_NO?>">
								<? } ?>
								
								<a href="javascript:js_view('<?=$GP_NO?>')"><?=$GROUP_NO?></a>
							</td>
							<td class="modeual_nm" colspan="3"><a href="javascript:js_view('<?=$GP_NO?>')"><?=$CP_NM?></a></td>
							<td><?= $REG_DATE?> </td>
							<td><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> </td>
						</tr>

				<?
							$arr_rs_goods = listGoodsProposalGoods($conn, $GP_NO, '');
							if (sizeof($arr_rs_goods) > 0 && sizeof($arr_rs_goods) <= 20) {
								

								for ($k = 0 ; $k < sizeof($arr_rs_goods); $k++) {

									//GPG.GPG_NO, GPG.GOODS_NO, G.GOODS_CODE, GPG.GOODS_NAME, GPG.RETAIL_PRICE, GPG.DELIVERY_CNT_IN_BOX, 
									//GPG.COMPONENT,
									//GPG.PROPOSAL_PRICE, GPG.UP_ADM, GPG.UP_DATE, GPG.CANCEL_TF, GPG.CANCEL_DATE, GPG.CANCEL_ADM
									
									$GPG_NO						= trim($arr_rs_goods[$k]["GPG_NO"]);
									$GOODS_CODE					= SetStringFromDB($arr_rs_goods[$k]["GOODS_CODE"]);
									$GOODS_NAME					= SetStringFromDB($arr_rs_goods[$k]["GOODS_NAME"]);
									$RETAIL_PRICE				= trim($arr_rs_goods[$k]["RETAIL_PRICE"]);
									$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$k]["DELIVERY_CNT_IN_BOX"]);
									$COMPONENT					= trim($arr_rs_goods[$k]["COMPONENT"]);
									$PROPOSAL_PRICE				= trim($arr_rs_goods[$k]["PROPOSAL_PRICE"]);

									$UP_DATE					= trim($arr_rs_goods[$k]["UP_DATE"]);
									$UP_ADM						= trim($arr_rs_goods[$k]["UP_ADM"]);
									
									$CANCEL_TF					= trim($arr_rs_goods[$k]["CANCEL_TF"]);
									$CANCEL_DATE				= trim($arr_rs_goods[$k]["CANCEL_DATE"]);
									$CANCEL_ADM					= trim($arr_rs_goods[$k]["CANCEL_ADM"]);

									if($UP_DATE != "0000-00-00 00:00:00")
										$UP_DATE = date("Y-m-d H:i",strtotime($UP_DATE));
									else
										$UP_DATE = "";
								
									if($CANCEL_DATE != "0000-00-00 00:00:00")
										$CANCEL_DATE = date("Y-m-d H:i",strtotime($CANCEL_DATE));
									else
										$CANCEL_DATE = "";

									if($CANCEL_TF == "Y")
										$str_cancel_style = "cancel_order";
									else
										$str_cancel_style = "";
				
				?>
						<tr height="30" class="<?=$str_cancel_style?>">
							<td>
								<input type="checkbox" name="chk_rg_no[]" value="<?=$GPG_NO?>" <?($CANCEL_TF == "Y" ? 'checked' : '')?>>
							</td>
							<td class="modeual_nm">[<?= $GOODS_CODE ?>] <?= $GOODS_NAME ?></td>
							<td><?= number_format($RETAIL_PRICE)?></td>
							<td><b><?= number_format($PROPOSAL_PRICE)?></b></td>
							<td><?=$UP_DATE?></td>
							<td>
								<? if($CANCEL_TF == "Y") {?>
									<font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">취소됨</font>
								<? } ?>
							</td>
						</tr>
				<?			
								}
							} else if (sizeof($arr_rs_goods) > 20) {
									$GOODS_NAME					= SetStringFromDB($arr_rs_goods[0]["GOODS_NAME"]);
							
							?>
								<tr>
									<td align="center" height="50" colspan="6"><?=$GOODS_NAME?> 외 <?=sizeof($arr_rs_goods)?> 개의 상품이 있습니다. </td>
								</tr>

							<?
							}
						}
					
					}  else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="6">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<input type="button" name="btnCreateLink" value=" 링크 생성 " class="btntxt">&nbsp;&nbsp;&nbsp;
					<input type="button" name="btnCreateImage" value=" 이미지로 저장 " class="btntxt">&nbsp;&nbsp;&nbsp;
					<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
						<input type="button" name="aa" value=" 선택한 제안서 삭제 " class="btntxt" onclick="js_delete();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" 선택한 제안상품 취소 " class="btntxt" onclick="js_cancel();">
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

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<!-- // E: mwidthwrap -->



		</td>
	</tr>
	</table>

	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="100%" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>