<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/member/member.php";	

    $mem_no = $_SESSION['C_MEM_NO'];
    $mem_type = $_SESSION['C_MEM_TYPE'];

?>	
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "_common/v2_header.php";	
?>
<script>
    ;
     $(document).ready(function(){
         
        $.ajax({
			url: "json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_MYINFO_SEL"
                    , mem_no   : <?=$mem_no?>
                    , mem_type : "<?=$mem_type?>"
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            document.frm.iid.value = item.MEM_ID;
                            document.frm.name.value = item.MEM_NM; 	
                            document.frm.companyname.value = item.CP_NM;		
                            document.frm.ceoname.value = item.CEO_NM; 	
                            document.frm.biz_num1.value = item.BIZ_NUM1;
                            document.frm.biz_num2.value = item.BIZ_NUM2;
                            document.frm.biz_num3.value = item.BIZ_NUM3;
                            document.frm.email1.value = item.EMAIL1;
                            document.frm.email2.value = item.EMAIL2;
                            document.frm.zipcode.value = item.ZIPCODE;
                            document.frm.addr.value = item.ADDR1;
                            document.frm.telphone1.value = item.PHONE1;
                            document.frm.telphone2.value = item.PHONE2;
                            document.frm.telphone3.value = item.PHONE3;
                            document.frm.phone1.value = item.HPHONE1;
                            document.frm.phone2.value = item.HPHONE2;
                            document.frm.phone3.value = item.HPHONE3;
                            document.frm.cp_no.value = item.CP_NO;
                            fn_check();
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
					alert('��� ����');
					return;
				}
		});

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
            $('#zipcodebt').focus();
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
function fn_check()
{
    var frm = document.frm;
    //alert(frm.cp_no.value);
    if(frm.cp_no.value != "" && frm.cp_no.value != "0")
    {
        js_popClose();
    }

    return;
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

function js_popClose()
{
    document.getElementById("companyname").readOnly = true;
    document.getElementById("biz_num1").readOnly = true;
    document.getElementById("biz_num2").readOnly = true;
    document.getElementById("biz_num3").readOnly = true;
    document.getElementById("ceoname").readOnly = true;

    document.getElementById('search_btn').disabled = true;
}

function fn_update()
{
    var frm = document.frm;

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

	if (frm.zipcode.value == "" || frm.addr.value == "") {
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

    $.ajax({
			url: "json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_CUSTOMER_UP"
                    , custNo: frm.mem_no.value
                    , custTp: frm.mem_type.value
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
                            alert("�������� ���� �Ǿ����ϴ�.");
                            location.reload();
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

</script>
</head>
<body>
<style>

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
<?
	require "_common/v2_top.php";
?>
<!-- ���������� -->
<div class="container members signin">
    <h5 class="title">����������</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mem_no" value="<?=$mem_no?>">
            <input type="hidden" name="mem_type" value="<?=$mem_type?>">

			<? if($error_msg <> "") { ?>
			<div class="alert alert-danger alert-dismissible" role="alert"><?=$error_msg?></div>
			<? } ?>
			
			<ul class="nav nav-pills navbar-right">
                <li class="active">��������</li>
            </ul>
            <div class="form-group">
                <label class="control-label col-sm-3" for="iid">���̵�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9" >
                    <input type="text" class="form-control" id="iid" name="iid" max="9999" maxlength="12" oninput="maxLengthCheck(this)" readonly>
                </div>			
				
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="pwd">��й�ȣ</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd" >
					<span>6-12���� ����+������+Ư������ ����</span>
                </div><br ><br >
                <label class="control-label col-sm-3" for="rpwd">��й�ȣ Ȯ��</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd_check" name="pwd_check" >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="name">�̸�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="name" name="name"  maxlength=10>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">�̸��� �ּ�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="email1" name="email1" >  @ 
					<input type="text" class="form-control" id="email2" name="email2" >
                    <div class="btn-group">
                         <button type="button" name = "emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">�ڵ� �Է�<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="email2">
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                      </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">��ȭ��ȣ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="number" class="form-control" name="telphone1" id="telphone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone2" id="telphone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
					<input type="number" class="form-control" name="telphone3" id="telphone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">�޴���ȭ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                <input type="hidden" id="hpcerCk" name="hpcerCk">
				<input type="number" class="form-control" name="phone1" id="phone1" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone2" id="phone2" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
				<input type="number" class="form-control" name="phone3" id="phone3" style="width:63px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">                
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">�����ȣ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="number" class="form-control" id="zipcode" name="zipcode" style="width:63px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcodebt" name="zipcodebt">�ּҰ˻�</button>
                </div>
				<label class="control-label col-sm-3">��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%; display:inline;" id="addr" name="addr">
                </div>
            </div>					
            <br><div style="text-align: center;"><span style="color: green;">������� ��� ��������� �Է��� �ּ���. ���ݰ�꼭�� ���ϰ� �̿��Ͻ� �� �ֽ��ϴ�.��</span></div>
            <div class="form-group">
            <label class="control-label col-sm-3" for="biz_num1">����� ��ȣ</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input class="form-control inline-3" id="biz_num1" name="biz_num1" type="number" style="width:63px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
					<input class="form-control inline-3" id="biz_num2" name="biz_num2" type="number" style="width:60px;display:inline;" max="9999" maxlength="2" oninput="maxLengthCheck(this)"> -
					<input class="form-control inline-3" id="biz_num3" name="biz_num3" type="number" style="width:66px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
					&nbsp;&nbsp;<button type="button" id="search_btn" class="btn btn-default">�˻�</button>
                    <button type="button" id="reset_btn" class="btn btn-default">�ʱ�ȭ</button>
					<span>���� ��ü�� ��� �˻� �����մϴ�.</span>
					<input type="hidden" id="cp_no" name="cp_no" />
				</div><br ><br >
                <label class="control-label col-sm-3" for="companyname">ȸ���</label>
					<div class="col-sm-10 col-lg-offset-0 col-lg-9">
						<input type="text" class="form-control" id="companyname" name="companyname"  maxlength=10>
				    </div><br ><br >
                <label class="control-label col-sm-3" for="ceoname">��ǥ�ڸ�</label>
					<div class="col-sm-10 col-lg-offset-0 col-lg-9">
						<input type="text" class="form-control" id="ceoname" name="ceoname"  maxlength=10>
					</div>
            </div>	

            <div class="btns text-center" role="group">
                <button type="button" class="btn btn-default active" onclick="fn_update()">����</button>
                <!--<button type="button" class="btn btn-default" onclick="history.back()" >���</button>-->
                <a href="index.php"><button type="button" class="btn btn-default">Ȩ����</button></a>
            </div>
        </form>
    </div>
</div>
<!-- // ȸ������ -->

<?
	require "_common/v2_footer.php";
?>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

	$(function(){

		//�����Է�
		$(".btn_submit").click(function(e){
			e.preventDefault();

			var frm = document.frm;
			frm.mode.value="U";
			frm.method="post";
			frm.action="<?=$_SERVER[PHP_SELF]?>";
			frm.submit();

		});

		$(".sel_email_ext").click(function(e){
			e.preventDefault();
			var target_elem = $(this).closest(".dropdown-menu").data("target");
			var sel_email_ext = $(this).html();
			$("[name=" + target_elem + "]").val(sel_email_ext);
		});

		$(".trigger-find_addr").click(sample6_execDaumPostcode);

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
</body>
</html>

