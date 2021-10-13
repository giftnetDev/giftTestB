<?
    ini_set('memory_limit',-1);
	require "_common/home_pre_setting.php";
	require "_classes/biz/member/member.php";

    // similar_text("비교문자열1", "비교 문자열2", $percent);

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
            $dv_goods_cate2 = "선택";
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
        $dv_cate_04 = $arr_rs[0]["CATE_04"];// 판매상태
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
            $dv_goods_cate2 = "선택";
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
        $dv_cate_04 = $arr_rs[0]["CATE_04"];// 판매상태
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
        //공급가 = 매입가
        $dv_buy_price = 0;
        //박스입수
        $dv_delivery_cnt_in_box = 1;
        //스티커비용
        $dv_sticker_price = 0;
        //포장인쇄비용
        $dv_print_price = 0;
        //택배비용
        $dv_delivery_price = 0;
        //인건비
        $dv_labor_price = 0;
        //기타 비용
        $dv_other_price = 0;
        //추가비용
        $dv_extra_price = 0;
        //매입합계
        $dv_price = 0;
    }
    
    function makeDefaultCategoryOption($db){
        //납품업체는 전체 카테고리가 필요없고, 단품 카테고리만 보여주면 되기 때문에 레벨2 단품부터 가져온다.
        $query =    "SELECT CATE_CD, REPLACE(CATE_NAME,'단품 ','') AS CATE_NAME
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
        
        $selectOptions = "<option value=''>선택</option>";
        for($i=0;$i<sizeof($record);$i++){
            $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."'>".$record[$i]["CATE_NAME"]."</option>";
        }
        
        if($selectOptions == ""){
            return "<option value=''>선택</option>";
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
        //매입가 변경시
        $("#buy_price").change(function(){
            var temp_sum = parseInt($("#buy_price").val())+parseInt($("#extra_price").val());
            $("#price").val(temp_sum);
        });

        //그외 추가 비용 항목들 변경시
        $("#sticker_price, #print_price, #delivery_price, #delivery_cnt_in_box, #labor_price, #other_price").change(function(){
            //추가비용 변경
            var temp_sum = parseInt($("#sticker_price").val())+parseInt($("#print_price").val())+(parseInt($("#delivery_price").val())/parseInt($("#delivery_cnt_in_box").val()))+parseInt($("#labor_price").val())+parseInt($("#other_price").val());
            $("#extra_price").val(temp_sum);
            //매입합계 변경
            var temp_sum2 = parseInt($("#extra_price").val())+parseInt($("#buy_price").val());
            $("#price").val(temp_sum2);
        });

        //첫번째 카테고리 셀렉트 선택 시 두번째 카테고리 셀렉트 표시
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

        //페이지 리로드 됐을 때 검색 조건 기억(카테고리 레벨2)
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
                    //페이지 리로드 됐을 때 검색 조건 기억(카테고리2))
                    $("#goods_cate2").val("<?=$dv_goods_cate2?>");
                }
            },
            error : function (jqXHR, textStatus, errorThrown) {
                alert('ERRORS: ' + textStatus);
            }
        });//ajax

        //초기화 버튼
        $("#resetBtn").click(function(){
            //카테고리
            $("#goods_cate").val("");
            $("#goods_cate2").val("");
            //상품명
            $("#goods_name").val("");
            //상품옵션
            $("#goods_sub_name").val("");
            //제조사
            $("#cate_02").val("");
            //판매상태
            $("#cate_04").val("판매중");
            //매입합계
            $("#price").val(0);
            //공급가 = 매입가
            $("#buy_price").val(0);
            //매입합계 - 매입가 = 추가비용
            $("#extra_price").val(0);
            //상품상세 텍스트
            $("#contents").val("");
            //상품 메모
            $("#memo").val("");
            //박스입수
            $("#delivery_cnt_in_box").val(0);
            //스티커비용
            $("#sticker_price").val(0);
            //포장인쇄비용
            $("#print_price").val(0);
            //택배비용
            $("#delivery_price").val(0);
            //인건비
            $("#labor_price").val(0);
            //기타 비용
            $("#other_price").val(0);
            //일반이미지
            $("#img_normal").val("");;
            //상세이미지
            $("#img_detail").val("");;
        });//resetBtn click

        //등록버튼
        $("#saveBtn").click(function(){
            //유효성 검사
            var error_msg="";

            //카테고리 1차 필수
            if($("#goods_cate").val()==""){
                error_msg += "카테고리를 선택해주세요 1차 카테고리는 필수입니다(2차 선택)<br>";
            }

            //상품명 공백 X
            if($("#goods_name").val()==""){
                error_msg += "상품명을 입력해주세요.<br>";
            }

            // 판매상태  판매중 or 단종 or 품절중 하나인지 확인
            if($("#cate_04").val()!="판매중" && $("#cate_04").val()!="품절" && $("#cate_04").val()!="단종"){
                error_msg += "판매상태를 선택해주세요.<br>";
            }
            
            //공급가 1원이상
            if($("#buy_price").val()<1){
                error_msg += "공급가를 입력해주세요.<br>";
            }

            //박스입수 1이상인지 확인
            if($("#delivery_cnt_in_box").val()<=0){
                error_msg += "박스입수는 최소 1개입니다.<br>";
            }
            
            //이미지 일반 : 확장자 검사
            if($("#img_normal").val()!=""){
                var ext = $('#img_normal').val().split('.').pop().toLowerCase();
                if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                    error_msg += "[일반 이미지]이미지 파일이 아닙니다. 파일을 다시 선택해주세요.<br>";
                }
            }

            //이미지 상세 : 확장자 검사
            if($("#img_detail").val()!=""){
                var ext = $('#img_detail').val().split('.').pop().toLowerCase();
                if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                    error_msg += "[상세 이미지]이미지 파일이 아닙니다. 파일을 다시 선택해주세요.<br>";
                }
            }

            //유효성 검사 결과 표시
            $("#error_msg").html(error_msg);
            if($("#error_msg").html()==""){
                $("#error_msg").hide();
                //요청 삽입 or 수정
                //카테1만 입력했는지 카테2까지 입력했는지에 따라 전달하는 카테코드 상이
                var goods_cate ="";
                if($("#goods_cate2").val() == "" || $("#goods_cate2").val() == "선택"){
                    goods_cate = $("#goods_cate").val();
                } else {
                    goods_cate = $("#goods_cate2").val();
                }
                //이미지 파일 AJAX넘기기 전 처리
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
                                alert("신규 등록하였습니다.");
                                //id받아옴
                            } else if(response == "update"){
                                alert("수정되었습니다.");
                                //id받아옴
                            } else {
                                alert("등록 실패하였습니다.");
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

        //페이지 리로드 됐을 때 검색 조건 기억(판매상태)
        if("<?=$dv_cate_04?>" == ""){
            $("#cate_04").val("판매중");    
        } else {
            $("#cate_04").val("<?=$dv_cate_04?>");
        }

        //페이지 리로드 됐을 때 검색 조건 기억(카테고리))
        $("#goods_cate").val("<?=$dv_goods_cate1?>");        
    });//ready
    </script>
