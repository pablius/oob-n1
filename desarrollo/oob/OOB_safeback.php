<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/**
This class generates safe-back pages (so a user can click the "back" button)  without getting error.
*/
 
 class OOB_safeback
 {
 	
 private function __construct ()
 {}
 
 
 /** This will generate redirection urls, so you can use the back button of the browser
  *  (baseurl, postvars, validvalues, execute)
  * string $baseurl :  is the module adress (example = /seguridad/list)
  * array  $postvars : is an array with the post variables
  * array  $valid_values : is an array of arrays where each one is used to validate the postvar of the same index, if its an array no validation is made
  * bool   $execute : TRUE executes the redirect entry. FALSE returns the generated URL
  */
 static public function generate ($baseurl = "", $postvars = array(), $valid_values = array(), $execute = true)
 {
  	 global $ari;

	 // check for the starting slash 
	 if (substr ($baseurl, 0, 1) !== "/")
	 {
	 	$baseurl = "/" . $baseurl;
	 }
	 
	  // check for the ending slash
	 if (substr($baseurl, -1) != "/")
	 {$baseurl = $baseurl. "/";}
	 
	 
	 	// validamos los elementos
	 if ((count ($postvars) != count ($valid_values)) || !is_array ($postvars) || !is_array ($valid_values))
	 {throw new OOB_exception("Cuando se generaba una SafeURL, se encontro un elemento inválido", "501", "Error del programa", true);}
	 $i = 0;
	 $work = "";
	 
	 
	 foreach ($postvars as $var)
	 {
	 	//generamos la nueva url, si es un array hay que serialize & base64_encode para pasarlo!
	 	if (in_array ($var, $valid_values[$i]) || is_array ($var))
	 	{
	 		if (is_array ($var))
	 		{
	 			$var = base64_encode(serialize($var));
	 		}
	 		
	 		$work .= $var . "/"; 
	 	
	 	} else {
	 		throw new OOB_exception("El valor {$var}, no corresponde a ninguno de los valores de validación", "403", "Error en el Formulario", true);
	 	}
	 
	 	++$i;
	 }
	 
	 
	 //entrega
	 if ($execute)
	 {
	 	if ($ari->mode == 'admin')
	 		header( "Location: " . $ari->adminaddress . $baseurl . $work ); 
	 	if ($ari->mode == 'user')
	 		header( "Location: " . $ari->adminaddress . $baseurl . $work ); 
	 	exit ();
	 }
	 else
	 {return $baseurl . $work;}
	 
	 } 
 
 }
 
// //teST
// $pb[]="hola";
//  $pb[]="sisi";
// $valid_uno[]="chau";
// $valid_uno[]="hola";
//  $valid_uno[]="sisi";
// 
//$valid[] = $valid_uno;
//$valid[] = $valid_uno;
//$valid[] = array();
//
//$pb[] =  $valid_uno;
//
////si no fuera un array el post var, el array de comprobacion seria uno solo! adaptar!
//
// print oob_safeback::generate ("/seguridad/lista", $pb, $valid, false)
?>
