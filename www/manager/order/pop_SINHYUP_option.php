<?session_start();?>
<?
//header("Pragma;no-cache");
//header("Cache-Control;no-cache,must-revalidate");

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD005"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/cart/cart.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# REQUEST_VALUABLE
#====================================================================

	$goodsNo=$_REQUEST['goodsNo'];
	$seq=$_REQUEST['seq'];

	// echo "goodsNo : ".$goodsNo."<br>";
	// echo "seq : ".$seq."<br>";



#====================================================================
# LOCAL_FUNCTIONS
#====================================================================

	function getGoodsInfoByGoodsNo($db, $goodsNo, $seq){
		$query = "SELECT 	G.GOODS_NAME, C.CP_NO, C.CP_NM,  G.FILE_PATH_150, G.FILE_RNM_150, G.TAX_TF, G.DELIVERY_CNT_IN_BOX,
							TSO.OPT_SALE_TYPE, TSO.OPT_STICKER_NO, TSO.OPT_WRAP_NO, TSO.OPT_MEMO, TSO.OPT_REQUEST_MEMO, TSO.OPT_SUPPORT_MEMO,
							TSO.OPT_STICKER_MSG, TSO.OPT_PRINT_MSG,
							TSO.OPT_OUTSTOCK_DATE ,TSO.DELIVERY_TYPE ,TSO.DELIVERY_CP ,TSO.SA_DELIVERY_PRICE ,TSO.SENDER_NM ,TSO.SENDER_PHONE ,TSO.BULK_TF
					FROM	TBL_TEMP_SINHYUP_ORDER TSO
					JOIN	TBL_GOODS	G	ON TSO.GOODS_NO=G.GOODS_NO
					JOIN	TBL_COMPANY	C	ON G.CATE_03=C.CP_NO
					WHERE 	TSO.SEQ = '".$seq."'
					AND 	G.GOODS_NO = '".$goodsNo."'
				";

		// echo $query."<br>";
		// exit;

		$result=mysql_query($query, $db);

		if(!$result){
			echo "<script>alert('func.getGoodsInfoByGoodsNo() ERROR!');</script>";
			exit;
		}
		$record=array();
		$cnt=0;
		$cnt=mysql_num_rows($result);
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
			}
		}
		return $record;
		

		//CATE_03 :  공급사_CP_NO

	}

	// function getGoodsInfoByGoodsNo($db, $goodsNo){
	// 	$query = "SELECT G.GOODS_NAME, C.CP_NO, C.CP_NM,  G.FILE_PATH_150, G.FILE_RNM_150, G.TAX_TF, G.DELIVERY_CNT_IN_BOX,
	// 						TSO.OPT_SALE_TYPE, TSO.OPT_STICKER_NO, TSO.OPT_WRAP_NO, TSO.OPT_MEMO, TSO.OPT_REQUEST_MEMO, TSO.OPT_SUPPORT_MEMO,
	// 						TSO.OPT_STICKER_MSG, TSO.OPT_PRINT_MSG,
	// 						TSO.OPT_OUTSTOCK_DATE ,TSO.DELIVERY_TYPE ,TSO.DELIVERY_CP ,TSO.SA_DELIVERY_PRICE ,TSO.SENDER_NM ,TSO.SENDER_PHONE ,TSO.BULK_TF
	// 				FROM TBL_GOODS G
	// 				JOIN TBL_COMPANY C ON G.CATE_03=C.CP_NO 
	// 				JOIN TBL_TEMP_SINHYUP_ORDER TSO ON G.GOODS_NO=TSO.GOODS_NO
	// 			WHERE G.GOODS_NO = '".$goodsNo."'
	// 			";

	// 	// echo $query."<br>";
	// 	// exit;

	// 	$result=mysql_query($query, $db);

	// 	if(!$result){
	// 		echo "<script>alert('func.getGoodsInfoByGoodsNo() ERROR!');</script>";
	// 		exit;
	// 	}
	// 	$record=array();
	// 	$cnt=0;
	// 	$cnt=mysql_num_rows($result);
	// 	if($cnt>0){
	// 		for($i=0; $i<$cnt; $i++){
	// 			$record[$i]=mysql_fetch_assoc($result);
	// 		}
	// 	}
	// 	return $record;
		

	// 	//CATE_03 :  공급사_CP_NO

	// }



	function getSINHYUPOrderOptionAndMemo($db, $SINHYUPOrderNo){
		$query ="SELECT TEMP_OPTION, TEMP_MEMO
				 FROM	TBL_TEMP_SINHYUP_ORDER
				 WHERE	SEQ='".$SINHYUPOrderNo."'
				 ";

		// echo $query."<br>";
		// exit;
		
		$result=mysql_query($query, $db);

		if($result){
			$rows = mysql_fetch_row($result);
			// $rows[0]="TEMP_OPTION", $rows[1]="TEMP_MEMO"
			return $rows;
		}
		else{
			echo "<script>alert('func.getSINHYUPOrderOptionAndMemo() ERROR');</script>";
			exit;
		}

	}

	function getStickerSINHYUPWithString($db, $str){
		// $query="";

		$result=mysql_query($query, $db);
	}


	function selectStickerSINHYUP($db, $selectID, $selected){
		$query="SELECT	GOODS_NO, GOODS_NAME, GOODS_SUB_NAME, FILE_NM_100
				FROM 	TBL_GOODS
				WHERE 	DEL_TF='N'
				AND 	USE_TF='Y'
				AND 	GOODS_CATE = '010316'
				";
		$result=mysql_query($query, $db);
		$record=array();
		$cnt=0;
		if($result){
			$cnt=mysql_num_rows($result);
		}
		else{
			echo "<script>alert('func.selectStickerSINHYUP() ERROR!');</script>";
			exit;
		}
		// echo "cnt : ".$cnt."<br>";
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
				$record[$i]['GOODS_NAME']=trim(SetStringFromDB($record[$i]['GOODS_NAME']));
			}
		}
		else{
			return "";
		}
		$selectBox="<SELECT name='".$selectID."'><OPTION value='0'>스티커 선택</OPTION>";
		for($i=0; $i<$cnt; $i++){
			$selectBox.="<OPTION value='".$record[$i]['GOODS_NO']."' data-imgPath='".$record[$i]['FILE_NM_100']."' ";
			if($selected>0){
				if($selected==$record[$i]['GOODS_NO']){
					$selectBox.=" selected ";
				}
			}
			$selectBox.=" >".$record[$i]['GOODS_NAME']."</OPTION>";
		}
		$selectBox.="</SELECT>";


		return $selectBox;
		
	}

	function selectWrapSINHYUP($db, $selectID, $selected){

		$query="SELECT	GOODS_NO, GOODS_NAME, GOODS_SUB_NAME, FILE_NM_100
				FROM TBL_GOODS
				WHERE DEL_TF='N'
				AND USE_TF='Y'
				AND GOODS_CATE = '010204'
				";
		$result=mysql_query($query, $db);
		$record=array();
		$cnt=0;
		if($result){
			$cnt=mysql_num_rows($result);
		}
		else{
			echo "<script>alert('func.selectWrapSINHYUP() ERROR!');</script>";
			exit;
		}
		// echo "cnt : ".$cnt."<br>";
		if($cnt>0){
			for($i=0; $i<$cnt; $i++){
				$record[$i]=mysql_fetch_assoc($result);
				$record[$i]['GOODS_NAME']=trim(SetStringFromDB($record[$i]['GOODS_NAME']));
			}
		}
		else{
			return "";
		}
		$selectBox="<SELECT id='".$selectID."'><OPTION>포장지 선택</OPTION>";
		for($i=0; $i<$cnt; $i++){
			$selectBox.="<OPTION value='".$record[$i]['GOODS_NO']."' data-imgPath='".$record[$i]['FILE_NM_100']."'";
			if($selected>0){
				if($record[$i]['GOODS_NO']==$selected){
					$selectedBox.=" selected ";
				}
			}
			$selectBox.=">".$record[$i]['GOODS_NAME']."</OPTION>";
		}
		$selectBox.="</SELECT>";


		return $selectBox;
		
	}

	function updateSINHYUPOption($db, $options, $SINHYUPOrderNo){

		echo "bulkTF=".$options['BULK_TF']."<br>";


		if($options['BULK_TF']==''){
			$options['BULK_TF']='N';
		}
		if($options['OPT_STICKER_NO']==''){
			$options['OPT_STIKER_NO']=0;
		}
		if($options['CP_ORDER_NO']==''){
			$options['CP_ORDER_NO']=0;
		}
		if($options['OPT_WRAP_NO']==''){
			$options['OPT_WRAP_NO']=0;
		}
		if($options['OPT_SALE_TYPE']==''){
			$options['OPT_SALE_TYPE']='';
		}
		if($options['OPT_OUTBOX_TF']==''){
			$options['OPT_OUTBOX_TF']='N';
		}


		$query="UPDATE TBL_TEMP_ORDER_MRO_CONVERSION
				SET 
					OPT_WRAP_NO			='".$options['OPT_WRAP_NO']."',
					OPT_STICKER_MSG		='".$options['OPT_STICKER_MSG']."',
					OPT_PRINT_MSG		='".$options['OPT_PRINT_MSG']."',
					OPT_OUTBOX_TF		='".$options['OPT_OUTBOX_TF']."',
					CP_ORDER_NO			='".$options['CP_ORDER_NO']."',
					OPT_MEMO			='".$options['OPT_MEMO']."',
					OPT_REQUEST_MEMO	='".$options['OPT_REQUEST_MEMO']."',
					OPT_SUPPORT_MEMO	='".$options['OPT_SUPPORT_MEMO']."',
					OPT_OUTSTOCK_DATE	='".$options['OPT_OUTSTOCK_DATE']."',
					DELIVERY_TYPE		='".$options['DELIVERY_TYPE']."',
					SA_DELIVERY_PRICE	='".$options['SA_DELIVERY_PRICE']."',
					SENDER_NM			='".$options['SENDER_NM']."',
					SENDER_PHONE		='".$options['SENDER_PHONE']."',
					BULK_TF				='".$options['BULK_TF']."',
					OPT_SALE_TYPE		='".$options['OPT_SALE_TYPE']."'
				
				WHERE SEQ='".$SINHYUPOrderNo."'
				";

		
		// echo $query."<br>";
		// exit;

		$result=mysql_query($query, $db);
		if(!$result){
			echo "<script>alert('func.updateSINHYUP_OPTION error()');</script>";
			exit;
		}
	}



