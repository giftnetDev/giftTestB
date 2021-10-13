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
	$menu_right = "GD018"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/biz/member/member.php";

	$search_option = trim($search_field);
	$search_str = trim($search_str);

    if ($start_date == "") {
		$start_date = date("Y-m-d",strtotime("-12 month"));
	} else {
		$start_date = trim($start_date);
	}

	if ($end_date == "") {
		$end_date = date("Y-m-d",strtotime("0 month"));
	} else {
		$end_date = trim($end_date);
	}
	
	function isNewGoods($db, $goods_no){
        $query =    "SELECT GOODS_CODE
                            FROM TBL_GOODS
                            WHERE GOODS_NO = '$goods_no'
								AND DEL_TF = 'N'
		";
		
        //echo $query;
    
        $result = mysql_query($query,$db);
        $record = array();
        
        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
			}
			return $record[0]["GOODS_CODE"];
		} else {
			return "ã�� �� ����";
		}
	}

	function makeDefaultCategoryOption($db, $checkitem = null){
        //��ǰ��ü�� ��ü ī�װ��� �ʿ����, ��ǰ ī�װ��� �����ָ� �Ǳ� ������ ����2 ��ǰ���� �����´�.
        $query =    "SELECT CATE_CD, CATE_NAME
                            FROM TBL_CATEGORY
                            WHERE LENGTH(CATE_CD) = '4'
                                AND CATE_CD LIKE '17%'
                                AND DEL_TF = 'N'
                                AND USE_TF = 'Y'
                            ORDER BY CATE_SEQ02 ASC
        ";

        //echo $query;
    
        $result = mysql_query($query,$db);
        $record = array();
        
        if ($result <> "") {
            for($i=0;$i < mysql_num_rows($result);$i++) {
                $record[$i] = sql_result_array($result,$i);
            }
        }
        
        $selectOptions = "<option value=''>����</option>";
        for($i=0;$i<sizeof($record);$i++){
            if($record[$i]["CATE_CD"] != $checkitem)
                $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."'>".$record[$i]["CATE_NAME"]."</option>";
            else
                $selectOptions .= "<option value='".$record[$i]["CATE_CD"]."' selected>".$record[$i]["CATE_NAME"]."</option>";
        }
        
        if($selectOptions == ""){
            return "<option value=''>����</option>";
        } else {
            return $selectOptions;
        }
	}
	
	function getMemberGoodsProposal($db,$start_date,$end_date,$cate_cd,$cate_03,$cate_04,$search_option,$search_str,$sort_by,$order,$acpt_tf){
		$query ="SELECT A.*, B.CATE_NAME
						FROM TBL_MEMBER_PROPOSAL A JOIN TBL_CATEGORY B ON A.GOODS_CATE = B.CATE_CD
						WHERE A.DEL_TF = 'N'
		";

		//�����
		$query .= " AND A.REG_DATE BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' ";

		//ī�װ�
		if($cate_cd != ""){
			$query .= " AND A.GOODS_CATE LIKE '$cate_cd%' ";
		}

		//���޻�
		if($cate_03 != ""){
			$query .= " AND  A.CATE_03 = '$cate_03' ";
		}

		//�ǸŻ���
		if($cate_04 != ""){
		$query .= " AND  A.CATE_04 = '$cate_04' ";
		}

		//�˻����� ��ǰ�� �ɼ� ������
		if($search_option == ""){
			$query .= " AND (
									(A.GOODS_NAME LIKE '%$search_str%') OR
									(A.GOODS_SUB_NAME LIKE '%$search_str%') OR
									(A.CATE_02 LIKE '%$search_str%')
								) 
			";
		}else if($search_option == "GOODS_NAME"){
			$query .= " AND A.GOODS_NAME LIKE '%$search_str%' ";
		} else if($search_option == "GOODS_SUB_NAME"){
			$query .= " AND A.GOODS_SUB_NAME LIKE '%$search_str%' ";
		} else if($search_option == "CATE_02"){
			$query .= " AND A.CATE_02 LIKE '%$search_str%' ";
		}

		//���ο���
		if($acpt_tf != ""){
			$query .= " AND  A.ACPT_TF = '$acpt_tf' ";
		}

		//���� sort_by : ���ı���, order : �������� or ��������
		//�����, ��ǰ��, ����ó
		if($order == "ASC"){
			if($sort_by == "REG_DATE"){
				$query .= " ORDER BY A.REG_DATE ASC ";
			}else if($sort_by == "GOODS_NAME"){
				$query .= " ORDER BY A.GOODS_NAME ASC ";
			}else if($sort_by == "CATE_03"){
				$query .= " ORDER BY A.CATE_03 ASC ";
			}
		} else {
			if($sort_by == "REG_DATE"){
				$query .= " ORDER BY A.REG_DATE DESC ";
			}else if($sort_by == "GOODS_NAME"){
				$query .= " ORDER BY A.GOODS_NAME DESC ";
			}else if($sort_by == "CATE_03"){
				$query .= " ORDER BY A.CATE_03 DESC ";
			}
		}

		// echo "$query<br>";
		
		$result = mysql_query($query,$db);
		$record = array();
		
		if ($result <> "") {
			for($i=0;$i < mysql_num_rows($result);$i++) {
				$record[$i] = sql_result_array($result,$i);
			}
		}

		return $record;
	}

	// echo "start_date : $start_date, end_date : $end_date, cate_cd : $cate_cd, cate_03 : $cate_03, cate_04 : $cate_04, search_option : $search_option, search_str : $search_str,sort_by : $sort_by, order : $order, acpt_tf : $acpt_tf<br>";

	$arr_rs = getMemberGoodsProposal($conn,$start_date,$end_date,$cate_cd,$cate_03,$cate_04,$search_option,$search_str,$sort_by,$order,$acpt_tf);
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>

