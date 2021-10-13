<?require "../_common/home_pre_setting.php";  ?>
<?php // PHP_FUNCTION_ZONE


?><!--end of PHP_FUNCTION_ZONE-->
<?php 
//PROCESS_ZONE

    if($paramMode=="DELETE_WISH"){
        $delSeq=$_POST['hd_del_item_seq'];
        
        $query="DELETE FROM T_WISH_LIST
                WHERE  WISH_LIST_NO = '$delSeq'
                ";
        if(!mysql_query($query, $conn)){
            echo "<script>alert('M_Wishlist delete ERROR!');</scrip>";
            exit;
        }
    }
    if($mode=="INSERT_ITEM_TO_SHOPPINGBAG"){
        $str="";
        $cntWish=$_POST['cnt_wish'];

        echo "mode : ".$mode."<br>";
        echo "WISHLIST_CNT : ".$cntWish."<br>";


        for($i = 0; $i < $cntWish; $i++){
            $val="hd_inbox_checked_".$i;
            $key="hd_seq_no_".$i;

            if($_POST[$val]=="Y"){
                $str.=$_POST[$key].", ";
            }
        }//end of for

        $str=rtrim($str, ", ");


        echo $str."<br>";

    }//end of mode="INSERT_ITEM_TO_SHOPPINGBAG"


    $cntLike=0;
    $tmpCntLike=0;
    if($_SESSION['C_MEM_NO'] &&$_SESSION['C_MEM_NO']>0){
        $memberNo=$_SESSION['C_MEM_NO'];
        $cpNo=$_SESSION["C_CP_NO"];
        $wishList=listWishList($conn, $memberNo);
        $cntLike=sizeof($wishList);
        $tmpCntLike=$cntLike;
    }

    if($cntLike<1){
    ?>
        <script>
            alert('위시리스트에 담긴 상품이 없습니다');
            location.href="./Mindex.php";
        </script>        
    <?

        // echo "<script>alert('위시리스트에 담긴 상품이 없습니다);</script>";
        // header('Location:Mindex.php');

    }

    // echo "CNT_LIKE : $cntLike<br>";
    // exit;

