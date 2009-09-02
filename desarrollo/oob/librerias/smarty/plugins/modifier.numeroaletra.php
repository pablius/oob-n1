<?php
/**
 * Smarty numeroaletra modifier plugin
 *
 * Type:     modifier<br>
 * Name:     numeroaletra<br>
 * Purpose:  convierte de numeros a letras
 * @param numeric
 * @return string
 */
function smarty_modifier_numeroaletra($string,$moneda = 'Pesos')
{
    
	require_once (dirname(dirname(dirname(__FILE__))) .DIRECTORY_SEPARATOR. 'numeroaletra' .DIRECTORY_SEPARATOR. 'CNumeroaLetra.php');
	
	$numero_limpio = str_replace(',','.',$string);
	$numero_limpio = str_replace('$','',$numero_limpio);
	$numero_limpio = trim($numero_limpio);
	
	
	$numalet= new CNumeroaletra ();
	$numalet->setNumero($numero_limpio);
	$numalet->setMoneda($moneda);
	$numalet->setPrefijo("");
	$numalet->setSufijo("");
	return $numalet->letra();
	
}

?>
