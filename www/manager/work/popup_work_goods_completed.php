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
	$menu_right = "WO003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# DML Process
#====================================================================
	
	//echo $work_date;
	$arr_goods_no = array();
	$arr_work_qty_no = array();
	$arr_cnt = 0; 

	$arr_rs = listWorkGoodsCompleted($conn, $work_date);
	
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$GOODS_NO					= SetStringFromDB($arr_rs[$j]["GOODS_NO"]);
			$DELIVERY_CNT_IN_BOX		= SetStringFromDB($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$OG_GOODS_CATE				= SetStringFromDB($arr_rs[$j]["GOODS_CATE"]);
			$WORK_QTY					= SetStringFromDB($arr_rs[$j]["WORK_QTY"]);

			// 하위 상품이 있는지 확인 합니다.
			$arr_sub_goods = getSubGoodsInfo($conn, $GOODS_NO);
			
			if (sizeof($arr_sub_goods) > 0) {

				for ($k = 0 ; $k < sizeof($arr_sub_goods); $k++) {
					$is_flag = false; 
					$GOODS_SUB_NO	= SetStringFromDB($arr_sub_goods[$k]["GOODS_SUB_NO"]);
					$GOODS_CNT		= SetStringFromDB($arr_sub_goods[$k]["GOODS_CNT"]);
					$GS_GOODS_CATE	= SetStringFromDB($arr_sub_goods[$k]["GOODS_CATE"]);
					
					if (sizeof($arr_goods_no) > 0) {
						
						for ($g = 0 ; $g < sizeof($arr_goods_no) ; $g++) {
							if (trim($arr_goods_no[$g]) == $GOODS_SUB_NO) {
								//echo " 하위 상품 경우 수량만 넣을때 ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
								$is_flag = true;

								if(startsWith("010202", $GS_GOODS_CATE)) { 
									$arr_work_qty_no[$g] = $arr_work_qty_no[$g] + ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
								} else { 
									$arr_work_qty_no[$g]  = $arr_work_qty_no[$g] + ($WORK_QTY * $GOODS_CNT);
								}
							}
						}

						if ($is_flag == false) {
							$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

							if(startsWith("010202", $GS_GOODS_CATE)) { 
								$arr_work_qty_no[$arr_cnt] = ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
							} else { 
								$arr_work_qty_no[$arr_cnt] = $WORK_QTY * $GOODS_CNT;
							}
							//echo " 하위 상품 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
							$arr_cnt = $arr_cnt + 1;
						}

					} else {
						$is_flag == false;
						$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

						if(startsWith("010202", $GS_GOODS_CATE)) { 
							$arr_work_qty_no[$arr_cnt] = ceil(($WORK_QTY * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
						} else { 
							$arr_work_qty_no[$arr_cnt] = $WORK_QTY * $GOODS_CNT;
						}
						//echo "하위 상품 처음 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
						
					}
				}

			} else {

				// 하위 상품이 없을 경우
				if (sizeof($arr_goods_no) > 0) {
					for ($h = 0 ; $h < sizeof($arr_goods_no) ; $h++) {

						if (trim($arr_goods_no[$h]) == $GOODS_NO) {

							//echo " 하위 상품이 없을 경우 수량만 넣을때 ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
							$is_flag = true;
							$arr_work_qty_no[$h]  = $arr_work_qty_no[$h] + $WORK_QTY;
						}
					}

					if ($is_flag == false) {
						$arr_goods_no[$arr_cnt] = $GOODS_NO;

						$arr_work_qty_no[$arr_cnt] = $WORK_QTY;

						//echo " 하위 상품이 없을 경우 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
					}

				} else {

					$is_flag == false;
					$arr_goods_no[$arr_cnt] = $GOODS_NO;
					
					$arr_work_qty_no[$arr_cnt] = $WORK_QTY;

					//echo "하위 상품이 없을 경우 처음 배열에 넣을때 ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
					$arr_cnt = $arr_cnt + 1;
				}

			}

			$is_flag = false;
		
		}
	}

	$arr = array();
	if(sizeof($arr_goods_no) > 0) {
		for($i = 0; $i < sizeof($arr_goods_no); $i++) {

			$arr[$i]["GOODS_NO"] = $arr_goods_no[$i];
			$arr[$i]["WORK_QTY_NO"] = $arr_work_qty_no[$i];

			$arr_goods = selectGoods($conn, $arr_goods_no[$i]);
			$arr[$i]["GOODS_CODE"]	= trim($arr_goods[0]["GOODS_CODE"]);
			$arr[$i]["GOODS_NAME"]	= trim($arr_goods[0]["GOODS_NAME"]);
			$arr[$i]["GOODS_CATE"]	= trim($arr_goods[0]["GOODS_CATE"]);
			$arr[$i]["STOCK_CNT"]	= trim($arr_goods[0]["STOCK_CNT"]);
			$arr[$i]["FSTOCK_CNT"]	= trim($arr_goods[0]["FSTOCK_CNT"]);
		}
	}

	foreach ($arr as $key => $row) {
		$GOODS_CATE[$key]  = $row['GOODS_CATE'];
		$GOODS_NAME[$key]  = $row['GOODS_NAME'];
	}

	if(sizeof($arr) > 0)
		array_multisort($GOODS_CATE, SORT_ASC, $GOODS_NAME, SORT_ASC, $arr);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

<script type="text/javascript">
	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "popup_work_goods_completed_excel.php";
		frm.submit();

	}

	function js_search() {

		var frm = document.frm;
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}
</script>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">
	<input type="hidden" name="work_date" value="<?=$work_date?>" />
	<input type="hidden" name="start_work_date" value="<?=$start_work_date?>" />
	<input type="hidden" name="end_work_date" value="<?=$end_work_date?>" />

<div id="popupwrap_file">
	<h1>작업 완료 자재 리스트</h1>
	<div id="postsch_code">
		<div class="addr_inp">

		<div style="width:92%; margin:5px; overflow:hidden;">
			<div style="float:right;">
				<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
			</div>
		</div>
		<div class="clear"></div>
		<table cellpadding="0" cellspacing="0" width="98%">
			<tr>
				<td>
			<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
				<col width="15%" />
				<col width="55%" />
				<col width="10%"/>
				<col width="10%"/>
				<col width="10%" />
				</colgroup>
				<tr>
					<th>상품코드</th>
					<th>자재명</th>
					<th>완료량</th>
					<th>가재고</th>
					<th class="end">재고</th>
				</tr>
				<?
					if(sizeof($arr) > 0) {
						for($i = 0; $i < sizeof($arr); $i++) {
							$goods_code		= $arr[$i]["GOODS_CODE"];
							$work_qty_no	= $arr[$i]["WORK_QTY_NO"];
							$goods_name		= $arr[$i]["GOODS_NAME"];
							$stock_cnt		= $arr[$i]["STOCK_CNT"];
							$fstock_cnt		= $arr[$i]["FSTOCK_CNT"];

				?>
				<tr>
					<td><?=$goods_code?></td>
					<td class="modeual_nm"><?=$goods_name?> </td>
					<td class="price" title="작업된수량"><?=number_format($work_qty_no)?> 개</td>
					<td class="price"><?=number_format($fstock_cnt)?> 개</td>
					<td class="price"><?=number_format($stock_cnt)?> 개</td>
				</tr>
				<?
						}
					} else {
				?>
				<tr>
					<td height="30" colspan="5">구성품이 없습니다</td>
				</tr>
				<?
					}
				?>
			</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="sp20"></div>
</div>
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="닫기" /></a></div>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>