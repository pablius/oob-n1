<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE PROCESA EL FORMULARIO BORRAR CACHE

global $ari;
$ari->t->caching = 0; // dynamic content 

 if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('cache','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}

 	$ari->clearCache();
 	 
 
?>
