<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');
include_once(__ROOT__.'/util/cabang_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');



class inv_ledger_by_sku_model {


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


	private function get_sku($cabangs, $connections, $sku='') {

		
		$rows_sku = array();
		foreach($connections as $dbname=>$connection) {

			$cab = cabang_util::find_cabang($cabangs, $dbname);

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];	

			$sql = "
			select distinct a.reference as sku,			
			a.name as product,
			b.name as category
			from products a
			left join categories b on a.category = b.id
			".($sku!=''? " where a.reference LIKE '%".$sku."%' ": "")."
			group by a.reference, a.name
			order by a.reference, a.name
			";	

			$rows2 = $connection->fetch_rows($sql);
			$rows_sku = $this->merge_sku($rows_sku, $rows2);

		}

		return $rows_sku;		

	}




	public function get_data($cab_id='', $from_date, $to_date, $sku='') {
			

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


		$rows_sku = $this->get_sku($cabangs, $connections, $sku);		

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
				$branch_id = $cab["id"];

				
				$row_cabang = array();

				//begin
				$sql = "
				select				
				b.reference,	
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where b.reference='".$row['sku']."'
				and date(a.datenew)>='2015-01-01' and date(a.datenew)<'".$from_date."'				
				group by b.reference
				";		
				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}
				$row_cabang['begin'] = (int)$data['units'];


				//purchase
				$sql = "
				select		
				b.reference,				
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='1' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";		
				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['purchase'] = (int)$data['units'];


				//move-in
				$sql = "
				select
				b.reference,					
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='4' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";				

				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['move-in'] = (int)$data['units'];



				//move-out
				$sql = "
				select		
				b.reference,			
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='-4' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";
				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['move-out'] = (int)$data['units'];



				//sales-out
				$sql = "
				select		
				b.reference,			
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='-1' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";

				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['sales'] = (int)$data['units'];



				//sales-return
				$sql = "
				select		
				b.reference,			
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='2' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";			

				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['sales-return'] = (int)$data['units'];




				//adjustment-in
				$sql = "
				select		
				b.reference,			
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				inner join products b on a.product = b.id
				where reason='3' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";
				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['adjustment-in'] = (int)$data['units'];



				//adjustment-out
				$sql = "
				select		
				b.reference,			
				sum(ifnull(a.units,0)) as units		
				from stockdiary a
				left join products b on a.product = b.id
				where reason='-5' and b.reference='".$row['sku']."'
				and date(a.datenew)>='".$from_date."' and date(a.datenew)<='".$to_date."'				
				group by b.reference
				";

				$data = $connection->fetch_row($sql);
				if($data==null) {
					$data['units'] = 0;
				}
				if($data['units']==null) {
					$data['units'] = 0;
				}

				$row_cabang['adjustment-out'] = (int)$data['units'];


				$row_cabang['total-stock'] = $row_cabang['purchase'] + $row_cabang['move-in']
				+ $row_cabang['move-out'] + $row_cabang['sales'] + $row_cabang['sales-return'] + $row_cabang['adjustment-in'] + $row_cabang['adjustment-out'];

				$row_cabang['end'] = $row_cabang['begin'] + $row_cabang['total-stock'];

				$row['cabang'][$cabang] = $row_cabang;


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