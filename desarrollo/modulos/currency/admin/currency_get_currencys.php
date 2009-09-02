<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$language = $ari->get('agent')->getLang();

if (!isset($_POST['data'])) $_POST['data'] = "";

//SE SETEA POR DEFECTO LA MONEDA QUE SE PASA POR PARAMETRO
	if (isset ($_POST['ChangePredeterminadaData']) )
	{	
		//DECODIFICADO EL JSON
		$default_item = json_decode( $_POST['ChangePredeterminadaData'] , true );
				
			//SE CREA EL OBJETO Y SE SETEA COMO DEFAULT
			$currency = new currency_currency($default_item["0"]["id"]);
			$currency->set('default', YES);
			
			//SE GUARDAN LOS CAMBIOS
			$currency->store();
			
	}
	
//FILTRO POR COLUMNAS 
$where = "";

if( $_POST['data'] != "" ){


$filtros = admin_session_state::cache_filters(  json_decode( $_POST['data'], true ) );

$operadores = array();
$operadores["eq"] = "=";
$operadores["lt"] = "<";
$operadores["gt"] = ">";

foreach($filtros as $filtro)
{

switch( $filtro['type'] ){		
		
		case "string":				
			$operador_inicio = " LIKE '%";
			$operador_fin = "%'";
		Break;
		case "numeric":
			if( $filtro['field'] == 'cotizacion'  ){
						
					$in = array();
					if( $currencies = currency_currency::listCurrenciesForLanguage( USED, 'name', OPERATOR_EQUAL, $language))
					{	
						foreach($currencies as $c)
						{
								if( $lastValue = $c->getLastChange() )
								{				
									$value = OOB_numeric::formatPrint( $lastValue['value'] );		
								
						
										switch( $filtro['comparison'] ){			
											case "eq":
												if( $filtro['value'] == $value){
													$in[] =	$c->get('id');
												}
											break;
											case "lt":
												if( $value < $filtro['value']){
													$in[] =	$c->get('id');				
												}
											break;
											case "gt":
												if( $value > $filtro['value'] ){
													$in[] =	$c->get('id');
												}
											break;
										}//end switch
							
								}//end if
						}//end each		
						
					}//end if
					
					$operador_inicio = " IN(";
					$operador_fin = ")";				
					$filtro['field'] = "id";
				if(count($in) > 0 ){
					$filtro['value']  = implode( ",", $in );									
				}else{
					$filtro['value'] = 0;
				}	
			}
			else
			{
				$operador_inicio = $operadores[$filtro['comparison']];
				$operador_fin = "";		
			}
		Break;
		case 'boolean' : 
			$operador_inicio = "=";
			$operador_fin = "";				
			$filtro['value']  = ($filtro['value']=="true")?"1":"0";
			$filtro['field'] = "`" . $filtro['field'] . "`" ;
		Break;
		case 'list' : 
			$operador_inicio = " IN(";
			$operador_fin = ")";				
		Break;
		
}

$where.=" AND C.{$filtro['field']} {$operador_inicio}{$filtro['value']}{$operador_fin} "; 

}

}



//FIN DE FILTROS	


//SE OBTIENE EL LISTADO DE MONEDAS
$i = 0;
$return = array();
if( $currencies = currency_currency::listCurrenciesForLanguage( USED, 'name', OPERATOR_EQUAL, $language, $where))
{
	
	foreach($currencies as $c)
	{	
	
		$return[$i]['id'] = $c->get('id');
		$return[$i]['name'] = $c->get('name') . " (" . $c->get('sign') . ")";
		$return[$i]['default'] = ($c->get('default')==YES)?"SI":"NO";
		$return[$i]['type'] = ( $c->get('type') == '1')?"Fija":"Flotante";
		
		if( $lastValue = $c->getLastChange() )
		{				
			$return[$i]['cotizacion'] = OOB_numeric::formatPrint( $lastValue['value'] );		
		}
		
		if( $c->get('type') == '1' ){
			$return[$i]['cotizacion'] = OOB_numeric::formatPrint( $c->get('value') );		
		}
		
		$i++;
	}	
}

//ARRAY CON LOS RESULTADOS
$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>