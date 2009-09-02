<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
  include ("..\oob_agentdetector.php");
  
  $agent = new oob_agentdetector ();
  $agent->printInfo();
  print $_SERVER["HTTP_ACCEPT_LANGUAGE"];

?>
