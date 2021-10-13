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
	$menu_right = "GD010"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/email/email.php";

	

#====================================================================
# Request Parameter
#====================================================================
	// �ϰ� �߼�
	if($mode == "SEND_GROUP_EMAIL") {
		include('../../_PHPMailer/class.phpmailer.php');
		$row_cnt = count($chk_no);
		
		$succes_cnt = 0;
		$fail_cnt = 0;
		$fail_to_names = "";
		
		for ($k = 0; $k < $row_cnt; $k++) {
			$idx = $chk_no[$k];
			$str_cp_no 		= $chk_cp_no[$idx];
			$str_to_email	= $chk_email_to[$idx];
			$str_to_name	= $chk_to_name[$idx];
			$download_url 	= "https://".$_SERVER['HTTP_HOST']."/manager/goods/goods_buy_company_excel.php?cp_no=".$str_cp_no."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
			$path 			= $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
			$filename 		= "����Ʈ��_��ǰȮ�ο�û��(".$str_cp_no.").xls";
			$file 			= $path . "/" . $filename;

			downloadFile($download_url, $file);
			
			if($str_cp_no <> "" && $from_email <> "" && $str_to_email <> "" && $file <> "") {
				$name_from = "����Ʈ��";
				mailer($name_from, $from_email, $str_to_name, $str_to_email, $email_title, $email_body, $path, $filename);
	
				//���Ϲ߼ۻ�Ȳ ������Ʈ
				$page_from = $_SERVER[PHP_SELF];
				insertEmail($conn, "��ǰȮ�ο�û��", $str_cp_no, $name_from, $from_email, $str_to_name, $str_to_email, $email_title, $email_body, $download_url, $s_adm_no);
			
				$strParam = $strParam."nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
				// echo "<br>$strParam";

				//������ ����+1
				$succes_cnt++;
			} else {
				//������ ����+1
				$fail_cnt++;
				//������ ȸ��� ����
				if($fail_cnt == 1){
					$fail_to_names .= "���г��� : $str_to_name";
				} else {
					$fail_to_names .= ", ".$str_to_name;
				}
			}
		}//for
		?>
		<script language="javascript">
			// ó�����
			alert('�� <?=$row_cnt?>�� ó��, ���� <?=$succes_cnt?>��, ���� <?=$fail_cnt?>��\n<?=$fail_to_names?>');
			document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
		</script>
		<?
	}//if mode : SEND_GROUP_EMAIL

	if($mode == "SEND_EMAIL") {

		//echo $from_email." ".$to_email." ".$to_name." ".$email_title." ".$email_body." <br/>";

		$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/goods/goods_buy_company_excel.php?cp_no=".base64url_encode($cp_no)."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "����Ʈ��_��ǰȮ�ο�û��(".$cp_no.").xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		if($cp_no <> "" && $from_email <> "" && $to_email <> "" && $file <> "") {
		
			include('../../_PHPMailer/class.phpmailer.php');

			$name_from = "����Ʈ��";

			echo "name_from : $name_from, from_email : $from_email, to_name : $to_name, to_email : $to_email, email_title : $email_title, email_body : $email_body, path : $path, filename : $filename<br>";
			exit;
			mailer($name_from, $from_email, $to_name, $to_email, $email_title, $email_body, $path, $filename);

			//���Ϲ߼ۻ�Ȳ ������Ʈ
			$page_from = $_SERVER[PHP_SELF];
			insertEmail($conn, "��ǰȮ�ο�û��", $cp_no, $name_from, $from_email, $to_name, $to_email, $email_title, $email_body, $download_url, $s_adm_no);

		
			$strParam = $strParam."nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
		
			
	?>
	<script language="javascript">
		alert('���� ó�� �Ǿ����ϴ�x');
		document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
	</script>
	<?
		} else {
	?>
	<script language="javascript">
			alert('�����Դϴ� �̸��� �ּҰ� �ִ��� Ȯ�κ�Ź�帳�ϴ�.');
	</script>
	<?
		}

	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);

	
	if($type_a == "" && $type_b == "" && $type_c == "") { 
		$type_a = "Y";
		$type_b = "Y";
		$type_c = "Y";

	}

