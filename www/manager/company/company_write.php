<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CP002"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/counsel/counsel.php";

	function getMemberID($db, $cpNo){
		$query="SELECT MEM_ID
				FROM	TBL_MEMBER
				WHERE	CP_NO='$cpNo'
				AND		DEL_TF='N'
				AND		USE_TF='Y'
				
				";
		
		// echo $query;
		// exit;
		$result=mysql_query($query, $db);

		if($result<>""){
			$rows=mysql_fetch_array($result);

			return $rows[0];
		}
		return "";
	}//end of function

	
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
	$cp_nm2					= SetStringToDB($cp_nm2);
	$cp_code				= SetStringToDB($cp_code);
	$cp_phone				= SetStringToDB($cp_phone);
	$cp_hphone			    = SetStringToDB($cp_hphone);
	$cp_fax					= SetStringToDB($cp_fax);
	$cp_addr				= SetStringToDB($cp_addr);
	$re_addr				= SetStringToDB($re_addr);
	$homepage				= SetStringToDB($homepage);
	$biz_no					= SetStringToDB($biz_no);
	$ceo_nm					= SetStringToDB($ceo_nm);
	$upjong					= SetStringToDB($upjong);
	$uptea					= SetStringToDB($uptea);
	$sale_adm_no            = SetStringToDB($sale_adm_no);
	$manager_nm			    = SetStringToDB($manager_nm);
	$phone					= SetStringToDB($phone);
	$hphone					= SetStringToDB($hphone);
	$fphone					= SetStringToDB($fphone);
	$email					= SetStringToDB($email);
	$ad_type				= SetStringToDB($ad_type);
	$account_bank		    = SetStringToDB($account_bank);
	$account_owner_nm	    = SetStringToDB($account_owner_nm);
	
	$result	= false  ;

