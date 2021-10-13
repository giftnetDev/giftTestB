<?
	require "../_common/home_pre_setting.php";
	
	require "../_classes/biz/member/member.php";	

    $mem_no = $_SESSION['C_MEM_NO'];
    $mem_type = $_SESSION['C_MEM_TYPE'];

?>	
<!DOCTYPE html>
<html lang="ko">
<head>
<?
	require "header.php";	
?>
<script>
    
     $(document).ready(function(){
         
        $.ajax({            
			url: "./json/json_hp_regist.php",
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
                            document.frm.addr2.value = item.ADDR2;
                            document.frm.telphone1.value = item.PHONE1;
                            document.frm.telphone2.value = item.PHONE2;
                            document.frm.telphone3.value = item.PHONE3;
                            document.frm.phone1.value = item.HPHONE1;
                            document.frm.phone2.value = item.HPHONE2;
                            document.frm.phone3.value = item.HPHONE3;
                            document.frm.cp_no.value = item.CP_NO;
                            document.frm.selcpno.value = item.CP_NO;
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
					alert('통신 실패');
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
//maxlength 체크
function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
    {
        object.value = object.value.slice(0, object.maxLength);
    }   
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
function fn_check()
{
    var frm = document.frm;
    //alert(frm.cp_no.value);    
    if(frm.cp_no.value != "" && frm.cp_no.value != "10300")
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

function fn_update()
{
    var frm = document.frm;

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

	if (frm.zipcode.value == "" || frm.addr.value == ""  || frm.addr2.value == "") 
    {
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

    if(!confirm("고객정보를 수정 하시겠습니까?"))return;

    $.ajax({
			url: "./json/json_hp_regist.php",
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
                            alert("고객정보가 수정 되었습니다.");
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
					alert('통신 실패');
					return;
				}
		});
}

function js_member_pwck()
{
    var frm = document.frm;

    if (!CheckPassword(frm.iid.value, frm.pwd.value))    return;

    if(frm.pwd.value != frm.pwd_check.value)
    {
        alert("비밀번호가 일치 하지 않습니다.\n\n다시 확인 바랍니다.");
        frm.pwd_check.focus();
        return false;
    }

    $.ajax({
			url:"./json/json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_MEMBER_PWCK"
                    , custNo: frm.mem_no.value
					, custID: frm.iid.value
                    , custPwd: frm.pwd.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
							js_member_del();
                        }
                        else
                        {
                            alert("비밀번호를 정확히 입력 해 주세요.");
							frm.pwd.focus();
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

function js_member_del()
{
    if(!confirm("탈퇴 시 고객님의 정보가 삭제되며, 복구 하실수 없습니다.\n\n또한, 탈퇴 후 재가입 시 탈퇴 전 사용한 아이디는 불가합니다.\n\n정말 탈퇴 하시겠습니까?")) return;

    $.ajax({
			url: "./json/json_hp_regist.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "HOMEPAGE_MEMBER_DEL"
                    , custNo   : <?=$mem_no?>
					, custID: frm.iid.value
				},
				success: function(data) 
				{
					$.each(data, function(i, item)
                    {
                        if(item.RESULT == "Y")
                        {
                            js_log_out();
                        }
                        else
                        {
                            alert("error. 탈퇴 처리가 되지 않았습니다. 고객센터 문의 바랍니다.");
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

function js_log_out()
{
    alert("탈퇴 처리가 완료 되었습니다.");

    var frm=document.frm1;
    frm.mode.value="LOGOUT";
    frm.method="POST";
    frm.action="Nindex.php";
    frm.target="";
    frm.submit();
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

.col-sm-10 button:disabled { background:gray !important;color:white !important; cursor: not-allowed; }

input:read-only {background-color: #F0F0F0;}  

</style>
<?
	require "top.php";
?>
<!-- 마이페이지 -->
<div class="detail_page">
    <div class="detail_page_inner">

        <form name="frm" class="form-horizontal in-signin" method="post">
            <input type="hidden" name="mem_no" value="<?=$mem_no?>">
            <input type="hidden" name="mem_type" value="<?=$mem_type?>">
            <input type="hidden" id="selcpno" name="selcpno">
                
    <!-------------------------------------------------------------정보입력 START----------------------------------------------------------------------->			
            <div class="cart_info">    
            <h2>마이페이지</h2>
                <div class="process">      
                    <div class="nav active">정보변경</div>
                </div>
                
                    <div class="form-group regist rg_first" style="margin-top: 30px;">
                        <label class="control-label" for="iid"><font color="red">*</font> 아이디</label>
                        <div class="col-sm-10" >
                            <input type="text" class="form-control" style="width:255px;" id="iid" name="iid" max="9999" maxlength="12" oninput="maxLengthCheck(this)" readonly >
                        </div>	                    
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="pwd"><font color="red">*</font> 비밀번호</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" style="width:255px" id="pwd" name="pwd" maxlength="12">
                                <span style="color: gray;">&nbsp;&nbsp;6-12자의 숫자+영문자+특수문자 조합</span>
                            </div><br><br>

                        <label class="control-label" for="rpwd"><font color="red">*</font> 비밀번호 확인</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" style="width:255px" id="pwd_check" name="pwd_check" maxlength="12">                                
                            </div>                    
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="name"><font color="red">*</font> 이름</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" style="width:255px;" id="name" name="name" maxlength="10">
                            </div>
                    </div>                

                    <div class="form-group regist">
                        <label class="control-label" for="email"><font color="red">*</font> 이메일 주소</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email1" name="email1" style="width:255px;">  @ 
                                <input type="text" class="form-control" id="email2" name="email2" style="width:255px;">
                                <div class="btn-group">
                                &nbsp;<button type="button" name="emailbt" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">자동 입력</button>
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
                        <label class="control-label" for="phone"><font color="red">*</font> 전화번호</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control " name="telphone1" id="telphone1" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                            <input type="number" class="form-control " name="telphone2" id="telphone2" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                            <input type="number" class="form-control " name="telphone3" id="telphone3" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                        </div>
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="name"><font color="red">*</font> 휴대전화</label>
                        <div class="col-sm-10">
                        <input type="hidden" id="hpcerCk" name="hpcerCk">
                        <input type="number" class="form-control " name="phone1" id="phone1" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                        <input type="number" class="form-control " name="phone2" id="phone2" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)"> -
                        <input type="number" class="form-control " name="phone3" id="phone3" style="width:75px;display:inline;" max="9999" maxlength="4" oninput="maxLengthCheck(this)">
                        </div>
                    </div>

                    <div class="form-group regist">
                        <label class="control-label" for="zipcode"><font color="red">*</font> 우편번호</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="zipcode" name="zipcode" style="width:75px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
                            &nbsp;<button type="button" class="trigger-find_addr" id="zipcodebt" name="zipcodebt">주소검색</button>
                        </div><div style="clear:both"></div>
                        <label class="control-label"><font color="red">*</font> 주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" style="width:400px; display:inline;" id="addr" name="addr" maxlength=50>
                            <input type="text" class="form-control" style="width:300px; display:inline;" id="addr2" name="addr2" maxlength=50>
                        </div>
                    </div>

                    <!--<div style="text-align: center;"><span style="color: green;">▷기업의 경우 기업정보를 입력해 주세요. 세금계산서시 편리하게 이용하실 수 있습니다.◁</span></div>-->
                    <div style="text-align: center;background:#fff;padding-top:20px;padding-bottom:20px;border-top:1px solid #d4d4d4;padding-left:20px;box-sizing:border-box;"><span style="color: green;"> 기업의 경우 기업정보를 정확히 입력해 주세요. 세금계산서시 편리하게 이용하실 수 있습니다.</span></div>
                    
                    <div class="form-group regist rg_last">
                    <label class="control-label" for="biz_num1">사업자 번호</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="biz_num1" name="biz_num1" type="number" style="width:75px;display:inline;" max="9999" maxlength="3" oninput="maxLengthCheck(this)"> -
                            <input class="form-control" id="biz_num2" name="biz_num2" type="number" style="width:75px;display:inline;" max="9999" maxlength="2" oninput="maxLengthCheck(this)"> -
                            <input class="form-control" id="biz_num3" name="biz_num3" type="number" style="width:75px;display:inline;" max="9999" maxlength="5" oninput="maxLengthCheck(this)">
                            &nbsp;&nbsp;<button type="button" id="search_btn" >검색</button>
                            <button type="button" id="reset_btn">초기화</button>
                            <span style="color: gray;">&nbsp;&nbsp;기존 업체일 경우 검색 가능합니다.</span>
                            <input type="hidden" id="cp_no" name="cp_no" />
                        </div><br ><br >
                        <label class="control-label" for="companyname">회사명</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="companyname" name="companyname" style="width:255px;"  maxlength=15>
                            </div><br ><br >
                        <label class="control-label" for="ceoname">대표자명</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ceoname" style="width:255px;" name="ceoname"  maxlength=10>
                            </div>
                    </div>		
                    <div style="text-align: right;background:#fff; margin-top: -65px;">
                        <button type="button" onclick="js_member_pwck();" style="cursor:pointer;width:80px;height:30px;background:gray;color:white;border:none;vertical-align:middle;" id="member_del">회원탈퇴</button>
                    </div>
                    <div class="tcenter margin-top-10">
                        <a href="Newindex.php" class="carting">홈으로</a>
                        <button type="button" class="joomoon" onclick="fn_update()">수정</button>
                    </div>

            </div> 
    <!-------------------------------------------------------------정보변경 END----------------------------------------------------------------------->
        </form>
    </div>
</div>    
<!-- // 마이페이지 -->

<?
	require "footer.php";
?>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script type="text/javascript">

	$(function(){

		//정보입력
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
			//NewWindow("pop_search_company.php", 'search_company','450','600','YES');
            NewWindow("pop_find_company.php", 'search_company','450','740','YES');
		});

        $("#reset_btn").click(function()
        {
            //alert($('#cp_no').val());
            //alert($('#selcpno').val());

            if($('#cp_no').val() != 10300 && $('#cp_no').val() != "" && $('#selcpno').val() != 10300 )
            {   
                alert("기업정보를 변경 하실 경우 고객센터에 문의 바랍니다.\n\n☎ 031-527-6812");
                return;
            }
            else
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
            }
            
		});
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

