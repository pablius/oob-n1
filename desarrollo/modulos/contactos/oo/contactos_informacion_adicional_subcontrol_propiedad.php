<?php

class contactos_informacion_adicional_subcontrol_propiedad extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_subcontrol' 	=> 'object-contactos_informacion_adicional_subcontrol',
			'value' 		=> 'isCorrectLength-0-4000',
			'nombre' 		=> 'isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_subcontrol_propiedad';
	static protected $class = __CLASS__;	
	static $orders = array('status'); 
	
	// definimos los attr del objeto
	public $id_subcontrol;
	public $value;
	public $nombre;
	
	
	public function set_property_value( $subcontrol, $name, $value ){
	
		$obj_property = false;
	
		$filtros[] = array( "field"=>"nombre", "type"=>"list", "value"=>$name );
		$filtros[] = array( "field"=>"subcontrol", "type"=>"list", "value"=>$subcontrol->id() );
		if( $list_propiedades = contactos_informacion_adicional_subcontrol_propiedad::getFilteredList( false , false , false, false, $filtros ) ){
				if( count($list_propiedades) > 0 ){
					$obj_property = $list_propiedades[0];
				}else{
					$obj_property = false;
				}
		}else{
			$obj_property = false;
		}
		
		if($obj_property){
			$obj_property->set('value',$value);
			$obj_property->store();
		}else{
			$obj_property = new contactos_informacion_adicional_subcontrol_propiedad();
			$obj_property->set('nombre', $name );
			$obj_property->set('subcontrol', $subcontrol );
			$obj_property->set('value', $value );
			$obj_property->store();
		}
	
	
	}//end function
	
	public function get_property_value( $subcontrol, $name ){
	
		$obj_property = false;
		
		$filtros[] = array( "field"=>"nombre", "type"=>"list", "value"=>$name );
		$filtros[] = array( "field"=>"subcontrol", "type"=>"list", "value"=>$subcontrol->id() );
		if( $list_propiedades = contactos_informacion_adicional_subcontrol_propiedad::getFilteredList( false , false , false, false, $filtros ) ){
				if( count($list_propiedades) > 0 ){
					$obj_property = $list_propiedades[0]->get('value');				 
				}else{
					$obj_property = false;
				}
		}else{
			$obj_property = false;
		}
	
		return $obj_property;
		
	}//end function
	
}//end class
	
	
?>	