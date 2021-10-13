<? session_start(); ?>
<?
    require "../../_common/config.php";
?>
<?
    $idx= $_GET['idx'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= $g_charset ?>" />
        <title><?= $g_title ?></title>
        <link rel="stylesheet" href="../css/admin.css" type="text/css" />
        <script type="text/javascript" src="../js/common.js"></script>
        <script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
        
        <script type="text/javascript">
            var g_goodData;
            var saleStates={"판매중":"1","품절":"2","단종":"3","판매금지":"4","출시예정":"5"};
            function js_load_goods_data(){
                console.log('<?=$idx?>');
                g_goodData=window.opener.js_get_good_info(<?=$idx?>);
                console.log(g_goodData);
            }
            function js_init_goods_info(){
                var imgUrl=g_goodData['FILE_PATH']+g_goodData['FILE_RNM'];
                document.getElementById('tdImg').innerHTML="<img src='"+imgUrl+"' width='200' height='200'>";
                document.getElementById('hGoodsNo').innerHTML=g_goodData['GOODS_NO'];
                $('#txtGoodName').val(g_goodData['GOODS_NAME']);
                $('#txtGoodCode').val(g_goodData['GOODS_CODE']);
                $('#txtPrice').val(g_goodData['PRICE']);
                $('#txtDeliveryCntInBox').val(g_goodData['DELIVERY_CNT_IN_BOX']);
                //판매상태는 잠시 후에 컨트롤 한다.

                document.getElementById('txtDsc1').innerHTML=g_goodData['GOODS_DSC1'];
                document.getElementById('txtDsc2').innerHTML=g_goodData['GOODS_DSC2'];
                document.getElementById('txtDsc3').innerHTML=g_goodData['GOODS_DSC3'];
                document.getElementById('txtDsc4').innerHTML=g_goodData['GOODS_DSC4'];
                document.getElementById('txtDsc5').innerHTML=g_goodData['GOODS_DSC5'];
                document.getElementById('txtDsc6').innerHTML=g_goodData['GOODS_DSC6'];
                document.getElementById('txtDsc7').innerHTML=g_goodData['GOODS_DSC7'];

            }
            function js_check_existance_enter_key(){
                var enterChar='\n';
                
                for(var i=1;i<=7;i++){
                    var txtId="#txtDsc"+i;
                    var dscs=$(txtId).val();
                    if(dscs.indexOf(enterChar)!=-1){
                        alert('설명 '+i+' 에 Enter Key가 들어갔습니다. Enter Key를 사용할 수 없습니다.');
                        $(txtId).focus();
                        return 0;
                    }
                }
                return 1;

            }
            function js_save(){
                if(js_check_existance_enter_key()==1){
                    g_goodData['GOODS_NAME']=$("#txtGoodName").val();
                    g_goodData['GOODS_CODE']=$("#txtGoodCode").val();
                    g_goodData['PRICE']=$("#txtPrice").val();
                    //판매 상태 표시는 좀 더 고민해보고 넣자
                    console.log(g_goodData);
                    for(var i=1;i<=7;i++){
                        g_goodData['GOODS_DSC'+i]=$('#txtDsc'+i).val();
                    }
                    if($('#chkMulti').prop("checked")==true){
                        g_goodData['MULTIPLE_TF']="Y";
                    }
                    else{
                        g_goodData['MULTIPLE_TF']="N";
                    }
                    g_goodData['SALE_STATE']=$('#sltSaleState').val();
                    g_goodData['DELIVERY_CNT_IN_BOX']=$('#txtDeliveryCntInBox').val();
                    
                    window.opener.js_update_good_info(g_goodData,<?=$idx?>);
                    alert('저장');
                }
            }
            function js_close_this(){
                if(confirm('저장후 종료하시겠습니까?')){
                    js_save();
                    
                    self.close();
                }
                else{
                    alert('저장 안 함');
                    self.close();
                }
            }
            function js_delete(){
                if(confirm('현재 상품을 삭제하시겠습니까?')){
                    alert('해당 상품이 삭제되었습니다');
                    window.opener.js_delete_goods(<?=$idx?>);
                    self.close();
                }
                else{
                    alert('삭제 취소');
                }
            }
            function js_init_multi_checkbox(){

            }

        </script>
        <script>
            $(document).ready(function(){
                if(g_goodData['MULTIPLE_TF']=='Y'){
                    $('#chkMulti').prop('checked',true);
                }
                $('#sltSaleState').val(g_goodData['SALE_STATE']).prop('selected',true);
            });
        </script>
        <script type="text/javascript">
            js_load_goods_data();
        </script>        
    </head>
    <body>
        <div style='text-align:center;'>
            <table border="1">
                <tr>
                    <td id='tdImg' width='300' height='200' style="text-align: center;">

                    </td>
                    <td rowspan="3" width='300' height='600' style="text-align: center;">
                        <table>
                            <tr><!--1-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명1 </h3>  <textarea name='txtDsc1' id='txtDsc1' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;">'엔터'가 들어갔는지 검사</textarea></label>
                                </td>
                            </tr>

                            <tr><!--2-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명2 </h3> <textarea name='txtDsc2' id='txtDsc2' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--3-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명3 </h3>  <textarea name='txtDsc3' id='txtDsc3' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--4-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명4 </h3>  <textarea name='txtDsc4' id='txtDsc4' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--5-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명5 </h3>  <textarea name='txtDsc5' id='txtDsc5' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--6-->
                                <td width='250' height='75' style="text-align: center;">
                                    <br/>
                                    <label><h3>설명6 </h3>  <textarea name='txtDsc6' id='txtDsc6' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--7-->
                                <td width='250' height='75' style="text-align: center;">
                                <br/>
                                    <label><h3>설명7 </h3>  <textarea name='txtDsc7' id='txtDsc7' cols='30' rows='3' style="margin: 0px; height: 50px; width: 250px;"></textarea></label>
                                </td>
                            </tr>

                            <tr><!--버튼들이 들어간다 (저장), (삭제), (종료) -->
                                <td rowspan='2' width='250' height='150' style="text-align: center;">
                                    <input type='button' value='저장' onclick="js_save()">
                                    <input type='button' value='삭제' onclick="js_delete()">
                                    <input type='button' value='종료' onclick='js_close_this()'/>
                                   
                                </td>
                            </tr>

                            
                        </table>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2" width='200' height='400'>
                        <table>
                            <tr><!--1 : GOODS_NO-->
                                <td width='200' height='50' style='text-align:center;'>
                                   <label for='goodNo'>상품 번호 : <a id='hGoodsNo' href='#'></a> </label>
                                </td>
                            </tr>
                            <tr><!--2 : GOODS_NAME-->
                                <td width='200' height='50'>
                                  <labal for='goodName'>상품 명 : <input type='text' id='txtGoodName' name='txtGoodName' size='25'/></label>
                                </td>
                            </tr>
                            <tr><!--3 : GOODS_CODE-->
                                <td width='200' height='50'>
                                   <labal>상품 코드:<input type='text' id='txtGoodCode' name='txtGoodCode'/></label>
                                </td>
                            </tr>
                            <tr><!--4 : GOODS_PRICE -->
                                <td width='200' height='50'>
                                   <labal>상품 가격 : <input type='text' id='txtPrice' name='txtPrice'/></label>
                                </td>
                            </tr>
                            <tr><!--5 : SALE_STATE -->
                                <td width='10' height='50'>
                                   <label for='saleState'>판매 상태 : 
                                        <SELECT id='sltSaleState'>
                                            <OPTION id='opt1' value='판매중'>판매중</OPTION>
                                            <OPTION id='opt2' value='품절'>품절</OPTION>
                                            <OPTION id='opt3' value='단종'>단종</OPTION>
                                            <OPTION id='opt4' value='판매금지'>판매금지</OPTION>
                                            <OPTION id='opt5' value='출시예정'>출시예정</OPTION>
                                        </SELECT>
                                   </label>
                                </td>

                            </tr>
                            <tr>
                                <td width='200' height='50'>
                                    <label for="lblMonoMulti">복합상품인 경우 체크 <input type='checkbox' id='chkMulti' name='chkMulti'/></label>
                                </td>
                            </tr>
                            <tr>
                                <td width='200' height='50'>
                                  <labal for='goodName'>박스입수 : <input type='text' id='txtDeliveryCntInBox' name='txtDeliveryCntInBox' size='15'/></label>
                                </td>
                            </tr>
 
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
<script type="text/javascript">
    js_init_goods_info();
</script>
