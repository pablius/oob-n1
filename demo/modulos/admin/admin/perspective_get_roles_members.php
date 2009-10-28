<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (isset($_POST['id'])){

$perspective = new oob_perspective ($_POST['id']);
$i = 0;

if($roles = OOB_perspective::listRolesFor($perspective))
{		
	foreach ($roles as $r)
	{
		$return[$i]['id']= $r->get('role');
		$return[$i]['name']= $r->get('name');
		++$i;
	}
	
}//end if

$result=array();
$result["totalCount"]=$i;
$result["topics"]=$return;		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

?>