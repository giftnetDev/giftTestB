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
	$menu_right = "BO003"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/order/order.php";

	$bb_code = "CLAIM";

#====================================================================
# DML Process
#====================================================================
	$title		= SetStringToDB($title);
	$contents = SetStringToDB($contents);

	if ($this_date == "") 
		$this_date = date("Y-m-d",strtotime("0 month"));

	if ($this_h == "") 
		$this_h = date("H",strtotime("0 month"));

	if ($this_i == "") 
		$this_i = date("i",strtotime("0 month"));

	if ($this_s == "") 
		$this_s = date("s",strtotime("0 month"));


	$temp_date = $this_date." ".$this_h.":".$this_i.":".$this_s;

	if ($mode == "I") {

#====================================================================
	$savedir1 = $g_physical_path."upload_data/board";
#====================================================================

		
		$contents = $contents."  ".$s_adm_nm." (".$temp_date.")<br/><br/>";

		$result =  insertBoard($conn, $bb_code, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no);
		
		$new_bb_no = $result;
		updateClaimExtra($conn, $reply, $s_adm_no, $bb_code, $new_bb_no);

	}

	if ($mode == "U") {

		if ($confirm_tf == "Y") {
			$result = updateClaimOrderFinish($conn, $cate_01,$cate_03, $bb_code, $bb_no, $s_adm_no);
		}

		$result = updateBoardConfirmTF($conn, $confirm_tf, $s_adm_no, $bb_code, $bb_no);
		$contents = $contents."  ".$s_adm_nm." (".$temp_date.")<br/><br/>";


		$result = updateBoard($conn, $cate_01, $cate_02, $cate_03, $cate_04, $writer_nm, $writer_pw, $email, $homepage, $title, $ref_ip, $recomm, $contents, $file_nm, $file_rnm, $file_path, $file_size, $file_ext, $keyword, $comment_tf, $use_tf, $s_adm_no, $bb_code, $bb_no);

		updateClaimExtra($conn, $reply, $s_adm_no, $bb_code, $bb_no);


	}


	if ($mode == "T") {

		updateBannerUseTF($conn, $use_tf, $s_adm_no, $bb_code, $bb_no);

	}

	if ($mode == "D") {
		
		
	//	$row_cnt = count($chk);
		
	//	for ($k = 0; $k < $row_cnt; $k++) {
		
	//		$tmp_banner_no = $chk[$k];

			$result = deleteBoard($conn, $s_adm_no, $bb_code, $bb_no);
		
//		}
	}

	if ($mode == "S") {

		$arr_rs = selectBoard($conn, $bb_code, $bb_no);
		

		$rs_bb_no						= trim($arr_rs[0]["BB_NO"]); 
		$rs_bb_code					= trim($arr_rs[0]["BB_CODE"]); 
		$rs_title						= SetStringFromDB($arr_rs[0]["TITLE"]); 
		$rs_contents				= SetStringFromDB($arr_rs[0]["CONTENTS"]); 
		$rs_file_nm					= trim($arr_rs[0]["FILE_NM"]); 
		$rs_file_rnm				= trim($arr_rs[0]["FILE_RNM"]); 
		$rs_file_size				= trim($arr_rs[0]["FILE_SIZE"]); 
		$rs_file_etc				= trim($arr_rs[0]["FILE_ETC"]); 
		$rs_cate_01					= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02					= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03					= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04					= trim($arr_rs[0]["CATE_04"]); 
		$rs_keyword					= trim($arr_rs[0]["KEYWORD"]); 
		$rs_use_tf					= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf					= trim($arr_rs[0]["DEL_TF"]);  

		$rs_writer_nm				= trim($arr_rs[0]["WRITER_NM"]);  
		$rs_writer_pw				= trim($arr_rs[0]["WRITER_PW"]);  
		$rs_email						= trim($arr_rs[0]["EMAIL"]);  
		$rs_homepage				= trim($arr_rs[0]["HOMEPAGE"]);  
		$rs_confirm_tf			= trim($arr_rs[0]["REPLY_STATE"]); 

		$rs_reply				= trim($arr_rs[0]["REPLY"]); 

		$content  = $rs_contents;

		$arr_order_rs = selectOrder($conn, $rs_cate_01);

		$rs_cp_no							= trim($arr_order_rs[0]["CP_NO"]); 
		$rs_order_no					= trim($arr_order_rs[0]["ORDER_NO"]); 
		$rs_reserve_no					= trim($arr_order_rs[0]["RESERVE_NO"]); 
		$rs_o_mem_nm					= trim($arr_order_rs[0]["O_MEM_NM"]); 
		$rs_o_zipcode					= trim($arr_order_rs[0]["O_ZIPCODE"]); 
		$rs_o_addr1						= trim($arr_order_rs[0]["O_ADDR1"]); 
		$rs_o_addr2						= trim($arr_order_rs[0]["O_ADDR2"]); 
		$rs_o_phone						= trim($arr_order_rs[0]["O_PHONE"]); 
		$rs_o_hphone					= trim($arr_order_rs[0]["O_HPHONE"]); 
		$rs_o_email						= trim($arr_order_rs[0]["O_EMAIL"]); 
		$rs_r_mem_nm					= trim($arr_order_rs[0]["R_MEM_NM"]); 
		$rs_r_zipcode					= trim($arr_order_rs[0]["R_ZIPCODE"]); 
		$rs_r_addr1						= trim($arr_order_rs[0]["R_ADDR1"]); 
		$rs_r_addr2						= trim($arr_order_rs[0]["R_ADDR2"]); 
		$rs_r_phone						= trim($arr_order_rs[0]["R_PHONE"]); 
		$rs_r_hphone					= trim($arr_order_rs[0]["R_HPHONE"]); 
		$rs_r_email						= trim($arr_order_rs[0]["R_EMAIL"]); 
		$rs_order_date					= trim($arr_order_rs[0]["ORDER_DATE"]); 
		$rs_recomm						= trim($arr_order_rs[0]["RECOMM"]); 

		$arr_goods = listClaimGoods($conn, $bb_no, $rs_reserve_no);

	}

	if ($result) {
		$strParam = $strParam."?nPage=".$nPage."&&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
		$strParam .= $strParam."&con_cate_04=".$con_cate_04."&reply_state=".$reply_state."&con_cate_02=".$con_cate_02;
		$strParam .= $strParam."&cp_type=".$cp_type."&start_date=".$start_date."&end_date=".$end_date;
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		document.location.href = "claim_list.php<?=$strParam?>";
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript" type="text/javascript">


function js_list() {
	var frm = document.frm;
		
	frm.contents.value = "";
	frm.keyword.value = "";
	frm.method = "post";
	frm.action = "claim_list.php";
	frm.submit();
}


function js_save() {

	var frm = document.frm;
	var bb_no = "<?= $bb_no ?>";
	
	frm.title.value = frm.title.value.trim();
	
	if (isNull(frm.title.value)) {
		alert('제목을 입력해주세요.');
		frm.title.focus();
		return ;		
	}

	if (document.frm.rd_use_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_use_tf[0].checked == true) {
			frm.use_tf.value = "Y";
		} else {
			frm.use_tf.value = "N";
		}
	}

	if (document.frm.rd_confirm_tf == null) {
		//alert(document.frm.rd_use_tf);
	} else {
		if (frm.rd_confirm_tf[0].checked == true) {
			frm.confirm_tf.value = "Y";
		} else {
			frm.confirm_tf.value = "N";
		}
	}

	if (isNull(bb_no)) {
		frm.mode.value = "I";
	} else {
		frm.mode.value = "U";
		frm.bb_no.value = frm.bb_no.value;
	}

	oEditors[0].exec("UPDATE_CONTENTS_FIELD", []);   // 에디터의 내용이 textarea에 적용된다.
	frm.method = "post";
	frm.target = "";
	frm.action = "<?=$_SERVER[PHP_SELF]?>";
	frm.submit();

}

	function js_view(rn, reserve_no) {

		var frm = document.frm;
		
		var url = "/manager/order/order_read.php?reserve_no="+reserve_no;

		NewWindow(url, 'order_detail','860','600','YES');
	}


