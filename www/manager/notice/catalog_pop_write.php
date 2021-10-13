<?session_start();?>
<?

#====================================================================
# DB Include, DB Connection
#====================================================================
	require "../../_classes/com/db/DBUtil.php";

	$conn = db_connection("w");

#==============================================================================
# Confirm right
#==============================================================================
	$menu_right = "BO008"; // 메뉴마다 셋팅 해 주어야 합니다

#====================================================================
# common_header Check Session
#====================================================================
	require "../../_common/common_header.php"; 
	
#=====================================================================
# common function, login_function
#=====================================================================
	require "../../_common/config.php";
	require "../../_classes/com/util/Util.php";
	require "../../_classes/com/etc/etc.php";
	require "../../_classes/biz/board/board.php";
	require "../../_classes/biz/board/catalog_pop.php";

	$file_path="/upload_data/catalog_pop/";


#====================================================================
# DML Process
#====================================================================

	if ($mode == "S") {

		$arr_rs = catalog_pop_Sel($conn, $catalog_no);
		$CTLPOP_NO				= trim($arr_rs[0]["CTLPOP_NO"]);
		$TITLE					= SetStringFromDB($arr_rs[0]["TITLE"]);
		$CTLPOP_START			= trim($arr_rs[0]["CTLPOP_START"]);
		$CTLPOP_END				= trim($arr_rs[0]["CTLPOP_END"]);
		$FILE_NM				= trim($arr_rs[0]["FILE_NM"]);
		$FILE_RNM				= trim($arr_rs[0]["FILE_RNM"]);
		$FILE_PATH				= trim($arr_rs[0]["FILE_PATH"]);
		$FILE_SIZE				= trim($arr_rs[0]["FILE_SIZE"]);
		$FILE_EXT				= trim($arr_rs[0]["FILE_EXT"]);
		$REG_ADM				= trim($arr_rs[0]["REG_ADM"]);
		$REG_DATE				= trim($arr_rs[0]["REG_DATE"]);
		$HIT_CNT				= trim($arr_rs[0]["HIT_CNT"]);
		$REG_DATE 				= date("Y-m-d",strtotime($REG_DATE));
		
		$file_full_path=$FILE_PATH.$FILE_NM;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$g_charset?>" />
<title><?=$g_title?></title>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/httpRequest.js"></script> <!-- Ajax js -->
<script type="text/javascript" src="../jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-datepicker-ko.js"></script>
<link rel="stylesheet" href="../jquery/jquery-ui.min.css" type="text/css" />
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<style type="text/css">
#pop_table_scroll { z-index: 1;  overflow: auto; width:95%; height: 250px; }
</style>
<script>
  $(function() {
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
  });
</script>

<script language="Javascript">

    $.fn.setPreview = function (opt) {
	    "use strict"
    	var defaultOpt = {
        	inputFile: $(this),
        	img: null,
        	w: 'auto',
        	h: 'auto'
    	};
    	$.extend(defaultOpt, opt);

    	var previewImage = function () {
        	if (!defaultOpt.inputFile || !defaultOpt.img)
          	return;

        var inputFile = defaultOpt.inputFile.get(0);
        var img = defaultOpt.img.get(0);

        // FileReader
        if (window.FileReader) {
        	
          	if (!inputFile.files[0].type.match(/image\//))
            return;

          // preview
          try {
          	var reader = new FileReader();
            	reader.onload = function (e) {
              	img.src = e.target.result;
             	img.style.width = defaultOpt.w + 'px';
              	if (defaultOpt.h != 'auto')
                img.style.height = defaultOpt.h + 'px';
              	else
                img.style.height = 'auto';
              	img.style.display = '';
            }
            reader.readAsDataURL(inputFile.files[0]);
        } catch (e) {
          // exception...
        }
          // img.filters (MSIE)
        } else if (img.filters) {
        	inputFile.select();
          	inputFile.blur();
          	var imgSrc = document.selection.createRange().text;

          	img.style.width = defaultOpt.w + 'px';
          	if (defaultOpt.h != 'auto')
            img.style.height = defaultOpt.h + 'px';
          	else
            img.style.height = 'auto';
          	//    img.style.height = defaultOpt.h+'px';
          	img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enable='true',sizingMethod='scale',src=\"" + imgSrc + "\")";
          	img.style.display = '';
          	// no support
        } else {
          // Safari5, ...
        }
    };

    // onchange
    $(this).change(function () {
    	previewImage();
      	});
    };

		$(document).ready(function(){
			var opt = {
				img: $('#preview')
				, w: 'auto'
				, h: 'auto'
			};
			$('#file_nm').setPreview(opt);
		});  
</script>

<script language="javascript" type="text/javascript">

function js_list() {
	var frm = document.frm;

	frm.method = "post";
	frm.action = "catalog_pop_list.php";
	frm.submit();
}

function js_check(type)
{
	var frm = document.frm;
	
	if (isNull(frm.title.value)) 
	{
		alert('제목을 입력해주세요.');
		frm.title.focus();
		return;		
	}
	
	if(frm.ctlpop_start.value == "")
	{
		alert('등록기간을 선택 해 주세요.');
		frm.ctlpop_start.focus();
		return ;		
	}

	if(frm.ctlpop_end.value == "")
	{
		alert('등록기간을 선택 해 주세요.');
		frm.ctlpop_end.focus();
		return ;		
	}

	var date1 = frm.ctlpop_start.value;
	var date2 = frm.ctlpop_end.value;
		
	date1 = date1.replace('-','');
	date2 = date2.replace('-','');
	date1 = date1.replace('-','');
	date2 = date2.replace('-','');
	
	if(parseInt(date1) > parseInt(date2)) { alert('시작날짜가 종료날짜보다 큽니다.'); return;}

	if(type == "I")
	{
		if(document.getElementById("file_name").value == "")
		{
			alert('첨부파일을 등록 해주세요.');
			frm.file_nm.focus();
			return ;		
		}

		var ext = $('#file_nm').val().split('.').pop().toLowerCase();
		
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) 		
		{
			alert('gif, png, jpg, jpeg 파일만 업로드 할 수 있습니다.');
			$('#file_nm').val("");
			$('#file_name').val("");
			$('#file_nm').focus();
			return;
		}	
	}
	else
	{
		if(frm.Attachments.value == "Y")
		{
			var ext = $('#file_nm').val().split('.').pop().toLowerCase();

			if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) 		
			{
				alert('gif, png, jpg, jpeg 파일만 업로드 할 수 있습니다.');
				$('#file_name').val($("input[name=filernm]").val());
				$('#file_nm').focus();
				return;
			}
		}			
	}
	
	return true;
}

function js_save() 
{
	var frm = document.frm;
	
	if (!js_check('I'))	return;

	if (!confirm("저장 하시겠습니까?")) return;	

	var file = $("input[name='file_nm']")[0].files[0];

		datas = new FormData();
		datas.append("mode", "INSERT_CATALOG");

		datas.append("reg_adm", "<?=$s_adm_no?>");
		datas.append("catalog_no", frm.catalog_no.value);
		datas.append("catalog_title", frm.title.value);
		datas.append("ctlpop_start",frm.ctlpop_start.value);
		datas.append("ctlpop_end", frm.ctlpop_end.value);

		datas.append("file", $("input[name='file_nm']")[0].files[0]);

		$.ajax({
			url: 'json_catalog_pop.php',
			dataType: 'json',
			contentType: 'multipart/form-data', 
			mimeType: 'multipart/form-data',
			type: 'post',
			data : datas,
			success: function(response) {
				alert("저장 되었습니다.");
				frm.Attachments.value = "";
			},
			error : function (jqXHR, textStatus, errorThrown) {
				alert('ERRORS: ' + textStatus);
			},
			cache: false,
			contentType: false,
			processData: false
		});
}


function js_update() 
{
	var frm = document.frm;
	
	if (!js_check('U'))	return;

	if (!confirm("수정 하시겠습니까?")) return;	
	
		var file = $("input[name='file_nm']")[0].files[0];

		datas = new FormData();
		datas.append("mode", "UPDATE_CATALOG");

		datas.append("reg_adm", "<?=$s_adm_no?>");
		datas.append("catalog_no", frm.catalog_no.value);
		datas.append("catalog_title", frm.title.value);
		datas.append("ctlpop_start",frm.ctlpop_start.value);
		datas.append("ctlpop_end", frm.ctlpop_end.value);

		datas.append("filenm", frm.filenm.value);
		datas.append("filernm", frm.filernm.value);
		datas.append("filesize",frm.filesize.value);
		datas.append("fileext", frm.fileext.value);

		datas.append("file", $("input[name='file_nm']")[0].files[0]);

		$.ajax({
			url: 'json_catalog_pop.php',
			dataType: 'json',
			contentType: 'multipart/form-data', 
			mimeType: 'multipart/form-data',
			type: 'post',
			data : datas,
			success: function(response) {
				alert("수정이 완료 되었습니다.");
				frm.Attachments.value = "";
			},
			error : function (jqXHR, textStatus, errorThrown) {
				alert('ERRORS: ' + textStatus);
			},
			cache: false,
			contentType: false,
			processData: false
		});

}


function file_change(file_nm) { 
//alert(file_nm);
	document.getElementById("file_name").value = file_nm; 
	document.frm.Attachments.value = "Y";
}


function js_delete() {

	var frm = document.frm;

	if (!confirm("삭제 하시겠습니까?")) return;	
	
	$.ajax({
		url: "json_catalog_pop.php",
		dataType: 'json',
		type: 'post',
		async: true,
		data: {
				  mode: "DELETE_CATALOG"					
				, catalog_no: frm.catalog_no.value
			},
			success: function(data) 
			{
				alert("삭제 되었습니다. 목록으로 이동합니다.");
				js_list();
			}	,
			error: function(jqXHR, textStatus, errorThrown)
			{
				alert('통신 실패');
				return;
			}
	});
}


</script>

</head>
<body id="admin" onresize="BodyMinSize();">

<form name="frm" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="catalog_no" value="<?=$catalog_no?>" />
<input type="hidden" name="nPage" value="<?=$nPage?>" />
<input type="hidden" name="nPageSize" value="<?=$nPageSize?>" />
<input type="hidden" name="Attachments" value="" />

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
		</td>
		<td class="contentarea">
      <!-- S: mwidthwrap -->
      <div id="mwidthwrap">
        <h2>카다로그 팝업등록</h2>  		
        <div class="btnright">
		<?
			if ($mode == "I") {
		?>
			<a href="javascript:js_save();"><img src="../images/admin/btn_confirm.gif" alt="확인" /></a>
			<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
		<?	} else {	?>
          	<a href="javascript:js_update();"><img src="../images/admin/btn_modify.gif" alt="수정" /></a>
			<a href="javascript:js_delete();"><img src="../images/admin/btn_delete.gif" alt="삭제" /></a>
          	<a href="javascript:js_list();"><img src="../images/admin/btn_list.gif" alt="목록" /></a>
		 <?	}	?>            	
        </div>
        <table cellpadding="0" cellspacing="0" class="colstable02">
        <colgroup>
          <col width="120" />
          <col width="*" />
        </colgroup>
		<tr>
          <th>제목</th>
		  <td class="line">
				<input type="text" class="txt" name="title" value="<?=$TITLE?>" style="width: 80%; height:30%;"  />
			</td>
        </tr>	
        <tr>
		<th>등록 기간</th>
		<td class="line">
				<input name="ctlpop_start" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $CTLPOP_START ?>"> ~ 
				<input name="ctlpop_end" type="text" class="txt datepicker" style="width:80px; margin-right:3px;" readonly value="<?= $CTLPOP_END ?>">
			</td>
        </tr>	
        <tr>
          <th>첨부</th>
          	<td class="line">
				<input type="text" id="file_name" disabled="disabled" value="<?=$FILE_RNM?>" class="txt" style="width: 80%; height:30%;"  /> 
				<input type="file" id="file_nm" name="file_nm"  accept=".gif, .jpg, .jpeg, .png" style="width: 5%" onchange="file_change(this.value);"/> 
				<input type="hidden" name="filenm" value="<?=$FILE_NM?>" />
				<input type="hidden" name="filernm" value="<?=$FILE_RNM?>" />
				<input type="hidden" name="filesize" value="<?=$FILE_SIZE?>" />
				<input type="hidden" name="fileext" value="<?=$FILE_EXT?>" />
			</td>
        </tr>
		<tr>
			<th>첨부사진 미리보기</th>
			<td>
				&nbsp;&nbsp;<img name = "preview" id="preview" src="<?=$file_full_path?>">
			</td>
		</tr>
        </table>      
      </div>
      <!-- // E: mwidthwrap -->
    </td>
  </tr>

  </table>
</div>
</form>
</body>
</html>
<?
#=====================================================================
# DB Close
#=====================================================================
	mysql_close($conn);
?>