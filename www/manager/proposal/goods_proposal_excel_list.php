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
	$menu_right = "GD007"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/proposal/proposal.php";


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
	
	$del_tf = "N";

#====================================================================
# DML Process
#====================================================================

	$file_name="제안 리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

	$arr_rs = listGoodsProposal($conn, $start_date, $end_date, $cp_type, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>

<font size=3><b>제안 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<th align='center' bgcolor='#F4F1EF'>전표</th>
		<th align='center' bgcolor='#F4F1EF'>제안업체</th>
		<th align='center' bgcolor='#F4F1EF'>등록일</th>
		<th align='center' bgcolor='#F4F1EF'>발송일</th>
		<th align='center' bgcolor='#F4F1EF'>제품명</th>
		<th align='center' bgcolor='#F4F1EF'>기프트넷단가</th>
		<th align='center' bgcolor='#F4F1EF'>제안가</th>
		<th align='center' bgcolor='#F4F1EF'>최종수정일</th>
		<th align='center' bgcolor='#F4F1EF'>취소일/취소여부</th>
	</tr>
	
	<?
		
		if (sizeof($arr_rs) > 0) {
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

				$rn							= trim($arr_rs[$j]["rn"]);
				$GP_NO						= trim($arr_rs[$j]["GP_NO"]);
				$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
				$CP_NO						= trim($arr_rs[$j]["CP_NO"]);
				$CP_NM			= getCompanyNameWithNoCode($conn, $CP_NO);
				$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
				$IS_SENT					= trim($arr_rs[$j]["IS_SENT"]);
				$SENT_DATE					= trim($arr_rs[$j]["SENT_DATE"]);

				if($SENT_DATE == "0000-00-00 00:00:00")
					$SENT_DATE = "";
				else
					$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

				$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));
	
	?>

	<?
				$arr_rs_goods = listGoodsProposalGoods($conn, $GP_NO, '');
				if (sizeof($arr_rs_goods) > 0) {
					
					for ($k = 0 ; $k < sizeof($arr_rs_goods); $k++) {

						$GPG_NO						= trim($arr_rs_goods[$k]["GPG_NO"]);
						$GOODS_CODE					= SetStringFromDB($arr_rs_goods[$k]["GOODS_CODE"]);
						$GOODS_NAME					= SetStringFromDB($arr_rs_goods[$k]["GOODS_NAME"]);
						$RETAIL_PRICE				= trim($arr_rs_goods[$k]["RETAIL_PRICE"]);
						$DELIVERY_CNT_IN_BOX		= trim($arr_rs_goods[$k]["DELIVERY_CNT_IN_BOX"]);
						$COMPONENT					= trim($arr_rs_goods[$k]["COMPONENT"]);
						$PROPOSAL_PRICE				= trim($arr_rs_goods[$k]["PROPOSAL_PRICE"]);

						$UP_DATE					= trim($arr_rs_goods[$k]["UP_DATE"]);
						$UP_ADM						= trim($arr_rs_goods[$k]["UP_ADM"]);
						
						$CANCEL_TF					= trim($arr_rs_goods[$k]["CANCEL_TF"]);
						$CANCEL_DATE				= trim($arr_rs_goods[$k]["CANCEL_DATE"]);
						$CANCEL_ADM					= trim($arr_rs_goods[$k]["CANCEL_ADM"]);

						if($UP_DATE != "0000-00-00 00:00:00")
							$UP_DATE = date("Y-m-d H:i",strtotime($UP_DATE));
						else
							$UP_DATE = "";
					
						if($CANCEL_DATE != "0000-00-00 00:00:00")
							$CANCEL_DATE = date("Y-m-d H:i",strtotime($CANCEL_DATE));
						else
							$CANCEL_DATE = "";


	?>

			<tr height="30">
				<td bgColor='#FFFFFF' align='left'><?=$GROUP_NO?></td>
				<td bgColor='#FFFFFF' align='left'><?=$CP_NM?></td>
				<td bgColor='#FFFFFF' align='left'><?=$REG_DATE?> </td>
				<td bgColor='#FFFFFF' align='left'><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>발송전</font>"?> </td>
				<td bgColor='#FFFFFF' align='left'>[<?= $GOODS_CODE ?>] <?= $GOODS_NAME ?></td>
				<td bgColor='#FFFFFF' align='left'><?= number_format($RETAIL_PRICE)?></td>
				<td bgColor='#FFFFFF' align='left'><b><?= number_format($PROPOSAL_PRICE)?></b></td>
				<td bgColor='#FFFFFF' align='left'><?=$UP_DATE?></td>
				<td bgColor='#FFFFFF' align='left'>
					<? if($CANCEL_TF == "Y") {?>
						<font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">취소됨</font>
					<? } ?>
				</td>
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
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>