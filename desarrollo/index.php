<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 

*/

//  USER SIDE INDEX

 try {
 	
 	ignore_user_abort ( true );
	require_once ("oob".DIRECTORY_SEPARATOR."engine.php");
	global $ari;
	
	OOB_ari::initEngine();
	$ari->generateOutput();

//	$time= $ari->finCronometro();

//	print "<br><br><small>".$time." mS <br>Nutus OOB/n1 ".$ari->version()."<br></small>";

//   print $ari->ExecutionMonitor();
// ------------------------------------------------------------------------------------------------------	
 } catch (OOB_exception $e) {
	error_reporting (0);
	# After all, sometimes things can go wrong!, lets be brave and handle it
	require_once ("oob".DIRECTORY_SEPARATOR."librerias".DIRECTORY_SEPARATOR."smarty".DIRECTORY_SEPARATOR."Smarty.class.php");

	// start smarty object for section template
	$extpl= new Smarty;
	$extpl->template_dir= dirname(__FILE__).DIRECTORY_SEPARATOR.'perspectives'.DIRECTORY_SEPARATOR.'default';
	$extpl->compile_dir= dirname(__FILE__).DIRECTORY_SEPARATOR.'archivos'.DIRECTORY_SEPARATOR.'cache';
	$extpl->debugging= false;
	$extpl->force_compile= false;
	$extpl->caching=0;
	$extpl->compile_check= false;
	//end smarty load

	$extpl->assign("error_numero", $e->getCode());
	$extpl->assign("error_mensaje", $e->getUserMessage());

	if (502 > $e->getCode() && $e->getCode() < 400)
		{		$e->logmessage();}
				else
		{		header("HTTP/1.1 {$e->getCode()} {$e->getUserMessage()}");}
	
$extpl->display("error.tpl");
} 



?>

