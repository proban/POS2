<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';
include_once $basedir2.'/lib/db_log.php';
include_once 'func_update_companies.php';

//log_util::set_log_file($basedir2."/log/update_pricesell_".$BRANCH_ID.".log");

try {


	$conn = new mysql_db($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);

	$db_log = new db_log($conn);	
	$db_log->insert(array(
		'location_id'=> $BRANCH_ID,
		'script_type'=> basename(__FILE__),
		'script_filedate'=> date ("Y-m-d H:i:s.", filemtime(__FILE__)),
		'action_type'=> 0
	));


	$ch = curl_init();
	$url = 'http://10.8.0.1/Dropbox/companies/get_companies.php';

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$res=curl_exec($ch);
	curl_close($ch);

	$rows = json_decode($res, true);

	update_companies($conn, $rows);	

	$db_log->insert(array(
		'location_id'=> $BRANCH_ID,
		'script_type'=> basename(__FILE__),
		'script_filedate'=> date ("Y-m-d H:i:s.", filemtime(__FILE__)),
		'action_type'=> 1
	));

	$conn->close();

	log_util::write("DONE");


}catch(Exception $e) {
	log_util::write($e->getMessage());
}



exit();


?>