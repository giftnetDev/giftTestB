<?
ini_set('memory_limit',-1);
session_start();
?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "OD017"; // 메뉴마다 셋팅 해 주어야 합니다

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
	require "../../_classes/biz/order/order.php";
	require "../../_classes/biz/goods/goods.php";
	require "../../_classes/biz/member/member.php";
	require "../../_classes/biz/payment/payment.php";
	require "../../_classes/LinkedList.php";

?>
<?
	 function restoreToString(&$list){
        $str="";
        $curNode=$list->getHead();
        while($curNode!=null){
            $curData=$curNode->getData();
            if($curData>=128){
                $nextData=$curNode->getNext()->getData();

                $str.=chr($curNode->getData()).chr($nextData);
                $curNode = $curNode->getNext();
            }
            else{
                $str.=chr($curData);
            }
            $curNode = $curNode->getNext();
        }//End of while($curNode!=null);
        return $str;
    }
    
    function deleteNextNode(&$curNode,&$list){
        $deletedNode=$curNode->getNext();//여기서 deletedNode는 무조건 존재한다.
        // echo " Deleted Node : ".$deletedNode."<br>";
        $nextNode=null;
        if($deletedNode->getNext()!=null) $nextNode = $deletedNode->getNext();
        echo "Delete ".$deletedNode->getData()."!<br>";
    
        unset($deletedNode);  //deleteNode를 삭제한다.
        $list->printNode();
        echo "<br>";
        if($nextNode){
            echo "The NextNode is ".$nextNode->getData()."<br>";
            $curNode->setNext($nextNode);
        }


	}
	function transformCode2(&$list){
		$currNode=$list->getHead();
		while($currNode!=null){
			if($currNode->getData()>=128){//1> EUC-KR인 경우
				
				$curData=$currNode->getData();
				if($curData==163 || $curData==161){//1. 바꿔야 하는 경우
					$nextData=$currNode->getNext()->getData();
					if($curData==163){
						if($nextData==173){
							// echo "HYPEN:curNode->getData()->".$currNode->getData()."<br>";
							//1. $currNode의 데이터를 아스키코드 '-'로 바꾼 후 
							$currNode->setData(45);
							//2. $currNode->getNext()를 삭제한다.
							$nextNode=$currNode->getNext()->getNext();
							$currNode->setNext($nextNode);
							$deletedNode=$currNode->getNext();
							unset($deletedNode);
		
						}
						else if(176<=$nextData && $nextData<=185){
							// echo "NUM:currNode->getData()->".$currNode->getData()."<br>";
							//1. $currNode의 숫자를 아스키코드 숫자로 변경
							$newData=$nextData-128;
							// echo "newData : ".$newData."<br>";
							$currNode->setData($newData);
							//2. $currNode->getNext()를 삭제한다.
							$nextNode=$currNode->getNext()->getNext();
							$currNode->setNext($nextNode);
							$deletedNode=$currNode->getNext();
							unset($deletedNode);
						}

					}
					else{//$curData==161
						if($nextData==161){
							$currNode->setData(32);
							//2. $currNode->getNext()를 삭제한다.
							$nextNode=$currNode->getNext()->getNext();
							$currNode->setNext($nextNode);
							$deletedNode=$currNode->getNext();
							unset($deletedNode);
						}

					}

				}
				else{//2. 안 바꿔도 되는 경우
					$currNode= $currNode->getNext();//EUC-KR은 2byte이기 때문에 1.의 경우가 아니라면 1칸 앞으로 진행
				}

			}//End Of if(euc-kr인 경우)
			$currNode= $currNode->getNext();//아스키이건 EUC-KR이건 일단 1칸 앞으로 진행
		}
	}
	
	function concatenate_str($nextSheet, $initX, $initY, $curX, $curY, $str){
		//this function is Recursion Function
		$arrCol=array("C","D","E","F","G","H");
		// echo"curX : $curX, curY : $curY<br>";
		if($curX>4) return $str;
		$col=$arrCol[$curX];
		$str.=trim($nextSheet->getCell($col.$curY)->getValue());
		// echo "location : ".$col.$curY."<br>";
		if(trim($nextSheet->getCell($arrCol[$curX+1].$curY)->getValue())<>""){
			// echo "좌표 : ".$arrCol[$curX+1].$curY."<br>";
			$str=concatenate_str($nextSheet, $initX, $initY, $curX+1, $curY, $str);

		}
		else if(trim($nextSheet->getCell("C".($curY+1))->getValue())<>""){
			// echo "else좌표 : ".$arrCol[$curX+1].$curY."<br>";
			$str=concatenate_str($nextSheet, $initX, $initY, $initX, $curY+1, $str);

		}
		return $str;
	}
	function completeAddr(&$list, $addr1, $addr2){
		// $arr_num=array('０','１','２','３','４','５','６','７','８','９');
		$addr=$addr1." ".$addr2;

		echo "addr : ".$addr."-order_write_file_costco_162Line<br>";
		$cntAddr=strlen($addr);

		for($i=0; $i<$cntAddr; $i++){
			$list->insertAtBack(ord(substr($addr,$i,1)));
			
		}
		echo "리스트 만들기 성공 -order_write_file_costco_169Line<br>";
		transformCode2($list);
		$alteredAddr=restoreToString($list);



		return $alteredAddr;
	}
