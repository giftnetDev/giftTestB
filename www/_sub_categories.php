<!-- ��ü ī�װ� -->
<?

	if($cate <> "") { 

		//echo $cate;
	
		//ī�޷α�
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
	<!-- ��ǰ�� �� ũ�� ���� 19�⵵ ��ݱ� -->
	<!-- ��Ÿ�� ����� �⺻ 14px -->
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
		//������ ī�װ� ��Ī�� �������� �ʰ�, �����ִ� ��Ī�� �����ϰ� ���� -s
		// $today = date("Ym");
		// if($today<"201908"){
			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
					case "��ü ��ǿ�ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ǿ�ǰ ��ƮA";		break;
					case "���� ��Ź��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ź��ǰ ��ƮB";		break;
					case "���� ��ȸ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ȸ��ǰ ��ƮB";		break;
					case "���� �ֹ�⹰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ�⹰ ��ƮB";		break;
					case "���� ��ǰ ��Ʈ"		 : $arr_rs_cate[$j]["CATE_NAME"] = "��ǰ ��ƮB";				break;
					case "���� ����ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "����ǰ ��ƮB";		break;
					case "���� ������Ʈ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ ��ƮB";		break;
					case "���� ȭ��ǰ ��Ʈ"  : $arr_rs_cate[$j]["CATE_NAME"] = "ȭ��ǰ ��ƮB";			break;
					case "���� ��Ȱ��ȭ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ȱ��ȭ ��ƮB";		break;
					case "���� �ֹ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ��ǰ ��ƮB";		break;
					case "���� ��ǿ�ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ǿ�ǰ ��ƮB";		break;
					case "��ü �ֹ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ��ǰ ��ƮA";		break;
					case "��ü ��Ź��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ź��ǰ ��ƮA";		break;
					case "��ü ��ȸ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ȸ��ǰ ��ƮA";		break;
					case "��ü �ֹ�⹰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ�⹰ ��ƮA";		break;
					case "��ü ��ǰ ��Ʈ"		 : $arr_rs_cate[$j]["CATE_NAME"] = "��ǰ ��ƮA";				break;
					case "��ü ����ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "����ǰ ��ƮA";		break;
					case "��ü ������Ʈ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ ��ƮA";		break;
					case "��ü ȭ��ǰ ��Ʈ"	 : $arr_rs_cate[$j]["CATE_NAME"] = "ȭ��ǰ ��ƮA";			break;
					case "��ü ��Ȱ��ȭ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ȱ��ȭ ��ƮA";		break;
					case "������Ʈ"					 : $arr_rs_cate[$j]["CATE_NAME"] = "Ȱ������Ʈ(����)";	break;
					case "�йи���Ʈ"				 : $arr_rs_cate[$j]["CATE_NAME"] = "Ȱ������Ʈ(����)";
				}
			}
			
			function querySort ($x, $y) {
				return strcasecmp($x['CATE_NAME'], $y['CATE_NAME']);
			}
			usort($arr_rs_cate, 'querySort');
			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
					case "Ȱ������Ʈ(����)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ(����)";		break;
					case "Ȱ������Ʈ(����)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ(����)";
				}
			}
		// }
		//������ ī�װ� ��Ī�� �������� �ʰ�, �����ִ� ��Ī�� �����ϰ� ���� -e

		
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
<!-- // ��ü ī�װ� -->