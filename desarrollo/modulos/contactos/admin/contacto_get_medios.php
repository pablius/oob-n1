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

$filtros[] = array( "field"=>"status", "type"=>"list", "value"=>1);
$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$id );

if( $lista_emails = contactos_medios_contacto::getFilteredList( false, false, false, false, $filtros ) ){
		foreach( $lista_emails as $email ){			
			$return[$i]['id'] = $email->get('id');
			$return[$i]['direccion'] =  $email->get('direccion');
			$return[$i]['tipo'] =  $email->get('tipo')->get('detalle');
			$return[$i]['id_tipo'] =  $email->get('tipo')->id();
			$return[$i]['prefix'] =  $email->get('tipo')->get('prefix');
			$i++;
		}					
}


//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_medios_contacto::getFilteredListCount();
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>