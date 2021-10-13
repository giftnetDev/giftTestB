<?require "../_common/home_pre_setting.php" ?>
<?//FUNCTIONS ZONE
    function getMemberInfo($db, $memberNo){
        $query="SELECT  MEM_NM, EMAIL, PHONE, HPHONE, ZIPCODE, ADDR1, ADDR2
                FROM    TBL_MEMBER
                WHERE   MEM_NO='$memberNo'

                ";
        $result=mysql_query($query, $db);
        if($result<>""){
            $rows=mysql_fetch_row($result);
        }

        return $rows;
    
    }//end of function
    function verificateCartInform($db, $cartNo, $goodsNo, $qty){
        $query="UPDATE TBL_CART
                SET     QTY =   '$qty'
                WHERE   CART_NO= '$cartNo'
                AND     GOODS_NO='$goodsNo'
                AND     DELIVERY_CNT_IN_BOX <='$qty'
                ";
        if(mysql_query($query, $db)){
            return 1;
        }
        else{
            return 0;
        }
        
    }
    function listCartByMemNoAndCartNos($db, $cartNos, $mem_no, $use_tf, $del_tf, $order_str) {

        $query = "SELECT    C.CART_NO, C.ON_UID, C.CP_ORDER_NO, C.CP_NO, C.BUY_CP_NO, C.MEM_NO, C.CART_SEQ, C.GOODS_NO, 
                            G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, C.QTY, C.OPT_STICKER_NO, C.OPT_OUTBOX_TF, 
                            C.DELIVERY_CNT_IN_BOX, C.OPT_WRAP_NO, C.OPT_PRINT_MSG, C.OPT_OUTSTOCK_DATE, C.OPT_MEMO, 
                            C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO, C.DELIVERY_TYPE, C.DELIVERY_CP, C.SENDER_NM, 
                            C.SENDER_PHONE, C.CATE_01 AS C_CATE_01, C.CATE_02 AS C_CATE_02, C.CATE_03 AS C_CATE_03, 
                            C.CATE_04 AS C_CATE_04, G.CATE_01, G.CATE_02, G.CATE_03, G.CATE_04, C.PRICE, C.BUY_PRICE, 
                            C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.DISCOUNT_PRICE, C.USE_TF, 
                            C.DEL_TF, C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, G.SALE_PRICE AS CUR_SALE_PRICE,
                            C.STICKER_PRICE, C.PRINT_PRICE, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE, G.IMG_URL, 
                            G.FILE_NM_100, G.FILE_RNM_100, G.FILE_PATH_100, G.FILE_SIZE_100, G.FILE_EXT_100, G.FILE_NM_150, 
                            G.FILE_RNM_150, G.FILE_PATH_150, G.FILE_SIZE_150, G.FILE_EXT_150, C.OPT_STICKER_MSG
                    FROM    TBL_CART C, TBL_GOODS G 
                    WHERE   C.GOODS_NO = G.GOODS_NO
                    AND     CART_NO IN($cartNos)
                    ";
        // if ($cp_no <> "") {
        //     $query .= " AND C.CP_NO = '".$cp_no."' ";
        // }
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

    function selectCurrentGoodsM($db, $goodsNo){
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

    function insertCartWithMemNoM($db, $on_uid, $cp_order_no, $cp_no, $buy_cp_no, $mem_no, $cart_seq, $goods_no, $goods_code, $goods_name, $goods_sub_name, $qty, $opt_sticker_no, $opt_sticker_msg, $opt_outbox_tf, $delivery_cnt_in_box, $opt_wrap_no, $opt_print_msg, $opt_outstock_date, $opt_memo, $memos, $delivery_type, $delivery_cp, $sender_nm, $sender_phone, $cate_01, $cate_02, $cate_03, $cate_04,  $price, $buy_price, $sale_price, $extra_price, $delivery_price, $sa_delivery_price, $discount_price, $sticker_price, $print_price, $sale_susu, $labor_price, $other_price, $use_tf, $reg_adm)
	{
		$opt_request_memo = $memos["opt_request_memo"];
		$opt_support_memo = $memos["opt_support_memo"];


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
		

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]������ �߻��Ͽ����ϴ� - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return true;
		}

	}//end of function


    function getLatestCartNoWithMemberNoM($db, $memberNo){
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

    function listCartByCartNoM($db, $cartNos, $memberNo){
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


?>
<?//PAGE_PROCESS ZONE



    if($_SESSION['C_MEM_NO'] == ""){
    ?>
        <script type="text/javascript">
            alert("�α��� �Ǿ����� �ʰų� ������ ���� �Ǿ����ϴ�. �� �α��� ���ּ���.");
        </script>
        <meta http-equiv="Refresh" content='0; URL=/'>
        
    <?
        exit;
    }
    // print_r($_POST);
    // exit;


    $memberNo=$_SESSION["C_MEM_NO"];

    $arrCart=array();
    $cntOrder=0;
    $cntCart=intval($_POST['hd_goods_cnt']);
    $cartNos="";


    for($i=0; $i<$cntCart; $i++){
        if($_POST['hd_inbox_checked_'.$i]=="Y"){//���ι鿡�� chk�� �ֵ鸸 �Ÿ��� �б⹮
            // echo "hd_inbox_checked_".$i." : ".$_POST['hd_inbox_checked_'.$i]."<br>";
            $CART_NO    =   $_POST['hd_cart_no_'.$i];

        //������ �ڽ��Լ����� ������ �������� ����������� 
        $cartNos.="'".$CART_NO."', ";
            
        }

    }//end of for(cntCart);

    $cartNos=rtrim($cartNos, ", ");


    if($mode == "ORDER_CURRENT_ITEM"){
        /**
         * 1. �޾ƿ� GOODS_NO(�� ��忡���� ��ǰ�� 1���� �����´�.)�� �ش� ��ǰ�� ������ DB���� �޾ƿ´�.
         * 2. �޾ƿ� ��ǰ���� �� ��������� �����Ͽ� TBL_CART�� �����Ѵ�
         * 3. TBL_CART���� ���� ���߿� ��ϵ� ROW�� �ҷ��ͼ� �ֹ��Ϸ��� ��Ͽ� �ѷ��ش�.
         * 
        */

        unset($cartNos);
    
            $goodsNo=       $_POST['goods_no'];
            $goodsInfo=selectCurrentGoodsM($conn, $goodsNo);
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
    
            $optPrintMsg   =str_replace("\r\n",'<br>', $optPrintMsg);
    
            // echo "goodsQty : ".$_POST['qty'];s
    
    
            insertCartWithMemNoM( $conn, $on_uid_X, $cp_order_no_X, $_SESSION['C_CP_NO'], $buy_cp_no_X, $memberNo, $cart_seq_X, $goodsNo, $goodsCode,$goodsName,$goodsSubName,$goodsQty, $stickerNo,
                        $opt_sticker_msg_X, $opt_outbox_tf_X, $delivery_cnt_in_box_X, $opt_wrap_no_X, $optPrintMsg, $opt_outstock_date_X, $opt_memo_X,
                        $memos_X, $delivery_type_X, $delivery_cp_X, $sender_nm_X, $sender_phone_X, $cate_01_X, $cate_02_X, $cate_03_X, $cate_04_X, $price_X, $buy_price_X, $salePrice,
                        $extra_price_X, $deliveryPrice, $sa_delivery_price_X, $discount_price_X, $sticker_price_x, $print_price_X, $sale_susu_X, $labor_price_X, $other_price_X, 
                        "N", $reg_adm_X);

            $cartNos = mysql_insert_id();
            
        }//end of mode


    $arrCart=listCartByMemNoAndCartNos($conn, $cartNos, $memberNo, "", "N", "");
    $cntArr=sizeof($arrCart);

    $memberInfo=getMemberInfo($conn, $memberNo);

    // MEM_NM, EMAIL, PHONE, HPHONE, ZIPCODE, ADDR1, ADDR2
    $M_MEM_NM =       $memberInfo[0];
    $M_EMAIL  =       $memberInfo[1];
    $M_PHONE  =       $memberInfo[2];
    $M_HPHONE =       $memberInfo[3];
    $M_ZIPCODE=       $memberInfo[4];
    $M_ADDR1  =       $memberInfo[5];
    $M_ADDR2  =       $memberInfo[6];

    $EMAIL_ELEMENT=  explode("@", $M_EMAIL);
    $EMAIL_PRE  =   $EMAIL_ELEMENT[0];
    $EMAIL_POST =   $EMAIL_ELEMENT[1];
    
    $PHONE_ELEMENT  =explode("-", $M_PHONE);
    $O_PHONE_1        =$PHONE_ELEMENT[0];
    $O_PHONE_2        =$PHONE_ELEMENT[1];
    $O_PHONE_3        =$PHONE_ELEMENT[2];

    $HPHONE_ELEMENT=explode("-", $M_HPHONE);
    $O_HPHONE_1       =$HPHONE_ELEMENT[0];
    $O_HPHONE_2       =$HPHONE_ELEMENT[1];
    $O_HPHONE_3       =$HPHONE_ELEMENT[2];

    $O_ZIPCODE      =   $M_ZIPCODE;


    // print_r($memberInfo);
    // exit;

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./Mheader.php"; ?>
        <script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
        <script>//JS_FUNCTION ZONE
            $(function(){
                $(".trigger-find_addr").click(sample6_execDaumPostcode);
            });

        </script>
        <script type="text/javascript">
            function js_same_order_receiver(){
                if($("#chkSame").is(":checked")== true){
       
                    // return;
                    if($("#rName").val()==""){
                        $("#rName").val($("#oName").val());   
                    }
                    if($("#rEmail1").val()=="" && $("#rEmail2").val()==""){
                        $("#rEmail1").val($("#oEmail1").val());
                        $("#rEmail2").val($("#oEmail2").val());
                    }
                    if($("#rPhone1").val()=="" && $("#rPhone2").val()=="" && $("#rPhone3").val()==""){
                        $("#rPhone1").val($("#oPhone1").val());
                        $("#rPhone2").val($("#oPhone2").val());
                        $("#rPhone3").val($("#oPhone3").val());
                    }
                    if($("#rHPhone1").val()=="" && $("#rHPhone2").val()=="" && $("#rHPhone3").val()==""){
                        $("#rHPhone1").val($("#oHPhone1").val());
                        $("#rHPhone2").val($("#oHPhone2").val());
                        $("#rHPhone3").val($("#oHPhone3").val());
                    }
                    if($("#rAddr1").val()=="" && $("#rAddr2").val()==""){
                        $("#rAddr1").val($("#Oaddr1").val());
                        $("#rAddr2").val($("#Oaddr2").val());
                    }
                    if($("#rZipCode").val()==""){
                        $("#rZipCode").val($("#o_zipcode").val());
                    }
                }//end of if
                else{
                    alert('no checked');
                    return;
                    if($("#rName").val()==$("#oName").val()){
                        $("#rName").val();
                    }

                }
            }//end of js_function
            
            function maxLengthCheck(object)
            {
                if (object.value.length > object.maxLength)
                {
                    object.value = object.value.slice(0, object.maxLength);
                }   
            }

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
                        }
                                        
                        // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
                        document.getElementById("rZipCode").value = data.zonecode;
                        //document.getElementById("re_zip").value = data.postcode2;
                        document.getElementById("rAddr1").value = fullAddr;
                        // Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
                        document.getElementById("rAddr1").focus();

                    }
                }).open();
            }
            function js_order_complete(){
                if($("input:checkbox[name='agreeYn']").is(":checked") == false){
                    alert("���� ���Ǹ� �� �ּž� �մϴ�");
                    return;
                }

                if(js_validation()<1){
                    return ;
                }
                alert('�ֹ��Ϸ� �Ͻðڽ��ϱ�?');
                var frm=document.frm;
                frm.target="";
                frm.action="./order_action.php";
                frm.method="POST";
                frm.submit();
            }
            function js_validation(){

              
                { //�ֹ��� 

                    //�ֹ��� �̸��� 1, 2
                    if($("#oEmail1").val()==""){
                        alert("�ֹ��� �̸��� �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#oEmail1").focus()
                        return 0;
                    }
                    if($("#oEmail2").val()==""){
                        alert("�ֹ��� �̸��� �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#oEmail2").focus()
                        return 0;
                    }

                    //�ֹ��� �̸�
                    if($("#oName").val()==""){
                        alert("�ֹ��� ������ ���Ե��� �ʾҽ��ϴ�.");
                        $("#oName").focus()
                        return 0;
                    }

                    //�ֹ��� ��ȭ��ȣ 1,2,3
                    if($("#oPhone1").val()==""){
                        alert("�ֹ��� ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone1").focus()
                        return 0;
                    }
                    if($("#oPhone2").val()==""){
                        alert("�ֹ��� ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone2").focus()
                        return 0;
                    }
                    if($("#oPhone3").val()==""){
                        alert("�ֹ��� ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone3").focus()
                        return 0;
                    }

                    //�ֹ��� �޴��� 1,2,3
                    if($("#oPhone1").val()==""){
                        alert("�ֹ��� �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone1").focus()
                        return 0;
                    }
                    if($("#oPhone2").val()==""){
                        alert("�ֹ��� �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone2").focus()
                        return 0;
                    }
                    if($("#oPhone3").val()==""){
                        alert("�ֹ��� �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#oPhone3").focus()
                        return 0;
                    }

                    //�ֹ��� �����ȣ
                    if($("#o_zipcode").val()==""){
                        alert("�ֹ��� ���� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#o_zipcode").focus()
                        return 0;
                    }

                    //�ֹ��� �ּ� 1, 2
                    if($("#Oaddr1").val()==""){
                        alert("�ֹ��� �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#Oaddr1").focus()
                        return 0;
                    }
                    // if($("#Oaddr2").val()==""){
                    //     alert("�ֹ��� �̼��ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                    //     $("#Oaddr2").focus()
                    //     return 0;
                    // }
                }//end of �ֹ���

                { //������ 

                    //������ �̸�
                    if($("#rName").val()==""){
                        alert("������ ������ ���Ե��� �ʾҽ��ϴ�");
                        $("#oName").focus()
                        return 0;
                    }

                    //������ �̸��� 1, 2
                    if($("#rEmail1").val()==""){
                        alert("������ �̸��� �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#rEmail1").focus()
                        return 0;
                    }
                    if($("#rEmail2").val()==""){
                        alert("������ �̸��� �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#rEmail2").focus()
                        return 0;
                    }



                    //������ ��ȭ��ȣ 1,2,3
                    if($("#rPhone1").val()==""){
                        alert("������ ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rPhone1").focus()
                        return 0;
                    }
                    if($("#rPhone2").val()==""){
                        alert("������ ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rPhone2").focus()
                        return 0;
                    }
                    if($("#rPhone3").val()==""){
                        alert("������ ��ȭ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rPhone3").focus()
                        return 0;
                    }

                    //������ �޴��� 1,2,3
                    if($("#rHPhone1").val()==""){
                        alert("������ �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rHPhone1").focus()
                        return 0;
                    }
                    if($("#rHPhone2").val()==""){
                        alert("������ �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rHPhone2").focus()
                        return 0;
                    }
                    if($("#rHPhone3").val()==""){
                        alert("������ �޴��� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rHPhone3").focus()
                        return 0;
                    }

                    //������ �����ȣ
                    if($("#rZipCode").val()==""){
                        alert("������ ���� ��ȣ�� �Էµ��� �ʾҽ��ϴ�");
                        $("#rZipCode").focus()
                        return 0;
                    }

                    //������ �ּ� 1, 2
                    if($("#rAddr1").val()==""){
                        alert("������ �ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                        $("#rAddr1").focus()
                        return 0;
                    }
                    // if($("#rAddr2").val()==""){
                    //     alert("������ �̼��ּҰ� ���Ե��� �ʾҽ��ϴ�.");
                    //     $("#rAddr2").focus()
                    //     return 0;
                    // }
                }//end of ������

                return 1;


            }//end of function

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
            }//end of function

        </script>  
    </head>
    <body>
        <div class="background">
            <form name="frm" method="POST">
                <div class="title_line">
                    <span class="left_button"></span>
                    <span class="page_title">�ֹ��ϱ�</span>
                    <span class="right_button" onclick="js_order_complete()">�ֹ��Ϸ�></span>
                </div><!--end of title_line-->
                <div class="content">
                    <form name="frmContent">
                        <input type="hidden" name="contentMode">
                        <h3>�ֹ��ϱ�</h3>
                        <?// list �ҷ�����

                            for($i=0; $i<$cntArr; $i++){
                                $img_url=$arrCart[$i]["FILE_PATH_150"].$arrCart[$i]["FILE_RNM_150"];
                                $totalSalePrice=$arrCart[$i]["SALE_PRICE"]*$arrCart[$i]["QTY"]+$arrCart[$i]["DELIVERY_PRICE"];
                                $GOODS_NAME     =$arrCart[$i]["GOODS_NAME"];

                                $SALE_PRICE =$arrCart[$i]["SALE_PRICE"];
                                $QTY        =$arrCart[$i]["QTY"];
                                $CART_NO    =$arrCart[$i]["CART_NO"];
                            ?>
                                <div class="list_index">
                                    <div class="inbox_index_title">
                                        <?=$GOODS_NAME?>

                                    </div><!--inbox_index_title-->
                                    <table style="width:96%; margin:2%; text-align: center;">
                                        <colgroup>
                                            <col width="30%">
                                            <col width="25%">
                                            <col width="45%">
                                        </colgroup>
                                        <tr>
                                            <td rowspan="3">
                                                <div style="background:url('<?=$img_url?>') no-repeat;background-size:100% 100%; background-position:center center; width:90px;height:90px; margin-left:4%;"></div>
                                            </td>
                                            <td class="key right_align">����  </td>
                                            <td class="value right_align"><?=number_format($SALE_PRICE)?>��</td>
                                            <input type="hidden" id="hd_sale_price_<?=$i?>" value="<?=$SALE_PRICE?>">
                                        </tr>
                                        <tr>
                                            <td class="key right_align">����  </td>
                                            <td class="value right_align" >
                                            <?=number_format($QTY)?>��
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="key right_align black">�� ����  </td>
                                            <td class="value right_align black" id="td_total_price_<?=$i?>"><?=number_format($SALE_PRICE*$QTY)?>��</td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="cartNo[]" value="<?=$CART_NO?>">

                                </div><!--list_index-->
                            <?
                            }//end of for
                        ?>
                        <div class="list_index_edit">
                            <h3>�ֹ��� ����</h3>
                            <div class="form-group regist">
                                <label class="control-label" for="name"><font color="red">*</font> �̸�</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control"  id="oName" name="oName" maxlength="10" value="<?=$M_MEM_NM?>">
                                    </div>
                            </div>

                            <div class="form-group regist">
                                <label class="control-label" for="email"><font color="red">*</font> �̸���</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="oEmail1" name="oEmail1" maxlength="20" style="width: 107px;" value="<?=$EMAIL_PRE?>">  @ 
                                    <input type="text" class="form-control" id="oEmail2" name="oEmail2" maxlength="15" style="width: 83px;" value="<?=$EMAIL_POST?>">
                                    <!-- <button type="button" name="emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">����</button> -->
                                </div>
                            </div>
                            <div class="form-group regist">
                                <label class="control-label" for="phone"><font color="red">*</font> ��ȭ��ȣ</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control " name="oPhone1" id="oPhone1" style="width:50px;display:inline;" maxlength="3" value="<?=$O_PHONE_1?>" oninput="maxLengthCheck(this)"> -
                                    <input type="number" class="form-control " name="oPhone2" id="oPhone2" style="width:50px;display:inline;" maxlength="4" value="<?=$O_PHONE_2?>" oninput="maxLengthCheck(this)"> -
                                    <input type="number" class="form-control " name="oPhone3" id="oPhone3" style="width:50px;display:inline;" maxlength="4" value="<?=$O_PHONE_3?>" oninput="maxLengthCheck(this)">
                                </div>
                            </div>

                            <div class="form-group regist">
                                <label class="control-label" for="name"><font color="red">*</font> �޴���ȭ</label>
                                <div class="col-sm-10">
                                <input type="hidden" id="hpcerCk" name="hpcerCk">
                                <input type="number" class="form-control " name="oHPhone1" id="oHPhone1" style="width:50px;display:inline;" maxlength="3" value="<?=$O_HPHONE_1?>" oninput="maxLengthCheck(this)"> -
                                <input type="number" class="form-control " name="oHPhone2" id="oHPhone2" style="width:50px;display:inline;" maxlength="4" value="<?=$O_HPHONE_2?>" oninput="maxLengthCheck(this)"> -
                                <input type="number" class="form-control " name="oHPhone3" id="oHPhone3" style="width:50px;display:inline;" maxlength="4" value="<?=$O_HPHONE_3?>" oninput="maxLengthCheck(this)">
                                </div>
                            </div>
                            <div class="form-group regist">
                                <label class="control-label" for="zipcode"><font color="red">*</font> �����ȣ</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="o_zipcode" name="o_zipcode" style="width:75px;display:inline;"  max="99999" maxlength="5" value="<?=$O_ZIPCODE?>" oninput="maxLengthCheck(this)">
                                
                                </div><div style="clear:both"></div>
                                <label class="control-label"><font color="red">*</font> ��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</label>
                                <div class="col-sm-10">
                                    <input type="text" class="long-text" style=" display:inline; width:270px" id="Oaddr1"    name="Oaddr1" maxlength=50 value="<?=$M_ADDR1?>"><br>
                                    <input type="text" class="long-text" style=" display:inline; width:270px" id="Oaddr2"   name="Oaddr2" maxlength=50 value="<?=$M_ADDR2?>">
                                </div>
                            </div>

                                
                        </div>


                        <div class="list_index_edit">
                            <b>������ ����</b>&nbsp;&nbsp;<label>�ֹ��� ������ ����<input type="checkbox" id="chkSame" onclick="js_same_order_receiver();" ><label>
                            <div class="form-group regist">
                                <label class="control-label" for="name"><font color="red">*</font> �̸�</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control"  id="rName" name="rName" maxlength="10">
                                    </div>
                            </div>

                            <div class="form-group regist">
                                <label class="control-label" for="email1"><font color="red">*</font> �̸���</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="rEmail1" name="rEmail1" maxlength="20" style="width: 107px;">  @ 
                                    <input type="text" class="form-control" id="rEmail2" name="rEmail2" maxlength="15" style="width: 83px;">
                                    <!-- <button type="button" name="emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">����</button> -->
                                    <div class="btn-group">                                
                                        <ul class="dropdown-menu" data-target="email2" style="display:none;">
                                            <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
                                            <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
                                            <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                                        </ul>                                                                
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function(){
                                        $(".dropdown-toggle").click(function(){
                                            if ( $(".dropdown-menu").css("display") == "none" )
                                            {
                                                $(".dropdown-menu").css("display","block");
                                            } else {
                                                $(".dropdown-menu").css("display","none");
                                            }
                                        });
                                        $(".dropdown-menu").click(function(){
                                            $(this).css("display","none");
                                        });
                                    });
                                </script>          
                            </div>
                            <div class="form-group regist">
                                <label class="control-label" for="phone"><font color="red">*</font> ��ȭ��ȣ</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control " name="rPhone1" id="rPhone1" style="width:50px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                                    <input type="number" class="form-control " name="rPhone2" id="rPhone2" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                                    <input type="number" class="form-control " name="rPhone3" id="rPhone3" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                                </div>
                            </div>

                            <div class="form-group regist">
                                <label class="control-label" for="name"><font color="red">*</font> �޴���ȭ</label>
                                <div class="col-sm-10">
                                <input type="hidden" id="hpcerCk" name="hpcerCk">
                                <input type="number" class="form-control " name="rHPhone1" id="rHPhone1" style="width:50px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                                <input type="number" class="form-control " name="rHPhone2" id="rHPhone2" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                                <input type="number" class="form-control " name="rHPhone3" id="rHPhone3" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                                </div>
                            </div>
                            <div class="form-group regist">
                                <label class="control-label" for="rZipCode"><font color="red">*</font> �����ȣ</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="rZipCode" name="rZipCode" style="width:75px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
                                    <button type="button" class="trigger-find_addr" id="zipcodebt" name="zipcodebt">�ּҰ˻�</button>
                                </div><div style="clear:both"></div>
                                <label class="control-label"><font color="red">*</font> ��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</label>
                                <div class="col-sm-10">
                                    <input type="text" class="long-text" style=" display:inline; width:270px;" id="rAddr1" name="rAddr1" maxlength=50><br>
                                    <input type="text" class="long-text" style=" display:inline; width:270px;" id="rAddr2" name="rAddr2" maxlength=50>
                                </div>
                            </div>
                            <div class="form-group regist" style="padding:1%;">
                                <input type="text" placeholder="��û������ ���� �Է��մϴ�" name="rMemo" style="width:270px; height:30px; border:1px solid #DDDDDD;">
                            </div>
                            <!-- <div class="form-group regist">



                            </div> -->
                            <font color="gray">������ [���� 10�� ~ 17��]<br>�ֹ� �� ��ȭ ��û ���� üũ���ּ���!</font><br>
                            <label style="position:relative; right:-60%;" >������ ��ȭ��û<input type="checkbox" id="callyn" name="callyn" onclick="js_call_yn()" value="N"></label>

                            <div class="" style="background-color:#FCEEF1; padding-top:5px; padding-bottom:5px;" >
                                �ֹ��Ͻ� ��ǰ, ����, �������, ���γ��� ���� ���� Ȯ���Ͽ�����, ���� �����Ͻðڽ��ϱ�? <br>(���ڻ�ŷ��� ��8�� �� 2��) &nbsp; &nbsp; 
                                <label><input type="checkbox" name="agreeYn">�����մϴ�.</label>
                            </div>
                                
                        


                            <input type="hidden" name="is_mobile" value="mobileMode">

                                
                        </div>

                    </form>
                </div><!--content-->
            </form>
        </div><!--"background"-->
    </body>
</html>