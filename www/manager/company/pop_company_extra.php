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
	$menu_right = "CP002"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/confirm/confirm.php";
	require "../../_classes/biz/company/company.php";


#====================================================================
# Request Parameter
#====================================================================

	if ($mode == "I") {

		//MANAGER_NM, PHONE, HPHONE, ADDR, MEMO
		$result = insertCompanyExtra($conn, $cp_no, $manager_nm, $phone, $hphone, $addr, $memo, $s_adm_no);

		if($result) { 
?>
<script language="javascript">
		alert("���� �Ǿ����ϴ�.");
		window.location.replace("/manager/company/pop_company_extra.php?cp_no=<?=$cp_no?>");
</script>
<?
		}
		exit();
	}

	//	�������
	if ($mode == "I_DELIVERY_PAPER") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		$arr_error = array();

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_ce_no = $chk_no[$k];

			$arr_ce = selectCompanyExtra($conn, $temp_ce_no);

			for($k = 0; $k < sizeof($arr_ce); $k ++) { 

				$DELIVERY_FEE_CODE = $DELIVERY_CP."-����";
				$DELIVERY_FEE = getDcodeName($conn, "DELIVERY_FEE", $DELIVERY_FEE_CODE); 

				$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

				$OP_CP_NM		= $arr_op_cp[0]["CP_NM"];
				$OP_CP_ADDR		= $arr_op_cp[0]["CP_ADDR"];

				if($SENDER_NM == $OP_CP_NM)
					$CON_SEND_CP_ADDR = $OP_CP_ADDR;
				else
					$CON_SEND_CP_ADDR = str_replace($OP_CP_NM, "", $OP_CP_ADDR);
				
				//�ù�ȸ�� ������ ������� �Ұ�
				if($DELIVERY_CP == "" || $DELIVERY_FEE == "")   
					$error_msg_each = "�ù�ȸ��/��� ����,";

				if($error_msg_each != "") { 
					array_push($arr_error, array('CE_NO' => $temp_ce_no, 'ERROR_MSG_EACH' => rtrim($error_msg_each, ",")));
					continue;
				}

				$MEMO_ALL = "�޹�ۺ�Ź�帳�ϴ�   ������� ��ǰ�Դϴ�-�ιڽ��� �ѼյǴ� ������ �����ּ���~";

				$MANAGER_NM = $arr_ce[0]["MANAGER_NM"];
				$PHONE		= $arr_ce[0]["PHONE"];
				$HPHONE		= $arr_ce[0]["HPHONE"];
				$ADDR		= $arr_ce[0]["ADDR"];

				$DELIVERY_CNT = 1;
				$SEQ_OF_DELIVERY = 1;
				$CON_ORDER_QTY = "1";
				$CON_PAYMENT_TYPE = "�ſ�";
				$CON_DELIVERY_TYPE = "�ù�";

				$RECEIVER_NAME = $MANAGER_NM;

				//������ �ڵ�����ȣ�� ������� ������ ��ȭ��ȣ�� �Է�
				if($PHONE != "" && $HPHONE == "")
					$HPHONE = $R_PHONE;

				$RECEIVER_NAME		= SetStringToDB($RECEIVER_NAME);
				$R_ADDR1			= SetStringToDB($R_ADDR1);
				$SENDER_NM			= SetStringToDB($SENDER_NM);
				$CON_SEND_CP_ADDR	= SetStringToDB($CON_SEND_CP_ADDR);
				$MEMO				= SetStringToDB($MEMO);
				
				$order_goods_delivery_no = insertOrderDeliveryPaper($conn, $chk_work_date, "", 0, 0, $cp_no, $DELIVERY_CNT, $SEQ_OF_DELIVERY, $RECEIVER_NAME, $PHONE, $HPHONE, $ADDR, $CON_ORDER_QTY, $MEMO_ALL, $SENDER_NM, $SENDER_PHONE, $SENDER_NM, $SENDER_PHONE, $CON_PAYMENT_TYPE, $CON_SEND_CP_ADDR, $GOODS_DELIVERY_NAME, $DELIVERY_CP, $CON_DELIVERY_TYPE, $DELIVERY_FEE, $DELIVERY_FEE_CODE, $s_adm_no);

			}
		
		}
			
