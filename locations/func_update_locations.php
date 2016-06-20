<?php
function update_locations($conn, $rows) {
	
	if(count($rows)>0) {		

		foreach($rows as $row) {	

			$sql = "
			select count(1) as cnt from locations
			where id='".$row['id']."'
			";
			$cnt = $conn->fetch_row($sql);

			if( $cnt['cnt'] >0) {

				$sql = "update locations 
				set  				
				name='".$row['name']."',
				address='".$row['name']."'
				where id='".$row['id']."'
				";				
				exec_sql($conn, $sql);	

			}
			else {

				$sql = "insert into locations (id, name, address) 
				values (
					'".$row['id']."',
					'".$row['name']."',
					'".$row['name']."'
					)";

				exec_sql($conn, $sql);	

			}
	
		}
		
	}

}
?>