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
	$menu_right = "CS001"; // �޴����� ���� �� �־�� �մϴ�

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
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/company/company.php";
	require "../../_classes/biz/counsel/counsel.php";
	
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);

    if($counsel_date == ""){
        $counsel_date = date("Y-m-d",strtotime("0 month"));
    } else {
        $counsel_date = trim($counsel_date);
    }

    if($seq_no == ""){
        $seq_no = trim($seq_no);
    }

    
#====================================================================
# DML Process
#====================================================================
function getCounsel($db, $seq_no){
    $query =    "SELECT *
                        FROM TBL_COUNSEL
                        WHERE SEQ_NO = '$seq_no'
                            AND DEL_TF = 'N'
    ";

    // echo $query;

	$result = mysql_query($query,$db);
	$record = array();

	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}
	return $record;
}
if($seq_no != ""){
    $arr_rs = getCounsel($conn,$seq_no);

    $cp_type = $arr_rs[0]["CP_NO"];
    $manager_nm = $arr_rs[0]["MANAGER_NM"];
    $counsel_adm_no = $arr_rs[0]["COUNSEL_ADM_NO"];
    $counsel_date = $arr_rs[0]["COUNSEL_DATE"];
    $counsel_type = $arr_rs[0]["COUNSEL_TYPE"];
	$ask = $arr_rs[0]["ASK"];
	$answer = $arr_rs[0]["ANSWER"];
	
	//echo "<script>alert('������".$ask."');</script>";
	//echo "����� $answer<br>";

}
?>

<!DOCTYPE html>
<html xml:lang="ko" lang="ko">
<head>
<title>����Ʈ�� ������</title>
<meta content="����Ʈ�� ������" name="keywords" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<link rel="icon" type="image/x-icon" href="/" />
<link type="text/css" rel="stylesheet" href="../css/reset.css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/goods_common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script src="../../lib/js/jquery-1.11.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/jquery_ui.js" charset="euc-kr"></script>
<script type="text/javascript" src="../jquery/jquery.datetimepicker.full.js"></script>

<script>

	$(document).ready(function(){
		// $('#datepicker').datetimepicker({
		// 	format:'Y-m-d H:i:s',
		// });
		$("#btn_save").click(function(){
			var temp_cp_type=$("#cp_type").val();	//CP_NO
			var temp_manager_nm=$("#manager_nm").val();//��ü �����
			var temp_counsel_date =$("#counsel_date").val();//�����
			var temp_counsel_adm_no=$("select[name=counsel_adm_no]").val();//�����
			var temp_counsel_type=$("select[name=counsel_type]").val();//�������

			// alert(temp_counsel_adm_no);
			var temp_ask=$("#ask").val();						//����
			var temp_answer=$("#answer").val();					//�亯
			var temp_seq_no ='<?=$seq_no?>';

			// alert(temp_seq_no);
			// alert("<?=$s_adm_no?>");
			if(temp_cp_type==""){
				alert("��ü�� �Էµ��� �ʾҽ��ϴ�");
				return;
			}
			if(temp_manager_nm==""){
				if(!confirm("��ü ����ڰ� ��ϵ��� �ʾҽ��ϴ�. ����Ͻðڽ��ϱ�"))return;
			}
			if(temp_ask==""){
				if(!confirm("������ ��ϵ��� �ʾҽ��ϴ�. ����Ͻðڽ��ϱ�?")) return;
			}
			if(temp_answer==""){
				if(!confirm("�亯�� ��ϵ��� �ʾҽ��ϴ�. ����Ͻðڽ��ϱ�?"))return;
			}
			$.ajax({
                url: '/manager/ajax_processing.php',
                dataType: 'text',
                type: 'post',
                data : {
                    "mode":"INSERT_COUNSEL",
                    "cp_type":temp_cp_type,
                    "manager_nm":temp_manager_nm,
                    "counsel_date":temp_counsel_date,
                    "counsel_adm_no":temp_counsel_adm_no,//�����
                    "counsel_type":temp_counsel_type,
                    "ask":temp_ask,
                    "answer":temp_answer,
                    //"s_adm_no":temp_counsel_adm_no,
					"s_adm_no":"<?=$s_adm_no?>", //�����
                    "seq_no":temp_seq_no
                },
                success: function(response) {
                    if(response.length > 0){
                        if(response == "counsel not exist"){
                            alert("����� �������� �ʽ��ϴ�.\n������ �����Ͽ����ϴ�.");
                        } else if(response == "update"){
                            alert("�����Ǿ����ϴ�.");
                        } else if(response == "update fail"){
                            alert("������ �����Ͽ����ϴ�.");
                        } else if(response == "insert"){
                            alert("��ϵǾ����ϴ�.");
                        } else if(response == "isnert fail"){
                            alert("��Ͽ� �����Ͽ����ϴ�.");
                        }
                    } else {
                        alert("�����Ͽ����ϴ�.");
                    }
					location.href='counsel_list.php';
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    alert('ERRORS: ' + textStatus);
                }
            });//ajax
		});

	});
