<?php

//codigo por jpcoseani.
//script que devuelve el listado de permisos

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start = $_POST['start'];
$limit = $_POST['limit'];

//ACTUALIZO  LOS VALORES DE LOS DEPOSITOS
if( isset( $_POST['NewsValuesData'] ) ){

$news_values = json_decode( $_POST['NewsValuesData'], true );

foreach( $news_values as $item )
 	{
			if( $item['id'] == '' ){
				$deposito = new items_stock_deposito();			
			}
			else
			{
				$deposito = new items_stock_deposito( $item['id'] );
			}
			
		
			$deposito->set( 'nombre', $item['nombre'] );
			$deposito->set( 'contacto', new contactos_contacto($item['contacto']) );				
			$deposito->set( 'sucursal', new items_stock_sucursal($item['sucursal']) );				
			$deposito->store();		
  	}
}




//BORRAR SUCURSALES
if ( isset( $_POST['DeleteData'] ) ){

$delete_values = json_decode( $_POST['DeleteData'] , true );

foreach($delete_values as $item)
 	{	
		$deposito = new items_stock_deposito( $item['id'] );
		$deposito->delete();			
		
	}	

}//end delete impuestos


//FILTRO POR COLUMNAS 
$filtros = false;

if( $_POST['data'] != "" ){

	$filtros = admin_session_state::cache_filters( json_decode( $_POST['data'], true) );

}


//FIN DE FILTROS	
$i = 0;
$return = array();

if(!$filtros){
	$filtros[] = array("field"=>"status","type"=>"list","value"=>"1");
}

if( $list_permisos = seguridad_permission::listPermission() )		
{	
	foreach( $list_permisos as $permiso )
	{				
		$return[$i]['id'] = $permiso->get('permission');
		$return[$i]['nombre'] = $permiso->get('name');
		$return[$i]['descripcion'] = $permiso->get('nicename');		
		// if($modulo = new OOB_module($permiso->get('modulename'))){
			// $return[$i]['module'] = $modulo->name(); // es como el id		
			// $return[$i]['modulenicename'] = $modulo->nicename(); //detalle
		// }
		$i++;
	}//end each	
}//end if



$result = array();
$result["totalCount"] = seguridad_permission::permissionCount();
$result["topics"] = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>