<?
	require "_common/home_pre_setting.php";

	require "_classes/biz/member/member.php";
	require "_classes/biz/order/order.php";


	if ($_SESSION['C_MEM_NO'] == "") {


	// print_r($_SESSION);

?>
<script type="text/javascript">
	alert('로그인 되어있지 않거나 세션이 만료 되었습니다. 재 로그인 해주세요.');
</script>
<meta http-equiv='Refresh' content='0; URL=/log-in.php'>
<?
			exit;
	}

	print_r($_SESSION);

	$mem_no=$_SESSION['C_MEM_NO'];
	// exit;
	// echo "mode : ".$mode."<br>";
	// exit;

	if ($mode == "I") {
		// 신규 주문 번호 생성
		$new_reserve_no = getReservNo($conn,"EN");

		$s_ord_no = get_session('s_ord_no');
		//echo "주문번호".$s_ord_no;

		$cp_type = $_SESSION['C_CP_NO'];
		$new_mem_no = $_SESSION['C_MEM_NO'];
		$o_email = $o_email1."@".$o_email2;
		$r_email = $r_email1."@".$r_email2;
		$order_date = date("Y-m-d H:i:s", strtotime("0 day"));
		
		//echo $s_ord_no;

		$arr_rs_cart = listCartByMemNo($conn, $s_ord_no, $cp_type, $mem_no, 'Y', 'N', "DESC");

		$nCnt = 0;
		$TOTAL_PRICE = 0;
		$TOTAL_BUY_PRICE = 0;
		$TOTAL_SALE_PRICE = 0;
		$TOTAL_EXTRA_PRICE = 0;
		$TOTAL_DELIVERY_PRICE = 0;
		$TOTAL_QTY = 0;
		
		if (sizeof($arr_rs_cart) > 0) {

			for ($j = 0 ; $j < sizeof($arr_rs_cart); $j++) {
				
				$CART_NO					= SetStringToDB($arr_rs_cart[$j]["CART_NO"]);
				$ON_UID						= SetStringToDB($arr_rs_cart[$j]["ON_UID"]);
				$CP_ORDER_NO				= SetStringToDB($arr_rs_cart[$j]["CP_ORDER_NO"]);
				$BUY_CP_NO					= SetStringToDB($arr_rs_cart[$j]["BUY_CP_NO"]);
				$GOODS_NO					= SetStringToDB($arr_rs_cart[$j]["GOODS_NO"]);
				$GOODS_CODE					= SetStringToDB($arr_rs_cart[$j]["GOODS_CODE"]);
				$GOODS_NAME					= SetStringToDB($arr_rs_cart[$j]["GOODS_NAME"]);
				$QTY						= SetStringToDB($arr_rs_cart[$j]["QTY"]);
				$BUY_PRICE					= SetStringToDB($arr_rs_cart[$j]["BUY_PRICE"]);
				$PRICE						= SetStringToDB($arr_rs_cart[$j]["PRICE"]);
				$SALE_PRICE					= SetStringToDB($arr_rs_cart[$j]["SALE_PRICE"]);
				$EXTRA_PRICE				= SetStringToDB($arr_rs_cart[$j]["EXTRA_PRICE"]);
				$DELIVERY_PRICE				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_PRICE"]);
				$SA_DELIVERY_PRICE			= SetStringToDB($arr_rs_cart[$j]["SA_DELIVERY_PRICE"]);
				$DISCOUNT_PRICE				= SetStringToDB($arr_rs_cart[$j]["DISCOUNT_PRICE"]);

				//카트에 대한 여분필드
				$C_CATE_01					= SetStringToDB($arr_rs_cart[$j]["C_CATE_01"]); //샘플, 증정, 일반("")
				$C_CATE_02					= SetStringToDB($arr_rs_cart[$j]["C_CATE_02"]); 
				$C_CATE_03					= SetStringToDB($arr_rs_cart[$j]["C_CATE_03"]);
				$C_CATE_04					= SetStringToDB($arr_rs_cart[$j]["C_CATE_04"]);

				//상품에 대한 여분필드 - 저장안함
				$cate_01					= SetStringToDB($arr_rs_cart[$j]["CATE_01"]); 
				$cate_02					= SetStringToDB($arr_rs_cart[$j]["CATE_02"]);
				$cate_03					= SetStringToDB($arr_rs_cart[$j]["CATE_03"]);
				$cate_04					= SetStringToDB($arr_rs_cart[$j]["CATE_04"]); 

				$OPT_STICKER_NO				= SetStringToDB($arr_rs_cart[$j]["OPT_STICKER_NO"]);
				$OPT_OUTBOX_TF				= SetStringToDB($arr_rs_cart[$j]["OPT_OUTBOX_TF"]);
				$DELIVERY_CNT_IN_BOX		= SetStringToDB($arr_rs_cart[$j]["DELIVERY_CNT_IN_BOX"]);
				$OPT_WRAP_NO				= SetStringToDB($arr_rs_cart[$j]["OPT_WRAP_NO"]);
				$OPT_PRINT_MSG				= SetStringToDB($arr_rs_cart[$j]["OPT_PRINT_MSG"]);
				$OPT_OUTSTOCK_DATE			= SetStringToDB($arr_rs_cart[$j]["OPT_OUTSTOCK_DATE"]);
				$OPT_MEMO					= SetStringToDB($arr_rs_cart[$j]["OPT_MEMO"]);
				$DELIVERY_TYPE				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_TYPE"]);
				$DELIVERY_CP				= SetStringToDB($arr_rs_cart[$j]["DELIVERY_CP"]);
				$SENDER_NM					= SetStringToDB($arr_rs_cart[$j]["SENDER_NM"]);
				$SENDER_PHONE				= SetStringToDB($arr_rs_cart[$j]["SENDER_PHONE"]);

				$STICKER_PRICE				= SetStringToDB($arr_rs_cart[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE				= SetStringToDB($arr_rs_cart[$j]["PRINT_PRICE"]); 
				$SALE_SUSU					= SetStringToDB($arr_rs_cart[$j]["SALE_SUSU"]);
				$OPT_STICKER_MSG			= SetStringToDB($arr_rs_cart[$j]["OPT_STICKER_MSG"]); 

				// 사입건에 대한 부분 확인 하자
				$SUM_PRICE = $QTY * $PRICE;
				$SUM_BUY_PRICE = $QTY * $BUY_PRICE;
				$SUM_SALE_PRICE = $QTY * $SALE_PRICE;

				$SUM_EXTRA_PRICE = $QTY * $EXTRA_PRICE;

				$TOTAL_QTY = $TOTAL_QTY + $QTY;
				$TOTAL_PRICE = $TOTAL_PRICE + $SUM_PRICE;
				$TOTAL_BUY_PRICE = $TOTAL_BUY_PRICE + $SUM_BUY_PRICE;
				$TOTAL_SALE_PRICE = $TOTAL_SALE_PRICE + $SUM_SALE_PRICE;
				$TOTAL_EXTRA_PRICE = $TOTAL_EXTRA_PRICE + $SUM_EXTRA_PRICE;
				//$TOTAL_DELIVERY_PRICE = $TOTAL_DELIVERY_PRICE + $DELIVERY_PRICE;
				$TOTAL_SA_DELIVERY_PRICE = $TOTAL_SA_DELIVERY_PRICE + $SA_DELIVERY_PRICE;
				$TOTAL_DISCOUNT_PRICE = $TOTAL_DISCOUNT_PRICE + $DISCOUNT_PRICE;

				$order_state		= "1"; //입금완료처리 - giftnet
				$use_tf					= "Y";

				$arr_rs_goods = selectGoods($conn, $GOODS_NO);
				$TAX_TF				= trim($arr_rs_goods[0]["TAX_TF"]); 

				$DELIVERY_TYPE = "0"; //택배로 설정

				$memos = array('opt_request_memo' => $opt_request_memo, 'opt_support_memo' => $opt_support_memo);

				$result = insertOrderGoods($conn, $ON_UID, $new_reserve_no, $CP_ORDER_NO, $BUY_CP_NO, $new_mem_no, $j, $GOODS_NO, $GOODS_CODE, $GOODS_NAME, $goods_sub_name, $QTY, $OPT_STICKER_NO, $OPT_STICKER_MSG, $OPT_OUTBOX_TF, $DELIVERY_CNT_IN_BOX, $OPT_WRAP_NO, $OPT_PRINT_MSG, $OPT_OUTSTOCK_DATE, $OPT_MEMO, $memos, $DELIVERY_TYPE, $DELIVERY_CP, $SENDER_NM, $SENDER_PHONE, $C_CATE_01, $C_CATE_02, $C_CATE_03, $C_CATE_04, $PRICE, $BUY_PRICE, $SALE_PRICE, $EXTRA_PRICE, $DELIVERY_PRICE, $SA_DELIVERY_PRICE, $DISCOUNT_PRICE, $STICKER_PRICE, $PRINT_PRICE, $SALE_SUSU, $labor_price, $other_price, $TAX_TF, $order_state, $use_tf, $s_adm_no);

			}//end of for(size_of_arrCart);
		}//end of if(size_of_arrCart);

		$o_phone 	=  $telphone1."-".$telphone2."-".$telphone3;
		$o_hphone 	=  $phone1."-".$phone2."-".$phone3;

		$r_phone 	=  $r_telphone1."-".$r_telphone2."-".$r_telphone3;
		$r_hphone 	=  $r_phone1."-".$r_phone2."-".$r_phone3;
		

		$result = insertOrder($conn, $ON_UID, $new_reserve_no, $new_mem_no, $cp_type, $o_mem_name, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $r_mem_name, $r_zipcode, $r_addr1, $r_addr2, $r_phone, $r_hphone, $r_email, $memo, $bulk_tf, $opt_manager_no, $order_state, $TOTAL_PRICE, $TOTAL_BUY_PRICE, $TOTAL_SALE_PRICE, $TOTAL_EXTRA_PRICE, $TOTAL_DELIVERY_PRICE, $TOTAL_SA_DELIVERY_PRICE, $TOTAL_DISCOUNT_PRICE, $TOTAL_QTY, $pay_type, $delivery_type, $use_tf, $s_adm_no);

		$query_date = "UPDATE TBL_ORDER SET ORDER_DATE = '$order_date' WHERE RESERVE_NO = '$new_reserve_no' ";
		mysql_query($query_date,$conn);

		$query_flag="UPDATE TBL_CART
						SET USE_TF='N'
						,	REG_ORDER_DATE=NOW()
					WHERE 	MEM_NO='".$mem_no."'
					AND		DEL_TF='N'
					";
		if(!mysql_query($query_flag, $conn)){
			echo "<script>alert('query_flag Error!!');</script>";
		}

		set_session('s_ord_no', "");

		

		if($result) { 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('주문 완료 되었습니다. 주문/배송관리에서 배송상황을 확인 하실 수 있습니다.');
	location.href="/";
</script>
<?
		} else { 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script type="text/javascript">
	alert('알수없는 이유로 시스템에 입력되지 않았습니다.');
	location.href="<?=$_SERVER[PHP_SELF]?>";
</script>
<?
		}

		exit;		
	}//end of mode "I"


#====================================================================
# Request Parameter
#====================================================================

	if (!get_session('s_ord_no')) {
		set_session('s_ord_no', getUniqueId($conn));
	}

	$s_ord_no = get_session('s_ord_no');
	$mem_no = $_SESSION['C_MEM_NO'];

	$arr_rs = listCartByMemNo($conn, '', $cp_type, $mem_no, 'Y', 'N', "ASC");



	$arr_rs = selectMember($conn, $mem_no);
	
	$rs_mem_nm					= SetStringFromDB($arr_rs[0]["MEM_NM"]); 
	$rs_email					= trim($arr_rs[0]["EMAIL"]); 
	$rs_phone					= trim($arr_rs[0]["PHONE"]); 
	$rs_hphone					= trim($arr_rs[0]["HPHONE"]); 
	$rs_zipcode					= SetStringFromDB(trim($arr_rs[0]["ZIPCODE"])); 
	$rs_addr1					= SetStringFromDB(trim($arr_rs[0]["ADDR1"]));
	$rs_etc						= SetStringFromDB(trim($arr_rs[0]["ETC"])); 

	$rs_phone1					= trim($arr_rs[0]["PHONE1"]); 
	$rs_phone2					= trim($arr_rs[0]["PHONE2"]); 
	$rs_phone3					= trim($arr_rs[0]["PHONE3"]); 
	$rs_hphone1					= trim($arr_rs[0]["HPHONE1"]);
	$rs_hphone2					= trim($arr_rs[0]["HPHONE2"]);
	$rs_hphone3					= trim($arr_rs[0]["HPHONE3"]);

	$cp_type					= trim($arr_rs[0]["CP_NO"]); 

	$cp_no = $_SESSION['C_CP_NO'];

	$arr_rs = listCartByMemNo($conn, '', $cp_type, $mem_no, 'Y', 'N', "ASC");
	
?>

<script type="text/javascript">
//maxlength 체크
function js_view_order_goods_detail(idx){
	$('#dvOrderGoodsDetail_'+idx).show();
}
function js_hide_order_goods_detail(idx){
	$('#dvOrderGoodsDetail_'+idx).hide();
}
function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
    {
        object.value = object.value.slice(0, object.maxLength);
    }   
}
</script>

<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
	
?>
</head>
<body>

<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<?
	require "_common/v2_top.php";
?>
<!-- 주문페이지 -->
<div class="container members signin">
    <h5 class="title">주문하기</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mode" value="">
			<nav class="nav nav-pills navbar-nav l_nav">
				<a class="navbar-brand" href="#order_info">주문자 정보</a>
			</nav>
            <div class="form-group group_line">
                <label class="control-label col-sm-3" for="o_mem_name">이름</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="name" class="form-control" id="o_mem_name" name="o_mem_name" placeholder="" value="<?=$rs_mem_nm?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">이메일 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<?
						$email_split = explode ("@", $rs_email);
						if(sizeof($email_split) > 0) { 
							$front_email = $email_split[0];
							$end_email = $email_split[1];
						}
					?>
                    <input type="text" class="form-control" id="email" name="o_email1" placeholder="" value="<?=$front_email?>">  @ 
					<input type="text" class="form-control" name="o_email2" placeholder="" value="<?=$end_email?>">
                    <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">자동 입력<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="o_email2">
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                    </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">전화번호</label>
                <!--<div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="phone" name="o_phone" placeholder="" value="<?=$rs_phone?>">
                </div>-->
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="number" class="form-control" name="telphone1" id="telphone1" value="<?=$rs_phone1?>" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone2" id="telphone2" value="<?=$rs_phone2?>" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone3" id="telphone3" value="<?=$rs_phone3?>" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">휴대전화</label>
                <!--<div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="hphone" name="o_hphone" placeholder="" value="<?=$rs_hphone?>">
                </div>-->
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
                <input type="hidden" id="hpcerCk" name="hpcerCk">
				<input type="number" class="form-control" name="phone1" id="phone1" value="<?=$rs_hphone1?>" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone2" id="phone2" value="<?=$rs_hphone2?>" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone3" id="phone3" value="<?=$rs_hphone3?>" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">                
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">우편번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="zipcode" name="o_zipcode" placeholder="" value="<?=$rs_zipcode?>">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcode">주소검색</button>
                </div>
				<label class="control-label col-sm-3" for="addr1">주문자 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%;" id="addr1" name="o_addr1" placeholder="" value="<?=$rs_addr1?>">
                </div>
            </div>
            <nav class="nav nav-pills navbar-nav l_nav group_span">
				<a class="navbar-brand" href="#receiver_info">수령자 정보</a>
			</nav>
			<div class="nav-sub">
				<label><input type="checkbox" id="chk_same" value="Y"/>주문자 정보와 동일한 경우 체크 하세요.</label>
            </div>
			<div class="form-group group_line">
                <label class="control-label col-sm-3" for="r_mem_name">이름</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="name" class="form-control" name="r_mem_name" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">이메일 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" name="r_email1" placeholder="">  @ 
					<input type="text" class="form-control" name="r_email2" placeholder="">
                    <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">자동 입력<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="r_email2">
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                    </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">전화번호</label>
                <!--<div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" name="r_phone" placeholder="">
                </div>-->
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="number" class="form-control" name="r_telphone1" id="r_telphone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="r_telphone2" id="r_telphone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="r_telphone3" id="r_telphone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">                
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">휴대전화</label>
                <!--<div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" name="r_hphone" placeholder="">
                </div>-->
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="number" class="form-control" name="r_phone1" id="r_phone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="r_phone2" id="r_phone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="r_phone3" id="r_phone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">                
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">우편번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="zipcode" name="r_zipcode" placeholder="">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcode">주소검색</button>
                </div>
				<label class="control-label col-sm-3" for="addr1">주문자 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%;" name="r_addr1" placeholder="">
                </div>
            </div>
			<div class="form-group">
				<label class="control-label col-sm-3" for="addr1">배송 메모</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%;" name="memo" placeholder="">
                </div>
            </div>
			<nav class="nav nav-pills navbar-nav l_nav group_span">
				<a class="navbar-brand" href="#goods">주문 상품 정보</a>
			</nav>
			<div class="form-group group_line">
				<table class="table table-hover table-striped table-responsive">
				<colgroup>
					<col class="col-md-1">
					<col class="col-md-5">
					<col class="col-md-2">
					<col class="col-md-1">
					<col class="col-md-2">
					<col class="col-md-1">
				</colgroup>
				  <thead>
					<tr>
					  <th></th>
					  <th>상품정보</th>
					  <th>판매가</th>
					  <th>수량</th>
					  <th>구매예정가</th>
					  <th>옵션</th>
					</tr>
				  </thead>
				  <tbody>
			<?
				$nCnt = 0;
				$TOTAL_SUM_PRICE = 0;
				$TOTAL_QTY = 0;
				
				if (sizeof($arr_rs) > 0) {
					for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
						
						$rn									= trim($arr_rs[$j]["rn"]);
						$CART_NO						= trim($arr_rs[$j]["CART_NO"]);
						$ON_UID							= trim($arr_rs[$j]["ON_UID"]);
						$GOODS_CODE						= trim($arr_rs[$j]["GOODS_CODE"]);
						$GOODS_NAME					= trim($arr_rs[$j]["GOODS_NAME"]);
						$QTY								= trim($arr_rs[$j]["QTY"]);
						$BUY_PRICE					= trim($arr_rs[$j]["BUY_PRICE"]);
						$PRICE						= trim($arr_rs[$j]["PRICE"]);
						$SALE_PRICE					= trim($arr_rs[$j]["SALE_PRICE"]);
						$EXTRA_PRICE				= trim($arr_rs[$j]["EXTRA_PRICE"]);
						$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]);
						$DISCOUNT_PRICE			= trim($arr_rs[$j]["DISCOUNT_PRICE"]);
						$SA_DELIVERY_PRICE	= trim($arr_rs[$j]["SA_DELIVERY_PRICE"]);

						$IMG_URL						= trim($arr_rs[$j]["IMG_URL"]);
						$FILE_NM						= trim($arr_rs[$j]["FILE_NM_100"]);
						$FILE_RNM						= trim($arr_rs[$j]["FILE_RNM_100"]);
						$FILE_PATH					= trim($arr_rs[$j]["FILE_PATH_100"]);
						$FILE_SIZE					= trim($arr_rs[$j]["FILE_SIZE_100"]);
						$FILE_EXT						= trim($arr_rs[$j]["FILE_EXT_100"]);
						$FILE_NM_150				= trim($arr_rs[$j]["FILE_NM_150"]);
						$FILE_RNM_150				= trim($arr_rs[$j]["FILE_RNM_150"]);
						$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
						$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
						$FILE_EXT_150				= trim($arr_rs[$j]["FILE_EXT_150"]);

						$CATE_01						= trim($arr_rs[$j]["C_CATE_01"]);

						$OPT_STICKER_NO				= trim($arr_rs[$j]["OPT_STICKER_NO"]);
						$OPT_OUTBOX_TF				= trim($arr_rs[$j]["OPT_OUTBOX_TF"]);
						$OPT_WRAP_NO				= trim($arr_rs[$j]["OPT_WRAP_NO"]);
						$OPT_STICKER_MSG			= trim($arr_rs[$j]["OPT_STICKER_MSG"]);
						$OPT_PRINT_MSG				= trim($arr_rs[$j]["OPT_PRINT_MSG"]);
						$OPT_OUTSTOCK_DATE			= trim($arr_rs[$j]["OPT_OUTSTOCK_DATE"]);
						if($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00")
							$OPT_OUTSTOCK_DATE			= date("Y-m-d", strtotime($OPT_OUTSTOCK_DATE));
						$OPT_MEMO					= trim($arr_rs[$j]["OPT_MEMO"]);

						$OPT_OUTBOX_TF = ($OPT_OUTBOX_TF == "Y" ? "있음" : "" );

						$OPT_OUTSTOCK_DATE = ($OPT_OUTSTOCK_DATE != "0000-00-00 00:00:00" ? $OPT_OUTSTOCK_DATE : "출고미정");


						if($CATE_01 <> "")
							$str_cate_01 = $CATE_01.") ";
						else 
							$str_cate_01 = "";

						$SUM_PRICE = ($QTY * $SALE_PRICE) + $SA_DELIVERY_PRICE - $DISCOUNT_PRICE;

						$TOTAL_QTY = $TOTAL_QTY + $QTY;

						//if($CATE_01 == "") //2016-12-21 샘플, 증정 주문서 금액에 다시 추가
						$TOTAL_SUM_PRICE = $TOTAL_SUM_PRICE + $SUM_PRICE;

						$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");
						
						$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
			?>
					<tr>
						<th scope="row">
							<input type="hidden" name="m_cart_no[]" value="<?=$CART_NO?>">
							<img src="<?=$img_url?>" width="50" height="50">
						</th>
						<td> [<?=$GOODS_CODE?>] <?=$GOODS_NAME?></td>
						<td id="idSalePrice_<?=$j?>"><?=number_format($SALE_PRICE)?>원</td>
						<td id="idQty_<?=$j?>"><?=number_format($QTY)?></td>
						<td id="idSumPrice_<?=$j?>"><?=number_format($SUM_PRICE)?>원</td>
						<td id="idOptView_<?=$j?>" onmouseover="js_view_order_goods_detail('<?=$j?>')" onmouseout="js_hide_order_goods_detail('<?=$j?>');" style="z-index: 2; position:relative;">
							<div id="dvOrderGoodsDetail_<?=$j?>" style="z-index:10;display:none; background-color: #FFFF00; position:absolute; top:20px; left:3px; width:200px;">
								*옵션내용*</br>
								<?
									if($OPT_STICKER_NAME<>""){
									?>
										<b>스티커 :</b> <?=$OPT_STICKER_NAME?></br>
									<?
									}
									if($OPT_PRINT_MSG<>""){
									?>
										<b>인쇄내용 :</b> <?=$OPT_PRINT_MSG?></br>
									<?
									}
								?>
							</div>
							옵션

						</td>
						<!-- <td><a class="btn-delete cursor-pointer" data-cart_no="<?=$CART_NO?>"><span class="glyphicon glyphicon-remove"></span></a></td> -->
					</tr>
			<?		} 
				} else { 
			?>

			<?  } ?>
				  </tbody>
				</table>
			</div>
			<div><!--총액 나오는 곳-->
				총액 : <?=number_format($TOTAL_SUM_PRICE)?> 원
			</div>

            <div class="btns text-center" role="group">
                <button type="submit" class="btn btn-default active btn-submit">확인</button>
                <a href="/"><button type="reset" class="btn btn-default">취소</button></a>
            </div>

        </form>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
