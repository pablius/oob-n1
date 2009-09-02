<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE CONTACTOS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['notificacion'])) $_POST['notificacion'] = "";


$notificacion = $_POST['notificacion'];

$filtros = false;

if( $notificacion != '' ){
	$filtros[] = array("field"=>"notificacion","type"=>"list","value"=>$notificacion );
}


//FIN DE FILTROS
$i = 0;
$return = array();

//TRAIGO EL LISTADO DE CONTACTOS
if( $relaciones = contactos_contacto_notificacion::getFilteredList( false ,false ,false ,false , $filtros ) ){
		
		foreach( $relaciones as $relacion ){			
			$contacto = $relacion->get('contacto');
			$return[$i]['id'] = $contacto->id();		
			$return[$i]['uname'] = $contacto->name();										
			$i++;
			
		}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_contacto_notificacion::getFilteredListCount( $filtros );
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>