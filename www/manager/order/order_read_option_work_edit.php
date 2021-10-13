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
	$menu_right = "OD005"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/confirm/confirm.php";
	

	if ($mode == "U") {

		if($opt_outstock_date == "1970-01-01 00:00:00")
			$opt_outstock_date = "";

		/*
		$arr_rs_og = selectOrderGoods($conn, $order_goods_no);
		if(sizeof($arr_rs_og) > 0) {
		
			$old_work_seq = $arr_rs_og[0]["WORK_SEQ"];
			$old_work_flag = $arr_rs_og[0]["WORK_FLAG"];
			$old_work_start_date = $arr_rs_og[0]["WORK_START_DATE"];

			//echo $old_work_seq." // ".$old_work_start_date." // ".$old_work_flag."<br/>";

			//순번이 잡혀있고 아직 작업전이라면
			if($old_work_seq <> 0 && $old_work_start_date <> "0000-00-00 00:00:00" && $old_work_flag == "N") { 

				$old_opt_wrap_no = $arr_rs_og[0]["OPT_WRAP_NO"];
				// 포장지 작업
				if ($opt_wrap_no <> $old_opt_wrap_no && $opt_wrap_no <> "") {
					$result = insertWork($conn, $order_goods_no, "WRAP", "N", $old_work_start_date, $old_work_seq, $s_adm_no);
				} 
				if ($opt_wrap_no == "") { 
					$result = deleteWork($conn, $order_goods_no, "WRAP", "N");
				}

				$old_opt_sticker_no = $arr_rs_og[0]["OPT_STICKER_NO"];
				// 인케이스 스티커 작업
				if ($opt_sticker_no <> $old_opt_sticker_no && $opt_sticker_no <> "") {
					$result = insertWork($conn, $order_goods_no, "STICKER", "N", $old_work_start_date, $old_work_seq, $s_adm_no);
				}
				if ($opt_sticker_no == "") { 
					$result = deleteWork($conn, $order_goods_no, "STICKER", "N");
				}

				$old_opt_outbox_tf = $arr_rs_og[0]["OPT_OUTBOX_TF"];
				// 아웃박스 스티커작업
				if ($opt_outbox_tf <> $old_opt_outbox_tf && $opt_outbox_tf == "Y") {
					$result = insertWork($conn, $order_goods_no, "OUTSTICKER", "N", $old_work_start_date, $old_work_seq, $s_adm_no);
				}
				if ($opt_outbox_tf <> $old_opt_outbox_tf && $opt_outbox_tf == "N") {
					$result = deleteWork($conn, $order_goods_no, "OUTSTICKER", "N");
				}
			}

		}
		*/
		//exit;

		$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

		$result = updateOrderGoodsOptionWork($conn, $reserve_no, $order_goods_no, $old_cate_01, $cate_01, $cate_02, $opt_wrap_no, $opt_sticker_no, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $cp_order_no, $opt_outstock_date, $delivery_type, $old_sa_delivery_price, $sa_delivery_price, $opt_memo, $memos, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box, $s_adm_no);

		updateCompanyLedgerByOrderSub($conn, "추가배송비", $order_goods_no, $sa_delivery_price, $s_adm_no);

		//2017-07-19 기장에 추가/샘플, 과세비과세 일괄 수정
		updateCompanyLedgerExtraInfo($conn, $cate_01, $tax_tf, $order_goods_no);

		//외부업체발송, 기타 (작업없음)에서
		if($old_delivery_type == "98" || $old_delivery_type == "99") { 
			//택배, 직접수령, 퀵, 개별 (작업있음)으로 변경될때
			if($delivery_type == "0" || $delivery_type == "1" || $delivery_type == "2" || $delivery_type == "3") { 
				updateWorksFlagNOrderGoods($conn, $order_goods_no);
			}
		} else {
			
			//역으로 바뀌었을때 순번제외
			if($delivery_type == "98" || $delivery_type == "99") { 
				deleteWorks($conn, $order_goods_no, $s_adm_no);
			}

		}

		resetOrderInfor($conn, $reserve_no);

		if ($result) {
?>
<script type="text/javascript">
	window.opener.js_reload();
	alert("수정 되었습니다.");
	self.close();
</script>
<?
			mysql_close($conn);
			exit;
		}

	}

