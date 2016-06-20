<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);

include_once $basedir2."/locations/location_model.php";
include_once $basedir2."/lib/mysql_db.php";
include_once $basedir2."/lib/log_util.php";
include_once $basedir2."/config/dbconnection_servers.php";

//log_util::set_log_file($basedir2.'/log/integrate_customers.log');

$location_model = new location_model();
$locations = $location_model->get_locations();


$SERVER = $DBCONNECTION_SERVERS['SERVER-1'];

$db_pusat = new mysql_db(
			$SERVER['SERVER'],
			$SERVER['USER'],
			$SERVER['PASS'],
			$SERVER['DBNAME']);


$rows = array();
foreach($locations as $cab) {

	$dbname = $cab["database_name"];
	$cabang = $cab["name"];		
	$port = $cab["port"];
	$branch_id = $cab["id"];

	try {

		$con = new mysql_db("p:127.0.0.1:".$port,
			"root",
			"@pro123$", 
			$dbname);


		$sql = "select 		
		  ID,		  
		  SEARCHKEY,
		  TAXID,
		  NAME,
		  TAXCATEGORY,
		  CARD,
		  MAXDEBT,
		  ADDRESS,
		  ADDRESS2,
		  POSTAL,
		  CITY,
		  REGION,
		  COUNTRY,
		  FIRSTNAME,
		  LASTNAME,
		  EMAIL,
		  PHONE,
		  PHONE2,
		  FAX,
		  NOTES,
		  VISIBLE,
		  CURDATE,
		  CURDEBT,
		  IMAGE,
		  EMAIL2,
		  BIRTH,
		  VEHICLE,
		  SEX,
		  POINT,
		  POSTAL2,
		  BIKE_BRAND,
		  BIKE_TYPE,
		  TYRE_BRAND,
		  TYRE_SIZE,
		  TYRE_TYPE,
		  ACCU_BRAND,
		  ACCU_TYPE,
		  OIL_BRAND,
		  BRAKE_TYPE,
		  DATE_REGIS,
		  DATE_EXP,
		  POINT_TMP
		from customers";

		$rows = $con->fetch_rows($sql);			
		
		if(count($rows)>0) {

			foreach($rows as $row) {

				foreach($row as $key=>$value) {
					
					if($key == 'NAME' || $key == 'ADDRESS' ||  $key == 'ADDRESS2' || $key == 'FIRSTNAME' || $key=='LASTNAME') {

						$value = str_replace("\'", "", $value);						
						$value = str_replace("'", "", $value);
						$value = str_replace("\\", "", $value);
						$value = str_replace("\r", "", $value);
						$value = str_replace("\n", "", $value);
						$row[$key] = $value;
						
					}

					if($key!='IMAGE') {
						$row[$key] = $db_pusat->escape($value);						
					}

				}

				$sql = "select count(1) as cnt from customers
				where searchkey='".$row['SEARCHKEY']."'";
				$cnt_row = $db_pusat->fetch_row($sql);

				if($cnt_row!=null) {
					$cnt = $cnt_row['cnt'];
				}
				else {
					$cnt = 0;	
				}

				if($cnt<=0) { 

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
					  '".$branch_id."',
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

					//echo $sql;
					try {

						$db_pusat->exec($sql);	
					}catch(Exception $e) {
						echo $e->getMessage();
					}					
					//log_util::info($sql);					

				}

			}
		}
		
		$con->close();	


	}catch(Exception $e) {

		$con->close();	
		$db_pusat->close();

		$msg = array('status'=>FALSE, 'message'=> $e->getMessage());
		echo json_encode($msg);

	}

}


$db_pusat->close();
echo "OK";
exit();

?>