<?require "../_common/home_pre_setting.php";  ?>
<?
    //스티커 옵션과 프린트 옵션이 있는 경우 동적으로 list_index의 height값을 변화시켜준다

    function getStickerNameByStickerNo($db, $stickerNo){
        $query = "SELECT    GOODS_NAME
                    FROM    TBL_GOODS
                    WHERE   GOODS_NO = '".$stickerNo."' ;
                       ";
        $result=mysql_query($query, $db);

        if(!$result){
            return "";
        }
        else{
            $rows=mysql_fetch_row($result);
            return $rows[0];
        }
    }//end of function

    function deleteGoodsInCart($db, $memberNo, $cartNos){
        $query="DELETE FROM TBL_CART
                WHERE MEM_NO= '$memberNo'
                AND     CART_NO IN($cartNos)
                ";
        
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('M_Delete Error');</script>";
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
    else{
        // echo "C_MEM_NO : ".$_SESSION['C_MEM_NO']."<br>";
        // print_r($_SESSION);

        $s_ord_no=get_session('s_ord_no');
        $cp_no=$_SESSION['C_CP_NO'];
        $mem_no=$_SESSION['C_MEM_NO'];
    }

?>
<?


    if($mode=="DELETE_CART"){
        $query= "   DELETE FROM TBL_CART
                    WHERE       CART_NO='".$hd_del_cart_no."'
        ";
        $result=mysql_query($query, $conn);
        if(!$result){
            echo"<script>alert('DELETE_CART_ERROR');</script>";
            exit;
        } 

    }
    if($contentMode=="SAVE_MODIFIED_QTY"){
        $cntGoods=$_POST["hd_goods_cnt"];
        for($i=0;$i<$cntGoods; $i++){
            $CART_NO        = $_POST["hd_cart_no_".$i];
            $QTY            = $_POST["hd_qty_".$i];
            $GOODS_NO       = $_POST["hd_goods_no_".$i];
            $DELIVERY_CNT   = $_POST["hd_delivery_cnt_".$i];

            $query="UPDATE TBL_CART
                    SET     QTY     =   '$QTY'
                    WHERE   GOODS_NO='$GOODS_NO'
                    AND     DELIVERY_CNT_IN_BOX     <='$QTY'
                    AND     CART_NO = '$CART_NO'
            ";

            $result=mysql_query($query, $conn);
        }

    }
    //
    $arr_rs=listCartByMemNo($conn, $s_ord_no, $cp_no, $mem_no, 'Y', 'N', "ASC");
    $cntArr=sizeof($arr_rs);
    if($cntArr<1){
        echo "<script>alert('장바구니에 담겨있는 상품이 없습니다');</script>";
        ?>
        <script>
            location.href="./Mindex.php";
        </script>
        <?
        exit;
    }
    // print_r($arr_rs);
    // exit;

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./Mheader.php"; ?>
        <script>
            let g_modified='N';
            // var h= innerHeight;
            // var w= innerWidth;
            function js_add_comma(value){
				value=value+"";
				value=value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				return value;
				
			}

            function js_modify_qty(sign, idx){
                let qty=$("#hd_qty_"+idx).val()-0;
                let deliveryCnt=$("#delivery_cnt_"+idx).val();
                if(sign=='-'){
                    if(qty<=deliveryCnt){
                        alert('박스입수 아래로 주문하실 수 없습니다. 문의주시기 바랍니다');
                        $("#txt_qty_"+idx).val(deliveryCnt);
                        $("#hd_qty_"+idx).val(deliveryCnt);
                        return;
                    }
                    $("#txt_qty_"+idx).val(qty-1);
                    $("#hd_qty_"+idx).val(qty-1);
                }
                else{
                    if(qty<deliveryCnt){
                        alert('박스입수 아래로 주문하실 수 없습니다. 문의주시기 바랍니다');
                        $("#txt_qty_"+idx).val(deliveryCnt);
                        $("#hd_qty_"+idx).val(deliveryCnt);
                        return;
                    }
                    $("#txt_qty_"+idx).val(qty+1);
                    $("#hd_qty_"+idx).val(qty+1);
                }
                
                
                let total_price=js_add_comma(Number($("#hd_qty_"+idx).val())*Number($("#hd_sale_price_"+idx).val()));
                total_price+="원";
                $("#td_total_price_"+idx).html(total_price);
                $("#hd_total_price_"+idx).val(Number($("#hd_qty_"+idx).val())*Number($("#hd_sale_price_"+idx).val()));
                let cnt=$("#hd_goods_cnt").val();
                let sum_total_price=0;

                for(i=0; i<cnt; i++){
                    let tmpPrice=$("#hd_total_price_"+i).val();
                    sum_total_price+=Number($("#hd_total_price_"+i).val());
                }
                $("#span_sum_total_price").html(js_add_comma(sum_total_price)+" 원");
                // alert(sign);
                if(g_modified=='N'){
                    g_modified='Y';
                    $("#span_save_modified_qty").css('display','block');

                }
            }//end of function
            function js_inbox_select(idx){
                // alert($("#inbox_checked_"+idx).val());
                if($("#inbox_checked_"+idx).val()=='N'){
                    $("#inbox_select_"+idx).css("background","url('./img/chkBoxY.png') no-repeat");
                    $("#inbox_select_"+idx).css("background-size","100% 100%");
                    // $("#inbox_select_"+idx).css("background-position","center center");
                    $("#inbox_checked_"+idx).val('Y');
                }
                else if($("#inbox_checked_"+idx).val()=='Y'){
                    $("#inbox_select_"+idx).css("background","url('./img/chkBoxN.png') no-repeat");
                    $("#inbox_select_"+idx).css("background-size","100% 100%");
                    // $("#inbox_select_"+idx).css("background-position","center center");
                    $("#inbox_checked_"+idx).val('N');
                }
                

                
            }
            function js_order(){
                let cntGoods=Number($("#hd_goods_cnt").val());
                let cntCheckedOrder=0;
                for(i=0; i<cntGoods;i++){
                    if($("#inbox_checked_"+i).val()=='Y'){
                        cntCheckedOrder++;
                    }
                }
                if(cntCheckedOrder<1){
                    alert('선택된 상품이 없습니다');
                    return;

                }

                
                if(g_modified=='Y'){
                    alert('"< 저장" 버튼을 누르셔서 수량변경을 저장하신 후 진행해 주시기 바랍니다');
                    $("#span_save_modified_qty").focus();
                    return;
                }

                var frm=document.frmContent;
                frm.action="./Morder_process.php";
                frm.target="";
                frm.method="POST";
                frm.submit();
                
            }
            function js_cancel(cartNo, idx){
                if(!confirm("해당 상품을 장바구니에서 빼시겠습니까?")){
                    return;
                }
                var frm=document.frm;
                frm.mode.value="DELETE_CART";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.target="";
                frm.method="POST";
                $("input[name='hd_del_cart_no']").val(cartNo);
                frm.submit();
            }
            function js_save(){
                var frm=document.frmContent;
                frm.contentMode.value="SAVE_MODIFIED_QTY";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.method="POST";
                frm.target="";
                frm.submit();
            }


        </script>
    </head>
    <body>
        <!-- <header style="width:100%;height:60px;background:black;position:fixed;top:0px;left:0px;text-align:center;z-index:9;">

        </header> -->

        <div class="background">
            <div class="title_line">
                    <span class="left_button" id="span_save_modified_qty" onclick="js_save()" style="display:none;">< 저장</span>
                    <span class="page_title">장바구니</span>
                    <span class="right_button" onclick="js_order()">주문 ></span>
            </div>
            <div class="content">
                <form name="frmContent">
                    <input type="hidden" name="contentMode">
                    <!-- <input type="hidden" name="contentMode"> -->
                    <h3>장바구니</h3>
                    <?  
                        
                        for($i=0; $i<$cntArr; $i++){
                            $CART_NO                    =   trim($arr_rs[$i]["CART_NO"]);
                            $ON_UID                     =   trim($arr_rs[$i]["ON_UID"]);
                            $GOODS_NO                   =   trim($arr_rs[$i]["GOODS_NO"]);
                            $GOODS_CODE                 =   trim($arr_rs[$i]["GOODS_CODE"]);
                            $GOODS_NAME                 =   trim($arr_rs[$i]["GOODS_NAME"]);
                            $QTY                        =   trim($arr_rs[$i]["QTY"]);
                            $BUY_PRICE                  =   trim($arr_rs[$i]["BUY_PRICE"]);
                            $PRICE                      =   trim($arr_rs[$i]["PRICE"]);
                            $SALE_PRICE                 =   trim($arr_rs[$i]["SALE_PRICE"]);
                            $CUR_SALE_PRICE				=   trim($arr_rs[$i]["CUR_SALE_PRICE"]);
                            $EXTRA_PRICE				=   trim($arr_rs[$i]["EXTRA_PRICE"]);
                            $DELIVERY_PRICE				=   trim($arr_rs[$i]["DELIVERY_PRICE"]);
                            $DISCOUNT_PRICE				=   trim($arr_rs[$i]["DISCOUNT_PRICE"]);
                            $SA_DELIVERY_PRICE			=   trim($arr_rs[$i]["SA_DELIVERY_PRICE"]);
                            $DELIVERY_CNT_IN_BOX		=   trim($arr_rs[$i]["DELIVERY_CNT_IN_BOX"]);

                            $IMG_URL					=   trim($arr_rs[$i]["IMG_URL"]);
                            $FILE_NM					=   trim($arr_rs[$i]["FILE_NM_100"]);
                            $FILE_RNM					=   trim($arr_rs[$i]["FILE_RNM_100"]);
                            $FILE_PATH					=   trim($arr_rs[$i]["FILE_PATH_100"]);
                            $FILE_SIZE					=   trim($arr_rs[$i]["FILE_SIZE_100"]);
                            $FILE_EXT					=   trim($arr_rs[$i]["FILE_EXT_100"]);
                            $FILE_NM_150				=   trim($arr_rs[$i]["FILE_NM_150"]);
                            $FILE_RNM_150				=   trim($arr_rs[$i]["FILE_RNM_150"]);
                            $FILE_PATH_150			    =   trim($arr_rs[$i]["FILE_PATH_150"]);
                            $FILE_SIZE_150			    =   trim($arr_rs[$i]["FILE_SIZE_150"]);
                            $FILE_EXT_150				=   trim($arr_rs[$i]["FILE_EXT_150"]);

                            $CATE_01					=   trim($arr_rs[$i]["C_CATE_01"]);

                            $OPT_STICKER_NO				=   trim($arr_rs[$i]["OPT_STICKER_NO"]);
                            $OPT_OUTBOX_TF				=   trim($arr_rs[$i]["OPT_OUTBOX_TF"]);
                            $OPT_WRAP_NO				=   trim($arr_rs[$i]["OPT_WRAP_NO"]);
                            $OPT_STICKER_MSG			=   trim($arr_rs[$i]["OPT_STICKER_MSG"]);
                            $OPT_PRINT_MSG				=   trim($arr_rs[$i]["OPT_PRINT_MSG"]);
                            $OPT_OUTSTOCK_DATE			=   trim($arr_rs[$i]["OPT_OUTSTOCK_DATE"]);

                            if($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00"){
                                $OPT_OUTSTOCK_DATE  =date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));
                            }
                            $OPT_MEMO               =   trim($arr_rs[$i]["OPT_MEMO"]);

                            $OPT_OUTBOX_TF          =   ($OPT_OUTBOX_TF == "Y"? "있음" : "");

                            $OPT_OUTSTOCK_DATE      =   ($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? $OPT_OUTSTOCK_DATE : "출고미정");

                            $priceChangeTF='N';

                            if($CUR_SALE_PRICE <> $SALE_PRICE){
                                $priceChangeTF='Y';
                            }

                            if($CATE_01 <> "")
                                $str_cate_01 = $CATE_01.") ";
                            else 
                                $str_cate_01 = "";

                            $SUM_PRICE = ($QTY * $SALE_PRICE);// + $SA_DELIVERY_PRICE - $DISCOUNT_PRICE;

                            $TOTAL_QTY = $TOTAL_QTY + $QTY;

                            //if($CATE_01 == "") //2016-12-21 샘플, 증정 주문서 금액에 다시 추가
                            $TOTAL_SUM_PRICE = $TOTAL_SUM_PRICE + $SUM_PRICE;

                            $img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");
                            
                            $REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);

                            // echo "OPT_STICKER_NO : ".$OPT_STICKER_NO."<br>";

                            $OPT_STICKER_NAME=getStickerNameByStickerNo($conn, $OPT_STICKER_NO);
                            ?>
                                <div class="list_index" id="div_cart_list_<?=$i?>">
                                    <div class="inbox_icon" style="background-color: #E5E5E5" onclick="js_cancel('<?=$CART_NO?>','<?=$i?>');">X</div>
                                    <div class="inbox_icon" id="inbox_select_<?=$i?>" onclick="js_inbox_select('<?=$i?>')" style="background:url('./img/chkBoxN.png') no-repeat;background-size:100% 100%; background-position:center center; width:30px;height:30px;"></div>
                                    <input type="hidden" id="inbox_checked_<?=$i?>" name="hd_inbox_checked_<?=$i?>" value='N'>
                                    <input type="hidden" name="hd_cart_no_<?=$i?>" value='<?=$CART_NO?>'>
                                    <div class="inbox_index_title" id="index_goods_name">
                                        <?=$GOODS_NAME?>
                                    </div>
                                    <table style="width:96%; margin:2%; text-align: center;">
                                        <colgroup>
                                            <col width="30%">
                                            <col width="25%">
                                            <col width="45%">
                                        </colgroup>
                                        <tr>
                                            <td rowspan="3">
                                                <div style="background:url('<?=$img_url?>') no-repeat;background-size:100% 100%; background-position:center center; width:90px;height:90px; margin-left:4%;"></div>
                                            </td>
                                            <td class="key right_align">가격  </td>
                                            <td class="value right_align"><?=number_format($SALE_PRICE)?>원</td>
                                            <input type="hidden" id="hd_sale_price_<?=$i?>" value="<?=$SALE_PRICE?>">
                                        </tr>
                                        <tr>
                                            <td class="key right_align">개수  </td>
                                            <td class="value" >
                                                <div class="inbox_index_qty">
                                                    <div class="inbox_decrease_qty" onclick="js_modify_qty('-', '<?=$i?>');">-</div><div class="inbox_div_qty"><input type="text" id="txt_qty_<?=$i?>" disabled value="<?=$QTY?>"></div><div class="inbox_increase_qty" onclick="js_modify_qty('+','<?=$i?>');">+</div>
                                                </div>
                                                <input type="hidden" id="hd_qty_<?=$i?>" name="hd_qty_<?=$i?>" value="<?=$QTY?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="key right_align black">총 가격  </td>
                                            <td class="value right_align black" id="td_total_price_<?=$i?>"><?=number_format($SALE_PRICE*$QTY)?>원</td>
                                        </tr>


                                    </table>
                                    <input type="hidden" name="hd_goods_no_<?=$i?>" value="<?=$GOODS_NO?>">
                                    <input type="hidden" id="delivery_cnt_<?=$i?>" name="hd_delivery_cnt_<?=$i?>" value="<?=$DELIVERY_CNT_IN_BOX?>">
                                    <input type="hidden" id="hd_total_price_<?=$i?>" value="<?=$SUM_PRICE?>">
                                    
                                </div><!--list_index-->
                        <?
                        }//end of for()
                    ?>
                    <input type="hidden" id="hd_goods_cnt" name="hd_goods_cnt" value="<?=$cntArr?>">
                <!-- <div class="testBu" -->
                
                </form><!--frmContent-->
            </div><!--content-->
            <div class="end_line">
                <span class="line_left">구매 예상 가격 : </span>
                <span class="line_right" id="span_sum_total_price"><?=number_format($TOTAL_SUM_PRICE)?></span>
            </div>
        </div><!--background-->
        <form name="frm">
            <input type="hidden" name="hd_del_cart_no">
            <input type="hidden" name="mode">
        </form>
    </body>

    <script>
        
        // $(document).ready(function(){
        //     var w=innerWidth;
        //     var h=innerHeight;
        //     alert(w);
        //     alert(h);

        // });
    </script>

</html>