<?php

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/com/util/ImgUtil.php";
	require "../../_classes/com/util/Util.php";

	function Catalog_Insert($db, $reg_adm, $catalog_title, $ctlpop_start, $ctlpop_end, $file_nm, $file_rnm, $file_size, $file_ext)
	{
		$file_path = '/upload_data/catalog_pop/';

		$qre = "SELECT MAX(CTLPOP_NO) + 1 FROM T_CATALOG_POP ";		

		$result 		= mysql_query($qre,$db);
		$rows   		= mysql_fetch_array($result);
		$ctlPopMaxNo  	= $rows[0];

		$query=	"	 
						INSERT INTO T_CATALOG_POP 
							( 
							   CTLPOP_NO
							 , TITLE
							 , CTLPOP_START
							 , CTLPOP_END
							 , HIT_CNT
							 , FILE_NM
							 , FILE_RNM
							 , FILE_PATH
							 , FILE_SIZE
							 , FILE_EXT
							 , DEL_TF
							 , REG_ADM
							 , REG_DATE
							 )
						VALUE(
							   '$ctlPopMaxNo'
							 , '$catalog_title'
							 , '$ctlpop_start'
							 , '$ctlpop_end'
							 , 0
							 , '$file_nm'
							 , '$file_rnm'
							 , '$file_path'
							 , '$file_size'
							 , '$file_ext'
							 , 'N'
							 , '$reg_adm'
							 , NOW() 
							 )
					";		

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Catalog_Delete($db, $reg_adm, $catalog_no)
	{
		$query="UPDATE T_CATALOG_POP
				   SET DEL_ADM		= '$reg_adm'
					 , DEL_DATE		= NOW()
					 , DEL_TF		= 'Y'
				 WHERE CTLPOP_NO 	= '$catalog_no'
				 ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	function Catalog_Update($db, $reg_adm, $catalog_no, $title, $ctlpop_start, $ctlpop_end, $file_nm, $file_rnm, $file_size, $file_ext)
	{
		$file_path = '/upload_data/catalog_pop/';

		$query="UPDATE T_CATALOG_POP
				   SET TITLE		= '$title'
				   	 , CTLPOP_START = '$ctlpop_start'
					 , CTLPOP_END   = '$ctlpop_end'	
					 , FILE_NM		= '$file_nm'	
					 , FILE_RNM		= '$file_rnm'	
					 , FILE_PATH	= '$file_path'	
					 , FILE_SIZE	= '$file_size'	
					 , FILE_EXT		= '$file_ext'	
				     , UP_ADM		= '$reg_adm'
					 , UP_DATE		= NOW()
				 WHERE CTLPOP_NO 	= '$catalog_no'
				   AND DEL_TF 		= 'N'
				 ";

		$result=mysql_query($query,$db);
		//echo $query;
		// exit;
		if($result<>"") return 1;

		else return 0;	
	}

	/************************************************************************************************************************************ */

	if($mode=="UPDATE_CATALOG")
	{
		$catalog_title 	= iconv("utf8","euckr", $catalog_title);

		$file       = $_FILES['file'];		
		$file_path	= $g_physical_path."upload_data/catalog_pop";
		$file_nm	= upload($file, $file_path, 10 , array('gif', 'jpeg', 'jpg','png'));
		//$file_rnm 	= $file;
		$file_rnm	= $_FILES['file']['name'];
		$file_size = $_FILES['file']['size'];
		$file_ext  = end(explode('.', $_FILES['file']['name']));

		if($file == "")
		{
			$file_nm	= $filenm;
			$file_rnm	= $filernm;
			$file_size	= $filesize;
			$file_ext	= $fileext;
		}

		if(Catalog_Update($conn, $reg_adm, $catalog_no, $catalog_title, $ctlpop_start, $ctlpop_end, $file_nm, $file_rnm, $file_size, $file_ext) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}

	if($mode=="DELETE_CATALOG")
	{
		if(Catalog_Delete($conn, $reg_adm, $catalog_no) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}
	}

	if($mode=="INSERT_CATALOG")
	{
		$catalog_title 	= iconv("utf8","euckr", $catalog_title);

		$file       = $_FILES['file'];		
		$file_path	= $g_physical_path."upload_data/catalog_pop";
		$file_nm	= upload($file, $file_path, 10 , array('gif', 'jpeg', 'jpg','png'));
		//$file_rnm 	= $file;
		$file_rnm	= $_FILES['file']['name'];
		$file_size = $_FILES['file']['size'];
		$file_ext  = end(explode('.', $_FILES['file']['name']));

		if(Catalog_Insert($conn, $reg_adm, $catalog_title, $ctlpop_start, $ctlpop_end, $file_nm, $file_rnm, $file_size, $file_ext) == 1 )
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}
	
?>

