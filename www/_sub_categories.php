<!-- 전체 카테고리 -->
<?

	if($cate <> "") { 

		//echo $cate;
	
		//카달로그
		if(startsWith($cate, '20'))
			$cate_length = 4;
		//else
		//	$cate_length = 6;
		
		if(strlen($cate) > $cate_length)
			$arr_rs_cate = listSubCategory($conn, substr($cate, 0, $cate_length), substr($cate, 0, $cate_length));
		else
			$arr_rs_cate = listSubCategory($conn, $cate, $cate);

		if(sizeof($arr_rs_cate) > 0 && $cate != "") { 
?>
<div class="container hidden-xs" id="cate-list">
	<!-- 상품명 길어서 크기 조정 19년도 상반기 -->
	<!-- 스타일 지우면 기본 14px -->
		<ul style="font-size: 12px;">
    <!-- <ul"> -->

		<?

			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				//category_NO, category_CD, category_NAME, category_URL, category_FLAG, category_SEQ01, category_SEQ02, category_SEQ03, category_RIGHT
				
				$CATE_NO				= trim($arr_rs_cate[$j]["CATE_NO"]);
				$CATE_CD				= trim($arr_rs_cate[$j]["CATE_CD"]);
				$CATE_NAME			= trim($arr_rs_cate[$j]["CATE_NAME"]);
				$CATE_MEMO			= trim($arr_rs_cate[$j]["CATE_MEMO"]);
				$CATE_CODE			= trim($arr_rs_cate[$j]["CATE_CODE"]);

		?>
		
    	<li><a href="/sub.php?cate=<?=$CATE_CD?>" <?if($cate == $CATE_CD) echo 'class="active"'; ?> ><?=$CATE_NAME?><span class="desc"><?=$CATE_MEMO?></span></a></li>
		<?	 }	?>
    </ul>
</div>
<? } ?>

<?
	} else { 

	if($code_cate <> "") { 

		$arr_rs_cate = listSubMenusByCodeCate($conn, $code_cate, $arr_options);

		if(sizeof($arr_rs_cate) > 0) { 
?>
<div class="container hidden-xs" id="cate-list">
    <ul>
		<?
		//전산의 카테고리 명칭을 변경하지 않고, 보여주는 명칭만 변경하고 정렬 -s
		// $today = date("Ym");
		// if($today<"201908"){
			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
					case "자체 욕실용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "욕실용품 세트A";		break;
					case "공급 세탁용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "세탁용품 세트B";		break;
					case "공급 일회용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "일회용품 세트B";		break;
					case "공급 주방기물 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방기물 세트B";		break;
					case "공급 식품 세트"		 : $arr_rs_cate[$j]["CATE_NAME"] = "식품 세트B";				break;
					case "공급 등산용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "등산용품 세트B";		break;
					case "공급 지갑벨트 세트": $arr_rs_cate[$j]["CATE_NAME"] = "지갑벨트 세트B";		break;
					case "공급 화장품 세트"  : $arr_rs_cate[$j]["CATE_NAME"] = "화장품 세트B";			break;
					case "공급 생활잡화 세트": $arr_rs_cate[$j]["CATE_NAME"] = "생활잡화 세트B";		break;
					case "공급 주방용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방용품 세트B";		break;
					case "공급 욕실용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "욕실용품 세트B";		break;
					case "자체 주방용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방용품 세트A";		break;
					case "자체 세탁용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "세탁용품 세트A";		break;
					case "자체 일회용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "일회용품 세트A";		break;
					case "자체 주방기물 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방기물 세트A";		break;
					case "자체 식품 세트"		 : $arr_rs_cate[$j]["CATE_NAME"] = "식품 세트A";				break;
					case "자체 등산용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "등산용품 세트A";		break;
					case "자체 지갑벨트 세트": $arr_rs_cate[$j]["CATE_NAME"] = "지갑벨트 세트A";		break;
					case "자체 화장품 세트"	 : $arr_rs_cate[$j]["CATE_NAME"] = "화장품 세트A";			break;
					case "자체 생활잡화 세트": $arr_rs_cate[$j]["CATE_NAME"] = "생활잡화 세트A";		break;
					case "선물세트"					 : $arr_rs_cate[$j]["CATE_NAME"] = "활선물세트(명절)";	break;
					case "패밀리세트"				 : $arr_rs_cate[$j]["CATE_NAME"] = "활선물세트(감사)";
				}
			}
			
			function querySort ($x, $y) {
				return strcasecmp($x['CATE_NAME'], $y['CATE_NAME']);
			}
			usort($arr_rs_cate, 'querySort');
			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
					case "활선물세트(명절)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "선물세트(명절)";		break;
					case "활선물세트(감사)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "선물세트(감사)";
				}
			}
		// }
		//전산의 카테고리 명칭을 변경하지 않고, 보여주는 명칭만 변경하고 정렬 -e

		
			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				//category_NO, category_CD, category_NAME, category_URL, category_FLAG, category_SEQ01, category_SEQ02, category_SEQ03, category_RIGHT
				
				$CATE_NAME			= trim($arr_rs_cate[$j]["CATE_NAME"]);
				$CATE_CODE			= trim($arr_rs_cate[$j]["CATE_CODE"]);
		?>

    	<li><a href="/sub.php?code_cate=<?=$CATE_CODE?>" <?if($code_cate == $CATE_CODE) echo 'class="active"'; ?> ><?=$CATE_NAME?><!-- <span class="desc"><?=$CATE_MEMO?></span>--></a></li>
		<?	 }	?>
    </ul>
</div>
<? } } ?>
<?
	}
?>
<!-- // 전체 카테고리 -->