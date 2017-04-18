<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$class_general = new general_class();
$today = $class_general->get_datetime_today();
if($_POST['cmp_code']=="PEM")
	{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM1";}
else{$cmp_code =$_POST['cmp_code'];  $cmp_opposite = "PEM";}

$check_opposite = "select * from item_map where atid=?";
$params_opposite = array($_POST['itematid_edit']);
$arr_check_opposite = $local_db->query_data($check_opposite,$params_opposite);
if(!is_array($arr_check_opposite)||(is_array($arr_check_opposite)&&sizeof($arr_check_opposite)==0)){
	echo json_encode(array(false,"error")); exit();
}else{
	
	$old_itemcode_opposite = $arr_check_opposite[0]['itemcode_'.$cmp_opposite];
	$check_dupp = "select * from item_map where itemcode_".$cmp_opposite."=? and itemcode_".$cmp_code."=? and atid!=?";
	$params_dupp = array($old_itemcode_opposite,$_POST['itemcode_edit'],$_POST['itematid_edit']);
	$arr_check_dupp = $local_db->query_data($check_dupp,$params_dupp);
	if(!is_array($arr_check_dupp)||(is_array($arr_check_dupp)&&sizeof($arr_check_dupp)>0)){
		echo json_encode(array(false,"Itemcode ที่เลือกเคยมีการจับคู่กันแล้วกับฝั่งตรงข้าม\nไม่สามารถเลือก Itemcode นี้ได้")); exit();
	}else{
		$str_q = "update item_map set itemcode_".$cmp_code."=?,itemdes_".$cmp_code."=? OUTPUT Inserted.atid as id where atid=?";
		$params_q = array($_POST['itemcode_edit'],$_POST['itemdes_edit'],$_POST['itematid_edit']);
		$arr_return = $local_db->query_data($str_q,$params_q);
		if(is_array($arr_return)&&sizeof($arr_return)>0){
			echo json_encode(array(true,$arr_return[0]['id']));
		}else{
			echo json_encode(array(false,$arr_return));
		}
	}
}
?>