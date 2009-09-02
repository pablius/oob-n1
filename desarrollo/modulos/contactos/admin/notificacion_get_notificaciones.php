<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE CONTACTOS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];

if ( isset($_POST['DeleteData']) ){

$change_status = json_decode( $_POST['DeleteData'], true );


		$ari->db->StartTrans();
		
		foreach( $change_status as $notificacion )
		{
			$obj_notificacion = new contactos_notificacion( $notificacion['id'] );
			$obj_notificacion->delete();		
		}
		
		if ($ari->db->CompleteTrans())
		{	
			$ari->clearCache();
		}

}


$filtros = false;

//FILTRO POR COLUMNAS 
if( $_POST['data'] != "" ){

	$filtros = admin_session_state::cache_filters( json_decode( $_POST['data'], true ) );

}


$filtros[] = array("field"=>"status","type"=>"integer","value"=>"1");

//FIN DE FILTROS
$i = 0;
$return = array();

//TRAIGO EL LISTADO DE CONTACTOS
if( $list_notificaciones= contactos_notificacion::getFilteredList( (int) $start , (int) $count , false, false, $filtros ) ){
		
		foreach( $list_notificaciones as $notificacion ){			
			
			$return[$i]['id'] = $notificacion->id();		
			$return[$i]['novedad'] = $notificacion->get('titulo');
			$return[$i]['fecha'] = $notificacion->get('fecha')->format($ari->get("locale")->get('shortdateformat','datetime'));			
			$return[$i]['mensaje'] = $notificacion->get('mensaje');		
			$return[$i]['iscomunicacion'] = ($notificacion->get('iscomunicacion') == '1')?'SI':'NO';	
			$return[$i]['sendmail'] = ($notificacion->get('sendmail') == '1')?'SI':'NO';	
			$return[$i]['receptor'] = $notificacion->get('receptor');			
			$return[$i]['estado'] = ($notificacion->get('status') == '1')?'Activa':'Eliminada';	
			$return[$i]['resultado'] = $notificacion->get('resultado');
					
				$filtrosn = false;			
				$filtrosn[] = array("field"=>"notificacion","type"=>"list","value"=>$notificacion->id() );
				
				//FIN DE FILTROS
				$x = 0;
				$contactos = array();

				//TRAIGO EL LISTADO DE CONTACTOS
				if( $relaciones = contactos_contacto_notificacion::getFilteredList( false ,false ,false ,false , $filtrosn ) ){
						
						foreach( $relaciones as $relacion ){			
							$contacto = $relacion->get('contacto');							
							$contactos[$x]['name'] = $contacto->name();										
							$x++;
							
						}
				}

			$return[$i]['contactos'] = $contactos;		
			
			$i++;
			
		}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_notificacion::getFilteredListCount( $filtros );
$result["topics"]  = $return;

 
//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>