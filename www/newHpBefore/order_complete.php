<? require "_common/home_pre_setting.php"; ?>
<?


?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <?  require "header.php";   ?>
        <script>
        
        </script>
    </head>
    <body>
        <div class="wrap">
            <? require "top.php";   ?>

            <div class="detail_page">
                <div class="detail_page_inner">
                    <div class="cart_info">
                        <img src="img/order_complete.png" alt="�����մϴ�!" style="position:relative; z-index:999999; width:90%; display:block;margin:0px; auto;">
                        <div class="cong">      

                            �����ȸ�� ���� �ֹ��� ��ǰ�� Ȯ���ϼ���
                        </div><!--cong-->
                        <br>
                        <br>
                        <div class="tcenter margin-top-10"> 
                            <button class="carting" onclick="js_move_page('delivery_confirm.php');">�����ȸ</button>
                            <button class="joomoon" onclick="js_move_page('index.php');">�������� ����</button>
                        </div><!--tcenter margin-top-10-->

                    </div><!--cart_info-->
                </div><!--detail_page_inner-->
            </div><!--detail_page-->
            <? require "./footer.php"; ?>
        </div><!--wrap-->
    </body>
</html>