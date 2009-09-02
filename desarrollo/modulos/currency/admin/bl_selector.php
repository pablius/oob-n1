<?php
# OOB-n1 [�2005 - 2008 Nutus, todos los derechos reservados]
/*
 * Created on 22/07/2008
 * @author Pablo Micolini (pablo.micolini@nutus.com.ar)
 */

global $ari;
$plantilla_bl = $ari->newTemplate();
$plantilla_bl->caching = 0; // dynamic content 
$language = $ari->get('agent')->getLang();



//var_dump($params);exit;
//validar parametros del bloque
$currency_selected = false;
if (isset($params['currencyID']) && OOB_validatetext::isNumeric($params['currencyID']) && 	$params['currencyID'] > 0)
{
	$currency_selected = $params['currencyID'];
}



$value = '';
if (isset($params['value']) && 	OOB_validatetext::isNumeric($params['value']))
{
	$value = $params['value'];
} 

$prefix = 'currency';
if(isset($params['prefix']))
{
	$prefix = $params['prefix'];
}
	
if ($currencies = currency_currency :: listCurrenciesForLanguage (USED, 'name', $operator = OPERATOR_EQUAL, $language) )
{
	$i = 0;
	foreach($currencies as $c)
	{
		$array_currencies[ $c->get('id')] = $c->get('name') . " (" . $c->get('sign') . ")";
		
		if ($currency_selected === false && $c->get('default') == YES)
		{
			$currency_selected = $c->get('id');
		}
		$i++;
	}	
}
 
$plantilla_bl->assign("options", $array_currencies );
$plantilla_bl->assign("prefix", $prefix);
$plantilla_bl->assign("value", $value);
$plantilla_bl->assign("currency_selected", $currency_selected);



$plantilla_bl->display($modulo->admintpldir(). "/bl_selector.tpl");

?>