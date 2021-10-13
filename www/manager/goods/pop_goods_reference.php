<?session_start();?>
<?
# =============================================================================
# File Name    : order_write.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @CNC Corp. All Rights Reserved.
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================

	$keyword				= trim($keyword);
	
#===============================================================
# Get Search list count
#===============================================================
	
	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_ref_no = $chk_no[$k];
			$result = deleteGoodsCodeReference($conn, $str_ref_no);
		}

		if($result) { 
?>
<script type="text/javascript">
	alert('정상 등록 되었습니다.');
</script>
<?
		}
	}
	

	$next_number = str_pad(getMaxCodeByGoodsCode($conn),5,"0",STR_PAD_LEFT);

	if($keyword != "")
		$arr_goods = listGoodsCodeReference($conn, $keyword, $order_field, $order_str);
	else
		$keyword = $next_number;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script language="javascript">
	
	function js_search()
	{
		var frm = document.frm;

		frm.mode.value = "S";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_select_new_code() {
	
		var new_code = $(".combined_code").html();
		opener.js_fill_goods_code(new_code);
		self.close();

	}

	function js_delete() 
	{ 

		var frm = document.frm;

		frm.mode.value = "D";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
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

<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>신규 코드 생성</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post">
<input type="hidden" name="objname" value="" />
<input type="hidden" name="depth" value="" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="keyword" value="">

	<div class="sp10"></div>
	<h2>* 신규코드 생성</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
		<colgroup>
			<col width="12%" />
			<col width="33%" />
			<col width="12%" />
			<col width="33%" />
			<col width="*" />
		</colgroup>
		<thead>
			<tr>
				<th>상품분류표</th>
				<td colspan="3" class="line"><?= makeCategoryGenericSelectBoxOnChange($conn, "goods_code_part_A", "15");?></td>
			</tr>
			<tr>
				<th>거래처 구분</th>
				<td colspan="3" class="line"><?= makeCategoryGenericSelectBoxOnChange($conn, "goods_code_part_B", "16");?></td>
			</tr>
			<tr>
				<th>일련번호(5자리)</th>
				<td colspan="3" class="line"><input type="text" name="goods_code_part_C" value="<?=$next_number?>"/></td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<script>
		$(function(){
			$("select[name=goods_code_part_A_01]").prop('disabled', 'disabled');
			$("select[name=goods_code_part_B_01]").prop('disabled', 'disabled');
			$("select[name=goods_code_part_B_03]").hide();

			$("select[name=goods_code_part_A_03],select[name=goods_code_part_B_02]").change(function(){
				combine();
			});

			$("input[name=goods_code_part_C]").keyup(function(){
				combine();
			});

			function combine() { 
				var partA = $("select[name=goods_code_part_A_03]").find(':selected').data('code');
				var partB = $("select[name=goods_code_part_B_02]").find(':selected').data('code');
				var partC = $("input[name=goods_code_part_C]").val().trim();

				if(partA != undefined && partB != undefined) { 
					$(".combined_code").html(partA + "-" + partB + partC);
					if(partC.length >= 5)
						checkDuplicate($(".combined_code").html(), partC);
				}

			}

			function checkDuplicate(new_code, partA, partB, serial_part) {

				if (!isNull(new_code)) {

					$.ajax({
					  url: "json_goods_list.php",
					  dataType: 'json',
					  async: false,
					  data: {goods_code: new_code, serial_part:serial_part},
					  success: function(data) {
						$.each( data, function( i, item ) {
							
							if(item.RESULT == "1" && item.PARTLY == "1") {
							
								$(".msg").css("color","red");
								$(".msg").html("에러 : 상품코드 중복");

							} else if(item.RESULT == "0" && item.PARTLY == "1") {
							
								$(".msg").css("color","blue");
								$(".msg").html("체크요망 : 일련번호 중복");
							
							} else if(item.RESULT == "0" && item.PARTLY == "0") {
								$(".msg").css("color","black");
								$(".msg").html("");
							}

						  });
					  }
					});
				}

			}
		});
	</script>
	<div class="sp15"></div>
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
		<colgroup>
			<col width="12%" />
			<col width="*" />
		</colgroup>
		<tr>
			<th>다음 상품코드</th>
			<td colspan="3" class="line"><a href="javascript:js_select_new_code()" class="combined_code"></a> &nbsp;&nbsp;&nbsp;<span class="msg"></span></td>
		</tr>
	</table>
	
	<h2>* 기존 입력 조회</h2>
	
	<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
	<colgroup>
		<col width="12%" />
		<col width="38%" />
		<col width="12%" />
		<col width="38%" />
	</colgroup>
	<tbody>
		<tr>
			<th>정렬</th>
			<td class="line">
				<select name="order_field" style="width:84px;">
					<option value="FROM_TABLE" <? if ($order_field == "FROM_TABLE") echo "selected"; ?> >위치</option>
					<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE" || $order_field == "") echo "selected"; ?> >상품코드</option>
					<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >상품명</option>
				</select>&nbsp;&nbsp;
				<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> 오름차순 &nbsp;
				<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > 내림차순 
			</td>
			<th>검색 키워드</th>
			<td class="line"><input type="text" name="keyword" value="<?=$keyword?>" onkeydown = "if(event.keyCode==13) js_search();" /> &nbsp;&nbsp;&nbsp;<a href="javascript:js_search();"><img src="../images/admin/btn_search.gif" alt="확인" /></a></td>
		</tr>
	</tbody>
	</table>


	<div class="sp10"></div>
	<table cellpadding="0" cellspacing="0" width="100%" class="rowstable">
	<colgroup>
		<col width="2%" />
		<col width="10%" />
		<col width="20%" />
		<col width="15%" />
		<col width="*" />
		
	</colgroup>
	<thead>
		<tr>
			<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
			<th>No.</th>
			<th>위치</th>
			<th>상품코드</th>
			<th class="end">상품명</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_goods) >= 1) {
		for($i = 0; $i < sizeof($arr_goods); $i ++) { 

			$REF_NO			= trim($arr_goods[$i]["REF_NO"]);
			$GOODS_CODE		= trim($arr_goods[$i]["GOODS_CODE"]);
			$GOODS_NAME		= trim($arr_goods[$i]["GOODS_NAME"]);
			$FROM_TABLE	    = trim($arr_goods[$i]["FROM_TABLE"]); 

	?>

	
	<tr height="30" data-ref_no='<?=$REF_NO?>'>
		<td>
			<? if($FROM_TABLE != "등록상품") {  ?>
				<input type="checkbox" name="chk_no[]" value="<?=$REF_NO?>"/>
			<? } ?>
		</td>
		<td><?=$REF_NO?></td>
		<td><?=$FROM_TABLE?></td>
		<td><?=$GOODS_CODE?></td>
		<td class="modeual_nm"><?=$GOODS_NAME?></td>
	</tr>
	
	<?
		}
	} else {

	?>
	<tr>
		<td colspan="5" height="40" align="center">데이터가 없습니다</td>
	</tr>
	<?

	}
	
	?>
	</tbody>
	</table>
	<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
		<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "운영") {?>
			<input type="button" name="aa" value=" 선택한 임시 상품 삭제 " class="btntxt" onclick="js_delete();">
		<? } ?>
	</div>

	<div class="btn">
	  <a href="javascript:window.close();"><img src="../images/admin/btn_cancel.gif" alt="취소" /></a>
	</div>   
	

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>