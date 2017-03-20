<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$i=0; $arr_item_all = array();
$arr_item=$local_db->query_data("select * from item_map",NULL);
if(is_array($arr_item)){
	$arr_loop_item = array();
	array_push($arr_item_all,true);
	$i=0;
	while($i<sizeof($arr_item)){
		$arr_loop_item[$arr_item[$i]['atid']] = $arr_item[$i];
		$i++;
	}
	array_push($arr_item_all,$arr_loop_item);
	
}else{
	array_push($arr_item_all,false);
}
echo json_encode($arr_item_all);
?>