<!-- 전체 카테고리 -->
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
	
		//카달로그
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
                    //전산의 카테고리 명칭을 변경하지 않고, 보여주는 명칭만 변경하고 정렬 -s
                    // $today = date("Ym");
                    // if($today<"201908"){
                        for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
                            switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
                                case "자체 욕실용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "욕실용품 세트A";		break;
                                case "공급 세탁용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "세탁용품 세트B";		break;
                                case "공급 일회용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "일회용품 세트B";		break;
                                case "공급 주방기물 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방기물 세트B";		break;
                                case "공급 식품 세트"		 : $arr_rs_cate[$j]["CATE_NAME"] = "식품 세트B";				break;
                                case "공급 등산용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "등산용품 세트B";		break;
                                case "공급 지갑벨트 세트": $arr_rs_cate[$j]["CATE_NAME"] = "지갑벨트 세트B";		break;
                                case "공급 화장품 세트"  : $arr_rs_cate[$j]["CATE_NAME"] = "화장품 세트B";			break;
                                case "공급 생활잡화 세트": $arr_rs_cate[$j]["CATE_NAME"] = "생활잡화 세트B";		break;
                                case "공급 주방용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방용품 세트B";		break;
                                case "공급 욕실용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "욕실용품 세트B";		break;
                                case "자체 주방용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방용품 세트A";		break;
                                case "자체 세탁용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "세탁용품 세트A";		break;
                                case "자체 일회용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "일회용품 세트A";		break;
                                case "자체 주방기물 세트": $arr_rs_cate[$j]["CATE_NAME"] = "주방기물 세트A";		break;
                                case "자체 식품 세트"		 : $arr_rs_cate[$j]["CATE_NAME"] = "식품 세트A";				break;
                                case "자체 등산용품 세트": $arr_rs_cate[$j]["CATE_NAME"] = "등산용품 세트A";		break;
                                case "자체 지갑벨트 세트": $arr_rs_cate[$j]["CATE_NAME"] = "지갑벨트 세트A";		break;
                                case "자체 화장품 세트"	 : $arr_rs_cate[$j]["CATE_NAME"] = "화장품 세트A";			break;
                                case "자체 생활잡화 세트": $arr_rs_cate[$j]["CATE_NAME"] = "생활잡화 세트A";		break;
                                case "선물세트"					 : $arr_rs_cate[$j]["CATE_NAME"] = "활선물세트(명절)";	break;
                                case "패밀리세트"				 : $arr_rs_cate[$j]["CATE_NAME"] = "활선물세트(감사)";
                            }
                        }
                        
                        function querySort ($x, $y) {
                            return strcasecmp($x['CATE_NAME'], $y['CATE_NAME']);
                        }
                        usort($arr_rs_cate, 'querySort');
                        for ($j = 0 ; $j < sizeof($arr_rs_cate); $j++) {
                            switch(trim($arr_rs_cate[$j]["CATE_NAME"])){
                                case "활선물세트(명절)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "선물세트(명절)";		break;
                                case "활선물세트(감사)"				 : $arr_rs_cate[$j]["CATE_NAME"] = "선물세트(감사)";
                            }
                        }
                    // }
                    //전산의 카테고리 명칭을 변경하지 않고, 보여주는 명칭만 변경하고 정렬 -e
                    
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
<!-- // 전체 카테고리 -->