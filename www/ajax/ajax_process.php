<?
	require_once "../_classes/com/db/DBUtil.php";
	require_once "../_classes/com/util/Util.php";
	require_once "../_classes/com/util/ImgUtil.php";

	$conn=db_connection("w");
?>
<?
	function getOrderGoodsInfo($db, $orderGoodsNo){
		//주문상품 REC는 하나만 나온다.
		$query	="	SELECT 	OG.GOODS_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME, OG.QTY, OG.ORDER_STATE, OG.SALE_PRICE, OG.DELIVERY_PRICE, OG.DISCOUNT_PRICE,
							OG.OPT_STICKER_NO, OG.OPT_PRINT_MSG, G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
					FROM	TBL_ORDER_GOODS OG
					JOIN	TBL_GOODS G ON OG.GOODS_NO=G.GOODS_NO
					WHERE	OG.ORDER_GOODS_NO = '$orderGoodsNo'
					AND		OG.USE_TF='Y'
					AND		OG.DEL_TF='N'
					AND		G.USE_TF='Y'
					AND		G.DEL_TF='N'
							";

		//  echo $query;
		//  exit;
		$record=array();
		$result=mysql_query($query, $db);
		if($result<>""){
			$record[0]=mysql_fetch_assoc($result);
		}
		return $record[0];
	}//end of function

	function getCountWishList($db, $memberNo){
		$query="SELECT 	COUNT(*)
				FROM	T_WISH_LIST
				WHERE	MEM_NO 	='$memberNo'
				AND		USE_TF	='Y'
				";

		// echo "query : ".$query."<br>";
		// exit;
		$result=mysql_query($query, $db);
		$rows="";
		if($result<>""){
			$rows=mysql_fetch_row($result);
		}
		// echo "cnt : ".$rows[0]."<br>";
		// exit;

		return $rows[0];
	}
	function insertCartWithMemNoAJAX($db, $on_uid, $cp_order_no, $cp_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04,  $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm)
	{
		$opt_request_memo = $memos["opt_request_memo"];
		$opt_support_memo = $memos["opt_support_memo"];

		//$query = "SELECT COUNT(GOODS_NO) AS CNT 
		//						FROM TBL_CART 
		//					 WHERE ON_UID = '$on_uid' 
		//						 AND GOODS_NO = '$goods_no' 
		//						 ";

		//$result = mysql_query($query,$db);
		//$rows   = mysql_fetch_array($result);
		//$cnt  = $rows[0];
		
		//if ($cnt == 0) {

			$query="INSERT INTO TBL_CART (ON_UID, CP_ORDER_NO, CP_NO, BUY_CP_NO, MEM_NO, CART_SEQ, GOODS_NO, QTY,
											OPT_STICKER_NO, OPT_STICKER_MSG, OPT_OUTBOX_TF, DELIVERY_CNT_IN_BOX,
											OPT_WRAP_NO, OPT_PRINT_MSG, OPT_OUTSTOCK_DATE,
											OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, DELIVERY_TYPE, DELIVERY_CP, SENDER_NM, SENDER_PHONE, CATE_01, CATE_02, CATE_03, CATE_04,  
											PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, 
											DELIVERY_PRICE, SA_DELIVERY_PRICE, DISCOUNT_PRICE, STICKER_PRICE, PRINT_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE, 
											USE_TF, REG_ADM, REG_DATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME) 
									values ('$on_uid', '$cp_order_no', '$cp_no', '$buy_cp_no', '$mem_no', '$cart_seq', '$goods_no', '$qty',
										'$opt_sticker_no', '$opt_sticker_msg', '$opt_outbox_tf', '$delivery_cnt_in_box',
										'$opt_wrap_no', '$opt_print_msg', '$opt_outstock_date',
										'$opt_memo', '$opt_request_memo', '$opt_support_memo', '$delivery_type', '$delivery_cp', '$sender_nm', '$sender_phone', 
										'$cate_01', '$cate_02', '$cate_03', '$cate_04', 
										'$price', '$buy_price', '$sale_price', '$extra_price',
										'$delivery_price', '$sa_delivery_price', '$discount_price', '$sticker_price', '$print_price', '$sale_susu', '$labor_price', '$other_price', '$use_tf', '$reg_adm', now(),
										'$goods_code', '$goods_name', '$goods_sub_name'); ";
		
			//echo $query;
			//exit;
		//} 
		//else {
		//	$query = "UPDATE TBL_CART SET QTY = QTY + 1
		//						 WHERE ON_UID = '$on_uid' 
		//							 AND GOODS_NO = '$goods_no' 
		//							 ";
		//}

		//echo $query."<br/>";

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}//end of function

	function selectGoodsAJAX($db, $goods_no) {

		$query = "SELECT GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, RESTOCK_DATE,
								PRICE, BUY_PRICE, SALE_PRICE, NEXT_SALE_PRICE, EXTRA_PRICE, STOCK_CNT, FSTOCK_CNT, BSTOCK_CNT, MSTOCK_CNT, TSTOCK_CNT, 
								TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100, 
								FILE_NM_150, FILE_RNM_150, FILE_PATH_150, FILE_SIZE_150, FILE_EXT_150, CONTENTS, MEMO, 
								DELIVERY_CNT_IN_BOX, STICKER_PRICE, PRINT_PRICE, DELIVERY_PRICE, SALE_SUSU, LABOR_PRICE, OTHER_PRICE,
								READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE
								FROM TBL_GOODS
							WHERE GOODS_NO = '$goods_no' ";

		// echo $query."<br>";
		// exit;
		
		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

    function insertWishList($db, $memberNo, $cpNo, $goodsNo, $goodsCode, $goodsName, $deliveryCntInBox, $salePrice, $imgUrl){
		$query="INSERT INTO T_WISH_LIST(CP_NO, MEM_NO, GOODS_NO, GOODS_CODE, GOODS_NAME, DELIVERY_CNT_IN_BOX, SALE_PRICE, IMG_URL, USE_TF, REG_DATE)
								VALUES('$cpNo', '$memberNo', '$goodsNo', '$goodsCode', '$goodsName', '$deliveryCntInBox', '$salePrice', '$imgUrl','Y', now() ) ";

		// echo $query."<br>";
		// exit;



		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} 
			
			// return true;
	}//end of function
	function deleteFromWishList($db, $memberNo, $goodsNo){
		$query="DELETE 
				FROM 	T_WISH_LIST
				WHERE	GOODS_NO	=	'$goodsNo'
				AND		MEM_NO		=	'$memberNo'
		";

		// echo $query."<br>";
		// exit;

		$result=mysql_query($query, $db);
		if(!$result){
			return 0;
		}
		else{
			return 2;
		}
	}

	function getGoodsInform($db, $memberNo, $goodsNo){

		$rets=array();

		$query="SELECT  W.GOODS_NO, W.GOODS_CODE, W.GOODS_NAME, W.SALE_PRICE, W.DELIVERY_CNT_IN_BOX, W.IMG_URL
				FROM	T_WISH_LIST W
				WHERE 	W.MEM_NO	='$memberNo'
				AND		W.GOODS_NO	='$goodsNo'
				";

			
		// echo $query."<br>";
		// exit;

		$arr=array();
		$stickers=array();
		$cnt=0;
		$result=mysql_query($query, $db);
		if($result<>""){
			$arr=mysql_fetch_assoc($result);

			// print_r($arr);
			// exit;

			$arr['GOODS_NO']				=	urlencode(iconv("euckr", "utf8", 	$arr['GOODS_NO']));
			$arr['GOODS_CODE']				=	urlencode(iconv("euckr", "utf8", 	$arr['GOODS_CODE']));
			$arr['GOODS_NAME']				=	urlencode(iconv("euckr", "utf8", 	SetStringFromDB($arr['GOODS_NAME'])));
			$arr["SALE_PRICE"]				=	urlencode(iconv("euckr", "utf8", 	$arr["SALE_PRICE"]));
			$arr["DELIVERY_CNT_IN_BOX"]		=	urlencode(iconv("euckr", "utf8", 	$arr["DELIVERY_CNT_IN_BOX"]));
			$arr["IMG_URL"]					=	urlencode(iconv("euckr", "utf8",	$arr["IMG_URL"]));


			// print_r($arr);
			// exit;

			$query2="	SELECT 	G.GOODS_NO AS STICKER_NO, G.GOODS_NAME AS STICKER_NAME
						FROM 	T_GOODS_OPTION OP
						JOIN 	TBL_GOODS		G ON G.GOODS_NO=OP.OPTION_GOODS_NO
						WHERE 	OP.GOODS_NO='$goodsNo'
						AND 	OP.OPTION_TYPE='S'
					";

			// echo $query2."<br>";
			// exit;
			

			$result2=mysql_query($query2, $db);
			if($result2<>""){
				$cnt=mysql_num_rows($result2);
				
			}
			if($cnt>0){
				for($i=0; $i<$cnt; $i++){
					$stickers[$i]=mysql_fetch_assoc($result2);
					$stickers[$i]['STICKER_NO']		=urlencode($stickers[$i]["STICKER_NO"]);
					$stickers[$i]['STICKER_NAME']	=urlencode(iconv("euckr","utf8",SetStringFromDB($stickers[$i]["STICKER_NAME"])));
				}
			}
			
		}//end of if($result<>"");
		$rets[0]=$arr;
		$rets[1]=$stickers;

		// print_r($rets[1]);
		// exit;

		// print_r($rets);
		// exit;

		return $rets;

	}//end of function
