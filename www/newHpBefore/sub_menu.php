<?
    require "_common/home_pre_setting.php";

    $mem_no=$_SESSION['C_MEM_NO'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "header.php";
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
</script>
</head>
<body>
<div class="wrap">
<?
	require "top.php";

    require "sub_category.php";
    
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
    
        $del_tf = "N";
    
        $con_cate = $cate;
    
        $is_catalog = startsWith($con_cate, getDcodeExtByCode($conn, "HOME_BANNER", "BANNER_CATE" ));
    
        if($is_catalog) { 
    
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
            
        } else { 
        
            $arr_options["code_cate"]=$code_cate;        
            $arrGoods 		= listHomepageGoods($conn, $search_field, $search_str, $arr_options, $order_field, $order_str, 1, 100, 1000);
        
        }
    
        if($search_str <> "")
            $search_field = "ALL";
?> 
        <div class="play_button_to_the_first">&lt;&lt;</div>
		<div class="play_button_prev">&lt;</div>
		<div class="play_button_next">&gt;</div>  
        
        <div class="filter">
        
                <a id = "sort1" href="javascript:js_order_by('REG_DATE', 'DESC', 'sort1');" class="<?if(($order_field== "REG_DATE" || $order_field== "") && ($order_str == "DESC" || $order_str == "")) ?>">신제품순</a> &middot; 
                <a id = "sort2" href="javascript:js_order_by('SALE_PRICE', 'ASC', 'sort2');" class="<?if($order_field== "SALE_PRICE" && $order_str == "ASC") ?>">낮은 가격순</a> &middot; 
                <a id = "sort3" href="javascript:js_order_by('SALE_PRICE', 'DESC', 'sort3');" class="<?if($order_field== "SALE_PRICE" && $order_str == "DESC") ?>">높은 가격순</a>  &middot; 
                <a id = "sort4" href="javascript:js_order_by('GOODS_NAME', 'ASC', 'sort4');" class="<?if($order_field == "GOODS_NAME" && $order_str == "ASC") ?>">상품명순</a>

        </div><!--class="filter"-->
        <div class="wrapper wrapper_sub">
            <?
                $cntGoods=sizeof($arrGoods);
                if($cntGoods>0){
                    for($j=0; $j<$cntGoods; $j++){
                        
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
                        // if($_SESSION['C_CP_NO'] && $_SESSION['C_CP_NO']<>""){
                        //     $SALE_PRICE=getCompanyGoodsPriceOrDCRate($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150,"150","150");
                        // }

                        if($j%2==0){
                        ?>
                            <div class="wrapper_cell_sub">
                            <div>
                        <?
                        }
                        ?>
                                <div class="wrapper_cell_inner_cell_sub">
                                    <!-- <div class="cart"></div> -->
                                    <div class="div"></div>
                                    <?
                                        if($tmpCntLike>0 && $memberNo>0){
                                            if(isExistingAtWishList($conn, $GOODS_NO, $memberNo)>0){
                                            ?>
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
                                    <!-- <div class="like"></div> -->

                                    <dl onclick="location.href='/sub_detail.php?goods_no=<?=$GOODS_NO?>'">
                                    <div class="img"><img src="<?=$img_url?>" alt=""></div>
                                    <div class="text">
                                        <span><?=$GOODS_CODE?></span><br>
                                        <b><?=$GOODS_NAME?></b>
                                        <?
                                            if($mem_no <> "")
                                            {	
                                            ?>
                                                <i><em><?=number_format($SALE_PRICE)?></em> 원</i>
                                            <?
                                            }
                                            else
                                            {	
                                                if($concel_tf != "Y")
                                                {   
                                            ?>      <i><em><?=number_format($SALE_PRICE)?></em> 원</i>
                                            <?	}
                                                else
                                                {   
                                                ?>
                                                    <i><em>가격문의</em></i>
                                            <?
                                                }
                                            } 
                                        ?>  
                                    </div>
                                    </dl>
                                </div><!--class="wrapper_cell_inner_cell_sub"-->

                        <?
                        if($j%2==1){
                        ?>
                            </div>
                            </div><!--class="wrapper_cell_sub-->
                        <?
                        }
                    }//end of for(cntGoods)
                }//end of if(cntGoods>0)
                else
                {   ?>
                    <div class="no_result">리스트가 없습니다.</div>
                <?
                }

                if($cntGoods%2==1){//예외처리 : 상품의 총 수가 홀수개인 경우 마지막에 "닫기코드"가 실행이 되지않는다. 이것을 보안하기 위해 이 코드(if($cntGoods%2=1){})삽입
                ?>
                            <div class="wrapper_cell_inner_cell_sub_dummy">
                                
			
			                </div>
                        </div>
                    </div><!--class="wrapper_cell_sub-->
                <?
                }//end of if($cntGoods%2==1)
            ?>
            
            <div class="wrapper_cell_sub">
				<!--empty pannel for padding-->
			</div>
        </div><!--class="wrapper wrapper_sub-->
</div>            
<?
	require "footer.php";
?>   
        <form name="frm">
            <input type="hidden" name="mode">
            <input type="hidden" name="curUrl" value="Newindex.php">
            <div id="login_popup">
                <div class="dark_wall"></div>
                <div class="login_pop">
                    <div class="login_pop_x">X</div>
                    <div class="cart_info">
                        <br>
                        <h2>
                            Login / Join
                        </h2>
                        <div class="tcenter" style="margin-top:40px;">
                            <input type="text" placeholder="아이디" class="id" name="iid"><br>
                            <input type="password" placeholder="패스워드" class="pw" name="pwd"><br>
                            <div class="tcenter_for_check"><input type="checkbox" id="id_save"> <label for="id_save" style="cursor:pointer;">아이디 저장</label></div>
                            <a href="javascript:js_login();" class="joomoon login_run">로그인</a>
                    </div>
                    <div class="tcenter_02" style="margin-top:-40px;">
                        <a href="#">아이디 / 비밀번호 찾기</a>
                    </div>
                                
                </div><!--login_pop-->
            </div>

        </form>     
        <script>
            $(function() {
                $(".wrapper").mousewheel(function(event, delta) {
                    this.scrollLeft -= (delta * 120);
                    event.preventDefault();
                });
                   
                $("#main_<?=substr($code_cate, 0, 1)?>").closest("li").addClass("menu_on");
                $("#<?=$sort?>").addClass("now_bold");
            })		
        </script>
        <script src="js/jquery.mousewheel.js"></script>
        <script type="text/javascript">
            document.addEventListener("mousemove", parallax);
                function parallax(e) {
                
                    this.querySelectorAll('.elements').forEach(Layer => {
                    const speed = Layer.getAttribute('data-speed');
                    const x = (window.innerWidth - e.pageX*speed)/100;
                    Layer.style.transform = 'translateX(${x}px)';
                })
            }
        </script>
    </body>
</html>
