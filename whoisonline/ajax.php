<?php

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php');

$filename = DOKU_PLUGIN . 'whoisonline/online.txt';
if( file_exists( $filename ) == true ) { // read in table
	$online_users = json_decode(file_get_contents( $filename ), true);
	if( count( $online_users ) == 0 ) {
		$result .= "<div class='WIO_displayline'>No users online</div>";
	} else {
		asort( $online_users );
		foreach( $online_users as $user ) { $result .= "<div class='WIO_displayline'>".$user['display']."</div>"; }
	}	
} 
echo $result;
?>