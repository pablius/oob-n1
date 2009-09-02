<?php
/**
########################################
#OOB/N1 Framework [©2009]
#
#  @copyright Pablo Micolini
#  @license BSD
#  @version 1.1
######################################## 
*/

/**
 Validates a given var to see if it's of the choosen kind
*/
class OOB_validatetext {

	private function __construct() {
	}

	static public function isCorrectLength($v, $length_from= 0, $length_to, $coincide= false) {


		$v = trim($v);
		if ($coincide) {
			if (strlen($v) != $length_to) {

				return false;
			}
		} else {
			if (strlen($v) < $length_from || strlen($v) > $length_to) {
				return false;
			}
		}
		return true;
	}

	static public function checkNotAllowChars($v, $pattern) {
		if (preg_match($pattern, $v)) { // , $matches
			return false;
		}
		return true;
	}
/** Valida que sea un EMAIL válido */
	static public function isEmail($email) {
		if (!eregi("^([a-z0-9\\_\\.\\-]+)@([a-z0-9\\_\\.\\-]+)\\.([a-z]{2,6})$", $email)) 
		{
			// to be replaced by /^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD
			return false;
		}
			return true;
	}
/** Valida que sea una clave válida entre 4 y 12 caracteres */
	static public function isPassword($passw, $field= "") {
		if (strlen($passw) < 4 || strlen($passw) > 12) {
			return false;
		}
		return true;
	}
/** Valida que sea un numero, utiliza localidad, por lo q difiere en puntos y comas segun LOCALE */
	static public function isNumeric($v) {
		return OOB_numeric::isValid($v);
	}
	
	static public function isFloat($v)
	{
		return ($v == (string)(float)$v);
	}
	
/** Valida que sea un numero entero sin decimal */
	static public function isInt($v) {
		if (!eregi("^([-]?[0-9]+)$", $v) || $v == '') { // old > "^([0-9]+)$" /// "@^[-]?[0-9]+$@"
			return false;
		}
		return true;
	}

/** Valida que sea un booleano */
	static public function isBool($v) {
		if ($v === true)
		{$v =1;}
		
		if ($v === false)
		{$v =0;}
		
		if (!eregi("^([0-1]+)$", $v) && $v != '') {
			return false;
		}
		return true;
	}

/** valida que el string no tenga tags HTML */
	static public function isClean($str) {
		$original= $str;

		while ($str != strip_tags($str)) {
			$str= strip_tags($str);
		}
		$srt= htmlentities($str);

		if ($original !== $str)
			return false;
		else
			return true;

	}
	
	static public function isCUIT($S) 
	{
		/*
		 determina si el dígito verificador es correcto
		 Retorna true si es correcto y false si es incorrecto
		*/
		
		if(	$S == 0 ){
			return false;
		}
		
		$v2 = (substr($S,0,1) * 5 +
				 substr($S,1,1)  * 4 +
				 substr($S,2,1)  * 3 +
				 substr($S,3,1)  * 2 +
				 substr($S,4,1)  * 7 +
				 substr($S,5,1)  * 6 +
				 substr($S,6,1)  * 5 +
				 substr($S,7,1)  * 4 +
				 substr($S,8,1)  * 3 +
				 substr($S,9,1) * 2) % 11;
			 $v3 = 11 - $v2;
			 switch ($v3) {
			 case 11 : $v3 = 0; break;
			 Case 10 : $v3 = 9; break;
			 }
			 
		return substr($S,10,1) == $v3;

	}
	
