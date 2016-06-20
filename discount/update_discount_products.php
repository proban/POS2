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


//log_util::set_log_file($basedir2."/log/update_diary_".$BRANCH_ID.".log");

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
	$url = 'http://10.8.0.1/Dropbox/discount/get_discount_products.php?id='.$BRANCH_ID;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$res=curl_exec($ch);
	curl_close($ch);

	$rows = json_decode($res, true);
	
	if(count($rows)>0) {		

		foreach($rows as $row) {
	
			$sql = "select count(1) as cnt from discount_products where discount_code='".$row['discount_code']."' and sku='".$row['sku']."'";			

			$row_cnt = $conn->fetch_row($sql);
			if($row_cnt!=null && $row_cnt['cnt']>0) {				

				$sql="
				update discount_products
				set
				self_discount='".$row['self_discount']."',
				discount_sku='".$row['discount_sku']."',
				discount_type='".$row['discount_type']."',
				discount_value='".$row['discount_value']."',
				discount_value_type='".$row['discount_value_type']."',
				enabled='".$row['enabled']."',
				allow_delete='".$row['allow_delete']."'

				where discount_code='".$row['discount_code']."'
				and sku='".$row['sku']."'
				";

				$conn->exec($sql);
				print_r($row);

			}
			else {				

				$sql = "insert into discount_products (
					discount_code, sku, self_discount, discount_sku, 
					discount_type, discount_value, discount_value_type, 
					enabled, allow_delete)

				 values (				
				'".$row['discount_code']."',
				'".$row['sku']."',
				'".$row['self_discount']."',
				'".$row['discount_sku']."',
				'".$row['discount_type']."',
				'".$row['discount_value']."',
				'".$row['discount_value_type']."',
				'".$row['enabled']."',
				'".$row['allow_delete']."'
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