</head>
<body>
<?
	require "_common/v2_top.php";
?>

<!-- 마이페이지 -->
<div class=" members signin" style="width:100%;padding-left:15px;padding-right:15px;">
    <h5 class="title">상품 등록</h5>
    <div class="contents">
        <form name="frm" id="frm" class="form-horizontal in-signin" method="post">
            <input type="hidden" name="mode" value="">
            <div id="error_msg" class="alert alert-danger alert-dismissible" role="alert" style="display:none;"></div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*카테고리</label>
                <div class="col-sm-7">
                    <select id="goods_cate" class="form-control" style="width:49%;display:inline;">
                        <?=makeDefaultCategoryOption($conn)?>
                    </select>
                    <select id="goods_cate2" class="form-control" style="width:49%;display:inline;">
                        <option value="">선택</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[필수]</dt><dd style="color:#707070;">상품의 카테고리를 선택해주세요.<br>적절한 카테고리가 없다면 기타를 선택해주세요.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*상품명</label>
                <div class="col-sm-7">
                    <input type="text" id="goods_name" style="width:100%;" class="form-control" value="<?=$dv_goods_name?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[필수]</dt><dd style="color:#707070;"></dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">옵션(모델명)</label>
                <div class="col-sm-7">
                    <input type="text" id="goods_sub_name" style="width:100%;" class="form-control" value="<?=$dv_goods_sub_name?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택(옵션이 있다면 필수)]</dt><dd style="color:#707070;">상품의 색상, 사이즈, 구성 등을 입력해주세요.<br>여러 개라면 콤마로 구분해주세요.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*판매상태</label>
                <div class="col-sm-7">
                    <select class="form-control" id="cate_04">
                        <option value="판매중">판매중</option>
                        <option value="품절">품절</option>
                        <option value="단종">단종</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[필수]</dt><dd style="color:#707070;"></dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">공급사</label>
                <div class="col-sm-7">
                    <input type="text" id="cate_03_text" style="width:100%;" class="form-control" value="<?=$rs_cp_nm?>" readonly>
                    <input type="hidden" id="cate_03"  name="cate_03" value="<?=$mem_arr_rs[0]["CP_NO"]?>" />
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">자동입력됩니다.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">제조사</label>
                <div class="col-sm-7">
                    <input type="text" id="cate_02" style="width:100%;" class="form-control" value="<?=$dv_cate_02?>">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">물건을 제조한 곳을 입력해주세요.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*공급가</label>
                <div class="col-sm-7">
                    <input type="number" id="buy_price" min="0" value="<?=$dv_buy_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[필수]</dt><dd style="color:#707070;">기프트넷에 납품하는 가격을 입력해주세요.<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">스티커비용</label>
                <div class="col-sm-7">
                    <input type="number" id="sticker_price" min="0" value="<?=$dv_sticker_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">스티커 부착시 얼마의 비용이 추가되나요?<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">포장인쇄비용</label>
                <div class="col-sm-7">
                    <input type="number" id="print_price" min="0" value="<?=$dv_print_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">포장 인쇄 시 얼마의 비용이 추가되나요?<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">택배비용</label>
                <div class="col-sm-7">
                    <input type="number" id="delivery_price" min="0" value="<?=$dv_delivery_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">택배 비용을 입력해주세요.<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" style="color:red;">*박스입수</label>
                <div class="col-sm-7">
                    <input type="number" id="delivery_cnt_in_box" min="1" value="<?=$dv_delivery_cnt_in_box?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt style="color:red;">[필수]</dt><dd style="color:#707070;">한 박스에 몇 개의 상품이 들어가나요?</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">인건비</label>
                <div class="col-sm-7">
                    <input type="number" id="labor_price" min="0" value="<?=$dv_labor_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">인건비 추가시 개당 얼마의 비용이 추가되나요?<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">기타비용</label>
                <div class="col-sm-7">
                    <input type="number" id="other_price" min="0" value="<?=$dv_other_price?>" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">기타 비용 발생 시 입력해주세요.<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label title="스티커비용+포장인쇄비용+(택배비용/박스입수)+인건비+기타비용=추가비용" class="control-label col-sm-2">추가비용</label>
                <div class="col-sm-7">
                    <input type="number" id="extra_price" min="0" value="<?=$dv_extra_price?>" style="width:100%;" class="form-control" readonly>
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">자동입력됩니다.<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label title="공급가+추가비용=매입합계" class="control-label col-sm-2">매입합계</label>
                <div class="col-sm-7">
                    <input type="number" id="price" min="0" value="<?=$dv_price?>" style="width:100%;" class="form-control" readonly>
                </div>
                <div class="col-sm-3">
                    <dl><dd style="color:#707070;">자동입력됩니다.<br>단위(원)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">이미지(일반)</label>
                <div class="col-sm-7">
                    <input type="file" id="img_normal" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd>일반적인 상품 이미지를 업로드해주세요.<br>500x500 권장(px)</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">상품상세(텍스트)</label>
                <div class="col-sm-7">
                    <textarea  id="contents" class="form-control" > <?=$dv_contents?></textarea>
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">텍스트로 된 상세 상품 설명이 필요한 경우 작성해주세요.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">이미지(상세)</label>
                <div class="col-sm-7">
                    <input type="file" id="img_detail" style="width:100%;" class="form-control">
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">상품상세 이미지를 업로드해주세요.</dd></dl>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">메모</label>
                <div class="col-sm-7">
                    <textarea  id="memo" class="form-control" ><?=$dv_memo?></textarea>
                </div>
                <div class="col-sm-3">
                    <dl><dt>[선택]</dt><dd style="color:#707070;">상품에 대해 추가적으로 알려주실 사항이 있으시면 적어주세요.</dd></dl>
                </div>
            </div>
            <?if($dv_acpt_tf == "W"){?>
            <div class="form-group" style="text-align:right; padding-bottom:10px;">
                <button type="button" id="resetBtn" class="btn btn-default">초기화</button>
                <button type="button" id="saveBtn" class="btn btn-default">등&nbsp;&nbsp;록</button>
            </div>
            <?} else {?>
            <div class="form-group" style="text-align:right; padding-bottom:10px;">
                처리 완료된 상품은 수정하실 수 없습니다.
            </div>
            <?}?>
        </form>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
</body>
</html>