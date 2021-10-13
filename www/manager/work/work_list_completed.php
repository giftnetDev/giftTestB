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
	
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급" ) { 
		$cp_type2 = $s_adm_com_code;
	}

	if ($s_adm_cp_type == "판매") { 
		$cp_type = $s_adm_com_code;
	}

	if($mode == "UNDO") { 

		//주문 작업 및 작업 수량 기록 취소
		updateOrderGoodsWorkUndo($conn, $work_done_no, $s_adm_no);

		//작업완료시 출고된 재고 삭제
		deleteStockByWorkDoneNo($conn, $work_done_no, $s_adm_no);

		undoOrderStateFromComplete($conn, $order_goods_no);
		resetOrderInfor($conn, $reserve_no);

		//출고대기 상품 삭제 - 삭제될경우 그에 따른 입출기록 지워질수 있음
		//deleteStockEachByWorkDoneNo($conn, $work_done_no, $s_adm_no);


	}
	
#====================================================================
# Request Parameter
#====================================================================

	if ($work_date == "") {
		$work_date = date("Y-m-d",strtotime("0 day"));
	} else {
		$work_date = trim($work_date);
	}


	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
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
		$nPageSize = 2000;
	}

	$nPageBlock	= 10;
	
#	echo $start_date;
#	echo $end_date;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntWorkListCompleted($conn, $work_date, "Y", "N", $search_field, $search_str);
	#echo $nListCnt;

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listWorkListCompleted($conn, $work_date, "Y", "N", $search_field, $search_str, $nPage, $nPageSize);

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

	// 조회 버튼 클릭 시 
	function js_view_log(idx){
		// alert('open');
		$('#dvLog_'+idx).show();
	}
	function js_hide_log(idx){
		// alert('close');
		$('#dvLog_'+idx).hide();
	}
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}
	
	function js_undo_order_work_completed(work_done_no, order_goods_no, reserve_no) {
		
		var frm = document.frm;
		frm.work_done_no.value = work_done_no;
		frm.order_goods_no.value = order_goods_no;
		frm.reserve_no.value = reserve_no;
		frm.mode.value = "UNDO";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	
	}

	function js_sub_goods_popup_completed() {

		var frm = document.frm;
		
		var url = "popup_work_goods_completed.php?work_date=" + frm.work_date.value;

		NewWindow(url,'popup_work_goods_completed','820','700','YES');

	}

	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
	}

