<?
    ini_set('memory_limit',-1);
	require "_common/home_pre_setting.php";
	require "_classes/biz/member/member.php";

    // similar_text("�񱳹��ڿ�1", "�� ���ڿ�2", $percent);

    function getMemberProposalFromProposalNo($db,$proposal_no){
        $query ="SELECT *
                        FROM TBL_MEMBER_PROPOSAL
                        WHERE PROPOSAL_NO = '$proposal_no'
                            AND DEL_TF = 'N'
        ";

        // echo "$query";

        $result = mysql_query($query,$db);
        $record = array();

        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }

        return $record;
    }

    function getMemberProposalFromGoodsNo($db, $goods_no, $cp_no){
        $query ="SELECT A.*, B.CATE_NAME
                        FROM TBL_GOODS A JOIN TBL_CATEGORY B ON A.GOODS_CATE = B.CATE_CD
                        WHERE A.GOODS_NO = '$goods_no'
                            AND A.DEL_TF = 'N'
                            AND A.CATE_03 = '$cp_no'
        ";
        
        // echo $query;

        $result = mysql_query($query,$db);
        $record = array();
        
        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }

        return $record;
    }

	$mem_no = $_SESSION['C_MEM_NO'];

    $mem_arr_rs = selectMember($conn, $mem_no);
	$rs_mem_id = trim($mem_arr_rs[0]["MEM_ID"]);
	$rs_cp_nm = getCompanyNameOnly($conn, trim($mem_arr_rs[0]["CP_NO"]));
    $rs_mem_nm = SetStringFromDB(trim($mem_arr_rs[0]["MEM_NM"]));

    if($proposal_no != ""){
        $arr_rs = getMemberProposalFromProposalNo($conn,$proposal_no);

        //default value
        // goods_cate , goods_cate2
        $temp_goods_cate = $arr_rs[0]["GOODS_CATE"];
        $dv_goods_cate1 = substr($temp_goods_cate,0,4);
        $dv_goods_cate2 = substr($temp_goods_cate,0,6);
        if(strlen($temp_goods_cate)!=6){
            $dv_goods_cate2 = "����";
        }
        // goods_name
        $dv_goods_name = $arr_rs[0]["GOODS_NAME"];
        // goods_sub_name
        $dv_goods_sub_name = $arr_rs[0]["GOODS_SUB_NAME"];
        // cate_02
        $dv_cate_02 = $arr_rs[0]["CATE_02"];
        // cate_03
        $dv_cate_03 = $arr_rs[0]["CATE_03"];
        // cate_03_text
        $rs_cp_nm = getCompanyNameOnly($conn, $dv_cate_03);
        // cate_04
        $dv_cate_04 = $arr_rs[0]["CATE_04"];// �ǸŻ���
        // price
        $dv_price = $arr_rs[0]["PRICE"];
        // buy_price
        $dv_buy_price = $arr_rs[0]["BUY_PRICE"];
        // extra_price
        $dv_extra_price = $arr_rs[0]["EXTRA_PRICE"];
        // img_normal
        $dv_normal_file_nm = $arr_rs[0]["NORMAL_FILE_NM"];
        // img_detail
        $dv_detail_file_nm = $arr_rs[0]["DETAIL_FILE_NM"];
        // contents
        $dv_contents = $arr_rs[0]["CONTENTS"];
        // memo
        $dv_memo = $arr_rs[0]["MEMO"];
        // delivery_cnt_in_box
        $dv_delivery_cnt_in_box = $arr_rs[0]["DELIVERY_CNT_IN_BOX"];
        // sticker_price
        $dv_sticker_price = $arr_rs[0]["STICKER_PRICE"];
        // print_price
        $dv_print_price = $arr_rs[0]["PRINT_PRICE"];
        // delivery_price
        $dv_delivery_price = $arr_rs[0]["DELIVERY_PRICE"];
        // labor_price
        $dv_labor_price = $arr_rs[0]["LABOR_PRICE"];
        // other_price
        $dv_other_price = $arr_rs[0]["OTHER_PRICE"];
        //acpt_tf
        $dv_acpt_tf = $arr_rs[0]["ACPT_TF"];
    } else if($goods_no != ""){
        $arr_rs = getMemberProposalFromGoodsNo($conn,$goods_no, $mem_arr_rs[0]["CP_NO"]);
        
        //default value
        // goods_cate , goods_cate2
        $temp_goods_cate = $arr_rs[0]["GOODS_CATE"];
        $dv_goods_cate1 = substr($temp_goods_cate,0,4);
        $dv_goods_cate2 = substr($temp_goods_cate,0,6);

        // echo "temp_goods_cate : $temp_goods_cate, dv_goods_cate1 : $dv_goods_cate1, dv_goods_cate2 : $dv_goods_cate2<br>";
        
        if(strlen($temp_goods_cate)!=6){
            $dv_goods_cate2 = "����";
        }
        // goods_name
        $dv_goods_name = $arr_rs[0]["GOODS_NAME"];
        // goods_sub_name
        $dv_goods_sub_name = $arr_rs[0]["GOODS_SUB_NAME"];
        // cate_02
        $dv_cate_02 = $arr_rs[0]["CATE_02"];
        // cate_03
        $dv_cate_03 = $arr_rs[0]["CATE_03"];
        // cate_03_text
        $rs_cp_nm = getCompanyNameOnly($conn, $dv_cate_03);
        // cate_04
        $dv_cate_04 = $arr_rs[0]["CATE_04"];// �ǸŻ���
        // price
        $dv_price = $arr_rs[0]["PRICE"];
        // buy_price
        $dv_buy_price = $arr_rs[0]["BUY_PRICE"];
        // extra_price
        $dv_extra_price = $arr_rs[0]["EXTRA_PRICE"];
        // img_normal
        $dv_normal_file_nm = $arr_rs[0]["NORMAL_FILE_NM"];
        // img_detail
        $dv_detail_file_nm = $arr_rs[0]["DETAIL_FILE_NM"];
        // contents
        $dv_contents = $arr_rs[0]["CONTENTS"];
        // memo
        $dv_memo = $arr_rs[0]["MEMO"];
        // delivery_cnt_in_box
        $dv_delivery_cnt_in_box = $arr_rs[0]["DELIVERY_CNT_IN_BOX"];
        // sticker_price
        $dv_sticker_price = $arr_rs[0]["STICKER_PRICE"];
        // print_price
        $dv_print_price = $arr_rs[0]["PRINT_PRICE"];
        // delivery_price
        $dv_delivery_price = $arr_rs[0]["DELIVERY_PRICE"];
        // labor_price
        $dv_labor_price = $arr_rs[0]["LABOR_PRICE"];
        // other_price
        $dv_other_price = $arr_rs[0]["OTHER_PRICE"];
    } else {
        //default value
        //���ް� = ���԰�
        $dv_buy_price = 0;
        //�ڽ��Լ�
        $dv_delivery_cnt_in_box = 1;
        //��ƼĿ���
        $dv_sticker_price = 0;
        //�����μ���
        $dv_print_price = 0;
        //�ù���
        $dv_delivery_price = 0;
        //�ΰǺ�
        $dv_labor_price = 0;
        //��Ÿ ���
        $dv_other_price = 0;
        //�߰����
        $dv_extra_price = 0;
        //�����հ�
        $dv_price = 0;
    }
    
    function makeDefaultCategoryOption($db){
        //��ǰ��ü�� ��ü ī�װ��� �ʿ����, ��ǰ ī�װ��� �����ָ� �Ǳ� ������ ����2 ��ǰ���� �����´�.
        $query =    "SELECT CATE_CD, REPLACE(CATE_NAME,'��ǰ ','') AS CATE_NAME
                            FROM TBL_CATEGORY
                            WHERE LENGTH(CATE_CD) = '4'
                                AND CATE_CD LIKE '17%'
                                AND DEL_TF = 'N'
                                AND USE_TF = 'Y'
                            ORDER BY CATE_SEQ02 ASC
        ";

        //echo $query;
    
        $result = mysql_query($query,$db);
        $record = array();
        
        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }
        
        $selectOptions = "<option value=''>����</option>";
        for($i=0;$i<sizeof($record);$i++){
            $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."'>".$record[$i]["CATE_NAME"]."</option>";
        }
        
        if($selectOptions == ""){
            return "<option value=''>����</option>";
        } else {
            return $selectOptions;
        }
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
?>
<script>
    $(document).ready(function(){
        //���԰� �����
        $("#buy_price").change(function(){
            var temp_sum = parseInt($("#buy_price").val())+parseInt($("#extra_price").val());
            $("#price").val(temp_sum);
        });

        //�׿� �߰� ��� �׸�� �����
        $("#sticker_price, #print_price, #delivery_price, #delivery_cnt_in_box, #labor_price, #other_price").change(function(){
            //�߰���� ����
            var temp_sum = parseInt($("#sticker_price").val())+parseInt($("#print_price").val())+(parseInt($("#delivery_price").val())/parseInt($("#delivery_cnt_in_box").val()))+parseInt($("#labor_price").val())+parseInt($("#other_price").val());
            $("#extra_price").val(temp_sum);
            //�����հ� ����
            var temp_sum2 = parseInt($("#extra_price").val())+parseInt($("#buy_price").val());
            $("#price").val(temp_sum2);
        });

        //ù��° ī�װ� ����Ʈ ���� �� �ι�° ī�װ� ����Ʈ ǥ��
        $("#goods_cate").change(function(){
            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'text',
                type: 'post',
                data : {
                    "mode":"SELECT_CATE_LEVEL3",
                    "cate_cd":$("#goods_cate").val()
                },
                success: function(response) {
                    if(response.length > 0){
                        $("#goods_cate2").html(response);
                    }
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    alert('ERRORS: ' + textStatus);
                }
            });//ajax
        });//goods_cate2 change

        //������ ���ε� ���� �� �˻� ���� ���(ī�װ� ����2)
        $.ajax({
            url: '/manager/ajax_processing.php',
            dataType: 'text',
            type: 'post',
            data : {
                "mode":"SELECT_CATE_LEVEL3",
                "cate_cd":"<?=$dv_goods_cate1?>"
            },
            success: function(response) {
                if(response.length > 0){
                    $("#goods_cate2").html(response);
                    //������ ���ε� ���� �� �˻� ���� ���(ī�װ�2))
                    $("#goods_cate2").val("<?=$dv_goods_cate2?>");
                }
            },
            error : function (jqXHR, textStatus, errorThrown) {
                alert('ERRORS: ' + textStatus);
            }
        });//ajax

        //�ʱ�ȭ ��ư
        $("#resetBtn").click(function(){
            //ī�װ�
            $("#goods_cate").val("");
            $("#goods_cate2").val("");
            //��ǰ��
            $("#goods_name").val("");
            //��ǰ�ɼ�
            $("#goods_sub_name").val("");
            //������
            $("#cate_02").val("");
            //�ǸŻ���
            $("#cate_04").val("�Ǹ���");
            //�����հ�
            $("#price").val(0);
            //���ް� = ���԰�
            $("#buy_price").val(0);
            //�����հ� - ���԰� = �߰����
            $("#extra_price").val(0);
            //��ǰ�� �ؽ�Ʈ
            $("#contents").val("");
            //��ǰ �޸�
            $("#memo").val("");
            //�ڽ��Լ�
            $("#delivery_cnt_in_box").val(0);
            //��ƼĿ���
            $("#sticker_price").val(0);
            //�����μ���
            $("#print_price").val(0);
            //�ù���
            $("#delivery_price").val(0);
            //�ΰǺ�
            $("#labor_price").val(0);
            //��Ÿ ���
            $("#other_price").val(0);
            //�Ϲ��̹���
            $("#img_normal").val("");;
            //���̹���
            $("#img_detail").val("");;
        });//resetBtn click

        //��Ϲ�ư
        $("#saveBtn").click(function(){
            //��ȿ�� �˻�
            var error_msg="";

            //ī�װ� 1�� �ʼ�
            if($("#goods_cate").val()==""){
                error_msg += "ī�װ��� �������ּ��� 1�� ī�װ��� �ʼ��Դϴ�(2�� ����)<br>";
            }

            //��ǰ�� ���� X
            if($("#goods_name").val()==""){
                error_msg += "��ǰ���� �Է����ּ���.<br>";
            }

            // �ǸŻ���  �Ǹ��� or ���� or ǰ���� �ϳ����� Ȯ��
            if($("#cate_04").val()!="�Ǹ���" && $("#cate_04").val()!="ǰ��" && $("#cate_04").val()!="����"){
                error_msg += "�ǸŻ��¸� �������ּ���.<br>";
            }
            
            //���ް� 1���̻�
            if($("#buy_price").val()<1){
                error_msg += "���ް��� �Է����ּ���.<br>";
            }

            //�ڽ��Լ� 1�̻����� Ȯ��
            if($("#delivery_cnt_in_box").val()<=0){
                error_msg += "�ڽ��Լ��� �ּ� 1���Դϴ�.<br>";
            }
            
            //�̹��� �Ϲ� : Ȯ���� �˻�
            if($("#img_normal").val()!=""){
                var ext = $('#img_normal').val().split('.').pop().toLowerCase();
                if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                    error_msg += "[�Ϲ� �̹���]�̹��� ������ �ƴմϴ�. ������ �ٽ� �������ּ���.<br>";
                }
            }

            //�̹��� �� : Ȯ���� �˻�
            if($("#img_detail").val()!=""){
                var ext = $('#img_detail').val().split('.').pop().toLowerCase();
                if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                    error_msg += "[�� �̹���]�̹��� ������ �ƴմϴ�. ������ �ٽ� �������ּ���.<br>";
                }
            }

            //��ȿ�� �˻� ��� ǥ��
            $("#error_msg").html(error_msg);
            if($("#error_msg").html()==""){
                $("#error_msg").hide();
                //��û ���� or ����
                //ī��1�� �Է��ߴ��� ī��2���� �Է��ߴ����� ���� �����ϴ� ī���ڵ� ����
                var goods_cate ="";
                if($("#goods_cate2").val() == "" || $("#goods_cate2").val() == "����"){
                    goods_cate = $("#goods_cate").val();
                } else {
                    goods_cate = $("#goods_cate2").val();
                }
                //�̹��� ���� AJAX�ѱ�� �� ó��
                var normal_file = $("#img_normal")[0].files[0];
                var detail_file = $("#img_detail")[0].files[0];
                datas = new FormData();
                datas.append("mode", "INSERT_MEMBER_PROPOSAL");
                datas.append("normal_file", $("#img_normal")[0].files[0]);
                datas.append("detail_file", $("#img_detail")[0].files[0]);
                datas.append("mem_no",<?=$mem_no?>);
                datas.append("goods_cate",goods_cate);
                datas.append("goods_name",$("#goods_name").val());
                datas.append("goods_sub_name",$("#goods_sub_name").val());
                datas.append("cate_02",$("#cate_02").val());
                datas.append("cate_03",$("#cate_03").val());
                datas.append("cate_04",$("#cate_04").val());
                datas.append("price",$("#price").val());
                datas.append("buy_price",$("#buy_price").val());
                datas.append("extra_price",$("#extra_price").val());
                datas.append("contents",$("#contents").val());
                datas.append("memo",$("#memo").val());
                datas.append("delivery_cnt_in_box",$("#delivery_cnt_in_box").val());
                datas.append("sticker_price",$("#sticker_price").val());
                datas.append("print_price",$("#print_price").val());
                datas.append("delivery_price",$("#delivery_price").val());
                datas.append("labor_price",$("#labor_price").val());
                datas.append("other_price",$("#other_price").val());
                datas.append("mem_nm","<?=$rs_mem_nm?>");
                datas.append("proposal_no","<?=$proposal_no?>");
                datas.append("goods_no","<?=$goods_no?>");

                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'text',
                    contentType: 'multipart/form-data', 
				    mimeType: 'multipart/form-data',
                    type: 'post',
                    data : datas,
                    success: function(response) {
                        if(response.length > 0){
                            if(response == "insert"){
                                alert("�ű� ����Ͽ����ϴ�.");
                                //id�޾ƿ�
                            } else if(response == "update"){
                                alert("�����Ǿ����ϴ�.");
                                //id�޾ƿ�
                            } else {
                                alert("��� �����Ͽ����ϴ�.");
                            }
                        }
                    },
                    error : function (jqXHR, textStatus, errorThrown) {
                        alert('ERRORS: ' + textStatus);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });//ajax
            } else {
                $("#error_msg").show();
            }
        });

        //������ ���ε� ���� �� �˻� ���� ���(�ǸŻ���)
        if("<?=$dv_cate_04?>" == ""){
            $("#cate_04").val("�Ǹ���");    
        } else {
            $("#cate_04").val("<?=$dv_cate_04?>");
        }

        //������ ���ε� ���� �� �˻� ���� ���(ī�װ�))
        $("#goods_cate").val("<?=$dv_goods_cate1?>");        
    });//ready
    </script>
