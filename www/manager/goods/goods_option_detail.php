<?session_start();?>
<?
header("Pragma;no-cache");
header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD021"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/goods/goods_option.php";
	require "../../_classes/biz/admin/admin.php";


?>

<?

		#user_paramenter
		$stiker_type = trim($stiker_type);

		if($tab_index == "")
		$tab_index = 0;

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<style>
	.wordbreak{
		word-wrap:break-word;
	}

#ClickWrapOne {
	width: 40%;
	float: left;
	border:0px solid #d1d1d1;	
}


#WrapOne {
	z-index: 1;
	overflow: auto;
	width: 40%;
	height:420px;
	float: left;
	border:1px solid #d1d1d1;
	overflow: scroll;
	overflow-y: visible;
	overflow-x: hidden;
}

#ClickWrapTwo {
	width: 55%;
	float: left;
	border:0px solid #d1d1d1;	
	margin-left: 20px;
}

#WrapTwo {
	overflow: auto;
	width: 55%;
	height:420px;
	float: left;
	border:1px solid #d1d1d1;
	overflow: scroll;
	overflow-y: visible;
	overflow-x: hidden;
	margin-left: 20px;
}

#ClickStikerOne {
	width: 40%;
	float: left;
	border:0px solid #d1d1d1;	
}

#StikerOne {
	z-index: 1;
	overflow: auto;
	width: 40%;
	height:420px;
	float: left;
	border:1px solid #d1d1d1;
	overflow: scroll;
	overflow-y: visible;
	overflow-x: hidden;
}

#ClickStikerTwo {
	width: 55%;
	float: left;
	border:0px solid #d1d1d1;	
	margin-left: 20px;
}

#StikerTwo {
	 overflow: auto;
	width: 55%;
	height:420px;
	float: left;
	border:1px solid #d1d1d1;
	overflow: scroll;
	overflow-y: visible;
	overflow-x: hidden;
	margin-left: 20px;
}

.fixedHeader {
	position: sticky;
	top: 0;
}

table.rowstable {
    width: 100%;
}

table td.contentarea h2 {
	margin: 0 0 0px 0;
}

</style>
<script>
	$(document).ready(function(){
		$("#tabs").tabs({
		  active : <?=$tab_index?>
		});
		
		fn_goods_search();
		fn_goodtp_select();
		fn_search_list1();
		fn_search_list2();
		igmShow();
		onload();
	});

