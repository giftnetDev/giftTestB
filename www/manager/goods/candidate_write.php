<?session_start();?>
<?
header("Pragma;no-cache");
header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD011"; // 메뉴마다 셋팅 해 주어야 합니다

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

#====================================================================
# common_header Check Session
#====================================================================
	include "../../_common/common_header.php"; 

	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/admin/admin.php";
	require "../../_classes/biz/goods/candidate.php";

	// 공급 업체 인경우
	//	$con_cate_03 = $s_adm_com_code;

#====================================================================
# DML Process
#====================================================================
	$goods_name			= SetStringToDB($goods_name);
	$goods_sub_name	= SetStringToDB($goods_sub_name);

#====================================================================
	$savedir1 = $g_physical_path."upload_data/goods";
#====================================================================
		
	//echo $mode."<br>";
	//echo $goods_no;

	if ($mode == "I") {
		
		//echo $goods_type;

		$file_nm_100			= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
		$file_rnm_100			= $_FILES[file_nm_100][name];

		$file_size_100		= $_FILES[file_nm_100][size];
		$file_ext_100			= end(explode('.', $_FILES[file_nm_100][name]));

		$fstock_cnt = 0;
		$bstock_cnt = 0;

		$extra_price = $price - $buy_price;

		$new_goods_no =  insertGoods($conn, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_cnt, $fstock_cnt, $bstock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no);

		//제안서정보
		updateGoodsProposal($conn, $component, $description_title, $description_body, $origin, $new_goods_no);

		// 기타 이미지 등록
		$file_cnt = count($ex_file1);

		for($i=0; $i <= $file_cnt; $i++) {

			if ($ex_flag1[$i] == "insert") {

				$file_name1					= multiupload($_FILES[ex_file1], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname1				= $_FILES[ex_file1][name][$i];

				$file_size1					= $_FILES[ex_file1][size][$i];
				$file_ext1					= end(explode('.', $_FILES[ex_file1][name][$i]));
			
			}

			if ($ex_flag2[$i] == "insert") {

				$file_name2					= multiupload($_FILES[ex_file2], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname2				= $_FILES[ex_file2][name][$i];

				$file_size2					= $_FILES[ex_file2][size][$i];
				$file_ext2					= end(explode('.', $_FILES[ex_file2][name][$i]));
			}
				$use_tf = "Y";

			if (($file_name1 <> "") or ($file_name2 <> "")) {
				$result_file = insertGoodsFile($conn, $new_goods_no, $file_name1, $file_rname1, $file_path1, $file_size1, $file_ext1, $file_name2, $file_rname2, $file_path2, $file_size2, $file_ext2);
			}
		}
		
?>
<script type="text/javascript">
	document.location = "candidate_write.php?mode=S&goods_no=<?=$new_goods_no?>";
</script>
<?
//		$mode			= "S";
//		$goods_no	= $new_goods_no;

		exit;
	}

	if ($mode == "U") {

		# file업로드
		switch ($flag01) {
			
			case "insert" :

				$file_nm_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
				$file_rnm_100		= $_FILES[file_nm_100][name];

				$file_size_100	= $_FILES[file_nm_100]['size'];
				$file_ext_100		= end(explode('.', $_FILES[file_nm_100]['name']));

			break;
			case "keep" :

				$file_nm_100		= $old_file_nm_100;
				$file_rnm_100		= $old_file_rnm_100;

				$file_size_100	= $old_file_size_100;
				$file_ext_100		= $old_file_ext_100;

			break;
			case "delete" :

				$file_nm_100		= "";
				$file_rnm_100		= "";

				$file_size_100	= "";
				$file_ext_100		= "";

			break;
			case "update" :

				$file_nm_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
				$file_rnm_100		= $_FILES[file_nm_100][name];

				$file_size_100 = $_FILES[file_nm_100]['size'];
				$file_ext_100  = end(explode('.', $_FILES[file_nm_100]['name']));

			break;
		}

		$extra_price = $price - $buy_price;

		$result = updateGoods($conn, $goods_cate, $goods_code, $goods_name, $goods_sub_name, $cate_01, $cate_02, $cate_03, $cate_04, $restock_date, $price, $buy_price, $sale_price, $next_sale_price, $extra_price, $stock_cnt, $mstock_cnt, $tax_tf, $img_url, $file_nm_100, $file_rnm_100, $file_path_100, $file_size_100, $file_ext_100, $file_nm_150, $file_rnm_150, $file_path_150, $file_size_150, $file_ext_150, $contents, $memo, $delivery_cnt_in_box, $sticker_price, $print_price, $delivery_price, $sale_susu, $labor_price, $other_price, $use_tf, $s_adm_no, $goods_no);

		//제안서정보
		updateGoodsProposal($conn, $component, $description_title, $description_body, $origin, $goods_no);
		
		// 이미지 파일 등록
		$reslt_file_del = deleteGoodsFile($conn, $s_adm_no, $goods_no);

		$file_cnt1 = count($ex_file1);
		$file_cnt2 = count($ex_file2);
		
		for($i=0; $i <= ($file_cnt1 - 1) ; $i++) {
			
			//echo $ex_flag1[$i];

			if ($ex_flag1[$i] == "insert") {

				$file_name1					= multiupload($_FILES[ex_file1], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname1				= $_FILES[ex_file1][name][$i];

				$file_size1					= $_FILES[ex_file1][size][$i];
				$file_ext1					= end(explode('.', $_FILES[ex_file1][name][$i]));

				//echo $goods_no."//".$file_name1."//".$file_rname1."<br/>"; 
			
			}

			if ($ex_flag1[$i] == "update") {

				$file_name1					= multiupload($_FILES[ex_file1], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname1				= $_FILES[ex_file1][name][$i];

				$file_size1					= $_FILES[ex_file1][size][$i];
				$file_ext1					= end(explode('.', $_FILES[ex_file1][name][$i]));
				$reg_date1					= $old_ex_reg_date[$i];
			
			    //echo $goods_no."//".$file_name1."//".$file_rname1."<br/>";
			}

			if ($ex_flag1[$i] == "keep") {
				
				$file_name1					= $old_ex_file_nm1[$i];
				$file_rname1				= $old_ex_file_rnm1[$i];

				$file_size1					= $old_ex_file_size1[$i];
				$file_ext1					= $old_ex_file_ext1[$i];
				$reg_date1					= $old_ex_reg_date1[$i];

				//echo "200 ".$file_name1." ".$i."<br>";
				//echo $goods_no."//".$file_name1."//".$file_rname1."<br/>";

			}


			if ($ex_flag2[$i] == "insert") {

				$file_name2					= multiupload($_FILES[ex_file2], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname2				= $_FILES[ex_file2][name][$i];

				$file_size2					= $_FILES[ex_file2][size][$i];
				$file_ext2					= end(explode('.', $_FILES[ex_file2][name][$i]));
			
			}

			if ($ex_flag2[$i] == "update") {

				$file_name2					= multiupload($_FILES[ex_file2], $i, $savedir1, 1 , array('gif', 'jpeg', 'jpg','png'));
				$file_rname2				= $_FILES[ex_file2][name][$i];

				$file_size2					= $_FILES[ex_file2][size][$i];
				$file_ext2					= end(explode('.', $_FILES[ex_file2][name][$i]));
				$reg_date2					= $old_ex_reg_date[$i];
			
			}
			
			//echo "50 ".$ex_flag2[$i]."<br>";
			
			if ($ex_flag2[$i] == "keep") {
					
				$file_name2					= $old_ex_file_nm2[$i];
				$file_rname2				= $old_ex_file_rnm2[$i];

				$file_size2					= $old_ex_file_size2[$i];
				$file_ext2					= $old_ex_file_ext2[$i];
				$reg_date2					= $old_ex_reg_date2[$i];

				//echo "50 ".$file_name2." ".$i."<br>";

			
			}
			

			if (($file_name1 <> "") or ($file_name2 <> "")) {

				//echo $goods_no."//".$file_name1."//".$file_rname1."<br/>";
				$result_file = insertGoodsFile($conn, $goods_no, $file_name1, $file_rname1, $file_path1, $file_size1, $file_ext1, $file_name2, $file_rname2, $file_path2, $file_size2, $file_ext2);
			}
			
			$file_name1 = "";
			$file_name2 = "";
			
		}
		
	}

	if ($mode == "D") {

		$result = deleteGoods($conn, $s_adm_no, $goods_no);
	}

	if ($mode == "S") {

		$arr_rs = selectGoods($conn, $goods_no);

		$rs_goods_no			= trim($arr_rs[0]["GOODS_NO"]); 
		$rs_goods_cate			= trim($arr_rs[0]["GOODS_CATE"]); 
		$rs_goods_code			= trim($arr_rs[0]["GOODS_CODE"]); 
		$rs_goods_name			= SetStringFromDB($arr_rs[0]["GOODS_NAME"]); 
		$rs_goods_sub_name	    = SetStringFromDB($arr_rs[0]["GOODS_SUB_NAME"]); 
		$rs_cate_01				= trim($arr_rs[0]["CATE_01"]); 
		$rs_cate_02				= trim($arr_rs[0]["CATE_02"]); 
		$rs_cate_03				= trim($arr_rs[0]["CATE_03"]); 
		$rs_cate_04				= trim($arr_rs[0]["CATE_04"]);
		$rs_restock_date		= trim($arr_rs[0]["RESTOCK_DATE"]); 
		$rs_price				= trim($arr_rs[0]["PRICE"]); 
		$rs_buy_price			= trim($arr_rs[0]["BUY_PRICE"]); 
		$rs_sale_price			= trim($arr_rs[0]["SALE_PRICE"]); 
		$rs_extra_price			= trim($arr_rs[0]["EXTRA_PRICE"]); 
		$rs_stock_cnt			= trim($arr_rs[0]["STOCK_CNT"]); 
		$rs_mstock_cnt          = trim($arr_rs[0]["MSTOCK_CNT"]);
		$rs_tax_tf				= trim($arr_rs[0]["TAX_TF"]); 
		$rs_img_url				= trim($arr_rs[0]["IMG_URL"]); 
		$rs_file_nm_100			= trim($arr_rs[0]["FILE_NM_100"]); 
		$rs_file_rnm_100		= trim($arr_rs[0]["FILE_RNM_100"]); 
		$rs_file_path_100		= trim($arr_rs[0]["FILE_PATH_100"]); 
		$rs_file_size_100		= trim($arr_rs[0]["FILE_SIZE_100"]); 
		$rs_file_ext_100		= trim($arr_rs[0]["FILE_EXT_100"]); 
		$rs_file_nm_150			= trim($arr_rs[0]["FILE_NM_150"]); 
		$rs_file_rnm_150		= trim($arr_rs[0]["FILE_RNM_150"]); 
		$rs_file_path_150		= trim($arr_rs[0]["FILE_PATH_150"]); 
		$rs_file_size_150		= trim($arr_rs[0]["FILE_SIZE_150"]); 
		$rs_file_ext_150		= trim($arr_rs[0]["FILE_EXT_150"]); 
		$rs_contents			= trim($arr_rs[0]["CONTENTS"]); 
		$rs_memo				= trim($arr_rs[0]["MEMO"]); 
		$rs_delivery_cnt_in_box	= trim($arr_rs[0]["DELIVERY_CNT_IN_BOX"]); 
		$rs_read_cnt			= trim($arr_rs[0]["READ_CNT"]); 
		$rs_disp_seq			= trim($arr_rs[0]["DISP_SEQ"]); 
		$rs_use_tf				= trim($arr_rs[0]["USE_TF"]); 
		$rs_del_tf				= trim($arr_rs[0]["DEL_TF"]); 
		$contents			    = trim($arr_rs[0]["CONTENTS"]); 
		
		/* 2015 9월 08일 추가*/
		$rs_sticker_price		= trim($arr_rs[0]["STICKER_PRICE"]); 
		$rs_print_price			= trim($arr_rs[0]["PRINT_PRICE"]); 
		$rs_delivery_price		= trim($arr_rs[0]["DELIVERY_PRICE"]); 
		$rs_sale_susu			= trim($arr_rs[0]["SALE_SUSU"]); 

		/* 2016 2월 18일 추가*/
		$rs_labor_price			= trim($arr_rs[0]["LABOR_PRICE"]); 
		$rs_other_price			= trim($arr_rs[0]["OTHER_PRICE"]); 

		/* 2016 10월 10일 추가*/
		$rs_next_sale_price		= trim($arr_rs[0]["NEXT_SALE_PRICE"]); 
		$rs_reg_adm				= trim($arr_rs[0]["REG_ADM"]); 
		$rs_reg_date			= trim($arr_rs[0]["REG_DATE"]); 

		if($rs_reg_date != "0000-00-00 00:00:00")
			$rs_reg_date = date("Y-m-d H:i",strtotime($rs_reg_date));
		else
			$rs_reg_date = "";

		if($rs_restock_date != "0000-00-00 00:00:00")
			$rs_restock_date = date("Y-m-d",strtotime($rs_restock_date));
		else
			$rs_restock_date = "";

		$arr_rs_file = selectGoodsFile($conn, $goods_no);

		$arr_rs_goods_proposal = selectGoodsProposal($conn, $goods_no);

		if(sizeof($arr_rs_goods_proposal) > 0) { 
			
			$rs_component			= SetStringFromDB($arr_rs_goods_proposal[0]["COMPONENT"]); 
			$rs_description_title   = SetStringFromDB($arr_rs_goods_proposal[0]["DESCRIPTION_TITLE"]); 
			$rs_description_body    = SetStringFromDB($arr_rs_goods_proposal[0]["DESCRIPTION_BODY"]);
			$rs_origin				= SetStringFromDB($arr_rs_goods_proposal[0]["ORIGIN"]);

		} else { 

			$rs_component = "";
			$rs_description_title = "";
			$rs_description_body = "";
			$rs_origin = "";
		}

	}

	$strParam = "?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf;

	if ($result) {

		//$strParam = $strParam."?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str;
?>	
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<script language="javascript">
	function init() {
		alert('정상 처리 되었습니다.');
		document.frm.target = "";
		document.frm.submit();
	}
		
</script>
<body onload="init();">
<form name="frm" action="candidate_list.php" method="post">
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

<input type="hidden" name="order_field" value="<?=$order_field?>">
<input type="hidden" name="order_str" value="<?=$order_str?>">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<input type="hidden" name="start_price" value="<?=$start_price?>">
<input type="hidden" name="end_price" value="<?=$end_price?>">
<input type="hidden" name="con_cate" value="<?=$con_cate?>">
<input type="hidden" name="con_cate_01" value="<?=$con_cate_01?>">
<input type="hidden" name="con_cate_02" value="<?=$con_cate_02?>">
<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
<input type="hidden" name="con_cate_04" value="<?=$con_cate_04?>">
<input type="hidden" name="con_tax_tf" value="<?=$con_tax_tf?>">
</body>
</html>

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
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../../_common/SE2.1.1.8141/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.cookie.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });
  });
</script>
<script>
	$(function() {
		$('.autocomplete_off').attr('autocomplete', 'off');
	});
</script>
<script>
	$(function() {
		$("#tabs").tabs({
		  active : 0
		});
	});
</script>
<script type="text/javascript">

	function js_list() {
		document.location.href = "candidate_list.php<?=$strParam?>";
	}

	function js_save()
	{
		var frm = document.frm;
		var goods_no = "<?= $goods_no ?>";
		
		frm.goods_name.value = frm.goods_name.value.trim();
		frm.buy_price.value = frm.buy_price.value.trim();
		frm.sale_price.value = frm.sale_price.value.trim();

		// 제조사
		frm.cate_02.value = frm.cate_02.value.trim();

		oEditors[0].exec("UPDATE_CONTENTS_FIELD", []);   // 에디터의 내용이 textarea에 적용된다.
		frm.contents.value = frm.contents.value.trim();
		
		if (isNull(frm.goods_name.value)) {
			alert('상품명을 입력해주세요.');
			frm.goods_name.focus();
			return ;		
		}

		if (frm.cate_03.value == "") {
			if(!confirm('선택한 공급사가 없습니다. 샘플처럼 특정 회사가 없는경우만 공급사가 없습니다. 저장하시겠습니까?')) { 
				frm.cate_03.focus();
				return ;	
			}
		}

		if (isNull(frm.delivery_cnt_in_box.value) || frm.delivery_cnt_in_box.value == "0") {
			alert('박스입수를 입력해주세요. 최소수량은 1입니다');
			frm.delivery_cnt_in_box.focus();
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


		if (isNull(goods_no)) {
			frm.mode.value = "I";
		} else {
			frm.mode.value = "U";
			frm.goods_no.value = frm.goods_no.value;
		}

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_delete() {

		var frm = document.frm;

		bDelOK = confirm('자료를 삭제 하시겠습니까?');
		
		if (bDelOK==true) {
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

/**
* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
*/
function js_fileView(obj) {

	var fileCnt = 3;
	if(fileCnt == 1) {
		if (obj.selectedIndex == 2) { 
			document.frm.file_nm_100.style.visibility = "visible"; 
		} else { 
			document.frm.file_nm_100.style.visibility = "hidden"; 
		}	
	} else if (fileCnt > 1) {
		if (obj.selectedIndex == 2) { 
			document.frm.file_nm_100.style.visibility = "visible"; 
		} else { 
			document.frm.file_nm_100.style.visibility = "hidden"; 
		}	
	}
}

/**
* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
*/
function js_fileView2(obj) {

	var fileCnt = 3;
	if(fileCnt == 1) {
		if (obj.selectedIndex == 2) { 
			document.frm.file_nm_150.style.visibility = "visible"; 
		} else { 
			document.frm.file_nm_150.style.visibility = "hidden"; 
		}	
	} else if (fileCnt > 1) {
		if (obj.selectedIndex == 2) { 
			document.frm.file_nm_150.style.visibility = "visible"; 
		} else { 
			document.frm.file_nm_150.style.visibility = "hidden"; 
		}	
	}
}

/**
* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
*/
function js_exfileView1(idx) {

	var obj = document.frm["ex_flag1[]"][idx];
	
	if (obj.selectedIndex == 2) {
		document.frm["ex_file1[]"][idx].style.visibility = "visible"; 
	} else { 
		document.frm["ex_file1[]"][idx].style.visibility = "hidden"; 
	}	
}

function js_exfileView2(idx) {

	var obj = document.frm["ex_flag2[]"][idx];
	
	if (obj.selectedIndex == 2) {
		document.frm["ex_file2[]"][idx].style.visibility = "visible"; 
	} else { 
		document.frm["ex_file2[]"][idx].style.visibility = "hidden"; 
	}	
}


</script>

<script type="text/javascript">

	function js_calculate_buy_and_sale_price( )	{

		var i_sale_price		= 0;
		var i_buy_price			= 0;
		var i_sticker_price = 0;
		var i_print_price		= 0;
		var i_delivery_cnt_in_box = 1;
		var i_delivery_price = 0;
		var f_sale_susu = 0;
		var i_delivery_per_price = 0;
		var i_total_wonga = 0;
		var i_susu_price = 0;
		var i_labor_price = 0;
		var i_other_price = 0;
		var i_majin	= 0;
		var f_majin_per	= 0;

		if ($("input[name=sale_price]").val() != "") i_sale_price = parseInt($("input[name=sale_price]").val());
		if ($("input[name=buy_price]").val() != "") i_buy_price = parseInt($("input[name=buy_price]").val());
		if ($("input[name=sticker_price]").val() != "") i_sticker_price = parseInt($("input[name=sticker_price]").val());
		if ($("input[name=print_price]").val() != "") i_print_price = parseInt($("input[name=print_price]").val());
		if ($("input[name=delivery_cnt_in_box]").val() != "") i_delivery_cnt_in_box = parseInt($("input[name=delivery_cnt_in_box]").val());
		if ($("input[name=delivery_price]").val() != "") i_delivery_price = parseInt($("input[name=delivery_price]").val());
		if ($("input[name=sale_susu]").val() != "") f_sale_susu = parseFloat($("input[name=sale_susu]").val());
		if ($("input[name=labor_price]").val() != "") i_labor_price = parseInt($("input[name=labor_price]").val());
		if ($("input[name=other_price]").val() != "") i_other_price = parseInt($("input[name=other_price]").val());

		var has_susu = $("input[name=has_susu]").is(":checked");
		
		if(i_delivery_price == 0)
			i_delivery_per_price = 0;
		else
			i_delivery_per_price = Math.round(i_delivery_price / i_delivery_cnt_in_box);
		$("#delivery_per_price").html(numberFormat(i_delivery_per_price));

		i_susu_price = Math.round((i_sale_price / 100) * f_sale_susu);
		$("#susu_price").html(numberFormat(i_susu_price));

		i_total_wonga = i_buy_price + i_sticker_price + i_print_price + i_delivery_per_price + i_labor_price + i_other_price;
		$("#total_wonga").val(i_total_wonga);
		
		if(!has_susu) {
			f_sale_susu = 0;
			i_susu_price = 0;
		}

		i_majin = i_sale_price - i_susu_price - i_total_wonga;
		if(i_majin > 0)
			$("#majin").html(numberFormat(i_majin));
		else
			$("#majin").html(i_majin);
		
		if (i_sale_price != 0) {
			f_majin_per = Math.round10((i_majin / i_sale_price) * 100.0, -2);
			$("#majin_per").html(f_majin_per);
		} else {
			if (i_majin == 0) {
				f_majin_per = 0
				$("#majin_per").html(f_majin_per);
			} else {
				f_majin_per = -100
				$("#majin_per").html(f_majin_per);
			}
		}

		var i_vender_calc = 0;

		if(i_sale_price > 0 && i_majin > 0) { 

			if ($("input[name=vendor_calc]").val() != "") i_vender_calc = parseInt($("input[name=vendor_calc]").val());
			var vendor15 = Math.ceil10(((i_sale_price - i_total_wonga) * 15 / 100.0 + i_total_wonga) , 1);
			var vendor35 = Math.ceil10(((i_sale_price - i_total_wonga) * 35 / 100.0 + i_total_wonga) , 1);
			var vendor_calc = Math.ceil10(((i_sale_price - i_total_wonga) * i_vender_calc / 100.0 + i_total_wonga) , 1);

			$("#vendor15").html(numberFormat(vendor15));
			$("#vendor35").html(numberFormat(vendor35));
			$("#vendor_calc").html(numberFormat(vendor_calc));
		} else { 
			$("#vendor15").html("0");
			$("#vendor35").html("0");
			$("#vendor_calc").html("0");
		}

		var i_best_sale_calc = 0;
		if ($("input[name=best_sale_calc]").val() != "") i_best_sale_calc = parseInt($("input[name=best_sale_calc]").val());

		var best_sale_price = Math.ceil10(i_total_wonga / ((100 - f_sale_susu - i_best_sale_calc) / 100), 1);
		$("#best_sale_price").html(numberFormat(best_sale_price));


		$(".calc").each(function(index, value){
	
			var name = $(this).attr("name");
			if(name.indexOf("[]") <= -1) { 
				if(name != "sale_susu") { 
					if($(this).val() != parseInt($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				} else {
					if($(this).val() != parseFloat($("font[class="+name+"]").attr("data-value")))
						$("font[class="+name+"]").show();
					else
						$("font[class="+name+"]").hide();
				}
			}

		});

	}

$(function(){
	$(".calc").blur(function(){

		var withcomma = $(this).val();
		$(this).val(withcomma.replaceall(',',''));
	
		js_calculate_buy_and_sale_price();

	});
});

	function js_view_company(cp_no) {

		window.open(
		  "/manager/company/company_write.php?mode=S&cp_no=" + cp_no,
		  '_blank' 
		);
		
	}

</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="" />
<input type="hidden" name="depth" value="" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="goods_no" value="<?=$goods_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />

<input type="hidden" name="search_field" value="<?=$search_field?>">
<input type="hidden" name="search_str" value="<?=$search_str?>">

<input type="hidden" name="order_field" value="<?=$order_field?>">
<input type="hidden" name="order_str" value="<?=$order_str?>">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<input type="hidden" name="start_price" value="<?=$start_price?>">
<input type="hidden" name="end_price" value="<?=$end_price?>">
<input type="hidden" name="con_cate" value="<?=$con_cate?>">
<input type="hidden" name="con_cate_01" value="<?=$con_cate_01?>">
<input type="hidden" name="con_cate_02" value="<?=$con_cate_02?>">
<input type="hidden" name="con_cate_03" value="<?=$con_cate_03?>">
<input type="hidden" name="con_cate_04" value="<?=$con_cate_04?>">
<input type="hidden" name="con_tax_tf" value="<?=$con_tax_tf?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_type" value="">
<input type="hidden" name="exclude_category" value="">
<!--<input type="hidden" name="send_data" value="">-->


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
			
			<h2>후보 상품 관리</h2>

				<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<tbody>
					<tr>
						<th>상품명</th>
						<td colspan="3" class="line">
							<input type="text" class="txt" style="width:95%" name="goods_name" required value="<?=$rs_goods_name?>" />
						</td>
					</tr>
					<tr>
						<th>모델명</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="goods_sub_name" value="<?=$rs_goods_sub_name?>" />
						</td>
						<th>상품코드</th>
						<td class="line">
							<input type="text" class="txt" style="width:110px" name="goods_code" value="<?=$rs_goods_code?>" /> 
							&nbsp;&nbsp;&nbsp;<span class="msg"></span>
							<script>

								$("input[name=goods_code]").keyup(function(){
									var new_code = $(this).val().trim();
									if(new_code.length >= 10)
										checkDuplicate($(this).val().trim(), '');
								});

								function checkDuplicate(new_code, serial_part) {

									if (!isNull(new_code)) {
										
										$.ajax({
										  url: "json_goods_list.php",
										  dataType: 'json',
										  async: false,
										  data: {goods_code: new_code, serial_part:serial_part},
										  success: function(data) {
											$.each( data, function( i, item ) {
												if(item.RESULT == "1" && item.PARTLY == "1") {
												
													$(".msg").css("color","red");
													$(".msg").html("에러 : 상품코드 중복");

												} else if(item.RESULT == "0" && item.PARTLY == "1") {
												
													$(".msg").css("color","blue");
													$(".msg").html("체크요망 : 일련번호 중복");
												
												} else if(item.RESULT == "0" && item.PARTLY == "0") {
													$(".msg").css("color","black");
													$(".msg").html("");
												}

											  });
										  }
										});
									}

								}

							</script>
						</td>
					</tr>
					<tr>
						<th>제조사</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="cate_02" value="<?=$rs_cate_02?>" />
						</td>
						<th>최소재고</th>
						<td class="line">
							<input type="text" class="txt" style="width:210px" name="mstock_cnt" value="<?=($rs_mstock_cnt != "" ? $rs_mstock_cnt : 0)?>" onkeyup="return isNumber(this)"/> 개
						</td>
					</tr>

					<tr>
						<th>공급사</th>
						<td class="line">
							<input type="text" class="autocomplete_off" style="width:200px" placeholder="업체(명/코드) 입력후 엔터를 눌러주세요" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$rs_cate_03)?>" />
							<input type="hidden" name="cate_03" value="<?=$rs_cate_03?>">
							

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=cate_03]").val('');
											} else { 
												$.getJSON( "../company/json_company_list.php?cp_type=" + encodeURIComponent('구매,판매공급') +"&term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "cate_03", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?con_cp_type=구매,판매공급&search_str="+keyword + "&target_name=txt_cp_type&target_value=cate_03",'pop_company_searched_list','950','650','YES');

													} else 
														alert("검색결과가 없습니다.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=cate_03]").val('');
										}
									});

								});

								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

								}

							</script>
							<input type="button" onclick="javascript:js_view_company(document.frm.cate_03.value)" value="업체정보"/>
						</td>
						<th>재고 수량</th>
						<td class="line">
							<?=$rs_stock_cnt?> 개
							<input type="hidden" name="stock_cnt" value="<?=$rs_stock_cnt?>" />
						</td>
					</tr>

					<tr>
						<th>판매상태</th>
						<td  class="line">
							<? if($goods_no <> "") { ?>
								<?= makeSelectBox($conn,"GOODS_STATE","cate_04","120","판매상태", "", $rs_cate_04)?>
							<? } else { ?>
								<?= makeSelectBox($conn,"GOODS_STATE","cate_04","120","판매상태", "", "판매중")?>
							<? } ?> 
							<span class="restock" style="<?= ($rs_cate_04 != "품절" || $goods_no == "" ? "display:none;" : "")?>">&nbsp;&nbsp;
								재입고일:<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px; " name="restock_date" value="<?=$rs_restock_date?>" maxlength="10"/>
							</span>
							<script>
								$("select[name=cate_04]").change(function(){
									if($(this).val() == "품절")
										$(".restock").show();
									else
										$(".restock").hide();
								});
							</script>
						</td>

						<th>과세여부</th>
						<td class="line">
							<?= makeSelectBox($conn,"TAX_TF","tax_tf","105","","",$rs_tax_tf)?>
						</td>
					</tr>
					<tr>
						<th>상품비고란</th>
						<td colspan="3" class="line">
							<textarea name="memo" style="padding-left:0px;width:100%;height:40px;"><?=$rs_memo?></textarea>
						</td>
					</tr>
					<tr>
						<th style="color:red;">사용여부</th>
						<td class="line" colspan="3">
							<input type="radio" class="radio" name="rd_use_tf" value="Y" <? if (($rs_use_tf =="Y") || ($rs_use_tf =="")) echo "checked"; ?>> 사용함 <span style="width:20px;"></span>&nbsp;&nbsp;&nbsp;
							<input type="radio" class="radio" name="rd_use_tf" value="N" <? if ($rs_use_tf =="N") echo "checked"; ?>> 사용안함
							<input type="hidden" name="use_tf" value="<?= $rs_use_tf ?>"> 
						</td>

					</tr>
				</table>

				<div class="sp20"></div>

				<div id="tabs" style="width:95%; margin:10px 0;">
					<ul>
						<li><a href="#tabs-1">가격 정보</a></li>
						<li><a href="#tabs-2">상품 이미지</a></li>
						<li><a href="#tabs-3">제안서 정보</a></li>
					</ul>
					<div id="tabs-1">

						<table cellpadding="0" cellspacing="0" class="colstable02">
						<colgroup>
							<col width="15%" />
							<col width="35%" />
							<col width="15%" />
							<col width="35%" />
						</colgroup>
							
							<tr>
								<th title="(세트)매입가 = 아웃박스 제외 구성자재 매입가 * 수량의 합 + (아웃박스 매입가 * 수량 / 박스입수)">매입가</th>
								<td class="line">
									<input type="text" class="txt calc buy_price" style="width:90px" name="buy_price" value="<?=$rs_buy_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" <?=(startsWith($rs_goods_cate, '14')  ? "readonly" : "") ?> /> 원 <font class="buy_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_buy_price?>">(<?=$rs_buy_price?> 원)</font>
								</td>
								<th>판매가</th>
								<td class="line">
									<input type="text" class="txt calc sale_price" style="width:90px" name="sale_price" value="<?=$rs_sale_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()" /> 원 <font class="sale_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_price?>">(<?=$rs_sale_price?> 원)</font>
								</td>
							</tr>
							
							<tr>
								<th>스티커 비용</th>
								<td class="line">
									<input type="text" class="txt calc sticker_price" style="width:90px" name="sticker_price" value="<?=$rs_sticker_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="sticker_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sticker_price?>">(<?=$rs_sticker_price?> 원)</font>
								</td>
								<th>밴더할인 15%</th>
								<td class="line">
									<span id="vendor15"></span>원
								</td>
							</tr>
							<tr>
								<th>포장인쇄 비용</th>
								<td class="line">
									<input type="text" class="txt calc print_price" style="width:90px" name="print_price" value="<?=$rs_print_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="print_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_print_price?>">(<?=$rs_print_price?> 원)</font>
								</td>
								<th>밴더할인 35%</th>
								<td class="line">
									<span id="vendor35"></span>원
								</td>
							</tr>
							<tr>
								<th>택배비용</th>
								<td class="line">
									<input type="text" class="txt calc delivery_price" style="width:90px" name="delivery_price" value="<?=$rs_delivery_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="delivery_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_price?>">(<?=$rs_delivery_price?> 원)</font>
								</td>
								<th>밴더할인 <input type="text" name="vendor_calc" value="55" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
								<td class="line">
									<span id="vendor_calc"></span>원
								</td>
							</tr>
							<tr>
								<th>박스입수</th>
								<td class="line">
									<input type="text" class="txt calc delivery_cnt_in_box" style="width:90px" name="delivery_cnt_in_box" value="<?=($rs_delivery_cnt_in_box == '' || $rs_delivery_cnt_in_box == '0' ? 1 : $rs_delivery_cnt_in_box) ?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 개 <font class="delivery_cnt_in_box" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_delivery_cnt_in_box?>">(<?=$rs_delivery_cnt_in_box?> 개)</font>
								</td>
								<th>판매 수수률</th>
								<td class="line">
									<input type="text" class="txt calc sale_susu" style="width:90px" name="sale_susu" value="<?=($goods_no == "" ? "7.15" : $rs_sale_susu)?>" onkeyup="return isFloat(this)" onkeyup="js_calculate_buy_and_sale_price()"/> % <font class="sale_susu" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_sale_susu?>">(<?=$rs_sale_susu?> %)</font> &nbsp;&nbsp; <input type="checkbox" name="has_susu" onchange="js_calculate_buy_and_sale_price()" checked value="Y"/>&nbsp;(참조용 - 저장하지 않음)
								</td>	
							</tr>
							<tr>
								<th title="물류비 = 택배비용 / 박스입수">
									물류비
								</th>
								<td class="line">
									<span id="delivery_per_price">0</span> 원
								</td>
								<th title="판매 수수료 = ((판매가 / 100) * 판매 수수률)">판매 수수료</th>
								<td class="line">
									<span id="susu_price">0</span> 원
								</td>	
			
							</tr>
							<tr>
								<th>인건비</th>
								<td class="line">
									<input type="text" class="txt calc labor_price" style="width:90px" name="labor_price" value="<?=$rs_labor_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="labor_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_labor_price?>">(<?=$rs_labor_price?> 원)</font>
								</td>
								<th title="마진 = 판매가 - 판매수수료 - 매입합계">마진</th>
								<td class="line">
									<span id="majin">0</span> 원
									
								</td>	
							</tr>
							<tr>
								<th>기타 비용</th>
								<td class="line">
									<input type="text" class="txt calc other_price" style="width:90px" name="other_price" value="<?=$rs_other_price?>" onkeyup="return isNumber(this)" onkeyup="js_calculate_buy_and_sale_price()"/> 원 <font class="other_price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_other_price?>">(<?=$rs_other_price?> 원)</font>
								</td>
								<th title="마진률 = 마진 / 판매가 * 100">마진률</th>
								<td class="line">
									<span id="majin_per">0</span> %
								</td>
							</tr>
							<tr>
								<th title="매입합계 = 매입가(아웃박스 제외 자재매입가의 합 + (아웃박스 매입가 / 박스입수)) + 스티커비용 + 포장인쇄비용 + 물류비 + 인건비 + 기타비용">매입합계</th>
								<td class="line">
									<input type="text" id="total_wonga" class="txt calc price" style="width:90px" name="price" value="<?=$rs_price?>" onkeyup="return isNumber(this)" readonly /> 원 <font class="price" color="gray" style="font-size:0.9em; display:none;" data-value="<?=$rs_price?>">(<?=$rs_price?> 원)</font>
									
								</td>
								<th title="마진률 기반으로 판매가 역 산출">최적판매가 <input type="text" name="best_sale_calc" value="20" class="txt calc" onkeyup="js_calculate_buy_and_sale_price()" style="width:20px;"/> %</th>
								<td class="line">
									<span id="best_sale_price">N/A</span> 원
								</td>
							</tr>

						</table>
						<span style="color:red;">* 금액 변경시 나오는 괄호안 숫자는 현재 저장되어 있는 가격입니다</span> 
						<div class="sp20"></div>
					</div>

					
					<div id="tabs-2">
						<table cellpadding="0" cellspacing="0" class="colstable02">
							<colgroup>
								<col width="15%" />
								<col width="35%" />
								<col width="15%" />
								<col width="35%" />
							</colgroup>
						
							<tr>
								<th>상품 이미지 URL</th>
								<td colspan="2" class="line">
									<? if($rs_img_url != "") { ?>
										<img class="img_goods" src="<?=$rs_img_url?>" alt="<?=$rs_img_url?>" width="100" alt="이미지"><br>
									<? } ?>
									<input type="text" class="txt" style="width:75%" name="img_url" value="<?=$rs_img_url?>" />
								</td>
								<td rowspan="3" style="text-align:center; vertical-align: middle;"><img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/></td>
								<script>
								$(function(){

									$(".img_goods").each(function(index, obj){
										
										var image_url = $(this).attr('src');
										if(image_url != '')
											$("img[name=sample_img]").attr("src", image_url);


									});

									$(".img_goods").click(function(){
										
										var image_url = $(this).attr('src');
										$("img[name=sample_img]").attr("src", image_url);

									});

								});
								</script>
							</tr>
											
							<th>이미지 경로</th>
								<td colspan="2" class="line">
									<? if($rs_file_path_150 != "" && $rs_file_rnm_150 != "") { ?>
										<img class="img_goods" src="<?=$rs_file_path_150?><?=$rs_file_rnm_150?>" alt="" width="100" alt="이미지"><br>
									<? } ?>
									<input type="text" class="txt" style="width:25%" name="file_path_150" value="<?=$rs_file_path_150?>" />&nbsp;
									<input type="text" class="txt" style="width:15%" name="file_rnm_150" value="<?=$rs_file_rnm_150?>" />
								</td>
							</tr>
							
							<tr>
								<th>상품 이미지</th>
								<td colspan="2" class="line">
								<?
									if (strlen($rs_file_nm_100) > 3) {
								?>
									<? if($rs_file_nm_100 != "") { ?>
										<img class="img_goods" src="/upload_data/goods/<?=$rs_file_nm_100?>" alt="<?=$rs_file_rnm_100?>" width="100" alt="이미지"><br>
									<? } ?>
									<?=$rs_file_rnm_100?><br>
									<select name="flag01" style="width:70px;" onchange="javascript:js_fileView(this)">
										<option value="keep">유지</option>
										<option value="delete">삭제</option>
										<option value="update">수정</option>
									</select>
									<input type="file" name="file_nm_100" size="20" class="txt" style="visibility:hidden" > &nbsp; 500*500<br>
									
									<input type="hidden" name="old_file_nm_100" value="<?= $rs_file_nm_100?>">
									<input type="hidden" name="old_file_rnm_100" value="<?= $rs_file_rnm_100?>">
									<input type="hidden" name="old_file_size_100" value="<?= $rs_file_size_100?>">
									<input type="hidden" name="old_file_ext_100" value="<?= $rs_file_ext_100?>">

								<?
									} else {	
								?>

									<input type="file" name="file_nm_100" size="20" class="txt"><span class="txt_tbl_in nbsp"> &nbsp; 500*500</span>
									<input type="hidden" name="old_file_nm_100" value="">
									<input type="hidden" name="old_file_rnm_100" value="">
									<input type="hidden" name="old_file_size_100" value="">
									<input type="hidden" name="old_file_ext_100" value="">
									<input TYPE="hidden" name="flag01" value="insert">

								<?
									}	
								?>

								</td>
							</tr>
							<tr>
								<th>상품 이미지(상세)</th>
								
								<td colspan="3" class="line">
									<div class="sp5"></div>
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
									<?
										if (sizeof($arr_rs_file) > 0) {
								
											for ($j = 0 ; $j < 3; $j++) {
									
												$GOODS_IMAGE_NO			= trim($arr_rs_file[$j]["GOODS_IMAGE_NO"]);
												$GOODS_NO						= trim($arr_rs_file[$j]["GOODS_NO"]);
												$FILE_NM1						= trim($arr_rs_file[$j]["FILE_NM1"]);
												$FILE_RNM1					= trim($arr_rs_file[$j]["FILE_RNM1"]);
												$FILE_SIZE1					= trim($arr_rs_file[$j]["FILE_SIZE1"]);
												$FILE_EXT1					= trim($arr_rs_file[$j]["FILE_EXT1"]);
												$FILE_NM2						= trim($arr_rs_file[$j]["FILE_NM2"]);
												$FILE_RNM2					= trim($arr_rs_file[$j]["FILE_RNM2"]);
												$FILE_SIZE2					= trim($arr_rs_file[$j]["FILE_SIZE2"]);
												$FILE_EXT2					= trim($arr_rs_file[$j]["FILE_EXT2"]);

												if (strlen($FILE_NM1) > 3) {
									?>
											<td width="50%">
											<div class="sp5"></div>
											<img src="/upload_data/goods/<?=$FILE_NM1?>" width="50" height="50">
											<select name="ex_flag1[]" style="width:50px;" onchange="javascript:js_exfileView1('<?=$j?>')">
												<option value="keep">유지</option>
												<option value="delete">삭제</option>
												<option value="update">수정</option>
											</select>&nbsp;
											<input type="file" name="ex_file1[]" size="15" style="visibility:hidden" class="txt"> &nbsp; 500*500
											<input type="hidden" name="old_ex_file_nm1[]" value="<?=$FILE_NM1?>">
											<input type="hidden" name="old_ex_file_rnm1[]" value="<?=$FILE_RNM1?>">
											<input type="hidden" name="old_ex_file_size1[]" value="<?=$FILE_SIZE1?>">
											<input type="hidden" name="old_ex_file_ext1[]" value="<?=$FILE_EXT1?>">
											<input type="hidden" name="old_ex_reg_date1[]" value="<?=$REG_DATE1?>">
											</td>
										</tr>
									<?
												} else {
									?>
											<td width="50%" >
											<div class="sp5"></div>
											<input type="hidden" name="ex_flag1[]" value="insert"><input type="file" size="20" name="ex_file1[]" class="txt"> &nbsp; 500*500
											<input type="hidden" name="old_ex_file_nm1[]" value="">
											<input type="hidden" name="old_ex_file_rnm1[]" value="">
											<input type="hidden" name="old_ex_file_size1[]" value="">
											<input type="hidden" name="old_ex_file_ext1[]" value="">
											<input type="hidden" name="old_ex_reg_date1[]" value="">
											</td>
										</tr>
											
									<?			
												}
											}
										}
									?>
											</td>
										</tr>
									</table>
									<? if (sizeof($arr_rs_file) < 1) {?>
										<div class="sp5"></div>
										<div style="height:23px">
											<input type="hidden" name="ex_flag1[]" value="insert"><input type="file" size="20" name="ex_file1[]" class="txt"> &nbsp; 500*500&nbsp;&nbsp;&nbsp;&nbsp;
											<!--<input type="hidden" name="ex_flag2[]" value="insert"><input type="file" size="20" name="ex_file2[]" class="box01"> 50*50-->
										</div>
										<div style="height:23px">
											<input type="hidden" name="ex_flag1[]" value="insert"><input type="file" size="20" name="ex_file1[]" class="txt"> &nbsp; 500*500&nbsp;&nbsp;&nbsp;&nbsp;
											<!--<input type="hidden" name="ex_flag2[]" value="insert"><input type="file" size="20" name="ex_file2[]" class="box01"> 50*50-->
										</div>
										<div style="height:23px">
											<input type="hidden" name="ex_flag1[]" value="insert"><input type="file" size="20" name="ex_file1[]" class="txt"> &nbsp; 500*500&nbsp;&nbsp;&nbsp;&nbsp;
											<!--<input type="hidden" name="ex_flag2[]" value="insert"><input type="file" size="20" name="ex_file2[]" class="box01"> 50*50-->
										</div>
									<? 
										}
									?>
									<div class="sp5"></div>
								</td>
							</tr>
							<tr>
								<th>상품 상세</th>
								<td colspan="3" class="subject line">
									<span class="fl" style="padding-left:0px;width:100%;height:500px;"><textarea name="contents" id="contents"  style="padding-left:0px;width:100%;height:400px;"><?=$rs_contents?></textarea></span>
								</td>
							</tr>

						</table>
					</div>

					<div id="tabs-3">
						<table cellpadding="0" cellspacing="0" class="colstable02">
							<colgroup>
								<col width="15%" />
								<col width="35%" />
								<col width="15%" />
								<col width="35%" />
							</colgroup>
							<tr>
								<th>구성</th>
								<td class="line" colspan="3">
									<input type="text" class="txt" style="width:95%" name="component" value="<?=$rs_component?>" />
								</td>
							</tr>
							<tr>
								<th>용도 및 특징 : 제목</th>
								<td class="line" colspan="3">
									<input type="text" class="txt" style="width:95%" name="description_title" value="<?=$rs_description_title?>" />
								</td>
							</tr>
							<tr>
								<th>용도 및 특징 : 내용</th>
								<td class="line" colspan="3">
									<textarea name="description_body" style="padding-left:0px;width:100%;height:100px;"><?=$rs_description_body?></textarea>
								</td>
							</tr>
							<tr>
								<th>원산지</th>
								<td class="line" colspan="3">
									<input type="text" class="txt" style="width:95%" name="origin" value="<?=$rs_origin?>" />
								</td>
							</tr>
						</table>
					</div>
					
				</div>
				
				<div class="sp15"></div>
				
				<div>
					<? 
						if($goods_no <> "")
							echo "최초 등록자 : ".getAdminName($conn, $rs_reg_adm).", 등록일시 : ".$rs_reg_date;
						
					?>
					
				</div>
				<div class="sp5"></div>
				<div class="btnright">

					<? if ($adm_no <> "" ) {?>
						<? if ($sPageRight_U == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a> 
						<? } ?>
					<? } else {?>
						<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a> 
						<? } ?>
					<? }?>
					<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					<? if ($adm_no <> "") {?>
						<? if ($sPageRight_D == "Y") {?>
					<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
						<? } ?>
					<? } ?>
				</div>

				
				<div class="sp50"></div>
				
    </td>
  </tr>
  </table>
</div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
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

js_calculate_buy_and_sale_price();
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