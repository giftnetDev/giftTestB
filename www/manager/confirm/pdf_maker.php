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
	$menu_right = "CF006"; // �޴����� ���� �� �־�� �մϴ�

#	$sPageRight_		= "Y";
#	$sPageRight_R		= "Y";
#	$sPageRight_I		= "Y";
#	$sPageRight_U		= "Y";
#	$sPageRight_D		= "Y";
#	$sPageRight_F		= "Y";

if ($s_adm_cp_type == "�Ǹ�" || $s_adm_cp_type == "�ǸŰ���") { 
	$cp_type = $s_adm_com_code;
}


#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";

#====================================================================
# Request Parameter
#====================================================================

	if ($start_date == "") {
		$d = new DateTime('first day of this month');
		$start_date = $d->format("Y.m.d");
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y.m.d",strtotime("0 month"));;
	} else {
		$end_date = trim($end_date);
	}

#===============================================================
# Get Search list count
#===============================================================

	$arr_rs_prev = getCompanyLedgerPreviousMonth($conn, $start_date, $cp_type);

	if($cp_type <> "")
		$arr_rs = listCompanyLedger($conn, $start_date, $end_date, $cp_type);

	
	$arr_rs_company = selectCompany($conn, $cp_type);
	
	if(sizeof($arr_rs_company) > 0) { 
		$rs_cp_zip							= SetStringFromDB($arr_rs_company[0]["CP_ZIP"]); 
		$rs_cp_addr							= SetStringFromDB($arr_rs_company[0]["CP_ADDR"]); 
		$rs_biz_no							= SetStringFromDB($arr_rs_company[0]["BIZ_NO"]); 
		$rs_ceo_nm							= SetStringFromDB($arr_rs_company[0]["CEO_NM"]); 
	}


	
