<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$result = array();

if (isset($_POST['id'])){


$grupo = new seguridad_group($_POST['id']);

//FILTRO
$value = "";
if(isset( $_POST['query'] ) ){
	$value = $_POST['query'];
}

if(isset( $_POST['ToData'] )){

//ACTUALIZO LOS USUARIOS
$miebros=split("," , $_POST['ToData']);
$real_members = array();

if($usuariosm = seguridad_group::listUsersFor($grupo))
		{		
			
			foreach ($usuariosm as $um)
			{
				$real_members[]=$um->get('user');		
			}			
		}
		
		
		for($i=0;$i<count($real_members);$i++){
		if (!in_array($real_members[$i], $miebros)){
			$tmpUser = new oob_user($real_members[$i]);
  			$grupo->removeUser($tmpUser);
		}
		}
		
		for($i=0;$i<count($miebros);$i++){
		if (!in_array($miebros[$i], $real_members)){
			$tmpUser = new oob_user($miebros[$i]);
  			$grupo->addUser($tmpUser);			
		}			
		}
}		
//FIN ACTUALIZACION		



$return = array();
$i = 0;

if( $value != "" ){

	if($usuarios = seguridad_group::searchNoMembers( $value ,DELETED,OPERATOR_DISTINCT,$grupo->get("group")))
	{	
			
		foreach ($usuarios as $u)
		{			
			$return[$i]['id'] = $u->get('user');
			$return[$i]['uname'] = $u->name();
			$i++;			
				
		}//end each			
	}//end if

}//end if


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