//end of functions?>
<?//start of modes

	$SINHYUPInfo=getSINHYUPOrderOptionAndMemo($conn, $seq);
	// $SINHYUPInfo[0] : TEMP_OPTION
	// $INHUYUPInfo[1] : TEMP_MEMO
	$TEMP_OPTION	=trim(SetStringFromDB($SINHYUPInfo[0]));
	$TEMP_MEMO		=trim(SetStringFromDB($SINHYUPInfo[1]));

	$arr_goods_rs=getGoodsInfoByGoodsNo($conn, $goodsNo, $seq);

	//G.GOODS_NAME, C.CP_NM,  G.FILE_PATH_150, G.FILE_RNM_150, G.TAX_TF, G.DELIVERY_CNT_IN_BOX
	// TSO.OPT_STICKER_NO, TSO.OPT_WRAP_NO, TSO.OPT_MEMO, TSO.OPT_REQUEST_MEMO, TSO.OPT_SUPPORT_MEMO,
	// TSO.OPT_STICKER_MSG, TSO.OPT_PRINT_MSG
	// TSO.OPT_OUTSTOCK_DATE
	// TSO.DELIVERY_TYPE
	// TSO.DELIVERY_CP
	// TSO.SA_DELIVERY_PRICE
	// TSO.SENDER_NM
	// TSO.SENDER_PHONE
	// TSO.BULK_TF

	$goodsName			=	$arr_goods_rs[0]["GOODS_NAME"];
	$CPName				=	$arr_goods_rs[0]["CP_NM"];
	$filePath			=	$arr_goods_rs[0]["FILE_PATH_150"];
	$fileName			=	$arr_goods_rs[0]["FILE_RNM_150"];
	$taxTF				=	$arr_goods_rs[0]["TAX_TF"];
	$deliveryCntInBox	=	$arr_goods_rs[0]["DELIVERY_CNT_IN_BOX"];
	$optStickerNo		=	$arr_goods_rs[0]["OPT_STICKER_NO"];
	$optWrapNo			=	$arr_goods_rs[0]["OPT_WRAP_NO"];
	$optSaleType		=	$arr_goods_rs[0]["OPT_SALE_TYPE"];
	$optStickerMsg		=	$arr_goods_rs[0]["OPT_STICKER_MSG"];
	$optPrintMsg		=	$arr_goods_rs[0]["OPT_PRINT_MSG"];
	$optMemo			=	SetStringFromDB($arr_goods_rs[0]["OPT_MEMO"]);
	$optRequestMemo		=	SetStringFromDB($arr_goods_rs[0]["OPT_REQUEST_MEMO"]);
	$optSupportMemo		=	SetStringFromDB($arr_goods_rs[0]["OPT_SUPPORT_MEMO"]);
	$optOutStockDate	= 	$arr_goods_rs[0]["OPT_OUTSTOCK_DATE"];
	$deliveryType		=	$arr_goods_rs[0]["DELIVERY_TYPE"];
	$deliveryCP			=	$arr_goods_rs[0]["DELIVERY_CP"];
	$saDeliveryPrice	=	$arr_goods_rs[0]["SA_DELIVERY_PRICE"];
	$senderNm			=	$arr_goods_rs[0]["SENDER_NM"];
	$senderPhone		=	$arr_goods_rs[0]["SENDER_PHONE"];
	$bulkTF				=	$arr_goods_rs[0]["BULK_TF"];


	
	$img_url="";
	if($filePath<>""){
		$img_url.=$filePath;
	}
	else{
		$img_url.="/upload_data/goods_image/500/";
	}

	$img_url.=$fileName;

	// echo "IMG_URL : ".$img_url."<br>";





	if($mode=="INSERT_OPTION"){

		echo "----------POST_START-------<br>";
		print_r($_POST);
		echo "-----------POST_END--------<br>";
		
		exit;

		// echo "선택된 스티커는 : ".$sel_sticker."<br>";

		//function(){
		$options=array();
		$options['OPT_WRAP_NO']=		$sel_wrap;
		$options['OPT_STICKER_MSG']=	$opt_sticker_msg;
		$options['OPT_PRINT_MSG']=		$opt_print_msg;
		$options['OPT_OUTBOX_TF']=		$opt_outbox_tf;
		$options['CP_ORDER_NO']=		$cp_order_no;
		$options['OPT_MEMO']=			$opt_memo;
		$options['OPT_REQUEST_MEMO']=	$opt_request_memo;
		$options['OPT_SUPPORT_MEMO']=	$opt_support_memo;
		$options['OPT_OUTSTOCK_DATE']=	$opt_outstock_date;
		$options['DELIVERY_TYPE']=		$delivery_type;
		$options['DELIVERY_CP']	=		$delivery_cp;
		$options['SA_DELIVERY_PRICE']=	$sa_delivery_price;
		$options['SENDER_NM']=			$sender_nm;
		$options['SENDER_PHONE']=		$sender_phone;
		$options['BULK_TF']		=		$bulk_tf;
		$options['OPT_SALE_TYPE']	=	$cate_01;
	
		updateSINHYUPOption($conn, $options, $seq);

		echo "<script>self.close();</script>";
		

	}