/*

					
					

*/
	
	$html = 
	'			
				'.getCompanyNameWithNoCode($conn, $cp_type).'
				<br>
				<hr>
				<br>
				<table cellpadding="0" cellspacing="0" border="1">
					<tr>
						<td width="750">
							�Ⱓ : '.date("Y�� n�� j��",strtotime($start_date)).'
							 ~ 
							'.date("Y�� n�� j��",strtotime($end_date)).'
						</td>
					</tr>
					<tr>
						<td width="750">
							�ܾ� : '.getSafeNumberFormatted(getBalance($conn, $cp_type)).'
						</td>
					</tr>
					<tr>
						<td width="750">
							����� ��ȣ : '.$rs_biz_no.', ��ǥ�� �� : '.$rs_ceo_nm.'
						</td>
					</tr>
					<tr>
						<td width="750">
							��ǥ �ּ� : '.$rs_cp_zip.' '.$rs_cp_addr.'
						</td>
					</tr>
				</table>
				<br><br>			
				<table border="1">
				<tr>
					<td width="80">��¥</td>
					<td width="40">����</td>
					<td width="110">��ǰ��</td>
					<td width="40">����</td>
					<td width="75">�ܰ�</td>
					<td width="75">����/���޾�</td>
					<td width="75">����/�Աݾ�</td>
					<td width="75">�ΰ���</td>
					<td width="100">�ܾ�</td>
					<td width="80">���</td>
				</tr>
				';

					if (sizeof($arr_rs_prev) > 0) {
						for ($k = 0 ; $k < sizeof($arr_rs_prev); $k++) {

							$BALANCE					= trim($arr_rs_prev[$k]["BALANCE"]);
	$html .= 
	'			<tr>
					<td width="80">'.date("Y.m.d",strtotime("-1 day", strtotime($start_date))).'</td>
					<td width="40">&nbsp;</td>
					<td width="110">�����̿�</td>
					<td width="40">&nbsp;</td>
					<td width="75">&nbsp;</td>
					<td width="75">&nbsp;</td>
					<td width="75">&nbsp;</td>
					<td width="75">&nbsp;</td>
					<td width="100">'.number_format($BALANCE, 0).'</td>
					<td width="80">&nbsp;</td>
				</tr>
	';

						}  
					}  
	
					$month_group = "";
					$month_qty_total = "";
					$month_withdraw_total = ""; 
					$month_deposit_total = "";
					$month_surtax_total = "";
					$month_balance_total = "";
					

					$day_group = "";
					$day_qty_total = "";
					$day_withdraw_total = ""; 
					$day_deposit_total = "";
					$day_surtax_total = "";
					$day_balance_total = "";

					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {

							//CL_NO, CP_NO, INOUT_DATE, INOUT_TYPE, NAME, QTY, UNIT_PRICE, WITHDRAW, DEPOSIT, RESERVE_NO

							$CL_NO						= trim($arr_rs[$j]["CL_NO"]);
							$INOUT_DATE					= trim($arr_rs[$j]["INOUT_DATE"]);
							$INOUT_TYPE					= trim($arr_rs[$j]["INOUT_TYPE"]);
							$NAME						= trim($arr_rs[$j]["NAME"]);
							$QTY						= trim($arr_rs[$j]["QTY"]);
							$UNIT_PRICE					= trim($arr_rs[$j]["UNIT_PRICE"]);
							$WITHDRAW					= trim($arr_rs[$j]["WITHDRAW"]);
							$DEPOSIT					= trim($arr_rs[$j]["DEPOSIT"]);
							$SURTAX						= trim($arr_rs[$j]["SURTAX"]);
							$MEMO						= trim($arr_rs[$j]["MEMO"]);
							$RESERVE_NO					= trim($arr_rs[$j]["RESERVE_NO"]);
							$ORDER_GOODS_NO				= trim($arr_rs[$j]["ORDER_GOODS_NO"]);
							$RGN_NO						= trim($arr_rs[$j]["RGN_NO"]);

							$USE_TF						= trim($arr_rs[$j]["USE_TF"]);

							$TAX_CONFIRM_TF				= trim($arr_rs[$j]["TAX_CONFIRM_TF"]);
							$TAX_CONFIRM_DATE			= trim($arr_rs[$j]["TAX_CONFIRM_DATE"]);

							if($USE_TF == "Y")
								$BALANCE = $BALANCE + $DEPOSIT - $WITHDRAW;
							else { 
								$QTY = 0;
								$WITHDRAW = 0;
								$DEPOSIT = 0;
								$SURTAX = 0;
							}

							if($INOUT_TYPE == "����") { 
								$TAX_TF = getOrderGoodsTaxTF($conn, $ORDER_GOODS_NO);

								if ($TAX_TF == "�����") {
									$STR_TAX_TF = "�����) ";
								} else {
									$STR_TAX_TF = "����) ";
								}
							} else 
								$STR_TAX_TF = "";


							//���� ����� ������ �ϰ� ǥ��
							if($day_group != date("Y.m.d", strtotime($INOUT_DATE)) && $day_group != "" ) { 
				
	$html .= 
	'			<tr>
					<td width="230">�ϰ� : '.$day_group.'</td>
					<td width="40">'.number_format($day_qty_total).'</td>
					<td width="75">&nbsp;</td>
					<td width="75">'.number_format($day_deposit_total).'</td>
					<td width="75">'.number_format($day_withdraw_total).'</td>
					<td width="75">'.number_format($day_surtax_total).'</td>
					<td width="100">'.number_format($day_balance_total, 0).'</td>
					<td width="80">&nbsp;</td>
				</tr>
	'; 

								$day_group = date("Y.m.d",strtotime($INOUT_DATE));
								$day_qty_total = $QTY;
								$day_withdraw_total = $WITHDRAW;
								$day_deposit_total = $DEPOSIT;
								$day_surtax_total = $SURTAX;
								$day_balance_total = $BALANCE;
							} else { 
								
								$day_qty_total += $QTY;
								$day_withdraw_total += $WITHDRAW;
								$day_deposit_total += $DEPOSIT;
								$day_surtax_total += $SURTAX;
								$day_balance_total = $BALANCE;
							}




							//���� ����� ������ ���� ǥ��
							if($month_group != date("Y.m", strtotime($INOUT_DATE)) && $month_group != "" ) { 
				
	$html .= 
	'			<tr>
					<td width="230">���� : '.$month_group.'</td>
					<td width="40">'.number_format($month_qty_total).'</td>
					<td width="75">&nbsp;</td>
					<td width="75">'.number_format($month_deposit_total).'</td>
					<td width="75">'.number_format($month_withdraw_total).'</td>
					<td width="75">'.number_format($month_surtax_total).'</td>
					<td width="100">'.number_format($month_balance_total, 0).'</td>
					<td width="80">&nbsp;</td>
				</tr>
	';


								$month_group = date("Y.m",strtotime($INOUT_DATE));
								$month_qty_total = $QTY;
								$month_withdraw_total = $WITHDRAW;
								$month_deposit_total = $DEPOSIT;
								$month_surtax_total = $SURTAX;
								$month_balance_total = $BALANCE;
							} else { 
								
								$month_qty_total += $QTY;
								$month_withdraw_total += $WITHDRAW;
								$month_deposit_total += $DEPOSIT;
								$month_surtax_total += $SURTAX;
								$month_balance_total = $BALANCE;
							}

							
	$html .= 
	'
				<tr height="30">
					<td width="80">'.date("Y.m.d",strtotime($INOUT_DATE)).'</td>
					<td width="40">'.$INOUT_TYPE.'</td>
					<td width="110">
						'.$STR_TAX_TF.'
						'.$NAME.'
					</td>
					<td width="40">'.number_format($QTY).'</td>
					<td width="75">'.number_format($UNIT_PRICE).'</td>
					<td width="75">'.number_format($DEPOSIT).'</td>
					<td width="75">'.number_format($WITHDRAW).'</td>
					<td width="75">'.number_format($SURTAX).'</td>
					<td width="100">'.number_format($BALANCE, 0).'</td>
					<td width="80">'.$MEMO.'</td>
					
				</tr>

	';
							if($day_group == "")
									$day_group = date("Y.m.d",strtotime($INOUT_DATE));

							if($month_group == "")
									$month_group = date("Y.m",strtotime($INOUT_DATE));

						}

	$html .= 
	'					<tr>
							<td width="230" height="30" bgcolor="#EFEFEF">�ϰ� : '.$day_group.'</td>
							<td width="40" height="30" bgcolor="#EFEFEF">'.number_format($day_qty_total).'</td>
							<td width="75" height="30" bgcolor="#EFEFEF">&nbsp;</td>
							<td width="75" height="30" bgcolor="#EFEFEF">'.number_format($day_deposit_total).'</td>
							<td width="75" height="30" bgcolor="#EFEFEF">'.number_format($day_withdraw_total).'</td>
							<td width="75" height="30" bgcolor="#EFEFEF">'.number_format($day_surtax_total).'</td>
							<td width="100" height="30" bgcolor="#EFEFEF">'.number_format($day_balance_total, 0).'</td>
							<td width="80" height="30" bgcolor="#EFEFEF">&nbsp;</td>
						</tr>

						<tr>
							<td width="230" height="30" bgcolor="#DFDFDF">���� : '.$month_group.'</td>
							<td width="40" height="30" bgcolor="#EFEFEF">'.number_format($month_qty_total).'</td>
							<td width="75" height="30" bgcolor="#DFDFDF">&nbsp;</td>
							<td width="75" height="30" bgcolor="#DFDFDF">'.number_format($month_deposit_total).'</td>
							<td width="75" height="30" bgcolor="#DFDFDF">'.number_format($month_withdraw_total).'</td>
							<td width="75" height="30" bgcolor="#DFDFDF">'.number_format($month_surtax_total).'</td>
							<td width="100" height="30" bgcolor="#DFDFDF">'.number_format($month_balance_total, 0).'</td>
							<td width="80" height="30" bgcolor="#DFDFDF">&nbsp;</td>
						</tr>
	';

					} else { 
	$html .= 
	'
				<tr>
					<td width="750">�����Ͱ� �����ϴ�.</td>
				</tr>
	';			
					}
	$html .= 
	'
				</table>
	';

	require('../../_fpdf181/pdf.php'); 

	//$pdf=new PDF_Korean();  
	

	$pdf = new PDF();

	$pdf->AddUHCFont('����', 'Gulim'); 

	// First page
	$pdf->AddPage();
	$pdf->SetFont('����','',9); 

	$pdf->WriteHTML($html);
	$pdf->Output();
	

?>