<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class movements_model {


	public function get_data($cab_id='', $from_date, $to_date, $status='') {

		$sql_status = "";
		if($status!='') {
			$sql_status = " and a.closed='".$status."'";
		}



		$cabang_model = new cabang_model();	
		$cabangs = $cabang_model->get_cabangs($cab_id);
		$connections = array();

		/*open connection*/
		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$port = $cab["port"];

			$connections[$dbname] = new mysql_db( db_config::get_db_server().':'.$port, 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				$dbname);			

		}


		$rows = array();
		foreach($cabangs as $cab) {

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];
			$branch_id = $cab["id"];

			$sql = "
			select			
			a.document_no, 
			a.location_id,
			a.move_date, 
			if(a.move_type='-4','Move-Out', a.move_type) as move_type, 
			a.from_warehouse,
			a.to_warehouse,
			a.move_by,
			if(a.closed='1','closed', 'open') as closed,

			b.datenew,
			if(b.reason='-4','Move-out',b.reason) as reason,
			b.location,
			b.sku,
			c.name,
			b.units

			from movements_pusat a
			inner join stockdiary_pusat b on a.move_id = b.transaction_id
			left join products c on b.sku = c.reference			
			where date(a.move_date)>='".$from_date."' and date(a.move_date)<='".$to_date."'
			".$sql_status."			
			";

			$rows2 = $connections[$dbname]->fetch_rows($sql);			
			$rows = array_merge($rows2, $rows);

		}


		/*close connection*/
		foreach($cabangs as $cab) {
			$dbname = $cab["database_name"];			
			$connections[$dbname]->close();	
		}

		return $rows;

	}



}

?>
