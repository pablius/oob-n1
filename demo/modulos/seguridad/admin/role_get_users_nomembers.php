<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$result = array();

if (isset($_POST['id'])){
	$role = new seguridad_role($_POST['id']);


//FILTRO
$value = "";
if(isset( $_POST['query'] )){
	$value = $_POST['query'];
}

if( isset($_POST['ToData']) ){

//USUARIOS
$miebros_usuarios = split("," , $_POST['ToData']);
$real_members_users = array();

if($usuarios = seguridad_role::listUsersFor($role))
		{		
			
			foreach ($usuarios as $u)
			{
				$real_members_users[]=$u->get('user');		
			}			
		}
		
		
		for($i=0;$i<count($real_members_users);$i++){
		if (!in_array($real_members_users[$i], $miebros_usuarios)){
			$tmpUser = new oob_user($real_members_users[$i]);
  			$role->removeUser($tmpUser);
		}
		}
		
		for($i=0;$i<count($miebros_usuarios);$i++){
		if (!in_array($miebros_usuarios[$i], $real_members_users)){
			$tmpUser = new oob_user($miebros_usuarios[$i]);
  			$role->addUser($tmpUser);			
		}			
		}
		
}			

$return = array();
$i = 0;
if( $value != "" ){

 if($usuarios = seguridad_role::searchNoMembers( $value, DELETED, OPERATOR_DISTINCT, $role, USER ) )
 {			
	foreach ($usuarios as $u)
	{
		$return[$i]['id'] = $u->get('user');
		$return[$i]['uname'] = $u->name();
		$i++;
	}			
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
