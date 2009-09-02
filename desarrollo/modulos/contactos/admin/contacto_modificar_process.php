<?php
//SCRIPT QUE PROCESA LOS DATOS DEL FORM NUEVO CONTACTO
//CODIGO POR JPCOSEANI

global $ari;
$ari->popup = 1;


//ARRAY PARA LOS ERRORES
$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

$ari->db->startTrans();

	if( isset( $_POST['id'] ) ){
		$id_contacto = $_POST['id'];
		$contacto = new contactos_contacto($id_contacto);		
	}else{
		throw new OOB_Exception_400("La variable [id] no esta definida");
	}
	
//SETEO DE VALORES

	
		//NOMBRE
		$nombre = "";
		if (isset($_POST['txt_nombre']))
		{	
			$nombre = $_POST['txt_nombre'];			
		}
		
		if( $nombre == "" ){
			$contacto->error()->addError("NO_NOMBRE");			
		}
		
		//APELLIDO Y RAZON SOCIAL
		$apellido = "";
		if ( isset( $_POST['txt_razonsocial'] ))
		{	
			$apellido= $_POST['txt_razonsocial'];
		}
		
		if( $apellido == "" ){
			$contacto->error()->addError("NO_APELLIDO");			
		}
		
		//CUIT
		$cuit = "";
		if ( isset( $_POST['txt_cuit'] ) )
		{	
			$cuit = str_replace( '-','', $_POST['txt_cuit'] );			
		}
		
		//INGRESOS BRUTOS
		$ingbrutos = "";
		if ( isset( $_POST['txt_ingbrutos'] ) )
		{	
			$ingbrutos = $_POST['txt_ingbrutos'];
		}
		
		//NRO CLIENTE
		$nrocliente = "";
		if ( isset( $_POST['txt_nrocliente'] ))
		{	
			$nrocliente = $_POST['txt_nrocliente'];
		}
					
		//RUBRO
		$rubro = "";
		if ( isset( $_POST['cbo_rubro_value'] ) )
		{	
			$rubro = new contactos_rubro( $_POST['cbo_rubro_value'] );
		}else{
			throw new OOB_Exception_400("La variable [cbo_rubro_value] no esta definida");
		}
		
		//CLASE
		$clase = "";
		if ( isset( $_POST['cbo_clase_value'] ))
		{	
			$clase = new contactos_clase( $_POST['cbo_clase_value'] );
		}

		
		//CATEGORIA
		$categoria = "";
		if ( isset( $_POST['cbo_categoria_value'] ) )
		{	
			$categoria = new impuestos_categorizacion($_POST['cbo_categoria_value']);			
		}else{
			throw new OOB_Exception_400("La variable [cbo_categoria_value] no esta definida");
		}
		
		//DIAS DE PAGO
		$diaspago = "";
		if ( isset( $_POST['txt_diaspago'] ) )
		{	
			$diaspago = $_POST['txt_diaspago'];
		}
		
		//TIPO
		$tipo = 1;		
		if (isset ($_POST['persona']))
		{		
			if ($_POST['persona'] == 'pf')
			{	
				$tipo = 2;		
			}
		}
		
		
		//USUARIO		
		$usuario =  new oob_user();
				
		
			if (isset ($_POST['optusuario']))
			{		
				if ($_POST['optusuario'] == 'existente')
				{	
					//se crea un nuevo objeto usuarios
					if( $_POST['cbo_usuario_value'] == '' ){
						$usuario = new oob_user($_POST['cbo_usuario_value']);
					}
					else
					{
						$usuario = $contacto->get('usuario');
					}
					
				}
				else
				{
					
					
							//se crea un nuevo objeto usuarios
							$usuario = new oob_user();

							//se asigna el usuario
							$usuario->set( 'uname', $_POST['txt_usuario'] );

							
							// password
							if ($_POST['txt_pass'] != "") // solo lo asignamos si escribi\u00f3 algo
							{
								$usuario->set ('password', $_POST['txt_pass']);
							}

							// email
							$usuario->set ('email', $_POST['txt_email']);

							// status
							$usuario->set ('status', "1");

							// tratamos de grabar si puso los dos pass iguales
							if ($_POST['txt_pass'] === $_POST['txt_repetir'])
							{
								if(!$usuario->store()){
									$contacto->error()->addError("NOSTOREUSER");									
								}
								
								if ($errores = $usuario->error()->getErrors())
								{	
									foreach ($errores as $error)
									{
										$contacto->error()->addError($error);									
									}
								}
								
							}
							else
							{						
								$contacto->error()->addError("MISSMATCH");
							}//end if
					
					
				
				}//end if
				
			}//end if
		
		
		
		
		//SE CREA EL OBJETO				
		$contacto->set('nombre', $nombre );
		$contacto->set('apellido', $apellido );
		$contacto->set('cuit', $cuit );
		$contacto->set('ingbrutos', $ingbrutos );
		$contacto->set('numerocliente', $nrocliente );
		$contacto->set('rubro', $rubro );
		$contacto->set('usuario', $usuario );
		$contacto->set('tipo', new contactos_tipo($tipo) );
		$contacto->set('clase', $clase );
		$contacto->set('categoria', $categoria );
		$contacto->set('dias_pago', $diaspago);
		
