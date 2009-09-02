<?php
# Nutus [2006 - Nutus, Todos los derechos reservados]
/*
 * Created on 13/08/2005
 * @author Flavio Robles (flavio.robles@nutus.com.ar)
 */
 
if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('union','state','address')) )
{	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

global $ari;
$ari->t->caching = false;
$ari->t->assign("error",false);
$handle = $ari->url->getVars();

//selector de pais
if ($countries = address_country :: listCountries(USED,'name',OPERATOR_EQUAL) )
{
	foreach($countries as $c)
	{	$array_countries[$c->get('id')] = $c->get('name');		
	}	
}
$ari->t->assign("countries",$array_countries );


//ciudades
if ($cities = address_city::listCitiesByStates() ) 
{
	// show time
	$i = 0;
	foreach ($cities as $s)
	{
		$array_cities[$i]['id']= $s->get('id');
		$array_cities[$i]['name']= $s->get('name');
		$array_cities[$i]['stateName']= $s->get('state')->get("name");	
		$array_cities[$i]['countryName']= $s->get('state')->get("country")->get("name");	
		
		//verifico origenes seleccionados
		$array_cities[$i]['checked']= "";
		if (isset($_POST["sources"]))
		{	if (in_array($s->get('id'), $_POST["sources"]))
			{	$array_cities[$i]['source_checked']= "checked";
			}
		}
		
			
		$i++;
		
	}//end foreach
	
	$ari->t->assign("cities", $array_cities );
	
}//end if


if (!isset($_POST['guardar']))
{
	$new_city = false;
}
else
{	
	//guardar union!!
	//var_dump($_POST);exit;
	
	$errores = array();
	
	$sources = array();
	if (!isset($_POST['sources']))	
	{	$errores[] = "INVALID_SOURCES";	
	}
	else
	{
		foreach($_POST['sources'] as $s)
		{
			$sources[] = new address_city($s);			
		}//end foreach	
	}
	
	$country = false;
	if (isset($_POST['address_country'][0]) && 
		$_POST['address_country'][0] <> "" && 
		$_POST['address_country'][0] <> ID_UNDEFINED )
	{
		$country = new address_country ($_POST['address_country'][0]);
		//var_dump($country);
		$ari->t->assign("address_country_selected", $country->get("id"));
	}
	else
	{	$errores[] = "NO_COUNTRY";
	}
	
	$state = false;
	if( isset($_POST['address_state'][0]) && 
		$_POST['address_state'][0] <> "" && 
		$_POST['address_state'][0] <> ID_UNDEFINED )
	{
		$state = new address_state ($_POST['address_state'][0]);
		$ari->t->assign("address_state_id", $state->get('id'));
		$ari->t->assign("address_state_name", $state->get('name'));
	}
	else
	{	$errores[] = "NO_STATE";
	}
	
				
	if (!isset($_POST['destiny']))	
	{	$errores[] = "NO_DESTINY_OPTION";	
	}
	else
	{	
		//verifico si el destino es uno nuevo o existente
		if($_POST['destiny'] == ID_UNDEFINED)
		{	
			//destino nuevo
			
			$destiny = new address_city(ID_UNDEFINED);
			$destiny->set("state", $state);
			$destiny->set("status", USED);
			
			if (isset($_POST['new_name']))	
			{	
				$new_name = OOB_validatetext :: inputHTML($_POST['new_name']);
				$ari->t->assign("new_name", $new_name);
				$destiny->set("name", $new_name);
				//$errores[] = "INVALID_DESTINY_NEW";	
			}
			
			//var_dump($destiny);exit;
			
			if(!$destiny->isValid())
			{	$errores[] = "INVALID_OBJECT";	
			}
			
			$new_city = true;
			
		}
		else
		{	
			//destino existente
			if (isset($_POST['address_city'][0]) && 
				$_POST['address_city'][0] <> "" && 
				$_POST['address_city'][0] <> ID_UNDEFINED )
			{	
				$destiny = new address_city ($_POST['address_city'][0]);
				$ari->t->assign("address_city_id", $destiny->get('id'));
				$ari->t->assign("address_city_name", $destiny->get('name'));
				
				//$ari->t->assign("address_state_id", $destiny->get('state')->get('id'));
				//$ari->t->assign("address_state_name", $destiny->get('state')->get('name'));
				//$ari->t->assign("address_country_selected", $destiny->get('state')->get('country')->get('id'));
			}
			else
			{	$errores[] = "NO_DESTINY_EXISTS";
			}
			
			$new_city = false;
			
		}
		
	}
	

	if (count($errores) == 0)
	{
		$ari->db->StartTrans();
		if ($new_city)
		{	$destiny->store();
		}
		address_city::union($sources, $destiny);
		$ari->db->CompleteTrans();
		
		/*
		var_dump($sources);
		echo "<br><br>";
		var_dump($destiny);
		*/
		
		header( "Location: " . $ari->get("adminaddress") . '/address/city/union');	
		exit;
		
	}
	else
	{
		$ari->t->assign('error',true);
		foreach($errores as $e)
		{	$ari->t->assign($e, true);	
		}
		
		//error de destino nuevo ingresado
		if($erroresDestiny = $ari->error->getErrorsfor("address_city"))
		{	foreach ($erroresDestiny as $error)
			{	$ari->t->assign($error, true );	
			}
		}
	}
}

$ari->t->assign("new_city", $new_city);
$ari->t->display($ari->module->admintpldir(). "/city_union.tpl");
 
?>