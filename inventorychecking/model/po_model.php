<?php
include_once ('defines.php');

include_once(__ROOT__.'/database/odbc_db.php');
include_once(__ROOT__.'/config/db_config.php');

include_once(__ROOT__.'/util/log_util.php');
include_once(__ROOT__.'/util/category_util.php');
include_once(__ROOT__.'/util/date_util.php');

include_once(__ROOT__.'/model/cabang_model.php');
include_once(__ROOT__.'/model/category_model.php');


class po_model {


	public function get_data($docEntry) {		
		
		$connection = new mysql_db( db_config::get_db_server(), 
				db_config::get_db_user(),
				db_config::get_db_pass(), 
				db_config::get_db_name());	

		$BRANCH_ID = db_config::get_branch_id();
		
		$sql = "
		select *
		from purchase_order_pusat 
		where DocEntry='".$docEntry."' and WhsCode='".$BRANCH_ID."'";			

		$rows = $connection->fetch_rows($sql);	
		

		if($rows == null || count($rows) == 0) {

			$ch = curl_init();		
			$url = 'http://10.8.0.1/Dropbox/purchase_order/get_po.php?branch_id='.$BRANCH_ID.'&doc_entry='.$docEntry;	

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res=curl_exec($ch);
			curl_close($ch);	

			$rows = json_decode($res, true);	


			if(count($rows)>0) {

				foreach($rows as $row) {


					$sql = "select count(1) as cnt from purchase_order_pusat
					where DocEntry='".$row['DocEntry']."' and WhsCode='".$row['WhsCode']."'
					and ItemCode='".$row['ItemCode']."' and LineNum='".$row['LineNum']."'";


					$cnt = $connection->fetch_row($sql);
					if($cnt['cnt']>0) {

					}
					else {

						$sql = "insert into purchase_order_pusat (DocEntry, DocDate, DocDueDate, 
							CardCode, CardName, Address, ItemCode, ItemName, Quantity, LineNum, 
							WhsCode, Canceled, LineStatus, Closed, Comments)
						values (
						'".$row['DocEntry']."',
						'".$row['DocDate']."',
						'".$row['DocDueDate']."',
						'".$row['CardCode']."',
						'".$row['CardName']."',
						'".$row['Address']."',
						'".$row['ItemCode']."',
						'".$row['ItemName']."',
						'".$row['Quantity']."',
						'".$row['LineNum']."',
						'".$row['WhsCode']."',
						'".$row['Canceled']."',
						'".$row['LineStatus']."',
						'0',
						'".$row['Comments']."'
						)";

						$connection->exec($sql);

					}


					
				}
			}
			

		}
		else {


			$found = false;
			foreach($rows as $row) {
				if((int)$row['Closed'] == 1) {
					$found = true;
					break;
				}
			}

			if($found) {
				//throw new Exception("Document No: ".$docEntry." sudah pernah diterima.");
			}

		}

		$connection->close();

		return $rows;

	}
}

?>
