<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();

$q_yrf = "select yr.yrf_item_id,yr.yrf_itemcode,yr.yrf_doc_number,yr.yrf_qty from your_ref yr inner join reserve_item ri on yr.yrf_item_id = ri.atid inner join reserve_transaction rt on ri.reserve_transaction_id = rt.atid where rt.atid=? order by yr.yrf_item_id asc";
$params_yrf = array($_POST['trans_id']);
$array_q_yrf = $local_db->query_data($q_yrf,$params_yrf);

if(is_array($array_q_yrf)&&sizeof($array_q_yrf)>0){
	$arr_return = array();
	foreach ($array_q_yrf as $key => $value) {
		if(!isset($arr_return["index_".$value['yrf_item_id']])){
			$arr_return["index_".$value['yrf_item_id']] = array(array($value['yrf_item_id'],$value['yrf_itemcode'],$value['yrf_doc_number'],$value['yrf_qty']));
		}else{
			array_push($arr_return["index_".$value['yrf_item_id']],array($value['yrf_item_id'],$value['yrf_itemcode'],$value['yrf_doc_number'],$value['yrf_qty']));
		}
 	} 
	echo json_encode(array(true,$arr_return));
}else{
	echo json_encode(array(false,$array_q_yrf));
}
?>