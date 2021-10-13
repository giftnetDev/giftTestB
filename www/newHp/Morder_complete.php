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
                    <span class="left_button" onclick="js_move_page('Mindex.php');">< 메인으로 </span>
                    <span class="page_title">주문완료</span>
                    <span class="right_button" onclick="js_move_page('Mdelivery_confirm.php');">주문/배송></span>
                </div><!--end of title_line-->
                <div class="content">
                    <div class="center_box" style="text-align:center; position:relative; top:40px;">
                        <img src="img/order_complete.png" alt="축하합니다!" style="position:relative; z-index:2; width:100%;">
                        <br>
                        <span class="center_gray">주문번호: <?=$reserveno?></span>
                        <br>
                        <span class="center_gray">배송조회를 통해 주문한 제품을 확인하세요</span>
                    </div>

                </div><!--content-->
            </form>
        </div><!--"background"-->
    </body>
</html>