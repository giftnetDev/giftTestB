<?
	require "_common/home_pre_setting.php";

	function getStickerGroupByGoodsNoAndCpNo($db, $goodsNo, $cpCate){
		$query="SELECT G.GOODS_NAME, G.GOODS_NO, G.GOODS_CODE
				FROM	T_GOODS_OPTION O
				JOIN	TBL_GOODS G ON O.OPTION_GOODS_NO=G.GOODS_NO	
				WHERE	O.GOODS_NO='".$goodsNo."'
				AND		O.OPTION_TYPE='S'

				";
			if($cpCate<>""){
				$query.="AND O.CP_CATE='".$cpCate."' ";
			}
		
		$result=mysql_query($query, $db);

		if(!$result){
			echo "<script>alert('func.getStickerGroupByGoodsNoAndCpNo()_ERRROR');</script>";
			exit;
		}
		$record=array();
		$cnt=mysql_num_rows($result);
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
			return $record;
		}
		$record="";
		return $record;
	}
	function makeSelectBoxForSticker($data, $name){

		$cnt=sizeof($data);

		if($data==""){
			echo "????<br>";
			return;
		}
		$strSelect="<SELECT name='".$name."'><OPTION value=''>????</OPTION>";
		for($i=0; $i<$cnt; $i++){
			$strSelect.="<OPTION value='".$data[$i]['GOODS_NO']."'>".$data[$i]['GOODS_NAME']."</OPTION>";
		}
		$strSelect.="</SELECT>";
		echo $strSelect;
	}

	// function getWrapGroupByGoodsNoAndCpNo($db, $goodsNo, $cpCate){
	// 	$query="SELECT G.GOODS_NAME, G.GOODS_NO
	// 			FROM	T_GOODS_OPTION O
	// 			JOIN	TBL_GOODS G ON O.OPTION_GOODS_NO=G.GOODS_NO	
	// 			WHERE	O.GOODS_NO='".$goodsNo."'
	// 			AND		O.OPTION_TYPE='S'

	// 			";
	// 		if($cpCate<>""){
	// 			$query.="AND O.CP_CATE='".$cpCate."' ";
	// 		}
		
	// 	$result=mysql_query($query, $db);

	// 	if(!$result){
	// 		echo "<script>alert('func.getWrapGroupByGoodsNoAndCpNo()_ERRROR');</script>";
	// 		exit;
	// 	}
	// 	$record=array();
	// 	$cnt=mysql_num_rows($result);
	// 	if($cnt>0){
	// 		for($i=0; $i<$cnt; $i++){
	// 			$record[$i]=mysql_fetch_assoc($result);
	// 		}
	// 		return $record;
	// 	}
	// 	$record="";
	// 	return $record;
	// }

