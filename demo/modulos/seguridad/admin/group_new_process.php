<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que PROCESA LOS DATOS DEL FORM NUEVO  GRUPO
 
global $ari;
$ari->popup = 1;

$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

$group = new seguridad_group();


		
		//DESCRIPCION		
		if (isset ($_POST['txt_descripcion']))
		{
			$group->set ('description',  $_POST['txt_descripcion'] );			
		}else{
			throw new OOB_Exception_400("La variable [txt_descripcion] no esta definida");
		}		
		
		//NOMBRE
		if (isset ($_POST['txt_nombre']))
		{
			$group->set ('name', $_POST['txt_nombre']);	
		}else{
			throw new OOB_Exception_400("La variable [txt_nombre] no esta definida");
		}
		
		//ESTADO
		$group->set ('status', 1);	
	
	//TRATAMOS DE GRABAR
	if ($group->store()){
		$resultado["success"]= true;
		$resultado["id"] = $group->id();		
	}
 
	
if ($errores = $ari->error->getErrorsfor("seguridad_group"))
{

   $error_codes = array();
  
   $error_codes['INVALID_NAME'] = array("id"=>"txt_nombre","msg"=>"El Nombre del Grupo no es v&aacute;lido.");
   $error_codes['DUPLICATE_GROUP'] = array("id"=>"txt_nombre","msg"=>"El Grupo con ese nombre ya existe");
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