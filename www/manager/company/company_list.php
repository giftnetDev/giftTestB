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
	$menu_right = "CP002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/confirm/confirm.php";

	function cal_misu($db, $CP_NO, $end_date){

		$query =   "SELECT
						CP_NO
						,IFNULL(SUM(DEPOSIT)-SUM(WITHDRAW),0) AS SUM_BALANCE
					FROM
						TBL_COMPANY_LEDGER
					WHERE
						DEL_TF =  'N'
						AND USE_TF =  'Y'
						AND INOUT_DATE <= '$end_date 23:59:59'
						AND CP_NO =  '$CP_NO'
					GROUP BY CP_NO
		";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		return $record[0]["SUM_BALANCE"];
	}
	function updateCompanyMisuAndMachulmisu($db, $cp_no, $misu, $machulmisu){
		$query="UPDATE TBL_COMPANY 
				SET MISU='$misu', MACHUL_MISU = '$machulmisu'
				WHERE CP_NO='$cp_no' ;";
		mysql_query($query, $db);
	}

	function cal_machulmisu($db, $cp_no, $start_date, $end_date){

		$query =   "SELECT
						O.CP_NO,
						O.SALE_ADM_NO,
						IFNULL(SUM(E.SUM_BALANCE), 0) AS TOTAL_SUM_BALANCE,
						IFNULL(A.PREV_BALANCE, 0) - IFNULL(C.SUM_COLLECT, 0) AS EXCEPT_SALE
					FROM(
						(SELECT CP_NO, CP_TYPE, SALE_ADM_NO
						FROM TBL_COMPANY
						WHERE USE_TF = 'Y' AND DEL_TF = 'N' AND CP_NO = '$cp_no'
						) O
					
						LEFT JOIN (
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS PREV_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF = 'N' AND USE_TF = 'Y' AND INOUT_DATE < '$start_date 00:00:00'
							AND CP_NO = '$cp_no'
							GROUP BY CP_NO
						) A ON O.CP_NO = A.CP_NO
					
						LEFT JOIN (
							SELECT CP_NO, IFNULL(SUM( WITHDRAW ), 0) AS SUM_COLLECT
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_TYPE IN ('대체', '대입', '입금', '지급') AND INOUT_DATE >= '$start_date 00:00:00' AND INOUT_DATE <= '$end_date 23:59:59'
							AND CP_NO = '$cp_no'
							GROUP BY CP_NO 
						) C ON O.CP_NO = C.CP_NO
					
						LEFT JOIN (
							SELECT CP_NO, IFNULL(SUM( DEPOSIT ) - SUM( WITHDRAW ), 0) AS SUM_BALANCE
							FROM TBL_COMPANY_LEDGER 
							WHERE DEL_TF =  'N' AND USE_TF = 'Y' AND INOUT_DATE <= '$end_date 23:59:59'
							AND CP_NO = '$cp_no'
							GROUP BY CP_NO 
						) E ON O.CP_NO = E.CP_NO
					)
					WHERE
						O.CP_TYPE IN ('판매', '판매공급')
						AND (IFNULL(A.PREV_BALANCE, 0) - IFNULL(C.SUM_COLLECT, 0) > 0)
		";

		//echo $query."<br/>";

		$result = mysql_query($query,$db);
		$record = array();

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		return $record[0]["EXCEPT_SALE"];
	}

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_cp_no = $chk_no[$k];
			
			$result = deleteCompany($conn, $s_adm_no, $str_cp_no);
		}
	}
	if($mode=="UPDATE_MISU"){
		// echo "<script>alert('test');</script>";
		// exit;
		$query="SELECT CP_NO 
				FROM TBL_COMPANY
				WHERE DEL_TF='N'
				AND USE_TF='Y'
				";
		$result=mysql_query($query, $conn);
		$record=array();
		if($result<>""){
			$cnt=mysql_num_rows($result);
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);

				$cpNo=$record[$i]["CP_NO"];
				$bal = cal_misu($conn, $cpNo, date("Y-m-d"));
				// echo $bal."<br>";
				if($bal<0){
					$misu=0;
					$machul_misu=$bal;
				}
				else{
					$misu=$bal;
					$machul_misu=0;
				}
				updateCompanyMisuAndMachulmisu($conn, $cpNo, $misu, $machul_misu);


			}//end of for(sizeof(TBL_COMPANY)
		}//end of if($result<>"")

	}//end of if($mode=="UPDATE_MISU);

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
	
	if ( count($_GET) == 0 && count($_POST) == 0 ) { 
		if($s_adm_md_tf == "Y")
			$sel_sale_adm_no = $s_adm_no;
	}
	
	#user_paramenter
	$con_cp_type = trim($con_cp_type);
	
	
	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$date_start			= trim($date_start);
	$date_end				= trim($date_end);
	
	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================
	$filter = array("exclude_category" => $exclude_category, "con_is_mall" => $con_is_mall, 'con_sale_adm_no' => $sel_sale_adm_no, 'con_cp_type' => $con_cp_type, 'con_ad_type' => $con_ad_type);
	
	$nListCnt =totalCntCompany($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sel_sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / (int)$nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	
	$first_day_of_this_month = new DateTime('first day of this month');
	$start_date = $first_day_of_this_month->format("Y-m-d");
	$end_date = date("Y-m-d",strtotime("0 month"));

	$arr_rs = listCompany($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sel_sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_sum = SumAccountReceivable($conn, $start_date, $end_date, $filter);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">
	function js_view_ledger(code) {
		window.open("/manager/confirm/company_ledger_list.php?cp_type=" + code + "&start_date=<?=$start_date?>&end_date=<?=$end_date?>",'_blank');
	}
	function js_view_account_receivable_list(code) {
		window.open("/manager/confirm/account_receivable_list.php?cp_type=" + code + "&start_date=<?=$start_date?>&end_date=<?=$end_date?>&search_field=CP_CODE&search_str=" + code,'_blank');
	}
	function js_view_account_receivable_report(code) {
		window.open("/manager/confirm/account_receivable_report.php?cp_type=" + code + "&start_date=<?=$start_date?>&end_date=<?=$end_date?>&search_field=CP_CODE&search_str=" + code,'_blank');
	}

	function js_write(){
		location.href = "company_write.php" ;
	}

	function js_view(rn, seq) {

		var frm = document.frm;
		frm.rn.value = rn;
		frm.cp_no.value = seq;
		frm.mode.value = "S";
		frm.method = "get";
		frm.action = "company_write.php";
		frm.submit();
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


	function js_exclude_category() {
		var frm = document.frm;
		
		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.exclude_category.value = frm.con_cate.value;
		frm.con_cate.value = "";

		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_excel() {

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('선택한 업체를 삭제 하시겠습니까?\n체크박스에 선택을 하셨어도 상품이나 주문 내역이 있을 경우 삭제 되지 않을 수 있습니다.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_batch_modify() {
		var url = "/manager/company/company_batch_modify.php";
		var frm = document.frm;
		NewWindow('about:blank', 'company_modify_batch_popup', '860', '513', 'YES');
		frm.mode.value = "";
		frm.target = "company_modify_batch_popup";
		frm.action = url;
		frm.submit();
	}
	function js_update_misu(){
		alert('testtest');
		var frm= document.frm;
		frm.mode.value="UPDATE_MISU";
		frm.target="";
		frm.action="<?=$_SERVER['PHP_SELF']?>";
		frm.submit();
	}
</script>
</head>

<body id="admin">
<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="">
<input type="hidden" name="cp_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">

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
?>

		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">

				<h2 style="margin:0;">업체 관리</h2>
				<div class="btnright" style="margin:0 0 5px;">
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
				</div>
				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="32%" />
					<col width="10%" />
					<col width="42%" />
					<col width="6%" />
				</colgroup>
				<tr>
					<th>카테고리</th>
					<td colspan="4">
						<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
						<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						<input type="button" value="제외" onclick="js_exclude_category();"/>
						<input type="hidden" name="exclude_category" value="<?=$exclude_category?>"/>
						<span class="exception">
						<?
							if($exclude_category <> "") { 
								$max_index = 0;
								while($max_index <= strlen($exclude_category)) {
											
									if($max_index > 2)
										echo " > ";
									echo getCategoryNameOnly($conn, left($exclude_category, $max_index));

									$max_index += 2;

								}
						?>
						
						<a href="#" id="clear_exclude_category">(X)</a>
						</span>
						<?

							}
						?>
					</td>
				</tr>
				<tr>
					<th>업체구분</th>
					<td>
						<?= makeSelectBox($conn,"CP_TYPE","con_cp_type","125","전체","",$con_cp_type)?>
					</td>
					<th>결제구분</th>
					<td colspan="2">
						<?= makeSelectBox($conn,"AD_TYPE","con_ad_type","125","전체","",$con_ad_type)?>
					</td>
				</tr>
				<tr>
					<th>밴더할인</th>
					<td>
						<input type="text" name="min_dc_rate" value="<?=$min_dc_rate?>" class="txt" />% ~ <input type="text" name="max_dc_rate" value="<?=$max_dc_rate?>" class="txt" />%
					</td>
					<th>영업담당</th>
					<td>
						<?= makeAdminInfoByMDSelectBox($conn,"sel_sale_adm_no"," style='width:70px;' ","영업사원","", $sel_sale_adm_no) ?>
						<!--
						<? if ($s_adm_md_tf != "Y") { ?>
							<?= makeAdminInfoByMDSelectBox($conn,"sel_sale_adm_no"," style='width:70px;' ","영업사원","", $sel_sale_adm_no) ?>
						<? } else { ?>
							<input type="hidden" name="sel_sale_adm_no" value="<?=$sel_sale_adm_no?>"/>
							<?=getAdminName($conn, $sel_sale_adm_no)?>
						<? } ?>
						-->
					</td>
				</tr>
				<tr>
					<th>필터</th>
					<td colspan="3">
						<b>인터넷몰여부:</b>
						<select name="con_is_mall">
							<option value="" <? if ($con_is_mall == "") echo "selected"; ?> >전체</option>
							<option value="N" <? if ($con_is_mall == "N") echo "selected"; ?> >일반,벤더</option>
							<option value="Y" <? if ($con_is_mall == "Y") echo "selected"; ?> >인터넷몰</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>정렬</th>
					<td>
						<select name="order_field" style="width:84px;">
							<option value="TRIM_CP_NAME" <? if ($order_field == "TRIM_CP_NAME") echo "selected"; ?> >업체명</option>
							<option value="CP_CODE" <? if ($order_field == "CP_CODE") echo "selected"; ?> >관리코드</option>
							<option value="MANAGER_NM" <? if ($order_field == "MANAGER_NM") echo "selected"; ?> >담당자명</option>
							<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
							<option value="SALE_ADM_NO" <? if ($order_field == "SALE_ADM_NO") echo "selected"; ?> >영업담당자</option>
							<option value="MISU"	<? if($order_field == "MISU") echo "selected"; ?> >미수</option>
							<option value="MACHUL_MISU" <?if($order_field == "MACHUL_MISU") echo "selected" ?> >매출미수</option>
						</select>&nbsp;&nbsp;
						<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
						<input type='radio' name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?>> 내림차순
					</td>

					<th>검색조건</th>
					<td>
						<select name="nPageSize" style="width:84px;">
							<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
							<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
							<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							<option value="200" <? if ($nPageSize == "200") echo "selected"; ?> >200개씩</option>
							<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
						</select>&nbsp;
						<select name="search_field" style="width:154px;">
							<option value="CP_CODE,CP_NM,CP_NM2,BIZ_NO,CEO_NM,CP_PHONE,CP_FAX,CP_ADDR,RE_ADDR,MANAGER_NM,EMAIL,PHONE,HPHONE,FPHONE" <? if ($search_field == "CP_CODE,CP_NM,CP_NM2,BIZ_NO,CEO_NM,CP_PHONE,CP_FAX,CP_ADDR,RE_ADDR,MANAGER_NM,EMAIL,PHONE,HPHONE,FPHONE") echo "selected"; ?> >전체</option>
							<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >관리코드</option>
							<option value="CP_NM" <? if ($search_field == "CP_NM") echo "selected"; ?> >업체명</option>
							<option value="CP_NM2" <? if ($search_field == "CP_NM2") echo "selected"; ?> >지점명</option>
							<option value="BIZ_NO" <? if ($search_field == "BIZ_NO") echo "selected"; ?> >사업자등록번호</option>
							<option value="CEO_NM" <? if ($search_field == "CEO_NM") echo "selected"; ?> >대표자명</option>
							<option value="CP_PHONE" <? if ($search_field == "CP_PHONE") echo "selected"; ?> >대표전화번호</option>
							<option value="CP_FAX" <? if ($search_field == "CP_FAX") echo "selected"; ?> >대표FAX</option>
							<option value="CP_ADDR" <? if ($search_field == "CP_ADDR") echo "selected"; ?> >주소1</option>
							<option value="RE_ADDR" <? if ($search_field == "RE_ADDR") echo "selected"; ?> >주소2</option>
							<option value="MANAGER_NM" <? if ($search_field == "MANAGER_NM") echo "selected"; ?> >담당자명</option>
							<option value="EMAIL" <? if ($search_field == "EMAIL") echo "selected"; ?> >담당자이메일</option>
							<option value="PHONE" <? if ($search_field == "PHONE") echo "selected"; ?> >담당전화번호</option>
							<option value="HPHONE" <? if ($search_field == "HPHONE") echo "selected"; ?> >담당휴대전화번호</option>
							<option value="FPHONE" <? if ($search_field == "FPHONE") echo "selected"; ?> >담당팩스</option>
							
							<option value="CP_NO" <? if ($search_field == "CP_NO") echo "selected"; ?> >*업체번호</option>
							<option value="MEMO" <? if ($search_field == "MEMO") echo "selected"; ?> >*업체메모</option>
						</select>&nbsp;

						<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
					</td>
					<td align="right">
						<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
					</td>
				</tr>
			</table>
			<div class="sp20"></div>
			총 <?=number_format($nListCnt)?> 건

				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="3%">
						<col width="5%">
						<col width="5%">
						<col width="*">
						<col width="11%">
						<col width="8%">
						<col width="10%">
						<col width="8%">
						<col width="6%">
						<col width="6%">
						<col width="6%">
						<col width="7%">
						<col width="6%">
						<col width="6%">

					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>업체번호</th>
							<th>관리코드</th>
							<th>업체명</th>
							<th>지점명</th>
							<th>담당자명</th>
							<th>연락처</th>
							<!-- <th>팩스</th> -->
							<!-- <th>업체구분</th> -->
							<!-- <th>결제구분</th> -->
							<th>업체구분</th>
							<th>밴더할인<br/>/수수율</th>
							<th>등록일</th>
							<th>영업담당자</th>
							<th>미수</th>
							<th>매출미수</th>
							<th class="end">원장확인</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
																
								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$CP_CODE				= trim($arr_rs[$j]["CP_CODE"]);
								$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]);
								$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]);
								$CEO_NM					= SetStringFromDB($arr_rs[$j]["CEO_NM"]);
								$CP_TYPE				= SetStringFromDB($arr_rs[$j]["CP_TYPE"]);
								$AD_TYPE				= SetStringFromDB($arr_rs[$j]["AD_TYPE"]);
								$SALE_ADM_NO		    = SetStringFromDB($arr_rs[$j]["SALE_ADM_NO"]);
								$MANAGER_NM			    = SetStringFromDB($arr_rs[$j]["MANAGER_NM"]);
								$CP_PHONE				= SetStringFromDB($arr_rs[$j]["CP_PHONE"]);
								$CP_FAX					= SetStringFromDB($arr_rs[$j]["CP_FAX"]);
								$PHONE					= SetStringFromDB($arr_rs[$j]["PHONE"]);
								$DC_RATE				= SetStringFromDB($arr_rs[$j]["DC_RATE"]);
								
								$CONTRACT_START	= trim($arr_rs[$j]["CONTRACT_START"]);
								$CONTRACT_END		= trim($arr_rs[$j]["CONTRACT_END"]);
								$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
								
								$CONTRACT_START = date("Y-m-d",strtotime($CONTRACT_START));
								$CONTRACT_END		= date("Y-m-d",strtotime($CONTRACT_END));
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								$SALE_ADM_NM = getAdminInfoNameMD($conn, $SALE_ADM_NO); 

								$memberID	=			trim(getMemberID($conn, $CP_NO));
								
								if($USE_TF == "N")
									$str_use_style = "unused";
								else
									$str_use_style = "";
					
					?>
						<tr class="<?=$str_use_style ?>" height="35" >
							<td>
								<input type="checkbox" name="chk_no[]" value="<?=$CP_NO?>">
							</td>
							
							<?	if($memberID != "")
							{	?>
								<td title="ID : <?=$memberID?>"><?=$CP_NO?></td>								
							<?
							}
							else{
							?>
								<td><?=$CP_NO?></td>
							<?
							}?>
							
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $CP_NO ?>');"><?=$CP_CODE?></a></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $CP_NO ?>');"><?= $CP_NM ?></a></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $CP_NO ?>');"><?= $CP_NM2 ?></a></td>
							<td><?= $MANAGER_NM ?></td>
							<td><?= $CP_PHONE ?></td>
							<td><?= getDcodeName($conn, "CP_TYPE", $CP_TYPE);?></td>
							<!-- <td><?// $CP_FAX ?></td> -->
							<!-- <td><?// getDcodeName($conn, "AD_TYPE", $AD_TYPE);?></td> -->
							<td><?= ($DC_RATE != "0" ? $DC_RATE."%" : "") ?></td>
							<td class="filedown"><?= $REG_DATE ?></td>
							<td><?= $SALE_ADM_NM ?></td>
							<?
							$this_sum_balance = number_format(cal_misu($conn, $CP_NO, $end_date));
							if($this_sum_balance<0) {
								$misu = 0;
								$machul_misu = $this_sum_balance;
							} else {
								$misu = $this_sum_balance;
								$machul_misu = 0;
							}
							?>
							<td><a href="javascript:js_view_account_receivable_list('<?=$CP_CODE?>')"><?=$misu;?></a></td>
							<td><a href="javascript:js_view_account_receivable_report('<?=$CP_CODE?>')"><?=$machul_misu;?></a></td>
							<td><input type="button" value="원장확인" onclick="js_view_ledger('<?=$CP_NO?>')"/></td>
						</tr>
					<?			
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="13">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
							</tbody>
						</table>

					<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
							<input type="button" value="미수/매출미수 갱신" onclick="js_update_misu();">
						<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
							<input type="button" name="aa" value=" 선택한 업체 삭제 " class="btntxt" onclick="js_delete();">
						<? } ?>
					</div>

					<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
							<input type="button" name="aa" value="선택한 업체내역 일괄변경" class="btntxt" onclick="js_batch_modify();">
					</div>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&con_cp_type=".$con_cp_type."&con_ad_type=".$con_ad_type."&date_start=".$date_start."&date_end=".$date_end."&min_dc_rate=".$min_dc_rate."&max_dc_rate=".$max_dc_rate."&sel_sale_adm_no=".$sel_sale_adm_no."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&con_is_mall=".$con_is_mall."&con_cate=".$con_cate."&exclude_category=".$exclude_category;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
			</div>
			<!-- // E: mwidthwrap -->
			<script>

				$(function(){

					$("#clear_exclude_category").click(function(e){

						e.preventDefault();
						$("input[name=exclude_category]").val('');
						$(".exception").html('');

						js_search();

					});
				});

			</script>
		</td>
	</tr>
	</table>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">▲ 위로</a>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
