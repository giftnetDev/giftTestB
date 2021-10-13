<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD030"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
    require "../../_classes/biz/order/order.php";
    require "../../_classes/dataStructure/LinkedList.php";

    // print_r($_SESSION);

?>
<?
    function mappingStickerCode($db, $goodsNo, $cate, $stickerOption){

        // echo "goodsNo : ".$goodsNo.", cate : ".$cate.", stickerOption : ".$stickerOption."<br>";
        // exit;

        if($goodsNo==""){
            echo "there's no goodsNo<br>";
            return "";
        }

        $query = "  SELECT GG.GOODS_NO, GG.GOODS_NAME 
                    FROM (
                    SELECT G.GOODS_NO, G.GOODS_NAME
                    FROM TBL_GOODS G
                    WHERE GOODS_NAME LIKE '%$stickerOption%'
                    AND GOODS_CATE = '$cate'
                    ) GG
                    WHERE GG.GOODS_NAME LIKE CONCAT('%',
                    (SELECT IE.DCODE
                    FROM TBL_GOODS_EXTRA IE
                    WHERE IE.GOODS_NO = '$goodsNo'
                    AND IE.PCODE = 'GOODS_STICKER_SIZE'),'%')
                ";
        
        // echo $query."<br>";
        // exit;
        $result=mysql_query($query, $db);
        $record=array();
        $cnt=0;
        if($result<>""){
            // echo "------DB_RESULT------<br>";
            $cnt=mysql_num_rows($result);

            if($cnt>0){
                for($i=0; $i<$cnt; $i++){
                    $record[$i]=mysql_fetch_assoc($result);
                }
            }
            // echo "-------DB_RESULT CHK START-------<br>";
            // print_r($record);
            // echo "--------DB_RESULT CHK END--------<br>";
            return $record;
        }
        // echo "--------DB_RESULT IS NULL-------<br>";

    }
    function selectStickerSizeMRO($db, $category, $goodsCode, $stickerType){
        $query="    SELECT IFNULL(G.GOODS_CODE, '')
                    FROM    TBL_GOODS G
                    JOIN    TBL_GOODS_EXTRA E ON G.GOODS_NO = E.GOODS_NO
                    WHERE   E.DCODE IN(
                        SELECT  E.DCODE
                        FROM    TBL_GOODS G
                        JOIN TBL_GOODS_EXTRA E
                        WHERE   G.GOODS_CODE='$goodsCode'
                        AND     E.PCODE =   'GOODS_STICKER_SIZE'
                    )
                    AND E.PCODE = 'GOODS_STICKER_SIZE'
                    AND G.GOODS_NAME LIKE '%$sticker_type%'
                    AND G.GOODS_CATE LIKE '$category%' 
                    ";
        
        $result =   mysql_query($query, $db);
        $rows   =   mysql_fetch_array($result);
        $record =   $rows[0];
        if($record <>''){
            return $record;
        }
        else{
            $query= "   SELECT  G.GOODS_CODE
                        FROM    TBL_GOODS G
                        JOIN    TBL_GOODS_EXTRA E ON G.GOODS_NO = E.GOODS_NO
                        WHERE   E.DCODE = '중'
                        AND     E.PCODE = 'GOODS_STICKER_SIZE'
                        AND     G.GOODS_NAME    LIKE    '%$stickerType%'
                        AND     G.GOODS_CATE    LIKE    '$category%'
            ";

            $result =   mysql_query($query, $db);
            $rows   =   mysql_fetch_array($result);
            $record =   $rows[0];
            return $record;
        }
    }
    function insertToTempOrderMROConversion($db, $temp_no, $order_date, $cp_order_no, $cp_no, $goods_code, $goods_name, $sale_price, $qty, $o_mem_nm, $o_addr, $o_phone, $o_hphone, $r_mem_nm, $r_phone, $r_hphone, $zipcode, $r_addr1, $memo, $opt_wrap_code, $opt_sticker_code, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $opt_manager_nm, $opt_outstock_date, $delivery_type, $delivery_price, $work_memo, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box ) {

        $query = "INSERT INTO TBL_TEMP_ORDER_MRO_CONVERSION	
                            (TEMP_NO, ORDER_DATE, CP_ORDER_NO, CP_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE, QTY, O_MEM_NM, O_ADDR, O_PHONE, O_HPHONE, R_MEM_NM, R_PHONE, R_HPHONE, ZIPCODE, R_ADDR1, MEMO, OPT_WRAP_CODE, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_PRINT_MSG, OPT_OUTBOX_TF, OPT_MANAGER_NM, OPT_OUTSTOCK_DATE, DELIVERY_TYPE, DELIVERY_PRICE, WORK_MEMO, DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX)
    
                    VALUES   ('$temp_no', '$order_date', '$cp_order_no', '$cp_no', '$goods_code', '$goods_name', '$sale_price', '$qty', '$o_mem_nm', '$o_addr', '$o_phone', '$o_hphone', '$r_mem_nm', '$r_phone', '$r_hphone', '$zipcode', '$r_addr1', '$memo', '$opt_wrap_code', '$opt_sticker_code', '$opt_sticker_msg', '$opt_print_msg', '$opt_outbox_tf', '$opt_manager_nm', '$opt_outstock_date', '$delivery_type', '$delivery_price', '$work_memo', '$delivery_cp', '$sender_nm', '$sender_phone', '$delivery_cnt_in_box');
                    ";

        //echo $query."<br/>";
        //exit;

        if(!mysql_query($query,$db)) {
            return false;
            echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
            exit;
        } else {
            return mysql_insert_id();
        }
        
    }
	function insertTempOrderMRO1($db, $temp_no, $order_date, $cp_order_no, $o_mem_nm, $o_phone, $o_hphone, $o_addr1, $r_mem_nm, $r_phone, $r_hphone, $r_zipcode, $r_addr2, $goods_name, $qty, $sale_price, $sale_total_price, $delivery_state, $delivery_request, $memo, $delivery_no, $wrap_method) {

		$query = "INSERT INTO TBL_TEMP_ORDER_MRO (TEMP_NO, ORDER_DATE, CP_ORDER_NO, O_MEM_NM, O_PHONE, 
													O_HPHONE, O_ADDR1, R_MEM_NM, R_PHONE, R_HPHONE, 
													ZIPCODE, R_ADDR2, GOODS_NAME, QTY, SALE_PRICE, 
													SALE_TOTAL_PRICE, DELIVERY_STATE, DELIVERY_REQUEST, MEMO, DELIVERY_NO, 
													WRAP_METHOD)
				   
				   VALUES (	'$temp_no', '$order_date', '$cp_order_no', '$o_mem_nm', '$o_phone', '$o_hphone', '$o_addr1', '$r_mem_nm', '$r_phone', '$r_hphone', '$r_zipcode', '$r_addr2', '$goods_name', '$qty', '$sale_price', '$sale_total_price', '$delivery_state', '$delivery_request', '$memo', '$delivery_no', '$wrap_method' ); ";

		// echo $query;
		exit;

		if(!mysql_query($query,$db)) {
			return false;
			echo "<script>alert(\"[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."\"); //history.go(-1);</script>";
			exit;
		} else {
			return mysql_insert_id();
		}
		
	}
    function makingSelectWrapMRO($db, $id, $name){
        $query="SELECT  GOODS_NO, GOODS_NAME
                FROM    TBL_GOODS
                WHERE   GOODS_CATE='010204'
                ";

        $result=mysql_query($query, $db);
        $recrd=array();
        $str="";
        $cnt=0;
        if($result){
            $cnt=mysql_num_rows($result);
        }
        else{
            return;
        }
        if($cnt>0){
            $str.="<select id='".$id."' name='".$name."'><option value=''>포장지를 선택하세요</option>";

            for($i=0; $i<$cnt; $i++){

                $record[$i]=mysql_fetch_assoc($result);
                $str.="<option value='".$record[$i]["GOODS_NO"]."'>".$record[$i]["GOODS_NAME"]."</option>";
            }
            $str.="</select>";

            echo $str;
        }
    }//end of function
    function getGoodsInfoByGoodsCode($db, $goodsCode){
		$query = " SELECT GOODS_NO, GOODS_NAME, GOODS_CODE, CATE_04
				FROM TBL_GOODS
				WHERE GOODS_CODE='".$goodsCode."' ; ";
		$result=mysql_query($query, $db);
		if($result){
			$rows=mysql_fetch_array($result);
			return $rows;
		}
		return "";
    }

    function getListTempOrderMROConversion($db, $temp_no) {

		$query = "SELECT    SEQ,ORDER_DATE, CP_ORDER_NO, CP_NO, GOODS_CODE, GOODS_NAME, SALE_PRICE, QTY, 
		                    O_MEM_NM, O_PHONE, O_HPHONE, R_MEM_NM, R_PHONE, R_HPHONE,
						    ZIPCODE, R_ADDR1, MEMO, OPT_WRAP_CODE, OPT_STICKER_NO, OPT_STICKER_CODE, OPT_STICKER_MSG, OPT_PRINT_MSG, 
                            OPT_OUTBOX_TF, OPT_MANAGER_NM, OPT_OUTSTOCK_DATE, DELIVERY_TYPE, DELIVERY_PRICE, WORK_MEMO, 
                            DELIVERY_CP, SENDER_NM, SENDER_PHONE, DELIVERY_CNT_IN_BOX
				  FROM      TBL_TEMP_ORDER_MRO_CONVERSION	
				  WHERE     TEMP_NO = '$temp_no' 
                  AND       REG_ORDER_TF='N'
                  
                ";

		// echo $query;
		//exit;

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
		
	}//end of function

    function latestRegisteredMRO($db){
        $query="SELECT MAX(TEMP_NO)
                FROM TBL_TEMP_ORDER_MRO_CONVERSION
                ";
        
        $result=mysql_query($query, $db);

        if($result){
            $rows=mysql_fetch_row($result);
            return $rows[0]; 
        }
        else{
            echo "<script>alert('latestRegisteredSINHYUPOrder() Error!');</script>";
            exit;
        }
    }

    function getGoodsNoByGoodsCodeMRO($db, $goodsCode){
        $query="SELECT  GOODS_NO
                FROM    TBL_GOODS
                WHERE   GOODS_CODE = '$goodsCode'
                AND     USE_TF='Y'
                AND     DEL_TF='N'
                ";
        
        $result=mysql_query($query, $db);
        $rows=mysql_fetch_row($result);
        return $rows[0];
    }

    function makeSelectBoxOnNonghyupSticker($arr, $filter, $id, $name, $defualtValue, $defaultName, $selectedValue, $seq){

        $str="";
        if(empty($arr) || $arr=="") return;
        $cnt=sizeof($arr);

        if($cnt<1) return ;

        // echo "test<br>";
        // return;

        $str.= "<SELECT id='$id' name='$name' onchange='js_change_sticker(\"".trim($id)."\", \"$seq\")'>
                <OPTION value='$defualtValue'>$defaultName</OPTION>
                ";
        for($i = 0; $i < $cnt; $i++){
            $str.= "<OPTION value='".$arr[$i]["GOODS_NO"]."'";
            if($selectedValue==$arr[$i]["GOODS_NO"]){
                $str.=" selected ";
            }
            $str.=">".$arr[$i]["GOODS_NAME"]."</OPTION>";
        }
        $str.="</SELECT>";

        echo $str;
    }

    function makeSelectBoxAllNonghyupSticker($db, $id, $name, $defaultValue, $defaultName){
        $query="SELECT  GOODS_NO, GOODS_NAME
                FROM    TBL_GOODS
                WHERE   GOODS_CATE='010304'
                AND     USE_TF='Y'
                AND     DEL_TF='N'
                ";
        $result=mysql_query($query, $db);
        $cnt=0;
        $record=array();
        if($result<>""){
            $cnt=mysql_num_rows($result);
            if($cnt<1){
                
            }
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }

            $str="<SELECT id='$id' name='$name'>";
            $str.="<OPTION value='$defaultValue'>$defaultName</OPTION>";
            for($i=0; $i<$cnt; $i++){
                $str.="<OPTION value=".$record[$i]["GOODS_NO"].">".$record[$i]["GOODS_NAME"]."</OPTION>";
            }
            $str.="</SELECT>";
        }
        echo $str;
    }
    function getAllNonghyupSticker($db){
        $query="SELECT  GOODS_NO, GOODS_NAME
                FROM    TBL_GOODS
                WHERE   GOODS_CATE='010304'
                AND     USE_TF='Y'
                AND     DEL_TF='N'
                ";
        $result=mysql_query($query, $db);
        $cnt=0;
        $record=array();

        if($result<>""){
            $cnt = mysql_num_rows($result);
            
        }
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
        }
        return $record;
    }

    function getMROOrderBySEQ($db, $seqs){

        $query="SELECT  MRO.ORDER_DATE,     MRO.O_MEM_NM,       MRO.GOODS_CODE,         MRO.O_ADDR,         MRO.O_PHONE,            MRO.O_HPHONE,
                        MRO.R_MEM_NM,       MRO.ZIPCODE,        MRO.R_ADDR1,            MRO.R_HPHONE,       MRO.R_PHONE,
                        MRO.GOODS_NAME,     MRO.QTY,            MRO.SALE_PRICE,         MRO.DELIVERY_PRICE, MRO.SA_DELIVERY_PRICE,  MRO.MEMO,
                        MRO.OPT_STICKER_NO, MRO.OPT_WRAP_NO,    MRO.OPT_STICKER_MSG,    MRO.CP_ORDER_NO,    MRO.WORK_MEMO,          MRO.REQUEST_MEMO,   MRO.SUPPORT_MEMO,   MRO.OPT_OUTSTOCK_DATE,
                        MRO.DELIVERY_TYPE,  MRO.DELIVERY_CP,    MRO.SENDER_NM,      MRO.SENDER_PHONE,       MRO.BULK_TF

                FROM    TBL_TEMP_ORDER_MRO_CONVERSION MRO
                WHERE   SEQ IN (".$seqs.")
                        
                        ";

        // echo $query."<br>";
        
        $result=mysql_query($query, $db);
        $record=array();
        $cnt=0;

        if(!$result){
            echo "<script>alert('list_MRO_Order_By_SEQ : ERROR');</script>";
            exit;
        }
        else{
            $cnt=mysql_num_rows($result);
        }
        for($i=0; $i<$cnt; $i++){
            $record[$i]=mysql_fetch_assoc($result);
        }
        return $record;

    }//end of func.getMRO_Orders.......

    function getOrderGoodsMROInfoByGoodsCode($db, $goodsCode){
        $query="SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME,
                        G.CATE_03, G.DELIVERY_CNT_IN_BOX, G.TAX_TF, G.BUY_PRICE, G.PRICE
                FROM    TBL_GOODS G
                WHERE   G.GOODS_CODE='$goodsCode'
                ";
        echo $query."<br>";
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('FUNC-get_order_goods_mro_info_by_goods_code-ERROR');</script>";
            exit;
        }
        $rows=mysql_fetch_row($result);
        return $rows;
    }

    function updateMROOrder($db, $seq){
        $query="UPDATE  TBL_TEMP_ORDER_MRO_CONVERSION
                SET     REG_ORDER_TF='Y'
                WHERE   SEQ IN (".$seq.")
                AND     GOODS_CODE<>'' ";


        $result=mysql_query($query, $db);

        if(!$result){
            echo "<script>alert('func.update_MRO_Order-ERROR');</script>";
            exit;
        }
    }//end of 
  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++//
