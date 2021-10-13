<?
	require "_common/home_pre_setting.php";


	// print_r($_SESSION);
	// echo "<br><br>";


	function getStickerNameByStickerNo($db, $stickerNo){
		$query="SELECT GOODS_NAME
				FROM	TBL_GOODS
				WHERE GOODS_NO='".$stickerNo."' ; ";

		// echo $query."<br>";
		$result=mysql_query($query, $db);
		if(!$result){
			return "";
		}
		else{
			$rows=mysql_fetch_row($result);
			return $rows[0];
		}
	}
	function deleteGoodsInCart($db, $memberNo, $cartNos){
		$query="DELETE FROM TBL_CART
				WHERE	MEM_NO = '$memberNo'
				AND		CART_NO  IN ($cartNos)
				;
				";
		
		// echo $query."<br>";
		// exit;
		
		$result=mysql_query($query, $db);
		if(!$result){
			echo "<script>alert('Delete Error');</script>";
			return false;
		}
		else{
			return true;
		}
	
	}

	if ($_SESSION['C_MEM_NO'] == "") {

?>
<script type="text/javascript">
	alert('로그인 되어있지 않거나 세션이 만료 되었습니다. 재 로그인 해주세요.');
</script>
<meta http-equiv='Refresh' content='0; URL=/'>
<?
			exit;
	}