function js_goods_view(goods_no) {

	var frm = document.frm;
	
	frm.goods_no.value = goods_no;
	frm.mode.value = "S";
	frm.target = "blank";
	frm.method = "post";
	frm.action = "/manager/goods/goods_write.php";
	frm.submit();
	
}

function js_delete() {

	var frm = document.frm;
//	var chk_cnt = 0;

//	check=document.getElementsByName("chk[]");
	
//	for (i=0;i<check.length;i++) {
//		if(check.item(i).checked==true) {
//			chk_cnt++;
//		}
//	}
	
//	if (chk_cnt == 0) {
//		alert("선택 하신 자료가 없습니다.");
//	} else {

		bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

//	}
}

</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="seq_no" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bb_no" value="<?=$bb_no?>" />
<input type="hidden" name="bb_code" value="<?=$bb_code?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="start_date" value="<?=$start_date?>" />
<input type="hidden" name="end_date" value="<?=$end_date?>" />
<input type="hidden" name="search_field" value="<?=$search_field?>" />
<input type="hidden" name="search_str" value="<?=$search_str?>" />
<input type="hidden" name="reply_state" value="<?=$reply_state?>" />
<input type="hidden" name="con_cate_01" value="<?=$con_cate_01?>" />
<input type="hidden" name="con_cate_02" value="<?=$con_cate_02?>" />
<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>" />
<input type="hidden" name="con_cate_04" value="<?=$con_cate_04?>" />
<input type="hidden" name="cp_type" value="<?=$cp_type?>" />
<input type="hidden" name="order_field" value="<?=$order_field?>" />
<input type="hidden" name="order_str" value="<?=$order_str?>" />
<input type="hidden" name="goods_no" value="" />

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
        <h2>클레임 관리</h2>  
        <table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
          <col width="120" />
          <col width="*" />
        </colgroup>
        <tr>
          <th>상품명</th>
          <td colspan="3" class="line">
						<input type="hidden" name="title" value="<?=$rs_title?>" /><?=$rs_title?>
					</td>
        </tr>
        <tr>
          <th>주문번호</th>
          <td class="line">
						<input type="hidden" name="cate_01" value="<?=$rs_cate_01?>" /><a href="javascript:js_view('<?=$rn?>','<?=$rs_cate_01?>');"><?=$rs_cate_01?></a>
					</td>
          <th>클레임구분</th>
          <td class="line">
						<input type="hidden" name="cate_04" value="<?=$rs_cate_04?>" /><?=getDcodeName($conn,"ORDER_STATE",$rs_cate_04)?>
					</td>
				</tr>
        <tr>
          <th>공급업체</th>
          <td class="line">
						<?=getCompanyName($conn,$rs_keyword)?>
					</td>
          <th>사유</th>
          <td class="line">
						<!-- input type="hidden" name="cate_02" value="<?=$rs_cate_02?>" / -->
						<?=makeSelectBox($conn,"CLAIM_TYPE", "cate_02","200", "클레임 사유를 선택하세요.", "", $rs_cate_02)?>
						<script>
							$(function(){
								
								claim_options_all = $("select[name=cate_02] option").clone();

								var claim_type = $("select[name=cate_02]").find('option').remove().end();
								if($("input[name=cate_04]").val() == "6") //취소
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CC") == 0)
											$("select[name=cate_02]").append(item);

									});
								}
								else if($("input[name=cate_04]").val() == "7") //반품
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CR") == 0)
											$("select[name=cate_02]").append(item);

									});
								}
								else if($("input[name=cate_04]").val() == "8") //교환
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CE") == 0)
											$("select[name=cate_02]").append(item);

									});
								}
								else if($("input[name=cate_04]").val() == "99") //기타
								{
									claim_options_all.each(function(index, item){

										if(item.value.indexOf("CX") == 0)
											$("select[name=cate_02]").append(item);

									});
								}

							});
							</script>
					</td>
        </tr>

        <tr>
          <th>주문자</th>
          <td class="line">
						<?=$rs_o_mem_nm?>
					</td>
          <th>주문자연락처</th>
          <td class="line">
						<?=$rs_o_phone?>&nbsp;&nbsp;&nbsp;<?=$rs_o_hphone?>
					</td>
        </tr>

        <tr>
          <th>수령자</th>
          <td class="line">
						<?=$rs_r_mem_nm?>
					</td>
          <th>수령자연락처</th>
          <td class="line">
						<?=$rs_r_phone?>&nbsp;&nbsp;&nbsp;<?=$rs_r_hphone?>
					</td>
        </tr>

		<tr> 
            <th>입/출고상품 리스트</th>
            <td colspan="3" class="line">
				<? if(sizeof($arr_goods) > 0) {?>
				<table cellpadding="0" cellspacing="0" class="rowstable" style="margin-top:5px; width:98%;">
					<colgroup>
					  <col width="4%" />
					  <col width="11%" />
					  <col width="12%" />
					  <col width="*" />
					  <col width="8%" />
					  <col width="10%" />
					  <col width="12%" />
					  <col width="10%" />
					</colgroup>
					<thead>
					<tr>
					  <th>No.</th>
					  <th>재고유형</th>
					  <th>재고종류</th>
					  <th>상품명</th>
					  <th>수량</th>
					  <th>사유</th>
					  <th>사유상세</th>
					  <th class="end">입출고일</th>
					</tr>
					</thead>
					<tbody class="sub_goods_list">
					<? 
						for($i = 0; $i < sizeof($arr_goods); $i++) {
							$STOCK_TYPE = $arr_goods[$i]["STOCK_TYPE"];
							$STOCK_CODE = $arr_goods[$i]["STOCK_CODE"];
							$GOODS_NAME = $arr_goods[$i]["GOODS_NAME"];
							$GOODS_NO	= $arr_goods[$i]["GOODS_NO"];
							$IN_QTY		= $arr_goods[$i]["IN_QTY"];
							$IN_FQTY	= $arr_goods[$i]["IN_FQTY"];
							$IN_BQTY	= $arr_goods[$i]["IN_BQTY"];
							$OUT_QTY	= $arr_goods[$i]["OUT_QTY"];
							$OUT_FQTY	= $arr_goods[$i]["OUT_FQTY"];
							$OUT_BQTY	= $arr_goods[$i]["OUT_BQTY"];
							$OUT_QTY	= $arr_goods[$i]["OUT_QTY"];
							$IN_DATE	= $arr_goods[$i]["IN_DATE"];
							$OUT_DATE	= $arr_goods[$i]["OUT_DATE"];
							$IN_LOC		= $arr_goods[$i]["IN_LOC"];
							$IN_LOC_EXT	= $arr_goods[$i]["IN_LOC_EXT"];
	

							if($IN_DATE <> '0000-00-00 00:00:00') 
								$IN_DATE = date("Y-m-d",strtotime($IN_DATE));
							
							if($OUT_DATE <> '0000-00-00 00:00:00')
								$OUT_DATE = date("Y-m-d",strtotime($OUT_DATE));

						$str_row_background = "";
						if($STOCK_TYPE == "IN") { 
							if($STOCK_CODE == "FST02") 
								$str_row_background = "style='background-color:yellow;'";
							if(($STOCK_CODE == "NST01" || $STOCK_CODE == "BST03") && $IN_LOC == "LOCD") //클레임 + 정상입고 or 불량입고
								$str_row_background = "style='background-color:#42f450;'";
							if(($STOCK_CODE == "NST01" || $STOCK_CODE == "BST03") && $IN_LOC == "LOCD" && strpos($IN_LOC_EXT, "내품유실") > 0) //클레임 + 정상입고 or 불량입고 + 내품유실 
								$str_row_background = "style='background-color:#E6A1A1;'";
					?>
						<tr height="30" <?=$str_row_background?>>
							<td><?=$i+1?></td>
							<td>입고</td>
							<td><?=getDcodeName($conn, "IN_ST", $STOCK_CODE)?></td>
							<td class="modeual_nm"> <?=$GOODS_NAME?>[<a href="javascript:js_goods_view('<?=$GOODS_NO?>')"><?=$GOODS_NO?></a>]</td>
							<td>
								<? 
									if(startsWith($STOCK_CODE, "N")) { 
										echo $IN_QTY;
									} else if(startsWith($STOCK_CODE, "B")) { 
										echo $IN_BQTY;
									} else if(startsWith($STOCK_CODE, "F")) { 
										echo $IN_FQTY;
									}
								?> 개
							</td>
							<td class="modeual_nm"><?=getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td class="modeual_nm"><?=$IN_LOC_EXT?></td>
							<td><?=$IN_DATE?></td>
						</tr>
					<?
						} else { 
					?>
						<tr height="30">
							<td><?=$i+1?></td>
							<td>출고</td>
							<td><?=getDcodeName($conn, "OUT_ST", $STOCK_CODE)?></td>
							<td class="modeual_nm"> <?=$GOODS_NAME?>[<a href="javascript:js_goods_view('<?=$GOODS_NO?>')"><?=$GOODS_NO?></a>]</td>
							<td>
								<? 
									if(startsWith($STOCK_CODE, "N")) { 
										echo $OUT_QTY;
									} else if(startsWith($STOCK_CODE, "B")) { 
										echo $OUT_BQTY;
									} else if(startsWith($STOCK_CODE, "F")) { 
										echo $OUT_FQTY;
									}
								?> 개
							</td>
							<td class="modeual_nm"><?=getDcodeName($conn, "LOC", $IN_LOC)?></td>
							<td class="modeual_nm"><?=$IN_LOC_EXT?></td>
							<td><?=$OUT_DATE?></td>
						</tr>
					<?
						}

						}
					?>
					</tbody>
				</table>
				<?} else {?>
				데이터가 없습니다.
				<? } ?>
			</td>
        </tr>
		<!--
	    <tr> 
          <th>사유 상세</th>
          <td colspan="3" class="contentswrite line">
						<textarea name="reply"  style="padding-left:0px;width:890px;height:100px;"><?=$rs_reply?></textarea>
					</td>
        </tr>
		-->
        <tr> 
            <th>클레임 사유 <br> 처리 내용</th>
            <td colspan="3" class="contentswrite line">
				<span class="fl" style="padding-left:0px;width:100%;height:500px;">
					<textarea name="contents" id="contents"  style="padding-left:0px;width:100%;height:400px;"><?=$rs_contents?></textarea>
				</span>
			</td>
        </tr>
				<tr class="end"> <!-- 가장 마지막에 오는 tr 엘리먼트에 end 클래스 붙여주세요 -->
					<th>접수구분</th>
					<td  colspan="3" class="choices">
						<input type="radio" class="radio" name="rd_confirm_tf" value="Y" <? if ($rs_confirm_tf =="Y") echo "checked"; ?>> 처리완료 <span style="width:20px;"></span>
						<input type="radio" class="radio" name="rd_confirm_tf" value="N" <? if ($rs_confirm_tf =="N" || $rs_confirm_tf =="") echo "checked"; ?>> 접수
						<input type="hidden" name="confirm_tf" value="<?= $rs_confirm_tf ?>"> 
						<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						<input type="hidden" name="keyword" value="<?=$rs_keyword?>"/>
						<input type="hidden" name="writer_nm" value="<?=$rs_writer_nm?>"/>
						<input type="hidden" name="writer_pw" value="<?=$rs_writer_pw?>"/>
						<input type="hidden" name="email" value="<?=$rs_email?>"/>
						<input type="hidden" name="homepage" value="<?=$rs_homepage?>"/>
						<input type="hidden" name="cate_03" value="<?=$rs_cate_03?>"/>
					</td>
				</tr>

	    
        </table>
        <div class="btnright">
          <a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
          <a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($bb_no <> "") {?>
          <a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
					<? } ?>
        </div>      
      </div>
      <!-- // E: mwidthwrap -->

    </td>
  </tr>
  </table>
</div>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<SCRIPT LANGUAGE="JavaScript">

var oEditors = [];
	nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "contents",
	sSkinURI: "../../_common/SE2.1.1.8141/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true, 
		fOnBeforeUnload : function(){ 
			// alert('야') 
		},
		fOnAppLoad : function(){ 
		// 이 부분에서 FOCUS를 실행해주면 됩니다. 
		this.oApp.exec("EVENT_EDITING_AREA_KEYDOWN", []); 
		this.oApp.setIR(""); 
		//oEditors.getById["ir1"].exec("SET_IR", [""]); 
		}
	}, 
	fCreator: "createSEditor2"
});

//-->
</SCRIPT>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>