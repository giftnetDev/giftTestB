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
	$menu_right = "GD002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/admin/admin.php";


?>
<?
	function insertGoodsInfo($db, $goodsNo, $imgUrl, $filePath150, $fileRnm150){

		if($imgUrl=="") return;
		if($filePath150=="") return ;
		if($fileRnm150=="") return ;
		$query="UPDATE TBL_GOODS 
				SET
					IMG_URL = '".$imgUrl."',
					FILE_PATH_150 = '".$filePath150."',
					FILE_RNM_150 =	'".$fileRnm150."'

				WHERE GOODS_NO = '".$goodsNo."' ; ";
		// echo $query."<BR>";
		// exit;

		$result=mysql_query($query, $db);
	}
	function insertGoodsImg($db, $goodsNo, $fileNm100, $fileRnm100, $fileSize100, $fileExt100){

		/**
		 * file_nm_100
		 * file_rnm_100
		 * file_size_100
		 * file_ext_100
		 */
		$query = "	UPDATE TBL_GOODS
					SET FILE_NM_100 = '".$fileNm100."',
						FILE_RNM_100='".$fileRnm100."',
						FILE_SIZE_100='".$fileSize100."',
						FILE_EXT_100='".$fileExt100."'
					WHERE GOODS_NO = '".$goodsNo."'
					; ";
		
		// echo $query,"<br>";
		// exit;
		$result= mysql_query($query, $db);
	}
	// upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png') ->네시.........
	function uploadEx($fileArray, $targetDir, $goodsCode, $maxSize = 1 /* MByte */, $allowExt) {


		$maxSize = $maxSize * 1024 * 1024 * 1;    // 바이트로 계산한다. 1MB = 1024KB = 1048576Byte   
		
		if(!file_exists($targetDir)){
			mkdir($targetDir, 0777);
		}
		if($fileArray['size'] > $maxSize){
			return false;
		}
		else{
			$fileExt = end(explode('.',$fileArray['name'])); //확장자

			$fileRealName=str_replace(".".$fileExt,"",$fileArray['name']); //확장자를 뗀 파일이름

			if($fileRealName=="") return ;

			if(in_array(strtolower($fileExt),$allowExt)){ //확장자 검사코드

				$goodsCode=str_replace("-", "_", $goodsCode);
				
				$tempFileName=$goodsCode.".".$fileExt;

				// $tmpFileInfo=$targetDir."/".$tempFileName;





				$fileName = get_filename_check($targetDir, $tempFileName);

				$path = $targetDir.$fileName;

				// echo $path."<br>";
				// exit;




				if(move_uploaded_file($fileArray['tmp_name'], $path)){
					//move_uploaded_file()->올라온 파일을 서버의 지정된 디렉토리에 위치시키는 함수
					return $fileName;
				}
				else{
					return false;
				}
			}//end of if(in_array()....)
			else{
				if($fileExt!=""){
	 				?>
	 				<script langauge="javascript"> 
	 					alert('등록할수 없는 확장자 입니다.'); 
	 					history.back(); 
	 				</script> 
	 				<?
					die;
				}
			}
		}
	}
?>
<?

	// 공급 업체 인경우
	//	$con_cate_03 = $s_adm_com_code;

#====================================================================
# DML Process
#====================================================================
	$goods_name			= SetStringToDB($goods_name);
	$goods_sub_name	= SetStringToDB($goods_sub_name);

#====================================================================
	$savedir1 = $g_physical_path."upload_data/goods";
	$fileDir= $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/";
