<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2."/lib/mysql_db.php";
include_once $basedir2."/lib/log_util.php";
include_once $basedir2."/lib/mysqldb_util.php";
include_once $basedir2.'/lib/db_log.php';

log_util::set_log_file($basedir2."/log/fix_customer_".$BRANCH_ID.".log");

/*
$db = new mysql_db($DB_SERVER, 
			$DB_USER,
			$DB_PASS, 
			$DB_NAME);



$db_log = new db_log($db);	
$db_log->insert(array(
	'location_id'=> $BRANCH_ID,
	'script_type'=> basename(__FILE__),
	'script_filedate'=> date ("Y-m-d H:i:s.", filemtime(__FILE__)),
	'action_type'=> 0
));


function allocate_customer($row, $db) {

	$sql = "select count(1) as cnt from customers_error where searchkey='".$row['searchkey']."'";
	$row_cnt = $db->fetch_row($sql);

	if($row_cnt==null || $row_cnt['cnt']<=0) {
		
		$sql = "
		insert into customers_error (id, searchkey, taxid,name, taxcategory, card, maxdebt, address,
		address2, postal,city, region, country, firstname, lastname,email, phone, phone2,
		fax, notes, visible, curdate, curdebt, email2, birth, vehicle, sex, point, postal2, 
		bike_brand, bike_type, tyre_brand, tyre_size, tyre_type, accu_brand, accu_type,
		oil_brand, brake_type, date_regis,date_exp,point_tmp )
		select id, searchkey, taxid,name, taxcategory, card, maxdebt, address,
		address2, postal,city, region, country, firstname, lastname,email, phone, phone2,
		fax, notes, visible, curdate, curdebt, email2, birth, vehicle, sex, point, postal2, 
		bike_brand, bike_type, tyre_brand, tyre_size, tyre_type, accu_brand, accu_type,
		oil_brand, brake_type, date_regis,date_exp,point_tmp 					
		from customers where id='".$row['id']."'";
		exec_sql($db, $sql);

	}


	$sql = "delete from customers where id='".$row['id']."'";
	exec_sql($db, $sql);	
	
}



//allocate invalid member_id
$sql = "select id, searchkey, taxid from customers ";
$rows = $db->fetch_rows($sql);


if(count($rows)>0) {

	foreach($rows as $row) {		

		if($row['searchkey'] == null || $row['searchkey'] == '') {			

			if($row['taxid']!=null && $row['taxid']!='') {

				if(strlen(trim($row['taxid']))==12) {

					$sql = "update customers set searchkey='".$row['taxid']."' where id='".$row['id']."'";
					exec_sql($db, $sql);						

				}
				else {

					allocate_customer($row, $db);

				}

			}
			else {
				allocate_customer($row, $db);
			}

		}
		else {


			if(strlen(trim($row['searchkey']))!=12) {

				if(strlen($row['taxid']) == 12) {

					$sql = "update customers set searchkey='".$row['taxid']."' where taxid='".$row['taxid']."'";
					exec_sql($db, $sql);	

				}
				else {
					allocate_customer($row, $db);	
				}

			}
			else {

				$sql = "update customers set taxid='".$row['searchkey']."' where searchkey='".$row['searchkey']."'";
				exec_sql($db, $sql);	

			}

		}
	}

}




//remove duplicate
$sql = "
select searchkey, cnt
from
(
	select searchkey, count(1) as cnt
	from customers 
	group by searchkey
) a
where a.cnt > '1'
";

$rows = $db->fetch_rows($sql);
if(count($rows)>0) {
	foreach($rows as $row) {

		$sql = "select * from customers where searchkey='".$row['searchkey']."' order by date_regis desc";
		$duplicate_rows = $db->fetch_rows($sql);

		if(count($duplicate_rows)>1) {

			for($i=1;$i<count($duplicate_rows);$i++) {

				$d_row = $duplicate_rows[$i];
				$sql = "delete from customers where id='".$d_row['id']."'";
				exec_sql($db, $sql);	

			}

		}


	}
}



//fix date
$sql = "update customers set curdate=now() where curdate is null or curdate='0000-00-00 00:00:00'";
exec_sql($db, $sql);	

$sql = "update customers set date_regis=now() where date_regis is null or date_regis='0000-00-00 00:00:00'";
exec_sql($db, $sql);	

$sql = "update customers set date_exp=now() where date_exp is null or date_exp='0000-00-00 00:00:00'";
exec_sql($db, $sql);	


$sql = "delete from tr_member_point where length(searchkey)!='12'";
exec_sql($db, $sql);	


$sql = "update customers set searchkey=upper(searchkey), taxid=upper(taxid)";
exec_sql($db, $sql);


$db_log->insert(array(
	'location_id'=> $BRANCH_ID,
	'script_type'=> basename(__FILE__),
	'script_filedate'=> date ("Y-m-d H:i:s.", filemtime(__FILE__)),
	'action_type'=> 1
));


$db->close();
log_util::write("DONE");
*/

exit();

?>