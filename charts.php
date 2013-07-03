<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));

echo "\n\n"; $e = echoeinit(); $in = finopen( 'raw.bz64jsonl'); $H = array();
while ( ! findone( $in)) {
	list( $h, $p) = finread( $in); if ( ! $h) continue; echoe( $e, "reading $p");
	extract( $h); // range, degree, conventional, proposed
	lpush( $H, $h);
}
finclose( $in); echo " OK\n";

$FS = 20; $BS = 4.5;
class MyChartFactory extends ChartFactory { public function make( $C, $margins) { return new ChartLP( $C->setup, $C->plot, $margins);}}
$S = new ChartSetupStyle(); $S->style = 'D'; $S->lw = 0.1; $S->draw = '#000'; $S->fill = null;
list( $C, $CS, $CST) = chartlayout( new MyChartFactory(), 'P', '1x3', 25, '0.1:0.2:0.1:0.25');

// by number of people
$C2 = lshift( $CS);
$CON = array(); $PRO = array();
foreach ( $vs as $h) {
	extract( $h); // range, degree, conventional, proposed
	$ks1 = ttl( 'nearest,bigger,government'); $h1 = hvak( $ks1);
	$ks2 = ttl( 'transfers,traveltime,nearest,government'); $h2 = hvak( $ks2);
	$sum = msum( hv( $conventional));
	$sumlog = round( 2 * log10( $sum), 1);
	htouch( $CON, "$sumlog"); $CON2 =& $CON[ "$sumlog"];
	foreach ( $conventional as $k => $v) { htouch( $CON2, $h1[ "$k"], 0, false, false); $CON2[ $k1[ "$k"]] += $v; }
	htouch( $PRO, "$sumlog"); $PRO2 =& $PRO[ "$sumlog"];
	foreach ( $proposed as $k => $v) { htouch( $PRO2, $h1[ "$k"], 0, false, false); $PRO2[ $k1[ "$k"]] += $v; }
	unset( $CON2); unset( $PRO2);
}


?>