#====================================================================
		
	//echo $mode."<br>";
	//echo $goods_no;

	

	if ($mode == "U") {

	// print_r($_POST);
	// exit;
		
	

	insertGoodsInfo($conn, $goods_no, $img_url, $rs_file_path_150, $rs_file_rnm_150);

		if($stock_tf)
			$stock_tf = 'Y';
		else
			$stock_tf = 'N';
		//function uploadEx($fileArray, $targetDir, $goodsCode, $maxSize = 1 /* MByte */, $allowExt)
		uploadEx($_FILES[urlDetail], $fileDir, $rs_goods_code,20, array('gif','jpeg','jpg','png'));

		# file업로드
		switch ($flag01) {
			
			case "insert" :
				// echo "<script>alert('insert');</script>";
				// exit;

				$file_nm_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
				$file_rnm_100		= $_FILES[file_nm_100][name];

				$file_size_100	= $_FILES[file_nm_100]['size'];
				$file_ext_100		= end(explode('.', $_FILES[file_nm_100]['name']));

				// function insertGoodsImg($db, $goodsNo, $fileNm100, $fileRnm100, $fileSize100, $fileExt100
				insertGoodsImg($conn, $goods_no, $file_nm_100, $file_rnm_100, $file_size_100, $file_ext_100);

			break;
			case "keep" :

				// echo "<script>alert('keep');</script>";
				// echo "keep";
				// exit;

				$file_nm_100		= $old_file_nm_100;
				$file_rnm_100		= $old_file_rnm_100;

				$file_size_100	= $old_file_size_100;
				$file_ext_100		= $old_file_ext_100;

			break;
			case "delete" :
				// echo "<script>alert('delete');</script>";
				// echo "delete<br>";
				// exit;


				$file_nm_100		= "";
				$file_rnm_100		= "";

				$file_size_100	= "";
				$file_ext_100		= "";
				insertGoodsImg($conn, $goods_no, $file_nm_100, $file_rnm_100, $file_size_100, $file_ext_100);

			break;
			case "update" :
				// echo "<script>alert('update');</script>";
				// echo "update<br>";
				// exit;


				$file_nm_100		= upload($_FILES[file_nm_100], $savedir1, 10 , array('gif', 'jpeg', 'jpg','png'));
				$file_rnm_100		= $_FILES[file_nm_100][name];

				$file_size_100 = $_FILES[file_nm_100]['size'];
				$file_ext_100  = end(explode('.', $_FILES[file_nm_100]['name']));
				insertGoodsImg($conn, $goods_no, $file_nm_100, $file_rnm_100, $file_size_100, $file_ext_100);

			break;
		}




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

		
	
	?>
<script type="text/javascript">
	document.location = "goods_list_limited.php";
</script>
<?
//		$mode			= "S";
//		$goods_no	= $new_goods_no;

		exit;
	}
	if($mode=="DELETE_DETAIL"){
		$goodsCode=str_replace("-", "_", $rs_goods_code);
		// str_replace()
		// echo "goods_code : $goodsCode<br>";
		// exit;

		$fileInfo=$fileDir.$goodsCode.".jpg";
		// echo "File_Info : $fileInfo<br>";
		// exit;

		unlink($fileInfo);
		$mode="S";
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
		$rs_stock_tf		    = trim($arr_rs[0]["STOCK_TF"]); 
		
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
		$rs_exposure_tf			= trim($arr_rs[0]["EXPOSURE_TF"]);

		$CURRENT_REASEON = trim($arr_rs[0]["REASON"]); 

		if($rs_reg_date != "0000-00-00 00:00:00")
			$rs_reg_date = date("Y-m-d H:i",strtotime($rs_reg_date));
		else
			$rs_reg_date = "";

		if($rs_restock_date != "0000-00-00 00:00:00")
			$rs_restock_date = date("Y-m-d",strtotime($rs_restock_date));
		else
			$rs_restock_date = "";

		$arr_rs_file = selectGoodsFile($conn, $goods_no);

		//사용안함
		//$arr_rs_option = selectGoodsOption($conn, $goods_no);

		$arr_rs_price = listGoodsPriceUpdate($conn, $goods_no, '');

		$arr_rs_other_price = listGoodsPriceBySaleCompany($conn, $goods_no);

		$arr_rs_goods_sub = selectGoodsSub($conn, $goods_no);

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

		
		$rs_goods_price_company = selectGoodsPriceChangeDistinctCPNo($conn, $goods_no);

	}


	$strParam = "?nPage=".$nPage."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&start_price=".$start_price."&end_price=".$end_price."&con_cate=".$con_cate;
	$strParam = $strParam."&con_cate_01=".$con_cate_01."&con_cate_02=".$con_cate_02."&con_cate_03=".$con_cate_03."&con_cate_04=".$con_cate_04."&con_tax_tf=".$con_tax_tf;
	$strParam = $strParam."&chk_vendor=".$chk_vendor."&txt_vendor_calc=".$txt_vendor_calc."&view_type=".$view_type."&con_exclude_category=".$con_exclude_category;
	$strParam = $strParam."&chk_next_sale_price=".$chk_next_sale_price;

	echo "result : $result<br>";


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
<style>
	.wordbreak{
		word-wrap:break-word;
	}
