<?
	require "_common/home_pre_setting.php";
	require "_classes/biz/member/member.php";
    
    if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-12 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
    }
    
    function getMemberGoods($db,$cp_no,$start_date,$end_date,$cate_cd,$cate_04,$search_option,$search_str){
        $query ="SELECT A.*, B.CATE_NAME
                        FROM TBL_GOODS A JOIN TBL_CATEGORY B ON A.GOODS_CATE = B.CATE_CD
                        WHERE A.CATE_03 = '$cp_no'
                            AND A.DEL_TF = 'N'
                            AND A.REG_DATE BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                            AND A.GOODS_CATE LIKE '17%' 
        ";

        if($cate_cd != ""){
            $query .=" AND A.GOODS_CATE LIKE '$cate_cd%' ";
        }

        if($cate_04 != ""){
            $query .=" AND A.CATE_04 = '$cate_04' ";
        }

        if($search_str != ""){
            if($search_option == "상품명"){
                $query .=" AND A.GOODS_NAME LIKE '%$search_str%' ";
            } else if($search_option == "제조사"){
                $query .=" AND A.CATE_02 LIKE '%$search_str%' ";
            } else if($search_option == "옵션"){
                $query .=" AND A.GOODS_SUB_NAME LIKE '%$search_str%' ";
            } else {
                //미선택(종합검색)
                $query .="  AND(
                                A.GOODS_NAME LIKE '%$search_str%' OR
                                A.CATE_02 LIKE '%$search_str%' OR
                                A.GOODS_SUB_NAME LIKE '%$search_str%'
                            )
                ";
            }
        }
        
        $query .=" ORDER BY A.REG_DATE DESC";

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
    
    function makeDefaultCategoryOption($db, $checkitem = null){
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
            if($record[$i]["CATE_CD"] != $checkitem)
                $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."'>".$record[$i]["CATE_NAME"]."</option>";
            else
                $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."' selected>".$record[$i]["CATE_NAME"]."</option>";
        }
        
        if($selectOptions == ""){
            return "<option value=''>선택</option>";
        } else {
            return $selectOptions;
        }
    }

    $mem_no = $_SESSION['C_MEM_NO'];

    $mem_arr_rs = selectMember($conn, $mem_no);
	$rs_mem_id = trim($mem_arr_rs[0]["MEM_ID"]);
    $rs_zipcode = SetStringFromDB(trim($mem_arr_rs[0]["ZIPCODE"])); 
    $rs_cp_no = SetStringFromDB(trim($mem_arr_rs[0]["CP_NO"])); 
    
    if($goods_cate2 == "" || $goods_cate2 == "선택"){
        $cate_cd = $goods_cate;
    } else {
        $cate_cd = $goods_cate2;
    }

    $arr_rs = getMemberGoods($conn,$rs_cp_no,$start_date,$end_date,$cate_cd,$cate_04,$search_option,$search_str);  
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";
?>
</head>
<body>
<?
	require "_common/v2_top.php";
?>

