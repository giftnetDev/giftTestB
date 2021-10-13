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
		
			SELECT  CASE WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ��ǿ�ǰ ��Ʈ')THEN '��ǿ�ǰ ��ƮA' 
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ��Ź��ǰ ��Ʈ')THEN '��Ź��ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ��ȸ��ǰ ��Ʈ')THEN '��ȸ��ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� �ֹ�⹰ ��Ʈ')THEN '�ֹ�⹰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ��ǰ ��Ʈ')THEN '��ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ����ǰ ��Ʈ')THEN '����ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ������Ʈ ��Ʈ')THEN '������Ʈ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ȭ��ǰ ��Ʈ')THEN 'ȭ��ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ��Ȱ��ȭ ��Ʈ')THEN '��Ȱ��ȭ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� �ֹ��ǰ ��Ʈ')THEN '�ֹ��ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('���� ��ǿ�ǰ ��Ʈ')THEN '��ǿ�ǰ ��ƮB'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü �ֹ��ǰ ��Ʈ')THEN '�ֹ��ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ��Ź��ǰ ��Ʈ')THEN '��Ź��ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ��ȸ��ǰ ��Ʈ')THEN '��ȸ��ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü �ֹ�⹰ ��Ʈ')THEN '�ֹ�⹰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ��ǰ ��Ʈ')THEN '��ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ����ǰ ��Ʈ')THEN '����ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ������Ʈ ��Ʈ')THEN '������Ʈ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ȭ��ǰ ��Ʈ')THEN 'ȭ��ǰ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('��ü ��Ȱ��ȭ ��Ʈ')THEN '��Ȱ��ȭ ��ƮA'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('������Ʈ')THEN 'Ȱ������Ʈ(����)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('�йи���Ʈ')THEN 'Ȱ������Ʈ(����)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('Ȱ������Ʈ(����)')THEN '������Ʈ(����)'
							WHEN TRIM(SUBSTRING(CATE_NAME, INSTR(CATE_NAME, '. ') + 1, LENGTH(CATE_NAME))) = TRIM('Ȱ������Ʈ(����)')THEN '������Ʈ(����)'
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