</script>
<script type="text/javascript">

	function onload()
	{
		if(document.frm.optionCf.value == "Y")
		{
			document.getElementById("span1").style.display ="";
			document.getElementById("span1").innerHTML = "옵션 완료 시간 ["+document.frm.optionConfirmDt.value+"]";
		}
		return;
	}

	function fn_goods_search()
	{
		var frm = document.frm;
		
		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: false,
			data : {
						mode: "SELECT_GOODS"
					, goods_no : frm.goods_no.value
			},
			success: function(data) 
			{
				$.each(data, function(i, item)
				{
					if(item.RESULT == "Y")
					{
						document.getElementById("goods_name").innerHTML = item.GOODS_NAME;
						document.getElementById("goods_code").innerHTML = item.GOODS_CODE;
						document.getElementById("tax_tf").innerHTML = item.TAX_TF;
						document.getElementById("cate_03").innerHTML = item.CATE_03;
						document.getElementById("sale_price").innerHTML = item.SALE_PRICE+" 원";
						document.getElementById("delivery_cnt_in_box").innerHTML = item.DELIVERY_CNT_IN_BOX;						
						frm.optionCf.value = item.OPTION_CF;						
						frm.optionConfirmDt.value = item.OPTION_DATE;
						$("#mainImg").attr("src", item.IMG_URL);
					}
					else
					{
						alert("error");
						return ;
					}
				});
			}	,
			fail : function(jqXHR, textStatus, errorThrown)
			{
				alert('통신 실패');
				return;
			}
		});
	}

	function fn_goodtp_select()
	{
		var frm = document.frm;
			var stiker_type = $("#stiker_type").val();
			var cnt;
			var empty_option = "<option value =''></option>전체";
			$.ajax({
				url: 'json_goods_option.php',
				dataType: 'json',
				type: 'post',
				data: {
					mode: "SELECT_GOODS_BOX",
					stiker_type : stiker_type
				},
				success: function(data){
					// alert("success");
					js_selectbox(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText);
				}
			});
	}

	function js_selectbox(response)
	{
		var cnt = response.length;
		var str = "<OPTION value=''>전체</OPTION>";
	
		for (var i = 0; i < cnt; i++) 
		{
			str += "<option value = '" + response[i]["CATE_CD"] + "'>" + response[i]["CATE_NAME"] + "</option>";
		} 
		str+="";

		$("#stiker_type").html(str);
	}

	function fn_search_list1()
	{
		var frm = document.frm;
		
		$.ajax({
					url: "json_goods_option.php",
					dataType: 'json',
					type: 'post',
					async: false,
					data : {
							  mode: "SEL_STIKER"
							, optionTpNo : "0103"
							, goods_no : frm.goods_no.value
							, optionTp : "S"
							, stikerTp : frm.hdstiker_type.value
					},
					success: function(response) {
						if(response != false){

							for(var i=0;i<response.length;i++){
								RN 					= "";
								GOODS_NO 			= "";
								GOODS_CODE 			= "";
								GOODS_NAME 			= "";
								CP_CATE 			= "";
								CATE_NAME 			= "";
								FILE_NM_100 		= "";
								IMG_URL 			= "";
								FILE_PATH_150 		= "";
								FILE_RNM_150 		= "";

								RN 					= response[i]["RN"];
								GOODS_NO 			= response[i]["GOODS_NO"];
								GOODS_CODE 			= response[i]["GOODS_CODE"];
								GOODS_NAME		 	= response[i]["GOODS_NAME"];
								CP_CATE 			= response[i]["CP_CATE"];
								CATE_NAME 			= response[i]["CATE_NAME"];
								FILE_NM_100 		= response[i]["FILE_NM_100"];
								IMG_URL 			= response[i]["IMG_URL"];
								FILE_PATH_150 		= response[i]["FILE_PATH_150"];
								FILE_RNM_150 		= response[i]["FILE_RNM_150"];
								
								var temp = "<tr height='30'>\
												<td><input type='checkbox' name='chk_noSI[]' value='"+GOODS_NO+"'/></td>\
												<td>"+RN+"</td>\
												<td style='padding: 1px 1px 1px 1px'>\
													<img src='"+IMG_URL+"' title='클릭하시면 새 창에 원본 이미지가 열립니다.' data-thumbnail='"+IMG_URL+"' class='goods_thumb' width='50px' height='50px'>\
												</td>\
												<td>"+GOODS_CODE+"</td>\
												<td class='modeual_nm'>"+GOODS_NAME+"</td>\
											</tr>";
								$("#StikerList:last").append(temp);					
								
								//console.log('json_info');
								//console.log(temp);
							}
						} else{
							if(response.length == 0){
								//alert("불러올 내역이 없습니다.");
								var temp = "<tr>\
												<td height='30' colspan='5'>스티커 상품이 없습니다.</td>\
											</tr>";
								$("#StikerList:last").append(temp);
							}
							else {
								alert("실패하였습니다.");
							}
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});
	}

	function fn_search_list2()
	{
		var frm = document.frm;
		
		$.ajax({
					url: "json_goods_option.php",
					dataType: 'json',
					type: 'post',
					async: false,
					data : {
							  mode: "SEL_STIKER_OPTION"
							, goods_no : frm.goods_no.value
							, optionTp : "S"
					},
					success: function(response) {
						if(response != false){

							for(var i=0;i<response.length;i++){
								RN 					= "";
								OPTION_NO 			= "";
								GOODS_NO 			= "";
								GOODS_CODE 			= "";
								OPTION_TYPE 		= "";
								OPTION_GOODS_NO 	= "";
								OPTION_GOODS_NAME 	= "";
								CP_CATE 			= "";
								CATE_NAME 			= "";
								REG_ADM 			= "";
								REG_DATE 			= "";
								FILE_NM_100 		= "";
								IMG_URL 			= "";
								FILE_PATH_150 		= "";
								FILE_RNM_150 		= "";

								RN 					= response[i]["RN"];
								OPTION_NO 			= response[i]["OPTION_NO"];
								GOODS_NO 			= response[i]["GOODS_NO"];
								GOODS_CODE 			= response[i]["GOODS_CODE"];
								OPTION_TYPE 		= response[i]["OPTION_TYPE"];
								OPTION_GOODS_NO 	= response[i]["OPTION_GOODS_NO"];
								OPTION_GOODS_NAME 	= response[i]["OPTION_GOODS_NAME"];
								CP_CATE 			= response[i]["CP_CATE"];
								CATE_NAME 			= response[i]["CATE_NAME"];
								REG_ADM 			= response[i]["REG_ADM"];
								REG_DATE 			= response[i]["REG_DATE"];
								FILE_NM_100 		= response[i]["FILE_NM_100"];
								IMG_URL 			= response[i]["IMG_URL"];
								FILE_PATH_150 		= response[i]["FILE_PATH_150"];
								FILE_RNM_150 		= response[i]["FILE_RNM_150"];

								var temp = "<tr height='30'>\
												<td><input type='checkbox' name='chk_noSD[]' value='"+OPTION_NO+"'/></td>\
												<td>"+RN+"</td>\
												<td style='padding: 1px 1px 1px 1px'>\
													<img src='"+IMG_URL+"' title='클릭하시면 새 창에 원본 이미지가 열립니다.' data-thumbnail='"+IMG_URL+"' class='goods_thumb' width='50px' height='50px'>\
												</td>\
												<td>"+GOODS_CODE+"</td>\
												<td class='modeual_nm'>"+OPTION_GOODS_NAME+"</td>\
												<td class='modeual_nm'>"+CATE_NAME+"</td>\
												<td class='modeual_nm'>"+REG_ADM+"</td>\
												<td>"+REG_DATE+"</td>\
											</tr>";
								$("#StikerOptionList:last").append(temp);
							}
						} else{
							if(response.length == 0){
								//alert("불러올 내역이 없습니다.");
								var temp = "<tr>\
												<td height='30' colspan='8'>스티커 상품이 없습니다.</td>\
											</tr>";
								$("#StikerOptionList:last").append(temp);
							}
							else {
								alert("실패하였습니다.");
							}
						}
					}, error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR.responseText); 
					}
				});
	}

	function js_all_check(optionTp) 
	{
		var frm = document.frm;
		var chkOption;
		var all_chk;		
		
		if(optionTp == "SI")
		{
			chkOption = frm['chk_noSI[]'] ;
			all_chk = frm.all_chkSI;
		}

		if(optionTp == "SD")
		{
			chkOption = frm['chk_noSD[]'] ;
			all_chk = frm.all_chkSD;
		}
		
		if (chkOption != null) {

			if (chkOption.length != null) {
				if (all_chk.checked == true) {					
					for (i = 0; i < chkOption.length; i++) {
						chkOption[i].checked = true;
					}
				} else {
					for (i = 0; i < chkOption.length; i++) {
						chkOption[i].checked = false;
					}
				}
			} else {
			
				if (all_chk.checked == true) {
					chkOption.checked = true;
				} else {
					chkOption.checked = false;
				}
			}
		}
	}

	function optionSelsave(optionTp) 
	{
		var frm = document.frm;
		var optionNo;
		
		if(optionTp == "W")
		{
			if(frm.wrap_no.value == "") 
			{
				alert('포장지를 선택 해 주세요.');
				return;
			}
			else
			{
				optionNo = frm.wrap_no.value;
			}
		}
		
		if(optionTp == "S")
		{
			if(frm.stiker_no.value == "") 
			{
				alert('스티커를 선택 해 주세요.');
				return;
			}
			else
			{
				optionNo = frm.stiker_no.value;
			}
		}

		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_SAVE"					
					, reg_adm: <?=$s_adm_no?>
					, goods_no: frm.goods_no.value
					, goods_cd: frm.goods_cd.value
					, optionTp: optionTp
					, optionNo: optionNo
				},
				success: function(data) 
				{
					//alert("성공");							
					fn_search_list1();
					fn_search_list2();
				}	
				,error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function ckoptionSelsave(optionTp) 
	{ 
		var frm = document.frm;
		
		var selected_cnt = $("input[name='chk_noSI[]']:checked").length;

		if(selected_cnt == 0) 
		{
			alert('선택된 데이터가 없습니다');
			return;
		}

		if (!confirm("선택한 데이터를 저장 하시겠습니까?")) return;	

		var chk_goods_no= new Array();

		$("input[name='chk_noSI[]']:checked").each(function(){
			chk_goods_no.push($(this).val());
		});
		
		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_SEL_SAVE"				
					, reg_adm: <?=$s_adm_no?>
					, goods_no: frm.goods_no.value
					, optionTp: optionTp
					, chk_goods_no: chk_goods_no
				},
				success: function(data) 
				{
					alert("선택 된 데이터가 저장 되었습니다.");
					$("#StikerList:last").empty();
					$("#StikerOptionList:last").empty();
					$("input:checkbox[class='all_chkSI']").attr("checked", false);
					$("input:checkbox[class='all_chkSD']").attr("checked", false);
					fn_search_list1();
					fn_search_list2();
					igmShow();
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function js_selDel(optionTp) 
	{ 
		var frm = document.frm;

		var selected_cnt = $("input[name='chk_noSD[]']:checked").length;

		if(selected_cnt == 0) 
		{
			alert('선택된 데이터가 없습니다');
			return;
		}

		if (!confirm("선택한 데이터를 삭제 하시겠습니까?")) return;	

		var option_no= new Array();

		$("input[name='chk_noSD[]']:checked").each(function(){
			option_no.push($(this).val());
		});

		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_DEL"					
					, option_no: option_no
				},
				success: function(data) 
				{
					alert("선택 된 데이터가 삭제 되었습니다.");
					
					$("#StikerList:last").empty();
					$("#StikerOptionList:last").empty();
					$("input:checkbox[class='all_chkSI']").attr("checked", false);
					$("input:checkbox[class='all_chkSD']").attr("checked", false);
					fn_search_list1();
					fn_search_list2();
					igmShow();
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function js_reload() 
	{
		location.reload();
	}
	
	function js_search(optionTp) {
		var frm = document.frm;
		
		if(optionTp == "SI" || optionTp == "SD")
		{
			frm.tab_index.value = 1;	
		}
		else
		{
			frm.tab_index.value = 0;	
		}
		
		frm.mode.value = "S";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_list() 
	{
		document.location.href = "goods_option_list.php";
	}

	function optionConfirm() 
	{
		if (!confirm("옵션 선택을 완료 하시겠습니까?")) return;	

		$.ajax({
			url: "json_goods_option.php",
			dataType: 'json',
			type: 'post',
			async: true,
			data: {
					  mode: "GOODS_OPTION_CONFIRM"					
					, reg_adm: <?=$s_adm_no?>
					, goods_no: frm.goods_no.value
				},
				success: function(data) 
				{
					alert("옵션 선택이 완료 되었습니다. \n\n상품옵션관리 리스트 화면으로 이동합니다.");
					js_list();
				}	,
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert('통신 실패');
					return;
				}
		});
	}

	function igmShow() 
	{
		var img_frame = $("<div style='background-color: #EFEFEF; border: 1px solid #DEDEDE; padding:5px 5px 5px 5px; z-index:9999;'></div>");
		$(".goods_thumb").hover(function(){

			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			img_frame.show().append($("<img src='"+origin_img+"?v="+(new Date()).getTime()+"' style='max-height:400px; max-width:300px;'/>"));

			$(this).after(img_frame);

			img_frame.center();

		}, function(){

			img_frame.empty().hide();

		});

		var win;
		$(".goods_thumb").click(function() {
			
			var origin_img = $(this).prop("src").replace("simg/s_50_50_","");
			
			win = window.open(origin_img, 'win');

		});

		$(window).scroll(function() {
		img_frame.empty().hide();
		});
	}

	(function($){
		$.fn.extend({
			center: function () {
				return this.each(function() {
					var top = ($(window).height() - $(this).find("img").outerHeight()) / 2 + $(window).scrollTop();
					var left = ($(window).width() - $(this).find("img").outerWidth()) / 2;

					if($(this).find("img").outerHeight() == 0 || $(this).find("img").outerWidth() == 0)
						$(this).css({position:'absolute', margin:0, top: (100 + $(window).scrollTop()) +'px', left: 350 +'px'});
					else
						$(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
				});
			}
		}); 
	})(jQuery);

	$(function(){
	
		$("#stiker_type").change(function()
		{
			var selecttype=$("#stiker_type").val();
			$("input[name=hdstiker_type]").val(selecttype);
			$("#StikerList").empty();	
			$("input:checkbox[class='all_chkSI']").attr("checked", false);
			$("input:checkbox[class='all_chkSD']").attr("checked", false);
			fn_search_list1();
			igmShow();
		});

	});

	

</script>

<style>
	.row_deleted {background-color:#dfdfdf; }
	.row_deleted > td{color:#fff !important;}
	.row_deleted > td > a{color:#fff !important;}
</style>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="optionCf" />
<input type="hidden" name="tab_index" value="<?=$tab_index?>" />
<input type="hidden" name="optionConfirmDt" />
<input type="hidden" name="hdstiker_type" value="<?=$stiker_type?>" />

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

			<h2 style="margin:0;">상품 옵션 관리</h2>				
			<div class="btnright" style="margin:0 0 5px 0;">
				<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록"></a>
			</div>
			
			<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="15%" />
					<col width="15%" />
					<col width="55%" />
				</colgroup>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
				<tbody>
					<tr>
						<td align="center" style="padding: 5px 5px 5px 5px" class="line" colspan="2" rowspan="6">
							<img id="mainImg"  border="0" width="180" height="180">
						</td>
						<th>상품명</th>
						<td colspan="3" class="line">
						<span id="goods_name" name="goods_name">
						</td>
					</tr>
					<tr>
						<th>상품코드</th>
						<td class="line">
						<span id="goods_code" name="goods_code">
						</td>
					</tr>
					<tr>
						<th>공급업체</th>
						<td class="line">
							<span id="cate_03" name="cate_03">
						</td>
					</tr>					
					<tr>
						<th>과세여부</th>
						<td class="line">
						<span id="tax_tf" name="tax_tf">
						</td>
					</tr>
					<tr>
						<th>박스입수</th>
						<td class="line">
						<span id="delivery_cnt_in_box" name="delivery_cnt_in_box">
						</td>
					</tr>
					<tr>
						<th>판매가</th>
						<td class="line">
							<span id="sale_price" name="sale_price">
						</td>
					</tr>			

				</table>
				
				<div class="sp20"></div>
				<div class="btnright" style="margin:0 0 5px 0;">				
					<span id="span1" style="font-family:tahoma;font-size:15px; display: none;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;				
				<input type="button" value="옵션선택완료" onclick="optionConfirm()">
				</div>
				<div id="tabs" style="width:95%; height:533px; margin:10px 0;">
					<ul>
						<li><a href="#tabs-2" >스티커</a></li> 

					</ul>
					
					<div id="tabs-2">
						<div id ="ClickStikerOne">
						* 스티커 상품 리스트	
						<div >
							업체구분 <select id="stiker_type" style=width:125px;><option value="">전체</option></select>
							<input type="button" value="선택저장" onclick="ckoptionSelsave('SI')" style="float:right; text-align: right; margin: 0 0 10px 0;">
						</div>
						</div>

						<div id ="ClickStikerTwo">
						* 스티커 상품 옵션 리스트	
						<div style="display:inline-block; width: 100%; text-align: right; margin: 0 0 10px 0;">
							<input type="button" value="선택삭제" onclick="js_selDel('SD')">
						</div>
						</div>

						<div id ="StikerOne">		
						<table cellpadding="0" cellspacing="0" class="rowstable">
							<colgroup>
								<col width="5" />
								<col width="5"/>
								<col width="12" />
								<col width="12" />
								<col width="66"/>
							</colgroup>
							<thead>
							<tr>							
								<th class='fixedHeader'><input type="checkbox" class="all_chkSI" name="all_chkSI" onClick="js_all_check('SI');"></th>
								<th class='fixedHeader'>No.</th>
								<th class='fixedHeader'>이미지</th>
								<th class='fixedHeader'>상품코드</th>
								<th class='fixedHeader'>상품명</th>
							</tr>
							</thead>

							<tbody id="StikerList">
							</tbody>
						</table>
						</div>		
						
						<div id ="StikerTwo">	
						<table cellpadding="0" cellspacing="0" class="rowstable" >
							<colgroup>
								<col width="5" />
								<col width="5"/>
								<col width="12" />
								<col width="12" />
								<col width="31"/>
								<col width="10"/>
								<col width="10"/>
								<col width="15"/>
							</colgroup>
							<thead>
							<tr>							
								<th class='fixedHeader'><input type="checkbox" class="all_chkSD" name="all_chkSD" onClick="js_all_check('SD');"></th>
								<th class='fixedHeader'>No.</th>
								<th class='fixedHeader'>이미지</th>
								<th class='fixedHeader'>상품코드</th>
								<th class='fixedHeader'>상품명</th>
								<th class='fixedHeader'>회사카테고리</th>
								<th class='fixedHeader'>등록자</th>
								<th class='fixedHeader'>등록시간</th>
							</tr>
							</thead>

							<tbody id="StikerOptionList">
							</tbody>
						</table>
						</div>
					</div>		
			</div>
				
    </td>
  </tr>
  </table>
</div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>