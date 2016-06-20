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



	$sql = "select max(move_date) as move_date from movements_pusat ";
	$row = $conn->fetch_row($sql);
	
	$move_date='';
	if($row == null) {
		$move_date='2015-01-01 00:00:00';
	}
	else {		
		if($row['move_date'] == null) {
			$move_date='2015-01-01 00:00:00';
		}
		else {
			$move_date=$row['move_date'];
		}		
	}

	
	$ch = curl_init();		
	$url = 'http://183.81.156.116/Dropbox/movements/get_movements_hq.php?id='.$BRANCH_ID.'&move_date='.$move_date;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$res=curl_exec($ch);
	curl_close($ch);	

	$rows = json_decode($res, true);
	
	
	if(count($rows)>0) {
	
		foreach($rows as $row) {
	
			$sql = "select count(1) as cnt from movements_pusat where move_id='".$row['move_id']."'";
			$row_cnt = $conn->fetch_row($sql);
			
			if($row_cnt!=null && $row_cnt['cnt']>0) {				
			}
			else {
				
				$sql = "insert into movements_pusat (move_id, document_no, location_id, move_date, move_type, from_warehouse, to_warehouse, 
				move_by,move_ref, insert_by, insert_date ) values (
				'".$row['move_id']."',
				'".$row['document_no']."',
				'".$row['location_id']."',
				'".$row['move_date']."',
				'".$row['move_type']."',
				'".$row['from_warehouse']."',
				'".$row['to_warehouse']."',
				'".$row['move_by']."',
				'".$row['move_ref']."',
				'".$row['insert_by']."',
				'".$row['insert_date']."'
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