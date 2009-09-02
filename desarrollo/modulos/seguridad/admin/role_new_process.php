<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPC
// Script que PROCESA LOS DATOS DEL FORM NUEVO  ROL
 
global $ari;
$ari->popup = 1;

$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;
	
$role = new seguridad_role();
		
		if (isset ($_POST['txt_descripcion']))
		{
			$role->set ('description', $_POST['txt_descripcion']);
		}else{
			throw new OOB_Exception_400("La variable [txt_descripcion] no esta definida");
		}
		
		if (isset ($_POST['txt_nombre']))
		{
			$role->set ('name', $_POST['txt_nombre']);	
		}else{
			throw new OOB_Exception_400("La variable [txt_nombre] no esta definida");
		}
		
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
		
		$role->set ('status', USED);
		
	if ($role->store()){
		$resultado["success"] = true;	
		$resultado["id"] = $role->id();
	}
 
	
if ($errores = $ari->error->getErrorsfor("seguridad_role"))
{

   $error_codes = array();
  
   $error_codes['INVALID_NAME'] = array("id"=>"txt_nombre","msg"=>"El Nombre del Rol no es v&aacute;lido.");
   $error_codes['INVALID_DESCRIPTION'] = array("id"=>"txt_descripcion","msg"=>"La descripci&oacute;n es invalida");
   
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);

?>