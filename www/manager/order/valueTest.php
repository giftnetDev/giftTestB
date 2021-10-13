<?
    require "../../_classes/com/db/DBUtil.php";
    $conn=db_connection("w");   
    if($conn){
        echo "conn success</br>";
    }
    else{
        echo "conn Fail</br>";
        exit();
    }
    
    $query="SELECT  DCODE, DCODE_NM 
            FROM    TBL_CODE_DETAIL 
            WHERE   DEL_TF='N'
            AND     USE_TF='Y'
            AND     POCDE ='DELIVERY_CP'
            ORDER BY DCODE_SEQ_NO ";

    
    $result=mysql_query($query, $conn);
    echo"<script>console.log('aaa')</script>";
    echo"<script>console.log('".$result[0][0]."')</script>";

    $total=mysql_affected_rows();


?>
<form name="tmpFrom" method="POST">
    <select name="delivery_cp" class="box01" style="width:105px;">
    <option value="">ÀüÃ¼</option>
    <?
        
        for($i=0 ; $i < $total; $i++)
        {
            mysql_data_seek($result,$i);
            $row=mysql_fetch_array($result);

            $RS_DCODE   =Trim($row[0]["R"]);
            $RS_DCODE_NM=Trim($row[0]);

            if(""==$RS_DCODE){
                echo("<option value'".$RS_DCODE."' selected>".$RS_DCODE_NM."</option>");
            }
            else{
                echo("<option value'".$RS_DCODE."'>".$RS_DCODE_NM."</option>");
            }
        }
        echo"</select>";
    ?>    
</form>
<?
    mysql_close($conn);
?>