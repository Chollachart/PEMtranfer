<?php
class db_class{
	public function __construct(){

	}
	public function connect_db()
	{
		$serverName = "192.168.10.4\EXACTDB";
		$userName = "admin_prod";
		$userPassword = "PreciseI$#910";
		$dbName = "001";
		$conn = new PDO("sqlsrv:server=".$serverName." ; Database = ".$dbName, $userName, $userPassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}
	public function query_data($query_string,$params){
		$conn_data = $this->connect_db();
		try{
			$stmt_query=$conn_data->prepare($query_string);
			$stmt_query->execute($params);
			$array_return = array();
			while($arr_q=$stmt_query->fetch( PDO::FETCH_ASSOC )){
				array_push($array_return, $arr_q);
			}
			return $array_return;
		}catch(PDOException $e){
			return $e->getMessage();
		}
	}
}
?>