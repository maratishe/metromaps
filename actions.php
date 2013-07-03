<?php
set_time_limit( 0);
ob_implicit_flush( 1);
$prefix = ''; if ( is_dir( "ajaxkit")) $prefix = 'ajaxkit/'; for ( $i = 0; $i < 3; $i++) { if ( ! is_dir( $prefix . 'lib')) $prefix .= '../'; else break; }
if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; // hoping for another location of ajaxkit
if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
// global functions and env
require_once( $prefix . 'functions.php');
require_once( $prefix . 'env.php'); //echo "env[" . htt( $env) . "]\n";
// additional (local) functions and env (if present)
if ( is_file( "$BDIR/functions.php")) require_once( "$BDIR/functions.php");
if ( is_file( "$BDIR/env.php")) require_once( "$BDIR/functions.php");
htg( hm( $_GET, $_POST));  
if ( isset( $ss)) htg( jsonparse( base64_decode( $ss)), '', 'ss');
$uid = 'nobody'; checksession(); if ( isset( $ssuid)) $uid = $ssuid;
require_once( 'common.php');

if ( $action == 'getdata') {
	$H = placesread();
	list( $C, $CS, $S) = placeschartinit();
	list( $C2, $x, $y, $z) = placeschartprepare( lshift( $CS), $H);
	$x = mnorm( $x); $y = mnorm( $y); 
	$D = array(); $ks = hk( $H); 
	for ( $i = 0; $i < count( $ks); $i++) {
		lpush( $D, lth( array( $x[ $i], $y[ $i], $ks[ $i]), ttl( 'x,y,label')));
	}
	$JO[ 'data'] = $D;
	die( jsonsend( $JO));
}
if ( $action == 'store')  { // tag, path
	$out = foutopen( 'paths.bz64jsonl', 'a');
	$path = json2h( $path, true);
	foutwrite( $out, compact( ttl( 'tag,path')));
	foutclose( $out);
	die( jsonsend( jsonmsg( 'ok')));
}

?>