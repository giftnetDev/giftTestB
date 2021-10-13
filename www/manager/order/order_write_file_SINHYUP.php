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
	$menu_right = "OD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
    function findGoodsNoByGoodsCode($db, $goodsCode){
        $query= "   SELECT GOODS_NO
                    FROM    TBL_GOODS
                    WHERE   GOODS_CODE = '$goodsCode'
                    ";
        $result = mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('Error of Function_findGoodsNoByGoodsCode');</script>";
        }
        $rows=mysql_fetch_row($result);
        return $rows[0];
    }
    function selectStickerSINHYUPA($db, $selectID, $selected, $seq){
        $seq=$seq+0;
        $selected = $selected+0;

 
        $queryC ="   UPDATE TBL_TEMP_SINHYUP_ORDER
                    SET     OPT_STICKER_NO='".$selected."'
                    WHERE   SEQ     =   '".$seq."'
                    ";
        $resultC=mysql_query($queryC, $db);
        if(!$resultC){
            echo "<script>alert('OPT_STICKER_UPDATE_ERROR!);</script>";
            exit;
        }


        $query="SELECT	GOODS_NO, GOODS_NAME, GOODS_SUB_NAME
                FROM 	TBL_GOODS
                WHERE 	DEL_TF='N'
                AND 	USE_TF='Y'
                AND 	GOODS_CATE = '010316'
                ";
        $result=mysql_query($query, $db);
        $record=array();
        $cnt=0;
        if($result<>""){
            $cnt=mysql_num_rows($result);
        }
        else{
            echo "<script>alert('func.selectStickerSINHYUP() ERROR!');</script>";
            exit;
        }
        // echo "cnt : ".$cnt."<br>";
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
                $record[$i]['GOODS_NAME']=trim(SetStringFromDB($record[$i]['GOODS_NAME']));
            }
        }
        else{
            return "";
        }
        $selectBox="<SELECT name='".$selectID."' onchange='js_change_select_value(".$seq.",this);'><OPTION value='0'>스티커 선택</OPTION>";
        for($i=0; $i<$cnt; $i++){
            $selectBox.="<OPTION value='".$record[$i]['GOODS_NO']."' ";
            if($selected>0){
                if($selected==$record[$i]['GOODS_NO']){
                    $selectBox.=" selected ";
                }
            }
            $selectBox.=" >".$record[$i]['GOODS_NAME']."</OPTION>";
        }
        $selectBox.="</SELECT>";


        return $selectBox;
        
    }
    function getGoodsInfoByMartGoodsCode($db, $tempMartGoodsCode){
        $query="SELECT G.GOODS_NO, G.GOODS_CODE,G.GOODS_NAME, G.GOODS_SUB_NAME, G.CATE_03, G.DELIVERY_CNT_IN_BOX, G.TAX_TF
                FROM    TBL_GOODS_SINHYUP GS
                JOIN    TBL_GOODS G  ON G.GOODS_NO=GS.GOODS_NO
                WHERE   GS.SINHYUP_GOODS_NO='".$tempMartGoodsCode."'
                ";
        
        $result=mysql_query($query, $db);

        if(!$result){
            echo "<script>alert('func.getGoodsInfoByMartGoodsCode()_ERROR');</script>";
            exit;
        }
        $rows=mysql_fetch_row($result);

        return $rows;

    }
    /**
     * 2021-08-18 생성
     * TBL_TEMP_SINHYUP_ORDER 에 있는 GOODS_NO를 가져와서 상품의 정보를 체워넣는다.
     */
    function getGoodsInfoByGoodsNo($db, $goodsNo){
        $query="SELECT G.GOODS_NO, G.GOODS_CODE, G.GOODS_NAME, G.GOODS_SUB_NAME, G.CATE_03, G.DELIVERY_CNT_IN_BOX, G.TAX_TF
                FROM    TBL_GOODS G
                WHERE   G.GOODS_NO = '$goodsNo'
                ";
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('func.getGoodsInfoByGoodsNo()_ERROR');</script>";
        }
        $rows=mysql_fetch_row($result);

        return $rows;
    }
    function insertSINHYUPOrder($db, $arr){
        $query="INSERT INTO TBL_TEMP_SINHYUP_ORDER (
        TEMP_FILE_NO, TEMP_ORDER_NO,
        TEMP_ORDER_DATE ,TEMP_ORDER_MEM, TEMP_ORDER_ADDR, TEMP_RECIEVER_MEM, TEMP_RECIEVER_ZIP,
        TEMP_RECIEVER_ADDR, TEMP_RECIEVER_HPHONE, TEMP_RECIEVER_PHONE, TEMP_MART_GOODS_CODE, TEMP_GOODS_NAME,
        TEMP_GOODS_QTY,TEMP_BUY_PRICE,TEMP_SALE_PRICE,TEMP_TOTAL_OPTION_PRICE,TEMP_PRICE,
        TEMP_OPTION,TEMP_DELIVERY_PRICE,TEMP_TOTAL_BUY_PRICE,TEMP_TOTAL_SALE_PRICE, 
        TEMP_MEMO,REG_DATE,REG_ADMIN,GOODS_NO
        )

        VALUES(
            '".$arr["TEMP_FILE_NO"]."',
            '".$arr["TEMP_ORDER_NO"]."',
            '".$arr["TEMP_ORDER_DATE"]."',
            '".$arr["TEMP_ORDER_MEM"]."',
            '".$arr["TEMP_ORDER_ADDR"]."',
            '".$arr["TEMP_RECIEVER_MEM"]."',
            '".$arr["TEMP_RECIEVER_ZIP"]."',
            '".$arr["TEMP_RECIEVER_ADDR"]."',
            '".$arr["TEMP_RECIEVER_HPHONE"]."',
            '".$arr["TEMP_RECIEVER_PHONE"]."',
            '".$arr["TEMP_MART_GOODS_CODE"]."',
            '".$arr["TEMP_GOODS_NAME"]."',
            '".$arr["TEMP_GOODS_QTY"]."',
            '".$arr["TEMP_BUY_PRICE"]."',
            '".$arr["TEMP_SALE_PRICE"]."',
            '".$arr["TEMP_TOTAL_OPTION_PRICE"]."',
            '".$arr["TEMP_PRICE"]."',
            '".$arr["TEMP_OPTION"]."',
            '".$arr["TEMP_DELIVERY_PRICE"]."',
            '".$arr["TEMP_TOTAL_BUY_PRICE"]."',
            '".$arr["TEMP_TOTAL_SALE_PRICE"]."',
            '".$arr["TEMP_MEMO"]."',
            '".$arr["REG_DATE"]."',
            '".$arr["REG_ADMIN"]."',
            '".$arr["GOODS_NO"]."'
        ) ; "; 

        // echo $query."<br>";
        // exit;
        $result=mysql_query($query, $db);
        if(!$result){
        echo "<sciript>alert('TEMP_SINHYUP_INSERT_ERROR');</script>";
        }

    }//End of Function

    function getSupplyCompanyByGoodsNo($db, $goodsNo){
        $query="SELECT CATE_03
                FROM    TBL_GOODS
                WHERE GOODS_NO='".$goodsNo."'
                ";

        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('func.getSupplyCompanyByGoodsNo()_ERROR');</script>";
            exit;
        }
        else{
            $rows=mysql_fetch_row($result);
            return $rows;
        }
    }//end of func.getSupplyCompany......
    
    function setRegInfo($arr, $s_adm_no){
        $arr["REG_DATE"]=date("Y-m-d H:i:s", strtotime("0 month"));
        $arr["REG_ADMIN"]=$s_adm_no;
    }//End of Function

    function getSINHYUPOrderBySEQ($db, $seqs){
        $query="SELECT  TEMP_ORDER_DATE, TEMP_ORDER_MEM, TEMP_ORDER_ADDR, GOODS_NO,
                        TEMP_RECIEVER_MEM, TEMP_RECIEVER_ZIP, TEMP_RECIEVER_ADDR, TEMP_RECIEVER_HPHONE, TEMP_RECIEVER_PHONE,
                        TEMP_MART_GOODS_CODE, TEMP_GOODS_NAME, TEMP_GOODS_QTY, TEMP_BUY_PRICE, TEMP_SALE_PRICE, TEMP_TOTAL_OPTION_PRICE,
                        TEMP_PRICE, TEMP_OPTION, TEMP_DELIVERY_PRICE, TEMP_TOTAL_BUY_PRICE, TEMP_TOTAL_SALE_PRICE, TEMP_MEMO,
                        OPT_STICKER_NO, OPT_WRAP_NO, OPT_STICKER_MSG, OPT_PRINT_MSG, CP_ORDER_NO, OPT_MEMO, OPT_REQUEST_MEMO, OPT_SUPPORT_MEMO, OPT_OUTSTOCK_DATE, OPT_SALE_TYPE,
                        DELIVERY_TYPE, DELIVERY_CP, SA_DELIVERY_PRICE, SENDER_NM, SENDER_PHONE, BULK_TF,
                        REG_ORDER_TF, REG_DATE, REG_ADMIN
                FROM    TBL_TEMP_SINHYUP_ORDER
                WHERE   SEQ IN (".$seqs.")
                ";

        // echo $query."<br>";
        // exit;
        $result=mysql_query($query, $db);
        $record=array();
        $cnt=0;
        if(!$result){
            echo "<script>alert('func.getSINHYUPOrderBySEQ() ERROR');</script>";
            exit;
        }
        else{
            $cnt=mysql_num_rows($result);
        }
        for($i=0; $i<$cnt; $i++){
            $record[$i]=mysql_fetch_assoc($result);
        }
        return $record;
    }//end of func.getSINHYUPOrder....

    function listOrderSINHYUP($db, $tempFileNo){
        $query="SELECT 	    TSO.SEQ, TSO.TEMP_FILE_NO, TSO.REG_ORDER_TF, TSO.GOODS_NO,
                            TSO.TEMP_ORDER_NO, TSO.TEMP_ORDER_DATE, TSO.TEMP_ORDER_MEM, TSO.TEMP_ORDER_ADDR,
                            TSO.TEMP_RECIEVER_MEM, TSO.TEMP_RECIEVER_ZIP, TSO.TEMP_RECIEVER_ADDR, TSO.TEMP_RECIEVER_HPHONE, TSO.TEMP_RECIEVER_PHONE, 
                            TSO.TEMP_MART_GOODS_CODE, TSO.TEMP_GOODS_NAME, TSO.TEMP_GOODS_QTY, TSO.TEMP_BUY_PRICE, TSO.TEMP_SALE_PRICE, 
                            TSO.TEMP_TOTAL_OPTION_PRICE, TSO.TEMP_PRICE, TSO.TEMP_OPTION, TSO.TEMP_DELIVERY_PRICE, TSO.TEMP_TOTAL_BUY_PRICE, 
                            TSO.TEMP_TOTAL_SALE_PRICE, TSO.TEMP_MEMO,TSO.OPT_STICKER_NO
                FROM        TBL_TEMP_SINHYUP_ORDER TSO
                WHERE       TEMP_FILE_NO = '".$tempFileNo."'
                AND         REG_ORDER_TF='N'
            ";

        // echo "$query<br>";
        // exit;

        $cnt=0;
        $record=array();
        $result=mysql_query($query, $db);
        if($result<>""){
            $cnt = mysql_num_rows($result);
        }
        else{
            echo "<script>alert('func.listOrderSINHYUP() ERROR');</script>";
            exit;
        }
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            return $record;
        }
    }

    function listSINHYUPOrder($db, $tempFileNo){
        $query="SELECT 	    TSO.SEQ, TSO.TEMP_FILE_NO, TSO.REG_ORDER_TF,
                            TSO.TEMP_ORDER_NO, TSO.TEMP_ORDER_DATE, TSO.TEMP_ORDER_MEM, TSO.TEMP_ORDER_ADDR, GS.GOODS_NO,
                            TSO.TEMP_RECIEVER_MEM, TSO.TEMP_RECIEVER_ZIP, TSO.TEMP_RECIEVER_ADDR, TSO.TEMP_RECIEVER_HPHONE, TSO.TEMP_RECIEVER_PHONE, 
                            TSO.TEMP_MART_GOODS_CODE, TSO.TEMP_GOODS_NAME, TSO.TEMP_GOODS_QTY, TSO.TEMP_BUY_PRICE, TSO.TEMP_SALE_PRICE, 
                            TSO.TEMP_TOTAL_OPTION_PRICE, TSO.TEMP_PRICE, TSO.TEMP_OPTION, TSO.TEMP_DELIVERY_PRICE, TSO.TEMP_TOTAL_BUY_PRICE, 
                            TSO.TEMP_TOTAL_SALE_PRICE, TSO.TEMP_MEMO, IFNULL(GS.SINHYUP_GOODS_NO,0) AS S_GOODS_NO
                FROM        TBL_TEMP_SINHYUP_ORDER TSO
                LEFT JOIN   TBL_GOODS_SINHYUP GS ON TSO.TEMP_MART_GOODS_CODE= GS.SINHYUP_GOODS_NO
                WHERE       TEMP_FILE_NO = '".$tempFileNo."'
                AND         REG_ORDER_TF='N'
                ORDER BY    TEMP_ORDER_NO

                        ";

        // echo "$query<br>";
        // exit;
        $cnt=0;
        $record=array();
        $result=mysql_query($query, $db);
        if($result<>""){
            $cnt = mysql_num_rows($result);
        }
        else{
            echo "<script>alert('error of listSINHYUPOrder_function');</script>";
            exit;
        }
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            return $record;
        }

        

    }//end of Function;
    function getGoodsBuyPriceByGoodsNo($db, $goodsNo){
        $query="SELECT  G.BUY_PRICE
                FROM    TBL_GOODS G
                WHERE   G.GOODS_NO='$goodsNo'
                ";
        
        $result=mysql_query($query, $db);
        if($result<>""){
            $rows=mysql_fetch_row($result);
            return $rows[0];
        }
        
    }
    function findGoodsNameByGoodsNo($db, $goodsNo){
        $query="SELECT G.GOODS_NAME,G.GOODS_CODE
                FROM    TBL_GOODS G
                WHERE   G.GOODS_NO='".$goodsNo."'
                AND G.USE_TF='Y'
                AND G.DEL_TF='N'
                ";

        $record=array();
        $cnt=0;
        $result=mysql_query($query, $db);
        if($result){
            $cnt=mysql_num_rows($result);
        }
        if($cnt<1){
            return $record;
        }
        $rows=mysql_fetch_row($result);
        return $rows;
    }//end of function

    function autoRegistrationSGoodsByGoodsCode($db, $goodsCode, $sGoodsNo){
        $query="SELECT GOODS_NO
                FROM TBL_GOODS
                WHERE GOODS_CODE='$goodsCode'
        ";
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('func.autoRegistration..()_query Error');</script>";
            exit;
        }
        $rows=mysql_fetch_row($result);
        $goodsNo=$rows[0];
        // echo "rows[0] : ".$goodsNo."<br>";

        if($goodsNo==""){
            echo"No goodsNo<br>";
            return "";
        }
        $query2="INSERT INTO TBL_GOODS_SINHYUP(SINHYUP_GOODS_NO, GOODS_NO)
                VALUES('".$sGoodsNo."', '".$goodsNo."') ";

        $result2=mysql_query($query2, $db);
        if(!$result2){
            echo "<script>alert('func.autoRegistration..()_query2 Error');</script>";
            exit;
        }
        return $goodsNo;

    }

    function findGoodsNoBySGoodsNo($db,$sGoodsNo){
        $query="SELECT  GOODS_NO
                FROM    TBL_GOODS_SINHYUP
                WHERE SINHYUP_GOODS_NO='".$sGoodsNo."'
                ";
        // echo $query."<br>";
        // exit;
        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('latestRegisteredSINHYUPOrder() Error!');</script>";
            exit;
        }
        $rows=mysql_fetch_row($result);
        return $rows[0];

    }

    function latestRegisteredSINHYUPOrder($db){
        $query="SELECT MAX(TEMP_FILE_NO)
                FROM TBL_TEMP_SINHYUP_ORDER";
        
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
    function updateSINHYUPOrder($db, $seq){
        $query="UPDATE TBL_TEMP_SINHYUP_ORDER
                SET
                    REG_ORDER_TF='Y'
                WHERE SEQ IN (".$seq.")
                AND GOODS_NO>0 ";

        // echo $query."<br>";
        // exit;
        $result=mysql_query($query, $db);

        if(!$result){
            echo "<script>alert('func.updateSINHYUPOrder()_ERROR');</script>";
            exit;
        }

    }
    function extractGoodsCodeFromSINHYUPOrderGoods($str){
        // echo "|goodsName : ".$str."<br>";

        $list = new LinkedList();
        $cnt=strlen($str);
        $max=0;
        $start=-1;
        for($i=0; $i<$cnt; $i++){
            $list->insertAtBack(ord(substr($str,$i,1)));
        }
        // echo "size of List :".$list->sizeOfList()."<br>";
        // echo "cnt : $cnt<br>";
        $curNode=$list->getHead();
        $i=0;
        while($curNode!=null){
            if($curNode->getData()>=48 && $curNode->getData()<=57 && $max!=3){//숫자라면
                if($start<0){
                    $start=$i;
                }
                $max++;
                // echo $max."<br>";

            }
            else{//숫자가 아니라면
                if($curNode->getData()==45 && $max==3){
                    $max++;
                    // echo $max."<br>";
                }
                else{
                    $max=0;
                    $start=-1;
                }

            }
            if($max==10){

                $goodsCode=substr($str,$start,$max);
                // echo "complete! goodsCode : ".$goodsCode."<br>";
                $list=null;
                return $goodsCode;
            }
            $curNode=$curNode->getNext();
            $i++;

        }
        $goodsCode="";
        $list=null;
        return $goodsCode;
    }//end of func.extractGoodsCodeFromSINHYU....

    function selectStickerFromOption($db, $optStr, $goodsNo){
        if($goodsNo<1){
            return 0;
        }
        $cnt=0;
        $n=-1;
        $idx=0;
        // echo "OPT: ".$optStr."<br>";
        // $value=strpos($optStr,"타입");
        if(strpos($optStr, "타입")===false){
            // echo $value."<br>";
            // echo "0번탈락<br>";
            return 0;
        }
        $targets=array("A","B","C","D","E","F","G","H","I","N","O");
        foreach($targets as $t){
            if(strpos($optStr, $t)!==false){
                $cnt++;
                $n=$idx;
            }
            $idx++;
        }
        if($cnt!=1){
            // echo "cnt : ".$cnt."<br>";
            // echo "1번탈락<br>";
            return 0;
        }
        //여기까지 컨트롤이 넘어왔다면 optionString에서 스티커를 발견했다
        //즉 TYPE을 찾아내었다고 가정한다

        //해당 상품에 달려있는 스티커 사이즈 찾아내는 코드
        $query="SELECT  DCODE
                FROM    TBL_GOODS_EXTRA
                WHERE   GOODS_NO='".$goodsNo."'
                AND     PCODE   =   'GOODS_STICKER_SIZE'
                ";
        $result=mysql_query($query, $db);
        if(!$result){

            return 0;
        }

        $rows=mysql_fetch_row($result);
        

        $query1="SELECT  G.GOODS_NO
                FROM    TBL_GOODS   G
                WHERE   G.GOODS_CATE = '010316'
                AND     G.USE_TF='Y'
                AND     G.DEL_TF='N'
                AND     G.GOODS_NAME LIKE '%".$targets[$n]."%'
                AND     G.GOODS_NAME LIKE '%".$rows[0]."%'
                ";
        
        // echo $query1."<br>";
        // exit;

        $result1=mysql_query($query1, $db);
        if($result1<>""){
            $rows=mysql_fetch_row($result1);
            return $rows[0];
        }
        else{
            return 0;
        }

    }//end of function


?>
<?


    // echo "tempFileName : $tempFileName<br>";
    if($mode=="UPLOAD_ORDER"){



        $row_cnt=count($chk_order);
        $seqStr="";

        for($k=0; $k< $row_cnt; $k++){
            // echo $chk_order[$k]."<br>";
            $seqStr.=$chk_order[$k].",";
        }
        $seqStr=trim($seqStr);
        $seqStr=trim($seqStr,",");

        $SINHYUPOrders=getSINHYUPOrderBySEQ($conn, $seqStr);


        $cntS=sizeof($SINHYUPOrders);


        $on_uid="";//임시
        $new_mem_no=0;//임시
        $sinhyup_no=5597;//신협(CU);
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
        $sinhyup_order_addr=SetStringToDB("대전광역시 서구 한밭대로 745 (문산동)");//신협 관련된 주문_주소는 모두 이 주소로 나오도록 페치

        for($i=0; $i<$cntS; $i++){

            if($SINHYUPOrders[$i]["GOODS_NO"]<1){
                echo "<script>alert('상품등록이 되어있지 않습니다');</script>";
                continue;
            } 


            $reserveNo=getReservNo($conn, "EN");

            // echo "TEMP_MEMO : ".$SINHYUPOrders['TEMP_MEMO']."<BR>";


            


            insertOrder($conn, $on_uid, $reserveNo, $new_mem_no,
                        $sinhyup_no, $SINHYUPOrders[$i]['TEMP_ORDER_MEM'], $o_zipcode, $sinhyup_order_addr, $o_addr2,
                        $o_phone, $o_hphone, $o_email, $SINHYUPOrders[$i]['TEMP_RECIEVER_MEM'], $SINHYUPOrders[$i]['TEMP_RECIEVER_ZIP'],
                        $SINHYUPOrders[$i]['TEMP_RECIEVER_ADDR'], $r_addr2, $SINHYUPOrders[$i]['TEMP_RECIEVER_PHONE'], $SINHYUPOrders[$i]['TEMP_RECIEVER_HPHONE'], $r_email, $SINHYUPOrders[$i]['TEMP_MEMO'],
                        $SINHYUPOrders[$i]['BULK_TF'], $sale_adm_no, $order_state, $SINHYUPOrders[$i]['TEMP_TOTAL_BUY_PRICE'], $SINHYUPOrders[$i]['TEMP_TOTAL_BUY_PRICE'],
                        $SINHYUPOrders[$i]['TEMP_TOTAL_SALE_PRICE'], $total_extra_price, $SINHYUPOrders[$i]['TEMP_DELIVERY_PRICE'], $SINHYUPOrders[$i]['SA_DELIVERY_PRICE'],
                        $total_discount_price, $SINHYUPOrders[$i]['TEMP_GOODS_QTY'], $pay_type, $SINHYUPOrders['DELIVERY_TYPE'], 'Y', $s_adm_no);


            $goodsInfo=getGoodsInfoByGoodsNo($conn, $SINHYUPOrders[$i]["GOODS_NO"]);

            $GOODS_NO               =   trim($goodsInfo[0]);
            $GOODS_CODE             =   trim($goodsInfo[1]);
            $GOODS_NAME             =   trim(SetStringFromDB($goodsInfo[2]));
            $GOODS_SUB_NAME         =   trim(SetStringFromDB($goodsInfo[3]));
            $SUPPLY_CP_NO           =   trim($goodsInfo[4]);
            $DELIVERY_CNT_IN_BOX    =   trim($goodsInfo[5]);
            $TAX_TF                 =   trim($goodsInfo[6]);

            $memos=array('opt_request_memo' => $SINHYUPOrders[$i]["OPT_REQUEST_MEMO"], 'opt_support_memo' => $SINHYUPOrders[$i]["OPT_SUPPORT_MEMO"]);

            
            $C_CATE_02 =""; //계산서 번호
            $C_CATE_03 =""; //계산서 종류 ('CF001' => '전자계산서', 'CF002' => '전자세금계산서', 'CF004' => '카드결제')
            $C_CATE_04 =""; //어떤 애인지 잘 모르겠다.....


            /**
             * // CATE_01 =""; //샘플, 증정, 추가, 일반("")
             * TBL_ORDER_GOODS.PRICE            : 매입가        ->TEMP_BUY_PRICE
             * TBL_ORDER_GODOS.BUY_PRICE        : 상품가격      ->TEMP_BUY_PRICE
             * TBL_ORDER_GOODS.SALE_PRICE       : 판매가        ->TEMP_SALE_PRICE
             * TBL_ORDER_GOODS.EXTRA_PRICE      : 관리비        ->
             * TBL_ORDER_GOODS.DELIVERY_PRICE   : 운송비
             * TBL_ORDER_GOODS.SA_DELIVERY_PRICE: 운송관리비
             * 
             * 
             */

            if($SINHYUPOrders[$i]["SENDER_NM"]==""){
                $SINHYUPOrders[$i]["SENDER_NM"]="CU몰 기프트넷";

            }
            if($SINHYUPOrders[$i]["SENDER_PHONE"]==""){
                $SINHYUPOrders[$i]["SENDER_PHONE"]="031-527-6812";
            }
            if($SINHYUPOrders[$i]["OPT_OUTSTOCK_DATE"]=="0000-00-00 00:00:00"){
                $week=date("w");
                if($week==5){//오늘이 금요일일 경우
                    $SINHYUPOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+3 day"));	
                } 
                else if($week==6){//오늘이 토요일일 경우
                    $SINHYUPOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+2 day"));
                }
                else{//그 외
                    $SINHYUPOrders[$i]["OPT_OUTSTOCK_DATE"]=date("Y-m-d",strtotime("+1 day"));
                }
                
            }


            insertOrderGoods(   $conn, $on_uid, $reserveNo, $cp_order_no, $SUPPLY_CP_NO, 
                                $new_mem_no, $i, $GOODS_NO, $GOODS_CODE, $GOODS_NAME,
                                $GOODS_SUB_NAME, $SINHYUPOrders[$i]["TEMP_GOODS_QTY"], $SINHYUPOrders[$i]["OPT_STICKER_NO"], $SINHYUPOrders[$i]["OPT_STICKER_MSG"], $SINHYUPOrders[$i]["OPT_STICKER_NO"],
                                $DELIVERY_CNT_IN_BOX, $SINHYUPOrders[$i]["OPT_WRAP_NO"], $SINHYUPOrders[$i]["OPT_PRINT_MSG"], $SINHYUPOrders[$i]["OPT_OUTSTOCK_DATE"], $SINHYUPOrders[$i]["OPT_MEMO"], 
                                $memos, $SINHYUPOrders[$i]["DELIVERY_TYPE"], $SINHYUPOrders[$i]["DELIVERY_CP"], $SINHYUPOrders[$i]["SENDER_NM"], $SINHYUPOrders[$i]["SENDER_PHONE"],
                                $SINHYUPOrders[$i]["OPT_SALE_TYPE"], $C_CATE_02, $C_CATE_03, $C_CATE_04, $SINHYUPOrders[$i]["TEMP_PRICE"],
                                $SINHYUPOrders[$i]["TEMP_PRICE"], $SINHYUPOrders[$i]["TEMP_BUY_PRICE"], $t_extra_price, $t_delivery_price, $SINHYUPOrders[$i]["SA_DELIVERY_PRICE"],
                                $t_discount_price, $t_sticker_price, $t_print_price, $t_sale_susu, $t_labor_price,
                                $t_order_price, $TAX_TF, $order_state, "Y", $s_adm_no
                            );


        }
        updateSINHYUPOrder($conn, $seqStr);


        $mode="LIST";



    }
    if($mode=="CHANGE_SGOODS"){
        $sGoodsNo   = $_POST['paramSGoodsNo'];
        $goodsNo    = $_POST['paramGoodsNo'];
        $buyPrice   = $_POST['paramBuyPrice'];
        $seq        = $_POST['paramSeq'];

        $query = "  UPDATE TBL_TEMP_SINHYUP_ORDER
                    SET     GOODS_NO    =   '$goodsNo'
                    WHERE   SEQ         =   '$seq'
                ";
        
        // echo "$query<br>";
        // exit;
        
        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('CHANGE_GOODS_ERROR11');</script>";
        }
        // exit;

        /* Dispose at 2021-08-18
            // $query="UPDATE TBL_GOODS_SINHYUP
            // SET GOODS_NO='".$goodsNo."'
            // WHERE SINHYUP_GOODS_NO='".$sGoodsNo."' ";

            // $result=mysql_query($query, $conn);
            // if(!$result){
            //     echo "<script>alert('CHANGE_SGOODS Error!');</script>";
            //     exit;
            // }

            // $query2="UPDATE TBL_TEMP_SINHYUP_ORDER
            // SET
            //     GOODS_NO='".$goodsNo."'
            //     ,TEMP_PRICE='".$buyPrice."'
            // WHERE TEMP_MART_GOODS_CODE='".$sGoodsNo."'
            //     ";

            // $result2=mysql_query($query2, $conn);
            // if(!$result2){

            //     echo "<script>alert('CHANGE_SGOODS_UPDATE<query2> ERROR!');</script>";
            //     echo $query2."<br>";
            //     exit;
            // }
        */

        $mode="LIST";
    }
    if($mode=="REGISTER_SGOODS"){
        
        $sGoodsNo   =   $_POST['paramSGoodsNo'];
        $goodsNo    =   $_POST['paramGoodsNo'];
        $buyPrice   =   $_POST['paramBuyPrice'];

        // echo "sGoodsNo : ".$sGoodsNo."<br>";
        // echo "goodsNo  : ".$goodsNo."<br>";


        $query="INSERT INTO TBL_GOODS_SINHYUP(SINHYUP_GOODS_NO, GOODS_NO)
                VALUES('".$sGoodsNo."', '".$goodsNo."') ";

        // echo $query."<br>";
        // exit;

        // $result=mysql_query($query, $conn);
        // if(!$result){
        //     echo "<script>alert('REGISTER_SGOODS_INSERT<query> Error!');</script>";
        //     exit;
        // }

        $query2="UPDATE TBL_TEMP_SINHYUP_ORDER
                SET
                    GOODS_NO='".$goodsNo."'
                    , TEMP_PRICE='".$buyPrice."'
                WHERE TEMP_MART_GOODS_CODE='".$sGoodsNo."'
                    ";

        $result2=mysql_query($query2, $conn);
        if(!$result2){
            echo "<script>alert('REGISTER_SGOODS_UPDATE<query2> ERROR!');</script>";
            exit;
        }

        $mode="LIST";
                
    }

    if($mode=="FU"){
        // echo "File Update"."<br>";
        $fileDir=$_SERVER[DOCUMENT_ROOT]."/upload_data/tempExcel/";

 

        $fileName=upload($_FILES[fileName], $fileDir, 10000, array('xls'));

        $fileInfo=$fileDir.$fileName;

        // echo $fileInfo."<br>";
        require_once "../../_PHPExcel/Classes/PHPExcel.php";
        require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
        
        $objPHPExcel= new PHPExcel();

        
        try{
            $objPHPExcel=PHPExcel_IOFactory::load($fileInfo);

            $curSheet=$objPHPExcel->getActiveSheet();

            $lastRow=$curSheet->getHighestRow();

            $tempFileName=date("Y-m-d H:i:s", strtotime("0 month"));

            for($i=2; $i<=$lastRow; $i++){
                $sFlag=0;//신협플레그 -> 뜻 :??
                $arr=array();
                $arr['TEMP_ORDER_NO'] =             SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("A".$i)->getValue())));
                $arr['TEMP_ORDER_DATE']=            SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("B".$i)->getValue())));
                $arr['TEMP_ORDER_MEM']=             SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("E".$i)->getValue())));
                $arr['TEMP_ORDER_ADDR']=            SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("F".$i)->getValue())));
                $arr['TEMP_RECIEVER_MEM']=          SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("G".$i)->getValue())));
                $arr['TEMP_RECIEVER_ZIP']=          SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("H".$i)->getValue())));
                $arr['TEMP_RECIEVER_ADDR']=         SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("I".$i)->getValue())));
                $arr['TEMP_RECIEVER_HPHONE']=       SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("J".$i)->getValue())));
                $arr['TEMP_RECIEVER_PHONE']=        SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("K".$i)->getValue())));
                $arr['TEMP_MART_GOODS_CODE']=       SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("N".$i)->getValue())));
                $arr['TEMP_GOODS_NAME']=            SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("O".$i)->getValue())));
                $arr['TEMP_GOODS_QTY']=             SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("P".$i)->getValue())));
                $arr['TEMP_BUY_PRICE']=             SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("Q".$i)->getValue())));
                $arr['TEMP_SALE_PRICE']=            SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("R".$i)->getValue())));
                $arr['TEMP_TOTAL_OPTION_PRICE']=    SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("S".$i)->getValue())));
                // $arr['TEMP_PRICE'] =                SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("T".$i)->getValue())));
                $arr['TEMP_OPTION'] =               SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("U".$i)->getValue())));
                $arr['TEMP_DELIVERY']  =            SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("V".$i)->getValue())));
                $arr['TEMP_TOTAL_BUY_PRICE']   =    SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("X".$i)->getValue())));
                $arr['TEMP_TOTAL_SALE_PRICE']  =    SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("Y".$i)->getValue())));
                $arr['TEMP_MEMO']              =    SetStringToDB(trim(iconv("UTF-8","EUC-KR",$curSheet->getCell("AC".$i)->getValue())));

                $arr['TEMP_FILE_NO']= $tempFileName;    

            // Using 'GOODS_CODE' in TEMP_GODS_NAME get our goods's information insetead of getting SINHYUP's TEMP_MART_GOODS_CODE  <2021-08-18 required by YUNMI JYO>
                
                //extracting GOODS_CODE at TEMP_GOODS_NAME

                $goodsCode=extractGoodsCodeFromSINHYUPOrderGoods($arr['TEMP_GOODS_NAME']);
                if($goodsCode<>""){
                    $tGoodsNo=findGoodsNoByGoodsCode($conn, $goodsCode);
                    $arr['GOODS_NO']=$tGoodsNo;
                }
                // preg_match_all('/[0-9]{3]-[0-9]{6}');

 
                /* Dispose at 2021-08-18
                    // $tGoodsNo=findGoodsNoBySGoodsNo($conn,$arr['TEMP_MART_GOODS_CODE']);    //TBL_GOODS_SINHYUP에서 해당 TEMP_MART_GOODS_CODE가 등록되어있는지 확인후 
                    //                                                                         //등록되어있으면 TBL_TEMP_SINHYUP_ORDER의 GOODS_NO에 등록한다.
                    // if($tGoodsNo<>""){
                    //     // echo "1<br>";
                    //     $arr['GOODS_NO']=$tGoodsNo;
                    //     $sFlag=1;
                    // }
                */

                /* Dispose at 2021-08-18

                    // if($sFlag<1){     
                    //     //  
                    //     //    TBL_GOODS_SINHYUP에 해당 TEMP_MART_GOODS_CODE가 등록되어있지 않다면
                    //     //    현재 들어온 'TEMP_GOODS_NAME에서 GOODS_CODE를 추출해낸다.
                    //     
                    //     $goodsCode=extractGoodsCodeFromSINHYUPOrderGoods($arr['TEMP_GOODS_NAME']);
                    //     if($goodsCode<>""){//추출에 성공했다면
                    //         // echo "goodsCode : ".$goodsCode."<br>";
                    //         $tGoodsNo=autoRegistrationSGoodsByGoodsCode($conn, $goodsCode, $arr['TEMP_MART_GOODS_CODE']);//TBL_GOODS에서 해당 상품의 GOODS_NO를 찾아온뒤 TBL_GOODS_SINHYUP에 등록한다.
                    //         if($tGoodsNo<>""){
                    //             $arr['GOODS_NO']=$tGoodsNo;
                    //         }
                    //     }

                        

                    // }

                */


                if($tGoodsNo<>""){//위의 상품데이터가 있는지의 여부와 상관없이 "상품구매가"는 변할수 있으므로 언제나 가져와준다.
                    $arr['TEMP_PRICE']=getGoodsBuyPriceByGoodsNo($conn,$arr['GOODS_NO']);
                }
                setRegInfo($arr, $s_adm_no);
                insertSINHYUPOrder($conn, $arr);

            }//end of for(Excel's Data Rows Count)


            
        }//end of try()
        catch(exception $e){
            echo $e;
        }


        $mode="LIST";
    }//end of if(mode=FU)

    if($mode=="LIST"){
        // echo "now mode is LIST mode<br>";
        if($tempFileName==""){
            $tempFileName=latestRegisteredSINHYUPOrder($conn);
        }
        $arr_rs=listOrderSINHYUP($conn, $tempFileName);
        $cntArr=sizeof($arr_rs);
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

            function js_change_select_value(seq, select){
                var stickerNo=select.value;
                // alert(stickerNo);
                // return ;
                $.ajax({
                    url:"../ajax/ajax_SINHYUP.php",
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
                                frm['chk_order[]'][i].checked=true;
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

                //2021_09_10 : 선택된 주문이 없을시 JS에서 차단하도록
                if($("input:checkbox[name='chk_order[]']").length<1){
                    alert("선택된 주문이 없습니다");
                    return ;
                }
                
                var cntArr=$("input[name='hdArrCnt']").val();
                // alert(cntArr)
                if( cntArr<1){
                    alert('리스트가 없습니다');
                    return;
                }
                var frm = document.frm;
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
                frm.mode.value="FU";//File Upload
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
                let url="pop_SINHYUP_option.php?seq="+index+"&goodsNo="+goodsNo;
                let wndObj=NewWindow(url,"pop_SINHYUP_option",700,800,'yes');            

            }

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
                            <form name='frm' enctype="multipart/form-data" >
<!---------------------------------------------------------------------------------------------------->

                                <div></div><!--네비게이션-->
                                <div>
                                    <h2 class="title">주문등록 - 신협</h2>
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
                                            <th>신협주문파일</th>
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
                                            <col width="3%"><!--수량-->
                                            <col width="5%"><!--매입단가-->
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
                                                <th>수량</th><!--수량-->
                                                <th>매입단가</th><!--매입단가-->
                                                <th>구매단가</th><!--구입단가-->
                                                <th>옵션(항목)</th><!--옵션(항목)-->
                                                <th>배송특이사항</th><!--배송특이사항-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?
                                            
                                            for($i=0; $i<$cntArr; $i++){

                                                $t_GoodsNo=$arr_rs[$i]['GOODS_NO'];
                                              

                                                //sticker
                                                //TEMP_OPTION
                                                // echo "TEST!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
                                                if($arr_rs[$i]['OPT_STICKER_NO']<1){
                                                    $stickerNo=selectStickerFromOption($conn, $arr_rs[$i]['TEMP_OPTION'], $arr_rs[$i]['GOODS_NO']);
                                                }
                                                else{
                                                    $stickerNo=$arr_rs[$i]['OPT_STICKER_NO'];
                                                }

                                                // echo $stickerNo."<br>";

                                                
                                                if($t_GoodsNo>0){ //when registering Excel File, if Our Goods is Identified
                                                    $goodsInfo=findGoodsNameByGoodsNo($conn, $t_GoodsNo);
                                                    echo "$t_GoodsNo<br>";

                                                    $sGoodsName=$goodsInfo[0];
                                                    $sGoodsCode=$goodsInfo[1];

                                                    $sGoods="[".$sGoodsCode."]".$sGoodsName;

                                                }
                                                else{
                                                    $sGoods="";
                                                }
                                        ?>
                                                <tr>
                                                    <td rowspan="2">
                                                    <?
                                                        if($arr_rs[$i]['REG_ORDER_TF']=='N'){
                                                        ?>
                                                            <input type="checkbox" name="chk_order[]" value="<?=$arr_rs[$i]['SEQ']?>">
                                                            <input type="hidden" id="hdSeq_<?=$i?>" value="<?=$arr_rs[$i]['SEQ']?>">
                                                        <?
                                                        }
                                                    ?>
                                                    </td>
                                                    <td rowspan="2"><?=$arr_rs[$i]['TEMP_ORDER_MEM']?></td><!--주문자명-->
                                                    <td rowspan="2"><?=$arr_rs[$i]['TEMP_ORDER_ADDR']?></td><!--수취인명-->
                                                    <td class="tdNoBar">
                                                        <?=$arr_rs[$i]['TEMP_GOODS_NAME']?>                                
                                                    </td><!--주문제품명-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['TEMP_GOODS_QTY'])?></td><!--수량-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['TEMP_BUY_PRICE'])?>원</td><!--매입단가-->
                                                    <td rowspan="2" class="tdNumber"><?=number_format($arr_rs[$i]['TEMP_PRICE'])?>원</td><!--구매단가-->
                                                    <td class="tdNoBar"><?=$arr_rs[$i]['TEMP_OPTION']?></td><!--옵션(항목)-->
                                                    <td rowspan="2"><?=$arr_rs[$i]['TEMP_MEMO']?></td><!--배송특이사항-->
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <?
                                                        if($arr_rs[$i]['GOODS_NO']>0){
                                                            $goodsTF="existence";
                                                        }
                                                        else{
                                                            $goodsTF="empty";
                                                        }
                                                    ?>

                                                        <input type="text" id="txtGoodsName_<?=$i?>" class="<?=$goodsTF?>" value="<?=$sGoods?>" placeholder="상품(명/코드) 입력후 엔터를 누르세요" size="70">
                                                        <input type="hidden" id="hdSGoodsNo_<?=$i?>" value="<?=$arr_rs[$i]['TEMP_MART_GOODS_CODE']?>">
                                                        <input type="hidden" id="hdGoodsNo_<?=$i?>" value="<?=$arr_rs[$i]['GOODS_NO']?>">
                                                        <input type="hidden" id="hdBuyPrice_<?=$i?>" value="<?=$arr_rs[$i]['TEMP_PRICE']?>"><!--우리회사가 매입한 단가-->
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
                                                                                url:"../ajax/ajax_SINHYUP.php",
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

                                                                                        NewWindow('pop_sinhyup_goods_candidate.php?idx=<?=$i?>','pop_sinhyup_goods_candidate',700,650,'No');


                                                                                    }
                                                                                    else if(len==1){
                                                                                        alert('GOODS이 검색되었습니다');

                                                                                        
                                                                                        let goods="["+data[0]['GOODS_CODE']+"]"+data[0]['GOODS_NAME'];
                                                                                        
                                                                                        $("#txtGoodsName_<?=$i?>").val(goods);
                                                                                        $("#hdGoodsNo_<?=$i?>").val(data[0]['GOODS_NO']);
                                                                                        $("#hdBuyPrice_<?=$i?>").val(data[0]['BUY_PRICE']);

                                                                                    }
                                                                                    else{
                                                                                        alert('검색되지 않았습니다');
                                                                                    }

                                                                                },
                                                                                error:function(jqueryXHR,textStatus,errorThrown){
                                                                                    alert('fail of test');
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
                                                        <input type="button" value="Option선택" onclick="js_setting_option('<?=$arr_rs[$i]['SEQ']?>','<?=$arr_rs[$i]['GOODS_NO']?>',this)">
                                                        &nbsp;
                                                        <?if($arr_rs[$i]['GOODS_NO']>0){?><?=selectStickerSINHYUPA($conn, "select_sticker_".$i, $stickerNo, $arr_rs[$i]['SEQ']);?><?}?>


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