<?php

//codigo por jpcoseani
//script que devuelve el listado de usuarios miembros de un grupo determinado

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$i = 0;
$result = array();
$return = array();

if( isset($_POST['id']) ){

$grupo = new seguridad_group($_POST['id']);

if( $usuarios = seguridad_group::listUsersFor($grupo) )
		{		
			
			foreach( $usuarios as $u )
			{
				$return[$i]['id'] = $u->get('user');
				$return[$i]['uname'] = $u->get('uname');
				$i++;			
			}			
		}


$result["totalCount"] = $i;
$result["topics"] = $return;		

}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>