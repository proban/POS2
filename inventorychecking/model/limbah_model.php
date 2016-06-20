<?php
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/util/cabang_util.php');


class limbah_model {



	public function insert($limbah) {
		
		$db = new mysql_db(
		db_config::get_db_server(),
		db_config::get_db_user(),
		db_config::get_db_pass(),
		db_config::get_db_name());		

	    $branch_id=db_config::get_branch_id();
		
		$sql = "
		insert into limbah(input_date, locations, category, qty, unit_id, description, user) values (
			now(),
		'".$branch_id."',
		'".$limbah['category']."',
		'".$limbah['qty']."',
		'".$limbah['unit_id']."',
		'".$limbah['description']."',
		'".$limbah['user']."'
		)
		";			
		$db->exec($sql);
		
		$db->close();	
		
	}



	public function update($limbah) {
		
		$db = new mysql_db(
		db_config::get_db_server(),
		db_config::get_db_user(),
		db_config::get_db_pass(),
		db_config::get_db_name());
		
		$branch_id=db_config::get_branch_id();

		$sql = "
		update limbah
		set 
		locations='".$branch_id."',
		category='".$limbah['category']."',
		qty='".$limbah['qty']."',
		unit_id='".$limbah['unit_id']."',
		description='".$limbah['description']."',
		user='".$limbah['user']."'
		where id='".$limbah['id']."'
		";

		$db->exec($sql);		
		$db->close();		

	}

	public function delete($limbah) {
		
		$db = new mysql_db(
		db_config::get_db_server(),
		db_config::get_db_user(),
		db_config::get_db_pass(),
		db_config::get_db_name());

		$sql = "
		delete from limbah
		where id='".$limbah['id']."'		
		";

		$db->exec($sql);		
		$db->close();		
	
	}


	public function single($limbah) {
		
		$db = new mysql_db(
		db_config::get_db_server(),
		db_config::get_db_user(),
		db_config::get_db_pass(),
		db_config::get_db_name());

		$sql = "
		select id, locations, category, qty, unit_id, description, user
		from limbah
		where id='".$limbah['id']."'		
		";

		$row = $db->fetch_row($sql);			
		$db->close();
		return $row;
		
	}

	
	
	public function get_data($filter) {
		
		$db = new mysql_db(
		db_config::get_db_server(),
		db_config::get_db_user(),
		db_config::get_db_pass(),
		db_config::get_db_name());

		$conditions= array();
		if($filter['from_date']!='' && $filter['to_date']!='') {
			$conditions[]=" date(input_date)>='".$filter['from_date']."' and date(input_date)<='".$filter['to_date']."'";
		}
		
		
		if($filter['category']!='') {
			$conditions[]=" category like '%".$filter['category']."%'";
		}

				
		$sql_condition = "";
		if(count($conditions)>0) {
			$sql_condition = " WHERE ";
			foreach($conditions as $c) {
				if(strlen($c)>0) {
					$sql_condition.= $c." AND ";
				}
			}	
			$sql_condition = substr($sql_condition, 0, strlen($sql_condition)-5);
		}
		


		$sql = "
		select 
		id,input_date,locations, category, qty, unit_id, description, user
		from limbah
		".$sql_condition."
		order by input_date
		";

		$rows = $db->fetch_rows($sql);	
		$db->close();
		return $rows;
		



		
	}
	
	

}

?>