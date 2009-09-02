<?php
# Eumafes v2 [?2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 04-ago-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
OOB_module :: includeClass('currency','currency_currency');

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('list','currency','currency')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

global $ari;

$allowDelete = seguridad :: isAllowed(seguridad_action :: nameConstructor('delete','currency','currency'));
$ari->t->assign("allowDelete",$allowDelete);
$allowUpdate = seguridad :: isAllowed(seguridad_action :: nameConstructor('update','currency','currency'));
$ari->t->assign("allowUpdate",$allowUpdate);

$handle = $ari->url->getVars();
$ari->t->caching = 0; // dynamic content
$sp = new oob_safepost ("list");
 
//cargo las monedas
$language = $ari->get('agent')->getLang();
$ari->t->assign("error",false);
$ari->t->assign('lang',$language);

if (isset ($_POST['ok'])) 
{
	//verificar datos enviados duplicados
	$error=false;
	if(!$sp->Validar())
	{	
		$error=true;
		$ari->t->assign('error', true);
		$ari->t->assign('SENT_DUPLICATE_DATA', true);
	}
	
	//borro las monedas seleccionadas
	if(isset($_POST['delete_currency']))
	{	
		if(!$error)
		{	foreach($_POST['delete_currency'] as $id_currency)
			{
				$currency = new currency_currency($id_currency);
				$currency->delete();	
			}
		}
	}

	// check the group update status selector, and update if selected
	if (isset ($_POST['default']) )
	{
		if(!$error)
		{	$currency = new currency_currency($_POST['default']);
			$currency->set('default', YES);
			
			if ($currency->store())
			{	
				header( "Location: " . $ari->get("adminaddress") . '/currency/currency/list');
				exit;
			}
			else
			{	//store falla	
				$ari->t->assign("error", true );	
				$errores = $ari->error->getErrorsfor("currency_currency");
				foreach ($errores as $error)
				{	$ari->t->assign($error, true );		
				}	
			}
		}
	}
}


$array_currencies = array();
if ($currencies = currency_currency :: listCurrenciesForLanguage (USED, 'name', $operator = OPERATOR_EQUAL, $language) )
{
	$i = 0;
	foreach($currencies as $c)
	{
		$array_currencies[$i]['name'] = $c->get('name') . " (" . $c->get('sign') . ")";
		$array_currencies[$i]['id'] = $c->get('id');
		
		$array_currencies[$i]['allowDelete'] = $c->allowDelete();
		
		if ( $c->get('default') == YES)
		{
			$array_currencies[$i]['checked'] = 'checked';
		}
		else
		{	$array_currencies[$i]['checked'] = '';	}
		$i++;
	}	
}
 
$ari->t->assign("currencies", $array_currencies );
if (count($array_currencies) == 0)
{	$ari->t->assign("allowDelete", false);	}

//$ari->t->assign("count_currencies", count($array_currencies) );
//$ari->t->assign("count_deletes", currency_currency :: countCurrencies(DELETED,OPERATOR_EQUAL, $language));

$ari->t->assign("formElement", $sp->FormElement());
// display
$ari->t->display($ari->module->admintpldir(). "/currency_list.tpl");

 
?>
