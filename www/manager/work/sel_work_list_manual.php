<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
        <meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
        <title><?=$g_title?></title>
        <link rel="stylesheet" href="../css/admin.css" type="text/css" />
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
        <script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
        <style>
            /** */
            span.emphasis {color:red;}
        </style>
    </head>
    <body>
        <div>
            <h3>"익일 작업 관리"에서 송장 생성 못하는 경우</h3><br>
            
            1. 택배회사 없을 때<br>
            2. 개별택배의 개별주소가 입력되지 않았을 때<br>
            3. 직접수령, 퀵서비스, 외부업체 발송, 기타인 경우<br>
            4. 이미 송장이 존재하는 경우 
            <span class='emphasis'>(이 경우는 거의 비슷한 시간에 두 명의 사람이 다른 컴퓨터에서 송장을 생성하는 경우 서버의 Latency 로 인해 생성될 수 있으니 주의!)</span><br>
            

        </div>
    </body>
</html>
