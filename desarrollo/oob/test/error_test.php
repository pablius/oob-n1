<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
    include ("..\oob_errorhandler.php");
    
    $error = new OOB_errorhandler ("c:\\");
    
$error->addError ("a", "no tiene cargado el error");
   $error->addError ("b", "hay otro error");
    
     
    if ($eru = $error->getErrorsfor("a"))
   { 
   	 ;
	foreach (	$eru as $er)
 print "<br>[error] : " . $er ;
 //var_dump ($eru);


   }
   else
   print "no error"
?>
