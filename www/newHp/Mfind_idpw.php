
<?
	require "../_common/home_pre_setting.php";
?>
<!DOCTYPE html>
<head>
<?
	require "Mheader.php";
?>

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
        alert("�̸��� �Է� �� �ּ���.");
        frm.hpnm.focus();
        return false;
    }

    if (frm.phone1.value == "") {
        alert("�ڵ��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone1.focus();
        return false;
    }

    if (frm.phone2.value == "") {
        alert("�ڵ��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone2.focus();
        return false;
    }
    
    if (frm.phone3.value == "") {
        alert("�ڵ��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone3.focus();
        return false;
    }


    $.ajax({
			url: "./json/json_sms_sender.php",
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
                            alert("����� �޴��� ��ȣ�� ��ġ�ϴ� �����Ͱ� �����ϴ�.");
                            return ;
                        }
                    });
				}	,
				fail : function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
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
    title   = "[����Ʈ��]\n";       //smsŸ���� S�ϰ�� ������ ǥ����� �ʴ´�. 
    msg     = "������ȣ ["+frm.cer_no.value+"]�� �Է� �� �ּ���.";
    recver1 = "070";
    recver2 = "8896";
    recver3 = "0627";
    smstext = title+msg;        //(����ȭ���� Sms�̱⿡..)

    $.ajax({
			url: "./json/json_sms_sender.php",
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
                    , msg: smstext      //cafe24 �μ�Ʈ��
                    , message: smstext  //����Ʈ�� �μ�Ʈ��
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
                            alert("["+ item.RETURN_VLAUE +"]\n\n������ȣ�� �߼۵Ǿ����ϴ�. ������ȣ�� Ȯ���� �ּ���!");
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
					alert('��� ����');
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

//maxlength üũ
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
    var leftSec = 180; //3�� ����
    // ���� �ð�
    // �̹� Ÿ�̸Ӱ� �۵����̸� ����
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

    document.getElementById("divtimer").innerHTML = minutes + "��" + seconds + "��";

    // Ÿ�̸� ��
    if (--count < 0) 
    {
        clearInterval(timer);
        alert("������ȣ Ȯ�� �ð��� �ʰ� �Ǿ����ϴ�. �ٽ� Ȯ�� �ٶ��ϴ�.");
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
        alert("������ȣ 6�ڸ��� �Է��� �ּ���.");
        frm.cerbox.focus();
        return false;
    }

    $.ajax({
			url: "./json/json_sms_sender.php",
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
                            alert("������ȣ�� Ȯ�� �Ǿ����ϴ�.\n\n���ο� ��й�ȣ�� ���� �� �ּ���.");
                            frm.memNo.value = item.MEM_NO;
                            frm.custId.value = item.MEM_ID;
                            js_pwUpSetting();
                        }
                        else
                        {
                            alert("������ȣ�� ��ġ ���� �ʽ��ϴ�.\n\n�ٽ� Ȯ�� �ٶ��ϴ�.");
                            return ;
                        }
                    });
				}	,
				fail : function(jqXHR, textStatus, errorThrown)
				{
					alert('��� ����');
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

    document.getElementById("spanIdpw").innerHTML = "<b>��й�ȣ ����</b>";

    frm.custNpw.focus();
}

function js_pwUpConfirm()
{
    var frm = document.frm;

    if (!CheckPassword(frm.custId.value, frm.custNpw.value))    return;

    if(frm.custNpw.value != frm.custNpwcf.value)
    {
        alert("��й�ȣ�� ��ġ ���� �ʽ��ϴ�.\n\n�ٽ� Ȯ�� �ٶ��ϴ�.");
        frm.custNpwcf.focus();
        return false;
    }

/*
    alert(frm.memNo.value);
    alert(frm.custId.value);
    alert(frm.custNpw.value);
*/
    $.ajax({
			url: "./json/json_sms_sender.php",
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
                            alert("���ο� ��й�ȣ�� �����Ǿ����ϴ�.\n\n�α��� ȭ�鿡�� �α��� ���ּ���.");
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
					alert('��� ����');
					return;
				}
		});
}

