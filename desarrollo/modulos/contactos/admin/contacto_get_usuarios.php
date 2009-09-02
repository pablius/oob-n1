<?php
global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['id'])) $_POST['id'] = '';
if (!isset($_POST['query'])) $_POST['query'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];


$id = $_POST['id'];



	$return = array();
	$i = 0;		
	
	if( $id != '' ){
		if( $contacto = new contactos_contacto($id) ){
			$return[$i]['id'] = $contacto->get('usuario')->get('user');
			$return[$i]['name'] = $contacto->get('usuario')->name();	
			$i = 1;
		}
	}
	
	$where = false;
	if( $_POST['query'] != '' ){	
		$where = " u.uname like '%" . $_POST['query'] . "%'";
	}
	
	if( $lista_usuarios = contactos_contacto::listNoAssigned( null , $start, $count, $where ) ){		
			foreach ( $lista_usuarios as $usuario  ){
				$return[$i]['id'] = $usuario->get('user');
				$return[$i]['name'] = $usuario->name();	
				$i++;
			}
	}
	
	
	$x = 0;
	if( $lista_usuarios = contactos_contacto::listNoAssigned(false,false,false,$where) ){		
			foreach ( $lista_usuarios as $usuario  ){
				$x++;
			}
	}
	
//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = $x;
$result["topics"]  = $return;

 
//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);	


?>