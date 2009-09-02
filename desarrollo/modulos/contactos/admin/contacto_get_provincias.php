<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['query'])) $_POST['query'] = "";

if (!isset($_POST['id'])){
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

$start =  $_POST["start"];
$count =  $_POST["limit"];
$id = $_POST['id'];

$filtros = false;
$hab = false;

if( $id != "" ){
	$filtros[] = array( "field"=>"country", "type"=>"list", "value"=>$id );
	$hab = true;
}

$filtros[] = array( "field"=>"name", "type"=>"String", "value"=>$_POST['query']);

	$return = array();
	$i = 0;
	
if($hab){	
	if( $lista_provincias = address_state::getFilteredList( (int) $start , (int) $count, false, false, $filtros ) ){		
			foreach ( $lista_provincias as $provincia  ){
				$return[$i]['id'] = $provincia->id();
				$return[$i]['name'] = $provincia->get('name');
				$i++;
			}
	}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = address_state::getFilteredListCount($filtros);
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);
	
?>