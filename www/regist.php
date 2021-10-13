<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/member/member.php";

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
<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<script>
	$(document).ready(function(){
		document.getElementById("divlevel2").style.display ="none";
        document.getElementById("divlevel3").style.display ="none";

		document.getElementById("idyn").style.display ="none";
        document.getElementById("ceryn").style.display ="none";
	});

$(function() {
    $("#telphone1").keyup (function(){
        if(this.value.length == 3) 
        {
            $('#telphone2').focus();
        }        
    })

    $("#telphone2").keyup (function(){
        if(this.value.length == 4) 
        {
            $('#telphone3').focus();
        }        
    })
	
	$("#telphone3").keyup (function(){
        if(this.value.length == 4) 
        {
            $('#phone1').focus();
        }        
    })

	$("#phone1").keyup (function(){
        if(this.value.length == 3) 
        {
            $('#phone2').focus();
        }        
    })

    $("#phone2").keyup (function(){
        if(this.value.length == 4) 
        {
            $('#phone3').focus();
        }        
    })

	$("#phone3").keyup (function(){
        if(this.value.length == 4) 
        {
            $('#hpConfrim').focus();
        }        
    })

    $("#biz_num1").keyup (function(){
        if(this.value.length == 3) 
        {
            $('#biz_num2').focus();
        }        
    })
    
    $("#biz_num2").keyup (function(){
        if(this.value.length == 2) 
        {
            $('#biz_num3').focus();
        }        
    })
    
    $("#biz_num3").keyup (function(){
        if(this.value.length == 5) 
        {
            $('#search_btn').focus();
        }        
    })
});	

</script>
<script type="text/javascript">
//maxlength 체크
function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
    {
        object.value = object.value.slice(0, object.maxLength);
    }   
}

function CheckId()
{
	var frm = document.frm;
	var idReg = /^[A-za-z]+[A-za-z0-9]{5,12}$/g;

	if( !idReg.test( $("input[name=iid]").val() ) ) 
	{
        alert("아이디는 영문자로 시작하는 영/숫자 6~12자 입니다.");
		frm.iid.focus();
        return;
    }

    return true;
}

function jsIdck()
{
	var frm = document.frm;
	document.getElementById("idyn").style.display ="none";
	if (!CheckId())	return;

	$.ajax({
			url: "json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CUSTOMER_CK"
					, custID: frm.iid.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
							frm.iidck.value = "Y";
							frm.iidtwo.value = frm.iid.value;
                            document.getElementById("idyn").style.display ="block";
							frm.pwd.focus();
							return;
                        }
                        else
                        {
                            alert("중복되는 ID가 있습니다.\n\n다시 확인 바랍니다.");
							//frm.iid.value = "";
							frm.iid.focus();
                            return ;
                        }
                    });
				}	,
				fail : function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});

}

function jsHpck()
{
	var frm = document.frm;

    if(frm.name.value == "") 
	{
        alert("이름을 입력 해 주세요.");
        frm.name.focus();
        return false;
    }

    NewWindow("pop_hp_confirm.php", 'hp_certificationNm','420','300','YES');
}

function CheckPassword(uid, upw)
{
    //if(!/^[a-zA-Z0-9]{6,12}$/.test(upw))    //숫자+영문자
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{6,12}$/.test(upw)) //숫자+영문자+특수문자           
    {
        alert("비밀번호는 숫자, 영문자, 특수문자 조합으로\n\n6~12자리를 사용해야 합니다.");
        frm.pwd.focus();
        return false;
    }
    
    var chk_num = upw.search(/[0-9]/g);
    var chk_eng = upw.search(/[a-z]/ig);
    
    if(chk_num<0 || chk_eng<0)
    {
        alert("비밀번호는 숫자와 영문자를 혼용하여야 합니다.");
        frm.pwd.focus();
        return false;
    }
    
    if(/(\w)\1\1\1/.test(upw))
    {
        alert("비밀번호에 같은 문자를 4번 이상 사용하실 수 없습니다.");
        frm.pwd.focus();
        return false;
    }
    
    if(upw.search(uid)>-1)
    {
        alert("ID가 포함된 비밀번호는 사용하실 수 없습니다.");
        frm.pwd.focus();
        return false;
    }

    return true;
}

function CheckEmail(email)
{                       
	var reg_email = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;
	//alert(email);
	if(!reg_email.test(email))
	{                             
		alert("이메일 형식이 올바르지 않습니다.")
		document.frm.emailbt.focus();
		return false;         
	}
	
	return true;
}     

