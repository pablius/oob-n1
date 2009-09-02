<?
#OOB/N1 Framework [©2004,2006]
// System General Cron Manager
/*
# This allows to execute cron tasks under each module.
#
#
*/
 try {
 
@ignore_user_abort ( true );
set_time_limit('200000000');
	
echo "Executing CRON at " .  date("h:i:s") . " ...\n";

require_once ("oob" . DIRECTORY_SEPARATOR . "engine.php");
	global $ari;

	oob_ari::initEngine("cron");

	$modulos = oob_module::listModules();

	foreach ($modulos as $modulo)
	{
	$ari->module =  $modulo;
	print "Modulo: " . $modulo->name() . "\n";
	@include ($modulo->admindir() . DIRECTORY_SEPARATOR . 'cron.php');
	}
print "END CRON";

 } catch (OOB_exception $e) {

 print $e->getUserMessage();
 
 }
?>