//FIN DE SETEO DE VALORES		
		//INTENTAMOS GUARDAR LOS DATOS
		if( $contacto->store() ){
				$resultado["success"] = true;
				
				
								
				$medios = json_decode( $_POST['medios'] , true );
				$medios_activos = array();
				foreach( $medios as $medio )
				{	
					if(  $medio['id'] != "" ){	
						if( $med = new contactos_medios_contacto($medio['id']) ){
							$med->set( 'direccion', $medio['direccion'] );
							$med->set( 'tipo', new contactos_medios_contacto_tipo( $medio['tipo'] ) );
							$med->store();
							$medios_activos[] = $medio['id'];
						}					
					}
					else
					{
							$obj_medio = new contactos_medios_contacto();
							$obj_medio->set( 'contacto', $contacto );
							$obj_medio->set( 'direccion', $medio['direccion'] );
							$obj_medio->set( 'tipo', new contactos_medios_contacto_tipo($medio['tipo']) );
							$obj_medio->store();
							$medios_activos[] = $obj_medio->id();
					}					
				}
				
				$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );
				if( $lista_emails = contactos_medios_contacto::getFilteredList( false, false, false, false, $filtros ) ){
						foreach( $lista_emails as $email ){			
							//si no esta en el array la borro
							if( !in_array( $email->id(),$medios_activos) ){
								$email->delete();
							}							
						}					
				}
				
								
				$direcciones = json_decode( $_POST['direcciones'] , true );
				
				$dir_activas = array();
				foreach( $direcciones as $direccion )				
				{
				
					if(  $direccion['id'] != "" ){
						if( $dir = new contactos_direccion($direccion['id']) ){
							$dir->set( 'direccion', $direccion['direccion'] );					
							$dir->set( 'extra', $direccion['extra'] );					
							$dir->set( 'cp', $direccion['cp'] );							
							$dir->set( 'ciudad', new address_city( $direccion['ciudad']));
							$dir->set( 'tipo', new contactos_direccion_tipo( $direccion['tipo'] ) );
							$dir->store();
							$dir_activas[] = $direccion['id'];
						}	
					}
					else
					{
							$obj_dir = new contactos_direccion();
							$obj_dir->set( 'contacto', $contacto );
							$obj_dir->set( 'direccion', $direccion['direccion'] );					
							$obj_dir->set( 'extra', $direccion['extra'] );					
							$obj_dir->set( 'cp', $direccion['cp'] );							
							$obj_dir->set( 'ciudad', new address_city( $direccion['ciudad']));
							$obj_dir->set( 'tipo', new contactos_direccion_tipo( $direccion['tipo'] ) );
							$obj_dir->store();								
							$dir_activas[] = $obj_dir->id();
					}//end if
					
				}//end each
				
				
				$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );
				if( $lista_direcciones = contactos_direccion::getFilteredList( false, false, false, false, $filtros ) ){
						foreach( $lista_direcciones as $direccion ){			
							//si no esta en el array la borro
							if( !in_array( $direccion->id(),$dir_activas) ){
								$direccion->delete();
							}	
						}					
				}
				
				
				
				//controles
				$controles = json_decode( $_POST['controles'] , true );

				foreach( $controles as $control )
				{
				if(isset($control['name'])){
				if( $obj_control = new contactos_informacion_adicional_control($control['name']) ){
				
					if(isset($control['value'])){
					contactos_informacion_adicional_control_value::set_control_value($contacto,$obj_control,$control['value']);
					}
					
					//veo si tiene subitems
					if(isset($control['subitems'])){
					
						foreach( $control['subitems'] as $subitem ){
							if(isset($subitem['name'])){
								if( $sub_control = new contactos_informacion_adicional_subcontrol($subitem['name']) ){
									
									if(isset($subitem['value'])){
										contactos_informacion_adicional_subcontrol_value::set_control_value($contacto,$sub_control,$subitem['value']);
									}
								}						
							}
						}					
					
					}
							
					
				}
				}
				
				
				}
				//end controles
				
				//actualizo las areas del contacto
				//si es empleado
				if( $clase->id() == '4' && $_POST['areas_values'] != '' ){						
						
						if( $_POST['areas_values'] != '' ){
						if( $areas = explode( ";", $_POST['areas_values'] ) ){
												
						if( count($areas) > 0 ){						
						
							foreach( $areas as $area ){
							
								$count = 0;
								$filtros = false;
								$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );
								$filtros[] = array( "field"=>"area", "type"=>"list", "value"=>$area );
								if( $list_rel = contactos_contacto_area::getFilteredList( false, false, false, false, $filtros ) ){
										$count = count($list_rel);
								}//end if
							
								//si no existe lo inserto
								if( $count == 0 ){
									$obj_area = new contactos_areas($area);
									$obj_relacion = new contactos_contacto_area();
									$obj_relacion->set( 'area', $obj_area );
									$obj_relacion->set( 'contacto', $contacto );
									$obj_relacion->store();
								}//end if
								
								//tengo que elimnar las que no vienen seleccionadas
								$filtros = false;
								$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );								
								if( $list_rel = contactos_contacto_area::getFilteredList( false, false, false, false, $filtros ) ){
										foreach( $list_rel as $rel ){
											if(!in_array( $rel->get('area')->id(),$areas )){
												$rel->delete();
											}//end if
										}//end each
										
								}//end if
								
								
							}//end for each
						
						
						}//end if
						}
						}
						
				}else{
				
				
								$filtros = false;
								$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );								
								if( $list_rel = contactos_contacto_area::getFilteredList( false, false, false, false, $filtros ) ){
										foreach( $list_rel as $rel ){									
												$rel->delete();
										}//end each
										
								}//end if
				
				
				}
				//end actualizacion de areas
				
			$ari->db->CompleteTrans();			
				
		}
		else
		{
			$ari->db->failTrans();
		}
 	

