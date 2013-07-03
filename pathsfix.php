<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));
require_once( 'common.php');

echo "\n\n"; $e = echoeinit();
$H = placesread();
class MyChartFactory extends ChartFactory { public function make( $C, $margins) { return new ChartLP( $C->setup, $C->plot, $margins);}}
$S = new ChartSetupStyle(); $S->style = 'D'; $S->lw = 0.1; $S->draw = '#000'; $S->fill = null;
list( $C, $CS, $CST) = chartlayout( new MyChartFactory(), 'L', '1x1', 30, '0.03:0.15:0.03:0.03');
list( $C2, $x, $y, $z) = placeschartprepare( lshift( $CS), $H);
$P = pathsread(); $label2pos = hvak( hk( $H));
$refpath = hvak( $P[ 0][ 'path']);
for ( $ppos = 1; $ppos < count( $P); $ppos++) {
	echo "\n\n"; //sleep( 3);
	extract( $P[ $ppos]);	// tag, path
	echo "path[$ppos] tag[$tag] path: " . json_encode( $path) . "\n";
	$head = lfirst( $path);
	echo "   + head[$head]\n";
	if ( isset( $refpath[ $head])) { echo "head OK\n"; continue; }
	$h = array();
	foreach ( $refpath as $label => $v) $h[ $label] = pow( pow( $x[ $label2pos[ $label]] - $x[ $label2pos[ $head]], 2) + pow( $y[ $label2pos[ $label]] - $y[ $label2pos[ $head]], 2), 0.5);
	asort( $h, SORT_NUMERIC); list( $label2, $distance) = hfirst( $h);
	echo "   + change[$label2]   distance[$distance]\n";
	sleep( 3);
	$path[ 0] = $label2;
	$P[ $ppos] = compact( ttl( 'tag,path'));
}
$out = foutopen( 'paths2.bz64jsonl', 'w');
foreach ( $P as $h) foutwrite( $out, $h);
foutclose( $out);

?>