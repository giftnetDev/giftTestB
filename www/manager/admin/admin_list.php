<?session_start();?>
<?

//print_r ($_SESSION);

# =============================================================================
# File Name    : admin_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

if ($s_adm_cp_type <> "운영") {
	$next_url = "admin_write.php?mode=S&adm_no=$s_adm_no";
?>
<meta http-equiv='Refresh' content='0; URL=<?=$next_url?>'>
<?
	exit;
}

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "AD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/admin/admin.php";


	if ($mode == "T") {
		updateAdminUseTF($conn, $use_tf, $s_adm_no, $adm_no);
	}

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

	if($sel_use_tf == "")
		$sel_use_tf = "Y";
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
		$nPageSize = 15;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntAdmin($conn, $con_group_no, $sel_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listAdmin($conn, $con_group_no, $sel_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" >


	function js_write() {
		document.location.href = "admin_write.php";
	}

	function js_view(rn, adm_no) {

		var frm = document.frm;
		
		frm.adm_no.value = adm_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "admin_write.php";
		frm.submit();
		
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(adm_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('사용 여부를 변경 하시겠습니까?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.adm_no.value = adm_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_pop_log_list(adm_id) { 

		var frm = document.frm;
		
		var url = "pop_admin_latest_access.php?adm_id=" + adm_id;

		NewWindow(url, 'pop_admin_latest_access','1000','600','YES');
	}
</script>	
<script type="text/javascript" >
	$(function(){
		$("select[name=sel_use_tf]").change(function(){
			js_search();
		});
	});
</script>
</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="adm_no" value="">
<input type="hidden" name="use_tf" value="">
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

				<h2>관리자 관리</h2>
				
				<? if($s_adm_group_no == "5")	{	?>				
					<div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a></div>				
				<?	}	?>
				
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>
				<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="3%" />
					<col width="12%"/>
					<col width="8%"/>
					<col width="10%" />
					<col width="5%"/>
					<col width="*"/>
					<col width="14%" />
					<col width="10%" />
					<col width="14%" />
					<col width="10%" />
				</colgroup>
				<thead>

					<tr>
						<th>번호</th>
						<th>관리자그룹</th>
						<th>소속업체</th>
						<th>ID</th>
						<th>사원번호</th>
						<th>이름</th>
						<th>휴대폰</th>
						<th>등록일</th>
						<th>최종로그인</th>
						<th class="end">
							<?= makeSelectBox($conn,"USE_TF","sel_use_tf","70","사용여부","",$sel_use_tf)?>
						</th>
					</tr>
				</thead>
				<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//ADM_ID, ADM_NO, PASSWD, ADM_NAME, ADM_INFO, ADM_HPHONE, ADM_PHONE, ADM_EMAIL, 
							//GROUP_NO, ADM_FLAG, POSITION_CODE, DEPT_CODE, COM_CODE, USE_TF, DEL_TF, REG_ADM, REG_DATE, UP_ADM, UP_DATE, DEL_ADM, DEL_DATE,
							//GROUP_NAME

							$rn						= trim($arr_rs[$j]["rn"]);
							$ADM_ID				= trim($arr_rs[$j]["ADM_ID"]);
							$ADM_NO				= trim($arr_rs[$j]["ADM_NO"]);
							$ADM_NAME			= SetStringFromDB($arr_rs[$j]["ADM_NAME"]);
							$ADM_PHONE		= trim($arr_rs[$j]["ADM_PHONE"]);
							$ADM_HPHONE		= trim($arr_rs[$j]["ADM_HPHONE"]);
							$GROUP_NAME		= trim($arr_rs[$j]["GROUP_NAME"]);
							$CP_NM				= trim($arr_rs[$j]["CP_NM"]);
							$USE_TF				= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF				= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE			= trim($arr_rs[$j]["REG_DATE"]);

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>사용중</font>";
							} else {
								$STR_USE_TF = "<font color='red'>사용안함</font>";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							$arr_log = selectUserLogLatest($conn, "'ad','AO'", $ADM_ID);
							if(sizeof($arr_log) > 0) { 
								$rs_log_ip = $arr_log[0]["LOG_IP"];
								$rs_log_date = $arr_log[0]["LOGIN_DATE"];
								$rs_log_date = date("Y-m-d H:i",strtotime($rs_log_date));
							} else {
								$rs_log_date = "로그인 기록 없음";
								$rs_log_ip = "없음";
							}
				
				?>

					<tr>
						<td><?=$rn?></td>
						<td class="pname"><?= $GROUP_NAME ?></td>
						<td class="pname"><?= $CP_NM ?></td>
						<td><a href="javascript:js_view('<?= $rn ?>','<?= $ADM_NO ?>');"><?= $ADM_ID?></a></td>
						<td><a href="javascript:js_view('<?= $rn ?>','<?= $ADM_NO ?>');"><?= $ADM_NO?></a></td>
						<td><a href="javascript:js_view('<?= $rn ?>','<?= $ADM_NO ?>');"><?= $ADM_NAME ?></a></td>						
						<td><?= $ADM_HPHONE ?></td>
						<td><?= $REG_DATE ?></td>
						<td title="<?=$rs_log_ip?>"><a href="javascript:js_pop_log_list('<?= $ADM_ID?>');" style="text-decoration:underline;"><?= $rs_log_date ?></a></td>
						<td class="filedown"><a href="javascript:js_toggle('<?=$ADM_NO?>','<?=$USE_TF?>');"><?= $STR_USE_TF ?></a></td>
					</tr>

				<?			
						}
					} else { 
				?> 
					<tr>
						<td align="center" height="50" colspan="9">데이터가 없습니다. </td>
					</tr>
				<? 
					}
				?>
				</tbody>
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
							$strParam = $strParam."&sel_use_tf=".$sel_use_tf;
					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
				<div class="bottom_search">
					<select name="search_field" style="width:84px;">
						<option value="ADM_NAME" <? if ($search_field == "ADM_NAME") echo "selected"; ?> >이름</option>
						<option value="ADM_ID" <? if ($search_field == "ADM_ID") echo "selected"; ?> >아이디</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
				</div>      
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
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
