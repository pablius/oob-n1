<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 01-jul-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */

global $ari;
$handle = $ari->url->getVars();

switch ($handle[0])
{ 
	
	case "contacto":
	case "infoadicional":
	case "notificacion":
	case "areas":
	{
	
		//verifiacar si existe el archivo especificado en la url
		if (isset ($handle[1]) && file_exists ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" .$handle[1] . ".php"))
		{	
			include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" . $handle[1] . ".php");
		}
		else
		{//no existe el archivo, verificar si existe el archivo list.php a partir del permiso seteado
			throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
		}	
		break;
	}
	case "":
	{
		include ($ari->module->admindir() . DIRECTORY_SEPARATOR  . "contacto_list.php");
		break;
	}
	default:
	{
		throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
		break;
	}
	
}//-- end switch

?>
