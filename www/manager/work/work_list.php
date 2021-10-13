<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

	$menu_right = "WO003"; // 메뉴마다 셋팅 해 주어야 합니다


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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/stock/stock.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($work_date == "") {
		$work_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$work_date = trim($work_date);
	}


	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listWorkList($conn, $work_date, $search_field, $search_str);

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

	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}

	function js_sub_goods_popup(start_work_date, end_work_date) {
		
		var url = "popup_work_goods.php?start_work_date=" + start_work_date + "&end_work_date=" + end_work_date;

		NewWindow(url,'popup_work_goods','820','700','YES');

	}

	function js_outcase(goods_no) {
		var url = "popup_outcase.php?goods_no="+goods_no;
		NewWindow(url,'popup_outcase','1024','400','YES');
	}

	function js_opt_memo_view(order_goods_no) {
		var url = "popup_opt_memo.php?order_goods_no="+order_goods_no;
		NewWindow(url,'popup_opt_memo','820','700','YES');
	}

	function js_order_popup(order_goods_no) {

		var url = "popup_work.php?work_date=<?=$work_date?>&order_goods_no="+order_goods_no;

		NewWindow(url,'popup_work','1600','1200','YES');

	}

	$(function(){
		$("input[name=work_msg]").on('keydown',function(){

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ //Enter

				var order_goods_no = $(this).data("order_goods_no");
				var work_msg = $(this).val();

				(function() {
				  $.getJSON( "/manager/order/json_update_order_goods.php", {
					mode: "UPDATE_ORDER_GOODS_WORK_MSG",
					order_goods_no: order_goods_no,
					work_msg: work_msg
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('연결오류 : 잠시후 다시 시도해주세요');

					  });
					});
				})();
			}
		});
	});

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_170_170_","");
			
			img_frame.show().append($("<img src='"+origin_img+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});
