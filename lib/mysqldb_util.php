<?php
function exec_sql($conn, $sql) {
	
	try {
		//log_util::write("[SQL]=".$sql);		
		$ar = $conn->exec($sql);		
		echo $sql;	
		//log_util::write("[AFFECTED_ROWS]=".$ar);		
		
	}catch(Exception $e) {
		//log_util::write('[ERROR]'.$e->getMessage());
	}	
}
?>