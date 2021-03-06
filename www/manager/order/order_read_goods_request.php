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
	$menu_right = "SP009"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/confirm/confirm.php";
?>

<?
	function getDeliveryPaper($db,$orderGoodsNo){
		$query="SELECT REQ_GOODS_NO, GOODS_NO, GOODS_NAME, REQ_QTY 
				FROM TBL_GOODS_REQUEST_GOODS
				WHERE ORDER_GOODS_NO=".$orderGoodsNo." ; ";
		
		echo $query;
		// exit;

		$result=mysql_query($query, $db);
		$record=array();
		if($result<>""){
			$cnt=mysql_num_rows($result);
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);

			}

		}
		return $record;
	}
?>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
	<script>

		function js_attach_delivery_price(reqGoodsNo,delPapers){

			var idDelPrice="txt_"+reqGoodsNo+"";
			var deliveryPrice=document.getElementById(idDelPrice).value;
			alert(deliveryPrice+" "+reqGoodsNo);
			// return;

			$.ajax({
				url:"../ajax_processing.php",
				dataType:"text",
				type:'POST',
				data:{
					'mode':"ATTACH_DELIVERY_PRICE",
					'reqGoodsNo':reqGoodsNo,
					'delPrice':deliveryPrice,
					'box':delPapers
				},
				success:function(data){
					alert('택배비 변경 성공');
					self.close();
					
				},
				error:function(jqXHR, textStatus, errorThrown){
					alert('실패');
				}
				
			});//end of $.ajax({});
			return ;//explicit function end
		}//end of function js_attach_delivery_price(reqGoodsNo);
	</script>
