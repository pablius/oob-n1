<?php
global $ari;
$ari->popup = 1;

if( isset($_POST['id']) ){

$id_categoria = $_POST['id'];


if( $obj_categoria = new contactos_informacion_adicional_categoria($id_categoria) ){

	
	$array_relacion = array();
	if( $array_relacion = contactos_informacion_adicional_categoria_clases::getRelated($obj_categoria) ){
		foreach( $array_relacion as $relacion ){
			$relacion->delete();		
		}//end each	
	}//end if

	$array_relacion = array();
	if( $array_relacion = contactos_informacion_adicional_control::getRelated($obj_categoria) ){
		foreach( $array_relacion as $relacion ){
			$relacion->delete();		
		}//end each	
	}//end if

	$obj_categoria->delete();
}

}else{
		throw new OOB_Exception_400("La variable [id] no esta definida");
}

?>