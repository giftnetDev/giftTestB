<?
 ini_set("memory_limit", -1);
#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";
$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
require "../../_common/config.php";
require "../../_classes/com/util/Util.php";
require "../../_classes/com/util/ImgUtil.php";
require "../../_classes/com/etc/etc.php";
require "../../_classes/biz/goods/goods.php";
require "../../_classes/biz/company/company.php";
    
#===============================================================
# custom function
#===============================================================

?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= $g_charset ?>" />
    <title>기프트넷</title>
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/goods_common.js"></script>
    <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <script src="../js/swiper.min.js"></script>
    <script src="../js/html2canvas.min.js"></script>
    <meta property ="og:title" content="기프트넷 모바일 상품 제안서입니다"/>
    <meta property ="og:type" content="website"/>
    <meta property ="og:description" content="손가락으로 스와이프해주세요"/>
    <meta property ="og:image" content="https://www.giftnet.co.kr/manager/images/admin/giftnet_logo.png"/>
    <style>
        .card {
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 6px;
            height: 100%;
        }

        .card>.card-link .card-img img {
            border-radius: 6px 6px 0 0;
        }

        .card .card-img {
            position: relative;
            padding: 0;
            display: table;
        }

        .card .card-img .card-caption {
            position: absolute;
            right: 0;
            bottom: 16px;
            left: 0;
        }

        .card .card-body {
            display: table;
            width: 100%;
            padding: 12px;
        }

        .card .card-header {
            border-radius: 6px 6px 0 0;
            padding: 8px;
        }

        .card .card-footer {
            border-radius: 0 0 6px 6px;
            padding: 8px;
        }

        .card .card-left {
            position: relative;
            float: left;
            padding: 0 0 8px 0;
        }

        .card .card-right {
            position: relative;
            float: left;
            padding: 8px 0 0 0;
        }

        .card .card-body h1:first-child,
        .card .card-body h2:first-child,
        .card .card-body h3:first-child,
        .card .card-body h4:first-child,
        .card .card-body .h1,
        .card .card-body .h2,
        .card .card-body .h3,
        .card .card-body .h4 {
            margin-top: 0;
        }

        .card .card-body .heading {
            display: block;
        }

        .card .card-body .heading:last-child {
            margin-bottom: 0;
        }

        .card .card-body .lead {
            text-align: center;
        }

        /* -- default theme ------ */
        .card-default {
            border-color: #ddd;
            background-color: #fff;
        }

        .card-default>.card-header,
        .card-default>.card-footer {
            color: #888;
            background-color: #fff;
        }

        .card-default>.card-header {
            border-bottom: 1px solid #ddd;
            padding: 8px;
        }

        .card-default>.card-footer {
            border-top: 1px solid #ddd;
            padding: 8px;
        }

        .card-default>.card-img:first-child img {
            border-radius: 6px 6px 0 0;
        }

        .card-default>.card-left {
            padding-right: 4px;
        }

        .card-default>.card-right {
            padding-left: 4px;
        }

        .card-default p:last-child {
            margin-bottom: 0;
        }

        .card-default .card-caption {
            color: #fff;
            text-align: center;
            text-transform: uppercase;
        }

        .container{
            overflow:hidden;
            width:100%;
            height:100%;
            padding:5px;
        }
        .card-header{
            text-align: center;
            font-size: 1em;
        }
        .card-body {
            height:40%;
        }
        .card-img {
            width:100%;
            text-align: center;
            border-top: 1px solid #888;
            border-bottom: 1px solid #888;
        }
        .table {
            display: table;
            width: 100%;
        }
        .table-row {
            display: table-row;
        }
        .table-cell {
            display: table-cell;
            padding: 3px;
        }
        .col-title {
            width:30%;
            color: #888;
            text-align: left;
            font-size: 1em;
            padding-left:20px;
        }
        .col-contents {
            width:70%;
            color: #66a5da;
            text-align: right;
            font-size: 1.5em;
            padding-right:30px;
        }
        .col-contents-long {
            width:70%;
            color: #66a5da;
            text-align: left;
            font-size: 1em;
            padding-right:30px;
        }
        /*여기부터 스위퍼 추가*/
        html, body {
            position: relative;
            height: 100%;
        }
        body {
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color:#000;
            margin: 0;
            padding: 0;
        }
        .swiper-container {
            width: 100%;
            height: 100%;
            /* overflow-y:scroll; */
        }
        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
    </style>
    <script>
        //client side에서 동적으로 생성된 meta tag 카카오 크롤러가 수집 불가 서버 사이드에서 구현 필요
        //이 방법 사용시 ajax 사용 불가 처음부터 다시 구현해야함

        // var metas = new Array();
        // metas[0] = document.createElement('meta');
        // metas[1] = document.createElement('meta');
        // metas[2] = document.createElement('meta');
        // metas[3] = document.createElement('meta');
        // metas[4] = document.createElement('meta');
        // metas[5] = document.createElement('meta');
        
        // metas[0].property   = "og:title";
        // metas[0].content    = "기프트넷 모바일 상품 제안서";
        // metas[1].property   = "og:type";
        // metas[1].content    = "website";
        // metas[2].property   = "og:description";
        // metas[2].content    = "상품명1 등 n개 상품 정보";
        // metas[3].property   = "og:url";
        // metas[3].content    = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info.php?key=<?=$key?>";
        // metas[4].property   = "og:image";
        // metas[4].content    = "https://www.giftnet.co.kr/upload_data/goods_image/500/809-213507.jpg";
        // metas[5].property   = "og:locale";
        // metas[5].content    = "ko_KR";
        
        // for(var i=0;i<metas.length;i++){
        //     document.getElementsByTagName('head')[0].appendChild(metas[i]);
        // }

        var link_no = "<?=$key?>";
        var cnt = "";
        var goods_name = new Array();
        var goods_code = new Array();
        var goods_price = new Array();
        var proposal_price = new Array();
        var in_box_cnt = new Array();
        var img_path = new Array();
        var swiper;
        var j = 0;

        function swiperInitialize(){
            swiper = new Swiper('.swiper-container');
        }

        $(document).ready(function(){
            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'json',
                type: 'post',
                data : {
                'mode': "SELECT_DATA_BY_LINK",
                'link_no': link_no
                },
                success: function(response) {
                    if(response != false){
                        cnt = response.length;
                        for(var i=0;i<cnt;i++){
                            goods_name[i]   = response[i]["GOODS_NAME"];
                            goods_code[i]   = response[i]["GOODS_CODE"];
                            goods_price[i]  = response[i]["SALE_PRICE"];
                            proposal_price[i]  = (typeof(response[i]["PROPOSAL_PRICE"])!=='undefined')?response[i]["PROPOSAL_PRICE"]:"";
                            in_box_cnt[i]   = response[i]["DELIVERY_CNT_IN_BOX"];
                            if(response[i]["FILE_RNM_150"] != ""){
                                img_path[i] = response[i]["FILE_PATH_150"] + response[i]["FILE_RNM_150"];
                            } else if(response[i]["FILE_NM_100"] != "") {
                                img_path[i] = "/upload_data/goods/" + response[i]["FILE_NM_100"];
                            } else {
                                img_path[i] = "/manager/images/no_img.gif";
                            }
                        }
                    } else{
                        alert("실패하였습니다.");
                    }
                }, error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText); 
                }
            }).done(function(){
                for(var i=0;i<cnt;i++){
                    if(proposal_price[i] != ""){
                        var proposal_price_str =   "<div class='table-row'>\
                                                        <div class='table-cell col-title'>제안가</div>\
                                                        <div class='table-cell col-contents'>"
                                                            + proposal_price[i] + "원\
                                                        </div>\
                                                    </div>";
                    } else {
                        var proposal_price_str = "";
                    }
                    var estimateFormat = 
                        "<div id='goods_info"+i+"' class='swiper-slide'>\
                            <div class='container'>\
                                <div class='card card-default'>\
                                    <div class='card-header' style='text-align:center;'>"
                                        + goods_code[i] +
                                    "</div>\
                                    <div class='card-img'>\
                                        <img width='80%' src='"+img_path[i]+"'>\
                                    </div>\
                                    <div class='card-body'>\
                                        <div class='table'>\
                                            <div class='table-row'>\
                                                <div class='table-cell col-title'>상품명</div>\
                                                <div class='table-cell col-contents-long'>"
                                                    + goods_name[i] +
                                                "</div>\
                                            </div>\
                                            <div class='table-row'>\
                                                <div class='table-cell col-title'>판매가</div>\
                                                <div class='table-cell col-contents'>"
                                                    + goods_price[i] + "원\
                                                </div>\
                                            </div>"
                                            + proposal_price_str +
                                            "<div class='table-row'>\
                                                <div class='table-cell col-title'>박스입수</div>\
                                                <div class='table-cell col-contents'>"
                                                    + in_box_cnt[i] + "개\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>";
                    $(".swiper-wrapper").append(estimateFormat);
                }
                // for(var i=0;i<cnt;i++){
                for(var i=0;i<cnt;i++){
                    html2canvas(document.querySelector("#goods_info"+i)).then(canvas => {
                        var a = document.createElement('a');
                            a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
                            a.download = 'gift_info.jpeg';
                            a.click();
                        a.remove();
                    });
                }
                // setTimeout(function(){
                    // self.close();
                // },4000);
            });//ajax

            $(document).on("keyup",function(e) {
                // Up: 38 Down: 40 Right: 39 Left: 37
                if(e.keyCode == 37) {
                    //left arrow key
                    for(var i=0;i<cnt;i++){
                        $("#goods_info"+i).hide();
                    }

                    if(j>0){
                        j--;
                    } else {
                        j=cnt-1;
                    }
                    
                    $("#goods_info"+j).show();
                } else if(e.keyCode == 39) {
                    //right arrow key
                    for(var i=0;i<cnt;i++){
                        $("#goods_info"+i).hide();
                    }

                    if(j<cnt-1){
                        j++;
                    } else {
                        j=0;
                    }

                    $("#goods_info"+j).show();
                }
            });//on keypress

        });//ready
    </script>
</head>

<body>
<div class="swiper-container">
    <div class="swiper-wrapper"></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================
mysql_close($conn);
?>