<?

	if($mode =="APPLICATE_DELIVERY_PRICE"){
		echo "DELIVERY PRICE : ".$txtDeliveryPrice."<br>";
		echo "ORDER_GOODS-NO : ".$order_goods_no."<br>";
		echo "delivery_cnt_in_box : ".$delivery_cnt_in_box."<br>";

		$arrDelivery=getDeliveryPaper($conn, $order_goods_no);
		$cntDelivery=sizeof($arrDelivery);
		$deliveryPrice=str_replace(",","",$txtDeliveryPrice);

		// $deliveryPrice=abs($deliveryPrice);

		if($cntDelivery>1){
			?>
				<table>
					<tr>
						<td>상품명</td>
						<td>발주수량</td>
						<td>박스입수</td>
						<td>택배비</td>
						<td>변경</td>
					</tr>
			<?
			for($i=0;$i<$cntDelivery;$i++){
				$mReqGoodsNo	=	$arrDelivery[$i]["REQ_GOODS_NO"];
				$mGoodsNo		=	$arrDelivery[$i]["GOODS_NO"];
				$mGoodsName		=	$arrDelivery[$i]["GOODS_NAME"];
				$mReqQty		=	$arrDelivery[$i]["REQ_QTY"];
				$remiander=$mReqQty%$delivery_cnt_in_box;
				$deliveryPaperCnt= sprintf('%d',$mReqQty/$delivery_cnt_in_box);
				if($remiander>0) $deliveryPaperCnt++;
				?>
					<tr>
						<td><?=$mGoodsName?></td>
						<td><?=$mReqQty?></td>
						<td><?=$delivery_cnt_in_box?></td>
						<td><input type='text' id='txt_<?=$mReqGoodsNo?>' value='<?=$txtDeliveryPrice?>'></td>
						<td><input type='button' value="택배비 변경" onclick="javascript:js_attach_delivery_price(<?=$mReqGoodsNo?>,<?=$deliveryPaperCnt?>)"></td>
					</tr>
				<?
			}
		}
		else if($cntDelivery==1){
			$req_qty = $arrDelivery[0]["REQ_QTY"];//-18

			$req_qty = abs($req_qty);//18


			// exit;

			$remiander = $req_qty%$delivery_cnt_in_box;

			$deliveryPaperCnt = sprintf('%d',$req_qty/$delivery_cnt_in_box);
			if($remiander > 0) $deliveryPaperCnt++;
			echo "deliveryPaperCnt : ".$deliveryPaperCnt."<br>";
			// exit;
			InsertRequestGoodsSubLedger($conn,$arrDelivery[0]["REQ_GOODS_NO"],"택배비",$deliveryPaperCnt,$deliveryPrice,"",$s_adm_no);
			echo "<script>self.close();</script>";
			// exit;
			
		}
		else{
			echo "0이하<br>";
		}

	}

	if($mode == "FROM_ORDER") { 
		$arr_order_rs = selectOrder($conn, $reserve_no);

		$cp_no						= trim($arr_order_rs[0]["CP_NO"]); 
		$order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
		$o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]); 
		$o_zipcode					= trim($arr_order_rs[0]["O_ZIPCODE"]); 
		$o_addr1					= trim($arr_order_rs[0]["O_ADDR1"]); 
		$o_addr2					= trim($arr_order_rs[0]["O_ADDR2"]); 
		$o_phone					= trim($arr_order_rs[0]["O_PHONE"]); 
		$o_hphone					= trim($arr_order_rs[0]["O_HPHONE"]); 
		$o_email					= trim($arr_order_rs[0]["O_EMAIL"]); 
		$r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
		$r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
		$r_addr1					= trim($arr_order_rs[0]["R_ADDR1"]); 
		$r_addr2					= trim($arr_order_rs[0]["R_ADDR2"]); 
		$r_phone					= trim($arr_order_rs[0]["R_PHONE"]); 
		$r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
		$r_email					= trim($arr_order_rs[0]["R_EMAIL"]); 
		$order_date					= trim($arr_order_rs[0]["ORDER_DATE"]); 
		$memo						= trim($arr_order_rs[0]["MEMO"]); 
		$total_delivery_price		= trim($arr_order_rs[0]["TOTAL_DELIVERY_PRICE"]); 
		$cp_nm = getCompanyNameWithNoCode($conn,$cp_no);
	  
		$rs_opt_manager_no			= trim($arr_order_rs[0]["OPT_MANAGER_NO"]); 

		$today = date("Y-m-d", strtotime("0 month"));

		$goods_no = trim($goods_no);

		// $memo1 비고1
		$rs_order_goods = selectOrderGoods($conn, $order_goods_no);
		$rs_goods_no			= trim($rs_order_goods[0]["GOODS_NO"]);
		$rs_opt_wrap_no			= trim($rs_order_goods[0]["OPT_WRAP_NO"]);
		$rs_opt_sticker_no		= trim($rs_order_goods[0]["OPT_STICKER_NO"]);
		$rs_opt_sticker_ready	= trim($rs_order_goods[0]["OPT_STICKER_READY"]);
		$rs_opt_outbox_tf		= trim($rs_order_goods[0]["OPT_OUTBOX_TF"]);
		$rs_opt_sticker_msg		= trim($rs_order_goods[0]["OPT_STICKER_MSG"]);
		$rs_opt_print_msg		= trim($rs_order_goods[0]["OPT_PRINT_MSG"]);
		$rs_opt_memo			= trim($rs_order_goods[0]["OPT_MEMO"]);
		$rs_opt_request_memo	= trim($rs_order_goods[0]["OPT_REQUEST_MEMO"]);
		$rs_delivery_type		= trim($rs_order_goods[0]["DELIVERY_TYPE"]);
		
		$memo1	= "";

		if($rs_delivery_type != "98") { 
			$memo1 .= ($rs_opt_print_msg <> "" ? "인쇄메세지 : ".$rs_opt_print_msg." / " : "");
			$memo1 .= ($rs_opt_request_memo <> "" ? "발주메모 : ".$rs_opt_request_memo. " / " : "");
			$memo1 = rtrim($memo1, '/ ');
			
		} else {
			$memo1 .= ($rs_opt_sticker_no <> "0" ? "스티커 : ".getGoodsName($conn, $rs_opt_sticker_no)." / " : "");
			$memo1 .= ($rs_opt_outbox_tf == "Y" ? "아웃박스스티커 : 있음 / " : "" );
			$memo1 .= ($rs_opt_wrap_no <> "0" ? "포장지 : ".getGoodsName($conn, $rs_opt_wrap_no). " / " : "");
			$memo1 .= ($rs_opt_sticker_msg <> "" ? "스티커메세지 : ".$rs_opt_sticker_msg. " / " : "");
			$memo1 .= ($rs_opt_print_msg <> "" ? "인쇄메세지 : ".$rs_opt_print_msg. " / " : "");
			$memo1 .= ($rs_opt_request_memo <> "" ? "발주메모 : ".$rs_opt_request_memo. " / " : "");
			$memo1 = rtrim($memo1, '/ ');
		}

		// 업체가 몰일 경우에 수령자를, 몰이 아닐 경우엔 판매업체명을 표기 (2016-05-12) - $r_mem_nm 비고2
		if(isCompanyMall($conn, $cp_no))
			$memo2 = $o_mem_nm;
		else
			$memo2 = $cp_nm;

		$req_qty		= $order_qty;

		$arr_goods = selectGoods($conn, $goods_no);
		$buy_cp_no = trim($arr_goods[0]["CATE_03"]);
		$buy_price = trim($arr_goods[0]["BUY_PRICE"]);
		$goods_code = trim($arr_goods[0]["GOODS_CODE"]);
		$goods_name = trim($arr_goods[0]["GOODS_NAME"]);
		$goods_sub_name = trim($arr_goods[0]["GOODS_SUB_NAME"]);
		
		if($is_selected == "Y" && $selected_buy_info <> "") {
			$arr_info = explode("|", $selected_buy_info);
			if(sizeof($arr_info) == 2) { 
				$buy_cp_no = $arr_info[0];
				$buy_price = $arr_info[1];
			}
		}

		//echo $is_selected."//".$buy_cp_no."//".$buy_price."<br/>";

		if($buy_cp_no == "" && $cp_type <> "")
			$buy_cp_no = $cp_type;

		if($goods_sub_name == "" && $sub_name <> "")
			$goods_sub_name = $sub_name;

		//샘플, 인쇄비등에 공급가 선 추가 - 2017-03-27 
		if($sub_buy_price <> "")
			$buy_price = $sub_buy_price;

		$group_no = cntMaxGroupNoRequest($conn);
		$req_date = $today;
		$delivery_type = "";
		$memo = "";
		$buy_total_price = $buy_price * $req_qty;
		
		if($rs_delivery_type == "98") //외부업체발송
			$chk_to_here = false;
		else
			$chk_to_here = true;
		
		$arr_req = getReqNoFromBuyCPNo($conn, $buy_cp_no); 
		if(sizeof($arr_req) <= 0) { 
			$req_no = insertGoodsRequest($conn, $s_adm_com_code, $group_no, $req_date, $buy_cp_no, $delivery_type, $memo, $s_adm_no);
		} else { 
			$req_no = $arr_req[0]["REQ_NO"];
			$group_no = $arr_req[0]["GROUP_NO"];
		}

		$memo1 = SetStringToDB($memo1);
		$memo2 = SetStringToDB($memo2);

		insertGoodsRequestGoods($conn, $s_adm_com_code, $req_no, $reserve_no, $order_goods_no, $group_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $s_adm_no);
		resetGoodsRequestTotal($conn, $req_no);

		//단품등 공급상품일경우 알림후 닫기, 세트 및 부자재는 발주후 안닫음
		if($is_window_closing == "Y") { 
?>
<script>
		alert("입력되었습니다. 발주리스트에서 확인해주세요.");
		self.close();
		
</script>
<?
		} else { 
?>
<script>
		window.location.replace("/manager/order/order_read_goods_request.php?reserve_no=<?=$reserve_no?>&order_goods_no=<?=$order_goods_no?>");
</script>
<?

		}
		exit();
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