?>
<?

	$s_ord_no = get_session('s_ord_no');
	$cp_no = $_SESSION['C_CP_NO'];
	$mem_no=$_SESSION['C_MEM_NO'];

	if ($mode == "DELETE_SELECT") {
		//echo "cart_no:".$cart_no."<br/>";

		$row_cnt=count($chk);

		$cartNos="";
		for($k=0; $k<$row_cnt; $k++){
			$cartNos.=$chk[$k].", ";
		}

		$cartNos=rtrim($cartNos , ", ");

		$result = deleteGoodsInCart($conn, $mem_no, $cartNos);

		if($result) { 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('삭제되었습니다.');
	location.href="<?=$_SERVER[PHP_SELF]?>";
</script>
<?
		}
		exit;
	}

	




	$arr_rs = listCartByMemNo($conn, $s_ord_no, $cp_no, $mem_no, 'Y', 'N', "ASC");
	// print_r($_POST);
	// exit;
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
	<?
		require "header.php";
		
	?>
		<script>
			function js_order_select(){
				// alert('order_select');
				var chkbox=$(".chkboxT");
				// return;
				var frm = document.frm;
				let flag='N'

				var cntTest=chkbox.length;
				// alert(cntTest);
				for(i=0; i<cntTest; i++){
					if(chkbox[i].checked == true){
						flag='Y';
						break;
					}
				}

				if(flag=='N'){
					alert('선택된 상품이 없습니다');
					return ;
				}


				frm.mode.value="ORDER_SELECT";
				frm.method="POST";
				frm.action="./order_process.php";
				frm.target="";
				frm.submit();
			}
			function js_delete_select(){
				// alert('order_select');
				var chkbox=$(".chkboxT");
				// return;
				var frm = document.frm;
				let flag='N'

				var cntTest=chkbox.length;
				// alert(cntTest);
				for(i=0; i<cntTest; i++){
					if(chkbox[i].checked == true){
						flag='Y';
						break;
					}
				}
				if(flag=='N'){
					alert('선택된 상품이 없습니다');
					return;
				}
				// var frm=document.frm;

				frm.mode.value="DELETE_SELECT";
				frm.method="POST";
				frm.action="<?=$_SERVER['PHP_SELF']?>";
				frm.target="";
				frm.submit();

			}
			function js_all_chk(){
				var frm=document.frm;
				if(frm['chk[]'] != null){
					if(frm['chk[]'].length !=null){
						if(frm.chk_all.checked==true){
							for(i=0; i<frm['chk[]'].length; i++){
								frm['chk[]'][i].checked=true;
							}
						}
						else{
							for(i=0; i<frm['chk[]'].length; i++){
								frm['chk[]'][i].checked=false;
							}
						}
					}//end of if(frm['chk[]]length!=null)
					else{
						if(frm.chk_all.checked==true){
							frm['chk[]'].checked=true;
						}
						else{
							frm['chk[]'].checked=false;
						}
					}

				}
			}

			function js_view_order_goods_detail(idx){
				$('#dvOrderGoodsDetail_'+idx).show();
			}
			function js_hide_order_goods_detail(idx){
				$('#dvOrderGoodsDetail_'+idx).hide();
			}
			function js_add_comma(value){
				value=value+"";
				value=value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				return value;
				
			}
			function js_change_qty_at_db(idx, cnt){
				// alert('test');
				var curQty=Number($('#strQty_'+idx).html());
				var cartNo=$('#hdCartNo_'+idx).val();
				var goodsNo=$('#hdGoodsNo_'+idx).val();
				var totalPrice=0;

				var GoodsNm=$('#GoodsNm_'+idx).val();
				
				if($("#btnChangeQty_"+idx).prop('disabled')==false){
					$("#btnChangeQty_"+idx).attr('disabled',true);
					$("#btnChangeQty_"+idx).css("background-color","gray");
					$("#btnChangeQty_"+idx).css("cursor","defalut");
					$('#hdOriginalQty_'+idx).val(curQty);


					for(i=0; i<cnt; i++){
						var q=Number($('#hdOriginalQty_'+i).val());
						var p=Number($('#hdCurSalePrice_'+i).val());
						// alert(p);
						// alert(q);
						tmpPrice=(p*q);
						totalPrice+=tmpPrice;
					}
					var strTotalPrice=js_add_comma(totalPrice+"");
					// alert(totalPrice);
					$('#strTotalPrice').html(strTotalPrice);
				}
				
				$.ajax({
					url:"ajax_cart.php",
					type:"POST",
					dataType:"JSON",
					data:{
						'mode':'CHAGNE_CORRESPONDING_QTY',
						'cartNo':cartNo,
						'goodsNo':goodsNo,
						'curQty':curQty,
					},
					success:function(data){
						
						alert("상품 : "+GoodsNm+"\n\n수량 : "+curQty+"\n\n변경 되었습니다.");
						//alert("변경 되었습니다.");
						return;
					},
					error:function(jqXHR, textStatus, errorThrown){

					}
				});

			}
			function js_change_goods_qty(idx, sign){
				var deliveryCntInBox=Number($('#hdDeliveryCntInBox_'+idx).val());
				var curQty=Number($('#strQty_'+idx).html());
				var cartNo=$('#hdCartNo_'+idx).val();
				// alert(curQty);
				// alert(deliveryCntInBox);
				if(sign=='-'){
					if(curQty-1<deliveryCntInBox){
						alert('박스입수 이하로는 신청하실 수 없습니다');
						return ;
					}
					curQty=curQty-1;
				}
				else if(sign=='+'){
					// alert('test');
					curQty=curQty+1;

				}
				var curPrice=Number($('#hdCurSalePrice_'+idx).val());
				var totalPrice=curQty*curPrice;
				var totalPriceStr="";
				var goodsNo=$('#hdGoodsNo_'+idx).val();
				// alert(goodsNo);
				var originalQty=$('#hdOriginalQty_'+idx).val();

				if(curQty != originalQty){
					$('#btnChangeQty_'+idx).attr("disabled",false);
					$("#btnChangeQty_"+idx).css("background-color","black");					
					$("#btnChangeQty_"+idx).css("cursor","pointer");
				}
				else if(curQty == originalQty){
					$('#btnChangeQty_'+idx).attr("disabled",true);
					$("#btnChangeQty_"+idx).css("background-color","gray");
					$("#btnChangeQty_"+idx).css("cursor","defalut");
				}

				$('#strQty_'+idx).text(curQty);
				totalPriceStr=js_add_comma(totalPrice)+"원";
				$('#tdTotalPrice_'+idx).text(totalPriceStr);

			}//end of func
		</script>
	</head>

	<body>
	<div class="wrap">
	<?
		require "top.php";
	?>
