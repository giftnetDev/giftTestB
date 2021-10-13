<?
    require "_common/home_pre_setting.php";
    require "./_classes/biz/order/order.php";
    require "./_classes/biz/confirm/confirm.php";


    $curPage=$_POST['curPage'];
    if($curPage==""){
        $curPage=1;
    }

    // echo "curPage : ".$curPage."<br>";

    /**
     *memberNo별 order_state당 cnt를 구하는 함수
     */


     /**
      * 해당 ORDER_GOODS_NO관련 ORDER_GOODS REC 가져오기
      */
    function getOrderGoodsInfo($db, $order_goods_no) {

		$query = "SELECT C.ON_UID, C.ORDER_GOODS_NO, C.RESERVE_NO, C.CP_ORDER_NO, C.BUY_CP_NO, C.MEM_NO, C.ORDER_SEQ, 
						 C.GOODS_NO, G.GOODS_CODE, C.GOODS_NAME, C.GOODS_SUB_NAME, C.QTY, C.CATE_01, C.CATE_02, C.CATE_03, C.CATE_04, 
						 C.BUY_PRICE, C.SALE_PRICE, C.EXTRA_PRICE, C.DELIVERY_PRICE, C.SA_DELIVERY_PRICE, C.TAX_TF, C.USE_TF, C.DEL_TF, 
						 C.REG_ADM, C.REG_DATE, C.DEL_ADM, C.DEL_DATE, C.ORDER_DATE, C.FINISH_DATE, C.PAY_DATE, C.ORDER_STATE, C.ORDER_CONFIRM_DATE,
					
						 ((C.SALE_PRICE * C.QTY) + (C.EXTRA_PRICE * C.QTY)) AS SUM_PRICE, 
						 ((C.SALE_PRICE * C.QTY) - ((C.BUY_PRICE * C.QTY)+C.DELIVERY_PRICE)) AS PLUS_PRICE,
						 ROUND((((C.SALE_PRICE * C.QTY) - ((C.BUY_PRICE * C.QTY)+C.DELIVERY_PRICE)) / (C.SALE_PRICE * C.QTY) * 100),2) AS LEE,
										 
						 C.DELIVERY_CP, C.SENDER_NM, C.SENDER_PHONE, C.DELIVERY_NO, C.PRICE, C.STICKER_PRICE, C.PRINT_PRICE, C.DISCOUNT_PRICE,
						 C.DELIVERY_CNT_IN_BOX, C.SALE_SUSU, C.LABOR_PRICE, C.OTHER_PRICE,
						 C.OPT_WRAP_NO, C.OPT_STICKER_NO, C.OPT_STICKER_READY, C.OPT_STICKER_MSG, C.OPT_OUTBOX_TF, C.OPT_PRINT_MSG, C.OPT_MEMO, C.OPT_REQUEST_MEMO, C.OPT_SUPPORT_MEMO,
						 C.DELIVERY_TYPE, C.OPT_OUTSTOCK_DATE, C.WORK_SEQ, C.WORK_QTY, C.WORK_FLAG, C.WORK_START_DATE,
						 C.SALE_CONFIRM_TF, 
						 G.CATE_03 AS G_CATE_03, G.FILE_NM_100, G.STOCK_CNT, G.FSTOCK_CNT, G.TSTOCK_CNT, G.DELIVERY_CNT_IN_BOX AS DELIVERY_CNT_IN_BOX_G

						FROM TBL_ORDER_GOODS C, TBL_GOODS G 
						 WHERE G.USE_TF= 'Y' 
							 AND G.DEL_TF = 'N' 
							 AND C.GOODS_NO = G.GOODS_NO  
							 AND C.ORDER_GOODS_NO = '$order_goods_no' ";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
            $record[0]=mysql_fetch_assoc($result);
		}
		return $record;
	}

    /**
     * 최근 1주일동안의 해당 member의 ORDER_STATE CNT를 보여준다.
     */
    function cntOrderStateFromMember($db, $memberNo, $start_date, $end_date, $search_str, $orderState){



        $query="SELECT  OG.ORDER_STATE, COUNT(OG.ORDER_STATE) AS CNT
                FROM    TBL_ORDER O
                JOIN    TBL_ORDER_GOODS OG ON O.RESERVE_NO=OG.RESERVE_NO
                WHERE   OG.DEL_TF='N'
                AND     OG.USE_TF='Y'
                AND     O.MEM_NO='$memberNo' 
                AND     DATE_FORMAT(OG.REG_DATE, '%Y-%m-%d') >='$start_date'
                AND     DATE_FORMAT(OG.REG_DATE, '%Y-%m-%d') <= NOW() 
                GROUP BY OG.ORDER_STATE
                ORDER BY OG.ORDER_STATE
                ";

        
        // echo $query."<br>";
        // exit;

        $record=array();
        $cnt=0;
        $result=mysql_query($query, $db);

        if($result<>""){
            $cnt=mysql_num_rows($result);
        }
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
        }
        return $record;
        

    }//end of function

    /**
     * 해당 Member에 관련된 해당 orderState에 대해 "0000-00-00"형태로 된 리스트를 뽑아낸다.
     */
    function listOrderFromMember($db, $memberNo, $start_date, $end_date, $search_str, $orderState, $page){

        // $orderState="";

        $res=explode("/", $start_date);
        $date1=$res[2]."-".$res[0]."-".$res[1];

        $res2=explode("/", $end_date);
        $date2=$res2[2]."-".$res2[0]."-".$res2[1];

        if($memberNo==""){
            return ;
        }
        $query="SELECT  DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS T_REG_DATE
                FROM    TBL_ORDER O
                JOIN    TBL_ORDER_GOODS OG ON O.RESERVE_NO=OG.RESERVE_NO
                WHERE   O.MEM_NO='".$memberNo."'
                AND     O.DEL_TF='N'
                AND     O.USE_TF='Y'
                
                ";
        if($start_date<>""){
            $query.= " AND O.REG_DATE >= '".$date1." 00:00:00' ";
        }
        else{
            $query.=" AND O.REG_DATE >= '2020-01-01 00:00:00' ";
        }
        if($end_date<>""){
            $query.=" AND O.REG_DATE <= '".$date2." 23:59:59' ";
        }
        else{
            $query.=" AND O.REG_DATE <= NOW() ";
        }
        if($search_str<> ""){
            $query.="AND (OG.GOODS_NAME LIKE '%".$search_str."%' OR OG.GOODS_CODE LIKE '%".$search_str."%' ) ";
        }
        if($orderState<>""){
            $query.="AND OG.ORDER_STATE = '$orderState' ";
        }
        $query.=" GROUP BY T_REG_DATE ";
        $query.=" ORDER BY T_REG_DATE DESC, O.RESERVE_NO DESC  ";
        $query.=" LIMIT ".(($page-1)*3).", 3 ";


        // echo "$query<br>";
        // exit;

        $result=mysql_query($query, $db);

        $record=array();
        $cnt=0;


        if($result<>""){
            $cnt=mysql_num_rows($result);
            //echo "cnt : ".$cnt."<br>";

            for($i = 0; $i < $cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
        }
        return $record;


    }//end of function


    /**
     * memberNo별로 주문의 총건을 받아온다.
     */
    function totalCntOrderByMember($db, $memberNo, $start_date, $end_date, $strSearch, $orderState){

        $res=explode("/", $start_date);
        $date1=$res[2]."-".$res[0]."-".$res[1];

        $res2=explode("/", $end_date);
        $date2=$res2[2]."-".$res2[0]."-".$res2[1];

        if($memberNo=="") return 0;
        $query="SELECT  DATE_FORMAT(O.REG_DATE, '%Y-%m-%d')
                FROM    TBL_ORDER O
                JOIN    TBL_ORDER_GOODS OG ON O.RESERVE_NO = OG.RESERVE_NO
                WHERE   O.MEM_NO='".$memberNo."'
                AND     O.DEL_TF='N'
                AND     O.USE_TF='Y'
                
                ";
        if($start_date<>""){
            $query.= " AND O.REG_DATE >= '".$date1." 00:00:00' ";
        }
        else{
            $query.=" AND O.REG_DATE >= '2020-01-01 00:00:00' ";
        }
        if($end_date<>""){
            $query.=" AND O.REG_DATE <= '".$date2." 23:59:59' ";
        }
        else{
            $query.=" AND O.REG_DATE <= NOW() ";
        }
        if($strSearch<> ""){
            $query.="AND (OG.GOODS_NAME LIKE '%".$strSearch."%' OR OG.GOODS_CODE LIKE '%".$strSearch."%' ) ";
        }
        if($orderState<>""){
            $query.="AND OG.ORDER_STATE = '$orderState' ";
        }
        $query.=" GROUP BY DATE_FORMAT( O.REG_DATE, '%Y-%m-%d' ) ";

        // echo "$query<br>";
        // exit;

        $result=mysql_query($query, $db);

        $cnt=0;


        if($result<>""){
            $cnt=mysql_num_rows($result);
        }
        return $cnt;
    }//end of function


    /**
     * 정해진 날자의 OrderGoods를 모두 들고온다.
     */
    function listOrderGoodsByRegDate($db, $memberNo, $regDates, $orderState, $searchStr){
        $query="SELECT   O.RESERVE_NO, O.MEM_NO , DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') AS T_REG_DATE
                        ,OG.GOODS_CODE, OG.GOODS_NO, OG.GOODS_NAME, OG.SALE_PRICE, OG.QTY, OG.ORDER_STATE, OG.ORDER_GOODS_NO
                        ,G.FILE_NM_100, G.IMG_URL, G.FILE_PATH_150, G.FILE_RNM_150
                        , OG.DELIVERY_CP
                        , OG.DELIVERY_NO
                FROM    TBL_ORDER O
                JOIN    TBL_ORDER_GOODS OG ON O.RESERVE_NO=OG.RESERVE_NO
                JOIN    TBL_GOODS   G   ON OG.GOODS_NO=G.GOODS_NO
                WHERE   DATE_FORMAT(O.REG_DATE, '%Y-%m-%d') IN (".$regDates.") ";

        if($orderState <> ""){
            $query.= "AND     OG.ORDER_STATE='".$orderState."' ";
        }
        if($searchStr<> ""){
            $query.="AND (OG.GOODS_NAME LIKE '%".$searchStr."%' OR OG.GOODS_CODE LIKE '%".$searchStr."%' ) ";
        }

            
                
        $query.="   AND     O.MEM_NO='".$memberNo."'
                AND     OG.DEL_TF='N'
                AND     OG.USE_TF='Y'
                ORDER BY O.REG_DATE DESC, OG.RESERVE_NO DESC, OG.ORDER_STATE DESC 

                    ;   ";


        //  echo "$query<br>";
        // exit;

        $result=mysql_query($query, $db);
        $record=array();
        $cnt=0;
        if($result<>""){
            $cnt=mysql_num_rows($result);

            for($i = 0; $i < $cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
            }
            return $record;
        }
        else{
            return null;
        }


    }//end of function
    function changeDateFormat($date){
        $res=explode("/", $date);
        $changedDate=$res[2]."-".$res[0]."-".$res[1];
        return $changedDate;
    }

?>
<?// PHP PROCESS

    if ($this_date == "") 
    $this_date = date("Y-m-d",strtotime("0 month"));

    if ($this_h == "") 
    $this_h = date("H",strtotime("0 month"));

    if ($this_i == "") 
    $this_i = date("i",strtotime("0 month"));

    if ($this_s == "") 
    $this_s = date("s",strtotime("0 month"));


    $temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

    if($mode == "REGISTER_CLAIM"){

        print_r($_POST);
        echo "<br><br>";
        // exit;

        $orderGoodsInfo=getOrderGoodsInfo($conn, $hd_order_goods_no);

        $DB_ORDER_GOODS_NO				= trim($orderGoodsInfo[0]["ORDER_GOODS_NO"]);
        $DB_ON_UID						= trim($orderGoodsInfo[0]["ON_UID"]);
        $DB_MEM_NO						= trim($orderGoodsInfo[0]["MEM_NO"]);
        $DB_CP_ORDER_NO				    = trim($orderGoodsInfo[0]["CP_ORDER_NO"]);
        $DB_BUY_CP_NO					= trim($orderGoodsInfo[0]["BUY_CP_NO"]);
        $DB_RESERVE_NO					= trim($orderGoodsInfo[0]["RESERVE_NO"]);
        $DB_GOODS_NO					= trim($orderGoodsInfo[0]["GOODS_NO"]);
        $DB_GOODS_CODE					= trim($orderGoodsInfo[0]["GOODS_CODE"]);
        $DB_GOODS_SUB_NAME				= trim($orderGoodsInfo[0]["GOODS_SUB_NAME"]);
        $DB_GOODS_NAME					= SetStringFromDB($orderGoodsInfo[0]["GOODS_NAME"]);
        $DB_PRICE						= trim($orderGoodsInfo[0]["PRICE"]);
        $DB_BUY_PRICE					= trim($orderGoodsInfo[0]["BUY_PRICE"]);
        $DB_SALE_PRICE					= trim($orderGoodsInfo[0]["SALE_PRICE"]);
        $DB_EXTRA_PRICE				    = trim($orderGoodsInfo[0]["EXTRA_PRICE"]);
        $DB_DELIVERY_PRICE				= trim($orderGoodsInfo[0]["DELIVERY_PRICE"]);
        $DB_SA_DELIVERY_PRICE 			= trim($orderGoodsInfo[0]["SA_DELIVERY_PRICE"]);
        $DB_DISCOUNT_PRICE 			    = trim($orderGoodsInfo[0]["DISCOUNT_PRICE"]);
        $DB_STICKER_PRICE 				= trim($orderGoodsInfo[0]["STICKER_PRICE"]);
        $DB_PRINT_PRICE 				= trim($orderGoodsInfo[0]["PRINT_PRICE"]);
        $DB_SALE_SUSU 					= trim($orderGoodsInfo[0]["SALE_SUSU"]);
        $DB_TAX_TF						= trim($orderGoodsInfo[0]["TAX_TF"]);
        $DB_LABOR_PRICE 				= trim($orderGoodsInfo[0]["LABOR_PRICE"]);
        $DB_OTHER_PRICE 				= trim($orderGoodsInfo[0]["OTHER_PRICE"]);
        $DB_CATE_01					    = trim($orderGoodsInfo[0]["CATE_01"]);
        $DB_CATE_02					    = trim($orderGoodsInfo[0]["CATE_02"]);
        $DB_CATE_03					    = trim($orderGoodsInfo[0]["CATE_03"]);
        $DB_CATE_04					    = trim($orderGoodsInfo[0]["CATE_04"]);
        $DB_SUM_PRICE					= trim($orderGoodsInfo[0]["SUM_PRICE"]);
        $DB_PLUS_PRICE					= trim($orderGoodsInfo[0]["PLUS_PRICE"]);
        $DB_GOODS_LEE					= trim($orderGoodsInfo[0]["LEE"]);
        $DB_QTY						    = trim($orderGoodsInfo[0]["QTY"]);
        $DB_REQ_DATE					= trim($orderGoodsInfo[0]["PAY_DATE"]);
        $DB_END_DATE					= trim($orderGoodsInfo[0]["FINISH_DATE"]);
        $DB_ORDER_STATE				    = trim($orderGoodsInfo[0]["ORDER_STATE"]);
        $DB_DELIVERY_CP				    = trim($orderGoodsInfo[0]["DELIVERY_CP"]);
        $DB_DELIVERY_NO				    = trim($orderGoodsInfo[0]["DELIVERY_NO"]);
        $DB_SENDER_NM					= trim($orderGoodsInfo[0]["SENDER_NM"]);
        $DB_SENDER_PHONE				= trim($orderGoodsInfo[0]["SENDER_PHONE"]);
        $DB_OPT_STICKER_NO              = trim($orderGoodsInfo[0]["OPT_STICKER_NO"]);
        $DB_OPT_STICKER_MSG             = trim($orderGoodsInfo[0]["OPT_STICKER_MSG"]);
        $DB_OPT_OUTBOX_TF		        = trim($orderGoodsInfo[0]["OPT_OUTBOX_TF"]);
        $DB_DELIVERY_CNT_IN_BOX         = trim($orderGoodsInfo[0]["DELIVERY_CNT_IN_BOX"]);
        $DB_OPT_WRAP_NO		            = trim($orderGoodsInfo[0]["OPT_WRAP_NO"]);
        $DB_OPT_PRINT_MSG		        = trim($orderGoodsInfo[0]["OPT_PRINT_MSG"]);
        $DB_OPT_OUTSTOCK_DATE	        = trim($orderGoodsInfo[0]["OPT_OUTSTOCK_DATE"]);
        $DB_OPT_MEMO			        = trim($orderGoodsInfo[0]["OPT_MEMO"]);
        $DB_OPT_REQUEST_MEMO	        = trim($orderGoodsInfo[0]["OPT_REQUEST_MEMO"]);
        $DB_OPT_SUPPORT_MEMO	        = trim($orderGoodsInfo[0]["OPT_SUPPORT_MEMO"]);
        $DB_DELIVERY_TYPE		        = trim($orderGoodsInfo[0]["DELIVERY_TYPE"]);
        $DB_WORK_QTY			        = trim($orderGoodsInfo[0]["WORK_QTY"]);
        $DB_OPT_OUTSTOCK_DATE	        = date('Y-m-d', strtotime($DB_OPT_OUTSTOCK_DATE));

        $use_tf                         =   "Y";
        $cart_seq                       =   getOrderGoodsMaxSeq($conn, $DB_RESERVE_NO);
        $cart_seq++;   


        print_r($orderGoodsInfo);
        echo "<br><br>";
        

        $arr_order_rs                   =   selectOrder($conn, $DB_RESERVE_NO);
        $rs_o_mem_nm                    =   trim($arr_order_rs[0]["O_MEM_NM"]);
        $rs_r_mem_nm                    =   trim($arr_order_rs[0]["R_MEM_NM"]);
        $rs_cp_no                       =   trim($arr_order_rs[0]["REG_DATE"]);
        $rs_reg_date                    =   trim($arr_order_rs[0]["REG_DATE"]);

        $discount_price              =   0;
        $claim_order_goods_no           =   "";


        print_r($arr_order_rs);
        echo"<br><br>";
        // exit;

        if($claim_state <> "99"){//99 : 기타 (홈페이지단에서는 기타 선택지가 아예 없지만 전체적인 흐름 이해를 위해 이 코드 첨가)
            $memos = array('opt_request_memo' => $DB_OPT_REQUEST_MEMO, 'opt_support_memo' => $DB_OPT_SUPPORT_MEMO);

            //클레임 관련 ORDER_GOODS REC를 만들 때는 
            $claim_order_goods_no = insertOrderGoods(   $conn, $DB_ON_UID, $DB_RESERVE_NO, $DB_CP_ORDER_NO, $DB_BUY_CP_NO, $DB_MEM_NO, $cart_seq, $DB_GOODS_NO, $DB_GOODS_CODE, $DB_GOODS_NAME,
                                                        $DB_GOODS_SUB_NAME, $DB_QTY, $DB_OPT_STICKER_NO, $DB_OPT_STICKER_MSG, $DB_OPT_OUTBOX_TF, $DB_DELIVERY_CNT_IN_BOX, $DB_OPT_WRAP_NO, $DB_OPT_PRINT_MSG,
                                                        $DB_OPT_OUTSTOCK_DATE, $DB_OPT_MEMO, $memos, $DB_DELIVERY_TYPE, $DB_DELIVERY_CP,$DB_SENDER_NM, $DB_SENDER_PHONE, $DB_CATE_01, $DB_CATE_02, $DB_CATE_03, 
                                                        $null_cate_04, $DB_PRICE, $DB_BUY_PRICE, $DB_SALE_PRICE, $DB_EXTRA_PRICE, $DB_DELIVERY_CP, $DB_SA_DELIVERY_PRICE, $DB_DISCOUNT_PRICE, $DB_STICKER_PRICE, 
                                                        $DB_PRINT_PRICE, $DB_SALE_SUSU, $DB_LABOR_PRICE, $DB_ORDER_PRICE, $DB_TAX_TF, $claim_state, $use_tf, $_SESSION["C_MEM_NM"]);
        }

        // exit;

        if($DB_DELIVERY_TYPE != "98" && $DB_DELIVERY_TYPE !="3"){ //98: 외부업체 발송, 3: 퀵서비스
            updateOrderGoodsGroupNo($conn, $hd_order_goods_no, $claim_order_goods_no, $DB_QTY);
        }
        else{
            if($claim_state != "8"){// 8 :교환임 -> 즉 8이 아니기 때문에 반품이라는 말이다.
                updateOrderGoodsGroupNo($conn, $hd_order_goods_no, $claim_order_goods_no, $DB_QTY);
            }
        }
        updateOrderGoodsClaimNo($conn, $hd_order_goods_no, $claim_order_goods_no);

        $inout_date=date("Y-m-d", strtotime("0 month"));

        $inout_type= "LR01";

        $claim_state_name = getDcodeName($conn, "ORDER_STATE", $claim_state);

        $TEMP_MEMO          =    $claim_state_name."(클레임:".$claim_order_goods_no.")";

        $options = array('CLAIM_ORDER_GOODS_NO' => $claim_order_goods_no);

        $base_date=getDcodeExtByCode($conn, "LEDGER_SETUP", "BASE_DATE");

        if($base_date < $rs_reg_date && $DB_ORDER_STATE != 1){
            insertCompanyLedger($conn, $rs_cp_no, $inout_date, $input_type, $DB_GOODS_NO, $DB_GOODS_NAME."[".$DB_GOODS_CODE."]",-1*$DB_QTY, $DB_SALE_PRICE,null, 0, $DB_CATE_01,$DB_TAX_TF, 
                                $TEMP_MEMO, $DB_RESERVE_NO, $ORDER_GOODS_NO, "클레임 ".$claim_state_name, null, $_SESSION["C_MEM_NM"], $options);
        }

        if($DB_DELIVERY_TYPE != "98" && $DB_ORDER_STATE == "2" && $claim_state == "8"){
            $refund_able_qty    =   getRefundAbleQty($conn, $DB_RESERVE_NO, $hd_order_goods_no);

            if($DB_WORK_QTY >= $refund_able_qty){
                updateWorksFlagNOrderGoods($conn, $hd_order_goods_no);
            }
        }

        if($claim_state =="8"){ //교환인 경우
            $cart_seq++;

            $new_order_state    =   "1";
            $cate_04            =   "CHANGE";
            $memos              =   array("opt_request_memo" => $DB_OPT_REQUEST_MEMO, 'opt_support_memo' => $DB_OPT_SUPPORT_MEMO);

            $new_order_goods_no =   insertOrderGoods($conn, $DB_ON_UID, $DB_RESERVE_NO, $DB_CP_ORDER_NO, $DB_CP_ORDER_NO, $DB_MEM_NO, $cart_seq,
                                                        $DB_GOODS_NO, $DB_GOODS_CODE, $DB_GOODS_NAME, $DB_GOODS_SUB_NAME, $DB_QTY, $DB_OPT_STICKER_NO, 
                                                        $DB_OPT_STICKER_MSG, $DB_OUTBOX_TF, $DB_DELIVERY_CNT_IN_BOX, $DB_OPT_WRAP_NO, $OPT_PRINT_MSG,
                                                        $DB_OPT_OUTSTOCK_DATE, $DB_OPT_MEMO, $memos, $DB_DELIVERY_TYPE, $DB_DELIVERY_CP, $DB_SENDER_NM,
                                                        $DB_SENDER_PHONE, $DB_CATE_01, $DB_CATE_02, $DB_CATE_03, $cate_04, $DB_PRICE, 
                                                        $DB_BUY_PRICE, $DB_SALE_PRICE, $DB_EXTRA_PRICE, $DB_DELIVERY_PRICE, $DB_SA_DELIVERY_PRICE,
                                                        $DB_DISCOUNT_PRICE, $DB_STICKER_PRICE, $DB_PRINT_PRICE, $DB_SALE_SUSU, $DB_LABOR_PRICE, $DB_OTHER_PRICE, $DB_TAX_TF,
                                                        $new_order_state, "Y", $_SESSION["C_MEM_NM"]
                                                    );

            
        }
        // if($claim)

        $bb_code="CLAIM";
        $writer_nm=$_SESSION["C_MEM_NM"];
        $writer_pw=$_SESSION["C_MEM_NO"];

        $bb_cate_01 =   $DB_RESERVE_NO;
        $bb_cate_02 =   $claim_type;
        $bb_cate_03 =   $cart_seq;
        $bb_cate_04 =   $claim_state;
        $bb_title   =   $DB_GOODS_NAME;
        $bb_content =   $claim_memo;
        $bb_email      =   $rs_o_mem_nm;
        $bb_homepage   =   $rs_r_mem_nm;
        $bb_keyword    =   $buy_cp_no;
        $bb_file_size  =   $DB_QTY;

        $contents= $contents.$_SESSION["C_MEM_NM"]." (".$temp_date.")";
        $recomm=$order_goods_no;

        $new_bb_no= insertBoard($conn ,$bb_code, $bb_cate_01, $bb_cate_02, $bb_cate_03, $bb_cate_04, 
                                $writer_nm, $writer_pw, $bb_email, $bb_homepage, $bb_title, 
                                $bb_ref_id, $bb_recomm, $bb_content, $bb_file_nm, $bb_file_rnm, $bb_file_path, $bb_file_size, 
                                $bb_file_ext, $bb_keyword, $bb_comment_tf, "Y", $_SESSION["C_MEM_NO"]);
        
?>
    <script>
        alert('클레임이 등록되었습니다');
        $("#claim_popup").css("display","none");
    </script>
<?

        

    }//end of mode("REGISTER_CLAIM)

    print_r($_SESSION);
    echo "<br>";
    if($start_date<>""){
        $startDate  = $start_date;
        //$start_date = changeDateFormat($start_date);
        $start_date = date("m/d/Y",strtotime($start_date));
    }
    else
    {
        $toDay = date("m/d/Y");        
        $start_date = date("m/d/Y",strtotime($toDay."-7day"));
    }

    if($end_date<>""){
        $endDate    = $end_date;
        //$end_date   = changeDateFormat($end_date);
        $end_date = date("m/d/Y",strtotime($end_date));
    }
    else
    {
        $end_date   = date("m/d/Y");
    }


    //잡아놓고 사용하는 변수임으로 수정하지 말것 <2021-06-16 16:47>
    $now=date("Y-m-d");
    $before7days=date("Y-m-d",strtotime($now."-7day"));
    if($strTerm==""){
        $strTerm=7;
    }


?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./header.php"; ?>
        <script>
            // function js_cancel_qty_selectBox(qty){
            //     let str="<SELECT>";
            //     str+="<OPTION value='0'>선택</OPTION>";
            //     for(i=0; i<=qty; i++){
            //         str+="<OPTION value='"+i+"'>"+i+"개</OPTION>";
            //     }
            //     str+="</SELECT>";
            //     return str;
            // }

            function js_add_comma(value){
				value=value+"";
				value=value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				return value;
				
			}
            function js_open_claim_window(orderGoodsNo){

                $.ajax({
                    url:"./ajax/ajax_process.php",
                    dataType:'JSON',
                    type:"POST",
                    async:true,
                    data:{
                        "mode" : "GET_ORDER_GOODS",
                        "orderGoodsNo":orderGoodsNo
                    },
                    success:function(goodsInfo){
                        // console.log(data);
                        $("#goods_image").css("background","url("+goodsInfo["ORDER_GOODS_IMG"]+") center center / cover no-repeat");
                        $("#goods_name").html("["+goodsInfo["GOODS_CODE"]+"]"+goodsInfo["GOODS_NAME"]+" "+goodsInfo["GOODS_SUB_NAME"]);
                        $("#goods_price").html(js_add_comma(goodsInfo["SALE_PRICE"]));
                        $("#delivery_price").html(js_add_comma(goodsInfo["DELIVERY_PRICE"]));
                        $("#discount_price").html(js_add_comma(goodsInfo["DISCOUNT_PRICE"]));
                        $("#goods_quantity").html(js_add_comma(goodsInfo["QTY"]));
                        $("#opt_print_msg").html(js_add_comma(goodsInfo["OPT_PRINT_MSG"]));
                        $("#opt_sticker_no").html(js_add_comma(goodsInfo["OPT_STICKER_NO"]));
                        $("input[name='hd_order_goods_no']").val(orderGoodsNo);

                        var totalPrice=Number(goodsInfo["QTY"])*Number(goodsInfo["SALE_PRICE"])+Number(goodsInfo["DELIVERY_PRICE"])-Number(goodsInfo["DISCOUNT_PRICE"]);
                        $("#total_goods_price").html(js_add_comma(totalPrice));

                        //--------------------------------------------------------//                        
                        $("#claim_popup").css("display","block");
                    },
                    error:function(jqXHR, textStatus, errorThrown){

                    }
                });
            }
            function js_viewOrderGoodsByReserveNo(reserveNo){
                location.href="delivery_detail.php?reserveNo="+reserveNo;
            }
            function js_movePagination(page, totalPage){
                frm=document.pageForm;
                pageForm.curPage.value=page;
                frm.submit();
            }
            function js_search(){
                var frm=document.searchForm;
                frm.targat="";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();
            }
            function getDateStr(pDate){

                var year = pDate.getFullYear();              //yyyy
                var month = (1 + pDate.getMonth());          //M
                var day = pDate.getDate();                   //d

                month = month >= 10 ? month : '0' + month;  //month 두자리로 저장                
                day = day >= 10 ? day : '0' + day;          //day 두자리로 저장

                return month+'/'+day+'/'+year;
            }
            function js_select_date(days){
                let d = new Date();
                let td= new Date();
                
                if(days==7){
                    var retDate;
                    var dayOfMonth=td.getDate();
                    td.setDate(dayOfMonth-7);
                    tRetDate    =   getDateStr(td);
                    retDate     =   getDateStr(d);
                    $("#idTerm").html("1주일 <span class='caret'></span>");
                    
                    $("#datepicker-start").val(tRetDate);
                    $("#datepicker-end").val(retDate);
                    $("input[name='strTerm']").val(days);

                    return ;
                }
                else if(days==30){
                    var retDate;
                    var monthOfYear=td.getMonth();
                    td.setMonth(monthOfYear-1);
                    tRetDate    =   getDateStr(td);
                    retDate     =   getDateStr(d);
                    $("#idTerm").html("1개월 <span class='caret'></span>");

                    $("#datepicker-start").val(tRetDate);
                    $("#datepicker-end").val(retDate);
                    $("input[name='strTerm']").val(days);
                    return ;
                }
                else if(days==90){

                    var retDate;
                    var monthOfYear=td.getMonth();
                    td.setMonth(monthOfYear-3);
                    tRetDate    =   getDateStr(td);
                    retDate     =   getDateStr(d);
                    $("#idTerm").html("3개월 <span class='caret'></span>");

                    $("#datepicker-start").val(tRetDate);
                    $("#datepicker-end").val(retDate);
                    $("input[name='strTerm']").val(days);
                    return ; 
                }
            }//end of jsFunction

            function js_select_orderState(osNo)
            {
                if(osNo == 1)
                {
                    $("#sel_order_state").html("주문접수중");        
                }
                else if(osNo == 2)
                {
                    $("#sel_order_state").html("작업/배송");
                }
                else if(osNo == 3)
                {
                    $("#sel_order_state").html("배송완료");
                }
                else
                {
                    $("#sel_order_state").html("전체");
                }

                $("input[name='sel_order_state']").val(osNo);
            }

            function js_delivery_detail(seq){
                // var frm = document.frm;
                frm=document.forms["frmDetail_"+seq];
                frm.target="";
                frm.action="delivery_detail.php";
                frm.submit();
                // alert(frm);
                // return ;
                // location.href="delivery_detail.php?memberNo="+memberNo+"&reserveNo="+reserveNo
            }

            function js_delivery_pop(delivery_cd, delivery_no)
            {
                var url = "pop_delivery_trace.php?delivery_cp=" + delivery_cd + "&delivery_no=" + delivery_no;
                window.open(url, "pop_delivery_trace", "width=10, height=10, top=50, left=0");
            }
            function js_cancel(){
                $("#claim_popup").css("display","none");
            }
            function js_claim(){
                if($("input[name='claim_type']").val()=="0"){
                    alert("클레임 사유를 선택해 주세요");
                    $("input[name='claim_type']").focus();
                    return;
                }
                frm=document.frmClaim;
                frm.mode.value="REGISTER_CLAIM";
                frm.target="";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();
            }
        </script>
        <style>
            #claim_popup{
                display:none;
            }
        </style>


    </head>
    <body>

        <div class="wrap">
            <? require "./top.php";?>
           

            <div class="detail_page">
                <div class="detail_page_inner">
                    
                    <div class="cart_info">
                        <h4>
                            주문/배송 조회<span>&nbsp;&nbsp;(최근 일주일)</span>
                        </h4>
                        <div class="processing">
                            <?
                                $arrOrderState=cntOrderStateFromMember($conn, $memberNo, $before7days, $now, $search_str, $sel_order_state);
                                //
                                // echo "arrOrderstate: ";
                                // print_r($arrOrderState);
                                // echo "<br><br>";

                            ?>
                            <img src="img/process.png" alt="">
                            <div>주문접수<span><?=$arrOrderState[0]["CNT"]<>""? $arrOrderState[0]["CNT"]:0?></span></div>
                            <div>작업중<span><?=$arrOrderState[1]["CNT"]<>""? $arrOrderState[1]["CNT"]:0?></span></div>
                            <div>배송중<span><?=$arrOrderState[2]["CNT"]<>""? $arrOrderState[2]["CNT"]:0?></span></div>
                            <div>배송완료<span><?=$arrOrderState[3]["CNT"]<>""?$arrOrderState[3]["CNT"]:0?></span></div>
                        </div>
                        <form name="searchForm" method="POST">
                            <div class="navbar navbar-default">
                        
                                <div class="container-fluid">
                                    
                                    <div class="navbar-header">
                                    <span class="navbar-brand">조회기간</span>
                                    </div>
                                    <div class="collapse navbar-collapse navbar-left" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav">
                                            <li class="dropdown">
                                                <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id='idTerm'>
                                                <?
                                                        if($strTerm==7){
                                                            echo "1주일";
                                                        }
                                                        else if($strTerm==30){
                                                            echo "1개월";
                                                        }
                                                        else if($strTerm==90){
                                                            echo "3개월";
                                                        }
                                                        else{

                                                            echo "전체";
                                                        }
                                                    ?>
                                                    <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu" style="display:none;">
                                                    <li><a href="#" class="date" data-date_span="7"     onclick="js_select_date(7)">1주일</a></li>
                                                    <li><a href="#" class="date" data-date_span="30"    onclick="js_select_date(30)">1개월</a></li>
                                                    <li><a href="#" class="date" data-date_span="90"    onclick="js_select_date(90)">3개월</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <input type="hidden" name="strTerm" value="<?=$strTerm?>">
                                    
                                    </div><!-- /.navbar-collapse -->
                                    <div class="navbar-form navbar-left">
                                        <div class="input-group">
                                            <input type="text" id="datepicker-start" class="form-control datepicker" name="start_date" value="<?=$start_date?>">    
                                            <label class="input-group-addon" for="datepicker-start">
                                                <span id="btn-datepicker-start"></span>
                                            </label>
                                        </div>
                                        ~
                                        <div class="input-group">
                                            <input type="text" id="datepicker-end" class="form-control datepicker" name="end_date" value="<?=$end_date?>">    
                                            <label class="input-group-addon" for="datepicker-end">
                                                <span id="btn-datepicker-end" class="glyphicon glyphicon-calendar"></span>
                                            </label>						
                                        </div>
                                        
                                    </div>
                                </div><!-- /.container-fluid -->
                                
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <span class="navbar-brand">주문상태</span>
                                    </div>
                                    <div class="collapse navbar-collapse navbar-left" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav">
                                            <li class="dropdown">
                                                <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                    <span id="sel_order_state">
                                                    <?
                                                        if($sel_order_state==1){
                                                            echo "주문접수중";
                                                        }
                                                        else if($sel_order_state==2){
                                                            echo "작업/배송";
                                                        }
                                                        else if($sel_order_state==3){
                                                            echo "배송완료";
                                                        }
                                                        else{
                                                            echo "전체";
                                                        }
                                                    ?>
                                                    </span> 
                                                <span class="caret"></span></a>
                                                <ul class="dropdown-menu" style="display:none;">
                                                    <li><a href="#" class="li_order_state" >전체</a></li>
                                                    <li><a href="#" class="li_order_state" onclick="js_select_orderState(1)">주문접수</a></li>
                                                    <li><a href="#" class="li_order_state" onclick="js_select_orderState(2)">작업/배송</a></li>
                                                    <li><a href="#" class="li_order_state" onclick="js_select_orderState(3)">배송완료</a></li>
                                                </ul>
                                                <input type="hidden" name="sel_order_state" value="<?=$sel_order_state?>">
                                            </li>
                                        </ul>
                                    
                                    </div><!-- /.navbar-collapse -->

                                    <div class="navbar-form navbar-left">
                                        
                                        <input type="text" class="form-control" style="width:200px" name="search_str" placeholder="검색어 입력" value="<?=$search_str?>">
                                        
                                        <button type="submit" class="btn btn-default" onclick="js_search()">조회하기</button>
                                    </div>
                                </div><!--container-fluid-->
                            </div><!--navbar navbar-default-->

                        </form>

                        
                        <script>
                            $(document).ready(function(){
                                $(".dropdown a").click(function(){
                                    if ( $(this).parent().children(".dropdown-menu").css("display") == "none" )
                                    {
                                        $(this).parent().children(".dropdown-menu").css("display","block");
                                    } else {
                                        $(this).parent().children(".dropdown-menu").css("display","none");
                                    }
                                });
                                $(".dropdown-menu").click(function(){
                                    $(".dropdown-menu").css("display","none");
                                });
                            });
                        </script>
                        <script>
                            $(document).ready( function() {

                                $("#datepicker-start").datepicker();
                                $("#datepicker-end").datepicker();

                                $(document).on('keypress',function(e) {
                                    if(e.which == 13) {
                                        //delete g_goods[1];
                                        // g_goods.push({"CATALOG_NO": "5","FILE_PATH": "/upload_data/goods_image/0101/","FILE_RNM": "301-008065.jpg","GOODS_CODE": "301-008065","GOODS_DSC1": "","GOODS_DSC2": "","GOODS_DSC3": "","GOODS_DSC4": "","GOODS_DSC5": "","GOODS_DSC6": "","GOODS_DSC7": "","GOODS_DSC8": "","GOODS_DSC9": "","GOODS_IDX": "18","GOODS_NAME": "슈가버블 주방세제 용기 300g","GOODS_NO": "3797","MULTIPLE_TF": "F","PAGE_NO": "0","POS_X": "1","POS_Y": "1","PRICE": "0","SALE_STATE": "판매중","SIZE_X": "1","SIZE_Y": "1","USE_TF": "Y"});
                                        //console.log(g_goods);
                                        var a=$("input[name='start_date']").val();
                                        var b=$("input[name='end_date']").val();
                                    }
                                });
                            });
                        </script>

                        <!----------------------------------------------------------------------->
                        <?
                            $cntOfTotal=totalCntOrderByMember($conn, $memberNo, $start_date, $end_date, $search_str,$sel_order_state);

                            // echo "cntOfTotal : ".$cntOfTotal."<br>";
                            // exit;

                        
                            $arrOrderDate= listOrderFromMember($conn, $memberNo, $start_date, $end_date, $search_str, $sel_order_state, $curPage);
                            $cntOrderDate=sizeof($arrOrderDate);
                            $strOrderDate="";
                            for($i=0; $i<$cntOrderDate; $i++){
                                $strOrderDate.="'".$arrOrderDate[$i]['T_REG_DATE']."',";
                            }//end of for($cntOderGoods)
                            $strOrderDate=rtrim($strOrderDate,",");
                            // echo "strOrderDate : ".$strOrderDate."<br>";
                            // echo"---------------------------------------------------------------<br>";
                            // exit;

                            $arrOrderGoods  =   listOrderGoodsByRegDate($conn, $memberNo, $strOrderDate, $sel_order_state, $search_str);
                            $cntOrderGoods  =   sizeof($arrOrderGoods);





                            for($i=0; $i<$cntOrderGoods; $i++){
                                $FILE_NM_100    =   $arrOrderGoods[$i]["FILE_NM_100"];
                                $IMG_URL        =   $arrOrderGoods[$i]["IMG_URL"];
                                $FILE_PATH_150  =   $arrOrderGoods[$i]["FILE_PATH_150"];
                                $FILE_RNM_150   =   $arrOrderGoods[$i]["FILE_RNM_150"];

                                $DELIVERY_CP   =   $arrOrderGoods[$i]["DELIVERY_CP"];
                                $DELIVERY_NO   =   $arrOrderGoods[$i]["DELIVERY_NO"];
    
                                $orderGoodsImgUrl	= getGoodsImage($FILE_NM_100, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "100", "100");
                                if($i==0){
                                ?>
                                    <h4><?=$arrOrderGoods[$i]["T_REG_DATE"]?> 주문</h4>
                                    <div class="divline"></div>
                                    <div class="order_list">
                                        <b onclick="js_viewOrderGoodsByReserveNo('<?=$arrOrderGoods[$i]['RESERVE_NO']?>')" style="cursor:pointer;"><?=$arrOrderGoods[$i]["RESERVE_NO"]?></b>
                                        <table>

                                <?
                                }
                                if($i>0 && $arrOrderGoods[$i-1]["T_REG_DATE"] != $arrOrderGoods[$i]["T_REG_DATE"]){
                                ?>
                                        </table>
                                    </div><!--order_list-->
                                    <h4><?=$arrOrderGoods[$i]["T_REG_DATE"]?> 주문</h4>
                                    <div class="divline"></div>
                                    <div class="order_list">
                                        <b onclick="js_viewOrderGoodsByReserveNo('<?=$arrOrderGoods[$i]['RESERVE_NO']?>')" style="cursor:pointer;"><?=$arrOrderGoods[$i]["RESERVE_NO"]?></b>
                                        <table>



                                <?
                                }
                                else if($i>0 && $arrOrderGoods[$i-1]["RESERVE_NO"] != $arrOrderGoods[$i]["RESERVE_NO"]){
                                ?>
                                        </table>
                                        <b onclick="js_viewOrderGoodsByReserveNo('<?=$arrOrderGoods[$i]['RESERVE_NO']?>')" style="cursor:pointer;"><?=$arrOrderGoods[$i]["RESERVE_NO"]?></b>
                                        <table>
                                <?
                                }
                            ?>

                                            <tr>
                                                <td rowspan="3"><div class="thumb" style="background:url('<?=$orderGoodsImgUrl?>') no-repeat;background-size:cover;"></div></td>
                                                <td colspan="2">
                                                    [<?=$arrOrderGoods[$i]["GOODS_CODE"]?>] <?=SetStringFromDB($arrOrderGoods[$i]["GOODS_NAME"])?>
                                                    <br><i style="font-style:noraml">옵션</i>
												</td>
                                                <td rowspan="3">
                                                <?
                                                    if($DELIVERY_NO != "")
                                                    {
                                                ?>
                                                        <button onclick="js_delivery_pop('<?=$DELIVERY_CP?>','<?=$DELIVERY_NO?>')">배송조회</button><br>
                                                <?
                                                    }
                                                    else
                                                    {
                                                ?>      <button disabled>배송조회</button><br>  
                                                <?
                                                    }
                                                ?>
                                                    <form name="frmDetail_<?=$i?>" method="POST">
                                                        <input type="hidden" name="orderGoodsNo" value="<?=$arrOrderGoods[$i]["ORDER_GOODS_NO"]?>">
                                                        <input type="hidden" name="memberNo" value="<?=$memberNo?>">
                                                    </form>
                                                    <button class="red" type="button" onclick="js_open_claim_window('<?=$arrOrderGoods[$i]['ORDER_GOODS_NO']?>')">교환/반품신청</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?=$arrOrderGoods[$i]["QTY"]?>개</td><td style="text-align:right !important"><?=number_format($arrOrderGoods[$i]["SALE_PRICE"])?>원</td>
                                            </tr>
                                            <tr>
                                                <?
                                                    $orderState=$arrOrderGoods[$i]["ORDER_STATE"];
                                                    if($orderState==1){
                                                        $orderStateStr="주문접수";
                                                    }
                                                    else if($orderState==2){
                                                        $orderStateStr="작업중";
                                                    }
                                                    else if($orderState==3){
                                                        $orderStateStr="배송중";
                                                    }
                                                    else{
                                                        $orderStateStr="기타";
                                                    }
                                                ?>
                                                <td colspan="2"><?=$orderStateStr?></td>
                                            </tr>
                            <?

                            }//end of for(cntOrderGoods)
                            
                        ?>
                                        </table>
                                    </div>


                        <form name="pageForm" method="POST">
                            <div class="bottom_section">
                                <?if($curPage>10){?><a href="javascript:js_movePagination('<?=($curPage-10)?>','<?=$cntTotalPage?>')" class="dir">&lt;&lt;</a><?}?>
                                <?if($curPage>1){?> <a href="javascript:js_movePagination('<?=($curPage-1)?>','<?=$cntTotalPage?>')" class="dir">&lt;</a><?}?>
                            <?
                                $cntTotalPage=(int)($cntOfTotal/3);
                                if($cntOfTotal%3>0) $cntTotalPage++;

                                for($p=1; $p<=$cntTotalPage; $p++){
                                ?>
                                    <a class="<?if($curPage==$p){echo "now";}?>" href="javascript:js_movePagination('<?=$p?>','<?=$cntTotalPage?>')"><?=$p?></a>
                                <?
                                }//end of for(cntTotalPage)
                            ?>
                                <?if($curPage<$cntTotalPage){?>     <a href="javascript:js_movePagination('<?=($curPage+1)?>','<?=$cntTotalPage?>')" class="dir">&gt;</a><?}?>
                                <?if($curPage+10<=$curTotalPage){?> <a href="javascript:js_movePagination('<?=($curPage+10)?>','<?=$cntTotalPage?>')" class="dir">&gt;&gt;</a><?}?>
                                <input type="hidden" name="curPage" value="<?=$curPage?>">
                                <input type="hidden" name="start_date" value="<?=$startDate?>">
                                <input type="hidden" name="end_date" value="<?=$endDate?>">
                                <input type="hidden" name="search_str"  value="<?=$search_str?>">
                                <input type="hidden" name="sel_order_state" value="<?=$sel_order_state?>">
                                <input type="hidden" name="strTerm" value="<?=$strTerm?>">
                            </div>
                        </form>

                    </div><!--cart-info-->     
                </div><!--detail_page_inner-->
            </div><!--detail_page-->
        </div><!--wrap-->
