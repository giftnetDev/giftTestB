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
	$menu_right = "SG017"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/stock/stock.php";
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/confirm/confirm.php";

#====================================================================
# Request Parameter
#====================================================================


	if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-1 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}

	$day_0 = date("Y-m-d",strtotime("0 month"));
	$day_1 = date("Y-m-d",strtotime("-1 day"));
	$day_7 = date("Y-m-d",strtotime("-7 day"));
	$day_31 = date("Y-m-d",strtotime("-1 month"));

	#List Parameter
	$nPage			= trim($nPage);
	$nPageSize	= trim($nPageSize);

	$search_field		= trim($search_field);
	$search_str			= trim($search_str);
	
#============================================================
# Page process
#============================================================

	if ($nPage <> "") {
		$nPage = (int)($nPage);
	} else {
		$nPage = 1;
	}

	if ($nPageSize <> "") {
		$nPageSize = (int)($nPageSize);
	} else {
		$nPageSize = 20;
	}

	$nPageBlock	= 10;

#===============================================================
# Get Search list count
#===============================================================


	if ($mode == "I") {
		
		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_rg_no[$x];

			//* ���� ���� ����
			//���Ա���
			$result = insertCompanyLedgerDepositFromGoodsRequest($conn, $temp_req_no, $s_adm_no);

			//����Ȯ�� ǥ�� 
			if($result)
				updateGoodsRequestConfirm($conn, $temp_req_no, $s_adm_no);
		}
	}

