<?session_start();?>
<?
# =============================================================================
# File Name    : goods_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
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
	$menu_right = "GD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

	// 공급 업체 인경우
	if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
		$con_cate_03 = $s_adm_com_code;
	}

	if ($mode == "T") {
		updateGoodsUseTF($conn, $use_tf, $s_adm_no, $goods_no);
	}

	if ($mode == "SU") {
		$row_cnt = count($chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no = $chk_no[$k];
			$result = updateStateGoods($conn, $goods_state_mod, $str_goods_no, $s_adm_no);
		}
	}

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			if (isSaleGoods($conn, $str_goods_no)) {
				
			} else {
				$result = deleteGoods($conn, $str_goods_no, $s_adm_no);
			}
		}
		
	}

	if ($mode == "C") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			$result = copyGoods($conn, $str_goods_no, $s_adm_no);
		
		}
		
	}

#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-12 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

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

	$nListCnt =totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);
	
	/*
	echo $con_cate."<br>";
	echo $start_date."<br>";
	echo $end_date."<br>";
	echo $start_price."<br>";
	echo $end_price."<br>";
	echo $con_cate_01."<br>";
	echo $con_cate_02."<br>";
	echo $con_cate_03."<br>";
	echo $con_cate_04."<br>";
	echo $con_tax_tf."<br>";
	echo $con_use_tf."<br>";
	echo $del_tf."<br>";
	echo $search_field."<br>";
	echo $search_str."<br>";
	*/
	#echo sizeof($arr_rs);

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04;

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" >


	function js_write() {
		document.location.href = "goods_write.php";
	}

	function js_view(rn, goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "post";
		frm.action = "goods_write.php";
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

		if (frm.gd_cate_03 != null) {
			frm.con_cate_03.value = frm.cp_type.value;
		}

		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(goods_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('사용 여부를 변경 하시겠습니까?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.goods_no.value = goods_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_excel() {
		
		//alert("준비중 입니다..");
		//return;

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

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

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품을 삭제 하시겠습니까?\n체크박스에 선택을 하셨어도 상품 판매 내역이 있을 경우 삭제 되지 않을 수 있습니다.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_state_mod() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품 판매 상태를 변경 하시겠습니까?\n');
		
		if (bDelOK==true) {
			
			frm.mode.value = "SU";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}



	function js_copy() {
		var frm = document.frm;

		bDelOK = confirm('선택한 상품을 복사 하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_layer_show(goods_no) {
		document.getElementById("l_"+goods_no).style.display = "block";
	}

	function js_layer_hide(goods_no) {
		document.getElementById("l_"+goods_no).style.display = "none";
	}

</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->

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

				<h2>상품 관리</h2>
				<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a>
				<? } ?>
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<thead>
					<tr>
						<th>카테고리</th>
						<td colspan="4">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>등록일</th>
						<td colspan="4">
							<input type="text" class="txt" style="width: 70px;" name="start_date" value="<?=$start_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.start_date', document.frm.start_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>  ~ 

							<input type="text" class="txt" style="width: 70px;" name="end_date" value="<?=$end_date?>" maxlength="10" readonly="1" />
							<a href="javascript:show_calendar('document.frm.end_date', document.frm.end_date.value);" onFocus="blur();"><!--
						--><img src="/manager/images/bu/ic_calendar.gif" alt="" /></a>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
					</tr>
					<tr>
						<th>판매상태</th>
						<td>
							<?= makeSelectBox($conn,"GOODS_STATE","con_cate_04","125","선택","",$con_cate_04)?>
						</td>
						<th>과세구분</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","선택","",$con_tax_tf)?>
						</td>
					</tr>

					<? if ($s_adm_cp_type == "운영") { ?>
					<tr>
						<th>판매가</th>
						<td>
							<input type="text" value="<?=$start_price?>" name="start_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원 ~
							<input type="text" value="<?=$end_price?>" name="end_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> 원
						</td>
						<th>공급업체</th>
						<td colspan="2">
							<?= makeCompanySelectBox($conn, '구매', $con_cate_03);?>
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
						</td>
					</tr>
					<? } else { ?>
						<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
						<input type="hidden" name="cp_type" value="">
					<? } ?>

					<tr>
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >등록일</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
								<option value="BUY_PRICE" <? if ($order_field == "BUY_PRICE") echo "selected"; ?> >매입가</option>
								<? if ($s_adm_cp_type == "운영") { ?>
								<option value="SALE_PRICE" <? if ($order_field == "SALE_PRICE") echo "selected"; ?> >판매가</option>
								<option value="CP_NAME" <? if ($order_field == "CP_NAME") echo "selected"; ?> >공급업체</option>
								<? } ?>
								<option value="STOCK_CNT" <? if ($order_field == "STOCK_CNT") echo "selected"; ?> >재고</option>
								<option value="UP_DATE" <? if ($order_field == "UP_DATE") echo "selected"; ?> >수정일</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 오름차순 &nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 내림차순
						</td>
						<th>검색조건</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >상품번호</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >상품코드</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>

				총 <?=number_format($nListCnt)?> 건
				<table cellpadding="0" cellspacing="0" class="rowstable">
					

					<? if ($s_adm_cp_type == "운영") { ?>
					<colgroup>
						<col width="3%" />
						<col width="5%" />
						<col width="7%" />
						<col width="10%" />
						<col width="25%"/>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="5%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>공급사</th>
							<!--<th>사이즈</th>-->
							<th>매입가</th>
							<th>판매가</th>
							<!--<th>관리비</th>-->
							<th>등록일</th>
							<th>재고</th>
							<th class="end">사용여부</th>
						</tr>
					</thead>
					<tbody>
					<? } ?>

					<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
					<colgroup>
						<col width="5%" />
						<col width="7%" />
						<col width="10%" />
						<col width="38%"/>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="5%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>상품번호</th>
							<th>이미지</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>공급사</th>
							<!--<th>사이즈</th>-->
							<th>매입가</th>
							<!--<th>관리비</th>-->
							<th>등록일</th>
							<th>재고</th>
							<th class="end">사용여부</th>
						</tr>
					</thead>
					<tbody>
					<? } ?>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							#GOODS_NO, GOODS_TYPE, GOODS_CODE, GOODS_NAME, GOODS_SUB_NAME, CATE_01, CATE_02, CATE_03, CATE_04,
							#PRICE, SALE_PRICE, EXTRA_PRICE, FILE_NM, FILE_RNM, FILE_PATH, FILE_SIZE, FILE_EXT, CONTENTS,
							#READ_CNT, DISP_SEQ, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, ADM_NO, UP_DATE, DEL_ADM, DEL_DATE

							$rn								= trim($arr_rs[$j]["rn"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME		= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
							$PRICE						= trim($arr_rs[$j]["PRICE"]);
							$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
							$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$IMG_URL					= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM					= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM					= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT					= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150		= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150		= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$CONTENTS					= trim($arr_rs[$j]["CONTENTS"]);
							$READ_CNT					= trim($arr_rs[$j]["READ_CNT"]);
							$DISP_SEQ					= trim($arr_rs[$j]["DISP_SEQ"]);
							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF						= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);

							$str_goods_no = $GOODS_TYPE.substr("000000".$GOODS_NO,-5);

							//echo $IMG_URL;

							// 이미지가 저장 되어 있을 경우
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>사용중</font>";
							} else {
								$STR_USE_TF = "<font color='red'>사용안함</font>";
							}

							if ($TAX_TF == "비과세") {
								$STR_TAX_TF = "<font color='orange'>(비과세)</font>";
							} else {
								$STR_TAX_TF = "<font color='navy'>(과세)</font>";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							
				
				?>


					<? if ($s_adm_cp_type == "운영") { ?>
						<tr>
							<td>
								<input type="checkbox" name="chk_no[]" value="<?=$GOODS_NO?>">
							</td>
							<td>
								<div id="l_<?=$GOODS_NO?>" style="position:absolute; background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; left:410px; text-align:left; display:none;" >
								<?
									$arr_rs_sale = listCompanySale($conn, $GOODS_NO, 'YES');

									if (sizeof($arr_rs_sale) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs_sale); $k++) {
											$CP_NO					= trim($arr_rs_sale[$k]["CP_NO"]);
											$CP_NM					= trim($arr_rs_sale[$k]["CP_NM"]);
								?>
								<?=$CP_NM?></br>
								<?
											}
										} else {
											echo "판매 업체가 등록되지 않았습니다.";
										}
								?>
								</div>
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><img src="<?=$img_url?>" width="50" height="50" onmouseover="js_layer_show('<?= $GOODS_NO ?>');" onmouseout="js_layer_hide('<?= $GOODS_NO ?>');"></a></td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?> </td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?></a></td>
							<td><?= getCompanyName($conn, $CATE_03);?></a></td>
							<!--<td><?= getDcodeName($conn, "GOODS_SIZE", $CATE_01);?></a></td>-->
							<td class="price"><?= number_format($BUY_PRICE) ?> 원</td>
							<td class="price"><?= number_format($SALE_PRICE) ?> 원</td>
							<!--<td class="rpd5"><?= number_format($EXTRA_PRICE) ?> 원</td>-->
							<td><?= $REG_DATE ?></td>
							<td class="price"><?= number_format($STOCK_CNT) ?></td>
							<td class="filedown"><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></a></td>
						</tr>
					<? } ?>

					<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
						<tr>
							<td><?=$GOODS_NO?></td>
							<td style="padding: 1px 1px 1px 1px"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><img src="<?=$img_url?>" width="50" height="50"></a></td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?= $GOODS_NAME ?></a></td>
							<td><?= getCompanyName($conn, $CATE_03);?></a></td>
							<!--<td><?= getDcodeName($conn, "GOODS_SIZE", $CATE_01);?></a></td>-->
							<td class="price"><?= number_format($BUY_PRICE) ?> 원</td>
							<!--<td class="rpd5"><?= number_format($EXTRA_PRICE) ?> 원</td>-->
							<td><?= $REG_DATE ?></td>
							<td class="price"><?= number_format($STOCK_CNT) ?></td>
							<td class="filedown"><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></a></td>
						</tr>
					<? } ?>


				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="10">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="10"></td>
						</tr>
					</tfoot>
				</table>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">

				<input type="button" name="aa" value="선택한 상품" class="btntxt" onclick="js_state_mod();">
				<?= makeSelectBox($conn,"GOODS_STATE","goods_state_mod","125","상태선택","","")?>
				<input type="button" name="aa" value="으로 변경" class="btntxt" onclick="js_state_mod();">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<input type="button" name="aa" value=" 선택한 상품 복사 " class="btntxt" onclick="js_copy();"> 
				<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
					<input type="button" name="aa" value=" 선택한 상품 삭제 " class="btntxt" onclick="js_delete();">
				<? } ?>
				</div>

					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
							$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<!--
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
	</tr>
	-->
	</table>
</div>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>