	/** DEPRECIADA, UTILIZAR FUNCION DATE EN SU LUGAR */
	static public function isDate ($string)
	{
		$date = new Date();
		$year = $month = $day = 0;
		$string_set = '';
		
		$string = str_replace('/' , '-', $string); // por si viene en formato con barritas
		
		$string = explode (' ',$string); // por si es ISO con HHMMSS
		
		$array_date = explode ('-',$string[0]);
	
		
		if (count($array_date) != 3)
		{
			return false;
		}
		
		
	
		// ISO (solo fecha)
		if (strlen($array_date[0]) == 4)
		{
			$year = $array_date[0];
			$month = $array_date[1];
			$day = $array_date[2];
			
		}
		else
		{
				
			$year = $array_date[2];
			
			//DDMMAAAA
			if ($array_date[0] > 12)
			{
				$month = $array_date[1];
				$day = $array_date[0];
			}
			if ($array_date[1] > 12)
			{
				$month = $array_date[0];
				$day = $array_date[1];
			}
		
		}
			
		if ($month > 0 && $day > 0)
		{
			$string_set = $year . '-'. $month. '-'. $day	. ' 00:00:00';
		}
		else
		{
			//MMDDAAAA
			if (checkdate ($array_date[0],$array_date[1],$array_date[2]))
			{
				$string_set = $array_date[2] . '-'. $array_date[0] . '-'. $array_date[1]	. ' 00:00:00';
			}
			
			//DDMMAAAA (tiene mas importancia)
			if (checkdate ($array_date[1],$array_date[0],$array_date[2]))
			{
				$string_set = $array_date[2] . '-'. $array_date[1] . '-'. $array_date[0]	. ' 00:00:00';
			}
		}
		
	
		if ($string_set == '')
		{
			return false;
		}
		
		$date->setDate($string_set);
		return self::isValidDate($date);
	}
	
	/** Verifica si un directorio no se sube de nivel
	 * Parent indica el directorio padre para validar si 
	 * está debajo de ese nivel */
	static public function isValidDir ($dir, $parent = false)
	{
	
			 if(eregi("\.\.",$dir) ) 
		       {
		           
		            return false;
		       } 
		       else 
		       { 
		            	if ($parent === false)
						{
							return true;
						} 
						else
						{   if(!eregi ($parent,$dir )) 
								{return true;}
							else
								{ return false;}
						}
		       }
	}
		
	/**
	 * Adds a "0" in front of the value if it's below 10.
	 */
	static public function addZero( $value )
	{
    	settype( $value, "integer" );
    	$ret = $value;
    	if ( $ret < 10 )
    	{
        	$ret = "0". $ret;
    	}
    	return $ret;
	}//end function
	
	/**
	 * A partir de una cadena de caracteres y segun su longitud
	 * devuelve un objeto Date_Span.
	*/
	static public function strToSpan( $value )
	{
		$value = trim($value);
		$value = str_replace(':','',$value);
		
		switch(strlen($value))
		{
			case 1:
			{
				//caso H
		 		$value = new Date_Span($value, "%h");
				break;
			}
			case 2:
			{
				//caso HH
				$value = new Date_Span($value, "%H");
				break;
			}
			//@todo:no anda bien ver casos primer caracter <=9
			case 3:
			{
				//caso HMM
				$value = "0" . $value;
				$value = new Date_Span($value, "%H%M");
				break;
			}		
			case 4:
			{
				//caso HHMM
				$value = new Date_Span($value, "%H%M");
				break;
			}		
			default:
			{
				//caso inesperado
				//@todo: le asigno una de comienzo prederminada o devuelvo error
			 	$value = new Date_Span("0900", "%H%M");
				break;	
			}	
		}//end switch
		
		return $value;
	}//end function

	/**
	 * Solo horas y minutos 
	 */	
	 function isTime($str) 
  	{
    	return preg_match('#^((1\d)|(2[0-3])|(0?\d))[:]{0,1}([0-5]\d)$#', $str);    
  	}//end function
  	