</head>
<body>
<?
	require "_common/v2_top.php";
?>

<!-- ���������� -->
<div class=" members signin" style="width:100%;padding-left:15px;padding-right:15px;">
    <h5 class="title">��ǰ ���</h5>
    <div class="contents">
        <form name="frm" id="frm" class="form-horizontal in-signin" method="post">
            <input type="hidden" name="mode" value="">
            <div id="error_msg" class="alert alert-danger alert-dismissible" role="alert" style="display:none;"></div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*ī�װ�</label>
                <div class="col-sm-7">
                    <select id="goods_cate" class="form-control" style="width:49%;display:inline;">
                        <?=makeDefaultCategoryOption($conn)?>
                    </select>
                    <select id="goods_cate2" class="form-control" style="width:49%;display:inline;">
                        <option value="">����</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[�ʼ�]</dt><dd style="color:#707070;">��ǰ�� ī�װ��� �������ּ���.<br>������ ī�װ��� ���ٸ� ��Ÿ�� �������ּ���.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*��ǰ��</label>
                <div class="col-sm-7">
                    <input type="text" id="goods_name" style="width:100%;" class="form-control" value="<?=$dv_goods_name?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[�ʼ�]</dt><dd style="color:#707070;"></dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�ɼ�(�𵨸�)</label>
                <div class="col-sm-7">
                    <input type="text" id="goods_sub_name" style="width:100%;" class="form-control" value="<?=$dv_goods_sub_name?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����(�ɼ��� �ִٸ� �ʼ�)]</dt><dd style="color:#707070;">��ǰ�� ����, ������, ���� ���� �Է����ּ���.<br>���� ����� �޸��� �������ּ���.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*�ǸŻ���</label>
                <div class="col-sm-7">
                    <select class="form-control" id="cate_04">
                        <option value="�Ǹ���">�Ǹ���</option>
                        <option value="ǰ��">ǰ��</option>
                        <option value="����">����</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[�ʼ�]</dt><dd style="color:#707070;"></dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">���޻�</label>
                <div class="col-sm-7">
                    <input type="text" id="cate_03_text" style="width:100%;" class="form-control" value="<?=$rs_cp_nm?>" readonly>
                    <input type="hidden" id="cate_03"  name="cate_03" value="<?=$mem_arr_rs[0]["CP_NO"]?>" />
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">�ڵ��Էµ˴ϴ�.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">������</label>
                <div class="col-sm-7">
                    <input type="text" id="cate_02" style="width:100%;" class="form-control" value="<?=$dv_cate_02?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">������ ������ ���� �Է����ּ���.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*���ް�</label>
                <div class="col-sm-7">
                    <input type="number" id="buy_price" min="0" value="<?=$dv_buy_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[�ʼ�]</dt><dd style="color:#707070;">����Ʈ�ݿ� ��ǰ�ϴ� ������ �Է����ּ���.<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">��ƼĿ���</label>
                <div class="col-sm-7">
                    <input type="number" id="sticker_price" min="0" value="<?=$dv_sticker_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">��ƼĿ ������ ���� ����� �߰��ǳ���?<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�����μ���</label>
                <div class="col-sm-7">
                    <input type="number" id="print_price" min="0" value="<?=$dv_print_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">���� �μ� �� ���� ����� �߰��ǳ���?<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�ù���</label>
                <div class="col-sm-7">
                    <input type="number" id="delivery_price" min="0" value="<?=$dv_delivery_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">�ù� ����� �Է����ּ���.<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*�ڽ��Լ�</label>
                <div class="col-sm-7">
                    <input type="number" id="delivery_cnt_in_box" min="1" value="<?=$dv_delivery_cnt_in_box?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[�ʼ�]</dt><dd style="color:#707070;">�� �ڽ��� �� ���� ��ǰ�� ������?</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�ΰǺ�</label>
                <div class="col-sm-7">
                    <input type="number" id="labor_price" min="0" value="<?=$dv_labor_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">�ΰǺ� �߰��� ���� ���� ����� �߰��ǳ���?<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">��Ÿ���</label>
                <div class="col-sm-7">
                    <input type="number" id="other_price" min="0" value="<?=$dv_other_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">��Ÿ ��� �߻� �� �Է����ּ���.<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label title="��ƼĿ���+�����μ���+(�ù���/�ڽ��Լ�)+�ΰǺ�+��Ÿ���=�߰����" class="control-label col-sm-2">�߰����</label>
                <div class="col-sm-7">
                    <input type="number" id="extra_price" min="0" value="<?=$dv_extra_price?>" style="width:100%;" class="form-control" readonly>
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">�ڵ��Էµ˴ϴ�.<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label title="���ް�+�߰����=�����հ�" class="control-label col-sm-2">�����հ�</label>
                <div class="col-sm-7">
                    <input type="number" id="price" min="0" value="<?=$dv_price?>" style="width:100%;" class="form-control" readonly>
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">�ڵ��Էµ˴ϴ�.<br>����(��)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�̹���(�Ϲ�)</label>
                <div class="col-sm-7">
                    <input type="file" id="img_normal" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd>�Ϲ����� ��ǰ �̹����� ���ε����ּ���.<br>500x500 ����(px)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">��ǰ��(�ؽ�Ʈ)</label>
                <div class="col-sm-7">
                    <textarea  id="contents" class="form-control" > <?=$dv_contents?></textarea>
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">�ؽ�Ʈ�� �� �� ��ǰ ������ �ʿ��� ��� �ۼ����ּ���.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�̹���(��)</label>
                <div class="col-sm-7">
                    <input type="file" id="img_detail" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">��ǰ�� �̹����� ���ε����ּ���.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">�޸�</label>
                <div class="col-sm-7">
                    <textarea  id="memo" class="form-control" ><?=$dv_memo?></textarea>
                </div>
                <div class="col-sm-3">
                    <dl><dt>[����]</dt><dd style="color:#707070;">��ǰ�� ���� �߰������� �˷��ֽ� ������ �����ø� �����ּ���.</dd></dl>
                </div>
            </div>
            <?if($dv_acpt_tf == "W"){?>
            <div class="form-group" style="text-align:right; padding-bottom:10px;">
                <button type="button" id="resetBtn" class="btn btn-default">�ʱ�ȭ</button>
                <button type="button" id="saveBtn" class="btn btn-default">��&nbsp;&nbsp;��</button>
            </div>
            <?} else {?>
            <div class="form-group" style="text-align:right; padding-bottom:10px;">
                ó�� �Ϸ�� ��ǰ�� �����Ͻ� �� �����ϴ�.
            </div>
            <?}?>
        </form>
    </div>
</div>
<!-- // ȸ������ -->

<?
	require "_common/v2_footer.php";
?>
</body>
</html>