<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script language="javascript">

	function js_applicate_delivery_price(){
		var frm = document.frm;
		//frm.order_goods_no.value=order_goods_no;
		frm.mode.value="APPLICATE_DELIVERY_PRICE";
		frm.method="POST";
		frm.action = "<?=$_SERVER['PHP_SELF']?>";
		frm.submit();
	}

	function js_goods_request(order_goods_no, goods_no, order_qty_name, qty, is_selected, is_window_closing) { 

		var frm = document.frm;
		
		frm.order_goods_no.value = order_goods_no;
		frm.goods_no.value = goods_no;
		frm.is_selected.value = is_selected;

		var order_qty = 0;
		if(qty != "")
			order_qty = qty;
		else 
			order_qty = $("input[name="+order_qty_name+"]").val();

		frm.order_qty.value = order_qty;

		var ct = document.getElementsByName('cp_type');

		if(order_qty == "0") {
			alert('발주낼 수량이 없습니다.');
			$("input[name=btn_request]").show();
			return;

		} else if(ct.length != 0) { 
				if(frm.cp_type.value == "" || frm.cp_type.value == "0") { 
				alert('공급사가 없습니다.');
				$("input[name=btn_request]").show();
				return;
			}
		} 

		frm.is_window_closing.value = is_window_closing;

		frm.mode.value = "FROM_ORDER";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER['PHP_SELF']?>";
		frm.submit();

	}