?>
<?
#====================================================================
# Request Parameter
#====================================================================
	$mode	= trim($mode);
	
	if ($this_date == "") 
		$this_date = date("Y-m-d H:i:s",strtotime("0 month"));
#====================================================================
# DML Process
#====================================================================
	if ($mode == "FR") {
		// echo "<script>alert('FR MODDE');</script><br>";
	
	#====================================================================
		$savedir1 = $g_physical_path."upload_data/temp_order";
	#====================================================================

		$file_nm	= upload($_FILES[file_nm], $savedir1, 10000 , array('xls','xlsx'));
		
		//echo $file_nm;
		require_once "../../_PHPExcel/Classes/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		require_once "../../_PHPExcel/Classes/PHPExcel/IOFactory.php";
		$filename = '../../upload_data/temp_order/'.$file_nm; 

		
		error_reporting(E_ALL ^ E_NOTICE);

		$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
		$objReader->setReadDataOnly(true);
		$objExcel = $objReader->load($filename);
		
		
		$sheetCount = $objExcel->getSheetCount();
		$valid_sheetCount = 0;

		$temp_no = $file_nm;
		$goods_option_nm = ""; //코스트코는 옵션 없음
		$goods_option_nm2 = "";
		$cp_no = "6"; //코스트코 전산번호
		$order_state = "1";
		$delivery = "0"; 
		$sa_delivery = "0";
		$use_tf = "Y";
		$goods_price = "0";

		$sheetIdx=0;

		// $discriminant=i

		// echo "sheetCount : ".$sheetCount."<br>";

		while($sheetIdx<$sheetCount){

			$objExcel->setActiveSheetIndex($sheetIdx);
			$curSheet=$objExcel->getActiveSheet();
			$str="";

			$list = new LinkedList();


			if($sheetIdx+1<$sheetCount){
				$nextSheet=$objExcel->setActiveSheetIndex($sheetIdx+1);
				$a1=iconv("UTF-8","cp949", $nextSheet->getCell('A1')->getValue());
				// echo $sheetIdx."_a1 : $a1<br>";
				// exit;

				//if($a1<>iconv("EUC-KR","EUC-KR","㈜코스트코 코리아")){
				if($a1<>"㈜코스트코 코리아"){
					// echo "NextSheet : ".$nextSheet."<br>";
					$str=concatenate_str($nextSheet,0,3,0,3,"");
					$sheetIdx++;
					//만약 다음장에도 같은 추가시트가 나오면 어떻게 하지?
					$tmpSheetIdx=$sheetIdx+1;
					while($tmpSheetIdx<$sheetCount){
						$tmpSheet=$objExcel->setActiveSheetIndex($tmpSheetIdx);
						$a1_1=iconv("UTF-8","EUC-KR",$tmpSheet->getCell('A1')->getValue());
						if($a1_1<>"㈜코스트코 코리아"){
							$tmpSheetIdx++;
							$sheetIdx++;
							// echo "tmpSheetIdx : $tmpSheetIdx, sheetIdx : $sheetIdx<br>";
						}
						else{
							break;
						}
					}
				}//end of if($a<>"㈜코스트코 코리아")
			}// end of while($sheetIdx<$sheetCount);

			$str=iconv("UTF-8","cp949",$str);
			// echo "string1 : $str<br>";

			$maxRow = $curSheet->getHighestRow();

			$addr_front 	= iconv("UTF-8","cp949", $curSheet->getCell('E2')->getValue());
			$addr_back		= iconv("UTF-8","cp949", $curSheet->getCell('E3')->getValue());

			// echo "addr_front : $addr_front, addr_back : $addr_back<br>";
			if($addr_back=="0") $addr_back="";

			$r_addr1	=completeAddr($list, $addr_front, $addr_back);// $addr_front." ".$addr_back;
			// echo "r_addr1 : $r_addr1<br>";
			$r_addr1	= SetStringToDB($r_addr1);

			$r_zipcode		= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('E4')->getValue()));
			$r_name			= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('E5')->getValue()));
			$r_hPhone		= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('E6')->getValue()));
			$cp_order_no	= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('A9')->getValue()));
			$order_date		= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('C9')->getValue()));
			$memo			= iconv("UTF-8","cp949",SetStringToDB($curSheet->getCell('D9')->getValue()));

			if(strlen($r_zipcode) == 4){
				$r_zipcode = "0".$r_zipcode;
			}

			$r_hPhone = "0".$r_hPhone;

			if($memo == "" && $str==""){
				$memo = "취급주의 제품입니다-인박스가 훼손되니 던지지 말아주세요~";

			}
			$r_phone = $r_hPhone;
			$o_name = $r_name;
			$o_phone = $r_hPhone;
			$o_hPhone = $r_hPhone;

			if($cp_order_no != "") $valid_sheetCount ++;

			$broken_format = false;

			for($i=11; $i<=$maxRow; $i++){
				$goods_mart_code	= iconv("UTF-8","EUC-KR", SetStringToDB($curSheet->getCell("A".$i)->getValue()));
				if($goods_mart_code =="623029"){
					$broken_format=true;
					$addr_front = iconv("UTF-8","cp949",$curSheet->getCell('F2')->getValue());
					$addr_back	= iconv("UTF-8","cp949",$curSheet->getCell('F3')->getValue());
					$r_addr1	= completeAddr($list, $addr_front,$addr_back);

					$r_zipcode	= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('F4')->getValue()));
					$r_name		= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('F5')->getValue()));
					$r_hPhone	= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('F6')->getValue()));
					$cp_order_no= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('A9')->getValue()));
					$order_date = iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('C9')->getValue()));
					$memo		= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell('E9')->getValue()));

					if(strlen($r_zipcode)==4) $r_zipcode = "0".$r_zipcode;

					$r_hPhone = "0".$r_hPhone;

					if($memo == ""){
						if($str==""){
							$memo = " 취급주의 제품입니다.-인박스가 훼손되니 던지지 말아주세요~";
						}
					}
					$r_phone=$r_hPhone;
					$o_name=$r_name;
					$o_phone=$r_hPhone;
					$o_hPhone=$r_hPhone;
				}
			}

			$memo.=$str;

			for($i = 11; $i <= $maxRow ; $i++){
				$goods_mart_code	= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("A".$i)->getValue()));
				if($goods_mart_code == "623029"){
					$goods_name			= "홈스타 습기를 부탁해 x 24개";
					$qty						= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("H".$i)->getValue()));
				} 
				else {
					if($broken_format){
						$goods_name			= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("B".$i)->getValue()));
						$qty				= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("H".$i)->getValue()));
					} else {
						$goods_name			= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("B".$i)->getValue()));
						$qty				= iconv("UTF-8","EUC-KR",SetStringToDB($curSheet->getCell("F".$i)->getValue()));
					}
				}
				if($goods_mart_code =="") break;

				$goods_no=tryGoodNoFromMartData($conn, $goods_name, $goods_option_nm, $goods_mart_code);

				if($cp_order_no != $prev_cp_order_no || $r_addr1 != $prev_r_addr1 || $r_name != $prev_r_name){
					$order_seq=1;

					$inserted_order_no = insertTempOrder($conn, $temp_no, $cp_no, '', '', '', '', '', '', '', '', '',
														 '', $o_name, $o_phone, $o_hPhone, $r_name, $r_phone, $r_hPhone, 
														 $r_zipcode, $r_addr1, $memo, $order_state, $delivery, $sa_delivery, 
														 $cp_order_no,$ec_order_no, $use_tf, $s_adm_no);
					// echo "ordered_sheet : $sheetIdx<br>";
					insertTempOrderGoods($conn, $temp_no, $inserted_order_no, $order_seq, $goods_no, $goods_name, $goods_price, $qty, $goods_option_nm, $goods_option_nm2, $goods_mart_code);

					$prev_order_no = $inserted_order_no;
				}
				else{
					$order_seq = $order_seq + 1;
					insertTempOrderGoods($conn, $temp_no, $prev_order_no, $order_seq,  $goods_no, $goods_name, $goods_price, $qty, $goods_option_nm, $goods_option_nm2, $goods_mart_code);
				}
				$prev_cp_order_no = $cp_order_no;
				$prev_r_addr1 = $r_addr1;
				$prev_r_name = $r_name;

			}
			$sheetIdx++;
			$list->deleteAll();
			unset($list);


		}//end of while();

		// exit;



