<?php
global $ari;
$ari->popup = 1;

if (!isset($_POST['items'])){
	throw new OOB_Exception_400("La variable [items] no esta definida");
}

if (!isset($_POST['nombre'])) $_POST['nombre'] = "";
if (!isset($_POST['descripcion'])) $_POST['descripcion'] = "";

$resultado = array();

$items = array();
$items = json_decode( $_POST['items'] , true );

$categoria = new contactos_informacion_adicional_categoria();
$categoria->set( 'nombre', $_POST['nombre'] );
$categoria->set( 'descripcion', $_POST['descripcion'] );

	if( $categoria->store() ){
		$resultado["id"] = $categoria->id();
		foreach( $items as $item ){
			$control = new contactos_informacion_adicional_control();
			$control->set( 'tipo', contactos_informacion_adicional_tipo::get_type( $item['xtype'] ) );
			$control->set( 'categoria', $categoria );
			$control->store();
			
			foreach( $item['propiedades'] as $propiedad ){	
				foreach( $propiedad as $key => $value){			
					$propiedad = new contactos_informacion_adicional_control_propiedad();
					$propiedad->set('control',$control);
					$propiedad->set('value',$value );
					$propiedad->set('nombre',$key);
					$propiedad->store();
				}
			
			}//end each
			
			if( isset($item['subitems'])){			
				
				foreach( $item['subitems'] as $subitem ){
				
				if( isset($subitem['name'])){
					$subcontrol = new contactos_informacion_adicional_subcontrol($subitem['name']);					
				}else{
					$subcontrol = new contactos_informacion_adicional_subcontrol();				
				}
				
					$array_subitems[]  = $subitem;
					$subcontrol->set( 'control',$control );					
					$subcontrol->set( 'tipo', $subitem['tipo'] );					
					$subcontrol->store();
					
					$propiedad = new contactos_informacion_adicional_subcontrol_propiedad();
					$propiedad->set( 'subcontrol',$subcontrol );
					$propiedad->set( 'value', $subitem['label'] );
					$propiedad->set( 'nombre', 'label' );
					$propiedad->store();
					
				}
			
			
			
			}
			
		}//end each
		
		//ahora tengo que actualiza para quien es visible la categoria
			
			$filtros2 = false;
			$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );				  
			if( $relaciones = contactos_informacion_adicional_categoria_clases::getFilteredList( false, false, false, false, $filtros2 ) ){				
				foreach( $relaciones as $rel ){
					$rel->delete();
				}				
			}	
			
			if( !isset($_POST['empleado']) ){
				throw new OOB_Exception_400("La variable [empleado] no esta definida");
			}	
			
			if( $_POST['empleado'] == "true"){
			//empleado
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(4));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
			
			if( !isset($_POST['cliente']) ){
				throw new OOB_Exception_400("La variable [cliente] no esta definida");
			}	
			
			if( $_POST['cliente'] == "true"){
			//cliente
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(1));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
			
			if( !isset($_POST['proveedor']) ){
				throw new OOB_Exception_400("La variable [proveedor] no esta definida");
			}	
			
			if( $_POST['proveedor'] == "true"){
			//proveedor
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(2));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
		
	}//end if

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);	

?>