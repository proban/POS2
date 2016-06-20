<?php
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once('../../../config/config.php');

class cabang_model {

	public function get_cabangs($cabang_ids='') {
		
		global $BRANCH_ID, $DB_SERVER, $DB_PORT, $DB_USER, $DB_PASS, $DB_NAME;
		/*
		$BRANCH_ID = 'BKS007';
		$DB_SERVER = '127.0.0.1';
		$DB_PORT = "3306";
		$DB_PORT2 = "3307";
		$DB_USER = 'root';
		$DB_PASS = '@pro123$';
		$DB_NAME = 'bks007proban';
		*/
		$cabangs = array(
			array(
				'database_name'=>$DB_NAME,
				'name'=>$BRANCH_ID,
				'port'=>$DB_PORT,
				'id'=>$BRANCH_ID
			)
		);
		//print_r($cabangs);		
		return $cabangs;
		
	}
	

}

?>