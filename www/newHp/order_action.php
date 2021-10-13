<?session_start();?>
<?
    require "../_classes/com/db/DBUtil.php";
    require "../_classes/com/util/Util.php";
    require "../_classes/biz/order/order.php";





    // print_r($_SESSION);

    // echo"<br><br><br>";
    
    // print_r($_POST);

    // exit;


    $conn= db_connection("w");

    // if($is_mobile<>"")

?>
<?
    function getCompanyInfoByCpNo($db, $cpNo){
        $query="SELECT CP_NO, CP_NM, CP_CODE, CP_TYPE
                FROM   TBL_COMPANY
                WHERE = '$cpNo'
                ";
        $result=mysql_query($query, $db);

        if($result<>""){

        }
    }

    function listCartByMemNo1($db, $cp_no, $mem_no, $use_tf, $del_tf, $order_str) {

        $query = "SELECT C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
                                            C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
                                            C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, C.CATE_04 AS C_CATE_04, 
                                            G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, 
                                            C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, G.SALE_PRICE AS CUR_SALE_PRICE,
                                            C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
                                            G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
                                            G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
                    FROM TBL_CART C, TBL_GOODS G 
                    WHERE C.GOODS_NO = G.GOODS_NO
                                    ";

        if ($cp_no <> "") {
            $query .= " AND C.CP_NO = '".$cp_no."' ";
        }
        if($mem_no<>""){
            $query .= "AND C.MEM_NO = '".$mem_no."' ";
        }

        if ($use_tf <> "") {
            $query .= " AND C.USE_TF = '".$use_tf."' ";
        }

        if ($del_tf <> "") {
            $query .= " AND C.DEL_TF = '".$del_tf."' ";
        }

        if ($order_str == "") 
            $order_str = "DESC";

        $query .= " ORDER BY C.CART_NO ".$order_str;

        // echo $query;
        // exit;

        $result = mysql_query($query,$db);
        $record = array();
        

        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }
        return $record;
    }//end of function

    function listCartByCartNos($db, $cp_no, $mem_no, $cartNos, $use_tf, $del_tf, $order_str) {

        $query = "SELECT C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
                                            C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
                                            C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, C.CATE_04 AS C_CATE_04, 
                                            C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, 
                                            C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, G.SALE_PRICE AS CUR_SALE_PRICE,
                                            C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
                                            G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
                                            G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
                    FROM TBL_CART C, TBL_GOODS G 
                    WHERE C.GOODS_NO = G.GOODS_NO
                                    ";
        if($cartNos<>""){
            $query .= "AND C.CART_NO IN ($cartNos) ";
        }

        if ($cp_no <> "") {
            $query .= " AND C.CP_NO = '".$cp_no."' ";
        }
        if($mem_no<>""){
            $query .= "AND C.MEM_NO = '".$mem_no."' ";
        }

        // if ($use_tf <> "") {
        //     $query .= " AND C.USE_TF = '".$use_tf."' ";
        // }

        if ($del_tf <> "") {
            $query .= " AND C.DEL_TF = '".$del_tf."' ";
        }

        if ($order_str == "") 
            $order_str = "DESC";

        $query .= " ORDER BY C.CART_NO ".$order_str;

        // echo $query;
        // exit;

        $result = mysql_query($query,$db);
        $record = array();
        

        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }
        return $record;
    }//end of function

    function getStickerNameByNo($db, $stickerNo){
        $query="SELECT GOODS_NAME, GOODS_SUB_NAME
                FROM TBL_GOODS
                WHERE GOODS_CATE LIKE'0103%'
                AND GOODS_NO='$stickerNo'
                AND USE_TF='Y'
                AND DEL_TF='N'
        ";

        // echo "query : ".$query."<br>";
        // exit;
        $result= mysql_query($query, $db);
        $rows="";
        if($result<>""){
            $rows=mysql_fetch_row($result);
        }
        return $rows[0];
            
    }//end of fucntion

    function getMemberInfo($db ,$memberNo){
        $query= "   SELECT      MEM_NM, EMAIL, PHONE, HPHONE, CP_NO
                    FROM        TBL_MEMBER
                    WHERE       MEM_NO  =   '$memberNo'
                    AND         USE_TF  =   'Y'
                    AND         DEL_TF  =   'N'
                    ";
        
        $result=mysql_query($query, $db);
        $rows="";
        if($result<>""){
            $rows=mysql_fetch_row($result);
        }
        return $rows;
    }//end of function

    function getReservNo($db, $type, $len=13) {

		$thisdate = date("Y-m-d",strtotime("0 month"));;
		$thisdate_Reserve_no = date("Ymd",strtotime("0 month"));;

		$query ="SELECT COUNT(CNT_NO) AS CNT FROM TBL_RESERVE_NO WHERE THIS_DATE = '$thisdate'";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		
		if (!$rows[0]) {
			$sql = " INSERT INTO TBL_RESERVE_NO (CNT_NO, THIS_DATE) VALUES ('1','$thisdate'); ";
		} else {
			$sql = " UPDATE TBL_RESERVE_NO SET CNT_NO = CNT_NO + 1 WHERE THIS_DATE = '$thisdate' ";
		}

		//echo $sql;
		
		mysql_query($sql,$db);
		
		$query ="SELECT IFNULL(MAX(CNT_NO),0) AS NEXT_NO FROM TBL_RESERVE_NO WHERE THIS_DATE = '$thisdate'";
		$result = mysql_query($query,$db);
		$rows   = mysql_fetch_array($result);
		$new_reserve_no  = $thisdate_Reserve_no.$type.right("00000".$rows[0],5);
		
		return $new_reserve_no;
	}//end of function

    function updateCartInfo($db, $cartNos){
        $query="UPDATE TBL_CART
                SET     USE_TF='O'
                ,       REG_ORDER_DATE=NOW()
                WHERE   CART_NO IN ($cartNos)
                ";
        
        // echo "$query<br>";

        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('Cart Inform Update ERROR!');</script>";
        }
    }//end of function

    function insertOrderHomepage($db, $on_uid, $reserve_no, $mem_no, $cp_no, $o_mem_nm, $o_zipcode, $o_addr1, $o_addr2, $o_phone, $o_hphone, $o_email, $r_mem_nm, $r_zipcode, $r_addr1, $r_addr2, $r_phone, $r_hphone, $r_email, $memo, $bulk_tf, $opt_manager_no, $order_state, $total_price, $total_buy_price, $total_sale_price, $total_extra_price, $total_delivery_price, $total_sa_delivery_price, $total_discount_price, $total_qty, $pay_type, $delivery_type, $use_tf, $reg_adm, $homepage_tf, $call_tf) {


		if ($order_state == "1") {
		
			// PAY_DATE added
			$query="INSERT INTO TBL_ORDER (ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO, ORDER_STATE, TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_SA_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, ORDER_DATE, PAY_DATE, PAY_TYPE, DELIVERY_TYPE, USE_TF, REG_ADM, REG_DATE, HOMEPAGE_YN, CALL_YN) 
													 values ('$on_uid','$reserve_no', '$mem_no', '$cp_no', '$o_mem_nm', '$o_zipcode', '$o_addr1', '$o_addr2', '$o_phone', '$o_hphone', '$o_email',
																	 '$r_mem_nm', '$r_zipcode', '$r_addr1', '$r_addr2', '$r_phone', '$r_hphone', '$r_email',
																	 '$memo', '$bulk_tf', '$opt_manager_no', '$order_state', '$total_price', '$total_buy_price', '$total_sale_price', '$total_extra_price', '$total_delivery_price',  '$total_sa_delivery_price', '$total_discount_price', '$total_qty', now(), 
																	 now(), '$pay_type', '$delivery_type', '$use_tf', '$reg_adm', now(), '$homepage_tf', '$call_tf'); ";
		} else {

			$query="INSERT INTO TBL_ORDER (ON_UID, RESERVE_NO, MEM_NO, CP_NO, O_MEM_NM, O_ZIPCODE, O_ADDR1, O_ADDR2, O_PHONE, O_HPHONE, O_EMAIL, R_MEM_NM, R_ZIPCODE, R_ADDR1, R_ADDR2, R_PHONE, R_HPHONE, R_EMAIL, MEMO, BULK_TF, OPT_MANAGER_NO, ORDER_STATE, TOTAL_PRICE, TOTAL_BUY_PRICE, TOTAL_SALE_PRICE, TOTAL_EXTRA_PRICE, TOTAL_DELIVERY_PRICE, TOTAL_SA_DELIVERY_PRICE, TOTAL_DISCOUNT_PRICE, TOTAL_QTY, ORDER_DATE, PAY_TYPE, DELIVERY_TYPE, USE_TF, REG_ADM, REG_DATE, HOMEPAGE_YN, CALL_YN) 
													 values ('$on_uid','$reserve_no', '$mem_no', '$cp_no', '$o_mem_nm', '$o_zipcode', '$o_addr1', '$o_addr2', '$o_phone', '$o_hphone', '$o_email',
																	 '$r_mem_nm', '$r_zipcode', '$r_addr1', '$r_addr2', '$r_phone', '$r_hphone', '$r_email',
																	 '$memo', '$bulk_tf', '$order_state', '$total_price', '$total_buy_price', '$total_sale_price', '$total_extra_price', '$total_delivery_price', '$total_sa_delivery_price', '$total_discount_price', '$total_qty', now(), 
																	 '$pay_type', '$delivery_type', '$use_tf', '$reg_adm', now(), '$homepage_tf', '$call_tf'); ";
		}
		
		//echo $query;
		//exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}
	}