<!-- 장바구니 -->
	<div class="detail_page">
		<div class="detail_page_inner">
			
			<div class="cart_info">
				<h4>
					장바구니
				</h4>
				<form name="frm" class="form-horizontal in-signin" method="post">
					<input type="hidden" name="mode" value="">
					<input type="hidden" name="cart_no" value="">

					<table>
						<caption>장바구니</caption>

						<tr>
							<th><input type="checkbox" name="chk_all" onclick='js_all_chk()'></th>
							<th>상품썸네일</th>
							<th>상품정보</th>
							<th>판매가</th>
							<th>수량</th>
							<th>변경</th>
							<th>구매예정가</th>
							<!-- <th>삭제</th> -->
						</tr>

					<?
						$nCnt = 0;
						$TOTAL_SUM_PRICE = 0;
						$TOTAL_QTY = 0;

						$cntArr=sizeof($arr_rs);
						
						if ($cntArr > 0) {

							for ($j = 0 ; $j < $cntArr; $j++) {
								
								$rn							= trim($arr_rs[$j]["rn"]);
								$CART_NO					= trim($arr_rs[$j]["CART_NO"]);
								$ON_UID						= trim($arr_rs[$j]["ON_UID"]);
								$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
								$GOODS_CODE					= trim($arr_rs[$j]["GOODS_CODE"]);
								$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
								$QTY						= trim($arr_rs[$j]["QTY"]);
								$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
								$PRICE						= trim($arr_rs[$j]["PRICE"]);
								$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
								$CUR_SALE_PRICE				= trim($arr_rs[$j]["CUR_SALE_PRICE"]);
								$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
								$DELIVERY_PRICE				= trim($arr_rs[$j]["DELIVERY_PRICE"]);
								$DISCOUNT_PRICE				= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
								$SA_DELIVERY_PRICE			= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);
								$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

								$IMG_URL						= trim($arr_rs[$j]["IMG_URL"]);
								$FILE_NM						= trim($arr_rs[$j]["FILE_NM_100"]);
								$FILE_RNM						= trim($arr_rs[$j]["FILE_RNM_100"]);
								$FILE_PATH					= trim($arr_rs[$j]["FILE_PATH_100"]);
								$FILE_SIZE					= trim($arr_rs[$j]["FILE_SIZE_100"]);
								$FILE_EXT						= trim($arr_rs[$j]["FILE_EXT_100"]);
								$FILE_NM_150				= trim($arr_rs[$j]["FILE_NM_150"]);
								$FILE_RNM_150				= trim($arr_rs[$j]["FILE_RNM_150"]);
								$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
								$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
								$FILE_EXT_150				= trim($arr_rs[$j]["FILE_EXT_150"]);

								$CATE_01						= trim($arr_rs[$j]["C_CATE_01"]);

								$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
								$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
								$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
								$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
								$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
								$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
								if($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00")
									$OPT_OUTSTOCK_DATE			= date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));
								$OPT_MEMO					= trim($arr_rs[$j]["OPT_MEMO"]);

								$OPT_OUTBOX_TF = ($OPT_OUTBOX_TF == "Y" ? "있음" : "" );

								$OPT_OUTSTOCK_DATE = ($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? $OPT_OUTSTOCK_DATE : "출고미정");


								$priceChangeTF='N';

								if($CUR_SALE_PRICE<>$SALE_PRICE){
									$priceChangeTF='Y';
								}


								if($CATE_01 <> "")
									$str_cate_01 = $CATE_01.") ";
								else 
									$str_cate_01 = "";

								$SUM_PRICE = ($QTY * $SALE_PRICE) + $SA_DELIVERY_PRICE - $DISCOUNT_PRICE;

								$TOTAL_QTY = $TOTAL_QTY + $QTY;

								//if($CATE_01 == "") //2016-12-21 샘플, 증정 주문서 금액에 다시 추가
								$TOTAL_SUM_PRICE = $TOTAL_SUM_PRICE + $SUM_PRICE;

								$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");
								
								$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);

								// echo "OPT_STICKER_NO : ".$OPT_STICKER_NO."<br>";

								$OPT_STICKER_NAME=getStickerNameByStickerNo($conn, $OPT_STICKER_NO);

								// $CUR_SALE_PRICE					= trim($arr_rs[$j]["CUR_SALE_PRICE"]);
					?>
						<tr>
							<td style="width:10px;"><input type="checkbox" name="chk[]" value="<?=$CART_NO?>" class="chkboxT"></td>
							<td style="width:40px;">
								<a href="/sub_detail.php?goods_no=<?=$GOODS_NO?>">
								<input type="hidden" name="m_cart_no[]" value="<?=$CART_NO?>">&nbsp;
								<div class="thumb" style="background:url('<?=$img_url?>') no-repeat;background-size:cover;background-position:center;"></div>
								</a>
							</td>

							<td onmouseover="js_view_order_goods_detail('<?=$j?>')" onmouseout="js_hide_order_goods_detail('<?=$j?>')" style="position:relative; cursor: default;"><!--상품정보-->
							<?
									if($OPT_STICKER_NAME <>"" || $OPT_PRINT_MSG <>"")
							{	?>
								<div id="dvOrderGoodsDetail_<?=$j?>" style="z-index:10; display: none; overflow: auto; width:400px;;height:auto; background:#FAFAFA;box-shadow:4px 4px 10px rgba(0,0,0,0.9);box-sizing:content-box;position:absolute;top:50px;right:-10px;">
									*옵션내용*</br>
								<?
									if($OPT_STICKER_NAME <>""){
										// getStickerNameByStickerNo($OPT_STICKER_NO);
									?>
										<b>스티커 :</b> <?=$OPT_STICKER_NAME?></br>
									<?
									}
									if($OPT_PRINT_MSG <>""){
									?>
										<b>인쇄 :</b> <?=$OPT_PRINT_MSG?></br>
									<?
									}

								?>

									

									<input type="hidden" id="hdStickerName_<?=$j?>" value="<?=$OPT_STICKER_NAME?>">
									<input type="hidden" id="hdPrintMessage_<?=$j?>" value="<?=$OPT_PRINT_MSG?>">

								</div>
								<?	}	?>
									<input type="hidden" id="GoodsNm_<?=$j?>" value="<?=$GOODS_NAME?>">
							 	[<?=$GOODS_CODE?>] <?=$GOODS_NAME?>
								
							</td>
							<td style="text-align-last: right; width:40px;"><!--판매가-->
								<?if($priceChangeTF=='Y'){echo number_format($CUR_SALE_PRICE)."/";}?><?=number_format($SALE_PRICE)?>원
								<input type="hidden" id="hdCurSalePrice_<?=$j?>" value="<?=$CUR_SALE_PRICE?>">
							</td>
							<td style="width:120px;"><!--수량-->
								<!--<input type="button" value="-" onclick="js_change_goods_qty('<?=$j?>','-')">
								<strong id="strQty_<?=$j?>"><?=number_format($QTY)?></strong>
								<input type="button" value="+" onclick="js_change_goods_qty('<?=$j?>','+')">-->
								<div class="count_up_down">
									<div class="count_up_down_down" onclick="js_change_goods_qty('<?=$j?>','-')">
										-
									</div>
									<strong id="strQty_<?=$j?>">
										<?=number_format($QTY)?>
									</strong>
									<div class="count_up_down_up" onclick="js_change_goods_qty('<?=$j?>','+')">
										+
									</div>
								</div>

							</td>
							<td><!--변경버튼-->
								<button style="background-color: gray; cursor: default;" id="btnChangeQty_<?=$j?>" disabled onclick="js_change_qty_at_db('<?=$j?>','<?=$cntArr?>')" >변경</button>
								<!--<input type="button" value="변경" id="btnChangeQty_<?=$j?>" disabled onclick="js_change_qty_at_db('<?=$j?>','<?=$cntArr?>')" >-->
								<input type="hidden" value="<?=$QTY?>" id='hdOriginalQty_<?=$j?>'>
							</td>

							<td id="tdTotalPrice_<?=$j?>" style="text-align-last: right;">
								<?=number_format($SUM_PRICE)?>원
							</td><!--구매예정가-->

							<!-- <td> -->
								<!-- <button class="red btn-delete cursor-pointer" data-cart_no="<?=$CART_NO?>" >삭제 </button>												 -->
							<!-- </td> -->

							<input type="hidden" id="hdDeliveryCntInBox_<?=$j?>" value="<?=$DELIVERY_CNT_IN_BOX?>">
							<input type="hidden" id="hdGoodsNo_<?=$j?>" value="<?=$GOODS_NO?>">
							<input type="hidden" id="hdCartNo_<?=$j?>" value="<?=$CART_NO?>">
						</tr>
				<?		
					} 
				?>
					</table>

					<div class="price" style="text-align-last: right;">
						합계금액  <i><span id="strTotalPrice"></span>원</i>&nbsp;&nbsp;
						<input type="hidden" value="<?=$TOTAL_SUM_PRICE?>" id='hdTotalPrice'>
					</div>
				</div>				
				<div class="clear"></div>
				
				<div class="tright" style="margin-top:40px;">
					<a href="#" class="deleteGoods" onclick="js_delete_select()">선택 상품 삭제</a> 
					<a href="#" onclick="js_order_select()" class="joomoon">선택 상품 주문</a></div>

				<? } else { ?>
					<div class="well text-center">장바구니에 넣으신 상품이 없습니다.</div>
				<? } ?>
				</form>
			</div>
		</div>
	</div>	

	<?
		require "footer.php";
	?>
	<script type="text/javascript">
		$(function(){
			var tp="<?=$TOTAL_SUM_PRICE?>";
			$("#hdTotalPrice").val(tp);
			var ctp=js_add_comma(tp+"");
			$("#strTotalPrice").html(ctp);

			$(".btn-delete").click(function(){

				var cart_no = $(this).data("cart_no");

				var frm = document.frm;

				frm.method="post";
				frm.cart_no.value = cart_no;
				frm.mode.value="DEL_CART";
				frm.action="<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			});
		
		});//end of $(function()){}
	</script>

	</body>
</html>