#====================================================================
# DML Process
#====================================================================
	
	if($is_mall == "on")
		$is_mall = 'Y';
	else
		$is_mall = 'N';
	
	if ($mode == "I") {
		$cp_cate = "";

		if ($gd_cate_01 <> "") {
			$cp_cate = $gd_cate_01;
		}
		if ($gd_cate_02 <> "") {
			$cp_cate = $gd_cate_02;
		}
		if ($gd_cate_03 <> "") {
			$cp_cate = $gd_cate_03;
		}
		if ($gd_cate_04 <> "") {
			$cp_cate = $gd_cate_04;
		}

		//����ȭ ������ �Է����� �� ���� �޾� ����
		$cp_code = getNextCPNo($conn);
		do{
			$chkboxTF=isset($_POST['chkRedanduncyCpNm']);
			echo "<script>console.log('test : ".$chkboxTF."');</script>";
			if(isCompanyNameRedundancy($conn, $cp_nm,$cp_nm2)==1  && $chkboxTF<>"1"){
				echo "<script>
						var isSave = confirm('��ü��� �������� �ߺ��Ǿ����ϴ�. �Է��� �� �����ϴ�');
					</script>";
				// echo"<script>console.log('DB Redanduncy');</script>";
				echo "redundacy<br/>";
				break;
			}
			$result =  insertCompany($conn, $cp_cate, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $memo, $is_mall, $use_tf, $s_adm_no);
	

		}while(false);
		
		$new_cp_no = $result;

		//���� �߰� ����Ʈ
		if (isset($arr_cp_option_nm)) 
		{
			
			for($j = 0; $j < sizeof($arr_cp_option_nm); $j ++) { 
				$t_cp_option_nm = $arr_cp_option_nm[$j]; 
				$t_cp_option_value = $arr_cp_option_value[$j]; 

				if($t_cp_option_nm == "" && $t_cp_option_value == "") continue;

				insertCompanyEtc($conn, $new_cp_no, $t_cp_option_nm, $t_cp_option_value);
			}
		}

	}

	if ($mode == "S") {
		//echo "<script>alert('mode S');</script>";

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

		$memberId							=trim(getMemberID($conn, $cp_no));
		

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

		$arr_company_etc = listCompanyEtc($conn, $cp_no);


	}

	if ($mode == "U") {
		$cp_cate = "";

		if ($gd_cate_01 <> "") {
			$cp_cate = $gd_cate_01;
		}
		if ($gd_cate_02 <> "") {
			$cp_cate = $gd_cate_02;
		}
		if ($gd_cate_03 <> "") {
			$cp_cate = $gd_cate_03;
		}
		if ($gd_cate_04 <> "") {
			$cp_cate = $gd_cate_04;
		}

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
		
		if ($rs_contract_start == "0000-00-00") $rs_contract_start = "";
		if ($rs_contract_end == "0000-00-00") $rs_contract_end = "";

		$str_changed_items = "";

		if (trim($rs_cp_cate) <> trim($cp_cate)) $str_changed_items = $str_changed_items."|ī�װ�(CP_CATE)|";
		if (trim($rs_cp_nm) <> trim($cp_nm)) $str_changed_items = $str_changed_items."|��ü��(CP_NM)|";
		if (trim($rs_cp_nm2) <> trim($cp_nm2)) $str_changed_items = $str_changed_items."|������(CP_NM2)|";
		if (trim($rs_cp_code) <> trim($cp_code)) $str_changed_items = $str_changed_items."|�����ڵ�(CP_CODE)|";
		if (trim($rs_cp_type) <> trim($cp_type)) $str_changed_items = $str_changed_items."|��ü����(CP_TYPE)|";
		if (trim($rs_ad_type) <> trim($ad_type)) $str_changed_items = $str_changed_items."|���籸��(AD_TYPE)|";
		if (trim($rs_cp_phone) <> trim($cp_phone)) $str_changed_items = $str_changed_items."|��ǥ��ȭ��ȣ(CP_PHONE)|";
		if (trim($rs_cp_fax) <> trim($cp_fax)) $str_changed_items = $str_changed_items."|��ǥFAX(CP_FAX)|";
		if (trim($rs_cp_zip) <> trim($cp_zip)) $str_changed_items = $str_changed_items."|�����ȣ1(CP_ZIP)|";
		if (trim($rs_cp_addr) <> trim($cp_addr)) $str_changed_items = $str_changed_items."|�ּ�1(CP_ADDR)|";
		if (trim($rs_re_zip) <> trim($re_zip)) $str_changed_items = $str_changed_items."|�����ȣ2(RE_ZIP)|";
		if (trim($rs_re_addr) <> trim($re_addr)) $str_changed_items = $str_changed_items."|�ּ�2(RE_ADDR)|";
		if (trim($rs_biz_no) <> trim($biz_no)) $str_changed_items = $str_changed_items."|����ڵ�Ϲ�ȣ(BIZ_NO)|";
		if (trim($rs_ceo_nm) <> trim($ceo_nm)) $str_changed_items = $str_changed_items."|��ǥ�ڸ�(CEO_NM)|";
		if (trim($rs_upjong) <> trim($upjong)) $str_changed_items = $str_changed_items."|����(UPJONG)|";
		if (trim($rs_uptea) <> trim($uptea)) $str_changed_items = $str_changed_items."|����(UPTEA)|";
		if (trim($rs_account_bank) <> trim($account_bank)) $str_changed_items = $str_changed_items."|�ŷ�����(ACCOUNT_BANK)|";
		if (trim($rs_account) <> trim($account)) $str_changed_items = $str_changed_items."|���¹�ȣ(ACCOUNT)|";
		if (trim($rs_account_owner_nm) <> trim($account_owner_nm)) $str_changed_items = $str_changed_items."|������(ACCOUNT_OWNER_NM)|";
		if (trim($rs_homepage) <> trim($homepage)) $str_changed_items = $str_changed_items."|Ȩ������(HOMEPAGE)|";
		if (trim($rs_memo) <> trim($memo)) $str_changed_items = $str_changed_items."|��ü�޸�(MEMO)|";
		if (trim($rs_dc_rate) <> trim($dc_rate)) $str_changed_items = $str_changed_items."|����/������(DC_RATE)|";
		if (trim($rs_sale_adm_no) <> trim($sale_adm_no)) $str_changed_items = $str_changed_items."|���������(SALE_ADM_NO)|";
		if (trim($rs_manager_nm) <> trim($manager_nm)) $str_changed_items = $str_changed_items."|����ڸ�(MANAGER_NM)|";
		if (trim($rs_phone) <> trim($phone)) $str_changed_items = $str_changed_items."|�������ȭ��ȣ(PHONE)|";
		if (trim($rs_hphone) <> trim($hphone)) $str_changed_items = $str_changed_items."|������޴���ȭ��ȣ(HPHONE)|";
		if (trim($rs_fphone) <> trim($fphone)) $str_changed_items = $str_changed_items."|�����FAX��ȣ(FPHONE)|";
		if (trim($rs_email) <> trim($email)) $str_changed_items = $str_changed_items."|�̸���(EMAIL)|";
		if (trim($rs_email_tf) <> trim($email_tf)) $str_changed_items = $str_changed_items."|�̸��ϼ��ſ���(EMAIL_TF)|";
		if (trim($rs_contract_start) <> trim($contract_start)) $str_changed_items = $str_changed_items."|���Ⱓ(CONTRACT_START)|";
		if (trim($rs_contract_end) <> trim($contract_end)) $str_changed_items = $str_changed_items."|���Ⱓ(CONTRACT_END)|";
		if (trim($rs_is_mall) <> trim($is_mall)) $str_changed_items = $str_changed_items."|���ͳݸ�����(IS_MALL)|";
		if (trim($rs_use_tf) <> trim($use_tf)) $str_changed_items = $str_changed_items."|��뿩��(USE_TF)|";
		
		if ($str_changed_items <> "") {
			// ��ü ���� �̷� ���
			$INS_DATE = date("Y-m-d H:i:s",strtotime("0 day"));

			$arr_data = array("CP_NO"=>$cp_no,
												"CP_CATE"=>$rs_cp_cate,
												"CP_CODE"=>$rs_cp_code,
												"CP_TYPE"=>$rs_cp_type,
												"CP_NM"=>$rs_cp_nm,
												"CP_NM2"=>$rs_cp_nm2,
												"CP_PHONE"=>$rs_cp_phone,
												"CP_HPHONE"=>$rs_cp_hphone,
												"CP_FAX"=>$rs_cp_fax,
												"CP_ZIP"=>$rs_cp_zip,
												"CP_ADDR"=>$rs_cp_addr,
												"RE_ZIP"=>$rs_re_zip,
												"RE_ADDR"=>$rs_re_addr,
												"HOMEPAGE"=>$rs_homepage,
												"BIZ_NO"=>$rs_biz_no,
												"CEO_NM"=>$rs_ceo_nm,
												"UPJONG"=>$rs_upjong,
												"UPTEA"=>$rs_uptea,
												"DC_RATE"=>$rs_dc_rate,
												"SALE_ADM_NO"=>$rs_sale_adm_no,
												"MANAGER_NM"=>$rs_manager_nm,
												"PHONE"=>$rs_phone,
												"HPHONE"=>$rs_hphone,
												"FPHONE"=>$rs_fphone,
												"EMAIL"=>$rs_email,
												"EMAIL_TF"=>$rs_email_tf,
												"CONTRACT_START"=>$rs_contract_start,
												"CONTRACT_END"=>$rs_contract_end,
												"AD_TYPE"=>$rs_ad_type,
												"ACCOUNT_BANK"=>$rs_account_bank,
												"ACCOUNT"=>$rs_account,
												"ACCOUNT_OWNER_NM"=>$rs_account_owner_nm,
												"MEMO"=>$rs_memo,
												"IS_MALL"=>$rs_is_mall,
												"CHANGED_ITEMS"=>$str_changed_items,
												"USE_TF"=>$rs_use_tf,
												"REG_ADM"=>$_SESSION["s_adm_no"],
												"REG_DATE"=>$INS_DATE
											);

			$result_history = insertCompanyHistory($conn, $arr_data);

		}

		$result = updateCompany($conn, $cp_cate, $cp_type, $cp_nm, $cp_nm2, $cp_code, $cp_phone, $cp_hphone, $cp_fax, $cp_zip, $cp_addr, $re_zip, $re_addr, $homepage, $biz_no, $ceo_nm, $upjong, $uptea, $dc_rate, $sale_adm_no, $manager_nm, $phone, $hphone, $fphone, $email, $email_tf, $contract_start, $contract_end, $ad_type, $account_bank, $account, $account_owner_nm, $is_mall, $memo, $use_tf, $s_adm_no, $cp_no);

		//���� �߰� ����Ʈ
		deleteCompanyEtc($conn, $cp_no);
		if (isset($arr_cp_option_nm)) 
		{
			
			for($j = 0; $j < sizeof($arr_cp_option_nm); $j ++) { 
				$t_cp_option_nm = $arr_cp_option_nm[$j]; 
				$t_cp_option_value = $arr_cp_option_value[$j]; 

				if($t_cp_option_nm == "" && $t_cp_option_value == "") continue;

				insertCompanyEtc($conn, $cp_no, $t_cp_option_nm, $t_cp_option_value);
			}
		}
	}

	if ($mode == "D") {
		$result = deleteCompany($conn, $s_adm_no, $cp_no);
		deleteCompanyEtc($conn, $cp_no);
	}

	//5�ڸ� ���� ��ü �ڵ�� �Է�
	if($rs_cp_code == "")
		$rs_cp_code = getNextCPNo($conn);

	
	$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&con_cp_type=".$con_cp_type."&sel_sale_adm_no=".$sel_sale_adm_no;
	
	if ($result) {
		
		if ($mode == "U") {
		//mode=="U"
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�. mode is <?=$mode?>');
		location.href="company_list.php<?=$strParam?>";
		//location.href =  "company_write.php<?=$strParam?>&mode=S&cp_no=<?=$cp_no?>";
</script>
<?
		} 
		else {	
?>	
<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�.22');
		location.href =  "company_list.php";
</script>
<?
		}
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
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<style type="text/css">
#pop_table_scroll { z-index: 1;  overflow: auto; width:95%; height: 250px; }
</style>
<script>

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
								
				if (document.getElementById("addr_type").value == "s") {
				  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
					document.getElementById("cp_zip").value = data.zonecode;
					//document.getElementById("cp_zip").value = data.postcode2;
					document.getElementById("cp_addr").value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById("cp_addr").focus();
				} else {
				  // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
					document.getElementById("re_zip").value = data.zonecode;
					//document.getElementById("re_zip").value = data.postcode2;
					document.getElementById("re_addr").value = fullAddr;
					// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
					document.getElementById("re_addr").focus();
				}


            }
        }).open();
    }

		function js_addr_open(s) {
			document.getElementById("addr_type").value = s;
			sample6_execDaumPostcode();
		}


	function js_pop_history_view(cp_no, history_no) {

		var url = "pop_company_compare.php?cp_no="+cp_no+"&history_no="+history_no;
		NewWindow(url, '�̷���ȸ', '1030', '600', 'yes');
	}

