<?

//SCRIPT QUE PROCESA LOS DATOS DEL FORM MODIFICAR MONEDA
//CODIGO POR JPCOSEANI

global $ari;
$ari->popup = 1;

if( isset ($_POST['id']) ){

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
			throw new OOB_Exception_400("La variable [opt-txt_moneda] no esta definida");
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
		if (isset ($_POST['opt-tipo-cambio']))
		{	
			if ($_POST['opt-tipo-cambio'] == 'float')
			{	
				$tipo = FLOAT_CHANGE;
				$valor = "1";
			}
		}else{
			throw new OOB_Exception_400("La variable [opt-tipo-cambio] no esta definida");
		}

		//SIGNO DE LA MONEDA ( SIMBOLO)
		$signo='';
		if (isset ($_POST['txt_signo']))
		{	
			$signo = $_POST['txt_signo'];
		}else{
			throw new OOB_Exception_400("La variable [txt_signo] no esta definida");
		}
		
		
		//MONEDA PREDETERMINADA
		$predeterminada = NO ;
		if (isset ($_POST['opt-predeterminada']))
		{	
			if ($_POST['opt-predeterminada'] == 'yes')
			{
				$predeterminada = YES;
				$type = FIXED_CHANGE;
				$valor = "1";
			}
		}else{
			throw new OOB_Exception_400("La variable [opt-predeterminada] no esta definida");
		}
		
//SE CREA EL OBJETO Y SE LE PASAN LOS VALORES
		$currency = new currency_currency($_POST['id']);
		$currency->set ('name', $nombre);		
		$currency->set ('sign', $signo);
		$currency->set ('type', $tipo);
		$currency->set ('value', $valor);
		$currency->set ('default', $predeterminada);		
		$currency->set ('status', USED);
		
//FIN DE SETEO DE VALORES		
		
		//INTENTAMOS GUARDAR LOS DATOS
		if($currency->store()){
		
		if (isset ($_POST['idiomas']))
		{	
			$idiomas = array();
			$idiomas = split( "," , $_POST['idiomas'] );	
		
			if (currency_currency :: removeAllLanguages( $currency ))
			{
				for($i=0;$i<count($idiomas);$i++){		
					$currency->addLanguage ( $idiomas[$i] );			
				}
			}	
			
			$resultado["success"]= true;	
				
		}
 	
		}

//ERRORES
if ($errores = $ari->error->getErrorsfor("currency_currency"))
{

    
   $error_codes = array();
   $error_codes['INVALID_NAME'] = array("id"=>"txt_moneda","msg"=>"El Nombre de la de la moneda es invalido");
   $error_codes['INVALID_SIGN'] = array("id"=>"txt_signo","msg"=>"El valor del signo es invalido");
   $error_codes['INVALID_VALUE'] = array("id"=>"txt_valor","msg"=>"El Valor es invalido");
   $error_codes['DUPLICATE_CURRENCY'] = array("id"=>"txt_moneda","msg"=>"La moneda ya existe");
  
   
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}		


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);

}else{
			throw new OOB_Exception_400("La variable [id] no esta definida");
}

?>