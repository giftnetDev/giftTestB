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
		<!--�Ե��ù� CSS-->
		<link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/common.css">
		<link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/sub.css">


		 <!-- jQuery -->
		 <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-ui.min.js"></script>
    
    <!-- �Ե��ù� CSS �������̵�-->
    <style>
        /* ��ü�� */
        .inner{
            width: 100% !important;
            min-width: 100% !important;
        }

        /* ������ ���� */
        .contStep{
            margin : 0 0 20px 110px !important;
        }

        /* ������ ũ��, ����̹��� */
        .contStep li{
            background-image:none !important;
            width: auto !important;
            height: auto !important;
        }

        /* ���� ��� ���� */
        .contTitArea{
            padding-top:10px !important;
        }
    </style>
    <script>
		
        $(document).ready(function(){
            //���ʿ� ��� ����
            $(".tblTopArea").remove();
			
            //��� ������ �� �� ī��Ʈ
            var cnt = $(".tblH:eq(1) tr").length-1;
			
            //��� ������ ������ ���� �ܰ踦 ������
            var step = $(".tblH:eq(1) tr:eq("+cnt+") td:eq(0)").html();

			//��ǰ���� : .item01, ��ǰ �̵��� : .item03, ��� ��� : .item05, ��� �Ϸ� .item06
			var target = "";
            
            if(step == "��ǰ����"){
                target = ".item01";
            } else if(step == "��ǰ �̵���"){
                target = ".item03";
            } else if(step == "��� ���"){
                target = ".item05";
            } else {
                target = ".item06";
            }

            //���� �ܰ� Ȱ��ȭ ǥ��
			$(target).addClass("on");
        });

		
	</script>
	<script>
		
    </script>

	</head>
	<body>
		<!--�ʿ��� ������ ������ �ִ� �ι�° inner class div�� ���-->
		<?=iconv("UTF-8","EUC-KR",$html[1])?>

	</body>
</html>