?>
<script language="javascript">
		alert("������� �Ϸ� �Ǿ����ϴ�. �ù�� �ε����� ���ε��ϼż� ���� ����ϼ���.");
		window.location.replace("/manager/company/pop_company_extra.php?cp_no=<?=$cp_no?>");
</script>
<?
		exit();
	}


	if ($mode == "D") {

		$row_cnt = count($chk_no);
		//echo $row_cnt;

		for ($k = 0; $k < $row_cnt; $k++) {
		
			$temp_ce_no = $chk_no[$k];
						
			$result = deleteCompanyExtra($conn, $temp_ce_no, $s_adm_no);
		
		}
		
		if($result) { 
	?>	
	<script language="javascript">
		alert('�����׸��� ���� �Ǿ����ϴ�.');
		window.location.replace("/manager/company/pop_company_extra.php?cp_no=<?=$cp_no?>");
	</script>
	<?
		}
		exit;
	}

	//�ּ��߰������� �⺻����
	if($tab_index == "")
		$tab_index = 0;


	$arr_rs = listCompanyExtra($conn, $cp_no);

	$arr_op_cp = getOperatingCompany($conn, $s_adm_com_code);

	$OP_CP_NM		= $arr_op_cp[0]["CP_NM"];
	$OP_CP_PHONE	= $arr_op_cp[0]["CP_PHONE"];

	if($SENDER_NM == "")
		$SENDER_NM = $OP_CP_NM;
	
	if($SENDER_PHONE == "")
		$SENDER_PHONE = $OP_CP_PHONE;
		
	$arr_sticker = listOrderGoodsDeliveryForStickerWithoutDeliveryNo($conn, $cp_no);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

<script>

    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {

                // �˾����� �˻���� �׸��� Ŭ�������� ������ �ڵ带 �ۼ��ϴ� �κ�.
                // �� �ּ��� ���� ��Ģ�� ���� �ּҸ� �����Ѵ�.
                // �������� ������ ���� ���� ��쿣 ����('')���� �����Ƿ�, �̸� �����Ͽ� �б� �Ѵ�.
                var fullAddr = ''; // ���� �ּ� ����
                var extraAddr = ''; // ������ �ּ� ����

                // ����ڰ� ������ �ּ� Ÿ�Կ� ���� �ش� �ּ� ���� �����´�.
                if (data.userSelectedType === 'R') { // ����ڰ� ���θ� �ּҸ� �������� ���
                    fullAddr = data.roadAddress;

                } else { // ����ڰ� ���� �ּҸ� �������� ���(J)
                    fullAddr = data.jibunAddress;
                }

                // ����ڰ� ������ �ּҰ� ���θ� Ÿ���϶� �����Ѵ�.
                if(data.userSelectedType === 'R'){
                    //���������� ���� ��� �߰��Ѵ�.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // �ǹ����� ���� ��� �߰��Ѵ�.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // �������ּ��� ������ ���� ���ʿ� ��ȣ�� �߰��Ͽ� ���� �ּҸ� �����.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
								
			    // �����ȣ�� �ּ� ������ �ش� �ʵ忡 �ִ´�.
				document.getElementById("addr").value = fullAddr;
				// Ŀ���� ���ּ� �ʵ�� �̵��Ѵ�.
				document.getElementById("addr").focus();


            }
        }).open();
    }

		function js_addr_open() {
			sample6_execDaumPostcode();
		}