<!-- 마이페이지 -->
<div class=" members signin" style="width:100%;padding-left:15px;padding-right:15px;">
    <h5 class="title">내 판매 상품</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
            <input type="hidden" name="mode" value="">
            <ul class="nav nav-pills navbar-right">
                <li class="active">내 판매 상품</li>
                <li class=""><a href="/goods_management.php" style="padding:0;color:grey;">상품 관리</a></li>
            </ul>
            <div class="form-group">
                <label class="control-label col-sm-2" for="email">날짜</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control txt datepicker" style="width: 110px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
                    &nbsp;&nbsp;~&nbsp;&nbsp;
                    <input type="text" class="form-control txt datepicker" style="width: 110px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
                </div>
                <label class="control-label col-sm-2" for="zipcode">카테고리</label>
                <div class="col-sm-5">
                    <select id="goods_cate" name="goods_cate" class="form-control" style="width:49%;display:inline;">
                    <?=makeDefaultCategoryOption($conn, $goods_cate)?>
                    </select>
                    <select id="goods_cate2" name="goods_cate2" class="form-control" style="width:49%;display:inline;">
                        <option value="">선택</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2" for="zipcode">판매상태</label>
                <div class="col-sm-3">
                    <select class="form-control" name="cate_04" id="cate_04">CATE_NAME
                        <option value="">선택</option>
                        <option value="판매중">판매중</option>
                        <option value="품절">품절</option>
                        <option value="단종">단종</option>
                    </select>
                </div>
                <label class="control-label col-sm-2" for="zipcode">검색조건</label>
                <div class="col-sm-5">
                    <select class="form-control" style="width:20%;display:inline;" name="search_option" id="search_option">
                        <option value="">선택</option>
                        <option value="상품명">상품명</option>
                        <option value="제조사">제조사</option>
                        <option value="옵션">옵션(모델명)</option>
                    </select>
                    <input type="text" name="search_str" id="search_str" class="form-control" style="width:57%;" />
					<button type="button" name="search_btn" id="search_btn" class="btn btn-default form-control" style="width:20%;display:inline;margin-bottom:3px;">검색</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        td, th{
            white-space: nowrap;
            text-align:center;
        }
        .border{
            border:solid 1px #e0e0e0;
        }
        #accept_list{
            overflow:auto;
            width:100%;
            height:500px;
        }
        .members .contents{
            margin-bottom:10px;
        }
        .item:hover{
            cursor:pointer;
        }
    </style>

    <div style="text-align:right; padding-bottom:10px;">
        <button type="button" id="insertBtn" class="btn btn-default">신규등록</button>
    </div>

    <div id="accept_list">
        <table class="table table-hover border" style="background-color:#fff;width:100%;">
            <thead class="thead-light">
                <tr>
                    <th scope="col" class="border" ><input type="checkbox" id="chk_all" /></th>
                    <th scope="col" class="border" >이미지</th>
                    <th scope="col" class="border" >상품명</th>
                    <th scope="col" class="border" >옵션(모델명)</th>
                    <th scope="col" class="border" >판매상태</th>
                    <th scope="col" class="border" >카테고리</th>
                    <th scope="col" class="border" >공급가</th>
                    <th scope="col" class="border" >스티커비용</th>
                    <th scope="col" class="border" >포장인쇄비용</th>
                    <th scope="col" class="border" >택배비용</th>
                    <th scope="col" class="border" >박스입수</th>
                    <th scope="col" class="border" >물류비</th>
                    <th scope="col" class="border" >인건비</th>
                    <th scope="col" class="border" >기타비용</th>
                    <th scope="col" class="border" >추가비용합계</th>
                    <th scope="col" class="border" >공급합계</th>
                    <th scope="col" class="border" >제조사</th>
                    <th scope="col" class="border" >공급처</th>
                    <th scope="col" class="border" >등록일</th>
                </tr>
            </thead>
            <tbody>
                <?
                for($i=0;$i<count($arr_rs);$i++){
                    $GOODS_NO = $arr_rs[$i]["GOODS_NO"];
                    $NORMAL_FILE_NM = $arr_rs[$i]["NORMAL_FILE_NM"];
                    $GOODS_NAME = $arr_rs[$i]["GOODS_NAME"];
                    $GOODS_SUB_NAME = $arr_rs[$i]["GOODS_SUB_NAME"];
                    $CATE_04 = $arr_rs[$i]["CATE_04"];
                    $GOODS_CATE = $arr_rs[$i]["CATE_NAME"];
                    $BUY_PRICE = $arr_rs[$i]["BUY_PRICE"];
                    $STICKER_PRICE = $arr_rs[$i]["STICKER_PRICE"];
                    $PRINT_PRICE = $arr_rs[$i]["PRINT_PRICE"];
                    $DELIVERY_PRICE = $arr_rs[$i]["DELIVERY_PRICE"];
                    $DELIVERY_PRICE = $arr_rs[$i]["DELIVERY_PRICE"];
                    $DELIVERY_CNT_IN_BOX = $arr_rs[$i]["DELIVERY_CNT_IN_BOX"];
                    $LABOR_PRICE = $arr_rs[$i]["LABOR_PRICE"];
                    $OTHER_PRICE = $arr_rs[$i]["OTHER_PRICE"];
                    $EXTRA_PRICE = $arr_rs[$i]["EXTRA_PRICE"];
                    $PRICE = $arr_rs[$i]["PRICE"];
                    $CATE_02 = $arr_rs[$i]["CATE_02"];
                    $CATE_03 = $arr_rs[$i]["CATE_03"];
                    $REG_ADM = $arr_rs[$i]["REG_ADM"];
                    $REG_DATE = $arr_rs[$i]["REG_DATE"];
                    $REASON = $arr_rs[$i]["REASON"];
                    
                    $IMG_URL = trim($arr_rs[$i]["IMG_URL"]);
                    $FILE_NM = trim($arr_rs[$i]["FILE_NM_100"]);
                    $FILE_RNM = trim($arr_rs[$i]["FILE_RNM_100"]);
                    $FILE_PATH = trim($arr_rs[$i]["FILE_PATH_100"]);
                    $FILE_SIZE = trim($arr_rs[$i]["FILE_SIZE_100"]);
                    $FILE_EXT = trim($arr_rs[$i]["FILE_EXT_100"]);
                    $FILE_NM_150 = trim($arr_rs[$i]["FILE_NM_150"]);
                    $FILE_RNM_150 = trim($arr_rs[$i]["FILE_RNM_150"]);
                    $FILE_PATH_150 = trim($arr_rs[$i]["FILE_PATH_150"]);
                    $FILE_SIZE_150 = trim($arr_rs[$i]["FILE_SIZE_150"]);
                    $FILE_EXT_150 = trim($arr_rs[$i]["FILE_EXT_150"]);

                    $img_url = getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "100", "100");
                ?>
                <tr>
                    <td scope="row"  class="border" ><input type="checkbox" id="chk_<?=$GOODS_NO?>" value="<?=$GOODS_NO?>" /></td>
                    <td scope="row"  class="item border" ><img src="<?=$img_url?>" width="100px" height="100px" /></td>
                    <td scope="row"  class="item border" ><?=$GOODS_NAME?></td>
                    <td scope="row"  class="item border" ><?=$GOODS_SUB_NAME?></td>
                    <td scope="row"  class="item border" ><?=$CATE_04?></td>
                    <td scope="row"  class="item border" ><?=$GOODS_CATE?></td>
                    <td scope="row"  class="item border" ><?=number_format($BUY_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($STICKER_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($PRINT_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($DELIVERY_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($DELIVERY_CNT_IN_BOX)?></td>
                    <td scope="row"  class="item border" ><?=number_format(($DELIVERY_PRICE/$DELIVERY_CNT_IN_BOX))?></td>
                    <td scope="row"  class="item border" ><?=number_format($LABOR_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($OTHER_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($EXTRA_PRICE)?></td>
                    <td scope="row"  class="item border" ><?=number_format($PRICE)?></td>
                    <td scope="row"  class="item border" ><?=$CATE_02?></td>
                    <td scope="row"  class="item border" ><?=getCompanyNameOnly($conn,$CATE_03)?></td>
                    <td scope="row"  class="item border" ><?=$REG_DATE?></td>
                </tr>
                <?}?>
            </tbody>
        </table>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script type="text/javascript" src="/manager/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="/manager/jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="/manager/jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="/manager/jquery/jquery.cookie.js"></script>
<script>
    $(document).ready(function(){
        $( ".datepicker" ).datepicker({
            showOn: "button",
            buttonImage: "/manager/images/calendar/cal.gif",
            buttonImageOnly: true,
            buttonText: "Select date",
            showOn: "both",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 99999999999999);
                }, 0);
            }
        });//datepicker

        //페이지 리로드 됐을 때 검색 조건 기억(카테고리 레벨2)
        $.ajax({
            url: '/manager/ajax_processing.php',
            dataType: 'text',
            type: 'post',
            data : {
                "mode":"SELECT_CATE_LEVEL3",
                "cate_cd":"<?=$goods_cate?>"
            },
            success: function(response) {
                if(response.length > 0){
                    $("#goods_cate2").html(response);
                    $("#goods_cate2").val("<?=$goods_cate2?>");
                }
            },
            error : function (jqXHR, textStatus, errorThrown) {
                alert('ERRORS: ' + textStatus);
            }
        });//ajax

        //페이지 리로드 됐을 때 검색 조건 기억(판매상태)
        $("#cate_04").val("<?=$cate_04?>");
        
        //페이지 리로드 됐을 때 검색 조건 기억(판매상태)
        $("#search_option").val("<?=$search_option?>");

        //페이지 리로드 됐을 때 검색 조건 기억(판매상태)
        $("#search_str").val("<?=$search_str?>");

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
        
        //insertBtn
        $("#insertBtn").click(function(){
            location.href="./member_goods_proposal.php"
        });

        //delBtn
        $("#delBtn").click(function(){
            //선택된 체크박스들
            var selectedCheckBoxs = $("tbody > tr > td > input:checked");

            //ajax 삭제 요청 : 승인된 요청은 삭제 또는 수정이 불가능함, 승인되지 않은 요청만 삭제 가능

            //요청 성공 시 삭제요청 컬럼 변경

            //행 삭제
            for(i=0;i<selectedCheckBoxs.length;i++){
                selectedCheckBoxs.eq(i).parents("td").parents("tr").remove();
            }
        });

        $("#chk_all").click(function(){
            //전체 선택/해제 체크박스 체크 여부
            var isChecked = $(this)[0].checked;

            //전체 체크박스
            var checkBoxs = $("tbody > tr > td > input");

            if(isChecked){
                //전체 선택
                for(i=0;i<checkBoxs.length;i++){
                    checkBoxs.eq(i)[0].checked = true;
                }
            } else {
                //전체 선택 해제
                for(i=0;i<checkBoxs.length;i++){
                    checkBoxs.eq(i)[0].checked = false;
                }
            }
        });

        //seaerch_btn
        $("#search_btn").click(function(){
            var frm = document.frm;
            frm.target = "";
            frm.method = "post";
            frm.action = "<?=$_SERVER[PHP_SELF]?>";
            frm.submit();
        });

        $(".item").click(function(){
            //선택된 제안 상품의 제안번호
            var goods_no = $(this).parents("tr").children("td:first").children("input").val();

            //이동할 주소
            var url = "https://www.giftnet.co.kr/member_goods_proposal.php?goods_no="+goods_no;

            //이동
            window.open(url, "test_name");
        });
    });//ready
</script>
</body>
</html>