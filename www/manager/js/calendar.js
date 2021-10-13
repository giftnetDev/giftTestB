function show_calendar(str_target, str_datetime) {

	var arr_months = ["1월", "2월", "3월", "4월", "5월", "6월",	"7월", "8월", "9월", "10월", "11월", "12월"];
	var arr_months_dt = ["01", "02", "03", "04", "05", "06",	"07", "08", "09", "10", "11", "12"];
	
	var week_days = ["일", "월", "화", "수", "목", "금", "토"];
	
	var n_weekstart = 1;

	var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));

	var dt_prev_month = new Date(dt_datetime);//
	dt_prev_month.setMonth(dt_datetime.getMonth()-1);

	var dt_next_month = new Date(dt_datetime);//
	dt_next_month.setMonth(dt_datetime.getMonth()+1);

	var dt_firstday = new Date(dt_datetime);
	dt_firstday.setDate(1);
	dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
	var dt_lastday = new Date(dt_next_month);
	dt_lastday.setDate(0);

	var str_buffer = new String (
		"<html>\n"+
		"<head>\n"+
		"	<title>Calendar</title>\n"+
		"<script type=\"text/javascript\">\n"+
		"function go_date() { \n"+
		"var cal_data = document.cal.sel_year.value + \"-\" +document.cal.sel_month.value + \"-"+dt2daystr(dt_datetime.getDate())+"\";\n "+
		"window.opener.show_calendar('"+str_target+"', cal_data); \n"+
		"}"+
		"</script>\n"+
		"</head>\n"+
		"<body bgcolor=\"White\">\n"+
		"<table cellspacing=\"0\" border=\"0\" width=\"100%\">\n"+
		"<form name=\"cal\"><tr><td bgcolor=\"#4682B4\">\n"+
		"<table cellspacing=\"1\" cellpadding=\"3\" border=\"0\" width=\"100%\">\n"+
		"<tr>\n	<td bgcolor=\"#4682B4\"><a href=\"javascript:window.opener.show_calendar('"+
		str_target+"', '"+ dt2dtstr(dt_prev_month)+"');\">"+
		"<img src=\"/manager/images/calendar/prev.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"previous month\"></a></td>\n"+
		"	<td bgcolor=\"#4682B4\" colspan=\"5\" align=\"center\">"+
		"<b><font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"+
		"<select name=\"sel_year\" onChange=\"go_date();\">"+
		"<option value=\""+(dt_datetime.getFullYear() -3 )+"\">"+(dt_datetime.getFullYear() -3)+"</option>"+
		"<option value=\""+(dt_datetime.getFullYear() -2 )+"\">"+(dt_datetime.getFullYear() -2)+"</option>"+
		"<option value=\""+(dt_datetime.getFullYear() -1 )+"\">"+(dt_datetime.getFullYear() -1)+"</option>"+
		"<option value=\""+dt_datetime.getFullYear()+"\" selected>"+dt_datetime.getFullYear()+"</option>"+
		"<option value=\""+(dt_datetime.getFullYear() +1 )+"\">"+(dt_datetime.getFullYear() +1)+"</option>"+
		"<option value=\""+(dt_datetime.getFullYear() +2 )+"\">"+(dt_datetime.getFullYear() +2)+"</option>"+
		"</select name=\"sel_year\"> 년 "+
		"<select name=\"sel_month\" onChange=\"go_date();\">"
	);
	
	for (i = 1; i < (arr_months_dt.length + 1) ; i++) {
		if (dt_datetime.getMonth() == (i-1)) {
			str_buffer += "<option value=\""+ dt2monthstr(i) +"\" selected>"+ dt2monthstr(i) +"</option>"
		} else {
			str_buffer += "<option value=\""+ dt2monthstr(i) +"\">"+ dt2monthstr(i) +"</option>"
		}
	}

	str_buffer += "</select>"+
		" 월</font></b></td>\n"+
		"	<td bgcolor=\"#4682B4\" align=\"right\"><a href=\"javascript:window.opener.show_calendar('"
		+str_target+"', '"+dt2dtstr(dt_next_month)+"');\">"+
		"<img src=\"/manager/images/calendar/next.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"next month\"></a></td>\n</tr>\n";


	var dt_current_day = new Date(dt_firstday);
	str_buffer += "<tr>\n";
	for (var n=0; n<7; n++)
		str_buffer += "	<td bgcolor=\"#87CEFA\">"+
		"<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"+
		week_days[(n_weekstart+n)%7]+"</font></td>\n";
	str_buffer += "</tr>\n";
	while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
		dt_current_day.getMonth() == dt_firstday.getMonth()) {
		str_buffer += "<tr>\n";
		for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
				if (dt_current_day.getDate() == dt_datetime.getDate() &&
					dt_current_day.getMonth() == dt_datetime.getMonth())
					str_buffer += "	<td bgcolor=\"#FFB6C1\" align=\"right\">";
				else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
					str_buffer += "	<td bgcolor=\"#DBEAF5\" align=\"right\">";
				else
					str_buffer += "	<td bgcolor=\"white\" align=\"right\">";

				if (dt_current_day.getMonth() == dt_datetime.getMonth())
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'; window.close();\">"+
					"<font color=\"black\" face=\"tahoma, verdana\" size=\"2\">";
				else
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'; window.close();\">"+
					"<font color=\"gray\" face=\"tahoma, verdana\" size=\"2\">";
				str_buffer += dt_current_day.getDate()+"</font></a></td>\n";
				dt_current_day.setDate(dt_current_day.getDate()+1);
		}
		str_buffer += "</tr>\n";
	}
	str_buffer +=
		"</form>\n" +
		"</table>\n" +
		"</tr>\n</td>\n</table>\n" +
		"</body>\n" +
		"</html>\n";

	var vWinCal = window.open("", "Calendar",
		"width=200,height=200,status=no,resizable=yes,top=200,left=200");
	vWinCal.opener = self;
	var calc_doc = vWinCal.document;
	calc_doc.write (str_buffer);
	calc_doc.close();
}

