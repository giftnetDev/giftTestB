<?
    // require "./www/_classes/com/db/DBUtil.php";

    // $conn=db_connection("w");


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="EUC-KR">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <script>
            function js_test(){
                setInterval(function(){alert("Hello");
                    $.ajax({
                        url:'ajax_timeTest.php',
                        data:{
                            mode:"TEST_DB",
                            type:"POST",
                            dataType:"text"
                        },
                        success:function(data){
                            if(data=="1"){
                                alert('play 가능');
                            }
                        },
                        error:function(jqXHR,textStatus,errorThrown){

}
                    });//end of ajax
                
                },//end of innerFunction
                3000);// end of setInterval
            }
        </script>
    </head>
    <body>
        <input type="button" onclick="js_complete_play()" value='play종료'>
        
    </body>
</html>