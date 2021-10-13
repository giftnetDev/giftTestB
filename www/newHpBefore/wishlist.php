<? require "_common/home_pre_setting.php";?>
<?
    function deleteItem($db, $memberNo, $goodsNos){
        $query = "DELETE FROM T_WISH_LIST
                    WHERE   MEM_NO='$memberNo'
                    AND     GOODS_NO IN($goodsNos);
                    ";
            
        // echo "$query<br>";
        // exit;
        
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('wishlist_deletingItemError');</script>";
            exit;
        }
    }
?>
<?
    // print_r($_SESSION);
    // echo "SERVER_ADDR : ".$_SERVER['HTTP_HOST']."<br>";
    // echo "SERVER_REQUEST_URI : ".$_SERVER['REQUEST_URI']."<br>";


    $serverUri=$_SERVER['REQUEST_URI'];

    if($mode=="DELETE_ITEM"){
        $row_cnt=count($chk_no);


        $goodsNos="";

        for($k=0; $k<$row_cnt; $k++){
            $goodsNos.=$chk_no[$k].", ";
        }
        $goodsNos=rtrim($goodsNos, ", ");
        $memberNo=$_SESSION["C_MEM_NO"];

        // echo "goodsNos : ".$goodsNos."<br>";
        // exit;
        deleteItem($conn, $memberNo, $goodsNos);


    }

    if($mode =="CART"){
        $cart_seq=0;
        $use_tf='Y';

        if(!get_session('s_ord_no')){
            set_session('s_ord_no', getUniqueId($conn));
        }
        $s_ord_no = get_session('s_ord_no');

		$opt_sticker_no    = 0;
		$opt_sticker_msg   = "";
		$opt_outbox_tf     = "N";
		$opt_wrap_no       = 0;
		$opt_print_msg     = "";
		$opt_memo          = "";
		//$opt_outstock_date = trim($opt_outstock_date);
		//$delivery_type	   = trim($delivery_type);

		// echo "opt_sticker_no : ".$selectSticker."<br>";
		// exit;

		$delivery_type = "0"; //�ù� �⺻���� 
		$opt_outstock_date = date("Y-m-d", strtotime("1 day")); //��������� +1��

		$cate_01 = "";

		$arr_goods = selectGoods($conn, $goods_no);

		$price				 = $arr_goods[0]["PRICE"];
		$buy_price			 = $arr_goods[0]["BUY_PRICE"];
		$sticker_price		 = $arr_goods[0]["STICKER_PRICE"];
		$print_price		 = $arr_goods[0]["PRINT_PRICE"];
		//$sale_susu			 = $arr_goods[0]["SALE_SUSU"];
		$delivery_cnt_in_box = $arr_goods[0]["DELIVERY_CNT_IN_BOX"];
		$delivery_price		 = $arr_goods[0]["DELIVERY_PRICE"];
		$labor_price		 = $arr_goods[0]["LABOR_PRICE"];
		$other_price		 = $arr_goods[0]["OTHER_PRICE"];
		$buy_cp_no           = $arr_goods[0]["CATE_03"];
		
		$sa_delivery_price   = 0;
		$discount_price      = 0;
		$susu_price = 0;
		$sale_susu = 0;




    }//end of mode=="CART"


