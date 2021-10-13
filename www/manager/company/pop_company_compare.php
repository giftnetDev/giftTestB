<?session_start();?>
<?
# =============================================================================
# File Name    : pop_company_compare.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2019-12-01
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CP002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";
	
#====================================================================
# common_header
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================
	$mode 			= trim($mode);
	$cp_no			= trim($cp_no);
	$history_no	= trim($history_no);
	
	$result = false;

	$arr_rs = selectCompany($conn, $cp_no);

	$rs_cp_no								= trim($arr_rs[0]["CP_NO"]); 
	$rs_cp_cate							= SetStringFromDB($arr_rs[0]["CP_CATE"]); 
	$rs_cp_nm								= SetStringFromDB($arr_rs[0]["CP_NM"]); 
	$rs_cp_nm2							= SetStringFromDB($arr_rs[0]["CP_NM2"]); 
	$rs_cp_code							= SetStringFromDB($arr_rs[0]["CP_CODE"]); 
	$rs_cp_type							= SetStringFromDB($arr_rs[0]["CP_TYPE"]); 
	$rs_ad_type							= SetStringFromDB($arr_rs[0]["AD_TYPE"]); 
	$rs_cp_phone						= SetStringFromDB($arr_rs[0]["CP_PHONE"]); 
	$rs_cp_hphone						= SetStringFromDB($arr_rs[0]["CP_HPHONE"]); 
	$rs_cp_fax							= SetStringFromDB($arr_rs[0]["CP_FAX"]); 
	$rs_cp_zip							= trim($arr_rs[0]["CP_ZIP"]); 
	$rs_cp_addr							= SetStringFromDB($arr_rs[0]["CP_ADDR"]); 
	$rs_re_zip							= trim($arr_rs[0]["RE_ZIP"]); 
	$rs_re_addr							= SetStringFromDB($arr_rs[0]["RE_ADDR"]); 
	$rs_biz_no							= trim($arr_rs[0]["BIZ_NO"]); 
	$rs_ceo_nm							= SetStringFromDB($arr_rs[0]["CEO_NM"]); 
	$rs_upjong							= SetStringFromDB($arr_rs[0]["UPJONG"]); 
	$rs_uptea								= SetStringFromDB($arr_rs[0]["UPTEA"]); 
	$rs_account_bank				= SetStringFromDB($arr_rs[0]["ACCOUNT_BANK"]); 
	$rs_account							= trim($arr_rs[0]["ACCOUNT"]); 
	$rs_account_owner_nm		= trim($arr_rs[0]["ACCOUNT_OWNER_NM"]); 
	$rs_homepage						= SetStringFromDB($arr_rs[0]["HOMEPAGE"]); 
	$rs_memo								= trim($arr_rs[0]["MEMO"]); 
	$rs_dc_rate							= trim($arr_rs[0]["DC_RATE"]); 
	$rs_sale_adm_no					= trim($arr_rs[0]["SALE_ADM_NO"]);
	$rs_manager_nm					= SetStringFromDB($arr_rs[0]["MANAGER_NM"]); 
	$rs_phone								= SetStringFromDB($arr_rs[0]["PHONE"]); 
	$rs_hphone							= SetStringFromDB($arr_rs[0]["HPHONE"]); 
	$rs_fphone							= SetStringFromDB($arr_rs[0]["FPHONE"]); 
	$rs_email								= SetStringFromDB($arr_rs[0]["EMAIL"]); 
	$rs_email_tf						= trim($arr_rs[0]["EMAIL_TF"]); 
	$rs_contract_start			= trim($arr_rs[0]["CONTRACT_START"]); 
	$rs_contract_end				= trim($arr_rs[0]["CONTRACT_END"]); 
	$rs_is_mall							= trim($arr_rs[0]["IS_MALL"]); 
	$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 
	$rs_del_tf							= trim($arr_rs[0]["DEL_TF"]); 
	$rs_reg_adm							= trim($arr_rs[0]["REG_ADM"]); 
	$rs_reg_date						= trim($arr_rs[0]["REG_DATE"]); 
	$rs_up_adm							= trim($arr_rs[0]["UP_ADM"]); 
	$rs_up_date							= trim($arr_rs[0]["UP_DATE"]); 
	$rs_del_adm							= trim($arr_rs[0]["DEL_ADM"]); 
	$rs_del_date						= trim($arr_rs[0]["DEL_DATE"]); 

	if ($rs_contract_start <> "0000-00-00") {
		$rs_contract_start = date("Y-m-d",strtotime($rs_contract_start));
	} else {
		$rs_contract_start = "";
	}

	if ($rs_contract_end <> "0000-00-00") {
		$rs_contract_end = date("Y-m-d",strtotime($rs_contract_end));
	} else {
		$rs_contract_end = "";
	}

	$arr_info_history = selectCompanyHistory($conn, $history_no);

	$pre_cp_no							= trim($arr_info_history[0]["CP_NO"]); 
	$pre_cp_cate						= SetStringFromDB($arr_info_history[0]["CP_CATE"]); 
	$pre_cp_nm							= SetStringFromDB($arr_info_history[0]["CP_NM"]); 
	$pre_cp_nm2							= SetStringFromDB($arr_info_history[0]["CP_NM2"]); 
	$pre_cp_code						= SetStringFromDB($arr_info_history[0]["CP_CODE"]); 
	$pre_cp_type						= SetStringFromDB($arr_info_history[0]["CP_TYPE"]); 
	$pre_ad_type						= SetStringFromDB($arr_info_history[0]["AD_TYPE"]); 
	$pre_cp_phone						= SetStringFromDB($arr_info_history[0]["CP_PHONE"]); 
	$pre_cp_hphone					= SetStringFromDB($arr_info_history[0]["CP_HPHONE"]); 
	$pre_cp_fax							= SetStringFromDB($arr_info_history[0]["CP_FAX"]); 
	$pre_cp_zip							= trim($arr_info_history[0]["CP_ZIP"]); 
	$pre_cp_addr						= SetStringFromDB($arr_info_history[0]["CP_ADDR"]); 
	$pre_re_zip							= trim($arr_info_history[0]["RE_ZIP"]); 
	$pre_re_addr						= SetStringFromDB($arr_info_history[0]["RE_ADDR"]); 
	$pre_biz_no							= trim($arr_info_history[0]["BIZ_NO"]); 
	$pre_ceo_nm							= SetStringFromDB($arr_info_history[0]["CEO_NM"]); 
	$pre_upjong							= SetStringFromDB($arr_info_history[0]["UPJONG"]); 
	$pre_uptea							= SetStringFromDB($arr_info_history[0]["UPTEA"]); 
	$pre_account_bank				= SetStringFromDB($arr_info_history[0]["ACCOUNT_BANK"]); 
	$pre_account						= trim($arr_info_history[0]["ACCOUNT"]); 
	$pre_account_owner_nm		= trim($arr_info_history[0]["ACCOUNT_OWNER_NM"]); 
	$pre_homepage						= SetStringFromDB($arr_info_history[0]["HOMEPAGE"]); 
	$pre_memo								= trim($arr_info_history[0]["MEMO"]); 
	$pre_dc_rate						= trim($arr_info_history[0]["DC_RATE"]); 
	$pre_sale_adm_no				= trim($arr_info_history[0]["SALE_ADM_NO"]);
	$pre_manager_nm					= SetStringFromDB($arr_info_history[0]["MANAGER_NM"]); 
	$pre_phone							= SetStringFromDB($arr_info_history[0]["PHONE"]); 
	$pre_hphone							= SetStringFromDB($arr_info_history[0]["HPHONE"]); 
	$pre_fphone							= SetStringFromDB($arr_info_history[0]["FPHONE"]); 
	$pre_email							= SetStringFromDB($arr_info_history[0]["EMAIL"]); 
	$pre_email_tf						= trim($arr_info_history[0]["EMAIL_TF"]); 
	$pre_contract_start			= trim($arr_info_history[0]["CONTRACT_START"]); 
	$pre_contract_end				= trim($arr_info_history[0]["CONTRACT_END"]); 
	$pre_is_mall						= trim($arr_info_history[0]["IS_MALL"]); 
	$pre_use_tf							= trim($arr_info_history[0]["USE_TF"]); 
	$pre_del_tf							= trim($arr_info_history[0]["DEL_TF"]); 
	$pre_reg_adm						= trim($arr_info_history[0]["REG_ADM"]); 
	$pre_reg_date						= trim($arr_info_history[0]["REG_DATE"]); 

	if ($pre_contract_start <> "0000-00-00") {
		$pre_contract_start = date("Y-m-d",strtotime($pre_contract_start));
	} else {
		$pre_contract_start = "";
	}

	if ($pre_contract_end <> "0000-00-00") {
		$pre_contract_end = date("Y-m-d",strtotime($pre_contract_end));
	} else {
		$pre_contract_end = "";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>

<style type="text/css">
<!--
#pop_table_scroll { z-index: 1;  overflow: auto; height: 368px; }
-->
</style>
<script language="javascript">

</script>

</head>
<body id="popup_work" >

<form name="frm" method="post">

<div id="popupwrap_work">
	<h1>수정 이력 조회</h1>
	<div id="postsch">
		<div class="addr_inp">
			<div class="sp10"></div>
			* 업체 정보
			<div class="sp5"></div>
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="12%">
					<col width="44%">
					<col width="44%">
				</colgroup>
				<thead>
					<?
						if ($rs_cp_cate <> $pre_cp_cate) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>카테고리</th>
						<td class="line">
							<?=getCategoryName($conn, $rs_cp_cate)?>
						</td>
						<td class="line">
							<?=getCategoryName($conn, $pre_cp_cate)?>
						</td>
					</tr>
					<?
						if ($rs_cp_nm <> $pre_cp_nm) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>업체명</th>
						<td>
							<?=$rs_cp_nm?>
						</td>
						<td>
							<?=$pre_cp_nm?>
						</td>
					</tr>
					<?
						if ($rs_cp_code <> $pre_cp_code) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>관리코드</th>
						<td>
							<?=$rs_cp_code?>
						</td>
						<td>
							<?=$pre_cp_code?>
						</td>
					</tr>
					<?
						if ($rs_cp_type <> $pre_cp_type) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>업체구분</th>
						<td>
							<?=getDcodeName($conn,"CP_TYPE",$rs_cp_type)?>
						</td>
						<td>
							<?=getDcodeName($conn,"CP_TYPE",$pre_cp_type)?>
						</td>
					</tr>
					<?
						if ($rs_dc_rate <> $pre_dc_rate) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>할인/수수율</th>
						<td>
							<?=$rs_dc_rate?>
						</td>
						<td>
							<?=$pre_dc_rate?>
						</td>
					</tr>
					<?
						if ($rs_biz_no <> $pre_biz_no) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>사업자	등록번호</th>
						<td>
							<?=$rs_biz_no?>
						</td>
						<td>
							<?=$pre_biz_no?>
						</td>
					</tr>
					<?
						if ($rs_ceo_nm <> $pre_ceo_nm) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>대표자명</th>
						<td>
							<?=$rs_ceo_nm?>
						</td>
						<td>
							<?=$pre_ceo_nm?>
						</td>
					</tr>
					<?
						if ($rs_cp_phone <> $pre_cp_phone) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>대표 전화번호</th>
						<td>
							<?=$rs_cp_phone?>
						</td>
						<td>
							<?=$pre_cp_phone?>
						</td>
					</tr>
					<?
						if ($rs_cp_fax <> $pre_cp_fax) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>대표 FAX</th>
						<td>
							<?=$rs_cp_fax?>
						</td>
						<td>
							<?=$pre_cp_fax?>
						</td>
					</tr>
					<?
						if ($rs_cp_zip.$rs_cp_addr <> $pre_cp_zip.$pre_cp_addr) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>주소 1</th>
						<td>
							[<?=$rs_cp_zip?>] <?=$rs_cp_addr?>
						</td>
						<td>
							[<?=$pre_cp_zip?>] <?=$pre_cp_addr?>
						</td>
					</tr>
					<?
						if ($rs_re_zip.$rs_re_addr <> $pre_re_zip.$pre_re_addr) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>주소 2</th>
						<td>
							[<?=$rs_re_zip?>] <?=$rs_re_addr?>
						</td>
						<td>
							[<?=$pre_re_zip?>] <?=$pre_re_addr?>
						</td>
					</tr>
					<?
						if ($rs_uptea <> $pre_uptea) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>업태</th>
						<td>
							<?=$rs_uptea?>
						</td>
						<td>
							<?=$pre_uptea?>
						</td>
					</tr>
					<?
						if ($rs_upjong <> $pre_upjong) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>종목</th>
						<td>
							<?=$rs_upjong?>
						</td>
						<td>
							<?=$pre_upjong?>
						</td>
					</tr>
				</thead>
			</table>
			<div class="sp10"></div>
			* 담당자 정보
			<div class="sp5"></div>
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="12%">
					<col width="44%">
					<col width="44%">
				</colgroup>
				<thead>
					<?
						if ($rs_manager_nm <> $pre_manager_nm) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>담당자 명</th>
						<td class="line">
							<?=$rs_manager_nm?>
						</td>
						<td class="line">
							<?=$pre_manager_nm?>
						</td>
					</tr>
					<?
						if ($rs_phone <> $pre_phone) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>전화번호</th>
						<td>
							<?=$rs_phone?>
						</td>
						<td>
							<?=$pre_phone?>
						</td>
					</tr>
					<?
						if ($rs_hphone <> $pre_hphone) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>휴대 전화번호</th>
						<td>
							<?=$rs_hphone?>
						</td>
						<td>
							<?=$pre_hphone?>
						</td>
					</tr>
					<?
						if ($rs_fphone <> $pre_fphone) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>FAX 번호</th>
						<td>
							<?=$rs_fphone?>
						</td>
						<td>
							<?=$pre_fphone?>
						</td>
					</tr>
					<?
						if ($rs_email <> $pre_email) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>이메일</th>
						<td>
							<?=$rs_email?>
						</td>
						<td>
							<?=$pre_email?>
						</td>
					</tr>
					<?
						if ($rs_email_tf <> $pre_email_tf) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>이메일 수신여부</th>
						<td>
							<?=$rs_email_tf?>
						</td>
						<td>
							<?=$pre_email_tf?>
						</td>
					</tr>
				</thead>
			</table>
			<div class="sp10"></div>
			* 기타 정보
			<div class="sp5"></div>
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="12%">
					<col width="44%">
					<col width="44%">
				</colgroup>
				<thead>
					<?
						if ($rs_sale_adm_no <> $pre_sale_adm_no) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>영업담당자</th>
						<td class="line">
							<?=getAdminName($conn, $rs_sale_adm_no);?>
						</td>
						<td class="line">
							<?=getAdminName($conn, $pre_sale_adm_no);?>
						</td>
					</tr>
					<?
						if ($rs_ad_type <> $pre_ad_type) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>결재구분</th>
						<td>
							<?=getDcodeName($conn,"AD_TYPE",$rs_ad_type)?>
						</td>
						<td>
							<?=getDcodeName($conn,"AD_TYPE",$pre_ad_type)?>
						</td>
					</tr>
					<?
						if ($rs_account_bank <> $pre_account_bank) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>거래은행</th>
						<td>
							<?=$rs_account_bank?>
						</td>
						<td>
							<?=$pre_account_bank?>
						</td>
					</tr>
					<?
						if ($rs_account <> $pre_account) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>계좌번호</th>
						<td>
							<?=$rs_account?>
						</td>
						<td>
							<?=$pre_account?>
						</td>
					</tr>
					<?
						if ($rs_account_owner_nm <> $pre_account_owner_nm) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>예금주</th>
						<td>
							<?=$rs_account_owner_nm?>
						</td>
						<td>
							<?=$pre_account_owner_nm?>
						</td>
					</tr>
					<?
						if ($rs_contract_start.$rs_contract_end <> $pre_contract_start.$pre_contract_end) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>계약 기간</th>
						<td>
							<?=$rs_contract_start?> ~ <?=$rs_contract_end?>
						</td>
						<td>
							<?=$pre_contract_start?> ~ <?=$pre_contract_end?>
						</td>
					</tr>
					<?
						if ($rs_homepage <> $pre_homepage) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>홈페이지</th>
						<td>
							<?=$rs_homepage?>
						</td>
						<td>
							<?=$pre_homepage?>
						</td>
					</tr>
					<?
						if ($rs_is_mall <> $pre_is_mall) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>인터넷몰 여부</th>
						<td>
							<?=$rs_is_mall?>
						</td>
						<td>
							<?=$pre_is_mall?>
						</td>
					</tr>
					<?
						if ($rs_memo <> $pre_memo) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>업체메모</th>
						<td>
							<?=nl2br($rs_memo)?>
						</td>
						<td>
							<?=nl2br($pre_memo)?>
						</td>
					</tr>
					<?
						if ($rs_use_tf <> $pre_use_tf) { 
							$bg_color = "bgcolor='yellow'";
						} else {
							$bg_color = "";
						}
					?>
					<tr <?=$bg_color?>>
						<th>사용여부</th>
						<td>
							<?=$rs_use_tf?>
						</td>
						<td>
							<?=$pre_use_tf?>
						</td>
					</tr>

				</thead>
			</table>



		</div>
		<div class="btn">
			<a href="javascript:self.close();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
		</div>

	</div>
	<br />
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>