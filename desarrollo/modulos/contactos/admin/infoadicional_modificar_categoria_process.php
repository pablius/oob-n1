<?php

if (!isset($_POST['id'])){
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

if (!isset($_POST['empleado'])) $_POST['empleado'] = "false";
if (!isset($_POST['cliente'])) $_POST['cliente'] = "false";
if (!isset($_POST['proveedor'])) $_POST['proveedor'] = "false";
if (!isset($_POST['items'])) $_POST['items'] = "";
if (!isset($_POST['nombre'])) $_POST['nombre'] = "";
if (!isset($_POST['descripcion'])) $_POST['descripcion'] = "";

$items = array();
$items = json_decode( $_POST['items'] , true );
$array_subitems  = array();

$categoria = new contactos_informacion_adicional_categoria($_POST['id']);
$categoria->set( 'nombre', $_POST['nombre'] );
$categoria->set( 'descripcion', $_POST['descripcion'] );


	if( $categoria->store() ){
		foreach( $items as $item ){
			//si viene el nombre del control
			if( isset($item['name'])){
				$control = new contactos_informacion_adicional_control($item['name']);				
			}else{
				$control = new contactos_informacion_adicional_control();			
			}	
			
				$control->set( 'tipo', contactos_informacion_adicional_tipo::get_type( $item['xtype'] ) );
				$control->set( 'categoria', $categoria );
				$control->store();
			
						
			 foreach( $item['propiedades'] as $propiedad ){	
				foreach( $propiedad as $key => $value){			
					contactos_informacion_adicional_control_propiedad::set_property_value( $control, $key, nl2br($value) );					
				}//end each			
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
			
			
			
			}//end if ( si viene subitems )
			
		}//end each
		
		
		$l = "";
			$filtros2 = false;
			$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );				  
			if( $controles = contactos_informacion_adicional_control::getFilteredList( false, false, false, false, $filtros2 ) ){
				
				foreach( $controles as $control ){
					
					$esta = false;
					//busco el control
					foreach( $items as $item ){
						if(isset($item['name'])){
							if( $item['name'] == $control->id() ){
								$esta = true;
							}
						}else{
							$esta = true;
						}	
						
					}
					
					if(!$esta){
						$control->delete();
					}//end if
					
					
					$filtros5 = false;
								$filtros5[] = array( "field"=>"control", "type"=>"list", "value"=>$control->id() );				  
								if( $subcontroles = contactos_informacion_adicional_subcontrol::getFilteredList( false, false, false, false, $filtros5 ) ){
									
									foreach( $subcontroles as $objsubcontrol ){
									
										$esta2 = false;										
										foreach( $array_subitems as $subitem ){
										
																		
											if(isset($subitem['name'])){
												if( $subitem['name'] == $objsubcontrol->id() ){
													$esta2 = true;
												}
											}else{
												$esta2 = true;
											}	
										}	
										
										if(!$esta2){
											$objsubcontrol->delete();
										}//end if												
									}
								}
					
			
				}//end each controles
				
			}
			//end actualizacion de controles

			//ahora tengo que actualiza para quien es visible la categoria
			
			$filtros2 = false;
			$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );				  
			if( $relaciones = contactos_informacion_adicional_categoria_clases::getFilteredList( false, false, false, false, $filtros2 ) ){				
				foreach( $relaciones as $rel ){
					$rel->delete();
				}				
			}	
			
			if( $_POST['empleado'] == "true"){
			//empleado
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(4));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
			
			if( $_POST['cliente'] == "true"){
			//cliente
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(1));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
			
			if( $_POST['proveedor'] == "true"){
			//proveedor
			$rel = new contactos_informacion_adicional_categoria_clases();
			$rel->set( 'clase', new contactos_clase(2));
			$rel->set( 'categoria', $categoria );
			$rel->store();
			}
			
	}//end if
	
?>