?>
<?

    $mode=trim($_POST['mode']);

    if($mode=="ADD_GOODS_TO_CART"){
        $goodsNo=trim($_POST['goodsNo']);
        $memberNo=trim($_POST['memberNo']);
		$cpNo=trim($_POST['cpNo']);


        

    }
    else if($mode=="ADD_GOODS_TO_WISHLIST"){
		$goodsNo=trim($_POST['goodsNo']);
        $memberNo=trim($_POST['memberNo']);
		$cpNo=trim($_POST['cpNo']);
		$imgUrl=trim(SetStringToDB($_POST['imgUrl']));

		$query="SELECT 		GOODS_CODE
						, 	GOODS_NAME
						, 	SALE_PRICE
						, 	DELIVERY_CNT_IN_BOX

				FROM	TBL_GOODS
				WHERE	GOODS_NO='".$goodsNo."'
				AND		USE_TF='Y'
				AND		DEL_TF='N'
				";
		$result=mysql_query($query, $conn);
		if($result==""){
			echo -1;
			exit;
		}

		$rows=mysql_fetch_row($result);

		$goodsCode			= $rows[0];
		$goodsName			= trim(SetStringToDB($rows[1]));
		$salePrice			= $rows[2];
		$deliveryCntInBox	= $rows[3];





		$cnt=insertWishList($conn, $memberNo, $cpNo, $goodsNo, $goodsCode, $goodsName, $deliveryCntInBox, $salePrice,$imgUrl);
		if($cnt<>"" && $cnt>0){
			echo $cnt;
		}
		$record=array();
		if($result){
			
			$record['GOODS_NO']=		urlencode(iconv("euckr","utf8",$goodsNo));
			$record['GOODS_NAME']=		urlencode(iconv("euckr","utf8",$goodsName));
			$record['GOODS_CODE']=		urlencode(iconv("euckr","utf8",$goodsCode));
			$record['IMG_URL']=			urlencode(iconv("euckr","utf8",$imgUrl));
			$record['SALE_PRICE']=		urlencode(iconv("euckr","utf8",$salePrice));
			$record['DELIVERY_CNT_IN_BOX']=urlencode(iconv("euckr","utf8",$deliveryCntInBox));

			$arrJson=json_encode($record);
			$rets=urldecode($arrJson);
			echo $rets;

		}
		else{
			echo 0;
		}
		// insertWishList($conn, "","",0,)
    }//end of if(mode=="ADD_GOODS_TO_WISHLIST")

	else if($mode=="EXCLUDE_GOODS_FROM_WISHLIST"){
		$goodsNo=trim($_POST['goodsNo']);
		$memberNo=trim($_POST['memberNo']);

		if(deleteFromWishList($conn, $memberNo, $goodsNo)>0){
			$cnt=0;

			$cnt=getCountWishList($conn, $memberNo);
			if($cnt<>"" && $cnt>0){
				echo $cnt;
			}
			else{
				echo 0;
			}

		}
		// echo "t";

	}

	else if($mode=="GET_GOODS_INFORM"){

		$goodsNo=trim($_POST['goodsNo']);
		$memberNo=trim($_POST['memberNo']);
//GOODS_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE, DELIVERY_CNT_IN_BOX
		$goodsInfo=getGoodsInform($conn, $memberNo, $goodsNo);

		$arrJson=json_encode($goodsInfo);
		$rets=urldecode($arrJson);

		print_r($rets);
		exit;
		echo $rets;
	}//end of mode="GET_GOODS_INFORM"

	else if($mode=="WISHLIST_TO_SHOPPINGBAG"){

		$memberNo	=	$_POST['memberNo'];
		$goodsNo	=	$_POST['goodsNo'];
		$qty		=	$_POST['qty'];
		$stickerNo	=	$_POST['stickerNo'];
		$optPrintMsg=	trim(SetStringToDB(iconv("utf8","euckr",$_POST['optPrintMsg'])));
		$cpNo		=	$_POST['cpNo'];

		$goods=selectGoodsAJAX($conn, $goodsNo);

		// $optStickerNo=			$stickerNo;
		$opt_Print_msg=			$optPrintMsg;
		$price=					$goods[0]["PRICE"];
		$buyPrice=				$goods[0]["BUY_PRICE"];
		$salePrice=				$goods[0]["SALE_PRICE"];
		$stickerPrice=			$goods[0]["STICKER_PRICE"];
		$printPrice=			$goods[0]["PRINT_PRICE"];
		$deliveryCntInBox=		$goods[0]["DELIVERY_CNT_IN_BOX"];
		$deliveryPrice=			$goods[0]["DELIVERY_PRICE"];
		$laborPrice=			$goods[0]["LABOR_PRICE"];
		$orderPrice=			$goods[0]["ORDER_PRICE"];
		$buyCpNo=				$goods[0]["CATE_03"];
		$goodsCode	=			$goods[0]["GOODS_CODE"];
		$goodsName	=			$goods[0]["GOODS_NAME"];
		$goodsSubName	=		$goods[0]["GOODS_SUB_NAME"];

		$sa_delivery_price		=0;
		$discount_price			=0;
		$susu_price				=0;
		$sale_susu				=0;
		$extra_price			=$susu_price;
		$cp_order_no=	"";
		$opt_sticker_msg="";
		$opt_outstock_date="";
		$delivery_type=				0;
		$delivery_cp="";


		$memos =	array();

		$result=insertCartWithMemNoAJAX($conn,"",$cp_order_no, $cpNo, $buyCpNo, $memberNo, 0, $goodsNo, 
								$goodsCode, $goodsName, $goodsSubName, $qty, $stickerNo, $opt_sticker_msg,$opt_outbox_tf, 
								$deliveryCntInBox, $opt_wrap_no, $optPrintMsg,$opt_outstock_date, $opt_memo, $memos, $delivery_type, 
								$delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04, 
								$price, $buyPrice, $salePrice, $extra_price, $deliveryPrice, $sa_delivery_price,
								$discount_price, $stickerPrice, $print_price, $sale_susu, $laberPrice, $order_price, "Y","");
		if($result){
			echo 1;
		}
		else{
			echo 0;
		}
	}
	else if($mode=="GET_ORDER_GOODS"){

		$orderGoodsNo=$_POST['orderGoodsNo'];

		$goods=getOrderGoodsInfo($conn, $orderGoodsNo);

		// print_r($goods);
		// exit;

		$orderGoodsImg=getGoodsImage($goods["FILE_NM_100"], $IMG_URL, $goods["FILE_PATH_150"], $goods["FILE_RNM_150"],400,400);
		$goodsInfo=array();

		
		$goodsInfo["GOODS_NO"]=			urlencode(iconv("euckr","utf8",$goods["GOODS_NO"]));
		$goodsInfo["GOODS_CODE"]=		urlencode(iconv("euckr","utf8",$goods["GOODS_CODE"]));
		$goodsInfo["GOODS_NAME"]=		urlencode(iconv("euckr","utf8",$goods["GOODS_NAME"]));
		$goodsInfo["GOODS_SUB_NAME"]=	urlencode(iconv("euckr","utf8",$goods["GOODS_SUB_NAME"]));
		$goodsInfo["QTY"]=				urlencode(iconv("euckr","utf8",$goods["QTY"]));
		$goodsInfo["ORDER_STATE"]=		urlencode(iconv("euckr","utf8",$goods["ORDER_STATE"]));
		$goodsInfo["SALE_PRICE"]=		urlencode(iconv("euckr","utf8",$goods["SALE_PRICE"]));
		$goodsInfo["DELIVERY_PRICE"]=	urlencode(iconv("euckr","utf8",$goods["DELIVERY_PRICE"]));
		$goodsInfo["OPT_STICKER_NO"]=	urlencode(iconv("euckr","utf8",$goods["OPT_STICKER_NO"]));
		$goodsInfo["OPT_PRINT_MSG"]=	urlencode(iconv("euckr","utf8",$goods["OPT_PRINT_MSG"]));
		$goodsInfo["DISCOUNT_PRICE"]=	urlencode(iconv("euckr","utf8",$goods["DISCOUNT_PRICE"]));
		$goodsInfo["ORDER_GOODS_IMG"]=	urlencode(iconv("euckr","utf8",$orderGoodsImg));

		// $goodsInfo["ORDER_GOODS_NO"]=	urlencode(iconv("euckr","utf8",$orderGoodsNo));
		// $goods["FILE_NM_100"]=		urlencode(iconv("euckr","utf8",$goods["FILE_NM_100"]));
		// $goods["IMG_URL"]=			urlencode(iconv("euckr","utf8",$goods["IMG_URL"]));
		// $goods["FILE_PATH_150"]=	urlencode(iconv("euckr","utf8",$goods["FILE_PATH_150"]));
		// $goods["FILE_RNM_150"]=		urlencode(iconv("euckr","utf8",$goods["FILE_RNM_150"]));



		$arrJson=json_encode($goodsInfo);
		
		$rets=urldecode($arrJson);

		echo $rets;

	}//end of mode="GET_ORDER_GOODS"


?>