<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD018"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";

	$file_name="����Ϸ��Ͽ븮��Ʈ-".date("Ymd").".xls";
	  header( "Content-type: application/vnd.ms-excel" ); // ����� ����ϴ� �κ� (�� ���α׷��� �ٽ�)
	  header( "Content-Disposition: attachment; filename=$file_name" );
	
	$arr_rs = listTempOrderGoodsDeliveryReturn_Interpark($conn, $temp_no);

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<style>td { mso-number-format:\@; } </style> 
</head>

<body>

<TABLE border=1>
	<!--
	<tr>
		<th>���θ��ڵ�</th><th>�ֹ���ȣ</th><th>��ǰ�ڵ�</th><th>�Ǹ��ڻ�ǰ�ڵ�</th><th>�ֹ���ID</th><th>�ֹ���</th><th>�ֹ�����ȭ��ȣ</th><th>�ֹ����ڵ���</th><th>������</th><th>��ȭ��ȣ</th><th>�ڵ���</th><th>������</th><th>�ֹ���</th><th>�ֹ�����</th><th>ī�װ���</th><th>��ǰ��</th><th>�ɼ�</th><th>����</th><th>�ǸŰ���</th><th>�ɼǰ���</th><th>���ǸŰ���</th><th>��ۺ�</th><th>�����</th><th>�ּ�</th><th>�ֹ��ÿ䱸����</th><th>ȸ����޺����αݾ��հ�</th><th>�������αݾ��հ�</th><th>�������</th><th>�������Ʈ</th><th>�Ϲݰ���ݾ�</th><th>ȸ���׷�</th><th>��������</th><th>��ҿϷ���</th><th>��ǰ�Ϸ���</th><th>����</th><th>�ù��</th><th class="end">�����ȣ</th>
	</tr>
	-->
				<?
					
					if (sizeof($arr_rs) > 0) {
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							$A	= SetStringFromDB($arr_rs[$j]["A"]);
							$B	= SetStringFromDB($arr_rs[$j]["B"]);
							$C	= SetStringFromDB($arr_rs[$j]["C"]);
							$D	= SetStringFromDB($arr_rs[$j]["D"]);
							$E	= SetStringFromDB($arr_rs[$j]["E"]);
							$F	= SetStringFromDB($arr_rs[$j]["F"]);
							$G	= SetStringFromDB($arr_rs[$j]["G"]);
							$H	= SetStringFromDB($arr_rs[$j]["H"]);
							$I	= SetStringFromDB($arr_rs[$j]["I"]);
							$J	= SetStringFromDB($arr_rs[$j]["J"]);
							$K	= SetStringFromDB($arr_rs[$j]["K"]);
							$L	= SetStringFromDB($arr_rs[$j]["L"]);
							$M	= SetStringFromDB($arr_rs[$j]["M"]);
							$N	= SetStringFromDB($arr_rs[$j]["N"]);
							$O	= SetStringFromDB($arr_rs[$j]["O"]);
							$P	= SetStringFromDB($arr_rs[$j]["P"]);
							$Q	= SetStringFromDB($arr_rs[$j]["Q"]);
							$R	= SetStringFromDB($arr_rs[$j]["R"]);
							$S	= SetStringFromDB($arr_rs[$j]["S"]);
							$T	= SetStringFromDB($arr_rs[$j]["T"]);
							$U	= SetStringFromDB($arr_rs[$j]["U"]);
							$V	= SetStringFromDB($arr_rs[$j]["V"]);
							$W	= SetStringFromDB($arr_rs[$j]["W"]);
							$X	= SetStringFromDB($arr_rs[$j]["X"]);
							$Y	= SetStringFromDB($arr_rs[$j]["Y"]);
							$Z	= SetStringFromDB($arr_rs[$j]["Z"]);
							$AA	= SetStringFromDB($arr_rs[$j]["AA"]);
							$AB	= SetStringFromDB($arr_rs[$j]["AB"]);
							$AC	= SetStringFromDB($arr_rs[$j]["AC"]);
							$AD	= SetStringFromDB($arr_rs[$j]["AD"]);
							$AE	= SetStringFromDB($arr_rs[$j]["AE"]);
							$AF	= SetStringFromDB($arr_rs[$j]["AF"]);
							$AG	= SetStringFromDB($arr_rs[$j]["AG"]);
							$AH	= SetStringFromDB($arr_rs[$j]["AH"]);
							$AI	= SetStringFromDB($arr_rs[$j]["AI"]);
							$AJ	= SetStringFromDB($arr_rs[$j]["AJ"]);
							$AK	= SetStringFromDB($arr_rs[$j]["AK"]);
						?>
					<tr>
						<td><?=$A?></td>
						<td><?=$B?></td>
						<td><?=$C?></td>
						<td><?=$D?></td>
						<td><?=$E?></td>
						<td><?=$F?></td>
						<td><?=$G?></td>
						<td><?=$H?></td>
						<td><?=$I?></td>
						<td><?=$J?></td>
						<td><?=$K?></td>
						<td><?=$L?></td>
						<td><?=$M?></td>
						<td><?=$N?></td>
						<td><?=$O?></td>
						<td><?=$P?></td>
						<td><?=$Q?></td>
						<td><?=$R?></td>
						<td><?=$S?></td>
						<td><?=$T?></td>
						<td><?=$U?></td>
						<td><?=$V?></td>
						<td><?=$W?></td>
						<td><?=$X?></td>
						<td><?=$Y?></td>
						<td><?=$Z?></td>
						<td><?=$AA?></td>
						<td><?=$AB?></td>
						<td><?=$AC?></td>
						<td><?=$AD?></td>
						<td><?=$AE?></td>
						<td><?=$AF?></td>
						<td><?=$AG?></td>
						<td><?=$AH?></td>
						<td><?=$AI?></td>
						<td><?=$AJ?></td>
						<td><?=$AK?></td>
					</tr>

						<?
						}
					}else{
						?>
						<tr class="order">
							<td height="50" align="center" colspan="37">�����Ͱ� �����ϴ�. </td>
						</tr>
					<?
						}
					?>
</table>

</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>