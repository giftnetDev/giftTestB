<?

?>
<!DOCTYPE html>
<html lang="ko">
    <script type="text/javascript" src="../../js/jquery.js"></script>

    <head>
        <meta charset="euc-kr">
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
        <title>TEST of cntTxt</title>
        <script>
            $(document).ready(function(){
                $('.DOC_TEXT').keyup(function(e){
                    var content = $(this).val();
                    $('#counter').html("("+content.length+" / 최대 200자)");
                    if(content.length>200){
                        alert("최대 200자까지 입력 가능합니다");
                        $(this).val(content.substr(0,200));
                        $('#counter').html("(200 / 최대 200자)");
                    }
                    // if(content.length > 200){
                    //     alert("최대 200자까지 입력 가능합니다.");
                    //     $(this).val(content.substring(0, 200));
                    //     $('#counter').html("(200 / 최대 200자)");
                    // }
                });

            });


        </script>

    </head>

    <body>
    <textarea style="width:600px;" class="DOC_TEXT" name="DOC_TEXT" placeholder="선택하신 서류사항 항목에 대한 내용을 200자 이내로 기재해주세요."></textarea>
        <br />
        <span style="color:#aaa;" id="counter">(0 / 최대 200자)</span>





    </body>
</html>