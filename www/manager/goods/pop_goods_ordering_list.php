<?session_start();?>
<?

#===============================================================
# 상품의 선출고 주문을 찾는 리스트
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

	// 단품 계산중
	function listGoodsInOrderingEach($db, $goods_no) { 

		$query ="	SELECT OG.RESERVE_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME,
						
							IFNULL((
							SELECT  SUM( 
												CASE OGS.ORDER_STATE
												WHEN 6 
												THEN - OGS.QTY
												WHEN 8 
												THEN - OGS.QTY
												ELSE OGS.QTY
												END )
											
							FROM TBL_ORDER_GOODS OGS
							WHERE OGS.GROUP_NO = OG.ORDER_GOODS_NO
							  AND OGS.USE_TF =  'Y'
							  AND OGS.DEL_TF =  'N'
							  AND OGS.CATE_01 <> '추가'
							  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
							), 0) 

							+ 

							IFNULL((
								SELECT  SUM(OGS.QTY)
								FROM TBL_ORDER_GOODS OGS
								WHERE OGS.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
								  AND OGS.GROUP_NO = 0
								  AND OGS.USE_TF =  'Y'
								  AND OGS.DEL_TF =  'N'
								  AND OGS.CATE_01 <> '추가'
								  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
							), 0)
						
						- IFNULL((SELECT SUM(I.SUB_QTY) FROM TBL_ORDER_GOODS_INDIVIDUAL I WHERE I.IS_DELIVERED = 'Y' AND I.DEL_TF = 'N' AND I.ORDER_GOODS_NO = OG.ORDER_GOODS_NO) , 0)
						AS QTY

						, OG.WORK_QTY

					FROM TBL_ORDER_GOODS OG
					WHERE OG.USE_TF = 'Y' AND OG.DEL_TF = 'N' 
					AND OG.ORDER_STATE IN (1, 2)
					AND OG.DELIVERY_TYPE NOT IN ('98', '99')
					AND OG.CATE_01 <> '추가'
					AND OG.GOODS_NO = $goods_no
				 ";

		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

	// 단품 계산중
	function listGoodsInOrderingSet($db, $goods_no) { 
		
		$query ="SELECT OG.RESERVE_NO, OG.GOODS_CODE, OG.GOODS_NAME, OG.GOODS_SUB_NAME,
							
								(
									(SELECT IFNULL(SUM( 
									CASE OGS.ORDER_STATE
									WHEN 6 
									THEN - OGS.QTY
									WHEN 8 
									THEN - OGS.QTY
									ELSE OGS.QTY
									END  * GS.GOODS_CNT ), 0) AS QTY
										FROM TBL_ORDER_GOODS OGS
										WHERE OGS.GROUP_NO = OG.ORDER_GOODS_NO
										  AND OGS.USE_TF =  'Y'
										  AND OGS.DEL_TF =  'N'
										  AND OGS.CATE_01 <> '추가'
										  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
									)

									+

									IFNULL(( 
										SELECT SUM(OGS.QTY  * GS.GOODS_CNT)
										FROM TBL_ORDER_GOODS OGS
										WHERE OGS.ORDER_GOODS_NO = OG.ORDER_GOODS_NO
										  AND OGS.GROUP_NO = 0
									  	  AND OGS.USE_TF =  'Y'
										  AND OGS.DEL_TF =  'N'
										  AND OGS.CATE_01 <> '추가'
										  AND OGS.DELIVERY_TYPE NOT IN ('98', '99')
									), 0)
								) AS QTY
								
								, (OG.WORK_QTY  * GS.GOODS_CNT) AS WORK_QTY
							
						FROM TBL_ORDER_GOODS OG
						JOIN TBL_GOODS_SUB GS ON OG.GOODS_NO = GS.GOODS_NO
						WHERE GS.GOODS_SUB_NO = $goods_no
						AND OG.USE_TF =  'Y'
						AND OG.DEL_TF =  'N'
						AND OG.CATE_01 <> '추가'
						AND OG.ORDER_STATE IN (1, 2)
						AND OG.DELIVERY_TYPE NOT IN ('98', '99')
						ORDER BY OG.RESERVE_NO DESC
					
				 ";

		//echo $query;
		$result = mysql_query($query,$db);
		$record = array();
		

		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}
		return $record;
	}

#====================================================================
# Request Parameter
#====================================================================

	$goods_no				= trim($goods_no);
	
