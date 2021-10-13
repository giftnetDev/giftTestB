<?
	require "_common/home_pre_setting.php";
?>
<?
#====================================================================
# Request Parameter
#====================================================================

	$mem_no=$_SESSION['C_MEM_NO'];

	if ($start_date == "") {
		$start_date = "2010-07-24";
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
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
		$nPageSize = 24;
	}

	$nPageBlock	= 5;

#===============================================================
# Get Search list count
#===============================================================

	

//	if($cate == "")
//		$cate = "2203";
		
	$con_cate = $cate;

	$is_catalog = startsWith($con_cate, getDcodeExtByCode($conn, "HOME_BANNER", "BANNER_CATE" ));

	if($is_catalog) { 

		$arr_options = array("exclude_category" => $exclude_category, "vendor_calc" => $vendor_calc, "cate_page" => $cate_page);

		$order_field = "PAGE_SEQ";
		$order_str = "ASC";

		$nListCnt =totalCntGoodsCatalog($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, '판매중', $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options);

		$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

		if ((int)($nTotalPage) < (int)($nPage)) {
			$nPage = $nTotalPage;
		}

		$arr_rs = listGoodsCatalog($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, '판매중', $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
		
	} else { 

		$arr_options = array("code_cate" => $code_cate, "start_price" => $start_price, "end_price" => $end_price);
		
		$nListCnt = totalCntHomepageGoods($conn, $search_field, $search_str, $arr_options);

		$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

		if ((int)($nTotalPage) < (int)($nPage)) {
			$nPage = $nTotalPage;
		}

		$arr_rs = listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
	
	}


?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
?>

<script>
	function js_order_by(order_field, order_str) {

		location.href ="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&code_cate=<?=$code_cate?>&order_field=" + order_field + "&order_str=" + order_str;

	}

	function js_page_size(pagesize) { 
		location.href ="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&code_cate=<?=$code_cate?>&order_field=<?=$order_field?>&order_str=<?=$order_str?>&start_price=<?=$start_price?>&end_price=<?=$end_price?>&nPageSize=" + pagesize;
	}

	function js_cate_page(cate_page) { 
		location.href ="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&code_cate=<?=$code_cate?>&order_field=<?=$order_field?>&order_str=<?=$order_str?>&nPageSize=<?=$nPageSize?>&start_price=<?=$start_price?>&end_price=<?=$end_price?>&cate_page=" + cate_page;
	}

	function js_price(start, end) { 
		location.href ="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&code_cate=<?=$code_cate?>&order_field=<?=$order_field?>&order_str=<?=$order_str?>&nPageSize=<?=$nPageSize?>&start_price=" + start + "&end_price=" + end;
	}
</script>
<meta name="robots" content="noindex,nofollow">
</head>
<body>

	<input type="hidden" name="order_field" value=""/>
	<input type="hidden" name="order_str" value=""/>
	<input type="hidden" name="code_cate" value="<?=$code_cate?>"/>
	<input type="hidden" name="cate" value="<?=$cate?>"/>
	<input type="hidden" name="cate_page" value="<?=$cate_page?>"/>
<?
	require "_common/v2_top.php";
?> 
<?
	require "_sub_categories.php";
?> 
<!-- 상품 목록 -->
<div class="container-fluid" id="prod-list">
    <div class="container">
        <div class="row prod-top">
            <div class="col-lg-5 col-xs-12">
                <h4><?=getCategoryNameOnly($conn, $cate)?> <span><?=getCategoryMemoOnly($conn, $cate)?></span></h4>
            </div>

            <div class="text-right col-lg-7 col-xs-12 prod-view-option">
					<? if($is_catalog) { ?>
					&middot; <a href="javascript:js_order_by('PAGE_SEQ', 'ASC');" class="<?if($order_field == "PAGE_SEQ" && $order_str == "ASC") echo "selected";?>">페이지순</a>

					<? } else { ?>
					&middot; <a href="javascript:js_order_by('REG_DATE', 'DESC');" class="<?if(($order_field== "REG_DATE" || $order_field== "") && ($order_str == "DESC" || $order_str == "")) echo "selected";?>">신제품순</a> &middot; <a href="javascript:js_order_by('SALE_PRICE', 'ASC');" class="<?if($order_field== "SALE_PRICE" && $order_str == "ASC") echo "selected";?>">낮은 가격순</a> &middot; <a href="javascript:js_order_by('SALE_PRICE', 'DESC');" class="<?if($order_field== "SALE_PRICE" && $order_str == "DESC") echo "selected";?>">높은 가격순</a>  &middot; <a href="javascript:js_order_by('GOODS_NAME', 'ASC');" class="<?if($order_field == "GOODS_NAME" && $order_str == "ASC") echo "selected";?>">상품명순</a>
					<? } ?>
					<? 
							if($is_catalog) { 

								$arr_cate_page = listGoodsCategoryPage($conn, $cate); 
								if(sizeof($arr_cate_page) > 0) { 
					?>
				<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?=($cate_page == "" ? "페이지 선택" : $cate_page." 페이지 ")?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li role="presentation"><a href="javascript:js_cate_page('');">전체 페이지</a></li>
						<? 
								for ($c = 0; $c < sizeof($arr_cate_page); $c ++) { 
									$rs_cate_page = $arr_cate_page[$c]["PAGE"];
						?>
                        <li role="presentation"><a href="javascript:js_cate_page('<?=$rs_cate_page?>');"><?=$rs_cate_page?> 페이지</a></li>
						<?		} ?>
                    </ul>
				</div>
				<?			} 
						}
				?>

				<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?
						if($start_price <> "")
							$start_price = number_format($start_price-1)."원 초과";

						if($end_price <> "")
							$end_price = number_format($end_price)."원 이하";

						
					?>
					<?=($start_price == "" && $end_price == "" ? "가격별 보기" : $start_price." ~ ".$end_price )?> <span class="caret"></span>
					</button>

					
                    <ul class="dropdown-menu">
						<li role="presentation"><a href="javascript:js_price('', '');">전체..</a></li>
                        <li role="presentation"><a href="javascript:js_price('', '3000');">3천원이하</a></li>
                        <li role="presentation"><a href="javascript:js_price('3001', '5000');"">3천원~5천원</a></li>
                        <li role="presentation"><a href="javascript:js_price('5001', '10000');"">5천원~1만원</a></li>
						<li role="presentation"><a href="javascript:js_price('10001', '30000');"">1만원~3만원</a></li>
						<li role="presentation"><a href="javascript:js_price('30001', '');"">3만원이상</a></li>
                    </ul>
                </div>
				
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?=$nPageSize?>개씩 보기 <span class="caret"></span>
					</button>

					
                    <ul class="dropdown-menu">
                        <li role="presentation"><a href="javascript:js_page_size('24');">24개씩 보기</a></li>
                        <li role="presentation"><a href="javascript:js_page_size('48');"">48개씩 보기</a></li>
                        <li role="presentation"><a href="javascript:js_page_size('96');"">96개씩 보기</a></li>
                    </ul>
                </div>
            </div>
        </div>
		
		<div class="row">
		<?
			if(sizeof($arr_rs) > 0) { 

				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

					$rn								= trim($arr_rs[$j]["rn"]);
					$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
					$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
					$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
					$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
					$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
					$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
					$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
					$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
					$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
					$PRICE						= trim($arr_rs[$j]["PRICE"]);
					$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
					$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
					$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
					$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);
					$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
					$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
					$IMG_URL					= trim($arr_rs[$j]["IMG_URL"]);
					$FILE_NM					= trim($arr_rs[$j]["FILE_NM_100"]);
					$FILE_RNM					= trim($arr_rs[$j]["FILE_RNM_100"]);
					$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
					$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
					$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
					$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
					$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
					$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
					$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
					$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
					$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
					$CONTENTS					= trim($arr_rs[$j]["CONTENTS"]);
					$READ_CNT					= trim($arr_rs[$j]["READ_CNT"]);
					$DISP_SEQ					= trim($arr_rs[$j]["DISP_SEQ"]);
					$USE_TF						= trim($arr_rs[$j]["USE_TF"]);
					$DEL_TF						= trim($arr_rs[$j]["DEL_TF"]);
					$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
					$PRICE						= trim($arr_rs[$j]["PRICE"]);

					$CONCEAL_PRICE_TF			= trim($arr_rs[$j]["CONCEAL_PRICE_TF"]);

					// if ($_SESSION['C_CP_NO'] <> "") {
						$SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $_SESSION['C_CP_NO']);
					// }

					// 이미지가 저장 되어 있을 경우
					$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "150", "150");

					$REG_DATE = date("Y-m-d",strtotime($REG_DATE));


		?>
            <div class="col-lg-3 col-xs-6 col-sm-4 item">
                <a href="detail1.php?cate=<?=$cate?>&code_cate=<?=$code_cate?>&goods_no=<?=$GOODS_NO?>" class="thumbnail">
					<img src="<?=$img_url?>" alt="<?=$GOODS_NAME?>" >
					<p class="code"><?=$GOODS_CODE?></p>
					<p class="title" title="<?=$GOODS_NAME?>"><?=$GOODS_NAME?></p>
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
			} else { 
		?>
			<div class="col-lg-12 col-xs-12 col-sm-12 item" style="text-align:center;">
				조건에 맞는 상품이 없습니다.
			</div>
		<?
			}
		?>
        </div>
    </div>
</div>
<!-- // 상품 목록 -->

<div class="container prod-page">
    <nav class="text-center">
        <!-- Add class .pagination-lg for larger blocks or .pagination-sm for smaller blocks-->
        <?
			# ==========================================================================
			#  페이징 처리
			# ==========================================================================
			if (sizeof($arr_rs) > 0) {
				#$search_field		= trim($search_field);
				#$search_str			= trim($search_str);

				$strParam = "";
				$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
				$strParam = $strParam."&cate=".$cate."&cate_page=".$cate_page."&start_price=".$start_price."&end_price=".$end_price."&code_cate=".$code_cate;

		?>
		<?= Home_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
		<?
			}
		?>
    </nav>
</div>

<?
	require "_common/v2_footer.php";
?>
</body>
</html>