</script>
<script>
	function js_selecting_company(targetName, cpName, targetValue, cpNo){
		$(function(){
			$("input[name="+targetName+"]").val(cpName);
			$("input[name="+targetValue+"]").val(cpNo);
		})
	}

	function js_search(){
		var frm=document.frm;
		frm.target="";
		frm.method="get";
		frm.action="<?=$_SERVER["PHP_SELF"]?>";
		frm.submit();
		
	}

</script>
</head>

<body>

<div id="wrap">
	<!-- S: toparea -->
    <? require "../../_common/top_area3.php"; ?>
	<!-- //E: toparea -->

	<!-- S: midarea -->
	<div class="midarea">

		<div class="leftarea">
            <? require "../../_common/left_area3.php"; ?>
        </div>

	
		<div class="contentsarea">
			<div class="menu-modify">
				<h2><strong>�����</strong></h2>
				<div class="member-joinbox">
					<div class="join-contents step2">
						<fieldset>
							<legend>�����Է�</legend>
							<ul class="form-insert">
								<li>
									<label>��ü</label>
									<div>
										<p class="inpbox">
											<input type="text" class="txt txt-orange" name="txt_cp_type" id="txt_cp_type" title="��ü" value="<?=getCompanyAutocompleteTextBox($conn,'',$cp_type)?>" placeholder="��ü(��/�ڵ�) �Է��� ���͸� �����ּ���" />
											<input type="hidden" id="cp_type" name="cp_type" value="<?=$cp_type?>"/>
											<script>
												$(function(){
													$("input[name=txt_cp_type]").keydown(function(e){
														if(e.keyCode==13){
															var keyword=$(this).val();
															//alert('keyword is '+keyword);
															if(keyword==""){
																$("input[name=cp_type]").val('');
															}
															else{
																//alert('keyword is '+keyword);
																$.getJSON( "../company/json_company_list.php?term=" + encodeURIComponent(keyword) + "&search_field=CP_NM,CP_NM2,CP_ADDR,RE_ADDR,MANAGER_NM,CP_CODE", function(data) {
																if(data.length == 1) { 
																	js_selecting_company("txt_cp_type", data[0].label, "cp_type", data[0].id);

																} else if(data.length > 1){ 
																	NewWindow("../company/pop_company_searched_list.php?search_str="+keyword + "&target_name=txt_cp_type&target_value=cp_type",'pop_company_searched_list','950','650','YES');

																} else 
																	alert("�˻������ �����ϴ�.");
																});
															}
														}
													});

													$("input[name=txt_cp_type]").keyup(function(e){
														var keyword=$(this).val();
														if(keyword == ""){
															$("input[name=cp_type]").val('');
															//alert("�ƹ��͵� �����ϴ�");
														}
													})
												});
											</script>
											
										</p>

									</div>
								</li>
								<li>
									<label>��ü�����</label>
									<div>
										<p class="inpbox"><input type="text" id="manager_nm" class="txt" title="��ü�����" value="<?=$manager_nm?>" /></p>
									</div>
								</li>
								<li>
									<label>�����</label>
									<div class="rev-header">
                                        <div class="datesch">
                                            <span class="datebox"><input type="text" class="txt_date" id="counsel_date" placeholder="����� ����" value="<?=$counsel_date?>"/></span>
                                        </div>
                                    </div>
								</li>
								<li>
									<label>�����</label>
									<div>
										<span class="optionbox">
											<!-- <select title="����� ����"><option>ȫ�浿</option><option>ȫ�浿</option><option>ȫ�浿</option><option>ȫ�浿</option></select> -->
											<?
											//���� ��� �����̸� ���� ����ڸ� ǥ���ϰ�, �ƴϸ� ���� �����ڸ� ǥ���Ѵ�.
												if($counsel_adm_no != ""){
													echo makeSelectBox1($conn,"COUNSEL_ADM", "counsel_adm_no","����","",$counsel_adm_no);
												} else {
													echo makeSelectBox1($conn,"COUNSEL_ADM", "counsel_adm_no","����","",$s_adm_no);
												}
											?>
										</span>
									</div>
								</li>
								<li>
									<label>�������</label>
									<div>
										<span class="optionbox">
											<!-- <select title="������� ����"><option>����</option><option class="selected">����</option><option>����</option><option>����</option></select> -->
											<? echo makeSelectBox1($conn,"COUNSEL_TYPE","counsel_type","����","",$counsel_type);?>
										</span>
									</div>
								</li>
								<li>
									<label>����</label>
									<div>
										<textarea id="ask" style="width:100%;height:150px"><?=$ask?></textarea>
									</div>
								</li>
								<li>
									<label>�亯</label>
									<div>
										<textarea id="answer" style="width:100%;height:150px"><?=$answer?></textarea>
									</div>
								</li>
							</ul>
							<span class="btncenter">
								<button type="button" class="btn-navy btn-large" id="btn_save">���</button>
								<button type="button" class="btn-white btn-large" onclick="location.href='counsel_list.php'">���</button>
							</span>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- //E: midarea -->

</div>


</body>
</html>

<script src="../../lib/js/jquery.easing.1.3.js" charset="euc-kr"></script>
<script src="../../lib/js/modernizr-2.8.3-respond-1.4.2.min.js" charset="euc-kr"></script>
<script src="../../lib/js/slick.js" charset="euc-kr"></script>
<script src="../../lib/js/common_ui.js" charset="euc-kr"></script>