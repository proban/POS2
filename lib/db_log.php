<?php
class db_log {

	private $connection;

	public function __construct($connection) {
		$this->connection = $connection;
	}

	public function insert($log) {

		$sql = "insert into logging (log_date, location_id, script_type, script_filedate, action_type) 
		values (
		now(),
		'".$log['location_id']."',
		'".$log['script_type']."',
		'".$log['script_filedate']."',
		'".$log['action_type']."')";

		exec_sql($this->connection, $sql);

	}
	
}
?>