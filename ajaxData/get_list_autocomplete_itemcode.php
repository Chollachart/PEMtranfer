<?php
require_once '../function.php';
$arr_return = array();
$arr_return_all = array();
$class_q = new db_class("localhost");
$source = $_POST["company_source"];
$destination = $_POST["company_destination"];

$arr_itemcode=$class_q->query_data("select * from item_map where itemcode_".$_POST["company_source"]." is not null ",NULL);
//print_r($arr_itemcode);
$i=0;
while($i<sizeof($arr_itemcode)){
	array_push($arr_return,array("label"=>trim($arr_itemcode[$i]['itemcode_'.$source])." - ".trim($arr_itemcode[$i]['itemdes_'.$source]),"atid"=>$arr_itemcode[$i]['atid'],"itemcode_source"=>trim($arr_itemcode[$i]['itemcode_'.$source]),"itemdes_source"=>trim($arr_itemcode[$i]['itemdes_'.$source]),"itemcode_destination"=>trim($arr_itemcode[$i]['itemcode_'.$destination]),"itemdes_destination"=>trim($arr_itemcode[$i]['itemdes_'.$destination]),"value"=>trim($arr_itemcode[$i]['itemcode_'.$source])));
	$i++;
}
$arr_return_all = array(true,$arr_return);
echo json_encode($arr_return_all);
?>