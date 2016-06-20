<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');


class fix_duplicate_data_model {

	public function exec($branch_id, $ticket, $product) {


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
			select * from stockdiary where transaction_id='".$ticket."'
			and product='".$product."'";

			$rows = $db->fetch_rows($sql);
			if(count($rows) ==2) {

				$sql = "delete from stockdiary where id='".$rows[0]['ID']."'";
				$db->exec($sql);


				$sql = "				
				select * from stockdiary where transaction_id='".$ticket."'
				and product='".$product."'";

				$rows = $db->fetch_rows($sql);
				
			}

			$db->close();

		}

		return $rows;

	}



}

?>