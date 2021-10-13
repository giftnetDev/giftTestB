<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/member/member.php";

	/*
		echo "아이디: ".$iid."<br/>";
		echo "비번: ".$pwd."<br/>";
		echo "이름: ".$name."<br/>";
		echo "이메일주소: ".$email1."@".$email2."<br/>";
		echo "전화: ".$phone."<br/>";
		echo "휴대전화: ".$hphone."<br/>";
		echo "회사명: ".$etc."<br/>";
		echo "모드1: ".$mode."<br/>";
	*/

	if($mode == "SIGNIN") { 

		//내용에 이상이 없으면 GOTO level_3, 아니면 다시 level_2로 
		$mem_type = "S"; //Supplier 납품업체
		$biz_num1 = trim($biz_num1);
		$biz_num2 = trim($biz_num2);
		$biz_num3 = trim($biz_num3);
		$mem_id = trim($iid);
		$mem_pw = trim($pwd); 
		$mem_nm = trim($name);
		$email = trim($email1)."@".trim($email2);
		$phone = trim($phone);
		$hphone = trim($hphone);
		$etc = trim($etc);
		$cp_no = trim($cp_no);
		
		//승인 필요
		$use_tf = 'N';
		$reg_adm = 0;

		$cnt = dupMember ($conn, $mem_id);

		$error_msg = "";

		if($error_msg == "" && ($biz_num1 == "" || $biz_num2 == "" || $biz_num3 == ""))
			$error_msg = "사업자 등록번호를 입력해 주십시요.";

		if($error_msg == "" && $mem_id == "")
			$error_msg = "아이디를 입력해주세요.";

		if($error_msg == "" && strlen($mem_id) < 6)
			$error_msg = "6자 이상의 아이디를 사용해 주십시요.";

		if($error_msg == "" && $cnt > 0) 
			$error_msg = "중복된 아이디가 있습니다.";
		
		if($error_msg == "" && ($pwd <> $pwd_check))
			$error_msg = "비밀번호와 비밀번호 확인이 다릅니다.";


		if($error_msg == "" && $pwd == "")
			$error_msg = "비밀번호를 입력해주세요.";

		if($error_msg == "" && ($email1 == "" || $email2 == ""))
			$error_msg = "이메일을 입력해주세요.";

		if($error_msg == "" && ($phone == "" || $hphone == ""))
			$error_msg = "연락 가능한 연락처를 입력해주세요.";

		if($error_msg == "" && $addr1 == "")
			$error_msg = "배송 받으실 배송지 주소를 입력해주세요.";

		//echo "에러메세지 : ".$error_msg."<br/>";

		if($error_msg == "") { 
			$result = insertMember($conn, $mem_type, $mem_id, $mem_pw, $mem_nm, $jumin1, $jumin2, $biz_num1, $biz_num2, $biz_num3, $birth_date, $calendar, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $job, $position, $cphone, $cfax, $czipcode, $caddr1, $caddr2, $join_how, $join_how_person, $join_how_etc, $etc, $foreigner_num, $use_tf, $reg_adm,$cp_no);
			
			//정상이면 level3로 가므로 표시되지 않음
			$error_msg = "입력과정중 시스템 오류가 발생하였습니다.";
		} else
			$result = false;

		//실패시 level_2로
		if($result) { 
			$mode = "level_3";
		
			//시스템 계정에 이메일 발송
			$from_email = getDcodeName($conn, "HOME_INFO", "MEM_REG_DISPLAY");
			$to_email = getDcodeName($conn, "HOME_INFO", "MEM_REG_EMAIL");
			$email_subject = getDcodeExtByCode($conn, "HOME_INFO", "MEM_REG_SUBJECT");
			$email_body = getDcodeExtByCode($conn, "HOME_INFO", "MEM_REG_BODY");

			$email_subject = str_replace("[MEM_ID]", $mem_nm." 님", $email_subject);

			$email_body = str_replace("[BR]", "<br>", $email_body);
			

			include('_PHPMailer/class.phpmailer.php');
			ini_set('display_errors', 'Off');

			$result_msg = mailer("기프트넷-홈페이지관리", $from_email, $to_email, $to_email, $email_subject, $email_body, '', '');

		} else { 
			$mode = "level_2";
			
		}


	}
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
<!-- 회원가입 -->
<div class="container members signin">
    <h5 class="title">회원가입</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mode" value="">
			<? if($mode == "") { ?>
            <ul class="nav nav-pills navbar-right">
                <li>사용자 유형</li>
                <li class="active">약관동의</li>
                <li>정보입력</li>
                <li>가입완료</li>
            </ul>
            <div class="form-group">
                
				<div style="width:100%; height:300px; padding:10px; overflow:hidden; overflow-y:scroll; overflow-x:scroll; border: 1px solid #babdc8;">
				<?
						$bb_no = 3;
						$bb_code = "HOMEPAGE";
						$arr_rs = selectBoard($conn, $bb_code, $bb_no);
						$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]);
						echo $rs_contents;
				?>
				</div>
				<div class="checkbox">
					<label>
					  <input type="checkbox" id="chk_area1"> 약관을 읽었으며 내용에 동의합니다.
					</label>
				 </div>
            </div>
			<div class="btns text-center" role="group">
                <button type="button" class="btn btn-default btn_level1">다음 단계로</button>
                <button type="reset" class="btn btn-default">취소</button>
            </div>

			<? } ?>
			<? if($mode == "level_2") { ?>
				<? if($error_msg <> "") { ?>
			<div class="alert alert-danger alert-dismissible" role="alert"><?=$error_msg?></div>
				<? } ?>
			<ul class="nav nav-pills navbar-right">
				<li>사용자 유형</li>
				<li>약관동의</li>
                <li class="active">정보입력</li>
                <li>가입완료</li>
            </ul>
            <div class="form-group">
				<label class="control-label col-sm-3" for="biz_num1">사업자 번호</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input class="form-control inline-3" id="biz_num1" name="biz_num1" type="text" value="<?=$biz_num1?>">
					<input class="form-control inline-3" id="biz_num2" name="biz_num2" type="text" value="<?=$biz_num2?>">
					<input class="form-control inline-3" id="biz_num3" name="biz_num3" type="text" value="<?=$biz_num3?>">
				</div>
            </div>			
            <div class="form-group">
                <label class="control-label col-sm-3" for="iid">아이디</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="id" class="form-control" id="iid" name="iid" placeholder="" value="<?=$iid?>">
                    <!--<button type="button" class="btn btn-default" id="id-dup">아이디 중복 확인</button>-->
					<span>띄어쓰기 없이 영/숫자 6~10자</span>
                </div>
				
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="pwd">비밀번호</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="">
					<span>6-15자의 영문 대소문자, 숫자 및 특수문자 조합</span>
                </div><br ><br >
                <label class="control-label col-sm-3" for="pwd">비밀번호 확인</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd_check" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="name">이름</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="name" class="form-control" id="name" name="name" placeholder="" value="<?=$name?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">이메일 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="email" name="email1" placeholder="" value="<?=$email1?>">  @ 
					<input type="text" class="form-control" name="email2" placeholder="" value="<?=$email2?>">
                    <div class="btn-group">
                         <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">자동 입력<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="email2">
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                      </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">전화번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="" value="<?=$phone?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">휴대전화</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="hphone" name="hphone" placeholder="" value="<?=$hphone?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">우편번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="" value="<?=$zipcode?>">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcode">주소검색</button>
                </div>
				<label class="control-label col-sm-3" for="addr1">업체 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%;" id="addr1" name="addr1" placeholder="" value="<?=$addr1?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="etc">기타 정보</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="etc" name="etc" placeholder="" value="<?=$etc?>">
					<span>소속업체 / 가입하시려는 목적등</span>
                </div>
			</div>
							
			<div class="form-group">
				<label class="control-label col-sm-3">기존 납품업체는 거래처 DB에서 업체를 선택해주세요.</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="button" id="search_btn" style="width:100px;margin-bottom:3px;" class="btn-sm btn btn-default" value="검색">
					<input type="hidden" id="cp_no" name="cp_no" value />
				</div>
			</div>

            <div class="btns text-center" role="group">
                <button type="submit" class="btn btn-default active btn_level2">회원가입</button>
                <button type="reset" class="btn btn-default">취소</button>
            </div>
			<? } ?>
			<? if($mode == "level_3") { ?>
				<ul class="nav nav-pills navbar-right">
					<li>사용자 유형</li>
					<li>약관동의</li>
					<li>정보입력</li>
					<li class="active">가입완료</li>
				</ul>
				<div class="form-group">
					
					<div style="width:100%; height:300px; padding:10px; text-align:center;">
						가입되셨습니다. 관리자 승인이 완료되면 메일이나 유선을 통해서 연락드리겠습니다. 
					</div>
				</div>
				<div class="btns text-center" role="group">
					<a href="login.php"><button type="button" class="btn btn-default btn_login active">로그인</button></a>
					<button type="reset" class="btn btn-default">홈으로</button>
				</div>
			<? } ?>
        </form>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