function checkbizNo(bizID) 
{    
    //alert(bizID);
    // bizID는 숫자만 10자리로 해서 문자열로 넘긴다. 
    var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1); 
    var tmpBizID, i, chkSum=0, c2, remander; 
    bizID = bizID.replace(/-/gi,''); 

    for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i); 
    c2 = "0" + (checkID[8] * bizID.charAt(8)); 
    c2 = c2.substring(c2.length - 2, c2.length); 
    chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1)); 
    remander = (10 - (chkSum % 10)) % 10 ; 

    if (Math.floor(bizID.charAt(9)) != remander)
    {
        alert("사업자 번호를 확인 해 주세요.");
        return false; 
    } 

    return true;
    
}

function js_memeberIn()
{		
	var frm = document.frm;

    if(frm.iid.value == "") 
	{
        alert("아이디를 입력 해 주세요.");
        frm.iid.focus();
        return false;
    }
	
	if (!CheckPassword(frm.iid.value, frm.pwd.value))    return;

    if(frm.pwd.value != frm.pwd_check.value)
    {
        alert("비밀번호가 일치 하지 않습니다.\n\n다시 확인 바랍니다.");
        frm.pwd_check.focus();
        return false;
    }

	if(frm.name.value == "") 
	{
        alert("이름을 입력 해 주세요.");
        frm.name.focus();
        return false;
    }

	if(frm.email1.value == "" || frm.email2.value == "") 
	{
        alert("email을 정확히 입력해 주세요.");
		if(frm.email1.value == "")
		{	
			frm.email1.focus();
		}
		else
		{
			frm.email2.focus();
		}        
        return false;
    }

	if (!CheckEmail(frm.email1.value+"@"+frm.email2.value))	return;

	if (frm.telphone1.value == "") {
        alert("전화번호를 정확히 입력해 주세요.");
        frm.telphone1.focus();
        return false;
    }

    if (frm.telphone2.value == "") {
        alert("전화번호를 정확히 입력해 주세요.");
        frm.telphone2.focus();
        return false;
    }
    
    if (frm.telphone3.value == "") {
        alert("전화번호를 정확히 입력해 주세요.");
        frm.telphone3.focus();
        return false;
    }

	if (frm.phone1.value == "" || frm.phone1.value.length != 3) 
    {
        alert("휴대폰 번호를 정확히 입력해 주세요.");
        frm.phone1.focus();
        return false;
    }

    if (frm.phone2.value == "" || frm.phone2.value.length != 4) 
    {
        alert("휴대폰 번호를 정확히 입력해 주세요.");
        frm.phone2.focus();
        return false;
    }
    
    if (frm.phone3.value == "" || frm.phone3.value.length != 4) 
    {
        alert("휴대폰 번호를 정확히 입력해 주세요.");
        frm.phone3.focus();
        return false;
    }

	if (frm.zipcode.value == "" || frm.addr.value == "") {
        alert("우편번호 및 주소를 정확히 입력해 주세요.");
        frm.zipcodebt.focus();
        return false;
    }

    if (frm.biz_num1.value != "" || frm.biz_num2.value != "" || frm.biz_num3.value != "") 
    {
        if (!checkbizNo(frm.biz_num1.value+frm.biz_num2.value+frm.biz_num3.value))    return;

        if (frm.companyname.value == "") 
        {
            alert("회사명을 입력 해 주세요.");
            frm.companyname.focus();
            return false;
        }
        
        if (frm.ceoname.value == "") 
        {
            alert("대표자명을 입력 해 주세요.");
            frm.ceoname.focus();
            return false;
        }
    }

    if(frm.iidck.value != "Y")
    {
        alert("ID 중복체크를 해주세요.");
        frm.id_dup.focus();
        return false;
    }

    if(frm.iid.value != frm.iidtwo.value)
    {
        alert("ID 중복체크 한 값이 변경이 되었습니다.\n\nID 중복체크를 다시 해주세요.");
        frm.iidck.value = "";
        document.getElementById("idyn").style.display ="none";
        frm.id_dup.focus();
        return false;
    }

    if(frm.hpcerCk.value != "Y")
    {
        alert("휴대폰인증을 해주세요.");
        frm.hpConfrim.focus();
        return false;
    }
    
    $.ajax({
			url: "json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CUSTOMER_IN"
                    , custTp: "C"
					, custID: frm.iid.value
                    , custPwd: frm.pwd.value
                    , custNm: frm.name.value
                    , custEmail: frm.email1.value+"@"+frm.email2.value
                    , custTelPhone: frm.telphone1.value+"-"+frm.telphone2.value+"-"+frm.telphone3.value
                    , custPhone: frm.phone1.value+"-"+frm.phone2.value+"-"+frm.phone3.value
                    , custZipcode: frm.zipcode.value
                    , custAddr: frm.addr.value
                    , bizNo1: frm.biz_num1.value
                    , bizNo2: frm.biz_num2.value
                    , bizNo3: frm.biz_num3.value
                    , companyNm: frm.companyname.value
                    , ceoNm: frm.ceoname.value
                    , cpNo: frm.cp_no.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            alert("회원가입이 완료 되었습니다.");
                            document.getElementById("divlevel1").style.display ="none";
                            document.getElementById("divlevel2").style.display ="none";
                            document.getElementById("divlevel3").style.display ="block";
                        }
                        else
                        {
                            alert("error");
                            return;
                        }
                    });
				}	,
				fail : function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
}


