<?
    $type;

    $query="SELECT DCODE
            FROM    TBL_GOODS_EXTRA
            WHERE   GOODS_NO='-----'
            AND     PCODE='GOODS_STICKER_SIZE'
        ";

    $result=mysql_query($query, $conn);

    if($result<>""){
        $rows=mysql_fetch_row($result);

        if($rows[0]==""){
            return;
        }


        $query2="SELECT GOODS_NO
                FROM    TBL_GOODS   
                WHERE   GOODS_NAME LIKE ";
        
    }



    "SELECT


    SELECT IFNULL(GOODS_NO,0), IFNULL(GOODS_NAME,'') FROM 
    (
    SELECT G.GOODS_NO, G.GOODS_NAME
    FROM TBL_GOODS G
    WHERE GOODS_NAME LIKE '%가%'
    AND GOODS_CATE = '010304'
    ) GG
    WHERE GG.GOODS_NAME LIKE CONCAT('%',
    (SELECT IE.DCODE
    FROM TBL_GOODS_EXTRA IE
    WHERE IE.GOODS_NO = '14325'
    AND IE.PCODE = 'GOODS_STICKER_SIZE'),'%')



    SELECT * 
    FROM (
    SELECT G.GOODS_NO, G.GOODS_NAME
    FROM TBL_GOODS G
    WHERE GOODS_NAME LIKE '%가%'
    AND GOODS_CATE = '010304'
    ) GG
    WHERE GG.GOODS_NAME LIKE CONCAT('%',
    (SELECT IE.DCODE
    FROM TBL_GOODS_EXTRA IE
    WHERE IE.GOODS_NO = '14325'
    AND IE.PCODE = 'GOODS_STICKER_SIZE'),'%')


    SELECT * 
    FROM (
    SELECT G.GOODS_NO, G.GOODS_NAME
    FROM TBL_GOODS G
    WHERE GOODS_NAME LIKE '%가%'
    AND GOODS_CATE = '010304'
    ) GG
    WHERE GG.GOODS_NAME LIKE CONCAT('%',
    (
        SELECT IF(IE.DCODE != '', IE.DCODE, '--------------------') AS DCODE
        FROM TBL_GOODS_EXTRA IE
        WHERE IE.GOODS_NO = '14325'
        AND IE.PCODE = 'GOODS_STICKER_SIZE'
    )
    ,'%'
    )
"



"SELECT


SELECT * 
FROM ( 
        SELECT G.GOODS_NO, G.GOODS_NAME 
        FROM TBL_GOODS G 
        WHERE GOODS_NAME LIKE '%다타입%' 
        AND GOODS_CATE = '010304' ) GG 
WHERE GG.GOODS_NAME LIKE CONCAT(
        '%', (
            SELECT IE.DCODE 
            FROM TBL_GOODS_EXTRA IE 
            WHERE IE.GOODS_NO = '' 
            AND IE.PCODE = 'GOODS_STICKER_SIZE'),
        '%')


"
?>