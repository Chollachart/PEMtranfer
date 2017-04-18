<?php
	require_once("function.php");
	
	$class_mysql = new db_class_mysql("192.168.90.250","servicedeskdb2","dbadmin_read","dbadmin_read");
	$arr_q_mail = $class_mysql->query_data("select Ex_mail from User where UserID=?",array(1098));
	print_r($arr_q_mail);

?>