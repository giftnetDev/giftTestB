<?
    require "_common/home_pre_setting.php";

?>   
<?
    //--------------------Require------------------------
    $goods_code=$_POST['goodsCode'];
    $goods_no=$_POST['goodsNo'];

    // echo "GOODS_CODE : ".$goods_code."<br>";
    // echo "GOODS_NO : ".$goods_no."<br>";

    //---------------------------------------------------
    if($mode=="S"){

       // $conn=db_connection("w");

        $txtName=trim(setStringToDB($txtName));
        $txtPhone=trim(setStringToDB($txtPhone));
        $txtEmail=trim(setStringToDB($txtEmail));
        $txtTitle=trim(setStringToDB($txtTitle));
        $txtContent=trim(setStringToDB($txtContent));


        $query= "INSERT INTO TBL_PURCHASE_CONSULTATION (GOODS_NO, CON_NAME, CON_PHONE, CON_EMAIL, CON_TITLE, CON_CONTENTS) 
                VALUE ( $goods_no, '$txtName', '$txtPhone', '$txtEmail', '$txtTitle', '$txtContent') ; ";

        $result=mysql_query($query, $conn);
        echo mysql_error();
        if(!$result){
            echo "<script>alert('insert errror');</script>";
        }

        ?>
        <script>
            document.location="index2.php";
        </script>
        <?
        exit;
    }
    



?>
<!DOCTYPE HTML>
<html lang="ko">
    <head>
        <meta charset="euc-kr">
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width">
        <meta name="description" content="기프트넷" />
        <title>기프트넷</title>
        <link rel="icon" type="image/x-icon" herf="/" />
        <script type="text/javascript" src="newDesign/js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="newDesign/js/jquery_ui.js"></script>
        <script type="text/javascript" src="newDesign/js/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="newDesign/js/slick.js"></script>
        <script type="text/javascript" src="newDesign/js/common_ui.js"></script>
        <link type="text/css" rel="stylesheet" href="newDesign/css/reset.css" />

        <script>
            $(document).ready(function(){
                $('#cTitle').keyup(function(e){
                    var content = $(this).val();
                    $('#cTitleTxtCnt').html(content.length);
                    if(content.length>50){
                        alert("최대 50자까지 가능합니다!");
                        $(this).val(content.substr(0,50));
                        $('#cTitleTxtCnt').html("최대 50");
                    }
                });


            });

        </script>
        <script type="text/javascript">
            function js_save(){
                var frm=document.frm;

                
                frm.mode.value="S";
                frm.action="<?=$_SERVER[PHP_SELF]?>";

                frm.submit();

            }
        </script>
    </head>
    <body id="">
        <form name='frm' method='POST'>
            <input type='hidden' name='mode' value="">
            <input type='hidden' name='goodsCode' value="<?=$goods_code?>">
            <input type='hidden' name='goodsNo' value="<?=$goods_no?>">
            <div id="wrap">
                <div class="header">
                    <div class="innerbox">

                        <h2>구매 상담</h2>
                        <button type="button" class="btn-prevpage" onclick="history.back(-1)" title="이전">이전</button>
                        <button type="button" class="btn-gnb-search" onclick="searchToggle()" title="검색">검색</button>

                    </div><!--class='innerbox'-->
                    <div class="searchbox">
                        <h2>상품 검색</h2>
                        <button type="button" class="btn-close" onclick="searchToggle()" title="검색 닫기">검색 닫기</button>
                        <fieldset>
                            <legend>검색어 입력</legend>
                            <p><span class="inpbox"><input type="text" class="txt" placeholder="검색어를 입력해주세요." /></span><button type="button">검색</button></p>
                        </fieldset>
                        <dl>
                            <dt>추천 검색어</dt>
                            <dd>
                                <span><a href="#"></a></span>
                            </dd>
                        </dl>

                    </div><!--class='searchbox'-->
            
            
                </div><!--class='header'-->
                <div class="container">

                    <div class="contentsarea">

                        <div class="counselbox">

                            <div class="goods-number"><span>상품번호</span><strong><?=$goods_code?></strong></div>
                            <fieldset>
                                <legend>구매상담 정보 입력</legend>
                                <ul>
                                    <li>
                                        <label>이름</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtName" title="이름 입력" /></p></div>
                                    </li>
                                    <li>
                                        <label>전화번호</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtPhone" title="전화번호 입력" /></p></div>
                                    </li>
                                    <li>
                                        <label>이메일</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtEmail" title="이메일 입력" /></p></div>
                                    </li>
                                    <li>
                                        <label>제목</label><span class="txtcnt"><strong id="cTitleTxtCnt">0</strong> / 50자</span>
                                        <div><p class="inpbox"><input type="text" class="txt" id="cTitle" name="txtTitle" placeholder="제목을 입력해주세요." title="제목 입력" /></p></div>
                                    </li>
                                    <li>
                                        <label>내용</label><span class="txtcnt"><strong id="cContentTxtCnt">0</strong> / 2500자</span>
                                        <div><p class="txtbox"><textarea cols="" rows="" id="cContent" name="txtContent" placeholder="내용을 입력해주세요." title="내용 입력"></textarea></p></div>
                                    </li>
                                </ul>
                            </fieldset>
                            <div class="rulebox">
                                <ul>
                                    <li>수집 항목 : [필수] 이름, 전화번호</li>
                                    <li>수집·이용 목적 : 상품 구매 상담</li>
                                    <li>업무 위탁 제공처 : ㈜기프트넷</li>
                                    <li>보유·이용기간 : 본인이 동의를 철회할 때까지 유효 (수정되는 정보 포함)<br />(단, 상법 등 타 법령에 따른 정보는 해당 법령에서 정한 기간 동안 보존)</li>
                                    <li>동의 철회 방법 : 대표전화(031-527-6812) 또는개인정보 보호 관리자 및 담당자에게 서면, 전화 또는이메일로 연락</li>
                                </ul>
                                <p>※ 귀하께서는 본 동의를 거절하실 수 있으나, 미동의 시 해당 서비스 신청 및 이용에 제약이 있을 수 있습니다.</p>
                                <p>※ 개인정보 처리에 대한 상세한 사항은 화면 하단의 개인정보 처리 방침을 참조하십시오.</p>
                            </div>
                            <p class="agreechk"><input type="checkbox" id="agreeOk" /><label for="agreeOk">개인정보 수집 및 이용에 동의합니다.</label></p>
                            <p class="btncenter"><button type="button" id="" class="btn-green btn-large" onclick="js_save()">확인</button></p>
                        </div><!-- class='counselbox' -->


                    </div><!--class='contentsarea'-->



                </div><!--class='container'-->


            </div><!--class='wrap'-->
        </form>
    </body>




</html>

