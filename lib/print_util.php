<?php
define('DEBUG',TRUE);

function print_me($string) {

	if(DEBUG == TRUE) {
		echo $string;
	}
}

?>