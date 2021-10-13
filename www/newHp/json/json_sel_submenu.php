<?session_start();?>
<?
#====================================================================
# DB Include, DB Connection
#====================================================================
require "../../_classes/com/db/DBUtil.php";

$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
require "../../_common/config.php";


function selectSubmenu($db, $catecode, $catecodelen)
{
	if($catecodelen == 3)
	{		
		$query = "
		
			SELECT  CASE WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 욕실용품 세트')THEN '욕실용품 세트A' 
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 세탁용품 세트')THEN '세탁용품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 일회용품 세트')THEN '일회용품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 주방기물 세트')THEN '주방기물 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 식품 세트')THEN '식품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 등산용품 세트')THEN '등산용품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 지갑벨트 세트')THEN '지갑벨트 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 화장품 세트')THEN '화장품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 생활잡화 세트')THEN '생활잡화 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 주방용품 세트')THEN '주방용품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('공급 욕실용품 세트')THEN '욕실용품 세트B'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 주방용품 세트')THEN '주방용품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 세탁용품 세트')THEN '세탁용품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 일회용품 세트')THEN '일회용품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 주방기물 세트')THEN '주방기물 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 식품 세트')THEN '식품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 등산용품 세트')THEN '등산용품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 지갑벨트 세트')THEN '지갑벨트 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 화장품 세트')THEN '화장품 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('자체 생활잡화 세트')THEN '생활잡화 세트A'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('선물세트')THEN '활선물세트(명절)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('패밀리세트')THEN '활선물세트(감사)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('활선물세트(명절)')THEN '선물세트(명절)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('활선물세트(감사)')THEN '선물세트(감사)'
							ELSE SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))
						END AS CATE_NAME
					, CATE_CODE
			FROM TBL_CATEGORY 
			WHERE USE_TF = 'Y' 
			  AND DEL_TF = 'N' 
			  AND CATE_CD LIKE '15%'
		";
			if($catecode != "" && $catecode % 100 == 0) { 
				$query .= "	  AND CATE_CODE LIKE '".substr($catecode, 0, 1)."%' AND CATE_CODE <> '".$catecode."'  ";
			} else { 
				$query .= "	  AND CATE_CODE LIKE '".substr($catecode, 0, 1)."%' AND CATE_CODE <> '".substr($catecode, 0, 1)."00'  ";
			}

			$query .= "
			ORDER BY 1 ";
	}
	else
	{
		$query = "
					SELECT CONCAT( CATE_SEQ01, CATE_SEQ02, CATE_SEQ03, CATE_SEQ04 ) AS SEQ
						 , SUBSTRING(CATE_NAME,3,LENGTH(CATE_NAME)) AS CATE_NAME
						 , CATE_CD AS CATE_CODE
					  FROM TBL_CATEGORY
					 WHERE CATE_CD LIKE '$catecode%'
					   AND USE_TF = 'Y'
					   AND DEL_TF = 'N'
					   AND CATE_CD != '$catecode'
					 ORDER BY SEQ ASC
					 ";
	}
	//echo $query."<br/><br/>";
	//exit;

	$result = mysql_query($query,$db);
	$record = array();

	if ($result <> "") {
		for($i=0;$i < mysql_num_rows($result);$i++) {
			$record[$i] = sql_result_array($result,$i);
		}
	}

    for($i=0;$i<count($record);$i++)
	{
        $record[$i]["CATE_CODE"] = iconv("euc-kr","utf-8",$record[$i]["CATE_CODE"]);
        $record[$i]["CATE_NAME"] = iconv("euc-kr","utf-8",$record[$i]["CATE_NAME"]);
    }

    $sel_list = array();
    for($i=0;$i<count($record);$i++)
	{
        $CATE_NAME  = $record[$i]["CATE_NAME"];
        $CATE_CODE  = $record[$i]["CATE_CODE"];

        array_push($sel_list, array("CATE_CODE" => $CATE_CODE, "CATE_NAME" => $CATE_NAME));
    }

    return $sel_list;
}

//-----------------------------------------------------------------------------------------------------------------------------------//

if($mode == "SELECT_SUB_MENU")
{
    $catecode = iconv("utf-8","euc-kr",trim($_POST["catecode"]));

	$catecodelen = strlen($catecode);
    
    $result = selectSubmenu($conn, $catecode, $catecodelen);

    if($result != false){
        echo json_encode($result);
    } else {
        echo json_encode(false);
    }
}

?>