function str2dt (str_datetime) {
	//var re_date = /^\d{1,4}\-\d{1,2}\-\d{1,2}$/;
	var re_date = /^(\d+)\-(\d+)\-(\d+)$/;

	if (!re_date.exec(str_datetime))
		return alert("유효한 날짜형식이 아닙니다 : "+ str_datetime);
//	return (new Date (RegExp.$3, RegExp.$2-1, RegExp.$1, RegExp.$4, RegExp.$5, RegExp.$6));
	return (new Date (RegExp.$1, RegExp.$2-1, RegExp.$3));
}


function dt2dtstr (dt_datetime) {
	
	var str_month =(dt_datetime.getMonth()+1) + "";
	
	if (str_month.length == 1 ) {
		str_month = "0"+str_month;
	}

	var str_day =dt_datetime.getDate() + "";
	
	if (str_day.length == 1 ) {
		str_day = "0"+str_day;
	}

	return (new String (
//			dt_datetime.getDate()+"-"+(dt_datetime.getMonth()+1)+"-"+dt_datetime.getFullYear()+" "));
			dt_datetime.getFullYear()+"-"+str_month+"-"+str_day));
}


function dt2tmstr (dt_datetime) {
	return (new String (
			dt_datetime.getHours()+":"+dt_datetime.getMinutes()+":"+dt_datetime.getSeconds()));
}

function dt2monthstr(dt_datemonth) {
	
	var str_month = dt_datemonth + "";

	if (str_month.length == 1 ) {
		str_month = "0"+str_month;
	}

	return (new String (str_month));
}

function dt2daystr(dt_dateday) {
	
	var dt_dateday = dt_dateday + "";

	if (dt_dateday.length == 1 ) {
		dt_dateday = "0"+dt_dateday;
	}

	return (new String (dt_dateday));
}