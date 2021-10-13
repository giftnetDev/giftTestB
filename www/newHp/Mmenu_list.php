<?
    require "../_common/home_pre_setting.php";

    $mem_no=$_SESSION['C_MEM_NO'];
    
?>


<?    
    if ($start_date == "") 
    {
        $start_date = "2010-07-24";
    } 
    else 
    {
        $start_date = trim($start_date);
    }

    if ($end_date == "") 
    {
        $end_date = date("Y-m-d",strtotime("0 month"));
    } 
    else 
    {
        $end_date = trim($end_date);
    }

    $del_tf = "N";

    $con_cate = $cate;

    $is_catalog = startsWith($con_cate, getDcodeExtByCode($conn, "HOME_BANNER", "BANNER_CATE" ));

    if($is_catalog) 
    { 
        if($order_field == "" )
        {
            $order_field = "PAGE_SEQ";
        }

        if($order_str == "" )
        {
            $order_str = "ASC";
        }

        $arr_options["code_cate"]=$code_cate;        
        $arrGoods = listGoodsCatalog($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, '판매중', $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 100, 1000);
        
    } else 
    {     
        $arr_options["code_cate"]=$code_cate;        
        $arrGoods 		= listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 100, 1000);
    
    }

    if($search_str <> "")
        $search_field = "ALL";
?> 

<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "Mheader.php";
	require "Msearch.php";
