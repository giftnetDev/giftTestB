<?session_start();?>
<?
# =============================================================================
# File Name    : goods_list.php
# =============================================================================

$file_name="��ǰ-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
header( "Content-Disposition: attachment; filename=$file_name" );

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/goods/goods.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-6 month"));;
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
	$del_tf = "N";
#============================================================
# Page process
#============================================================

	$nPageBlock	= 10;
	
	$nPage = 1;
	$nPageSize = 100000;

#===============================================================
# Get Search list count
#===============================================================
	$this_date = date("Ymd",strtotime("0 day"));

	if($chk_vendor != "Y" && $txt_vendor_calc == "100")
		$txt_vendor_calc = 100;

	$arr_options = array("exclude_category" => $con_exclude_category, "txt_vendor_calc" => $txt_vendor_calc);

	if(startsWith($print_type, "FOR_CATALOG")) { 
		$arr_rs = listGoodsWithPage($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);
	} else { 
		$arr_rs = listGoods($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_tax_tf, $con_use_tf, $del_tf, $search_field, $search_str, $arr_options, $order_field, $order_str, $nPage, $nPageSize);
		
		//��ǰ����Ʈ�� MRO�ǸŰ�(TBL_GOODS_PRICE.MRO_SALE_PRICE) �߰�
		if(sizeof($arr_rs)>0){
			for($i=0;$i<sizeof($arr_rs);$i++){
				$TEMP_GOODS_NO = trim($arr_rs[$i]["GOODS_NO"]);
				$arr_rs[$i]["MRO_SALE_PRICE"] = getMroSalePrice($conn, $TEMP_GOODS_NO);
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>
<!-- List -->
<? if(startsWith($print_type, "FOR_REG")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">��ǰ��ȣ</th>
		<th style="color:#ff0000;">��ǰī�װ�</th>
		<th style="color:#ff0000;">��ǰ��</th>
		<th style="color:#ff0000;">�𵨸�</th>
		<th style="color:#ff0000;">��ǰ�ڵ�</th>
		<th style="color:#ff0000;">����ǰ����</th>
		<th style="color:#ff0000;">�ڽ��Լ�</th>
		<th style="color:#ff0000;">�ּ����</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">���޻�</th>
		<th style="color:#ff0000;">�ǸŻ���</th>
		<th style="color:#ff0000;">���԰�</th>
		<th style="color:#ff0000;">��ƼĿ���</th>
		<th style="color:#ff0000;">�����μ���</th>
		<th style="color:#ff0000;">�ù� ��ۺ�</th>
		<th style="color:#ff0000;">�ΰǺ�</th>
		<th style="color:#ff0000;">��Ÿ���</th>
		<th style="color:#ff0000;">�ǸŰ�</th>
		<th style="color:#ff0000;">MRO�ǸŰ�</th>
		<th style="color:#ff0000;">�Ǹż�����</th>
		<th style="color:#ff0000;">��������</th>
		<th style="color:#ff0000;">�̹�������URL</th>
		<th style="color:#ff0000;">�̹������</th>
		<th style="color:#ff0000;">�̹������ϸ�</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}


	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--��ǰ��ȣ(�ý���)-->
			<td><?=$GOODS_CATE ?></td>							<!--��ǰī�װ�-->
			<td><?=$GOODS_NAME ?></td>							<!--��ǰ��-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--�𵨸�-->
			<td><?=$GOODS_CODE ?></td>							<!--��ǰ�ڵ�-->
			<td><?=$CATE_01	?></td>								<!--����ǰ����(��)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--�ڽ��Լ�-->
			<td><?=$MSTOCK_CNT ?></td>							<!--�ּ����-->
			<td><?=$CATE_02	?></td>								<!--������-->
		    <td><?=getCompanyCodeAsNo($conn, $CATE_03)?></td>	<!--���޻�-->
			<td><?=$CATE_04 ?></td>								<!--�ǸŻ���-->
			<td><?=$BUY_PRICE ?></td>							<!--���԰�-->				
			<td><?=$STICKER_PRICE ?></td>						<!--��ƼĿ���-->
			<td><?=$PRINT_PRICE ?></td>							<!--�����μ���-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--�ù� ��ۺ�-->
			<td><?=$LABOR_PRICE ?></td>							<!--�ΰǺ�-->
			<td><?=$OTHER_PRICE ?></td>							<!--��Ÿ���-->
			<td><?=$SALE_PRICE ?></td>							<!--�ǸŰ�-->
			<td><?=$MRO_SALE_PRICE ?></td>						<!--MRO�ǸŰ�-->
			<td><?=$SALE_SUSU ?></td>							<!--�Ǹż�����-->
			<td><?=$TAX_TF ?></td>								<!--��������-->		
			<td><?=$IMG_URL ?></td>								<!--�̹�������URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--�̹������-->
			<td><?=$FILE_RNM_150 ?></td>						<!--�̹������ϸ�-->
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>		
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--���԰�-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>

<? } ?>
<!------------------------------------------------------------------------------------------------------------------------->

<? if(startsWith($print_type, "FOR_PRINT")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">��ǰ��ȣ</th>
		<th style="color:#ff0000;">��ǰī�װ�</th>
		<th style="color:#ff0000;">��ǰ��</th>
		<th style="color:#ff0000;">�𵨸�</th>
		<th style="color:#ff0000;">��ǰ�ڵ�</th>
		<th style="color:#ff0000;">����ǰ����</th>
		<th style="color:#ff0000;">�ڽ��Լ�</th>
		<th style="color:#ff0000;">�ּ����</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">���޻�</th>
		<th style="color:#ff0000;">�ǸŻ���</th>
		<th style="color:#ff0000;">���԰�</th>
		<th style="color:#ff0000;">��ƼĿ���</th>
		<th style="color:#ff0000;">�����μ���</th>
		<th style="color:#ff0000;">�ù� ��ۺ�</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">�ΰǺ�</th>
		<th style="color:#ff0000;">��Ÿ���</th>
		<th style="color:#ff0000;">�����հ�</th>
		<th style="color:#ff0000;">�ǸŰ�</th>
		<th style="color:#ff0000;">MRO�ǸŰ�</th>
		<th style="color:#ff0000;">����15%</th>
		<th style="color:#ff0000;">����35%</th>
		<th style="color:#ff0000;">����55%</th>
		<? if ($chk_vendor == "Y") { ?>
			<th style="color:#ff0000;">�������ΰ�</th>
		<? } ?>
		<th style="color:#ff0000;">�Ǹż�����</th>
		<th style="color:#ff0000;">�Ǹż�����</th>
		<th style="color:#ff0000;">����</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">�������� �ǸŰ�</th>
		<th style="color:#ff0000;">��������</th>
		<th style="color:#ff0000;">�̹�������URL</th>
		<th style="color:#ff0000;">�̹������</th>
		<th style="color:#ff0000;">�̹������ϸ�</th>
		<th style="color:#ff0000;">�������</th>
		<th style="color:#ff0000;">�ҷ����</th>
		
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$BSTOCK_CNT				= trim($arr_rs[$j]["BSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
				$NEXT_SALE_PRICE		= trim($arr_rs[$j]["NEXT_SALE_PRICE"]); 

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);


				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);


				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "���Ұ�";

				$VENDER_15_PRICE = ceiling((($SALE_PRICE - $PRICE) * 15 / 100.0 + $PRICE), -1);
				$VENDER_35_PRICE = ceiling((($SALE_PRICE - $PRICE) * 35 / 100.0 + $PRICE), -1);
				$VENDER_55_PRICE = ceiling((($SALE_PRICE - $PRICE) * 55 / 100.0 + $PRICE), -1);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--��ǰ��ȣ(�ý���)-->
			<td>
			<?
				$max_index = 0;
				while($max_index <= strlen($GOODS_CATE)) {
							
					if($max_index > 2)
						echo " > ";
					echo getCategoryNameOnly($conn, left($GOODS_CATE, $max_index));

					$max_index += 2;

				}
			?></td>							<!--��ǰī�װ�-->
			<td><?=$GOODS_NAME ?></td>							<!--��ǰ��-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--�𵨸�-->
			<td><?=$GOODS_CODE ?></td>							<!--��ǰ�ڵ�-->
			<td><?=$CATE_01	?></td>								<!--����ǰ����(��)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--�ڽ��Լ�-->
			<td><?=$MSTOCK_CNT ?></td>							<!--�ּ����-->
			<td><?=$CATE_02	?></td>								<!--������-->
		    <td><?=getCompanyName($conn, $CATE_03)?></td>		<!--���޻�-->
			<td><?=$CATE_04 ?></td>								<!--�ǸŻ���-->
			<td><?=$BUY_PRICE ?></td>							<!--���԰�-->				
			<td><?=$STICKER_PRICE ?></td>						<!--��ƼĿ���-->
			<td><?=$PRINT_PRICE ?></td>							<!--�����μ���-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--�ù� ��ۺ�-->
			<td><?=$DELIVERY_PER_PRICE ?></td>					<!--������-->
			<td><?=$LABOR_PRICE ?></td>							<!--�ΰǺ�-->
			<td><?=$OTHER_PRICE ?></td>							<!--��Ÿ���-->
			<td><?=$PRICE ?></td>								<!--�����հ�-->
			<td><?=$SALE_PRICE ?></td>							<!--�ǸŰ�-->
			<td><?=$MRO_SALE_PRICE ?></td>						<!--MRO�ǸŰ�-->
			<td><?=$VENDER_15_PRICE ?></td>						<!--����15��-->
			<td><?=$VENDER_35_PRICE ?></td>						<!--����35��-->
			<td><?=$VENDER_55_PRICE ?></td>						<!--����55��-->
			<? if ($chk_vendor == "Y") { ?>
				<td><?=$VENDER_PRICE ?></td>
			<? } ?>
			<td><?=$SALE_SUSU ?></td>							<!--�Ǹż�����-->
			<td><?=$SUSU_PRICE ?></td>							<!--�Ǹż�����-->
			<td><?=$MAJIN ?></td>								<!--����-->
			<td><?=$MAJIN_PER ?></td>							<!--������-->
			<td><?=$NEXT_SALE_PRICE ?></td>						<!--�������� �ǸŰ�-->
			<td><?=$TAX_TF ?></td>								<!--��������-->		
			<td><?=$IMG_URL ?></td>								<!--�̹�������URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--�̹������-->
			<td><?=$FILE_RNM_150 ?></td>						<!--�̹������ϸ�-->
			<td><?=$STOCK_CNT ?></td>							<!--�������-->
			<td><?=$BSTOCK_CNT ?></td>							<!--�ҷ����-->
			
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>		
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--���԰�-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<? if ($chk_vendor == "Y") { ?>
			<td></td>
			<? }?>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>


<? } ?>
<!-------------------------------------------------------------------------------------------------------------------------->
<? if(startsWith($print_type, "FOR_CATALOG")) { ?>
<table border="1">
	<tr>
		<th style="color:#ff0000;">��ǰ��ȣ</th>
		<th style="color:#ff0000;">�˻�ī�װ�</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">����</th>
		<th style="color:#ff0000;">��ǰ��</th>
		<th style="color:#ff0000;">�𵨸�</th>
		<th style="color:#ff0000;">��ǰ�ڵ�</th>
		<th style="color:#ff0000;">����ǰ����</th>
		<th style="color:#ff0000;">�ڽ��Լ�</th>
		<th style="color:#ff0000;">�ּ����</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">���޻�</th>
		<th style="color:#ff0000;">�ǸŻ���</th>
		<th style="color:#ff0000;">���԰�</th>
		<th style="color:#ff0000;">��ƼĿ���</th>
		<th style="color:#ff0000;">�����μ���</th>
		<th style="color:#ff0000;">�ù� ��ۺ�</th>
		<th style="color:#ff0000;">�ΰǺ�</th>
		<th style="color:#ff0000;">��Ÿ���</th>
		<th style="color:#ff0000;">�ǸŰ�</th>
		<? if ($chk_vendor == "Y") { ?>
			<th style="color:#ff0000;">�������ΰ�</th>
		<? } ?>
		<th style="color:#ff0000;">�Ǹż�����</th>
		<th style="color:#ff0000;">��������</th>
		<th style="color:#ff0000;">�̹�������URL</th>
		<th style="color:#ff0000;">�̹������</th>
		<th style="color:#ff0000;">�̹������ϸ�</th>
		
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}


	?>
	<tr>
			<td><?=$GOODS_NO ?></td>							<!--��ǰ��ȣ(�ý���)-->
			<td>
			<?
				$max_index = 0;
				while($max_index <= strlen($SEARCH_CATE)) {
							
					if($max_index > 2)
						echo " > ";
					echo getCategoryNameOnly($conn, left($SEARCH_CATE, $max_index));

					$max_index += 2;

				}
			?>
			</td>
			<td><?=$PAGE ?></td>								<!--������-->
			<td><?=$SEQ ?></td>									<!--����-->
			<td><?=$GOODS_NAME ?></td>							<!--��ǰ��-->
			<td><?=$GOODS_SUB_NAME ?></td>						<!--�𵨸�-->
			<td><?=$GOODS_CODE ?></td>							<!--��ǰ�ڵ�-->
			<td><?=$CATE_01	?></td>								<!--����ǰ����(��)-->
			<td><?=$DELIVERY_CNT_IN_BOX ?></td>					<!--�ڽ��Լ�-->
			<td><?=$MSTOCK_CNT ?></td>							<!--�ּ����-->
			<td><?=$CATE_02	?></td>								<!--������-->
		    <td><?=getCompanyCodeAsNo($conn, $CATE_03)?></td>	<!--���޻�-->
			<td><?=$CATE_04 ?></td>								<!--�ǸŻ���-->
			<td><?=$BUY_PRICE ?></td>							<!--���԰�-->				
			<td><?=$STICKER_PRICE ?></td>						<!--��ƼĿ���-->
			<td><?=$PRINT_PRICE ?></td>							<!--�����μ���-->
			<td><?=$DELIVERY_PRICE ?></td>						<!--�ù� ��ۺ�-->
			<td><?=$LABOR_PRICE ?></td>							<!--�ΰǺ�-->
			<td><?=$OTHER_PRICE ?></td>							<!--��Ÿ���-->
			<td><?=$SALE_PRICE ?></td>							<!--�ǸŰ�-->
			<? if ($chk_vendor == "Y") { ?>
				<td><?=$VENDER_PRICE ?></td>
			<? } ?>
			<td><?=$SALE_SUSU ?></td>							<!--�Ǹż�����-->
			<td><?=$TAX_TF ?></td>								<!--��������-->		
			<td><?=$IMG_URL ?></td>								<!--�̹�������URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--�̹������-->
			<td><?=$FILE_RNM_150 ?></td>						<!--�̹������ϸ�-->
	</tr>
	<?	
		if(!endsWith($print_type, "_NO_SUB")) { 
		$arr_goods_sub = selectGoodsSub($conn, $GOODS_NO);
		for ($k = 0 ; $k < sizeof($arr_goods_sub); $k++) 
		{
			//GOODS_NAME, GOODS_SUB_NO, GOODS_CNT, GOODS_CODE
			$SUB_GOODS_SUB_NO	= trim($arr_goods_sub[$k]["GOODS_SUB_NO"]);
			$SUB_GOODS_NAME		= trim($arr_goods_sub[$k]["GOODS_NAME"]);
			$SUB_GOODS_CODE		= trim($arr_goods_sub[$k]["GOODS_CODE"]);
			$SUB_GOODS_CNT		= trim($arr_goods_sub[$k]["GOODS_CNT"]);
			$SUB_BUY_PRICE		= trim($arr_goods_sub[$k]["BUY_PRICE"]);
	?>
	<tr>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_SUB_NO?></td>
			<td></td>	
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_NAME ?></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CODE ?></td>			
			<td style="color:#8c8c8c;"><?=$SUB_GOODS_CNT ?></td>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="color:#8c8c8c;"><?=$SUB_BUY_PRICE ?></td> <!--���԰�-->
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<? if ($chk_vendor == "Y") { ?>
			<td></td>
			<? } ?>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	<?
			}
		}
	?>
	<?
			}
		}
	?>
