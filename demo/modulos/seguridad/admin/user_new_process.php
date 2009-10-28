<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Codigo por JPCOSEANI
// Script que procesa los datos del FORM NUEVO USUARIO



global $ari;
$ari->popup = 1;

$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

//se crea un nuevo objeto usuarios
$usuario = new oob_user();

//se asigna el usuario
if( isset($_POST['txt_pass']) )
{
	$usuario->set( 'uname', $_POST['txt_usuario'] );
}else{
	throw new OOB_Exception_400("La variable [txt_usuario] no esta definida");
}	

/* Asignamos los valores al objeto directamente del formulario */
// password

if( isset($_POST['txt_pass']) )
{

	if ($_POST['txt_pass'] != "") // solo lo asignamos si escribi\u00f3 algo
	{
		$usuario->set ('password', $_POST['txt_pass']);
	}	
	
}else{
	throw new OOB_Exception_400("La variable [txt_pass] no esta definida");
}

if( !isset($_POST['txt_repetir']) )
{
	throw new OOB_Exception_400("La variable [txt_repetir] no esta definida");
}

// email
if( isset($_POST['txt_email']) )
{	
	$usuario->set ('email', $_POST['txt_email']);
}else{
	throw new OOB_Exception_400("La variable [txt_email] no esta definida");
}

// status
$usuario->set ('status', "1");


// tratamos de grabar si puso los dos pass iguales

if ($_POST['txt_pass'] === $_POST['txt_repetir'])
{
	if ($usuario->store()){
		$resultado["success"]= true;	
	}
}

else

{

	// si queremos agregar una validacion extra al objeto, lo que hacemos es agregar el ID del error en caso de que no la pase 

	$usuario->error()->addError("MISSMATCH");

}



/* si el store devuelve false, es porque encontr\u00f3 errores de validacion, el metodo ->error()->getErrors() nos dice cuales fueron */

if ($errores = $usuario->error()->getErrors())

{



	// hacemos un array que contenga los posibles errores del objeto, y el campo y mensaje que le corresponden

	$error_codes = array();

	$error_codes['INVALID_PASS'] = array("id"=>"txt_pass","msg"=>"Contrase&ntilde;a inv&aacute;lida (4 a 8 caracteres alfanum&eacute;ricos)");

	$error_codes['MISSMATCH'] =array("id"=>"txt_repetir","msg"=>"Las contrase&ntilde;as no concuerdan");

	$error_codes['INVALID_USER'] =array("id"=>"txt_usuario","msg"=>"El nombre de usuario no es v&aacute;lido");

	$error_codes['INVALID_EMAIL'] = array("id"=>"txt_email","msg"=>"El e-mail ingresado no es v&aacute;lido");

	$error_codes['INVALID_STATUS'] = array("id"=>"cbo_estados","msg"=>"El estado elegido no es v&aacute;lido");

	$error_codes['ALREADY_DELETED'] = array("id"=>"cbo_estados","msg"=>"Este usuario ya se encuentra borrado");

	

	/* asignamos al array de resultado, los mensajes segun lo que viene del objeto */

	foreach ($errores as $error)

	{

		$resultado["errors"][] = $error_codes[$error];

	}



}


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);
