<?
    if($mode=="TEST"){
        echo "text_value : $text_value<br>";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="EUC-KR">
        <meta http-equiv="Content-Type" content="text/html; charset=EUC-KR"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
            function js_redirect(){
                var frm=document.frm;
                frm.mode.value="TEST";
                frm.target="";
                frm.action="<?=$_SERVER['PHP_SELF']?>";
                frm.submit();

            }
        </script>
        <title>Document</title>
    </head>
    <body>
        <form name="frm" method="POST">            
            <input type="text" name="text_value"> &nbsp;
            <input type="button" value="submit" onclick="js_redirect()">
            <input type="hidden" name="mode">
        </form>
    </body>
</html>