<?php
require_once '../function.php';
$class_db = new db_class($_POST['company_code']);
$class_general = new general_class();

$query_yourref = "select ISNULL(SUM(ISNULL(aantal,0)),0) as qty from gbkmut where RTRIM(LTRIM(docnumber))=? and RTRIM(LTRIM(artcode))=? and transtype='N' and reknr IN ('117111','117114') and transsubtype='B'";
$params = array(trim($_POST['your_ref']),trim($_POST['itemcode']));

$arr_return = $class_db->query_data($query_yourref,$params);
if(is_array($arr_return)&&sizeof($arr_return)>0){
	if($arr_return[0]['qty']>0){
		echo json_encode(array(true,$arr_return[0]['qty']));
	}else{
		echo json_encode(array(false,"ERROR QTY."));
	}
	
}else{
	echo json_encode(array(false,$arr_return));
}
?>