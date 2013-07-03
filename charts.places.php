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
$h = hlth( hv( $H), 'label', 'population'); arsort( $h, SORT_NUMERIC); jsondump( $h, 'temp.json');
$FS = 25; $BS = 4.5;
class MyChartFactory extends ChartFactory { public function make( $C, $margins) { return new ChartLP( $C->setup, $C->plot, $margins);}}
$S = new ChartSetupStyle(); $S->style = 'D'; $S->lw = 0.1; $S->draw = '#000'; $S->fill = null;
list( $C, $CS, $CST) = chartlayout( new MyChartFactory(), 'L', '1x1', 30, '0.03:0.15:0.03:0.03');
list( $C2, $x, $y, $z) = placeschartprepare( lshift( $CS), $H);
placeschartdrawnodes( $C2, $x, $y, $z, $S);
$P = pathsread( 'paths2.bz64jsonl');
$S2 = clone $S; $S2->lw = 2.5; $S2->draw = '#000';
$S3 = clone $S2; $S3->lw = 0.5;
foreach ( $P as $h) { extract( $h); pathsdrawone( $C2, $H, $path, $tag == 'one' ? $S2 : $S3); }
$C->dump( 'places.pdf');


?>