<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class sales_stock_model {


	public function get_data($cab_id='') {

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
			'".$branch_id."' as branch_id,
			'".$cabang."' as branch_name,			
			sku,
			product_name,
			stockcurrent,
			stockdiary,
			stock_diff,
			pricesell
			from
			(
				select
				a.reference as sku,
				a.name as product_name,
				ifnull(b.units,0) as stockcurrent,
				ifnull(c.units,0) as stockdiary,
				( ifnull(b.units,0) - ifnull(c.units,0)) as stock_diff,
				a.pricesell
				from products a
				left join
				(

					select 
					a.product as product_id, sum(a.units) as units
					from stockcurrent a						
					group by a.product

				) b on a.id = b.product_id
				left join
				(
					select 
					a.product as product_id, sum(a.units) as units
					from stockdiary a						
					group by a.product

				) c on a.id = c.product_id

			) a
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