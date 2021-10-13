<?session_start();?>
<?
# =============================================================================
# File Name    : goods_price_excel_list.php
# =============================================================================

$file_name="��ǰ����-".date("Ymd").".xls";
header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-Description: orion@giringrim.com" );

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "GD005"; // �޴����� ���� �� �־�� �մϴ�

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

	$filter = array("cp_no" => $cp_no);

	$arr_rs = listGoodsPrice($conn, $con_cate, $start_date, $end_date, $start_price, $end_price, $con_cate_01, $con_cate_02, $con_cate_03, $con_cate_04, $con_use_tf, $del_tf, $filter, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
</head>

<body>
<h3>��ǰ���� ����Ʈ</h3>

<?if($print_type == "") { ?>
<table border="1">
	<tr>
		<th>No.</th>
		<th>��ǰ��ȣ</th>
		<th>��ǰ�ڵ�</th>
		<th>��ǰ��</th>
		<th>�𵨸�</th>
		<th>���޻�</th>
		<th>�ڽ��Լ�</th>
		<th>���԰�</th>
		<th>�����հ�</th>
		<th>�ǸŰ�</th>
		<th>����(������)</th>
		<th>�Ǹž�ü ������</th>
		<th>�Ǹž�ü �ǸŰ�</th>
		<th>�Ǹž�ü</th>
	</tr>
				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn						= trim($arr_rs[$j]["rn"]);
							$SEQ_NO					= trim($arr_rs[$j]["SEQ_NO"]);
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
							$CP_NAME				= trim($arr_rs[$j]["CP_NAME"]);
							$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
							$DEL_TF					= trim($arr_rs[$j]["DEL_TF"]);
							$REG_DATE				= trim($arr_rs[$j]["REG_DATE"]);

							$DELIVERY_CNT_IN_BOX	= trim($arr_rs[$j]["DELIVERY_CNT_IN_BOX"]);
							$STICKER_PRICE			= trim($arr_rs[$j]["STICKER_PRICE"]); 
							$PRINT_PRICE			= trim($arr_rs[$j]["PRINT_PRICE"]); 
							$DELIVERY_PRICE			= trim($arr_rs[$j]["DELIVERY_PRICE"]); 
							$LABOR_PRICE			= trim($arr_rs[$j]["LABOR_PRICE"]); 
							$OTHER_PRICE			= trim($arr_rs[$j]["OTHER_PRICE"]);
							$SALE_SUSU				= trim($arr_rs[$j]["SALE_SUSU"]);

							$CP_SALE_SUSU			= trim($arr_rs[$j]["CP_SALE_SUSU"]);
							$CP_SALE_PRICE			= trim($arr_rs[$j]["CP_SALE_PRICE"]);
							

							if($DELIVERY_PRICE == 0 || $DELIVERY_CNT_IN_BOX == 0)
								$DELIVERY_PER_PRICE = 0;
							else 
								$DELIVERY_PER_PRICE = round($DELIVERY_PRICE / $DELIVERY_CNT_IN_BOX, 0);
							
							$SUSU_PRICE = round($SALE_PRICE / 100, 0) * $SALE_SUSU;

							$MAJIN = $SALE_PRICE - $SUSU_PRICE - $PRICE;

							if($SALE_PRICE != 0)
								$MAJIN_PER = round(($MAJIN / $SALE_PRICE) * 100, 2)."%";
							else 
								$MAJIN_PER = "���Ұ�";

							$REG_DATE = date("Y-m-d",strtotime($REG_DATE));
				
				?>

	<tr>
		<td><?= $rn ?></td>
		<td><?=$GOODS_NO?></td>
		<td><?=$GOODS_CODE?></td>
		<td><?=$GOODS_NAME?></td>
		<td><?=$GOODS_SUB_NAME?></td>
		<td><?= getCompanyName($conn, $CATE_03);?></td>
		<td><?=$DELIVERY_CNT_IN_BOX?></td>
		<td><?= number_format($BUY_PRICE) ?> ��</td>
		<td><?= number_format($PRICE) ?> ��</td>
		<td><?= number_format($SALE_PRICE) ?> ��</td>
		<td><?= number_format($MAJIN) ?> ��(<?=$MAJIN_PER?>)</td>
		<td><?= $CP_SALE_SUSU ?> %</td>
		<td><?= number_format($CP_SALE_PRICE) ?> ��</td>
		<td><?= $CP_NAME ?></td>
	</tr>

				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="14">��ϵ� ������ �����ϴ�</td>
	</tr>
				<? 
					}
				?>

</table>
<? } ?>
<? if($print_type == "FOR_REG") { ?>

<table border="1">
	<tr>
		<th style="color:#ff0000;">����</th>
		<th style="color:#ff0000;">�Ǹž�ü</th>
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

							$CP_NAME				= trim($arr_rs[$j]["CP_NAME"]);
							
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


				?>
	<tr>
			<td><?= $rn ?></td>
			<td><?=$CP_NAME?></td>								<!--�Ǹž�ü-->
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
			<td><?=$SALE_SUSU ?></td>							<!--�Ǹż�����-->
			<td><?=$TAX_TF ?></td>								<!--��������-->		
			<td><?=$IMG_URL ?></td>								<!--�̹�������URL-->
			<td><?=$FILE_PATH_150 ?></td>						<!--�̹������-->
			<td><?=$FILE_RNM_150 ?></td>						<!--�̹������ϸ�-->
	</tr>

<?	
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
			<td></td>
			<td></td>
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
?>
				<?			
						}
					} else { 
				?> 
	<tr>
		<td height="50" align="center" colspan="25">��ϵ� ������ �����ϴ�</td>
	</tr>
				<? 
					}
				?>

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