</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="order_goods_no" value="">
<input type="hidden" name="selected_qty" value="">

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
				<h2>작업 리스트 (<?=$work_date?>)</h2>


				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th>작업일</th>
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="work_date" value="<?=$work_date?>" maxlength="10"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
					</tr>
				</thead>
				</table>
				<div class="btnright02">
					
				</div>
				<!--
				<input type="button" name="aa" value=" 금일 작업 자재 조회 " class="btntxt" onclick="js_sub_goods_popup('', '<?=$work_date?>');">&nbsp;&nbsp;&nbsp;
				-->
	
				<b>총 <span class="total_qty"></span> 건 </b>
				<!--*  작업일이 오늘 날짜인 경우와 오늘 이전 날짜 중 작업 완료 아닌 건을 노출-->
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">

					<colgroup>
						<col width="3%" />
						<col width="3%" />
						<col width="17%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="*" />
						<col width="5%" />
						<col width="6%"/>
						<col width="22%" />
					</colgroup>
					<thead>
						<tr>
							<th></th>
							<th>순번</th>
							<th>상품이미지</th>
							<th>주문업체</th>
							<th>주문자</th>
							<th>수령자</th>
							<th>상품명</th>
							<th>주문수량</th>
							<th>영업담당</th>
							<th class="end">작업상태</th>
						</tr>

					</thead>

					<tbody>
					<?
						$nCnt = 0;
						$cancelQty = 0;

						$work_date_total_order_qty = 0;
						$work_date_total_work_qty = 0;

						$incase_work_total = 0;
						$wrap_work_total = 0;
						$sticker_work_total = 0;
						$outbox_sticker_work_total = 0;
						$sticker_msg_work_total = 0;

						$incase_work_goods_total = 0;
						$wrap_work_goods_total = 0;
						$sticker_work_goods_total = 0;
						$outbox_sticker_work_goods_total = 0;
						$sticker_msg_work_goods_total = 0;
						
						$is_alternative_color = false;

						if (sizeof($arr_rs) > 0) {
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
								$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
								$ORDER_DATE					= trim($arr_rs[$j]["ORDER_DATE"]);
								$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
								$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
								$O_MEM_NM					= trim($arr_rs[$j]["O_MEM_NM"]);
								$R_MEM_NM					= trim($arr_rs[$j]["R_MEM_NM"]);
								$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
								$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
								$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
								$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
								$OPT_MANAGER_NO				= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
								$OPT_MEMO					= trim($arr_rs[$j]["OPT_MEMO"]);
								$WORK_START_DATE 			= trim($arr_rs[$j]["WORK_START_DATE"]);
								$BULK_TF					= trim($arr_rs[$j]["BULK_TF"]);
								$refund_able_qty			= trim($arr_rs[$j]["QTY"]);
								$WORK_REQ_QTY				= trim($arr_rs[$j]["WORK_REQ_QTY"]);


								$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
								$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
								$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
								$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
								$OPT_STICKER_MSG		    = trim($arr_rs[$j]["OPT_STICKER_MSG"]);
								$WORK_LINE					= trim($arr_rs[$j]["WORK_LINE"]);
								$WORK_QTY					= trim($arr_rs[$j]["WORK_QTY"]);
								$WORK_SEQ					= trim($arr_rs[$j]["WORK_SEQ"]);
								$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
								$WORK_MSG					= trim($arr_rs[$j]["WORK_MSG"]);
								$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
								$DELIVERY_CP			 	= trim($arr_rs[$j]["DELIVERY_CP"]);
			
								
								$refund_able_qty = $refund_able_qty - $WORK_QTY;

								if($WORK_REQ_QTY > 0 && $WORK_REQ_QTY <= $refund_able_qty)
									$refund_able_qty = $WORK_REQ_QTY;
								
								//$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);

								$work_date_total_order_qty += $refund_able_qty;
							
								/*
								//전체취소건은 제외
								if($refund_able_qty == 0) { 
									$cancelQty ++; 
									continue;
								}
								*/

								$work_date_total_work_qty += $WORK_QTY;

								if($CATE_01 <> "")
									$GOODS_NAME = $CATE_01.")".$GOODS_NAME;

								if ($CATE_04 == "CHANGE") {
									$str_cate_04 = "<font color='red'>(교환건)</font>";
								} else {
									$str_cate_04 = "";
								}

								$GOODS_IMG = getImage($conn, $GOODS_NO, "170", "170");

								// 구성품 정보 가지고 오기 
								$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);

								$left_qty = 0;
								
								//echo $refund_able_qty."<br/>";
							
								//if($WORK_QTY <> 0)
								//	$left_qty = $refund_able_qty - $WORK_QTY;
								//else
									$left_qty = $refund_able_qty;

								

							?>
							<tr height="25" class="<?="tr_".$ORDER_GOODS_NO?> order_goods tr_title" <? if ($is_alternative_color) {?> style="background:#EFEFEF" <? } ?> >
								<td rowspan="2" colspan="2">
									<b><?=$WORK_SEQ?></b> 
									<br/><br/>
									<span title="작업지시일"><?= date("n월j일",strtotime($WORK_START_DATE))?><br><?=$ORDER_GOODS_NO?></span>
								</td> 
								<td rowspan="2" style="padding: 3px 3px 3px 3px">
									<a href="javascript:js_order_popup('<?=$ORDER_GOODS_NO?>');">
									<img src="<?=$GOODS_IMG?>" width="170" height="170"  data-thumbnail="<?=$GOODS_IMG?>" class="goods_thumb">
									<br>
									<div style="width:100%;text-align:left; padding:10px 5px 5px 10px">
									<?
										if (sizeof($arr_goods_sub) > 0) {
											for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
												$sub_goods_name			= trim($arr_goods_sub[$jk]["GOODS_NAME"]);
												$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
												echo $sub_goods_name."&nbsp;&nbsp;<font color='red'>(<b>".$sub_goods_cnt."</b>)</font><br>";
											}
										}
									?>
									</div>
									</a>
								</td>
								</td>
								<td style="text-align:center"><?= getCompanyName($conn, $CP_NO) ?></td>
								<td class="modeual_nm"><?= $O_MEM_NM?></td>
								<td class="modeual_nm"><?= $R_MEM_NM?></td>
								<td class="modeual_nm"><?=$str_cate_04?><a href="javascript:js_view('<?=$RESERVE_NO?>');"><?=$GOODS_NAME?></a></td>
								<td class="price"><b><?=number_format($left_qty)?></b></td>
								<td style="text-align:center"><?=getAdminName($conn,$OPT_MANAGER_NO);?></td>
								<td style="text-align:right;padding-right:10px;padding-top:5px;padding-bottom:5px; font-weight:bold;">
									<?= getDcodeName($conn, "DELIVERY_TYPE", $DELIVERY_TYPE) ?> 
									<? 
										$delivery_cp = getDcodeName($conn, "DELIVERY_CP", $DELIVERY_CP);
									    if(($DELIVERY_TYPE == "0" || $DELIVERY_TYPE == "3" || $DELIVERY_TYPE == "98") && $delivery_cp != "")
										echo "(".$delivery_cp.")";
									?>
								</td>
							</tr>
							<tr class="<?="tr_".$ORDER_GOODS_NO?> order_goods tr_body" <? if ($is_alternative_color) {?> style="background:#EFEFEF" <? } ?>>
								<td colspan="7" style="text-align:center; vertical-align:middle;">
									<?
										//취소나 오류로 인해 주문수량보다 작업수량이 많은경우
										if($left_qty > 0) { 
									?> 
									<table style="width:100%" cellpadding="0" cellspacing="0" border="0">
										<tr height="25">
											<td width="15%"><b>인박스</b></td>
											<td width="15%"><b>포장지</b></td>
											<td width="15%"><b>스티커</b></td>
											<td width="15%"><b>아웃박스</b></td>
											<td width="15%"><b>아웃박스스티커</b></td>
											<td width="15%"><b>스티커/인쇄 메세지</b></td>
											<td width="10%"></td>
										</tr>
										<tr>
											<?
												//초기화
												$case_name              = "";
												$case_img				= "";
												$incase_wrap_width      = "";
												$incase_wrap_length		= "";
												$incase_wrap_memo       = "";
												$wrap_img				= "";
												$wrap_name              = "";
												$sticker_img			= "";
												$sticker_name           = "";
												$out_img				= "";

											?>
											<td style="padding: 3px 3px 3px 3px">
												<?
													$has_case_work = checkOrderGoodsIncase($conn, $GOODS_NO);
													if($has_case_work) { 
														
														$incase_work_total ++;
														$incase_work_goods_total += $left_qty;

														$arr_incase = getOrderGoodsSub($conn, $GOODS_NO, "INCASE");
														$case_name			= $arr_incase[0]["GOODS_NAME"];
														$case_img			= getImage($conn, $arr_incase[0]["GOODS_NO"], "", "");
														$incase_wrap_width  = $arr_incase[0]["WRAP_WIDTH"];
														$incase_wrap_length = $arr_incase[0]["WRAP_LENGTH"];
														$incase_wrap_memo	= $arr_incase[0]["WRAP_MEMO"];
											
												?>
												<img src="<?=$case_img?>" style="max-width:80px; max-height:80px;"><br><br>
												<b><?=$case_name?></b>
												<? 
													if($incase_wrap_width <> "" || $incase_wrap_length <> "") { 
														echo "<br/>(".$incase_wrap_width." x ".$incase_wrap_length.")";
													}
													if($incase_wrap_memo <> "") { 
														echo "<br/>".$incase_wrap_memo;
													}
												
												?>
												<? } else { ?>
												<font color="#AFAFAF">없음</font>
												<? } ?>
											</td>
											<td style="padding: 3px 3px 3px 3px">
												<? 
													if ($OPT_WRAP_NO > 0) {
														
														$wrap_work_total ++;
														$wrap_work_goods_total += $left_qty;

														$wrap_img		= getImage($conn, $OPT_WRAP_NO, "", "");
														$wrap_name      = getGoodsName($conn, $OPT_WRAP_NO);
														$arr_wrap_info  = getGoodsWrapInfo($conn, $GOODS_NO);
														if(sizeof($arr_wrap_info) > 0) { 
															$wrap_width = $arr_wrap_info[0]["WRAP_WIDTH"];
															$wrap_length = $arr_wrap_info[0]["WRAP_LENGTH"];
															$wrap_memo = $arr_wrap_info[0]["WRAP_MEMO"];
														} else { 
															$wrap_width = "";
															$wrap_length = "";
															$wrap_memo = "";
														}
												
												?>
												<img src="<?=$wrap_img?>" style="max-width:80px; max-height:80px;"><br/>
												<font><?=$wrap_name?></font>
												<? 
													if($wrap_width <> "" || $wrap_length <> "") { 
														echo "<br/>(".$wrap_width." x ".$wrap_length.")";
													}
													if($wrap_memo <> "") { 
														echo "<br/>".$wrap_memo;
													}
												
												?>
												<? } else { ?>
												<font color="#AFAFAF">없음</font>
												<? } ?>
											</td>
											<td style="padding: 3px 3px 3px 3px">
												<? 
													if ($OPT_STICKER_NO > 0) { 

														$sticker_work_total ++;
														$sticker_work_goods_total += $left_qty;

														$sticker_img		= getImage($conn, $OPT_STICKER_NO, "", "");
														$sticker_name		= getGoodsName($conn, $OPT_STICKER_NO);
												?>
												<img src="<?=$sticker_img?>" style="max-width:80px; max-height:80px;"><br/>
												<font><?=$sticker_name?></font>
												<? } else { ?>
												<font color="#AFAFAF">없음</font>
												<? } ?>
											</td>

											<td style="padding: 3px 3px 3px 3px">
												<? 
													//$arr_outcase = getOrderGoodsSub($conn, $GOODS_NO, "OUTCASE");
													$out_img		= ""; //getImage($conn, $arr_outcase[0]["GOODS_NO"], "", "");
												?>
												<?	if ($out_img) {?>
												<img src="<?=$out_img?>" style="max-width:80px; max-height:80px;"><br><br>
												<a href="javascript:js_outcase('<?=$GOODS_NO?>');">상세보기</a>
												<?	} else { ?>
												<font color="#AFAFAF">이미지 미등록</font>
												<?	} ?>
												<br/>
												<font style="color:red; font-weight:bold;">박스입수:<?=$DELIVERY_CNT_IN_BOX?>개</font>
											</td>

											<td style="padding: 3px 3px 3px 3px">
												<? 
													if ($OPT_OUTBOX_TF  == "Y") { 
														$outbox_sticker_work_total ++;
														$outbox_sticker_work_goods_total += $left_qty;
												?>
												<b><font color="navy">있음</font></b>
												<? } else { ?>
												<font color="#AFAFAF">없음</font>
												<? } ?>
											</td>
											<td style="text-align:left;padding: 3px 3px 3px 3px; color:blue;">
												<?
													if($OPT_STICKER_MSG != "" || $OPT_PRINT_MSG != "") { 
														$sticker_msg_work_total ++;
														$sticker_msg_work_goods_total += $left_qty;
													}
												?>
												<?=($OPT_STICKER_MSG != "" ? " <b>스티커</b> : ".$OPT_STICKER_MSG : "")?><br/><?=($OPT_PRINT_MSG != "" ? " <b>인쇄</b> : ".$OPT_PRINT_MSG : "")?>
											</td>
											<td>
												
											</td>
										</tr>
										<tr height="35">
											<td>
												
											</td>
											<td>
												
											</td>
											<td>
												
											</td>
											<td>
												
											</td>
											<td>
												
											</td>

											<td colspan="2">
												<select name="arr_done_qty[]" data-order_goods_no="<?=$ORDER_GOODS_NO?>">
													<?	for ($c = $left_qty ; $c >= 1 ; $c--) { ?>
													<option value="<?=$c?>"><?=$c?></option>
													<? } ?>
												</select>
												<? if (($sPageRight_I == "Y") && ($sPageRight_U == "Y") && ($sPageRight_D == "Y")) { ?>
												<input type="button" class="btn_work_done" name="bb" data-order_goods_no="<?=$ORDER_GOODS_NO?>" value="출고대기" style="display:none;"/>
												<input type="button" class="btn_work_sent" name="bb" data-order_goods_no="<?=$ORDER_GOODS_NO?>" value="즉시출고" style="display:none;"/>
												<? } ?>
												
											</td>
										</tr>
										<tr height="25px">
											<td class="text_align_center"><b>작업메모</b></td>
											<td colspan="6" class="modeual_nm" style="padding:5px 2px 2px 5px; "><a href="javascript:js_opt_memo_view('<?=$ORDER_GOODS_NO?>');" style="color:red;"><?=$OPT_MEMO?></a></td>
										</tr>
										<tr>
											<th class="text_align_center">창고메세지</th>
											<td colspan="6"><input type="text" name="work_msg" style="width:90%; height:25px;" data-order_goods_no="<?=$ORDER_GOODS_NO?>" value="<?=$WORK_MSG?>" placeholder="전달하실 메세지 입력 후 엔터 눌러주세요"/></td>
										</tr>
									</table>
									<? } else { ?>
										<font style="color:red; font-size:16px; line-height:20px;">주문 수량 변경으로 인해 주문수량보다 작업된 수량이 많거나 같아서 더 작업할 수량이 없습니다. <br/><br/>작업수량을 재 확인해주시고 주문수량만큼 작업이 되어있다면 배송처리하세요.</font>
									<? } ?>
								</td>
							</tr>
						<?

									$is_alternative_color = !$is_alternative_color;
									
								} 
		
							}else{
						?>

							<tr class="order">
								<td height="50" align="center" colspan="12">데이터가 없습니다. </td>
							</tr>
						<?
							}
						?>
						<script>
						$(function(){
							$(".total_qty").html('<?=$j - $cancelQty?>');
							$(".btn_work_done").show();
							$(".btn_work_sent").show();
						});
						</script>
					</tbody>
				</table>
				<!--
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<input type="button" name="aa" value=" 금일 작업 자재 조회 " class="btntxt" onclick="js_sub_goods_popup('', '<?=$work_date?>');">&nbsp;&nbsp;&nbsp;
				</div>
				-->
				<br/>
				<b>총 작업 예정 수량 <?=number_format($work_date_total_order_qty)?> 건, 작업된 수량 <?=number_format($work_date_total_work_qty)?> 건</b><br/><br/>
				<span>인케이스 작업수 : <?=$incase_work_total?> 곳, <?=$incase_work_goods_total?> 개<br/> 
				      포장지 작업수 : <?=$wrap_work_total?> 곳, <?=$wrap_work_goods_total?> 개<br/> 
					  스티커 작업수 : <?=$sticker_work_total?> 곳, <?=$sticker_work_goods_total?> 개<br/> 
					  아웃박스 스티커 작업수 : <?=$outbox_sticker_work_total?> 곳, <?=$outbox_sticker_work_goods_total?> 개<br/> 
					  스티커/인쇄메세지 작업수 : <?=$sticker_msg_work_total?> 곳, <?=$sticker_msg_work_goods_total?> 개</span>
				

				<script>
					$(function(){
						$(".btn_work_done").click(function(e){

							e.preventDefault();

							$(this).hide();
							
							var order_goods_no = $(this).data("order_goods_no");
							var selected_qty = $("select[name='arr_done_qty[]'][data-order_goods_no="+order_goods_no+"]").val();

							(function() {
							  $.getJSON( "/manager/work/json_work_list.php", {
								mode: "WORK_DONE",
								order_goods_no: order_goods_no,
								selected_qty: selected_qty,
								work_done_adm : <?=$s_adm_no?>
							  })
								.done(function( data ) {
								  $.each( data, function( i, item ) {
									  if(item.RESULT == "0")
										  alert('연결오류 : 잠시후 다시 시도해주세요');
									  else { 
										$(".tr_" + item.RESULT).fadeOut( "slow" );
									  }
								  });
								});
							})();
						});

						$(".btn_work_sent").click(function(e){

							e.preventDefault();

							$(this).hide();
							
							var order_goods_no = $(this).data("order_goods_no");
							var selected_qty = $("select[name='arr_done_qty[]'][data-order_goods_no="+order_goods_no+"]").val();

							(function() {
							  $.getJSON( "/manager/work/json_work_list.php", {
								mode: "WORK_SENT",
								order_goods_no: order_goods_no,
								selected_qty: selected_qty,
								work_done_adm : <?=$s_adm_no?>
							  })
								.done(function( data ) {
								  $.each( data, function( i, item ) {
									  if(item.RESULT == "0")
										  alert('연결오류 : 잠시후 다시 시도해주세요');
									  else { 
										$(".tr_" + item.RESULT).fadeOut( "slow" );
									  }
								  });
								});
							})();
						});
					});
				</script>

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