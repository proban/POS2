<?php
$DB_SERVER = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '@pro123$';
$DB_NAME = 'proban_pusat';


$conn = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);
$sql = "select count(1) as cnt from customers";

$results = mysqli_query($conn, $sql);
$rows = [];
while(($row = mysqli_fetch_array($results, MYSQL_ASSOC))) {
	$rows[] = $row;
}
mysqli_close($conn);
echo json_encode($rows);

exit();

?>