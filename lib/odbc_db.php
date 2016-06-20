<?php
error_reporting(E_ALL);

class odbc_db {	

	private $server;
	private $user;
	private $pass;
	private $db_name;
	private $connection;


	public function __construct($server, $user, $pass, $db_name) {

		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;
		$this->db_name = $db_name;		

		try {

			$this->connection = odbc_connect("DRIVER={SQL Server};SERVER=".$this->server.",1433;DATABASE=".$this->db_name.";", $this->user, $this->pass);			
			
			/*
			if($this->connection) {
				echo "db connected";
			}*/

		}catch(Exception $e) {
			echo $this->db_name;
			throw $e;
		}

		
		
	}

	public function get_connection() {
		return $this->connection;
	}


	public function close() {

		if($this->connection) {
			odbc_close($this->connection);
			/*echo "db connection closed";*/
		}

	}
	


	public function prepare($sql) {
		$statement = odbc_prepare($this->connection, $sql);
		return $statement;
	}


	public function execute($statement, $params = array()) {

		if(count($params)>0) {
			$success = odbc_execute($statement, $params);
		}
		else {
			$success = odbc_execute($statement);	
		}

	}


	public function exec($sql) {

		$rows = array();

		$result = odbc_exec($this->connection, $sql);
		if(!$result) {
			echo $sql;
			echo odbc_error($this->connection); die;
		}

		return odbc_affected_rows($this->connection);
	}


	public function fetch_array($sql) {

		$rows = array();
		$result = odbc_exec($this->connection, $sql);

		if(!$result) {
			echo odbc_error($this->connection); die;
		}

		if ( odbc_num_rows($result) > 0) {
			while(($row = odbc_fetch_array($result))) {
				$rows[] = $row;
			}
		}

		return $rows;		
	}
	


	public function fetch_rows($sql) {

		$rows = array();
		$result = odbc_exec($this->connection, $sql);

		if(!$result) {
			echo odbc_error($this->connection); die;
		}

		if ( odbc_num_rows($result) > 0) {
			while(($row = odbc_fetch_array($result))) {
				$rows[] = $row;
			}
		}

		return $rows;		
	}
	

	public function fetch_row($sql) {

		$result = odbc_exec($this->connection, $sql);

		if(!$result) {
			echo odbc_error($this->connection); die;
		}

		if ( odbc_num_rows($result) > 0) {			
			$row = odbc_fetch_array($result);
			return $row;
		}

		return FALSE;		
	}
	
	

}
?>