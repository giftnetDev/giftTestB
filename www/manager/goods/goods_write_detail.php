<?session_start();?>
<?
# =============================================================================
# File Name    : goods_write_detail.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

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
	require "../../_classes/moneual/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================
	
	$mm_subtree	 = "2";

	$mode	= trim($mode);
	$idx	= trim($idx);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	//echo $pb_nm; 
	//echo $$mode;
	
	$goods_nm				= SetStringToDB($goods_nm);
	
	$result	= false  ;
	
	$strParam = "mode=S&goods_no=".$goods_no."&nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;

#====================================================================
# DML Process
#====================================================================
		
	if ($mode == "I") {

		$result_no = insertGoodsDetail($conn, $goods_no, $detail_info, $idx);

		$mode			= "S";

	}

	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		#GOODS_TYPE, GOODS_NM, BUY_URL, SALE_COUNTRY, MENU_TYPE,

		$rs_goods_no						= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_nm						= SetStringFromDB($arr_rs[0]["GOODS_NM"]); 
		$rs_detail_info_01			= trim($arr_rs[0]["DETAIL_INFO_01"]); 
		$rs_detail_info_02			= trim($arr_rs[0]["DETAIL_INFO_02"]); 
		$rs_detail_info_03			= trim($arr_rs[0]["DETAIL_INFO_03"]); 
		$rs_detail_info_04			= trim($arr_rs[0]["DETAIL_INFO_04"]); 
		$rs_detail_info_05			= trim($arr_rs[0]["DETAIL_INFO_05"]); 
		$rs_detail_info_06			= trim($arr_rs[0]["DETAIL_INFO_06"]); 
		$rs_detail_info_07			= trim($arr_rs[0]["DETAIL_INFO_07"]); 

		if ($idx == "01") $content  = $rs_detail_info_01;
		if ($idx == "02") $content  = $rs_detail_info_02;
		if ($idx == "03") $content  = $rs_detail_info_03;
		if ($idx == "04") $content  = $rs_detail_info_04;
		if ($idx == "05") $content  = $rs_detail_info_05;
		if ($idx == "06") $content  = $rs_detail_info_06;
		if ($idx == "07") $content  = $rs_detail_info_07;
	
	}
	
	if ($result) {
?>	
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script language="javascript">
		//alert('정상 처리 되었습니다.');
	function init() {
		document.frm.submit();
	}
</script>
</head>
<body onload="init();">
<form name="frm" method="get">
<input type="hidden" name="mode" value="S">
<input type="hidden" name="goods_no" value="<?= $goods_no?>">

<!--
<input type="hidden" name="goods_type" value="<?= $goods_type?>">
<input type="hidden" name="sale_country" value="<?= $sale_country?>">
<input type="hidden" name="menu_type" value="<?= $menu_type?>">
-->

<input type="hidden" name="search_field" value="<?= $search_field ?>">
<input type="hidden" name="search_str" value="<?= $search_str ?>">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
</form>
</body>
</html>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript">
	
	// 조회 버튼 클릭 시 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "goods_list.php";
		frm.submit();
	}

	function goNext(page_nm) {
		
		frm.mode.value = "S";

		if (page_nm == "") {
			
			frm.method = "get";
			frm.action = "goods_write.php";
			frm.submit();

		} else {

			frm.method = "get";
			frm.action = "goods_write_"+page_nm+".php";
			frm.submit();
		
		}
	
	}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var goods_no = "<?= $goods_no ?>";
		var frm = document.frm;
		
		frm.detail_info.value = SubmitHTML();

		frm.mode.value = "I";

		frm.method = "post";
		frm.target = "";
		frm.action = "goods_write_detail.php";
		frm.submit();

	}


</script>
</head>

<body id="admin" onresize="BodyMinSize();">


<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="goods_no" value="<?= $rs_goods_no?>">
<input type="hidden" name="idx" value="<?= $idx?>">

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

	require "../../_common/left_area.php";

	include_once('../../_common/editor/func_editor.php');
?>

		</td>
		<td class="contentarea">


			<div id="mwidthwrap">
        <h2>본 제품 관리</h2>
        <? if ($goods_no <> "") {?>
				<div class="subtab">
          <div class="rwrap">
          <ul>
            <li><a href="goods_write.php?<?=$strParam?>">기본정보</a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=01"><? if ($idx == 01) echo "<b>"?>Overview<? if ($idx == 01) echo "</b>"?></a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=02"><? if ($idx == 02) echo "<b>"?>Features<? if ($idx == 02) echo "</b>"?></a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=03"><? if ($idx == 03) echo "<b>"?>Multimedia<? if ($idx == 03) echo "</b>"?></a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=04"><? if ($idx == 04) echo "<b>"?>Design<? if ($idx == 04) echo "</b>"?></a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=05"><? if ($idx == 05) echo "<b>"?>Gallery<? if ($idx == 05) echo "</b>"?></a>
            <li><a href="goods_write_detail.php?<?=$strParam?>&idx=06"><? if ($idx == 06) echo "<b>"?>Specification<? if ($idx == 06) echo "</b>"?></a>
            <li class="end"><a href="goods_write_detail.php?<?=$strParam?>&idx=07"><? if ($idx == 07) echo "<b>"?>Tip<? if ($idx == 07) echo "</b>"?></a></li> <!-- 가장 마지막에 오늘 메뉴명이 들어있는 li 엘리먼트에 end 클래스 붙여주세요 -->
          </ul>
          </div>
        </div>
				<br />
				<?	} ?>
				<ul class="magctxt">
          <li>- <span>반드시 1단계 기본정보 등록을 완료 해야 상품 등록이 됩니다.</span></li>
          <li>- 1단계 기본정보 등록만 해도 상품은 등록 됩니다.</li> 
          <li>- 2단계 부가 정보는 수정 화면에서 다시 등록 하실 수 있습니다.</li>
        </ul>

				
        <h3><span>2단계</span> : [<?=$rs_goods_nm?>] 부가 정보 등록</h3>
        <table cellpadding="0" cellspacing="0" class="colstable02" border="0">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <td class="contentswrite" colspan="100">
						<input type="hidden" name="detail_info" value="">
						<?= myEditor(1,'../../_common/editor','frm','content','100%','300');?>
					</td>
        </tr>
        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($goods_no <> "") {?>
          <!--<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>-->
					<? } ?>
        </div>
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  <tr>
    <td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
  </tr>
  </table>
</div>

</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
