<?session_start();?>
<?

if ($s_adm_cp_type <> "운영") {
	$next_url = "company_write.php?mode=S&cp_no=$s_adm_com_code";
?>
<meta http-equiv='Refresh' content='0; URL=<?=$next_url?>'>
<?
	exit;
}


#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "CP002"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/admin/admin.php";

	$file_name="업체관리리스트-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // 헤더를 출력하는 부분 (이 프로그램의 핵심)
	  header( "Content-Disposition: attachment; filename=$file_name" );

#====================================================================
# Request Parameter
#====================================================================
	#user_paramenter
	$con_cp_type = trim($con_cp_type);
	
	
	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$date_start			= trim($date_start);
	$date_end				= trim($date_end);
	
	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
#============================================================
# Page process
#============================================================

	$use_tf				= "Y";
	$del_tf				= "N";
	$nPage				= "1";
	$nPageSize		= "100000";

#===============================================================
# Get Search list count
#===============================================================
	$filter = array('con_is_mall' => $con_is_mall);

	// $arr_rs = listCompany($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sel_sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);
	$arr_rs = listCompanyWithLastOrderDate($conn, $con_cate, $con_cp_type, $con_ad_type, $date_start, $date_end, $min_dc_rate, $max_dc_rate, $sel_sale_adm_no, $filter, $use_tf, $del_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize);

