<?
	require "_common/home_pre_setting.php";

	require "_classes/biz/order/order.php";

	if ($_SESSION['C_MEM_NO'] == "") {

?>
<script type="text/javascript">
	alert('로그인 되어있지 않거나 세션이 만료 되었습니다. 재 로그인 해주세요.');
</script>
<meta http-equiv='Refresh' content='0; URL=/'>
<?
			exit;
	}
	
	$search_date_type = "order_date";

	if($start_date == "")
		$start_date = date("Y-m-d", strtotime("-1 month"));
	if($end_date == "")
		$end_date = date("Y-m-d", strtotime("0 day"));

	$cp_type2 = $_SESSION['C_CP_NO'];
	$nPage = 1;
	$nPageSize = 100;
	$search_field = "ALL";
	$del_tf = 'N';

	$arr_rs = listManagerDelivery($conn, $search_date_type, $start_date, $end_date, $bulk_tf, $sel_order_state, $cp_type, $cp_type2, $sel_cate_01, $sel_sale_confirm_tf, $con_work_flag, $sel_opt_manager_no, $sel_delivery_type, $sel_delivery_cp, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
	
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" type="text/css" />
</head>
<body>
<?
	require "_common/v2_top.php";
?>
<!-- 주문/배송조회 -->
<div class="container orders">
    <h5 class="title">주문/배송조회</h5>
    <div class="contents">
		<input type="hidden" name="mode" value="">
		<form method="get">
			<style>
				ul.order_state{list-style:none; text-align:center; overflow:hidden; width:600px; margin:10px auto; padding:0;}
				ul.order_state > li {float:left; margin-right:15px; padding-left: 25px; background: url(/img/common/bg_arrow03.png) no-repeat 0 40px;}
				ul.order_state > li:first-child{background: none;}
				ul.order_state > li > img {width:100px;}
				ul.order_state > li > div {font-family: 'NanumGothic', '나눔고딕', sans-serif; line-height:25px;}
				ul.order_state > li > div > span {background: url(/img/common/bg_pink_num.png) no-repeat 3px 0px; width: 28px; height: 25px; display: inline-block; color: #FFF; padding-left: 5px;} 
			</style>
			<div class="container-fluid">
				<?
					/*
						//성능문제로 수정
					$cnt_1 = cntOrderGoodsStateByMember($conn, '1', '', $start_date, $end_date, $cp_type2); // 주문확인
					$cnt_2 = cntOrderGoodsStateByMember($conn, '2', 'N', $start_date, $end_date, $cp_type2); // 작업중
					$cnt_3 = cntOrderGoodsStateByMember($conn, '2', 'Y', $start_date, $end_date, $cp_type2); // 배송중
					$cnt_4 = cntOrderGoodsStateByMember($conn, '3', '', $start_date, $end_date, $cp_type2); // 배송완료
					*/
				?>
				<ul class="order_state">
					<li>
						<img src="/img/order_state1.jpg" alt="주문접수"/>
						<div>주문접수<span id="cnt_1">0</span></div>
					</li>
					<!-- <li>
						<img src="/img/order_state1.jpg" alt="주문접수"/>
						<div>주문완료<span id="cnt_1">0</span></div>
					</li> -->
					<li>
						<img src="/img/order_state2.jpg" alt="배송준비중"/>
						<div>작업중<span id="cnt_2">0</span></div>
					</li>
					<li>
						<img src="/img/order_state3.gif" alt="배송중"/>
						<div>배송중<span id="cnt_3">0</span></div>
					</li>
					<li>
						<img src="/img/order_state4.jpg" alt="배송완료"/>
						<div>배송완료<span id="cnt_4">0</span></div>
					</li>
				</ul>
			</div>
			<nav class="navbar navbar-default">
				
				<div class="container-fluid">
					
					<div class="navbar-header">
					  <span class="navbar-brand">조회기간</span>
					</div>
					<div class="collapse navbar-collapse navbar-left" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">기간선택 <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="#" class="date" data-date_span="7">1주일</a></li>
									<li><a href="#" class="date" data-date_span="30">1개월</a></li>
									<li><a href="#" class="date" data-date_span="90">3개월</a></li>
								</ul>
							</li>
						</ul>
					  
					</div><!-- /.navbar-collapse -->
					<div class="navbar-form navbar-left">
						<div class="form-group input-group">
							<input type="text" id="datepicker-start" class="form-control datepicker" name="start_date" placeholder="시작일 (YYYY-MM-DD)" value="<?=$start_date?>" />    
							<label class="input-group-addon" for="datepicker-start">
								<span id="btn-datepicker-start" class="glyphicon glyphicon-calendar"></span>
							</label>
						</div>
						  ~
						<div class="form-group input-group">
							<input type="text" id="datepicker-end" class="form-control datepicker" name="end_date" placeholder="종료일 (YYYY-MM-DD)" value="<?=$end_date?>"/>    
							<label class="input-group-addon" for="datepicker-end">
								<span id="btn-datepicker-end" class="glyphicon glyphicon-calendar"></span>
							</label>						
						</div>
						
					</div>
				</div><!-- /.container-fluid -->
				
				
				<div class="container-fluid">
					<div class="navbar-header">
						<span class="navbar-brand">주문상태</span>
					</div>
					<div class="collapse navbar-collapse navbar-left" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									<span id="sel_order_state">
									<? 
										switch($sel_order_state) {
											case "1" : echo "주문접수중"; break;
											case "2" : echo "작업/배송중"; break;
											case "3" : echo "배송완료"; break;
											default : echo "전체";
										}
									?>
									</span> 
								<span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="#" class="li_order_state" data-order_state="">전체</a></li>
									<li><a href="#" class="li_order_state" data-order_state="1">주문접수중</a></li>
									<li><a href="#" class="li_order_state" data-order_state="2">작업/배송중</a></li>
									<li><a href="#" class="li_order_state" data-order_state="3">배송완료</a></li>
								</ul>
								<input type="hidden" name="sel_order_state" value="<?=$sel_order_state?>"/>
							</li>
						</ul>
					  
					</div><!-- /.navbar-collapse -->
					<div class="navbar-form navbar-left">
						<div class="form-group">
						  <input type="text" class="form-control" name="search_str" placeholder="검색어 입력" value="<?=$search_str?>">
						</div>
						<button type="submit" class="btn btn-default">조회하기</button>
					</div>
				</div>
			</nav>
		</form>
			<?
				$nCnt = 0;
				
				if (sizeof($arr_rs) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						
						$RESERVE_NO							= trim($arr_rs[$j]["RESERVE_NO"]);
						$MEM_TYPE							= trim($arr_rs[$j]["MEM_TYPE"]);
						$MEM_NO								= trim($arr_rs[$j]["MEM_NO"]);
						$CP_NO								= trim($arr_rs[$j]["CP_NO"]);
						
						$PAY_TYPE							= trim($arr_rs[$j]["PAY_TYPE"]);
						$ORDER_CONFIRM_DATE					= trim($arr_rs[$j]["ORDER_CONFIRM_DATE"]);
						
						//$ORDER_STATE						= trim($arr_rs[$j]["ORDER_STATE"]);
						$PAY_STATE							= trim($arr_rs[$j]["PAY_STATE"]);
						$O_MEM_NM							= trim($arr_rs[$j]["O_MEM_NM"]);
						$O_PHONE							= trim($arr_rs[$j]["O_PHONE"]);
						$O_HPHONE							= trim($arr_rs[$j]["O_HPHONE"]);
						$R_MEM_NM							= trim($arr_rs[$j]["R_MEM_NM"]);
						$R_ZIPCODE							= trim($arr_rs[$j]["R_ZIPCODE"]);
						$R_ADDR1							= trim($arr_rs[$j]["R_ADDR1"]);
						$R_PHONE							= trim($arr_rs[$j]["R_PHONE"]);
						$R_HPHONE							= trim($arr_rs[$j]["R_HPHONE"]);
						$TOTAL_BUY_PRICE					= trim($arr_rs[$j]["TOTAL_BUY_PRICE"]);
						$TOTAL_SALE_PRICE					= trim($arr_rs[$j]["TOTAL_SALE_PRICE"]);
						$TOTAL_EXTRA_PRICE					= trim($arr_rs[$j]["TOTAL_EXTRA_PRICE"]);
						$TOTAL_QTY							= trim($arr_rs[$j]["TOTAL_QTY"]);
						$TOTAL_DELIVERY_PRICE				= trim($arr_rs[$j]["TOTAL_DELIVERY_PRICE"]);
						$TOTAL_SA_DELIVERY_PRICE			= trim($arr_rs[$j]["TOTAL_SA_DELIVERY_PRICE"]);
						$TOTAL_DISCOUNT_PRICE				= trim($arr_rs[$j]["TOTAL_DISCOUNT_PRICE"]);

						$TOTAL_PRICE						= trim($arr_rs[$j]["TOTAL_PRICE"]);
						$TOTAL_PLUS_PRICE					= trim($arr_rs[$j]["TOTAL_PLUS_PRICE"]);
						$LEE								= trim($arr_rs[$j]["LEE"]);
						
						$OPT_MANAGER_NO						= trim($arr_rs[$j]["OPT_MANAGER_NO"]);
						$admName = getAdminName($conn, $OPT_MANAGER_NO);

						$REG_ADM						= trim($arr_rs[$j]["REG_ADM"]);
						$regADMName = getAdminName($conn, $REG_ADM);

						$ORDER_DATE							= trim($arr_rs[$j]["ORDER_DATE"]);
						$PAY_DATE							= trim($arr_rs[$j]["PAY_DATE"]);
						$CANCEL_DATE						= trim($arr_rs[$j]["CANCEL_DATE"]);

						$DELIVERY_TYPE						= trim($arr_rs[$j]["DELIVERY_TYPE"]);
						$REG_DATE							= trim($arr_rs[$j]["REG_DATE"]);
						
						$ORDER_DATE		= date("n월j일 H시i분",strtotime($ORDER_DATE));


						if ($TOTAL_QTY == 0)
							$str_cancel_style = "cancel_order";
						else
							$str_cancel_style = "";

					?>
					<div class="panel panel-default">
					  <div class="panel-heading">
						<!--<h3 class="panel-title">주문일 : <b><?=$ORDER_DATE?></b>, 주문번호: <b><?=$RESERVE_NO?></b></h3>-->
							<h3 class="panel-title"><b><?=$RESERVE_NO?></b> (<?=$ORDER_DATE?>)</h3>
					  </div>
					  <div class="panel-body">
						 <div class="form-group group_line">
							<table class="table table-hover table-striped table-responsive">
							  <colgroup>
								<col width="2%"/>
								<col width=""/>
								<col width="10%"/>
								<col width="5%"/>
								<col width="10%"/>
								<col width="10%"/>
							  </colgroup>
							  <thead>
								<tr>
								  <th>#</th>
								  <th>상품정보</th>
								  <th>판매가</th>
								  <th>수량</th>
								  <th>구매예정가</th>
								  <th>배송상황</th>
								</tr>
							  </thead>
							  <tbody>
					<?
							$arr_goods = listManagerOrderGoods($conn, $RESERVE_NO, $MEM_NO, "Y", "N");

							if (sizeof($arr_goods) > 0) {
								for ($h = 0 ; $h < sizeof($arr_goods); $h++) {
									
									$ORDER_GOODS_NO				= trim($arr_goods[$h]["ORDER_GOODS_NO"]);
									$RESERVE_NO					= trim($arr_goods[$h]["RESERVE_NO"]);
									$BUY_CP_NO					= trim($arr_goods[$h]["BUY_CP_NO"]);
									$CP_ORDER_NO				= trim($arr_goods[$h]["CP_ORDER_NO"]);
									$GOODS_NO					= trim($arr_goods[$h]["GOODS_NO"]);
									$GOODS_CODE					= trim($arr_goods[$h]["GOODS_CODE"]);
									$GOODS_NAME					= SetStringFromDB($arr_goods[$h]["GOODS_NAME"]);
									$BUY_PRICE					= trim($arr_goods[$h]["BUY_PRICE"]);
									$SALE_PRICE					= trim($arr_goods[$h]["SALE_PRICE"]);
									$EXTRA_PRICE				= trim($arr_goods[$h]["EXTRA_PRICE"]);

									//C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.OPT_OUTBOX_CNT, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO

									$DELIVERY_CP				= trim($arr_goods[$h]["DELIVERY_CP"]);
									$DELIVERY_NO				= trim($arr_goods[$h]["DELIVERY_NO"]);

									$DELIVERY_CNT				= trim($arr_goods[$h]["DELIVERY_CNT"]);

									$DELIVERY_TYPE				= trim($arr_goods[$h]["DELIVERY_TYPE"]);

									$SUM_PRICE					= trim($arr_goods[$h]["SUM_PRICE"]);
									$PLUS_PRICE					= trim($arr_goods[$h]["PLUS_PRICE"]);
									$GOODS_LEE					= trim($arr_goods[$h]["LEE"]);
									$QTY						= trim($arr_goods[$h]["QTY"]);
									$PAY_DATE					= trim($arr_goods[$h]["PAY_DATE"]);
									$DELIVERY_DATE				= trim($arr_goods[$h]["DELIVERY_DATE"]);
									$FINISH_DATE				= trim($arr_goods[$h]["FINISH_DATE"]);
									$ORDER_STATE				= trim($arr_goods[$h]["ORDER_STATE"]);
									$ORDER_CONFIRM_DATE			= trim($arr_goods[$h]["ORDER_CONFIRM_DATE"]);

									$OPT_STICKER_NO				= trim($arr_goods[$h]["OPT_STICKER_NO"]);
									$OPT_OUTBOX_TF				= trim($arr_goods[$h]["OPT_OUTBOX_TF"]);
									$OPT_WRAP_NO				= trim($arr_goods[$h]["OPT_WRAP_NO"]);
									$OPT_STICKER_MSG			= trim($arr_goods[$h]["OPT_STICKER_MSG"]);
									$OPT_PRINT_MSG				= trim($arr_goods[$h]["OPT_PRINT_MSG"]);
									$OPT_OUTSTOCK_DATE			= trim($arr_goods[$h]["OPT_OUTSTOCK_DATE"]);

									$SALE_CONFIRM_TF			= trim($arr_goods[$h]["SALE_CONFIRM_TF"]);
									$SALE_CONFIRM_YMD			= trim($arr_goods[$h]["SALE_CONFIRM_YMD"]);

									
									
									if($OPT_OUTSTOCK_DATE != "" && $OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" && $OPT_OUTSTOCK_DATE != "1970-01-01 00:00:00")
										$OPT_OUTSTOCK_DATE			= date("n월 j일", strtotime($OPT_OUTSTOCK_DATE));
									else 
										$OPT_OUTSTOCK_DATE = "출고미정";

									$OPT_MEMO					= trim($arr_goods[$h]["OPT_MEMO"]);

									$CATE_01					= trim($arr_goods[$h]["CATE_01"]);
									$CATE_04					= trim($arr_goods[$h]["CATE_04"]);
									$WORK_FLAG					= trim($arr_goods[$h]["WORK_FLAG"]);
									$TAX_TF						= trim($arr_goods[$h]["TAX_TF"]);

									if ($TAX_TF == "비과세") {
										$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
									} else {
										$STR_TAX_TF = "<font color='navy'>(과세)</font>";
									}

									
									$SALE_PRICE = abs($SALE_PRICE);

									$IMG_URL = getImage($conn, $GOODS_NO, "50", "50");

									if($CATE_01 <> "")
										$str_cate_01 = $CATE_01.") ";
									else 
										$str_cate_01 = "";

									if ($CATE_04 == "CHANGE") {
										$str_cate_04 = "<font color='red'>(교환건)</font>";
									} else {
										$str_cate_04 = "";
									}

									if ($REQ_DATE <> "")  {
										$REQ_DATE		= date("Y-m-d H:i",strtotime($REQ_DATE));
									}
									
									if ($DELIVERY_CP <> "") {
										if ($FINISH_DATE <> "")  {
											$FINISH_DATE		= date("Y-m-d H:i",strtotime($FINISH_DATE));
										}
									} else {
										$FINISH_DATE = "";
									}
									
									if ($h == (sizeof($arr_goods)-1)) {

										if ($ORDER_STATE == "1") {
											$str_tr = "goods_1_end";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3_end";
										} else {
											$str_tr = "goods_end";
										}

									} else {

										if ($ORDER_STATE == "1") {
											$str_tr = "goods_1";
										} else if ($ORDER_STATE == "3") {
											$str_tr = "goods_3";
										} else {
											$str_tr = "goods";
										}
									}
									
									$OPT_OUTBOX_TF = ($OPT_OUTBOX_TF == "Y" ? "있음" : "" );

									$str_price_class = "price";
									$str_state_class = "state";

									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2")) {
										$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
									
									
									} else if (($ORDER_STATE == "3")) {
										$refund_able_qty = getRealDeliveryQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);
									
									
									} else if ($ORDER_STATE == "7") {
										$refund_able_qty = -$QTY;

										$str_price_class = "price_refund";
										$str_state_class = "state_refund";

									} else {
										$refund_able_qty = $QTY;
									}

									if ($refund_able_qty == 0)
										$str_cancel_style = "cancel_goods";
									else
										$str_cancel_style = "";

									if($refund_able_qty > 0) { 
										switch($ORDER_STATE) { 
											case "1" : $cnt_1++; break;
											case "2" : if($WORK_FLAG == "Y")
															$cnt_3++;
														else 
															$cnt_2++;
														break;
											case "3" : $cnt_4++; break;

										}
									}
									
									if (($ORDER_STATE == "1") || ($ORDER_STATE == "2") || ($ORDER_STATE == "3")) {
										// || ($ORDER_STATE == "7") -- 반품은 반품리스트에서 보여져야함
									
										//if ($refund_able_qty <> 0) {
						?>
						<tr>
							<th scope="row">
								<img src="<?=$IMG_URL?>" width="50" height="50">
							</th>
							<td> [<?=$GOODS_CODE?>] <?=$GOODS_NAME?></td>
							<td><?=number_format($SALE_PRICE)?>원</td>
							<td><?=number_format($refund_able_qty)?></td>
							<td><?=number_format($SALE_PRICE * $refund_able_qty)?>원</td>
							<td>
								<?  
								
									if($refund_able_qty > 0) { 
										if($ORDER_STATE == "2") { 
											if($WORK_FLAG == "Y")
												echo "배송중";
											else 
												echo "작업중";
										} else
											echo getDcodeName($conn, "ORDER_STATE", $ORDER_STATE);
									} else
										echo "취소";
								?>
							
							</td>
						</tr>
						<?
									}
								}
							}
						?>
						
							  </tbody>
							</table>
						</div>
					  </div>
					  <div class="panel-footer">
						<b>주문합계 :</b> 총 판매가: <b><?=number_format($TOTAL_SALE_PRICE)?></b>원, 총 수량: <b><?=number_format($TOTAL_QTY)?></b>개, 총 추가배송비: <b><?=number_format($TOTAL_SA_DELIVERY_PRICE)?></b>원, 총 할인: <b><?=number_format($TOTAL_DISCOUNT_PRICE)?></b>원, 총 구매 합계: <b><?=number_format($TOTAL_SALE_PRICE + $TOTAL_SA_DELIVERY_PRICE - $TOTAL_DISCOUNT_PRICE  )?></b>원
					  </div>
					</div>
			<?		
					}
			   } else { ?>
				<div class="well text-center">검색된 주문내역이 없습니다.</div>
			<? } ?>

			
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
<script type="text/javascript" src="/manager/jquery/jquery-datepicker-ko.js"></script>
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });

	$(".date").click(function(e){
		e.preventDefault();
		var date_span = $(this).data("date_span");

		if(date_span == "") return;

		var d = new Date();
		d.setDate(d.getDate() - date_span);
		$("#datepicker-start").val(d.toISOString().slice(0,10));
		var e = new Date();
		$("#datepicker-end").val(e.toISOString().slice(0,10));

	});


	$(".li_order_state").click(function(e){
		e.preventDefault();
		var sel_order_state = $(this).data("order_state");
		$("input[name=sel_order_state]").val(sel_order_state);
	});

	$('#btn-datepicker-start').click(function(){
		//alert('clicked');
		$(document).ready(function(){
			$("#datepicker-start").datepicker().focus();
		});
	});
	$('#btn-datepicker-end').click(function(){
		//alert('clicked');
		$(document).ready(function(){
			$("#datepicker-end").datepicker().focus();
		});
	});

	//수량 디스플레이
	$("#cnt_1").html(<?=$cnt_1?>);
	$("#cnt_2").html(<?=$cnt_2?>);
	$("#cnt_3").html(<?=$cnt_3?>);
	$("#cnt_4").html(<?=$cnt_4?>);
  });
</script>
</body>
</html>

