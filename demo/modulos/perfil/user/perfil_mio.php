<?php

global $ari;
$ari->t->force_compile= true;
$ari->t->cache = false;
$ct = new OOB_cleantext();
	
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


//nuevo 
if (count ($_POST))
{
	
	$foto = false;
	$mensaje = false;
	
	// tenemos 3 casos
	// solo mensaje
	// solo foto
	// las dos cosas.
		
	$mensaje = new perfil_mensaje();
	$mensaje->set ('perfil',$perfil);
	$mensaje->set ('fecha', new Date());
	if ($_POST['mensaje'] != '')
	{
		$mensaje->set('mensaje',$_POST['mensaje']);
	}
	
	$up = new OOB_fileupload("file");
	if ($_FILES['file']['name'] != '')
	{
		$allowedTypes = array("image/gif","image/pjpeg","image/jpeg","image/x-png");
		$uploadPath = $ari->get('filesdir') . DIRECTORY_SEPARATOR .'archivos'  . DIRECTORY_SEPARATOR . 'fotos';
		
		if ($upload = $up->upload($uploadPath, false, $allowedTypes, $perfil->get('id') . '_' . time(),true))
		{
			$mensaje->set('foto',$upload['name']);
			//$mensaje->set('exif',exif_read_data($upload['full_path'])); // no anda don exif!
		}	
	}
	
	if ($_POST['mensaje'] == '' && $_FILES['file']['name'] == '')
	{
		$mensaje->error()->addError('NO_MENSAJE');
	}
	
	if ($mensaje->store())
	{
		header( "Location: " . $ari->get('webaddress') .'/perfil/perfil/mio');
		exit;
	}
	

	$ari->t->assign("error", true);
	$errores = array();
	
	
	// errores del mensaje
	if ($e =  $mensaje->error()->getErrors())
	{
		$errores = array_merge ($e,$errores);
	}
	
	if ($e =  $up->error()->getErrors())
	{
		$errores = array_merge ($e,$errores);
	}
	
			
	var_dump ($errores);
	foreach ($errores as $error)
	{
		$ari->t->assign($error, true);
	}
	

}	
	// mostramos los datos en la pantalla

	// usuario
	$ari->t->assign("nombre", $perfil->name() );
	$ari->t->assign("telefono", $ct->dropHTML($perfil->get('telefono')));
	$ari->t->assign("bio", $ct->dropHTML($perfil->get('bio')));
	$ari->t->assign("url", $ct->dropHTML($perfil->get('url')));
	$ari->t->assign("foto", $perfil->foto() );

	$ari->t->assign("novedades", perfil_notificacion::get_novedades_usuario());

$ari->t->display($ari->module->usertpldir(). DIRECTORY_SEPARATOR."perfil_mio.tpl");

?>
