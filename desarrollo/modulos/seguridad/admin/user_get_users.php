<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if( !isset( $_POST['start'] )) $_POST['start'] = 0;
if( !isset( $_POST['limit'] )) $_POST['limit'] = PAGE_SIZE ;
if( !isset( $_POST['data'] )) $_POST['data'] = "";
if( !isset( $_POST['query'] )) $_POST['query'] = "";

$start =  $_POST["start"];
$count =   $_POST["limit"]; 

//Para eliminar Usuarios
if ( isset($_POST['DeleteUserData']) ){


//se decodifica el json en un array y hacemos each para recorrer los roles que desea eliminar el usuario
$change_status = json_decode( $_POST['DeleteUserData'], true );


		$ari->db->StartTrans();
		
		foreach( $change_status as $id_user )
		{
			$user = new oob_user($id_user['id']);
			$user->delete();		
		}
		if ($ari->db->CompleteTrans())
		{	
			$ari->clearCache();
		}

}


//Para cambiar el estado de los usuarios
if( isset( $_POST['UpdateStateData'] ) )
{
	//se decodifica el json en un array y se le pasa al metodo para cambiar el estado a los usuarios
	$change_status = json_decode( $_POST['UpdateStateData'] , true );
	oob_user::updateStatusFor( $change_status['items'] , $change_status['status'] );
	
}//end if




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
					case 'list' : 
						$operador_inicio = " IN(";
						$operador_fin = ")";				
					Break;
					
			}//end switch

			$where.=" AND {$filtro['field']} {$operador_inicio}{$filtro['value']}{$operador_fin} "; 

		}//end each

	

}//end if

#fin de filtros


//FILTRO DE BUSQUEDA POR 3 COLUMNAS 
if( trim( $_POST['query'] )!= "" ){

	$value = $_POST['query'];
	$where = " AND (uname LIKE '%$value%' OR email LIKE '%$value%' OR id='$value') ";

}

if( trim($where) == '' ){
	$where = " AND status = 1 ";
}

$return = array();
$i = 0;
if( $usuarios = oob_user::search( 'all','uname', $where, $start, $count) ) 
{
foreach ($usuarios as $u)
	{
		$return[$i]['id']= $u->get('user');
		$return[$i]['uname']= $u->get('uname');
		$return[$i]['email']= $u->get('email');
		$return[$i]['status']= oob_user::getStatus( $u->get('status') );
		$i++;
	}
}

$result = array();
$result["totalCount"] = oob_user::searchCount( "all", $where );
$result["topics"] = $return;


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>