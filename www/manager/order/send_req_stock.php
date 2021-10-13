<?
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/stock/stock.php";

	$recive_email = trim($recive_email);
	$today = date("Y-m-d",strtotime("0 month"));

	$mail_body = "";
	//$mail_body .= "* 금일 주문 건 수 대비 부족 분 발주 요청 입니다.";
	$mail_body .= "* 금일 발주 요청 입니다.";

	$mail_body .= "<table cellpadding='0' cellspacing='0' style='width: 95%; border-top: 1px solid #86a4b3;'>";
	$mail_body .= "<colgroup>";
	$mail_body .= "<col width='20%' />";
	$mail_body .= "<col width='50%' />";
	$mail_body .= "<col width='15%' />";
	$mail_body .= "<col width='15%' />";
	$mail_body .= "</colgroup>";

	$mail_body .= "<tr style='padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-bottom: 1px solid #d2dfe5; background: #ebf3f6 url(\"http://www.giftace.or.kr/manager/images/admin/bg_bar_01.gif\") right center no-repeat;'>";
	$mail_body .= "<th style='padding: 9px 0 6px 0;'>자재코드</th>";
	$mail_body .= "<th style='padding: 9px 0 6px 0;'>자재명</th>";
	$mail_body .= "<th style='padding: 9px 0 6px 0;'>박스입수</th>";
	$mail_body .= "<th style='background: #ebf3f6;'> 발주요청박스수량</th>";
	$mail_body .= "</tr>";


	
	$row_cnt = count($req_goods_no);

	for ($k = 0; $k < $row_cnt; $k++) {

		$tmp_chk_req             = $chk_req[$k];
		$tmp_goods_no		      = $req_goods_no[$k];
		$tmp_goods_code		      = $req_goods_code[$k];
		$tmp_goods_nm			  = $req_goods_nm[$k];
		$tmp_goods_order	      = $req_goods_order[$k];
		$tmp_f_stock			  = $req_f_stock[$k];
		$tmp_n_stock			  = $req_n_stock[$k];
		$tmp_a_stock			  = $req_a_stock[$k];
		$tmp_req_cnt			  = $req_cnt[$k];
		$tmp_req_goods_cnt_in_box = $req_goods_cnt_in_box[$k];


		if(!in_array($tmp_goods_no, $chk_req)) continue;

		$mail_body .= "<tr style='height:25px;'>";
		$mail_body .= "<td style='text-align: center; vertical-align: middle; background: url(\"http://www.giftace.or.kr/manager/images/admin/dotline_01.gif\") left bottom repeat-x;'>".$tmp_goods_code."</td>";
		$mail_body .= "<td style='text-align: left; vertical-align: middle; background: url(\"http://www.giftace.or.kr/manager/images/admin/dotline_01.gif\") left bottom repeat-x;'>".$tmp_goods_nm."</td>";
		$mail_body .= "<td style='text-align: right; padding-right:10px;vertical-align: middle; background: url(\"http://www.giftace.or.kr/manager/images/admin/dotline_01.gif\") left bottom repeat-x;'>".$tmp_req_goods_cnt_in_box."</td>";
		$mail_body .= "<td style='text-align: right; padding-right:10px;vertical-align: middle; background: url(\"http://www.giftace.or.kr/manager/images/admin/dotline_01.gif\") left bottom repeat-x;'>".$tmp_req_cnt."</td>";
		$mail_body .= "</tr>";


		///////// 가재고 입력 /////////////
		
		$stock_type = "IN";

		$goods_no = $tmp_goods_no;
		$stock_code = "FST02";
		$cp_type = 2;
		$qty = $tmp_req_cnt * $tmp_req_goods_cnt_in_box;
		$buy_price = '';
		$in_loc = "LOCA";
		$in_loc_ext = "발주";
		$in_date = $today;
		$pay_date = $today;

		$in_qty		= 0;
		$in_bqty	= 0;
		$in_fqty	= $qty;

		$in_cp_no = $cp_type;
		$in_price = $buy_price;
		$close_tf = "N";

		$result = insertStock($conn, $stock_type, $stock_code, $in_cp_no, $out_cp_no, $goods_no, $in_loc, $in_loc_ext, $in_qty, $in_bqty, $in_fqty, $out_qty, $out_bqty, $out_fqty, $in_price, $out_price, $in_date, $out_date, $pay_date, $reserve_no, $close_tf, $s_adm_no,$memo);
		

	}
	


	$mail_body .= "</table>";
		
	$EMAIL	= "gift@giftnet.co.kr";
	$NAME		= "자재 담당자";
	$SUBJECT = $today." 일자 자재 발주서 입니다.";

	$CONTENT = $mail_body;
	$mailto	 = $recive_email;

	$result = sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $mailto);

	$result = sendMail($EMAIL, $NAME, $SUBJECT, $CONTENT, $EMAIL);

?>
<?=$mail_body?>