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
{ 
//--

case "info":
{
		switch ($handle[1])
		{
					case "about": {
					include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "info_about.php");
					break;}	
					
					case "phpinfo": {
					include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "info_phpinfo.php");
					break;}	
					case "welcome": {
					include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "info_welcome.php");
					break;}	
					default:
					{
					throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
					break;
					}
					
		}

break;
}

// default case, when module is selected.
case "":
{
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "info_about.php");
break;
}

default:
{
	throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
break;
}

//-- end switch
}

?>