</script>  
<script>
	$(function() {
		$( ".datepicker" ).datepicker({
		    buttonImage: "/manager/images/calendar/cal.gif",
		    buttonImageOnly: true,
		    buttonText: "Select date",
			showOn: "both",
			dateFormat: "yy-mm-dd",
			numberOfMonths: 2,
			changeMonth: true,
			changeYear: true
		});
	});
</script>
<script>
	$(function() {
		$("#tabs").tabs({
		  active : <?=$tab_index?>
		});
	});
</script>
<script language="javascript">

	function js_create_delivery_paper()
	{
		var frm = document.frm;

		var cntChecked = 0;
		$("input[name='chk_no[]']:checked").each(function ()
		{
			cntChecked ++;
		});
		
		if(cntChecked < 1) {
			alert("�ּ� 1���̻� �����Ͽ� �ּ���.");
			return;

		} else {

			if($("select[name=DELIVERY_CP]").val() == "")
			{
				alert('�ù�ȸ�縦 �������ּ���.');
				return;
			}

			if(frm.GOODS_DELIVERY_NAME.value == "")
			{
				alert('���峻���� �� �Է����ּ���.');
				return;
			}

			frm.mode.value = "I_DELIVERY_PAPER";
			frm.target = "";
			frm.method = "get";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}

	}

	function js_save()
	{
		var frm = document.frm;

		if(frm.memo.value == "")
		{
			alert('���и޸� �� �Է����ּ���.');
			return;
		}

		frm.mode.value = "I";
		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();

	}

	
	function js_delete() {

		var frm = document.frm;

		var cntChecked = 0;
		$("input[name='chk_no[]']:checked").each(function ()
		{
			cntChecked ++;
		});
		
		if(cntChecked < 1) {
			alert("�ּ� 1���̻� �����Ͽ� �ּ���.");
			return;

		} else {

			if (confirm("���ó����� �����˴ϴ�. ��� �����Ͻðڽ��ϱ�?")) {
				frm.mode.value = "D";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			}
		}
	}

	
	function js_excel() {

		var frm = document.frm;
		
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_update_delivery_paper(order_goods_delivery_no) {

		var url = "/manager/order/pop_delivery_paper_detail.php?order_goods_delivery_no=" + order_goods_delivery_no;

		NewWindow(url, 'delivery_paper_detail','1000','500','YES');
		
	}

	function js_pop_delivery_paper_frame(delivery_cp, delivery_no) { 

		var url = "/manager/order/pop_delivery_paper_wrapper.php?delivery_cp=" + delivery_cp + "&delivery_no=" + delivery_no;
		NewWindow(url, 'pop_delivery_paper_wrapper', '920', '700', 'YES');

	}
</script>
<style>
	input[type=text].list {display:none; width:90%;} 
</style>
<script>
	$(function(){
		
		$(".data_list td, th").on("click", function(){

			//if($(this).closest("tr").prop("class") == "delivered") { 
			//	alert("���ó�� �� ������ ������ �� �����ϴ�.");
			//	return;
			//}

			//var source = $(this).prop("title");
			var text_box = $(this).find("input[type=text].list");

			$(this).find("span").hide();
			text_box.val($(this).prop("title")).show().focus();

		});

		$("input[type=text].list").on("keydown", function(){
			$(this).parent().find("span").html($(this).val());

			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){

				var ce_no = $(this).closest("tr").data("ce_no");
				var column = $(this).data("column");
				var value = $(this).val();

				(function() {
				  $.getJSON( "/manager/company/json_company_extra.php", {
					mode: "UPDATE_COMPANY_EXTRA",
					ce_no: ce_no,
					column: column,
					value : value
				  })
					.done(function( data ) {
					  $.each( data, function( i, item ) {
						  if(item.RESULT == "0")
							  alert('������� : ����� �ٽ� �õ����ּ���');
					  });
					});
				})();


				$("span").show();
				$("input[type=text].list").hide();
			}
		});

		$("input[type=text].list").on("blur", function(){
			$("span").show();
			$("input[type=text].list").hide();
		});
	});
