<?session_start();?>
<?
# =============================================================================
# File Name    : goods_key_write.php
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
	
	$mm_subtree	 = "3";

	$mode	= trim($mode);

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$goods_type			= trim($goods_type);
	$sale_country		= trim($sale_country);
	$menu_type			= trim($menu_type);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	//echo $pb_nm; 
	//echo $$mode;
		
	$result	= false  ;
	

#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = selectGoodsKey($conn, $key_no);

		#GOODS_TYPE, GOODS_NM, BUY_URL, SALE_COUNTRY, MENU_TYPE,

		$rs_key_no							= trim($arr_rs[0]["KEY_NO"]); 
		$rs_goods_no						= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_nm						= SetStringFromDB($arr_rs[0]["GOODS_NM"]); 
		$rs_goods_type					= trim($arr_rs[0]["GOODS_TYPE"]); 
		$rs_goods_key						= trim($arr_rs[0]["GOODS_KEY"]); 
		$rs_name								= trim($arr_rs[0]["name"]); 
		$rs_m_id								= trim($arr_rs[0]["m_id"]); 
		$rs_mobile							= trim($arr_rs[0]["mobile"]); 
		$rs_use_tf							= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf							= trim($arr_rs[0]["DEL_TF"]); 
		$rs_reg_adm							= trim($arr_rs[0]["REG_ADM"]); 
		$rs_reg_date						= trim($arr_rs[0]["REG_DATE"]); 
		$rs_up_adm							= trim($arr_rs[0]["UP_ADM"]); 
		$rs_up_date							= trim($arr_rs[0]["UP_DATE"]); 
		$rs_del_adm							= trim($arr_rs[0]["DEL_ADM"]); 
		$rs_del_date						= trim($arr_rs[0]["DEL_DATE"]); 

	}

	if ($mode == "U") {

		$result = updateGoodsKey($conn, $g_site_no, $goods_key, $use_tf, $s_adm_no, $key_no);

	}

	if ($mode == "D") {
		$result = deleteGoodsKey($conn, $s_adm_no, $key_no);
	}

	$strParam = "?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
	
	if ($result) {
?>	
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "goods_key_list.php<?=$strParam?>";
</script>
</head>
<body onload="init();">
<form name="frm" method="get">
<input type="hidden" name="mode" value="S">
<input type="hidden" name="key_no" value="<?= $key_no?>">

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
		frm.action = "goods_key_list.php";
		frm.submit();
	}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var key_no = "<?= $key_no ?>";
		var frm = document.frm;

		if (document.frm.rd_use_tf == null) {
			//alert(document.frm.rd_use_tf);
		} else {
			if (frm.rd_use_tf[0].checked == true) {
				frm.use_tf.value = "Y";
			} else {
				frm.use_tf.value = "N";
			}
		}
		

		if (isNull(key_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
		}

		if (frm.mode.value == "U") {
			bDelOK = confirm('수정 하시겠습니까?');//
			if (bDelOK == false) {	
				return;
			}
		}

		frm.method = "post";
		frm.target = "";
		frm.action = "goods_key_write.php";
		frm.submit();

	}

	function js_delete() {
		
		bDelOK = confirm('정말 삭제 하시겠습니까?');//정말 삭제 하시겠습니까?
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.method = "post";
			frm.action = "goods_key_write.php";
			frm.submit();
		} else {
			return;
		}
	}
</script>
</head>

<body id="admin" onresize="BodyMinSize();">


<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="key_no" value="<?= $rs_key_no?>">

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
?>

		</td>
		<td class="contentarea">


			<div id="mwidthwrap">
        <h2>제품 시리얼 관리</h2>
				<br />

        <table cellpadding="0" cellspacing="0" class="colstable">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <th>분류</th>
          <td><?= getDcodeName($conn,"GOODS",$rs_goods_type)?></td>
        </tr>
        <tr>
          <th>제품명</th>
          <td>
						<?=$rs_goods_nm?>
					</td>
        </tr>

        <tr>
          <th>제품 번호</th>
          <td><input type="text" class="txt" name="goods_key" value="<?=$rs_goods_key?>" style="width: 95%;" /></td>
        </tr>
        <tr>
          <th>아이디</th>
          <td><?=$rs_m_id?></td>
        </tr>
        <tr>
          <th>이름</th>
          <td><?=$rs_name?></td>
        </tr>
        <tr>
          <th>연락처</th>
          <td><?=$rs_mobile?></td>
        </tr>
        <tr>
          <th>등록일</th>
          <td><?=$rs_reg_date?></td>
        </tr>

        <tr class="end">
          <th>승인여부</th>
          <td class="choices">
						<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 승인 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 미승인
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>">
					</td>
        </tr>
        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($key_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
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