?><!--end of PROCESS_ZONE-->
<html>
    <head>
        <? require "./Mheader.php"; ?>
        <script>//JS_FUNCTION_ZONE
            function js_cancel(wishSeq){
                if(!confirm("해당 상품을 위시리스트에서 빼시겠습니까?")){
                    return;
                }

                var frm=document.paramForm;
                frm.paramMode.value="DELETE_WISH";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.target="";
                frm.method="POST";
                $("input[name='hd_del_item_seq']").val(wishSeq);
                frm.submit();
            }

            function js_inbox_select(idx){
                var cnt=Number($("#cnt_checked_item").val());

                // alert($("#inbox_checked_"+idx).val());
                if($("#inbox_checked_"+idx).val()=='N'){
                    $("#inbox_select_"+idx).css("background","url('./img/chkBoxY.png') no-repeat");
                    $("#inbox_select_"+idx).css("background-size","100% 100%");
                    // $("#inbox_select_"+idx).css("background-position","center center");
                    $("#inbox_checked_"+idx).val('Y');
                    cnt++;
                    $("#cnt_checked_item").val(cnt);
                }
                else if($("#inbox_checked_"+idx).val()=='Y'){
                    $("#inbox_select_"+idx).css("background","url('./img/chkBoxN.png') no-repeat");
                    $("#inbox_select_"+idx).css("background-size","100% 100%");
                    // $("#inbox_select_"+idx).css("background-position","center center");
                    $("#inbox_checked_"+idx).val('N');
                    cnt--;
                    $("#cnt_checked_item").val(cnt);
                }
            }
            function js_insert_item_to_shoppingbag(){
                var cnt = Number($("#cnt_checked_item").val());
                if(cnt<1){
                    alert("선택된 상품이 없습니다");
                    return;
                }
                var frm= document.frm;
                frm.mode.value="INSERT_ITEM_TO_SHOPPINGBAG";
                frm.target="";
                frm.method="POST";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();


            }

            function js_go_to_goods_detail(goodsNo){
                location.href="Mgoods_info.php?goods_no="+goodsNo;
            }

        </script><!--END OF JS_FUNCTION_ZONE-->
    </head>
    <body>
        <!-- <header style="width:100%;height:60px;background:black;position:fixed;top:0px;left:0px;text-align:center;z-index:9;">

        </header> -->
        <div class="background">
            <div class="title_line">
                <span class="left_button"></span>
                <span class="page_title">위시리스트</span>
                <span class="right_button"></span>
            </div>
            <div class="content">
                <form name='frm' method="POST">
                    <h3>위시리스트</h3>
                    <?
                        $cntWish=sizeof($wishList);
                        for($i=0; $i<$cntWish; $i++){
                            $curSEQ        =       $wishList[$i]["WISH_LIST_NO"];
                            $curGoodsNo    =       $wishList[$i]["GOODS_NO"];
                            $curGoodsCode  =       $wishList[$i]["GOODS_CODE"];
                            $curGoodsName  =       $wishList[$i]["GOODS_NAME"];
                            $curImgUrl     =       $wishList[$i]["IMG_URL"];
                            $curSalePrice  =       $wishList[$i]["SALE_PRICE"];
                            $curDeliveryCntInBox = $wishList[$i]["DELIVERY_CNT_IN_BOX"];

                    ?>
                        <div class="list_index" id="div_cart_list_<?=$i?>">
                            <div class="inbox_icon" style="background-color: #E5E5E5" onclick="js_cancel('<?=$curSEQ?>');">X</div>
                            <div onclick="js_go_to_goods_detail('<?=$curGoodsNo?>')">
                                <!-- <div class="inbox_icon" id="inbox_select_<?=$i?>" onclick="js_inbox_select('<?=$i?>')" style="background:url('./img/chkBoxN.png') no-repeat;background-size:100% 100%; background-position:center center; width:30px;height:30px;"></div>
                                <input type="hidden" id="inbox_checked_<?=$i?>" name="hd_inbox_checked_<?=$i?>" value='N'>
                                <input type="hidden" name="hd_seq_no_<?=$i?>" value='<?=$curSEQ?>'> -->
                                <div class="inbox_index_title" id="index_goods_name">
                                    <?=$curGoodsName?>
                                </div>
                                <table style="width:96%; margin:2%; text-align: center;">
                                    <colgroup>
                                        <col width="30%">
                                        <col width="25%">
                                        <col width="45%">
                                    </colgroup>
                                    <tr>
                                        <td rowspan="3">
                                            <div style="background:url('<?=$curImgUrl?>') no-repeat;background-size:100% 100%; background-position:center center; width:90px;height:90px; margin-left:4%;"></div>
                                        </td>
                                        <td class="key right_align">상품코드  </td>
                                        <td class="value right_align"><?=$curGoodsCode?></td>
                                        <input type="hidden" id="hd_sale_price_<?=$i?>" value="<?=$SALE_PRICE?>">
                                    </tr>
                                    <tr>
                                        <td class="key right_align">박스입수  </td>
                                        <td class="value right_align"><?=number_format($curDeliveryCntInBox)?>개</td>
                                        <input type="hidden" id="hd_qty_<?=$i?>" name="hd_qty_<?=$i?>" value="<?=$QTY?>">
                    
                                    </tr>
                                    <tr>
                                        <td class="key right_align black">가격  </td>
                                        <td class="value right_align black" id="td_total_price_<?=$i?>"><?=number_format($curSalePrice)?>원</td>
                                    </tr>


                                </table>
                                <input type="hidden" name="hd_goods_no_<?=$i?>" value="<?=$GOODS_NO?>">
                                <input type="hidden" id="delivery_cnt_<?=$i?>" name="hd_delivery_cnt_<?=$i?>" value="<?=$DELIVERY_CNT_IN_BOX?>">
                                <input type="hidden" id="hd_total_price_<?=$i?>" value="<?=$SUM_PRICE?>">
                            </div>
                        </div><!--list_index-->
                    <?
                        }
                    ?>

                    <input type="hidden" name="mode">
                    <input type="hidden" name="cnt_checked_item" id="cnt_checked_item" value='0'>
                    <input type="hidden" name="cnt_wish" value="<?=$cntWish?>">
                        
                    
                </form>
                <form name="paramForm" method="POST">
                    <input type="hidden" name="paramMode">
                    <input type="hidden" name="hd_del_item_seq">
                </form>
            </div><!--content-->
        </div><!--background-->

    </body>
    
</html>


