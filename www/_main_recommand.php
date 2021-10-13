<?
	$con_cate = '220203';
	$arr_options = null;
	$arr_rs = listGoods($conn, $con_cate, '', '', '', '', $con_cate_01, $con_cate_02, $con_cate_03, '판매중', '', 'Y', 'N', '', '', $arr_options, '', '', 1, 4);

?>
<div class="container">
	<h4><img src="img/banner_recommand_goods.jpg" alt="추천상품"/></h4>
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

					$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "190", "190");
					
					if ($_SESSION['C_CP_NO'] <> "") {
						$SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $_SESSION['C_CP_NO']);
					}
		?>
		<div class="item col-lg-3">
			<a href="/detail1.php?goods_no=<?=$GOODS_NO?>" class="thumbnail">
				<img src="<?=$img_url?>" alt="<?=$GOODS_NAME?>">
				<p class="code"><?=$GOODS_CODE?></p>
				<p class="title"><?=$GOODS_NAME?></p>
				<?if ($_SESSION['C_CP_NO'] <> "") {?>
					<p class="price"><strong><?=number_format($SALE_PRICE)?></strong>원</p>
				<? } ?>
			</a>
		</div>
		<?
				}
			}
		?>
	</div>
</div>