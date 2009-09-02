<?php
//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE PROVEEDORES

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['query'])) $_POST['query'] = "";
if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;

//filtro por aptos para venta

$filtros[] = array( "field"=>"clase", "type"=>"list", "value"=>"2");
$filtros[] = array( "field"=>"apellido", "type"=>"String", "value"=>$_POST['query']);

//los que estan activos
$filtros[] = array( "field"=>"status", "type"=>"list", "value"=>"1");

$return = array();
$i = 0;
if( $lista_contactos = contactos_contacto::getFilteredList( (int) $_POST['start'] , (int) $_POST['limit'] , false, false, $filtros ) ){		
			foreach( $lista_contactos as $contacto ){					
					$return[$i]['id'] = $contacto->id();
					$return[$i]['name'] = $contacto->name();	
					$return[$i]['anticipos'] = movimientos_anticipo::total_anticipos( $contacto->id() )->getPrintable();
					$return[$i]['anticipos_value'] = movimientos_anticipo::total_anticipos( $contacto->id() )->get('value');					
					$i++;					
			}
$i = contactos_contacto::getFilteredListCount( $filtros );		
			
}

$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>