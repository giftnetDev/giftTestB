<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD002"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

	if ($mode == "T") {
		updateGoodsUseTF($conn, $use_tf, $s_adm_no, $goods_no);
	}

	if ($mode == "SU") {
		$row_cnt = count($chk_no);
		for ($k = 0; $k < $row_cnt; $k++) {
			$str_goods_no = $chk_no[$k];
			$result = updateStateGoods($conn, $goods_state_mod, $str_goods_no, $s_adm_no);
		}
	}

	if ($mode == "D") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			if (isSaleGoods($conn, $str_goods_no)) {
				echo"<script>alert('saleGoods');</script>";
			} else {
				$result = deleteGoods($conn, $str_goods_no, $s_adm_no);
			}
		}
		
	}

	if ($mode == "C") {
		$row_cnt = count($chk_no);

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$str_goods_no = $chk_no[$k];
			
			$result = copyGoods($conn, $str_goods_no, $s_adm_no);
		
		}
		
	}

	if($mode == "UPDATE_NEXT_SALE_PRICE") { 

		$result = updateNextSalePrice($conn, $s_adm_no);


	}

#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		//$start_date = date("Y-m-d",strtotime("-12 month"));
		$start_date = "2010-07-24";
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

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
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	if($chk_vendor != "Y" && $txt_vendor_calc == "100")
		$txt_vendor_calc = 100;

	$arr_options = array("exclude_category" => $con_exclude_category, "vendor_calc" => $txt_vendor_calc);

	$nListCnt =totalCntGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	#$del_tf = "Y";

	$arr_rs = listGoods1($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize,$nListCnt);
	
	//MRO�ǸŰ�(TBL_GOODS_PRICE.MRO_SALE_PRICE) �߰�
	if(sizeof($arr_rs)>0){
		for($i=0;$i<sizeof($arr_rs);$i++){
			$TEMP_GOODS_NO = trim($arr_rs[$i]["GOODS_NO"]);
			$arr_rs[$i]["MRO_SALE_PRICE"] = getMroSalePrice($conn, $TEMP_GOODS_NO);
		}
	}

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04;
	$strParam = $strParam."&chk_vendor=".$chk_vendor."&txt_vendor_calc=".$txt_vendor_calc."&view_type=".$view_type."&con_exclude_category=".$con_exclude_category;

	if($result) { 
?>
	<script type="text/javascript">
		document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
	
	</script>
<?
	}
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
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	.wrong {background-color:#ffcdcf;} 

	td.DETAIL_EXISTS{
		background-color:cadetblue;
	}	

</style>

<script>
	var link;
	$(document).ready(function(){
		$("input[name=btnAccess").on("mousedown",function(){
			if($("input[name='chk_no[]']:checked").length==0){
				alert("��ǰ�� �� �� �̻� ������ �ּ���.");
			}
			else{
				confirmTF=confirm("������ ��ǰ���� �ֽ�ȭ�� �����Ͻðڽ��ϱ�?");
				if(!confirmTF) return;
				var goods_nos= new Array();
				$("input[name='chk_no[]']:checked").each(function(){
					goods_nos.push($(this).val());
				});
				$.ajax({
					url:'/manager/ajax_processing.php',
					dataType: 'json',
					type: 'POST',
					data:{
						'mode': "UPDATE_ACCESS_TIME",
						'goods_nos': goods_nos
					},
					success: function(response){
						alert('���� �Ϸ�');

					},
					error: function(jqXHR, textStatus, errorThrown){
						alert('���� ����');

					}
				});
			}
		});
		$("#make_link_btn").on("mousedown", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				alert("��ǰ�� �Ѱ� �̻� �������ּ���.");
			} else {
				var goods_no = new Array();
				var reg_adm = "<?=$s_adm_no?>";
				$("input[name='chk_no[]']:checked").each(function(){
					goods_no.push($(this).val());
				});
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
					'mode': "INSERT_LINK",
					'goods_no': goods_no,
					"reg_adm": reg_adm
					},
					success: function(response) {
						if(response != false){
							link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info.php?key=" + response;
							var $temp = $("<input id='clipboard' />");
							$("body").append($temp);
							$('#clipboard').val(link);
						} else{
							alert("�����Ͽ����ϴ�.");
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});//ajax
			}//else
		});//make_link_btn mousedown
		
		$("#make_link_btn").on("click", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				// alert("��ǰ�� �Ѱ� �̻� �������ּ���."); //��� �� ���� ���
			} else {
				setTimeout(function() {
					var $input = $("#clipboard");
					if ($input.length && $input.val().length > 0) {
						$input.select();
						document.execCommand("copy");
						$input.remove();
					}
					window.open(link, '_blank', 'width=450, height=768');
					// alert("��ũ�� ����Ǿ����ϴ�.\n\n"+link);
					link="";
				}, 100);
			}
		});

		$("#make_image_btn").on("click", function() {
			if($("input[name='chk_no[]']:checked").length == 0){
				alert("��ǰ�� �Ѱ� �̻� �������ּ���.");
			} else {
				var goods_no = new Array();
				var reg_adm = "<?=$s_adm_no?>";
				$("input[name='chk_no[]']:checked").each(function(){
					goods_no.push($(this).val());
				});
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
					'mode': "INSERT_LINK",
					'goods_no': goods_no,
					"reg_adm": reg_adm
					},
					success: function(response) {
						if(response != false){
							link = "https://www.giftnet.co.kr/manager/goods/pop_simple_goods_info_image.php?key=" + response;
							window.open(link, '_blank', 'width=450, height=768');
						} else{
							alert("�����Ͽ����ϴ�.");
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});//ajax
			}//else
		});//make_image_btn click
	});//ready
	$(function() {
		$( ".datepicker" ).datepicker({
			showOn: "button",
			buttonImage: "/manager/images/calendar/cal.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			beforeShow: function() {
				setTimeout(function(){
					$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});
	});
  
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
	
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});

  </script>
  <script type="text/javascript" >

	function js_write() {
		document.location.href = "goods_write.php";
	}

	function js_view(rn, goods_no) {
		// $.ajax({
		// 			url: '/manager/ajax_processing.php',
		// 			dataType: 'json',
		// 			type: 'post',
		// 			data : {
		// 			'mode': "UPDATE_ACCESS_TIME",
		// 			'goods_no': goods_no,
		// 			},
		// 			success: function(response) {
		// 				if(response != false){
							
		// 				} else{
		// 					alert("�����Ͽ����ϴ�.");
		// 				}
		// 			}, error: function(jqXHR, textStatus, errorThrown) {
		// 				console.log(jqXHR.responseText); 
		// 			}
		// 		});//ajax

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_write.php";
		frm.submit();
		
	}

	function js_view_company(cp_no) {

		window.open(
		  "/manager/company/company_write.php?mode=S&cp_no=" + cp_no,
		  '_blank' 
		);
		
	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

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

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_exclude_category() {
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

		frm.con_exclude_category.value = frm.con_cate.value;
		frm.con_cate.value = "";

		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	function js_toggle(goods_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('��� ���θ� ���� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.goods_no.value = goods_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_excel() {
		
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
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

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('������ ��ǰ�� ���� �Ͻðڽ��ϱ�?\nüũ�ڽ��� ������ �ϼ̾ ��ǰ �Ǹ� ������ ���� ��� ���� ���� ���� �� �ֽ��ϴ�.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_state_mod() {
		var frm = document.frm;

		bDelOK = confirm('������ ��ǰ �Ǹ� ���¸� ���� �Ͻðڽ��ϱ�?\n');
		
		if (bDelOK==true) {
			
			frm.mode.value = "SU";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_copy() {
		var frm = document.frm;

		bDelOK = confirm('������ ��ǰ�� ���� �Ͻðڽ��ϱ�? ��ǰ ����� �⺻ ������ ���������� ����˴ϴ�.');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_update_next_sale_price() { 

		var frm = document.frm;

		bOK = confirm('���� ������ ���� �������� �ǸŰ��� �ϰ� ������Ʈ �մϴ�. �����ϰ� �������ּ���.');
		
		if (bOK==true) {
			
			frm.mode.value = "UPDATE_NEXT_SALE_PRICE";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_layer_show(goods_no, img_url) {
		
		var origin_img = img_url.replace("simg/s_50_50_","");
		$("#l_"+goods_no).html("<img src='"+origin_img+"'>").show();

	}

	function js_layer_hide(goods_no) {
		$("#l_"+goods_no).hide();
	}


	function js_batch_modify() {
		var url = "/manager/goods/goods_batch_modify.php";
		var frm = document.frm;
		NewWindow('about:blank', 'modify_batch_popup', '860', '513', 'YES');
		frm.mode.value = "";
		frm.target = "modify_batch_popup";
		frm.action = url;
		frm.submit();
	}

	function js_link_to_item_ledger(goods_no, order_date) { 

		window.open("/manager/stock/item_ledger_list.php?nPage=1&nPageSize=20&search_field=GOODS_NO&start_date="+order_date+"&search_str=" + goods_no,'_blank');

	}

	function js_search_postback() {
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

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_pop_goods_ordering_list(goods_no) {

		var url = "pop_goods_ordering_list.php?goods_no=" + goods_no;
		NewWindow(url,'pop_goods_ordering_list','800','450','YES');

	}

	/*
	function js_sub_goods_modify() {

		var url = "goods_sub_goods_modify.php";
		NewWindow('about:blank', 'sub_goods_modify_popup', '860', '513', 'YES');
		var frm = document.frm;
		frm.target = "sub_goods_modify_popup";
		frm.action = url;
		frm.submit();
	}
	*/

	function js_goods_proposal() { 

		var frm = document.frm;

		frm.target = "_blank";
		frm.method = "post";
		frm.action = "/manager/proposal/goods_proposal_write.php";
		frm.submit();
	}

	function js_reload() {
		location.reload();
	}

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 400 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"?v="+(new Date()).getTime()+"' style='max-height:800px; max-width:600px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		var win;
		$(".goods_thumb").click(function() {
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');
			window.setTimeout('check()',1000);

		});

		function check() {
			if(win.document.readyState =='complete'){
				win.document.execCommand("SaveAs");
				win.close();
			} else { 
				window.setTimeout('check();',1000);
			}
		}

		$(window).scroll(function() {
		   img_frame.empty().hide();
		});

	});
</script>

</head>

<body id="admin">
<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="rn" value="">
<input type="hidden" name="depth" value="">
<input type="hidden" name="goods_no" value="">
<input type="hidden" name="use_tf" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">

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

				<h2>��ǰ ���� 5678</h2>
				<div class="btnright">
				<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���" /></a>
				<? } ?>
				</div>
				<div class="category_choice">&nbsp;</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
					<col width="*" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th>ī�װ�</th>
						<td colspan="3">
							<?= makeCategorySelectBoxOnChange($conn, $con_cate, $con_exclude_category);?>
							<input type="hidden" name="con_cate" value="<?=$con_cate?>">
							<input type="button" value="����" onclick="js_exclude_category();"/>
							<input type="hidden" name="con_exclude_category" value="<?=$con_exclude_category?>"/>
							<span class="exception">
							<?
								if($con_exclude_category <> "") { 
									$max_index = 0;
									while($max_index <= strlen($con_exclude_category)) {
												
										if($max_index > 2)
											echo " > ";
										echo getCategoryNameOnly($conn, left($con_exclude_category, $max_index));

										$max_index += 2;

									}
							?>
							
							<a href="#" id="clear_exclude_category">(X)</a>
							</span>
							<?

								}
							?>
						</td>
						<td><input type="checkbox" name="con_use_tf" value="Y" <?if($con_use_tf == "Y") echo "checked";?>/>����ǰ</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>�����</th>
						<td colspan="2">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
						<td colspan="2" align="right">
							
							<select name="print_type" style="width:84px;">
								<option value="FOR_REG" <? if ($print_type == "FOR_REG") echo "selected"; ?> >��ǰ��Ͽ뼭��</option>
								<option value="FOR_REG_NO_SUB" <? if ($print_type == "FOR_REG_NO_SUB") echo "selected"; ?> >��ǰ��Ͽ뼭��(��Ʈ����)</option>
								<option value="FOR_PRINT" <? if ($print_type == "FOR_PRINT") echo "selected"; ?> >��¿�</option>
								<option value="FOR_PRINT_NO_SUB" <? if ($print_type == "FOR_PRINT_NO_SUB") echo "selected"; ?> >��¿�(��Ʈ����)</option>
								<option value="FOR_CATALOG" <? if ($print_type == "FOR_CATALOG") echo "selected"; ?> >ī�޷α׿�</option>
								<option value="FOR_CATALOG_NO_SUB" <? if ($print_type == "FOR_CATALOG_NO_SUB") echo "selected"; ?> >ī�޷α׿�(��Ʈ����)</option>
								<option value="DISPLAY" <? if ($print_type == "DISPLAY") echo "selected"; ?> >ȭ��״��</option>
							</select>&nbsp;
							
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
					<tr>
						<th>�ǸŻ���</th>
						<td>
							<?= makeSelectBox($conn,"GOODS_STATE","con_cate_04","125","����","",$con_cate_04)?>
							&nbsp;&nbsp;
							<label>�����ǸŰ� ���� <input type="checkbox" name="chk_next_sale_price" value="Y" <?if($chk_next_sale_price == "Y") echo "checked"; ?>/></label>
						</td>
						<th>��������</th>
						<td colspan="2">
							<?= makeSelectBox($conn,"TAX_TF","con_tax_tf","125","����","",$con_tax_tf)?>
						</td>
					</tr>

					<tr>
						<th>�ǸŰ� </th>
						<td>
							<input type="text" value="<?=$start_price?>" name="start_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> �� ~
							<input type="text" value="<?=$end_price?>" name="end_price" style="width: 75px;" class="txt" onkeyup="return isNumber(this)" /> ��

							 (<input type="checkbox" name="chk_vendor" <? if($chk_vendor == "Y") echo "checked"; ?> value="Y"/> ��������<input type="text" name="txt_vendor_calc" value="<?=$txt_vendor_calc?>" class="txt" style="width:25px;"/>%)
							
						</td>
						<th>���޾�ü</th>
						<td colspan="2">
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cate_03)?>" />
							<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){
										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cate_03]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('����,�ǸŰ���') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=����,�ǸŰ���&search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cate_03]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}

							</script>
							
						</td>
					</tr>

					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >�����</option>
								<option value="GOODS_NAME" <? if ($order_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="GOODS_NO" <? if ($order_field == "GOODS_NO") echo "selected"; ?> >��ǰ��ȣ</option>
								<option value="GOODS_CODE" <? if ($order_field == "GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="BUY_PRICE" <? if ($order_field == "BUY_PRICE") echo "selected"; ?> >���԰�</option>
								<option value="SALE_PRICE" <? if ($order_field == "SALE_PRICE") echo "selected"; ?> >�ǸŰ�</option>
								<option value="EXTRA_PRICE" <? if ($order_field == "EXTRA_PRICE") echo "selected"; ?> >�����հ�</option>
								<option value="CP_NAME" <? if ($order_field == "CP_NAME") echo "selected"; ?> >���޾�ü</option>
								<option value="STOCK_CNT" <? if ($order_field == "STOCK_CNT") echo "selected"; ?> >���</option>
								<option value="UP_DATE" <? if ($order_field == "UP_DATE") echo "selected"; ?> >������</option>
								<option value="SALE_COUNT" <? if ($order_field == "SALE_COUNT") echo "selected"; ?> >#�ֹ�ȸ��</option>
								<option value="SALE_AMOUNT" <? if ($order_field == "SALE_AMOUNT") echo "selected"; ?> >#�ֹ�����</option>
								<option value="SALE_TOTAL" <? if ($order_field == "SALE_TOTAL") echo "selected"; ?> >#�ֹ��հ�</option>
								<option value="VENDOR_PRICE" <? if ($order_field == "VENDOR_PRICE") echo "selected"; ?> >*������(����üũ)</option>
								<option value="MAJIN" <? if ($order_field == "MAJIN") echo "selected"; ?> >*����</option>
								<option value="MAJIN_RATE" <? if ($order_field == "MAJIN_RATE") echo "selected"; ?> >*������</option>
								<option value="CATALOG" <? if($order_field=="CATALOG") echo "selected"; ?> >**īŻ�α�</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if ($order_str == "ASC") echo " checked"; ?>> �������� &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?> > �������� 
						</td>
						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��</option>
								<option value="GOODS_NO" <? if ($search_field == "GOODS_NO") echo "selected"; ?> >��ǰ��ȣ</option>
								<option value="GOODS_CODE" <? if ($search_field == "GOODS_CODE") echo "selected"; ?> >��ǰ�ڵ�</option>
								<option value="MEMO" <? if ($search_field == "MEMO") echo "selected"; ?> >����</option>
								<option value="GOODS_NAME_AND" <? if ($search_field == "GOODS_NAME_AND") echo "selected"; ?> >*��ǰ��(AND)</option>
								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >*���޻��ڵ�</option>
								<option value="GOODS_SUB_NO" <? if ($search_field == "GOODS_SUB_NO") echo "selected"; ?> >*���Ի�ǰ��ȣ</option>
								<option value="GOODS_SUB_CODE" <? if ($search_field == "GOODS_SUB_CODE") echo "selected"; ?> >*���Ի�ǰ�ڵ�</option>
								<option value="GOODS_SUB_CODE_AND" <? if ($search_field == "GOODS_SUB_CODE_AND") echo "selected"; ?> >*���Ի�ǰ�ڵ�(AND)</option>
								<option value="GOODS_SUB_NAME_AND" <? if ($search_field == "GOODS_SUB_NAME_AND") echo "selected"; ?> >*���Ի�ǰ��(AND)</option>
								<option value="SUB_CP_CODE" <? if ($search_field == "SUB_CP_CODE") echo "selected"; ?> >*���԰��޻��ڵ�</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
	
				�� <?=number_format($nListCnt)?> ��

				<div style="float:right; margin-right:60px;">
					���� : 
					<select name="view_type" onchange="js_search_postback();">
						<option value="price" <? if ($view_type == "price" || $view_type == "") echo "selected"; ?> >����</option>
						<option value="stock" <? if ($view_type == "stock") echo "selected"; ?> >���</option>
					</select>
				<!--(�ڽ����� �ֹ�)  VAT����, ���������� �ڽ����� �̸� �ֹ��� �����񺰵�-->
				</div>
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					

					<colgroup>
						<col width="2%" />
						<col width="5%" />
						<col width="5%" />
						<col width="8%" />
						<col width="*"/>
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<col width="5%" />
						<? if($view_type == "price" || $view_type == "") { ?>
							<!-- ���� ����� ������ ��� -->
							<col width="5%" />
							<col width="7%" />
							<col width="5%"/>
							<col width="5%" />
							<col width="5%" />
						<?} else {?>
							<!-- ���� ����� ����� ��� -->
							<col width="5%" />
							<col width="5%"/>
							<col width="5%" />
							<col width="6%" />
							<col width="10%" />
						<?}?>
					</colgroup>
					<thead>
						<? if($view_type == "price" || $view_type == "") { ?>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>��ǰ��ȣ</th>
							<th>�̹���</th>
							<th>��ǰ�ڵ�</th>
							<th>��ǰ��</th>
							<th>���޻�</th>
							<th>���԰�</th>
							<th>�����հ�</th>
							<th>
								<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
									<b>�ǸŰ�</b>
								<? } else { ?>
									�ǸŰ�
									<? if ($chk_vendor == "Y") {?>
										<br/><b>������</b>��
									<? } ?>
									<? if($chk_next_sale_price == "Y") { ?>
										<br/><b>�����ǸŰ�</b>
									<? } ?>
								<? } ?>
							</th>
							<th>����</th>
							<th>������</th>
							<th>MRO�ǸŰ�</th>
							<th>�ڽ��Լ�</th>
							<?if($order_field == "SALE_COUNT") { ?>
								<th><b>�ֹ�ȸ��</b></th>
							<? } else if ($order_field == "SALE_AMOUNT") {  ?>
								<th><b>�ֹ�����</b></th>
							<? } else if ($order_field == "SALE_TOTAL") { ?>
								<th><b>�ֹ��հ�</b></th>
							<? } else { ?>
								<th>���</th>
							<? } ?>
							<th class="end">�ǸŻ���</th>
						</tr>
						<? } ?>
						<? if ($view_type == "stock") { ?>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>��ǰ��ȣ</th>
							<th>�̹���</th>
							<th>��ǰ�ڵ�</th>
							<th>��ǰ��</th>
							<th>�ڽ��Լ�</th>
							<th>�ּ����</th>
							<th>�����</th>
							<th>�����</th>
							<th>�������</th>
							<th>�ҷ����</th>
							<th>�������</th>
							<th>�ǸŻ���</th>
							<th>����Ȯ��</th>
							<th class="end">������ȸ�ð�</th>
						</tr>
						<? } ?>
					</thead>
					

					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							$rn								= trim($arr_rs[$j]["rn"]);
							$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
							$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
							$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
							$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
							$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
							$CATE_01					= trim($arr_rs[$j]["CATE_01"]);
							$CATE_02					= trim($arr_rs[$j]["CATE_02"]);
							$CATE_03					= trim($arr_rs[$j]["CATE_03"]);
							$CATE_04					= trim($arr_rs[$j]["CATE_04"]);
							$PRICE						= trim($arr_rs[$j]["PRICE"]);
							$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
							$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
							$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
							$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);
							$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
							$TAX_TF						= trim($arr_rs[$j]["TAX_TF"]);
							$IMG_URL					= trim($arr_rs[$j]["IMG_URL"]);
							$FILE_NM					= trim($arr_rs[$j]["FILE_NM_100"]);
							$FILE_RNM					= trim($arr_rs[$j]["FILE_RNM_100"]);
							$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
							$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
							$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
							$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
							$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
							$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
							$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
							$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$CONTENTS					= trim($arr_rs[$j]["CONTENTS"]);
							$READ_CNT					= trim($arr_rs[$j]["READ_CNT"]);
							$DISP_SEQ					= trim($arr_rs[$j]["DISP_SEQ"]);
							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF						= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);

							$STOCK_TF				= trim($arr_rs[$j]["STOCK_TF"]);
							$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
							$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
							$MSTOCK_CNT				= trim($arr_rs[$j]["MSTOCK_CNT"]);

							//2018-11-30 �ֹ�ȸ��, �ֹ�����, �ֹ��հ�
							$SALE_COUNT				= trim($arr_rs[$j]["SALE_COUNT"]);
							$SALE_AMOUNT			= trim($arr_rs[$j]["SALE_AMOUNT"]);
							$SALE_TOTAL				= trim($arr_rs[$j]["SALE_TOTAL"]);

							//echo "SALE_COUNT : ".$SALE_COUNT.", SALE_AMOUNT : ".$SALE_AMOUNT.", SALE_TOTAL : ".$SALE_TOTAL."<br/>";
	
							/* 2015 9�� 08�� �߰�*/
							$STICKER_PRICE		= trim($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE		= trim($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE		= trim($arr_rs[$j]["DELIVERY_PRICE"]); 

							/* 2016 2�� 18�� �߰�*/
							$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= trim($arr_rs[$j]["OTHER_PRICE"]);

							//2016-10-20 �߰�
							$RESTOCK_DATE			= trim($arr_rs[$j]["RESTOCK_DATE"]);

							$NEXT_SALE_PRICE		= trim($arr_rs[$j]["NEXT_SALE_PRICE"]);

							$str_goods_no = $GOODS_TYPE.substr("000000".$GOODS_NO,-5);
							$ACCESS_DATE = trim($arr_rs[$j]["UP_DATE"]);
							$ACCESS_DATE = date("Y-m-d",strtotime($ACCESS_DATE));

							$convertedGoodsCode	=	str_replace("-", "_", $GOODS_CODE);
							$detailURL			=	$_SERVER['DOCUMENT_ROOT']."/upload_data/goods_image/detail/".$convertedGoodsCode.".jpg";

							if(file_exists($detailURL)){
								$DETAIL_TF="DETAIL_EXISTS";
							}
							else{
								$DETAIL_TF="";
							}
							?>

							<?

							//echo $IMG_URL;

							// �̹����� ���� �Ǿ� ���� ���
							$img_url	= getGoodsImage($FILE_NM, $IMG_URL, $FILE_PATH_150, $FILE_RNM_150, "50", "50");

							if ($USE_TF == "Y") {
								$STR_USE_TF = "<font color='navy'>�����</font>";
							} else {
								$STR_USE_TF = "<font color='red'>������</font>";
							}

							if ($TAX_TF == "�����") {
								$STR_TAX_TF = "<font color='orange'>(�����)</font>";
							} else {
								$STR_TAX_TF = "<font color='navy'>(����)</font>";
							}

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

							if($RESTOCK_DATE <> '0000-00-00 00:00:00' && $CATE_04 != "����")
								$RESTOCK_DATE = "<span style='color:#0047ab;'>(".date("Y-m-d",strtotime($RESTOCK_DATE)).")</span>";
							else
								$RESTOCK_DATE = '';
							
							if($BUY_PRICE + $STICKER_PRICE + $PRINT_PRICE + round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX) + $LABOR_PRICE + $OTHER_PRICE != $PRICE)
								$str_wrong_style = "wrong";
							else
								$str_wrong_style = "";


							if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
								$DELIVERY_PER_PRICE = 0;
							else 
								$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
							
							$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

							$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

							if($SALE_PRICE != 0)
								$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2)."%";
							else 
								$MAJIN_PER = "���Ұ�";
							
							if($USE_TF == "N")
								$str_use_style = "unused";
							else { 
								if($CATE_04 != "�Ǹ���")
									$str_use_style = "expired";
								else
									$str_use_style = "";
							}

							if($chk_vendor == "Y") {
								$DC_RATE = $txt_vendor_calc;
								$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
							} else {
								$DC_RATE = "";
								$VENDER_PRICE = $SALE_PRICE;
							}

							if($view_type == "stock")
								$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);
							else
								$TSTOCK_CNT = 0;
							
				
				?>

						<? if($view_type == "price" || $view_type == "") { ?>
						<tr class="<?=$str_use_style?> <?=$str_wrong_style?>" >
							<td>
								<input type="checkbox" name="chk_no[]" class="chk" value="<?=$GOODS_NO?>">
							</td>
							<td class="<?=$DETAIL_TF?>">
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="Ŭ���Ͻø� �� â�� ���� �̹����� �����ϴ�." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><a href="javascript:js_view_company(<?=$CATE_03?>)"><?= getCompanyName($conn, $CATE_03);?></a></td>
							<td class="price"><?= number_format($BUY_PRICE) ?> ��</td>
							<td class="price"><?= number_format($PRICE) ?> ��</td>
							<td class="price">

								<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
									<b><?= number_format($SALE_PRICE) ?> ��</b>
								<? } else { ?>
									<?= number_format($SALE_PRICE) ?> ��
									<? if ($chk_vendor == "Y") {?>
										<br/><b><?= number_format($VENDER_PRICE)?> ��</b>
									<? } ?>
									<?  if($chk_next_sale_price == "Y") { 
											if($NEXT_SALE_PRICE == "") { 
									?>
										<br/><b>����</b>
									<?	    } else { ?>
										<br/><b><?= getSafeNumberFormatted($NEXT_SALE_PRICE) ?> ��</b>
									<?      }
										}
									?>
								<? } ?>
							</td>
							<td class="price" title="������:<?=$SUSU_PRICE?>"><?= number_format($MAJIN) ?> ��</td>
							<td class="price" title="������:<?=$SUSU_PRICE?>"><?=$MAJIN_PER?></td>
							<td class="price" title="MRO�ǸŰ�:<?=$MRO_SALE_PRICE?>"><?= number_format($MRO_SALE_PRICE) ?> ��</td>
							<td><?= $DELIVERY_CNT_IN_BOX ?></td>

							<? if($order_field == "SALE_COUNT") { ?>
								<td class="price"><b><?= number_format($SALE_COUNT) ?></b></td>
							<? } else if ($order_field == "SALE_AMOUNT") {  ?>
								<td class="price"><b><?= number_format($SALE_AMOUNT) ?></b></td>
							<? } else if ($order_field == "SALE_TOTAL") {  ?>
								<td class="price"><b><?= number_format($SALE_TOTAL) ?></b></td>
							<? } else { ?>
								<td class="price"><?= number_format($STOCK_CNT) ?></td>
							<? } ?>

							
							<td class="filedown">
								<?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?>
								<? if($CATE_04 != "�Ǹ���") {?>	
									<br/><?=$RESTOCK_DATE?>
								<? } ?>
							</td>
						</tr>
						<? } ?>
						<? if ($view_type == "stock") { ?>
						<tr class="<?=$str_use_style?> <?=$str_wrong_style?>" >
							<td>
								<input type="checkbox" name="chk_no[]" value="<?=$GOODS_NO?>">
							</td>
							<td>
								<?=$GOODS_NO?>
							</td>
							<td style="padding: 1px 1px 1px 1px">
								<img src="<?=$img_url?>" title="Ŭ���Ͻø� �� â�� ���� �̹����� �����ϴ�." data-thumbnail="<?=$img_url?>" class="goods_thumb" width="50px" height="50px">
							</td>
							<td><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><a href="javascript:js_view('<?= $rn ?>','<?= $GOODS_NO ?>');"><?=$STR_TAX_TF?> <?= $GOODS_NAME ?> <?= $GOODS_SUB_NAME ?></a></td>
							<td><?= $DELIVERY_CNT_IN_BOX ?></td>
							<td class="price"><?= number_format($MSTOCK_CNT) ?> </td>
							<td class="price"><?= ($TSTOCK_CNT > 0 ? "<a href=\"javascript:js_pop_goods_ordering_list('".$GOODS_NO."');\">-".number_format($TSTOCK_CNT)."</a>" : 0) ?> </td>
							<td class="price"><?= number_format($FSTOCK_CNT) ?> </td>
							<td class="price"><?= number_format($STOCK_CNT) ?> </td>
							<td class="price"><?= number_format($BSTOCK_CNT) ?> </td>
							<td class="price"><b><?= number_format($STOCK_CNT + $FSTOCK_CNT - $TSTOCK_CNT) ?></b> </td>
							<td class="filedown">
								<?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?>
								<? if($CATE_04 != "�Ǹ���") {?>	
									<br/><?=$RESTOCK_DATE?>
								<? } ?>
							</td>
							<td class="modeual_nm">
								<input type="button" name="b" value="��ȸ" class="btntxt" onclick="js_link_to_item_ledger('<?=$GOODS_NO?>', '<?=$REG_DATE?>');">
							</td>
							<td class="modeual_nm"><?=$ACCESS_DATE?></td>
							
						</tr>

						<? } ?>
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="14">�����Ͱ� �����ϴ�. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					
					<? if ($sPageRight_D == "Y") {?>
						<input type="button" name="aa" value="������ ��ǰ" class="btntxt" onclick="js_state_mod();">
						<?= makeSelectBox($conn,"GOODS_STATE","goods_state_mod","125","���¼���","","")?>
						<input type="button" name="aa" value="���� ����" class="btntxt" onclick="js_state_mod();">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<input type="button" name="aa" value=" ������ ��ǰ ���� " class="btntxt" onclick="js_copy();"> 
						<input type="button" name="aa" value=" ������ ��ǰ ���� " class="btntxt" onclick="js_delete();">
					<? } ?>
				</div>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<input type="button" name="" value=" ������ ��ǰ���� �̹����� ���� " class="btntxt" id ="make_image_btn">
					<input type="button" name="" value=" ������ ��ǰ���� ��ũ�� ����� " class="btntxt" id ="make_link_btn">
					<input type="button" name="aa" value=" ������ ��ǰ���� �����ϱ� " class="btntxt" onclick="js_goods_proposal();">
					<input type="button" name="aa" value="������ ��ǰ���� �ϰ�����" class="btntxt" onclick="js_batch_modify();">
					<input type="button" name="btnAccess" value="������ ��ǰ �ֽ�ȭ Ȯ��" class="btntxt">
				</div>

				
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
							$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf."&chk_vendor=".$chk_vendor."&txt_vendor_calc=".$txt_vendor_calc."&view_type=".$view_type."&con_exclude_category=".$con_exclude_category."&chk_next_sale_price=".$chk_next_sale_price;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
			</div>
			<div class="sp50"></div>
			<!--<? if ($sPageRight_F == "Y") {?>
				<div><input type="button" name="aa" value=" ���� ������ ���� ���� �ǸŰ� �ϰ� ������Ʈ " class="btntxt" onclick="js_update_next_sale_price();"> </div>
				<div class="sp50"></div>
			<? } ?>--	//20210805 ȿ��븮��û ��ư ���� ��Ŵ.		>
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	<script>

		$(function(){

			$("#clear_exclude_category").click(function(e){

				e.preventDefault();
				$("input[name=con_exclude_category]").val('');
				$(".exception").html('');

				js_search();

			});

			//��ü �ε��� Ŭ�� ����
			$("input[name=all_chk]").show();

			$("input[name=txt_vendor_calc]").keyup(function(){
				if($(this).val() != "")
					$("input[name=chk_vendor]").prop("checked", true);
				else
					$("input[name=chk_vendor]").prop("checked", false);
			});


			var last_click_idx = -1;
			$(".chk").click(function(event){
				
				var clicked_elem = $(this);
				var clicked_elem_chked = $(this).prop("checked");

				var start_idx = -1;
				var end_idx = -1;
				var click_idx = -1;

				$(".chk").each(function( index, elem ) {

					//Ŭ����ġ ����
					if(clicked_elem.val() == $(elem).val())
						click_idx = index;

				});

				if(event.shiftKey) {

					if($(".chk:checked").size() >= 2) {
						$(".chk").each(function( index, elem ) {

							//üũ�� ���� ���� üũ
							if(start_idx == -1 && $(elem).prop("checked"))
								start_idx = index;

							//üũ�� ������ �ε��� üũ
							if($(elem).prop("checked"))
								end_idx = index;

						});

						if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
							start_idx = click_idx;

						if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
							end_idx = click_idx;


						//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

						
						$(".chk").each(function(index, elem) {

							if(start_idx <= index && index <= end_idx) {
								$(elem).prop("checked", true);
							}
							else
								$(elem).prop("checked", false);
							
						});
						
					}

					last_click_idx = click_idx;
				}

			});

		});
	
	</script>
	<a style="display:scroll;position:fixed;bottom:10px;right:10px;" href="#">�� ����</a>
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