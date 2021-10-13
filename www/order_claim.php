<? require "_common/home_pre_setting.php";?>
<?//FUNCTION_DEFINITION_ZONE
    function getOrderGoodsByOrderGoodsNo($db, $memberNo, $orderGoodsNo){
        $query="SELECT  OG.GOODS_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME
                        OG.OPT_STICKER_NO, OG.OPT_PRINT_MSG, OG.QTY, OG.SALE_PRICE, OG.DELIVERY_PRICE, OG.ORDER_STATE
                FROM    TBL_ORDER O
                JOIN    TBL_ORDER_GOODS OG ON O.RESERVE_NO=OG.RESERVE_NO
                WHERE   O.MEM_NO = '$memberNo'
                AND     OG.ORDER_GOODS_NO = '$orderGoodsNo'
                "   ;
        
        /**
         * orderGoodsNo가 유일성을 만족하지만 TBL_ORDER에 JOIN하여 MEM_NO='$memberNo'을 한 이유는 일종의 '보안'차원이다.
         */
        
        $result=mysql_query($query, $db);
        $record=array();
        // $cnt=0;
        if($result<>""){
            // $cnt=mysql_num_rows($result);
            $record[0]=mysql_fetch_assoc($result);
        }

        return $record;

    }//end of function
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <? require "./header.php"; ?>
    </head>
    <body>
        <h2>교환/반품 등록</h2>
        <form name="frm" method="POST">
            <input type="hidden" name="search_name" value="">
            <input type="hidden" name="mode" value="">

            <table>

            </table>

            
        </form><!--name="frm"-->
        
    </body>
</html>