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
class general_class{
	public function utf8($tis) {
			$utf8 = "";
			for( $i=0 ; $i< strlen($tis) ; $i++ ){
			$s = substr($tis, $i, 1);
			$val = ord($s);
			if( $val < 0x80 ){
			$utf8 .= $s;
			} elseif ((0xA1 <= $val and $val <= 0xDA)
			or (0xDF <= $val and $val <= 0xFB)) {
			$unicode = 0x0E00 + $val - 0xA0;
			$utf8 .= chr( 0xE0 | ($unicode >> 12) );
			$utf8 .= chr( 0x80 | (($unicode >> 6) & 0x3F) );
			$utf8 .= chr( 0x80 | ($unicode & 0x3F) );
			}
			}
			return trim($utf8);
	}
	public function tis620($string) {
			$str = $string;
			$res = "";
			for ($i = 0; $i < strlen($str); $i++) {
			if (ord($str[$i]) == 224) {
			$unicode = ord($str[$i+2]) & 0x3F;
			$unicode |= (ord($str[$i+1]) & 0x3F) << 6;
			$unicode |= (ord($str[$i]) & 0x0F) << 12;
			$res .= chr($unicode-0x0E00+0xA0);
			$i += 2;
			} else {
			$res .= $str[$i];
			}
			}
			return trim($res);
	}
	public function split_comma($string)
	{
		if(substr_count($string,".")>1){$string = str_replace(".","",$string);}
		$return_str = str_replace(",","",$string);
		return $return_str;
	}
	public function str_sharp_filename_replace($str)
	{
		$return_str = str_replace(" ","",str_replace("#","",$str));
		return $return_str;
	}
	public function get_datetime_today()
	{
		$arr = getdate(); 
		return $arr['year']."-".str_pad($arr['mon'],2,"0",STR_PAD_LEFT)."-".str_pad($arr['mday'],2,"0",STR_PAD_LEFT)." ".str_pad($arr['hours'],2,"0",STR_PAD_LEFT).":".str_pad($arr['minutes'],2,"0",STR_PAD_LEFT).":".str_pad($arr['seconds'],2,"0",STR_PAD_LEFT);
	}
	public function change_date_from_db_to_show_datetime($datetime){
		if($datetime!=""){
		return date("d/m/Y h:i:s",strtotime($datetime));
		}
		else{return "";}
	}
	public function change_date_to_db($date)
	{
		$arr_date = explode("/", $date);
		return $arr_date[2]."-".$arr_date[1]."-".$arr_date[0]." 00:00:00.000";
	}
	public function change_date_from_db_to_show_date($datetime){
		if($datetime!=""){
		return date("d/m/Y",strtotime($datetime));
		}
		else{return "";}
	}
}
?>