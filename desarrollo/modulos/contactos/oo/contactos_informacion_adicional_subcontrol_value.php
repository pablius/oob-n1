<?php
class contactos_informacion_adicional_subcontrol_value extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_subcontrol' 	=> 'object-contactos_informacion_adicional_subcontrol',
		'id_contacto' 		=> 'object-contactos_contacto',
		'value' 			=> 'isClean,isCorrectLength-0-4000'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_subcontrol_value';
	static protected $class = __CLASS__;	
	static $orders = array('status'); 
	
	// definimos los attr del objeto
	public $id_control;
	public $id_contacto;
	public $value;
	
	public function set_control_value( $contacto, $subcontrol, $value ){
	
	$obj_control = false;
	
	$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );
	$filtros[] = array( "field"=>"subcontrol", "type"=>"list", "value"=>$subcontrol->id() );
	if( $list_controles = contactos_informacion_adicional_subcontrol_value::getFilteredList( false , false , false, false, $filtros ) ){
			if( count($list_controles) > 0 ){
				$obj_control = $list_controles[0];
			}else{
				$obj_control = false;
			}
	}else{
		$obj_control = false;
	}
	
	if($obj_control){
		$obj_control->set('value',$value);
		$obj_control->store();
	}else{
		$obj_control = new contactos_informacion_adicional_subcontrol_value();
		$obj_control->set('contacto',$contacto);
		$obj_control->set('subcontrol', $subcontrol);
		$obj_control->set('value',$value);
		$obj_control->store();
	}
	
	}//end function
	
	public function get_control_value( $contacto, $subcontrol ){

		$return = "";
		
		$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );
		$filtros[] = array( "field"=>"subcontrol", "type"=>"list", "value"=>$subcontrol->id() );
		if( $list_controles = contactos_informacion_adicional_subcontrol_value::getFilteredList( false , false , false, false, $filtros ) ){
				if( count($list_controles) > 0 ){
					$return = $list_controles[0]->get('value');
				}else{
					$return = "";
				}
		}else{
			$return = "";
		}
		
		return $return;
	
	}//end function
	
}
?>