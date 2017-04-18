<?php
require_once '../function.php';
$arr_return = array();
$arr_return_all = array();
$class_q = new db_class($_POST['cmp_code']);

$arr_itemcode=$class_q->query_data("select distinct(ItemCode),Description from Items ",NULL);
$i=0; //echo sizeof($arr_itemcode);
//array_push($arr_return,array("label"=>"Unmatched","value"=>"Unmatched","itemcode"=>"Unmatched","description"=>"Unmatched"));
while($i<sizeof($arr_itemcode)){
	$item = $arr_itemcode[$i];
	array_push($arr_return,array("label"=>$item['ItemCode']." - ".$item['Description'],"value"=>$item['ItemCode'],"itemcode"=>$item['ItemCode'],"description"=>$item['Description']));
	$i++;
}
$arr_return_all = array(true,$arr_return);
echo json_encode($arr_return_all);
?>