</script>  


<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>

<script language="javascript">
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_list() {
		//var frm = document.frm;
			
		//frm.method = "get";
		//frm.action = "company_list.php<?=$strParam?>";
		//frm.submit();

		document.location.href = "company_list.php<?=$strParam?>";
	}

	// ���� ��ư Ŭ�� �� 
	function js_save() {
		
		var cp_no = "<?= $cp_no ?>";
		var frm = document.frm;
		
		if (isNull(frm.cp_nm.value)) {
			alert('��ü���� �Է����ּ���.');
			frm.cp_nm.focus();
			return ;		
		}

		if (isNull(frm.cp_code.value)) {
			alert('�����ڵ带 �Է����ּ���.');
			frm.cp_code.focus();
			return ;		
		}

		if($(".msg").html() != "") {
			alert('���� �ڵ尡 �ߺ��Դϴ�.');
			frm.cp_code.focus();
			return ;	
		}
		
		if (frm.cp_type.value == "") {
			alert('��ü������ �������ּ���.');
			frm.cp_type.focus();
			return ;		
		}
			
		/*
		if (isNull(frm.biz_no.value)) {
			alert('����� ��Ϲ�ȣ�� �Է����ּ���.');
			frm.biz_no.focus();
			return ;		
		}

		if (isNull(frm.ceo_nm.value)) {
			alert('��ǥ�ڸ��� �Է����ּ���.');
			frm.ceo_nm.focus();
			return ;		
		}

		if (isNull(frm.cp_phone.value)) {
			alert('��ǥ ��ȭ��ȣ�� �Է����ּ���.');
			frm.cp_phone.focus();
			return ;		
		}

		if (isNull(frm.account_bank.value)) {
			alert('�ŷ������� �Է����ּ���.');
			frm.account_bank.focus();
			return ;		
		}

		if (isNull(frm.account.value)) {
			alert('���¹�ȣ�� �Է����ּ���.');
			frm.account.focus();
			return ;		
		}
		*/

		var res = frm.email.value.match(/[^0-9a-zA-Z-_.@,;]/gi);
		if (res != null) {
			alert('�̸��Ͽ��� ������ �ʴ� ��ȣ�� �ֽ��ϴ�. \n���� �̸����� �ƴҰ�� ���� ���� �߼��� �ȵǸ� �������� �����Ƕ��� �ּһ��̿� , �Ǵ� ; ��ȣ�� ����ϼ���.');
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

		if (isNull(cp_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}

		frm.method = "post";
		frm.action = "company_write.php";
		frm.submit();
	}

	//�����ȣ ã��
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '�����ȣã��', '390', '370', 'NO');
	}

	/**
	* ���� ÷�ο� ���� ���ÿ� ���� ����÷�� �Է¶� visibility ����
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

	function js_pop_company_extra() { 

		var frm = document.frm;
		
		var url = "pop_company_extra.php?cp_no="+frm.cp_no.value;

		NewWindow(url, 'pop_company_extra','1000','600','YES');
	}

	function bizNoCk(obj) 
	{ 
		var number = obj.value.replace(/[^0-9]/g, ""); 
		var bizno = ""; 
		if(number.length < 4) 
		{
			return number; 
		} 
		else if(number.length < 6) 
		{
			bizno += number.substr(0, 3); 
			bizno += "-"; 
			bizno += number.substr(3, 2); 
		} 
		else if(number.length < 11) 
		{
			bizno += number.substr(0, 3); 
			bizno += "-"; 
			bizno += number.substr(3, 2); 
			bizno += "-"; 
			bizno += number.substr(5); 
		} 
		else 
		{
			bizno += number.substr(0, 3); 
			bizno += "-"; 
			bizno += number.substr(3, 2); 
			bizno += "-"; 
			bizno += number.substr(5); 
		} 
		
		obj.value = bizno; 
	}


</script>
<script>
	$(document).ready(function(){
		$('select[name=cp_type]').change(function(){
			var cpType=$('select[name=cp_type]').val();
			if(cpType=="����"){
				$('select[name=ad_type]').val("��ð���").prop("checked",true);
			}
		});
	});
	
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="<?= $cp_no?>">
<input type="hidden" name="con_cp_type" value="<?= $con_cp_type?>">
<input type="hidden" name="date_start" value="<?= $date_start ?>">
<input type="hidden" name="date_end" value="<?= $date_end ?>">
<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
<input type="hidden" name="sel_sale_adm_no" value="<?=$sel_sale_adm_no?>">
<input type="hidden" name="addr_type" id="addr_type" value="">

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

	require "../../_common/left_area.php";
	include_once('../../_common/editor/func_editor.php');

?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>��ü ����</h2>  
				* ��ü ����
				<?
					if($memberId<>""){
						?>
						<span style="font-weight:900;">ID : <?=$memberId?></span>
						<?
					}
					else{
					?>
						
					<?
					}
				?>
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>ī�װ�</th>
							<td colspan="3" class="line">
								<?= makeCategorySelectBoxOnChange($conn, $rs_cp_cate, $exclude_category);?>
							</td>
						</tr>
						<tr>
							<th>��ü��</th>
							<td>
								<input type="text" name="cp_nm" value="<?= $rs_cp_nm ?>" style="width:60%;" itemname="��ü��" required class="txt">
								<input type="text" name="cp_nm2" value="<?= $rs_cp_nm2 ?>" style="width:30%;" itemname="������" class="txt">
								<br/>
								<label>ȸ���̸� �ߺ� ���
								<input type="checkbox" name="chkRedanduncyCpNm" value="test" title="��ü�� �ߺ����"></label>
							</td>
							<th>�����ڵ�</th>
							<td>
								<input type="text" name="cp_code" value="<?= $rs_cp_code ?>" style="width:20%;" itemname="�����ڵ�" required class="txt">
								<span class="msg" style="color:red;" data-cp_code="<?= $rs_cp_code ?>"></span>
								<script>
								$(function(){

									$("input[name=cp_code]").keyup(function(){

										//alert($(this).val());
										var cp_code = $(this).val();
										$.getJSON( "../company/json_company_list.php?cp_code=" + cp_code, function(data) {
											if(data[0].CNT > 0 && $(".msg").data("cp_code") != cp_code) {
												$(".msg").html('�̹� ���� �ڵ尡 ��ϵǾ� �ֽ��ϴ�');
											} else 
												$(".msg").html('');
										});

									});

								});
								</script>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>��ü����</th>
							<td>
								<?= makeSelectBox($conn,"CP_TYPE","cp_type","125","����","",$rs_cp_type)?>
							</td>
							<th title="�Ǹ�:�ǸŰ��� �������� ����, ī��:�������� ����">����/������</th>
							<td>
								<input type="Text" name="dc_rate" value="<?= $rs_dc_rate?>" value="" style="width:70px;" class="txt"> %
							</td>
						</tr>
						<tr>
							<th>�����	��Ϲ�ȣ</th>
							<td>
								<!--<input type="Text" name="biz_no" value="<?= $rs_biz_no?>" style="width:140px;" itemname="�����	��Ϲ�ȣ" class="txt"> '-' �����Ͽ� �Է��� �ּ���-->
								<input type="text" name="biz_no" value="<?= $rs_biz_no?>" style="width:140px;" itemname="�����	��Ϲ�ȣ" class="txt" onKeyup="bizNoCk(this);return isPhoneNumber(this);" maxlength="12"  > '-' �����Ͽ� �Է��� �ּ���
							</td>
							<th>��ǥ�ڸ�</th>
							<td><input type="Text" name="ceo_nm" value="<?= $rs_ceo_nm ?>" style="width:30%;" itemname="��ǥ�ڸ�" class="txt"></td>
						</tr>
						<tr>
							<th>��ǥ ��ȭ��ȣ</th>
							<td>
								<input type="Text" name="cp_phone" value="<?= $rs_cp_phone?>" style="width:120px;" itemname="��ǥ ��ȭ��ȣ" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>��ǥ FAX</th>
							<td>
								<input type="Text" name="cp_fax" value="<?= $rs_cp_fax?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>
						<tr>
							<th>�ּ� 1</th>
							<td colspan="3">
								<input type="Text" name="cp_zip" id="cp_zip" value="<?= $rs_cp_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="cp_addr" id="cp_addr" value="<?= $rs_cp_addr?>" style="width:65%;" class="txt">
								<a href="#none" onClick="js_addr_open('s');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
							</td>
						<tr>
						<tr>
							<th>�ּ� 2</th>
							<td colspan="3">
								<input type="Text" name="re_zip" id="re_zip" value="<?= $rs_re_zip?>" style="width:60px;" maxlength="7" class="txt">
								<input type="Text" name="re_addr" id="re_addr" value="<?= $rs_re_addr?>" style="width:65%;" class="txt">
								<a href="#none" onClick="js_addr_open('r');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
							</td>
						<tr>
						<tr>
							<th>����</th>
							<td><input type="Text" name="uptea" value="<?= $rs_uptea?>" style="width:40%;" class="txt"></td>
							<th>����</th>
							<td><input type="Text" name="upjong" value="<?= $rs_upjong?>" style="width:65%;" class="txt"></td>
						</tr>
					</tbody>
				</table>
				<div class="sp15"></div>
				* ����� ����
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tbody>
						<tr>
							<th>����� ��</th>
							<td><input type="Text" name="manager_nm" value="<?= $rs_manager_nm ?>" style="width:90%;" class="txt"></td>
							<th>��ȭ��ȣ</th>
							<td>
								<input type="Text" name="phone" value="<?= $rs_phone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						</tr>
						<tr>
							<th>�޴� ��ȭ��ȣ</th>
							<td>
								<input type="Text" name="hphone" value="<?= $rs_hphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
							<th>FAX ��ȣ</th>
							<td>
								<input type="Text" name="fphone" value="<?= $rs_fphone ?>" style="width:120px;" class="txt" onkeyup="return isPhoneNumber(this)">
							</td>
						<tr>
						<tr>
							<th>�̸���</th>
							<td><input type="Text" name="email" value="<?= $rs_email ?>" style="width:90%;" class="txt"></td>
							<th>�̸��� ���ſ���</th>
							<td>
								<input type="radio" name="rd_email_tf" value="Y" <? if (($rs_email_tf =="Y") || ($rs_email_tf =="")) echo "checked"; ?>> ����<span style="width:20px;"></span>
								<input type="radio" name="rd_email_tf" value="N" <? if ($rs_email_tf =="N") echo "checked"; ?>> �̼���</td>
								<input type="hidden" name="email_tf" value="">
							</td>
						<tr>
					</tbody>
				</table>
				<div class="sp15"></div>
				* ��Ÿ ����
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tbody>
			 		    <tr>
							<th>���������</th>
							<td>
								<?=makeAdminInfoByMDSelectBox($conn, "sale_adm_no" ," style='width:100px;' ","����","",$rs_sale_adm_no)?>
							</td>
							<th>���籸��</th>
							<td>
								<?= makeSelectBox($conn,"AD_TYPE","ad_type","125","����","����",$rs_ad_type)?>
					
							</td>
						</tr>
						<tr>
							<th>�ŷ�����</th>
							<td><input type="Text" name="account_bank" value="<?= $rs_account_bank?>" itemname="�ŷ�����" style="width:40%;" class="txt"></td>
							<th>���¹�ȣ</th>
							<td><input type="Text" name="account" value="<?= $rs_account?>" style="width:40%;" itemname="���¹�ȣ" class="txt" onkeyup="return isPhoneNumber(this)"></td>
						</tr>
						<tr>
							<th>������</th>
							<td><input type="Text" name="account_owner_nm" value="<?= $rs_account_owner_nm?>" style="width:40%;" itemname="������"  class="txt"></td>
							<th>��� �Ⱓ</th>
							<td class="lpd20 right">
								<input name="contract_start" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $rs_contract_start ?>"> ~ 
								<input name="contract_end" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $rs_contract_end ?>">
							</td>
						</tr>
						<tr>
							<th>Ȩ������</th>
							<td>
								<input type="Text" name="homepage" value="<?= $rs_homepage?>" style="width:90%;" class="txt">
							</td>
							<th title="1. ���������� 2. �Ǹž�ü��� ������ǥ��">���ͳݸ� ����</th>
							<td>
								<? if ($s_adm_cp_type <> "�") { ?>
									<?= ($rs_is_mall == "Y" ? "��" : "�ƴϿ�" )?>
								<? } else { ?>
									<input type="checkbox" name="is_mall" <?= ($rs_is_mall == "Y" ? "checked" : "" )?>>
								<? } ?>
								
							</td>
							
						</tr>
						<tr>
							<th>��ü�޸�</th>
							<td colspan="3" class="memo">
								<textarea style="width:75%; height:160px;" name="memo"><?= str_replace("char(13)", "<br/>", $rs_memo) ?></textarea>
							</td>
						</tr>
						<tr>
							<th>��ü ��Ÿ ����</th>
							<td colspan="3" class="add_here">
								<? 
									
									for($o = 0; $o < sizeof($arr_company_etc); $o ++) { 
										$rs_cp_option_nm = $arr_company_etc[$o]["CP_OPTION_NM"];
										$rs_cp_option_value = $arr_company_etc[$o]["CP_OPTION_VALUE"];
										
								?>
									<div class="options">
										<?=makeSelectBoxOnChange($conn,"COMPANY_ETC", "arr_cp_option_nm[]","100", "�����ϼ���", "", $rs_cp_option_nm)?>
										<input type="text" name="arr_cp_option_value[]" value="<?=$rs_cp_option_value?>" placeholder="�ɼ� �󼼳���" />
										<input type="button" name="b" onclick="js_append_option(this);" value="�߰�" />
										<input type="button" name="b" onclick="js_delete_option(this);" value="����" />
									</div>
								<? } ?>
								<div class="options">
									<?=makeSelectBoxOnChange($conn,"COMPANY_ETC", "arr_cp_option_nm[]","100", "�����ϼ���", "", "")?>
										<input type="text" name="arr_cp_option_value[]" value="" placeholder="�ɼ� �󼼳���" />
									<input type="button" name="b" onclick="js_append_option(this);" value="�߰�" />
									<input type="button" name="b" onclick="js_delete_option(this);" value="����" />
								</div>
							</td>
						</tr>
						<script>
							function js_append_option(elem) { 
								var copied = $(elem).closest(".options").clone();
								copied.find("input[type=select]").val('');
								copied.find("input[type=text]").val('');
								$(".add_here").append(copied);
							}

							function js_delete_option(elem) {
								$(elem).closest(".options").remove();
							}
						</script>
						<tr>
							<th>��뿩��</th>
							<td colspan="3">
								<input type="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> ���<span style="width:20px;"></span>
								<input type="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> �̻��
								<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
							</td>
						</tr>
					</tbody>
				</table>
				
				<div class="sp20"></div>
				<div style="width:95%;">
					<div style="float:left;">* �߰� �ּ� ����</div>
					<div style="float:right;"><a href="javascript:js_pop_company_extra();" style="text-decoration:underline;">���</a></div>
				</div>
				
				<div class="sp5"></div>
				<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
				<colgroup>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="*" />
					<col width="5%" />
				</colgroup>
				<thead>
					<tr>
						
						<th>����</th>
						<th>������</th>
						<th>����ó</th>
						<th>�޴�����ȣ</th>
						<th>�ּ�</th>
						<th class="end"></th>
					</tr>
				</thead>
				<tbody>
				
				<?
					$arr_rs = listCompanyExtra($conn, $cp_no);
					if(sizeof($arr_rs) >= 1) {
						for($i = 0; $i < sizeof($arr_rs); $i ++) { 

							//MANAGER_NM, PHONE, HPHONE, ADDR, MEMO

							$CE_NO					= trim($arr_rs[$i]["CE_NO"]);
							$EXT_MANAGER_NM			= trim($arr_rs[$i]["MANAGER_NM"]);
							$EXT_PHONE				= trim($arr_rs[$i]["PHONE"]);
							$EXT_HPHONE				= trim($arr_rs[$i]["HPHONE"]);
							$EXT_ADDR				= trim($arr_rs[$i]["ADDR"]);
							$EXT_MEMO				= trim($arr_rs[$i]["MEMO"]);
				?>
					<tr height="35" >
						
						<td><?=$EXT_MEMO?></td>
						<td><?=$EXT_MANAGER_NM?></td>
						<td><?=$EXT_PHONE?></td>
						<td><?=$EXT_HPHONE?></td>
						<td><?=$EXT_ADDR?></td>
						<td><a href="javascript:js_pop_company_extra();" style="text-decoration:underline;">����</a></td>
					</tr>
				
				<?
						}
					} else {

				?>
					<tr>
						<td colspan="6" height="50" align="center">�����Ͱ� �����ϴ�</td>
					</tr>
				<?
					}
				?>
				
				</tbody>
				</table>
				
				<div class="sp20"></div>
				<div class="btnright">
				<? if ($cp_no <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					  <a href="javascript:js_save();"><img src="../images/save.png" alt="Ȯ��" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
						 <a href="javascript:js_save();"><img src="../images/save.png" alt="Ȯ��" /></a>
					<? } ?>
				<? }?>

				<? if ($s_adm_cp_type == "�") { ?>
					<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="���" /></a>
				<? } ?>
				
				<? if ($s_adm_cp_type == "�") { ?>
				<? if ($adm_no <> "") {?>
					<? if ($sPageRight_D == "Y") {?>
					  <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="����" /></a>
					<? } ?>
				<? } ?>
				<? } ?>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->



				<div class="sp20"></div>
				<div style="width:95%;">
					<div style="float:left;">* ���� �̷� ��ȸ</div>
				</div>
				
				<div class="sp5"></div>

				<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
				<colgroup>
					<col width="12%" />
					<col width="*" />
					<col width="17%" />
				</colgroup>
				<thead>
					<tr>
						<th>������</th>
						<th>������ �׸�</th>
						<th class="end">����������</th>
					</tr>
				</thead>
				</table>
				<div id="pop_table_scroll">
				<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
				<colgroup>
					<col width="14%" />
					<col width="*" />
					<col width="5%" />
				</colgroup>
				<tbody>
				<?
					$arr_rs = listCompanyHistory($conn, $cp_no);
					
					if(sizeof($arr_rs) >= 1) {
						for($i = 0; $i < sizeof($arr_rs); $i ++) { 

							$CP_NO					= trim($arr_rs[$i]["CP_NO"]);
							$HISTORY_NO			= trim($arr_rs[$i]["HISTORY_NO"]);
							$CHANGED_ITEMS	= trim($arr_rs[$i]["CHANGED_ITEMS"]);
							$REG_DATE				= trim($arr_rs[$i]["REG_DATE"]);
							$REG_ADM				= trim($arr_rs[$i]["REG_ADM"]);

							$CHANGED_ITEMS	= str_replace("||",",", $CHANGED_ITEMS);
							$CHANGED_ITEMS	= str_replace("|","", $CHANGED_ITEMS);

							$REG_ADM_NM = getAdminName($conn, $REG_ADM); 
				?>
					<tr height="35" >
						
						<td><a href="javascript:js_pop_history_view('<?=$CP_NO?>', '<?=$HISTORY_NO?>')"><?=$REG_DATE?></a></td>
						<td style="text-align:left"><a href="javascript:js_pop_history_view('<?=$CP_NO?>', '<?=$HISTORY_NO?>')"><?=$CHANGED_ITEMS?></a></td>
						<td><?=$REG_ADM_NM?></td>
					</tr>
				
				<?
						}
					} else {

				?>
					<tr>
						<td colspan="6" height="50" align="center">�����Ͱ� �����ϴ�</td>
					</tr>
				<?
					}
				?>
				</tbody>
				</table>
				</div>
				
				<div class="sp20"></div>
				<div style="width:95%;">
					<div style="float:left;">* ��� �̷� ��ȸ</div>
				</div>
				
				<div class="sp5"></div>

				<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
				<colgroup>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="35%" />
					<col width="35%" />
				</colgroup>
				<thead>
					<tr>
						<th>�����</th>
						<th>�������</th>
						<th>�����</th>
						<th>��㳻��</th>
						<th class="end">�亯</th>
					</tr>
				</thead>
				</table>
				<div id="pop_table_scroll">
				<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
				<colgroup>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="35%" />
					<col width="35%" />
				</colgroup>
				<tbody>
				<?
					$counsel_arr_rs = listCounselForCompanyWrite($conn,$cp_no);
					
					if(sizeof($counsel_arr_rs) >= 1) {
						for($i = 0; $i < sizeof($counsel_arr_rs); $i ++) { 
							$TEMP_SEQ_NO = trim($counsel_arr_rs[$i]["SEQ_NO"]);
							$TEMP_COUNSEL_DATE = trim($counsel_arr_rs[$i]["COUNSEL_DATE"]);
							$TEMP_COUNSEL_TYPE = trim($counsel_arr_rs[$i]["COUNSEL_TYPE"]);
							$TEMP_ASK = trim($counsel_arr_rs[$i]["ASK"]);
							$TEMP_ANSWER = trim($counsel_arr_rs[$i]["ANSWER"]);
							$TEMP_MANAGER_NM = trim($counsel_arr_rs[$i]["MANAGER_NM"]);
				?>
					<tr height="35" >
						<td><?=$TEMP_COUNSEL_DATE?></td>
						<td><?=$TEMP_COUNSEL_TYPE?></td>
						<td><?=$TEMP_MANAGER_NM?></td>
						<td><?=$TEMP_ASK?></td>
						<td><?=$TEMP_ANSWER?></td>
						<!-- <a href="javascript:js_pop_history_view('<?=$CP_NO?>', '<?=$HISTORY_NO?>')"><?=$REG_DATE?></a> -->
					</tr>
				
				<?
						}
					} else {
				?>
					<tr>
						<td colspan="6" height="50" align="center">�����Ͱ� �����ϴ�</td>
					</tr>
				<?
					}
				?>
				</tbody>
				</table>
				</div>
				
				<div class="sp30"></div>

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
<script>
	function js_redundany_confirm(){
		var ans=confirm('ȸ�� �̸��� �ߺ��Դϴ�. ����Ͻðڽ��ϱ�?');
		if(ans==true){
			return 1;
		}
		else return 0;
	}
	function js_test(){
		$.ajax({
			url:'/manager/ajax_processing.php',
			dataType:'text',
			type:'POST',
			data:{
				'mode':"CONFRIM_TO_ALLOW_REDUNDANCY_CP_NM",
				'cp_cate':cp_cate, 
				'cp_type':cp_types
			}

		});
	}
</script>