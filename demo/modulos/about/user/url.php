<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/*
Url Handler for ABOUT module

*/
global $ari;

$handle = $ari->url->getVars();

switch ($handle[0])
{ //--

case "":
case "uso":
case "privacidad":
{
$text = false; 
break;
}

default: {
throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
break;
}

}
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . "about.php");
?>