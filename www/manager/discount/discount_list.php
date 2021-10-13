<?session_start();?>
<?
# =============================================================================
# File Name    : discount_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.12.21
# Modify Date  : 
#	Copyright : Copyright @아름지기 Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
$dc_cate			= trim($dc_cate);

if($dc_cate == "hy"){
	$menu_right = "R0003"; // 메뉴마다 셋팅 해 주어야 합니다
}else if ($dc_cate == "art"){
	$menu_right = "AE002"; // 메뉴마다 셋팅 해 주어야 합니다
}else if ($dc_cate == "aca"){
	$menu_right = "SP003"; // 메뉴마다 셋팅 해 주어야 합니다
}

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
	require "../../_classes/biz/discount/discount.php";


#====================================================================
# Request Parameter
#====================================================================

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
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================
	$this_date = date("Ymd",strtotime("0 day"));

	$nListCnt =totalCntDiscount($conn, $dc_cate, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}


	$arr_rs = listDiscount($conn, $dc_cate, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />

<script type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() {
		document.location.href = "discount_write.php?dc_cate=<?=$dc_cate?>";
	}

	function js_view(rn, dc_no) {

		var frm = document.frm;
		
		frm.dc_no.value = dc_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "discount_write.php";
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

</script>
</head>

<body id="bg">
<div id="wrap">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="dc_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="dc_cate" value="<?=$dc_cate?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";

	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
?>

	<div id="contents">
		<p><a href="/">홈</a> &gt; 할인 관리</p>
		
		<div id="tit01">
			<h2>할인 관리</h2>
			<div id="sch">
				<ul>
					<li>
						<select name="search_field" style="width:84px;">
							<option value="TITLE" <? if ($search_field == "TITLE") echo "selected"; ?> >제목</option>
						</select>
					</li>
					<li>
						<input type="text" value="<?=$search_str?>" name="search_str" class="box01" />
						<a href="javascript:js_search();"><img src="../images/common/btn/btn_go01.gif" alt="go" /></a>
					</li>
				</ul>
			</div>
		</div>

		<div id="bbsList">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<colgroup>
					<col width="5%" />
					<col/>
					<col width="25%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
					<col width="15%" />
				</colgroup>
				<thead>
					<tr>
						<th>No.</th>
						<th>제목</th>
						<th>기간</th>
						<th>할인율</th>
						<th>회원할인율</th>
						<th>직원할인율</th>
						<th>사용여부</th>
						<th class="end">등록일</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$DC_NO					= trim($arr_rs[$j]["DC_NO"]);
							$TITLE					= trim($arr_rs[$j]["TITLE"]);
							$DC_FROM				= trim($arr_rs[$j]["DC_FROM"]);
							$DC_TO					= trim($arr_rs[$j]["DC_TO"]);
							$DC_RATE				= trim($arr_rs[$j]["DC_RATE"]);
							$DC_RATE_MEMBER	= trim($arr_rs[$j]["DC_RATE_MEMBER"]);
							$DC_RATE_EMP		= trim($arr_rs[$j]["DC_RATE_EMP"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
							
							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>사용</font>";
							} else {
								$STR_USE_TF = "<font color='red'>미사용</font>";
							}
							
							$from_date = str_replace("-","",$DC_FROM);
							$to_date = str_replace("-","",$DC_TO);
							
				?>
					<tr>
						<td class="filedown"><?= $rn ?></td>
						<td class="lpd10"><a href="javascript:js_view('<?=$rn?>','<?=$DC_NO?>');"><?=$TITLE?></a></td>
						<td class="pname"><?=$DC_FROM?> ~ <?=$DC_TO?></td>
						<td class="filedown"><?=$DC_RATE?> %</td>
						<td class="filedown"><?=$DC_RATE_MEMBER?> %</td>
						<td class="filedown"><?=$DC_RATE_EMP?> %</td>
						<td><?=$STR_USE_TF?></td>
						<td><?= $REG_DATE ?></td>
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
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
			<span class="btn_write">
				<? if ($sPageRight_I == "Y") {?>
				<a href="javascript:js_write();"><img src="../images/common/btn/btn_app.gif" alt="등록" /></a>
				<? } ?>
			</span>
		</div>
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&dc_cate=".$dc_cate;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
	</div>
	<div id="site_info">Copyright &copy; 2009 (재)아름지기 All Rights Reserved.</div>

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