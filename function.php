<?php
class db_class{
	private $array_connection = NULL;
	public function __construct($cmp_code){
		if($cmp_code=="PEM"){
			$this->array_connection = array("server"=>"192.168.10.4\EXACTDB","user"=>"admin_prod","password"=>"PreciseI$#910","dbname"=>"002");
		}
		else if($cmp_code=="PEM1"||$cmp_code=="PEM (Branch 1)"){
			$this->array_connection = array("server"=>"192.168.33.5","user"=>"sa","password"=>"PreciseI$","dbname"=>"110");
		}
		else if($cmp_code=="localhost"){
			$this->array_connection = array("server"=>"192.168.33.5","user"=>"sa","password"=>"PreciseI$","dbname"=>"reserve_db");
		}
	}
	public function connect_db()
	{
		$serverName = $this->array_connection['server'];
		$userName = $this->array_connection['user'];
		$userPassword = $this->array_connection['password'];
		$dbName = $this->array_connection['dbname'];
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