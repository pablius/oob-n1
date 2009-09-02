<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE AREAS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];

//ACTUALIZO  LOS VALORES DE LAS AREAS
if( isset( $_POST['NewsValuesData'] ) ){

$news_values = json_decode( $_POST['NewsValuesData'], true );

foreach( $news_values as $item )
 	{
			if( $item['id'] == '' ){
				$tipo = new contactos_areas();			
			}else{
				$tipo = new contactos_areas( $item['id'] );			
			}
			
			$tipo->set( 'nombre', $item['nombre'] );					
			$tipo->set( 'descripcion', $item['descripcion'] );								
			$tipo->set( 'sucursal', new items_stock_sucursal($item['sucursal']) );								
			$tipo->store();
		
  	}
}

if ( isset($_POST['DeleteData']) ){

$change_status = json_decode( $_POST['DeleteData'], true );


		$ari->db->StartTrans();
		
		foreach( $change_status as $area )
		{
			$obj_area = new contactos_areas( $area['id'] );
			$obj_area->delete();		
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
if( $list_areas = contactos_areas::getFilteredList( (int) $start , (int) $count , false, false, $filtros ) ){
		
		foreach( $list_areas as $area ){			
			
			$return[$i]['id'] = $area->id();		
			$return[$i]['nombre'] = $area->get('nombre');
			$return[$i]['descripcion'] = $area->get('descripcion');						
			$return[$i]['sucursal'] = $area->get('sucursal')->get('nombre');						
			$return[$i]['sucursal::nombre'] = $area->get('sucursal')->id();						
			$i++;
			
		}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_areas::getFilteredListCount( $filtros );
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>