?>	
<script language="javascript">
	location.href =  'order_write_file_costco.php?mode=L&temp_no=<?=$file_nm?>&this_date=<?=$this_date?>&total_sheet=<?=$sheetCount?>&valid_sheet=<?=$valid_sheetCount?>';
</script>
<?
		exit;

	}	
	

	if ($mode == "I") {

		$row_cnt = count($ok);
		
		$str_order_no = "";

		for ($k = 0; $k < $row_cnt; $k++) {
			$str_order_no .= "'".$ok[$k]."',";
		}

		$str_order_no = substr($str_order_no, 0, (strlen($str_order_no) -1));
		//echo $str_cp_no;

		$insert_result = insertTempToRealOrderWithDate($conn, $temp_no, $str_order_no, $this_date);

		if ($insert_result) {
			$delete_result = deleteTempToRealOrder($conn, $temp_no, $str_order_no);
		}

		$mode = "L";

	}

	if ($mode == "D") {

		$row_cnt = count($chk);
		
		for ($k = 0; $k < $row_cnt; $k++) {
		
			$tmp_order_no = $chk[$k];

			$temp_result = deleteTempOrder($conn, $temp_no, $tmp_order_no);
		}
		
		$mode = "L";
	}

	if ($mode == "L") {
		$args_cp_no = '6';
		$arr_rs = listTempOrderForMart($conn, $temp_no, $args_cp_no);
	}
	
	if ($result) {
?>	
<script language="javascript">
		alert('정상 처리 되었습니다.');
		location.href =  'order_list.php';
</script>
<?
		exit;
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui-timepicker-addon.css" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
  $(function() {
   /*
	$( ".datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/manager/images/calendar/cal.gif",
      buttonImageOnly: true,
      buttonText: "Select date",
	  showOn: "both",
	  dateFormat: "yy-mm-dd",
	  changeMonth: true,
      changeYear: true
    });

  */

	 $('.datepicker').datetimepicker({
	   	  dateFormat: "yy-mm-dd", 
		  timeFormat: "HH:mm:ss",
		  buttonImage: "/manager/images/calendar/cal.gif",
          buttonImageOnly: true,
          buttonText: "Select date",
     	  showOn: "both",
	      changeMonth: true,
	      changeYear: true
     });
  });
</script>
<style type="text/css">

/*#pop_table {z-index: 1; left: 80; overflow: auto; width: 500; height: 220}*/
#ex_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 100%; height:155px; border:1px solid #d1d1d1;}
#temp_scroll { z-index: 1; background-color:#f7f7f7; overflow: auto; width: 95%; height:100%; border:1px solid #d1d1d1;}

</style>

<script language="javascript">
	
	// 조회 버튼 클릭 시 
	function js_list() {
		var frm = document.frm;
		
		frm.method = "get";
		frm.action = "order_list.php";
		frm.submit();
	}

	// 저장 버튼 클릭 시 
	function js_save() {
		
		var file_rname = "<?= $file_rname ?>";
		var frm = document.frm;

		//frm.full_date.value = frm.this_date.value+" "+frm.this_h.value+":"+frm.this_m.value+":00";

		//alert(frm.full_date.value);
		
		if (isNull(frm.file_nm.value)) {
			alert('파일을 선택해 주세요.');
			frm.file_nm.focus();
			return ;		
		}
		
		AllowAttach(frm.file_nm);

		if (isNull(file_rname)) {
			frm.mode.value = "FR";//파일 집어넣고 "확인"버튼 눌렀을 때 일반적으로 "FR"모드로 간다.
		} else {
			frm.mode.value = "I";//
		}

		frm.method = "post";
		frm.action = "order_write_file_costco.php";
		frm.submit();
	}

	//우편번호 찾기
	function js_post(zip, addr) {
		var url = "/_common/common_post.php?zip="+zip+"&addr="+addr;
		NewWindow(url, '우편번호찾기', '390', '370', 'NO');
	}

	/**
	* 파일 첨부에 대한 선택에 따른 파일첨부 입력란 visibility 설정
	*/
	function js_fileView(obj,idx) {
		
		var frm = document.frm;
		
		if (idx == 01) {
			if (obj.selectedIndex == 2) {
				frm.contracts_nm.style.visibility = "visible";
			} else {
				frm.contracts_nm.style.visibility = "hidden";
			}
		}

	}

	function LimitAttach(obj) {
		var file = obj.value;
		extArray = new Array(".jsp", ".cgi", ".php", ".asp", ".aspx", ".exe", ".com", ".php3", ".inc", ".pl", ".asa", ".bak");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (!allowSubmit){
			//
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return;
		}
	}

	function AllowAttach(obj) {
		var file = obj.value;
		extArray = new Array(".xls", ".xlsx");
		allowSubmit = false;
		
		if (!file){
			//form1.submit();
		}

		while (file.indexOf("\\") != -1){
			file = file.slice(file.indexOf("\\") + 1);
			ext = file.slice(file.indexOf(".")).toLowerCase();

			for (var i = 0; i < extArray.length; i++){
				if (extArray[i] == ext){ 
					allowSubmit = true; 
					break; 
				}
			}
		}

		if (allowSubmit){
			//
		}else{
			alert("입력하신 파일은 업로드 될 수 없습니다!");
			return;
		}
	}

	function js_view(rn, file_nm, order_no) {
		
		var url = "order_modify.php?mode=S&temp_no="+file_nm+"&order_no="+order_no;
		NewWindow(url, '주문대량입력', '860', '513', 'YES');
		
	}

	function js_reload() {
		location.href =  'order_write_file_costco.php?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_chk_island() {

		if(document.frm.has_island.checked)
			location.href =  'order_write_file.php?mode=L&temp_no=<?=$temp_no?>&has_island=true';
		else
			location.href =  'order_write_file.php?mode=L&temp_no=<?=$temp_no?>';
	}

	function js_delete() {

		var frm = document.frm;
		var chk_cnt = 0;

		check=document.getElementsByName("chk[]");
		
		for (i=0;i<check.length;i++) {
			if(check.item(i).checked==true) {
				chk_cnt++;
			}
		}
		
		if (chk_cnt == 0) {
			alert("선택 하신 자료가 없습니다.");
		} else {

			bDelOK = confirm('선택하신 자료를 삭제 하시겠습니까?');
			
			if (bDelOK==true) {
				frm.mode.value = "D";
				frm.target = "";
				frm.action = "<?=$_SERVER[PHP_SELF]?>";
				frm.submit();
			}
		}
	}

	function js_register() {
		var frm = document.frm;
		bDelOK = confirm('정상 데이타는 모두 등록 하시겠습니까?');

		if (bDelOK==true) {
			frm.mode.value = "I";
			frm.target = "";
			frm.action = "<?=$_SERVER[PHP_SELF]?>";
			frm.submit();
		}
		
	}

	function js_unregistered_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_unregistered_goods.php";
		frm.submit();

		//alert("자료 출력");
	}

	function js_excel() {
		
		var frm = document.frm;

		frm.target = "";
		
		frm.action = "order_write_file_excel_mart.php";
		frm.submit();

		//alert("자료 출력");
	}

	function js_temp_goods_excel() {
		
		var frm = document.frm;

		frm.target = "";
		frm.action = "order_write_file_temp_goods_excel.php";
		frm.submit();

	}

	function js_goods_view(goods_no) {

		var frm = document.frm;
		
		frm.goods_no.value = goods_no;
		frm.mode.value = "S";
		frm.target = "blank";
		frm.method = "post";
		frm.action = "/manager/goods/goods_write.php";
		frm.submit();
		
	}
