<?php
class Database{
	public $conn = array();
	private $db;
	function __construct($table=''){
		//include('config/db.php');
		global $db;
		$this->db=$db;

		foreach($db as $k=>$d)
		{
			try{
				$connstr='mysql:dbname='.$d['database'].';host='.$d['host'];
				if(isset($d['port'])){
					$connstr.=';port='.$d['port'];
				}

				$this->conn[$k] = new PDO($connstr, $d['user'], $d['password']);
				$this->conn[$k]->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch (PDOException $e) {
				echo "Error!!!!!!!! <br>";
				print_r($e);
				exit;

			}
		}


	}
	public function get_connection($num=0){
		return $this->conn[$num];
	}

}
