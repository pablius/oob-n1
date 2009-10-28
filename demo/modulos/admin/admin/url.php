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
Url Handler for Admin MODULE

*/


global $ari;
$handle = $ari->url->getVars();

switch ($handle[0])
{ 
//--
case "selector": {
	if (OOB_validatetext::isClean($_POST['modulo']) && $_POST['modulo'] != "/" )
		header( "Location: " . $ari->get('adminaddress') ."/" . $_POST['modulo']);
	else
		throw new OOB_exception('', "404", 'Selecione un modulo válido.');	
break;
}	


case "menu": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "menu.php");
	}
break;

case "newtab": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "newtab.php");
	}
break;

case "getcache": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "getcache.php");
	}
break;

case "getfilters": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "getfilters.php");
	}
break;

case "closetab": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "closetab.php");
	}
break;

case "script": 
	{	
	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "script.php");
	}
break;

case "module":
case "perspective":
{
	if (isset ($handle[1]) && file_exists ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" .$handle[1] . ".php"))
	{ include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" . $handle[1] . ".php");}
	else
	{include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_list.php");}
	break;
}

case "config":
{
		switch ($handle[1])
		{
					
					case "config": {
					include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "config_config.php");
					break;}	
					
					case "cache": {
					include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "config_cache.php");
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
	// include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "perspectiveslist.php");
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