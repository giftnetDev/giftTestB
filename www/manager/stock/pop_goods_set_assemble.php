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
	$menu_right = "SG007"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if($tab_index == "")
		$tab_index = 0;

	if($mode == "I")
	{
		/*
		echo "tab_index: ".$tab_index."<br/>";

		echo "goods_no: ".$goods_no."<br/>";
		echo "set_qty: ".$set_qty."<br/>";

		$row_cnt = count($sub_goods_no);
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_sub_goods_no			= $sub_goods_no[$k];
			$str_sub_goods_cnt			= $sub_goods_cnt[$k];
			$str_n_qty					= $n_qty[$k];
			$str_b_qty					= $b_qty[$k];

			echo "sub_goods_no: ".$str_sub_goods_no."<br/>";
			echo "sub_goods_cnt: ".$str_sub_goods_cnt."<br/>";
			echo "n_qty: ".$str_n_qty."<br/>";
			echo "b_qty: ".$str_b_qty."<br/>";
		}
		*/
		
		//세트 조립
		if($tab_index == 0) { 

			$stock_type     = "IN";         
			$stock_code     = "NST95";      
			$in_cp_no		= "1";	        
			$in_loc			= "LOCA";        
			$in_loc_ext	    = "세트조립입고";
			$in_qty			= $set_qty;
			$in_price	    = 0 ;   
			$in_date = date("Y-m-d",strtotime("0 day"));
			$close_tf = 'N';
			
			
			$in_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
			
			
			if($in_result) { 

				$row_cnt = count($sub_goods_no);
				for ($k = 0; $k < $row_cnt; $k++) {
				
					$str_sub_goods_no			= $sub_goods_no[$k];
					$str_sub_goods_cnt			= $sub_goods_cnt[$k];
					$str_n_qty					= $n_qty[$k];
					$str_b_qty					= $b_qty[$k];
					
					if($str_n_qty > 0) { 
						
						$stock_type     = "OUT";         
						$stock_code     = "NOUT90";      
						$in_cp_no		= "";	         
						$out_cp_no		= "1";	         
						$in_loc			= "LOCA";        
						$in_loc_ext	    = "세트조립정상출고";
						$in_qty			= 0;
						$in_price		= 0;
						$out_qty		= $str_n_qty;
						$out_bqty		= 0;
						$out_price	    = 0 ;   
						$in_date		= "";	
						$out_date		= date("Y-m-d",strtotime("0 day"));
						$close_tf = 'N';
						
						$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $str_sub_goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
					}

					if($str_b_qty > 0) { 
					
						$stock_type     = "OUT";         
						$stock_code     = "BOUT03";      
						$in_cp_no		= "";	         
						$out_cp_no		= "1";	         
						$in_loc			= "LOCA";        
						$in_loc_ext	    = "세트조립불량출고";
						$in_qty			= 0;
						$in_price		= 0;
						$out_qty		= 0;
						$out_bqty		= $str_b_qty;
						$out_price	    = 0 ;   
						$in_date		= "";	
						$out_date		= date("Y-m-d",strtotime("0 day"));
						$close_tf = 'N';
						
						$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $str_sub_goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
					}

				}

			}
			

		}

		//세트 해체
		if($tab_index == 1) { 

			$stock_type     = "OUT";         
			$stock_code     = "NOUT90";      
			$out_cp_no		= "1";	        
			$in_loc			= "LOCA";        
			$in_loc_ext	    = "세트해체출고";
			$out_qty		= $set_qty;
			$out_price	    = 0;   
			$out_date = date("Y-m-d",strtotime("0 day"));
			$close_tf = 'N';
			
			
			$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
			
			
			if($out_result) { 

				$row_cnt = count($sub_goods_no);
				for ($k = 0; $k < $row_cnt; $k++) {
				
					$str_sub_goods_no			= $sub_goods_no[$k];
					$str_sub_goods_cnt			= $sub_goods_cnt[$k];
					$str_n_qty					= $n_qty[$k];
					$str_b_qty					= $b_qty[$k];
					
					if($str_n_qty > 0) { 
						// 세트품의 구성품을 입고
						$stock_type     = "IN";         
						$stock_code     = "NST95";      
						$in_cp_no		= "1";	         
						$out_cp_no		= "";	         
						$in_loc			= "LOCA";        
						$in_loc_ext	    = "세트해체정상입고";
						$in_qty			= $str_n_qty;
						$in_bqty		= 0;
						$in_price		= 0;
						$out_qty		= 0;
						$out_price	    = 0;   
						$in_date		= date("Y-m-d",strtotime("0 day"));	
						$out_date		= "";
						$close_tf = 'N';
						
						$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $str_sub_goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
					}

					if($str_b_qty > 0) { 
						// 세트품의 구성품을 입고
						$stock_type     = "IN";         
						$stock_code     = "BST03";      
						$in_cp_no		= "1";	         
						$out_cp_no		= "";	         
						$in_loc			= "LOCA";        
						$in_loc_ext	    = "세트해체불량입고";
						$in_qty			= 0;
						$in_bqty		= $str_b_qty;
						$in_price		= 0;
						$out_qty		= 0;
						$out_price	    = 0;   
						$in_date		= date("Y-m-d",strtotime("0 day"));	
						$out_date		= "";
						$close_tf = 'N';
						
						$out_result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $str_sub_goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_tqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $order_goods_no, $rgn_no, $close_tf, $s_adm_no, $memo);
					}

				}

			}
			

		}

