<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if ($perspectivas = oob_perspective::listPerspectives())
{
	$i=0;
	foreach($perspectivas as $p)
	{	
		$return[$i]['id']= $p;
		$return[$i]['name']= $p;
		$return[$i]['path']= $ari->get('filesdir') . DIRECTORY_SEPARATOR . 'perspectives' . DIRECTORY_SEPARATOR . $p ;
		$return[$i]['path2'] = str_replace(DIRECTORY_SEPARATOR,"/",$return[$i]['path']);
		$i++;
	}	
}

$result=array();
$result["totalCount"]=$i;
$result["topics"]=$return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>

