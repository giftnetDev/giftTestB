<!DOCTYPE html>
<head>
<title>아이디/비밀번호 찾기</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

ul{
    padding:0px;
}

li{
    padding-left:40px;
    padding-top:10px;
}

li:hover{
    background-color:#f0f0f0;
    cursor: pointer;
}
hr{
    margin-bottom: 0px;
}
</style>

<script>
	$(document).ready(function(){
		document.getElementById("divcer").style.display ="none";
        document.getElementById("divcust").style.display ="none";
	});

</script>
<script type="text/javascript">

var timer = null;
var isRunning = false;

function fn_idpwFind()
{
    var frm = document.frm;

    if (frm.hpnm.value == "") {
        alert("이름을 입력 해 주세요.");
        frm.hpnm.focus();
        return false;
    }

    if (frm.phone1.value == "") {
        alert("핸드폰 번호를 정확히 입력해 주세요.");
        frm.phone1.focus();
        return false;
    }

    if (frm.phone2.value == "") {
        alert("핸드폰 번호를 정확히 입력해 주세요.");
        frm.phone2.focus();
        return false;
    }
    
    if (frm.phone3.value == "") {
        alert("핸드폰 번호를 정확히 입력해 주세요.");
        frm.phone3.focus();
        return false;
    }


    $.ajax({
			url: "json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CERTIFICATION"
					, custNm: frm.hpnm.value
                    , phone1: frm.phone1.value 
                    , phone2: frm.phone2.value 
                    , phone3: frm.phone3.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            frm.cer_no.value = item.CER_NO;
                            js_smsInsert();
                        }
                        else
                        {
                            alert("고객명과 휴대폰 번호가 일치하는 데이터가 없습니다.");
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

function js_smsInsert()
{
    var frm = document.frm;
    var rphone;
    var title;
    var msg;

    rphone  = frm.phone1.value + frm.phone2.value + frm.phone3.value;
    title   = "[기프트넷]\n";       //sms타입이 S일경우 제목이 표출되지 않는다. 
    msg     = "인증번호 ["+frm.cer_no.value+"]를 입력 해 주세요.";
    recver1 = "070";
    recver2 = "8896";
    recver3 = "0627";
    smstext = title+msg;        //(지금화면은 Sms이기에..)

    $.ajax({
			url: "json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_SMS_INSERT"
					, custNm: frm.hpnm.value
					, rphone: rphone
                    , sms_code : frm.sms_code.value
                    , phone1: frm.phone1.value 
                    , phone2: frm.phone2.value 
                    , phone3: frm.phone3.value
                    , title: title
                    , msg: smstext      //cafe24 인서트용
                    , message: smstext  //기프트넷 인서트용
                    , recver1: recver1
                    , recver2: recver2 
                    , recver3: recver3
                    , rdate: frm.rdate.value
                    , rtime: frm.rtime.value
					, testflag: frm.testflag.value
                    , destination: frm.destination.value
                    , repeatFlag: frm.repeatFlag.value
                    , repeatNum: frm.repeatNum.value
                    , repeatTime: frm.repeatTime.value                    
					, smsType: frm.smsType.value
                    , cer_no: frm.cer_no.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        alert("item.RESULT:"+item.RESULT);
                        if(item.RESULT == "Y")
                        {
                            alert("["+ item.RETURN_VLAUE +"]\n\n인증번호가 발송되었습니다. 인증번호를 확인해 주세요!");
                            js_cerCk();
                        }
                        else
                        {
                            alert("error==["+ item.RETURN_VLAUE +"]");
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


$(function() {
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
            $('#idpw').focus();
        }        
    })
});

//maxlength 체크
function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
    {
        object.value = object.value.slice(0, object.maxLength);
    }   
}

function js_cerCk()
{
    var frm = document.frm;

    frm.hpnmhd.value = frm.hpnm.value;
    frm.phone1hd.value = frm.phone1.value;
    frm.phone2hd.value = frm.phone2.value;
    frm.phone3hd.value = frm.phone3.value;

    document.getElementById("dividpw").style.display ="none"; 
    document.getElementById("divcer").style.display ="block"; 

    document.getElementById("hpnm").disabled = true;
    document.getElementById("phone1").disabled = true;
    document.getElementById("phone2").disabled = true;
    document.getElementById("phone3").disabled = true;

    document.frm.cerbox.focus();

    

    var display = $('.time');
    var leftSec = 180; //3분 세팅
    // 남은 시간
    // 이미 타이머가 작동중이면 중지
    if (isRunning)
    {
        clearInterval(timer);
        startTimer(leftSec, display);
    }else
    {
        startTimer(leftSec, display);
    }  
}

function startTimer(count, display) {
            
    var minutes, seconds;
    timer = setInterval(function () {
    minutes = parseInt(count / 60, 10);
    seconds = parseInt(count % 60, 10);

    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    document.getElementById("divtimer").innerHTML = minutes + "분" + seconds + "초";

    // 타이머 끝
    if (--count < 0) 
    {
        clearInterval(timer);
        alert("인증번호 확인 시간이 초과 되었습니다. 다시 확인 바랍니다.");
        document.getElementById("certifi").disabled = true;
        isRunning = false;
        
        location.reload();
    }

    }, 1000);
        isRunning = true;
}

function js_cerConfirm()
{
    var frm = document.frm;
    
    if (frm.cerbox.value == "" || frm.cerbox.value.length != 6) 
    {
        alert("인증번호 6자리를 입력해 주세요.");
        frm.cerbox.focus();
        return false;
    }

    $.ajax({
			url: "json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_IDPW_CERTIFICATION"
					, custNm: frm.hpnmhd.value
                    , phone1: frm.phone1hd.value 
                    , phone2: frm.phone2hd.value 
                    , phone3: frm.phone3hd.value
                    , sms_code : frm.sms_code.value
                    , cerNo : frm.cerbox.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            alert("인증번호가 확인 되었습니다.\n\n새로운 비밀번호를 설정 해 주세요.");
                            frm.memNo.value = item.MEM_NO;
                            frm.custId.value = item.MEM_ID;
                            js_pwUpSetting();
                        }
                        else
                        {
                            alert("인증번호가 일치 하지 않습니다.\n\n다시 확인 바랍니다.");
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

function js_pwUpSetting()
{
    var frm = document.frm;

    clearInterval(timer);

    document.getElementById("divhpnm").style.display ="none";
    document.getElementById("divphone").style.display ="none";
    document.getElementById("dividpw").style.display ="none";
    document.getElementById("divcer").style.display ="none";

    document.getElementById("divcust").style.display ="block";

    document.getElementById("spanIdpw").innerHTML = "비밀번호 설정 (숫자+영문자+특수문자 조합으로 6~12자리)";

    frm.custNpw.focus();
}

function js_pwUpConfirm()
{
    var frm = document.frm;

    if (!CheckPassword(frm.custId.value, frm.custNpw.value))    return;

    if(frm.custNpw.value != frm.custNpwcf.value)
    {
        alert("비밀번호가 일치 하지 않습니다.\n\n다시 확인 바랍니다.");
        frm.custNpwcf.focus();
        return false;
    }

/*
    alert(frm.memNo.value);
    alert(frm.custId.value);
    alert(frm.custNpw.value);
*/
    $.ajax({
			url: "json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_IDPW_UPDATE"
                    , memNo: frm.memNo.value 
                    , custId: frm.custId.value 
                    , custNpw: frm.custNpw.value 
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            alert("새로운 비밀번호가 설정되었습니다.\n\n로그인 화면에서 로그인 해주세요.");
                            js_close();
                        }
                        else
                        {
                            alert("error3");
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

function CheckPassword(uid, upw)
{
    //if(!/^[a-zA-Z0-9]{6,12}$/.test(upw))    //숫자+영문자
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{5,12}$/.test(upw)) //숫자+영문자+특수문자           
    {
        alert("비밀번호는 숫자, 영문자, 특수문자 조합으로\n\n6~12자리를 사용해야 합니다.");
        frm.custNpw.focus();
        return false;
    }
    
    var chk_num = upw.search(/[0-9]/g);
    var chk_eng = upw.search(/[a-z]/ig);
    
    if(chk_num<0 || chk_eng<0)
    {
        alert("비밀번호는 숫자와 영문자를 혼용하여야 합니다.");
        frm.custNpw.focus();
        return false;
    }
    
    if(/(\w)\1\1\1/.test(upw))
    {
        alert("비밀번호에 같은 문자를 4번 이상 사용하실 수 없습니다.");
        frm.custNpw.focus();
        return false;
    }
    
    if(upw.search(uid)>-1)
    {
        alert("ID가 포함된 비밀번호는 사용하실 수 없습니다.");
        frm.custNpw.focus();
        return false;
    }

    return true;
}

function js_close()
{
    parent.opener.document.frm.iid.focus();
    window.close();
}
</script>
</head>
<body>
    <br>
    <div class="container members login">
    <h5 class="title"><span id="spanIdpw">아이디/비밀번호 찾기</span></h5>
    <div class="contents">
        <form class="form-horizontal in-login" name="frm" method="post" onsubmit="return js_login();">
		<input type="hidden" name="mode" value="S">
        <input type="hidden" name="hpnmhd">
        <input type="hidden" name="phone1hd">
        <input type="hidden" name="phone2hd">
        <input type="hidden" name="phone3hd">
        <input type="hidden" name="memNo">
        <input type="hidden" name="cer_no">
     
        <!--SMS용-->
        <input type="hidden" name="smsType" value="S"/>
        <input type="hidden" name="destination" value="">
        <input type="hidden" name="nointeractive" value="0"> 
        <input type="hidden" name="testflag" value="Y">
        <input type="hidden" name="repeatFlag" value="N" />
        <input type="hidden" name="rdate" value=""> 
        <input type="hidden" name="rtime" value="">
        <input type="hidden" name="repeatNum" value="">
        <input type="hidden" name="repeatTime" value="">
        <input type="hidden" name="sms_code" value="HP02">
        <!--SMS용-->

          <div class="form-group" id="divhpnm">
            <label class="control-label col-sm-3" for="hpnm">고객명</label>
            <div class="col-sm-10 col-lg-offset-0 col-lg-9">
              <input type="text" class="form-control" name="hpnm" id="hpnm" style="width:385px;display:inline;"maxlength=10>
            </div>
          </div>
          <div class="form-group" id="divphone">
            <label class="control-label col-sm-3" for="phone">휴대폰번호</label>
            <div class="col-sm-10 col-lg-9"> 
              <input type="number" class="form-control" name="phone1" id="phone1" style="width:120px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
              <input type="number" class="form-control" name="phone2" id="phone2" style="width:120px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
              <input type="number" class="form-control" name="phone3" id="phone3" style="width:120px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
            </div>
          </div>
          <div class="form-group" id="dividpw"> 
            <div class="col-sm-offset-2 col-sm-10 col-lg-9 col-lg-offset-3" style="text-align:center;">
              <button type="button" name="idpw" id="idpw" class="btn btn-default" onclick="fn_idpwFind();">ID/PW 찾기</button>
              <!--<button type="button" name="idpw" id="idpw" class="btn btn-default" onclick="js_cerCk();">ID/PW 찾기</button>-->
            </div>
          </div>

          <div class="form-group" id="divcer" >           
            <div class="col-sm-offset-2 col-sm-10 col-lg-9 col-lg-offset-3" style="text-align:left;">
            <label>인증번호</label><br>
            <input type="number" class="form-control" name="cerbox" id="cerbox" style="width:185px;display:inline; text-align: center;" max="9999" maxlength="6" oninput="maxLengthCheck(this)" >  
            &nbsp;&nbsp;&nbsp;<div id = "divtimer" style="width:300px;display:inline; text-align: center;"></div>&nbsp;&nbsp;&nbsp;
            <button type="button" name="certifi" id="certifi" class="btn btn-default" onclick="js_cerConfirm();">인증번호확인</button>
            </div>
          </div>

          <div class="form-group" id="divcust" style="display: block;">

            <label class="control-label col-sm-3" >고객 ID</label>
            <div class="col-sm-10 col-lg-offset-0 col-lg-9">
              <input type="text" class="form-control" name="custId" id="custId" style="width:380px;display:inline;"maxlength=10 readonly>
            </div> <br>

            <label class="control-label col-sm-3" >새로운 비밀번호</label>
            <div class="col-sm-10 col-lg-offset-0 col-lg-9">
              <input type="password" class="form-control" name="custNpw" id="custNpw" style="width:380px;display:inline;"maxlength=10>
            </div> <br>

            <label class="control-label col-sm-3" >비밀번호 확인</label>
            <div class="col-sm-10 col-lg-offset-0 col-lg-9">
              <input type="password" class="form-control" name="custNpwcf" id="custNpwcf" style="width:380px;display:inline;"maxlength=10>
            </div> <br>

            <div class="col-sm-offset-2 col-sm-10 col-lg-9 col-lg-offset-3" style="text-align:center;">
              <button type="button" name="custpwOk" id="custpwOk" class="btn btn-default" onclick="js_pwUpConfirm();">확인</button>
            </div>

          </div>

            

        </form>     
        
        
    </div>
</div>
            
</body>
</html>