?>
<font size=3><b><?=$Admin_shop_name?> 업체 리스트 </b></font> <br>
<br>
출력 일자 : [<?=date("Y년 m월 d일")?> ]
<br>
<br>
<TABLE border=1>
	<tr>
		<td align='center' bgcolor='#F4F1EF'>시스템번호</td> 
		<td align='center' bgcolor='#F4F1EF'>카테고리</td> 
		<td align='center' bgcolor='#F4F1EF'>관리코드</td>
		<td align='center' bgcolor='#F4F1EF'>업체명</td>
		<td align='center' bgcolor='#F4F1EF'>지점명</td>
		<td align='center' bgcolor='#F4F1EF'>업체구분</td>
		<td align='center' bgcolor='#F4F1EF'>밴더할인/수수율</td>
		<td align='center' bgcolor='#F4F1EF'>사업자번호</td>
		<td align='center' bgcolor='#F4F1EF'>대표자명</td>
		<td align='center' bgcolor='#F4F1EF'>대표전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>대표FAX</td>
		<td align='center' bgcolor='#F4F1EF'>주소1-우편번호</td>
		<td align='center' bgcolor='#F4F1EF'>주소1</td>
		<td align='center' bgcolor='#F4F1EF'>주소2-우편번호</td>
		<td align='center' bgcolor='#F4F1EF'>주소2</td>
		<td align='center' bgcolor='#F4F1EF'>업태</td>
		<td align='center' bgcolor='#F4F1EF'>종목</td>
		
		<td align='center' bgcolor='#F4F1EF'>담당자명</td>
		<td align='center' bgcolor='#F4F1EF'>전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>휴대전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>FAX전화번호</td>
		<td align='center' bgcolor='#F4F1EF'>이메일</td>
		<td align='center' bgcolor='#F4F1EF'>이메일 수신여부</td>

		<td align='center' bgcolor='#F4F1EF'>영업담당자</td>
		<td align='center' bgcolor='#F4F1EF'>결재구분</td>
		<td align='center' bgcolor='#F4F1EF'>거래은행</td>
		<td align='center' bgcolor='#F4F1EF'>계좌번호</td>
		<td align='center' bgcolor='#F4F1EF'>예금주</td>
		<td align='center' bgcolor='#F4F1EF'>계약기간</td>
		<td align='center' bgcolor='#F4F1EF'>홈페이지</td>
		<td align='center' bgcolor='#F4F1EF'>인터넷몰 여부</td>
		<td align='center' bgcolor='#F4F1EF'>업체메모</td>
		<td align='center' bgcolor='#F4F1EF'>등록일자</td>
		<td align='center' bgcolor='#F4F1EF'>마지막 주문일자</td>
	</tr>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
																
								$rn								= trim($arr_rs[$j]["rn"]);
								$CP_NO							= trim($arr_rs[$j]["CP_NO"]);
								$CP_CATE						= SetStringFromDB($arr_rs[$j]["CP_CATE"]); 
								$CP_NM							= SetStringFromDB($arr_rs[$j]["CP_NM"]); 
								$CP_NM2							= SetStringFromDB($arr_rs[$j]["CP_NM2"]); 
								$CP_CODE						= SetStringFromDB($arr_rs[$j]["CP_CODE"]); 
								$CP_TYPE						= SetStringFromDB($arr_rs[$j]["CP_TYPE"]); 
								$AD_TYPE						= SetStringFromDB($arr_rs[$j]["AD_TYPE"]); 
								$CP_PHONE						= SetStringFromDB($arr_rs[$j]["CP_PHONE"]); 
								$CP_HPHONE						= SetStringFromDB($arr_rs[$j]["CP_HPHONE"]); 
								$CP_FAX							= SetStringFromDB($arr_rs[$j]["CP_FAX"]); 
								$CP_ZIP							= trim($arr_rs[$j]["CP_ZIP"]); 
								$CP_ADDR						= SetStringFromDB($arr_rs[$j]["CP_ADDR"]); 
								$RE_ZIP							= trim($arr_rs[$j]["RE_ZIP"]); 
								$RE_ADDR						= SetStringFromDB($arr_rs[$j]["RE_ADDR"]); 
								$BIZ_NO							= trim($arr_rs[$j]["BIZ_NO"]); 
								$CEO_NM							= SetStringFromDB($arr_rs[$j]["CEO_NM"]); 
								$UPJONG							= SetStringFromDB($arr_rs[$j]["UPJONG"]); 
								$UPTEA							= SetStringFromDB($arr_rs[$j]["UPTEA"]); 
								$ACCOUNT_BANK				    = SetStringFromDB($arr_rs[$j]["ACCOUNT_BANK"]); 
								$ACCOUNT						= trim($arr_rs[$j]["ACCOUNT"]); 
								$ACCOUNT_OWNER_NM		        = trim($arr_rs[$j]["ACCOUNT_OWNER_NM"]); 
								$HOMEPAGE						= SetStringFromDB($arr_rs[$j]["HOMEPAGE"]); 
								$MEMO							= trim($arr_rs[$j]["MEMO"]); 
								$DC_RATE						= trim($arr_rs[$j]["DC_RATE"]); 
								$SALE_ADM_NO                    = trim($arr_rs[$j]["SALE_ADM_NO"]);
								$MANAGER_NM					    = SetStringFromDB($arr_rs[$j]["MANAGER_NM"]); 
								$PHONE							= SetStringFromDB($arr_rs[$j]["PHONE"]); 
								$HPHONE							= SetStringFromDB($arr_rs[$j]["HPHONE"]); 
								$FPHONE							= SetStringFromDB($arr_rs[$j]["FPHONE"]); 
								$EMAIL							= SetStringFromDB($arr_rs[$j]["EMAIL"]); 
								$EMAIL_TF						= trim($arr_rs[$j]["EMAIL_TF"]); 
								$CONTRACT_START			        = trim($arr_rs[$j]["CONTRACT_START"]); 
								$CONTRACT_END			    	= trim($arr_rs[$j]["CONTRACT_END"]); 
								$IS_MALL						= trim($arr_rs[$j]["IS_MALL"]); 
								$USE_TF							= trim($arr_rs[$j]["USE_TF"]); 
								$REG_DATE						= trim($arr_rs[$j]["REG_DATE"]);
								
								$CONTRACT_START 				= date("Y-m-d",strtotime($CONTRACT_START));
								$CONTRACT_END					= date("Y-m-d",strtotime($CONTRACT_END));
								$REG_DATE						= date("Y-m-d",strtotime($REG_DATE));

								$SALE_ADM_NM 					= getAdminInfoNameMD($conn, $SALE_ADM_NO); 
								$LAST_ORDER_DATE 				= trim($arr_rs[$j]["LAST_ORDER"]);
					
					?>
	<tr>
		<td bgColor='#FFFFFF' align='center'><?=$CP_NO?></td>
		<td bgColor='#FFFFFF' align='center'>
		<?
			$max_index = 0;
			while($max_index <= strlen($CP_CATE)) {
						
				if($max_index > 2)
					echo " > ";
				echo getCategoryNameOnly($conn, left($CP_CATE, $max_index));

				$max_index += 2;

			}
		?>
		</td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_CODE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_NM2?></td>
		<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn, "CP_TYPE", $CP_TYPE);?></td>
		<td bgColor='#FFFFFF' align='left'><?=$DC_RATE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$BIZ_NO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CEO_NM?></td> 
		<td bgColor='#FFFFFF' align='left'><?=$CP_PHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_ZIP?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CP_ADDR?></td>
		<td bgColor='#FFFFFF' align='left'><?=$RE_ZIP?></td>
		<td bgColor='#FFFFFF' align='left'><?=$RE_ADDR?></td>
		<td bgColor='#FFFFFF' align='left'><?=$UPTEA?></td>
		<td bgColor='#FFFFFF' align='left'><?=$UPJONG?></td>

		<td bgColor='#FFFFFF' align='left'><?=$MANAGER_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$PHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$HPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$FPHONE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$EMAIL?></td>
		<td bgColor='#FFFFFF' align='left'><?=$EMAIL_TF?></td>

		<td bgColor='#FFFFFF' align='left'><?=$SALE_ADM_NM ?></td>
		<td bgColor='#FFFFFF' align='left'><?=getDcodeName($conn, "AD_TYPE", $AD_TYPE);?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT_BANK?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT?></td>
		<td bgColor='#FFFFFF' align='left'><?=$ACCOUNT_OWNER_NM?></td>
		<td bgColor='#FFFFFF' align='left'><?=$CONTRACT_START?> ~ <?=$CONTRACT_END?></td>
		<td bgColor='#FFFFFF' align='left'><?=$HOMEPAGE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$IS_MALL?></td>
		<td bgColor='#FFFFFF' align='left'><?=$MEMO?></td>
		<td bgColor='#FFFFFF' align='left'><?=$REG_DATE?></td>
		<td bgColor='#FFFFFF' align='left'><?=$LAST_ORDER_DATE?></td>
	</tr>
					<?			
									}
								} else { 
							?> 
								<tr>
									<td align="center" height="50"  colspan="32">데이터가 없습니다. </td>
								</tr>
							<? 
								}
							?>

</table>
</body>
</html>

<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>
