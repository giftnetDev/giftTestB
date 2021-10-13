<? require "./_common/home_pre_setting.php";  ?>
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
    }


?>
<?//Page Process Region
    // print_r($_SESSION);

    // if(!$_SESSION["C_MEM_NO"]){
        
    // }

    $memberNo=$_SESSION["C_MEM_NO"];

    if(!$memberNo){
        // return ;
        echo "<script>alert('�α����� �Ǿ����� �ʽ��ϴ�');</script>";
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
        // echo "��ǰ��<br>";

        $goodsCode  = $goodsInfo[0]["GOODS_CODE"];
        $goodsName  = $goodsInfo[0]["GOODS_NAME"];
        $goodsSubName=$goodsInfo[0]["GOODS_SUB_NAME"];        
        $salePrice=     $goodsInfo[0]["SALE_PRICE"];
        $deliveryPrice= $goodsInfo[0]["DELIVERY_PRICE"];

        $goodsQty=      $_POST['qty'];
        $imgUrl =       $_POST['filePath'];
        $stickerNo=     $_POST['selectSticker'];
        $optPrintMsg=   $_POST['opt_print_msg'];

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
            echo "<script>alert('���õ� ��ǰ�� �����ϴ�');</script>";
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
        // echo "���ΰ�<br>";
        // $listCart=listCartByMemNo1($conn, $cp_no, $memberNo, "Y", "N", $orderState);
    }


    $cntCart2=sizeof($listCart);
    if($cntCart2<1){
        echo "<script>alert('�α����� �Ǿ����� �ʰų� �ֹ��� ��ǰ�� �����ϴ�');</script>";
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

                }
            }//end of function


            function sample6_execDaumPostcode() {


                new daum.Postcode({
                    oncomplete: function(data) {

                        // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.
                        // �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
                        // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
                        var fullAddr = ''; // ���� �ּ� ����
                        var extraAddr = ''; // ������ �ּ� ����

                        // ����ڰ� ������ �ּ� Ÿ�Կ� ���� �ش� �ּ� ���� �����´�.
                        if (data.userSelectedType === 'R') { // ����ڰ� ���θ� �ּҸ� �������� ���
                            fullAddr = data.roadAddress;

                        } else { // ����ڰ� ���� �ּҸ� �������� ���(J)
                            fullAddr = data.jibunAddress;
                        }
                        // alert('');

                        // ����ڰ� ������ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
                        if(data.userSelectedType === 'R'){
                            //���������� ���� ��� �߰��Ѵ�.
                            if(data.bname !== ''){
                                extraAddr += data.bname;
                            }
                            // �ǹ����� ���� ��� �߰��Ѵ�.
                            if(data.buildingName !== ''){
                                extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                            }
                            // �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
                            fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');

                            // alert(fullAddr);
                        }
                                        
                        if (document.getElementById("addr_type").value == "s") {

                            // alert('s');
                        // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
                            $("input[name='rZipCode").val(data.zonecode);
                            // document.getElementById("rZipCode").value = data.zonecode;
                            // alert('z');
                            //document.getElementById("cp_zip").value = data.postcode2;
                            $("input[name='rAddr1']").val(fullAddr);
                            // document.getElementById("rAddr1").value = fullAddr;
                            // alert('a');
                            // Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
                            $("input[name='rAddr2']").focus();
                            // document.getElementById("cp_addr").focus();
                        } 
                        // else {
                        // // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
                        //     document.getElementById("re_zip").value = data.zonecode;
                        //     //document.getElementById("re_zip").value = data.postcode2;
                        //     document.getElementById("re_addr").value = fullAddr;
                        //     // Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
                        //     document.getElementById("re_addr").focus();
                        // }


                    }
                }).open();
            }// end of function

            
            function js_addr_open1(s) {
                
                document.getElementById("addr_type").value = s;
                sample6_execDaumPostcode();

            }//end of function();

            function checkEssentialInfo(){
                if($("input[name='rName']").val()==''){
                    alert('������ �̸� �Է� ���');
                    return false;
                }
            //----------------------�̸���---------------------------
                if($("input[name='rEmail1']").val()==''){
                    alert('����� �̸��� �Է� ���');
                    return false;
                }

                if($("input[name='rEmail2']").val()==''){
                    alert('����� �̸��� �Է� ���');
                    return false;
                }

            //--------------------�ڵ���-----------------------------
                if($("input[name='rHPhone1']").val()==''){
                    alert('����� �޴���ȭ ��ȣ �Է� ���');
                    return false;
                }

                if($("input[name='rHPhone2']").val()==''){
                    alert('����� �޴���ȭ ��ȣ �Է� ���');
                    return false;
                }
                if($("input[name='rHPhone3']").val()==''){
                    alert('����� �޴���ȭ ��ȣ �Է� ���');
                    return false;
                }
            //--------------------�����ȣ---------------------------
                if($("input[name='rZipCode']").val()==''){
                    alert('������ �����ȣ �Է� ���');
                    return false;
                }

            //--------------------�ּ�-------------------------------
                if($("input[name='rAddr1']").val()==''){
                    alert('������ �ֹ��� �ּ� �Է� ���');
                    return false;
                }

                if($("input[name='rAddr2']").val()==''){
                    alert('������ �ֹ��� �����ּ� �Է� ���');
                    return false;
                }
                return true;
            }
            function js_order_complete(){

                if(!checkEssentialInfo()){
                    return;
                }


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
                    document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("cashbox").style.display ="block";
                    document.getElementById("cardbox").style.display ="none";   
                }
                else if(val == "1")
                {
                    alert("�ſ�ī�������ǥ�� ���ݰ�꼭�� ����� �� �ֵ���\n\n�ΰ������� ����Ǿ� ������ ���ݰ�꼭�� �߼۵��� �ʽ��ϴ�.\n\n�ſ�ī�� ���� �� �������� �μ��Ͽ� ���ݰ�꼭�� ����ϼ���.\n\nȮ�� �� �����ϱ� ��ư�� �� ���� �����ּ���.");

                    document.getElementById("payment_tax_receipt").style.display ="none";
                    
                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    document.getElementById("payment_method_inner3").style.display ="block";

                    document.getElementById("cashbox").style.display ="none";
                    document.getElementById("cardbox").style.display ="block";                    
                }
                else
                {
                    document.getElementById("payment_tax_receipt").style.display ="none";

                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("cashbox").style.display ="none";
                    document.getElementById("cardbox").style.display ="block";
                }
            }

            function js_sel_receipt(val)
            {
                if(val == "T")
                {
                    document.getElementById("payment_method_inner1").style.display ="block";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    document.getElementById("payment_method_inner3").style.display ="none";
                }
                else if(val == "C")
                {
                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="block";
                    document.getElementById("payment_method_inner3").style.display ="none";

                    document.getElementById("individual").style.display ="block";
                    document.getElementById("buisnessman").style.display ="none";

                    $('input[name="rd_pay_receipt_dt"]').removeAttr('checked');
                    $('input[name="rd_pay_receipt_dt"]').filter("[value=I]").prop("checked", true);
                }
                else
                {
                    alert("���ݰ�꼭�� �ʿ��Ͻ� ���� �Ϳ� 10�� ������\n\n����, �ݾ�, �̸����ּҸ� ����ڵ����\n\n�纻�� �����Ͽ�, �Ʒ� �ѽ���ȣ�� �����ֽø�\n\n�̸��Ϸ� �߼� �� ���� �帮�ڽ��ϴ�.\n\nFAX : 031-527-6858");

                    document.getElementById("payment_method_inner1").style.display ="none";
                    document.getElementById("payment_method_inner2").style.display ="none";
                    document.getElementById("payment_method_inner3").style.display ="none";
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

        </script>
    </head>

    <body>

    <script>
	$(document).ready(function(){

        var frm = document.mainFrm;

        frm.rd_pay_method.value = "0";      //�������Ա�

        document.getElementById("payment_method_inner1").style.display ="none";
        document.getElementById("payment_method_inner2").style.display ="none";
        document.getElementById("payment_method_inner3").style.display ="none";

        document.getElementById("cashbox").style.display ="block";
        document.getElementById("cardbox").style.display ="none";

	});
    </script>

        <div class="wrap">
            
            <? require "./top.php"; ?>
            <form name='mainFrm'>
                <div class="detail_page">
                    <div class="detail_page_inner">
                        <div class="cart_info">
                            <h4>�ֹ�/����</h4>

                            <div class="order_box">
                                <table>
                                    <tr>
                                        <th colspan="2">��ǰ����</th>
                                        <th>��ǰ����</th>
                                        <th>��ۺ�</th>
                                        <th>����</th>
                                        <th>����</th>
                                        <th>��ǰ�ݾ� (��������)</th>
                                    </tr>
                                    <?
                                        if($cntCart2>0){
                                            $allTotalPrice=0;
                                            for($i = 0; $i < $cntCart2; $i++){
                                                $imgUrl=$listCart[$i]["FILE_PATH_150"].$listCart[$i]["FILE_RNM_150"];
                                                $totalSalePrice=$listCart[$i]["SALE_PRICE"]*$listCart[$i]["QTY"]+$listCart[$i]["DELIVERY_PRICE"];
                                                // echo "<br>";
                                                // echo "imgUrl : ".$imgUrl."<br>";
                                            ?>
                                                <tr>
                                                    <td> <div class="thumb_img" style="background:url('<?=$imgUrl?>') no-repeat; background-size:cover; background-position:center center;"></div></td>
                                                    <td style="width:350px !important"><?=$listCart[$i]['GOODS_NAME']?><br>
                                                    <?
                                                        if($listCart[$i]["OPT_STICKER_NO"]>0 || $listCart[$i]["OPT_PRINT_MSG"]){
                                                            ?>
                                                                <i>- �ɼ� -</i></br>
                                                            <?
                                                            $optStickerNo=$listCart[$i]["OPT_STICKER_NO"];
                                                            $optPrintMsg=SetStringFromDB($listCart[$i]["OPT_PRINT_MSG"]);
                                                            if($optStickerNo>0){
                                                                $optSticker=SetStringFromDB(getStickerNameByNo($conn, $optStickerNo));
                                                                ?>
                                                                    <i>��ƼĿ �ɼ� : <?=$optSticker?></i><br>
                                                                <?
                                                            }
                                                            if($optPrintMsg<>""){
                                                            ?>
                                                                <i>�μ� �ɼ� : <?=$optPrintMsg?></i>
                                                            <?
                                                            }
                                                        }
                                                    ?>
                                                        <!-- <i>��ƼĿ : ���� AŸ�� ��<br>�μ�޼��� : ���ڰ� ������ �ּ���</i> -->

                                                    </td><!--��ǰ����-->
                                                    <td><?=number_format($listCart[$i]["SALE_PRICE"])?>��</td>
                                                    <td><?=number_format($listCart[$i]["DELIVERY_PRICE"])?>��</td>
                                                    <td><?=$listCart[$i]['QTY']?></td>
                                                    <td>0��</td>
                                                    <td style="text-align:right;">
                                                        <?=number_format($totalSalePrice)?>��
                                                    </td>
                                                    <input type="hidden" name="cartNo[]" value="<?=$listCart[$i]["CART_NO"]?>">
                                                    

                                                </tr>  
                                            <?

                                                $allTotalPrice+=$totalSalePrice;
                                            }//end of for($cntCart2)
                                            ?>
                                                <tr>
                                                    <td colspan='6'>
                                                        �� �ݾ�
                                                    </td>
                                                    <td style="text-align:right;">
                                                        <?=number_format($allTotalPrice)?>��
                                                    </td>
                                                </tr>
                                            <?
                                        }//end of if(cntCart2>0)
                                        //for(){}
                                        //������ �ֹ���ǰ ������ ���⿡ �ѷ��ش�
                                    ?>

                                </table>
                            </div><!--order_box-->
                            <br>
                            <div class="order_detail">
                                <b>������ ���� &nbsp; &nbsp; &nbsp; &nbsp;<input type="checkbox" id="same" onclick="js_same_click()"> <label for="same" style="cursor:pointer">�ֹ��� ������ ����</label></b>
                                <div class="address">
                                    <input type="text" placeholder="�̸�" style="width:15%;" name="rName" value=""><br>
                                    <input type="text" placeholder="�̸����ּ�" style="width:15%;" name="rEmail1" value=""> @ <input type="text" style="width:10%;" name="rEmail2"><br><br>
                                    �ڵ�����ȣ
                                    <br>
                                    <input type="text" style="width:10%;margin-top:7px;"       name="rHPhone1">
                                    - <input type="text" style="width:10%; margin-top:7px;"    name="rHPhone2">
                                    - <input type="text" style="width:10%; margin-top:7px;"    name="rHPhone3">
                                    <br>

                                    <input type="text" placeholder="�����ȣ" style="width:20%;" name="rZipCode"><button type="button" onclick="js_addr_open1('s')">�� ��</button>
                                    <input type="hidden" name="addr_type" id="addr_type" value="">
                                    <br>
                                    <input type="text" placeholder="�ֹ����ּ�" name="rAddr1"><br>
                                    <input type="text" placeholder="�����ּ�" name="rAddr2"></br>
                                    <input type="text" placeholder="��û������ ���� �Է��մϴ�" name="rMemo">
                                    <select>
                                        <option>��û���� ����</option>
                                        <option>��û���� ����</option>
                                        <option>��û���� ����</option>
                                        <option>��û���� ����</option>
                                        <option>��û���� ����</option>
                                    </select>
                                </div><!--address-->
                                <!-- <b>���ϸ���</b>
                                <br>
                                <div class="order_box">
                                    <div class="order_td_01">�������ϸ���</div>
                                    <div class="order_td_02">7777��</div>
                                    <div class="order_td_01">���</div>
                                    <div class="order_td_02"><input type="number" value="0">�� <button>���׻��</button></div>
                                </div>order_box -->
                            </div><!--order_detail-->

                            <div class="order_detail_info">
                                <b>�ֹ�������</b>
                                <div class="address">
                                    <input type="text" placeholder="�̸�" style="width:40%;" name="oName" value="<?=$memberName?>"><br>
                                    <input type="text" placeholder="�̸����ּ�" style="width:40%" name="oEmail1" value="<?=$emails[0]?>"> @ <input type="text" style="width:40%" name="oEmail2" value="<?=$emails[1]?>"><br><br>
                                    ��ȭ��ȣ<br>
                                    <input type="text" style="width:20%; margin-top:7px;"   name="oPhone1" value="<?=$phones[0]?>"> 
                                    - <input type="text" style="width:20%; margin-top:0px;" name="oPhone2" value="<?=$phones[1]?>">
                                    - <input type="text" style="width:20%; margin-top:7px;" name="oPhone3" value="<?=$phones[2]?>">

                                    <br><br>
                                    �ڵ�����ȣ<br>
                                    <input type="text" style="width:20%; margin-top:7px;"   name="oHPhone1" value="<?=$hPhones[0]?>">
                                    - <input type="text" style="width:20%; margin-top:0px;" name="oHPhone2" value="<?=$hPhones[1]?>">
                                    - <input type="text" style="width:20%; margin-top:7px;" name="oHPhone3" value="<?=$hPhones[2]?>">

                                </div><!--address-->
                            </div><!--order_detail_info-->
                            <div class="space30" style="width:100%; height:30px; clear:both;"></div>
                            <div class="payment_inform" style="width:100%; clear:both; color:black;">
                                <b>���� ����<b>
                                <hr style="height:1px; border-bottom:0px; "noshade='noshade'>
                                <div class="payment_method" style="padding-left:20px; float:left; width:48%;">
                                    <span>������� ����</span>
                                    <br>
                                    &nbsp;
                                    <label><input type="radio" name="rd_pay_method" value="0" onclick="js_sel_pament('0')">�������Ա� </label>
                                    <label><input type="radio" name="rd_pay_method" value="1" onclick="js_sel_pament('1')">ī����� </label>
                                    <label><input type="radio" name="rd_pay_method" value="2" onclick="js_sel_pament('2')">�ڵ���</label>
                                    <div id="payment_tax_receipt" style="margin-top:10px">
                                        <span>���ݰ�꼭 �� ���ݿ����� ��û</sapn>
                                        <br>
                                        &nbsp;
                                        <label><input type="radio" name="rd_pay_receipt" value="T" onclick="js_sel_receipt('T')">���ݰ�꼭��û </label>&nbsp;
                                        <label><input type="radio" name="rd_pay_receipt" value="C" onclick="js_sel_receipt('C')">���ݿ�������û </label>&nbsp;
                                        <label><input type="radio" name="rd_pay_receipt" value="X" onclick="js_sel_receipt('X')">��û����</label>
                                    </div>
                                    <div id="payment_method_inner1" style="padding:10px;">
                                        <table>
                                            <colgroup>
                                                <col width="25%">
                                                <col width="75%">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th style="font-size:14px !important; ">����ڹ�ȣ</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>    
                                                </tr>
                                                <tr>
                                                    <th style="font-size:14px !important;" >��ȣ(���θ�)</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="font-size:14px !important;" >��ǥ��</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="font-size:14px !important;" >����&����</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 43%; height: 25px; border: 1px solid #d4d4d4;">
                                                        &nbsp;&&nbsp;
                                                        <input type="text" style="width: 45%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="font-size:14px !important;" >������ּ�</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="font-size:14px !important;" >�̸���</th>
                                                    <td style="text-align: left !important;">
                                                        <input type="text" style="width: 43%; height: 25px; border: 1px solid #d4d4d4;">
                                                        &nbsp;@&nbsp;
                                                        <input type="text" style="width: 44%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!--payment_method_inner1-->

                                    <div id="payment_method_inner2" style="border:1px solid #d4d4d4; margin-top:5px; width:95%; padding:10px;">
                                        <label><input type="radio" name="rd_pay_receipt_dt" value="I" onclick="js_sel_IB('I')">���� �ҵ������(��ȭ��ȣ)</label>&nbsp;
                                        <label><input type="radio" name="rd_pay_receipt_dt" value="B" onclick="js_sel_IB('B')">����� ��������� </label><br>
                                        <div id="individual">
                                            <table>
                                                <colgroup>
                                                    <col width="25%">
                                                    <col width="75%">
                                                </colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th style="font-size:14px !important; ">����</th>
                                                        <td><input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </tr>
                                                    <tr>
                                                        <th style="font-size:14px !important; ">��ȭ��ȣ</th>
                                                        <td style=" text-align: left;">
                                                            <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th style="font-size:14px !important;" >�̸���</th>
                                                        <td style="text-align: left !important;">
                                                            <input type="text" style="width: 43%; height: 25px; border: 1px solid #d4d4d4;">
                                                            &nbsp;@&nbsp;
                                                            <input type="text" style="width: 44%; height: 25px; border: 1px solid #d4d4d4;">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>    
                                        <div id="buisnessman">
                                            <table>
                                                <colgroup>
                                                    <col width="25%">
                                                    <col width="75%">
                                                </colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th style="font-size:14px !important; ">����ڹ�ȣ</th>
                                                        <td><input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    </tr>
                                                    <tr>
                                                        <th style="font-size:14px !important; ">��ȭ��ȣ</th>
                                                        <td style=" text-align: left;">
                                                            <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th style="font-size:14px !important;" >�̸���</th>
                                                        <td style="text-align: left !important;">
                                                            <input type="text" style="width: 43%; height: 25px; border: 1px solid #d4d4d4;">
                                                            &nbsp;@&nbsp;
                                                            <input type="text" style="width: 44%; height: 25px; border: 1px solid #d4d4d4;">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>    
                                    </div>
                                    <div id="payment_method_inner3" style="border:1px solid #d4d4d4; margin-top:20px; width:95%; padding:10px;">
                                        ������������ �ȳ� - 30���� �̻� ��� �ſ�ī�� ������ ���������� �ʿ�<br>
                                                            (�� 3���� ���� �����ô� ������������ ��� ���� ����)
                                                            
                                    </div>

                                </div>
                                <div id="cashbox" style="float:left; width:45%; padding:10px;" >
                                    <table>
                                        <colgroup>
                                            <col width="25%">
                                            <col width="75%">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th style="font-size:14px !important; ">�Ա��ڸ�</th>
                                                <td><input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                            </tr>
                                            <tr>
                                                <th style="font-size:14px !important; ">�Ա��Ͻ�����</th>
                                                <td style=" text-align: left;">
                                                    <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;"><br>
                                                    <font color="gray" style="font-size: 12px;">��ǰ��� �Աݰ��´� ������ ����Ʈ��(��)�����̸� ���ݰ�꼭�� ����Ʈ��(��)���� ����˴ϴ�.</font>

                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="font-size:14px !important; ">�߰����</th>
                                                <td style=" text-align: left;">
                                                    <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    <font color="gray" style="font-size: 12px;">(�ΰ�������)<br>���θ��� ���ǵ� �߰��ݾ��� �޸�(,)���� ���ڸ� �Է����ּ���.</font>
                                                </td>                                           
                                            </tr>
                                            <tr>
                                                <th style="font-size:14px !important; ">�Ѱ����ݾ�</th>
                                                <td>
                                                    <input type="text" style="width: 93%; height: 25px; border: 1px solid #d4d4d4;">
                                                    <font color="gray" style="font-size: 12px;">��</font>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="cardbox" style="float:left; width:45%; padding:20px;" >
                                    <table>
                                        <colgroup>
                                            <col width="25%">
                                            <col width="75%">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th style="font-size:14px !important; ">�߰����</th>
                                                <td style=" text-align: left;">
                                                    <input type="text" style="width: 98%; height: 25px; border: 1px solid #d4d4d4;">
                                                    <font color="gray" style="font-size: 12px;">(�ΰ�������)<br>���θ��� ���ǵ� �߰��ݾ��� �޸�(,)���� ���ڸ� �Է����ּ���.</font>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="font-size:14px !important; ">�Ѱ����ݾ�</th>
                                                <td>
                                                    <input type="text" style="width: 93%; height: 25px; border: 1px solid #d4d4d4;">
                                                    <font color="gray" style="font-size: 12px;">��</font>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="tcenter">
                                <div class="" style="background-color:#FCEEF1; padding-top:5px; padding-bottom:5px;" >�ֹ��Ͻ� ��ǰ, ����, �������, ���γ��� ���� ���� Ȯ���Ͽ�����, ���� �����Ͻðڽ��ϱ�? (���ڻ�ŷ��� ��8�� �� 2��) &nbsp; &nbsp; <label><input type="checkbox">�����մϴ�.</label></div>
                                <br>
                                <button class="joomoon" type="button" style="margin-left:0px;" onclick="js_order_complete();">�����ϱ�</button>
                            </div><!--tcenter-->
                            
                        </div><!--cart_info-->
                    </div><!--detail_page_inner-->
                </div><!--detail_page-->
            </form>
        </div><!--wrap-->
        <? require "./footer.php"; ?>
    </body>
</html>