</script>
</head>
<body id="admin">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="rn" value="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="temp_no" value="<?=$temp_no?>">
<input type="hidden" name="cp_no" value="<?=$args_cp_no?>">


<div id="adminwrap">

<?
	#====================================================================
	# common top_area
	#====================================================================

	require "../../_common/top_area.php";
?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="180" />
		<col width="*" />
	</colgroup>
	<tr>
		<td class="leftarea">
<?
	#====================================================================
	# common left_area
	#====================================================================

	require "../../_common/left_area.php";
	include_once('../../_common/editor/func_editor.php');

?>
<style>
	table.rowstable04 { border-top: none; }
	table.rowstable04 > th { padding: 9px 0 8px 0; font-weight: normal; color: #86a4b2; border-top: 1px solid #d2dfe5; background: #ebf3f6 url('../images/admin/bg_bar_01.gif') right center no-repeat; }
	table.rowstable04 > th.end { background: #ebf3f6; }
	table.rowstable04 td { color: #555555; text-align: center; vertical-align: middle; background: none; }

</style>
		</td>
		<td class="contentarea">

			<!-- S: mwidthwrap -->
			<div id="mwidthwrap">
				<h2>주문 등록 - 코스트코</h2>
				<table cellpadding="0" cellspacing="0" class="colstable">

					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<thead>
						<tr>
							<th>
								입력 예
								<br><br>
								<a href="/_common/download_file.php?file_name=insert_order.xls&filename_rnm=insert_example.xls&str_path=manager/order/">받기</a>
							</th>
							<td colspan="3">
								코스트코에서 내려받은 PDF 원본을 Simpo PDF to Excel 에서 엑셀 변환하셔서 파일로 넣어주세요.
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>파일</th>
							<td><input type="file" name="file_nm" style="width:60%;" class="txt"></td>
							<th>주문일</th>
							<td>
								<input type="text" class="txt datepicker" style="width: 150px; margin-right:3px;" name="this_date" value="<?=$this_date?>" maxlength="10"/>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="btnright">
				<? if ($file_nm <> "" ) {?>
					<? if ($sPageRight_U == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? } else {?>
					<? if ($sPageRight_I == "Y") {?>
					<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
					<? } ?>
				<? }?>
				</div>

				<div class="sp20"></div>
				<div>
					<!--
					* 전체 <?=totalCntTempOrder($conn, $temp_no)?> 주문건수 중 &nbsp;&nbsp;
					* 주문번호별 <?=totalCntTempOrderGoods($conn, $temp_no)?> 건 &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
					-->
					*총 <?=($total_sheet != "" ? $total_sheet : "...") ?> 시트 중 <?=($valid_sheet != "" ? $valid_sheet : "...") ?>개의 주문시트(배송메모제외) &nbsp;&nbsp;
					*주문상품수<?=totalCntTempOrderGoods($conn, $temp_no)?> 건 &nbsp;&nbsp;
					<? if ($insert_result) {?>
					* 등록건 <?=$row_cnt?> 건
					<? }?>
				</div>
				<div id="temp_scroll">
				<table cellpadding="0" cellspacing="0" class="rowstable01" style="width:2630px">
					<colgroup>
						<col width="35">
						<col width="150">
						<col width="100">
						<col width="520">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="120">
						<col width="80">
						<col width="500">
						<col width="400">
						
					</colgroup>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>비고</th>
							<th>업체주문번호</th>
							<th>
								주문 상품<br>
								<table cellpadding="0" cellspacing="0" class="rowstable04" style="width:98%">
									<colgroup>
										<col width="12%">
										<col width="18%">
										<col width="45%">
										<col width="20%">
										<col width="10%">
									</colgroup>
									<thead>
										<tr>
											<th>상품번호</th>
											<th>마트상품번호</th>
											<th>상품명</th>
											<th>속성</th>
											<th class="end">수량</th>
										</tr>
									</thead>
								</table>
							</th>
							<th>업체명</th>
							<th>주문자</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>수취인</th>
							<th>연락처</th>
							<th>휴대전화번호</th>
							<th>우편번호</th>
							<th>주소</th>
							<th class="end">주문자메모</th>
						</tr>
					</thead>
					<tbody>
					<?
						$nCnt = 0;
						
						if (sizeof($arr_rs) > 0) {
							
							for ($j = 0 ; $j < sizeof($arr_rs); $j++) {
								
								$rn							= trim($arr_rs[$j]["rn"]);
								$CP_NO					= trim($arr_rs[$j]["CP_NO"]);
								$ORDER_NO				= trim($arr_rs[$j]["ORDER_NO"]);

								$O_NAME					= SetStringFromDB($arr_rs[$j]["O_NAME"]);
								$O_PHONE				= SetStringFromDB($arr_rs[$j]["O_PHONE"]);
								$O_HPHONE				= SetStringFromDB($arr_rs[$j]["O_HPHONE"]);
								$R_NAME					= SetStringFromDB($arr_rs[$j]["R_NAME"]);
								$R_PHONE				= SetStringFromDB($arr_rs[$j]["R_PHONE"]);
								$R_HPHONE				= SetStringFromDB($arr_rs[$j]["R_HPHONE"]);
								$R_ZIPCODE			= SetStringFromDB($arr_rs[$j]["R_ZIPCODE"]);
								$R_ADDR1				= SetStringFromDB($arr_rs[$j]["R_ADDR1"]);
								$MEMO						= trim($arr_rs[$j]["MEMO"]);
								$DELIVERY				= trim($arr_rs[$j]["DELIVERY"]);
								$SA_DELIVERY		= trim($arr_rs[$j]["SA_DELIVERY"]);
								$CP_ORDER_NO		= trim($arr_rs[$j]["CP_ORDER_NO"]);
								$USE_TF					= trim($arr_rs[$j]["USE_TF"]);
								$REG_DATE				= date("Y-m-d",strtotime($REG_DATE));

								// 데이터 유효성 검사
								$err_str = "정상";
								$warning_str = "";

								if ($CP_NO == "") {
									$err_str .=  "판매업체 누락,";
								} else {
									if (getCompayChk($conn, "판매", $s_adm_cp_type, $CP_NO) == "") {
										$err_str .=  "판매업체 오류,";
									}
								}

								

								$arr_rs_temp_goods = selectTempOrderGoods($conn, $temp_no, $ORDER_NO);
								if (sizeof($arr_rs_temp_goods) > 0) {
									// echo "tmpGoodsSize : ".sizeof($arr_rs_temp_goods)."<br>";
									for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

										$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
										$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
										$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
										$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
										$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
										$GOODS_OPTION_NM	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_NM"]);

										// 상품명으로 검색해서 $GOODS_NO 구하기
										
										if ($GOODS_NO == "등록요망") {

											$GOODS_NAME = SetStringToDB($GOODS_NAME);
											$GOODS_OPTION_NM = SetStringToDB($GOODS_OPTION_NM);

											$GOODS_NO = tryGoodNoFromMartData($conn, $GOODS_NAME, $GOODS_OPTION_NM, $GOODS_MART_CODE);
										}


										if ($GOODS_NO == "등록요망" || $GOODS_NO == "복수상품존재") {
											$err_str .=  "상품번호 누락,";
										}
										else
										{
											updateTempOrderGoodsNo($conn, $ORDER_NO, $ORDER_SEQ, $GOODS_NO, $temp_no);
											$arr_rs_temp_goods[$k]["GOODS_NO"] = $GOODS_NO;
										}

										// echo "$CP_ORDER_NO , $GOODS_NO , $R_NAME<br>";
										// exit;
										
										if (chkCompanyOrderNoAndRName($conn, $CP_ORDER_NO, $GOODS_NO, $R_NAME) > 0) {
											//$warning_str = "마트 주문번호 존재 ";
											$err_str .= "마트 주문번호 존재, ";
										}
										
										/*
										$arr_rs_goods = selectGoods($conn, $GOODS_NO);

										// 상품 번호에 해당하는 주문 상품 임시 재고 등록 하기
										// 마트구분, 마트 주문번호, 상품번호, 수량
										//$fake_result = insertFakeStock($conn, $CP_NO, $CP_ORDER_NO, $GOODS_NO, $QTY);

										if($arr_rs_goods[0] != null){
									
											$rs_buy_price			= trim($arr_rs_goods[0]["BUY_PRICE"]);
											$rs_sale_price			= trim($arr_rs_goods[0]["SALE_PRICE"]);
											$rs_goods_state			= trim($arr_rs_goods[0]["CATE_04"]);

											if ($rs_goods_state <> "판매중") {
												$str_goods_state = "<font color='red'>".getDcodeName($conn, "GOODS_STATE", $rs_goods_state)."</font>";
											} else {
												$str_goods_state = getDcodeName($conn, "GOODS_STATE", $rs_goods_state);
											}

											if ($CP_NO <> "") {
												$new_price = getCompanyGoodsPrice($conn, $GOODS_NO, $CP_NO );

											if ($new_price <> 0)
												$rs_sale_price = $new_price;
											}
											
											if ($GOODS_PRICE <> $rs_sale_price ) {
												$str_price = "<font color='red'>".number_format($GOODS_PRICE)."</font>";
											} else {
												$str_price = number_format($GOODS_PRICE);
											}
											
											if ($GOODS_PRICE < $rs_buy_price) {
												$str_plus_price = "<font color='red'>".number_format($GOODS_PRICE - $rs_buy_price)."</font>";
											} else {
												$str_plus_price = number_format($GOODS_PRICE - $rs_buy_price);
											}
										}
										*/
										

										if ($QTY == "") {
											$err_str .=  "수량 누락,";
										} else {
											if ($QTY  < "1") {
												$err_str .=  "수량 오류,";
											}
										}
										

										
										if ($R_NAME == "") {
											$err_str .=  "수령인 누락,";
										}

										if ($R_HPHONE == "") {
											$err_str .=  "휴대전화번호 누락,";
										}
										
										/*
										if ($R_PHONE == "") {
											$err_str .=  "연락처 누락,";
										}
										
										if ($R_ZIPCODE <> "") {
											if (!chkZip($conn, $R_ZIPCODE)) {
												$err_str .=  "우편번호 오류,";
											}
										}
										*/

										if ($R_ADDR1 == "") {
											$err_str .=  "주소 누락,";
										}
									}
								}
								
								/*								
								if ($R_ADDR1 == "") {
									$R_ADDR1 = "보안 송장 배송 주문";
								}
								*/


								if ($err_str <> "정상") {
									$err_str = str_replace("정상","",$err_str);
									$err_str = substr($err_str, 0, (strlen($err_str) -1));
									$err_str = str_replace(",","<div class='sp5'></div>",$err_str);
									$err_str = "<font color='red'>".$err_str."</font>";
								}

								if($warning_str <> ""){ 
									$warning_str = "<font color='blue'>".$warning_str."</font>";
								}
					?>
						<tr>
							<td class="filedown">
								<input type="checkbox" name="chk[]" value="<?=$ORDER_NO?>">
							</td>
							<td class="modeual_nm" style="padding:8px 0 3px 0">
								<a href="javascript:js_view('<?= $rn ?>','<?= $temp_no ?>','<?= $ORDER_NO ?>');"><?=$err_str?></a>
								<? if ($err_str == "정상") {?>
								<input type="hidden" name="ok[]" value="<?=$ORDER_NO?>">
								<? } ?><br/>
								<?= $warning_str?>
							</td><!--비고-->
							<td><?= $CP_ORDER_NO?></td><!--업체주문번호-->
							<td>

								<table cellpadding="0" cellspacing="0" class="rowstable04"  style="width:98%">
									<colgroup>
										<col width="12%">
										<col width="18%">
										<col width="45%">
										<col width="20%">
										<col width="10%">
									</colgroup>
								<?
									if (sizeof($arr_rs_temp_goods) > 0) {
										for ($k = 0 ; $k < sizeof($arr_rs_temp_goods); $k++) {

											$GOODS_NO			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NO"]);
											$GOODS_MART_CODE	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_MART_CODE"]);
											$GOODS_NAME			= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_NAME"]);
											$GOODS_PRICE		= trim($arr_rs_temp_goods[$k]["GOODS_PRICE"]);
											$QTY				= trim($arr_rs_temp_goods[$k]["QTY"]);
											$GOODS_OPTION_NM	= SetStringFromDB($arr_rs_temp_goods[$k]["GOODS_OPTION_NM"]);
											?>
											<tr>
												<td><?= $GOODS_NO?></td>
												<td><?= $GOODS_MART_CODE?></td>
												<td class="modeual_nm"><?= $GOODS_NAME?></td>
												<td class="modeual_nm"><?= $GOODS_OPTION_NM?></td>
												<td><?= number_format($QTY)?></td>
											</tr>
											<?
										}
									}
								?>
								</table>
							</td>
							<td class="modeual_nm"><?=getCompanyName($conn, $CP_NO)?></a></td>
							<td><?= $O_NAME?></td>
							<td><?= $O_PHONE?></td>
							<td><?= $O_HPHONE?></td>
							<td><?= $R_NAME?></td>
							<td><?= $R_PHONE?></td>
							<td><?= $R_HPHONE?></td>
							<td><?= $R_ZIPCODE?></td>
							<td class="modeual_nm"><?= $R_ADDR1?></td>
							<td class="modeual_nm"><?= $MEMO?></td>
							
						</tr>
					<?			
										$warning_str = "";
										$err_str = "";
									}
								} else { 
					?> 
								<tr>
									<td align="center" height="50"  colspan="25">데이터가 없습니다. </td>
								</tr>
					<? 
								}
					?>
							</tbody>
						</table>
					</div>


				<div class="btnright">
					<input type="button" name="aa" value=" 상품등록요망 리스트 " class="btntxt" onclick="js_unregistered_goods_excel();">&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type="button" name="aa2" value=" 미등록자료 엑셀받기 " class="btntxt" onclick="js_excel();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="bb" value=" 정상자료 등록 " class="btntxt" onclick="js_register();">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cc" value=" 선택자료 삭제 " class="btntxt" onclick="js_delete();">
				</div>

			</div>

			<?
				// 현재 주문 상품 수량 리스트
				$order_goods_list = listTempOrderCnt($conn, $temp_no, $has_island);
			?>
			<div class="text_frame">* 현재 주문 상품 수량 리스트 &nbsp; <a href="javascript:js_temp_goods_excel();"><img src="../images/common/btn/btn_excel.gif" alt="주문 상품 수량 리스트"></a>
			<div class="float_right"><label><input type="checkbox" onclick="js_chk_island()" name="has_island" checked='checked' value="true"/> 제주도 및 도서산간 포함</label></div>
			</div>
			<table cellpadding="0" cellspacing="0" class="rowstable">

				<colgroup>
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
					<col width="*" />
					<col width="120px" />
					<col width="120px" />
					<col width="120px" />
				</colgroup>
				<thead>
					<tr>
						<th>상품종류</th>
						<th>상품코드</th>
						<th>낱개바코드</th>
						<th>상품명</th>
						<th>재고</th>
						<th>박스입수</th>
						<th class="end">주문수량*구성상품수량 (주문총합)</th>
					</tr>
				</thead>
				<tbody>
				<?
					if (sizeof($order_goods_list) > 0) {
						
						for ($j = 0 ; $j < sizeof($order_goods_list); $j++) {
							//GOODS_NO	GOODS_CODE	GOODS_NAME DELIVERY_CNT_IN_BOX CNT
							$GOODS_NO		= trim($order_goods_list[$j]["GOODS_NO"]);
							$CATE_NAME		= trim($order_goods_list[$j]["CATE_NAME"]);
							
							//$CATE_02		= trim($order_goods_list[$j]["CATE_02"]);
							//$CATE_02 = getDcodeName($conn, "GOODS_SUB_CATE", $CATE_02);
							$GOODS_CODE		= trim($order_goods_list[$j]["GOODS_CODE"]);
							$KANCODE		= trim($order_goods_list[$j]["KANCODE"]);
							$GOODS_NAME		= SetStringFromDB($order_goods_list[$j]["GOODS_NAME"]);
							$DELIVERY_CNT_IN_BOX = trim($order_goods_list[$j]["DELIVERY_CNT_IN_BOX"]);
							$CNT		    = trim($order_goods_list[$j]["CNT"]);
							$STOCK_CNT			 = trim($order_goods_list[$j]["STOCK_CNT"]);
							$BSTOCK_CNT			 = trim($order_goods_list[$j]["BSTOCK_CNT"]);
				?>
					<tr>
						<td height="24px"><?=$CATE_NAME?></td>
						<td><?=$GOODS_CODE?></td>
						<td><?=$KANCODE?></td>
						<td class="pname" style="text-align:left;padding-left:5px;"><?=$GOODS_NAME?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($STOCK_CNT)?></td>
						<td><?=$DELIVERY_CNT_IN_BOX?></td>
						<td style="text-align:right;padding-right:5px;"><?=number_format($CNT)?></td>
					</tr>
				<?
						}
					}else {
				?>
					<tr>
						<td colspan="7" height="30">데이터가 없습니다</td>
					</tr>
				<?
					}
				?>
				</tbody>
			</table>
			<br/>
			<span>(제주, 울릉등 도서산간지역은 합산에서 제외됩니다.)</span>
			<div class="sp20"></div>
</form>

    </td>
  </tr>
  </table>
</div>
<script type="text/javascript" src="../js/wrest.js"></script>
<iframe src="" name="ifr_hidden" frameborder="no" width="0" height="0" marginwidth="0" marginheight="0" border="0"></iframe>
</body>
</html>
<?
#====================================================================
# DB Close
#====================================================================

	mysql_close($conn);
?>