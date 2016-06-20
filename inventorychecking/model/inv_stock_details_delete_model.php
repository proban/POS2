<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');


class inv_stock_details_delete_model {


	public function execute($branch_id='', $stock_id) {

		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($branch_id);					

		$row = 0;
		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];					
			$location = $cab["id"];


			$db = new mysql_db( db_config::get_db_server().':'.$port, 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				$dbname);			
			
			$sql = "
			delete from stockdiary where location='".$location."' and id='".$stock_id."'
			";

			try {
				$row = $db->exec($sql);
			}catch(Exception $e) {				
			}

			$db->close();
			
		}

		return $row;



	}



}

?>