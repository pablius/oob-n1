<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 
 class OOB_numeric
 {
 	static public function isValid($number)
 	{
 		global $ari;
 		
 		
 		if ($number === "")
			return false;
 		
		//@todo: sacar el menos si tiene un menos adelante
		
 		$separador_decimal = trim($ari->locale->get('decimal', 'numbers'));
 		$separador_miles = trim($ari->locale->get('separadormiles', 'numbers'));
 		
 		$escape_separador_decimal = "";
 		$escape_separador_miles = "";
 		
 		if ($separador_decimal == ".")
 		{	$escape_separador_decimal = "\\";	}

 		if ($separador_miles == ".")
 		{	$escape_separador_miles = "\\";	}
 		 		
 		if (ereg("^-{0,1}[0-9]{1,}($escape_separador_miles$separador_miles?[0-9]{3}){0,}($escape_separador_decimal$separador_decimal?[0-9]+)?$",$number) )
 		{	return true;	}
 		else
 		{	return false;	}
 	}

 	static public function formatMySQL($number)
 	{
 		global $ari;
 		
 		$separador_decimal = trim($ari->locale->get('decimal', 'numbers'));
 		$separador_miles = trim($ari->locale->get('separadormiles', 'numbers'));
 		
 		//tengo q sacar los separadores de miles sea cual sea
 		if ($separador_miles == ".")
 		{	$number = str_replace(".","",$number);		}
 		elseif ($separador_miles == ",")
 		{	$number = str_replace(",","",$number);		} 		
 		
 		//tengo q tener siempre un separador de decimales "."
 		if ($separador_decimal == ",")
 		{	$number = str_replace(",",".",$number);		}
	
 		return $number;
 		
 	} 	
 	
 	static public function formatPrint($number)
 	{
  		global $ari;
 		
 		//print $number . "<br>";
 		
 		$separador_decimal = trim($ari->locale->get('decimal', 'numbers'));
 		$separador_miles = trim($ari->locale->get('separadormiles', 'numbers'));
 		
 		// el dato viene de la BD como #########.###
 		
 		//cambio el punto por el separador de decimales actual
 		$number = str_replace(".",$separador_decimal,$number);				 
 		
 		//saco la posicion donde esta el separador de decimales
 		if(!$pos = strpos($number, $separador_decimal))
 		{	
 			$decimal = "";
 			$entero = $number;
 		}
 		else
 		{
 			$decimal = substr($number,$pos,strlen($number)-1);
 			$entero = substr($number,0,$pos);
 		}
 		//invierto la cadena $entera
 		$entero = strrev($entero);
 		
 		$tmp = "";
 		
 		if (strlen($entero) > 3)
		{
			$flagTerminar = false;
			$i = 0;
			while (!$flagTerminar)
			{ 	
				if ($i + 3 > strlen($entero) )
				{
					$flagTerminar = true;
					$tmp .= substr($entero,$i,strlen($entero)-$i);
				}
				else
				{	$tmp .= substr($entero,$i,3) . $separador_miles  ;	}	
 				$i = $i + 3;
			}
		}
		else
		{ $tmp = $entero;	}
		
 		//lo vuelvo a invertir
 		$entero = strrev($tmp);
 		
 		if (substr($entero,0,1) == $separador_miles)
 		{	$entero = substr($entero,1,strlen($entero)-1);	}
 		
 		$number = $entero . $decimal;
 		
 		return $number;
 	} 	

 	
 }//end class
?>
