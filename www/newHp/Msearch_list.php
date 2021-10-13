<?
	require "../_common/home_pre_setting.php";
?>
<?
#====================================================================
# Request Parameter
#====================================================================

    $mem_no=$_SESSION['C_MEM_NO'];

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

#===============================================================
# Get Search list count
#===============================================================

	if($search_str <> "")
		$search_field = "ALL";

		$arr_options = null;
		$arr_rs = listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 100, 1000);

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "Mheader.php";
    require "Msearch.php";
?>
	<script>
		function js_close_popup(){
			$("#popup_catalog").css("display","none");
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
<!-- <input type="text"> -->

<body>
<div class="wrap">
<br>
<!--<div class="search_word" style="color: black;"> &nbsp;검색어 : <font color ="#e8378a"><b><?=$search_str?></b></font></div>  검색창 있으니 주석처리하자-->
		<div class="product_list">
		<?
			$cntGoods=sizeof($arr_rs);
            if ($cntGoods > 0) {
                if($cntGoods>11){        //더보기 클릭 시 12개만 나오도록..
                    $extendflag='Y';
                }
                else{
                    $extendflag='N';
                }

                for ($j = 0 ; $j < $cntGoods; $j++) {
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

		<div class="product_list_more"><button onclick="js_add_goods_view();">더보기</button></div>
        <input type="hidden" name="goodsCnt" value="<?=$goodsCnt?>">
	    <input type="hidden" name="extendflag" value='<?=$extendflag?>'>
        <input type="hidden" name="totalGoodsCnt"value="<?=$cntGoods?>">

<?
	require "Mfooter.php";
?>
</div>
</body>
</html>