?>	
<script language="javascript">
	alert('완료 되었습니다.');
	location.href="pop_goods_set_assemble.php";
</script>
<?
		exit;
	}

#====================================================================
# DML Process
#====================================================================

	if($goods_no <> "") {
		$arr_rs_sub = selectGoodsSub($conn, $goods_no);
		$goods_name = getGoodsCodeName($conn, $goods_no);
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script>
	
	function js_save() { 

		var frm = document.frm;

		frm.mode.value = "I";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	$(function() {
		$( ".datepicker" ).datepicker({
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
	$(function() {
		
		$("#tabs").tabs({
			active: <?=$tab_index?>,
			
			beforeActivate: function (event, ui) {
			   window.location.href = $(ui.newTab).find('a').data('href');
			   return false;
			}
		});

	});
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
				if(arr_keywordList[8] != "판매중")
					html += "<td style='color:gray;'>" + arr_keywordList[1] + "</td>";
				else
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
		
		arr_keywordValues = selectedKey.split('');
		var goods_name =  arr_keywordValues[0];
		var goods_no =  arr_keywordValues[1];

		document.frm.search_name.value = goods_name;
		location.href = "pop_goods_set_assemble.php?goods_no=" + goods_no + "&tab_index=0#tab_1";

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

</script>
<style> 
	body#popup_file {width:100%;}
	#popupwrap_file {width:100%;}
	input[type=button] {width:95%; height:50px; margin:10px;}
	.tab_panel {padding:15px;}
	tr.sub_header{height:30px; font-weight:bold;color:gray;}
</style>
</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="keyword" value="" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="tab_index" value="<?=$tab_index?>" />
<input type="hidden" name="con_cate" value="14" /> <!--세트품만 로딩-->
<div id="popupwrap_file">
	<h1>세트 조립/해체</h1>
	<div id="postsch">

		<table cellpadding="0" cellspacing="0" width="100%" class="colstable02" border="0">

			<colgroup>
				<col width="14%" />
				<col width="*" />
			</colgroup>
			<tbody>
				<tr>
					<th>상품검색</th>
					<td style="position:relative" class="line">
						<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:1000; visibility: hidden; width:95%; ">
							<div id="suggestList" style="height:500px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
						</div>
						<input type="text" class="txt" style="width:95%; ime-mode:Active;" name="search_name" required value="<?=$goods_name?>" onKeyDown="startSuggest();" />
					</td>
				</tr>
			</tbody>
		</table>

		<div id="tabs" class="addr_inp" style="width:95%; margin:10px 0;">
			<ul>

				<li><a href="#tab_1" data-href="pop_goods_set_assemble.php?goods_no=<?=$goods_no?>&tab_index=0">조립</a></li>
				<li><a href="#tab_2" data-href="pop_goods_set_assemble.php?goods_no=<?=$goods_no?>&tab_index=1">해체</a></li>
			</ul>
			<? if($tab_index == 0) { ?>
			<div class="tab_panel">
				<table cellpadding="0" cellspacing="0" class="colstable02">
					<colgroup>
						<col width="48%" />
						<col width="*" />
						<col width="48%" />
					</colgroup>
					<tr>
						<th>구성품(출고)</th>
						<td rowspan="2" style="text-align:center;">-></td>
						<th>세트품(입고)</th>
					</tr>
					<tr style="height:100px;">
						<td>
							<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:100%;margin:10px 0 0;">
								<colgroup>
									<col width="*" />
									<col width="55px" />
									<col width="55px" />
									<col width="55px" />
								</colgroup>
								<thead>
									<tr class="sub_header">
										<td>상품명</td>
										<td>구성수</td>
										<td>정상(개)</td>
										<td>불량(개)</td>
									</tr>
								</thead>
								<tbody>
									<?
										for($i = 0; $i < sizeof($arr_rs_sub); $i++)
										{
											$sub_goods_sub_no = $arr_rs_sub[$i]["GOODS_SUB_NO"];
											$sub_goods_code = $arr_rs_sub[$i]["GOODS_CODE"];
											$sub_goods_name = $arr_rs_sub[$i]["GOODS_NAME"];
											$sub_goods_cnt	= $arr_rs_sub[$i]["GOODS_CNT"];
											
									?>
									<tr>
										<td>
											<input type="hidden" name="sub_goods_no[]" value="<?=$sub_goods_sub_no?>" />
											<input type="hidden" name="sub_goods_cnt[]" value="<?=$sub_goods_cnt?>" />
											[<?=$sub_goods_code?>] <?=$sub_goods_name?> 
										</td>
										<td><?=$sub_goods_cnt?></td>
										<td><input type="text" name="n_qty[]" value="<?=$sub_goods_cnt?>" data-goods_cnt="<?=$sub_goods_cnt?>" style="width:50px;"/></td>
										<td><input type="text" name="b_qty[]" value="0" style="width:50px;"/></td>
									</tr>
									<? } ?>
								</tbody>
							</table>
						</td>
						<td>
							<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:100%;margin:10px 0 0;">
								<colgroup>
									<col width="*" />
									<col width="55px" />
									<col width="55px" />
								</colgroup>
								<thead>
									<tr class="sub_header">
										<td>상품명</td>
										<td>수량(개)</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$goods_name?></td>
										<td><input type="text" name="set_qty" value="1" style="width:50px;"/></td>
										<td><input type="button" class="btn_change" value="변경" style="width:50px;height:19px;"/></td>
									</tr>
								</tbody>
							</table>
						</td>
						
					</tr>
				</table>
				<div class="sp10"></div>
				<b>사유 :</b> <input type="text" name="memo" value="" style="width:90%;"/>

				<div class="sp10"></div>
				
			</div>
			<? } ?>
			<? if($tab_index == 1) { ?>
			<div class="tab_panel">
				<table cellpadding="0" cellspacing="0" class="colstable02">
					<colgroup>
						<col width="48%" />
						<col width="*" />
						<col width="48%" />
					</colgroup>
					<tr>
						<th>세트품(출고)</th>
						<td rowspan="2" style="text-align:center;">-></td>
						<th>구성품(입고)</th>
					</tr>
					<tr style="height:100px;">
						<td>
							<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:100%;margin:10px 0 0;">
								<colgroup>
									<col width="*" />
									<col width="55px" />
									<col width="55px" />
									
								</colgroup>
								<thead>
									<tr class="sub_header">
										<td>상품명</td>
										<td>수량(개)</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$goods_name?></td>
										<td><input type="text" name="set_qty" value="1" style="width:50px;"/></td>
										<td><input type="button" class="btn_change" value="변경" style="width:50px;height:19px;"/></td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							
							<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:100%;margin:10px 0 0;">
								<colgroup>
									<col width="*" />
									<col width="55px" />
									<col width="55px" />
									<col width="55px" />
								</colgroup>
								<thead>
									<tr class="sub_header">
										<td>상품명</td>
										<td>구성수</td>
										<td>정상(개)</td>
										<td>불량(개)</td>
									</tr>
								</thead>
								<tbody>
									<?
										for($i = 0; $i < sizeof($arr_rs_sub); $i++)
										{
											$sub_goods_sub_no = $arr_rs_sub[$i]["GOODS_SUB_NO"];
											$sub_goods_code = $arr_rs_sub[$i]["GOODS_CODE"];
											$sub_goods_name = $arr_rs_sub[$i]["GOODS_NAME"];
											$sub_goods_cnt	= $arr_rs_sub[$i]["GOODS_CNT"];
											
									?>
									<tr>
										<td>
											<input type="hidden" name="sub_goods_no[]" value="<?=$sub_goods_sub_no?>" />
											<input type="hidden" name="sub_goods_cnt[]" value="<?=$sub_goods_cnt?>" />
											[<?=$sub_goods_code?>] <?=$sub_goods_name?> 
										</td>
										<td><?=$sub_goods_cnt?></td>
										<td><input type="text" name="n_qty[]" value="<?=$sub_goods_cnt?>" data-goods_cnt="<?=$sub_goods_cnt?>" style="width:50px;"/></td>
										<td><input type="text" name="b_qty[]" value="0" style="width:50px;"/></td>
									</tr>
									<? } ?>
								</tbody>
							</table>
						</td>
						
					</tr>
				</table>
				<div class="sp10"></div>
				<b>사유 :</b> <input type="text" name="memo" value="" style="width:90%;"/>

				<div class="sp10"></div>
			</div>
			<? } ?>
			
		</div>
		<div class="btn_right">
			<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인"></a>
		</div>
	</div>
	<br />
	<script type="text/javascript">
		$(function(){
			$(".btn_change").click(function(){

				var set_qty = $("input[name=set_qty]").val();

				var elem_n_qty = $("input[name='n_qty[]']");

				$("input[name='n_qty[]']").each(function(){
					var goods_cnt = $(this).data("goods_cnt");
					$(this).val(goods_cnt * set_qty);
				});


			});
		});
	</script>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>