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
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/work/work.php";
	require "../../_classes/biz/order/order.php";

#====================================================================
# Request Parameter
#====================================================================

	$req_no = trim($req_no);

#===============================================================
# Get Search list count
#===============================================================


	if($mode == "FROM_ORDER") { 

		$today = date("Y-m-d", strtotime("0 month"));

		$goods_no = trim($goods_no);
		$order_qty	= getRefundAbleQty($conn, $reserve_no, $order_goods_no);

		// $memo1 비고1
		$rs_order_goods = selectOrderGoods($conn, $order_goods_no);
		$rs_goods_no			= trim($rs_order_goods[0]["GOODS_NO"]);
		$rs_opt_wrap_no			= trim($rs_order_goods[0]["OPT_WRAP_NO"]);
		$rs_opt_sticker_no		= trim($rs_order_goods[0]["OPT_STICKER_NO"]);
		$rs_opt_sticker_ready	= trim($rs_order_goods[0]["OPT_STICKER_READY"]);
		$rs_opt_outbox_tf		= trim($rs_order_goods[0]["OPT_OUTBOX_TF"]);
		$rs_opt_sticker_msg		= trim($rs_order_goods[0]["OPT_STICKER_MSG"]);
		$rs_opt_print_msg		= trim($rs_order_goods[0]["OPT_PRINT_MSG"]);
		$rs_opt_memo			= trim($rs_order_goods[0]["OPT_MEMO"]);

		$rs_delivery_type		= trim($rs_order_goods[0]["DELIVERY_TYPE"]);
		
		$memo1	= "";

		if($rs_delivery_type != "98") { 
			$memo1 .= ($rs_opt_print_msg <> "" ? "인쇄메세지 : ".$rs_opt_print_msg." / " : "");
			
		} else {
			$memo1 .= ($rs_opt_sticker_no <> "0" ? "스티커 : ".getGoodsName($conn, $rs_opt_sticker_no)." / " : "");
			$memo1 .= ($rs_opt_outbox_tf == "Y" ? "아웃박스스티커 : 있음 / " : "" );
			$memo1 .= ($rs_opt_wrap_no <> "0" ? "포장지 : ".getGoodsName($conn, $rs_opt_wrap_no). " / " : "");
			$memo1 .= ($rs_opt_sticker_msg <> "" ? "스티커메세지 : ".$rs_opt_sticker_msg. " / " : "");
			$memo1 .= ($rs_opt_print_msg <> "" ? "인쇄메세지 : ".$rs_opt_print_msg. " / " : "");
			$memo1 .= ($rs_opt_memo <> "" ? "작업메모 : ".$rs_opt_memo. " / " : "");
			$memo1 = rtrim($memo1, '/ ');
		}

		// 업체가 몰일 경우에 수령자를, 몰이 아닐 경우엔 판매업체명을 표기 (2016-05-12) - $r_mem_nm 비고2
		if(isCompanyMall($conn, $cp_no))
			$memo2 = $o_mem_nm;
		else
			$memo2 = $cp_nm;

		$arr_sub_goods = getSubGoodsInfo($conn, $goods_no);
			
		if (sizeof($arr_sub_goods) > 0) {

			for ($k = 0 ; $k < sizeof($arr_sub_goods); $k++) {
				$goods_no		= trim($arr_sub_goods[$k]["GOODS_SUB_NO"]);
				$req_qty		= trim($arr_sub_goods[$k]["GOODS_CNT"]) * $order_qty;

				$arr_goods = selectGoods($conn, $goods_no);
				$GOODS_CATE = trim($arr_goods[0]["GOODS_CATE"]);
				if(startsWith($GOODS_CATE , "0102"))
					continue;

				$buy_cp_no = trim($arr_goods[0]["CATE_03"]);
				$buy_price = trim($arr_goods[0]["BUY_PRICE"]);
				$goods_code = trim($arr_goods[0]["GOODS_CODE"]);
				$goods_name = trim($arr_goods[0]["GOODS_NAME"]);
				$goods_sub_name = trim($arr_goods[0]["GOODS_SUB_NAME"]);

				$group_no = cntMaxGroupNoRequest($conn);
				$req_date = $today;
				$delivery_type = "";
				$memo = "";
				$buy_total_price = $buy_price * $req_qty;

				if($rs_delivery_type == "98") //외부업체발송
					$chk_to_here = false;
				else
					$chk_to_here = true;

				$arr_req = getReqNoFromBuyCPNo($conn, $buy_cp_no); 
				if(sizeof($arr_req) <= 0) { 
					$req_no = insertGoodsRequest($conn, $s_adm_com_code, $group_no, $req_date, $buy_cp_no, $delivery_type, $memo, $s_adm_no);
				} else { 
					$req_no = $arr_req[0]["REQ_NO"];
					$group_no = $arr_req[0]["GROUP_NO"];
				}
				insertGoodsRequestGoods($conn, $s_adm_com_code, $req_no, $reserve_no, $order_goods_no, $group_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $s_adm_no);
				resetGoodsRequestTotal($conn, $req_no);
			}

		} else {

				$req_qty		= $order_qty;

				$arr_goods = selectGoods($conn, $goods_no);
				$buy_cp_no = trim($arr_goods[0]["CATE_03"]);
				$buy_price = trim($arr_goods[0]["BUY_PRICE"]);
				$goods_code = trim($arr_goods[0]["GOODS_CODE"]);
				$goods_name = trim($arr_goods[0]["GOODS_NAME"]);
				$goods_sub_name = trim($arr_goods[0]["GOODS_SUB_NAME"]);

				$group_no = cntMaxGroupNoRequest($conn);
				$req_date = $today;
				$delivery_type = "";
				$memo = "";
				$buy_total_price = $buy_price * $req_qty;
				
				if($rs_delivery_type == "98") //외부업체발송
					$chk_to_here = false;
				else
					$chk_to_here = true;
				
				$arr_req = getReqNoFromBuyCPNo($conn, $buy_cp_no); 
				if(sizeof($arr_req) <= 0) { 
					$req_no = insertGoodsRequest($conn, $s_adm_com_code, $group_no, $req_date, $buy_cp_no, $delivery_type, $memo, $s_adm_no);
				} else { 
					$req_no = $arr_req[0]["REQ_NO"];
					$group_no = $arr_req[0]["GROUP_NO"];
				}

				insertGoodsRequestGoods($conn, $s_adm_com_code, $req_no, $reserve_no, $order_goods_no, $group_no, $goods_no, $goods_code, $goods_name, $goods_sub_name, $buy_price, $req_qty, $buy_total_price, $chk_to_here, $memo1, $memo2, $s_adm_no);
				resetGoodsRequestTotal($conn, $req_no);

		}
		
		$mode = "";

	}

?>

<script>
	window.location.replace("/manager/stock/goods_request_write.php?req_no=" + <?=$req_no?>);
</script>