<?
	require "footer.php";
?>
        <script>
            $(function() {
                $(".wrapper").mousewheel(function(event, delta) {
                    this.scrollLeft -= (delta * 120);
                    event.preventDefault();
                });
            })
        </script>
        <script src="js/jquery.mousewheel.js"></script>
    </body>
    <div id="claim_popup">
        <div class="dark_wall"></div>
        <div class="claim_pop">
            <h2>교환/반품 신청</h2>
            <div class="claim_pop_x">X</div>
            <form name="frmClaim" method="POST">
                <div class="goods_info">    
                    <div class="order_box1">
                        <table>
                            <colgroup>
                                <col width="7%">
                                <col width="33%">
                                <col width="12%">
                                <col width="12%">
                                <col width="12%">
                                <col width="12%">
                                <col width="12%">
                            </colgroup>
                            <tr>
                                <th colspan="2">상품정보</th>
                                <th>상품가격</th>
                                <th>배송비</th>
                                <th>수량</th>
                                <th>할인</th>
                                <th>상품금액 (할인포함)</th>
                            </tr>

                            <tr>
                                <td> <div class="product_pic" id="goods_image" style="background:url('') no-repeat; background-size:cover; background-position:center center;"></div></td>
                                <td class="text_format">
                                    <span class="span_text_format" id="goods_name"></span>          </br>
                                    <span class="span_text_format" id="opt_sticker_name"></span>    </br>
                                    <span class="span_text_format" id="opt_print_msg"></span>
                                    <!-- <i>스티커 : 농협 A타입 대<br>인쇄메세지 : 예쁘게 포장해 주세요</i> -->
                                </td><!--상품정보-->
                                <td style="text-align:center"><span class="span_num_format" id="goods_price"></span> 원</td>    <!--상품가격-->
                                <td style="text-align:center"><span class="span_num_format" id="delivery_price"></span> 원</td>    <!--배송비-->
                                <td style="text-align:center"><span class="span_num_format" id="goods_quantity"></span> 개</td>      <!--수량-->
                                <td style="text-align:center"><span class="span_num_format" id="discount_price"></span> 원</td>    <!--할인-->
                                <td style="text-align:center">
                                <span class="span_num_format" id="total_goods_price"></span> 원<!--(총)상품금액(할인포함)-->
                                </td>
                                <input type="hidden" name="cartNo[]" value="">
                                

                            </tr>  

                            <tr>
                                <td colspan='6'>

                                </td>
                                <td style="text-align:right;">
                                </td>
                            </tr>
                        </table>
                    </div><!--order_box-->                    
                </div><!--product_info_pop-->
                <div class="clear"></div>
                <div class="data_zone">
                    <table class="claim">
                        <colgroup>
                            <col width="20%">
                            <col width="30%">
                            <col width="20%">
                            <col width="30%">
                        </colgroup>
                        <tr>
                            <th>클레임 선택</th>
                            <td>
                                <input type="radio" name="claim_state" id="exchange" value="8" checked="checked">
                                <label for="exchange">교환</label>
                                <input type="radio" name="claim_state" id="refund" value="7">
                                <label for="refund">반품</label>
                            </td>
                            <th>클레임 사유</th>
                            <td>
                                <SELECT name="claim_type">
                                    <option value="0">선택</option>
                                    <option value="파손">파손</option>
                                    <option value="불량">불량</option>
                                    <option value="오배송">오배송</option>
                                    <option value="단순변심">단순변심</option>
                                </SELECT>
                            </td>
                        </tr>

                        <tr>
                            <th>클레임 메모</th>
                            <td colspan="3" ><textarea name="claim_memo" style="margin: 5px; width: 1000px; height: 70px; margin:auto;"></textarea></td>
                        </tr>

                    </table>
                <div>
                <div class="clear"></div>
                <div class="button_zone2">
                    <button type="button" class="btn_normal" onclick="js_cancel();">취소</button>
                    <button type="button" class='btn_red' onclick="js_claim();">신청</button>
                    <!-- <a style="cursor:pointer" id="btn-cart" class="cart" onclick='js_cart()' >장바구니</a>
                    <a style="cursor:pointer" id="btn-cancel" class="cart" onclick="js_cancel()" >취소</a> -->

                    <input type="hidden" name="hdGoodsNo">
                    <input type="hidden" id="hdSalePrice">
                    <input type="hidden" name="hdCntCart" value="<?=$cntCart?>">
                    <input type="hidden" name="hd_order_goods_no">
                    <input type="hidden" name="mode">

                </div><!--button_zone2-->
            </form><!--frmClaim-->
        </div><!--claim_pop-->
    </div><!--id="claim_popup"-->
</html>


