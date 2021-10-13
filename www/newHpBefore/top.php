<?
    function cntCart($db, $memberNo, $cpNo){
        $query= "   SELECT COUNT(CART_NO) AS CNT
                    FROM    TBL_CART 
                    WHERE   MEM_NO = '$memberNo'
                    AND     TBL_CART.USE_TF='Y'
                    AND     TBL_CART.DEL_TF='N'
                    AND     TBL_CART.CP_NO='$cpNo'
                    ";
        // echo $query."<br>";
        // exit;
        
        $result=mysql_query($query, $db);

        if($result){
            $rows=mysql_fetch_row($result);
            return $rows[0];
        }
        else{
            echo"<script>alert('error');</script>";
        }
    }
?>
<?
    print_r($_SESSION);
    $httpHost=$_SERVER['HTTP_HOST'];
    echo "HTTP_HOST : ".$httHost."<br>";

	echo "C_MEM_NO :".$_SESSION['C_MEM_NO'].", ";
	echo "C_MEM_NM :".$_SESSION['C_MEM_NM'].", ";
	echo "C_MEM_ID :".$_SESSION['C_MEM_ID'].", ";
	echo "C_CP_NO :".$_SESSION['C_CP_NO'].", ";
	echo "C_CP_NM :".$_SESSION['C_CP_NM']."<br/>";

    // print_r($_SESSION);


    $arrTopMenu=listTopMenus($conn, null);
?>
<?//mode
    if($mode=="LOGOUT"){
        $_SESSION=Array();
        setcookie(session_name(),'',time()-42000);
        session_destroy();

    }
    ?>
<?
    $cntLike=0;
    $tmpCntLike=0;
    if($_SESSION['C_MEM_NO'] &&$_SESSION['C_MEM_NO']>0){
        $memberNo=$_SESSION['C_MEM_NO'];
        $cpNo=$_SESSION["C_CP_NO"];
        $wishList=listWishList($conn, $memberNo);
        $cntLike=sizeof($wishList);
        $tmpCntLike=$cntLike;
        $cntCart=cntCart($conn,$memberNo,$cpNo);
    }
