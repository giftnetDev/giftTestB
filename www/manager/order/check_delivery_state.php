<? session_start();

#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";
$conn = db_connection("w");
//body > div id=wrap > div id=container > div id=contents > div class=inner > div class=contArea > table class=tblH > tbody > tr > td �� ��ǰ���� �̸� ���ϵ� ��
#==============================================================================
# Confirm right
#==============================================================================
// �޴� �����ڵ�
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
                                            <td>�ֹ���ȣ</td>\
                                            <td>�ֹ���ü</td>\
                                            <td>����ȣ</td>\
                                            <td>������</td>\
                                            <td>��������ȭ��ȣ</td>\
                                            <td>�������ڵ�����ȣ</td>\
                                            <td>�������ּ�</td>\
                                            <td>�ֹ���</td>\
                                            <td>�ֹ�����ȭ��ȣ</td>\
                                            <td>�ֹ���ǰ</td>\
                                            <td>���ȸ��</td>\
                                            <td>�����ȣ</td>\
                                            <td>����</td>\
                                            <td>����Ÿ��</td>\
                                            <td>�����</td>\
                                            <td>��ۻ���</td>\
                                        </tr>\
                                ";
                            $("#resultTable").append(thead);

                            for (var i = 0; i < response.length; i++) {
                                var reserve_no = response[i]['RESERVE_NO']; //�ֹ���ȣ
                                var cp_no = response[i]['CP_NO']; //�ֹ���ü
                                var delivery_seq = response[i]['DELIVERY_SEQ']; //����ȣ
                                var receiver_nm = response[i]['RECEIVER_NM']; //������
                                var receiver_phone = response[i]['RECEIVER_PHONE']; //��������ȭ��ȣ
                                var receiver_hphone = response[i]['RECEIVER_HPHONE']; //�������ڵ�����ȣ
                                var receiver_addr = response[i]['RECEIVER_ADDR']; //�������ּ�
                                var order_nm = response[i]['ORDER_NM']; //�ֹ���
                                var order_phone = response[i]['ORDER_PHONE']; //�ֹ�����ȭ��ȣ
                                var goods_delivery_name = response[i]['GOODS_DELIVERY_NAME']; //�ֹ���ǰ
                                var delivery_cp = response[i]['DELIVERY_CP']; //���ȸ��
                                var delivery_no = response[i]['DELIVERY_NO']; //�����ȣ
                                var delivery_fee = response[i]['DELIVERY_FEE']; //����
                                var delivery_fee_code = response[i]['DELIVERY_FEE_CODE']; //����Ÿ��
                                var reg_date = response[i]['REG_DATE']; //�����

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
                            console.log("������ �����Ͱ� �����ϴ�.");
                        }
                    } else {
                        console.log("������ �����Ͱ� �����ϴ�.");
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
                //���� �ε����϶� �ٸ� ���� ��ȸ�ϸ� ��ȸ �� ��, �ε� �� �Ǹ� ���� ���� �ε��ؾ���
                checkState(0,cnt);
            });
        });
    </script>
</head>

<body>
    <!-- <iframe src='https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo=232009815833'></iframe> -->
    ��ȸ�Ⱓ : <input type="date" id="startDate" value="<?= $start_date ?>" /> ~ <input type="date" id="endDate" value="<?= $end_date ?>" />

    <br>
    �ù�ȸ�� :
    <select id="deliveryCp">
        <option value="�Ե��ù�">�Ե��ù�</option>
        <option value="CJ�������">CJ�������</option>
    </select>
    <input type="button" value="��ȸ" id="searchBtn" />
    <br>
    <input type="button" value="��ۻ��� Ȯ��" id="checkState" />
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