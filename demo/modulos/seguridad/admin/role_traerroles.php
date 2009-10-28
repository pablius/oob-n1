<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = 5;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];

//Para eliminar Roles
if (isset($_POST['DeleteRolData'])){


//se decodifica el json en un array y hacemos each para recorrer los roles que desea eliminar el usuario
$change_status=json_decode($_POST['DeleteRolData'],true);


		$ari->db->StartTrans();
		
		foreach($change_status as $id_rol)
		{
			$rol = new seguridad_role($id_rol['id']);
			$rol->delete();		
		}
		if ($ari->db->CompleteTrans())
		{	
			$ari->clearCache();
		}

}

//FILTRO POR COLUMNAS 
$where="";

if($_POST['data']!=""){


$filtros=json_decode($_POST['data'],true);

$operadores = array();
$operadores["eq"] = "=";
$operadores["lt"] = "<";
$operadores["gt"] = ">";

foreach($filtros as $filtro)
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

$where.=" AND {$filtro['field']} {$operador_inicio}{$filtro['value']}{$operador_fin} "; 

}

}

//FIN DE FILTROS

$return = array();
if ($roles = seguridad_role::search(DELETED,'name',$where,$start,$count,OPERATOR_DISTINCT)) 
{
$i=0;
foreach ($roles as $r)
	{
		$return[$i]['id']= $r->get('role');
		$return[$i]['name']= $r->get('name');
		$return[$i]['description']= $r->get('description');		
		$i++;
	}
}

$result=array();
$result["totalCount"] = seguridad_role::roleCount("","",$where);
$result["topics"] = $return;

//RESULTADO
echo json_encode($result);

?>