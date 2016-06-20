<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');

class inv_stock_details_minusstock_model {


	private function parse_sku($sku) {

		$sql_in = "";
		$skus = explode(",", $sku);
		if(count($skus)>0) {			
			foreach($skus as $s) {
				if(strlen(trim($s))>0) {
					$sql_in .= "'".$s."',";	
				}				
			}	
			$sql_in = substr($sql_in, 0 , strlen($sql_in) -1);
		}			 
		return $sql_in;

	}


	public function get_data($branch_id='', $sku='') {

		$sql_sku = $this->parse_sku($sku);
		if(strlen(trim($sql_sku))>0) {
			$sql_sku = " and a.sku in (".$sql_sku.")";
		}

		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($branch_id);

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
			a.location,
			'".$cabang."' as location_name,
			b.reference as sku,
			a.product as product_id,
			b.name as product,
			a.units
			from
			(
				SELECT 		
				a.location,					
				a.product,
				sum(a.units) as units
				FROM stockdiary a 				
				group by a.location, a.product	

			) a 
			left join products b on a.product = b.id
			where a.units < '0'
			order by a.location, b.reference
			";
			
			
			
			$rows2 = $db->fetch_array($sql);				
			$rows = array_merge($rows, $rows2);

			$db->close();

		}

		return $rows;

	}



}

?>