<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	require_once ("..\engine.php");
 
 try {

 		$ari = new OOB_ari ();
 	$a = 0;
 	if ($a == 0)
   throw new OOB_Exception("hola", "0", "tres", true);
   
} catch (Exception $e) {      // Will be caught
   echo "Caught my exception\n", $e->getMessage()," " ,$e->getUserMessage()," " ,$e->getFile()," " ,$e->getLine();
  
 }
?>
