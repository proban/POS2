<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/reason_util.php');
include_once(__ROOT__.'/model/cabang_model.php');


class inv_stock_details_duplicate_model {


	public function get_data($branch_id='') {


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

			
			select 
			a.datenew, a.branchcode, a.ticket, a.ticketid, a.product, a.sku, a.product_name,
			a.cnt as sales_cnt, a.units as sales_units, ifnull(b.cnt,0) as stock_cnt, ifnull(b.units,0) as stock_units
			from
			(
				select 
				b.datenew, a.branchcode, a.ticket, c.ticketid, a.product, a.sku, d.name as product_name, count(1) as cnt, sum(a.units) as units
				from ticketlines a
				inner join receipts b on a.ticket = b.id
				inner join tickets c on a.ticket = c.id
				left join products d on a.product = d.id
				where date(b.datenew)>='2016-01-01' and tickettype='0'
				group by b.datenew, a.branchcode, a.ticket, c.ticketid, d.name, a.product, a.sku
				
			) a
			left join 
			(
				select a.datenew, a.location, a.transaction_id, a.product, count(1) as cnt, sum(a.units*-1) as units
				from stockdiary a
				where date(a.datenew)>='2016-01-01' and date(a.datenew) and reason='-1'
				group by a.datenew, a.location,  a.transaction_id, a.product
				
			) b on (a.datenew = b.datenew and a.branchcode = b.location and a.product = b.product and a.ticket = b.transaction_id)
			where (a.cnt <> b.cnt or a.units <> b.units) or (b.cnt is null or b.units is null)	


			union


			select 
			b.datenew, b.location, b.transaction_id, a.ticketid, b.product, b.sku, b.product_name,
			ifnull(a.cnt,0) as sales_cnt, ifnull(a.units,0) as sales_units, b.cnt as stock_cnt, b.units as stock_units
			from
			(
				select 
				b.datenew, a.branchcode, a.ticket, c.ticketid, a.product, a.sku, d.name as product_name, count(1) as cnt, sum(a.units) as units
				from ticketlines a
				inner join receipts b on a.ticket = b.id
				inner join tickets c on a.ticket = c.id
				left join products d on a.product = d.id
				where date(b.datenew)>='2016-01-01' and tickettype='0'
				group by b.datenew, a.branchcode, a.ticket, c.ticketid, d.name, a.product, a.sku
				
			) a
			right join 
			(
				select a.datenew, a.location, a.transaction_id, a.product, a.sku, b.name as product_name, count(1) as cnt, sum(a.units*-1) as units
				from stockdiary a
				left join products b on a.product = b.id
				where date(a.datenew)>='2016-01-01' and date(a.datenew) and reason='-1'
				group by a.datenew, a.location,  a.transaction_id, a.product, a.sku, b.name
				
			) b on (a.datenew = b.datenew and a.branchcode = b.location and a.product = b.product and a.ticket = b.transaction_id)
			where (a.cnt <> b.cnt or a.units <> b.units) or (a.cnt is null or a.units is null)	
			
			";

			$rows2 = $db->fetch_array($sql);				
			$rows = array_merge($rows, $rows2);

			$db->close();

		}

		return $rows;

	}



}

?>