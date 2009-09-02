<?

//SCRIPT QUE PROCESA LOS DATOS DEL FORM NUEVA MONEDA
//CODIGO POR JPCOSEANI

global $ari;
$ari->popup = 1;


//ARRAY PARA LOS ERRORES
$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;


//SETEO DE VALORES

	
		//NOMBRE MONEDA
		$nombre = '';
		if (isset ($_POST['txt_moneda']))
		{	
			$nombre = $_POST['txt_moneda'];
		}else{
			throw new OOB_Exception_400("La variable [txt_moneda] no esta definida");
		}
		
		//VALOR DE LA MONEDA
		$valor = '';
		if (isset ($_POST['txt_valor']))
		{	
			$valor = $_POST['txt_valor'];
		}else{
			throw new OOB_Exception_400("La variable [txt_valor] no esta definida");
		}			
		
		//TIPO DE CAMBIO (FIJO O FLOTANTE)
		$tipo = FIXED_CHANGE;
		if (isset ($_POST['tipo']))
		{	
			if( $_POST['tipo'] == 'float')
			{	
				
				$tipo = FLOAT_CHANGE;
				$valor = "1";
			}
		}else{
			throw new OOB_Exception_400("La variable [tipo] no esta definida");
		}

		//SIGNO DE LA MONEDA ( SIMBOLO)
		$signo = '';
		if (isset ($_POST['txt_signo']))
		{	
			$signo = $_POST['txt_signo'];
		}else{
			throw new OOB_Exception_400("La variable [txt_signo] no esta definida");
		}
		
		
		//MONEDA PREDETERMINADA
		$predeterminada = NO ;		
		if (isset ($_POST['predeterminada']))
		{	
			if( $_POST['predeterminada'] == 'true' || $_POST['predeterminada'] == true )
			{				
				$predeterminada = YES;
				$tipo = FIXED_CHANGE;
				$valor = "1";
			}
		}else{
			throw new OOB_Exception_400("La variable [predeterminada] no esta definida");
		}
		file_put_contents('fewfwfw.txt',$predeterminada);
		
//SE CREA EL OBJETO
		$currency = new currency_currency();
		$currency->set( 'name', $nombre );		
		$currency->set( 'sign', $signo );
		$currency->set( 'type', $tipo );
		$currency->set( 'value', $valor );
		$currency->set( 'default', $predeterminada );		
		$currency->set( 'status', USED );
		
//FIN DE SETEO DE VALORES		
		
		//INTENTAMOS GUARDAR LOS DATOS
		if( $currency->store() ){
				
		//IDIOMAS
		//PASO A UN ARRAY LOS IDIOMAS SELECCIONADOS
		if ( isset( $_POST['idiomas'] ) )
		{
			if( $idiomas = split( "," , $_POST['idiomas'] ) )
			{	
				foreach( $idiomas as $idioma )
				{
					$currency->addLanguage( $idioma );			
				}//end each				
			}//end if		
		}//end if
				
		
		$resultado["success"] = true;
		
		}
 	

//ERRORES
if( $errores = $ari->error->getErrorsfor("currency_currency") )
{

    
   $error_codes = array();
   $error_codes['INVALID_NAME'] = array( "id"=>"txt_moneda", "msg" => "El Nombre de la de la moneda es invalido" );
   $error_codes['INVALID_SIGN'] = array( "id"=>"txt_signo", "msg" => "El valor del signo es invalido" );
   $error_codes['INVALID_VALUE'] = array( "id"=>"txt_valor", "msg" => "El Valor es invalido" );
   $error_codes['DUPLICATE_CURRENCY'] = array( "id"=>"txt_moneda", "msg" => "La moneda ya existe" );
   $error_codes['INVALID_NAME'] = array( "id"=>"txt_moneda", "msg" => "El Nombre de la de la moneda es invalido" );
   $error_codes['INVALID_LANGUAGE'] = array( "id"=>"txt_valor", "msg" => "Debe seleccionar un idioma" );
   
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}		


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);

?>