<?
    require "../../_common/home_pre_setting.php";
?>
<?
#===============================================================
#   Request Parameter
#===============================================================
    if($start_date == ""){
        $start_date="2010-07-24";
    }
    else{
        $start_date=trim($start_date);
    }

    if($end_date==""){
        $end_date=date("Y-m-d",strtotime("0 month"));
    }
    else{
        $end_date=trim($end_date);
    }

    $day_0 = date("Y-m-d",strtotime("0 month"));
    $day_1 = date("Y-m-d",strtotime("-1 day"));
    $day_7 = date("Y-m-d",strtotime("-7 day"));
    $day_31= date("Y-m-d",strtotime("-1 month"));

    #List Parameter
    $nPage      =   trim($nPage);
    $nPageSize  =   trim($nPageSize);

    $search_field   =   trim($search_field);
    $search_str     =   trim($search_str);

    $del_tf="N";

#===============================================================
#   Page process
#===============================================================

    if($nPage <> ""){
        $nPage = (int)($nPage);
    }
    else{
        $nPage=1;
    }
    
    if($nPageSize <> ""){
        $nPageSize = (int)($nPageSize);
    }
    else{
        $nPageSize = 24;
    }
    
    $nPageBlock=5;


#===============================================================
#   Get Search List Count
#===============================================================

    if($search_str <> ""){
        $search_field="ALL";
    }

    $arr_options = array("code_cate" => $code_cate, "start_price" => $start_price, "end_price" => $end_price);

    $nListCnt = totalCntHomepageGoods($conn, $search_field,$search_str, $arr_option);
?>
<?
//Function
    function listHomepageGoods($db, $search_field, $earch_str, $arr_options, $order_field, $order_str, $nPage, $nRowCount, $total_cnt){
        $offset= $nRowCount*($nPage-1);

        $logical_num=($total_cnt-$offset)+1;

        $query="SELECT @rownum:= @rownum -1 as rn, GOODS_NO, GOODS_CATE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04, DELIVERY_CNT_IN_BOX,
                PRICE, BUY_PRICE, SALE_PRICE, EXTRA_PRICE, STOCK_CNT, TAX_TF, IMG_URL, FILE_NM_100, FILE_RNM_100, FILE_PATH_100, FILE_SIZE_100, FILE_EXT_100";
    }
?>