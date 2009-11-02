<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE PROCESA EL FORMULARIO BORRAR CACHE

global $ari;
$ari->popup = true;

 if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('cache','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}

 	$ari->clearCache();
 
$result=array();
$result["errors"]=array();
$result["success"] = true;

$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);
 
?>