#===============================================================
# Get Search list count
#===============================================================

	

	if($goods_no <> "") { 
		
		$arr_rs_each = listGoodsInOrderingEach($conn, $goods_no);
		$arr_rs_set = listGoodsInOrderingSet($conn, $goods_no);

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

	function js_reset() { 
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


	function js_view(reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
		
	}

</script>

</script>
</head>
<body id="popup_file">
<form name="frm" method="post">
	<input type="hidden" name="goods_no" value="<?=$goods_no?>" />

	<div id="popupwrap_file">
		<h1>선출고 주문 리스트</h1>
		<div class="btn_right">
			<input type="button" name="bb" value=" 새로고침 " onclick="js_reset();"/>
		</div>
		<div id="postsch">
			<div class="addr_inp">
				<h2>단품</h2>
				<table cellpadding="0" cellspacing="0" class="rowstable">
					<colgroup>
						<col width="18%" />
						<col width="10%" />
						<col width="*" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>주문번호</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>주문수량</th>
							<th>작업수량</th>
							<th class="end">잔여수량</th>
						</tr>
					</thead>
					<tbody>
						<? 
						if(sizeof($arr_rs_each) > 0) { 
							for($i = 0; $i < sizeof($arr_rs_each); $i++) {
								
								$RESERVE_NO			= $arr_rs_each[$i]["RESERVE_NO"];
								$GOODS_CODE			= $arr_rs_each[$i]["GOODS_CODE"];
								$GOODS_NAME			= $arr_rs_each[$i]["GOODS_NAME"];
								$GOODS_SUB_NAME		= $arr_rs_each[$i]["GOODS_SUB_NAME"];
								$QTY				= $arr_rs_each[$i]["QTY"];
								$WORK_QTY			= $arr_rs_each[$i]["WORK_QTY"];
								$LEFT_QTY = $QTY - $WORK_QTY;
								?>
						<tr height="30">
							<td><a href="javascript:js_view('<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?></td>
							<td><?=number_format($QTY)?></td>
							<td><?=number_format($WORK_QTY)?></td>
							<td><?=number_format($LEFT_QTY)?></td>
						</tr>
								<?
							}
					
							?>

						<? } else { ?>
						<tr>
							<td colspan="6" height="30" style="text-align:center;">내역이 없습니다.</td>
						</tr>
							
						<? } ?>
						
						
					</tbody>
				</table>
				<div class="sp20"></div>
				<h2>세트</h2>
				<table cellpadding="0" cellspacing="0" class="rowstable">
					<colgroup>
						<col width="18%" />
						<col width="10%" />
						<col width="*" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>주문번호</th>
							<th>상품코드</th>
							<th>상품명</th>
							<th>주문수량</th>
							<th>작업수량</th>
							<th class="end">잔여수량</th>
						</tr>
					</thead>
					<tbody>
						<? 
						if(sizeof($arr_rs_set) > 0) { 
							for($i = 0; $i < sizeof($arr_rs_set); $i++) {
								
								$RESERVE_NO			= $arr_rs_set[$i]["RESERVE_NO"];
								$GOODS_CODE			= $arr_rs_set[$i]["GOODS_CODE"];
								$GOODS_NAME			= $arr_rs_set[$i]["GOODS_NAME"];
								$GOODS_SUB_NAME		= $arr_rs_set[$i]["GOODS_SUB_NAME"];
								$QTY				= $arr_rs_set[$i]["QTY"];
								$WORK_QTY			= $arr_rs_set[$i]["WORK_QTY"];
								$LEFT_QTY = $QTY - $WORK_QTY;

								if($QTY == 0) continue;
								?>
						<tr height="30">
							<td><a href="javascript:js_view('<?=$RESERVE_NO?>');"><?=$RESERVE_NO?></a></td>
							<td><?=$GOODS_CODE?></td>
							<td class="modeual_nm"><?=$GOODS_NAME?> <?=$GOODS_SUB_NAME?></td>
							<td><?=number_format($QTY)?></td>
							<td><?=number_format($WORK_QTY)?></td>
							<td><?=number_format($LEFT_QTY)?></td>
						</tr>
								<?
							}
					
							?>

						<? } else { ?>
						<tr>
							<td colspan="6" height="30" style="text-align:center;">내역이 없습니다.</td>
						</tr>
							
						<? } ?>
					</tbody>
				</table>		
			</div>
		</div>
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