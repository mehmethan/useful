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

	function query($sql){
		try{
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			return $stmt;
		}catch(PDOException $e){
			echo "Sql Statement Failed: ". $e->getMessage();
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
			echo "Insert Statement Failed: ". $e->getMessage();
			return false;
		}
	}

	/* Example usages of select function:
		
		$result = $db->select("users", "name, age");
		$result = $db->select("users", "*", [ ["name", "=", "john"], ["age", ">", 21] ], "or", 10);
		$result = $db->select("users", ["id", "name", "age"], [ ["age", ">", 21] ]);

	*/

	//Order by should be implemented
	function select($table, $columns = "*", $conditions = [], $operator = "AND", $limit = 0){
		
		$columns = empty($columns) ? "*" : $columns;
		$columns = is_array($columns) ? implode(',', $columns) : $columns;

		$limit = is_int($limit) && $limit > 0 ? $limit : 0;

		$operator = in_array(strtolower($operator), ["or", "and"]) ? strtoupper($operator) : "AND";

		$placeholders = [];
		$condition_values = [];
		foreach ($conditions as $condition) {
			$placeholders[] = $condition[0].$condition[1].":".$condition[0];
			$condition_values[$condition[0]] = $condition[2]; 
		}

		$sql = "SELECT ". $columns ." FROM ". $table;

		if(!empty($conditions)){
			$sql .= " WHERE ". implode(' '.$operator.' ', $placeholders);
		}

		if($limit){
			$sql .= " LIMIT ".$limit;
		}

		try{
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($condition_values);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			echo "Select Statement Failed: ". $e->getMessage();
			return false;
		}

	}

}