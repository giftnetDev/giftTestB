<?  require "../_common/home_pre_setting.php"; ?>
<?//FUNCTION_DEFINITION_ZONE
    // function getOrderDetailOrderGoodsNo($db, $memberNo, $orderGoodsNo){
	// 	$query="SELECT	O.RESERVE_NO, DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS REG_DATE, OG.GOODS_CODE, OG.GOODS_NO, OG.GOODS_NAME, OG.ORDER_STATE, OG.GOODS_CODE
	// 					, OG.SALE_PRICE, OG.QTY
	// 					, O.R_MEM_NM, O.R_PHONE, O.R_HPHONE, O.R_ADDR1, O.MEMO, G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
	// 			FROM	TBL_ORDER	O 
	// 			JOIN	TBL_ORDER_GOODS OG ON OG.RESERVE_NO=O.RESERVE_NO
	// 			JOIN	TBL_GOODS G ON OG.GOODS_NO=G.GOODS_NO
	// 			WHERE	OG.ORDER_GOODS_NO='".$orderGoodsNo."'
	// 			AND		O.MEM_NO='".$memberNo."'
	// 			AND		O.DEL_TF='N'
	// 			AND		O.USE_TF='Y'
	// 			AND		OG.DEL_TF='N'
	// 			AND		OG.USE_TF='Y'
	// 			";

	// 	// echo $query."<br>";		
	// 	// exit;

	// 	$result=mysql_query($query, $db);
	// 	$record=array();
	// 	if($result<>""){
	// 		$record[0]=mysql_fetch_assoc($result);
	// 	}
	// 	return $record;
	// }//end of function


	function getOrderDetailByReserveNo($db, $memberNo, $reserveNo){


		$query="SELECT *
				FROM(
					SELECT	O.RESERVE_NO, DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS REG_DATE, OG.GOODS_NO
							, OG.GOODS_NAME, OG.GOODS_CODE, OG.ORDER_STATE
							, OG.SALE_PRICE, OG.QTY, OG.ORDER_GOODS_NO
							, O.R_MEM_NM, O.R_PHONE, O.R_HPHONE, O.R_ADDR1, O.MEMO, G.FILE_NM_100
							, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
							, OG.DELIVERY_CP
							, OG.DELIVERY_NO
							, OG.OPT_STICKER_NO
							, OG.OPT_PRINT_MSG
							, G.DELIVERY_PRICE
					FROM	TBL_ORDER	O 
					JOIN	TBL_ORDER_GOODS OG ON OG.RESERVE_NO=O.RESERVE_NO
					JOIN	TBL_GOODS G ON OG.GOODS_NO=G.GOODS_NO
					WHERE	OG.RESERVE_NO	=	'$reserveNo'
					AND		O.MEM_NO		=	'$memberNo'
					AND		O.DEL_TF		=	'N'
					AND		O.USE_TF		=	'Y'
					AND		OG.DEL_TF		=	'N'
					AND		OG.USE_TF		=	'Y'
					AND		OG.GROUP_NO		=	0
					AND		OG.CATE_04 		!= 	'CHANGE'

					UNION

					SELECT	O.RESERVE_NO, DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS REG_DATE, OG.GOODS_NO
							, OG.GOODS_NAME, OG.GOODS_CODE, MAX(OG.ORDER_STATE) AS ORDER_STATE
							, OG.SALE_PRICE, OG.QTY, OG.ORDER_GOODS_NO
							, O.R_MEM_NM, O.R_PHONE, O.R_HPHONE, O.R_ADDR1, O.MEMO, G.FILE_NM_100
							, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
							, OG.DELIVERY_CP
							, OG.DELIVERY_NO
							, OG.OPT_STICKER_NO
							, OG.OPT_PRINT_MSG
							, G.DELIVERY_PRICE
					FROM	TBL_ORDER	O 
					JOIN	TBL_ORDER_GOODS OG ON OG.RESERVE_NO=O.RESERVE_NO
					JOIN	TBL_GOODS G ON OG.GOODS_NO=G.GOODS_NO
					WHERE	OG.RESERVE_NO	=	'$reserveNo'
					AND		O.MEM_NO		=	'$memberNo'
					AND		O.DEL_TF		=	'N'
					AND		O.USE_TF		=	'Y'
					AND		OG.DEL_TF		=	'N'
					AND		OG.USE_TF		=	'Y'
					AND		OG.GROUP_NO		!=	0
					GROUP BY OG.GROUP_NO

				) S
				ORDER BY S.REG_DATE DESC

		";

		// echo $query;
		// exit;

		$result=mysql_query($query, $db);
		$record=array();
		$cnt=0;
		if($result<>""){
			$cnt=mysql_num_rows($result);
		}
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
		}
		return $record;
	}

	function getStickerNameByNo($db, $stickerNo){
        $query="SELECT GOODS_NAME, GOODS_SUB_NAME
                FROM TBL_GOODS
                WHERE GOODS_CATE LIKE'0103%'
                AND GOODS_NO='$stickerNo'
                AND USE_TF='Y'
                AND DEL_TF='N'
        ";

        // echo "query : ".$query."<br>";
        // exit;
        $result= mysql_query($query, $db);
        $rows="";
        if($result<>""){
            $rows=mysql_fetch_row($result);
        }
        return $rows[0];
            
    }//end of fucntion
	function deleteOrderGoodsHomepage2($db, $order_goods_no, $del_adm) {

		$query="UPDATE TBL_ORDER_GOODS SET DEL_TF = 'Y', DEL_ADM = '".$del_adm."', DEL_DATE = now() WHERE ORDER_GOODS_NO = '".$order_goods_no."' ";
		
		#echo $query;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
		
	}
	function resetOrderInforHomepage2($db, $reserve_no) {

		if($reserve_no == "") return false;
		
		$query = "SELECT CATE_01, QTY, PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, DISCOUNT_PRICE, ORDER_STATE
					FROM TBL_ORDER_GOODS
				   WHERE USE_TF = 'Y'
					 AND DEL_TF = 'N'
					 AND RESERVE_NO = '$reserve_no' ";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();
			
		$tmp_order_state = "";

		$total_qty = 0;
		$total_price = 0;
		$total_buy_price = 0;
		$total_sale_price = 0;
		$total_extra_price = 0;
		$total_discount_price = 0;

		$is_all_completed = true;

		for($i=0 ; $i< $total ; $i++) {

			mysql_data_seek($result,$i);

			$row     = mysql_fetch_array($result);

			$RS_CATE_01				= Trim($row["CATE_01"]);
			$RS_QTY					= Trim($row["QTY"]);
			$RS_PRICE				= Trim($row["PRICE"]);
			$RS_BUY_PRICE			= Trim($row["BUY_PRICE"]);
			$RS_SALE_PRICE			= Trim($row["SALE_PRICE"]);
			$RS_EXTRA_PRICE			= Trim($row["EXTRA_PRICE"]);
			$RS_DISCOUNT_PRICE		= Trim($row["DISCOUNT_PRICE"]);
			$RS_ORDER_STATE			= Trim($row["ORDER_STATE"]);

			//2016-10-11 증정, 샘플은 주문금액 합산에서 아예 제외, 2016-12-21 샘플, 증정 주문서 금액에 다시 추가
			/*
			if($RS_CATE_01 <> "") { 
				$RS_SALE_PRICE = 0;
				$RS_DISCOUNT_PRICE = 0;
				$RS_EXTRA_PRICE = 0;
				$RS_PRICE = 0;
				$RS_BUY_PRICE = 0;

			}
			*/
			if($RS_ORDER_STATE != "3")
				$is_all_completed = false;

			if (($RS_ORDER_STATE == "0") || ($RS_ORDER_STATE == "1") || ($RS_ORDER_STATE == "2") || ($RS_ORDER_STATE == "3")) {
				$total_qty = $total_qty + $RS_QTY;
				$total_price = $total_price + ($RS_PRICE * $RS_QTY);
				$total_buy_price = $total_buy_price + ($RS_BUY_PRICE * $RS_QTY);
				$total_sale_price = $total_sale_price + ($RS_SALE_PRICE * $RS_QTY);
				$total_extra_price = $total_extra_price + ($RS_EXTRA_PRICE * $RS_QTY);
				$total_discount_price = $total_discount_price + $RS_DISCOUNT_PRICE;
			} else if ($RS_ORDER_STATE == "4") {
				$total_qty = $total_qty;
				$total_price = $total_price;
				$total_buy_price = $total_buy_price;
				$total_sale_price = $total_sale_price;
				$total_extra_price = $total_extra_price;
				$total_discount_price = $total_discount_price;
			} else {
				$total_qty = $total_qty - $RS_QTY;
				$total_price = $total_price - ($RS_PRICE * $RS_QTY);
				$total_buy_price = $total_buy_price - ($RS_BUY_PRICE * $RS_QTY);
				$total_sale_price = $total_sale_price - ($RS_SALE_PRICE * $RS_QTY);
				$total_extra_price = $total_extra_price - ($RS_EXTRA_PRICE * $RS_QTY);
				$total_discount_price = $total_discount_price - $RS_DISCOUNT_PRICE;
			}

			if ($i == 0) {
				$tmp_order_state = $RS_ORDER_STATE;
			} else {
				$tmp_order_state .= ",".$RS_ORDER_STATE;
			}
		}

		//echo $total_sale_price."<br/>";
		
		$up_query = "UPDATE TBL_ORDER 
						SET ORDER_STATE = '$tmp_order_state', 
							TOTAL_PRICE = '$total_price',
							TOTAL_BUY_PRICE = '$total_buy_price',
							TOTAL_SALE_PRICE = '$total_sale_price',
							TOTAL_EXTRA_PRICE = '$total_extra_price',
							TOTAL_DISCOUNT_PRICE = '$total_discount_price',
							TOTAL_QTY = '$total_qty' ";
		if(!$is_all_completed) { 
			$up_query .= ", FINISH_DATE = null
						  , DELIVERY_DATE = null
						 ";
		}
		
		$up_query .=" WHERE RESERVE_NO = '$reserve_no' 
						AND USE_TF = 'Y'
						AND DEL_TF = 'N' ";
		
		if(!mysql_query($up_query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}
?>
<?//PHP_PROCESS_ZONE


	print_r($_POST);
	// exit;

	$reserveNo=$_POST['sel_reserve_no'];
	// echo "reserve_no : ".$reserveNo."<br>";
	// exit;
	// echo "orderGoodsNo : 	".$orderGoodsNo."<br>";
	// echo "memberNo	:	".$memberNo."<br>";
	// exit;
	$memberNo=$_SESSION["C_MEM_NO"];
	$memberId=$_SESSION["C_MEM_ID"];
	$orderInfos=getOrderDetailByReserveNo($conn, $memberNo, $reserveNo);

	// print_r($orderInfos);
	// exit;
	$cnt=sizeof($orderInfos);


	if($doc_mode=="CANCEL_ORDER_GOODS"){

		$reserveNo=$_POST['sel_reserve_no'];
		$orderGoodsNo=$_POST['sel_order_goods_no'];
		echo "reserveNo : ".$reserveNo."<br>";
		echo "orderGoodsNo : ".$orderGoodsNo."<br>";
		exit;
		$result0=deleteOrderGoodsHomepage2($conn, $orderGoodsNo, '');
		$result0=resetOrderInforHomepage2($conn, $reserveNo);
	}



?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./header.php"; ?>

	<script>
		function js_cancel_order_goods(reserveNo, orderGoodsNo){
			if(!confirm("취소하시면 헌재 상품이 삭제됩니다")){ 
				return;
			}
			var frm = document.doc_frm;
			frm.doc_mode.value="CANCEL_ORDER_GOODS";
			frm.sel_order_goods_no.value=orderGoodsNo;
			frm.sel_reserve_no.value=reserveNo;
			frm.action="<?=$_SERVER['PHP_SELF']?>";
			frm.target="";
			frm.submit();
		}
		function js_joomonList()
		{
			//history.back();
			location.href="delivery_confirm.php";
        }

		function js_delivery_pop(delivery_cd, delivery_no)
		{
			var url = "pop_delivery_trace.php?delivery_cp=" + delivery_cd + "&delivery_no=" + delivery_no;
			window.open(url, "pop_delivery_trace", "width=10, height=10, top=50, left=0");
		}
		function js_joomoonDel(reserve_no)
		{
			if (!confirm("삭제시 주문한 전체 상품의 결제내역을 복구 할 수 없습니다.\n\n삭제 하시겠습니까?")) return;	

			$.ajax({
				url:"./ajax/ajax_process.php",
				dataType:'JSON',
				type:"POST",
				data:{
						  mode : "JOOMOON_DEL"
						, reserve_no:reserve_no
						, memberId:"<?=$memberId?>"
				},
				success:function(data){
					alert("삭제 되었습니다.");
					js_joomonList();
				},
				error:function(jqXHR, textStatus, errorThrown){
					alert("error");
					return;
				}
			});
		}
    </script>

    </head>
    <body>
        <div class="wrap">
			<form name="doc_frm">
				<input type="hidden" name="doc_mode">
				<input type="hidden" name="sel_order_goods_no">
				<input type="hidden" name="sel_reserve_no">
			</form>
            <? require "./top.php"; ?>
    <!-------------------------------BEGIN_Main Contents-------------------------------------->
			<div class="detail_page">
				<div class="detail_page_inner">
					<div class="cart_info">

						<h4>주문상세</h4>
						<div class="order_list">
							<b><?=$orderInfos[0]["REG_DATE"]?> 주문 <br>주문번호 <?=$orderInfos[0]["RESERVE_NO"]?></b><br>
							<?
								if($cnt>0){
									$FINAL_AMOUNT = 0;
									$DELIVERY_TOT = 0;
									$GOODS_TOT 	  = 0;
									for($i=0 ; $i< $cnt; $i++){
										
										$imgPathUrl=getGoodsImage($orderInfos[$i]["FILE_NM_100"], $orderInfos[$i]["IMG_URL"], $orderInfos[$i]["FILE_PATH_150"], $orderInfos[$i]["FILE_RNM_150"], 200, 200);

										$GOODS_NO		=   $orderInfos[$i]["GOODS_NO"];
										$SALE_PRICE		=   $orderInfos[$i]["SALE_PRICE"];
                                		$QTY			=   $orderInfos[$i]["QTY"];
										//$DELIVERY_PRICE	=   $orderInfos[$i]["DELIVERY_PRICE"];	배송비제외

										$GOODS_PRICE	= 	$SALE_PRICE * $QTY;

										$TOTAL_PRICE	= 	($SALE_PRICE * $QTY); //+ $DELIVERY_PRICE;

										$DELIVERY_NO	=   $orderInfos[$i]["DELIVERY_NO"];
										$DELIVERY_NO   	=   $orderInfos[$i]["DELIVERY_NO"];
										$ORDER_STATE	=	$orderInfos[$i]["ORDER_STATE"];
										$ORDER_GOODS_NO	=	$orderInfos[$i]["ORDER_GOODS_NO"];
									?>
										<div class="order_box">
											<!--<h4>2021.04.20<b>4/21(수) 도착</b></h4>-->
											<h4><?=$orderInfos[$i]["GOODS_NAME"]?></b></h4>
												<div>
													<a href="sub_detail.php?goods_no=<?=$GOODS_NO?>">
														<img  src="<?=$imgPathUrl?>" width='100px'>
													</a>
												</div>
												<div class="order_box_dt">
													<table style="width:90%;">
														<colgroup>
															<col width="500px">
															<col width="100px">
															<col width="100px">
															<col width="100px">
															<col width="100px">

														</colgroup>
														<td style="text-align: left; overflow:hidden;">
															<?
																if($orderInfos[$i]["OPT_STICKER_NO"]>0 || $orderInfos[$i]["OPT_PRINT_MSG"]){
																	?>
																		<i>- 옵션 -</i></br>
																	<?
																	$optStickerNo=$orderInfos[$i]["OPT_STICKER_NO"];
																	$optPrintMsg=SetStringFromDB($orderInfos[$i]["OPT_PRINT_MSG"]);
																	if($optStickerNo>0){
																		$optSticker=SetStringFromDB(getStickerNameByNo($conn, $optStickerNo));
																		?>
																			<i>스티커 옵션 : <?=$optSticker?></i><br>
																		<?
																	}
																	if($optPrintMsg<>""){
																	?>
																		<i>인쇄 옵션 : <?=$optPrintMsg?></i>
																	<?
																	}
																}
																else
																{
																	?>
																		<i> 옵션 : 없음</i></br>
																	<?
																}
															?>
														</td>
														<td style="width:100px; !important; text-align: center;">상품가격 <br><br> <?=number_format($orderInfos[$i]["SALE_PRICE"])?>원</td>
														<td style="width:100px; !important; text-align: center;">수량 <br><br> <?=number_format($orderInfos[$i]["QTY"])?>개</td>
														<!--<td style="width:100px; !important; text-align: center;">배송비 <br><br> <?=number_format($DELIVERY_PRICE)?>원</td>-->
														<td style="width:100px; !important; text-align: center;">배송비 <br><br> 0원</td>
														<td style="width:100px; !important; text-align: center;">구매가 <br><br> <?=number_format($TOTAL_PRICE)?>원</td>
													</table>
												</div>	
																						
											<?
												if($DELIVERY_NO != "")
												{
											?>
													<div class="button_order_rel_01" onclick="js_delivery_pop('<?=$DELIVERY_CP?>','<?=$DELIVERY_NO?>')">배송조회</div>
												<?
                                                }
                                                else
                                                {
												?>	
													<!-- <div class="button_order_disabled">배송조회</div> -->
												<?
                                                }
                                                ?>


											<!-- <div class="button_order_rel_03">리뷰작성</div> -->
										</div>
									<?
									$FINAL_AMOUNT 	+= $TOTAL_PRICE;
									$GOODS_TOT 		+= $GOODS_PRICE;
									$DELIVERY_TOT 	+= $DELIVERY_PRICE;
									}//end of for($i<$cnt);
								}//end of if($cnt>0);
							?>
							<br>
							<b>받는사람 정보</b><br>
							<div class="order_box">
								<div class="order_td_01">받는사람</div>
								<div class="order_td_02"><?=SetStringFromDB($orderInfos[0]["R_MEM_NM"])?></div>
								<div class="order_td_01">연락처</div>
								<div class="order_td_02"><?=$orderInfos[0]["R_HPHONE"]?></div>
								<div class="order_td_01">받는주소</div>
								<div class="order_td_02"><?=SetStringFromDB($orderInfos[0]["R_ADDR1"])?></div>
								<div class="order_td_01">배송요청사항</div>
								<div class="order_td_02"><?=SetStringFromDB($orderInfos[0]["MEMO"])?></div>
							</div><br>
							<b>결제정보</b><br>
							<div class="order_box">
								<div class="order_td_01"></div>
								<div class="order_td_02"></div>
								<div class="order_td_01"></div>
								<div class="order_td_02"></div>
								<div class="order_td_01"></div>
								<div class="order_td_02"></div>
								<div class="total_pr">
									<div>
										<span>총 결제금액</span><br><i><?=number_format($FINAL_AMOUNT)?>원</i>
									</div>
								</div>
							</div>
							<!--<b>결제정보</b><br>
							<div class="order_box">
								<div class="order_td_01">결제수단</div>
								<div class="order_td_02">현대카드</div>
								<div class="order_td_01">배송비</div>
								<div class="order_td_02"><?=number_format($DELIVERY_TOT)?>원</div>
								<div class="order_td_01">총상품가격</div>
								<div class="order_td_02"><?=number_format($GOODS_TOT)?>원</div>
								<div class="total_pr">
									<div>
										<span>총 결제금액</span><br><i><?=number_format($FINAL_AMOUNT)?>원</i>
									</div>
								</div>
							</div><br>
							<b>결제영수증정보</b><br>
							<div class="order_box">
								<div class="order_td_01"><button>신용카드 전표</button></div>
								<div class="order_td_02">해당 주문건에 대해 구매 전표 확인이 가능합니다.</div>
								<div class="order_td_01"><button>거래명세서</button></div>
								<div class="order_td_02">해당 주문건에 대해 거래명세서 확인이 가능합니다.</div>
							</div>-->
							<div class="tcenter">
								<button class="carting" onclick="js_joomonList();">주문목록</button>
								<button class="joomoon" onclick="js_joomoonDel('<?=$reserveNo?>')">주문내역 삭제</button>
							</div>
						</div><!--order_list-->
					</div><!--cart_info-->
				</div><!--detail_page_inner-->
			</div><!--detail_page-->
    <!--------------------------------END_Main Contents--------------------------------------->
            <? require "./footer.php"; ?>
        </div><!--wrap-->
    </body>
</html>