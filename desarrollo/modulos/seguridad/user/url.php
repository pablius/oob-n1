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
Url Handler for SECURITY MODULE

*/
global $ari;
$handle = $ari->url->getVars();

switch ($handle[0])
{ //--
case "login": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . "login.php");
break;}	

case "logout": {
 if (!is_a($ari->user, 'oob_user'))
 {header( "Location: " . $ari->get("webaddress") . '/');
 exit; } 	
$ari->user->logout();

break;}	

case "nuevo": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . "nuevo.php");
break;}	

case "forgot": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . "forgot.php");
break;}

case "restored": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . "restored.php");
break;}


case "update": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  ."update.php");
break;}	

case "delete": {
include ($ari->module->userdir() . DIRECTORY_SEPARATOR  ."delete.php");
break;}	

default:
{
throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
break;
}

//-- end switch
}

?>