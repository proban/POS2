<?php
error_reporting(E_ALL);
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';

if( isset($_GET['id']) && $_GET['id'] != '') {
	
	$id = $_GET['id'];		
		
	$DB_SERVER = '127.0.0.1';
	$DB_USER = 'root';
	$DB_PASS = '@pro123$';

	$conn = new mysql_db($DB_SERVER, $DB_USER, $DB_PASS, 'proban_pusat');
	$sql = "select db_master_update from lokasi where id='".$id."'";
	$row = $conn->fetch_row($sql);

	
	$rows = array();	
	if($row!=null) {

		$conn->select_db($row['db_master_update']);		
		
		$sql = "select sku, disc_type, disc_val, enabled
		from member_discounts";				
		
		$rows = $conn->fetch_rows($sql);

	}

	$conn->close();
	echo json_encode($rows);


}

exit();


?>