?>
    <script>

        function js_goto_shoppingback(){
            let sessionNo=Number(sessionName=$('input[name=sessionName]').val());
            if(sessionNo<1){
                // alert('로그인X');
                location.href="log-in.php";
                
            }
            else{
                // alert('로그인O');
                location.href="shoppingbag.php";
            }

        
        }

        function js_logout(){
            // alert('logout');            
            if (!confirm("로그아웃 하시겠습니까?")) return;	
            var frm=document.frm1;
            frm.mode.value="LOGOUT";
            frm.method="POST";
            //frm.action="<?=$_SERVER['PHP_SELF']?>"
            frm.action="Newindex.php";
            frm.target="";
            frm.submit();

        }
    </script>
    <style>
        .like_region{

            position:relative;
            top:75px;
            left:10px;
            /* width:100%; */
        }
        .like_item{
            background-color: rgba(0,0,0,0.4);
            width:100px;
			height:100px;
            padding:5px;
            /* padding-left: 20px; */
            /* padding-right:20px; */
            margin:3px;
            border-radius: 10px;
            position:relative;
			right:5px;
        }
		.like_item img { border-radius:7px; }
		.like_item:hover { background:#000 }
        .like_item div.like_item_description{
            z-index:10000; 
            display: none; 
            overflow: auto; 
            width:240px;
            height:auto;
			color:black;
			line-height:30px;
			padding-left:20px;

			padding-top:5px;
			padding-bottom:10px;
			font-weight:bold;
            background:rgba(90, 90, 90, 0.7);
            /* box-shadow:4px 4px 10px rgba(0,0,0,0.3); */
            box-sizing:content-box;
            position:absolute;
            top:2px;
            right:103px;
			white-space:nowrap;text-overflow:ellipsis;overflow:hidden;
			color:white !important;
        } 
		.like_item div div.like_close { width:20px;
                                        height:20px;
                                        border-radius:50%;
                                        background:#444;
                                        position:absolute;
                                        top:-2px;
                                        right:-2px;
                                        color:white;
                                        cursor:pointer;
                                        line-height:20px;
                                        text-align:center;
                                        font-size:10px;
                                        font-weight:bold; }
		.like_item div div.like_close:hover { background:black !important; }
    </style>

    <header>
        <a href="/Newindex.php" class="logo"></a>
        <ul>
        <?
            $cntArrTopMenu=sizeof($arrTopMenu);
            if($cntArrTopMenu>0){
                for($i=0; $i<$cntArrTopMenu; $i++){
                    $MENU_NAME  =   $arrTopMenu[$i]["CATE_NAME"];
                    $CODE_CATE  =   $arrTopMenu[$i]["CATE_CODE"];

                ?>
                    <li id = "main_<?=substr($CODE_CATE, 0, 1)?>">
                    <a href="/sub_menu.php?code_cate=<?=$CODE_CATE?>&sort=sort1"><?=$MENU_NAME?></a>
                    </li>
                <?
                }
            }
        ?>
        </ul>
        <div class="col-sm-4 col-lg-5 col-xs-12 text-center" id="search">
			<form role="search" action="/search_word.php" method="get" name="frm1">
                <div class="search">
                    <!--<input type="text" placeholder="Search"><button></button>-->
                    <input type="text" name="search_str" placeholder="<?=($search_str == "" ? "" : $search_str)?>">
                    <input type="hidden" name="CNT" value="<?=$CNT?>" />
                    <input type="hidden" name="sessionNo" value="<?=$_SESSION['C_MEM_NO']?>">
                    <input type="hidden" name="mode">
                    <button type="submit"></button>
                </div>
					<!-- </div> -->

            <?
                if($_SESSION['C_MEM_NO'] && $_SESSION['C_MEM_NO']>0){
                ?>
                    <a href="#" class="user_name"><b><?=$_SESSION['C_MEM_NM']?></b>님
                        <span class="user_name_detail" style="display:none;">
                            <div onclick="js_move_page('my_page.php')">My Page</div>
                            <div onclick="js_move_page('wishlist.php')">Wish List</div>
                            <div onclick="js_move_page('delivery_confirm.php')">주문 / 배송조회</div>
                        </span>

                    </a>
                    <a href="javascript:js_logout()" class="logout_btn"><img src="img/logout.png" alt=""> Logout</a>
                <?
                }
                else{
                ?>
			         <a href="/log-in.php" id="member_inform" class="login_btn"><img src="img/icon_login.png" alt=""> Login</a>
                     <a href="/register.php" class="join_btn"><img src="img/icon_join.png" alt=""> Join</a>
                <?
                }
            ?>


                <div class="cart_btn" onclick="js_goto_shoppingback()"><div class="detail">0</div>
            </form>
        </div>
    </header>
    <?php
        $likeRegionWidth=$cntLike>0? 110:0;
        $likeRegionPaddingBottom=$cntLike>0?10:0;
        $likeRegion_scroll=$cntLike>5? "overflow:hidden; overflow-y:auto":"";
    ?>


    <?
        // echo "SERVER_URI in top : ".$serverUri."<br>";
        if($_SESSION["C_MEM_NO"]<>"" && strpos($serverUri, "wishlist")==false){
            // echo "NOT WISHLIST<br>";
            echo "SESSION!!!!! : ".$_SESSION["C_MEM_NO"]."<br>";

    ?>
            <div class="like_zone">


                <div class="like_count_link"><img id="imgLike" src="img/like_c.png" alt=""> <a class="like_cnt" href="wishlist.php"><?=$cntLike?></a>   
                </div>
                <div class="like_region">
                <?
                    // echo "cntLike : ".$cntLike."<br>";
                    for($i=0; $i<$cntLike;  $i++){
                        $goodsNo    =       $wishList[$i]["GOODS_NO"];
                        $goodsCode  =       $wishList[$i]["GOODS_CODE"];
                        $goodsName  =       $wishList[$i]["GOODS_NAME"];
                        $imgUrl     =       $wishList[$i]["IMG_URL"];
                        $salePrice  =       $wishList[$i]["SALE_PRICE"];
                        $deliveryCntInBox = $wishList[$i]["DELIVERY_CNT_IN_BOX"];
                        
                        
                        ?>
                        <div class="like_item" id='like_<?=$goodsNo?>' onmouseover="js_view_item_description('<?=$goodsNo?>')" onmouseout="js_hide_item_description('<?=$goodsNo?>')">
                            <div>
                                <div class="like_close" onclick="js_delete_from_wishList('<?=$goodsNo?>','<?=$memberNo?>')">X</div>
                                <a href="sub_detail.php?goods_no=<?=$goodsNo?>">
                                    <img  src="<?=$imgUrl?>" width='100px'>
                                </a>
                            </div>
                            <div class="like_item_description" id="description_<?=$goodsNo?>">
                                <?=$goodsName?><br>
                                <?=$goodsCode?><br>
                                <?=number_format($salePrice)?> 원
                            </div><!--class="like_item_description"-->
                        </div><!--class="like_itme"-->

                    <?
                    }//end of for(cntLike);
                    ?>
                </div><!--like_region-->
            </div><!--like_zone-->
        <?
        }//end of if(wishlist);
        else{

        }
        ?>
    <script>

        $(document).ready(function(){
            var cnt=<?=$cntLike?>;
            var cntCart=Number("<?=$cntCart?>");
            js_like_region_process(cnt);

            if(cnt>0){
                $("#imgLike").attr("src","./img/like_c_on.png");
            }
            else{
                $("#imgLike").attr("src","./img/like_c.png");
            }
            if(cntCart>0){
                // $('.cart_btn').addClass("cart_btn_on");
                $('.detail').css("background-color","#e8378a");
                $('.detail').css("color","#FFFFFF");
                
            }
            else{
                // $('.cart_btn').removeClass("cart_btn_on");
                $('.detail').css("background-color","#FFFFFF");
                $('.detail').css("color","#000000");
            }
            $('.detail').html(cntCart);

            

        });
        
    </script>




    <!--뒷배경이지만 top.php에 배치시켜놓음-->
    <div class="bgc_01 elements" data-speed="7"></div>
    <div class="bgc_02 elements" data-speed="10"></div>
    <div class="bgc_03 elements" data-speed="6"></div>
    <div class="bgc_04 elements" data-speed="7"></div>
    <div class="bgc_05 elements" data-speed="-10"></div>
    <div class="bgc_06 elements" data-speed="5"></div>
    <div class="bgc_07 elements" data-speed="-7"></div>	

