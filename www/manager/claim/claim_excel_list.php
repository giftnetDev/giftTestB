<?session_start();?>
<?
# =============================================================================
# File Name    : claim_list.php
# Modlue       : 
# Writer       : Park Chan Ho 
# Create Date  : 2009.05.21
# Modify Date  : 
#	Copyright : Copyright @MONEUAL Corp. All Rights Reserved.
# =============================================================================

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

	$file_name="클레임 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	  header( "Content-Description: orion70kr@gmail.com" );
	
	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";


if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { 
	$cp_type = $s_adm_com_code;
}

//echo $cp_type;
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
# Request Parameter
#====================================================================

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$con_cate_01 = trim($con_cate_01);
	$con_cate_02 = trim($con_cate_02);
	$con_cate_03 = trim($con_cate_03);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
#============================================================
# Page process
#============================================================

	$con_use_tf		= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";

	$arr_rs = listBoardClaim($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

	$arr_rs_order_state = totalCntClaimOrderState($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str);

	$arr_rs_claim_type = totalCntClaimType($conn, $start_date, $end_date, $bb_code, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $cp_type, $reply_state, $adm_no, $con_use_tf, $del_tf, $search_field, $search_str);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>

<body>

<font size=3><b><?=$Admin_shop_name?> 클레임 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>클레임종류</td>
		<td align='center' bgcolor='#F4F1EF'>총합</td>
	</tr>
	<?
		if (sizeof($arr_rs_order_state) > 0) {
			for ($k = 0 ; $k < sizeof($arr_rs_order_state); $k++) {

				$DCODE	= trim($arr_rs_order_state[$k]["DCODE"]);
				$ORDER_STATE_NAME	= trim($arr_rs_order_state[$k]["ORDER_STATE_NAME"]);
				$ORDER_STATE_CNT	= trim($arr_rs_order_state[$k]["ORDER_STATE_CNT"]);
	?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$ORDER_STATE_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$ORDER_STATE_CNT?></td>
	</tr>

	<?
			}
		}
	?>
	</tbody>
</table>
<br/>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>클레임종류</td>
		<td align='center' bgcolor='#F4F1EF'>클레임사유</td>
		<td align='center' bgcolor='#F4F1EF'>총합</td>
	</tr>
	<?
		
		if (sizeof($arr_rs_claim_type) > 0) {
			for ($k = 0 ; $k < sizeof($arr_rs_claim_type); $k++) {

				$CLAIM_PCODE	= trim($arr_rs_claim_type[$k]["PCODE"]);
				$CLAIM_PCODE_NM	= trim($arr_rs_claim_type[$k]["PCODE_NM"]);
				$CLAIM_DCODE	= trim($arr_rs_claim_type[$k]["DCODE_NM"]);
				$CLAIM_CNT	= trim($arr_rs_claim_type[$k]["CNT"]);

	?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CLAIM_PCODE_NM?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CLAIM_DCODE?></td>
		<td bgColor='#FFFFFF' align='center'><?=number_format($CLAIM_CNT) ?></td>
	</tr>

	<?


			}
		}
	?>

</table>
<br/>
<TABLE border=1>
	<? if ($s_adm_cp_type == "운영") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>No.</td>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>클레임구분</td>
		<td align='center' bgcolor='#F4F1EF'>사유</td>
		<td align='center' bgcolor='#F4F1EF'>주문자</td>
		<td align='center' bgcolor='#F4F1EF'>수령자</td>
		<td align='center' bgcolor='#F4F1EF'>수령자주소</td>
		<td align='center' bgcolor='#F4F1EF'>연락처</td>
		<td align='center' bgcolor='#F4F1EF'>휴대전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>판매업체</td>
		<td align='center' bgcolor='#F4F1EF'>공급업체</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>클레임 내용</td>
		<td align='center' bgcolor='#F4F1EF'>처리상태</td>
		<td align='center' bgcolor='#F4F1EF'>등록일</td>
		<td align='center' bgcolor='#F4F1EF'>처리일</td>
	</tr>
	<? }?>

	<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>No.</td>
		<td align='center' bgcolor='#F4F1EF'>주문번호</td>
		<td align='center' bgcolor='#F4F1EF'>클레임구분</td>
		<td align='center' bgcolor='#F4F1EF'>사유</td>
		<td align='center' bgcolor='#F4F1EF'>주문자</td>
		<td align='center' bgcolor='#F4F1EF'>수령자</td>
		<td align='center' bgcolor='#F4F1EF'>수령자주소</td>
		<td align='center' bgcolor='#F4F1EF'>연락처</td>
		<td align='center' bgcolor='#F4F1EF'>휴대전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>공급업체</td>
		<td align='center' bgcolor='#F4F1EF'>상품명</td>
		<td align='center' bgcolor='#F4F1EF'>수량</td>
		<td align='center' bgcolor='#F4F1EF'>클레임 내용</td>
		<td align='center' bgcolor='#F4F1EF'>처리상태</td>
		<td align='center' bgcolor='#F4F1EF'>등록일</td>
		<td align='center' bgcolor='#F4F1EF'>처리일</td>
	</tr>
	<? } ?>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn							= trim($arr_rs[$j]["rn"]);
				$BB_NO					= trim($arr_rs[$j]["BB_NO"]);
				$BB_CODE				= trim($arr_rs[$j]["BB_CODE"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$WRITER_NM			= trim($arr_rs[$j]["WRITER_NM"]);
				$TITLE					= trim($arr_rs[$j]["TITLE"]);
				$HIT_CNT				= trim($arr_rs[$j]["HIT_CNT"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$CONFIRM_TF			= trim($arr_rs[$j]["REPLY_STATE"]);
				$REPLY_DATE			= trim($arr_rs[$j]["REPLY_DATE"]);
				$REF_IP					= trim($arr_rs[$j]["KEYWORD"]);
				$FILE_SIZE			= trim($arr_rs[$j]["FILE_SIZE"]);
				 
				$O_NAME					= trim($arr_rs[$j]["EMAIL"]);
				$R_NAME					= trim($arr_rs[$j]["HOMEPAGE"]);

				$R_ZIPCODE			= trim($arr_rs[$j]["R_ZIPCODE"]);
				$R_ADDR1				= trim($arr_rs[$j]["R_ADDR1"]);
				$R_PHONE				= trim($arr_rs[$j]["R_PHONE"]);
				$R_HPHONE				= trim($arr_rs[$j]["R_HPHONE"]);


				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));

				if ($CONFIRM_TF == "Y")
					$REPLY_DATE = date("Y-m-d",strtotime($REPLY_DATE));


				if ($CONFIRM_TF == "Y") {
					$STR_CONFIRM_TF = "<font color='navy'>처리완료</font>";
				} else {
					$STR_CONFIRM_TF = "<font color='red'>접수</font>";
				}
	
				if ($USE_TF == "Y") {
					$STR_USE_TF = "<font color='navy'>공개</font>";
				} else {
					$STR_USE_TF = "<font color='red'>비공개</font>";
				}

				$str_cp_nm = getSaleCompanyName($conn, $CATE_01);
	?>
	<? if ($s_adm_cp_type == "운영") { ?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?= $rn ?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CATE_01?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn,"ORDER_STATE",$CATE_04)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn,"CLAIM_TYPE",$CATE_02)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_NAME?></td>
		<td bgColor='#FFFFFF' align='left'>[<?=$R_ZIPCODE?>] <?=$R_ADDR1?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_PHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$str_cp_nm?></td>
		<td bgColor='#FFFFFF' align='left'><?=getCompanyName($conn,$REF_IP)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$TITLE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$FILE_SIZE?></td>
		<td bgColor='#FFFFFF' align='left'><?=get_text($CONTENTS)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$STR_CONFIRM_TF?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REG_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REPLY_DATE?></td>
	</tr>
	<? } ?>
	<? if ($s_adm_cp_type == "구매" || $s_adm_cp_type == "판매공급") { ?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?= $rn ?></td>
		<td bgColor='#FFFFFF' align='center'><?=$CATE_01?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn,"ORDER_STATE",$CATE_04)?></td>
		<td bgColor='#FFFFFF' align='center'><?=getDcodeName($conn,"CLAIM_TYPE",$CATE_02)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$O_NAME?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_NAME?></td>
		<td bgColor='#FFFFFF' align='left'>[<?=$R_ZIPCODE?>] <?=$R_ADDR1?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_PHONE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$R_HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=getCompanyName($conn,$REF_IP)?></td>
		<td bgColor='#FFFFFF' align='left'><?=$TITLE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$FILE_SIZE?></td>
		<td bgColor='#FFFFFF' align='left'><?=get_text($CONTENTS)?></td>
		<td bgColor='#FFFFFF' align='center'><?=$STR_CONFIRM_TF?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REG_DATE?></td>
		<td bgColor='#FFFFFF' align='center'><?=$REPLY_DATE?></td>
	</tr>
	<? } ?>
<? } ?>
<? } ?>

</table>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
