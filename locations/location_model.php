<?php
include_once('../lib/mysql_db.php');
include_once('../lib/log_util.php');
include_once('../../config/dbconnection_servers.php');


class location_model {
	

	public function get_locations() {
		
		global $DBCONNECTION_SERVERS;

		try {

			$SERVER = $DBCONNECTION_SERVERS['SERVER-1'];

			$db = new mysql_db($SERVER['SERVER'], $SERVER['USER'], $SERVER['PASS'], $SERVER['DBNAME']);			
			$sql = "SELECT database_name, name, port, id, ip_vpn
			FROM lokasi 
			ORDER BY name ASC";

			$locations = $db->fetch_array($sql);					
			$db->close();

		}catch(Exception $e){
			echo $e->getMessage();
			$db->close();
		}

		return $locations;

	}



}

?>