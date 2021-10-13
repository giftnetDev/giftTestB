function js_gd_cate_01() {

	var frm = document.frm;
	
	var obj02 = "gd_cate_02";
	obj = eval("frm."+obj02);
	
	if (obj != null) {
		clear_select(obj);
	}

	var obj03 = "gd_cate_03";
	obj = eval("frm."+obj03);

	if (obj != null) {
		clear_select(obj);
	}

	var obj04 = "gd_cate_04";
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (frm.gd_cate_01.value != "") {
		frm.depth.value = "1";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_gd_cate_02() {
	var frm = document.frm;

	var obj03 = "gd_cate_03";
	obj = eval("frm."+obj03);

	if (obj != null) {
		clear_select(obj);
	}

	var obj04 = "gd_cate_04";
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (frm.gd_cate_02.value != "") {
		frm.depth.value = "2";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_gd_cate_03() {
	var frm = document.frm;

	var obj04 = "gd_cate_04";
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (frm.gd_cate_03.value != "") {
		frm.depth.value = "3";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_gd_cate_04() {
	var frm = document.frm;

	var obj05 = "gd_cate_05";
	obj = eval("frm."+obj05);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (frm.gd_cate_04.value != "") {
		frm.depth.value = "4";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function add_cate_select(depth, value, text, index){

	var obj = "";
	if (depth == "1") 
		obj = eval("document.frm.gd_cate_02");

	if (depth == "2") 
		obj = eval("document.frm.gd_cate_03");

	if (depth == "3") 
		obj = eval("document.frm.gd_cate_04");

	if (obj != null) {
		obj.options[index] = new Option(text, value);
	}
}

///////////////////////////// generic
function js_generic_cate_01(obj_name) {

	var frm = document.frm;
	
	var obj02 = obj_name + "_02";
	obj = eval("frm."+obj02);
	
	if (obj != null) {
		clear_select(obj);
	}

	var obj03 = obj_name + "_03";
	obj = eval("frm."+obj03);

	if (obj != null) {
		clear_select(obj);
	}

	var obj04 = obj_name + "_04";
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (document.getElementsByName(obj_name + "_01").value != "") {
		frm.objname.value = obj_name;
		frm.depth.value = "1";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_generic_cate_02(obj_name) {
	var frm = document.frm;

	var obj03 = obj_name + "_03";
	obj = eval("frm."+obj03);

	if (obj != null) {
		clear_select(obj);
	}

	var obj04 = obj_name + "_04"
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (document.getElementsByName(obj_name + "_02").value != "") {
		frm.objname.value = obj_name;
		frm.depth.value = "2";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_generic_cate_03(obj_name) {
	var frm = document.frm;

	var obj04 = obj_name + "_04";
	obj = eval("frm."+obj04);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (document.getElementsByName(obj_name + "_03").value != "") {
		frm.objname.value = obj_name;
		frm.depth.value = "3";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function js_generic_cate_04(obj_name) {
	var frm = document.frm;

	var obj05 = obj_name + "_05";
	obj = eval("frm."+obj05);

	if (obj != null) {
		clear_select(obj);
	}
	
	if (document.getElementsByName(obj_name + "_04").value != "") {
		frm.objname.value = obj_name;
		frm.depth.value = "4";
		frm.target = "ifr_hidden";
		frm.action = "../../_common/get_next_cate.php";
		frm.submit();
	}
}

function add_generic_cate_select(objname, depth, value, text, code, index){

	var obj = "";
	if (depth == "1") 
		obj = eval("document.frm."+objname+ "_0" + (parseInt(depth) + 1));

	if (depth == "2") 
		obj = eval("document.frm."+objname+ "_0" + (parseInt(depth) + 1));

	if (depth == "3") 
		obj = eval("document.frm."+objname+ "_0" + (parseInt(depth) + 1));

	if (obj != null) {
		obj.options[index] = new Option(text, value);
		obj.options[index].setAttribute("data-code", code);
	}
}