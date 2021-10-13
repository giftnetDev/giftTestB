<?
    require "../../_classes/com/db/DBUtil.php";

    $conn = db_connection("w");

    $mode=$_POST['mode'];
    
    if($mode=="CHANGE_STICKER_NO"){
        $seq=$_POST['seq'];
        $stickerNo=$_POST['stickerNo'];

        $query="UPDATE TBL_TEMP_SINHYUP_ORDER
        SET 
            OPT_STICKER_NO  =   '".$stickerNo."'
        WHERE
            SEQ             =   '".$seq."'
        ";

        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('AJAX: STICKER_UPDATE_ERROR()');</script>";
            exit;
        }
        else{
            echo 1;
        }
    }


    if($mode=="INSERT_OPTION"){
        $seq=$_POST['seq'];
        $stickerNo=$_POST['stickerNo'];
        $wrapNo=$_POST['wrapNo'];

        $query="UPDATE TBL_TEMP_SINHYUP_ORDER
                SET OPT_STICKER_NO  =   '".$stickerNo."'
                ,   OPT_WRAP_NO     =   '".$wrapNo."'
                WHERE
                    SEQ             =   '".$seq."'
                ";
        
        $result=mysql_query($query, $conn);
        $record=array();
        $cnt=0;

        if(!$result){
            echo "<script>alert('INSERT_OPTION.mode ERRROR');</script>";
            exit;
        }

    }

    if($mode=="SEARCH_GOODS"){

        $term= $_POST['keyword'];

        $term=trim(iconv("UTF-8","EUC-KR",$term));

        $query="SELECT GOODS_NO, GOODS_CODE, GOODS_NAME, CATE_04
                FROM    TBL_GOODS
                WHERE   DEL_TF='N'
                AND     USE_TF='Y'
                AND(
                        GOODS_NAME LIKE '%".$term."%'
                        OR GOODS_CODE LIKE '%".$term."%'
                    )
                        AND NOT SUBSTR(GOODS_CODE,5,1)='1'
                ";

        $cnt=0;
        $record=array();
        $result=mysql_query($query, $conn);

        if($result){
            $cnt=mysql_num_rows($result);
        }
        if($cnt>0){
            for($i=0; $i<$cnt; $i++){
                $record[$i]=mysql_fetch_assoc($result);
                $record[$i]['GOODS_NO']     =urlencode(iconv("EUC-KR","UTF-8",$record[$i]['GOODS_NO']));
                $record[$i]['GOODS_CODE']   =urlencode(iconv("EUC-KR","UTF-8",$record[$i]['GOODS_CODE']));
                $record[$i]['GOODS_NAME']   =urlencode(iconv("EUC-KR","UTF-8",$record[$i]['GOODS_NAME']));
                $record[$i]['SALE_STATE']   =urlencode(iconv("EUC-KR","UTF-8",$record[$i]['CATE_04']));
            }
            $arrJson=json_encode($record);
        }
        else{
            $arrJson=json_encode($receord);
        }
        $rets=urldecode($arrJson);
        echo $rets;
    }

    if($mode=="CHANGE_SGOODS"){
        $sGoodsNo=$_POST['sGoodsNo'];
        $goodsNo=$_POST['goodsNo'];

        $query="UPDATE TBL_GOODS_SINHYUP
                SET GOODS_NO='".$goodsNo."'
                WHERE SINHYUP_GOODS_NO='".$sGoodsNo."' ";
        
        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('CHANGE_SGOODS Error!');</script>";
            exit;
        }

        $query2="UPDATE TBL_TEMP_SINHYUP_ORDER
        SET
            GOODS_NO='".$goodsNo."'
        WHERE TEMP_MART_GOODS_CODE='".$sGoodsNo."'
            ";

        $result2=mysql_query($query2, $conn);
        if(!$result2){

            echo "<script>alert('CHANGE_SGOODS_UPDATE<query2> ERROR!');</script>";
            echo $query2."<br>";
            exit;
        }
        echo "1";
    }

    if($mode=="REGISTER_SGOODS"){
        $sGoodsNo=$_POST['sGoodsNo'];
        $goodsNo=$_POST['goodsNo'];

        $query="INSERT INTO TBL_GOODS_SINHYUP(SINHYUP_GOODS_NO, GOODS_NO)
                VALUES('".$sGoodsNo."', '".$goodsNo."') ";

        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('REGISTER_SGOODS_INSERT<query> Error!');</script>";
            exit;
        }

        $query2="UPDATE TBL_TEMP_SINHYUP_ORDER
                SET
                    GOODS_NO='".$goodsNo."'
                WHERE TEMP_MART_GOODS_CODE='".$sGoodsNo."'
                    ";

        $result2=mysql_query($query2, $conn);
        if(!$result2){
            echo "<script>alert('REGISTER_SGOODS_UPDATE<query2> ERROR!');</script>";
            exit;
        }
                
        echo "1";


    }

?>