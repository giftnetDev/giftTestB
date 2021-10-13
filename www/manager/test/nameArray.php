<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="EUC-KR">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script>
            function js_test(){
                var frm=document.frm;
                var cnt=frm['chk[]'].length;

                var cntT=$("input[name='chk[]']").length;
                var cntC=$("input[name='chk[]']:checked").length;

                $.each($("input[name='chk[]']:checked"),function(index, item){
                    alert(index+" : "+item);
                });

                // $("input[name='chk[]']:checked").each(function(index, value){
                //     alert(index+" : "+value);
                // });

                // for(i=0; i<cntT;i++){
                //     // alert($("input[name='chk[]']:checked").);
                // }



                

                // var cnt1=$("#test").length;
                // alert(cnt1);
                return ;
            }
        </script>
        <title>Document</title>
    </head>
    <body>
        <form name="frm">
            <label><input type="checkbox" name="chk[]" id='test' value="A">apple</label> <br>
            <label><input type="checkbox" name="chk[]" id='test' value="O">orange</label><br>
            <label><input type="checkbox" name="chk[]" value="B">banana</label> 
            <input type="button" value="TEST" onclick="js_test()">
            <!-- <input type="button" value="TEST1" onclick="js_test()"> -->
        </form>


    </body>
</html>