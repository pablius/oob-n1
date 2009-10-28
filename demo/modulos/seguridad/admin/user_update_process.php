<?php

#OOB/N1 Framework [2008 - Nutus] - PM 
// Codigo por JPC

// Script que procesa los datos del FORM MI CUENTA

global $ari;
$ari->popup = 1;

$resultado=array();
$resultado["errors"]=array();
$resultado["success"] = false;

//id
if( isset($_POST['id']) ){

	if (OOB_validatetext :: isNumeric( $_POST['id']) )
	{
		$usuario = new oob_user( $_POST['id'] );
	}
	else
	{
		throw new OOB_exception("INVALID_ID_VALUE", "501", "INVALID_ID_VALUE", false);
	}
	
}
else
{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}


/* Asignamos los valores al objeto directamente del formulario */
// password
if( isset($_POST['txt_pass']) ){
	if ( $_POST['txt_pass'] != "" ) // solo lo asignamos si escribi\u00f3 algo
	{
		$usuario->set ('password', $_POST['txt_pass']);
	}
}else{
		throw new OOB_Exception_400("La variable [txt_pass] no esta definida");
}

// email
if( isset($_POST['txt_email']) ){
	$usuario->set ('email', $_POST['txt_email']);
}else{
		throw new OOB_Exception_400("La variable [txt_email] no esta definida");
}

// status
if( isset($_POST['cbo_estados']) ){
	$id_status = oob_user::getStatus($_POST['cbo_estados'], false);
	$usuario->set ('status', $id_status );
}else{
		throw new OOB_Exception_400("La variable [cbo_estados] no esta definida");
}

if( !isset($_POST['txt_repetir']) ){
	throw new OOB_Exception_400("La variable [txt_repetir] no esta definida");
}

// tratamos de grabar si puso los dos pass iguales

if ($_POST['txt_pass'] === $_POST['txt_repetir'])
{
	if( $usuario->store() )
	{
		$resultado["success"]= true;	
	}
}
else
{

	// si queremos agregar una validacion extra al objeto, lo que hacemos es agregar el ID del error en caso de que no la pase 
	$usuario->error()->addError("MISSMATCH");

}



/* si el store devuelve false, es porque encontr\u00f3 errores de validacion, el metodo ->error()->getErrors() nos dice cuales fueron */

if( $errores = $usuario->error()->getErrors() )
{



	// hacemos un array que contenga los posibles errores del objeto, y el campo y mensaje que le corresponden

	$error_codes = array();

	$error_codes['INVALID_PASS'] = array("id"=>"txt_pass","msg"=>"Contrase&ntilde;a inv&aacute;lida (4 a 8 caracteres alfanum&eacute;ricos)");

	$error_codes['MISSMATCH'] = array("id"=>"txt_repetir","msg"=>"Las contrase&ntilde;as no concuerdan");

	$error_codes['INVALID_USER'] = array("id"=>"txt_usuario","msg"=>"El nombre de usuario no es v&aacute;lido");

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

?>