<?session_start();?>
<?
# =============================================================================
# File Name    : goods_key_list.php
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
	require "../../_classes/moneual/member/member.php";

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

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

	if ($con_goods_type <> "") {
		$nPage = 1;
		$nPageSize = 100;
	} else {
		$nPageSize = 10;
	}

	$nPageBlock	= 10;

	if ($mode == "T") {

		if ($use_tf <> "Y") {
			
			$arr_rs = selectMemberAsID($conn, $m_id);
			$rs_email	= trim($arr_rs[0]["email"]); 
			$rs_m_id	= $m_id;
		
			### 회원가입메일
			//if ( $_POST[email] && $cfg[mailyn_10] == 'y' )
			//{

				//echo 	$rs_email;
				$modeMail = 22;
				include "../../shopping/lib/automail.class.php";
				include "../../shopping/conf/config.php";

				$automail = new automail();

				$automail->_set($modeMail,$rs_email,$cfg);
				$automail->_assign('id',$rs_m_id);
				$automail->_send();
			}

		$result_no = updateGoodsKeyUseTF($conn, $use_tf, $s_adm_no, $key_no);

		$mode = "R";

	}

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntGoodsKey($conn, $g_site_no, $con_goods_type, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$del_tf = "N";

	$arr_rs = listGoodsKey($conn, $g_site_no, $con_goods_type, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript" type="text/javascript" >


	function js_write() {
		alert("준비 중 입니다.");
		return;
		document.location.href = "goods_key_file_write.php";
	}

	function js_view(rn, seq) {

		var frm = document.frm;
		
		frm.key_no.value = seq;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_key_write.php";
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

	function js_con_goods_type () {

		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

function js_toggle(key_no, use_tf, m_id) {
	var frm = document.frm;

	bDelOK = confirm('승인 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.key_no.value = key_no;
		frm.m_id.value = m_id;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
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
<input type="hidden" name="key_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="m_id" value="">
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
        <h2>제품 시리얼 관리</h2>
        <!--
				<ul class="magctxt">
          <li>- <span>상품의 Display 순서 변경은 분류 선택 시 만 가능 합니다.</span></li>
          <li>- 전체 조회시 최근 등록 순으로 보여집니다.</li>
        </ul>
				-->
        <div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="상품키등록" /></a>&nbsp;</div>
        <div class="category_choice">
					분류 : <?= makeSelectBoxOnChange($conn,"GOODS","con_goods_type","105","전체","",$con_goods_type)?>
				</div>
        <table cellpadding="0" cellspacing="0" class="rowstable" id='t'>
        <colgroup>
          <col width="5%" />
          <col width="10%" />
          <col width="25%" />
          <col width="15%" />
          <col width="20%" />
          <col width="15%" />
          <col width="10%" />
        </colgroup>
        <tr>
          <th>순서</th>
          <th>분류</th>
          <th>상품명</th>
          <th>회원명</th>
          <th>등록키</th>
          <th>승인여부</th>
          <th class="end">등록일</th>
        </tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$KEY_NO					= trim($arr_rs[$j]["KEY_NO"]);
							$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_NM				= trim($arr_rs[$j]["GOODS_NM"]);
							$GOODS_TYPE			= trim($arr_rs[$j]["GOODS_TYPE"]);
							$GOODS_KEY			= trim($arr_rs[$j]["GOODS_KEY"]);
							$NAME						= trim($arr_rs[$j]["name"]);
							$M_ID						= trim($arr_rs[$j]["m_id"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<img src=\"../images/admin/ok.gif\">";
							} else {
								$STR_USE_TF = "<img src=\"../images/admin/no.gif\">";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>

        <tr>
					<td class="sort"><span><?=$rn?></span></td>
          <td class="depth">
						<?=$GOODS_TYPE?>
					</td>
          <td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $KEY_NO ?>');"><?=$GOODS_NM?></a></td>
          <td><?=$NAME?> (<?=$M_ID?>)</td>
          <td><?=$GOODS_KEY?></td>
          <td><a href="javascript:js_toggle('<?=$KEY_NO?>','<?=$USE_TF?>','<?=$M_ID?>');"><?=$STR_USE_TF?></a></td>
          <td><?=$REG_DATE?></td>
        </tr>

				<?
						}
					} else { 
				?> 
				<tr>
					<td align="center" height="50" colspan="7">데이터가 없습니다. </td>
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
						<option value="GOODS_NM" <? if ($search_field == "GOODS_NM") echo "selected"; ?> >상품명</option>
					</select>
					<input type="text" value="<?=$search_str?>" name="search_str" class="txt" />
					<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" class="sch" alt="Search" /></a>
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
</form>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>