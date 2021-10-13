<?session_start();?>
<?
# =============================================================================
# File Name    : faq_list.php
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
	require "../../_classes/moneual/board/board.php";

	$bb_code = "KFAQ";

	if ($mode == "T") {
		updateBoardUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);
	}

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

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
		$nPageSize = 1000;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listBoard($conn, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $keyword, $reply_state, $con_use_tf, $del_tf, $search_field, $search_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; chaset=<?=$g_chrset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script language="javascript" type="text/javascript">

	function js_write() {
		document.location.href = "faq_write.php";
	}

	function js_view(rn, bb_code, bb_no) {

		var frm = document.frm;
		
		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "faq_write.php";
		frm.submit();
		
	}

	function js_delete(rn, bb_code, bb_no) {

		var frm = document.frm;
	
		bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
		if (bDelOK==true) {
			frm.bb_code.value = bb_code;
			frm.bb_no.value = bb_no;
			frm.mode.value = "D";
			frm.target = "";
			frm.method = "get";
			frm.action = "faq_write.php";
			frm.submit();
		}
		
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

function js_toggle(bb_code, bb_no, use_tf) {
	var frm = document.frm;

	bDelOK = confirm('공개 여부를 변경 하시겠습니까?');
		
	if (bDelOK==true) {

		if (use_tf == "Y") {
			use_tf = "N";
		} else {
			use_tf = "Y";
		}

		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.use_tf.value = use_tf;
		frm.mode.value = "T";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
}

function js_con_cate_01 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_02 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

function js_con_cate_03 () {
	frm.nPage.value = "1";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();
}

var layerId = '';
function ShowContent(num) {
 var submenu = document.getElementById('box_' + num);

 if(layerId != submenu) {
  if(layerId != '') layerId.style.display = 'none';
  submenu.style.display = 'block';
  layerId = submenu;
 } else {
  submenu.style.display = 'none';
  layerId = '';
 }
}
</script>
</head>

<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
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

        <h2>FAQ 관리</h2>
        <div class="btnright"><a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="등록" /></a></div>
        <div class="category_choice">
					<?= makeSelectBoxOnChange($conn,"FAQ","con_cate_01","125","카테고리선택","",$con_cate_01)?>&nbsp;
					<? if ($con_cate_01 == "") $con_cate_01 = "!";?>
					<?= makeSelectBoxOnChange($conn,$con_cate_01,"con_cate_02","125","카테고리선택","",$con_cate_02)?>&nbsp;
				</div>     
        
				
				
        <div id="faqlist">
							<?
								$nCnt = 0;
								
								if (sizeof($arr_rs) > 0) {
									
									for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
										
										$rn							= trim($arr_rs[$j]["rn"]);
										$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
										$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
										$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
										$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
										$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
										$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
										$WRITER_NM			= trim($arr_rs[$j]["WRITER_NM"]);
										$TITLE					= SetStringFromDB($arr_rs[$j]["TITLE"]);
										$CONTENTS				= SetStringFromDB($arr_rs[$j]["CONTENTS"]);
										$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>공개</font>";
										} else {
											$STR_USE_TF = "<font color='red'>비공개</font>";
										}
							?>

					<dl <? if (sizeof($arr_rs) == ($j+1) ) {echo "class=\"end\"";} ?>>
            <dt>
              <span class="tit_question">질문</span>
							<a href="javascript:js_toggle('<?=$BB_CODE?>','<?=$BB_NO?>','<?=$USE_TF?>');">[<?= $STR_USE_TF?>]</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="javascript:ShowContent('qs<?=$rn?>');" id="question<?=$rn?>">[<?=getDcodeName($conn, "FAQ", $CATE_01)?>] [<?=getDcodeName($conn, $CATE_01, $CATE_02)?>] <?=$TITLE?></a>
							<span class="btnset">
								<a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><img src="../images/admin/btn_modify_s.gif" alt="수정" /></a>
								<a href="javascript:js_delete('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><img src="../images/admin/btn_delete_s.gif" alt="삭제" /></a>
							</span>
            </dt>
            <dd id="box_qs<?=$rn?>" style="display:none;">
              <div class="answerwrap">
                <strong class="tit_answer">답변</strong> 
                <div class="txt_answer"><?=nl2br($CONTENTS)?>
                </div>
              </div>
            </dd>
          </dl>
          
							<?			
									}
								} else { 
							?> <!--
								<tr>
									<td height="50" align="center" colspan="8">데이터가 없습니다. </td>
								</tr>
								-->
							<? 
								}
							?>
					
        </div>
				
				
<!--
        <tr> 
          <td><?= $rn ?></td>
          <td class="pname"><?=getGoodsName($conn, $CATE_02)?></td>
          <td class="modeual_nm"><a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$TITLE?></a></td>
          <td class="filedown">
						<a href="/_common/download_file.php?menu=board&bb_code=<?= $BB_CODE ?>&bb_no=<?= $BB_NO ?>&field=file_nm"><img src="../images/admin/icon_file.gif" alt="파일 다운로드" /></a>
					</td>
					<td><a href="javascript:js_toggle('<?=$BB_CODE?>','<?=$BB_NO?>','<?=$USE_TF?>');"><?= $STR_USE_TF ?></a></td>
          <td><?= $REG_DATE ?></td>
        </tr>
-->

      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  <tr>
    <td colspan="2" height="70"><div class="copyright"><img src="../images/admin/copyright.gif" alt="" /></div></td>
  </tr>
  </table>
</div>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
