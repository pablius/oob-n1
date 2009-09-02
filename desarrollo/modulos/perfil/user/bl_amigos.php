<?php

global $ari;
$plantilla = $ari->newTemplate();
$plantilla->caching = 0; 
$plantilla->force_compile= true;
$modulo = new oob_module ('perfil');

$limit = 4*8-1; // dejamos un lugar para el link.

// check user permissions
if (is_a($ari->user, 'oob_user'))
{
	$usuario = $ari->user;
	// asignamos datos del usuario
	if ($perfil_existente = perfil_perfil::existe_usuario($ari->user))
	{
		$perfil = $perfil_existente[0];
	
		
		$plantilla->assign("amigos", perfil_amigo::get_mis_amigos_bloque($limit));
		
		$plantilla->display($modulo->usertpldir(). DIRECTORY_SEPARATOR."bl_amigos.tpl");
	}
}




?>
