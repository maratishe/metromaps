// assumes that the environment has already been set
$.ioutils.nolog = true
$.ioutils.jsonsession = true;
$( document).ready( function() { $( 'body').css({ width: '100%', height: '100%', position: 'relative'}); start(); })
function start() { $( 'body').ioanimoutemptyin( 'fast', function() { $.jsonload( 'actions.php', { action: 'getdata'}, function( json) {
	var $box = $( 'body').ioover(); var w = $box.width(); var h = $box.height();
	var $canvas = $box.ioover();
	var D = {};
	var one = function( h2) { var $box2 = $box.ioover(); $box2.css({ top: Math.round( ( 1 - h2.y) * h) + 'px', left: Math.round( h2.x * w) + 'px', width: '10px', height: '10px', 'background-color': '#000'}).ioatomsOnOffButton({ donotwrap: true, donotdraw: true, on: 1.0, hover: 0.6, off: 0.3}).onchange( function( status) { 
		if ( ! status) { delete D[ h2.label]; $box2.css({ 'background-color': '#000'}); }
		else { $box2.css({ 'background-color': '#f00'}); D[ h2.label] = true; } 
	})}
	var done = function() { 
		var $box2 = $( 'body').ioover().ioground( '#fff', 0.9)
		.ioover().css({ width: '30%', height: 'auto'})
		var tag = $box2.ioover( true).ioatomsInput({ css: { 'font-size': $.io.defs.fonts.huge, height: '1.2em', border: '2px solid #555', 'background-color': '#ccc', color: '#fff', width: '100%'}})
		$box2.iocenterv();
		$box2.iocenterh();
		tag.inner().focus();
		var submit = function() { $.jsonload( 'actions.php', { action: 'store', tag: tag.value(), path: $.h2json( $.hk( D), true)}, function( json) { start(); })}
		$( window).keyup( function( e) { if ( e.keyCode == 13) return eval( submit)(); })
	}
	$box.ioloop( $.hv( json.data), '1ms', function( dom, value, sleep, c) { 
		if ( ! value.length) return eval( c)();
		eval( one)( value.shift());
		eval( c)( value);
	})
	$( window).unbind().keyup( function( e) { 
		console.log( e.keyCode);
		if ( e.keyCode == 16) return eval( done)();
	})
	$( 'body').ioover({ position: 'absolute', bottom: '6px', right: '6px', 'font-size': $.io.defs.fonts.small}).append( 'Keys: <strong>S, enter</strong>')
})})}

