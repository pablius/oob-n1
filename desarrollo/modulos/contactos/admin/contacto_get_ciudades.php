<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['query'])) $_POST['query'] = "";


$start =  $_POST["start"];
$count =  $_POST["limit"];

$filtros = false;


$filtros[] = array( "field"=>"realname", "type"=>"String", "value"=>$_POST['query']);

	$return = array();
	$i = 0;
	

	if( $lista_ciudades = address_city::getFilteredList( (int) $start , (int) $count, false, false, $filtros  ) ){		
			foreach ( $lista_ciudades as $ciudad  ){
				$return[$i]['id'] = $ciudad->id();
				$return[$i]['name'] = $ciudad->get('name');
				$i++;
			}
	}
	
	
//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = address_city::getFilteredListCount($filtros);
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);	
	
?>