<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>

<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" >


	function js_write() {
		document.location.href = "member_write.php";
	}

	function js_view(rn, mem_no) {

		var frm = document.frm;
		
		frm.mem_no.value = mem_no;
		frm.mode.value = "S";
		frm.target = "";
		frm.method = "get";
		frm.action = "member_write.php";
		frm.submit();
		
	}
	
	// ��ȸ ��ư Ŭ�� �� 
	function js_search() {
		var frm = document.frm;
		
		frm.nPage.value = "1";
		frm.method = "get";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}

	function js_toggle(mem_no, use_tf) {
		var frm = document.frm;

		bDelOK = confirm('��� ���θ� ���� �Ͻðڽ��ϱ�?');
		
		if (bDelOK==true) {

			if (use_tf == "Y") {
				use_tf = "N";
			} else {
				use_tf = "Y";
			}

			frm.mem_no.value = mem_no;
			frm.use_tf.value = use_tf;
			frm.mode.value = "T";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
	}
</script>
<script>
    $(document).ready(function(){
        $( ".datepicker" ).datepicker({
            showOn: "button",
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
        });//datepicker
		
		$("#chk_all").click(function(){
            //��ü ����/���� üũ�ڽ� üũ ����
            var isChecked = $(this)[0].checked;

            //��ü üũ�ڽ�
            var checkBoxs = $("tbody > tr > td > input");

            if(isChecked){
                //��ü ����
                for(i=0;i<checkBoxs.length;i++){
                    checkBoxs.eq(i)[0].checked = true;
                }
            } else {
                //��ü ���� ����
                for(i=0;i<checkBoxs.length;i++){
                    checkBoxs.eq(i)[0].checked = false;
                }
            }
        });
	});

<?
    $day_0 = date("Y-m-d",strtotime("0 month"));
    $day_1 = date("Y-m-d",strtotime("-1 day"));
    $day_7 = date("Y-m-d",strtotime("-7 day"));
    $day_31 = date("Y-m-d",strtotime("-1 month"));
?>

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

		frm.method = "post";
		frm.target = "";
		frm.action = "<?=$_SERVER[PHP_SELF]?>";
		frm.submit();
	}
