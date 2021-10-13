<?session_start();?>
<?
# =============================================================================
# File Name    : qna_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @�Ƹ����� Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "C0004"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/board/board.php";

	$bb_code = "QNA";

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
		$nPageSize = 10;
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="STYLESHEET" type="text/css" href="../css/bbs.css" />
<link rel="STYLESHEET" type="text/css" href="../css/layout.css" />

<script type="text/javascript" src="../js/common.js"></script>

<script language="javascript">

	function js_write() {
		document.location.href = "qna_read.php";
	}

	function js_view(rn, bb_code, bb_no) {

		var frm = document.frm;
		
		frm.bb_code.value = bb_code;
		frm.bb_no.value = bb_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "qna_read.php";
		frm.submit();
		
	}

	// ��ȸ ��ư Ŭ�� �� 
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

	bDelOK = confirm('���� ���θ� ���� �Ͻðڽ��ϱ�?');
		
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
</script>
</head>

<body id="bg">
<div id="wrap">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="bb_no" value="">
<input type="hidden" name="bb_code" value="<?=$bb_code?>">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="seq_no" value="" />
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
		<p><a href="/">Ȩ</a> &gt; ������ �Խ��� ����</p>
		
		<div id="tit01">
			<h2>Q&A ����</h2>
			<div id="sch">
				<ul>
					<li>
						<?= makeSelectBox($conn,"QNA","con_cate_01","120","�������� ����","",$con_cate_01)?>
						<select name="search_field" style="width:84px;">
							<option value="TITLE" <? if ($search_field == "TITLE") echo "selected"; ?> >����</option>
							<option value="WRITER_NM" <? if ($search_field == "WRITER_NM") echo "selected"; ?> >����</option>
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
          <col width="10%" />
          <col width="45%" />
          <col width="10%" />
          <col width="15%" />
          <col width="15%" />
        </colgroup>
				<thead>
					<tr>
						<th>No.</th>
						<th>����</th>
						<th>����</th>
						<th>����</th>
						<th>�����</th>
						<th>ó�����</th>
					</tr>
				</thead>
				<tbody>
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
										$TITLE					= trim($arr_rs[$j]["TITLE"]);
										$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
										$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
										$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
										$REPLY_STATE		= trim($arr_rs[$j]["REPLY_STATE"]);
										
										$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

										if ($REPLY_STATE == "Y") {
											$STR_REPLY_STATE = "<font color='navy'>�亯�Ϸ�</font>";
										} else {
											$STR_REPLY_STATE = "<font color='red'>�̴亯</font>";
										}
							
										if ($USE_TF == "Y") {
											$STR_USE_TF = "<font color='navy'>����</font>";
										} else {
											$STR_USE_TF = "<font color='red'>�����</font>";
										}
							?>
        <tr> 
          <td><?= $rn ?></td>
          <td><?= getDcodeName($conn, 'QNA', $CATE_01) ?></td>
          <td class="lpd10"><a href="javascript:js_view('<?=$rn?>','<?=$BB_CODE?>','<?=$BB_NO?>');"><?=$TITLE?></a></td>
					<td><?=$WRITER_NM?></td>
          <td><?= $REG_DATE ?></td>
          <td class="filedown"><?= $STR_REPLY_STATE ?></td>
        </tr>
							<?			
									}
								} else { 
							?> 
								<tr>
									<td height="50" align="center" colspan="8">�����Ͱ� �����ϴ�. </td>
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

			<!--<span class="btn_write"><a href="javascript:js_write();"><img src="../images/common/btn/btn_app.gif" alt="����" /></a></span>-->
		</div>
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
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
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
			<!-- // E: mwidthwrap -->
	</div>

	<div id="site_info">Copyright &copy; 2009 (��)�Ƹ����� All Rights Reserved.</div>

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