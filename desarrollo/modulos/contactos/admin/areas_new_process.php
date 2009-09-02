<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE PROCESA LOS DATOS DEL FORM NUEVA AREA

global $ari;
$ari->popup = 1;

//ARRAY PARA LOS RESULTADO
$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

//CREO EL OBJETO
$area = new contactos_areas();

//PASO LOS DATOS DEL FORMULARIO A VARIABLES 
	
	

	//NOMBRE
	$nombre = '';
	if( isset( $_POST['txt_nombre'] ) && !empty( $_POST['txt_nombre']) ){
		$nombre = $_POST['txt_nombre'];
	}
	
	//DESCRIPCION
	$descripcion = '';
	if( isset( $_POST['txt_descripcion'] ) && !empty( $_POST['txt_descripcion']) ){
		$descripcion = $_POST['txt_descripcion'];
	}
	


	
	//SETEO LOS VALORES	
	$area->set('nombre', $nombre );
	$area->set('descripcion', $descripcion );
			
	if( $area->store() ){
			$resultado["success"] = true;
	}
	
	




//ERRORES
if ( $errores = $area->error()->getErrors() )
{

    
   $error_codes = array();
   $error_codes['NO_NOMBRE'] = array( "id"=>"txt_nombre" , "msg"=>"Debe ingresar un nombre" );
   $error_codes['NO_DESCRIPCION'] = array( "id"=>"txt_descripcion" , "msg"=>"Debe ingresar una descripci&oacute;n" );
   

  
  
   
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}	

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);

?>