/*
	if ($mode == "U") {
		
		$arrlength = count($req_qty);

		for($x = 0; $x < $arrlength; $x++) {
			
			$temp_req_no = $req_no[$x];
			$temp_req_qty = $req_qty[$x];
			$temp_receive_qty = $receive_qty[$x];
			$temp_reason = $reason[$x];

			UpdateRequestGoods($conn, $temp_req_no, $temp_req_qty, $temp_receive_qty, $temp_reason);
		}
	}
*/

	if ($mode == "D") {

		$arrlength = count($chk_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_no[$x];

			DeleteGoodsRequest($conn, $temp_req_no, $s_adm_no);
		}
	}

	if ($mode == "C") {

		$arrlength = count($chk_rg_no);

		for($x = 0; $x < $arrlength; $x++) {
			$temp_req_no = $chk_rg_no[$x];

			UpdateGoodsRequestGoodsStatus($conn, $temp_req_no, $s_adm_no);
		}
		
	}

	$filter_tf = "Y";

	$nListCnt =totalCntGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter_tf, $search_field, $search_str, $exclude_category, $order_field, $order_str);

	$nTotalPage = (int)(($nListCnt - 1) / $nPageSize + 1) ;

	if ((int)($nTotalPage) < (int)($nPage)) {
		$nPage = $nTotalPage;
	}

	$arr_rs = listGoodsRequest($conn, $start_date, $end_date, $con_cp_type, $filter_tf, $search_field, $search_str, $order_field, $order_str, $nPage, $nPageSize, $nListCnt);

	$arr_stat = cntRequestGoodsState($conn);

	$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
	$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script type="text/javascript" src="../jquery/jquery.floatThead.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script>
  $(function() {
    $( ".datepicker" ).datepicker({
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true,
	  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
	  }
    });

	$(".datepicker").keydown(function(){

		var value = $(this).val();

		if(value.length == 4 && value.lastIndexOf('-') == -1)
			$(this).val(value.substr(0, 4)+ "-" + value.substr(4)) ;

		if(value.length == 7 && value.lastIndexOf('-') == 4)
			$(this).val(value.substr(0, 8) + "-" + value.substr(8)) ;
	});

	$(".datepicker").blur(function(){
		if($(this).val().length > 8)
			checkStaEndDt($("input[name=start_date]"), $("input[name=end_date]"));
	});
  });

  </script>
  <script>
		$(function() {
			$('.autocomplete_off').attr('autocomplete', 'off');
		});
  </script>
  <script>
	$(function(){
		$('table.fixed_header_table').floatThead({
			position: 'fixed'
		});
	});
  </script>
  <script type="text/javascript" >

	function js_write() {

		var frm = document.frm;
		
		//var url = "pop_goods_request_write.php";

		//NewWindow(url, 'pop_goods_request_write','860','600','YES');
		
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_request_write.php";
		frm.submit();
		
	}

	function js_view(req_no) {

		var frm = document.frm;

		frm.req_no.value = req_no;
		frm.target = "";
		frm.method = "get";
		frm.action = "goods_request_write.php";
		frm.submit();

		//var url = "pop_goods_request.php";

		//NewWindow(url, 'pop_goods_request','860','600','YES');
		
		
	}
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";

		frm.target = "";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_excel() {
		
		//alert("�غ��� �Դϴ�..");
		//return;

		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.target = "";
		frm.action = "<?=str_replace("list","excel_list",$_SERVER[PHP_SELF])?>";
		frm.submit();

	}

	function js_all_check() {
		var frm = document.frm;
		
		if (frm['chk_no[]'] != null) {
			
			if (frm['chk_no[]'].length != null) {

				if (frm.all_chk.checked == true) {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = true;
					}
				} else {
					for (i = 0; i < frm['chk_no[]'].length; i++) {
						frm['chk_no[]'][i].checked = false;
					}
				}
			} else {
			
				if (frm.all_chk.checked == true) {
					frm['chk_no[]'].checked = true;
				} else {
					frm['chk_no[]'].checked = false;
				}
			}
		}
	}

	function js_delete() {
		var frm = document.frm;

		bDelOK = confirm('������ ���ָ� �����Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "D";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	function js_cancel() {
		var frm = document.frm;

		bDelOK = confirm('������ ���ָ� ����Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {
			
			frm.mode.value = "C";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}

	//����Ȯ��
	function js_deposit() {
		var frm = document.frm;

		var selected_cnt = $("input[name='chk_rg_no[]']:checked").length;

		if(selected_cnt > 0) 
		{
			if(selected_cnt > 1) 
			{
				if (confirm('������ ���ָ� ����Ȯ���Ͻðڽ��ϱ�?'))
				{
					frm.mode.value = "I";
					frm.target = "";
					frm.action = "<?=$_SERVER[PHP_SELF]?>";
					frm.submit();
				}
			} else { 

				var rg_no = $("input[name='chk_rg_no[]']:checked").val();
				//NewDownloadWindow("pop_goods_request_ledger_write.php", {rg_no : rg_no}, "get");
				
				//window.open("/manager/stock/pop_goods_request_ledger_write.php?rg_no=" + rg_no,'_blank');

				frm.rg_no.value = rg_no;

				NewWindow('about:blank', 'pop_goods_request_ledger_write', '1000', '413', 'YES');
				frm.target = "pop_goods_request_ledger_write";
				frm.action = "/manager/stock/pop_goods_request_ledger_write.php";
				frm.submit();

			}

		}
		else
			alert('���õ� ���ְ� �����ϴ�');
	}


	var day_0 = "<?=$day_0?>";
	var day_1 = "<?=$day_1?>";
	var day_7 = "<?=$day_7?>";
	var day_31 = "<?=$day_31?>";

	function js_search_date(iday) {

		var frm = document.frm;
		
		if (iday == 0) {
			frm.start_date.value = day_0;
			frm.end_date.value = day_0;
		}

		if (iday == 1) {
			frm.start_date.value = day_1;
			frm.end_date.value = day_0;
		}

		if (iday == 7) {
			frm.start_date.value = day_7;
			frm.end_date.value = day_0;
		}

		if (iday == 31) {
			frm.start_date.value = day_31;
			frm.end_date.value = day_0;
		}

		frm.nPage.value = "1";
		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_trace(url) {

		window.open(url);
		//alert(url);
	}

	function js_link_to_in_stock(request_goods_no, start_date) { 

		window.open("/manager/stock/in_list.php?nPage=1&order_field=REG_DATE&order_str=DESC&nPageSize=20&search_field=RESERVE_NO&search_str=RGN%3A" + request_goods_no + "&start_date=" + start_date,'_blank');

	}

</script>
<style>
	.top_group td {border-top: 2px solid black; border-bottom: 1px solid black; font-weight:bold;}
	table.rowstable td {background: none;}
	.confirm_order {background: #EFEFEF;}
</style>
</head>

<body id="admin">

<form name="frm" method="post" onSubmit="js_search(); return true;">
<input type="hidden" name="depth" value="" />
<input type="hidden" name="req_no" value="">
<input type="hidden" name="rg_no" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="nPage" value="<?=$nPage?>">
<input type="hidden" name="keyword" value="">
<input type="hidden" name="group_no" value="">
<!--<input type="hidden" name="nPageSize" value="<?=$nPageSize?>">-->

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
				
				<h2>���� ����</h2>
				<div class="btnright">
					<a href="javascript:js_write();"><img src="../images/admin/btn_regist_02.gif" alt="���"></a>
				</div>

				<table cellpadding="0" cellspacing="0" class="colstable">
				<colgroup>
					<col width="10%" />
					<col width="37%" />
					<col width="10%" />
					<col width="37%" />
					<col width="6%" />
				</colgroup>
				<tbody>
					<tr>
						<th>��������</th>
						<td colspan="4">
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="start_date" value="<?=$start_date?>" maxlength="10"/>
							 ~ 
							<input type="text" class="txt datepicker" style="width: 80px; margin-right:3px;" name="end_date" value="<?=$end_date?>" maxlength="10"/>
							&nbsp;<a href="javascript:js_search_date('0');"><img src="/manager/images/bu/btn_s_dday.gif" alt="" /></a>
							<a href="javascript:js_search_date('1');"><img src="/manager/images/bu/btn_s_bday.gif" alt="" /></a>
							<a href="javascript:js_search_date('7');"><img src="/manager/images/bu/btn_s_7day.gif" alt="" /></a>
							<a href="javascript:js_search_date('31');"><img src="/manager/images/bu/btn_s_1mon.gif" alt="" /></a>
						</td>
					</tr>
					<tr>
						<th>���Ծ�ü</th>
						<td>
							<input type="text" class="autocomplete_off" style="width:90%" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" name="txt_cp_type" value="<?=getCompanyAutocompleteTextBox($conn,'',$con_cp_type)?>" />
							<input type="hidden" name="con_cp_type" value="<?=$con_cp_type?>">

							<script>
								$(function(){

									$("input[name=txt_cp_type]").keydown(function(e){

										if(e.keyCode==13) { 

											var keyword = $(this).val();
											if(keyword == "") { 
												$("input[name=con_cp_type]").val('');
												js_search();
											} else { 
												$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE,MEMO", function(data) {
													if(data.length == 1) { 
														
														js_selecting_company("txt_cp_type", data[0].label, "con_cp_type", data[0].id);

													} else if(data.length > 1){ 
														NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=con_cp_type",'pop_company_searched_list','950','650','YES');

													} else 
														alert("�˻������ �����ϴ�.");
												});
											}
										}

									});

									$("input[name=txt_cp_type]").keyup(function(e){
										var keyword = $(this).val();

										if(keyword == "") { 
											$("input[name=con_cp_type]").val('');
										}
									});

								});

							</script>
							<script>
								function js_selecting_company(target_name, cp_nm, target_value, cp_no) {
									
									$(function(){

										$("input[name="+target_name+"]").val(cp_nm);
										$("input[name="+target_value+"]").val(cp_no);

									});

									js_search();
								}
							</script>
						</td>
						<td colspan="2">
							<b>��ۿ���:</b>
							<select name="con_delivery_tf">
								<option value="" <? if ($con_delivery_tf == "") echo "selected"; ?> >��ü</option>
								<option value="N" <? if ($con_delivery_tf == "N") echo "selected"; ?> >�߼���</option>
								<option value="Y" <? if ($con_delivery_tf == "Y") echo "selected"; ?> >�߼���</option>
							</select>
							<b>����ó:</b>
							<select name="con_to_here">
								<option value="" <? if ($con_to_here == "") echo "selected"; ?> >��ü</option>
								<option value="Y" <? if ($con_to_here == "Y") echo "selected"; ?> >��ü����</option>
								<option value="N" <? if ($con_to_here == "N") echo "selected"; ?> >����</option>
							</select>
							<b>��ҿ���:</b>
							<select name="con_cancel_tf">
								<option value="" <? if ($con_cancel_tf == "") echo "selected"; ?> >��ü</option>
								<option value="N" <? if ($con_cancel_tf == "N") echo "selected"; ?> >��ȿ��</option>
								<option value="Y" <? if ($con_cancel_tf == "Y") echo "selected"; ?> >��Ҹ�</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>����</th>
						<td>
							<select name="order_field" style="width:84px;">
								<option value="REG_DATE" <? if ($order_field == "REG_DATE") echo "selected"; ?> >�����</option>
								<option value="REQ_DATE" <? if ($order_field == "REQ_DATE") echo "selected"; ?> >������</option>
								<option value="BUY_CP_NM" <? if ($order_field == "BUY_CP_NM") echo "selected"; ?> >���Ծ�ü</option>
								<option value="BUY_MANAGER_NM" <? if ($order_field == "BUY_MANAGER_NM") echo "selected"; ?> >���Դ����</option>
							</select>&nbsp;&nbsp;
							<input type='radio' class="" name='order_str' value='ASC' <? if (($order_str == "ASC")) echo " checked"; ?> > �������� &nbsp;
							<input type='radio' name='order_str' value='DESC' <? if (($order_str == "DESC") || ($order_str == "")) echo " checked"; ?>> ��������
						</td>
						<th>�˻�����</th>
						<td>
							<select name="nPageSize" style="width:84px;">
								<option value="20" <? if ($nPageSize == "20") echo "selected"; ?> >20����</option>
								<option value="50" <? if ($nPageSize == "50") echo "selected"; ?> >50����</option>
								<option value="100" <? if ($nPageSize == "100") echo "selected"; ?> >100����</option>
							</select>&nbsp;
							<select name="search_field" style="width:84px;">
								<option value="ALL" <? if ($search_field == "ALL") echo "selected"; ?> >���հ˻�</option>
								<option value="BUY_CP_NM" <? if ($search_field == "BUY_CP_NM") echo "selected"; ?> >���Ծ�ü</option>
								<option value="BUY_MANAGER_NM" <? if ($search_field == "BUY_MANAGER_NM") echo "selected"; ?> >���Դ����</option>
								<option value="BUY_CP_PHONE" <? if ($search_field == "BUY_CP_PHONE") echo "selected"; ?> >���Կ���ó</option>
								<option value="REQ_GOODS_NO" <? if ($search_field == "REQ_GOODS_NO") echo "selected"; ?> >���ֻ�ǰ��ȣ</option>
								<option value="GOODS_NAME" <? if ($search_field == "GOODS_NAME") echo "selected"; ?> >��ǰ��/�ڵ�</option>
								<option value="MEMO2" <? if ($search_field == "MEMO2") echo "selected"; ?> >���2</option>
							</select>&nbsp;

							<input type="text" value="<?=$search_str?>" name="search_str" size="20" class="txt" onmouseup="return false;" onfocus="this.select();" onkeydown = "if(event.keyCode==13) js_search();" />
							<a href="javascript:js_search();"><img src="/manager/images/admin/btn_search.gif" alt="go"/></a>
						</td>
						<td align="right">
							<a href="javascript:js_excel();"><img src="../images/common/btn/btn_excel.gif" alt="���� ����Ʈ" /></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="sp20"></div>
				<div style="width: 95%; text-align: right; margin: 0 0 10px 0;">
					<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "�") {?>
						<input type="button" name="aa" value=" ������ ���� ����Ȯ�� " class="btntxt" onclick="js_deposit();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" ������ ���ּ� ��ü���� " class="btntxt" onclick="js_delete();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" ������ ���� ��ǰ ��� " class="btntxt" onclick="js_cancel();">
					<? } ?>
				</div>
				<?
					for($o = 0; $o < sizeof($arr_stat); $o++) { 
						$cnt		= $arr_stat[$o]["CNT"];
						$grg_type   = $arr_stat[$o]["GRG_TYPE"];

						echo "<b><font color='blue'>".$grg_type."</font> <font color='red'>".$cnt."</font> <font color='blue'>��</font></b>&nbsp;&nbsp;";
					}
				?>
				<table cellpadding="0" cellspacing="0" class="rowstable fixed_header_table">
					<!--
					<colgroup>
						<col width="5%" />
						<col width="7%" />
						<col width="*" />
						<col width="10%" />
						<col width="5%" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					-->
					<thead>
						<tr>
							<th>���ֹ�ȣ</th>
							<th>���Ծ�ü</th>
							<th>��ǰ��</th>
							<th>�ܰ�</th>
							<th>����</th>
							<th>�����հ�</th>
							<th>�߼���</th>
							<th>��ǰó</th> 
							<th>���2</th>
							<th>�԰����</th>
							<th class="end">�԰�ó���� / ��ҿ���</th>
						</tr>
					</thead>
					<tbody>

				<?
					$nCnt = 0;
					
					if (sizeof($arr_rs) > 0) {
						
						for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
							
							$rn							= trim($arr_rs[$j]["rn"]);
							$REQ_NO						= trim($arr_rs[$j]["REQ_NO"]);
							$GROUP_NO					= trim($arr_rs[$j]["GROUP_NO"]);
							$REQ_DATE					= trim($arr_rs[$j]["REQ_DATE"]);
							$BUY_CP_NO					= trim($arr_rs[$j]["BUY_CP_NO"]);
							$BUY_CP_NM					= trim($arr_rs[$j]["BUY_CP_NM"]);
							$BUY_MANAGER_NM				= trim($arr_rs[$j]["BUY_MANAGER_NM"]);
							$BUY_CP_PHONE				= trim($arr_rs[$j]["BUY_CP_PHONE"]);
							$TOTAL_REQ_QTY				= trim($arr_rs[$j]["TOTAL_REQ_QTY"]);
							$TOTAL_BUY_TOTAL_PRICE		= trim($arr_rs[$j]["TOTAL_BUY_TOTAL_PRICE"]);
							$REG_DATE					= trim($arr_rs[$j]["REG_DATE"]);
							$DELIVERY_TYPE				= trim($arr_rs[$j]["DELIVERY_TYPE"]);
							$IS_SENT					= trim($arr_rs[$j]["IS_SENT"]);
							$SENT_DATE					= trim($arr_rs[$j]["SENT_DATE"]);

							if($SENT_DATE == "0000-00-00 00:00:00")
								$SENT_DATE = "";
							else
								$SENT_DATE = date("Y-m-d H:i", strtotime($SENT_DATE));

							$REQ_DATE = date("Y-m-d",strtotime($REQ_DATE));
							$REG_DATE = date("Y-m-d H:i",strtotime($REG_DATE));
				?>
						
				<?
							$arr_rs_goods = listGoodsRequestGoods($conn, $REQ_NO, '');
							if (sizeof($arr_rs_goods) > 0) {
								
								for ($k = 0 ; $k < sizeof($arr_rs_goods); $k++) {

									$REQ_GOODS_NO				= trim($arr_rs_goods[$k]["REQ_GOODS_NO"]);
									$ORDER_GOODS_NO				= trim($arr_rs_goods[$k]["ORDER_GOODS_NO"]);
									$GOODS_NAME					= SetStringFromDB($arr_rs_goods[$k]["GOODS_NAME"]);
									$GOODS_SUB_NAME				= SetStringFromDB($arr_rs_goods[$k]["GOODS_SUB_NAME"]);
									$BUY_PRICE					= trim($arr_rs_goods[$k]["BUY_PRICE"]);
									$REQ_QTY					= trim($arr_rs_goods[$k]["REQ_QTY"]);
									$BUY_TOTAL_PRICE			= trim($arr_rs_goods[$k]["BUY_TOTAL_PRICE"]);
									$RECEIVE_QTY				= trim($arr_rs_goods[$k]["RECEIVE_QTY"]);
									$RECEIVE_DATE				= trim($arr_rs_goods[$k]["RECEIVE_DATE"]);
									$RECEIVER_NM				= trim($arr_rs_goods[$k]["RECEIVER_NM"]);
									$TO_HERE					= trim($arr_rs_goods[$k]["TO_HERE"]);
									$MEMO2						= trim($arr_rs_goods[$k]["MEMO2"]);

									$UP_DATE					= trim($arr_rs_goods[$k]["UP_DATE"]);
									$UP_ADM						= trim($arr_rs_goods[$k]["UP_ADM"]);
									
									$CANCEL_TF					= trim($arr_rs_goods[$k]["CANCEL_TF"]);
									$CANCEL_DATE				= trim($arr_rs_goods[$k]["CANCEL_DATE"]);
									$CANCEL_ADM					= trim($arr_rs_goods[$k]["CANCEL_ADM"]);

									$CONFIRM_TF					= trim($arr_rs_goods[$k]["CONFIRM_TF"]);
									$CONFIRM_DATE				= trim($arr_rs_goods[$k]["CONFIRM_DATE"]);

									if($RECEIVE_DATE != "0000-00-00 00:00:00")
										$RECEIVE_DATE = "<font color='blue'>".date("Y-m-d H:i",strtotime($RECEIVE_DATE))."</font>";
									else
										$RECEIVE_DATE = "<font color='red'>�԰���</font>";

									if($UP_DATE != "0000-00-00 00:00:00")
										$UP_DATE = date("Y-m-d",strtotime($UP_DATE));
									else
										$UP_DATE = "";
								
									if($CANCEL_DATE != "0000-00-00 00:00:00")
										$CANCEL_DATE = date("Y-m-d",strtotime($CANCEL_DATE));
									else
										$CANCEL_DATE = "";

									if($CONFIRM_DATE != "0000-00-00 00:00:00" && $CONFIRM_TF == 'Y')
										$CONFIRM_DATE = "����Ȯ���Ͻ�:".date("Y-m-d H:i",strtotime($CONFIRM_DATE));
									else
										$CANCEL_DATE = "";

									if($CANCEL_TF == "Y")
										$str_cancel_style = "cancel_order";
									else
										$str_cancel_style = "";

									if($CONFIRM_TF == "Y")
										$str_confirm_style = "confirm_order";
									else
										$str_confirm_style = "";

									if($TO_HERE != "Y") { 
										
										$arr_order_delivery_paper = getOrderGoodsDeliveryPaper($conn, $ORDER_GOODS_NO);
										
										if(sizeof($arr_order_delivery_paper) > 0) {
											$DELIVERY_NO = $arr_order_delivery_paper[0]["DELIVERY_NO"];
											$DELIVERY_CP = $arr_order_delivery_paper[0]["DELIVERY_CP"];
											$DELIVERY_DATE = $arr_order_delivery_paper[0]["DELIVERY_DATE"];

											$trace = getDeliveryUrl($conn, $DELIVERY_CP);
											$trace = $trace.trim($DELIVERY_NO);
											//echo $DELIVERY_CP."//".$DELIVERY_NO."<br/>";

											if($DELIVERY_DATE != "0000-00-00 00:00:00" && $DELIVERY_DATE != "")
												$DELIVERY_DATE = "<font color='blue'>".date("Y-m-d H:i",strtotime($DELIVERY_DATE))."</font>";
											else
												if($TO_HERE != "Y")
													$DELIVERY_DATE = "<font color='red'>���۹�ۿϷ���</font>";
												else
													$DELIVERY_DATE = "<font color='red'>�԰���</font>";

										} 
									} 
									
				
				?>

						<tr height="30" class="top_group <?=$str_cancel_style?> <?=$str_confirm_style?>" title="<?=$CONFIRM_DATE?>">
							<td>
								<!--
									<input type="checkbox" name="chk_rg_no[]" value="<?=$REQ_GOODS_NO?>" <?($CANCEL_TF == "Y" ? 'checked' : '')?>>
								-->
								<? if ($TO_HERE == "Y") {?>
								<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$UP_DATE?>');" style="font-weight:bold;"><?=$REQ_GOODS_NO?></a>
								<? } else { ?>
									<?=$REQ_GOODS_NO?>
								<? } ?>
							</td>
							<td class="modeual_nm"><a href="javascript:js_view('<?=$REQ_NO?>')"><?=$BUY_CP_NM?></a></td>
							<td class="modeual_nm"><?= $GOODS_NAME." ".$GOODS_SUB_NAME ?></td>
							<td><?= number_format($BUY_PRICE)?></td>
							<td><?= number_format($REQ_QTY)?></td>
							<td><?= number_format($BUY_TOTAL_PRICE)?></td>
							<td><?=$IS_SENT == "Y" ? "<font color='green'>".$SENT_DATE."</font>" : "<font color='red'>�߼���</font>"?> </td>
							<? if ($TO_HERE == "Y") {?>
								<td class="modeual_nm"><?=$RECEIVER_NM?></td>
								<td class="modeual_nm"><?=$MEMO2?></td>
								<td><a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$UP_DATE?>');" style="font-weight:bold; color:blue;"><?=$RECEIVE_QTY?></a></td>
								<td>
									<a href="javascript:js_link_to_in_stock('<?=$REQ_GOODS_NO?>', '<?=$UP_DATE?>');" style="font-weight:bold;"><?= $RECEIVE_DATE ?></a>
									<? if($CANCEL_TF == "Y") {?>
										/ <font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">��ҵ�</font>
									<? } ?>
								</td>
							<? } else { ?>
								<td class="modeual_nm"><?="����(".$RECEIVER_NM.")"?></td>
								<td class="modeual_nm"><?=$MEMO2?></td>
								<td>
									<? if ($DELIVERY_NO) {?>
										<a href="javascript:js_trace('<?=$trace?>');" style="font-weight:bold; color:blue;"><?=$DELIVERY_CP?>(<?=$DELIVERY_NO?>)</a>
									<? } ?>
								</td>
								<td>
									<?= $DELIVERY_DATE ?>
									<? if($CANCEL_TF == "Y") {?>
										/ <font color='red' title="<?=$CANCEL_DATE."/".getAdminName($conn, $CANCEL_ADM)?>">��ҵ�</font>
									<? } ?>
								</td>
							<? } ?>



							<!--
							<td class="modeual_nm"><a href="javascript:js_view('<?=$REQ_NO?>')"><?=$BUY_MANAGER_NM?></a></td>
							-->
							
						</tr>



				<?					
									$DELIVERY_NO = "";
									$DELIVERY_CP = "";
									$DELIVERY_DATE = "";
									$trace = "";
								}
							}
						}
					} else { 
				?> 
						<tr>
							<td align="center" height="50" colspan="11">�����Ͱ� �����ϴ�. </td>
						</tr>
				<? 
					}
				?>
					</tbody>
				</table>
				<div style="width: 95%; text-align: right; margin: 10px 0 20px 0;">
					<? if ($sPageRight_D == "Y" && $s_adm_cp_type == "�") {?>
						<input type="button" name="aa" value=" ������ ���� ����Ȯ�� " class="btntxt" onclick="js_deposit();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" ������ ���ּ� ��ü���� " class="btntxt" onclick="js_delete();">&nbsp;&nbsp;&nbsp;
						<input type="button" name="aa" value=" ������ ���� ��ǰ ��� " class="btntxt" onclick="js_cancel();">
					<? } ?>
				</div>

					<!-- --------------------- ������ ó�� ȭ�� START -------------------------->
					<?
						# ==========================================================================
						#  ����¡ ó��
						# ==========================================================================
						if (sizeof($arr_rs) > 0) {
							#$search_field		= trim($search_field);
							#$search_str			= trim($search_str);

							$strParam = "";
							$strParam = $strParam."&nPageSize=".$nPageSize."&search_field=".$search_field."&search_str=".$search_str."&order_field=".$order_field."&order_str=".$order_str;
							$strParam = $strParam."&start_date=".$start_date."&end_date=".$end_date."&con_cp_type=".$con_cp_type;

					?>
					<?= Image_PageList($_SERVER[PHP_SELF],$nPage,$nTotalPage,$nPageBlock,$strParam) ?>
					<?
						}
					?>
					<!-- --------------------- ������ ó�� ȭ�� END -------------------------->
			</div>
			<!-- // E: mwidthwrap -->



		</td>
	</tr>
	</table>

	        
</div>
<iframe src="about:blank" name="ifr_hidden" id="ifr_hidden" frameborder="no" width="100%" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>