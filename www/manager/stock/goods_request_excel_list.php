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
	$menu_right = "SG017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";


#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$nPage			= 1;
	$nPageSize	= 10000;
	$nListCnt   = 10000;

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$filter = array('con_delivery_tf' => $con_delivery_tf, 'con_to_here' => $con_to_here, 'con_cancel_tf' => $con_cancel_tf, 'con_confirm_tf' => $con_confirm_tf, 'con_changed_tf' => $con_changed_tf, 'con_wrap_tf' => $con_wrap_tf, 'con_sticker_tf' => $con_sticker_tf, 'con_receive_tf' => $con_receive_tf, 'chk_after_confirm' => $chk_after_confirm);

#====================================================================
# DML Process
#====================================================================

	$file_name="발주 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	switch($print_type) {
		case "BUY_CP" : 
				$arr_rs = listGoodsRequestDistinctBuyCP($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $order_field, $order_str);
				break;
		default : 
				$arr_rs = listGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);
				break;
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b>발주 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<?if($print_type == "BUY_CP") { ?>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>매입업체</td>
		<td align='center' bgcolor='#F4F1EF'>이메일</td>
	</tr>
	<?
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				$BUY_CP_NM 	= trim($arr_rs[$j]["BUY_CP_NM"]);
				$SENT_EMAIL = trim($arr_rs[$j]["SENT_EMAIL"]);
	?>
			<tr>
				<td bgColor='#FFFFFF' align='left'><?=$BUY_CP_NM?></td>
				<td bgColor='#FFFFFF' align='left'><?=$SENT_EMAIL?></td>
			</tr>
	<?			
						
			}
		} else { 
	?> 
			<tr>
				<td>데이터가 없습니다. </td>
			</tr>
	<? 
		}
	?>
