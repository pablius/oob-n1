<?php

//codigo por jpcoseani
//script que devuelve el listado de grupos

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];

//Para eliminar grupos
if ( isset($_POST['DeleteGroupData']) ){


//se decodifica el json en un array y hacemos each para recorrer los roles que desea eliminar el usuario
$delete_items =  json_decode( $_POST['DeleteGroupData'] , true ) ;


		$ari->db->StartTrans();
		
		foreach( $delete_items as $id_grupo )
		{
			$grupo = new seguridad_group( $id_grupo['id'] );
			$grupo->delete();		
		}
		if ($ari->db->CompleteTrans())
		{	
			$ari->clearCache();
		}

}

//FILTRO POR COLUMNAS 
$where = "";

if( $_POST['data'] != "" ){

		$filtros = admin_session_state::cache_filters( json_decode( $_POST['data'], true) );
		
		$operadores = array();
		$operadores["eq"] = "=";
		$operadores["lt"] = "<";
		$operadores["gt"] = ">";

		foreach( $filtros as $filtro )
		{

		switch( $filtro['type'] ){		
				
				case "string":				
					$operador_inicio = " LIKE '%";
					$operador_fin = "%'";
				Break;
				case "numeric":
					$operador_inicio = $operadores[$filtro['comparison']];
					$operador_fin = "";		
				Break;
						
		}

		$where.= " AND {$filtro['field']} {$operador_inicio}{$filtro['value']}{$operador_fin} "; 

		}

}//end if

//FIN DE FILTROS
$return = array();
$i = 0;

if( $groups = seguridad_group::search( DELETED, 'name', $where, $start, $count, OPERATOR_DISTINCT )) 
{

foreach( $groups as $g )
	{
		$return[$i]['id'] = $g->get('group');
		$return[$i]['name'] = $g->get('name');
		$return[$i]['description'] = $g->get('description');		
		$i++;
	}
}

$result = array();
$result["totalCount"] = seguridad_group::groupCount( "", "", $where);
$result["topics"] = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>