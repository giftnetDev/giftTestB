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
	$menu_right = "GD010"; // 메뉴마다 셋팅 해 주어야 합니다

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
	// 일괄 발송
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
			$filename 		= "기프트넷_상품확인요청서(".$str_cp_no.").xls";
			$file 			= $path . "/" . $filename;

			downloadFile($download_url, $file);
			
			if($str_cp_no <> "" && $from_email <> "" && $str_to_email <> "" && $file <> "") {
				$name_from = "기프트넷";
				mailer($name_from, $from_email, $str_to_name, $str_to_email, $email_title, $email_body, $path, $filename);
	
				//메일발송상황 업데이트
				$page_from = $_SERVER[PHP_SELF];
				insertEmail($conn, "상품확인요청서", $str_cp_no, $name_from, $from_email, $str_to_name, $str_to_email, $email_title, $email_body, $download_url, $s_adm_no);
			
				$strParam = $strParam."nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
				// echo "<br>$strParam";

				//결과기록 성공+1
				$succes_cnt++;
			} else {
				//결과기록 실패+1
				$fail_cnt++;
				//실패한 회사명 누적
				if($fail_cnt == 1){
					$fail_to_names .= "실패내역 : $str_to_name";
				} else {
					$fail_to_names .= ", ".$str_to_name;
				}
			}
		}//for
		?>
		<script language="javascript">
			// 처리결과
			alert('총 <?=$row_cnt?>건 처리, 성공 <?=$succes_cnt?>건, 실패 <?=$fail_cnt?>건\n<?=$fail_to_names?>');
			document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
		</script>
		<?
	}//if mode : SEND_GROUP_EMAIL

	if($mode == "SEND_EMAIL") {

		//echo $from_email." ".$to_email." ".$to_name." ".$email_title." ".$email_body." <br/>";

		$download_url = "https://".$_SERVER['HTTP_HOST']."/manager/goods/goods_buy_company_excel.php?cp_no=".base64url_encode($cp_no)."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
		$path = $_SERVER["DOCUMENT_ROOT"]."/upload_data/temp_mail";
		$filename = "기프트넷_상품확인요청서(".$cp_no.").xls";
		$file = $path . "/" . $filename;
		
		downloadFile($download_url, $file);

		if($cp_no <> "" && $from_email <> "" && $to_email <> "" && $file <> "") {
		
			include('../../_PHPMailer/class.phpmailer.php');

			$name_from = "기프트넷";

			echo "name_from : $name_from, from_email : $from_email, to_name : $to_name, to_email : $to_email, email_title : $email_title, email_body : $email_body, path : $path, filename : $filename<br>";
			exit;
			mailer($name_from, $from_email, $to_name, $to_email, $email_title, $email_body, $path, $filename);

			//메일발송상황 업데이트
			$page_from = $_SERVER[PHP_SELF];
			insertEmail($conn, "상품확인요청서", $cp_no, $name_from, $from_email, $to_name, $to_email, $email_title, $email_body, $download_url, $s_adm_no);

		
			$strParam = $strParam."nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str."&type_a=".$type_a."&type_b=".$type_b."&type_c=".$type_c;
		
			
	?>
	<script language="javascript">
		alert('정상 처리 되었습니다x');
		document.location.href = "<?=$_SERVER[PHP_SELF]?>?<?=$strParam?>";
	</script>
	<?
		} else {
	?>
	<script language="javascript">
			alert('에러입니다 이메일 주소가 있는지 확인부탁드립니다.');
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

	// 조회 버튼 클릭 시 
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

				<h2>공급업체 상품관리</h2>
				<?
					
					$FROM_EMAIL = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'FROM_EMAIL');
					$EMAIL_TITLE = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'EMAIL_TITLE');
					$EMAIL_BODY = getDcodeExtByCode($conn, 'BUY_COMPANY_EMAIL', 'EMAIL_BODY');
					// $EMAIL_BODY = "안녕하세요. (주)기프트넷 성인애입니다.[엔터][엔터]2019년 상반기 카다로그 진행 작업으로 연락드립니다.[엔터]당사에 등록된 귀사의 제품 진행 여부를 첨부된 파일에 작성하여 회신해 주시고,[엔터]새로 제안하실 제품이 있으시면 제안서도 함께 회신 부탁드립니다.[엔터]그리고 제품명 및 구성/규격/단가 등등 맞는지 꼭 확인해 주시고 변경사항 수정해서 보내주세요.[엔터]카다로그는 2월 말~3월 초 배포이고, 7월~8월까지 사용합니다. [엔터]그때까지 운용하는데 문제없는지도 확인해주시고 꼭 회신 부탁드리겠습니다.[엔터]★품절 제품 기재 시 재입고 일정을 필히 기재해 주시고,[엔터]재입고 계획이 없는 경우 단종으로 기재 부탁드립니다.★[엔터][엔터]감사합니다 :)[엔터][엔터]문의사항 : (직통번호) 070-4269-8150 성인애";
					$EMAIL_BODY = "안녕하세요. (주)기프트넷 입니다.

