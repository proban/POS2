<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');
include_once(__ROOT__.'/util/cabang_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');



class inv_stock_current_model {


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



	public function get_report($cab_id='') {
	
		

		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($cab_id);
		$connections = array();

		/*open connection*/
		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$port = $cab["port"];
	
			try {

				$db = new mysql_db( db_config::get_db_server().':'.$port, 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				$dbname);			

				$connections[$dbname] = $db;

			} catch (Exception $e) {								
			}			

		}


		$rows_sku = $this->get_sku($cabangs, $connections);		

		$rows = array();		
		foreach($rows_sku as $sku) {

			$row = array();
			$row['sku'] = $sku['sku'];	
			$row['product'] = $sku['product'];
			$row['category'] = $sku['category'];
			$row['cabang'] = array();

			foreach($connections as $dbname=>$connection) {

				$cab = cabang_util::find_cabang($cabangs, $dbname);

				$dbname = $cab["database_name"];
				$cabang = $cab["name"];		
				$port = $cab["port"];

				$sql = "
				select				
				a.sku,
				a.qty,
				a.amount
				from
				(
					select						
					b.reference as sku,				
					sum(a.units) as qty,
					sum(a.units*ifnull(b.pricesell,0)) as amount
					from stockdiary a 
					left join products b on a.product = b.id					
					where b.reference='".$connection->escape($sku['sku'])."'			
					group by b.reference

				) a";
				
				
				$data_cabang = $connection->fetch_rows($sql);
				if($data_cabang!=null || count($data_cabang) != 0) {

					foreach($data_cabang as &$dc) {

						$sql ="select a.datenew 
						from stockdiary a
						inner join products b on a.product = b.id
						where b.reference = '".$dc['sku']."'
						order by a.datenew desc limit 1";

						$lu_data = $connection->fetch_row($sql);
						if($lu_data!=null) {
							$dc['last_update'] = $lu_data['datenew'];							
						}
						else {
							$dc['last_update'] = '';								
						}												
					}

					$row['cabang'][$cabang] = $data_cabang;	
				}
				else {
					$row['cabang'][$cabang] = array(array('qty'=>0, 'amount'=>0, 'last_update'=>''));											
				}

			}

			$rows[] = $row;

		}




		/*close connection*/
		foreach($connections as $dbname=>$connection) {			
			$connection->close();	
		}

		return $rows;

	}



}

?>