?>
<script>
	function js_order_by(order_field, order_str, sort) {

		location.href ="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&code_cate=<?=$code_cate?>&order_field=" + order_field + "&order_str=" + order_str+ "&sort=" + sort;

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
    function js_add_goods_view(){
			var totalCntGoods=Number($("input[name='totalGoodsCnt']").val());

			var cnt=Number($("input[name='goodsCnt']").val());
			var from=cnt*12;                                                //더보기 클릭 시 12개만 나오도록..
			var nextCnt=(cnt+1)*12;

			if(totalCntGoods<=nextCnt){
				nextCnt=totalCntGoods;
				$(".product_list_more").css("display","none");
			}
			else{
				$("input[name='goodsCnt']").val(cnt+1);
			}
			for(i=from; i<nextCnt; i++){
				$("#dv_product_list_cell_"+i).css("display","block");
			}

		}
</script>
</head>
<body>
    
<!-- <input type="text"> -->

<div class="wrap">
<br>
    <div class="filter" style="text-align: center; color: black !important; font-weight: bold;">
        <a id = "sort1" href="javascript:js_order_by('REG_DATE', 'DESC', 'sort1');" class="<?if(($order_field== "REG_DATE" || $order_field== "") && ($order_str == "DESC" || $order_str == "")) ?>">신제품순</a> &middot; 
        <a id = "sort2" href="javascript:js_order_by('SALE_PRICE', 'ASC', 'sort2');" class="<?if($order_field== "SALE_PRICE" && $order_str == "ASC") ?>">낮은 가격순</a> &middot; 
        <a id = "sort3" href="javascript:js_order_by('SALE_PRICE', 'DESC', 'sort3');" class="<?if($order_field== "SALE_PRICE" && $order_str == "DESC") ?>">높은 가격순</a>  &middot; 
        <a id = "sort4" href="javascript:js_order_by('GOODS_NAME', 'ASC', 'sort4');" class="<?if($order_field == "GOODS_NAME" && $order_str == "ASC") ?>">상품명순</a>
    </div><!--class="filter"-->

		<div class="product_list">
		<?
			$cntGoods=sizeof($arrGoods);
            if($cntGoods>0){
                if($cntGoods>11){        //더보기 클릭 시 12개만 나오도록..
                    $extendflag='Y';
                }
                else{
                    $extendflag='N';
                }
                for($j=0; $j<$cntGoods; $j++)
                {
                    $rn								= trim($arrGoods[$j]["rn"]);
                    $GOODS_NO					= trim($arrGoods[$j]["GOODS_NO"]);
                    $GOODS_CATE				= trim($arrGoods[$j]["GOODS_CATE"]);
                    $GOODS_CODE				= trim($arrGoods[$j]["GOODS_CODE"]);
                    $GOODS_NAME				= SetStringFromDB($arrGoods[$j]["GOODS_NAME"]);
                    $GOODS_SUB_NAME			= SetStringFromDB($arrGoods[$j]["GOODS_SUB_NAME"]);
                    $CATE_01					= trim($arrGoods[$j]["CATE_01"]);
                    $CATE_02					= trim($arrGoods[$j]["CATE_02"]);
                    $CATE_03					= trim($arrGoods[$j]["CATE_03"]);
                    $CATE_04					= trim($arrGoods[$j]["CATE_04"]);
                    $PRICE						= trim($arrGoods[$j]["PRICE"]);
                    $SALE_PRICE				= trim($arrGoods[$j]["SALE_PRICE"]);
                    $BUY_PRICE				= trim($arrGoods[$j]["BUY_PRICE"]);
                    $EXTRA_PRICE			= trim($arrGoods[$j]["EXTRA_PRICE"]);
                    $SALE_SUSU				= trim($arrGoods[$j]["SALE_SUSU"]);
                    $STOCK_CNT				= trim($arrGoods[$j]["STOCK_CNT"]);
                    $TAX_TF						= trim($arrGoods[$j]["TAX_TF"]);
                    $IMG_URL					= trim($arrGoods[$j]["IMG_URL"]);
                    $FILE_NM					= trim($arrGoods[$j]["FILE_NM_100"]);
                    $FILE_RNM					= trim($arrGoods[$j]["FILE_RNM_100"]);
                    $FILE_PATH				= trim($arrGoods[$j]["FILE_PATH_100"]);
                    $FILE_SIZE				= trim($arrGoods[$j]["FILE_SIZE_100"]);
                    $FILE_EXT				= trim($arrGoods[$j]["FILE_EXT_100"]);
                    $FILE_NM_150			= trim($arrGoods[$j]["FILE_NM_150"]);
                    $FILE_RNM_150			= trim($arrGoods[$j]["FILE_RNM_150"]);
                    $FILE_PATH_150			= trim($arrGoods[$j]["FILE_PATH_150"]);
                    $FILE_SIZE_150			= trim($arrGoods[$j]["FILE_SIZE_150"]);
                    $FILE_EXT_150			= trim($arrGoods[$j]["FILE_EXT_150"]);
                    $DELIVERY_CNT_IN_BOX	= trim($arrGoods[$j]["DELIVERY_CNT_IN_BOX"]);
                    $CONTENTS					= trim($arrGoods[$j]["CONTENTS"]);
                    $READ_CNT					= trim($arrGoods[$j]["READ_CNT"]);
                    $DISP_SEQ					= trim($arrGoods[$j]["DISP_SEQ"]);
                    $USE_TF						= trim($arrGoods[$j]["USE_TF"]);
                    $DEL_TF						= trim($arrGoods[$j]["DEL_TF"]);
                    $REG_DATE					= trim($arrGoods[$j]["REG_DATE"]);
                    $PRICE						= trim($arrGoods[$j]["PRICE"]);

                    $concel_tf					= trim($arrGoods[$j]["CONCEAL_PRICE_TF"]);

                    if ($_SESSION['C_CP_NO'] && $_SESSION['C_CP_NO'] <> "") {
                        $SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $_SESSION['C_CP_NO']);
                    }

                // 이미지가 저장 되어 있을 경우
                    $img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "150", "150");

                    $REG_DATE = date("Y-m-d",strtotime($REG_DATE));


				?>
					<div class="product_list_cell" id="dv_product_list_cell_<?=$j?>">
                        <dl style="cursor: pointer;" onclick="location.href='Mgoods_info.php?goods_no=<?=$GOODS_NO?>'">
                        <div class="img" style="background:url('<?=$img_url?>') no-repeat;background-size:auto 100%; background-position:center center"></div>
						<b>
							<span><?=$GOODS_CODE?></span>
							<?=$GOODS_NAME?>
						</b>
                        <?
                            if($mem_no <> "")
                            {	
                            ?>
                                <i><i><?=number_format($SALE_PRICE)?></i> 원</i>
                            <?
                            }
                            else
                            {	
                                if($concel_tf != "Y")
                                {   
                            ?>      <i><i><?=number_format($SALE_PRICE)?></i> 원</i>
                            <?	}
                                else
                                {   
                                ?>
                                    <i><i>가격문의</i></i>
                            <?
                                }
                            } 
                        ?>
                    </dl>
					</div>
                    <script>
						if(Number('<?=$j?>')>11){        //더보기 클릭 시 12개만 나오도록..
							$("#dv_product_list_cell_<?=$j?>").css("display","none");
						}
					</script>
				<?
                }//end of for(cntGoods)
                $goodsCnt=1;
            }//end of if(cntGoods>0)
            else
            {  ?>
                <br>
                <div style="text-align: center; font-size: 25px;"><font color = "black"> 리스트가 없습니다. </font></div>
            <?
            }
		?>		
		</div><!--product_list-->
        <input type="hidden" name="goodsCnt" value="<?=$goodsCnt?>">
	    <input type="hidden" name="extendflag" value='<?=$extendflag?>'>
        <input type="hidden" name="totalGoodsCnt"value="<?=$cntGoods?>">
        <?
        if($extendflag=='Y'){
        ?>
            <div class="product_list_more"><button type="button" onclick="js_add_goods_view();">더보기</button></div>
        <?
        }
        ?>

<?
	require "Mfooter.php";
?>
</div>
</body>
</html>
