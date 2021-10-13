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
                        <img src="img/order_complete.png" alt="축하합니다!" style="position:relative; z-index:999999; width:90%; display:block;margin:0px; auto;">
                        <div class="cong">      

                            배송조회를 통해 주문한 제품을 확인하세요
                        </div><!--cong-->
                        <br>
                        <br>
                        <div class="tcenter margin-top-10"> 
                            <button class="carting" onclick="js_move_page('delivery_confirm.php');">배송조회</button>
                            <button class="joomoon" onclick="js_move_page('index.php');">메인으로 가기</button>
                        </div><!--tcenter margin-top-10-->

                    </div><!--cart_info-->
                </div><!--detail_page_inner-->
            </div><!--detail_page-->
            <? require "./footer.php"; ?>
        </div><!--wrap-->
    </body>
</html>