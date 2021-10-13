<? session_start();

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";
$conn = db_connection("w");
//body > div id=wrap > div id=container > div id=contents > div class=inner > div class=contArea > table class=tblH > tbody > tr > td 가 상품접수 이면 집하된 거
#==============================================================================
# Confirm right
#==============================================================================
// 메뉴 권한코드
$menu_right = "OD016";

#====================================================================
# common_header Check Session
#====================================================================
require "../../_common/common_header.php";

#=====================================================================
# common function, login_function
#=====================================================================
require "../../_common/config.php";
require "../../_classes/com/util/Util.php";
require "../../_classes/com/etc/etc.php";
require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================
if ($start_date == "") {
    $start_date = date("Y-m-d", strtotime("first day of this month"));
} else {
    $start_date = trim($start_date);
}

if ($end_date == "") {
    $end_date = date("Y-m-d", strtotime("last day of this month"));
} else {
    $end_date = trim($end_date);
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
    <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
    <script>
        var datas = [];
        //function
        function selectDeliveryPaper(startDate, endDate, deliveryCp) {
            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'json',
                type: 'post',
                data: {
                    'mode': 'SELECT_DELIVERYPAPER',
                    'startDate': startDate,
                    'endDate': endDate,
                    'deliveryCp': deliveryCp
                },
                success: function(response) {
                    if (response != false) {
                        if (response.length > 1) {
                            datas.push(response);
                            var thead = "<tr>\
                                            <td>주문번호</td>\
                                            <td>주문업체</td>\
                                            <td>출고번호</td>\
                                            <td>수령인</td>\
                                            <td>수령인전화번호</td>\
                                            <td>수령인핸드폰번호</td>\
                                            <td>수령인주소</td>\
                                            <td>주문자</td>\
                                            <td>주문자전화번호</td>\
                                            <td>주문상품</td>\
                                            <td>배송회사</td>\
                                            <td>송장번호</td>\
                                            <td>운임</td>\
                                            <td>운임타입</td>\
                                            <td>등록일</td>\
                                            <td>배송상태</td>\
                                        </tr>\
                                ";
                            $("#resultTable").append(thead);

                            for (var i = 0; i < response.length; i++) {
                                var reserve_no = response[i]['RESERVE_NO']; //주문번호
                                var cp_no = response[i]['CP_NO']; //주문업체
                                var delivery_seq = response[i]['DELIVERY_SEQ']; //출고번호
                                var receiver_nm = response[i]['RECEIVER_NM']; //수령인
                                var receiver_phone = response[i]['RECEIVER_PHONE']; //수령인전화번호
                                var receiver_hphone = response[i]['RECEIVER_HPHONE']; //수령인핸드폰번호
                                var receiver_addr = response[i]['RECEIVER_ADDR']; //수령인주소
                                var order_nm = response[i]['ORDER_NM']; //주문자
                                var order_phone = response[i]['ORDER_PHONE']; //주문자전화번호
                                var goods_delivery_name = response[i]['GOODS_DELIVERY_NAME']; //주문상품
                                var delivery_cp = response[i]['DELIVERY_CP']; //배송회사
                                var delivery_no = response[i]['DELIVERY_NO']; //송장번호
                                var delivery_fee = response[i]['DELIVERY_FEE']; //운임
                                var delivery_fee_code = response[i]['DELIVERY_FEE_CODE']; //운임타입
                                var reg_date = response[i]['REG_DATE']; //등록일

                                var tbody = "<tr>\
                                                <td>" + reserve_no + "</td>\
                                                <td>" + cp_no + "</td>\
                                                <td>" + delivery_seq + "</td>\
                                                <td>" + receiver_nm + "</td>\
                                                <td>" + receiver_phone + "</td>\
                                                <td>" + receiver_hphone + "</td>\
                                                <td>" + receiver_addr + "</td>\
                                                <td>" + order_nm + "</td>\
                                                <td>" + order_phone + "</td>\
                                                <td>" + goods_delivery_name + "</td>\
                                                <td>" + delivery_cp + "</td>\
                                                <td>" + delivery_no + "</td>\
                                                <td>" + delivery_fee + "</td>\
                                                <td>" + delivery_fee_code + "</td>\
                                                <td>" + reg_date + "</td>\
                                                <td id='"+delivery_no+"'></td>\
                                            </tr>\
                                ";

                                $("#resultTable").append(tbody);
                            }
                        } else {
                            console.log("가져온 데이터가 없습니다.");
                        }
                    } else {
                        console.log("가져온 데이터가 없습니다.");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                }
            }); //ajax
        }

        function checkState(i,cnt){
            if(i<cnt){
                $("#"+datas[0][i]['DELIVERY_NO']).html("<iframe src='https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo="+datas[0][i]['DELIVERY_NO']+"'></iframe>");
                i++;
                setTimeout(function() {
                    console.log($("#"+datas[0][i]['DELIVERY_NO']).html());
                    checkState(i,cnt);
                }, 3000);
            }
        }

        $(document).ready(function() {
            //initialize        

            //event
            $("#searchBtn").on("click", function() {
                var startDate = $("#startDate").val();
                var endDate = $("#endDate").val();
                var deliveryCp = $("#deliveryCp").val();
                selectDeliveryPaper(startDate, endDate, deliveryCp);
            });

            $("#checkState").on("click", function() {
                var cnt = datas[0].length;
                //ㅋㅋ 로딩중일때 다른 송장 조회하면 조회 안 됨, 로딩 다 되면 다음 송장 로딩해야함
                checkState(0,cnt);
            });
        });
    </script>
</head>

<body>
    <!-- <iframe src='https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo=232009815833'></iframe> -->
    조회기간 : <input type="date" id="startDate" value="<?= $start_date ?>" /> ~ <input type="date" id="endDate" value="<?= $end_date ?>" />

    <br>
    택배회사 :
    <select id="deliveryCp">
        <option value="롯데택배">롯데택배</option>
        <option value="CJ대한통운">CJ대한통운</option>
    </select>
    <input type="button" value="조회" id="searchBtn" />
    <br>
    <input type="button" value="배송상태 확인" id="checkState" />
    <br>
    <br>
    <table id="resultTable"></table>
</body>

</html>

<?
#====================================================================
# DB Close
#====================================================================
mysql_close($conn);
?>