//ERRORES
if ( $errores = $contacto->error()->getErrors() )
{

    
   $error_codes = array();
   $error_codes['NO_NOMBRE'] = array("id"=>"txt_nombre","msg"=>"Debe ingresar un nombre");
   $error_codes['NO_APELLIDO'] = array("id"=>"txt_razonsocial","msg"=>"Debe ingresar un apellido");
   $error_codes['NO_CUIT'] = array("id"=>"txt_cuit","msg"=>"El n&uacute;mero de CUIT es invalido");
   $error_codes['DUPLICATED'] = array("id"=>"txt_cuit","msg"=>"El n&uacute;mero de cuit ya existe");
   $error_codes['NO_INGBRUTOS'] = array("id"=>"txt_ingbrutos","msg"=>"El n&uacute;mero de Ing. brutos es invalido");   
   $error_codes['NOSTOREUSER'] = array("id"=>"txt_usuario","msg"=>"No se pudo grabar el usuario");
   $error_codes['INVALID_PASS'] = array("id"=>"txt_pass","msg"=>"Contrase&ntilde;a inv&aacute;lida (4 a 8 caracteres alfanum&eacute;ricos)");
   $error_codes['MISSMATCH'] =array("id"=>"txt_repetir","msg"=>"Las contrase&ntilde;as no concuerdan");
   $error_codes['INVALID_USER'] =array("id"=>"txt_usuario","msg"=>"El nombre de usuario no es v&aacute;lido");
   $error_codes['INVALID_EMAIL'] = array("id"=>"txt_email","msg"=>"El e-mail ingresado no es v&aacute;lido");
   $error_codes['INVALID_STATUS'] = array("id"=>"cbo_estados","msg"=>"El estado elegido no es v&aacute;lido");
   $error_codes['ALREADY_DELETED'] = array("id"=>"cbo_estados","msg"=>"Este usuario ya se encuentra borrado");
   
	    
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);		 

?>