<?php
session_start();
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
?>
<html>
<title><?php echo $ANAME ?></title>
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF8">
<?php jqload() ?>
<script>
// setup jquery environment
$.io = {};
$.io.aname = '<?php echo $ANAME ?>'; // name of this application
$.io.burl = '<?php echo $BURL ?>'; // base URL of this application
$.io.aburl = '<?php echo $ABURL ?>'; // base URL of ajaxkit
$.io.rand = 10;	// for refreshing content
// color and other setup, files stored in app dir overwrite default stored in ajaxkit
// obscene,crazy,bigass,jumbo,huger,huge,larger,large,big,normal,tiny,puny
$.io.font = <?php $dir = is_file( "$BDIR/config.fonts.json") ? $BDIR : $ABDIR; echo trim( ltt( file( "$dir/config.fonts.json"), '')) . "\n" ?>
$.io.defs = <?php $dir = is_file( "$BDIR/config.defs.json") ? $BDIR : $ABDIR; echo trim( ltt( file( "$dir/config.defs.json"), '')) . "\n" ?>
$.io.style = <?php $dir = is_file( "$BDIR/config.style.json") ? $BDIR : $ABDIR; echo trim( ltt( file( "$dir/config.style.json"), '')) . "\n" ?>
$.io.setup = <?php echo ( is_file( "$BDIR/setup.json") ? trim( ltt( file( "$BDIR/setup.json"), '')) : '{}') . "\n" ?>
// id in session is used by jsonload with identity
$.io.session = <?php echo strcleanup( jsonencode( $ASESSION), "\n\t") . "\n" ?>
</script>
<!--[if IE]><script type="text/javascript" src="excanvas.js"></script><![endif]-->
<script src="script.js?<?php echo mr( 10) ?>"></script>
</head>
<body style="overflow:hidden;">
</body>
</html>