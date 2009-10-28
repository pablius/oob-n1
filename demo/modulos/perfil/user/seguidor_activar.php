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

$amigo = new perfil_amigo($handle[2]);


if ($amigo->get('destino')->id() == $perfil->id())
{
	$amigo->set('bloqueo_destino', 0);
	
	if ($amigo->store())
	{
		echo 'activado';
		exit;
	}
}
echo 'no se puede activar';
exit;




?>
