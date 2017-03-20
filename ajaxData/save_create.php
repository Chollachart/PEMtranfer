<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$q_save = "insert into ";
$params_save = array();
$local_db->query_data($q_save,$params_save);

?>