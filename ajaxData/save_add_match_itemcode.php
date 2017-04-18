<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
if($_POST['cmp_code']=="PEM")
	{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM1";}
else{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM";}

$str_check_item = "select * from item_map where itemcode_".$cmp_code."=? and itemcode_".$cmp_opposite." is null ";
$arr_check = $local_db->query_data($str_check_item,array($_POST['itemcode_add']));
if(!is_array($arr_check)||(is_array($arr_check)&&sizeof($arr_check)>0)){
	echo json_encode(array(false,"มี Itemcode นี้ถูกเพิ่มไปแล้ว และยังไม่ถูก Match"));
	exit();
}else{
	$str_q = "insert into item_map ([itemcode_".$cmp_code."]
	      ,[itemdes_".$cmp_code."]) OUTPUT Inserted.atid as id values (?,?) ";
	$params_q = array($_POST['itemcode_add'],$_POST['itemdes_add']);
	$arr_return = $local_db->query_data($str_q,$params_q);
	if(is_array($arr_return)&&sizeof($arr_return)>0){
		echo json_encode(array(true,$arr_return[0]['id']));
	}else{
		echo json_encode(array(false,$arr_return));
	}
}
?>