function CheckPassword(uid, upw)
{
    //if(!/^[a-zA-Z0-9]{6,12}$/.test(upw))    //����+������
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{5,12}$/.test(upw)) //����+������+Ư������           
    {
        alert("��й�ȣ�� ����, ������, Ư������ ��������\n\n6~12�ڸ��� ����ؾ� �մϴ�.");
        frm.custNpw.focus();
        return false;
    }
    
    var chk_num = upw.search(/[0-9]/g);
    var chk_eng = upw.search(/[a-z]/ig);
    
    if(chk_num<0 || chk_eng<0)
    {
        alert("��й�ȣ�� ���ڿ� �����ڸ� ȥ���Ͽ��� �մϴ�.");
        frm.custNpw.focus();
        return false;
    }
    
    if(/(\w)\1\1\1/.test(upw))
    {
        alert("��й�ȣ�� ���� ���ڸ� 4�� �̻� ����Ͻ� �� �����ϴ�.");
        frm.custNpw.focus();
        return false;
    }
    
    if(upw.search(uid)>-1)
    {
        alert("ID�� ���Ե� ��й�ȣ�� ����Ͻ� �� �����ϴ�.");
        frm.custNpw.focus();
        return false;
    }

    return true;
}

function js_close()
{
    location.href = "Mlog-in.php";
}
</script>
</head>
<body >
<div class="wrap_id_pw">
    <div>
        <span id="spanIdpw"><b>���̵�/��й�ȣ ã��</b></span><br>
        
            <form class="form-horizontal in-login" name="frm" method="post" onsubmit="return js_login();">
            <input type="hidden" name="mode" value="S">
            <input type="hidden" name="hpnmhd">
            <input type="hidden" name="phone1hd">
            <input type="hidden" name="phone2hd">
            <input type="hidden" name="phone3hd">
            <input type="hidden" name="memNo">
            <input type="hidden" name="cer_no">
        
            <!--SMS��-->
            <input type="hidden" name="smsType" value="S"/>
            <input type="hidden" name="destination" value="">
            <input type="hidden" name="nointeractive" value="0"> 
            <input type="hidden" name="testflag" value="">  <!--���� ������ ���ڳ���. ���� Y�� �������� �ȵ�-->
            <input type="hidden" name="repeatFlag" value="N" />
            <input type="hidden" name="rdate" value=""> 
            <input type="hidden" name="rtime" value="">
            <input type="hidden" name="repeatNum" value="">
            <input type="hidden" name="repeatTime" value="">
            <input type="hidden" name="sms_code" value="HP02">
            <!--SMS��-->

            <div id="divhpnm">
                <span>����</span>
                <div>
                <input type="text" name="hpnm" id="hpnm" style="width:300px;display:inline;" maxlength=10>
                </div>
            </div>
            
            <div id="divphone">
                <span id="phone">�޴�����ȣ</span>
                <div style="width:auto;"> 
                    <input type="number" class="form-control" name="phone1" id="phone1" style="width:89px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                    <input type="number" class="form-control" name="phone2" id="phone2" style="width:89px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                    <input type="number" class="form-control" name="phone3" id="phone3" style="width:89px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                </div>
            </div>

            <div id="dividpw" class="trightidpw"> 
                <div>
                <a href="javascript:fn_idpwFind()" name="idpw" id="idpw" class="cartingidpw" >ID/PW ã��</a>
                </div>
            </div>
            
            <div id="divcer" >           
                <div>
                <span>������ȣ</span>
                <input type="number" class="ij" name="cerbox" id="cerbox" style="width:89px;display:inline; justify-content: left; align-items: left; text-align: center;" max="9999" maxlength="6" oninput="maxLengthCheck(this)" >  
                &nbsp;&nbsp;&nbsp;<div id = "divtimer" style="width:180px;display:inline; text-align: center; color: blue;"></div>&nbsp;&nbsp;&nbsp;
                <button type="button" name="certifi" id="certifi" onclick="js_cerConfirm();" style="width:100px; float: right;">������ȣȮ��</button>
                </div>
            </div>

            <div id="divcust" style="display: block;">

                <span>�� ID</span>
                
                <input type="text" name="custId" id="custId" style="width:300px;display:inline; margin-bottom: 30px;" maxlength=10 readonly>

                <span>���ο� ��й�ȣ</sapn>
                <input type="password" name="custNpw" id="custNpw" style="width:300px;display:inline; margin-bottom: 30px;;" maxlength=10 placeholder="����+������+Ư������ �������� 6~12�ڸ�">

                <sapn>��й�ȣ Ȯ��</sapn>
                <input type="password" name="custNpwcf" id="custNpwcf" style="width:300px;display:inline; margin-bottom: 30px;;" maxlength=10 placeholder="����+������+Ư������ �������� 6~12�ڸ�">

                <div class="trightidpw" style="text-align:center;">
                <a href="javascript:js_pwUpConfirm()" name="custpwOk" id="custpwOk" class="cartingidpw" >Ȯ��</a>
                </div>

            </div>           

        </form>         

    </div>
    
</div>
<?
	require "Mfooter.php";
?>                
</body>
</html>