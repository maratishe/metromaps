<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
htg( clget( 'run'));
require_once( "common.php");


echo "\n\n"; $e = echoeinit();
$H = placesread();


$h = hlth( hv( $H), 'label', 'population'); arsort( $h, SORT_NUMERIC); jsondump( $h, 'temp.json');
$FS = 18; $BS = 4.5;
class MyChartFactory extends ChartFactory { public function make( $C, $margins) { return new ChartLP( $C->setup, $C->plot, $margins);}}
$S = new ChartSetupStyle(); $S->style = 'D'; $S->lw = 0.1; $S->draw = '#000'; $S->fill = null;
list( $C, $CS, $CST) = chartlayout( new MyChartFactory(), 'P', '1x2', 5, '0.03:0.03:0.03:0.03');
list( $C2, $x, $y, $z) = placeschartprepare( lshift( $CS), $H);
placeschartdrawnodes( $C2, $x, $y, $z, $S);
$P = pathsread( 'paths2.bz64jsonl'); $backbone = $P[ 0][ 'path'];
$S2 = clone $S; $S2->lw = 2.5; $S2->draw = '#000';
$S3 = clone $S2; $S3->lw = 0.5;
foreach ( $P as $h) { extract( $h); pathsdrawone( $C2, $H, $path, $tag == 'one' ? $S2 : $S3); }
// stations
$station2pos = array(); $station2type = array(); $station2traveltime = array(); $label2pos = hvak( hk( $H));
foreach ( $P as $h) { 
	extract( $h);	// tag, path
	//die( " path:" . json_encode( $path) . "\n");
	foreach ( hvak( $path) as $label => $pos) {
		//die( " label[$label]\n");
		htouch( $station2pos, $label, $pos, true, false);
		htouch( $station2type, $label, $tag, false, false);
	}
	$station2traveltime[ $path[ 0]] = 0; $d = 0;
	for ( $i = 1; $i < count( $path); $i++) {
		$d2 = pow( pow( $x[ $label2pos[ $path[ $i - 1]]] - $x[ $label2pos[ $path[ $i]]], 2) + pow( $y[ $label2pos[ $path[ $i - 1]]] - $y[ $label2pos[ $path[ $i]]], 2), 0.5);
		$d += $d2;
		$station2traveltime[ $path[ $i]] = $d;
	}
	
}


//die( " station2pos: " . json_encode( $station2pos) . "\n");


// epicenter
$E = lr( hv( $H)); $D = array();
echo "disaster: " . $E[ 'label'] . "\n";
$range = 0.1 * mt_rand( 1, 15);
$degree = 0.01 + round( 0.05 * mt_rand( 0, 7), 2);
$xdiff = $degree * ( mmax( $x) - mmin( $x));
$ydiff = $degree * ( mmax( $y) - mmin( $y));
list( $D, $distance) = disastermake( $E, $xdiff, $ydiff); $DH = hvak( hltl( $D, 'label'), true, true);
disasterdraw( $D);


if ( count( $D) > 150) die( " Too big a disaster!\n");


// metromap
$C2 = lshift( $CS);
$MAP = array(); $count = 0;
foreach ( $P as $h) {
	extract( $h); // tag, path
	$area = 'none'; $linefull = "line$count"; $lineshort = $linefull;
	foreach ( $path as $v) {
		$stationfull = lshift( ttl( $v)); $stationshort = $stationfull;
		lpush( $MAP, compact( ttl( 'area,lineshort,linefull,stationshort,stationfull')));
	}
	$count++;
}
jsondump( $MAP, 'temp.json');
metromapdf( $C2, 'temp.json', false);


$C->dump( sprintf( 'tempdf.%05d.pdf', $run));




