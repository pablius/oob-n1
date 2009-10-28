<?php
# Eumafes v2 [�2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 04-ago-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
OOB_module :: includeClass('currency','currency_currency');

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('view','currency','currency'))  && !seguridad::RequireLogin())
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!");
} 

global $ari;

$handle = $ari->url->getVars();
$ari->t->caching = 0; // dynamic content
$sp = new oob_safepost("view");
$ari->t->assign("mostrar", false);

//------ Refrescar valores ------
//fecha desde  
if(!isset($_POST['desdeCheck']))
{
	$ari->t->assign("desdeChecked", '');
	//fecha desde
	if(isset($_POST['desdeYear']) && 
	   isset($_POST['desdeMonth']) && 
	   isset($_POST['desdeDay']))
	{//begin if
		$ari->t->assign("desde", $_POST['desdeYear'] ."-". $_POST['desdeMonth'] ."-". $_POST['desdeDay'] );
		$ari->t->assign("desdeDisabled", "");
	}		
}
else
{	$ari->t->assign("desdeChecked", 'checked');
	$ari->t->assign("desdeDisabled", "disabled");
}

//fecha hasta
if(!isset($_POST['hastaCheck']))
{
	$ari->t->assign("hastaChecked", '');
	//fecha hasta
	if(isset($_POST['hastaYear']) && 
	   isset($_POST['hastaMonth']) && 
	   isset($_POST['hastaDay']))
	{//begin if
		$ari->t->assign("hasta", $_POST['hastaYear'] ."-". $_POST['hastaMonth'] ."-". $_POST['hastaDay'] );
		$ari->t->assign("hastaDisabled", "");
	}		
}
else
{	$ari->t->assign("hastaChecked", 'checked');
	$ari->t->assign("hastaDisabled", "disabled");
}

$selMoneda = '';
if (isset($_POST['moneda']))
{	$selMoneda = $_POST['moneda'];
}	
$ari->t->assign("selMoneda", $selMoneda);

//---------------
 
//cargar las monedas
$language = $ari->get('agent')->getLang();
$ari->t->assign("error",false);
$ari->t->assign('lang',$language);
$array_currencies = array();

if ($currencies = currency_currency :: listCurrenciesForLanguage (USED, 'name', $operator = OPERATOR_EQUAL, $language) )
{	foreach($currencies as $c)
	{	$array_currencies[$c->get('id')] = $c->get('name') . " (" . $c->get('sign') . ")";
	}
}
$ari->t->assign("optMoneda", $array_currencies );

if (isset ($_POST['mostrar']) && 
	isset ($_POST['moneda']))
{
	//currency object
	$moneda = new currency_currency($_POST['moneda']);

	//fecha desde
	$desde = false;
	if(isset($_POST['desdeYear']) && 
	   isset($_POST['desdeMonth']) && 
	   isset($_POST['desdeDay']))
	{//begin if
		$fecha  = $_POST['desdeYear'] ."-";
		$fecha .= OOB_validatetext :: addZero($_POST['desdeMonth']) ."-";
		$fecha .= OOB_validatetext :: addZero($_POST['desdeDay']);
		$fecha .= ' 00:00:00';
		$desde  = new Date($fecha);
	}		

	//fecha hasta
	$hasta = false;
	if(isset($_POST['hastaYear']) && 
	   isset($_POST['hastaMonth']) && 
	   isset($_POST['hastaDay']))
	{//begin if
		$fecha  = $_POST['hastaYear'] ."-";
		$fecha .= OOB_validatetext :: addZero($_POST['hastaMonth']) ."-";
		$fecha .= OOB_validatetext :: addZero($_POST['hastaDay']);
		$fecha .= ' 23:59:59';
		$hasta  = new Date($fecha);
	}		
		
	//VALIDACIONES
	$errores = array();
	
	//verificar datos enviados duplicados
	if(!$sp->Validar())
	{	$errores[] = 'SENT_DUPLICATE_DATA';
	}

	//validar fechas
	if($desde)
	{	if(!contenido_estructura :: isValidDate($desde))
		{	$errores[] = "INVALID_DESDE";
		}
	}
	if($hasta)
	{	if(!contenido_estructura :: isValidDate($hasta))
		{	$errores[] = "INVALID_HASTA";
		}
	}
	if($desde && $hasta)
	{	
		//comparar las fechas desde y hasta
		//return int 0 if the dates are equal, -1 if d1 is before d2, 1 if d1 is after d2
		$res = Date :: compare($desde, $hasta);
		if($res == 1)
		{	$errores[] = "INVALID_INTERVAL";
		}
	}
	
	if(count($errores) == 0)
	{
		$array_currencies = array();
		if ($values = $moneda->getChanges($desde, $hasta))
		{
			//var_dump($values);
			$changes = array();
			$i=0;
			foreach($values as $v)
			{	
				$changes[$i]['value'] = OOB_numeric :: formatPrint($v['value']);
				$changes[$i]['date'] = $v['date'];
				$i++;
			}
			$ari->t->assign("changes", $changes);
		}
		$ari->t->assign("mostrar", true);
		//var_dump($changes);exit;
	}
	else
	{
		$ari->t->assign('error', true);
		foreach($errores as $e)
		{	
			$ari->t->assign($e, true);
		}
	
	}
}

$ari->t->assign("formElement", $sp->FormElement());
// display
$ari->t->display($ari->module->usertpldir(). "/currency_view.tpl");

?>