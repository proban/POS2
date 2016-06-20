<?php
function update_companies($conn, $rows) {
	
	if(count($rows)>0) {		

		foreach($rows as $row) {	

			$sql = "
			select count(1) as cnt from companies
			where id='".$row['id']."'
			";
			$cnt = $conn->fetch_row($sql);

			if( $cnt['cnt'] >0) {

				$sql = "update companies 
				set  				
				name='".$row['name']."',				
				where id='".$row['id']."'
				";				
				exec_sql($conn, $sql);	

			}
			else {

				$sql = "insert into companies (id, name) 
				values (
					'".$row['id']."',
					'".$row['name']."'
					)";

				exec_sql($conn, $sql);	

			}
	
		}
		
	}

}
?>