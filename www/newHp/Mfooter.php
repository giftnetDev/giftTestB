
	<div style="border-top: 1px solid #f4f4f4;">
		<div class="company_info">
			<b>������</b><br>
			<br><b>TEL : 031-527-6812</b><br>
			���� 10�� ~ 17��<br>
			(���� 12�� ~ 1��)<br>
			FAX : 031-527-6858
		</div>
		<div class="company_info">
			<b>�Աݰ���</b><br><br>
			��������<br>
			<b>351-1137-6964-13</b><br>
			������ ����Ʈ��
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
			(��)����Ʈ�� | ��⵵ �����ֽ� ������ ��縮 98���� (��)����Ʈ��<br>
			��ǥ�̻� ������ �� ����ڵ�Ϲ�ȣ 132-81-58846 �� ����Ǹž���Ű� 2005-��Ⳳ����-0238<br><br>
			�� �������� ��ǰ�̹��� ���۱��� (��)����Ʈ�ݿ� �ֽ��ϴ�.<br>
			��ǰ���� �� �̹����� ���ܺ����� ���մϴ�.<br><br>
			<div class="email">���� : gift@giftnet.co.kr</div><br>
			<div class="copy">Copyright ? 2016 giftnet ALL Rights Reserved.</div>
		</footer>
	</div>