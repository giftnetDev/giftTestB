<?
    require "_common/home_pre_setting.php";

?>   
<?
    //--------------------Require------------------------
    $goods_code=$_POST['goodsCode'];
    $goods_no=$_POST['goodsNo'];

    // echo "GOODS_CODE : ".$goods_code."<br>";
    // echo "GOODS_NO : ".$goods_no."<br>";

    //---------------------------------------------------
    if($mode=="S"){

       // $conn=db_connection("w");

        $txtName=trim(setStringToDB($txtName));
        $txtPhone=trim(setStringToDB($txtPhone));
        $txtEmail=trim(setStringToDB($txtEmail));
        $txtTitle=trim(setStringToDB($txtTitle));
        $txtContent=trim(setStringToDB($txtContent));


        $query= "INSERT INTO TBL_PURCHASE_CONSULTATION (GOODS_NO, CON_NAME, CON_PHONE, CON_EMAIL, CON_TITLE, CON_CONTENTS) 
                VALUE ( $goods_no, '$txtName', '$txtPhone', '$txtEmail', '$txtTitle', '$txtContent') ; ";

        $result=mysql_query($query, $conn);
        echo mysql_error();
        if(!$result){
            echo "<script>alert('insert errror');</script>";
        }

        ?>
        <script>
            document.location="index2.php";
        </script>
        <?
        exit;
    }
    



?>
<!DOCTYPE HTML>
<html lang="ko">
    <head>
        <meta charset="euc-kr">
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width">
        <meta name="description" content="����Ʈ��" />
        <title>����Ʈ��</title>
        <link rel="icon" type="image/x-icon" herf="/" />
        <script type="text/javascript" src="newDesign/js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="newDesign/js/jquery_ui.js"></script>
        <script type="text/javascript" src="newDesign/js/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="newDesign/js/slick.js"></script>
        <script type="text/javascript" src="newDesign/js/common_ui.js"></script>
        <link type="text/css" rel="stylesheet" href="newDesign/css/reset.css" />

        <script>
            $(document).ready(function(){
                $('#cTitle').keyup(function(e){
                    var content = $(this).val();
                    $('#cTitleTxtCnt').html(content.length);
                    if(content.length>50){
                        alert("�ִ� 50�ڱ��� �����մϴ�!");
                        $(this).val(content.substr(0,50));
                        $('#cTitleTxtCnt').html("�ִ� 50");
                    }
                });


            });

        </script>
        <script type="text/javascript">
            function js_save(){
                var frm=document.frm;

                
                frm.mode.value="S";
                frm.action="<?=$_SERVER[PHP_SELF]?>";

                frm.submit();

            }
        </script>
    </head>
    <body id="">
        <form name='frm' method='POST'>
            <input type='hidden' name='mode' value="">
            <input type='hidden' name='goodsCode' value="<?=$goods_code?>">
            <input type='hidden' name='goodsNo' value="<?=$goods_no?>">
            <div id="wrap">
                <div class="header">
                    <div class="innerbox">

                        <h2>���� ���</h2>
                        <button type="button" class="btn-prevpage" onclick="history.back(-1)" title="����">����</button>
                        <button type="button" class="btn-gnb-search" onclick="searchToggle()" title="�˻�">�˻�</button>

                    </div><!--class='innerbox'-->
                    <div class="searchbox">
                        <h2>��ǰ �˻�</h2>
                        <button type="button" class="btn-close" onclick="searchToggle()" title="�˻� �ݱ�">�˻� �ݱ�</button>
                        <fieldset>
                            <legend>�˻��� �Է�</legend>
                            <p><span class="inpbox"><input type="text" class="txt" placeholder="�˻�� �Է����ּ���." /></span><button type="button">�˻�</button></p>
                        </fieldset>
                        <dl>
                            <dt>��õ �˻���</dt>
                            <dd>
                                <span><a href="#"></a></span>
                            </dd>
                        </dl>

                    </div><!--class='searchbox'-->
            
            
                </div><!--class='header'-->
                <div class="container">

                    <div class="contentsarea">

                        <div class="counselbox">

                            <div class="goods-number"><span>��ǰ��ȣ</span><strong><?=$goods_code?></strong></div>
                            <fieldset>
                                <legend>���Ż�� ���� �Է�</legend>
                                <ul>
                                    <li>
                                        <label>�̸�</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtName" title="�̸� �Է�" /></p></div>
                                    </li>
                                    <li>
                                        <label>��ȭ��ȣ</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtPhone" title="��ȭ��ȣ �Է�" /></p></div>
                                    </li>
                                    <li>
                                        <label>�̸���</label>
                                        <div><p class="inpbox"><input type="text" class="txt" name="txtEmail" title="�̸��� �Է�" /></p></div>
                                    </li>
                                    <li>
                                        <label>����</label><span class="txtcnt"><strong id="cTitleTxtCnt">0</strong> / 50��</span>
                                        <div><p class="inpbox"><input type="text" class="txt" id="cTitle" name="txtTitle" placeholder="������ �Է����ּ���." title="���� �Է�" /></p></div>
                                    </li>
                                    <li>
                                        <label>����</label><span class="txtcnt"><strong id="cContentTxtCnt">0</strong> / 2500��</span>
                                        <div><p class="txtbox"><textarea cols="" rows="" id="cContent" name="txtContent" placeholder="������ �Է����ּ���." title="���� �Է�"></textarea></p></div>
                                    </li>
                                </ul>
                            </fieldset>
                            <div class="rulebox">
                                <ul>
                                    <li>���� �׸� : [�ʼ�] �̸�, ��ȭ��ȣ</li>
                                    <li>�������̿� ���� : ��ǰ ���� ���</li>
                                    <li>���� ��Ź ����ó : �߱���Ʈ��</li>
                                    <li>�������̿�Ⱓ : ������ ���Ǹ� öȸ�� ������ ��ȿ (�����Ǵ� ���� ����)<br />(��, ��� �� Ÿ ���ɿ� ���� ������ �ش� ���ɿ��� ���� �Ⱓ ���� ����)</li>
                                    <li>���� öȸ ��� : ��ǥ��ȭ(031-527-6812) �Ǵ°������� ��ȣ ������ �� ����ڿ��� ����, ��ȭ �Ǵ��̸��Ϸ� ����</li>
                                </ul>
                                <p>�� ���ϲ����� �� ���Ǹ� �����Ͻ� �� ������, �̵��� �� �ش� ���� ��û �� �̿뿡 ������ ���� �� �ֽ��ϴ�.</p>
                                <p>�� �������� ó���� ���� ���� ������ ȭ�� �ϴ��� �������� ó�� ��ħ�� �����Ͻʽÿ�.</p>
                            </div>
                            <p class="agreechk"><input type="checkbox" id="agreeOk" /><label for="agreeOk">�������� ���� �� �̿뿡 �����մϴ�.</label></p>
                            <p class="btncenter"><button type="button" id="" class="btn-green btn-large" onclick="js_save()">Ȯ��</button></p>
                        </div><!-- class='counselbox' -->


                    </div><!--class='contentsarea'-->



                </div><!--class='container'-->


            </div><!--class='wrap'-->
        </form>
    </body>




</html>

