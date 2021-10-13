
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>기프트넷</title>

<script src="js/babel.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery_ui.js"></script>
<script src="../js/jquery.cookie.js"></script>

<script src="js/homepage.js"></script>
<script src="js/all.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
<link rel="stylesheet" href="css/jquery_ui.css" type="text/css">
<link rel="stylesheet" href="css/stylemb.css" type='text/css'>

<link rel="stylesheet" href="css/mobile_temp.css" type="text/css">

<?
// 상단 메뉴 리스트
	function listTopMenus_Mb($db, $arr_options) {

		$query = "SELECT SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME)) AS CATE_NAME, CATE_CODE
					FROM TBL_CATEGORY 
				   WHERE USE_TF = 'Y' 
					 AND DEL_TF = 'N' 
					 AND CATE_CD LIKE '15%' 
					 AND LENGTH(CATE_CD) = 4
				  UNION 
				  SELECT DCODE_NM AS CATE_NAME,  SUBSTRING(DCODE_EXT,-4,4) AS CATE_CODE
				    FROM TBL_CODE_DETAIL
				   WHERE 1 =1
					 AND PCODE = 'HOME_BANNER'
					 AND DCODE = 'NEW_BANNER_TITLE'
				 ";


        //echo $query."<br/><br/>";
		//exit;

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}


    $arrTopMenu	= listTopMenus_Mb($conn, null);

	//print_r($_SESSION);

	$memberNo=$_SESSION["C_MEM_NO"];

	/*
    [C_MEM_NO] => 5876
    [C_MEM_NM] => 임시계정
    [C_MEM_ID] => guest
    [C_CP_NO] => 10300
    [C_CP_NM] => 개인고객 
    [C_MEM_TYPE] => C*/

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
	$(document).ready(function(){
	$(".menu_wrap").children("a").click(function(){
		if ( $(this).next("div").css("display") == "none" )
		{
			$(".menu_wrap a").removeClass("on_a");
			$(".menu_wrap div").css("display","none");

			$(this).next("div").css("display","block");
			$(this).addClass("on_a");
		} else {
			$(".menu_wrap a").removeClass("on_a");
			$(".menu_wrap div").css("display","none");
		}
		
	});

	$(".toggle").click(function(){
		$("#navi").css("display","block");
	});
	$(".nav_x").click(function(){
		$("#navi").css("display","none");
	});
		$(".count_up_down_up").click(function(){
		var no = $(this).parent().children("span").html();
		var no2 = Number(no);
		var no4 = no2 + 1;
		$(this).parent().children("span").html('');
		$(this).parent().children("span").html(no4);
	});
	$(".count_up_down_down").click(function(){
		var no = $(this).parent().children("span").html();
		var no2 = Number(no);
		var no4 = no2 - 1;
		$(this).parent().children("span").html('');
		$(this).parent().children("span").html(no4);
	});

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

<script language="javascript" type="text/javascript">
	
function submenu(chk, catecode) 
{
	//alert(catecode);
	
	$.ajax({
			url: "./json/json_sel_submenu.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
				"mode":"SELECT_SUB_MENU",
				"catecode":catecode
			},
			success: function(response) {
				//리스트 출력
				if(response.length > 0){
					menu_list = "";
					catecodelen = "";
					for(i=0;i<response.length;i++){

                            switch(response[i]["CATE_NAME"])
							{
								case "활선물세트(명절)"	 : response[i]["CATE_NAME"] = "선물세트(명절)";		break;
                                case "활선물세트(감사)"	 : response[i]["CATE_NAME"] = "선물세트(감사)";
                            }
								
							CATE_CODE = response[i]["CATE_CODE"];
							CATE_NAME = response[i]["CATE_NAME"];

							catecodelen = CATE_CODE.length;

							if(catecodelen == "3")
							{
								menu_list += "<a href='Mmenu_list.php?code_cate="+CATE_CODE+"&sort=sort1'> <dl><dt><span>"+CATE_NAME+"&nbsp;</span></dt></dl></a>"
							}
							else
							{
								menu_list += "<a href='Mmenu_list.php?cate="+CATE_CODE+"'> <dl><dt><span>"+CATE_NAME+"&nbsp;</span></dt></dl></a>"
							}					
					}

					submenuList = '#submenuList'+catecode;

					$(submenuList).html(menu_list);
				} else {
					menu_list = "";
					$("#submenuList").html(menu_list);
				}
			},//success
			error : function (jqXHR, textStatus, errorThrown) {
				alert('ERRORS: ' + textStatus);
			}//error
		});//ajax
}

function js_goto_shoppingbag(){
	let sessionNo=Number(sessionName=$('input[name=sessionNo]').val());
	
	if(sessionNo<1){
		alert('로그인 되어 있지 않습니다. 로그인 페이지로 이동 합니다.');
		location.href="Mlog-in.php";
		
	}
	else{
		//alert('로그인O');
		var t=parseInt($(".detail").html());
		if(t<1){
			alert('장바구니에 담긴 물건이 없습니다');
			return;
		}
		location.href="Mshoppingbag.php";
	}


}

function js_logout(){
	// alert('logout');            
	if (!confirm("로그아웃 하시겠습니까?")) return;	
	var frm=document.frm1;
	frm.mode.value="LOGOUT";
	frm.method="POST";
	//frm.action="<?=$_SERVER['PHP_SELF']?>"
	frm.action="Mindex.php";
	frm.target="";
	frm.submit();
}

</script>

<header>
	<div class="toggle"></div>
		<a href="Mindex.php" class="logo"></a>
		<div class="cart" onclick="js_goto_shoppingbag()">
			<!--<div class="detail">0</div>-->
			<span class="detail">0</span>
		</div>
		
</header>

<form name="frm1" method="get">	
	<input type="hidden" name="sessionNo" value="<?=$_SESSION['C_MEM_NO']?>">
	<input type="hidden" name="mode">
	
	<div id="navi" style="display:none;">
		<div class="dark_wall"></div>
		<div class="nav_x">X</div>
		<nav>
			<a href="Mindex.php" class="mlogo"></a><br>
			
			<?
				if($_SESSION['C_MEM_NO'] && $_SESSION['C_MEM_NO']>0){
				?>
				<div class="login_bt1">
					<a href="Mmy_page.php" class="user_name"><img src="img/icon_login.png" alt=""><b> <?=$_SESSION['C_MEM_NM']?></b>님</a>&nbsp;&nbsp;
					<a href="javascript:js_logout()" class="logout_btn"><img src="img/logout.png" alt=""> Logout</a>
				</div>
				<div class="login_bt2">
					<a href="#" class="cart" onclick="js_goto_shoppingbag()"><img src="img/cart_on.png" alt="장바구니"> 장바구니</a>&nbsp;&nbsp;
					<a href="Mwishlist.php" class="Mlike"><img src="img/like_on.png" alt="찜"> 찜</a>&nbsp;&nbsp;
					<a href="Mdelivery_confirm.php" class="ordertrace"><img src="img/order_on.png" alt="주문"> 주문</a>				
				</div>	
				<?
				}
				else{
				?>
					<a href="Mlog-in.php" class="login">로그인</a>
					<a href="Mregister.php" class="join">회원가입</a>
				<?
				}
			?>

			<!-- 전체 카테고리 -->
			<?
				$cntArrTopMenu=sizeof($arrTopMenu);
				if($cntArrTopMenu>0)
				{
					for($i=0; $i<$cntArrTopMenu; $i++)
					{
						$MENU_NAME  =   $arrTopMenu[$i]["CATE_NAME"];
						$CODE_CATE  =   $arrTopMenu[$i]["CATE_CODE"];

					?>
					<div class="menu_wrap" >
					
						<?$arr_rs_cate= listSubMenusByCodeCate($conn, $CODE_CATE, $arr_options);?>

						<a href="#" onclick="submenu(this, <?=$CODE_CATE?>); return false;"><?=$MENU_NAME?></a>

						<div id="sub_menu" style="display:none;">
							<ul id = "submenuList<?=$CODE_CATE?>">
							</ul>
						</div>
					</div>	
					<?
					}
				}
			?>	
			</div>	
		</nav>
	</div>
</form>	