?>
<?
	// print_r($_SESSION);
	// exit;
	$mem_no=$_SESSION['C_MEM_NO'];

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

		$delivery_type = "0"; //?ù? ?⺻???? 
		$opt_outstock_date = date("Y-m-d", strtotime("1 day")); //???????????? +1??

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
		
		//???? ?ֹ????????? ?Է?
		$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

		$result = insertCartWithMemNo($conn, $s_ord_no, $cp_order_no, $cp_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);

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
	alert('?˼????? ?????? ?ý??ۿ? ?Էµ??? ?ʾҽ??ϴ?.');
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

		$opt_sticker_no    = trim($selectSticker);
		$opt_sticker_msg   = trim($opt_sticker_msg);
		$opt_outbox_tf     = trim($opt_outbox_tf);

		$opt_wrap_no       = trim($opt_wrap_no);
		$opt_print_msg     = trim($opt_print_msg);
		$opt_memo          = trim($opt_memo);
		//$opt_outstock_date = trim($opt_outstock_date);
		//$delivery_type	   = trim($delivery_type);

		// echo "opt_sticker_no : ".$selectSticker."<br>";
		// exit;

		$delivery_type = "0"; //?ù? ?⺻???? 
		$opt_outstock_date = date("Y-m-d", strtotime("1 day")); //???????????? +1??

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
		
		//???? ?ֹ????????? ?Է?
		$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

		$result = insertCartWithMemNo($conn, $s_ord_no, $cp_order_no, $cp_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);

		if($result) { 
?>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW, NOARCHIVE">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	if(confirm('???ٱ??Ͽ? ???ҽ??ϴ?. ?̵??Ͻðڽ??ϱ??'))
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
	alert('?˼????? ?????? ?ý??ۿ? ?Էµ??? ?ʾҽ??ϴ?.');
	location.href="<?=$_SERVER[PHP_SELF]?>?cate=<?=$cate?>&goods_no=<?=$goods_no?>";
</script>
<?
		}

		exit;
	}//end of mode ="CART"


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

	/* 2015 9?? 08?? ?߰?*/
	$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
	$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
	$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
	$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 

	/* 2016 2?? 18?? ?߰?*/
	$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
	$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 

	/* 2016 10?? 10?? ?߰?*/
	$rs_next_sale_price		= trim($arr_rs[0]["NEXT_SALE_PRICE"]); 
	$rs_reg_adm				= trim($arr_rs[0]["REG_ADM"]); 
	$rs_reg_date			= trim($arr_rs[0]["REG_DATE"]); 

	$concel_tf			 	= trim($arr_rs[0]["CONCEAL_PRICE_TF"]);		//20210608 ?߰?

	if($rs_reg_date != "0000-00-00 00:00:00")
		$rs_reg_date = date("Y-m-d H:i",strtotime($rs_reg_date));
	else
		$rs_reg_date = "";

	if($rs_restock_date != "0000-00-00 00:00:00")
		$rs_restock_date = date("Y-m-d",strtotime($rs_restock_date));
	else
		$rs_restock_date = "";


	$stickerData=getStickerGroupByGoodsNoAndCpNo($conn, $goods_no, $cpCate);

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
	alert('?????? ?????ϰ? ???? ???? ??ǰ?Դϴ?. Ȩ???? ???ư??ϴ?.');
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
	<?
		require "_common/v2_header.php";
	?>
		<script>
			function addComma(v){
				v=v+"";//v?? str???? ?ƴϰ? int value???? 
				v=v.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				return v;
			}
			function js_change_goods_qty(delimiter){
				// alert('start of function');
				var num=($("input[name=qty]").val())*1;

				if(delimiter=='+'){
					$("input[name=qty]").val(num+1);
				}
				else if(delimiter=='-'){
					// alert(num);
					if(num><?=$rs_delivery_cnt_in_box?>){
						$("input[name=qty]").val(num-1);
					}
					else{
						alert('?ڽ??Լ? ?̸????δ? ?ֹ??Ͻ? ?? ?????ϴ?');

					}
				}

				var totalSalePrice=$('strong').data("sale_price")*$("input[name=qty]").val();
				value=addComma(totalSalePrice);
				$("#total_sale_price").text(value);

			}

			

		</script>
	</head>
<body>
<?
	require "_common/v2_top.php";
?>
<?
	if($search_str == "")
		require "_sub_categories.php";
?> 
 
