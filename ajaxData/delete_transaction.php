<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
$trans_id = $_POST['trans_id'];

$q_del = "update reserve_transaction set row_active=? where atid=".$trans_id;
$params_del = array(0);
$arr_query=$local_db->query_data($q_del,$params_del);

$q_flow = "insert into flow (
	   [userid]
      ,[username]
      ,[reserve_id]
      ,[flow_date]
      ,[typeoflow]
      ,[status]
      ,[status_note]) OUTPUT Inserted.atid as id values  (?,?,?,?,?,?,?)";
$params_flow = array($_POST['userid'],$_POST['username'],$trans_id,$today,"delete",0,NULL);
$arr_flow=$local_db->query_data($q_flow,$params_flow);


?>