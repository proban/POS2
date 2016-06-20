<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');
include_once(__ROOT__.'/util/cabang_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class check_sales_discounts_model {


	private function merge_sku($curr, $new) {

		foreach($new as $new_a) {

			$found = false;
			foreach($curr as $curr_a) {
				if($curr_a['sku'] == $new_a['sku']) {
					$found = true;
					break;
				}
			}

			if(!$found) {
				$curr[] = $new_a;
			}

		}

		return $curr;

	}	


	private function get_sku($cabangs, $connections) {

		
		$rows_sku = array();
		foreach($connections as $dbname=>$connection) {

			$cab = cabang_util::find_cabang($cabangs, $dbname);

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];	

			$sql = "
			select a.reference as sku,			
			a.name as product,
			b.name as category
			from products a
			left join categories b on a.category = b.id
			group by a.reference, a.name, b.name
			order by a.reference, a.name, b.name
			";	

			$rows2 = $connection->fetch_rows($sql);
			$rows_sku = $this->merge_sku($rows_sku, $rows2);

		}

		return $rows_sku;		

	}



	public function get_data($from_date, $to_date, $cab_id='') {
			
		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($cab_id);		
		$connections = array();

		/*open connection*/
		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$port = $cab["port"];
			
			try {

				$port = 3306;

				$db = new mysql_db( db_config::get_db_server().':'.$port, 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				$dbname);			

				$connections[$dbname] = $db;

			} catch (Exception $e) {								
			}			

		}


		$rows = array();
		
		foreach($connections as $dbname=>$connection) {

			$cab = cabang_util::find_cabang($cabangs, $dbname);

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];

			$sql = "
			SELECT a.ticket, c.id, c.reference, c.name, a.units, a.price, 
			d.ticket_id, d.product, d.discount_amt 
			FROM ticketlines a 
			inner join receipts b on a.ticket = b.id 
			inner join tickets e on b.id = e.id 
			left join products c on a.product = c.id 
			left join ( 
				select ticket_id, product, ticket_line, sum(discount_amount) as discount_amt 
				from custom_sales_itemdiscounts group by ticket_id, product, ticket_line 
			) d on (a.ticket = d.ticket_id and a.product = d.product and d.ticket_line = a.line )
			where date(b.datenew)>='".$from_date."' and date(b.datenew)<='".$to_date."'
			";
			
			
			$datas = $connection->fetch_rows($sql);
			foreach($datas as $data) {	

				if($data['reference'] == '411ACC4100001' || $data['reference']=='411ACC5100001') {
				
					if($data['discount_amt'] == null && (float)$data['price'] == (float)10 ) {							
											
						$sql = "select b.reference, a.line from ticketlines a
						left join products b on a.product = b.id
						where a.ticket='".$data['ticket']."' and b.reference='".$data['reference']."'
						order by a.line asc
						";				
						
						$updated_discounts = array();

						$lines = $connection->fetch_rows($sql);							
						for($i=0;$i<count($lines);$i++) {
							
							$line = $lines[$i];				
							
							if($line['reference'] == '411ACC4100001' || $line['reference']=='411ACC5100001') {
								
								$sql ="select a.*
								from custom_sales_itemdiscounts a
								inner join products b on a.product = b.id
								where ticket_id='".$data['ticket']."' and b.reference='".$line['reference']."'";
								
								$discounts = $connection->fetch_rows($sql);
								foreach($discounts as $discount) {
									
									
									if($line['line'] != $discount['ticket_line'] && !in_array($discount['id'], $updated_discounts) ) {
										
										$sql = "update custom_sales_itemdiscounts set ticket_line='".$line['line']."'
										where id='".$discount['id']."'";

										$updated_discounts[] = $discount['id'];

										//$connection->exec($sql);

										$discount['line'] = $line['line'];
										
										$rows[] = $discount;
										$i++;

									}

									

									
									
								}									
								
								
							}							
							
						}
						
					}	
					
				}
			
				
			}
			

		}



		/*close connection*/
		foreach($connections as $dbname=>$connection) {			
			$connection->close();	
		}
		

		return $rows;
		
	}



}

?>