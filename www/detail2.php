<?
	require "_common/home_pre_setting.php";
?>
<?

	if ($mode == "ORDER") {
		/*
			echo "mode:".$mode."<br/>";
			echo "goods_no:".$goods_no."<br/>";
			echo "sale_price:".$sale_price."<br/>";
			echo "qty:".$qty."<br/>";
		*/

		$cart_seq = 0;
		$use_tf		= "Y";

		if (!get_session('s_ord_no')) {
			set_session('s_ord_no', getUniqueId($conn));
		}
		$s_ord_no = get_session('s_ord_no');

		$opt_sticker_no    = trim($opt_sticker_no);
		$opt_sticker_msg   = trim($opt_sticker_msg);
		$opt_outbox_tf     = trim($opt_outbox_tf);

		$opt_wrap_no       = trim($opt_wrap_no);
		$opt_print_msg     = trim($opt_print_msg);
		$opt_memo          = trim($opt_memo);
		//$opt_outstock_date = trim($opt_outstock_date);
		//$delivery_type	   = trim($delivery_type);

		$delivery_type = "0"; //택배 기본으로 
		$opt_outstock_date = date("Y-m-d", strtotime("1 day")); //출고예정일은 +1일

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


		$qty = str_replace(",", "", $qty);
		$extra_price = $susu_price;

		$cp_no = $_SESSION['C_CP_NO'];
		
		//원래 주문등록으로 입력
		$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

		$result = insertCart($conn, $s_ord_no, $cp_order_no, $cp_no, $buy_cp_no, $cart_seq, $goods_no, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);

		if($result) { 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<meta name="robots" content="noindex,nofollow">
<title><?=$g_title?></title>
<script type="text/javascript">
	location.href="order.php";
</script>
<?
		} else { 
?>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('알수없는 이유로 시스템에 입력되지 않았습니다.');
	location.href="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&goods_no=<?=$goods_no?>";
</script>
<?
		}

		exit;
	}

	if ($mode == "CART") {
		/*
			echo "mode:".$mode."<br/>";
			echo "goods_no:".$goods_no."<br/>";
			echo "sale_price:".$sale_price."<br/>";
			echo "qty:".$qty."<br/>";
		*/

		$cart_seq = 0;
		$use_tf		= "Y";

		if (!get_session('s_ord_no')) {
			set_session('s_ord_no', getUniqueId($conn));
		}
		$s_ord_no = get_session('s_ord_no');

		$opt_sticker_no    = trim($opt_sticker_no);
		$opt_sticker_msg   = trim($opt_sticker_msg);
		$opt_outbox_tf     = trim($opt_outbox_tf);

		$opt_wrap_no       = trim($opt_wrap_no);
		$opt_print_msg     = trim($opt_print_msg);
		$opt_memo          = trim($opt_memo);
		//$opt_outstock_date = trim($opt_outstock_date);
		//$delivery_type	   = trim($delivery_type);

		$delivery_type = "0"; //택배 기본으로 
		$opt_outstock_date = date("Y-m-d", strtotime("1 day")); //출고예정일은 +1일

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


		$qty = str_replace(",", "", $qty);
		$extra_price = $susu_price;

		$cp_no = $_SESSION['C_CP_NO'];
		
		//원래 주문등록으로 입력
		$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

		$result = insertCart($conn, $s_ord_no, $cp_order_no, $cp_no, $buy_cp_no, $cart_seq, $goods_no, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);

		if($result) { 
?>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW, NOARCHIVE">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	if(confirm('장바구니에 담았습니다. 이동하시겠습니까?'))
		location.href="cart.php";
	else
		location.href="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&goods_no=<?=$goods_no?>";
</script>
<?
		} else { 
?>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW, NOARCHIVE">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('알수없는 이유로 시스템에 입력되지 않았습니다.');
	location.href="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&goods_no=<?=$goods_no?>";
</script>
<?
		}

		exit;
	}


	$arr_rs = selectGoods($conn, $goods_no);

	$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
	$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
	$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
	$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
	$rs_goods_sub_name	    = SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
	$rs_cate_01				= trim($arr_rs[0]["CATE_01"]); 
	$rs_cate_02				= trim($arr_rs[0]["CATE_02"]); 
	$rs_cate_03				= trim($arr_rs[0]["CATE_03"]); 
	$rs_cate_04				= trim($arr_rs[0]["CATE_04"]);
	$rs_restock_date		= trim($arr_rs[0]["RESTOCK_DATE"]); 
	$rs_price				= trim($arr_rs[0]["PRICE"]); 
	$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
	$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
	$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
	$rs_stock_cnt			= trim($arr_rs[0]["STOCK_CNT"]); 
	$rs_mstock_cnt          = trim($arr_rs[0]["MSTOCK_CNT"]);
	$rs_tax_tf				= trim($arr_rs[0]["TAX_TF"]); 
	$rs_img_url				= trim($arr_rs[0]["IMG_URL"]); 
	$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
	$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
	$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
	$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
	$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
	$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
	$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
	$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
	$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
	$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
	$rs_contents			= trim($arr_rs[0]["CONTENTS"]); 
	$rs_memo				= trim($arr_rs[0]["MEMO"]); 
	$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
	$rs_read_cnt			= trim($arr_rs[0]["READ_CNT"]); 
	$rs_disp_seq			= trim($arr_rs[0]["DISP_SEQ"]); 
	$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
	$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
	$contents			    = trim($arr_rs[0]["CONTENTS"]); 

	/* 2015 9월 08일 추가*/
	$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
	$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
	$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
	$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 

	/* 2016 2월 18일 추가*/
	$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
	$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 

	/* 2016 10월 10일 추가*/
	$rs_next_sale_price		= trim($arr_rs[0]["NEXT_SALE_PRICE"]); 
	$rs_reg_adm				= trim($arr_rs[0]["REG_ADM"]); 
	$rs_reg_date			= trim($arr_rs[0]["REG_DATE"]); 

	if($rs_reg_date != "0000-00-00 00:00:00")
		$rs_reg_date = date("Y-m-d H:i",strtotime($rs_reg_date));
	else
		$rs_reg_date = "";

	if($rs_restock_date != "0000-00-00 00:00:00")
		$rs_restock_date = date("Y-m-d",strtotime($rs_restock_date));
	else
		$rs_restock_date = "";

	//$arr_rs_file = selectGoodsFile($conn, $goods_no);

	//$arr_rs_option = selectGoodsOption($conn, $goods_no);

	//$arr_rs_price = listGoodsPriceUpdate($conn, $goods_no);

	//$arr_rs_goods_sub = selectGoodsSub($conn, $goods_no);

	$arr_rs_goods_proposal = selectGoodsProposal($conn, $goods_no);

	if($cate == '')
		$con_cate = '22';
	else
		$con_cate = $cate;

