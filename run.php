<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));

`rm -Rf tempdf*`; $before = tsystem();
echo "\n\n"; $e = echoeinit(); $id = 0; $out = foutopen( 'raw.bz64jsonl', 'w'); $size = 0; $count = 0;
while ( 1) { 
	echoe( $e, tshinterval( tsystem(), $before) . " $count > $size");
	`rm -Rf data.json`;
	procpipe( "php simulation.php $count");
	$h = @jsonload( 'data.json'); if ( ! $h) continue;
	$size = foutwrite( $out, $h); $count++;
}


?>