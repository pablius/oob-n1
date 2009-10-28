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

//estados
if ($states = address_state::listStatesByCountry() ) 
{
	// show time
	$i = 0;
	foreach ($states as $s)
	{
		$array_states[$i]['id']= $s->get('id');
		$array_states[$i]['name']= $s->get('name');
		$array_states[$i]['countryName']= $s->get('country')->get("name");	
		
		//verifico origenes seleccionados
		$array_states[$i]['checked']= "";
		if (isset($_POST["sources"]))
		{	if (in_array($s->get('id'), $_POST["sources"]))
			{	$array_states[$i]['source_checked']= "checked";
			}
		}
		
		//verifico destino seleccionados
		if (isset($_POST["destiny"]) && $s->get('id') == $_POST["destiny"])
		{	$array_states[$i]['destiny_checked']= "checked";
		}
			
		$i++;
	}//end foreach
	
	$ari->t->assign("states", $array_states );
	
}//end if


if (!isset($_POST['guardar']))
{
	$new_state = false;
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
			$sources[] = new address_state($s);			
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
				
	if (!isset($_POST['destiny']))	
	{	$errores[] = "NO_DESTINY_OPTION";	
	}
	else
	{	
		//verifico si el destino es uno nuevo o existente
		if($_POST['destiny'] == ID_UNDEFINED)
		{	
			//destino nuevo
			
			$destiny = new address_state(ID_UNDEFINED);
			$destiny->set("country", $country);
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
			
			$new_state = true;
			
		}
		else
		{	
			//destino existente
			//$destiny = new address_state($_POST['destiny']);
			
			if (isset($_POST['address_state'][0]) && 
				$_POST['address_state'][0] <> "" && 
				$_POST['address_state'][0] <> ID_UNDEFINED )
			{
				$destiny = new address_state ($_POST['address_state'][0]);
				//$ari->t->assign("address_city_id", ID_UNDEFINED);
				$ari->t->assign("address_state_id", $destiny->get('id'));
				$ari->t->assign("address_state_name", $destiny->get('name'));
				//$ari->t->assign("address_country_selected", $destiny->get('country')->get('id'));
			
			}
			else
			{	$errores[] = "NO_DESTINY_EXISTS";
			}
			
			$new_state = false;
			
		}
		
	}
	

	if (count($errores) == 0)
	{
		$ari->db->StartTrans();
		if ($new_state)
		{	$destiny->store();
		}
		address_state::union($sources, $destiny);
		$ari->db->CompleteTrans();
		
		/*
		var_dump($sources);
		echo "<br><br>";
		var_dump($destiny);
		*/
		
		header( "Location: " . $ari->get("adminaddress") . '/address/state/union');	
		exit;
		
	}
	else
	{
		$ari->t->assign('error',true);
		foreach($errores as $e)
		{	$ari->t->assign($e, true);	
		}
		
		//error de destino nuevo ingresado
		if($erroresDestiny = $ari->error->getErrorsfor("address_state"))
		{	foreach ($erroresDestiny as $error)
			{	$ari->t->assign($error, true );	
			}
		}
	}
}

$ari->t->assign("new_state", $new_state);
//$ari->t->assign("address_country_selected", $address_country_selected);	
$ari->t->display($ari->module->admintpldir(). "/state_union.tpl");
 
?>