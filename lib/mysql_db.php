<?php
class mysql_db {	

	private $server;
	private $user;
	private $pass;
	private $db_name;
	private $connection;


	public function __construct($server, $user, $pass, $db_name='') {

		$this->server = $server;
		$this->user = $user;
		$this->pass = $pass;
		$this->db_name = $db_name;

		if ( $this->db_name == '') {
			$this->connection = mysqli_connect($this->server, 
			$this->user,
			$this->pass);	
		}
		else {
			$this->connection = mysqli_connect($this->server, 
			$this->user,
			$this->pass,
			$this->db_name);		
		}
		//log_util::write('CONNECTED TO '.$this->server.':'.$this->user.":".$this->db_name);
		
	}

	public function select_db($dbname) {

		mysqli_select_db($this->connection, $dbname);
		
	}


	public function close() {

		if($this->connection) {
			mysqli_close($this->connection);
		}

	}
	


	public function exec($sql) {

		$rows = array();

		$result = mysqli_query($this->connection, $sql);
		if(!$result) {			
			throw new Exception(mysqli_error($this->connection));
		}

		return mysqli_affected_rows($this->connection);
	}


	public function fetch_array($sql) {

		$rows = array();

		$result = mysqli_query($this->connection, $sql);

		if(!$result) {
			throw new Exception(mysqli_error($this->connection));			
		}

		if ( mysqli_num_rows($result) > 0) {
			while(($row = mysqli_fetch_array($result, MYSQLI_ASSOC))) {
				$rows[] = $row;
			}
		}

		return $rows;		
	}
	


	public function fetch_rows($sql) {

		$rows = array();

		$result = mysqli_query($this->connection, $sql);

		if(!$result) {
			throw new Exception(mysqli_error($this->connection));			
		}

		if ( mysqli_num_rows($result) > 0) {
			while(($row = mysqli_fetch_array($result, MYSQLI_ASSOC))) {
				$rows[] = $row;
			}
		}

		return $rows;		
	}
	

	public function fetch_row($sql) {

		$result = mysqli_query($this->connection, $sql);

		if(!$result) {
			throw new Exception(mysqli_error($this->connection));			
		}

		if ( mysqli_num_rows($result) > 0) {			
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			return $row;
		}

		return FALSE;		
	}
	
	public function escape($value) {
		return mysqli_real_escape_string($this->connection, $value);
	}

	
	
	
}



?>