//--------------END OF FUNCTION ZONE-------------------//
//+++++++++++++++++++++++++++++++++++++++++++++++++++++//
?>
<?  
       if($mode=="UPLOAD_ORDER"){

        $row_cnt=count($chk_order);
        $seqStr="";

        for($k=0; $k< $row_cnt; $k++){
            // echo $chk_order[$k]."<br>";
            $seqStr.=$chk_order[$k].",";
        }

        // echo $seqStr."<br>";
        // exit;
        $seqStr=trim($seqStr);
        $seqStr=trim($seqStr,",");


        $MROOrders=getMROOrderBySEQ($conn, $seqStr);

        $cntS=sizeof($MROOrders);


        $on_uid="";//임시
        $new_mem_no=0;//임시
        $MRO_NO=3559;//농협_하나로_유통;
        $o_zipcode="";
        $o_addr2="";
        $o_phone="";
        $o_hphone="";
        $o_email="";
        $r_addr2="";
        $sale_adm_no=16;
        $order_state=1;
        $total_extra_price=0;
        // $total_sa_delivery_price=0;
        $total_discount_price=0;
        $pay_type='';
        $cp_order_no="";

//-------------TBL_ORDER_GOODS---------------------------

        $t_delivery_price=0;
        $t_discount_price=0;
        $t_extra_price=0;
        $t_labor_price=0;
        $t_order_price=0;
        $t_print_price=0;
        $t_sale_susu=0;
        $t_sticker_price=0;
        // $sinhyup_order_addr=SetStringToDB("대전광역시 서구 한밭대로 745 (문산동)");//신협 관련된 주문_주소는 모두 이 주소로 나오도록 페치

        for($i=0; $i<$cntS; $i++){

            if($MROOrders[$i]["GOODS_CODE"]<1){
                echo "<script>alert('상품등록이 되어있지 않습니다');</script>";
                continue;
            } 


            $reserveNo=getReservNo($conn, "EN");


            insertOrder($conn, $on_uid, $reserveNo, $new_mem_no,
                        $MRO_NO, $MROOrders[$i]['O_MEM_NM'], $o_zipcode, $MROOrders[$i]['O_ADDR'], $o_addr2,
                        $MROOrders[$i]['O_PHONE'], $MROOrders[$i]['O_HPHONE'], $o_email, $MROOrders[$i]['R_MEM_NM'], $MROOrders[$i]['ZIPCODE'],
                        $MROOrders[$i]['R_ADDR1'], $r_addr2, $MROOrders[$i]['R_HPHONE'], $MROOrders[$i]['R_PHONE'], $r_email, $MROOrders[$i]['MEMO'],
                        $MROOrders[$i]['BULK_TF'], $sale_adm_no, $order_state, $total_price_mro_temp, $total_buy_price_mro_temp,
                        $MROOrders[$i]["SALE_PRICE"]*$MROOrders[$i]["QTY"], $total_extra_price, $MROOrders[$i]['DELIVERY_PRICE'], $MROOrders[$i]['SA_DELIVERY_PRICE'],
                        $total_discount_price, $MROOrders[$i]['QTY'], $pay_type, $MROOrders['DELIVERY_TYPE'], 'Y', $s_adm_no);


            $goodsInfo=getOrderGoodsMROInfoByGoodsCode($conn, $MROOrders[$i]["GOODS_CODE"]);

            $G_GOODS_NO               =   trim($goodsInfo[0]);
            $G_GOODS_CODE             =   trim($goodsInfo[1]);
            $G_GOODS_NAME             =   trim(SetStringFromDB($goodsInfo[2]));
            $G_GOODS_SUB_NAME         =   trim(SetStringFromDB($goodsInfo[3]));
            $G_SUPPLY_CP_NO           =   trim($goodsInfo[4]);
            $G_DELIVERY_CNT_IN_BOX    =   trim($goodsInfo[5]);
            $G_TAX_TF                 =   trim($goodsInfo[6]);
            $G_BUY_PRICE              =   trim($goodsInfo[7]);
            $G_PRICE                  =   trim($goodsInfo[8]);

            $memos=array('opt_request_memo' => $MROOrders[$i]["REQUEST_MEMO"], 'opt_support_memo' => $MROOrders[$i]["SUPPORT_MEMO"]);

            $C_CATE_01  =""; //"샘플", "증정", "추가", "일반"
            $C_CATE_02  =""; //계산서 번호
            $C_CATE_03  =""; //계산서 종류 ('CF001' => '전자계산서', 'CF002' => '전자세금계산서', 'CF004' => '카드결제')
            $C_CATE_04  =""; //어떤 애인지 잘 모르겠다.....


            if($MROOrders[$i]["SENDER_NM"]==""){
                $MROOrders[$i]["SENDER_NM"]="(주)기프트넷";

            }
            if($MROOrders[$i]["SENDER_PHONE"]==""){
                $MROOrders[$i]["SENDER_PHONE"]="031-527-6812";
            }
            if($MROOrders[$i]["OPT_OUTSTOCK_DATE"]=="0000-00-00 00:00:00"){
                $week=date("w");
                if($week==5){//오늘이 금요일일 경우
                    $MROOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+3 day"));	
                } 
                else if($week==6){//오늘이 토요일일 경우
                    $MROOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+2 day"));
                }
                else{//그 외
                    $MROOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+1 day"));
                }
                
            }

            // echo "CP_ORDER_NO : ".$

            insertOrderGoods(   
                                $conn, $on_uid, $reserveNo, $MROOrders[$i]["CP_ORDER_NO"], $G_SUPPLY_CP_NO, 
                                $new_mem_no, $i, $G_GOODS_NO, $G_GOODS_CODE, $G_GOODS_NAME,
                                $G_GOODS_SUB_NAME, $MROOrders[$i]["QTY"], $MROOrders[$i]["OPT_STICKER_NO"], 
                                $MROOrders[$i]["OPT_STICKER_MSG"], $MROOrders[$i]["OPT_OUTBOX_TF"],
                                $G_DELIVERY_CNT_IN_BOX, $MROOrders[$i]["OPT_WRAP_NO"], $MROOrders[$i]["OPT_PRINT_MSG"], 
                                $MROOrders[$i]["OPT_OUTSTOCK_DATE"], $MROOrders[$i]["WORK_MEMO"], 
                                $memos, $MROOrders[$i]["DELIVERY_TYPE"], $MROOrders[$i]["DELIVERY_CP"], 
                                $MROOrders[$i]["SENDER_NM"], $MROOrders[$i]["SENDER_PHONE"],
                                $C_CATE_01, $C_CATE_02, $C_CATE_03, $C_CATE_04, $G_PRICE,
                                $G_BUY_PRICE, $MROOrders[$i]["SALE_PRICE"], $t_extra_price, 
                                $MROOrders[$i]["DELIVERY_PRICE"], $MROOrders[$i]["SA_DELIVERY_PRICE"],
                                $t_discount_price, $t_sticker_price, $t_print_price, $t_sale_susu, $t_labor_price,
                                $t_order_price, $G_TAX_TF, $order_state, "Y", $s_adm_no
                            );


        }//end of for($cntS)
        updateMROOrder($conn, $seqStr);


        $mode="LIST";

    }//end of mode="UPDATE_ORDER"

    if($mode=="CHANGE_SGOODS"){
        $sGoodsNo   = $_POST['paramSGoodsNo'];
        $goodsNo    = $_POST['paramGoodsNo'];
        $buyPrice   = $_POST['paramBuyPrice'];
        $seq        = $_POST['paramSeq'];
        $goodsCode  = $_POST['paramGoodsCode'];

        $query = "  UPDATE TBL_TEMP_ORDER_MRO_CONVERSION
                    SET     GOODS_CODE    =   '$goodsCode'
                    WHERE   SEQ         =   '$seq'
                ";
        
        // echo "$query<br>";
        // exit;
        
        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('CHANGE_GOODS_ERROR11');</script>";
        }
        // exit;


        $mode="LIST";
    }


    if ($mode == "FR") {
    

        // echo "File Update"."<br>";
        $fileDir=$_SERVER[DOCUMENT_ROOT]."/upload_data/temp_order_MRO/";

 

        $fileName=upload($_FILES[fileName], $fileDir, 10000, array('xls'));

        $fileInfo=$fileDir.$fileName;


        require_once "../../_PHPExcel/Classes/PHPExcel.php";
        require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
        
        $objPHPExcel= new PHPExcel();

        
        try {
            
            $objPHPExcel=PHPExcel_IOFactory::load($fileInfo);
            // exit;
            // $objReader->setReadDataOnly(true);

            $curSheet=$objPHPExcel->getActiveSheet();
            $lastRow=$curSheet->getHighestRow();

            // echo "lastRow : ".$lastRow."<br>";

            


            for ($i = 2 ; $i <= $lastRow ; $i++) {

                $order_date       = $curSheet->getCell('A'.$i)->getValue();

                //B_C_D
                $cp_order_no  = $curSheet->getCell('A'.$i)->getValue()."_".$curSheet->getCell('B'.$i)->getValue()."_".$curSheet->getCell('C'.$i)->getValue();

                $o_mem_nm         = $curSheet->getCell('D'.$i)->getValue();
                $sale_price		  = $curSheet->getCell('E'.$i)->getValue();
                $sale_total_price = $curSheet->getCell('F'.$i)->getValue();
                $o_phone          = $curSheet->getCell('G'.$i)->getValue();
                $o_hphone		  = $curSheet->getCell('H'.$i)->getValue();
                $o_addr1  	 	  = $curSheet->getCell('I'.$i)->getValue();
                $r_mem_nm		  = $curSheet->getCell('J'.$i)->getValue();
                $r_zipcode		  = $curSheet->getCell('K'.$i)->getValue();
                $r_addr2		  = $curSheet->getCell('L'.$i)->getValue();
                $r_phone		  = $curSheet->getCell('M'.$i)->getValue();
                $r_hphone		  = $curSheet->getCell('N'.$i)->getValue();
                $goods_name		  = $curSheet->getCell('O'.$i)->getValue();
                $qty			  = $curSheet->getCell('P'.$i)->getValue();
                $goods_code		  = $curSheet->getCell('Q'.$i)->getValue();
                $wrap_method	  = $curSheet->getCell('R'.$i)->getValue();
                $memo			  = $curSheet->getCell('S'.$i)->getValue();
                $delivery_request = $curSheet->getCell('T'.$i)->getValue();
                $buyer_request	  = $curSheet->getCell('U'.$i)->getValue();
                $susu_rate		  = $curSheet->getCell('V'.$i)->getValue();
                $confirm_tf		  = $curSheet->getCell('W'.$i)->getValue();
                $delivery_state   = $curSheet->getCell('X'.$i)->getValue();
                

                $o_mem_nm		= iconv("UTF-8","EUC-KR",$o_mem_nm);
                $o_addr1		= iconv("UTF-8","EUC-KR",$o_addr1);
                $r_mem_nm		= iconv("UTF-8","EUC-KR",$r_mem_nm);
                $r_zipcode		= iconv("UTF-8","EUC-KR",$r_zipcode);
                $r_addr2		= iconv("UTF-8","EUC-KR",$r_addr2);
                $goods_name		= iconv("UTF-8","EUC-KR",$goods_name);
                $delivery_state	= iconv("UTF-8","EUC-KR",$delivery_state);
                $delivery_request = iconv("UTF-8","EUC-KR",$delivery_request);
                $memo			= iconv("UTF-8","EUC-KR",$memo);
                $wrap_method	= iconv("UTF-8","EUC-KR",$wrap_method);
                $goods_code 	= iconv("UTF-8","EUC-KR", $goods_code);

                $r_mem_nm = $r_mem_nm."님";


                $opt_sticker_code = trim($wrap_method);

                $con_cp_no = "3559"; //MRO
                $zipcode = $r_zipcode;
                $r_addr1 = $r_addr2;
                $opt_wrap_code = ""; 
                $opt_sticker_msg = "";
                $opt_print_msg = "";
                $opt_outbox_tf = "";
                $opt_manager_nm = "기타"; //MRO 이므로 기본으로 기타 세팅
                $opt_outstock_date = date("Y-m-d",strtotime("1 day")); //기본값으로 금일 + 1
                $con_delivery_type = "택배"; //MRO는 일반적으로 택배사용
                $delivery_price = "";
                $work_memo = $memo;
                $order_memo = $delivery_request;
                $delivery_cp = "롯데택배"; //MRO는 일반적으로 CJ대한통운사용
                
                $sale_price = str_replace(",", "", $sale_price);

                insertToTempOrderMROConversion($conn, $fileName, $order_date, $cp_order_no, $con_cp_no, $goods_code, $goods_name, $sale_price, $qty, $o_mem_nm, $o_addr1, $o_phone, $o_hphone, $r_mem_nm, $r_phone, $r_hphone, $zipcode, $r_addr1, $order_memo, $opt_wrap_code, $opt_sticker_code, $opt_sticker_msg, $opt_print_msg, $opt_outbox_tf, $opt_manager_nm, $opt_outstock_date, $con_delivery_type, $delivery_price, $work_memo, $delivery_cp, $sender_nm, $sender_phone, $delivery_cnt_in_box);


            }//end of for()

        } catch (exception $e) {
            echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
            echo "<br><br>";
            echo $e."<br>";
        }
        $mode="LIST";
        
    }//end of mode="FR"


    if($mode=="LIST"){
        // echo "now mode is LIST mode<br>";
        $allStickerList=getAllNonghyupSticker($conn);


        // print_r($allStickerList);
        

        $tempFileName=latestRegisteredMRO($conn);
        // echo "tempFileName : ".$tempFileName."<br>";

        $arr_rs=getListTempOrderMROConversion($conn, $tempFileName);
        $cntArr=sizeof($arr_rs);

        // echo "Cnt_Of_MRO_Order : ".$cntArr."<br>";
    }//end of if(mode=LIST)