</table>


<? } ?>
<!-------------------------------------------------------------------------------------------------------->
<? if(startsWith($print_type, "DISPLAY")) { ?>
<table border="1">
	<? if($view_type == "price") { ?>
	<tr>
		<th style="color:#ff0000;">��ǰ��ȣ</th>
		<th style="color:#ff0000;">��ǰ�ڵ�</th>
		<th style="color:#ff0000;">��ǰ��</th>
		<th style="color:#ff0000;">���޻�</th>
		<th style="color:#ff0000;">���԰�</th>
		<th style="color:#ff0000;">�����հ�</th>
		<th style="color:#ff0000;">�ǸŰ�</th>
		<th style="color:#ff0000;">����</th>
		<th style="color:#ff0000;">������</th>
		<th style="color:#ff0000;">�ڽ��Լ�</th>
		<th style="color:#ff0000;">���</th>
		<th style="color:#ff0000;">�ǸŻ���</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$MRO_SALE_PRICE			= trim($arr_rs[$j]["MRO_SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);
				
				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "���Ұ�";

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

	?>
	<tr>
		<td><?=$GOODS_NO ?></td>									<!--��ǰ��ȣ(�ý���)-->
		<td><?=$GOODS_CODE ?></td>									<!--��ǰ�ڵ�-->
		<td><?=$GOODS_NAME ?> <?=$GOODS_SUB_NAME ?></td>			<!--��ǰ��-->
		<td><?=getCompanyName($conn, $CATE_03)?></td>				<!--���޻�-->
		<td><?=$BUY_PRICE ?></td>									<!--���԰�-->		
		<td><?=$PRICE ?></td>										<!--�����հ�-->	
		<td>														<!--�ǸŰ�-->
		<? if ($chk_vendor != "Y" && $chk_next_sale_price != "Y") {?>
			<?= number_format($SALE_PRICE) ?> ��
		<? } else { ?>
			<?= number_format($SALE_PRICE) ?> ��
			<? if ($chk_vendor == "Y") {?>
				(<?= number_format($VENDER_PRICE)?> ��)
			<? } ?>
			<?  if($chk_next_sale_price == "Y") { 
					if($NEXT_SALE_PRICE == "") { 
			?>
				(����)
			<?	    } else { ?>
				(<?= getSafeNumberFormatted($NEXT_SALE_PRICE) ?> ��)
			<?      }
				}
			?>
		<? } ?>
		</td>														
		<td><?=$MAJIN ?></td>										<!--����-->
		<td><?=$MAJIN_PER ?></td>									<!--������-->		
		<td><?=$MRO_SALE_PRICE ?></td>								<!--MRO�ǸŰ�-->		
		<td><?=$DELIVERY_CNT_IN_BOX ?></td>							<!--�ڽ��Լ�-->
		<td><?=$STOCK_CNT ?></td>									<!--���-->
		<td><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></td><!--�ǸŻ���-->
	</tr>
	<?		}
		}
	?>

	<? } ?>
	<? if($view_type == "stock") { ?>
	<tr>
		<th style="color:#ff0000;">��ǰ��ȣ</th>
		<th style="color:#ff0000;">��ǰ�ڵ�</th>
		<th style="color:#ff0000;">��ǰ��</th>
		<th style="color:#ff0000;">�ڽ��Լ�</th>
		<th style="color:#ff0000;">�ּ����</th>
		<th style="color:#ff0000;">�����</th>
		<th style="color:#ff0000;">�����</th>
		<th style="color:#ff0000;">�������</th>
		<th style="color:#ff0000;">�ҷ����</th>
		<th style="color:#ff0000;">�������</th>
		<th style="color:#ff0000;">�ǸŻ���</th>
	</tr>
	<?
		$nCnt = 0;
		
		if (sizeof($arr_rs) > 0) {
			
			for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
				
				$rn						= trim($arr_rs[$j]["rn"]);
				$GOODS_NO				= trim($arr_rs[$j]["GOODS_NO"]);
				$GOODS_CATE				= trim($arr_rs[$j]["GOODS_CATE"]);
				$GOODS_CODE				= trim($arr_rs[$j]["GOODS_CODE"]);
				$GOODS_NAME				= SetStringFromDB($arr_rs[$j]["GOODS_NAME"]);
				$GOODS_SUB_NAME			= SetStringFromDB($arr_rs[$j]["GOODS_SUB_NAME"]);
				$CATE_01				= trim($arr_rs[$j]["CATE_01"]);
				$CATE_02				= trim($arr_rs[$j]["CATE_02"]);
				$CATE_03				= trim($arr_rs[$j]["CATE_03"]);
				$CATE_04				= trim($arr_rs[$j]["CATE_04"]);
				$PRICE					= trim($arr_rs[$j]["PRICE"]);
				$SALE_PRICE				= trim($arr_rs[$j]["SALE_PRICE"]);
				$BUY_PRICE				= trim($arr_rs[$j]["BUY_PRICE"]);
				$EXTRA_PRICE			= trim($arr_rs[$j]["EXTRA_PRICE"]);
				$STOCK_CNT				= trim($arr_rs[$j]["STOCK_CNT"]);
				$FSTOCK_CNT				= trim($arr_rs[$j]["FSTOCK_CNT"]);
				$TAX_TF					= trim($arr_rs[$j]["TAX_TF"]);
				$IMG_URL				= trim($arr_rs[$j]["IMG_URL"]);
				$FILE_NM				= trim($arr_rs[$j]["FILE_NM_100"]);
				$FILE_RNM				= trim($arr_rs[$j]["FILE_RNM_100"]);
				$FILE_PATH				= trim($arr_rs[$j]["FILE_PATH_100"]);
				$FILE_SIZE				= trim($arr_rs[$j]["FILE_SIZE_100"]);
				$FILE_EXT				= trim($arr_rs[$j]["FILE_EXT_100"]);
				$FILE_NM_150			= trim($arr_rs[$j]["FILE_NM_150"]);
				$FILE_RNM_150			= trim($arr_rs[$j]["FILE_RNM_150"]);
				$FILE_PATH_150			= trim($arr_rs[$j]["FILE_PATH_150"]);
				$FILE_SIZE_150			= trim($arr_rs[$j]["FILE_SIZE_150"]);
				$FILE_EXT_150			= trim($arr_rs[$j]["FILE_EXT_150"]);
				$CONTENTS				= trim($arr_rs[$j]["CONTENTS"]);
				$READ_CNT				= trim($arr_rs[$j]["READ_CNT"]);
				$DISP_SEQ				= trim($arr_rs[$j]["DISP_SEQ"]);
				$STICKER_PRICE       	= trim($arr_rs[$j]["STICKER_PRICE"]); 
				$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
				$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
				$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
				$OTHER_PRICE	        = trim($arr_rs[$j]["OTHER_PRICE"]); 
				$SALE_SUSU			    = trim($arr_rs[$j]["SALE_SUSU"]);
				$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
				$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
				$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);
				$MSTOCK_CNT 			= trim($arr_rs[$j]["MSTOCK_CNT"]);
				$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);

				$SEARCH_CATE			= trim($arr_rs[$j]["SEARCH_CATE"]);
				$PAGE					= trim($arr_rs[$j]["PAGE"]);
				$SEQ					= trim($arr_rs[$j]["SEQ"]);

				$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				$ARR_OPTION_NAME[0] = "";
				$ARR_OPTION_NAME[1] = "";

				$arr_rs_option_name = selectGoodsOptionName($conn, $GOODS_NO);
				
				if (sizeof($arr_rs_option_name) > 0) {
			
					for ($k = 0 ; $k < sizeof($arr_rs_option_name); $k++) {
						$ARR_OPTION_NAME[$k]				= trim($arr_rs_option_name[$k]["OPTION_NAME"]);
					}
				}

				$FILE_PATH_150 = str_replace("<img src=","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace(">","", $FILE_PATH_150);
				$FILE_PATH_150 = str_replace("\"","", $FILE_PATH_150);


				$order   = array("\r\n", "\n", "\r");
				$replace = "";

				$CONTENTS = str_replace($order, $replace, $CONTENTS);
				
				if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
					$DELIVERY_PER_PRICE = 0;
				else 
					$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
				
				$SUSU_PRICE = round($SALE_PRICE / 100 * $SALE_SUSU, 0);

				$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

				if($SALE_PRICE != 0)
					$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2);
				else 
					$MAJIN_PER = "���Ұ�";

				if($chk_vendor == "Y") {
					$DC_RATE = $txt_vendor_calc;
					$VENDER_PRICE = ceiling((($SALE_PRICE - $PRICE) * $DC_RATE / 100.0 + $PRICE), -1);
				} else {
					$DC_RATE = "";
					$VENDER_PRICE = $SALE_PRICE;
				}

				$TSTOCK_CNT = getCalcGoodsInOrdering($conn, $GOODS_NO);

	?>
	<tr>
		<td><?=$GOODS_NO ?></td>									<!--��ǰ��ȣ(�ý���)-->
		<td><?=$GOODS_CODE ?></td>									<!--��ǰ�ڵ�-->
		<td><?=$GOODS_NAME ?> <?=$GOODS_SUB_NAME ?></td>			<!--��ǰ��-->
		<td><?=$DELIVERY_CNT_IN_BOX ?></td>							<!--�ڽ��Լ�-->
		<td><?=$MSTOCK_CNT ?></td>									<!--�ּ����-->		
		<td><?= ($TSTOCK_CNT > 0 ? "-".$TSTOCK_CNT : 0) ?></td>		<!--�����-->	
		<td><?=$FSTOCK_CNT ?></td>									<!--���԰�-->
		<td><?=$STOCK_CNT ?></td>									<!--�������-->
		<td><?=$BSTOCK_CNT ?></td>									<!--�ҷ����-->
		<td><?=($STOCK_CNT + $FSTOCK_CNT - $TSTOCK_CNT) ?></td>		<!--�������-->
		<td><?=getDcodeName($conn, "GOODS_STATE", $CATE_04); ?></td><!--�ǸŻ���-->
	</tr>
	<?		}
		}
	?>

	<? } ?>
</table>
<? } ?>
<!-- //List -->
			
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
