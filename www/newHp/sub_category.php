<!-- ��ü ī�װ� -->
<?
    function getCateCode($db)
    {
        $query = "SELECT SUBSTRING(DCODE_EXT,-4,4) AS CATE_CODE
                        FROM TBL_CODE_DETAIL
                    WHERE 1 =1
                        AND PCODE = 'HOME_BANNER'
                        AND DCODE = 'NEW_BANNER_TITLE'
                        ";

        $result = mysql_query($query,$db);
        $rows   = mysql_fetch_array($result);
        
        return $rows[0];
    }

	if($cate <> "") { 

		//echo $cate;
	
		//ī�޷α�
		if(startsWith($cate, '20'))
			$cate_length = 4;
		//else
		//	$cate_length = 6;
		
		if(strlen($cate) > $cate_length)
			$arr_rs_cate = listSubCategory($conn, substr($cate, 0, $cate_length), substr($cate, 0, $cate_length));
		else
			$arr_rs_cate = listSubCategory($conn, $cate, $cate);

		if(sizeof($arr_rs_cate) > 0 && $cate != "") { 
?>

        <nav>
            <div class="nav_sc">
            <div style="font-size: 25px; font-weight: bold; color: white; margin-left: 0px; padding-left: 20px;"><?=getCategoryNameOnly($conn, getCateCode($conn))?></div>
		<?

			for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
				//category_NO, category_CD, category_NAME, category_URL, category_FLAG, category_SEQ01, category_SEQ02, category_SEQ03, category_RIGHT
				
				$CATE_NO				= trim($arr_rs_cate[$j]["CATE_NO"]);
				$CATE_CD				= trim($arr_rs_cate[$j]["CATE_CD"]);
				$CATE_NAME			= trim($arr_rs_cate[$j]["CATE_NAME"]);
				$CATE_MEMO			= trim($arr_rs_cate[$j]["CATE_MEMO"]);
				$CATE_CODE			= trim($arr_rs_cate[$j]["CATE_CODE"]);

                if($CATE_CD==$cate){
                    $subMenuOn="sub_menu_on";
                }
                else{
                    $subMenuOn="";
                }
                $CATE_NM = substr($CATE_NAME, 3, strlen($CATE_NAME));
                
                if(strlen($CATE_NAME) > 35)
                {
                
		?>
                <a href="sub_menu.php?cate=<?=$CATE_CD?>" class="<?=$subMenuOn?>" style="font-size:14px; padding-left: 40px;" ><?=$CATE_NM?></a>
		<?	 
                }else {
        ?>            
                <a href="sub_menu.php?cate=<?=$CATE_CD?>" class="<?=$subMenuOn?>" style="padding-left: 40px;" ><?=$CATE_NM?></a>
         <?
                }
        }	
        ?>
<? } ?>
            </div>    
            <div class="nav_close"></div>
            <div class="nav_open" style="display:none;"></div>    
        </nav>
<?
	} else { 
    $arr_rs_cate 	= listSubMenusByCodeCate($conn, $code_cate, $arr_options);
    
?>
			<nav>
                <div class="nav_sc">
				<?
                    //������ ī�װ� ��Ī�� �������� �ʰ�, �����ִ� ��Ī�� �����ϰ� ���� -s
                    // $today = date("Ym");
                    // if($today<"201908"){
                        for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
                            switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
                                case "��ü ��ǿ�ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ǿ�ǰ ��ƮA";		break;
                                case "���� ��Ź��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ź��ǰ ��ƮB";		break;
                                case "���� ��ȸ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ȸ��ǰ ��ƮB";		break;
                                case "���� �ֹ�⹰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ�⹰ ��ƮB";		break;
                                case "���� ��ǰ ��Ʈ"		 : $arr_rs_cate[$j]["CATE_NAME"] = "��ǰ ��ƮB";				break;
                                case "���� ����ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "����ǰ ��ƮB";		break;
                                case "���� ������Ʈ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ ��ƮB";		break;
                                case "���� ȭ��ǰ ��Ʈ"  : $arr_rs_cate[$j]["CATE_NAME"] = "ȭ��ǰ ��ƮB";			break;
                                case "���� ��Ȱ��ȭ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ȱ��ȭ ��ƮB";		break;
                                case "���� �ֹ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ��ǰ ��ƮB";		break;
                                case "���� ��ǿ�ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ǿ�ǰ ��ƮB";		break;
                                case "��ü �ֹ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ��ǰ ��ƮA";		break;
                                case "��ü ��Ź��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ź��ǰ ��ƮA";		break;
                                case "��ü ��ȸ��ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��ȸ��ǰ ��ƮA";		break;
                                case "��ü �ֹ�⹰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "�ֹ�⹰ ��ƮA";		break;
                                case "��ü ��ǰ ��Ʈ"		 : $arr_rs_cate[$j]["CATE_NAME"] = "��ǰ ��ƮA";				break;
                                case "��ü ����ǰ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "����ǰ ��ƮA";		break;
                                case "��ü ������Ʈ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ ��ƮA";		break;
                                case "��ü ȭ��ǰ ��Ʈ"	 : $arr_rs_cate[$j]["CATE_NAME"] = "ȭ��ǰ ��ƮA";			break;
                                case "��ü ��Ȱ��ȭ ��Ʈ": $arr_rs_cate[$j]["CATE_NAME"] = "��Ȱ��ȭ ��ƮA";		break;
                                case "������Ʈ"					 : $arr_rs_cate[$j]["CATE_NAME"] = "Ȱ������Ʈ(����)";	break;
                                case "�йи���Ʈ"				 : $arr_rs_cate[$j]["CATE_NAME"] = "Ȱ������Ʈ(����)";
                            }
                        }
                        
                        function querySort ($x, $y) {
                            return strcasecmp($x['CATE_NAME'], $y['CATE_NAME']);
                        }
                        usort($arr_rs_cate, 'querySort');
                        for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
                            switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
                                case "Ȱ������Ʈ(����)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ(����)";		break;
                                case "Ȱ������Ʈ(����)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "������Ʈ(����)";
                            }
                        }
                    // }
                    //������ ī�װ� ��Ī�� �������� �ʰ�, �����ִ� ��Ī�� �����ϰ� ���� -e
                    
                    $ArrMenu=sizeof($arr_rs_cate);
                    if($ArrMenu>0){
                        for($i=0; $i<$ArrMenu; $i++){
                            $CATE_NAME  =   $arr_rs_cate[$i]["CATE_NAME"];
                            $CATE_CODE  =   $arr_rs_cate[$i]["CATE_CODE"];

                            if($CATE_CODE==$code_cate){
                                $subMenuOn="sub_menu_on";
                            }
                            else{
                                $subMenuOn="";
                            }
            
                        ?>
                            <a href="sub_menu.php?code_cate=<?=$CATE_CODE?>&sort=sort1" class="<?=$subMenuOn?>"> <?=$CATE_NAME?></a>
                        <?
                        }
                    }
                ?>        
                </div>    
                <div class="nav_close"></div>
                <div class="nav_open" style="display:none;"></div>    
			</nav>

<?
	}
?>
<!-- // ��ü ī�װ� -->