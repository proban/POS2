<?php
$basedir = dirname(__FILE__);
$basedir2 = dirname($basedir);
$basedir3 = dirname($basedir2);

include_once $basedir3.'/config/config.php';
include_once $basedir2.'/lib/log_util.php';
include_once $basedir2.'/lib/mysql_db.php';
include_once $basedir2.'/lib/odbc_db.php';
include_once $basedir2.'/lib/mysqldb_util.php';
include_once $basedir2.'/lib/db_log.php';

try {
	

	if( (isset($_GET['branch_id']) && $_GET['branch_id']!='') &&
		(isset($_GET['doc_entry']) && $_GET['doc_entry']!='') ) {

		$branch_id = $_GET['branch_id'];
		$doc_entry = $_GET['doc_entry'];
		
		$pos = stripos($branch_id, 'F_');

		if($pos!==FALSE) {
			$sap_branch = "HQPROBAN";
		}
		else {
			$sap_branch = $branch_id;
		}
		

		$connection = new mysql_db($DB_SERVER, 
					$DB_USER,
					$DB_PASS, 			
					$DB_NAME);


		$sql = "select a.DocEntry, a.DocDate, a.DocDueDate, a.CardCode, a.CardName, a.Address,
			a.ItemCode, a.ItemName, a.Quantity, a.LineNum, a.WhsCode, a.Canceled, 
			a.LineStatus, a.Comments
			from proban_pusat.purchase_order a			
			where a.DocEntry='".$doc_entry."'";			
			//where a.WhsCode='".$branch_id."' and a.DocEntry='".$doc_entry."'";			


		$rows = $connection->fetch_rows($sql);

		if($rows == null || count($rows) == 0) {

			//get from pusat
			$connection_sap = new odbc_db(base64_decode('MTAuMTEuMTEuMjA='), 
						base64_decode('c2E='),
						base64_decode('cHRoYSoqMzQ1'), 
						base64_decode('UFRQcm9iYW4='));
						
			

			$sql = "select a.DocEntry, a.DocDate, a.DocDueDate, a.CardCode, a.CardName, a.[Address],
			b.ItemCode, c.ItemName, b.Quantity, b.LineNum, b.WhsCode,
			a.Canceled, b.LineStatus, b.Price, b.DiscPrcnt, b.LineTotal, a.Comments
			from 
			(
				select
				DocEntry, DocNum, DocDate, DocDueDate, 
				CardCode, CardName, [Address], DocCur, 
				VatSum, DiscPrcnt, DiscSum,  DocTotal,
				Comments, Canceled
				from OPOR
				where Canceled!='Y'
			) a
			INNER JOIN 
			(
				select
				DocEntry, ItemCode, Quantity, LineNum, 
				case 
					when WhsCode='01' then 'HQPROBAN'
					else WhsCode
				end as WhsCode,
				LineStatus,				
				Price, 	DiscPrcnt,	LineTotal	
				from POR1				
			) b ON a.DocEntry = b.DocEntry
			left join
			(
				Select ItemCode, ItemName
				from OITM	
			) c ON b.ItemCode = c.ItemCode
			where a.DocEntry ='".$doc_entry."'";  
			//and b.WhsCode='".$sap_branch."'";
			
			$rows = $connection_sap->fetch_rows($sql);
			
			$connection_sap->close();


			if(count($rows)>0) {

				foreach($rows as &$row) {

					$sql = "select count(1) as cnt from proban_pusat.purchase_order
					where DocEntry='".$row['DocEntry']."' and ItemCode='".$row['ItemCode']."'
					and LineNum='".$row['LineNum']."'";
					//and WhsCode='".$branch_id."' and LineNum='".$row['LineNum']."'";

					$cnt = $connection->fetch_row($sql);

					if($cnt['cnt']>0) {

					}
					else {


						$sql = "insert into proban_pusat.purchase_order ( DocEntry, DocDate, DocDueDate, 
							CardCode, CardName, Address, ItemCode, ItemName, Quantity, Price, DiscPrcnt, LineTotal, LineNum, 
							WhsCode, Canceled, LineStatus, Comments) values 
						(
							'".$row['DocEntry']."',
							'".$row['DocDate']."',
							'".$row['DocDueDate']."',
							'".$row['CardCode']."',
							'".$row['CardName']."',
							'".$row['Address']."',
							'".$row['ItemCode']."',
							'".$row['ItemName']."',
							'".$row['Quantity']."',
							'".$row['Price']."',
							'".$row['DiscPrcnt']."',
							'".$row['LineTotal']."',
							'".$row['LineNum']."',
							'".$row['WhsCode']."',							
							'".$row['Canceled']."',
							'".$row['LineStatus']."',
							'".$row['Comments']."'
						)";	

						$connection->exec($sql);

					}



				}
			}			

		}

		$connection->close();
		
		echo json_encode($rows);

	}
	else {
		echo "invalid request";
	}


}catch(Exception $e) {	
}


exit();


?>