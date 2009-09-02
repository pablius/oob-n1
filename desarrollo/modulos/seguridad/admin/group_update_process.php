<?php

#OOB/N1 Framework [2008 - Nutus] - PM 
// Codigo por JPC

// Script que procesa los datos del FORM UPDATE GROUP

global $ari;
$ari->popup = 1;


$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

//id
if( isset( $_POST['id'] ) ){
	if (OOB_validatetext :: isNumeric($_POST['id']))
	{
		$grupo = new seguridad_group ($_POST['id']);
		
	}else
	{
		throw new OOB_Exception_400("La variable [id] es invalida");
	}
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

//nombre
if( isset($_POST['txt_nombre']) ){
	$grupo->set('name', $_POST['txt_nombre']);
}else{
	throw new OOB_Exception_400("La variable [txt_nombre] no esta definida");
}

//descripcion
if( isset($_POST['txt_descripcion']) ){
	$grupo->set('description', $_POST['txt_descripcion']);
}else{
	throw new OOB_Exception_400("La variable [txt_descripcion] no esta definida");
}

//estado
$grupo->set ('status', 1);


if( !isset($_POST['usuarios']) ){
	throw new OOB_Exception_400("La variable [usuarios] no esta definida");
}

// tratamos de grabar 

	if ($grupo->store()){
		$resultado["success"]= true;	
		
			//usuarios
			$miebros = split("," , $_POST['usuarios']);
			

		$real_members = array();	
		if( $usuarios = seguridad_group::listUsersFor($grupo) )
		{		
			
			foreach ($usuarios as $u)
			{
				$real_members[] = $u->get('user');		
			}			
		}
		
		
		for($i=0;$i<count($real_members);$i++){
		if (!in_array($real_members[$i], $miebros)){
			$tmpUser = new oob_user($real_members[$i]);
  			$grupo->removeUser($tmpUser);
		}
		}
		
		for($i=0;$i<count($miebros);$i++){
		if (!in_array($miebros[$i], $real_members)){
			$tmpUser = new oob_user($miebros[$i]);
  			$grupo->addUser($tmpUser);			
		}			
		}
				
		
	}



if ($errores = $ari->error->getErrorsfor("seguridad_group"))

{


	$error_codes = array();

	$error_codes['INVALID_NAME'] = array("id"=>"txt_nombre","msg"=>"El nombre del grupo es invalido");

	$error_codes['INVALID_DESCRIPTION'] =array("id"=>"txt_descripcion","msg"=>"La descripci&oacute;n del grupo es invalida");

	$error_codes['DUPLICATE_GROUP'] =array("id"=>"txt_nombre","msg"=>"El grupo ya existe");

	
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