#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================

	$nListCnt =totalCntBuyCompany($conn, $arr_options, $search_field, $search_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listBuyCompany($conn, $arr_options, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" >
	function js_link_to_goods_list(cp_no, cate_04) {

		window.open("/manager/goods/goods_list.php?con_cate_03=" + cp_no + "&con_cate_04=" + cate_04,'_blank');
	}

	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;

		frm.target = "";
		frm.nPage.value = 1;
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel(cp_no) { 
		var frm = document.frm;

		var type_a = document.getElementById('type_a');
		var type_a_val = "";
		if(type_a.checked)
			type_a_val = type_a.value;

		var type_b = document.getElementById('type_b');
		var type_b_val = "";
		if(type_b.checked)
			type_b_val = type_b.value;

		var type_c = document.getElementById('type_c');
		var type_c_val = "";
		if(type_c.checked)
			type_c_val = type_c.value;


		NewDownloadWindow("goods_buy_company_excel.php", {con_cate_03 : cp_no, type_a : type_a_val, type_b : type_b_val, type_c : type_c_val});
	}

	function js_send_email(cp_no) { 
		var frm = document.frm;
		
		frm.mode.value = "SEND_EMAIL";
		frm.cp_no.value = cp_no;
		frm.to_email.value = $("input[name=email_to_"+cp_no+"]").val();
		frm.to_name.value = $("input[name=to_name_"+cp_no+"]").val();
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_send_group_email() { 
		var frm = document.frm;
		
		frm.mode.value = "SEND_GROUP_EMAIL";
		frm.target = "";
		frm.method = "post";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}
</script>
</head>

<body id="admin">

<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="">
<input type="hidden" name="to_email" value="">
<input type="hidden" name="to_name" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">
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

				<h2>���޾�ü ��ǰ����</h2>
				<?
					
					$FROM_EMAIL = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'FROM_EMAIL');
					$EMAIL_TITLE = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'EMAIL_TITLE');
					$EMAIL_BODY = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'EMAIL_BODY');
					// $EMAIL_BODY = "�ȳ��ϼ���. (��)����Ʈ�� ���ξ��Դϴ�.[����][����]2019�� ��ݱ� ī�ٷα� ���� �۾����� �����帳�ϴ�.[����]��翡 ��ϵ� �ͻ��� ��ǰ ���� ���θ� ÷�ε� ���Ͽ� �ۼ��Ͽ� ȸ���� �ֽð�,[����]���� �����Ͻ� ��ǰ�� �����ø� ���ȼ��� �Բ� ȸ�� ��Ź�帳�ϴ�.[����]�׸��� ��ǰ�� �� ����/�԰�/�ܰ� ��� �´��� �� Ȯ���� �ֽð� ������� �����ؼ� �����ּ���.[����]ī�ٷα״� 2�� ��~3�� �� �����̰�, 7��~8������ ����մϴ�. [����]�׶����� ����ϴµ� ������������ Ȯ�����ֽð� �� ȸ�� ��Ź�帮�ڽ��ϴ�.[����]��ǰ�� ��ǰ ���� �� ���԰� ������ ���� ������ �ֽð�,[����]���԰� ��ȹ�� ���� ��� �������� ���� ��Ź�帳�ϴ�.��[����][����]�����մϴ� :)[����][����]���ǻ��� : (�����ȣ) 070-4269-8150 ���ξ�";
					$EMAIL_BODY = "�ȳ��ϼ���. (��)����Ʈ�� �Դϴ�.

2019�� �Ϲݱ� īŻ�α� ���� �۾����� �����帳�ϴ�.
��翡 ��ϵ� �ͻ��� ��ǰ ���� ���θ� ÷�ε� ���Ͽ� �ۼ��Ͽ� ȸ���� �ֽð�,
���� �����Ͻ� ��ǰ�� �����ø� ���ȼ��� �Բ� ȸ�� ��Ź�帳�ϴ�.
���� �ֽ� �� ��ǰ�� �� ����/�԰�/�ܰ� ��� �´��� �� Ȯ���� �ֽð� ������� �����ؼ� �����ּ���.
�� ǰ�� ��ǰ ���� �� ���԰� ������ ���� ������ �ֽð�,
���԰� ��ȹ�� ���� ��� �������� ���� ��Ź�帳�ϴ�. ��


7�� 29�ϱ��� �� ȸ�� ��Ź�帳�ϴ�. �����մϴ� :)