</script>
<style>
	.alternative_color {background:#EFEFEF;} 
	.dvTooltip table{

	}
</style>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="reserve_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="work_done_no" value="">
<input type="hidden" name="order_goods_no" value="">
<input type="hidden" name="nPage" value="">

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
				<h2>작업 완료 리스트</h2>


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
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="work_date" value="<?=$work_date?>" maxlength="10"/>
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td colspan="2" align="right">
							<input type="button" name="b" value=" 완료 전체 수량 보기 " onclick="js_sub_goods_popup_completed();" />
						</td>
					</tr>
				</thead>
				</table>
				<div class="sp10"></div>
				<b><span class="total_qty"></span> </b>
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

						$work_date_total_order_qty = 0;
						$work_date_total_work_qty = 0;
						$cancelQty = 0;
						
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
								$WORK_ORDER					= trim($arr_rs[$j]["WORK_ORDER"]);
								$WORK_START_DATE 			= trim($arr_rs[$j]["WORK_START_DATE"]);
								$BULK_TF					= trim($arr_rs[$j]["BULK_TF"]);
								
								$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);

								$work_date_total_order_qty += $refund_able_qty;
							
								//전체취소건은 색 입력
								if($refund_able_qty == 0) { 
									$cancelQty ++;
									$row_str = "cancel_goods"; 
								} else {
									if ($is_alternative_color)
										$row_str = "alternative_color";
									else
										$row_str = "";
								}

								$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
								$OPT_OUTBOX_TF				 = trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
								$OPT_WRAP_NO				   = trim($arr_rs[$j]["OPT_WRAP_NO"]);
								$OPT_PRINT_MSG				 = trim($arr_rs[$j]["OPT_PRINT_MSG"]);
								$OPT_STICKER_MSG		   = trim($arr_rs[$j]["OPT_STICKER_MSG"]);
								$WORK_LINE						 = trim($arr_rs[$j]["WORK_LINE"]);
								$WORK_QTY					 	  = trim($arr_rs[$j]["WORK_QTY"]);
								$WORK_SEQ					 	  = trim($arr_rs[$j]["WORK_SEQ"]);
								$DELIVERY_CNT_IN_BOX   = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
								$WORK_MSG						  = trim($arr_rs[$j]["WORK_MSG"]);
								$DELIVERY_TYPE				 = trim($arr_rs[$j]["DELIVERY_TYPE"]);
								$DELIVERY_CP			 	   = trim($arr_rs[$j]["DELIVERY_CP"]);
								
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

								if($work_date <> date("Y-m-d",strtotime($WORK_START_DATE)))
									$str_cls_diff_date = "style='color:red;'";
								else
									$str_cls_diff_date = "";
							?>
							<tr height="25" class="<?="tr_".$ORDER_GOODS_NO?> <?=$row_str?> order_goods" >
								<td rowspan="2" colspan="2">
									<b onmouseover="js_view_log('<?=$j?>')" onmouseout="js_hide_log('<?=$j?>')" style="position:relative;"><?=$WORK_SEQ?>
									<?
										$log=listOrderWorkLog($conn, $ORDER_GOODS_NO);
										$cntLog=sizeof($log);
									?>
										<div class="dvTooltip" id="dvLog_<?=$j?>" style="z-index:300; display: none; position:absolute; top:10px; left:10px; background:#FFFFFF;">
										<?
	
											if($cntLog>1){
											?>
												<table style="width:50px;">
													<tr>
														<td>이전순번</td>
													</tr>
											<?
												for($l=0; $l<$cntLog-1; $l++){
												?>
													<tr>
														<td><?=$log[$l]["WORK_SEQ"]?></td>														
													</tr>
												<?
												}//end of for($cntLog)
											?>
												</table>	
											<?
											}//end of if($cntLog>0)
										?>
										</div><!--dvTooltip-->
									
									</b> 
									
									<br/><br/>
									<span title="작업지시일" <?=$str_cls_diff_date?>><?= date("n월j일",strtotime($WORK_START_DATE))?></span>
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
								<td class="price"><b><?=number_format($refund_able_qty)?></b> / <?=$WORK_QTY?></td>
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
							<tr class="<?=$row_str?>">
								
							<?
									$left_qty = 0;
							
									if($WORK_QTY <> 0)
										$left_qty = $refund_able_qty - $WORK_QTY;
									else
										$left_qty = $refund_able_qty;

									

							?>
								<td colspan="3" style="text-align:left;">
									<?
										$rs_order_goods = selectOrderGoods($conn, $ORDER_GOODS_NO);
										$rs_goods_no			= trim($rs_order_goods[0]["GOODS_NO"]);
										$rs_opt_wrap_no			= trim($rs_order_goods[0]["OPT_WRAP_NO"]);
										$rs_opt_sticker_no		= trim($rs_order_goods[0]["OPT_STICKER_NO"]);
										$rs_opt_sticker_ready	= trim($rs_order_goods[0]["OPT_STICKER_READY"]);
										$rs_opt_outbox_tf		= trim($rs_order_goods[0]["OPT_OUTBOX_TF"]);
										$rs_opt_sticker_msg		= trim($rs_order_goods[0]["OPT_STICKER_MSG"]);
										$rs_opt_print_msg		= trim($rs_order_goods[0]["OPT_PRINT_MSG"]);

										$option_str	= "";
										
										$option_str .= ($rs_opt_sticker_no <> "0" ? "<b>스티커</b> : ".getGoodsName($conn, $rs_opt_sticker_no)." <br/>" : "");
										$option_str .= ($rs_opt_outbox_tf == "Y" ? "<b>아웃박스스티커</b> : 있음 <br/>" : "" );
										$option_str .= ($rs_opt_wrap_no <> "0" ? "<b>포장지</b> : ".getGoodsName($conn, $rs_opt_wrap_no). " <br/>" : "");
										$option_str .= ($rs_opt_sticker_msg <> "" ? "<b>스티커메세지</b> : ".$rs_opt_sticker_msg. " <br/>" : "");
										$option_str .= ($rs_opt_print_msg <> "" ? "<b>인쇄메세지</b> : ".$rs_opt_print_msg. " <br/>" : "");

										echo $option_str;

									//}
									
								?>

								<?= ($OPT_MEMO <> "" ? "<font color='red'><b>작업메모</b> : ".$OPT_MEMO."</font>" : "") ?>
									
								</td>
								<td colspan="4">
									<table cellpadding="0" cellspacing="0" class="colstable">
									<colgroup>
										<col width="*" />
										<col width="30%" />
										<col width="17%" />
										<col width="10%" />
										<col width="15%" />
									</colgroup>
										<thead>
											<th>완료일</th>
											<th>완료자</th>
											<th>출고구분</th>
											<th>수량</th>
											<th class="end">되돌리기</th>
										</thead>
										<tbody>
									
									<?
										$arr_sww = selectOrderWorkHistory($conn, $ORDER_GOODS_NO);
										if(sizeof($arr_sww) > 0) { 
											for ($k = 0 ; $k < sizeof($arr_sww); $k++) {
												$rs_work_done_no	= trim($arr_sww[$k]["WORK_DONE_NO"]);
												$rs_work_type		= trim($arr_sww[$k]["WORK_TYPE"]);
												$rs_sub_qty			= trim($arr_sww[$k]["QTY"]);
												$rs_reg_date		= trim($arr_sww[$k]["REG_DATE"]);
												$rs_reg_adm			= trim($arr_sww[$k]["REG_ADM"]);

												$rs_reg_date = date("Y-m-d H:i",strtotime($rs_reg_date));
												$rs_reg_adm = getAdminName($conn, $rs_reg_adm);

												switch($rs_work_type) { 
													case "WORK_DONE" : $rs_work_type = "출고대기"; break;
													case "WORK_SENT" : $rs_work_type = "즉시출고"; break;
												}
									?>
										<tr>
											<td><?=$rs_reg_date?></td>
											<td><?=$rs_reg_adm?></td>
											<td><?=$rs_work_type?></td>
											<td><?=$rs_sub_qty?></td>
											<td><input type="button" name="b" value="출고취소" onclick="javascript:js_undo_order_work_completed('<?=$rs_work_done_no?>', '<?=$ORDER_GOODS_NO?>', '<?=$RESERVE_NO?>');"/></td>
										</tr>
									<?
											}
										} else {
									?>
										<tr><td colspan="5">취소 가능한 이전 완료 기록이 없습니다.</td></tr>
									<?
										}
									?>
										</tbody>
									</table>
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
					</tbody>
				</table>
				<div class="sp10"></div>
				<script>
					$(function(){
						$(".total_qty").html('전체 <?=$nListCnt?> 건 중, 취소 <?=$cancelQty?>건');
					});
				</script>
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