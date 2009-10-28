<?php

global $ari;
$ari->t->force_compile= true;
$ari->t->cache = false;
$ct = new OOB_cleantext();
$handle = $ari->url->getVars();
$es_amigo = true;

$limit = 20;

if (!isset ($_GET['pos']) || !oob_validatetext::isNumeric($_GET['pos']))
{
	$_GET['pos'] = 0;
}

// perfil del usuario
// check user permissions
if (is_a($ari->user, 'oob_user'))
{
	$usuario = $ari->user;
	// asignamos datos del usuario
	if ($perfil_existente = perfil_perfil::existe_usuario($ari->user))
	{
		$perfil_usuario = $perfil_existente[0];
	}
}



if (!isset($handle[2]) || !oob_validatetext::isNumeric($handle[2]))
{
	$perfil = $perfil_usuario;
}
else
{
	$perfil = new perfil_perfil($handle[2]);
}

// mostramos los datos en la pantalla
$ari->t->assign("nombre", $perfil->name() );
$ari->t->assign("id_perfil", $perfil->id() );
$ari->t->assign("telefono", $ct->dropHTML($perfil->get('telefono')));
$ari->t->assign("bio", $ct->dropHTML($perfil->get('bio')));
$ari->t->assign("url", $ct->dropHTML($perfil->get('url')));
$ari->t->assign("foto", $perfil->foto() );


if (isset($perfil_usuario) && $perfil_usuario->id() != $perfil->id())
{
	if (!perfil_amigo::es_amigo($perfil_usuario,$perfil))
	{
		$es_amigo = false;
	}
}


$ari->t->assign("es_amigo", $es_amigo);
$ari->t->assign("timeline", perfil_notificacion::get_timeline($perfil,$_GET['pos']));
$ari->t->assign("timeline_count", perfil_notificacion::get_timeline_count($perfil));
$ari->t->assign("limit", $limit);

$ari->t->display($ari->module->usertpldir(). DIRECTORY_SEPARATOR."perfil_ver.tpl");

?>