<!-- ???? ž -->
<div class="container detail-top">
    <div class="row">
        <div class="col-lg-6 col-sm-6 col-xs-12 thumb-l">
            <img src="<?=$img_url?>" alt="[<?=$rs_goods_code?>] <?=$rs_goods_name?>"/>
        </div>
		<form name="frm">
			<input type="hidden" name="mode" value=""/>
			<input type="hidden" name="goods_no" value="<?=$goods_no?>"/>
			<input type="hidden" name="cate" value="<?=$cate?>"/>
			<input type="hidden" name="sale_price" value="<?=$rs_sale_price?>"/>

			<div class="col-lg-6 col-sm-6 col-xs-12  detail-right">
				<p class="code"><?=$rs_goods_code?></p>
				<h4 class="title"><?=$rs_goods_name?></h4>
				<!--<p class="description">?ؽ?Ʈ?? ???? ???? ??ǰ ???? <br>??Ȱ??ǰ 17???? ???? ??Ʈ ??ǰ?Դϴ?.</p>-->

				<?//if ($_SESSION['C_CP_NO'] <> "") {?>
				<div class="input-group" id="detail-price">
					<span class="input-group-addon">?ǸŴܰ?</span>
					<?
					if($mem_no <> "")
					{	
					?>
						<span class="form-control"><strong id="sale_price" data-sale_price="<?=$rs_sale_price?>"><?=number_format($rs_sale_price)?></strong>??</span>
					<?
					}
					else
					{	
						if($concel_tf != "Y")
						{
					?>
							<span class="form-control"><strong id="sale_price" data-sale_price="<?=$rs_sale_price?>"><?=number_format($rs_sale_price)?></strong>??</span>
					<?	}
						else
						{
					?>		<span class="form-control"><strong>???ݹ???</strong>
					<?
						}					
					}
					?>		

				</div>
				<div class="input-group" id="detail-piece">
					<span class="input-group-addon">?ֹ? ????</span>
					<span class="form-control"><input type="button" value="<" onclick="js_change_goods_qty('-')"><input type="text" name="qty" style="width:50px;" value="<?=$rs_delivery_cnt_in_box?>" autocomplete="off"/><input type="button" value=">" onclick="js_change_goods_qty('+')">??</span>
				</div>
				<div class="input-group" id="detail-piece">
					<span class="input-group-addon">?ڽ? ?Լ?</span>
					<span class="form-control"><strong><?=number_format($rs_delivery_cnt_in_box)?></strong>??</span>
				</div>
				<div class="input-group" id="detail-piece">
					<span class="input-group-addon">??ƼĿ ?ɼ?</span>
					<span class="form-control"><?=makeSelectBoxForSticker($stickerData,'selectSticker');?></span>
				</div>
				<div class="input-group" id="detail-piece">
					<span class="input-group-addon">?μ?(??????????)<br>
													?߰?????</span>
					<span class="form-control"><strong><input type="text" name="opt_print_msg" value="<?=$opt_print_msg?>"></strong></span>
				</div>
				<div class="input-group" id="detail-total">
				  <span class="input-group-addon">?հ? ?ݾ?</span>
					<?
					if($mem_no <> "")
					{	
					?>
						<span class="form-control"><strong id="total_sale_price"><?=number_format($rs_sale_price)?></strong>??</span>
					<?
					}
					else
					{	
						if($concel_tf != "Y")
						{
					?>
							<span class="form-control"><strong id="total_sale_price"><?=number_format($rs_sale_price)?></strong>??</span>
					<?	}
						else
						{
					?>		<span class="form-control"><strong>???ݹ???</strong>
					<?
						}					
					}
					?>
				</div>
				<div class="btns" role="group">
				<?
					if($mem_no <> "")
					{	
					?>
						<button type="button" class="btn btn-default" id="btn-order"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ?ֹ??ϱ?</button>
						<button type="button" class="btn btn-default" id="btn-cart"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ???ٱ???</button>
					<!--<button type="button" class="btn btn-default" id="btn-ask">?????? ????</button>-->
					<?
					}
					else
					{	
						if($concel_tf != "Y")
						{
					?>
						<button type="button" class="btn btn-default" id="btn-order"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ?ֹ??ϱ?</button>
						<button type="button" class="btn btn-default" id="btn-cart"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ???ٱ???</button>
					<?	}
						else
						{
					?>		
						<button type="button" class="btn btn-default" id="btn-order" disabled><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ?ֹ??ϱ?</button>
						<button type="button" class="btn btn-default" id="btn-cart" disabled><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ???ٱ???</button>
					<?
						}					
					}
					?>
				</div>
				<? //} else { ?>
				<!-- <div class="input-group" id="detail-price">
					<span class="input-group-addon">?ǸŴܰ?</span>
					<span class="form-control">???ǿ???</span>
				</div> -->
				<? //} ?>
				<!--
				<div class="input-group" id="detail-package">
				  <span class="input-group-addon">?????ɼ?</span>
				  <div class="btn-group">
					  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">???? ???̽? ????<span class="caret"></span></button>
					  <ul class="dropdown-menu">
						  <li role="presentation"><a href="#">???? ???̽? ????</a></li>
					  </ul>
				  </div>
				</div>
				
				
				-->
				
				
			</div>
		</form>
    </div>
</div>
<!-- // ???? ž-->
<!-- ???? ?ٵ? -->
<div class="container detail-body">
    <h5><span>??ǰ ???? ????</span></h5>
    
    <div class="contents">
		<? 
			$file_path = $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg";
			if(file_exists($file_path)){
				echo "<img src='/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg' style='width:100%;'/>";
		   } else { 
		?>
			<h3>
				??ǰ ???? ????
			</h3>
		
		<?
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
								$SUB_COMPONENT = $SUB_COMPONENT."(".$goods_cnt."??)";
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
						//	$COMPONENT .=  $sub_goods_name." (".$sub_goods_cnt."??)<br />";
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

			<? if($SIZE <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">??ǰ?԰?(cm)</div>
				<div class="panel-body">
					<?=$SIZE?>
				</div>
			</div>
			<? } ?>
			<? if($COMPONENT <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">????(???γ???)</div>
				<div class="panel-body">
					<?=$COMPONENT?>
				</div>
			</div>
			<? } ?>
			<? if($DESCRIPTION <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">?뵵 ?? Ư¡</div>
				<div class="panel-body">
					<?=nl2br($DESCRIPTION)?>
				</div>
			</div>
			<? } ?>
			<? if($DELIVERY_CNT_IN_BOX <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">?ڽ??Լ?</div>
				<div class="panel-body">
					<?=$DELIVERY_CNT_IN_BOX?>
				</div>
			</div>
			<? } ?>
			<? if($MANUFACTURER <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">??????</div>
				<div class="panel-body">
					<?=$MANUFACTURER?>
				</div>
			</div>
			<? } ?>
			<? if($ORIGIN <> "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading">??????</div>
				<div class="panel-body">
					<?=$ORIGIN?>
				</div>
			</div>
			<? } ?>
		
		<? } ?>
    </div>

