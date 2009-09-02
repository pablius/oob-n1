<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE EL LISTADO DE CONTACTOS

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if (!isset($_POST['start'])) $_POST['start'] = 0;
if (!isset($_POST['limit'])) $_POST['limit'] = PAGE_SIZE ;
if (!isset($_POST['data'])) $_POST['data'] = "";

$start =  $_POST["start"];
$count =  $_POST["limit"];


if ( isset($_POST['DeleteData']) ){

$change_status = json_decode( $_POST['DeleteData'], true );


		$ari->db->StartTrans();
		
		foreach( $change_status as $contacto )
		{
			if( $obj_contacto = new contactos_contacto( $contacto['id'] ) ){
				$usuario = $obj_contacto->get('usuario');
				if( !$obj_contacto->facturas_pendientes() ){
					if( $obj_contacto->delete() ){
						$usuario->delete();
					}
				}	
			}
		}
		if ($ari->db->CompleteTrans())
		{	
			$ari->clearCache();
		}

}


$filtros = false;
$filtros_adicionales = array() ;



//FILTRO POR COLUMNAS 


if( $_POST['data'] != "" ){

	$pre_filtros = admin_session_state::cache_filters( json_decode( $_POST['data'], true ) );
	
	
foreach( $pre_filtros as $filtro ){
	if( str_replace( "infoadicional_", "", $filtro['field'] ) != $filtro['field'] ){
		if( !isset( $filtro["comparison"] ) ){
			$filtro["comparison"] = "";
		}
		$filtros_adicionales[] = array( "id" 	=> str_replace( "infoadicional_", "", $filtro['field'] ),
										"value" => $filtro['value'],
										"type"  => $filtro['type'],
								   "comparison" => $filtro["comparison"] );
	}else{
		$filtros[] = $filtro;
	}
}//end each


}//end if


$ids = array();
$count_filtros = count($filtros_adicionales);
//me fijo si tengo filtros adicionales
if( $count_filtros > 0 ){

foreach( $filtros_adicionales as $filtro ){

	
$value = $filtro['value'];



if( $filtro['type'] == "list" ){

		$filter = array();
		$filter[] = array("field"=>"subcontrol","type"=>"list","value"=>$filtro['value']);
		$filter[] = array("field"=>"value","type"=>"list","value"=>"1");
	
		//busco los ids de los contacto que corresponde la info aficional filtrada
		if( $list_values = contactos_informacion_adicional_subcontrol_value::getFilteredList( false, false, false, false, $filter ) ){		
			foreach( $list_values as $value ){			
				$ids[] = $value->get('contacto')->id();
			}
		}else{
			$ids[] = 0;
		}//end if
		
	


}else
{


		$filter = array();
		if( $filtro['type'] == "date" ){
			$value = date('Y-m-d',strtotime($value));
			$filter[] = array("field"=>"value","type"=>"date","comparison"=>$filtro['comparison'],"value"=>$value);	
		}
		else
		{
			
			if( $filtro['type'] == "numeric" ){
				$filter[] = array("field"=>"value","type"=>"numeric","comparison"=>$filtro['comparison'],"value"=>$value);
			}else{
				$filter[] = array("field"=>"value","type"=>"string","value"=>$value);
			}
			
		}
	
	$filter[] = array("field"=>"control","type"=>"list","value"=>$filtro['id']);

		//busco los ids de los contacto que corresponde la info aficional filtrada
		if( $list_values = contactos_informacion_adicional_control_value::getFilteredList( false, false, false, false, $filter ) ){
			
			foreach( $list_values as $value ){
				$ids[] = $value->get('contacto')->id();
			}
		}else{
			$ids[] = 0;
		}//end if
		
		

}//end each

if( count($ids) > 0 ){
				$ids[] = 0;
		}
		

}		

if( count($ids) > 0 ){


$a_count = array_count_values($ids);

$n_ids = array();
foreach( $a_count as $key=>$value ){
	if($value > ($count_filtros -1) ){
		$n_ids[] = $key;
	}
}

if( count($n_ids) < 1 ){
	$n_ids = $ids;
}

	$filtros[] = array( "field"=>"id", "type"=>"list", "value"=>implode(",", $n_ids ) );	
}


}//end if

; 

$filtros[] = array("field"=>"status","type"=>"integer","value"=>"1");

//FIN DE FILTROS
$i = 0;
$return = array();



//TRAIGO EL LISTADO DE CONTACTOS
if( $list_contactos = contactos_contacto::getFilteredList( (int) $start , (int) $count , false, false, $filtros ) ){
		
		foreach( $list_contactos as $contacto ){			
		
			$return[$i]['id'] = $contacto->id();		
			$return[$i]['nombre'] = $contacto->get('nombre');
			$return[$i]['uname'] = $contacto->name();
			$return[$i]['apellido'] = $contacto->get('apellido');			
			$return[$i]['cuit'] = $contacto->get('cuit');
			$return[$i]['ingbrutos'] = $contacto->get('ingbrutos');			
			$return[$i]['numerocliente'] = $contacto->get('numerocliente');
			$return[$i]['clase'] = $contacto->get('clase')->get('detalle');
			$return[$i]['id_clase'] = $contacto->get('clase')->id();
			$return[$i]['usuario::name'] = $contacto->get('usuario')->name();
			$return[$i]['categoria'] = $contacto->get('categoria')->get('nombre');								
			$return[$i]['rubro::detalle'] = $contacto->get('rubro')->get('detalle');								
			$return[$i]['dias_pago'] = $contacto->get('dias_pago');	

			$telefonos = array();	
			if( $lista_medios = $contacto->get('contactos_medios_contacto')  ){
				foreach( $lista_medios as $medio){
					if( $medio->get('tipo')->id() == '2' ){
						$telefonos[] = $medio->get('direccion');
					}	
				}
			}
			
			$return[$i]['telefonos'] = implode( ",", $telefonos );		
			
			
			//informacion adicional
			//para los nombre de los campos de informacion adicional se sigue la siguiente nomenclatura
			// infoadicional_idcontrol donde idcontrol se reemplaza por el correspondiente id			
			if( $controles = contactos_informacion_adicional_control::getFilteredList() ){
				foreach( $controles as $control ){
					if( $control->get('tipo')->id() != '2' ){
					$return[$i][ 'infoadicional_' . $control->id() ] = 	contactos_informacion_adicional_control_value::get_control_value( $contacto, $control );				
					}
					else
					{
					//si es radio group
					 $filtro_control = false;
					 $filtro_control[] = array("field"=>"control","type"=>"list","value"=>$control->id() );	
					 if( $list_subcontroles = contactos_informacion_adicional_subcontrol::getFilteredList(false,false,false,false,$filtro_control) ){
							foreach( $list_subcontroles as $subcontrol ){	
								if( contactos_informacion_adicional_subcontrol_value::get_control_value($contacto,$subcontrol) == '1' ){
									$return[$i][ 'infoadicional_' . $control->id() ] =  contactos_informacion_adicional_subcontrol_propiedad::get_property_value( $subcontrol,'label' ) ;							
								}
							}
					 }
					
					
					}
				}
			}
			
			
			$i++;
			
		}
}

//ARRAY PARA DEVOLVER
$result = array();
$result["totalCount"] = contactos_contacto::getFilteredListCount( $filtros );
$result["topics"]  = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>