<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');


class fix_stock_model {


	public function exec($branchcode, $datenew, $ticket, $product, $sales_units, $stock_units) {

		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($branchcode);

		$rows = array();
		$current_cab = '';

		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];					

			$db = new mysql_db( db_config::get_db_server().':'.$port, 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				$dbname);


			$sql = "
			select 
			a.ticket, a.line, a.product, a.sku, a.units, a.price, a.taxrate, a.branchcode,
			b.person, b.ticketid, b.customer
			from ticketlines a
			inner join tickets b on a.ticket = b.id
			where a.ticket='".$ticket."' and a.product='".$product."'			
			";
			$row1 = $db->fetch_row($sql);



			$sql = "				
			select
			count(1) as cnt
			from stockdiary
			where transaction_id='".$row1['ticket']."' and 
			location='".$row1['branchcode']."' and
			product='".$row1['product']."'";
			$row = $db->fetch_row($sql);



			if ($row==null && $row1!=null) {

				$sql = "
				insert into stockdiary (id, datenew, reason, location, product, units, price, appuser, no_po,
					supplier_name, transaction_id, sku) values (
					uuid(),
					'".$datenew."',
					'-1',
					'".$row1['branchcode']."',
					'".$row1['product']."',
					'".($row1['units']*-1)."',
					'".$row1['price']."',
					'".$row1['person']."',
					'".$row1['ticketid']."',
					'".$row1['customer']."',
					'".$row1['ticket']."',
					'".$row1['sku']."'
				)				
				";
				//echo $sql;				
				$db->exec($sql);

			}
			else {

				if($row1!=null) {

					$stock = $sales_units - $stock_units;

					$sql = "
					insert into stockdiary (id, datenew, reason, location, product, units, price, appuser, no_po,
						supplier_name, transaction_id, sku) values (
						uuid(),
						'".$datenew."',
						'-1',
						'".$row1['branchcode']."',
						'".$row1['product']."',
						'".($stock*-1)."',
						'".$row1['price']."',
						'".$row1['person']."',
						'".$row1['ticketid']."',
						'".$row1['customer']."',
						'".$row1['ticket']."',
						'".$row1['sku']."'
					)				
					";
					//echo $sql;		
					$db->exec($sql);

				}
				else {
					//echo "ticketlines is null";
				}

			}
			
			$db->close();

		}

		return $rows;

	}



}

?>