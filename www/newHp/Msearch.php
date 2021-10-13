
    <form  name="frm" role="search" action="Msearch_list.php" method="get">
	<div id="dv_search" style="width:100%; height:50px; background-color:rgba(255, 255, 255, 0.8);margin-top:60px; text-align:center; position:fixed; z-index:10; border:solid #DDDDDD 2px; backdrop-filter: blur(20px);">
		<input type="text" id="search_str" name="search_str" placeholder="<?=($search_str == "" ? "" : $search_str)?>">
		<button type="submit"></button>
	</div>
    </form>

