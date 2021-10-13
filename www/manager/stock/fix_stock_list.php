<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "SG011"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	

#====================================================================
# Request Parameter
#====================================================================

	$mm_subtree	 = "3";

	$cnt_success = 0;

	#List Parameter
	if ($mode == "U") {

		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_temp_no = $chk_no[$k];
			//echo $str_temp_no."<br>";
			$result = updateFixStockGoods($conn, $str_temp_no, $s_adm_no);

			if($result)
				$cnt_success ++;
		}
	}

	$file_nm		= trim($file_nm);

#============================================================
# Page process
#============================================================

#===============================================================
# Get Search list count
#===============================================================


	$arr_rs = listFixStockGoods($conn, $file_nm);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>

  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>
<script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
</script>
<script language="javascript">

	// 조회 버튼 클릭 시 
	function js_search() {
		var frm = document.frm;
		
		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


	function js_update() {
		var frm = document.frm;

		var frm = document.frm;

		bDelOK = confirm('선택한 상품의 재고 수량을 수정 하시겠습니까?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "U";
			frm.method = "post";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_excel_dn() {

		var frm = document.frm;

		frm.con_cate.value = "";

		if (frm.gd_cate_01 != null) {
			if (frm.gd_cate_01.value != "") {
				frm.con_cate.value = frm.gd_cate_01.value;
			}
		}

		if (frm.gd_cate_02 != null) {
			if (frm.gd_cate_02.value != "") {
				frm.con_cate.value = frm.gd_cate_02.value;
			}
		}

		if (frm.gd_cate_03 != null) {
			if (frm.gd_cate_03.value != "") {
				frm.con_cate.value = frm.gd_cate_03.value;
			}
		}

		if (frm.gd_cate_04 != null) {
			if (frm.gd_cate_04.value != "") {
				frm.con_cate.value = frm.gd_cate_04.value;
			}
		}

		frm.mode.value = "";
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "fix_stock_excel_list.php";
		frm.submit();
	}

	function js_up() {
		var url = "fix_stock_input.php";
		NewWindow(url, ' 재고실사등록', '820', '613', 'YES');
	}
	
	function js_barcode_open() {
		var url = "pop_in_stock_barcode.php";
		NewWindow(url, ' 재고실사-바코드', screen.width, screen.height, 'YES');
	}

	function js_reload(filename) {
		
		var frm = document.frm;
		frm.file_nm.value = filename;
		//alert(filename);
		frm.target = "";
		frm.action = "fix_stock_list.php";
		frm.submit();
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}
</script>
</head>

<body id="admin">

<form name="frm" method="post" action="javascript:js_search();">
<input type="hidden" name="file_nm" value="<?=$file_nm?>">
<input type="hidden" name="depth" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="cal_qty" value="">
<input type="hidden" name="cal_bqty" value="">
<input type="hidden" name="cal_fqty" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->
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

				<h2>재고 실사 등록</h2>
				<div class="btnright">
					&nbsp;
				</div>
				<div class="category_choice"><!--<select style="width: 100px;"><option>제품선택</option></select>-->&nbsp;</div>


			<div class="btnright">
				<input type="button" name="aa" value=" 재고실사등록용 엑셀받기 " class="btntxt" onclick="js_excel_dn();">&nbsp;&nbsp;&nbsp;
				<input type="button" name="aa" value=" 재고실사 등록하기 " class="btntxt" onclick="js_up();"> 
			</div>
			<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tr>
					<th>카테고리</th>
					<td colspan="4">
						<?= makeCategorySelectBoxOnChange($conn, $con_cate, $exclude_category);?>
						<input type="hidden" name="con_cate" value="<?=$con_cate?>">
					</td>
				</tr>
			</table>
			<div class="sp20"></div>
			<font color="red"><b>
				1. 재고실사용 엑셀 리스트를 먼저 작성하신 후 해당 엑셀 파일을 등록해 주세요.<br>
				2. 재고실사 액셀 중 현 재고와 실재고가 다른 경우 리스트에 노츨 됩니다.<br>
				3. 재고실사를 반영할 상품을 선택 후 하단에 '선택한 재고  수정'을 클릭 하십시오.<br><br>
			</b></font>
			<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
				<colgroup>
					<col width="5%" />
					<col width="10%" />
					<col width="*" />
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="20%"/>
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
						<th>상품코드</th>
						<th>상품명</th>
						<th>정상재고</th>
						<th>실사정상재고</th>
						<th>불량재고</th>
						<th>실사불량재고</th>
						<th class="end">비고</th>
					</tr>
				</thead>
				<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$TEMP_NO				= trim($arr_rs[$j]["TEMP_NO"]);
							$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$GOODS_STOCK_CNT		= trim($arr_rs[$j]["GOODS_STOCK_CNT"]);
							$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
							$GOODS_BSTOCK_CNT		= trim($arr_rs[$j]["GOODS_BSTOCK_CNT"]);
							$MEMO					= trim($arr_rs[$j]["MEMO"]);

				?>
					<tr height="37">
						<td class="order"><input type="checkbox" name="chk_no[]" value="<?=$TEMP_NO?>"></td>
						<td class="modeual_nm"><?=$GOODS_CODE?></td>
						<td class="modeual_nm"><?=$GOODS_NAME?></td>
						<td class="price" style="padding-right:15px"><?=number_format($GOODS_STOCK_CNT)?></td>
						<td class="price" style="padding-right:15px"><?=number_format($STOCK_CNT)?></td>
						<td class="price" style="padding-right:15px"><?=number_format($GOODS_BSTOCK_CNT)?></td>
						<td class="price" style="padding-right:15px"><?=number_format($BSTOCK_CNT)?></td>
						<td class="modeual_nm"><?=$MEMO?></td>
					</tr>
					<?
						}
					}else{
						?>
						<tr class="order">
							<? if($cnt_success == 0) { ?>
								<? if ($file_nm <> "") {?>
								<td height="50" align="center" colspan="8">재고 실사 조정할 상품이 없습니다. </td>
								<? } else { ?>
								<td height="50" align="center" colspan="8">재고 실사 엑셀 파일을 등록해 주세요. </td>
								<? } ?>
							<? } else { ?>
								<td height="50" align="center" colspan="8"><b><?=$cnt_success?> 개의 상품이 반영되었습니다.</b> </td>
							<? } ?>
						</tr>
					<?
						}
					?>
				</tbody>
			</table>

			<div class="sp10"></div>
			<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
			<? if (($sPageRight_D == "Y") && ($s_adm_cp_type == "운영")) {?>
				<input type="button" name="aa" value=" 선택한 재고  수정 " class="btntxt" onclick="js_update();"> 
			<? } ?>
			</div>
			<div class="sp10"></div>
		<!-- // E: mwidthwrap -->
			</div>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>