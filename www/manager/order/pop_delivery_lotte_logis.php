<?
//�Ľ� ���̺귯�� ȣ��
require "../../_php_html_parser/simple_html_dom.php";

//�Ľ�
$html = file_get_html('https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo='.$delivery_no);

//���͸�
$html = $html->find('div[class=inner]');
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
    <!-- �Ե��ù� CSS -->
    <link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/common.css">
    <link rel="stylesheet" type="text/css" href="https://www.lotteglogis.com//resources/css/sub.css">
    
    <!-- jQuery -->
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="https://www.lotteglogis.com//resources/js/jquery/jquery-ui.min.js"></script>
    
    <!-- �Ե��ù� CSS �������̵�-->
    <style>
        .inner{
            width: 100% !important;
            min-width: 100% !important;
        }

        /* ������ ���� */
        .contStep{
            margin : 20px 0 20px 20px !important;
        }

        /* ������ ũ��, ����̹��� */
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

        /* ������ ��, ��ġ */
        .contStep li:after{
            left: -100px !important;
            width: 95px !important;
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
            } else if(step == "��� �Ϸ�"){
                target = ".item06";
            }
            else{
                target="";
            }

            //���� �ܰ� Ȱ��ȭ ǥ��
			$(target).addClass("on");
        });

    </script>
</HEAD>
<BODY>
    <!-- �ʿ��� ������ ������ �ִ� �ι�° inner div�� ���-->
    <?=iconv("utf8","euckr",$html[1])?>
</BODY>
</HTML>