</script>
</head>
<style>
body#popup_order_wide {width:100%;}
.delivered {background-color:#EFEFEF;}
tr.not_used > td {color: #A2A2A2;}
.color_yellow{background-color:yellow;}
</style>
<body id="popup_order_wide">

<div id="popupwrap_order_wide">
	<h1>��ü �߰� �ּ���</h1>
	<div id="postsch_code">

		<div class="addr_inp">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="cp_no" value="<?=$cp_no?>">
	
	<div id="tabs" style="width:95%; margin:10px 0;">
		<ul>
			<li><a href="#tabs-1">�߰��ּ� �Է�</a></li>
			<li><a href="#tabs-2">���� ����</a></li>
		</ul>
		<div id="tabs-1">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
			<colgroup>
				<col width="12%" />
				<col width="33%" />
				<col width="12%" />
				<col width="33%" />
				<col width="*" />
			</colgroup>
			<tr>
				<th>���� �޸�</th>
				<td class="line"><input type="Text" name="memo" value="" style="width:90%;" class="txt"></td>
				
				<th>����� ��</th>
				<td class="line" colspan="2"><input type="Text" name="manager_nm" value="" style="width:160px;" class="txt"></td>
				
			</tr>
			<tr>
				<th>��ȭ��ȣ</th>
				<td class="line">
					<input type="Text" name="phone" value="" style="width:160px;" class="txt" onkeyup="return isPhoneNumber(this)">
				</td>
				<th>�޴� ��ȭ��ȣ</th>
				<td class="line">
					<input type="Text" name="hphone" value="" style="width:160px;" class="txt" onkeyup="return isPhoneNumber(this)">
				</td>
				<td class="line" rowspan="2">
					<input type="button" name="b" value=" �߰��ּ� �Է� " onclick="js_save();">
				</td>
			</tr>
			<tr>
				<th>�ּ�</th>
				<td colspan="3" class="line">
					<input type="Text" name="addr" id="addr" value="" style="width:65%;" class="txt">
					<a href="#none" onClick="js_addr_open('');"><img src="/manager/images/admin/btn_filesch.gif" alt="ã��" align="absmiddle" /></a>
				</td>
			<tr>
			</table>
		</div>
		<div id="tabs-2">
			<table cellpadding="0" cellspacing="0" width="100%" class="colstable02">
				<colgroup>
					<col width="12%" />
					<col width="33%" />
					<col width="12%" />
					<col width="33%" />
					<col width="*" />
				</colgroup>
				<tbody>
					<tr>
						<th>���������</th>
						<td class="line">
							<?
								$chk_work_date = date("Y-m-d",strtotime("0 day"));
							?>
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="chk_work_date" value="<?=$chk_work_date?>" maxlength="10"/>
						</td>
						<th>�ù�ȸ��</th>
						<td class="line"><?=makeSelectBoxOnChange($conn,"DELIVERY_CP", "DELIVERY_CP","90", "�ù�ȸ�缱��", "", $DELIVERY_CP)?>
						<?	if($DELIVERY_CP == "") { ?>
							<script type="text/javascript">
								$("select[name=DELIVERY_CP] option:nth-child(2)").attr('selected','selected');
							</script>
						<? } ?>
						
						</td>
						
						<td class="line" rowspan="3">
							<input type="button" name="b" value=" ���� ���� " onclick="js_create_delivery_paper();">
						</td>
					</tr>
					<tr>
						<th>���� ǥ�� ����</th>
						<td class="line" colspan="3"><input type="text" style="width:90%;" name="GOODS_DELIVERY_NAME" value="��ƼĿ" /></td>
					</tr>
					<tr>
						<th>�����»��</th>
						<td class="line"><input type="text" name="SENDER_NM" value="<?=$SENDER_NM?>" /></td>
						<th>������ ��ȣ</th>
						<td class="line"><input type="text" name="SENDER_PHONE" value="<?=$SENDER_PHONE?>" />
					</tr>
				</tbody>
			</table> 
		</div>
	</div>
	<div class="sp10"></div>
	<div style="text-align:right; width:95%;">
		<input type="button" name="b" value="����(����)" onclick="js_delete();">
	</div>
	<div class="sp10"></div>
	<h2> ��ü �߰� �ּ� ����Ʈ </h2>
	<table cellpadding="0" cellspacing="0" width="90%" class="rowstable data_list">
	<colgroup>
		<col width="5%" />
		<col width="10%" />
		<col width="10%" />
		<col width="10%" />
		<col width="10%" />
		<col width="*" />
	</colgroup>
	<thead>
		<tr>
			<th></th>
			<th>���и޸�</th>
			<th>������</th>
			<th>����ó</th>
			<th>�޴�����ȣ</th>
			<th>�ּ�</th>
		</tr>
	</thead>
	<tbody>
	
	<?
	if(sizeof($arr_rs) >= 1) {
		for($i = 0; $i < sizeof($arr_rs); $i ++) { 

			//MANAGER_NM, PHONE, HPHONE, ADDR, MEMO

			$CE_NO					= trim($arr_rs[$i]["CE_NO"]);
			$MANAGER_NM				= trim($arr_rs[$i]["MANAGER_NM"]);
			$PHONE					= trim($arr_rs[$i]["PHONE"]);
			$HPHONE					= trim($arr_rs[$i]["HPHONE"]);
			$ADDR					= trim($arr_rs[$i]["ADDR"]);
			$MEMO					= trim($arr_rs[$i]["MEMO"]);
	?>
		<tr height="35" data-ce_no="<?=$CE_NO?>">
			<td><input type="checkbox" name="chk_no[]" class="chk" value="<?=$CE_NO?>"><br/><?=$CE_NO?></td>
			<td title="<?=$MEMO?>"><span><?=$MEMO?></span><input type="text" class="list"  data-column="memo" value=""/></td>
			<td title="<?=$MANAGER_NM?>"><span><?=$MANAGER_NM?></span><input type="text" class="list" data-column="manager_nm" value=""/></td>
			<td title="<?=$PHONE?>"><span><?=$PHONE?></span><input type="text" class="list"  data-column="phone" value=""/></td>
			<td title="<?=$HPHONE?>"><span><?=$HPHONE?></span><input type="text" class="list"  data-column="hphone" value=""/></td>
			<td title="<?=$ADDR?>"><span><?=$ADDR?></span><input type="text" class="list"  data-column="addr" value=""/></td>
		</tr>
	
	<?
		}
	} else {

	?>
		<tr>
			<td colspan="6" height="50" align="center">�����Ͱ� �����ϴ�</td>
		</tr>
	<?

	}
	
	?>
	
	</tbody>
	</table>
	

	<script>

		$(function(){

			//��ü �ε��� Ŭ�� ����
			$("input[name=all_chk]").show();

		});

		var last_click_idx = -1;
		$(".chk").click(function(event){
			
			var clicked_elem = $(this);
			var clicked_elem_chked = $(this).prop("checked");

			var start_idx = -1;
			var end_idx = -1;
			var click_idx = -1;

			$(".chk").each(function( index, elem ) {

				//Ŭ����ġ ����
				if(clicked_elem.val() == $(elem).val())
					click_idx = index;

			});

			if(event.shiftKey) {

				if($(".chk:checked").size() >= 2) {
					$(".chk").each(function( index, elem ) {

						//üũ�� ���� ���� üũ
						if(start_idx == -1 && $(elem).prop("checked"))
							start_idx = index;

						//üũ�� ������ �ε��� üũ
						if($(elem).prop("checked"))
							end_idx = index;

					});

					if($(".chk:checked").size() > 2 && last_click_idx > click_idx)
						start_idx = click_idx;

					if($(".chk:checked").size() > 2 && last_click_idx < click_idx)
						end_idx = click_idx;


					//alert("start_idx: " + start_idx + ", end_idx: " + end_idx + ", click_idx: " + click_idx+ ", last_click_idx: " + last_click_idx);

					
					$(".chk").each(function(index, elem) {

						if(start_idx <= index && index <= end_idx) {
							$(elem).prop("checked", true);
						}
						else
							$(elem).prop("checked", false);
						
					});
					
				}

				last_click_idx = click_idx;
			}

		});

		
	
	</script>

	
	<!-- ������ ��ƼĿ ���� Ȯ�� (�����ȣ ����) -->
	<div class="sp40"></div>
	<h2> ������ ��ƼĿ ���� ����Ʈ (�����ȣ ����) </h2>
	<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table" border="0">
		<colgroup>
			<col width="2%" />
			<col width="7%" />
			<col width="8%" />
			<col width="8%" />
			<col width="10%" />
			<col width="*" />
			<col width="25%" />
			<col width="8%" />
			<col width="7%" />
			<col width="5%" />

		</colgroup>
		<thead>
			<tr>
				<th rowspan="2"><!--<input type="checkbox" name="all_chk" onClick="js_all_check();">--></th>
				<th rowspan="2">����ȣ</th>
				<th>�����ȣ</th>
				<th>������</th>
				<th>��������ȭ��ȣ</th>
				<th rowspan="2">��ǰ��</th>
				<th>�������ּ�</th>
				<th>�Ǹ�ó</th>
				<th>���Ŭ����</th>	
				<th rowspan="2" class="end">�����</th>
			</tr>
			<tr>
				<th>�ù��</th>
				<th>�ֹ����̸�</th>
				<th>�������ڵ�����ȣ</th>
				<th>�޸�</th>
				<th>�Ǹ�ó��ȭ</th>
				<th>����Ÿ��</th>	
			</tr>
		</thead>
		<tbody>
		<?
			$nCnt = 0;
			
			if (sizeof($arr_sticker) > 0) {
				for ($j = 0 ; $j < sizeof($arr_sticker); $j++) {
					
					//$CP_ORDER_NO	= trim($arr_sticker[$j]["CP_ORDER_NO"]);

					$ORDER_GOODS_DELIVERY_NO	= trim($arr_sticker[$j]["ORDER_GOODS_DELIVERY_NO"]);
					$DELIVERY_CNT				= trim($arr_sticker[$j]["DELIVERY_CNT"]);
					$SEQ_OF_DELIVERY			= trim($arr_sticker[$j]["SEQ_OF_DELIVERY"]);
					$DELIVERY_SEQ				= trim($arr_sticker[$j]["DELIVERY_SEQ"]);
					$SEQ_OF_DAY					= trim($arr_sticker[$j]["SEQ_OF_DAY"]);

					$RECEIVER_NM				= trim($arr_sticker[$j]["RECEIVER_NM"]);
					$RECEIVER_PHONE				= trim($arr_sticker[$j]["RECEIVER_PHONE"]);
					$RECEIVER_HPHONE			= trim($arr_sticker[$j]["RECEIVER_HPHONE"]);
					$RECEIVER_ADDR				= trim($arr_sticker[$j]["RECEIVER_ADDR"]);
					$ORDER_QTY					= trim($arr_sticker[$j]["ORDER_QTY"]);

					$MEMO						= trim($arr_sticker[$j]["MEMO"]);
					$ORDER_NM					= trim($arr_sticker[$j]["ORDER_NM"]);
					$ORDER_PHONE				= trim($arr_sticker[$j]["ORDER_PHONE"]);
					$ORDER_MANAGER_NM			= trim($arr_sticker[$j]["ORDER_MANAGER_NM"]);
					$ORDER_MANAGER_PHONE		= trim($arr_sticker[$j]["ORDER_MANAGER_PHONE"]);

					$PAYMENT_TYPE				= trim($arr_sticker[$j]["PAYMENT_TYPE"]);
					$SEND_CP_ADDR				= trim($arr_sticker[$j]["SEND_CP_ADDR"]);
					$GOODS_DELIVERY_NAME		= trim($arr_sticker[$j]["GOODS_DELIVERY_NAME"]);
					$DELIVERY_CP				= trim($arr_sticker[$j]["DELIVERY_CP"]);
					$DELIVERY_NO				= trim($arr_sticker[$j]["DELIVERY_NO"]);
					
					$DELIVERY_TYPE				= trim($arr_sticker[$j]["DELIVERY_TYPE"]);
					$DELIVERY_DATE				= trim($arr_sticker[$j]["DELIVERY_DATE"]);
					$DELIVERY_FEE				= trim($arr_sticker[$j]["DELIVERY_FEE"]);
					$DELIVERY_FEE_CODE			= trim($arr_sticker[$j]["DELIVERY_FEE_CODE"]);
					$DELIVERY_CLAIM_CODE		= trim($arr_sticker[$j]["DELIVERY_CLAIM_CODE"]);
					$DELIVERY_CLAIM				=  getDcodeName($conn, 'DELIVERY_CLAIM', $DELIVERY_CLAIM_CODE);

					$USE_TF						= trim($arr_sticker[$j]["USE_TF"]);
					$DEL_TF						= trim($arr_sticker[$j]["DEL_TF"]);
					$REG_ADM					= trim($arr_sticker[$j]["REG_ADM"]);

					$REG_DATE					= trim($arr_sticker[$j]["REG_DATE"]);
				?>
				<tr height="37">
					<td rowspan="2">
					<?
						if ($DELIVERY_NO == "") {
					?>
						<!--<input type="checkbox" name="chk_no[]" value="<?=$ORDER_GOODS_DELIVERY_NO?>">-->
					<? } ?>
					</td>
					<td rowspan="2"><a href="javascript:js_update_delivery_paper('<?=$ORDER_GOODS_DELIVERY_NO?>')"><?=$DELIVERY_SEQ?></a></td>
					<td><a href="javascript:js_pop_delivery_paper_frame('<?=$DELIVERY_CP?>', '<?=$DELIVERY_NO?>');" style="font-weight:bold;" title="<?=$DELIVERY_NO?>"><?=$DELIVERY_NO?></a>
					</td>
					<td class="modeual_nm"><?=$RECEIVER_NM?> </td>
					<td><?=$RECEIVER_PHONE?> </td>
					<td rowspan="2"><?=$GOODS_DELIVERY_NAME?> </td>
					<td class="modeual_nm"><?=$RECEIVER_ADDR?> </td>
					<td class="modeual_nm"><?=$ORDER_MANAGER_NM?> </td>
					<td><?=$DELIVERY_CLAIM?> </td>
					<td rowspan="2"><?=$REG_DATE?> </td>
				</tr>
				<tr height="37">
					<td><?=$DELIVERY_CP?> </td>
					<td class="modeual_nm"><?=$ORDER_NM?> </td>
					<td><?=$RECEIVER_HPHONE?> </td>
					<td class="modeual_nm"><?=$MEMO?> </td>
					<td><?=$ORDER_MANAGER_PHONE?> </td>
					<td><?=$DELIVERY_FEE?> </td>
				</tr>
				<?
						}
					} else { 
			?>
				<tr height="37">
					<td colspan="9">�����Ͱ� �����ϴ�.</td>
				</tr>

			<?      } ?>
		</tbody>
	</table>
	<div class="sp10"></div>

<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="about:blank" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
<div class="bot_close" style="width:auto;"><a href="javascript: window.close();"><img src="../images/admin/icon_pclose.gif" alt="�ݱ�" /></a></div>
</div>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>