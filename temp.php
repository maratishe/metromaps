<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));

echo "\n\n";  $out = fopen( 'raw.txt', 'w'); $H = array();
foreach ( flget( '.', 'places', '', 'b64jsonl') as $file) { $in = finopen( $file); while ( ! findone( $in)) {
	list( $h, $p) = finread( $in, true, true, false); if ( ! $h) continue; 
	//die( jsonraw( $h) . "\n");
	extract( $h); // place, population
	echo "$place\n"; $H[ "$place"] = $population;
}; finclose( $in);}
arsort( $H, SORT_NUMERIC);
foreach ( $H as $k => $v) fwrite( $out, "$k\n");
fclose( $out);
?>