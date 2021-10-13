<?session_start();?>
<?
# =============================================================================
# File Name    : manager_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2016-04-01
# Modify Date  : 
#	Copyright    : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/admin/admin.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$date_start			= trim($date_start);
	$date_end				= trim($date_end);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	
	//echo $pb_nm; 
	//echo $$mode;
	
	$cp_type				= SetStringToDB($cp_type);
	$cp_nm					= SetStringToDB($cp_nm);
	$cp_phone				= SetStringToDB($cp_phone);
	$cp_hphone			= SetStringToDB($cp_hphone);
	$cp_fax					= SetStringToDB($cp_fax);
	$cp_addr				= SetStringToDB($cp_addr);
	$re_addr				= SetStringToDB($re_addr);
	$homepage				= SetStringToDB($homepage);
	$biz_no					= SetStringToDB($biz_no);
	$ceo_nm					= SetStringToDB($ceo_nm);
	$upjong					= SetStringToDB($upjong);
	$uptea					= SetStringToDB($uptea);
	$manager_nm			= SetStringToDB($manager_nm);
	$phone					= SetStringToDB($phone);
	$hphone					= SetStringToDB($hphone);
	$fphone					= SetStringToDB($fphone);
	$email					= SetStringToDB($email);
	$ad_type				= SetStringToDB($ad_type);
	$ad_type2				= SetStringToDB($ad_type2);
	$account_bank		= SetStringToDB($account_bank);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	
	
	if ($mode == "I") {
		
		$result =  insertCompany($conn, $cp_type, $cp_nm, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $manager_nm, $dept, $phone, $hphone, $fphone, $email, $email_tf, $manager_nm2, $dept2, $phone2, $hphone2, $fphone2, $email2, $email_tf2, $manager_nm3, $dept3, $phone3, $hphone3, $fphone3, $email3, $email_tf3, $contract_start, $contract_end, $ad_type, $ad_type2, $account_bank, $account_name, $account, $account_bank2, $account_name2, $account2, $account_bank3, $account_name3, $account3, $memo, $use_tf, $s_adm_no);
		
		$new_cp_no = mysql_insert_id();
		
		if ($cp_type == "판매") $group_no = "2";
		if ($cp_type == "공급") $group_no = "3";
		if ($cp_type == "판매공급") $group_no = "4";

		$result = insertAdmin($conn, $m_id, $m_pwd, $manager_nm, $adm_info, $hphone, $cp_phone, $email, $group_no, $adm_flag, $position_code, $dept_code, $new_cp_no, 'Y', '0');

	}

	if ($result) {
?>
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  "/manager/";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../js/jquery-1.7.min.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script language="javascript">

	function sample6_execDaumPostcode(a) {
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
				if (a == "O") {
					document.getElementById('cp_zip').value = data.zonecode; //5자리 새우편번호 사용
					document.getElementById('cp_addr').value = fullAddr;
					// 커서를 상세주소 필드로 이동한다.
					document.getElementById('cp_addr').focus();
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				if (a == "R") {
					document.getElementById('re_zip').value = data.zonecode; //5자리 새우편번호 사용
					document.getElementById('re_addr').value = fullAddr;
					// 커서를 상세주소 필드로 이동한다.
					document.getElementById('re_addr').focus();
				}

			}
		}).open();
	}


	// 저장 버튼 클릭 시 
	function js_save() {
		
		var frm = document.frm;
		
		if (isNull(frm.cp_nm.value)) {
			alert('업체명을 입력해주세요.');
			frm.cp_nm.focus();
			return ;		
		}
		
		if (frm.cp_type.value == "") {
			alert('업체구분을 선택해주세요.');
			frm.cp_type.focus();
			return ;		
		}

		if (frm.ad_type.value == "") {
			alert('결재구분을 선택해주세요.');
			frm.ad_type.focus();
			return ;		
		}

		if (isNull(frm.biz_no.value)) {
			alert('사업자 등록번호를 입력해주세요.');
			frm.biz_no.focus();
			return ;		
		}

		if (isNull(frm.ceo_nm.value)) {
			alert('대표자명을 입력해주세요.');
			frm.ceo_nm.focus();
			return ;		
		}

		if (isNull(frm.cp_phone.value)) {
			alert('대표 전화번호를 입력해주세요.');
			frm.cp_phone.focus();
			return ;		
		}
		

		if (isNull(frm.m_id.value)) {
			alert('아이디를 입력해주세요.');
			frm.m_id.focus();
			return ;		
		}

		if (isNull(frm.m_pwd.value)) {
			alert('비밀번호를 입력해주세요.');
			frm.m_pwd.focus();
			return ;		
		}

		if (isNull(frm.manager_nm.value)) {
			alert('담당자명을 입력해주세요.');
			frm.manager_nm.focus();
			return ;		
		}

		if (isNull(frm.hphone.value)) {
			alert('휴대전화번호를 입력해주세요.');
			frm.hphone.focus();
			return ;		
		}

		if (isNull(frm.email.value)) {
			alert('이메일을 입력해주세요.');
			frm.email.focus();
			return ;		
		}

		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}

		if (frm.rd_email_tf[0].checked == true) {
			frm.email_tf.value = "Y";
		} else {
			frm.email_tf.value = "N";
		}

		frm.mode.value = "I";

		frm.method = "post";
		frm.action = "manager_write.php";
		frm.submit();
	}

	//우편번호 찾기
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '우편번호찾기', '390', '370', 'NO');
	}

	/**
	* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
	*/
	function js_fileView(obj,idx) {
		
		var frm = document.frm;
		
		if (idx == 01) {
			if (obj.selectedIndex == 2) {
				frm.contracts_nm.style.visibility = "visible";
			} else {
				frm.contracts_nm.style.visibility = "hidden";
			}
		}

	}

	function chk_admin_id() {
		
		var frm = document.frm;

		var admin_id = frm.m_id.value.trim();

		var request = $.ajax({
			url:"/manager/company/manager_id_chk.php",
			type:"POST",
			data:{admin_id:admin_id},
			dataType:"html"
		});

		request.done(function(msg) {
			if (msg == "1") {
				alert("중복된 아이디가 있습니다.");
				frm.m_id.value = "";
			}
		});

		request.fail(function(jqXHR, textStatus) {
			alert("Request failed : " +textStatus);
			return false;
		});

	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="<?= $cp_no?>">
<input type="hidden" name="con_cp_type" value="<?= $con_cp_type?>">
<input type="hidden" name="date_start" value="<?= $date_start ?>">
<input type="hidden" name="date_end" value="<?= $date_end ?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	//require "../../_common/left_area.php";
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>제안업체 회원가입</h2>  
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>업체명</th>
							<td colspan="3"><input type="text" name="cp_nm" value="<?= $rs_cp_nm ?>" style="width:60%;" itemname="업체명" required class="txt"></td>
						</tr>
					</thead>
					<tbody>
							<th>업체구분</th>
							<td>
								<select name='cp_type' class="box01"  style='width:125px;' >
									<option value=''>선택</option><option value='판매'>판매</option>
									<option value='공급'>공급</option>
									<option value='판매공급'>판매공급</option>
								</select>
							</td>
							<th>결재구분</th>
							<td>
								<?= makeSelectBox($conn,"AD_TYPE","ad_type","125","선택","",$rs_ad_type)?>
							</td>
						</tr>

						<tr>
							<th>사업자	등록번호</th>
							<td>
								<input type="Text" name="biz_no" value="<?= $rs_biz_no?>" style="width:140px;" itemname="사업자	등록번호" required class="txt"> '-' 포함하여 입력해 주세요
							</td>
							<th>대표자명</th>
							<td><input type="Text" name="ceo_nm" value="<?= $rs_ceo_nm ?>" style="width:30%;" itemname="대표자명" required class="txt"></td>
						</tr>
						<tr>
							<th>대표 전화번호</th>
							<td>
								<input type="Text" name="cp_phone" value="<?= $rs_cp_phone?>" style="width:120px;" itemname="대표 전화번호" required class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>대표 FAX</th>
							<td>
								<input type="Text" name="cp_fax" value="<?= $rs_cp_fax?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>

						<tr>
							<th>주소</th>
							<td colspan="3">
								<input type="Text" name="cp_zip" id="cp_zip" value="<?= $rs_cp_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="cp_addr" id="cp_addr" value="<?= $rs_cp_addr?>" style="width:65%;" class="txt">
								<a href="javascript:void(0);" onclick="sample6_execDaumPostcode('O')"><img src="/manager/images/admin/btn_filesch.gif" alt="찾기" align="absmiddle" /></a>
							</td>
						<tr>

						<tr>
							<th>반품 주소</th>
							<td colspan="3">
								<input type="Text" name="re_zip" id="re_zip" value="<?= $rs_re_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="re_addr" id="re_addr" value="<?= $rs_re_addr?>" style="width:65%;" class="txt">
								<a href="javascript:void(0);" onclick="sample6_execDaumPostcode('R')"><img src="/manager/images/admin/btn_filesch.gif" alt="찾기" align="absmiddle" /></a>
							</td>
						<tr>

						<tr>
							<th>업종</th>
							<td><input type="Text" name="upjong" value="<?= $rs_upjong?>" style="width:40%;" class="txt"></td>
							<th>업태</th>
							<td><input type="Text" name="uptea" value="<?= $rs_uptea?>" style="width:40%;" class="txt"></td>
						</tr>

						<tr>
							<th>거래은행</th>
							<td><input type="Text" name="account_bank" value="<?= $rs_account_bank?>" itemname="거래은행" style="width:40%;" class="txt"></td>
							<th>계좌번호</th>
							<td><input type="Text" name="account" value="<?= $rs_account?>" style="width:40%;" itemname="계좌번호" class="txt" onkeyup="return isPhoneNumber(this)"></td>
						</tr>

						<tr>
							<th>계약 기간</th>
							<td class="lpd20 right" colspan="3">
								<input name="contract_start" type="text" class="txt" style="width:80px;" readonly value="<?= $rs_contract_start ?>"><a onclick="show_calendar('document.frm.contract_start', document.frm.contract_start.value)" style="cursor:hand">
								<img src="/manager/images/calendar/cal.gif" align="absmiddle"></a> ~ 
								<input name="contract_end" type="text" class="txt" style="width:80px;" readonly value="<?= $rs_contract_end ?>"><a onclick="show_calendar('document.frm.contract_end', document.frm.contract_end.value)" style="cursor:hand">
								<img src="/manager/images/calendar/cal.gif" align="absmiddle"></a>
							</td>
						</tr>


						<tr>
							<!--
							<th>활인율</th>
							<td>
								<input type="Text" name="dc_rate" value="<?= $rs_dc_rate?>" value="" style="width:70px;" class="txt" onkeyup="return isPhoneNumber(this)"> %
							</td>
							-->
							<th>홈페이지</th>
							<td>
								<input type="Text" name="homepage" value="<?= $rs_homepage?>" style="width:90%;" class="txt">
							</td>
							<th>사용여부</th>
							<td>
								<input type="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용<span style="width:20px;"></span>
								<input type="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 미사용
								<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
							</td>
						</tr>
						<tr>
							<th>업체메모</th>
							<td colspan="3" class="memo">
								<textarea style="width:75%" name="memo"><?= $rs_memo ?></textarea>
							</td>
						</tr>

					</tbody>
				</table>
				
				<div class="sp20"></div>

				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>아이디</th>
							<td><input type="Text" name="m_id" value="" style="width:90px;" required class="txt" onBlur="chk_admin_id()"></td>
							<th>비밀번호</th>
							<td><input type="password" name="m_pwd" value="" style="width:120px;" required class="txt"></td>
						</tr>
						<tr>
							<th>담당자 명</th>
							<td><input type="Text" name="manager_nm" value="<?= $rs_manager_nm ?>" required style="width:90px;" class="txt"></td>
							<th>전화번호</th>
							<td>
								<input type="Text" name="phone" value="<?= $rs_phone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>휴대 전화번호</th>
							<td>
								<input type="Text" name="hphone" value="<?= $rs_hphone ?>" style="width:120px;" required class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>FAX 번호</th>
							<td>
								<input type="Text" name="fphone" value="<?= $rs_fphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						<tr>
						<tr>
							<th>이메일</th>
							<td><input type="Text" name="email" value="<?= $rs_email ?>" style="width:90%;" required class="txt"></td>
							<th>이메일 수신여부</th>
							<td>
								<input type="radio" name="rd_email_tf" value="Y" <? if (($rs_email_tf =="Y") || ($rs_email_tf =="")) echo "checked"; ?>> 수신<span style="width:20px;"></span>
								<input type="radio" name="rd_email_tf" value="N" <? if ($rs_email_tf =="N") echo "checked"; ?>> 미수신</td>
								<input type="hidden" name="email_tf" value="">
							</td>
						<tr>
					</tbody>
				</table>

				<div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>