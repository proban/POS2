<?php
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/model/cabang_model.php');


class category_model {


	public function get_cats_by_transaction($cabang_id, $from_date, $to_date) {

		$cabang_model = new cabang_model();
		$cabangs = $cabang_model->get_cabangs($cabang_id);

		$rows= [];	

		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];

			$db = new mysql_db(
				db_config::get_db_server().':'.$port,
				db_config::get_db_user(),
				db_config::get_db_pass(),
				$dbname);

			$sql = "
			select 
			cat.id, 
			cat.name 
			from tickets a																						
			inner join receipts b on a.id = b.id						
			inner join ticketlines d on a.id = d.ticket
			inner join products products on d.product = products.id
			inner join categories cat on products.category= cat.id												
			where (date(b.datenew)>='".$from_date."' and date(b.datenew)<='".$to_date."')								
			group by cat.id, cat.name
			";

			$rows1 = $db->fetch_array($sql);	
			$this->merge_cats($rows, $rows1);

			$db->close();
		}		

		usort($rows, function($a, $b) {
    		return strcmp($a['name'], $b['name']);
		});

		log_util::info('categories:'.json_encode($rows));
		return $rows;
	}


	public function get_cats_by_trx_period($cabang_id, $from_date1, $to_date1, $from_date2, $to_date2) {

		$cabang_model = new cabang_model();
		$cabangs = $cabang_model->get_cabangs($cabang_id);

		$rows= [];	

		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];

			$db = new mysql_db(
				db_config::get_db_server().':'.$port,
				db_config::get_db_user(),
				db_config::get_db_pass(),
				$dbname);

			$sql = "
			select 
			cat.id, 
			cat.name 
			from tickets a																						
			inner join receipts b on a.id = b.id						
			inner join ticketlines d on a.id = d.ticket
			inner join products products on d.product = products.id
			inner join categories cat on products.category= cat.id												
			where 
			(date(b.datenew)>='".$from_date1."' and date(b.datenew)<='".$to_date1."') or								
			(date(b.datenew)>='".$from_date2."' and date(b.datenew)<='".$to_date2."') 
			group by cat.id, cat.name
			";

			$rows1 = $db->fetch_array($sql);	
			$this->merge_cats($rows, $rows1);
			$db->close();

		}		

		usort($rows, function($a, $b) {
    		return strcmp($a['name'], $b['name']);
		});

		log_util::info('categories:'.json_encode($rows));
		return $rows;
		
	}


	private function merge_cats(&$rows, $rows1) {

		foreach($rows1 as $row1) {

			$found = false;
			foreach($rows as $row) {
				if($row1['name'] == $row['name']) {
					$found = true;
					break;
				}
			}

			if(!$found) {
				$rows[] = $row1;
			}
		}

	}
	


	public function get_master_categories($cabang_id='') {
		
		$cabang_model = new cabang_model();
		$cabangs = $cabang_model->get_cabangs($cabang_id);

		$rows= [];	

		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];

			$db = new mysql_db(
				db_config::get_db_server().':'.$port,
				db_config::get_db_user(),
				db_config::get_db_pass(),
				$dbname);

			$sql = "
			select 
			category1,
			category2,
			category3
			from products
			where category1 is not null
			group by category1, category2, category3
			order by category1, category2, category3
			";

			$rows1 = $db->fetch_array($sql);	

			$this->merge_master_categories($rows, $rows1);

			$db->close();

		}		

		return $rows;

	}



	private function merge_master_categories(&$rows, $rows1) {

		foreach($rows1 as $row1) {

			$found = false;
			foreach($rows as $row) {
				if($row1['category1'] == $row['category1'] && 
					$row1['category2'] == $row['category2'] &&
					$row1['category3'] == $row['category3']
				) {
					$found = true;
					break;
				}
			}

			if(!$found) {
				$rows[] = $row1;
			}
		}

	}

}

?>