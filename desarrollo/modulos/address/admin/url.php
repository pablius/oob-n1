<?php
# Eumafes v2 [�2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 15-sep-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
 global $ari;
$handle = $ari->url->getVars();

switch ($handle[0])
{ //--
	case "city":
	case "state":
	{
		if (isset ($handle[1]) && file_exists ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" .$handle[1] . ".php"))
		{include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" . $handle[1] . ".php");}
		else
		{include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_list.php");}
		break;
	}
	
	case "":
	{
	//	include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "role_list.php");
	break;
	}


	default:
	{
		throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
		break;
	}

}//-- end switch

?>
