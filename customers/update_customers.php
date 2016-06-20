<?php
set_time_limit(0);

$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';
include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/curl_util.php';


log_util::set_log_file($basedir2.'/log/update_customer_'.$BRANCH_ID.".log");

$rows_count = get_request("http://10.8.0.1/Dropbox/customers/count_customers_pusat.php");
if(count($rows_count)>0) {

	$customers_count = $rows_count[0]['cnt'];

	$curr_count = 0;
	$curr_page = 0;

	$conn = new mysql_db($DB_SERVER, 
			$DB_USER,
			$DB_PASS, 
			$DB_NAME);

	while($curr_count < $customers_count) {

		$curr_count = ($curr_page+1)*1000;
		$rows = get_request("http://10.8.0.1/Dropbox/customers/get_customers_pusat.php?from=".$curr_count."&limit=1000");
		
		if(count($rows)>0) {			

			foreach($rows as $row) {

				$sql= "select count(1) as cnt from customers 
				where searchkey='".$row['SEARCHKEY']."'";

				$cnt = $conn->fetch_row($sql);
				if($cnt!=null && $cnt['cnt']>0 ){

					$sql = "select point from customers where searchkey='".$row['SEARCHKEY']."'";
					$customer_row = $conn->fetch_row($sql);

					if($customer_row['point'] != $row['POINT']) {

						$sql = "update customers 
						set POINT='".$row['POINT']."'
						where SEARCHKEY='".$row['SEARCHKEY']."'";
						exec_sql($conn, $sql);	

					}

					
				}
				else {

					$sql = "insert into customers
					(
					  ID, SEARCHKEY, TAXID, NAME, TAXCATEGORY, CARD, MAXDEBT, ADDRESS,
					  ADDRESS2, POSTAL, CITY, REGION, COUNTRY, FIRSTNAME, LASTNAME, EMAIL, PHONE,
					  PHONE2, FAX, NOTES, VISIBLE, CURDATE, CURDEBT, EMAIL2, BIRTH,
					  VEHICLE, SEX, POINT, POSTAL2, BIKE_BRAND, BIKE_TYPE, TYRE_BRAND, TYRE_SIZE,
					  TYRE_TYPE, ACCU_BRAND, ACCU_TYPE, OIL_BRAND, BRAKE_TYPE, DATE_REGIS,
					  DATE_EXP, POINT_TMP
					)
					values(		
					  '".$row['ID']."',					  
					  '".$row['SEARCHKEY']."',
					  '".$row['TAXID']."',
					  '".$row['NAME']."',
					  '".$row['TAXCATEGORY']."',
					  '".$row['CARD']."',
					  '".$row['MAXDEBT']."',
					  '".$row['ADDRESS']."',
					  '".$row['ADDRESS2']."',
					  '".$row['POSTAL']."',
					  '".$row['CITY']."',
					  '".$row['REGION']."',
					  '".$row['COUNTRY']."',
					  '".$row['FIRSTNAME']."',
					  '".$row['LASTNAME']."',
					  '".$row['EMAIL']."',
					  '".$row['PHONE']."',
					  '".$row['PHONE2']."',
					  '".$row['FAX']."',
					  '".$row['NOTES']."',
					  '".$row['VISIBLE']."',
					  '".$row['CURDATE']."',
					  '".$row['CURDEBT']."',				  
					  '".$row['EMAIL2']."',
					  '".$row['BIRTH']."',
					  '".$row['VEHICLE']."',
					  '".$row['SEX']."',
					  '".$row['POINT']."',
					  '".$row['POSTAL2']."',
					  '".$row['BIKE_BRAND']."',
					  '".$row['BIKE_TYPE']."',
					  '".$row['TYRE_BRAND']."',
					  '".$row['TYRE_SIZE']."',
					  '".$row['TYRE_TYPE']."',
					  '".$row['ACCU_BRAND']."',
					  '".$row['ACCU_TYPE']."',
					  '".$row['OIL_BRAND']."',
					  '".$row['BRAKE_TYPE']."',
					  '".$row['DATE_REGIS']."',
					  '".$row['DATE_EXP']."',
					  '".$row['POINT_TMP']."'
					)";
					
					exec_sql($conn, $sql);

				}

			}

		}

	}

	$conn->close();	

}


echo "OK";
exit();

?>