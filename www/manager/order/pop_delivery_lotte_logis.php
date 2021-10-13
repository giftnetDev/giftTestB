<?
//파싱 라이브러리 호출
require "../../_php_html_parser/simple_html_dom.php";

//파싱
$html = file_get_html('https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo='.$delivery_no);

//필터링
$html = $html->find('div[class=inner]');
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
    <!-- 롯데택배 CSS -->
    <link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/common.css">
    <link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/sub.css">
    
    <!-- jQuery -->
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-ui.min.js"></script>
    
    <!-- 롯데택배 CSS 오버라이드-->
    <style>
        .inner{
            width: 100% !important;
            min-width: 100% !important;
        }

        /* 아이콘 여백 */
        .contStep{
            margin : 20px 0 20px 20px !important;
        }

        /* 아이콘 크기, 배경이미지 */
        .contStep li{
            /* background-image:none; */
            background-size:100% 200% !important;
            background-position: 0 0;
            width: 15% !important;
            height: auto !important;
        }
        .contStep li.on{
            background-size:100% 200% !important;
            background-position: 0 -119px !important;
            width: 15% !important;
            height: auto !important;
        }
        

        .item03, .item05, .item06{
            margin-left: 100px !important;
        }

        /* 아이콘 폭, 위치 */
        .contStep li:after{
            left: -100px !important;
            width: 95px !important;
        }
        
        /* 제목 상단 여백 */
        .contTitArea{
            padding-top:10px !important;
        }
    </style>
    <script>
		
        $(document).ready(function(){
            //불필요 요소 삭제
            $(".tblTopArea").remove();
			
            //배송 내역의 행 수 카운트
            var cnt = $(".tblH:eq(1) tr").length-1;
			
            //배송 내역의 마지막 행의 단계를 가져옴
            var step = $(".tblH:eq(1) tr:eq("+cnt+") td:eq(0)").html();

			//상품접수 : .item01, 상품 이동중 : .item03, 배송 출발 : .item05, 배달 완료 .item06
			var target = "";
            
            if(step == "상품접수"){
                target = ".item01";
            } else if(step == "상품 이동중"){
                target = ".item03";
            } else if(step == "배송 출발"){
                target = ".item05";
            } else if(step == "배달 완료"){
                target = ".item06";
            }
            else{
                target="";
            }

            //현재 단계 활성화 표시
			$(target).addClass("on");
        });

    </script>
</HEAD>
<BODY>
    <!-- 필요한 정보를 가지고 있는 두번째 inner div만 출력-->
    <?=iconv("utf8","euckr",$html[1])?>
</BODY>
</HTML>