/*
	if(!chkExistSearchGoodsCateByGoodsNo($conn, $con_cate, $goods_no)) { 
		
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('현재는 진행하고 있지 않은 상품입니다. 홈으로 돌아갑니다.');
	location.href="<?=get_domain()?>";
</script>
<?
		
	}
*/
	

	if($arr_rs_goods_proposal > 0) { 
		
		$rs_component			= SetStringFromDB($arr_rs_goods_proposal[0]["COMPONENT"]); 
		$rs_description_title   = SetStringFromDB($arr_rs_goods_proposal[0]["DESCRIPTION_TITLE"]); 
		$rs_description_body    = SetStringFromDB($arr_rs_goods_proposal[0]["DESCRIPTION_BODY"]);
		$rs_origin				= SetStringFromDB($arr_rs_goods_proposal[0]["ORIGIN"]);

	} else { 

		$rs_component = "";
		$rs_description_title = "";
		$rs_description_body = "";
		$rs_origin = "";
	}
	

	$img_url	= getGoodsImage($rs_file_nm_100, $rs_img_url, $rs_file_path_150, $rs_file_rnm_150, "500", "500");

	// if ($_SESSION['C_CP_NO'] <> "") {
		$rs_sale_price = getCompanyGoodsPriceOrDCRate($conn, $rs_goods_no, $rs_sale_price, $rs_price, $_SESSION['C_CP_NO']);
	// }


?>
<!DOCTYPE html>
<html lang="ko">
<head>

	<meta charset="euc-kr">
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="기프트넷" />
	<title>기프트넷</title>
	<link rel="icon" type="image/x-icon" herf="/" />
	<script type="text/javascript" src="newDesign/js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="newDesign/js/jquery_ui.js"></script>
	<script type="text/javascript" src="newDesign/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="newDesign/js/slick.js"></script>
	<script type="text/javascript" src="newDesign/js/common_ui.js"></script>
	<link type="text/css" rel="stylesheet" href="newDesign/css/reset.css" />

	<script type="text/javascript">
		function goto_purchase_consultation(){
			var frm = document.createElement('form');

			document.body.appendChild(frm);

			var goodsNo=document.createElement('input');
			goodsNo.setAttribute('type','hidden');
			goodsNo.setAttribute('name','goodsNo')
			goodsNo.setAttribute('value','<?=$goods_no?>');

			var goodsCode=document.createElement('input');
			goodsCode.setAttribute('type','hidden');
			goodsCode.setAttribute('name','goodsCode');
			goodsCode.setAttribute('value','<?=$rs_goods_code?>');

			frm.appendChild(goodsNo);
			frm.appendChild(goodsCode);
			alert(goodsNo.value+", "+goodsCode.value);
			
			frm.setAttribute('method','POST');
			frm.setAttribute('action','purchase_consultation.php');

			//return 0;


			frm.submit();
		}
	</script>
