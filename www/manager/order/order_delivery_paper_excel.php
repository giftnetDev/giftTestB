<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD018"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";

	$file_name="송장완료등록용리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	
	$arr_rs = listTempOrderGoodsDeliveryReturn_Interpark($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>

<body>

<TABLE border=1>
	<!--
	<tr>
		<th>쇼핑몰코드</th><th>주문번호</th><th>상품코드</th><th>판매자상품코드</th><th>주문자ID</th><th>주문자</th><th>주문자전화번호</th><th>주문자핸드폰</th><th>수령인</th><th>전화번호</th><th>핸드폰</th><th>결제일</th><th>주문일</th><th>주문상태</th><th>카테고리명</th><th>상품명</th><th>옵션</th><th>수량</th><th>판매가격</th><th>옵션가격</th><th>총판매가격</th><th>배송비</th><th>면과세</th><th>주소</th><th>주문시요구사항</th><th>회원등급별할인금액합계</th><th>쿠폰할인금액합계</th><th>결제방식</th><th>사용포인트</th><th>일반결재금액</th><th>회원그룹</th><th>송장등록일</th><th>취소완료일</th><th>반품완료일</th><th>고객사</th><th>택배사</th><th class="end">송장번호</th>
	</tr>
	-->
				<?
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							$A	= SetStringFromDB($arr_rs[$j]["A"]);
							$B	= SetStringFromDB($arr_rs[$j]["B"]);
							$C	= SetStringFromDB($arr_rs[$j]["C"]);
							$D	= SetStringFromDB($arr_rs[$j]["D"]);
							$E	= SetStringFromDB($arr_rs[$j]["E"]);
							$F	= SetStringFromDB($arr_rs[$j]["F"]);
							$G	= SetStringFromDB($arr_rs[$j]["G"]);
							$H	= SetStringFromDB($arr_rs[$j]["H"]);
							$I	= SetStringFromDB($arr_rs[$j]["I"]);
							$J	= SetStringFromDB($arr_rs[$j]["J"]);
							$K	= SetStringFromDB($arr_rs[$j]["K"]);
							$L	= SetStringFromDB($arr_rs[$j]["L"]);
							$M	= SetStringFromDB($arr_rs[$j]["M"]);
							$N	= SetStringFromDB($arr_rs[$j]["N"]);
							$O	= SetStringFromDB($arr_rs[$j]["O"]);
							$P	= SetStringFromDB($arr_rs[$j]["P"]);
							$Q	= SetStringFromDB($arr_rs[$j]["Q"]);
							$R	= SetStringFromDB($arr_rs[$j]["R"]);
							$S	= SetStringFromDB($arr_rs[$j]["S"]);
							$T	= SetStringFromDB($arr_rs[$j]["T"]);
							$U	= SetStringFromDB($arr_rs[$j]["U"]);
							$V	= SetStringFromDB($arr_rs[$j]["V"]);
							$W	= SetStringFromDB($arr_rs[$j]["W"]);
							$X	= SetStringFromDB($arr_rs[$j]["X"]);
							$Y	= SetStringFromDB($arr_rs[$j]["Y"]);
							$Z	= SetStringFromDB($arr_rs[$j]["Z"]);
							$AA	= SetStringFromDB($arr_rs[$j]["AA"]);
							$AB	= SetStringFromDB($arr_rs[$j]["AB"]);
							$AC	= SetStringFromDB($arr_rs[$j]["AC"]);
							$AD	= SetStringFromDB($arr_rs[$j]["AD"]);
							$AE	= SetStringFromDB($arr_rs[$j]["AE"]);
							$AF	= SetStringFromDB($arr_rs[$j]["AF"]);
							$AG	= SetStringFromDB($arr_rs[$j]["AG"]);
							$AH	= SetStringFromDB($arr_rs[$j]["AH"]);
							$AI	= SetStringFromDB($arr_rs[$j]["AI"]);
							$AJ	= SetStringFromDB($arr_rs[$j]["AJ"]);
							$AK	= SetStringFromDB($arr_rs[$j]["AK"]);
						?>
					<tr>
						<td><?=$A?></td>
						<td><?=$B?></td>
						<td><?=$C?></td>
						<td><?=$D?></td>
						<td><?=$E?></td>
						<td><?=$F?></td>
						<td><?=$G?></td>
						<td><?=$H?></td>
						<td><?=$I?></td>
						<td><?=$J?></td>
						<td><?=$K?></td>
						<td><?=$L?></td>
						<td><?=$M?></td>
						<td><?=$N?></td>
						<td><?=$O?></td>
						<td><?=$P?></td>
						<td><?=$Q?></td>
						<td><?=$R?></td>
						<td><?=$S?></td>
						<td><?=$T?></td>
						<td><?=$U?></td>
						<td><?=$V?></td>
						<td><?=$W?></td>
						<td><?=$X?></td>
						<td><?=$Y?></td>
						<td><?=$Z?></td>
						<td><?=$AA?></td>
						<td><?=$AB?></td>
						<td><?=$AC?></td>
						<td><?=$AD?></td>
						<td><?=$AE?></td>
						<td><?=$AF?></td>
						<td><?=$AG?></td>
						<td><?=$AH?></td>
						<td><?=$AI?></td>
						<td><?=$AJ?></td>
						<td><?=$AK?></td>
					</tr>

						<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="37">데이터가 없습니다. </td>
						</tr>
					<?
						}
					?>
</table>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>