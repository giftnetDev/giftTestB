<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD012"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
	<title><?=$g_title?></title>
	<link rel="stylesheet" href="../css/admin.css" type="text/css" />
	<script type="text/javascript" src="../js/common.js"></script>
	<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
	<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
	<style type="text/css">
		#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
		#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:500px; border:1px solid #d1d1d1;}
		table.rowstable04 { border-top: none; }
		table.rowstable04 > th { padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-top: 1px solid #d2dfe5; background: #ebf3f6 url('../images/admin/bg_bar_01.gif') right center no-repeat; }
		table.rowstable04 > th.end { background: #ebf3f6; }
		table.rowstable04 td { color: #555555; text-align: center; vertical-align: middle; background: none; }
	</style>
	<script>
		function countRow(){
				var cnt = $("td > input[type='checkbox']").closest("tr").length;
				$("#count").html(cnt);
		}

		function js_save(){
			var file = $("input[name='file_nm']")[0].files[0];
			datas = new FormData();
			datas.append("mode", "EXCEL_FILE_READ");
			datas.append("file", $("input[name='file_nm']")[0].files[0]);
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'json',
				contentType: 'multipart/form-data', 
				mimeType: 'multipart/form-data',
				type: 'post',
				data : datas,
				success: function(response) {
					if(response.length > 0){
						for(i=0;i<response.length;i++){
							var goods_name = response[i]["goods_name"];
							var goods_code = response[i]["goods_code"];
							var mro_sale_price = numberFormat(response[i]["mro_sale_price"]);
							var memo = isNormal(goods_code, mro_sale_price);

							var html_checkbox 		= "<td><input type='checkbox' id='chk_"+goods_code+"'></td>";
							var html_memo 			= "<td>"+memo+"</td>";
							var html_goods_name 	= "<td>"+goods_name+"</td>";
							var html_mro_sale_price = "<td>"+mro_sale_price+"</td>";
							var html_goods_code 	= "<td>"+goods_code+"</td>";

							var tr = "<tr height='30px'>"+html_checkbox+html_memo+html_goods_name+html_goods_code+html_mro_sale_price+"</tr>"

							$("#data").append(tr);
						}
						countRow();
						$("input[name='file_nm']").val("");
						// alert("성공하였습니다.");
					} else {
						alert("실패하였습니다.");
					}
				},
				error : function (jqXHR, textStatus, errorThrown) {
                	alert('ERRORS: ' + textStatus);
            	},
				cache: false,
				contentType: false,
				processData: false
			});
		}

		function isNormal(code, price){
			//천단위 구분기호 제거
			price = price.replace(/,/g, '');

			//코드 정규식 패턴
			var codeRegex = /^[0-9]{3}-[0-9]{6}$/;
			
			//가격 정규식 패턴
			var priceRegex = /^[0-9]*$/;

			var memo ="";

			if(code==""){
				memo = "상품코드 누락";
			}else if(!codeRegex.test(code)){
				memo = "상품코드 오류";
			}
			
			if(price==""){
				if(memo == ""){
					memo = "판매가 누락";
				}else{
					memo += ", 판매가 누락";
				}
			}else if(!priceRegex.test(price)){
				if(memo == ""){
					memo = "판매가 오류";
				}else{
					memo += ", 판매가 오류";
				}
			}

			if(memo == ""){
				return "정상";
			}else{
				return memo;
			}
		}

		function toggleAllCheck(){
			var all_check = $("#allCheck").prop('checked');
			var individual_check = $("td > input[type='checkbox']");

			if(all_check){
				individual_check.prop('checked',true);
			} else {
				individual_check.prop('checked',false);
			}
		}

		function deleteSelectedRow(){
			var selectedRows = $("td > input:checked").closest("tr");
			selectedRows.remove();
			countRow();
		}

		function updateNormalData(){
			var selectedRows = $("td > input[type='checkbox']");
			var datas = Array();
			for(i=0;i<selectedRows.length;i++){
				//0 체크박스, 1 비고, 2 전시상품명, 3 공급처상품코드, 4 판매가
				var temp_checkbox 			= selectedRows.eq(i).closest("tr").children("td").eq(0).html();
				var temp_memo 				= selectedRows.eq(i).closest("tr").children("td").eq(1).html();
				var temp_goods_name 		= selectedRows.eq(i).closest("tr").children("td").eq(2).html();
				var temp_goods_code 		= selectedRows.eq(i).closest("tr").children("td").eq(3).html();
				//가격은 천단위 구분기호 제거해서 저장
				var temp_mro_sale_price 	= selectedRows.eq(i).closest("tr").children("td").eq(4).html().replaceall(",", "");

				if(selectedRows.eq(i).closest("tr").children("td").eq(1).html() == "정상"){
					//정상자료의 코드와 가격 배열에 등록
					datas.push({"code":temp_goods_code, "mro_sale_price":temp_mro_sale_price});
				}
			}//for

			if(datas.length>0){
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'json',
					type: 'post',
					data : {
						"mode":"UPDATE_MRO_SALE_PRICE",
						"s_adm_no":"<?=$s_adm_no?>",
						"data":datas
					},
					success: function(response) {
						if(response.length > 0){
							insert = response[0]["insert"];
							update = response[0]["update"];
							error = response[0]["error"];
							nochange = response[0]["nochange"];
							result = response[0]["result"];
							
							for(i=0;i<result.length;i++){
								goods_code = result[i]["goods_code"];
								state = result[i]["state"];

								var selectedRow = $("#chk_"+goods_code).closest("tr");
								if(state == "신규등록" || state == "수정" || state == "가격전과동일" ){
									selectedRow.remove();
								} else if(state == "상품코드확인불가" || state == "알수없는오류"){
									// 비고에 이유 추가
									selectedRow.children("td").eq(1).html(selectedRow.children("td").eq(1).html()+" "+state);
								}
							}
							
							countRow();

							//처리결과
							alert("성공하였습니다.\n"+"신규등록 "+insert+"건, 수정 "+update+"건, 전과동일 "+nochange+"건, 에러 "+error+"건");
						} else {
							alert("실패하였습니다.");
						}
					},
					error : function (jqXHR, textStatus, errorThrown) {
						alert('ERRORS: ' + textStatus);
					}
				});
			}
		}
		$(document).ready(function(){
			$("#allCheck").on("click",function(){
				toggleAllCheck();
			});

			$("#deleteRow").on("click",function(){
				deleteSelectedRow();
			});

			$("#updateItem").on("click",function(){
				updateNormalData();
			});
		});
	</script>