// simulate
$e = echoeinit();
$conventional = array();	// { authority: number of people}
$proposed = array(); // { station: number of people}
$bysize = array(); $label2pos = hvak( hk( $H)); // for each disaster city
$count = 0;
foreach ( $D as $h) {
	//die( " x:" . json_encode( $x) . "\n");
	$label = $h[ 'label']; htouch( $bysize, $label); 	// label
	echoe( $e, "($count/" . count( $D) . ") prepare1($label)");
	foreach ( $H as $L => $h2) {
		if ( $label == $h2[ 'label']) continue;
		$d = pow( pow( $h[ 'x'] - $x[ $label2pos[ $L]], 2) + pow( $h[ 'y'] - $y[ $label2pos[ $L]], 2), 0.5);
		if ( $d > $range * ( mmax( $x) - mmin( $x))) continue;	// out of range
		$bysize[ $label][ $L] = $h2[ 'population'];
	}
	//die( "  label[$label]\n");
	$limit = 5; //round( 0.5 * count( $bysize[ $label]));
	while ( $limit-- > 0) {
		arsort( $bysize[ $label], SORT_NUMERIC);
		list( $k, $v) = hfirst( $bysize[ $label]);
		if ( isset( $DH[ $k])) hshift( $bysize[ $label]);
		else break;
	}
	if ( count( $bysize[ $label])) arsort( $bysize[ $label], SORT_NUMERIC);
	$count++;
}
echo " OK\n";
$bydist = array(); $label2pos = hvak( hk( $H)); // for each disaster city
$count = 0;
foreach ( $D as $h) {
	$label = $h[ 'label']; htouch( $bydist, $label); 	// label
	echoe( $e, "($count/" . count( $D) . ") prepare2($label)");
	foreach ( $H as $L => $h2) {
		if ( $label == $h2[ 'label']) continue;
		$d = pow( pow( $h[ 'x'] - $x[ $label2pos[ $L]], 2) + pow( $h[ 'y'] - $y[ $label2pos[ $L]], 2), 0.5);
		if ( $d > $range * ( mmax( $x) - mmin( $x))) continue;	// out of range
		$bydist[ $label][ $L] = $d;
	}
	$limit = 5; //round( 0.5 * count( $bysize[ $label]));
	while ( $limit-- > 0) {
		asort( $bydist[ $label], SORT_NUMERIC);
		list( $k, $v) = hfirst( $bydist[ $label]);
		if ( isset( $DH[ $k])) hshift( $bydist[ $label]);
		else break;
	}
	if ( count( $bydist[ $label])) asort( $bydist[ $label], SORT_NUMERIC);
	$count++;
}
echo " OK\n";
//die( " bydist:" . json_encode( $bydist) . "\n");
foreach ( $DH as $label => $v) { 	// label, x, y, population
	echoe( $e, "simulating $label");
	$population = $H[ $label][ 'population'];
	// conventional
	foreach ( ttl( 'nearest,bigger,government') as $k) htouch( $conventional, $k, 0, false, false);
	$label2 = null; if ( count( $bydist[ $label])) list( $label2, $dist) = hfirst( $bydist[ $label]);
	$label3 = null; if ( count( $bysize[ $label])) list( $label3, $v) = hfirst( $bysize[ $label]);
	$k = 'government';
	if ( $label3 && ! isset( $bysize[ $label3])) $k = 'bigger';
	if ( $label2 && ! isset( $bydist[ $label2])) $k = 'nearest';
	$conventional[ $k] += $population;
	// proposed
	foreach ( ttl( 'transfers,traveltime,nearest,government') as $k) htouch( $proposed, $k, 0, false, false);
	$stations = array(); // label: distance
	foreach ( $bydist[ $label] as $label2 => $d) {
		if  ( ! isset( $station2pos[ $label2])) continue;	// not a station
		$stations[ $label2] = $bydist[ $label][ $label2];	// distance
	}
	//die( " station2pos: " . json_encode( $station2pos) . "\n");
	$temp = array(); foreach ( $stations as $label2 => $dist) $temp[ $label2] = $station2pos[ $label2]; asort( $temp, SORT_NUMERIC);
	$L2 = null; if ( count( $temp)) list( $label3, $pos) = hfirst( $temp); if ( $label3 && ! isset( $DH[ $label3])) $L2 = $label3;
	$temp = array(); foreach ( $stations as $label2 => $dist) $temp[ $label2] = $station2traveltime[ $label2]; asort( $temp, SORT_NUMERIC);
	$L3 = null; if ( count( $temp)) list( $label3, $pos) = hfirst( $temp); if ( $label3 && ! isset( $DH[ $label3])) $L3 = $label3;
	$temp = array(); foreach ( $stations as $label2 => $dist) $temp[ $label2] = $stations[ $label2]; asort( $temp, SORT_NUMERIC);
	$L4 = null; if ( count( $temp)) list( $label3, $pos) = hfirst( $temp); if ( $label3 && ! isset( $DH[ $label3])) $L4 = $label3;
	$k = 'government'; 
	if ( $L2) $k = 'transfers';
	if ( ! $L2 && $L3) $k = 'traveltime';
	if ( ! $L2 && ! $L3 && $L4) $k = 'nearest';
	$proposed[ $k] += $population;
}
echo " OK\n";
echo "conventional: " . htt( $conventional) . "\n";
echo "proposed: " . htt( $proposed) . "\n";

$sh = tth( 'progress=init'); 
$sh[ 'data'] = compact( ttl( 'range,degree,conventional,proposed'));
jsondump( $sh[ 'data'], 'data.json');


?>