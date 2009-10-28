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

$destino = new perfil_perfil($handle[2]);

$amigo = new perfil_amigo();
$amigo->set('origen',$perfil);
$amigo->set('destino',$destino);
$amigo->set('fecha',new Date());

if ($amigo->store())
{
	echo 'agregado';
	exit;
}

echo 'no se puede agregar';
exit;




?>