?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
		<title><?=$g_title?></title>

		<script type="text/javascript" src="../js/common.js"></script>
		<script type="text/javascript" src="../js/board.js"></script>
		<script type="text/javascript" src="../js/goods_common.js"></script>
		<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
		<!-- <link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
		<link rel="stylesheet" href="../jquery/theme.css" type="text/css" /> -->
		<link rel="stylesheet" href="../css/newStyle/newERPStyle.css" type="text/css" />
		<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->

		<script>
			$(function(){

				$(".datepicker").datepicker({
					dateFormat:"yy-mm-dd",
					changeMonth:true,
					changeYear:true,
					minDate:0,
					numberOfMonths:2
				});


				$(".datepicker").keydown(function(){
				var value = $(this).val();

				if(value.length == 4 && value.lastIndexOf('-') == -1){
					$(this).val(value.substr(0,4)+"-"+value.substr(4));
				}
				if(value.length == 7 && value.lastIndexOf('-') ==4){
					$(this).val(value.substr(0,8)+"-"+value.substr(8));

				}

			});

			$(".datepicker").blur(function(){
				if($(this).val().length >0){
					checkDt($("input[name=opt_outstock_date]"));
				}
			});

			});


			function js_save_option(){
				//출고예정일에 대한 정보가 있거나 대량건/출고미지정 CHECK-BOX에 CHK가 되어있어야 진행할 수 있도록 한다.

				var bulkTF=$("input[name='bulk_tf']").is(":checked");
				var optOutStockDate=$("input[name='opt_outstock_date']").val();

				// alert("bulkTF : "+bulkTF+", optOutStockDate : "+optOutStockDate);
				if(bulkTF==false && optOutStockDate==""){
					alert('둘 중 하나는 check되어있어야 합니다');
					return;
				}

				var frm = document.frm;
				frm.mode.value="INSERT_OPTION";
				frm.action="<?=$_SERVER['PHP_SELF']?>";
				frm.target="";
				frm.submit();

			}

			function js_close_window(){
				self.close();
			}
		</script>
	</head>
	<body width="700px">
		<form name="frm" method="POST">
			<input type="hidden" name="mode">
			<div class="popFrame">
				<div class="dvTitle">
					<h2>신협 주문상품 옵션</h2>
				</div><!--dvTitle-->
				<div class="mainContent">
					<hr class="popLineTitle">
					<div class="subTitle">
						<h3>신협 옵션</h3>
					</div>
					<div class="dvDashboard">
						<table class="dashboardTable">
							<colgroup>
								<col width="35%">
								<col width="65%">
							</colgroup>
							<tr><th>옵션내용</th><td><?=$TEMP_OPTION?></td></tr>
							<tr><th>메모내용</th><td><?=$TEMP_MEMO?></td></tr>

						</table>
					</div><!--dvDashboard-->
					<div class="space20px"></div>

					<div class="subTitle">
						<h3>상품 정보</h3>
					</div>
					<div class="dvDashboard">
						<table class="dashboardTable">
							<colgroup>
								<col width="40%">
								<col width="20%">
								<col width="40%">
							</colgroup>
							<tr>
								<td style="padding: 5px 5px 5px 5px"  rowspan='5'>
									<img src="<?=$img_url?>" width="250" height="250">
								</td>
								<th>상품명</th>
								<td><?=$goodsName?></td>
							</tr>
							<tr>
								<th>주문상품종류</th>
								<td><?=makeSelectBox($conn, "ORDER_GOODS_TYPE", "cate_01", "100", "선택", "", $optSaleType)?></td>
								<script>
									$(function(){
										$("select[name=cate_01").change(function(){

											if($(this).val() == "추가") { 
												$("input[name=opt_outstock_date]").val('<?=date("Y-m-d", strtotime("15 day"))?>'); //출고예정일 +15일
												$("input[name=bulk_tf]").prop("checked", false); //출고미지정 해제
												$("select[name=delivery_type]").val("99"); //기타
												$("select[name=delivery_cp]").hide(); //택배회사 가리기
											}

										});
									});
								</script>

							</tr>
							<tr>
								<th>공급업체</th>
								<td><?=$CPName?></td>
							</tr>
							<tr>
								<th>과세여부</th>
								<td><?=$taxTF?></td>
							</tr>
							<tr>
								<th>박스입수</th>
								<td><?=$deliveryCntInBox?></td>
							</tr>

						</table><!--dashboardTable-->
					</div><!--dvDashboard-->
					<div class="space10px"></div>
					<div class="subTitle">
						<h3>작업 내용</h3>
					</div>

					<div class="dvDashboard">
						<table class="dashboardTable">
							<colgroup>
								<col width="120" />
								<col width="*"/>
								<col width="120" />
								<col width="*" />
							</colgroup>
							<tr>
								<th>포장지</th>
								<td class="line">
									<?=selectWrapSINHYUP($conn, "sel_wrap", $optWrapNo)?>
									<!-- <script>
										$('#sel_wrap').on("change",function(){
											var imgName=$(this).find("option:selected").data("imagpath");
											var imgPath="/upload_da"
										});
									</script> -->
								</td>
								<td rowspan="2" colspan="2" style="text-align:center;">
									<img name="sample_img" src="/manager/images/no_img.gif" style="max-height:200px; max-width:200px;"/>
								</td>
							</tr>
							<tr>
								
							</tr>
							<tr>
								<th>스티커<br>메세지</th>
								<td class="line" colspan="3"> 
									<input type="text" class="txt" style="width:90%" name="opt_sticker_msg" value="<?=$optStickerMsg?>"/>
								</td>
							</tr>
							<tr>
								<th>인쇄<br>(통장지갑등)</th>
								<td class="line" colspan="3">
										<input type="text" class="txt" style="width:90%" name="opt_print_msg" value="<?=$optPrintMsg?>"/>
								</td>
							</tr>
							<tr>
								<th>아웃박스<br>스티커</th>
								<td><input type="checkbox" name="opt_outbox_tf" value="Y"></td>
								<th>업체주문번호</th>
								<td class="line">
										<input type="text" class="txt" style="width:120px;" name="cp_order_no" value=""/>
								</td>
							</tr>
							<tr>
								<th>출고예정일</th>
								<?
									// $optOutStockDate=date_format($optOutStockDate,"y-m-d");
									if($optOutStockDate=="0000-00-00 00:00:00"){

										$week=date("w");
										if($week==5){//오늘이 금요일일 경우
											$optOutStockDate=date("Y-m-d",strtotime("+3 day"));	
										} 
										else if($week==6){//오늘이 토요일일 경우
											$optOutStockDate=date("Y-m-d",strtotime("+2 day"));
										}
										else{//그 외
											$optOutStockDate=date("Y-m-d",strtotime("+1 day"));
										}
										
									}
									else{
										$optOutStockDate=strtotime($optOutStockDate);
										$optOutStockDate=date("Y-m-d",$optOutStockDate);
										// $optOutStockDate=date_format($optOutStockDate,"Y-m-d");
										echo $optOutStockDate."<br>";
									}
																				

								?>
								<td class="line" colspan="3">
									<input type="text" class="txt datepicker" autocomplete="off" style="width: 80px; margin-right:3px;" name="opt_outstock_date" value="<?=$optOutStockDate?>" maxlength="10"/>
									&nbsp; <label><input type="checkbox"  name="bulk_tf" value="Y"/> 대량건/출고미지정</label>
									<script>
										$(function(){
											$("input[type=checkbox][name=bulk_tf]").click(function(){
												$("input[type=text][name=opt_outstock_date]").val('');
											});

											$("input[type=text][name=opt_outstock_date]").on('keydown, click',function(){
												$("input[type=checkbox][name=bulk_tf]").prop('checked', false);
											});
										});
									</script>
								</td>
								
							</tr>
							<tr>
								<th>작업메모<br>(창고)</th>
								<td colspan="3">
									<textarea name="opt_memo" style="width:98%; height:50px" class="txt"><?=$optMemo?></textarea>
								</td>
							</tr>
							<tr>
								<th>발주메모<br>(공급사)</th>
								<td colspan="3">
									<textarea name="opt_request_memo" style="width:98%; height:50px" class="txt"><?=$optRequestMemo?></textarea>
								</td>
							</tr>
							<tr>
								<th>운영메모<br>(지원)</th>
								<td colspan="3">
									<textarea name="opt_support_memo" style="width:98%; height:50px" class="txt"><?=$optSupportMemo?></textarea>
								</td>
							</tr>
						</table>
					</div><!--dvDashboard-->
					<div class="subTitle">
						<h3>배송 내용</h3>
					</div>
					<div class="dvDashboard">
						<table class="dashboardTable">
							<colgroup>
								<col width="120" />
								<col width="*" />
								<col width="120" />
								<col width="*" />
							</colgroup>
							<tbody>
								<tr>
									<th>배송방식</th>
									<td>
										<?
											if($deliveryType==""){

											}
											if($deliveryType==""){
												$deliveryType=0;
											}
											if($deliveryCP==""){
												$deliveryCP="롯데택배";
											}
										?>
										<?=makeSelectBox($conn, "DELIVERY_TYPE", "delivery_type", "100", "배송방법을 선택하세요","",$deliveryType)?>
										<?=makeSelectBox($conn, "DELIVERY_CP_OP", "delivery_cp",  "100", "택배회사를 선태갛세요", "", $deliveryCP)?>
										<script>
											$(function(){
												$("select[name=delivery_type]").change(function(){
													if($("select[name=delivery_type]").val() == "0" ||$("select[name=delivery_type]").val()=="3"){
														$("select[name=delivery_cp]").show();
													}
													else{
														$("select[name=delivery_cp]").hide();
													}
												});
											});
										</script>

									</td>
									<th>배송비<BR>(운반비차액)</th>
									<td>
										<input type="text" class="txt" style="width:105px" name="sa_delivery_price" value="0" required onkeyup="return isMathNumber(this)"/> 원
									</td>
								</tr>
								<tr>
									<?
										if($cp_no==5597){
											$CP_NM="CU몰 기프트넷";
											$CP_PHONE="031-527-6812";
										}
									?>
									<th>보내는사람</th>
									<td>
										<input type="Text" name="sender_nm" value="CU몰 기프트넷" style="width:70%;">
									</td>
									<th>보내는사람<BR>연락처</th>
									<td>
										<input type="Text" name="sender_phone" value="031-527-6812" style="width:160px;">
									</td>
								</tr>
							</tbody>
						</table><!--dashboardTable-->
					</div><!--dvDashboard-->
					<div class="space20px"></div>
					<div class="dvButtonRight">
						<input type="button" class="btnSpecial"  value="옵션저장" onclick="js_save_option()">
						<input type="button" class="btnNormal" value="취소" onclick="js_close_window()">
					</div>
					<div class="space30px"></div>
				</div><!--mainContnet-->

			</div><!--popFramae-->

		</form>
	<body>

</html>
<script type="text/javascript">
	window.onload=function() {
		resizeBoardImage('700');
	}

</script>
<?
		
	

#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);

	
?>
