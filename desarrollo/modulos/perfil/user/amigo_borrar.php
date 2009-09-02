<?php

global $ari;
$ari->popup = true;
$handle = $ari->url->getVars();

// handle 2

// check user permissions
if (is_a($ari->user, 'oob_user'))
{
	$usuario = $ari->user;
	// asignamos datos del usuario
	if ($perfil_existente = perfil_perfil::existe_usuario($ari->user))
	{
		$perfil = $perfil_existente[0];
	}
}


// buscamos el objeto de relacion de esta amistad a punto de morir.
$destino = new perfil_perfil ($handle[2]);		

if ($amistad = perfil_amigo::es_amigo($perfil,$destino))
{
	foreach ($amistad as $amigo)
	{
		$amigo->delete();
	}
	echo 'borrado';
	exit;
}

echo 'no se pudo borrar';
exit;



?>
