
	<div style="border-top: 1px solid #f4f4f4;">
		<div class="company_info">
			<b>고객센터</b><br>
			<br><b>TEL : 031-527-6812</b><br>
			평일 10시 ~ 17시<br>
			(점심 12시 ~ 1시)<br>
			FAX : 031-527-6858
		</div>
		<div class="company_info">
			<b>입금계좌</b><br><br>
			농협은행<br>
			<b>351-1137-6964-13</b><br>
			예금주 기프트넷
		</div>
		<footer>
		<? 
			$arr_hp_f_c2 = listDcode($conn, "HOME_FOOTER2_CONTENT", 'Y', 'N', '', '', 1, 100);
			for($q = 0; $q < sizeof($arr_hp_f_c2); $q++) 
			{ 
				$footer_id = $arr_hp_f_c2[$q]["DCODE"];
				//$footer_name = $arr_hp_f_c2[$q]["DCODE_NM"];
				$footer_name = str_replace(' ' , '', $arr_hp_f_c2[$q]["DCODE_NM"]);
				$footer_url = $arr_hp_f_c2[$q]["DCODE_EXT"];				
				
				if($footer_url <> "") 
				{ ?>
					<a href="<?=$footer_url?>"><?=$footer_name?></a>
		<?		} 
				else 
				{ ?>
					<a href="Mcontent_info.php?no=<?=$footer_id?>"><font color="white" size="2"><?=$footer_name?></font>&nbsp;</a>
		<?		}
			}
		
		?>	<br><br>
			(주)기프트넷 | 경기도 남양주시 진건읍 배양리 98번지 (주)기프트넷<br>
			대표이사 양진현 ㅣ 사업자등록번호 132-81-58846 ㅣ 통신판매업고신고 2005-경기남양주-0238<br><br>
			본 페이지와 상품이미지 저작권은 (주)기프트넷에 있습니다.<br>
			상품내용 및 이미지의 무단복제를 금합니다.<br><br>
			<div class="email">문의 : gift@giftnet.co.kr</div><br>
			<div class="copy">Copyright ? 2016 giftnet ALL Rights Reserved.</div>
		</footer>
	</div>