  		/**
	 * Esta funcion encripta o desencripta el texto pasado como parametro
	 * segun el valor de $mode
	 * $mode = 0 => encriptar
	 * $mode = 1 => desencriptar
	 * El tercer parametro es la Clave que desea ponerle al texto encriptado, 
	 * debe de ser la misma para cuando se desee desencriptar el texto ya encriptado
	 */
	 static public function encrypt( $text, $mode, $seed = ENCRYPT_SEED )
	 {
		$maxca = 100;
		$c = 1;
		if ( $mode == 1 )
		{
		    $lnlen = strlen(trim($text));
		    $lcnewstring = "";
		    for ($j = 0; $j < $lnlen; $j++)
		    {
				$lcchar = ( ord (substr($text, $j, 1)) + ord (substr($seed, $c, 1))) % 256;
		        $lcnewstring = $lcnewstring . chr($lcchar);
		        $c++;
		        if ($c >= strlen($seed) )
				{	$c = 1;	}
		        
		    }//end for
		    $retval = $lcnewstring;
		}//end if
		
		if ( $mode == 0 )
		{
			$lnlen = strlen(trim($text));
			$lcnewstring = "";
		    for ($j = 0; $j < $lnlen; $j++)
		    {
		    	$lcchar = ((256 + ord(substr($text, $j, 1))) - ord(substr($seed, $c, 1)) % 256);
		    	$c++;
		    	if ( $c >= strlen($seed))
		    	{	$c = 1;	}
		    	$lcnewstring = ($lcnewstring . chr($lcchar));
		    }//end for
		   	$retval = $lcnewstring;	
		}//end if
		
		return $retval;
		
	 }//end function
			
	/**
	 *  Ordena array
	 * 
	 */
	function quickSort (&$array, $low, $high, $numeric = false, $key='', $reverse=false)
	{
		if ($low < $high)
		{
			$tmpLow = $low;
			$tmpHigh = $high + 1;
			$current = $array[$low];
			
			$done = false;
			while (!$done)
			{
				
				while (++$tmpLow <= $high && 
					OOB_validatetext :: isLess ($array[$tmpLow], $current, $numeric, $key) );
					
				while (OOB_validatetext :: isGreater ($array[--$tmpHigh], $current,$numeric, $key) );
				
				if ($tmpLow < $tmpHigh)
				{
					OOB_validatetext :: swap ($array, $tmpLow, $tmpHigh);
				}
				else
				{
					$done = true;
				}//end if
			}//end while
			
			OOB_validatetext :: swap ($array, $low, $tmpHigh);
			OOB_validatetext :: quickSort ($array, $low, $tmpHigh-1, $numeric, $key);
			OOB_validatetext :: quickSort ($array, $tmpHigh+1, $high, $numeric, $key);
			
		}//end if
		
		if ($reverse == ORDER_ASC) 
		{	$array = array_reverse($array);	} 		
		
	}//end function
	
	/**
	 *	Intercambia 2 elementos de un array 
	 */
	static public function swap (&$array, $i, $j)
	{
		$tmp = $array[$i];
		$array[$i]=$array[$j];
		$array[$j]=$tmp;
	} 
	
	/**
	 *  Devuelve true si $a es menor q $b  
	 */
	static public function isLess($a, $b, $numeric, $key)
	{
		global $ari;
		$return = false;
		
		if ( $key != "" && isset($a[$key]) && isset($b[$key]) )
		{
			$a = $a[$key];
			$b = $b[$key];
		}//end if
		
		if ( $numeric )
		{
			if ( $a < $b )
			{	$return = true;	}//end if				
		}
		else
		{
			if ( strcasecmp($a, $b) < 0 )
			{	$return = true;	}//end if				
		}//end if
		
		return $return;
	}//end function
	
	/**
	 * Devuelve true si $a es mayor q $b 
	 */
	static public function isGreater ($a, $b, $numeric, $key)
	{
		global $ari;
		$return = false;
		
		if ( $key != "" && isset($a[$key]) && isset($b[$key]) )
		{
			$a = $a[$key];
			$b = $b[$key];
		}//end if
		
		if ( $numeric )
		{
			if ( $a > $b )
			{	$return = true;	}//end if				
		}
		else
		{
			if ( strcasecmp($a, $b) > 0 )
			{	$return = true;	}//end if				
		}//end if
	
		return $return;
		
	}//end function	
	
	static public function inputHTML ($input, $char = "UTF-8", $url = false)
	{
		if ($url)
		{	return trim(urldecode(htmlentities($input,0,$char)));
		}
		else
		{	//var_dump(htmlentities($input,0,$char));exit;
			return trim(htmlentities($input,0,$char));
		}
		
	}//end function


