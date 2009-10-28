<?php

global $ari;
$ari->t->force_compile= true;
$ari->t->cache = false;
$ct = new OOB_cleantext();

$limit = 20;

if (!isset ($_GET['pos']) || !oob_validatetext::isNumeric($_GET['pos']))
{
	$_GET['pos'] = 0;
}

	
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
else
{
	seguridad::requireLogin();
}


// mostramos los datos en la pantalla
$ari->t->assign("nombre", $perfil->name() );
$ari->t->assign("telefono", $ct->dropHTML($perfil->get('telefono')));
$ari->t->assign("bio", $ct->dropHTML($perfil->get('bio')));
$ari->t->assign("url", $ct->dropHTML($perfil->get('url')));
$ari->t->assign("foto", $perfil->foto() );

$ari->t->assign("seguidores", perfil_amigo::get_mis_seguidores($_GET['pos']));
$ari->t->assign("seguidores_count", perfil_amigo::get_mis_seguidores_count());
$ari->t->assign("limit", $limit);


$ari->t->display($ari->module->usertpldir(). DIRECTORY_SEPARATOR."perfil_seguidores.tpl");

?>