���ǻ��� : (�����ȣ) 070-4269-8150 ���ξ�";
					$EMAIL_BODY = str_replace("[����]", "\r\n", $EMAIL_BODY);
				?>
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="10%" />
						<col width="*" />
					</colgroup>
						<tr>
							<th>�߼� �̸���</th>
							<td>
								<input type="text" name="from_email" value="<?=$FROM_EMAIL?>"/>
							</td>
						</tr>
						<tr>
							<th>��������</th>
							<td>
								<input type="text" name="email_title" value="<?=$EMAIL_TITLE?>" style="width:85%;"/>
							</td>
						</tr>
						<tr>
							<th>���ϳ���</th>
							<td>
								<textarea style="width:85%; height:160px;" name="email_body"><?= $EMAIL_BODY ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="sp10"></div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="CP_NM" <? if ($order_field == "CP_NM") echo "selected"; ?> >��ü��</option>
								<option value="GOODS_CNT" <? if ($order_field == "GOODS_CNT") echo "selected"; ?> >��ǰ��</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?>> �������� &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?> > �������� 
						</td>
						<th>�˻�����</th>
						<td>
							<!--<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300����</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500����</option>
							</select>&nbsp;
							-->
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >��ü�ڵ�</option>
								<option value="CP_NAME" <? if ($search_field == "CP_NAME") echo "selected"; ?> >��ü�� </option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="15" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="sp10"></div>
	
				�� <?=number_format($nListCnt)?> ��
				<div style="display:inline-block; width: 91.8%; text-align: right; margin: 0 0 10px 0;">
					<input type="button" value="���ù߼�" onclick="js_send_group_email()">
				</div>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<colgroup>
						<col width="2%" />
						<col width="7%" />
						<col width="*"/>
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="7%" />
						<col width="20%" />
						<col width="4%" />
						<col width="17%" />
					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="all_chk" onClick="js_all_check();"></th>
							<th>��ü�ڵ�</th>
							<th>���޾�ü��</th>
							<th>�Ǹ���<input type="checkbox" id="type_a" name="type_a" <?if($type_a == "Y") echo "checked";?> value="Y"/></th>
							<th>ǰ��<input type="checkbox" id="type_b" name="type_b" <?if($type_b == "Y") echo "checked";?> value="Y"/></th>
							<th>����<input type="checkbox" id="type_c" name="type_c" <?if($type_c == "Y") echo "checked";?> value="Y"/></th>
							<th>����Ȯ��</th>
							<th>�̸���</th>
							<th>�߼�</th>
							<th class="end">���� �߼���</th>
						</tr>
					</thead>
					<tbody>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {

						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//C.CP_NO, C.CP_CODE, C.CP_NM, C.CP_NM2, C.EMAIL
							$RN					= trim($arr_rs[$j]["RN"]);
							$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
							$CP_CODE				= trim($arr_rs[$j]["CP_CODE"]);
							$CP_NM					= SetStringFromDB($arr_rs[$j]["CP_NM"]);
							$CP_NM2					= SetStringFromDB($arr_rs[$j]["CP_NM2"]);
							$EMAIL					= trim($arr_rs[$j]["EMAIL"]);
							
				
				?>
						<tr>
							<td>
								<input type="checkbox" name="chk_no[]" class="chk" value="<?=$j?>">
								<input type="hidden" name="chk_cp_no[]" value="<?=$CP_NO?>">
							</td>
							<td>
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '')"><?=$RN?></a>
							</td>
							<td class="modeual_nm">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '')"><?= $CP_NM ?> <?= $CP_NM2 ?></a>
								<input type="hidden" name="to_name_<?=$CP_NO?>" value="<?= $CP_NM ?> <?= $CP_NM2 ?>"/>
								<!-- ���ù߼� �ʿ� ������ : 1. ��ü�� -->
								<input type="hidden" name="chk_to_name[]" value="<?= $CP_NM ?> <?= $CP_NM2 ?>"/>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '�Ǹ���')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "�Ǹ���")) ?>
								</a>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', 'ǰ��')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "ǰ��")) ?>
								</a>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '����')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "����")) ?>
								</a>
							</td>
							<td>
								<a href="javascript:js_excel('<?=$CP_NO?>');"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
							</td>
							<td>
								<input type="text"  name="email_to_<?=$CP_NO?>" value="<?=$EMAIL?>"/>
								<!-- ���ù߼� �ʿ� ������ : 2. �޴� �̸��� -->
								<input type="hidden"  name="chk_email_to[]" value="<?=$EMAIL?>"/>
							</td>
							<td>
								<input type="button" name="btn_send" value="�߼�" onclick="javascript:js_send_email('<?=$CP_NO?>');"/>
							</td>
							<td>
								<? 
									$arr_email = getEmailDate($conn, '��ǰȮ�ο�û��', $CP_NO); 
									if(sizeof($arr_email) > 0) {
										$reg_adm  = $arr_email[0]["REG_ADM"];
										$reg_date = $arr_email[0]["REG_DATE"];
										$reg_date = date("Y-m-d H:i",strtotime($reg_date));

										echo getAdminName($conn, $reg_adm);
										echo " ".$reg_date;
									} 
								
								?>
							</td>
						</tr>
						
				<?			
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="10">�����Ͱ� �����ϴ�. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
								
					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
				
				
			<!-- // E: mwidthwrap -->

		</td>
	</tr>
	</table>
	
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>