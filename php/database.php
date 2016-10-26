<?php

class Database{

	private $server = "localhost";
	private $db_name = "testdb";
	private $username = "";
	private $password = "";
	private $conn;

	function __construct(){
		try{
			$this->conn = new PDO("mysql:host=$this->server;dbname=$this->db_name;charset=utf8", $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			die("Connection Failed: " . $e->getMessage());
		}
	}

	function create($tableName = "", $columns = ""){
		if( empty($tableName) || empty($columns) ){
			echo "Table Name or Columns Can Not Be Empty";
			return false;
		}

		try{
			$sql = "CREATE TABLE IF NOT EXISTS ". $tableName. "(". $columns. ")";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			echo "Table Creation Failed: ". $e->getMessage();
			return false;
		}
	}

	function alter($tableName = "", $statement = ""){
		if( empty($tableName) || empty($statement) ){
			echo "Table Name or Statement Can Not Be Empty";
			return false;
		}

		try{
			$sql = "ALTER TABLE ". $tableName. " ". $statement;
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			echo "Table Alter Failed: ". $e->getMessage();
			return false;
		}
	}

	function drop($tableName = ""){
		if( empty($tableName) ){
			echo "Table Name Can Not Be Empty";
			return false;
		}

		try{
			if( is_array($tableName) ){
				
				foreach ($tableName as $table) {
					$sql = "DROP TABLE ".$table;
					$stmt = $this->conn->prepare($sql);
					$stmt->execute();
				}
				
			}else{
				$sql = "DROP TABLE ".$tableName;
				$stmt = $this->conn->prepare($sql);
				$stmt->execute();
			}

			return true;

		}catch(PDOException $e){
			echo "Table Drop Failed: ". $e->getMessage();
			return false;
		}
	}	


	function insert($table, $valuesArray){
		$placeholders = [];
		foreach ($valuesArray as $key => $value) {
			$placeholders[] = ":".$key;
		}
		
		try{
			$sql = "INSERT INTO ".$table."(" .implode(',',array_keys($valuesArray)). ") VALUES(" .implode(',',$placeholders). ")";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($valuesArray);

			return $this->conn->lastInsertId();
		}catch(PDOException $e){
			die("Insert Statement Failed: ". $e->getMessage());
		}
	}

}