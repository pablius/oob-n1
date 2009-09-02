<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$return = array();	
$i = 0;


$id = 0;
if( isset( $_POST['id'] ) ){
	$id = $_POST['id'];
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$id );
$filtros[] = array( "field"=>"status", "type"=>"list", "value"=>"1" );
if( $lista_direcciones = contactos_direccion::getFilteredList( false, false, false, false, $filtros ) ){
		foreach( $lista_direcciones as $direccion ){			
			$return[$i]['id'] = $direccion->get('id');
			$return[$i]['direccion'] =  $direccion->get('direccion');		
			$return[$i]['extra'] =  $direccion->get('extra');		
			$return[$i]['cp'] =  $direccion->get('cp');			
			$return[$i]['ciudad'] =  $direccion->get('ciudad')->get('name');
			$return[$i]['ciudadname'] =  $direccion->get('ciudad')->get('name');			
			$return[$i]['id_ciudad'] =  $direccion->get('ciudad')->id();
			$return[$i]['tipo'] =  $direccion->get('tipo')->get('detalle');
			$return[$i]['id_tipo'] =  $direccion->get('tipo')->id();
			$i++;
		}					
}


//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_direccion::getFilteredListCount();
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>