</script>
</head>

<body id="popup_file">

<div id="popupwrap_file">
	<h1>발주 관리</h1>
	<div id="postsch_file">
		<form name="frm" method="post">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="reserve_no" value="<?=$reserve_no?>">
		<input type="hidden" name="order_goods_no" value="<?=$order_goods_no?>">
		<input type="hidden" name="goods_no" value="">
		<input type="hidden" name="order_qty" value="">
		<input type="hidden" name="is_selected" value="">
		<input type="hidden" name="is_window_closing" value="">
		
				<h2>* 주문 상품</h2>
		<table cellpadding="0" cellspacing="0" class="colstable02" style="width:98%" border="0">
		<colgroup>
			<col width="12%" />
			<col width="*" />
			<col width="8%" />
			<col width="15%" />
			<col width="8%" />
			<col width="15%" />
			<col width="8%" />
		</colgroup>

		<?
			$nCnt = 0;
			$total_sum_price = 0;
			$sum_qty = 0;
			
			if (sizeof($arr_rs) > 0) {
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
					$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
					$ON_UID						= trim($arr_rs[$j]["ON_UID"]);
					$MEM_NO						= trim($arr_rs[$j]["MEM_NO"]);
					$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
					$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
					$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
					$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
					
					$GOODS_NAME					= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
					$GOODS_SUB_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);

					$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
					$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
					$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
					$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
					
					$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
					$SUM_PRICE					= trim($arr_rs[$j]["SUM_PRICE"]);
					$PLUS_PRICE					= trim($arr_rs[$j]["PLUS_PRICE"]);
					$GOODS_LEE					= trim($arr_rs[$j]["LEE"]);
					$QTY						= trim($arr_rs[$j]["QTY"]);
					$REQ_DATE					= trim($arr_rs[$j]["PAY_DATE"]);
					$END_DATE					= trim($arr_rs[$j]["FINISH_DATE"]);
					$ORDER_STATE				= trim($arr_rs[$j]["ORDER_STATE"]);
					$DELIVERY_CP				= trim($arr_rs[$j]["DELIVERY_CP"]);
					$DELIVERY_NO				= trim($arr_rs[$j]["DELIVERY_NO"]);
					$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);

					$G_CATE_03					= trim($arr_rs[$j]["G_CATE_03"]); //상품의 공급사
					$STOCK_CNT					= trim($arr_rs[$j]["STOCK_CNT"]);
					$FSTOCK_CNT					= trim($arr_rs[$j]["FSTOCK_CNT"]);
					//$TSTOCK_CNT					= trim($arr_rs[$j]["TSTOCK_CNT"]);
					$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);

					$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]); 
					$DELIVERY_CNT_IN_BOX_G		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX_G"]); 
					
					$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);

					//작업이 있을 경우 작업 부자재에 대한 발주 추가 2017-04-21
					$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
					$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);

					$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);

					$arr_rs_sub = selectGoodsSubDetail($conn, $GOODS_NO);

					$sum_req_qty = chkSumOrderGoodsNo($conn, $ORDER_GOODS_NO, $GOODS_NO);

					

				?>

		<tr>
			<th>주문상품명</th>
			<!--상품의 공급사가 없을 경우 공급사를 선택하게 해서 입력, 발주때문에 필요 / 재확인 2017-02-24-->
			<? if($G_CATE_03 <> "") { ?>
				<td class="modeual_nm line" colspan="8">[<?= $GOODS_CODE?>] <?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?>
					
				</td>
			<? } else { ?>
				<td class="modeual_nm line">[<?= $GOODS_CODE?>] <?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?></td>
				<th>공급사</th>
				<td class="line">
					<input type="text" class="autocomplete_off" style="width:90%" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$G_CATE_03)?>" />
					<input type="hidden" name="cp_type" value="<?=$G_CATE_03?>">

					<script>
						$(function(){

							$("input[name=txt_cp_type]").keydown(function(e){

								if(e.keyCode==13) { 

									var keyword = $(this).val();
									if(keyword == "") { 
										$("input[name=cp_type]").val('');
										js_search();
									} else { 
										$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
											if(data.length == 1) { 
												
												js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

											} else if(data.length > 1){ 
												NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

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

							});
						}
					</script>
				</td>
				<th>추가이름/모델명</th>
				<td class="line">
					<input type="text" name="sub_name" class="txt" value="" style="width:70%;"/>
				</td>
				<th>가격</th>
				<td class="line">
					<input type="text" name="sub_buy_price" class="txt" value="" style="width:85px;"/>
				</td>
			<? } ?>
		</tr>
		<tr>
			<th>가입고</th>
			<td class="line"><?=$FSTOCK_CNT?></td>
			<th>정상재고</th>
			<td class="line"><?=$STOCK_CNT?></td>
			<th>선출고</th>
			<td class="line"><?=-$TSTOCK_CNT?></td>
			<th>가용재고</th>
			<td class="line"><?=$FSTOCK_CNT - $TSTOCK_CNT + $STOCK_CNT?></td>
		</tr>
		<tr>
			<th>박스입수<br/>(상품/주문)</th>
			<td class="line"><?=$DELIVERY_CNT_IN_BOX_G?>/<?=$DELIVERY_CNT_IN_BOX?></td>
			<th>주문수량</th>
			<td class="line">
				<?	
					if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8") 
						echo (-1 * $QTY);
					else
						echo $refund_able_qty;
				?>
			</td>
			<? if(sizeof($arr_rs_sub) == 0) { ?>
			<th>발주수량</th>
			<td class="line">
				
				<? if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8") { ?>
					<input type="text" name="req_qty" class="txt" value="<?=-$QTY - $sum_req_qty?>" style="width:70%;"/>
				<? } else { ?>
					<input type="text" name="req_qty" class="txt" value="<?=$refund_able_qty - $sum_req_qty?>" style="width:70%;"/>
				<? } ?>
			</td>
			<td class="line" colspan="2">
				

			</td>

			<td class="line">
				
				<? if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8") { ?>
						<input type="button" name="btn_request" value=" 발주 " class="btntxt" onclick="this.style.display='none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$GOODS_NO?>', 'req_qty', '<?=-$QTY?>', 'Y', 'Y');">
				<? } else { ?>
					<? if($refund_able_qty - $sum_req_qty > 0) { ?>
						<input type="button" name="btn_request" value=" 발주 " class="btntxt" onclick="this.style.display='none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$GOODS_NO?>', 'req_qty', '', 'Y', 'Y');">
					<? } ?>
				<? } ?>

			</td>
			<? } else {?>
			<td class="line" colspan="5"></td>
			<? } ?>
		</tr>
		<tr>
			<th>상품비고</th><td class="line" colspan="8"><?=getGoodsMemo($conn, $GOODS_NO)?></td>
		</tr>
			<?
			}
		}
		?>
		</table>

		<? if($G_CATE_03 <> "" && sizeof($arr_rs_sub) <= 0) { ?>
		<? 
			$arr_buy_company = listGoodsBuyCompany($conn, $GOODS_NO);
		?>
		<div class="sp10"></div>
		<h2>* 공급사 발주 정보 체크</h2>
		<table cellpadding="0" cellspacing="0" class="colstable02" style="width:98%" border="0">
			<col width="2%" />
			<col width="30%" />
			<col width="10%" />
			<col width="40%" />
			<col width="18%" />
			<thead>
				<tr>
					<th></th>
					<th>업체명</th>
					<th>매입가</th>
					<th>비고</th>
					<th>택배비</th>
				</tr>
			</thead>
			<tbody>
				<?
					//2017-09-08 주문당시 공급가가 아닌 현재 상품 공급가 가져오기
					// $BUY_PRICE = getGoodsBuyPrice($conn, $GOODS_NO);  //2020-07-02 TBL_ORDER_GODOS의 공급가로 받아오기 위해 막음
				?>
				<tr>
					<td class="line"><input type="radio" name="selected_buy_info" checked="checked" value="<?=$G_CATE_03?>|<?=$BUY_PRICE?>"/></td>
					<td class="line"><?=getCompanyName($conn, $G_CATE_03)?></td>
					<td class="price line"><?=number_format($BUY_PRICE)?>원</td>
					<td class="modeual_nm line">현 예상 발주 가격</td>
					<td class="price line"><input type='text' id="txtDeliveryPrice" name='txtDeliveryPrice' value="<?=number_format($DELIVERY_PRICE)?>" style="width:70px;"><input type='button' onclick="js_applicate_delivery_price()" value="변경"></td>
				</tr>
				<input type="hidden" name="delivery_cnt_in_box" value="<?=$DELIVERY_CNT_IN_BOX?>">
				
				<? 
					for($k = 0; $k < sizeof($arr_buy_company); $k++) {
						
						$ab_BUY_CP_NO		= $arr_buy_company[$k]["BUY_CP_NO"];
						$ab_BUY_PRICE		= $arr_buy_company[$k]["BUY_PRICE"];
						$ab_MEMO			= $arr_buy_company[$k]["MEMO"];
						$ab_BUY_CP_NAME	= $arr_buy_company[$k]["BUY_CP_NAME"];
				?>
				<tr>
					<td><input type="radio" name="selected_buy_info" value="<?=$ab_BUY_CP_NO?>|<?=$ab_BUY_PRICE?>"/></td>
					<td class="line"><?=$ab_BUY_CP_NAME?></td>
					<td class="price line"><?=number_format($ab_BUY_PRICE)?>원</td>
					<td class="modeual_nm line"><?=$ab_MEMO?></td>
				</tr>
				<?
					}
			
				?>
			</tbody>
		</table>
		<? } ?>

		<div class="sp10"></div>
		<h2>* 작업 부자재</h2>
		<table cellpadding="0" cellspacing="0" class="colstable02" style="width:98%" border="0">
		<colgroup>
			<col width="10%" />
			<col width="*" />
			<col width="8%" />
			<col width="15%" />
			<col width="8%" />
			<col width="15%" />
			<col width="8%" />
		</colgroup>
		<?
			if($OPT_WRAP_NO > 0 || $OPT_STICKER_NO > 0) { 
		?>
		<?		if($OPT_WRAP_NO > 0) { ?>
		<tr>
			<th rowspan="2">포장지</th>
			<td class="modeual_nm line" colspan="6">
				<?=getGoodsName($conn, $OPT_WRAP_NO)?>
			</td>
			<th>발주수량</th>
			<td class="line">
				<input type="text" name="req_qty_wrap" class="txt" value="<?=$refund_able_qty?>" style="width:70%;"/>
			</td>
			<td class="line" colspan="2">
				<input type="button" name="b" value="발주" class="btntxt" onclick="this.style.display = 'none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$OPT_WRAP_NO?>', 'req_qty_wrap', '', 'N', 'N');">
			</td>
		</tr>
		<tr>
			<th>비고</th><td colspan="9" class="line"><?=getGoodsMemo($conn, $OPT_WRAP_NO)?></td>
		</tr>
		<?		} ?>
		<?		if($OPT_STICKER_NO > 0) { ?>
		<tr>
			<th rowspan="2">스티커</th>
			<td class="modeual_nm line" colspan="6">
				<?=getGoodsName($conn, $OPT_STICKER_NO)?>
			</td>
			<th>발주수량</th>
			<td class="line">
				<input type="text" name="req_qty_sticker" class="txt" value="<?=$refund_able_qty?>" style="width:70%;"/>
			</td>
			<td class="line" colspan="2">
				<input type="button" name="b" value="발주" class="btntxt" onclick="this.style.display = 'none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$OPT_STICKER_NO?>', 'req_qty_sticker', '', 'N', 'N');">
			</td>
		</tr>
		<tr>
			<th>비고</th><td colspan="9" class="line"><?=getGoodsMemo($conn, $OPT_STICKER_NO)?></td>
		</tr>
		<?		} ?>
		<? } else { ?>
		<tr>
			<td height="30" colspan="10" style="text-align:center;">발주할 작업 부자재가 없습니다.</td>
		</tr>
		<? } ?>
		</table>

		<div class="sp10"></div>
		<h2>* 구성 상품</h2>
		<table cellpadding="0" cellspacing="0" class="rowstable" style="width:98%" border="0">
			<colgroup>
				<col width="7%"/>
				<col width="*" />
				<col width="4%"/>
				<col width="4%"/>
				<col width="4%"/>
				<col width="6%"/>
				<col width="6%"/>
				<col width="6%"/>
				<col width="4%"/>
				<col width="18%"/>
				<col width="6%"/>
				<col width="6%"/>
			</colgroup>
			<tr>
				<th colspan="4"></th>
				<th colspan="4">재고량</th>
				<th colspan="4">발주</th>
			</tr>
			<tr>
				<th>코드</th>
				<th>상품명</th>
				<th>구성<br/>수량</th>
				<th>주문량</th>
				<th>가입고</th>
				<th>정상<br/>재고</th>
				<th>선출고</th>
				<th>가용<br/>재고</th>
				<th>박스<br/>입수</th>
				<th colspan="2">공급사 / 매입가</th>
				<th class="end">수량</th>
			</tr>
			<?
				if(sizeof($arr_rs_sub) > 0) {
					for($i = 0; $i < sizeof($arr_rs_sub); $i++)
					{
						
						$goods_sub_no				= $arr_rs_sub[$i]["GOODS_SUB_NO"];
						$goods_cate					= $arr_rs_sub[$i]["GOODS_CATE"];
						$sub_goods_code				= $arr_rs_sub[$i]["GOODS_CODE"];
						$sub_goods_name				= $arr_rs_sub[$i]["GOODS_NAME"];
						$sub_goods_sub_name			= $arr_rs_sub[$i]["GOODS_SUB_NAME"];
						$sub_cp_type				= $arr_rs_sub[$i]["CATE_03"];
						$sub_cp_nm					= $arr_rs_sub[$i]["CP_NM"];
						$sub_buy_price				= $arr_rs_sub[$i]["BUY_PRICE"];
						$sub_goods_cnt				= $arr_rs_sub[$i]["GOODS_CNT"];
						$sub_fstock					= $arr_rs_sub[$i]["FSTOCK_CNT"];
						$sub_stock					= $arr_rs_sub[$i]["STOCK_CNT"];
						$sub_delivery_cnt_in_box	= $arr_rs_sub[$i]["DELIVERY_CNT_IN_BOX"];

						$sum_req_qty = chkSumOrderGoodsNo($conn, $ORDER_GOODS_NO, $goods_sub_no);

						if($ORDER_STATE == "6" || $ORDER_STATE == "7" || $ORDER_STATE == "8")
							$total_qty = $sub_goods_cnt * -$QTY;
						else
							$total_qty = $sub_goods_cnt * $refund_able_qty;

						$sub_tstock = getCalcGoodsInOrdering($conn, $goods_sub_no);

						//아웃박스 일경우 박스입수로 나누기
						if(startsWith($goods_cate, '010202')) { 
							$total_qty = ceil($total_qty / $DELIVERY_CNT_IN_BOX);
							//아웃박스 잔여계산 오류로 수정 2017-10-12
							//$sum_req_qty = ceil($sum_req_qty / $DELIVERY_CNT_IN_BOX);
						}
						
						$sub_memo = getGoodsMemo($conn, $goods_sub_no);

			?>
			<tr title="<?=$sub_memo?>">
				<td><?=$sub_goods_code?></td>
				<td><?=$sub_goods_name?> <?=$sub_goods_sub_name?>
					<?
						if($sub_memo <> "")
							echo " <span style='color:red;'>※</span>";
					?>
				</td>
				
				<td class="price"><span class="sub_goods_cnt" data-goods_cnt="<?=$sub_goods_cnt ?>"><?=$sub_goods_cnt ?></span></td>
				<td class="price"><?=number_format($total_qty)?></td>
				<td class="price"><?=number_format($sub_fstock)?></td>
				<td class="price"><?=number_format($sub_stock)?></td>
				<td class="price"><?=-number_format($sub_tstock)?></td>
				<td class="price"><b><?=number_format($sub_stock - $sub_tstock + $sub_fstock)?></b></td>
				<td class="price"><?=number_format($sub_delivery_cnt_in_box)?></td>
				
				<td class="price" colspan="2" style="padding-bottom:0px;">
					<table style="width:100%;">
						<colgroup>
							<col width="*"/>
							<col width="20%"/>
							<col width="10%"/>
						</colgroup>
						<tr height="30">
							<td><?=$sub_cp_nm?></td>
							<td><?=number_format($sub_buy_price)?></td>
							<td>
								<? if($total_qty - $sum_req_qty != 0) { ?>
								<input type="radio" name="selected_buy_info" value="<?=$sub_cp_type?>|<?=$sub_buy_price?>" onclick="this.style.display = 'none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$goods_sub_no?>', 'req_qty_<?=$i?>', '', 'Y', 'N');"/>
								<? } ?>
							</td>
						</tr>
						<?
						$arr_buy_company = listGoodsBuyCompany($conn, $goods_sub_no);
						for($k = 0; $k < sizeof($arr_buy_company); $k++) {
						
						$ab_BUY_CP_NO		= $arr_buy_company[$k]["BUY_CP_NO"];
						$ab_BUY_PRICE		= $arr_buy_company[$k]["BUY_PRICE"];
						$ab_MEMO			= $arr_buy_company[$k]["MEMO"];
						$ab_BUY_CP_NAME	= $arr_buy_company[$k]["BUY_CP_NAME"];
					?>
						<tr height="30">
							<td><?=$ab_BUY_CP_NAME?></td>
							<td><?=number_format($ab_BUY_PRICE)?></td>
							<td>
								<? if($total_qty - $sum_req_qty != 0) { ?>
								<input type="radio" name="selected_buy_info" value="<?=$ab_BUY_CP_NO?>|<?=$ab_BUY_PRICE?>" onclick="this.style.display = 'none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$goods_sub_no?>', 'req_qty_<?=$i?>', '', 'Y', 'N');"/>
								<? } ?>
							</td>
						</tr>
					<? } ?>
					</table>
				</td>
				<td><input type="text" name="req_qty_<?=$i?>" class="txt" value="<?=$total_qty - $sum_req_qty?>" style="width:70%;"/></td>
				<!--<td>
					<? if($total_qty - $sum_req_qty != 0) { ?>
					<input type="button" name="b" value="발주" class="btntxt" onclick="this.style.display = 'none'; js_goods_request('<?=$ORDER_GOODS_NO?>', '<?=$goods_sub_no?>', 'req_qty_<?=$i?>', '', 'Y', 'N');">
					<? } ?>
				</td>-->
			</tr>
			<?
					}
				} else {
			?>
			<tr>
				<td height="30" colspan="10">구성품이 없습니다</td>
			</tr>
			<?
				}
			?>
		</table>

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