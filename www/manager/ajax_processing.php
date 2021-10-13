<?
ini_set('memory_limit',-1);
session_start();

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../_classes/com/db/DBUtil.php";
$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
// $menu_right = "CF012"; // 메뉴마다 셋팅 해 주어야 합니다
#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
// require "../_common/common_header.php"; //외부에서 볼 수 있게 하기위해 세션 체크 OFF

#=====================================================================
# common function, login_function
#=====================================================================
require "../_common/config.php";
require "../_classes/com/util/Util.php";
require "../_classes/com/etc/etc.php";
require "../_classes/biz/order/order.php";
require "../_classes/biz/confirm/confirm.php";
require "../_classes/biz/company/company.php";
require "../_classes/biz/proposal/proposal.php";
    
#=====================================================================
# functions
#=====================================================================
function insertReportingData($db,$contents,$memo,$sales_adm_no,$s_adm_no){
    $query =   "INSERT INTO TBL_COMPANY_LEDGER_REPORT_TIME(
                    CONTENTS
                    ,MEMO
                    ,SALES_ADM_NO
                    ,REG_ADM
                    ,REG_DATE
                )
                VALUES(
                    '".mysql_real_escape_string($contents)."'
                    ,'".mysql_real_escape_string($memo)."'
                    ,'".mysql_real_escape_string($sales_adm_no)."'
                    ,'".mysql_real_escape_string($s_adm_no)."'
                    ,now()
                )";
    //echo $query;
    if(!mysql_query($query,$db)){
        return false;
    } else {
        return true;
    }
}

function selectReportingTime($db, $report_time_no){
    $query =   "SELECT
                    *
                FROM
                    TBL_COMPANY_LEDGER_REPORT_TIME
                WHERE
                    REPORT_TIME_NO = '$report_time_no'
                    AND DEL_TF = 'N' AND USE_TF = 'Y'
                ";
    //echo $query;
    $result = mysql_query($query,$db);
    $record = array();
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    
    return stripslashes(unserialize(base64_decode($record[0]["CONTENTS"])));
}

function deleteReportingData($db, $report_time_no, $s_adm){
    $query =   "UPDATE TBL_COMPANY_LEDGER_REPORT_TIME
                SET DEL_TF = 'Y'
                    , DEL_ADM = '$s_adm'
                    ,DEL_DATE = now()
                WHERE REPORT_TIME_NO = '$report_time_no'
    ";
    // echo $query;
    if(mysql_query($query,$db)){
        return "true";
    } else {
        return "false";
    }
}

function makeScreenList($db, $sale_adm_no){
    $list = iconv("euc-kr","utf-8","<option>선택</option>");
    
    $query =   "SELECT *
                FROM TBL_COMPANY_LEDGER_REPORT_TIME
                WHERE SALES_ADM_NO = '$sale_adm_no'
                    AND DEL_TF = 'N'
                    AND USE_TF = 'Y'
                ORDER BY REG_DATE DESC
    ";
    
    //echo $query;
    
    $result = mysql_query($query,$db);
    $record = array();
    
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    
    for($i=0;$i<sizeof($record);$i++){
        $report_time_no     = iconv("euc-kr","utf-8",$record[$i]["REPORT_TIME_NO"]);
        $reg_date           = iconv("euc-kr","utf-8",$record[$i]["REG_DATE"]);
        $reg_adm_name       = iconv("euc-kr","utf-8",getAdminName($db, $record[$i]["REG_ADM"]));
        $memo               = iconv("euc-kr","utf-8",$record[$i]["MEMO"]);
        // $report_time_no     = iconv("euc-kr","utf-8",$record[$i]["REPORT_TIME_NO"]);
        // $reg_date           = iconv("euc-kr","utf-8",$record[$i]["REG_DATE"]);
        // $reg_adm_name       = iconv("euc-kr","utf-8",getAdminName($db, $record[$i]["REG_ADM"]));
        // $memo               = iconv("euc-kr","utf-8",trim($record[$i]["MEMO"]));
        if($memo == "")
            $list .= "<option value='".$report_time_no."'>".$reg_date." - ".$reg_adm_name."</option>";
        else
            $list .= "<option value='".$report_time_no."'>".$reg_date." - ".$reg_adm_name." - ".$memo."</option>";
    }
    
    echo $list;
}

function createGoodsLinkByGoodsNoList($db,$goods_no_list,$reg_adm){
    //배열을 문자열로 변경(구분자 ',')
    $strGoodsNo = null;
    for($i=0;$i<sizeof($goods_no_list);$i++) {
        $strGoodsNo .= $goods_no_list[$i].",";
    }
    $strGoodsNo = rtrim($strGoodsNo,",");

    //링크 생성
    $createLinkQuery = "INSERT INTO TBL_GOODS_LINK(
                            GOODS_NO_LIST
                            ,REG_ADM
                            ,REG_DATE
                        ) VALUES(
                            '".mysql_real_escape_string($strGoodsNo)."'
                            , '".mysql_real_escape_string($reg_adm)."'
                            ,now()
                        )
    ";
    
    $getLinkQuery =   "SELECT LINK_NO
                       FROM TBL_GOODS_LINK
                       WHERE GOODS_NO_LIST = '".mysql_real_escape_string($strGoodsNo)."'
    ";
    //echo $query;
    $createLinkResult = mysql_query($createLinkQuery,$db);
        
    if($createLinkResult){
        //echo $query;
        $getLinkResult = mysql_query($getLinkQuery,$db);
        $record = array();
        if ($getLinkResult <> "") {
            for($i=0;$i < mysql_num_rows($getLinkResult);$i++) {
                $record[$i] = sql_result_array($getLinkResult,$i);
            }
        }
        return $record[0]["LINK_NO"];
    } else {
        return false;
    }
}

//제안서에서 링크 생성
function createGoodsLinkByGpNo($db,$gp_no,$reg_adm){
    //링크 생성
    $createLinkQuery = "INSERT INTO TBL_GOODS_LINK(
                            GP_NO
                            ,REG_ADM
                            ,REG_DATE
                        ) VALUES(
                            '".mysql_real_escape_string($gp_no)."'
                            , '".mysql_real_escape_string($reg_adm)."'
                            ,now()
                        )
    ";
    
    $getLinkQuery =   "SELECT LINK_NO
                       FROM TBL_GOODS_LINK
                       WHERE GP_NO = '".mysql_real_escape_string($gp_no)."'
    ";
    //echo $query;
    $createLinkResult = mysql_query($createLinkQuery,$db);
        
    if($createLinkResult){
        //echo $query;
        $getLinkResult = mysql_query($getLinkQuery,$db);
        $record = array();
        if ($getLinkResult <> "") {
            for($i=0;$i < mysql_num_rows($getLinkResult);$i++) {
                $record[$i] = sql_result_array($getLinkResult,$i);
            }
        }
        return $record[0]["LINK_NO"];
    } else {
        return false;
    }
}

