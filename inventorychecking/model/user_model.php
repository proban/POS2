<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class user_model {


	public function get_users() {

		
		$db = new mysql_db( db_config::get_db_server(), 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				db_config::get_db_name());

		$sql = "
		select id, name 
		from people
		where visible='1'
		";

		$rows = $db->fetch_rows($sql);			

		$db->close();

		return $rows;
	}

}

?>
