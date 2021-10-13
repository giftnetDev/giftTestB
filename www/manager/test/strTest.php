<?

    function recTest($cur, $max){
        echo "cur : $cur<br>";
        if($cur<$max){
            recTest($cur+1,$max);
        }
    }

    recTest(0,5);
?>