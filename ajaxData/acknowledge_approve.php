<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();

$today = $class_general->get_datetime_today();
$trans_id = $_POST['trans_id'];

$query_approve = "update reserve_transaction set status=?,serial_reserve=?,confirm_deldate=?,job=? OUTPUT Inserted.atid as id where atid='".$trans_id."'";
$query_flow = "insert into flow (
	   [userid]
      ,[username]
      ,[reserve_id]
      ,[flow_date]
      ,[typeoflow]
      ,[status]
      ,[status_note]
      ) OUTPUT Inserted.atid as id values (?,?,?,?,?,?,?)";

if($_POST['approve_action']=="true"){
	$query_params = array(3,$_POST['approve_serial'],$class_general->change_date_to_db($_POST['approve_delivery_date']),$_POST['approve_job']);
	$params_flow = array($_POST['userid'],$_POST['username'],$trans_id,$today,'approve',3,NULL);
	
}else{
	$query_params = array(2);
	$params_flow = array($_POST['userid'],$_POST['username'],$trans_id,$today,'decline',2,$_POST['decline_reason']);
}
$arr_update_status = $local_db->query_data($query_approve,$query_params);

if(is_array($arr_update_status)&&sizeof($arr_update_status)>0){
	$arr_flow_status = $local_db->query_data($query_flow,$params_flow);
	if(is_array($arr_flow_status)&&sizeof($arr_flow_status)>0){
		echo json_encode(array(true,$trans_id));
	}else{
		echo json_encode(array(false,$arr_flow_status));
	}
}


?>