<?php
// places
function placesread() { // returns $H
	$H = array();
	foreach ( flget( '.', 'places', '', 'b64jsonl') as $file) { $in = finopen( $file); while ( ! findone( $in)) { 
		list( $h, $p) = finread( $in, true, true, false); if ( !$h) continue;
		extract( $h); // label
		$H[ $label] = $h;
	}; finclose( $in); }
	return $H;
}
function placeschartprepare( $C2, $H) { // returns [ $C2, $x, $y, $z] 
	$x = array(); $y = array(); $z = array();
	foreach ( $H as $h) { 
		extract( $h); 
		//die( json_encode( $h) . "\n");
		lpush( $x, $ib); lpush( $y, $hb); lpush( $z, round( log10( $population), 1));
	}
	$C2->train( $x, $y, '0.05:0.05:0:0.05');
	$C2->autoticks( null, null, 10, 10);
	$C2->frame( null, null);
	return array( $C2, $x, $y, $z);
}
function placeschartdrawnodes( $C2, $x, $y, $z) {
	global $S, $FS;
	for ( $i = 0; $i < count( $x); $i++) chartscatter( $C2, array( $x[ $i]), array( $y[ $i]), 'circle', $z[ $i], $S);
	for ( $size = 1; $size < 8; $size++) chartext( $C2, ttl( mmin( $x) . ':-9'), ttl( mmax( $y) . ':-' . ( ( $size - 1) * 7)), '' . round( 0.001 * pow( 10, $size), 2) . 'k', $S, $FS, 'plotstring');
	for ( $size = 1; $size < 8; $size++) chartscatter( $C2, ttl( mmin( $x) . ':-14'), ttl( mmax( $y) . ':-' . ( ( $size - 1) * 7) . ':3'), 'circle', $size, $S);
}
//  paths
function pathsread( $file = null) {	// H
	if ( $file === null) $file = 'paths.bz64jsonl';
	$in = finopen( $file); $H = array();
	while ( ! findone( $in)) {
		list( $h, $p) = finread( $in); if ( ! $h) continue;
		lpush( $H, $h);
	}
	finclose( $in);
	return $H;
}
function pathsdrawone( $C2, $H, $path, $S) {
	global $x, $y; $tag2pos = hvak( hk( $H));
	$x2 = array(); $y2 = array();
	foreach ( $path as $tag) { lpush( $x2, $x[ $tag2pos[ $tag]]); lpush( $y2, $y[ $tag2pos[ $tag]]); }
	chartline( $C2, $x2, $y2, $S);
}
// disaster
function disastermake( $E, $xdiff, $ydiff) { // returns [ $D, smallest distance to main path]
 	global $H, $x, $y, $backbone; $tag2pos = hvak( hk( $H)); $D = array();
 	extract( $E);	// label, ...
 	foreach ( $tag2pos as $label2 => $pos) {
 		if ( abs( $x[ $tag2pos[ $label]] - $x[ $tag2pos[ $label2]]) >= $xdiff) continue;
 		if ( abs( $y[ $tag2pos[ $label]] - $y[ $tag2pos[ $label2]]) >= $ydiff) continue;
 		$h = $H[ $label2]; $h[ 'x'] = $x[ $tag2pos[ $label2]]; $h[ 'y'] = $y[ $tag2pos[ $label2]];
 		lpush( $D, $h);
 	}
 	$h = array();
 	foreach ( $D as $h2) {
 		$label = $h2[ 'label'];
 		foreach ( $backbone as $label2) {
 			$h[ "$label:$label2"] = pow( pow( $x[ $tag2pos[ $label]] - $x[ $tag2pos[ $label2]], 2) + pow( $y[ $tag2pos[ $label]] - $y[ $tag2pos[ $label2]], 2), 0.5);
 		}
 		
 	}
 	asort( $h, SORT_NUMERIC); list( $tag, $distance) = hfirst( $h);
 	return array( $D, $distance);
}
function disasterdraw( $D) {
	global $C2, $S;
	$S2 = clone $S; $S2->draw = '#f00'; $S2->style = 'DF'; $S2->fill = '#f00'; $S2->lw = 0;
	chartscatter( $C2, hltl( $D, 'x'), hltl( $D, 'y'), 'circle', 3, $S2);
}
// metromap
function metromapwrite( $H, $path) { 	// H: [ { area, lineshort, linefull, stationshort, stationfull}, ...] -- list of station hashes
	$h = array(); foreach ( $H as $h2) { extract( $h2); htouch( $h, $lineshort); lpush( $h[ $lineshort], $stationshort); }
	$out = fopen( $path, 'w');
	fwrite( $out, "graph G {\n"); 
	foreach ( $h as $line => $stations) fwrite( $out, "   $line -- " . ltt( $stations, ' -- ') . "\n");
	fwrite( $out, "}\n");
	fclose( $out);
}
function metromaptext( $json, $size =  '11,8') { 	// depends on graphvizwrite() size in inches, default is an *.info file next to input *.dot
	$L = ttl( $json, '.'); lpop( $L); lpush( $L, 'dot'); $out = ltt( $L, '.'); 
	metromapwrite( jsonload( $json), $out); $in = $out;
	$L = ttl( $in, '/', '', false); $in = lpop( $L); $root = ltt( $L, '/'); 
	$L = ttl( $in, '.'); lpop( $L); $out = ltt( $L, '.') . '.info';
	$path = '/APPS/graphviz/bin';
	//$CWD = getcwd(); chdir( $root);
	$c = "$path/bin/neato -Gsize=$size -Tdot $in -o temp.info"; procpipe( $c);
	if ( ! is_file( $out)) die( "ERROR! graphviztext() failed to run c[$c]\n");
	//chdir( $CWD);
	return "$root/$out";
}
function metromapdf( $C2, $json, $legend = true, $specialine = null, $fontsize = 10, $size = '11,8') { 	// depends on graphvizwrite(), will create a PDF file with the same root
	$in2 = metromaptext( $json, $size);	// create *.info file first
	$L = ttl( $in2, '.'); lpop( $L); lpush( $L, 'pdf'); $out = ltt( $L, '.');
	$colors = ttl( '#099,#900,#990,#059,#809,#8B2,#B52,#29E,#0A0,#C0C');
	$raw = jsonload( $json); $link2line = array(); $line2stations = array();
	foreach ( $raw as $h2) {
		extract( $h2); 	// area, lineshort, linefull, stationshort, stationfull
		htouch( $line2stations, $lineshort);
		lpush( $line2stations[ $lineshort], $stationshort);
	}
	foreach ( $line2stations as $line => $stations) {
		lunshift( $stations, $line);
		for ( $i = 1; $i < count( $stations); $i++) $link2line[ $stations[ $i - 1] . ',' . $stations[ $i]] = $line;
	}
	$L = ttl( $json, '.'); lpop( $L); $root = ltt( $L, '.');
	// try to draw the PDF by yourself
	$lines = file( 'temp.info'); $line2color = array(); $station2colors = array(); $line2comment = array();	
	$stations = array(); $links = array();
	foreach ( $lines as $line) {
		$line = trim( $line); if ( ! $line) continue;
		$bads = '];'; for ( $i = 0; $i < strlen( $bads); $i++) $line = str_replace( substr( $bads, $i, 1), '', $line);
		$line = str_replace( '",', ':', $line); $line = str_replace( ', ', ':', $line);
		$line = str_replace( ',', ' ', $line);
		$line = str_replace( ':', ',', $line);
		$line = str_replace( '"', '', $line);
		$L = ttl( $line, '['); if ( count( $L) != 2) continue;
		$head = lshift( $L); $tail = lshift( $L);
		$h = tth( $tail); if ( ! isset( $h[ 'pos'])) continue;
		if ( count( ttl( $head, '--')) == 1) { 
			$h = hm( $h, lth( ttl( $h[ 'pos'], ' '), ttl( 'x,y'))); $stations[ trim( $head)] = $h; continue; 
		}
		extract( lth( ttl( $head, '--'), ttl( 'name1,name2')));
		$h = hm( $h, lth( ttl( $h[ 'pos'], ' '), ttl( 'x1,y1,x2,y2,x3,y3,x4,y4'))); 
		$k = "$name1,$name2";
		$h[ 'line'] = $link2line[ $k];
		$links[ $k] = $h;
	}
	foreach ( $raw as $h) { extract( $h); if ( ! isset( $line2color[ $lineshort])) $line2color[ $lineshort] = $lineshort == $specialine ? '#000' : ( count( $colors) ? lshift( $colors) : '#666'); $station2colors[ $lineshort] = array( $line2color[ $lineshort]); }
	foreach ( $raw as $h) { extract( $h); htouch( $station2colors, $stationshort); lpush( $station2colors[ $stationshort], $line2color[ $lineshort]); }
	//foreach ( $raw as $h) { extract( $h); $line2comment[ $lineshort] = $linefull . ' (' . $area . ') ' . $linecomment; }
	$bottom = 0.05; if ( $legend) $bottom += round( ( count( $line2color) * $fontsize) / 200, 2);
	$xs = array(); $ys = array(); foreach ( $stations as $k => $v) { extract( $v); lpush( $xs, $x); lpush( $ys, $y); }
	$C2->train( $xs, $ys); $C2->autoticks( null, null, 10, 10); $C2->frame( null, null);
	$yoff = '-5';
	foreach ( $links as $k => $v) {
		extract( $v); 	// x1..4, y1..4
		plotcurve( $C2->plot, $x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4, 'D', $line == $specialine ? 1 : 0.5, $line2color[ $line], null, 1.0);
	}
	foreach ( $stations as $k => $v) {
		extract( $v); 	// width, height, x, y
		extract( plotstringdim( $C2->plot, $k, $fontsize)); // w, h
		$colors = $station2colors[ $k]; $add = 0.07 * count( $colors);
		foreach ( $colors as $color) {
			$h2 = hvak( $line2color, true); $line = $h2[ $color];
			$color2 = ( $line == $specialine) ? '#fff' : ( isset( $line2color[ $k]) ? '#fff' : '#000');
			$color3 = isset( $line2color[ $k]) ? $color : ( $specialine == $line ? '#000' : '#fff');
			plotellipse( $C2->plot, $x, $y, ( 0.8 + $add) * $w, ( 0.7 + $add) * $h, 0, 0, 360, 'DF', 0.5, $color, $color3);
			plotstringmc( $C2->plot, $x, $y, $k, $fontsize, $color2, 1.0);
			$add -= 0.07;
			// draw line legend if needed
			if ( isset( $used[ $line]) || ! $legend) continue;
			// draw legend
			plotellipse( $C2->plot, 0.5 * $w, "0:$yoff", 0.8 * $w, 0.7 * $h, 0, 0, 360, 'DF', 0.5, $color, $color);
			plotstringmc( $C2->plot, 0.5 * $w, "0:$yoff", $line, $fontsize, '#fff', 1.0);
			plotstringml( $C2->plot, ( 0.5 * $w) . ":$w", "0:$yoff", $line2comment[ $line], $fontsize, '#000', 1.0);
			$used[ $line] = true; $yoff .= ":-$em:-2";
		}
		
	}
	//plotdump( $P, $out);
	return $out;
}


?>