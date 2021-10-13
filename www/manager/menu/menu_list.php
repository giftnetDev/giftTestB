<?session_start();?>
<?
# =============================================================================
# File Name    : menu_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @C&C Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "AD004"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/menu/menu.php";

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

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs = listAdminMenu($conn, $use_tf, $del_tf, $search_field, $search_str);

	#echo sizeof($arr_rs);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" >


	function js_write() {


		var url = "pcode_write_popup.php";

		NewWindow(url, '대분류등록', '600', '353', 'NO');
	}

	function js_view(rn, seq) {

		var url = "pcode_write_popup.php?mode=S&pcode_no="+seq;
		NewWindow(url, '대분류조회', '600', '353', 'NO');
	}
	
	function js_view_dcode(rn, seq) {

		var url = "dcode_list_popup.php?mode=R&pcode_no="+seq;
		NewWindow(url, '세부분류조회', '600', '650', 'NO');
	}
	
	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

</script>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="rn" value="">
<input type="hidden" name="pcode_no" value="">
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

				<h2>메뉴 관리</h2>
				<div class="btnrighttxt">
					<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:NewWindow('pop_menu_write.php', 'pop_add_menu', '560', '285', 'no');">대분류등록</a>&nbsp;&nbsp;
					<? } ?>
					<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:NewWindow('pop_menu_order.php', 'pop_order_menu', '560', '470', 'no');">메뉴순서변경 </a>
					<? } ?>
				
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>
				<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="30%" />
					<col width="40%" />
					<col width="10%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th>메뉴명</th>
						<th>메뉴URL</th>
						<th>권한코드</th>
						<th class="end">비고</th>
					</tr>
				</thead>
				<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							//MENU_NO, MENU_CD, MENU_NAME, MENU_URL, MENU_FLAG, MENU_SEQ01, MENU_SEQ02, MENU_SEQ03, MENU_RIGHT
							
							$MENU_NO				= trim($arr_rs[$j]["MENU_NO"]);
							$MENU_CD				= trim($arr_rs[$j]["MENU_CD"]);
							$MENU_NAME			= trim($arr_rs[$j]["MENU_NAME"]);
							$MENU_URL				= trim($arr_rs[$j]["MENU_URL"]);
							$MENU_FLAG			= trim($arr_rs[$j]["MENU_FLAG"]);
							$MENU_SEQ01			= trim($arr_rs[$j]["MENU_SEQ01"]);
							$MENU_SEQ02			= trim($arr_rs[$j]["MENU_SEQ02"]);
							$MENU_SEQ03			= trim($arr_rs[$j]["MENU_SEQ03"]);
							$MENU_RIGHT			= trim($arr_rs[$j]["MENU_RIGHT"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if (strlen($MENU_CD) == 2) {
								$menu_str = "<font color='blue'>⊙ ".$MENU_NAME."</font>";
							} else {
								for ($menuspace = 0 ; $menuspace < strlen($MENU_CD) ;$menuspace++) {
									$menu_str = $menu_str ."&nbsp;";
								}

								if (strlen($MENU_CD) == 4) {
									$menu_str = $menu_str ."┗ <font color='navy'>".$MENU_NAME."</font>";
								} else if (strlen($MENU_CD) == 6) {
									$menu_str = $menu_str ."&nbsp;&nbsp;┗ <font color='gray'>".$MENU_NAME."</font>";
								}
							}

				?>
					<tr>
						<td class="modeual_nm"><a href="javascript:NewWindow('pop_menu_write.php?mode=S&m_level=<?=$MENU_CD?>&menu_no=<?=$MENU_NO?>', 'pop_modify_menu', '560', '285', 'no');"><?=$menu_str?></a></td>
						<td class="modeual_nm"><?=$MENU_URL?></td>
						<td class="filedown"><?= $MENU_RIGHT ?></td>
						<td>
							<? 
								if ($sPageRight_I == "Y") {
									if (strlen($MENU_CD) <= 4) {
										if (strlen($MENU_CD) == 2) {
							?>
							<? if ($sPageRight_I == "Y") {?>
							<a href="javascript:NewWindow('pop_menu_write.php?m_level=<?=$MENU_CD?>&m_seq01=<?=$MENU_SEQ01?>&m_seq02=<?=$MENU_SEQ02?>', 'pop_add_menu', '560', '285', 'no');">중분류등록</a>&nbsp;
							<? } ?>
							<? if ($sPageRight_U == "Y") {?>
							<a href="javascript:NewWindow('pop_menu_order.php?m_level=<?=$MENU_CD?>', 'pop_order_menu', '560', '470', 'no');">순서변경</a>
							<? } ?>
							<?
										} else {
							?>
							<? if ($sPageRight_I == "Y") {?>
							<a href="javascript:NewWindow('pop_menu_write.php?m_level=<?=$MENU_CD?>&m_seq01=<?=$MENU_SEQ01?>&m_seq02=<?=$MENU_SEQ02?>', 'pop_add_menu', '560', '285', 'no');">소분류등록</a>&nbsp;
							<? } ?>
							<? if ($sPageRight_U == "Y") {?>
							<a href="javascript:NewWindow('pop_menu_order.php?m_level=<?=$MENU_CD?>', 'pop_order_menu', '560', '470', 'no');">순서변경</a>
							<? } ?>
							<?
										}
									}
									echo "&nbsp;";
								} else {
									echo "&nbsp;";
								}
							?>
						</td>
					</tr>
				<?			
							$menu_str = "";
						}
					} else { 
				?> 
					<tr>
						<td align="center" height="50" colspan="7">데이터가 없습니다. </td>
					</tr>
				<? 
					}
				?>
				</tbody>
				</tbody>
			</table>

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