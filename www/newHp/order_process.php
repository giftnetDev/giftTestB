<? require "../_common/home_pre_setting.php";  ?>
<?//function declaration region


    function selectCurrentGoods($db, $goodsNo){
        $query ="SELECT G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, G.SALE_PRICE, G.DELIVERY_PRICE
                FROM    TBL_GOODS G
                WHERE   GOODS_NO='$goodsNo'
                AND     USE_TF='Y'
                AND     DEL_TF='N'
                ";

        // echo $query;
        // exit;

        $result=mysql_query($query, $db);
        $record=array();
        if($result<>""){
            $record[0]=mysql_fetch_assoc($result);
            return $record;
        }
        else{
            echo "<script>alert('GET_CURRENT_GOODS_ERRROR');</script>";
            exit;
        }
    }
    function getLatestCartNoWithMemberNo($db, $memberNo){
        $query="SELECT MAX(CART_NO)
                FROM    TBL_CART
                WHERE   MEM_NO = '$memberNo'
                ";
        
        $result=mysql_query($query, $db);
        if($result<>""){
            $rows=mysql_fetch_row($result);
            return $rows[0];
        }
        else{
            echo "<script>alert('GET_LATEEST_CART_NO ERROR');</script>";
            exit;
        }
    }

    function listCartByCartNo($db, $cartNos, $memberNo){
        $query="SELECT  C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
                        C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
                        C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, C.CATE_04 AS C_CATE_04, 
                        G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, 
                        C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, G.SALE_PRICE AS CUR_SALE_PRICE,
                        C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
                        G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
                        G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
                FROM TBL_CART C, TBL_GOODS G 
                WHERE C.GOODS_NO = G.GOODS_NO
                AND C.MEM_NO    =   '$memberNo'
                AND C.CART_NO   IN  ($cartNos)
                AND C.DEL_TF    =   'N'
                AND G.USE_TF    =   'Y'
                AND G.DEL_TF    =   'N'
                
                ";

        // echo $query;
        // exit;

        $result= mysql_query($query,$db);
        $record = array();
        $cnt = 0;

        if($result <> ""){
            $cnt=mysql_num_rows($result);
            for($i=0; $i < $cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            return $record;

        }
        else{
            echo "<script>alert('SELECT_ORDER_ERROR');</script>";
            exit;
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

    function getOrderGoodsWithMemberNo($db, $memberNo, $cpNo, $goodsNo){
        $query ="SELECT ";
    }
    function listCartByMemNo2($db, $cp_no, $mem_no, $goodsNo, $use_tf, $del_tf, $order_str) {

        $query = "SELECT C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, 
                                            C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
                                            C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, C.CATE_04 AS C_CATE_04, 
                                            G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, C.PRICE, C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, 
                                            C.USE_TF, C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, G.SALE_PRICE AS CUR_SALE_PRICE,
                                            C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
                                            G.IMG_URL, G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, 
                                            G.FILE_NM_150, G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
                    FROM TBL_CART C, TBL_GOODS G 
                    WHERE C.GOODS_NO='".$goodsNo."'
                    AND C.GOODS_NO = G.GOODS_NO

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
        $query= "   SELECT      MEM_NM, EMAIL, PHONE, HPHONE, CP_NO, ZIPCODE, ADDR1, ADDR2
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
    }

    function accountBankSelectBox($db,$pcode,$objname) 
    {
		$query = "SELECT DCODE, DCODE_NM
					FROM TBL_CODE_DETAIL 
                   WHERE DEL_TF = 'N' 
                     AND USE_TF = 'Y' ";
		
		if ($pcode <> "") {
			$query .= " AND PCODE = '".$pcode."' ";
		}
		
		$query .= " ORDER BY DCODE_SEQ_NO ";

		// echo $query."<br>";
		
		$result = mysql_query($query,$db);
		$total  = mysql_affected_rows();

		$tmp_str = "<select name='".$objname."'  class=\"cashboxsel\" style='width: 93%; height: 34px; border: 1px solid #d4d4d4;'>";

		for($i=0 ; $i< $total ; $i++) {
			mysql_data_seek($result,$i);
			$row     = mysql_fetch_array($result);
			
			$RS_DCODE		= Trim($row[0]);
			$RS_DCODE_NM	= Trim($row[1]);

			$tmp_str .= "<option value='".$RS_DCODE."'>".$RS_DCODE_NM."</option>";
		}
		$tmp_str .= "</select>";

		return $tmp_str;
	}


?>
<?//Page Process Region
    // print_r($_SESSION);

    // if(!$_SESSION["C_MEM_NO"]){
        
    // }

    $memberNo=$_SESSION["C_MEM_NO"];

    if(!$memberNo){
        // return ;
        echo "<script>alert('로그인이 되어있지 않습니다');</script>";
        echo "<script>location.href='log-in.php'</script>";
        return;
    }

    // if(!$memberNo){
    //     header("Location:log-in.php");

    // }

    $conn =db_connection("w");
    $mode=$_POST['mode'];
    // echo "mode : $mode<br>";
    // exit;

    if($mode=="ORDER_CURRENT_ITEM"){

        $goodsNo=       $_POST['goods_no'];
        $goodsInfo=selectCurrentGoods($conn, $goodsNo);
        // echo "상품값<br>";

        $goodsCode  = $goodsInfo[0]["GOODS_CODE"];
        $goodsName  = $goodsInfo[0]["GOODS_NAME"];
        $goodsSubName=$goodsInfo[0]["GOODS_SUB_NAME"];        
        $salePrice=     $goodsInfo[0]["SALE_PRICE"];
        $deliveryPrice= $goodsInfo[0]["DELIVERY_PRICE"];

        $goodsQty=      $_POST['qty'];
        $imgUrl =       $_POST['filePath'];
        $stickerNo=     $_POST['selectSticker'];
        $optPrintMsg=   $_POST['opt_print_msg'];

        $optPrintMsg   =str_replace("\r\n",'<br>', $optPrintMsg);

        // echo "goodsQty : ".$_POST['qty'];s


        insertCartWithMemNo( $conn, $on_uid_X, $cp_order_no_X, $_SESSION['C_CP_NO'], $buy_cp_no_X, $memberNo, $cart_seq_X, $goodsNo, $goodsCode,$goodsName,$goodsSubName,$goodsQty, $stickerNo,
                    $opt_sticker_msg_X, $opt_outbox_tf_X, $delivery_cnt_in_box_X, $opt_wrap_no_X, $optPrintMsg, $opt_outstock_date_X, $opt_memo_X,
                    $memos_X, $delivery_type_X, $delivery_cp_X, $sender_nm_X, $sender_phone_X, $cate_01_X, $cate_02_X, $cate_03_X, $cate_04_X, $price_X, $buy_price_X, $salePrice,
                    $extra_price_X, $deliveryPrice, $sa_delivery_price_X, $discount_price_X, $sticker_price_x, $print_price_X, $sale_susu_X, $labor_price_X, $other_price_X, 
                    "N", $reg_adm_X);


        $cartNo=getLatestCartNoWithMemberNo($conn, $memberNo);

        $listCart=listCartByCartNo($conn, $cartNo, $memberNo);


        // $listCart=listCartByMemNo2($conn, $cp_no, $memberNo,$goodsNo,"Y", "N", $orderState);
    }
    else if($mode=="ORDER_SELECT"){
        // print_r($_POST);

        $rowCnt=count($chk);
        if($rowCnt<1){
            ?>
                <!DOCTYPE html>
                <html lang="ko">
                <head>
                    <meta charset="EUC-KR">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <?
            echo "<script>alert('선택된 상품이 없습니다');</script>";
            ?>
            <script>
                
                history.back();
            </script>
            <?
            exit;
        }
        $cartNos="";
        for($k=0; $k<$rowCnt; $k++){
            $cartNos.=$chk[$k].", ";
        }
        $cartNos = trim($cartNos, ", ");

        $listCart = listCartByCartNo($conn, $cartNos, $memberNo);
        
    }
    else{
        // echo "개인값<br>";
        // $listCart=listCartByMemNo1($conn, $cp_no, $memberNo, "Y", "N", $orderState);
    }


    $cntCart2=sizeof($listCart);
    if($cntCart2<1){
        echo "<script>alert('로그인이 되어있지 않거나 주문할 상품이 없습니다');</script>";
        ?>
            <script>
                history.back();
            </script>
        <?
        exit;
    }


    $memberInfo=getMemberInfo($conn, $memberNo);

    if($memberInfo==""){
        exit;
    }

    $memberName=$memberInfo[0];
    $emails=explode("@",$memberInfo[1]);
    $phones=explode("-",$memberInfo[2]);
    $hPhones=explode("-",$memberInfo[3]);
    $cpNo=$memberInfo[4];

    $Ozipcode   = $memberInfo[5];
    $Oaddr1     = $memberInfo[6];
    $Oaddr2     = $memberInfo[7];
    

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./header.php"; ?>
        <script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
        <script>
            function js_same_click(){
                if($("#same").is(":checked")==true){
                    $("input[name='rName']").val($("input[name='oName']").val());
                    $("input[name='rEmail1']").val($("input[name='oEmail1']").val());
                    $("input[name='rEmail2']").val($("input[name='oEmail2']").val());

                    $("input[name='rHPhone1']").val($("input[name='oHPhone1']").val());
                    $("input[name='rHPhone2']").val($("input[name='oHPhone2']").val());
                    $("input[name='rHPhone3']").val($("input[name='oHPhone3']").val());

                    $("input[name='rZipCode']").val($("input[name='Ozipcode']").val());
                    $("input[name='rAddr1']").val($("input[name='Oaddr1']").val());
                    $("input[name='rAddr2']").val($("input[name='Oaddr2']").val());
                }//end of if
                else{
                    if($("input[name='rName']").val() ==$("input[name='oName']").val()){
                        $("input[name='rName']").val('');
                    }
                    if($("input[name='rEmail1']").val() ==$("input[name='oEmail1']").val()){
                        $("input[name='rEmail1']").val('');
                    }
                    if($("input[name='rEmail2']").val() ==$("input[name='oEmail2']").val()){
                        $("input[name='rEmail2']").val('');
                    }
                    if($("input[name='rHPhone1']").val() ==$("input[name='oHPhone1']").val()){
                        $("input[name='rHPhone1']").val('');
                    }
                    if($("input[name='rHPhone2']").val() ==$("input[name='oHPhone2']").val()){
                        $("input[name='rHPhone2']").val('');
                    }
                    if($("input[name='rHPhone3']").val() ==$("input[name='oHPhone3']").val()){
                        $("input[name='rHPhone3']").val('');
                    }

                    if($("input[name='rZipCode']").val() ==$("input[name='Ozipcode']").val()){
                        $("input[name='rZipCode']").val('');
                    }
                    if($("input[name='rAddr1']").val() ==$("input[name='Oaddr1']").val()){
                        $("input[name='rAddr1']").val('');
                    }
                    if($("input[name='rAddr2']").val() ==$("input[name='Oaddr2']").val()){
                        $("input[name='rAddr2']").val('');
                    }

                }
            }//end of function


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
                        // alert('');

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

                            // alert(fullAddr);
                        }
                                        
                        if (document.getElementById("addr_type").value == "s") {

                            // alert('s');
                        // 우편번호와 주소 정보를 해당 필드에 넣는다.
                            $("input[name='rZipCode").val(data.zonecode);
                            // document.getElementById("rZipCode").value = data.zonecode;
                            // alert('z');
                            //document.getElementById("cp_zip").value = data.postcode2;
                            $("input[name='rAddr1']").val(fullAddr);
                            // document.getElementById("rAddr1").value = fullAddr;
                            // alert('a');
                            // 커서를 상세주소 필드로 이동한다.
                            $("input[name='rAddr2']").focus();
                            // document.getElementById("cp_addr").focus();
                        } 
                        // else {
                        // // 우편번호와 주소 정보를 해당 필드에 넣는다.
                        //     document.getElementById("re_zip").value = data.zonecode;
                        //     //document.getElementById("re_zip").value = data.postcode2;
                        //     document.getElementById("re_addr").value = fullAddr;
                        //     // 커서를 상세주소 필드로 이동한다.
                        //     document.getElementById("re_addr").focus();
                        // }


                    }
                }).open();
            }// end of function

            
            function js_addr_open1(s) {
                
                document.getElementById("addr_type").value = s;
                sample6_execDaumPostcode();

            }//end of function();

            function checkEssentialInfo()
            {
                if($("input[name='rName']").val()=='')
                {
                    alert('수령자 이름 입력 해 주세요.');
                    $("input[name='rName']").focus();
                    return false;
                }
            //----------------------이메일---------------------------
                if($("input[name='rEmail1']").val()=='' || $("input[name='rEmail2']").val()=='')
                {
                    if($("input[name='rEmail1']").val() == "")
                    {	
                        $("input[name='rEmail1']").focus();
                    }
                    else
                    {
                        $("input[name='rEmail2']").focus();
                    }        
                    return false;
                }

                if (!CheckEmail($("input[name='rEmail1']").val()+"@"+$("input[name='rEmail2']").val()))	return;
                
            //--------------------핸드폰-----------------------------
                if($("input[name='rHPhone1']").val()=='' || $("input[name='rHPhone1']").val().length!='3')
                {
                    alert('핸드폰 번호를 정확히 입력 해 주세요.');
                    $("input[name='rHPhone1']").focus();
                    return false;
                }

                if($("input[name='rHPhone2']").val() =='' || ($("input[name='rHPhone2']").val().length!='3' && $("input[name='rHPhone2']").val().length!='4'))
                {
                    alert('핸드폰 번호를 정확히 입력 해 주세요.');
                    $("input[name='rHPhone2']").focus();
                    return false;
                }

                if($("input[name='rHPhone3']").val()=='' || $("input[name='rHPhone3']").val().length!='4')
                {
                    alert('핸드폰 번호를 정확히 입력 해 주세요.');
                    $("input[name='rHPhone3']").focus();
                    return false;
                }
            //--------------------우편번호---------------------------
                if($("input[name='rZipCode']").val()=='')
                {
                    alert('우편번호를 입력해 주세요.');
                    $("input[name='rZipCode']").focus();
                    return false;
                }

            //--------------------주소-------------------------------
                if($("input[name='rAddr1']").val()=='')
                {
                    alert('주소를 정확히 입력 해 주세요.');
                    $("input[name='rAddr1']").focus();
                    return false;
                }

                if($("input[name='rAddr2']").val()=='')
                {
                    alert('주소를 정확히 입력 해 주세요.');
                    $("input[name='rAddr2']").focus();
                    return false;
                }

                /*if(document.mainFrm.rd_pay_method.value == '0')
                {   
                    if(document.mainFrm.rd_pay_receipt.value == '')
                    {
                        alert('세금계산서 & 현금영수증 & 신청안함 선택 해 주세요.');
                        return false;
                    }
                    
                    if(document.mainFrm.rd_pay_receipt.value == 'T')
                    {
                        if ($("input[name='biz_num1']").val() == '' || $("input[name='biz_num1']").val().length!='3')
                        {
                            alert("사업자 번호를 입력 해주세요.");
                            $("input[name='biz_num1']").focus();
                            return false;
                        }

                        if ($("input[name='biz_num2']").val() == '' || $("input[name='biz_num2']").val().length!='2')
                        {
                            alert("사업자 번호를 입력 해주세요..");
                            $("input[name='biz_num2']").focus();
                            return false;
                        }

                        if ($("input[name='biz_num3']").val() == '' || $("input[name='biz_num3']").val().length!='5')
                        {
                            alert("사업자 번호를 입력 해주세요...");
                            $("input[name='biz_num3']").focus();
                            return false;
                        }

                        if (!checkbizNo($("input[name='biz_num1']").val()+$("input[name='biz_num2']").val()+$("input[name='biz_num3']").val()))    return;

                        if ($("input[name='companyname']").val() == "") 
                        {
                            alert("상호(법인명)을 입력 해 주세요.");
                            $("input[name='companyname']").focus();
                            return false;
                        }
                        
                        if ($("input[name='ceoname']").val() == "") 
                        {
                            alert("대표자명을 입력 해 주세요.");
                            $("input[name='ceoname']").focus();
                            return false;
                        }

                        if ($("input[name='uptae']").val() == "") 
                        {
                            alert("업태를 입력 해 주세요");
                            $("input[name='uptae']").focus();
                            return false;
                        }

                        if ($("input[name='jongmog']").val() == "") 
                        {
                            alert("종목을 입력 해 주세요.");
                            $("input[name='jongmog']").focus();
                            return false;
                        }

                        if ($("input[name='companyaddr']").val() == "") 
                        {
                            alert("사업장주소를 입력 해 주세요.");
                            $("input[name='companyaddr']").focus();
                            return false;
                        }
                    }
                    else if(document.mainFrm.rd_pay_receipt.value == 'C')
                    {
                        if(document.mainFrm.rd_pay_receipt_dt.value == 'I')
                        {
                            if($("input[name='custnm']").val()=='')
                            {
                                alert('고객명을 입력 해 주세요.');
                                $("input[name='custnm']").focus();
                                return false;
                            }

                            if($("input[name='custphone1']").val()=='' || $("input[name='custphone1']").val().length!='3')
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='custphone1']").focus();
                                return false;
                            }

                            if($("input[name='custphone2']").val() =='' || ($("input[name='custphone2']").val().length!='3' && $("input[name='custphone2']").val().length!='4'))
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='custphone2']").focus();
                                return false;
                            }

                            if($("input[name='custphone3']").val()=='' || $("input[name='custphone3']").val().length!='4')
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='custphone3']").focus();
                                return false;
                            }
                        }
                        else
                        {
                            if ($("input[name='cash_biz_num1']").val() == '' || $("input[name='cash_biz_num1']").val().length!='3')
                            {
                                alert("사업자 번호를 입력 해주세요.");
                                $("input[name='cash_biz_num1']").focus();
                                return false;
                            }

                            if ($("input[name='cash_biz_num2']").val() == '' || $("input[name='cash_biz_num2']").val().length!='2')
                            {
                                alert("사업자 번호를 입력 해주세요..");
                                $("input[name='cash_biz_num2']").focus();
                                return false;
                            }

                            if ($("input[name='cash_biz_num3']").val() == '' || $("input[name='cash_biz_num3']").val().length!='5')
                            {
                                alert("사업자 번호를 입력 해주세요...");
                                $("input[name='cash_biz_num3']").focus();
                                return false;
                            }

                            if (!checkbizNo($("input[name='cash_biz_num1']").val()+$("input[name='cash_biz_num2']").val()+$("input[name='cash_biz_num3']").val()))    return;

                            if($("input[name='bizphone1']").val()=='' || $("input[name='bizphone1']").val().length!='3')
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='bizphone1']").focus();
                                return false;
                            }

                            if($("input[name='bizphone2']").val() =='' || ($("input[name='bizphone2']").val().length!='3' && $("input[name='bizphone2']").val().length!='4'))
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='bizphone2']").focus();
                                return false;
                            }

                            if($("input[name='bizphone3']").val()=='' || $("input[name='bizphone3']").val().length!='4')
                            {
                                alert('전화 번호를 정확히 입력 해 주세요.');
                                $("input[name='bizphone3']").focus();
                                return false;
                            }
                        }        
                    }

                    if ($("input[name='depositnm']").val() == "") 
                    {
                        alert("입금자명을 입력 해 주세요.");
                        $("input[name='depositnm']").focus();
                        return false;
                    }
                }*/

                if($("input:checkbox[name=agreeYn]").is(":checked") == true) 
                {
                    return true;
                }
                else
                {
                    alert("구매진행 동의에 체크 해 주셔야 결제가 진행 됩니다.");
                    return false;
                }
            }
            function js_order_complete(){

                if(!checkEssentialInfo()){
                    return;
                }

                if(!confirm("주문을 완료 하시겠습니까?")) return;

                var frm=document.mainFrm;
                frm.target="";
                frm.method="POST";
                frm.action="./order_action.php";
                frm.submit();
                
            }//end of function

            function js_sel_pament(val)
            {
                if(val == "0")
                {
                    $('input[name="rd_pay_receipt"]').removeAttr('checked');

                    //$('input[name="rd_pay_receipt"]').filter("[value=X]").prop("checked", true);

                    document.getElementById("payment_tax_receipt").style.display ="block";

                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    //document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("cashbox").style.display ="block";
                    document.getElementById("cardbox").style.display ="none";   
                }
                //else if(val == "1")
                else
                {
                    alert("신용카드매출전표를 세금계산서로 사용할 수 있도록\n\n부가세법이 변경되어 별도로 세금계산서가 발송되지 않습니다.\n\n신용카드 결제 후 영수증을 인쇄하여 세금계산서로 사용하세요.\n\n확인 후 결제하기 버튼을 한 번만 눌러주세요.");

                    document.getElementById("payment_tax_receipt").style.display ="none";
                    
                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    //document.getElementById("payment_method_inner3").style.display ="block";

                    document.getElementById("cashbox").style.display ="none";
                    document.getElementById("cardbox").style.display ="block";                    
                }
                /*else
                {
                    document.getElementById("payment_tax_receipt").style.display ="none";

                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("cashbox").style.display ="none";
                    document.getElementById("cardbox").style.display ="block";
                }*/
            }

            function js_sel_receipt(val)
            {
                if(val == "T")
                {
                    document.getElementById("payment_method_inner1").style.display ="block";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    //document.getElementById("payment_method_inner3").style.display ="none";
                }
                else if(val == "C")
                {
                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="block";
                    //document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("individual").style.display ="block";
                    document.getElementById("buisnessman").style.display ="none";

                    $('input[name="rd_pay_receipt_dt"]').removeAttr('checked');
                    $('input[name="rd_pay_receipt_dt"]').filter("[value=I]").prop("checked", true);
                }
                else
                {
                    alert("세금계산서가 필요하신 고객은 익월 10일 이전에\n\n고객명, 금액, 이메일주소를 사업자등록증\n\n사본에 기재하여, 아래 팩스번호로 보내주시면\n\n이메일로 발송 후 연락 드리겠습니다.\n\nFAX : 031-527-6858");

                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    //document.getElementById("payment_method_inner3").style.display ="none";
                }
            }

            function js_sel_IB(val)
            {
                if(val == "I")
                {
                    document.getElementById("individual").style.display ="block";
                    document.getElementById("buisnessman").style.display ="none";
                }
                else
                {
                    document.getElementById("individual").style.display ="none";
                    document.getElementById("buisnessman").style.display ="block";
                }
            }

            //maxlength 체크
            function maxLengthCheck(object)
            {
                if (object.value.length > object.maxLength)
                {
                    object.value = object.value.slice(0, object.maxLength);
                }   
            }

            function checkbizNo(bizID) 
            {    
                alert(bizID);
                // bizID는 숫자만 10자리로 해서 문자열로 넘긴다. 
                var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1); 
                var tmpBizID, i, chkSum=0, c2, remander; 
                bizID = bizID.replace(/-/gi,''); 

                for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i); 
                c2 = "0" + (checkID[8] * bizID.charAt(8)); 
                c2 = c2.substring(c2.length - 2, c2.length); 
                chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1)); 
                remander = (10 - (chkSum % 10)) % 10 ; 

                if (Math.floor(bizID.charAt(9)) != remander)
                {
                    alert("사업자 번호를 확인 해 주세요.");
                    return false; 
                } 

                return true;
                
            }

            function CheckEmail(email)
            {                       
                var reg_email = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;
                //alert(email);
                if(!reg_email.test(email))
                {                             
                    alert("이메일 형식이 올바르지 않습니다.")
                    return false;         
                }
                
                return true;
            }  

            function js_call_yn()
            {
                if($("#callyn").is(":checked")==true)
                {
                    $("input[name='callyn']").val('Y');
                }
                else
                {
                    $("input[name='callyn']").val('N');
                }
            }
        </script>
    </head>

    <body>

    <script>
	$(document).ready(function(){

        var frm = document.mainFrm;

        /*frm.rd_pay_method.value = "0";      //무통장입금
        document.getElementById("payment_method_inner1").style.display ="none";
        document.getElementById("payment_method_inner2").style.display ="none";
        //document.getElementById("payment_method_inner3").style.display ="none";

        document.getElementById("cashbox").style.display ="block";
        document.getElementById("cardbox").style.display ="none";
        
        결제정보 있을때 주석풀자.
        */

	});
    </script>

        <div class="wrap">
            
            <? require "./top.php"; ?>
            <style>

            .cashboxsel { font-size:13px !important; font-weight:normal !important; color:gray !important; text-align: left !important;}

            </style>
            <form name='mainFrm'>
                <div class="detail_page">
                    <div class="detail_page_inner">
                        <div class="cart_info">
                            <h4>주문/결제</h4>

                            <div class="order_box">
                                <table>
                                    <tr>
                                        <th colspan="2">상품정보</th>
                                        <th>상품가격</th>
                                        <th>배송비</th>
                                        <th>수량</th>
                                        <th>할인</th>
                                        <th>상품금액 (할인포함)</th>
                                    </tr>
                                    <?
                                        if($cntCart2>0){
                                            $allTotalPrice=0;
                                            for($i = 0; $i < $cntCart2; $i++){

                                                $imgUrl	= getGoodsImage($listCart[$i]["FILE_NM_100"], $listCart[$i]["IMG_URL"], $listCart[$i]["FILE_PATH_150"], $listCart[$i]["FILE_RNM_150"], "50", "50");
                                                //$imgUrl=$listCart[$i]["FILE_PATH_150"].$listCart[$i]["FILE_RNM_150"];
                                                //$totalSalePrice = ($listCart[$i]["SALE_PRICE"] * $listCart[$i]["QTY"]) + $listCart[$i]["DELIVERY_PRICE"]; 박스 입수 이상 결제는 배송비 0
                                                $totalSalePrice=$listCart[$i]["SALE_PRICE"]*$listCart[$i]["QTY"]
                                                // echo "<br>";
                                                // echo "imgUrl : ".$imgUrl."<br>";
                                            ?>
                                                <tr>
                                                    <td> <div class="thumb_img" style="background:url('<?=$imgUrl?>') no-repeat; background-size:cover; background-position:center center;"></div></td>
                                                    <td style="width:350px !important"><?=$listCart[$i]['GOODS_NAME']?><br>
                                                    <?
                                                        if($listCart[$i]["OPT_STICKER_NO"]>0 || $listCart[$i]["OPT_PRINT_MSG"]){
                                                            ?>
                                                                <i>- 옵션 -</i></br>
                                                            <?
                                                            $optStickerNo=$listCart[$i]["OPT_STICKER_NO"];
                                                            $optPrintMsg=SetStringFromDB($listCart[$i]["OPT_PRINT_MSG"]);
                                                            if($optStickerNo>0){
                                                                $optSticker=SetStringFromDB(getStickerNameByNo($conn, $optStickerNo));
                                                                ?>
                                                                    <i>스티커 옵션 : <?=$optSticker?></i><br>
                                                                <?
                                                            }
                                                            if($optPrintMsg<>""){
                                                            ?>
                                                                <i>인쇄 옵션 : <?=$optPrintMsg?></i>
                                                            <?
                                                            }
                                                        }
                                                    ?>
                                                        <!-- <i>스티커 : 농협 A타입 대<br>인쇄메세지 : 예쁘게 포장해 주세요</i> -->

                                                    </td><!--상품정보-->
                                                    <td><?=number_format($listCart[$i]["SALE_PRICE"])?>원</td>
                                                    <td>0원</td>    <!-- 박스 입수 이상일 경우 배송비 0 -->
                                                    <td><?=$listCart[$i]['QTY']?></td>
                                                    <td>0원</td>
                                                    <td style="text-align:right;">
                                                        <?=number_format($totalSalePrice)?>원
                                                    </td>
                                                    <input type="hidden" name="cartNo[]" value="<?=$listCart[$i]["CART_NO"]?>">
                                                    

                                                </tr>  
                                            <?

                                                $allTotalPrice+=$totalSalePrice;
                                            }//end of for($cntCart2)
                                            ?>
                                                <tr>
                                                    <td colspan='6'>
                                                        총 금액
                                                    </td>
                                                    <td style="text-align:right;">
                                                        <?=number_format($allTotalPrice)?>원
                                                    </td>
                                                </tr>
                                            <?
                                        }//end of if(cntCart2>0)
                                        //for(){}
                                        //가져온 주문상품 정보를 여기에 뿌려준다
                                    ?>

                                </table>
                            </div><!--order_box-->
                            <br>
                            <div class="order_detail">
                                <b><font color="black">수령자 정보</font> &nbsp; &nbsp; &nbsp; &nbsp;<input type="checkbox" id="same" onclick="js_same_click()"> <label for="same" style="cursor:pointer">주문자 정보와 같게</label></b>
                                <div class="address">
                                    <input type="text" placeholder="이름" style="width:13%;" name="rName" value="" maxlength="10"><br>
                                    <input type="text" placeholder="이메일주소" style="width:13%;" name="rEmail1" value=""> 
                                    @ <input type="text" style="width:14%;" name="rEmail2"><br><br>
                                    핸드폰번호
                                    <br>
                                    <input type="number" style="width:8%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;"   name="rHPhone1" max="9999" maxlength="3" oninput="maxLengthCheck(this)">
                                    - <input type="number" style="width:9%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="rHPhone2" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                                    - <input type="number" style="width:9%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="rHPhone3" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                                    <br>

                                    <input type="text" placeholder="우편번호" style="width:13%;" name="rZipCode"><button type="button" onclick="js_addr_open1('s')">검 색</button>
                                    <input type="hidden" name="addr_type" id="addr_type" value="">
                                    <br>
                                    <input type="text" placeholder="주문자주소" name="rAddr1"><br>
                                    <input type="text" placeholder="세부주소" name="rAddr2"></br>
                                    <input type="text" placeholder="요청사항을 직접 입력합니다" name="rMemo">
                                    <br>
                                    <br>
                                    <font color="gray">고객센터 [평일 10시 ~ 17시]&nbsp;&nbsp;&nbsp;주문 후 전화 요청 고객은 체크해주세요!</font>&nbsp;&nbsp;&nbsp;
                                    <input type="checkbox" id="callyn" name="callyn" onclick="js_call_yn()" value = "N"> <label for="callyn" style="cursor:pointer">고객센터 전화요청</label></b>
                                </div><!--address-->
                                <!-- <b>마일리지</b>
                                <br>
                                <div class="order_box">
                                    <div class="order_td_01">보유마일리지</div>
                                    <div class="order_td_02">7777원</div>
                                    <div class="order_td_01">사용</div>
                                    <div class="order_td_02"><input type="number" value="0">원 <button>전액사용</button></div>
                                </div>order_box -->
                            </div><!--order_detail-->

                            <div class="order_detail_info">
                                <b><font color="black">주문자정보</font></b>
                                <div class="address">
                                    <input type="text" placeholder="이름" style="width:30%;" name="oName" value="<?=$memberName?>"><br>
                                    <input type="text" placeholder="이메일주소" style="width:30%" name="oEmail1" value="<?=$emails[0]?>"> 
                                    @ <input type="text" style="width:30%" name="oEmail2" value="<?=$emails[1]?>"><br><br>
                                    전화번호<br>
                                    <input type="number" style="width:20%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;"   name="oPhone1" max="9999" maxlength="3" oninput="maxLengthCheck(this)" value="<?=$phones[0]?>"> 
                                    - <input type="number" style="width:20%; margin-top:0px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="oPhone2" max="9999" maxlength="4" oninput="maxLengthCheck(this)" value="<?=$phones[1]?>">
                                    - <input type="number" style="width:20%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="oPhone3" max="9999" maxlength="4" oninput="maxLengthCheck(this)" value="<?=$phones[2]?>">

                                    <br><br>
                                    핸드폰번호<br>
                                    <input type="number" style="width:20%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;"   name="oHPhone1" max="9999" maxlength="3" oninput="maxLengthCheck(this)" value="<?=$hPhones[0]?>">
                                    - <input type="number" style="width:20%; margin-top:0px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="oHPhone2" max="9999" maxlength="4" oninput="maxLengthCheck(this)" value="<?=$hPhones[1]?>">
                                    - <input type="number" style="width:20%; margin-top:7px; border: 1px solid #d4d4d4; height: 28px; text-align: center;" name="oHPhone3" max="9999" maxlength="4" oninput="maxLengthCheck(this)" value="<?=$hPhones[2]?>">

                                    <br><br>
                                    주소<br>
                                    <input type="text" style="width:20%; text-align: center;" name="Ozipcode"  maxlength="5" value="<?=$Ozipcode?>">
                                    <input type="text" style="width:78%; text-align: left;" name="Oaddr1" maxlength="50" value="<?=$Oaddr1?>">
                                    <input type="text" style="width:99%; text-align: left;" name="Oaddr2" maxlength="50" value="<?=$Oaddr2?>">

                                </div><!--address-->
                            </div><!--order_detail_info-->
                            <div class="space30" style="width:100%; height:30px; clear:both;"></div>
                            
                            <div class="clear"></div>
                            <div class="tcenter">
                                <div class="" style="background-color:#FCEEF1; padding-top:5px; padding-bottom:5px;" >
                                주문하실 상품, 가격, 배송정보, 할인내여 등을 최종 확인하였으며, 구매 동의하시겠습니까? (전자상거래법 제8조 제 2항) &nbsp; &nbsp; 
                                <label><input type="checkbox" name="agreeYn">동의합니다.</label></div>
                                <br>
                                <button class="joomoon" type="button" style="margin-left:0px;" onclick="js_order_complete();">주문완료</button>
                            </div><!--tcenter-->
                            
                        </div><!--cart_info-->
                    </div><!--detail_page_inner-->
                </div><!--detail_page-->
            </form>
        </div><!--wrap-->
        <? require "./footer.php"; ?>
    </body>
</html>