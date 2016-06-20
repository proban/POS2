<?php
function get_request($url) {

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res =curl_exec($ch);
	curl_close($ch);	
	
	$rows = json_decode($res, true);
	return $rows;

}
?>