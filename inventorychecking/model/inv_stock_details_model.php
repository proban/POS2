<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');

class inv_stock_details_model {


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


	public function get_data($branch_id='', $reason_id='', $from_date, $to_date, $sku='') {

		$sql_sku = $this->parse_sku($sku);
		if(strlen(trim($sql_sku))>0) {
			$sql_sku = " and b.reference in (".$sql_sku.")";
		}


		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($branch_id);

		$sql_reason = reason_util::get_sql_reason('a.reason', $reason_id);		

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
			SELECT 
			a.datenew as stock_date,
			'".$cabang."' as branch,			
			b.reference as sku,
			b.name as product,
			a.units as qty,
			case
				when a.reason='1' then 'purchase-in'
				when a.reason='2' then 'refund-in'
				when a.reason='3' then 'adjustment-in'
				when a.reason='4' then 'movement-in'
				when a.reason='-1' then 'sales-out'
				when a.reason='-2' then 'refund-out'
				when a.reason='-3' then 'break-out'
				when a.reason='-4' then 'movement-out'
				when a.reason='-5' then 'adjustment-out'
				when a.reason='1000' then 'crossing-out'	
				else 'unknown'
			end as reason,
			no_po,
			supplier_name,
			appuser
			FROM stockdiary a 
			left join products b on a.product = b.id
			WHERE (date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."')
			".$sql_sku."
			".$sql_reason."

			order by date(a.datenew) asc
			";


			$rows2 = $db->fetch_array($sql);				
			$rows = array_merge($rows, $rows2);

			$db->close();

		}

		return $rows;

	}



}

?>