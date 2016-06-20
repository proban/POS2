<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);

include_once $basedir2.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."mysql_db.php";
include_once $basedir2.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."mysqldb_util.php";
include_once $basedir2.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."log_util.php";

log_util::set_log_file($basedir2.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'member_points_server.log');

include_once $basedir2.DIRECTORY_SEPARATOR."locations".DIRECTORY_SEPARATOR."location_model.php";

$cabang_model = new location_model();
$cabangs = $cabang_model->get_locations();

$db_pusat = new mysql_db( "127.0.0.1:3306", 
			"root",
			"", 
			'proban_pusat');


foreach($cabangs as $cab) {

	$dbname = $cab["database_name"];
	$cabang = $cab["name"];		
	$port = $cab["port"];
	$branch_id = $cab["id"];

	try {		

		$con = new mysql_db( "127.0.0.1:".$port, "root", "", $dbname);		

		$sql="
		select	
		branch_id,			
		searchkey,		
		(sum(point_gain) - sum(redeem_amount)) as total_point
		from tr_member_point
		where length(searchkey) ='12'
		group by searchkey, branch_id";

		$rows = $con->fetch_rows($sql);

		if(count($rows)>0) {

			foreach($rows as $row) {

				$sql = "select count(1) as cnt from member_points								
				where searchkey='".$row['searchkey']."'";

				$cnt_row = $db_pusat->fetch_row($sql);
				if($cnt_row['cnt']!=null) {
					$cnt = $cnt_row['cnt'];
				}
				else {
					$cnt = 0;	
				}


				if($cnt>0) {

					$sql = "update member_points
					set total_point='".$row['total_point']."',
					last_update=now()
					where searchkey='".$row['searchkey']."'";
					exec_sql($db_pusat, $sql);

				}
				else {

					$sql = "insert into member_points
					(branch_id, searchkey, total_point, last_update)
					values(						
						'".strtoupper($row['branch_id'])."',
						'".strtoupper($row['searchkey'])."',
						'".$row['total_point']."',
						now()
					)";	
					exec_sql($db_pusat, $sql);

				}

			}
		}	

		$con->close();


	}catch(Exception $e) {
		$con->close();
	}

}


$sql= "
update customers a
inner join member_points b on a.searchkey = b.searchkey
set point = b.total_point";
exec_sql($db_pusat, $sql);


$sql= "
update member_points 
set total_point = '0'
where total_point <'0'";
exec_sql($db_pusat, $sql);



$sql= "
update customers 
set point = '0'
where point <'0'";
exec_sql($db_pusat, $sql);


$db_pusat->close();

echo "OK";
exit();

?>