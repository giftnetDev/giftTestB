<?require "../_common/home_pre_setting.php" ?>
<?//FUNCTIONS ZONE


?>
<?//PAGE_PROCESS ZONE









?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <? require "./Mheader.php"; ?>
        <script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
        <script>//JS_FUNCTION ZONE

        </script><!--END OF JS_FUNCTION ZONE-->
 
    </head>
    <body>
        <div class="background">
            <form name="frm" method="POST">
                <div class="title_line">
                    <span class="left_button" onclick="js_move_page('Mindex.php');">< �������� </span>
                    <span class="page_title">�ֹ��Ϸ�</span>
                    <span class="right_button" onclick="js_move_page('Mdelivery_confirm.php');">�ֹ�/���></span>
                </div><!--end of title_line-->
                <div class="content">
                    <div class="center_box" style="text-align:center; position:relative; top:40px;">
                        <img src="img/order_complete.png" alt="�����մϴ�!" style="position:relative; z-index:2; width:100%;">
                        <br>
                        <span class="center_gray">�ֹ���ȣ: <?=$reserveno?></span>
                        <br>
                        <span class="center_gray">�����ȸ�� ���� �ֹ��� ��ǰ�� Ȯ���ϼ���</span>
                    </div>

                </div><!--content-->
            </form>
        </div><!--"background"-->
    </body>
</html>