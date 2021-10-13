<?
	$arr_options = null;
	$arr_rs = listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 24, 1000);

	$mem_no=$_SESSION['C_MEM_NO'];

?>
<div class="container-fluid" id="recent">
    <div class="container">
        <!--<h4><span>기프트넷</span> 최근 등록 상품</h4>-->
        <div class="row">

			<?
				if (sizeof($arr_rs) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
						$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
						$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
						$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
						$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
						$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
						$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
						$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
						$PRICE					= trim($arr_rs[$j]["PRICE"]);
						$CONCEAL_PRICE_TF		= trim($arr_rs[$j]["CONCEAL_PRICE_TF"]);

						$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "150", "150");
						
						if ($_SESSION['C_CP_NO'] <> "") {
							$SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $_SESSION['C_CP_NO']);
						}
			?>
			<div class="col-lg-3 col-sm-4 col-xs-6  item">
				<a href="/detail1.php?goods_no=<?=$GOODS_NO?>" class="thumbnail">
					<img src="<?=$img_url?>" alt="<?=$GOODS_NAME?>">
					<p class="code"><?=$GOODS_CODE?></p>
					<p class="title"><?=$GOODS_NAME?></p>
					<?//if ($_SESSION['C_CP_NO'] <> "") {?>
						<?
						if($mem_no <> "")
						{	
						?>	
							<p class="price"><strong><?=number_format($SALE_PRICE)?></strong>원</p>
						<?
						}
						else
						{	
							if($CONCEAL_PRICE_TF != "Y")
							{
						?>
								<p class="price"><strong><?=number_format($SALE_PRICE)?></strong>원</p>
						<?	}
							else
							{
						?>
								<p class="price"><strong>가격문의</strong></p>
						<?
							}
						}
						?>
				</a>
			</div>
			<?
					}
				}
			?>
        </div>
    </div>
</div>