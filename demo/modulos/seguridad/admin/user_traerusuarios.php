<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = 5;
if (!isset($_POST['data'])) $_POST['data'] = "";
if (!isset($_POST['SearchData'])) $_POST['SearchData'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];

//Para cambiar el estado de los usuarios
if (isset($_POST['UpdateStateData'])){

//se decodifica el json en un array y se le pasa al metodo para cambiar el estado a los usuarios
$change_status=json_decode($_POST['UpdateStateData'],true);
oob_user::updateStatusFor ($change_status['items'],$change_status['status']);

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
		case 'list' : 
			$operador_inicio = " IN(";
			$operador_fin = ")";				
		Break;
		
}

$where.=" AND {$filtro['field']} {$operador_inicio}{$filtro['value']}{$operador_fin} "; 

}

}

//FIN DE FILTROS

//FILTRO DE BUSQUEDA POR 3 COLUMNAS 

if($_POST['SearchData']!=""){

$value = $_POST['SearchData'];

$where = " AND (uname LIKE '%$value%' OR email LIKE '%$value%' OR id='$value') ";


}


$return = array();
if ($usuarios = oob_user::search("all",'uname',$where,$start,$count)) 
{
$i=0;
foreach ($usuarios as $u)
	{
		$return[$i]['id']= $u->get('user');
		$return[$i]['uname']= $u->name();
		$return[$i]['email']= $u->get('email');
		$return[$i]['status']= oob_user::getStatus($u->get('status'));
		$i++;
	}
}

$result=array();
$result["totalCount"]=oob_user::searchCount("all",$where);
$result["topics"]=$return;

$fp = fopen("hola.txt","w+");
fwrite($fp,json_encode($result));
fclose($fp); 
 
//RESULTADO
echo json_encode($result);

?>