function js_popClose()
{
    document.getElementById("companyname").readOnly = true;
    document.getElementById("biz_num1").readOnly = true;
    document.getElementById("biz_num2").readOnly = true;
    document.getElementById("biz_num3").readOnly = true;
    document.getElementById("ceoname").readOnly = true;

    document.getElementById('search_btn').disabled = true;
}

function fn_hp_popEnd()
{
    var frm = document.frm;

    frm.hpcerCk.value = "Y"

    document.getElementById("ceryn").style.display ="block";
    document.getElementById("phone1").readOnly = true;
    document.getElementById("phone2").readOnly = true;
    document.getElementById("phone3").readOnly = true;    
    document.getElementById("hpConfrim").disabled = true;        
}

</script>

<!-- 회원가입 -->
<div class="container members signin">
    <h5 class="title">회원가입</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
		<input type="hidden" name="iidck">
		<input type="hidden" name="iidtwo">
<!-------------------------------------------------------------약관동의 start----------------------------------------------------------------------->
		<div id="divlevel1">      
            <ul class="nav nav-pills navbar-right">
                <li class="active">약관동의</li>
                <li>정보입력</li>
                <li>가입완료</li>
            </ul>
            <div class="form-group">
			<label>▶ 이용 약관</label>
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
					  <input type="checkbox" id="chk_area0"> 약관을 읽었으며 내용에 동의합니다.
					</label>
				 </div>
				<br>
				<label>▶ 개인정보 및 수집 이용</label>
				<div style="width:100%; height:300px; padding:10px; overflow:hidden; overflow-y:scroll; overflow-x:scroll; border: 1px solid #babdc8;">
				<?
						$bb_no = 10;
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
                <a href="login.php"><button type="button" class="btn btn-default">취소</button></a>
            </div>
		</div>
<!-------------------------------------------------------------약관동의 END----------------------------------------------------------------------->
			
<!-------------------------------------------------------------정보입력 START----------------------------------------------------------------------->			
		<div class="form-group" id="divlevel2" >      
			<ul class="nav nav-pills navbar-right">
				<li>약관동의</li>
                <li class="active">정보입력</li>
                <li>가입완료</li>
            </ul>
            <div class="form-group">
                <label class="control-label col-sm-3" for="iid">아이디</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9" >
                    <input type="text" class="form-control" id="iid" name="iid" max="9999" maxlength="12" oninput="maxLengthCheck(this)" >
                    &nbsp;<button type="button" class="btn btn-default" id="id_dup" onclick="jsIdck();">ID중복체크</button>
					<span id="idyn" style="color: blue;">사용가능한 ID입니다.</span>
                </div>			
				
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="pwd">비밀번호</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd" >
					<span>6-12자의 숫자+영문자+특수문자 조합</span>
                </div><br ><br >
                <label class="control-label col-sm-3" for="rpwd">비밀번호 확인</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd_check" name="pwd_check" >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="name">이름</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="name" name="name"  maxlength=10>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">이메일 주소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="email1" name="email1" >  @ 
					<input type="text" class="form-control" id="email2" name="email2" >
                    <div class="btn-group">
                         <button type="button" name = "emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">자동 입력<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="email2">
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                      </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">전화번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="number" class="form-control" name="telphone1" id="telphone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone2" id="telphone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone3" id="telphone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">휴대전화</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                <input type="hidden" id="hpcerCk" name="hpcerCk">
				<input type="number" class="form-control" name="phone1" id="phone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone2" id="phone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone3" id="phone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                &nbsp;<button type="button" class="btn btn-default" id="hpConfrim" onclick="jsHpck();">휴대폰인증</button>
                <button type="button" id="resetHp_btn" class="btn btn-default">초기화</button>
                <span id="ceryn" style="color: blue;">휴대폰인증이 완료 되셨습니다.</span>
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">우편번호</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="number" class="form-control" id="zipcode" name="zipcode" style="width:63px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcodebt" name="zipcodebt">주소검색</button>
                </div>
				<label class="control-label col-sm-3">주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%; display:inline;" id="addr" name="addr">
                </div>
            </div>					
            <br><div style="text-align: center;"><span style="color: green;">▷기업의 경우 기업정보를 입력해 주세요. 세금계산서시 편리하게 이용하실 수 있습니다.◁</span></div>
            <div class="form-group">
            <label class="control-label col-sm-3" for="biz_num1">사업자 번호</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input class="form-control inline-3" id="biz_num1" name="biz_num1" type="number" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input class="form-control inline-3" id="biz_num2" name="biz_num2" type="number" style="width:60px;display:inline;" max="9999" maxlength="2" oninput="maxLengthCheck(this)"> -
					<input class="form-control inline-3" id="biz_num3" name="biz_num3" type="number" style="width:66px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
					&nbsp;&nbsp;<button type="button" id="search_btn" class="btn btn-default">검색</button>
                    <button type="button" id="reset_btn" class="btn btn-default">초기화</button>
					<span>기존 업체일 경우 검색 가능합니다.</span>
					<input type="hidden" id="cp_no" name="cp_no" />
				</div><br ><br >
                <label class="control-label col-sm-3" for="companyname">회사명</label>
					<div class="col-sm-10 col-lg-offset-0 col-lg-9">
						<input type="text" class="form-control" id="companyname" name="companyname"  maxlength=10>
				    </div><br ><br >
                <label class="control-label col-sm-3" for="ceoname">대표자명</label>
					<div class="col-sm-10 col-lg-offset-0 col-lg-9">
						<input type="text" class="form-control" id="ceoname" name="ceoname"  maxlength=10>
					</div>
            </div>			

            <div class="btns text-center" role="group">
                <button type="button" class="btn btn-default active" onclick="js_memeberIn()">회원가입</button>
				<a href="login.php"><button type="button" class="btn btn-default">취소</button></a>
            </div>
		</div>