function selectDataByLink($db, $link_no){
    $selectGoodsNoListQuery =  "SELECT
                                    * 
                                FROM
                                    TBL_GOODS_LINK
                                WHERE
                                    LINK_NO = '$link_no'
                                    AND DEL_TF = 'N'
                                    AND USE_TF = 'Y'
    ";
    //echo $selectGoodsNoListQuery;
    //echo $selectGoodsInfoQuery;
    $result = mysql_query($selectGoodsNoListQuery,$db);
    $record = array();
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
        $strGoodsNoList = $record[0]["GOODS_NO_LIST"];
        if($strGoodsNoList != null){
            $selectGoodsInfoQuery =   "SELECT
                                            GOODS_NAME
                                            ,GOODS_CODE
                                            ,SALE_PRICE
                                            ,DELIVERY_CNT_IN_BOX
                                            ,FILE_NM_100
                                            ,FILE_RNM_150
                                            ,FILE_PATH_150
                                        FROM
                                            TBL_GOODS
                                        WHERE
                                            GOODS_NO IN ($strGoodsNoList)
            ";
            $result2 = mysql_query($selectGoodsInfoQuery,$db);
            $record2 = array();
            if ($result2 <> "") {
                for($i=0;$i < mysql_num_rows($result2);$i++) {
                    $record2[$i] = sql_result_array($result2,$i);
                }
                for($i=0;$i<sizeof($record2);$i++){
                    $record2[$i]["GOODS_NAME"] = iconv("euc-kr","utf-8",$record2[$i]["GOODS_NAME"]);
                    $record2[$i]["GOODS_CODE"] = iconv("euc-kr","utf-8",$record2[$i]["GOODS_CODE"]);
                    $record2[$i]["SALE_PRICE"] = iconv("euc-kr","utf-8",number_format($record2[$i]["SALE_PRICE"]));
                    $record2[$i]["DELIVERY_CNT_IN_BOX"] = iconv("euc-kr","utf-8",number_format($record2[$i]["DELIVERY_CNT_IN_BOX"]));
                    $record2[$i]["FILE_NM_100"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_NM_100"]);
                    $record2[$i]["FILE_RNM_150"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_RNM_150"]);
                    $record2[$i]["FILE_PATH_150"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_PATH_150"]);
                }
                // echo $selectGoodsInfoQuery;
            } else {
                return false;
            }
        } else {
            $gp_no = $record[0]["GP_NO"];
            $cancel_tf = "N";
            $selectGoodsInfoQuery = "SELECT
                                        G.GOODS_CODE
                                        ,G.GOODS_NAME
                                        ,GPG.RETAIL_PRICE AS SALE_PRICE
                                        ,GPG.PROPOSAL_PRICE
                                        ,GPG.DELIVERY_CNT_IN_BOX
                                        ,G.FILE_NM_100
                                        ,G.FILE_RNM_150
                                        ,G.FILE_PATH_150
                                    FROM
                                        TBL_PROPOSAL_SUB GPG 
                                        JOIN TBL_GOODS G ON GPG.GOODS_NO = G.GOODS_NO
                                    WHERE
                                        GPG.GP_NO = '$gp_no'
                                        AND GPG.DEL_TF = 'N'
                                        AND GPG.CANCEL_TF = '".$cancel_tf."'
            ";
            $result2 = mysql_query($selectGoodsInfoQuery,$db);
            $record2 = array();
            if ($result2 <> "") {
                for($i=0;$i < mysql_num_rows($result2);$i++) {
                    $record2[$i] = sql_result_array($result2,$i);
                }
                for($i=0;$i<sizeof($record2);$i++){
                    $record2[$i]["GOODS_NAME"] = iconv("euc-kr","utf-8",$record2[$i]["GOODS_NAME"]);
                    $record2[$i]["GOODS_CODE"] = iconv("euc-kr","utf-8",$record2[$i]["GOODS_CODE"]);
                    $record2[$i]["SALE_PRICE"] = iconv("euc-kr","utf-8",number_format($record2[$i]["SALE_PRICE"]));
                    $record2[$i]["PROPOSAL_PRICE"] = iconv("euc-kr","utf-8",number_format($record2[$i]["PROPOSAL_PRICE"]));
                    $record2[$i]["DELIVERY_CNT_IN_BOX"] = iconv("euc-kr","utf-8",number_format($record2[$i]["DELIVERY_CNT_IN_BOX"]));
                    $record2[$i]["FILE_NM_100"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_NM_100"]);
                    $record2[$i]["FILE_RNM_150"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_RNM_150"]);
                    $record2[$i]["FILE_PATH_150"] = iconv("euc-kr","utf-8",$record2[$i]["FILE_PATH_150"]);
                }
                // echo $selectGoodsInfoQuery;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    return $record2;
}

//무한 스크롤을 위해 confirm에 있는 listCompanyLedger에 offset만 추가
function listCompanyLedgerAddOffset($db, $start_date, $end_date, $cp_no, $order_field = "", $order_str = "", $search_field = "", $search_str = "", $nRowCount = 10000, $offset = 0) {
    $query =   "SELECT
                    CL_NO
                    ,CP_NO
                    ,TO_CP_NO
                    ,INOUT_DATE
                    ,INOUT_TYPE
                    ,GOODS_NO
                    ,NAME
                    ,QTY
                    ,UNIT_PRICE
                    ,ROUND(WITHDRAW, 0) AS WITHDRAW
                    ,ROUND(DEPOSIT, 0) AS DEPOSIT
                    ,SURTAX
                    ,MEMO
                    ,RESERVE_NO
                    ,ORDER_GOODS_NO
                    ,RGN_NO
                    ,TAX_CONFIRM_TF
                    ,TAX_CONFIRM_DATE
                    ,USE_TF
                    ,CATE_01
                    ,TAX_TF
                    ,CF_CODE
                    ,INPUT_TYPE
                FROM
                    TBL_COMPANY_LEDGER
                WHERE
                    DEL_TF = 'N' ";
    
    if ($start_date <> "") {
        $query .= " AND INOUT_DATE >= '".$start_date."' ";
    }

    if ($end_date <> "") {
        $query .= " AND INOUT_DATE <= '".$end_date." 23:59:59' ";
    }

    if ($cp_no <> "") {
        $query .= " AND CP_NO = '".$cp_no."' ";
    }

    if($search_field == "LATEST_5_BY_REG_ADM") { 
        $query .= " AND REG_ADM =  ".$search_str." AND INOUT_TYPE IN ('입금', '지급', '대체', '대입') ";
    }

    if($order_field  == "" && $order_str == "") 
        $query .= " ORDER BY INOUT_DATE ASC, REG_DATE ASC, ORDER_GOODS_NO ASC, CL_NO ASC ";
    else
    {
        if ($order_field == "") 
            $order_field = "INOUT_DATE";

        if ($order_str == "") 
            $order_str = "ASC";

        $query .= " ORDER BY ".$order_field." ".$order_str;
    }

    $query .= " limit " . $offset . ", " . $nRowCount;

    // echo $query;

    $result = mysql_query($query,$db);
    $record = array();

    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }

    for($i=0;$i<sizeof($record);$i++){
        $record[$i]["MEMO"] = iconv("euc-kr","utf-8",$record[$i]["MEMO"]);
        $record[$i]["NAME"] = iconv("euc-kr","utf-8",$record[$i]["NAME"]);
        $record[$i]["INOUT_TYPE"] = iconv("euc-kr","utf-8",$record[$i]["INOUT_TYPE"]);
        $record[$i]["CP_NO"] = iconv("euc-kr","utf-8",getCompanyNameWithNoCode($db,$record[$i]["CP_NO"]));
        $record[$i]["TO_CP_NO"] = iconv("euc-kr","utf-8",getCompanyNameWithNoCode($db,$record[$i]["TO_CP_NO"]));
    }
    return $record;
}

function select_goods_code($db, $code){
    $query =   "SELECT GOODS_CODE 
                FROM TBL_GOODS
                WHERE GOODS_CODE = '".mysql_real_escape_string($code)."'";
    
    // echo $query;
    
    $result = mysql_query($query,$db);
    $rows   = mysql_fetch_array($result);
    $record  = $rows[0];
    
    if($record != ""){
        return true;
    }else{
        return false;
    }
}

function updateReason($db, $seq_no, $reason){
    $query =   "UPDATE TBL_GOODS_PRICE_CHANGE
                        SET REASON = '$reason'
                        WHERE SEQ_NO = '$seq_no'
    ";

    //echo $query;

    if(mysql_query($query,$db)){
        return "true";
    } else {
        return "false";
    }
}

function updateCurrentReason($db, $goods_no, $reason){
    $query =   "UPDATE TBL_GOODS
                        SET REASON = '$reason'
                        WHERE GOODS_NO = '$goods_no'
    ";

    // echo $query;
    // exit;

    if(mysql_query($query,$db)){
        return "true";
    } else {
        return "false";
    }
}

function getGoodsInfo($db, $goods_code){
    $query =   "SELECT A.GOODS_NO, A.GOODS_CODE, B.SALE_PRICE
                        FROM TBL_GOODS A LEFT JOIN TBL_GOODS_PRICE B
                            ON A.GOODS_NO = B.GOODS_NO AND B.CP_NO = '1480'
                        WHERE A.GOODS_CODE = '".mysql_real_escape_string($goods_code)."'
                            AND A.DEL_TF = 'N'
    ";
    
    // echo $query;
    
    $result = mysql_query($query,$db);
    if ($result <> ""){
        for($i=0;$i < mysql_num_rows($result);$i++){
            $record[$i] = sql_result_array($result,$i);
        }
    }

    if($record != ""){
        return $record;
    }else{
        return false;
    }
}

function updateTblGoodsPrice($db, $goods_no, $sale_price, $up_adm){
    $query =   "UPDATE TBL_GOODS_PRICE
                        SET SALE_PRICE = '".mysql_real_escape_string($sale_price)."',
                            UP_ADM = '".mysql_real_escape_string($up_adm)."',
                            UP_DATE = now()
                        WHERE GOODS_NO = '".mysql_real_escape_string($goods_no)."'
    ";

    //echo $query;

    if(mysql_query($query,$db)){
        return true;
    } else {
        return false;
    }
}

function insertTblGoodsPrice($db, $goods_no, $sale_price, $reg_adm){
    //상품가격정보를 등록하기 위해서 MRO엑셀 파일만으로는 부족한 정보를 기존 상품 정보 테이블에서 가져옴
    $query1 =    "SELECT * 
                        FROM TBL_GOODS 
                        WHERE GOODS_NO = '$goods_no' 
                            AND DEL_TF = 'N'
    ";
    
    $result1 = mysql_query($query1,$db);
    $record1 = array();
    if ($result1 <> "") {
        for($i=0;$i < mysql_num_rows($result1);$i++) {
            $record1[$i] = sql_result_array($result1,$i);
        }
    }

    $BUY_PRICE = $record1[0]["BUY_PRICE"];
    $PRICE = $record1[0]["PRICE"];
    $STICKER_PRICE = $record1[0]["STICKER_PRICE"];
    $PRINT_PRICE = $record1[0]["PRINT_PRICE"];
    $DELIVERY_PRICE = $record1[0]["DELIVERY_PRICE"];
    $DELIVERY_CNT_IN_BOX = $record1[0]["DELIVERY_CNT_IN_BOX"];
    $LABOR_PRICE = $record1[0]["LABOR_PRICE"];
    $OTHER_PRICE = $record1[0]["OTHER_PRICE"];
    $SALE_SUSU = $record1[0]["SALE_SUSU"];
    $CP_SALE_SUSU = $record1[0]["CP_SALE_SUSU"];
    $CP_SALE_PRICE = $record1[0]["CP_SALE_PRICE"];

    //완전히 채워진 정보를 이용해서 상품가격정보 등록
    $query =   "INSERT INTO TBL_GOODS_PRICE(
                            GOODS_NO
                            ,CP_NO
                            ,BUY_PRICE
                            ,SALE_PRICE
                            ,PRICE
                            ,STICKER_PRICE
                            ,PRINT_PRICE
                            ,DELIVERY_PRICE
                            ,DELIVERY_CNT_IN_BOX
                            ,LABOR_PRICE
                            ,OTHER_PRICE
                            ,SALE_SUSU
                            ,CP_SALE_SUSU
                            ,CP_SALE_PRICE
                            ,USE_TF
                            ,REG_ADM
                            ,REG_DATE
                        ) VALUES (
                            '".mysql_real_escape_string($goods_no)."'
                            ,'1480'
                            ,'".mysql_real_escape_string($BUY_PRICE)."'
                            ,'".mysql_real_escape_string($sale_price)."'
                            ,'".mysql_real_escape_string($PRICE)."'
                            ,'".mysql_real_escape_string($STICKER_PRICE)."'
                            ,'".mysql_real_escape_string($PRINT_PRICE)."'
                            ,'".mysql_real_escape_string($DELIVERY_PRICE)."'
                            ,'".mysql_real_escape_string($DELIVERY_CNT_IN_BOX)."'
                            ,'".mysql_real_escape_string($LABOR_PRICE)."'
                            ,'".mysql_real_escape_string($OTHER_PRICE)."'
                            ,'".mysql_real_escape_string($SALE_SUSU)."'
                            ,'".mysql_real_escape_string($CP_SALE_SUSU)."'
                            ,'".mysql_real_escape_string($CP_SALE_PRICE)."'
                            ,'Y'
                            ,'".mysql_real_escape_string($reg_adm)."'
                            ,now()
                        )
    ";

    // echo $query;
    
    if(mysql_query($query,$db)){
        return true;
    } else {
        return false;
    }
}

function selectCompanyNo($db, $search_type, $search_str){
    $query =    "SELECT CP_NO, CP_NM, CP_NM2, BIZ_NO, CEO_NM
                        FROM TBL_COMPANY
                        WHERE (CP_TYPE = '구매' or CP_TYPE = '판매공급')
    ";

    //유효성 검사
    if($search_type == "" || $search_str == ""){
        return false;
    }

    if($search_type == "1"){
        $query .= " AND (REPLACE(BIZ_NO, '-', '') = '$search_str' OR BIZ_NO = '$search_str' )";
    } else if($search_type == "2"){
        $query .= " AND CP_NM LIKE '%$search_str%' ";
    } else {
        return false;
    }
    
    // echo iconv("euckr","utf8",$query);
    
    $result = mysql_query($query,$db);
    $record = array();
    
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }

    for($i=0;$i<count($record);$i++){
        $record[$i]["CP_NM"] = iconv("euc-kr","utf-8",$record[$i]["CP_NM"]);
        $record[$i]["CP_NM2"] = iconv("euc-kr","utf-8",$record[$i]["CP_NM2"]);
        $record[$i]["CEO_NM"] = iconv("euc-kr","utf-8",$record[$i]["CEO_NM"]);
    }

    $cp_list = array();
    for($i=0;$i<count($record);$i++){
        $cp_no  = $record[$i]["CP_NO"];
        $cp_nm  = $record[$i]["CP_NM"];
        $cp_nm2 = $record[$i]["CP_NM2"];
        $biz_no = $record[$i]["BIZ_NO"];
        $ceo_nm = $record[$i]["CEO_NM"];
        array_push($cp_list, array("cp_no" => $cp_no, "cp_nm" => $cp_nm, "cp_nm2" => $cp_nm2, "biz_no" => $biz_no, "ceo_nm" => $ceo_nm));
    }

    return $cp_list;
}
function changeExposureStatusOfGood($db, $goodNo, $exposureTF){
    $query = "UPDATE TBL_GOODS SET EXPOSURE_TF = '".$exposureTF."'  WHERE GOODS_NO = ".$goodNo." ; ";
    $result = mysql_query($query, $db);
    return $result;
}
function changeConcealStatusOfGood($db, $goodsNo, $concealTF){
    $query = "UPDATE TBL_GOODS SET CONCEAL_PRICE_TF = '".$concealTF."'  WHERE GOODS_NO = ".$goodsNo." ; ";
    $result = mysql_query($query, $db);
    return $result;
}

function isGoodsExist($db,$proposal_no){
    $query ="SELECT GOODS_NO
                    FROM TBL_MEMBER_PROPOSAL
                    WHERE PROPOSAL_NO = '$proposal_no'
    ";

    // echo $query;
    
    $result = mysql_query($query,$db);
    $rows   = mysql_fetch_array($result);
    $record  = $rows[0];
    
    if($record != ""){
        return $record;
    }else{
        return false;
    }
}

function deleteCounsel($db,$seq_no,$del_adm){
    $query ="UPDATE TBL_COUNSEL
                    SET
                        DEL_ADM = '$del_adm'
                        ,DEL_DATE = now()
                        ,DEL_TF = 'Y'
                    WHERE
                        SEQ_NO = '$seq_no'
    ";
    // echo $query;
    if(!mysql_query($query,$db)){
        return false;
    } else {
        return true;
    }
}
function updateAccessTime($db,$arrGoodsNo){
    $query="UPDATE TBL_GOODS SET UP_DATE = NOW() WHERE GOODS_NO IN(".$arrGoodsNo.") ";
    $result=mysql_query($query,$db);
    // echo $query;
    // exit;
    if($result<>"") return 1;
    else return 0;

}
function overwriteDeliveryNo($db, $deliverySeq, $deliveryNo){
    $query="UPDATE TBL_ORDER_GOODS_DELIVERY SET DELIVERY_NO = '".$deliveryNo."' WHERE DELIVERY_SEQ =".$deliverySeq." ; ";
    $result=mysql_query($query, $db);
    if($result<>""){
        return true;
    }
    else{
        return false;
    }
}
#====================================================================
# Request Parameter
#====================================================================
$mode = trim($_POST["mode"]);

#=====================================================================
# code
#=====================================================================
// if($mode="UPDATE_ACCESS_TIME"){
//     $goods_no=  $_POST['goods_no'];
//     if(updateAccessTime($conn, $goods_no)) return 1;
//     else return 0;
// }
if($mode=="OVERWRITE_DELIVERY_NO"){
    $arrDeliveryIdx=$_POST['arrDeliveryIdx'];
    $cnt=sizeof($arrDeliveryIdx);
    $arrDelivery = array();
    $strError="";
    for($i=0; $i<$cnt; $i++){
        $arrDelivery[$i]=explode("/",$arrDeliveryIdx[$i]);
        if(!overwriteDeliveryNo($conn, $arrDelivery[$i][0],$arrDelivery[1])){
            $strError.=$arrDelivery[$i][0].", ";
        }
    }
    if($strError<>""){
        $strError=rtrim($strError,", ");
        return $strError;
    }
    else{
        return 0;
    }
    
}
if($mode=="UPDATE_ACCESS_TIME"){
    $arr_goods_no = $_POST['goods_nos'];
    $cnt=sizeof($arr_goods_no);
    $strGoodsNo="";
    for($i = 0; $i < $cnt; $i++){
        $strGoodsNo.=$arr_goods_no[$i].",";
    }
    $strGoodsNo=rtrim($strGoodsNo, ",");
    if(updateAccessTime($conn,$strGoodsNo)==1){
        echo 1;
    }
    else echo 3;


}
if($mode == "CHANGE_EXPOSURE_STATUS"){
    $exposureTF =   $_POST['exposureTF'];
    $goodNo=        $_POST['goodNo'];
    if(changeExposureStatusOfGood($conn, $goodNo, $exposureTF)) echo 1;
    else echo 0;
}
if($mode == "CHANGE_CONCEAL_STATUS"){
    $concealTF =   $_POST['concealTF'];
    $goodsNo=        $_POST['goodsNo'];
    if(changeConcealStatusOfGood($conn, $goodsNo, $concealTF)) echo 1;
    else echo 0;
}
if($mode == "SELECT_SCREEN"){
    $report_time_no = $_POST["report_time_no"];
    echo selectReportingTime($conn, $report_time_no);
}

if($mode == "DELETE_SCREEN"){
    $report_time_no     = $_POST["report_time_no"];
    $s_adm              = $_POST["s_adm"];
    
    if(deleteReportingData($conn, $report_time_no, $s_adm)){
        echo "true";
    } else {
        echo "false";
    }
}

if($mode == "INSERT_SCREEN"){
    $contents       = $_POST["contents"];
    $memo           = $_POST["memo"];
    $sales_adm_no   = $_POST["sales_adm_no"];
    $s_adm_no       = $_POST["s_adm_no"];
    
    if(insertReportingData($conn, base64_encode(serialize($contents)), iconv("utf-8","euc-kr",$memo), iconv("utf-8","euc-kr",$sales_adm_no), iconv("utf-8","euc-kr",$s_adm_no))){
        echo "true";
    } else {
        echo "false";
    }
}

if($mode == "MAKE_SELECT"){
    $sales_adm_no   = $_POST["sales_adm_no"];
    echo makeScreenList($conn, $sales_adm_no);
}

if($mode == "INSERT_LINK"){
    $goods_no_list = $_POST["goods_no"];
    $gp_no = $_POST["gp_no"];
    $reg_adm = $_POST["reg_adm"];
    
    if($gp_no != ""){
        $result = createGoodsLinkByGpNo($conn, $gp_no, $reg_adm);
    } else{
        $result = createGoodsLinkByGoodsNoList($conn, $goods_no_list, $reg_adm);
    }

    if($result != false){
        echo $result;
    } else {
        echo false;
    }
}

if($mode == "SELECT_DATA_BY_LINK"){
    $link_no = $_POST["link_no"];
    echo json_encode(selectDataByLink($conn, $link_no));
}

if($mode == "SELECT_RECENT_LEDGER_HISTORY"){
    $offset         = $_POST["offset"];
    $s_adm_no       = $_POST["s_adm_no"];
    $start_date     = $_POST["start_date"];
    $end_date       = $_POST["end_date"];
    $cp_type        = $_POST["cp_type"];
    $order_field    = $_POST["order_field"];
    $order_str      = $_POST["order_str"];
    $search_field   = $_POST["search_field"];
    $nRowCount      = $_POST["nRowCount"];
    echo json_encode(listCompanyLedgerAddOffset($conn, $start_date, $end_date, $cp_no, $order_field, $order_str, $search_field, $s_adm_no, $nRowCount, $offset));
}

if($mode == "EXCEL_FILE_READ"){
    require_once "../_common/config.php";
    require_once "../_PHPExcel/Classes/PHPExcel.php";
    require_once "../_PHPExcel/Classes/PHPExcel/IOFactory.php";
    
    $file           = $_FILES['file'];
    $file_path      = $g_physical_path."upload_data/temp_goods";
    $file_nm		= upload($file, $file_path, 10000 , array('xls','xlsx'));
    $filename 		= '../upload_data/temp_goods/'.$file_nm;
    $objReader 		= PHPExcel_IOFactory::createReaderForFile($filename);
    $objReader 		-> setReadDataOnly(true);
    $objExcel 		= $objReader->load($filename);
    $objExcel 		-> setActiveSheetIndex(0);
    $objWorksheet 	= $objExcel->getActiveSheet();
    $rowIterator 	= $objWorksheet->getRowIterator();

    foreach ($rowIterator as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); 
    }

    $maxRow = $objWorksheet->getHighestRow();
    $arr_mro_info = array();
    
    for ($i = 2 ; $i <= $maxRow ; $i++) {
        //MRO 판매가 수정에 필요한 항목(전시상품명, 공급처상품코드, 판매가)만 사용
        $goods_name				= trim($objWorksheet->getCell('B' . $i)->getValue());//전시상품명
        $mro_sale_price			= trim($objWorksheet->getCell('C' . $i)->getValue());//판매가
        $goods_code				= trim($objWorksheet->getCell('E' . $i)->getValue());//공급처상품코드
        array_push($arr_mro_info, array("goods_name" => $goods_name,"mro_sale_price" => $mro_sale_price, "goods_code" => $goods_code));
    }
    echo json_encode($arr_mro_info);
}

if($mode == "SELECT_GOODS_CODE"){
    $code = trim($_POST["code"]);
    $result = array("exist" => select_goods_code($conn, $code));
    echo json_encode($result);
}

if($mode == "UPDATE_MRO_SALE_PRICE"){
    //goods_code 있는지 확인
        //Y : 가격관리에 MRO로 등록되어 있는지 확인
            //Y : 가격 업데이트
            //N : 가격관리에 신규 등록, 나머지 가격 정보는 기존 상품 정보에서 가져옴
        //N : 상품코드 확인불가 에러 리턴
    $s_adm_no  = trim($_POST["s_adm_no"]);
    $data = $_POST["data"];

    $insert_cnt = 0;
    $update_cnt = 0;
    $error_cnt = 0;
    $nochange_cnt = 0;
    $update_result = array();
    $result = array();
    
    //goods_code 있는지 확인
    for($i=0;$i < sizeof($data);$i++) {
        $code = trim($data[$i]["code"]);
        $mro_sale_price = trim($data[$i]["mro_sale_price"]);//mro에서 내려받은 판매가격

        $goods = getGoodsInfo($conn, $code);
        $goods_code = $goods[0]["GOODS_CODE"];
        $sale_price = $goods[0]["SALE_PRICE"];//전산상의 mro 판매가격
        $goods_no = $goods[0]["GOODS_NO"];
        $state = "";

        if($goods_code == null){
            //존재하지 않는 상품코드
            $state = "상품코드확인불가";
            $error_cnt++;
        } else if($sale_price == null){
            //상품가격관리에 정보 없음
            $state = "신규등록";
            $insert_cnt++;
        } else if($goods_code != "" && $sale_price != ""){
            //상품가격관리에 정보 있음
            //기존 가격과 다르면 수정
            if($mro_sale_price != $sale_price){
                //수정
                $state = "수정";
                $update_cnt++;
            } else {
                //변동사항 없음
                $state = "가격전과동일";
                $nochange_cnt++;
            }
        } else {
            //알 수 없는 오류
            $state = "알수없는오류";
            $error_cnt++;
        }

        //처리
        if($state == "신규등록"){
            if(!insertTblGoodsPrice($conn, $goods_no, $mro_sale_price, $s_adm_no)){
                $state = "DB처리오류";
                $error_cnt++;
                $insert_cnt--;
            }
        } else if($state == "수정"){
            if(!updateTblGoodsPrice($conn, $goods_no, $mro_sale_price, $s_adm_no)){
                $state = "DB처리오류";
                $error_cnt++;
                $update_cnt--;
            }
        }

        $state = iconv("euckr","utf8",$state);

        //처리 상태 배열에 추가
        array_push($result, array("goods_code" => $goods_code, "state" => $state));
    }

    //전체 처리 결과 배열에 추가
    array_push($update_result, array("insert" => $insert_cnt, "update" => $update_cnt, "error" => $error_cnt, "nochange" => $nochange_cnt, "result" => $result));
    
    //처리 결과 리턴
    echo json_encode($update_result);
}

if($mode == "UPDATE_REASON"){
    $seq_no     = trim($_POST["seq_no"]);
    $reason      = iconv("utf-8","euc-kr",trim($_POST["reason"]));
    
    if(updateReason($conn, $seq_no, $reason)){
        echo "true";
    } else {
        echo "false";
    }
}

if($mode == "UPDATE_CURRENT_REASON"){
    // echo "UPDATE_CURRENT_REASON";
    // exit;
    $goods_no   = trim($_POST["goods_no"]);
    $reason         = iconv("utf-8","euc-kr",trim($_POST["reason"]));
    
    if(updateCurrentReason($conn, $goods_no, $reason)){
        echo "true";
    } else {
        echo "false";
    }
}

if($mode == "SELECT_COMPANY_NO"){
    $search_type = iconv("utf-8","euc-kr",trim($_POST["search_type"]));
    $search_str = iconv("utf-8","euc-kr",trim($_POST["search_str"]));
    
    $result = selectCompanyNo($conn, $search_type, $search_str);
    if($result != false){
        echo json_encode($result);
    } else {
        echo json_encode(false);
    }
}

if($mode == "SELECT_CATE_LEVEL3"){
    $parents_cate_cd = trim($_POST["cate_cd"]);
    $list = iconv("euc-kr","utf-8","<option>선택</option>");
    
    $query =    "SELECT CATE_CD, REPLACE(CATE_NAME,'단품 ','') AS CATE_NAME
                        FROM TBL_CATEGORY
                        WHERE LENGTH(CATE_CD) = '6'
                            AND CATE_CD LIKE '$parents_cate_cd%'
                            AND DEL_TF = 'N'
                            AND USE_TF = 'Y'
                        ORDER BY CATE_SEQ03 ASC
    ";
    
    //echo $query;
    
    $result = mysql_query($query,$conn);
    $record = array();
    
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }
    
    for($i=0;$i<sizeof($record);$i++){
        $cate_name = iconv("euc-kr","utf-8",$record[$i]["CATE_NAME"]);
        $cate_cd = iconv("euc-kr","utf-8",$record[$i]["CATE_CD"]);
        $list .= "<option value='".$cate_cd."'>".$cate_name."</option>";
    }
    
    echo $list;
}

if($mode == "INSERT_MEMBER_PROPOSAL"){
    $mem_no = $_POST['mem_no'];
    $normal_file = $_FILES['normal_file'];
    $detail_file = $_FILES['detail_file'];
    $goods_cate = $_POST['goods_cate'];
    $goods_name = iconv("utf8","euckr",$_POST['goods_name']);
    $goods_sub_name = iconv("utf8","euckr",$_POST['goods_sub_name']);
    $cate_02 = iconv("utf8","euckr",$_POST['cate_02']);
    $cate_03 = $_POST['cate_03'];
    $cate_04 = iconv("utf8","euckr",$_POST['cate_04']);
    $price = $_POST['price'];
    $buy_price = $_POST['buy_price'];
    $extra_price = $_POST['extra_price'];
    $contents = iconv("utf8","euckr",$_POST['contents']);
    $memo = iconv("utf8","euckr",$_POST['memo']);
    $delivery_cnt_in_box = $_POST['delivery_cnt_in_box'];
    $sticker_price = $_POST['sticker_price'];
    $print_price = $_POST['print_price'];
    $delivery_price = $_POST['delivery_price'];
    $labor_price = $_POST['labor_price'];
    $other_price = $_POST['other_price'];
    $mem_nm = iconv("utf8","euckr",$_POST['mem_nm']);
    $goods_no = $_POST['goods_no'];
    $proposal_no = $_POST['proposal_no'];

    if($goods_no != ""){
        //goods_no
        //상품 번호로 중복 유무 확인
        $query =    "SELECT *
                            FROM TBL_MEMBER_PROPOSAL
                            WHERE CATE_03 = '$cate_03'
                                AND GOODS_NO = '$goods_no'
                                AND DEL_TF = 'N'
        ";
    }else if($proposal_no != ""){
        //proposal_no
        //제안 번호로 중복 유무 확인
        $query =    "SELECT *
                            FROM TBL_MEMBER_PROPOSAL
                            WHERE CATE_03 = '$cate_03'
                                AND PROPOSAL_NO = '$proposal_no'
                                AND DEL_TF = 'N'
        ";
    } else{
        //goods_name & goods_sub_name
        //상품명과 상품옵션으로 중복 유무 확인
        $query =    "SELECT *
                            FROM TBL_MEMBER_PROPOSAL
                            WHERE CATE_03 = '$cate_03'
                                AND GOODS_NAME = '$goods_name'
                                AND GOODS_SUB_NAME = '$goods_sub_name'
                                AND DEL_TF = 'N'
        ";
    }

    // echo $query;
    
    $result = mysql_query($query,$conn);
    $record = array();
    
    if ($result <> "") {
        for($i=0;$i < mysql_num_rows($result);$i++) {
            $record[$i] = sql_result_array($result,$i);
        }
    }

    if(count($record) == 1){
        //update
        $query = "UPDATE TBL_MEMBER_PROPOSAL
                         SET
                            GOODS_CATE = '$goods_cate'
                            ,GOODS_NAME = '$goods_name'
                            ,GOODS_SUB_NAME = '$goods_sub_name'
                            ,CATE_02 = '$cate_02'
                            ,CATE_03 = '$cate_03'
                            ,CATE_04 = '$cate_04'
                            ,PRICE = '$price'
                            ,BUY_PRICE = '$buy_price'
                            ,EXTRA_PRICE = '$extra_price'
                            ,NORMAL_FILE_NM = ''
                            ,DETAIL_FILE_NM = ''
                            ,CONTENTS = '$contents'
                            ,MEMO = '$memo'
                            ,DELIVERY_CNT_IN_BOX = '$delivery_cnt_in_box'
                            ,REG_ADM = '69'
                            ,REG_DATE = now()
                            ,STICKER_PRICE = '$sticker_price'
                            ,PRINT_PRICE = '$print_price'
                            ,DELIVERY_PRICE = '$delivery_price'
                            ,LABOR_PRICE = '$labor_price'
                            ,OTHER_PRICE = '$other_price'
                            ,REASON = CONCAT('$mem_nm ',DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s'))
                         WHERE DEL_TF = 'N'
                            AND PROPOSAL_NO = '".$record[0]["PROPOSAL_NO"]."'
        ";

        if(!mysql_query($query,$conn)){
            echo "update fail";
        } else {
            echo "update";
        }
    } else {
        //insert
        if($goods_no != ""){
            $query ="INSERT INTO TBL_MEMBER_PROPOSAL(
                GOODS_CATE
                ,GOODS_NAME
                ,GOODS_SUB_NAME
                ,CATE_02
                ,CATE_03
                ,CATE_04
                ,PRICE
                ,BUY_PRICE
                ,EXTRA_PRICE
                ,NORMAL_FILE_NM
                ,DETAIL_FILE_NM
                ,CONTENTS
                ,MEMO
                ,DELIVERY_CNT_IN_BOX
                ,REG_ADM
                ,REG_DATE
                ,STICKER_PRICE
                ,PRINT_PRICE
                ,DELIVERY_PRICE
                ,LABOR_PRICE
                ,OTHER_PRICE
                ,REASON
                ,GOODS_NO
            ) VALUES (
                '$goods_cate'
                ,'$goods_name'
                ,'$goods_sub_name'
                ,'$cate_02'
                ,'$cate_03'
                ,'$cate_04'
                ,'$price'
                ,'$buy_price'
                ,'$extra_price'
                ,''
                ,''
                ,'$contents'
                ,'$memo'
                ,'$delivery_cnt_in_box'
                ,'69'
                ,now()
                ,'$sticker_price'
                ,'$print_price'
                ,'$delivery_price'
                ,'$labor_price'
                ,'$other_price'
                ,CONCAT('$mem_nm ',DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s'))
                ,$goods_no
            )
        ";
        } else {
            $query ="INSERT INTO TBL_MEMBER_PROPOSAL(
                                GOODS_CATE
                                ,GOODS_NAME
                                ,GOODS_SUB_NAME
                                ,CATE_02
                                ,CATE_03
                                ,CATE_04
                                ,PRICE
                                ,BUY_PRICE
                                ,EXTRA_PRICE
                                ,NORMAL_FILE_NM
                                ,DETAIL_FILE_NM
                                ,CONTENTS
                                ,MEMO
                                ,DELIVERY_CNT_IN_BOX
                                ,REG_ADM
                                ,REG_DATE
                                ,STICKER_PRICE
                                ,PRINT_PRICE
                                ,DELIVERY_PRICE
                                ,LABOR_PRICE
                                ,OTHER_PRICE
                                ,REASON
                            ) VALUES (
                                '$goods_cate'
                                ,'$goods_name'
                                ,'$goods_sub_name'
                                ,'$cate_02'
                                ,'$cate_03'
                                ,'$cate_04'
                                ,'$price'
                                ,'$buy_price'
                                ,'$extra_price'
                                ,''
                                ,''
                                ,'$contents'
                                ,'$memo'
                                ,'$delivery_cnt_in_box'
                                ,'69'
                                ,now()
                                ,'$sticker_price'
                                ,'$print_price'
                                ,'$delivery_price'
                                ,'$labor_price'
                                ,'$other_price'
                                ,CONCAT('$mem_nm ',DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i:%s'))
                            )
            ";
        }
        

        if(!mysql_query($query,$conn)){
            echo "insert fail";
        } else {
            echo "insert";
        }

        // echo "$query";
    }
    // insert or upload
    //ID받음
    //ID를 파일명 앞에 넣어서 UPLOAD or DB저장
    //
}

if($mode == "ACCEPT_PROPOSAL"){
    $acceptor = $_POST["acceptor"];
    $proposal_nos = $_POST["data"];
    
    $proposal_nos_str ="";
    for($i=0;$i<sizeof($proposal_nos);$i++){
        $proposal_no = $proposal_nos[$i]["proposal_no"];
        
        //2.신규인지 수정인지 확인
       $temp_goods_no = isGoodsExist($conn, $proposal_no);
        if($temp_goods_no){
            //수정
            $query ="UPDATE TBL_GOODS A JOIN TBL_MEMBER_PROPOSAL B
                                ON
                                    A.GOODS_NO = '$temp_goods_no'
                                    AND B.PROPOSAL_NO = '$proposal_no'
                                    AND A.GOODS_NO = B.GOODS_NO
                            SET
                                A.GOODS_CATE = B.GOODS_CATE
                                ,A.GOODS_NAME = B.GOODS_NAME
                                ,A.GOODS_SUB_NAME = B.GOODS_SUB_NAME
                                ,A.CATE_02 = B.CATE_02
                                ,A.CATE_03 = B.CATE_03
                                ,A.CATE_04 = B.CATE_04
                                ,A.PRICE = B.PRICE
                                ,A.BUY_PRICE = B.BUY_PRICE
                                ,A.EXTRA_PRICE = B.EXTRA_PRICE
                                ,A.CONTENTS = B.CONTENTS
                                ,A.MEMO = B.MEMO
                                ,A.DELIVERY_CNT_IN_BOX = B.DELIVERY_CNT_IN_BOX
                                ,A.STICKER_PRICE = B.STICKER_PRICE
                                ,A.PRINT_PRICE = B.PRINT_PRICE
                                ,A.DELIVERY_PRICE = B.DELIVERY_PRICE
                                ,A.LABOR_PRICE = B.LABOR_PRICE
                                ,A.OTHER_PRICE = B.OTHER_PRICE
                                ,A.REASON = B.REASON
                                ,A.UP_ADM = B.ACCEPTOR
                                ,A.UP_DATE = B.ACCPT_DATE
                                -- ,A.FILE_RNM_150 = 'B.NORMAL_FILE_NM'
                                -- ,A.FILE_PATH_150 = 'B.NORMAL_FILE_PATH'
            ";
            echo "$query";
            mysql_query($query,$conn);
        } else {
            //신규
            //3.1상품등록
            $query = "INSERT INTO TBL_GOODS(GOODS_CATE,GOODS_NAME,GOODS_SUB_NAME,CATE_02,CATE_03,CATE_04,PRICE,BUY_PRICE,EXTRA_PRICE,CONTENTS,MEMO,DELIVERY_CNT_IN_BOX,STICKER_PRICE,PRINT_PRICE,DELIVERY_PRICE,LABOR_PRICE,OTHER_PRICE,REASON,REG_ADM,REG_DATE,FILE_RNM_150,FILE_PATH_150
                            )SELECT
                                GOODS_CATE
                                ,GOODS_NAME
                                ,GOODS_SUB_NAME
                                ,CATE_02
                                ,CATE_03
                                ,CATE_04
                                ,PRICE
                                ,BUY_PRICE
                                ,EXTRA_PRICE
                                ,CONTENTS
                                ,MEMO
                                ,DELIVERY_CNT_IN_BOX
                                ,STICKER_PRICE
                                ,PRINT_PRICE
                                ,DELIVERY_PRICE
                                ,LABOR_PRICE
                                ,OTHER_PRICE
                                ,REASON
                                ,ACCEPTOR
                                ,NOW()
                                ,NORMAL_FILE_NM
                                ,NORMAL_FILE_PATH
                            FROM TBL_MEMBER_PROPOSAL
                            WHERE PROPOSAL_NO = '$proposal_no'
                                AND DEL_TF = 'N'
            ";
            echo "$query";
            mysql_query($query,$conn);
        }

        //4.1이력추가
        // $query =    "INSERT INTO TBL_GOODS_PRICE_CHANGE(
        //                       GOODS_NO
        //                       ,CP_NO
        //                       ,BUY_PRICE
        //                       ,SALE_PRICE
        //                       ,PRICE
        //                       ,STICKER_PRICE
        //                       ,PRINT_PRICE
        //                       ,DELIVERY_PRICE
        //                       ,DELIVERY_CNT_IN_BOX
        //                       ,LABOR_PRICE
        //                       ,OTHER_PRICE
        //                       ,SALE_SUSU
        //                       ,CP_SALE_SUSU
        //                       ,CP_SALE_PRICE
        //                       ,REG_ADM
        //                       ,REG_DATE
        //                       ,REASON
        //                     ) SELECT
                                //  GOODS_NO
                                // ,CP_NO
                                // ,BUY_PRICE
                                // ,SALE_PRICE
                                // ,PRICE
                                // ,STICKER_PRICE
                                // ,PRINT_PRICE
                                // ,DELIVERY_PRICE
                                // ,DELIVERY_CNT_IN_BOX
                                // ,LABOR_PRICE
                                // ,OTHER_PRICE
                                // ,SALE_SUSU
                                // ,CP_SALE_SUSU
                                // ,CP_SALE_PRICE
                                // ,REG_ADM
                                // ,REG_DATE
                                // ,REASON
        //                     FROM TBL_GOODS
        //                     WHERE GOODS_NO = '$goods_no'
        //                         AND DEL_TF = 'N'
        // ";
        $proposal_nos_str .= "'".$proposal_nos[$i]["proposal_no"]."',";
    }
    $proposal_nos_str = rtrim($proposal_nos_str,",");

    //상품 정보 변경
    if($proposal_nos_str != ""){
        $query =    "UPDATE TBL_MEMBER_PROPOSAL
                            SET ACPT_TF = 'Y'
                                ,ACCEPTOR = '$acceptor'
                                ,ACCPT_DATE = now()
                            WHERE PROPOSAL_NO IN ($proposal_nos_str)
        ";
        // echo $query;
        if(!mysql_query($query,$conn)){
            echo "update fail";
        } else {
            echo "update";
        }
    }
}

if($mode == "DISALLOW_PROPOSAL"){
    $acceptor = $_POST["acceptor"];
    $proposal_nos = $_POST["data"];
    
    $proposal_nos_str ="";
    for($i=0;$i<sizeof($proposal_nos);$i++){
        $proposal_nos_str .= "'".$proposal_nos[$i]["proposal_no"]."',";
    }
    $proposal_nos_str = rtrim($proposal_nos_str,",");

    if($proposal_nos_str != ""){
        $query =    "UPDATE TBL_MEMBER_PROPOSAL
                            SET ACPT_TF = 'N'
                                ,ACCEPTOR = '$acceptor'
                                ,ACCPT_DATE = now()
                            WHERE PROPOSAL_NO IN ($proposal_nos_str)
        ";
        // echo $query;
        if(!mysql_query($query,$conn)){
            echo "update fail";
        } else {
            echo "update";
        }
    }
}

if($mode == "INSERT_COUNSEL"){
    $cp_type = $_POST["cp_type"];
    $manager_nm = iconv("utf8","euckr",$_POST["manager_nm"]);
    $counsel_date = $_POST["counsel_date"];
    $counsel_adm_no = $_POST["counsel_adm_no"];
    $counsel_type = iconv("utf8","euckr",$_POST["counsel_type"]);
    $ask = iconv("utf8","euckr",$_POST["ask"]);
    $answer = iconv("utf8","euckr",$_POST["answer"]);
    $s_adm_no = $_POST["s_adm_no"];
    $seq_no = $_POST["seq_no"];
    $counsel_title=iconv("utf8","euckr",$_POST["counsel_title"]);
    $manager_phone=iconv("utf8","euckr",$_POST["manager_phone"]);
    $manager_email=iconv("utf8","euckr",$_POST["manager_email"]);
    $goods_code=iconv("utf8","euckr",$_POST["GOODS_CODE"]);


    //수정
    if($seq_no != ""){
        //존재 여부 확인
        $query =    "SELECT *
                            FROM TBL_COUNSEL
                            WHERE SEQ_NO = '$seq_no'
        ";
        $result = mysql_query($query,$conn);
        $record = array();

        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }

        //존재시 수정
        if(count($record) == 1){
            $query =    "UPDATE TBL_COUNSEL
                                SET
                                    CP_NO = '$cp_type'
                                    ,MANAGER_NM = '$manager_nm'
                                    ,COUNSEL_DATE = '$counsel_date'
                                    ,COUNSEL_ADM_NO = '$counsel_adm_no'
                                    ,COUNSEL_TYPE = '$counsel_type'
                                    ,COUNSEL_TITLE= '$counsel_title'
                                    ,MANAGER_PHONE= '$manager_phone'
                                    ,MANAGER_EMAIL= '$manager_email'
                                    ,GOODS_CODE   = '$goods_code'
                                    ,ASK = '$ask'
                                    ,ANSWER = '$answer'
                                    ,UP_ADM = '$s_adm_no'
                                    ,UP_DATE = now()
                                WHERE
                                    SEQ_NO = '$seq_no'
            ";
            // echo $query;
            if(!mysql_query($query,$conn)){
                echo "update fail";
            } else {
                echo "update";
            }
        } else {
            //미존재시 알림
            echo "counsel not exist";
        }
    } else {
        //신규
        $query =    "INSERT INTO TBL_COUNSEL(
                                COUNSEL_DATE
                                ,COUNSEL_TYPE
                                ,COUNSEL_TITLE
                                ,MANAGER_NM
                                ,MANAGER_PHONE
                                ,MANAGER_EMAIL
                                ,COUNSEL_ADM_NO
                                ,CP_NO
                                ,ASK
                                ,ANSWER
                                ,REG_ADM
                                ,REG_DATE
                            ) VALUES(
                                '$counsel_date'
                                ,'$counsel_type'
                                ,'$counsel_title,
                                ,'$manager_nm'
                                ,'$manager_phone'
                                ,'$manager_email,
                                ,'$counsel_adm_no'
                                ,'$cp_type'
                                ,'$ask'
                                ,'$answer'
                                ,'$s_adm_no'
                                ,now()
                            )
        ";
            // echo $query;

        if(!mysql_query($query,$conn)){
            echo "insert fail";
        } else {
            echo "insert";
        }
    }//if
}

