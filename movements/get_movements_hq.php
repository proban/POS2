<?php
error_reporting(E_ALL);
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';

if( 
	isset($_GET['id']) && $_GET['id'] != ''    
	&& isset($_GET['move_date']) && $_GET['move_date'] != ''    
) {	

	$branch_id = $_GET['id'];
	$move_date = $_GET['move_date'];
		
	$DB_SERVER = '127.0.0.1';
	$DB_USER = 'root';
	$DB_PASS = '@pro123$';

	$db_pusat = new mysql_db($DB_SERVER, $DB_USER, $DB_PASS, 'proban_pusat');
	$sql = "select database_name, port from lokasi where id='hqproban'";
	$row = $db_pusat->fetch_row($sql);
	$db_pusat->close();

	if($row!=null) {

		$conn = new mysql_db($DB_SERVER.":".$row['port'], $DB_USER, $DB_PASS, $row['database_name']);
		/*
		$sql = "select  move_id, document_no, location_id, 
				move_date, move_type, from_warehouse, to_warehouse, 
				move_by,move_ref, insert_by, insert_date  
				from movements where move_date>'".$move_date."' AND move_type='-4' AND to_warehouse='".$branch_id."'";
		*/		
		$sql = "select  move_id, document_no, location_id, 
				move_date, move_type, from_warehouse, to_warehouse, 
				move_by,move_ref, insert_by, insert_date  
				from movements where move_type='-4' AND to_warehouse='".$branch_id."'";

		$rows = $conn->fetch_rows($sql);
		$conn->close();
		
		echo json_encode($rows);
		
	}


}

exit();


?>