<!-------------------------------------------------------------정보입력 END----------------------------------------------------------------------->

<!-------------------------------------------------------------가입완료 START----------------------------------------------------------------------->
		<div class="form-group" id="divlevel3" >      
				<ul class="nav nav-pills navbar-right">
					<li>약관동의</li>
					<li>정보입력</li>
					<li class="active">가입완료</li>
				</ul>
				<div class="form-group">
					
					<div style="width:100%; height:300px; padding:10px; text-align:center;">
						기프트넷 회원이 되신 것을 진심으로 환영 합니다!!
					</div>
				</div>
				<div class="btns text-center" role="group">
					<a href="login.php"><button type="button" class="btn btn-default btn_login active">로그인</button></a>
					<a href="index.php"><button type="button" class="btn btn-default active">홈으로</button></a>
				</div>
		</div>
<!-------------------------------------------------------------가입완료 END----------------------------------------------------------------------->
        </form>
    </div>
</div>
<!-- // 회원가입 -->

<?
	require "_common/v2_footer.php";
?>
<script type="text/javascript">
	$(function(){
		$("#chk_area0 , #chk_area1").click(function(){
			if($("input:checkbox[id='chk_area0']").is(":checked") && $("input:checkbox[id='chk_area1']").is(":checked"))
				$(".btn_level1").addClass("active");
			else
				$(".btn_level1").removeClass("active");
		});

		//약관동의
		$(".btn_level1").click(function(e){
			e.preventDefault();

			if($(this).hasClass("active")) {

				document.getElementById("divlevel1").style.display ="none";
				document.getElementById("divlevel3").style.display ="none";

				document.getElementById("divlevel2").style.display ="block";

			} else { 
				alert('약관에 동의해주셔야 회원가입이 진행됩니다.');
				$("#chk_area1").focus();
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

        $("#reset_btn").click(function()
        {
            $("#biz_num1").removeAttr("readonly");            
            $("#biz_num2").removeAttr("readonly");
            $("#biz_num3").removeAttr("readonly");
            $("#companyname").removeAttr("readonly");
            $("#ceoname").removeAttr("readonly");

            $('#biz_num1').val('');
            $('#biz_num2').val('');
            $('#biz_num3').val('');
            $('#companyname').val('');
            $('#ceoname').val('');
            $('#cp_no').val('');

            $('#search_btn').attr('disabled', false);

            $('#biz_num1').focus();
		});

        $("#resetHp_btn").click(function()
        {
            $('#phone1').attr('readOnly', false);
            $('#phone2').attr('readOnly', false);
            $('#phone3').attr('readOnly', false);

            $('#hpConfrim').attr('disabled', false);

            $("#ceryn").css("display","none");

            $('#hpcerCk').val('');

            $('#phone1').val('');
            $('#phone2').val('');
            $('#phone3').val('');

            $('#phone1').focus();
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
				document.getElementById("addr").value = fullAddr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById("addr").focus();

            }
        }).open();
    }

</script>  
</body>
</html>

