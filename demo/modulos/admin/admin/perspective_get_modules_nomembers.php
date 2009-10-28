<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (isset($_POST['id'])){

$perspective = new oob_perspective ($_POST['id']);
$i = 0;

if($modules = OOB_perspective::searchNoMembers("",DELETED,OPERATOR_DISTINCT, $perspective,MODULE))
{		
	foreach ($modules as $m)
	{
		$return[$i]['id']= $m->name();
		$return[$i]['name']= $m->nicename();		
		++$i;
	}
	
}

$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

?>