</script>
<style>
    .table > td, th{
        white-space: nowrap;
        text-align:center;
    }
    .table{
        table-layout: auto !important;
    }
    .table_head{
        background-color:#ebf3f6;
        color:#86a4b2;
        padding:8px;
    }
    .table_body{
        padding:8px;
    }
    .border{
        border:solid 1px #e0e0e0;
    }

    #accpect_list{
        overflow:auto;
        width:97%;
        height:520px;
    }
</style>
</head>
<body id="admin">

<form name="frm" method="post">

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

                <h2>���޾�ü ��û����</h2>
			<div class="sp20"></div>
			<div style="text-align:right;padding-right:65px;">
				<input type="button" id="accept_proposal_btn" style="width:100px;height:20px;" value="����" />
				<input type="button" id="disallow_proposal_btn" style="width:100px;height:20px;" value="�̽���" />
				<br><br>
			</div>
			<script>
			$(document).ready(function(){
				//������ ���ε� ���� �� �˻� ���� ���(ī�װ� ����2)
				$.ajax({
					url: '/manager/ajax_processing.php',
					dataType: 'text',
					type: 'post',
					data : {
						"mode":"SELECT_CATE_LEVEL3",
						"cate_cd":"<?=$goods_cate?>"
					},
					success: function(response) {
						if(response.length > 0){
							$("#goods_cate2").html(response);
							$("#goods_cate2").val("<?=$goods_cate2?>");
						}
					},
					error : function (jqXHR, textStatus, errorThrown) {
						alert('ERRORS: ' + textStatus);
					}
				});//ajax


				$("#accept_proposal_btn").click(function(){
					var selectedRows = $(".tbody > tr > td > input:checked");
					
					var datas = Array();
					for(i=0;i<selectedRows.length;i++){
						var temp_proposal_no = selectedRows.eq(i).val();
						datas.push({"proposal_no":temp_proposal_no});
					}//for

					//ajax ���� ������ TBL_MEMBER_PROPOSAL�� ACPT���� �÷� �ֽ�ȭ�ϰ�,
					//�űԸ� TBL_GOODS�� INSERT
					//�ű� �ƴϸ� UPDATE
					$.ajax({
						url: '/manager/ajax_processing.php',
						dataType: 'text',
						type: 'post',
						data : {
							"mode":"ACCEPT_PROPOSAL",
							"acceptor":"<?=$s_adm_no?>",
							"data":datas
						},
						success: function(response) {
							//ó�����
							if(response.length > 0){
								// insert = response[0]["insert"];
								
								for(i=0;i<response.length;i++){
									;
								}
								// alert("�����Ͽ����ϴ�.\n"+"�űԵ�� "+insert+"��, ���� "+update+"��, �������� "+nochange+"��, ���� "+error+"��");
								alert("�����Ͽ����ϴ�.");
							} else {
								alert("�����Ͽ����ϴ�.");
							}
						},//success
						error : function (jqXHR, textStatus, errorThrown) {
							alert('ERRORS: ' + textStatus);
						}//error
					});//ajax
				});//#accept_proposal_btn click

				$("#disallow_proposal_btn").click(function(){
					var selectedRows = $(".tbody > tr > td > input:checked");
					
					var datas = Array();
					for(i=0;i<selectedRows.length;i++){
						var temp_proposal_no = selectedRows.eq(i).val();
						datas.push({"proposal_no":temp_proposal_no});
					}//for

					$.ajax({
						url: '/manager/ajax_processing.php',
						dataType: 'text',
						type: 'post',
						data : {
							"mode":"DISALLOW_PROPOSAL",
							"acceptor":"<?=$s_adm_no?>",
							"data":datas
						},
						success: function(response) {
							//ó�����
							if(response.length > 0){
								// insert = response[0]["insert"];
								
								for(i=0;i<response.length;i++){
									;
								}
								alert("�����Ͽ����ϴ�.");
							} else {
								alert("�����Ͽ����ϴ�.");
							}
						},//success
						error : function (jqXHR, textStatus, errorThrown) {
							alert('ERRORS: ' + textStatus);
						}//error
					});//ajax
				});
				

				$("#goods_cate").change(function(){
					$.ajax({
						url: '/manager/ajax_processing.php',
						dataType: 'text',
						type: 'post',
						data : {
							"mode":"SELECT_CATE_LEVEL3",
							"cate_cd":$("#goods_cate").val()
						},
						success: function(response) {
							if(response.length > 0){
								$("#goods_cate2").html(response);   
							}
						},
						error : function (jqXHR, textStatus, errorThrown) {
							alert('ERRORS: ' + textStatus);
						}
					});//ajax
				});//goods_cate2 change
			});//ready
			</script>
                <div id="accpect_list">
                    <table cellpadding="0" cellspacing="0" style="width:100%;" class="table table-hover border ">
                    <thead>
                        <tr>
                            <th class="table_head border" scope="col"><input type="checkbox" id="chk_all" /></th>
                            <th class="table_head border" scope="col">���ο���</th>
                            <th class="table_head border" scope="col">�űԿ���</th>
                            <th class="table_head border" scope="col">�̹���</th>
                            <th class="table_head border" scope="col">��ǰ��</th>
                            <th class="table_head border" scope="col">�ɼ�(�𵨸�)</th>
                            <th class="table_head border" scope="col">�ǸŻ���</th>
                            <th class="table_head border" scope="col">ī�װ�</th>
                            <th class="table_head border" scope="col">���ް�</th>
                            <th class="table_head border" scope="col">��ƼĿ���</th>
                            <th class="table_head border" scope="col">�����μ���</th>
                            <th class="table_head border" scope="col">�ù���</th>
                            <th class="table_head border" scope="col">�ڽ��Լ�</th>
                            <th class="table_head border" scope="col">������</th>
                            <th class="table_head border" scope="col">�ΰǺ�</th>
                            <th class="table_head border" scope="col">��Ÿ���</th>
                            <th class="table_head border" scope="col">�߰�����հ�</th>
                            <th class="table_head border" scope="col">�����հ�</th>
                            <th class="table_head border" scope="col">������</th>
                            <th class="table_head border" scope="col">����ó</th>
                            <th class="table_head border" scope="col" class="end">�����</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
					<?
					for($i=0;$i<count($arr_rs);$i++){
						$PROPOSAL_NO = $arr_rs[$i]["PROPOSAL_NO"];
						$GOODS_CATE = $arr_rs[$i]["CATE_NAME"];
						$GOODS_NAME = $arr_rs[$i]["GOODS_NAME"];
						$GOODS_SUB_NAME = $arr_rs[$i]["GOODS_SUB_NAME"];
						$CATE_02 = $arr_rs[$i]["CATE_02"];
						$CATE_03 = $arr_rs[$i]["CATE_03"];
						$CATE_04 = $arr_rs[$i]["CATE_04"];
						$PRICE = $arr_rs[$i]["PRICE"];
						$BUY_PRICE = $arr_rs[$i]["BUY_PRICE"];
						$EXTRA_PRICE = $arr_rs[$i]["EXTRA_PRICE"];
						$NORMAL_FILE_NM = $arr_rs[$i]["NORMAL_FILE_NM"];
						$DETAIL_FILE_NM = $arr_rs[$i]["DETAIL_FILE_NM"];
						$CONTENTS = $arr_rs[$i]["CONTENTS"];
						$MEMO = $arr_rs[$i]["MEMO"];
						$DELIVERY_CNT_IN_BOX = $arr_rs[$i]["DELIVERY_CNT_IN_BOX"];
						$REG_DATE = $arr_rs[$i]["REG_DATE"];
						$UP_ADM = $arr_rs[$i]["UP_ADM"];
						$UP_DATE = $arr_rs[$i]["UP_DATE"];
						$DEL_ADM = $arr_rs[$i]["DEL_ADM"];
						$DEL_DATE = $arr_rs[$i]["DEL_DATE"];
						$ACPT_TF = $arr_rs[$i]["ACPT_TF"];
						$ACCEPTOR = $arr_rs[$i]["ACCEPTOR"];
						$ACCPT_DATE = $arr_rs[$i]["ACCPT_DATE"];
						$STICKER_PRICE = $arr_rs[$i]["STICKER_PRICE"];
						$PRINT_PRICE = $arr_rs[$i]["PRINT_PRICE"];
						$DELIVERY_PRICE = $arr_rs[$i]["DELIVERY_PRICE"];
						$LABOR_PRICE = $arr_rs[$i]["LABOR_PRICE"];
						$OTHER_PRICE = $arr_rs[$i]["OTHER_PRICE"];
						$REASON = $arr_rs[$i]["REASON"];
						$REG_ADM = $arr_rs[$i]["REG_ADM"];
						$GOODS_NO = $arr_rs[$i]["GOODS_NO"];
						$GOODS_NO_ID_STR = "";
						if($GOODS_NO != ""){
							$GOODS_NO_ID_STR = "id='goods_no_".$GOODS_NO."'";
						}
						$NEW_GOODS_TF = "";
						if($GOODS_NO == ""){
							$NEW_GOODS_TF = "�ű�";
						} else {
							$NEW_GOODS_TF = isNewGoods($conn, $GOODS_NO);
						}
						//DB �����͸� ����� ǥ�ÿ� �����ͷ� ó��
												
						//Y�� ��������, N�� �̽�������
						if($ACPT_TF == "Y"){
							$ACPT_TF = "����";
						} else if($ACPT_TF == "N"){
							$ACPT_TF = "�̽���";
						} else if($ACPT_TF == "W"){
							$ACPT_TF = "���";
						}

						//�ڵ带 �ؽ�Ʈ��
						$CATE_03 = getCompanyNameOnly($conn,$CATE_03);
					?>
                        <tr>
                            <td scope="row border" class="table_body border">
								<input type="checkbox" id="chk_<?=$PROPOSAL_NO?>" value="<?=$PROPOSAL_NO?>"/>
								<input type="hidden" <?=$GOODS_NO_ID_STR?> value="<?=$GOODS_NO?>"/>
							</td>
                            <td scope="row border" class="table_body border"><?=$ACPT_TF?></td>
                            <td scope="row border" class="table_body border"><?=$NEW_GOODS_TF?></td>
                            <td scope="row border" class="table_body border"><img src="<?=$NORMAL_FILE_NM?>" width="100px" height="100px" /></td>
                            <td scope="row border" class="table_body border"><?=$GOODS_NAME?></td>
                            <td scope="row border" class="table_body border"><?=$GOODS_SUB_NAME?></td>
                            <td scope="row border" class="table_body border"><?=$CATE_04?></td>
                            <td scope="row border" class="table_body border"><?=$GOODS_CATE?></td>
                            <td scope="row border" class="table_body border"><?=number_format($BUY_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($STICKER_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($PRINT_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($DELIVERY_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($DELIVERY_CNT_IN_BOX)?></td>
                            <td scope="row border" class="table_body border"><?=number_format(($DELIVERY_PRICE/$DELIVERY_CNT_IN_BOX))?></td>
                            <td scope="row border" class="table_body border"><?=number_format($LABOR_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($OTHER_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($EXTRA_PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=number_format($PRICE)?></td>
                            <td scope="row border" class="table_body border"><?=$CATE_02?></td>
                            <td scope="row border" class="table_body border"><?=$CATE_03?></td>
                            <td scope="row border" class="table_body border" class="end"><?=$REG_DATE?></td>
						</tr>
						<?}?>
                    </tbody>
                </table>
            </div><!-- accpect_list-->
			</div>
			<!-- // E: mwidthwrap -->
		</td>
	</tr>
	</table>
</div>
</form>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>