<!DOCTYPE html>
<head>
<title>휴대폰 인증</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="js/jquery.min.js"></script>

<script src="js/all.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
<link rel="stylesheet" href="css/stylemb.css" type='text/css'>

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
		document.getElementById("divcerconfirm").style.display ="none";
        /*document.getElementById("hpnmhd").value = opener.document.getElementById("name").value;
        document.getElementById("phone1").value = opener.document.getElementById("phone1").value;
        document.getElementById("phone2").value = opener.document.getElementById("phone2").value;
        document.getElementById("phone3").value = opener.document.getElementById("phone3").value;
        
        $('#cerNm').focus();
        */
	});

</script>
<script type="text/javascript">

var timer = null;
var isRunning = false;

function iframeOnload()
{
    clearInterval(timer);

    document.getElementById("phone1").disabled = false;
    document.getElementById("phone2").disabled = false;
    document.getElementById("phone3").disabled = false;

    document.frm.cerbox.value = "";

    document.getElementById("divcerSend").style.display ="block"; 
    document.getElementById("divcerconfirm").style.display ="none"; 

    document.getElementById("hpnmhd").value = parent.document.getElementById("name").value;
    document.getElementById("phone1").value = parent.document.getElementById("phone1").value;
    document.getElementById("phone2").value = parent.document.getElementById("phone2").value;
    document.getElementById("phone3").value = parent.document.getElementById("phone3").value;
    
    $('#cerNm').focus();
}

function fn_certificationNm()
{
    var frm = document.frm;

    if (frm.phone1.value == "" || frm.phone1.value.length != 3) 
    {
        alert("휴대폰 번호를 정확히 입력해 주세요.");
        frm.phone1.focus();
        return false;
    }

    if (frm.phone2.value == "" || (frm.phone2.value.length != 3 && frm.phone2.value.length != 4)) 
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

    $.ajax({
			url: "./json/json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CERTIFICATION_SEL"
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
                            alert("error");
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
			url: "./json/json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_SMS_INSERT"
					, custNm: frm.hpnmhd.value
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
                        //alert("item.RESULT:"+item.RESULT);
                        if(item.RESULT == "Y")
                        {
                            alert("기재된 번호로 인증번호가 전송되었습니다.\n\n인증번호를 입력해 주세요.\n\n※ 인증번호를 받지 못한신 경우 스팸문자함을 확인해 주세요.");
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
            $('#cerNm').focus();
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

    frm.phone1hd.value = frm.phone1.value;
    frm.phone2hd.value = frm.phone2.value;
    frm.phone3hd.value = frm.phone3.value;
    
    document.getElementById("divcerSend").style.display ="none"; 
    document.getElementById("divcerconfirm").style.display ="block"; 

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
			url: "./json/json_sms_sender.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CERTIFICATION_CONFIRM"
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
                            alert("인증번호가 확인 되었습니다.");
                            js_close();
                        }
                        else
                        {
                            alert("인증번호가 일치 하지 않습니다.\n\n다시 확인 바랍니다.");
                            frm.cerbox.focus();
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

function js_close()
{
    clearInterval(timer);

    parent.document.getElementById("phone1").value = frm.phone1hd.value;
    parent.document.getElementById("phone2").value = frm.phone2hd.value;
    parent.document.getElementById("phone3").value = frm.phone3hd.value;
    
    parent.fn_hp_popEnd();
    
    parent.document.getElementById("hp_popup").style.display = "none";
}
</script>
</head>
<body class="wrap_hp_body">
    <div class="wrap_hp">    
    <div>
        <b>휴대폰 인증</b>
            <span>휴대폰 번호</span>

        <form class="form-horizontal in-login" name="frm" method="post" onsubmit="return js_login();">
		<input type="hidden" name="mode" value="S">
        <input type="hidden" name="hpnmhd" id="hpnmhd">
        <input type="hidden" name="phone1hd">
        <input type="hidden" name="phone2hd">
        <input type="hidden" name="phone3hd">
        <input type="hidden" name="memNo">
        <input type="hidden" name="cer_no">
     
        <!--SMS용-->
        <input type="hidden" name="smsType" value="S"/>
        <input type="hidden" name="destination" value="">
        <input type="hidden" name="nointeractive" value="0"> 
        <input type="hidden" name="testflag" value="Y"><!--테스트일 경우 Y 아니면 공백으로 하면 문자날라간다-->
        <input type="hidden" name="repeatFlag" value="N" />
        <input type="hidden" name="rdate" value=""> 
        <input type="hidden" name="rtime" value="">
        <input type="hidden" name="repeatNum" value="">
        <input type="hidden" name="repeatTime" value="">
        <input type="hidden" name="sms_code" value="HP01">
        <!--SMS용-->

          <div id="divphone">
            <div> 
              <input type="number" class="form-control" name="phone1" id="phone1" style="width:50px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
              <input type="number" class="form-control" name="phone2" id="phone2" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
              <input type="number" class="form-control" name="phone3" id="phone3" style="width:50px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> 
            </div>
          </div>

          <div class="trightidpw" id="divcerSend" style="font-size: 16px;"> 
            <div>
              <a href="javascript:fn_certificationNm()" name="cerNm" id="cerNm" class="cartingidpw" >인증번호전송</a>
            </div>
          </div>

          <div id="divcerconfirm" >           
            <div class="trightidpw">
            <span>인증번호</span>
            <input type="number" class="form-control" name="cerbox" id="cerbox" style="width:42%;display:inline; text-align: center;" max="9999" maxlength="6" oninput="maxLengthCheck(this)" >  
            &nbsp;&nbsp;&nbsp;
            <div id = "divtimer" style="width:20%;display:inline; text-align: center; color: blue;"></div>&nbsp;&nbsp;&nbsp;
            <button type="button" name="certifi" id="certifi" class="cartingidpw" onclick="js_cerConfirm();">인증번호확인</button>
            </div>
          </div>           

        </form>     
        
        
    </div>
</div>
            
</body>
</html>