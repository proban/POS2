<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';
include_once $basedir2.'/lib/db_log.php';

try {

	$conn = new mysql_db($DB_SERVER, $DB_USER, $DB_PASS,'proban_pusat');
	$sql = "select id, name from lokasi 
	order by id";
	$rows = $conn->fetch_rows($sql);
	$conn->close();

	echo json_encode($rows);

}catch(Exception $e) {
	log_util::write($e->getMessage());
}

exit();


?>