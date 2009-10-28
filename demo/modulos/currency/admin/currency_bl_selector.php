<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE DEVUELVE UN JSON CON LAS MONEDAS 

global $ari;
$ari->popup = 1;
$language = $ari->get('agent')->getLang();

$return = array();	
$i = 0;

if ($currencies = currency_currency :: listCurrenciesForLanguage (USED, 'name', $operator = OPERATOR_EQUAL, $language) )
{	
	foreach($currencies as $c)
	{
		$return[$i]['id'] = $c->get('id') ;
		$return[$i]['name'] = $c->get('name') . " (" . $c->get('sign') . ")" ;
		
		if ($c->get('default') == YES)
		{
			$currency_selected = $c->get('id');
			$return[$i]["dataIndex"] = "selected";	
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