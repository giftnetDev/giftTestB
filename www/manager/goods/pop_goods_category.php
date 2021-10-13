<?session_start();?>
<?

#===============================================================
# 상품의 검색 카테고리 입력 / 수정 / 조회 페이지
#===============================================================

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

	$goods_no				= trim($goods_no);
	
#===============================================================
# Get Search list count
#===============================================================

	if($goods_cate == "") { 
		if ($gd_cate_01 <> "") {
			$goods_cate = $gd_cate_01;
		}
		if ($gd_cate_02 <> "") {
			$goods_cate = $gd_cate_02;
		}
		if ($gd_cate_03 <> "") {
			$goods_cate = $gd_cate_03;
		}
		if ($gd_cate_04 <> "") {
			$goods_cate = $gd_cate_04;
		}
	}

	if ($mode == "I") {

		if($goods_cate <> "" && $goods_no <> "") {
			insertGoodsCategory($conn, $goods_no, $goods_cate, $page, $seq);
			$goods_no = "";
		}

	}

	if ($mode == "D") {

		if($goods_cate <> "" && $goods_no <> "") {
			deleteGoodsCategory($conn, $goods_no, $goods_cate);
			$goods_no = "";
		}

	}

	if($goods_cate <> "" || $goods_no <> "") { 
		
		$arr_rs_goods_category = listSearchGoodsCategory($conn, $goods_cate, $has_sub_cate, $page, $goods_no);

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title>기프트넷</title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
  
<script language="javascript">

	function js_save() {

		var frm = document.frm;
		var goods_no = frm.goods_no.value;


		<? if($goods_cate == "") { ?>
			if (frm.gd_cate_01.value == "") {
				alert('카테고리구분을 선택해 주세요.');
				frm.gd_cate_01.focus();
				return ;		
			}
		<? } ?>

		frm.mode.value = "I";

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_search() {

		var frm = document.frm;
		frm.mode.value = "";

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_reset() { 
		location.href = "<?=$_SERVER[PHP_SELF]?>";
	}

	function js_clear(target) {

		var frm = document.frm;
		if(target == "goods_no")
			frm.goods_no.value = "";
		
		if(target == "goods_cate")
			frm.goods_cate.value = "";

		frm.mode.value = "";

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_delete(goods_cate, goods_no) { 

		frm.goods_cate.value = goods_cate;
		frm.goods_no.value = goods_no;

		frm.mode.value = "D";

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
	

</script>
<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {
		
		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				
				frm.keyword.value = keyword;
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}

	function displayResult(str) {
						
		var resultText = str;
		
		var result = resultText.split('|');

		var count = parseInt(result[0]);

		var keywordList = null;
		var arr_keywordList = null;

		if (count > 0) {
					
			keywordList = result[1].split('^');
			
			var html = '';
					
			for (var i = 0 ; i < keywordList.length ; i++) {
						
				arr_keywordList = keywordList[i].split('');
				
				html += "<table width='100%' border='0'>";
				html += "<tr>";
				html += "<td style='padding:0px 5px 0px 5px' width='55px'><img src='"+arr_keywordList[2]+"' width='50' height='50' border='0'></td>";
				html += "<td>" +"<a href=\"javascript:js_select('"+ arr_keywordList[0]+"','"+arr_keywordList[1]+"')\">"+arr_keywordList[1]+"</a>" + "</td>";
				html += "<td width='55px'>"+arr_keywordList[8]+"</td>";
				html += "<td width='105px'>판매가 : "+arr_keywordList[3]+"</td>";
				html += "</tr>";
				html += "</table>";
		
				//alert(html);
			}

			var listView = document.getElementById('suggestList');
			listView.innerHTML = html;
					
			suggest.style.visibility  ="visible"; 
		} else {
			suggest.style.visibility  ="hidden"; 
		}
	}

	function js_select(selectedKey,selectedKeyword) {
		
		var frm = document.frm;

		frm.search_name.value = selectedKeyword;
		
		arr_keywordValues = selectedKey.split('');

		//frm.goods_name.value					= arr_keywordValues[0];
		frm.goods_no.value						= arr_keywordValues[1];

		loopSendKeyword = false;
		checkFirst = false;
		hide('suggest');

	}

	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
		}
	}


/*
 * @(#)menu.js
 * 
 * 페이지설명 : 메뉴 순서 바꾸기 스크립트 파일
 * 작성  일자 : 2003.12.01
 */  


var preid = -1;

function js_up(n) {
	
	preid = parseInt(n);

	if (preid > 1) {
		

		temp1 = document.getElementById("t").rows[preid].innerHTML;
		temp2 = document.getElementById("t").rows[preid-1].innerHTML;

		var cells1 = document.getElementById("t").rows[preid].cells;
		var cells2 = document.getElementById("t").rows[preid-1].cells;

		for(var j=0 ; j < cells1.length; j++) {
			
			if (j != 0) {
				var temp = cells2[j].innerHTML;
				cells2[j].innerHTML =cells1[j].innerHTML;
				cells1[j].innerHTML = temp;

				var tempCode = document.frm.sub_seq_no[preid-2].value;
				document.frm.sub_seq_no[preid-2].value = document.frm.sub_seq_no[preid-1].value;
				document.frm.sub_seq_no[preid-1].value = tempCode;
			}
		}
		
		//preid = preid - 1;
		js_change_order();

	} else {
		alert("가장 상위에 있습니다. ");
	}
}


function js_down(n) {

	preid = parseInt(n);

	//alert(preid_plus);

	if (preid < document.getElementById("t").rows.length-1) {
		
		temp1 = document.getElementById("t").rows[preid].innerHTML;
		temp2 = document.getElementById("t").rows[preid+1].innerHTML;
		
		var cells1 = document.getElementById("t").rows[preid].cells;
		var cells2 = document.getElementById("t").rows[preid+1].cells;
		
		for(var j=0 ; j < cells1.length; j++) {

			if (j != 0) {
				var temp = cells2[j].innerHTML;
				cells2[j].innerHTML =cells1[j].innerHTML;
				cells1[j].innerHTML = temp;
	
				var tempCode = document.frm.sub_seq_no[preid-1].value;
				document.frm.sub_seq_no[preid-1].value = document.frm.sub_seq_no[preid].value;
				document.frm.sub_seq_no[preid].value = tempCode;
			}
		}
		
		//preid = preid + 1;	
		js_change_order();
	} else{
		alert("가장 하위에 있습니다. ");
	}
}

function js_change_order() {
	
	if(document.getElementById("t").rows.length < 2) {
		alert("순서를 저장할 메뉴가 없습니다");//순서를 저장할 메뉴가 없습니다");
		return;
	}

	document.frm.mode.value = "O";
	document.frm.target = "ifr_hidden";
	document.frm.action = "json_goods_category.php";
	document.frm.submit();

}


</script>
</head>
<body id="popup_delivery_confirmation">

<div id="popup_delivery_confirmation">
	<div id="postsch_code">
		<div class="addr_inp">

<form name="frm" method="post">
	
	
	<input type="hidden" name="frm_page" value=""/>
	<input type="hidden" name="mode" value="" />
	<input type="hidden" name="depth" value="" />
	<input type="hidden" name="keyword" value="">
	<input type="hidden" name="goods_cate" value="<?=$goods_cate?>"/>

		<h2>검색 카테고리 관리</h2>
		<div class="btn_right">
			<input type="button" name="bb" value=" 조회 " onclick="js_search();"/>
			<!--<input type="button" name="bb" value=" 설정 초기화 " onclick="js_reset();"/>-->
			
		</div>
		<table cellpadding="0" cellspacing="0" class="colstable02">
			<col width="15%" />
			<col width="35%" />
			<col width="15%" />
			<col width="35%" />
			
			<tr>
				<th>카테고리</th>
				<td colspan="3" class="line">
					<? if($goods_cate == "") { ?>
						<?= makeCategorySelectBoxOnChange($conn, "", $exclude_category);?>
					<? } else { ?>
						
						<?
							while($max_index <= strlen($goods_cate)) {
								
								if($max_index > 2)
									echo " > ";
								echo getCategoryNameOnly($conn, left($goods_cate, $max_index));

								$max_index += 2;

							}
						?>
						<input type="button" onclick="js_clear('goods_cate')" value="해제"/>
						&nbsp;&nbsp;
						<label><input type="checkbox" name="has_sub_cate" <?if($has_sub_cate == "Y") echo "checked";?> value="Y"/>하위 카테고리 포함</label>
					<? } ?>
				</td>
			</tr>
			<tr>
				<th>상품명</th>
				<td colspan="3" class="line"  style="position:relative">
					<? if($goods_no == "") { ?>
						<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:0; visibility: hidden; width:95%; ">
							<div id="suggestList" style="height:500px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
						</div>
						<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value="" onKeyDown="startSuggest();" placeholder="상품명 또는 상품코드를 입력해주세요." />
						<input type="hidden" name="goods_no" value=""/>
						
					<? } else { ?>
						<input type="hidden" name="goods_no" value="<?=$goods_no?>"/>
						<?=getGoodsCodeName($conn, $goods_no)?>
						<input type="button" onclick="js_clear('goods_no')" value="해제"/>
					<? } ?>
				</td>
			</tr>
			<tr>
				<th>옵션</th>
				<td class="line">
					<b>페이지 : </b><input type="text" name="page" value="<?=$page?>" placeholder="페이지 번호" style="width:80px;"/>

				</td>
				<th>추가</th>
				<td class="line">
					<input type="button" name="bb" value=" 등록 " onclick="js_save();"/>
				</td>

				
			</tr>
		</table>
		<br/>

		<table id='t' cellpadding="0" cellspacing="0" class="rowstable">
			<col width="9%" />
			<col width="*" />
			<col width="10%" />
			<col width="40%" />
			<col width="6%" />
			<col width="5%" />
			<thead>
				<tr>
					<th>순서</th>
					<th>카테고리</th>
					<th>상품코드</th>
					<th>상품명</th>
					<th>페이지</th>
					<th class="end"></th>
				</tr>
			</thead>
			<tbody>
				<? 
				if(sizeof($arr_rs_goods_category) > 0) { 
					for($i = 0; $i < sizeof($arr_rs_goods_category); $i++) {
						
						$rs_no			= $arr_rs_goods_category[$i]["rn"];
						$rs_goods_cate  = $arr_rs_goods_category[$i]["CATE_CD"];
						$rs_goods_no	= $arr_rs_goods_category[$i]["GOODS_NO"];
						$rs_goods_code  = $arr_rs_goods_category[$i]["GOODS_CODE"];
						$rs_goods_name  = $arr_rs_goods_category[$i]["GOODS_NAME"];
						$rs_page		= $arr_rs_goods_category[$i]["PAGE"];
						$rs_seq			= $arr_rs_goods_category[$i]["SEQ"];

						$max_index = 2;
						?>
				<tr height="30" style="line-height:20px;">
					<td>
						
						<? if($goods_cate <> "" && $has_sub_cate != "Y" && $page != "") { ?>
							<?=($i+1)?>
							<a href="javascript:js_up('<?=($i+1)?>');"><img src="../images/admin/icon_arr_top.gif" alt="" /></a> 
							<a href="javascript:js_down('<?=($i+1)?>');"><img src="../images/admin/icon_arr_bot.gif" alt="" /></a>
						<? } else { ?>
							없음
						<? } ?>
					</td>
					<td class="modeual_nm">
					<?
						while($max_index <= strlen($rs_goods_cate)) {
							
							if($max_index > 2)
								echo " > ";
							echo getCategoryNameOnly($conn, left($rs_goods_cate, $max_index));

							$max_index += 2;
						}
					?>
					</td>
					<td><?=$rs_goods_code?></td>
					<td class="modeual_nm"><?=$rs_goods_name?>
						<input type="hidden" name="sub_seq_no" value="<?=$rs_goods_cate?>|<?=$rs_goods_no?>"/>
						<input type="hidden" name="sub_key[]" value="<?=$rs_goods_cate?>|<?=$rs_goods_no?>"/>
					</td>
					<td><?=$rs_page?></td>
					<td style="text-align:center;">
						<input type="button" name="b" value=" 삭제 " onclick="js_delete('<?=$rs_goods_cate?>', '<?=$rs_goods_no?>')"/>
					</td>
				</tr>
						<?
					}
			
					?>

				<? } else { ?>
				<tr>
					<td colspan="6" height="30" style="text-align:center;">선택된 검색 카테고리가 없거나 선택된 상품이 없습니다.</td>
				</tr>
					
				<? } ?>
				
				
			</tbody>
		</table>
		<?if($has_sub_cate == "Y" || $page == "") { ?>
			<div class="sp10"></div>
			<span style='color:red;'>순서 변경은 하위 상품 포함 해제, 페이지 입력하셔야 가능합니다.</span>
		<? } ?>
	</div>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>