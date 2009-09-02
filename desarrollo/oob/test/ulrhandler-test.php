<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 16/02/2005
 * @author Pablo Micolini
 */
 
 	require_once ("..\oob_urlhandler.php");
 $clean = new oob_urlhandler();


if ($clean->getModule())
print $clean->getModule();
else 
 print "no mod";
 var_dump ($clean->getVars());
 //print_r "<br>page: ".$GLOBALS["page"];
?>