if($mode == "DELETE_COUNSEL"){
    //init
    $seq_nos = $_POST["seq_no"];
    $s_adm_no = $_POST["s_adm_no"];

    $result = array();
    $data = array();
    $update_cnt = 0;
    $update_fail_cnt = 0;

    //update & write result of update
    for($i=0,$seq_no = "";$i<count($seq_nos);$i++){
        $seq_no = $seq_nos[$i]["seq_no"];
        if(!deleteCounsel($conn,$seq_no,$s_adm_no)){
            //업데이트 실패
            $update_fail_cnt++;
            array_push($data,array("seq_no"=>$seq_no, "update_result"=>"fail"));
        } else {
            //업데이트 성공
            $update_cnt++;
            array_push($data,array("seq_no"=>$seq_no, "update_result"=>"success"));
        }
    }//for

    //모든 결과 배열에 추가(성공 횟수, 실패 횟수, 상담별 결과 배열))
    array_push($result,array("update_cnt"=>$update_cnt, "update_fail_cnt"=>$update_fail_cnt,"data"=>$data));
    
    //리턴
    echo json_encode($result);
}
if($mode == "ATTACH_DELIVERY_PRICE"){
    $reqGoodsNo=$_POST['reqGoodsNo'];
    $delPrice=$_POST['delPrice'];
    $box=abs($_POST['box']);
    $orderer=$_POST['orderer'];

    $delPrice= (int)str_replace(',', '', $delPrice);
    $delPrice=abs($delPrice);
    // $delPrice=str_replace($delPrice,",","");
    // echo $delPrice;
    // exit;
    $result=InsertRequestGoodsSubLedger($conn,$reqGoodsNo,"택배비",$box,$delPrice,$orderer,$s_adm_no);
    if($result) echo 1;
    else echo 0;
}

