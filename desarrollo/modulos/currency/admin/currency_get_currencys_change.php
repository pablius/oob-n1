<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$language = $ari->get('agent')->getLang();


//CAMBIO EL VALOR DE LAS MONEDAS
if (isset($_POST['NewsValuesData'])){

$news_values = json_decode( $_POST['NewsValuesData'], true );

foreach( $news_values['items'] as $item )
 	{
		$currency = new currency_currency($item['id']);
		if( $currency->get("type") == 2 ){
				$date = new Date( date('Y-m-d', strtotime( $news_values['fecha'])) ." ". date('H:i:s') );						
				$separador = trim( $ari->locale->get('decimal', 'numbers') );
				$currency->addChange( number_format(  1/ $item['value'], 6, $separador, "" ) ,$date);
		}
  	}
}


//SE OBTIENE EL LISTADO DE CAMBIOS DE MONEDAS
$i = 0;
$return = array();

//para que no muestre las monedas de cambio fijo
$where = ' AND type = 2';

if( $currencies = currency_currency::listCurrenciesForLanguage( USED, 'name', OPERATOR_EQUAL, $language, $where ) )
{
	
	foreach( $currencies as $c )
	{
		$return[$i]['id'] = $c->get('id');
		$return[$i]['currency'] = $c->get('name') . " (" . $c->get('sign') . ")";
		if( $lastValue = $c->getLastChange() )
		{				
			$return[$i]['value'] = round((1/$lastValue['value']),2);
			$return[$i]['date'] = $lastValue['date'];			
		}
		
		$i++;
	}	
}

$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>