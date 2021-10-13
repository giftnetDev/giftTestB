<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	// $conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	// $menu_right = "CP002"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	// require "../../_common/common_header.php"; 


#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================


	$idx=$_REQUEST['idx'];

	
#===============================================================
# Get Search list count
#===============================================================

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
		<title><?=$g_title?></title>
		<link rel="stylesheet" href="../css/admin.css" type="text/css" />

		<script type="text/javascript" src="../js/common.js"></script>
		<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
		<script>
			var g_goodsDate = new Array();
			var index=<?=$idx?>;
			$(function(){
				g_goodsDate =window.opener.js_get_candidate_goods();


				js_table_candidate_goods();
			});
			function js_select_goods(seq, index){
				//seq = 'this의 상품 나열된 순서'
				//index = parent의 상품 번째
				//즉 index에 해당하는 parent의 text박스에 this에 seq번째 상품정보를 넣어주어야 한다.
				// console.log('g_goodsDate_value');
				// console.log(g_goodsDate);
				// return;
				opener.document.getElementById("txtGoodsName_"+index).value="["+g_goodsDate[seq]['GOODS_CODE']+"]"+g_goodsDate[seq]['GOODS_NAME'];
				opener.document.getElementById("hdGoodsNo_"+index).value=g_goodsDate[seq]['GOODS_NO'];
				opener.document.getElementById("hdGoodsCode_"+index).value=g_goodsDate[seq]['GOODS_CODE'];
				window.opener.js_catch_goods_sale_state(g_goodsDate, g_goodsDate[seq]['SALE_STATE'], index, seq);
				// opener.document.getElementById("hdBuyPrice_"+index).value=g_goodsDate[seq]['BUY_PRICE'];
				// opener.document.getElementById("hdGoodsCode_"+index).value=g_goodsDate[seq]['GOODS_CODE'];
				// opener.document.getElementById("hdSaleState_"+index).value=g_goodsDate[seq]['SALE_STATE'];
				self.close();
			}

			function js_table_candidate_goods(){
				let len=g_goodsDate.length;
				console.log(g_goodsDate);


				let strTbl="<table><colgroup><col='7%'><col='10%'><col='15%'><col='*'></colgroup><thead><tr><th>상품번호</th><th>상품코드</th><th>상품명</th><th></th></tr></thead><tbody>";
				for(var i=0; i<len; i++){
					strTbl+="<tr> <td><input type='button' value='선택' onclick='js_select_goods("+i+", "+index+");'></td><td>"+g_goodsDate[i]['GOODS_NO']+"</td> <td>"+g_goodsDate[i]['GOODS_CODE']+"</td> <td>"+g_goodsDate[i]['GOODS_NAME']+"</td> </tr>";
				}
				strTbl+="</tbody></table>";

				$('#dvTable').html(strTbl);
			}

		</script>

		<style>
			.top_group td {border-top: 2px solid black;  }
			.bottom_group td {border-top: 1px dotted black; }
			table.rowstable td {background: none;}
			table.rowstable {border-bottom: 2px solid black;} 
			.btnright {text-align:right; padding-right:50px; margin: 4px 0;}

			table tr:hover{
				background-color: #DDDDDD;
			}
		</style>
	</head>

	<body id="popup_order_wide">

		<div id="popupwrap_order_wide">
			<h1>상품 조회</h1>
			<div id="postsch_code">

				<div class="addr_inp">

			<form name="frm" method="post">

					<div id="dvTable">

					</div>


			</form>
			<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
		</div>
	</body>
</html>