	/** Valida que el string sea un URL válido */
	static public function isURL ($string)
	{
		$return = false;
		$url_regexp ='<(?:(?:https?)://(?:(?:(?:(?:(?:(?:[a-zA-Z0-9][-a-zA-Z0-9]*)?[a-zA-Z0-9])[.])*(?:[a-zA-Z][-a-zA-Z0-9]*[a-zA-Z0-9]|[a-zA-Z])[.]?)|(?:[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+)))(?::(?:(?:[0-9]*)))?(?:/(?:(?:(?:(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*)(?:/(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*))*))(?:[?](?:(?:(?:[;/?:@&=+$,a-zA-Z0-9\\-_.!~*\'()]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)))?))?)>'; 
		if (preg_match ($url_regexp, $string) > 0)
		{
			$return = true;
		}
		
		return $return;
	}


	/** Limpiar tags HTML de $cadena
	 *  $htmlentities define si la cadena de salida usara previamente un htmlentities();	
	 */ 
	function stripTags ($cadena, $htmlentities = false) 
	{
		//javascript: at = dat.replace(/<[^>]+>/g,""); 
		
		$expresion = "<[^>]+>?([^>|^<]*)<?\/[^>]*>";
		$cadena = strip_tags($cadena);
		
		while (ereg($expresion,$cadena) == true) 
		{	$cadena = ereg_replace($expresion,'\\1',$cadena);
		}
		
		if ($htmlentities) 
		{	return htmlentities($cadena);
		} 
		else 
		{	return $cadena;
		}
	}

/** Valida que el (array) $array pasado sea un array de objetos de (string) $class pasada */
	static public function isArrayObject($array, $class) 
	{
		if(!is_array($array))
		{	return false;
		}
		foreach($array as $a)
		{	if(!is_a($a, $class))
			{	return false;
			}
		}
		return true;
	}

	/** Retorna true si el objeto $date (clase Date) pasado es válido,
	 *  caso contrario retorna false.
	 */
    public function isValidDate($date)
    {	
 		if (!Date_Calc::isValidDate($date->getDay(), $date->getMonth(), $date->getYear())) 
        {	return false;
        }
        return true;
    }//end function

	
	/** Limpia el $string pasado dejandolo listo para exportarlo a un formato '.csv'
		- Convierte todas las entidades HTML a sus caracteres correspondientes   
		- Elimina todos los tags de HTML
		- Sustituye todas las apariciones del valor del caracter separador de valores 
		  por otro valor, ambos predefinidos (CHAR_CSVEXPORT por CHAR_CSVEXPORT_CHANGE)
	*/
	static public function cleanToExport ($string, $char = "UTF-8")
	{
		//Convertir entidades HTML a sus caracteres correspondientes
		$string = trim(html_entity_decode($string, 0, $char));
		
		//Eliminar todos los tags de HTML
		$string = self::stripTags($string);
		
		//quitar ocurrencias de fin de linea '\n' y retorno de carro '\r'
		$string = str_replace(array("\r\n","\n","\r","\n\r"), " ", $string);

		//sustituir apariciones de CHAR_CSVEXPORT por CHAR_CSVEXPORT_CHANGE
		$string = str_replace (CHAR_CSVEXPORT, CHAR_CSVEXPORT_CHANGE, $string);
		
		return $string;
		
	}//end function

	/** Limpia el $string pasado quitandole las comillas dobles y comillas
	 *  simples para que sea aplicable para invocar metodos de javascript
	*/
	static public function cleanToScript($string)
	{
		//1. convertir todos los (") por (')
		//2. convertir todos los (') por (\')
		$string = str_replace('"', "'", $string);
		$string = str_replace("'", "\'", $string);
		return $string;
		
	}//end function

	/** only to be used by oob_model_type, you can use is_array, dont bother here */
	public function isArray($array)
    {	
 		if ($array == 'Array') return true;
		return false;
    }//end function
	
}//end class

?>