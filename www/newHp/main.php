
<?
    $mem_no=$_SESSION['C_MEM_NO'];

    $arr_options = null;

	$arr_rs = listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 100, 1000);

    //echo "memberNo : $memberNo, cntLike : $cntLike<br>";


?>
        <input type="hidden" id="g_arrCart">

        <div class="play_button_to_the_first">&lt;&lt;</div>
		<div class="play_button_prev">&lt;</div>
		<div class="play_button_next">&gt;</div>  
        
        <div class="wrapper">
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

                    $concel_tf			 	= trim($arr_rs[$j]["CONCEAL_PRICE_TF"]);	

                    $img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "150", "150");
                    
                    if ($_SESSION['C_CP_NO'] <> "") {
                        $SALE_PRICE = getCompanyGoodsPriceOrDCRate($conn, $GOODS_NO, $SALE_PRICE, $PRICE, $_SESSION['C_CP_NO']);
                    }

        // ?>
                                <div class="wrapper_cell">
                                    <div class="wrapper_cell_inner_cell">
                                        
                                        <!-- <div class="cart"onclick="js_add_goods_to_cart('<?=$GOODS_NO?>', '<?=$img_url?>', '<?=$_SESSION['C_MEM_NO']?>', '<?=$_SESSION['C_CP_NO']?>', this)"></div> -->
                                        <div class="div"></div>

                                        <?
                                            if($tmpCntLike>0 && $memberNo>0){
                                                if(isExistingAtWishList($conn,$GOODS_NO, $memberNo)>0){
                                                ?>
                                                    <!-- ���� -->
                                                    <div class="like like_on" id="likeSymbol_<?=$GOODS_NO?>" onclick="js_add_goods_to_wishList('<?=$GOODS_NO?>', '<?=$img_url?>', '<?=$_SESSION['C_MEM_NO']?>', '<?=$_SESSION['C_CP_NO']?>', this)"></div>

                                                <?
                                                    $tmpCntLike--;
                                                }
                                                else{
                                                ?>
                                                    <div class="like" id="likeSymbol_<?=$GOODS_NO?>" onclick="js_add_goods_to_wishList('<?=$GOODS_NO?>', '<?=$img_url?>', '<?=$_SESSION['C_MEM_NO']?>', '<?=$_SESSION['C_CP_NO']?>', this)"></div>
                                                <?
                                                }
                                            }
                                            else{
                                            ?>
                                                <div class="like" id="likeSymbol_<?=$GOODS_NO?>" onclick="js_add_goods_to_wishList('<?=$GOODS_NO?>', '<?=$img_url?>', '<?=$_SESSION['C_MEM_NO']?>', '<?=$_SESSION['C_CP_NO']?>', this)"></div>
                                            <?    
                                            }        
                                        ?>
                                        <dl onclick="location.href='sub_detail.php?goods_no=<?=$GOODS_NO?>'">
                                        <div class="img"><img src="<?=$img_url?>" alt=""></div>
                                        <div class="text">
                                            <span><?=$GOODS_CODE?></span><br>
                                            <b><?=$GOODS_NAME?></b>
                                            <?
                                            if($mem_no <> "")
                                            {	
                                            ?>
                                                <i><em><?=number_format($SALE_PRICE)?></em> ��</i>
                                            <?
                                            }
                                            else
                                            {	
                                                if($concel_tf != "Y")
                                                {   
                                            ?>      <i><em><?=number_format($SALE_PRICE)?></em> ��</i>
                                            <?	}
                                                else
                                                {   
                                                ?>
                                                    <i><em>���ݹ���</em></i>
                                            <?
                                                }
                                            } 
                                            ?>   
                                        </div>
                                        </dl>
        
                                    </div>
                                </div>
                            <?
                        }//end of for(cntGoods)

                    }//end of if(cntGoopds>0)

                ?>
                <div class="wrapper_cell">
                    <!--empty of for padding-->

            </div><!--wrap  -->

                

        </div><!--wrapper-->
        <script>
            $(document).ready(function(){
                
                var t=Number($('.detail').html());
                // alert(t);
                if(t>0){
                    // alert('test');
                    $('.detail').addClass("detail_on");
                }
            });
        </script>