</head>
<body id="admin">
	<div id="adminwrap">
	<?require "../../_common/top_area.php";?>
		<table width="100%" cellpadding="0" cellspacing="0">
			<colgroup>
				<col width="180" />
				<col width="*" />
			</colgroup>
			<tr>
				<td class="leftarea">
					<?require "../../_common/left_area.php";?>
				</td>
				<td class="contentarea">
				<div id="mwidthwrap">
					<h2>MRO 판매가 관리</h2>
					<table cellpadding="0" cellspacing="0" class="colstable">
						<colgroup>
							<col width="12%">
							<col width="88%">
						</colgroup>
						<tbody>
							<tr>
								<th>
									파일
									<br><br>
									<a href="/_common/download_file.php?file_name=mro_goods_insert_example.xlsx&filename_rnm=mro_goods_insert_example.xlsx&str_path=manager/goods/">받기</a>
								</th>
								<td><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
							</tr>
						</tbody>
					</table>

					<div class="btnright">
					<? if ($file_nm <> "" ) {?>
						<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } ?>
					<? } else {?>
						<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
						<? } ?>
					<? }?>
					</div>

					<div class="sp20"></div>
					<div>
						* 총 <span id="count">0</span>건
					</div>
					<div id="temp_scroll">
						<table cellpadding="0" cellspacing="0" style="width:100%;" class="rowstable01">
							<colgroup>
								<col width="3%">
								<col width="20%">
								<col width="30%">
								<col width="25%">
								<col width="22%">
							</colgroup>
							<thead>
								<tr>
									<th><input type="checkbox" id="allCheck"></th>
									<th>비  고</th>
									<th>전시상품명</th>
									<th>상품코드</th>
									<th class="end">판매가(원)</th>
								</tr>
							</thead>
							<tbody id="data">
							</tbody>
						</table>
					</div>
				<div class="btnright">
					<input type="button" id="updateItem" value=" 정상자료 수정 " class="btntxt">&nbsp;&nbsp;
					<input type="button" id="deleteRow" value=" 선택자료 삭제 " class="btntxt">
				</div>
				</div>
				</td>
			</tr>
  		</table>
	</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>