?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "header.php"; ?>
        <script>
            function js_delete_item_in_wishList(){

				var chkbox=$(".chk_no");
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
					alert('���õ� ��ǰ�� �����ϴ�');
					return;
				}
                frm.mode.value="DELETE_ITEM";
                frm.method="POST";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.target="";
                frm.submit();
            }
            function js_to_detail(){
                var goodsNo=$("input[name='hdGoodsNo']").val();
                location.href="sub_detail.php?goods_no="+goodsNo;

            }
            function js_close_memo(div){
                var d=div.parentElement;
                d.remove();
                return ;
            }
            function js_check_cnt(){
                var qty=Number($('.text_qty').val());
                var boxCnt=Number($('.span_deliveryCntInBox').html());
                if(qty<boxCnt){
                    $('.text_qty').val(boxCnt);
                    alert('�ڽ��Լ� �̾����δ� ��û�Ͻ� �� �����ϴ�');
                    return ;
                }
                var totalPrice=Number($("#hdSalePrice").val())*qty;
                totalPrice=js_add_comma(totalPrice);
                $('#total_sale_price').html(totalPrice);


            }
            
            function js_create_memo(goodsName){
                var now = new Date();
                var year=now.getFullYear();
                var month=now.getMonth()+1;
                var day=now.getDate();
                var hour = now.getHours();
                var min=now.getMinutes();

                var time=year+"-"+month+"-"+day+" "+hour+":"+min
                let str="";
                str+="<div class='tmpHistoryIndex'>";
                str+="<div class='closeHistory' onclick='js_close_memo(this)'>X</div>";
                str+=""+time+"<br>";
                str+=""+goodsName+"<br>";
                str+="<strong>īƮ�� �߰�</strong>";
                str+="</div>";

                $('.tmpHistoryList').append(str);


            }
            function js_cancel(){
                $("#option_popup").css("display","none");
            }
        	function js_add_comma(value){
				value=value+"";
				value=value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				return value;				
			}
            function js_cart(){
                let memberNo    =   $("input[name='hdMemberNo']").val();
                let goodsNo     =   $("input[name='hdGoodsNo']").val();
                let qty         =   $(".text_qty").val();
                let stickerNo   =   $("select[name='selectSticker']").val();
                //let optPrintMsg =   $("input[name='opt_pirint_msg']").val();
                let optPrintMsg =   $("textarea[name=opt_print_msg]").val();
                let goodsName   =   $(".h4_goodsName").html();
                goodsName=goodsName.trim();

                if(stickerNo===undefined){
                    stickerNo=0;
                }

                $.ajax({
                    url:"./ajax/ajax_process.php",
                    dataType:"JSON",
                    type:"POST",
                    async:true,
                    data:{
                        "mode":"WISHLIST_TO_SHOPPINGBAG",
                        "memberNo":memberNo,
                        "goodsNo":goodsNo,
                        "qty":qty,
                        "stickerNo":stickerNo,
                        "optPrintMsg":optPrintMsg,
                        "cpNo":"<?=$_SESSION["C_CP_NO"]?>"
                    },
                    success:function(data){
                        alert("��ٱ��Ͽ� ��ҽ��ϴ�");
                        var tmpCntCart=Number($("input[name='hdCntCart']").val());
                        tmpCntCart++;
                        $("input[name='hdCntCart']").val(tmpCntCart);
                        $(".detail").html(tmpCntCart);
                        js_cancel();
                        js_create_memo(goodsName);

                        if(tmpCntCart>0){
                            // $(".detail").addClass("detail_on");
                            $(".detail").css("background-color","rgb(232, 55, 138)");
                            $(".detail").css("color","#FFFFFF");

                        }
                        else{
                            // $(".detail").removeClass("detail_on");
                            $(".detail").css("background-color","#FFFFFF");
                            $(".detail").css("color","#000000");
                        }

                    },
                    error:function(jqXHR,textStatus, errorThrown){
                        alert("����");
                        js_cancel();

                    }
                });

            }

            function js_all_chk(){
                if(frm['chk_no[]'] != null){
                    if(frm['chk_no[]'].length != null){
                        if(frm.chk_all.checked == true){
                            for(i=0; i<frm['chk_no[]'].length; i++){
                                frm['chk_no[]'][i].checked = true;
                            }
                        }
                        else{
                            for(i=0; i<frm['chk_no[]'].length; i++){
                                frm['chk_no[]'][i].checked=false;
                            }
                        }//end of else
                    }//end of if(frm['chk_no[]'].length!=null)
                    else{
                        if(frm.chk_all.checked== true){
                            frm['chk_no[]'].checked=true;
                        }
                        else{
                            frm['chk_no[]'].checked=false;
                        }
                    }
                }//end of if(frm['chk_no[]'] != null)
            }
			function js_change_goods_qty(sign){

				var deliveryCntInBox=Number($('.span_deliveryCntInBox').html());
				var curQty=Number($('.text_qty').val());
				if(sign=='-'){
					if(curQty-1<deliveryCntInBox){
						alert('�ڽ��Լ� �̾����δ� ��û�Ͻ� �� �����ϴ�');
						return ;
					}
					curQty=curQty-1;
				}
				else if(sign=='+'){
					curQty=curQty+1;

				}
                $(".text_qty").val(curQty);

				var curPrice=Number($('#hdSalePrice').val());
				var totalPrice=curQty*curPrice;
                var totalPriceStr=js_add_comma(totalPrice);
                $("#total_sale_price").html(totalPriceStr);
                

                // $('#')
				
				// var goodsNo=$('#hdGoodsNo_'+idx).val();
				// alert(goodsNo);
				// var originalQty=$('#hdOriginalQty_'+idx).val();

				// if(curQty != originalQty){
				// 	$('#btnChangeQty_'+idx).attr("disabled",false);
				// }
				// else if(curQty == originalQty){
				// 	$('#btnChangeQty_'+idx).attr("disabled",true);
				// }

				// $('#strQty_'+idx).text(curQty);
				// totalPriceStr=js_add_comma(totalPrice)+"��";
				// $('#tdTotalPrice_'+idx).text(totalPriceStr);

			}//end of func
            function js_open_option_pop(goodsNo, memberNo){
                // alert('open');
                $.ajax({
                    url:'./ajax/ajax_process.php',
                    dataType:'JSON',
                    type:'POST',
                    async:true,
                    data:{
                        'mode':"GET_GOODS_INFORM",
                        'memberNo':memberNo,
                        'goodsNo':goodsNo

                    },
                    success:function(data){
                        $("#option_popup").css("display","block");
                        $(".span_goodsCode").html(data[0]["GOODS_CODE"]);
                        $(".h4_goodsName").html(data[0]["GOODS_NAME"]);
                        $("#sale_price").html(js_add_comma(data[0]["SALE_PRICE"]));
                        //$("input[name='opt_pirint_msg']").val('');
                        $("textarea[name=opt_print_msg]").val('');
                        $("#hdSalePrice").val(data[0]["SALE_PRICE"]);
                        $(".text_qty").val(data[0]["DELIVERY_CNT_IN_BOX"]);
                        $(".span_deliveryCntInBox").html(data[0]["DELIVERY_CNT_IN_BOX"]);
                        $("#total_sale_price").html(js_add_comma(data[0]["DELIVERY_CNT_IN_BOX"]*data[0]["SALE_PRICE"]));
                        $("input[name='hdGoodsNo']").val(goodsNo);
                        $("#hdSalePrice").val(data[0]["SALE_PRICE"]);

                        $(".product_pic").css("background","url("+data[0]["IMG_URL"]+") no-repeat");
                        
                        $(".product_pic").css("background-size","cover");
                        $(".product_pic").css("background-position","center");

                        var cnt=data[1].length;
                        // alert(cnt);
                        let str="";
                        if(cnt>0){
                            str="<SELECT name='selectSticker'><OPTION value='0'>��ƼĿ����</OPTION>";
                            for(i=0; i<cnt; i++){
                                str+="<OPTION value='"+data[1][i]["STICKER_NO"]+"'>"+data[1][i]["STICKER_NAME"]+"</OPTION>";
                            }       
                            str+="</SELECT>";

                            $(".td_stickerOption").html(str);
                        }
                        else{
                            $(".td_stickerOption").html("��ƼĿ �ɼ� ����");
                        }
                        // alert(str);


                    },
                    error:function(jqXHR, textStatus, errorThrown){
                        alert('����');
                    }
                });
            }
        </script>
    </head>
    <style>
        #option_popup{
            display: none;
        }

    </style>
    <body>
        <? require "top.php"; ?>
        <div class="detail_page">
            <div class="detail_page_inner">
                <div class="cart_info">
                    <h4>Wish List</h4>
                    <input type="hidden" name="hdMemberNo" value="<?=$memberNo?>">
                    <form name="frm" class="form-horizontal in-signin" method="POST">
                        <input type="hidden" name="mode" value="">
                        <table class="w_list_table">
                            <caption>Wish List</caption>
                            <tr>
                                <th><input type="checkbox" name="chk_all" onclick="js_all_chk()"></th>
                                <th>��ǰ����</th>
                                <th>��ǰ����</th>
                                <th>�ǸŰ�</th>
                                <th>�ּ��ֹ�</th>
                                <th>��ٱ���</th>
                            </tr>
                            <?
                                for($i=0; $i<$cntLike; $i++){
                                    $goodsNo    =       $wishList[$i]["GOODS_NO"];
                                    $goodsCode  =       $wishList[$i]["GOODS_CODE"];
                                    $goodsName  =       $wishList[$i]["GOODS_NAME"];
                                    $imgUrl     =       $wishList[$i]["IMG_URL"];
                                    $salePrice  =       $wishList[$i]["SALE_PRICE"];
                                    $deliveryCntInBox = $wishList[$i]["DELIVERY_CNT_IN_BOX"];

                                    ?>
                                    <tr>

                                        <td><input type="checkbox" name="chk_no[]" class="chk_no" value="<?=$goodsNo?>"></td>
                                        <td>
                                            <a href="sub_detail.php?goods_no=<?=$goodsNo?>">
                                                <div class="thumb" style="background:url('<?=$imgUrl?>') no-repeat;background-size:cover; background-position:center;"></div>
                                            </a>
                                        </td>
                                        <td>
                                            <?=$goodsName?>
                                        </td>
                                        <td style="text-align: right;">
                                            <?=number_format($salePrice)?> ��
                                        </td>
                                        <td>
                                            <?=$deliveryCntInBox?>
                                        </td>
                                        <td>
                                            <button type="button" class="gray cursor-pointer" onclick="js_open_option_pop('<?=$goodsNo?>','<?=$memberNo?>')" >��� </button>												
                                        </td><!--����-->
                                    </tr>
                                <?
                                }
                            ?>
                        </table>
                        <div class="alignRight">

                            <button class="btnRed" type="button" onclick="js_delete_item_in_wishList()">����ǰ�� ����</button>

                        </div>


                    </form><!--form-horizontal in-signin"-->
                </div><!--cart_info-->
            </div><!--detail_page_inner-->
        </div><!--detail_page-->
        <? require "./footer.php"; ?>
        <form name="optFrm" id="frmOption">
            <input type="hidden" name="mode">
            <input type="hidden" name="curUrl" value="Newindex.php">
            <div id="option_popup">
                <div class="dark_wall"></div>
                <div class="option_pop">
                    <div class="opt_pop_x">X</div>
                    <div class="cart_info">
                        <div class="product_info_pop">                        
                            <span class="span_goodsCode" style="text-align: left;">��ǰ�ڵ�</span>
                            <h4 class="h4_goodsName">��ǰ�̸�</h4>
                                <div class="product_pic" onclick="js_to_detail()" style="background:url('<?=$img_url?>')cursor:pointer;"></div>
                                <table>
                                    <caption>��ǰ��</caption>
                                    <tr>
                                        <th>�ǸŴܰ�</th>
                                        <td><span><strong id="sale_price" data-sale_price=<?=$salePrice?>">��ǰ����</strong>��</td>
                                        
                                    </tr>
                                    <tr>
                                        <th>�ֹ�����</th>
                                        <td>
                                            <div class="count_up_down">
                                                <div class="count_up_down_down" onclick="js_change_goods_qty('-')"> - </div>
                                                <input type="text" name="qty" class="text_qty" style="width:50px; border:none; height:30px; text-align: center; font-size: 16px;" value="qty" autocomplete="off" onfocusout="js_check_cnt();"/>
                                                <div class="count_up_down_up" onclick="js_change_goods_qty('+')"> + </div>
                                            </div><!--count_up_down-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>�ڽ��Լ�</th>
                                        <td><span class="span_deliveryCntInBox">�ڽ��Լ�</span>��</td>
                                    </tr>
                                    <tr>
                                        <th>��ƼĿ �ɼ�</th>
                                        <td class="td_stickerOption">
                                            //��ƼĿ�ɼ� SELECT_BOX
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>�μ�(����������)<br>�߰����</th>
                                        <td>
                                            <!--<span><strong><input type="text" name="opt_pirint_msg" value="<?=$opt_print_msg?>"></strong></span>-->
                                            <textarea cols="20" style="width:98%; white-space: pre-wrap; border: 1px solid #d4d4d4;" rows="5" id="opt_print_msg" name="opt_print_msg" maxlength="200" wrap="hard"></textarea>
                                        </td>

                                    </tr>
                                
                                    </div>
                                </table>

                                <div class="price_pop" style="text-align-last: right;">
                                    �հ�ݾ�
                                    <i><strong id="total_sale_price" style="color:#50b22e">test</strong>��</i>
                                    <input type="hidden" id="hdSalePrice" value="">
                                </div><!--price_pop-->   
                        </div><!--product_info_pop-->
                        <div class="clear"></div>
                        <div class="button_zone">
                            <a style="cursor:pointer" id="btn-cart" class="cart" onclick='js_cart()' >��ٱ���</a>
                            <a style="cursor:pointer" id="btn-cancel" class="cart" onclick="js_cancel()" >���</a>
                            <input type="hidden" name="hdGoodsNo">
                            <input type="hidden" id="hdSalePrice">
                            <input type="hidden" name="hdCntCart" value="<?=$cntCart?>">
                        </div>
                    </div><!--cart_info-->
                </div><!--option_pop-->
            </div><!--option_popup-->



        </form><!--optFrm-->

        <style>
			table.w_list_table tr td:nth-of-type(2) {width:60px;}
			table.w_list_table tr td:nth-of-type(2) div { position:relative;width:100%;height:70px;left:0px;top:0px;bottom:0px;right:0px; }
			table.w_list_table tr td:nth-of-type(3) { width:500px; text-align: left;}
            
		</style>
        <div class="tmpHistoryList">
        </div><!--tmpHistoryIndex-->



    </body>
</html>