<script type="text/javascript">
	$(function(){

		$(".btn-submit").click(function(){
			var frm = document.frm;
			
			frm.mode.value = "I";
			frm.method="post";
			frm.action="<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		});

		$(".sel_email_ext").click(function(e){
			e.preventDefault();
			var target_elem = $(this).closest(".dropdown-menu").data("target");
			var sel_email_ext = $(this).html();
			$("[name=" + target_elem + "]").val(sel_email_ext);
		});

		$(".btn-delete").click(function(){

			var cart_no = $(this).data("cart_no");

			var elem = $(".btn-delete");

			(function() {
			  $.getJSON( "json_order.php", {
				mode: "DELETE_CART",
				cart_no: cart_no
			  })
				.done(function( data ) {
				  $.each( data, function( i, item ) {
					  if(item.RESULT == "0")
						  alert('연결오류 : 시스템 관리자와 상의해주세요.');
				  });
				});
			})();

			$(this).closest("tr").hide();

		});

		$("#chk_same").change(function(){
			if($(this).is(":checked")) { 

				var o_mem_name	= $("input[name=o_mem_name]").val();
				var o_email1	= $("input[name=o_email1]").val();
				var o_email2	= $("input[name=o_email2]").val();
				//var o_phone		= $("input[name=o_phone]").val();
				//var o_hphone	= $("input[name=o_hphone]").val();
				var o_zipcode	= $("input[name=o_zipcode]").val();
				var o_addr1		= $("input[name=o_addr1]").val();

				var telphone1	= $("input[name=telphone1]").val();
				var telphone2	= $("input[name=telphone2]").val();
				var telphone3	= $("input[name=telphone3]").val();
				var phone1		= $("input[name=phone1]").val();
				var phone2		= $("input[name=phone2]").val();
				var phone3		= $("input[name=phone3]").val();

				$("input[name=r_mem_name]").val(o_mem_name);
				$("input[name=r_email1]").val(o_email1);
				$("input[name=r_email2]").val(o_email2);
				//$("input[name=r_phone]").val(o_phone);
				//$("input[name=r_hphone]").val(o_hphone);
				$("input[name=r_zipcode]").val(o_zipcode);
				$("input[name=r_addr1]").val(o_addr1);

				$("input[name=r_telphone1]").val(telphone1);
				$("input[name=r_telphone2]").val(telphone2);
				$("input[name=r_telphone3]").val(telphone3);
				$("input[name=r_phone1]").val(phone1);
				$("input[name=r_phone2]").val(phone2);
				$("input[name=r_phone3]").val(phone3);

			} else { 

				$("input[name=r_mem_name]").val('');
				$("input[name=r_email1]").val('');
				$("input[name=r_email2]").val('');
				//$("input[name=r_phone]").val('');
				//$("input[name=r_hphone]").val('');
				$("input[name=r_zipcode]").val('');
				$("input[name=r_addr1]").val('');

				$("input[name=r_telphone1]").val('');
				$("input[name=r_telphone2]").val('');
				$("input[name=r_telphone3]").val('');
				$("input[name=r_phone1]").val('');
				$("input[name=r_phone2]").val('');
				$("input[name=r_phone3]").val('');

			}

		});
	});
</script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

	$(function(){
		$(".trigger-find_addr").click(sample6_execDaumPostcode);
	});

	function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = ''; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    fullAddr = data.roadAddress;

                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    fullAddr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
                if(data.userSelectedType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
				 // 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById("zipcode").value = data.zonecode;
				//document.getElementById("re_zip").value = data.postcode2;
				document.getElementById("addr1").value = fullAddr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById("addr1").focus();


            }
        }).open();
    }

</script>  
</body>
</html>

