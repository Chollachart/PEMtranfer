<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
$item_id_size = sizeof($_POST['item_id_arr']);

if($item_id_size>0){
	$i=0;
	$q_delete_item = "delete from your_ref ";
	while($i<$item_id_size){
		if($i==0){
			$q_delete_item.=" where yrf_item_id='".$_POST['item_id_arr'][$i]."' ";
		}else{
			$q_delete_item.=" or yrf_item_id='".$_POST['item_id_arr'][$i]."' ";
		}		
		$i++;
	}
	$arr_delete = $local_db->query_data($q_delete_item,NULL);

	if($_POST['type_save']=="edit"){
		foreach ($_POST['your_ref_obj'] as $key => $value) {
			$array_loop = $value;
			foreach ($array_loop as $key => $value) {
				$array_save = $value;  //array 0 = item_id , 1 itemcode , 2 = ref , 3 = qty
				$q_insert = "insert into your_ref ([yrf_item_id],[yrf_doc_number],[yrf_itemcode],[yrf_qty],[yrf_user_id],[yrf_user_name],[yrf_modify_date],[yrf_confirm]) OUTPUT Inserted.yrf_item_id as item_id values (?,?,?,?,?,?,?,?)";
				$q_params = array($array_save[0],$array_save[2],$array_save[1],$class_general->split_comma($array_save[3]),$_POST['userid'],$_POST['username'],$today,0);	
				$arr_save = $local_db->query_data($q_insert,$q_params);
			}
		}
	}
}
?>