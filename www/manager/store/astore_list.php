<?session_start();?>
<?
# =============================================================================
# File Name    : astore_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/moneual/store/store.php";

	$store_type = "A";

	if ($mode == "T") {
		updateStoreUseTF($conn, $use_tf, $s_adm_no, $store_no);
	}

	if ($mode == "D") {
		
		$result = deleteStore($conn, $s_adm_no, $store_no);

	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "5";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_store_cate = trim($con_store_cate);

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntStore($conn, $g_site_no, $store_type, $con_store_cate, $con_store_url, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listStore($conn, $g_site_no, $store_type, $con_store_cate, $con_store_url, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() {
		document.location.href = "astore_write.php";
	}

	function js_view(rn, store_no) {

		var frm = document.frm;
		
		frm.store_no.value = store_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "astore_write.php";
		frm.submit();
		
	}

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

function js_toggle(store_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.store_no.value = store_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_con_store_cate () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_delete(store_no) {

	var frm = document.frm;

	bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
	if (bDelOK==true) {
		frm.mode.value = "D";
		frm.store_no.value = store_no;
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}
</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="store_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="">
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

	require "../../_common/left_area.php";
?>


		</td>
		<td class="contentarea">

      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">

        <h2>대리점 관리</h2>
        <div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a></div>
        <div class="category_choice">
					<?= makeSelectBoxOnChange($conn,"AREA","con_store_cate","125","지역선택","",$con_store_cate)?>&nbsp;
				</div>     
        <table cellpadding="0" cellspacing="0" class="rowstable">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
						<col width="40%">
						<col width="10%">
					</colgroup>
						<tr>
							<th scope="col">NO.</th>
							<th scope="col">지역</th>
							<th scope="col">지점명</th>
							<th scope="col">전화번호</th>
							<th scope="col">주소</th>
							<th class="end" scope="col">수정/삭제</th>
						</tr>

							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$STORE_NO				= trim($arr_rs[$j]["STORE_NO"]);
										$STORE_CATE			= trim($arr_rs[$j]["STORE_CATE"]);
										$STORE_NM				= SetStringFromDB($arr_rs[$j]["STORE_NM"]);
										$STORE_URL			= trim($arr_rs[$j]["STORE_URL"]);
										$ZIPCODE				= trim($arr_rs[$j]["ZIPCODE"]);
										$ADDR01					= SetStringFromDB($arr_rs[$j]["ADDR01"]);
										$ADDR02					= SetStringFromDB($arr_rs[$j]["ADDR02"]);
										$PHONE01				= trim($arr_rs[$j]["PHONE01"]);
										$PHONE02				= trim($arr_rs[$j]["PHONE02"]);
										$PHONE03				= trim($arr_rs[$j]["PHONE03"]);
										$STORE_HOUR			= SetStringFromDB($arr_rs[$j]["STORE_HOUR"]);
										$CONTENTS				= SetStringFromDB($arr_rs[$j]["CONTENTS"]);
										$FILE_NM				= trim($arr_rs[$j]["FILE_NM"]);
										$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>공개</font>";
										} else {
											$STR_USE_TF = "<font color='red'>비공개</font>";
										}
							?>

        <tr <? if (($j+1) == sizeof($arr_rs)) { echo "class=\"end\""; }?> height="35">
          <td class="filedown"><?=$rn?></td>
          <td><?=getDcodeName($conn,'AREA',$STORE_CATE)?></td>
          <td><?=$STORE_NM?></td>
          <td><?=$PHONE01?>-<?=$PHONE02?>-<?=$PHONE03?></td>
          <td class="banner_posi"><?=$ADDR01?> <?=$ADDR02?></td>
          <td>
            <a href="javascript:js_view('<?=$rn?>','<?=$STORE_NO?>');"><img src="../images/admin/btn_modify_s.gif" alt="수정" /></a>
            <a href="javascript:js_delete('<?=$STORE_NO?>');"><img src="../images/admin/btn_delete_s.gif" alt="삭제" /></a>
          </td>
        </tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="8">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>
						</table>

        </table>
				
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	<tr>
		<td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
	</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>