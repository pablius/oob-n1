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

$mensaje = new perfil_mensaje($handle[2]);

if ($mensaje->get('perfil')->id() == $perfil->id())
{
	if ($mensaje->delete())
	{
		echo 'borrado';
		exit;
	}
}
echo 'no se puede borrar';
exit;




?>
