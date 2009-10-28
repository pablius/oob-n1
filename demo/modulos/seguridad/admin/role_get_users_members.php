<?php

//codigo por jpcoseani
//script que devuelve el listado de usuarios miembros de un rol

global $ari;
$ari->popup = 1; // no mostrar el main_frame 


$i = 0;
$return = array();
$result = array();

if( isset( $_POST['id'] ) ){

$role = new seguridad_role( $_POST['id'] );

if($usuarios = seguridad_role::listUsersFor($role))
		{		
			
			foreach ($usuarios as $u)
			{
				$return[$i]['id']= $u->get('user');
				$return[$i]['uname']= $u->name();
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