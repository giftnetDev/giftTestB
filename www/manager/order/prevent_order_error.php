<? session_start(); ?>
<?
require "../../_classes/com/db/DBUtil.php";
$conn = db_connection("w");

$menu_right = "OD026"; // 메뉴마다 셋팅 해 주어야 합니다
require "../../_common/common_header.php";

require "../../_common/config.php";
require "../../_classes/com/etc/etc.php";
// require "../../_classes/com/util/Util.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="<?= $g_charset ?>" />
    <title><?= $g_title ?></title>
    <link rel="stylesheet" href="../css/admin.css" type="text/css" />
    <link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
    <script>
        var cate_cd = new Array();
        var cate_name = new Array();

        $(document).ready(function() {
            //first loading(cate) start
            var depth = 1;
            var cnt;
            var empty_option = "<option>선택 항목 없음</option>";
            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'json',
                type: 'post',
                data: {
                    'mode': "SELECT_CATEGORY",
                    'depth': depth
                },
                success: function(response) {
                    cnt = response.length;
                    var option = "";
                    for (var i = 0; i < cnt; i++) {
                        option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                    } //for
                    if (cnt != 0) {
                        $("#cate_depth1").html(option);
                        $("#cate_depth1 > option[value='30']").attr("selected", "true");
                        $("#cate_depth1").attr("disabled", "disabled");
                    } else {
                        $("#cate_depth1").html(empty_option);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                }
            }).done(function() {
                var depth = 2;
                var parent_cate_cd = $("#cate_depth1").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "";
                        for (var i = 0; i < cnt; i++) {
                            option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                        } //for
                        if (cnt != 0) {
                            $("#cate_depth2").html(option);
                        } else {
                            $("#cate_depth2").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            });
            //first loading(cate) end

            //firset loading(search) start
            var depth = 1;
            var cnt;
            var empty_option = "<option>선택 항목 없음</option>";
            $.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'json',
                type: 'post',
                data: {
                    'mode': "SELECT_CATEGORY",
                    'depth': depth
                },
                success: function(response) {
                    cnt = response.length;
                    var option = "";
                    for (var i = 0; i < cnt; i++) {
                        option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                    } //for
                    if (cnt != 0) {
                        $("#source_depth1").html(option);
                        $("#source_depth1 > option[value='01']").attr("selected", "true");
                        $("#source_depth1").attr("disabled", "disabled");
                    } else {
                        $("#source_depth1").html(empty_option);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                }
            }).done(function() {
                var depth = 2;
                var parent_cate_cd = $("#source_depth1").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "<option>선택 항목 없음</option>";
                        for (var i = 0; i < cnt; i++) {
                            if(response[i]["CATE_CD"] != "0101"){
                                option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                            }
                        } //for
                        if (cnt != 0) {
                            $("#source_depth2").html(option);
                        } else {
                            $("#source_depth2").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            });
            //firset loading(search) end

            //change event binding start(cate)
            $("#cate_depth2").on("change", function() {
                var depth = 3;
                var parent_cate_cd = $("#cate_depth2").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "";
                        for (var i = 0; i < cnt; i++) {
                            option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                        } //for
                        if (cnt != 0) {
                            $("#cate_depth3").html(option);
                        } else {
                            $("#cate_depth3").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            }); //on

            $("#cate_depth3").on("change", function() {
                var depth = 4;
                var parent_cate_cd = $("#cate_depth3").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "";
                        for (var i = 0; i < cnt; i++) {
                            option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                        } //for
                        if (cnt != 0) {
                            $("#cate_depth4").html(option);
                        } else {
                            $("#cate_depth4").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            }); //on

            $("#cate_depth4").on("change", function() {
                var depth = 4;
                var parent_cate_cd = $("#cate_depth3").val();
            }); //on
            //change event binding end(cate)

            //change event binding start(search)
            $("#source_depth2").on("change", function() {
                var depth = 3;
                var parent_cate_cd = $("#source_depth2").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "<option>선택 항목 없음</option>";
                        for (var i = 0; i < cnt; i++) {
                            option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                        } //for
                        if (cnt != 0) {
                            $("#source_depth3").html(option);
                        } else {
                            $("#source_depth3").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            }); //on

            $("#source_depth3").on("change", function() {
                var depth = 4;
                var parent_cate_cd = $("#source_depth3").val();
                var cnt;
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_CATEGORY",
                        'depth': depth,
                        "parent_cate_cd": parent_cate_cd
                    },
                    success: function(response) {
                        cnt = response.length;
                        var option = "<option>선택 항목 없음</option>";
                        for (var i = 0; i < cnt; i++) {
                            option += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
                        } //for
                        if (cnt != 0) {
                            $("#source_depth4").html(option);
                        } else {
                            $("#source_depth4").html(empty_option);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            }); //on

            $("#source_depth4").on("change", function() {
                var depth = 4;
                var parent_cate_cd = $("#source_depth3").val();
            }); //on
            //change event binding end(search)

            //검색 버튼 클릭
            $("#btnSearch").on("click", function() {
                //마지막 카테고리의 value를 가져옴
                var last_cate_value = "";
                for(var i=1;i<5;i++){
                    if($("#source_depth"+i).val() != "")
                        last_cate_value = $("#source_depth"+i).val();
                }
                
                //검색 및 출력
                var empty_contents = "<div class='row'><div class='cell'>데이터가 없습니다.</div></div>";
                
                $.ajax({
                    url: '/manager/ajax_processing.php',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'mode': "SELECT_GOODS",
                        'cate': last_cate_value
                    },
                    success: function(response) {
                        cnt = response.length;
                        var contents = "";
                        for (var i = 0; i < cnt; i++) {
                            contents += "<div class='row table_body'>\
                                            <div class='cell cell-chk'><input type='checkbox' value='"+response[i]["GOODS_NO"]+"' /></div>\
                                            <div class='cell cell-con5'>"+response[i]["카테고리"]+"</div>\
                                            <div class='cell cell-con5'>"+response[i]["자재구분"]+"</div>\
                                            <div class='cell cell-con5'>"+response[i]["자재코드"]+"</div>\
                                            <div class='cell cell-con5'>"+response[i]["자재명"]+"</div>\
                                            <div class='cell cell-con5'>"+response[i]["이미지"]+"</div>\
                                        </div>\
                            ";
                        } //for
                        if (cnt != 0) {
                            $("#search > .wrapper2 > .table").html(contents);
                        } else {
                            $("#search > .wrapper2 > .table").html(empty_contents);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                });
            });
            
            $("#hidearea").on("click", function() {
                if($(".leftarea").css("display") == "none"){
                    //show
                    $(".leftarea").css("display","inline-block");
                    $("#hidearea").removeClass('fa-eye');
                    $("#hidearea").addClass('fa-eye-slash');
                    $("#hidearea").css("margin-left","180px");
                    $(".contentArea").css("width","calc(100% - 205px)");
                } else {
                    //hide
                    $(".leftarea").css("display","none");
                    $("#hidearea").removeClass('fa-eye-slash');
                    $("#hidearea").addClass('fa-eye');
                    $("#hidearea").css("margin-left","0px");
                    $(".contentArea").css("width","99%");
                }
            });
        });
    </script>
    <style>
        html,
        body,
        .container {
            width: 100%;
            height: 100%;
            padding: 0px;
            margin: 0px;
            overflow: hidden;
        }

        .container {
            vertical-align: top;
        }

        .leftarea {
            display: inline-block;
            width: 160px;
            height: 100%;
            border-right: 1px solid #acacac;
            vertical-align: top;
            padding: 0 0 0 20px;
        }

        .contentArea {
            display: inline-block;
            width: calc(100% - 205px);
            height: calc(100% - 20px);
            padding-left: 20px;
            padding-top: 20px;
        }

        .logo {
            padding: 5px 10px 4px 0px;
        }

        .leftmenu {
            /* padding-top: 10px; */
        }

        .btn_logout {
            background-color: green;
            color: white;
            width: 47px;
            height: 15px;
            text-align: center;
        }

        .oneline25 {
            display: inline-block;
            width: calc((100% - 15px) / 4);
        }

        .oneline {
            display: inline;
        }

        #filter {
            display: block;
            width: 100%;
            height: 20%;
            /* background-color: rgba(0, 255, 0, 0.5); */
            margin: 0px;
            padding: 0px;
        }

        .subContentArea {
            height: calc(80% - 20px);
        }

        #search {
            display: inline-block;
            width: 30%;
            height: calc(100% - 20px);
            /* background-color: rgba(0, 0, 255, 0.5); */
            margin-top: 10px;
            vertical-align: top;
        }

        #contents {
            display: inline-block;
            width: calc(70% - 5px);
            height: calc(100% - 20px);
            /* background-color: rgba(255, 0, 0, 0.5); */
            margin-top: 10px;
        }

        .wrapper1 {
            overflow-y: auto;
            width: 100%;
            height: 100%;
        }

        .wrapper2 {
            overflow-y: auto;
            width: 100%;
            height: calc(100% - 80px);
        }

        .table_head {
            position: -webkit-sticky;
            position: sticky;
            width: 100%;
            height: 30px;
            top: 0;
            border-top: 1px solid #86a4b3;
            border-bottom: 1px solid #DDD;
            background-color: #ebf3f6;
            color: #86a4b2;
            text-align: center;
        }

        .table_body {}
        
        #cate_depth1,
        #cate_depth2,
        #cate_depth3,
        #cate_depth4 {
            width: 100%;
            font-size: 15px;
        }

        #source_depth1{
            width:70px;
        }
        #source_depth2,#source_depth3,#source_depth4{
            width:100px;
        }

        .title {
            color: #86a4b2;
            background-color: #ebf3f6;
            text-align: center;
            padding-top: 7px;
            padding-bottom: 7px;
            border-top: 1px solid #86a4b3;
        }

        .table {
            display: table;
            width: 100%;
        }

        .row {
            display: table-row;
            height: 150px;
        }

        .cell {
            text-align: center;
            display: table-cell;
            border-bottom: 1px solid #DDD;
            vertical-align: middle;
        }

        .cell-chk {
            width: 5%;
        }

        .cell-con5 {
            width: calc(calc(100% - 5%) / 5);
        }

        .th-chk {
            width: 5%;
            margin-top: 7px;
        }

        .th-con5 {
            width: calc(calc(100% - 5% - 18px) / 5);
            margin-top: 7px;
        }

        .inline_block {
            display: inline-block;
        }

        .controllPannel1{
            display: inline-block;
            text-align:right;
            width:calc(100% - 45px);
        }

        .controllPannel2{
            display: inline-block;
            text-align:right;
            width:calc(100% - 90px);
        }

        #add_goods{
            width:100px;
        }

        #del_goods{
            width:100px;
        }

        .searchFilter{
            height:70px;
        }
        #btnSearch{
            width:100px;
        }
        ##search_condition{
            width:70px;
        }
        #searchField{
            width:200px;
        }
        #hidearea{
            position: absolute;
            top: 0px;
            left: 0px;
            width: 30px;
            height: 15px;
            font-size: 15px;
            color: green;
            border: 1px solid #ddd;
            text-align: center;
            margin-left: 180px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="leftarea">
            <div class="logo">
                <a href="/manager/">
                    <img src="/manager/images/admin/giftnet_logo.png" style="width:100px;" alt="GIFTNET">
                </a>
            </div>
            <!-- /logo -->
            <div class="leftmenu">
                <? require "../../_common/left_area.php"; ?>
            </div>
            <!-- /leftarea -->
            <div class="user_info">
                <div><strong><?= $s_adm_nm ?></strong>님 환영합니다.</div>
                <br>
                <div class="btn_logout">
                    <a style="color:white;" href="/manager/login/logout.php">
                        <strong>Logout</strong>
                    </a>
                </div>
            </div>
        </div>

        <div class="contentArea">
            <div id="filter">
                <img src="../images/admin/icon_tit_01.gif" />
                <h2 class="oneline">오주문 예방</h2>
                <span>&nbsp;&nbsp;&nbsp;※. 업체 카테고리별로 사용 가능한 포장을 제한하여 오주문을 예방하는 기능입니다.</span>
                <div id ="hidearea" class="fa fa-eye-slash" aria-hidden="true"></div>
                <br>
                <div class="sp10"></div>
                <div class="oneline25">
                    <div class="title">1차 카테고리</div>
                    <div class="">
                        <select id="cate_depth1" size="6">
                            <option>선택 항목 없음</option>
                        </select>
                    </div>
                </div>
                <div class="oneline25">
                    <div class="title">2차 카테고리</div>
                    <div class="">
                        <select id="cate_depth2" size="6">
                            <option>선택 항목 없음</option>
                        </select>
                    </div>
                </div>
                <div class="oneline25">
                    <div class="title">3차 카테고리</div>
                    <div class="">
                        <select id="cate_depth3" size="6">
                            <option>선택 항목 없음</option>
                        </select>
                    </div>
                </div>
                <div class="oneline25">
                    <div class="title">4차 카테고리</div>
                    <div class="">
                        <select id="cate_depth4" size="6">
                            <option>선택 항목 없음</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--/filter-->

            <div class="subContentArea">
                <div id="search">
                    <h2 style="color:#656565" class="inline_block"> 검색</h2>
                    <div class="controllPannel1">
                        <input type="button" id="add_goods" value="추가" />
                    </div>
                    <div class="sp10"></div>
                    <div class="searchFilter">
                        <div class="" style="border-top: 1px solid #86a4b3;border-bottom: 1px solid #DDD;">
            <!-- text-align: center; -->
                            <div class="inline_block" style="color: #86a4b2;background-color: #ebf3f6;padding-top: 7px;padding-bottom: 7px;">
                                카테고리
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <select id="source_depth1">
                                    <option>선택 항목 없음</option>
                                </select>
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <select id="source_depth2">
                                    <option>선택 항목 없음</option>
                                </select>
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <select id="source_depth3">
                                    <option>선택 항목 없음</option>
                                </select>
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <select id="source_depth4">
                                    <option>선택 항목 없음</option>
                                </select>
                            </div>
                        </div>
                        <div class="" style="border-bottom: 1px solid #DDD;">
                            <div class="inline_block" style="color: #86a4b2;background-color: #ebf3f6;padding-top: 7px;padding-bottom: 7px;">
                                검색조건
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <select id="search_condition">
                                    <option>상품 코드</option>
                                    <option>상품명</option>
                                </select>
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <input type="text" id="searchField" />
                            </div>
                            <div class="inline_block" style="padding-top: 7px;padding-bottom: 7px;">
                                <input type="button" id="btnSearch" value="검색" />
                            </div>
                        </div>
                    </div>
                    <div class="sp10"></div>
                    <div class="wrapper2">
                        <div class="table_head">
                            <div class="inline_block th-chk"><input type="checkbox" /></div>
                            <div class="inline_block th-con5">카테고리</div>
                            <div class="inline_block th-con5">자재구분</div>
                            <div class="inline_block th-con5">자재코드</div>
                            <div class="inline_block th-con5">자재명</div>
                            <div class="inline_block th-con5">이미지</div>
                        </div>
                        <div class="table">
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                        </div>
                    </div>
                    <!-- wrapper2 -->
                </div>
                <!--/search-->

                <div id="contents">
                    <h2 style="color:#656565" class="inline_block"> 허용 포장</h2>
                    <div class="controllPannel2">
                        <input type="button" id="del_goods" value="삭제" />
                    </div>
                    <div class="sp10"></div>
                    <div class="wrapper1">
                        <div class="table_head">
                            <div class="inline_block th-chk"><input type="checkbox" /></div>
                            <div class="inline_block th-con5">카테고리</div>
                            <div class="inline_block th-con5">자재코드</div>
                            <div class="inline_block th-con5">자재명</div>
                            <div class="inline_block th-con5">메모</div>
                            <div class="inline_block th-con5">이미지</div>
                        </div>
                        <div class="table">
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                            <div class="row table_body">
                                <div class="cell cell-chk"><input type="checkbox" /></div>
                                <div class="cell cell-con5">2</div>
                                <div class="cell cell-con5">3</div>
                                <div class="cell cell-con5">4</div>
                                <div class="cell cell-con5">5</div>
                                <div class="cell cell-con5">6</div>
                            </div>
                        </div>
                        <!-- table -->
                    </div>
                    <!-- wrapper1 -->
                </div>
                <!--/contents-->
            </div>
            <!-- subContentArea -->
        </div>
        <!-- contentArea -->
    </div>
    <!-- container -->
</body>

</html>
<? mysql_close($conn); ?>