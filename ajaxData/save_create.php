<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
$arr_return = array();
$q_save = "insert into [reserve_transaction] (
	   [reserve_no]
	  ,[source_cmp]
      ,[source_div]
      ,[source_user]
      ,[source_user_name]
      ,[destination_cmp]
      ,[destination_div]
      ,[cause_id]
      ,[cause_detail]
      ,[source_note]
      ,[modifydate]
      ,[status]
      ,[row_active]) OUTPUT Inserted.atid as id values (?,?,?,?,?,?,?,?,?,?,?,?,?)";
$params_save = array($class_general->gen_tranfer_no($_POST['company_source']),$_POST['company_source'],$_POST['div_source'],$_POST['userid'],$_POST['username'],$_POST['company_destination'],$_POST['div_destination'],$_POST['reason'],$_POST['reason_detail'],$_POST['note'],$today,1,1);
$arr_query=$local_db->query_data($q_save,$params_save);

if(is_array($arr_query)&&sizeof($arr_query)>0){
	
	///////////////////////////////////////////////////////////////////// LOOP Insert Item
	$q_item = "insert into reserve_item (
	   [reserve_transaction_id]
      ,[item_id]
      ,[qty]
      ,[expect_date]
      ,[note_item]) OUTPUT Inserted.atid as id values (?,?,?,?,?)";
	$i=0;
	while($i<sizeof($_POST['array_item_insert'])){
		$arr_item = $_POST['array_item_insert'][$i];
		$params_item = array($arr_query[0]['id'],$arr_item[1],$class_general->split_comma($arr_item[7]),$class_general->change_date_to_db($arr_item[6]),$arr_item[8]);
		$arr_item_return=$local_db->query_data($q_item,$params_item);
		$i++;
	} 
	
	///////////////////////////////////////////////////////////////////// Insert Flow
	$q_flow = "insert into flow (
	   [userid]
      ,[username]
      ,[reserve_id]
      ,[flow_date]
      ,[typeoflow]
      ,[status]
      ,[status_note]) OUTPUT Inserted.atid as id values (?,?,?,?,?,?,?)";
	$params_flow = array($_POST['userid'],$_POST['username'],$arr_query[0]['id'],$today,"create",1,NULL);
	$arr_query_flow=$local_db->query_data($q_flow,$params_flow);
	//////////////////////////////////////////////////////////////////////

	array_push($arr_return,true);
	array_push($arr_return,$arr_query[0]['id']);
	
}else{
	array_push($arr_return,false);
	array_push($arr_return,$arr_query);
}
echo json_encode($arr_return);
?>