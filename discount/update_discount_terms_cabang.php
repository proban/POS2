<?php
error_reporting(E_ALL);
set_time_limit(0);

$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';
include_once $basedir2.'/lib/db_log.php';


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
	$url = 'http://10.8.0.1/Dropbox/discount/get_discount_terms_pusat.php?id='.$BRANCH_ID;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$res=curl_exec($ch);
	curl_close($ch);

	$rows = json_decode($res, true);

	if(count($rows)>0) {
		
		foreach($rows as $row) {

			$sql = "select count(1) as cnt from discount_terms 
			where discount_code='".$row['discount_code']."' and 
			item_id='".$row['item_id']."' and
			term_item='".$row['term_item']."'";			

			$row_cnt = $conn->fetch_row($sql);
			if($row_cnt!=null && $row_cnt['cnt']>0) {				
			}
			else {						

				$sql = "insert into discount_terms (discount_code, item_id, term_item, term_item_type, term_type, term_value)
				 values 
				 (
				'".$row['discount_code']."',
				'".$row['item_id']."',
				'".$row['term_item']."',
				'".$row['term_item_type']."',
				'".$row['term_type']."',
				'".$row['term_value']."'
				)";

				$conn->exec($sql);
				print_r($row);

			}						

		}		
	
		
	}


	$db_log->insert(array(
		'location_id'=> $BRANCH_ID,
		'script_type'=> basename(__FILE__),
		'script_filedate'=> date ("Y-m-d H:i:s.", filemtime(__FILE__)),
		'action_type'=> 1
	));

	$conn->close();	
	log_util::write("DONE.....");

	
	


}catch(Exception $e) {
	log_util::write($e->getMessage());
}



exit();


?>