?>
<? // PHP_PROCESS_START
    $memberNo=$_SESSION["C_MEM_NO"];
    $cpNo=  $_SESSION["C_CP_NO"];

    $rowCnt=count($cartNo);
    $cartNos="";
    for($k=0; $k<$rowCnt; $k++){
        $cartNos.=$cartNo[$k].", ";
    }
    $cartNos=rtrim($cartNos, ", ");

    echo "cartNos : ".$cartNos."<br>";
    // exit;

    // echo $cartNos."<br>";
    // exit;



    // print_r($_POST);
    // echo "<br>---------------------------------------------------------------------------------------------------------------------------------<br>";
    // print_r($_REQUEST);
    // exit;11
    

    $oName      =   trim($_POST["oName"]);
    $oEmail1    =   trim($_POST["oEmail1"]);
    $oEmail2    =   trim($_POST["oEmail2"]);
    $oHPhone1   =   trim($_POST["oHPhone1"]);
    $oHPhone2   =   trim($_POST["oHPhone2"]);
    $oHPhone3   =   trim($_POST["oHPhone3"]);
    $rName      =   trim($_POST["rName"]);
    $rEmail1    =   trim($_POST["rEmail1"]);
    $rEmail2    =   trim($_POST["rEmail2"]);
    $rHPhone1   =   trim($_POST["rHPhone1"]);
    $rHPhone2   =   trim($_POST["rHPhone2"]);
    $rHPhone3   =   trim($_POST["rHPhone3"]);
    $rZipCode   =   trim($_POST["rZipCode"]);
    $rAddr1     =   trim(SetStringToDB($_POST["rAddr1"]));
    $rAddr2     =   trim(SetStringToDB($_POST["rAddr2"]));
    $memo       =   trim(SetStringToDB($_POST["memo"]));
    $rMemo      =   trim(SetStringToDB($_POST["rMemo"]));

    $callyn     =   trim($_POST["callyn"]);
    $homepageyn  =   'Y';

    $cartList = listCartByCartNos($conn, $cp_no, $memberNo, $cartNos, "Y", "N", "");
    $cntCart  = sizeof($cartList);

    $newReserveNo= getReservNo($conn,"EN");

    $totalPrice=0;
    $totalBuyPrice=0;
    $totalSalePrice=0;
    $totalExtraPrice=0;
    $totalDeliveryPrice=0;
    $totalSADeliveryPrice=0;
    $totalDiscountPrice=0;
    $totalQty=0;


    
    // echo "CntCart : ".$cntCart."<br>";
    // exit;


    $cartNos = "";



    for($i=0; $i<$cntCart; $i++){

        $qty=$cartList[$i]["QTY"];

        $cartNos.=$cartList[$i]["CART_NO"].", ";

        $totalPrice=            $totalPrice+$cartList[$i]["PRICE"]*$qty;
        $totalBuyPrice=         $totalBuyPrice+$cartList[$i]["BUY_PRICE"]*$qty;
        $totalSalePrice=        $totalSalePrice+$cartList[$i]["SALE_PRICE"]*$qty;
        $totalExtraPrice=       $totalExtraPrice+$cartList[$i]["EXTRA_PRICE"];
        $totalDeliveryPrice=    $totalDeliveryPrice+$cartList[$i]["DELIVERY_PRICE"];
        $totalSADeliveryPrice=  $totalSADeliveryPrice+$cartList[$i]["SA_DELIVERY_PRICE"];
        $totalDiscountPrice=    $totalDiscountPrice+$cartList[$i]["DISCOUNT_PRICE"];
        $totalQty       =       $totalQty+$cartList[$i]["QTY"];

        insertOrderGoods($conn, "", $newReserveNo, "", "", $memberNo, "0", $cartList[$i]["GOODS_NO"], $cartList[$i]["GOODS_CODE"], $cartList[$i]["GOODS_NAME"], $cartList[$i]["GOODS_SUB_NAME"],
                        $cartList[$i]["QTY"], $cartList[$i]["OPT_STICKER_NO"], $cartList[$i]["OPT_STICKER_MSG"], $cartList[$i]["OPT_OUTBOX_TF"], $cartList[$i]["DELIVERY_CNT_IN_BOX"], $cartList[$i]["OPT_WRAP_NO"],
                        $cartList[$i]["OPT_PRINT_MSG"], $cartList[$i]["OPT_OUTSTOCK_DATE"], $cartList[$i]["OPT_MEMO"], $memos, $cartList[$i]["DELIVERY_TYPE"], $cartList[$i]["DELIVERY_CP"], $cartList[$i]["SENDER_NM"],
                        $cartList[$i]["SENDER_PHONE"], $cartList[$i]["C_CATE_01"], $cartList[$i]["C_CATE_02"], $cartList[$i]["C_CATE_03"], $cartList[$i]["C_CATE_04"], $cartList[$i]["PRICE"], $cartList[$i]["BUY_PRICE"], $cartList[$i]["SALE_PRICE"],
                        $cartList[$i]["EXTRA_PRICE"], $cartList[$i]["DELIVERY_PRICE"], $cartList[$i]["SA_DELIVERY_PRICE"],$cartList[$i]["DISCOUNT_PRICE"], $cartList[$i]["STICKER_PRICE"], $cartList[$i]["PRINT_PRICE"], $cartList[$i]["SALE_SUSU"],
                        $cartList[$i]["LABOR_PRICE"], $cartList[$i]["OTHER_PRICE"], $cartList[$i]["TAX_TF"], "1", "Y", $_SESSION["C_MEM_ID"]
                    );

    }//end of for();

    $oHPhone=$oHPhone1."-".$oHPhone2."-".$oHPhone3;
    $oPhome =$oPhone1."-".$oPhone2."-".$oPhone3;
    $oEmail =$oEmail1."@".$oEmail2;


    $rPhone =$rPhone1."-".$rPhone2."-".$rPhone3;
    $rHPhone=$rHPhone1."-".$rHPhone2."-".$rHPhone3;
    $rEmail =$rEmail1."@".$rEmail2;


    insertOrderHomepage($conn, "", $newReserveNo, $memberNo, $cpNo, 
                $oName, $Ozipcode, $Oaddr1, $Oaddr2, $oPhome, $oHPhone, $oEmail, 
                $rName, $rZipCode, $rAddr1, $rAddr2, $rPhone, $rHPhone, $rEmail, 
                $rMemo, "N", $opt_manager_no, "1", 
                $totalPrice, $totalBuyPrice, $totalSalePrice, $totalExtraPrice, $totalDeliveryPrice, $totalSADeliveryPrice, $totalDiscountPrice, 
                $totalQty, $pay_type, $delivery_type, "Y", $_SESSION["C_MEM_ID"], $homepageyn, $callyn);

    $cartNos= rtrim($cartNos, ", ");

    updateCartInfo($conn, $cartNos);


    echo "is_mobile : ".$is_mobile."<br>";
    // exit;

    if($is_mobile<>""){
        header('Location:Morder_complete.php?reserveno='.$newReserveNo);
    }
    else{
        header('Location:order_complete.php?reserveno='.$newReserveNo);
    }
    // exit;
    //insertOrder을 여기서 해야하는 이유(ordergoods_ 다 한다음에) : TBL_ORDER

?>