<?
	require "../_common/home_pre_setting.php";
	
	require "../_classes/biz/member/member.php";

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "header.php";
	
?>
</head>
<body>
<?
	require "top.php";
?>
<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.col-sm-10 button:disabled { background:gray !important;color:white !important; cursor: not-allowed; }

input:read-only {background-color: #F0F0F0;}  

</style>

<script>
	$(document).ready(function(){

        //document.getElementById("divlevel1").style.display ="none";
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
//maxlength üũ
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
        alert("���̵�� �����ڷ� �����ϴ� ��/���� 6~12�� �Դϴ�.");
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
			url: "./json/json_hp_regist.php",
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
                            alert("�ߺ��Ǵ� ID�� �ֽ��ϴ�.\n\n�ٽ� Ȯ�� �ٶ��ϴ�.");
							//frm.iid.value = "";
							frm.iid.focus();
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

function jsHpck()
{
	var frm = document.frm;

    if(frm.name.value == "") 
	{
        alert("�̸��� �Է� �� �ּ���.");
        frm.name.focus();
        return false;
    }

    //NewWindow("pop_hp_confirm.php", 'hp_certificationNm','420','300','YES');
    NewWindow("pop_hp_certification.php", 'hp_certificationNm','430','300','YES');
}

function CheckPassword(uid, upw)
{
    //if(!/^[a-zA-Z0-9]{6,12}$/.test(upw))    //����+������
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{6,12}$/.test(upw)) //����+������+Ư������           
    {
        alert("��й�ȣ�� ����, ������, Ư������ ��������\n\n6~12�ڸ��� ����ؾ� �մϴ�.");
        frm.pwd.focus();
        return false;
    }
    
    var chk_num = upw.search(/[0-9]/g);
    var chk_eng = upw.search(/[a-z]/ig);
    
    if(chk_num<0 || chk_eng<0)
    {
        alert("��й�ȣ�� ���ڿ� �����ڸ� ȥ���Ͽ��� �մϴ�.");
        frm.pwd.focus();
        return false;
    }
    
    if(/(\w)\1\1\1/.test(upw))
    {
        alert("��й�ȣ�� ���� ���ڸ� 4�� �̻� ����Ͻ� �� �����ϴ�.");
        frm.pwd.focus();
        return false;
    }
    
    if(upw.search(uid)>-1)
    {
        alert("ID�� ���Ե� ��й�ȣ�� ����Ͻ� �� �����ϴ�.");
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
		alert("�̸��� ������ �ùٸ��� �ʽ��ϴ�.")
		document.frm.emailbt.focus();
		return false;         
	}
	
	return true;
}     

function checkbizNo(bizID) 
{    
    //alert(bizID);
    // bizID�� ���ڸ� 10�ڸ��� �ؼ� ���ڿ��� �ѱ��. 
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
        alert("����� ��ȣ�� Ȯ�� �� �ּ���.");
        return false; 
    } 

    return true;
    
}

function js_memeberIn()
{		
	var frm = document.frm;

    if(frm.iid.value == "") 
	{
        alert("���̵� �Է� �� �ּ���.");
        frm.iid.focus();
        return false;
    }
	
	if (!CheckPassword(frm.iid.value, frm.pwd.value))    return;

    if(frm.pwd.value != frm.pwd_check.value)
    {
        alert("��й�ȣ�� ��ġ ���� �ʽ��ϴ�.\n\n�ٽ� Ȯ�� �ٶ��ϴ�.");
        frm.pwd_check.focus();
        return false;
    }

	if(frm.name.value == "") 
	{
        alert("�̸��� �Է� �� �ּ���.");
        frm.name.focus();
        return false;
    }

	if(frm.email1.value == "" || frm.email2.value == "") 
	{
        alert("email�� ��Ȯ�� �Է��� �ּ���.");
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
        alert("��ȭ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.telphone1.focus();
        return false;
    }

    if (frm.telphone2.value == "") {
        alert("��ȭ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.telphone2.focus();
        return false;
    }
    
    if (frm.telphone3.value == "") {
        alert("��ȭ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.telphone3.focus();
        return false;
    }

	if (frm.phone1.value == "" || frm.phone1.value.length != 3) 
    {
        alert("�޴��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone1.focus();
        return false;
    }

    if (frm.phone2.value == "" || frm.phone2.value.length != 4) 
    {
        alert("�޴��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone2.focus();
        return false;
    }
    
    if (frm.phone3.value == "" || frm.phone3.value.length != 4) 
    {
        alert("�޴��� ��ȣ�� ��Ȯ�� �Է��� �ּ���.");
        frm.phone3.focus();
        return false;
    }

	if (frm.zipcode.value == "" || frm.addr.value == "" || frm.addr2.value == "") 
    {
        alert("�����ȣ �� �ּҸ� ��Ȯ�� �Է��� �ּ���.");
        frm.zipcodebt.focus();
        return false;
    }

    if (frm.biz_num1.value != "" || frm.biz_num2.value != "" || frm.biz_num3.value != "") 
    {
        if (!checkbizNo(frm.biz_num1.value+frm.biz_num2.value+frm.biz_num3.value))    return;

        if (frm.companyname.value == "") 
        {
            alert("ȸ����� �Է� �� �ּ���.");
            frm.companyname.focus();
            return false;
        }
        
        if (frm.ceoname.value == "") 
        {
            alert("��ǥ�ڸ��� �Է� �� �ּ���.");
            frm.ceoname.focus();
            return false;
        }
    }

    if(frm.iidck.value != "Y")
    {
        alert("ID �ߺ�üũ�� ���ּ���.");
        frm.id_dup.focus();
        return false;
    }

    if(frm.iid.value != frm.iidtwo.value)
    {
        alert("ID �ߺ�üũ �� ���� ������ �Ǿ����ϴ�.\n\nID �ߺ�üũ�� �ٽ� ���ּ���.");
        frm.iidck.value = "";
        document.getElementById("idyn").style.display ="none";
        frm.id_dup.focus();
        return false;
    }

    if(frm.hpcerCk.value != "Y")
    {
        alert("�޴��������� ���ּ���.");
        frm.hpConfrim.focus();
        return false;
    }

    if(frm.cp_no.value == "")
    {
        frm.cp_no.value = "10300";      //���ΰ� 10300
    }

    if(!confirm("ȸ�� ���� �Ͻðڽ��ϱ�?")) return;
    
    $.ajax({
			url: "./json/json_hp_regist.php",
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
                    , custAddr2: frm.addr2.value
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
                            alert("ȸ�������� �Ϸ� �Ǿ����ϴ�.");
                            document.getElementById("divlevel1").style.display ="none";
                            document.getElementById("divlevel2").style.display ="none";

                            $('html, body').scrollTop(0);
                            
                            document.getElementById("divlevel3").style.display ="block";
                            
                            document.getElementById("spanid").innerHTML = "<b>"+frm.iid.value+"</b>";
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
					alert('��� ����');
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

    /*document.getElementById("companyname").style.backgroundColor = "#F0F0F0";
    document.getElementById("biz_num1").style.backgroundColor = "#F0F0F0";
    document.getElementById("biz_num2").style.backgroundColor = "#F0F0F0";
    document.getElementById("biz_num3").style.backgroundColor = "#F0F0F0";
    document.getElementById("ceoname").style.backgroundColor = "#F0F0F0";

    document.getElementById('search_btn').style.backgroundColor = "gray";
    document.getElementById('search_btn').style.cursor = "not-allowed";*/
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
    
    /*document.getElementById("phone1").style.backgroundColor = "#F0F0F0";
    document.getElementById("phone2").style.backgroundColor = "#F0F0F0";
    document.getElementById("phone3").style.backgroundColor = "#F0F0F0";

    document.getElementById('hpConfrim').style.backgroundColor = "gray";
    document.getElementById('hpConfrim').style.cursor = "not-allowed";*/
}

</script>

<!-- ȸ������ -->

<div class="detail_page">
    <div class="detail_page_inner">

        <form name="frm" class="form-horizontal in-signin" method="post">
            <input type="hidden" name="iidck">
            <input type="hidden" name="iidtwo">
    <!-------------------------------------------------------------������� start----------------------------------------------------------------------->
        <div id="divlevel1">
            <div class="cart_info">
            <h2>ȸ������</h2>
                <div class="process">      
                    <div class="nav active">�������</div>
                    <div class="nav ">�����Է�</div>
                    <div class="nav">���ԿϷ�</div>                
                </div>
                    <div class="form-group">
                        <label>�� �̿� ���</label>
                        <div style="width:90%; height:300px; padding:10px; overflow:hidden; overflow-y:scroll; overflow-x:scroll; border: 1px solid #babdc8;overflow-x:hidden !important;">
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
                                <input type="checkbox" id="chk_area0"> ����� �о����� ���뿡 �����մϴ�.
                            </label>
                        </div>            
                        <br>
                        <label>�� �������� �� ���� �̿�</label>
                        <div style="width:90%; height:300px; padding:10px; overflow:hidden; overflow-y:scroll; overflow-x:scroll; border: 1px solid #babdc8;">
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
                            <input type="checkbox" id="chk_area1"> ����� �о����� ���뿡 �����մϴ�.
                            </label>
                        </div>
                    </div>    
                    <div class="tcenter margin-top-10">
                        <a href="log-in.php" class="carting">���</a>
                        <button class="joomoon btn_level1">����</button>
                    </div>                    
            </div>     
        </div>    
    <!-------------------------------------------------------------������� END----------------------------------------------------------------------->
                
    <!-------------------------------------------------------------�����Է� START----------------------------------------------------------------------->			
        <div id="divlevel2">
            <div class="cart_info">    
            <h2>ȸ������</h2>
                <div class="process">      
                    <div class="nav">�������</div>
                    <div class="nav active">�����Է�</div>
                    <div class="nav">���ԿϷ�</div>
                </div>

                    <div class="form-group regist rg_first" style="margin-top: 30px;">
                        <label class="control-label" for="iid"><font color="red">*</font> ���̵�</label>
                        <div class="col-sm-10" >
                            <input type="text" class="form-control" style="width:255px;" id="iid" name="iid" max="9999" maxlength="12" oninput="maxLengthCheck(this)" >
                            &nbsp;<button type="button" id="id_dup" onclick="jsIdck();">ID�ߺ�üũ</button>
                            <span id="idyn" style="color: blue;">��밡���� ID�Դϴ�.</span>
                        </div>	                    
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="pwd"><font color="red">*</font> ��й�ȣ</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" style="width:255px" id="pwd" name="pwd" maxlength="12">
                                <span style="color: gray;">&nbsp;&nbsp;6-12���� ����+������+Ư������ ����</span>
                            </div><br><br>

                        <label class="control-label" for="rpwd"><font color="red">*</font> ��й�ȣ Ȯ��</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" style="width:255px" id="pwd_check" name="pwd_check" maxlength="12">                                
                            </div>                    
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="name"><font color="red">*</font> �̸�</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" style="width:255px;" id="name" name="name" maxlength="10">
                            </div>
                    </div>                

                    <div class="form-group regist">
                        <label class="control-label" for="email"><font color="red">*</font> �̸��� �ּ�</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email1" name="email1" style="width:255px;" maxlength="12">  @ 
                                <input type="text" class="form-control" id="email2" name="email2" style="width:255px;" maxlength="20">
                                <div class="btn-group">
                                &nbsp;<button type="button" name="emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">�ڵ� �Է�</button>
                                    <ul class="dropdown-menu" data-target="email2" style="display:none;">
                                        <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
                                        <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
                                        <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                                    </ul>                                                                
                                </div>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $(".dropdown-toggle").click(function(){
                                        if ( $(".dropdown-menu").css("display") == "none" )
                                        {
                                            $(".dropdown-menu").css("display","block");
                                        } else {
                                            $(".dropdown-menu").css("display","none");
                                        }
                                    });
                                    $(".dropdown-menu").click(function(){
                                        $(this).css("display","none");
                                    });
                                });
                            </script>          
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="phone"><font color="red">*</font> ��ȭ��ȣ</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control " name="telphone1" id="telphone1" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                            <input type="number" class="form-control " name="telphone2" id="telphone2" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                            <input type="number" class="form-control " name="telphone3" id="telphone3" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                        </div>
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="name"><font color="red">*</font> �޴���ȭ</label>
                        <div class="col-sm-10">
                        <input type="hidden" id="hpcerCk" name="hpcerCk">
                        <input type="number" class="form-control " name="phone1" id="phone1" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                        <input type="number" class="form-control " name="phone2" id="phone2" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                        <input type="number" class="form-control " name="phone3" id="phone3" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                        &nbsp;<button type="button" id="hpConfrim" onclick="jsHpck();">�޴�������</button>
                        <button type="button" id="resetHp_btn">�ʱ�ȭ</button>
                        <span id="ceryn" style="color: blue;">�޴��������� �Ϸ� �Ǽ̽��ϴ�.</span>
                        </div>
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="zipcode"><font color="red">*</font> �����ȣ</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="zipcode" name="zipcode" style="width:75px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
                            &nbsp;<button type="button" class="trigger-find_addr" id="zipcodebt" name="zipcodebt">�ּҰ˻�</button>
                        </div><div style="clear:both"></div>
                        <label class="control-label"><font color="red">*</font> ��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" style="width:400px; display:inline;" id="addr" name="addr" maxlength=50>
                            <input type="text" class="form-control" style="width:300px; display:inline;" id="addr2" name="addr2" maxlength=50>
                        </div>
                    </div>

                    <!--<div style="text-align: center;"><span style="color: green;">������� ��� ��������� �Է��� �ּ���. ���ݰ�꼭�� ���ϰ� �̿��Ͻ� �� �ֽ��ϴ�.��</span></div>-->
                    <div style="text-align: center;background:#fff;padding-top:20px;padding-bottom:20px;border-top:1px solid #d4d4d4;padding-left:20px;box-sizing:border-box;"><span style="color: green;"> ����� ��� ��������� ��Ȯ�� �Է��� �ּ���. ���ݰ�꼭�� ���ϰ� �̿��Ͻ� �� �ֽ��ϴ�.</span></div>
                    
                    <div class="form-group regist rg_last">
                    <label class="control-label" for="biz_num1">����� ��ȣ</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="biz_num1" name="biz_num1" type="number" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                            <input class="form-control" id="biz_num2" name="biz_num2" type="number" style="width:75px;display:inline;" max="9999" maxlength="2" oninput="maxLengthCheck(this)"> -
                            <input class="form-control" id="biz_num3" name="biz_num3" type="number" style="width:75px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
                            &nbsp;&nbsp;<button type="button" id="search_btn" >�˻�</button>
                            <button type="button" id="reset_btn">�ʱ�ȭ</button>
                            <span style="color: gray;">&nbsp;&nbsp;���� ��ü�� ��� �˻� �����մϴ�.(�˻� ���� �� �����ͷ� �������ּ���.)</span>
                            <input type="hidden" id="cp_no" name="cp_no" />
                        </div><br ><br >
                        <label class="control-label" for="companyname">ȸ���</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="companyname" name="companyname" style="width:255px;"  maxlength=15>
                            </div><br ><br >
                        <label class="control-label" for="ceoname">��ǥ�ڸ�</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ceoname" style="width:255px;" name="ceoname"  maxlength=10>
                            </div>
                    </div>			

                    <div class="tcenter margin-top-10">
                        <a href="log-in.php" class="carting">���</a>
                        <button type="button" class="joomoon" onclick="js_memeberIn()">ȸ������</button>
                    </div>

            </div> 
        </div>    
    <!-------------------------------------------------------------�����Է� END----------------------------------------------------------------------->

    <!-------------------------------------------------------------���ԿϷ� START----------------------------------------------------------------------->
        <div id="divlevel3">
            <div class="cart_info">    
            <h2>ȸ������</h2>
            <div class="process">
                <div class="nav">�������</div>
                <div class="nav">�����Է�</div>
                <div class="nav active">���ԿϷ�</div>
            </div>    
                <br><br>
                        
                <img src="img/cong.png" alt="�����մϴ�!" style="position:relative;z-index:999999;width:90%;display:block;margin:0px auto;">
                <div class="cong">
                    <div>
                        ID &nbsp;&nbsp;: &nbsp;&nbsp;<span id="spanid"></span><br>
                        PW : &nbsp;&nbsp;<b>***********</b>
                    </div><br><br>
                    ���̵� �н����带 �н��ϼ��� ��쿣 '��й�ȣ ã��'�� ���� ã���� �� �ֽ��ϴ�.
                </div><br><br>

                <div class="tcenter margin-top-10">
                    <a href="Nindex.php" class="carting">Ȩ����</a>
                    <a href="log-in.php" class="joomoon">�α���</a>
                </div>
            </div>
        </div>    
<!-------------------------------------------------------------���ԿϷ� END----------------------------------------------------------------------->
        </form>
    </div>
</div>    
<!-- // ȸ������ -->

<?
	require "footer.php";
?>
<script type="text/javascript">
	$(function(){
		$("#chk_area0 , #chk_area1").click(function(){
			if($("input:checkbox[id='chk_area0']").is(":checked") && $("input:checkbox[id='chk_area1']").is(":checked"))
				$(".btn_level1").addClass("active");
			else
				$(".btn_level1").removeClass("active");
		});

		//�������
		$(".btn_level1").click(function(e){
			e.preventDefault();

			if($(this).hasClass("active")) {

				document.getElementById("divlevel1").style.display ="none";
				document.getElementById("divlevel3").style.display ="none";

                $('html, body').scrollTop(0);
                
				document.getElementById("divlevel2").style.display ="block";                

			} else { 
				alert('����� �������ּž� ȸ�������� ����˴ϴ�.');
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
			//NewWindow("pop_search_company.php", 'search_company','450','600','YES');
            NewWindow("pop_find_company.php", 'search_company','450','740','YES');
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

            /*$('#biz_num1').css("backgroundColor", "white");
            $('#biz_num2').css("backgroundColor", "white");
            $('#biz_num3').css("backgroundColor", "white");

            $('#companyname').css("backgroundColor", "white");
            $('#ceoname').css("backgroundColor", "white");

            $('#search_btn').css("cursor", "pointer");

            $('#search_btn').hover(function() {
                $(this).css("background", "black");
            }, function(){
                $(this).css("background", "gray");
            });*/

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

            /*$('#phone1').css("backgroundColor", "white");
            $('#phone2').css("backgroundColor", "white");
            $('#phone3').css("backgroundColor", "white");

            $('#hpConfrim').css("cursor", "pointer");

            $('#hpConfrim').hover(function() {
                $(this).css("background", "black");
            }, function(){
                $(this).css("background", "gray");
            });*/

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

                // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.
                // �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
                // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
                var fullAddr = ''; // ���� �ּ� ����
                var extraAddr = ''; // ������ �ּ� ����

                // ����ڰ� ������ �ּ� Ÿ�Կ� ���� �ش� �ּ� ���� �����´�.
                if (data.userSelectedType === 'R') { // ����ڰ� ���θ� �ּҸ� �������� ���
                    fullAddr = data.roadAddress;

                } else { // ����ڰ� ���� �ּҸ� �������� ���(J)
                    fullAddr = data.jibunAddress;
                }

                // ����ڰ� ������ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
                if(data.userSelectedType === 'R'){
                    //���������� ���� ��� �߰��Ѵ�.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // �ǹ����� ���� ��� �߰��Ѵ�.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
				 // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				document.getElementById("zipcode").value = data.zonecode;
				//document.getElementById("re_zip").value = data.postcode2;
				document.getElementById("addr").value = fullAddr;
				// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
				document.getElementById("addr").focus();

            }
        }).open();
    }

</script>  
<style>
	body { overflow:hidden; }
</style>
</body>
</html>

