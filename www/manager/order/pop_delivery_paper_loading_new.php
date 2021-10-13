<?//session_start();?>
<?
    require "../../_classes/com/db/DBUtil.php";
    require "../../_classes/com/etc/etc.php";
    require "../../_classes/com/util/Util.php";




    $conn=db_connection("w");

    function updateOrderGoodsDeliveryNumberX($db, $deliverySeq, $deliveryCP, $deliveryNO){
        $query =    "UPDATE TBL_ORDER_GOODS_DELIVERY SET DELIVERY_NO ='".$deliveryNO."'
                     WHERE DELIVERY_SEQ ='".$deliverySeq."' AND DELIVERY_CP='".$deliveryCP."' ";

        if(!mysql_query($query,$db)){
            return false;
            echo "<script>alert('[1]오류가 발생하였습니다 - ".mysql_errno().":".mysql_error()."'); //history.go(-1);</script>";
        }
        else{
            return true;
        }
    }

    function isAlreadyExistenceDeliveryNo($db, $deliverySeq,$deliveryCP, $deliveryNo){
        $query="SELECT DELIVERY_NO
                FROM TBL_ORDER_GOODS_DELIVERY
                WHERE DELIVERY_SEQ = '".$deliverySeq."' 
                AND DELIVERY_CP='".$deliveryCP."' 
                AND DELIVERY_NO !='' 
                AND DELIVERY_NO != '".$deliveryNo."' ";

        // echo $query;
        // exit;
        $result=mysql_query($query, $db);
        if($result){
            $cnt=mysql_num_rows($result);
            if($cnt>0){
                return true;
            }   
            else{
                return false;
            } 
        }
    }



    function listOrderDeliveryDeleted($db, $deliverySeq, $deliveryCP){
        $cnt=0;
        $record=array();
        $query=" SELECT RESERVE_NO, DELIVERY_SEQ, RECEIVER_NM, RECEIVER_HPHONE, ORDER_NM, DELIVERY_CP
                FROM TBL_ORDER_GOODS_DELIVERY
                WHERE DELIVERY_SEQ ='".$deliverySeq."'
                AND DELIVERY_CP='".$deliveryCP."'
                AND (DEL_TF='Y' OR USE_TF='N') ";

        $result=mysql_query($query, $db);
        if($result<>""){
            $cnt=mysql_num_rows($result);
            if($cnt>0){
                for($i=0;$i<$cnt;$i++){
                    $record[$i]=mysql_fetch_assoc($result);
                }
                return $record;
            }
            else{
                return "";
            }
        }
    }
    function isDeletedDelivery($db, $deliverySeq, $deliveryCP){
        $query ="SELECT OGD.DELIVERY_SEQ, OGD.RECEIVER_NM, OGD.RECEIVER_ADDR, OG.GOODS_NAME 
                 FROM   TBL_ORDER_GODS_DELIVERY OGD
                 JOIN   TBL_ORDER_GOODS OG ON OGD.ORDER_GOODS_NO=OG.ORDER_GOODS_NO 
                 WHERE  OGD.DELIVERY_SEQ='".$deliverySeq."' 

                 AND    (DEL_TF='Y' OR USE_TF='N' ";
        $result=mysql_query($query, $db);
        $record=array();
        if($result){
            $cnt = mysql_num_rows($result);
            if($cnt>0){
                for($i=0; $i<$cnt; $i++){
                    $record[$i]=mysql_fetch_assoc($result);
                }
                return $record;
            }
            else{
                return "";
            }
        }
    }
    if($mode=="AFTER_OVERWRITE"){
        //DB에 있는 송장번호와 EXCEL에 있는 송장번호가 같은지 판단해서 다른 애들만 보여준다.

    }
    if($mode=="AFTER_DELETED_OVERWRITE"){

    }

    if($mode=="FU"){
        // echo"<script>alert('mode : FU');</script>";
        $saveDir1 = $_SERVER['DOCUMENT_ROOT']."/upload_data/temp_delivery_seq";

        $fileName=upload($_FILES[fileName], $saveDir1, 10000, array('xls'));
        // echo "fileName : ".$fileName."<br>";

        require_once '../../_excel_reader/Excel/reader.php';
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('euc-kr');

        $data->read($saveDir1."/".$fileName);
        error_reporting(E_ALL ^ E_NOTICE);
        // echo "2<br>";
        $redundancyGroups = array();
        $deletedGroups    = array();
        $redundancyCnt=0;
        $deletedCnt=0;

        // echo "선택된 택배 : ".$deliveryCP."<br>";
        // echo "행 개수 : ".$data->sheets[0]['numRows']."<br>";

        for($i=2; $i<=$data->sheets[0]['numRows']; $i++){

            if($deliveryCP=="롯데택배"){
                // echo "롯데택배가 선택되었음<br>";

                $TEMP_DELIVERY_NO   =SetStringToDB(trim($data->sheets[0]['cells'][$i][7]));     //운송장번호    G
                $TEMP_DELIVERY_SEQ  =SetStringToDB(trim($data->sheets[0]['cells'][$i][10]));    //주문번호      J
                $TEMP_RECEIVER_NAME =SetStringToDB(trim($data->sheets[0]['cells'][$i][16]));    //수하인명      P
                $TEMP_RECEIVER_ADDR =SetStringToDB(trim($data->sheets[0]['cells'][$i][17]));    //수하인 주소   Q
                $TEMP_GOODS_NAME    =SetStringToDB(trim($data->sheets[0]['cells'][$i][29]));    //상품명        AC
        

            }
            else if($deliveryCP =="CJ대한통운"){
                // echo "<script>alert('롯데택배 아님');</script>";
                $TEMP_DELIVERY_NO   =SetStringToDB(trim($data->sheets[0]['cells'][$i][8]));
                $TEMP_DELIVERY_SEQ  =SetStringToDB(trim($data->sheets[0]['cells'][$i][30]));

                $TEMP_DELIVERY_NO=str_replace("-","",$TEMP_DELIVERY_NO);
            }
            else{
                // echo "<script>alert('두 택배 모두 아님');</script>";

            }

            $isAlreadyExistence=isAlreadyExistenceDeliveryNo($conn,$TEMP_DELIVERY_SEQ, $deliveryCP,$TEMP_DELIVERY_NO);
            echo "isAlreadyExistence : ".$isAlreadyExistence."<br>";
            if($isAlreadyExistence){
                //덮어씌어질 수 있는 문제들
                //엑셀에서
                
                
                $redundancyGroups[$redundancyCnt]['DELIVERY_NO']    =$TEMP_DELIVERY_NO;
                $redundancyGroups[$redundancyCnt]['DELIVERY_SEQ']   =$TEMP_DELIVERY_SEQ;
                $redundancyGroups[$redundancyCnt]['RECEIVER_NAME']  =$TEMP_RECEIVER_NAME;
                $redundancyGroups[$redundancyCnt]['RECEIVER_ADDR']  =$TEMP_RECEIVER_ADDR;
                $redundancyGroups[$redundancyCnt]['GOODS_NAME']     =$TEMP_GOODS_NAME;
                $redundancyGroups[$redundancyCnt]['isProcess']      ='NO';
                $redundancyCnt++;
                continue;
            }

            $arr_del=isDeletedDelivery($conn,$deliverySeq, $deliveryCP);
            if($arr_del<>""){
                for($j=0;$j<sizeof($arr_del);$j++){
                    $deletedGroups[$deletedCnt]['DELIVERY_NO']  =$arr_del[$j]['DELIVERY_NO'];
                    $deletedGroups[$deletedCnt]['DELIVERY_SEQ'] =$arr_del[$j]['DELIVERY_SEQ'];
                    $deletedGroups[$deletedCnt]['RECEIVER_NAME']=$arr_del[$j]['RECEIVER_NAME'];
                    $deletedGroups[$deletedCnt]['RECEIVER_ADDR']=$arr_del[$j]['RECEIVER_ADDR'];
                    $deletedGroups[$deletedCnt]['GOODS_NAME']   =$arr_del[$j]['GOODS_NAME'];
                    $deletedGroups[$deletedCnt]['isProcess']    ='NO';
                    $deletedCnt++;
                }
                continue;
            }
                updateOrderGoodsDeliveryNumberX($conn, $TEMP_DELIVERY_SEQ, $deliveryCP, $TEMP_DELIVERY_NO);

        }

        ?>
        <script languge="javascript">
            alert('송장 완료파일 입력완료 되었습니다');
        </script>
        <?
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/newStyle/newStyle.css" type="text/css"/>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>


<script>
    $(document).ready(function(){
        var redundancyCnt="<?=$redundancyCnt?>";
        //lert('문제송장의 개수 : '+redundancyCnt);
        if(redundancyCnt>0){
            $('.overwrite').show();
        }
        else{
            $('.overwrite').hide();
        }

        var deletedCnt="<?=$deletedCnt?>";
        if(deletedCnt>0){
            $('.deleted').show();
        }
        else{
            $('.deleted').hide();
        }

        $('#btnOverwriteDelivery').on("mousedown",function(){
            // var arrDeliveryIdx = new Array();
            if($("input[name='chk_no[]']:checked").length==0){
                alert("중복송장을 하나 이상 선택해주세요.");
            }
            else{
                var overwriteTF=false;
                //confirm을 한 번 걸어준다.
                overwriteTF=confirm('덮어씌우겠습니까?');
                if(overwriteTF){
                    var arrDeliveryIdx= new Array();
                    $("input[name='chk_no[]']:checked").each(function(){
                        arrDeliveryIdx.push($(this).val());
                    });

                    $.ajax({
                        url: '/manager/ajax_processing.php',
                        dataType: 'text',
                        type: 'POST',
                        data:{
                            'mode' : "OVERWRITE_DELIVERY_NO",
                            'arrDeliveryIdx' : arrDeliveryIdx

                        },
                        success: function(response){
                            if(response!="0"){
                                alert("특정 데어터 실패");
                            }
                            else{
                                alert("성공");

                            }
                            self.close();
                        },
                        error:function(jqXHR, textStatus, errorThrown){
                            alert("덮어쓰기 실패");
                        }
                    });
                }
                else{
                    return ;
                }     
            }
        });
        $('#btnDeletedDelivery').on("mousedown",function(){
            if($("input[name='chk_delNo[]']:checked").length==0){
                alert("삭제된 송장을 하나 이상 선택해 주세요");
            }
            else{
                var overwriteTF=false;
                overwriteTF=confirm('덮어씌우시겠습니까?');
                if(overwriteTF){
                    var arrDeliveryIdx = new Array();
                    $("input[name='chk_delNo[]']:checked").each(function(){
                        arrDeliveryIdx.push($(this).val());
                    })
                    $.ajax({
                        url: '/manager/ajax_processing.php',
                        dataType: 'text',
                        type: 'POST',
                        data:{
                            'mode' : "OVERWRITE_DELIVERY_NO",
                            'arrDeliveryIdx' : arrDeliveryIdx
                        },
                        success: function(response){
                            if(response!="0"){
                                alert(response+" 실패");
                            }
                            else{
                                alert("덮어쓰기 성공");

                            }
                        },
                        error: function(jqXHR,textStatus,errorThrown){
                            alert("덮어쓰기 실패");
                        }
                    });
                }

            }
        });
    });

</script>
<script>
    window.onunload=refreshParent;
    function refreshParent(){
        window.openner.js_search();
    }
</script>

</head>
<body>
    <div id="mainContent">
        <div class="dvTopBtnLoc">
            <input type='button' class='normalBtn' value="송장 로딩 엑셀" onclick="js_delivery_paper_loading()"/>
        </div>
        <h2>송장 번호 등록 / 업데이트</h2>
        <div class="partContent"><!-- 보드 -->
            <form name="frm" method="post" enctype="multipart/form-data">
                <table>
                    
                    <colgroup>
                        <col width="20%">
                        <col width="45%">
                        <col width="35%">
                    </colgroup>
                    <tr>
                        <td>
                            <!-- SelectBox : 송장회사 -->
                            <?=makeSelectBox($conn, "DELIVERY_CP","deliveryCP","105","전체","","")?>
                        </td>
                        <td>
                            <!-- SelectBox : 비용 -->
                            <?=makeSelectBoxAsName($conn,"DELIVERY_FEE", "deliveryFee,","80px","운임선택","","","")?>
                        </td>
                        <td>
                            <!-- 파일입력 + button -->
                            <input type="file" name="fileName" style="width:60%;" class="txt">
                            <input type="button" name="btnInsert" value="입력" onclick="js_save_seq(this)"/>
                        </td>
                </table>
                <input type="hidden" name="mode" value="<?=$mode?>">

            </form>
        </div>
        <div class="partContent overwrite"><!-- 리스트 -->
            <table>
                <colgroup>
                    <col width="3%">    <!--chk_no-->
                    <col width="11%">   <!--DELIVERY_SEQ-->
                    <col width="11%">   <!--DELIVERY_NO-->
                    <col width="35%">   <!--GOODS_NAME-->
                    <col width="10%">   <!--RECEIVER_NAME-->
                    <col width="30%">   <!--RECEIVER_ADDR-->
                </colgroup>
                <thead>
                    <td><input type='checkbox' name='all_chk'></td>
                    <td>주문번호</td>
                    <td>운송장번호</td>
                    <td>상품명</td>
                    <td>수하인명</td>
                    <td>수하인주소</td>
                </thead>
                <tbody>
                    <?
                        for($i=0; $i<$redundancyCnt; $i++){
                            ?>
                                <td><input type='checkbox' name='chk_no[]' value='<?=$redundancyGroups[$i]['DELIVERY_SEQ']."/".$redundancyGroups[$i]['DELIVERY_NO']?>'></td>
                                <td><?=$redundancyGroups[$i]['DELIVERY_SEQ']?></td>
                                <td><?=$redundancyGroups[$i]['DELIVERY_NO']?></td>
                                <td><?=$redundancyGroups[$i]['GOODS_NAME']?></td>
                                <td><?=$redundancyGroups[$i]['RECEIVER_NAME']?></td>
                                <td><?=$redundancyGroups[$i]['RECEIVER_ADDR']?></td>
                            <?
                        }
                    ?>
                </tbody>
            </table>
            <input type="button" class="overwrite btnCritcal" id="btnOverwriteDelivery" value="선택된 송장 덮어쓰기" onclick="js_overwrite_delivery_no()">

        </div>
        <div class="partContent deleted"><!--deleted List-->
            <table>
                <colgroup>
                    <col width="3%">
                    <col width="11%">
                    <col width="11%">
                    <col width="35%">
                    <col width="10%">
                    <col width="30%">
                </colgroup>
                <thead>
                    <td>주문번호</td>
                    <td>운송장번호</td> 
                    <td>상품명</td>
                    <td>수하인명</td>
                    <td>수하인주소</td>
                </thead>
                <tbody>
                    <?
                        for($i=0; $i<$deletedCnt;$i++){
                    ?>
                            <td><input type='checkbox' name='chk_delNo[]' value='<?=$deletedGroups[$i]['DELIVERY_SEQ']."/".$deletedGroups[$i]['DELIVERY_NO']?>'></td>
                            <td><?$deletedGroups[$i]['DELIEVERY_SEQ']?></td>
                            <td><?$deletedGroups[$i]['DELIVERY_NO']?></td>
                            <td><?$deletedGroups[$i]['GOODS_NAME']?></td>
                            <td><?$deletedGroups[$i]['RECEIVER_NAME']?></td>
                            <td><?$deletedGroups[$i]['RECEIVER_ADDR']?></td>
                    <?      
                        }
                    ?>
                </tbody>

            </table>
            <input type="button" class="deleted btnCritical" id="btnDeletedDelivery" value="삭제된 송장에 덮어쓰기" onclick="js_overwrite_delivery_on_deleted()">
        </div>
    </div>


</body>
<script>
    function js_delivery_paper_loading(){
        var frm = document.frm;

        if(frm.deliveryCP.value==""){
            alert("송장을 등록할 택배사를 선택해주세요");
            return;
        }
        
        frm.target="";
        frm.mode.value="excel";
        frm.action="pop_delivery_paper_loading_excel.php";
        frm.submit();
    }
    function js_save_seq(btn){
        var frm=document.frm;

        if(frm.deliveryCP.value == ""){
            alert("송장을 등록할 택배사를 선택해주세요");
            return;
        }

        btn.style.visibility='hidden';
        if(isNull(frm.fileName.value)){
            alert('파일을 선택해 주세요.');
            btn.style.visibility='visible';
            frm.fileName.focus();
            return;
        }
        // alert('파일 들어감');
        if(!allowAttach(frm.fileName)){
            btn.style.visibility='visible';
            frm.fileName.focus();

            return ;
        }

        // alert('성공');
        // return;

        frm.mode.value="FU";
        frm.target="";
        frm.action="<?=$_SERVER[PHP_SELF]?>";
        frm.submit();
        
    }
    function allowAttach(obj) { //구현함
		var file = obj.value;
		extArray = new Array(".xls");
		allowSubmit = false;
        // alert('검사시작');
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.lastIndexOf(".")).toLowerCase();
            // alert('검사중');

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
            // alert("입력가능");
			return true;
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return false;
		}
	}
</script>
<html>
<?
    mysql_close($conn);
?>