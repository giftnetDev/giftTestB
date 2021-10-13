<?

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="EUC-KR">
        <meta http-equiv="Content-Type" content="text/html; charset=EUC-KR"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
        function js_chk(){
            var frm=document.frm;
            for(i = 0; i < frm['chk_arr[]'].length ; i++){
                if(frm['chk_arr[]'][i].checked==true){
                    frm['txt_arr[]'][i].value="CHECK";
                    $("[name=span_arr[]]:eq(0)").html("test");
                    // frm['span_arr[]'][i].innerHTML("TEST");
                    // t.createElement('h1');

                }
                else{
                    frm['txt_arr[]'][i].value="Not";
                    // $("[name='span_arr[]]")[i].val("test");
                }

            }
        }
        $(document).ready(function(){
            var i=3;
            $(document).on('keypress',function(e){
                if(e.which == 13){
                    alert($(".span_arr:eq("+i+")").html("H1"));
                }
                if(e.which ==32){

                }
            });
        });
        
        </script>
    </head>
    <body>
        <form name="frm">
        <?
            for($i=0; $i<5; $i++){
            ?>
                <input type="checkbox" name="chk_arr[]" onchange="js_chk()"/>   
                <input type="text" name="txt_arr[]"/>
                <span class="span_arr"><?=$i?></span>
                <br>
            <?            
            }
        ?>
        <form>
    </body>
</html>