// if($mode=="GET_LAST_MESSAGE_SEND_DATE"){
//     $query="SELECT SYSTEM_DATE 
//             FROM TBL_SYSTEM_DATE
//             WHERE SYSTEM_CODE='LAST_MESSAGE_SEND_DATE'
//             ";
//     $result=mysql_query($query, $conn);
//     $rows=mysql_fetch_array($result);


//     echo $rows[0];

// }
if($mode=="GET_LAST_MESSAGE_SEND_DATE"){
    $query="SELECT SYSTEM_CODE_VALUE
            FROM TBL_SYSTEM_CODE
            WHERE SYSTEM_CODE='LAST_SEND_MESSAGE'
            ";
    $result=mysql_query($query, $conn);
    $rows=mysql_fetch_array($result);
    $retString=iconv("EUC-KR","UTF-8",$rows[0]);
    echo $retString;
}
if($mode=="SAVE_NEW_MESSAGE"){
    $content=$_POST['content'];
    $query="UPDATE TBL_SYSTEM_CODE 
            SET SYSTEM_CODE_VALUE = '".$content."'
            WHERE SYSTEM_CODE='LAST_SEND_MESSAGE' ";
    $result=mysql_query($query, $conn);
}
if($mode=="SELECT_CATEGORY_DETAIL02"){

    $cateCode=$_POST['cateCode'];
    $query="SELECT CATE_CD, CATE_NAME
            FROM TBL_CATEGORY
            WHERE CATE_CD LIKE '".$cateCode."%'
            AND LENGTH(CATE_CD)=6
            AND USE_TF='Y'
            AND DEL_TF='N' " ;
    $result=mysql_query($query, $conn);
    $record=array();

    if($result<>""){
        $cnt=mysql_num_rows($result);
        for($i = 0; $i < $cnt; $i++){
            $record[$i]=mysql_fetch_assoc($result);
            $record[$i]["CATE_NAME"]=iconv("EUC-KR","UTF-8",$record[$i]["CATE_NAME"]);
        }
        echo json_encode($record);

        
    }
}
if($mode=="INSERT_GOODS_CHANGE_LOG"){
    $arrGoods=$_POST['arrGoods'];

    $query="INSERT INTO TBL_GOODS_CHANGE_LOG
        (GOODS_NO, 
        GOODS_NAME, AFTER_GOODS_NAME, 
        GOODS_SUB_NAME, AFTER_GOODS_SUB_NAME,
        GOODS_CATE, AFTER_GOODS_CATE,
        GOODS_CODE, AFTER_GOODS_CODE,
        MSTOCK_CNT, AFTER_MSTOCK_CNT,
        CATE_04,    AFTER_CATE_04,
        TAX_TF,     AFTER_TAX_TF,
        MEMO,       AFTER_MEMO,
        EXPOSURE_TF, AFTER_EXPOSURE_TF,
        CONCEAL_PRICE_TF,   AFTER_CONCEAL_PRICE_TF,
        USE_TF,         AFTER_USE_TF,
        REG_ADM,
        REG_DATE )

        VALUES('".$arrGoods["GOODS_NO"]."',
         '".$arrGoods["OLD_GOODS_NAME"]."',         '".$arrGoods["NEW_GOODS_NAME"]."',
         '".$arrGoods["OLD_GOODS_SUB_NAME"]."',     '".$arrGoods["NEW_GOODS_SUB_NAME"]."',
         '".$arrGoods["OLD_SALE_STATE"]."',         '".$arrGoods["NEW_SALE_STATE"]."',
         '".$arrGoods["OLD_GOODS_CODE"]."',         '".$arrGoods["NEW_GOODS_CODE"]."',
         '".$arrGoods["OLD_MSTOCK_CNT"]."',         '".$arrGoods["NEW_MSTOCK_CNT"]."',
         '".$arrGoods["OLD_SALE_STATE"]."',         '".$arrGoods["NEW_SALE_STATE"]."',
         '".$arrGoods["OLD_TAX_TF"]."',             '".$arrGoods["NEW_TAX_TF"]."',
         '".$arrGoods["OLD_MEMO"]."',               '".$arrGoods["NEW_MEMO"]."',
         '".$arrGoods["OLD_EXPOSURE_TF"]."',        '".$arrGoods["NEW_EXPOSURE_TF"]."',
         '".$arrGoods["OLD_CONCEAL_PRICE_TF"]."',   '".$arrGoods["NEW_CONCEAL_PRICE_TF"]."',
         '".$arrGoods["GOODS_NO"]."',               '".$arrGoods["GOODS_NO"]."',
         '".$arrGoods["REG_ADM"]."',
         NOW()
         )
        ";

    echo $query."<br>";   

    exit;
    
    if(!mysql_query($query, $conn)){
        echo "<script>alert('INSERT_GOODS_CAHNAGE_LOG-ERROR!');</script>";
        exit;
    }

     

}//end of mode

#====================================================================
# DB Close
#====================================================================
mysql_close($conn);
?>