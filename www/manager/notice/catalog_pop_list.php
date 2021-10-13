<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection			//20210407 KBJ
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "BO008"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/board/catalog_pop.php";


#====================================================================
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
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

	$nListCnt =totalCntCatalogPop($conn, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = catalog_pop_list($conn, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() 
	{
		document.location.href = "catalog_pop_write.php?mode=I";
	}

	function js_view(catalog_no) 
	{
		var frm = document.frm;
		
		frm.catalog_no.value = catalog_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "catalog_pop_write.php";
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

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="catalog_no">
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

        <h2>카다로그 팝업</h2>			
	
        <div style="float: left; ">
		총 <?=number_format($nListCnt)?> 건
		</div>
		<div class="btnright" style="margin: 0 0 5px 0;">
			<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록"/></a>
		</div>
        <table cellpadding="0" cellspacing="0" class="rowstable">
        <colgroup>
          <col width="5%" />
          <col width="45%" />
          <col width="10%" />
		  <col width="10%" />
		  <col width="10%" />
          <col width="10%" />
		  <col width="10%" />
        </colgroup>
        <tr>
          <th>No.</th>
          <th>제목</th>
		  <th>시작일</th>
		  <th>종료일</th>
		  <th>작성자</th>
          <th>등록일</th>
		  <th>조회수</th>
        </tr>
		<?
			$nCnt = 0;
			
			if (sizeof($arr_rs) > 0) {
				
				for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
					
					$RN						= trim($arr_rs[$j]["RN"]);
					$CTLPOP_NO				= trim($arr_rs[$j]["CTLPOP_NO"]);
					$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
					$CTLPOP_START			= trim($arr_rs[$j]["CTLPOP_START"]);
					$CTLPOP_END				= trim($arr_rs[$j]["CTLPOP_END"]);
					$ADM_NAME				= trim($arr_rs[$j]["ADM_NAME"]);
					$REG_ADM				= trim($arr_rs[$j]["REG_ADM"]);
					$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
					$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
					$REG_DATE 				= date("Y-m-d",strtotime($REG_DATE));
		
		?>
        <tr height="30">
          <td><?= $RN ?></td>
          <td class="modeual_nm"><a href="javascript:js_view('<?=$CTLPOP_NO?>');"><?=$TITLE?></a></td>
          <td><?= $CTLPOP_START ?></td>
		  <td><?= $CTLPOP_END ?></td>
		  <td><?= $ADM_NAME ?></td>
		  <td><?= $REG_DATE ?></td>
		  <td><?= $HIT_CNT ?></td>
        </tr>
			<?			
					}
				} else { 
			?> 
				<tr>
					<td height="50" align="center" colspan="7">데이터가 없습니다. </td>
				</tr>
			<? 
				}
			?>
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
						<option value="TITLE" <? if ($search_field == "TITLE") echo "selected"; ?> >제목</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
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
