<?session_start();?>
<?
# =============================================================================
# File Name    : member_list.php
# =============================================================================


#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "ME002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/member/member.php";

#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";

	//일반회원 목록 페이지의 mem_type 고정
	$mem_type = "C";
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

	$nListCnt = totalCntMember($conn, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}
	
	$arr_rs = listMember($conn, $mem_type, $email_tf, $use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" >


	function js_write() {
		document.location.href = "member_write.php";
	}

	function js_view(rn, mem_no) {

		var frm = document.frm;
		
		frm.mem_no.value = mem_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "member_write.php";
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

	function js_toggle(mem_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('사용 여부를 변경 하시겠습니까?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.mem_no.value = mem_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mem_no" value="">
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

				<h2>홈페이지 회원</h2>
				<div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a></div>
				<div class="category_choice">&nbsp;</div>
				<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="2%" />
					<col width="10%" />
					<col width="10%" />
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
				</colgroup>
				<thead>

					<tr>
						<th>No..</th>
						<th>ID</th>
						<th>이름</th>
						<th>이메일</th>
						<th>전화</th>
						<th>핸드폰</th>
						<th>회사명</th>
						<th>등록일</th>
						<th class="end">사용여부</th>
					</tr>
				</thead>
				<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							/*
								MEM_NO, MEM_TYPE, MEM_ID, MEM_PW, MEM_NM, JUMIN1, 
								JUMIN2, BIZ_NUM1, BIZ_NUM2, BIZ_NUM3, BIRTH_DATE, CALENDAR, EMAIL, EMAIL_TF, ZIPCODE, ADDR1, ADDR2, PHONE, HPHONE, JOB, 
								POSITION, CPHONE, CFAX, CZIPCODE, CADDR1, CADDR2, JOIN_HOW, JOIN_HOW_PERSON, JOIN_HOW_ETC, ETC,
							*/
							$rn						= trim($arr_rs[$j]["rn"]);
							$MEM_NO				= trim($arr_rs[$j]["MEM_NO"]);
							$MEM_ID				= trim($arr_rs[$j]["MEM_ID"]);
							$MEM_NM				= SetStringFromDB($arr_rs[$j]["MEM_NM"]);
							$EMAIL				= trim($arr_rs[$j]["EMAIL"]);
							$PHONE				= trim($arr_rs[$j]["PHONE"]);
							$HPHONE				= trim($arr_rs[$j]["HPHONE"]);
							
							$ETC				= SetStringFromDB($arr_rs[$j]["ETC"]);
							$USE_TF				= trim($arr_rs[$j]["USE_TF"]);
							$REG_DATE			= trim($arr_rs[$j]["REG_DATE"]);

							$CP_NM				= SetStringFromDB($arr_rs[$j]["CP_NM"]);

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>승인완료</font>";
							} else {
								$STR_USE_TF = "<font color='red'>미승인</font>";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>

					<tr>
						<td><?=$rn?></td>
						<td><a href="javascript:js_view('<?= $rn ?>','<?= $MEM_NO ?>');" class="pname"><?= $MEM_ID?></a></td>
						<td><a href="javascript:js_view('<?= $rn ?>','<?= $MEM_NO ?>');" class="pname"><?= $MEM_NM ?></a></td>
						<td><?= $EMAIL ?></td>
						<td><?= $PHONE ?></td>
						<td><?= $HPHONE ?></td>
						<td class="pname"><?= $CP_NM ?></td>
						<td><?= $REG_DATE ?></td>
						<td class="filedown"><?= $STR_USE_TF ?></td>
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

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				<br />
				<div class="bottom_search">
					<select name="search_field" style="width:84px;">
						<option value="MEM_NM" <? if ($search_field == "MEM_NM") echo "selected"; ?> >이름</option>
						<option value="MEM_ID" <? if ($search_field == "MEM_ID") echo "selected"; ?> >아이디</option>
						<option value="CP_NM" <? if ($search_field == "CP_NM") echo "selected"; ?> >회사명</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();"/>
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
				</div>      
			</div>
			<!-- // E: mwidthwrap -->

		</td>
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
