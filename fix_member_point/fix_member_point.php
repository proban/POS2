<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);


include_once $basedir3.'/config/config.php';
include_once $basedir2."/lib/mysql_db.php";
include_once $basedir2."/lib/log_util.php";
include_once $basedir2."/lib/mysqldb_util.php";

$DB_SERVER = "127.0.0.1";
$DB_USER = "root";
$DB_PASS = "";

$db = new mysql_db($DB_SERVER, 
			$DB_USER,
			$DB_PASS, 
			$DB_NAME);
/*
try {
	$sql = "
	CREATE TABLE IF NOT EXISTS `tr_member_point_2016` (
	  `id` bigint(20) NOT NULL,
	  `tr_date` datetime NOT NULL,
	  `branch_id` varchar(10) NOT NULL,
	  `ticket` varchar(100) NOT NULL,
	  `product` varchar(100) NOT NULL,
	  `customer` varchar(100) NOT NULL,
	  `searchkey` varchar(100) NOT NULL,
	  `point_gain` int(11) NOT NULL,
	  `point_redeem` int(11) NOT NULL,
	  `redeem_amount` decimal(18,2) NOT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	$db->exec($sql);

	$sql= "
	ALTER TABLE `tr_member_point_2016`
 	ADD PRIMARY KEY (`id`); ";
 	$db->exec($sql);

 	$sql = "
 	ALTER TABLE `tr_member_point_2016`
	MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;";
	$db->exec($sql);


}catch(Exception $e) {
}
*/

try {
	$sql = "truncate table tr_member_point;";
	$db->exec($sql);
}catch(Exception $e) {
}


$sql = "
SELECT c.searchkey, sum(a.units*a.price) as amt
FROM ticketlines a
inner join tickets b on a.ticket = b.id
left join customers c on b.customer = c.id
group by c.searchkey ";
$rows = $db->fetch_rows($sql);


if(count($rows)>0) {

	foreach($rows as $row) {

		$sql = "select fax as branch_id
		from customers 
		where searchkey='".$row['searchkey']."'";
		$customer_row =  $db->fetch_row($sql);

		$branch_id = '';
		if($customer_row!=null) {
			$branch_id = $customer_row['branch_id'];
		}		


		$sql = "select sum(point_redeem) as point_redeem,
		sum(redeem_amount) as redeem_amount
		from tr_member_point_2016 where searchkey='".$row['searchkey']."'";
		$point = $db->fetch_row($sql);


		$redeem_point = 0;
		$redeem_amount = 0;
		if($point!=null) {
			$redeem_point = (int)$point['point_redeem'];
			$redeem_amount =  $point['redeem_amount'];
		}
		$point_gain = $row['amt'] * (1/100);


		$sql = "INSERT INTO tr_member_point (tr_date, branch_id, ticket, product, customer, searchkey, 
			point_gain, point_redeem, redeem_amount)	
			values (
				now(),
				'".$branch_id."',
				NULL,
				NULL,
				'".$row['searchkey']."',
				'".$point_gain."',
				'".$redeem_point."',
				'".$redeem_amount."'
			)";		
		$db->exec($sql);		


	}
}

$db->close();
log_util::write("DONE");

exit();

?>