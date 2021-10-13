<?
	require "_common/home_pre_setting.php";
	
	require "_classes/biz/member/member.php";

	/*
		echo "���̵�: ".$iid."<br/>";
		echo "���: ".$pwd."<br/>";
		echo "�̸�: ".$name."<br/>";
		echo "�̸����ּ�: ".$email1."@".$email2."<br/>";
		echo "��ȭ: ".$phone."<br/>";
		echo "�޴���ȭ: ".$hphone."<br/>";
		echo "ȸ���: ".$etc."<br/>";
		echo "���1: ".$mode."<br/>";
	*/

	if($mode == "SIGNIN") { 

		//���뿡 �̻��� ������ GOTO level_3, �ƴϸ� �ٽ� level_2�� 
		$mem_type = "S"; //Supplier ��ǰ��ü
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
		
		//���� �ʿ�
		$use_tf = 'N';
		$reg_adm = 0;

		$cnt = dupMember ($conn, $mem_id);

		$error_msg = "";

		if($error_msg == "" && ($biz_num1 == "" || $biz_num2 == "" || $biz_num3 == ""))
			$error_msg = "����� ��Ϲ�ȣ�� �Է��� �ֽʽÿ�.";

		if($error_msg == "" && $mem_id == "")
			$error_msg = "���̵� �Է����ּ���.";

		if($error_msg == "" && strlen($mem_id) < 6)
			$error_msg = "6�� �̻��� ���̵� ����� �ֽʽÿ�.";

		if($error_msg == "" && $cnt > 0) 
			$error_msg = "�ߺ��� ���̵� �ֽ��ϴ�.";
		
		if($error_msg == "" && ($pwd <> $pwd_check))
			$error_msg = "��й�ȣ�� ��й�ȣ Ȯ���� �ٸ��ϴ�.";


		if($error_msg == "" && $pwd == "")
			$error_msg = "��й�ȣ�� �Է����ּ���.";

		if($error_msg == "" && ($email1 == "" || $email2 == ""))
			$error_msg = "�̸����� �Է����ּ���.";

		if($error_msg == "" && ($phone == "" || $hphone == ""))
			$error_msg = "���� ������ ����ó�� �Է����ּ���.";

		if($error_msg == "" && $addr1 == "")
			$error_msg = "��� ������ ����� �ּҸ� �Է����ּ���.";

		//echo "�����޼��� : ".$error_msg."<br/>";

		if($error_msg == "") { 
			$result = insertMember($conn, $mem_type, $mem_id, $mem_pw, $mem_nm, $jumin1, $jumin2, $biz_num1, $biz_num2, $biz_num3, $birth_date, $calendar, $email, $email_tf, $zipcode, $addr1, $addr2, $phone, $hphone, $job, $position, $cphone, $cfax, $czipcode, $caddr1, $caddr2, $join_how, $join_how_person, $join_how_etc, $etc, $foreigner_num, $use_tf, $reg_adm,$cp_no);
			
			//�����̸� level3�� ���Ƿ� ǥ�õ��� ����
			$error_msg = "�Է°����� �ý��� ������ �߻��Ͽ����ϴ�.";
		} else
			$result = false;

		//���н� level_2��
		if($result) { 
			$mode = "level_3";
		
			//�ý��� ������ �̸��� �߼�
			$from_email = getDcodeName($conn, "HOME_INFO", "MEM_REG_DISPLAY");
			$to_email = getDcodeName($conn, "HOME_INFO", "MEM_REG_EMAIL");
			$email_subject = getDcodeExtByCode($conn, "HOME_INFO", "MEM_REG_SUBJECT");
			$email_body = getDcodeExtByCode($conn, "HOME_INFO", "MEM_REG_BODY");

			$email_subject = str_replace("[MEM_ID]", $mem_nm." ��", $email_subject);

			$email_body = str_replace("[BR]", "<br>", $email_body);
			

			include('_PHPMailer/class.phpmailer.php');
			ini_set('display_errors', 'Off');

			$result_msg = mailer("����Ʈ��-Ȩ����������", $from_email, $to_email, $to_email, $email_subject, $email_body, '', '');

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
<!-- ȸ������ -->
<div class="container members signin">
    <h5 class="title">ȸ������</h5>
    <div class="contents">
        <form name="frm" class="form-horizontal in-signin" method="post">
			<input type="hidden" name="mode" value="">
			<? if($mode == "") { ?>
            <ul class="nav nav-pills navbar-right">
                <li>����� ����</li>
                <li class="active">�������</li>
                <li>�����Է�</li>
                <li>���ԿϷ�</li>
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
					  <input type="checkbox" id="chk_area1"> ����� �о����� ���뿡 �����մϴ�.
					</label>
				 </div>
            </div>
			<div class="btns text-center" role="group">
                <button type="button" class="btn btn-default btn_level1">���� �ܰ��</button>
                <button type="reset" class="btn btn-default">���</button>
            </div>

			<? } ?>
			<? if($mode == "level_2") { ?>
				<? if($error_msg <> "") { ?>
			<div class="alert alert-danger alert-dismissible" role="alert"><?=$error_msg?></div>
				<? } ?>
			<ul class="nav nav-pills navbar-right">
				<li>����� ����</li>
				<li>�������</li>
                <li class="active">�����Է�</li>
                <li>���ԿϷ�</li>
            </ul>
            <div class="form-group">
				<label class="control-label col-sm-3" for="biz_num1">����� ��ȣ</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input class="form-control inline-3" id="biz_num1" name="biz_num1" type="text" value="<?=$biz_num1?>">
					<input class="form-control inline-3" id="biz_num2" name="biz_num2" type="text" value="<?=$biz_num2?>">
					<input class="form-control inline-3" id="biz_num3" name="biz_num3" type="text" value="<?=$biz_num3?>">
				</div>
            </div>			
            <div class="form-group">
                <label class="control-label col-sm-3" for="iid">���̵�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="id" class="form-control" id="iid" name="iid" placeholder="" value="<?=$iid?>">
                    <!--<button type="button" class="btn btn-default" id="id-dup">���̵� �ߺ� Ȯ��</button>-->
					<span>���� ���� ��/���� 6~10��</span>
                </div>
				
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="pwd">��й�ȣ</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="">
					<span>6-15���� ���� ��ҹ���, ���� �� Ư������ ����</span>
                </div><br ><br >
                <label class="control-label col-sm-3" for="pwd">��й�ȣ Ȯ��</label>
                <div class="col-sm-10 col-lg-9">
                    <input type="password" class="form-control" id="pwd" name="pwd_check" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="name">�̸�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="name" class="form-control" id="name" name="name" placeholder="" value="<?=$name?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3" for="email">�̸��� �ּ�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="email" name="email1" placeholder="" value="<?=$email1?>">  @ 
					<input type="text" class="form-control" name="email2" placeholder="" value="<?=$email2?>">
                    <div class="btn-group">
                         <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">�ڵ� �Է�<span class="caret"></span></button>
                          <ul class="dropdown-menu" data-target="email2">
                              <li role="presentation"><a href="#" class="sel_email_ext">gmail.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">naver.com</a></li>
							  <li role="presentation"><a href="#" class="sel_email_ext">daum.net</a></li>
                          </ul>
                      </div>
                </div>
                
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="phone">��ȭ��ȣ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="" value="<?=$phone?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="name">�޴���ȭ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="hphone" name="hphone" placeholder="" value="<?=$hphone?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="zipcode">�����ȣ</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="" value="<?=$zipcode?>">
					<button type="button" class="btn btn-default trigger-find_addr" id="zipcode">�ּҰ˻�</button>
                </div>
				<label class="control-label col-sm-3" for="addr1">��ü �ּ�</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" style="width:100%;" id="addr1" name="addr1" placeholder="" value="<?=$addr1?>">
                </div>
            </div>
			<div class="form-group">
                <label class="control-label col-sm-3" for="etc">��Ÿ ����</label>
                <div class="col-sm-10 col-lg-offset-0 col-lg-9">
                    <input type="text" class="form-control" id="etc" name="etc" placeholder="" value="<?=$etc?>">
					<span>�ҼӾ�ü / �����Ͻ÷��� ������</span>
                </div>
			</div>
							
			<div class="form-group">
				<label class="control-label col-sm-3">���� ��ǰ��ü�� �ŷ�ó DB���� ��ü�� �������ּ���.</label>
				<div class="col-sm-10 col-lg-offset-0 col-lg-9">
					<input type="button" id="search_btn" style="width:100px;margin-bottom:3px;" class="btn-sm btn btn-default" value="�˻�">
					<input type="hidden" id="cp_no" name="cp_no" value />
				</div>
			</div>

            <div class="btns text-center" role="group">
                <button type="submit" class="btn btn-default active btn_level2">ȸ������</button>
                <button type="reset" class="btn btn-default">���</button>
            </div>
			<? } ?>
			<? if($mode == "level_3") { ?>
				<ul class="nav nav-pills navbar-right">
					<li>����� ����</li>
					<li>�������</li>
					<li>�����Է�</li>
					<li class="active">���ԿϷ�</li>
				</ul>
				<div class="form-group">
					
					<div style="width:100%; height:300px; padding:10px; text-align:center;">
						���ԵǼ̽��ϴ�. ������ ������ �Ϸ�Ǹ� �����̳� ������ ���ؼ� �����帮�ڽ��ϴ�. 
					</div>
				</div>
				<div class="btns text-center" role="group">
					<a href="login.php"><button type="button" class="btn btn-default btn_login active">�α���</button></a>
					<button type="reset" class="btn btn-default">Ȩ����</button>
				</div>
			<? } ?>
        </form>
    </div>
</div>
<!-- // ȸ������ -->

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

		//�������
		$(".btn_level1").click(function(e){
			e.preventDefault();

			if($(this).hasClass("active")) {
				var frm = document.frm;
				frm.mode.value="level_2";
				frm.method="post";
				frm.action="<?=$_SERVER[PHP_SELF]?>";
				frm.submit();

			} else { 
				alert('����� �������ּž� ȸ�������� ����˴ϴ�.');
				$("#chk_area1").focus();
			}
		});

		//�����Է�
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
				document.getElementById("addr1").value = fullAddr;
				// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
				document.getElementById("addr1").focus();


            }
        }).open();
    }

</script>  
</body>
</html>

