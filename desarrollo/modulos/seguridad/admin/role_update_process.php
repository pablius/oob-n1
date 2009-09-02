<?php

#OOB/N1 Framework [2008 - Nutus] - PM 
// Codigo por JPCOSEANI

// Script que procesa los datos del FORM UPDATE  ROLE

global $ari;
$ari->popup = 1;


$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

if( isset($_POST['id']) ){

//id
if (OOB_validatetext :: isNumeric( $_POST['id'] ))
{
	$role = new seguridad_role ( $_POST['id'] );
	
}else
{
	throw new OOB_exception("INVALID_ID_VALUE", "501", "INVALID_ID_VALUE", false);	
}


//nombre
$role->set('name', $_POST['txt_nombre']);

//descripcion
$role->set('description', $_POST['txt_descripcion']);

//anonimo

if( isset($_POST['chk_anonimo']) ){
	$role->set ('anonymous', ANONIMO);
}else
{
	$role->set ('anonymous', NO_ANONIMO);
}

//confiable
if( isset($_POST['chk_confiados']) ){
	$role->set ('trustees', YES);
}else
{
	$role->set ('trustees', NO);
}



//estado
$role->set ('status', USED);




// TRATAMOS DE GRABAR

	if ($role->store()){
	
		$ari->clearCache("menu");
		$ari->clearCache("db");
		$resultado["success"]= true;

		
if( isset($_REQUEST['usuarios']) ){

//USUARIOS
$miebros_usuarios = split("," , $_POST['usuarios']);
$real_members_users = array();

if($usuarios = seguridad_role::listUsersFor($role))
		{		
			
			foreach ($usuarios as $u)
			{
				$real_members_users[]=$u->get('user');		
			}			
		}
		
		
		for($i=0;$i<count($real_members_users);$i++){
		if (!in_array($real_members_users[$i], $miebros_usuarios)){
			$tmpUser = new oob_user($real_members_users[$i]);
  			$role->removeUser($tmpUser);
		}
		}
		
		for($i=0;$i<count($miebros_usuarios);$i++){
		if (!in_array($miebros_usuarios[$i], $real_members_users)){
			$tmpUser = new oob_user($miebros_usuarios[$i]);
  			$role->addUser($tmpUser);			
		}			
		}
		
}				

if( isset($_POST['grupos']) ){
		
//GRUPOS
$miebros_grupos = split("," , $_POST['grupos']);
$real_members_groups = array();

if($grupos = seguridad_role::listGroupsFor($role))
		{		
			
			foreach ($grupos as $g)
			{
				$real_members_groups[] = $g->get('group');		
			}			
		}
		
		
		for($i=0;$i<count($real_members_groups);$i++){
		if (!in_array($real_members_groups[$i], $miebros_grupos)){
				$tmpGroup = new seguridad_group($real_members_groups[$i]);
				$role->removeGroup($tmpGroup);			
		}
		}
		
		for($i=0;$i<count($miebros_grupos);$i++){
		if (!in_array($miebros_grupos[$i], $real_members_groups)){
			$tmpGroup = new seguridad_group($miebros_grupos[$i]);
			$role->addGroup($tmpGroup);			
		}			
		}		

		
}	


//MODULOS
if( isset($_REQUEST['modulos']) ){


//ARRAY QUE TIENE LOS CAMBIOS HECHOS EN EL TREEPANEL
$selected = split("," , $_REQUEST['modulos']);



$v="";
//HAGO LOS CAMBIOS
for($i=0;$i<count($selected);$i++){
		$id = split( "_" , $selected[$i] );
		
		//si es modulo
		if( $id[0] == "m" ){
		
		$tmpModule = new OOB_module($id[1]);
							
			if( $selected[$i+1] == "true" )	{
				$role->addModule($tmpModule); //agrego el modulo
			}else{			
				$role->removeModule($tmpModule); //elimino el modulo
			}
		}	
			
		if( $id[0] == "a" ){	
		
		$tmpAction = new seguridad_action($id[1]);
		
			if( $selected[$i+1] == "true" )	{
				$role->addAction($tmpAction);//agrego la accion
			}else{			
				$role->removeAction($tmpAction); //elimino el modulo
			}
		}
		
		$i++;
		}
		
}
	
		
	}



if ($errores = $ari->error->getErrorsfor("seguridad_role"))

{


	$error_codes = array();

	$error_codes['INVALID_NAME'] = array("id"=>"txt_nombre","msg"=>"El nombre del grupo es invalido");

	$error_codes['INVALID_DESCRIPTION'] =array("id"=>"txt_descripcion","msg"=>"La descripci&oacute;n del grupo es invalida");

	$error_codes['DUPLICATE_ROLE'] =array("id"=>"txt_nombre","msg"=>"El grupo ya existe");

	
	foreach ($errores as $error)

	{

		$resultado["errors"][] = $error_codes[$error];

	}



}

}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);



?>