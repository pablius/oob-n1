<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE CONTACTOS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['id'])) $_POST['id'] = "";

$id =  $_POST["id"];
$start =  $_POST["start"];
$count =  $_POST["limit"];



$filtros = false;

if( $id != "" ){
	$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$id );
}


$filtros[] = array("field"=>"status","type"=>"integer","value"=>"1");

//FIN DE FILTROS
$i = 0;
$return = array();

//TRAIGO EL LISTADO DE CONTACTOS
if( $list_relaciones = contactos_contacto_notificacion::getFilteredList( (int) $start , (int) $count ,false,false,$filtros ) ){
		
		foreach( $list_relaciones as $relacion ){			
			
			$notificacion = $relacion->get('notificacion');
			$return[$i]['id'] = $notificacion->id();		
			$return[$i]['novedad'] = $notificacion->get('titulo');
			$return[$i]['fecha'] = $notificacion->get('fecha')->format($ari->get("locale")->get('shortdateformat','datetime'));			
			$return[$i]['mensaje'] = $notificacion->get('mensaje');		
			$return[$i]['iscomunicacion'] = ($notificacion->get('iscomunicacion') == '1')?'SI':'NO';	
			$return[$i]['sendmail'] = ($notificacion->get('sendmail') == '1')?'SI':'NO';	
			$return[$i]['receptor'] = $notificacion->get('receptor');
			$return[$i]['estado'] = ($notificacion->get('status') == '1')?'Activa':'Eliminada';	
			$return[$i]['resultado'] = $notificacion->get('resultado');						
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