<?php
include_once ('defines.php');
include_once(__ROOT__.'/database/mysql_db.php');
include_once(__ROOT__.'/config/db_config.php');
include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');
include_once(__ROOT__.'/util/cabang_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class sales_sap_model {
	

	public function get_report($cab_id='', $from_date, $to_date, $sku='', $ticket_no='') {


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


		$rows = array();

		foreach($connections as $dbname=>$connection) {

			$cab = cabang_util::find_cabang($cabangs, $dbname);

			$dbname = $cab["database_name"];
			$cabang = $cab["name"];		
			$port = $cab["port"];
			$branch_id = $cab["id"];

			$sql = "
			select
			a.date,
			a.wh_code,
			a.wh_name,
			a.no_ticket,
			a.headstore,
			a.sap_item_code,
			a.product,
			a.category,

			round(a.price_after_dc, 2) as price_after_dc,
			a.qty,

			ifnull(a.payment_type,'') as payment_type,
			ifnull(a.notes,'') as notes,
			ifnull(a.cardname,'') as reference,
			a.member_id,
			a.customer_name

			from
			(

				select	
				b.datenew as date,
				'".$branch_id."' as wh_code,
				'".$cabang."' as wh_name,
				a.ticketid as no_ticket,
				c.name as headstore,
				e.reference as sap_item_code, 
				e.name as product,
				f.name as category, 	

				(
				   (
				   		(d.price*(1+d.taxrate)) - (ifnull(g.discount_amt,0)/d.units)
				   )
				   /
				   (1+d.taxrate)

				) as price_after_dc, 

				d.units as qty,
				
				case
					when h.payment='cash' and g.remarks is null then 'Cash'
					when h.payment='cash' and g.remarks is not null then 'Discount'
					when h.payment='cashrefund' then 'Cash Refund'
					when h.payment='magcard' then 'MagCard'
					else h.payment
				end as payment_type,

				h.notes,
				h.cardname,
				i.searchkey as member_id,
				i.name as customer_name

				from 
				tickets a 
				inner join receipts b on a.id = b.id			
				left join people c on a.person = c.id
				inner join ticketlines d on a.id = d.ticket
				left join products e on d.product= e.id
				left join categories f on e.category = f.id
				left join
				(
					select ticket_id, product, ticket_line, sum(discount_amount) as discount_amt,
					remarks
					from custom_sales_itemdiscounts a
					inner join tickets b on a.ticket_id = b.id
					group by ticket_id, product, ticket_line
				) g on (d.ticket = g.ticket_id and d.product = g.product and g.ticket_line = d.line)

				inner join payments h on h.receipt = b.id
				left join customers i on a.customer = i.id

				where date(b.datenew)>='".$from_date."' and date(b.datenew)<='".$to_date."'

				".($sku==""?"" :" and e.reference like '%".$sku."%'")."				
				".($ticket_no==""?"" :" and a.ticketid='".$ticket_no."'")."				

			) a
			order by a.date asc, a.wh_code asc
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