?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>"/>
        <title><?=$g_title?></title>

        <link rel="stylesheet" href="../css/newStyle/newERPStyle.css">
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>

        <script>

            let g_candidate_goods = new Array();

            function js_change_sticker(id, seq){
                var stickerNo=$("#"+id).val();
                // alert(seq);
                // return ;
                $.ajax({
                    url:"../ajax/ajax_MRO.php",
                    type:"POST",
                    dataType:"JSON",
                    async:true,
                    data:{
                        'mode':"CHANGE_STICKER_NO",
                        'stickerNo':stickerNo,
                        'seq':seq
                    },
                    success:function(data){
                        alert('스티커가 변경되었습니다');

                    },
                    error:function(jqueryXHR,textStatus,errorThrown){
                        alert('fail of test');
                        console.log(jqueryXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }

                });
            }

            function js_all_check(){
                var frm=document.frm;

                if(frm['chk_order[]'] != null){
                    if(frm['chk_order[]'].length != null){
                        if(frm.chkAll.checked == true){
                            for(i=0; i<frm['chk_order[]'].length; i++){
                                if(!$("#chk_order_"+i).attr("disabled")){
                                    frm['chk_order[]'][i].checked=true;
                                }
                            }
                        }
                        else{
                            for(i=0; i<frm['chk_order[]'].length; i++){
                                frm['chk_order[]'][i].checked=false;
                            }
                        }
                    }
                    else{
                        if(frm.chkAll.checked == true){
                            frm['chk_order[]'].checked=true;
                        }
                        else{
                            frm['chk_order[]'].checked=false;
                        }
                    }
                }
            }
            function js_upload_order(){

                // alert('js_upload_order');
                // return; 

                var cntArr=$("input[name='hdArrCnt']").val();
                // alert(cntArr)
                if( cntArr<1){
                    alert('리스트가 없습니다');
                    return;
                }
                var frm = document.frm;

                var chkedLength=$("input:checkbox[name='chk_order[]']:checked").length;
                if(chkedLength<1){
                    alert('선택된 MRO 주문이 없습니다');
                    return ;
                }

                frm.mode.value="UPLOAD_ORDER";
                frm.method="POST";
                frm.target="";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();
                
            }//end of function

            function js_tmp_list(){
                var frm=document.frm;
                frm.mode.value="LIST";
                frm.tempFileName="<?=$tempFileName?>";
                frm.target="";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();
            }
            function js_upload_file(){
                var frm =document.frm;

                if(isNull(frm.fileName.value)){
                    alert('파일을 선택해 주세요.');
                    frm.fileName.focus();
                    return;
                }
                if(!AllowAttach(frm.fileName)){
                    return;
                }
                frm.mode.value="FR";//File Upload
                frm.target="";
                frm.method="POST"; //파일은 반드시 포스트로 넘겨야 한다.
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();

            }
            function AllowAttach(obj){
                var file = obj.value;
                extArray= new Array(".xls",".xlsx");
                allowSubmit= false;

                if(!file){

                }
                while(file.indexOf("\\") != -1){
                    file = file.slice(file.indexOf("\\") +1);
                    ext = file.slice(file.lastIndexOf(".")).toLowerCase();

                    for(var i=0;i<extArray.length;i++){
                        if(extArray[i]==ext){
                            allowSubmit=true;
                            break;
                        }
                    }
                }
                if(allowSubmit){
                    return true;
                }
                else{
                    alert("입력하신 파일은 업로드 될 수 없습니다.");
                    return false;
                }
            }

            function js_change_sGoods(index){
                let seq     =   $("#hdSeq_"+index).val();
                let sGoodsNo=   $("#hdSGoodsNo_"+index).val();
                let goodsNo=    $("#hdGoodsNo_"+index).val();
                let buyPrice=   $("#hdBuyPrice_"+index).val();
                let goodsCode=  $("#hdGoodsCode_"+index).val();
                let txt=$('#txtGoodsName_'+index).val();
                if(txt==""){
                    alert('상품을 검색하여 입력해주세요');
                    return ;
                }
                if(goodsNo==""){
                    alert('올바른 상품선택이 되지 않았습니다');
                    return ;
                }
                confirm('상품을 변경하시겠습니까?');
                                // alert(index);
                var frm=document.frm;
                frm.paramGoodsNo.value=goodsNo;
                frm.paramSGoodsNo.value=sGoodsNo;
                frm.paramSeq.value=seq;
                frm.paramGoodsCode.value=goodsCode;
                frm.mode.value="CHANGE_SGOODS";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.target="";
                frm.method="POST";
                frm.submit();

            }
            function js_register_sGoods(index){

                let sGoodsNo=   $("#hdSGoodsNo_"+index).val();
                let goodsNo=    $("#hdGoodsNo_"+index).val();
                let buyPrice=   $("#hdBuyPrice_"+index).val();
                let txt=$('#txtGoodsName_'+index).val();
                if(txt==""){
                    alert('상품을 검색하여 입력해주세요');
                    return ;
                }
                if(goodsNo=="" || goodsNo==0 || goodsNo=='0'){
                    alert('올바른 상품선택이 되지 않았습니다');
                    return ;
                }

                var frm=document.frm;
                frm.paramGoodsNo.value=goodsNo;
                frm.paramSGoodsNo.value=sGoodsNo;
                frm.paramBuyPrice.value=buyPrice;
                frm.mode.value="REGISTER_SGOODS";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.target="";
                frm.method="POST";
                frm.submit();

            }
            function js_setting_option(index, goodsNo,btn){
                //index : DB상에서 TEMP_SINHYUP_ORDER의 SEQ
                //seq : order_write_file_SINHYUP.php 상에서의 순서
                btn.style.backgroundColor ="#22BB22";
                if(goodsNo<1){
                    alert('상품이 선택되지 않았습니다');
                    return ;
                }
                let url="pop_MRO_option.php?seq="+index+"&goodsNo="+goodsNo;
                let wndObj=NewWindow(url,"pop_MRO_option",700,800,'yes');            

            }

            function js_catch_goods_sale_state(data,saleState, seq, dataSeq){

                seq=Number(seq);

                let goods="["+data[dataSeq]['GOODS_CODE']+"]"+data[dataSeq]['GOODS_NAME'];

                if(saleState=="단종"){
                    $("#btn_option_"+seq).attr("disabled",true);
                    $("#mro_sticker_"+seq).attr("disabled",true);
                    $("#chk_order_"+seq).attr("disabled",true);

                    if($("#txtGoodsName_"+seq).hasClass("block_blue")===true){
                        $("#txtGoodsName_").removeClass("block_blue");
                    }
                    if($("#txtGoodsName_"+seq).hasClass("block_red")===false){
                        $("#txtGoodsName_"+seq).addClass("block_red");                                                                                                
                    }
                    goods="(***단종***)"+goods;
                }
                else if(data[0]['SALE_STATE']=="품절"){
                    $("#btn_option_"+seq).attr("disabled",false);
                    $("#mro_sticker_"+seq).attr("disabled",false);
                    $("#chk_order_"+seq).attr("disabled",false);

                    if($("#txtGoodsName_"+seq).hasClass("block_red")===true){
                        $("#txtGoodsName_"+seq).removeClass("block_red");
                    }
                    if($("#txtGoodsName_"+seq).hasClass("block_blue")===false){
                        $("#txtGoodsName_"+seq).addClass("block_blue");                                                                                                
                    }
                    goods="(***품절***)"+goods;

                }
                else{
                    $("#btn_option_"+seq).attr("disabled",false);
                    $("#mro_sticker_"+seq).attr("disabled",false);
                    $("#chk_order_"+seq).attr("disabled",false);
                    if($("#txtGoodsName_"+seq).hasClass("block_red")===true){
                        $("#txtGoodsName_"+seq).removeClass("block_red");
                    }
                    if($("#txtGoodsName_"+seq).hasClass("block_blue")===true){
                        $("#txtGoodsName_"+seq).removeClass("block_blue");
                    }
                }
                
                $("#txtGoodsName_"+seq).val(goods);

            }//end of function:js__catch__goods__sate__state

            function js_get_candidate_goods(){
                return g_candidate_goods;
            }
            $(function(){
                $("input[name='chkAll']").click(function(){
                    js_all_check();
                });
            });    


        </script>
    </head>
    <body>
        <table id="wholeFrame" width="2400px">
            <thead>            
                <colgroup>
                    <col width="190"/>
                    <col width="*"/>
                </colgroup>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" id="tdTopArea">
                    <? require "../../_common/top_area4.php"; ?>
                    </td>
                </tr>
                <tr>
                    <td id="tdLeftArea"><? require "../../_common/left_area_new.php"; ?></td>
                    <td id="tdContent">
                        <div class="mainContent">
                            <form name='frm' enctype="multipart/form-data" method="POST">
<!---------------------------------------------------------------------------------------------------->

                                <div></div><!--네비게이션-->
                                <div>
                                    <h2 class="title">주문등록 - MRO</h2>
                                    <hr class="lineTitle" Noshade size='1' >
                                </div><!--타이틀-->
                                <div class="dvDashboard">
                                    <table class="dashboardTable">
                                        <colgroup>
                                            <col width="10%">
                                            <col width="*">
                                            <col width="*">
                                        </colgroup>
                                        <tr>
                                            <th>MRO 주문파일</th>
                                            <td>
                                                &nbsp;<label for="fileSinhyup"><div class="uploadFileName" id="dvFileSinhyup">Excel File</div></label>
                                                <input type="file" id="fileSinhyup" name="fileName" class="fileHidden">
                                                <input type="button" value="UPLOAD" class="btnExcelUpload" onclick="js_upload_file()">
                                                
                                            </td>
                                        </tr>
                                    </table>

                                </div><!--class="dvDashboard"-->
                                <div class="space50px"></div>

                                <div class="dvCnt">
                                    총 <?=number_format($cntArr)?> 건
                                </div>
                                <div class="space10px"></div>
                                <div class="dvListTable">
                                    <table class="listTable">
                                        <colgroup>
                                            <col width=""><!--chkbox-->
                                            <col width=""><!--주문자명-->
                                            <col width=""><!--수취인명-->
                                            <col width="30%"><!--주문제품명-->
                                            
                                            <col width="5%"><!--매입단가-->
                                            <col width="3%"><!--수량-->
                                            <col width="5%"><!--구매단가-->
                                            <col width="25%"><!--옵션(항목)-->
                                            <col width=""><!--배송특이사항-->
                                            <col width=""><!--주문등록버튼-->

                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="chkAll"></th><!--allChkBox-->
                                                <th>주문자명</th><!--주문자명-->
                                                <th>수취인명</th><!--수취인명-->
                                                <th>주문제품명</th><!--주문제품명-->
                                                <th>단가</th><!--매입단가-->
                                                <th>수량</th><!--수량-->
                                                <th>총구매가</th><!--구입단가-->
                                                <th>옵션(항목)</th><!--옵션(항목)-->
                                                <th>배송특이사항</th><!--배송특이사항-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?
                                            
                                            $stickerInfo=array();
                                            $cntSticker=0;

                                            // print_r($arr_rs);
                                            // echo "<br><br>";
                                            for($i=0; $i<$cntArr; $i++){
                                                // echo "$i 번째<br>";
                                                
                                                $t_goodsCode=$arr_rs[$i]["GOODS_CODE"];
                                                // echo "goods_code : ".$t_goodsCode."<br>";
                                                if($t_goodsCode<>""){
                                                    $goodsInfo=getGoodsInfoByGoodsCode($conn, $t_goodsCode);

                                                    $sGoodsName=$goodsInfo[1];
                                                    $sGoodsCode=$goodsInfo[2];
                                                    $sCATE_04  =$goodsInfo[3];

                                                    $sGoods="[".$t_goodsCode."]".$sGoodsName;

                                                    $goodsNo=getGoodsNoByGoodsCodeMRO($conn, $sGoodsCode);

                                                    // echo "mappginStikcerCode Start!<br>";
                                                    if($goodsNo<>""){
                                                        $stickerInfo = mappingStickerCode($conn, $goodsNo, '010304', $arr_rs[$i]["OPT_STICKER_CODE"]);
                                                    }
                                                    else{
                                                        // echo $goodsCode." : no_goods_no<br>";
                                                    }


                                                    $cntSticker=sizeof($stickerInfo);

                                                }
                                                else{//t_goodsCode==''
                                                    $sGoods="";
                                                }

                                                
                                        ?>
                                                <tr>
                                                    <td rowspan="2">

                                                        <input type="checkbox" name="chk_order[]" id="chk_order_<?=$i?>" value="<?=$arr_rs[$i]['SEQ']?>">
                                                        <input type="hidden" id="hdSeq_<?=$i?>" value="<?=$arr_rs[$i]['SEQ']?>">

                                                    </td>
                                                    <td rowspan="2"><?=$arr_rs[$i]['O_MEM_NM']?></td><!--주문자명-->
                                                    <td rowspan="2"><?=$arr_rs[$i]['R_MEM_NM']?></td><!--수취인명-->
                                                    <td class="tdNoBar">
                                                        <?=$arr_rs[$i]['GOODS_NAME']?>                                
                                                    </td><!--주문제품명-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['SALE_PRICE'])?>원</td><!--단가-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['QTY'])?></td><!--수량-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['SALE_PRICE']*$arr_rs[$i]["QTY"])?>원</td><!--총 구매가-->
                                                    <td class="tdNoBar"><?=$arr_rs[$i]['OPT_STICKER_CODE']?></td><!--옵션(항목)-->
                                                    <td rowspan="2"><?=$arr_rs[$i]['TEMP_MEMO']?></td><!--배송특이사항-->
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <?
                                                        // echo "SALE_STATE : ".$sCATE_04."<br>";
                                                        if($sCATE_04=="단종"){
                                                            $saleState="block_red";
                                                            $saleInfo="(***단종***)";
                                                            ?>
                                                                <script>
                                                                    $("#chk_order_<?=$i?>").attr("disabled",true);
                                                                </script>
                                                            <?
                                                            
                                                            // echo "단종<br>";
                                                        }
                                                        else if($sCATE_04=="품절"){
                                                            $saleState="block_blue";
                                                            $saleInfo="(***품절***)";
                                                        }
                                                        else{
                                                            $saleState="";
                                                            $saleInfo="";
                                                        }

                                                        // if($arr_rs[$i]['GOODS_NO']>0){
                                                        //     $saleState="existence";
                                                        // }
                                                        // else{
                                                        //     $goodsTF="empty";
                                                        // }
                                                    ?>

                                                        <input type="text" id="txtGoodsName_<?=$i?>" class="<?=$saleState?>" value="<?=$saleInfo.$sGoods?>" placeholder="상품(명/코드) 입력후 엔터를 누르세요" size="70">
                                                        <input type="hidden" id="hdGoodsNo_<?=$i?>" value="<?=$goodsNo?>">
                                                        <input type="hidden" id="hdGoodsCode_<?=$i?>" value="<?=$t_goodsCode?>">
                                                        <input type="hidden" id="hdSaleState_<?=$i?>" value="<?=$sCATE_04?>">
                                        
                                                        <script>
                                                            $(function(){
                                                                $("#txtGoodsName_<?=$i?>").keydown(function(e){
                                                                    if(e.keyCode==13){
                                                                        var keyword=$(this).val();
                                                                        if(keyword ==""){
                                                                            alert('keyword를 입력해 주세요');
                                                                            $("#txtGoodsName_<?=$i?>").val('');
                                                                        }
                                                                        else{
                                                                            $.ajax({
                                                                                url:"../ajax/ajax_MRO.php",
                                                                                type:"POST",
                                                                                dataType:"JSON",
                                                                                async:true,
                                                                                data:{
                                                                                    'mode':"SEARCH_GOODS",
                                                                                    'keyword':keyword
                                                                                },
                                                                                success:function(data){
                                                                                    let len=0;
                                                                                    if(data!=null) len=data.length;
                                                                                    if(len>1){
                                                                                        g_candidate_goods=data;
                                                                                        alert('여러개의 GOODS이 검색되었습니다');
                                                                                        // alert(g_candidate_goods.length);

                                                                                        NewWindow('pop_mro_goods_candidate.php?idx=<?=$i?>','pop_mro_goods_candidate',700,650,'No');


                                                                                    }
                                                                                    else if(len==1){
                                                                                        alert('GOODS이 검색되었습니다');

                                                                                        js_catch_goods_sale_state(data,data[0]["SALE_STATE"],'<?=$i?>',0);

                                                                                        $("#hdGoodsNo_<?=$i?>").val(data[0]['GOODS_NO']);
                                                                                        $("#hdBuyPrice_<?=$i?>").val(data[0]['BUY_PRICE']);
                                                                                        $("#hdGoodsCode_<?=$i?>").val(data[0]['GOODS_CODE']);


                                                                                    }
                                                                                    else{
                                                                                        alert('검색되지 않았습니다');
                                                                                    }

                                                                                },
                                                                                error:function(jqueryXHR,textStatus,errorThrown){
                                                                                    alert('fail of searching_goods');
                                                                                    console.log(jqueryXHR);
                                                                                    console.log(textStatus);
                                                                                    console.log(errorThrown);
                                                                                }
                                                                            });
                                                                        }//end of else
                                                                    }
                                                                });
                                                            });
                                                        </script>
                                                        <?
                                                            if($sGoods<>""){
                                                            ?>
                                                                <input type="button" value="변경" class="btnChange" onclick="js_change_sGoods('<?=$i?>')">
                                                            <?
                                                            }
                                                            else{
                                                            ?>
                                                                <input type="button" class="btnRegister" value="등록" onclick="js_register_sGoods('<?=$i?>')">
                                                            <?
                                                            }
                                                        ?>
                                                    </td><!--주문제품명-->
                                                    <td>
                                                        <!---옵션선택창을 집어넣어라-->

                                                            <input type="button" value="Option선택" id="btn_option_<?=$i?>" onclick="js_setting_option('<?=$arr_rs[$i]['SEQ']?>','<?=$goodsNo?>',this)">

                                                        &nbsp;
                                                        <?
                                                            //#scene_sticker
                                                            if($arr_rs[$i]["OPT_STICKER_NO"]>0){
                                                                makeSelectBoxOnNonghyupSticker($allStickerList, "", "mro_sticker_".$i, "mro_sticker_".$i, "", "스티커를 선택하세요", $arr_rs[$i]["OPT_STICKER_NO"], $arr_rs[$i]["SEQ"]);
                                                            }
                                                            else{
                                                                if($cntSticker>0){
                                                                    if($cntSticker>1){
                                                                        // making SelectBox with 3 sticker
                                                                        makeSelectBoxOnNonghyupSticker($stickerInfo, "", "mro_sticker_".$i, "mro_sticker_".$i, "", "스티커를 선택하세요", "", $arr_rs[$i]["SEQ"]);
                                                                    }
                                                                    else{
                                                                        //fixed sticker
    
    
    
                                                                        $stickerNo=$stickerInfo[0]["GOODS_NO"];
                                                                        // $opt_sticker_no=$;
                                                                        // echo "stickerNo : ".$stickerNo."<br>";
                                                                        if($arr_rs[$i]["OPT_STICKER_NO"]==0){
                                                                            $query="UPDATE TBL_TEMP_ORDER_MRO_CONVERSION SET OPT_STICKER_NO='".$stickerNo."' WHERE SEQ='".$arr_rs[$i]['SEQ']."'   ";
                                                                            if(!mysql_query($query, $conn)){
                                                                                echo "<script>alert('can t update sticker');</script>";
                                                                            }
    
                                                                        }
    
    
                                                                        makeSelectBoxOnNonghyupSticker($allStickerList, "", "mro_sticker_".$i, "mro_sticker_".$i, "", "스티커를 선택하세요", $stickerNo, $arr_rs[$i]["SEQ"]);
                                                                        // echo $stickerInfo[0]["GOODS_NAME"]." : ".$stickerInfo[0]["GOODS_NO"];
                                                                        // echo $tempStr;
                                                                    }
                                                                }
                                                                else{
                                                                        //making SelectBox by All Nonhyup Sticker
                                                                        makeSelectBoxOnNonghyupSticker($allStickerList, "", "mro_sticker_".$i, "mro_sticker_".$i, "", "스티커를 선택하세요", "", $arr_rs[$i]["SEQ"]);
                                                                }

                                                            }
                                                            if($sCATE_04=="단종"){
                                                            ?>
                                                                <script>
                                                                    $("#mro_sticker_"+"<?=$i?>").attr("disabled",true);
                                                                    $("#btn_option_"+"<?=$i?>").attr("disabled",true);
                                                                </script>
                                                            <?  
                                                            }

                                                            ?>
                                                            &nbsp;&nbsp;
                                                            <?
                                                            // makingSelectWrapMRO($conn, "mro_wrap_".$i, "mro_wrap_".$i);
                                                        ?>
                                                    </td><!--옵션(항목)-->
                                                </tr>
                                            <?
                                            }//end of for(arrCnt);
                                        ?>
                                        </tbody>
                                    </table>
                                </div><!--dvListTable-->	
                                <div class="space10px"></div>

                                <div class="dvButtonRight">
                                    <input type="button" class="btnSpecial" value="선택한 주문 등록" onclick="js_upload_order()">
 
                                    <input type="button" class="btnNormal" onclick="js_tmp_list()" value="LIST모드">

                                </div>

<!------------------------------------------------HIDDEN DATAS------------------------------------------------>
                                <input type="hidden" name="paramGoodsNo">
                                <input type="hidden" name="paramSGoodsNo">
                                <input type="hidden" name="paramSeq">
                                <input type="hidden" name="paramBuyPrice">
                                <input type="hidden" name="paramGoodsCode">
                                <input type="hidden" name="mode" value="<?=$mode?>">
                                <input type="hidden" name="tempFileName" value="">
                                <input type="hidden" name="hdArrCnt" value="<?=$cntArr?>">					
<!---------------------------------------------------------------------------------------------------->
                            </form>
                        </div><!--mainContent-->
                    </td><!--td_mainContent-->
                </tr>
            </tbody>
		</table>

    </body>
    <script>
        $(document).ready(function(){
            var fileTarget=$('#fileSinhyup');
            fileTarget.on('change', function(){
                //값이 변경되면
                if(window.FileReader){
                    var fileName=$(this)[0].files[0].name;
                    $('#dvFileSinhyup').html(fileName);
                }

            });
        });
    </script>
</html>