<?
	//require "_common/v2_header.php";
?>
</head>
<body id="">
<?
	//require "_common/v2_top.php";
?>
<?
	//if($search_str == "")
		require "_sub_categories.php";
?> 
 
	<div id="wrap">
		<div class="header">
			<div class="innerbox">
				<h2>상품 상세</h2>
				<button type="button" class="btn-prevpage" onclick="history.back(-1)" title="이전">이전</button>
				<button type="button" class="btn-gnb-search" onclick="searchToggle()" title="검색">검색</button>

			</div><!--class=innerbox -->
			<div class="searchbox">
				<h2>상품 검색</h2>
				<button type="button" class="btn-close" onclick="searchToggle()" title="검색 닫기">검색 닫기</button>
				<fieldset>
					<legend>검색어 입력</legend>
					<p><span class="inpbox"><input type="text" class="txt" placeholder="검색어를 입력해주세요." /></span><button type="button">검색</button>
				</fieldset>
				<dl>
					<dt>추천 검색어</dt>
					<dd>
						<?
						$arr_tm=listTopMenus($conn,null);
						$cntArr=sizeof($arr_tm);
						if(sizeof($arr_tm)>0){
							for($i=0;$i<$cntArr;$i++){
								$MENU_NAME=$arr_tm[$i]["CATE_NAME"];
								$CODE_CATE=$arr_tm[$i]["CATE_CODE"];
					?>
						<span><a href="#">#<?=$MENU_NAME?></a></span>
					<?
							}
						}
					?>
					</dd>
				</dl>

					
			</div><!--class=searchbox -->
		</div> <!--class='header'-->
	</div> <!--class='wrap' -->
	<!-- E.header-->
	<!-- S.container-->
	<div class="container">
		<div class="contentsarea">
			<div class="goods-view">
				<div class="goods-detailview">
					<div class="goods-info">
						<div class="pic"><span><img src="<?=$img_url?>" alt="[<?=$rs_goods_code?>] <?=$rs_goods_name?>"/></span></div>
						<div class="info">
							<em><!--GOODS_CODE--><?=$rs_goods_code?></em>
							<p><?=$rs_goods_name?></p>
							<span><strong><?=$rs_sale_price?></strong></span>
						</div><!--class='info'-->

					</div><!--class='goods_info'-->
					<div class="goods-tab">
						<ul>
							<li class="detailview on"><a href="javascript:commonTab('detailview')">상품상세</a></li>
							<li class="as"><a href="javascript:commonTab('as')">배송/AS 문의</a></li>
						</ul>
					</div><!--class='goods-tab' -->
					<div class="tab-hiddencontents detailview on">
						<!-- 상품상세 컨텐츠 영역<br />
						상품상세 컨텐츠 영역<br />
						상품상세 컨텐츠 영역<br />
						상품상세 컨텐츠 영역<br />
						상품상세 컨텐츠 영역<br /> -->

					<?

					$file_path = $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg";
					if(file_exists($file_path)){
						echo "<img src='/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg' style='width:100%;'/>";
					} 
					else { 

						$arr_goods = selectGoodsProposal($conn, $goods_no);
						if(sizeof($arr_goods) > 0) {
							$COMPONENT		   = $arr_goods[0]["COMPONENT"];
							$DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
							$DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
							$ORIGIN			   = $arr_goods[0]["ORIGIN"];

							$DESCRIPTION_BODY = str_replace("//","\n",$DESCRIPTION_BODY);

							if($DESCRIPTION_TITLE != "" || $DESCRIPTION_BODY != "")
								$DESCRIPTION  = $DESCRIPTION_TITLE."\n\n".$DESCRIPTION_BODY;
						}


						if($COMPONENT == "" || $DESCRIPTION == "") {  
							$arr_goods_sub = selectGoodsSub($conn, $goods_no);
							
							$SUB_TOTAL_COMPONENT = "";
							$SUB_TOTAL_DESCRIPTION = "";
							if (sizeof($arr_goods_sub) > 0) {

								for ($jk = 0 ; $jk < sizeof($arr_goods_sub); $jk++) {
									$sub_goods_no			= trim($arr_goods_sub[$jk]["GOODS_SUB_NO"]);
									$goods_cnt				= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
									
									$arr_goods = selectGoodsProposal($conn, $sub_goods_no);
									if(sizeof($arr_goods) > 0) {
										$SUB_COMPONENT		   = $arr_goods[0]["COMPONENT"];
										$SUB_DESCRIPTION_TITLE = $arr_goods[0]["DESCRIPTION_TITLE"];
										$SUB_DESCRIPTION_BODY  = $arr_goods[0]["DESCRIPTION_BODY"];
										$SUB_DESCRIPTION_BODY = str_replace("//","\n",$SUB_DESCRIPTION_BODY);

										if($SUB_COMPONENT <> "")
											$SUB_COMPONENT = $SUB_COMPONENT."(".$goods_cnt."입)";
									} else {
										$SUB_COMPONENT = "";
										$SUB_DESCRIPTION_TITLE = "";
										$SUB_DESCRIPTION_BODY = "";
									}

									if($COMPONENT == "") { 
										$SUB_TOTAL_COMPONENT .= ($SUB_TOTAL_COMPONENT != "" && $SUB_COMPONENT != "" ? ", " : "").$SUB_COMPONENT;
									}

									if($DESCRIPTION == "") { 
										if($SUB_DESCRIPTION_TITLE != "") 
											$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_TITLE."\n\n";
										
										if($SUB_DESCRIPTION_BODY != "")
											$SUB_TOTAL_DESCRIPTION .= $SUB_DESCRIPTION_BODY."\n\n";
									}

									//$sub_goods_cnt			= trim($arr_goods_sub[$jk]["GOODS_CNT"]);
									//$sub_goods_cate			= trim($arr_goods_sub[$jk]["GOODS_CATE"]);
										
									//if(!startsWith($sub_goods_cate, '0102')) 
									//	$COMPONENT .=  $sub_goods_name." (".$sub_goods_cnt."입)<br />";
								}
							} 

							if($COMPONENT == "") 
								$COMPONENT = $SUB_TOTAL_COMPONENT;

							if($DESCRIPTION == "") 
								$DESCRIPTION = $SUB_TOTAL_DESCRIPTION;

						}

						$arr_goods = selectGoods($conn, $goods_no);
						if (sizeof($arr_goods) > 0) {
							$SIZE			= trim($arr_goods[0]["GOODS_SUB_NAME"]);
							$MANUFACTURER	= trim($arr_goods[0]["CATE_02"]);
						}
					?>
					<div class="detail-body"><div class="contents">
					<? if($SIZE <> "") { ?>
						<div class="panel panel-default">
							<div class="panel-heading">상품규격(cm)</div>
							<div class="panel-body">
								<?=$SIZE?>
							</div>
						</div>
						<? } ?>
						<? if($COMPONENT <> "") { ?>
						<div>
							<div>구성(세부내역)</div>
							<div`>
								<?=$COMPONENT?>
							</div>
						</div>
						<? } ?>
						<? if($DESCRIPTION <> "") { ?>
						<div>
							<div>용도 및 특징</div>
							<div>
								<?=nl2br($DESCRIPTION)?>
							</div>
						</div>
						<? } ?>
						<? if($DELIVERY_CNT_IN_BOX <> "") { ?>
						<div>
							<div>박스입수</div>
							<div>
								<?=$DELIVERY_CNT_IN_BOX?>
							</div>
						</div>
						<? } ?>
						<? if($MANUFACTURER <> "") { ?>
						<div class="panel panel-default">
							<div class="panel-heading">제조원</div>
							<div class="panel-body">
								<?=$MANUFACTURER?>
							</div>
						</div>
						<? } ?>
						<? if($ORIGIN <> "") { ?>
						<div>
							<div>원산지</div>
							<div>
								<?=$ORIGIN?>
							</div>
						</div>
						<? } ?>
					</div><!--class='contents' -->
					</div>
	<?
					}
	?>
					</div>
					<div class="tab-hiddencontents as">
						배송/AS 문의 컨텐츠 영역<br />
						배송/AS 문의 컨텐츠 영역<br />
						배송/AS 문의 컨텐츠 영역<br />
						배송/AS 문의 컨텐츠 영역<br />
						배송/AS 문의 컨텐츠 영역<br />

					</div>
				</div><!--class='goods-detailview'-->
				<div class="goods-list">
					<h3>관련상품</h3>
					<div class="listgroup">
						<dl onclick="location.href='./goos/goods-list.html'">
						<dt><span class="pic"><img src="../images/tmp.png" alt=""/></span></dt>
						<dd>
							<p></p>
							<span><strong></strong>원</span>
						</dd>

					</div><!--class='listgroup'-->
				</div><!--class='goods-list'-->
				<div class="goods-list">
					<h3>추천상품</h3>
					<div class="listgroup">
						<dl onclick="location.href='./goos/goods-list.html'">
						<dt><span class="pic"><img src="../images/tmp.png" alt=""/></span></dt>
						<dd>
							<p></p>
							<span><strong></strong>원</span>
						</dd>

					</div><!--class='listgroup'-->
				</div><!--class='goods-list'-->
			</div><!--class='goods-view'-->
			<p class="btn-bottom"><button type="button" class="btn-green btn-large" onclick="goto_purchase_consultation()">구매 상담</button></p>
		</div><!--class='contentsarea'-->
	</div><!--class='container'-->



</body>
</html>