</table>
<? } else { ?>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>순</td>
		<td align='center' bgcolor='#F4F1EF'>발주일자</td>
		<td align='center' bgcolor='#F4F1EF'>매입업체</td>
		<td align='center' bgcolor='#F4F1EF'>제품명</td>
		<td align='center' bgcolor='#F4F1EF'>단가</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>납품처</td> 
		<td align='center' bgcolor='#F4F1EF'>입고처</td>
		<td align='center' bgcolor='#F4F1EF'>입고일자</td>
		<td align='center' bgcolor='#F4F1EF'>작업</td>
		<td align='center' bgcolor='#F4F1EF'>입고수량</td>
		<td align='center' bgcolor='#F4F1EF'>취소여부</td>
	</tr>
	<?
		
		if (sizeof($arr_rs) > 0) {
			$TEMP_DATE = "";
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$REQ_NO						= trim($arr_rs[$j]["REQ_NO"]);
				$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
				$REQ_DATE					= trim($arr_rs[$j]["REQ_DATE"]);
				$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
				$BUY_CP_NM					= trim($arr_rs[$j]["BUY_CP_NM"]);
				$BUY_MANAGER_NM				= trim($arr_rs[$j]["BUY_MANAGER_NM"]);
				$BUY_CP_PHONE				= trim($arr_rs[$j]["BUY_CP_PHONE"]);
				$TOTAL_REQ_QTY				= trim($arr_rs[$j]["TOTAL_REQ_QTY"]);
				$TOTAL_BUY_TOTAL_PRICE		= trim($arr_rs[$j]["TOTAL_BUY_TOTAL_PRICE"]);
				$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
				$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
				$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
				$IS_SENT					= trim($arr_rs[$j]["IS_SENT"]);
				$SENT_DATE					= trim($arr_rs[$j]["SENT_DATE"]);

				if($SENT_DATE == "0000-00-00 00:00:00")
					$SENT_DATE = "";
				else
					$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

				$REQ_DATE = date("m월 d일",strtotime($REQ_DATE));
				$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));
	
	?>

	<?
				$arr_rs_goods = listGoodsRequestGoods($conn, $REQ_NO, $con_cancel_tf);
				if (sizeof($arr_rs_goods) > 0) {
					
					for ($k = 0 ; $k < sizeof($arr_rs_goods); $k++) {

						if($REQ_DATE != $TEMP_DATE) {
							$TEMP_DATE = $REQ_DATE;
							$n = 0;
						}

						$n++;
						
						$GOODS_NO					= trim($arr_rs_goods[$k]["GOODS_NO"]);
						$GOODS_NAME					= SetStringFromDB($arr_rs_goods[$k]["GOODS_NAME"]);
						$GOODS_SUB_NAME				= SetStringFromDB($arr_rs_goods[$k]["GOODS_SUB_NAME"]);
						$BUY_PRICE					= trim($arr_rs_goods[$k]["BUY_PRICE"]);
						$REQ_QTY					= trim($arr_rs_goods[$k]["REQ_QTY"]);
						$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$k]["BUY_TOTAL_PRICE"]);
						$RECEIVE_QTY				= trim($arr_rs_goods[$k]["RECEIVE_QTY"]);
						$RECEIVE_DATE				= trim($arr_rs_goods[$k]["RECEIVE_DATE"]);
						$RECEIVER_NM				= trim($arr_rs_goods[$k]["RECEIVER_NM"]);
						$TO_HERE					= trim($arr_rs_goods[$k]["TO_HERE"]);
						$MEMO1						= trim($arr_rs_goods[$k]["MEMO1"]);
						$MEMO2						= trim($arr_rs_goods[$k]["MEMO2"]);
						$CANCEL_TF					= trim($arr_rs_goods[$k]["CANCEL_TF"]);

						$GOODS_CODE = getGoodsCode($conn, $GOODS_NO);

						if($RECEIVE_DATE != "0000-00-00 00:00:00")
							$RECEIVE_DATE = date("Y-m-d",strtotime($RECEIVE_DATE));
						else
							$RECEIVE_DATE = "입고전";

						if($TO_HERE != "Y") { 
										
							$arr_order_delivery_paper = getOrderGoodsDeliveryPaper($conn, $ORDER_GOODS_NO);
							
							if(sizeof($arr_order_delivery_paper) > 0) {
								$DELIVERY_NO = $arr_order_delivery_paper[0]["DELIVERY_NO"];
								$DELIVERY_CP = $arr_order_delivery_paper[0]["DELIVERY_CP"];
								$DELIVERY_DATE = $arr_order_delivery_paper[0]["DELIVERY_DATE"];

								$trace = getDeliveryUrl($conn, $DELIVERY_CP);
								$trace = $trace.trim($DELIVERY_NO);
								//echo $DELIVERY_CP."//".$DELIVERY_NO."<br/>";

								if($DELIVERY_DATE != "0000-00-00 00:00:00" && $DELIVERY_DATE != "")
									$DELIVERY_DATE = "<font color='blue'>".date("Y-m-d H:i",strtotime($DELIVERY_DATE))."</font>";
								else
									$DELIVERY_DATE = "<font color='red'>입고전</font>";

							} 
						} 

	?>
			<tr>
				<td bgColor='#FFFFFF' align='left'><?=$n?></td>
				<td bgColor='#FFFFFF' align='center'><?=$REQ_DATE?></td>
				<td bgColor='#FFFFFF' align='left'><?=$BUY_CP_NM?></td>
				<td bgColor='#FFFFFF' align='left'><?="[".$GOODS_CODE."] ".$GOODS_NAME." ".$GOODS_SUB_NAME ?></td>
				<td bgColor='#FFFFFF' align='right'><?= number_format($BUY_PRICE)?></td>
				<td bgColor='#FFFFFF' align='right'><?= number_format($REQ_QTY)?></td>
				<td bgColor='#FFFFFF' align='left'><?=$MEMO2?></td>
				<td bgColor='#FFFFFF' align='left'><?=($TO_HERE == "Y" ? $RECEIVER_NM : "직송(".$RECEIVER_NM.")") ?></td>
				<td bgColor='#FFFFFF' align='center'><?= $RECEIVE_DATE ?></td>
				<td bgColor='#FFFFFF' align='left'><?=$MEMO1?></td>
				<? if ($TO_HERE == "Y") {?>
					<td bgColor='#FFFFFF' align='right'><?= $RECEIVE_QTY ?></td>
				<? } else { ?>
					<td bgColor='#FFFFFF' align='right'>
						<? if ($DELIVERY_NO) {?>
							<?=$DELIVERY_CP?>(<?=$DELIVERY_NO?>)<?= $DELIVERY_DATE ?>
						<? } ?>
					</td>
				<? } ?>
				<td bgColor='#FFFFFF' align='right'><?= $RECEIVE_QTY ?></td>
				<td bgColor='#FFFFFF' align='right'><?= ($CANCEL_TF == "Y" ? "취소됨" : "") ?></td>
			</tr>
	<?			
						
					}
				}
			}
		} else { 
	?> 
			<tr>
				<td colspan="10">데이터가 없습니다. </td>
			</tr>
	<? 
		}
	?>
</table>
<? } ?>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>