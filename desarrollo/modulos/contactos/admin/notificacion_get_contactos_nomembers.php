<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE CONTACTOS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

//FILTRO
$value = "";
if(isset( $_POST['query'] )){
	$value = $_POST['query'];
	
	if( trim($value) != "" ){
		$filtros[] = array("field"=>"apellido","type"=>"string","value"=>$value);
		$filtros[] = array("field"=>"nombre","type"=>"string","value"=>$value,"connector"=>"OR");
	}
	
}

$filtros[] = array("field"=>"status","type"=>"integer","value"=>"1");

//FIN DE FILTROS
$i = 0;
$return = array();

//TRAIGO EL LISTADO DE CONTACTOS
if( $list_contactos = contactos_contacto::getFilteredList( false , false , false, false, $filtros ) ){
		
		foreach( $list_contactos as $contacto ){					
			$return[$i]['id'] = $contacto->id();		
			$return[$i]['uname'] = $contacto->name();		
			$i++;			
		}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_contacto::getFilteredListCount( $filtros );
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>