</style>
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

	function modifyReason(seq_no){
		//현재 메모
		var current_reason = $("#pre_reason_"+seq_no).children("a").html();

		//prompt 띄워서 메모 내용 받음
		var new_reason = prompt( '가격 변경 사유를 작성해주십시오', current_reason );
		
		if(current_reason != new_reason && new_reason != null){
			//ajax로 seq_no의 내용을 수정함
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'text',
				type: 'post',
				data : {
					'mode': "UPDATE_REASON",
					'seq_no': seq_no,
					'reason': new_reason
				},
				success: function(response) {
					if(response == "true"){
						// 메모 변경
						if(new_reason==""){
							//추가 표시
							$("#pre_reason_"+seq_no).children("a").html("추가");
						} else {
							//메모 내용 표시
							$("#pre_reason_"+seq_no).children("a").html(new_reason);
						}
						alert("성공하였습니다.");
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText);
					alert("통신 실패");
				}
			});//ajax
		} else {
			return;
		}
	}

	function modifyCurrentReason(goods_no){
		//현재 메모
		var current_reason = $("#reason_"+goods_no).children("a").html();

		//prompt 띄워서 메모 내용 받음
		var new_reason = prompt( '가격 변경 사유를 작성해주십시오', current_reason );
		
		if(current_reason != new_reason && new_reason != null){
			//ajax로 seq_no의 내용을 수정함
			$.ajax({
				url: '/manager/ajax_processing.php',
				dataType: 'text',
				type: 'post',
				data : {
					'mode': "UPDATE_CURRENT_REASON",
					'goods_no': goods_no,
					'reason': new_reason
				},
				success: function(response) {
					if(response == "true"){
						// 메모 변경
						if(new_reason==""){
							//추가 표시
							$("#reason_"+goods_no).children("a").html("추가");
						} else {
							//메모 내용 표시
							$("#reason_"+goods_no).children("a").html(new_reason);
						}
						alert("성공하였습니다.");
					} else{
						alert("실패하였습니다.");
					}
				}, error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText);
					alert("통신 실패");
				}
			});//ajax
		} else {
			return;
		}
	}
