<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
if($_POST['cmp_code']=="PEM")
	{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM1";}
else{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM";}

$str_check_item = "select * from item_map where atid=?";
$arr_check = $local_db->query_data($str_check_item,array($_POST['itematid_edit']));
if(!is_array($arr_check)||(is_array($arr_check)&&sizeof($arr_check)==0)){
	echo json_encode(array(false,"error"));
	exit();
}else{
	if($arr_check[0]['itemcode_'.$cmp_opposite]==NULL||$arr_check[0]['itemcode_'.$cmp_opposite]==""){
		$str_delete = "delete from item_map where atid=?";
		$arr_delete = $local_db->query_data($str_delete,array($_POST['itematid_edit']));
		
	}else{
		$str_update = "update item_map set itemcode_".$cmp_code."=NULL,itemdes_".$cmp_code."=NULL where atid=?";
		$arr_update = $local_db->query_data($str_update,array($_POST['itematid_edit']));
	}
	echo json_encode(array(true,"ยกเลิกการจับคู่สำเร็จ"));
}
?>