<?  require "_common/home_pre_setting.php"; ?>
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
		$query="SELECT	O.RESERVE_NO, DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS REG_DATE, OG.GOODS_CODE, OG.GOODS_NO, OG.GOODS_NAME, OG.ORDER_STATE, OG.GOODS_CODE
						, OG.SALE_PRICE, OG.QTY
						, O.R_MEM_NM, O.R_PHONE, O.R_HPHONE, O.R_ADDR1, O.MEMO, G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
						, OG.DELIVERY_CP
						, OG.DELIVERY_NO
						, OG.OPT_STICKER_NO
						, OG.OPT_PRINT_MSG
						, G.DELIVERY_PRICE
						, G.GOODS_NO
				FROM	TBL_ORDER	O 
				JOIN	TBL_ORDER_GOODS OG ON OG.RESERVE_NO=O.RESERVE_NO
				JOIN	TBL_GOODS G ON OG.GOODS_NO=G.GOODS_NO
				WHERE	OG.RESERVE_NO='".$reserveNo."'
				AND		O.MEM_NO='".$memberNo."'
				AND		O.DEL_TF='N'
				AND		O.USE_TF='Y'
				AND		OG.DEL_TF='N'
				AND		OG.USE_TF='Y'
		";

		echo $query;

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
?>
<?//PHP_PROCESS_ZONE



	// echo "orderGoodsNo : 	".$orderGoodsNo."<br>";
	// echo "memberNo	:	".$memberNo."<br>";
	// exit;
	$memberNo=$_SESSION["C_MEM_NO"];
	$memberId=$_SESSION["C_MEM_ID"];
	$orderInfos=getOrderDetailByReserveNo($conn, $memberNo, $reserveNo);

	// print_r($orderInfos);
	// exit;
	$cnt=sizeof($orderInfos);

	// if($cnt>0){
	// 	for($i=0; $i<$cnt; $i++){
	// 		$imgPahthUrl=getGoodsImge($orderInfos[$i]["FILE_NM_100"], $orderInfos[$i]["IMG_URL"], $orderInfos[$i]["FILE_PATH_150"], $orderInfos[$i]["FILE_RNM_150"], 200, 200);
	// 	}
	// }

	// $imgPathUrl=getGoodsImage($orderInfos[0]["FILE_NM_100"], $orderInfos[0]["IMG_URL"], $orderInfos[0]["FILE_PATH_150"], $orderInfos[0]["FILE_RNM_150"], 200, 200);

	// echo "ImgUrl : ".$imgPathUrl."<br>";



?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./header.php"; ?>

	<script>
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
										$DELIVERY_PRICE	=   $orderInfos[$i]["DELIVERY_PRICE"];

										$GOODS_PRICE	= 	$SALE_PRICE * $QTY;

										$TOTAL_PRICE	= 	($SALE_PRICE * $QTY) + $DELIVERY_PRICE;

										$DELIVERY_NO	=   $orderInfos[$i]["DELIVERY_NO"];
										$DELIVERY_NO   	=   $orderInfos[$i]["DELIVERY_NO"];
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
													<table>
														<td style="width:120% !important; text-align: left;">
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
														<td style="width:100px; !important; text-align: center;">배송비 <br><br> <?=number_format($DELIVERY_PRICE)?>원</td>
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
													<div class="button_order_disabled">배송조회</div>
												<?
                                                }
                                                ?>
											<div class="button_order_rel_02">교환, 반품 신청</div>
											<div class="button_order_rel_03">리뷰작성</div>
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
							</div>
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