</script>
<script type="text/javascript">

	function js_list() {
		//var frm = document.frm;
			
		//frm.method = "get";
		//frm.action = "goods_list.php<?=$strParam?>";
		//frm.submit();

		document.location.href = "goods_list.php<?=$strParam?>";
	}

	function js_reload() {
		location.reload();
	}



	//상품코드 중복체크를 위해서 Ajax 체크 & 함수 분리
	function js_save() {

		var frm = document.frm;
		var goods_no = "<?= $goods_no ?>";

			js_save_process();



	}

	function js_save_process()
	{
		var frm = document.frm;
		var goods_no = "<?= $goods_no ?>";
		

		if (isNull(goods_no)) {
			document.location.href="goods_list_limited.php";
		} else {
			frm.mode.value = "U";
			frm.goods_no.value = frm.goods_no.value;
		}

		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}


	function js_fill_goods_code(new_code) {
		var frm = document.frm;
		frm.goods_code.value = new_code;
	}


	function js_price_change_by_company() {
		var frm = document.frm;

		frm.mode.value = "S";
		
		frm.method = "get";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_goods_view(goods_no) {

		window.open(
		  "/manager/goods/goods_write.php?mode=S&goods_no=" + goods_no,
		  '_blank' 
		);
		
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
	function js_delete_detail(){
		var frm = document.frm;
		frm.mode.value="DELETE_DETAIL";
		frm.action="<?=$_SERVER[PHP_SELF]?>";
		frm.target="";
		frm.method="POST";
		frm.submit();

	}


</script>

<script type="text/javascript">

	// tag 관련 레이어가 다 로드 되기전인지 판별하기 위해 필요
	var tag_flag = "0";

	var checkFirst = false;
	var lastKeyword = '';
	var loopSendKeyword = false;
	
	function startSuggest() {

		if ((event.keyCode == 8) || (event.keyCode == 46)) {
			checkFirst = false;
			loopSendKeyword = false;
		}

		if (checkFirst == false) {
			setTimeout("sendKeyword();", 100);
			loopSendKeyword = true;
		}
		checkFirst = true;
	}

	function sendKeyword() {
		
		var frm = document.frm;

		if (loopSendKeyword == false) return;

		var keyword = document.frm.search_name.value;
		
		if (keyword == '') {
			
			lastKeyword = '';
		
			hide('suggest');

		} else if (keyword != lastKeyword) {

			lastKeyword = keyword;
				
			if (keyword != '') {
				frm.keyword.value = keyword;
				frm.exclude_category.value = "14"; //세트품은 검색 안되도록
				frm.goods_type.value = "unit";
				frm.action = "/manager/goods/search_goods.php";
				frm.target = "ifr_hidden";
				frm.submit();
				
			} else {
				hide('suggest');
			}
		}
		setTimeout("sendKeyword();", 100);
	}


	function show(elementId) {
		var element = document.getElementById(elementId);
		
		if (element) {
			element.style.visibility  ="visible"; 
			//element.style.display = '';
		}
	}

	function hide(elementId) {
		var element = document.getElementById(elementId);
		if (element) {
			element.style.visibility  ="hidden"; 
			//element.style.display = 'none';
		}
	}

	function js_register_proposal(goods_proposal_no, goods_no){
		var url = "pop_goods_proposal.php?mode=S&goods_proposal_no="+goods_proposal_no+"&goods_no=" + goods_no;
		NewWindow(url,'pop_detail','830','600','Yes');
	};

	function js_view_buy_company(goods_no) { 
		var url = "pop_goods_buy_company.php?goods_no=" + goods_no;
		NewWindow(url,'pop_goods_buy_company','830','600','Yes');
	}

	function js_open_category(goods_no) { 
		var url = "pop_goods_category.php?has_sub_cate=N&goods_no=" + goods_no;
		NewWindow(url,'pop_goods_category','830','600','Yes');
	}

	function js_goods_price_change(seq_no) { 
		var url = "pop_goods_price_change_detail.php?seq_no="+seq_no+ "&kind=history";
		NewWindow(url,'pop_goods_price_change_detail','830','600','Yes');
	}

	function js_goods_price_write(cp_no) {

		if(cp_no != "")
			frm.sel_pc_company.value = cp_no;

		var url = "pop_goods_price_write.php?goods_no="+frm.goods_no.value+"&cp_type=" + cp_no;
		NewWindow(url,'pop_goods_price_write','830','600','Yes');
	}

$(function(){
	$('body').on('click', '.remove_sub', function() {
		$(this).closest("tr").remove();
		js_calculate_buy_and_sale_price();
	});

	$(".calc").blur(function(){

		var withcomma = $(this).val();
		$(this).val(withcomma.replaceall(',',''));
	
		js_calculate_buy_and_sale_price();

	});
});

$(document).ready(function(){

});

</script>
<style>
	.row_deleted {background-color:#dfdfdf; }
	.row_deleted > td{color:#fff !important;}
	.row_deleted > td > a{color:#fff !important;}
</style>
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
<input type="hidden" name="con_exclude_category" value="<?=$con_exclude_category?>">
<input type="hidden" name="chk_next_sale_price" value="<?=$chk_next_sale_price?>">
<input type="hidden" name="chk_vendor" value="<?=$chk_vendor?>">
<input type="hidden" name="txt_vendor_calc" value="<?=$txt_vendor_calc?>">
<input type="hidden" name="view_type" value="<?=$view_type?>">
<input type="hidden" name="rs_goods_code" value="<?=$rs_goods_code?>">

<input type="hidden" name="keyword" value="">
<input type="hidden" name="goods_type" value="">
<input type="hidden" name="exclude_category" value="">
<input type="hidden" name="seq_no" value="">
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
			
			<h2>상품 관리</h2>

				<table cellpadding="0" cellspacing="0" class="colstable02">
				<colgroup>
					<col width="15%" />
					<col width="35%" />
					<col width="15%" />
					<col width="35%" />
				</colgroup>
				<thead>

				</thead>
				<tbody>
					<tr>
						<th>상품명</th>
						<td colspan="3" class="line">
							<?=$rs_goods_name?>
						</td>
					</tr>
					<tr class="set_goods" style="display:none;">
						<th>구성상품 등록</th>
							<td colspan="3" style="position:relative" class="line">
								<div id="suggest" style="position:absolute; left:15px; top:25px; width:95%; height:81px; z-index:1; visibility: hidden; width:95%; ">
									<div id="suggestList" style=" height:600px; overflow-y:auto; position:relative; border:solid 1px #cec6ba; background:#FFFFFF; width:95%;"></div>
								</div>
								<input type="text" class="txt search_name" style="width:95%; ime-mode:Active;" autocomplete="off" name="search_name" value="" onkeyup="startSuggest();" onFocus="this.value='';" placeholder="검색하실 상품을 입력 후 잠시 기다려주세요." />

								<table cellpadding="0" cellspacing="0" class="colstable02" style="margin-top:5px;">
								<colgroup>
									<col width="9%" />
									<col width="*" />
									<col width="9%" />
									<col width="8%" />
									<col width="9%" />
									<col width="15%" />
									<col width="5%" />
								</colgroup>
								<thead>
									<tr>
										<th colspan="7" class="line">상품을 검색해서 선택하시면 아래에 구성 상품이 추가됩니다</th>
									</tr>
								</thead>
								<tbody class="sub_goods_list">
								</tbody>
								</table>
							</td>
					</tr>


					<tr>
						<th></th>
						<td class="line">
						</td>
						<th>상품코드</th>
						<td class="line">
							<?=$rs_goods_code?>
						</td>
					</tr>
					

					<tr>
						<?if( sizeof($arr_rs_goods_sub)>0){ ?>
							<th>구성상품</th><?}?>
						<td class="line" colspan="3">
						<?
							if(sizeof($arr_rs_goods_sub)>0){
								$cnt=sizeof($arr_rs_goods_sub);
								?>	
									<table>
									<thead>
										<td style="text-align:center; padding : 5px;">상품코드</td>
										<td style="text-align:center padding : 5px;">상품명</td>
										<td style="text-align:center padding : 5px;">갯수</td>
									</thead>
									<tbody>

								<?
								for($i=0; $i<$cnt; $i++){
									$subGoodsName=$arr_rs_goods_sub[$i]["GOODS_NAME"];
									$subGoodsCode=$arr_rs_goods_sub[$i]["GOODS_CODE"];
									$subGoodsCnt =$arr_rs_goods_sub[$i]["GOODS_CNT"];
								?>
									<tr>
										<td style="text-align:center; padding : 5px"><?=$subGoodsCode?></td>
										<td style="text-align:center; padding : 5px"><?=$subGoodsName?></td>
										<td style="text-align:center; padding : 5px"><?=$subGoodsCnt?></td>
									</tr>
								<?
								}
								?>
								</tbody>
							</table>
							<?
							}
						?>
						</td>
						
					</tr>

				</table>

				<div class="sp20"></div>

				<div id="tabs" style="width:95%; margin:10px 0;">
					<ul>
						<!-- <li><a href="#tabs-1">가격 정보</a></li> -->
						<li><a href="#tabs-2">상품 이미지</a></li>

					</ul>
					

					
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
									<input type="text" class="txt" style="width:60%" name="file_path_150" value="<?=$rs_file_path_150?>" />&nbsp;
									<input type="text" class="txt" style="width:30%" name="file_rnm_150" value="<?=$rs_file_rnm_150?>" />
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
								<th>상품 이미지(상세) <br>
									<!--<a href="javascript:js_add();"><img src="../images/sbtn_add.gif" alt="추가"></a>--></th>
								
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
								<!-- <th>상품 상세</th> -->
								<td colspan="3" class="subject line">
									<input type="file" name="urlDetail" class="txt"> &nbsp; &nbsp;
									<input type="button" name="deleteDetail" onclick="js_delete_detail()" value="상세사진 삭제">
									<?
									//startsWith($rs_goods_cate, "14") && 

										
										$file_path = $_SERVER[DOCUMENT_ROOT]."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg";
										echo "<img src='/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg'/>";
										

									
										// if(file_exists($file_path)){
										// 	echo "이미지 URL : <a href='https://".$_SERVER['HTTP_HOST']."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg' target='_blank'>https://".$_SERVER['HTTP_HOST']."/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg </a>";
										// 	echo "<br/><br/>";
										// 	echo "<img src='/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg'/>";
										// } else { 
										// 	echo "[기대] FTP 업로드위치와 파일명 : /www/upload_data/goods_image/detail/".str_replace("-","_",$rs_goods_code).".jpg<br></br>";
										// }
									?>
									

										
									<!-- <span class="fl" style="padding-left:0px;width:100%;height:500px;"><textarea name="contents" id="contents"  style="padding-left:0px;width:100%;height:400px;"><?=$rs_contents?></textarea></span> -->
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
						<a href="javascript:js_save();"><img src="../images/save.png" alt="확인" /></a> 
						<? } ?>
					<? } else {?>
						<? if ($sPageRight_I == "Y") {?>
						<a href="javascript:js_save();"><img src="../images/save.png" alt="확인" /></a> 
						<? } ?>
					<? }?>
					<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
					
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

// var oEditors = [];
// 	nhn.husky.EZCreator.createInIFrame({
// 	oAppRef: oEditors,
// 	elPlaceHolder: "contents",
// 	sSkinURI: "../../_common/SE2.1.1.8141/SmartEditor2Skin.html",
// 	htParams : {
// 		bUseToolbar : true, 
// 		fOnBeforeUnload : function(){ 
// 			// alert('야') 
// 		},
// 		fOnAppLoad : function(){ 
// 		// 이 부분에서 FOCUS를 실행해주면 됩니다. 
// 		this.oApp.exec("EVENT_EDITING_AREA_KEYDOWN", []); 
// 		this.oApp.setIR(""); 
// 		//oEditors.getById["ir1"].exec("SET_IR", [""]); 
// 		}
// 	}, 
// 	fCreator: "createSEditor2"
// });


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