#====================================================================
# Request Parameter
#====================================================================

	$order_goods_no		= trim($order_goods_no);

	$del_tf = "N";
	
#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = selectOrderGoods($conn, $order_goods_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/board.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="../jquery/theme.css" type="text/css" />
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
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
      changeYear: true
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
			checkDt($("input[name=opt_outstock_date]"));
	});

  });
</script>
<script language="javascript">


function js_save() {
	
	var frm = document.frm;

	//작업지시서에 계속 떠있게 하기 위해서 -> 변경 대량건을 제외하고 출고일 반드시 지정
	if(!document.frm.bulk_tf.checked) { 
		if (document.frm.opt_outstock_date.value.length == 0) {
			
			alert("대량건을 제외하고 출고예정일을 지정해주십시오.");
			document.frm.opt_outstock_date.focus();
			return;
		} else { 

			var diff = new Date(new Date() - new Date(document.frm.opt_outstock_date.value));
			var days = diff/1000/60/60/24;

			if(days > 1) {
				alert('출고일이 과거로 설정되어 있습니다. 확인해주세요.');
			}

		}
	}

	frm.mode.value = "U";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}


</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>옵션 수정</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="mode"value="">
		<input type="hidden" name="order_goods_no"value="<?=$order_goods_no?>">
		
		
		<h2>* 주문 상품</h2>
		<?
			$nCnt = 0;
			$total_sum_price = 0;
			$sum_qty = 0;
			
			if (sizeof($arr_rs) > 0) {
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
					$ORDER_GOODS_NO		= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
					$ON_UID				= trim($arr_rs[$j]["ON_UID"]);
					$RESERVE_NO			= trim($arr_rs[$j]["RESERVE_NO"]);
					$GOODS_NO			= trim($arr_rs[$j]["GOODS_NO"]);
					$GOODS_CODE			= trim($arr_rs[$j]["GOODS_CODE"]);
					$GOODS_SUB_NAME		= trim($arr_rs[$j]["GOODS_SUB_NAME"]);
					
					$GOODS_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);

					$CATE_01			= trim($arr_rs[$j]["CATE_01"]);
					$CATE_02			= trim($arr_rs[$j]["CATE_02"]);
					$CATE_03			= trim($arr_rs[$j]["CATE_03"]);
					$CATE_04			= trim($arr_rs[$j]["CATE_04"]);

					$ORDER_STATE		= trim($arr_rs[$j]["ORDER_STATE"]);

					$rs_opt_wrap_no		= trim($arr_rs[$j]["OPT_WRAP_NO"]);
					$rs_opt_sticker_no  = trim($arr_rs[$j]["OPT_STICKER_NO"]);
					$rs_opt_sticker_msg = trim($arr_rs[$j]["OPT_STICKER_MSG"]);
					$rs_opt_print_msg   = trim($arr_rs[$j]["OPT_PRINT_MSG"]);
					$rs_opt_outbox_tf		= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
					$rs_opt_outstock_date	= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);

					if($rs_opt_outstock_date != "0000-00-00 00:00:00")
						$rs_opt_outstock_date = date('Y-m-d', strtotime($rs_opt_outstock_date));
					else 
						$rs_opt_outstock_date = "";


					$rs_delivery_type		= trim($arr_rs[$j]["DELIVERY_TYPE"]);
					$rs_sa_delivery_price	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
					$rs_opt_memo			= trim($arr_rs[$j]["OPT_MEMO"]);
					$rs_opt_request_memo	= trim($arr_rs[$j]["OPT_REQUEST_MEMO"]);
					$rs_opt_support_memo	= trim($arr_rs[$j]["OPT_SUPPORT_MEMO"]);
					$rs_cp_order_no			= trim($arr_rs[$j]["CP_ORDER_NO"]);
					$rs_delivery_cp			= trim($arr_rs[$j]["DELIVERY_CP"]);
					$rs_sender_nm			= trim($arr_rs[$j]["SENDER_NM"]);
					$rs_sender_phone		= trim($arr_rs[$j]["SENDER_PHONE"]);
					$rs_delivery_cnt_in_box = trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
					$rs_tax_tf				= trim($arr_rs[$j]["TAX_TF"]);

					$arr_cp = selectCompanyByReserveNo($conn, $RESERVE_NO);
					$CP_CATE	= $arr_cp[0]["CP_CATE"];

				?>
					<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
						<colgroup>
						<col width="10%" />
						<col width="50%" />
						<col width="*" />
					</colgroup>
					<tr>
						<th>상품코드</th>
						<th>상품명</th>
						<th class="end">주문상품종류</th>
					</tr>
					<tr>
						<td><?= $GOODS_NO?></td>
						<td class="modeual_nm" height="35"><?=$GOODS_NAME?><br><?=$GOODS_SUB_NAME?>
							<input type="hidden" name="on_uid" value="<?=$ON_UID?>">
							<input type="hidden" name="reserve_no" value="<?=$RESERVE_NO?>">
							<input type="hidden" name="tax_tf" value="<?=$rs_tax_tf?>">
						</td>
						<td>
							<?= makeSelectBox($conn,"ORDER_GOODS_TYPE","cate_01","70","일반","",$CATE_01) ?>
							<input type="hidden" name="old_cate_01" value="<?=$CATE_01?>">
							<script>
								$(function(){
									$("select[name=cate_01]").change(function(){

										if($(this).val() == "추가") { 
											$("input[name=opt_outstock_date]").val('<?=date("Y-m-d", strtotime("15 day"))?>'); //출고예정일 +15일
											$("input[name=bulk_tf]").prop("checked", false); //출고미지정 해제
											$("select[name=delivery_type]").val("99"); //기타
											$("select[name=delivery_cp]").hide(); //택배회사 가리기
										}

									});
								});
							</script>
						</td>
					</tr>
					</table>
				

					<div class="sp10"></div>
					* 작업 내용
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="*" />
					</colgroup>
					<tbody>


					<tr>
						<th>포장지</th>
						<td class="line">
							<?
								$ar_wrap_filtered = array();
								$ar_wrap_all = array();
								//echo $CP_CATE."<br/>";
								if($CP_CATE <> "" && $rs_opt_wrap_no <= 0) { 

									$arr_options = array("and_search_category" => $CP_CATE, "or_search_category" => "310301");

									$arr_wrap_filtered = listGoods($conn, "010204", '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', $arr_options, 'GOODS_NAME', 'ASC', '1', '1000');

									$arr_wrap_all = listGoods($conn, '010204', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');

									
									foreach($arr_wrap_filtered as $item) { 
										$ar_wrap_filtered[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_filtered, "opt_wrap_no", "150", "선택없음", "", $rs_opt_wrap_no, "GOODS_NO", "GOODS_NAME");

								} else { 
									$arr_wrap_all = listGoods($conn, '010204', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');
									
									foreach($arr_wrap_all as $item) { 
										$ar_wrap_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
									}

									echo makeGoodsSelectBoxWithDataImage($conn, $arr_wrap_all, "opt_wrap_no", "150", "선택없음", "", $rs_opt_wrap_no, "GOODS_NO", "GOODS_NAME");
								}

							?>
								<?
									if($CP_CATE <> "" && $rs_opt_wrap_no <= 0) { 
								?>
								<label><input type="checkbox" id="wrap_all" value="Y"/>전체</label>
								<?
									}
								?>
								<script>
								$(function(){

									var arr_wrap_filtered = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_wrap_filtered)?>));
									var arr_wrap_all = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_wrap_all)?>));
			
									$("#wrap_all").click(function(){
										$("select[name=opt_wrap_no]").find('option').remove().end();
										$("select[name=opt_wrap_no]").append('<option value="" data-image="/manager/images/no_img.gif">선택없음</option>');

										if($(this).is(":checked")) { 
											

											for(var i = 0; i < arr_wrap_all.length; i++) { 
												$("select[name=opt_wrap_no]").append('<option value="'+arr_wrap_all[i].GOODS_NO+'" data-image="'+arr_wrap_all[i].IMG_URL+'">'+arr_wrap_all[i].GOODS_NAME2+'</option>');
											}
											
										} else { 
											for(var i = 0; i < arr_wrap_filtered.length; i++) { 
												$("select[name=opt_wrap_no]").append('<option value="'+arr_wrap_filtered[i].GOODS_NO+'" data-image="'+arr_wrap_filtered[i].IMG_URL+'">'+arr_wrap_filtered[i].GOODS_NAME2+'</option>');
											}
										}

									});


									$("select[name=opt_wrap_no]").change(function(){
										var image_url = $(this).find(':selected').attr('data-image');
										$("img[name=sample_img]").attr("src", image_url);

										//js_calculate_all_price();

											

									});

									<?
										if($rs_opt_wrap_no > 0) { 	
									?>
										$("select[name=opt_wrap_no]").trigger('change');
									<?
										}	
									?>
								});
								</script>

						</td>
						<td rowspan="2" colspan="2" style="text-align:center;"><img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/></td>
					</tr>
					<tr>
						<th>스티커</th>
					<td class="line"> 
							
							<?

							$ar_sticker_filtered = array();
							$ar_sticker_all = array();

							if($CP_CATE <> "" && $rs_opt_sticker_no <= 0) { 

								$arr_options = array("and_search_category" => $CP_CATE, "or_search_category" => "310302");
								print_r($arr_options);

								$arr_sticker_filtered = listGoods($conn, "0103", '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', $arr_options, 'GOODS_NAME', 'ASC', '1', '1000');
								echo count($arr_sticker_filtered);

								$arr_sticker_all = listGoods($conn, '0103', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');
								
								foreach($arr_sticker_filtered as $item) { 
									$ar_sticker_filtered[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
								}
								
								foreach($arr_sticker_all as $item) { 
									$ar_sticker_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
								}

								echo makeGoodsSelectBoxWithDataImage($conn, $arr_sticker_filtered, "opt_sticker_no", "150", "선택없음", "", $rs_opt_sticker_no, "GOODS_NO", "GOODS_NAME");

								
							} else { 

								$arr_sticker_all = listGoods($conn, '0103', '', '', '', '', '', '', '', '판매중', '', 'Y', 'N', '', '', '', 'GOODS_NAME', 'ASC', '1', '1000');

								foreach($arr_sticker_all as $item) { 
									$ar_sticker_all[] = array("GOODS_NO" => $item["GOODS_NO"],  "GOODS_NAME2" => iconv("EUC-KR", "UTF-8", $item["GOODS_NAME"]), "IMG_URL" => getImage($conn, $item["GOODS_NO"], "250", "250"));
								}

								echo makeGoodsSelectBoxWithDataImage($conn, $arr_sticker_all, "opt_sticker_no", "150", "선택없음", "", $rs_opt_sticker_no, "GOODS_NO", "GOODS_NAME");
							}

							

							?>
							<? if($CP_CATE <> "" && $rs_opt_sticker_no <= 0) { ?>
							<label><input type="checkbox" id="sticker_all" value="Y"/>전체</label>
							<? } ?>
						<script>
						$(function(){

							var arr_sticker_filtered = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_sticker_filtered)?>));
							var arr_sticker_all = jQuery.parseJSON(JSON.stringify(<?=json_encode($ar_sticker_all)?>));
	
							$("#sticker_all").click(function(){
								$("select[name=opt_sticker_no]").find('option').remove().end();
								$("select[name=opt_sticker_no]").append('<option value="" data-image="/manager/images/no_img.gif">선택없음</option>');

								if($(this).is(":checked")) { 
									

									for(var i = 0; i < arr_sticker_all.length; i++) { 
										$("select[name=opt_sticker_no]").append('<option value="'+arr_sticker_all[i].GOODS_NO+'" data-image="'+arr_sticker_all[i].IMG_URL+'" >'+arr_sticker_all[i].GOODS_NAME2+'</option>');
									}
									
								} else { 
									for(var i = 0; i < arr_sticker_filtered.length; i++) { 
										$("select[name=opt_sticker_no]").append('<option value="'+arr_sticker_filtered[i].GOODS_NO+'" data-image="'+arr_sticker_filtered[i].IMG_URL+'" >'+arr_sticker_filtered[i].GOODS_NAME2+'</option>');
									}
								}

							});

							$("select[name=opt_sticker_no]").change(function(){
								var image_url = $(this).find(':selected').attr('data-image');
								$("img[name=sample_img]").attr("src", image_url);

								//js_calculate_all_price();

							});

							<?
								if($rs_opt_sticker_no > 0) { 	
							?>
								$("select[name=opt_sticker_no]").trigger('change');
							<?
								}	
							?>

						});
						</script>
					</td>
					
					</tr>
					<tr>
						<th>스티커 메세지</th>
						<td class="line" colspan="3"> 
							<input type="text" class="txt" style="width:90%" name="opt_sticker_msg" value="<?=$rs_opt_sticker_msg?>"/>
						</td>
					</tr>
					<tr>
						<th>인쇄 (통장지갑등)</th>
						<td class="line" colspan="3">
								<input type="text" class="txt" style="width:90%" name="opt_print_msg" value="<?=$rs_opt_print_msg?>"/>
						</td>
					</tr>
					<tr>
						<th>아웃박스 스티커</th>
						<td> <?= makeSelectBox($conn,"OUTBOX_STICKER_TF","opt_outbox_tf","150","","",$rs_opt_outbox_tf) ?></td>
						<th>업체주문번호</th>
						<td class="line">
								<input type="text" class="txt" style="width:120px;" name="cp_order_no" value="<?=$rs_cp_order_no?>"/>
						</td>
					</tr>
					<tr>
						<th>출고예정일</th>
						<td class="line" colspan="3">
						<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="opt_outstock_date" value="<?=$rs_opt_outstock_date?>" maxlength="10"/>
						&nbsp; <label><input type="checkbox"  name="bulk_tf" <?if($rs_opt_outstock_date == "") echo "checked";?> value="Y"/> 대량건/출고미지정</label>
						<script type="text/javascript">
							$(function(){
								$("input[name=bulk_tf]").click(function(){
									$("input[name=opt_outstock_date]").val('');
								});

								$("input[type=text][name=opt_outstock_date]").on('keydown, click',function(){
									$("input[type=checkbox][name=bulk_tf]").prop('checked', false);
								});
							});
						</script>
						</td>
						
					</tr>
					<tr>
						<th>작업메모(창고)</th>
						<td colspan="3">
							<textarea name="opt_memo" style="width:98%; height:50px" class="txt"><?=$rs_opt_memo?></textarea>
						</td>
					</tr>
					<tr>
						<th>발주메모(공급사)</th>
						<td colspan="3">
							<textarea name="opt_request_memo" style="width:98%; height:50px" class="txt"><?=$rs_opt_request_memo?></textarea>
						</td>
					</tr>
					<tr>
						<th>운영메모(지원)</th>
						<td colspan="3">
							<textarea name="opt_support_memo" style="width:98%; height:50px" class="txt"><?=$rs_opt_support_memo?></textarea>
						</td>
					</tr>
					</tbody>
				</table>
				<div class="sp10"></div>
				* 배송 내용
					<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="*" />
					</colgroup>
					<tbody>
					<tr>
						<th>배송방식</th>
						<td>
							<input type="hidden" name="old_delivery_type"value="<?=$rs_delivery_type?>">

							<?=makeSelectBox($conn,"DELIVERY_TYPE", "delivery_type", "100", "배송방법을 선택하세요", "", $rs_delivery_type)?>
							<?=makeSelectBox($conn,"DELIVERY_CP_OP", "delivery_cp", "100", "택배회사를 선택하세요", "", $rs_delivery_cp)?>
							<script>
								$(function(){

									if($("select[name=delivery_type]").val() == "0" || $("select[name=delivery_type]").val() == "3") //택배, 개별택배시
										$("select[name=delivery_cp]").show();
									else
										$("select[name=delivery_cp]").hide();

									$("select[name=delivery_type]").change(function(){
										if($("select[name=delivery_type]").val() == "0" || $("select[name=delivery_type]").val() == "3") //택배, 개별택배시
											$("select[name=delivery_cp]").show();
										else { 
											$("select[name=delivery_cp]").val('');
											$("select[name=delivery_cp]").hide();
										}
									});
								});
							</script>
						</td>
						<th>배송비(운반비차액)</th>
						<td>
							<input type="hidden" name="old_sa_delivery_price" value="<?=$rs_sa_delivery_price?>" />
							<input type="text" class="txt" style="width:105px" name="sa_delivery_price" value="<?=$rs_sa_delivery_price?>" required onkeyup="return isMathNumber(this)"/> 원
						</td>
					</tr>
					<tr>
						<th>보내는사람</th>
						<td>
							<input type="Text" name="sender_nm" value="<?= $rs_sender_nm?>" style="width:70%;" class="txt">
						</td>
						<th>보내는사람연락처</th>
						<td>
							<input type="Text" name="sender_phone" value="<?= $rs_sender_phone?>" style="width:160px;" class="txt">
						</td>
					</tr>
					<tr>
						<th>박스입수</th>
						<td colspan="3">
							<input type="Text" name="delivery_cnt_in_box" value="<?= $rs_delivery_cnt_in_box?>" style="width:70%;" class="txt">
						</td>
					</tr>
					
					</table>
				<?
						}
					}else{
				?>
					<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:98%" border="0">
						<colgroup>
						<col width="10%" />
						<col width="50%" />
						<col width="*" />
						</colgroup>
						<tr>
							<td height="50" align="center" colspan="3">데이터가 없습니다. </td>
						</tr>
					</table>
				<?
					}
				?>

				<? if($ORDER_STATE == "1") { ?>
				<div class="sp10"></div>
				* 세금 계산서 
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="120" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<th>승인번호</th>
					<td>
						<textarea name="cate_02" style="width:345px; height:60px" placeholder="ex)20500101-10000000-XXXXXXXX"><?=$CATE_02?></textarea>
					</td>
				</tr>
				</tbody>
				</table>
				<script type="text/javascript">
					$(function(){
						$("[name=cate_02]").keyup(function(){
							var ks = $(this).val();
							prev_row = ks.substring(0, ks.lastIndexOf("\n") + 1);
							last_row = ks.substring(ks.lastIndexOf("\n") + 1);
							key_text = $(this).val();
							if(last_row.length == 8) { 
								merged = last_row + "-10000000-";
								$(this).val(prev_row + merged);
							}
						});
					});
				</script>
				<? } ?>
					<div class="sp10"></div>
					<div class="btn">
					<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="등록" /></a>
					<? } ?>
					</div>
					<div class="sp35"></div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>