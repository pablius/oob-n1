<?php


global $ari;
$handle = $ari->url->getVars();
//var_dump($handle);

switch ($handle[0])
{ 
	case "perfil":
	case "mensaje":
	case "notificacion":
	case "amigo":
	case "seguidor":
	case "grupo":
	{
		if (isset ($handle[1]) && file_exists ($ari->module->userdir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" .$handle[1] . ".php"))
		{
			include ($ari->module->userdir() . DIRECTORY_SEPARATOR  . $handle[0] . "_" . $handle[1] . ".php");
		}
		else
		{
			throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
		}
		break;
	}
	

	default:
	{
		throw new OOB_exception('', "404", 'Revise que la dirección ingresada sea correcta.');	
		break;
	}

}//-- end switch
 
 
?>