</div>
<!-- // ???? ?ٵ?-->
<!-- ???? Ǫ?? -->
<? 
	$arr_hp_d = listDcode($conn, "HOME_DETAIL_CONTENT", 'Y', 'N', '', '', 1, 100);
	for($q = 0; $q < sizeof($arr_hp_d); $q++) { 
		$detail_sub_id = $arr_hp_d[$q]["DCODE"];

		if($detail_sub_id == "") continue;
		$arr_rs = selectBoard($conn, "HOMEPAGE", $detail_sub_id);
	

		$detail_sub_title					= SetStringFromDB($arr_rs[0]["TITLE"]); 
		$detail_sub_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]); 

		$detail_sub_contents  = html_entity_decode($detail_sub_contents);


		?>
	<div class="container detail-footer">
		<h5><span><?=$detail_sub_title?></span></h5>
		
		<div class="contents">
			<?=$detail_sub_contents?>
		</div>

	</div>
		<?

	}

?>

<!-- // ???? Ǫ??-->

<!-- ???? ??ǰ -->

<?
	$arr_rs_related = listGoods($conn, $con_cate, '', '', '', '', $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, '', 'Y', 'N', '', '', null, 'RANDOM', '', 1, 12);

?>
<div class="container-fluid" id="related">
    <div class="container">
        <h4>???? ??ǰ</h4>
        <div class="row">
			<?
				if (sizeof($arr_rs_related) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs_related); $j++) {
						$GOODS_NO				= trim($arr_rs_related[$j]["GOODS_NO"]);
						$GOODS_CODE				= trim($arr_rs_related[$j]["GOODS_CODE"]);
						$GOODS_NAME				= SetStringFromDB($arr_rs_related[$j]["GOODS_NAME"]);
						$IMG_URL				= trim($arr_rs_related[$j]["IMG_URL"]);
						$FILE_NM				= trim($arr_rs_related[$j]["FILE_NM_100"]);
						$FILE_RNM_150			= trim($arr_rs_related[$j]["FILE_RNM_150"]);
						$FILE_PATH_150			= trim($arr_rs_related[$j]["FILE_PATH_150"]);
						$SALE_PRICE				= trim($arr_rs_related[$j]["SALE_PRICE"]);

						$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "150", "150");

			?>
            <div class="col-lg-3 col-sm-4 col-xs-6 item">
				<a href="/detail1.php?goods_no=<?=$GOODS_NO?>" class="thumbnail">
					<img src="<?=$img_url?>" alt="<?=$GOODS_NAME?>">
					<p class="code"><?=$GOODS_CODE?></p>
					<p class="title"><?=$GOODS_NAME?></p>
					<!--<p class="price"><strong><?=number_format($SALE_PRICE)?></strong>??</p>-->
				</a>
            </div>
            <?		}
				}
			?>
        </div>
    </div>
</div>
<!-- // ???? ??ǰ -->

<?
	require "_common/v2_footer.php";
?>
<script type="text/javascript">
	$(function(){
		$("input[name=qty]").keyup(function(){
			var sale_price = $("#sale_price").data("sale_price");
			var qty = $(this).val();

			var total_sale_price = sale_price * qty;
			$("#total_sale_price").html(number_format(total_sale_price));
		});

		$("#btn-order").click(function(){

			var frm = document.frm;
	
			frm.mode.value="ORDER";
			frm.method="post";
			frm.action="<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		});

		$("#btn-cart").click(function(){

			if($("input[name='opt_print_mgs']").val()!=""){
				var flag=confirm("?μ? ?߰??????? ?߻??մϴ?. ?ڼ??? ??Ȳ?? ?????Ͻñ? ?ٶ??ϴ?");
				if(!flag){
					return; 
				}
			}

			var frm = document.frm;

			frm.mode.value="CART";
			frm.method="post";
			frm.action="<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		});
	});
</script>
<script type="text/javascript">
	var totalSalePrice=$('strong').data("sale_price")*$("input[name=qty]").val();
	value=addComma(totalSalePrice);
	$("#total_sale_price").text(value);
</script>

</body>
</html>

