<?php

//codigo por jpcoseani
//script que devuelve el listrado de grupos no miembros de un rol

global $ari;
$ari->popup = 1; // no mostrar el main_frame 


$i = 0;
$return = array();

if( isset( $_POST['id'] ) ){

$role = new seguridad_role( $_POST['id'] );

  if( $grupos = seguridad_role::searchNoMembers( '', DELETED,OPERATOR_DISTINCT, $role, GROUP ) )
  {	
			
	foreach ($grupos as $u)
	{
		$return[$i]['id'] = $u->get('group');
		$return[$i]['uname'] = $u->get('name');
		$i++;			
	}			
  }

}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}		
		
$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>