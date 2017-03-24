<?php
session_start();
include("../function.php");
$local_db = new db_class("localhost");
$class_general = new general_class();
$trans_id = $_GET['trans_id'];

$file_n = $trans_id."-".$class_general->str_sharp_filename_replace($class_general->tis620($_FILES['file_attach']['name']));
$file_p = 'file_upload/'.$file_n;
move_uploaded_file($_FILES["file_attach"]["tmp_name"],"../".$file_p);
$q_x = "insert into file_upload ([file_upload_name]
      ,[file_upload_path]
      ,[file_upload_trans_id]
      ,[file_upload_modify_date]) OUTPUT INSERTED.atid as id values (?,?,?,?)";
$param_x = array($class_general->str_sharp_filename_replace($class_general->tis620($_FILES['file_attach']['name'])),$file_p,$trans_id,$class_general->get_datetime_today());
$stmt_x = $local_db->query_data($q_x,$param_x);

?>