2019년 하반기 카탈로그 진행 작업으로 연락드립니다.
당사에 등록된 귀사의 제품 진행 여부를 첨부된 파일에 작성하여 회신해 주시고,
새로 제안하실 제품이 있으시면 제안서도 함께 회신 부탁드립니다.
보내 주실 때 제품명 및 구성/규격/단가 등등 맞는지 꼭 확인해 주시고 변경사항 수정해서 보내주세요.
★ 품절 제품 기재 시 재입고 일정을 필히 기재해 주시고,
재입고 계획이 없는 경우 단종으로 기재 부탁드립니다. ★


7월 29일까지 꼭 회신 부탁드립니다. 감사합니다 :)

문의사항 : (직통번호) 070-4269-8150 성인애";
					$EMAIL_BODY = str_replace("[엔터]", "\r\n", $EMAIL_BODY);
				?>
				<table cellpadding="0" cellspacing="0" class="colstable">
					<colgroup>
						<col width="10%" />
						<col width="*" />
					</colgroup>
						<tr>
							<th>발송 이메일</th>
							<td>
								<input type="text" name="from_email" value="<?=$FROM_EMAIL?>"/>
							</td>
						</tr>
						<tr>
							<th>메일제목</th>
							<td>
								<input type="text" name="email_title" value="<?=$EMAIL_TITLE?>" style="width:85%;"/>
							</td>
						</tr>
						<tr>
							<th>메일내용</th>
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
						<th>정렬</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="CP_NM" <? if ($order_field == "CP_NM") echo "selected"; ?> >업체명</option>
								<option value="GOODS_CNT" <? if ($order_field == "GOODS_CNT") echo "selected"; ?> >상품수</option>
							</select>&nbsp;&nbsp;
							<input type='radio' name='order_str' value='ASC' <? if (($order_str == "ASC") || ($order_str == "")) echo " checked"; ?>> 오름차순 &nbsp;
							<input type='radio' class="" name='order_str' value='DESC' <? if ($order_str == "DESC") echo " checked"; ?> > 내림차순 
						</td>
						<th>검색조건</th>
						<td>
							<!--<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20개씩</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50개씩</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100개씩</option>
								<option value="300" <? if ($nPageSize == "300") echo "selected"; ?> >300개씩</option>
								<option value="500" <? if ($nPageSize == "500") echo "selected"; ?> >500개씩</option>
							</select>&nbsp;
							-->
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >통합검색</option>
								<option value="CP_CODE" <? if ($search_field == "CP_CODE") echo "selected"; ?> >업체코드</option>
								<option value="CP_NAME" <? if ($search_field == "CP_NAME") echo "selected"; ?> >업체명 </option>
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
	
				총 <?=number_format($nListCnt)?> 건
				<div style="display:inline-block; width: 91.8%; text-align: right; margin: 0 0 10px 0;">
					<input type="button" value="선택발송" onclick="js_send_group_email()">
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
							<th>업체코드</th>
							<th>공급업체명</th>
							<th>판매중<input type="checkbox" id="type_a" name="type_a" <?if($type_a == "Y") echo "checked";?> value="Y"/></th>
							<th>품절<input type="checkbox" id="type_b" name="type_b" <?if($type_b == "Y") echo "checked";?> value="Y"/></th>
							<th>단종<input type="checkbox" id="type_c" name="type_c" <?if($type_c == "Y") echo "checked";?> value="Y"/></th>
							<th>엑셀확인</th>
							<th>이메일</th>
							<th>발송</th>
							<th class="end">최종 발송일</th>
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
								<!-- 선택발송 필요 데이터 : 1. 업체명 -->
								<input type="hidden" name="chk_to_name[]" value="<?= $CP_NM ?> <?= $CP_NM2 ?>"/>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '판매중')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "판매중")) ?>
								</a>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '품절')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "품절")) ?>
								</a>
							</td>
							<td class="price">
								<a href="javascript:js_link_to_goods_list('<?=$CP_NO?>', '단종')">
									<?= number_format(cntGoodsBuyCompany($conn, $CP_NO, "단종")) ?>
								</a>
							</td>
							<td>
								<a href="javascript:js_excel('<?=$CP_NO?>');"><img src="../images/common/btn/btn_excel.gif" alt="엑셀 리스트" /></a>
							</td>
							<td>
								<input type="text"  name="email_to_<?=$CP_NO?>" value="<?=$EMAIL?>"/>
								<!-- 선택발송 필요 데이터 : 2. 받는 이메일 -->
								<input type="hidden"  name="chk_email_to[]" value="<?=$EMAIL?>"/>
							</td>
							<td>
								<input type="button" name="btn_send" value="발송" onclick="javascript:js_send_email('<?=$CP_NO?>');"/>
							</td>
							<td>
								<? 
									$arr_email = getEmailDate($conn, '상품확인요청서', $CP_NO); 
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
							<td align="center" height="50" colspan="10">데이터가 없습니다. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
								
					<!-- --------------------- 페이지 처리 화면 START -------------------------->
					<?
						# ==========================================================================
						#  페이징 처리
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
					<!-- --------------------- 페이지 처리 화면 END -------------------------->
				
				
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