<?php

global $ari;
$ari->t->force_compile= true;
$ari->t->cache = false;
$ct = new OOB_cleantext();
	
	
$default_login = $ari->get('config')->get('defaultlogin', 'main');
$ar_config = $ari->get("config")->get('can-self-register', 'user');
$ari->t->assign("end_year", date("Y") - 18);

//see if a user can register from user-interface
$allowregister = false;
if ( $ar_config === "true" || $ar_config === "yes")
{
	$allowregister = true;
}

// check user permissions
$perfil = new perfil_perfil();

$usuario = new oob_user();
$disable_username_change = false;
$new_user = true;

if (is_a($ari->user, 'oob_user'))
{
	$disable_username_change = true;
	$new_user = false;
	$usuario = $ari->user;
	// asignamos datos del usuario
	
	if ($perfil_existente = perfil_perfil::existe_usuario($ari->user))
	{
		$perfil = $perfil_existente[0];

	}
}

$ari->t->assign("register", $allowregister );
$ari->t->assign("disable_username_change", $disable_username_change );

//nuevo 
if (count ($_POST))
{
		
	// el usuario queda pendiente o se activa inmediatamente
	$validation = $usuario->get('new_validation');
	
	if ($new_user)
	{
		if (isset($_POST['usuario']))
		{
			$usuario->set ('uname', $_POST['usuario']);
		}
		
		if ($validation == "no")
		{
			$usuario->set ('status', "1");
		}
		else
		{
			$usuario->set ('status', "0");
		}
	}
	
	// usuario
	$usuario->set ('email', $_POST['email']);
	
	if (isset($_POST['pass']) && isset($_POST['passtwo']) && ($_POST['pass'] != '' || $_POST['passtwo'] !==''))
	{	
		if($_POST['pass'] === $_POST['passtwo'])
		{
			$usuario->set ('password', $_POST['pass']);
		}
		else
		{
			$usuario->error()->addError ( "NO_CONCUERDAN");
		}
	}
	
	if (!isset ($_POST['condiciones']) || $_POST['condiciones'] !='checkbox')
	{
		$usuario->error()->addError ( "INVALID_CONDICIONES");
	}
	
	// perfil
	$perfil->set('nombre',$_POST['nombre']);
	$perfil->set('fecha_nacimiento',new Date($_POST['fecha_nacimiento_Year'] . '-' .oob_validatetext::addZero($_POST['fecha_nacimiento_Month']). '-' .oob_validatetext::addZero($_POST['fecha_nacimiento_Day']) . ' 00:00:00'));
	
	
	$perfil->set('telefono',$_POST['telefono']);
	$perfil->set('bio',$_POST['bio']);
	$perfil->set('url',$_POST['url']);


	if ($usuario->store()) 
	{  
		if ($new_user)
		{
			$usuario->linkStandardGroup();
		}
		
		$perfil->set('usuario',$usuario);
		
		
		if ($perfil->store())
		{
						
			if ($new_user)
			{
				// mandar mail de nuevo usuario
				$perfil->enviar_mail_perfil_nuevo();
				
				// si el usuario puede loguearse
				if ($validation == "no")
				{
					oob_user::login($_POST['usuario'],$_POST['pass'] );
				}
				else //si no lo mandamos a una direccion donde le decimos que su usuario está pendiente de aprobacion
				{
					$_SESSION['redirecting'] = '/seguridad/pending';
				}
				
				
				if (isset ($_SESSION['redirecting']))
				{
					$default_login = $_SESSION['redirecting'];
					unset ($_SESSION['redirecting']);
				}
			}
			
			header( "Location: " . $ari->get('webaddress') . $default_login);
			exit;
		}
			
	}
	
	

	$ari->t->assign("error", true);
	$errores = array();
	

	// errores del usuario
	if ($e =  $usuario->error()->getErrors())
	{
		$errores = array_merge ($e,$errores);
	}
	
	// errores del perfil
	$perfil->isValid();
	if ($e =  $perfil->error()->getErrors())
	{
		$errores = array_merge ($e,$errores);
	}
			
	//var_dump ($errores);
	foreach ($errores as $error)
	{
		$ari->t->assign($error, true);
	}
		

}	
	// mostramos los datos en la pantalla

	// usuario
	$ari->t->assign("usuario", $usuario->get('uname') );
	$ari->t->assign("email", $ct->dropHTML($usuario->get('email')) );

	// asignamos datos del perfil
	$ari->t->assign("nombre", $ct->dropHTML($perfil->get('nombre')) );
	
	$fecha_nacimiento = $perfil->get('fecha_nacimiento');
	if (is_a($fecha_nacimiento,'Date'))
	{
		$ari->t->assign("fecha_nacimiento", $ct->dropHTML($fecha_nacimiento->format("%Y-%m-%d")));
	}
	
	$ari->t->assign("telefono", $ct->dropHTML($perfil->get('telefono')));
	$ari->t->assign("bio", $ct->dropHTML($perfil->get('bio')));
	$ari->t->assign("url", $ct->dropHTML($perfil->get('url')));

	

$ari->t->display($ari->module->usertpldir(). DIRECTORY_SEPARATOR."perfil_modificar.tpl");

?>
