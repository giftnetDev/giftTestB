<?
    require "../_classes/com/db/DBUtil.php";

    $conn= db_connection("w");

    $mode=$_POST['mode'];

?>
<?// Zone Of Functions
    function changeQtyAtCart($db, $cartNo, $goodsNo, $curQty, $curPrice){
        $query="UPDATE TBL_CART
                SET     QTY         = '".$curQty."'
                WHERE   GOODS_NO    = '".$goodsNo."'
                AND     CART_NO     = '".$cartNo."' 
                ;   ";

        $result=mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('func.changeQtyAt....() Error');</script>";
            return false;
        }
        else{
            return true;
        }
    }

    function changeCorrespondingQty($db, $cartNo, $goodsNo, $curQty){
        $query="UPDATE TBL_CART
                SET     QTY         = '".$curQty."'
                WHERE   CART_NO     = '".$cartNo."'
                AND     GOODS_NO    = '".$goodsNo."'
                ;   ";
                
        $result = mysql_query($query, $db);
        if(!$result){
            echo "<script>alert('func.changeCorresponding....() Error');</script>";
            return false;
        }
        else{
            return true;
        }
    }
    


?>
<?//Zone Of Mode
    if($mode=="CHANGE_QTY"){
        $cartNo     = $_POST['cartNo'];
        $goodsNo    = $_POST['goodsNo'];
        $curQty     = $_POST['curQty'];
        $curPrice   = $_POST['curPrice'];

        $ret=changeQtyAtCart($conn, $cartNo, $goodsNo, $curQty, $curPrice);

        echo $ret;
    }

    if($mode=="CHAGNE_CORRESPONDING_QTY"){
        $cartNo     =$_POST['cartNo'];
        $goodsNo    =$_POST['goodsNo'];
        $curQty     =$_POST['curQty'];
        
        $ret=changeCorrespondingQty($conn, $cartNo, $goodsNo, $curQty);
        //return $ret;

        echo $ret;
    }

    if($mode=="DELETE_CART"){
        $cartNo=$_POST['cartNo'];
        $query="DELETE FROM TBL_CART
                WHERE CART_NO= '".$cartNo."'
                ";
        $result=mysql_query($query, $conn);
        if(!$result){
            echo "<script>alert('DELETE ERROR');</script>";
            exit;
        }
        else{
            echo 1;
        }
    }

  
?>
<?// SQL Á¾·á
    mysql_close($conn);
?>