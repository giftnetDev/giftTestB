<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD005"; // �޴����� ���� �� �־�� �մϴ�

#====================================================================
# common_header Check Session
#====================================================================
	require "../_common/common_header.php"; 

#=====================================================================
# common function, login_function
#=====================================================================
	require "../_common/config.php";
	require "../_classes/com/util/Util.php";
	require "../_classes/com/etc/etc.php";
	require "../_classes/biz/order/order.php";
	require "../_classes/biz/member/member.php";
	require "../_classes/biz/payment/payment.php";
	require "../_classes/biz/board/board.php";
	require "../_classes/biz/stock/stock.php";
	require "../_classes/biz/goods/goods.php";
	require "../_classes/biz/company/company.php";
	require "../_classes/biz/confirm/confirm.php";
	require "../_classes/biz/work/work.php";
	require "../_classes/biz/email/email.php";



	include('../_PHPMailer/class.phpmailer.php');

	$path = "/home/hosting_users/kustaf/www/upload_data/temp_mail";
	$filename = "����Ʈ��_�ŷ�����_20181019EN00010.xls";

	$error_msg = mailer("webadmin@giftnet.co.kr", "webadmin@giftnet.co.kr", "gift@giftnet.co.kr", "gift@giftnet.co.kr", "����߼���-���ϸ����üũ", "�׽�Ʈ��", $path, $filename);
	if($error_msg <> "") { 
?>
	<script type="text/javascript">
		alert('<?=$error_msg?>');
	
	</script>
<?
	} else { 
?>
	<script type="text/javascript">
		alert('�߼ۿϷ�!');
	</script>
<?
	

	}

	echo $path."<br>";
	echo $filename."<br>";

//	echo $_SERVER["DOCUMENT_ROOT"]."<br/>";
	
?>