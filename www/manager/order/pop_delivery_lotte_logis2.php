<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";
	$conn = db_connection("w");

	require "../../_php_html_parser/simple_html_dom.php";

	$html=file_get_html('https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo='.$delivery_no);

	$html=$html->find('div[class=inner]');
	// echo("delivery_cp is : ".$delivery_cp);
	// echo("delivery_no is : ".$delivery_no);
?>

<!DOCTYPE HTML>
<html>
	<head>
		<!--롯데택배 CSS-->
		<link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/common.css">
		<link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/sub.css">


		 <!-- jQuery -->
		 <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-ui.min.js"></script>
    
    <!-- 롯데택배 CSS 오버라이드-->
    <style>
        /* 전체폭 */
        .inner{
            width: 100% !important;
            min-width: 100% !important;
        }

        /* 아이콘 여백 */
        .contStep{
            margin : 0 0 20px 110px !important;
        }

        /* 아이콘 크기, 배경이미지 */
        .contStep li{
            background-image:none !important;
            width: auto !important;
            height: auto !important;
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
            } else {
                target = ".item06";
            }

            //현재 단계 활성화 표시
			$(target).addClass("on");
        });

		
	</script>
	<script>
		
    </script>

	</head>
	<body>
		<!--필요한 정보를 가지고 있는 두번째 inner class div만 출력-->
		<?=iconv("UTF-8","EUC-KR",$html[1])?>

	</body>
</html>