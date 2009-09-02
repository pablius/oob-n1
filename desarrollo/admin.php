<?php
//phpinfo();
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/*
//Crear los directorios necesarios...
mkdir("B:/eximius");
mkdir("B:/eximius/db");
*/

// ADMIN SIDE INDEX

try {
 	ignore_user_abort ( true );
	require_once ("oob".DIRECTORY_SEPARATOR."engine.php");
	global $ari;

	
	oob_ari::initEngine('admin');
	
	$ari->generateOutput();

//	$time= $ari->finCronometro();
//
//	print "<br><br><small>".$time." mS <br>OOB/N1 ".$ari->version()."<br></small>";
//    print $ari->ExecutionMonitor();



} catch (OOB_exception $e) {

			# After all, sometimes things can go wrong!, lets be brave and handle it
			require_once ("oob".DIRECTORY_SEPARATOR."librerias".DIRECTORY_SEPARATOR."smarty".DIRECTORY_SEPARATOR."Smarty.class.php");

			// start smarty object for section template
			$extpl= new Smarty;
			$extpl->template_dir= dirname(__FILE__).DIRECTORY_SEPARATOR.'perspectives'.DIRECTORY_SEPARATOR.'default';
			$extpl->compile_dir= dirname(__FILE__).DIRECTORY_SEPARATOR.'archivos'.DIRECTORY_SEPARATOR.'cache';
			$extpl->debugging= false;
			$extpl->force_compile= false;
			$extpl->caching= 0;
			$extpl->compile_check= false;
			//end smarty load


			$extpl->assign("error_numero", $e->getCode());
			$extpl->assign("error_mensaje", $e->getUserMessage());
			$extpl->display("error.tpl");
	
		 if (502 > $e->getCode() && $e->getCode() < 400)
		 {		$e->logmessage();}
		 else
		 {		
		 
		 header("HTTP/1.1 {$e->getCode()} {$e->getUserMessage()}");
	     $msg = utf8_decode($e->getUserMessage()); 
		 header("message: {$msg} ");
		 
		 }

		

	require_once ("oob".DIRECTORY_SEPARATOR."OOB_ext_comunication.php");
	
	// RESULTADO
	$obj_comunication = new OOB_ext_comunication();
	$obj_comunication->set_message($e->getUserMessage());
	$obj_comunication->set_code($e->getCode());
	$obj_comunication->set_data('');

	$obj_comunication->send(true);	

}


?>

