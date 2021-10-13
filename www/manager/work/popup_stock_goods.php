<?session_start();?>
<?
# =============================================================================
# File Name    : popup_stock_goods.php
# =============================================================================

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "WO004"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/work/work.php";

#====================================================================
# DML Process
#====================================================================
	//echo $work_date;
	$arr_goods_no = array();
	$arr_qty_no = array();
	$arr_cnt = 0; 

	$arr_rs = selectOrderGoods($conn, $order_goods_no);
	
	if (sizeof($arr_rs) > 0) {
		for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

			$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
			$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
			$GOODS_NO					= trim($arr_rs[$j]["GOODS_NO"]);
			$DELIVERY_CNT_IN_BOX		= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
			$OG_GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
			$WORK_QTY					= trim($arr_rs[$j]["WORK_QTY"]);

			$refund_able_qty = getRefundAbleQty($conn, $RESERVE_NO, $ORDER_GOODS_NO);

			if($refund_able_qty == 0) continue;

			//2017-07-24 �۾��� ������ ����
			$refund_able_qty = $refund_able_qty - $WORK_QTY;
			
			//echo "ORDER_GOODS_NO : ".$ORDER_GOODS_NO.", GOODS_NO : ".$GOODS_NO.", refund_able_qty : ".$refund_able_qty."<br>";

			// ���� ��ǰ�� �ִ��� Ȯ�� �մϴ�.
			$arr_sub_goods = getSubGoodsInfo($conn, $GOODS_NO);
			
			if (sizeof($arr_sub_goods) > 0) {

				for ($k = 0 ; $k < sizeof($arr_sub_goods); $k++) {
					$is_flag = false; 
					$GOODS_SUB_NO	= trim($arr_sub_goods[$k]["GOODS_SUB_NO"]);
					$GOODS_CNT		= trim($arr_sub_goods[$k]["GOODS_CNT"]);
					$GS_GOODS_CATE	= trim($arr_sub_goods[$k]["GOODS_CATE"]);
					
					if (sizeof($arr_goods_no) > 0) {
						
						for ($g = 0 ; $g < sizeof($arr_goods_no) ; $g++) {
							if (trim($arr_goods_no[$g]) == $GOODS_SUB_NO) {
								//echo " ���� ��ǰ ��� ������ ������ ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
								$is_flag = true;

								if(startsWith("010202", $GS_GOODS_CATE))
									$arr_qty_no[$g]  = $arr_qty_no[$g] + ceil(($refund_able_qty * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
								else
									$arr_qty_no[$g]  = $arr_qty_no[$g] + ($refund_able_qty * $GOODS_CNT);
							}
						}

						if ($is_flag == false) {
							$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

							if(startsWith("010202", $GS_GOODS_CATE))
								$arr_qty_no[$arr_cnt] = ceil(($refund_able_qty * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
							else
								$arr_qty_no[$arr_cnt] = $refund_able_qty * $GOODS_CNT;
							//echo " ���� ��ǰ �迭�� ������ ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
							$arr_cnt = $arr_cnt + 1;
						}

					} else {
						$is_flag == false;
						$arr_goods_no[$arr_cnt] = $GOODS_SUB_NO;

						if(startsWith("010202", $GS_GOODS_CATE))
							$arr_qty_no[$arr_cnt] = ceil(($refund_able_qty * $GOODS_CNT) / $DELIVERY_CNT_IN_BOX);
						else
							$arr_qty_no[$arr_cnt] = $refund_able_qty * $GOODS_CNT;
						//echo "���� ��ǰ ó�� �迭�� ������ ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
						
					}
				}

			} else {

				// ���� ��ǰ�� ���� ���
				if (sizeof($arr_goods_no) > 0) {
					for ($h = 0 ; $h < sizeof($arr_goods_no) ; $h++) {

						if (trim($arr_goods_no[$h]) == $GOODS_NO) {

							//echo " ���� ��ǰ�� ���� ��� ������ ������ ".$arr_goods_no[$g]. " : ".$arr_qty_no[$g]."^".($g)."<br>";
							$is_flag = true;
							$arr_qty_no[$h]  = $arr_qty_no[$h] + $refund_able_qty;
						}
					}

					if ($is_flag == false) {
						$arr_goods_no[$arr_cnt] = $GOODS_NO;

						$arr_qty_no[$arr_cnt] = $refund_able_qty;
						//echo " ���� ��ǰ�� ���� ��� �迭�� ������ ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
						$arr_cnt = $arr_cnt + 1;
					}

				} else {

					$is_flag == false;
					$arr_goods_no[$arr_cnt] = $GOODS_NO;
					
					$arr_qty_no[$arr_cnt] = $refund_able_qty;
					//echo "���� ��ǰ�� ���� ��� ó�� �迭�� ������ ". $OG_GOODS_CATE." ".$arr_goods_no[$arr_cnt]. " : ".$arr_qty_no[$arr_cnt]."^".$arr_cnt."<br>";
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
			$arr[$i]["QTY_NO"] = $arr_qty_no[$i];
			$arr_goods = selectGoods($conn, $arr_goods_no[$i]);
			$arr[$i]["GOODS_CODE"]	= trim($arr_goods[0]["GOODS_CODE"]);
			$arr[$i]["GOODS_NAME"]	= trim($arr_goods[0]["GOODS_NAME"]);
			$arr[$i]["GOODS_CATE"]	= trim($arr_goods[0]["GOODS_CATE"]);
			$arr[$i]["STOCK_CNT"]	= trim($arr_goods[0]["STOCK_CNT"]);
			$arr[$i]["FSTOCK_CNT"]	= trim($arr_goods[0]["FSTOCK_CNT"]);
			//$arr[$i]["TSTOCK_CNT"]	= trim($arr_goods[0]["TSTOCK_CNT"]);
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

</script>

</head>
<body id="popup_file">

<form name="frm" method="post" enctype="multipart/form-data">

<div id="popupwrap_file">
	<h1>��� Ȯ��</h1>
	<div id="postsch_code">
		<h2>* ���� ����Ʈ �Դϴ�.</h2>
		<div class="addr_inp">

		<table cellpadding="0" cellspacing="0" width="98%">
			<tr>
				<td>
			<table cellpadding="0" cellspacing="0" class="rowstable">
				<colgroup>
					<!--<col width="5%" />-->
					<col width="15%" />
					<col width="*" />
					<col width="10%"/>
					<col width="10%"/>
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<tr>
					<th>��ǰ�ڵ�</th>
					<th>�����</th>
					<th>�ֹ���</th>
					<th>�����</th>
					<th>�����</th>
					<th>���</th>
					<th class="end">�������</th>
				</tr>
				<?
					if(sizeof($arr) > 0) {
						for($i = 0; $i < sizeof($arr); $i++) {
							$goods_no		= $arr[$i]["GOODS_NO"];
							$goods_code		= $arr[$i]["GOODS_CODE"];
							$qty_no			= $arr[$i]["QTY_NO"];
							$goods_name		= $arr[$i]["GOODS_NAME"];
							$stock_cnt		= $arr[$i]["STOCK_CNT"];
							$fstock_cnt		= $arr[$i]["FSTOCK_CNT"];
							//$tstock_cnt		= $arr[$i]["TSTOCK_CNT"];
							$tstock_cnt = getCalcGoodsInOrdering($conn, $goods_no);

				?>
				<tr>
					<td><?=$goods_code?></td>
					<td class="modeual_nm"><?=$goods_name?> </td>
					<td class="price"><?=number_format($qty_no)?> ��</td>
					<td class="price">-<?=$tstock_cnt ?> ��</td>
					<td class="price"><?=number_format($fstock_cnt)?> ��</td>
					<td class="price"><?=number_format($stock_cnt)?> ��</td>
					<td class="price"><b><?=getSafeNumberFormatted($fstock_cnt + $stock_cnt - $tstock_cnt) ?></b> ��</td>
				</tr>
				<?
						}
					} else {
				?>
				<tr>
					<td height="30" colspan="5">����ǰ�� �����ϴ�</td>
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
	<div class="bot_close"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
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