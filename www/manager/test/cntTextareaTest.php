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
                    $('#counter').html("("+content.length+" / �ִ� 200��)");
                    if(content.length>200){
                        alert("�ִ� 200�ڱ��� �Է� �����մϴ�");
                        $(this).val(content.substr(0,200));
                        $('#counter').html("(200 / �ִ� 200��)");
                    }
                    // if(content.length > 200){
                    //     alert("�ִ� 200�ڱ��� �Է� �����մϴ�.");
                    //     $(this).val(content.substring(0, 200));
                    //     $('#counter').html("(200 / �ִ� 200��)");
                    // }
                });

            });


        </script>

    </head>

    <body>
    <textarea style="width:600px;" class="DOC_TEXT" name="DOC_TEXT" placeholder="�����Ͻ� �������� �׸� ���� ������ 200�� �̳��� �������ּ���."></textarea>
        <br />
        <span style="color:#aaa;" id="counter">(0 / �ִ� 200��)</span>





    </body>
</html>