<script type="text/javascript">
	$(function(){
		$("#chk_area1").change(function(){
			if($(this).is(":checked"))
				$(".btn_level1").addClass("active");
			else
				$(".btn_level1").removeClass("active");
		});

		//약관동의
		$(".btn_level1").click(function(e){
			e.preventDefault();

			if($(this).hasClass("active")) {
				var frm = document.frm;
				frm.mode.value="level_2";
				frm.method="post";
				frm.action="<?=$_SERVER[PHP_SELF]?>";
				frm.submit();

			} else { 
				alert('약관에 동의해주셔야 회원가입이 진행됩니다.');
				$("#chk_area1").focus();
			}
		});

		//정보입력
		$(".btn_level2").click(function(e){
			e.preventDefault();

			if($(this).hasClass("active")) {
				var frm = document.frm;
				frm.mode.value="SIGNIN";
				frm.method="post";
				frm.action="<?=$_SERVER[PHP_SELF]?>";
				frm.submit();

			} 
		});

		$(".sel_email_ext").click(function(e){
			e.preventDefault();
			var target_elem = $(this).closest(".dropdown-menu").data("target");
			var sel_email_ext = $(this).html();
			$("[name=" + target_elem + "]").val(sel_email_ext);
		});
		
		$("#search_btn").click(function(){
			NewWindow("pop_search_company.php", 'search_company','450','600','YES');
		});
		
	});
</script>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

	$(function(){
		$(".trigger-find_addr").click(sample6_execDaumPostcode);
	});

    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = ''; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    fullAddr = data.roadAddress;

                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    fullAddr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
                if(data.userSelectedType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
				 // 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById("zipcode").value = data.zonecode;
				//document.getElementById("re_zip").value = data.postcode2;
				document.getElementById("addr1").value = fullAddr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById("addr1").focus();


            }
        }).open();
    }

</script>  
</body>
</html>

